<?php
/**
 * WordPress core privacy integrations for per-user Quick Colors.
 *
 * Site-wide saved dates and named shortcodes are not user-meta records and are
 * intentionally preserved by the existing uninstall policy.
 */

defined( 'ABSPATH' ) || exit;

final class ALPDT_Privacy {
    private const META_KEY = 'alpdt_quick_colors';
    private const GROUP_ID = 'alpdt-quick-colors';

    /** Register a personal-data exporter with the standard WordPress workflow. */
    public static function register_exporters( array $exporters ): array {
        $exporters['alpdt-quick-colors'] = array(
            'exporter_friendly_name' => __( 'ALP Dynamic Time & Countdowns', 'alp-dynamic-time-countdowns' ),
            'callback'               => array( __CLASS__, 'export_quick_colors' ),
        );
        return $exporters;
    }

    /** Export only the matching account's stored custom palette, not defaults. */
    public static function export_quick_colors( $email_address, $page = 1 ): array {
        $result = array( 'data' => array(), 'done' => true );
        if ( 1 !== (int) $page || ! is_string( $email_address ) || ! is_email( $email_address ) ) {
            return $result;
        }
        $user = get_user_by( 'email', $email_address );
        if ( ! $user || ! metadata_exists( 'user', $user->ID, self::META_KEY ) ) {
            return $result;
        }

        $palette = get_user_meta( $user->ID, self::META_KEY, true );
        $validated = ALPDT_Admin::valid_quick_colors( $palette );
        if ( null !== $validated ) {
            $value = implode( ', ', $validated );
        } else {
            // Legacy or malformed values must still be represented in a
            // personal-data export; never inject raw markup into the report.
            $json  = wp_json_encode( $palette );
            $value = is_string( $json ) ? sanitize_text_field( $json ) : '';
        }
        $result['data'][] = array(
            'group_id'    => self::GROUP_ID,
            'group_label' => __( 'ALP Dynamic Time & Countdowns preferences', 'alp-dynamic-time-countdowns' ),
            'item_id'     => 'alpdt-quick-colors-' . absint( $user->ID ),
            'data'        => array(
                array(
                    'name'  => __( 'Quick Colors palette', 'alp-dynamic-time-countdowns' ),
                    'value' => $value,
                ),
            ),
        );
        return $result;
    }

    /** Register a targeted eraser; the core UI handles confirmation and access. */
    public static function register_erasers( array $erasers ): array {
        $erasers['alpdt-quick-colors'] = array(
            'eraser_friendly_name' => __( 'ALP Dynamic Time & Countdowns', 'alp-dynamic-time-countdowns' ),
            'callback'             => array( __CLASS__, 'erase_quick_colors' ),
        );
        return $erasers;
    }

    /** Remove a requesting user's palette without touching any other metadata. */
    public static function erase_quick_colors( $email_address, $page = 1 ): array {
        $result = array(
            'items_removed'  => false,
            'items_retained' => false,
            'messages'       => array(),
            'done'           => true,
        );
        if ( 1 !== (int) $page || ! is_string( $email_address ) || ! is_email( $email_address ) ) {
            return $result;
        }
        $user = get_user_by( 'email', $email_address );
        if ( ! $user || ! metadata_exists( 'user', $user->ID, self::META_KEY ) ) {
            return $result;
        }
        if ( delete_user_meta( $user->ID, self::META_KEY ) ) {
            $result['items_removed'] = true;
        } else {
            $result['items_retained'] = true;
            $result['messages'][] = __( 'Could not erase the saved Quick Colors palette. Please try again.', 'alp-dynamic-time-countdowns' );
        }
        return $result;
    }

    /** Offer site administrators truthful text for their privacy policy. */
    public static function suggest_policy_content(): void {
        if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
            return;
        }
        $content = '<p>' . esc_html__( 'ALP Dynamic Time & Countdowns stores the Quick Colors palette in user metadata for administrators who save color preferences. These preferences remain on this WordPress site and can be exported or erased through Tools > Export Personal Data / Erase Personal Data. Removing the plugin deletes its Quick Colors user metadata.', 'alp-dynamic-time-countdowns' ) . '</p>';
        $content .= '<p>' . esc_html__( 'Saved dates and named shortcodes are stored as site-wide WordPress options and are intentionally retained after uninstall, so site administrators should review them separately when responding to a privacy request. Publicly referenced saved dates must be suitable for public display. The plugin does not include analytics or send countdown data to an external service.', 'alp-dynamic-time-countdowns' ) . '</p>';
        wp_add_privacy_policy_content( __( 'ALP Dynamic Time & Countdowns', 'alp-dynamic-time-countdowns' ), wp_kses_post( $content ) );
    }
}
