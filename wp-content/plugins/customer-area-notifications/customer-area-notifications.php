<?php
/*
	Plugin Name: 	WP Customer Area - Notifications
	Description: 	Adds email notifications to the Customer Area plugin
	Plugin URI: 	http://wp-customerarea.com
	Version: 		6.5.0
	Author: 		MarvinLabs
	Author URI: 	http://www.marvinlabs.com
	Text Domain: 	cuarno
	Domain Path: 	/languages
*/

/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

//------------------------------------------------------------
// Main plugin detection stuff

include( dirname(__FILE__) . '/libs/cuar/cuar_commons.php' );
if ( cuar_is_main_plugin_missing() )
{
	return;
}

// End of main plugin detection stuff
//------------------------------------------------------------

define('CUARNO_STORE_ITEM_NAME', 'Customer Area – Notifications');
define('CUARNO_STORE_ITEM_ID', 4251);
define('CUARNO_PLUGIN_VERSION', '6.5.0');

define('CUARNO_PLUGIN_DIR', WP_PLUGIN_DIR . '/customer-area-notifications');
define('CUARNO_LANGUAGE_DIR', 'customer-area-notifications/languages');
define('CUARNO_INCLUDES_DIR', CUARNO_PLUGIN_DIR . '/src/php');
define('CUARNO_PLUGIN_FILE', CUARNO_PLUGIN_DIR . '/customer-area-notifications.php');


// Load the addon
include_once( CUARNO_INCLUDES_DIR . '/helpers/notifications-admin-interface.class.php' );
include_once( CUARNO_INCLUDES_DIR . '/helpers/notifications-mailer-helper.class.php' );
include_once( CUARNO_INCLUDES_DIR . '/helpers/notifications-hooks-helper.class.php' );
include_once( CUARNO_INCLUDES_DIR . '/helpers/notifications-settings-helper.class.php' );
include_once( CUARNO_INCLUDES_DIR . '/helpers/notifications-logger.class.php' );
include_once( CUARNO_INCLUDES_DIR . '/helpers/notifications-placeholder-helper.class.php' );

include_once( CUARNO_INCLUDES_DIR . '/notifications-addon.class.php' );