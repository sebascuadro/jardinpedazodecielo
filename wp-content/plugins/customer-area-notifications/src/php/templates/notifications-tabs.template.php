<?php
/** Template version: 1.2.0
 *
 * -= 1.2.0 =-
 * - Replace clearfix CSS classes with cuar-clearfix
 *
 * -= 1.1.0 =-
 * - Enhance the default notification template to be able to use admin settings
 *
 * -= 1.0.0 =-
 * - Initial version
 *
 */ ?>

<?php
wp_enqueue_script('jquery-ui-tabs');

$notifications = $this->get_configurable_notifications();
?>

<div id="notifications_tabs" class="tab-container tab-vertical">
    <ul class="tab-wrapper">
        <?php foreach ($notifications as $notif_id => $notif_props) : ?><?php printf('<li class="nav-tab"><a href="#notification_tab_%s">%s</a></li>',
            esc_attr($notif_id),
            $notif_props['title']); ?><?php endforeach; ?>
        <li class="nav-tab nav-tab-with-margin"><a href="#notification_tab_placeholders_help"><?php _e('Available placeholders', 'cuarno'); ?></a></li>
    </ul>

    <?php foreach ($notifications as $notif_id => $notif_props) :
        $notif_params = $this->no_addon->settings()->get_notification_params($notif_id);
        ?>
        <div id="notification_tab_<?php echo esc_attr($notif_id); ?>">
            <table class="form-table">
                <tbody>
                <tr valign="top">
                    <td>
                        <h2 class="tab-panel-title"><?php echo $notif_props['title']; ?></h2>
                        <p><?php echo esc_html($notif_props['description']); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <td>
                        <strong><?php _e('Mode', 'cuarno'); ?></strong> <br><br>
                        <?php
                        $field_id = $notif_id . '_mode';
                        $field_name = CUAR_Settings::$OPTIONS_GROUP . "[" . CUAR_NotificationsSettingsHelper::$OPTION_NOTIFICATIONS . "]" . "[" . $notif_id . "]"
                            . "[mode]";

                        printf('<select id="%1$s" name="%2$s">', esc_attr($field_id), $field_name);

                        foreach ($notif_props['available_modes'] as $value => $label) :
                            $selected = ($notif_params['mode'] == $value) ? 'selected="selected"' : '';
                            echo sprintf('<option value="%s" %s>%s</option>', esc_attr($value), $selected, $label);
                        endforeach;

                        echo '</select>';
                        ?>
                    </td>
                </tr>
                <?php if (isset($notif_props['available_recipients'])) : ?>
                <tr valign="top">
                    <td>
                        <strong><?php _e('Recipients', 'cuarno'); ?></strong> <br><br>
                        <?php
                        $field_id = $notif_id . '_recipient';
                        $field_name = CUAR_Settings::$OPTIONS_GROUP . "[" . CUAR_NotificationsSettingsHelper::$OPTION_NOTIFICATIONS . "]" . "[" . $notif_id . "]"
                            . "[recipient]";

                        printf('<select id="%1$s" name="%2$s">', esc_attr($field_id), $field_name);

                        foreach ($notif_props['available_recipients'] as $value => $label) :
                            $selected = ($notif_params['recipient'] == $value) ? 'selected="selected"' : '';
                            echo sprintf('<option value="%s" %s>%s</option>', esc_attr($value), $selected, $label);
                        endforeach;

                        echo '</select>';
                        ?>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <strong><?php _e('Email subject', 'cuarno'); ?></strong> <br><br>
                        <?php
                        $field_id = $notif_id . '_subject';
                        $field_name = CUAR_Settings::$OPTIONS_GROUP . "[" . CUAR_NotificationsSettingsHelper::$OPTION_NOTIFICATIONS . "]" . "[" . $notif_id . "]"
                            . "[subject]";
                        ?>
                        <input type="text" class="large-text" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo $notif_params['subject']; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php _e('Content heading', 'cuarno'); ?></strong> <br><br>
                        <?php
                        $field_id = $notif_id . '_heading';
                        $field_name = CUAR_Settings::$OPTIONS_GROUP . "[" . CUAR_NotificationsSettingsHelper::$OPTION_NOTIFICATIONS . "]" . "[" . $notif_id . "]"
                            . "[heading]";
                        ?>
                        <input type="text" class="large-text" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo $notif_params['heading']; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php _e('Content text', 'cuarno'); ?></strong> <br><br>
                        <?php
                        $field_id = $notif_id . '_body';
                        $field_name = CUAR_Settings::$OPTIONS_GROUP . "[" . CUAR_NotificationsSettingsHelper::$OPTION_NOTIFICATIONS . "]" . "[" . $notif_id . "]"
                            . "[body]";

                        if ( !isset ($editor_settings)) $editor_settings = array();
                        $editor_settings['textarea_name'] = $field_name;

                        // wp_editor ( $notif_params['body'], $field_id, $editor_settings );
                        printf('<textarea id="%s" name="%s" class="large-text" rows="10">%s</textarea>', esc_attr($field_id), esc_attr($field_name),
                            $notif_params['body']);
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
    <div id="notification_tab_placeholders_help">
        <p><?php _e('You will find here a list of all the placeholders that may be used in the notification subject, heading or content. Please note that depending on the context, some placeholders may not be available.', 'cuarno'); ?></p>
        <table class="widefat" style="clear: none;">
            <tbody>
        <?php $placeholders = $this->no_addon->get_available_placeholders();
        foreach ($placeholders as $placeholder => $desc) :
            ?>
                    <tr>
                        <td><strong>{{<?php echo $placeholder; ?>}}</strong></td>
                        <td><?php echo $desc; ?></td>
                    </tr>
        <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="cuar-clearfix"></div>
</div>

<script>
    jQuery(function ($) {
        $("#notifications_tabs").tabs();
    });
</script>