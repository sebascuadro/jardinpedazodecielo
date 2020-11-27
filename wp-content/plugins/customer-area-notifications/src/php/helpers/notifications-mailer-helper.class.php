<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_NotificationsMailerHelper
{
    /** @var CUAR_Plugin */
    private $plugin;

    /** @var CUAR_NotificationsAddOn */
    private $no_addon;

    /**
     * Constructor
     *
     * @param CUAR_Plugin             $plugin
     * @param CUAR_NotificationsAddOn $no_addon
     */
    public function __construct($plugin, $no_addon)
    {
        $this->plugin = $plugin;
        $this->no_addon = $no_addon;
    }

    /**
     * Send a notification to a list of users
     *
     * @param array  $recipient_ids
     * @param string $notification_id
     * @param int    $post_id
     * @param array  $extra
     */
    public function send_mass_notification($recipient_ids, $notification_id, $post_id = null, $extra = array())
    {
        // Do nothing if notification is disabled
        $post_type = $post_id === null ? null : get_post_type($post_id);
        $notif_settings = $this->no_addon->settings()->get_notification_params($notification_id, $post_type);

        if ($notif_settings['mode'] === 'disabled') return;

        // Make sure we have no duplicates in the recipient list
        $recipient_ids = array_unique($recipient_ids);

        // Send the notification to every recipient individually
        foreach ($recipient_ids as $recipient_id) {
            $this->send_notification($recipient_id, $notification_id, $post_id, $notif_settings, $extra);
        }

        // Done
        do_action('cuar/notifications/on-mass-notification-sent', $recipient_ids, $notification_id, $post_id, $extra);
    }

    /**
     * Send a notification to a single user
     *
     * @param int    $recipient_id
     * @param string $notification_id
     * @param int    $post_id
     * @param array  $notif_settings
     * @param array  $extra
     */
    public function send_notification($recipient_id, $notification_id, $post_id = null, $notif_settings = null, $extra = array())
    {
        if ($notif_settings === null) {
            $post_type = $post_id === null ? null : get_post_type($post_id);
            $notif_settings = $this->no_addon->settings()->get_notification_params($notification_id, $post_type);

            if ($notif_settings === null) {
                return;
            }
        }

        // Do nothing if notification is disabled
        if ($notif_settings['mode'] === 'disabled') {
            return;
        }


        // Unless this is a test, assume that the currently logged user does not need to get notified of his own actions
        if ($recipient_id === get_current_user_id()
            && $this->is_notification_to_self_allowed($recipient_id, $notification_id, $post_id, $notif_settings, $extra) === false) {
            return;
        }

        // Allow replacing some placeholders within the notification heading and content
        $notification_content = apply_filters('cuar/notifications/content', $notif_settings['body'], $recipient_id, $notification_id, $post_id, $extra);
        $notification_heading = apply_filters('cuar/notifications/heading', $notif_settings['heading'], $recipient_id, $notification_id, $post_id, $extra);

        // When no content, we assume that the notification should not be sent (disabled via a hook for that recipient for instance)
        if ($notification_content === false) return;

        // Build the email subject
        $email_subject = apply_filters('cuar/notifications/subject', $notif_settings['subject'], $recipient_id, $notification_id, $post_id, $extra);

        // Get the recipient address and name
        $recipient = get_userdata($recipient_id);
        $to_name = $recipient->display_name;
        $to_address = $recipient->user_email;

        // Get some mail settings
        $from_name = $this->no_addon->settings()->get_from_name();
        $from_address = $this->no_addon->settings()->get_from_address();
        $email_format = $this->no_addon->settings()->get_email_format();

        // Auto-p if required
        if ($this->no_addon->settings()->is_autop_enabled() && 'html' === $email_format) {
            $notification_content = wpautop($notification_content, true);
        }

        // Build the email content using the layout specified in the settings
        $email_body = $this->build_email_content($notification_heading, $notification_content);

        // Send the email
        $this->send_email($from_name, $from_address, $to_name, $to_address, $email_subject, $email_body, $email_format);

        // Done
        do_action('cuar/notifications/on-notification-sent', $recipient_id, $notification_id, $post_id, $extra);
    }

    /**
     * Send an email using the settings for format, from name and from address
     *
     * @param string $from_name
     * @param string $from_address
     * @param string $to_name
     * @param string $to_address
     * @param string $subject
     * @param string $body
     * @param string $format
     */
    public function send_email($from_name, $from_address, $to_name, $to_address, $subject, $body, $format)
    {
        //  Build headers
        $headers = array();
        $headers[] = "From: \"$from_name\" <$from_address>\n";
        $headers[] = "Return-Path: <" . $from_address . ">\n";
        $headers[] = "Reply-To: \"" . $from_name . "\" <" . $from_address . ">\n";
        $headers[] = "X-Mailer: PHP" . phpversion() . "\n";
        $headers[] = "MIME-Version: 1.0\n";

        $subject = stripslashes($subject);
        $body = stripslashes($body);

        if ('html' === $format) {
            $headers[] = "Content-Type: " . get_bloginfo('html_type') . "; charset=\"" . get_bloginfo('charset') . "\"\n";
        } else {
            $headers[] = "Content-Type: text/plain; charset=\"" . get_bloginfo('charset') . "\"\n";
            $body = preg_replace('|&[^a][^m][^p].{0,3};|', '', $body);
            $body = preg_replace('|&amp;|', '&', $body);
            $body = wordwrap(strip_tags($body), 80, "\n");
        }
        $to = sprintf("%s <%s>", $to_name, $to_address) . "\r\n";

        $headers = apply_filters('cuar/notifications/headers', $headers);

        @wp_mail($to, $subject, $body, $headers);

        do_action('cuar/notifications/on-email-sent', $to_name, $to_address, $subject, $body, $headers);
    }

    /**
     * Build the email content using the layout specified in the settings
     *
     * @param $content
     *
     * @return string
     */
    public function build_email_content($heading, $content)
    {
        $template_id = $this->no_addon->settings()->get_email_layout();
        $template_root = apply_filters('cuar/notifications/template-root', CUARNO_INCLUDES_DIR, $template_id);

        // Prepare some variables for the template
        /** @noinspection PhpUnusedLocalVariableInspection */
        $email_content = $content;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $main_heading = $heading;

        ob_start();
        include($this->plugin->get_template_file_path(
            $template_root,
            array(
                'notification-mail-layout-' . $template_id . '.template.php',
                'notification-mail-layout.template.php',
            ),
            'templates'
        ));
        $body = ob_get_contents();
        ob_end_clean();

        return $body;
    }

    private function is_notification_to_self_allowed($recipient_id, $notification_id, $post_id, $notif_settings, $extra)
    {
        return apply_filters('cuar/notifications/allow-notification-to-self', true,
            $recipient_id, $notification_id, $post_id, $notif_settings, $extra);
    }
}