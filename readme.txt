=== ALP Dynamic Time & Countdowns ===
Tags: dynamic date, countdown, timer, anniversary, divi
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 8.2
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Free dynamic dates, anniversaries, recurring countdowns and reusable milestones for WordPress, Gutenberg and Divi 5.

== Description ==

ALP Dynamic Time & Countdowns is a FREE WordPress plugin for dynamic dates, anniversary messages and attractive countdowns. Use it for promotions, seasonal campaigns, company milestones or text that should update automatically.

= Main features =

* 20 selectable countdown designs with customizable typography, colors, labels, motion and layout.
* Dynamic years, calendar dates, anniversary counts and messages, copyright years and recurring events.
* Saved dates and reusable shortcode configurations, managed in the WordPress dashboard.
* Gutenberg block and optional Divi 5 module with a simple saved-shortcode selector.
* SEO shortcode output compatibility with supported Rank Math, Yoast SEO and SEOPress fields, plus native Rank Math/Yoast variables where applicable.
* Seasonal countdowns for Black Friday, Cyber Monday, Christmas, New Year and other occasions.
* Bundled local fonts, translations and reduced-motion support.

All current features are free. A future Pro edition is only a possibility, not an existing product or requirement.

== Installation ==

1. Upload the ALP Dynamic Time & Countdowns ZIP through Plugins > Add New Plugin > Upload Plugin.
2. Activate the plugin and open ALP Dynamic Time & Countdowns in your WordPress admin menu (the sidebar label is abbreviated).
3. Configure an item in Generator and copy its shortcode into a shortcode-enabled area.
4. To use the Gutenberg block or Divi 5 module, save a shortcode first, then select it in the block/module.

== Frequently Asked Questions ==

= Is ALP Dynamic Time & Countdowns free? =

Yes. All features in version 1.0.0 are free. A Pro edition might be considered later but is not currently available.

= How do I create a countdown? =

In Generator select Countdown, set the target date and appearance, then copy the shortcode. Example: [alpdt_time type="countdown" to="2027-12-31T23:59" countdown_display="full" clock_style="glow"]. The date and time use the WordPress site timezone.

= How do the Gutenberg block and Divi 5 module work? =

Save a configuration using Generator > Save shortcode. Insert the ALP Dynamic Time & Countdowns Gutenberg block or Divi 5 module and choose that saved item. The complete configuration stays in the plugin dashboard.

= Can I reuse a countdown or anniversary in several places? =

Yes. Create a named saved shortcode and place [alpdt_time saved="new-year-countdown"] anywhere shortcodes are supported. Updating the saved configuration updates all references to that identifier.

= Can ALP Dynamic Time & Countdowns replace Years Since - Timeless Texts? =

ALP Dynamic Time & Countdowns was inspired by Years Since - Timeless Texts after that plugin was closed in the WordPress.org directory. It was developed independently to preserve common shortcode forms like [years-since] and [years-since-gb] while adding more functionality. These aliases are available only if another plugin has not already registered them. The two products are not affiliated and behavior is not guaranteed to be identical.

= Does ALP Dynamic Time & Countdowns support SEO plugins? =

Yes, through supported published-output filters and native variables where available for Rank Math and Yoast SEO. Some SEOPress output filters are also supported. Verify the actual published metadata, since editor previews and caches may differ.

== Privacy and stored data ==

Saved dates and named shortcodes are stored in site options; individual Quick Colors palettes use user metadata. Some live updates and date calculations use the site's own REST API. Saved dates are intended for publicly displayable information and may be requested by users who know their identifiers. ALP Dynamic Time & Countdowns does not include analytics or transmit countdown data to external services. Bundled font files are served from the WordPress installation. Removing the plugin does not automatically delete saved data.

== Changelog ==

= 1.0.0 =
* First stable version: dynamic dates, anniversaries, 20 countdown designs, reusable shortcodes and saved milestones.
* Gutenberg block and Divi 5 module select saved shortcodes.
* Improved copy confirmations, streamlined local fonts and clean administrative hero without hiding important third-party notices.
