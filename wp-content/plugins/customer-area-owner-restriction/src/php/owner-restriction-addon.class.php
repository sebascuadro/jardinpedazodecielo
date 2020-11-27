<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon.class.php');

require_once(dirname(__FILE__) . '/owner-restriction-admin-interface.class.php');

if (!class_exists('CUAR_OwnerRestrictionAddOn')) :

    /**
     * Add-on to allow setting user groups or user roles as owner of a private content
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_OwnerRestrictionAddOn extends CUAR_AddOn
    {

        protected static $HANDLED_OWNER_TYPES = ['glo', 'usr', 'grp', 'rol', 'mgrp', 'sgrp', 'prj'];

        public function __construct()
        {
            parent::__construct('owner-restriction');
        }

        public function get_addon_name()
        {
            return __('Owner Restrictions', 'cuaror');
        }

        public function run_addon($plugin)
        {
            $this->enable_licensing(CUAROR_STORE_ITEM_ID, CUAROR_STORE_ITEM_NAME, CUAROR_PLUGIN_FILE, CUAROR_PLUGIN_VERSION);
            $this->load_textdomain();

            add_filter('cuar/core/ownership/selectable-owner-types', [&$this, 'get_selectable_owner_types']);
            add_filter('cuar/core/ownership/validate-owners', [&$this, 'validate_owners'], 10, 2);

            foreach (self::$HANDLED_OWNER_TYPES as $type)
            {
                add_filter(
                    "cuar/core/ownership/selectable-owners?owner-type=$type",
                    [&$this, "get_selectable_owners_for_type_$type"],
                    10, 3
                );
            }

            // Init the admin interface if needed
            if (is_admin())
            {
                add_action('cuar/core/addons/after-init', [&$this, 'add_private_content_hooks']);
                add_filter('cuar/core/status/directories-to-scan', [&$this, 'add_hook_discovery_directory']);

                foreach (self::$HANDLED_OWNER_TYPES as $type)
                {
                    add_filter(
                        "cuar/owner-restrictions/selectable-restrictions?owner-type=$type",
                        [&$this, "get_available_restrictions_for_owner_type_$type"]
                    );
                }

                $this->admin_interface = new CUAR_OwnerRestrictionAdminInterface($plugin, $this);
            }
        }

        public function add_hook_discovery_directory($dirs)
        {
            $dirs[CUAROR_PLUGIN_DIR] = $this->get_addon_name();

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

        public function get_available_restrictions_for_owner_type_usr($arr)
        {
            $restrictions = [
                'multiple' => false,
                'options' => [
                    self::$NO_RESTRICTION => __('Any user', 'cuaror'),
                    self::$FULL_RESTRICTION => __('No user at all', 'cuaror'),
                    self::$RESTRICTION_USR_HIMSELF => __('Only himself', 'cuaror'),
                ],
            ];

            $mg_addon = $this->plugin->get_addon('managed-groups');
            if ($mg_addon != null)
            {
                $restrictions['options'][self::$RESTRICTION_USR_MANAGED] = __('Any member of the groups where the author is a manager', 'cuaror');
                $restrictions['options'][self::$RESTRICTION_USR_MANAGERS] = __('Any manager of the groups where the author is a member', 'cuaror');
            }

            global $wp_roles;
            if (!isset($wp_roles))
            {
                $wp_roles = new WP_Roles();
            }
            $all_roles = $wp_roles->role_objects;
            foreach ($all_roles as $role)
            {
                $role_label = CUAR_WordPressHelper::getRoleDisplayName($role->name);
                $restrictions['options'][self::$RESTRICTION_USR_WITH_ROLE . $role->name] = sprintf(__('Any users with the role: %s', 'cuaror'), $role_label);
            }

            return $restrictions;
        }

        public function get_available_restrictions_for_owner_type_glo($arr)
        {
            return [
                'multiple' => false,
                'options' => [
                    self::$NO_RESTRICTION => __('Any global rule', 'cuaror'),
                    self::$FULL_RESTRICTION => __('No global rule', 'cuaror'),
                ],
            ];
        }

        public function get_available_restrictions_for_owner_type_rol($arr)
        {
            $restrictions = [
                'multiple' => false,
                'options' => [
                    self::$NO_RESTRICTION => __('Any role', 'cuaror'),
                    self::$FULL_RESTRICTION => __('No role', 'cuaror'),
                ],
            ];

            global $wp_roles;
            if (!isset($wp_roles))
            {
                $wp_roles = new WP_Roles();
            }
            $all_roles = $wp_roles->role_objects;
            foreach ($all_roles as $role)
            {
                $role_label = CUAR_WordPressHelper::getRoleDisplayName($role->name);
                $restrictions['options'][self::$RESTRICTION_SINGLE_ROLE . $role->name] = sprintf(__('Only the %s role', 'cuaror'), $role_label);
            }

            return $restrictions;
        }

        public function get_available_restrictions_for_owner_type_grp($arr)
        {
            return [
                'multiple' => false,
                'options' => [
                    self::$NO_RESTRICTION => __('Any user group', 'cuaror'),
                    self::$FULL_RESTRICTION => __('No user group', 'cuaror'),
                    self::$RESTRICTION_GRP_MEMBER => __('Groups where the author is a member', 'cuaror'),
                ],
            ];
        }

        public function get_available_restrictions_for_owner_type_mgrp($arr)
        {
            return [
                'multiple' => false,
                'options' => [
                    self::$NO_RESTRICTION => __('Any group', 'cuaror'),
                    self::$FULL_RESTRICTION => __('No group', 'cuaror'),
                    self::$RESTRICTION_GRP_MANAGER_MEMBER => __('Groups where the author is a member or a manager', 'cuaror'),
                    self::$RESTRICTION_GRP_MANAGER => __('Groups where the author is a manager', 'cuaror'),
                    self::$RESTRICTION_GRP_MEMBER => __('Groups where the author is a member', 'cuaror'),
                ],
            ];
        }

        public function get_available_restrictions_for_owner_type_sgrp($arr)
        {
            return [
                'multiple' => false,
                'options' => [
                    self::$NO_RESTRICTION => __('Any group', 'cuaror'),
                    self::$FULL_RESTRICTION => __('No group', 'cuaror'),
                    self::$RESTRICTION_GRP_MEMBER => __('Groups where the author is a member', 'cuaror'),
                ],
            ];
        }

        public function get_available_restrictions_for_owner_type_prj($arr)
        {
            $restrictions = [
                'multiple' => false,
                'options' => [
                    self::$NO_RESTRICTION => __('Any project', 'cuaror'),
                    self::$FULL_RESTRICTION => __('No project', 'cuaror'),
                    self::$RESTRICTION_PRJ_ASSIGNED => __('Projects where the author is involved', 'cuaror'),
                ],
            ];

            /** @var CUAR_ProjectsAddOn $pj_addon */
            $pj_addon = $this->plugin->get_addon('projects');
            $roles = $pj_addon->settings()->get_project_roles();
            foreach ($roles as $role_id => $role_desc)
            {
                $key = self::project_restriction_option_key($role_id);
                $restrictions['options'][$key] = sprintf(__('Projects where the author is &laquo; %1$s &raquo;', 'cuaror'), strtolower($role_desc['label']));
            }

            return $restrictions;
        }

        public static function project_restriction_option_key($role)
        {
            return str_replace('{{role}}', $role, self::$RESTRICTION_PRJ_ACTOR);
        }

        public function get_restriction($current_user_role, $content_type, $owner_type)
        {
            return $this->plugin->get_option(self::get_restriction_option_id($content_type, $current_user_role, $owner_type));
        }

        public static function get_restriction_option_id($content_type, $role, $owner_type)
        {
            return sprintf(self::$OPTION_ID_RESTRICTION_FORMAT, $content_type, $role, $owner_type);
        }

        // OwnerRestriction options
        public static $OPTION_ID_RESTRICTION_FORMAT = 'cuar_or_restriction_ct_%s_ur_%s_ot_%s';

        /*------- RESTRICTIONS ------------------------------------------------------------------------------------------*/

        public function add_private_content_hooks()
        {
            global $wp_roles;
            if (!isset($wp_roles))
            {
                $wp_roles = new WP_Roles();
            }
            $all_roles = $wp_roles->role_objects;

            /** @var CUAR_PostOwnerAddOn $po_addon */
            $po_addon = $this->plugin->get_addon('post-owner');
            $owner_types = $po_addon->get_owner_types();

            $private_content_types = $this->plugin->get_content_post_types();

            foreach ($private_content_types as $content_type)
            {
                foreach ($all_roles as $role)
                {
                    foreach ($owner_types as $type => $owner_type_label)
                    {
                        $option_id = CUAR_OwnerRestrictionAddOn::get_restriction_option_id($content_type, $role->name, $type);
                        $value = $this->plugin->get_option($option_id);
                        if (!isset($value))
                        {
                            $this->plugin->update_option($option_id, CUAR_OwnerRestrictionAddOn::$NO_RESTRICTION);
                        }
                    }
                }
            }
        }

        public function get_selectable_owner_types($arr)
        {
            $content_type = $this->get_current_content_type();
            if ($content_type == null)
            {
                return null;
            }

            $current_user_role = $this->get_current_user_role();
            if ($current_user_role == null)
            {
                return null;
            }

            /** @var CUAR_PostOwnerAddOn $po_addon */
            $po_addon = $this->plugin->get_addon("post-owner");
            $all_owner_types = $po_addon->get_owner_types();

            $result = [];
            foreach ($all_owner_types as $type => $label)
            {
                $restriction = $this->get_restriction($current_user_role, $content_type, $type);
                if (isset($restriction) && $restriction == self::$FULL_RESTRICTION)
                {
                    continue;
                }
                $result[$type] = $label;
            }

            return $result;
        }

        public function get_selectable_owners_for_type_usr($arr, $search, $page, $only_ids = false)
        {
            $content_type = $this->get_current_content_type();
            if ($content_type == null)
            {
                return null;
            }

            $current_user_role = $this->get_current_user_role();
            if ($current_user_role == null)
            {
                return null;
            }

            $restriction = $this->get_restriction($current_user_role, $content_type, 'usr');
            if (!isset($restriction))
            {
                return null;
            }

            // Handle the restriction
            switch ($restriction)
            {
                case self::$FULL_RESTRICTION:
                    return [];

                case self::$NO_RESTRICTION:
                    return null;

                case self::$RESTRICTION_USR_MANAGERS:
                {
                    $mg_addon = $this->plugin->get_addon('managed-groups');
                    if ($mg_addon == null)
                    {
                        return [];
                    }

                    $users = $mg_addon->get_user_managers(get_current_user_id());

                    if ($only_ids)
                    {
                        return $users;
                    }

                    /** @var CUAR_PostOwnerAddOn $po_addon */
                    $po_addon = $this->plugin->get_addon('post-owner');
                    return $po_addon->ajax()->format_items_for_select2(
                        $this->map_users_for_ajax($users, 'usr', $po_addon),
                        $search,
                        $page
                    );
                }

                case self::$RESTRICTION_USR_MANAGED:
                {
                    $mg_addon = $this->plugin->get_addon('managed-groups');
                    if ($mg_addon == null)
                    {
                        return [];
                    }

                    $users = $mg_addon->get_users_managed_by_user(get_current_user_id());

                    if ($only_ids)
                    {
                        return $users;
                    }

                    /** @var CUAR_PostOwnerAddOn $po_addon */
                    $po_addon = $this->plugin->get_addon('post-owner');
                    return $po_addon->ajax()->format_items_for_select2(
                        $this->map_users_for_ajax($users, 'usr', $po_addon),
                        $search,
                        $page
                    );
                }

                case self::$RESTRICTION_USR_HIMSELF:
                {
                    if ($only_ids)
                    {
                        return [get_current_user_id()];
                    }

                    /** @var CUAR_PostOwnerAddOn $po_addon */
                    $po_addon = $this->plugin->get_addon('post-owner');
                    return $po_addon->ajax()->format_items_for_select2(
                        $this->map_users_for_ajax([get_current_user_id()], 'usr', $po_addon),
                        $search,
                        $page
                    );
                }
            }

            // More restrictions
            global $wp_roles;
            if (!isset($wp_roles))
            {
                $wp_roles = new WP_Roles();
            }
            $all_roles = $wp_roles->role_objects;
            foreach ($all_roles as $role)
            {
                if ($restriction == self::$RESTRICTION_USR_WITH_ROLE . $role->name)
                {
                    $users = get_users(['role' => $role->name, 'fields' => 'id']);

                    if ($only_ids)
                    {
                        return $users;
                    }

                    /** @var CUAR_PostOwnerAddOn $po_addon */
                    $po_addon = $this->plugin->get_addon('post-owner');
                    return $po_addon->ajax()->format_items_for_select2(
                        $this->map_users_for_ajax($users, 'usr', $po_addon),
                        $search,
                        $page
                    );
                }
            }

            return null;
        }

        public function get_selectable_owners_for_type_rol($arr, $search, $page, $only_ids = false)
        {
            $content_type = $this->get_current_content_type();
            if ($content_type == null)
            {
                return null;
            }

            $current_user_role = $this->get_current_user_role();
            if ($current_user_role == null)
            {
                return null;
            }

            $restriction = $this->get_restriction($current_user_role, $content_type, 'rol');
            if (!isset($restriction))
            {
                return null;
            }

            // Handle the restriction
            switch ($restriction)
            {
                case self::$FULL_RESTRICTION:
                    return [];

                case self::$NO_RESTRICTION:
                    return null;
            }

            // More restrictions
            global $wp_roles;
            if (!isset($wp_roles))
            {
                $wp_roles = new WP_Roles();
            }
            $all_roles = $wp_roles->role_names;
            foreach ($all_roles as $role => $name)
            {
                if ($restriction == self::$RESTRICTION_SINGLE_ROLE . $role)
                {
                    /** @var CUAR_PostOwnerAddOn $po_addon */
                    $po_addon = $this->plugin->get_addon('post-owner');

                    if ($only_ids)
                    {
                        return [$role];
                    }

                    return $po_addon->ajax()->format_items_for_select2(
                        [$role => $po_addon->get_owner_display_name('rol', $role)],
                        $search,
                        $page
                    );
                }
            }

            return null;
        }

        public function get_selectable_owners_for_type_glo($arr, $search, $page, $only_ids = false)
        {
            $content_type = $this->get_current_content_type();
            if ($content_type == null)
            {
                return null;
            }

            $current_user_role = $this->get_current_user_role();
            if ($current_user_role == null)
            {
                return null;
            }

            $restriction = $this->get_restriction($current_user_role, $content_type, 'glo');
            if (!isset($restriction))
            {
                return null;
            }

            // Handle the restriction
            switch ($restriction)
            {
                case self::$FULL_RESTRICTION:
                    return [];

                case self::$NO_RESTRICTION:
                    return null;
            }

            return null;
        }

        public function get_selectable_owners_for_type_grp($arr, $search, $page, $only_ids = false)
        {
            $content_type = $this->get_current_content_type();
            if ($content_type == null)
            {
                return null;
            }

            $current_user_role = $this->get_current_user_role();
            if ($current_user_role == null)
            {
                return null;
            }

            $restriction = $this->get_restriction($current_user_role, $content_type, 'grp');
            if (!isset($restriction))
            {
                return null;
            }

            // Handle the restriction
            switch ($restriction)
            {
                case self::$FULL_RESTRICTION:
                    return [];

                case self::$NO_RESTRICTION:
                    return null;

                case self::$RESTRICTION_GRP_MEMBER:
                {
                    /** @var CUAR_UserGroupAddOn $ug_addon */
                    $ug_addon = $this->plugin->get_addon('user-group');
                    if ($ug_addon == null)
                    {
                        return [];
                    }
                    $groups = $ug_addon->get_groups_of_user(get_current_user_id());

                    if ($only_ids)
                    {
                        return array_map(
                            static function ($group)
                            {
                                return $group->ID;
                            },
                            $groups);
                    }

                    /** @var CUAR_PostOwnerAddOn $po_addon */
                    $po_addon = $this->plugin->get_addon('post-owner');
                    return $po_addon->ajax()->format_items_for_select2(
                        $this->map_posts_for_ajax($groups, 'grp', $po_addon),
                        $search,
                        $page
                    );
                }
            }

            return null;
        }

        public function get_selectable_owners_for_type_mgrp($arr, $search, $page, $only_ids = false)
        {
            $content_type = $this->get_current_content_type();
            if ($content_type == null)
            {
                return null;
            }

            $current_user_role = $this->get_current_user_role();
            if ($current_user_role == null)
            {
                return null;
            }

            $restriction = $this->get_restriction($current_user_role, $content_type, 'mgrp');
            if (!isset($restriction))
            {
                return null;
            }

            // Handle the restriction
            switch ($restriction)
            {
                case self::$FULL_RESTRICTION:
                    return [];

                case self::$NO_RESTRICTION:
                    return null;

                case self::$RESTRICTION_GRP_MANAGER:
                {
                    /** @var CUAR_ManagedGroupsAddOn $mg_addon */
                    $mg_addon = $this->plugin->get_addon('managed-groups');
                    if ($mg_addon == null)
                    {
                        return [];
                    }

                    $groups = $mg_addon->get_groups_managed_by_user(get_current_user_id());

                    if ($only_ids)
                    {
                        return array_map(
                            static function ($group)
                            {
                                return $group->ID;
                            },
                            $groups);
                    }

                    /** @var CUAR_PostOwnerAddOn $po_addon */
                    $po_addon = $this->plugin->get_addon('post-owner');
                    return $po_addon->ajax()->format_items_for_select2(
                        $this->map_posts_for_ajax($groups, 'mgrp', $po_addon),
                        $search,
                        $page
                    );
                }

                case self::$RESTRICTION_GRP_MEMBER:
                {
                    /** @var CUAR_ManagedGroupsAddOn $mg_addon */
                    $mg_addon = $this->plugin->get_addon('managed-groups');
                    if ($mg_addon == null)
                    {
                        return [];
                    }

                    $groups = $mg_addon->get_groups_of_user(get_current_user_id());

                    if ($only_ids)
                    {
                        return array_map(
                            static function ($group)
                            {
                                return $group->ID;
                            },
                            $groups);
                    }

                    /** @var CUAR_PostOwnerAddOn $po_addon */
                    $po_addon = $this->plugin->get_addon('post-owner');
                    return $po_addon->ajax()->format_items_for_select2(
                        $this->map_posts_for_ajax($groups, 'mgrp', $po_addon),
                        $search,
                        $page
                    );
                }

                case self::$RESTRICTION_GRP_MANAGER_MEMBER:
                {
                    /** @var CUAR_ManagedGroupsAddOn $mg_addon */
                    $mg_addon = $this->plugin->get_addon('managed-groups');
                    if ($mg_addon == null)
                    {
                        return [];
                    }

                    $groups = array_merge(
                        $mg_addon->get_groups_managed_by_user(get_current_user_id()),
                        $mg_addon->get_groups_of_user(get_current_user_id())
                    );

                    if ($only_ids)
                    {
                        return array_map(
                            static function ($group)
                            {
                                return $group->ID;
                            },
                            $groups);
                    }

                    /** @var CUAR_PostOwnerAddOn $po_addon */
                    $po_addon = $this->plugin->get_addon('post-owner');
                    return $po_addon->ajax()->format_items_for_select2(
                        $this->map_posts_for_ajax($groups, 'mgrp', $po_addon),
                        $search,
                        $page
                    );
                }
            }

            return null;
        }

        public function get_selectable_owners_for_type_sgrp($arr, $search, $page, $only_ids = false)
        {
            $content_type = $this->get_current_content_type();
            if ($content_type == null)
            {
                return null;
            }

            $current_user_role = $this->get_current_user_role();
            if ($current_user_role == null)
            {
                return null;
            }

            $restriction = $this->get_restriction($current_user_role, $content_type, 'sgrp');
            if (!isset($restriction))
            {
                return null;
            }

            // Handle the restriction
            switch ($restriction)
            {
                case self::$FULL_RESTRICTION:
                    return [];

                case self::$NO_RESTRICTION:
                    return null;

                case self::$RESTRICTION_GRP_MEMBER:
                {
                    /** @var CUAR_SmartGroupsAddOn $sg_addon */
                    $sg_addon = $this->plugin->get_addon('smart-groups');
                    if ($sg_addon == null)
                    {
                        return [];
                    }

                    $groups = $sg_addon->get_groups_of_user(get_current_user_id());

                    if ($only_ids)
                    {
                        return array_map(
                            static function ($group)
                            {
                                return $group->ID;
                            },
                            $groups);
                    }

                    /** @var CUAR_PostOwnerAddOn $po_addon */
                    $po_addon = $this->plugin->get_addon('post-owner');
                    return $po_addon->ajax()->format_items_for_select2(
                        $this->map_posts_for_ajax($groups, 'sgrp', $po_addon),
                        $search,
                        $page
                    );
                }
            }

            return null;
        }

        public function get_selectable_owners_for_type_prj($arr, $search, $page, $only_ids = false)
        {
            $content_type = $this->get_current_content_type();
            if ($content_type == null)
            {
                return null;
            }

            $current_user_role = $this->get_current_user_role();
            if ($current_user_role == null)
            {
                return null;
            }

            $restriction = $this->get_restriction($current_user_role, $content_type, 'prj');
            if (!isset($restriction))
            {
                return null;
            }

            // Handle the restriction
            switch ($restriction)
            {
                case self::$FULL_RESTRICTION:
                    return [];

                case self::$NO_RESTRICTION:
                    return null;

                case self::$RESTRICTION_PRJ_ASSIGNED:
                {
                    /** @var CUAR_ProjectsAddOn $pj_addon */
                    $pj_addon = $this->plugin->get_addon('projects');
                    if ($pj_addon == null)
                    {
                        return [];
                    }

                    $projects = $pj_addon->get_projects_of_user(get_current_user_id());

                    if ($only_ids)
                    {
                        return array_map(
                            static function ($project)
                            {
                                return $project->ID;
                            },
                            $projects);
                    }

                    /** @var CUAR_PostOwnerAddOn $po_addon */
                    $po_addon = $this->plugin->get_addon('post-owner');
                    return $po_addon->ajax()->format_items_for_select2(
                        $this->map_posts_for_ajax($projects, 'prj', $po_addon),
                        $search,
                        $page
                    );
                }
            }

            // Additional logic for project roles
            /** @var CUAR_ProjectsAddOn $pj_addon */
            $pj_addon = $this->plugin->get_addon('projects');
            $roles = $pj_addon->settings()->get_project_roles();
            foreach ($roles as $role_id => $role_desc)
            {
                if ($restriction != self::project_restriction_option_key($role_id))
                {
                    continue;
                }

                $projects = $pj_addon->get_projects_where_user_is(get_current_user_id(), $role_id);

                if ($only_ids)
                {
                    return array_map(
                        static function ($project)
                        {
                            return $project->ID;
                        },
                        $projects);
                }

                /** @var CUAR_PostOwnerAddOn $po_addon */
                $po_addon = $this->plugin->get_addon('post-owner');
                return $po_addon->ajax()->format_items_for_select2(
                    $this->map_posts_for_ajax($projects, 'prj', $po_addon),
                    $search,
                    $page
                );
            }

            return null;
        }

        /**
         * @param array  $owner_ids
         * @param string $owner_type
         * @return array
         */
        public function validate_owners($owner_ids, $owner_type)
        {
            if (!in_array($owner_type, self::$HANDLED_OWNER_TYPES, true))
            {
                return $owner_ids;
            }

            $function_name = "get_selectable_owners_for_type_$owner_type";
            $allowed_ids = $this->$function_name(null, null, null, true);

            if ($allowed_ids === null || $owner_ids === null)
            {
                return $owner_ids;
            }

            return array_intersect($owner_ids, $allowed_ids);
        }

        public function get_current_user_role()
        {
            $current_user = wp_get_current_user();
            if (!($current_user instanceof WP_User))
            {
                return null;
            }

            $user_roles = $current_user->roles;
            if (empty($user_roles))
            {
                return null;
            }

            return $user_roles[0];
        }

        public function get_current_content_type()
        {
            // Find the current content type
            if (isset($_POST['cuar_post_type']))
            {
                return $_POST['cuar_post_type'];
            }
            if (isset($_GET['post_type']))
            {
                return $_GET['post_type'];
            }
            if (isset($_POST['content_type']))
            {
                return $_POST['content_type'];
            }
            if (isset($_GET['content_type']))
            {
                return $_GET['content_type'];
            }

            return null;
        }

        /**
         * @param array               $posts
         * @param string              $owner_type
         * @param CUAR_PostOwnerAddOn $po_addon
         * @return array
         */
        private function map_posts_for_ajax($posts, $owner_type, $po_addon)
        {
            $out = [];
            foreach ($posts as $p)
            {
                $out[$p->ID] = $po_addon->get_owner_display_name($owner_type, $p->ID);
            }
            asort($out);

            return $out;
        }

        /**
         * @param array               $users
         * @param string              $owner_type
         * @param CUAR_PostOwnerAddOn $po_addon
         * @return array
         */
        private function map_users_for_ajax($users, $owner_type, $po_addon)
        {
            $out = [];
            foreach ($users as $id)
            {
                $out[$id] = $po_addon->get_owner_display_name($owner_type, $id);
            }
            asort($out);

            return $out;
        }

        /*------- INITIALISATION ----------------------------------------------------------------------------------------*/

        /**
         * Load the translation file for current language.
         */
        public function load_textdomain()
        {
            $this->plugin->load_textdomain('cuaror', 'customer-area-owner-restriction');
        }

        /** @var CUAR_OwnerRestrictionAddOn */
        private $admin_interface;

        public static $NO_RESTRICTION = 'no_restriction';
        public static $FULL_RESTRICTION = 'full_restriction';
        public static $RESTRICTION_PRJ_ASSIGNED = 'prj_manager_participant';
        public static $RESTRICTION_PRJ_ACTOR = 'prj_{{role}}';
        public static $RESTRICTION_GRP_MANAGER_MEMBER = 'grp_manager_member';
        public static $RESTRICTION_GRP_MANAGER = 'grp_manager';
        public static $RESTRICTION_GRP_MEMBER = 'grp_member';
        public static $RESTRICTION_USR_WITH_ROLE = 'usr_with_rol_';
        public static $RESTRICTION_USR_MANAGERS = 'usr_managers';
        public static $RESTRICTION_USR_MANAGED = 'usr_managed';
        public static $RESTRICTION_USR_HIMSELF = 'usr_himself';
        public static $RESTRICTION_SINGLE_ROLE = 'single_role_';
    }

    // Make sure the addon is loaded
    new CUAR_OwnerRestrictionAddOn();

endif; // if (!class_exists('CUAR_OwnerRestrictionAddOn')) 
