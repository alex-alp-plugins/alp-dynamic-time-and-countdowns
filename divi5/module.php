<?php
/** Divi 5 saved-shortcode selector. Content editing remains in the ALP dashboard. */
defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/class-alpdt-divi5-data.php';

if ( ! interface_exists( '\\ET\\Builder\\Framework\\DependencyManagement\\Interfaces\\DependencyInterface' ) ||
     ! class_exists( '\\ET\\Builder\\Packages\\ModuleLibrary\\ModuleRegistration' ) ) { return; }

final class ALPDT_Divi5_Module implements \ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface {
    public function load() {
        if ( did_action( 'init' ) ) { self::register_module(); }
        else { add_action( 'init', array( __CLASS__, 'register_module' ), 100 ); }
    }
    public static function register_module(): void {
        \ET\Builder\Packages\ModuleLibrary\ModuleRegistration::register_module(
            ALPDT_DIR . 'divi5', array( 'render_callback' => array( __CLASS__, 'render_callback' ) )
        );
    }
    public static function module_styles( $args ): void {
        $elements = $args['elements'] ?? null;
        if ( ! is_object( $elements ) || ! method_exists( $elements, 'style' ) ) { return; }
        \ET\Builder\FrontEnd\Module\Style::add( array(
            'id' => $args['id'] ?? '', 'name' => $args['name'] ?? '',
            'orderIndex' => $args['orderIndex'] ?? 0, 'storeInstance' => $args['storeInstance'] ?? '',
            'styles' => array( $elements->style( array( 'attrName' => 'module' ) ),
                $elements->style( array( 'attrName' => 'clockTypography' ) ),
                $elements->style( array( 'attrName' => 'labelTypography' ) ) ),
        ) );
    }
    public static function module_script_data( $args ): void {
        if ( isset( $args['elements'] ) && is_object( $args['elements'] ) && method_exists( $args['elements'], 'script_data' ) ) {
            $args['elements']->script_data( array( 'attrName' => 'module' ) );
        }
    }
    public static function module_classnames( $args ): void {
        if ( ! isset( $args['classnamesInstance'] ) || ! is_object( $args['classnamesInstance'] ) ) { return; }
        $attrs = is_array( $args['attrs'] ?? null ) ? $args['attrs'] : array();
        $args['classnamesInstance']->add( \ET\Builder\Packages\Module\Options\Element\ElementClassnames::classnames( array(
            'attrs' => $attrs['module']['decoration'] ?? array(),
        ) ) );
    }
    public static function render_callback( $attrs, $content, $block, $elements ): string {
        $attrs = is_array( $attrs ) ? $attrs : array();
        wp_enqueue_style( 'alpdt-divi5-local', ALPDT_URL . 'divi5/module.css', array(), ALPDT_VERSION );
        $style = ALPDT_Divi5_Data::inline_style( $attrs );
        $html = '<div class="et_pb_module_inner"><div class="alpdt_d5_value"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>' . ALPDT_Divi5_Data::markup( $attrs ) . '</div></div>';
        $block_data = is_object( $block ) && isset( $block->parsed_block ) && is_array( $block->parsed_block ) ? $block->parsed_block : array();
        $type = is_object( $block ) && isset( $block->block_type ) && is_object( $block->block_type ) ? $block->block_type : (object) array();
        return \ET\Builder\Packages\Module\Module::render( array(
            'orderIndex' => $block_data['orderIndex'] ?? 0,
            'storeInstance' => $block_data['storeInstance'] ?? '',
            'attrs' => $attrs, 'elements' => $elements,
            'id' => $block_data['id'] ?? '',
            'moduleClassName' => ALPDT_Divi5_Data::wrapper_classes( $attrs ),
            'name' => $type->name ?? 'alpdt/dynamic-time',
            'classnamesFunction' => array( __CLASS__, 'module_classnames' ),
            'moduleCategory' => $type->category ?? 'module',
            'stylesComponent' => array( __CLASS__, 'module_styles' ),
            'scriptDataComponent' => array( __CLASS__, 'module_script_data' ),
            'children' => ( is_object( $elements ) && method_exists( $elements, 'style_components' ) ? $elements->style_components( array( 'attrName' => 'module' ) ) . $elements->style_components( array( 'attrName' => 'clockTypography' ) ) . $elements->style_components( array( 'attrName' => 'labelTypography' ) ) : '' ) . $html,
        ) );
    }
}
/**
 * Expose the saved-shortcode picker through Divi 5's supported settings channel.
 * The previous inline script could run before PackageBuildManager registered its
 * handle, leaving the only select option as the static placeholder.
 */
add_filter( 'divi_visual_builder_settings_data', static function ( $settings ) {
    if ( ! is_array( $settings ) ) { return $settings; }
    if ( current_user_can( 'edit_posts' ) ) {
        $settings['alpdtSavedShortcodeChoices'] = ALPDT_Divi5_Data::choices();
        $settings['alpdtPreviewEndpoint'] = esc_url_raw( rest_url( 'alpdt/v1/divi-preview' ) );
        $settings['alpdtPreviewNonce'] = wp_create_nonce( 'wp_rest' );
    }
    return $settings;
}, 20 );

/** Keep the server-side block definition in sync with saved items. */
add_filter( 'block_type_metadata_settings', static function ( $settings ) {
    if ( ! is_array( $settings ) || ( $settings['name'] ?? '' ) !== 'alpdt/dynamic-time' || ! current_user_can( 'edit_posts' ) ) {
        return $settings;
    }
    if ( isset( $settings['attributes']['savedKey']['settings']['innerContent']['item']['component']['props'] ) ) {
        $settings['attributes']['savedKey']['settings']['innerContent']['item']['component']['props']['options'] = array_map(
            static function ( $label ) { return array( 'label' => $label ); }, ALPDT_Divi5_Data::choices()
        );
    }
    return $settings;
}, 20 );

// Tiny module-only stylesheet is loaded before <head> closes on Divi 5 sites.
// Countdown assets remain conditional on an actually rendered saved clock.
add_action( 'wp_enqueue_scripts', static function () {
    if ( function_exists( 'et_builder_d5_enabled' ) && et_builder_d5_enabled() ) {
        wp_enqueue_style( 'alpdt-divi5-local', ALPDT_URL . 'divi5/module.css', array(), ALPDT_VERSION );
    }
} );

add_action( 'divi_visual_builder_assets_before_enqueue_scripts', static function () {
    if ( ! function_exists( 'et_core_is_fb_enabled' ) || ! et_core_is_fb_enabled() ||
         ! function_exists( 'et_builder_d5_enabled' ) || ! et_builder_d5_enabled() ||
         ! class_exists( '\\ET\\Builder\\VisualBuilder\\Assets\\PackageBuildManager' ) ) { return; }
    wp_enqueue_style( 'alpdt-countdown', ALPDT_URL . 'assets/countdown.css', array(), ALPDT_VERSION );
    wp_enqueue_style( 'alpdt-divi5-local', ALPDT_URL . 'divi5/module.css', array(), ALPDT_VERSION );
    wp_enqueue_script( 'alpdt-countdown', ALPDT_URL . 'assets/countdown.js', array(), ALPDT_VERSION, true );
    \ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build( array(
        'name' => 'alpdt-divi5-builder', 'version' => ALPDT_VERSION,
        'script' => array(
            'src' => ALPDT_URL . 'divi5/builder.js',
            'deps' => array( 'divi-module-library', 'divi-vendor-wp-hooks' ),
            'enqueue_top_window' => false, 'enqueue_app_window' => true,
            'args' => array( 'in_footer' => false ),
        ),
    ) );
} );
