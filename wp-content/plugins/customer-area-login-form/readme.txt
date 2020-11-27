=== WP Customer Area - Authentication Forms ===

Contributors: 		vprat, marvinlabs
Tags: 				private files,client area,customer area,user files,secure area,crm,project,project management,access control
License: 			Commercial
License URI: 		http://wp-customerarea.com/terms-and-conditions/
Donate link: 		http://www.marvinlabs.com/donate/
Requires at least:  4.7
Tested up to:       5.0.0
Stable tag:         5.1.3

== Changelog ==

= 5.1.3 (2020/01/14) =

* Fix: translated message could not be translated "An email has been sent with the instructions to...."

= 5.1.2 (2018/12/14) =

* Fix: remove use of WP_***_URL constants in favor of functions [#315](https://github.com/marvinlabs/customer-area/issues/315)

= 5.1.1 (2018/09/14) =

* Fix: bug in reset password [#311](https://github.com/marvinlabs/customer-area/issues/311)

= 5.1.0 (2018/04/24) =

* Tweak: Compatibility with our new Terms Of Service add-on
* Tweak: better style for login remember me checkbox
* Fix: redirection of term archive pages [#297](https://github.com/marvinlabs/customer-area/issues/297)

= 5.0.3 (2017/10/11) =

* Fix: switch clearfix CSS class to cuar-clearfix to avoid conflicts

= 5.0.2 (2017/05/15) =

* Fix: compatibility with WP Customer Area 7.2

= 5.0.1 (2016/09/15) =

* Fix: compatibility with WP Customer Area 7.1
* Fix: email field on register page not populated properly

= 5.0.0 (2016/06/07) =

* New: Compatibility with WP Customer Area 7.x
* New: Improved the frontend look and feel
* New: Compatibility with the notifications add-on to customize the emails sent on registration/password reset
* New: Clear text password is not sent in the registration email anymore for better security (like with WordPress 4.3+)

= 4.3.0 (2015/11/26) =

* New: widget to show a login form

= 4.2.0 (2015/09/09) =

* New: Users can now login using either their username or their email address (a setting can disable that and only use username as before)
* New: Hooks to change the form links (see [code snippet](http://wp-customerarea.com/snippet/authentication-forms-change-the-links-below-the-forms/))

= 4.1.0 (2015/05/06) =

* Fix: Compatibility with Peter's login redirect

= 4.0.0 (2015/02/17) =

* New: Support for Customer Area 6
* New: Add-on is changing name (formerly "Login & Register forms")

= 3.1.0 (2014/06/17) =

* New: Compatibility with Customer Area 5

= 3.0.3 (2014/04/17) =

* New: Support for template versioning
* Fix: avoid just dying with an error message if the page is not set properly. Just redirect to the wp login page.

= 3.0.2 (2014/02/20) =

* Fix: protect more the reset password page (no form displayed if no key and login parameters)

= 3.0.1 (2014/02/17) =

* Add support for hook discovery 
* Fix redirect issue occurring sometimes (headers already sent)
* Fix a bug with the password reset action

= 3.0.0 (2014/02/15) =

* Compatibility with Customer Area 4
* Fixes bugs in the licensing system
* Uses separate pages to handle each form. This makes customizing titles and pages very easy.

= 2.2.2 (2013/12/17) =

* Added Italian translation

= 2.2.1 (2013/11/27) =

* Fixed login form not shown when not logged-in and accessing a direct link to another page than the dashboard 

= 2.2.0 (2013/10/23) =

* Added the possibility to override WordPress' form URLs for login, registration and password recovery
* Fixed forgot password URL in the error message when the password is not correct

= 2.1.3 (2013/10/22) =

* Fixed a compatibility issue with some plugins using the 'wp_login' action (e.g. WP eMember)

= 2.1.2 (2013/10/21) =

* Fixed forgot password URL in the error message when the login is not correct
* Fixed a fatal error preventing the user from resetting the password

= 2.1.1 (2013/10/18) =

* German translation by [Benjamin Oechsler](http://benlocal.de)

= 2.1.0 (2013/10/03) =

* Added support for most plugins dealing with login and registration (captcha, extra fields, ...)

= 2.0.3 (2013/08/22) =

* Added filters to change the form title and message: cuarlf_login_form_title_{{action}} and cuarlf_login_form_message_{{action}}
where {{action}} is one of: register, login, forgot or reset.

= 2.0.2 (2013/06/26) =

* Added an information message for automatic updates licensing

= 2.0.1 (2013/05/31) =

* Compatibility with Customer Area 2.0.0

= 1.1.0 (2013/05/17) =

* Fixed automatic updates

= 1.0.0 (2013/05/06) =

* First add-on release
* User registration
* Password recovery
* Login
* Password reset
