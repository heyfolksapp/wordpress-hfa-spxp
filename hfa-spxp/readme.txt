=== Support for the Social Profile Exchange Protocol (SPXP) ===
Contributors: heyfolksapp
Tags: spxp, social, social network, feed, decentralized
Requires at least: 4.7
Tested up to: 7.0
Stable tag: 1.3
Requires PHP: 8.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Exposes your WordPress blog via the Social Profile Exchange Protocol (SPXP) — an open, decentralized social networking protocol focused on privacy and individual sovereignty.

== Description ==

The Social Profile Exchange Protocol (SPXP) is an open, decentralized social networking protocol focused on privacy and individual sovereignty — a social network of independent actors, not controlled by any single entity.

This plugin exposes your WordPress blog via SPXP so anyone using an SPXP client (like the [HeyFolks app](https://HeyFolks.app/)) can follow your posts. It supports text, photo, video, and web post types, with configurable image sizes and a profile picture.

Once activated, your blog is available at `/spxp`. If your blog lives at `https://example.com`, your SPXP profile URI is `https://example.com/spxp`.

You can follow the development and contribute PRs on [GitHub](https://github.com/heyfolksapp/wordpress-hfa-spxp)

== Get Involved ==

Development happens on GitHub:
https://github.com/heyfolksapp/wordpress-hfa-spxp

You can browse the code, suggest a feature, file an issue or contribute directly by raising a pull request.

== Installation ==

As usual: Copy the `/hfa-spxp` folder to `/wp-content/plugins` of your wordpress instance, activate the plugin and then adjust it
to your liking on the SPXP settings page.

Once activated, it exposes your blog via SPXP under the `/spxp` endpoint. If your blog lives at `https://example.com/myblog`, then
your SPXP profile URI would be `https://example.com/myblog/spxp`.

== Screenshots ==

1. Settings of your SPXP profile

== Changelog ==

= 1.3 =
* Added SPXP `video` post type: posts with a video attachment and a featured image now appear as `video` in the feed with `media` and `preview` fields
* Refactored post-building logic into focused helper methods for easier maintenance

= 1.2 =
* Updated minimum PHP requirement to 8.2
* Updated tested WordPress version to 7.0
* Fixed PHP 8.x compatibility warnings (undefined array key access)
* Added nonce verification to settings AJAX handler
* Fixed settings values not being sanitized on save
* Code quality improvements: method visibility, unicode JSON output

= 1.1 =
* Fixed image posts
* Fixed bug with deleted image assets

= 1.0 =
* Initial version

== Upgrade Notice ==

= 1.0 =
Initial version.