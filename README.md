# ALP Dynamic Time & Countdowns

**ALP Dynamic Time & Countdowns is a free WordPress countdown timer and dynamic dates plugin** with 20 customizable countdown designs, anniversary counters, reusable shortcodes, a Gutenberg block and Divi 5 module. Create automatic date displays and event countdowns without manually updating your pages.

## Features

- **20 countdown designs:** Minimal (Inline Text); Soft Glow; Flip Digits; Sliding Numbers; Gentle Pulse; Animated Underline; Circular Progress Dial; 3D Odometer; Neon Digital; Liquid; Pixel Matrix; Orbit Motion; Cyber Glitch; Particle Morph; Hourglass; Snowfall; Christmas Lights; Fireworks Celebration; Celebration Confetti & Streamers; and Blood Dripping. Customize colors, typography, units, layout, and spacing.
- **Dynamic dates and anniversary text** for elapsed years, the current year/month, recurring events, smart dates and copyright ranges.
- **Reusable dates and saved shortcodes:** configure a countdown or date once, then reuse and update it across your site.
- **Gutenberg block** and optional **Divi 5 module**: select a saved shortcode, with the detailed configuration managed in the plugin dashboard.
- **SEO support:** shortcode output in supported Rank Math, Yoast SEO and SEOPress fields, plus native Rank Math/Yoast variables where applicable.
- **Seasonal campaigns:** build countdowns for Black Friday, Cyber Monday, Christmas, New Year, launches and other events.
- **Years Since migration:** preserve legacy `[years-since]` and `[years-since-gb]` shortcode aliases when they are not already registered by another plugin.
- **WordPress-friendly:** self-hosted bundled fonts, local assets, internationalized strings ready for WordPress.org community language packs, and reduced-motion support.

All features currently included in this free release are available at no charge.

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

## Migration from Years Since – Timeless Texts

**Years Since – Timeless Texts was closed in the WordPress.org plugin directory.** ALP Dynamic Time & Countdowns supports the familiar `[years-since]` and `[years-since-gb]` shortcode forms, allowing sites with legacy shortcodes to migrate without manually replacing each instance. Those aliases are registered **only if no other plugin has already registered them**. Behavior may differ; test important pages after migration. This plugin is developed independently and is not affiliated with or endorsed by the original developers.

## Compatibility and privacy

Saved dates and shortcode configurations are stored in WordPress site options; per-user Quick Colors are stored in user metadata. Saved palettes can be exported or erased with WordPress built-in privacy tools. Uninstall removes only Quick Colors user metadata; saved dates and named shortcodes intentionally remain. The plugin offers suggested privacy policy wording in the WordPress privacy policy editor. Public date calculations are available through the site's REST API, so saved dates should be information intended for public display. The plugin does not include analytics or transmit countdown data to a third-party service. See [`readme.txt`](readme.txt) for details and frequently asked questions.

## Version history

For release-by-release changes and technical fixes, see the [Changelog](CHANGELOG.md).

## License

GPL-2.0-or-later. © ALP Plugins.
