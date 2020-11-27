<?php /** Template version: 2.1.0
 *
 * -= 2.1.0 =-
 * - Replace clearfix CSS classes with cuar-clearfix
 *
 * -= 2.0.0 =-
 * - Improve UI for new master-skin
 *
 */ ?>

<?php
$current_username = isset($_POST['user_login']) ? $_POST['user_login'] : '';
?>

<?php if ($this->should_print_form()) : ?>

    <?php $this->print_form_header(); ?>

    <div class="row cuar-clearfix">
        <div class="col-sm-10 col-md-8 col-sm-offset-1 col-md-offset-2">
            <div class="panel panel-primary panel-border top">
                <div class="panel-heading">
                    <span class="panel-title fs-lg"><i class="fa fa-edit"></i> <?php _e('Lost password?', 'cuarlf'); ?></span>
                </div>

                <div class="panel-body">
                    <div class="form-group mb-sm mt-md">
                        <label for="user_login" class="control-label sr-only"><?php _e('Username or Email', 'cuarlf'); ?></label>
                        <div class="row cuar-clearfix">
                            <div class="col-xs-12">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <input type="text" name="user_login" id="user_login" placeholder="<?php esc_attr_e('Username or Email',
                                        'cuarlf'); ?>" value="<?php echo esc_attr($current_username); ?>" class="form-control"/>
                                    <span class="append-icon right"><i class="field-icon fa fa-asterisk text-muted"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php do_action('lostpassword_form'); ?>
                    <?php do_action('cuar/authentication/print-lostpassword-form'); ?>

                    <?php if (class_exists("ReallySimpleCaptcha")) : ?><?php
                        /** @noinspection PhpUndefinedClassInspection */
                        $captcha_instance = new ReallySimpleCaptcha();
                        $word = $captcha_instance->generate_random_word();
                        $prefix = mt_rand();
                        $captcha_src = untrailingslashit(content_url()) . '/plugins/really-simple-captcha/tmp/' . $captcha_instance->generate_image($prefix,
                                $word);
                        ?>
                        <div class="form-group">
                            <label for="captcha" class="control-label"><?php _e('Spam Check', 'cuarlf'); ?></label>
                            <div class="row cuar-clearfix">
                                <div class="col-sm-12">
                                    <img src="<?php echo $captcha_src; ?>" alt="captcha" class="cuar-captcha"/>
                                </div>
                                <div class="col-sm-12">
                                    <span class="append-icon right"><i class="field-icon fa fa-asterisk text-muted"></i></span>
                                    <input type="text" name="captcha" id="captcha" required="required" class="form-control" placeholder="<?php esc_attr_e("Copy the code shown above in this field.",
                                        'cuarlf'); ?>"/>
                                    <span class="help-block sr-only"><?php _e("Copy the code shown above in this field.", 'cuarlf'); ?></span>

                                    <input id="cuar_captcha_prefix" name="cuar_captcha_prefix" type="hidden" value="<?php echo $prefix; ?>"/>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="panel-footer">
                    <input type="submit" name="cuar_do_register" value="<?php _e("Get New Password", 'cuarlf'); ?>" class="btn btn-primary"/>
                </div>
            </div>
        </div>
    </div>

    <?php $this->print_form_footer(); ?>

<?php endif; ?>	
