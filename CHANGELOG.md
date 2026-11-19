## 1.0.0-rc.3 (2026-11-19)

- Withdraw the experimental Custom font uploader, AJAX action, UI controls and font-face registration until a future version. Keep Media Library attachments/options from rc.2 installations untouched; any saved references to custom fonts fall back to the default font.
- Keep the removal of the two heavy Black And White Picture and Rubik Scribble families from rc.2.
- Protect the ALP dashboard hero from third-party notices injected near its heading. Move recognized admin notices above the hero, preserving their content, dismissal buttons and visibility; stop the previous CSS rule that hid critical outside notices.
- Keep all saved dates, shortcodes, Divi/Gutenberg identifiers, assets and the ALP footer unchanged.

## 1.0.0-rc.2 (2026-11-19)

- Remove Black And White Picture and Rubik Scribble font options, 95 bundled WOFF2 files and their `@font-face` declarations to reduce package and page payload. Old customizations using these removed choices fall back to the style default.
- Add an administrator-only Custom font uploader in Typography for WOFF2/WOFF/TTF/OTF (maximum 2 MB per file, 12 fonts), saved to Media Library with capability, nonce, binary signature and WordPress MIME validation. Select an uploaded font for any compatible countdown, including saved shortcodes.
- Register uploaded custom font faces for frontend, wp-admin preview, Divi and Gutenberg shortcode rendering without loading remote font services.

## 1.0.0-rc.1 (2026-11-19)
- Replace Gutenberg's configuration-heavy interface with a single **Saved shortcode** selector, following the simplified Divi 5 module workflow. Saved configurations continue to be created and edited exclusively in ALP Dynamic Time & Countdowns > Generator / Saved shortcodes.
- Populate the Gutenberg selector from the validated named-shortcode library; only expose names and identifiers to editors. Keep the official isotype in the block inserter.
- Server-render the selected shortcode through `ALPDT_Shortcodes::main()` to preserve animated countdown markup, asset loading, text and future updates to the saved configuration.
- Preserve the `alpdt/time` block identifier but remove experimental pre-1.0 Gutenberg field attributes and legacy rendering; the public 1.0 block stores only the saved shortcode slug. WordPress posts using earlier experimental blocks must select a saved shortcode anew. Deletion of a saved entry does not silently substitute a different one.
- Advance plugin header, runtime constant, directory `Stable tag` and documentation to **1.0.0-rc.1** (release candidate only; not a public final release).
- Real WordPress Gutenberg editing, saved/reloaded posts, Divi 5 and frontend visual verification remain pending until tested on staging.

## 0.10.28-rc.3 (2026-11-19)
- Remove the obsolete short-alias FAQ from the directory readme while leaving historical changelog and shortcode behavior untouched.
- Explain that ALP Dynamic Time & Countdowns was independently inspired by the closed Years Since - Timeless Texts plugin, preserving supported legacy shortcode forms only when unclaimed and extending functionality. The original plugin remains a separate project.
- Clarify that ALP Dynamic Time & Countdowns is free, with a Pro edition only a potential future possibility.
- Gutenberg block: only show Countdown target for Countdown (days), explain its meaning and format, and include `to` in the previewed shortcode. Keep the same `to` attribute and dynamic render callback.
- Standardize public-facing documentation references to ALP Dynamic Time & Countdowns. No stored options, existing shortcode aliases, Divi module schemas, API contracts, or branded SVGs changed.
- Source checks and packaging completed; full WordPress/Gutenberg editor and Divi 5 runtime tests are still pending.

## 0.10.28-rc.2 (2026-11-19)
- Divi 5: register the official ALP Dynamic Time isotype in the module library through the documented `divi.iconLibrary.icon.map` filter; add the `moduleIcon` identifier in PHP and Visual Builder metadata. No stored layout attributes change.
- Center the existing 2-second SafeEdit-style copy confirmation in the viewport; keep it accessible, non-blocking and independent of card layout. Clipboard failures retain their longer manual-copy guidance.
- Add **Light · 300** in the countdown font-weight selector. Validate and preserve the value consistently in editor, preview, public shortcode rendering and saved shortcodes.
- Advertise the true 300 weight range in the locally bundled variable Inter, Montserrat and Fredoka families. Other fonts with no light face may render at their nearest available weight.
- No saved data, shortcode keys, branded SVGs, REST routes or Divi module contracts were changed.

## 0.10.28-rc.1 (2026-11-19)
- Serve countdown fonts locally with upstream font licenses and source links.
- Register the Divi 5 module when Divi builds its dependency tree.
- Preserve countdown markup and completion actions when a shortcode also requests live text refresh.
- Correct identifier validation in recent browsers and preserve WordPress notices on the plugin screen.
- Update the directory readme and describe public date lookups and data retention.

## 0.10.27 (development candidate, 2026-11-19)
- Revert only the relocation of the reusable-date editor from Saved dates to Generator, restoring the previous two-column design in Saved dates.
- Rename the form heading from "Add a saved date" to "Save reusable date" (Spanish: "Guardar fecha reutilizable").
- Preserve clipboard success notification from 0.10.26, saved dates/IDs, shortcodes, server actions, existing branding and SEO integrations.
- No options/data migration; real WordPress/Divi 5 verification pending.

## 0.10.26 (development candidate, 2026-11-19)
- Replace inline copy confirmations with one unobtrusive, non-modal SafeEdit-style confirmation. Shortcode and dynamic/SEO variable copy successes appear for exactly 2 seconds without changing the card geometry or moving keyboard focus. A failed clipboard write selects the available source text and shows a longer explanatory message.
- Saved dates tab lists reusable dates and a link into Generator, without the duplicate new-date form heading. The original reusable-date form has moved into a collapsible section within Generator, preserving all form IDs, POST action, nonces, saved date identifiers, and edit/delete functionality. Editing from Saved dates opens Generator with the appropriate pre-filled values and a locked identifier; Cancel restores the list and focus.
- No database migrations, public shortcode changes, API changes, SVG changes, or Divi behavior changes. More detail: `docs/QA-0.10.26.md`.

## 0.10.25 (development candidate, 2026-11-18)
- Display anniversary variable copy confirmations underneath their corresponding buttons; automatically clear success feedback after 2.8 seconds and clear older messages when copying another variable. Preserve aria-live and failure fallback.
- Add a decorative yellow five-point SVG star to the center review invitation in the established three-zone ALP footer; original approved logo assets remain unchanged, and the invitation is still non-interactive pending a verified review URL.
- Scoped admin changes only. No existing shortcodes, REST endpoints, options or persisted data were modified. See `docs/QA-0.10.25.md`.

## 0.10.24 (development candidate, 2026-11-18)
- Anniversary message editor: focusing Before, On or After shows that exact message in the live preview with an explanatory caption, independent of the actual anniversary date. The normal published shortcode continues to select the proper message by date. The preview-only REST argument `preview_message` is strictly allowlisted, never saved or exposed as a shortcode attribute.
- Dashboard footer: maintain its placement near the bottom of short admin pages, with a centered text-only invitation "Enjoying the plugin? Help us with a review!" between the two unchanged official logo assets. No link is included until an actual review URL is supplied.
- SEO guide: registered native `alpdt_month` for Rank Math (`%alpdt_month%`) and Yoast (`%%alpdt_month%%`), alongside the existing current-year variables, with visible copy buttons. The month uses WordPress's timezone and site language. SEOPress continues to use existing shortcode-based replacement where supported.
- Fixed malformed Saved dates tabpanel id/aria-labelledby attribute found in the base version. No data keys, saved options, or public shortcode syntax were changed.
- PHP/JS/CSS syntax, isolated PHP logic and simulated Chromium rendering passed. Live WordPress/Divi 5 and SEO provider integration remain unverified. Details: `docs/QA-0.10.24.md`.

## 0.10.23 (development candidate, 2026-11-18)
- Saved shortcodes: remove inner scrolling, show at most ten records per page, and add accessible server-rendered numbered pagination.
- Anniversary messaging: distinguish plain `{years}` from ordinal `{number}` (same representation as Anniversary number); keep the old `{year}` alias for compatibility.
- Generator: clarify what each display type does and when it needs a date.
- Smart date: add preview-only example date for post publication/modified expressions and avoid treating missing post context as an invalid date entered by the editor.
- No saved shortcode data keys were migrated. WordPress/Divi live tests remain outstanding.

## 0.10.22 (development candidate, 2026-11-18)
- Fix anniversary message placeholders by accepting `{year}` as an alias for `{years}` (anniversary count), and add `{current_year}` for four-digit calendar years. Copy buttons and clear descriptions added to the editor.
- Move SEO variable controls out of the generator; retain SEO integrations and display actual Rank Math/Yoast markers with copy buttons for saved text shortcodes.
- Celebration burst durations: 5, 8, 10 seconds in the editor; preserve saved 3-second shortcodes for compatibility. JavaScript now supports the 10-second animation duration.
- Add scoped blue clock SVG beside Clock appearance.
- Use each Christmas bulb's own selected color in its glass fill and halo, with a lighter same-hue near glow.
- No schema/option/shortcode migration. No unapproved live deployment.

## 0.10.21 (development candidate, 2026-11-18)
- Snowfall: remove unintended extra inner space when number-card Left/Right padding is zero; allow independent horizontal padding without changing the stable digit widths.
- When Countdown padding is explicitly zero on all sides, remove the seasonal panel's inherited minimum height and padding. Leaving it unset preserves the original defaults.
- Retire the duplicate Snowfall number-card spacing field from the generator; retain the legacy `card_inner_spacing` shortcode attribute and saved configurations for compatibility.
- Expand Countdown border radius and Blood Dripping font controls to the full section width.
- Christmas Lights: add 8–60 configurable bulbs (15 default) and optional bulb colors 6–12; wider strands can maintain tighter bulb spacing.
- Soft Glow: add optional custom background color, preserving the original gradient on blank and validating HEX input.
- Hourglass: vary sand-flow animation speed for seconds/minutes/hours/days and update level by elapsed fraction of the represented unit.
- Celebration Confetti & Streamers: slower balloon movement and staggered launches, plus optional confetti/balloon palette colors 5–10 with more balloons as palette entries are added.
- Preserve existing plugin name, ALP admin design, public shortcode and Divi identifiers, old options and saved shortcode data. No data migration or production deployment.
- Live WordPress/Divi rendering and saved-setting parity require separate validation.

## 0.10.20 (development candidate, 2026-11-18)
- Restore separate countdown container padding and number card padding, each with four explicit sides, two compact link buttons and no oversized all-four control.
- Ensure left and right number card padding can expand visible Snowfall/Christmas Lights cards instead of being constrained by an inherited card max-width.
- Add opt-in 100% countdown width for all clock layouts, with centered timer cells and full-width Christmas Lights strand.
- Preserve 0.10.19 shortcodes and legacy global padding, no schema/data migration.
- Release pending real WordPress/Divi functional and browser verification.

## 0.10.19 (development candidate, 2026-11-18)
- Shorten only the wp-admin sidebar menu label to ALP Dynamic Time & CD.
- Show four independent number-card padding fields with vertical, horizontal and all-sides links, replacing the mode picker while reading legacy global padding values.
- Add validated Christmas Lights bulb-to-card gap (0–70px), preserving legacy default if unset.
- Reinforce Snowfall card padding on the visible digit container, including vertical sides.
- WordPress/Divi 5 integration tests remain pending.

## 0.10.18 (development candidate, 2026-11-18)
- Make the plugin-owned Font family dropdown span the full available Typography grid width in desktop and mobile layouts, preserving its existing ALP accessible picker.
- Correct number-card padding assignment: decorative styles keep their own OUTER panel gutter while the editable padding now targets the visible number card. Minimalist and legacy Promo use their painted cells; Ring/Liquid/Orbit receive outer clearance without distorting circular artwork.
- Stabilize per-unit countdown cell widths across numeral changes, using actual computed font metrics (including proportional display fonts) without overriding flip/odometer frame animation. The plain-text days countdown also reserves its initial numeric width. Recalculate when viewport or loaded fonts change.
- Almonte Snow selection can request a remote WOFF2/WOFF webfont instead of requiring each visitor to install the font locally; keep local-face first and fallback font. Prevent synthetic bold for this single-weight typeface unless a weight override is explicitly chosen. No font software is included in this ZIP. Remote accessibility and actual font rendering on customer sites remain to be validated.
- Retain all existing shortcode identifiers and stored configurations; no database/options migration. Live WordPress, Divi 5, PHPStan, PHPCS/WPCS and Plugin Check remain to be executed.

## 0.10.17 (development candidate, 2026-11-18)
- Add two independent, accessible chain toggles between the Top/Bottom and Left/Right individual padding inputs. Blue pressed state means changing either side updates its paired field, without changing existing shortcode attribute names. Linked state is an editing convenience and is not saved as a new setting.
- Number and label font-size controls now support px, %, vw, vh, rem, em, and a restricted `clamp(min, preferred, max)` expression via the unit selector. Old numeric px values and saved shortcodes are preserved.
- Apply allowlisted CSS lengths and clamp expressions to preview and frontend, including proportional ring sizing for relative digit units; reject malformed values when saving named shortcode configurations.
- Preserve existing WordPress/Divi options, theme styles, author/Settings metadata, and PHP 8.2 minimum. Real WordPress/Divi/browser-runtime verification still required before production deployment.

## 0.10.16 (development candidate, 2026-11-18)
- ALP UI: move Typography immediately after Colors; put Number size and Label size with Font family, Font weight and Font style; keep circle dimensions under Size & shape; move Border radius under Spacing.
- Spacing mode selector now spans a full row; padding controls appear as Top/Bottom and Left/Right pairs.
- Add a Snowfall-only number card inner-width reduction control. This changes unused fixed slot width without changing number size or the stable-width behavior as digits tick.
- Add optional font weight and italic typography overrides for applicable countdown styles; decorative dot/morph/blood drawings remain protected.
- Add Google Fonts Black And White Picture and Rubik Scribble as conditional font choices for Snowfall; keep Snowburst One. Almonte Snow is an optional locally installed font (user-supplied CC0 source metadata verified); no font binary bundled.
- Retain saved shortcode contracts, PHP minimum 8.2, Divi module and brand resources. WordPress/Divi runtime verification remains pending.

## 0.10.15 (development candidate, 2026-11-17)
- More compact Snowfall / Christmas Lights digit cards; lower Christmas Lights header and full horizontal panel height, while preserving the five bulb colors and animation.
- Reorganize Clock colors, size and typography into Colors, Size & shape, Spacing, Christmas bulb colors, and Typography groups. Conditionally hide irrelevant sections.
- Add a curated whitelist of modern, display, Christmas and Halloween Google Fonts, loaded **only for the selected font**. The default Snowfall font remains Snowburst One; the special Blood Dripping font selector is unchanged.
- Existing shortcode attributes, WordPress menu, brand assets and PHP 8.2 minimum preserved. Real WordPress/Divi visual and font-network tests remain pending.

## 0.10.14 (development candidate, 2026-11-17)
- In Christmas Lights with Minimalist / Horizontal and reduced explicit padding, move decorative bulbs from the numeral midline to a small band along the inside top edge. Keep the decoration clipped inside the panel, behind the numerals if necessary, without changing Default layout.
- Reduce the compact bulb and socket size and limit the glow so the five customizable colors remain visible without overwhelming the countdown.
- Tighten the default numeric card padding in Horizontal Christmas Lights only. Explicit user-defined number padding continues to take precedence.
- No changes to shortcode identifiers, saved data, countdown calculations, branding, Divi 5 integration or other clock appearances. Real WordPress/Divi integration testing remains pending.

## 0.10.13 (development, 2026-11-16)
- Christmas Lights: when the Horizontal layout has explicit top padding below 48px, confine the decorative strand inside the panel and place bulbs behind the numbers; preserve the original strand placement at normal spacing.
- Add optional `clock_radius` (0–80px) to generator, shortcode rendering, saved configurations and live preview. Blank retains the original style-specific radius; circular artwork is excluded to retain shape.
- Snowfall: reserve fixed-width digit slots to avoid panel-width jumps as proportional Snowburst One numerals change; maintain variable digit sizes and the existing CSS-only snow animation.
- PHP syntax, JS syntax, CSS and isolated browser rendering to be verified before release; WordPress/Divi live testing still pending.

## 0.10.12 (development candidate, 2026-11-16)
- Horizontal layouts now respect explicit `clock_padding="0"` on both the numeric containers and their outer appearance panel. Empty padding settings preserve the existing design.
- Individual top/right/bottom/left padding also overrides appearance-specific outer padding in Horizontal mode, including responsive rules, without changing Default layouts.
- Snowfall and Christmas Lights show only effective appearance controls. Rename their background field contextually and retain Christmas Lights' five independent repeating bulb-color settings.
- Retain all saved shortcode attributes, effects, source files, Divi 5 module identifiers and ALP brand assets.
- Real WordPress/Divi visual and Plugin Check verification remain pending.

## 0.10.11 (development candidate, 2026-11-16)
- Remove the repeating radial-gradient dots that formed obvious horizontal/vertical rows in Snowfall.
- Fix the admin preview, which still generated 64 flakes using periodic formulas; frontend and preview now generate 96 independently positioned/phase-shifted flakes using matching settings.
- Slow and diversify snow trajectories (19–37 seconds, gentle horizontal swaying, varied scale/blur/opacity, staggered negative phases), with CSS-only motion and no particle timer.
- Preserve Christmas Lights, Horizontal/Default layouts, saved shortcodes, brand assets and technical identifiers.
- Browser/WordPress/Divi 5 runtime verification pending unless separately documented in QA.

## 0.10.10 (development candidate, 2026-11-16)
- Refine **Snowfall** so flakes are staggered more naturally, use slower varied fall durations, differ in drift/sway, and avoid visible straight rows on first render.
- Simplify **Christmas Lights** to one main strand configuration while adding five repeating bulb color controls. Slow the festive light motion and diversify it with several gentle real-light blink patterns.
- Make the **Horizontal** countdown layout more compact overall, especially for low-profile / minimalist uses, while preserving each appearance.
- Fix Plugin Check warnings by setting explicit versions on the Google Fonts Snowburst stylesheet registrations.
- Preserve backward compatibility with existing `lights_mode` values and previously saved shortcodes. Full WordPress/Divi runtime verification remains pending.

## 0.10.9 (development candidate, 2026-11-16)
- Consistently use the full product name **ALP Dynamic Time & Countdowns** in the WordPress sidebar, Gutenberg block and binding label, SEO variable labels, admin guidance and Divi 5 saved-shortcode instructions.
- Preserve all technical identifiers, data, animations and configuration options from 0.10.8. No database migration.
- Full runtime WordPress, Gutenberg and Divi 5 checks remain pending; this release is not published.

## 0.10.8 (development candidate, 2026-11-16)
- Added Snowfall, a winter countdown with 64 individually positioned snowflake glyphs. Snowflake movement is CSS-only (no JavaScript animation or canvas), with reduced-motion support. Snowburst One Regular is loaded via Google Fonts when used.
- Added Christmas Lights, a countdown with an accessible static decorative strand and 14 animated colored bulbs. Users can choose Multicolor or Warm white in the generator; saved shortcodes retain the selection.
- Both styles work with countdown duration/repetition, existing colors/padding, frontend shortcodes, admin live preview and horizontal layout; the WordPress/Divi saved-shortcode interface remains unchanged.
- No CodePen source was copied. Referenced source implementations were not fully accessible; work is original CSS/markup.
- PHP/JS lint and isolated preview tests were performed. Full WordPress/Divi 5/Plugin Check tests remain pending.

## 0.10.7 (development, 2026-11-13)
- Switched padding from the countdown grid to the numeric containers across all styles; preserved circle diameter, arcs and internal animation geometry via exterior spacing for ring/liquid/orbit.
- Added a global vs individual (top/right/bottom/left) padding editor. Existing saved `clock_padding` values remain supported; individual sides are validated as integers 0–80 px.
- Centered both dynamic text values and countdowns in the admin live preview.
- Updated the official user-provided product isotype and logo assets in color/white, horizontal/vertical variants; the wp-admin menu retains the white 20px isotype. Header and footer use the new product lockups.
- Added the official ALP Plugins corporate SVG to the dashboard footer while preserving the separate product logo and link destination.
- Full real WordPress/Divi runtime tests and external Plugin Check remain pending.

## 0.10.5 (2026-11-12, development)
- Show only appearance controls which change the selected countdown: circle diameter for Ring, Liquid and Orbit; track, card and accent colors only for styles that use them.
- Widen the generator preview on desktops and increase the available vertical preview space while keeping the mobile single-column layout.
- Add optional `clock_padding` (0–80 px) to countdowns, validated server-side and stored with reusable shortcodes; blank preserves the original spacing for each style.
- Restore Creepster Regular 400 for Blood Dripping at all sizes and offer Nosifer Regular 400 only in that style. At small sizes or with Nosifer, disable just the SVG goo composite while keeping droplets and the red puddle.
- Creepster and Nosifer load as Google Fonts WOFF2 on demand; original font binaries are not bundled. Font licensing details: `docs/FONTS.md`.
- Preserve all other styles, configurations, the Divi integration and the menu isotype; live WordPress/Divi tests remain pending.

## 0.10.4 — Horizontal flip alignment, Ring/Confetti layout, compact Blood legibility (development, 2026-11-12)
- Reflow horizontal Flip separators as normal flex items instead of absolute positioning, preventing overlap with unit initials while preserving each flip card's 3D transition. Smaller viewports omit separators to prevent awkward wraps.
- Enable Minimalist / Horizontal within Circular Progress Dial and Celebration Confetti & Streamers without changing their SVG ring and confetti animations. Horizontal rings use a more compact diameter; Default remains unchanged.
- Improve small-size Blood Dripping legibility at computed digit sizes up to 44px by rendering crisp regular numerals without the SVG goo filter; preserve droplets, red puddles and the larger Creepster appearance.
- Preserve serialized identifiers, custom unit labels, saved shortcodes, source animations, Divi module integration, admin icon, and locale files. Live WordPress/Divi integration remains pending.

## 0.10.3 — Horizontal layout for existing countdown appearances (development, 2026-11-12)
- Moved **Minimalist / Horizontal** out of the appearance picker into a separate **Countdown layout** select under the chosen Clock appearance. Default remains unchanged.
- The horizontal presentation puts the translated first letter of each time unit immediately to the right of its number, without replacing the selected clock style or its existing animation.
- Supported across Minimal inline text, Soft glow, Flip digits, Sliding numbers, Gentle pulse, Animated underline, 3D Odometer, Neon Digital, Liquid, Pixel Matrix, Cyber Glitch, Particle Morph and Fireworks celebration.
- The new `clock_layout="horizontal"` shortcode attribute persists in named configurations and works with full/custom fixed and recurring countdowns and the dashboard preview. Existing 0.10.1/0.10.2 standalone Minimalist/promo shortcodes continue to render, but the generator imports them to Simple + Horizontal for new edits.
- No new animation or clock style added; Divi 5 continues to render existing saved shortcode configurations. Testing in real WordPress/Divi remains pending.

## 0.10.2 — Single Minimalist countdown appearance (development, 2026-11-12)
- Replaced the three visible Minimal Promo Bar options with one **Minimalist** entry inside the existing Clock appearance dropdown. No separate top-bar builder or settings panel was introduced.
- Numbers and short unit badges sit side by side within compact cards; defaults are Y, MO, D, H, M, S and can be overridden using existing unit-label fields.
- Reused existing digit/card/label/accent color, font and size controls. Full/custom, fixed/recurring countdowns continue to use the same frontend and preview renderers.
- Preserved compatibility with shortcodes saved with the three legacy 0.10.1 promo style identifiers. Loading them into the generator maps their style selector to Minimalist without rewriting stored data.
- Remaining release gates: WordPress/Divi functional and visual validation, Plugin Check, PHPCS and PHPStan.

## 0.10.1 — Dynamic Dates + Recurring Countdowns (development)
- Renamed the displayed product to **ALP Dynamic Time & Countdowns**. Kept the WordPress plugin folder/slug, text domain, shortcode identifiers, stored option names, CLI command, block registration and Divi module slug intact.
- Introduced a strict, timezone-aware smart date expression engine, with current/relative/next weekday/month-end/quarter/meteorological-season/post-date/signed-date-difference expressions.
- Added daily, weekly and monthly local-time recurrence, clamping short months and skipping nonexistent DST clock times; a public, read-only, no-store endpoint recalculates the next event after expiry.
- Added opt-in fixed-countdown completion actions: hide, accessible message or restricted local-path redirect (only after a visible running timer). Recurring schedules do not redirect.
- Added date-expression, recurrence and end-action controls to the existing ALP shortcode generator, plus saved-configuration support, REST preview and optional live refresh after cached page load.
- Preserved the 18 countdown styles, their animation assets, the menu isotype, saved data, existing shortcodes and security boundaries. Full WordPress/Divi/Plugin Check release gates remain pending.

## 0.9.48 (legacy menu redirect security scan, 2026-11-06)
- Normalize GET request method using `wp_unslash()` and `sanitize_text_field()` before legacy menu redirect checks. Restrict the redirect to administrators and GET requests, preserving existing behavior.
- Explain and narrowly suppress `WordPress.Security.NonceVerification.Recommended` for GET-only legacy bookmark navigation. Redirects do not mutate state, while the destination export handler enforces `check_admin_referer( 'alpdt_export' )` and `manage_options` before returning JSON. POST save/delete and Quick Colors keep their existing nonce/capability checks.
- No changes to the Divi 5 integration, stored options, shortcodes, animations, admin icons or branding. Full Plugin Check and live WordPress/Divi functional verification remain pending.

## 0.9.47 (security review and maintenance)

- Register the authenticated Divi preview route only when the Divi 5 module is available.
- Preserve WordPress REST query parameters for the generator, Divi preview and live shortcodes.
- Remove the unused Divi style map and skip visual-only parameters in the REST text renderer.
- Validate only the requested record when resolving a saved shortcode and reject IDs PHP would store as integer keys.

## 0.9.46 (admin menu icon correction, 2026-11-06)
- Restrict the official white ALP Dynamic Time isotype in the WordPress admin sidebar to the standard 20 × 20px menu-image footprint, horizontally centered. Do not replace it with the logo/text lockup.
- Override WordPress menu-image dimming ONLY for the ALP Dynamic Time entry: opacity 1, no filter. Keep the original white SVG unchanged and load the small isolated stylesheet on all admin screens.
- Record the reusable ALP admin-menu-icon rule in `docs/DESIGN.md`. Preserve all other menus and the existing dashboard/shortcode behavior.
- Real WordPress admin visual verification is pending.

## 0.9.45 (development candidate, 2026-11-06)
- Replace the generic `dashicons-clock` top-level admin menu icon with the official user-provided **white ALP Dynamic Time isotype** from local `assets/branding/isotype-white.svg`. Preserve the SVG artwork, existing dashboard logo, Gutenberg color isotype, permissions, menu slug, settings link and saved data.
- Main plugin, runtime and readme versions updated to 0.9.45. WordPress/Divi runtime verification remains pending.

## 0.9.44 (development candidate, 2026-11-06)
- Promote ALP Dynamic Time from Settings to a first-level WordPress admin menu item with the clock icon. Keep the same page slug, `manage_options` capability, dashboard UI, shortcodes, stored data and PHP 8.2 minimum.
- Route Settings action link, export, forms, save/delete redirects and scanner pagination to `admin.php?page=alpdt-time`. Update assets/notices to the new `toplevel_page_alpdt-time` screen hook.
- Redirect existing GET bookmarks from `options-general.php?page=alpdt-time` to the new dashboard without re-registering an entry inside Settings. GET exports preserve their nonce. Leave legacy POST actions supported during transition.
- Static/smoke checks performed on package only; confirm top-level menu rendering and workflow in WordPress/Divi before release.

## 0.9.43 (metadata correction, 2026-11-05)
- Raise the plugin main-header and `readme.txt` minimum PHP version from 8.1 to **8.2**, per the owner's PHP2Shell security-baseline decision. A patched PHP release remains necessary.
- Add `Tested up to: 7.1` to `readme.txt` using the user-reported WordPress 7.1.4 Plugin Check environment; full Divi 5 and functional compatibility checks remain pending.
- Bump plugin header, runtime version constant and readme Stable tag to 0.9.43. No functional code changed.

## 0.9.42 (development candidate, 2026-11-06)
- Blood Dripping: explicitly enforce regular weight 400 on the figures and labels and disable synthetic bolding, while preserving the existing Creepster font and user-defined colors. Use one continuous descent per droplet, with distance calibrated to the existing 1.59em puddle location and each droplet's size; drops remain fully opaque until touching the puddle, then briefly fade as if absorbed. Independent timings and 1:1 droplet proportions remain.
- Replace the `load_plugin_textdomain()` call with WordPress core's `WP_Textdomain_Registry::set_custom_path()` to retain bundled .mo translations in private ZIP installations without the discouraged function warning; add the `Domain Path: /languages` main plugin header.
- Plugin Check `missing_readme_header_tested` remains a release blocker: `Tested up to` cannot be specified truthfully without executing on a known WordPress core version. No version is invented. Run a real WordPress smoke test and then insert the verified version into readme.txt.
- Executed: PHP syntax (13), JS syntax (5), JSON and CSS parsing, isolated translation-registry callback, and Chromium contact/weight geometry on 900px, 375px, and 320px viewports. WordPress/Divi and Plugin Check remain untested.

## 0.9.41 (development candidate, 2026-11-06)
- Blood Dripping: use a constant 1.82em puddle width with a centered transform, independent of the changing numeral widths; preserve responsive font-size scaling, goo motion and existing drops.
- Saved Shortcodes: list/count only configurations that successfully parse as `[alpdt_time]`; keep legacy raw option records untouched until explicitly overwritten. New saves are not blocked by invalid historical records with the same identifier, and deleting valid records preserves other raw data.
- Plugin Check source review: add accurate translators comments beside placeholder-based translations; sanitize palette JSON before size checks; document read-only GET pagination/status and bounded prepared SQL via narrow inline PHPCS exceptions. Retain bundled ZIP localization behavior.
- Move the product design contract to `docs/DESIGN.md` in the installable package instead of the plugin root to avoid the unrelated Plugin Check root-markdown warning; preserve original content for project maintenance.
- `Tested up to` remains pending an actual tested WordPress core release; do not invent a compatibility claim. WordPress/Divi builder and plugin-specific functional tests remain pending.

## 0.9.40 (development candidate, 2026-11-05)
- Confetti: preserve the approved 0.9.38 Canvas paper physics, editable palette and six balloons. The right-hand cannon fires first and the left follows 300 milliseconds later on every burst. The two sides are intentionally staggered rather than simultaneous, including when celebration duration is 3, 5 or 8 seconds.
- **Breaking change authorized for testing sites:** remove the deprecated `[alp_time]` shortcode registration and acceptance from saved-shortcode parsing, admin editor validation and SEO replacement. The canonical shortcode is `[alpdt_time]`. Existing data is never automatically deleted or rewritten; saved configurations containing the short alias need to be edited/re-saved. The separate `[years-since]` compatibility aliases remain unchanged.
- Update README/readme.txt examples, migration guidance and version metadata. Real WordPress/Divi 5 and WordPress.org Plugin Check validation remain pending.

## 0.9.39 (development candidate, 2026-11-05)
- Celebration Confetti & Streamers: keep the approved native canvas fountain on the right and add a mirrored, independently varied confetti fountain on the left, both synchronized. The same saved confetti palette, stable short cycles, six unchanged balloons, reduced-motion support and cleanup remain intact.
- New shortcode output uses `[alpdt_time]` (unique five-letter ALPDT prefix). Existing `[alp_time]` shortcodes are deliberately preserved as a backwards-compatible alias, including saved configurations and SEO replacements. This historical alias can remain flagged by static prefix scanners; removing it would break existing pages.
- Security review: moved Quick Colors AJAX registration adjacent to the nonce- and capability-checked handler; bounded and sanitized palette JSON before its strict allowlist; sanitized saved shortcode input before revalidation; unslashed scan pagination; used `%i` to prepare the WordPress posts table identifier as well as existing query parameters.
- Kept `load_plugin_textdomain()` for the bundled ES/CR translations: a context-aware informational warning, not a demonstrated security issue. A WordPress.org `Tested up to` claim is intentionally pending an actual WordPress version test, not fabricated from `Requires at least`.
- No database migration, publishing, live site changes or removal of existing shortcode callbacks. Real WordPress/Divi functional regression tests and Plugin Check remain pending.

## 0.9.38 (development candidate, 2026-11-05)
- Renamed the user-facing countdown appearance from `Blood Dripping (Halloween)` to `Blood Dripping`; preserved the `blood` shortcode style, color settings, and historical changelog references. Updated es_CR/es_ES PO and MO translations for the exact new label.
- Rebuilt Celebration Confetti as a small, lower-right pixel-paper burst using a native, isolated Canvas 2D effect, then physical upward spray, gravity and flutter. The supplied PNG has just one frame, so exact movement cannot be recovered; this original interpretation matches its tiny square/orange-teal fragment shapes and starting position as closely as feasible.
- Confetti stays customizable through saved `confetti_1_color`…`confetti_4_color` attributes; six existing rounded balloons and their original CSS trajectories are unchanged. Repeat a short particle launch at fixed speed during the selected 3/5/8-second window.
- The effect is bounded to the selected timer, honors reduced motion and offscreen visibility, and does not load a remote animation library or change any public shortcode slug.
- Local Chromium playback and responsive checks passed for the new renderer, including 3/8-second duration, replay, offscreen pause, reduced motion and unchanged six balloons. Real Divi 5/WordPress runtime and visual comparison with a moving source remain pending.

## 0.9.37 (development candidate, 2026-11-05)
- Reworked Celebration Confetti & Streamers again: replaced the symmetric center boom with two staggered lateral paper-cannon fans, asymmetric trajectories and gravity settle. Preserved the existing shapes, palette attributes, shortcodes, balloons and accessible no-motion behavior. The precise supplied CodePen could not be fetched; resemblance remains pending user visual approval.
- Halloween: widened and lowered the liquid pool beneath each number, reduced drops from nine to four–six per unit, and added stable unit-specific variation to the position, size, rounded teardrop silhouette, rotation, timings and falling distance. Each droplet's intrinsic box maintains aspect ratio 1:1.
- Preserved public CSS classes, saved-shortcode data, Divi 5 module schema, colors and compatibility. Live WordPress/Divi animation verification pending.

## 0.9.36 (development candidate, 2026-11-05)
- Celebration confetti uses one synchronous center-origin “Boom” explosion, followed by gravity-driven falling pieces and ribbons. Preserves the three confetti shapes (paper, triangles/circles, streamers), original balloon launch trajectories and independent shortcode color palettes. Fixed 2.4s particle cycles repeat through the configured 3/5/8-second window; no new continuous JS loop.
- Halloween Blood keeps each droplet square (width=height, aspect-ratio 1/1) and uniformly scales/rotates a rounded-corner teardrop shape following the cited Nico Prat concept. Red puddles move lower beneath each numeric block, with increased number padding to maintain space before labels.
- CodePen reference marieandboone/YzMdVWe was inaccessible to the available retrieval tools. Motion is a clean-room approximation of the described Boom interaction, not a claim of exact visual equivalence or source reuse.
- Existing shortcode slugs (`confetti`, `blood`), color options, timing modes, labels, saved data and WordPress/Divi adapter remain unchanged.
- Executed validation: PHP syntax for all 13 PHP files; JavaScript syntax for five JS files; CSS stylesheet parsed with tinycss2; dynamic Node VM assertions of 104 paper/ribbon pieces and six balloons, saved colors, 3/8 second cleanup and nine 1:1 drops; PHP shortcode markup/palette regression assertions. Chromium screenshot attempt timed out; real WordPress/Divi runtime and exact visual comparison to the first CodePen remain pending.

## 0.9.35 (development candidate, 2026-11-05)
- Celebration confetti now uses a timed multi-burst launch pattern closer to the fireworks motion, while preserving the separate balloon release and independent user color palettes. The saved shortcode slug remains `confetti`.
- Renamed the generator appearance from Birthday confetti & streamers to Celebration confetti & streamers, and shortened Clock appearance labels by removing redundant uses of the word Countdown.
- Refined Halloween blood with rounder, shorter drops and a red puddle beneath the digits. Motion remains a single continuous fall that fades out before the label area.
- Divi 5 module: renamed Shortcode Colors & Label to Shortcode Colors, hid the duplicate top Label color field, renamed Label Text Size to Label Typography, removed the old local Text Alignment control from the UI, and filtered out Mouse Parallax together with unsupported Line Hover Animation groups.
- Divi 5 module: the empty Shortcode Typography group is now hidden in the Visual Builder, while legacy stored attributes continue rendering for backward compatibility.
- PHP/JS syntax and package checks remain required after these edits; live WordPress and Divi builder verification is still pending.

## 0.9.34 (development candidate, 2026-11-05)
- Added user-provided official SVG brand artwork locally: white horizontal logo for the admin hero, color logo for the admin footer, and color isotype for the Gutenberg block picker. Kept all shapes and colors intact; stripped only XML/DOCTYPE declarations from image assets.
- Preserved existing hero animated clock, version badge, shortcode rendering and Divi module metadata. No global Divi or WordPress UI overrides; image loading is scoped to the plugin admin and block editor.
- WordPress.org directory icons require separate SVN assets and are not changed by this ZIP. Official ALP Plugins corporate footer logo was not supplied; footer therefore links to ALP Plugins with text and explicitly displays the product logo, without pretending the product mark is the corporate mark.
- Production WordPress/Divi browser, accessibility and responsive tests remain pending.

## 0.9.33 (2026-11-05)
- Halloween Blood: Creepster display-face override, shorter continuous downward drops that fade away before restarting; retains reduced-motion fallback.
- Celebration Confetti: independently configurable 4-color palettes for balloons and confetti via the existing ALP color picker; existing saved shortcodes retain their classic defaults. Includes saved-shortcode validation support for the eight new color attributes, including HEX alpha.
- Divi 5: replace duplicate label-size range control with native Label Text font sizing that supports Divi's unit picker (px, vw, rem, clamp, etc.), retaining the legacy `localLabelSize` attribute for compatibility. The embedded builder metadata and PHP style-component output are synchronized with `module.json`.
- Increase admin saved-shortcode Label size numeric limit to 200px; no arbitrary CSS expression is accepted as shortcode parameters.
- Creepster is currently served via Google Fonts only for the Halloween effect because the static bundled font could not be retrieved in this environment; self-hosting remains a release prerequisite under DESIGN.md.

## 0.9.32 (development candidate)
- Fixed the ALP admin HEX color field opening the existing color popover; enabled Divi 5 local native color inputs to accept global color values (actual Divi Global Colors UI requires a live editor check).
- Conditionally hide native Divi typography for saved Particle Morph, Pixel Matrix and Halloween Blood clocks. Trim unnecessary Divi font fields while retaining existing stored attributes.
- Read module alignment from native Divi Sizing, center the clock container itself, and expose a validated Label text size override (10–32px) for individual modules.
- Birthday confetti and balloons repeat short animation cycles within a 3/5/8-second run instead of stretching particle duration. Balloon movement is linear in time with a curved lateral sway and no mid-flight pause.
- Halloween blood effect now creates nine staggered drip streams per numeral under an original SVG goo filter, using the technique of the user-provided CodePen as visual reference. No external animation library or font download.
- A module-scoped Divi 5 groups filter now suppresses a group explicitly named `Line Hover Animation` when an extension injects it; other modules and the original saved attributes remain untouched. Exact third-party integration requires testing in the installed Visual Builder.
- Local PHP/JavaScript syntax, isolated frontend animation and Divi metadata/adapter tests executed; full WordPress and real Divi Visual Builder checks remain pending.

## 0.9.31 (candidate)
- Divi 5 Global Colors: resolve stored global color references on server (and in Visual Builder) using official Divi APIs, with strict CSS validation.
- Replace legacy Divi size, font-family and alignment controls with a native Divi typography group; retain the old attributes hidden for compatibility.
- Synchronize Liquid wave and fill movement and unify their accent color to prevent a moving seam.
- Replace Celebration Confetti with three organic paper bursts, ribbons and balloon releases; respect reduced motion.
- QA: isolated PHP/JS/browser and frontend tests, pending a real Divi 5 editor verification.

## 0.9.30 — Celebration loops, birthday confetti and Halloween dripping (staging candidate)

- Optional loop while a countdown is active for fireworks and birthday confetti, completion-only remains default.
- New confetti and long streamer effect, with low-motion fallback.
- New blood-dripping text style with reusable color tokens and accessible numeral text.
- Hide font family when digits are rendered as a dot matrix (Matrix/Morph).
- Preserve saved shortcode ids and the streamlined Divi module.

## 0.9.28 — Divi 5 saved shortcode choices (staging candidate)
- Fix the empty Saved Shortcode selector by supplying choices through Divi 5's server-generated settings, registering them with Divi's module-attributes hook and syncing the PHP block metadata.
- Remove an unreliable inline-script dependency on a package handle registered by Divi later in the loading lifecycle. Keep shortcodes, module ID, saved configurations and local Design controls unchanged.
- Show a useful empty-state option if no valid shortcodes have been saved.
- Static and simulated WordPress/Divi tests performed; real Visual Builder confirmation still pending.

## 0.9.27 — Fireworks scatter refinement (staging candidate)

- Replaced the ring-like/spiral fireworks with irregular, randomly scattered colored bursts and layered gravity, following the motion principle of the user-provided Pure CSS Fireworks CodePen without copying its source code.
- Preserved the outer glow and central flash, editable panel background, custom final message and 3/5/8-second animation duration.
- Fireworks still runs only once when an active countdown first reaches zero, or when intentionally previewed from the Generator. A previously expired countdown does not replay it automatically.
- Respect reduced-motion preferences and existing saved shortcode attributes. All changes stay scoped to Fireworks.
- PHP/JS lint and isolated Chromium tests required; WordPress/Divi runtime checks pending.

## 0.9.26

- Make the Generator countdown preview sticky beside long settings on desktop; retain normal flow on mobile.
- Vertically center Pixel Matrix and Particle Morph dot glyphs within their cards.
- Remove the out-of-bounds lower Hourglass reflection stroke.
- Refine Fireworks with four staged bursts, 120 small glowing embers and a light central flash; respect reduced motion and existing duration settings.
- Preserve all existing shortcode slugs, saved data, Divi integration and color settings.

## 0.9.25 — Hourglass and Orbit visual refinement (staging candidate)
- Rebuilt Hourglass artwork as a uniquely clipped SVG with a glass silhouette, upper and lower sand levels, a bottom mound and independent staggered grains. Inspired by the falling and accumulating sand behavior in comehope's Hourglass loader CodePen, with original implementation and no copied source or external runtime libraries.
- Reimagined Orbit Motion as an elapsed-time progress arc with two inclined satellite tracks, rather than two undifferentiated spinning borders.
- Kept the existing `clock_style="hourglass"` and `clock_style="orbit"` slugs, shortcode validation and stored data unchanged. JavaScript adds only `aria-hidden` decorations and uses the existing 1s clock tick for sand levels. The numeric text is always server-rendered.
- Fixed 320px and 390px layout overflow for the enlarged illustrated styles. Preserved existing color, size, label, typography and timezone options.
- Decorative motion is paused offscreen and disabled under reduced-motion preference. Real WordPress and Divi 5 validation is still pending.

## 0.9.24 — Eight countdown appearances (staging candidate)
- Added independent 3D Odometer, Neon Digital, Liquid, Pixel Matrix, Orbit Motion, Cyber Glitch, Particle Morph and Hourglass styles, using the existing live countdown engine.
- Extended the Generator appearance selector, frontend rendering and saved shortcode validation without migrating stored settings or changing the Divi 5 simplified module.
- Added progressive digit effects with server-rendered text fallback; paused offscreen decorative animations and disabled pseudo-element animations for reduced-motion preferences.
- Preserved existing style slugs and legacy shortcodes. PHP/JS and isolated-browser checks performed; live WordPress/Divi and full accessibility/performance QA pending.

## 0.9.23 - 2026-11-05 (candidate)
- Fireworks now exposes the existing, validated clock panel background color control. Set a six/eight-digit HEX color or remove the panel using the transparent checkbox; saved shortcodes and Divi rendering use the same setting.
- Hero now contains just four highlights in one non-wrapping row: Dynamic shortcodes, Reusable milestones, SEO variables and Countdowns. Removed Divi 5 ready, reduced chip type/icon size; on narrow displays the row scrolls horizontally instead of wrapping.
- Preserved saved shortcode identifiers, clock calculation, animations, SEO integrations, Divi module metadata and previously saved values. Real WordPress and Divi runtime validation remains pending.

## 0.9.22 - 2026-11-05 (candidate)
- Reorganized About & guide into four accessible subtabs: How it works, Examples, SEO plugins, and Divi. Retained code-copy controls and revised simplified Divi module instructions.
- Added only three hero chips: Divi 5 ready, SEO variables, and Countdowns; existing two chips are unchanged.
- Added PHP 8.2+ security recommendation with PHP2Shell patched release baselines and PHP 8.2 EOL guidance (minimum requirement still 8.1).
- Introduced optional Fireworks celebration countdown style: once-only zero event, custom final message, 3/5/8-second duration, localized strings, reduced-motion support, preview, isolated CSS/JS, and saved shortcode compatibility.
- No WordPress production or real Divi Visual Builder QA performed; candidate for staging.

## 0.9.21 - 2026-10-30 (candidate)
- Replaced the overloaded Divi 5 module interface with **Content → Saved Shortcode** and **Design → Local Appearance Overrides**. The module identifier remains `alpdt/dynamic-time`; no live-site module migration was needed per user confirmation.
- Added per-module number/text, label and accent colors, font size, font family and alignment without modifying the Saved Shortcodes library. Retained native Divi background, layout and spacing options.
- Added an authenticated WordPress REST preview that uses the same PHP shortcode renderer as the frontend; JS no longer implements duplicate date arithmetic for module preview. Countdown refreshes after VB content changes.
- Preserved the full shortcode generator, saved dates, saved shortcodes, SEO integrations, compatibility aliases, Quick Colors, site timezone and frontend clock animations.
- Available isolated tests passed; **real WordPress / Divi Visual Builder and Theme Builder testing remains pending**.

## 0.9.20 - 2026-10-30
- Prefer Divi's own `vendor.wp.element` React hooks to prevent a mismatched React instance in the Visual Builder; retain compatible fallback.
- Align the builder package dependencies with the documented `divi-vendor-wp-hooks` handle, loaded in the app window before render registration.
- Extend the Shortcode generator intro across the dashboard content width; preserve wrapping on small screens.
- Add a regression test simulating separate React implementations and initial module insertion.
- The original Visual Builder failure has not been reproduced in a real Divi 5 installation; staging verification remains necessary.

## 0.9.20 — Divi 5 new-module insertion guard (staging candidate)
- Prevent a Visual Builder render error when Divi supplies partially initialized `elements` or attributes during module insertion.
- Keep native module style/classname/script-data callbacks but guard them until Divi provides the corresponding APIs.
- Show a non-blocking placeholder while Divi initializes the module instead of throwing on missing `styleComponents`.
- Add red-before/green-after initial-insertion regression test and PHP regression for empty newly inserted module attributes.
- No changes to shortcode formats, stored dates, saved shortcode identifiers or module metadata / attribute names.
- The original screenshot alone is insufficient to confirm the runtime exception; real Divi 5 reproduction and verification remain PENDING.

## 0.9.18 — Navigation tab icons (staging candidate)
- Added user-supplied brackets, calendar and compatibility SVGs to the five WordPress dashboard tabs.
- Added bookmark and book icons for Saved shortcodes and About & guide.
- Icons are decorative SVG masks adopting current text color; keyboard navigation, accessible text, saved data and shortcode logic are unchanged.
- Made icon spacing and sizing responsive without changing native Divi controls.
- Real WordPress/Divi integration remains unverified.

## 0.9.17 — Action controls and SEO variables for saved text (staging candidate)
- Quick Colors: compact swatches, separate Add Color action and Manage Colors mode revealing accessible red remove controls only when requested.
- Generator: Copy shortcode + native Rank Math/Yoast variable controls for text. Current year is ready immediately; other configurations can be saved to register SEO variables, with no invented/unregistered tokens.
- Saved Shortcodes: copy native Rank Math and Yoast SEO variable markers for saved non-countdown text configurations.
- Styling: responsive, spaced ALP actions and clearer hierarchies, including saved shortcode actions.
- Existing date calculations, saved identifiers, countdown animations, theme colors and shortcode compatibility unchanged.
- Live WordPress/Divi/SEO editor validation remains pending.

## 0.9.16 — Updated Quick Colors defaults (staging candidate)
- Replaced old pastel preset swatches with red `#F33B49`, blue `#053197` and green `#07B57B`; added yellow `#FFCC00`. Retained black and white, for six starting colors.
- Existing users' personal Quick Colors migrate only swatches matching the three old preset HEX values; other custom swatches, removed items and empty palettes are preserved. Add yellow once during eligible migration, within the 24-color limit.
- No changes to countdown rendering styles, stored shortcodes, Divi module attributes or settings. PHP/JS syntax and isolated regression checks recorded separately.

## 0.9.15 — Named saved shortcodes (staging candidate)
- Added Save shortcode action to the generator and a Saved shortcodes library with copy, edit and SafeEdit-style delete confirmation.
- Added named reusable `[alp_time saved="identifier"]` reference, resolved safely without nested shortcode evaluation; existing full shortcodes continue unchanged.
- Added strict input validation, administrator/nonce protections, maximum 100 items, localized notices and mobile layout.
- Preserved the separate Saved dates option and all existing date/calculation/Divi module attributes.
- Static and isolated browser tests do not replace WordPress/Divi runtime validation.

## 0.9.14 — Editable Quick Colors
- Replace the twelve fixed swatches with five defaults: black, pastel red, pastel blue, pastel green, white.
- Add (+) the selected HEX/HEX-alpha color and remove (×) a saved swatch with a SafeEdit-style confirmation.
- Save the palette to each administrator's WordPress user meta, shared across the five color pickers; changes never rewrite existing countdown styles.
- Retain legacy shortcodes, saved dates and Divi module data unchanged.

## 0.9.13 - Divi-inspired color editor
- Larger, responsive dark color panel with hue, saturation/brightness, adjustable opacity, direct HEX input and ALP quick swatches. No dependence on Divi private APIs.
- Supports six-digit and eight-digit HEX colors across shortcode and Divi previews; existing saved values remain valid.

## 0.9.12 (2026-10-29)
- Countdown generator groups options into keyboard-accessible Countdown Units, Style and Unit Labels tabs, showing units first for Custom live clock.
- Adds a configurable Flip Digits panel background: HEX color or transparent; card colors remain independent. Whitelisted shortcode and Divi 5 module option.
- Keeps saved dates, legacy shortcodes, established option names and builder module name unchanged.
- Candidate for staging; full WordPress and Divi UI testing pending.

## 0.9.11 (2026-10-29)
- Standardized explicit copy buttons with clipboard icons and green success feedback for generator, saved-date shortcodes, guide examples, and SEO examples. Manual selection fallback on clipboard error.
- Renamed hero eyebrow to ALP Plugins and restored bordered, icon-bearing hero chips.
- Added accessible admin fields for Anniversary Message before/on/after text using `{years}`; existing shortcode attributes remain compatible.
- Clarified read-only legacy scan results and independence from Years Since - Timeless Texts plugin.
- Registered native Rank Math and Yoast SEO variables for current year and saved dates, preserving original shortcode-based output filters; editor counters may still differ from published metadata.
- Added browser and PHP isolated verification. No real WordPress or Divi 5 runtime certification.

## 0.9.10 (2026-10-28)
- Corrected optical vertical alignment of Flip Digits colon separators: visible punctuation is centered against the split-flap cards, rather than only centering its CSS line box.
- Correction scales proportionally with 24px, 42px and 64px digits; existing mobile two-row layout remains unchanged.
- No shortcode, saved date, Divi module attribute, font, or countdown calculation changes.

## 0.9.9 (2026-10-28)
- Sliding number overlay now uses the live computed font size, family and weight, matching responsive/Divi typography.
- Countdown unit captions automatically use the WordPress site language and support six optional plain-text overrides in shortcodes, admin generator and Divi 5.
- Lato is identified clearly as a fallback-only font, without downloading external resources.
- No migrations to existing date IDs, shortcodes or Divi module identifiers.

## 0.9.8 · 2026-10-28 · HEX-first color popovers, proportional rings, reset and centered flip separators
- The color swatch opens an ALP-scoped HSV/HEX palette. HEX is always displayed at the bottom of the panel; browser-native RGB popups are no longer used.
- Restore appearance defaults without changing dates, mode or selected countdown units.
- Flip colons centered vertically between digit cards at all supported sizes.
- Circular SVG dial diameter scales proportionally to chosen digit font size, including in shortcode/server output and Divi preview.
- Improved responsive flex layout for dial units. Existing shortcode, saved date and Divi persisted field names preserved.

## 0.9.7 · 2026-10-28 · Proportional flip clock, staged halves, responsive layout and HEX color fields
- The flip upper half holds the old value until its hinge finishes; the lower half now changes only after its own hinge, correcting premature bottom-digit updates.
- Added orange colon separators between clock units and moved flip labels beneath the digits, with an optional dark clock panel and screenshot-inspired palette. Unit/shortcode values remain unchanged.
- Flip card frame dimensions now follow the effective digit font size, including typography changes in Divi; the builder reacts to visual design settings even if the numerical value is unchanged.
- Added responsive two-column countdown arrangement on narrow screens with narrower glyphs and safe gaps. No global styles or Divi native controls are changed.
- Replaced cramped color input layout with ALP-labeled HEX text inputs first, synced native swatches second; invalid HEX values cannot enter shortcode output and revert on blur. Flip preset colors are selected only when the user has not customized them.
- Isolated Chromium tests cover visual layout at 1280/768/390/320 px, proportions (64px down to 24px), flip stage timing and HEX-first input; WordPress and real Divi 5 still require staging verification.

## 0.9.6 · 2026-10-28 · Flip cards, large circular progress and appearance controls
- Reworked Flip Digits into individual light split-flap cards with central seam, red digits and 3D hinges, following the visual behavior of the reference CodePen using an original implementation without external libraries.
- Replaced the small conic dial with a larger SVG circular progress timer (default 170px) and user-selectable diameter. Progress stroke animates between countdown values, and supports track/accent colors.
- Added scoped, validated clock colors, font family, number and label sizing in Generator and Divi 5. Attributes are additive and older layouts retain their data.
- Styled full-mode clocks only when a non-legacy animated style was explicitly selected, keeping the basic full/day outputs and prior shortcode compatibility.
- Verified JS/CSS/PHP and an isolated Chromium visual fixture. WordPress/Divi 5 runtime, accessibility, performance and official release checkers still require staging verification.

## 0.9.5 · 2026-10-25 · Dropdown, clock animations and Divi preview refinement
- Added ALP dropdown overlays with visible selection and keyboard navigation to the admin generator. Native select remains a no-JS fallback; not applied to Divi controls.
- Clarified that `YYYY-MM-DD` means year-month-day and restored an explicit date example. Added an About & guide tab with SEO examples and accurate shortcode/metadata cache cautions.
- Explicitly disclosed that the removed Years Since - Timeless Texts plugin and ALP Dynamic Time are independent and unaffiliated.
- Replaced whole-digit placeholder motion with actual two-half hinged flip cards and a rolling slide transition, added circular progress timer option. Preserved existing styles and unit selection; honors reduced motion.
- Both hero clock hands rotate at different speeds around a shared pivot. Existing WordPress data and Divi module identifiers remain intact.
- Added separate "Countdown Appearance & Animation" group to Divi 5 and server-calculated numerical updates in the Visual Builder preview using the plugin REST endpoint, with local fallback. Still pending live Divi verification.
- Tested source/shortcodes with CLI PHP/Node; Chrome mock visual checks are not equivalent to WordPress or Divi production tests.

# Changelog

## 0.9.4 · 2026-10-25 · Author attribution, Divi grouping, customizable clocks
- Corrected plugin Author to ALP Plugins, added Author URI and native Settings link.
- Removed redundant Divi layout decorations on computed value/caption, preserving their persisted fields and typography attributes.
- Added flexible clock-unit selection in admin and Divi 5 with new lightweight original flip, slide, pulse and sweep animations.
- Made hero clock's minute hand stationary while its second hand rotates.
- Added isolated PHP/JS tests and Spanish translations; no live WordPress/Divi verification claimed.

## 0.9.3 · 2026-10-25 · Generator, countdown and ALP UI candidate
- Success check with green confirmation after clipboard copying; failures remain neutral and advise manual copying.
- Friendlier date formats (examples instead of raw `j F Y`), native ALP dropdowns, explicit day/month/year help.
- Copyright year-only or range display, preserving default range behavior and existing shortcodes.
- New exact-time countdown (WordPress timezone) with days/hours/minutes/seconds, optional animated glow and reduced-motion fallback. Legacy date-only countdown keeps its results.
- Hero animated background and clock without the accent edge stripe. Enhanced accessible SafeEdit-style delete dialog.
- Added matching experimental Divi 5 controls for countdown format, optional glow and copyright style, retaining module identity and saved fields.
- PHP/JS isolated tests expanded. Real WordPress/Divi activation and visual testing remain pending.

## 0.9.2 · 2026-10-25 · Divi 5 configuration candidate
- Fixed unnamed Content accordion: explicit `Dynamic Time`, `Display Text` and `Anniversary Messages` groups, all with registered `groupSlug` bindings.
- Added all eight backend generator types to the Divi 5 module, including current year for automatic footer copyright, and configurable text before/after the value.
- Added saved date ID, countdown target, date format, elapsed unit, year mode, ordinal / anniversary messages to Divi 5 settings.
- Preserved Divi module identity and existing date/caption paths; removed inline editing of the calculated value to prevent overwriting its source date.
- Added PHP adapter and isolated PHP + mocked builder regression tests. Live Divi 5 Visual Builder and Theme Builder checks remain unperformed.


## 0.9.1 · 2026-10-25 · UI/UX candidate
- Redesigned ALP admin hero, centered layout, cards, input spacing and red destructive actions; scoped external-notice suppression to the plugin screen.
- Added plain-language option descriptions, contextual generator fields and cleaner shortcode output.
- Added editing saved dates without changing shortcode identifiers, cancel editing, and server-side duplicate-ID protection.
- Updated Spanish gettext catalogs and migration terminology. Preserved date engine, public shortcode and stored options.
- Requires actual WordPress/Divi screen testing before production use.


## 0.9.0 · 2026-10-25 · Development candidate
- Created original date engine, strict input parser, legacy shortcode adapter, admin generator, saved dates, Gutenberg block and block binding.
- Added read-only REST preview, opt-in frontend live refresh, SEO text hooks, WP-CLI, and experimental Divi 5 native module.
- Added PHP isolated tests and pre-release documentation. **No runtime WordPress/Divi test claim.**
