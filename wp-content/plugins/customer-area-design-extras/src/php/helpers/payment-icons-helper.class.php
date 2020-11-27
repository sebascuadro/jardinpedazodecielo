<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_PaymentIconsHelper
{
    private static $AVAILABLE_PACK_IDS = array('color-dark', 'light', 'dark');

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

        add_filter('cuar/core/payments/payment-icons/packs', array(&$this, 'get_available_payment_icon_packs'), 20, 1);

        foreach (self::$AVAILABLE_PACK_IDS as $pack_id) {
            add_filter('cuar/core/payments/payment-icons/available?pack=' . $pack_id, array(&$this, 'get_available_payment_icons'), 20, 2);
        }
    }

    /**
     * Set the default values for the options
     *
     * @param array $defaults
     *
     * @return array
     */
    public static function set_default_options($defaults)
    {
        foreach (self::$AVAILABLE_PACK_IDS as $pack_id) {
            $defaults[CUAR_PaymentsSettingsHelper::$OPTION_ENABLED_PAYMENT_ICONS . $pack_id] = array('visa', 'mastercard');
        }

        return $defaults;
    }
    /**
     * @return array A description of all available payment icon packs
     */
    public function get_available_payment_icon_packs($packs)
    {
        $packs['color-dark'] = array(
            'id'    => 'color-dark',
            'label' => __('Colorful Dark', 'cuarde'),
            'path'  => CUARDE_PLUGIN_URL . '/assets/payment-icons/color-dark/',
        );

        $packs['light'] = array(
            'id'    => 'light',
            'label' => __('Monochrome Light', 'cuarde'),
            'path'  => CUARDE_PLUGIN_URL . '/assets/payment-icons/light/',
        );

        $packs['dark'] = array(
            'id'    => 'dark',
            'label' => __('Monochrome Dark', 'cuarde'),
            'path'  => CUARDE_PLUGIN_URL . '/assets/payment-icons/dark/',
        );

        return $packs;
    }


    /**
     * @return array A description of all available payment icons
     */
    public function get_available_payment_icons($icons, $icon_pack)
    {
        if ( !in_array($icon_pack['id'], self::$AVAILABLE_PACK_IDS)) {
            return $icons;
        }

        return array(
            '2checkout'           => array(
                'label' => __('2 Checkout', 'cuarde'),
                'icon'  => $icon_pack['path'] . '2checkout.png',
            ),
            'amazon'              => array(
                'label' => __('Amazon', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'amazon.png',
            ),
            'amazon-a'            => array(
                'label' => __('Amazon', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'amazon-a.png',
            ),
            'amazon-payments'     => array(
                'label' => __('Amazon Payments', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'amazon-payments.png',
            ),
            'american-express'    => array(
                'label' => __('American Express', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'american-express.png',
            ),
            'amex'                => array(
                'label' => __('Amex', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'amex.png',
            ),
            'chase'               => array(
                'label' => __('Chase', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'chase.png',
            ),
            'chase-2'             => array(
                'label' => __('Chase', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'chase-2.png',
            ),
            'cirrus'              => array(
                'label' => __('Cirrus', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'cirrus.png',
            ),
            'delta'               => array(
                'label' => __('Delta', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'delta.png',
            ),
            'diners-club'         => array(
                'label' => __('Diner\'s Club', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'diners-club.png',
            ),
            'direct-debit'        => array(
                'label' => __('Direct Debit', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'direct-debit.png',
            ),
            'discover'            => array(
                'label' => __('Discover', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'discover.png',
            ),
            'ebay'                => array(
                'label' => __('Ebay', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'ebay.png',
            ),
            'etsy'                => array(
                'label' => __('Etsy', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'etsy.png',
            ),
            'eway'                => array(
                'label' => __('Eway', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'eway.png',
            ),
            'google-wallet'       => array(
                'label' => __('Google Wallet', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'google-wallet.png',
            ),
            'jcb'                 => array(
                'label' => __('JCB', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'jcb.png',
            ),
            'maestro'             => array(
                'label' => __('Maestro', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'maestro.png',
            ),
            'mastercard'          => array(
                'label' => __('Mastercard', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'mastercard.png',
            ),
            'moneybookers'        => array(
                'label' => __('Moneybookers', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'moneybookers.png',
            ),
            'paypal'              => array(
                'label' => __('PayPal', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'paypal.png',
            ),
            'paypal-p'            => array(
                'label' => __('PayPal', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'paypal-p.png',
            ),
            'sage'                => array(
                'label' => __('Sage', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'sage.png',
            ),
            'shopify'             => array(
                'label' => __('Shopify', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'shopify.png',
            ),
            'skrill'              => array(
                'label' => __('Skrill', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'skrill.png',
            ),
            'skrill-moneybookers' => array(
                'label' => __('Skrill Moneybookers', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'skrill-moneybookers.png',
            ),
            'solo'                => array(
                'label' => __('Solo', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'solo.png',
            ),
            'switch'              => array(
                'label' => __('Switch', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'switch.png',
            ),
            'visa'                => array(
                'label' => __('Visa', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'visa.png',
            ),
            'visa-electron'       => array(
                'label' => __('Visa Electron', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'visa-electron.png',
            ),
            'western-union'       => array(
                'label' => __('Western Union', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'western-union.png',
            ),
            'worldpay'            => array(
                'label' => __('World Pay', 'cuarde'),
                'icon'  => $icon_pack['path'] . 'worldpay.png',
            ),
        );
    }
}