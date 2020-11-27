<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon-update-content-page.class.php');

if ( !class_exists('CUAR_CustomerUpdateConversationAddOn')) :

    /**
     * Add-on to edit conversations in the customer area
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_CustomerUpdateConversationAddOn extends CUAR_AbstractUpdateContentPageAddOn
    {

        public function __construct()
        {
            parent::__construct('customer-update-conversation');

            $this->set_page_parameters(650, array(
                    'slug'               => 'customer-update-conversation',
                    'parent_slug'        => 'customer-conversations',
                    'friendly_post_type' => CUAR_Conversation::$POST_TYPE,
                )
            );

            $this->set_page_shortcode('customer-area-update-conversation');
        }

        public function get_label()
        {
            return __('Private Conversations - Update', 'cuarme');
        }

        public function get_title()
        {
            return __('Update conversation', 'cuarme');
        }

        public function get_hint()
        {
            return __('Page to update conversations.', 'cuarme');
        }

        public function run_addon($plugin)
        {
            parent::run_addon($plugin);

            if (is_admin())
            {
                $this->enable_settings('cuar_conversations');
            }
        }

        public function get_page_addon_path()
        {
            return CUARME_INCLUDES_DIR . '/customer-update-conversation';
        }

        /*------- FORM HANDLING -----------------------------------------------------------------------------------------*/

        protected function get_default_required_fields()
        {
            return array('cuar_title', 'cuar_content', 'cuar_owner');
        }

        protected function do_edit_content($action, $form_data)
        {
            if (parent::do_edit_content($action, $form_data) === true)
            {
                return true;
            }

            $post_id = $this->get_current_post_id();
            $title = $this->check_submitted_title($form_data, __('The title cannot be empty', 'cuarme'));
            $message = $this->check_submitted_content($form_data, __('The content cannot be empty', 'cuarme'));
            $recipients = $this->check_submitted_owners($form_data, __('You must select at least one owner', 'cuarme'));

            if ($post_id > 0 && $title !== false && $message != false && $recipients != false)
            {
                /** @var CUAR_ConversationsAddOn $me_addon */
                $me_addon = $this->plugin->get_addon('conversations');
                $post_id = $me_addon->editor()->update_conversation($post_id, $title, $message);

                if (is_wp_error($post_id))
                {
                    /** @var WP_Error $post_id */
                    $this->form_errors[] = $post_id->get_error_message();
                }
            }

            if (empty($this->form_errors))
            {
                /** @var CUAR_PostOwnerAddOn $po_addon */
                $po_addon = $this->plugin->get_addon('post-owner');
                $po_addon->save_post_owners($post_id, $recipients);

                /** @var CUAR_CustomerPagesAddOn $cp_addon */
                $cp_addon = $this->plugin->get_addon('customer-pages');
                $this->set_form_success(
                    __('Done', 'cuarme'),
                    __('The conversation has been updated.', 'cuarme'),
                    array(
                        array(
                            'title' => __('Back to conversations', 'cuarme'),
                            'url'   => $cp_addon->get_page_url($this->get_parent_slug()),
                            'icon'  => 'fa fa-arrow-circle-left',
                        ),
                        array(
                            'title' => __('View it', 'cuarme'),
                            'url'   => get_permalink($this->get_current_post_id()),
                            'icon'  => 'fa fa-arrow-circle-right',
                        ),
                    ));
            }

            return empty($this->form_errors);
        }
    }

// Make sure the addon is loaded
    new CUAR_CustomerUpdateConversationAddOn();

endif; // if (!class_exists('CUAR_CustomerUpdateConversationAddOn')) 
