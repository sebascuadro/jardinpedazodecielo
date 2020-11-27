<?php /** Template version: 2.1.0
 *
 * -= 2.1.0 =-
 * - Replace clearfix CSS classes with cuar-clearfix
 *
 * -= 2.0.0 =-
 * - Improve UI for new master-skin
 *
 * -= 1.1.0 =-
 * - Change label if we allow login in with email
 */ ?>

<?php
/** @var CUAR_LoginFormAddOn $lf_addon */
$lf_addon = cuar_addon('login-forms');
$is_email_login_enabled = $lf_addon->is_email_login_enabled();

$current_username = isset($_POST['username']) ? $_POST['username'] : '';
$username_label = $is_email_login_enabled ? __('Email or username', 'cuarlf') : __('Username', 'cuarlf');
?>

<?php /** @var CUAR_CustomerLoginAddOn $this */ ?>

<?php if ($this->should_print_form()) : ?>

    <?php $this->print_form_header(); ?>

    <div class="row cuar-clearfix">
        <div class="col-sm-10 col-md-8 col-sm-offset-1 col-md-offset-2">
            <div class="panel panel-primary panel-border top">
                <div class="panel-heading">
                    <span class="panel-title fs-lg"><i class="fa fa-sign-in"></i> <?php _e('Login', 'cuarlf'); ?></span>
                </div>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="username" class="sr-only"><?php echo $username_label; ?></label>
                        <div class="input-group mb-sm mt-md">
                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                            <input class="form-control" type="text" name="username" id="username" placeholder="<?php echo esc_attr($username_label); ?>" value="<?php echo esc_attr($current_username); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pwd" class="sr-only"><?php _e('Password', 'cuarlf'); ?></label>
                        <div class="input-group mb-lg">
                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                            <input class="form-control" type="password" name="pwd" id="pwd" placeholder="<?php esc_attr_e('Password', 'cuarlf'); ?>" value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox checkbox-custom mb-md">
                            <input type="checkbox" id="remember_me" name="remember" value="forever">
                            <label for="remember_me">
                                <?php _e('Remember me', 'cuarlf'); ?>
                            </label>
                        </div>
                    </div>

                    <?php do_action('login_form'); ?>
                    <?php do_action('cuar/authentication/print-login-form'); ?>
                </div>

                <div class="panel-footer">
                    <input type="submit" name="cuar_do_login" value="<?php _e("Login", 'cuarlf'); ?>" class="btn btn-primary"/>
                </div>
            </div>
        </div>
    </div>

    <?php $this->print_form_footer(); ?>

<?php endif; ?>	