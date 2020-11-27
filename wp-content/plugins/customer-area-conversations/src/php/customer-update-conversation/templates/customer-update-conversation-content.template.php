<?php /** Template version: 3.2.0
 *
 * -= 3.2.0 =-
 * - Deactivate key navigation for wizard
 *
 * -= 3.1.0 =-
 * - Hide the owner tab if current user has no permission to select an owner
 *
 * -= 3.0.0 =-
 * - New template using Wizard
 *
 * -= 1.0.0 =-
 * - Initial version
 *
 */

/** @var CUAR_CustomerNewConversationAddOn $this */

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

    <div class="cuar-title cuar-js-wizard-section-title"><?php _e( 'Details', 'cuarme' ); ?></div>
    <div class="cuar-js-wizard-section">
		<?php if ( ! $is_owner_selectable && empty( $default_owners ) ) { ?>
            <div class="alert alert-danger">
				<?php _e( 'You are not allowed to pick a recipient and there are no default recipients for this type of content. Please contact your website administrator!', 'cuarme' ); ?>
            </div>
		<?php } ?>

		<?php
		$this->print_title_field( __( 'Topic', 'cuarme' ) );
		$this->print_content_field( __( 'Message', 'cuarme' ) );

		if ( ! $is_owner_selectable && empty( $default_owners ) ) {
			$this->print_submit_disabled_button( __( 'Done', 'cuarme' ), 0 );
		} elseif ( ! $is_owner_selectable ) {
			$this->print_owner_field( __( 'Recipient', 'cuarme' ) );
			$this->print_submit_button( __( 'Send', 'cuarme' ), 0 );
		} else {
			$this->print_submit_button( __( 'Next Step', 'cuarme' ), 0 );
		}
		?>
    </div>

	<?php if ( $is_owner_selectable ) { ?>
        <div class="cuar-title cuar-js-wizard-section-title"><?php _e( 'Recipient', 'cuarme' ); ?></div>
        <div class="cuar-js-wizard-section">
			<?php
			$this->print_owner_field( __( 'Recipient', 'cuarme' ) );
			$this->print_submit_button( __( 'Update', 'cuarme' ), 1 );
			?>
        </div>
	<?php } ?>

	<?php $this->print_form_footer(); ?>

    <script type="text/javascript">
        <!--
        (function ($) {
            "use strict";
            $(document).ready(function () {

                // Init Form Wizard
                if ($.isFunction($.fn.steps)) {
                    $(".cuar-form.cuar-update-content-form.cuar-customer-update-conversation-form").steps({
                        headerTag: ".cuar-js-wizard-section-title",
                        bodyTag: ".cuar-js-wizard-section",
                        cssClass: "cuar-wizard",
                        enableAllSteps: true,
                        enablePagination: false,
                        startIndex: 0,
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

	<?php $this->print_form_footer(); ?>

<?php endif; ?>	