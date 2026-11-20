# Changelog

## 1.0.8 · Official WordPress.org slug alignment (2026-11-20)

- WordPress.org's automated upload scanner explicitly requires text domain `alp-dynamic-time-countdowns`, unlike the prior locally checked private slug `alp-dynamic-time`. Use the assigned official slug in the main PHP header, all runtime PHP translation calls, Gutenberg translation calls and the WordPress text-domain registry path.
- Rename bundled `es_CR` and `es_ES` `.po` / `.mo` catalog files to use the official slug, preserving translated messages.
- Package under the canonical WordPress.org directory `alp-dynamic-time-countdowns/` rather than the previous private distribution directory `alp-dynamic-time/`.
- Keep the main plugin PHP filename, `ALPDT_` APIs, plugin settings, saved dates and shortcodes, data model, Divi 5 integrations, styles and non-translation behavior unchanged.
- **Migration note:** changing the directory slug will not automatically replace installations under the older private folder. Deactivate the older copy, back up and migrate deliberately; do not run both copies concurrently.
- Scope-limited checks performed locally; actual WordPress.org rescanning and live WordPress/Divi smoke tests remain pending.


## 1.0.7 · Packaging & Plugin Check correction (2026-11-20)

- Package the complete plugin with a single, canonical `alp-dynamic-time/` directory instead of placing loose plugin files at the archive root. The stable path enables future WordPress upgrades to target the correct plugin directory.
- Align the distribution folder slug with the already-correct `Text Domain: alp-dynamic-time` and existing translation calls, resolving the common cause behind 525 translation-domain errors and one warning in the uploaded 1.0.6 Plugin Check report.
- Do not alter existing dates, WordPress options, color preferences, shortcodes, CSS/JS, Gutenberg or Divi 5 behavior.
- Pre-existing duplicate installations stored under other folder names require manual deactivation/removal after backup. The package does not delete or migrate folders automatically.


## 1.0.6 · Compact color picker (2026-11-20)

- Reduce the palette width, color field, preview swatch, spacing, and Quick Colors swatches to compact 26×26 px tiles, preserving minimum target size.
- Preserve viewport collision handling, internal scrolling, HEX/opacity controls, keyboard use, and saved color data.

## 1.0.4 · Color picker viewport positioning (2026-11-20)

- Fix the ALP color picker being cut off by the top toolbar or overlapping the left WordPress admin menu.
- Use viewport-anchored positioning and a bounded, internally scrollable panel at desktop, tablet and mobile sizes.
- Preserve HEX, opacity, Quick Colors, Escape and color selection behavior, without changing stored settings.

## 1.0.3 · Hourglass sand animation (2026-11-20)

- Sand grains now fall at their natural speed for all time units; days, hours and minutes display progressively fewer grains than seconds without slow-motion drops.
- Preserve the timer values, sand-bed progression, accessible reduced-motion behavior, shortcodes and saved settings.

## 1.0.2 · Maintenance update (2026-11-20)

- Update SEO-focused public descriptions; remove references to hypothetical future paid versions. The plugin remains free.
- Refine keyboard-focus styling on Generator/tabs without the intrusive turquoise outline; preserve accessible focus indication.
- Slow Blood Dripping droplet travel (3.0–4.7 seconds) and stagger phases.
- Randomize celebration balloon spatial lanes, colors and launch delays, preserving original curved trajectories and configurable balloon count.
- Preserve existing shortcode identifiers, saved dates, Gutenberg/Divi module contracts, fonts, branding and settings.


## 1.0.0 · First stable release (2026-11-20)

Initial public baseline of **ALP Dynamic Time & Countdowns**.

### Features

- Dynamic dates, anniversaries, copyright years, recurring dates and saved milestones.
- **20 selectable countdown designs**, configurable animations, colors, typography, spacing and layouts.
- Named saved shortcodes, with a shortcode generator and copy controls.
- Gutenberg block and Divi 5 module using saved shortcode selections.
- SEO shortcode output support and native variables for supported Rank Math/Yoast workflows.
- Optional compatibility aliases for Years Since – Timeless Texts when available.

### Main refinements

- Simplified Gutenberg and Divi workflows, using the same saved-shortcode library.
- Reduced bundled size by removing the heavy Black And White Picture and Rubik Scribble font packages.
- Consistent, accessible two-second confirmation after copying a shortcode or variable.
- Third-party administrative notices stay visible outside the ALP hero, including SEO/security notices.
- Responsive ALP dashboard and official branded footer.

**This is the first stable version.** Earlier development/RC notes are retained in Git history rather than shown as public release changes. Live WordPress/Divi end-to-end testing remains pending.
