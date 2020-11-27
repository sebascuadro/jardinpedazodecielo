<?php
/** Template version: 3.0.0
 *
 * -= 3.0.0 =-
 * - Ajaxify select
 */
?>

<?php /** @var int $current_fake_id */ ?>

<?php
$po_addon = cuar_addon('post-owner');
?>

<div id="cuarsu_identity_selection" class="cuarsu-js-id-select-container cuarsu-main pull-right">
    <form id="cuar-user-switcher" method="POST" action="">
        <?php wp_nonce_field("cuarsu_switch_identity", "cuar_switch_user_nonce", true); ?>
        <div class="cuarsu-form-wrapper" data-toggle="tooltip" data-placement="top" title="<?php esc_attr_e('Switch identity', 'cuarsu'); ?>">
            <div class="cuarsu-inputs-wrapper">
                <div class="cuarsu-id-select-wrapper input-group select2-bootstrap-append">
                    <select id="cuarsu_fake_id"
                            name="cuarsu_fake_id"
                            data-nonce="<?php echo esc_attr(wp_create_nonce('cuar_search_fake_identity')); ?>"
                            class="cuarsu-js-id-select">
                        <?php if ($current_fake_id == -1 || $current_fake_id == get_current_user_id()) : ?>
                            <option value="-1" selected="selected"><?php _e('Yourself', 'cuarsu'); ?></option>
                        <?php else : ?>
                            <option value="<?php echo esc_attr($current_fake_id); ?>" selected="selected"><?php
                                echo $po_addon->ajax()->get_user_display_value($current_fake_id, 'switch_users');
                                ?></option>
                        <?php endif; ?>
                    </select>
                    <div class="cuarsu-submit-wrapper input-group-btn">
                        <button type="submit" id="cuarsu_do_switch_identity" name="cuarsu_do_switch_identity" class="cuarsu-submit btn btn-default">
                            <i class="fa fa-random"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>