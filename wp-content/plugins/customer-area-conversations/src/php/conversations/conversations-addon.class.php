<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon.class.php');

if ( !class_exists('CUAR_ConversationsAddOn')) :

    /**
     * Add-on to allow users to send messages to each other
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_ConversationsAddOn extends CUAR_AddOn
    {

        /** @var CUAR_ConversationsLogger $logger */
        private $logger;

        /** @var CUAR_ConversationEditorHelper $editor */
        private $editor;

        /** @var CUAR_ConversationsAdminInterface */
        private $admin_interface;

        /**
         * CUAR_ConversationsAddOn constructor.
         */
        public function __construct()
        {
            parent::__construct('conversations');
        }

        /** @override */
        public function get_addon_name()
        {
            return __('Conversations', 'cuarme');
        }

        /**
         * @param CUAR_Plugin $plugin
         */
        public function run_addon($plugin)
        {
            $this->enable_licensing(CUARME_STORE_ITEM_ID, CUARME_STORE_ITEM_NAME, CUARME_PLUGIN_FILE, CUARME_PLUGIN_VERSION);
            $this->load_textdomain();

            $this->logger = new CUAR_ConversationsLogger($plugin, $this);
            $this->editor = new CUAR_ConversationEditorHelper($plugin, $this);

            add_action('init', array(&$this, 'register_custom_types'));
            add_action('init', array(&$this, 'register_ajax_scripts'));

            add_filter('cuar/core/post-types/content', array(&$this, 'register_private_post_types'));
            add_filter('cuar/core/types/content', array(&$this, 'register_content_type'));
            add_filter('cuar/search/content/query-args', array(&$this, 'alter_search_args'), 10, 2);

            add_action('init', array(&$this, 'add_post_type_rewrites'));
            add_filter('post_type_link', array(&$this, 'built_post_type_permalink'), 1, 3);

            // Init the admin interface if needed
            if (is_admin()) {
                $this->admin_interface = new CUAR_ConversationsAdminInterface($plugin, $this);

                add_filter('cuar/core/status/directories-to-scan', array(&$this, 'add_hook_discovery_directory'));
            }

            add_action('wp_ajax_cuar_delete_reply', array(&$this, 'ajax_delete_reply'));
            add_action('wp_ajax_nopriv_cuar_delete_reply', array(&$this, 'ajax_delete_reply'));

            add_action('wp_ajax_cuar_add_reply', array(&$this, 'ajax_add_reply'));
            add_action('wp_ajax_nopriv_cuar_add_reply', array(&$this, 'ajax_add_reply'));

            add_filter('cuar/core/js-messages?zone=admin', array(&$this, 'add_js_messages'));
            add_filter('cuar/core/js-messages?zone=frontend', array(&$this, 'add_js_messages'));
        }

        public function add_hook_discovery_directory($dirs)
        {
            $dirs[CUARME_PLUGIN_DIR] = $this->get_addon_name();

            return $dirs;
        }

        /**
         * @return CUAR_ConversationEditorHelper
         */
        public function editor()
        {
            return $this->editor;
        }

        /*------- AJAX CALLBACKS -----------------------------------------------------------------------------------------*/

        /**
         * Register the JS scripts for later inclusion if required
         */
        public function register_ajax_scripts()
        {
            if (is_admin()) {
                wp_register_script('cuar.conversations.admin', CUARME_PLUGIN_URL . '/assets/admin/js/customer-area-conversations.min.js',
                    array('jquery', 'cuar.admin'), CUARME_PLUGIN_VERSION);
            } else {
                wp_register_script('cuar.conversations.frontend', CUARME_PLUGIN_URL . '/assets/frontend/js/customer-area-conversations.min.js',
                    array('jquery', 'cuar.frontend'), CUARME_PLUGIN_VERSION);
            }
        }

        /**
         * Add our JS messages
         *
         * @param array $messages
         *
         * @return array
         */
        public function add_js_messages($messages)
        {
            $messages['deleteReplyConfirmMessage'] = __('Are you sure that you want to delete this reply? ', 'cuarme');

            return $messages;
        }

        /**
         * Enqueue the scripts required by the editor
         */
        public function enqueue_scripts()
        {
            wp_enqueue_script(is_admin() ? 'cuar.conversations.admin' : 'cuar.conversations.frontend');
        }

        /**
         * Remove a reply from an AJAX request
         */
        public function ajax_delete_reply()
        {
            $conversation_id = isset($_POST['cuar_conversation_id']) ? $_POST['cuar_conversation_id'] : 0;
            if ($conversation_id <= 0) {
                wp_send_json_error(__('Conversation id is not specified', 'cuarme'));
            }

            // Check nonce
            $nonce_action = 'cuar-delete-reply-' . $conversation_id;
            $nonce_name = 'cuar_delete_reply_nonce';
            if ( !isset($_POST[$nonce_name]) || !wp_verify_nonce($_POST[$nonce_name], $nonce_action)) {
                wp_send_json_error(__('Trying to cheat?', 'cuarme'));
            }

            $reply_id = isset($_POST['cuar_reply_id']) ? $_POST['cuar_reply_id'] : 0;
            if ($reply_id <= 0) {
                wp_send_json_error(__('Reply id is not specified', 'cuarme'));
            }

            // Check permissions
            if ( !$this->user_can_delete_reply($conversation_id, $reply_id)) {
                wp_send_json_error(__('You are not allowed to delete this reply', 'cuarme'));
            }

            $this->editor()->delete_reply($conversation_id, $reply_id);

            wp_send_json_success(array('deleted' => true));
        }

        /**
         * Add a reply from an AJAX request
         */
        public function ajax_add_reply()
        {
            $conversation_id = isset($_POST['cuar_conversation_id']) ? $_POST['cuar_conversation_id'] : 0;
            if ($conversation_id <= 0) {
                wp_send_json_error(__('Conversation id is not specified', 'cuarme'));
            }

            // Is reply author allowed to post replies?
            if ( !$this->user_can_add_reply($conversation_id)) {
                wp_send_json_error(__('You are not allowed to post replies.', 'cuarme'));
            }

            // Check nonce
            $nonce_action = 'cuar-add-reply-' . $conversation_id;
            $nonce_name = 'cuar_add_reply_nonce';
            if ( !isset($_POST[$nonce_name]) || !wp_verify_nonce($_POST[$nonce_name], $nonce_action)) {
                wp_send_json_error(__('Trying to cheat?', 'cuarme'));
            }

            $reply_author_id = get_current_user_id();
            $reply_content = isset($_POST['cuar_reply_content']) ? $_POST['cuar_reply_content'] : '';
            if (empty($reply_content)) {
                wp_send_json_error(__('You cannot send an empty reply', 'cuarme'));
            }

            $reply_id = $this->editor()->add_reply($conversation_id, array(
                'reply_content'   => $reply_content,
                'reply_author_id' => $reply_author_id,
            ));

            if (is_wp_error($reply_id)) {
                wp_send_json_error(__('Failed to add reply', 'cuarme'));
            }

            $reply = new CUAR_ConversationReply($reply_id);
            $author = get_userdata($reply_author_id);

            wp_send_json_success(array(
                'user_can_delete'   => $this->user_can_delete_reply($conversation_id, $reply_id),
                'reply_id'          => $reply->ID,
                'reply_content'     => $reply->post->post_content,
                'reply_date'        => get_the_date('', $reply->ID),
                'reply_time'        => get_the_time('', $reply->ID),
                'author_name'       => $author->display_name,
                'author_avatar_url' => get_avatar_url($reply_author_id, array('size' => 64)),
            ));
        }

        /*------- PERMISSIONS --------------------------------------------------------------------------------------------*/

        /**
         * Does the current user have permission to delete a reply
         *
         * @param $conversation_id
         * @param $reply_id
         *
         * @return bool
         */
        public function user_can_delete_reply($conversation_id, $reply_id)
        {
            $has_permission = false;

            // Can delete anything
            if (current_user_can('cuarme_delete_any_reply')) $has_permission = true;

            // Can delete replies in own conversations
            if ( !$has_permission && current_user_can('cuarme_delete_replies_in_conversation')) {
                $c = new CUAR_Conversation($conversation_id);
                if ($c->post->post_author == get_current_user_id()) $has_permission = true;
            }

            // Can delete replies he created
            if ( !$has_permission && current_user_can('cuarme_delete_own_replies')) {
                $r = new CUAR_ConversationReply($reply_id);
                if ($r->post->post_author == get_current_user_id()) $has_permission = true;
            }

            return apply_filters('cuar/private-content/conversation/permissions/delete-reply',
                $has_permission,
                $conversation_id, $reply_id);
        }

        /**
         * Does the current user have permission to add a reply to the conversation
         *
         * @param $conversation_id
         *
         * @return bool
         */
        public function user_can_add_reply($conversation_id)
        {
            $allowed = false;

            /** @var CUAR_PostOwnerAddOn $po_addon */
            $po_addon = $this->plugin->get_addon('post-owner');
            $current_user_id = get_current_user_id();

            $conversation = new CUAR_Conversation($conversation_id);

            if ($conversation->is_closed()) {
                $allowed = false;
            } else if ( !current_user_can('cuarme_reply_to_conversation')) {
                $allowed = false;
            } else if ($current_user_id == $conversation->post->post_author) {
                $allowed = true;
            } else if ($po_addon->is_user_owner_of_post($conversation_id, $current_user_id)) {
                $allowed = true;
            }

            return apply_filters('cuar/private-content/conversation/permissions/add-reply', $allowed, $conversation_id);
        }

        public function can_user_close_conversation($conversation_id)
        {
            $allowed = false;

            $conversation = new CUAR_Conversation($conversation_id);
            if (get_current_user_id() == $conversation->post->post_author) {
                $allowed = true;
            }

            return apply_filters('cuar/private-content/conversation/permissions/close', $allowed, $conversation_id);
        }

        /*------- QUERIES TO LIST CONVERSATIONS AND REPLIES --------------------------------------------------------------*/

        public function get_default_conversations_query_args($current_page = 0, $limit = 10)
        {
            /** @var CUAR_PostOwnerAddOn $po_addon */
            $po_addon = $this->plugin->get_addon('post-owner');

            $meta_query = $po_addon->get_meta_query_post_owned_by(get_current_user_id());
            $meta_query = array_merge($meta_query, array(
                array(
                    'key'   => CUAR_Conversation::$META_STARTED_BY,
                    'value' => apply_filters('cuar/private-content/conversations/query/default/override-user-id', get_current_user_id())
                )
            ));
            $meta_query['relation'] = 'OR';

            // Get user pages
            $args = array(
                'post_type'      => CUAR_Conversation::$POST_TYPE,
                'posts_per_page' => $limit,
                'paged'          => $current_page,
                'orderby'        => 'modified',
                'order'          => 'DESC',
                'meta_query'     => $meta_query
            );

            return apply_filters('cuar/private-content/conversations/query/started-by/query-args', $args);
        }

        public function get_started_conversations_query_args($current_page = 0, $limit = 10)
        {
            // Get user pages
            $args = array(
                'post_type'      => CUAR_Conversation::$POST_TYPE,
                'posts_per_page' => $limit,
                'paged'          => $current_page,
                'orderby'        => 'modified',
                'order'          => 'DESC',
                'author'         => apply_filters('cuar/private-content/conversations/query/started-by/override-user-id', get_current_user_id())
            );

            return apply_filters('cuar/private-content/conversations/query/started-by/query-args', $args);
        }

        public function get_participated_conversations_query_args($current_page = 0, $limit = 10)
        {
            /** @var CUAR_PostOwnerAddOn $po_addon */
            $po_addon = $this->plugin->get_addon('post-owner');

            // Get user pages
            $args = array(
                'post_type'      => CUAR_Conversation::$POST_TYPE,
                'posts_per_page' => $limit,
                'paged'          => $current_page,
                'orderby'        => 'modified',
                'order'          => 'DESC',
                'meta_query'     => $po_addon->get_meta_query_post_owned_by(get_current_user_id())
            );

            return apply_filters('cuar/private-content/conversations/query/started-by/query-args', $args);
        }

        public function get_conversation_replies_query_args($post_id)
        {
            $args = array(
                'post_type'      => CUAR_ConversationReply::$POST_TYPE,
                'posts_per_page' => -1,
                'orderby'        => 'date',
                'order'          => 'ASC',
                'post_parent'    => $post_id
            );

            return apply_filters('cuar/private-content/conversations/query/replies/query-args', $args);
        }

        public function alter_search_args($args, $criterias)
        {
            $args['meta_query'] = array_merge($args['meta_query'], array(
                array(
                    'key'   => CUAR_Conversation::$META_STARTED_BY,
                    'value' => $criterias['owner_id']
                )
            ));

            return $args;
        }

        /*------- INITIALISATION -----------------------------------------------------------------------------------------*/

        /**
         * Declare our content type
         *
         * @param array $types
         *
         * @return array
         */
        public function register_content_type($types)
        {
            $types[CUAR_Conversation::$POST_TYPE] = array(
                'label-singular'     => _x('Conversation', 'cuar_conversation', 'cuarme'),
                'label-plural'       => _x('Conversations', 'cuar_conversation', 'cuarme'),
                'content-page-addon' => 'customer-conversations',
                'type'               => 'content'
            );

            return $types;
        }

        /**
         * Declare that our post type is owned by someone
         *
         * @param array $types
         *
         * @return array
         */
        public function register_private_post_types($types)
        {
            $types[] = CUAR_Conversation::$POST_TYPE;

            return $types;
        }

        /**
         * Register the custom post type for files and the associated taxonomies
         */
        public function register_custom_types()
        {
            CUAR_Conversation::register_post_type();
            CUAR_ConversationReply::register_post_type();
        }

        /**
         * Add the rewrite rule for the private files.
         */
        function add_post_type_rewrites()
        {
            global $wp_rewrite;

            $pf_slug = 'conversation';

            $wp_rewrite->add_rewrite_tag('%cuar_conversation%', '([^/]+)', 'cuar_conversation=');
            $wp_rewrite->add_permastruct('cuar_conversation',
                $pf_slug . '/%year%/%monthnum%/%day%/%cuar_conversation%',
                false);
        }

        /**
         * Build the permalink for the private files
         *
         * @param unknown $post_link
         * @param unknown $post
         * @param unknown $leavename
         *
         * @return unknown|mixed
         */
        function built_post_type_permalink($post_link, $post, $leavename)
        {
            // Only change permalinks for private files
            if ($post->post_type != CUAR_Conversation::$POST_TYPE) return $post_link;

            // Only change permalinks for published posts
            $draft_or_pending = isset($post->post_status)
                && in_array($post->post_status, array('draft', 'pending', 'auto-draft'));
            if ($draft_or_pending and !$leavename) return $post_link;

            // Change the permalink
            global $wp_rewrite, $cuar_pf_addon;

            $permalink = $wp_rewrite->get_extra_permastruct('cuar_conversation');
            $permalink = str_replace("%cuar_conversation%", $post->post_name, $permalink);

            $post_date = strtotime($post->post_date);
            $permalink = str_replace("%year%", date_i18n("Y", $post_date), $permalink);
            $permalink = str_replace("%monthnum%", date_i18n("m", $post_date), $permalink);
            $permalink = str_replace("%day%", date_i18n("d", $post_date), $permalink);

            $permalink = home_url() . "/" . user_trailingslashit($permalink);
            $permalink = str_replace("//", "/", $permalink);
            $permalink = str_replace(":/", "://", $permalink);

            return $permalink;
        }

        /*------- INITIALISATION ----------------------------------------------------------------------------------------*/

        /**
         * Load the translation file for current language.
         */
        public function load_textdomain()
        {
            $this->plugin->load_textdomain('cuarme', 'customer-area-conversations');
        }
    }

// Make sure the addon is loaded
    new CUAR_ConversationsAddOn();

endif; // if (!class_exists('CUAR_ConversationsAddOn')) 
