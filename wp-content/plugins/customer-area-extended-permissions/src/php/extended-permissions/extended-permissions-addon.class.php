<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon.class.php');

if (!class_exists('CUAR_ExtendedPermissionsAddOn')) :

    /**
     * Add-on to allow setting user groups or user roles as owner of a private content
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_ExtendedPermissionsAddOn extends CUAR_AddOn
    {

        public function __construct()
        {
            parent::__construct('extended-permissions');
        }

        public function get_addon_name()
        {
            return __('Additional owner types', 'cuarep');
        }

        public function run_addon($plugin)
        {
            $this->enable_licensing(CUAREP_STORE_ITEM_ID, CUAREP_STORE_ITEM_NAME, CUAREP_PLUGIN_FILE, CUAREP_PLUGIN_VERSION);

            add_action('init', array(&$this, 'load_textdomain'));

            add_filter('cuar/core/ownership/content/meta-query',
                array(&$this, 'extend_private_posts_meta_query'), 10, 3);
            add_filter('cuar/core/ownership/owner-types', array(&$this, 'declare_new_owner_types'));
            add_filter('cuar/core/ownership/validate-post-ownership', array(&$this, 'is_user_owner_of_post'), 10, 5);
            add_filter('cuar/core/ownership/saved-displayname', array(&$this, 'saved_post_owner_displayname'), 10, 4);

            foreach (array('rol', 'grp', 'glo') as $t)
            {
                add_filter('cuar/core/ownership/real-user-ids?owner-type=' . $t,
                    array(&$this, 'get_post_owner_user_ids_from_' . $t), 10, 2);

                add_filter('cuar/core/ajax/search/post-owners?owner-type=' . $t,
                    array(&$this, 'get_printable_owners_for_type_' . $t), 10, 3);

                add_filter('cuar/core/ownership/enable-multiple-select?owner-type=' . $t,
                    array(&$this, 'enable_multiple_select_for_type_' . $t), 10, 2);

                add_filter('cuar/core/ownership/owner-display-name?owner-type=' . $t,
                    array(&$this, 'get_owner_display_name_for_type_' . $t), 10, 2);
            }

            add_filter('cuar/core/ownership/enable-multiple-select?owner-type=usr',
                array(&$this, 'enable_multiple_select_for_type_usr'), 10, 2);

            if (is_admin())
            {
                add_filter('cuar/core/status/directories-to-scan', array(&$this, 'add_hook_discovery_directory'));
            }
        }

        public function add_hook_discovery_directory($dirs)
        {
            $dirs[CUAREP_PLUGIN_DIR] = $this->get_addon_name();

            return $dirs;
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

        /*------- EXTEND THE OWNER TYPES AVAILABLE ----------------------------------------------------------------------*/

        /**
         * Give the display name for our owner types
         *
         * @param string $displayname
         * @param int    $post_id
         * @param string $owner_type
         * @param array  $owner_ids
         *
         * @return string
         *
         */
        public function saved_post_owner_displayname($displayname, $post_id, $owner_type, $owner_ids)
        {
            $names = array();
            foreach ($owner_ids as $owner_id)
            {
                if ($owner_type == 'rol')
                {
                    $names[] = $this->get_owner_display_name_for_type_rol($displayname, $owner_id);
                }
                else if ($owner_type == 'grp')
                {
                    $names[] = $this->get_owner_display_name_for_type_grp($displayname, $owner_id);
                }
                else if ($owner_type == 'glo')
                {
                    $names[] = $this->get_owner_display_name_for_type_glo($displayname, $owner_id);
                }
            }
            asort($names);

            return empty($names) ? $displayname : implode(", ", $names);
        }

        /**
         * @param $name
         * @param $owner_id
         * @return string
         */
        public function get_owner_display_name_for_type_glo($name, $owner_id)
        {
            if ($owner_id == 'any_reg')
            {
                return __('Any registered user', 'cuarep');
            }

            return $name;
        }

        /**
         * @param $name
         * @param $owner_id
         * @return string
         */
        public function get_owner_display_name_for_type_grp($name, $owner_id)
        {
            $title = get_the_title($owner_id);
            if ($title != null && !empty($title))
            {
                return $title;
            }

            return $name;
        }

        /**
         * @param $name
         * @param $owner_id
         * @return string
         */
        public function get_owner_display_name_for_type_rol($name, $owner_id)
        {
            global $wp_roles;
            if (!isset($wp_roles))
            {
                $wp_roles = new WP_Roles();
            }

            if (isset($wp_roles->role_names[$owner_id]))
            {
                return translate_user_role($wp_roles->role_names[$owner_id]);
            }

            return $name;
        }

        /**
         * Check if a user owns the given post
         *
         * @param boolean $initial_result
         * @param int     $post_id
         * @param int     $user_id
         * @param string  $post_owner_type
         * @param array   $post_owner_ids
         *
         * @return boolean true if the user owns the post
         */
        public function is_user_owner_of_post($initial_result, $post_id, $user_id, $post_owner_type, $post_owner_ids)
        {
            if ($initial_result)
            {
                return true;
            }

            // If post owner type is a role, check the user has the given role
            if ($post_owner_type == 'rol')
            {
                $user = new WP_User($user_id);
                $user_roles = $user->roles;
                if (!empty($user_roles) && is_array($user_roles))
                {
                    foreach ($user_roles as $role)
                    {
                        if (in_array($role, $post_owner_ids))
                        {
                            return true;
                        }
                    }
                }
            }
            else if ($post_owner_type == 'grp')
            {
                /** @var CUAR_UserGroupAddOn $ug_addon */
                $ug_addon = $this->plugin->get_addon('user-group');
                $user_groups = $ug_addon->get_groups_of_user($user_id);

                if (!empty($user_groups) && is_array($user_groups))
                {
                    foreach ($user_groups as $g)
                    {
                        if (in_array($g->ID, $post_owner_ids))
                        {
                            return true;
                        }
                    }
                }
            }
            else if ($post_owner_type == 'glo')
            {
                $rule = isset($post_owner_ids[0]) ? $post_owner_ids[0] : '';
                switch ($rule)
                {
                    case 'any_reg':
                        return $user_id > 0;
                }
            }

            return false;
        }

        /**
         * Print a select field with various global rules
         *
         * @param $response
         * @param $search
         * @param $page
         *
         * @return array
         */
        public function get_printable_owners_for_type_glo($response, $search, $page)
        {
            // Allow 3rd party code to provide their own selection
            $items = apply_filters('cuar/core/ownership/selectable-owners?owner-type=glo', null, $search, $page);
            if ($items === null)
            {
                $items = array(
                    'any_reg' => __('Any registered user', 'cuarep'),
                );

                /** @var CUAR_PostOwnerAddOn $po_addon */
                $po_addon = $this->plugin->get_addon('post-owner');
                list($results, $has_more) = $po_addon->ajax()->format_items_for_select2($items, $search, $page);
            }
            else
            {
                list($results, $has_more) = $items;
            }

            $response['results'] = $results;
            $response['more'] = $has_more;

            return $response;
        }

        /**
         * Print a select field with all roles
         *
         * @param $response
         * @param $search
         * @param $page
         * @return array
         */
        public function get_printable_owners_for_type_rol($response, $search, $page)
        {
            // Allow 3rd party code to provide their own selection
            $items = apply_filters('cuar/core/ownership/selectable-owners?owner-type=rol', null, $search, $page);
            if ($items === null)
            {
                global $wp_roles;
                if (!isset($wp_roles))
                {
                    $wp_roles = new WP_Roles();
                }

                /** @var CUAR_PostOwnerAddOn $po_addon */
                $po_addon = $this->plugin->get_addon('post-owner');
                list($results, $has_more) = $po_addon->ajax()->format_items_for_select2($wp_roles->role_names, $search, $page);
            }
            else
            {
                list($results, $has_more) = $items;
            }

            $response['results'] = $results;
            $response['more'] = $has_more;

            return $response;
        }

        /**
         * Print a select field with all user groups
         *
         * @param $response
         * @param $search
         * @param $page
         * @return array
         */
        public function get_printable_owners_for_type_grp($response, $search, $page)
        {
            // Allow 3rd party code to provide their own selection
            $items = apply_filters('cuar/core/ownership/selectable-owners?owner-type=grp', null, $search, $page);
            if ($items === null)
            {
                $ug_addon = $this->plugin->get_addon('user-group');
                $items = $ug_addon->get_selectable_groups($search, $page);

                $results = $items['results'];
                $has_more = $items['more'];
            }
            else
            {
                list($results, $has_more) = $items;
            }

            $response['results'] = $results;
            $response['more'] = $has_more;

            return $response;
        }

        public function enable_multiple_select_for_type_glo($val)
        {
            return false;
        }

        public function enable_multiple_select_for_type_usr($val)
        {
            return true;
        }

        public function enable_multiple_select_for_type_grp($val)
        {
            return true;
        }

        public function enable_multiple_select_for_type_rol($val)
        {
            return true;
        }

        /**
         * Extend the meta query to fetch private posts belonging to a user (also fetches the posts for his role and
         * groups)
         *
         * @param array               $base_meta_query
         * @param int                 $user_id The user we want to fetch private posts for
         * @param CUAR_PostOwnerAddOn $po_addon
         *
         * @return array
         */
        public function extend_private_posts_meta_query($base_meta_query, $user_id, $po_addon)
        {
            // For roles
            $roles_meta_query = array();
            $user = new WP_User($user_id);
            $user_roles = $user->roles;
            if (!empty($user_roles) && is_array($user_roles))
            {
                foreach ($user_roles as $role)
                {
                    $roles_meta_query[] = $po_addon->get_owner_meta_query_component('rol', $role);
                }
            }

            // For user groups
            $groups_meta_query = array();
            /** @var CUAR_UserGroupAddOn $ug_addon */
            $ug_addon = $this->plugin->get_addon('user-group');
            $user_groups = $ug_addon->get_groups_of_user($user_id);

            if (!empty($user_groups) && is_array($user_groups))
            {
                foreach ($user_groups as $g)
                {
                    $groups_meta_query[] = $po_addon->get_owner_meta_query_component('grp', $g->ID);
                }
            }

            // For global rules
            // Any registered user
            $global_rules_meta_query = array();
            if ($user_id > 0)
            {
                $global_rules_meta_query[] = $po_addon->get_owner_meta_query_component('glo', 'any_reg');
            }

            // Deal with all this
            return array_merge($base_meta_query, $roles_meta_query, $groups_meta_query, $global_rules_meta_query);
        }

        /**
         * Declare the new owner types managed by this add-on
         *
         * @param array $types the existing types
         *
         * @return array The existing types + our types
         */
        public function declare_new_owner_types($types)
        {
            $new_types = array(
                'glo' => __('Global', 'cuarep'),
                'rol' => __('Role', 'cuarep'),
                'grp' => __('User Group', 'cuarep'),
            );

            return array_merge($types, $new_types);
        }

        /**
         * Return all user IDs for the given rule
         *
         * @param array $users the initial users
         * @param array $rule  The type of selection to do
         *
         * @return array
         */
        public function get_post_owner_user_ids_from_glo($users, $rule)
        {
            if (in_array('any_reg', $rule))
            {
                $users = get_users(array(
                    'fields'  => 'ID',
                    'orderby' => 'display_name',
                ));
            }

            return array_unique($users, SORT_REGULAR);
        }

        /**
         * Return all user IDs that belong to the given role
         *
         * @param array  $users   the initial users
         * @param string $role_id The role id
         *
         * @return array
         */
        public function get_post_owner_user_ids_from_rol($users, $role_ids)
        {
            $all_users = $users;

            foreach ($role_ids as $role_id)
            {
                $users_for_role = get_users(array(
                    'role'    => $role_id,
                    'fields'  => 'ID',
                    'orderby' => 'display_name',
                ));

                $all_users = array_merge($all_users, $users_for_role);
            }

            return array_unique($all_users, SORT_REGULAR);
        }

        /**
         * Return all user IDs that belong to the given group
         *
         * @param array  $users   the initial users
         * @param string $role_id The role id
         *
         * @return array
         */
        public function get_post_owner_user_ids_from_grp($users, $group_ids)
        {
            $ug_addon = $this->plugin->get_addon('user-group');

            $all_users = $users;

            foreach ($group_ids as $group_id)
            {
                $users_for_group = $ug_addon->get_group_members($group_id);

                $all_users = array_merge($all_users, $users_for_group);
            }

            return array_unique($all_users, SORT_REGULAR);
        }

        /*------- INITIALISATION ----------------------------------------------------------------------------------------*/

        /**
         * Load the translation file for current language.
         */
        public function load_textdomain()
        {
            $this->plugin->load_textdomain('cuarep', 'customer-area-extended-permissions');
        }
    }

// Make sure the addon is loaded
    new CUAR_ExtendedPermissionsAddOn();

endif; // if (!class_exists('CUAR_ExtendedPermissionsAddOn')) 
