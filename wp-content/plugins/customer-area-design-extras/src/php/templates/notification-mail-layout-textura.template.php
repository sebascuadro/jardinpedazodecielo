<?php /** Template version: 1.0.0 */

/** @var string $header_image_url */
/** @var string $main_heading */
/** @var string $email_content */

/** @var CUAR_NotificationsSettingsHelper $settings_helper */
$settings_helper = cuar_addon('notifications')->settings();

$colors = array(
    'header_image'      => $settings_helper->get_email_template_setting($template_id, 'header_image'),
    'color_link'        => $settings_helper->get_email_template_setting($template_id, 'color_link'),
    'color_title'       => $settings_helper->get_email_template_setting($template_id, 'color_title'),
    'color_header_text' => $settings_helper->get_email_template_setting($template_id, 'color_header_text'),
    'color_main_text'   => $settings_helper->get_email_template_setting($template_id, 'color_main_text'),
    'color_footer_text' => $settings_helper->get_email_template_setting($template_id, 'color_footer_text'),
);

?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title><?php echo get_bloginfo('name'); ?></title>

    <style>
        a, a:visited {
            color: <?php echo $colors['color_link']; ?> !important;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .footer p {
            font-size: 11px;
            color: <?php echo $colors['color_footer_text']; ?>;
            margin: 0;
            padding: 0;
            font-family: Helvetica, Arial, sans-serif;
        }

        .content p, .content {
            color: <?php echo $colors['color_main_text']; ?>;
            font-weight: normal;
            margin: 0;
            padding: 0;
            line-height: 20px;
            font-size: 12px;
            font-family: Helvetica, Arial, sans-serif;
        }
    </style>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="<?php echo "font-family: Helvetica, Arial, sans-serif; font-size: 12px; margin: 0; line-height: 20px; padding: 0; background: #4b4b4b url('" . CUARDE_PLUGIN_URL . "/assets/notifications/textura/bg_email.png');"; ?>">

<?php if ( !empty($header_image_url)) : ?>
    <div id="template_header_image">
        <?php echo '<p style="text-align:center; padding: 40px 0 0 0;"><img src="' . esc_url($header_image_url) . '" alt="' . get_bloginfo('name') . '" /></p>'; ?>
    </div>
<?php endif; ?>

<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%" style="padding: 35px 0;">
    <tbody>
    <tr>
        <td align="center" style="margin: 0; padding: 0;">
            <table cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="font-family: Helvetica, Arial, sans-serif; background:#2a2a2a;" class="header">
                <tbody>
                <tr>
                    <td width="600" align="left" style="padding: 0; font-size: 0; line-height: 0; height: 7px;" height="7" colspan="2">
                        <img src="<?php echo CUARDE_PLUGIN_URL . "/assets/notifications/textura/bg_header.png"; ?>" alt="header bg">
                    </td>
                </tr>
                <tr>
                    <td width="20" style="font-size: 0;">&nbsp;</td>
                    <td width="580" align="left" style="padding: 18px 0 10px;">
                        <?php if ( !empty ($main_heading)) : ?>
                            <h1 style="color: <?php echo $colors['color_title']; ?>; font: bold 32px Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 40px;">
                                <?php echo $main_heading; ?>
                            </h1>
                        <?php endif; ?>
                        <p style="color: <?php echo $colors['color_header_text']; ?>; font: normal 12px Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 18px;">
                            <?php _e('From:', 'cuarde'); ?> <?php echo get_bloginfo('name'); ?>
                            - <?php _e('Date:', 'cuarde'); ?>  <?php echo date(get_option('date_format')); ?>
                        </p>
                    </td>
                </tr>
                <?php if ( !empty($header_image_url)) : ?>
                    <tr id="template_header_image">
                        <?php echo '<td><img src="' . esc_url($header_image_url) . '" alt="' . get_bloginfo('name') . '" style="margin-top:0; max-width: 100%; height: auto; width: auto;"/></td>'; ?>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            <table cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="font-family: Helvetica, Arial, sans-serif; background: #fff;" bgcolor="#fff">

                <tbody>
                <tr>
                    <td width="600" valign="top" align="left" style="font-family: Helvetica, Arial, sans-serif; padding: 20px 0 0;" class="content">

                        <table cellpadding="0" cellspacing="0" border="0" style="color: <?php echo $colors['color_main_text']; ?>; font: normal 11px Helvetica, Arial, sans-serif; margin: 0; padding: 0;" width="600">
                            <tbody>
                            <tr>
                                <td width="21" style="font-size: 1px; line-height: 1px;">
                                    <img src="<?php echo CUARDE_PLUGIN_URL . "/assets/notifications/textura/spacer.gif"; ?>" alt="space" width="20">
                                </td>
                                <td style="padding: 20px 0 0;" align="left">
                                    <?php echo $email_content; ?>
                                </td>
                                <td width="21" style="font-size: 1px; line-height: 1px;">
                                    <img src="<?php echo CUARDE_PLUGIN_URL . "/assets/notifications/textura/spacer.gif"; ?>" alt="space" width="20">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <p>&nbsp;</p>
                                    <p>&nbsp;</p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>

                </tr>
                <tr>
                    <td width="600" align="left" style="padding: 0; font-size: 0; line-height: 0; height: 3px;" height="3" colspan="2">
                        <img src="<?php echo CUARDE_PLUGIN_URL . "/assets/notifications/textura/bg_bottom.png"; ?>" alt="header bg">
                    </td>
                </tr>
                </tbody>
            </table>
            <table cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="font-family: Helvetica, Arial, sans-serif; line-height: 10px;" class="footer">
                <tbody>
                <tr>
                    <td align="center" style="padding: 10px 0 10px; font-size: 11px; color:<?php echo $colors['color_footer_text']; ?>; margin: 0; line-height: 1.2;font-family: Helvetica, Arial, sans-serif;" valign="top">
                        <?php echo wpautop(wp_kses_post(wptexturize(apply_filters('cuar/notifications/template/footer-credits',
                            __('This is an automatic notification message sent by', 'cuarde') . ' <a href="' . esc_url(home_url()) . '">' . get_bloginfo('name') . '</a>')))); ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>

</body>