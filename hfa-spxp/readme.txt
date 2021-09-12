=== Support for the Social Profile Exchange Protocol (SPXP) ===
Contributors: heyfolksapp
Tags: spxp
Requires at least: 4.7
Tested up to: 5.8.1
Stable tag: 1.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Exposes your blog via the Social Profile Exchange Protocol so your friends can follow your updates using any SPXP client, like the HeyFolks app.

== Description ==

The Social Profile Exchange Protocol (SPXP) defines how independent clients and servers can exchange information about social profiles,
focusing on privacy, security and individual sovereignty.  
It aims to create a social media network consisting of independent actors rather than being controlled by a single entity.

This plugin makes your wordpress instance available via this protocol so that people can start following your blog posts with
any SPXP client of their liking, like the [Hey Folks app](https://HeyFolks.app/).

You can set the profile picture and a longer "About" text in the settings and tweak how posts on our Blog are presented via SPXP.

This plugin is not yet available for multisite installations.

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
2. Your blog as seen in the HeyFolks app

== Changelog ==

= 1.0 =
* Initial version

== Upgrade Notice ==

= 1.0 =
Initial version.
