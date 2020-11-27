<?php /**
 * Template version: 3.0.0
 * Template zone: frontend
 */
?>

<?php /* @var array $pack */ ?>
<?php /* @var string $extension */ ?>

<?php
$css_class = 'cuar-attachment-icon cuar-icon-type-' . $pack['type'] . ' cuar-iconpack-' . $pack['id'];
$img = str_replace('{{extension}}', $extension, $pack['filename']);
$img_path = $pack['asset_path'] . '/' . $img;
$img_url = file_exists($img_path)
    ? $pack['asset_url'] . '/' . $img
    : $img_url = $pack['asset_url'] . '/' . $pack['fallback'];
$size = $pack['size'];
?>

<td class="cuar-icon">
    <span class="<?php echo esc_attr($css_class); ?>">
        <img src="<?php echo $img_url; ?>" width="<?php echo $size[0]; ?>" height="<?php echo $size[1]; ?>" style="<?php echo $pack['inline_style']; ?>"/>
    </span>
</td>
