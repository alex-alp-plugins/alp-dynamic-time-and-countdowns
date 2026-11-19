# ALP Dynamic Time & Countdowns 1.0.0-rc.3 — verificación

**Fecha:** 19 de noviembre de 2026  
**Base:** ZIP 1.0.0-rc.2 de la conversación.  
**Estado:** Release Candidate, sin despliegue ni publicación en WordPress.org.

## Cambios autorizados

1. Se retiró **Custom fonts**: campo de archivo, botón, scripts de subida, endpoint AJAX, clase de subida, integración CSS y fuente dinámica en las opciones. La funcionalidad no queda disponible en esta RC. Se mantienen las fuentes integradas ligeras y la eliminación de Black And White Picture y Rubik Scribble de rc.2.
2. No se borran adjuntos de la Biblioteca de medios ni la antigua opción `alpdt_custom_font_ids`. Si alguien guardó `clock_font="custom_123"` durante rc.2, en rc.3 se utiliza la fuente predeterminada. Esto evita borrar información existente durante la RC.
3. Se añadió la región `#alpdt-external-notices` **antes** del hero para mover ahí avisos identificados que otros plugins inserten dentro del hero mediante DOM (también inserciones posteriores). Conserva visibilidad, contenido y botones de cierre. Se eliminó CSS que ocultaba avisos `.notice`, `.updated` y `.error` a nivel de `#wpbody-content` en la pantalla del plugin.
4. No cambian Gutenberg `alpdt/time`, módulo Divi `alpdt/dynamic-time`, shortcodes, datos guardados, REST, paneles y el footer ALP.

## APROBADAS — pruebas ejecutadas

- `php -l` en **14 archivos PHP** (PHP CLI 8.4.24).
- `node --check` en **5 archivos JavaScript**.
- Parseo de **4 CSS** con `tinycss2`, sin errores de nivel superior.
- Traducciones `es_CR` y `es_ES`: lectura de PO, compilación de MO y validación de cadenas activas.
- `ALPDT_Clock::appearance()` en PHP CLI: Inter conserva su familia; las dos fuentes eliminadas y `custom_123` utilizan el estilo por defecto.
- Inventario contra rc.2: único archivo fuente eliminado `includes/class-alpdt-fonts.php`; logos, Divi, Gutenberg, countdown JS/CSS no cambiaron (hash SHA-256).
- No queda un endpoint AJAX `alpdt_upload_font` ni campos u opciones de selección de fuentes personalizadas.
- Chromium **aislado**, con la rutina exacta extraída de `assets/admin.js`: simulaciones de avisos dentro del hero (inicial y asíncrono), traslado a una región visible anterior al hero, conservación de botón de cierre, aviso externo visible y sin desplazamiento horizontal a **320/375/768/1024/1440 px**.
- ZIP integral: estructura y `ZipFile.testzip()` sin errores.

## PENDIENTES

- Verificar el problema exacto con el plugin SEO real que emite `SEO Notice: Your site is set to No Index`, incluida su inserción dinámica y su acción de descartar.
- Activar/actualizar en WordPress real, PHP 8.2+/versiones soportadas y Divi 5, guardar y reabrir shortcodes y verificar frontend/Gutenberg/Visual Builder.
- Comprobar zoom, lector de pantalla, y notices de otros plugins en wp-admin real.

## NO DISPONIBLES

- PHPCS/WPCS, PHPStan, WordPress Plugin Check y prueba con instalación real de WordPress/Divi.

**Nota:** las pruebas de navegador aisladas verifican el código del guard y sus interacciones simuladas; no prueban el comportamiento en la instalación del usuario.
