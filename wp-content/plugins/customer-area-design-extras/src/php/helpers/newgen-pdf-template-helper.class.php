<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_NewgenPdfTemplateHelper extends CUAR_AbstractPdfTemplateHelper
{
    public static $TEMPLATE_ID = 'newgen';

    /**
     * Constructor
     */
    public function __construct($plugin, $de_addon)
    {
        parent::__construct($plugin, $de_addon);
    }

    public function get_template_name()
    {
        return __('Newgen', 'cuarde');
    }

    public static function get_template_fields()
    {
        return array(
            array(
                'id'          => 'color_text',
                'label'       => __('Text color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#5C5C5C',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_neutral',
                'label'       => __('Neutral color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#DDDDDD',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_neutral_text',
                'label'       => __('Neutral text color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#5C5C5C',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_primary',
                'label'       => __('Primary color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#57B223',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_primary_text',
                'label'       => __('Primary text color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#FFFFFF',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_secondary',
                'label'       => __('Secondary color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#0087C3',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_secondary_text',
                'label'       => __('Secondary text color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#FFFFFF',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'back_img',
                'label'       => __('Background image', 'cuarde'),
                'type'        => 'upload',
                'default'     => CUARDE_PLUGIN_URL . "/assets/invoices/bg-1.jpg",
                'description' => __('Upload an image to customize the PDF pages background', 'cuarde')
            ),
            array(
                'id'          => 'color_back',
                'label'       => __('Background color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#FFFFFF',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'back_img_x',
                'label'       => __('Background horizontal position', 'cuarde'),
                'type'        => 'text',
                'default'     => "left",
                'description' => __('Can be: left / center / right / value(mm, px, pt, %)', 'cuarde'),
                'validate'    => 'always'
            ),
            array(
                'id'          => 'back_img_y',
                'label'       => __('Background vertical position', 'cuarde'),
                'type'        => 'text',
                'default'     => "top",
                'description' => __('Can be: top / middle / bottom / value(mm, px, pt, %)', 'cuarde'),
                'validate'    => 'always'
            ),
            array(
                'id'          => 'back_img_w',
                'label'       => __('Background width', 'cuarde'),
                'type'        => 'text',
                'default'     => "100%",
                'description' => __('Can be: value(mm, px, pt, %)', 'cuarde'),
                'validate'    => 'always'
            ),
            array(
                'id'          => 'page_orientation',
                'label'       => __('Page orientation', 'cuarde'),
                'type'        => 'select',
                'default'     => 'portrait',
                'options'     => array(
                    'portrait'  => __('Portrait', 'cuarde'),
                    'landscape' => __('Landscape', 'cuarde'),
                ),
                'multiple'    => false,
                'description' => __('Choose weither to render the page in potrait or landscape mode', 'cuarde')
            ),

        );
    }
}