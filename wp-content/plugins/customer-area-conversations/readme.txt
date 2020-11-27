=== WP Customer Area - Conversations ===

Contributors: 		vprat, marvinlabs
Tags: 				private files,client area,customer area,user files,secure area,crm,project,project management,access control
License: 			Commercial
License URI: 		http://wp-customerarea.com/terms-and-conditions/
Donate link: 	    http://www.marvinlabs.com/donate/
Requires at least:  4.7
Tested up to:	    5.0.0
Stable tag: 		4.3.4

== Changelog ==

= 4.3.4 (2020/01/14) =

* Fix: Disable key navigation for wizard steps (was conflicting with moving caret using arrow keys)

= 4.3.3 (2019/10/02) =

* Fix: Syntax error

= 4.3.2 (2018/12/14) =

* Fix: remove use of WP_***_URL constants in favor of functions [#315](https://github.com/marvinlabs/customer-area/issues/315)

= 4.3.1 (2018/05/07) =

* Fix: Updated conversation-editor-replies-add-form template to allow image AJAX posting in replies

= 4.3.0 (2018/04/24) =

* Fix: redirection of term archive pages [#297](https://github.com/marvinlabs/customer-area/issues/297)

= 4.2.1 (2018/01/09) =

* Tweak: hide the owner tab while creating content from front-office if user role is allowed to select one

= 4.2.0 (2017/10/11) =

* New: the conversation author can decide to close the conversation to new replies
* Fix: switch clearfix CSS class to cuar-clearfix to avoid conflicts

= 4.1.1 (2017/06/29) =

* Tweak: improve longs owner names rendering

= 4.1.0 (2017/05/15) =

* New: compatibility with the new unread documents add-on

= 4.0.3 (2016/09/15) =

* Fix: compatibility with WP Customer Area 7.1
* Fix: notifications not sent when a conversation was started

= 4.0.2 (2016/06/30) =

* Fix: dates and calendars where sometimes not properly internationalized

= 4.0.1 (2016/06/21) =

* Fix: ACF fields where showing up on all wizard panels

= 4.0.0 (2016/06/07) =

* New: Compatibility with WP Customer Area 7.x
* New: Improved the frontend look and feel
* New: Threw in some Ajax goodness for handling replies (add, delete)
* New: Replies can now be deleted
* New: Log addition and deletion of replies
* Fix: Replies' HTML code sometimes not inserted properly (gets transformed to plain text)

= 3.1.2 (2015/11/26) =

* Fix: the add_meta_box function was not called within the proper callback

= 3.1.1 (2015/09/09) =

* Fix: translations and texts

= 3.1.0 (2015/05/06) =

* New: support for WP Customer Area 6.1
* Fix: fix PHP error in template file to list replies

= 3.0.0 (2015/02/17) =

* New: Support for Customer Area 6
* Fix: protect public access to conversation replies

= 2.3.0 (2014/06/17) =

* New: compatibility with Customer Area 5
* New: You can now edit and delete conversations from the frontend
* Fix: mark conversation as read when user views it

= 2.2.0 (2014/04/17) =

* New: compatibility with the new Advanced Custom Fields Integration add-on
* Fix: changed item templates to be compatible with the updated Customer Area 4.7 theme 
* Fix: months where not properly localized.

= 2.1.0 (2014/03/13) =

* New: compatibility with the new search add-on

= 2.0.3 (2014/02/23) =

* Fix: conversations started by a user not showing up on his dashboard 

= 2.0.2 (2014/02/20) =

* Compatibility with Customer Area 4.2.0

= 2.0.1 (2014/02/19) =

* Fix bug: also show conversations we have started in the conversation list 
* Add support for hook discovery 
* Fix bug: conversations menu item not showing up in the admin area

= 2.0.0 (2014/02/15) =

* Compatibility with Customer Area 4

= 1.3.0 (2014/01/23) =

* Add a capability to activate/deactive the rich editor for some users
* Make owner selection more user-friendly (see Customer Area 3.9.0 changes). If you have overidden any template file, please look for changes in there!
* Changed the form markup and the corresponding CSS to be mobile-friendly. Should improve compatibility with themes. If you have overidden any template file, please look for changes in there! 
* Load frontend scripts only when showing the Customer Area page (not on the other pages of the website)

= 1.2.3 (2013/12/03) =

* Adjusted styles for the new WordPress 3.8 admin

= 1.2.2 (2013/11/27) =

* Updated templates (requires CUAR 3.7.2+)

= 1.2.1 (2013/11/01) =

* Harmonize section headers with the other content lists
* Take the dashboard item limit into account
* Fix: when a user replies to a conversation, that one is not marked as containing new replies for him
* Fix: when a user starts a conversation, that one is not marked as containing new replies

= 1.1.1 (2013/10/30) =

* Can show the customer area menu on the conversation page
* Added a class to replies to differentiate author replies from recipient replies

= 1.1.0 (2013/10/27) =

* Delete all related replies when deleting a conversation
* Show the replies in the back office for a conversation
* Refined capabilities for the back-office

= 1.0.0 (2013/10/07) =

* First add-on release
