<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon.class.php');

if ( !class_exists('CUAR_NotificationsAddOn')) :

    /**
     * Add-on to send notifications on some events
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_NotificationsAddOn extends CUAR_AddOn
    {
        /** @var CUAR_NotificationsPlaceholderHelper */
        private $placeholder_helper;

        /** @var CUAR_NotificationsHooksHelper */
        private $hooks_helper;

        /** @var CUAR_NotificationsSettingsHelper */
        private $settings_helper;

        /** @var CUAR_NotificationsMailerHelper */
        private $mailer;

        /** @var CUAR_NotificationsLogger */
        private $logger;

        /** @var CUAR_NotificationsAddOn */
        private $admin_interface;

        /**
         * Constructor.
         */
        public function __construct()
        {
            parent::__construct('notifications');
        }

        public function get_addon_name()
        {
            return __('Notifications', 'cuarno');
        }

        public function before_run($plugin)
        {
            parent::before_run($plugin);
            $this->hooks_helper = new CUAR_NotificationsHooksHelper($plugin, $this);
        }

        public function run_addon($plugin)
        {
            $this->enable_licensing(CUARNO_STORE_ITEM_ID, CUARNO_STORE_ITEM_NAME, CUARNO_PLUGIN_FILE, CUARNO_PLUGIN_VERSION);
            $this->load_textdomain();

            $this->mailer = new CUAR_NotificationsMailerHelper($plugin, $this);
            $this->settings_helper = new CUAR_NotificationsSettingsHelper($plugin, $this);
            $this->placeholder_helper = new CUAR_NotificationsPlaceholderHelper($plugin, $this);
            $this->logger = new CUAR_NotificationsLogger($plugin, $this);

            // Init the admin interface if needed
            if (is_admin()) {
                $this->admin_interface = new CUAR_NotificationsAdminInterface($plugin, $this);
                add_filter('cuar/core/status/directories-to-scan', array(&$this, 'add_hook_discovery_directory'));
            }
        }

        public function add_hook_discovery_directory($dirs)
        {
            $dirs[CUARNO_PLUGIN_DIR] = $this->get_addon_name();

            return $dirs;
        }

        /**
         * Load the translation file for current language.
         */
        public function load_textdomain()
        {
            $this->plugin->load_textdomain('cuarno', 'customer-area-notifications');
        }

        /*------- SETTINGS ACCESSORS ------------------------------------------------------------------------------------*/

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
            $defaults = CUAR_NotificationsSettingsHelper::set_default_options($defaults);

            return $defaults;
        }

        public function send_test_email()
        {
            $this->mailer->send_notification(get_current_user_id(), 'test');
        }

        public function get_available_placeholders()
        {
            return $this->placeholder_helper->get_available_placeholders();
        }

        public function mailer()
        {
            return $this->mailer;
        }

        public function settings()
        {
            return $this->settings_helper;
        }
    }

    // Make sure the addon is loaded
    new CUAR_NotificationsAddOn();

endif; // if (!class_exists('CUAR_NotificationsAddOn')) 
