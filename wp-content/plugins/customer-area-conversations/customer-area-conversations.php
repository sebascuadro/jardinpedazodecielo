<?php
/*
	Plugin Name: 	WP Customer Area - Conversations
	Description: 	Adds private conversations between users of your customer area
	Plugin URI: 	http://wp-customerarea.com
	Version: 		4.3.4
	Author: 		MarvinLabs
	Author URI: 	http://www.marvinlabs.com
	Text Domain: 	cuarme
	Domain Path: 	/languages
*/

/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

//------------------------------------------------------------
// Main plugin detection stuff

include(dirname(__FILE__) . '/libs/cuar/cuar_commons.php');
if (cuar_is_main_plugin_missing()) return;

// End of main plugin detection stuff
//------------------------------------------------------------

define( 'CUARME_STORE_ITEM_NAME', 	'Customer Area – Conversations' );
define( 'CUARME_STORE_ITEM_ID', 	4255 );
define( 'CUARME_PLUGIN_VERSION', 	'4.3.4' );

define( 'CUARME_PLUGIN_URL', 	plugins_url() . '/customer-area-conversations' );
define( 'CUARME_PLUGIN_DIR', 	WP_PLUGIN_DIR . '/customer-area-conversations' );
define( 'CUARME_LANGUAGE_DIR', 	'customer-area-conversations/languages' );
define( 'CUARME_INCLUDES_DIR', 	CUARME_PLUGIN_DIR . '/src/php' );
define( 'CUARME_PLUGIN_FILE',	CUARME_PLUGIN_DIR . '/customer-area-conversations.php' );

// Load the addon
include_once( CUARME_INCLUDES_DIR . '/conversation.class.php' );
include_once( CUARME_INCLUDES_DIR . '/conversation-reply.class.php' );

include_once( CUARME_INCLUDES_DIR . '/customer-conversations-home/customer-conversations-home-addon.class.php' );
include_once( CUARME_INCLUDES_DIR . '/customer-conversations/customer-conversations-addon.class.php' );
include_once( CUARME_INCLUDES_DIR . '/customer-new-conversation/customer-new-conversation-addon.class.php' );
include_once( CUARME_INCLUDES_DIR . '/customer-update-conversation/customer-update-conversation-addon.class.php' );

include_once( CUARME_INCLUDES_DIR . '/conversations/helpers/conversations-logger.class.php' );
include_once( CUARME_INCLUDES_DIR . '/conversations/helpers/conversations-admin-interface.class.php' );
include_once( CUARME_INCLUDES_DIR . '/conversations/helpers/conversations-editor-helper.class.php' );
include_once( CUARME_INCLUDES_DIR . '/conversations/conversations-addon.class.php' );

include_once( CUARME_INCLUDES_DIR . '/functions/functions-conversations.php' );
