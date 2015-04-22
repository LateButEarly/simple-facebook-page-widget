=== Simple Facebook Page Widget & Shortcode ===
Contributors: irkanu
Donate link: https://donorbox.org/dylan-ryan-simple-facebook-page-widget
Tags: facebook, widget, plugin, page, feed, shortcode, facebook page, facebook widget, facebook shortcode, facebook page widget, facebook page shortcode, social media
Requires at least: 3.0.0
Tested up to: 4.1.2
Stable tag: 1.2.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Easily display Facebook's new Page feed using a widget or shortcode.

== Description ==
= Overview =
This plugin uses Facebook Graph API v2.3 to generate a Facebook Page feed. Many sites are currently running Facebook Like Boxes which will become **deprecated on June 23rd, 2015**. The Page Plugin lets you easily embed and promote any Facebook Page on your website. Just like on Facebook, your visitors can like and share the Page without having to leave your site.

= Widget Instructions =
1. Add a customizable Facebook widget through Appearance -> Widgets.
2. Choose *Simple Facebook Page Widget*.
3. Update the *Facebook Page URL* and click Save.

See the [screenshots](https://wordpress.org/plugins/simple-facebook-twitter-widget/screenshots/) for examples.

= Shortcode Instructions =
Basic Shortcode Example:
> `[facebook-page href="facebook"]`

Custom Shortcode Example:
> `[facebook-page href="facebook" width="300" height="800" hide_cover="false" show_facepile="false" show_posts="true"]`

**Note:** If your *Facebook Page URL* is https://facebook.com/facebook then please enter `facebook` as the href argument.

= Support =

Support is offered on the [WordPress Support Forum](https://wordpress.org/support/plugin/simple-facebook-twitter-widget) for free, but please provide as much detail as possible as well as a link to where the issue is occurring. If you are comfortable with GitHub, then feel free to submit an [issue](https://github.com/irkanu/simple-facebook-page-widget/issues). Iâ€™ll do my best to answer all support threads and issues.

> <strong>Donations</strong><br>
> This plugin was developed to be used free of charge; however, if you do wish to donate, then you can visit the [secure donation page](https://donorbox.org/dylan-ryan-simple-facebook-page-widget).

== Installation ==

This section describes how to install the plugin and get it working.

1. Download `simple-facebook-page-widget.zip`.
2. Upload `simple-facebook-page-widget` plugin to the `/wp-content/plugins/` directory through the WordPress plugin interface.
3. Activate the plugin through the `Plugins` menu in WordPress.
4. `Optional:` Add a Simple Facebook Page Widget from the `Appearance -> Widgets` menu in WordPress.
5. `Optional:` Add a [facebook-page href="facebook"] shortcode inside of a page or post.

== Frequently Asked Questions ==

= Can you make this plugin responsive? =

Yes and no. The minimum width of this widget is 280px and the maximum is 500px. The minimum height is 130px. This is set by Facebook & I have no control over it. If you figure out a way to hack this, then please submit a [pull request](https://github.com/irkanu/simple-facebook-page-widget). :)

= Is your plugin going to work if someone from another country visits my site? =

It depends. Some countries block websites like Twitter & Facebook, so this widget may not work for international viewers. However, if there is interest in a fallback in the scenario in which this happens, then Iâ€™ll consider it.

= Does the visitor have to be logged in to Facebook to see the widget? =

Currently, yes. Please see this [support thread](https://wordpress.org/support/topic/unowned-facebook-pages-are-not-supported) to understand why. Again, if there is interest in a fallback in the scenario in which this happens, Iâ€™ll be happy to consider it.

= Do I have to create my own custom Facebook App? =

Currently, no. In the future, perhaps.

== Screenshots ==

1. Frontend view of the Facebook Page widget.
2. Backend view of the Facebook Page widget.
3. Frontend view of the Facebook Page shortcode.
4. Backend view of the Facebook Page shortcode.

== Changelog ==

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

= 1.2.1 =
* Added PHP 5.2 Compatibility

= 1.1.0 =
* Added support for Spanish