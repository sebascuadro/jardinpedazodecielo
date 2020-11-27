<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_AirmailEmailTemplateHelper extends CUAR_AbstractEmailTemplateHelper
{
    public static $TEMPLATE_ID = 'airmail';

    /**
     * Constructor
     */
    public function __construct($plugin, $de_addon)
    {
        parent::__construct($plugin, $de_addon);
    }

    public function get_template_name()
    {
        return __('Airmail', 'cuarde');
    }

    public static function get_template_fields()
    {
        return array(
            array(
                'id'          => 'color_link',
                'label'       => __('Link color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#f46969',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'back_img',
                'label'       => __('Header background image', 'cuarde'),
                'type'        => 'upload',
                'default'     => CUARDE_PLUGIN_URL . '/assets/notifications/airmail/topbar.jpg',
                'description' => __('Stripped bar background used on the top of the email', 'cuarde')
            ),
            array(
                'id'          => 'back_img_color',
                'label'       => __('Header background color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#efefef',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'main_background',
                'label'       => __('Main background', 'cuarde'),
                'type'        => 'color',
                'default'     => '#2d2d2d',
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
                'default'     => '#4e6fb6',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'footer_background',
                'label'       => __('Footer background', 'cuarde'),
                'type'        => 'color',
                'default'     => '#E5E5E5',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'footer_border',
                'label'       => __('Footer border color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#4e6fb6',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'footer_text_color',
                'label'       => __('Footer text color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#4e6fb6',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),

        );
    }
}