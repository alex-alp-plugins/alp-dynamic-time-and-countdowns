# ALP Dynamic Time & Countdowns 1.0.0-rc.2 | QA de fuentes

**Fecha:** 2026-11-19  
**Base:** ZIP instalable 1.0.0-rc.1 proporcionado en la conversación.  
**Estado:** candidata de lanzamiento; no desplegada ni publicada.

## Cambios

1. Eliminadas las familias **Black And White Picture** y **Rubik Scribble** de la lista de tipografías, mapas JavaScript/PHP y de `@font-face` (88 + 7 archivos WOFF2, respectivamente). Las selecciones antiguas de estas familias se degradan al estilo predeterminado; no se eliminan datos guardados.
2. Carga de fuente personalizada en **Generator → Countdown → Typography → Font family**: WOFF2/WOFF/TTF/OTF, hasta 2 MiB por archivo y 12 fuentes. Requiere `manage_options` y `upload_files`, nonce AJAX y `wp_handle_upload()` con MIME permitidos de fuentes, además de cotejar extensión, tamaño y firma binaria.
3. Cada fuente se guarda como attachment de la Biblioteca de medios, de acceso público; se referencia por `clock_font="custom_<attachment_id>"`. No se almacena CSS arbitrario ni URLs proporcionadas por el usuario. Renderiza vía CSS local y `font-display:swap` en admin y frontend. Evitar terceros y no incluir uploads en ZIP.
4. Los shortcodes, diseños, elementos de Gutenberg, identificadores Divi, opciones anteriores, SVGs de marca y footer no se modifican.

## APROBADAS

- **15 PHP:** `php -l` sin errores.
- **5 JavaScript:** `node --check` sin errores.
- **4 CSS:** análisis con tinycss2 sin errores de nivel superior.
- **19 comprobaciones PHP simuladas:** firma y tipo, rechazo de extensiones doble/ejecutables, tamaño, control de acceso y nonce, carga simulada, registro de attachments, generación segura de CSS y de `clock_font`, rechazo de las dos fuentes eliminadas, deduplicación y máximo de 12 fuentes, rechazo de URL maliciosa y control de `wp_add_inline_style` único.
- **Chromium aislado:** subida simulada, selección dinámica, estado accesible, CSS generado, petición `FormData` y ausencia de scroll horizontal en anchos 320/375/390/768/1024/1440 px.
- **ZIP:** comprobar encabezado/versiones, consistencia de carpeta raíz, eliminación de los dos directorios y de sus referencias en código/CSS, integridad `ZipFile.testzip()`.

## PENDIENTES

- Verificar carga real mediante `wp_handle_upload` / `wp_check_filetype_and_ext` con archivos font válidos en WordPress/PHP 8.2+ y en hosting; también estados de error por políticas MIME del servidor.
- Verificar persistencia del attachment y uso al editar/cargar shortcodes tras recarga, Gutenberg y Divi 5 reales.
- Probar fuentes variables y estáticas reales en diferentes navegadores y contraste y accesibilidad con tecnología asistiva.
- Revisar WordPress administrado en multisite y sitios que descargan Media Library a almacenamiento remoto.

## NO DISPONIBLES

- PHPStan, PHPCS/WPCS, WP-CLI/WordPress real y WordPress Plugin Check.

Una prueba PHP simulada y una vista en Chromium sin WordPress no equivalen a validación de integración funcional ni a aprobación del directorio WordPress.org.
