<?php /**
 * Template version: 3.0.0
 * Template zone: frontend
 */
?>

<?php /* @var array $pack */ ?>

<?php
$css_class = str_replace('{{extension}}', $extension, $pack['css_class']);
?>

<td class="cuar-icon">
    <span class="<?php echo esc_attr($css_class); ?>" style="<?php echo $pack['inline_style']; ?>"></span>
</td>
