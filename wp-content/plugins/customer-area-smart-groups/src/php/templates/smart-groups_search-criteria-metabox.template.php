<?php /** Template version: 1.0.0 */ ?>

<?php
$is_enabled = isset($sc['enabled']) && $sc['enabled'] == 1;
?>

<div class="metabox-row">
    <label for="cuar_sc_enabled">
        <input type="checkbox" name="cuar_sc_enabled" id="cuar_sc_enabled" value="cuar_sc_enabled" <?php checked(true, $is_enabled); ?> />
        <?php _e('Enable the search criteria', 'cuarsg'); ?>
    </label>
</div>
<div class="cuar-sc-panel" <?php if ( !$is_enabled) echo 'style="display: none;"'; ?>>
    <table class="metabox-row">
        <tr>
            <td class="label">
                <?php _e('Fields', 'cuarsg'); ?>
            </td>
            <td>
                <?php foreach ($all_fields as $id => $label) : ?>
                    <label for="cuar_sc_fields_<?php echo $id; ?>" class="cuar-inline-checkboxes">
                        <input type="checkbox" name="cuar_sc_fields[]" id="cuar_sc_fields_<?php echo $id; ?>"
                               value="<?php echo $id; ?>" <?php checked(true, in_array($id, $sc['fields'])); ?> />
                        <?php echo $label; ?>
                    </label>
                <?php endforeach; ?>
        </tr>
        </tr>
    </table>
    <table class="metabox-row">
        <tr>
        <tr>
            <td class="label">
                <label for="cuar_sc_query"><?php _e('Query', 'cuarsg'); ?></label>
            </td>
            <td>
                <input type="text" name="cuar_sc_query" id="cuar_sc_query" value="<?php echo $sc['query']; ?>"/>
            </td>
        </tr>
        <tr>
            <td>
            </td>
            <td>
                <p class="description"><?php _e('Use of the * wildcard before and/or after the string will match on columns starting with*, *ending with, or *containing* the string you enter.',
                        'cuarsg'); ?></p>
            </td>
        </tr>
    </table>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#cuar_sc_enabled').change(function () {
            $('.cuar-sc-panel').slideToggle();
        });
    });
</script>