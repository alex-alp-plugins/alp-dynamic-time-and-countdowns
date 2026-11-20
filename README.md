# ALP Dynamic Time & Countdowns

**A new, free WordPress plugin for dynamic dates, anniversaries and countdowns.** Build reusable time-based content without manually updating your pages every year or after every promotion.

## Features

- **20 countdown designs:** Minimal (Inline Text); Soft Glow; Flip Digits; Sliding Numbers; Gentle Pulse; Animated Underline; Circular Progress Dial; 3D Odometer; Neon Digital; Liquid; Pixel Matrix; Orbit Motion; Cyber Glitch; Particle Morph; Hourglass; Snowfall; Christmas Lights; Fireworks Celebration; Celebration Confetti & Streamers; and Blood Dripping. Customize colors, typography, units, layout, and spacing.
- **Dynamic dates and anniversary text** for elapsed years, the current year/month, recurring events, smart dates and copyright ranges.
- **Reusable dates and saved shortcodes:** configure a countdown or date once, then reuse and update it across your site.
- **Gutenberg block** and optional **Divi 5 module**: select a saved shortcode, with the detailed configuration managed in the plugin dashboard.
- **SEO support:** shortcode output in supported Rank Math, Yoast SEO and SEOPress fields, plus native Rank Math/Yoast variables where applicable.
- **Seasonal campaigns:** build countdowns for Black Friday, Cyber Monday, Christmas, New Year, launches and other events.
- **WordPress-friendly:** self-hosted bundled fonts, local assets, localized English/Spanish UI, and respect for reduced-motion settings.

All features in this first release are **FREE**. A Pro edition may be considered in the future, but none is required or available today.

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

Saved dates and shortcode configurations are stored in WordPress site options; per-user Quick Colors are stored in user metadata. Public date calculations are available through the site's REST API, so saved dates should be information intended for public display. The plugin does not include analytics or transmit countdown data to a third-party service. See [`readme.txt`](readme.txt) for details and frequently asked questions.

## Version 1.0.0

This is the **first stable baseline** of **ALP Dynamic Time & Countdowns**. Key improvements in the release include the single-selector Gutenberg/Divi workflow, accessible copy confirmations, streamlined bundled fonts and safeguards that keep third-party WordPress admin notices outside the ALP hero.

See [`CHANGELOG.md`](CHANGELOG.md) for the short release summary and [`docs/FONTS.md`](docs/FONTS.md) for bundled font licenses. The detailed pre-1.0 development history remains available in the repository's Git commit history; this README intentionally focuses on the public release.

**Testing note:** static syntax and isolated browser checks were performed on earlier candidates. A complete live WordPress/Divi 5 compatibility test is not yet documented; please test in staging before use on a critical production site.

## License

GPL-2.0-or-later. © ALP Plugins · [alpplugins.com](https://alpplugins.com)
