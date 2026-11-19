<?php
/** Saved, named ALP Dynamic Time & Countdowns shortcode configurations. */
defined( 'ABSPATH' ) || exit;

final class ALPDT_Saved_Shortcodes {
    private const OPTION = 'alpdt_saved_shortcodes';
    public const MAX = 100;

    /** Raw stored records, including old invalid shortcode aliases. Never silently delete them. */
    private static function stored(): array {
        $items = get_option( self::OPTION, array() );
        return is_array( $items ) ? $items : array();
    }

    /** Only usable saved configurations should be listed, counted or offered to Divi. */
    public static function all(): array {
        $valid = array();
        foreach ( self::stored() as $slug => $entry ) {
            if ( ! is_string( $slug ) || null === self::parse_record( $slug, $entry ) ) {
                continue;
            }
            $valid[ $slug ] = $entry;
        }
        return $valid;
    }

    /** Strictly accept one ALP shortcode, with whitelisted double-quoted attributes. */
    public static function parse( $code ): ?array {
        if ( ! is_string( $code ) || strlen( $code ) > 2400 || ! preg_match( '/^\[alpdt_time((?:\s+[a-z][a-z0-9_]*="[^"\[\]\r\n]*")*)\]$/D', trim( $code ), $match ) ) {
            return null;
        }
        $allowed = array( 'type', 'from', 'to', 'id', 'mode', 'unit', 'text', 'format', 'offset', 'html', 'live', 'before', 'on', 'after', 'copyright_style', 'expression', 'hemisphere', 'recurrence', 'repeat_time', 'repeat_weekday', 'repeat_day', 'end_action', 'end_message', 'end_url', 'countdown_display', 'clock_style', 'clock_layout', 'clock_width', 'clock_units', 'celebration_duration', 'celebration_mode', 'celebration_message', 'digit_color', 'label_color', 'card_color', 'accent_color', 'track_color', 'panel_color', 'clock_size', 'digit_size', 'label_size', 'clock_radius', 'clock_padding', 'clock_padding_mode', 'clock_padding_top', 'clock_padding_right', 'clock_padding_bottom', 'clock_padding_left', 'countdown_padding_top', 'countdown_padding_right', 'countdown_padding_bottom', 'countdown_padding_left', 'clock_font', 'clock_weight', 'clock_italic', 'card_inner_spacing', 'bulb_card_gap', 'bulb_count', 'glow_background_color', 'blood_font', 'lights_mode', 'christmas_1_color', 'christmas_2_color', 'christmas_3_color', 'christmas_4_color', 'christmas_5_color', 'christmas_6_color', 'christmas_7_color', 'christmas_8_color', 'christmas_9_color', 'christmas_10_color', 'christmas_11_color', 'christmas_12_color', 'label_years', 'label_months', 'label_days', 'label_hours', 'label_minutes', 'label_seconds', 'confetti_1_color', 'confetti_2_color', 'confetti_3_color', 'confetti_4_color', 'confetti_5_color', 'confetti_6_color', 'confetti_7_color', 'confetti_8_color', 'confetti_9_color', 'confetti_10_color', 'balloon_1_color', 'balloon_2_color', 'balloon_3_color', 'balloon_4_color', 'balloon_5_color', 'balloon_6_color', 'balloon_7_color', 'balloon_8_color', 'balloon_9_color', 'balloon_10_color' );
        $attrs = array();
        preg_match_all( '/\s+([a-z][a-z0-9_]*)="([^"\[\]\r\n]*)"/', $match[1], $found, PREG_SET_ORDER );
        foreach ( $found as $item ) {
            $key = $item[1];
            if ( ! in_array( $key, $allowed, true ) || isset( $attrs[ $key ] ) || strlen( $item[2] ) > 250 ) { return null; }
            $attrs[ $key ] = $item[2];
        }
        if ( ! isset( $attrs['type'] ) || ! in_array( $attrs['type'], array( 'elapsed', 'year', 'month', 'date', 'countdown', 'copyright', 'ordinal', 'anniversary_message', 'smart_date' ), true ) ) { return null; }
        if ( in_array( $attrs['type'], array( 'elapsed', 'ordinal', 'anniversary_message' ), true ) && empty( $attrs['from'] ) && empty( $attrs['id'] ) ) { return null; }
        if ( 'countdown' === $attrs['type'] && ( ( empty( $attrs['recurrence'] ) || 'none' === $attrs['recurrence'] ) ? ( empty( $attrs['to'] ) || ! ALPDT_Engine::target( $attrs['to'] ) ) : ! ALPDT_Schedule::next( $attrs ) ) ) { return null; }
        if ( 'copyright' === $attrs['type'] && ( $attrs['copyright_style'] ?? 'range' ) !== 'current' && empty( $attrs['from'] ) && empty( $attrs['id'] ) ) { return null; }
        if ( ! empty( $attrs['from'] ) && ! ALPDT_Engine::parse( $attrs['from'] ) ) { return null; }
        if ( isset( $attrs['clock_width'] ) && ! in_array( $attrs['clock_width'], array( 'auto', 'full' ), true ) ) { return null; }
        if ( isset( $attrs['clock_layout'] ) && ! in_array( $attrs['clock_layout'], array( 'default', 'horizontal' ), true ) ) { return null; }
        if ( isset( $attrs['blood_font'] ) && ! in_array( $attrs['blood_font'], array( 'creepster', 'nosifer' ), true ) ) { return null; }
        if ( isset( $attrs['clock_weight'] ) && ! in_array( $attrs['clock_weight'], array( '300','400','500','600','700','800','900' ), true ) ) { return null; }
        if ( isset( $attrs['clock_italic'] ) && ! in_array( $attrs['clock_italic'], array( 'normal','italic' ), true ) ) { return null; }
        foreach ( array( 'digit_size', 'label_size' ) as $font_key ) {
            if ( isset( $attrs[ $font_key ] ) && null === ALPDT_Clock::font_size( $attrs[ $font_key ], $font_key ) ) { return null; }
        }
        if ( isset( $attrs['bulb_card_gap'] ) && ! preg_match( '/^(?:[0-9]|[1-6][0-9]|70)$/D', $attrs['bulb_card_gap'] ) ) { return null; }
        if ( isset( $attrs['bulb_count'] ) && ( ! preg_match( '/^[0-9]{1,2}$/D', $attrs['bulb_count'] ) || (int) $attrs['bulb_count'] < 8 || (int) $attrs['bulb_count'] > 60 ) ) { return null; }
        if ( isset( $attrs['card_inner_spacing'] ) && ! preg_match( '/^(?:[0-9]|[12][0-9]|30)$/D', $attrs['card_inner_spacing'] ) ) { return null; }
        if ( isset( $attrs['lights_mode'] ) && ! in_array( $attrs['lights_mode'], array( 'multicolor', 'warm' ), true ) ) { return null; }
        if ( isset( $attrs['clock_padding_mode'] ) && ! in_array( $attrs['clock_padding_mode'], array( 'global', 'individual' ), true ) ) { return null; }
        if ( isset( $attrs['clock_radius'] ) && ! preg_match( '/^(?:[0-9]|[1-7][0-9]|80)$/D', $attrs['clock_radius'] ) ) { return null; }
        foreach ( array( 'clock_padding', 'clock_padding_top', 'clock_padding_right', 'clock_padding_bottom', 'clock_padding_left', 'countdown_padding_top', 'countdown_padding_right', 'countdown_padding_bottom', 'countdown_padding_left' ) as $padding_key ) {
            if ( isset( $attrs[ $padding_key ] ) && ! preg_match( '/^(?:[0-9]|[1-7][0-9]|80)$/D', $attrs[ $padding_key ] ) ) { return null; }
        }
        if ( isset( $attrs['celebration_duration'] ) && ! in_array( $attrs['celebration_duration'], array( '3', '5', '8', '10' ), true ) ) { return null; }
        if ( isset( $attrs['celebration_mode'] ) && ! in_array( $attrs['celebration_mode'], array( 'end', 'loop' ), true ) ) { return null; }
        if ( 'smart_date' === $attrs['type'] && ! in_array( $attrs['expression'] ?? 'today', ALPDT_Schedule::expressions(), true ) ) { return null; }
        if ( 'smart_date' === $attrs['type'] && 'date_difference' === ( $attrs['expression'] ?? '' ) && ( ! ALPDT_Engine::parse( $attrs['from'] ?? '' ) || ! ALPDT_Engine::parse( $attrs['to'] ?? '' ) ) ) { return null; }
        if ( isset( $attrs['recurrence'] ) && ! in_array( $attrs['recurrence'], array( 'none', 'daily', 'weekly', 'monthly' ), true ) ) { return null; }
        if ( isset( $attrs['end_action'] ) && ! in_array( $attrs['end_action'], array( 'none', 'message', 'hide', 'redirect' ), true ) ) { return null; }
        if ( isset( $attrs['end_url'] ) && '' !== $attrs['end_url'] && ! ALPDT_Schedule::redirect_path( $attrs['end_url'] ) ) { return null; }
        if ( isset( $attrs['hemisphere'] ) && ! in_array( $attrs['hemisphere'], array( 'north', 'south' ), true ) ) { return null; }
        foreach ( $attrs as $key => $value ) {
            if ( str_contains( $value, '<' ) || str_contains( $value, '>' ) || str_contains( $value, chr( 92 ) ) ) { return null; }
            if ( str_ends_with( $key, '_color' ) && 'transparent' !== $value && ! preg_match( '/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/D', $value ) ) { return null; }
        }
        return $attrs;
    }

    public static function save( string $slug, string $name, string $code, string $original = '' ): bool {
        if ( ! self::valid_slug( $slug ) || '' === trim( $name ) || strlen( $name ) > 120 || null === self::parse( $code ) ) { return false; }
        $items = self::stored();
        $valid = self::all();
        if ( '' === $original ) {
            if ( isset( $valid[ $slug ] ) || count( $valid ) >= self::MAX ) { return false; }
        } elseif ( $original !== $slug || ! isset( $valid[ $slug ] ) ) { return false; }
        // An explicitly re-saved slug may replace an unusable old record.
        // All other legacy records remain untouched in the option.
        $items[ $slug ] = array( 'name' => $name, 'code' => trim( $code ) );
        update_option( self::OPTION, $items, false );
        return true;
    }

    public static function delete( string $slug ): bool {
        $items = self::stored();
        if ( ! isset( self::all()[ $slug ] ) ) { return false; }
        unset( $items[ $slug ] );
        update_option( self::OPTION, $items, false );
        return true;
    }

    public static function resolve( $slug ): ?array {
        if ( ! is_string( $slug ) ) { return null; }
        $items = self::stored();
        return self::parse_record( $slug, $items[ $slug ] ?? null );
    }

    /** Validate a stored record before exposing it through a shortcode or selector. */
    private static function parse_record( string $slug, $entry ): ?array {
        if ( ! self::valid_slug( $slug ) || ! is_array( $entry ) ||
             ! isset( $entry['code'], $entry['name'] ) || ! is_string( $entry['name'] ) ||
             '' === trim( $entry['name'] ) ) {
            return null;
        }
        return self::parse( $entry['code'] );
    }

    private static function valid_slug( string $slug ): bool {
        // PHP stores canonical integer keys as integers, which all() skips.
        return (string) (int) $slug !== $slug && 1 === preg_match( '/^[a-z0-9][a-z0-9_-]{0,54}$/D', $slug );
    }
}
