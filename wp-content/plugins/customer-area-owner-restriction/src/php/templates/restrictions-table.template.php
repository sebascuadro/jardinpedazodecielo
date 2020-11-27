<?php
/** Template version: 1.1.0
 *
 * -= 1.1.0 =-
 * - Replace clearfix CSS classes with cuar-clearfix
 *
 * -= 1.0.0 =-
 * - Initial version
 *
 */ ?>

<?php
require_once(CUAR_INCLUDES_DIR . '/helpers/wordpress-helper.class.php');

global $wp_roles;
if ( !isset($wp_roles)) $wp_roles = new WP_Roles();
$all_roles = $wp_roles->role_objects;

$po_addon = $this->plugin->get_addon('post-owner');
$owner_types = $po_addon->get_owner_types();

wp_enqueue_script('jquery-ui-tabs');
?>

<div id="restrictions_tabs" class="tab-container tab-vertical">
    <ul class="tab-wrapper">
        <?php foreach ($all_roles as $role) : ?><?php printf('<li class="nav-tab"><a href="#restriction_tab_%s">%s</a></li>',
            esc_attr($role->name),
            esc_html(CUAR_WordPressHelper::getRoleDisplayName($role->name))); ?><?php endforeach; ?>
    </ul>

    <?php foreach ($all_roles as $role) :
        $role_label = CUAR_WordPressHelper::getRoleDisplayName($role->name);
        ?>
        <div id="restriction_tab_<?php echo esc_attr($role->name); ?>">
            <p><strong><?php echo $role_label; ?></strong> <span class="description"><?php _e('can create private content for:', 'cuaror'); ?></span></p>
            <table>

                <?php foreach ($owner_types as $type => $owner_type_label) :
                    $option_id = CUAR_OwnerRestrictionAddOn::get_restriction_option_id($content_type, $role->name, $type);
                    $select_params = apply_filters('cuar/owner-restrictions/selectable-restrictions?owner-type=' . $type, array());
                    $multiple = $select_params['multiple'] ? 'multiple="multiple"' : "";
                    $options = $select_params['options'];
                    ?>
                    <tr>
                        <td align="right" style="vertical-align: middle;"><?php echo $owner_type_label; ?> &nbsp;</td>
                        <td style="vertical-align: middle;">&nbsp;&rarr;&nbsp;</td>
                        <td style="vertical-align: middle;">
                            <?php
                            echo sprintf('<select id="%s" name="%s[%s]" %s style="width: 100%%;">', esc_attr($option_id), CUAR_Settings::$OPTIONS_GROUP,
                                esc_attr($option_id), $multiple);

                            foreach ($options as $value => $label)
                            {
                                $selected = ($this->plugin->get_option($option_id) == $value) ? 'selected="selected"' : '';
                                echo sprintf('<option value="%s" %s>%s</option>', esc_attr($value), $selected, $label);
                            }

                            echo '</select>';
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </table>
            <p>&nbsp;</p>
        </div>
    <?php endforeach; ?>

    <div class="cuar-clearfix"></div>
</div>

<script>
    jQuery(function ($) {
        $("#restrictions_tabs").tabs();
    });
</script>