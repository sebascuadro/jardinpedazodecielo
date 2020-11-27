<?php
/** Template version: 3.0.0
 *
 * -= 3.0.0 =-
 * - Initial version
 *
 */ ?>

<?php /** @var CUAR_Invoice $invoice */ ?>

<?php
$template_id = 'newgen';

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

// Colors
$textColor = $settings_helper->get_pdf_template_setting($template_id, 'color_text');
$neutralColor = $settings_helper->get_pdf_template_setting($template_id, 'color_neutral');
$neutralTextColor = $settings_helper->get_pdf_template_setting($template_id, 'color_neutral_text');
$primaryColor = $settings_helper->get_pdf_template_setting($template_id, 'color_primary');
$primaryTextColor = $settings_helper->get_pdf_template_setting($template_id, 'color_primary_text');
$secondaryColor = $settings_helper->get_pdf_template_setting($template_id, 'color_secondary');
$secondaryTextColor = $settings_helper->get_pdf_template_setting($template_id, 'color_secondary_text');

?>
<style>


    table#items {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 20pt;
    }

    table#items th,
    table#items td {
        text-align: center;
        border-bottom: 1px solid <?php echo $neutralColor; ?>;
        padding: 5pt 10pt;
    }

    table#items th {
        font-weight: normal;
        padding: 10pt 5pt 0;
        text-transform: uppercase;
        font-size: 8pt;
        color: <?php echo $neutralTextColor; ?>
    }

    table#items th small {
        text-transform: lowercase;
    }

    table#items td {
        text-align: right;
        vertical-align: middle;
        margin: 0;
        line-height: 10pt;
        color: <?php echo $textColor; ?>;
    }

    table#items td h3 {
        color: <?php echo $primaryColor; ?>;
        font-size: 12pt;
        font-weight: normal;
        margin: 0;
        line-height: 13pt;
    }

    table#items .no {
        color: <?php echo $primaryTextColor; ?>;
        font-weight: bold;
        background: <?php echo $primaryColor; ?>;
        text-align: center;
    }

    table#items th.no {
        padding-top: 20pt;
    }

    table#items .desc {
        background: <?php echo $backColor; ?>;
        text-align: left;
    }

    table#items .unit {
        background: <?php echo $neutralColor; ?>;
        color: <?php echo $neutralTextColor; ?>;
    }

    table#items .qty {
        background: <?php echo $backColor; ?>;
    }

    table#items .total {
        background: <?php echo $primaryColor; ?>;
        color: <?php echo $primaryTextColor; ?>;
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
        background: <?php echo $backColor; ?>;
        border-bottom: none;
        border-top: 1pt solid <?php echo $neutralColor; ?>;
        width: 50%;
        color: <?php echo $textColor; ?>;
    }

    table#totals tr {
        width: 100%;
    }

    table#totals td {
        text-align: right;
    }

    #notices {
        padding-top: 0;
        padding-left: 20pt;
        border-left: 6pt solid <?php echo $secondaryColor; ?>;
        color: <?php echo $textColor; ?>;
        font-size: 7pt;
        text-align: justify;
    }

</style>
<page backtop="<?php echo $backTop; ?>" backbottom="<?php echo $backBottom; ?>" backleft="<?php echo $backLeft; ?>" backright="<?php echo $backRight; ?>" backcolor="<?php echo $backColor; ?>" orientation="<?php echo $orientation; ?>" backimg="<?php echo $backImg; ?>" backimgx="<?php echo $backImgX; ?>" backimgy="<?php echo $backImgY; ?>" backimgw="<?php echo $backImgW; ?>">

    <table cellpadding="0" cellspacing="0" style="width:100%; margin-bottom: 20pt;">
        <tr>
            <td style="color: <?php echo $textColor; ?>;">
                <?php cuar_the_invoice_header($invoice->ID); ?>
            </td>
        </tr>
    </table>

    <table style="width: 100%; padding: 10pt 0 0; margin-bottom: 20pt;">
        <tr>
            <td style="margin-top: 8pt; width: 50%; text-align: left; padding: 0;">
                <?php $logo_url = cuar_get_the_invoice_emitter_logo($invoice->ID);
                if ( !empty($logo_url)) : ?>
                    <img src="<?php echo esc_attr($logo_url); ?>" style="max-width: 100%; max-height: 70pt; height: auto;"/>
                <?php endif; ?>
            </td>
            <td style="text-align: right; width: 50%; padding: 0 20pt 0 0; margin: 0; border-right: 6pt solid <?php echo $secondaryColor; ?>;">
                <span style="text-transform: uppercase; font-size: 10pt; margin: 0; color: #777777;"><?php _e('From', 'cuarde'); ?></span>
                <p style="font-size: 12pt; color: <?php echo $textColor; ?>;"><?php cuar_print_address(cuar_get_the_invoice_emitter_address($invoice->ID), 'emitter_address', '', 'pdf'); ?></p>
            </td>
        </tr>
    </table>
    <table id="page-main" border="0" cellspacing="0" cellpadding="0" style="width: 100%; margin-top: 20pt;">
        <tr>
            <td>
                <table border="0" cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom: 20pt;">
                    <tr>
                        <td id="client" style="width: 50%; padding-left: 20px; border-left: 6px solid <?php echo $secondaryColor; ?>; padding-top: 0; padding-bottom: 0; text-align:left;">
                            <span style="text-transform: uppercase; font-size: 10pt; margin: 0; color: #777777;"><?php _e('To', 'cuarde'); ?></span>
                            <p style="font-size: 12pt; color: <?php echo $textColor; ?>;"><?php cuar_print_address(cuar_get_the_invoice_billing_address($invoice->ID), 'billing_address', '', 'pdf'); ?></p>
                        </td>
                        <td style="width: 50%; text-align: right;">
                            <table cellspacing="0" cellpadding="0" style="width:100%; color: <?php echo $textColor; ?>;">
                                <tr>
                                    <th style="width:50%;text-transform: uppercase;color: <?php echo $secondaryColor; ?>;font-weight: normal;margin: 0  0 10pt 0;">
                                        <p style="font-size: 16pt;margin-top:0;line-height:13pt;"><?php _e('Invoice', 'cuarde'); ?></p></th>
                                    <td style="width:50%;color: <?php echo $secondaryColor; ?>;font-weight: normal;margin: 0  0 10pt 0;">
                                        <p style="font-size: 16pt; font-weight: bold;margin-top:0;line-height:13pt;"><?php cuar_the_invoice_number($invoice->ID); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width:50%;"><p><?php _e('Created', 'cuarde'); ?></p></th>
                                    <td style="width:50%;"><p><?php cuar_the_invoice_date($invoice->ID); ?></p></td>
                                </tr>
                                <tr>
                                    <th style="width:50%;"><?php _e('Status', 'cuarde'); ?></th>
                                    <td style="width:50%;"><?php cuar_the_invoice_status($invoice->ID); ?></td>
                                </tr>
                                <tr>
                                    <th style="width:50%;"><?php _e('Due date', 'cuarde'); ?></th>
                                    <td style="width:50%;">
                                        <?php if (cuar_get_the_invoice_due_date($invoice->ID) != '') {
                                            cuar_the_invoice_due_date($invoice->ID);
                                        } else {
                                            _e('None', 'cuarde');
                                        } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width:50%;"><?php _e('Amount due', 'cuarde'); ?></th>
                                    <td style="width:50%;"><?php cuar_the_invoice_total($invoice->ID); ?></td>
                                </tr>
                                <?php $mode = cuar_get_the_invoice_payment_mode($invoice->ID);
                                if ( !empty($mode)): ?>
                                    <tr>
                                        <th style="width:50%;"><?php _e('Payment mode', 'cuarde'); ?></th>
                                        <td style="width:50%;"><?php echo $mode; ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table id="items" cellpadding="0" cellspacing="0" style="width:100%; border: 1px solid <?php echo $neutralColor; ?>; margin-top: 20pt;">
        <thead style="width:100%;">
        <tr style="width:100%;">
            <th class="no" style="width: 10%; vertical-align: top;">#</th>
            <th class="desc" style="color: <?php echo $textColor; ?>; border: 1pt solid <?php echo $neutralColor; ?>; width:48%; background: <?php echo $neutralColor; ?>; vertical-align: top;">
                <p><?php _e('Item', 'cuarde'); ?></p></th>
            <th class="qty" style="color: <?php echo $textColor; ?>; border: 1pt solid <?php echo $neutralColor; ?>; width:14%; margin-left: 3mm; text-align: right; background: <?php echo $neutralColor; ?>; vertical-align: top;">
                <p><?php _e('Quantity', 'cuarde'); ?></p></th>
            <th class="unit" style="color: <?php echo $textColor; ?>; border: 1pt solid <?php echo $neutralColor; ?>; width:14%; margin-left: 3mm; text-align: right; background: <?php echo $neutralColor; ?>; vertical-align: top;">
                <p><?php _e('Unit price', 'cuarde'); ?><br>
                    <small><?php _e('(excl. taxes)', 'cuarde'); ?></small>
                </p>
            </th>
            <th class="total" style="color: <?php echo $textColor; ?>; border: 1pt solid <?php echo $neutralColor; ?>; width:14%; margin-left: 3mm; text-align: right; background: <?php echo $neutralColor; ?>; vertical-align: top;">
                <p><?php _e('Total price', 'cuarde'); ?><br>
                    <small><?php _e('(excl. taxes)', 'cuarde'); ?></small>
                </p>
            </th>
        </tr>
        </thead>
        <tbody style="width:100%;">
        <?php
        $c = 0;
        foreach (cuar_get_the_invoice_items($invoice->ID) as $i => $item) : $c++; ?>
            <tr style="width:100%;">
                <td class="no" style="width: 10%;"><?php echo $c; ?></td>
                <td class="desc" style="border: 1pt solid <?php echo $neutralColor; ?>; width:48%;">
                    <h3><?php cuar_the_invoice_item_title($invoice->ID, $item); ?></h3>
                    <?php if ( !empty($item['description'])) : ?>
                        <small style="margin-top: 8pt;"><?php cuar_the_invoice_item_description($invoice->ID, $item); ?></small>
                    <?php endif; ?>
                </td>
                <td class="qty" style="border: 1pt solid <?php echo $neutralColor; ?>; width:14%; text-align: right;">
                    <p><?php cuar_the_invoice_item_quantity($invoice->ID, $item); ?></p></td>
                <td class="unit" style="border: 1pt solid <?php echo $neutralColor; ?>; width:14%; text-align: right;">
                    <p><?php cuar_the_invoice_item_unit_price($invoice->ID, $item); ?></p></td>
                <td class="total" style="border: 1pt solid <?php echo $neutralColor; ?>; width:14%; text-align: right;">
                    <p><?php cuar_the_invoice_item_total_price($invoice->ID, $item); ?></p></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <table cellpadding="0" cellspacing="0" style="width:100%; margin-bottom: 20pt;">
        <tr style="width:100%;">
            <td style="width: 58%;">
                &nbsp;
            </td>
            <td style="width: 42%;">
                <table id="totals" cellspacing="0" cellpadding="0" style="width:100%;">
                    <tr>
                        <th style="border-top: none;"><?php _e('Subtotal (excl. taxes)', 'cuarde'); ?></th>
                        <td style="border-top: none;"><?php cuar_the_invoice_total($invoice->ID, 'items'); ?></td>
                    </tr>
                    <?php if (cuar_is_discount_before_tax($invoice->ID)) : ?>
                        <tr>
                            <th><?php cuar_the_invoice_discount_description($invoice->ID); ?></th>
                            <td><?php cuar_the_invoice_total($invoice->ID, 'discount'); ?></td>
                        </tr>
                        <tr>
                            <th><?php cuar_the_invoice_taxes_description($invoice->ID); ?></th>
                            <td><?php cuar_the_invoice_total($invoice->ID, 'tax'); ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <th><?php cuar_the_invoice_taxes_description($invoice->ID); ?></th>
                            <td><?php cuar_the_invoice_total($invoice->ID, 'tax'); ?></td>
                        </tr>
                        <tr>
                            <th><?php cuar_the_invoice_discount_description($invoice->ID); ?></th>
                            <td><?php cuar_the_invoice_total($invoice->ID, 'discount'); ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr class="cuar-total">
                        <th style="color: <?php echo $primaryColor; ?>; border-top: 1pt solid <?php echo $primaryColor; ?>;">
                            <h3><?php _e('Total', 'cuarde'); ?></h3></th>
                        <td style="color: <?php echo $primaryColor; ?>; border-top: 1pt solid <?php echo $primaryColor; ?>;">
                            <h3><?php cuar_the_invoice_total($invoice->ID); ?></h3></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table cellpadding="0" cellspacing="0" style="width:100%; margin-bottom: 20pt;">
        <tr>
            <td id="notices" style="width: 100%;">
                <h3 style="margin-bottom: 10pt;margin-top: 0;"><?php _e('NOTICE:', 'cuarde'); ?></h3>
                <?php cuar_the_invoice_notice($invoice->ID); ?>
            </td>
        </tr>
    </table>

    <table cellpadding="0" cellspacing="0" style="width:100%; margin-bottom: 20pt;">
        <tr>
            <td style="color: <?php echo $textColor; ?>; width: 100%;">
                <?php cuar_the_invoice_footer($invoice->ID); ?>
            </td>
        </tr>
    </table>
</page>