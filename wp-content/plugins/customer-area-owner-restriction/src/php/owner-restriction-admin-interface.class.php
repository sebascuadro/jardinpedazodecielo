<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/settings.class.php');
require_once(CUAR_INCLUDES_DIR . '/helpers/wordpress-helper.class.php');

if ( !class_exists('CUAR_OwnerRestrictionAdminInterface')) :

    /**
     * Administation area for private files
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_OwnerRestrictionAdminInterface
    {

        public function __construct($plugin, $or_addon)
        {
            $this->plugin = $plugin;
            $this->or_addon = $or_addon;

            $tabs = array('cuar_private_files', 'cuar_private_pages', 'cuar_conversations', 'cuar_tasklists', 'cuar_invoicing');
            foreach ($tabs as $tab)
            {
                add_action('cuar/core/settings/print-settings?tab=' . $tab, array(&$this, 'print_settings_' . $tab), 60, 2);
                add_filter('cuar/core/settings/validate-settings?tab=' . $tab, array(&$this, 'validate_options_' . $tab), 60, 3);
            }
        }

        /*------- CUSTOMISATION OF THE PLUGIN SETTINGS PAGE -------------------------------------------------------------*/

        public function print_restriction_settings($content_type, $cuar_settings, $options_group)
        {
            add_settings_section(
                'cuar_owner_restriction',
                __('Owner Restriction', 'cuaror'),
                array(&$this, 'print_owner_restriction_section_for_' . $content_type),
                CUAR_Settings::$OPTIONS_PAGE_SLUG
            );
        }

        /**
         * Print some info about the section
         */
        public function print_owner_restriction_section_info($content_type)
        {
            echo '<p>'
                . __('The table below sets the owners that can be chosen by an author when creating or editing this private content type.', 'cuaror')
                . '</p>';

            include($this->plugin->get_template_file_path(
                CUAROR_INCLUDES_DIR,
                'restrictions-table.template.php',
                'templates'));
        }

        public function print_settings_cuar_private_pages($cuar_settings, $options_group)
        {
            $this->print_restriction_settings('cuar_private_page', $cuar_settings, $options_group);
        }

        public function validate_options_cuar_private_pages($validated, $cuar_settings, $input)
        {
            return $this->validate_restriction_options('cuar_private_page', $validated, $cuar_settings, $input);
        }

        public function print_settings_cuar_private_files($cuar_settings, $options_group)
        {
            $this->print_restriction_settings('cuar_private_file', $cuar_settings, $options_group);
        }

        public function validate_options_cuar_private_files($validated, $cuar_settings, $input)
        {
            return $this->validate_restriction_options('cuar_private_file', $validated, $cuar_settings, $input);
        }

        public function print_settings_cuar_conversations($cuar_settings, $options_group)
        {
            $this->print_restriction_settings('cuar_conversation', $cuar_settings, $options_group);
        }

        public function validate_options_cuar_conversations($validated, $cuar_settings, $input)
        {
            return $this->validate_restriction_options('cuar_conversation', $validated, $cuar_settings, $input);
        }

        public function print_settings_cuar_tasklists($cuar_settings, $options_group)
        {
            $this->print_restriction_settings('cuar_tasklist', $cuar_settings, $options_group);
        }

        public function validate_options_cuar_tasklists($validated, $cuar_settings, $input)
        {
            return $this->validate_restriction_options('cuar_tasklist', $validated, $cuar_settings, $input);
        }

        public function print_settings_cuar_invoicing($cuar_settings, $options_group)
        {
            $this->print_restriction_settings('cuar_invoice', $cuar_settings, $options_group);
        }

        public function validate_options_cuar_invoicing($validated, $cuar_settings, $input)
        {
            return $this->validate_restriction_options('cuar_invoice', $validated, $cuar_settings, $input);
        }

        public function print_owner_restriction_section_for_cuar_private_file()
        {
            $this->print_owner_restriction_section_info('cuar_private_file');
        }

        public function print_owner_restriction_section_for_cuar_private_page()
        {
            $this->print_owner_restriction_section_info('cuar_private_page');
        }

        public function print_owner_restriction_section_for_cuar_conversation()
        {
            $this->print_owner_restriction_section_info('cuar_conversation');
        }

        public function print_owner_restriction_section_for_cuar_tasklist()
        {
            $this->print_owner_restriction_section_info('cuar_tasklist');
        }

        public function print_owner_restriction_section_for_cuar_invoice()
        {
            $this->print_owner_restriction_section_info('cuar_invoice');
        }

        public function validate_restriction_options($content_type, $validated, $cuar_settings, $input)
        {
            global $wp_roles;
            if ( !isset($wp_roles)) $wp_roles = new WP_Roles();
            $all_roles = $wp_roles->role_objects;

            /** @var CUAR_PostOwnerAddOn $po_addon */
            $po_addon = $this->plugin->get_addon('post-owner');
            $owner_types = $po_addon->get_owner_types();

            foreach ($all_roles as $role)
            {
                foreach ($owner_types as $type => $owner_type_label)
                {
                    $option_id = CUAR_OwnerRestrictionAddOn::get_restriction_option_id($content_type, $role->name, $type);
                    $validated[$option_id] = $input[$option_id];
                }
            }

            return $validated;
        }


        /** @var CUAR_Plugin */
        private $plugin;

        /** @var CUAR_OwnerRestrictionAddOn */
        private $or_addon;
    }

endif; // if (!class_exists('CUAR_OwnerRestrictionAdminInterface')) :