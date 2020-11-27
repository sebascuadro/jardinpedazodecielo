<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon.class.php');

if ( !class_exists('CUAR_SearchAddOn')) :

    /**
     * Add-on to search for private content in the frontend
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_SearchAddOn extends CUAR_AddOn
    {

        public function __construct()
        {
            parent::__construct('search');
        }

        public function get_addon_name()
        {
            return __('Search', 'cuarse');
        }

        public function run_addon($plugin)
        {
            $this->enable_licensing(CUARSE_STORE_ITEM_ID, CUARSE_STORE_ITEM_NAME, CUARSE_PLUGIN_FILE, CUARSE_PLUGIN_VERSION);
            $this->load_textdomain();

            if (is_admin())
            {
                add_filter('cuar/core/status/directories-to-scan', array(&$this, 'add_hook_discovery_directory'));
            }
            else
            {
            }
        }

        public function add_hook_discovery_directory($dirs)
        {
            $dirs[CUARSE_PLUGIN_DIR] = $this->get_addon_name();

            return $dirs;
        }

        /**
         * Load the translation file for current language.
         */
        public function load_textdomain()
        {
            $this->plugin->load_textdomain('cuarse', 'customer-area-search');
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

            return $defaults;
        }

        public function find_private_content($criteria)
        {
            if ($criteria['owner_id'] <= 0 || $criteria['owner_id'] == null)
            {
                $criteria['owner_id'] = get_current_user_id();
            }

            $allowed_post_types = $criteria['post_type'];
            if ($allowed_post_types == null || $allowed_post_types == 'any')
            {
                $allowed_post_types = $this->plugin->get_content_post_types();
            }

            /** @var CUAR_PostOwnerAddOn $po_addon */
            $po_addon = $this->plugin->get_addon('post-owner');
            $owner_meta_query = $po_addon->get_meta_query_post_owned_by($criteria['owner_id']);

            $args = array(
                'query_filter'     => 'cuar_search_add_authored_by',
                'suppress_filters' => false,
                'post_type'        => $allowed_post_types,
                'meta_query'       => $owner_meta_query,
                'posts_per_page'   => $criteria['limit'],
                'orderby'          => $criteria['sort_by'],
                'order'            => $criteria['sort_order'],
            );

            if ($criteria['query'] != null && !empty($criteria['query']))
            {
                $args['s'] = $criteria['query'];
            }

            if ($criteria['author_id'] != null && !$criteria['author_id'] > 0)
            {
                $args['author'] = $criteria['author_id'];
            }

            $args = apply_filters('cuar/search/content/query-args', $args, $criteria);

            add_filter( 'posts_where', [&$this, 'filter_query_to_add_authored_by'], 9, 2);
            $posts = new WP_Query($args);
            remove_filter('posts_where', [&$this, 'filter_query_to_add_authored_by']);

            $result = array();
            foreach ($posts->posts as $post)
            {
                if (is_array($allowed_post_types) && !in_array($post->post_type, $allowed_post_types)) continue;
                if ( !is_array($allowed_post_types) && $post->post_type != $allowed_post_types) continue;

                if ( !isset($result[$post->post_type])) $result[$post->post_type] = array();
                $result[$post->post_type][] = $post;
            }

            ksort($result);

            return $result;
        }

        public function find_private_containers($criteria)
        {
            if ($criteria['owner_id'] <= 0 || $criteria['owner_id'] == null)
            {
                $criteria['owner_id'] = get_current_user_id();
            }

            $allowed_post_types = $criteria['post_type'];
            if ($allowed_post_types == null || $allowed_post_types == 'any')
            {
                $allowed_post_types = $this->plugin->get_container_post_types();
            }

            /** @var CUAR_ContainerOwnerAddOn $co_addon */
            $co_addon = $this->plugin->get_addon('container-owner');
            $args = array(
                'query_filter'     => 'cuar_search_add_authored_by',
                'suppress_filters' => false,
                'post_type'        => $allowed_post_types,
                'meta_query'       => $co_addon->get_meta_query_containers_owned_by($criteria['owner_id']),
                'posts_per_page'   => $criteria['limit'],
                'orderby'          => $criteria['sort_by'],
                'order'            => $criteria['sort_order'],
            );

            if ($criteria['query'] != null && !empty($criteria['query']))
            {
                $args['s'] = $criteria['query'];
            }

            if ($criteria['author_id'] != null && !$criteria['author_id'] > 0)
            {
                $args['author'] = $criteria['author_id'];
            }

            $args = apply_filters('cuar/search/container/query-args', $args, $criteria);

            add_filter( 'posts_where', [&$this, 'filter_query_to_add_authored_by'], 9, 2);
            $posts = new WP_Query($args);
            remove_filter('posts_where', [&$this, 'filter_query_to_add_authored_by']);

            $result = array();
            foreach ($posts->posts as $post)
            {
                if (is_array($allowed_post_types) && !in_array($post->post_type, $allowed_post_types)) continue;
                if ( !is_array($allowed_post_types) && $post->post_type != $allowed_post_types) continue;

                if ( !isset($result[$post->post_type])) $result[$post->post_type] = array();

                $result[$post->post_type][] = $post;
            }

            ksort($result);

            return $result;
        }

        public function filter_query_to_add_authored_by( $where, $q ) {
            if ( isset($q->query['query_filter']) && 'cuar_search_add_authored_by' === $q->query['query_filter'] ) {
                $disable_authored_by = apply_filters('cuar/core/page/query-disable-authored-by', true);
                foreach($q->query['post_type'] as $type) {
                    $disable_authored_by = apply_filters('cuar/core/page/query-disable-authored-by?post_type=' . $type, $disable_authored_by, $q);
                }
                if($disable_authored_by) return $where;

                $needle_open = "( wp_postmeta.meta_key = '" . CUAR_PostOwnerAddOn::$META_OWNER_QUERYABLE . "'";
                $pos_open = strpos($where, $needle_open);
                if ($pos_open !== false) {
                    $new_cond_open = "( post_author = '" . get_current_user_id() . "' ) OR " . $needle_open;
                    $where = substr_replace($where, $new_cond_open, $pos_open, strlen($needle_open));
                }
            }
            return $where;
        }
    }

// Make sure the addon is loaded
    new CUAR_SearchAddOn();

endif; // if (!class_exists('CUAR_SearchAddOn')) 
