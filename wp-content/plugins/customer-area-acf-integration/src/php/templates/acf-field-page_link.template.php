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

    echo '<div class="cuar-readonly-field-page_link">';
    ?>
    <?php if (is_array($field_value)) {
        $return_type = '';
        ?>
        <?php foreach ($field_value as $link):
            ?>
            <div class="panel">
                <div class="panel-heading">
                    <a href="<?php echo $link; ?>" class="panel-title">
                            <?php echo $link; ?>
                    </a>
                </div>
            </div>
        <?php endforeach;
    } else {
        ?>
        <div class="panel">
            <div class="panel-heading">
                <a href="<?php echo $field_value; ?>" class="panel-title">
                        <?php echo $field_value; ?>
                </a>
            </div>
        </div>
        <?php
    }
    ?>
    <?php

    echo '</div>';
}


