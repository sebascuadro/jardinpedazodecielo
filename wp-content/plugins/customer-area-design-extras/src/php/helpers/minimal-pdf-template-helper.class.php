<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_MinimalPdfTemplateHelper extends CUAR_AbstractPdfTemplateHelper
{
    public static $TEMPLATE_ID = 'minimal';

    /**
     * Constructor
     */
    public function __construct($plugin, $de_addon)
    {
        parent::__construct($plugin, $de_addon);

        //add_filter('cuar/private-content/invoices/pdf/html2pdf-object?template=' . static::$TEMPLATE_ID, array(&$this, 'prepare_html2pdf_object'), 15, 1);
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
        $html2pdf->addFont('freemono', 'BI', CUARDE_PLUGIN_DIR . '/assets/fonts/freemonob.php');
        $html2pdf->setDefaultFont('freemono');

        return $html2pdf;
    }

    public function get_template_name()
    {
        return __('Minimal', 'cuarde');
    }

    public static function get_template_fields()
    {
        return array(
            array(
                'id'          => 'color_text',
                'label'       => __('Text color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#232323',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_accent',
                'label'       => __('Accent color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#4f4f4f',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_items',
                'label'       => __('Items color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#4f4f4f',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_secondary_contents',
                'label'       => __('Secondary contents color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#666666',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_border',
                'label'       => __('Border color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#000000',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_secondary_border',
                'label'       => __('Secondary border color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#939393',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'back_img',
                'label'       => __('Background image', 'cuarde'),
                'type'        => 'upload',
                'default'     => CUARDE_PLUGIN_URL . "/assets/invoices/bg-2.jpg",
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
                'id'          => 'footer_thanks_message',
                'label'       => __('Footer thanks message', 'cuarde'),
                'type'        => 'text',
                'default'     => __('THANK YOU', 'cuarde'),
                'description' => __('Leave empty if you don\'t want a footer to be displayed.', 'cuarde'),
                'validate'    => 'always'
            ),
            array(
                'id'          => 'footer_thanks_message_size',
                'label'       => __('Footer thanks message typo size', 'cuarde'),
                'type'        => 'select',
                'default'     => '30pt',
                'options'     => array(
                    '36pt'  => '36pt',
                    '34pt'  => '34pt',
                    '32pt'  => '32pt',
                    '30pt'  => '30pt',
                    '28pt'  => '28pt',
                    '26pt'  => '26pt',
                    '24pt'  => '24pt',
                    '22pt'  => '22pt',
                    '20pt'  => '20pt',
                    '18pt'  => '18pt',
                    '16pt'  => '16pt',
                ),
                'multiple'    => false,
                'description' => __('Choose the typo size for your Thanks message.', 'cuarde')
            ),
            array(
                'id'          => 'color_footer_bg',
                'label'       => __('Footer background color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#000000',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_footer_text',
                'label'       => __('Footer text color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#FFFFFF',
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