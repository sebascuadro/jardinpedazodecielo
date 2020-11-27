<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_TexturaEmailTemplateHelper extends CUAR_AbstractEmailTemplateHelper
{
    public static $TEMPLATE_ID = 'textura';

    /**
     * Constructor
     */
    public function __construct($plugin, $de_addon)
    {
        parent::__construct($plugin, $de_addon);
    }

    public function get_template_name()
    {
        return __('Textura', 'cuarde');
    }

    public static function get_template_fields()
    {
        return array(
            array(
                'id'          => 'header_image',
                'label'       => __('Link color', 'cuarde'),
                'type'        => 'upload',
                'default'     => '',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_link',
                'label'       => __('Link color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#4785c7',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_title',
                'label'       => __('Title color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#4785c7',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_header_text',
                'label'       => __('Header text color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#c6c6c6',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_main_text',
                'label'       => __('Main text color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#767676',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_footer_text',
                'label'       => __('Footer text color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#7d7a7a',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
        );
    }
}