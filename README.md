## ALP Dynamic Time & Countdowns 1.0.0-rc.3 | Hero notices and font scope

- Removed the experimental **Custom font** upload controls, uploader endpoint and runtime integration from this release candidate. It may return in a later version after further testing.
- Retained the removal of Black And White Picture and Rubik Scribble from rc.2. Removed families and experimental custom-font references fall back to the style default; saved data is not deleted.
- External notices inserted inside the hero are relocated to a visible space **above** it. Important WordPress, SEO and security messages are not suppressed.
- Existing Media Library attachments uploaded while testing rc.2 are preserved; they are no longer selectable in this release.


## ALP Dynamic Time & Countdowns 1.0.0-rc.1 | Gutenberg saved-shortcode selector

- The Gutenberg block now offers **one saved-shortcode selector**, mirroring the Divi 5 module: configure dates, countdowns, animation and typography in **Generator**, save the shortcode, then choose that saved configuration inside the Gutenberg block.
- `alpdt/time` keeps its identity and uses only the `saved` block attribute. Experimental pre-1.0 Gutenberg field attributes are intentionally not supported; select a saved shortcode in the new block. New selections use `saved` and render the actual `[alpdt_time saved="..."]` configuration, including animated countdowns when the selected shortcode uses them.
- Validated saved shortcode names/identifiers are sent to the editor (not full shortcode codes). A deleted selection becomes unavailable rather than silently rendering another configuration.
- The official Gutenberg isotype, WordPress menu, Divi 5 module and all other shortcodes are unchanged. This is a **release candidate**, not a final release or WordPress.org publication. Runtime tests on real WordPress and Divi 5 remain pending.

## ALP Dynamic Time & Countdowns 0.10.28-rc.3 | Readme and Gutenberg clarity

- `readme.txt`: remove the obsolete short-alias FAQ; explain that ALP Dynamic Time & Countdowns was independently created after Years Since - Timeless Texts was closed in the WordPress.org directory, preserving supported legacy shortcodes when available and adding new features. It is not affiliated with the former plugin authors.
- Gutenberg: Countdown target is the future end date (`YYYY-MM-DD` or `YYYY-MM-DDTHH:MM`) in the WordPress timezone. It appears only for the Countdown (days) type, with help text and a correct shortcode summary; its stored `to` attribute is unchanged.
- ALP Dynamic Time & Countdowns is free; an optional Pro edition is only a possible future offering. No Pro edition is available.
- Public documentation uses the complete product name. Technical slugs and the intentionally abbreviated WordPress sidebar label are unchanged.
- No stored dates, shortcodes, Divi attributes, or compatibility alias names are changed.

## 0.10.28-rc.2 · Divi 5 icon and bundled fonts

**0.10.28-rc.2.** Divi 5 module selector registered the official ALP Dynamic Time & Countdowns isotype as its native builder icon, using a monochrome 16×16 projection of the bundled SVG path. The identifier `alpdt/dynamic-time`, saved layouts, controls and frontend output are unchanged. Live Visual Builder verification remains pending. Countdown fonts are served from the plugin directory. See [font licenses and sources](docs/FONTS.md). Entries below describe the behavior of their respective historical versions.

## 0.10.27 · Saved-date form restored

- Restores the reusable-date editor next to the dates list in the **Saved dates** tab, matching the prior two-card layout.
- Changes the form title to **Save reusable date**, translated as **Guardar fecha reutilizable**.
- Keeps the non-modal, 2-second copy confirmation, existing date identifiers, shortcode behavior and settings unchanged.

## 0.10.26 · Copy confirmation and saved-date editor location
- All shortcode and variable Copy actions now use one floating, accessible, non-modal confirmation (2 seconds), without adding status text inside rows or shifting card dimensions. Copy failures offer manual selection guidance.
- **Saved dates** is now a listing. Use **Add date in Generator** to open the existing reusable-date form, or edit a row to open that form with its date and identifier intact. The form lives in a collapsible section of **Generator** to avoid an always-visible redundant creation panel in Saved dates.
- Existing saved date identifiers, shortcodes and stored settings are preserved. The footer retains its approved three-zone composition and yellow review star. Tests are isolated; live WordPress/Divi validation is pending.

## 0.10.25 · Copy feedback and footer star
- Copying an anniversary message variable shows a confirmation **below its button**, disappears after approximately 2.8 seconds, and clears previous variable confirmations. Keyboard and screen-reader status feedback remain available.
- A yellow SVG star now precedes the centered footer review invitation. Existing official plugin and ALP corporate SVGs are preserved; review text remains unlinked until a verified repository URL is available.
- No option keys, shortcode attributes or database migrations changed. WordPress/Divi functional verification remains pending.

## 0.10.24 · Anniversary preview, footer positioning and SEO month variable
- Click or tab into the **Before**, **On**, or **After** anniversary message field to preview precisely that message. The real shortcode still uses the current date to select the right text automatically. The preview selection never changes the saved shortcode.
- The dashboard footer remains near the bottom of short pages; it now has a centered review invitation without an unverified external link. The existing product and ALP Plugins logos remain untouched.
- About & guide → SEO plugins now displays copyable native Rank Math/Yoast markers for both current year and current month. The month name uses the WordPress site timezone and locale.
- An existing malformed Saved dates tabpanel attribute was corrected. Existing shortcodes, settings, URLs, and option keys are preserved.
- Development candidate: test in a staging WordPress installation and validate Rank Math/Yoast SEO metadata and Divi 5 separately.

## 0.10.23 · Clearer date tools and saved-shortcode pagination
- Saved shortcodes display without an inner scroll and paginate after ten entries.
- Anniversary messages offer `{number}` (ordinal, e.g. 30.º), `{years}` (plain 30) and `{current_year}`. Existing `{year}` markers remain accepted.
- Smart Date post-date expressions can use a preview-only sample date in the generator; published content always uses its actual WordPress post date.
- Type descriptions explain what to expect, especially Anniversary number vs Anniversary message, Copyright years, and Smart date.

## 0.10.22 · Anniversary placeholders, cleaner editor and matching Christmas bulb glow

- Anniversary message tokens: `{years}` is the established elapsed anniversary number, `{year}` is an accepted alias for that same number, and `{current_year}` shows the four-digit calendar year. The generator displays all three with context and Copy actions; the PHP preview and frontend use the same engine. Existing `{years}` shortcodes are unchanged.
- The SEO variables panel was removed from the main generator to avoid confusing it with anniversary message placeholders. Rank Math and Yoast SEO variable registration remains intact. Saved Shortcodes now visibly shows the registered tokens next to the existing copy controls; further instructions remain under About & guide → SEO plugins.
- The Celebration burst duration selector offers 5, 8 and 10 seconds. Older saved 3-second configurations continue to render; 10 seconds is accepted by the PHP renderer and the JavaScript animation.
- The Clock appearance label has a small blue SVG clock icon, scoped to the ALP dashboard.
- Christmas Lights bulb glow now uses each bulb's selected color for both fill and glow, including a lighter same-hue near halo on compatible browsers.
- This is a development candidate. Browser fixture and static checks do not establish live WordPress or Divi 5 compatibility; test on staging before release.

## 0.10.21 · Tighter spacing and configurable seasonal/celebration effects

- **Countdown padding** controls the outside of the complete timer. Explicit Top/Bottom/Left/Right zero is respected; leaving fields empty keeps the original style defaults.
- **Number card padding** controls only each number card. In Snowfall, zero Left/Right now removes unnecessary internal spacing while preserving enough glyph width to prevent a jump when values change.
- The older **Snowfall number card spacing** field was removed from the visible editor to eliminate overlapping controls. Existing `card_inner_spacing` shortcodes and saved settings remain supported.
- **Countdown border radius** and **Blood Dripping font** take the full width of their respective editor sections.
- **Christmas Lights** has 8–60 bulbs, default 15; raising the bulb count reduces horizontal distance at 100% width. Add up to seven new bulb palette colors (12 total).
- **Soft Glow** offers an optional background HEX value. Leaving the field empty retains the original pale-green gradient.
- **Hourglass** now varies its sand-flow animation speed and fill change with the units. **Confetti & Streamers** launches balloons more slowly in staggered positions, and Add color buttons allow up to 10 confetti/balloon colors.

The ZIP is a development candidate. Standalone PHP and Chromium checks do not replace full WordPress, Divi 5 Visual Builder or Theme Builder verification.

## 0.10.20 · Full-width countdown, independent padding and compact links

- **Countdown width:** choose Automatic (existing behavior) or Full width (100% of its parent container). Christmas Lights extends the bulb strand across the container while centering number cards and their labels; the same opt-in setting works with other countdown styles.
- **Countdown padding:** Top/Bottom and Left/Right now control the space around the entire timer, independent of the **Number card padding** controls for the four sides of individual number cards.
- **Compact link buttons:** vertical and horizontal chain icons beside the inputs, each with `aria-pressed`, replace the oversized All four control. Linked values stay synchronized in either editing direction. The state is editor-only; the values remain ordinary shortcode attributes.
- **Snowfall horizontal padding:** independently set Left and Right to visibly widen or narrow the numbered cards. Fixed numeral slots continue to avoid width changes on every countdown tick.
- Backward compatibility: `clock_padding` and `clock_padding_mode` from older shortcodes remain accepted, and all previously saved configurations retain automatic width unless deliberately changed.

The ZIP is a development candidate. Isolated PHP and Chromium checks do not replace full WordPress, Divi 5 Visual Builder or Theme Builder verification.

## 0.10.19 · Compact menu and simplified padding / Christmas Lights gap

- WordPress menu label shortened to **ALP Dynamic Time & CD**, without renaming the plugin.
- The spacing panel now always shows Top, Bottom, Left and Right numeric card padding. Link Top/Bottom, Left/Right or all four with accessible chain buttons. Legacy uniform padding settings and saved shortcodes are still accepted.
- Snowfall top/bottom padding now applies directly to the visible number card without weakening its fixed numerical width.
- A **Bulbs to number cards spacing** (0–70px) control is available for Christmas Lights. Leaving it empty preserves the existing layout.

This is a **development candidate**. Browser fixtures and syntax checks are distinct from WordPress/Divi functional and visual verification.

## 0.10.18 · Full-width font picker, actual number padding and stable digits

The Font family selector spans the Typography section width. Every countdown design now applies configured numeric padding to its actual visible card: the number container for regular styles, the colored cell for Minimalist/legacy Promo, and exterior clearance for circular dial styles (to keep dials round). The horizontal outer panel keeps its appearance preset rather than borrowing the number card's padding. Leaving all padding values empty preserves the theme's designed defaults.

Digit slots reserve a stable per-unit width using the chosen font's glyph metrics. This prevents countdowns shifting width as the values update; on a browser resize or font load, widths can be measured again to fit the responsive layout. The plain-text countdown is covered, too. Split-flap and odometer animations remain separate from the width lock.

Almonte Snow no longer relies solely on a locally installed desktop font: `@font-face` first tries local installation and then a third-party public WOFF2 (with WOFF fallback). The font is public-domain CC0 per Typodermic's official catalogue; no font binary is redistributed in the plugin. The external font request may be blocked by browser CORS, CSP, ad-blocking, or network policy. Self-hosting a properly licensed font is recommended for production stability/privacy. Only the selected face is downloaded, subject to browser behavior.

This is a **development candidate**. Static PHP/JS/CSS checks and isolated Chromium fixtures are not equivalent to real wp-admin, frontend or Divi 5 installation tests. The main header and `Stable tag` are 0.10.18. No stored options/shortcode migration is necessary.

## 0.10.17 · Linked padding and CSS typography units

The generator supports independent link/unlink controls for Top/Bottom and Left/Right padding. A blue chain means the values mirror each other in both directions. Shortcodes still store explicit per-side numeric values; the link toggle is an editor convenience, not a new persisted setting.

Number and label font sizes accept `px`, `rem`, `em`, `%`, `vw`, `vh`, or restricted `clamp()` syntax. For example `clamp(24px, 4vw, 72px)` smoothly keeps numerals between 24px and 72px using viewport width. Legacy unitless sizes are treated as pixels. Only simple CSS lengths are accepted inside clamp. Per-font selector changes automatically refresh the generated shortcode.

This version preserves stored option keys, installed-plugin identity, WordPress security flow, and Divi module attributes. Browser tests use an isolated WordPress-like rendered fixture; live WordPress and Divi 5 verification remains pending.

## 0.10.12 · Compact Horizontal padding behavior

In **ALP Dynamic Time & Countdowns**, the number-container padding controls also override the appearance panel's preset padding **only in Horizontal mode** when explicitly configured. Set `clock_padding="0"` to remove outer panel and inner numeric padding (the face geometry required for Flip animation remains intact). An empty padding field preserves each style's previous padding. The Individual option supports `clock_padding_top`, `clock_padding_right`, `clock_padding_bottom`, `clock_padding_left`. In Default layout the original styling is preserved.

For Snowfall and Christmas Lights the editor only exposes number/label colors, seasonal background color, usable number/label sizes, layout, padding and unit labels. Christmas Lights also offers its font and five independently selected bulb colors. Extra unused controls remain hidden, but legacy saved values are preserved.

# ALP Dynamic Time & Countdowns 0.10.9 · Product naming consistency
- **Naming consistency update:** WordPress sidebar, Gutenberg block, SEO variable labels and Divi guidance now use the full product name **ALP Dynamic Time & Countdowns**. The old technical slugs `alp-dynamic-time`, `alpdt/time`, `[alpdt_time]` and saved settings remain unchanged.
- This is a naming-only maintenance release based on 0.10.8; features and supported shortcode attributes are unchanged.


- **New CSS seasonal countdowns:** Snowfall with 64 animated snowflakes and Christmas Lights with 14 bulbs, in Multicolor or Warm white. Snowburst One Regular is loaded via Google Fonts when Snowfall is used.
- **Per-number padding:** Choose **Global** or **Individual** (Top/Right/Bottom/Left), 0–80 px. Uniform `clock_padding` shortcodes from previous releases remain valid.
- The controls affect individual numeric cards. On Ring, Liquid and Orbit the added space is exterior margin, keeping the circles perfectly round. Leave inputs blank to keep each appearance's defaults.
- Both countdowns and other dynamic text are centered in the live preview.
- The six user-provided official **product** SVGs are included unchanged. Hero uses the new white horizontal product lockup, product footer uses color, Gutenberg uses the color isotype and WordPress sidebar uses white isotype at 20 px. White/color vertical variants are included for future uses.
- **Corporate logo asset integrated:** the official ALP Plugins corporate SVG is now bundled locally and shown in the dashboard footer alongside the product logo.
- This is a development candidate. Live WordPress/Divi 5/Plugin Check testing remains outstanding.

<!-- Development release 0.10.1: full validation in WordPress/Divi 5 and Plugin Check is pending. -->
# ALP Dynamic Time & Countdowns v0.9.46 | Official menu isotype

- The top-level WordPress admin menu now uses the official user-provided white SVG isotype instead of the generic WordPress clock icon. The original artwork remains local at `assets/branding/isotype-white.svg`.
- Existing settings, dashboard, menu slug, shortcode behavior, Divi integration, translations and data are unchanged. Requires PHP 8.2; Tested up to 7.1 remains the owner-reported test environment, not a fully verified release claim.

# ALP Dynamic Time & Countdowns v0.9.44 | WordPress top-level navigation

- The generator, saved shortcodes, saved dates, and compatibility panel now live under the dedicated **ALP Dynamic Time & Countdowns** WordPress admin dashboard (sidebar label: **ALP Dynamic Time & CD**), rather than **Settings**.
- Installed Plugins > **Settings** links to `admin.php?page=alpdt-time`. Existing GET bookmarks under `options-general.php?page=alpdt-time` are redirected to the new location.
- Save/delete forms, JSON export and read-only compatibility scanner links use the new location. Permissions and nonces remain unchanged. No stored options, saved shortcodes or frontend/Divi integrations are migrated.
- WordPress `Tested up to: 7.1` and PHP minimum `8.2` are unchanged from 0.9.43; full live verification remains pending.

## Version 0.9.43 (development)

### 0.9.43 metadata and compatibility
- WordPress `Tested up to: 7.1` in `readme.txt` reflects the user-reported WordPress 7.1.4 installation used for Plugin Check. This is not certification of all functions or Divi 5 runtime behavior.
- PHP minimum requirement deliberately raised to **8.2** in both the plugin main header and `readme.txt`, following the user's PHP2Shell-related compatibility policy. Install an up-to-date patched 8.2+ release; the bare minimum 8.2.0 is not a security guarantee.
- Main plugin version and readme Stable tag are both 0.9.43. No runtime features changed.


### 0.9.42 fixes
- Blood Dripping figures/labels use CSS regular weight (400) without synthetic bold. Drops stay visible until reaching the fixed-width pool, then are absorbed. Different shapes and timings remain.
- Private ZIP translations now register the local `languages` directory with WordPress's translation registry (`Domain Path: /languages`). This replaces the discouraged `load_plugin_textdomain` invocation.
- **Release gate:** WordPress 7.1 is declared in `readme.txt` based on the owner-reported WordPress 7.1.4 Plugin Check environment. Full live WordPress/Divi 5 functional compatibility remains pending.


- Blood Dripping puddles maintain constant width across changes such as 02 → 01. The width still scales with the configured numeral size.
- Saved Shortcodes counts and lists only working `[alpdt_time]` configurations. Incompatible historical records remain in the WordPress option as a backup but are excluded from the visible count and from Divi selections. They are not silently deleted.
- Translator notes and bounded security/SQL exceptions were updated for the supplied Plugin Check report. The local design contract ships at `docs/DESIGN.md`.
- `Tested up to: 7.1` identifies the owner-reported WordPress 7.1.4 environment; do not publish to WordPress.org until runtime compatibility and Plugin Check are validated.

## 0.9.38 animation and naming update (development candidate)

- **Blood Dripping** is the new user-facing appearance label, without “Halloween”. The original saved shortcode slug `clock_style="blood"` is unchanged, including Divi 5 saved-shortcode compatibility. Spanish translations were updated.
- **Celebration confetti & streamers:** replaces the old CSS lateral poppers with an original native Canvas 2D particle shower. It starts as a small cluster near the right-hand lower edge (approximately 73% x / 95% y of the countdown), spreads upward and then falls under per-particle gravity and flutter. Pieces are tiny square and irregular pixel-like paper with occasional curled streamers. This is modeled on the **single static** user screenshot; an exact animation cannot be inferred from one frame.
- **Balloons:** keeps the same six balloons, original curved ascent, delays and independent color palette. Confetti colors, legacy saved shortcodes, and 3/5/8-second celebration duration remain compatible. Repeating short bursts do not slow down for longer durations.
- The animation is scoped to the clock widget, stops when complete, pauses while offscreen or the page is hidden, and is omitted for `prefers-reduced-motion`. No external animation library is loaded. Local isolated Chromium tests passed, including replay, pause, responsive 375px, duration 3/8 seconds, and reduced motion. **WordPress and Divi 5 Visual Builder runtime verification is still pending.**

## 0.9.36 animation refinement

- **Celebration confetti & streamers:** keeps the same paper, triangle, circular and curled-ribbon shapes plus curved balloon paths. Confetti launches together from the widget center like a one-click explosion, expands quickly, then floats and falls under gravity. Repeats at a fixed speed for configured 3/5/8-second duration. Legacy `clock_style="confetti"` works unchanged.
- **Blood Dripping:** each teardrop is based on a square (aspect-ratio 1:1), and its rotation/scale do not squash it. The blood puddle is positioned lower beneath the digit, with enough room before the label. Keeps the original Goo SVG treatment, custom colors and reduced-motion support.
- The referenced `YzMdVWe` CodePen could not be loaded by the available retrieval tools; the animation is an independently implemented Boom-style approximation, not an exact reproduction.

# ALP Dynamic Time & Countdowns (v0.9.40)

**Breaking shortcode change in 0.9.40:** `[alp_time]` is no longer registered or expanded by ALP Dynamic Time & Countdowns. Use `[alpdt_time]` in test pages, saved configurations and SEO fields. No stored WordPress data is deleted or rewritten. The independent `[years-since]` aliases are unchanged.

**Status: installable development candidate, NOT certified or production-tested.** New, independent code. The supplied Years Since - Timeless Texts plugin ZIP (v1.5.0) was statically reviewed; none of its executable PHP/JS or libraries are bundled here.

## What works in source

- `[alpdt_time]` for elapsed/calendar years, complete anniversaries, dates, current year/month, copyright range or current-year only, ordinal anniversaries, countdown days or live time to a specific hour/minute, and years/months/weeks/days.
- `[years-since]` compatibility alias, only if the shortcode is not already owned by another plugin. Supports `y`, `m`, `d`, `text`, `html` and now correctly processes `show=y`, `show=ym` and `show=ymd`.
- Admin generator with WordPress REST preview, reusable date identifiers, editor-safe delete confirmation, JSON export, and paged read-only legacy shortcode scanner.
- Gutenberg server-rendered block `alpdt/time` and Block Bindings source `alpdt/date` (requires compatible core block binding usage).
- Dedicated *string output* hooks for Yoast, Rank Math and SEOPress titles/descriptions, plus optional `alpdt_dynamic_text` filter for developers.
- WP-CLI `wp alpdt render` and `wp alpdt count` if WP-CLI is present.
- Divi 5 experimental **Saved Shortcode** selector module. Create all content and countdown settings in the dashboard. In Content select a saved configuration, then optionally override its number/text color, label color, accent, label size, native typography or alignment in Design. The saved configuration is never modified. Uses an edit-permission-protected REST preview and server-rendered shortcode HTML. **Real Visual Builder and Theme Builder verification pending.**
- Optional `live="true"` revalidation of visible numbers over a public read-only REST endpoint. Server output is always present for no-JS/crawler rendering.

## Branding in 0.9.35

The dashboard uses the official user-supplied **white horizontal ALP Dynamic Time & Countdowns logo** inside its existing navy hero, retaining the animated clock. The bottom of that settings screen shows the official **color horizontal logo** and a text link to **ALP Plugins**. The WordPress Gutenberg `alpdt/time` block uses the official color isotype in its editor picker. These assets are served locally from `assets/branding/` with no external image requests. The official ALP Plugins corporate logo is now supplied and used in the dashboard footer as a separate local SVG, without replacing the product branding. The plugin's frontend countdowns and Divi native module icon are unchanged. WordPress.org directory icons require separately deployed SVN assets.

# ALP Dynamic Time & Countdowns (v0.9.33)

**Status: installable development candidate, NOT certified or production-tested.** New, independent code. The supplied Years Since - Timeless Texts plugin ZIP (v1.5.0) was statically reviewed; none of its executable PHP/JS or libraries are bundled here.

## What works in source

- `[alpdt_time]` for elapsed/calendar years, complete anniversaries, dates, current year/month, copyright range or current-year only, ordinal anniversaries, countdown days or live time to a specific hour/minute, and years/months/weeks/days.
- `[years-since]` compatibility alias, only if the shortcode is not already owned by another plugin. Supports `y`, `m`, `d`, `text`, `html` and now correctly processes `show=y`, `show=ym` and `show=ymd`.
- Admin generator with WordPress REST preview, reusable date identifiers, editor-safe delete confirmation, JSON export, and paged read-only legacy shortcode scanner.
- Gutenberg server-rendered block `alpdt/time` and Block Bindings source `alpdt/date` (requires compatible core block binding usage).
- Dedicated *string output* hooks for Yoast, Rank Math and SEOPress titles/descriptions, plus optional `alpdt_dynamic_text` filter for developers.
- WP-CLI `wp alpdt render` and `wp alpdt count` if WP-CLI is present.
- Divi 5 experimental **Saved Shortcode** selector module. Create all content and countdown settings in the dashboard. In Content select a saved configuration, then optionally override its number/text color, label color, accent, label size, native typography or alignment in Design. The saved configuration is never modified. Uses an edit-permission-protected REST preview and server-rendered shortcode HTML. **Real Visual Builder and Theme Builder verification pending.**
- Optional `live="true"` revalidation of visible numbers over a public read-only REST endpoint. Server output is always present for no-JS/crawler rendering.

## Changes in 0.9.33

- Halloween uses the requested **Creepster** typeface; shorter, continuous-fall drops fade out before reaching the clock labels. The font is loaded from Google Fonts on the Halloween effect only until it can be legally bundled for offline/local use; the existing DESIGN.md rule preferring no external font requests remains an unresolved release issue.
- Birthday confetti now offers **four confetti colors plus four independently configurable balloon colors** in the ALP generator, both through existing HEX color pickers. Legacy saved shortcodes use the original party palette unless edited and re-saved.
- Divi 5: the old 10–32px "Label text size" range is now hidden in favor of the native Divi font size control for labels, including the native unit picker and responsive controls. The old field and stored values remain supported. The dashboard's label-size numeric setting now permits 6–200px.
- No change to timers, shortcodes, saved dates, existing module identifiers or existing color settings.

## Changes in 0.9.32

- Clicking the ALP HEX input now opens the existing custom color picker; Divi 5 local color settings are configured to recognize global-color references, pending verification in an actual Visual Builder session.
- Divi native typography is conditional: only applicable for textual styles. Extraneous advanced font fields were trimmed, a Label text size override (10–32px) was added, and Sizing alignment now also aligns the clock grid.
- Birthday confetti, streamers and curved balloon paths repeat at fixed speed within 3/5/8-second animation windows, rather than slowing down as duration increases.
- Halloween Blood has nine independently animated, SVG-filtered drops per timer value, in a new original implementation based on the supplied visual reference.
- A module-specific Divi 5 filter suppresses groups named `Line Hover Animation`, which may come from Divi Supreme Pro; its effect on the actual installed extension still needs live Divi verification.
- Live Divi editor and responsive visual tests remain pending.

## New in 0.9.29

- **Fireworks Celebration:** choose whether fireworks play once at zero (default for all existing saved shortcodes) or loop in spaced bursts while the countdown is running and visible. No automatic replay on already-expired pages.
- **Celebration Confetti & Streamers:** separate colorful falling paper and long curled ribbons, with the same end/loop control and user-selected color panel.
- **Blood Dripping:** decorative dripping liquid effect beneath real, selectable countdown numerals; number, card and accent colors are editable. Inspired by Nico Prat's gooey-drip animation (https://codepen.io/nicooprat/pen/RxrVwR), with independent CSS/JavaScript and no copied code.
- **Typography:** the font picker is hidden for Pixel Matrix and Particle Morph, whose glyphs use drawn dots rather than a font. All other styles retain their font controls.
- Particle animation is suppressed when `prefers-reduced-motion` is enabled; no third-party animation packages or tracking are introduced. Divi 5 loads saved shortcodes using the existing simplified module; live builder validation is still pending.

```text
[alpdt_time type="countdown" to="2027-01-01T00:00" countdown_display="full" clock_style="celebration" celebration_mode="loop"]
[alpdt_time type="countdown" to="2027-01-01T00:00" countdown_display="custom" clock_units="days,hours,minutes" clock_style="confetti" celebration_mode="end"]
[alpdt_time type="countdown" to="2027-10-31T18:00" countdown_display="full" clock_style="blood" digit_color="#C81D32" accent_color="#8D0E22"]
```

## New in 0.9.23

- **About & guide** is organized into four sub-tabs with preserved copy actions. The **Compatibility** tab recommends a supported, patched PHP version, particularly in light of PHP2Shell (fixed in 8.2.35 / 8.3.36 / 8.4.27 / 8.5.12). PHP 8.2 reaches end of security maintenance on 2026-12-31; the plugin's minimum PHP requirement is now 8.2; use a security-patched release (not merely 8.2.0).
- **Fireworks celebration** is an optional countdown appearance that displays a completion message and emits an original, lightweight burst when a clock actually reaches zero. It does not run a particle animation on already-expired pages; reduced-motion users see the final message without sparks. This is not a calendar-triggered site-wide fireworks service.

```text
[alpdt_time type="countdown" to="2027-01-01T00:00" countdown_display="full" clock_style="celebration" celebration_message="Happy New Year!" celebration_duration="5"]
```

## Quick start

1. Upload `alp-dynamic-time.zip` via **Plugins → Add New → Upload Plugin** on a staging site.
2. Activate. Open **ALP Dynamic Time & Countdowns**.
3. Choose a date and format in the Generator, copy the shortcode into supported text content.
4. Optionally create a reusable milestone (`company-founded`, `2000-12-15`) and use `[alpdt_time id="company-founded"]`.
5. For sites using the Years Since - Timeless Texts plugin, back up the site, confirm the legacy scanner output and compare key frontend pages **before** disabling the old plugin. Never activate both expecting ALP to override another plugin's shortcode registration.

## Shortcodes and semantic differences

```text
[alpdt_time type="year"]                                => 2026 (in 2026)
[alpdt_time type="year" offset="1"]                     => 2027 (in 2026)
[alpdt_time type="date" format="F Y"]                   => October 2026 (locale-dependent)
[alpdt_time type="elapsed" from="2000" mode="calendar"]
[alpdt_time type="elapsed" from="2000-12-15" text="false"]
[alpdt_time type="elapsed" from="2000-01-01" format="ymd"]
[alpdt_time type="ordinal" from="2006" mode="calendar"]
[alpdt_time type="countdown" to="2027-01-01"]
[alpdt_time type="countdown" to="2027-01-01T18:30" countdown_display="full" clock_style="glow"]
[alpdt_time type="copyright" copyright_style="current"]
[alpdt_time type="copyright" from="2000"]
[alpdt_time id="company-founded"]
[alpdt_time from="2000" live="true"]
[alpdt_time type="anniversary_message" from="2000-12-15" before="Soon: {years} years" on="Today: {years} years" after="Our story continues"]
```

- `mode="calendar"` subtracts the start **year**, useful for marketing "since 2000" counters. `mode="anniversary"` (default) counts complete years since the supplied date. Year-only input normalizes to January 1; year-month defaults to day 1.
- `format="ymd"` on elapsed time shows years, months and days. `unit` can be `years`, `months`, `weeks`, `days`.
- Countdown defaults to legacy date-only calendar days (returning zero after the target). Set `to="YYYY-MM-DDTHH:MM" countdown_display="full"` for a ticking clock (days, hours, minutes and seconds), with the WordPress site timezone. The shortcode renders server-side initially and adds a small self-updating browser clock; optional `clock_style="glow"` enables an animated accent, while `prefers-reduced-motion` disables the animation. For a plain number of days to a specific time, omit `countdown_display`.
- `html` is only a narrow safe tag allowlist, never arbitrary markup. Output is escaped.
- Date format accepts a small allowlisted character set of WordPress date format tokens; the generator exposes friendly example-based presets instead of requiring token knowledge. `type="copyright" copyright_style="current"` returns the current year without needing a starting date; `range` is the backward-compatible default. Date uses WordPress locale/timezone.
- `type="ordinal"` supports Spanish (º) and English (st/nd/rd/th) rules; other locales currently use English ordinal fallback.
- Messages: three anniversary states (before, on, after) relative to the current year anniversary. 29 February is treated as 28 February **for this message type** in non-leap years; exact complete-year counting uses PHP DateInterval instead. Message text is escaped.
- When an invalid input is supplied, the frontend emits empty text rather than a PHP warning or exception.
- Existing `[years-since]` output **will not always match the old 1.5.0**: this fixes the old implementation's ignored `show`, invalid leap day logic and undefined month/day access. Confirm each important page.

## Internationalization

Translatable UI / plural strings use the `alp-dynamic-time` domain. Included compiled Spanish translations (es_ES / es_CR) cover the main PHP interface and standard unit labels; some JavaScript-only settings and native Divi fields remain English until editor i18n review. Other languages fall back to source English (translation contributions welcome).

## Cache and SEO

Server rendering updates when pages are generated. A page cache, CDN or stored SEO HTML can preserve stale values until expiry/purge. `live="true"` updates the number visible in browsers only, **not** the crawler's cached HTML or SEO plugin caches. The REST request sends only user-authored shortcode parameters; there are no external requests or analytics.

## Compatibility disclosure

WordPress 6.5+ / PHP 8.2+ **minimum requirements**. `Tested up to: 7.1` reflects the owner-reported WordPress 7.1.4 Plugin Check environment, not a comprehensive functional compatibility test. Check `docs/compatibility.md` and `docs/qa-report.md` for verified versus pending work. Install on staging and validate your Divi, multilingual and SEO setup before production rollout.

## Development / security

- Per-plugin globals prefixed `ALPDT_` / `alpdt_`.
- No third-party JavaScript runtime libraries, custom database tables or trackers. Halloween currently requests one Creepster font file from Google Fonts; self-hosting is pending before public release.
- Presets stored per site in non-autoloaded `alpdt_presets` option, limited to 200 items. Removing plugin does not delete this option automatically.
- Public REST is read-only, validates/string-limits requests and returns public computed numbers only. All admin mutations require `manage_options` and nonce, with escaped output.
- Install/release validation: `php tests/run.php`, PHP lint, `node --check` JS. WP Plugin Check, PHPCS/WPCS, PHPStan, live Divi and functional WordPress remain separate gates.

Licensed GPL-2.0-or-later. See LICENSE.

## Editing saved dates (0.9.1)

Open **ALP Dynamic Time & Countdowns → Saved dates**. Select **Edit** for an existing milestone, change its label or date, and choose **Save changes**. The identifier remains unchanged so all existing `[alpdt_time id="..."]` instances continue working. Use **Cancel editing** to return to the new-date form.

The Generator shows explanations for each type, year-calculation mode and unit. Only options relevant to the chosen type appear in the generated shortcode. The ALP dashboard hides third-party WordPress admin notices on its own screen only, while its own save/error feedback stays visible.

## Divi 5 simplified module (v0.9.21, supersedes the older controls below)

1. In **ALP Dynamic Time & Countdowns → Generator**, create your date, anniversary, copyright or countdown and **Save shortcode**.
2. Add **ALP Dynamic Time & Countdowns** to a Divi 5 layout.
3. Under **Content → Saved Shortcode**, select its name from the list. If the list is empty, create a saved shortcode and reload the Visual Builder.
4. Under **Design → Local Appearance Overrides**, optionally adjust the three colors (native Divi color pickers), numeric text size (12–110 px), font family and alignment. Empty choices inherit the saved style. Standard module background, spacing and border controls remain in Divi Design.
5. Save and reload. A change in Saved Shortcodes updates every module referencing that identifier; local Design overrides remain local to each module.

The old module's date/time/countdown inputs have been removed intentionally. **No pages used the old module**, per the owner. The identifier `alpdt/dynamic-time` stays intact, but old attribute values are no longer interpreted by the new module. For inline paragraphs use `[alpdt_time saved="identifier"]`. For Divi, a Code module with that shortcode remains an alternative. The private Visual Builder preview uses WordPress REST with edit permissions and a nonce. Browser/frontend correctness remains pending real Divi 5 testing.

### Historical notes
The release sections below document previous experimental versions; **do not follow their old Divi module settings instructions for 0.9.21 or later**.

## Divi 5 module improvements (v0.9.2)

The native **ALP Dynamic Time & Countdowns** module has three labeled groups under Content:

- **Dynamic Time**: choose a display type from Elapsed time, Current year, Current month, Current date, Countdown days, Copyright year range, Anniversary number, or Anniversary message. Additional fields include the start date, target date, saved date ID, calendar/anniversary mode, elapsed unit/word display, year offset, and date format. Unused settings are ignored by the active type, not deleted.
- **Display Text**: optional text before or after the generated value and the legacy Caption. Clear Caption for a one-line footer.
- **Anniversary Messages**: optional text for before/on/after the anniversary, with `{years}` placeholder.

**Single-year copyright footer:** Display type = Current year; Text before = `©`; Text after = `My Company`; Caption = empty. The output for 2026 is `© 2026 My Company`, and the year changes when WordPress renders the page in 2027. For a copyright range, select Copyright year range and set the Start year/date (e.g. `2000`) to get `2000–2026`.

Existing Divi modules preserve the same module name `alpdt/dynamic-time` and the original `date.innerContent` / `caption.innerContent` attribute paths. Unset Display type defaults to the previous elapsed calendar-years mode. The calculated number can no longer be inline-edited on the canvas, preventing unintended modification of the source date; Caption remains directly editable.

The Visual Builder preview computes in the editor browser timezone and may differ from the site timezone around a date rollover. The final PHP frontend uses WordPress timezone and locale. Saved date IDs and uncommon PHP date-format tokens display a preview fallback in the editor; the frontend renders them using WordPress. No live Divi Visual Builder or Theme Builder integration test has been performed.

## 0.9.25: Refined Hourglass and Orbit Motion

The existing **Hourglass** now displays glass chambers with a narrowed neck, sand draining from the top, a growing deposit at the bottom, and individual grains falling at staggered speeds. The motion follows the same idea demonstrated by [comehope's Hourglass loader](https://codepen.io/comehope/pen/VGegxr), but uses original, plugin-scoped SVG/CSS/JS, not the Pen's source. **Orbit Motion** now has a progress arc and two tilted orbital paths with moving satellites.

No new shortcode attributes or migration are required. Existing `clock_style="hourglass"` and `clock_style="orbit"` saved configurations display the redesigned styles automatically. Use the existing color controls (accent for sand/satellites, track for frame/orbits, card background and numeral/label colors) and the number-size control to scale artwork. There are no remote requests or additional JavaScript intervals. Animated grains are decorative and hidden from assistive technology; the numeric value remains the actual text. Offscreen and reduced-motion behavior is maintained.

**QA:** Isolated Chromium rendering at 1280/768/390/320px and code-level regressions were performed; **real WordPress/Divi 5, cache, actual mobile devices and complete accessibility certification were NOT performed**. Install in staging first.

## 0.9.24: Eight new countdown appearances (staging candidate)

In **Generator → Countdown → Style → Clock appearance**, choose from eight additional original ALP styles: **3D Odometer**, **Neon Digital**, **Liquid**, **Pixel Matrix**, **Orbit Motion**, **Cyber Glitch**, **Particle Morph**, and **Hourglass**. The style can be saved in **Saved Shortcodes** and displayed through `[alpdt_time saved="slug"]`, including the simplified Divi 5 module that references the saved shortcode.

Examples (replace target time as required):

```
[alpdt_time type="countdown" to="2027-01-01T12:00" countdown_display="full" clock_style="odometer"]
[alpdt_time type="countdown" to="2027-01-01T12:00" countdown_display="custom" clock_units="days,hours,minutes" clock_style="matrix" digit_color="#07B57B" card_color="#101C19"]
[alpdt_time type="countdown" to="2027-01-01T12:00" countdown_display="full" clock_style="hourglass" accent_color="#FFCC00"]
```

The eight new styles use the established `clock_style` option and the existing validated color, size, font and label attributes. No migrations, external APIs, remote fonts, third-party animation frameworks or changes to stored dates are necessary. All clocks initially output server-rendered text. For Odometer, Pixel Matrix and Particle Morph, JavaScript adds `aria-hidden` decorative representations while retaining the accessible original text. Decorative motion is suppressed by `prefers-reduced-motion` and paused when off-screen where IntersectionObserver is available. Existing full/custom countdowns and other styles continue using their previous markup and styling.

**Performance notes:** Pixel Matrix and Particle Morph need additional DOM nodes for each dot (5 × 7 per digit). Use these visually rich styles sparingly on pages with many concurrent timers. These effects do not require WebGL or a dedicated frame loop; they reuse the existing 1-second timer and only transition changed units. Chrome isolated fixture testing is not a real WordPress/Divi or production-page performance benchmark.

**Live Visual Builder/WordPress and full WCAG testing remain pending.**

## 0.9.4: custom countdown and Divi cleanup

- The plugin list uses **By ALP Plugins** with the author link `https://alpplugins.com` and a native **Settings** action.
- Divi 5 retains `alpdt/dynamic-time` and all prior stored attributes. Only the main module exposes the Layout decoration; date/caption keep their typography styling without duplicate Layout groups.
- **Clock Units (Custom mode)** in Divi 5 lets you enable any combination of years, months, days, hours, minutes and seconds. Defaults include days through seconds. Use Countdown display = Custom. For the shortcode, use `countdown_display="custom" clock_units="months,days"`.
- Style choices: `simple`, `glow`, `flip`, `slide`, `pulse`, `sweep`. They use original scoped CSS without third-party dependencies or copying CodePen source. Reduced-motion users receive the same values without animations.
- Calendar years/months are computed as calendar units in the site's WordPress timezone. Omitted larger units roll into smaller selected units. Selected smallest unit truncates lower precision; any remaining fraction is not shown.
- Old `days` and `full` clocks remain supported without rewriting their HTML. Select Custom to use the new segmented clock. The WordPress admin generator also exposes the independent units.
- **Line Hover Animation** is not registered in ALP Dynamic Time & Countdowns source; if present, investigate a Divi-wide setting registered by a separate plugin rather than hiding another plugin's UI through global CSS.

**Required staging verification:** Save/reopen existing Divi modules, verify only one Layout group, test Theme Builder footers, reduced motion, mobile wrapping, timezone boundaries, caches and active third-party Divi extensions. No live Divi or Plugin Check tests were performed in this environment.

## 0.9.5: options and compatibility

- Date inputs use the format **YYYY-MM-DD**: `2000-12-15` is **15 December 2000**, not an ambiguous 12/15.
- Generator drop-downs have an ALP-style accessible enhanced view (mouse, keyboard), but Divi's native controls remain untouched.
- *Countdown appearance* has hinged flip, rolling slide, pulse, underline, glow, minimal and circular dial. Styles are original CSS/JS based on interaction ideas, not bundled CodePen code. Reduced motion disables the digit animations. The current UI preview is a mockable browser rendering, not certified in WP/Divi.
- Divi 5: the frontend still renders with PHP; the Visual Builder now queries its own REST endpoint for values and attempts to display a live countdown using the same number-transition CSS. Builder runtime correctness requires validation on a real Divi site.
- SEO: in a supported SEO title or description field (Rank Math, Yoast, SEOPress) enter `[alpdt_time type="year"]` or `More than [alpdt_time from="2000" mode="calendar" text="false"] years of experience`. The value is replaced by registered filters in the published metadata; the SEO editor preview might still show raw shortcode, and caches can delay yearly changes.
- The **Years Since - Timeless Texts plugin** was removed from WordPress.org. ALP Dynamic Time & Countdowns independently recognizes certain old shortcodes but is not affiliated with or endorsed by the original authors. Validate old results in staging.


### Countdown appearance (0.9.6 candidate)

The *Generator* exposes colors for numbers, labels, cards, progress and track; ring diameter; numeric and label sizes; and a font-family selector. In Divi 5 these inputs are available under the native **Countdown Appearance & Animation** group (hex colors and pixel sizes). For example:

```text
[alpdt_time type="countdown" to="2027-12-31T23:59" countdown_display="custom" clock_units="days,hours,minutes,seconds" clock_style="flip" digit_color="#de4848" card_color="#ffffff" digit_size="66" label_size="14" clock_font="system"]

[alpdt_time type="countdown" to="2027-12-31T23:59" countdown_display="custom" clock_units="days,hours,minutes" clock_style="ring" clock_size="190" digit_size="48" label_size="14" digit_color="#243665" accent_color="#2fc5b5" track_color="#e1e8f0"]
```

The PHP renderer outputs real text, while the JavaScript animates the values. No CodePen libraries or external animation services are loaded. A real Divi 5/WordPress instance is still required to verify Visual Builder parity and Theme Builder behavior.

### Flip clock refinements (0.9.7)
- Flip digits use a dark-card design with colon separators and unit labels underneath. On a narrow display the units reflow into two rows, while the characters, their hinges and frames scale with the selected number size.
- The upper half turns and updates before the lower half. Flip animation is decorative; the current value remains available as accessible text, and reduced-motion users see a static clock.
- Dashboard color choices show a HEX value first and a synchronized color swatch to the right; the visual theme is a suggestion and individually chosen colors are preserved.
- Divi's module design remains native and uses the saved typography options; the live module still requires verification on the installed Divi 5 version.

### 0.9.9 appearance usability
- Color swatches open a plugin-owned picker displaying the hexadecimal value below the color controls (no browser-native RGB popup). The adjacent HEX text field remains editable.
- **Reset appearance to defaults** restores only color, font and size options for the selected clock style. It does not reset the target date or selected units.
- Circular progress rings scale with number size: `clock_size` controls the base diameter at a 60px numeral size. This also works in Divi 5 and shortcode markup.

### Countdown labels and font fallback (0.9.9)
Clock units use WordPress's active language automatically. To override any unit for a particular clock, enter a caption in **Generator → Countdown unit labels**, or in **Divi 5 → Countdown Appearance & Animation**. The shortcode supports `label_days="Días"`, `label_hours="Horas"`, `label_minutes="Minutos"`, `label_seconds="Segundos"`, `label_years="Años"`, and `label_months="Meses"`. Empty fields keep the built-in translation. Captions are plain text, escaped on render, limited to 32 characters.

The **Lato** option does not bundle or load a webfont. The plugin uses Lato only when already available to the site; otherwise browsers fall back to Arial. This avoids adding third-party font requests.

### Flip Digits punctuation alignment (0.9.10)
The orange colon separators are visually centered with the split-flap cards using a font-relative optical correction. The earlier CSS-only line-box centering caused the visible punctuation to sit low, especially with large digits. The correction does not alter card size, shortcode attributes or saved Divi module fields. In the narrow two-column mobile layout, separators remain hidden to avoid punctuation between unrelated rows.

### 0.9.11: Shortcode copying and SEO variables

- In **Generator**, click **Copy shortcode** next to the output. The same copy action now appears next to each snippet in **About & guide** and beside shortcodes under **Saved dates**. An icon and green check confirm actual clipboard success; if clipboard permission is denied, the UI selects example text when possible and explains manual copying.
- **Anniversary message** offers editable **Before / On / After** fields in the generator. Use `{years}` for the anniversary number. For the correct anniversary day, use a full date, e.g. `2000-12-15`.
- In Rank Math use `%alpdt_year%` for the current year or `%alpdt_since_company_founded%` for a saved date with identifier `company-founded`.
- In Yoast use `%%alpdt_year%%` or `%%alpdt_since_company_founded%%`. The custom variables are registered with the plugins' public variable APIs. Shortcode-based metadata replacement remains for Yoast, Rank Math and SEOPress. The SEO editor's character counter may still reflect the placeholder length or an example, not the final published length; test actual metadata after save.
- Legacy scans are read-only and show only posts containing `[years-since` in their main content. This plugin is independent of the removed Years Since - Timeless Texts plugin.
- All WordPress/Divi and SEO integrations require validation in an actual site; standalone PHP and browser-mock tests are not equivalent.

### 0.9.13: Countdown controls and flip panel background

Custom live clocks place **Countdown Units** first, followed by **Style** and **Unit Labels** tabs. The tabs change the editor layout only; they do not modify shortcode options, saved dates or units. Keyboard arrows, Home and End switch tabs. On **Style**, Flip Digits provides a separate **Flip panel background** control: enter a six-digit HEX color or check **Transparent / remove panel background**. Cards retain their independent background color. The corresponding shortcode option is `panel_color="#0F0F0F"` or `panel_color="transparent"`. The Divi 5 module includes a text field for the same value; real Visual Builder validation remains pending.

### Quick Colors (0.9.14)
The admin color picker begins with black (`#000000`), red (`#F33B49`), blue (`#053197`), green (`#07B57B`), yellow (`#FFCC00`) and white (`#FFFFFF`). Previous preset pastels are upgraded when found in saved user palettes; other custom swatches and explicitly empty palettes are preserved. Pick any HEX/HEX-alpha color and click **+** to add it to Quick Colors. Use the red **×** on a color swatch to remove it after confirmation. Your palette is saved to the administrator's WordPress account, not the public website; it follows that user between browsers. Removing a swatch does not change previously configured clocks or shortcodes. Up to 24 unique swatches can be saved.

## Saved shortcodes (0.9.15)

In **ALP Dynamic Time & Countdowns → Generator**, choose any dynamic text or countdown and click **Save shortcode**. Enter a recognizable name and a unique identifier. The **Saved shortcodes** tab offers two copy options:

- **Reusable:** `[alpdt_time saved="new-year-countdown"]` resolves its full settings each time a page is rendered. Editing the named configuration updates all instances that use this reference.
- **Complete configuration:** Expand **Show complete configuration** to copy the full `[alpdt_time type="…" ...]` shortcode. The copied text is independent and will **not** change when the saved item is edited.

Choose **Edit** to open the saved configuration in Generator and use **Update saved shortcode**. A saved identifier is immutable. Deleting a configuration makes its reusable shortcode produce no output, without changing pasted full configurations. Saved dates are a **separate** library. Configurations are stored in the site option `alpdt_saved_shortcodes` (maximum 100) and are not erased automatically on uninstall. Mutations require administrator capability and a nonce; they never write to post/page content.

### SEO variables for generated text (0.9.17)

The Generator displays **Copy shortcode** for every type. For non-countdown types it also provides **Copy SEO variable** with a Rank Math/Yoast switch. The unmodified current year can be copied immediately (`%alpdt_year%` or `%%alpdt_year%%`). Other text configurations should first be saved as a named Saved Shortcode; reopening that configuration enables its registered SEO variable. Saved Shortcodes also provide a copy action for each supported provider. Live countdowns are intentionally excluded. This does not guarantee exact live character counts in SEO editor previews; check published metadata.


### Version 0.9.26 display refinements
The Generator preview stays visible as you scroll the countdown controls on desktop; on narrow screens it flows normally. Pixel Matrix and Particle Morph digits are centered, the Hourglass reflection remains inside the glass, and Fireworks has brighter, layered bursts. All shortcode options remain backward compatible.


### Version 0.9.27: Fireworks inspired by Pure CSS Fireworks (motion study)

The **Fireworks Celebration** countdown triggers a single short visual celebration when an actively running countdown reaches zero. You can also use **Preview fireworks** while editing; it does not change the saved deadline. Previously expired countdowns only show the final message, without replaying fireworks upon every page load.

The new effect uses irregular, two-layer scatters of colorful particles with gravitational fall and **preserves the soft outer glow**. It replaces the previous ring-like, spiral-looking trajectories. The configured accent color, panel HEX/transparent color, final message and 3/5/8 second duration remain available. Reduced-motion visitors see the final message without particles.

Inspired by the behavior of [Pure CSS Fireworks by yshlin](https://codepen.io/yshlin/pen/WNMmQX); independently coded, with no copied assets, Sass/CSS, or third-party library. WordPress + Divi runtime verification remains pending.


## ALP Dynamic Time & Countdowns 0.10.1 (development)

Features: dynamic calendar expressions (`type="smart_date"`), recurring daily/weekly/monthly countdowns in the WordPress timezone, fixed-timer completion actions (message, hide, internal redirect), and the existing 18 animation choices. The original plugin folder, text domain, shortcode name (`alpdt_time`), saved settings, and Divi module slug remain unchanged for upgrades.

Examples:

```text
[alpdt_time type="smart_date" expression="next_friday" format="Y-m-d" live="true"]
[alpdt_time type="smart_date" expression="date_difference" from="2026-01-01" to="2026-12-31"]
[alpdt_time type="countdown" recurrence="weekly" repeat_weekday="5" repeat_time="18:00" countdown_display="full" clock_style="flip"]
[alpdt_time type="countdown" to="2027-01-01T12:00" countdown_display="full" end_action="message" end_message="Happy New Year!"]
```

**Limitations:** post date expressions require a published-post context and should not use `live=true` (REST does not know the post context); SEO editors may cache previews; a visitor must have JavaScript enabled to apply completion actions or advance an already-expired recurring timer in a cached page. Server-rendered countdown text is still provided as a fallback.

**Verification:** standalone PHP date and security checks are not equivalent to WordPress activation, scheduled cron, browser UI testing, Divi Visual Builder testing or Plugin Check. Verify WordPress 7.1.x, PHP 8.2 and PHP 8.3, SEO integrations, theme/page cache, DST boundaries, saved configuration editing and recurring timers on staging before release.


### Version 0.10.2: Minimalist inside Clock appearance
The Generator now offers one **Minimalist** countdown style, with the numeral and a short unit indicator side by side. Use existing color/size/label controls to style it for top bars and campaigns. The previous `promo_dark`, `promo_light`, and `promo_outline` values remain accepted in existing saved shortcodes but are no longer selectable as separate styles.

Example: `[alpdt_time type="countdown" to="2027-11-26T23:59" countdown_display="full" clock_style="minimalist"]`.

### Countdown layout (0.10.3)
Choose an existing Clock appearance and then **Countdown layout → Minimalist / Horizontal** to show the initial of each translated unit after its digit without changing its animation. The default layout remains unchanged. The shortcode stores this as `clock_layout="horizontal"`.

### 0.10.4 Horizontal layout and compact Blood Dripping

- `clock_layout="horizontal"` also works with `clock_style="ring"` and `clock_style="confetti"` while preserving their native animations.
- Horizontal Flip digit separators no longer overlap unit initials.
- Blood Dripping digits at 44px or smaller use a clean regular typeface without the goo filter so the counter stays readable; droplets and puddles are retained.

### 0.10.8 Countdown appearance updates

Only relevant design fields are shown per selected clock appearance. **Circle diameter (px)** applies to Circular Progress Dial, Liquid and Orbit. Leave **Countdown padding (px)** blank for each style’s original spacing or choose 0–80 pixels to change spacing around the full countdown. The saved shortcode uses `clock_padding="16"` when configured. Blood Dripping offers **Creepster Regular 400** (default) or **Nosifer Regular 400** under its own font selector; other styles keep their font controls. The two decorative fonts are requested from Google Fonts only when selected, so sites may need a suitable Content Security Policy for fonts.gstatic.com.

### 0.10.13 countdown refinements

- When Christmas Lights is placed in Horizontal with explicit top padding below 48px, bulbs remain **behind** numerals and inside the rounded panel. Larger/default padding uses the original raised light strand.
- `clock_radius` is an optional 0–80px display value for countdown backgrounds and visible cards. Blank means the original radius; ring, liquid and orbit retain their circular artwork.
- Snowfall reserves fixed widths for proportional Snowburst One figures, reducing layout shifts during countdown ticks.
