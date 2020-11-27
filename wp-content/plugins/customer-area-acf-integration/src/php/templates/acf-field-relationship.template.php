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
    if ($field_value === false || $field_value === null || $field_value === '')
    {
        $field_value = [];
    }
    else if (!is_array($field_value))
    {
        $field_value = [$field_value];
    }

    echo '<div class="cuar-readonly-field-relationship">';
    $return_type = '';
    ?>
    <?php foreach ($field_value as $post):
    if (is_int($post))
    {
        $return_type = 'int'; ?>
        <div class="panel">
            <div class="panel-heading">
                <a href="<?php echo get_permalink($post); ?>" class="panel-title">
                    <?php echo get_the_title($post); ?>
                </a>
            </div>
        </div>
        <?php
    }
    else
    {
        setup_postdata($post);
        ?>
        <div class="panel">
            <div class="panel-heading">
                <a href="<?php echo get_permalink(); ?>" class="panel-title">
                    <?php echo $post->post_title; ?>
                </a>
                <div class="widget-menu pull-right">
                    <span class="mr10 bg-default"><?php echo date_i18n(get_option('date_format'), strtotime($post->post_date)); ?></span>
                </div>
            </div>
            <?php
            $excerpt = get_the_excerpt();
            if (!empty($excerpt))
            {
                ?>
                <div class="panel-body">
                    <?php echo $excerpt; ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
<?php endforeach;
    if ($return_type === 'int')
    {
        wp_reset_postdata();
    }

    ?>
    <?php

    echo '</div>';
}


