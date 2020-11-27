<?php
/*
	Plugin Name: 	WP Customer Area - Front-office publishing
	Description: 	Publish and edit private content from your website frontend
	Plugin URI: 	http://wp-customerarea.com
	Version: 		4.2.0
	Author: 		MarvinLabs
	Author URI: 	http://www.marvinlabs.com
	Text Domain: 	cuarco
	Domain Path: 	/languages
*/

/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

//------------------------------------------------------------
// Main plugin detection stuff

include(dirname(__FILE__) . '/libs/cuar/cuar_commons.php');
if (cuar_is_main_plugin_missing()) return;

// End of main plugin detection stuff
//------------------------------------------------------------

define( 'CUARCO_STORE_ITEM_NAME', 	'Customer Area – Collaboration' );
define( 'CUARCO_STORE_ITEM_ID', 	4239 );
define( 'CUARCO_PLUGIN_VERSION', 	'4.2.0' );

define( 'CUARCO_PLUGIN_DIR', 	WP_PLUGIN_DIR . '/customer-area-collaboration' );
define( 'CUARCO_LANGUAGE_DIR', 	'customer-area-collaboration/languages' );
define( 'CUARCO_INCLUDES_DIR', 	CUARCO_PLUGIN_DIR . '/src/php' );
define( 'CUARCO_PLUGIN_FILE',	CUARCO_PLUGIN_DIR . '/customer-area-collaboration.php' );

// Load the addon
include_once( CUARCO_INCLUDES_DIR . '/customer-new-private-page/customer-new-private-page-addon.class.php' );
include_once( CUARCO_INCLUDES_DIR . '/customer-update-private-page/customer-update-private-page-addon.class.php' );

include_once( CUARCO_INCLUDES_DIR . '/customer-new-private-file/customer-new-private-file-addon.class.php' );
include_once( CUARCO_INCLUDES_DIR . '/customer-update-private-file/customer-update-private-file-addon.class.php' );

include_once( CUARCO_INCLUDES_DIR . '/collaboration/collaboration-addon.class.php' );
