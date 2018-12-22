=== Simple Facebook Page Widget & Shortcode ===
Contributors: irkanu
Donate link: http://dylanryan.co/
Tags: facebook page, facebook widget, facebook shortcode, facebook page widget, facebook page shortcode, social widget, social shortcode
Requires at least: 3.0.0
Tested up to: 5.0.2
Stable tag: 1.6.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Easily display a Facebook Page's feed using a customizable widget or shortcode - now supports Page tabs & CTA! WP 5.0+ compatible.

== Description ==
= Overview =
This plugin uses Facebook Graph API v2.5 to generate a Facebook Page feed. The Page Plugin lets you easily embed and promote any Facebook Page on your website. Just like on Facebook, your visitors can like and share the Page without having to leave your site.

= Features =
* Facebook Page Widget
* Facebook Page Shortcode
* Optional Facebook script enqueue
* Facebook script loads asynchronously
* Displays Facebook Page in over 130+ languages
* Every support request receives a response
* Highest rated Facebook Page plugin
* Configure custom App ID (Advanced)
* Display Page timeline, events, messages tabs
* Display Page call to action
* Visual Composer support
* NEW: WordPress 5.0+ compatible

= Widget Instructions =
1. Add a customizable Facebook widget through Appearance -> Widgets.
2. Choose *Simple Facebook Page Widget*.
3. Update the *Facebook Page URL* and click Save.

See the [screenshots](https://wordpress.org/plugins/simple-facebook-twitter-widget/screenshots/) for examples.

= Shortcode Instructions =
Basic Shortcode Example:

`[facebook-page href="https://facebook.com/facebook" tabs="timeline"]`

Custom Shortcode Example:

`[facebook-page href="https://facebook.com/facebook" width="300" height="800" tabs="timeline, events, messages" show_cta="true" small_header="false" align="left" hide_cover="false" show_facepile="false"]`

= Support =

Support is offered on the [WordPress Support Forum](https://wordpress.org/support/plugin/simple-facebook-twitter-widget) for free, but please provide as much detail as possible as well as a link to where the issue is occurring. If you are comfortable with GitHub, then feel free to submit an [issue](https://github.com/irkanu/simple-facebook-page-widget/issues). I'll do my best to answer all support threads and issues.

> **Powered with ❤ by [Freemius Insights - Analytics for Plugin Developers](https://freemius.com/wordpress/insights/?utm_source=wordpress&utm_medium=link&utm_content=simple-facebook-twitter-widget&utm_campaign=insights)**

== Installation ==

= Automatic Installation =

The easiest way to install this plugin is automatically through WordPress because it will handle all of the file transferring. To get started, log in to your WordPress dashboard, navigate to the Plugins menu and click "Add New".

In the search field, type "Simple Facebook Widget" and hit Enter. Find the plugin with the icon with "SIMPLE" in all caps. You can install it by clicking "Install Now".

= Manual Installation =

The manual installation method involves downloading the plugin and uploading it to your server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= Updating =

It is recommended that you keep this plugin up-to-date. This plugin utilizes [Semantic Versioning](http://semver.org/).

See the [plugin description](https://wordpress.org/plugins/simple-facebook-twitter-widget/) for instructions.

See the [screenshots](https://wordpress.org/plugins/simple-facebook-twitter-widget/screenshots/) for examples.

== Frequently Asked Questions ==

= Can you make this plugin responsive? =

Yes and no. The minimum width of this widget is 280px and the maximum is 500px. The minimum height is 130px. This is set by Facebook & I have no control over it. If you figure out a way to hack this, then please submit a [pull request](https://github.com/irkanu/simple-facebook-page-widget). :)

= Is your plugin going to work if someone from another country visits my site? =

It depends. Some countries block websites like Twitter & Facebook, so this widget may not work for international viewers. However, if there is interest in a fallback in the scenario in which this happens, then I'll consider it.

= Does the visitor have to be logged in to Facebook to see the widget? =

Currently, yes. Please see this [support thread](https://wordpress.org/support/topic/unowned-facebook-pages-are-not-supported) to understand why. Again, if there is interest in a fallback in the scenario in which this happens, I'll be happy to consider it.

= Do I have to create my own custom Facebook App? =

Nope, but you can if you'd like.

== Screenshots ==

1. Frontend view of the Facebook Page widget.
2. Backend view of the Facebook Page widget.
3. Frontend view of the Facebook Page shortcode.
4. Backend view of the Facebook Page shortcode.

== Changelog ==

= 1.5.0 =
*Release Date 10/6/2017*

* Tested WordPress 4.8.2
* Updates Freemius SDK 1.2.2.9

= 1.4.16 =
*Release Date 9/8/2016*

* Tested WordPress 4.6.1
* Adds support for Visual Composer - thanks Hendrik!

= 1.4.15 =
*Release Date 8/21/2016*

* Tested WordPress 4.6.0

= 1.4.14 =
*Release Date 6/6/2016*

* Fixes current user deprecation
* Fixes a bug where disabling the SDK wouldn't display the setting properly
* Tested WordPress 4.5.3

= 1.4.13 =
*Release Date 4/10/2016*

* Fixed WPLANG fallback - Thanks drtonyb!
* Tested WordPress 4.4.3

= 1.4.12 =
*Release Date 2/24/2016*

* Updated Freemius SDK v1.7.3

= 1.4.11 =
*Release Date 2/24/2016*

* Fixes [Share bug](https://wordpress.org/support/topic/share-button-error-message) (app not configured)
* Fixes [wp_enqueue_script invalid arguments](https://github.com/irkanu/simple-facebook-page-widget/issues/11) - Thanks Česlav!
* Language now gracefully fails to WPLANG
* New option to add custom App ID
* App ID gracefully fails to plugin author's App ID
* Facebook SDK API v2.5

= 1.4.10 =
* Facebook TABS!!
* Validate href attributes on shortcode & widgets

= 1.4.9 =
* Tested WordPress 4.4.2
* Cleaner UI
* Freemius Insights integration for Analytics (new users only)

= 1.4.8.1 =
* Tested WordPress 4.3.1
* Removed Ads

= 1.4.7 =
* Performance optimization (Facebook script called asynchronously.)
* Code cleanup

= 1.4.6 =
* Fixed undefined index

= 1.4.5 =
* Updated settings page
* Working on customizable App ID

= 1.4.4 =
* Reworked settings page
* Preparation for next plugin

= 1.4.3 =
* Fixed widget outputting invalid HTML

= 1.4.2 =
* Bug fix translations
* Performance tweaks on settings page
* Feedback initiative

= 1.4.1 =
* Bug fix lib folder

= 1.4.0 =
* Added settings page
* Added Language option
* Added 135 languages

= 1.3.1 =
* Added German i18n

= 1.3.0 =
* Added alignment feature

= 1.2.2 =
* Tested up to WordPress 4.1.2

= 1.2.1 =
* Added PHP 5.2 Compatibility

= 1.2.0 =
* Added debugging tools

= 1.1.1 =
* Fixed screenshot bug

= 1.1.0 =
* i18n Support

= 1.0.0 =
* Initial Release
* Widget & Shortcode Fully Functional

== Upgrade Notice ==

= 1.5.0 =
* COMPATIBILITY & DEPENDENCY UPDATE: This is a compatibility update to ensure everythin is still working properly. Also updates Freemius SDK to latest version.

= 1.4.16 =
* COMPATIBILITY & FEATURE: This is a compatibility update to ensure everything is still working properly - it also adds support for Visual Composer modules.

= 1.4.14 =
* COMPATIBILITY: This is just a compatibility update to ensure everything is still working properly.

= 1.4.13 =
* COMPATIBILITY: This is just a compatibility update to ensure everything is still working properly.

= 1.4.11 =
* IMPORTANT: Fixes an [issue with the Share button](https://wordpress.org/support/topic/share-button-error-message) inside the like box. Also, Facebook SDK API v2.5.

= 1.4.10 =
* FEATURE: Now integrates with Facebook Tabs - use tabs="timeline, events, messages" in the shortcode.

= 1.4.8.2 =
* COMPATIBILITY: This is just a compatibility update to ensure everything is still working properly.

= 1.4.7 =
* IMPORTANT: Major performance boost on Facebook script. (It's now called asynchronously!)

= 1.4.3 =
* IMPORTANT: Please update to fix widget alignment outputting invalid HTML.

= 1.4.2 =
* Fixes Spanish & German translations

= 1.4.1 =
* Fixes missing chosen library on backend

= 1.4.0 =
* This update allows you to change the language output of your widget/shortcode.

= 1.3.1 =
* Added alignment feature (see plugin repo for examples) & German i18n support

= 1.2.1 =
* Added PHP 5.2 Compatibility

= 1.1.0 =
* Added support for Spanish