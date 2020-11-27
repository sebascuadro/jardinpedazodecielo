<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_CleanyEmailTemplateHelper extends CUAR_AbstractEmailTemplateHelper
{
    public static $TEMPLATE_ID = 'cleany';

    /**
     * Constructor
     */
    public function __construct($plugin, $de_addon)
    {
        parent::__construct($plugin, $de_addon);
    }

    public function get_template_name()
    {
        return __('Cleany', 'cuarde');
    }

    public static function get_template_fields()
    {
        return array(
            array(
                'id'          => 'color_link',
                'label'       => __('Link color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#be3132',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'main_text_color',
                'label'       => __('Main text color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#8c8c8c',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'main_title_color',
                'label'       => __('Main title color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#4f4f4f',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'main_content_bg',
                'label'       => __('Main content background', 'cuarde'),
                'type'        => 'color',
                'default'     => '#ffffff',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'main_content_color',
                'label'       => __('Main content color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#777777',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'main_content_border',
                'label'       => __('Main content border', 'cuarde'),
                'type'        => 'color',
                'default'     => '#e1e1e1',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
        );
    }
}