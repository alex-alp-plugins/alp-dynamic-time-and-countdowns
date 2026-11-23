# ALP Dynamic Time & Countdowns 1.0.10 | WordPress.org pre-submission

**Baseline:** supplied complete v1.0.9 ZIP, canonical WordPress.org directory.
**Reported warnings:** `Author URI` 404, bundled locale files, unescaped nested shortcode wrapper.

## Scoped modifications
1. Remove the invalid URL from the main plugin Author URI, dashboard footer, and README. Retain ALP's official footer SVG as non-interactive branding.
2. Remove `.po`/`.mo` Spanish translation catalogs **only from this distribution**. The v1.0.9 ZIP retains them as backup. Use WordPress.org language packs and `wp_set_script_translations()` for Gutenberg; do not set a custom locale path. PHP gettext calls retain their official domain.
3. Escape **after** expanding `[years-since-gb]` nested shortcodes via `wp_kses( do_shortcode( ... ), $allowed_html )`; only HTML post tags and the subset of SVG/circle attributes actually used by the clock are allowed.
4. Bump header/constant/stable-tag/docs to 1.0.10 without data or API migrations.

## Behaviors requiring live smoke testing
- Legacy `[years-since-gb][years-since y="2020" m="1" d="1"][/years-since-gb]` renders plain text.
- Legacy wrapper nested `[alpdt_time]` countdown including `clock_style="ring"` preserves the SVG dial, data attributes and safe CSS custom properties after WordPress KSES.
- Unsafe nested shortcode returning `<script>`, handler attributes or unsafe URLs is sanitized.
- Gutenberg strings use WordPress.org language packs when those packs become available; Spanish locale content may temporarily fall back to English until approved.
- ALP dashboard footer displays the official logo without a dead link, including keyboard accessibility and layout.

## Actual run log
Executed 2026-11-23 in this build environment:
- **APROBADO (static):** 42/42 checks, zero failed. Archive ZIP integrity, single canonical root, path uniqueness, file diff against v1.0.9, 4 excluded locale catalogs, no broken runtime URL, correct version and text domain, 3 shortcode registrations, safe wrapper syntax, Gutenberg script i18n, Settings link, no outdated translation-domain literals.
- **APROBADO (syntax):** PHP CLI 8.4.24 `php -l` on 14 PHP files; Node v22.16.0 `node --check` on 5 JavaScript files; `tinycss2` parse on 4 CSS files; JSON parse on 1 module manifest.
- **PENDIENTE:** execute WordPress.org rescan; test `[years-since-gb]` with plain text, nested rich markup, ring SVG and malicious nested shortcodes on WordPress 6.5+.
- **NO DISPONIBLE:** actual WordPress/Divi 5 staging installation, frontend/Visual Builder screenshot and keyboard QA, Plugin Check, PHPCS/WPCS and PHPStan; server-side PHP security patch status.
- **Scope:** this is a localized bug/security/package review update, not a full independent security audit or WordPress.org approval.

