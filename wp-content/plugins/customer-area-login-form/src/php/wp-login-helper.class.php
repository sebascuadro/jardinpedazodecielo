<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

if ( !class_exists('CUAR_WPLoginHelper')) :

    /**
     * Helper class that takes the functions defined in wp-login.php to facilitate execution of the actions
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_WPLoginHelper
    {
        /**
         * Handles sending password retrieval email to user.
         *
         * @uses $wpdb WordPress Database object
         *
         * @return bool|WP_Error True: when finish. WP_Error on error
         */
        public static function retrieve_password()
        {
            global $wpdb;

            $errors = new WP_Error();
            $user_data = null;

            if (empty($_POST['user_login']))
            {
                $errors->add('empty_username', __('Enter a username or e-mail address.', 'cuarlf'));
            }
            else if (strpos($_POST['user_login'], '@'))
            {
                $user_data = get_user_by('email', trim($_POST['user_login']));
                if (empty($user_data))
                {
                    $errors->add('invalid_email', __('There is no user registered with that email address.', 'cuarlf'));
                }
            }
            else
            {
                $login = trim($_POST['user_login']);
                $user_data = get_user_by('login', $login);
            }

            do_action('lostpassword_post');

            if ($errors->get_error_code())
            {
                return $errors;
            }

            if ( !$user_data)
            {
                $errors->add('invalidcombo', __('Invalid username or e-mail.', 'cuarlf'));

                return $errors;
            }

            // redefining user_login ensures we return the right case in the email
            $user_login = $user_data->user_login;

            do_action('retreive_password', $user_login);  // Misspelled and deprecated
            do_action('retrieve_password', $user_login);

            $allow = apply_filters('allow_password_reset', true, $user_data->ID);

            if ( !$allow)
            {
                return new WP_Error('no_password_reset', __('Password reset is not allowed for this user', 'cuarlf'));
            }
            else if (is_wp_error($allow))
            {
                return $allow;
            }

            $key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
            if (empty($key))
            {
                // Generate something random for a key...
                $key = wp_generate_password(20, false);
                do_action('retrieve_password_key', $user_login, $key);

                // Now insert the new md5 key into the db
                $wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
            }

            // Send notification
            self::forgot_password_notification($user_data, $key);

            return true;
        }

        /**
         * Retrieves a user row based on password reset key and login
         *
         * @uses $wpdb WordPress Database object
         *
         * @param string $key   Hash to validate sending user's password
         * @param string $login The user login
         *
         * @return object|WP_Error User's database row on success, error object for invalid keys
         */
        public static function check_password_reset_key($key, $login)
        {
            global $wpdb;

            if (empty($key) || !is_string($key))
            {
                return new WP_Error('invalid_key', __('Invalid key', 'cuarlf'));
            }

            if (empty($login) || !is_string($login))
            {
                return new WP_Error('invalid_key', __('Invalid key', 'cuarlf'));
            }

            $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_login = %s", $login));

            if (empty($user) || empty($key))
            {
                return new WP_Error('invalid_key', __('Invalid key', 'cuarlf'));
            }

            if (empty($user->user_activation_key) || $user->user_activation_key !== $key )
            {
                return new WP_Error('invalid_key', __('Invalid key', 'cuarlf'));
            }

            return $user;
        }

        /**
         * Handles resetting the user's password.
         *
         * @param WP_User $user     The user
         * @param string  $new_pass New password for the user in plaintext
         */
        public static function reset_password($user, $new_pass)
        {
            do_action('password_reset', $user, $new_pass);

            wp_set_password($new_pass, $user->ID);

            // Send notification
            self::password_change_admin_notification($user);
        }

        /**
         * Handles registering a new user.
         *
         * @param string $user_login User's username for logging in
         * @param string $user_email User's email address to send password and add
         *
         * @return int|WP_Error Either user's ID or error on failure.
         */
        public static function register_new_user($user_login, $user_email)
        {
            global $wpdb;

            $errors = new WP_Error();

            $sanitized_user_login = sanitize_user($user_login);
            $user_email = apply_filters('user_registration_email', $user_email);

            // Check the username
            if ($sanitized_user_login == '')
            {
                $errors->add('empty_username', __('Please enter a username.', 'cuarlf'));
            }
            elseif ( !validate_username($user_login))
            {
                $errors->add('invalid_username', __('Sorry. Please enter a username using only lowercase letters and numbers.', 'cuarlf'));
                $sanitized_user_login = '';
            }
            elseif (username_exists($sanitized_user_login))
            {
                $errors->add('username_exists', __('This username is already registered. Please choose another one.', 'cuarlf'));
            }

            // Check the e-mail address
            if ($user_email == '')
            {
                $errors->add('empty_email', __('Please type your e-mail address.', 'cuarlf'));
            }
            elseif ( !is_email($user_email))
            {
                $errors->add('invalid_email', __('The email address isn&#8217;t correct.', 'cuarlf'));
                $user_email = '';
            }
            elseif (email_exists($user_email))
            {
                $errors->add('email_exists', __('This email is already registered, please choose another one.', 'cuarlf'));
            }

            do_action('register_post', $sanitized_user_login, $user_email, $errors);

            $errors = apply_filters('registration_errors', $errors, $sanitized_user_login, $user_email);

            if ($errors->get_error_code())
            {
                return $errors;
            }

            $user_pass = wp_generate_password(12, false);
            $user_id = wp_create_user($sanitized_user_login, $user_pass, $user_email);
            if ( !$user_id)
            {
                $errors->add('registerfail',
                    sprintf(__('Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'cuarlf'), get_option('admin_email')));

                return $errors;
            }

            update_user_option($user_id, 'default_password_nag', true, true); //Set up the Password change nag.


            // Generate something random for a key...
            $activation_key = wp_generate_password(20, false);
            do_action('retrieve_password_key', $user_login, $activation_key);

            // Now insert the new md5 key into the db
            $wpdb->update($wpdb->users, array('user_activation_key' => $activation_key), array('user_login' => $user_login));

            // Send notifications
            $user = get_userdata($user_id);
            self::new_user_notification_admin($user);
            self::new_user_notification($user, $activation_key);

            return $user_id;
        }

        /**
         * Notify the blog admin of a new user, normally via email.
         *
         * @since 2.0
         *
         * @param WP_User $user User
         */
        public static function new_user_notification_admin($user)
        {
            $user_login = $user->user_login;

            // The blogname option is escaped with esc_html on the way into the database in sanitize_option
            // we want to reverse this for the plain text arena of emails.
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

            $message = sprintf(__('New user registration on your site %s:', 'cuarlf'), $blogname) . "\r\n\r\n";
            $message .= sprintf(__('Username: %s', 'cuarlf'), $user_login) . "\r\n\r\n";
            $message .= sprintf(__('E-mail: %s', 'cuarlf'), $user->user_email) . "\r\n";

            $subject = sprintf(__('[%s] New User Registration', 'cuarlf'), $blogname);

            // Allow custom emails
            $subject = apply_filters('cuar/authentication-forms/email/register-admin/subject', $subject, $user);
            $headers = apply_filters('cuar/authentication-forms/email/register-admin/headers', array(), $user);
            $message = apply_filters('cuar/authentication-forms/email/register-admin/body', $message, $user);

            if ($message !== false)
            {
                @wp_mail(get_option('admin_email'), $subject, $message, $headers);
            }

            do_action('cuar/authentication-forms/email/register-admin', $user);
        }

        /**
         * Notify the blog admin of a new user, normally via email.
         *
         * @since 2.0
         *
         * @param WP_User $user           User
         * @param string  $activation_key User's activation key
         */
        public static function new_user_notification($user, $activation_key)
        {
            $user_login = $user->user_login;

            // The blogname option is escaped with esc_html on the way into the database in sanitize_option
            // we want to reverse this for the plain text arena of emails.
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

            /** @var CUAR_LoginFormAddOn $lf_addon */
            $lf_addon = cuar_addon('login-forms');
            $reset_url = $lf_addon->get_reset_password_url($activation_key, $user_login);

            $message = __('Hello,', 'cuarlf') . "\r\n\r\n";
            $message .= sprintf(__('Thank you for registering on our site. Your username is: %1$s', 'cuarlf'), $user_login) . "\r\n\r\n";
            $message .= __('To set your password, visit the following address:', 'cuarlf') . "\r\n\r\n";
            $message .= '<' . $reset_url . ">\r\n\r\n";

            $subject = sprintf(__('[%1$s] Welcome', 'cuarlf'), $blogname);

            // Allow custom emails
            $subject = apply_filters('cuar/authentication-forms/email/register/subject', $subject, $user);
            $headers = apply_filters('cuar/authentication-forms/email/register/headers', array(), $user);
            $message = apply_filters('cuar/authentication-forms/email/register/body', $message, $user, $reset_url);

            if ($message !== false && !wp_mail($user->user_email, $subject, $message, $headers))
            {
                wp_die(__('The e-mail could not be sent.', 'cuarlf') . "<br />\n"
                    . __('Possible reason: your host may have disabled the mail() function...', 'cuarlf'));
            }

            do_action('cuar/authentication-forms/email/register', $user, $reset_url);
        }

        /**
         * @param WP_User $user
         * @param string  $reset_key
         */
        public static function forgot_password_notification($user, $reset_key)
        {
            /** @var CUAR_LoginFormAddOn $lf_addon */
            $lf_addon = cuar_addon('login-forms');
            $reset_url = $lf_addon->get_reset_password_url($reset_key, $user->user_login);

            // The blogname option is escaped with esc_html on the way into the database in sanitize_option
            // we want to reverse this for the plain text arena of emails.
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

            $message = __('Someone requested that the password be reset for the following account:', 'cuarlf') . "\r\n\r\n";
            $message .= network_home_url('/') . "\r\n\r\n";
            $message .= sprintf(__('Username: %s', 'cuarlf'), $user->user_login) . "\r\n\r\n";
            $message .= __('If this was a mistake, just ignore this email and nothing will happen.', 'cuarlf') . "\r\n\r\n";
            $message .= __('To reset your password, visit the following address:', 'cuarlf') . "\r\n\r\n";
            $message .= '<' . $reset_url . ">\r\n";

            $subject = sprintf(__('[%1$s] Password Reset', 'cuarlf'), $blogname);

            // Allow custom emails
            $subject = apply_filters('cuar/authentication-forms/email/forgot-password/subject', $subject, $user);
            $headers = apply_filters('cuar/authentication-forms/email/forgot-password/headers', array(), $user);
            $message = apply_filters('cuar/authentication-forms/email/forgot-password/body', $message, $user, $reset_url);

            if ($message !== false && !wp_mail($user->user_email, $subject, $message, $headers))
            {
                wp_die(__('The e-mail could not be sent.', 'cuarlf') . "<br />\n"
                    . __('Possible reason: your host may have disabled the mail() function...', 'cuarlf'));
            }

            do_action('cuar/authentication-forms/email/forgot-password', $user, $reset_url);
        }

        /**
         * @param WP_User $user
         */
        public static function password_change_admin_notification($user)
        {
            // send a copy of password change notification to the admin
            // but check to see if it's the admin whose password we're changing, and skip this
            if (0 !== strcasecmp($user->user_email, get_option('admin_email')))
            {
                // The blogname option is escaped with esc_html on the way into the database in sanitize_option
                // we want to reverse this for the plain text arena of emails.
                $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

                $message = sprintf(__('A user changed his password on your site %s: %s', 'cuarlf'), $blogname, network_home_url('/')) . "\r\n\r\n";
                $message .= sprintf(__('Password lost and changed for user: %s', 'cuarlf'), $user->user_login) . "\r\n\r\n";

                $subject = sprintf(__('[%1$s] Password lost and changed', 'cuarlf'), $blogname);

                // Allow custom emails
                $subject = apply_filters('cuar/authentication-forms/email/password-reset-admin/subject', $subject, $user);
                $headers = apply_filters('cuar/authentication-forms/email/password-reset-admin/headers', array(), $user);
                $message = apply_filters('cuar/authentication-forms/email/password-reset-admin/body', $message, $user);


                if ($message !== false && !wp_mail(get_option('admin_email'), $subject, $message, $headers))
                {
                    wp_die(__('The e-mail could not be sent.', 'cuarlf') . "<br />\n"
                        . __('Possible reason: your host may have disabled the mail() function...',
                            'cuarlf'));
                }
            }

            do_action('cuar/authentication-forms/email/password-reset-admin', $user);
        }
    }

endif;