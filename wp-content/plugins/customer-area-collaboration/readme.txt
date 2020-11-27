=== WP Customer Area - Front-office publishing ===

Contributors: 		vprat, marvinlabs
Tags: 				private files,client area,customer area,user files,secure area,crm,project,project management,access control
License: 			Commercial
License URI: 		http://wp-customerarea.com/terms-and-conditions/
Donate link: 		http://www.marvinlabs.com/donate/
Requires at least:	4.7
Tested up to:		5.4.2
Stable tag: 		4.2.0

== Changelog ==

= 4.2.0 (2020/06/18) =

* New: added new links on single private content, into the carousel (added dropdown-menus) to let you quickly assign new contents to the selected owner
* New: added new ability to pre-fill owner forms by passing owners'data in URLs
* Fix: also handle default owner when editing from front-office

= 4.1.2 (2020/01/14) =

* New: Frontend Summernote editor - Disabled auto paragraph generation when pressing enter
* New: Frontend Summernote editor - Shortcodes can now be used
* New: Frontend Summernote editor - New compatibility with Embed shortcode, that allows to embed content from other sites
* Fix: Disable key navigation for wizard steps (was conflicting with moving caret using arrow keys)

= 4.1.1 (2018/12/14) =

* Tweak: update translations

= 4.1.0 (2018/04/24) =

* Updated french translations

= 4.0.4 (2018/01/09) =

* Tweak: hide the owner tab while creating content from front-office if user role is allowed to select one

= 4.0.3 (2017/10/11) =

* Fix: switch clearfix CSS class to cuar-clearfix to avoid conflicts

= 4.0.2 (2016/09/15) =

* Fix: compatibility with WP Customer Area 7.1

= 4.0.1 (2016/06/21) =

* Fix: ACF fields where showing up on all wizard panels

= 4.0.0 (2016/06/07) =

* New: Compatibility with WP Customer Area 7.x
* New: Improved the frontend look and feel
* Fix: Hooks for each post type (cuar/private-content/collaboration/on-post-created?...) have been simplified to a single hook (cuar/private-content/collaboration/on-post-created)

= 3.1.1 (2015/11/26) =

* Fix: Fix form validation not correct when required fields are changed from default

= 3.1.0 (2015/09/09) =

* New: Support new ajax file upload system from WP Customer Area 6.2+

= 3.0.1 (2015/05/06) =

* Fix: page created message was displayed twice

= 3.0.0 (2015/02/17) =

* New: Support for Customer Area 6
* New: Add-on is changing name (formerly "Collaboration")

= 2.2.0 (2014/06/17) =

* New: compatibility with Customer Area 5
* New: You can now edit and delete private content from the frontend
* Fix: comments where disabled after creating the content (whereas they are enabled if created from the admin panel). Now comments are enabled (can be changed with a filter though).

= 2.1.0 (2014/04/17) =

* New: compatibility with the new Advanced Custom Fields Integration add-on

= 2.0.1 (2014/02/17) =

* Add support for hook discovery 

= 2.0.0 (2014/02/15) =

* Compatibility with Customer Area 4
* Fixed: page categories was not being saved.

= 1.6.0 (2014/01/23) =

* Add a capability to activate/deactive the rich editor for some users
* Make owner selection more user-friendly (see Customer Area 3.9.0 changes). If you have overidden any template file, please look for changes in there!
* Changed the form markup and the corresponding CSS to be mobile-friendly. Should improve compatibility with themes. If you have overidden any template file, please look for changes in there! 
* Load frontend scripts only when showing the Customer Area page (not on the other pages of the website)

= 1.5.2 (2014/01/07) =

* Better error handling when uploading forbidden file types and files which exceed the size limit

= 1.5.1 (2013/12/03) =

* Allow selecting a category when creating pages (Requires Customer Area 3.8.0+)
* Adjusted styles in admin for WordPress 3.8

= 1.4.0 (2013/11/27) =

* Allow selecting a category when creating files (Requires Customer Area 3.7.3+)
* Updated templates

= 1.3.1 (2013/10/27) =

* Fixed content displayed after successful create content action (was always dashboard, now depends on content type created)

= 1.3.0 (2013/10/24) =

* Added possibility to show the private content authored by the current user in the frontend

= 1.2.0 (2013/10/18) =

* Support for two new add-ons: Managed Groups and Messenger 
* Compatibility with the new customer area layout (Customer Area 3.x required)

= 1.0.3 (2013/08/21) =

* Fix a warning on empty array

= 1.0.2 (2013/08/02) =

* Fixed a couple of bugs linked to the new ownership system

= 1.0.1 (2013/08/01) =

* Compatibility with Extended Permissions 2.1.0 and Customer Area 2.3.0

= 1.0.0 (2013/06/26) =

* First add-on release
* Create a file from the customer area page
* Create a page from the customer area page
* Moderate created content
* Fine grain permission control on all actions available from the front-end
* Compatible with the extended permissions add-on
* Compatible with the notifications add-on
