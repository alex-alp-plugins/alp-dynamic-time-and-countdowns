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

**Make every second count. Bring dynamic dates, animated countdowns, and memorable milestones to your WordPress website.**

**ALP Dynamic Time & Countdowns** is a free, flexible WordPress plugin that lets you create beautiful countdown timers, automatically updating dates, anniversary counters, and reusable time-based content, all from one intuitive dashboard.

Whether you're counting down to a product launch, celebrating an anniversary, or keeping your copyright year up to date, *your website stays in sync with time.*

**20 countdown designs. Powerful customization. Endless creative possibilities.**

= ✨ Key Features =

* **20 animated countdown designs:** Choose from Flip Digits, Neon Digital, Circular Progress Dial, 3D Odometer, Particle Morph, Hourglass, Snowfall, Fireworks, and more.
* **Dynamic dates:** Automatically display the current year, month, formatted dates, and copyright year ranges.
* **Anniversaries and milestones:** Calculate elapsed years, celebrate special dates, and display personalized anniversary messages.
* **Reusable shortcodes:** Create once, save your configuration, and display it throughout your website.
* **Gutenberg and Divi 5 integration:** Insert saved countdowns and dynamic dates using dedicated blocks or modules.
* **SEO plugin compatibility:** Dynamic date output in supported Rank Math, Yoast SEO, and selected SEOPress fields; native variables in supported Rank Math and Yoast contexts.
* **Advanced styling:** Customize colors, typography, layouts, animations, and time-unit labels where supported by each design.
* **Recurring events:** Create countdowns for birthdays, holidays, seasonal promotions, and annual events.
* **Responsive and accessibility-conscious:** Adaptable layouts, locally bundled resources, and reduced-motion support.

= ⏱️ Animated Countdown Timers =

Create countdowns for product launches, special announcements, promotions, holidays, weddings, birthdays, and milestones. Choose between supported styles, adjust the presentation, and display remaining days, hours, minutes, and seconds. Time calculations use the WordPress site's configured timezone.

= 📅 Dynamic Dates and Anniversaries =

Show the current year or month, formatted calendar dates, copyright year ranges, elapsed time, anniversary numbers, and special messages. Smart date expressions and recurring dates can keep time-sensitive content current without manual annual edits.

= 🔁 Save Once, Reuse Anywhere =

Use the Generator to configure and save countdowns or dates as named shortcodes. Reuse a saved identifier across multiple pages and update its configuration from the plugin dashboard.

* **Example countdown:** `[alpdt_time type="countdown" to="2027-12-31T23:59" countdown_display="full" clock_style="glow"]`
* **Example saved configuration:** `[alpdt_time saved="new-year-countdown"]`

The dedicated Gutenberg block and optional Divi 5 module let you select previously saved shortcodes. Divi 5 is not required for standard shortcodes or Gutenberg.

= 🚀 What's New in v1.0.12? =

This release focuses on **privacy, performance, and WordPress.org distribution readiness**, while preserving existing features and configurations.

* **Improved privacy integration:** Export and erase personal Quick Colors palettes using WordPress's built-in privacy tools.
* **Privacy policy guidance:** Suggested disclosure text for website administrators.
* **Optimized assets:** Minified CSS and JavaScript in normal operation, with readable sources available via `SCRIPT_DEBUG`.
* **Safer uninstall behavior:** Remove plugin-specific Quick Colors user metadata while retaining saved dates, shortcodes, and site-wide settings.
* **Cleaner distribution:** Exclude internal development documentation from the WordPress.org installation package.

*Existing shortcode formats, saved data structures, and all 20 countdown designs are unchanged.*

= 🎬 Interactive Demos Coming Soon! =

We're preparing **live interactive demos** so you can explore the countdown designs, animations, customization options, and dynamic date features before installing the plugin.

**Stay tuned!** More examples, creative inspiration, and interactive previews are on the way.

= 🧩 Compatibility and Requirements =

* **WordPress:** 6.5 or newer
* **PHP:** 8.2 or newer (use a security-patched runtime)
* **Gutenberg:** Dedicated block
* **Divi 5:** Optional dedicated module
* **License:** GPL-2.0-or-later

= 💜 Built for WordPress =

**Powerful time-based content, intuitive controls, and creative freedom.**

All features included in version 1.0.12 are available free of charge.

Built by **ALP Plugins** with a focus on flexibility, customization, performance, and a WordPress-friendly experience. The plugin includes no analytics or third-party transmission of countdown data.

If you enjoy the plugin, consider supporting its development with feedback, suggestions, or an honest WordPress.org review. **Your feedback helps shape what comes next!**

== Installation ==

1. Upload the ALP Dynamic Time & Countdowns ZIP through Plugins > Add New Plugin > Upload Plugin.
2. Activate the plugin and open ALP Dynamic Time & Countdowns in your WordPress admin menu (the sidebar label is abbreviated).
3. Configure an item in Generator and copy its shortcode into a shortcode-enabled area.
4. To use the Gutenberg block or Divi 5 module, save a shortcode first, then select it in the block/module.

== Frequently Asked Questions ==

= Is ALP Dynamic Time & Countdowns free? =

Yes. All features included in version 1.0.12 are available free of charge.

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

== Screenshots ==

1. Circular Progress Dial countdown with editable colors, progress rings, and a live preview.
2. Flip Digits countdown with animated numeric cards and configurable time units.
3. Neon Digital style with glowing numbers and custom typography options.
4. Fireworks Celebration countdown with configurable celebration timing and a live preview.
5. Confetti and Balloons celebration with adjustable colors and animation settings.
6. Blood Dripping countdown with dedicated style and color controls.
7. Christmas Lights countdown with customizable bulb colors and spacing.
8. Snowfall countdown with its default layout and winter-themed appearance.
9. Snowfall countdown shown in a compact horizontal layout.
10. Particle Morph countdown with dot-based digital numbers.
11. Orbit Motion countdown with animated circular number cards.
12. Anniversary Message generator with before, on, and after-anniversary text options.
13. Elapsed Time generator for years, months, weeks, or days since a starting date.
14. Smart Dates and calendar expressions that update automatically.
15. Saved Shortcodes area for reusing and editing named countdown configurations.
16. Saved Dates area for managing reusable milestones and their identifiers.
17. SEO plugin compatibility guide with dynamic variables for supported Rank Math and Yoast SEO fields.

== Privacy and stored data ==

Saved dates and named shortcodes are stored in site options; individual Quick Colors palettes use user metadata. Some live updates and date calculations use the site's own REST API. Saved dates are intended for publicly displayable information and may be requested by users who know their identifiers. ALP Dynamic Time & Countdowns does not include analytics or transmit countdown data to external services. Bundled font files are served from the WordPress installation. Quick Colors can be exported and erased using WordPress privacy tools (Tools > Export Personal Data / Erase Personal Data). Uninstall removes only Quick Colors user metadata. Site-wide saved dates and shortcodes are intentionally retained and must be reviewed separately for privacy requests. The privacy policy helper offers suggested disclosure text.

== Changelog ==

= 1.0.12 =
* Add WordPress personal data exporter, eraser and privacy-policy guidance for per-user Quick Colors palettes.
* Remove only the plugin-owned Quick Colors user metadata on uninstall while keeping all site-wide saved dates and shortcodes intact.
* Use minified CSS and JavaScript in production and original human-readable sources when SCRIPT_DEBUG is enabled, preserving all features and WordPress/Divi/Gutenberg script handles.
* List the verified WordPress.org developer contributor; remove developer-only documentation from the installable WordPress.org ZIP.
* No changes to saved data formats, existing shortcode names or the 20 countdown designs.

Earlier releases: see `CHANGELOG.md`.
