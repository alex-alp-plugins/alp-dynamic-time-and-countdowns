<?php
defined( 'ABSPATH' ) || exit;

final class ALPDT_Block {
    public static function register(): void {
        wp_register_script( 'alpdt-block-editor', ALPDT_URL . 'blocks/editor.js', array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ), ALPDT_VERSION, true );
        wp_add_inline_script( 'alpdt-block-editor', 'window.alpdtBlockIconUrl=' . wp_json_encode( ALPDT_URL . 'assets/branding/isotype-color.svg' ) . ';', 'before' );

        // The selector uses the same validated library as the Divi 5 module.
        // Only labels and identifiers go to the editor; no shortcode code or private settings.
        $choices = array();
        if ( current_user_can( 'edit_posts' ) ) {
            foreach ( ALPDT_Saved_Shortcodes::all() as $slug => $entry ) {
                $name = sanitize_text_field( (string) $entry['name'] );
                $choices[] = array(
                    'value' => $slug,
                    'label' => '' !== $name ? $name : $slug,
                );
            }
        }
        $editor_data = array(
            'savedShortcodes' => $choices,
            'manageUrl' => current_user_can( 'manage_options' ) ? admin_url( 'admin.php?page=alpdt-time&alpdt_view=shortcodes' ) : '',
        );
        wp_add_inline_script( 'alpdt-block-editor', 'window.alpdtBlockData=' . wp_json_encode( $editor_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );

        register_block_type( 'alpdt/time', array(
            'api_version' => 2,
            'editor_script' => 'alpdt-block-editor',
            'render_callback' => array( __CLASS__, 'render' ),
            'attributes' => array(
                'saved' => array( 'type' => 'string', 'default' => '' ),
            ),
        ) );
        // Block Bindings can read a named preset into a compatible block's source attribute.
        if ( function_exists( 'register_block_bindings_source' ) ) {
            register_block_bindings_source( 'alpdt/date', array(
                'label' => __( 'ALP Dynamic Time & Countdowns', 'alp-dynamic-time' ),
                'get_value_callback' => static function ( $args ) {
                    return is_array( $args ) ? ALPDT_Engine::render( $args ) : '';
                },
            ) );
        }
    }

    public static function render( array $attrs ): string {
        // Public 1.0 block: only a named saved shortcode, same source as Divi 5.
        $saved = $attrs['saved'] ?? '';
        if ( ! is_string( $saved ) || '' === $saved || null === ALPDT_Saved_Shortcodes::resolve( $saved ) ) {
            return ''; // No selection or deleted/invalid entry: never substitute another value.
        }
        $output = ALPDT_Shortcodes::main( array( 'saved' => $saved ) );
        return '' === $output ? '' : '<div class="alpdt-block">' . $output . '</div>';
    }
}
