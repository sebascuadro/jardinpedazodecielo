<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon-page.class.php');

if ( !class_exists('CUAR_SearchPageAddOn')) :

    /**
     * Add-on to show the search form and results in the frontend
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_SearchPageAddOn extends CUAR_AbstractPageAddOn
    {

        public function __construct()
        {
            parent::__construct('customer-search');

            $this->set_page_parameters(830, array(
                    'slug'        => 'customer-search',
                    'parent_slug' => 'customer-home'
                )
            );

            $this->set_page_shortcode('customer-area-search');
        }

        public function get_label()
        {
            return __('Private Content - Search', 'cuarse');
        }

        public function get_title()
        {
            return __('Search', 'cuarse');
        }

        public function get_hint()
        {
            return __('Page to search the private content owned by a user.', 'cuarse');
        }

        public function run_addon($plugin)
        {
            parent::run_addon($plugin);

            // Widget area for our sidebar
            $this->enable_sidebar(array(), true);

            if (is_admin())
            {
            }
            else
            {
                add_filter('template_redirect', array(&$this, 'handle_form_submission'), 1000);
            }
        }

        public function get_page_addon_path()
        {
            return CUARSE_INCLUDES_DIR;
        }

        /*------- FORM HANDLING -----------------------------------------------------------------------------------------*/

        public function handle_form_submission()
        {
            $this->initialise_search_criteria();

            if ( !isset($_POST['cuar_search_nonce']) &&  !isset($_POST['cuar_query'])) return false;

            if ( !wp_verify_nonce($_POST["cuar_search_nonce"], 'cuar_search'))
            {
                die('An attempt to bypass security checks was detected! Please go back and try again.');
            }

            $this->show_results = true;

            return true;
        }

        /*------- PAGE HANDLING -----------------------------------------------------------------------------------------*/

        public function print_search_form()
        {
            include($this->plugin->get_template_file_path(
                $this->get_page_addon_path(),
                "customer-search-form.template.php",
                'templates'));
        }

        public function print_search_form_minimal()
        {
            include($this->plugin->get_template_file_path(
                $this->get_page_addon_path(),
                "customer-search-form-minimal.template.php",
                'templates'));
        }

        public function print_form_header()
        {
            printf('<form name="%1$s" method="post" class="cuar-big-form cuar-%1$s-form cuar-clearfix" action="%2$s">', 'search', $this->get_page_url());

            wp_nonce_field('cuar_search', 'cuar_search_nonce');

            if ( !empty($this->form_errors))
            {
                foreach ($this->form_errors as $error)
                {
                    if (is_wp_error($error))
                    {
                        printf('<p class="alert alert-warning">%s</p>', $error->get_error_message());
                    }
                    else if ($error !== false && !empty($error) && !is_array($error))
                    {
                        printf('<p class="alert alert-info">%s</p>', $error);
                    }
                }
            }
        }

        public function print_form_footer()
        {
            echo '</form>';
        }

        public function print_search_results()
        {
            if ( !$this->show_results) return;

            /** @var CUAR_SearchAddOn $se_addon */
            $se_addon = $this->plugin->get_addon('search');
            $content_result = $se_addon->find_private_content($this->criteria);
            $container_result = $se_addon->find_private_containers($this->criteria);

            $search_result = array_merge($content_result, $container_result);

            if ($search_result === false || empty($search_result))
            {
                include($this->plugin->get_template_file_path(
                    $this->get_page_addon_path(),
                    "customer-search-results-empty.template.php",
                    'templates'));
            }
            else
            {
                include($this->plugin->get_template_file_path(
                    $this->get_page_addon_path(),
                    "customer-search-results.template.php",
                    'templates'));
            }
        }

        private function initialise_search_criteria()
        {
            $this->criteria['post_type'] = isset($_POST['cuar_post_type']) ? $_POST['cuar_post_type'] : 'any';
            $this->criteria['query'] = isset($_POST['cuar_query']) ? $_POST['cuar_query'] : '';
            $this->criteria['owner_id'] = isset($_POST['cuar_owner_id']) ? $_POST['cuar_owner_id'] : get_current_user_id();
            $this->criteria['limit'] = isset($_POST['cuar_limit']) ? (int)($_POST['cuar_limit']) : 10;
            $this->criteria['author_id'] = isset($_POST['cuar_author_id']) ? $_POST['cuar_author_id'] : -1;
            $this->criteria['sort_by'] = isset($_POST['cuar_sort_by']) ? $_POST['cuar_sort_by'] : 'date';
            $this->criteria['sort_order'] = isset($_POST['cuar_sort_order']) ? $_POST['cuar_sort_order'] : 'ASC';

            $this->criteria = apply_filters('cuar/search/default-criteria', $this->criteria);
        }

        private function get_post_type_label($post_type)
        {
            $descriptors = $this->get_post_type_descriptors();

            if (isset($descriptors[$post_type]))
            {
                return $descriptors[$post_type]['label'];
            }

            return $post_type;
        }

        private function get_post_type_template($post_type)
        {
            $descriptors = $this->get_post_type_descriptors();

            if (isset($descriptors[$post_type]) && !empty($descriptors[$post_type]['template']))
            {
                return $descriptors[$post_type]['template'];
            }

            return $this->plugin->get_template_file_path(
                $this->get_page_addon_path(),
                "customer-search-content-item.template.php",
                'templates');
        }

        /**
         * Get the post type descriptors. Each descriptor is an array with:
         * - 'template'    - to use when displaying results
         * - 'label'        - to show in various places (section titles, search fields, ...)
         *
         * @return array
         */
        private function get_post_type_descriptors()
        {
            if ($this->post_type_descriptors == null)
            {
                $this->post_type_descriptors = array();

                $types = $this->plugin->get_private_types();
                foreach ($types as $post_type => $desc)
                {
                    /** @var CUAR_AbstractPageAddOn $cp_addon */
                    $cp_addon = $this->plugin->get_addon($desc['content-page-addon']);

                    $this->post_type_descriptors[$post_type]['label'] = $desc['label-plural'];

                    $template_prefix = isset($desc['content-page-addon']) ? $desc['content-page-addon'] : $post_type;
                    $template_paths = isset($desc['content-page-addon'])
                        ? array(
                            $this->get_page_addon_path(),
                            $cp_addon->get_page_addon_path()
                        )
                        : $this->get_page_addon_path();

                    $this->post_type_descriptors[$post_type]['template'] = $this->plugin->get_template_file_path(
                        $template_paths,
                        array(
                            $template_prefix . "-content-item-search.template.php",
                            "customer-search-content-item.template.php"
                        ),
                        'templates'
                    );
                }

                $this->post_type_descriptors = apply_filters('cuar/search/item-templates', $this->post_type_descriptors);
            }

            return $this->post_type_descriptors;
        }

        /**
         * Enqueues the select script on the user-edit and profile screens.
         */
        public function enqueue_scripts()
        {
            if (is_admin())
            {
                $this->plugin->enable_library('jquery.select2');
            }
        }

        protected $criteria = array();

        protected $show_results = false;

        protected $post_type_descriptors = null;

        protected $form_errors = array();

        protected $form_messages = array();
    }

// Load the add-on
    new CUAR_SearchPageAddOn();

endif; // if (!class_exists('CUAR_SearchPageAddOn')) :/ if (!class_exists('CUAR_SearchPageAddOn')) :
