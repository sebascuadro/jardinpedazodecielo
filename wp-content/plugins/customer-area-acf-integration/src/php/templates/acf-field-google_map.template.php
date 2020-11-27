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
if (isset($field['value'])) {
    $field_value = $field['value'];

    if (!empty($field_value['lat']) && !empty($field_value['lng'])) {
        ?>
        <div class="cuar-readonly-field-google_map">
            <div class="marker" data-lat="<?php echo $field_value['lat']; ?>"
                 data-lng="<?php echo $field_value['lng']; ?>"></div>
        </div>
        <?php
    }
}


