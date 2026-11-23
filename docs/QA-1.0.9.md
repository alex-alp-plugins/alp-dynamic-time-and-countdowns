# ALP Dynamic Time & Countdowns 1.0.9 | Identifier UX

Baseline: published v1.0.8 distribution ZIP, canonical `alp-dynamic-time-countdowns/` root.

## Scope

- Two existing fields: `#alpdt-slug` (saved dates) and `#alpdt-shortcode-slug` (reusable saved shortcode modal).
- Input normalization only: lowercasing, Unicode accent stripping, whitespace-to-hyphen conversion, filtering unsupported characters, cursor preservation, IME composition guard.
- English helper text and translated es_CR/es_ES PO/MO messages, linked with `aria-describedby`.
- No changed option keys, shortcodes, REST hooks, validation rules, CSS or saved data.

## Acceptance examples

- Enter `alex ingreso` -> `alex-ingreso` (including typed trailing space).
- Paste `Álex Ingreso` -> `alex-ingreso`.
- New identifier remains editable; existing saved identifiers remain read-only.
- Shortcode modal's automatic slug from a configuration name remains functional and can be manually edited.

## QA evidence

See actual executed test log for this package. Static tests do not replace WordPress+Divi+browser validation.
