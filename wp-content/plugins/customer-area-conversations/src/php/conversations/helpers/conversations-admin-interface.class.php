<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/settings.class.php');

if ( !class_exists('CUAR_ConversationsAdminInterface')) :

    /**
     * Administation area for private files
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_ConversationsAdminInterface
    {

        public function __construct($plugin, $me_addon)
        {
            $this->plugin = $plugin;
            $this->me_addon = $me_addon;

            add_filter('cuar/core/permission-groups', array(&$this, 'get_configurable_capability_groups'));
            add_action('add_meta_boxes', array(&$this, 'register_edit_page_meta_boxes'));
            add_filter("manage_edit-cuar_conversation_columns", array(&$this, 'owner_column_register'));
            add_action("manage_cuar_conversation_posts_custom_column", array(&$this, 'owner_column_display'), 10, 2);

            // Add a tab for conversations
            add_filter('cuar/core/settings/settings-tabs', array(&$this, 'add_settings_tab'), 530, 1);
        }

        public function add_settings_tab($tabs)
        {
            $tabs['cuar_conversations'] = __('Conversations', 'cuarme');

            return $tabs;
        }

        /*------- CAPABILITIES ------------------------------------------------------------------------------------------*/

        public function get_configurable_capability_groups($capability_groups)
        {
            $capability_groups[CUAR_Conversation::$POST_TYPE] = array(
                'label'  => __('Conversations', 'cuarme'),
                'groups' => array(
                    'back-office'  => array(
                        'group_name'   => __('Back-office', 'cuarme'),
                        'capabilities' => array(
                            'cuarme_co_list_all' => __('List all conversations', 'cuarme'),
                            'cuarme_co_edit'     => __('Create/Edit conversations', 'cuarme'),
                            'cuarme_co_delete'   => __('Delete conversations', 'cuarme'),
                            'cuarme_co_read'     => __('Access conversations', 'cuarme'),
                        )
                    ),
                    'front-office' => array(
                        'group_name'   => __('Front-office', 'cuarme'),
                        'capabilities' => array(
                            'cuarme_view_any_cuar_conversation'     => __('View any conversation', 'cuarme'),
                            'cuarme_view_conversations'             => __('View conversations', 'cuarme'),
                            'cuarme_reply_to_conversation'          => __('Reply to a conversation', 'cuarme'),
                            'cuarme_delete_any_reply'               => __('Delete any reply', 'cuarme'),
                            'cuarme_delete_replies_in_conversation' => __('Delete any reply in own conversations', 'cuarme'),
                            'cuarme_delete_own_replies'             => __('Delete own replies', 'cuarme'),
                        )
                    )
                )
            );

            return $capability_groups;
        }

        /*------- CUSTOMISATION OF THE EDIT PAGE FOR POST ---------------------------------------------------------------*/

        /**
         * Register some additional boxes on the page to edit the files
         */
        public function register_edit_page_meta_boxes($post_type)
        {
            if ($post_type != CUAR_Conversation::$POST_TYPE) return;

            add_meta_box(
                'cuar_conversation_replies',
                __('Replies', 'cuarme'),
                array(&$this, 'print_conversation_replies_meta_box'),
                CUAR_Conversation::$POST_TYPE,
                'normal', 'low'
            );
        }

        /**
         * Print the metabox to upload a file
         */
        public function print_conversation_replies_meta_box()
        {
            global $post;

            $replies_query = new WP_Query($this->me_addon->get_conversation_replies_query_args($post->ID));

            if ($replies_query->have_posts())
            {
                while ($replies_query->have_posts())
                {
                    $replies_query->the_post();

                    echo '<div class="conversation-reply">';

                    $date = sprintf("<em>%s</em>", get_the_date());
                    $author = sprintf("<em>%s</em>", get_the_author_meta('display_name'));

                    printf('<p class="meta">' . __('%1$s replied on %2$s', 'cuarme') . '</p>', $author, $date);
                    echo '<p class="content">' . get_the_content() . '</p>';

                    echo '</div>';
                }
            }
            else
            {
                echo "<p>" . __('No replies yet', 'cuarme') . '</p>';
            }

            wp_reset_postdata();
        }

        /*------- CUSTOMISATION OF THE LISTING OF POSTS -----------------------------------------------------------------*/

        /**
         * Register the owner column
         */
        public function owner_column_register($columns)
        {
            $columns['cuar_reply_count'] = __('Replies', 'cuarme');

            return $columns;
        }

        /**
         * Display the column content
         */
        public function owner_column_display($column_name, $post_id)
        {
            if ('cuar_reply_count' != $column_name)
            {
                return;
            }

            echo get_post_meta($post_id, CUAR_Conversation::$META_REPLY_COUNT, true);
        }

        /** @var CUAR_Plugin */
        private $plugin;

        /** @var CUAR_ConversationsAddOn */
        private $me_addon;
    }

endif; // if (!class_exists('CUAR_ConversationsAdminInterface')) :