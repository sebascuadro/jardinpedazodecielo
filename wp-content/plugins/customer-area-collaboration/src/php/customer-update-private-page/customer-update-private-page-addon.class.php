<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon-update-content-page.class.php');

if ( !class_exists('CUAR_CustomerUpdatePageAddOn')) :

    /**
     * Add-on to put private-pages in the customer area
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_CustomerUpdatePageAddOn extends CUAR_AbstractUpdateContentPageAddOn
    {

        public function __construct()
        {
            parent::__construct('customer-update-private-page');

            $this->set_page_parameters(650, array(
                    'slug'               => 'customer-update-private-page',
                    'parent_slug'        => 'customer-private-pages',
                    'friendly_post_type' => 'cuar_private_page',
                    'friendly_taxonomy'  => 'cuar_private_page_category',
                )
            );

            $this->set_page_shortcode('customer-area-update-private-page');
        }

        public function get_label()
        {
            return __('Private Pages - Update', 'cuarco');
        }

        public function get_title()
        {
            return __('Update page', 'cuarco');
        }

        public function get_hint()
        {
            return __('Page to update update private pages.', 'cuarco');
        }

        public function run_addon($plugin)
        {
            parent::run_addon($plugin);

            if (is_admin())
            {
                $this->enable_settings('cuar_private_pages');
            }
        }

        public function get_page_addon_path()
        {
            return CUARCO_INCLUDES_DIR . '/customer-update-private-page';
        }

        /*------- FORM HANDLING -----------------------------------------------------------------------------------------*/


        protected function do_edit_content($action, $form_data)
        {
            if (parent::do_edit_content($action, $form_data) === true)
            {
                return true;
            }

            $post_id = $this->get_current_post_id();
            $title = $this->check_submitted_title($form_data, __('The title cannot be empty', 'cuarco'));
            $content = $this->check_submitted_content($form_data, __('The content cannot be empty', 'cuarco'));
            $owners = $this->check_submitted_owners($form_data, __('You must select at least one owner', 'cuarco'));
            $category = $this->check_submitted_category($form_data, __('You must select a category', 'cuarco'));

            if ($post_id > 0 && $title !== false && $content !== false && $owners !== false && $category !== false)
            {
                /** @var CUAR_CollaborationAddOn $co_addon */
                $co_addon = $this->plugin->get_addon('collaboration');
                $errors = $co_addon->update_private_content(
                    'cuar_private_page',
                    $post_id,
                    $title,
                    $content,
                    $owners,
                    $this->get_friendly_taxonomy(),
                    $category,
                    $this->current_user_can_select_owner());

                if (is_array($errors) && !empty($errors))
                {
                    $this->form_errors = array_merge($this->form_errors, $errors);
                }
            }

            if (empty($this->form_errors))
            {
                /** @var CUAR_CustomerPagesAddOn $cp_addon */
                $cp_addon = $this->plugin->get_addon('customer-pages');
                $this->set_form_success(
                    __('Done', 'cuarco'),
                    __('The page has been updated.', 'cuarco'),
                    array(
                        array(
                            'title' => __('Back to pages', 'cuarco'),
                            'url'   => $cp_addon->get_page_url($this->get_parent_slug()),
                            'icon'  => 'fa fa-arrow-circle-left',
                        ),
                        array(
                            'title' => __('View it', 'cuarco'),
                            'url'   => get_permalink($this->get_current_post_id()),
                            'icon'  => 'fa fa-arrow-circle-right',
                        ),
                    ));
            }

            return empty($this->form_errors);
        }
    }

// Make sure the addon is loaded
    new CUAR_CustomerUpdatePageAddOn();

endif; // if (!class_exists('CUAR_CustomerUpdatePageAddOn')) 
