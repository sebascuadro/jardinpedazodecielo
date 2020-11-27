<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_NotificationsSettingsHelper
{
    public static $OPTION_LOG_EMAILS = 'cuar_no_log_emails';
    public static $OPTION_NOTIFICATIONS = 'cuar_no_notifications';
    public static $OPTION_NOTIFICATION_FORMAT = 'cuar_no_format';
    public static $OPTION_ENABLE_AUTOP = 'cuar_no_enable_autop';
    public static $OPTION_NOTIFICATION_FROM_ADDRESS = 'cuar_no_from_address';
    public static $OPTION_NOTIFICATION_FROM_NAME = 'cuar_no_from_name';
    public static $OPTION_NOTIFICATION_TEMPLATE = 'cuar_no_layout';
    public static $OPTION_NOTIFICATION_HEADER_IMAGE = 'cuar_no_header_image';
    public static $OPTION_EMAIL_TEMPLATE_OPTION_FORMAT = 'cuar_no_template_{{template_id}}_{{option_id}}';

    /** @var CUAR_Plugin */
    private $plugin;

    /** @var CUAR_NotificationsAddOn */
    private $no_addon;

    /**
     * Constructor
     */
    public function __construct($plugin, $no_addon)
    {
        $this->plugin = $plugin;
        $this->no_addon = $no_addon;

        if (is_admin()) {
            // Add a settings tab for invoices
            add_filter('cuar/core/settings/settings-tabs', array(&$this, 'add_settings_tab'), 530, 1);
            add_action('cuar/core/settings/print-settings?tab=cuar_notifications', array(&$this, 'print_settings'), 10, 2);
            add_action('cuar/core/settings/print-settings?tab=cuar_notifications', array(&$this, 'process_settings_actions'), 10, 2);
            add_filter('cuar/core/settings/validate-settings?tab=cuar_notifications', array(&$this, 'validate_options'), 10, 3);

            add_action('cuar/notifications/settings/print-template-settings', array(&$this, 'print_default_email_template_settings'), 10, 3);
            add_filter('cuar/notifications/settings/validate-template-settings', array(&$this, 'validate_default_email_template_settings'), 10, 4);
        }
    }

    /*------- INVOICE DEFAULTS -------------------------------------------------------------------------------------*/

    public function get_configurable_notification($notification_id, $post_type = '')
    {
        $notifications = $this->get_configurable_notifications();

        $out = null;

        $specialized_notification_id = $notification_id . '-' . $post_type;
        if (isset($notifications[$specialized_notification_id])) {
            $out = $notifications[$specialized_notification_id];
            $out['id'] = $specialized_notification_id;
        }

        if (isset($notifications[$notification_id])) {
            $out = $notifications[$notification_id];
            $out['id'] = $notification_id;
        }

        return $out;
    }

    public function get_configurable_notifications()
    {
        $notifications = array();

        $private_types = $this->plugin->get_private_types();
        foreach ($private_types as $type => $desc) {
            $notifications['private-content-published-' . $type] = array(
                'title'           => sprintf(__('%s created', 'cuarno'), $desc['label-singular']),
                'description'     => sprintf(__('Email that gets sent to a user when new private content of type &laquo; %s &raquo; is published for him.',
                    'cuarno'), $desc['label-singular']),
                'available_modes' => array(
                    'all'      => __('Enabled', 'cuarno'),
                    'disabled' => __('Disabled', 'cuarno')
                ),
            );
        }

        $notifications['private-content-published-admin'] = array(
            'title'           => __('Content created', 'cuarno') . ' <span class="dashicons dashicons-admin-users"></span>',
            'description'     => __('Email that gets sent to the administrators when new private content is published by someone.',
                'cuarno'),
            'available_modes' => array(
                'all'      => __('Enabled', 'cuarno'),
                'disabled' => __('Disabled', 'cuarno')
            ),
        );

        $notifications['new-comment'] = array(
            'title'           => __('Comment posted', 'cuarno'),
            'description'     => __('Email that gets sent to the private content author and owners when a comment is added.',
                'cuarno'),
            'available_modes' => array(
                'all'      => __('Enabled', 'cuarno'),
                'disabled' => __('Disabled', 'cuarno')
            ),
        );

        $notifications['new-comment-moderated'] = array(
            'title'           => __('Comment held for moderation', 'cuarno'),
            'description'     => __('Email that gets sent to the administrators when a comment is held for moderation.',
                'cuarno'),
            'available_modes' => array(
                'all'      => __('Enabled', 'cuarno'),
                'disabled' => __('Disabled', 'cuarno')
            ),
        );

        $co_addon = $this->plugin->get_addon('collaboration');
        if (isset($co_addon)) {
            $notifications['private-content-moderated'] = array(
                'title'           => __('Content held for moderation', 'cuarno'),
                'description'     => __('Email that gets sent to you when a new private post is created from the frontend but is '
                    . 'not yet published (waiting for review before being published).', 'cuarno'),
                'available_modes' => array(
                    'all'      => __('Enabled', 'cuarno'),
                    'disabled' => __('Disabled', 'cuarno')
                ),
            );
        }

        $notifications['private-file-downloaded'] = array(
            'title'                => __('File downloaded', 'cuarno'),
            'description'          => __('Email that gets sent when a file is downloaded.', 'cuarno'),
            'available_modes'      => array(
                'all'      => __('Always', 'cuarno'),
                'first'    => __('Only the first time', 'cuarno'),
                'disabled' => __('Disabled', 'cuarno')
            ),
            'available_recipients' => array(
                'admin'  => __('Site administrators', 'cuarno'),
                'author' => __('Document author', 'cuarno'),
                'all'    => __('All recipients', 'cuarno')
            ),
        );

        $ta_addon = $this->plugin->get_addon('tasks');
        if (isset($ta_addon)) {
            $notifications['tasklist-completed'] = array(
                'title'                => __('Task list completed', 'cuarno'),
                'description'          => __('Email that gets sent when all the tasks in the list have been checked.', 'cuarno'),
                'available_modes'      => array(
                    'all'      => __('Always', 'cuarno'),
                    'disabled' => __('Disabled', 'cuarno')
                ),
                'available_recipients' => array(
                    'owner'  => __('Task list owners', 'cuarno'),
                    'author' => __('Task list author', 'cuarno'),
                    'all'    => __('All recipients', 'cuarno')
                ),
            );

            $notifications['task-soon-overdue'] = array(
                'title'                => __('Task soon overdue', 'cuarno'),
                'description'          => __('Email that gets sent when a task\'s due date is approaching (requires WordPress CRON enabled).', 'cuarno'),
                'available_modes'      => array(
                    'all'      => __('Always', 'cuarno'),
                    'disabled' => __('Disabled', 'cuarno')
                ),
                'available_recipients' => array(
                    'owner'  => __('Task list owners', 'cuarno'),
                    'author' => __('Task list author', 'cuarno'),
                    'all'    => __('All recipients', 'cuarno')
                ),
            );

            $notifications['task-overdue-reminder'] = array(
                'title'                => __('Task overdue', 'cuarno'),
                'description'          => __('Reminder that gets sent when a task\'s due date is passed (requires WordPress CRON enabled).', 'cuarno'),
                'available_modes'      => array(
                    'all'      => __('Always', 'cuarno'),
                    'disabled' => __('Disabled', 'cuarno')
                ),
                'available_recipients' => array(
                    'owner'  => __('Task list owners', 'cuarno'),
                    'author' => __('Task list author', 'cuarno'),
                    'all'    => __('All recipients', 'cuarno')
                ),
            );
        }

        $me_addon = $this->plugin->get_addon('conversations');
        if (isset($me_addon)) {
            $notifications['private-conversation-new-reply'] = array(
                'title'           => __('New Reply', 'cuarno'),
                'description'     => __('Email that gets sent to the participants of a conversation when a new reply is posted.',
                    'cuarno'),
                'available_modes' => array(
                    'all'      => __('Enabled', 'cuarno'),
                    'disabled' => __('Disabled', 'cuarno')
                ),
            );
        }

        $lf_addon = $this->plugin->get_addon('login-forms');
        if (isset($lf_addon)) {
            $notifications['forgot-password'] = array(
                'title'           => __('Password reset', 'cuarno'),
                'description'     => __('Email that gets sent to the user requesting a password reset. This must contain a link to the page where the user can effectively reset the password.',
                    'cuarno'),
                'available_modes' => array(
                    'all'      => __('Enabled', 'cuarno'),
                    'disabled' => __('Disabled', 'cuarno')
                ),
            );
            $notifications['password-reset-admin'] = array(
                'title'           => __('Password reset', 'cuarno') . ' <span class="dashicons dashicons-admin-users"></span>',
                'description'     => __('Email that gets sent to the administrators after a user resets his password.',
                    'cuarno'),
                'available_modes' => array(
                    'all'      => __('Enabled', 'cuarno'),
                    'disabled' => __('Disabled', 'cuarno')
                ),
            );
            $notifications['register'] = array(
                'title'           => __('Registration', 'cuarno'),
                'description'     => __('Email that gets sent to the user who registered on the website. This must contain a link to the page where the user can set his password.',
                    'cuarno'),
                'available_modes' => array(
                    'all'      => __('Enabled', 'cuarno'),
                    'disabled' => __('Disabled', 'cuarno')
                ),
            );
            $notifications['register-admin'] = array(
                'title'           => __('Registration', 'cuarno') . ' <span class="dashicons dashicons-admin-users"></span>',
                'description'     => __('Email that gets sent to the administrators after a user registers on the website.',
                    'cuarno'),
                'available_modes' => array(
                    'all'      => __('Enabled', 'cuarno'),
                    'disabled' => __('Disabled', 'cuarno')
                ),
            );
        }

        /** @var CUAR_PaymentsAddOn $pa_addon */
        $pa_addon = $this->plugin->get_addon('payments');
        if (isset($pa_addon) && $pa_addon->is_enabled()) {
            $notifications['payment-completed'] = array(
                'title'           => __('Payment accepted', 'cuarno'),
                'description'     => __('Email that gets sent to someone after his payment has been marked as "complete".',
                    'cuarno'),
                'available_modes' => array(
                    'all'      => __('Enabled', 'cuarno'),
                    'disabled' => __('Disabled', 'cuarno')
                ),
            );
            $notifications['payment-rejected'] = array(
                'title'           => __('Payment rejected', 'cuarno'),
                'description'     => __('Email that gets sent to someone after his payment has been marked as "rejected".',
                    'cuarno'),
                'available_modes' => array(
                    'all'      => __('Enabled', 'cuarno'),
                    'disabled' => __('Disabled', 'cuarno')
                ),
            );
            $notifications['payment-completed-admin'] = array(
                'title'           => __('Payment accepted', 'cuarno') . ' <span class="dashicons dashicons-admin-users"></span>',
                'description'     => __('Email that gets sent to the administrators after a user payment is marked as completed.',
                    'cuarno'),
                'available_modes' => array(
                    'all'      => __('Enabled', 'cuarno'),
                    'disabled' => __('Disabled', 'cuarno')
                ),
            );
            $notifications['payment-rejected-admin'] = array(
                'title'           => __('Payment rejected', 'cuarno') . ' <span class="dashicons dashicons-admin-users"></span>',
                'description'     => __('Email that gets sent to the administrators after a user payment is marked as rejected.',
                    'cuarno'),
                'available_modes' => array(
                    'all'      => __('Enabled', 'cuarno'),
                    'disabled' => __('Disabled', 'cuarno')
                ),
            );
            $notifications['payment-pending-admin'] = array(
                'title'           => __('Payment pending', 'cuarno') . ' <span class="dashicons dashicons-admin-users"></span>',
                'description'     => __('Email that gets sent to the administrators after a user payment is marked as pending.',
                    'cuarno'),
                'available_modes' => array(
                    'all'      => __('Enabled', 'cuarno'),
                    'disabled' => __('Disabled', 'cuarno')
                ),
            );
        }

        uasort($notifications, array(&$this, 'sort_configurable_notification_items'));

        return apply_filters('cuar/notifications/configurable-notifications', $notifications);
    }

    /**
     * uasort callback
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    public function sort_configurable_notification_items($a, $b)
    {
        return strcmp($a['title'], $b['title']);
    }

    public function get_notification_params($notification_id, $post_type = '')
    {
        // Special case for the test notification
        if ($notification_id === 'test') {
            return array(
                'id'      => $notification_id,
                'mode'    => 'all',
                'subject' => __('[{{site_name}}] Test message', 'cuarno'),
                'heading' => __('Congratulations!', 'cuarno'),
                'body'    => __("Hello {{logged_user_name}},\n\nThis is the sample email you are supposed to receive. If you are reading this, the server seems to be properly configured.\n\nBest regards,\n\nYour WP Customer Area plugin",
                    'cuarno'),
            );
        }

        // Just have a look into our saved notification parameters
        $all_params = $this->plugin->get_option(self::$OPTION_NOTIFICATIONS);
        $option_name = empty($post_type) ? $notification_id : $notification_id . '-' . $post_type;

        if (isset($all_params[$option_name])) {
            $params = $all_params[$option_name];
        } else if (isset($all_params[$notification_id])) {
            $params = $all_params[$notification_id];
        } else {
            // Default notifications
            if ($option_name === 'private-file-downloaded') {
                return array(
                    'id'        => $option_name,
                    'mode'      => 'first',
                    'recipient' => 'admin',
                    'subject'   => __('[{{site_name}}] New file download', 'cuarno'),
                    'heading'   => __('New download', 'cuarno'),
                    'body'      => __("Hello {{to_name}},\n\nThe attachment <strong>{{attachment_name}}</strong> has been downloaded by <strong>{{logged_user_name}}</strong> on <a href=\"{{post_url}}\">{{post_title}}</a>\n\n", 'cuarno')
                );
            } else if ($option_name === 'private-conversation-new-reply') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] New reply to a conversation', 'cuarno'),
                    'heading' => __('Conversation updated', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\nA new reply has been posted in the conversation <a href=\"{{post_url}}\">{{post_title}}</a> by {{author_name}}.", 'cuarno')
                );
            } else if ($option_name === 'private-content-published-admin') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] Private content published', 'cuarno'),
                    'heading' => __('New content', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\nA new post has been published in the customer area:\n\n<a href=\"{{post_url}}\">{{post_title}}</a> by <strong>{{author_name}}</strong> for <strong>{{owner_name}}</strong>", 'cuarno')
                );
            } else if ($option_name === 'private-content-moderated') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] Private content held for moderation', 'cuarno'),
                    'heading' => __('Review required', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\nNew <em>{{post_type}}</em> created by <strong>{{author_name}}</strong> for <strong>{{owner_name}}</strong> is held for moderation. You can review it on that page: <a href=\"{{review_url}}\">{{post_title}}</a>", 'cuarno')
                );
            } else if ($option_name === 'new-comment') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] New comment on "{{post_title}}"', 'cuarno'),
                    'heading' => __('New comment', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\n<a href=\"{{commenter_url}}\">{{commenter_name}}</a> ({{commenter_email}}) has left a comment on <a href=\"{{post_url}}#comments\">{{post_title}}</a>:\n\n{{comment_content}}",
                        'cuarno')
                );
            } else if ($option_name === 'new-comment-moderated') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] Comment held for moderation on "{{post_title}}"', 'cuarno'),
                    'heading' => __('Review required', 'cuarno'),
                    'body'    => __("Hello {{to_name}},A new comment on the post <a href=\"{{post_url}}\">{{post_title}}</a> is waiting for your approval.\n\n<ul>\n  <li><strong>Author name</strong>: #{{commenter_name}}</li>\n  <li><strong>Author email</strong>: {{commenter_email}}</li>\n  <li><strong>Author URL</strong>: {{commenter_url}}</li>\n</ul>\n\n{{comment_content}}\n\n{{comment_moderation_links}}",
                        'cuarno')
                );
            } else if ($option_name === 'private-content-published-cuar_private_file') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] New file available', 'cuarno'),
                    'heading' => __('New file', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\n{{author_name}} has published new content for you. Just follow this link to read it: <a href=\"{{post_url}}\">{{post_title}}</a>\n\nThis file has some attachments which can be downloaded: \n\n{{attachment_list}}",
                        'cuarno')
                );
            } else if ($option_name === 'private-content-published-cuar_project') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] New project available', 'cuarno'),
                    'heading' => __('New project', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\n{{author_name}} has created a new project where you are involved. Just follow this link to view it: <a href=\"{{post_url}}\">{{post_title}}</a>",
                        'cuarno')
                );
            } else if ($option_name === 'private-content-published-cuar_invoice') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] Your invoice', 'cuarno'),
                    'heading' => __('Invoice #{{invoice_number}}', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\n{{author_name}} has issued an invoice for you:\n\n<ul>\n  <li><strong>Invoice</strong>: #{{post_title}}</li>\n  <li><strong>Amount</strong>: {{invoice_total}}</li>\n  <li><strong>Due date</strong>: {{invoice_due_date}}</li>\n</ul>\n\nYou can view it online it by following <a href=\"{{post_url}}\">this link</a>\n\nThank you.",
                        'cuarno')
                );
            } else if (false !== strpos($option_name, 'private-content-published-cuar_conversation')) {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] New conversation', 'cuarno'),
                    'heading' => __('New conversation', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\n{{author_name}} has started a conversation with you. Just follow this link to read it: <a href=\"{{post_url}}\">{{post_title}}</a>",
                        'cuarno')
                );
            } else if (false !== strpos($option_name, 'private-content-published-')) {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] New content available', 'cuarno'),
                    'heading' => __('New document', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\n{{author_name}} has published new content for you. Just follow this link to read it: <a href=\"{{post_url}}\">{{post_title}}</a>",
                        'cuarno')
                );
            } else if ($option_name === 'forgot-password') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] Password Reset', 'cuarno'),
                    'heading' => __('Reset your password', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\nSomeone requested that the password be reset on <a href=\"{{site_url}}\">{{site_name}}</a>. Your username there is: {{user_login}}\n\nIf this was a mistake, just ignore this email and nothing will happen.\n\nTo reset your password, visit the following address: <a href=\"{{reset_password_url}}\">Reset password</a>",
                        'cuarno')
                );
            } else if ($option_name === 'register') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] Welcome', 'cuarno'),
                    'heading' => __('Congratulations', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\nThank you for registering on <a href=\"{{site_url}}\">{{site_name}}</a>. Your username is: {{user_login}}\n\nTo set your password, visit the following address: <a href=\"{{reset_password_url}}\">Set password</a>",
                        'cuarno')
                );
            } else if ($option_name === 'password-reset-admin') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] Password lost and changed', 'cuarno'),
                    'heading' => __('Password reset', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\nA user changed his password on <a href=\"{{site_url}}\">{{site_name}}</a>. Here are some details about that user:\n\nUsername: {{user_login}}\nEmail: {{user_email}}",
                        'cuarno')
                );
            } else if ($option_name === 'register-admin') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] New user registration', 'cuarno'),
                    'heading' => __('New user', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\nA new user has registered on <a href=\"{{site_url}}\">{{site_name}}</a>. Here is some more information:\n\nUsername: {{user_login}}\nEmail: {{user_email}}",
                        'cuarno')
                );
            } else if ($option_name === 'payment-completed') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] Thank you for your payment', 'cuarno'),
                    'heading' => __('Payment accepted', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\nThank you for your payment of {{payment_amount}} on <a href=\"{{site_url}}\">{{site_name}}</a>.",
                        'cuarno')
                );
            } else if ($option_name === 'payment-rejected') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] Your payment has been rejected', 'cuarno'),
                    'heading' => __('Payment rejected', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\nUnfortunately your payment of {{payment_amount}} has been rejected on <a href=\"{{site_url}}\">{{site_name}}</a>. If you feel this is an error, please contact us. Else please try again.",
                        'cuarno')
                );
            } else if ($option_name === 'payment-completed-admin') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] Payment accepted', 'cuarno'),
                    'heading' => __('Payment accepted &ndash; {{payment_amount}}', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\nA payment has been validated on <a href=\"{{site_url}}\">{{site_name}}</a>.\n\n<a href=\"{{payment_edit_url}}\">Show payment</a>.",
                        'cuarno')
                );
            } else if ($option_name === 'payment-rejected-admin') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] Payment rejected', 'cuarno'),
                    'heading' => __('Payment rejected &ndash; {{payment_amount}}', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\nA payment has been rejected on <a href=\"{{site_url}}\">{{site_name}}</a>.\n\n<a href=\"{{payment_edit_url}}\">Show payment</a>.",
                        'cuarno')
                );
            } else if ($option_name === 'payment-pending-admin') {
                return array(
                    'id'      => $option_name,
                    'mode'    => 'all',
                    'subject' => __('[{{site_name}}] Payment pending', 'cuarno'),
                    'heading' => __('Payment pending &ndash; {{payment_amount}}', 'cuarno'),
                    'body'    => __("Hello {{to_name}},\n\nA payment has been received but is still pending.\n\n<a href=\"{{payment_edit_url}}\">Show payment</a>.",
                        'cuarno')
                );
            } else if ($option_name === 'tasklist-completed') {
                return array(
                    'id'        => $option_name,
                    'mode'      => 'all',
                    'recipient' => 'all',
                    'subject'   => __('[{{site_name}}] Task list has been completed', 'cuarno'),
                    'heading'   => __('{{post_title}} has just been completed', 'cuarno'),
                    'body'      => __("Hello {{to_name}},\n\nThe task list \"{{post_title}}\" is now complete. Each of its tasks has been checked.\n\n<a href=\"{{post_url}}\">Show task list</a>.",
                        'cuarno')
                );
            } else if ($option_name === 'task-overdue-reminder') {
                return array(
                    'id'        => $option_name,
                    'mode'      => 'all',
                    'recipient' => 'all',
                    'subject'   => __('[{{site_name}}] Task is overdue', 'cuarno'),
                    'heading'   => __('A task was due on: {{task_due_date}}', 'cuarno'),
                    'body'      => __("Hello {{to_name}},\n\nA task on the list \"{{post_title}}\" is due since {{task_due_date}}. Here is the task summary:\n\n{{task_description}}\n\n<a href=\"{{post_url}}\">Show task list</a>.",
                        'cuarno')
                );
            } else if ($option_name === 'task-soon-overdue') {
                return array(
                    'id'        => $option_name,
                    'mode'      => 'all',
                    'recipient' => 'all',
                    'subject'   => __('[{{site_name}}] Task\'s due date is approaching', 'cuarno'),
                    'heading'   => __('A task is due on {{task_due_date}}', 'cuarno'),
                    'body'      => __("Hello {{to_name}},\n\nA task on the list \"{{post_title}}\" will be due on {{task_due_date}}. Here is the task summary:\n\n{{task_description}}\n\n<a href=\"{{post_url}}\">Show task list</a>.",
                        'cuarno')
                );
            } else {
                return array(
                    'id'      => null,
                    'mode'    => 'disabled',
                    'subject' => 'INVALID NOTIFICATION PARAMS',
                    'body'    => 'INVALID NOTIFICATION PARAMS',
                    'heading' => 'INVALID NOTIFICATION PARAMS',
                );
            }
        }

        if ( !isset($params['heading'])) {
            $params['heading'] = '';
        }

        return $params;
    }

    public function get_available_email_layouts()
    {
        $layouts = apply_filters('cuar/notifications/settings/available-layouts', array(
            'default'    => __('Default', 'cuarno'),
            'plain_html' => __('Plain HTML', 'cuarno'),
            'plain_text' => __('Plain text', 'cuarno'),
        ));

        asort($layouts);

        return $layouts;
    }

    public function get_email_layout()
    {
        return $this->plugin->get_option(self::$OPTION_NOTIFICATION_TEMPLATE);
    }

    public function get_email_format()
    {
        return $this->plugin->get_option(self::$OPTION_NOTIFICATION_FORMAT);
    }

    public function is_autop_enabled()
    {
        return $this->plugin->get_option(self::$OPTION_ENABLE_AUTOP);
    }

    public function should_log_emails()
    {
        return $this->plugin->get_option(self::$OPTION_LOG_EMAILS);
    }

    public function get_from_address()
    {
        return $this->plugin->get_option(self::$OPTION_NOTIFICATION_FROM_ADDRESS);
    }

    public function get_from_name()
    {
        return $this->plugin->get_option(self::$OPTION_NOTIFICATION_FROM_NAME);
    }


    /*------- OPTIONS --------------------------------------------------------------------------------------------*/

    /**
     * Set the default values for the options
     *
     * @param array $defaults
     *
     * @return array
     */
    public static function set_default_options($defaults)
    {
        $defaults[self::$OPTION_NOTIFICATION_FORMAT] = 'html';
        $defaults[self::$OPTION_NOTIFICATION_TEMPLATE] = 'default';
        $defaults[self::$OPTION_ENABLE_AUTOP] = true;
        $defaults[self::$OPTION_LOG_EMAILS] = false;

        // Notifications are from...
        $defaults[self::$OPTION_NOTIFICATION_FROM_ADDRESS] = get_bloginfo('admin_email');
        $defaults[self::$OPTION_NOTIFICATION_FROM_NAME] = get_bloginfo('name');

        // Set defaults for the default email template
        $colors = self::get_default_email_template_colors();
        foreach ($colors as $c) {
            $defaults[self::get_email_template_setting_id('default', $c['id'])] = $c['default'];
        }
        $defaults[self::get_email_template_setting_id('default', 'logo_url')] = '';

        return $defaults;
    }


    /*------- CUSTOMISATION OF THE PLUGIN SETTINGS PAGE -------------------------------------------------------------*/

    /**
     * Add a tab to the settings page
     *
     * @param array $tabs
     *
     * @return array
     */
    public function add_settings_tab($tabs)
    {
        $tabs['cuar_notifications'] = __('Notifications', 'cuarno');

        return $tabs;
    }

    /**
     * Add our fields to the settings page
     *
     * @param CUAR_Settings $cuar_settings The settings class
     */
    public function print_settings($cuar_settings, $options_group)
    {
        add_settings_section(
            'cuar_notifications_general',
            __('General', 'cuarno'),
            array(&$cuar_settings, 'print_empty_section_info'),
            CUAR_Settings::$OPTIONS_PAGE_SLUG
        );

        add_settings_field(
            self::$OPTION_NOTIFICATION_FORMAT,
            __('Format', 'cuarno'),
            array(&$cuar_settings, 'print_select_field'),
            CUAR_Settings::$OPTIONS_PAGE_SLUG,
            'cuar_notifications_general',
            array(
                'option_id' => self::$OPTION_NOTIFICATION_FORMAT,
                'options'   => array(
                    'plain' => __('Plain text', 'cuarno'),
                    'html'  => __('HTML', 'cuarno')
                ),
                'after'     => '<p class="description">'
                    . __('The format in which notifications get sent. HTML might not always be properly formatted.', 'cuarno')
                    . '</p>'
            )
        );

        add_settings_field(
            self::$OPTION_ENABLE_AUTOP,
            __('Clean HTML', 'cuarno'),
            array(&$cuar_settings, 'print_input_field'),
            CUAR_Settings::$OPTIONS_PAGE_SLUG,
            'cuar_notifications_general',
            array(
                'option_id' => self::$OPTION_ENABLE_AUTOP,
                'type'      => 'checkbox',
                'after'     => __('Automatically add paragraphs to HTML notifications', 'cuarno')
                    . '<p class="description">'
                    . __('You can uncheck this box if you already have valid HTML notification content. This is only effective if the format set above is HTML.',
                        'cuarno')
                    . '</p>'
            )
        );

        add_settings_field(
            self::$OPTION_NOTIFICATION_FROM_NAME,
            __('From', 'cuarno'),
            array(&$cuar_settings, 'print_input_field'),
            CUAR_Settings::$OPTIONS_PAGE_SLUG,
            'cuar_notifications_general',
            array(
                'option_id' => self::$OPTION_NOTIFICATION_FROM_NAME,
                'type'      => 'text'
            )
        );

        add_settings_field(
            self::$OPTION_NOTIFICATION_FROM_ADDRESS,
            '',
            array(&$cuar_settings, 'print_input_field'),
            CUAR_Settings::$OPTIONS_PAGE_SLUG,
            'cuar_notifications_general',
            array(
                'option_id' => self::$OPTION_NOTIFICATION_FROM_ADDRESS,
                'type'      => 'text',
                'after'     => '<p class="description">'
                    . __('The emails will be shown as sent from this name and address.', 'cuarno')
                    . '</p>'
            )
        );

        add_settings_field(
            self::$OPTION_LOG_EMAILS,
            __('Log emails', 'cuarno'),
            array(&$cuar_settings, 'print_input_field'),
            CUAR_Settings::$OPTIONS_PAGE_SLUG,
            'cuar_notifications_general',
            array(
                'option_id' => self::$OPTION_LOG_EMAILS,
                'type'      => 'checkbox',
                'after'     => __('Log all the emails that are sent by the notifications addon. Useful for debugging.', 'cuarno')
                    . '<p class="description">'
                    . __('Enable this when you think that emails are not sent properly. When everything works, you may disable this option because it will add quite a log of data to your database.',
                        'cuarno')
                    . '</p>'
            )
        );

        add_settings_section(
            'cuar_notification_template',
            __('Email template', 'cuarno'),
            array(&$this, 'print_layout_section_info'),
            CUAR_Settings::$OPTIONS_PAGE_SLUG
        );

        add_settings_field(
            self::$OPTION_NOTIFICATION_TEMPLATE,
            __('Template', 'cuarno'),
            array(&$cuar_settings, 'print_select_field'),
            CUAR_Settings::$OPTIONS_PAGE_SLUG,
            'cuar_notification_template',
            array(
                'option_id' => self::$OPTION_NOTIFICATION_TEMPLATE,
                'options'   => $this->get_available_email_layouts(),
                'multiple'  => false,
                'after'     => '<p class="description">'
                    . __('The template used to format the emails sent by WP Customer Area.', 'cuarno')
                    . '</p>',
                'class'     => 'cuar-js-layout-selector',
            )
        );

        do_action('cuar/notifications/settings/print-template-settings', $this, $cuar_settings, 'cuar_notification_template');

        add_settings_field(
            'cuar_send_test_notification',
            __('Test', 'cuarno'),
            array(&$cuar_settings, 'print_submit_button'),
            CUAR_Settings::$OPTIONS_PAGE_SLUG,
            'cuar_notification_template',
            array(
                'option_id'    => 'cuar_send_test_notification',
                'label'        => __('Save and send a sample email', 'cuarno'),
                'nonce_action' => 'send_test_notification',
                'nonce_name'   => 'send_test_notification_nonce',
                'before'       => '<p>' . __('Send a test message to the email linked to your account.', 'cuarno') . '</p>'
            )
        );

        add_settings_section(
            'cuar_notifications_params',
            __('Individual notification settings', 'cuarno'),
            array(&$this, 'print_notifications_section_info'),
            CUAR_Settings::$OPTIONS_PAGE_SLUG
        );

        add_settings_field(
            'cuar_reset_notification_messages',
            __('Reset messages', 'cuarno'),
            array(&$cuar_settings, 'print_submit_button'),
            CUAR_Settings::$OPTIONS_PAGE_SLUG,
            'cuar_notifications_params',
            array(
                'option_id'       => 'cuar_reset_notification_messages',
                'label'           => __('Reset Messages', 'cuarno'),
                'nonce_action'    => 'reset_notification_messages',
                'nonce_name'      => 'reset_notification_messages_nonce',
                'before'          => '<p>'
                    . __('All the notification subjects, headings and contents will be reset to the default values.', 'cuarno')
                    . '</p>',
                'confirm_message' => __('Are you sure that you want to reset all the notification messages?', 'cuarno')
            )
        );
    }

    public function process_settings_actions()
    {
        if (get_transient('cuar_send_test_notification') == 'yes') {
            delete_transient('cuar_send_test_notification');
            $this->no_addon->send_test_email();
        }
    }

    /**
     * Validate our options
     *
     * @param CUAR_Settings $cuar_settings
     * @param array         $input
     * @param array         $validated
     *
     * @return array
     */
    public function validate_options($validated, $cuar_settings, $input)
    {
        $cuar_settings->validate_boolean($input, $validated, self::$OPTION_LOG_EMAILS);
        $cuar_settings->validate_boolean($input, $validated, self::$OPTION_ENABLE_AUTOP);
        $cuar_settings->validate_enum($input, $validated, self::$OPTION_NOTIFICATION_FORMAT, array('plain', 'html'));
        $cuar_settings->validate_enum($input, $validated, self::$OPTION_NOTIFICATION_TEMPLATE, array_keys($this->get_available_email_layouts()));
        $cuar_settings->validate_email($input, $validated, self::$OPTION_NOTIFICATION_FROM_ADDRESS);
        $cuar_settings->validate_not_empty($input, $validated, self::$OPTION_NOTIFICATION_FROM_NAME);

        $notifications = $this->get_configurable_notifications();
        $defaults = $this->plugin->get_default_options();
        $validated[self::$OPTION_NOTIFICATIONS] = array();

        foreach ($notifications as $notif_id => $notif_props) {
            $validated[self::$OPTION_NOTIFICATIONS][$notif_id] = array();
            $mode = $input[self::$OPTION_NOTIFICATIONS][$notif_id]['mode'];
            $recipient = isset($input[self::$OPTION_NOTIFICATIONS][$notif_id]['recipient'])
                ? $input[self::$OPTION_NOTIFICATIONS][$notif_id]['recipient']
                : null;
            $subject = $input[self::$OPTION_NOTIFICATIONS][$notif_id]['subject'];
            $heading = $input[self::$OPTION_NOTIFICATIONS][$notif_id]['heading'];
            $body = $input[self::$OPTION_NOTIFICATIONS][$notif_id]['body'];

            if ( !key_exists($mode, $notif_props['available_modes'])) {
                add_settings_error($notif_id . '_mode', 'settings-errors', $mode . __(' is not a valid value for ', 'cuarno') . $notif_props['title'],
                    'error');
                $validated[self::$OPTION_NOTIFICATIONS][$notif_id]['mode'] = $defaults[self::$OPTION_NOTIFICATIONS][$notif_id]['mode'];
            } else {
                $validated[self::$OPTION_NOTIFICATIONS][$notif_id]['mode'] = $mode;
            }

            if ( !is_null($recipient) && !key_exists($recipient, $notif_props['available_recipients'])) {
                add_settings_error($notif_id . '_recipient', 'settings-errors', $mode . __(' is not a valid value for ', 'cuarno') . $notif_props['title'],
                    'error');
                $validated[self::$OPTION_NOTIFICATIONS][$notif_id]['recipient'] = $defaults[self::$OPTION_NOTIFICATIONS][$notif_id]['recipient'];
            } else if ( !is_null($recipient)) {
                $validated[self::$OPTION_NOTIFICATIONS][$notif_id]['recipient'] = $recipient;
            }

            if (empty($subject)) {
                add_settings_error($notif_id . '_subject', 'settings-errors', printf(__('You must set a subject for %s', 'cuarno'), $notif_props['title']),
                    'error');
                $validated[self::$OPTION_NOTIFICATIONS][$notif_id]['subject'] = $defaults[self::$OPTION_NOTIFICATIONS][$notif_id]['subject'];
            } else {
                $validated[self::$OPTION_NOTIFICATIONS][$notif_id]['subject'] = $subject;
            }

            if (empty($input[self::$OPTION_NOTIFICATIONS][$notif_id]['body'])) {
                add_settings_error($notif_id . '_body', 'settings-errors', printf(__('You must set a body for %s', 'cuarno'), $notif_props['title']),
                    'error');
                $validated[self::$OPTION_NOTIFICATIONS][$notif_id]['body'] = $defaults[self::$OPTION_NOTIFICATIONS][$notif_id]['body'];
            } else {
                $validated[self::$OPTION_NOTIFICATIONS][$notif_id]['body'] = $body;
            }

            $validated[self::$OPTION_NOTIFICATIONS][$notif_id]['heading'] = $heading;
        }

        if (isset($_POST['cuar_send_test_notification']) && check_admin_referer('send_test_notification', 'send_test_notification_nonce')) {
            set_transient('cuar_send_test_notification', 'yes');
        }

        if (isset($_POST['cuar_reset_notification_messages']) && check_admin_referer('reset_notification_messages', 'reset_notification_messages_nonce')) {
            $validated[self::$OPTION_NOTIFICATIONS] = array();
        }

        $validated = apply_filters('cuar/notifications/settings/validate-template-settings', $validated, $cuar_settings, $input, $this);

        return $validated;
    }

    public function print_notifications_section_info()
    {
        echo '<p>'
            . __('You can customize every notification that will be sent to users by the Customer Area.', 'cuarno')
            . '</p>';


        include($this->plugin->get_template_file_path(
            CUARNO_INCLUDES_DIR,
            'notifications-tabs.template.php',
            'templates'));
    }

    public function print_layout_section_info()
    {
        include($this->plugin->get_template_file_path(
            CUARNO_INCLUDES_DIR,
            'notifications-settings-scripts.template.php',
            'templates'));
    }

    public static function get_email_template_setting_id($template_id, $option_id)
    {
        $final_option_id = str_replace('{{template_id}}', $template_id, self::$OPTION_EMAIL_TEMPLATE_OPTION_FORMAT);
        $final_option_id = str_replace('{{option_id}}', $option_id, $final_option_id);

        return $final_option_id;
    }

    public function get_email_template_setting($template_id, $option_id, $default_value = '')
    {
        $final_option_id = $this->get_email_template_setting_id($template_id, $option_id);
        $value = $this->plugin->get_option($final_option_id);

        return empty($value) ? $default_value : $value;
    }

    public function get_js_setting_marker($template_id)
    {
        return '<span class="cuar-js-layout-setting cuar-js-layout-setting-' . $template_id . '" style="display: none;"></span>';
    }

    /**
     * Print settings for the blocs template
     *
     * @param CUAR_NotificationsSettingsHelper $notifications_settings
     * @param CUAR_Settings                    $cuar_settings
     * @param string                           $section_id
     */
    public function print_default_email_template_settings($notifications_settings, $cuar_settings, $section_id)
    {
        $template_id = 'default';
        $colors = self::get_default_email_template_colors();

        add_settings_field(
            $notifications_settings->get_email_template_setting_id($template_id, 'logo_url'),
            __('Header image', 'cuarno'),
            array(&$cuar_settings, 'print_input_field'),
            CUAR_Settings::$OPTIONS_PAGE_SLUG,
            $section_id,
            array(
                'option_id' => $notifications_settings->get_email_template_setting_id($template_id, 'logo_url'),
                'type'      => 'upload',
                'after'     => '<p class="description">'
                    . __('Optional. The email template may use this image as a header.', 'cuarno')
                    . '</p>'
                    . $notifications_settings->get_js_setting_marker($template_id),
            )
        );

        foreach ($colors as $c) {
            add_settings_field(
                $notifications_settings->get_email_template_setting_id($template_id, $c['id']),
                $c['label'],
                array(&$cuar_settings, 'print_input_field'),
                CUAR_Settings::$OPTIONS_PAGE_SLUG,
                $section_id,
                array(
                    'option_id' => $notifications_settings->get_email_template_setting_id($template_id, $c['id']),
                    'type'      => 'color',
                    'after'     =>
                        '<p class="description">'
                        . $c['description'] . " " . sprintf(__('[Default: %s]', 'cuarno'), $c['default'])
                        . '</p>'
                        . $notifications_settings->get_js_setting_marker($template_id),
                )
            );
        }
    }

    private static function get_default_email_template_colors()
    {
        return array(
            array(
                'id'          => 'color_link',
                'label'       => __('Link color', 'cuarno'),
                'type'        => 'color',
                'default'     => '#4592d6',
                'description' => __('Hexadecimal format, example: #123456', 'cuarno')
            ),
            array(
                'id'          => 'color_main_bg',
                'label'       => __('Main background', 'cuarno'),
                'description' => __('Color for the background around the message box', 'cuarno'),
                'default'     => '#f3f3f3',
            ),
            array(
                'id'          => 'color_message_bg',
                'label'       => __('Text box background', 'cuarno'),
                'description' => __('Color for the box around the message', 'cuarno'),
                'default'     => '#ffffff',
            ),
        );
    }

    /**
     * Validate the settings for the blocs template
     *
     * @param CUAR_Settings                    $cuar_settings
     * @param array                            $input
     * @param array                            $validated
     * @param CUAR_NotificationsSettingsHelper $notifications_settings
     *
     * @return array
     */
    public function validate_default_email_template_settings($validated, $cuar_settings, $input, $notifications_settings)
    {
        $template_id = 'default';
        $colors = self::get_default_email_template_colors();
        foreach ($colors as $c) {
            $cuar_settings->validate_hex_color($input, $validated, $notifications_settings->get_email_template_setting_id($template_id, $c['id']));
        }

        $cuar_settings->validate_always($input, $validated, $notifications_settings->get_email_template_setting_id($template_id, 'logo_url'));

        return $validated;
    }
}