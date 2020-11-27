<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_NotificationsLogger
{
    public static $TYPE_NOTIFICATION_SENT = 'cuar-notification-sent';
    public static $TYPE_EMAIL_SENT = 'cuar-email-sent';

    public static $META_NOTIFICATION_ID = 'cuar_no_notification_id';
    public static $META_RECIPIENT_ID = 'cuar_no_recipient_id';
    public static $META_EXTRA = 'cuar_no_extra';

    public static $META_HEADERS = 'cuar_no_headers';
    public static $META_SUBJECT = 'cuar_no_subject';
    public static $META_BODY = 'cuar_no_body';

    /** @var CUAR_Plugin */
    private $plugin;

    /** @var CUAR_NotificationsAddOn */
    private $no_addon;

    /** @var CUAR_Logger */
    private $logger;

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
        $this->logger = $plugin->get_logger();

        add_filter('cuar/core/log/event-types', array(&$this, 'add_default_event_types'));

        if (is_admin()) {
            add_filter('cuar/core/log/table-displayable-meta', array(&$this, 'get_table_displayable_meta'), 10, 1);
            add_filter('cuar/core/log/table-meta-pill-descriptor', array(&$this, 'get_table_meta_pill'), 10, 3);
        }

        add_action('cuar/notifications/on-notification-sent', array(&$this, 'on_notification_sent'), 10, 4);

        if ($this->no_addon->settings()->should_log_emails()) {
            add_action('cuar/notifications/on-email-sent', array(&$this, 'on_email_sent'), 10, 5);
        }
    }


    /**
     * Add the event types we are currently supporting to the main array
     *
     * @param array $default_types the currently available types
     *
     * @return array
     */
    public function add_default_event_types($default_types)
    {
        return array_merge($default_types, array(
            self::$TYPE_NOTIFICATION_SENT => __('Notification sent', 'cuarno'),
            self::$TYPE_EMAIL_SENT        => __('Email sent', 'cuarno'),
        ));
    }

    /*------- LOG VIEWER -----------------------------------------------------------------------------------------*/

    public function get_table_displayable_meta($meta)
    {
        return array_merge($meta, array(
            self::$META_NOTIFICATION_ID,
            self::$META_RECIPIENT_ID,
            self::$META_EXTRA,
            self::$META_SUBJECT,
            self::$META_BODY,
            self::$META_HEADERS,
        ));
    }

    /**
     * @param array         $pill
     * @param string        $meta
     * @param CUAR_LogEvent $item
     *
     * @return array
     */
    public function get_table_meta_pill($pill, $meta, $item)
    {
        switch ($meta) {
            case self::$META_NOTIFICATION_ID:
                $notificationParams = $this->no_addon->settings()->get_configurable_notification($item->$meta, $item->related_object_type);
                if ($notificationParams !== null) {
                    $pill['value'] = $notificationParams['id'];
                    $pill['title'] = esc_html($notificationParams['description']);
                } else {
                    $pill['value'] = __('Unknonwn notification type', 'cuarno');
                    $pill['title'] = '';
                }
                break;

            case self::$META_RECIPIENT_ID:
                $user = new WP_User($item->$meta);
                $pill['value'] = esc_attr(__('To: ', 'cuarno') . $user->display_name);
                $pill['title'] = $user->user_email;
                break;

            case self::$META_EXTRA:
                $pill['value'] = esc_attr(__('Extra', 'cuarno'));
                $pill['title'] = esc_html(json_encode($item->$meta));
                break;

            case self::$META_HEADERS:
                $pill['value'] = esc_attr(__('Headers', 'cuarno'));
                $pill['title'] = esc_html(json_encode($item->$meta));
                break;

            case self::$META_SUBJECT:
                $pill['value'] = esc_attr(__('Subject', 'cuarno'));
                $pill['title'] = esc_html($item->$meta);
                break;

            case self::$META_BODY:
                $pill['value'] = esc_attr(__('Body', 'cuarno'));
                $pill['title'] = esc_html($item->$meta);
                break;
        }

        return $pill;
    }

    /*------- LOGGING --------------------------------------------------------------------------------------------*/

    public function on_email_sent($to_name, $to_address, $subject, $body, $headers)
    {
        /** @var CUAR_LogAddOn $log_addon */
        $log_addon = $this->plugin->get_addon('log');
        $default_meta = $log_addon->get_default_event_meta();

        $new_meta = array(
            self::$META_HEADERS => $headers,
            self::$META_SUBJECT => $subject,
            self::$META_BODY    => $body,
        );

        $should_log_event = apply_filters('cuar/core/log/should-log-event?event=' . self::$TYPE_NOTIFICATION_SENT, true, $to_address);
        if ($should_log_event) {
            $this->logger->log_event(
                self::$TYPE_EMAIL_SENT,
                -1,
                'string||' . $to_address,
                $default_meta + $new_meta);
        }
    }

    public function on_notification_sent($recipient_id, $notification_id, $post_id, $extra)
    {
        /** @var CUAR_LogAddOn $log_addon */
        $log_addon = $this->plugin->get_addon('log');
        $default_meta = $log_addon->get_default_event_meta();

        $new_meta = array(
            self::$META_NOTIFICATION_ID => $notification_id,
            self::$META_RECIPIENT_ID    => $recipient_id,
            self::$META_EXTRA           => $extra,
        );

        $post_id = $post_id > 0 ? $post_id : -1;
        $post_type = $post_id > 0 ? get_post_type($post_id) : null;

        $should_log_event = apply_filters('cuar/core/log/should-log-event?event=' . self::$TYPE_NOTIFICATION_SENT, true, $notification_id);
        if ($should_log_event) {
            $this->logger->log_event(
                self::$TYPE_NOTIFICATION_SENT,
                $post_id,
                $post_type,
                $default_meta + $new_meta);
        }
    }
}