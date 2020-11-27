<?php /** Template version: 1.0.0 */

/** @var string $header_image_url */
/** @var string $main_heading */
/** @var string $email_content */

/** @var CUAR_NotificationsSettingsHelper $settings_helper */
$settings_helper = cuar_addon('notifications')->settings();

$colors = array(
    'color_link'       => $settings_helper->get_email_template_setting($template_id, 'color_link'),
    'main_text_color'  => $settings_helper->get_email_template_setting($template_id, 'main_text_color'),
    'main_title_color' => $settings_helper->get_email_template_setting($template_id, 'main_title_color'),
    'main_content_bg'  => $settings_helper->get_email_template_setting($template_id, 'main_content_bg'),
);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml><![endif]-->
    <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="format-detection" content="date=no"/>
    <meta name="format-detection" content="address=no"/>
    <meta name="format-detection" content="telephone=no"/>
    <title><?php echo get_bloginfo('name'); ?></title>


    <style type="text/css" media="screen">
        /* Linked Styles */
        body {
            padding: 0 !important;
            margin: 0 !important;
            display: block !important;
            background: #1e1e1e;
            -webkit-text-size-adjust: none
        }

        a, a:visited {
            color: <?php echo $colors['color_link']; ?> !important;
            text-decoration: none
        }

        p {
            padding: 0 !important;
            margin: 0 !important
        }

        /* Mobile styles */
    </style>
    <style media="only screen and (max-device-width: 480px), only screen and (max-width: 480px)" type="text/css">
        @media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
            div[class='mobile-br-5'] {
                height: 5px !important;
            }

            div[class='mobile-br-10'] {
                height: 10px !important;
            }

            div[class='mobile-br-15'] {
                height: 15px !important;
            }

            div[class='mobile-br-20'] {
                height: 20px !important;
            }

            div[class='mobile-br-25'] {
                height: 25px !important;
            }

            div[class='mobile-br-30'] {
                height: 30px !important;
            }

            th[class='m-td'],
            td[class='m-td'],
            div[class='hide-for-mobile'],
            span[class='hide-for-mobile'] {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
                font-size: 0 !important;
                line-height: 0 !important;
                min-height: 0 !important;
            }

            span[class='mobile-block'] {
                display: block !important;
            }

            div[class='wgmail'] img {
                min-width: 320px !important;
                width: 320px !important;
            }

            div[class='img-m-center'] {
                text-align: center !important;
            }

            div[class='fluid-img'] img,
            td[class='fluid-img'] img {
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
            }

            table[class='mobile-shell'] {
                width: 100% !important;
                min-width: 100% !important;
            }

            td[class='td'] {
                width: 100% !important;
                min-width: 100% !important;
            }

            table[class='center'] {
                margin: 0 auto;
            }

            td[class='column-top'],
            th[class='column-top'],
            td[class='column'],
            th[class='column'] {
                float: left !important;
                width: 100% !important;
                display: block !important;
            }

            td[class='content-spacing'] {
                width: 15px !important;
            }

            div[class='h2'] {
                font-size: 44px !important;
                line-height: 48px !important;
            }
        }
    </style>
</head>
<body class="body" style="padding:0 !important; margin:0 !important; display:block !important; background:#1e1e1e; -webkit-text-size-adjust:none">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#1e1e1e">
    <tr>
        <td align="center" valign="top">
            <!-- Top -->
            <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#161616">
                <tr>
                    <td align="center" valign="top">
                        <table width="600" border="0" cellspacing="0" cellpadding="0" class="mobile-shell">
                            <tr>
                                <td class="td" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; width:600px; min-width:600px; Margin:0" width="600">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td class="content-spacing" style="font-size:0pt; line-height:0pt; text-align:left" width="20"></td>
                                            <td>
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                    <tr>
                                                        <td height="10" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                            &nbsp;
                                                        </td>
                                                    </tr>
                                                </table>

                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td>
                                                            <div class="text-header" style="color:#666666; font-family:Arial, sans-serif; min-width:initial !important; font-size:12px; line-height:16px; text-align:left">
                                                                <a href="#" target="_blank" class="link-1" style="color:#666666; text-decoration:none">
                                                                    <span class="link-1" style="color:#666666; text-decoration:none"><img src="<?php echo CUARDE_PLUGIN_URL . "/assets/notifications/muscard/ico_webversion.jpg"; ?>" border="0" width="14" height="16" alt="" style="vertical-align: middle;"/>&nbsp; <?php echo sprintf(__('From %s', 'cuarde'), get_bloginfo('name')); ?></span>
                                                                </a>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="text-header-1" style="color:#666666; font-family:Arial, sans-serif; min-width:initial !important; font-size:12px; line-height:16px; text-align:right">
                                                                <a href="#" target="_blank" class="link-1" style="color:#666666; text-decoration:none">
                                                                    <span class="link-1" style="color:#666666; text-decoration:none"><img src="<?php echo CUARDE_PLUGIN_URL . "/assets/notifications/muscard/ico_forward.jpg"; ?>" border="0" width="14" height="16" alt="" style="vertical-align: middle;"/>&nbsp; <?php echo sprintf(__('On %s', 'cuarde'), date(get_option('date_format'))); ?></span>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                    <tr>
                                                        <td height="10" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                            &nbsp;
                                                        </td>
                                                    </tr>
                                                </table>

                                            </td>
                                            <td class="content-spacing" style="font-size:0pt; line-height:0pt; text-align:left" width="20"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!-- END Top -->
            <!-- Header -->
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="content-spacing" style="font-size:0pt; line-height:0pt; text-align:left" width="20"></td>
                    <td>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                            <tr>
                                <td height="15" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">&nbsp;</td>
                            </tr>
                        </table>

                        <div class="img-center" style="font-size:0pt; line-height:0pt; text-align:center">
                            <a href="<?php echo esc_url(get_site_url()); ?>" title="<?php echo sprintf(esc_attr__('Visit our site: %s', 'cuarde'), get_bloginfo('name')); ?>">
                                <?php if ( !empty($header_image_url)) :
                                    echo '<img width="auto" height="50" src="' . esc_url($header_image_url) . '" alt="' . esc_attr(get_bloginfo('name')) . '" />';
                                else:
                                    echo get_bloginfo('name');
                                endif; ?>
                            </a>
                        </div>

                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                            <tr>
                                <td height="15" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">&nbsp;</td>
                            </tr>
                        </table>

                    </td>
                    <td class="content-spacing" style="font-size:0pt; line-height:0pt; text-align:left" width="20"></td>
                </tr>
            </table>
            <!-- END Header -->


            <table width="600" border="0" cellspacing="0" cellpadding="0" class="mobile-shell">
                <tr>
                    <td class="td" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; width:600px; min-width:600px; Margin:0" width="600">

                        <!-- Main -->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <!-- Head -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#d2973b">
                                        <tr>
                                            <td>
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="27">
                                                            <img src="<?php echo CUARDE_PLUGIN_URL . "/assets/notifications/muscard/top_left.jpg"; ?>" border="0" width="27" height="27" alt=""/>
                                                        </td>
                                                        <td>
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" height="3" bgcolor="#e6ae57">
                                                                        &nbsp;
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                <tr>
                                                                    <td height="24" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                        &nbsp;
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                        </td>
                                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="27">
                                                            <img src="<?php echo CUARDE_PLUGIN_URL . "/assets/notifications/muscard/top_right.jpg"; ?>" border="0" width="27" height="27" alt=""/>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="3" bgcolor="#e6ae57"></td>
                                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="10"></td>
                                                        <td>
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                <tr>
                                                                    <td height="15" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                        &nbsp;
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                <tr>
                                                                    <td height="5" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                        &nbsp;
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                            <div class="h2" style="color:<?php echo $colors['main_title_color']; ?>; font-family:Georgia, serif; min-width:initial !important; font-size:35px; line-height:64px; text-align:center">
                                                                <em><?php if ( !empty ($main_heading)) : ?><?php echo $main_heading; ?><?php endif; ?></em>
                                                            </div>
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                <tr>
                                                                    <td height="35" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                        &nbsp;
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                        </td>
                                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="10"></td>
                                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="3" bgcolor="#e6ae57"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- END Head -->

                                    <!-- Body -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $colors['main_content_bg']; ?>">
                                        <tr>
                                            <td>
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td class="content-spacing" style="font-size:0pt; line-height:0pt; text-align:left" width="20"></td>
                                                        <td>
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                <tr>
                                                                    <td height="35" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                        &nbsp;
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                            <div class="h5-2-center" style="color:<?php echo $colors['main_text_color']; ?>; font-family:Arial, sans-serif; min-width:initial !important; font-size:16px; line-height:26px; text-align:center">
                                                                <?php echo $email_content; ?>
                                                            </div>

                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                <tr>
                                                                    <td height="25" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                        &nbsp;
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td class="content-spacing" style="font-size:0pt; line-height:0pt; text-align:left" width="20"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- END Body -->

                                    <!-- Foot -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#d2973b">
                                        <tr>
                                            <td>
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="3" bgcolor="#e6ae57"></td>
                                                        <td>
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                <tr>
                                                                    <td height="30" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                        &nbsp;
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                <tr>
                                                                    <td height="15" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                        &nbsp;
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="3" bgcolor="#e6ae57"></td>
                                                    </tr>
                                                </table>
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="27">
                                                            <img src="<?php echo CUARDE_PLUGIN_URL . "/assets/notifications/muscard/bot_left.jpg"; ?>" border="0" width="27" height="27" alt=""/>
                                                        </td>
                                                        <td>
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                <tr>
                                                                    <td height="24" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                                        &nbsp;
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" height="3" bgcolor="#e6ae57">
                                                                        &nbsp;
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="27">
                                                            <img src="<?php echo CUARDE_PLUGIN_URL . "/assets/notifications/muscard/bot_right.jpg"; ?>" border="0" width="27" height="27" alt=""/>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- END Foot -->
                                </td>
                            </tr>
                        </table>
                        <!-- END Main -->

                        <!-- Footer -->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="content-spacing" style="font-size:0pt; line-height:0pt; text-align:left" width="20"></td>
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                        <tr>
                                            <td height="30" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                &nbsp;
                                            </td>
                                        </tr>
                                    </table>

                                    <div class="text-footer" style="color:#666666; font-family:Arial, sans-serif; min-width:initial !important; font-size:12px; line-height:18px; text-align:center">
                                        <?php echo wpautop(wp_kses_post(wptexturize(apply_filters('cuar/notifications/template/footer-credits',
                                            sprintf(__('This is an automatic notification message sent by <a href="%s">%s</a>', 'cuarde'), esc_url(home_url()), get_bloginfo('name')))))); ?>
                                    </div>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                        <tr>
                                            <td height="30" class="spacer" style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                &nbsp;
                                            </td>
                                        </tr>
                                    </table>

                                </td>
                                <td class="content-spacing" style="font-size:0pt; line-height:0pt; text-align:left" width="20"></td>
                            </tr>
                        </table>
                        <!-- END Footer -->
                    </td>
                </tr>
            </table>
            <div class="wgmail" style="font-size:0pt; line-height:0pt; text-align:center">
                <img src="<?php echo CUARDE_PLUGIN_URL . "/assets/notifications/muscard/gmail_fix.gif"; ?>" width="600" height="1" style="min-width:600px" alt="" border="0"/>
            </div>
        </td>
    </tr>
</table>
</body>
</html>
