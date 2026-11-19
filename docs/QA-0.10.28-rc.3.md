# ALP Dynamic Time & Countdowns 0.10.28-rc.3 | Documentation and Gutenberg QA

## Scope

Based on user-provided `alp-dynamic-time-cd-0.10.28-rc.2.zip`. Candidate documentation/Gutenberg-editor fix, not a published WordPress.org release.

### Changed files

- `readme.txt`: removed obsolete FAQ for removed `[alp_time]` alias; updated legacy Years Since inspiration and conditional shortcode compatibility; free status and potential future Pro disclaimer; Countdown target explanation; consistent full product name; moved an existing reusable-shortcode FAQ from below changelog into FAQ section; aligned Stable tag.
- `README.md`: new 0.10.28-rc.3 section; full product-name references in human-facing prose and updated historical section headings for clarity. Technical slugs and abbreviated sidebar label remain intact.
- `blocks/editor.js`: show target field only for `Countdown (days)`, with a description and example; show `to=` in the informational shortcode preview. Existing `to` attribute key, saved attributes, callback, and block slug `alpdt/time` are unchanged.
- `alp-dynamic-time.php`: version header and constant to 0.10.28-rc.3.
- `CHANGELOG.md`: current entry added; no earlier changelog history removed.
- `docs/QA-0.10.28-rc.3.md`: this report.

### Verifications executed

**APROBADO**
- Read original ZIP and confirm `readme.txt`, `README.md`, `blocks/editor.js`, and legacy shortcode implementation before editing.
- 14 PHP files: `php -l` PASS (PHP CLI 8.4.24 in this environment, not a PHP 8.2-runtime test).
- 5 JavaScript files: `node --check` PASS.
- Isolated Gutenberg editor-registration test with mocked WordPress UI: block `alpdt/time` and its attributes unchanged; countdown type shows only target setting with explanatory help; `onChange` persists `to`; informational code sample contains `to`; elapsed type still shows `from`, without target field; `save()` still returns null.
- Isolated PHP target parser test with `America/Costa_Rica`: `YYYY-MM-DD`, `YYYY-MM-DDTHH:MM` accepted; invalid calendar date rejected.
- Readme FAQ removes requested obsolete alias question, describes free plugin and possible future Pro (no Pro launch claim), full product name, and conditional support for legacy shortcodes.
- Cross-check with WordPress.org original listing (`https://wordpress.org/plugins/years-since/`), which reports closure on 2025-03-06 and was not available for download as of its indexed record; wording is limited to closure, not permanent removal.
- ZIP integrity and content verification in packaging script.

**PENDIENTE**
- Real WordPress Gutenberg visual/editor verification and save/reload on WordPress 6.5+.
- Divi 5 Visual Builder and frontend checks (unchanged code).
- Real PHP 8.2-host runtime and WordPress.org publication process.

**NO DISPONIBLE / NO EJECUTADO**
- WordPress PHPStan, PHPCS/WPCS and official Plugin Check executable environment.
- Official readme validator and live staging deployment.

All other plugin files and binaries remain byte-for-byte as provided in rc.2, except the listed changed files. The original historical `[alp_time]` removal remains in the changelog; no support is added back.
