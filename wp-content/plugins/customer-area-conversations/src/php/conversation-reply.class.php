<?php
/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/Content/custom-post.class.php');

class CUAR_ConversationReply extends CUAR_CustomPost
{
    public static $POST_TYPE = 'cuar_conv_reply';

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
     * Register the custom post type
     */
    public static function register_post_type()
    {
        $labels = array(
            'name'               => _x('Conversation Replies', 'cuar_conversation', 'cuarme'),
            'singular_name'      => _x('Conversation Reply', 'cuar_conversation', 'cuarme'),
            'add_new'            => _x('Add New', 'cuar_conversation', 'cuarme'),
            'add_new_item'       => _x('Add New Reply', 'cuar_conversation', 'cuarme'),
            'edit_item'          => _x('Edit Reply', 'cuar_conversation', 'cuarme'),
            'new_item'           => _x('New Reply', 'cuar_conversation', 'cuarme'),
            'view_item'          => _x('View Reply', 'cuar_conversation', 'cuarme'),
            'search_items'       => _x('Search Conversation Replies', 'cuar_conversation', 'cuarme'),
            'not_found'          => _x('No reply found', 'cuar_conversation', 'cuarme'),
            'not_found_in_trash' => _x('No reply found in Trash', 'cuar_conversation', 'cuarme'),
            'parent_item_colon'  => _x('Parent Conversation:', 'cuar_conversation', 'cuarme'),
            'menu_name'          => _x('Conversations', 'cuar_conversation', 'cuarme'),
        );

        $args = array(
            'labels'              => $labels,
            'hierarchical'        => false,
            'supports'            => array('editor', 'author'),
            'taxonomies'          => array(),
            'public'              => false,
            'show_ui'             => false,
            'show_in_menu'        => false,
            'show_in_nav_menus'   => false,
            'publicly_queryable'  => false,
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

        register_post_type(self::$POST_TYPE, apply_filters('cuar/private-content/conversations/replies/register-post-type-args', $args));
    }

}