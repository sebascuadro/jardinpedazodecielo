<?php /** Template version: 1.1.0
 *
 * -= 1.1.0 =-
 * - Replace clearfix CSS classes with cuar-clearfix
 *
 * -= 1.0.0 =-
 * - Initial version
 *
 */ ?>

<?php if ( $this->should_print_form() ) : ?>

<?php $this->print_form_header(); ?>

	<div class="row cuar-clearfix">
		<div class="col-sm-10 col-md-8 col-sm-offset-1 col-md-offset-2">
			<div class="panel panel-primary panel-border top">
				<div class="panel-heading">
					<span class="panel-title fs-lg"><i class="fa fa-edit"></i> <?php _e('Reset your password', 'cuarlf'); ?></span>
				</div>

				<div class="panel-body">
					<div class="form-group mb-sm mt-md">
						<label for="new-pass" class="control-label sr-only"><?php _e( 'New Password', 'cuarlf' ); ?></label>
						<div class="row cuar-clearfix">
							<div class="col-xs-12">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-lock"></i></span>
									<input type="password" name="new-pass" id="new-pass" class="form-control"  placeholder="<?php esc_attr_e( "New password", 'cuarlf' );?>"/>
									<span class="append-icon right"><i class="field-icon fa fa-asterisk text-muted"></i></span>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group mb-sm mt-md">
						<label for="new-pass-confirm" class="control-label sr-only">&nbsp;</label>
						<div class="row cuar-clearfix">
							<div class="col-xs-12">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-repeat"></i></span>
									<input type="password" name="new-pass-confirm" id="new-pass-confirm" class="form-control" placeholder="<?php esc_attr_e( "Type your new password again", 'cuarlf' );?>"/>
									<span class="append-icon right"><i class="field-icon fa fa-asterisk text-muted"></i></span>
								</div>
							</div>
						</div>
					</div>

					<?php do_action('cuar/authentication/print-resetpassword-form'); ?>
				</div>

				<div class="panel-footer">
					<input type="submit" name="cuar_do_register" value="<?php _e("Change Password", 'cuarlf'); ?>" class="btn btn-primary"/>
				</div>
			</div>
		</div>
	</div>

<?php $this->print_form_footer(); ?>
	
<?php endif; ?>	