# ALP Dynamic Time & Countdowns 1.0.11 | Review Checker pre-submission QA

Date: 2026-11-23. Baseline: complete WordPress.org 1.0.10 ZIP, canonical folder `alp-dynamic-time-countdowns/`.

## Reported ALP Review Checker issues

- **ERROR** `[alpdt_time]` returned HTML without a recognized final output escaping boundary.
- **ERROR** `[years-since]` returned concatenated HTML without a recognized final output escaping boundary.
- **WARNING** `widget_text` replacement returned `preg_replace_callback()` output (analysis cannot prove arbitrary input markup safe).
- **WARNING** `widget_text` closure used `sanitize_text_field()` alone on newly inserted dynamic text instead of output escaping.
- **WARNING** saved shortcode branch called `self::main()` indirectly, making its output contract difficult to prove.

## Applied changes

- Use `wp_kses( $escaped, self::allowed_output_html() )` at the **final return** of the main shortcode, with an allowlist of ALP-generated span/SVG/circle elements and explicitly named `data-alpdt-*`/ARIA/style attributes. WordPress's CSS sanitizer filters inline CSS declarations. See WordPress 6.5 `wp-includes/kses.php`, which includes `--*` custom properties in the safe CSS list.
- For legacy `[years-since]`, `wp_kses( $formatted, array_fill_keys( $allowed, array() ) )` after `esc_html()` of calculated text; keep its wrapper-tag allowlist intact.
- Resolve named saved configurations to `$atts` and render through the same callback final `wp_kses` boundary, avoiding the previous indirect `self::main` return.
- In `ALPDT_Integrations::replace()`, escape **only newly inserted dynamic text** with `esc_html( sanitize_text_field( ALPDT_Engine::render( ... ) ) )`. The unmodified input may already be HTML from a WordPress widget/SEO filter; passing it as-is is deliberate so this plugin does not strip unrelated markup. The scanner may continue to report the helper `preg_replace_callback()` as a **requires-review warning** if its flow analysis does not recognize the escaped callback output. That would not, by itself, prove a vulnerability.
- No database migrations, CSS/JS changes, shortcode removals or REST/Divi API modifications.

## Tests actually executed

- **APROBADO:** PHP CLI `php -l` on 14 PHP files using system CLI version 8.4.24.
- **APROBADO:** Node `--check` on 5 JavaScript files, `tinycss2` on 4 CSS stylesheets, JSON decode of Divi module manifest.
- **APROBADO, isolated STUBS only:** PHP contract harness with a mocked WordPress environment; 613 assertions checking allowed emitted tags and attributes for Ring/Snowfall/Christmas/Confetti, saved shortcodes, live attributes, text escaping, legacy wrappers and preservation of unrelated widget markup. This harness *does not execute WordPress's real `wp_kses`*.
- **APROBADO, static:** source diff, version alignment, shortcode registration, translation exclusion, broken Author URI removal, archive CRC.
- **PENDIENTE:** run updated **ALP Review Checker** and official WordPress **Plugin Check** on version 1.0.11; verify the scanner's remaining warnings with real contextual review.
- **PENDIENTE:** WordPress.org plugin submission rescan and review.
- **NO DISPONIBLE:** actual WordPress 6.5+ (including a 6.5 compatibility test), PHP 8.2 runtime, Gutenberg and Divi 5 frontend/Visual Builder interaction, responsive/keyboard tests, PHPCS/WPCS, PHPStan, server PHP patch inspection.

## Scope / safety

The ZIP is a *pre-submission candidate*. It does not certify absence of XSS, full WordPress security compliance, live performance equivalence, or reviewer approval. Preserve and check live frontend effects on staging before replacing production installations.
