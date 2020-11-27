<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon-create-content-page.class.php');

if ( !class_exists('CUAR_CustomerNewConversationAddOn')) :

    /**
     * Add-on to put conversations in the customer area
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_CustomerNewConversationAddOn extends CUAR_AbstractCreateContentPageAddOn
    {

        public function __construct()
        {
            parent::__construct('customer-new-conversation');

            $this->set_page_parameters(220, array(
                    'slug'               => 'customer-new-conversation',
                    'parent_slug'        => 'customer-conversations-home',
                    'friendly_post_type' => CUAR_Conversation::$POST_TYPE,
                )
            );

            $this->set_page_shortcode('customer-area-new-conversation');
        }

        public function get_label()
        {
            return __('Conversations - New', 'cuarme');
        }

        public function get_title()
        {
            return __('Start conversation', 'cuarme');
        }

        public function get_hint()
        {
            return __('Page to create new conversations.', 'cuarme');
        }

        public function run_addon($plugin)
        {
            parent::run_addon($plugin);

            if (is_admin())
            {
                $this->enable_settings('cuar_conversations', array('rich-editor', 'default-ownership'));
            }
        }

        public function get_page_addon_path()
        {
            return CUARME_INCLUDES_DIR . '/customer-new-conversation';
        }

        /*------- FORM HANDLING -----------------------------------------------------------------------------------------*/

        protected function get_redirect_url_after_action()
        {
            return null;
        }

        protected function do_edit_content($action, $form_data)
        {
            if (parent::do_edit_content($action, $form_data) === true)
            {
                return true;
            }

            $title = $this->check_submitted_title($form_data, __('The topic cannot be empty', 'cuarme'));
            $message = $this->check_submitted_content($form_data, __('The message cannot be empty', 'cuarme'));
            $recipients = $this->check_submitted_owners($form_data, __('You must select at least one recipient', 'cuarme'));
            $post_status = $this->get_default_publish_status();

            if ($title !== false && $message != false && $recipients != false)
            {
                /** @var CUAR_ConversationsAddOn $co_addon */
                $co_addon = $this->plugin->get_addon('conversations');
                $post_id = $co_addon->editor()->add_conversation(get_current_user_id(), $title, $message, $post_status);

                if (is_wp_error($post_id))
                {
                    /** @var WP_Error $post_id */
                    $this->form_errors[] = $post_id->get_error_message();
                }
                else
                {
                    /** @var CUAR_PostOwnerAddOn $po_addon */
                    $po_addon = $this->plugin->get_addon('post-owner');
                    $po_addon->save_post_owners($post_id, $recipients);

                    $this->set_current_post_id($post_id);
                }
            }

            if (empty($this->form_errors))
            {
                $conversation = new CUAR_Conversation($post_id);
                do_action("cuar/private-content/conversations/on-conversation-started", $post_id, $conversation->get_post());

                /** @var CUAR_CustomerPagesAddOn $cp_addon */
                $cp_addon = $this->plugin->get_addon('customer-pages');
                $this->set_form_success(
                    __('Done', 'cuarme'),
                    __('The conversation has been started.', 'cuarme'),
                    array(
                        array(
                            'title' => __('Back to conversations', 'cuarme'),
                            'url'   => $cp_addon->get_page_url($this->get_parent_slug()),
                            'icon'  => 'fa fa-arrow-circle-left',
                        ),
                        array(
                            'title' => __('Create more', 'cuarme'),
                            'url'   => $this->get_page_url(),
                            'icon'  => 'fa fa-plus-circle',
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
    new CUAR_CustomerNewConversationAddOn();

endif; // if (!class_exists('CUAR_CustomerNewConversationAddOn')) 
