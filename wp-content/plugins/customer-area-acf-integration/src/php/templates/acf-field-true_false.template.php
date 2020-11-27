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
 *
 * = 3.1.0 =-
 *  Updated markup to show only the value of the field
 */ ?>

<?php /** @var array $field */ ?>

<?php
if ( isset($field[ 'value' ]) )
{
	$field_value = $field[ 'value' ];

    echo '<div class="cuar-readonly-field-true_false">';
	echo '<i class="fa fa-' . ( $field_value == 1 ? 'check' : 'close' ) . '"></i>';
	echo '<span>&nbsp;' . ( $field_value == 1 ? __('Yes', 'cuaracf') : __('No', 'cuaracf') ) . '</span>';
	echo '</div>';
}
