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
if (isset($field['value'])) {
    $field_value = $field['value'];
    $size = 'wpca-thumb'; ?>

    <div class="cuar-readonly-field-image">

        <?php if (is_array($field_value)) {
            // vars
            $url = $field_value['url'];
            $title = $field_value['title'];
            $alt = $field_value['alt'];
            $caption = $field_value['caption'];

            // thumbnail
            $thumb = $field_value['sizes'][$size];
            $width = $field_value['sizes'][$size . '-width'];
            $height = $field_value['sizes'][$size . '-height'];

            ?>
            <div class="panel pull-left clearfix">
                <div class="panel-body pn">
                    <a href="<?php echo $url; ?>" title="<?php echo $title; ?>" class="thumbnail br-n mn">
                        <img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>"
                             height="<?php echo $height; ?>"/>
                    </a>
                </div>
                <?php if ($caption): ?>
                    <div class="panel-footer">
                        <div class="wp-caption"><?php echo $caption; ?></div>
                    </div>
                <?php endif; ?>
            </div>
            <?php

        } elseif (is_int($field_value)) {
            echo wp_get_attachment_image($field_value, $size);

        } elseif (is_string($field_value)) {
            echo '<img src="' . esc_attr($field_value) . '" />';

        } ?>

    </div>
    <?php
}


