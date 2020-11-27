<?php /** Template version: 1.0.0 */

/** @var string $header_image_url */
/** @var string $main_heading */
/** @var string $email_content */


/** @var CUAR_NotificationsSettingsHelper $settings_helper */
$settings_helper = cuar_addon('notifications')->settings();

$colors = array(
    'color_link'          => $settings_helper->get_email_template_setting($template_id, 'color_link'),
    'main_text_color'     => $settings_helper->get_email_template_setting($template_id, 'main_text_color'),
    'main_title_color'    => $settings_helper->get_email_template_setting($template_id, 'main_title_color'),
    'main_content_bg'     => $settings_helper->get_email_template_setting($template_id, 'main_content_bg'),
    'main_content_color'  => $settings_helper->get_email_template_setting($template_id, 'main_content_color'),
    'main_content_border' => $settings_helper->get_email_template_setting($template_id, 'main_content_border')
);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title><?php echo get_bloginfo('name'); ?></title>

    <style type="text/css">
        /* Take care of image borders and formatting, client hacks */
        img {
            max-width: 600px;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        a img {
            border: none;
        }

        table {
            border-collapse: collapse !important;
        }

        #outlook a {
            padding: 0;
        }

        .ReadMsgBody {
            width: 100%;
        }

        .ExternalClass {
            width: 100%;
        }

        .backgroundTable {
            margin: 0 auto;
            padding: 0;
            width: 100% !important;
        }

        table td {
            border-collapse: collapse;
        }

        .ExternalClass * {
            line-height: 115%;
        }

        .container-for-gmail-android {
            min-width: 600px;
        }

        /* General styling */
        * {
            font-family: Helvetica, Arial, sans-serif;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            width: 100% !important;
            margin: 0 !important;
            height: 100%;
            color: <?php echo $colors['main_text_color']; ?>;
        }

        td {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: <?php echo $colors['main_text_color']; ?>;
            text-align: center;
            line-height: 21px;
        }

        a, a:visited {
            color: <?php echo $colors['color_link']; ?> !important;
            font-weight: bold;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .pull-left {
            text-align: left;
        }

        .pull-right {
            text-align: right;
        }

        .header-lg,
        .header-md,
        .header-sm {
            font-size: 32px;
            font-weight: 600;
            line-height: normal;
            padding: 35px 0 0;
            color: <?php echo $colors['main_title_color']; ?>;
        }

        .header-md {
            font-size: 24px;
        }

        .header-sm {
            padding: 5px 0;
            font-size: 18px;
            line-height: 1.3;
        }

        .content-padding {
            padding: 20px 0 30px;
        }

        .mobile-header-padding-right {
            width: 290px;
            text-align: right;
            padding-left: 10px;
        }

        .mobile-header-padding-left {
            width: 290px;
            text-align: left;
            padding-bottom: 4px;
        }

        .free-text {
            width: 100% !important;
            padding: 10px 60px 0;
        }

        .button {
            padding: 30px 0 0;
        }

        .info-block {
            padding: 0 20px;
            width: 260px;
        }

        .mini-block-container {
            padding: 30px 50px;
            width: 500px;
        }

        td.mini-block {
            background-color: <?php echo $colors['main_content_bg']; ?>;
            width: 498px;
            border: 1px solid <?php echo $colors['main_content_border']; ?>;
            border-radius: 5px;
            padding: 45px 75px;
        }

        .block-rounded {
            width: 260px;
        }

        .info-img {
            width: 258px;
            border-radius: 5px 5px 0 0;
        }

        .force-width-img {
            width: 480px;
            height: 1px !important;
        }

        .force-width-full {
            width: 600px;
            height: 1px !important;
        }

        .user-img img {
            width: 130px;
            border-radius: 5px;
            border: 1px solid #cccccc;
        }

        .user-img {
            text-align: center;
            border-radius: 100px;
            color: <?php echo $colors['color_link']; ?>;
            font-weight: 600;
        }

        td.user-msg {
            padding-top: 10px;
            font-size: 14px;
            text-align: center;
            font-style: italic;
            color: <?php echo $colors['main_content_color']; ?>;
        }

        .mini-img {
            padding: 5px;
            width: 140px;
        }

        .mini-img img {
            border-radius: 5px;
            width: 140px;
        }

        .force-width-gmail {
            min-width: 600px;
            height: 0px !important;
            line-height: 1px !important;
            font-size: 1px !important;
        }

        .mini-imgs {
            padding: 25px 0 30px;
        }
    </style>

    <style type="text/css" media="screen">
        @import url(//fonts.googleapis.com/css?family=Oxygen:400,700);
    </style>

    <style type="text/css" media="screen">
        @media screen {
            /* Thanks Outlook 2013! http://goo.gl/XLxpyl */
            * {
                font-family: 'Oxygen', 'Helvetica Neue', 'Arial', 'sans-serif' !important;
            }
        }
    </style>

    <style type="text/css" media="only screen and (max-width: 480px)">
        /* Mobile styles */
        @media only screen and (max-width: 480px) {

            table[class*="container-for-gmail-android"] {
                min-width: 290px !important;
                width: 100% !important;
            }

            table[class="w320"] {
                width: 320px !important;
            }

            img[class="force-width-gmail"] {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
            }

            td[class*="mobile-header-padding-left"] {
                width: 160px !important;
                padding-left: 0 !important;
            }

            td[class*="mobile-header-padding-right"] {
                width: 160px !important;
                padding-right: 0 !important;
            }

            td[class="mobile-block"] {
                display: block !important;
            }

            td[class="mini-img"],
            td[class="mini-img"] img {
                width: 150px !important;
            }

            td[class="header-lg"] {
                font-size: 24px !important;
                padding-bottom: 5px !important;
            }

            td[class="header-md"] {
                font-size: 18px !important;
                padding-bottom: 5px !important;
            }

            td[class="content-padding"] {
                padding: 5px 0 30px !important;
            }

            td[class="button"] {
                padding: 5px !important;
            }

            td[class*="free-text"] {
                padding: 10px 18px 30px !important;
            }

            img[class="force-width-img"],
            img[class="force-width-full"] {
                display: none !important;
            }

            td[class="info-block"] {
                display: block !important;
                width: 280px !important;
                padding-bottom: 40px !important;
            }

            td[class="info-img"],
            img[class="info-img"] {
                width: 278px !important;
            }

            td[class="mini-block-container"] {
                padding: 8px 20px !important;
                width: 280px !important;
            }

            td[class="mini-block"] {
                padding: 20px !important;
            }

            td[class="user-img"] {
                display: block !important;
                text-align: center !important;
                width: 100% !important;
                padding-bottom: 10px;
            }

            td[class="user-msg"] {
                display: block !important;
                padding-bottom: 20px;
            }
        }
    </style>
</head>

<body bgcolor="#f7f7f7">
<table align="center" cellpadding="0" cellspacing="0" class="container-for-gmail-android" width="100%">
    <!-- header -->
    <tr>
        <td align="left" valign="top" width="100%" style="background:repeat-x url(<?php echo CUARDE_PLUGIN_URL . '/assets/notifications/cleany/bg_top.jpg'; ?>) #ffffff;">
            <center>
                <table cellspacing="0" cellpadding="0" width="100%" bgcolor="#ffffff" background="<?php echo CUARDE_PLUGIN_URL . '/assets/notifications/cleany/bg_top.jpg'; ?>" style="border-top: 1px solid #eeeeee; background-color:transparent">
                    <tr>
                        <td width="100%" height="80" valign="top" style="text-align: center; vertical-align:middle;">
                            <!--[if gte mso 9]>
                            <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="mso-width-percent:1000;height:80px; v-text-anchor:middle;">
                                <v:fill type="tile" src="<?php echo CUARDE_PLUGIN_URL . '/assets/notifications/cleany/bg_top.jpg'; ?>" color="#ffffff"/>
                                <v:textbox inset="0,0,0,0"><![endif]-->
                            <center>
                                <table cellpadding="0" cellspacing="0" width="600" class="w320">
                                    <tr>
                                        <td class="pull-left mobile-header-padding-left" style="vertical-align: middle; text-align: center;">
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
                            </center>
                            <!--[if gte mso 9]></v:textbox></v:rect><![endif]-->
                        </td>
                    </tr>
                </table>
            </center>
        </td>
    </tr>
    <!-- END:header -->
    <!-- body -->
    <tr>
        <td align="center" valign="top" width="100%" style="background-color: #f7f7f7;" class="content-padding">
            <center>
                <table cellspacing="0" cellpadding="0" width="600" class="w320">
                    <?php if ( !empty ($main_heading)) : ?>
                        <tr>
                            <td class="header-lg">
                                <?php echo $main_heading; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="mini-block-container">
                            <table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:separate !important;">
                                <tr>
                                    <td class="mini-block">
                                        <table cellspacing="0" cellpadding="0" width="100%">
                                            <tr>
                                                <td class="user-msg">
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
    <!-- END:body -->
    <!-- footer -->
    <tr style="border-top: 1px solid #e5e5e5;">
        <td align="center" valign="top" width="100%" style="background-color: #f7f7f7; height: 100px;">
            <center>
                <table cellspacing="0" cellpadding="0" width="600" class="w320">
                    <tr>
                        <td style="padding: 25px 0 25px">
                            <?php echo wpautop(wp_kses_post(wptexturize(apply_filters('cuar/notifications/template/footer-credits',
                                sprintf(__('This is an automatic notification message sent by <a href="%s">%s</a>', 'cuarde'), esc_url(home_url()), get_bloginfo('name')))))); ?>
                        </td>
                    </tr>
                </table>
            </center>
        </td>
    </tr>
    <!-- END:footer -->
</table>
</body>
</html>
