<?php /** Template version: 1.0.0 */ ?>

<?php
$item_classes = array('metabox-row', 'cuar-mc-query-item', 'cuar-repeatable-item');
if ($show_new_item_template)
{
    $i = -1;
    $item = array('key' => '', 'compare' => '', 'value' => '');
    $item_classes[] = 'cuar-mc-query-item-template';
    $name_index = "{{row-count-placeholder}}";
}
else
{
    $item_classes[] = 'cuar-mc-query-item-' . $i;
    $name_index = $i;
}
?>

<table class="<?php echo implode(' ', $item_classes); ?>">
    <tr>
        <td rowspan="3" class="cuar-move" style="text-align: center;">
            <span class="dashicons dashicons-arrow-up-alt2"></span><br><span class="dashicons dashicons-arrow-down-alt2"></span>
        </td>
        <td class="label">
            <label><?php _e('Meta key', 'cuarsg'); ?></label>
        </td>
        <td>
            <select name="cuar_mc_items[<?php echo $name_index ?>][key]">
                <?php foreach ($all_keys as $k) : ?>
                    <option value="<?php echo esc_attr($k->meta_key); ?>" <?php selected($k->meta_key, $item['key']); ?>><?php echo $k->meta_key; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td rowspan="3" class="cuar-remove" style="text-align: center;">
            <span class="dashicons dashicons-no"></span>
        </td>
    </tr>
    <tr>
        <td class="label">
            <label><?php _e('Operator', 'cuarsg'); ?></label>
        </td>
        <td>
            <select name="cuar_mc_items[<?php echo $name_index ?>][compare]">
                <?php foreach ($all_operators as $o => $l) : ?>
                    <option value="<?php echo esc_attr($o); ?>" <?php selected($o, $item['compare']); ?>><?php echo $l; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label">
            <label><?php _e('Value', 'cuarsg'); ?></label>
        </td>
        <td>
            <input type="text" name="cuar_mc_items[<?php echo $name_index ?>][value]" value="<?php echo $item['value']; ?>"/>
        </td>
    </tr>
</table>