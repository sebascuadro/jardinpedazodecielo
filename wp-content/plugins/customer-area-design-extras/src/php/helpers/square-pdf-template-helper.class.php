<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_SquarePdfTemplateHelper extends CUAR_AbstractPdfTemplateHelper
{
    public static $TEMPLATE_ID = 'square';

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
        $html2pdf->setDefaultFont('freemono');

        return $html2pdf;
    }

    public function get_template_name()
    {
        return __('Square', 'cuarde');
    }

    public static function get_template_fields()
    {
        return array(
	        array(
		        'id'          => 'color_back',
		        'label'       => __('Columns background color', 'cuarde'),
		        'type'        => 'color',
		        'default'     => '#2f6182',
		        'description' => __('Hexadecimal format, example: #123456', 'cuarde')
	        ),
            array(
                'id'          => 'color_text',
                'label'       => __('Text color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#232323',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
	        array(
		        'id'          => 'color_text_columns',
		        'label'       => __('Column text color', 'cuarde'),
		        'type'        => 'color',
		        'default'     => '#FFFFFF',
		        'description' => __('Hexadecimal format, example: #123456', 'cuarde')
	        ),
            array(
                'id'          => 'color_items',
                'label'       => __('Items color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#3a5b84',
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
		        'id'          => 'color_square',
		        'label'       => __('Square color', 'cuarde'),
		        'type'        => 'color',
		        'default'     => '#000000',
		        'description' => __('Hexadecimal format, example: #123456', 'cuarde')
	        ),
	        array(
		        'id'          => 'color_text_square',
		        'label'       => __('Square text color', 'cuarde'),
		        'type'        => 'color',
		        'default'     => '#FFFFFF',
		        'description' => __('Hexadecimal format, example: #123456', 'cuarde')
	        ),
            array(
                'id'          => 'footer_thanks_message',
                'label'       => __('Footer thanks message', 'cuarde'),
                'type'        => 'text',
                'default'     => __('THANK YOU&nbsp;!', 'cuarde'),
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
                'label'       => __('Footer thanks message background color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#000000',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),
            array(
                'id'          => 'color_footer_text',
                'label'       => __('Footer thanks message color', 'cuarde'),
                'type'        => 'color',
                'default'     => '#FFFFFF',
                'description' => __('Hexadecimal format, example: #123456', 'cuarde')
            ),

        );
    }
}