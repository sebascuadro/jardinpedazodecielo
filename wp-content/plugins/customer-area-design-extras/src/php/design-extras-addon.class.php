<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon.class.php');

if ( !class_exists('CUAR_DesignExtrasAddOn')) :

    /**
     * Add-on to provide some stylesheets for master-skin
     *
     * @author Thomas Lartaud @ MarvinLabs
     */
    class CUAR_DesignExtrasAddOn extends CUAR_AddOn
    {
        private static $PDF_TEMPLATES_HELPERS = array(
            'CUAR_AirmailPdfTemplateHelper' => null,
            'CUAR_NewgenPdfTemplateHelper'  => null,
            'CUAR_MinimalPdfTemplateHelper' => null,
            'CUAR_SquarePdfTemplateHelper'  => null,
        );

        private static $EMAIL_TEMPLATES_HELPERS = array(
            'CUAR_AirmailEmailTemplateHelper' => null,
            'CUAR_CleanyEmailTemplateHelper'  => null,
            'CUAR_MuscardEmailTemplateHelper' => null,
            'CUAR_TexturaEmailTemplateHelper' => null,
        );

        private $payment_icons = null;

        public function __construct()
        {
            parent::__construct('design-extras');
        }

        public function get_addon_name()
        {
            return __('Design Extras', 'cuarde');
        }

        public function run_addon($plugin)
        {
            $this->enable_licensing(CUARDE_STORE_ITEM_ID, CUARDE_STORE_ITEM_NAME, CUARDE_PLUGIN_FILE, CUARDE_PLUGIN_VERSION);
            $this->load_textdomain();

            // Init
            add_filter('cuar/core/settings/theme-root-directories', array(&$this, 'add_themes_location'));

            // For email templates
            if (defined('CUARNO_PLUGIN_VERSION')) {
                foreach (self::$EMAIL_TEMPLATES_HELPERS as $class_name => $instance) {
                    self::$EMAIL_TEMPLATES_HELPERS[$class_name] = new $class_name($plugin, $this);
                }

                add_filter('cuar/notifications/template-root', array(&$this, 'add_email_layouts_location'), 10, 2);
            }

            // For PDF templates
            if (defined('CUARIN_PLUGIN_VERSION')) {
                foreach (self::$PDF_TEMPLATES_HELPERS as $class_name => $instance) {
                    self::$PDF_TEMPLATES_HELPERS[$class_name] = new $class_name($plugin, $this);
                }

                add_filter('cuar/private-content/invoices/pdf/template-root', array(&$this, 'add_pdf_templates_location'), 10, 2);
            }

            // More helpers
            if (class_exists('CUAR_PaymentsAddOn')) {
                $this->payment_icons = new CUAR_PaymentIconsHelper($plugin, $this);
            }

            // Init the admin interface if needed
            if (is_admin()) {
                add_filter('cuar/core/status/directories-to-scan', array(&$this, 'add_hook_discovery_directory'));
            }
        }

        /**
         * @param $dirs
         *
         * @return mixed
         */
        public function add_hook_discovery_directory($dirs)
        {
            $dirs[CUARDE_PLUGIN_DIR] = $this->get_addon_name();

            return $dirs;
        }

        /*------- OPTIONS --------------------------------------------------------------------------------------------*/

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

            if (defined('CUARIN_PLUGIN_VERSION')) {
                foreach (self::$PDF_TEMPLATES_HELPERS as $class_name => $instance) {
                    $defaults = $class_name::set_default_options($defaults);
                }
            }

            if (defined('CUARNO_PLUGIN_VERSION')) {
                foreach (self::$EMAIL_TEMPLATES_HELPERS as $class_name => $instance) {
                    $defaults = $class_name::set_default_options($defaults);
                }
            }

            if (class_exists('CUAR_PaymentsAddOn')) {
                $defaults = CUAR_PaymentIconsHelper::set_default_options($defaults);
            }

            return $defaults;
        }

        /*------- INITIALISATIONS ----------------------------------------------------------------------------------------*/

        /**
         * Load the translation file for current language.
         */
        public function load_textdomain()
        {
            $this->plugin->load_textdomain('cuarde', 'customer-area-design-extras');
        }

        /*------- FOR NEW SKINS ------------------------------------------------------------------------------------------*/

        /**
         * Tell Customer Area where to find the new themes
         *
         * @param $locations
         *
         * @return array
         */
        public function add_themes_location($locations)
        {
            $themes_type = 'frontend';
            $locations[] = array(
                'base'       => 'addon',
                'addon-name' => 'customer-area-design-extras',
                'type'       => $themes_type,
                'dir'        => untrailingslashit(CUARDE_PLUGIN_DIR) . '/skins/' . $themes_type,
                'label'      => __('Design Extras add-on', 'cuarde')
            );

            return $locations;
        }

        /*------- FOR NEW EMAIL TEMPLATES ----------------------------------------------------------------------------------*/

        /**
         * Tell Customer Area where to find the new notification templates
         *
         * @param $default_root
         * @param $layout_id
         *
         * @return string
         *
         */
        public function add_email_layouts_location($default_root, $layout_id)
        {
            if ( !is_array($default_root)) $default_root = array($default_root);

            array_unshift($default_root, CUARDE_INCLUDES_DIR);

            return $default_root;
        }

        /*------- FOR PDF TEMPLATES ---------------------------------------------------------------------------------------*/

        /**
         * Tell Customer Area where to find the new notification templates
         *
         * @param string $default_root
         * @param string $template_id
         *
         * @return string
         *
         */
        public function add_pdf_templates_location($default_root, $template_id)
        {
            if ( !is_array($default_root)) $default_root = array($default_root);

            array_unshift($default_root, CUARDE_INCLUDES_DIR);

            return $default_root;
        }

    }

    // Make sure the addon is loaded
    new CUAR_DesignExtrasAddOn();

endif; // if (!class_exists('CUAR_MasterColorsAddOn'))
