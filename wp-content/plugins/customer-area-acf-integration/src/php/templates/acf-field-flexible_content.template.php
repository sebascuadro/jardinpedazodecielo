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

<?php /** @var array $field */ ?>
<?php /** @var string $template_suffix */ ?>

<?php
$layouts = $field['layouts'];
$values = $field['value'];
if ($values === false || $values === null || $values === '')
{
    $values = [];
}
echo '<div class="cuar-readonly-field-flexible_content">';
foreach ($values as $value)
{
    $layout = current(array_filter($layouts, static function ($l) use ($value) {
        return $l['name'] === $value['acf_fc_layout'];
    }));

    if ($layout === false) {
        continue;
    }

    foreach ($value as $field_key => $field_value) {
        $sub_field = current(array_filter($layout['sub_fields'], static function ($f) use ($field_key) {
            return $f['name'] === $field_key;
        }));

        if ($sub_field === false) {
            continue;
        }

        $sub_field['value'] = $field_value;
        $this->print_field($sub_field, $template_suffix);
    }
}
echo '</div>';
