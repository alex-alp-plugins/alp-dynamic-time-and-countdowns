# ALP Dynamic Time & Countdowns 0.10.28-rc.2 | Scope and QA

Source: user-provided `alp-dynamic-time-cd-0.10.28-rc.1.zip`.

## Change scope
- Add the official ALP Dynamic Time isotype to the Divi 5 module picker via the `moduleIcon` metadata property and the documented Divi `divi.iconLibrary.icon.map` hook; the monochrome icon preserves the original bundled path, adjusted to 16x16. The module name and saved attributes are unchanged.
- Move the existing non-modal copy status from bottom-center to viewport center. Success still disappears after 2000 ms; clipboard failure retains 6000 ms for reading. No blocking overlay and no focus movement.
- Add countdown font-weight Light (300) to the admin selector, JavaScript serialization/preview, PHP appearance allowlist and saved shortcode validation.
- Set `@font-face` weight descriptors to the supported `wght` axis of the bundled variable fonts: Inter (100-900), Montserrat (100-900), Fredoka (300-700). Font families without a real Light face may fall back to their nearest supported weight.

## Executed focused tests
- `php -l` on all **14 PHP** files: PASS (PHP 8.4.24 CLI). This is syntax-only; PHP 8.2 runtime was not tested.
- `node --check` on all **5 JS** files: PASS (Node 22).
- `python -m json.tool divi5/module.json`: PASS.
- Isolated Node hook simulation (`test_divi_isotype.cjs`, not shipped): PASS. Checks that `divi.iconLibrary.icon.map` is present, keeps native icons, registers the exact official SVG path, returns a 16x16 icon and registers an unchanged module name and attribute set.
- SVG rendered in a local vector-to-PNG test: PASS. Visual geometry fits within the 16x16 viewBox with clear buffer. Not evidence of Divi rendering.
- Actual Divi 5 Visual Builder module picker, save/reload, Theme Builder and frontend: PENDING, no connected WordPress/Divi site to test.
- PHPCS/WPCS, PHPStan, Plugin Check: NOT AVAILABLE in this environment.

## Tests
Complete test records are reported in the accompanying conversation. A static syntax check cannot prove frontend WordPress or Divi 5 compatibility. WP runtime/Divi 5 integration and font appearance in each user-selected combination remain PENDING; PHPCS/WPCS, PHPStan and Plugin Check are NOT AVAILABLE if the tools are not installed.

## Compatibility
All prior `clock_weight` values, data keys, shortcodes and settings are preserved. New `clock_weight="300"` is accepted; arbitrary values remain rejected.
