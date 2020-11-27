<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon.class.php');

require_once(dirname(__FILE__) . '/managed-groups-admin-interface.class.php');

if (!class_exists('CUAR_ManagedGroupsAddOn')) :

    /**
     * Add-on to allow using groups handled by a manager
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_ManagedGroupsAddOn extends CUAR_AddOn
    {

        public function __construct()
        {
            parent::__construct('managed-groups');
        }

        public function get_addon_name()
        {
            return __('Managed Groups', 'cuarmg');
        }

        public function run_addon($plugin)
        {
            $this->enable_licensing(CUARMG_STORE_ITEM_ID, CUARMG_STORE_ITEM_NAME, CUARMG_PLUGIN_FILE, CUARMG_PLUGIN_VERSION);
            $this->load_textdomain();

            add_action('init', array(&$this, 'register_custom_types'));
            add_action('delete_user', array(&$this, 'before_user_deleted'));

            add_filter('cuar/core/permission-groups', array(&$this, 'get_configurable_capability_groups'));
            add_filter('cuar/core/ownership/content/meta-query', array(&$this,
                                                                       'extend_private_posts_meta_query'), 10, 3);
            add_filter('cuar/core/ownership/owner-types', array(&$this, 'declare_new_owner_types'));
            add_filter('cuar/core/ownership/real-user-ids?owner-type=mgrp',
                array(&$this, 'get_post_owner_user_ids_from_mgrp'), 10, 2);
            add_filter('cuar/core/ownership/validate-post-ownership', array(&$this, 'is_user_owner_of_post'), 10, 5);
            add_filter('cuar/core/ajax/search/post-owners?owner-type=mgrp',
                array(&$this, 'get_printable_owners_for_type_mgrp'), 10, 3);
            add_filter('cuar/core/ownership/enable-multiple-select?owner-type=mgrp',
                array(&$this, 'enable_multiple_select_for_type_mgrp'), 10, 2);
            add_filter('cuar/core/ownership/saved-displayname', array(&$this, 'saved_post_owner_displayname'), 10, 4);
            add_filter('cuar/core/ownership/owner-display-name?owner-type=mgrp',
                array(&$this, 'get_owner_display_name'), 10, 2);

            add_action('cuar/core/admin/submenu-items?group=users', array(&$this, 'add_menu_items'), 14);
            add_filter('cuar/core/post-types/other', array(&$this, 'add_managed_post_type'), 10, 1);

            // Init the admin interface if needed
            if (is_admin())
            {
                $this->admin_interface = new CUAR_ManagedGroupsAdminInterface($plugin, $this);

                add_filter('cuar/core/status/directories-to-scan', array(&$this, 'add_hook_discovery_directory'));
            }
        }

        public function add_hook_discovery_directory($dirs)
        {
            $dirs[CUARMG_PLUGIN_DIR] = $this->get_addon_name();

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

            $defaults[self::$OPTION_MEMBER_ROLE] = 'cuar_any';
            $defaults[self::$OPTION_MANAGER_ROLE] = 'cuar_any';

            return $defaults;
        }

        /*------- ADMIN MENU --------------------------------------------------------------------------------------------*/

        /**
         * Add the menu item
         *
         * @param $submenus
         *
         * @return array
         */
        public function add_menu_items($submenus)
        {
            $post_type = get_post_type_object('cuar_managed_group');

            $item = array(
                'page_title' => __($post_type->labels->name, 'cuarmg'),
                'title'      => __($post_type->labels->name, 'cuarmg'),
                'slug'       => 'edit.php?post_type=' . $post_type->name,
                'function'   => null,
                'capability' => 'cuar_mg_read',
            );

            $item['children'] = array();
            $item['children'][] = array(
                'title' => sprintf(__('All %s', 'cuarmg'), strtolower(__($post_type->labels->name, 'cuarmg'))),
                'slug'  => 'list-' . $post_type->name,
                'href'  => admin_url('edit.php?post_type=' . $post_type->name),
            );
            $item['children'][] = array(
                'title' => sprintf(__('New %s', 'cuarmg'), strtolower(__($post_type->labels->singular_name, 'cuarmg'))),
                'slug'  => 'new-' . $post_type->name,
                'href'  => admin_url('post-new.php?post_type=' . $post_type->name),
            );

            $submenus[] = $item;

            return $submenus;
        }

        public function add_managed_post_type($post_types)
        {
            $post_types['cuar_managed_group'] = array(
                "label-singular"     => _x('Managed Group', 'cuar_managed_group', 'cuarmg'),
                "label-plural"       => _x('Managed Groups', 'cuar_managed_group', 'cuarmg'),
                "content-page-addon" => null,
                "type"               => "other",
            );

            return $post_types;
        }

        // ManagedGroups options
        public static $OPTION_MEMBER_ROLE = 'cuar_mg_member_role_filter';
        public static $OPTION_MANAGER_ROLE = 'cuar_mg_manager_role_filter';

        public function get_default_member_role()
        {
            return $this->plugin->get_option(self::$OPTION_MEMBER_ROLE);
        }

        public function get_default_manager_role()
        {
            return $this->plugin->get_option(self::$OPTION_MANAGER_ROLE);
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
            if ($owner_type != 'mgrp')
            {
                return $displayname;
            }

            $names = array();
            foreach ($owner_ids as $gid)
            {
                $names[] = $this->get_owner_display_name($displayname, $gid);
            }
            asort($names);

            return empty($names) ? $displayname : implode(", ", $names);
        }

        /**
         * @param $name
         * @param $owner_id
         * @return string
         */
        public function get_owner_display_name($name, $owner_id)
        {
            $title = get_the_title($owner_id);
            if ($title != null && !empty($title))
            {
                return $title;
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
         * @param string  $post_owner_id
         *
         * @return boolean true if the user owns the post
         */
        public function is_user_owner_of_post($initial_result, $post_id, $user_id, $post_owner_type, $post_owner_ids)
        {
            if ($initial_result)
            {
                return true;
            }

            if ($post_owner_type == 'mgrp')
            {
                $mg_addon = $this->plugin->get_addon('managed-groups');
                $user_groups = $mg_addon->get_groups_of_user_including_managed($user_id);

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

            return false;
        }

        /**
         * Print a select field with all user groups
         *
         * @param $response
         * @param $search
         * @param $page
         * @return array
         */
        public function get_printable_owners_for_type_mgrp($response, $search, $page)
        {
            // Allow 3rd party code to provide their own selection
            $items = apply_filters('cuar/core/ownership/selectable-owners?owner-type=mgrp', null, $search, $page);
            if ($items === null)
            {
                $items = $this->get_selectable_groups($search, $page);

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

        public function enable_multiple_select_for_type_mgrp($val)
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
            // For user groups
            $groups_meta_query = array();
            $user_groups = $this->get_groups_of_user_including_managed($user_id);

            if (!empty($user_groups) && is_array($user_groups))
            {
                foreach ($user_groups as $g)
                {
                    $groups_meta_query[] = $po_addon->get_owner_meta_query_component('mgrp', $g->ID);
                }
            }

            // Deal with all this
            return array_merge($base_meta_query, $groups_meta_query);
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
                'mgrp' => __('Managed Group', 'cuarmg'),
            );

            return array_merge($types, $new_types);
        }

        /**
         * Return all user IDs that belong to the given group
         *
         * @param array  $users   the initial users
         * @param string $role_id The role id
         *
         * @return array
         */
        public function get_post_owner_user_ids_from_mgrp($users, $group_ids)
        {
            $mg_addon = $this->plugin->get_addon('managed-groups');

            $all_users = $users;

            foreach ($group_ids as $group_id)
            {
                $users_for_group = $mg_addon->get_group_members($group_id);

                $all_users = array_merge($all_users, $users_for_group);
            }

            return array_unique($all_users);
        }

        /*------- FUNCTIONS TO ACCESS THE GROUPS ------------------------------------------------------------------------*/

        /**
         * Get all the user groups in the system
         *
         * @return array An array of posts
         */
        public function get_all_groups()
        {
            // Use WordPress to bring back the projects
            $groups = get_posts(array(
                'post_type'   => "cuar_managed_group",
                'numberposts' => -1,
            ));

            return $groups;
        }

        /**
         * Get the managed groups in the system corresponding to the search
         *
         * @param $search
         * @param $page
         * @return array An array of posts and a boolean
         */
        public function get_selectable_groups($search, $page)
        {
            /** @var CUAR_PostOwnerAddOn $po_addon */
            $po_addon = $this->plugin->get_addon('post-owner');
            return $po_addon->ajax()->find_posts($search, 'managed-groups', $page, array(
                'post_type' => 'cuar_managed_group',
            ));
        }

        /**
         * Get the groups to which a user belongs
         *
         * @param int $user_id The user we are interested about
         *
         * @return array An array of posts
         */
        public function get_groups_of_user($user_id)
        {
            $groups = get_posts(array(
                'post_type'   => "cuar_managed_group",
                'numberposts' => -1,
                'orderby'     => 'title',
                'order'       => 'ASC',
                'meta_query'  => array(
                    array(
                        'key'     => self::$META_GROUP_MEMBERS,
                        'value'   => '|' . $user_id . '|',
                        'compare' => 'LIKE',
                    ),
                ),
            ));

            return $groups;
        }

        /**
         * Get the groups to which a user belongs
         *
         * @param int $user_id The user we are interested about
         *
         * @return array An array of posts
         */
        public function get_groups_of_user_including_managed($user_id)
        {
            $groups = get_posts(array(
                'post_type'   => "cuar_managed_group",
                'numberposts' => -1,
                'orderby'     => 'title',
                'order'       => 'ASC',
                'meta_query'  => array(
                    'relation' => 'OR',
                    array(
                        'key'     => self::$META_GROUP_MEMBERS,
                        'value'   => '|' . $user_id . '|',
                        'compare' => 'LIKE',
                    ),
                    array(
                        'key'     => self::$META_GROUP_MANAGERS,
                        'value'   => '|' . $user_id . '|',
                        'compare' => 'LIKE',
                    ),
                ),
            ));

            return $groups;
        }

        /**
         * Persist the group members
         *
         * @param int   $group_id
         * @param array $user_ids
         */
        public function set_group_members($group_id, $user_ids)
        {
            update_post_meta($group_id, self::$META_GROUP_MEMBERS, $this->encode_members($user_ids));
        }

        /**
         * Retrieve the group members
         *
         * @param int $group_id
         *
         * @return array the user ids
         */
        public function get_group_members($group_id)
        {
            return $this->decode_members(get_post_meta($group_id, self::$META_GROUP_MEMBERS, true));
        }

        /**
         * Add a member to a group (the group must exist)
         *
         * @param unknown $user_id
         * @param unknown $group_id
         */
        public function add_member_to_group($user_id, $group_id)
        {
            $members = $this->get_group_members($group_id);

            // Already a member
            if (in_array($user_id, $members))
            {
                return;
            }

            $members[] = $user_id;
            $this->set_group_members($group_id, $members);
        }

        /**
         * Remove a member from a group (the group must exist)
         *
         * @param unknown $user_id
         * @param unknown $group_id
         */
        public function remove_member_from_group($user_id, $group_id)
        {
            $members = $this->get_group_members($group_id);

            // Not a member
            if (!in_array($user_id, $members))
            {
                return;
            }

            $members = array_diff($members, array($user_id));
            $this->set_group_members($group_id, $members);
        }

        /**
         * Get the groups managed by a user
         *
         * @param int $user_id The user we are interested about
         *
         * @return array An array of posts
         */
        public function get_groups_managed_by_user($user_id)
        {
            $groups = get_posts(array(
                'post_type'   => "cuar_managed_group",
                'numberposts' => -1,
                'orderby'     => 'title',
                'order'       => 'ASC',
                'meta_query'  => array(
                    array(
                        'key'     => self::$META_GROUP_MANAGERS,
                        'value'   => '|' . $user_id . '|',
                        'compare' => 'LIKE',
                    ),
                ),
            ));

            return $groups;
        }

        /**
         * Persist the group managers
         *
         * @param int   $group_id
         * @param array $user_ids
         */
        public function set_group_managers($group_id, $user_ids)
        {
            update_post_meta($group_id, self::$META_GROUP_MANAGERS, $this->encode_members($user_ids));
        }

        /**
         * Retrieve the group managers
         *
         * @param int $group_id
         *
         * @return array the user ids
         */
        public function get_group_managers($group_id)
        {
            return $this->decode_members(get_post_meta($group_id, self::$META_GROUP_MANAGERS, true));
        }

        /**
         * Add a manager to a group (the group must exist)
         *
         * @param unknown $user_id
         * @param unknown $group_id
         */
        public function add_manager_to_group($user_id, $group_id)
        {
            $managers = $this->get_group_managers($group_id);

            // Already a manager
            if (in_array($user_id, $managers))
            {
                return;
            }

            $managers[] = $user_id;
            $this->set_group_managers($group_id, $managers);
        }

        /**
         * Remove a manager from a group (the group must exist)
         *
         * @param unknown $user_id
         * @param unknown $group_id
         */
        public function remove_manager_from_group($user_id, $group_id)
        {
            $managers = $this->get_group_managers($group_id);

            // Not a manager
            if (!in_array($user_id, $managers))
            {
                return;
            }

            $managers = array_diff($managers, array($user_id));
            $this->set_group_managers($group_id, $managers);
        }

        /**
         * Get the users managed by another user
         *
         * @param int $user_id The user we are interested about
         *
         * @return array An array of posts
         */
        public function get_users_managed_by_user($manager_id)
        {
            $manager_groups = $this->get_groups_managed_by_user($manager_id);

            $users = array();

            foreach ($manager_groups as $group)
            {
                $users = array_merge($users, $this->get_group_members($group->ID));
            }

            return $users;
        }

        /**
         * Get the users managing another user
         *
         * @param int $user_id The user we are interested about
         *
         * @return array An array of posts
         */
        public function get_user_managers($user_id)
        {
            $manager_groups = $this->get_groups_of_user($user_id);

            $users = array();

            foreach ($manager_groups as $group)
            {
                $users = array_merge($users, $this->get_group_managers($group->ID));
            }

            return $users;
        }

        /**
         * Decode an array of users/user groups as stored in the meta table. We store users in an array. The array items
         * are formed by concatenation of the role and the id (separated by |). For instance:
         * [ 'tpress_group_project_coworker|12,14,34,54|', 'tpress_group_project_leader|22,13|' ]
         */
        private function decode_members($raw)
        {
            if (!isset($raw) || $raw == null || empty($raw))
            {
                return array();
            }

            return array_filter(explode('|', $raw));
        }

        /**
         * Encode an array of users/user groups for storage in the meta table. We expect a dictionnary where the keys are
         * user groups and values are arrays of user IDs.
         */
        private static function encode_members($user_ids)
        {
            if (!isset($user_ids) || $user_ids == null || empty($user_ids))
            {
                $user_ids = array();
            }

            $raw = '|' . implode('|', array_filter($user_ids)) . '|';

            return $raw;
        }

        private static $META_GROUP_MEMBERS = 'cuar_group_members';
        private static $META_GROUP_MANAGERS = 'cuar_group_managers';

        /*------- GENERAL MAINTAINANCE FUNCTIONS ------------------------------------------------------------------------*/

        /**
         * Remove a user from a group if he is deleted
         *
         * @param int $user_id
         */
        public function before_user_deleted($user_id)
        {
            $groups = $this->get_groups_of_user($user_id);

            foreach ($groups as $group)
            {
                $this->remove_manager_from_group($user_id, $group->ID);
                $this->remove_member_from_group($user_id, $group->ID);
            }
        }

        /*------- INITIALISATION ----------------------------------------------------------------------------------------*/

        /**
         * Load the translation file for current language.
         */
        public function load_textdomain()
        {
            $this->plugin->load_textdomain('cuarmg', 'customer-area-managed-groups');
        }

        public function get_configurable_capability_groups($capability_groups)
        {
            $capability_groups['cuar_managed_group'] = array(
                'label'  => __('Managed Groups', 'cuarmg'),
                'groups' => array(
                    'back-office' => array(
                        'group_name'   => __('Back-office', 'cuarmg'),
                        'capabilities' => array(
                            'cuar_mg_edit'         => __('Create/Edit managed groups', 'cuarmg'),
                            'cuar_mg_delete'       => __('Delete managed groups', 'cuarmg'),
                            'cuar_mg_read'         => __('Access managed groups', 'cuarmg'),
                            'cuar_mg_view_profile' => __('View the groups from a user profile', 'cuarmg'),
                            'cuar_mg_edit_profile' => __('Edit the groups from a user profile', 'cuarmg'),
                        ),
                    ),
                ),
            );

            return $capability_groups;
        }

        /**
         * Register the custom post type for files and the associated taxonomies
         */
        public function register_custom_types()
        {
            $labels = array(
                'name'               => _x('Managed Groups', 'cuar_managed_group', 'cuarmg'),
                'singular_name'      => _x('Managed Group', 'cuar_managed_group', 'cuarmg'),
                'add_new'            => _x('Add New', 'cuar_managed_group', 'cuarmg'),
                'add_new_item'       => _x('Add New Managed Group', 'cuar_managed_group', 'cuarmg'),
                'edit_item'          => _x('Edit Managed Group', 'cuar_managed_group', 'cuarmg'),
                'new_item'           => _x('New Managed Group', 'cuar_managed_group', 'cuarmg'),
                'view_item'          => _x('View Managed Group', 'cuar_managed_group', 'cuarmg'),
                'search_items'       => _x('Search Managed Groups', 'cuar_managed_group', 'cuarmg'),
                'not_found'          => _x('No user groups found', 'cuar_managed_group', 'cuarmg'),
                'not_found_in_trash' => _x('No user groups found in Trash', 'cuar_managed_group', 'cuarmg'),
                'parent_item_colon'  => _x('Parent Managed Group:', 'cuar_managed_group', 'cuarmg'),
                'menu_name'          => _x('Managed Groups', 'cuar_managed_group', 'cuarmg'),
            );

            $args = array(
                'labels'              => $labels,
                'hierarchical'        => false,
                'supports'            => array('title'),
                'taxonomies'          => array(),
                'public'              => false,
                'show_ui'             => true,
                'show_in_menu'        => false,
                'show_in_nav_menus'   => false,
                'publicly_queryable'  => false,
                'exclude_from_search' => true,
                'has_archive'         => false,
                'can_export'          => false,
                'rewrite'             => false,
                'capabilities'        => array(
                    'edit_post'          => 'cuar_mg_edit',
                    'edit_posts'         => 'cuar_mg_edit',
                    'edit_others_posts'  => 'cuar_mg_edit',
                    'publish_posts'      => 'cuar_mg_edit',
                    'read_post'          => 'cuar_mg_read',
                    'read_private_posts' => 'cuar_mg_read',
                    'delete_post'        => 'cuar_mg_delete',
                    'delete_posts'       => 'cuar_mg_delete',
                ),
            );

            register_post_type('cuar_managed_group',
                apply_filters('cuar/content-container/managed-groups/register-post-type-args', $args));
        }

        /** @var CUAR_ManagedGroupsAddOn */
        private $admin_interface;
    }

// Make sure the addon is loaded
    new CUAR_ManagedGroupsAddOn();

endif; // if (!class_exists('CUAR_ManagedGroupsAddOn')) 
