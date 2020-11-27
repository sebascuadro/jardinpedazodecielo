<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_NotificationsHooksHelper
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

        // Conversation replies
        add_action('cuar/private-content/conversations/on-new-reply', array(&$this, 'on_new_reply_to_conversation'), 1000, 2);

        // File download
        add_action('cuar/private-content/files/on-download', array(&$this, 'on_private_file_downloaded'), 10, 4);
        add_action('cuar/private-content/files/on-view', array(&$this, 'on_private_file_downloaded'), 10, 4);

        // Content creation
        if (is_admin()) {
            add_action('cuar/core/ownership/after-save-owner', array(&$this, 'on_private_post_published_admin'), 1000, 4);
            add_action('cuar/private-content/projects/on-project-created', array(&$this, 'on_project_published'), 10, 2);
        } else {
            add_action('cuar/private-content/collaboration/on-post-created', array(&$this, 'on_private_post_published_frontend'), 10, 4);
            add_action('cuar/private-content/projects/on-project-created', array(&$this, 'on_project_published'), 10, 2);
            add_action('cuar/private-content/conversations/on-conversation-started', array(&$this, 'on_conversation_started_frontend'), 10, 2);
            add_action('cuar/private-content/task-lists/on-list-created', array(&$this, 'on_private_post_published_frontend'), 10, 4);
        }

        // Authentication forms
        add_filter('cuar/authentication-forms/email/password-reset-admin/body', array(&$this, 'disable_default_authentication_messages'), 10, 3);
        add_filter('cuar/authentication-forms/email/forgot-password/body', array(&$this, 'disable_default_authentication_messages'), 10, 3);
        add_filter('cuar/authentication-forms/email/register-admin/body', array(&$this, 'disable_default_authentication_messages'), 10, 2);
        add_filter('cuar/authentication-forms/email/register/body', array(&$this, 'disable_default_authentication_messages'), 10, 2);

        add_action('cuar/authentication-forms/email/forgot-password', array(&$this, 'on_notify_forgot_password'), 10, 2);
        add_action('cuar/authentication-forms/email/register', array(&$this, 'on_notify_register'), 10, 2);
        add_action('cuar/authentication-forms/email/password-reset-admin', array(&$this, 'on_notify_password_reset_admin'), 10, 1);
        add_action('cuar/authentication-forms/email/register-admin', array(&$this, 'on_notify_register_admin'), 10, 1);

        // Comments
        add_filter('notify_moderator', array(&$this, 'maybe_override_comment_moderation_message'), 10, 2);
        add_filter('notify_post_author', array(&$this, 'maybe_override_new_comment_message'), 10, 2);

        // Payments
        add_action('cuar/core/payments/on-status-updated', array(&$this, 'on_notify_payment_status_changed'), 10, 3);

        // Tasks
        add_action('cuar/private-content/tasks/refresh-list-status', array(&$this, 'on_task_list_refreshed'), 200, 3);
        add_action("cuar/private-content/tasks/on-task-overdue", array(&$this, 'on_task_overdue'), 10, 4);
    }

    /*------- PAYMENTS CALLBACKS -------------------------------------------------------------------------------------*/

    /**
     * @param CUAR_Payment $payment
     * @param string       $old_status
     * @param string       $new_status
     */
    public function on_notify_payment_status_changed($payment, $old_status, $new_status)
    {
        if ($old_status == $new_status) return;

        $payer_id = $payment->get_user_id();
        if ($payer_id < 0) return;

        $recipient_ids = array($payer_id);
        $notification_id = '';
        $only_admins = false;

        switch ($new_status) {
            // If status is now complete, send a thank you message to the payer & admins
            case CUAR_PaymentStatus::$STATUS_COMPLETE: {
                $notification_id = 'payment-completed';
                break;
            }

            // If status rejected, let the payer & admins know that
            case CUAR_PaymentStatus::$STATUS_FAILED: {
                $notification_id = 'payment-rejected';
                break;
            }

            // If status pending, let the admins know that
            case CUAR_PaymentStatus::$STATUS_PENDING: {
                $notification_id = 'payment-pending';
                $only_admins = true;
                break;
            }
        }

        if ( !empty($notification_id)) {
            if ( !$only_admins) {
                $this->no_addon->mailer()->send_mass_notification($recipient_ids, $notification_id, $payment->ID, array(
                    'format' => $this->no_addon->settings()->get_email_format(),
                ));

                $payment->add_note('WP Customer Area', sprintf(__('Notification "%1$s" sent to payer (user ID: %2$s)', 'cuarno'),
                    $notification_id, $payer_id));
            }

            // Also send a notification to the admin
            $recipient_ids = $this->get_administrator_user_ids();
            $notification_id .= '-admin';

            $this->no_addon->mailer()->send_mass_notification($recipient_ids, $notification_id, $payment->ID, array(
                'format' => $this->no_addon->settings()->get_email_format(),
            ));

            $payment->add_note('WP Customer Area', sprintf(__('Notification "%1$s" sent to %2$d administrator(s)', 'cuarno'),
                $notification_id, count($recipient_ids)));
        }
    }

    /*------- COMMENTS NOTIFICATIONS CALLBACKS -----------------------------------------------------------------------*/

    /**
     * Disable the default notifications for comments only for private content
     *
     * @param bool $maybe_notify
     * @param int  $comment_ID
     *
     * @return bool
     */
    public function maybe_override_comment_moderation_message($maybe_notify, $comment_ID)
    {
        if ($maybe_notify == false) return $maybe_notify;

        $comment = get_comment($comment_ID);
        $post_type = get_post_type($comment->comment_post_ID);
        $private_types = $this->plugin->get_private_post_types();

        // Let WordPress handle comments on post types not controlled by WP Customer Area
        if ( !in_array($post_type, $private_types)) return $maybe_notify;

        // We have a comment for a private post type, handle it ourselves

        // Send notification to all administrators
        $recipient_ids = $this->get_administrator_user_ids();
        $this->no_addon->mailer()->send_mass_notification($recipient_ids, 'new-comment-moderated', $comment->comment_post_ID, array(
            'email_format' => $this->no_addon->settings()->get_email_format(),
            'comment'      => $comment,
        ));

        // Do not send the default WordPress message
        return false;
    }

    /**
     * Disable the default notifications for comments only for private content
     *
     * @param bool $maybe_notify
     * @param int  $comment_ID
     *
     * @return bool
     */
    public function maybe_override_new_comment_message($maybe_notify, $comment_ID)
    {
        if ($maybe_notify == false) return $maybe_notify;

        $comment = get_comment($comment_ID);
        $post_type = get_post_type($comment->comment_post_ID);
        $private_types = $this->plugin->get_private_post_types();

        // Let WordPress handle comments on post types not controlled by WP Customer Area
        if ( !in_array($post_type, $private_types)) return $maybe_notify;

        // We have a comment for a private post type, handle it ourselves

        // Only send notifications for approved comments.
        if ( !isset($comment->comment_approved) || '1' != $comment->comment_approved) {
            return false;
        }

        // Build the recipient list
        /** @var CUAR_PostOwnerAddOn $po_addon */
        $po_addon = $this->plugin->get_addon('post-owner');
        $recipient_ids = $po_addon->get_post_owner_user_ids($comment->comment_post_ID);

        $post = get_post($comment->comment_post_ID);
        $recipient_ids[] = $post->post_author;

        $this->no_addon->mailer()->send_mass_notification($recipient_ids, 'new-comment', $comment->comment_post_ID, array(
            'email_format' => $this->no_addon->settings()->get_email_format(),
            'comment'      => $comment,
        ));

        // Do not send the default WordPress message
        return false;
    }


    /*------- AUTH NOTIFICATIONS CALLBACKS ---------------------------------------------------------------------------*/

    /**
     * Disable the default messages, we will do the mailing ourself
     *
     * @param string  $message
     * @param WP_User $user
     *
     * @return bool
     */
    public function disable_default_authentication_messages($message, $user, $reset_url = false)
    {
        return false;
    }

    /**
     * @param WP_User $user
     * @param string  $reset_url
     */
    public function on_notify_forgot_password($user, $reset_url)
    {
        $recipient_ids = array($user->ID);
        $this->no_addon->mailer()->send_mass_notification($recipient_ids, 'forgot-password', null, array(
            'format'             => $this->no_addon->settings()->get_email_format(),
            'user'               => $user,
            'reset_password_url' => $reset_url
        ));
    }

    /**
     * @param WP_User $user
     * @param string  $reset_url
     */
    public function on_notify_register($user, $reset_url)
    {
        $recipient_ids = array($user->ID);
        $this->no_addon->mailer()->send_mass_notification($recipient_ids, 'register', null, array(
            'format'             => $this->no_addon->settings()->get_email_format(),
            'user'               => $user,
            'reset_password_url' => $reset_url
        ));
    }

    /**
     * @param WP_User $user
     */
    public function on_notify_password_reset_admin($user)
    {
        $recipient_ids = $this->get_administrator_user_ids();
        $this->no_addon->mailer()->send_mass_notification($recipient_ids, 'password-reset-admin', null, array(
            'email_format' => $this->no_addon->settings()->get_email_format(),
            'user'         => $user,
        ));
    }

    /**
     * @param WP_User $user
     */
    public function on_notify_register_admin($user)
    {
        $recipient_ids = $this->get_administrator_user_ids();
        $this->no_addon->mailer()->send_mass_notification($recipient_ids, 'register-admin', null, array(
            'email_format' => $this->no_addon->settings()->get_email_format(),
            'user'         => $user,
        ));
    }

    /*------- TASKS CALLBACKS ---------------------------------------------------------------------------*/

    public function on_task_overdue($task, $task_list, $due_date, $days_diff)
    {
        $notification_id = $days_diff > 0 ? 'task-overdue-reminder' : 'task-soon-overdue';
        $notif_settings = $this->no_addon->settings()->get_notification_params($notification_id);

        // Build the recipient list for the notification going out to the owners
        /** @var CUAR_PostOwnerAddOn $po_addon */
        $po_addon = $this->plugin->get_addon('post-owner');
        $recipient_ids = array();

        switch ($notif_settings['recipient']) {
            case 'author':
                $recipient_ids = array_merge($recipient_ids, array($task_list->post_author));
                break;

            case 'owner':
                $recipient_ids = array_merge($recipient_ids, $po_addon->get_post_owner_user_ids($task_list->ID));
                break;

            case 'all':
            default:
                $recipient_ids = array_merge($recipient_ids, array($task_list->post_author));
                $recipient_ids = array_merge($recipient_ids, $po_addon->get_post_owner_user_ids($task_list->ID));
                break;
        }

        if (empty($recipient_ids)) return;

        $this->no_addon->mailer()->send_mass_notification($recipient_ids, $notification_id, $task_list->ID, array(
            'email_format' => $this->no_addon->settings()->get_email_format(),
            'task'         => $task,
            'due_date'     => $due_date,
            'days'         => $days_diff,
        ));
    }

    /**
     * @param int $list_id
     * @param int $new_progress
     * @param int $old_progress
     */
    public function on_task_list_refreshed($list_id, $new_progress, $old_progress)
    {
        // Only when task list progress goes from <100 to >=100
        if ( !($old_progress < 100 && $new_progress >= 100)) return;

        $notification_id = 'tasklist-completed';
        $notif_settings = $this->no_addon->settings()->get_notification_params($notification_id);

        // Build the recipient list for the notification going out to the owners
        /** @var CUAR_PostOwnerAddOn $po_addon */
        $po_addon = $this->plugin->get_addon('post-owner');
        $recipient_ids = array();

        $task_list = get_post($list_id);

        switch ($notif_settings['recipient']) {
            case 'author':
                $recipient_ids = array_merge($recipient_ids, array($task_list->post_author));
                break;

            case 'owner':
                $recipient_ids = array_merge($recipient_ids, $po_addon->get_post_owner_user_ids($task_list->ID));
                break;

            case 'all':
            default:
                $recipient_ids = array_merge($recipient_ids, array($task_list->post_author));
                $recipient_ids = array_merge($recipient_ids, $po_addon->get_post_owner_user_ids($task_list->ID));
                break;
        }

        if (empty($recipient_ids)) return;

        $this->no_addon->mailer()->send_mass_notification($recipient_ids, $notification_id, $task_list->ID, array(
            'email_format' => $this->no_addon->settings()->get_email_format(),
        ));
    }

    /*------- OTHER NOTIFICATIONS CALLBACKS --------------------------------------------------------------------------*/

    /**
     * We will send a notification to the author of the file that a user has downloaded it
     *
     * @param int                   $post_id
     * @param int                   $user_id The user who downloaded the file
     * @param CUAR_PrivateFileAddOn $pf_addon
     * @param string                $file_id
     */
    public function on_private_file_downloaded($post_id, $user_id, $pf_addon, $file_id)
    {
        $notification_id = 'private-file-downloaded';
        $notif_settings = $this->no_addon->settings()->get_notification_params($notification_id);

        // If we want a notification only on the first download but it is not, bail
        if ($notif_settings['mode'] == 'first') {
            $count = $pf_addon->get_file_download_count($post_id, $file_id, $user_id);
            if ($count > 1) return;
        }

        // Build the recipient list
        $recipient_ids = array();
        $post = get_post($post_id);

        switch ($notif_settings['recipient']) {
            case 'admin':
                $recipient_ids = array_merge($recipient_ids, $this->get_administrator_user_ids());
                break;

            case 'author':
                $recipient_ids = array_merge($recipient_ids, array($post->post_author));
                break;

            case 'all':
            default:
                $recipient_ids = array_merge($recipient_ids, $this->get_administrator_user_ids());
                $recipient_ids = array_merge($recipient_ids, array($post->post_author));
                break;
        }

        if (empty($recipient_ids)) return;

        $this->no_addon->mailer()->send_mass_notification($recipient_ids, $notification_id, $post_id, array(
            'format'        => $this->no_addon->settings()->get_email_format(),
            'attachment_id' => $file_id
        ));
    }

    /**
     *
     * @param int     $post_id
     * @param WP_Post $post
     * @param array   $previous_owners
     * @param array   $new_owners
     */
    public function on_private_post_published_admin($post_id, $post, $previous_owners, $new_owners)
    {
        // Only if checkbox is checked or if we are forced to send it
        if ( !isset($_POST['cuar_no_send_new_private_post_notification'])) return;

        // Bail if not published
        if (get_post_status($post_id) != 'publish') {
            $this->plugin->add_admin_notice(__('The notification can not be sent if the post is not published', 'cuarno'));

            return;
        }

        $this->on_private_post_published($post_id);
    }

    /**
     *
     * @param int     $post_id
     * @param WP_Post $post
     * @param array   $owners
     * @param array   $errors
     */
    public function on_private_post_published_frontend($post_id, $post, $owners = null, $errors = array())
    {
        // If post is published, we notify as usual. If not, we notify admins that the post is ready for moderation
        if (get_post_status($post_id) == 'publish') {
            $this->on_private_post_published($post_id);
        } else if (get_post_status($post_id) == 'draft') {
            $recipient_ids = $this->get_administrator_user_ids();
            $this->no_addon->mailer()->send_mass_notification($recipient_ids, 'private-content-moderated', $post_id, array(
                'email_format' => $this->no_addon->settings()->get_email_format()
            ));
        }

        $this->plugin->clear_admin_notices();
    }

    /**
     * @param int     $post_id
     * @param WP_Post $post
     */
    public function on_conversation_started_frontend($post_id, $post)
    {
        $this->on_private_post_published_frontend($post_id, $post);
    }

    /**
     * @param int     $post_id
     * @param WP_Post $post
     */
    public function on_project_published($post_id, $post)
    {
        if (is_admin()) {
            // Only if checkbox is checked or if we are forced to send it
            if ( !isset($_POST['cuar_no_send_new_private_post_notification'])) {
                return;
            }

            // Bail if not published
            if (get_post_status($post_id) !== 'publish') {
                $this->plugin->add_admin_notice(__('The notification can not be sent if the post is not published', 'cuarno'));

                return;
            }
        }

        // Build the recipient list for the notification going out to the project team
        $recipient_ids = array();

        $project = new CUAR_Project($post);

        /** @var CUAR_ProjectsAddOn $pj_addon */
        $pj_addon = $this->plugin->get_addon('projects');
        $roles = $pj_addon->settings()->get_project_roles();
        foreach ($roles as $role => $desc) {
            $actors = $project->get_actors($role);
            $recipient_ids = array_merge($recipient_ids, $actors);
        }

        if ( !empty($recipient_ids)) {
            $this->no_addon->mailer()->send_mass_notification($recipient_ids, 'private-content-published', $post_id, array(
                'email_format' => $this->no_addon->settings()->get_email_format()
            ));

            if (is_admin()) {
                $this->plugin->add_admin_notice(sprintf(__('A notification has been sent to %d recipient(s)', 'cuarno'), count($recipient_ids)), 'updated');
            }
        } else {
            if (is_admin()) {
                $this->plugin->add_admin_notice(__('This project does not yet have any members. Nothing has been sent.', 'cuarno'));
            }
        }

        // Build the recipient list for the notification going out to the administrators
        $this->on_private_post_published_for_admins($post_id);
    }

    /**
     * @param $post_id
     */
    private function on_private_post_published($post_id)
    {
        // Build the recipient list for the notification going out to the owners
        /** @var CUAR_PostOwnerAddOn $po_addon */
        $po_addon = $this->plugin->get_addon('post-owner');
        $recipient_ids = $po_addon->get_post_owner_user_ids($post_id);

        if ( !empty($recipient_ids)) {
            $this->no_addon->mailer()->send_mass_notification($recipient_ids, 'private-content-published', $post_id, array(
                'email_format' => $this->no_addon->settings()->get_email_format()
            ));

            if (is_admin()) {
                $this->plugin->add_admin_notice(__('A notification has been sent to the private content owner(s)', 'cuarno'), 'updated');
            }
        } else {
            if (is_admin()) {
                $this->plugin->add_admin_notice(__('This post does not yet have any owners. Nothing has been sent.', 'cuarno'));
            }
        }

        // Build the recipient list for the notification going out to the administrators
        $this->on_private_post_published_for_admins($post_id);
    }

    /**
     * @param int               $reply_id
     * @param CUAR_Conversation $conversation
     */
    public function on_new_reply_to_conversation($reply_id, $conversation)
    {
        // Bail if not published
        if (get_post_status($reply_id) != 'publish') {
            $this->plugin->add_admin_notice(__('The notification can not be sent if the post is not published', 'cuarno'));

            return;
        }

        // Build the recipient list for the notification going out to the owners + the author
        /** @var CUAR_PostOwnerAddOn $po_addon */
        $po_addon = $this->plugin->get_addon('post-owner');
        $recipient_ids = $po_addon->get_post_owner_user_ids($conversation->ID);
        $recipient_ids[] = $conversation->get_post()->post_author;

        $this->no_addon->mailer()->send_mass_notification($recipient_ids, 'private-conversation-new-reply', $conversation->ID, array(
            'format'   => $this->no_addon->settings()->get_email_format(),
            'reply_id' => $reply_id
        ));
    }

    /**
     * @return array
     */
    private function get_administrator_user_ids()
    {
        $users_query = new WP_User_Query(array(
            'role'   => 'administrator',
            'fields' => 'ID'
        ));
        $results = $users_query->get_results();

        return apply_filters('cuar/notifications/administrator-recipient-ids', $results);
    }

    /**
     * @param $post_id
     */
    private function on_private_post_published_for_admins($post_id)
    {
        $recipient_ids = $this->get_administrator_user_ids();
        $this->no_addon->mailer()->send_mass_notification($recipient_ids, 'private-content-published-admin', $post_id, array(
            'email_format' => $this->no_addon->settings()->get_email_format()
        ));
    }

}
