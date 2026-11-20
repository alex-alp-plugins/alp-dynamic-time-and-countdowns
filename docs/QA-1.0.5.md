# ALP Dynamic Time & Countdowns 1.0.5 · QA

**Base:** ALP Dynamic Time & Countdowns 1.0.4 (ZIP de esta conversación).  
**Alcance:** compactar solamente el selector de colores propio del dashboard, incluyendo la última petición de reducir aún más los cuadros de colores. Sin migración de datos. **Sin despliegue en GitHub ni WordPress.**

## Cambios

- `assets/admin.css`: ventana de color limitada a 368 px, área de selección reducida de 225 a 136 px en escritorio (116 px en móvil), muestra de color de 62 a 32 px (30 px en móvil), controles HEX/opacidad y espaciados más compactos. Colores rápidos: cuadros de 41 a **26 × 26 px** (área visual ~60% menor), con su indicador seleccionado, botón de añadir y botones de eliminar al administrar la paleta.
- `assets/admin.js`: la anchura usada para colocar el popup se sincroniza con la del CSS (368 px). Se conserva la detección del menú de WordPress, barra superior y los bordes de pantalla, y el scroll interno.
- `alp-dynamic-time.php`, `readme.txt`, `README.md`, `CHANGELOG.md`: versión 1.0.5 y notas de cambio.

## APROBADO · Pruebas ejecutadas localmente

- `php -l`: 14 archivos PHP sin errores de sintaxis.
- `node --check`: 5 archivos JavaScript sin errores de sintaxis.
- `tinycss2`: 4 hojas de estilo sin errores de parseo de primer nivel.
- Chromium aislado con los JS/CSS reales del selector: apertura, valor HEX editable, cierre con Escape y clic fuera, no desbordamiento horizontal y ubicación dentro de la pantalla a 320×680, 375×720, 390×844, 768×800, 1024×768, 1440×900, 1766×864 y 1024×520 px.
- Chromium aislado: cuadros de paleta de 26×26 px, muestra entre 30 y 32 px, cambio de color con clic, visualización de los controles de eliminación en *Manage colors* y cierre mediante la cruz a 320, 375, 1024 y 1440 px.
- Chromium aislado: paleta de 24 colores rápidos en pantalla de 1024×520 px, con scroll interno hasta los últimos cuadros.
- Comparación de archivos con 1.0.4: no se eliminaron archivos. Cambios limitados a CSS/JS del editor, metadatos de versión y documentación.

## PENDIENTE

- Prueba de la ventana dentro de la instalación real de WordPress del usuario, incluidos plugins terceros, zoom a 200%, lector de pantalla, validación integral de accesibilidad y actualización 1.0.4 → 1.0.5.
- Pruebas de regresión integrales del Generador, Divi 5 y Gutenberg en WordPress real.

## NO DISPONIBLE

- PHPCS/WPCS, PHPStan y Plugin Check. Verificación de PHP servido por el hosting.

**Límite de las pruebas:** Chromium aislado comprueba el componente y su JS/CSS reales dentro de un documento que simula el marco de WordPress; no equivale a una instalación real del plugin ni a una auditoría completa WCAG.
