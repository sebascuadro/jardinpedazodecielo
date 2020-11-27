<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon-content-page.class.php');

require_once(dirname(__FILE__) . '/widget-private-file-authors.class.php');
require_once(dirname(__FILE__) . '/widget-private-file-categories.class.php');
require_once(dirname(__FILE__) . '/widget-private-file-dates.class.php');
require_once(dirname(__FILE__) . '/widget-private-files.class.php');

if ( !class_exists('CUAR_CustomerPrivateFilesAddOn')) :

    /**
     * Add-on to put private files in the customer area
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_CustomerPrivateFilesAddOn extends CUAR_AbstractContentPageAddOn
    {

        public function __construct()
        {
            parent::__construct('customer-private-files');

            $this->set_page_parameters(510, array(
                    'slug'                => 'customer-private-files',
                    'parent_slug'         => 'customer-private-files-home',
                    'friendly_post_type'  => 'cuar_private_file',
                    'friendly_taxonomy'   => 'cuar_private_file_category',
                    'required_capability' => 'cuar_view_files'
                )
            );

            $this->set_page_shortcode('customer-area-private-files');
        }

        public function get_label()
        {
            return __('Private Files - Owned', 'cuar');
        }

        public function get_title()
        {
            return __('My files', 'cuar');
        }

        public function get_hint()
        {
            return __('Page to list the files a customer owns.', 'cuar');
        }

        public function run_addon($plugin)
        {
            $this->pf_addon = $plugin->get_addon('private-files');

            parent::run_addon($plugin);

            // This page can also list archive for private content
            $this->enable_content_archives_permalinks();
            $this->enable_single_private_content_permalinks();

            // Widget area for our sidebar
            if ($this->pf_addon->is_enabled())
            {
                $this->enable_sidebar(array(
                    'CUAR_PrivateFileCategoriesWidget', 'CUAR_PrivateFileDatesWidget', 'CUAR_PrivateFilesWidget', 'CUAR_PrivateFileAuthorsWidget'
                ), true);
            }

            if (is_admin())
            {
                $this->enable_settings('cuar_private_files');
            }
            else
            {
                add_filter('cuar/core/page/toolbar/project-content-submenu-dropdown-items',
                    [&$this, 'toolbar_add_links_to_project_dropdown'], 10);
                add_filter('cuar/core/ownership/owner-submenu-items',
                    [&$this, 'owner_add_links_to_dropdown_menu'], 10, 3);
            }
        }

        public function get_page_addon_path()
        {
            return CUAR_INCLUDES_DIR . '/core-addons/customer-private-files';
        }

        protected function get_author_archive_page_subtitle($author_id)
        {
            if ($author_id == get_current_user_id())
            {
                return __('Files you created', 'cuar');
            }

            $author = get_userdata($author_id);

            return sprintf(__('Files created by %1$s', 'cuar'), $author->display_name);
        }

        protected function get_category_archive_page_subtitle($category)
        {
            return sprintf(__('Files under %1$s', 'cuar'), $category->name);
        }

        protected function get_date_archive_page_subtitle($year, $month = 0)
        {
            if (isset($month) && ((int)($month) > 0))
            {
                $month_name = date_i18n("F", mktime(0, 0, 0, (int)$month, 10));
                $page_subtitle = sprintf(__('Files published in %2$s %1$s', 'cuar'), $year, $month_name);
            }
            else
            {
                $page_subtitle = sprintf(__('Files published in %1$s', 'cuar'), $year);
            }

            return $page_subtitle;
        }

        protected function get_default_page_subtitle()
        {
            return __('Files', 'cuar');
        }

        protected function get_default_dashboard_block_title()
        {
            return __('Recent Files', 'cuar');
        }

        protected function print_default_widgets()
        {
            $w = new CUAR_PrivateFileCategoriesWidget();
            $w->widget($this->get_default_widget_args($w->id_base), array(
                'title' => __('Categories', 'cuar'),
            ));

            $w = new CUAR_PrivateFileDatesWidget();
            $w->widget($this->get_default_widget_args($w->id_base), array(
                'title' => __('Archives', 'cuar'),
            ));

            $w = new CUAR_PrivateFileAuthorsWidget();
            $w->widget($this->get_default_widget_args($w->id_base), array(
                'title' => __('Created By', 'cuar'),
            ));
        }

        /**
         * Add some links to the single-project dropdown button
         * (requires Front-Office add-on)
         *
         * @param $links array Current dropdown links
         *
         * @return array New dropdown links
         */
        public function toolbar_add_links_to_project_dropdown($links)
        {
            if (!$this->pf_addon->is_enabled() || !class_exists('CUAR_CollaborationAddon'))
            {
                return $links;
            }

            /** @var CUAR_CustomerNewFileAddOn $new_file_addon */
            $new_file_addon = $this->plugin->get_addon('customer-new-private-file');
            $new_file_addon_id = $new_file_addon->get_page_id();

            $links[] = [
                'title' => __('Attach new file', 'cuar'),
                'url' => CUAR_CollaborationAddOn::get_owners_autofill_permalink($new_file_addon_id, 'prj,' . get_queried_object_id()),
                'tooltip' => __('Create a new file linked to this project', 'cuar'),
                'extra_class' => 'text-right',
            ];

            return $links;
        }

        /**
         * Add some links to the user dropdown menus
         * (requires Front-Office add-on)
         *
         * @param $links      array Current dropdown links
         * @param $owner_id   integer The owner unique ID
         * @param $owner_type string The owner type
         *
         * @return array New dropdown links
         */
        public function owner_add_links_to_dropdown_menu($links, $owner_id, $owner_type)
        {
            if (!$this->pf_addon->is_enabled() || !class_exists('CUAR_CollaborationAddon'))
            {
                return $links;
            }

            /** @var CUAR_CustomerNewFileAddOn $new_file_addon */
            $new_file_addon = $this->plugin->get_addon('customer-new-private-file');
            $new_file_addon_id = $new_file_addon->get_page_id();

            if (!$new_file_addon->current_user_can_select_owner() || !$new_file_addon->current_user_can_create_content())
            {
                return $links;
            }

            $links[] = [
                'title' => __('Assign new file', 'cuar'),
                'url' => CUAR_CollaborationAddOn::get_owners_autofill_permalink($new_file_addon_id, $owner_type . ',' .
                                                                                                    $owner_id),
                'tooltip' => __('Create a new file assigned to this owner', 'cuar'),
                'extra_class' => '',
            ];

            return $links;
        }

        /*------- SETTINGS ACCESSORS ------------------------------------------------------------------------------------*/

        public function is_hide_contents_created_by_user_enabled()
        {
            return $this->pf_addon->is_enabled() && parent::is_hide_contents_created_by_user_enabled();
        }
        public function is_show_in_dashboard_enabled()
        {
            return $this->pf_addon->is_enabled() && parent::is_show_in_dashboard_enabled();
        }

        public function is_show_in_single_post_footer_enabled()
        {
            return $this->pf_addon->is_enabled() && parent::is_show_in_single_post_footer_enabled();
        }

        /** @var CUAR_PrivateFileAddOn */
        private $pf_addon;
    }

// Make sure the addon is loaded
    new CUAR_CustomerPrivateFilesAddOn();

endif; // if (!class_exists('CUAR_CustomerPrivateFilesAddOn')) 
