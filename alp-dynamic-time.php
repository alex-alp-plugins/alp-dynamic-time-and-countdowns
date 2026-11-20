<?php
/**
 * Plugin Name: ALP Dynamic Time & Countdowns
 * Description: Dynamic dates, smart calendar expressions, anniversaries and animated recurring countdowns for WordPress and Divi.
 * Version: 1.0.0
 * Requires at least: 6.5
 * Requires PHP: 8.2
 * Author: ALP Plugins
 * Author URI: https://alpplugins.com
 * Text Domain: alp-dynamic-time
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit;

define( 'ALPDT_VERSION', '1.0.0' );
define( 'ALPDT_DIR', plugin_dir_path( __FILE__ ) );
define( 'ALPDT_URL', plugin_dir_url( __FILE__ ) );

require_once ALPDT_DIR . 'includes/class-alpdt-engine.php';
require_once ALPDT_DIR . 'includes/class-alpdt-clock.php';
require_once ALPDT_DIR . 'includes/class-alpdt-schedule.php';
require_once ALPDT_DIR . 'includes/class-alpdt-saved-shortcodes.php';
require_once ALPDT_DIR . 'includes/class-alpdt-shortcodes.php';
require_once ALPDT_DIR . 'includes/class-alpdt-admin.php';
require_once ALPDT_DIR . 'includes/class-alpdt-integrations.php';
require_once ALPDT_DIR . 'includes/class-alpdt-block.php';
require_once ALPDT_DIR . 'includes/class-alpdt-api.php';
if ( defined( 'WP_CLI' ) && WP_CLI && class_exists( 'WP_CLI' ) ) {
    require_once ALPDT_DIR . 'includes/class-alpdt-cli.php';
    WP_CLI::add_command( 'alpdt', 'ALPDT_CLI' );
}

add_action( 'init', array( 'ALPDT_Shortcodes', 'register' ), 20 );
add_action( 'init', array( 'ALPDT_Block', 'register' ) );
add_action( 'init', array( 'ALPDT_Integrations', 'register' ) );
add_action( 'rest_api_init', array( 'ALPDT_API', 'register' ) );
add_action( 'admin_menu', array( 'ALPDT_Admin', 'menu' ) );
add_action( 'admin_init', array( 'ALPDT_Admin', 'redirect_legacy_menu_url' ), 5 );
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( 'ALPDT_Admin', 'settings_link' ) );
add_action( 'admin_init', array( 'ALPDT_Admin', 'post_actions' ) );
add_action( 'admin_enqueue_scripts', array( 'ALPDT_Admin', 'menu_icon_assets' ) );
add_action( 'admin_enqueue_scripts', array( 'ALPDT_Admin', 'assets' ) );
// Register the bundled language directory for WordPress's just-in-time
// translations. WP_Textdomain_Registry is available since 6.1, below our 6.5
// minimum; this preserves private-ZIP translations without the discouraged
// manual translation loader.
add_action( 'init', static function () {
    global $wp_textdomain_registry;
    if ( $wp_textdomain_registry instanceof WP_Textdomain_Registry ) {
        $wp_textdomain_registry->set_custom_path( 'alp-dynamic-time', ALPDT_DIR . 'languages' );
    }
} );
// Divi builds its dependency tree during early init. Subscribe before it starts.
add_action( 'divi_module_library_modules_dependency_tree', static function ( $tree ) {
    if ( ! function_exists( 'et_builder_d5_enabled' ) || ! et_builder_d5_enabled() || ! is_object( $tree ) || ! method_exists( $tree, 'add_dependency' ) ) {
        return;
    }
    require_once ALPDT_DIR . 'divi5/module.php';
    if ( class_exists( 'ALPDT_Divi5_Module', false ) ) {
        $tree->add_dependency( new ALPDT_Divi5_Module() );
    }
} );
