# ALP Dynamic Time & Countdowns · QA 1.0.2

**Fecha:** 2026-11-20  
**Base:** 1.0.0, ZIP estable de la conversación.  
**Alcance:** texto SEO y eliminación de referencias a una posible edición Pro; estilos de foco en tabs y paneles; velocidad de Blood Dripping; orden aleatorio de globos de celebración. Sin migraciones de base de datos.

## Cambios

1. `README.md`, `readme.txt` y cabecera del plugin: descripción centrada en WordPress countdown timer, dynamic dates, 20 countdown designs, anniversary counters, saved shortcodes, Gutenberg y Divi 5. Sin ofertas de productos Pro hipotéticos. La descripción corta de readme tiene 133 caracteres; la cabecera PHP, 145.
2. `assets/admin.css`: se elimina el contorno turquesa de los tabs, los paneles con `tabindex=0` y la sección del Generador. Para teclado, los tabs mantienen un subrayado interior azul marino; los paneles, una marca lateral azul marino. Otros controles conservan indicadores de foco.
3. `assets/countdown.js`, `assets/countdown.css`: gotas Blood Dripping de 3,0 a 4,7 s en lugar de 1,4 a 2,52 s, con fases negativas desincronizadas; pooling/absorption intactos.
4. `assets/countdown.js`: posiciones de globos por carriles mezclados con Fisher-Yates, retrasos independientes aleatorios, colores mezclados; se mantienen cantidad, movimiento curvo, CSS original, reducción de movimiento y pausa fuera de pantalla.
5. Versión actualizada de 1.0.0 a 1.0.2. No se alteraron shortcodes, integraciones, opciones, clases PHP funcionales, datos ni recursos SVG oficiales.

## APROBADAS (ejecutadas)

- **PHP:** `php -l` en 14 archivos PHP, en CLI PHP 8.4.24. Sin errores de sintaxis.
- **JavaScript:** `node --check` en 5 archivos JS, Node v22.16.0. Sin errores.
- **CSS:** 4 archivos analizados con tinycss2 sin errores top-level.
- **Chromium aislado:** comprobación computada de foco en tab/panel/selector del Generador sin rectángulo turquesa, en 320/375/768/1024/1440 px y sin desplazamiento horizontal en el escenario de prueba.
- **Chromium aislado:** generación real de gotas Blood Dripping, con duración en 3,0–4,7 s y desfases; 3 ejecuciones independientes de globos con orden de posiciones y tiempos no secuencial.
- **Accesibilidad parcial:** estilos de foco visibles, `prefers-reduced-motion: reduce` desactiva animación de globos y gotas en simulación; sin excepciones JavaScript en esas pruebas aisladas.
- **Metadatos:** `Version`, constante `ALPDT_VERSION`, `Stable tag` y versiones documentales alineadas en 1.0.2. Descripción WP principal de 145 caracteres y descripción corta WordPress.org de 133 caracteres. 20 estilos reales en el selector.
- **Integridad:** ZIP completo y comparación de archivos contra 1.0.0 sin cambios ajenos al alcance.

## PENDIENTES

- Activar, actualizar, y comprobar en instalación WordPress real; validar Divi 5 Visual Builder, Gutenberg, shortcode rendering, SEO metadata y caché.
- Verificación visual funcional dentro de wp-admin junto con otros plugins, focus/hover/teclado en Safari y Chrome reales y contraste con capturas del usuario.
- Prueba explícita en PHP 8.2 en servidor WordPress, y comprobación de parches de seguridad PHP del proveedor.
- Pruebas reales de animación ante eventos de fin de countdown, múltiples estilos y cambios de velocidad en dispositivos móviles.

## NO DISPONIBLES

- PHPCS/WPCS, PHPStan, WordPress Plugin Check (no instalados en el entorno).

**Estado:** paquete de mantenimiento completo para prueba en staging, no publicado automáticamente en GitHub ni en WordPress.org. El navegador aislado no equivale a una ejecución integrada en WordPress/Divi.
