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
    if ($field_value === false || $field_value === null || $field_value === '')
    {
        $field_value = [];
    }
    else if (!is_array($field_value))
    {
        $field_value = [$field_value];
    }

    echo '<div class="cuar-readonly-field-taxonomy">';
    $return_type = '';
    ?>
    <?php
    if (is_array($field_value)) {
        foreach ($field_value as $term):
            ?>
            <div class="panel">
                <div class="panel-heading">
                        <span class="panel-title">
                            <?php echo $term->name; ?>
                        </span>
                    <a href="<?php echo get_term_link($term); ?>" class="widget-menu pull-right">
                        <span class="mr10 bg-default"><?php _e('View all', 'cuaracf'); ?></span>
                    </a>
                </div>
                <?php
                $description = $term->description;
                if (!empty($description)) {
                    ?>
                    <div class="panel-body">
                        <?php echo $description; ?>
                    </div>
                <?php } ?>
            </div>
        <?php endforeach;

    } else { ?>
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">
                    <?php echo $term->name; ?>
                </span>
            </div>
        </div>
        <?php
    } ?>
    <?php

    echo '</div>';
}


