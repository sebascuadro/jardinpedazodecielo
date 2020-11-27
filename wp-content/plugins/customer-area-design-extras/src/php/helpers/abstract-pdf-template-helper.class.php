<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

abstract class CUAR_AbstractPdfTemplateHelper extends CUAR_AbstractTemplateHelper
{
    /**
     * Constructor
     */
    public function __construct($plugin, $de_addon)
    {
        parent::__construct($plugin, $de_addon);
    }

    public function get_register_template_hook()
    {
        return 'cuar/private-content/invoices/pdf/available-templates';
    }

    public function get_print_settings_hook()
    {
        return 'cuar/private-content/invoices/print-template-settings';
    }

    public function get_validate_settings_hook()
    {
        return 'cuar/private-content/invoices/validate-template-settings';
    }

    public static function get_template_setting_id($field_id)
    {
        return CUAR_InvoiceSettingsHelper::get_pdf_template_setting_id(static::$TEMPLATE_ID, $field_id);
    }
}