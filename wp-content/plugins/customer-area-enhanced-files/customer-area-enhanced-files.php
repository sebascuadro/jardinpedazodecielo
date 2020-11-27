<?php
/*
	Plugin Name: 	WP Customer Area - Enhanced Files
	Description: 	Supercharge private files: custom captions, attach multiple files to a single post, etc.
	Plugin URI: 	http://wp-customerarea.com
	Version: 		2.1.1
	Author: 		MarvinLabs
	Author URI: 	http://www.marvinlabs.com
	Text Domain: 	cuaref
	Domain Path: 	/languages
*/

/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

//------------------------------------------------------------
// Main plugin detection stuff

include(dirname(__FILE__) . '/libs/cuar/cuar_commons.php');
if (cuar_is_main_plugin_missing()) return;

// End of main plugin detection stuff
//------------------------------------------------------------

define('CUAREF_STORE_ITEM_NAME', 'Customer Area – Enhanced Files');
define('CUAREF_STORE_ITEM_ID', 13394);
define('CUAREF_PLUGIN_VERSION', '2.1.1');

define('CUAREF_PLUGIN_DIR', WP_PLUGIN_DIR . '/customer-area-enhanced-files');
define('CUAREF_PLUGIN_URL', plugins_url() . '/customer-area-enhanced-files');
define('CUAREF_LANGUAGE_DIR', 'customer-area-enhanced-files/languages');
define('CUAREF_INCLUDES_DIR', CUAREF_PLUGIN_DIR . '/src/php');
define('CUAREF_PLUGIN_FILE', CUAREF_PLUGIN_DIR . '/customer-area-enhanced-files.php');


// Load the addon
include_once(CUAREF_INCLUDES_DIR . '/enhanced-files-addon.class.php');
