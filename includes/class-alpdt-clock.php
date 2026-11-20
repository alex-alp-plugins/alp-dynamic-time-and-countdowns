<?php
/** Optional segmented, animated countdown. Original shortcode display modes are preserved. */
defined( 'ABSPATH' ) || exit;

final class ALPDT_Clock {
    private const UNITS = array( 'years', 'months', 'days', 'hours', 'minutes', 'seconds' );
    private const STYLES = array( 'simple', 'glow', 'flip', 'slide', 'pulse', 'sweep', 'ring', 'minimalist', 'promo_dark', 'promo_light', 'promo_outline', 'celebration', 'confetti', 'blood', 'odometer', 'neon', 'liquid', 'matrix', 'orbit', 'glitch', 'morph', 'hourglass', 'snowfall', 'christmas_lights' );

    /** Layout is independent of appearance: never replace the selected animation. */
    public static function supports_horizontal( string $style ): bool {
        return in_array( $style, array( 'simple', 'glow', 'flip', 'slide', 'pulse', 'sweep', 'ring', 'odometer', 'neon', 'liquid', 'matrix', 'glitch', 'morph', 'celebration', 'confetti', 'snowfall', 'christmas_lights' ), true );
    }

    /** @return list<string> */
    public static function units( string $raw ): array {
        $selected = array_unique( array_map( 'trim', explode( ',', strtolower( $raw ) ) ) );
        return array_values( array_filter( self::UNITS, static function ( string $unit ) use ( $selected ): bool {
            return in_array( $unit, $selected, true );
        } ) );
    }

    public static function style( string $style ): string {
        return in_array( $style, self::STYLES, true ) ? $style : 'simple';
    }

    /** Strict CSS font length allowlist. Legacy unitless values remain supported as px. */
    public static function font_size( mixed $raw, string $key ): ?string {
        if ( ! is_scalar( $raw ) || ! in_array( $key, array( 'digit_size', 'label_size' ), true ) ) { return null; }
        $value = strtolower( trim( (string) $raw ) );
        if ( '' === $value || strlen( $value ) > 96 ) { return null; }
        $min_px = 'digit_size' === $key ? 18 : 6;
        $max_px = 'digit_size' === $key ? 110 : 200;
        if ( preg_match( '/^[0-9]{1,3}(?:\.[0-9]{1,3})?$/D', $value ) ) {
            $number = (float) $value;
            return $number >= $min_px && $number <= $max_px ? $value . 'px' : null;
        }
        $length = static function ( string $part, bool $clamp ) use ( $min_px, $max_px ): ?string {
            $part = trim( $part );
            if ( ! preg_match( '/^([0-9]{1,4}(?:\.[0-9]{1,3})?)(px|rem|em|%|vw|vh)$/D', $part, $matches ) ) { return null; }
            $number = (float) $matches[1];
            $unit = $matches[2];
            $ceiling = match ( $unit ) { 'px' => $clamp ? 300 : $max_px, 'rem', 'em' => 25, '%' => 1200, default => 100 };
            $floor = 'px' === $unit && ! $clamp ? $min_px : ( $clamp ? 0 : 0.01 );
            return $number >= $floor && $number <= $ceiling ? $part : null;
        };
        if ( str_starts_with( $value, 'clamp(' ) && str_ends_with( $value, ')' ) ) {
            $parts = explode( ',', substr( $value, 6, -1 ) );
            if ( 3 !== count( $parts ) ) { return null; }
            $validated = array();
            foreach ( $parts as $part ) {
                $safe = $length( $part, true );
                if ( null === $safe ) { return null; }
                $validated[] = $safe;
            }
            return 'clamp(' . implode( ', ', $validated ) . ')';
        }
        return $length( $value, false );
    }

    /** Safe presentation options shared by shortcode, wp-admin and Divi. No arbitrary CSS is accepted. */
    public static function appearance( array $input ): array {
        $result = array();
        $colors = array( 'digit_color' => '--alpdt-digit-color', 'label_color' => '--alpdt-label-color',
            'card_color' => '--alpdt-card-color', 'accent_color' => '--alpdt-accent-color',
            'track_color' => '--alpdt-track-color', 'panel_color' => '--alpdt-flip-panel-bg',
            'glow_background_color' => '--alpdt-glow-bg' );
        if ( isset( $input['panel_color'] ) && 'transparent' === $input['panel_color'] ) { $result['--alpdt-flip-panel-bg'] = 'transparent'; }
        foreach ( $colors as $key => $prop ) {
            $value = isset( $input[ $key ] ) && is_scalar( $input[ $key ] ) ? trim( (string) $input[ $key ] ) : '';
            if ( preg_match( '/^#[0-9a-fA-F]{6}(?:[0-9a-fA-F]{2})?$/D', $value ) ) { $result[ $prop ] = strtolower( $value ); }
        }
        foreach ( range( 1, 12 ) as $index ) {
            $key = 'christmas_' . $index . '_color';
            $prop = '--alpdt-light-' . $index . '-color';
            $value = isset( $input[ $key ] ) && is_scalar( $input[ $key ] ) ? trim( (string) $input[ $key ] ) : '';
            if ( preg_match( '/^#[0-9a-fA-F]{6}(?:[0-9a-fA-F]{2})?$/D', $value ) ) { $result[ $prop ] = strtolower( $value ); }
        }
        $diameter = isset( $input['clock_size'] ) && is_scalar( $input['clock_size'] ) ? (string) $input['clock_size'] : '';
        if ( preg_match( '/^[0-9]{2,3}$/D', $diameter ) && (int) $diameter >= 90 && (int) $diameter <= 300 ) {
            $result['--alpdt-size'] = (int) $diameter . 'px';
        }
        foreach ( array( 'digit_size' => '--alpdt-digit-size', 'label_size' => '--alpdt-label-size' ) as $key => $property ) {
            if ( isset( $input[ $key ] ) ) {
                $safe_size = self::font_size( $input[ $key ], $key );
                if ( null !== $safe_size ) { $result[ $property ] = $safe_size; }
            }
        }
        // Explicit radius applies to visible countdown panels/cards only.
        // Omitted value means preserve each clock style's original corners.
        $radius = isset( $input['clock_radius'] ) && is_scalar( $input['clock_radius'] ) ? trim( (string) $input['clock_radius'] ) : '';
        if ( preg_match( '/^(?:[0-9]|[1-7][0-9]|80)$/D', $radius ) ) {
            $result['--alpdt-border-radius'] = (int) $radius . 'px';
        }
        $weight = isset( $input['clock_weight'] ) && is_scalar( $input['clock_weight'] ) ? (string) $input['clock_weight'] : '';
        if ( in_array( $weight, array( '300', '400', '500', '600', '700', '800', '900' ), true ) ) { $result['--alpdt-clock-font-weight'] = $weight; }
        $italic = isset( $input['clock_italic'] ) && is_scalar( $input['clock_italic'] ) ? (string) $input['clock_italic'] : '';
        if ( in_array( $italic, array( 'normal', 'italic' ), true ) ) { $result['--alpdt-clock-font-style'] = $italic; }
        $bulb_gap = isset( $input['bulb_card_gap'] ) && is_scalar( $input['bulb_card_gap'] ) ? trim( (string) $input['bulb_card_gap'] ) : '';
        if ( preg_match( '/^(?:[0-9]|[1-6][0-9]|70)$/D', $bulb_gap ) ) { $result['--alpdt-bulb-card-gap'] = (int) $bulb_gap . 'px'; }
        $bulb_count = isset( $input['bulb_count'] ) && is_scalar( $input['bulb_count'] ) ? trim( (string) $input['bulb_count'] ) : '';
        if ( preg_match( '/^[0-9]{1,2}$/D', $bulb_count ) && (int) $bulb_count >= 8 && (int) $bulb_count <= 60 ) { $result['--alpdt-bulb-count'] = $bulb_count; }
        // Legacy Snowfall extra width is honored only when an old saved shortcode specifies it.
        $card_spacing = isset( $input['card_inner_spacing'] ) && is_scalar( $input['card_inner_spacing'] ) ? trim( (string) $input['card_inner_spacing'] ) : '';
        if ( preg_match( '/^(?:[0-9]|[12][0-9]|30)$/D', $card_spacing ) ) { $result['--alpdt-snow-extra-width'] = (int) $card_spacing . 'px'; }
        // Keep the progress ring in proportion to the configured numeral size.
        // A 60px digit corresponds to the original configured circle diameter.
        if ( isset( $result['--alpdt-digit-size'] ) ) {
            $base = isset( $result['--alpdt-size'] ) ? (int) $result['--alpdt-size'] : 170;
            $digit_size = $result['--alpdt-digit-size'];
            if ( preg_match( '/^[0-9]+(?:\.[0-9]+)?px$/D', $digit_size ) ) {
                $result['--alpdt-ring-diameter'] = max( 72, min( 300, (int) round( $base * (float) $digit_size / 60 ) ) ) . 'px';
            } else {
                // Preserve fluid units in the proportional ring rather than coercing vw/rem to px.
                $result['--alpdt-ring-diameter'] = 'clamp(72px, calc(' . $digit_size . ' * ' . round( $base / 60, 4 ) . '), 300px)';
            }
        }
        $blood_font = isset( $input['blood_font'] ) && is_scalar( $input['blood_font'] ) ? (string) $input['blood_font'] : '';
        if ( 'nosifer' === $blood_font ) { $result['--alpdt-blood-font'] = "'ALPDT Nosifer', 'Nosifer', cursive"; }
        if ( 'creepster' === $blood_font ) { $result['--alpdt-blood-font'] = "'ALPDT Creepster', 'Creepster', cursive"; }
        // New shortcodes use four fields without a mode switch. Legacy uniform values
        // remain accepted and still apply to all four sides.
        $padding_keys = array( 'top', 'right', 'bottom', 'left' );
        $has_explicit_sides = false;
        foreach ( $padding_keys as $side ) { if ( isset( $input['clock_padding_' . $side] ) && '' !== (string) $input['clock_padding_' . $side] ) { $has_explicit_sides = true; break; } }
        $individual = $has_explicit_sides || ( isset( $input['clock_padding_mode'] ) && 'individual' === $input['clock_padding_mode'] );
        if ( $individual ) {
            $has_side = false;
            foreach ( $padding_keys as $side ) {
                $key = 'clock_padding_' . $side;
                $value = isset( $input[ $key ] ) && is_scalar( $input[ $key ] ) ? trim( (string) $input[ $key ] ) : '';
                if ( preg_match( '/^(?:[0-9]|[1-7][0-9]|80)$/D', $value ) ) {
                    $result['--alpdt-pad-' . $side] = (int) $value . 'px';
                    $has_side = true;
                }
            }
            if ( $has_side ) {
                foreach ( $padding_keys as $side ) {
                    if ( ! isset( $result['--alpdt-pad-' . $side] ) ) { $result['--alpdt-pad-' . $side] = '0px'; }
                }
            }
        } elseif ( isset( $input['clock_padding'] ) && is_scalar( $input['clock_padding'] ) ) {
            $padding = trim( (string) $input['clock_padding'] );
            if ( preg_match( '/^(?:[0-9]|[1-7][0-9]|80)$/D', $padding ) ) {
                foreach ( $padding_keys as $side ) { $result['--alpdt-pad-' . $side] = (int) $padding . 'px'; }
            }
        }
        // Dedicated outer padding, independent of legacy clock_padding_* (number cards).
        // Only values from the allowlist become CSS custom properties; unset means style default.
        $has_outer_padding = false;
        foreach ( $padding_keys as $side ) {
            $key = 'countdown_padding_' . $side;
            $value = isset( $input[ $key ] ) && is_scalar( $input[ $key ] ) ? trim( (string) $input[ $key ] ) : '';
            if ( preg_match( '/^(?:[0-9]|[1-7][0-9]|80)$/D', $value ) ) {
                $result['--alpdt-outer-' . $side] = (int) $value . 'px';
                $has_outer_padding = true;
            }
        }
        if ( $has_outer_padding ) {
            foreach ( $padding_keys as $side ) {
                if ( ! isset( $result['--alpdt-outer-' . $side] ) ) { $result['--alpdt-outer-' . $side] = '0px'; }
            }
        }
        $fonts = array( 'system' => 'system-ui, sans-serif', 'arial' => 'Arial, Helvetica, sans-serif',
            'verdana' => 'Verdana, sans-serif', 'georgia' => 'Georgia, serif',
            'trebuchet' => '\'Trebuchet MS\', sans-serif', 'monospace' => 'ui-monospace, monospace',
            'inter' => '\'ALPDT Inter\', system-ui, sans-serif', 'poppins' => '\'ALPDT Poppins\', sans-serif',
            'montserrat' => '\'ALPDT Montserrat\', sans-serif', 'lato' => '\'ALPDT Lato\', Arial, sans-serif',
            'fredoka' => '\'ALPDT Fredoka\', sans-serif', 'orbitron' => '\'ALPDT Orbitron\', sans-serif',
            'mountains_christmas' => '\'ALPDT Mountains of Christmas\', Georgia, serif',
            'snowburst' => '\'ALPDT Snowburst One\', Georgia, serif',
            'almonte_snow' => '\'ALPDT Almonte Snow\', Georgia, serif',
            'creepster' => '\'ALPDT Creepster\', \'Creepster\', cursive',
            'nosifer' => '\'ALPDT Nosifer\', \'Nosifer\', cursive' );
        $font = isset( $input['clock_font'] ) && is_scalar( $input['clock_font'] ) ? (string) $input['clock_font'] : '';
        if ( isset( $fonts[ $font ] ) ) { $result['--alpdt-font'] = $fonts[ $font ]; }
        return $result;
    }

    public static function inline_css( array $design ): string {
        $safe = self::appearance( $design );
        $out = array();
        foreach ( $safe as $name => $value ) { $out[] = $name . ':' . $value; }
        return implode( ';', $out );
    }

    /** Return calendar parts, carrying omitted larger units into the next selected unit. */
    public static function parts( DateTimeImmutable $now, DateTimeImmutable $target, array $requested ): array {
        $selected = array_values( array_intersect( self::UNITS, $requested ) );
        if ( ! $selected ) { $selected = array( 'days', 'hours', 'minutes', 'seconds' ); }
        $result = array_fill_keys( $selected, 0 );
        if ( $target <= $now ) { return $result; }
        $zone = $target->getTimezone();
        $cursor = $now->setTimezone( $zone );
        $delta = $cursor->diff( $target );
        $years = $delta->y;
        $months = $delta->m;
        if ( in_array( 'years', $selected, true ) ) {
            $result['years'] = $years;
            $cursor = self::add_months_clamped( $cursor, $years * 12 );
        } else {
            $months += $years * 12;
        }
        if ( in_array( 'months', $selected, true ) ) {
            // A month is a calendar month, never a fixed number of seconds.
            $n = $months;
            $next = self::add_months_clamped( $cursor, $n );
            while ( $n > 0 && $next > $target ) { --$n; $next = self::add_months_clamped( $cursor, $n ); }
            $result['months'] = $n;
            $cursor = $next;
        }
        if ( in_array( 'days', $selected, true ) ) {
            $n = (int) $cursor->diff( $target )->days;
            $next = $cursor->modify( '+' . $n . ' days' );
            while ( $n > 0 && $next > $target ) { --$n; $next = $cursor->modify( '+' . $n . ' days' ); }
            $result['days'] = $n;
            $cursor = $next;
        }
        $remaining = max( 0, $target->getTimestamp() - $cursor->getTimestamp() );
        foreach ( array( 'hours' => 3600, 'minutes' => 60, 'seconds' => 1 ) as $unit => $step ) {
            if ( in_array( $unit, $selected, true ) ) {
                $result[ $unit ] = intdiv( $remaining, $step );
                $remaining %= $step;
            }
        }
        return $result;
    }

    private static function add_months_clamped( DateTimeImmutable $date, int $months ): DateTimeImmutable {
        $year = (int) $date->format( 'Y' );
        $month = (int) $date->format( 'n' );
        $total = $year * 12 + $month - 1 + $months;
        $y = intdiv( $total, 12 );
        $m = $total % 12 + 1;
        if ( $y < 1 || $y > 9999 ) { return $date; }
        $last = (int) $date->setDate( $y, $m, 1 )->format( 't' );
        return $date->setDate( $y, $m, min( (int) $date->format( 'j' ), $last ) );
    }

    public static function markup( DateTimeImmutable $target, string $raw_units, string $raw_style, ?DateTimeImmutable $now = null, array $design = array() ): string {
        $units = self::units( $raw_units );
        if ( ! $units ) { $units = array( 'days', 'hours', 'minutes', 'seconds' ); }
        $now = $now ?: new DateTimeImmutable( 'now', wp_timezone() );
        $parts = self::parts( $now, $target, $units );
        $labels = array(
            'years' => __( 'Years', 'alp-dynamic-time-countdowns' ),
            'months' => __( 'Months', 'alp-dynamic-time-countdowns' ),
            'days' => __( 'Days', 'alp-dynamic-time-countdowns' ),
            'hours' => __( 'Hours', 'alp-dynamic-time-countdowns' ),
            'minutes' => __( 'Minutes', 'alp-dynamic-time-countdowns' ),
            'seconds' => __( 'Seconds', 'alp-dynamic-time-countdowns' ),
        );
        // Minimalist promo bars use short unit badges by default. Custom labels always win.
        $full_labels = $labels;
        // Legacy minimalist remains available to old saved 0.10.2 codes, not the style selector.
        $is_minimalist = 'minimalist' === self::style( $raw_style );
        $is_horizontal = 'horizontal' === ( $design['clock_layout'] ?? '' ) && self::supports_horizontal( self::style( $raw_style ) );
        if ( $is_minimalist ) {
            $labels = array( 'years' => 'Y', 'months' => 'MO', 'days' => 'D', 'hours' => 'H', 'minutes' => 'M', 'seconds' => 'S' );
        }
        // Empty overrides use the active WordPress locale. Admin and Divi may override each unit.
        foreach ( self::UNITS as $unit ) {
            $key = 'label_' . $unit;
            if ( isset( $design[ $key ] ) && is_scalar( $design[ $key ] ) ) {
                $raw = trim( sanitize_text_field( (string) $design[ $key ] ) );
                if ( '' !== $raw ) { $labels[ $unit ] = function_exists( 'mb_substr' ) ? mb_substr( $raw, 0, 32 ) : substr( $raw, 0, 32 ); }
            }
        }
        $duration = isset( $design['celebration_duration'] ) && in_array( (string) $design['celebration_duration'], array( '3', '5', '8', '10' ), true ) ? (string) $design['celebration_duration'] : '5';
        $message = isset( $design['celebration_message'] ) && is_scalar( $design['celebration_message'] ) ? trim( sanitize_text_field( (string) $design['celebration_message'] ) ) : '';
        if ( '' === $message ) { $message = __( 'It’s time to celebrate!', 'alp-dynamic-time-countdowns' ); }
        $message = function_exists( 'mb_substr' ) ? mb_substr( $message, 0, 120 ) : substr( $message, 0, 120 );
        $mode = isset( $design['celebration_mode'] ) && 'loop' === $design['celebration_mode'] ? 'loop' : 'end';
        $palette_attrs = '';
        if ( 'confetti' === self::style( $raw_style ) ) {
            foreach ( array( 'confetti', 'balloon' ) as $kind ) {
                $palette = array();
                $defaults = 'confetti' === $kind ? array( '#FFCC00', '#F33B49', '#053197', '#07B57B' ) : array( '#F78AB5', '#7B5DE8', '#07B57B', '#FFCC00' );
                $highest = 0;
                for ( $index = 1; $index <= 10; ++$index ) {
                    $key = $kind . '_' . $index . '_color';
                    $color = isset( $design[ $key ] ) && is_scalar( $design[ $key ] ) ? trim( (string) $design[ $key ] ) : '';
                    if ( preg_match( '/^#[0-9a-fA-F]{6}(?:[0-9a-fA-F]{2})?$/D', $color ) ) { $highest = $index; }
                }
                for ( $index = 1; $index <= $highest; ++$index ) {
                    $key = $kind . '_' . $index . '_color';
                    $color = isset( $design[ $key ] ) && is_scalar( $design[ $key ] ) ? trim( (string) $design[ $key ] ) : '';
                    $palette[] = preg_match( '/^#[0-9a-fA-F]{6}(?:[0-9a-fA-F]{2})?$/D', $color ) ? strtolower( $color ) : strtolower( $defaults[ ( $index - 1 ) % count( $defaults ) ] );
                }
                if ( $palette ) { $palette_attrs .= ' data-alpdt-' . $kind . '-colors="' . esc_attr( implode( ',', $palette ) ) . '"'; }
            }
        }
        $celebration_attrs = in_array( self::style( $raw_style ), array( 'celebration', 'confetti' ), true ) ? ' data-alpdt-celebration-duration="' . esc_attr( $duration ) . '" data-alpdt-celebration-mode="' . esc_attr( $mode ) . '" data-alpdt-celebration-message="' . esc_attr( $message ) . '"' : '';
        if ( 'blood' !== self::style( $raw_style ) ) { unset( $design['blood_font'] ); }
        $safe_design = self::appearance( $design );
        $padding_class = isset( $safe_design['--alpdt-pad-top'] ) ? ' alpdt-has-padding' : '';
        $outer_padding_class = isset( $safe_design['--alpdt-outer-top'] ) ? ' alpdt-has-outer-padding' : '';
        $bulb_gap_class = 'christmas_lights' === self::style( $raw_style ) && isset( $safe_design['--alpdt-bulb-card-gap'] ) ? ' alpdt-has-bulb-gap' : '';
        $radius_class = isset( $safe_design['--alpdt-border-radius'] ) ? ' alpdt-has-radius' : '';
        // With limited top padding, keep the light strand clipped inside the
        // panel and behind the readable digits instead of above/outside it.
        $lamps_behind = 'christmas_lights' === self::style( $raw_style )
            && $is_horizontal
            && isset( $safe_design['--alpdt-pad-top'] )
            && (int) $safe_design['--alpdt-pad-top'] < 48 ? ' alpdt-xmas-behind' : '';
        $glow_background_class = 'glow' === self::style( $raw_style ) && isset( $safe_design['--alpdt-glow-bg'] ) ? ' alpdt-has-glow-background' : '';
        $type_class = isset( $safe_design['--alpdt-clock-font-weight'] ) || isset( $safe_design['--alpdt-clock-font-style'] ) ? ' alpdt-has-typography' : '';
        $font_class = isset( $design['clock_font'] ) && 'almonte_snow' === $design['clock_font'] ? ' alpdt-font-almonte-snow' : '';
        $full_width_class = isset( $design['clock_width'] ) && 'full' === $design['clock_width'] ? ' alpdt-full-width' : ''; 
        $blood_font_attr = 'blood' === self::style( $raw_style ) ? ' data-alpdt-blood-font="' . esc_attr( isset( $design['blood_font'] ) && 'nosifer' === $design['blood_font'] ? 'nosifer' : 'creepster' ) . '"' : '';
        $lights_class = 'christmas_lights' === self::style( $raw_style ) && isset( $design['lights_mode'] ) && 'warm' === $design['lights_mode'] ? ' alpdt-lights-warm' : '';
        $markup = '<span class="alpdt-countdown alpdt-clock-grid alpdt-clock-' . esc_attr( str_replace( '_', '-', self::style( $raw_style ) ) ) . ( $is_horizontal ? ' alpdt-layout-horizontal' : '' ) . $padding_class . $outer_padding_class . $bulb_gap_class . $radius_class . $lamps_behind . $lights_class . $type_class . $font_class . $full_width_class . $glow_background_class . '" style="' . esc_attr( self::inline_css( $design ) ) . '"' . $blood_font_attr . ' role="timer" aria-live="off" data-alpdt-target="' . esc_attr( $target->getTimestamp() ) . '" data-alpdt-zone="' . esc_attr( wp_timezone()->getName() ) . '" data-alpdt-units="' . esc_attr( implode( ',', $units ) ) . '"' . $celebration_attrs . $palette_attrs . '>';
        // Static semantic decoration. Snowflakes and bulbs move entirely through scoped CSS,
        // with no JavaScript particle engine, timers or externally loaded images.
        if ( 'snowfall' === self::style( $raw_style ) ) {
            // Match the reference's independently falling, gently drifting flakes.
            // A stratified fractional phase prevents clusters at exactly the same height.
            // All motion is CSS-only; this PHP emits static, sanitized presentation tokens.
            $markup .= '<span class="alpdt-snow-layer" aria-hidden="true">';
            $symbols = array( '❄', '❅', '❆', '✳', '•' );
            $flake_count = 96;
            for ( $i = 0; $i < $flake_count; ++$i ) {
                $x = wp_rand( 0, 1000 ) / 10;
                $size = wp_rand( 4, 16 );
                $duration = wp_rand( 190, 370 ) / 10;
                $phase = ( $i + wp_rand( 0, 999 ) / 1000 ) / $flake_count;
                $delay = -$duration * $phase;
                $drift = wp_rand( -39, 39 );
                $sway = wp_rand( -18, 18 );
                $opacity = wp_rand( 45, 92 ) / 100;
                $shape = $symbols[ wp_rand( 0, count( $symbols ) - 1 ) ];
                $markup .= '<span class="alpdt-snowflake" style="' . esc_attr( sprintf( '--alpdt-flake-x:%.1f%%;--alpdt-flake-size:%dpx;--alpdt-flake-duration:%.1fs;--alpdt-flake-delay:%.2fs;--alpdt-flake-drift:%dpx;--alpdt-flake-sway:%dpx;--alpdt-flake-opacity:%.2f', $x, $size, $duration, $delay, $drift, $sway, $opacity ) ) . '">' . esc_html( $shape ) . '</span>';
            }
            $markup .= '</span>';
        } elseif ( 'christmas_lights' === self::style( $raw_style ) ) {
            $markup .= '<span class="alpdt-xmas-strand" aria-hidden="true">';
            $bulb_count = isset( $design['bulb_count'] ) && is_scalar( $design['bulb_count'] ) && preg_match( '/^[0-9]{1,2}$/D', (string) $design['bulb_count'] ) ? (int) $design['bulb_count'] : 15;
            $bulb_count = max( 8, min( 60, $bulb_count ) );
            $palette = array( '#ff4958', '#21ed8e', '#ffdb52', '#63b5ff', '#ed71f9' );
            for ( $index = 1; $index <= 12; ++$index ) {
                $color = isset( $design[ 'christmas_' . $index . '_color' ] ) && is_scalar( $design[ 'christmas_' . $index . '_color' ] ) ? trim( (string) $design[ 'christmas_' . $index . '_color' ] ) : '';
                if ( $index <= 5 && preg_match( '/^#[0-9a-fA-F]{6}(?:[0-9a-fA-F]{2})?$/D', $color ) ) { $palette[ $index - 1 ] = strtolower( $color ); }
                if ( $index > 5 && preg_match( '/^#[0-9a-fA-F]{6}(?:[0-9a-fA-F]{2})?$/D', $color ) ) { $palette[] = strtolower( $color ); }
            }
            for ( $i = 0; $i < $bulb_count; ++$i ) {
                $markup .= '<span class="alpdt-xmas-light" style="--alpdt-light-color:' . esc_attr( 'warm' === ( $design['lights_mode'] ?? '' ) ? '#ffe4a0' : $palette[ $i % count( $palette ) ] ) . '"></span>';
            }
            $markup .= '</span>';
        }
        foreach ( $parts as $key => $value ) {
            $shown = $is_minimalist || $is_horizontal || ! in_array( $key, array( 'years', 'months', 'days' ), true ) ? sprintf( '%02d', $value ) : (string) $value;
            $label = $labels[ $key ];
            if ( $is_horizontal ) {
                // Unicode-safe first character of the translated/customized unit label.
                $initial = '';
                if ( preg_match( '/^./u', $label, $first ) ) {
                    $initial = function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $first[0], 'UTF-8' ) : strtoupper( $first[0] );
                }
                $label = '' !== $initial ? $initial : $label;
            }
            $markup .= '<span class="alpdt-clock-cell" data-alpdt-unit="' . esc_attr( $key ) . '"><span class="alpdt-clock-number">' . ( 'ring' === self::style( $raw_style ) ? '<svg class="alpdt-ring-svg" viewBox="0 0 120 120" focusable="false" aria-hidden="true"><circle class="alpdt-ring-track" cx="60" cy="60" r="49" pathLength="100"/><circle class="alpdt-ring-progress" cx="60" cy="60" r="49" pathLength="100"/></svg>' : '' ) . '<span class="alpdt-clock-digit">' . esc_html( $shown ) . '</span></span><span class="alpdt-clock-label"' . ( $is_minimalist || $is_horizontal ? ' aria-hidden="true"' : '' ) . '>' . esc_html( $label ) . '</span>' . ( $is_minimalist || $is_horizontal ? '<span class="alpdt-visually-hidden">' . esc_html( $is_horizontal ? $labels[ $key ] : $full_labels[ $key ] ) . '</span>' : '' ) . '</span>';
        }
        return $markup . '</span>';
    }
}
