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

<?php
if (isset($field['value']))
{
    $field_value = $field['value'];
    if ($field_value === false || $field_value === null || !is_string($field_value))
    {
        $field_value = '';
    }

    preg_match('/src="(.+?)"/', $field_value, $matches);

    if (count($matches) >= 2)
    {
        $src = $matches[1];
        $params = [
            'controls' => 0,
            'hd'       => 1,
            'autohide' => 1
        ];

        $new_src = add_query_arg($params, $src);

        $field_value = str_replace($src, $new_src, $field_value);
        $attributes = 'frameborder="0"';

        $field_value = str_replace('></iframe>', ' ' . $attributes . '></iframe>', $field_value);

        echo '<div class="cuar-readonly-field-oembed">' . $field_value . '</div>';
    }
}
?>


