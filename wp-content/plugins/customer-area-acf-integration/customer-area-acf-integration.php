<?php
/*
	Plugin Name: 	WP Customer Area - ACF Integration
	Description: 	Integrates the <a href="http://www.advancedcustomfields.com/">Advanced Custom Fields</a> plugin to your Customer Area
	Plugin URI: 	http://wp-customerarea.com
	Version: 		5.0.0
	Author: 		MarvinLabs
	Author URI: 	http://www.marvinlabs.com
	Text Domain: 	cuaracf
	Domain Path: 	/languages
*/

/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

//------------------------------------------------------------
// Main plugin detection stuff

include(dirname(__FILE__) . '/libs/cuar/cuar_commons.php');
if (cuar_is_main_plugin_missing()) return;

// End of main plugin detection stuff
//------------------------------------------------------------

define( 'CUARACF_STORE_ITEM_NAME', 	'Customer Area – Advanced Custom Fields Integration' );
define( 'CUARACF_STORE_ITEM_ID', 	4257 );
define( 'CUARACF_PLUGIN_VERSION', 	'5.0.0' );

define( 'CUARACF_PLUGIN_DIR', 		WP_PLUGIN_DIR . '/customer-area-acf-integration' );
define( 'CUARACF_PLUGIN_URL', 		untrailingslashit(plugins_url()) . '/customer-area-acf-integration' );
define( 'CUARACF_LANGUAGE_DIR', 	'customer-area-acf-integration/languages' );
define( 'CUARACF_INCLUDES_DIR', 	CUARACF_PLUGIN_DIR . '/src/php' );
define( 'CUARACF_PLUGIN_FILE',		CUARACF_PLUGIN_DIR . '/customer-area-acf-integration.php' );


// Load the addon
include_once( CUARACF_INCLUDES_DIR . '/acf-integration-installer.class.php' );
include_once( CUARACF_INCLUDES_DIR . '/acf-integration-addon.class.php' );

// Some hooks for activation, deactivation, ...
register_deactivation_hook( __FILE__, array( 'CUAR_ACFIntegrationInstaller', 'on_deactivate' ) );
