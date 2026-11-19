<?php
/** Narrow Divi 5 adapter: saved shortcode content is authoritative, local presentation stays local. */
defined( 'ABSPATH' ) || exit;

final class ALPDT_Divi5_Data {
    public static function plain( array $attrs, string $field, string $default = '' ): string {
        $value = $attrs[ $field ]['innerContent']['desktop']['value'] ?? $default;
        return is_scalar( $value ) && strlen( (string) $value ) <= ( in_array( $field, array( 'localNumberColor', 'localLabelColor', 'localAccentColor' ), true ) ? 768 : 160 ) ? sanitize_text_field( (string) $value ) : $default;
    }

    public static function slug( array $attrs ): string {
        $value = self::plain( $attrs, 'savedKey' );
        return preg_match( '/^[a-z0-9][a-z0-9_-]{0,54}$/D', $value ) ? $value : '';
    }

    /** Accept only bounded RGB(A)/HEX colors that Divi's native picker can emit. */
    public static function safe_color( string $input ): string {
        $input = trim( $input );
        // Divi stores global picker selections as $variable(JSON)$ tokens, not HEX.
        // Resolve only through its official API; validate the result before CSS output.
        if ( ( str_starts_with( $input, '$variable(' ) || str_starts_with( $input, 'gcid-' ) || str_starts_with( $input, 'var(--gcid-' ) ) &&
             class_exists( '\\ET\\Builder\\Packages\\GlobalData\\GlobalData' ) ) {
            $resolved = \ET\Builder\Packages\GlobalData\GlobalData::resolve_global_color_variable( $input );
            if ( is_string( $resolved ) && $resolved !== $input ) { $input = trim( $resolved ); }
        }
        if ( preg_match( '/^#([0-9a-fA-F]{3,4})$/D', $input, $short ) ) { $input = '#' . implode( '', array_map( static fn( $char ) => $char . $char, str_split( $short[1] ) ) ); }
        if ( preg_match( '/^#[0-9a-fA-F]{6}(?:[0-9a-fA-F]{2})?$/D', $input ) ) { return strtolower( $input ); }
        if ( ! preg_match( '/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})(?:\s*,\s*(0(?:\.\d{1,3})?|\.\d{1,3}|1(?:\.0{1,3})?))?\s*\)$/iD', $input, $m ) ) { return ''; }
        $rgb = array( (int) $m[1], (int) $m[2], (int) $m[3] );
        if ( max( $rgb ) > 255 ) { return ''; }
        $hex = sprintf( '#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2] );
        return isset( $m[4] ) && '' !== $m[4] ? $hex . sprintf( '%02x', (int) round( 255 * (float) $m[4] ) ) : $hex;
    }

    /** Strictly validated local settings; never write to Saved Shortcodes. */
    public static function local_style( array $attrs ): array {
        $safe = array();
        foreach ( array( 'localNumberColor' => 'digit_color', 'localLabelColor' => 'label_color', 'localAccentColor' => 'accent_color' ) as $field => $key ) {
            $value = self::safe_color( self::plain( $attrs, $field ) );
            if ( '' !== $value ) { $safe[ $key ] = $value; }
        }
        $label_size = self::plain( $attrs, 'localLabelSize' );
        if ( preg_match( '/^([0-9]{1,3})(?:px)?$/D', $label_size, $match ) && (int) $match[1] >= 6 && (int) $match[1] <= 200 ) {
            $safe['label_size'] = (int) $match[1];
        }
        $size = self::plain( $attrs, 'localFontSize' );
        if ( ctype_digit( $size ) && (int) $size >= 12 && (int) $size <= 110 ) { $safe['digit_size'] = (int) $size; }
        $font = self::plain( $attrs, 'localFontFamily' );
        if ( in_array( $font, array( 'system','arial','verdana','georgia','trebuchet','lato','monospace' ), true ) ) { $safe['clock_font'] = $font; }
        // Sizing is the authoritative Divi module-alignment control; retain the
        // legacy localAlignment value only as a fallback for already saved modules.
        $sizing = $attrs['module']['decoration']['sizing']['desktop']['value']['alignment'] ?? self::plain( $attrs, 'sizingAlignment' );
        $alignment = in_array( $sizing, array( 'left', 'center', 'right' ), true ) ? $sizing : self::plain( $attrs, 'localAlignment' );
        if ( in_array( $alignment, array( 'left','center','right' ), true ) ) { $safe['align'] = $alignment; }
        return $safe;
    }

    /** Return identical body markup in PHP frontend and the authenticated VB preview. */
    public static function markup( array $attrs ): string {
        $slug = self::slug( $attrs );
        if ( '' === $slug ) { return '<span class="alpdt-d5-hint">' . esc_html( __( 'Select a saved shortcode in Content.', 'alp-dynamic-time' ) ) . '</span>'; }
        $params = ALPDT_Saved_Shortcodes::resolve( $slug );
        if ( null === $params ) { return '<span class="alpdt-d5-hint">' . esc_html( __( 'This saved shortcode is no longer available.', 'alp-dynamic-time' ) ) . '</span>'; }
        $local = self::local_style( $attrs );
        // Apply overrides exclusively to the locally rendered instance. No saved option changes.
        foreach ( array( 'digit_color','label_color','accent_color','digit_size','clock_font','label_size' ) as $field ) {
            if ( isset( $local[ $field ] ) ) { $params[ $field ] = (string) $local[ $field ]; }
        }
        return ALPDT_Shortcodes::main( $params );
    }

    public static function wrapper_classes( array $attrs ): string {
        $local = self::local_style( $attrs );
        $names = array( 'alpdt_d5_module' );
        if ( isset( $local['digit_color'] ) ) { $names[] = 'alpdt-has-number-color'; }
        if ( isset( $local['label_color'] ) ) { $names[] = 'alpdt-has-label-color'; }
        if ( isset( $local['accent_color'] ) ) { $names[] = 'alpdt-has-accent-color'; }
        if ( isset( $local['digit_size'] ) ) { $names[] = 'alpdt-has-number-size'; }
        if ( isset( $local['label_size'] ) ) { $names[] = 'alpdt-has-label-size'; }
        if ( isset( $local['align'] ) ) { $names[] = 'alpdt-align-' . $local['align']; }
        if ( isset( $local['clock_font'] ) ) { $names[] = 'alpdt-has-font'; }
        $native = $attrs['clockTypography']['decoration']['font']['font'] ?? array();
        if ( is_array( $native ) ) {
            foreach ( $native as $breakpoint ) {
                $value = $breakpoint['value'] ?? array();
                if ( is_array( $value ) && ( ! empty( $value['family'] ) || ! empty( $value['size'] ) ) ) {
                    $names[] = 'alpdt-has-native-typeface';
                    break;
                }
            }
        }
        return implode( ' ', $names );
    }

    public static function inline_style( array $attrs ): string {
        $local = self::local_style( $attrs );
        $styles = array();
        if ( isset( $local['digit_color'] ) ) { $styles[] = '--alpdt-local-number:' . $local['digit_color']; }
        if ( isset( $local['label_color'] ) ) { $styles[] = '--alpdt-local-label:' . $local['label_color']; }
        if ( isset( $local['accent_color'] ) ) { $styles[] = '--alpdt-local-accent:' . $local['accent_color']; }
        if ( isset( $local['label_size'] ) ) { $styles[] = '--alpdt-local-label-size:' . $local['label_size'] . 'px'; }
        if ( isset( $local['digit_size'] ) ) {
            $styles[] = '--alpdt-local-size:' . $local['digit_size'] . 'px';
            // Maintain the dial's shape when font size changes.
            $styles[] = '--alpdt-local-ring:' . max( 72, min( 300, (int) round( 170 * $local['digit_size'] / 60 ) ) ) . 'px';
        }
        if ( isset( $local['clock_font'] ) ) {
            $fonts = array( 'system' => 'system-ui,sans-serif', 'arial' => 'Arial,Helvetica,sans-serif', 'verdana' => 'Verdana,sans-serif', 'georgia' => 'Georgia,serif', 'trebuchet' => 'Trebuchet MS,sans-serif', 'lato' => 'Lato,Arial,sans-serif', 'monospace' => 'ui-monospace,monospace' );
            $styles[] = '--alpdt-local-font:' . $fonts[ $local['clock_font'] ];
        }
        if ( isset( $local['align'] ) ) { $styles[] = 'text-align:' . $local['align']; }
        return implode( ';', $styles );
    }

    public static function choices(): array {
        $choices = array( '' => __( 'Choose a saved shortcode', 'alp-dynamic-time' ) );
        foreach ( ALPDT_Saved_Shortcodes::all() as $slug => $entry ) {
            $choices[ $slug ] = sanitize_text_field( (string) ( $entry['name'] ?? $slug ) );
        }
        return $choices;
    }
}
