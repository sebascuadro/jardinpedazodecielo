<?php
/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/Content/custom-post.class.php');

class CUAR_Conversation extends CUAR_CustomPost
{
    public static $POST_TYPE = 'cuar_conversation';

    public static $META_STARTED_BY = 'cuar_started_by';
    public static $META_IS_CLOSED = 'cuar_is_closed';
    public static $META_REPLY_COUNT = 'cuar_reply_count';
    public static $META_USER_READ_LOG = 'cuar_conversation_read_log';

    /**
     * Constructor
     *
     * @param WP_Post|int $custom_post
     * @param boolean     $load_post If we supply an int as the first argument, shall we load the post object?
     */
    public function __construct($custom_post, $load_post = true)
    {
        parent::__construct($custom_post, $load_post);
    }

    /**
     * Get the number of replies to this conversation
     * @return int
     */
    public function get_reply_count()
    {
        $count = get_post_meta($this->ID, self::$META_REPLY_COUNT, true);
        if ( !isset($count) || empty($count)) $count = 0;

        return $count;
    }

    /**
     * Set the user ID who started the conversation
     *
     * @param int $user_id
     *
     * @return int
     */
    public function set_started_by($user_id)
    {
        update_post_meta($this->ID, self::$META_STARTED_BY, $user_id);
    }

    /**
     * Get the user ID who started the conversation
     *
     * @return int
     */
    public function get_started_by()
    {
        $user_id = get_post_meta($this->ID, self::$META_STARTED_BY, true);
        if ( !isset($user_id)) {
            $user_id = $this->post->post_author;
            $this->set_started_by($user_id);
        }

        return $user_id;
    }

    /** Is the conversation closed to new replies? */
    public function is_closed()
    {
        $is_closed = get_post_meta($this->ID, self::$META_IS_CLOSED, true);
        $is_closed = ($is_closed == 1);

        return apply_filters('cuar/private-content/conversations/replies', $is_closed, $this);
    }

    /** Set whether the conversation is closed to new replies */
    public function set_closed($is_closed = true)
    {
        update_post_meta($this->ID, self::$META_IS_CLOSED, $is_closed ? 1 : 0);
    }

    /**
     * Get the number of replies to this conversation
     *
     * @param int $count
     *
     * @return int
     */
    public function set_reply_count($count)
    {
        update_post_meta($this->ID, self::$META_REPLY_COUNT, $count);
    }

    public function update_modified_date()
    {
        $post_data = array(
            'ID'                => $this->ID,
            'post_modified'     => current_time('mysql'),
            'post_modified_gmt' => current_time('mysql', 1),
        );
        wp_update_post($post_data);
    }

    /**
     * Get the replies to this conversation
     *
     * @return array[CUAR_ConversationReply]
     */
    public function get_replies()
    {
        $replies = get_posts($this->get_replies_query_args());
        foreach ($replies as $i => $r) {
            $replies[$i] = new CUAR_ConversationReply($r);
        }

        return apply_filters('cuar/private-content/conversations/replies', $replies, $this);
    }

    /**
     * Tell if the given user has seen all replies since his last visit
     *
     * @param int $user_id The user ID who needs to check update status for this conversation
     *
     * @return bool
     */
    public function mark_as_read_by_user($user_id)
    {
        $last_read = get_user_meta($user_id, self::$META_USER_READ_LOG, true);
        $last_read[$this->ID] = current_time('mysql', 1);
        update_user_meta($user_id, self::$META_USER_READ_LOG, $last_read);
    }

    /**
     * Tell if the given user has seen all replies since his last visit
     *
     * @param int $user_id The user ID who needs to check update status for this conversation
     *
     * @return bool
     */
    public function has_new_replies($user_id)
    {
        $last_read = get_user_meta($user_id, self::$META_USER_READ_LOG, true);
        if (isset($last_read) && isset($last_read[$this->ID]) && ($last_read[$this->ID] >= $this->post->post_modified_gmt)) {
            return false;
        }

        return true;
    }

    /**
     * The arguments to query this conversation's replies
     *
     * @return array
     */
    private function get_replies_query_args()
    {
        return apply_filters('cuar/private-content/conversations/replies/query-args', array(
            'post_type'      => CUAR_ConversationReply::$POST_TYPE,
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'ASC',
            'post_parent'    => $this->ID
        ), $this);
    }

    /**
     * Register the custom post type
     */
    public static function register_post_type()
    {
        $labels = array(
            'name'               => _x('Conversations', 'cuar_conversation', 'cuarme'),
            'singular_name'      => _x('Conversation', 'cuar_conversation', 'cuarme'),
            'add_new'            => _x('Add New', 'cuar_conversation', 'cuarme'),
            'add_new_item'       => _x('Add New Conversation', 'cuar_conversation', 'cuarme'),
            'edit_item'          => _x('Edit Conversation', 'cuar_conversation', 'cuarme'),
            'new_item'           => _x('New Conversation', 'cuar_conversation', 'cuarme'),
            'view_item'          => _x('View Conversation', 'cuar_conversation', 'cuarme'),
            'search_items'       => _x('Search Conversations', 'cuar_conversation', 'cuarme'),
            'not_found'          => _x('No conversation found', 'cuar_conversation', 'cuarme'),
            'not_found_in_trash' => _x('No conversation found in Trash', 'cuar_conversation', 'cuarme'),
            'parent_item_colon'  => _x('Parent Conversation:', 'cuar_conversation', 'cuarme'),
            'menu_name'          => _x('Conversations', 'cuar_conversation', 'cuarme'),
        );

        $args = array(
            'labels'              => $labels,
            'hierarchical'        => false,
            'supports'            => array('title', 'editor', 'author', 'thumbnail', 'excerpt'),
            'taxonomies'          => array(),
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'show_in_nav_menus'   => false,
            'publicly_queryable'  => true,
            'exclude_from_search' => true,
            'has_archive'         => false,
            'query_var'           => self::$POST_TYPE,
            'can_export'          => false,
            'rewrite'             => false,
            'capabilities'        => array(
                'edit_post'          => 'cuarme_co_edit',
                'edit_posts'         => 'cuarme_co_edit',
                'edit_others_posts'  => 'cuarme_co_edit',
                'publish_posts'      => 'cuarme_co_edit',
                'read_post'          => 'cuarme_co_read',
                'read_private_posts' => 'cuarme_co_list_all',
                'delete_post'        => 'cuarme_co_delete',
                'delete_posts'       => 'cuarme_co_delete'
            )
        );

        register_post_type(self::$POST_TYPE, apply_filters('cuar/private-content/conversations/register-post-type-args', $args));
    }

}