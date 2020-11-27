<?php
/*
	Plugin Name: 	WP Customer Area - Smart Groups
	Description: 	Create dynamic groups of customers based on their profile information.
	Plugin URI: 	http://wp-customerarea.com
	Version: 		2.1.1
	Author: 		MarvinLabs
	Author URI: 	http://www.marvinlabs.com
	Text Domain: 	cuarsg
	Domain Path: 	/languages
*/

/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

//------------------------------------------------------------
// Main plugin detection stuff

include(dirname(__FILE__) . '/libs/cuar/cuar_commons.php');
if (cuar_is_main_plugin_missing()) return;

// End of main plugin detection stuff
//------------------------------------------------------------

define( 'CUARSG_STORE_ITEM_NAME', 	'Customer Area – Smart Groups' );
define( 'CUARSG_STORE_ITEM_ID', 	9893 );
define( 'CUARSG_PLUGIN_VERSION', 	'2.1.1' );

define( 'CUARSG_PLUGIN_DIR', 	WP_PLUGIN_DIR . '/customer-area-smart-groups' );
define( 'CUARSG_LANGUAGE_DIR', 	'customer-area-smart-groups/languages' );
define( 'CUARSG_INCLUDES_DIR', 	CUARSG_PLUGIN_DIR . '/src/php' );
define( 'CUARSG_PLUGIN_FILE',	CUARSG_PLUGIN_DIR . '/customer-area-smart-groups.php' );

// Load the addon
include_once( CUARSG_INCLUDES_DIR . '/smart-groups-addon.class.php' );