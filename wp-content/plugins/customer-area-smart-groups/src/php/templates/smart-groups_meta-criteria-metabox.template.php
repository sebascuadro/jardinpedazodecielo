<?php /** Template version: 1.0.0 */ ?>

<?php
$is_enabled = isset($mc['enabled']) && $mc['enabled'] == 1;
?>

<div class="metabox-row">
    <label for="cuar_mc_enabled">
        <input type="checkbox" name="cuar_mc_enabled" id="cuar_mc_enabled" value="cuar_mc_enabled" <?php checked(true, $is_enabled); ?> />
        <?php _e('Enable the custom fields criteria', 'cuarsg'); ?>
    </label>
</div>
<div class="cuar-mc-panel" <?php if ( !$is_enabled) echo 'style="display: none;"'; ?>>

    <!-- QUERY ITEMS -->

    <div class="cuar-mc-query-items-wrapper cuar-repeatable-wrapper">

        <!-- RELATION BETWEEN ITEMS -->
        <table class="metabox-row">
            <tr>
                <td class="label">
                    <?php _e('Relation between query items', 'cuarsg'); ?>
                </td>
                <td>
                    <select id="cuar_mc_relation" name="cuar_mc_relation">
                        <?php foreach ($all_relations as $v => $o) : ?>
                            <option value="<?php echo esc_attr($v); ?>" <?php selected($v, $mc['relation']); ?>><?php echo $o; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

        <table class="metabox-row cuar-repeatable-header">
            <tr>
                <td class="label">
                    <p><?php _e('Meta fields query items', 'cuarsg'); ?></p>
                </td>
                <td class="cuar-add">
                    <span class="dashicons dashicons-plus"></span> <span><?php _e('Add an item', 'cuarsg'); ?></span>
                </td>
            </tr>
        </table>

        <div class="cuar-mc-query-items-container cuar-repeatable-container">
            <!-- Template for new query items -->
            <?php
            $show_new_item_template = true;
            include($item_template);
            ?>

            <?php
            $show_new_item_template = false;
            $i = 0;
            foreach ($mc['items'] as $item)
            {
                include($item_template);
                ++$i;
            }
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#cuar_mc_enabled').change(function () {
            $('.cuar-mc-panel').slideToggle();
        });

        $('.cuar-mc-panel').repeatable_fields({
            wrapper: '.cuar-mc-query-items-wrapper',
            container: '.cuar-mc-query-items-container',
            row: '.cuar-mc-query-item',
            add: '.cuar-add',
            remove: '.cuar-remove',
            move: '.cuar-move',
            template: '.cuar-mc-query-item-template'
        });
    });
</script>