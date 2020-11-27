<?php
/** Template version: 1.1.0
 *
 * -= 1.1.0 =-
 * - Enhance the default notification template to be able to use admin settings
 *
 * -= 1.0.0 =-
 * - Initial version
 *
 */

/** @var string $main_heading */
/** @var string $email_content */

/** @var CUAR_NotificationsSettingsHelper $settings */
$settings = cuar_addon('notifications')->settings();

$color_link = $settings->get_email_template_setting($template_id, 'color_link');
$header_image_url = $settings->get_email_template_setting($template_id, 'logo_url');
$color_main_bg = $settings->get_email_template_setting($template_id, 'color_main_bg');
$color_message_bg = $settings->get_email_template_setting($template_id, 'color_message_bg');

// For gmail compatibility, including CSS styles in head/body are stripped out therefore styles need to be inline. These variables contain rules which are added to the template inline.
// !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
$styles = array(
    'body'               => "
        background-color: " . $color_main_bg . ";
        font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
    ",
    'wrapper'            => "
        width:100%;
        -webkit-text-size-adjust:none !important;
        margin:0;
        padding: 70px 0 70px 0;
    ",
    'template_container' => "
        box-shadow:0 0 0 1px " . $color_main_bg . " !important;
        border-radius:3px !important;
        background-color: " . $color_message_bg . ";
        border: 1px solid #e9e9e9;
        border-radius:3px !important;
        padding: 20px;
    ",
    'template_header'    => "
        color: #00000;
        border-top-left-radius:3px !important;
        border-top-right-radius:3px !important;
        border-bottom: 0;
        font-weight:bold;
        line-height:100%;
        text-align: center;
        vertical-align:middle;
    ",
    'body_content'       => "
        border-radius:3px !important;
        font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
    ",
    'body_content_inner' => "
        color: #000000;
        font-size:14px;
        font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
        line-height:150%;
        text-align:left;
    ",
    'header_content_h1'  => "
        color: #000000;
        margin:0;
        padding: 28px 24px;
        display:block;
        font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
        font-size:32px;
        font-weight: 500;
        line-height: 1.2;
    ",
    'template_footer'    => "
        border-top:0;
        -webkit-border-radius:3px;
        ",
    'credit'             => "
        border:0;
        color: #000000;
        font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
        font-size:12px;
        line-height:125%;
        text-align:center;
        "
);

?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title><?php echo get_bloginfo('name'); ?></title>
    <style type="text/css">
        a, a:visited {
            color: <?php echo $color_link; ?> !important;
            font-weight: bold;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="<?php echo $styles['body']; ?>">
<div style="<?php echo $styles['wrapper']; ?>">
    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
        <tr>
            <td align="center" valign="top">
                <table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="<?php echo $styles['template_container']; ?>">
                    <?php if ( !empty($header_image_url)) : ?>
                        <tr id="template_header_image">
                            <?php echo '<td><img src="' . esc_url($header_image_url) . '" alt="' . get_bloginfo('name') . '" style="margin-top:0; max-width: 100%; height: auto; width: auto;"/></td>'; ?>
                        </tr>
                    <?php endif; ?>
                    <?php if ( !empty ($main_heading)) : ?>
                        <tr>
                            <td align="center" valign="top">
                                <!-- Header -->
                                <table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style="<?php echo $styles['template_header']; ?>" bgcolor="<?php echo $color_message_bg; ?>">
                                    <tr>
                                        <td>
                                            <h1 style="<?php echo $styles['header_content_h1']; ?>"><?php echo $main_heading; ?></h1>
                                        </td>
                                    </tr>
                                </table>
                                <!-- End Header -->
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td align="center" valign="top">
                            <!-- Body -->
                            <table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
                                <tr>
                                    <td valign="top" style="<?php echo $styles['body_content']; ?>">
                                        <!-- Content -->
                                        <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                            <tr>
                                                <td valign="top">
                                                    <div style="<?php echo $styles['body_content_inner']; ?>">
                                                        <?php echo $email_content; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <!-- End Content --></td>
                                </tr>
                            </table>
                            <!-- End Body --></td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            <!-- Footer -->
                            <table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="<?php echo $styles['template_footer']; ?>">
                                <tr>
                                    <td valign="top">
                                        <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                            <tr>
                                                <td colspan="2" valign="middle" id="credit" style="<?php echo $styles['credit']; ?>">
                                                    <?php echo wpautop(wp_kses_post(wptexturize(apply_filters('cuar/notifications/template/footer-credits',
                                                        '<a href="' . esc_url(home_url()) . '">' . get_bloginfo('name') . '</a>')))); ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- End Footer -->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>