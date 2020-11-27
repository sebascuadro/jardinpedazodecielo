<?php /**
 * Template version: 5.0.0
 * Template zone: frontend
 *
 * = 5.0.0 =-
 *  Update template for ACF v5.8.3+
 *
 *  = 4.1.0 =-
 *  Fix template syntax
 *
 * = 4.0.0 =-
 *  Update for ACF 5 compatibility
 */ ?>

<?php
/** @var array $field */
/** @var array $template_suffix */
$repeater_field = $field;
?>

<div class="cuar-readonly-field-repeater">
    <table class="table">
        <thead>
        <tr>
            <?php foreach ($repeater_field['sub_fields'] as $sub_field) : ?>
                <th><?php echo $sub_field['label']; ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!is_array($repeater_field['value']))
        {
            $repeater_field['value'] = [];
        }
        foreach ($repeater_field['value'] as $row) : ?>
            <tr>
                <?php foreach ($repeater_field['sub_fields'] as $sub_field) :
                    if (empty($row[$sub_field['name']]))
                    {
                        $sub_field['value'] = isset($sub_field['default_value']) ? $sub_field['default_value'] : '';
                    }
                    else
                    {
                        $sub_field['value'] = $row[$sub_field['name']];
                    }
                    ?>
                    <td>
                        <?php $this->print_field($sub_field, $template_suffix, false); ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
