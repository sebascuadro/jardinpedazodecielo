<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once( CUAR_INCLUDES_DIR . '/core-classes/addon-page.class.php' );
require_once( CUAR_INCLUDES_DIR . '/core-classes/widget-content-dates.class.php' );

if (!class_exists('CUAR_ConversationDatesWidget')) :

/**
 * Widget to show private page categories
*
* @author Vincent Prat @ MarvinLabs
*/
class CUAR_ConversationDatesWidget extends CUAR_ContentDatesWidget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
				'cuar_conversation_archives', 
				__('WPCA - Conversation Archives', 'cuarme'),
				array( 
						'description' => __( 'Shows the conversation yearly archives of the Customer Area', 'cuarme' ), 
					)
			);

		add_filter( 'cuar/core/widget/query-args?widget-id=' . $this->id_base, array( &$this, 'change_query_parameters' ) );
	}

	protected function get_post_type() {
		return CUAR_Conversation::$POST_TYPE;
	}
	
	protected function get_default_title() {
		return __( 'Archives', 'cuarme' );
	}
	
	protected function get_link( $year, $month=0 ) {
		/** @var CUAR_CustomerConversationsAddOn $cfp_addon */
		$cfp_addon = cuar_addon( 'customer-conversations' );
		return $cfp_addon->get_date_archive_url( $year, $month );
	}

	/**
	 * Include the conversations that got started by the current user in the query
	 */
	public function change_query_parameters( $args ) {
		$meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();		
		$meta_query = array_merge( $meta_query, array(
				array(
						'key'		=> CUAR_Conversation::$META_STARTED_BY,
						'value'		=> apply_filters( 'cuar/private-content/conversations/query/default/override-user-id', get_current_user_id() )
					)
			) );		
		$meta_query['relation'] = 'OR';
		
		$args['meta_query'] = $meta_query;
		
		return $args;
	}
	
}

endif; // if (!class_exists('CUAR_ConversationDatesWidget')) 
