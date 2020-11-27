<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/addon.class.php');

if (!class_exists('CUAR_EnhancedFilesAddOn')) :

    /**
     * Add-on to enhance private files
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_EnhancedFilesAddOn extends CUAR_AddOn
    {

        public function __construct()
        {
            parent::__construct('enhanced-files');
        }

        public function get_addon_name()
        {
            return __('Enhanced Files', 'cuaref');
        }

        public function run_addon($plugin)
        {
            $this->enable_licensing(CUAREF_STORE_ITEM_ID, CUAREF_STORE_ITEM_NAME, CUAREF_PLUGIN_FILE, CUAREF_PLUGIN_VERSION);
            $this->load_textdomain();

            // Init the admin interface if needed
            if (is_admin()) {
                add_filter('cuar/core/permission-groups', array(&$this, 'get_configurable_capability_groups'), 10000);

                add_filter('cuar/core/status/directories-to-scan', array(&$this, 'add_hook_discovery_directory'));

                // Settings
                add_action('cuar/core/settings/print-settings?tab=cuar_private_files', array(&$this, 'print_settings'), 10000, 2);
                add_filter('cuar/core/settings/validate-settings?tab=cuar_private_files', array(&$this, 'validate_options'), 10000, 3);

                add_filter('admin_enqueue_scripts', array(&$this, 'enqueue_icon_pack_styles'));
            } else {
                add_filter('wp_enqueue_scripts', array(&$this, 'enqueue_icon_pack_styles'));
                add_action('cuar/templates/file-attachment-item/before-caption', array(&$this, 'print_attachment_icon'), 10, 2);
            }

            add_filter('cuar/private-content/files/default-action', array(&$this, 'change_default_file_actions'), 10, 2);
            add_filter('cuar/private-content/files/max-attachment-count', array(&$this, 'filter_max_attachment_count'));
            add_filter('cuar/templates/attachment-manager/before-file-attachment-caption', array(&$this, 'print_editable_hint'), 10, 2);
            add_filter('cuar/templates/attachment-manager/after-file-attachment-list', array(&$this, 'print_additional_attachment_manager_scripts'));
        }

        /**
         * Add our folder to the status file scanner
         *
         * @param array $dirs The directories
         *
         * @return array The directories + ours
         */
        public function add_hook_discovery_directory($dirs)
        {
            $dirs[CUAREF_PLUGIN_DIR] = $this->get_addon_name();

            return $dirs;
        }

        /*------- ENHANCEMENTS -------------------------------------------------------------------------------------------*/

        /**
         * If the file extension is in our setting, then force the view action
         *
         * @param string $action
         * @param array  $attachment
         *
         * @return string
         */
        public function change_default_file_actions($action, $attachment)
        {
            $ext = pathinfo($attachment['file'], PATHINFO_EXTENSION);
            $ext = strtolower($ext);

            // View is forced?
            $force_view = $this->get_forced_view_extensions();
            if (in_array($ext, $force_view)) return 'view';

            return $action;
        }

        /**
         * Allow more files to be attached to the private file content type
         *
         * @param int $count Original number of files
         *
         * @return int The allowed number
         */
        public function filter_max_attachment_count($count)
        {
            if (current_user_can('cuar_pf_unlimited_attachments')) return -1;

            return $this->get_max_attachment_count();
        }

        /**
         * Print the JS to enable inline editing of the file
         */
        public function print_additional_attachment_manager_scripts()
        {
            $this->plugin->enable_library('jquery.jeditable');

            include($this->plugin->get_template_file_path(
                CUAREF_INCLUDES_DIR,
                'attachments-additional-scripts.template.php'
            ));
        }

        /*------- ICON PACKS ---------------------------------------------------------------------------------------------*/

        /**
         * Get all available icon packs
         *
         * @return array
         */
        public function get_icon_packs()
        {
            $root_dir = trailingslashit(CUAREF_PLUGIN_DIR) . 'assets/packs/';
            $root_url = trailingslashit(CUAREF_PLUGIN_URL) . 'assets/packs/';

            $img_inline_style = 'border: none; box-shadow: none;';
            $font_inline_style = 'font-size: 1.8em;';

            $packs = array();

            // Freepick flaticons PNG
            foreach (array(128, 64, 32) as $s) {
                $inline_style = $img_inline_style . ' width: ' . $s . 'px; height: ' . $s . 'px;';
                $packs['flaticon-' . $s] = array(
                    'type'         => 'img',
                    'label'        => 'Flaticon ' . $s . 'px',
                    'author'       => 'Freepick',
                    'link'         => 'http://www.flaticon.com/packs/file-formats-icons',
                    'license_url'  => $root_url . 'freepick-flaticon/license.pdf',
                    'asset_path'   => $root_dir . 'freepick-flaticon/png-' . $s,
                    'asset_url'    => $root_url . 'freepick-flaticon/png-' . $s,
                    'filename'     => '{{extension}}.png',
                    'fallback'     => 'default.png',
                    'inline_style' => $inline_style,
                    'size'         => array($s, $s),
                );
            }

            // Freepick flaticons font
            $packs['flaticon-font'] = array(
                'type'         => 'font',
                'label'        => 'Flaticon font',
                'author'       => 'Freepick',
                'link'         => 'http://www.flaticon.com/packs/file-formats-icons',
                'license_url'  => $root_url . 'freepick-flaticon/license.pdf',
                'asset_path'   => $root_dir . 'freepick-flaticon',
                'asset_url'    => $root_url . 'freepick-flaticon',
                'css_file'     => 'flaticon.css',
                'css_class'    => 'flaticon-{{extension}}',
                'inline_style' => $font_inline_style,
                'fallback'     => '',
            );

            // filesquare
            foreach (array(128, 64, 32) as $s) {
                $inline_style = $img_inline_style . ' width: ' . $s . 'px; height: ' . $s . 'px;';
                $packs['filesquare' . $s] = array(
                    'type'         => 'img',
                    'label'        => 'FileSquare file icons ' . $s . 'px',
                    'author'       => 'FileSquare',
                    'link'         => 'http://filetypeicons.com/',
                    'license_url'  => 'http://creativecommons.org/licenses/by-sa/3.0/hk/',
                    'asset_path'   => $root_dir . 'filesquare/png-' . $s,
                    'asset_url'    => $root_url . 'filesquare/png-' . $s,
                    'filename'     => '{{extension}}.png',
                    'fallback'     => 'default.png',
                    'inline_style' => $inline_style,
                    'size'         => array($s, $s),
                );
            }

            // Pelfusion
            foreach (array(128, 56) as $s) {
                $inline_style = $img_inline_style . ' width: ' . $s . 'px; height: ' . $s . 'px;';
                $packs['pelfusion-ls' . $s] = array(
                    'type'         => 'img',
                    'label'        => 'Pelfusion long shadows ' . $s . 'px',
                    'author'       => 'Pelfusion',
                    'link'         => 'http://www.pelfusion.com/file-type-icons-free/',
                    'license_url'  => 'http://www.pelfusion.com/file-type-icons-free/',
                    'asset_path'   => $root_dir . 'pelfusion-ls/' . $s,
                    'asset_url'    => $root_url . 'pelfusion-ls/' . $s,
                    'filename'     => '{{extension}}.png',
                    'fallback'     => 'default.png',
                    'inline_style' => $inline_style,
                    'size'         => array($s, $s),
                );
            }

            // Medialoot
            foreach (array(80, 40) as $s) {
                $inline_style = $img_inline_style . ' width: ' . $s . 'px; height: ' . $s . 'px;';
                $packs['medialoot-flat-' . $s] = array(
                    'type'         => 'img',
                    'label'        => 'MediaLoot Flat Icons ' . $s . 'px',
                    'author'       => 'MediaLoot',
                    'link'         => 'http://medialoot.com/item/free-flat-filetype-icons/',
                    'license_url'  => $root_url . 'medialoot-flat/license.pdf',
                    'asset_path'   => $root_dir . 'medialoot-flat/png-' . $s,
                    'asset_url'    => $root_url . 'medialoot-flat/png-' . $s,
                    'filename'     => '{{extension}}.png',
                    'fallback'     => 'default.png',
                    'inline_style' => $inline_style,
                    'size'         => array((int)(0.825 * $s), $s),
                );
            }

            return apply_filters('cuar/private-content/files/icon-packs', $packs);
        }

        /**
         * If the icon pack has any associated CSS, enqueue it
         */
        public function enqueue_icon_pack_styles()
        {
            if (is_admin()
                && (!isset($_GET['tab']) || $_GET['tab'] != 'cuar_private_files')
                && (!isset($_GET['page']) || $_GET['page'] != 'wpca-settings')
            ) {
                // Only on the settings page for the admin area
                return;
            }

            $pack = $this->get_icon_pack();
            if ($pack != false && isset($pack['css_file'])) {
                wp_enqueue_style($pack['id'], $pack['asset_url'] . '/' . $pack['css_file'], null,
                    $this->plugin->get_version());
            }
        }

        /**
         * Display the HTML before the attachment caption
         */
        public function print_attachment_icon($post_id, $attachment)
        {
            $pack = $this->get_icon_pack();
            if ($pack == false) return;

            $filename = $attachment['file'];

            /** @noinspection PhpUnusedLocalVariableInspection */
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            /** @noinspection PhpIncludeInspection */
            include($this->plugin->get_template_file_path(
                CUAREF_INCLUDES_DIR,
                'attachments-additional-column-icon-' . $pack['type'] . '.template.php'
            ));
        }

        /**
         * Display the HTML before the attachment caption in editable mode
         */
        public function print_editable_hint($post_id, $attached_file)
        {
            if (is_admin()) {
                echo '<span class="dashicons dashicons-edit" title="'
                    . __('Click the file name to change it', 'cuaref')
                    . '"></span>&nbsp;&nbsp;';
            } else {
                echo '<td style="width: 1px; white-space: nowrap;" title="'
                    . __('Click the file name to change it', 'cuaref')
                    . '"><span class="fa fa-edit text-muted"></span></td>';
            }
        }

        /*------- CUSTOMISATION OF THE PLUGIN SETTINGS PAGE --------------------------------------------------------------*/

        /** getter for a setting */
        public function get_icon_pack()
        {
            $res = true;
            $pack_id = $this->plugin->get_option(self::$OPTION_ICON_PACK);

            if (empty($pack_id)) $res = false;

            if ($res != false) {
                $packs = $this->get_icon_packs();
                if (!isset($packs[$pack_id])) $res = false;

                if ($res != false) {
                    $res = $packs[$pack_id];
                    $res['id'] = $pack_id;
                }
            }

            return apply_filters('cuar/private-content/files/selected-icon-pack', $res);
        }

        /** getter for a setting */
        public function get_max_attachment_count()
        {
            return $this->plugin->get_option(self::$OPTION_MAX_ATTACHMENT_COUNT);
        }

        /** getter for a setting */
        public function get_forced_view_extensions()
        {
            $list = $this->plugin->get_option(self::$OPTION_FORCE_VIEW_ACTION_EXTENSIONS);
            $list = explode(',', $list);
            foreach ($list as $i => $l) {
                $list[$i] = trim($l);
            }

            return $list;
        }

        /**
         * Add our fields to the settings page
         *
         * @param CUAR_Settings $cuar_settings The settings class
         * @param string        $options_group
         */
        public function print_settings($cuar_settings, $options_group)
        {
            add_settings_field(
                self::$OPTION_MAX_ATTACHMENT_COUNT,
                __('Max. attachments', 'cuaref'),
                array(&$cuar_settings, 'print_input_field'),
                CUAR_Settings::$OPTIONS_PAGE_SLUG,
                'cuar_private_files_addon_general',
                array(
                    'option_id' => self::$OPTION_MAX_ATTACHMENT_COUNT,
                    'type'      => 'text',
                    'after'     => '<p class="description">'
                        . __('The maximum number of files that can be attached to private content. You can allow some roles to have unlimited attachments by giving them the "unlimited attachments" capability.',
                            'cuaref')
                        . '</p>',
                )
            );

            add_settings_field(
                self::$OPTION_FORCE_VIEW_ACTION_EXTENSIONS,
                __('View inline', 'cuaref'),
                array(&$cuar_settings, 'print_input_field'),
                CUAR_Settings::$OPTIONS_PAGE_SLUG,
                'cuar_private_files_addon_general',
                array(
                    'option_id' => self::$OPTION_FORCE_VIEW_ACTION_EXTENSIONS,
                    'type'      => 'text',
                    'after'     => __('Comma-separated list of file extensions. e.g. <code>pdf,txt</code>', 'cuaref')
                        . '<p class="description">'
                        . __('You can specify here the file extensions for which the browser will try to show them inline rather than downloading them. Kindly note that this is a hint to the browser and depending on the browser version, user settings, etc. the file may still be downloaded instead of viewed inline.',
                            'cuaref')
                        . '</p>',
                )
            );

            $packs = $this->get_icon_packs();
            $available_packs = array('' => __('Do not show any icons', 'cuaref'));
            foreach ($packs as $id => $p) {
                $available_packs[$id] = $p['label'];
            }

            add_settings_field(
                self::$OPTION_ICON_PACK,
                __('File icons', 'cuaref'),
                array(&$cuar_settings, 'print_select_field'),
                CUAR_Settings::$OPTIONS_PAGE_SLUG,
                'cuar_private_files_addon_general',
                array(
                    'option_id' => self::$OPTION_ICON_PACK,
                    'options'   => $available_packs,
                    'after'     => $this->get_icon_pack_description(),
                )
            );
        }

        /**
         * Validate our options
         *
         * @param CUAR_Settings $cuar_settings
         * @param array         $input
         * @param array         $validated
         *
         * @return array
         */
        public function validate_options($validated, $cuar_settings, $input)
        {
            $cuar_settings->validate_int($input, $validated, self::$OPTION_MAX_ATTACHMENT_COUNT);
            $cuar_settings->validate_always($input, $validated, self::$OPTION_ICON_PACK);
            $cuar_settings->validate_always($input, $validated, self::$OPTION_FORCE_VIEW_ACTION_EXTENSIONS);

            return $validated;
        }

        private function get_icon_pack_description()
        {
            $pack = $this->get_icon_pack();
            if ($pack == false) return '<p class="description">' . __('File icons are currently switched off.', 'cuaref') . '</p>';

            ob_start();

            echo '<p>';
            printf(__('You are currently using the icon pack %1$s designed by <a href="%2$s">%3$s</a> available under the <a href="%4$s">following license</a>.',
                'cuaref'),
                $pack['label'],
                esc_attr($pack['link']),
                $pack['author'],
                $pack['license_url']
            );
            echo ' ' . __('Here are a few sample icons from this pack: ', 'cuaref');
            echo '</p>';
            echo '<p>';
            $sample_ext = array('jpg', 'pdf', 'zip', 'mp3', 'txt');
            foreach ($sample_ext as $ext) {
                echo '<span title="' . esc_attr($ext) . '">';
                $this->print_attachment_icon(0, array('file' => 'test.' . $ext));
                echo '</span>&nbsp;&nbsp;';
            }
            echo '</p>';

            $out = ob_get_contents();
            ob_end_clean();

            return $out;

        }

        // Options
        public static $OPTION_MAX_ATTACHMENT_COUNT = 'cuar_ef_max_attachment_count';
        public static $OPTION_ICON_PACK = 'cuar_ef_icon_pack';
        public static $OPTION_FORCE_VIEW_ACTION_EXTENSIONS = 'cuar_ef_force_view_action';

        /*------- INITIALISATIONS ----------------------------------------------------------------------------------------*/

        /**
         * Set the default values for the options
         *
         * @param array $defaults
         *
         * @return array
         */
        public function set_default_options($defaults)
        {
            $defaults = parent::set_default_options($defaults);

            $defaults[self::$OPTION_MAX_ATTACHMENT_COUNT] = 1;
            $defaults[self::$OPTION_ICON_PACK] = 'flaticon-32';
            $defaults[self::$OPTION_FORCE_VIEW_ACTION_EXTENSIONS] = 'pdf,txt,md';

            return $defaults;
        }

        public function get_configurable_capability_groups($capability_groups)
        {
            $bo_caps = &$capability_groups['cuar_private_file']['groups']['global']['capabilities'];

            $bo_caps['cuar_pf_unlimited_attachments'] = __('Unlimited file attachments', 'cuaref');

            return $capability_groups;
        }

        /**
         * Load the translation file for current language.
         */
        public function load_textdomain()
        {
            $this->plugin->load_textdomain('cuaref', 'customer-area-enhanced-files');
        }
    }

    // Make sure the addon is loaded
    new CUAR_EnhancedFilesAddOn();

endif; // if (!class_exists('CUAR_EnhancedFilesAddOn')) 
