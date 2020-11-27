<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

abstract class CUAR_AbstractTemplateHelper
{
    public static $TEMPLATE_ID = 'INVALID';

    /** @var CUAR_Plugin */
    private $plugin;

    /** @var CUAR_DesignExtrasAddOn */
    private $de_addon;

    /**
     * Constructor
     */
    public function __construct($plugin, $de_addon)
    {
        $this->plugin = $plugin;
        $this->de_addon = $de_addon;

        if (is_admin()) {
            add_filter($this->get_register_template_hook(), array(&$this, 'register_template'));
            add_action($this->get_print_settings_hook(), array(&$this, 'print_template_settings'), 10, 3);
            add_filter($this->get_validate_settings_hook(), array(&$this, 'validate_template_settings'), 10, 4);
        }
    }

    public abstract function get_register_template_hook();

    public abstract function get_print_settings_hook();

    public abstract function get_validate_settings_hook();

    public abstract function get_template_name();

    public static function get_template_setting_id($field_id)
    {
        return 'INVALID';
    }

    public static function get_template_fields()
    {
        return array();
    }

    /**
     * Register our template
     *
     * @param $templates
     *
     * @return array
     */
    public function register_template($templates)
    {
        $templates[static::$TEMPLATE_ID] = $this->get_template_name();

        return $templates;
    }

    /*------- OPTIONS --------------------------------------------------------------------------------------------*/

    /**
     * Set the default values for the options
     *
     * @param array $defaults
     *
     * @return array
     */
    public static function set_default_options($defaults)
    {
        // Set defaults for the PDF template
        $fields = static::get_template_fields();
        foreach ($fields as $field) {
            $setting_id = static::get_template_setting_id($field['id']);
            $defaults[$setting_id] = $field['default'];
        }

        return $defaults;
    }

    /**
     * Print settings for the blocs template
     *
     * @param CUAR_NotificationsSettingsHelper|CUAR_InvoiceSettingsHelper $settings
     * @param CUAR_Settings                                               $cuar_settings
     * @param string                                                      $section_id
     */
    public function print_template_settings($settings, $cuar_settings, $section_id)
    {
        $fields = static::get_template_fields();

        foreach ($fields as $field) {
            $setting_id = static::get_template_setting_id($field['id']);

            if ($field['type'] == 'text' || $field['type'] == 'color' || $field['type'] == 'upload') {
                add_settings_field(
                    $setting_id,
                    $field['label'],
                    array(&$cuar_settings, 'print_input_field'),
                    CUAR_Settings::$OPTIONS_PAGE_SLUG,
                    $section_id,
                    array(
                        'option_id' => $setting_id,
                        'type'      => $field['type'],
                        'after'     =>
                            '<p class="description">'
                            . $field['description'] . " " . sprintf(__('[Default: %s]', 'cuarde'), $field['default'])
                            . '</p>'
                            . $settings->get_js_setting_marker(static::$TEMPLATE_ID),
                    )
                );
            } elseif ($field['type'] == 'select') {
                add_settings_field(
                    $setting_id,
                    $field['label'],
                    array(&$cuar_settings, 'print_select_field'),
                    CUAR_Settings::$OPTIONS_PAGE_SLUG,
                    $section_id,
                    array(
                        'option_id' => $setting_id,
                        'options'   => $field['options'],
                        'multiple'  => $field['multiple'],
                        'after'     =>
                            '<p class="description">'
                            . $field['description'] . " " . sprintf(__('[Default: %s]', 'cuarde'), $field['default'])
                            . '</p>'
                            . $settings->get_js_setting_marker(static::$TEMPLATE_ID)
                    )
                );
            } else {
                wp_die('ERROR: Invalid or unknown field type ( ' . $field['type'] . ' ) for field ' . $setting_id);
            }
        }
    }

    /**
     * Validate the settings for the blocs template
     *
     * @param CUAR_Settings                                               $cuar_settings
     * @param array                                                       $input
     * @param array                                                       $validated
     * @param CUAR_NotificationsSettingsHelper|CUAR_InvoiceSettingsHelper $settings
     *
     * @return array
     */
    public function validate_template_settings($validated, $cuar_settings, $input, $settings)
    {
        $fields = static::get_template_fields();

        foreach ($fields as $field) {
            $setting_id = static::get_template_setting_id($field['id']);

            if (isset($field['validate'])) {
                if ($field['validate'] == 'always') {
                    $cuar_settings->validate_always($input, $validated, $setting_id);
                } else {
                    wp_die('ERROR: Invalid or unknown validate option ( ' . $field['validate'] . ' ) for field ' . $setting_id);
                }
            } else {
                if ($field['type'] == 'color') {
                    $cuar_settings->validate_hex_color($input, $validated, $setting_id);
                } elseif ($field['type'] == 'text') {
                    $cuar_settings->validate_not_empty($input, $validated, $setting_id);
                } elseif ($field['type'] == 'upload') {
                    $cuar_settings->validate_always($input, $validated, $setting_id);
                } elseif ($field['type'] == 'select') {
                    $cuar_settings->validate_not_empty($input, $validated, $setting_id);
                } else {
                    wp_die('ERROR: No validation method available for current field type ( ' . $field['type'] . ' ) with ID ' . $setting_id);
                }
            }
        }

        return $validated;
    }
}