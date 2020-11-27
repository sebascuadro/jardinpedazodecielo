<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon.class.php');

if (!class_exists('CUAR_ACFIntegrationAddOn')) :

    /**
     * Add-on to integrate Advanced Custom Fields with Customer Area
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_ACFIntegrationAddOn extends CUAR_AddOn
    {

        protected $supported_acf_versions = [
            '/^5\.10\.([0-9^\.]+)$/'       => 0,    // regular 5.10.x are not tested yet
            '/^5\.9\.([0-9^\.]+)$/'        => 0,    // regular 5.9.x are not tested yet
            '/^5\.8\.([0-9^\.]+)$/'        => 1,    // regular 5.8.x are supported
            '/^5\.7\.([0-9^\.]+)$/'        => -1,   // regular 5.7.x are not supported
            '/^5\.6\.([0-9^\.]+)$/'        => -1,   // regular 5.6.x are not supported
            '/^5\.5\.([0-9^\.]+)$/'        => -1,   // regular 5.5.x are not supported
            '/^5\.([0-9^\.]+[\.]{0,1})+$/' => 0,    // Other 5.x versions are not tested
            '/^([0-9^\.]+[\.]{0,1})+$/'    => -1    // Any other version is not supported
        ];

        public function __construct()
        {
            parent::__construct('acf-integration');
        }

        public function get_addon_name()
        {
            return __('ACF Integration', 'cuaracf');
        }

        public function run_addon($plugin)
        {
            $this->enable_licensing(CUARACF_STORE_ITEM_ID, CUARACF_STORE_ITEM_NAME, CUARACF_PLUGIN_FILE,
                CUARACF_PLUGIN_VERSION);

            $this->load_textdomain();

            // Don't do anything else if ACF is not installed
            if (!$this->is_acf_plugin_active()) return;

            // Init the admin interface if needed
            if (is_admin())
            {
                add_filter('cuar/core/status/directories-to-scan', [&$this, 'add_hook_discovery_directory']);
            }
            else
            {
                add_action('wp_head', [&$this, 'maybe_do_acf_head']);

                // ACF tweaks
                add_action('wp_enqueue_scripts', [&$this, 'dequeue_acf_select2'], 98);
                add_action('wp_enqueue_scripts', [&$this, 'enqueue_scripts'], 99);

                // ACF Deactivate some fields
                add_filter('acf/prepare_field/type=link', [&$this, 'deactivate_field']);
                add_filter('acf/prepare_field/type=wysiwyg', [&$this, 'deactivate_field']);
                add_filter('acf/prepare_field/type=color_picker', [&$this, 'deactivate_field']);
                add_filter('acf/prepare_field/type=user', [&$this, 'deactivate_field']);

                // User profile
                add_action('cuar/core/user-profile/view/after_fields', [&$this, 'print_profile_field_values']);
                add_action('cuar/core/user-profile/edit/after_fields', [&$this, 'print_profile_edit_form']);
                add_action('cuar/core/user-profile/edit/save_profile_fields', [&$this, 'save_profile_fields'], 10, 3);

                // Private content
                add_action('cuar/private-content/view/before_footer',
                    [&$this, 'print_private_content_field_values'], 1);
                add_action('cuar/private-content/edit/before_submit_button',
                    [&$this, 'print_private_content_edit_form'], 10, 2);
                add_action('cuar/private-content/edit/after_create', [&$this, 'save_private_content_fields'], 10, 3);
                add_action('cuar/private-content/edit/after_update', [&$this, 'save_private_content_fields'], 10, 3);

                add_action('acf/location/rule_match', [&$this, 'patch_acf_match_location_rule'], 100, 4);

                // Private containers
                add_action('cuar/core/private-container/view/before_footer',
                    [&$this, 'print_private_content_field_values'], 1);
            }
        }

        public function check_attention_needed()
        {
            parent::check_attention_needed();

            if (!is_admin())
            {
                return;
            }

            if (!$this->is_acf_plugin_active())
            {
                $this->plugin->set_attention_needed('acf-plugin-missing',
                    sprintf(__('The ACF Integration add-on requires the <a href="%s">Advanced Custom Fields</a> plugin by Elliot Condon. This plugin is either missing or not activated.',
                        'cuaracf'),
                        'http://www.advancedcustomfields.com/'
                    ), 100);
            }
            else
            {
                $this->plugin->clear_attention_needed('acf-plugin-missing');
            }

            if (function_exists('acf'))
            {
                $acf_version = $this->get_acf_version();
                $found = false;

                $recommended_acf_version_link = 'https://wordpress.org/plugins/advanced-custom-fields/';

                foreach ($this->supported_acf_versions as $regex => $support)
                {
                    if ($regex !== $acf_version && 0 === preg_match($regex, $acf_version))
                    {
                        continue;
                    }

                    if ($support === 1)
                    {
                        $this->plugin->clear_attention_needed('acf-unsupported-version');
                        $this->plugin->clear_attention_needed('acf-untested-version');
                    }
                    else if ($support === -1)
                    {
                        $this->plugin->clear_attention_needed('acf-untested-version');
                        $this->plugin->set_attention_needed('acf-unsupported-version',
                            sprintf(__('You have installed Advanced Custom Fields %1$s. This version is known to cause problems with the ACF Integration add-on. We recommend the <a href="%2$s">latest ACF version</a>.',
                                'cuaracf'),
                                $acf_version,
                                $recommended_acf_version_link
                            ), 100);
                    }
                    else
                    {
                        $this->plugin->clear_attention_needed('acf-unsupported-version');
                        $this->plugin->set_attention_needed('acf-untested-version',
                            sprintf(__('You have installed Advanced Custom Fields %1$s. This version has not yet been tested with the ACF Integration add-on. We recommend the <a href="%2$s">latest ACF version</a>.',
                                'cuaracf'),
                                $acf_version,
                                $recommended_acf_version_link
                            ), 100);
                    }

                    $found = true;
                    break;
                }

                if (!$found)
                {
                    $this->plugin->set_attention_needed('acf-untested-version',
                        sprintf(__('You have installed Advanced Custom Fields %1$s. This version has not yet been tested with the ACF Integration add-on. We recommend the <a href="%2$s">latest ACF version</a>.',
                            'cuaracf'),
                            $acf_version,
                            $recommended_acf_version_link
                        ), 100);
                }
            }
        }

        public function add_hook_discovery_directory($dirs)
        {
            $dirs[CUARACF_PLUGIN_DIR] = $this->get_addon_name();

            return $dirs;
        }

        /**
         * Load the translation file for current language.
         */
        public function load_textdomain()
        {
            $this->plugin->load_textdomain('cuaracf', 'customer-area-acf-integration');
        }

        public function maybe_do_acf_head()
        {
            if (is_admin()) return;

            /** @var \CUAR_CustomerPagesAddOn $cp_addon */
            $cp_addon = $this->plugin->get_addon('customer-pages');
            if (!$cp_addon->is_customer_area_page()) return;

            acf_form_head();
        }

        private function get_acf_version()
        {
            if (file_exists(WP_PLUGIN_DIR . '/advanced-custom-fields-pro/acf.php'))
            {
                $info = get_file_data(WP_PLUGIN_DIR . '/advanced-custom-fields-pro/acf.php',
                    ['Version' => 'Version',], 'plugin');
                return $info['Version'];
            }

            if (file_exists(WP_PLUGIN_DIR . '/advanced-custom-fields/acf.php'))
            {
                $info = get_file_data(WP_PLUGIN_DIR . '/advanced-custom-fields/acf.php',
                    ['Version' => 'Version',], 'plugin');
                return $info['Version'];
            }

            return '0.0.0';
        }

        // Related to https://github.com/AdvancedCustomFields/acf/issues/96
        public function patch_acf_match_location_rule($result, $rule, $screen, $field_group)
        {
            if (isset($screen['post_id'])
                && ($rule['param'] === 'page' || $rule['param'] === 'post')
                && strpos($screen['post_id'], 'user') === 0)
            {
                // Only needed on edit profile page
                $cp_addon = $this->plugin->get_addon('customer-pages');
                $edit_account_page = $cp_addon->get_customer_area_page('customer-account-edit');
                $current_page = get_queried_object();

                if (isset($current_page->ID) && $edit_account_page->get_page_id() === $current_page->ID)
                {
                    return acf_match_location_rule($rule, ['post_id' => $current_page->ID] + $screen, $field_group);
                }
            }

            return $result;
        }

        /**
         * Enqueue ACF Integration scripts
         */
        public function enqueue_scripts()
        {
            if (cuar_is_customer_area_page(get_queried_object_id()) || cuar_is_customer_area_private_content(get_the_ID()))
            {
                $this->plugin->enable_library('jquery.select2');
                wp_enqueue_script('cuar-acf-integration',
                    CUARACF_PLUGIN_URL . '/assets/frontend/js/customer-area-acf-integration.min.js',
                    ['jquery', 'jquery.select2', 'acf-input'], CUARACF_PLUGIN_VERSION);
            }
        }

        /**
         * Enqueue ACF Google maps scripts
         */
        protected function enqueue_map_scripts()
        {
            if (function_exists('acf_get_setting'))
            {
                $acf_api_key_for_wpca = apply_filters('/cuar/acf-integration/maps_api_key',
                    acf_get_setting('google_api_key'));
                wp_enqueue_script('google-map', 'https://maps.googleapis.com/maps/api/js?key=' . $acf_api_key_for_wpca,
                    [], '3', true);
            }
        }

        /**
         * Dequeue ACF select2 on WPCA pages
         */
        public function dequeue_acf_select2()
        {
            if (cuar_is_customer_area_page(get_queried_object_id()) || cuar_is_customer_area_private_content(get_the_ID()))
            {
                acf_update_setting('enqueue_select2', false);
            }
        }

        /**
         * Deactivate some non-compatible ACF fields
         *
         * @param $field ACF field data
         * @return ACF|bool
         */
        public function deactivate_field($field)
        {
            if (cuar_is_customer_area_page(get_queried_object_id()) || cuar_is_customer_area_private_content(get_the_ID()))
            {
                return false;
            }
            return $field;
        }

        /*------- PRIVATE CONTENT ---------------------------------------------------------------------------------------*/

        /**
         * @param CUAR_AbstractEditContentPageAddOn $edit_content_page
         * @param array                             $form_errors
         */
        public function save_private_content_fields($edit_content_page, $form_errors, $form_data)
        {
            if ($edit_content_page->get_current_post_id() === null || !empty($form_errors)) return;

            // Actually save the fields using ACF callbacks
            $post_id = apply_filters('acf/pre_save_post', $edit_content_page->get_current_post_id(), $form_data);
            do_action('acf/save_post', $post_id);
        }

        /**
         * Print the ACF fields in the profile edition page
         *
         * @param CUAR_AbstractEditContentPageAddOn $create_content_page
         * @param int                               $step_id
         */
        public function print_private_content_edit_form($create_content_page, $step_id)
        {
            // Only print fields on the first wizard step
            if ($step_id !== 0) return;

            $options = [
                'form'            => false,
                'return'          => null,
                'uploader'        => 'basic',
                'label_placement' => 'left',
            ];

            if ($create_content_page->get_current_post_id() === null)
            {
                $options['post_id'] = 'new_post';
                $options['new_post'] = [
                    'post_type' => $create_content_page->get_friendly_post_type(),
                ];
            }
            else
            {
                $options['post_id'] = $create_content_page->get_current_post_id();
            }

            acf_form($options);
        }

        /**
         * Print the ACF fields in the profile edition page
         *
         * @param CUAR_AbstractEditContentPageAddOn $content_page
         */
        public function print_private_content_field_values($content_page)
        {
            // Use current post
            $this->print_acf_fields(0);
        }


        /*------- USER PROFILE ------------------------------------------------------------------------------------------*/

        /**
         * Print the ACF fields in the profile edition page
         *
         * @param unknown $user
         */
        public function print_profile_field_values($user)
        {
            $this->print_acf_fields($user, 'profile');
        }

        /**
         * Print the ACF fields in the profile edition page
         *
         * @param unknown $user
         */
        public function print_profile_edit_form($user)
        {
            $options = [
                'post_id'  => 'user_' . $user->ID,
                'form'     => false,
                'return'   => null,
                'uploader' => 'basic',
            ];

            acf_form($options);
        }

        /**
         * Saves the ACF fields after form submission
         *
         * @param unknown $current_user_id
         * @param unknown $form_errors
         */
        public function save_profile_fields($current_user_id, $form_errors, $form_data)
        {
            // Actually save the fields using ACF callbacks
            $post_id = apply_filters('acf/pre_save_post', 'user_' . $current_user_id, $form_data);

            do_action('acf/save_post', $post_id);
        }

        /**
         * Converts a jQuery date format to a PHP date format
         *
         * @param string $js_format date format to convert
         *
         * @return string date format usable in PHP
         */
        public function convert_js_date_format_to_php_date_format($js_format)
        {
            $tokens = [
                'year'  => [
                    'yy' => 'Y',
                    'y'  => 'y',
                ],
                'month' => [
                    'MM' => 'F',
                    'M'  => 'M',
                    'mm' => 'm',
                    'm'  => 'n',
                ],
                'day'   => [
                    'dd' => 'd',
                    'd'  => 'j',
                    'DD' => 'l',
                    'o'  => 'z',
                ],
            ];

            $php_format = $js_format;

            foreach ($tokens as $cat => $subs)
            {
                foreach ($subs as $js => $php)
                {
                    if (false !== strpos($php_format, $js))
                    {
                        $php_format = str_replace($js, $php, $php_format);
                        break;
                    }
                }
            }

            return $php_format;
        }

        protected function print_acf_fields($object, $template_suffix = 'default')
        {
            $fields = get_field_objects($object);
            if ($fields === false)
            {
                return;
            }

            foreach ($fields as $field)
            {
                $this->print_field($field, $template_suffix);
            }
        }

        protected function print_field($field, $template_suffix = 'default', $print_wrapper = true)
        {
            $field_type = $field['type'];
            $field_name = $field['name'];

            if ($field_type === 'google_map')
            {
                $this->enqueue_map_scripts();
            }

            if ($print_wrapper)
            {
                /** @noinspection PhpIncludeInspection */
                include($this->plugin->get_template_file_path(
                    CUARACF_INCLUDES_DIR,
                    [
                        "acf-field-wrapper-open-$field_type-$template_suffix.template.php",
                        "acf-field-wrapper-open-$field_type-$field_name.template.php",
                        "acf-field-wrapper-open-$field_type.template.php",
                        "acf-field-wrapper-open-$template_suffix.template.php",
                    ],
                    'templates',
                    'acf-field-wrapper-open.template.php'));
            }

            /** @noinspection PhpIncludeInspection */
            include($this->plugin->get_template_file_path(
                CUARACF_INCLUDES_DIR,
                [
                    "acf-field-$field_type-$template_suffix.template.php",
                    "acf-field-$template_suffix.template.php",
                    "acf-field-$field_type-$field_name.template.php",
                    "acf-field-$field_type.template.php",
                ],
                'templates',
                'acf-field.template.php'));

            if ($print_wrapper)
            {
                /** @noinspection PhpIncludeInspection */
                include($this->plugin->get_template_file_path(
                    CUARACF_INCLUDES_DIR,
                    [
                        "acf-field-wrapper-close-$field_type-$template_suffix.template.php",
                        "acf-field-wrapper-close-$template_suffix.template.php",
                        "acf-field-wrapper-close-$field_type-$field_name.template.php",
                        "acf-field-wrapper-close-$field_type.template.php",
                    ],
                    'templates',
                    'acf-field-wrapper-close.template.php'));
            }
        }

        protected function is_acf_plugin_active()
        {
            return class_exists('acf');
        }
    }

    // Make sure the addon is loaded
    new CUAR_ACFIntegrationAddOn();

endif; // if (!class_exists('CUAR_ACFIntegrationAddOn')) 
