# QA for ALP Dynamic Time & Countdowns v1.0.8 (WordPress.org submission candidate)

## Source and root cause

- Input: complete, previously packaged private-release v1.0.7 ZIP.
- External evidence: WordPress.org automated submission report supplied by the plugin owner on 2026-11-20: **525 ERROR*** notices (`WordPress.WP.I18n.TextDomainMismatch`) and **1 WARNING** (`textdomain_mismatch`).
- WordPress.org assigned the public directory slug `alp-dynamic-time-countdowns`. The previous private release intentionally used `alp-dynamic-time`, matching its local folder, but this is **not** the slug assigned by the public Plugin Directory.
- WordPress requirement: the text domain must equal the WordPress.org plugin URL slug.

## Changes in v1.0.8

1. Change only the translation-domain argument in PHP and Gutenberg JS from `alp-dynamic-time` to `alp-dynamic-time-countdowns`.
2. Align the main plugin header and WordPress text-domain registry custom path with `alp-dynamic-time-countdowns`.
3. Rename both bundled Spanish `.po` / `.mo` catalogs for `es_CR` and `es_ES`; translation payloads are identical.
4. Change the ZIP's one plugin folder from `alp-dynamic-time/` to `alp-dynamic-time-countdowns/`.
5. Bump the version header, constant, stable tag and release notes to 1.0.8.

## Tests performed locally

- **APROBADO:** ZIP CRC/integrity, safe entries, unique paths and single canonical root.
- **APROBADO:** exactly matching `Text Domain`, official directory and locale catalog filenames.
- **APROBADO:** no residual legacy `alp-dynamic-time` text-domain literals inside the runtime PHP or JS files.
- **APROBADO:** both `.mo` files parse as GNU gettext catalogs; translated payloads preserved byte-for-byte.
- **APROBADO:** PHP `php -l` **14/14** (CLI PHP 8.4.24), JavaScript `node --check` **5/5** (Node 22.16.0), CSS `tinycss2` **4/4** (1.5.1).
- **APROBADO:** Compared with v1.0.7, 93 files stayed byte-for-byte unchanged and 13 files changed only within the release scope, with one new QA document.
- **PENDIENTE:** rerun WordPress.org upload scanner after submitting **this** ZIP.
- **PENDIENTE / NO DISPONIBLE:** WordPress Plugin Check inside a running WordPress installation, PHPCS/WPCS, PHPStan, PHP8.2 runtime, activation, staged migration, Divi 5 Visual Builder, frontend and accessibility visual regression.

## Migrating existing sites

- The older private distribution root was `wp-content/plugins/alp-dynamic-time/`. WordPress.org will use `wp-content/plugins/alp-dynamic-time-countdowns/`.
- Do not activate both plugin copies simultaneously: classes and hooks are still named `ALPDT_*`.
- Perform a verified backup; deactivate the prior copy; install the official directory version; check options and shortcode renders on staging; remove the obsolete directory only once verified.
- This package **does not delete, rename, copy or reset** saved options, saved dates, user preferences or existing installs.

## Other release notes

- PHP8.2+ remains the declared minimum. Production PHP patch status, including PHP2Shell mitigations, requires verification on the actual hosting platform.
- `Tested up to` remains unchanged from v1.0.7; no new WordPress/Divi compatibility claim is made.
