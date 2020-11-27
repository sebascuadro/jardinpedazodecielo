<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_MuscardEmailTemplateHelper extends CUAR_AbstractEmailTemplateHelper
{
    public static $TEMPLATE_ID = 'muscard';

    /**
     * Constructor
     */
    public function __construct($plugin, $de_addon)
    {
        parent::__construct($plugin, $de_addon);
    }

    public function get_template_name()
    {
        return __('Muscard', 'cuarde');
    }

    public static function get_template_fields()
    {
        return array(
            array(
                'id'          => 'color_link',
                'label'       => __('Link color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#a88123',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'main_text_color',
                'label'       => __('Main text color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#333333',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'main_title_color',
                'label'       => __('Main title color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#ffffff',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'main_content_bg',
                'label'       => __('Main content background', 'cuarde'),
                'type'        => 'color',
                'default'     => '#ffffff',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
        );
    }
}