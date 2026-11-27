# Changelog

## 1.0.27 · WordPress i18n compliance (2026-11-26)

- Add the missing `translators:` note immediately before the `removeQuickPreset` localized string, explaining `%s` as a saved Quick Colors preset label.
- Keep all existing application behavior, shortcode formats, assets, settings, user data, WordPress/Divi 5 interfaces and saved gradients unchanged.
- WordPress live tests, official Plugin Check, and WPCS remain pending.

## 1.0.26 · Border radius controls and Liquid gradients (2026-11-26)

- Match the established paired padding layout for radius corners, with independent top and bottom linking controls. Keep existing global and per-corner stored attributes intact.
- Keep Liquid circle gradients visible beneath animated liquid overlays, only when the gradient targets individual cards. Solid colors and other designs keep their previous presentation.
- Preserve existing shortcodes, saved colors, frontend timers, and Divi/Gutenberg integrations.


## 1.0.25 · Flip mask, Odometer padding and contextual spacing help (2026-11-26)

- Remove the color picker's unnecessary divider and set the editable Angle box to 50px without dropping three-digit support.
- Replace 3D-projected half rotation with an accessible two-stage masked flip: the card's rounded boundary stays fixed during the transition and respects reduced motion.
- Correct Odometer drum inner viewport: apply the four side paddings to each drum, not inter-wheel gaps.
- Preserve Liquid's requested spacing between circular dials, with accurate design-specific descriptions.
- Display the Christmas Lights width note only for Christmas Lights; show context-appropriate card padding explanations for Odometer, Flip, ring, liquid, orbit, snowfall, and Christmas Lights.
- Versioned new admin and frontend stylesheets to avoid stale CSS on plugin updates; saved data, public shortcode attributes, and other countdown designs remain unchanged.
- WordPress/Divi 5 live tests and official Plugin Check remain pending.

## 1.0.24 · Spacing and animation corrections (2026-11-26)

- Correct number card padding in 3D Odometer and Liquid within the number card without adding space between cards.
- Expose validated four-corner radius controls, preserving `clock_radius` compatibility in older shortcodes and frontend CSS.
- Reorder Particle Morph immediately after Pixel Matrix.
- Compact the gradient stop section and allow three-digit angle values.
- Strengthen clipping of Flip Digits tiles throughout animation.
- WordPress/Divi live tests and official Plugin Check are pending.

## 1.0.22 · Live preview, gradient palettes, and flip card corners (2026-11-26)

- Remove the original Soft Glow mint backdrop when a user-defined gradient is active; retain the original look for default colors.
- Tighten the editable angle field and separate Start/End labels from their HEX values in the color picker.
- Save full validated linear/radial gradient presets alongside existing HEX Quick Colors, with backwards-compatible user-meta storage and WordPress privacy tooling. Gradient swatches are reusable on compatible background fields only.
- Round both animated halves of Flip Digits, including transient hinged flaps, without clipping their rotation.
- Keep shortcode settings, saved dates, Divi/Gutenberg contracts and all other countdown designs unchanged.
- Live WordPress/Divi and official Plugin Check remain pending.


## 1.0.21 · Solid Soft Glow preview and editable gradient angle (2026-11-26)

- Stop the original pastel Soft Glow preview wrapper from showing behind an explicitly selected solid HEX; let the chosen solid fill the countdown preview without the old outer mint gutter. Preserve the original pastel effect when the background is left at its style default.
- Replace the read-only angle badge with a compact editable numeric input (0–360°), synchronized in both directions with the slider and the existing gradient setting, including temporary invalid-input handling.
- Introduce `color-picker-ui-1021.css` and enqueue it after the existing plugin styles to avoid stale copies from previous versions.
- Preserve saved shortcodes, existing data/options, Gutenberg/Divi 5 integrations and the other countdown styles. Live WordPress/Divi and official Plugin Check remain pending.

## 1.0.20 · Cache-proof Color Picker presentation and compact angle (2026-11-26)

- Add a dedicated `color-picker-ui-1020.css` stylesheet after the existing admin and countdown styles, using a distinct asset path and version to avoid stale cached presentations without purging unrelated WordPress/browser/optimization caches.
- Increase style specificity for Solid/Gradient, Linear/Radial, Start/End and reset buttons so WordPress administrative button rules do not override the ALP segmented design.
- Place the Angle label, interactive slider and live degree value in one compact row (32px tested in an isolated browser fixture); retain 0–360 degree setting and its existing serialized shortcode/hidden fields.
- Keep HEX/opacity/Quick Colors, the existing saved settings, modal keyboard controls, and immediate gradient preview intact.
- PHP/JS/CSS static and isolated Chromium fixture tests executed; full WordPress/Divi 5 live regression and official Plugin Check remain pending.

## 1.0.19 · Color Picker modal polish (2026-11-26)

- Refresh the plugin-owned Color Picker popup with a coherent dark ALP design inspired by the supplied solid/gradient references: segmented mode buttons, selectable Start/End color swatches, a gradient strip, a more spacious canvas, balanced HEX/opacity fields and grouped Linear/Radial/Angle controls.
- Style all popup controls explicitly rather than exposing browser/WordPress default button treatments. Keep keyboard focus, existing HEX and alpha controls, Quick Colors management, gradient state and saved shortcode output intact.
- Scoped frontend/admin behavior only to the picker; PHP color validation, shortcode schema, options and Divi/Gutenberg integration are not modified.
- WordPress/Divi live interaction and official Plugin Check remain pending.

## 1.0.17 · Review Checker, font sizing and gradient picker (2026-11-26)

- Scope shortcode KSES custom CSS validation to a separate callback; retain the final HTML sanitization boundary while avoiding misleading render-return scan findings.
- Offer native numeric steppers for Number size and Label size, switching to a text expression only for clamp() sizing.
- Improve gradient picker controls, accessible pressed states, stop selection, angle controls and responsive layout.
- Synchronize the gradient immediately with the live preview, independent of the REST-date refresh.
- Exclude the hidden distribution-only .distignore file from the installable ZIP (the source build rules are retained).
- Preserve saved dates, shortcode identifiers and attributes, twenty countdown designs, Gutenberg and Divi 5 integrations.
- Live WordPress/Divi testing, Plugin Check and ALP Review Checker rerun remain pending.


## 1.0.16 · Plugin Check / ALP Review compliance, integrated color-picker gradients & UI polish (2026-11-26)

- Rename root PSR-4 namespace from `ALP\DynamicTime` to `AlpDynamicTime` across `composer.json`, `Autoloader`, `src/`, `alp-dynamic-time.php`, and `includes/legacy-aliases.php`.
- Replace `extract()` in `AdminView::partial()` with scoped closure rendering across all 6 admin views, escape `$id` in `Container::get()`, and escape dynamic class/role/data attributes in `dashboard.php` and `tab-shortcodes.php`.
- Restore styled native `<input type="number">` controls for `Label size` (matching `Padding bottom` and keeping `clamp()` text mode).
- Integrate `Solid` / `Gradient` tabs, Start/End color stops, Linear/Radial toggle, Angle slider/input, and Soft Glow `"Use style default"` reset inside the background Color Picker popovers (`glow_background_color`, `card_color`, `panel_color`); fix countdown container/card gradient CSS (`countdown.css`) and preserve `--alpdt-*` CSS custom properties across WordPress `safecss_filter_attr_allow_css` during `[alpdt_time]` `wp_kses()` filtering.
- Center the footer 5-point SVG review star optically and vertically with the review link label.

## 1.0.15 · Feedback notice cleanup & footer star vertical alignment (2026-11-26)

- Remove the `×` close button from auto-dismissing dashboard confirmation notices in `dashboard.php`, `admin.css`, and `admin.js`.
- Center the yellow 5-point SVG review star geometrically and vertically with the review link in the dashboard footer, and harden footer link CSS against wp-admin link/alignment overrides.
- Prefix `AssetManager::asset_version()` with `ALPDT_VERSION` (`ALPDT_VERSION . '-' . $mtime`) so upgraded releases always invalidate cached `admin.min.css` and `admin.min.js`.

## 1.0.14 · UX refinements, typography defaults & gradient backgrounds (2026-11-26)

- Add the official WordPress.org review link (`https://wordpress.org/support/plugin/alp-dynamic-time-countdowns/reviews/#new-post`) to the dashboard footer review prompt while retaining the ALP 3-zone footer layout.
- Change the default countdown `Number size` from `60px` to `50px` for all styles except `Blood Dripping`, which remains `60px`.
- Enable `Font family` selection for unit labels (`Days`, `Hours`, `Minutes`, `Seconds`) in `Blood Dripping` while keeping `Creepster`/`Nosifer` locked on the numerals.
- Add accessible up/down stepper arrows and keyboard stepping to the `Label size` control.
- Auto-dismiss admin confirmation banners (e.g. `"Date saved. You can reuse it anywhere on your site."`) after 4.5s (8s for errors) with hover/focus pause and a close button, and clean `alpdt_message` from the browser URL.
- Add `linear` and `radial` background gradient support (`bg_gradient_type`, `bg_gradient_start`, `bg_gradient_end`, `bg_gradient_angle`, `bg_gradient_target`) safely using CSS custom properties validated by `ShortcodeValidator` and `wp_kses()`.

## 1.0.13 · Security hardening, Clean Architecture PSR-4 & performance optimization (2026-11-26)

- Refactor PHP codebase to PSR-4 Clean Architecture (`src/Domain`, `src/Infrastructure`, `src/Presentation`) with PHP 8.2+ typing, a lightweight lazy DI container, centralized hook registration, and backward-compatible `ALPDT_*` facades.
- Restrict `AdminController` loading strictly behind `is_admin()`, split admin HTML into modular templates, and harden WordPress hook boundary signatures against strict-type mismatches.
- Harden REST API endpoints (`/alpdt/v1/divi-preview` and `/alpdt/v1/render`) with full schema `args` validation and `manage_options` gating for preview overrides; replace `preg_replace()` with `preg_replace_callback()` in `[alpdt_time]` to prevent PCRE backreference injection.
- Harden JavaScript (`divi5/builder.js`, `assets/countdown.js`, `assets/live.js`, `assets/admin.js` and `.min.js` counterparts) with strict same-origin `fetch()` checks, client-side `DOMParser` HTML allowlist sanitization for Divi preview, safe `createElementNS` SVG construction, and CSS selector escaping.
- Optimize runtime performance: defer Gutenberg editor data to `enqueue_block_editor_assets`, memoize saved shortcodes/presets per request, conditionally enqueue `divi5/module.css`, cache legacy post scans in a 5-minute transient, use `filemtime()` versioning and `defer` strategy, and remove unreferenced vertical SVG assets.
- Preserve exact folder slug (`alp-dynamic-time-countdowns/`), main file (`alp-dynamic-time.php`), options, shortcodes, Gutenberg block, and Divi 5 module identifiers for clean in-place upgrades.

## 1.0.12 · WordPress.org privacy and distribution hardening (2026-11-23)

- Integrate the saved Quick Colors per-user palette with WordPress Core personal-data export and erasure workflows. Offer truthful suggested privacy-policy disclosure in the WordPress policy editor.
- Clear only plugin-specific `alpdt_quick_colors` user metadata when the plugin is uninstalled; preserve existing site-wide dates, named shortcodes and their WordPress options, along with users and other metadata.
- Keep original readable JS/CSS together with compact `.min` builds. Load `.min` in normal operation and readable sources with `SCRIPT_DEBUG` so the source is inspectable and the exact same script handles and dependencies remain intact.
- Add verified WordPress.org contributor `alexalp`; public package excludes internal `docs/` development QA and design files. No broken `Author URI`/`Plugin URI` is reintroduced.
- No shortcode or Divi 5 API changes, frontend redesign, or data migration. Live WordPress/Divi testing and official Plugin Check remain pending.


## 1.0.11 · Final shortcode HTML safety boundaries (2026-11-23)

- WordPress.org/ALP Review Checker: add a final `wp_kses()` boundary to the main `[alpdt_time]` rich-HTML callback. The explicit ALP-owned element/attribute allowlist includes all current countdown `data-alpdt-*` hooks, the ring `svg`/`circle` nodes, safe CSS custom properties (`style`), accessible timer labels and the seven supported wrapper tags. Unsupported HTML/event attributes are removed, and WordPress KSES filters CSS values.
- Resolve saved shortcode attributes in the same `main()` render pass to avoid an indirect return with an unverified escaping boundary.
- Sanitize the full legacy `[years-since]` output through a fixed wrapper-tag KSES allowlist, preserving calculated text and legacy supported HTML tags.
- Escape only the *replacement* from `ALPDT_Integrations::replace()` using `esc_html( sanitize_text_field(...) )`. The unchanged input filter value and its surrounding HTML deliberately pass through, as needed for WordPress widgets and SEO integrations; changing unrelated content would cause regressions. Review Checker may still flag a flow-analysis warning on `preg_replace_callback()` because it cannot prove that distinction.
- Keep the canonical public slug, language-pack approach, existing saved data, twenty clock styles, Divi/Gutenberg modules, frontend JS/CSS assets, and shortcode names without migrations.
- Local syntax/static checks do not constitute a full WordPress.org audit or live functional verification.

## 1.0.10 · WordPress.org review and output escaping (2026-11-23)

- Remove nonworking `Author URI: https://alpplugins.com` from the main plugin header; preserve `Author: ALP Plugins`.
- Keep the official ALP Plugins footer logo but render it without a broken external destination while the website returns 404; remove the stale link in the README.
- Exclude the `.po` and `.mo` Spanish catalogs from the public WordPress.org ZIP. They remain available in the previous private 1.0.9 archive. Remove the obsolete WordPress text-domain custom-path override and opt into Gutenberg script translations via `wp_set_script_translations()`.
- Properly escape legacy `[years-since-gb]` wrapper HTML **after** nested shortcode expansion with a WordPress `wp_kses()` allowlist. Retain ALP ring SVG/circle support without permitting arbitrary SVG scripts, HTML event handlers or links.
- Preserve shortcode APIs, frontend countdown effects, saved dates/identifiers, WordPress options, admin experience and Divi/Gutenberg identifiers. Local static tests do not replace WordPress.org or real runtime checks.


## 1.0.9 · Clear identifier input and guidance (2026-11-20)

- Normalize identifiers while typing in both Saved dates and saved-shortcode dialogs: spaces become hyphens, uppercase becomes lowercase, and accented letters are normalized.
- Keep the insertion point stable while normalizing; preserve trailing hyphens and skip incomplete IME composition to avoid disrupting input.
- Add visible, screen-reader-associated identifier format instructions and an `alex-ingreso` example in both fields, translated for Spanish (Costa Rica and Spain).
- Preserve existing saved identifiers, shortcodes, options, PHP server validation, WordPress and Divi integration and the official WordPress.org slug.
- Static syntax and isolated behavior checks only; full live WordPress/Divi verification and WordPress.org scan remain pending.


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
