<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon-content-page.class.php');

require_once(dirname(__FILE__) . '/widget-conversation-dates.class.php');
require_once(dirname(__FILE__) . '/widget-conversation-authors.class.php');

if ( !class_exists('CUAR_CustomerConversationsAddOn')) :

    /**
     * Add-on to put conversations in the customer area
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_CustomerConversationsAddOn extends CUAR_AbstractContentPageAddOn
    {

        public function __construct()
        {
            parent::__construct('customer-conversations');

            $this->set_page_parameters(220, array(
                    'slug'                => 'customer-conversations',
                    'parent_slug'         => 'customer-conversations-home',
                    'friendly_post_type'  => CUAR_Conversation::$POST_TYPE,
                    'required_capability' => 'cuarme_view_conversations'
                )
            );

            $this->set_page_shortcode('customer-area-conversations');
        }

        public function get_label()
        {
            return __('Conversations - Owned', 'cuarme');
        }

        public function get_title()
        {
            return __('My conversations', 'cuarme');
        }

        public function get_hint()
        {
            return __('Page to list the conversations a customer owns.', 'cuarme');
        }

        public function run_addon($plugin)
        {
            parent::run_addon($plugin);

            // This page can also list archive for private content
            $this->enable_content_archives_permalinks();
            $this->enable_single_private_content_permalinks();

            // Widget area for our sidebar
            $this->enable_sidebar(array('CUAR_ConversationDatesWidget', 'CUAR_ConversationAuthorsWidget'), true);

            if (is_admin()) {
                $this->enable_settings('cuar_conversations');
            } else {
                add_action('template_redirect', array(&$this, 'intercept_single_conversation_shown'));
                add_filter('cuar/core/page/query-args?slug=' . $this->get_slug(), array(&$this, 'change_query_parameters'));
                add_filter('cuar/core/dashboard/block-query-args?slug=' . $this->get_slug(), array(&$this, 'change_query_parameters'));

                add_action('cuar/private-content/view/single-post-action-links?post-type=' . CUAR_Conversation::$POST_TYPE,
                    array(&$this, 'get_single_content_action_links'));
            }
        }

        public function print_default_widgets()
        {
            $w = new CUAR_ConversationDatesWidget();
            $w->widget($this->get_default_widget_args($w->id_base), array(
                'title' => __('Archives', 'cuarme'),
            ));

            $w = new CUAR_ConversationAuthorsWidget();
            $w->widget($this->get_default_widget_args($w->id_base), array(
                'title' => __('Started By', 'cuarme'),
            ));
        }

        public function get_page_addon_path()
        {
            return CUARME_INCLUDES_DIR . '/customer-conversations';
        }

        protected function get_author_archive_page_subtitle($author_id)
        {
            if ($author_id == get_current_user_id()) {
                return __('Conversations you started', 'cuarme');
            }

            $author = get_userdata($author_id);

            return sprintf(__('Conversations started by %1$s', 'cuarme'), $author->display_name);
        }

        protected function get_category_archive_page_subtitle($category)
        {
            return sprintf(__('Conversations under %1$s', 'cuarme'), $category->name);
        }

        protected function get_date_archive_page_subtitle($year, $month = 0)
        {
            if (isset($month) && ((int)($month) > 0)) {
                $month_name = date_i18n("F", mktime(0, 0, 0, (int)$month, 10));
                $page_subtitle = sprintf(__('Conversations started in %2$s %1$s', 'cuarme'), $year, $month_name);
            } else {
                $page_subtitle = sprintf(__('Conversations started in %1$s', 'cuarme'), $year);
            }

            return $page_subtitle;
        }

        protected function get_default_page_subtitle()
        {
            return __('Conversations', 'cuarme');
        }

        protected function get_default_dashboard_block_title()
        {
            return __('Recent Conversations', 'cuarme');
        }

        public function intercept_single_conversation_shown()
        {
            // If not on a matching post type, we do nothing
            if ( !is_singular($this->get_friendly_post_type())) {
                return;
            }

            // If not logged-in, bail
            if ( !is_user_logged_in()) return;

            $conversation = new CUAR_Conversation(get_the_ID());
            $conversation->mark_as_read_by_user(get_current_user_id());

            // If should mark as closed...
            if (isset($_GET['close_conversation'])
                && isset($_GET['close_nonce'])
                && wp_verify_nonce($_GET['close_nonce'], 'close_conversation_' . $conversation->ID)
            ) {
                /** @var CUAR_ConversationsAddOn $co_addon */
                $co_addon = $this->plugin->get_addon('conversations');
                if ($co_addon->can_user_close_conversation($conversation->ID)) {
                    $conversation->set_closed($_GET['close_conversation'] == 1);
                }
            }
        }

        /*------- SETTINGS -------------------------------------------------------------------------------------------*/

        public function is_rich_editor_enabled_for_replies()
        {
            return $this->plugin->get_option(self::$ENABLE_RICH_EDITOR_FOR_REPLIES, false);
        }

        /**
         * Set the default values for the options
         *
         * @param array $defaults
         *
         * @return array
         */
        public function set_default_options($defaults)
        {
            $defaults = parent::set_default_options($defaults);
            $defaults[self::$ENABLE_RICH_EDITOR_FOR_REPLIES] = false;

            return $defaults;
        }

        protected function print_additional_settings($cuar_settings, $options_group)
        {
            parent::print_additional_settings($cuar_settings, $options_group);

            add_settings_field(
                self::$ENABLE_RICH_EDITOR_FOR_REPLIES,
                __('Rich Editor', 'cuarme'),
                array(&$cuar_settings, 'print_input_field'),
                CUAR_Settings::$OPTIONS_PAGE_SLUG,
                $this->get_settings_section(),
                array(
                    'option_id' => self::$ENABLE_RICH_EDITOR_FOR_REPLIES,
                    'type'      => 'checkbox',
                    'after'     => __('Enable the rich editor for the reply form.', 'cuarme')
                )
            );
        }

        protected function validate_additional_settings(&$validated, $cuar_settings, $input)
        {
            $validated = parent::validate_additional_settings($validated, $cuar_settings, $input);

            $cuar_settings->validate_boolean($input, $validated, self::$ENABLE_RICH_EDITOR_FOR_REPLIES);

            return $validated;
        }

        public static $ENABLE_RICH_EDITOR_FOR_REPLIES = 'enable_richeditor_reply_conversation';

        /*------- ENHANCE CONVERSATION LIST VIEW ---------------------------------------------------------------------*/

        /**
         * Include the conversations that got started by the current user in the query
         */
        public function change_query_parameters($args)
        {
            $meta_query = isset($args['meta_query']) ? $args['meta_query'] : array();
            $meta_query = array_merge($meta_query, array(
                array(
                    'key'   => CUAR_Conversation::$META_STARTED_BY,
                    'value' => apply_filters('cuar/private-content/conversations/query/default/override-user-id', get_current_user_id())
                )
            ));
            $meta_query['relation'] = 'OR';

            $args['meta_query'] = $meta_query;

            return $args;
        }

        /*------- ENHANCE SINGLE CONVERSATION VIEW -------------------------------------------------------------------*/

        /**
         * Show some actions in the toolbar
         *
         * @param array $links
         *
         * @return array
         */
        public function get_single_content_action_links($links)
        {
            $conversation = new CUAR_Conversation(get_queried_object_id());

            /** @var CUAR_ConversationsAddOn $co_addon */
            $co_addon = $this->plugin->get_addon('conversations');

            if ($co_addon->can_user_close_conversation($conversation->ID)) {
                $closed = $conversation->is_closed();

                // Authors can close the conversation at any time
                $links[] = array(
                    'title'       => $closed
                        ? '<span class="fa fa-unlock"></span> ' . __('Re-open', 'cuarme')
                        : '<span class="fa fa-lock"></span> ' . __('Close', 'cuarme'),
                    'tooltip'     => $closed
                        ? __('Allow users to post new replies', 'cuarme')
                        : __('Close conversation to disallow new replies', 'cuarme'),
                    'url'         => $this->get_close_conversation_url($conversation->ID, !$closed),
                    'extra_class' => ''
                );
            }

            return $links;
        }

        protected function print_additional_private_content_footer()
        {
            $conversation_id = get_queried_object_id();

            /** @var CUAR_ConversationsAddOn $co_addon */
            $co_addon = $this->plugin->get_addon('conversations');
            $co_addon->editor()->print_replies($conversation_id);
        }

        /**
         * Get the URL to mark a conversation as closed
         *
         * @param int  $conversation_id
         * @param bool $should_close_it
         *
         * @return string
         */
        public function get_close_conversation_url($conversation_id, $should_close_it)
        {
            $url = trailingslashit(get_permalink($conversation_id));
            $url = add_query_arg(array(
                'close_conversation' => $should_close_it ? 1 : 0,
                'close_nonce'        => wp_create_nonce('close_conversation_' . $conversation_id)
            ), $url);

            return $url;
        }
    }

// Make sure the addon is loaded
    new CUAR_CustomerConversationsAddOn();

endif; // if (!class_exists('CUAR_CustomerConversationsAddOn'))
