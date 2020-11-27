<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_ConversationsLogger
{
    public static $TYPE_REPLY_ADDED = 'cuar-conversation-reply-added';
    public static $TYPE_REPLY_DELETED = 'cuar-conversation-reply-deleted';

    public static $META_REPLY_CONTENT = 'cuar_me_reply_content';

    /** @var CUAR_Plugin */
    private $plugin;

    /** @var CUAR_ConversationsAddOn */
    private $me_addon;

    /** @var CUAR_Logger */
    private $logger;

    /**
     * Constructor
     *
     * @param CUAR_Plugin             $plugin
     * @param CUAR_ConversationsAddOn $me_addon
     */
    public function __construct($plugin, $me_addon)
    {
        $this->plugin = $plugin;
        $this->me_addon = $me_addon;
        $this->logger = $plugin->get_logger();

        add_filter('cuar/core/log/event-types', array(&$this, 'add_default_event_types'));

        if (is_admin())
        {
            add_filter('cuar/core/log/table-displayable-meta', array(&$this, 'get_table_displayable_meta'), 10, 1);
            add_filter('cuar/core/log/table-meta-pill-descriptor', array(&$this, 'get_table_meta_pill'), 10, 3);
        }

        add_action('cuar/private-content/conversations/on-new-reply', array(&$this, 'on_reply_added'), 10, 2);
        add_action('cuar/private-content/conversations/on-delete-reply', array(&$this, 'on_reply_deleted'), 10, 2);
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
            self::$TYPE_REPLY_ADDED   => __('Reply added', 'cuarme'),
            self::$TYPE_REPLY_DELETED => __('Reply deleted', 'cuarme'),
        ));
    }

    /*------- LOG VIEWER -----------------------------------------------------------------------------------------*/

    public function get_table_displayable_meta($meta)
    {
        return array_merge($meta, array(
            self::$META_REPLY_CONTENT,
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
        if ($meta == self::$META_REPLY_CONTENT)
        {
            $val = empty($item->$meta) ? '-' : $item->$meta;
            $title_val = $val;
            $value_val = substr(wp_strip_all_tags($val, true), 0, 35);

            $pill['title'] = esc_attr(__('Content: ', 'cuarme') . $title_val);
            $pill['value'] = __('Content: ', 'cuarme') . $value_val;
        }

        return $pill;
    }

    /*------- LOGGING --------------------------------------------------------------------------------------------*/

    /**
     * @param int               $reply_id
     * @param CUAR_Conversation $conversation
     */
    public function on_reply_added($reply_id, $conversation)
    {
        $reply = new CUAR_ConversationReply($reply_id);
        $this->log_reply_changed(
            self::$TYPE_REPLY_ADDED,
            $conversation,
            $reply->post->post_content);
    }

    /**
     * @param CUAR_Conversation      $conversation
     * @param CUAR_ConversationReply $reply
     */
    public function on_reply_deleted($reply, $conversation)
    {
        $this->log_reply_changed(
            self::$TYPE_REPLY_DELETED,
            $conversation,
            $reply->post->post_content);
    }

    /**
     * Log a property changed event
     *
     * @param string                $event
     * @param int|CUAR_Conversation $conversation
     * @param mixed                 $content
     */
    private function log_reply_changed($event, $conversation, $content)
    {
        /** @var CUAR_LogAddOn $log_addon */
        $log_addon = $this->plugin->get_addon('log');

        $default_meta = $log_addon->get_default_event_meta();
        $new_meta = array(
            self::$META_REPLY_CONTENT => substr($content, 0, 200)
        );

        if (is_a($conversation, 'CUAR_Conversation'))
        {
            $conversation = $conversation->ID;
        }

        $should_log_event = true;
        $should_log_event = apply_filters('cuar/core/log/should-log-event?event=' . $event, $should_log_event, $conversation);
        if ($should_log_event)
        {
            $this->logger->log_event(
                $event,
                $conversation,
                CUAR_Conversation::$POST_TYPE,
                $default_meta + $new_meta);
        }
    }

}