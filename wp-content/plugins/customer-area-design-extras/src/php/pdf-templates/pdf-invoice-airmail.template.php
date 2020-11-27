<?php
/** Template version: 3.0.0
 *
 * -= 3.0.0 =-
 * - Initial version
 *
 */ ?>

<?php /** @var CUAR_Invoice $invoice */ ?>

<?php
$template_id = 'airmail';

/** @var CUAR_InvoiceSettingsHelper $settings_helper */
$settings_helper = cuar_addon('invoicing')->settings();
?>

<?php

// Page Inner Margins
$backTop = "50pt";    // value(mm, px, pt, %)
$backBottom = "30pt";    // value(mm, px, pt, %)
$backLeft = "0mm";    // value(mm, px, pt, %)
$backRight = "0mm";    // value(mm, px, pt, %)

// Page Style
$orientation = $settings_helper->get_pdf_template_setting($template_id, 'page_orientation');

// Page background
$backImg = $settings_helper->get_pdf_template_setting($template_id, 'back_img');
$backColor = $settings_helper->get_pdf_template_setting($template_id, 'color_back');

// Colors
$textColor = $settings_helper->get_pdf_template_setting($template_id, 'color_text');
$neutralColor = $settings_helper->get_pdf_template_setting($template_id, 'color_neutral');
$neutralTextColor = $settings_helper->get_pdf_template_setting($template_id, 'color_neutral_text');
$primaryColor = $settings_helper->get_pdf_template_setting($template_id, 'color_primary');
$secondaryColor = $settings_helper->get_pdf_template_setting($template_id, 'color_secondary');
$secondaryTextColor = $settings_helper->get_pdf_template_setting($template_id, 'color_secondary_text');

?>

<style>
    .side-borders {
        width: 100%;
        border-left: 1pt solid <?php echo $neutralColor; ?>;
        border-right: 1pt solid <?php echo $neutralColor; ?>;
        padding: 0 20pt;
    }

    .side-borders.padded {
        padding: 10pt 20pt;
    }

    .global-table {
        width: 100%;
        color: <?php echo $textColor; ?>;
        background: <?php echo $backColor; ?>;
    }
</style>

<page backtop="<?php echo $backTop; ?>" backbottom="<?php echo $backBottom; ?>" backleft="<?php echo $backLeft; ?>" backright="<?php echo $backRight; ?>" backcolor="#FFFFFF" orientation="<?php echo $orientation; ?>" backimg="" backimgx="" backimgy="" backimgw="">

    <page_header style="width:100%;">
        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background: <?php echo $backColor; ?>;">
            <tr>
                <td align="left" style="background:repeat-x url(<?php echo $backImg; ?>) <?php echo $backColor; ?>; padding: 30pt 10pt 20pt; width: 100%; border: 1pt solid <?php echo $neutralColor; ?>; border-bottom: 0;"></td>
            </tr>
        </table>
    </page_header>

    <table class="global-table" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td class="side-borders padded">
                <?php cuar_the_invoice_header($invoice->ID); ?>
            </td>
        </tr>
    </table>

    <table class="global-table" cellpadding="0" cellspacing="0" border="0">
        <tbody>
        <tr>
            <td class="side-borders padded" style="width: 100%;">
                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; padding-bottom: 10pt;">
                    <tbody>
                    <tr>
                        <td align="left" style="width: 37%; vertical-align: top;">
                            <table align="left" cellpadding="0" cellspacing="0" border="0" style="width: 100%; vertical-align: top;">
                                <tbody>
                                <tr>
                                    <td align="left" style="width: 100%; padding: 0 10pt 0 0; vertical-align: top;">
                                        <h6 style="color: <?php echo $primaryColor; ?>;"><?php _e('From', 'cuarde'); ?></h6>
                                        <hr style="border: 0; background: <?php echo $secondaryColor; ?>;"/>
                                        <p style="font-size: 10pt; line-height: 15pt;"><?php cuar_print_address(cuar_get_the_invoice_emitter_address($invoice->ID), 'emitter_address', '', 'pdf'); ?></p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                        <td align="center" style="width: 26%; vertical-align: top;">
                            <table align="center" cellpadding="0" cellspacing="0" style="width: 100%;">
                                <tbody>
                                <tr>
                                    <td align="center" style="vertical-align: top; width: 100%;">
                                        <h6 style="color: <?php echo $primaryColor; ?>; text-transform: uppercase;">
                                            <?php _e('Invoice', 'cuarde'); ?>
                                            <strong><?php cuar_the_invoice_number($invoice->ID); ?></strong>
                                        </h6>
                                        <hr style="border: 0; background: <?php echo $secondaryColor; ?>;"/>
                                        <table align="center" cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tbody>
                                            <tr>
                                                <td align="center" style="padding: 10pt 10pt 0 10pt; vertical-align: top; width: 100%;">

                                                    <?php $logo_url = cuar_get_the_invoice_emitter_logo($invoice->ID);
                                                    if ( !empty($logo_url)) : ?>
                                                        <img src="<?php echo esc_attr($logo_url); ?>" style="max-width: 100%; max-height: 70pt; height: auto;"/>
                                                    <?php endif; ?>

                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                        <td align="left" style="width: 37%; vertical-align: top;">
                            <table align="left" cellpadding="0" cellspacing="0" border="0" style="width: 100%; vertical-align: top;">
                                <tbody>
                                <tr>
                                    <td align="right" style="width: 100%; padding: 0 0 0 10pt; vertical-align: top;">
                                        <h6 style="color: <?php echo $primaryColor; ?>;"><?php _e('To', 'cuarde'); ?></h6>
                                        <hr style="border: 0; background: <?php echo $secondaryColor; ?>;"/>
                                        <p style="font-size: 10pt; line-height: 15pt;"><?php cuar_print_address(cuar_get_the_invoice_billing_address($invoice->ID), 'billing_address', '', 'pdf'); ?></p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <table class="global-table" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td class="side-borders">
                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; color: <?php echo $neutralTextColor; ?>;">
                    <tr>
                        <th style="color: <?php echo $neutralTextColor; ?>; border: 1pt solid <?php echo $neutralColor; ?>; width:50%; background: <?php echo $neutralColor; ?>; vertical-align: top; padding: 5pt;">
                            <p><?php _e('Item', 'cuarde'); ?></p></th>
                        <th style="color: <?php echo $neutralTextColor; ?>; border: 1pt solid <?php echo $neutralColor; ?>; width:16%; margin-left: 3mm; text-align: right; background: <?php echo $neutralColor; ?>; vertical-align: top; padding: 5pt;">
                            <p><?php _e('Quantity', 'cuarde'); ?></p></th>
                        <th style="color: <?php echo $neutralTextColor; ?>; border: 1pt solid <?php echo $neutralColor; ?>; width:17%; margin-left: 3mm; text-align: right; background: <?php echo $neutralColor; ?>; vertical-align: top; padding: 5pt;">
                            <p><?php _e('Unit price', 'cuarde'); ?><br>
                                <small><?php _e('(excl. taxes)', 'cuarde'); ?></small>
                            </p>
                        </th>
                        <th style="color: <?php echo $neutralTextColor; ?>; border: 1pt solid <?php echo $neutralColor; ?>; width:17%; margin-left: 3mm; text-align: right; background: <?php echo $neutralColor; ?>; vertical-align: top; padding: 5pt;">
                            <p><?php _e('Total price', 'cuarde'); ?><br>
                                <small><?php _e('(excl. taxes)', 'cuarde'); ?></small>
                            </p>
                        </th>
                    </tr>
                </table>
            </td>
        </tr>
        <?php foreach (cuar_get_the_invoice_items($invoice->ID) as $i => $item) : ?>
            <tr>
                <td class="side-borders">
                    <table cellpadding="0" cellspacing="0" border="0" style="margin-top: -2pt; width: 100%;">
                        <tr>
                            <td style="border: 1pt solid <?php echo $neutralColor; ?>; width:50%; padding: 0 5pt 5pt;">
                                <p>
                                    <?php cuar_the_invoice_item_title($invoice->ID, $item); ?>
                                    <?php if ( !empty($item['description'])) : ?>
                                        <br>
                                        <small><?php cuar_the_invoice_item_description($invoice->ID, $item); ?></small>
                                    <?php endif; ?>
                                </p>
                            </td>
                            <td style="border: 1pt solid <?php echo $neutralColor; ?>; width:16%; margin-left: 3mm; text-align: right; padding: 0 5pt 5pt;">
                                <p><?php cuar_the_invoice_item_quantity($invoice->ID, $item); ?></p></td>
                            <td style="border: 1pt solid <?php echo $neutralColor; ?>; width:17%; margin-left: 3mm; text-align: right; padding: 0 5pt 5pt;">
                                <p><?php cuar_the_invoice_item_unit_price($invoice->ID, $item); ?></p></td>
                            <td style="border: 1pt solid <?php echo $neutralColor; ?>; width:17%; margin-left: 3mm; text-align: right; padding: 0 5pt 5pt;">
                                <p><?php cuar_the_invoice_item_total_price($invoice->ID, $item); ?></p></td>
                        </tr>
                    </table>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td class="side-borders">
                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                    <tr>
                        <td colspan="4" style="width: 100%; height: 6pt; background: <?php echo $neutralColor; ?>;"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="global-table" cellspacing="0" cellpadding="0" style="margin-bottom: 10mm; margin-top: -2pt;">
        <tr>
            <td class="side-borders">
                <table cellspacing="0" cellpadding="0" style="width:100%;">
                    <tr>
                        <td style="width:50%;">
                            <table cellspacing="0" cellpadding="0" style="width:100%;">
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
                        <td style="width:50%;">
                            <table cellspacing="0" cellpadding="0" style="width:100%; border: 1px solid <?php echo $neutralColor; ?>; border-collapse: collapse;">
                                <tr>
                                    <th style="width:50%; text-align: right; color: <?php echo $neutralTextColor; ?>; border: 1pt solid <?php echo $neutralColor; ?>; background: <?php echo $neutralColor; ?>; padding: 5pt;"><?php _e('Subtotal (excl. taxes)', 'cuarde'); ?></th>
                                    <td style="width:50%; text-align: right; border: 1pt solid <?php echo $neutralColor; ?>; padding: 5pt;"><?php cuar_the_invoice_total($invoice->ID, 'items'); ?></td>
                                </tr>
                                <?php if (cuar_is_discount_before_tax($invoice->ID)) : ?>
                                    <tr>
                                        <th style="width:50%; text-align: right; color: <?php echo $neutralTextColor; ?>; border: 1pt solid <?php echo $neutralColor; ?>; background: <?php echo $neutralColor; ?>; padding: 5pt;"><?php cuar_the_invoice_discount_description($invoice->ID); ?></th>
                                        <td style="width:50%; text-align: right; border: 1pt solid <?php echo $neutralColor; ?>; padding: 5pt;"><?php cuar_the_invoice_total($invoice->ID, 'discount'); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="width:50%; text-align: right; color: <?php echo $neutralTextColor; ?>; border: 1pt solid <?php echo $neutralColor; ?>; background: <?php echo $neutralColor; ?>; padding: 5pt;"><?php cuar_the_invoice_taxes_description($invoice->ID); ?></th>
                                        <td style="width:50%; text-align: right; border: 1pt solid <?php echo $neutralColor; ?>; padding: 5pt;"><?php cuar_the_invoice_total($invoice->ID, 'tax'); ?></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <th style="width:50%; text-align: right; color: <?php echo $neutralTextColor; ?>; border: 1pt solid <?php echo $neutralColor; ?>; background: <?php echo $neutralColor; ?>; padding: 5pt;"><?php cuar_the_invoice_taxes_description($invoice->ID); ?></th>
                                        <td style="width:50%; text-align: right; border: 1pt solid <?php echo $neutralColor; ?>; padding: 5pt;"><?php cuar_the_invoice_total($invoice->ID, 'tax'); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="width:50%; text-align: right; color: <?php echo $neutralTextColor; ?>; border: 1pt solid <?php echo $neutralColor; ?>; background: <?php echo $neutralColor; ?>; padding: 5pt;"><?php cuar_the_invoice_discount_description($invoice->ID); ?></th>
                                        <td style="width:50%; text-align: right; border: 1pt solid <?php echo $neutralColor; ?>; padding: 5pt;"><?php cuar_the_invoice_total($invoice->ID, 'discount'); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr class="cuar-total">
                                    <th style="width:50%; text-align: right; color: <?php echo $primaryColor; ?>; border: 1pt solid <?php echo $neutralColor; ?>; background: <?php echo $neutralColor; ?>; padding: 5pt;">
                                        <h3><?php _e('Total', 'cuarde'); ?></h3></th>
                                    <td style="width:50%; text-align: right; border: 1pt solid <?php echo $neutralColor; ?>; padding: 5pt; color: <?php echo $secondaryTextColor; ?>; background: <?php echo $secondaryColor; ?>;">
                                        <h3><?php cuar_the_invoice_total($invoice->ID); ?></h3></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="background:repeat-x url(<?php echo $backImg; ?>;) <?php echo $backColor; ?>;; width: 100%; height: 3pt; "></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="global-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="side-borders padded">
                <?php cuar_the_invoice_notice($invoice->ID); ?>
            </td>
        </tr>
    </table>

    <table class="global-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="side-borders padded" style="padding-bottom: 30pt; border-bottom: 1pt solid <?php echo $neutralColor; ?>;">
                <?php cuar_the_invoice_footer($invoice->ID); ?>
            </td>
        </tr>
    </table>

</page>