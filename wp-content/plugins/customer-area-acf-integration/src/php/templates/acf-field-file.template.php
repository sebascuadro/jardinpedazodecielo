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
    $field_value = $field['value']; ?>

    <div class="cuar-readonly-field-file">

        <?php
        if (is_array($field_value)) {
            // vars
            $url = $field_value['url'];
            $title = $field_value['title'];
            $caption = $field_value['caption'];

            // icon
            $icon = $field_value['icon'];

            if ($field_value['type'] == 'image') {
                $icon = $field_value['sizes']['thumbnail'];
            } ?>

            <a href="<?php echo $url; ?>" title="<?php echo $title; ?>" class="btn btn-default">
                <img src="<?php echo $icon; ?>"/>
                <span><?php echo $title; ?></span>
                <?php if ($caption): ?>
                    <div class="wp-caption"><?php echo $caption; ?></div>
                <?php endif; ?>
            </a>

            <?php
        } elseif (is_int($field_value)) {
            $url = wp_get_attachment_url($field_value); ?>
            <a href="<?php echo $url; ?>" class="btn btn-default"><?php _e("Download File", 'cuaracf'); ?></a>
            <?php

        } elseif (is_string($field_value)) {
            echo '<a href="' . esc_attr($field_value) . '" class="btn btn-default">' . $field_value . '</a>';
        } ?>

    </div>
    <?php
}
