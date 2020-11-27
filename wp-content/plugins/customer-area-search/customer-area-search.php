<?php
/*
 	Plugin Name: 	WP Customer Area - Search
	Description: 	Adds search capabilities to the Customer Area plugin
	Plugin URI: 	http://wp-customerarea.com
	Version: 		3.1.2
	Author: 		MarvinLabs
	Author URI: 	http://www.marvinlabs.com
	Text Domain: 	cuarse
	Domain Path: 	/languages
*/

/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

//------------------------------------------------------------
// Main plugin detection stuff

include(dirname(__FILE__) . '/libs/cuar/cuar_commons.php');
if (cuar_is_main_plugin_missing()) return;

// End of main plugin detection stuff
//------------------------------------------------------------

define( 'CUARSE_STORE_ITEM_NAME',   'Customer Area – Search' );
define( 'CUARSE_STORE_ITEM_ID', 	4231 );
define( 'CUARSE_PLUGIN_VERSION', 	'3.1.2' );

define( 'CUARSE_PLUGIN_DIR', 	WP_PLUGIN_DIR . '/customer-area-search' );
define( 'CUARSE_LANGUAGE_DIR', 	'customer-area-search/languages' );
define( 'CUARSE_INCLUDES_DIR', 	CUARSE_PLUGIN_DIR . '/src/php' );
define( 'CUARSE_PLUGIN_FILE',	CUARSE_PLUGIN_DIR . '/customer-area-search.php' );

// Load the addon
include_once( CUARSE_INCLUDES_DIR . '/search-addon.class.php' );
include_once( CUARSE_INCLUDES_DIR . '/search-page-addon.class.php' );