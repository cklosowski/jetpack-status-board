=== Jetpack - Status Board ===
Contributors: cklosows
Tags: status board, panic, jetpack, ios, ipad
Requires at least: 3.0
Tested up to: 3.6
Stable tag: 1.1.1
Donate link: https://wp-push.com/donations/
License: GPLv2

Integrate the Jetpack Stats Module with the Status Board iPad app from Panic.

== Description ==

Jetpack Stats - Status Board Integrates the Jetpack Stats Module with the Status Board iPad app.

Simply visit wp-admin on your iPad, click on the Jetpack menu, and choose the 'Status Board' menu item to add the graph to your Status Board app.

**This plugin requires you to have Jetpack installed, activated, and the stats module enabled**

= The following filters exist =
* jssb_output - Alter the graph options prior to outputting

== Installation ==

1. Install the Jetpack Stats - Status Board plugin
2. Activate the plugin
3. Click on the 'Status Board' menu item under the Jetpack menu.
4. Enjoy Status Board Updates for your Jetpack Stats


== Changelog ==
= 1.1.1 =
* FIX: Fixes a bug where ob_end_clean was being run when no buffers existed, causing the JSON to be blank.

= 1.1 =
* NEW: You can now copy and paste the URL for stats, or click a button to add to Status Board. Should help in cases where wp_redirect isn't working properly.
* NEW: Added a Contextual Help menu in order to assist in troubleshooting. Just click on the 'Help' tab in the upper right corner of the settings page.
* FIX: Corrected an issue if you update the admin email, it prevents the graph from being updated in Status Board. Due to this, you WILL NEED TO RE-ADD YOUR GRAPH TO STATUS BOARD. (Sorry for the caps, I just needed your attention)

= 1.0.3 =
* FIX: Output buffers causing an error when trying to load the Status Board View the first time

= 1.0.1 =
* FIX: Corrected issue when 0 visits is passed as 'null'

= 1.0 =
* Initial Release

== Frequently Asked Questions ==

= Do I have to buy the app for iOS for iPad =

Yes, this plugin requires that you buy the Status Board application by Panic. Their app is only available for iPad. You can download it directly from the App Store:
https://itunes.apple.com/us/app/status-board/id449955536?mt=8

== Screenshots ==
Coming Soon
