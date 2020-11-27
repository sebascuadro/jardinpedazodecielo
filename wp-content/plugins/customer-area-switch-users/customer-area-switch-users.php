<?php
/*
	Plugin Name: 	WP Customer Area - Switch Users
	Description: 	Allow some roles to view the private area of other users without logging out
	Plugin URI: 	http://wp-customerarea.com
	Version: 		4.1.2
	Author: 		MarvinLabs
	Author URI: 	http://www.marvinlabs.com
	Text Domain: 	cuarsu
	Domain Path: 	/languages
*/

/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

//------------------------------------------------------------
// Main plugin detection stuff

include(dirname(__FILE__) . '/libs/cuar/cuar_commons.php');
if (cuar_is_main_plugin_missing()) return;

// End of main plugin detection stuff
//------------------------------------------------------------

define( 'CUARSU_STORE_ITEM_NAME', 	'Customer Area – Switch Users' );
define( 'CUARSU_STORE_ITEM_ID', 	4094 );
define( 'CUARSU_PLUGIN_VERSION', 	'4.1.2' );

define( 'CUARSU_PLUGIN_DIR', 	WP_PLUGIN_DIR . '/customer-area-switch-users' );
define( 'CUARSU_PLUGIN_URL', 	plugins_url() . '/customer-area-switch-users' );
define( 'CUARSU_LANGUAGE_DIR', 	'customer-area-switch-users/languages' );
define( 'CUARSU_INCLUDES_DIR', 	CUARSU_PLUGIN_DIR . '/src/php' );
define( 'CUARSU_PLUGIN_FILE',	CUARSU_PLUGIN_DIR . '/customer-area-switch-users.php' );


// Load the addon
include_once( CUARSU_INCLUDES_DIR . '/switch-users-addon.class.php' );
