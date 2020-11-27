<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_ConversationEditorHelper
{
    /** @var CUAR_Plugin */
    private $plugin;

    /** @var CUAR_ConversationsAddOn */
    private $co_addon;

    /**
     * Constructor
     *
     * @param $plugin
     * @param $co_addon
     */
    public function __construct($plugin, $co_addon)
    {
        $this->plugin = $plugin;
        $this->co_addon = $co_addon;

        add_action('before_delete_post', array(&$this, 'before_post_deleted'));
    }

    /*------- GENERAL MAINTENANCE FUNCTIONS --------------------------------------------------------------------------*/

    /**
     * Delete the replies when a conversation is deleted
     *
     * @param int $post_id
     */
    public function before_post_deleted($post_id)
    {
        if (get_post_type($post_id) != CUAR_Conversation::$POST_TYPE) return;

        $args = array(
            'post_parent' => $post_id,
            'post_type'   => CUAR_ConversationReply::$POST_TYPE
        );

        $posts = get_posts($args);
        if (is_array($posts) && count($posts) > 0) {
            foreach ($posts as $post) {
                wp_delete_post($post->ID, true);
            }
        }
    }

    public function update_conversation($post_id, $title, $message)
    {
        // Save private content (post first, then owner, then the rest)
        $post_data = apply_filters('cuar/private-content/conversation/update-conversation/args', array(
            'ID'                => $post_id,
            'post_title'        => wp_strip_all_tags($title),
            'post_content'      => $message,
            'post_type'         => CUAR_Conversation::$POST_TYPE,
            'post_modified'     => current_time('mysql'),
            'post_modified_gmt' => current_time('mysql', 1),
        ));

        $post_id = wp_update_post($post_data);
        if (is_wp_error($post_id)) {
            return $post_id;
        }

        $conversation = new CUAR_Conversation($post_id);
        $conversation->mark_as_read_by_user($conversation->post->post_author);

        do_action("cuar/private-content/conversations/on-conversation-updated", $post_id, $conversation->post);

        return $post_id;
    }


    /**
     * @param $author_id
     * @param $title
     * @param $message
     * @param $post_status
     *
     * @return int|WP_Error
     */
    public function add_conversation($author_id, $title, $message, $post_status)
    {
        // Save private content (post first, then owner, then the rest)
        $post_data = apply_filters('cuar/private-content/conversation/create-conversation/args', array(
            'post_title'        => wp_strip_all_tags($title),
            'post_content'      => $message,
            'post_status'       => $post_status,
            'post_type'         => CUAR_Conversation::$POST_TYPE,
            'post_author'       => $author_id,
            'post_modified'     => current_time('mysql'),
            'post_modified_gmt' => current_time('mysql', 1),
            'comment_status'    => 'closed',
            'ping_status'       => 'closed',
        ));

        $post_id = wp_insert_post($post_data);
        if (is_wp_error($post_id)) return $post_id;

        $conversation = new CUAR_Conversation($post_id);
        $conversation->set_started_by($author_id);
        $conversation->set_reply_count(0);
        $conversation->mark_as_read_by_user($author_id);

        return empty($errors) ? $post_id : $errors;
    }

    /**
     * Print list of replies and associated controls
     *
     * @param int $conversation_id
     */
    public function print_replies($conversation_id)
    {
        $this->co_addon->enqueue_scripts();
        $template_suffix = is_admin() ? '-admin' : '-frontend';

        /** @var CUAR_CustomerConversationsAddOn $cc_addon */
        $cc_addon = $this->plugin->get_addon('customer-conversations');

        /** @noinspection PhpUnusedLocalVariableInspection */
        $enable_rich_editor = $cc_addon->is_rich_editor_enabled_for_replies();

        /** @noinspection PhpUnusedLocalVariableInspection */
        $conversation = new CUAR_Conversation($conversation_id);

        /** @noinspection PhpUnusedLocalVariableInspection */
        $item_template = $this->plugin->get_template_file_path(
            CUARME_INCLUDES_DIR . '/conversations',
            array(
                'conversation-editor-replies-list-item' . $template_suffix . '.template.php',
                'conversation-editor-replies-list-item.template.php',
            ),
            'templates');

        /** @noinspection PhpUnusedLocalVariableInspection */
        $form_template = '';
        if ($conversation->is_closed()) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $form_template = $this->plugin->get_template_file_path(
                CUARME_INCLUDES_DIR . '/conversations',
                'conversation-editor-replies-closed.template.php',
                'templates');
        } else if ($this->co_addon->user_can_add_reply($conversation_id)) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $form_template = $this->plugin->get_template_file_path(
                CUARME_INCLUDES_DIR . '/conversations',
                array(
                    'conversation-editor-replies-add-form' . $template_suffix . '.template.php',
                    'conversation-editor-replies-add-form.template.php',
                ),
                'templates');
        }

        include($this->plugin->get_template_file_path(
            CUARME_INCLUDES_DIR . '/conversations',
            array(
                'conversation-editor-replies-list' . $template_suffix . '.template.php',
                'conversation-editor-replies-list.template.php',
            ),
            'templates'));
    }

    /**
     * @param int   $conversation_id
     * @param array $form_data
     *
     * @return int|WP_Error
     */
    public function add_reply($conversation_id, $form_data)
    {
        $reply_content = isset($form_data['reply_content']) ? $form_data['reply_content'] : '';
        $reply_author_id = isset($form_data['reply_author_id']) ? $form_data['reply_author_id'] : -1;

        // Save the reply as a post
        $post_data = apply_filters('cuar/private-content/conversations/reply/saved-post-data', array(
            'post_title'     => '',
            'post_content'   => $reply_content,
            'post_status'    => 'publish',
            'post_type'      => CUAR_ConversationReply::$POST_TYPE,
            'post_parent'    => $conversation_id,
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
            'post_author'    => $reply_author_id
        ));

        $reply_id = wp_insert_post($post_data);
        if (is_wp_error($reply_id)) return $reply_id;

        // We have one more reply
        $conversation = new CUAR_Conversation($conversation_id);
        $conversation->set_reply_count($conversation->get_reply_count() + 1);

        // We have been modified
        $conversation->update_modified_date();

        // Mark conversation as read by the reply author
        $conversation->mark_as_read_by_user($reply_author_id);

        // Let other addons do something on this occasion
        do_action("cuar/private-content/conversations/on-new-reply", $reply_id, $conversation);

        return $reply_id;
    }

    /**
     * @param int $conversation_id
     * @param int $reply_id
     */
    public function delete_reply($conversation_id, $reply_id)
    {
        $reply = new CUAR_ConversationReply($reply_id);
        $result = wp_delete_post($reply_id, true);
        if (false == $result) return;

        // We have one reply less
        $conversation = new CUAR_Conversation($conversation_id);
        $conversation->set_reply_count($conversation->get_reply_count() - 1);

        // Let other addons do something on this occasion
        do_action("cuar/private-content/conversations/on-delete-reply", $reply, $conversation);
    }
}