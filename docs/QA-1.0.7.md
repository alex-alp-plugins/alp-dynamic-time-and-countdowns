# ALP Dynamic Time & Countdowns 1.0.7 · Packaging QA

**Base:** original user-uploaded 1.0.6 ZIP and Plugin Check report dated 2026-11-20.  
**Scope:** fix distribution archive structure and release metadata only; no WordPress database, UI or business logic change.

## Root cause and correction

- Input ZIP contained `alp-dynamic-time.php`, `includes/`, `assets/`, etc. **at archive root**, without a stable plugin folder. WordPress/Plugin Check used a directory derived from a transient/versioned installation name, not the established plugin slug.
- The report has **525 errors** of type `WordPress.WP.I18n.TextDomainMismatch` and **1 warning** `textdomain_mismatch`, all pointing to the same discrepancy: expected a versioned/temporary slug, actual and intentional `alp-dynamic-time`.
- Output ZIP contains **exactly one root folder**: `alp-dynamic-time/`, with the original main PHP file `alp-dynamic-time/alp-dynamic-time.php`. The plugin text domain and language file prefixes remain `alp-dynamic-time`.
- Main PHP version constant/header, WordPress readme Stable tag and documentation incremented to `1.0.7`.
- No files in `assets/`, `includes/`, `divi5/`, `blocks/`, `languages/`, or `uninstall.php` are modified. WordPress options/user metadata are never altered in the release process.

## Local checks and evidence

The build script checks ZIP root, version/header/text domain, known report issue types, unchanged runtime files, archive integrity, translation file names, and absence of unsafe ZIP paths. **APROBADO / Pruebas realizadas en este contenedor:**

- `php -l`: **14/14** archivos PHP sin errores de sintaxis (PHP CLI 8.4.24; PHP 8.2 no disponible para ejecutar pruebas).
- `node --check`: **5/5** archivos JavaScript sin errores de sintaxis (Node 22.16.0).
- `tinycss2.parse_stylesheet`: **4/4** hojas CSS sin errores de sintaxis de nivel superior (tinycss2 1.5.1). No es una validación visual del navegador.
- Integridad ZIP: **APROBADA**, sin entradas repetidas, rutas peligrosas o pérdidas de archivos; carpeta raíz única `alp-dynamic-time/`.
- Archivos ejecutables conservados: **101/101** archivos de contenido original sin modificar; solo metadatos/documentación modificados (`alp-dynamic-time.php`, `readme.txt`, `README.md`, `CHANGELOG.md`) y agregado este documento QA.
- Conteo del informe 1.0.6 verificado: **525** errores `WordPress.WP.I18n.TextDomainMismatch` + **1** advertencia `textdomain_mismatch`.
- Comprobación estática: coincidencia del slug del directorio, `Text Domain` y prefijos `.po`/`.mo`; no se modifica ninguna de las llamadas de traducción.

**NO EJECUTADO:** Plugin Check en WordPress real, instalación/actualización WP, Divi 5 Visual Builder, WP_DEBUG, tests funcionales con base de datos, revisión accesibilidad o validación visual.


## Pending in an actual WordPress site

- Upload/install 1.0.7 over an existing **canonical** `wp-content/plugins/alp-dynamic-time/` installation and verify WordPress offers/executes **Replace current with uploaded** without introducing a second plugin.
- On sites with a **noncanonical older folder**, deactivate the obsolete copy, back up the site, remove that duplicate safely and install/activate the canonical package. WordPress may not automatically replace a differently named plugin directory, even if the display names are identical.
- Run the WordPress **Plugin Check** plugin against the correctly installed 1.0.7 and confirm the previously reported mismatch is no longer present. No Plugin Check CLI is bundled in this execution environment.
- Confirm saved presets, dates, user Quick Colors, dynamic countdowns, Gutenberg, Divi 5 and SEO shortcodes on staging, with actual browsers at mobile and desktop widths.
- PHPCS/WPCS, PHPStan, true WordPress activation/update, and host PHP patch level: NOT RUN / NOT AVAILABLE locally.

## PHP security notice

Local PHP CLI version (8.4.24) is below the skill's referenced PHP2Shell fixed release (8.4.27). This is a runtime/hosting patch issue, not a plugin-package defect. Verify the separately served PHP version and vendor backports before production; no server runtime was inspected.

## Rollback

Keep a backup of plugin files and the WordPress database. If the new package fails, deactivate the new canonical plugin, restore the prior known-good plugin, and check saved data. **Do not activate two copies simultaneously.**
