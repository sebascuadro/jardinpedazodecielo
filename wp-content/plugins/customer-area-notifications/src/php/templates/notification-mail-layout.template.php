<?php /** Template version: 1.0.0 */

/** @var string $header_image_url */
/** @var string $main_heading */
/** @var string $email_content */

?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title><?php echo get_bloginfo('name'); ?></title>
</head>
<body>
<?php if ( !empty ($main_heading)) : ?>
    <h1><?php echo $main_heading; ?></h1>
<?php endif; ?>

<?php echo $email_content; ?>

<?php echo wpautop(wp_kses_post(wptexturize(apply_filters('cuar/notifications/template/footer-credits',
    '<a href="' . esc_url(home_url()) . '">' . get_bloginfo('name') . '</a>')))); ?>
</body>
</html>