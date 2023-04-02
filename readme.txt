=== FS Login Devices ===
Contributors: fsylum
Tags: user, auth, session, user-agent
Requires at least: 5.9
Tested up to: 6.2
Stable tag: 1.0.1
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Track and display all users devices used during authentication process

== Description ==

This plugin allows you to track all user devices used when logging in to your site. The user agent, login date/time, and logout date/time (if the users log out from the site) are tracked automatically and persisted into the database for later viewing.

You can find a list of all login devices available under the Users > Login Devices submenu.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/fs-login-devices` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. View all login devices on Users > Login Devices.

== Screenshots ==

1. Listing page that show login history alongside the user agent and timestamps.

== Changelog ==

**1.0.1**

* [FIXED] Simplify composer.json and package.json (unrelated to actual plugin)
* [FIXED] Compatibility with WordPress 5.8

**1.0.0**

* Initial release
