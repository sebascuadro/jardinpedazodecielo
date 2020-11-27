<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_AirmailPdfTemplateHelper extends CUAR_AbstractPdfTemplateHelper
{
    public static $TEMPLATE_ID = 'airmail';

    /**
     * Constructor
     */
    public function __construct($plugin, $de_addon)
    {
        parent::__construct($plugin, $de_addon);

        //add_filter('cuar/private-content/invoices/pdf/html2pdf-object?template=' . static::$TEMPLATE_ID, array(&$this, 'prepare_html2pdf_object'), 10, 1);
    }

    public function get_template_name()
    {
        return __('Airmail', 'cuarde');
    }

    /**
     * @param HTML2PDF $html2pdf
     *
     * @return HTML2PDF
     */
    public function prepare_html2pdf_object($html2pdf)
    {
        $html2pdf->addFont('freemono', '', CUARDE_PLUGIN_DIR . '/assets/fonts/freemono.php');
        $html2pdf->addFont('freemono', 'B', CUARDE_PLUGIN_DIR . '/assets/fonts/freemonob.php');
        $html2pdf->addFont('freemono', 'I', CUARDE_PLUGIN_DIR . '/assets/fonts/freemonoi.php');
        $html2pdf->setDefaultFont('freemono');

        return $html2pdf;
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
                'default'     => '#878787',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_primary',
                'label'       => __('Primary color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#4d6fb4',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_secondary',
                'label'       => __('Secondary color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#e55f5b',
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
                'default'     => CUARDE_PLUGIN_URL . '/assets/notifications/airmail/topbar.jpg',
                'description' => __('Stripped bar background used on the top of the pages', 'cuarde')
            ),
            array(
                'id'          => 'color_back',
                'label'       => __('Background color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#f0f0f0',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
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