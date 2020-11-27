<?php
/** Template version: 3.0.0
 *
 * -= 3.0.0 =-
 * - Initial version
 *
 */ ?>

<?php /** @var CUAR_Invoice $invoice */ ?>

<?php
$template_id = 'minimal';

/** @var CUAR_InvoiceSettingsHelper $settings_helper */
$settings_helper = cuar_addon('invoicing')->settings();
?>

<?php

// Page Inner Margins
$backTop = "0mm";    // value(mm, px, pt, %)
$backBottom = "0mm";    // value(mm, px, pt, %)
$backLeft = "0mm";    // value(mm, px, pt, %)
$backRight = "0mm";    // value(mm, px, pt, %)

// Page Style
$orientation = $settings_helper->get_pdf_template_setting($template_id, 'page_orientation');

// Page background
$backImg = $settings_helper->get_pdf_template_setting($template_id, 'back_img');
$backColor = $settings_helper->get_pdf_template_setting($template_id, 'color_back');
$backImgX = $settings_helper->get_pdf_template_setting($template_id, 'back_img_x');
$backImgY = $settings_helper->get_pdf_template_setting($template_id, 'back_img_y');
$backImgW = $settings_helper->get_pdf_template_setting($template_id, 'back_img_w');

// Fonts
$fontDefault = 'freemono';

// Page footer
$footerThanksMessage = $settings_helper->get_pdf_template_setting($template_id, 'footer_thanks_message');
$footerBackground = $settings_helper->get_pdf_template_setting($template_id, 'color_footer_bg');
$footerTextColor = $settings_helper->get_pdf_template_setting($template_id, 'color_footer_text');
$footerThanksMessageSize = $settings_helper->get_pdf_template_setting($template_id, 'footer_thanks_message_size');

// Colors
$textColor = $settings_helper->get_pdf_template_setting($template_id, 'color_text');
$accentColor = $settings_helper->get_pdf_template_setting($template_id, 'color_accent');
$itemsColor = $settings_helper->get_pdf_template_setting($template_id, 'color_items');
$secondaryContentsColor = $settings_helper->get_pdf_template_setting($template_id, 'color_secondary_contents');
$borderColor = $settings_helper->get_pdf_template_setting($template_id, 'color_border');
$borderSecondaryColor = $settings_helper->get_pdf_template_setting($template_id, 'color_secondary_border');

?>
<style>

    .main-table {
        width: 100%;
        margin-top: 30pt;
        color: <?php echo $textColor; ?>;
        font-size: 10pt;
    }

    table,
    tr,
    td {
        width: 100%;
    }

    table#items {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 20pt;
    }

    table#items th,
    table#items td {
        text-align: left;
        padding: 5pt 10pt;
    }

    table#items th {
        font-weight: bold;
        text-transform: uppercase;
        font-size: 8pt;
        vertical-align: top;
    }

    table#items th small {
        text-transform: lowercase;
    }

    table#items td {
        vertical-align: middle;
        margin: 0;
        line-height: 10pt;
    }

    table#items td h3 {
        font-size: 11pt;
        font-weight: normal;
        margin: 0;
        line-height: 12pt;
        color: <?php echo $itemsColor; ?>
    }

    table#items th.no,
    table#items td.no {
        font-weight: bold;
        padding-left: 3pt;
    }

    table#items td.total,
    table#items th.total {
        padding-right: 3pt;
    }

    table#items .desc,
    table#items .qty {
        text-align: left;
    }

    table#items .unit {
        text-align: right;
    }

    table#items td.unit,
    table#items td.qty,
    table#items td.total {
        font-size: 8pt;
        line-height: 9pt;
    }

    table#items td.unit p,
    table#items td.qty p,
    table#items td.total p {
        margin: 0;
        padding: 0;
        font-size: 8pt;
        line-height: 9pt;
    }

    table#items tbody tr:last-child td {
        border: none;
    }

    table#totals td,
    table#totals th {
        padding: 15pt 0;
        border-bottom: none;
        width: 50%;
    }

    table#totals tr {
        width: 100%;
    }

    table#totals td {
        text-align: right;
    }

    table#extras p {
        font-size: 8pt;
        margin: 0;
        line-height: 7pt;
    }

    table#extras th,
    table#totals th,
    table#items td.no,
    .addresses-title,
    .secondary-content-color {
        color: <?php echo $secondaryContentsColor; ?>;
    }

    .text-color-accent {
        color: <?php echo $accentColor; ?>;
    }

    .big-top-border {
        border-top: 1mm solid <?php echo $borderColor; ?>;
    }

    .big-top-border-accent {
        border-top: 1mm solid <?php echo $accentColor; ?>
    }

    .thin-top-border {
        border-top: 0.3mm solid <?php echo $borderColor; ?>;
    }

    .thin-top-border-neutral {
        border-top: 0.3mm solid <?php echo $borderSecondaryColor; ?>
    }

</style>
<page backtop="<?php echo $backTop; ?>" backbottom="<?php echo $backBottom; ?>" backleft="<?php echo $backLeft; ?>" backright="<?php echo $backRight; ?>" backcolor="<?php echo $backColor; ?>" orientation="<?php echo $orientation; ?>" backimg="<?php echo $backImg; ?>" backimgx="<?php echo $backImgX; ?>" backimgy="<?php echo $backImgY; ?>" backimgw="<?php echo $backImgW; ?>">


    <table class="main-table text-color-accent" cellpadding="0" cellspacing="0" style="margin-top: 0;">
        <tr>
            <td style="width: 58%; vertical-align: bottom;">
                <?php $logo_url = cuar_get_the_invoice_emitter_logo($invoice->ID);
                if ( !empty($logo_url)) : ?>
                    <img src="<?php echo esc_attr($logo_url); ?>" style="max-width: 100%; max-height: 100pt; height: auto;"/>
                <?php else:
                    $emitter_company = cuar_get_the_invoice_emitter_company($invoice->ID);
                    $emitter_name = cuar_get_the_invoice_emitter_name($invoice->ID);
                    if ( !empty($emitter_company)) :
                        echo $emitter_company;
                    elseif ( !empty($emitter_name)) :
                        echo $emitter_name;
                    else :
                        bloginfo('name');
                    endif;
                endif; ?>
            </td>
            <td style="width: 42%; vertical-align: bottom;">
                <p style="font-size: 20pt; font-weight: bold; text-transform: uppercase;">
                    <?php printf(__('Invoice %s', 'cuarde'), cuar_get_the_invoice_number($invoice->ID)); ?>
                </p>
            </td>
        </tr>
    </table>

    <table id="page-main" class="main-table" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <table border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="width: 58%;">
                            <span class="addresses-title" style="text-transform: uppercase; font-size: 14pt; font-weight: bold; margin: 0;"><?php _e('To', 'cuarde'); ?></span>
                            <p style="font-size: 11pt;"><?php cuar_print_address(cuar_get_the_invoice_billing_address($invoice->ID), 'billing_address', '', 'pdf'); ?></p>
                        </td>
                        <td style="width: 42%;">
                            <span class="addresses-title" style="text-transform: uppercase; font-size: 14pt; font-weight: bold; margin: 0;"><?php _e('From', 'cuarde'); ?></span>
                            <p style="font-size: 11pt;"><?php cuar_print_address(cuar_get_the_invoice_emitter_address($invoice->ID), 'emitter_address', '', 'pdf'); ?></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <?php $invoice_header = cuar_get_the_invoice_header($invoice->ID);
    if ( !empty($invoice_header)) { ?>
        <table class="main-table thin-top-border-neutral secondary-content-color" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <?php cuar_the_invoice_header($invoice->ID); ?>
                </td>
            </tr>
        </table>
    <?php } ?>

    <table id="items" class="main-table" cellpadding="0" cellspacing="0">
        <thead style="width:100%;">
        <tr style="width:100%;">
            <th class="no big-top-border" style="width: 5%; vertical-align: top;">#</th>
            <th class="desc big-top-border" style="width:53%; vertical-align: top;">
                <?php _e('Item', 'cuarde'); ?></th>
            <th class="qty big-top-border" style="width:14%; vertical-align: top;">
                <?php _e('Quantity', 'cuarde'); ?></th>
            <th class="unit big-top-border" style="width:14%; vertical-align: top;">
                <?php _e('Unit price', 'cuarde'); ?><br>
                <small><?php _e('(excl. taxes)', 'cuarde'); ?></small>
            </th>
            <th class="total big-top-border" style="width:14%; vertical-align: top; text-align: right;">
                <?php _e('Total price', 'cuarde'); ?><br>
                <small><?php _e('(excl. taxes)', 'cuarde'); ?></small>
            </th>
        </tr>
        </thead>
        <tbody style="width:100%;">
        <?php
        $c = 0;
        foreach (cuar_get_the_invoice_items($invoice->ID) as $i => $item) : $c++; ?>
            <tr style="width:100%; vertical-align: top;">
                <td class="no" style="width: 5%; vertical-align: top;"><?php echo $c; ?></td>
                <td class="desc" style="vertical-align: top; width:53%;">
                    <h3><?php cuar_the_invoice_item_title($invoice->ID, $item); ?></h3>
                    <?php if ( !empty($item['description'])) : ?>
                        <small style="margin-top: 8pt;"><?php cuar_the_invoice_item_description($invoice->ID, $item); ?></small>
                    <?php endif; ?>
                </td>
                <td class="qty" style="vertical-align: top; width:14%;">
                    <p><?php cuar_the_invoice_item_quantity($invoice->ID, $item); ?></p></td>
                <td class="unit" style="vertical-align: top; width:14%;">
                    <p><?php cuar_the_invoice_item_unit_price($invoice->ID, $item); ?></p></td>
                <td class="total" style="vertical-align: top; width:14%; text-align: right;">
                    <p><?php cuar_the_invoice_item_total_price($invoice->ID, $item); ?></p></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <table class="main-table" cellpadding="0" cellspacing="0">
        <tr style="width:100%; vertical-align: top;">
            <td class="thin-top-border secondary-content-color" style="width: 58%; vertical-align: top;">
                <?php $notice_text = cuar_get_the_invoice_notice($invoice->ID);
                if ( !empty($notice_text)) { ?>
                    <table cellspacing="0" cellpadding="0" style="width:100%;">
                        <tr>
                            <td style="padding-right: 20pt; padding-top: 15pt; text-align: justify; font-size: 8pt;">
                                <p style="margin-bottom: 5pt; margin-top: 0; text-transform: uppercase; font-size: 10pt; padding:0; font-weight: bold;">
                                    <?php _e('Notice', 'cuarde'); ?>
                                </p>
                                <?php echo $notice_text; ?>
                            </td>
                        </tr>
                    </table>
                <?php } ?>
            </td>
            <td class="thin-top-border" style="width: 42%; vertical-align: top;">
                <table id="totals" cellspacing="0" cellpadding="0" style="width:100%;">
                    <tr>
                        <th><?php _e('Subtotal (excl. taxes)', 'cuarde'); ?></th>
                        <td><?php cuar_the_invoice_total($invoice->ID, 'items'); ?></td>
                    </tr>
                    <?php if (cuar_is_discount_before_tax($invoice->ID)) : ?>
                        <tr>
                            <th class="thin-top-border-neutral"><?php cuar_the_invoice_discount_description($invoice->ID); ?></th>
                            <td class="thin-top-border-neutral"><?php cuar_the_invoice_total($invoice->ID, 'discount'); ?></td>
                        </tr>
                        <tr>
                            <th class="thin-top-border-neutral"><?php cuar_the_invoice_taxes_description($invoice->ID); ?></th>
                            <td class="thin-top-border-neutral"><?php cuar_the_invoice_total($invoice->ID, 'tax'); ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <th class="thin-top-border-neutral"><?php cuar_the_invoice_taxes_description($invoice->ID); ?></th>
                            <td class="thin-top-border-neutral"><?php cuar_the_invoice_total($invoice->ID, 'tax'); ?></td>
                        </tr>
                        <tr>
                            <th class="thin-top-border-neutral"><?php cuar_the_invoice_discount_description($invoice->ID); ?></th>
                            <td class="thin-top-border-neutral"><?php cuar_the_invoice_total($invoice->ID, 'discount'); ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr class="cuar-total">
                        <th class="big-top-border-accent text-color-accent">
                            <h3><?php _e('Total', 'cuarde'); ?></h3>
                        </th>
                        <td class="big-top-border-accent text-color-accent">
                            <h3><?php cuar_the_invoice_total($invoice->ID); ?></h3>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table id="extras" class="main-table big-top-border" cellspacing="0" cellpadding="0">
        <tr>
            <th style="width:58%; padding-top: 20pt; text-align: right;">
                <p style="padding: 0 20pt; text-transform: uppercase;"><?php _e('Created', 'cuarde'); ?></p>
            </th>
            <td style="width:42%; padding-top: 20pt;">
                <p style="padding: 0;"><?php cuar_the_invoice_date($invoice->ID); ?></p>
            </td>
        </tr>
        <tr>
            <th style="width:58%; text-align: right;">
                <p style="padding:0 20pt; text-transform: uppercase;"><?php _e('Status', 'cuarde'); ?></p>
            </th>
            <td style="width:42%;"><p style="padding: 0;"><?php cuar_the_invoice_status($invoice->ID); ?></p></td>
        </tr>
        <tr>
            <th style="width:58%; text-align: right; text-transform: uppercase;"><p style="padding: 0 20pt;"><?php _e('Due date', 'cuarde'); ?></p></th>
            <td style="width:42%;"><p style="padding: 0;">
                    <?php if (cuar_get_the_invoice_due_date($invoice->ID) != '') {
                        cuar_the_invoice_due_date($invoice->ID);
                    } else {
                        _e('None', 'cuarde');
                    } ?></p>
            </td>
        </tr>
        <tr>
            <th style="width:58%; text-align: right; text-transform: uppercase;"><p style="padding: 0 20pt;"><?php _e('Amount due', 'cuarde'); ?></p></th>
            <td style="width:42%;"><p style="padding: 0;"><?php cuar_the_invoice_total($invoice->ID); ?></p></td>
        </tr>
        <?php $mode = cuar_get_the_invoice_payment_mode($invoice->ID);
        if ( !empty($mode)): ?>
            <tr>
                <th style="width:58%; text-align: right; text-transform: uppercase;"><p style="padding: 0 20pt;"><?php _e('Payment mode', 'cuarde'); ?></p>
                </th>
                <td style="width:42%;"><p style="padding: 0;"><?php echo $mode; ?></p></td>
            </tr>
        <?php endif; ?>
    </table>

    <?php $invoice_footer = cuar_get_the_invoice_footer($invoice->ID);
    if ( !empty($invoice_footer) || !empty($footerThanksMessage)) { ?>
        <table id="footer" cellpadding="0" cellspacing="0" style="background: <?php echo $footerBackground; ?>; color: <?php echo $footerTextColor; ?>; vertical-align: bottom; margin-top: 20pt;">
            <tr style="vertical-align: bottom; ">
                <td style="width: 70%; vertical-align: bottom; padding: 0 20pt 20pt;">
                    <?php echo $invoice_footer; ?>
                </td>
                <td style="width: 30%; vertical-align: bottom; text-align: right; padding: 0 20pt 20pt; font-weight: bold; font-size: <?php echo $footerThanksMessageSize; ?>;">
                    <p><?php echo $footerThanksMessage; ?></p>
                </td>
            </tr>
        </table>
    <?php } ?>
</page>