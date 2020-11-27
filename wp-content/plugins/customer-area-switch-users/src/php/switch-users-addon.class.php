<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon.class.php');

if (!class_exists('CUAR_SwitchUsersAddOn')) :

    /**
     * Add-on to send switch-users on some events
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_SwitchUsersAddOn extends CUAR_AddOn
    {

        public function __construct()
        {
            parent::__construct('switch-users');
        }

        public function get_addon_name()
        {
            return __('Switch Users', 'cuarsu');
        }

        public function run_addon($plugin)
        {
            $this->enable_licensing(CUARSU_STORE_ITEM_ID, CUARSU_STORE_ITEM_NAME, CUARSU_PLUGIN_FILE, CUARSU_PLUGIN_VERSION);
            $this->load_textdomain();

            // Ajax
            add_action('wp_ajax_cuar_search_fake_identity', array(&$this, 'ajax_search_fake_identity'));

            // Init the admin interface if needed
            if (is_admin())
            {
                add_filter('cuar/core/permission-groups', array(&$this, 'get_configurable_capability_groups'), 100);

                add_filter('cuar/core/status/directories-to-scan', array(&$this, 'add_hook_discovery_directory'));

                // Settings
                add_action('cuar/core/settings/print-settings?tab=cuar_core', array(&$this, 'print_settings'), 10, 2);
                add_filter('cuar/core/settings/validate-settings?tab=cuar_core',
                    array(&$this, 'validate_options'), 10, 3);

            }
            else if (current_user_can('cuarsu_use_fake_identity'))
            {
                add_action('init', array(&$this, 'register_ajax_scripts'));

                add_filter('cuar/core/user-profile/view/override-user-id', array(&$this, 'get_fake_user_id'));
                add_filter('cuar/core/ownership/container/meta-query/override-owner-id',
                    array(&$this, 'get_fake_user_id'));
                add_filter('cuar/core/ownership/content/meta-query/override-owner-id',
                    array(&$this, 'get_fake_user_id'));
                add_filter('cuar/core/ownership/protect-single-post/override-user-id',
                    array(&$this, 'get_fake_user_id'));
                add_filter('cuar/private-content/conversations/query/default/override-user-id',
                    array(&$this, 'get_fake_user_id'));
                add_filter('cuar/private-content/conversations/query/started-by/override-user-id',
                    array(&$this, 'get_fake_user_id'));

                add_action('cuar/core/page/toolbar', array(&$this, 'add_fake_identity_selection_toolbar_group'), 9000);
                add_action('template_redirect', array(&$this, 'handle_fake_identity_selection'));
            }
        }

        public function add_hook_discovery_directory($dirs)
        {
            $dirs[CUARSU_PLUGIN_DIR] = $this->get_addon_name();

            return $dirs;
        }

        /*------- CUSTOMISATION OF THE PLUGIN SETTINGS PAGE --------------------------------------------------------------*/

        public function get_fake_identity_timeout()
        {
            return $this->plugin->get_option(self::$OPTION_FAKE_IDENTITY_TIMEOUT_SEC);
        }

        /**
         * Add our fields to the settings page
         *
         * @param CUAR_Settings $cuar_settings The settings class
         */
        public function print_settings($cuar_settings, $options_group)
        {
            add_settings_field(
                self::$OPTION_FAKE_IDENTITY_TIMEOUT_SEC,
                __('Switch Users Timeout', 'cuarsu'),
                array(&$cuar_settings, 'print_input_field'),
                CUAR_Settings::$OPTIONS_PAGE_SLUG,
                'cuar_general_settings',
                array(
                    'option_id' => self::$OPTION_FAKE_IDENTITY_TIMEOUT_SEC,
                    'type'      => 'text',
                    'after'     => ' <span> ' . __('(seconds)', 'cuarsu') . '</span>'
                                   . '<p class="description">'
                                   . __('If a user who can use a fake identity is inactive for more than that time, the fake identity will be cancelled and he will see his own customer area again. '
                                        . 'You can use any negative value if you want the fake identity to never expire automatically.', 'cuarsu')
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
         *
         * @return array
         */
        public function validate_options($validated, $cuar_settings, $input)
        {
            $cuar_settings->validate_int($input, $validated, self::$OPTION_FAKE_IDENTITY_TIMEOUT_SEC);

            return $validated;
        }

        // SwitchUsers options
        public static $OPTION_FAKE_IDENTITY_TIMEOUT_SEC = 'cuar_su_timeout';

        /*------- INITIALISATIONS ----------------------------------------------------------------------------------------*/

        public function register_ajax_scripts()
        {
            wp_register_script('cuar.switch-users',
                CUARSU_PLUGIN_URL . '/assets/frontend/js/customer-area-switch-users.min.js',
                array('cuar.frontend', 'jquery', 'jquery.select2'),
                CUARSU_PLUGIN_VERSION);
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

            $defaults[self::$OPTION_FAKE_IDENTITY_TIMEOUT_SEC] = 5 * 60;

            return $defaults;
        }

        public function get_configurable_capability_groups($capability_groups)
        {
            if (isset($capability_groups['cuar_general']))
            {
                if (!isset($capability_groups['cuar_general']['groups']['front-office']))
                {
                    $capability_groups['cuar_general']['groups']['front-office'] = array(
                        'group_name'   => __('Front-office', 'cuarsu'),
                        'capabilities' => array(),
                    );
                }

                $capability_groups['cuar_general']['groups']['front-office']['capabilities']['cuarsu_use_fake_identity'] = __("View someone else's Customer Area (Switch users)",
                    'cuarsu');
            }

            return $capability_groups;
        }

        /**
         * Load the translation file for current language.
         */
        public function load_textdomain()
        {
            $this->plugin->load_textdomain('cuarsu', 'customer-area-switch-users');
        }

        /*------- FAKE USER ID --------------------------------------------------------------------------------*/

        public function ajax_search_fake_identity()
        {
            $po_addon = $this->plugin->get_addon('post-owner');

            $po_addon->ajax()->check_nonce_query_param('cuar_search_fake_identity');
            $po_addon->ajax()->check_capability('cuarsu_use_fake_identity');

            $search = $po_addon->ajax()->get_query_param('search', '');
            $page = $po_addon->ajax()->get_query_param('page', 1);

            $extra_args = array('exclude' => array(get_current_user_id()));

            $result = $po_addon->ajax()->find_users($search, 'switch_users', $page, $extra_args);
            if ($page==1)
            {
                $result['results'] = array_merge(
                    array(array('id' => '-1', 'text' => __('Yourself', 'cuarsu'))),
                    $result['results']
                );
            }

            wp_send_json_success($result);
        }

        public function get_fake_user_id($user_id)
        {
            $this->check_fake_identity_timeout();
            if (!isset($_SESSION['cuarsu_fake_user_id']))
            {
                return $user_id;
            }

            return $_SESSION['cuarsu_fake_user_id'];
        }

        private function check_fake_identity_timeout()
        {
            if (!isset($_SESSION['cuarsu_fake_user_id']))
            {
                return;
            }

            $timeout = $this->get_fake_identity_timeout();
            if ($timeout <= 0)
            {
                return;
            }

            if ((time() - $_SESSION['cuarsu_fake_user_id_timestamp']) > $timeout)
            {
                unset($_SESSION['cuarsu_fake_user_id']);
            }

            $_SESSION['cuarsu_fake_user_id_timestamp'] = time();
        }

        private function set_fake_user_id($user_id)
        {
            if ($user_id <= 0)
            {
                unset($_SESSION['cuarsu_fake_user_id']);
            }
            else
            {
                $_SESSION['cuarsu_fake_user_id'] = $user_id;
            }

            $_SESSION['cuarsu_fake_user_id_timestamp'] = time();
        }

        /**
         * Prints the toolbar to select the fake identity
         */
        public function print_fake_identity_selection()
        {
            $this->plugin->enable_library('jquery.select2');
            wp_enqueue_script('cuar.switch-users');
            $current_fake_id = $this->get_fake_user_id(get_current_user_id());

            include($this->plugin->get_template_file_path(
                CUARSU_INCLUDES_DIR,
                "user-switcher.template.php",
                'templates'));
        }

        /**
         * Filter the_content to automatically prepend the toolbar to select the fake identity. To disable automatic printing, the theme can
         * declare support and print that toolbar by itself.
         *
         * @param array $groups The toolbar groups
         *
         * @return string The content prepended
         */
        public function add_fake_identity_selection_toolbar_group($groups)
        {
            $theme_support = get_theme_support('customer-area.switch-users.toolbar');
            if ($theme_support === true)
            {
                return $groups;
            }

            // Only on customer area pages
            /** @var CUAR_CustomerPagesAddOn $cp_addon */
            $cp_addon = $this->plugin->get_addon('customer-pages');
            if (!$cp_addon->is_customer_area_page())
            {
                return $groups;
            }

            // Exclude some pages
            $excluded_pages = $this->get_excluded_pages();
            $current_page = $cp_addon->get_customer_area_page_from_id();
            if ($current_page !== false && in_array($current_page->get_slug(), $excluded_pages))
            {
                return $groups;
            }

            ob_start();
            $this->print_fake_identity_selection();
            $switcher = ob_get_contents();
            ob_end_clean();

            $groups['switch-users'] = array(
                'type' => 'raw',
                'html' => $switcher,
            );

            return $groups;
        }

        public function handle_fake_identity_selection()
        {
            if (!isset($_POST['cuarsu_do_switch_identity']))
            {
                return;
            }
            if (!current_user_can('cuarsu_use_fake_identity'))
            {
                return;
            }
            if (!wp_verify_nonce($_POST['cuar_switch_user_nonce'], 'cuarsu_switch_identity'))
            {
                return;
            }

            $this->set_fake_user_id($_POST['cuarsu_fake_id']);
        }

        /**
         * @return array
         */
        private function get_excluded_pages()
        {
            return array(
                'payments-checkout',
                'payments-success',
                'payments-failure',
                'customer-new-private-page',
                'customer-update-private-page',
                'customer-new-private-file',
                'customer-update-private-file',
                'customer-new-conversation',
                'customer-update-conversation',
                'customer-new-tasklist',
                'customer-update-tasklist',
                'customer-new-project',
                'customer-update-project',
                'customer-account-edit',
            );
        }
    }

// Make sure the addon is loaded
    new CUAR_SwitchUsersAddOn();

endif; // if (!class_exists('CUAR_SwitchUsersAddOn')) 
