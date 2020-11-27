<?php
/** Template version: 3.0.0
 *
 * -= 3.0.0 =-
 * - Initial version
 *
 */ ?>

<?php /** @var CUAR_Invoice $invoice */ ?>

<?php
$template_id = 'square';

/** @var CUAR_InvoiceSettingsHelper $settings_helper */
$settings_helper = cuar_addon( 'invoicing' )->settings();
?>

<?php

// Page Inner Margins
$backTop    = "0mm";    // value(mm, px, pt, %)
$backBottom = "0mm";    // value(mm, px, pt, %)
$backLeft   = "0mm";    // value(mm, px, pt, %)
$backRight  = "0mm";    // value(mm, px, pt, %)

// Page Style
$orientation = 'P';

// Page background
$backImg  = CUARDE_PLUGIN_URL . "/assets/invoices/bg-3.png";
$backImgX = 'left';
$backImgY = 'top';
$backImgW = '100%';

// Page footer
$footerThanksMessage     = $settings_helper->get_pdf_template_setting( $template_id, 'footer_thanks_message' );
$footerBackground        = $settings_helper->get_pdf_template_setting( $template_id, 'color_footer_bg' );
$footerTextColor         = $settings_helper->get_pdf_template_setting( $template_id, 'color_footer_text' );
$footerThanksMessageSize = $settings_helper->get_pdf_template_setting( $template_id, 'footer_thanks_message_size' );

// Colors
$backColor              = $settings_helper->get_pdf_template_setting( $template_id, 'color_back' );
$backTextColor          = $settings_helper->get_pdf_template_setting( $template_id, 'color_text_columns' );
$textColor              = $settings_helper->get_pdf_template_setting( $template_id, 'color_text' );
$squareColor            = $settings_helper->get_pdf_template_setting( $template_id, 'color_square' );
$squareTextColor        = $settings_helper->get_pdf_template_setting( $template_id, 'color_text_square' );
$itemsColor             = $settings_helper->get_pdf_template_setting( $template_id, 'color_items' );
$secondaryContentsColor = $settings_helper->get_pdf_template_setting( $template_id, 'color_secondary_contents' );

?>
<style>

    .main-table {
        width: 100%;
        margin-top: 30pt;
        color: <?php echo $textColor; ?>;
        font-size: 8pt;
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
        padding: 5pt 20pt;
    }

    table#items th {
        font-weight: bold;
        text-transform: uppercase;
        font-size: 7pt;
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

    table#items td .desc-item-title {
        color: <?php echo $itemsColor; ?>;
        font-size: 11pt;
        font-weight: bold;
        margin: 0;
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

    table#items td.desc small {
        text-align: justify;
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
        padding: 15pt 20pt;
        border-bottom: none;
        width: 50%;
    }

    table#totals tr {
        width: 100%;
    }

    table#totals th {
        text-align: right;
        width: 68%;
    }

    table#totals td {
        width: 32%;
        padding-right: 0;
    }

    table#extras p {
        font-size: 8pt;
        margin: 0;
        line-height: 7pt;
    }

    table#extras th,
    table#totals th,
    .addresses-title,
    .secondary-content-color {
        color: <?php echo $secondaryContentsColor; ?>;
    }

    .text-color-back {
        color: <?php echo $backTextColor; ?>;
    }

    .thin-top-border {
        border-top: 1mm solid <?php echo $backColor; ?>;
    }

    .thin-top-border-back {
        border-top: 1mm solid <?php echo $backTextColor; ?>;
    }

    .square {
        display: inline;
        width: auto;
        padding: 5pt;
        background: <?php echo $squareColor; ?>;
        color: <?php echo $squareTextColor; ?>;
        margin: 0;
    }

    td.square {
        display: table-cell;
        color: <?php echo $squareTextColor; ?>;;
        vertical-align: middle;
        text-align: center;
    }

    .small {
        padding: 2pt 5pt;
    }

</style>
<page backtop="<?php echo $backTop; ?>" backbottom="<?php echo $backBottom; ?>" backleft="<?php echo $backLeft; ?>" backright="<?php echo $backRight; ?>" backcolor="<?php echo $backColor; ?>" orientation="<?php echo $orientation; ?>" backimg="<?php echo $backImg; ?>" backimgx="<?php echo $backImgX; ?>" backimgy="<?php echo $backImgY; ?>" backimgw="<?php echo $backImgW; ?>">


    <table class="main-table" cellpadding="0" cellspacing="0" style="margin-top: 0;">
        <tr>
            <td style="width: 83%;">
                <p style="padding-right: 20pt; text-align: right;">
					<?php $logo_url = cuar_get_the_invoice_emitter_logo( $invoice->ID );
					if ( ! empty( $logo_url ) ) : ?>
                        <img src="<?php echo esc_attr( $logo_url ); ?>" style="max-width: 100%; max-height: 100pt; height: auto;"/>
					<?php else:
						$emitter_company = cuar_get_the_invoice_emitter_company( $invoice->ID );
						$emitter_name    = cuar_get_the_invoice_emitter_name( $invoice->ID );
						if ( ! empty( $emitter_company ) ) :
							echo $emitter_company;
                        elseif ( ! empty( $emitter_name ) ) :
							echo $emitter_name;
						else :
							bloginfo( 'name' );
						endif;
					endif; ?>
                </p>
            </td>
        </tr>
    </table>

    <table id="page-main" class="main-table" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width: 83%;">
                <table border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="width: 58%;">
                            <p class="addresses-title" style="text-transform: uppercase; font-weight: bold; margin: 0;"><?php _e( 'To', 'cuarde' ); ?></p>
                            <p style="font-size: 11pt;"><?php cuar_print_address( cuar_get_the_invoice_billing_address( $invoice->ID ), 'billing_address', '', 'pdf' ); ?></p>

                        </td>
                        <td style="width: 42%; text-align: right;">
                            <p class="addresses-title" style="padding-right: 15pt; text-transform: uppercase; margin: 0;"><?php _e( 'From', 'cuarde' ); ?></p>
                            <p style="padding-right: 15pt; font-size: 11pt;"><?php cuar_print_address( cuar_get_the_invoice_emitter_address( $invoice->ID ), 'emitter_address', '', 'pdf' ); ?></p>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="main-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 83%;">
                <div class="square" style="font-size: 20pt; font-weight: bold; text-transform: uppercase;">
					<?php printf( __( 'Invoice %s', 'cuarde' ), cuar_get_the_invoice_number( $invoice->ID ) ); ?>
                </div>
            </td>
        </tr>
    </table>

	<?php $invoice_header = cuar_get_the_invoice_header( $invoice->ID );
	if ( ! empty( $invoice_header ) ) { ?>
        <table class="main-table secondary-content-color" cellpadding="0" cellspacing="0" style="padding-right: 15pt; text-align: justify;">
            <tr>
                <td style="width: 83%;">
					<?php echo $invoice_header; ?>
                </td>
            </tr>
        </table>
	<?php } ?>

    <table id="items" class="main-table" cellpadding="0" cellspacing="0">
        <thead style="width:100%;">
        <tr style="width:100%;">
            <th class="no" style="width: 3%; vertical-align: top; padding-right: 0;">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="square small">##</td>
                    </tr>
                </table>
            </th>
            <th class="desc" style="width:46%; vertical-align: top;">
				<?php _e( 'Item', 'cuarde' ); ?></th>
            <th class="qty" style="width:14%; vertical-align: top;">
				<?php _e( 'Quantity', 'cuarde' ); ?></th>
            <th class="unit" style="width:18%; vertical-align: top;">
				<?php _e( 'Unit price', 'cuarde' ); ?><br>
                <small><?php _e( '(excl. taxes)', 'cuarde' ); ?></small>
            </th>
            <th class="total text-color-back" style="width:18%; vertical-align: top;">
				<?php _e( 'Total price', 'cuarde' ); ?><br>
                <small><?php _e( '(excl. taxes)', 'cuarde' ); ?></small>
            </th>
        </tr>
        </thead>
        <tbody style="width:100%;">
		<?php
		$c = 0;
		foreach ( cuar_get_the_invoice_items( $invoice->ID ) as $i => $item ) : $c ++; ?>
            <tr style="width:100%; vertical-align: top;">
                <td class="no" style="width: 3%; vertical-align: top; padding-right: 0;">
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="square small">
								<?php printf( "%02d", $c ); ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="desc" style="vertical-align: top; width:46%;">
                    <strong class="desc-item-title" style="display: block;"><?php cuar_the_invoice_item_title( $invoice->ID, $item ); ?></strong>
					<?php if ( ! empty( $item['description'] ) ) : ?>
                        <br>
                        <small style="margin-top: 8pt;"><?php cuar_the_invoice_item_description( $invoice->ID, $item ); ?></small>
					<?php endif; ?>
                </td>
                <td class="qty" style="vertical-align: top; width:14%;">
                    <p><?php cuar_the_invoice_item_quantity( $invoice->ID, $item ); ?></p></td>
                <td class="unit" style="vertical-align: top; width:18%;">
                    <p><?php cuar_the_invoice_item_unit_price( $invoice->ID, $item ); ?></p></td>
                <td class="total text-color-back" style="vertical-align: top; width:18%;">
                    <p><?php cuar_the_invoice_item_total_price( $invoice->ID, $item ); ?></p></td>
            </tr>
		<?php endforeach; ?>
        </tbody>
    </table>

    <table class="main-table" cellpadding="0" cellspacing="0">
        <tr style="width:100%; vertical-align: top;">
            <td>
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="thin-top-border" style="width: 83.5%;"></td>
                        <td class="thin-top-border-back" style="width: 16.5%;"></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="width:100%; vertical-align: top;">
            <td>
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="secondary-content-color" style="width: 8%; vertical-align: top;"></td>
                        <td class="secondary-content-color" style="width: 42%; vertical-align: top;">
							<?php $notice_text = cuar_get_the_invoice_notice( $invoice->ID );
							if ( ! empty( $notice_text ) ) { ?>
                                <div style="padding-right: 20pt; margin-top: 10pt; text-align: justify; font-size: 8pt;">
									<?php echo $notice_text; ?>
                                </div>
							<?php } ?>
                        </td>
                        <td style="width: 49%; vertical-align: top;">
                            <table id="totals" cellspacing="0" cellpadding="0" style="width:100%;">
                                <tr>
                                    <th><?php _e( 'Subtotal (excl. taxes)', 'cuarde' ); ?></th>
                                    <td class="text-color-back"><?php cuar_the_invoice_total( $invoice->ID, 'items' ); ?></td>
                                </tr>
								<?php if ( cuar_is_discount_before_tax( $invoice->ID ) ) : ?>
                                    <tr>
                                        <th><?php cuar_the_invoice_discount_description( $invoice->ID ); ?></th>
                                        <td class="text-color-back"><?php cuar_the_invoice_total( $invoice->ID, 'discount' ); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php cuar_the_invoice_taxes_description( $invoice->ID ); ?></th>
                                        <td class="text-color-back"><?php cuar_the_invoice_total( $invoice->ID, 'tax' ); ?></td>
                                    </tr>
								<?php else: ?>
                                    <tr>
                                        <th><?php cuar_the_invoice_taxes_description( $invoice->ID ); ?></th>
                                        <td class="text-color-back"><?php cuar_the_invoice_total( $invoice->ID, 'tax' ); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php cuar_the_invoice_discount_description( $invoice->ID ); ?></th>
                                        <td class="text-color-back"><?php cuar_the_invoice_total( $invoice->ID, 'discount' ); ?></td>
                                    </tr>
								<?php endif; ?>
                                <tr>
                                    <th style="font-size: 8pt; font-weight: bold; padding-top: 22pt;">
										<?php _e( 'Total', 'cuarde' ); ?>
                                    </th>
                                    <td style="font-size: 8pt; font-weight: bold;">
                                        <div class="square">
											<?php cuar_the_invoice_total( $invoice->ID ); ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

	<?php $invoice_footer = cuar_get_the_invoice_footer( $invoice->ID );
	if ( ! empty( $invoice_footer ) ) { ?>
        <table class="main-table secondary-content-color" cellpadding="0" cellspacing="0" style="padding-right: 15pt; text-align: justify;">
            <tr>
                <td style="width: 8.5%;"></td>
                <td style="width: 73%;">
					<?php echo $invoice_footer; ?>
                </td>
            </tr>
        </table>
	<?php } ?>

    <table id="extras" class="main-table" cellspacing="0" cellpadding="0" style="width: 100%;">
        <tr>
            <td style="width: 8.5%;"></td>
            <td style="width: 73%;">
                <table cellspacing="0" cellpadding="0" style="width: 50%;">
                    <tr>
                        <th style="width:58%; padding: 5pt 0pt;">
                            <div class="square" style="text-transform: uppercase;"><?php _e( 'Created', 'cuarde' ); ?></div>
                        </th>
                        <td style="width:42%;  padding: 5pt 0pt;">
                            <p><?php cuar_the_invoice_date( $invoice->ID ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th style="width:58%; padding: 5pt 0pt;">
                            <div class="square" style="text-transform: uppercase;"><?php _e( 'Status', 'cuarde' ); ?></div>
                        </th>
                        <td style="width:42%; padding: 5pt 0pt;"><p><?php cuar_the_invoice_status( $invoice->ID ); ?></p></td>
                    </tr>
                    <tr>
                        <th style="width:58%; text-transform: uppercase;  padding: 5pt 0pt;">
                            <div class="square"><?php _e( 'Due date', 'cuarde' ); ?></div>
                        </th>
                        <td style="width:42%; padding: 5pt 0pt;"><p>
								<?php if ( cuar_get_the_invoice_due_date( $invoice->ID ) != '' ) {
									cuar_the_invoice_due_date( $invoice->ID );
								} else {
									_e( 'None', 'cuarde' );
								} ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th style="width:58%; text-transform: uppercase; padding: 5pt 0pt;">
                            <div class="square"><?php _e( 'Amount due', 'cuarde' ); ?></div>
                        </th>
                        <td style="width:42%; padding: 5pt 0pt;"><p><?php cuar_the_invoice_total( $invoice->ID ); ?></p></td>
                    </tr>
					<?php $mode = cuar_get_the_invoice_payment_mode( $invoice->ID );
					if ( ! empty( $mode ) ): ?>
                        <tr>
                            <th style="width:58%; text-transform: uppercase; padding: 5pt 0pt;">
                                <div class="square"><?php _e( 'Payment mode', 'cuarde' ); ?></div>
                            </th>
                            <td style="width:42%; padding: 5pt 0pt;"><p><?php echo $mode; ?></p></td>
                        </tr>
					<?php endif; ?>
                </table>
            </td>
        </tr>
    </table>

	<?php if ( ! empty( $footerThanksMessage ) ) { ?>
        <page_footer>
            <table id="footer" cellpadding="0" cellspacing="0" style="vertical-align: bottom; ">
                <tr style="vertical-align: bottom; ">
                    <td style="width: 70%; vertical-align: bottom; padding: 0 20pt 20pt;"></td>
                    <td style="background: <?php echo $footerBackground; ?>; color: <?php echo $footerTextColor; ?>; width: 30%; vertical-align: bottom; text-align: right; padding: 0 20pt 20pt; font-weight: bold; font-size: <?php echo $footerThanksMessageSize; ?>;">
                        <p><?php echo $footerThanksMessage; ?></p>
                    </td>
                </tr>
            </table>
        </page_footer>
	<?php } ?>
</page>