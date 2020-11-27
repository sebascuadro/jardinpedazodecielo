<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon.class.php');
require_once(dirname(__FILE__) . '/smart-groups-admin-interface.class.php');

/**
 * Add-on to allow using groups handled by a manager
 *
 * @author Vincent Prat @ MarvinLabs
 */
class CUAR_SmartGroupsAddOn extends CUAR_AddOn
{

    public function __construct()
    {
        parent::__construct('smart-groups');
    }

    public function get_addon_name()
    {
        return __('Smart Groups', 'cuarsg');
    }

    public function run_addon($plugin)
    {
        $this->enable_licensing(CUARSG_STORE_ITEM_ID, CUARSG_STORE_ITEM_NAME, CUARSG_PLUGIN_FILE, CUARSG_PLUGIN_VERSION);
        $this->load_textdomain();

        add_action('init', array(&$this, 'register_custom_types'));

        add_filter('cuar/core/permission-groups', array(&$this, 'get_configurable_capability_groups'));
        add_filter('cuar/core/ownership/content/meta-query', array(&$this, 'extend_private_posts_meta_query'), 10, 3);
        add_filter('cuar/core/ownership/owner-types', array(&$this, 'declare_new_owner_types'));
        add_filter('cuar/core/ownership/real-user-ids?owner-type=sgrp',
            array(&$this, 'get_post_owner_user_ids_from_sgrp'), 10, 2);
        add_filter('cuar/core/ownership/validate-post-ownership', array(&$this, 'is_user_owner_of_post'), 10, 5);
        add_filter('cuar/core/ajax/search/post-owners?owner-type=sgrp',
            array(&$this, 'get_printable_owners_for_type_sgrp'), 10, 3);
        add_filter('cuar/core/ownership/enable-multiple-select?owner-type=sgrp',
            array(&$this, 'enable_multiple_select_for_type_sgrp'), 10, 2);
        add_filter('cuar/core/ownership/saved-displayname', array(&$this, 'saved_post_owner_displayname'), 10, 4);
        add_filter('cuar/core/ownership/owner-display-name?owner-type=sgrp',
            array(&$this, 'get_owner_display_name'), 10, 2);

        add_action('cuar/core/admin/submenu-items?group=users', array(&$this, 'add_menu_items'), 14);
        add_filter('cuar/core/post-types/other', array(&$this, 'add_managed_post_type'), 10, 1);

        // Init the admin interface if needed
        if (is_admin())
        {
            $this->admin_interface = new CUAR_SmartGroupsAdminInterface($plugin, $this);
            add_filter('cuar/core/status/directories-to-scan', array(&$this, 'add_hook_discovery_directory'));
        }
    }

    public function add_hook_discovery_directory($dirs)
    {
        $dirs[CUARSG_PLUGIN_DIR] = $this->get_addon_name();

        return $dirs;
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
        $post_type = get_post_type_object('cuar_smart_group');

        $item = array(
            'page_title' => __($post_type->labels->name, 'cuarsg'),
            'title'      => __($post_type->labels->name, 'cuarsg'),
            'slug'       => 'edit.php?post_type=' . $post_type->name,
            'function'   => null,
            'capability' => 'cuar_sg_read',
        );

        $item['children'] = array();
        $item['children'][] = array(
            'title' => sprintf(__('All %s', 'cuarsg'), strtolower(__($post_type->labels->name, 'cuarsg'))),
            'slug'  => 'list-' . $post_type->name,
            'href'  => admin_url('edit.php?post_type=' . $post_type->name),
        );
        $item['children'][] = array(
            'title' => sprintf(__('New %s', 'cuarsg'), strtolower(__($post_type->labels->singular_name, 'cuarsg'))),
            'slug'  => 'new-' . $post_type->name,
            'href'  => admin_url('post-new.php?post_type=' . $post_type->name),
        );

        $submenus[] = $item;

        return $submenus;
    }

    public function add_managed_post_type($post_types)
    {
        $post_types['cuar_smart_group'] = array(
            "label-singular"     => _x('Smart Group', 'cuar_smart_group', 'cuarsg'),
            "label-plural"       => _x('Smart Groups', 'cuar_smart_group', 'cuarsg'),
            "content-page-addon" => null,
            "type"               => "other",
        );

        return $post_types;
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
        if ($owner_type != 'sgrp')
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
     * @param string  $post_owner_ids
     *
     * @return boolean true if the user owns the post
     */
    public function is_user_owner_of_post($initial_result, $post_id, $user_id, $post_owner_type, $post_owner_ids)
    {
        if ($initial_result)
        {
            return true;
        }

        if ($post_owner_type == 'sgrp')
        {
            $user_groups = $this->get_groups_of_user($user_id);

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
    public function get_printable_owners_for_type_sgrp($response, $search, $page)
    {
        // Allow 3rd party code to provide their own selection
        $items = apply_filters('cuar/core/ownership/selectable-owners?owner-type=sgrp', null, $search, $page);
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

    /**
     * Shall we enable multiple owner selection for this type?
     */
    public function enable_multiple_select_for_type_sgrp($val)
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
        $user_groups = $this->get_groups_of_user($user_id);

        if (!empty($user_groups) && is_array($user_groups))
        {
            foreach ($user_groups as $g)
            {
                $groups_meta_query[] = $po_addon->get_owner_meta_query_component('sgrp', $g->ID);
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
            'sgrp' => __('Smart Group', 'cuarsg'),
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
    public function get_post_owner_user_ids_from_sgrp($users, $group_ids)
    {
        $all_users = $users;

        foreach ($group_ids as $group_id)
        {
            $users_for_group = $this->get_group_members($group_id);
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
            'post_type'   => "cuar_smart_group",
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
        return $po_addon->ajax()->find_posts($search, 'smart-groups', $page, array(
            'post_type' => 'cuar_smart_group',
        ));
    }


    /**
     * Get all users in group
     *
     * @return array IDs of the users in group or full objects
     */
    public function get_group_members($group_id, $only_ids = true)
    {
        $sc = $this->get_search_criteria($group_id);
        $mc = $this->get_meta_criteria($group_id);

        // If no criteria is enabled, we send an empty array
        if ($sc['enabled'] == 0 && $mc['enabled'] == 0)
        {
            return array();
        }

        $args = array(
            'fields'      => ($only_ids ? 'ID' : 'all_with_meta'),
            'count_total' => false,
        );

        // Search criteria
        if ($sc['enabled'] == 1)
        {
            $args['search'] = $sc['query'];
            $args['search_columns'] = $sc['fields'];
        }

        // meta data
        if ($mc['enabled'] == 1)
        {
            $mq = array('relation' => $mc['relation']);

            foreach ($mc['items'] as $q)
            {
                $mq[] = $q;
            }

            $args['meta_query'] = $mq;
        }

        $query = new WP_User_Query($args);

        return $query->get_results();
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
        $groups = $this->get_all_groups();
        $user = new WP_User($user_id);
        $result = array();

        foreach ($groups as $group)
        {
            if ($this->is_user_in_group($group, $user))
            {
                $result[] = $group;
            }
        }

        return $result;
    }

    /**
     * Returns true if the user matches the criteria of the smart group
     */
    public function is_user_in_group($group, $user)
    {
        $users_for_group = $this->get_group_members($group->ID);

        return in_array($user->ID, $users_for_group);
    }

    /**
     * Get the criteria for search
     *
     * @param int $group_id The group id
     *
     * @return array
     */
    public function get_search_criteria($group_id)
    {
        $sc = get_post_meta($group_id, self::$META_CRITERIA_SEARCH, true);
        if ( !$sc) {
            $sc = [];
        }

        if (!isset($sc['enabled']))
        {
            $sc['enabled'] = 0;
        }
        if (!isset($sc['query']))
        {
            $sc['query'] = '';
        }
        if (!isset($sc['fields']))
        {
            $sc['fields'] = array();
        }

        return $sc;
    }

    /**
     * Enable the search criteria for a group
     *
     * @param int    $group_id
     * @param string $query
     * @param array  $fields
     *
     * @return array errors if any
     */
    public function enable_search_criteria($group_id, $query, $fields)
    {
        $errors = array();
        if (empty($query))
        {
            $errors[] = __('The search query must not be empty', 'cuarsg');
        }
        if (empty($fields))
        {
            $errors[] = __('You must pick at least 1 search field', 'cuarsg');
        }

        if (!empty($errors))
        {
            return $errors;
        }

        $sc = array(
            'enabled' => 1,
            'query'   => $query,
            'fields'  => $fields,
        );
        update_post_meta($group_id, self::$META_CRITERIA_SEARCH, $sc);

        return $errors;
    }

    /**
     * Disable the search criteria for a group
     *
     * @param int $group_id
     */
    public function disable_search_criteria($group_id)
    {
        $sc = array(
            'enabled' => 0,
            'query'   => '',
            'fields'  => array(),
        );
        update_post_meta($group_id, self::$META_CRITERIA_SEARCH, $sc);
    }

    /**
     * Disable the meta criteria for a group
     *
     * @param int    $group_id
     * @param string $relation
     * @param array  $items
     *
     * @return array
     */
    public function enable_meta_criteria($group_id, $relation, $items)
    {
        $errors = array();
        if (empty($relation) || !in_array($relation, array('OR', 'AND')))
        {
            $errors[] = __('The relation must be either OR or AND', 'cuarsg');
        }
        if (empty($items))
        {
            $errors[] = __('You must enter at least 1 meta field', 'cuarsg');
        }
        foreach ($items as $id => $i)
        {
            if (!in_array($i['compare'],
                array('=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN',
                      'EXISTS', 'NOT EXISTS'))
            )
            {
                $errors[] = sprintf(__('Invalid operator for item %s', 'cuarsg'), $id);
            }

            if (empty($i['key']))
            {
                $errors[] = sprintf(__('The item %s cannot have an empty key', 'cuarsg'), $id);
            }

            if (empty($i['value']))
            {
                $errors[] = sprintf(__('The item %s cannot have an empty value', 'cuarsg'), $id);
            }
        }

        if (!empty($errors))
        {
            return $errors;
        }

        $mc = array(
            'enabled'  => 1,
            'relation' => $relation,
            'items'    => $items,
        );
        update_post_meta($group_id, self::$META_CRITERIA_META, $mc);

        return $errors;
    }

    /**
     * Disable the meta criteria for a group
     *
     * @param int $group_id
     */
    public function disable_meta_criteria($group_id)
    {
        $mc = array(
            'enabled'  => 0,
            'relation' => 'OR',
            'items'    => array(),
        );
        update_post_meta($group_id, self::$META_CRITERIA_META, $mc);
    }

    /**
     * Get the meta criteria for a group
     *
     * @param int $group_id
     *
     * @return array
     */
    public function get_meta_criteria($group_id)
    {
        $mc = get_post_meta($group_id, self::$META_CRITERIA_META, true);
        if ( !$mc) {
            $mc = [];
        }

        if (!isset($mc['enabled']))
        {
            $mc['enabled'] = 0;
        }
        if (!isset($mc['relation']))
        {
            $mc['relation'] = 'OR';
        }
        if (!isset($mc['items']))
        {
            $mc['items'] = array();
        }

        return $mc;
    }

    private static $META_CRITERIA_SEARCH = 'cuar_criteria_search';
    private static $META_CRITERIA_META = 'cuar_criteria_meta';

    /*------- GENERAL MAINTAINANCE FUNCTIONS ------------------------------------------------------------------------*/


    /*------- INITIALISATION ----------------------------------------------------------------------------------------*/

    /**
     * Load the translation file for current language.
     */
    public function load_textdomain()
    {
        $this->plugin->load_textdomain('cuarsg', 'customer-area-smart-groups');
    }

    public function get_configurable_capability_groups($capability_groups)
    {
        $capability_groups['cuar_smart_group'] = array(
            'label'  => __('Smart Groups', 'cuarsg'),
            'groups' => array(
                'back-office' => array(
                    'group_name'   => __('Back-office', 'cuarsg'),
                    'capabilities' => array(
                        'cuar_sg_edit'         => __('Create/Edit smart groups', 'cuarsg'),
                        'cuar_sg_delete'       => __('Delete smart groups', 'cuarsg'),
                        'cuar_sg_read'         => __('Access smart groups', 'cuarsg'),
                        'cuar_sg_view_profile' => __('View the groups from a user profile', 'cuarsg'),
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
            'name'               => _x('Smart Groups', 'cuar_smart_group', 'cuarsg'),
            'singular_name'      => _x('Smart Group', 'cuar_smart_group', 'cuarsg'),
            'add_new'            => _x('Add New', 'cuar_smart_group', 'cuarsg'),
            'add_new_item'       => _x('Add New Smart Group', 'cuar_smart_group', 'cuarsg'),
            'edit_item'          => _x('Edit Smart Group', 'cuar_smart_group', 'cuarsg'),
            'new_item'           => _x('New Smart Group', 'cuar_smart_group', 'cuarsg'),
            'view_item'          => _x('View Smart Group', 'cuar_smart_group', 'cuarsg'),
            'search_items'       => _x('Search Smart Groups', 'cuar_smart_group', 'cuarsg'),
            'not_found'          => _x('No smart groups found', 'cuar_smart_group', 'cuarsg'),
            'not_found_in_trash' => _x('No smart groups found in Trash', 'cuar_smart_group', 'cuarsg'),
            'parent_item_colon'  => _x('Parent Smart Group:', 'cuar_smart_group', 'cuarsg'),
            'menu_name'          => _x('Smart Groups', 'cuar_smart_group', 'cuarsg'),
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
                'edit_post'          => 'cuar_sg_edit',
                'edit_posts'         => 'cuar_sg_edit',
                'edit_others_posts'  => 'cuar_sg_edit',
                'publish_posts'      => 'cuar_sg_edit',
                'read_post'          => 'cuar_sg_read',
                'read_private_posts' => 'cuar_sg_read',
                'delete_post'        => 'cuar_sg_delete',
                'delete_posts'       => 'cuar_sg_delete',
            ),
        );

        register_post_type('cuar_smart_group',
            apply_filters('cuar/content-container/smart-groups/register-post-type-args', $args));
    }

    /** @var CUAR_SmartGroupsAddOn */
    private $admin_interface;
}

// Make sure the addon is loaded
new CUAR_SmartGroupsAddOn();