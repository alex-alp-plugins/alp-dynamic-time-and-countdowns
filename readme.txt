=== ALP Dynamic Time & Countdowns ===
Contributors: alexalp
Tags: countdown timer, dynamic dates, anniversary counter, countdown clock, divi 5
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 8.2
Stable tag: 1.0.12
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Free WordPress countdown timer and dynamic dates plugin: 20 designs, anniversary counters, reusable shortcodes, Gutenberg and Divi 5.

== Description ==

ALP Dynamic Time & Countdowns is a free WordPress countdown timer and dynamic dates plugin with 20 countdown designs, customizable animations, anniversary counters, reusable shortcodes, Gutenberg support and a Divi 5 module. Create event countdowns for Black Friday, Cyber Monday, Christmas, New Year, launches and promotions, or display milestones and dates that update automatically.

= Main features =

* 20 selectable countdown designs with customizable typography, colors, labels, motion and layout.
* Dynamic years, calendar dates, anniversary counts and messages, copyright years and recurring events.
* Saved dates and reusable shortcode configurations, managed in the WordPress dashboard.
* Gutenberg block and optional Divi 5 module with a simple saved-shortcode selector.
* SEO shortcode output compatibility with supported Rank Math, Yoast SEO and SEOPress fields, plus native Rank Math/Yoast variables where applicable.
* Seasonal countdowns for Black Friday, Cyber Monday, Christmas, New Year and other occasions.
* Bundled local fonts, translation-ready strings and reduced-motion support.

All currently available features are free; no paid plan or subscription is required.

== Installation ==

1. Upload the ALP Dynamic Time & Countdowns ZIP through Plugins > Add New Plugin > Upload Plugin.
2. Activate the plugin and open ALP Dynamic Time & Countdowns in your WordPress admin menu (the sidebar label is abbreviated).
3. Configure an item in Generator and copy its shortcode into a shortcode-enabled area.
4. To use the Gutenberg block or Divi 5 module, save a shortcode first, then select it in the block/module.

== Frequently Asked Questions ==

= Is ALP Dynamic Time & Countdowns free? =

Yes. All features included in ALP Dynamic Time & Countdowns are free to use.

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

Saved dates and named shortcodes are stored in site options; individual Quick Colors palettes use user metadata. Some live updates and date calculations use the site's own REST API. Saved dates are intended for publicly displayable information and may be requested by users who know their identifiers. ALP Dynamic Time & Countdowns does not include analytics or transmit countdown data to external services. Bundled font files are served from the WordPress installation. Quick Colors can be exported and erased using WordPress privacy tools (Tools > Export Personal Data / Erase Personal Data). Uninstall removes only Quick Colors user metadata. Site-wide saved dates and shortcodes are intentionally retained and must be reviewed separately for privacy requests. The privacy policy helper offers suggested disclosure text.

== Changelog ==

= 1.0.12 =
* Add WordPress personal data exporter, eraser and privacy-policy guidance for per-user Quick Colors palettes.
* Remove only the plugin-owned Quick Colors user metadata on uninstall while keeping all site-wide saved dates and shortcodes intact.
* Use minified CSS and JavaScript in production and original human-readable sources when SCRIPT_DEBUG is enabled, preserving all features and WordPress/Divi/Gutenberg script handles.
* List the verified WordPress.org developer contributor; remove developer-only documentation from the installable WordPress.org ZIP.
* No changes to saved data formats, existing shortcode names or the 20 countdown designs.


= 1.0.11 =
* Sanitize final [alpdt_time] HTML using a scoped WordPress KSES allowlist, retaining all supported timer effects, SVG rings and data attributes.
* Apply a final HTML-escaping boundary to the [years-since] legacy shortcode.
* Escape shortcode replacements inserted in widget and SEO text without changing surrounding markup.
* Resolve saved shortcode attributes before rendering so they use the same output-sanitization boundary.
* No database migrations, shortcode renames, removed designs or Divi/Gutenberg API changes.

= 1.0.10 =
* Remove broken author/branding website links while preserving ALP Plugins author credit and official footer logo.
* Remove bundled Spanish PO/MO catalogs from the WordPress.org package; WordPress.org language packs can provide approved translations.
* Sanitize the legacy [years-since-gb] wrapper's final HTML while preserving the ALP circular countdown's safe SVG.
* Register Gutenberg JavaScript translations for WordPress.org language packs.
* No changes to shortcode names, data storage, Divi modules, countdown styles, or saved configuration formats.

= 1.0.9 =
* Improve identifier fields with automatic normalization and clear examples in Saved dates and saved shortcodes.

= 1.0.8 =
* Match the official WordPress.org slug `alp-dynamic-time-countdowns` in PHP, Gutenberg, metadata and bundled translation filenames.
* Distribute the plugin in the `alp-dynamic-time-countdowns/` directory required by WordPress.org.
* Keep existing saved dates, shortcodes, custom options, data structures and frontend behavior unchanged.
* Existing installations in the older `alp-dynamic-time/` directory need a deliberate, backed-up migration; do not activate both copies.

= 1.0.7 =
* Correct ZIP distribution: stable alp-dynamic-time/ root folder for in-place updates and consistent Plugin Check slug and translation domain.
* No changes to stored options, saved dates, presets, shortcodes, color palettes, Divi 5, Gutenberg, visual controls or translations.
* Existing parallel installations under a different folder must be deactivated and removed manually after backup; WordPress cannot safely merge unknown duplicate installation paths.


= 1.0.6 =
* Compact the ALP color picker, including a smaller color field and 26px Quick Colors tiles, while retaining viewport-safe positioning and internal scrolling.

= 1.0.4 =
* Correct the ALP color picker positioning so it fits below the admin toolbar and inside the WordPress content area, with internal scrolling if needed.

= 1.0.3 =
* Restore natural falling-grain speed in Hourglass across days, hours, minutes and optional larger units; vary grain appearance frequency by time unit instead of slowing the falling motion.

= 1.0.2 =
* Improve WordPress countdown timer and dynamic dates description. Remove references to hypothetical paid editions.
* Replace distracting turquoise tab focus rectangles with subtle, accessible keyboard focus styling.
* Slow Blood Dripping animation and randomize the release positions and timing of celebration balloons.

= 1.0.0 =
* First stable version: dynamic dates, anniversaries, 20 countdown designs, reusable shortcodes and saved milestones.
* Gutenberg block and Divi 5 module select saved shortcodes.
* Improved copy confirmations, streamlined local fonts and clean administrative hero without hiding important third-party notices.
