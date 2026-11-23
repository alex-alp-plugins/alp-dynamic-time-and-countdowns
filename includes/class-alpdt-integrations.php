<?php
defined( 'ABSPATH' ) || exit;

/** Scoped text-only replacements and native variable registration for supported SEO plugins. */
final class ALPDT_Integrations {
    public static function register(): void {
        foreach ( array(
            'wpseo_title', 'wpseo_metadesc', 'wpseo_opengraph_title', 'wpseo_opengraph_desc',
            'wpseo_twitter_title', 'wpseo_twitter_description',
            'rank_math/frontend/title', 'rank_math/frontend/description',
            'seopress_titles_title', 'seopress_titles_desc',
        ) as $hook ) {
            add_filter( $hook, array( __CLASS__, 'replace' ), 20, 1 );
        }
        add_filter( 'widget_text', array( __CLASS__, 'replace' ), 20 );
        add_filter( 'widget_title', array( __CLASS__, 'replace' ), 20 );
        add_filter( 'alpdt_dynamic_text', array( __CLASS__, 'replace' ) );
        // Official plugin-specific variable registration hooks. No global editor DOM modifications.
        add_action( 'rank_math/vars/register_extra_replacements', array( __CLASS__, 'register_rank_math' ) );
        add_action( 'wpseo_register_extra_replacements', array( __CLASS__, 'register_yoast' ) );
    }

    /** Stable collision-resistant name for a saved text shortcode. */
    public static function saved_variable_name( string $slug ): string {
        return 'alpdt_saved_' . str_replace( '-', '_', $slug ) . '_' . substr( hash( 'sha256', $slug ), 0, 8 );
    }

    /** @return array<string,array{label:string,callback:callable}> */
    private static function seo_variables(): array {
        $vars = array(
            'alpdt_year' => array(
                'label' => __( 'ALP Dynamic Time & Countdowns: current year', 'alp-dynamic-time-countdowns' ),
                'callback' => static function (): string {
                    return sanitize_text_field( ALPDT_Engine::render( array( 'type' => 'year' ) ) );
                },
            ),
            'alpdt_month' => array(
                'label' => __( 'ALP Dynamic Time & Countdowns: current month', 'alp-dynamic-time-countdowns' ),
                'callback' => static function (): string {
                    return sanitize_text_field( ALPDT_Engine::render( array( 'type' => 'month' ) ) );
                },
            ),
        );
        $presets = get_option( 'alpdt_presets', array() );
        if ( ! is_array( $presets ) ) { $presets = array(); }
        $n = 0;
        foreach ( $presets as $slug => $preset ) {
            if ( ++$n > 100 ) { break; }
            if ( ! is_string( $slug ) || ! preg_match( '/^[a-z0-9_-]{1,55}$/D', $slug ) || ! is_array( $preset ) || empty( $preset['date'] ) ) { continue; }
            $name = 'alpdt_since_' . str_replace( '-', '_', $slug );
            if ( isset( $vars[ $name ] ) ) { continue; } // Prevent collisions on normalized identifiers.
            $vars[ $name ] = array(
                'label' => sprintf( /* translators: %s: identifier of saved date. */ __( 'ALP Dynamic Time & Countdowns: years since %s', 'alp-dynamic-time-countdowns' ), $slug ),
                'callback' => static function () use ( $slug ): string {
                    return sanitize_text_field( ALPDT_Engine::render( array( 'type' => 'elapsed', 'id' => $slug, 'mode' => 'calendar', 'text' => 'false' ) ) );
                },
            );
        }
        $saved = ALPDT_Saved_Shortcodes::all();
        $n = 0;
        foreach ( $saved as $slug => $entry ) {
            if ( ++$n > ALPDT_Saved_Shortcodes::MAX ) { break; }
            if ( ! is_string( $slug ) || ! preg_match( '/^[a-z0-9][a-z0-9_-]{0,54}$/D', $slug ) ) { continue; }
            $attrs = ALPDT_Saved_Shortcodes::resolve( $slug );
            if ( ! $attrs || 'countdown' === ( $attrs['type'] ?? '' ) ) { continue; }
            $name = self::saved_variable_name( $slug );
            $vars[ $name ] = array(
                'label' => sprintf( /* translators: %s: saved shortcode name or identifier. */ __( 'ALP Dynamic Time & Countdowns: saved text %s', 'alp-dynamic-time-countdowns' ), isset( $entry['name'] ) && is_string( $entry['name'] ) ? $entry['name'] : $slug ),
                'callback' => static function () use ( $slug ): string {
                    $config = ALPDT_Saved_Shortcodes::resolve( $slug );
                    if ( ! $config || 'countdown' === ( $config['type'] ?? '' ) ) { return ''; }
                    return sanitize_text_field( wp_strip_all_tags( ALPDT_Engine::render( $config ) ) );
                },
            );
        }
        return $vars;
    }

    public static function register_rank_math(): void {
        if ( ! function_exists( 'rank_math_register_var_replacement' ) ) { return; }
        foreach ( self::seo_variables() as $name => $config ) {
            rank_math_register_var_replacement( $name, array(
                'name' => $config['label'],
                'description' => __( 'Dynamic text calculated by ALP Dynamic Time & Countdowns.', 'alp-dynamic-time-countdowns' ),
                'variable' => $name,
                // Rank Math uses 'example' in the snippet editor, not necessarily the current frontend value.
                'example' => call_user_func( $config['callback'] ),
            ), $config['callback'] );
        }
    }

    public static function register_yoast(): void {
        if ( ! function_exists( 'wpseo_register_var_replacement' ) ) { return; }
        foreach ( self::seo_variables() as $name => $config ) {
            wpseo_register_var_replacement( '%%' . $name . '%%', $config['callback'], 'advanced', $config['label'] );
        }
    }

    public static function replace( $value ) {
        if ( ! is_string( $value ) || strlen( $value ) > 20000 || false === strpos( $value, '[alpdt_time' ) ) { return $value; }
        return preg_replace_callback( '/\[alpdt_time((?:\s+[^\[\]]{0,600})?)\]/i', static function ( array $match ) {
            $body = $match[1];
            $attributes = shortcode_parse_atts( $body );
            if ( ! is_array( $attributes ) ) { $attributes = array(); }
            if ( isset( $attributes['saved'] ) ) {
                $attributes = ALPDT_Saved_Shortcodes::resolve( $attributes['saved'] );
                if ( null === $attributes ) { return ''; }
            }
            // Only the inserted replacement is ours to escape. Keep the
            // original widget/SEO HTML intact rather than sanitizing its wrapper.
            return esc_html( sanitize_text_field( ALPDT_Engine::render( $attributes ) ) );
        }, $value );
    }
}
