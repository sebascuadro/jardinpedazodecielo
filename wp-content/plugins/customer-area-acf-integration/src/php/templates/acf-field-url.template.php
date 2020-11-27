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
if ( isset($field[ 'value' ]) )
{
	$field_value = $field[ 'value' ];

	echo '<a href="' . esc_attr($field_value) . '">' . $field_value . '</a>';
}
