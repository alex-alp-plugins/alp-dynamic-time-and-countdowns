=== ALP Dynamic Time & Countdowns ===
Tags: dynamic date, countdown, timer, anniversary, divi
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 8.2
Stable tag: 1.0.0-rc.3
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Dynamic dates, recurring countdowns, celebrations and reusable milestones with optional Divi 5 integration.

== Description ==

ALP Dynamic Time & Countdowns is a free WordPress plugin that replaces static year references with safe dynamically calculated text. It provides a shortcode, a Gutenberg block that selects saved shortcodes, saved milestones, a named saved-shortcode library, a generator, copy controls on shortcode examples, SEO text filters, native SEO variables for Rank Math and Yoast (including saved text shortcode configurations), and optional live refresh. The old [years-since] syntax is recognized only when available; existing content is not migrated or modified automatically.

The optional Divi 5 module selects saved shortcodes and offers local style overrides. A Pro edition may be considered in the future, but all features documented here are currently free.

== Installation ==

1. In Plugins > Add New Plugin, upload the ZIP.
2. Activate and open ALP Dynamic Time & Countdowns from its WordPress admin menu (shortened to ALP Dynamic Time & CD in the sidebar).
3. Copy a shortcode from the generator, a saved date or an example in About & guide. Insert it in a field that processes WordPress shortcodes. For Gutenberg, save a complete shortcode configuration first, then select it in the ALP Dynamic Time & Countdowns block.

== Privacy and stored data ==

Countdown fonts are served from your WordPress installation. Live text and recurring countdowns can request the site's own REST API. The plugin does not send this data to third parties or include analytics.

Saved dates are intended for public display. Anyone who knows a saved date identifier can request a calculation based on it through the public REST API. Use saved dates for information you intend to publish.

The plugin stores saved dates and named shortcode configurations in site options and Quick Colors palettes in user metadata. Deleting the plugin preserves this data for reinstallation.

== Frequently Asked Questions ==

= How do I show years since 2000? =

Use [alpdt_time from="2000" mode="calendar"] to display 26 years in 2026.

= Can it replace the Years Since - Timeless Texts plugin? =

ALP Dynamic Time & Countdowns was inspired by Years Since - Timeless Texts. After that plugin was closed in the WordPress.org directory on March 6, 2025, ALP Dynamic Time & Countdowns was developed independently to preserve support for its commonly used legacy shortcode forms, including [years-since] and [years-since-gb], and to add more features such as anniversaries, reusable dates, animated countdowns, Gutenberg and Divi 5 integration. These aliases are registered only when another plugin has not already registered them. The project is not affiliated with or endorsed by the original developers. Compatibility does not guarantee identical output in every case; back up your site and test existing pages before switching. The original listing is at https://wordpress.org/plugins/years-since/.

= What if I use Divi 5? =

Create a configuration in ALP Dynamic Time & Countdowns > Saved shortcodes. In Divi 5, insert ALP Dynamic Time & Countdowns, choose the saved shortcode in Content, and optionally adjust colors, label font size, font family and alignment in Design. The frontend renders the same saved configuration. This integration still requires validation in your installed Divi version.

= Can I insert the current year in the Divi 5 module? =

Create a Current year shortcode in Generator and save it with a name. In Divi select that saved shortcode. For additional fixed text such as © and your company name, include it in surrounding page content or a separate Divi Text module. The output uses your WordPress site timezone.

= How do I display a live countdown? =

Use [alpdt_time type="countdown" to="2027-01-01T18:30" countdown_display="full" clock_style="glow"]. The time uses the WordPress site timezone. "glow" is optional; accessibility preferences disable the animation.

= Can I adjust the look and size of the countdown? =

Yes. For custom or animated live clocks, choose Flip digits, Circular progress dial, Odometer, Neon Digital, Liquid, Pixel Matrix, Orbit, Cyber Glitch, Particle Morph or Hourglass and adjust number and label colors, card background, accent and track, circle diameter, typeface and sizes. Example: [alpdt_time type="countdown" to="2027-12-31T23:59" countdown_display="custom" clock_style="ring" clock_size="190" digit_color="#243665" accent_color="#2fc5b5"]. Use staging to check frontend and Divi output.

= Can fireworks play during a countdown? =

Yes. In Generator > Countdown > Style select Fireworks celebration, then choose Loop while counting down. The default remains Once when countdown ends for backward compatibility. Example: [alpdt_time type="countdown" to="2027-01-01T00:00" countdown_display="full" clock_style="celebration" celebration_mode="loop"]. Animation pauses off-screen and is disabled for reduced-motion users.

= How can I create a birthday or Halloween countdown? =

Use Celebration confetti & streamers for falling confetti and ribbons, with a completion-only or loop mode. Blood Dripping is a separate appearance with real text and decorative drops. Customize the colors and number/label size. The font chooser is hidden for Matrix, Morph and Blood when their saved shortcode is selected in Divi.

= How can I show only the current copyright year? =

Use [alpdt_time type="copyright" copyright_style="current"]. To show a range use [alpdt_time type="copyright" from="1999" copyright_style="range"]. Existing range shortcodes keep working.

= How do I use ALP Dynamic Time & Countdowns with Rank Math, Yoast or SEOPress? =

Enter a shortcode such as [alpdt_time type="year"] or [alpdt_time from="2000" mode="calendar" text="false"] in a supported SEO title or meta description field. ALP Dynamic Time & Countdowns expands the value in supported published output filters; preview/editor badges may not show the calculated number. Inspect the live source and clear caches as necessary. This does not change publication dates.

= Can I copy an SEO variable for any text? =

Current year has a registered variable immediately. Other non-countdown configurations can be saved in Saved Shortcodes to register a provider-specific native variable. Open the saved configuration to copy it for Rank Math or Yoast. Live countdowns cannot be embedded as SEO variables. Check published metadata; SEO-editor character counts may differ.

= How does the Gutenberg block work? =

Create and save a configuration in ALP Dynamic Time & Countdowns > Generator > Save shortcode. Insert the ALP Dynamic Time & Countdowns Gutenberg block and select that saved shortcode in the block's settings. Its full date, countdown, animation, typography and color options remain managed in the plugin dashboard, just like in the Divi 5 module. You do not need to configure Countdown target separately inside Gutenberg. Editing a saved shortcode updates all blocks using it.

= Is ALP Dynamic Time & Countdowns free? =

Yes. ALP Dynamic Time & Countdowns is free, including the features described here. An optional Pro version may be considered in the future, but no Pro version is currently available or required to use this plugin.

= What does YYYY-MM-DD mean? =

For example, 2000-12-15 means year 2000, month 12 (December) and day 15. The middle pair is always the month and the last pair always the day. The generator includes this explanation and examples.

= Is ALP Dynamic Time & Countdowns affiliated with Years Since - Timeless Texts? =

No. ALP Dynamic Time & Countdowns is an independent, inspired-by project, not an official continuation or an endorsed release. The plugin recognizes supported legacy shortcodes when another plugin has not already registered them.

= Can I save a countdown or shortcode and reuse it later? =

Yes. Configure it in Generator, select Save shortcode and enter a name and identifier. Find it under Saved shortcodes. Copy a reusable shortcode like [alpdt_time saved="new-year-countdown"] so edits to its saved configuration update all locations using that identifier. You can also copy the full independent shortcode. Deleting a saved entry leaves full shortcodes untouched but makes the reusable reference empty.

== Changelog ==

= 1.0.0-rc.3 =
* Defer the experimental custom-font uploader and its AJAX endpoint until a later release. No uploaded media attachments or site data are deleted.
* Keep the 95 large Black And White Picture and Rubik Scribble font files removed in rc.2.
* Keep third-party WordPress notices visible but relocate any inserted inside the ALP hero to a separate area above it. Do not hide security or SEO warnings.


= 1.0.0-rc.2 =
* Remove two large bundled font families and their CSS definitions.
* Add restricted administrator custom font uploads (WOFF2, WOFF, TTF, OTF) and a reusable font selector for countdowns. Uploaded fonts are stored in the WordPress Media Library.
* Keep saved shortcode, Divi 5, Gutenberg and plugin branding contracts unchanged.

= 1.0.0-rc.1 =
* Gutenberg: a single saved-shortcode selector, matching the Divi 5 content workflow. All advanced styling and dates remain in Generator. The public 1.0 Gutenberg block stores only a saved-shortcode identifier; experimental pre-release Gutenberg attributes are not migrated.
* Render selected saved shortcodes using the authoritative frontend shortcode renderer, including animated countdowns and conditional assets. Previously saved shortcode configurations and Divi module settings are unchanged.
* Release candidate version 1.0.0-rc.1. Not a final stable release or WordPress.org publication.

= 0.10.28-rc.3 =
* Clarify the Years Since - Timeless Texts inspiration, independent development and conditional legacy shortcode compatibility; remove the obsolete short-alias FAQ.
* Document that ALP Dynamic Time & Countdowns is free and a Pro edition is only a future possibility.
* Explain the Gutenberg Countdown target field and display it only for Countdown (days), with a clear example and accurate shortcode summary.
* Standardize public-facing references to the full ALP Dynamic Time & Countdowns name.

= 0.10.28-rc.2 =
* Serve countdown fonts locally across the generator, frontend and Divi.
* Register the Divi 5 module during the builder startup.
* Keep recurring countdowns and countdown completion actions working when live refresh is also enabled.
* Validate saved identifiers in current browsers and allow WordPress admin notices on the plugin screen.

= 0.10.9 =
* Show only controls that affect the selected clock appearance.
* Widen the countdown generator preview and introduce optional 0–80px padding (`clock_padding`).
* Restore Blood Dripping Creepster at 400 across all sizes and add a Blood-only Nosifer Regular 400 option. Fonts load from Google Fonts when selected; full WordPress/Divi runtime verification remains pending.


= 0.9.48 =
- Normalize and sanitize the legacy menu redirect request method.
- Document nonce scanner exceptions solely for read-only GET navigation; state-changing actions retain existing nonce and capability checks.

= 0.9.47 =
* Guard Divi preview registration and support plain permalinks in all REST previews.
* Remove unused preview data and avoid scanning all saved shortcodes for individual lookups.

= 0.9.40 =
* Confetti launch is staggered: right side first, left side 300ms later, with the same balloon movement and saved palettes.
* Removed the short `[alp_time]` alias as requested. Use `[alpdt_time]` in all shortcode content, stored configurations and supported SEO text. Independent Years Since compatibility aliases are retained.
* Real WordPress / Divi 5 tests are pending.

= 0.9.39 =
* Renamed the visible appearance option to Blood Dripping; saved `clock_style="blood"` values are preserved. Updated Spanish translations.
* Rebuilt Celebration Confetti with original, isolated Canvas 2D physics: a compact lower-right burst, small angular paper fragments and streamers, upward release, natural gravity and flutter. The provided image is static, so this is a close visual interpretation, not proven frame-for-frame equivalence.
* Preserved the six existing balloon animations, independently saved colors, 3/5/8-second duration, accessible timer text, reduced-motion behavior, and offscreen pause.
* Local PHP/JS/CSS checks and isolated Chromium animation tests passed. Real WordPress and Divi 5 Visual Builder testing remains pending.

= 0.9.33 =
* Halloween Blood: Creepster typeface is now requested for Blood only, with much shorter continuous falling drops that fade away, and no pauses in intermediate keyframes. Google Fonts remote dependency is temporary until the licensed font can be bundled locally.
* Celebration Confetti: four independent confetti colors and four independent balloon colors, each editable via the ALP color picker. New shortcodes can use black/gold/white for Black Friday; previously saved shortcodes keep their existing palette.
* Divi 5: Label Text Size now uses the native font size group, including unit picker, px, vw, rem, clamp() and global variables when available. Legacy numeric 10–32px values remain readable.
* Admin: shortcode label size range extended to 6–200px, without allowing arbitrary CSS text through shortcode params.
* WordPress/Divi visual verification and local Creepster self-hosting remain pending.

= 0.9.32 =
* Admin: a click on the HEX color value opens the existing ALP color popover; the native Divi color controls are configured for global color values.
* Divi 5: hide typography controls for saved Matrix, Morph and Blood clocks; keep the native typography group for other styles, trim extraneous font fields, add per-module label font size (10 to 32px), and align clocks according to native Sizing.
* Birthday: curved, constant-motion balloon trajectories and short, repeating particle cycles throughout the selected 3, 5 or 8-second celebration.
* Halloween: original nine-point liquid drips and self-contained SVG goo filters for numeric glyphs, no remote font dependency.
* The ALP-only Divi settings group filter attempts to hide third-party Line Hover Animation; actual extension compatibility must be verified in Divi.
* Source-level tests executed; live Divi 5 Visual Builder, responsive and accessibility testing is still required.

= 0.9.31 =
* Divi 5 local color pickers resolve Global Colors references through official Divi 5 APIs in editor and PHP frontend rendering.
* Replaced separate text size/font/alignment dropdown controls with a native Divi typography group; legacy saved attributes retained for compatibility. Live Divi 5 validation remains pending.
* Liquid surface and fill use the same color and transition, preventing a visible gap as the level changes.
* Celebration Confetti now launches paper bursts, short ribbons and floating balloons from multiple points. Reduced-motion safeguards remain active.

= 0.9.30 =
* Optional looping Fireworks, birthday confetti with balloons, Halloween Blood Dripping text, and contextual typography controls.
* Preserves legacy completion-only countdown behavior and saved shortcode identifiers.

= 0.9.14 =
Editable per-user Quick Colors palette: five defaults, add (+) and remove (×) with confirmation; unchanged countdown styles.


= 0.9.13 =
* Added Divi-inspired, responsive color picker with large color square, hue and opacity sliders, HEX input and ALP quick colors.
* Supports 6- and 8-digit HEX colors, including opacity, without depending on Divi global-color APIs.
* Preserved saved dates, shortcodes and Divi module identifiers.


= 0.9.5 =
* Admin ALP dropdown with keyboard-friendly options, expanded About & guide and explicit ISO date explanation.
* Independent legacy compatibility disclosure and SEO shortcode guidance.
* Visual countdown flip/rolling animations, new radial dial, real motion on digit changes, dual-speed hero hands.
* Divi 5 dedicated animation group and REST-backed numeric preview (experimental; real Divi builder testing still required).


= 0.9.4 =
* Correct ALP Plugins authorship and Settings link. Custom clock units and number animations. Single Divi Layout decoration.

= 0.9.3 =
* Added date-and-time countdown with seconds, accessible subtle ticking and optional animated glow; retained legacy days-only behavior.
* Added separate current-year-only and start-year-to-current-year copyright modes.
* Replaced the technical date-format field with friendly presets and clarified the order of year, month and day.
* Added green check feedback for successful clipboard copying, consistent native dropdown styling, safer delete confirmation and animated ALP hero with reduced-motion support.



= 0.9.2 =
* Divi 5: corrected the unlabeled Content group and added controls for every Generator output type (elapsed, current year/month/date, countdown, copyright range, anniversary number/message).
* Added optional surrounding text for footer copyright and kept existing date/caption attribute paths. Live Divi 5 verification is pending.


= 0.9.1 =
* Redesigned ALP admin dashboard and generator guidance. Added saved-date editing and red delete confirmations. Preserves stored identifiers and legacy shortcode handling.

= 0.9.0 =
* Initial development candidate: source implementation, static tests and installation package.
