<?php
defined( 'ABSPATH' ) || exit;

final class ALPDT_Shortcodes {
    public static function register(): void {
        // Sole ALP Dynamic Time & Countdowns shortcode; the short deprecated alias is removed.
        if ( ! shortcode_exists( 'alpdt_time' ) ) { add_shortcode( 'alpdt_time', array( __CLASS__, 'main' ) ); }
        if ( ! shortcode_exists( 'years-since' ) ) { add_shortcode( 'years-since', array( __CLASS__, 'legacy' ) ); }
        if ( ! shortcode_exists( 'years-since-gb' ) ) { add_shortcode( 'years-since-gb', array( __CLASS__, 'legacy_gb' ) ); }
    }

    public static function main( $atts = array() ): string {
        $atts = is_array( $atts ) ? $atts : array();
        // Named saved configurations resolve to attributes, not to nested shortcode text.
        if ( isset( $atts['saved'] ) ) {
            $resolved = ALPDT_Saved_Shortcodes::resolve( $atts['saved'] );
            return null === $resolved ? '' : self::main( $resolved );
        }
        $options = shortcode_atts( array(
            'type' => '', 'from' => '', 'to' => '', 'id' => '', 'mode' => 'anniversary',
            'unit' => 'years', 'text' => 'true', 'format' => 'j F Y', 'offset' => '0',
            'html' => '', 'live' => 'false', 'before' => '', 'on' => '', 'after' => '',
            'copyright_style' => 'range', 'expression' => 'today', 'hemisphere' => 'north', 'recurrence' => 'none', 'repeat_time' => '12:00', 'repeat_weekday' => '1', 'repeat_day' => '1', 'end_action' => 'none', 'end_message' => '', 'end_url' => '', 'countdown_display' => 'days', 'clock_style' => 'simple', 'clock_layout' => 'default', 'clock_width' => 'auto', 'clock_units' => 'days,hours,minutes,seconds', 'celebration_duration' => '5', 'celebration_mode' => 'end', 'celebration_message' => '',
            'digit_color' => '', 'label_color' => '', 'card_color' => '', 'accent_color' => '', 'track_color' => '', 'panel_color' => '',
            'clock_size' => '', 'digit_size' => '', 'label_size' => '', 'clock_radius' => '', 'clock_padding' => '', 'clock_padding_mode' => 'global', 'clock_padding_top' => '', 'clock_padding_right' => '', 'clock_padding_bottom' => '', 'clock_padding_left' => '', 'countdown_padding_top' => '', 'countdown_padding_right' => '', 'countdown_padding_bottom' => '', 'countdown_padding_left' => '', 'clock_font' => '', 'clock_weight' => '', 'clock_italic' => '', 'card_inner_spacing' => '', 'bulb_card_gap' => '', 'bulb_count' => '', 'glow_background_color' => '', 'blood_font' => '', 'lights_mode' => 'multicolor', 'christmas_1_color' => '', 'christmas_2_color' => '', 'christmas_3_color' => '', 'christmas_4_color' => '', 'christmas_5_color' => '', 'christmas_6_color' => '', 'christmas_7_color' => '', 'christmas_8_color' => '', 'christmas_9_color' => '', 'christmas_10_color' => '', 'christmas_11_color' => '', 'christmas_12_color' => '',
            'confetti_1_color' => '', 'confetti_2_color' => '', 'confetti_3_color' => '', 'confetti_4_color' => '', 'confetti_5_color' => '', 'confetti_6_color' => '', 'confetti_7_color' => '', 'confetti_8_color' => '', 'confetti_9_color' => '', 'confetti_10_color' => '',
            'balloon_1_color' => '', 'balloon_2_color' => '', 'balloon_3_color' => '', 'balloon_4_color' => '', 'balloon_5_color' => '', 'balloon_6_color' => '', 'balloon_7_color' => '', 'balloon_8_color' => '', 'balloon_9_color' => '', 'balloon_10_color' => '',
            'label_years' => '', 'label_months' => '', 'label_days' => '', 'label_hours' => '', 'label_minutes' => '', 'label_seconds' => '',
        ), $atts, 'alpdt_time' );
        if ( '' === $options['type'] ) { $options['type'] = 'elapsed'; }
        $result = ALPDT_Engine::render( $options );
        if ( '' === $result ) { return ''; }
        $escaped = esc_html( $result );
        $has_countdown_timer = 'countdown' === $options['type'] && ( in_array( $options['countdown_display'], array( 'full', 'custom' ), true ) || 'none' !== $options['recurrence'] || 'none' !== $options['end_action'] );
        if ( $has_countdown_timer ) {
            $target = 'none' !== $options['recurrence'] ? ALPDT_Schedule::next( $options ) : ALPDT_Engine::target( (string) $options['to'] );
            if ( ! $target ) { return ''; }
            wp_enqueue_script( 'alpdt-countdown', ALPDT_URL . 'assets/countdown.js', array(), ALPDT_VERSION, true );
            wp_enqueue_style( 'alpdt-countdown', ALPDT_URL . 'assets/countdown.css', array(), ALPDT_VERSION );
            if ( 'custom' === $options['countdown_display'] || ( 'full' === $options['countdown_display'] && ( ( 'horizontal' === $options['clock_layout'] && ALPDT_Clock::supports_horizontal( (string) $options['clock_style'] ) ) || ! empty( ALPDT_Clock::appearance( $options ) ) || in_array( $options['clock_style'], array( 'flip', 'ring', 'slide', 'pulse', 'sweep', 'minimalist', 'promo_dark', 'promo_light', 'promo_outline', 'celebration', 'confetti', 'blood', 'odometer', 'neon', 'liquid', 'matrix', 'orbit', 'glitch', 'morph', 'hourglass', 'snowfall', 'christmas_lights' ), true ) ) ) ) {
                $units = 'full' === $options['countdown_display'] ? 'days,hours,minutes,seconds' : (string) $options['clock_units'];
                $escaped = ALPDT_Clock::markup( $target, $units, (string) $options['clock_style'], null, $options );
            } else {
                $clock_class = 'glow' === $options['clock_style'] ? ' alpdt-countdown-glow' : '';
                $escaped = '<span class="alpdt-countdown' . $clock_class . '" role="timer" aria-live="off" data-alpdt-display="' . esc_attr( $options['countdown_display'] ) . '" data-alpdt-target="' . esc_attr( $target->getTimestamp() ) . '">' . $escaped . '</span>';
            }
            $extra = '';
            if ( 'none' !== $options['recurrence'] && in_array( $options['recurrence'], array( 'daily', 'weekly', 'monthly' ), true ) ) {
                $extra .= ' data-alpdt-recurrence="' . esc_attr( $options['recurrence'] ) . '" data-alpdt-repeat-time="' . esc_attr( $options['repeat_time'] ) . '" data-alpdt-repeat-weekday="' . esc_attr( $options['repeat_weekday'] ) . '" data-alpdt-repeat-day="' . esc_attr( $options['repeat_day'] ) . '" data-alpdt-next-url="' . esc_url( rest_url( 'alpdt/v1/next' ) ) . '"';
            } else {
                $action = in_array( $options['end_action'], array( 'none', 'message', 'hide', 'redirect' ), true ) ? $options['end_action'] : 'none';
                $path = ALPDT_Schedule::redirect_path( $options['end_url'] );
                if ( 'redirect' === $action && '' === $path ) { $action = 'none'; }
                $extra .= ' data-alpdt-end-action="' . esc_attr( $action ) . '"';
                if ( 'message' === $action ) {
                    $message = sanitize_text_field( (string) $options['end_message'] );
                    if ( '' === $message ) { $message = __( 'Time is up!', 'alp-dynamic-time-countdowns' ); }
                    $message = function_exists( 'mb_substr' ) ? mb_substr( $message, 0, 120 ) : substr( $message, 0, 120 );
                    $extra .= ' data-alpdt-end-message="' . esc_attr( $message ) . '"';
                }
                if ( 'redirect' === $action ) { $extra .= ' data-alpdt-end-url="' . esc_attr( $path ) . '"'; }
            }
            $escaped = preg_replace( '/^<span\b/', '<span' . $extra, $escaped, 1 );
        }
        $tag = in_array( $options['html'], array( 'span', 'strong', 'em', 'b', 'i', 'small', 'sup' ), true ) ? $options['html'] : '';
        if ( ( 'true' === $options['live'] || '1' === $options['live'] ) && ! ( 'smart_date' === $options['type'] && in_array( $options['expression'], array( 'post_published', 'post_modified' ), true ) ) && ! $has_countdown_timer ) {
            // Only update visible content. Crawlers receive server-rendered text as well.
            wp_enqueue_script( 'alpdt-live', ALPDT_URL . 'assets/live.js', array(), ALPDT_VERSION, true );
            $safe = array_intersect_key( $options, array_flip( array( 'type', 'from', 'to', 'id', 'mode', 'unit', 'text', 'format', 'offset', 'before', 'on', 'after', 'copyright_style', 'countdown_display', 'clock_units', 'expression', 'hemisphere' ) ) );
            $payload = esc_attr( wp_json_encode( $safe ) );
            $escaped = '<span class="alpdt-live" data-alpdt-zone="' . esc_attr( wp_timezone()->getName() ) . '" data-alpdt-endpoint="' . esc_url( rest_url( 'alpdt/v1/render' ) ) . '" data-alpdt-config="' . $payload . '">' . $escaped . '</span>';
        }
        if ( $tag ) { $escaped = '<' . $tag . '>' . $escaped . '</' . $tag . '>'; }
        return $escaped;
    }

    /** Years Since - Timeless Texts plugin 1.5.0 migration: semantically correct dates; fixes known old arithmetic bugs. */
    public static function legacy( $atts = array() ): string {
        $atts = is_array( $atts ) ? $atts : array();
        $year = isset( $atts['y'] ) && is_scalar( $atts['y'] ) ? (string) $atts['y'] : '';
        if ( ! preg_match( '/^[0-9]{4}$/D', $year ) ) { return ''; }
        $month = isset( $atts['m'] ) && is_scalar( $atts['m'] ) ? (string) $atts['m'] : '1';
        $day   = isset( $atts['d'] ) && is_scalar( $atts['d'] ) ? (string) $atts['d'] : '1';
        if ( ! ctype_digit( $month ) || ! ctype_digit( $day ) ) { return ''; }
        $date = sprintf( '%s-%02d-%02d', $year, (int) $month, (int) $day );
        if ( ! ALPDT_Engine::parse( $date ) || ALPDT_Engine::parse( $date ) > ALPDT_Engine::today() ) { return ''; }
        $text = ! isset( $atts['text'] ) || 'false' !== strtolower( (string) $atts['text'] );
        $show = isset( $atts['show'] ) ? (string) $atts['show'] : '';
        $date_obj = ALPDT_Engine::parse( $date );
        $diff = $date_obj->diff( ALPDT_Engine::today() );
        if ( ! $text ) {
            $output = (string) $diff->y;
        } elseif ( 'y' === $show || 'ym' === $show || 'ymd' === $show ) {
            $parts = array( self::unit( $diff->y, 'year' ) );
            if ( 'ym' === $show || 'ymd' === $show ) { $parts[] = self::unit( $diff->m, 'month' ); }
            if ( 'ymd' === $show ) { $parts[] = self::unit( $diff->d, 'day' ); }
            $output = implode( ' ', $parts );
        } elseif ( $diff->y >= 1 ) {
            $parts = array( self::unit( $diff->y, 'year' ) );
            if ( $diff->m ) { $parts[] = self::unit( $diff->m, 'month' ); }
            if ( $diff->d ) { $parts[] = self::unit( $diff->d, 'day' ); }
            $output = implode( ' ', $parts );
        } elseif ( $diff->m >= 1 ) {
            $output = self::unit( $diff->m, 'month' );
        } else {
            $days = (int) $diff->format( '%a' );
            $output = $days >= 7 ? self::unit( intdiv( $days, 7 ), 'week' ) : self::unit( $days, 'day' );
        }
        $tag = isset( $atts['html'] ) && is_scalar( $atts['html'] ) ? (string) $atts['html'] : '';
        $allowed = array( 'span', 'div', 'p', 'strong', 'em', 'strike', 'abbr', 'acronym', 'b', 'blockquote', 'cite', 'code', 'del', 'i', 'q', 's', 'u' );
        return in_array( $tag, $allowed, true ) ? '<' . $tag . '>' . esc_html( $output ) . '</' . $tag . '>' : esc_html( $output );
    }

    private static function unit( int $n, string $unit ): string {
        switch ( $unit ) {
            case 'year':
                /* translators: %d: number of years in the elapsed interval. */
                return sprintf( _n( '%d year', '%d years', $n, 'alp-dynamic-time-countdowns' ), $n );
            case 'month':
                /* translators: %d: number of months in the elapsed interval. */
                return sprintf( _n( '%d month', '%d months', $n, 'alp-dynamic-time-countdowns' ), $n );
            case 'week':
                /* translators: %d: number of weeks in the elapsed interval. */
                return sprintf( _n( '%d week', '%d weeks', $n, 'alp-dynamic-time-countdowns' ), $n );
            default:
                /* translators: %d: number of days in the elapsed interval. */
                return sprintf( _n( '%d day', '%d days', $n, 'alp-dynamic-time-countdowns' ), $n );
        }
    }

    public static function legacy_gb( $atts = array(), $content = null ): string {
        // Preserve normal shortcode expansion inside the legacy content wrapper.
        return is_string( $content ) ? do_shortcode( $content ) : '';
    }
}
