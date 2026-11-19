<?php
/** Date math, without side effects. */
defined( 'ABSPATH' ) || exit;

final class ALPDT_Engine {
    /** @return DateTimeImmutable */
    public static function today( ?DateTimeImmutable $override = null ): DateTimeImmutable {
        return $override ? $override->setTime( 0, 0, 0 ) : new DateTimeImmutable( 'today', wp_timezone() );
    }

    /** Reject overflow and arbitrary datetime strings, require real Gregorian calendar dates. */
    public static function parse( string $input, ?DateTimeZone $zone = null ): ?DateTimeImmutable {
        $input = trim( $input );
        $zone  = $zone ?: wp_timezone();
        if ( preg_match( '/^([0-9]{4})$/D', $input, $m ) ) {
            $input .= '-01-01';
        } elseif ( preg_match( '/^([0-9]{4})-([0-9]{2})$/D', $input, $m ) ) {
            $input .= '-01';
        }
        if ( ! preg_match( '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/D', $input, $m ) ) {
            return null;
        }
        if ( (int) $m[1] < 1 || ! checkdate( (int) $m[2], (int) $m[3], (int) $m[1] ) ) {
            return null;
        }
        $date = DateTimeImmutable::createFromFormat( '!Y-m-d', $input, $zone );
        return $date instanceof DateTimeImmutable ? $date : null;
    }

    /** Countdown target uses the site's timezone; rejects invalid dates and nonexistent DST times. */
    public static function target( string $input, ?DateTimeZone $zone = null ): ?DateTimeImmutable {
        $zone = $zone ?: wp_timezone();
        $input = trim( $input );
        if ( preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D', $input ) ) {
            return self::parse( $input, $zone );
        }
        if ( ! preg_match( '/^([0-9]{4}-[0-9]{2}-[0-9]{2})T([0-9]{2}):([0-9]{2})(?::([0-9]{2}))?$/D', $input, $parts ) ) {
            return null;
        }
        if ( ! self::parse( $parts[1], $zone ) || (int) $parts[2] > 23 || (int) $parts[3] > 59 || ( isset( $parts[4] ) && (int) $parts[4] > 59 ) ) {
            return null;
        }
        $stamp = $parts[1] . ' ' . $parts[2] . ':' . $parts[3] . ':' . ( $parts[4] ?? '00' );
        $date = DateTimeImmutable::createFromFormat( '!Y-m-d H:i:s', $stamp, $zone );
        return $date instanceof DateTimeImmutable && $date->format( 'Y-m-d H:i:s' ) === $stamp ? $date : null;
    }

    /** Format a ticking countdown with compact numeric day/hour/minute/second markers. */
    public static function countdown_clock( int $seconds ): string {
        $seconds = max( 0, $seconds );
        $days = intdiv( $seconds, 86400 );
        $hours = intdiv( $seconds % 86400, 3600 );
        $minutes = intdiv( $seconds % 3600, 60 );
        $remaining = $seconds % 60;
        return sprintf( '%dd %02dh %02dm %02ds', $days, $hours, $minutes, $remaining );
    }

    /** Format an anniversary exactly like the standalone Anniversary number shortcode. */
    public static function anniversary_number( int $years ): string {
        if ( 0 === strpos( get_locale(), 'es_' ) ) { return $years . '.º'; }
        $suffix = ( $years % 100 >= 11 && $years % 100 <= 13 ) ? 'th' : ( array( 1 => 'st', 2 => 'nd', 3 => 'rd' )[ $years % 10 ] ?? 'th' );
        return $years . $suffix;
    }

    /** @param array<string,mixed> $raw */
    public static function render( array $raw, ?DateTimeImmutable $today = null ): string {
        $now = $today ?: new DateTimeImmutable( 'now', wp_timezone() );
        $today = self::today( $today );
        $type = isset( $raw['type'] ) && is_scalar( $raw['type'] ) ? (string) $raw['type'] : 'elapsed';
        $type = in_array( $type, array( 'elapsed', 'year', 'month', 'date', 'countdown', 'since', 'copyright', 'ordinal', 'anniversary_message', 'smart_date' ), true ) ? $type : 'invalid';
        $source = isset( $raw['id'] ) && is_scalar( $raw['id'] ) ? sanitize_key( (string) $raw['id'] ) : '';
        if ( '' !== $source ) {
            $presets = get_option( 'alpdt_presets', array() );
            if ( ! is_array( $presets ) || ! isset( $presets[ $source ]['date'] ) ) {
                return '';
            }
            $raw = array_merge( $raw, array( 'from' => $presets[ $source ]['date'] ) );
            if ( ! isset( $raw['type'] ) || '' === $raw['type'] ) {
                $type = 'elapsed';
            }
        }
        $text = ! isset( $raw['text'] ) || ! in_array( strtolower( (string) $raw['text'] ), array( 'false', 'no', '0' ), true );
        $mode = isset( $raw['mode'] ) && 'calendar' === $raw['mode'] ? 'calendar' : 'anniversary';
        $unit = isset( $raw['unit'] ) && is_scalar( $raw['unit'] ) ? (string) $raw['unit'] : 'years';
        $unit = in_array( $unit, array( 'years', 'months', 'weeks', 'days' ), true ) ? $unit : 'years';
        if ( 'smart_date' === $type ) { return ALPDT_Schedule::dynamic( $raw, $now ); }
        switch ( $type ) {
            case 'year':
                $offset = isset( $raw['offset'] ) && filter_var( $raw['offset'], FILTER_VALIDATE_INT ) !== false ? (int) $raw['offset'] : 0;
                if ( abs( $offset ) > 100 ) { return ''; }
                return (string) ( (int) $today->format( 'Y' ) + $offset );
            case 'month':
                return wp_date( 'F', $today->getTimestamp(), $today->getTimezone() );
            case 'date':
                $format = isset( $raw['format'] ) && is_scalar( $raw['format'] ) ? (string) $raw['format'] : 'j F Y';
                if ( strlen( $format ) > 40 || ! preg_match( '/^[\\\\A-Za-z 0-9,.\/\-]+$/D', $format ) ) { return ''; }
                return wp_date( $format, $today->getTimestamp(), $today->getTimezone() );
            case 'copyright':
                $copyright_style = isset( $raw['copyright_style'] ) && is_scalar( $raw['copyright_style'] ) ? (string) $raw['copyright_style'] : 'range';
                if ( 'current' === $copyright_style ) { return $today->format( 'Y' ); }
                $from = isset( $raw['from'] ) && is_scalar( $raw['from'] ) ? (string) $raw['from'] : '';
                $date = self::parse( $from, $today->getTimezone() );
                if ( ! $date || $date > $today ) { return ''; }
                return $date->format( 'Y' ) === $today->format( 'Y' ) ? $today->format( 'Y' ) : $date->format( 'Y' ) . '–' . $today->format( 'Y' );
            case 'elapsed':
            case 'anniversary_message':
            case 'ordinal':
            case 'since':
            case 'countdown':
                $key = 'countdown' === $type ? 'to' : 'from';
                $from = isset( $raw[ $key ] ) && is_scalar( $raw[ $key ] ) ? (string) $raw[ $key ] : '';
                $date = 'countdown' === $type ? ( ! empty( $raw['recurrence'] ) && 'none' !== $raw['recurrence'] ? ALPDT_Schedule::next( $raw, $now ) : self::target( $from, $today->getTimezone() ) ) : self::parse( $from, $today->getTimezone() );
                if ( ! $date ) { return ''; }
                if ( 'countdown' === $type ) {
                    $clock = isset( $raw['countdown_display'] ) && 'full' === $raw['countdown_display'];
                    if ( $clock ) { return self::countdown_clock( $date->getTimestamp() - $now->getTimestamp() ); }
                    if ( isset( $raw['countdown_display'] ) && 'custom' === $raw['countdown_display'] ) {
                        $units = ALPDT_Clock::units( isset( $raw['clock_units'] ) && is_scalar( $raw['clock_units'] ) ? (string) $raw['clock_units'] : '' );
                        $parts = ALPDT_Clock::parts( $now, $date, $units );
                        $symbols = array( 'years' => 'y', 'months' => 'mo', 'days' => 'd', 'hours' => 'h', 'minutes' => 'm', 'seconds' => 's' );
                        $items = array();
                        foreach ( $parts as $unit_name => $number ) { $items[] = $number . $symbols[ $unit_name ]; }
                        return implode( ' ', $items );
                    }
                    if ( $date < $today ) { return '0'; }
                    // Preserve the date-only day count of pre-0.9.3 shortcodes.
                    if ( false !== strpos( $from, 'T' ) ) { return (string) max( 0, (int) ceil( ( $date->getTimestamp() - $now->getTimestamp() ) / 86400 ) ); }
                    return (string) (int) $today->diff( $date )->format( '%a' );
                }
                if ( $date > $today ) { return ''; }
                $diff = $date->diff( $today );
                if ( 'anniversary_message' === $type ) {
                    $year = (int) $today->format( 'Y' );
                    $month = (int) $date->format( 'm' );
                    $day = min( (int) $date->format( 'd' ), (int) $today->setDate( $year, $month, 1 )->format( 't' ) );
                    $anniversary = $today->setDate( $year, $month, $day );
                    $when = $anniversary > $today ? 'before' : ( $anniversary < $today ? 'after' : 'on' );
                    // Editor-only preview override, not accepted from saved shortcode attributes.
                    // The REST handler validates the requested phase against the three allowed values.
                    $preview_phase = $raw['_alpdt_preview_phase'] ?? '';
                    if ( is_string( $preview_phase ) && in_array( $preview_phase, array( 'before', 'on', 'after' ), true ) ) {
                        $when = $preview_phase;
                    }
                    $fallback = array(
                        'before' => __( 'Our anniversary is coming soon', 'alp-dynamic-time' ),
                        'on' => __( 'Today is our anniversary', 'alp-dynamic-time' ),
                        'after' => __( 'Thank you for celebrating with us', 'alp-dynamic-time' ),
                    );
                    $message = isset( $raw[ $when ] ) && is_scalar( $raw[ $when ] ) && '' !== $raw[ $when ] ? (string) $raw[ $when ] : $fallback[ $when ];
                    if ( strlen( $message ) > 240 ) { return ''; }
                    // Historical {years} is the anniversary ordinal; accept the intuitive
                    // {year} alias too. {current_year} is the four-digit calendar year.
                    return strtr( $message, array(
                        '{number}'       => self::anniversary_number( $year - (int) $date->format( 'Y' ) ),
                        '{years}'        => (string) ( $year - (int) $date->format( 'Y' ) ),
                        '{year}'         => (string) ( $year - (int) $date->format( 'Y' ) ),
                        '{current_year}' => (string) $year,
                    ) );
                }
                if ( 'ordinal' === $type ) {
                    $years = 'calendar' === $mode ? (int) $today->format( 'Y' ) - (int) $date->format( 'Y' ) : $diff->y;
                    return self::anniversary_number( $years );
                }
                if ( isset( $raw['format'] ) && 'ymd' === $raw['format'] && $text ) {
                    return implode( ' ', array(
                        /* translators: %d: elapsed number of years. */
                        sprintf( _n( '%d year', '%d years', $diff->y, 'alp-dynamic-time' ), $diff->y ),
                        /* translators: %d: elapsed number of months. */
                        sprintf( _n( '%d month', '%d months', $diff->m, 'alp-dynamic-time' ), $diff->m ),
                        /* translators: %d: elapsed number of days. */
                        sprintf( _n( '%d day', '%d days', $diff->d, 'alp-dynamic-time' ), $diff->d ),
                    ) );
                }
                if ( 'calendar' === $mode && 'years' === $unit ) {
                    $n = (int) $today->format( 'Y' ) - (int) $date->format( 'Y' );
                } else {
                    switch ( $unit ) {
                        case 'months': $n = $diff->y * 12 + $diff->m; break;
                        case 'weeks': $n = intdiv( (int) $diff->format( '%a' ), 7 ); break;
                        case 'days': $n = (int) $diff->format( '%a' ); break;
                        default: $n = $diff->y;
                    }
                }
                if ( ! $text ) { return (string) $n; }
                switch ( $unit ) {
                    case 'months':
                        /* translators: %d: elapsed number of months. */
                        return sprintf( _n( '%d month', '%d months', $n, 'alp-dynamic-time' ), $n );
                    case 'weeks':
                        /* translators: %d: elapsed number of weeks. */
                        return sprintf( _n( '%d week', '%d weeks', $n, 'alp-dynamic-time' ), $n );
                    case 'days':
                        /* translators: %d: elapsed number of days. */
                        return sprintf( _n( '%d day', '%d days', $n, 'alp-dynamic-time' ), $n );
                    default:
                        /* translators: %d: elapsed number of years. */
                        return sprintf( _n( '%d year', '%d years', $n, 'alp-dynamic-time' ), $n );
                }
        }
        return '';
    }
}
