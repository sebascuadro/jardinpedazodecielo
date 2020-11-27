<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/settings.class.php');

/**
 * Administation area for private files
 *
 * @author Vincent Prat @ MarvinLabs
 */
class CUAR_NotificationsAdminInterface
{
    /** @var CUAR_Plugin */
    private $plugin;

    /** @var CUAR_NotificationsAddOn */
    private $notifications_addon;

    public function __construct($plugin, $notifications_addon)
    {
        $this->plugin = $plugin;
        $this->no_addon = $notifications_addon;

        if (is_admin()) {
            // Private file edit page metaboxes
            add_action('add_meta_boxes', array(&$this, 'register_edit_page_meta_boxes'));
        }
    }

    /*------- PRIVATE FILE METABOXES ---------------------------------------------------------------------------------*/

    /**
     * Register some additional boxes on the page to edit the files
     */
    public function register_edit_page_meta_boxes($post_type)
    {
        $post_types = $this->plugin->get_content_post_types();
        foreach ($post_types as $type) {
            if ($post_type != $type) continue;

            add_meta_box('cuar_private_post_notifications',
                __('Notifications', 'cuarno'),
                array(&$this, 'print_notifications_meta_box'),
                $type,
                'side',
                'core');
        }

        if ($post_type == 'cuar_project') {
            add_meta_box('cuar_private_post_notifications',
                __('Notifications', 'cuarno'),
                array(&$this, 'print_project_notifications_meta_box'),
                $post_type,
                'side',
                'core');
        }
    }

    /**
     * Print the metabox to send a notification
     */
    public function print_project_notifications_meta_box()
    {
        do_action("cuar/notifications/send-notification-meta-box/before");

        echo '<div id="cuar-notification" class="metabox-row">';
        printf('<input type="checkbox" name="%1$s" id="%1$s" %2$s />&nbsp;%3$s',
            'cuar_no_send_new_private_post_notification',
            apply_filters('cuar/notifications/send-notification-meta-box/default-checkbox-value', ''),
            __('Let the team know they can access this project', 'cuarno')
        );
        printf('<p class="description">%s</p>',
            __('The notification will only be sent if you publish the post, and if you have assigned team members. Else nothing will be sent.', 'cuarno')
        );
        echo '</div>';

        do_action("cuar/notifications/send-notification-meta-box/after");
    }

    /**
     * Print the metabox to send a notification
     */
    public function print_notifications_meta_box()
    {
        do_action("cuar/notifications/send-notification-meta-box/before");

        echo '<div id="cuar-notification" class="metabox-row">';
        printf('<input type="checkbox" name="%1$s" id="%1$s" %2$s />&nbsp;%3$s',
            'cuar_no_send_new_private_post_notification',
            apply_filters('cuar/notifications/send-notification-meta-box/default-checkbox-value', ''),
            __('Let the owner know he can access this private content', 'cuarno')
        );
        printf('<p class="description">%s</p>',
            __('The notification will only be sent if you publish the post, and if you have set an owner. Else nothing will be sent.', 'cuarno')
        );
        echo '</div>';

        do_action("cuar/notifications/send-notification-meta-box/after");
    }
}
