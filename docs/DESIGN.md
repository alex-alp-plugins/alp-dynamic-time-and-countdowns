# ALP Dynamic Time | Visual design contract v0.9.4

## Scope and identity
- Admin screen **ALP Dynamic Time** as a standalone, top-level WordPress admin menu entry. Use `.alpdt-admin` scope only, including alerts and dialogs. Keep third-party admin/Divi styles intact.
- ALP primary `#243665`, accent `#2fc5b5`, danger `#b42332`; default radius 5px, slightly rounded hero/card 7px.
- System WordPress admin sans-serif font; no remote fonts, external icon libraries or assets. Contrast, mobile and RTL must be validated on a real installation before release approval.
- Hero: centered max-width 1430px dashboard; dark navy gradient header with subtle animated halo but **no colored edge/border stripe**, product title and version badge **inside** header, gently animated clock and orbits (static when reduced motion is requested). Keep the hero free of third-party notices, without suppressing legitimate WordPress, SEO or security alerts: show them above the hero in a dedicated visible region. Own inline feedback remains outside the hero.

## Layout rhythm
- Overall content width centered via automatic inline margins.
- Hero → tabs 24px; tabs → panel 26px; section intro → cards 26px; card padding 28–30px; label → control 9px; helper text below control; field → field 23px; textarea → copy button 18px.
- Under 960px, two columns become one, centered up to 770px. Under 600px, reduce card padding and stack saved-date actions.
- Inputs require at least 14px inline padding, a visible label, explanatory helper text, and focus outlines. Placeholders are examples, not label substitutes.

## Interaction contracts
- Tabs implement click plus arrow/Home/End navigation without reload.
- Saved dates: new and edit use one form. Editing does not change identifier, so existing shortcodes are preserved; cancel restores untouched new-date form. Duplicate identifiers are refused by server.
- Delete is red in list and confirmation. Native dialog with cancel/Escape and returning focus to origin; no native alert/confirm.
- Generator updates visible fields and shortcode by type. Live preview fetches the same public render endpoint as v0.9.0 and gives meaningful error/empty feedback.
- Own save/delete/error feedback uses `.alpdt-feedback`, not WordPress global notices. Suppress third-party `admin_notices` only on this screen.

## QA boundaries
- PHP/JS syntactic and stub tests record actual runs, no CSS visual claim from static review.
- Real WordPress/Divi browser checks and ALP/WordPress Plugin Check are required before production release.

## Divi 5 controls contract (v0.9.3)
- Label the Content groups explicitly: **Dynamic Time**, **Display Text**, **Anniversary Messages**. Never render an unlabeled accordion.
- Use Divi native field components; do not inject CSS into the builder's native panels, menus or icons.
- Keep data/format/mode controls together, separate optional display text and messages, and explain which types use each field. Do not silently delete inapplicable saved field values.
- Keep saved module identifiers and original date/caption persisted attribute paths stable. Avoid inline-editing computed date values.

## 0.9.3 additions
- Native selects use a locally defined ALP chevron, minimum 14px content padding, explicit hover/focus states and visible helper text; never hijack Divi controls.
- Clipboard success state shows a check in a circular pale-green icon and a readable green confirmation, only after the Clipboard API resolves successfully.
- SafeEdit-style deletion dialog: explicit named action, consequence description, red confirm, Cancel, Escape/focus restore and server-side nonce/capability validation.
- Date format uses semantic, localized labels with concrete examples. Timestamp inputs clearly specify YYYY-MM-DD and the site timezone.
- Live countdown provides a minimal inline appearance or optional highlighted glow. Animations do not play for reduced-motion preference; clock output is accessible without requiring the animation.

## 0.9.4 contract
- Hero clock: exactly one animated hand; second hand stays fixed. Honor reduced motion.
- Only one Divi Layout group: keep the module-level layout; do not register duplicates under date/caption sub-elements.
- Clock: independent units for Custom display, six accessible, labeled controls, styles scoped to `.alpdt-clock-*`. Never add another plugin’s settings or global Divi styles.
- Plugin list: By ALP Plugins linked to https://alpplugins.com; Settings link goes to the registered top-level ALP Dynamic Time screen.

## 0.9.5 refinements (supersede the 0.9.4 one-hand animation note)
- Both hero clock hands move independently, a slow hour hand and faster minute hand, with a centered pivot; static for reduced motion.
- Generator selects progressively enhance to an ALP listbox with white panel, restrained shadow, selected check, arrows/Home/End/Escape and the original native control as a no-JS fallback. This enhancement never runs inside Divi.
- About & guide explains date formats, shortcode usage, SEO filters and independent legacy compatibility; date order explanation appears beside the input as well.
- Flip displays split-flap halves transitioning around a horizontal pivot; Slide displays an actual rolling previous/new number. Ring uses a conic progress dial. Timer text remains readable without animation.
- Divi 5 uses native controls in a named animation group; computational value is not inline-editable. PHP frontend is authoritative; VB may query the public REST renderer with a debounced request and a local fallback. Real builder behavior needs staging validation.


## 0.9.6 clock appearance contract
- Flip follows the **layout and movement** of the approved Dorian Cami reference: bright cards, individual figures for each character, red glyphs by default, horizontal split and 3D two-half rotation; no runtime dependencies or source copy. Hidden numeric text remains accessible in JS-enhanced state; when JS is disabled the numeric fallback stays visible.
- Circular progress uses normalized SVG `pathLength=100`, an actual arc with time-based updates instead of conic-gradient approximations. Default diameter 170px, user adjustable 90–300px; its progress track and arc colors are independent.
- Custom clock design: five hex colors, number font size 18–110px, label font size 10–32px, ring diameter 90–300px, seven whitelisted font choices (including Lato if installed). All settings are validated server-side and scoped under `.alpdt-clock-grid` to avoid overriding Divi styles.
- Reduced-motion removes active hinges/transitions while keeping the numeric output. Never change persisted date or shortcode semantics just to enable styling.

## 0.9.7 flip refinement (supersedes light-card default from 0.9.6)
- Reference-inspired flip: near-black panel and cards, light primary digits, orange seconds and orange colon separators between time units; labels **below** cards. Colors remain customizable and scoped to `.alpdt-clock-flip` only.
- Actual card width, height, hinge and gaps are relative to the computed numeral size, not fixed independent dimensions. A typography change must rescale both digit and physical card.
- Sequencing: top half transitions before bottom half; no bottom update before the top hinge completes. Transitions are removed under `prefers-reduced-motion`.
- Responsive: columns wrap; small screens use two readable columns with no orphaned separators or horizontal overflow.
- Dashboard appearance color controls: semantic ALP label above an editable #RRGGBB field, synchronized swatch to right, sane intrinsic sizing and 320px reflow. Selectors remain isolated to ALP dashboard; don't replace native Divi controls.

## 0.9.8 appearance refinements
- Flip colons must be vertically centered against the card's current numeral-size-derived height, not placed at a fixed `top` offset.
- Circular progress ring diameter scales by the numeral-size ratio (60px is the reference size), with `clock_size` remaining an independent base diameter control.
- Dashboard color popovers are ALP-owned: always show a HEX value and allow edits by field, hue slider and saturation/brightness square; never rely on the browser's RGB-first native color popup.
- Restore Appearance resets only visual choices, preserving dates, clock mode and selected units.

### 0.9.9 UX addition
- Clock labels default to the WordPress locale and can be overridden per unit with text fields (ALP spacing, labels, responsive one-column fallback at narrow widths).
- Sliding overlay typography must match the rendered digit's computed CSS, including Divi typography overrides and responsive font size, and never rely on a historical clamp.
- Lato is a CSS fallback preference only, no externally loaded font.

## Countdown editor groups (0.9.12)
- Custom clock controls use scoped accessible nested tabs: Countdown Units, Style and Unit Labels, with keyboard arrow/Home/End, and restore previously entered values when changing tabs.
- Flip Digits panel background is independent from each digit card. Supports a validated six-digit HEX value or `transparent`; no arbitrary CSS.
- Preserve all ALP dropdown, SafeEdit, hero/footer and responsive requirements.

## 0.9.13 color picker
- Divi-inspired appearance in plugin-owned wp-admin controls only: dark titled popover, large saturation map, hue and alpha tracks, HEX input, opacity percentage and ALP quick palette. No Divi global-color integration.
- Opacity uses CSS 8-digit HEX; existing 6-digit HEX is unchanged. Keyboard, touch, Escape/focus return and constrained popup positioning are required.

## 0.9.14: User-editable Quick Colors
- Six base choices (black `#000000`, red `#F33B49`, blue `#053197`, green `#07B57B`, yellow `#FFCC00`, white `#FFFFFF`), plus adds the current color, visible red X removes via accessible confirmation. Existing personal palettes retain custom swatches; old preset pastel values are migrated by exact HEX match.
- Palette is scoped to each admin's user meta and shared among ALP color pickers; removal does not change existing countdown values.
- Keep dark popup palette and focus/keyboard/touch states, without styling Divi native picker.

## 0.9.15 saved shortcode library
- Keep the ALP primary/secondary CTA separation: **Copy shortcode** primary and **Save shortcode** secondary, with a named SafeEdit-style save modal.
- Add **Saved shortcodes** as a top-level dashboard tab alongside Generator/Saved dates; do not mix date-only presets with full saved shortcode configurations.
- Copy controls, naming fields and error states must match existing ALP spacing, responsiveness and keyboard operation. Delete remains red with confirmation.
- Reusable shortcode IDs are immutable; updating a stored item changes references globally. Show both alias and complete independent shortcode to avoid confusion.

## 0.9.17: Actions and SEO variable discovery
- Quick Colors has no permanent overlay of red X controls on every swatch; users enter Manage colors to reveal removal. Add color is a separate, labeled toolbar action. Preserve SafeEdit confirmation and accessibility.
- All generator actions must have consistent spacing, feedback and mobile wrapping. Text modes provide Copy shortcode and a provider-specific registered SEO variable, only enabled when a valid SEO variable exists. Saved text shortcodes register stable SEO variables; avoid unregistered markers.

## 0.9.18 dashboard tab icons
- All five top-level dashboard tabs include a decorative left icon, retaining visible accessible text labels.
- Generator uses user-supplied brackets.svg; Saved dates calendar.svg; Compatibility compatibility.svg; Saved shortcodes bookmark.svg and About & guide book.svg.
- Icon color inherits hover/active/focus text color through CSS masks and respects the tab's existing aria-selected state. No global WordPress/Divi CSS changes.

## 0.9.21 Divi 5 simplified module (supersedes v0.9.3–0.9.20 module UI)
- Content panel contains only a native Divi selector for a named Saved Shortcode; all date, format, animation and clock configuration stays in the WordPress dashboard.
- Design panel adds a single **Local Appearance Overrides** group. Colors use Divi's own `divi/color-picker`; font size, family and alignment are optional. No custom ALP controls are injected into Divi's native panels.
- Local overrides apply to the selected module instance only. They must never mutate the saved shortcode option or its shortcode output elsewhere.
- Keep Divi's native module decoration controls and the existing module ID. Runtime endpoint is edit-permission protected and previews the authoritative PHP shortcode output.
- Previous Divi module guidance is retained solely as historical provenance, not current user guidance. No previously used instances require automatic conversion (owner confirmed none).
- Visual/functional correctness in an actual Divi 5 Visual Builder, Theme Builder, responsive modes, keyboard, i18n and frontend still require live QA.
- As of 0.10.28-rc.2, the module library icon must reuse the official `assets/branding/isotype-color.svg` silhouette in Divi’s single-color, 16×16 icon system; set `moduleIcon` consistently in PHP/JS metadata and register through `divi.iconLibrary.icon.map`. No frontend logo injection, no change to `alpdt/dynamic-time` or saved attributes. Real insertion-panel QA remains required.

## 0.9.22: Guide, hero and celebration
- About & guide has four accessible nested tabs: How it works, Examples, SEO plugins and Divi. Keep all existing shortcode-copy controls within those panels.
- Hero retains the existing two chips and adds ONLY Divi 5 ready, SEO variables, and Countdowns, with existing icon/border pattern and responsive wrapping. Do not add a PHP-security hero chip.
- Compatibility displays a PHP 8.2+ recommendation with fixed PHP2Shell patch releases. As of 0.9.43 the declared minimum PHP requirement is 8.2; use a patched 8.2+ release.
- Fireworks celebration is an explicit countdown appearance. It shows an optional completed message and a bounded, light particle burst once when a live countdown reaches zero; never run particles on old expired clocks opened after the target. Motion-reduced users see the message without animation. A preview button in Style triggers only the visual burst. This style does not load external libraries.

## 0.9.23 | Hero and Fireworks update (overrides the 0.9.22 chip wrapping decision)
- The hero displays exactly four badges, in **one row**: Dynamic shortcodes, Reusable milestones, SEO variables and Countdowns. Omit Divi 5 ready. Reduce chip typography/icons in this row. On narrow screens permit horizontal scrolling instead of breaking to another line; keep keyboard focus available.
- Fireworks uses the existing validated `panel_color` shortcode attribute and ALP color picker for its backdrop, independent of card/digit color. Allow the existing `transparent` toggle and keep the same stored shortcode contract for Flip Digits. Do not touch Divi-native controls.

## 0.9.24 | Countdown appearance additions

Eight new clock appearances are additive styles under the existing `alpdt-clock-*` CSS scope: odometer (rolling individual drums), neon (glow with short ignition), liquid (wave-filled circle), matrix (five-by-seven LED dots), orbit (two rotating rings), glitch (RGB split only on digit changes), morph (dot scatter/reassembly), and hourglass (two sand chambers with stream). All retain the same PHP `role="timer"` content, WordPress translation labels, user design tokens, settings panel and saved shortcodes. Do not set a global HTML background or replace the ALP color picker. Animation must honor reduced motion and offscreen visibility; no WebGL, third-party CSS/JS, or remote fonts. Both accessible text and static fallback must survive unavailable JavaScript; DOM-heavy dot styles remain opt-in. The gallery is a QA preview, not an automatically installed dashboard feature.

## 0.9.25 | Illustrated countdown refinements
- Preserve the existing `orbit` and `hourglass` appearance slugs and shortcode/preset settings. They are **redesigned**, not new content types.
- Hourglass must read as a glass vessel: independent upper/lower chambers, narrow neck, contoured frame, upper depletion and lower accumulation based on the chosen clock unit. Animated grains have offset timing and gravity-shaped movement, but no dedicated JS timer; decorative art has `aria-hidden` and no HTML injection. Reduced motion disables the grains while retaining the real numeric value. The Pen reference is conceptual only, not a source-code dependency.
- Orbit shows a legible time-progress arc plus satellite markers on tilted tracks. Avoid using two identical dashed, endlessly spinning borders as the entire effect. Orbit color, size, type and labels use existing tokens.
- On <=600px, both illustrated styles use two columns for four units and confine the orbital overspill to their own grid without clipping focus controls or hiding page content. No global WP/Divi selectors.

## 0.9.31 | Divi native typography, global colors and celebration polish
- Preserve the simplified Saved Shortcode selector. Local Divi colors must resolve `gcid`/`$variable(...)$` references through official Divi GlobalData methods; never store resolved colors back into the saved shortcode library. Validate the resolved output before producing CSS, allowing HEX and RGB(A) only.
- Use native Divi font controls for local shortcode typography instead of custom numeric/text select fields. Keep previously stored localFontSize/localFontFamily/localAlignment attributes for compatibility but do not display duplicate controls. Native typography is scoped to `.alpdt_d5_value`, not the module wrapper. Dot-matrix and Blood visual styles retain their special-purpose glyph typefaces.
- Liquid wave and fill have the same color, transition timing and deliberate overlap at the liquid line. No pale band or disconnected highlight while moving.
- Celebration Confetti (0.9.36): a single center-origin Boom burst of existing paper, triangle/circle and short streamer forms expands rapidly, then gravity makes pieces fall. Preserve the six independently colored balloons and their existing curved ascents. Repeat the 2.4-second burst at unchanged speed during the selected 3/5/8-second duration. Overlays remain aria-hidden, transient, bounded, paused offscreen and disabled for reduced motion; no third-party JS or canvas-confetti.
- Halloween Blood (0.9.36): keep droplet width and height identical, square aspect ratio (1:1), a rounded-corner teardrop rotated 45 degrees and uniform scaling at all frames. Position the small red pool lower beneath the numeral and reserve space before the unit label. Preserve scoped SVG goo, user colors and font selection rules.
- Do not claim real Divi 5 UI parity until the actual Visual Builder shows the correct native controls, global color updating and saved-page/frontend consistency.

## 0.9.34 product-branding integration
- User-supplied official logo preserved in vector form: `assets/branding/logo-white.svg` for the existing dark hero, `logo-color.svg` in the dashboard footer, and `isotype-color.svg` in the Gutenberg block picker. All are local assets.
- Keep the hero clock animation, title semantics (image alt names the heading), native builder controls and frontend output unchanged. No duplicate visible text title is added above the wordmark.
- The footer includes the product logo plus the official ALP Plugins corporate SVG lockup and link. Do not substitute the product icon for the company mark.

## 0.9.37 animation refinements (current)
- Confetti: opposing paper-cannon launch from lower left/right with asymmetric timing, trajectories and settling motion; keep the established paper/ribbon shapes, editable color palettes, balloon movement, accessibility and shortcode behavior. Exact equivalence to the supplied CodePen remains unverified because that Pen could not be loaded in the working environment.
- Halloween: pool 88% of number width at 1.59em below number start; each unit has 4–6 drops with stable seeded timing, varied teardrop shape and size, intrinsic square 1:1 aspect ratio. Never regenerate drops on each clock tick.

## 0.9.38 Celebration confetti reference (supersedes earlier launch-path instructions)
- The provided `image(20261106-041339).png` is a **single still**: black 512×512 canvas and a tiny blocky teal/orange cluster centered near (73%, 96%). It is not an animated reference and cannot establish a frame-by-frame motion curve.
- Confetti launch originates near the lower right within the shortcode, starts compact, ejects upward and spreads, then falls naturally with gravity and slight flutter. Render tiny square/irregular chips and occasional thin streamers with no external library; keep six established balloons unchanged and existing saved palettes. Do not change the entire WordPress page background or make the widget non-responsive.
- Confetti is temporary, scoped, disabled under reduced-motion preference and pauses when hidden/offscreen. The saved `clock_style=confetti` and duration 3/5/8 are backward compatible.
- The `blood` display label is now **Blood Dripping**, without the Halloween qualifier. The data slug is unchanged.

## 0.9.40 approved celebration timing and shortcode naming
- Preserve the approved two-sided Canvas confetti shower, small angular paper fragments, streamers, independently configurable colors and all six upward-moving balloons. Right-hand confetti starts first; left-hand confetti follows **300ms** later on each repeated burst, with no extra pause in gravity or particle motion. Keep the clock, reduced-motion behavior and short 3/5/8-second duration unchanged.
- Use `[alpdt_time]` as the only registered ALP Dynamic Time shortcode (owner confirmed older `[alp_time]` was used only on test sites). Do not rewrite old stored data automatically. Leave independent Years Since interoperability aliases unchanged.


## ALP mandatory WordPress admin-menu isotype (2026-11-06)
- For any ALP plugin with a top-level WordPress admin menu, the icon MUST be its approved standalone white official isotype (no text lockup, no generic Dashicons substitution).
- Show pure white `#FFFFFF`, `opacity: 1`, `filter: none` in inactive/hover/active states. WordPress's default opacity on `.wp-menu-image img` must be overridden only for this plugin's menu entry.
- Restrict the rendered img to `width: 20px; max-width: 20px; height: 20px; max-height: 20px; object-fit: contain;` and center within the standard 36px WordPress menu icon column. Preserve aspect ratio; avoid oversize, cropping, stretching or global admin CSS.
- The icon styling must load throughout wp-admin, since the menu appears on every screen, not only on the plugin's dashboard. Verify it on active/inactive sidebar states, desktop and collapsed/mobile navigation.

## 0.10.3 | Countdown layout: Default or Minimalist / Horizontal
- The new arrangement is a **layout option** adjacent to `Clock appearance`, not a replacement countdown style or effect.
- Preserve the native animation and all digit effects of each supported style. Layout changes are scoped to `.alpdt-layout-horizontal` on the existing `.alpdt-clock-*` root, with label initials after the existing number structure.
- Translate labels first; display only their first grapheme when horizontal, retaining full translated/customized label for accessibility. Use existing digit and label colors/sizing controls.
- Default preserves previous display. Never expose `Minimalist` as a separate selectable style in the new generator. Retain legacy PHP/CSS style registration only to render saved 0.10.1/0.10.2 shortcode configurations.
- Top bars need compact, wrapping-safe layout for 320/375/768px, without global Divi styles. Verify JS effects and actual WordPress/Divi behavior before publishing.

## 0.10.6 product branding and padding decision
- The user supplied six official ALP Dynamic Time & Countdowns SVG assets on 2026-11-13. They replace the former product branding without path/color edits: isotype-color.svg, isotype-white.svg, logo-color.svg, logo-white.svg and vertical variants. Use the white isotype at a maximum width/height of 20px in the WordPress menu with opacity 1, scoped to this plugin only.
- In the dashboard, the actual user-provided white horizontal product logo replaces the old icon + text treatment; the product footer uses the revised color product logo.
- The ALP Plugins corporate logo is now available as `assets/branding/alp-plugins-logo.svg` and is used in the footer alongside the product logo.
- Per-unit numeric container padding has a global / four-sides mode, preserving legacy uniform `clock_padding` data. Fixed circular dials receive margin, not asymmetric interior padding, preserving round shapes and progress graphics.

## 0.10.8 · Seasonal appearances

- `snowfall` and `christmas_lights` are separate **Clock appearance** styles, not WordPress-wide effects. Snow uses static server-rendered decorative snowflake markup and CSS-only motion; no canvas, trackers or particle JavaScript.
- Christmas lights uses 14 decorative bulbs and CSS animation, with the validated `lights_mode` (`multicolor` / `warm`) option. Preserve existing countdown layout, labels, colors, pad values, recurrence and accessibility.
- Snowburst One Regular 400 is from the official Google Fonts service (SIL OFL 1.1), fetched only for the selected frontend style or the isolated admin generator. Document external host usage.
- Neither style replaces the product logo or official ALP Plugins footer.

## 0.10.12 horizontal panel and seasonal control contract
- In Horizontal, an explicitly configured padding value is authoritative for the outer panel as well as numeric containers. In particular, zero does not leave appearance padding behind; empty continues to inherit the built-in style.
- Snowfall and Christmas Lights show only effective color/size/typography/layout controls. Christmas Lights has five bulb color controls; Snowfall uses Snowburst One instead of a general font selector. Preserve animations and reduced-motion preferences.
- The full public product name is **ALP Dynamic Time & Countdowns**.

## 0.10.13 clock-card corner and seasonal compact layout
- The `clock_radius` option is a 0–80px integer applying to visible panels/rectangular cards on supported countdown appearances. Empty leaves style defaults unchanged; never deform circle/arc/rotating orbital artwork.
- The Horizontal Christmas Lights style moves the strand inside the clock and behind digits only when an explicit top padding of less than 48px is set (including 0px); otherwise preserve original raised lighting. No decorative overflow beyond the clock in compact mode.
- Snowfall reserves stable numeric slot widths because Snowburst One has proportional numeral glyphs; keep normal digit-size settings, 96 decorative flakes, CSS-only animation, and semantic DOM values.

## 0.10.15 scoped design editor
- Five visual groups: Colors, Size & shape, Spacing, Christmas bulb colors (when applicable), Typography. Preserve ALP dropdown and color picker interaction/keyboard behavior; keep hidden fields serialized in saved shortcodes as required.
- Selected font may trigger a whitelist-only Google Fonts CSS2 request. Do not bulk-load all fonts. Snowfall defaults to Snowburst One; Blood Dripping retains its dedicated Creepster/Nosifer selector. Preserve default styling for existing shortcodes with no `clock_font` setting.
- Christmas Lights horizontal must keep bulbs visible in a shallow upper strip with compact number cards. Snowfall numerals retain stable width as they tick. Explicit user padding must take priority over seasonal defaults.


## 0.10.16 | Approved controls and Snowfall compactness (supersedes obsolete Snowfall font-selector notes)
- Generator Appearance panel order: Colors, Typography, Size & shape only for circle-based styles, Spacing, then seasonal bulb palette where relevant.
- Typography owns Number size and Label size along with Font family, Font weight and Font style. Drawing-only style exceptions continue to hide unsupported controls.
- Spacing owns Border radius, the Global/Individual mode spanning a full row, and individual positions visually paired Top/Bottom and Left/Right. Do not change internal data keys or the actual RTL semantic meaning of each side.
- `card_inner_spacing` (0–30px) for Snowfall changes the reserved *extra* numeric slot width. It deliberately does not multiply existing `clock_padding` values; changing the number still must not shift the entire countdown width.
- Snowfall defaults to Snowburst One but offers the 0.10.16 curated font list. External Google font families remain lazy; Almonte Snow is CC0 but only exposed as a locally installed option without binary distribution.
- The official branding, ALP dropdown UI, SafeEdit actions, WordPress menu white isotype and Divi 5 native controls remain unchanged.

## 0.10.17 targeted control additions
- Individual padding keeps the existing fields and attributes. Each opposite-side pair has a chain button with `aria-pressed`; on blue/linked, updates copy in either direction, and unlink stops synchronization. No persistent new link option is introduced.
- Number and label sizes share a single value/selector per field. Unit conversion is a convenience approximation using a 16px root and reference viewport; use clamp() for true CSS-fluid sizing. CSS lengths and clamp expressions are validated in PHP and JS, with previous px strings preserved.
- WordPress/Divi native controls remain untouched; this only applies to plugin-owned generator fields. Verify actual wp-admin, Divi 5 and frontend across browsers before deploying.

## 0.10.18 · Numeral card spacing and stability (supersedes the 0.10.12 outer-panel coupling)
- The Number container padding controls apply to the actual visible card. Do not implicitly change the outer appearance panel with those settings. Keep unchanged preset panel gutters unless the user separately configures them in a future dedicated setting. Opposite-side link buttons from 0.10.17 remain editor-only.
- For painted-cell styles (legacy Promo and Minimalist), apply numeric padding to `.alpdt-clock-cell`; for Ring/Liquid/Orbit, add exterior clearance rather than deform fixed circular dials. The usual styles (including Snowfall and Christmas Lights) continue to apply numeric padding to `.alpdt-clock-number`.
- The selected countdown values should not cause tick-by-tick width changes. Stabilize using actual font glyph metrics once per layout/font; do not stop the split-flap, odometer, seasonal or other animations. Re-measure on viewport/font changes.
- Almonte Snow has a single Regular 400 and may require a verified third-party webfont host (no font files are bundled). Keep a fallback for offline/CSP-restricted sites; use self-hosting for production whenever possible.
- Font family picker belongs to a full-width grid row, without impacting unrelated WordPress or Divi native dropdowns.

## 0.10.19 | User-requested spacing controls (2026-11-18)
- WordPress sidebar menu label is `ALP Dynamic Time & CD`; product name, slug and admin page title remain unchanged.
- Four visible numeric card padding fields replace the editor's Global/Individual selector. Vertical, horizontal and all-sides chain buttons link values interactively (blue `aria-pressed` when active). Button link state is editor-only. Existing global padding and individual shortcodes remain supported by the renderer.
- Snowfall numeric card padding affects the painted number card's four sides including top/bottom, while fixed digit slots continue to prevent live width shifts.
- Christmas Lights has an optional `bulb_card_gap` (0–70px) on the root decoration-to-card clearance. Blank preserves the old style; explicit zero makes bulbs/cards as close as the decorative wire permits. Existing number card padding does not implicitly alter it.

## 0.10.20 | Separate padding and full-width countdown
- Supersedes previous instructions for an All four link and the Global/Individual picker. Use two compact independent link toggles per padding scope (Top/Bottom and Left/Right), SVG chain artwork with an accessible pressed state. Keep separate **Countdown padding** and **Number card padding** four-side settings.
- `clock_padding_*` continues controlling the number cards. `countdown_padding_*` controls the whole countdown, without changing previous shortcodes. Both use 0–80px integer values. Existing `clock_padding` stays a compatible legacy input.
- `clock_width=full` opts into 100% of the parent content width; the Christmas strand extends fully, but digits/labels remain flex-centered. This does not break out of Divi sections or force global viewport width. Unset/default width remains automatic.
- Revalidate in WordPress and Divi 5 before release; isolated browser checks are not proof of live integration.

## 0.10.27 · Restored Saved dates editor (supersedes 0.10.26 editor location)
- Return the create/edit reusable date form to the left card of **Saved dates** and retain the dates list in the right card. Do not show a duplicate form or expandable date editor in **Generator**.
- Use **Save reusable date** as the form heading, translated as **Guardar fecha reutilizable**. Preserve all field IDs, option/shortcode identifiers and native date inputs.
- Retain 0.10.26's non-modal two-second copy confirmation, the existing three-zone ALP footer and the yellow review star. Responsive Saved dates layout collapses to one column on smaller screens.

## 1.0.0-rc.2 | Gutenberg simplified block
- Block `alpdt/time` must offer **only one saved-shortcode selector**, as in the Divi 5 Content panel. Editing dates, colors, countdown layouts or animations belongs solely in the ALP Dynamic Time & Countdowns dashboard. Do not duplicate generator controls in Gutenberg.
- The block's official product isotype stays in the inserter, and the selected saved code is displayed as an informational summary, not an editable shortcode string.
- The first public Gutenberg block accepts only the `saved` shortcode identifier; no compatibility or migration is required for experimental pre-1.0 block fields. Saved shortcode identifiers resolve dynamically; invalid/deleted keys render empty, never alternate content.

## 1.0.4: Color picker viewport safety
- ALP's dark color popup remains in the Generator and retains HEX, alpha, Quick Colors and keyboard/Escape behavior.
- When open, its `position:fixed` coordinates are clamped inside the visible WordPress content viewport, below the admin toolbar and to the right of the WP menu. Maximum height is limited to available space, with internal scrolling instead of a clipped header/footer.
- Keep popup CSS scoped to `.alpdt-admin`; never reposition or modify native WordPress/Divi controls. No changes to stored colors or shortcode formats.
