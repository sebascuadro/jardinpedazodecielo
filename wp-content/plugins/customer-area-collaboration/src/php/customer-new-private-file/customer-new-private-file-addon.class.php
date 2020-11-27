<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon-create-content-page.class.php');

if (!class_exists('CUAR_CustomerNewFileAddOn')) :

    /**
     * Add-on to put private-files in the customer area
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_CustomerNewFileAddOn extends CUAR_AbstractCreateContentPageAddOn
    {

        public function __construct()
        {
            parent::__construct('customer-new-private-file');

            $this->set_page_parameters(550, [
                    'slug' => 'customer-new-private-file',
                    'parent_slug' => 'customer-private-files-home',
                    'friendly_post_type' => 'cuar_private_file',
                    'friendly_taxonomy' => 'cuar_private_file_category',
                ]
            );

            $this->set_page_shortcode('customer-area-new-private-file');
        }

        public function get_label()
        {
            return __('Private Files - New', 'cuarco');
        }

        public function get_title()
        {
            return __('Create file', 'cuarco');
        }

        public function get_hint()
        {
            return __('Page to create new private files.', 'cuarco');
        }

        public function run_addon($plugin)
        {
            parent::run_addon($plugin);

            if (is_admin())
            {
                $this->enable_settings('cuar_private_files');
            }
        }

        public function get_page_addon_path()
        {
            return CUARCO_INCLUDES_DIR . '/customer-new-private-file';
        }

        /*------- CREATION FORM -----------------------------------------------------------------------------------------*/

        protected function get_wizard_step_count()
        {
            return 2;
        }

        public function get_wizard_steps()
        {
            return [
                ['label' => __('File details', 'cuarco')],
                ['label' => __('Attachments', 'cuarco')],
            ];
        }

        /*------- FORM HANDLING -----------------------------------------------------------------------------------------*/

        protected function do_edit_content($action, $form_data)
        {
            if (parent::do_edit_content($action, $form_data) === true)
            {
                return true;
            }

            if ($this->get_current_wizard_step() === 0)
            {
                $title = $this->check_submitted_title($form_data, __('The title cannot be empty', 'cuarco'));
                $content = $this->check_submitted_content($form_data, __('The content cannot be empty', 'cuarco'));
                $owners = $this->check_submitted_owners($form_data, __('You must select at least one owner', 'cuarco'));
                $category = $this->check_submitted_category($form_data, __('You must select a category', 'cuarco'));
                $post_status = $this->get_default_publish_status();

                if ($title !== false && $content !== false && $owners !== false && $category !== false)
                {
                    /** @var CUAR_CollaborationAddOn $co_addon */
                    $co_addon = $this->plugin->get_addon('collaboration');
                    $post_id = $co_addon->create_private_content(
                        'cuar_private_file',
                        $title,
                        $content,
                        $owners,
                        $post_status,
                        $this->get_friendly_taxonomy(),
                        $category,
                        $this->current_user_can_select_owner()
                    );

                    if (is_array($post_id) && !empty($post_id))
                    {
                        $this->form_errors = array_merge($this->form_errors, $post_id);
                    }
                    else
                    {
                        $this->set_current_post_id($post_id);
                    }
                }
            }
            else
            {
                if (empty($this->form_errors))
                {
                    /** @var CUAR_CustomerPagesAddOn $cp_addon */
                    $cp_addon = $this->plugin->get_addon('customer-pages');
                    $this->set_form_success(
                        __('Done', 'cuarco'),
                        __('The file has been created.', 'cuarco'),
                        [
                            [
                                'title' => __('Back to files', 'cuarco'),
                                'url' => $cp_addon->get_page_url($this->get_parent_slug()),
                                'icon' => 'fa fa-arrow-circle-left',
                            ],
                            [
                                'title' => __('Create more', 'cuarco'),
                                'url' => $this->get_page_url(),
                                'icon' => 'fa fa-plus-circle',
                            ],
                            [
                                'title' => __('View it', 'cuarco'),
                                'url' => get_permalink($this->get_current_post_id()),
                                'icon' => 'fa fa-arrow-circle-right',
                            ],
                        ]);

                    $post_id = $this->get_current_post_id();
                    $post = get_post($post_id);

                    /** @var CUAR_PostOwnerAddOn $po_addon */
                    $po_addon = $this->plugin->get_addon('post-owner');
                    $owners = $po_addon->get_post_owners($post_id);

                    do_action('cuar/private-content/collaboration/on-post-created', $post_id, $post, $owners, []);
                }
            }

            return empty($this->form_errors);
        }
    }

// Make sure the addon is loaded
    new CUAR_CustomerNewFileAddOn();

endif; // if (!class_exists('CUAR_CustomerNewFileAddOn')) 
