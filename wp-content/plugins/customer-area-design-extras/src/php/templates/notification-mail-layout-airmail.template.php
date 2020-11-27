<?php /** Template version: 1.0.0 */

/** @var string $header_image_url */
/** @var string $main_heading */
/** @var string $email_content */

/** @var CUAR_NotificationsSettingsHelper $settings_helper */
$settings_helper = cuar_addon('notifications')->settings();

$colors = array(
    'primary'           => $settings_helper->get_email_template_setting($template_id, 'color_link'),
    'main_background'   => $settings_helper->get_email_template_setting($template_id, 'main_background'),
    'main_text_color'   => $settings_helper->get_email_template_setting($template_id, 'main_text_color'),
    'main_title_color'  => $settings_helper->get_email_template_setting($template_id, 'main_title_color'),
    'footer_background' => $settings_helper->get_email_template_setting($template_id, 'footer_background'),
    'footer_border'     => $settings_helper->get_email_template_setting($template_id, 'footer_border'),
    'footer_text_color' => $settings_helper->get_email_template_setting($template_id, 'footer_text_color'),
    'back_img'          => $settings_helper->get_email_template_setting($template_id, 'back_img'),
    'back_img_color'    => $settings_helper->get_email_template_setting($template_id, 'back_img_color')
);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=320, initial-scale=1"/>
    <title><?php echo get_bloginfo('name'); ?></title>
    <style type="text/css">

        /* ----- Client Fixes ----- */

        /* Force Outlook to provide a "view in browser" message */
        #outlook a {
            padding: 0;
        }

        /* Force Hotmail to display emails at full width */
        .ReadMsgBody {
            width: 100%;
        }

        .ExternalClass {
            width: 100%;
        }

        /* Force Hotmail to display normal line spacing */
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
            line-height: 100%;
        }

        /* Prevent WebKit and Windows mobile changing default text sizes */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        /* Remove spacing between tables in Outlook 2007 and up */
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        /* Allow smoother rendering of resized image in Internet Explorer */
        img {
            -ms-interpolation-mode: bicubic;
        }

        /* ----- Reset ----- */

        html,
        body,
        .body-wrap,
        .body-wrap-cell {
            margin: 0;
            padding: 0;
            background: <?php echo $colors['footer_background']; ?>;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: <?php echo $colors['main_text_color']; ?>;
            text-align: left;
        }

        img {
            border: 0;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        td, th {
            text-align: left;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: <?php echo $colors['main_text_color']; ?>;
            line-height: 1.5em;
        }

        a, a:visited {
            color: <?php echo $colors['primary']; ?> !important;
            font-weight: bold;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* ----- General ----- */

        td.center {
            text-align: center;
        }

        .body-padding {
            padding: 24px 40px 40px;
        }

        table.full-width-gmail-android {
            width: 100% !important;
        }

        /* ----- Header ----- */
        .header {
            font-weight: bold;
            font-size: 16px;
            line-height: 16px;
            height: 16px;
            padding-top: 19px;
            padding-bottom: 7px;
        }

        /* ----- Body ----- */

        .body .body-padded {
            padding-top: 34px;
        }

        .body-thanks-cell {
            padding: 25px 0 10px;
        }

        .body-signature-cell {
            padding: 0 0 30px;
        }

        /* ----- Footer ----- */

        .footer a {
            font-size: 12px;
        }

        /* ----- Soapbox ----- */

        .soapbox .soapbox-title {
            text-align: center;
            font-size: 30px;
            padding-bottom: 20px;
            color: <?php echo $colors['main_title_color']; ?>;
        }

    </style>

    <style type="text/css" media="only screen">
        @media only screen and (max-width: 505px) {

            *[class*="w320"] {
                width: 320px !important;
            }

        }
    </style>

    <style type="text/css" media="only screen and (max-width: 650px)">
        @media only screen and (max-width: 650px) {
            * {
                font-size: 16px !important;
            }

            table[class*="w320"] {
                width: 320px !important;
            }

            td[class="mobile-center"],
            div[class="mobile-center"] {
                text-align: center !important;
            }

            td[class*="body-padding"] {
                padding: 20px !important;
            }

            td[class="mobile"] {
                text-align: right;
                vertical-align: top;
            }
        }
    </style>

</head>
<body style="padding:0; margin:0; display:block; background: <?php echo $colors['footer_background']; ?>; -webkit-text-size-adjust:none">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td valign="top" align="left" width="100%" style="background: <?php echo $colors['footer_background']; ?>;">
            <center>

                <table class="w320 full-width-gmail-android" style="background: url(<?php echo $colors['back_img']; ?>) repeat-x <?php echo $colors['back_img_color']; ?>;" cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                        <td width="100%" height="48" valign="top">
                            <!--[if gte mso 9]>
                            <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="mso-width-percent:1000;height:49px;">
                                <v:fill type="tile" src="<?php echo $colors['back_img']; ?>" color="<?php echo $colors['back_img_color']; ?>"/>
                                <v:textbox inset="0,0,0,0">                            <![endif]-->
                            <table class="full-width-gmail-android" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td class="header center" width="100%">
                                        <a href="<?php echo esc_url(get_site_url()); ?>" title="<?php echo sprintf(esc_attr__('Visit our site: %s', 'cuarde'), get_bloginfo('name')); ?>">
                                            <?php if ( !empty($header_image_url)) :
                                                echo '<img width="auto" height="50" src="' . esc_url($header_image_url) . '" alt="' . esc_attr(get_bloginfo('name')) . '" />';
                                            else:
                                                echo get_bloginfo('name');
                                            endif; ?>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <!--[if gte mso 9]>                            </v:textbox>                            </v:rect><![endif]-->
                        </td>
                    </tr>
                </table>

                <table cellspacing="0" cellpadding="0" width="100%" bgcolor="<?php echo $colors['main_background']; ?>">
                    <tr>
                        <td align="center">
                            <center>
                                <table class="w320" cellspacing="0" cellpadding="0" width="500">
                                    <tr>
                                        <td class="body-padding mobile-padding">

                                            <?php if ( !empty ($main_heading)) : ?>
                                                <table class="soapbox" cellpadding="0" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td class="soapbox-title">
                                                            <?php echo $main_heading; ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            <?php endif; ?>

                                            <table class="body">
                                                <tr>
                                                    <td class="body-padded">
                                                        <table class="body-text" cellspacing="0" cellpadding="0" width="100%">
                                                            <tr>
                                                                <td class="body-text-cell" style="text-align:left; padding-bottom:30px;">
                                                                    <?php echo $email_content; ?>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>


                                        </td>
                                    </tr>
                                </table>
                            </center>
                        </td>
                    </tr>
                </table>

                <table class="w320" bgcolor="<?php echo $colors['footer_background']; ?>" cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                        <td style="border-top: 6px solid <?php echo $colors['footer_border']; ?>;" align="center">
                            <center>
                                <table class="w320" cellspacing="0" cellpadding="0" width="500" bgcolor="<?php echo $colors['footer_background']; ?>">
                                    <tr>
                                        <td>
                                            <table cellpadding="0" cellspacing="0" width="100%" bgcolor="<?php echo $colors['footer_background']; ?>">
                                                <tr>
                                                    <td class="center" style="padding:25px; text-align:center; color: <?php echo $colors['footer_text_color']; ?>">
                                                        <?php echo wpautop(wp_kses_post(wptexturize(apply_filters('cuar/notifications/template/footer-credits',
                                                            sprintf(__('This is an automatic notification message sent by <a href="%s">%s</a>', 'cuarde'), esc_url(home_url()), get_bloginfo('name')))))); ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </center>
                        </td>
                    </tr>
                </table>

            </center>
        </td>
    </tr>
</table>
</body>
</html>