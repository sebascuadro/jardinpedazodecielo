<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/settings.class.php');

if ( !class_exists('CUAR_SmartGroupsAdminInterface')) :

    /**
     * Administation area for private files
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_SmartGroupsAdminInterface
    {

        public function __construct($plugin, $sg_addon)
        {
            $this->plugin = $plugin;
            $this->sg_addon = $sg_addon;

            // Admin menu
            add_action("admin_footer", array(&$this, 'highlight_menu_item'));

            // Group edit page
            add_action('add_meta_boxes', array(&$this, 'register_edit_page_meta_boxes'));
            add_action('save_post', array(&$this, 'do_save_post'), 10, 2);
            add_action('admin_enqueue_scripts', array(&$this, 'enqueue_scripts'));

            // Group list page
            add_filter("manage_edit-cuar_smart_group_columns", array(&$this, 'group_column_register'));
            add_action("manage_cuar_smart_group_posts_custom_column", array(&$this, 'group_column_display'), 10, 2);

            // User profile
            add_action('show_user_profile', array(&$this, 'show_user_profile'));
            add_action('edit_user_profile', array(&$this, 'edit_user_profile'));
            add_action('personal_options_update', array(&$this, 'personal_options_update'));
            add_action('edit_user_profile_update', array(&$this, 'edit_user_profile_update'));
        }


        /*------- ADMIN MENU --------------------------------------------------------------------------------------------*/

        /**
         * Highlight the proper menu item in the customer area
         */
        public function highlight_menu_item()
        {
            global $post;

            // For posts
            if (isset($post) && get_post_type($post) == 'cuar_smart_group')
            {
                $highlight_top = '#toplevel_page_wpca';
                $unhighligh_top = '#menu-posts';
            }
            else
            {
                $highlight_top = null;
                $unhighligh_top = null;
            }

            if ($highlight_top && $unhighligh_top)
            {
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        $('<?php echo $unhighligh_top; ?>')
                            .removeClass('wp-has-current-submenu')
                            .addClass('wp-not-current-submenu');
                        $('<?php echo $highlight_top; ?>')
                            .removeClass('wp-not-current-submenu')
                            .addClass('wp-has-current-submenu current');
                    });
                </script>
                <?php
            }
        }

        /*------- USER PROFILE ------------------------------------------------------------------------------------------*/

        public function show_user_profile($user)
        {
            if (current_user_can('cuar_sg_view_profile'))
            {
                $groups = $this->sg_addon->get_groups_of_user($user->ID);
                $is_own_profile = ($user->ID == get_current_user_id());

                include($this->plugin->get_template_file_path(
                    CUARSG_INCLUDES_DIR,
                    "smart-groups_profile_list.template.php",
                    'templates'));
            }
        }

        public function edit_user_profile($user)
        {
            $this->show_user_profile($user);
        }

        public function personal_options_update($user_id)
        {
            $this->edit_user_profile_update($user_id);
        }

        public function edit_user_profile_update($user_id)
        {
            // Does nothing, we can't manually get in or out a smart group
        }

        /*------- CUSTOMISATION OF THE LISTING OF POSTS -----------------------------------------------------------------*/

        /**
         * Register the members and managers column
         */
        public function group_column_register($columns)
        {
            $columns['cuar_members'] = __('Members', 'cuarsg');

            return $columns;
        }

        /**
         * Display the column content
         */
        public function group_column_display($column_name, $post_id)
        {
            if ('cuar_members' == $column_name)
            {
                $max_shown = 20;
                $current_members = $this->sg_addon->get_group_members($post_id, false);
                $user_count = count($current_members);

                if ($user_count > 0)
                {
                    $tokens = array();
                    $i = 0;
                    foreach ($current_members as $u)
                    {
                        $tokens[] = sprintf('<a href="%4$s" class="cuar-user" title="%3$s - %2$s">%1$s</a>',
                            $u->user_login, $u->display_name, $u->ID, admin_url('user-edit.php?user_id=' . $u->ID));

                        if ($i > $max_shown) break;
                        ++$i;
                    }

                    echo implode(", ", $tokens);

                    if ($user_count > $max_shown)
                    {
                        printf(__('... and %1$s more', 'cuarsg'), $user_count - $max_shown);
                    }
                }
                else
                {
                    _e('This group is empty', 'cuarsg');
                }
            }
        }

        /*------- CUSTOMISATION OF THE GROUP EDIT PAGE ------------------------------------------------------------------*/

        /**
         * Enqueue javascript when necessary
         */
        public function enqueue_scripts()
        {
            $screen = get_current_screen();

            if (isset($screen->id))
            {
                switch ($screen->id)
                {
                    case 'cuar_managed_group' :
                        // $this->plugin->enable_library('jquery.select2');
                        break;
                }
            }
        }

        /**
         * Register some additional boxes on the page to edit the files
         */
        public function register_edit_page_meta_boxes($post_type)
        {
            if ($post_type!='cuar_smart_group') return;

            add_meta_box(
                'cuar_smart_group_search_criteria',
                __('Search criteria', 'cuarsg'),
                array(&$this, 'print_search_criteria_meta_box'),
                'cuar_smart_group',
                'normal',
                'high');

            add_meta_box(
                'cuar_smart_group_meta_criteria',
                __('Custom fields criteria', 'cuarsg'),
                array(&$this, 'print_meta_criteria_meta_box'),
                'cuar_smart_group',
                'normal',
                'high');

            add_meta_box(
                'cuar_smart_group_preview',
                __('Group preview', 'cuarsg'),
                array(&$this, 'print_preview_meta_box'),
                'cuar_smart_group',
                'normal',
                'high');
        }

        /**
         * Print the metabox to set managers
         */
        public function print_search_criteria_meta_box()
        {
            global $post;
            wp_nonce_field(plugin_basename(__FILE__), 'wp_cuar_nonce_sg');

            // $current_managers = $this->sg_addon->get_group_managers($post->ID);
            $sc = $this->sg_addon->get_search_criteria($post->ID);

            $all_fields = array(
                'login'    => __('Username (login)', 'cuarsg'),
                'nicename' => __('Nice name', 'cuarsg'),
                'email'    => __('Email', 'cuarsg'),
                'url'      => __('URL', 'cuarsg')
            );

            include($this->plugin->get_template_file_path(
                CUARSG_INCLUDES_DIR,
                'smart-groups_search-criteria-metabox.template.php',
                'templates'));
        }

        /**
         * Print the metabox to set managers
         */
        public function print_meta_criteria_meta_box()
        {
            global $post;

            // We need some library
            $this->plugin->enable_library('jquery.repeatable-fields');

            // $current_managers = $this->sg_addon->get_group_managers($post->ID);
            $mc = $this->sg_addon->get_meta_criteria($post->ID);

            $all_relations = array(
                'OR'  => __('OR', 'cuarsg'),
                'AND' => __('AND', 'cuarsg')
            );
            $all_operators = array(
                '='        => __('is equal to', 'cuarsg'),
                '!='       => __('is not equals to', 'cuarsg'),
                '>'        => __('is greater than', 'cuarsg'),
                '>='       => __('is greater than or equal to', 'cuarsg'),
                '<'        => __('is lower than', 'cuarsg'),
                '<='       => __('is lower than or equal to', 'cuarsg'),
                'LIKE'     => __('contains', 'cuarsg'),
                'NOT LIKE' => __('does not contain', 'cuarsg'),
                /*
                 * CURRENTLY NOT SUPPORTED
                'IN'          => __('in', 'cuarsg'),
                'NOT IN'      => __('not in', 'cuarsg'),
                'BETWEEN'     => __('between', 'cuarsg'),
                'NOT BETWEEN' => __('not between', 'cuarsg'),
                'EXISTS'      => __('exists', 'cuarsg'),
                'NOT EXISTS'  => __('does not exist', 'cuarsg')
                */
            );
            $all_keys = $this->get_user_meta_keys();

            $item_template = $this->plugin->get_template_file_path(
                CUARSG_INCLUDES_DIR,
                'smart-groups_meta-criteria-metabox-query-item.template.php',
                'templates');

            include($this->plugin->get_template_file_path(
                CUARSG_INCLUDES_DIR,
                'smart-groups_meta-criteria-metabox.template.php',
                'templates'));
        }

        /**
         * Print the metabox to set members
         */
        public function print_preview_meta_box()
        {
            global $post;
            $users = $this->sg_addon->get_group_members($post->ID, false);

            include($this->plugin->get_template_file_path(
                CUARSG_INCLUDES_DIR,
                'smart-groups_preview-metabox.template.php',
                'templates'));
        }

        /**
         * Callback to handle saving a post
         *
         * @param int     $post_id
         * @param WP_Post $post
         *
         * @return void|unknown
         */
        public function do_save_post($post_id, $post)
        {
            // When auto-saving, we don't do anything
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

            // Only take care of our own post type
            if ( !$post || get_post_type($post->ID) != 'cuar_smart_group') return $post_id;

            // Save the file
            if ( !isset($_POST['wp_cuar_nonce_sg'])
                || !wp_verify_nonce($_POST['wp_cuar_nonce_sg'], plugin_basename(__FILE__))
            )
            {
                return $post_id;
            }

            $update_successful = true;

            // Search criterias
            $sc_enabled = isset($_POST['cuar_sc_enabled']) ? 1 : 0;
            if ($sc_enabled)
            {
                $query = isset($_POST['cuar_sc_query']) ? $_POST['cuar_sc_query'] : "";
                $fields = isset($_POST['cuar_sc_fields']) ? $_POST['cuar_sc_fields'] : array();

                $errors = $this->sg_addon->enable_search_criteria($post->ID, $query, $fields);
                if ( !empty($errors))
                {
                    $update_successful = false;
                    foreach ($errors as $err)
                    {
                        $this->plugin->add_admin_notice($err);
                    }
                }
            }
            else
            {
                $this->sg_addon->disable_search_criteria($post->ID);
            }

            // Custom fields criteria
            $mc_enabled = isset($_POST['cuar_mc_enabled']) ? 1 : 0;
            if ($mc_enabled)
            {
                $relation = isset($_POST['cuar_mc_relation']) ? $_POST['cuar_mc_relation'] : "";
                $items = isset($_POST['cuar_mc_items']) ? $_POST['cuar_mc_items'] : array();

                $errors = $this->sg_addon->enable_meta_criteria($post->ID, $relation, $items);
                if ( !empty($errors))
                {
                    $update_successful = false;
                    foreach ($errors as $err)
                    {
                        $this->plugin->add_admin_notice($err);
                    }
                }
            }
            else
            {
                $this->sg_addon->disable_meta_criteria($post->ID);
            }

            if ($update_successful)
            {
                $this->plugin->add_admin_notice(__('The group has been updated', 'cuarsg'), 'updated');
            }
        }

        /**
         * Returns all unique meta key from user meta database
         *
         * @param no parameter right now
         *
         * @retun std Class
         */
        private function get_user_meta_keys()
        {
            global $wpdb;
            $select = "SELECT distinct $wpdb->usermeta.meta_key FROM $wpdb->usermeta ORDER BY $wpdb->usermeta.meta_key ASC";
            $usermeta = $wpdb->get_results($select);

            return $usermeta;
        }

        /** @var CUAR_Plugin */
        private $plugin;

        /** @var CUAR_SmartGroupsAddOn */
        private $sg_addon;
    }

endif; // if (!class_exists('CUAR_SmartGroupsAdminInterface')) :