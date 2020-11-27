=== WP Customer Area - Notifications ===

Contributors: 		vprat, marvinlabs
Tags: 				private files,client area,customer area,user files,secure area,crm,project,project management,access control
License: 			Commercial
License URI: 		http://wp-customerarea.com/terms-and-conditions/
Donate link: 		http://www.marvinlabs.com/donate/
Requires at least:  4.7
Tested up to:       5.4.2
Stable tag: 		6.5.0

== Changelog ==

= 6.5.0 (2020/06/18) =

* Tweak: Update translations
* Tweak: Improve security

= 6.4.2 (2018/12/14) =

* New: Add filter to allow filtering the IDs of the users who should receive the notifications targeted at administrators. Use filter 'cuar/notifications/administrator-recipient-ids' to customize.

= 6.4.1 (2018/06/15) =

* Tweak: Notifications for new content created will now be sent to the current connected user saving the post. Use filter 'cuar/notifications/allow-notification-to-self' to disable this.

= 6.4.0 (2018/04/24) =

* Updated french translations
* New: Add filter to allow filtering the IDs of the users who should receive the notifications targeted at administrators

= 6.3.1 (2017/12/20) =

* Fix: Some notifications where not properly sent
* Fix: Infinite recursion when notifying of a new project

= 6.3.0 (2017/10/11) =

* New: Log notifications that get sent
* Fix: Allow by default sending notifications to current user. The filter 'cuar/notifications/allow-notification-to-self' can be used to change that.
* Fix: switch clearfix CSS class to cuar-clearfix to avoid conflicts

= 6.2.0 (2017/06/29) =

* New: email layouts can now be configured (colors, logo, etc.)
* New: Notification when task list gets completed
* Tweak: Enhanced the default notification template

= 6.1.0 (2017/05/15) =

* New: compatibility with new addon "Design Extras"
* New: Add recipient setting for file downloaded notification
* New: Notification when tasks are about to be overdue
* New: Notification when tasks are overdue
* Fix: Fix bug for file download notification mode (first time only)

= 6.0.3 (2016/09/15) =

* Fix: compatibility with WP Customer Area 7.1
* Fix: added a custom message for conversation started notification

= 6.0.2 (2016/06/23) =

* Fix: notification not sent to conversation author when a new reply is posted

= 6.0.1 (2016/06/13) =

* Fix: notification not sent when a new reply is posted

= 6.0.0 (2016/06/07) =

* New: Compatibility with WP Customer Area 7.x
* New: You can now have email templates which look great with logo & all HTML goodness
* New: Custom emails sent on registration/password reset when used with the authentication forms add-on
* New: Custom emails sent on comment & comment moderation for private post types
* New: Rewrote the code to send the notifications, now the hooks are easier to understand and should allow more flexibility
* New: Individual notifications for each type of private content creation for better control
* Fix: Compatibility with the projects add-on (new project notification)
* Fix: Compatibility with the tasks add-on (new task list notification from frontend)
* Fix: Cleaner options page

= 5.2.0 (2015/11/26) =

* New: Allow filtering headers (the filter 'cuar/notifications/headers/return-path-email' has been replaced by more general 'cuar/notifications/headers')
* New: Add placeholders for attachment name/caption to the notification for download/view: %attachment_name% and %attachment_caption%
* Fix: the add_meta_box function was not called within the proper callback

= 5.1.0 (2015/09/09) =

* New: Support for WP Customer Area 6.2

= 5.0.0 (2015/02/17) =

* New: Support for WP Customer Area 6

= 4.1.0 (2014/06/17) =

* New: Compatibility with WP Customer Area 5

= 4.0.1 (2014/04/17) =

* New: Add support for hook discovery
* New: Support for template versioning

= 4.0.0 (2014/02/15) =

* Compatibility with WP Customer Area 4
* Removed the "post not published" error message shown even if the checkbox is not ticked when creating a post 
* Added a way to check the notification checkbox by default on posts

`function my_check_notification_by_default() {
	return 'checked="checked"';
}
add_filter( 'cuar/notifications/send-notification-meta-box/default-checkbox-value', 'my_check_notification_by_default' );`

= 3.1.0 (2013/12/05) =

* Added a notification to administrators when a new private content is published

= 3.0.3 (2013/11/01) =

* Fixed: author of a reply should not get the notification of his own reply

= 3.0.2 (2013/10/27) =

* Fixed: author of a conversation was not notified on a new reply

= 3.0.1 (2013/10/23) =

* Added support for notifications about the new conversations add-on (new conversation, new reply)
* Reworked the settings page to make it easier to customize your notifications

= 2.1.1 (2013/09/03) =

* Fixed bug while saving settings
* Fixed typo in default new private file notification 

= 2.1.0 (2013/06/26) =

* Added support for new collaboration add-on (notification when private content is held for moderation)

= 2.0.1 (2013/05/31) =

* Added some placeholders to the private file download notification
* Simplified the options for the new private post notification (same for files and pages)
* Compatibility with Customer Area 2.0.1

= 1.1.0 (2013/05/17) =

* Fixed automatic updates
* Support for private pages

= 1.0.0 (2013/05/02) =

* First plugin release
* Notify a customer when you update a new file for him
* Get notified when a customer downloads his file (only the first time, or each time)
