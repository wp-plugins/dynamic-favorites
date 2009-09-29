=== Dynamic Favorites ===
Contributors: ansimation, sivel
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=C3UA7TDWM4NLQ&lc=US&item_name=Donations%20for%20Sivel%2enet%20WordPress%20Plugins&cn=Add%20special%20instructions%20to%20the%20seller&no_shipping=1&rm=1&return=http%3a%2f%2fsivel%2enet%2fthanks&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: dynamic-favorites, dynamic, favorites, 2.7, admin
Requires at least: 2.7
Tested up to: 2.7
Stable tag: 1.3

Populates the favorites drop down menu, introduced in 2.7, with links based on actual page accesses. Lists the pages you actually use most.

== Description ==

Populates the favorites drop down menu, introduced in 2.7, with links based on actual page accesses. Lists the pages you actually use most.

Tracks admin page accesses and maintains a running total of those page accesses. Based on the top admin page accesses, this plugin will display a configurable number of favorites in the drop down menu.

== Installation ==

1. Upload the `dynamic-favorites` folder to the `/wp-content/plugins/` directory or install directly through the plugin installer.
1. Activate the plugin through the 'Plugins' menu in WordPress or by using the link provided by the plugin installer
1. Optional: Visit the options page in the Admin at `Settings>Dynamic Favorites` to configure the number of favorites to display in the drop down.

NOTE: See "Other Notes" for Upgrade and Usage Instructions as well as other pertinent topics.

== Frequently Asked Questions ==

= How can I show more items in my favorites menu? =

Browse to `Settings>Dynamic Favorites` and enter a new limit. The default is 5.

= Can I reset the links in my favorites drop down? =

Yes. Visit your profile page and select `true` from the `Reset Dymanic Favorites` drop down and then click `Update Profile`.

== Screenshots ==

1. Admin Favorites Drop Down Menu

== Upgrade ==

1. Use the plugin updater in WordPress or...
1. Delete the previous `dynamic-favorites` folder from the `/wp-content/plugins/`
directory
1. Upload the new `dynamic-favorites` folder to the `/wp-content/plugins/`
directory
1. Optional: Visit the options page in the Admin at `Settings>Dynamic Favorites`

== Usage ==

1. Just install and activate.
1. Optional: Visit the options page in the Admin at `Settings>Dynamic Favorites` to set the limit of items to disaplay in the favorites drop down.
1. Optional: Visit your profile to reset your list of items in the favorites menu.

== Changelog ==

= 1.3 (2008-02-04): =
* XHTML 1.0 fixes for links in drop down 

= 1.2 (2009-01-27): =
* Fixed function reference for call to add the options page

= 1.1 (2009-01-26): =
* Added prepare statements for the sql queries
* Added additional ignores
* Code cleanup

= 1.0 (2009-01-15): =
* Initial Public Release
