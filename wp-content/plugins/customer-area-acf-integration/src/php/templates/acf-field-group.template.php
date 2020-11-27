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

<?php /** @var CUAR_ACFIntegrationAddOn $this */ ?>
<?php /** @var array $field */ ?>
<?php /** @var string $template_suffix */ ?>

<?php
$sub_fields = $field[ 'sub_fields' ];

foreach ( $sub_fields as $sub_field ) :
	$sub_field[ 'value' ] = isset($field[ 'value' ][ $sub_field[ 'name' ] ]) ? $field[ 'value' ][ $sub_field[ 'name' ] ]
		: '';
	$this->print_field($sub_field, $template_suffix);
endforeach;
