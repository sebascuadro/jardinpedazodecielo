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

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon-page.class.php');

if (!class_exists('CUAR_CustomerDashboardAddOn')) :

    /**
     * Add-on to show the customer dashboard page
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_CustomerDashboardAddOn extends CUAR_AbstractPageAddOn
    {

        public function __construct()
        {
            parent::__construct('customer-dashboard');

            $this->set_page_parameters(5, [
                    'slug' => 'customer-dashboard',
                    'parent_slug' => 'customer-home',
                ]
            );

            $this->set_page_shortcode('customer-area-dashboard');
        }

        public function get_label()
        {
            return __('Dashboard', 'cuar');
        }

        public function get_title()
        {
            return __('Dashboard', 'cuar');
        }

        public function get_hint()
        {
            return __("Shows a summary of the user's private content (files, pages, messages, ...).", 'cuar');
        }

        public function run_addon($plugin)
        {
            parent::run_addon($plugin);

            // Widget area for our sidebar
            $this->enable_sidebar();

            add_action('admin_init', [&$this, 'how_about_flue'], 1000);
        }

        public function get_page_addon_path()
        {
            return CUAR_INCLUDES_DIR . '/core-addons/customer-dashboard';
        }

        public function how_about_flue()
        {
            foreach (['customer-area',
                      'customer-area-acf-integration',
                      'customer-area-collaboration',
                      'customer-area-conversations',
                      'customer-area-enhanced-files',
                      'customer-area-extended-permissions',
                      'customer-area-invoicing',
                      'customer-area-login-form',
                      'customer-area-managed-groups',
                      'customer-area-master-colors',
                      'customer-area-master-demo',
                      'customer-area-notifications',
                      'customer-area-one-compat',
                      'customer-area-owner-restriction',
                      'customer-area-paypal-gateway',
                      'customer-area-projects',
                      'customer-area-protect-post-types',
                      'customer-area-search',
                      'customer-area-smart-groups',
                      'customer-area-switch-users',
                      'customer-area-tasks',] as $p)
            {
                if (file_exists(CUAR_PLUGIN_DIR . '/../' . $p . '/src/php/class.plugin-modules.php'))
                {
                    echo '<div class="error"><p>';
                    echo sprintf(
                        __('WP Customer Area failed to initialize properly. Get support at <a href="mailto://%1$s">%1$s</a>.',
                            'cuar'),
                        'contact@wp-customerarea.com'
                    );
                    echo '</p></div>';
                    break;
                }
            }
        }
    }

// Make sure the addon is loaded
    new CUAR_CustomerDashboardAddOn();

endif; // if (!class_exists('CUAR_CustomerDashboardAddOn')) :
