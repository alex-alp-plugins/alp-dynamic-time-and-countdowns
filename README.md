# ⏳ ALP Dynamic Time & Countdowns

**Make every second count. Bring dynamic dates, animated countdowns, and memorable milestones to your WordPress website.**

**ALP Dynamic Time & Countdowns** is a free, flexible WordPress plugin that lets you create animated countdown timers, automatically updating dates, anniversary counters, and reusable time-based content, all from one intuitive dashboard.

Whether you're counting down to a product launch, celebrating an anniversary, or keeping your copyright year up to date, *your website stays in sync with time.*

**✨ 20 countdown designs. Powerful customization. Endless creative possibilities.**

## ✨ Key Features

- **20 animated countdown designs:** Minimal (Inline Text), Soft Glow, Flip Digits, Sliding Numbers, Gentle Pulse, Animated Underline, Circular Progress Dial, 3D Odometer, Neon Digital, Liquid, Pixel Matrix, Orbit Motion, Cyber Glitch, Particle Morph, Hourglass, Snowfall, Christmas Lights, Fireworks Celebration, Celebration Confetti & Streamers, and Blood Dripping.
- **Dynamic dates:** Automatically display the current year, month, formatted dates, and copyright year ranges.
- **Anniversaries and milestones:** Calculate elapsed years, celebrate special dates, and display personalized anniversary messages.
- **Reusable shortcodes:** Create once, save a configuration, and display it throughout your website.
- **Gutenberg and Divi 5 integration:** Insert saved countdowns and dynamic dates with the dedicated block or optional Divi 5 module.
- **SEO plugin compatibility:** Dynamic date output in supported Rank Math, Yoast SEO, and selected SEOPress fields, plus native variables in supported Rank Math/Yoast contexts.
- **Advanced styling:** Customize supported colors, typography, layouts, animations, and time-unit labels.
- **Recurring events:** Create countdowns for birthdays, holidays, seasonal promotions, and annual events.
- **Responsive and accessibility-conscious:** Adaptable layouts, locally bundled resources, and reduced-motion support.

## 🎬 Interactive Demos Coming Soon!

We're preparing **live interactive demos** so you can explore the countdown designs, animations, customization options, and dynamic date features before installing the plugin.

*Stay tuned! More examples, creative inspiration, and interactive previews are on the way.*

**All features included in version 1.0.12 are available free of charge.**

## Identifier format

For reusable saved dates and shortcode configurations, enter an identifier with lowercase letters, numbers, hyphens (`-`) or underscores (`_`), without spaces. For example, `alex-ingreso`. The dashboard automatically changes spaces to hyphens as you type. Existing saved identifiers stay unchanged when editing.

## Quick start

1. Download the plugin ZIP from this repository and install it using **WordPress → Plugins → Add New Plugin → Upload Plugin**.
2. Activate **ALP Dynamic Time & Countdowns** and open its dashboard (the admin sidebar label is shortened to **ALP Dynamic Time & CD**).
3. Use **Generator** to build a dynamic date or countdown, and copy the generated shortcode to any shortcode-enabled area.
4. For reuse, **save the shortcode** in the Generator. Then insert the **ALP Dynamic Time & Countdowns** Gutenberg block or Divi 5 module and select the saved configuration.

Example countdown (WordPress site timezone):

```text
[alpdt_time type="countdown" to="2027-12-31T23:59" countdown_display="full" clock_style="glow"]
```

Example saved configuration:

```text
[alpdt_time saved="new-year-countdown"]
```

Example dynamic year:

```text
[alpdt_time type="year"]
```

## Requirements

- WordPress **6.5 or later**
- PHP **8.2 or later** (use a security-patched runtime)
- Divi **5** only if you want the optional Divi module; not required for shortcodes or Gutenberg

## Compatibility and privacy

**ALP Dynamic Time & Countdowns** was inspired by *Years Since – Timeless Texts*. It independently supports the legacy `[years-since]` and `[years-since-gb]` forms **when those shortcode names are not registered by another plugin**. Compatibility is not an endorsement by the original developers or a promise of identical output in every case.

Saved dates and shortcode configurations are stored in WordPress site options; per-user Quick Colors are stored in user metadata. Saved palettes can be exported or erased with WordPress built-in privacy tools. Uninstall removes only Quick Colors user metadata; saved dates and named shortcodes intentionally remain. The plugin offers suggested privacy policy wording in the WordPress privacy policy editor. Public date calculations are available through the site's REST API, so saved dates should be information intended for public display. The plugin does not include analytics or transmit countdown data to a third-party service. See [`readme.txt`](readme.txt) for details and frequently asked questions.

## Version 1.0.12 · Privacy support and compact production assets

- Adds WordPress's native personal-data exporter and eraser for the per-user Quick Colors palette and privacy policy guidance for site administrators.
- Uninstall removes only the `alpdt_quick_colors` user-meta key; does not delete site options or saved dates/shortcodes.
- Adds minified CSS/JS builds, used in production; readable versions remain in the package and are selected by `SCRIPT_DEBUG`.
- WordPress.org public ZIP omits the internal `docs/` directory; developer QA documentation remains in the separate source/development archive.
- WordPress.org contributor `alexalp` verified by the account profile; `Author URI` remains intentionally absent after the review feedback.

## Version 1.0.11 · Shortcode output safety

- Enforce final `wp_kses()` sanitization on the public `[alpdt_time]` and `[years-since]` HTML callbacks; allow only the ALP timer elements, associated data attributes, safe inline CSS variables, and circular SVG markup.
- Use the same final safety boundary for saved `[alpdt_time saved="..."]` configurations.
- Escape only newly inserted ALP countdown text in widget/SEO filters, without re-sanitizing unrelated widget markup.
- Preserve saved dates/shortcodes, all countdown designs, translations via WordPress.org, WordPress options, Gutenberg, Divi 5 and frontend JavaScript/CSS.
- Developer QA report `QA-1.0.11.md` is retained only in the development archive for verified checks and unresolved testing.

## Version 1.0.10 · WordPress.org review updates

- Remove the broken `Author URI` and the corresponding dead link from the dashboard footer while preserving the official ALP Plugins logo and author credit.
- Remove bundled `.po`/`.mo` files and the custom language registry override; translations can be managed by the WordPress.org community. The prior Spanish catalogs are retained in version 1.0.9 as a local backup, not in the official submission ZIP. Spanish strings will appear once language packs are available/approved.
- Sanitize expanded HTML returned by the legacy `[years-since-gb]` wrapper using WordPress KSES; permit only the simple SVG and circle attributes needed for the ALP ring countdown.
- Add WordPress's JavaScript translation registration for Gutenberg.
- Preserve settings, shortcodes, blocks, Divi module identifiers, CSS animations, fonts, and WordPress option keys. Developer QA report `QA-1.0.10.md` is retained only in the development archive.

## Version 1.0.8 · WordPress.org submission compatibility

WordPress.org assigned the slug **`alp-dynamic-time-countdowns`**. Version 1.0.8 aligns the PHP, Gutenberg translation calls and header with that official slug, and renames the bundled Spanish `.po`/`.mo` files to keep both Spanish locales working. The distributable ZIP has one canonical top-level `alp-dynamic-time-countdowns/` folder.

The main PHP filename, `ALPDT_` namespace, database options, saved dates, saved shortcodes, CSS, visual controls and Divi module identifiers are intentionally unchanged. The full list of changes is in [`CHANGELOG.md`](CHANGELOG.md), and its scoped static QA is retained in the development archive.

**Migrating an existing private installation:** WordPress.org uses a different folder than the earlier `alp-dynamic-time/` private ZIP. Back up the site first, deactivate the older plugin and verify it is no longer active before installing the WordPress.org-slug version. The package does not automatically uninstall or rename the old directory. Avoid activating both copies, since they register the same PHP classes and WordPress hooks. Verify saved data and shortcode output on staging, then remove only the obsolete folder when safe.

**Testing note:** Syntax, archive, domain-consistency and translation-catalog checks were executed locally. WordPress.org's new upload scanner, Plugin Check on a local WordPress installation, visual regression in Divi 5 and real installation/migration checks have not been executed in this environment and remain **pending**.

## License

GPL-2.0-or-later. © ALP Plugins.
