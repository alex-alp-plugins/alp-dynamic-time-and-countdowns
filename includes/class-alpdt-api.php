<?php
defined( 'ABSPATH' ) || exit;

final class ALPDT_API {
    public static function register(): void {
        if ( class_exists( 'ALPDT_Divi5_Module', false ) && et_builder_d5_enabled() ) {
            register_rest_route( 'alpdt/v1', '/divi-preview', array(
                'methods' => WP_REST_Server::READABLE,
                'permission_callback' => static function () { return current_user_can( 'edit_posts' ); },
                'callback' => array( __CLASS__, 'divi_preview' ),
                'args' => array( 'savedKey' => array( 'sanitize_callback' => 'sanitize_key' ) ),
            ) );
        }
        register_rest_route( 'alpdt/v1', '/next', array(
            'methods' => WP_REST_Server::READABLE,
            'permission_callback' => '__return_true',
            'callback' => array( __CLASS__, 'next_occurrence' ),
            'args' => array(
                'recurrence' => array( 'sanitize_callback' => 'sanitize_key' ),
                'repeat_time' => array( 'sanitize_callback' => 'sanitize_text_field' ),
                'repeat_weekday' => array( 'sanitize_callback' => 'absint' ),
                'repeat_day' => array( 'sanitize_callback' => 'absint' ),
            ),
        ) );
        register_rest_route( 'alpdt/v1', '/render', array(
            'methods' => WP_REST_Server::READABLE,
            'permission_callback' => '__return_true',
            'callback' => array( __CLASS__, 'render' ),
            'args' => array(
                'type' => array( 'sanitize_callback' => 'sanitize_key' ),
                'id' => array( 'sanitize_callback' => 'sanitize_key' ),
                'from' => array( 'sanitize_callback' => 'sanitize_text_field' ),
                'to' => array( 'sanitize_callback' => 'sanitize_text_field' ),
                'mode' => array( 'sanitize_callback' => 'sanitize_key' ),
                'unit' => array( 'sanitize_callback' => 'sanitize_key' ),
                'format' => array( 'sanitize_callback' => 'sanitize_text_field' ),
                'text' => array( 'sanitize_callback' => 'sanitize_text_field' ),
                'offset' => array( 'sanitize_callback' => 'sanitize_text_field' ),
                'before' => array( 'sanitize_callback' => 'sanitize_text_field' ),
                'on' => array( 'sanitize_callback' => 'sanitize_text_field' ),
                'after' => array( 'sanitize_callback' => 'sanitize_text_field' ),
                'countdown_display' => array( 'sanitize_callback' => 'sanitize_key' ),
                'clock_units' => array( 'sanitize_callback' => 'sanitize_text_field' ),
                'copyright_style' => array( 'sanitize_callback' => 'sanitize_key' ),
                'expression' => array( 'sanitize_callback' => 'sanitize_key' ),
                'hemisphere' => array( 'sanitize_callback' => 'sanitize_key' ),
                'recurrence' => array( 'sanitize_callback' => 'sanitize_key' ),
                'repeat_time' => array( 'sanitize_callback' => 'sanitize_text_field' ),
                'repeat_weekday' => array( 'sanitize_callback' => 'absint' ),
                'repeat_day' => array( 'sanitize_callback' => 'absint' ),
            ),
        ) );
    }

    public static function divi_preview( WP_REST_Request $request ): WP_REST_Response {
        $fields = array( 'savedKey','localNumberColor','localLabelColor','localAccentColor','localFontSize','localFontFamily','localAlignment','localLabelSize','sizingAlignment' );
        $attrs = array();
        foreach ( $fields as $field ) {
            $value = $request->get_param( $field );
            if ( is_string( $value ) && strlen( $value ) <= ( in_array( $field, array( 'localNumberColor', 'localLabelColor', 'localAccentColor' ), true ) ? 768 : 100 ) ) {
                $attrs[ $field ] = array( 'innerContent' => array( 'desktop' => array( 'value' => sanitize_text_field( $value ) ) ) );
            }
        }
        $result = array( 'html' => ALPDT_Divi5_Data::markup( $attrs ),
                         'classes' => ALPDT_Divi5_Data::wrapper_classes( $attrs ),
                         'style' => ALPDT_Divi5_Data::inline_style( $attrs ) );
        $response = new WP_REST_Response( $result, 200 );
        $response->header( 'Cache-Control', 'private, no-store, max-age=0' );
        return $response;
    }

    /** Public and read-only: no stored data, user identifiers or secrets are returned. */
    public static function next_occurrence( WP_REST_Request $request ): WP_REST_Response {
        $raw = array();
        foreach ( array( 'recurrence', 'repeat_time', 'repeat_weekday', 'repeat_day' ) as $key ) {
            $value = $request->get_param( $key );
            if ( is_scalar( $value ) && strlen( (string) $value ) <= 32 ) { $raw[ $key ] = (string) $value; }
        }
        $next = ALPDT_Schedule::next( $raw );
        $response = new WP_REST_Response( array( 'target' => $next ? $next->getTimestamp() : 0 ), $next ? 200 : 400 );
        $response->header( 'Cache-Control', 'no-store, max-age=0' );
        return $response;
    }

    public static function render( WP_REST_Request $request ): WP_REST_Response {
        $fields = array( 'type', 'id', 'from', 'to', 'mode', 'unit', 'format', 'text', 'offset', 'before', 'on', 'after', 'countdown_display', 'copyright_style', 'clock_units', 'expression', 'hemisphere', 'recurrence', 'repeat_time', 'repeat_weekday', 'repeat_day' );
        $attrs  = array();
        foreach ( $fields as $field ) {
            $value = $request->get_param( $field );
            if ( is_scalar( $value ) && strlen( (string) $value ) <= ( in_array( $field, array( 'before', 'on', 'after' ), true ) ? 240 : 80 ) ) {
                $attrs[ $field ] = (string) $value;
            }
        }
        // The selected field is preview-only. Never persist it as an ordinary shortcode attribute.
        $preview_phase = $request->get_param( 'preview_message' );
        if ( 'anniversary_message' === ( $attrs['type'] ?? '' ) && is_string( $preview_phase ) && in_array( $preview_phase, array( 'before', 'on', 'after' ), true ) ) {
            $attrs['_alpdt_preview_phase'] = $preview_phase;
        }
        $result = array( 'value' => ALPDT_Engine::render( $attrs ) );
        // Read-only editor example. An actual shortcode always resolves its post context;
        // this synthetic date is never stored or passed into the shortcode renderer.
        if ( ( $attrs['type'] ?? '' ) === 'smart_date' && in_array( $attrs['expression'] ?? '', array( 'post_published', 'post_modified' ), true ) ) {
            $sample = $request->get_param( 'preview_date' );
            $date   = is_string( $sample ) && strlen( $sample ) <= 10 ? ALPDT_Engine::parse( $sample ) : null;
            $format = $attrs['format'] ?? 'j F Y';
            if ( $date && strlen( $format ) <= 40 && preg_match( '/^[A-Za-z 0-9,.\/\-]+$/D', $format ) ) {
                $result['value'] = wp_date( $format, $date->getTimestamp(), wp_timezone() );
            }
        }
        if ( ( $attrs['type'] ?? '' ) === 'countdown' && in_array( $attrs['countdown_display'] ?? '', array( 'full', 'custom' ), true ) ) {
            $target = ! empty( $attrs['recurrence'] ) && 'none' !== $attrs['recurrence'] ? ALPDT_Schedule::next( $attrs ) : ALPDT_Engine::target( $attrs['to'] ?? '' );
            if ( $target ) { $result['target'] = $target->getTimestamp(); }
        }
        $response = new WP_REST_Response( $result, 200 );
        $response->header( 'Cache-Control', 'no-store, max-age=0' );
        return $response;
    }
}
