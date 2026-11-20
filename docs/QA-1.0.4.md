# ALP Dynamic Time & Countdowns 1.0.4 | QA

**Fecha:** 2026-11-20  
**Base:** ZIP completo ALP Dynamic Time & Countdowns 1.0.3, sin subir a GitHub.  
**Ámbito:** Selector de color dentro del Generador, especialmente los campos de color de Confetti/Balloons.

## Síntoma y causa

El popup de 468 px se posicionaba absolutamente dentro de la columna del formulario. Su cálculo de apertura hacia arriba o abajo no comprobaba los límites del menú lateral `#wpcontent` ni de la barra superior `#wpadminbar`: si no cabía sobre el campo, el selector podía quedar fuera de la pantalla o detrás del menú de WordPress.

## Corrección

- `assets/admin.css`: el popup usa `position: fixed` dentro del espacio de la pantalla ALP.
- `assets/admin.js`: calcula límites horizontales a partir de `#wpcontent` y verticales desde `#wpadminbar` hasta el borde inferior de la ventana; ajusta ancho y altura de popup sin desbordamiento, y utiliza desplazamiento interno cuando la altura natural excede el espacio.
- Evita desplazar la página al enfocar el campo HEX; cierra el popup si el usuario desplaza el formulario, pero permite desplazarse dentro del popup.
- No cambia tipos de controles, identificadores, colores guardados, endpoint de Quick Colors, Divi, Gutenberg, shortcodes, efectos o datos persistentes.

## APROBADO (pruebas ejecutadas)

- Parseo de JavaScript y CSS, `php -l` de archivos PHP.
- Fixture aislado de Chromium con **el código real `assets/admin.js` y `assets/admin.css`**: popup creado desde inputs de tres alturas de página en 320, 375, 768, 1024, 1440 y 1766 px. Afirmaciones de límites de WordPress toolbar, menú lateral, ventana, CSS fixed y cierre con Escape.
- Integridad ZIP y sincronización de metadatos de versión.

## PENDIENTE

- Reproducción y verificación en la instalación real mostrada por el usuario, con WordPress y plugins de terceros activos.
- Pruebas completas de Quick Colors/contraste/lectores de pantalla y responsive en WordPress real.
- Integración con Divi 5/Gutenberg en WordPress real (no se modificaron).

## NO DISPONIBLE

- PHPCS/WPCS, PHPStan y Plugin Check; pruebas de activación/actualización en WordPress real.

**Limitación:** El fixture no contiene el Generador completo. Una excepción JavaScript por controles ausentes tras registrar el selector es esperable en ese fixture; no aparece en la función de posicionamiento y no demuestra fallo ni éxito de la página de WordPress completa.
