<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_NotificationsPlaceholderHelper
{
    public static $GLOBAL_PLACEHOLDERS = array(
        'site_name'    => '',
        'site_url'     => '',
        'from_name'    => '',
        'from_address' => '',
    );

    public static $RECIPIENT_PLACEHOLDERS = array(
        'to_name'    => '',
        'to_address' => '',
    );

    public static $CURRENT_USER_PLACEHOLDERS = array(
        'logged_user_name'    => '',
        'logged_user_address' => '',
    );

    public static $POST_OWNERSHIP_PLACEHOLDERS = array(
        'owner_name' => '',
    );

    public static $GENERAL_POST_PLACEHOLDERS = array(
        'post_title'     => '',
        'post_url'       => '',
        'post_content'   => '',
        'post_type'      => '',
        'author_name'    => '',
        'author_address' => '',
        'review_url'     => '',
    );

    public static $PRIVATE_FILE_PLACEHOLDERS = array(
        'attachment_list' => '',
        'attachment_name' => '',
    );

    public static $CONVERSATION_PLACEHOLDERS = array(
        'reply_author_name'    => '',
        'reply_author_address' => '',
    );

    public static $INVOICE_PLACEHOLDERS = array(
        'invoice_total'    => '',
        'invoice_number'   => '',
        'invoice_due_date' => '',
    );

    public static $AUTH_PLACEHOLDERS = array(
        'user_login'         => '',
        'user_email'         => '',
        'reset_password_url' => '',
    );

    public static $PAYMENT_PLACEHOLDERS = array(
        'payment_amount'   => '',
        'payment_edit_url' => '',
    );

    public static $COMMENT_PLACEHOLDERS = array(
        'commenter_name'           => '',
        'commenter_email'          => '',
        'commenter_url'            => '',
        'commenter_ip'             => '',
        'comment_content'          => '',
        'comment_moderation_links' => '',
    );

    public static $TASK_PLACEHOLDERS = array(
        'task_due_date'    => '',
        'task_description' => '',
    );

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

        add_filter('cuar/notifications/content', array(&$this, 'replace_placeholders_filter'), 10, 5);
        add_filter('cuar/notifications/heading', array(&$this, 'replace_placeholders_filter'), 10, 5);
        add_filter('cuar/notifications/subject', array(&$this, 'replace_placeholders_filter'), 10, 5);
    }

    /**
     * @param string $text
     * @param int    $recipient_id
     * @param int    $notification_id
     * @param null   $post_id
     * @param array  $extra
     *
     * @return mixed
     */
    public function replace_placeholders_filter($text, $recipient_id, $notification_id, $post_id = null, $extra = array())
    {
        // Start with placeholders we may find anywhere
        $text = $this->replace_placeholders($text, $this->get_global_placeholders());

        // Then placeholders relative to the recipient, current user
        $text = $this->replace_placeholders($text, $this->get_general_recipient_placeholders($recipient_id));
        $text = $this->replace_placeholders($text, $this->get_current_user_placeholders());

        // Then placeholders relative to the post
        if ($post_id != null) {
            $post = get_post($post_id);

            $text = $this->replace_placeholders($text, $this->get_general_post_placeholders($post));
            $text = $this->replace_placeholders($text, $this->get_post_ownership_placeholders($post));

            // Some specific stuff for a few add-ons
            $text = $this->replace_placeholders($text, $this->get_specific_conversation_placeholders($post, $extra));
            $text = $this->replace_placeholders($text, $this->get_specific_private_file_placeholders($post, $extra));
            $text = $this->replace_placeholders($text, $this->get_specific_invoice_placeholders($post, $extra));
            $text = $this->replace_placeholders($text, $this->get_specific_payment_placeholders($post, $extra));
            $text = $this->replace_placeholders($text, $this->get_specific_tasks_placeholders($post, $extra));
            $text = $this->replace_placeholders($text, $this->get_specific_comment_placeholders($extra));
        }

        // Finally some more specific placeholders which may be needed
        $text = $this->replace_placeholders($text, $this->get_specific_authentication_forms_placeholders($extra));

        return $text;
    }

    /**
     * Replace the placeholders inside a string
     *
     * @param string $input        The string to manipulate
     * @param array  $placeholders Array where key is the placeholder name and value is the replacement string
     *
     * @return mixed
     */
    private function replace_placeholders($input, $placeholders)
    {
        if (empty($placeholders)) return $input;

        $out = $input;
        foreach ($placeholders as $placeholder => $replacement) {
            // Deprecated placeholder syntax
            $out = str_replace("%{$placeholder}%", $replacement, $out);

            // New placeholder syntax
            $out = str_replace('{{' . $placeholder . '}}', $replacement, $out);
        }

        return $out;
    }

    /**
     * Placeholders we will always be able to find
     *
     * @return array
     */
    private function get_global_placeholders()
    {
        $placeholders = self::$GLOBAL_PLACEHOLDERS;

        $placeholders['site_name'] = get_bloginfo('name');
        $placeholders['site_url'] = get_bloginfo('url');
        $placeholders['from_name'] = $this->no_addon->settings()->get_from_name();
        $placeholders['from_address'] = $this->no_addon->settings()->get_from_address();

        return $placeholders;
    }

    /**
     * Placeholders valid for the user logged_in at the time of sending the email
     *
     * @return array
     */
    private function get_current_user_placeholders()
    {
        $placeholders = self::$CURRENT_USER_PLACEHOLDERS;
        $user_id = get_current_user_id();

        if ($user_id == null) return $placeholders;

        $user = get_userdata($user_id);
        $placeholders['logged_user_name'] = $user->display_name;
        $placeholders['logged_user_address'] = $user->user_email;

        return $placeholders;
    }

    /**
     * Placeholders valid for the user receiving the email
     *
     * @param $user_id
     *
     * @return array
     */
    private function get_general_recipient_placeholders($user_id)
    {
        $placeholders = self::$RECIPIENT_PLACEHOLDERS;

        if ($user_id == null) return $placeholders;

        $user = get_userdata($user_id);
        $placeholders['to_name'] = $user->display_name;
        $placeholders['to_address'] = $user->user_email;

        return $placeholders;
    }

    /**
     * Placeholders valid for all posts
     *
     * @param WP_Post $post
     *
     * @return array
     */
    private function get_general_post_placeholders($post)
    {
        $placeholders = self::$GENERAL_POST_PLACEHOLDERS;

        if ($post == null) return $placeholders;

        $placeholders['post_title'] = get_the_title($post);
        $placeholders['post_url'] = get_permalink($post);
        $placeholders['post_content'] = $post->post_content;
        $placeholders['review_url'] = admin_url('post.php?action=edit&post=' . $post->ID);

        $author = get_userdata($post->post_author);
        $placeholders['author_name'] = $author->display_name;
        $placeholders['author_address'] = $author->user_email;

        $post_type = get_post_type_object($post->post_type);
        $placeholders['post_type'] = $post_type->labels->singular_name;

        return $placeholders;
    }

    /**
     * Placeholders valid for all posts
     *
     * @param WP_Post $post
     *
     * @return array
     */
    private function get_post_ownership_placeholders($post)
    {
        $placeholders = self::$POST_OWNERSHIP_PLACEHOLDERS;

        if ($post == null) return $placeholders;

        /** @var CUAR_PostOwnerAddOn $po_addon */
        $po_addon = $this->plugin->get_addon('post-owner');
        $owner_names = $po_addon->get_post_displayable_owners($post->ID, false);

        $placeholders['owner_name'] = implode(', ', $owner_names);

        return $placeholders;
    }

    /**
     * Placeholders valid for private files
     *
     * @param WP_Post $post
     * @param array   $extra
     *
     * @return array
     */
    private function get_specific_private_file_placeholders($post, $extra)
    {
        $placeholders = self::$PRIVATE_FILE_PLACEHOLDERS;

        if ($post == null || $post->post_type != 'cuar_private_file') return $placeholders;

        /** @var CUAR_PrivateFileAddOn $pf_addon */
        $pf_addon = $this->plugin->get_addon('private-files');
        $is_html_output = isset($extra['email_format']) ? $extra['email_format'] == 'html' : false;

        // We have information about a specific attachment
        if (isset($extra['attachment_id'])) {
            $attachment = $pf_addon->get_attached_file($post->ID, $extra['attachment_id']);
            $caption = !empty($attachment['caption']) ? $attachment['caption'] : $attachment['file'];

            $placeholders['attachment_name'] = $caption;
        }

        // We want to output the list of all attachments
        $out = "";
        $attachments = $pf_addon->get_attached_files($post->ID);
        foreach ($attachments as $attachment) {
            $caption = !empty($attachment['caption']) ? $attachment['caption'] : $attachment['file'];
            $url = $pf_addon->get_file_permalink($post->ID, $attachment['id'], 'download', $attachment);

            if ($is_html_output) {
                $out .= sprintf('<li><a href="%1$s">%2$s</a></li>', $url, $caption);
            } else {
                $out .= '    * ' . $caption . ' | ' . $url;
            }

            $out .= "\n";
        }

        if ($is_html_output) {
            $out = "\n<ul>\n" . $out . "</ul>\n";
        }

        $placeholders['attachment_list'] = $out;

        return $placeholders;
    }

    /**
     * Placeholders valid for invoices
     *
     * @param WP_Post $post
     * @param array   $extra
     *
     * @return array
     */
    private function get_specific_invoice_placeholders($post, $extra)
    {
        $placeholders = self::$INVOICE_PLACEHOLDERS;

        if ( !class_exists('CUAR_Invoice')) return $placeholders;

        if ($post == null || $post->post_type != 'cuar_invoice') return $placeholders;

        $invoice = new CUAR_Invoice($post);
        $due_date = $invoice->get_due_date();

        $placeholders['invoice_number'] = $invoice->get_number();
        $placeholders['invoice_total'] = CUAR_CurrencyHelper::formatAmount($invoice->get_total(), $invoice->get_currency(), null);
        if ( !empty($due_date)) {
            $date_format = get_option('date_format');
            $placeholders['invoice_due_date'] = date_i18n($date_format, strtotime($due_date));
        }

        return $placeholders;
    }

    /**
     * Placeholders valid for payments
     *
     * @param WP_Post $post
     * @param array   $extra
     *
     * @return array
     */
    private function get_specific_payment_placeholders($post, $extra)
    {
        $placeholders = self::$PAYMENT_PLACEHOLDERS;

        if ( !class_exists('CUAR_Payment')) return $placeholders;

        /** @noinspection PhpUndefinedClassInspection */
        if ($post == null || $post->post_type != CUAR_Payment::$POST_TYPE) return $placeholders;

        /** @noinspection PhpUndefinedClassInspection */
        $payment = new CUAR_Payment($post);

        $amount = CUAR_CurrencyHelper::formatAmount($payment->get_amount(), $payment->get_currency());
        $payment_edit_url = admin_url('post.php?post=' . $payment->ID . '&action=edit');


        $placeholders['payment_amount'] = $amount;
        $placeholders['payment_edit_url'] = $payment_edit_url;

        return $placeholders;
    }

    /**
     * Placeholders valid for tasks
     *
     * @param WP_Post $post
     * @param array   $extra
     *
     * @return array
     */
    private function get_specific_tasks_placeholders($post, $extra)
    {
        $placeholders = self::$TASK_PLACEHOLDERS;

        if ( !empty($extra['due_date'])) {
            $date_format = get_option('date_format');
            $placeholders['task_due_date'] = date_i18n($date_format, strtotime($extra['due_date']));
        }

        if ( !empty($extra['task'])) {
            $placeholders['task_description'] = $extra['task']->post_content;
        }

        return $placeholders;
    }

    /**
     * Placeholders valid for conversations
     *
     * @param WP_Post $post
     * @param array   $extra
     *
     * @return array
     */
    private function get_specific_conversation_placeholders($post, $extra)
    {
        $placeholders = self::$CONVERSATION_PLACEHOLDERS;

        if ( !class_exists('CUAR_Conversation')) return $placeholders;

        if ($post == null || $post->post_type != CUAR_Conversation::$POST_TYPE) return $placeholders;

        // We have information about a specific attachment
        if (isset($extra['reply_id'])) {
            $reply = get_post($extra['reply_id']);
            $reply_author = get_userdata($reply->post_author);

            $placeholders['reply_author_name'] = $reply_author->display_name;
            $placeholders['reply_author_address'] = $reply_author->user_email;

            $placeholders['reply_content'] = $reply->post_content;
        }

        return $placeholders;
    }

    /**
     * Placeholders valid for conversations
     *
     * @param array $extra
     *
     * @return array
     */
    private function get_specific_comment_placeholders($extra)
    {
        $placeholders = self::$COMMENT_PLACEHOLDERS;

        if (isset($extra['comment'])) {
            /** @var WP_Comment $comment */
            $comment = $extra['comment'];
            $comment_id = $comment->comment_ID;

            $placeholders['comment_content'] = wp_specialchars_decode($comment->comment_content);
            $placeholders['commenter_name'] = $comment->comment_author;
            $placeholders['commenter_email'] = $comment->comment_author_email;
            $placeholders['commenter_url'] = $comment->comment_author_url;
            $placeholders['commenter_ip'] = $comment->comment_author_IP;

            $tmp_links = array(
                'approve' => __('Approve it', 'cuarno'),
                'spam'    => __('Spam it', 'cuarno'),
            );
            if (EMPTY_TRASH_DAYS) {
                $tmp_links['trash'] = __('Trash it', 'cuarno');
            } else $tmp_links['delete'] = __('Delete it', 'cuarno');

            $moderation_links = array();
            foreach ($tmp_links as $action => $label) {
                $moderation_links[] = sprintf(
                    ($extra['email_format'] == 'html') ? '<a href="%1$s">%2$s</a>' : '  - %2$s: %1$s',
                    admin_url("comment.php?action=" . $action . "&c=" . $comment_id),
                    $label);
            }

            if ($extra['email_format'] == 'html') {
                $placeholders['comment_moderation_links'] = '<p style="text-align: center;">' . implode(" | ", $moderation_links) . '</p>';
            } else {
                $placeholders['comment_moderation_links'] = implode("\n", $moderation_links);
            }
        }

        return $placeholders;
    }

    /**
     * Placeholders valid for conversations
     *
     * @param array $extra
     *
     * @return array
     */
    private function get_specific_authentication_forms_placeholders($extra)
    {
        $placeholders = self::$AUTH_PLACEHOLDERS;

        if (isset($extra['user'])) {
            /** @var WP_User $user */
            $user = $extra['user'];

            $placeholders['user_login'] = $user->user_login;
            $placeholders['user_email'] = $user->user_email;
        }

        if (isset($extra['reset_password_url'])) {
            $placeholders['reset_password_url'] = $extra['reset_password_url'];
        }

        return $placeholders;
    }

    public function get_available_placeholders()
    {
        $placeholders = array_merge(
            self::$GLOBAL_PLACEHOLDERS,
            self::$RECIPIENT_PLACEHOLDERS,
            self::$CURRENT_USER_PLACEHOLDERS,
            self::$POST_OWNERSHIP_PLACEHOLDERS,
            self::$GENERAL_POST_PLACEHOLDERS,
            self::$PRIVATE_FILE_PLACEHOLDERS,
            self::$CONVERSATION_PLACEHOLDERS,
            self::$INVOICE_PLACEHOLDERS,
            self::$AUTH_PLACEHOLDERS,
            self::$COMMENT_PLACEHOLDERS,
            self::$PAYMENT_PLACEHOLDERS,
            self::$TASK_PLACEHOLDERS
        );

        $placeholders['site_name'] = __('Your website name', 'cuarno');
        $placeholders['site_url'] = __('Your website home URL', 'cuarno');

        $placeholders['from_name'] = __('The from name set in the notifications settings', 'cuarno');
        $placeholders['from_address'] = __('The from address set in the notifications settings', 'cuarno');

        $placeholders['to_name'] = __('The notification recipient\'s display name', 'cuarno');
        $placeholders['to_address'] = __('The notification recipient\'s email address', 'cuarno');

        $placeholders['logged_user_name'] = __('The name of the user logged in at the time of the notification (the one who triggered the notification)',
            'cuarno');
        $placeholders['logged_user_address'] = __('The email address of the user logged in at the time of the notification', 'cuarno');

        $placeholders['post_title'] = __('The title of the private content', 'cuarno');
        $placeholders['post_url'] = __('The direct link to the private content', 'cuarno');
        $placeholders['post_content'] = __('The actual content (body) of the private content', 'cuarno');
        $placeholders['post_type'] = __('The type of private content (private file, private page, project, etc.)', 'cuarno');
        $placeholders['author_name'] = __('The name of the author of the private content', 'cuarno');
        $placeholders['author_address'] = __('The email address of the author of the private content', 'cuarno');
        $placeholders['owner_name'] = __('The name of the owner of the private content (user(s), role(s), group(s), etc.)', 'cuarno');
        $placeholders['review_url'] = __('The direct link to review a private content submission held for moderation', 'cuarno');

        $placeholders['attachment_list'] = __('The list of attachments to a private file', 'cuarno');
        $placeholders['attachment_name'] = __('The file name of the attachment related to the action which triggered the notification (for instance, the attachment which was downloaded)',
            'cuarno');

        $placeholders['reply_author_name'] = __('The name of the user who created the reply to the conversation', 'cuarno');
        $placeholders['reply_author_address'] = __('The email address of the user who created the reply to the conversation', 'cuarno');
        $placeholders['reply_content'] = __('The content of the reply to the conversation', 'cuarno');

        $placeholders['invoice_number'] = __('The number of the invoice', 'cuarno');
        $placeholders['invoice_total'] = __('The total amount for the invoice, including the currency', 'cuarno');
        $placeholders['invoice_due_date'] = __('The invoice due date', 'cuarno');

        $placeholders['user_login'] = __('The login of the person who submitted the authentication form (registration, password recovery, reset password)',
            'cuarno');
        $placeholders['user_email'] = __('The address of the person who submitted the authentication form (registration, password recovery, reset password)',
            'cuarno');
        $placeholders['reset_password_url'] = __('A link to the page where the user can set or reset his password', 'cuarno');

        $placeholders['comment_content'] = __('The comment content for notifications about comments on posts', 'cuarno');
        $placeholders['commenter_name'] = __('The name of the person who left a comment', 'cuarno');
        $placeholders['commenter_email'] = __('The address of the person who left a comment', 'cuarno');
        $placeholders['commenter_url'] = __('The URL of the person who left a comment', 'cuarno');
        $placeholders['commenter_ip'] = __('The IP address of the person who left a comment', 'cuarno');
        $placeholders['comment_moderation_links'] = __('The links for the comment moderators to approve a comment, delete it or flag it as spam', 'cuarno');

        $placeholders['payment_amount'] = __('The amount that has been paid (including currency)', 'cuarno');
        $placeholders['payment_edit_url'] = __('The URL to the payment edit page', 'cuarno');

        $placeholders['task_due_date'] = __('The task due date', 'cuarno');
        $placeholders['task_description'] = __('The task description', 'cuarno');

        ksort($placeholders);

        return $placeholders;
    }
}

