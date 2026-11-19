# ALP Dynamic Time & Countdowns 1.0.0-rc.2 | QA técnico

Fecha de revisión: 2026-11-19. **Estado:** release candidate de desarrollo; no publicada en WordPress.org.

## Alcance

- Base: ZIP proporcionado `alp-dynamic-time-cd-0.10.28-rc.3.zip` (193 archivos), sin reconstruir el plugin desde cero.
- Gutenberg `alpdt/time`: conservar el identificador, reducir a un único atributo persistido `saved`, un selector visual de shortcode guardado y el isotipo oficial. El generador es la fuente única de configuraciones.
- Al ser el primer lanzamiento público, **no se conserva compatibilidad con los atributos experimentales de bloques Gutenberg pre-1.0** (`type`, `from`, `to`, `mode`, `unit`, `text`, `id`). No se migran. Los sitios que probaron versiones RC anteriores deberán volver a seleccionar un shortcode guardado en Gutenberg.
- Los shortcodes `[alpdt_time]`, datos de fechas, biblioteca de shortcodes, módulo Divi 5 y API del plugin no se modifican.
- Rendimiento/seguridad: la configuración completa no viaja al navegador en los datos del editor, solamente etiquetas e identificadores previamente validados; el renderizado resuelve la clave guardada por el motor oficial y devuelve vacío para entradas inválidas o eliminadas.

## APROBADAS (ejecutadas)

1. `php -l` en **14** archivos PHP: sin errores de sintaxis.
2. `node --check` en **todos** los JS del plugin: sin errores de sintaxis.
3. `tinycss2.parse_stylesheet` en cuatro hojas CSS: sin errores de nivel superior.
4. Prueba de registro de bloque con funciones WordPress simuladas (`tests/test_gutenberg_rc.php` ejecutado fuera del paquete): una sola clave `saved`, permisos, etiquetas sanitizadas, opciones, renderizado del shortcode guardado y fallo cerrado para referencias ausentes, verificadas.
5. Prueba de editor con runtime JavaScript simulado (`tests/test_gutenberg_rc.cjs` ejecutado fuera del paquete): un único selector, selección persistida, opción eliminada no sustituida, biblioteca vacía y método `save()` nulo.
6. Comparación de contenido contra ZIP 0.10.28-rc.3: **ningún archivo anterior eliminado**, sin modificaciones en SVG, fonts, Divi 5, API ni shortcodes de backend. Los cambios se limitan a Gutenberg, documentación, versión y traducciones; se agrega este informe.
7. ZIP final: validación `testzip`, manifiesto, encabezado y estructura de carpeta raíz.

## PENDIENTES

- Instalación, activación y pruebas de selección/guardado/recarga en **WordPress Gutenberg real**.
- Comprender la salida de front-end y scripts CSS/JS de countdowns seleccionados en diferentes temas.
- Abrir Divi 5 Visual Builder y verificar que la integración ya existente sigue funcionando.
- Pruebas WCAG y de lector de pantalla en contexto real.
- Revisión de PHP servido en hosting y actualizaciones de PHP de su proveedor.

## NO DISPONIBLES en esta sesión

- PHPCS/WPCS, PHPStan, WordPress Plugin Check y servidor WordPress funcional.

Una prueba aislada o de sintaxis no equivale a una validación completa de WordPress/Divi. No se publica ni despliega sin autorización.
