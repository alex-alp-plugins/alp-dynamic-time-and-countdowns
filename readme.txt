=== ALP Dynamic Time & Countdowns ===
Contributors: alexalp
Tags: countdown timer, dynamic dates, anniversary counter, countdown clock, divi 5
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 8.2
Stable tag: 1.0.27
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Free WordPress countdown timer and dynamic dates plugin: 20 designs, anniversary counters, reusable shortcodes, Gutenberg and Divi 5.

== Description ==

**Make every second count.** Create animated countdowns, dynamic dates, and anniversary counters that update automatically on your WordPress site.

**ALP Dynamic Time & Countdowns** brings together **20 customizable countdown designs**, dynamic years and dates, reusable shortcodes, Gutenberg integration, and an optional Divi 5 module. From Black Friday and Cyber Monday to Christmas, New Year, product launches, and personal milestones, make important moments easy to follow.

= ✨ Key Features =

* **20 animated countdown designs:** Flip Digits, 3D Odometer, Neon Digital, Liquid, Pixel Matrix, Particle Morph, Snowfall, Fireworks and more.
* **Flexible customization:** Configure the layout, typography, colors, gradients, labels, animations, padding, and border-radius controls supported by each design.
* **Dynamic dates and anniversaries:** Show years, dates, copyright year ranges, elapsed time, and recurring special events without editing the page every year.
* **Reusable shortcodes:** Save configurations once and reuse them throughout your website.
* **Gutenberg and Divi 5:** Insert saved designs with a Gutenberg block or the optional Divi 5 module.
* **SEO plugin integration:** Display supported dynamic dates in selected Rank Math, Yoast SEO and SEOPress output contexts.
* **Privacy-conscious and accessible:** Locally bundled fonts, reduced-motion support, and no built-in analytics.

= ⏳ Countdowns for Every Occasion =

Make time-sensitive content stand out during **Black Friday, Cyber Monday, Christmas, New Year's Eve**, sales, launches, seasonal promotions, birthdays, and anniversaries. Choose the timer design and customize its appearance for your website.

= 📅 Dynamic Dates, Milestones and Anniversaries =

Use automatically updating dates for copyright notices, business milestones, years of experience, celebrations and recurring events. Create named configurations that you can update centrally.

= 🔁 Moving From Years Since - Timeless Texts? =

**Years Since - Timeless Texts is closed in the WordPress.org plugin directory.** If your website previously used it, ALP Dynamic Time & Countdowns offers compatibility with the familiar **`[years-since]`** and **`[years-since-gb]`** shortcodes, so you can migrate without manually replacing every shortcode in your posts and pages.

Compatibility aliases are registered **only when those shortcode names are not already used by another active plugin**. Output and options may differ from the original; review pages after switching. ALP Dynamic Time & Countdowns is an independent plugin and is **not affiliated with or endorsed by** the original Years Since developers.

= 🎬 Interactive Demos Coming Soon! =

We're preparing live demonstrations of the countdown designs, animated effects and customization options. **Stay tuned!**

= 💜 Built by ALP Plugins =

All features currently included in this free release are available at no charge. Suggestions and feedback help shape future updates.

== Installation ==

1. Upload the ALP Dynamic Time & Countdowns ZIP through Plugins > Add New Plugin > Upload Plugin.
2. Activate the plugin and open ALP Dynamic Time & Countdowns in your WordPress admin menu (the sidebar label is abbreviated).
3. Configure an item in Generator and copy its shortcode into a shortcode-enabled area.
4. To use the Gutenberg block or Divi 5 module, save a shortcode first, then select it in the block/module.

== Frequently Asked Questions ==

= Is ALP Dynamic Time & Countdowns free? =

Yes. All features in the currently available release are free to use.

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

= 1.0.27 =
* Add the translator context for the saved color or gradient removal label to satisfy WordPress i18n checks. No functional changes.

= 1.0.26 =
* Border radius controls now use the familiar paired, linkable four-corner layout; previous radius attributes remain supported.
* Liquid Card / circle background gradients remain visible beneath the animated liquid effect in previews and published countdowns.

= 1.0.25 =
* Remove the Gradient stops divider and set the editable Angle field to 50px.
* Keep Flip Digits' outer corners rounded throughout a masked, two-stage flap animation.
* Apply Number card padding inside each 3D Odometer wheel rather than between wheels.
* Clarify Liquid's between-circle spacing and show Christmas Lights, Snowfall, and other design-specific help only when relevant.

= 1.0.24 =
* Compact gradient picker spacing and preserve readability of editable angles up to 360 degrees.
* Apply 3D Odometer padding inside individual wheel cards and Liquid padding inside fixed circular number cards.
* Add independent corner radius options while preserving legacy clock_radius values and old shortcodes.
* Show Particle Morph immediately after Pixel Matrix.
* Clip Flip Digits cards during all frames of the hinge animation.

= 1.0.23 =
* Soft Glow preview: stronger reset removes the pale residual wrapper when a custom solid or container gradient is selected.
* Color picker: Start/End stop chips now show the label and HEX on the same row.
* Color picker: Angle input is narrower and fits its content more closely while preserving direct typing.
* Flip Digits: animated flap halves keep rounded corners for the full motion.

= 1.0.22 =
* Remove the Soft Glow mint wrapper in live previews when a custom gradient is active.
* Make the Angle input more compact and separate Start/End labels from HEX values.
* Save and reuse linear/radial gradient favorites alongside existing HEX Quick Colors with strict validation.
* Round flip-digit card corners consistently, including animated halves.
* Preserve existing shortcodes and saved data; live tests in WordPress/Divi remain pending.

= 1.0.21 =
* Remove the original pale Soft Glow preview background and its extra gutter when a custom solid HEX is selected; preserve the default appearance when no custom background is set.
* Let administrators type a linear-gradient angle (0–360 degrees) directly in the compact control, synchronized with its slider and the live preview.
* Use a distinct Color Picker stylesheet URL to avoid stale assets from the previous version. Existing saved colors, shortcodes, Gutenberg and Divi 5 interfaces remain unchanged.

= 1.0.20 =
* Load the Color Picker presentation from a newly named, versioned CSS file after existing admin styles, preventing stale cached CSS from displaying unstyled browser controls.
* Refine Solid/Gradient and Linear/Radial segmented buttons; display the gradient Angle control as a compact inline slider with a live degree value.
* Reduce the internal scroll track prominence while retaining keyboard scrolling, HEX, opacity, Quick Colors, saved colors and live gradient preview.
* No shortcode, REST, saved option, Gutenberg or Divi 5 API changes.


= 1.0.19 =
* Refine the ALP Color Picker's Solid/Gradient segmented controls, selected start/end colors, gradient preview, typography and spacing without default browser buttons.
* Preserve HEX, opacity, hue, saved Quick Colors, angle, linear/radial modes and the existing shortcode gradient fields.
* Keep all 20 designs, Gutenberg/Divi 5 integrations and saved configurations unchanged.


= 1.0.17 =
* Correct scanner ambiguity for shortcode output sanitization, improve size steppers and gradient picker UI, synchronize gradient live preview and exclude hidden .distignore from the installation package.


= 1.0.16 =
* Prefix root PHP namespace as AlpDynamicTime (PSR-4), escape exception identifier output in Container, wrap admin view templates in closures instead of extract(), and escape dynamic feedback/pagination attributes to resolve Plugin Check and ALP Review Checker findings.
* Restore native ALP-styled number input controls for Label size matching Padding controls.
* Integrate Solid / Gradient mode, stops, direction/angle, and clear reset directly into the background color pickers (Soft Glow, Card/Circle, and Panel background) and apply countdown background gradients reliably through KSES-safe CSS variables.
* Vertically and optically center the footer review star icon with the review link text.

= 1.0.15 =
* Remove the close (`×`) button from auto-dismissing dashboard confirmation notices.
* Vertically center the yellow review star icon with the review invitation link in the dashboard footer and harden asset cache-busting with plugin version + filemtime.

= 1.0.14 =
* Link the dashboard footer review prompt directly to the official WordPress.org plugin review form.
* Set the default countdown Number size to 50px across all designs while preserving 60px as the default for Blood Dripping.
* Allow choosing the Font family for unit labels in Blood Dripping without altering the Blood Dripping numeral font.
* Add accessible up/down stepper buttons and keyboard arrow stepping to Label size.
* Automatically dismiss dashboard confirmation notices (such as saved date confirmations) after a few seconds with a manual dismiss control.
* Add linear and radial background gradient options for countdown containers and number cards/circles.

= 1.0.13 =
* Refactor internal PHP architecture to PSR-4 Clean Architecture (PHP 8.2+) with lazy dependency injection, centralized hook registration, and admin isolation behind is_admin().
* Harden REST API schema validation on /alpdt/v1/divi-preview and /alpdt/v1/render, restricting preview overrides to manage_options administrators.
* Prevent PCRE backreference expansion in shortcode end_message rendering and harden Divi 5 preview, countdown, live, and admin JavaScript fetch origins and DOM handling.
* Optimize performance: defer Gutenberg editor payload loading to enqueue_block_editor_assets, memoize saved shortcodes per request, conditionally enqueue Divi 5 module styles, cache legacy post scan queries with a 5-minute transient, and remove unused SVG assets.
* Restore ALP Plugins Author URI header while preserving existing plugin folder slug, options, shortcodes, blocks, and Divi 5 module identifiers for seamless in-place updates.

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
