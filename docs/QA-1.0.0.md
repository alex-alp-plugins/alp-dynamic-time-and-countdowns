# ALP Dynamic Time & Countdowns 1.0.0 — verificación de primera versión estable

**Fecha:** 20 de noviembre de 2026  
**Fuente:** paquete íntegro 1.0.0-rc.3, conservado sin cambios funcionales.

## Alcance de esta entrega

- Versión de cabecera PHP y constante `ALPDT_VERSION`: `1.0.0`.
- `readme.txt`: `Stable tag: 1.0.0`, descripción pública, FAQ y changelog resumidos.
- `README.md`: página de inicio de GitHub simplificada, orientada a características e instalación.
- `CHANGELOG.md`: primera versión estable y correcciones principales; el historial de desarrollo permanece en Git.
- `docs/FONTS.md`: indicación actualizada de que no existen subidas personalizadas en 1.0.0.
- Sin cambios en shortcodes, datos guardados, endpoints REST, PHP funcional, JavaScript, CSS, atributos de Gutenberg o Divi.

## APROBADAS (realmente ejecutadas)

- `php -l` en 14 archivos PHP, sin errores sintácticos.
- `node --check` en cinco archivos JavaScript, sin errores sintácticos.
- `tinycss2`: cuatro CSS analizados, sin errores de sintaxis de nivel superior.
- Inventario de archivos del ZIP 1.0.0-rc.3: se preservaron los 99 archivos originales (más este reporte).
- En el generador se verificaron exactamente **20 opciones seleccionables** de estilo de countdown; no se contabilizaron las variantes técnicas antiguas no expuestas.
- Coinciden encabezado PHP `Version`, constante `ALPDT_VERSION` y `Stable tag` en `readme.txt`.
- La descripción corta de WordPress.org mide 116 caracteres, dentro del límite recomendado de 150.
- Solo cambian documentación, cabecera/constante de versión y este informe; archivos funcionales intactos.
- ZIP de distribución con un directorio de plugin, sin errores de compresión (verificación externa de empaquetado).

## PENDIENTES

- Activación y prueba de actualización en una instalación real de WordPress.
- Prueba de Divi 5 Visual Builder / Theme Builder y bloque Gutenberg real.
- Prueba funcional de REST, SEO plugins instalados, compatibilidad e interfaz en navegadores de producción.
- Capturas, teclado, accesibilidad y responsividad en una instalación real de wp-admin.
- Validación del campo `Tested up to: 7.1` contra una instalación real, actualmente es un dato heredado de la etapa de desarrollo.

## NO DISPONIBLES en este entorno

- PHPCS/WPCS, PHPStan, Plugin Check y pruebas reales de WordPress/Divi.
- Verificación de versión de PHP servida por el hosting / parches de seguridad.

**Nota:** se etiqueta `1.0.0` como versión estable inicial por decisión del propietario; esto no equivale a una certificación de compatibilidad o aprobación de WordPress.org.
