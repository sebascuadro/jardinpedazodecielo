<?php

if (!function_exists('cuar_custom_editor_styles')) {
    /**
     * Load custom styles for TinyMCE
     * @param $mce_css
     * @return string
     */
    function cuar_custom_editor_styles($mce_css)
    {
        if (is_admin()) return $mce_css;

        if (cuar_is_customer_area_page(get_queried_object_id()) || cuar_is_customer_area_private_content(get_the_ID())) {
            $mce_css .= ', ' . plugins_url('assets/css/custom-editor-style.css', __FILE__);
        }
        return $mce_css;
    }

    add_action('mce_css', 'cuar_custom_editor_styles');
}

if ( !function_exists('cuar_disable_sliding_sidebar'))
{
	/**
	 * Add a body class to disable hover effect on sidebar
	 * @param $classes
	 * @return array
	 */
	function cuar_disable_sliding_sidebar($classes)
	{
		$classes[] = 'disable-tray-rescale';
		return $classes;
	}

	add_filter('body_class', 'cuar_disable_sliding_sidebar');
}

// Includes functions from base theme
include(CUAR_PLUGIN_DIR . '/skins/frontend/master/cuar-functions.php');