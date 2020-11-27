<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once( CUAR_INCLUDES_DIR . '/core-classes/addon-root-page.class.php' );

if (!class_exists('CUAR_CustomerConversationsHomeAddOn')) :

/**
 * Add-on to put private files in the customer area
*
* @author Vincent Prat @ MarvinLabs
*/
class CUAR_CustomerConversationsHomeAddOn extends CUAR_RootPageAddOn {
	
	public function __construct() {
		parent::__construct('customer-conversations-home', 'customer-conversations');
		
		$this->set_page_parameters( 200, array(
					'slug'					=> 'customer-conversations-home',
					'parent_slug'			=> 'customer-home',
					'friendly_post_type'	=> CUAR_Conversation::$POST_TYPE,
					'required_capability'	=> 'cuarme_view_conversations'
				)
			);
		
		$this->set_page_shortcode( 'customer-area-conversations-home' );
	}
	
	public function get_label() {
		return __( 'Conversations - Home', 'cuarme' );
	}
	
	public function get_title() {
		return __( 'Conversations', 'cuarme' );
	}		
		
	public function get_hint() {
		return __( 'Root page for the customer conversations.', 'cuarme' );
	}	

	public function get_page_addon_path() {
		return CUARME_INCLUDES_DIR . '/customer-conversations';
	}
}

// Make sure the addon is loaded
new CUAR_CustomerConversationsHomeAddOn();

endif; // if (!class_exists('CUAR_CustomerConversationsHomeAddOn')) 
