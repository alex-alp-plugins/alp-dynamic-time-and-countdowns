<?php
/**
 * Plugin Name: ALP Dynamic Time & Countdowns
 * Description: Free WordPress countdown timer and dynamic dates plugin with 20 designs, anniversary counters, reusable shortcodes, Gutenberg and Divi 5 support.
 * Version: 1.0.27
 * Requires at least: 6.5
 * Requires PHP: 8.2
 * Author: ALP Plugins
 * Author URI: https://alpplugins.com
 * Text Domain: alp-dynamic-time-countdowns
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit;

define( 'ALPDT_VERSION', '1.0.27' );
// Keep editable source assets for SCRIPT_DEBUG, use compact assets on public sites.
define( 'ALPDT_ASSET_SUFFIX', ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min' );
define( 'ALPDT_FILE', __FILE__ );
define( 'ALPDT_DIR', plugin_dir_path( __FILE__ ) );
define( 'ALPDT_URL', plugin_dir_url( __FILE__ ) );

if ( is_readable( ALPDT_DIR . 'vendor/autoload.php' ) ) {
	require_once ALPDT_DIR . 'vendor/autoload.php';
} else {
	require_once ALPDT_DIR . 'src/Infrastructure/Autoloader.php';
	\AlpDynamicTime\Infrastructure\Autoloader::register();
}

require_once ALPDT_DIR . 'includes/legacy-aliases.php';

// WordPress.org language packs load automatically from wp-content/languages/plugins.
// Do not override the text-domain registry with a package-specific path.
add_action(
	'plugins_loaded',
	static function (): void {
		\AlpDynamicTime\Plugin::instance()->boot();
	}
);
