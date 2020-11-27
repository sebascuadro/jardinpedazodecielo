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

if (!class_exists('CUAR_TermsWidget')) :

    /**
     * Widget to show the terms of a taxonomy in a list
     *
     * @author Vincent Prat @ MarvinLabs
     */
    abstract class CUAR_TermsWidget extends WP_Widget
    {
        /**
         * Register widget with WordPress.
         *
         * @param string $id_base         Optional Base ID for the widget, lowercase and unique. If left empty,
         *                                a portion of the widget's class name will be used Has to be unique.
         * @param string $name            Name for the widget displayed on the configuration page.
         * @param array  $widget_options  Optional. Widget options. See {@see wp_register_sidebar_widget()} for
         *                                information on accepted arguments. Default empty array.
         * @param array  $control_options Optional. Widget control options. See {@see wp_register_widget_control()}
         *                                for information on accepted arguments. Default empty array.
         */
        function __construct($id_base, $name, $widget_options = [], $control_options = [])
        {
            parent::__construct($id_base, $name, $widget_options, $control_options);
        }

        /**
         * Get the URL to a term archive page
         *
         * @param $term The term
         *
         * @return string The URL
         */
        protected abstract function get_link($term);

        /**
         * Get the taxonomy explored by this widget
         *
         * @return string The taxonomy
         */
        protected abstract function get_taxonomy();

        /**
         * Get the post types explored by this widget
         *
         * @return string The post type
         */
        protected abstract function get_friendly_post_type();

        /**
         * Get the default title for the widget if none
         *
         * @return string The title
         */
        protected abstract function get_default_title();

        /**
         * Front-end display of widget.
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         * @see WP_Widget::widget()
         *
         */
        public function widget($args, $instance)
        {
            // Don't output anything if we don't have any categories or if the user is a guest
            if (!is_user_logged_in())
            {
                return;
            }

            $hide_empty = isset($instance['hide_empty']) ? $instance['hide_empty'] : 0;

            $get_terms_options = [
                'parent' => 0,
                'hide_empty' => $hide_empty,
            ];

            $use_advanced_hide_empty_categories = apply_filters('cuar/core/widget/use-exact-hide-empty-categories-algo', true);
            if ($use_advanced_hide_empty_categories)
            {
                /** @var CUAR_PostOwnerAddOn $po_addon */
                $po_addon = cuar_addon('post-owner');
                $query = new WP_Query([
                    'post_type' => $this->get_friendly_post_type(),
                    'posts_per_page' => -1,
                    'fields' => 'ids',
                    'meta' => $po_addon->get_meta_query_post_owned_by(get_current_user_id()),
                ]);
                $post_ids = $query->get_posts();
                $get_terms_options['object_ids'] = $post_ids;
            }

            $terms = get_terms($this->get_taxonomy(), $get_terms_options);
            if (count($terms) <= 0)
            {
                return;
            }

            echo $args['before_widget'];

            $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');
            if (!empty($title))
            {
                echo $args['before_title'] . $title . $args['after_title'];
            }

            $this->print_term_list($terms, $hide_empty, is_taxonomy_hierarchical($this->get_taxonomy()));

            $this->print_term_scripts(is_taxonomy_hierarchical($this->get_taxonomy()));

            echo $args['after_widget'];
        }

        /**
         * Print the list of terms
         *
         * @param array   $terms      The terms
         * @param boolean $hide_empty Shall we hide empty terms?
         * @param bool    $is_hierarchical
         */
        protected function print_term_list($terms, $hide_empty, $is_hierarchical = false, $depth = 0)
        {
            $template_suffix = $is_hierarchical ? '-tree' : '-cloud';

            $cuar = CUAR_Plugin::get_instance();
            $cuar->enable_library('jquery.fancytree');
            $template = $cuar->get_template_file_path(
                CUAR_INCLUDES_DIR . '/core-classes',
                [
                    "widget-terms" . $template_suffix . "-" . $this->id_base . ".template.php",
                    "widget-terms-" . $this->id_base . ".template.php",
                    "widget-terms" . $template_suffix . ".template.php",
                    "widget-terms.template.php",
                ],
                'templates'
            );
            include($template);
        }

        /**
         * Print the scripts associated to the term list
         *
         * @param bool $is_hierarchical
         */
        public function print_term_scripts($is_hierarchical = false)
        {
            $template_suffix = $is_hierarchical ? '-tree' : '-cloud';
            $template = CUAR_Plugin::get_instance()->get_template_file_path(
                CUAR_INCLUDES_DIR . '/core-classes',
                [
                    "widget-terms" . $template_suffix . "-" . $this->id_base . "-scripts.template.php",
                    "widget-terms-" . $this->id_base . "-scripts.template.php",
                    "widget-terms" . $template_suffix . "-scripts.template.php",
                    "widget-terms-scripts.template.php",
                ],
                'templates'
            );
            include($template);
        }

        /**
         * Back-end widget form.
         *
         * @param array $instance Previously saved values from database.
         *
         * @return string|void
         * @see WP_Widget::form()
         *
         */
        public function form($instance)
        {
            if (isset($instance['title']))
            {
                $title = $instance['title'];
            }
            else
            {
                $title = $this->get_default_title();
            }

            if (isset($instance['hide_empty']))
            {
                $hide_empty = $instance['hide_empty'];
            }
            else
            {
                $hide_empty = 0;
            }
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'cuar'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>" type="text"
                       value="<?php echo esc_attr($title); ?>">
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('hide_empty'); ?>"><?php _e('Empty terms:',
                        'cuar'); ?></label>
                <select class="widefat" id="<?php echo $this->get_field_id('hide_empty'); ?>"
                        name="<?php echo $this->get_field_name('hide_empty'); ?>">
                    <option value="0" <?php selected(0, $hide_empty); ?>><?php _e('Show', 'cuar'); ?></option>
                    <option value="1" <?php selected(1, $hide_empty); ?>><?php _e('Hide', 'cuar'); ?></option>
                </select>
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         * @see WP_Widget::update()
         *
         */
        public function update($new_instance, $old_instance)
        {
            $instance = [];
            $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
            $instance['hide_empty'] = (!empty($new_instance['hide_empty'])) ? 1 : 0;

            return $instance;
        }
    }

endif; // if (!class_exists('CUAR_TermsWidget'))
