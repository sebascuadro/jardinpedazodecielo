<?php /** Template version: 3.3.0
 *
 * -= 3.3.0 =-
 * - Deactivate key navigation for wizard
 *
 * -= 3.2.0 =-
 * - Hide the owner tab if current user has no permission to select an owner
 *
 * -= 3.1.0 =-
 * - Replace clearfix CSS classes with cuar-clearfix
 *
 * -=3.0.0=-
 * - Added support for the new master-skin
 * - Added wizard
 *
 * -=1.1.0=-
 * - Added support for the new file manager
 *
 */

/** @var CUAR_CustomerNewFileAddOn $this */

// DATAS
// ---------------------------------------------------------------------------------------------------------------------
// Get all the wizard steps | Array
$steps = $this->get_wizard_steps();

// Has the current user permission to select an owner ? | Bool
$is_owner_selectable = $this->current_user_can_select_owner();

// Get the defaults owners | Array
$default_owners = $this->get_default_owners();

// Get the current post ID | Int
$current_post_id = $this->get_current_post_id();
?>

<?php if ( $this->should_print_form() ) : ?>

	<?php $this->print_form_header(); ?>

    <div class="cuar-title cuar-js-wizard-section-title"><?php _e( 'Details', 'cuarco' ); ?></div>
    <div class="cuar-js-wizard-section">
		<?php if ( ! $is_owner_selectable && empty( $default_owners ) ) { ?>
            <div class="alert alert-danger">
				<?php _e( 'You are not allowed to pick an owner and there are no default owners for this type of content. Please contact your website administrator!', 'cuarco' ); ?>
            </div>
		<?php } ?>

		<?php
		$this->print_title_field( __( 'Title', 'cuarco' ) );
		$this->print_content_field( __( 'Content', 'cuarco' ) );
		$this->print_category_field( __( 'Category', 'cuarco' ) );

		if ( ! $is_owner_selectable ) {
			$this->print_owner_field( __( 'Owner', 'cuarco' ) );
			$label = __( 'Save and add attachments', 'cuarco' );
			$this->print_submit_button( $label, 0 );
		} else {
			$label = $this->get_current_wizard_step() !== 1 ? __( 'Next step', 'cuarco' ) : __( 'Done', 'cuarco' );
			$this->print_submit_button( $label, 0 );
		}
		?>
    </div>

	<?php if ( $is_owner_selectable ) { ?>
        <div class="cuar-title cuar-js-wizard-section-title"><?php _e( 'Owner', 'cuarco' ); ?></div>
        <div class="cuar-js-wizard-section">
			<?php
			$this->print_owner_field( __( 'Owner', 'cuarco' ) );
			/** @noinspection TernaryOperatorSimplifyInspection */
			$label = $this->get_current_wizard_step() !== 1 ? __( 'Save and add attachments', 'cuarco' ) : __( 'Done', 'cuarco' );
			$this->print_submit_button( $label, 1 );
			?>
        </div>
	<?php } ?>

    <div class="cuar-title cuar-js-wizard-section-title"><?php _e( 'Attachments', 'cuarco' ); ?></div>
    <div class="cuar-js-wizard-section">
		<?php if ( $current_post_id > 0 && $this->get_current_wizard_step() === 1 ) : ?>
            <div class="row cuar-clearfix">
                <div class="col-sm-5">
					<?php $this->print_add_attachment_method_browser( $current_post_id ); ?>
                </div>
                <div class="col-sm-7">
					<?php
					$this->print_current_attachments_manager( $current_post_id );
					$this->print_attachment_manager_scripts();
					?>
                </div>
            </div>
			<?php $this->print_submit_button( __( 'Done', 'cuarco' ), 2 ); ?>

		<?php else : ?>

            <div class="alert alert-default pastel">
				<?php _e( 'You must save the post before you can upload attachments', 'cuarco' ); ?>
            </div>

			<?php $this->print_submit_button( __( 'Save and add attachments', 'cuarco' ), 2 ); ?>

		<?php endif; ?>
    </div>

	<?php $this->print_form_footer(); ?>

	<?php
	$startIndex = 0;
	if ( $current_post_id > 0 && $this->get_current_wizard_step() === 1 ) {
		$startIndex = $is_owner_selectable ? 2 : 1;
	}
	?>
    <script type="text/javascript">
        <!--
        (function ($) {
            "use strict";
            $(document).ready(function ($) {

                // Init Form Wizard
                if ($.isFunction($.fn.steps)) {
                    $(".cuar-form.cuar-create-content-form.cuar-customer-new-private-file-form").steps({
                        headerTag: ".cuar-js-wizard-section-title",
                        bodyTag: ".cuar-js-wizard-section",
                        cssClass: "cuar-wizard",
                        enableAllSteps: false,
                        enablePagination: false,
                        startIndex: <?php echo $startIndex; ?>,
                        enableKeyNavigation: false,
                        onInit: function () {
                            $('#cuar-js-content-container').trigger('cuar:wizard:initialized');
                        }

                    });

					<?php if($is_owner_selectable) : ?>
                    // Bind next buttons
                    $("#steps-uid-0-p-0").on('click', 'input[type=submit]', function (e) {
                        e.preventDefault();
                        $("#steps-uid-0").steps("next");
                    });
					<?php endif; ?>
                }

            });
        })(jQuery);
        //-->
    </script>

<?php endif; ?>