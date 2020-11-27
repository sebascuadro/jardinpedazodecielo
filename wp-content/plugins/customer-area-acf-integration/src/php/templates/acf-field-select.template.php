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

    echo '<div class="cuar-readonly-field-checkbox">';

    if(is_array($field_value))
    {
        echo '<ul>';
        foreach ( $field_value as $fv ):
            echo '<li>' . $fv . '</li>';
        endforeach;
        echo '</ul>';
    } else {
        echo $field_value;
    }

    echo '</div>';
}


