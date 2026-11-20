# ALP Dynamic Time & Countdowns 1.0.3 | QA

**Date:** 2026-11-20  
**Base:** complete 1.0.2 ZIP (unmodified original retained).  
**Scope:** Hourglass sand motion only, plus version and release documentation.

## Implementation

- Separate normal grain travel (approximately 1.7–1.8 s) from the interval at which grains appear.
- Emission cycles by unit: seconds 1.8 s, minutes 3.6 s, hours 6 s, days 10 s, months 14 s, years 18 s. Each unit phases eight grains uniformly across its own cycle. These are animation cycles, **not** the passage of calendar time.
- Hide the static fine-stream illustration for the sparse units, which could make the falling sand look motionless. Keep it on seconds.
- Preserve upper/lower sand fill and live timer computation; do not change shortcode names/attributes, options, Divi 5/Gutenberg blocks, branding, stored data or accessible reduced-motion behavior.

## APROBADO — executed

- PHP CLI `php -l` on all PHP files.
- JavaScript `node --check` on all JavaScript files.
- CSS parsed with Python `tinycss2`, with no parse errors.
- Playwright/Chromium isolated fixture exercising the **actual** `assets/countdown.js` and `assets/countdown.css`: six unit types have 8 grains each, staggered emission timing and computed natural fall duration between 1.6 and 1.9 seconds.
- Playwright/Chromium isolated fixture: seconds retain the fine stream; sparse units hide the static stream; tick/refresh does not restart an active animation.
- Playwright/Chromium isolated fixture: no horizontal page overflow at 320, 375, 768, 1024 or 1440 px; `prefers-reduced-motion: reduce` disables grain animations.
- Source-file comparison with 1.0.2: only intended JS/CSS, version and documentation files modified; no functional files removed.
- ZIP compression/CRC integrity and plugin-root structure.

## PENDIENTE

- Visual and functional testing in an actual WordPress site with persisted shortcodes and the Generator preview.
- Testing in Divi 5 Visual Builder, Gutenberg and the published frontend under production theme styles.
- Real browser/assistive-technology tests beyond the isolated Chromium fixture.

## NO DISPONIBLE

- PHPCS/WPCS, PHPStan, Plugin Check and an actual WordPress environment.

This report does not claim a full end-to-end WordPress or Divi verification.
