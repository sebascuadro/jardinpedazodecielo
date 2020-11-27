<?php
/*
	Plugin Name: 	WP Customer Area - Owner Restriction
	Description: 	Restrict which owners can be chosen by users when they publish content
	Plugin URI: 	http://wp-customerarea.com
	Version: 		4.2.0
	Author: 		MarvinLabs
	Author URI: 	http://www.marvinlabs.com
	Text Domain: 	cuaror
	Domain Path: 	/languages
*/

/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

//------------------------------------------------------------
// Main plugin detection stuff

include(dirname(__FILE__) . '/libs/cuar/cuar_commons.php');
if (cuar_is_main_plugin_missing()) return;

// End of main plugin detection stuff
//------------------------------------------------------------

define( 'CUAROR_STORE_ITEM_NAME', 	'Customer Area – Advanced Owner Restriction' );
define( 'CUAROR_STORE_ITEM_ID', 	4256 );
define( 'CUAROR_PLUGIN_VERSION', 	'4.2.0' );

define( 'CUAROR_PLUGIN_DIR', 	WP_PLUGIN_DIR . '/customer-area-owner-restriction' );
define( 'CUAROR_LANGUAGE_DIR', 	'customer-area-owner-restriction/languages' );
define( 'CUAROR_INCLUDES_DIR', 	CUAROR_PLUGIN_DIR . '/src/php' );
define( 'CUAROR_PLUGIN_FILE',	CUAROR_PLUGIN_DIR . '/customer-area-owner-restriction.php' );

// Load the addon
include_once( CUAROR_INCLUDES_DIR . '/owner-restriction-addon.class.php' );
