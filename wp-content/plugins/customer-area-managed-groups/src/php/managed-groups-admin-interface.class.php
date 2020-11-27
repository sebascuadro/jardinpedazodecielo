<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/settings.class.php');

if (!class_exists('CUAR_ManagedGroupsAdminInterface')) :

    /**
     * Administation area for private files
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_ManagedGroupsAdminInterface
    {

        public function __construct($plugin, $mg_addon)
        {
            $this->plugin = $plugin;
            $this->mg_addon = $mg_addon;

            // Admin menu
            add_action("admin_footer", array(&$this, 'highlight_menu_item'));

            // Group edit page
            add_action('add_meta_boxes', array(&$this, 'register_edit_page_meta_boxes'));
            add_action('save_post', array(&$this, 'do_save_post'), 10, 2);
            add_action('admin_enqueue_scripts', array(&$this, 'enqueue_scripts'));

            // Group list page
            add_filter("manage_edit-cuar_managed_group_columns", array(&$this, 'group_column_register'));
            add_action("manage_cuar_managed_group_posts_custom_column", array(&$this, 'group_column_display'), 10, 2);

            // User profile
            add_action('show_user_profile', array(&$this, 'show_user_profile'));
            add_action('edit_user_profile', array(&$this, 'edit_user_profile'));
            add_action('personal_options_update', array(&$this, 'personal_options_update'));
            add_action('edit_user_profile_update', array(&$this, 'edit_user_profile_update'));

            // Settings
            add_action('cuar/core/settings/print-settings?tab=cuar_core', array(&$this, 'print_settings'), 10, 2);
            add_filter('cuar/core/settings/validate-settings?tab=cuar_core', array(&$this, 'validate_options'), 10, 3);

            // Ajax
            add_action('wp_ajax_cuar_search_managed_group_team', array(&$this, 'ajax_search_teammate'));
        }


        /*------- ADMIN MENU --------------------------------------------------------------------------------------------*/

        /**
         * Highlight the proper menu item in the customer area
         */
        public function highlight_menu_item()
        {
            global $post;

            // For posts
            if (isset($post) && get_post_type($post) == 'cuar_managed_group')
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
                    jQuery(document).ready(function ($)
                    {
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
            if (current_user_can('cuar_mg_edit_profile'))
            {
                $this->edit_user_profile($user);
            }
            else if (current_user_can('cuar_mg_view_profile'))
            {
                $groups_managed = $this->mg_addon->get_groups_managed_by_user($user->ID);
                $groups_subscribed = $this->mg_addon->get_groups_of_user($user->ID);
                $is_own_profile = ($user->ID == get_current_user_id());

                include($this->plugin->get_template_file_path(
                    CUARMG_INCLUDES_DIR,
                    "managed-groups_profile_list.template.php",
                    'templates'));
            }
        }

        public function edit_user_profile($user)
        {
            if (current_user_can('cuar_mg_edit_profile'))
            {
                $all_groups = $this->mg_addon->get_all_groups();
                $groups_managed = $this->mg_addon->get_groups_managed_by_user($user->ID);
                $groups_subscribed = $this->mg_addon->get_groups_of_user($user->ID);
                $is_own_profile = ($user->ID == get_current_user_id());

                /** @noinspection PhpIncludeInspection */
                include($this->plugin->get_template_file_path(
                    CUARMG_INCLUDES_DIR,
                    "managed-groups_profile_edit.template.php",
                    'templates'));
            }
            else if (current_user_can('cuar_mg_view_profile'))
            {
                $this->show_user_profile($user);
            }
        }

        public function personal_options_update($user_id)
        {
            $this->edit_user_profile_update($user_id);
        }

        public function edit_user_profile_update($user_id)
        {
            if (!current_user_can('cuar_mg_edit_profile'))
            {
                return;
            }

            $new_managed_group_ids = isset($_POST['cuar_managed_group_ids']) && is_array($_POST['cuar_managed_group_ids'])
                ? $_POST['cuar_managed_group_ids']
                : array();
            $user_groups = $this->mg_addon->get_groups_managed_by_user($user_id);

            // Remove from current groups that are not selected anymore
            foreach ($user_groups as $group)
            {
                if (!in_array($group->ID, $new_managed_group_ids))
                {
                    $this->mg_addon->remove_manager_from_group($user_id, $group->ID);
                }
            }

            // Add to all groups
            foreach ($new_managed_group_ids as $new_group_id)
            {
                $this->mg_addon->add_manager_to_group($user_id, $new_group_id);
            }

            $new_subscribed_group_ids = isset($_POST['cuar_subscribed_group_ids']) && is_array($_POST['cuar_subscribed_group_ids'])
                ? $_POST['cuar_subscribed_group_ids'] : array();
            $user_groups = $this->mg_addon->get_groups_of_user($user_id);

            // Remove from current groups that are not selected anymore
            foreach ($user_groups as $group)
            {
                if (!in_array($group->ID, $new_subscribed_group_ids))
                {
                    $this->mg_addon->remove_member_from_group($user_id, $group->ID);
                }
            }

            // Add to all groups
            foreach ($new_subscribed_group_ids as $new_group_id)
            {
                $this->mg_addon->add_member_to_group($user_id, $new_group_id);
            }
        }

        /*------- CUSTOMISATION OF THE LISTING OF POSTS -----------------------------------------------------------------*/

        /**
         * Enqueues the select script on the user-edit and profile screens.
         */
        public function enqueue_scripts()
        {
            $screen = get_current_screen();

            if (isset($screen->id))
            {
                switch ($screen->id)
                {
                    case 'cuar_managed_group' :
                        wp_enqueue_script('cuar.admin');
                        $this->plugin->enable_library('jquery.select2');
                        break;

                    case 'user-edit' :
                    case 'profile' :
                        $this->plugin->enable_library('jquery.select2');
                        break;
                }
            }
        }

        /**
         * Register the members and managers column
         */
        public function group_column_register($columns)
        {
            $columns['cuar_managers'] = __('Managers', 'cuarmg');
            $columns['cuar_members'] = __('Members', 'cuarmg');

            return $columns;
        }

        /**
         * Display the column content
         */
        public function group_column_display($column_name, $post_id)
        {
            if ('cuar_members' == $column_name)
            {
                $current_members = $this->mg_addon->get_group_members($post_id);
                $member_list = array();
                foreach ($current_members as $uid)
                {
                    $u = new WP_User($uid);
                    $member_list[] = $u->display_name;
                }

                echo implode(", ", $member_list);
            }
            else if ('cuar_managers' == $column_name)
            {
                $current_managers = $this->mg_addon->get_group_managers($post_id);
                $manager_list = array();
                foreach ($current_managers as $uid)
                {
                    $u = new WP_User($uid);
                    $manager_list[] = $u->display_name;
                }

                echo implode(", ", $manager_list);
            }
        }

        /*------- CUSTOMISATION OF THE GROUP EDIT PAGE ------------------------------------------------------------------*/

        /**
         * Register some additional boxes on the page to edit the files
         */
        public function register_edit_page_meta_boxes($post_type)
        {
            if ($post_type != 'cuar_managed_group')
            {
                return;
            }

            add_meta_box(
                'cuar_managed_group_team',
                __('Users who belong to this group', 'cuarmg'),
                array(&$this, 'print_team_meta_box'),
                'cuar_managed_group',
                'normal',
                'high');
        }

        /**
         * Print the metabox to set the group users
         */
        public function print_team_meta_box()
        {
            global $post;
            wp_nonce_field(plugin_basename(__FILE__), 'wp_cuar_nonce_team');

            $roles = array(
                'manager' => array(
                    'label_plural' => __('Managers', 'cuarmg'),
                    'users'        => $this->mg_addon->get_group_managers($post->ID),
                ),
                'member'  => array(
                    'label_plural' => __('Members', 'cuarmg'),
                    'users'        => $this->mg_addon->get_group_members($post->ID),
                ),
            );

            /** @noinspection PhpIncludeInspection */
            include($this->plugin->get_template_file_path(
                CUARMG_INCLUDES_DIR,
                "managed-groups_team.template.php",
                'templates'));
        }

        public function ajax_search_teammate() {
            $po_addon = $this->plugin->get_addon('post-owner');

            $team_role = $po_addon->ajax()->get_query_param('role', null, true);
            $po_addon->ajax()->check_nonce_query_param('cuar_search_managed_group_team_' . $team_role);
            $po_addon->ajax()->check_capability('cuar_access_admin_panel');
            $po_addon->ajax()->check_post_type_capability('cuar_managed_group', 'edit_posts');

            // Additional query args when restricting the user list to a given WP role
            $extra_args = array();
            if ($team_role==='manager') {
                $wp_role = $this->mg_addon->get_default_manager_role();
            } else if ($team_role==='member') {
                $wp_role = $this->mg_addon->get_default_member_role();
            } else {
                $wp_role = '';
            }

            if (!empty($wp_role) && $wp_role !== 'cuar_any')
            {
                $extra_args['role'] = $wp_role;
            }

            $search = $po_addon->ajax()->get_query_param('search', '');
            $page = $po_addon->ajax()->get_query_param('page', 1);
            wp_send_json_success($po_addon->ajax()->find_users($search, 'managed_group_team', $page, $extra_args));
        }

        /**
         * Callback to handle saving a post
         *
         * @param int    $post_id
         * @param string $post
         *
         * @return mixed
         */
        public function do_save_post($post_id, $post)
        {
            // When auto-saving, we don't do anything
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            {
                return $post_id;
            }

            // Only take care of our own post type
            if (!$post || get_post_type($post->ID) != 'cuar_managed_group')
            {
                return $post_id;
            }

            // Save the file
            if (!isset($_POST['wp_cuar_nonce_team'])
                || !wp_verify_nonce($_POST['wp_cuar_nonce_team'], plugin_basename(__FILE__))
            )
            {
                return $post_id;
            }

            $team = isset($_POST['managed_group_team']) ? $_POST['managed_group_team'] : array();

            $member_ids = isset($team['member']) ? $team['member'] : array();
            $this->mg_addon->set_group_members($post->ID, $member_ids);

            $manager_ids = isset($team['manager']) ? $team['manager'] : array();
            $this->mg_addon->set_group_managers($post->ID, $manager_ids);

            $this->plugin->add_admin_notice(
                sprintf(__('This group now has %d member(s) and %d manager(s)', 'cuarmg'),
                    count($member_ids), count($manager_ids)),
                'updated');
        }

        /*------- CUSTOMISATION OF THE PLUGIN SETTINGS PAGE -------------------------------------------------------------*/

        public function add_settings_tab($tabs)
        {
            $tabs['cuar_managed_groups'] = __('Managed Groups', 'cuarmg');

            return $tabs;
        }

        /**
         * Add our fields to the settings page
         *
         * @param CUAR_Settings $cuar_settings The settings class
         */
        public function print_settings($cuar_settings, $options_group)
        {
            add_settings_section(
                'cuar_managed_groups_admin',
                __('Managed Groups', 'cuarmg'),
                array(&$this, 'print_empty_section_info'),
                CUAR_Settings::$OPTIONS_PAGE_SLUG
            );

            add_settings_field(
                CUAR_ManagedGroupsAddOn::$OPTION_MANAGER_ROLE,
                __("Managers' Role", 'cuarmg'),
                array(&$cuar_settings, 'print_role_select_field'),
                CUAR_Settings::$OPTIONS_PAGE_SLUG,
                'cuar_managed_groups_admin',
                array(
                    'option_id'       => CUAR_ManagedGroupsAddOn::$OPTION_MANAGER_ROLE,
                    'show_any_option' => true,
                    'after'           => '<p class="description">'
                                         . __('The list of managers when creating or editing a managed group will be restricted to this role (or will not be restricted if you select "any role"). '
                                              . 'If your managers all belong to the same role, this makes selecting them easier.', 'cuarmg')
                                         . '</p>',
                )
            );

            add_settings_field(
                CUAR_ManagedGroupsAddOn::$OPTION_MEMBER_ROLE,
                __("Members' Role", 'cuarmg'),
                array(&$cuar_settings, 'print_role_select_field'),
                CUAR_Settings::$OPTIONS_PAGE_SLUG,
                'cuar_managed_groups_admin',
                array(
                    'option_id'       => CUAR_ManagedGroupsAddOn::$OPTION_MEMBER_ROLE,
                    'show_any_option' => true,
                    'after'           => '<p class="description">'
                                         . __('The list of members when creating or editing a managed group will be restricted to this role (or will not be restricted if you select "any role"). '
                                              . 'If your members all belong to the same role, this makes selecting them easier.', 'cuarmg')
                                         . '</p>',
                )
            );
        }

        /**
         * Validate our options
         *
         * @param CUAR_Settings $cuar_settings
         * @param array         $input
         * @param array         $validated
         */
        public function validate_options($validated, $cuar_settings, $input)
        {
            $cuar_settings->validate_role($input, $validated, CUAR_ManagedGroupsAddOn::$OPTION_MEMBER_ROLE);
            $cuar_settings->validate_role($input, $validated, CUAR_ManagedGroupsAddOn::$OPTION_MANAGER_ROLE);

            return $validated;
        }

        /**
         * Print some info about the section
         */
        public function print_empty_section_info()
        {
        }

        /** @var CUAR_Plugin */
        private $plugin;

        /** @var CUAR_ManagedGroupsAddOn */
        private $mg_addon;
    }

endif; // if (!class_exists('CUAR_ManagedGroupsAdminInterface')) :