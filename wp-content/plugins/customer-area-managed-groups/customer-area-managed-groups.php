<?php
/*
	Plugin Name: 	WP Customer Area - Managed Groups
	Description: 	Create groups of customers who are managed by another user.
	Plugin URI: 	http://wp-customerarea.com
	Version: 		4.1.0
	Author: 		MarvinLabs
	Author URI: 	http://www.marvinlabs.com
	Text Domain: 	cuarmg
	Domain Path: 	/languages
*/

/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

//------------------------------------------------------------
// Main plugin detection stuff

include(dirname(__FILE__) . '/libs/cuar/cuar_commons.php');
if (cuar_is_main_plugin_missing()) return;

// End of main plugin detection stuff
//------------------------------------------------------------

define( 'CUARMG_STORE_ITEM_NAME', 	'Customer Area – Managed Groups' );
define( 'CUARMG_STORE_ITEM_ID', 	4252 );
define( 'CUARMG_PLUGIN_VERSION', 	'4.1.0' );

define( 'CUARMG_PLUGIN_DIR', 	WP_PLUGIN_DIR . '/customer-area-managed-groups' );
define( 'CUARMG_LANGUAGE_DIR', 	'customer-area-managed-groups/languages' );
define( 'CUARMG_INCLUDES_DIR', 	CUARMG_PLUGIN_DIR . '/src/php' );
define( 'CUARMG_PLUGIN_FILE',	CUARMG_PLUGIN_DIR . '/customer-area-managed-groups.php' );

// Load the addon
include_once( CUARMG_INCLUDES_DIR . '/managed-groups-addon.class.php' );