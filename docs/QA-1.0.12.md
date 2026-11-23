# ALP Dynamic Time & Countdowns · 1.0.12 · Release preflight

Date: 2026-11-23. Baseline: 1.0.11 ZIP. Target: WordPress.org.

## Changes

- New `ALPDT_Privacy` class registering `wp_privacy_personal_data_exporters`, `wp_privacy_personal_data_erasers` and WordPress Privacy Policy helper text. Core confirmation/authorization remains responsible for personal-data requests.
- Uninstall deletes only plugin-owned `alpdt_quick_colors` user metadata; `alpdt_presets` and saved shortcode site options, WordPress users and other user metadata remain intact. Users' custom Quick Colors revert to the defaults after a privacy erasure.
- All 5 original JavaScript sources and 4 original CSS sources are unchanged. Add 9 corresponding `.min.js/.min.css` outputs. Production (`SCRIPT_DEBUG` false) uses compact builds; debugging (`SCRIPT_DEBUG` true) uses the readable sources. Identical logical handles and dependencies are retained.
- `readme.txt` includes verifiable WordPress.org contributor `alexalp`, matching public account at https://profiles.wordpress.org/alexalp/ and the ALP GitHub owner. No broken Plugin URI or Author URI added.
- Public ZIP omits `docs/` development-only design and old QA documents. Development archive retains them. License, bundled-font OFL/license files and source code stay in public ZIP.
- No code/data migration, functional shortcode changes, CSS redesign or asset-API rewrite. No PO/MO files are bundled.

## Minification verification

Compaction uses locally installed TypeScript lexical scanner / AST comparison for JavaScript, and PostCSS AST comparison for stylesheets; it does not change identifier names, strings, operators or syntax structure. Real browser rendering is not verified.

| Asset | Original bytes | Compact bytes | Reduction |
|---|---:|---:|---:|
| `assets/admin.js` | 104,156 | 91,193 | 12.4% |
| `assets/countdown.js` | 55,552 | 42,327 | 23.8% |
| `assets/live.js` | 1,854 | 1,364 | 26.4% |
| `blocks/editor.js` | 4,129 | 3,314 | 19.7% |
| `divi5/builder.js` | 22,515 | 21,254 | 5.6% |
| `assets/admin-menu-icon.css` | 537 | 296 | 44.9% |
| `assets/admin.css` | 72,969 | 67,839 | 7.0% |
| `assets/countdown.css` | 109,412 | 93,034 | 15.0% |
| `divi5/module.css` | 2,861 | 2,200 | 23.1% |

Total compiled asset payload (9 files): **373,985 → 322,821 bytes, 13.7% smaller**, before HTTP compression. Source assets are also distributed for WordPress.org human-readability compliance.

## Executed QA (APROBADO)

- 94 deterministic static/contract checks passed on source and release code, including version/readme/slug/domain consistency and core API registration assertions.
- PHP `php -l`: 15/15 PHP files (PHP 8.4.24 CLI) passed, including new privacy class and uninstall.
- Node `node --check`: 10/10 JavaScript files (originals and compact builds) passed (Node v22.16.0).
- PostCSS: four compact CSS files parsed and compared against original AST declarations, rules and at-rule params. CSS sources were not modified.
- TypeScript JS lexical tokens/AST: 5/5 minified files preserve lexical tokens and syntactic structure compared with their original sources.
- Isolated mocked WordPress privacy/uninstall contract test: 37/37 assertions passed, including cross-user isolation, data export, deletion, duplicate-page handling, malformed values and site-option preservation.
- Source archive and release archive integrity, single canonical ZIP root, no `docs/` in public distribution, no catalog files, and bundled font licenses: checked during archive build.

## Unavailable or pending

- **NO DISPONIBLE / NO EJECUTADO:** a real WordPress 7.1.5 installation, native WordPress exporter/eraser tools, Plugin Check inside WordPress, Divi 5 Visual Builder, Theme Builder and Gutenberg interactive browser tests.
- **NO DISPONIBLE / NO EJECUTADO:** PHPCS/WPCS, PHPStan and live WordPress.org upload scan. Local PHP 8.4 syntax does not prove a running PHP 8.2 hosting environment or WordPress compatibility.
- **PENDIENTE:** manually verify the current `Tested up to: 7.1` against live WordPress 7.1.x and test activation/update, saved shortcode rendering, all clock appearances and privacy tools before publication.
- **PENDIENTE:** browser visual tests at 320/375/768/1024/1440px, keyboard and screen readers; no layout/visual alterations were intentionally made.
- **PENDIENTE:** verify that the user's hosted PHP runtime is patched (CLI runtime is not web-served PHP).

**Release status: static preflight passed, ready for staging and official WordPress.org rescan; not a certification of live behavior or acceptance by WordPress.org.**
