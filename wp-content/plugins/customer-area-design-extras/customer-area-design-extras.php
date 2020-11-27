<?php
/*
	Plugin Name: 	WP Customer Area - Design Extras
	Description: 	Additional skin color schemes, PDF invoices and notification templates.
	Plugin URI: 	http://wp-customerarea.com
	Version: 		1.2.3
	Author: 		MarvinLabs
	Author URI: 	http://www.marvinlabs.com
	Text Domain: 	cuarmc
	Domain Path: 	/languages
*/

/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

//------------------------------------------------------------
// Main plugin detection stuff

include(dirname(__FILE__) . '/libs/cuar/cuar_commons.php');
if (cuar_is_main_plugin_missing()) return;

// End of main plugin detection stuff
//------------------------------------------------------------

define( 'CUARDE_STORE_ITEM_NAME', 	'Customer Area – Design Extras' );
define( 'CUARDE_STORE_ITEM_ID', 	87916 );
define( 'CUARDE_PLUGIN_VERSION', 	'1.2.3' );

define( 'CUARDE_PLUGIN_DIR', 	WP_PLUGIN_DIR . '/customer-area-design-extras' );
define( 'CUARDE_PLUGIN_URL', 	plugins_url() . '/customer-area-design-extras' );
define( 'CUARDE_LANGUAGE_DIR', 	'customer-area-design-extras/languages' );
define( 'CUARDE_INCLUDES_DIR', 	CUARDE_PLUGIN_DIR . '/src/php' );
define( 'CUARDE_PLUGIN_FILE',	CUARDE_PLUGIN_DIR . '/customer-area-design-extras.php' );


// Load the addon
include_once( CUARDE_INCLUDES_DIR . '/helpers/payment-icons-helper.class.php' );

include_once( CUARDE_INCLUDES_DIR . '/helpers/abstract-template-helper.class.php' );
include_once( CUARDE_INCLUDES_DIR . '/helpers/abstract-email-template-helper.class.php' );
include_once( CUARDE_INCLUDES_DIR . '/helpers/abstract-pdf-template-helper.class.php' );

include_once( CUARDE_INCLUDES_DIR . '/helpers/airmail-pdf-template-helper.class.php' );
include_once( CUARDE_INCLUDES_DIR . '/helpers/newgen-pdf-template-helper.class.php' );
include_once( CUARDE_INCLUDES_DIR . '/helpers/minimal-pdf-template-helper.class.php' );
include_once( CUARDE_INCLUDES_DIR . '/helpers/square-pdf-template-helper.class.php' );

include_once( CUARDE_INCLUDES_DIR . '/helpers/airmail-email-template-helper.class.php' );
include_once( CUARDE_INCLUDES_DIR . '/helpers/cleany-email-template-helper.class.php' );
include_once( CUARDE_INCLUDES_DIR . '/helpers/muscard-email-template-helper.class.php' );
include_once( CUARDE_INCLUDES_DIR . '/helpers/textura-email-template-helper.class.php' );

include_once( CUARDE_INCLUDES_DIR . '/design-extras-addon.class.php' );
