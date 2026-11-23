<?php
defined( 'ABSPATH' ) || exit;

final class ALPDT_Admin {
    private const PAGE = 'alpdt-time';

    /** Native Settings action in the WordPress plugins list. */
    public static function settings_link( array $links ): array {
        if ( current_user_can( 'manage_options' ) ) {
            array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php?page=' . self::PAGE ) ) . '">' . esc_html__( 'Settings', 'alp-dynamic-time-countdowns' ) . '</a>' );
        }
        return $links;
    }

    /** ALP Quick Colors defaults (black, red, blue, green, yellow, white). */
    public static function default_quick_colors(): array {
        return array( '#000000', '#F33B49', '#053197', '#07B57B', '#FFCC00', '#FFFFFF' );
    }

    /**
     * Replace previous preset swatches without discarding user-added colors.
     * Do not refill an intentionally empty/fully customized palette.
     */
    public static function upgrade_legacy_quick_colors( array $colors ): array {
        $replacements = array(
            '#F5A6AD' => '#F33B49',
            '#A5C8F2' => '#053197',
            '#A5DFC4' => '#07B57B',
        );
        $contains_legacy = false;
        $updated = array();
        foreach ( $colors as $color ) {
            if ( isset( $replacements[ $color ] ) ) {
                $contains_legacy = true;
                $color = $replacements[ $color ];
            }
            if ( ! in_array( $color, $updated, true ) ) {
                $updated[] = $color;
            }
        }
        if ( ! $contains_legacy ) {
            return $colors;
        }
        if ( ! in_array( '#FFCC00', $updated, true ) && count( $updated ) < 24 ) {
            $white_position = array_search( '#FFFFFF', $updated, true );
            if ( false === $white_position ) {
                $updated[] = '#FFCC00';
            } else {
                array_splice( $updated, $white_position, 0, array( '#FFCC00' ) );
            }
        }
        return $updated;
    }

    /** All palette values are untrusted, including data from user meta. */
    public static function valid_quick_colors( $colors ): ?array {
        if ( ! is_array( $colors ) || ! array_is_list( $colors ) || count( $colors ) > 24 ) { return null; }
        $clean = array();
        foreach ( $colors as $color ) {
            if ( ! is_string( $color ) || ! preg_match( '/^#[0-9a-fA-F]{6}([0-9a-fA-F]{2})?$/D', $color ) ) { return null; }
            $color = strtoupper( $color );
            if ( ! in_array( $color, $clean, true ) ) { $clean[] = $color; }
        }
        return $clean;
    }

    /** Read per-user palette. An explicitly empty array stays empty. */
    public static function quick_colors(): array {
        $stored = get_user_meta( get_current_user_id(), 'alpdt_quick_colors', true );
        if ( '' === $stored ) { return self::default_quick_colors(); }
        $validated = self::valid_quick_colors( $stored );
        if ( null === $validated ) { return self::default_quick_colors(); }
        $updated = self::upgrade_legacy_quick_colors( $validated );
        if ( $updated !== $validated ) {
            update_user_meta( get_current_user_id(), 'alpdt_quick_colors', $updated );
        }
        return $updated;
    }

    /** Persist quick colors for this admin only; never change shortcode styles. */
    public static function save_quick_colors(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'You are not allowed to edit Quick Colors.', 'alp-dynamic-time-countdowns' ) ), 403 );
        }
        if ( ! check_ajax_referer( 'alpdt_quick_colors', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( 'Your session has expired. Reload the page and try again.', 'alp-dynamic-time-countdowns' ) ), 403 );
        }
        if ( ! isset( $_POST['colors'] ) || ! is_string( $_POST['colors'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid color palette.', 'alp-dynamic-time-countdowns' ) ), 400 );
        }
        // JSON is bounded before decoding, then every item must pass the exact
        // HEX allowlist. No arbitrary CSS or non-scalar values are persisted.
        $raw_colors = sanitize_text_field( wp_unslash( $_POST['colors'] ) );
        if ( strlen( $raw_colors ) > 1024 ) {
            wp_send_json_error( array( 'message' => __( 'Invalid color palette.', 'alp-dynamic-time-countdowns' ) ), 400 );
        }
        // Decode as a JSON list (not an associative map), so `{ "0": ... }`
        // cannot masquerade as the expected color array.
        $decoded = json_decode( $raw_colors );
        $colors = self::valid_quick_colors( $decoded );
        if ( null === $colors ) {
            wp_send_json_error( array( 'message' => __( 'Invalid color palette.', 'alp-dynamic-time-countdowns' ) ), 400 );
        }
        update_user_meta( get_current_user_id(), 'alpdt_quick_colors', $colors );
        wp_send_json_success( array( 'colors' => $colors ) );
    }

    /** Make the dashboard a first-level WordPress admin item, not a Settings child. */
    public static function menu(): void {
        add_menu_page(
            __( 'ALP Dynamic Time & Countdowns', 'alp-dynamic-time-countdowns' ),
            __( 'ALP Dynamic Time & CD', 'alp-dynamic-time-countdowns' ),
            'manage_options',
            self::PAGE,
            array( __CLASS__, 'screen' ),
            ALPDT_URL . 'assets/branding/isotype-white.svg',
            58
        );
    }

    /** Redirect saved bookmarks from the old Settings location without adding a second menu entry. */
    public static function redirect_legacy_menu_url(): void {
        global $pagenow;
        if ( 'options-general.php' !== $pagenow || ! current_user_can( 'manage_options' ) ) { return; }
        // The request method is server input. Normalize it before deciding whether a
        // legacy URL may be redirected; never redirect POST or other methods.
        $request_method = isset( $_SERVER['REQUEST_METHOD'] )
            ? strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) )
            : '';
        if ( 'GET' !== $request_method ) { return; }

        // phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only GET navigation on a legacy settings URL; redirecting does not change server state. Requiring a nonce here would break existing bookmarks. Export's nonce is verified by post_actions() at the destination before any data is sent.
        if ( ! isset( $_GET['page'] ) || ! is_string( $_GET['page'] ) || self::PAGE !== sanitize_key( wp_unslash( $_GET['page'] ) ) ) { return; }

        $args = array( 'page' => self::PAGE );
        // Only keep supported, read-only navigation arguments and the export nonce.
        foreach ( array( 'alpdt_view', 'alpdt_message' ) as $key ) {
            if ( isset( $_GET[ $key ] ) && is_string( $_GET[ $key ] ) ) {
                $args[ $key ] = sanitize_key( wp_unslash( $_GET[ $key ] ) );
            }
        }
        if ( isset( $_GET['alpdt_scan_page'] ) && is_scalar( $_GET['alpdt_scan_page'] ) ) {
            $args['alpdt_scan_page'] = min( 100, max( 1, absint( wp_unslash( $_GET['alpdt_scan_page'] ) ) ) );
        }
        if ( isset( $_GET['alpdt_export'] ) ) {
            $args['alpdt_export'] = 1;
            if ( isset( $_GET['_wpnonce'] ) && is_string( $_GET['_wpnonce'] ) ) {
                $args['_wpnonce'] = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );
            }
        }
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
        wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
        exit;
    }

    /** Keep the official white isotype at native WordPress menu-icon dimensions. */
    public static function menu_icon_assets(): void {
        // Tiny, sidebar-only stylesheet; loaded across wp-admin because the menu is global.
        // Never modify other plugin/menu icons or WordPress's global image styles.
        wp_enqueue_style( 'alpdt-menu-icon', ALPDT_URL . 'assets/admin-menu-icon' . ALPDT_ASSET_SUFFIX . '.css', array(), ALPDT_VERSION );
    }

    public static function assets( string $hook ): void {
        if ( 'toplevel_page_' . self::PAGE !== $hook ) { return; }
        wp_enqueue_style( 'alpdt-admin', ALPDT_URL . 'assets/admin' . ALPDT_ASSET_SUFFIX . '.css', array(), ALPDT_VERSION );
        wp_enqueue_style( 'alpdt-countdown', ALPDT_URL . 'assets/countdown' . ALPDT_ASSET_SUFFIX . '.css', array(), ALPDT_VERSION );
        wp_enqueue_script( 'alpdt-countdown', ALPDT_URL . 'assets/countdown' . ALPDT_ASSET_SUFFIX . '.js', array(), ALPDT_VERSION, true );
        wp_enqueue_script( 'alpdt-admin', ALPDT_URL . 'assets/admin' . ALPDT_ASSET_SUFFIX . '.js', array( 'alpdt-countdown' ), ALPDT_VERSION, true );
        wp_add_inline_script( 'alpdt-admin', 'window.alpdtAdmin=' . wp_json_encode( array(
            'restUrl' => esc_url_raw( rest_url( 'alpdt/v1/render' ) ),
            'timezone' => wp_timezone()->getName(),
            'copied' => __( 'Shortcode copied.', 'alp-dynamic-time-countdowns' ),
            'copyGeneric' => __( 'Copy shortcode', 'alp-dynamic-time-countdowns' ),
            'seoCopied' => __( 'SEO variable copied.', 'alp-dynamic-time-countdowns' ),
            'anniversaryVariableCopied' => __( 'Variable copied.', 'alp-dynamic-time-countdowns' ),
            'manageColors' => __( 'Manage colors', 'alp-dynamic-time-countdowns' ),
            'doneManaging' => __( 'Done', 'alp-dynamic-time-countdowns' ),
            'addColor' => __( 'Add color', 'alp-dynamic-time-countdowns' ),
            'seoSaveFirst' => __( 'Save this configuration in Saved Shortcodes to create an SEO variable.', 'alp-dynamic-time-countdowns' ),
            'seoUnsavedChanges' => __( 'You changed this configuration. Save your changes before copying its SEO variable.', 'alp-dynamic-time-countdowns' ),
            'seoReady' => __( 'Ready for the selected SEO plugin. Confirm the final metadata on the published page.', 'alp-dynamic-time-countdowns' ),
            'seoNoCountdown' => __( 'SEO variables are only available for text values, not live countdown clocks.', 'alp-dynamic-time-countdowns' ),
            'seoProvider' => __( 'SEO plugin', 'alp-dynamic-time-countdowns' ),
            'seoSavedVars' => array_reduce( array_keys( ALPDT_Saved_Shortcodes::all() ), static function ( $result, $key ) {
                $attrs = ALPDT_Saved_Shortcodes::resolve( $key );
                if ( $attrs && 'countdown' !== ( $attrs['type'] ?? '' ) ) { $result[ $key ] = ALPDT_Integrations::saved_variable_name( $key ); }
                return $result;
            }, array() ),
            'deleteTitle' => __( 'Delete saved date?', 'alp-dynamic-time-countdowns' ),
            'deleteHelp' => __( 'This date will be permanently removed. Pages using its identifier will no longer display a value.', 'alp-dynamic-time-countdowns' ),
            'cancel' => __( 'Cancel', 'alp-dynamic-time-countdowns' ),
            'delete' => __( 'Delete', 'alp-dynamic-time-countdowns' ),
            'copyFallback' => __( 'Copy was blocked. Select the text and copy it manually.', 'alp-dynamic-time-countdowns' ),
            'previewUnavailable' => __( 'Preview unavailable. Please check your connection.', 'alp-dynamic-time-countdowns' ),
            'previewEmpty' => __( 'Enter a valid date to see the result.', 'alp-dynamic-time-countdowns' ),
            'previewPostDateNeeded' => __( 'Select a sample date to preview the post date. On your site the shortcode automatically uses the actual post date.', 'alp-dynamic-time-countdowns' ),
            'previewOrdinaryCaption' => __( 'This is how the dynamic value will appear on your site.', 'alp-dynamic-time-countdowns' ),
            'previewAnniversaryBefore' => __( 'Previewing the before-anniversary message. On your site, the date determines which message appears.', 'alp-dynamic-time-countdowns' ),
            'previewAnniversaryOn' => __( 'Previewing the anniversary-day message. On your site, the date determines which message appears.', 'alp-dynamic-time-countdowns' ),
            'previewAnniversaryAfter' => __( 'Previewing the after-anniversary message. On your site, the date determines which message appears.', 'alp-dynamic-time-countdowns' ),
            'smartDateHelp' => __( 'Calendar expressions calculate automatically. For a difference between dates, enter two dates below.', 'alp-dynamic-time-countdowns' ),
            'editDate' => __( 'Edit saved date', 'alp-dynamic-time-countdowns' ),
            'saveChanges' => __( 'Save changes', 'alp-dynamic-time-countdowns' ),
            'saveDate' => __( 'Save date', 'alp-dynamic-time-countdowns' ),
            'clockPreview' => __( 'Live clock', 'alp-dynamic-time-countdowns' ),
            'snowfallBackground' => __( 'Snowfall background', 'alp-dynamic-time-countdowns' ),
            'christmasBackground' => __( 'Christmas Lights background', 'alp-dynamic-time-countdowns' ),
            'cardBackground' => __( 'Card / circle background', 'alp-dynamic-time-countdowns' ),
            'circleDiameter' => __( 'Circle diameter (px)', 'alp-dynamic-time-countdowns' ),
            'liquidDiameter' => __( 'Liquid circle diameter (px)', 'alp-dynamic-time-countdowns' ),
            'orbitDiameter' => __( 'Orbit diameter (px)', 'alp-dynamic-time-countdowns' ),
            'unitLabels' => array( 'years' => __( 'Years', 'alp-dynamic-time-countdowns' ), 'months' => __( 'Months', 'alp-dynamic-time-countdowns' ), 'days' => __( 'Days', 'alp-dynamic-time-countdowns' ), 'hours' => __( 'Hours', 'alp-dynamic-time-countdowns' ), 'minutes' => __( 'Minutes', 'alp-dynamic-time-countdowns' ), 'seconds' => __( 'Seconds', 'alp-dynamic-time-countdowns' ) ),
            'copyrightFromLabel' => __( 'Copyright starting year', 'alp-dynamic-time-countdowns' ),
            'copyrightFromHelp' => __( 'Enter the first copyright year, for example 1999. You may also enter a full date in YYYY-MM-DD order: 1999-12-15 means 15 December 1999 (month 12, day 15). The copyright range uses only the year.', 'alp-dynamic-time-countdowns' ),
            'enterYear' => __( 'Enter a starting year below', 'alp-dynamic-time-countdowns' ),
            'selectOption' => __( 'Choose an option', 'alp-dynamic-time-countdowns' ),
            'hexValue' => __( 'HEX color', 'alp-dynamic-time-countdowns' ),
            'hue' => __( 'Hue', 'alp-dynamic-time-countdowns' ),
            'saturationBrightness' => __( 'Saturation and brightness', 'alp-dynamic-time-countdowns' ),
            'closeColor' => __( 'Close color picker', 'alp-dynamic-time-countdowns' ),
            'colorPicker' => __( 'Color Picker', 'alp-dynamic-time-countdowns' ),
            'opacity' => __( 'Opacity', 'alp-dynamic-time-countdowns' ),
            'quickColors' => __( 'Quick colors', 'alp-dynamic-time-countdowns' ),
            'quickColorValues' => self::quick_colors(),
            'quickColorsUrl' => admin_url( 'admin-ajax.php' ),
            'quickColorsNonce' => wp_create_nonce( 'alpdt_quick_colors' ),
            'addQuickColor' => __( 'Add current color to Quick Colors', 'alp-dynamic-time-countdowns' ),
            /* translators: %s: HEX color value to remove from the Quick Colors list. */
            'removeQuickColor' => __( 'Remove color %s from Quick Colors', 'alp-dynamic-time-countdowns' ),
            'removeQuickColorTitle' => __( 'Remove this quick color?', 'alp-dynamic-time-countdowns' ),
            'removeQuickColorHelp' => __( 'The color will disappear from your personal Quick Colors palette. It will not change the colors already used in clocks or shortcodes.', 'alp-dynamic-time-countdowns' ),
            'removeQuickColorConfirm' => __( 'Remove color', 'alp-dynamic-time-countdowns' ),
            'quickColorAdded' => __( 'Color added to Quick Colors.', 'alp-dynamic-time-countdowns' ),
            'quickColorRemoved' => __( 'Color removed from Quick Colors.', 'alp-dynamic-time-countdowns' ),
            'quickColorAlreadySaved' => __( 'This color is already saved.', 'alp-dynamic-time-countdowns' ),
            'quickColorLimit' => __( 'You can save up to 24 quick colors.', 'alp-dynamic-time-countdowns' ),
            'quickColorFailed' => __( 'Could not save Quick Colors. Try again.', 'alp-dynamic-time-countdowns' ),
            'quickColorEmpty' => __( 'No quick colors saved. Use + to add one.', 'alp-dynamic-time-countdowns' ),
            'saveShortcode' => __( 'Save shortcode', 'alp-dynamic-time-countdowns' ),
            'updateShortcode' => __( 'Update saved shortcode', 'alp-dynamic-time-countdowns' ),
            'shortcodeNameRequired' => __( 'Enter a name to save this shortcode.', 'alp-dynamic-time-countdowns' ),
            'shortcodeInvalid' => __( 'Check the date and options before saving.', 'alp-dynamic-time-countdowns' ),
            'fontSizeInvalid' => __( 'Enter a valid font size for the selected unit.', 'alp-dynamic-time-countdowns' ),
            'deleteShortcodeTitle' => __( 'Delete saved shortcode?', 'alp-dynamic-time-countdowns' ),
            'deleteShortcodeHelp' => __( 'Pages using the reusable shortcode identifier will stop displaying a value. A copied full shortcode will keep working.', 'alp-dynamic-time-countdowns' ),
            'shortcodeEdited' => __( 'Editing saved shortcode', 'alp-dynamic-time-countdowns' ),
            'shortcodeSaveAction' => __( 'Save configuration', 'alp-dynamic-time-countdowns' ),
            'saveAsNew' => __( 'Save as new', 'alp-dynamic-time-countdowns' ),

            'confirmDelete' => __( 'Delete this date', 'alp-dynamic-time-countdowns' ),
            'helpTypes' => array(
                'elapsed' => __( 'Shows how much time has passed since a date, such as 26 years of experience.', 'alp-dynamic-time-countdowns' ),
                'year' => __( 'Shows the current year and updates automatically every January.', 'alp-dynamic-time-countdowns' ),
                'month' => __( 'Shows the name of the current month in your site language.', 'alp-dynamic-time-countdowns' ),
                'date' => __( 'Shows today’s date in the format you choose.', 'alp-dynamic-time-countdowns' ),
                'countdown' => __( 'Count down to an event with an animated live clock or remaining days; you choose its deadline and appearance.', 'alp-dynamic-time-countdowns' ),
                'smart_date' => __( 'Shows an automatic calendar date: today, a future weekday, a season, two dates compared, or the published/modified date of the current post. Only date comparison needs two entered dates.', 'alp-dynamic-time-countdowns' ),
                'copyright' => __( 'Choose just the current year or a range from your first year to today, ideal for your website footer.', 'alp-dynamic-time-countdowns' ),
                'ordinal' => __( 'Shows only the anniversary number with an ordinal suffix, like 30.º or 30th. Ideal inside a headline: Our 30th anniversary.', 'alp-dynamic-time-countdowns' ),
                'anniversary_message' => __( 'Displays one of three custom messages before, on, or after your anniversary date. Insert {number} for 30.º or {years} for 30.', 'alp-dynamic-time-countdowns' ),
            ),
        ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
    }

    public static function post_actions(): void {
        if ( is_admin() && isset( $_GET['alpdt_export'] ) && isset( $_GET['page'] ) && is_string( $_GET['page'] ) && self::PAGE === sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
            if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'Access denied.', 'alp-dynamic-time-countdowns' ), '', 403 ); }
            check_admin_referer( 'alpdt_export' );
            $presets = get_option( 'alpdt_presets', array() );
            nocache_headers();
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Content-Disposition: attachment; filename="alpdt-dates.json"' );
            echo wp_json_encode( is_array( $presets ) ? $presets : array(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
            exit;
        }
        if ( ! is_admin() || ! isset( $_POST['alpdt_action'] ) || ! isset( $_GET['page'] ) || ! is_string( $_GET['page'] ) || self::PAGE !== sanitize_key( wp_unslash( $_GET['page'] ) ) ) { return; }
        if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'Access denied.', 'alp-dynamic-time-countdowns' ), '', 403 ); }
        check_admin_referer( 'alpdt_save', 'alpdt_nonce' );
        if ( ! is_string( $_POST['alpdt_action'] ) ) { return; }
        $action = sanitize_key( wp_unslash( $_POST['alpdt_action'] ) );
        // Saved shortcode operations are independent of the saved-date registry.
        if ( in_array( $action, array( 'save_shortcode', 'delete_shortcode' ), true ) ) {
            $slug = isset( $_POST['alpdt_slug'] ) && is_string( $_POST['alpdt_slug'] ) ? sanitize_key( wp_unslash( $_POST['alpdt_slug'] ) ) : '';
            if ( 'save_shortcode' === $action ) {
                $name = isset( $_POST['alpdt_name'] ) && is_string( $_POST['alpdt_name'] ) ? sanitize_text_field( wp_unslash( $_POST['alpdt_name'] ) ) : '';
                $code = isset( $_POST['alpdt_code'] ) && is_string( $_POST['alpdt_code'] ) ? sanitize_text_field( wp_unslash( $_POST['alpdt_code'] ) ) : '';
                $original = isset( $_POST['alpdt_original_slug'] ) && is_string( $_POST['alpdt_original_slug'] ) ? sanitize_key( wp_unslash( $_POST['alpdt_original_slug'] ) ) : '';
                $notice = ALPDT_Saved_Shortcodes::save( $slug, $name, $code, $original ) ? ( '' === $original ? 'shortcode_saved' : 'shortcode_updated' ) : 'shortcode_invalid';
            } else {
                $notice = ALPDT_Saved_Shortcodes::delete( $slug ) ? 'shortcode_deleted' : 'shortcode_invalid';
            }
            wp_safe_redirect( add_query_arg( array( 'page' => self::PAGE, 'alpdt_message' => $notice, 'alpdt_view' => 'shortcodes' ), admin_url( 'admin.php' ) ) );
            exit;
        }
        $presets = get_option( 'alpdt_presets', array() );
        if ( ! is_array( $presets ) ) { $presets = array(); }
        $notice = 'invalid';
        if ( 'save' === $action ) {
            $slug  = isset( $_POST['alpdt_slug'] ) && is_string( $_POST['alpdt_slug'] ) ? sanitize_key( wp_unslash( $_POST['alpdt_slug'] ) ) : '';
            $date  = isset( $_POST['alpdt_date'] ) && is_string( $_POST['alpdt_date'] ) ? sanitize_text_field( wp_unslash( $_POST['alpdt_date'] ) ) : '';
            $label = isset( $_POST['alpdt_label'] ) && is_string( $_POST['alpdt_label'] ) ? sanitize_text_field( wp_unslash( $_POST['alpdt_label'] ) ) : '';
            $original = isset( $_POST['alpdt_original_slug'] ) && is_string( $_POST['alpdt_original_slug'] ) ? sanitize_key( wp_unslash( $_POST['alpdt_original_slug'] ) ) : '';
            // Editing preserves the identifier, so existing shortcodes continue to work.
            $editing = '' !== $original;
            $valid = '' !== $slug && strlen( $slug ) <= 55 && '' !== $label && strlen( $label ) <= 100 && ALPDT_Engine::parse( $date );
            if ( $editing ) {
                $valid = $valid && $slug === $original && isset( $presets[ $original ] );
            } else {
                $valid = $valid && ! isset( $presets[ $slug ] ) && count( $presets ) < 200;
            }
            if ( $valid ) {
                $presets[ $slug ] = array( 'date' => $date, 'label' => $label );
                update_option( 'alpdt_presets', $presets, false );
                $notice = $editing ? 'updated' : 'saved';
            }
        } elseif ( 'delete' === $action ) {
            $slug = isset( $_POST['alpdt_slug'] ) && is_string( $_POST['alpdt_slug'] ) ? sanitize_key( wp_unslash( $_POST['alpdt_slug'] ) ) : '';
            if ( '' !== $slug && isset( $presets[ $slug ] ) ) {
                unset( $presets[ $slug ] );
                update_option( 'alpdt_presets', $presets, false );
                $notice = 'deleted';
            }
        }
        wp_safe_redirect( add_query_arg( array( 'page' => self::PAGE, 'alpdt_message' => $notice ), admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function screen(): void {
        if ( ! current_user_can( 'manage_options' ) ) { return; }
        $presets = get_option( 'alpdt_presets', array() );
        $presets = is_array( $presets ) ? $presets : array();
        $stored_shortcodes = ALPDT_Saved_Shortcodes::all();
        $shortcode_count  = count( $stored_shortcodes );
        $shortcode_pages  = max( 1, (int) ceil( $shortcode_count / 10 ) );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only pagination, not a state change.
        $requested_page   = isset( $_GET['alpdt_saved_page'] ) && is_scalar( $_GET['alpdt_saved_page'] ) ? absint( wp_unslash( $_GET['alpdt_saved_page'] ) ) : 1;
        $shortcode_page   = min( $shortcode_pages, max( 1, $requested_page ) );
        $visible_shortcodes = array_slice( $stored_shortcodes, ( $shortcode_page - 1 ) * 10, 10, true );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only status message; no form data is written here.
        $notice = isset( $_GET['alpdt_message'] ) && is_string( $_GET['alpdt_message'] ) ? sanitize_key( wp_unslash( $_GET['alpdt_message'] ) ) : '';
        $alias = shortcode_exists( 'years-since' ) && isset( $GLOBALS['shortcode_tags']['years-since'] ) && is_array( $GLOBALS['shortcode_tags']['years-since'] ) && 'ALPDT_Shortcodes' === $GLOBALS['shortcode_tags']['years-since'][0];
        ?>
        <div class="wrap alpdt-admin">
          <div id="alpdt-external-notices" class="alpdt-external-notices" hidden></div>
          <header class="alpdt-hero">
            <div class="alpdt-hero-main">
              <p class="alpdt-eyebrow"><?php esc_html_e( 'ALP Plugins', 'alp-dynamic-time-countdowns' ); ?></p>
              <div class="alpdt-hero-title"><h1 class="alpdt-brand-heading"><img class="alpdt-hero-logo" src="<?php echo esc_url( ALPDT_URL . 'assets/branding/logo-white.svg' ); ?>" alt="" width="460" height="68"><span class="alpdt-visually-hidden"><?php esc_html_e( 'ALP Dynamic Time & Countdowns', 'alp-dynamic-time-countdowns' ); ?></span></h1><span class="alpdt-version"><?php echo esc_html( 'v' . ALPDT_VERSION ); ?></span></div>
              <p class="alpdt-hero-description"><?php esc_html_e( 'Smart dates, reusable milestones and animated countdowns, all in one place.', 'alp-dynamic-time-countdowns' ); ?></p>
              <div class="alpdt-hero-meta" role="group" aria-label="<?php echo esc_attr__( 'Plugin highlights', 'alp-dynamic-time-countdowns' ); ?>" tabindex="0"><span class="alpdt-chip"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path d="m8 8-4 4 4 4m8-8 4 4-4 4M14 5l-4 14"/></svg><?php esc_html_e( 'Dynamic shortcodes', 'alp-dynamic-time-countdowns' ); ?></span><span class="alpdt-chip"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><rect x="4" y="5" width="16" height="16" rx="2"/><path d="M8 3v4m8-4v4M4 10h16m-12 5 2 2 5-5"/></svg><?php esc_html_e( 'Reusable milestones', 'alp-dynamic-time-countdowns' ); ?></span><span class="alpdt-chip"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path d="M3 9h18M3 15h18M8 4l-2 16M18 4l-2 16"/></svg><?php esc_html_e( 'SEO variables', 'alp-dynamic-time-countdowns' ); ?></span><span class="alpdt-chip"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg><?php esc_html_e( 'Countdowns', 'alp-dynamic-time-countdowns' ); ?></span></div>
            </div>
            <div class="alpdt-hero-art" aria-hidden="true"><span class="alpdt-clock-face"><span class="alpdt-clock-hand"></span><span class="alpdt-clock-minute-hand"></span></span><span class="alpdt-orbit alpdt-orbit-one"></span><span class="alpdt-orbit alpdt-orbit-two"></span></div>
          </header>
          <?php
          $messages = array(
              'saved' => __( 'Date saved. You can reuse it anywhere on your site.', 'alp-dynamic-time-countdowns' ),
              'updated' => __( 'Date updated. Every shortcode using this identifier will display the new value.', 'alp-dynamic-time-countdowns' ),
              'deleted' => __( 'Date deleted.', 'alp-dynamic-time-countdowns' ),
              'invalid' => __( 'Could not save. Check the date, required fields and identifier (it may already exist).', 'alp-dynamic-time-countdowns' ),
              'shortcode_saved' => __( 'Shortcode saved. Copy its reusable identifier or its complete code.', 'alp-dynamic-time-countdowns' ),
              'shortcode_updated' => __( 'Shortcode updated. All pages using its reusable identifier will display the new configuration.', 'alp-dynamic-time-countdowns' ),
              'shortcode_deleted' => __( 'Saved shortcode deleted.', 'alp-dynamic-time-countdowns' ),
              'shortcode_invalid' => __( 'Could not save shortcode. Check the identifier, name, configuration and duplicates.', 'alp-dynamic-time-countdowns' ),
          );
          if ( isset( $messages[ $notice ] ) ) : ?>
            <div class="alpdt-feedback <?php echo in_array( $notice, array( 'invalid', 'shortcode_invalid' ), true ) ? 'alpdt-feedback-error' : 'alpdt-feedback-success'; ?>" role="<?php echo in_array( $notice, array( 'invalid', 'shortcode_invalid' ), true ) ? 'alert' : 'status'; ?>"><?php echo esc_html( $messages[ $notice ] ); ?></div>
          <?php endif; ?>
          <div role="tablist" aria-label="ALP Dynamic Time & Countdowns" class="alpdt-tabs">
            <button type="button" role="tab" id="alpdt-tab-create" aria-controls="alpdt-panel-create" aria-selected="true" tabindex="0" data-alpdt-tab="create"><span class="alpdt-tab-icon alpdt-tab-icon--brackets" aria-hidden="true"></span><span class="alpdt-tab-label"><?php esc_html_e( 'Generator', 'alp-dynamic-time-countdowns' ); ?></span></button>
            <button type="button" role="tab" id="alpdt-tab-shortcodes" aria-controls="alpdt-panel-shortcodes" aria-selected="false" tabindex="-1" data-alpdt-tab="shortcodes"><span class="alpdt-tab-icon alpdt-tab-icon--bookmark" aria-hidden="true"></span><span class="alpdt-tab-label"><?php esc_html_e( 'Saved shortcodes', 'alp-dynamic-time-countdowns' ); ?></span></button>
            <button type="button" role="tab" id="alpdt-tab-dates" aria-controls="alpdt-panel-dates" aria-selected="false" tabindex="-1" data-alpdt-tab="dates"><span class="alpdt-tab-icon alpdt-tab-icon--calendar" aria-hidden="true"></span><span class="alpdt-tab-label"><?php esc_html_e( 'Saved dates', 'alp-dynamic-time-countdowns' ); ?></span></button>
            <button type="button" role="tab" id="alpdt-tab-compat" aria-controls="alpdt-panel-compat" aria-selected="false" tabindex="-1" data-alpdt-tab="compat"><span class="alpdt-tab-icon alpdt-tab-icon--compatibility" aria-hidden="true"></span><span class="alpdt-tab-label"><?php esc_html_e( 'Compatibility', 'alp-dynamic-time-countdowns' ); ?></span></button>
            <button type="button" role="tab" id="alpdt-tab-help" aria-controls="alpdt-panel-help" aria-selected="false" tabindex="-1" data-alpdt-tab="help"><span class="alpdt-tab-icon alpdt-tab-icon--book" aria-hidden="true"></span><span class="alpdt-tab-label"><?php esc_html_e( 'About & guide', 'alp-dynamic-time-countdowns' ); ?></span></button>
          </div>
          <section role="tabpanel" tabindex="0" id="alpdt-panel-create" aria-labelledby="alpdt-tab-create" class="alpdt-panel">
            <div class="alpdt-section-intro"><h2><?php esc_html_e( 'Shortcode generator', 'alp-dynamic-time-countdowns' ); ?></h2><p><?php esc_html_e( 'Choose what you want to show, check the result, then copy the shortcode into a page, post or compatible Divi text field.', 'alp-dynamic-time-countdowns' ); ?></p></div>
            <div class="alpdt-columns">
              <div class="alpdt-card alpdt-generator-card">
                <div class="alpdt-card-heading"><span class="alpdt-step">1</span><h3><?php esc_html_e( 'Choose the date and format', 'alp-dynamic-time-countdowns' ); ?></h3></div>
                <div class="alpdt-field">
                  <label for="alpdt-type"><?php esc_html_e( 'What do you want to display?', 'alp-dynamic-time-countdowns' ); ?></label>
                  <select id="alpdt-type" aria-describedby="alpdt-type-help">
                    <option value="elapsed"><?php esc_html_e( 'Elapsed time', 'alp-dynamic-time-countdowns' ); ?></option><option value="year"><?php esc_html_e( 'Current year', 'alp-dynamic-time-countdowns' ); ?></option><option value="month"><?php esc_html_e( 'Current month', 'alp-dynamic-time-countdowns' ); ?></option><option value="date"><?php esc_html_e( 'Current date', 'alp-dynamic-time-countdowns' ); ?></option><option value="smart_date"><?php esc_html_e( 'Smart date / calendar expression', 'alp-dynamic-time-countdowns' ); ?></option><option value="countdown"><?php esc_html_e( 'Countdown', 'alp-dynamic-time-countdowns' ); ?></option><option value="copyright"><?php esc_html_e( 'Copyright years', 'alp-dynamic-time-countdowns' ); ?></option><option value="ordinal"><?php esc_html_e( 'Anniversary number', 'alp-dynamic-time-countdowns' ); ?></option><option value="anniversary_message"><?php esc_html_e( 'Anniversary message', 'alp-dynamic-time-countdowns' ); ?></option>
                  </select><p id="alpdt-type-help" class="alpdt-field-help"></p>
                </div>
                <?php if ( $presets ) : ?>
                <div class="alpdt-field" data-alpdt-field="saved">
                  <label for="alpdt-saved"><?php esc_html_e( 'Use a saved date (optional)', 'alp-dynamic-time-countdowns' ); ?></label>
                  <select id="alpdt-saved"><option value=""><?php esc_html_e( 'Enter a date below', 'alp-dynamic-time-countdowns' ); ?></option><?php foreach ( $presets as $slug => $p ) : if ( ! is_array( $p ) || empty( $p['date'] ) ) { continue; } ?><option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( isset( $p['label'] ) ? $p['label'] : $slug ); ?></option><?php endforeach; ?></select>
                  <p class="alpdt-field-help"><?php esc_html_e( 'Saved dates can be changed once and updated everywhere.', 'alp-dynamic-time-countdowns' ); ?></p>
                </div>
                <?php endif; ?>
                <div class="alpdt-field" data-alpdt-field="from"><label for="alpdt-from"><?php esc_html_e( 'Start year or date', 'alp-dynamic-time-countdowns' ); ?></label><input id="alpdt-from" type="text" value="2000" maxlength="10" placeholder="2000 or 2000-12-15 (YYYY-MM-DD)" aria-describedby="alpdt-from-help"/><p id="alpdt-from-help" class="alpdt-field-help"><?php esc_html_e( 'Enter a year such as 2000, or a full date in YYYY-MM-DD order. Example: 2000-12-15 means year 2000, month 12 (December), day 15. The middle number is always the month; the final number is always the day.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                <div class="alpdt-field" data-alpdt-field="to"><label for="alpdt-to"><?php esc_html_e( 'Event or end date and time', 'alp-dynamic-time-countdowns' ); ?></label><input id="alpdt-to" type="datetime-local" step="60" value="2027-01-01T12:00" aria-describedby="alpdt-to-help"/><p id="alpdt-to-help" class="alpdt-field-help"><?php esc_html_e( 'Choose the exact day and hour of your event. The clock follows the time zone set in WordPress Settings → General. For example, 15 December 2026 at 18:30.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                <fieldset class="alpdt-field" data-alpdt-field="date_difference" hidden><legend><?php esc_html_e( 'Dates to compare', 'alp-dynamic-time-countdowns' ); ?></legend><div class="alpdt-date-difference-grid"><div><label for="alpdt-difference-from"><?php esc_html_e( 'Start date', 'alp-dynamic-time-countdowns' ); ?></label><input type="date" id="alpdt-difference-from" value="2026-01-01"/></div><div><label for="alpdt-difference-to"><?php esc_html_e( 'End date', 'alp-dynamic-time-countdowns' ); ?></label><input type="date" id="alpdt-difference-to" value="2026-12-31"/></div></div><p class="alpdt-field-help"><?php esc_html_e( 'Displays signed calendar days. A negative value means the end date precedes the start date.', 'alp-dynamic-time-countdowns' ); ?></p></fieldset>
                <div class="alpdt-field" data-alpdt-field="live" hidden><label class="alpdt-check" for="alpdt-live"><input type="checkbox" id="alpdt-live"/><span><?php esc_html_e( 'Refresh the displayed date after cached pages load', 'alp-dynamic-time-countdowns' ); ?></span></label><p class="alpdt-field-help"><?php esc_html_e( 'Useful with page caching. Dates from individual posts cannot be refreshed through the public endpoint.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                <div class="alpdt-field" data-alpdt-field="smart_expr" hidden>
                  <label for="alpdt-expression"><?php esc_html_e( 'Date expression', 'alp-dynamic-time-countdowns' ); ?></label>
                  <select id="alpdt-expression">
                    <?php foreach ( array( 'today' => __( 'Today', 'alp-dynamic-time-countdowns' ), 'tomorrow' => __( 'Tomorrow', 'alp-dynamic-time-countdowns' ), 'in_7_days' => __( 'In 7 days', 'alp-dynamic-time-countdowns' ), 'in_30_days' => __( 'In 30 days', 'alp-dynamic-time-countdowns' ), 'next_monday' => __( 'Next Monday', 'alp-dynamic-time-countdowns' ), 'next_tuesday' => __( 'Next Tuesday', 'alp-dynamic-time-countdowns' ), 'next_wednesday' => __( 'Next Wednesday', 'alp-dynamic-time-countdowns' ), 'next_thursday' => __( 'Next Thursday', 'alp-dynamic-time-countdowns' ), 'next_friday' => __( 'Next Friday', 'alp-dynamic-time-countdowns' ), 'next_saturday' => __( 'Next Saturday', 'alp-dynamic-time-countdowns' ), 'next_sunday' => __( 'Next Sunday', 'alp-dynamic-time-countdowns' ), 'month_end' => __( 'Last day of this month', 'alp-dynamic-time-countdowns' ), 'quarter' => __( 'Current quarter (1–4)', 'alp-dynamic-time-countdowns' ), 'season' => __( 'Season of the year', 'alp-dynamic-time-countdowns' ), 'post_published' => __( 'Current post publication date', 'alp-dynamic-time-countdowns' ), 'post_modified' => __( 'Current post last modified', 'alp-dynamic-time-countdowns' ), 'date_difference' => __( 'Days between two dates', 'alp-dynamic-time-countdowns' ) ) as $key => $label ) : ?>
                      <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                  </select>
                  <p id="alpdt-expression-help" class="alpdt-field-help"><?php esc_html_e( 'Uses your WordPress timezone. Most expressions need no manually entered date. Publication and modification dates require a published post or page.', 'alp-dynamic-time-countdowns' ); ?></p>
                </div>
                <div class="alpdt-field" data-alpdt-field="post_preview_date" hidden>
                  <label for="alpdt-preview-post-date"><?php esc_html_e( 'Example post date (preview only)', 'alp-dynamic-time-countdowns' ); ?></label>
                  <input id="alpdt-preview-post-date" type="date" value="2026-10-25" aria-describedby="alpdt-preview-post-help" />
                  <p id="alpdt-preview-post-help" class="alpdt-field-help"><?php esc_html_e( 'Choose any sample date to test the format here. This value is never saved in the shortcode: on a published post or page, its real publication or modification date is displayed automatically.', 'alp-dynamic-time-countdowns' ); ?></p>
                </div>
                <div class="alpdt-field" data-alpdt-field="hemisphere" hidden><label for="alpdt-hemisphere"><?php esc_html_e( 'Hemisphere', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-hemisphere"><option value="north"><?php esc_html_e( 'Northern', 'alp-dynamic-time-countdowns' ); ?></option><option value="south"><?php esc_html_e( 'Southern', 'alp-dynamic-time-countdowns' ); ?></option></select></div>
                <div class="alpdt-field" data-alpdt-field="recurrence" hidden>
                  <label for="alpdt-recurrence"><?php esc_html_e( 'Countdown schedule', 'alp-dynamic-time-countdowns' ); ?></label>
                  <select id="alpdt-recurrence"><option value="none"><?php esc_html_e( 'Fixed event date', 'alp-dynamic-time-countdowns' ); ?></option><option value="daily"><?php esc_html_e( 'Every day', 'alp-dynamic-time-countdowns' ); ?></option><option value="weekly"><?php esc_html_e( 'Every week', 'alp-dynamic-time-countdowns' ); ?></option><option value="monthly"><?php esc_html_e( 'Every month', 'alp-dynamic-time-countdowns' ); ?></option></select>
                  <p class="alpdt-field-help"><?php esc_html_e( 'Repeats in the WordPress timezone. A nonexistent daylight-saving time is skipped. Monthly day 31 means the last day of shorter months.', 'alp-dynamic-time-countdowns' ); ?></p>
                </div>
                <div class="alpdt-field" data-alpdt-field="repeat_time" hidden><label for="alpdt-repeat-time"><?php esc_html_e( 'Repeats at local time', 'alp-dynamic-time-countdowns' ); ?></label><input id="alpdt-repeat-time" type="time" value="12:00" step="60"/></div>
                <div class="alpdt-field" data-alpdt-field="repeat_weekday" hidden><label for="alpdt-repeat-weekday"><?php esc_html_e( 'Day of the week', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-repeat-weekday"><?php foreach ( array( __( 'Monday', 'alp-dynamic-time-countdowns' ), __( 'Tuesday', 'alp-dynamic-time-countdowns' ), __( 'Wednesday', 'alp-dynamic-time-countdowns' ), __( 'Thursday', 'alp-dynamic-time-countdowns' ), __( 'Friday', 'alp-dynamic-time-countdowns' ), __( 'Saturday', 'alp-dynamic-time-countdowns' ), __( 'Sunday', 'alp-dynamic-time-countdowns' ) ) as $index => $day ) : ?><option value="<?php echo esc_attr( $index + 1 ); ?>"><?php echo esc_html( $day ); ?></option><?php endforeach; ?></select></div>
                <div class="alpdt-field" data-alpdt-field="repeat_day" hidden><label for="alpdt-repeat-day"><?php esc_html_e( 'Day of the month (1–31)', 'alp-dynamic-time-countdowns' ); ?></label><input id="alpdt-repeat-day" type="number" min="1" max="31" step="1" value="1"/></div>
                <div class="alpdt-field" data-alpdt-field="end_action" hidden><label for="alpdt-end-action"><?php esc_html_e( 'When fixed countdown finishes', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-end-action"><option value="none"><?php esc_html_e( 'Keep showing zero', 'alp-dynamic-time-countdowns' ); ?></option><option value="message"><?php esc_html_e( 'Show a message', 'alp-dynamic-time-countdowns' ); ?></option><option value="hide"><?php esc_html_e( 'Hide countdown', 'alp-dynamic-time-countdowns' ); ?></option><option value="redirect"><?php esc_html_e( 'Redirect to a page on this website', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Only for fixed dates. Recurring timers automatically continue to the next occurrence.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                <div class="alpdt-field" data-alpdt-field="end_message" hidden><label for="alpdt-end-message"><?php esc_html_e( 'End message', 'alp-dynamic-time-countdowns' ); ?></label><input id="alpdt-end-message" type="text" maxlength="120" placeholder="Time is up!"/></div>
                <div class="alpdt-field" data-alpdt-field="end_url" hidden><label for="alpdt-end-url"><?php esc_html_e( 'Internal page path', 'alp-dynamic-time-countdowns' ); ?></label><input id="alpdt-end-url" type="text" maxlength="240" placeholder="/thank-you/" pattern="/[A-Za-z0-9/_-]*"/><p class="alpdt-field-help"><?php esc_html_e( 'Use an internal path starting with /, never an external address. Redirects occur only if the timer was running on the page.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                <div class="alpdt-field" data-alpdt-field="countdown_display"><label for="alpdt-countdown-display"><?php esc_html_e( 'Countdown display', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-countdown-display"><option value="days"><?php esc_html_e( 'Days remaining (number only)', 'alp-dynamic-time-countdowns' ); ?></option><option value="full"><?php esc_html_e( 'Live clock: days, hours, minutes and seconds', 'alp-dynamic-time-countdowns' ); ?></option><option value="custom"><?php esc_html_e( 'Custom live clock: choose which units to display', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Live clock updates each second on the page without reloading it. The animation pauses if reduced motion is enabled.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                <div class="alpdt-clock-settings" data-alpdt-field="clock_tabs">
                  <div class="alpdt-clock-tabs" role="tablist" aria-label="<?php echo esc_attr__( 'Countdown settings', 'alp-dynamic-time-countdowns' ); ?>">
                    <button type="button" role="tab" id="alpdt-clock-tab-units" aria-controls="alpdt-clock-panel-units" aria-selected="true" tabindex="0" data-alpdt-clock-tab="units"><?php esc_html_e( 'Countdown Units', 'alp-dynamic-time-countdowns' ); ?></button>
                    <button type="button" role="tab" id="alpdt-clock-tab-style" aria-controls="alpdt-clock-panel-style" aria-selected="false" tabindex="-1" data-alpdt-clock-tab="style"><?php esc_html_e( 'Style', 'alp-dynamic-time-countdowns' ); ?></button>
                    <button type="button" role="tab" id="alpdt-clock-tab-labels" aria-controls="alpdt-clock-panel-labels" aria-selected="false" tabindex="-1" data-alpdt-clock-tab="labels"><?php esc_html_e( 'Unit Labels', 'alp-dynamic-time-countdowns' ); ?></button>
                  </div>
                  <section class="alpdt-clock-panel" role="tabpanel" tabindex="0" id="alpdt-clock-panel-units" aria-labelledby="alpdt-clock-tab-units">                <fieldset class="alpdt-field" data-alpdt-field="clock_units"><legend><?php esc_html_e( 'Choose countdown units', 'alp-dynamic-time-countdowns' ); ?></legend><p class="alpdt-field-help"><?php esc_html_e( 'Select any combination. Unselected larger units are included in the next unit you choose. For example: months and days, or days and hours.', 'alp-dynamic-time-countdowns' ); ?></p><div class="alpdt-clock-units">
                  <?php foreach ( array( 'years' => __( 'Years', 'alp-dynamic-time-countdowns' ), 'months' => __( 'Months', 'alp-dynamic-time-countdowns' ), 'days' => __( 'Days', 'alp-dynamic-time-countdowns' ), 'hours' => __( 'Hours', 'alp-dynamic-time-countdowns' ), 'minutes' => __( 'Minutes', 'alp-dynamic-time-countdowns' ), 'seconds' => __( 'Seconds', 'alp-dynamic-time-countdowns' ) ) as $key => $title ) : ?>
                  <label class="alpdt-check"><input type="checkbox" data-alpdt-clock-unit="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, array( 'days', 'hours', 'minutes', 'seconds' ), true ) ); ?>/><span><?php echo esc_html( $title ); ?></span></label>
                  <?php endforeach; ?>
                </div></fieldset>
                  </section>
                  <section class="alpdt-clock-panel" role="tabpanel" tabindex="0" id="alpdt-clock-panel-style" aria-labelledby="alpdt-clock-tab-style" hidden>
                <div class="alpdt-field" data-alpdt-field="clock_style"><label for="alpdt-clock-style" class="alpdt-clock-style-label"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 6.5V12l3.6 2.1"/></svg><span><?php esc_html_e( 'Clock appearance', 'alp-dynamic-time-countdowns' ); ?></span></label><select id="alpdt-clock-style"><option value="simple"><?php esc_html_e( 'Minimal, inline text', 'alp-dynamic-time-countdowns' ); ?></option><option value="glow"><?php esc_html_e( 'Soft glow', 'alp-dynamic-time-countdowns' ); ?></option><option value="flip"><?php esc_html_e( 'Flip digits', 'alp-dynamic-time-countdowns' ); ?></option><option value="slide"><?php esc_html_e( 'Sliding numbers', 'alp-dynamic-time-countdowns' ); ?></option><option value="pulse"><?php esc_html_e( 'Gentle pulse', 'alp-dynamic-time-countdowns' ); ?></option><option value="sweep"><?php esc_html_e( 'Animated underline', 'alp-dynamic-time-countdowns' ); ?></option><option value="ring"><?php esc_html_e( 'Circular progress dial', 'alp-dynamic-time-countdowns' ); ?></option><option value="odometer"><?php esc_html_e( '3D Odometer', 'alp-dynamic-time-countdowns' ); ?></option><option value="neon"><?php esc_html_e( 'Neon Digital', 'alp-dynamic-time-countdowns' ); ?></option><option value="liquid"><?php esc_html_e( 'Liquid', 'alp-dynamic-time-countdowns' ); ?></option><option value="matrix"><?php esc_html_e( 'Pixel Matrix', 'alp-dynamic-time-countdowns' ); ?></option><option value="orbit"><?php esc_html_e( 'Orbit Motion', 'alp-dynamic-time-countdowns' ); ?></option><option value="glitch"><?php esc_html_e( 'Cyber Glitch', 'alp-dynamic-time-countdowns' ); ?></option><option value="morph"><?php esc_html_e( 'Particle Morph', 'alp-dynamic-time-countdowns' ); ?></option><option value="hourglass"><?php esc_html_e( 'Hourglass', 'alp-dynamic-time-countdowns' ); ?></option><option value="snowfall"><?php esc_html_e( 'Snowfall', 'alp-dynamic-time-countdowns' ); ?></option><option value="christmas_lights"><?php esc_html_e( 'Christmas Lights', 'alp-dynamic-time-countdowns' ); ?></option><option value="celebration"><?php esc_html_e( 'Fireworks celebration', 'alp-dynamic-time-countdowns' ); ?></option><option value="confetti"><?php esc_html_e( 'Celebration confetti & streamers', 'alp-dynamic-time-countdowns' ); ?></option><option value="blood"><?php esc_html_e( 'Blood Dripping', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Choose an appearance for your countdown. Use the Layout option below to place the first letter of each unit beside the number while keeping the selected style and animation. Decorative motion pauses off-screen and respects reduced-motion settings.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                <div class="alpdt-field" data-alpdt-field="clock_layout" hidden>
                  <label for="alpdt-clock-layout"><?php esc_html_e( 'Countdown layout', 'alp-dynamic-time-countdowns' ); ?></label>
                  <select id="alpdt-clock-layout">
                    <option value="default"><?php esc_html_e( 'Default', 'alp-dynamic-time-countdowns' ); ?></option>
                    <option value="horizontal"><?php esc_html_e( 'Minimalist / Horizontal', 'alp-dynamic-time-countdowns' ); ?></option>
                  </select>
                  <p class="alpdt-field-help"><?php esc_html_e( 'Shows the initial of each unit to the right of the number, without changing the selected clock animation. Ideal for compact promotional top bars.', 'alp-dynamic-time-countdowns' ); ?></p>
                </div>

                <div class="alpdt-celebration-settings" data-alpdt-field="celebration" hidden>
                  <div class="alpdt-field"><label for="alpdt-celebration-message"><?php esc_html_e( 'Message when countdown ends', 'alp-dynamic-time-countdowns' ); ?></label><input type="text" id="alpdt-celebration-message" maxlength="120" value="" placeholder="<?php echo esc_attr__( 'It’s time to celebrate!', 'alp-dynamic-time-countdowns' ); ?>"/><p class="alpdt-field-help"><?php esc_html_e( 'Shown when the timer reaches zero. Leave empty for the default message.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                  <div class="alpdt-field"><label for="alpdt-celebration-mode"><?php esc_html_e( 'When to play celebration', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-celebration-mode"><option value="end"><?php esc_html_e( 'Once when countdown ends', 'alp-dynamic-time-countdowns' ); ?></option><option value="loop"><?php esc_html_e( 'Loop while counting down', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Loop plays only while the countdown is running and visible, with a pause between bursts. Reduced-motion preferences disable decorative motion.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                  <div class="alpdt-field"><label for="alpdt-celebration-duration"><?php esc_html_e( 'Celebration burst duration', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-celebration-duration"><option value="5" selected><?php esc_html_e( '5 seconds', 'alp-dynamic-time-countdowns' ); ?></option><option value="8"><?php esc_html_e( '8 seconds', 'alp-dynamic-time-countdowns' ); ?></option><option value="10"><?php esc_html_e( '10 seconds', 'alp-dynamic-time-countdowns' ); ?></option></select></div>
                  <fieldset class="alpdt-field alpdt-celebration-palette" data-alpdt-confetti-palette>
                    <legend><?php esc_html_e( 'Confetti colors', 'alp-dynamic-time-countdowns' ); ?></legend>
                    <p class="alpdt-field-help"><?php esc_html_e( 'Customize confetti colors, and use Add confetti color for more variety.', 'alp-dynamic-time-countdowns' ); ?></p>
                    <div class="alpdt-design-grid">
                      <?php foreach ( array( '#FFCC00', '#F33B49', '#053197', '#07B57B' ) as $index => $color ) :
                          $name = 'confetti_' . ( $index + 1 ) . '_color';
                          /* translators: %d: color position in the palette. */
                          $caption = sprintf( __( 'Confetti color %d', 'alp-dynamic-time-countdowns' ), $index + 1 );
                          /* translators: %s: label of the color picker, e.g. Confetti color 1. */
                          $choose_caption = sprintf( __( 'Choose %s', 'alp-dynamic-time-countdowns' ), $caption ); ?>
                        <div class="alpdt-design-field alpdt-design-color">
                          <label for="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>"><?php echo esc_html( $caption ); ?></label>
                          <div class="alpdt-color-controls"><input type="text" maxlength="9" inputmode="text" spellcheck="false" autocomplete="off" pattern="#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?" id="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>" data-alpdt-design="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $color ); ?>" aria-label="<?php echo esc_attr( $caption . ' (HEX)' ); ?>"/><button type="button" class="alpdt-color-swatch" data-alpdt-color-picker="<?php echo esc_attr( $name ); ?>" data-alpdt-color="<?php echo esc_attr( $color ); ?>" aria-label="<?php echo esc_attr( $choose_caption ); ?>" aria-expanded="false" style="--alpdt-swatch:<?php echo esc_attr( $color ); ?>"><span aria-hidden="true"></span></button></div>
                        </div>
                      <?php endforeach; ?>

<?php foreach ( range( 5, 10 ) as $extra_index ) :
  $extra_name = 'confetti_' . $extra_index . '_color';
  $extra_default = array( '#F78AB5', '#7B5DE8', '#FF8844', '#6AC9FF', '#C3FC6D', '#FFFFFF' )[ ( $extra_index - 5 ) % 6 ];
  $extra_label = sprintf( /* translators: %d: palette color position */ __( "Confetti color %d", "alp-dynamic-time-countdowns" ), $extra_index ); ?>
<div class="alpdt-design-field alpdt-design-color" data-alpdt-extra-color-field="confetti:<?php echo esc_attr( (string) $extra_index ); ?>" hidden>
<label for="alpdt-<?php echo esc_attr( str_replace( "_", "-", $extra_name ) ); ?>"><?php echo esc_html( $extra_label ); ?></label>
<div class="alpdt-color-controls"><input type="text" maxlength="9" spellcheck="false" autocomplete="off" id="alpdt-<?php echo esc_attr( str_replace( "_", "-", $extra_name ) ); ?>" data-alpdt-design="<?php echo esc_attr( $extra_name ); ?>" value="<?php echo esc_attr( $extra_default ); ?>" aria-label="<?php echo esc_attr( $extra_label . " (HEX)" ); ?>"/><button type="button" class="alpdt-color-swatch" data-alpdt-color-picker="<?php echo esc_attr( $extra_name ); ?>" data-alpdt-color="<?php echo esc_attr( $extra_default ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: color name */ __( "Choose %s", "alp-dynamic-time-countdowns" ), $extra_label ) ); ?>" aria-expanded="false" style="--alpdt-swatch:<?php echo esc_attr( $extra_default ); ?>"><span aria-hidden="true"></span></button></div>
</div>
<?php endforeach; ?>
                    </div>
<button type="button" class="button alpdt-palette-add" data-alpdt-add-color="confetti" data-alpdt-max="10"><span aria-hidden="true">+</span> <?php esc_html_e( "Add confetti color", "alp-dynamic-time-countdowns" ); ?></button>
                  </fieldset>
                  <fieldset class="alpdt-field alpdt-celebration-palette" data-alpdt-confetti-palette>
                    <legend><?php esc_html_e( 'Balloon colors', 'alp-dynamic-time-countdowns' ); ?></legend>
                    <p class="alpdt-field-help"><?php esc_html_e( 'Customize balloon colors independently and add more as needed.', 'alp-dynamic-time-countdowns' ); ?></p>
                    <div class="alpdt-design-grid">
                      <?php foreach ( array( '#F78AB5', '#7B5DE8', '#07B57B', '#FFCC00' ) as $index => $color ) :
                          $name = 'balloon_' . ( $index + 1 ) . '_color';
                          /* translators: %d: color position in the palette. */
                          $caption = sprintf( __( 'Balloon color %d', 'alp-dynamic-time-countdowns' ), $index + 1 );
                          /* translators: %s: label of the color picker, e.g. Balloon color 1. */
                          $choose_caption = sprintf( __( 'Choose %s', 'alp-dynamic-time-countdowns' ), $caption ); ?>
                        <div class="alpdt-design-field alpdt-design-color">
                          <label for="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>"><?php echo esc_html( $caption ); ?></label>
                          <div class="alpdt-color-controls"><input type="text" maxlength="9" inputmode="text" spellcheck="false" autocomplete="off" pattern="#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?" id="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>" data-alpdt-design="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $color ); ?>" aria-label="<?php echo esc_attr( $caption . ' (HEX)' ); ?>"/><button type="button" class="alpdt-color-swatch" data-alpdt-color-picker="<?php echo esc_attr( $name ); ?>" data-alpdt-color="<?php echo esc_attr( $color ); ?>" aria-label="<?php echo esc_attr( $choose_caption ); ?>" aria-expanded="false" style="--alpdt-swatch:<?php echo esc_attr( $color ); ?>"><span aria-hidden="true"></span></button></div>
                        </div>
                      <?php endforeach; ?>

<?php foreach ( range( 5, 10 ) as $extra_index ) :
  $extra_name = 'balloon_' . $extra_index . '_color';
  $extra_default = array( '#FF8844', '#47C9F1', '#FA68BE', '#A4E45C', '#E8C3FF', '#FDDC71' )[ ( $extra_index - 5 ) % 6 ];
  $extra_label = sprintf( /* translators: %d: palette color position */ __( "Balloon color %d", "alp-dynamic-time-countdowns" ), $extra_index ); ?>
<div class="alpdt-design-field alpdt-design-color" data-alpdt-extra-color-field="balloon:<?php echo esc_attr( (string) $extra_index ); ?>" hidden>
<label for="alpdt-<?php echo esc_attr( str_replace( "_", "-", $extra_name ) ); ?>"><?php echo esc_html( $extra_label ); ?></label>
<div class="alpdt-color-controls"><input type="text" maxlength="9" spellcheck="false" autocomplete="off" id="alpdt-<?php echo esc_attr( str_replace( "_", "-", $extra_name ) ); ?>" data-alpdt-design="<?php echo esc_attr( $extra_name ); ?>" value="<?php echo esc_attr( $extra_default ); ?>" aria-label="<?php echo esc_attr( $extra_label . " (HEX)" ); ?>"/><button type="button" class="alpdt-color-swatch" data-alpdt-color-picker="<?php echo esc_attr( $extra_name ); ?>" data-alpdt-color="<?php echo esc_attr( $extra_default ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: color name */ __( "Choose %s", "alp-dynamic-time-countdowns" ), $extra_label ) ); ?>" aria-expanded="false" style="--alpdt-swatch:<?php echo esc_attr( $extra_default ); ?>"><span aria-hidden="true"></span></button></div>
</div>
<?php endforeach; ?>
                    </div>
<button type="button" class="button alpdt-palette-add" data-alpdt-add-color="balloon" data-alpdt-max="10"><span aria-hidden="true">+</span> <?php esc_html_e( "Add balloon color", "alp-dynamic-time-countdowns" ); ?></button>
                  </fieldset>
                  <button type="button" class="button alpdt-celebration-preview-button" id="alpdt-celebration-preview"><?php esc_html_e( 'Preview celebration', 'alp-dynamic-time-countdowns' ); ?></button>
                  <p class="alpdt-field-help"><?php esc_html_e( 'Choose when the effect runs. The original completion-only behavior remains the default.', 'alp-dynamic-time-countdowns' ); ?></p>
                </div>
                <div class="alpdt-field alpdt-flip-panel-field" data-alpdt-field="flip_panel_background">
                  <label for="alpdt-panel-color"><?php esc_html_e( 'Clock panel background', 'alp-dynamic-time-countdowns' ); ?></label>
                  <p class="alpdt-field-help"><?php esc_html_e( 'Set the background behind Flip, Fireworks or Confetti. To remove it, choose Transparent below.', 'alp-dynamic-time-countdowns' ); ?></p>
                  <div class="alpdt-color-controls">
                    <input type="text" maxlength="9" pattern="#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?" id="alpdt-panel-color" value="#0F0F0F" data-alpdt-design="panel_color" spellcheck="false" autocomplete="off" aria-label="<?php echo esc_attr__( 'Clock panel background (HEX)', 'alp-dynamic-time-countdowns' ); ?>" />
                    <button type="button" class="alpdt-color-swatch" data-alpdt-color-picker="panel_color" data-alpdt-color="#0f0f0f" aria-label="<?php echo esc_attr__( 'Choose clock panel background', 'alp-dynamic-time-countdowns' ); ?>" aria-expanded="false" style="--alpdt-swatch:#0f0f0f"><span aria-hidden="true"></span></button>
                  </div>
                  <label class="alpdt-panel-clear"><input type="checkbox" id="alpdt-panel-transparent" /> <span><?php esc_html_e( 'Transparent / remove panel background', 'alp-dynamic-time-countdowns' ); ?></span></label>
                </div>
                <fieldset class="alpdt-field alpdt-appearance" data-alpdt-field="clock_design"><legend><?php esc_html_e( 'Clock colors, size and typography', 'alp-dynamic-time-countdowns' ); ?></legend>
                  <p class="alpdt-field-help"><?php esc_html_e( 'Only controls relevant to the selected clock appearance are shown. Blank spacing retains its default.', 'alp-dynamic-time-countdowns' ); ?></p>
                  <div class="alpdt-design-sections">
                    <section class="alpdt-design-section" data-alpdt-design-section="colors" aria-labelledby="alpdt-design-title-colors">
                      <div class="alpdt-design-section-head"><h4 id="alpdt-design-title-colors"><?php esc_html_e( 'Colors', 'alp-dynamic-time-countdowns' ); ?></h4><p><?php esc_html_e( 'Customize numbers, labels and surfaces.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                      <div class="alpdt-design-grid">
                    <?php foreach ( array(
                      'digit_color' => array( __( 'Number color', 'alp-dynamic-time-countdowns' ), '#de4848' ),
                      'label_color' => array( __( 'Label color', 'alp-dynamic-time-countdowns' ), '#243665' ),
                      'card_color' => array( __( 'Card / circle background', 'alp-dynamic-time-countdowns' ), '#ffffff' ),
                      'accent_color' => array( __( 'Progress / accent color', 'alp-dynamic-time-countdowns' ), '#2fc5b5' ),
                      'track_color' => array( __( 'Progress track color', 'alp-dynamic-time-countdowns' ), '#e1e8f0' ),
                    ) as $name => $config ) :
                        /* translators: %s: clock color setting label, e.g. Number color. */
                        $choose_color_label = sprintf( __( 'Choose %s', 'alp-dynamic-time-countdowns' ), $config[0] ); ?>
                      <div class="alpdt-design-field alpdt-design-color" data-alpdt-appearance-only="<?php echo esc_attr( $name ); ?>"><label for="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>"><?php echo esc_html( $config[0] ); ?></label><div class="alpdt-color-controls"><input type="text" inputmode="text" spellcheck="false" autocomplete="off" maxlength="9" pattern="#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?" id="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>" data-alpdt-design="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( strtoupper( $config[1] ) ); ?>" aria-label="<?php echo esc_attr( $config[0] . ' (HEX)' ); ?>"/><button type="button" class="alpdt-color-swatch" id="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>-picker" data-alpdt-color-picker="<?php echo esc_attr( $name ); ?>" data-alpdt-color="<?php echo esc_attr( $config[1] ); ?>" aria-label="<?php echo esc_attr( $choose_color_label ); ?>" aria-expanded="false" aria-haspopup="true" style="--alpdt-swatch:<?php echo esc_attr( $config[1] ); ?>"><span aria-hidden="true"></span></button></div></div>
                    <?php endforeach; ?>
                      </div>
                      <div class="alpdt-design-field alpdt-design-color alpdt-design-full" data-alpdt-appearance-only="glow_background_color"><label for="alpdt-glow-background-color"><?php esc_html_e( 'Soft Glow background color', 'alp-dynamic-time-countdowns' ); ?></label><div class="alpdt-color-controls"><input type="text" id="alpdt-glow-background-color" data-alpdt-design="glow_background_color" maxlength="9" spellcheck="false" autocomplete="off" value="" placeholder="#EAF7F8" aria-label="<?php esc_attr_e( 'Soft Glow background color (HEX)', 'alp-dynamic-time-countdowns' ); ?>"/><button type="button" class="alpdt-color-swatch" data-alpdt-color-picker="glow_background_color" data-alpdt-color="#EAF7F8" aria-label="<?php esc_attr_e( 'Choose Soft Glow background', 'alp-dynamic-time-countdowns' ); ?>" aria-expanded="false" style="--alpdt-swatch:#EAF7F8"><span aria-hidden="true"></span></button></div><p class="alpdt-field-help"><?php esc_html_e( 'Leave empty to keep the original pale green-blue glow. Choose a HEX color to replace it.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                    </section>
                    <section class="alpdt-design-section" data-alpdt-design-section="typography" aria-labelledby="alpdt-design-title-typography">
                      <div class="alpdt-design-section-head"><h4 id="alpdt-design-title-typography"><?php esc_html_e( 'Typography', 'alp-dynamic-time-countdowns' ); ?></h4><p><?php esc_html_e( 'Choose a system font or a seasonal display face.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                      <div class="alpdt-design-grid">
                    <?php foreach ( array( 'digit_size' => array( __( 'Number size', 'alp-dynamic-time-countdowns' ), '60', 'alpdt-digit-size' ), 'label_size' => array( __( 'Label size', 'alp-dynamic-time-countdowns' ), '14', 'alpdt-label-size' ) ) as $size_key => $size_config ) : ?>
                    <div class="alpdt-design-field alpdt-font-size-field">
                      <label for="<?php echo esc_attr( $size_config[2] ); ?>"><?php echo esc_html( $size_config[0] ); ?></label>
                      <div class="alpdt-font-size-controls">
                        <input type="text" inputmode="decimal" autocomplete="off" spellcheck="false" maxlength="96" id="<?php echo esc_attr( $size_config[2] ); ?>" data-alpdt-design="<?php echo esc_attr( $size_key ); ?>" value="<?php echo esc_attr( $size_config[1] ); ?>" aria-describedby="<?php echo esc_attr( $size_config[2] ); ?>-help"/>
                        <select id="<?php echo esc_attr( $size_config[2] ); ?>-unit" data-alpdt-font-unit="<?php echo esc_attr( $size_key ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: number size or label size. */ __( '%s unit', 'alp-dynamic-time-countdowns' ), $size_config[0] ) ); ?>">
                          <?php foreach ( array( 'px', 'rem', 'em', '%', 'vw', 'vh', 'clamp' ) as $size_unit ) : ?>
                            <option value="<?php echo esc_attr( $size_unit ); ?>"><?php echo esc_html( 'clamp' === $size_unit ? 'clamp()' : $size_unit ); ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <p class="alpdt-field-help" id="<?php echo esc_attr( $size_config[2] ); ?>-help"><?php esc_html_e( 'Choose a unit or clamp() for fluid sizing, e.g. clamp(20px, 4vw, 72px).', 'alp-dynamic-time-countdowns' ); ?></p>
                    </div>
                    <?php endforeach; ?>
                    <div class="alpdt-design-field alpdt-font-style-field" data-alpdt-appearance-only="clock_weight"><label for="alpdt-clock-weight"><?php esc_html_e( 'Font weight', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-clock-weight" data-alpdt-design="clock_weight"><option value=""><?php esc_html_e( 'Style default', 'alp-dynamic-time-countdowns' ); ?></option><option value="300"><?php esc_html_e( 'Light · 300', 'alp-dynamic-time-countdowns' ); ?></option><option value="400">Regular · 400</option><option value="500">Medium · 500</option><option value="600">Semi-bold · 600</option><option value="700">Bold · 700</option><option value="800">Extra bold · 800</option><option value="900">Black · 900</option></select></div>
                    <div class="alpdt-design-field alpdt-font-style-field" data-alpdt-appearance-only="clock_italic"><label for="alpdt-clock-italic"><?php esc_html_e( 'Font style', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-clock-italic" data-alpdt-design="clock_italic"><option value=""><?php esc_html_e( 'Style default', 'alp-dynamic-time-countdowns' ); ?></option><option value="normal"><?php esc_html_e( 'Normal', 'alp-dynamic-time-countdowns' ); ?></option><option value="italic"><?php esc_html_e( 'Italic', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Decorative fonts may use synthetic bold or italic if the selected face has only one style.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                    <div class="alpdt-design-field alpdt-design-full alpdt-blood-font-field" data-alpdt-blood-font hidden><label for="alpdt-blood-font"><?php esc_html_e( 'Blood Dripping font', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-blood-font" data-alpdt-design="blood_font"><option value="creepster">Creepster · Regular 400</option><option value="nosifer">Nosifer · Regular 400</option></select><p class="alpdt-field-help"><?php esc_html_e( 'These two fonts are exclusive to Blood Dripping.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                    <div class="alpdt-design-field alpdt-font-field"><label for="alpdt-clock-font"><?php esc_html_e( 'Font family', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-clock-font" data-alpdt-design="clock_font">
                      <option value="auto"><?php esc_html_e( 'Style default (recommended)', 'alp-dynamic-time-countdowns' ); ?></option>
                      <option value="system"><?php esc_html_e( 'System · UI sans serif', 'alp-dynamic-time-countdowns' ); ?></option>
                      <option value="arial">System · Arial</option><option value="verdana">System · Verdana</option><option value="georgia">System · Georgia</option><option value="trebuchet">System · Trebuchet MS</option><option value="monospace">System · Monospace</option>
                      <option value="inter">Modern · Inter</option><option value="poppins">Modern · Poppins</option><option value="montserrat">Modern · Montserrat</option><option value="lato">Modern · Lato</option><option value="fredoka">Display · Fredoka</option><option value="orbitron">Display · Orbitron</option>
                      <option value="snowburst">Christmas · Snowburst One</option><option value="almonte_snow">Christmas · Almonte Snow</option><option value="mountains_christmas">Christmas · Mountains of Christmas</option><option value="creepster">Halloween · Creepster</option><option value="nosifer">Halloween · Nosifer</option>
                    </select><p class="alpdt-field-help"><?php esc_html_e( 'Fonts load only when used by a countdown. Special drawing effects may retain their own typography.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                      </div>
                    </section>
                    <section class="alpdt-design-section" data-alpdt-design-section="size" aria-labelledby="alpdt-design-title-size">
                      <div class="alpdt-design-section-head"><h4 id="alpdt-design-title-size"><?php esc_html_e( 'Size & shape', 'alp-dynamic-time-countdowns' ); ?></h4><p><?php esc_html_e( 'Circle dimensions for compatible styles.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                      <div class="alpdt-design-grid">
                    <div class="alpdt-design-field" data-alpdt-appearance-only="clock_size"><label for="alpdt-clock-size"><?php esc_html_e( 'Circle diameter (px)', 'alp-dynamic-time-countdowns' ); ?></label><input type="number" min="90" max="300" step="2" id="alpdt-clock-size" data-alpdt-design="clock_size" value="170"/></div>
                      </div>
                    </section>
                    <section class="alpdt-design-section" data-alpdt-design-section="spacing" aria-labelledby="alpdt-design-title-spacing">
                      <div class="alpdt-design-section-head"><h4 id="alpdt-design-title-spacing"><?php esc_html_e( 'Spacing', 'alp-dynamic-time-countdowns' ); ?></h4><p><?php esc_html_e( 'Adjust number cards, corners and individual sides.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                      <div class="alpdt-design-grid">
                    <div class="alpdt-design-field alpdt-design-full alpdt-clock-width-field">
                      <label for="alpdt-clock-width"><?php esc_html_e( 'Countdown width', 'alp-dynamic-time-countdowns' ); ?></label>
                      <select id="alpdt-clock-width" data-alpdt-design="clock_width">
                        <option value="auto"><?php esc_html_e( 'Automatic (content width)', 'alp-dynamic-time-countdowns' ); ?></option>
                        <option value="full"><?php esc_html_e( 'Full width (100%)', 'alp-dynamic-time-countdowns' ); ?></option>
                      </select>
                      <p class="alpdt-field-help"><?php esc_html_e( 'Expand the countdown to its parent container. Christmas Lights stretch across the available width while numbers and labels remain centered.', 'alp-dynamic-time-countdowns' ); ?></p>
                    </div>
                    <?php foreach ( array(
                      'countdown' => array( __( 'Countdown padding (px)', 'alp-dynamic-time-countdowns' ), __( 'Space around the entire countdown. The number card padding below is separate.', 'alp-dynamic-time-countdowns' ), 'countdown_padding_' ),
                      'card' => array( __( 'Number card padding (px)', 'alp-dynamic-time-countdowns' ), __( 'Inner space between each numeral and the card edges. In Snowfall this replaces the extra-spacing control; number widths remain stable.', 'alp-dynamic-time-countdowns' ), 'clock_padding_' ),
                    ) as $padding_scope => $padding_group ) : ?>
                    <div class="alpdt-design-field alpdt-design-full alpdt-padding-group" data-alpdt-padding-group="<?php echo esc_attr( $padding_scope ); ?>">
                      <div class="alpdt-padding-heading">
                        <span class="alpdt-padding-title"><?php echo esc_html( $padding_group[0] ); ?></span>
                        <p class="alpdt-field-help"><?php echo esc_html( $padding_group[1] ); ?></p>
                      </div>
                      <div class="alpdt-padding-group-grid">
                      <?php foreach ( array(
                        'vertical' => array( 'top' => __( 'Top', 'alp-dynamic-time-countdowns' ), 'bottom' => __( 'Bottom', 'alp-dynamic-time-countdowns' ) ),
                        'horizontal' => array( 'left' => __( 'Left', 'alp-dynamic-time-countdowns' ), 'right' => __( 'Right', 'alp-dynamic-time-countdowns' ) ),
                      ) as $axis => $sides ) : ?>
                        <div class="alpdt-padding-pair-controls">
                          <?php $side_index = 0; foreach ( $sides as $side => $caption ) : ?>
                            <?php if ( $side_index++ ) : ?>
                              <button type="button" class="alpdt-padding-link" data-alpdt-padding-link="<?php echo esc_attr( $padding_scope . ':' . $axis ); ?>" aria-pressed="false" aria-label="<?php echo esc_attr( sprintf( /* translators: 1: countdown or number card; 2: vertical or horizontal */ __( 'Link %1$s %2$s padding values', 'alp-dynamic-time-countdowns' ), $padding_group[0], 'vertical' === $axis ? __( 'vertical', 'alp-dynamic-time-countdowns' ) : __( 'horizontal', 'alp-dynamic-time-countdowns' ) ) ); ?>" title="<?php echo esc_attr__( 'Link or unlink padding values', 'alp-dynamic-time-countdowns' ); ?>">
                                <svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 8H8a4 4 0 0 0 0 8h2"/><path d="M14 8h2a4 4 0 0 1 0 8h-2"/><path d="M9 12h6"/></svg>
                              </button>
                            <?php endif; ?>
                            <div class="alpdt-padding-side"><label for="alpdt-<?php echo esc_attr( $padding_scope . '-padding-' . $side ); ?>"><?php echo esc_html( $caption ); ?></label><input type="number" min="0" max="80" step="1" id="alpdt-<?php echo esc_attr( $padding_scope . '-padding-' . $side ); ?>" data-alpdt-design="<?php echo esc_attr( $padding_group[2] . $side ); ?>" value="" placeholder="0"/></div>
                          <?php endforeach; ?>
                        </div>
                      <?php endforeach; ?>
                      </div>
                    </div>
                    <?php endforeach; ?>
                    <div class="alpdt-design-field alpdt-design-full" data-alpdt-appearance-only="bulb_count"><label for="alpdt-bulb-count"><?php esc_html_e( 'Number of Christmas bulbs', 'alp-dynamic-time-countdowns' ); ?></label><input type="number" min="8" max="60" step="1" id="alpdt-bulb-count" data-alpdt-design="bulb_count" value="" placeholder="15"/><p class="alpdt-field-help"><?php esc_html_e( 'More bulbs reduce the horizontal space between them, including at 100% countdown width. Leave empty for 15 bulbs.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                    <div class="alpdt-design-field alpdt-design-full" data-alpdt-appearance-only="bulb_card_gap"><label for="alpdt-bulb-card-gap"><?php esc_html_e( 'Bulbs to number cards spacing (px)', 'alp-dynamic-time-countdowns' ); ?></label><input type="number" min="0" max="70" step="1" id="alpdt-bulb-card-gap" data-alpdt-design="bulb_card_gap" value="" placeholder="<?php echo esc_attr__( 'Style default', 'alp-dynamic-time-countdowns' ); ?>"/><p class="alpdt-field-help"><?php esc_html_e( 'Christmas Lights only. Set 0 for the tightest vertical gap beneath the bulbs. Leave empty for the original spacing.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                    <input type="hidden" id="alpdt-card-inner-spacing" data-alpdt-design="card_inner_spacing" value=""/>
                    <div class="alpdt-design-field alpdt-design-full" data-alpdt-appearance-only="clock_radius"><label for="alpdt-clock-radius"><?php esc_html_e( 'Countdown border radius (px)', 'alp-dynamic-time-countdowns' ); ?></label><input type="number" min="0" max="80" step="1" id="alpdt-clock-radius" data-alpdt-design="clock_radius" value="" placeholder="<?php echo esc_attr__( 'Default', 'alp-dynamic-time-countdowns' ); ?>"/><p class="alpdt-field-help"><?php esc_html_e( 'Rounds the visible countdown panel or number cards. Leave blank to keep the selected appearance.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                      </div>
                    </section>
                    <section class="alpdt-design-section" data-alpdt-design-section="bulbs" aria-labelledby="alpdt-design-title-bulbs" hidden>
                      <div class="alpdt-design-section-head"><h4 id="alpdt-design-title-bulbs"><?php esc_html_e( 'Christmas bulb colors', 'alp-dynamic-time-countdowns' ); ?></h4><p><?php esc_html_e( 'Five default colors repeat across the strand. Add more colors for extra variety.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                      <div class="alpdt-design-grid">
                    <?php foreach ( array(
                      'christmas_1_color' => array( __( 'Bulb color 1', 'alp-dynamic-time-countdowns' ), '#FF4958' ),
                      'christmas_2_color' => array( __( 'Bulb color 2', 'alp-dynamic-time-countdowns' ), '#21ED8E' ),
                      'christmas_3_color' => array( __( 'Bulb color 3', 'alp-dynamic-time-countdowns' ), '#FFDB52' ),
                      'christmas_4_color' => array( __( 'Bulb color 4', 'alp-dynamic-time-countdowns' ), '#63B5FF' ),
                      'christmas_5_color' => array( __( 'Bulb color 5', 'alp-dynamic-time-countdowns' ), '#ED71F9' ),
                    ) as $name => $config ) :
                        /* translators: %s: Christmas bulb color setting label. */
                        $choose_color_label = sprintf( __( 'Choose %s', 'alp-dynamic-time-countdowns' ), $config[0] ); ?>
                      <div class="alpdt-design-field alpdt-design-color" data-alpdt-appearance-only="<?php echo esc_attr( $name ); ?>"><label for="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>"><?php echo esc_html( $config[0] ); ?></label><div class="alpdt-color-controls"><input type="text" inputmode="text" spellcheck="false" autocomplete="off" maxlength="9" pattern="#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?" id="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>" data-alpdt-design="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( strtoupper( $config[1] ) ); ?>" aria-label="<?php echo esc_attr( $config[0] . ' (HEX)' ); ?>"/><button type="button" class="alpdt-color-swatch" id="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>-picker" data-alpdt-color-picker="<?php echo esc_attr( $name ); ?>" data-alpdt-color="<?php echo esc_attr( $config[1] ); ?>" aria-label="<?php echo esc_attr( $choose_color_label ); ?>" aria-expanded="false" aria-haspopup="true" style="--alpdt-swatch:<?php echo esc_attr( $config[1] ); ?>"><span aria-hidden="true"></span></button></div></div>
                    <?php endforeach; ?>

<?php foreach ( range( 6, 12 ) as $extra_index ) :
  $extra_name = 'christmas_' . $extra_index . '_color';
  $extra_default = array( '#FF8833', '#47DAF1', '#FC76CE', '#A2EC54', '#FFB1B1', '#D7ACFF', '#FFFC8E' )[ ( $extra_index - 6 ) % 7 ];
  $extra_label = sprintf( /* translators: %d: palette color position */ __( "Bulb color %d", "alp-dynamic-time-countdowns" ), $extra_index ); ?>
<div class="alpdt-design-field alpdt-design-color" data-alpdt-extra-color-field="christmas:<?php echo esc_attr( (string) $extra_index ); ?>" hidden>
<label for="alpdt-<?php echo esc_attr( str_replace( "_", "-", $extra_name ) ); ?>"><?php echo esc_html( $extra_label ); ?></label>
<div class="alpdt-color-controls"><input type="text" maxlength="9" spellcheck="false" autocomplete="off" id="alpdt-<?php echo esc_attr( str_replace( "_", "-", $extra_name ) ); ?>" data-alpdt-design="<?php echo esc_attr( $extra_name ); ?>" value="<?php echo esc_attr( $extra_default ); ?>" aria-label="<?php echo esc_attr( $extra_label . " (HEX)" ); ?>"/><button type="button" class="alpdt-color-swatch" data-alpdt-color-picker="<?php echo esc_attr( $extra_name ); ?>" data-alpdt-color="<?php echo esc_attr( $extra_default ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: color name */ __( "Choose %s", "alp-dynamic-time-countdowns" ), $extra_label ) ); ?>" aria-expanded="false" style="--alpdt-swatch:<?php echo esc_attr( $extra_default ); ?>"><span aria-hidden="true"></span></button></div>
</div>
<?php endforeach; ?>
                      </div>
<button type="button" class="button alpdt-palette-add" data-alpdt-add-color="christmas" data-alpdt-max="12"><span aria-hidden="true">+</span> <?php esc_html_e( "Add bulb color", "alp-dynamic-time-countdowns" ); ?></button>
                    </section>

                  </div>
                  <div class="alpdt-reset-row"><button type="button" class="button alpdt-reset-appearance" id="alpdt-reset-appearance"><?php esc_html_e( 'Reset appearance to defaults', 'alp-dynamic-time-countdowns' ); ?></button><p class="alpdt-field-help"><?php esc_html_e( 'Restores the clock colors, sizes and font. Your dates and countdown settings stay unchanged.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                </fieldset>
                  </section>
                  <section class="alpdt-clock-panel" role="tabpanel" tabindex="0" id="alpdt-clock-panel-labels" aria-labelledby="alpdt-clock-tab-labels" hidden>
                <fieldset class="alpdt-field alpdt-clock-labels" data-alpdt-field="clock_labels"><legend><?php esc_html_e( 'Countdown unit labels (optional)', 'alp-dynamic-time-countdowns' ); ?></legend>
                  <p class="alpdt-field-help"><?php esc_html_e( 'Labels follow the WordPress site language automatically. You can replace any label with your own text, in any language. Leave a field empty for the automatic translation.', 'alp-dynamic-time-countdowns' ); ?></p>
                  <div class="alpdt-labels-grid">
                    <?php foreach ( array( 'years' => __( 'Years', 'alp-dynamic-time-countdowns' ), 'months' => __( 'Months', 'alp-dynamic-time-countdowns' ), 'days' => __( 'Days', 'alp-dynamic-time-countdowns' ), 'hours' => __( 'Hours', 'alp-dynamic-time-countdowns' ), 'minutes' => __( 'Minutes', 'alp-dynamic-time-countdowns' ), 'seconds' => __( 'Seconds', 'alp-dynamic-time-countdowns' ) ) as $unit => $label ) : ?>
                      <div class="alpdt-label-override"><label for="alpdt-label-<?php echo esc_attr( $unit ); ?>"><?php echo esc_html( $label ); ?></label><input type="text" maxlength="32" id="alpdt-label-<?php echo esc_attr( $unit ); ?>" data-alpdt-unit-label="<?php echo esc_attr( $unit ); ?>" placeholder="<?php echo esc_attr( $label ); ?>"/></div>
                    <?php endforeach; ?>
                  </div>
                </fieldset>
                  </section>
                </div>
                <div class="alpdt-field" data-alpdt-field="copyright_style"><label for="alpdt-copyright-style"><?php esc_html_e( 'Copyright display', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-copyright-style"><option value="range"><?php esc_html_e( 'Starting year – current year (example: 1999–2026)', 'alp-dynamic-time-countdowns' ); ?></option><option value="current"><?php esc_html_e( 'Current year only (example: 2026)', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Choose the current year for © 2026 or a year range for © 1999–2026. Next year updates automatically.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                <div class="alpdt-field" data-alpdt-field="mode"><label for="alpdt-mode"><?php esc_html_e( 'Year calculation', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-mode"><option value="calendar"><?php esc_html_e( 'Calendar-year difference', 'alp-dynamic-time-countdowns' ); ?></option><option value="anniversary"><?php esc_html_e( 'Completed anniversaries', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Calendar: 2026 minus 2000 = 26. Completed anniversaries: count only birthdays or anniversaries that have already happened.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                <div class="alpdt-field" data-alpdt-field="unit"><label for="alpdt-unit"><?php esc_html_e( 'Unit', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-unit"><option value="years"><?php esc_html_e( 'Years', 'alp-dynamic-time-countdowns' ); ?></option><option value="months"><?php esc_html_e( 'Months', 'alp-dynamic-time-countdowns' ); ?></option><option value="weeks"><?php esc_html_e( 'Weeks', 'alp-dynamic-time-countdowns' ); ?></option><option value="days"><?php esc_html_e( 'Days', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Choose whether to show years of experience, months, weeks or days. Calendar-year mode applies only to years.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                <div class="alpdt-field" data-alpdt-field="offset"><label for="alpdt-offset"><?php esc_html_e( 'Year offset (optional)', 'alp-dynamic-time-countdowns' ); ?></label><input id="alpdt-offset" type="number" value="0" min="-100" max="100" step="1"/><p class="alpdt-field-help"><?php esc_html_e( 'Use zero for the current year, or 1 for next year.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                <div class="alpdt-field" data-alpdt-field="format"><label for="alpdt-format"><?php esc_html_e( 'Date format', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-format"><option value="j F Y"><?php esc_html_e( 'Day month year (25 October 2026)', 'alp-dynamic-time-countdowns' ); ?></option><option value="F j, Y"><?php esc_html_e( 'Month day, year (October 25, 2026)', 'alp-dynamic-time-countdowns' ); ?></option><option value="d/m/Y"><?php esc_html_e( 'Day/month/year (25/10/2026)', 'alp-dynamic-time-countdowns' ); ?></option><option value="m/d/Y"><?php esc_html_e( 'Month/day/year (10/25/2026)', 'alp-dynamic-time-countdowns' ); ?></option><option value="Y-m-d"><?php esc_html_e( 'ISO year-month-day (2026-10-25)', 'alp-dynamic-time-countdowns' ); ?></option><option value="l, j F Y"><?php esc_html_e( 'Weekday, day month year (Sunday, 25 October 2026)', 'alp-dynamic-time-countdowns' ); ?></option><option value="F Y"><?php esc_html_e( 'Month and year (October 2026)', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Choose the way the date is written. The examples show the day, month and year so you can distinguish 15 December from 12 May. Month and weekday names use your website language.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                <fieldset class="alpdt-field alpdt-anniversary-fields" data-alpdt-field="anniversary_messages"><legend><?php esc_html_e( 'Your anniversary messages', 'alp-dynamic-time-countdowns' ); ?></legend>
                  <p class="alpdt-field-help"><?php esc_html_e( 'Enter the full start date (YYYY-MM-DD) to choose the actual anniversary day. Each message is selected automatically before, on or after that date.', 'alp-dynamic-time-countdowns' ); ?></p>
                  <div class="alpdt-anniversary-placeholders" role="group" aria-label="<?php echo esc_attr__( 'Anniversary variables', 'alp-dynamic-time-countdowns' ); ?>">
                    <strong><?php esc_html_e( 'Insert a dynamic variable in your message', 'alp-dynamic-time-countdowns' ); ?></strong>
                    <p class="alpdt-field-help"><?php esc_html_e( 'Use {number} for an ordinal (30.º), {years} for a plain number (30), and {current_year} for the calendar year. Copy a marker into any message. Old {year} markers still work as {years}.', 'alp-dynamic-time-countdowns' ); ?></p>
                    <?php foreach ( array(
                        '{number}' => __( 'Ordinal anniversary, like 30.º in Spanish or 30th in English (same as Anniversary number).', 'alp-dynamic-time-countdowns' ),
                        '{years}' => __( 'Completed anniversary years as a plain number, for example 30.', 'alp-dynamic-time-countdowns' ),
                        '{current_year}' => __( 'Current calendar year, for example 2026.', 'alp-dynamic-time-countdowns' ),
                    ) as $token => $description ) : ?>
                      <div class="alpdt-anniversary-variable">
                        <code><?php echo esc_html( $token ); ?></code><span><?php echo esc_html( $description ); ?></span>
                        <span class="alpdt-copy-action"><button class="button alpdt-anniversary-copy" type="button" data-alpdt-copy-kind="variable" data-alpdt-copy-value="<?php echo esc_attr( $token ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: dynamic variable name. */ __( 'Copy variable %s', 'alp-dynamic-time-countdowns' ), $token ) ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg><?php esc_html_e( 'Copy', 'alp-dynamic-time-countdowns' ); ?></button></span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                  <div class="alpdt-field"><label for="alpdt-message-before"><?php esc_html_e( 'Before the anniversary', 'alp-dynamic-time-countdowns' ); ?></label><input type="text" id="alpdt-message-before" maxlength="240" placeholder="Our {years}th anniversary is coming soon"/><p class="alpdt-field-help"><?php esc_html_e( 'Displayed until the day before the anniversary.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                  <div class="alpdt-field"><label for="alpdt-message-on"><?php esc_html_e( 'On the anniversary', 'alp-dynamic-time-countdowns' ); ?></label><input type="text" id="alpdt-message-on" maxlength="240" placeholder="Today we celebrate {years} years!"/><p class="alpdt-field-help"><?php esc_html_e( 'Displayed on the exact anniversary date.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                  <div class="alpdt-field"><label for="alpdt-message-after"><?php esc_html_e( 'After the anniversary', 'alp-dynamic-time-countdowns' ); ?></label><input type="text" id="alpdt-message-after" maxlength="240" placeholder="Thank you for {years} amazing years"/><p class="alpdt-field-help"><?php esc_html_e( 'Displayed after the anniversary until the next anniversary year.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                </fieldset>
                <div class="alpdt-field" data-alpdt-field="text"><label class="alpdt-check"><input id="alpdt-text" type="checkbox" checked /> <span><?php esc_html_e( 'Include unit names', 'alp-dynamic-time-countdowns' ); ?></span></label><p class="alpdt-field-help"><?php esc_html_e( 'Turn on for “26 years” or off to show only “26”.', 'alp-dynamic-time-countdowns' ); ?></p></div>
              </div>
              <div class="alpdt-card alpdt-preview-card"><div class="alpdt-card-heading"><span class="alpdt-step">2</span><h3><?php esc_html_e( 'Live preview', 'alp-dynamic-time-countdowns' ); ?></h3></div><div class="alpdt-preview-surface"><p id="alpdt-preview" class="alpdt-preview" aria-live="polite">…</p><p class="alpdt-field-help" id="alpdt-preview-caption" aria-live="polite"><?php esc_html_e( 'This is how the dynamic value will appear on your site.', 'alp-dynamic-time-countdowns' ); ?></p></div><div class="alpdt-output"><label for="alpdt-shortcode"><?php esc_html_e( 'Shortcode', 'alp-dynamic-time-countdowns' ); ?></label><textarea id="alpdt-shortcode" rows="3" readonly spellcheck="false"></textarea><div class="alpdt-generator-actions"><button type="button" id="alpdt-copy" class="button button-primary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg><?php esc_html_e( 'Copy shortcode', 'alp-dynamic-time-countdowns' ); ?></button><button type="button" id="alpdt-save-shortcode" class="button alpdt-save-shortcode"><?php esc_html_e( 'Save shortcode', 'alp-dynamic-time-countdowns' ); ?></button><button type="button" id="alpdt-save-as-new" class="button" hidden><?php esc_html_e( 'Save as new', 'alp-dynamic-time-countdowns' ); ?></button></div>
                <p id="alpdt-status" class="alpdt-copy-status" role="status" aria-live="polite"></p></div></div>
            </div>
          </section>
          <section role="tabpanel" tabindex="0" id="alpdt-panel-shortcodes" aria-labelledby="alpdt-tab-shortcodes" class="alpdt-panel" hidden>
            <div class="alpdt-section-intro"><h2><?php esc_html_e( 'Saved shortcodes', 'alp-dynamic-time-countdowns' ); ?></h2><p><?php esc_html_e( 'Save your complete countdowns and dynamic texts with a name. Copy the reusable identifier to update every linked page when you edit its configuration.', 'alp-dynamic-time-countdowns' ); ?></p></div>
            <div class="alpdt-card">
              <?php
              /* translators: %d: number of valid reusable shortcode configurations. */
              $saved_count_label = sprintf( _n( '%d saved shortcode', '%d saved shortcodes', count( $stored_shortcodes ), 'alp-dynamic-time-countdowns' ), count( $stored_shortcodes ) );
              ?>
              <div class="alpdt-list-toolbar"><strong><?php echo esc_html( $saved_count_label ); ?></strong><button type="button" class="button button-primary" data-alpdt-open-generator><?php esc_html_e( 'Create new shortcode', 'alp-dynamic-time-countdowns' ); ?></button></div>
              <?php if ( empty( $stored_shortcodes ) ) : ?>
                <div class="alpdt-empty"><p><?php esc_html_e( 'No saved shortcodes yet. Create one in Generator and select Save shortcode.', 'alp-dynamic-time-countdowns' ); ?></p></div>
              <?php else : ?><div class="alpdt-presets alpdt-shortcodes-unclipped">
              <?php foreach ( $visible_shortcodes as $slug => $item ) :
                  if ( ! is_string( $slug ) || ! is_array( $item ) || ! isset( $item['code'], $item['name'] ) || null === ALPDT_Saved_Shortcodes::parse( $item['code'] ) ) { continue; }
                  $alias_shortcode = '[alpdt_time saved="' . $slug . '"]'; ?>
                <div class="alpdt-preset alpdt-shortcode-item">
                  <div class="alpdt-preset-content"><strong><?php echo esc_html( $item['name'] ); ?></strong><small><?php esc_html_e( 'Reusable shortcode', 'alp-dynamic-time-countdowns' ); ?></small><code><?php echo esc_html( $alias_shortcode ); ?></code>
                    <div class="alpdt-saved-code"><details><summary><?php esc_html_e( 'Show complete configuration', 'alp-dynamic-time-countdowns' ); ?></summary><code><?php echo esc_html( $item['code'] ); ?></code><?php self::copy_button( $item['code'] ); ?></details></div>
                    <?php $stored_attrs = ALPDT_Saved_Shortcodes::parse( $item['code'] ); if ( $stored_attrs && 'countdown' !== $stored_attrs['type'] ) : $seo_name = ALPDT_Integrations::saved_variable_name( $slug ); ?>
                    <details class="alpdt-seo-saved"><summary><?php esc_html_e( 'SEO variables for this text', 'alp-dynamic-time-countdowns' ); ?></summary>
                      <p class="alpdt-field-help"><?php esc_html_e( 'Optional: use these variables in the SEO title or description of the selected plugin.', 'alp-dynamic-time-countdowns' ); ?></p><div class="alpdt-seo-saved-inner"><span>Rank Math</span><code><?php echo esc_html( '%' . $seo_name . '%' ); ?></code><?php self::copy_button( '%' . $seo_name . '%', true ); ?><span>Yoast SEO</span><code><?php echo esc_html( '%%' . $seo_name . '%%' ); ?></code><?php self::copy_button( '%%' . $seo_name . '%%', true ); ?></div>
                    </details><?php endif; ?>
                  </div>
                  <div class="alpdt-preset-actions alpdt-action-toolbar">
                    <?php self::copy_button( $alias_shortcode ); ?>
                    <button class="button" type="button" data-alpdt-edit-shortcode data-slug="<?php echo esc_attr( $slug ); ?>" data-name="<?php echo esc_attr( $item['name'] ); ?>" data-code="<?php echo esc_attr( $item['code'] ); ?>"><?php esc_html_e( 'Edit', 'alp-dynamic-time-countdowns' ); ?></button>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . self::PAGE ) ); ?>" data-alpdt-delete data-alpdt-delete-kind="shortcode"><input type="hidden" name="alpdt_action" value="delete_shortcode"/><input type="hidden" name="alpdt_slug" value="<?php echo esc_attr( $slug ); ?>"/><?php wp_nonce_field( 'alpdt_save', 'alpdt_nonce' ); ?><button class="button alpdt-danger" type="submit"><?php esc_html_e( 'Delete', 'alp-dynamic-time-countdowns' ); ?></button></form>
                  </div>
                </div>
              <?php endforeach; ?></div>
              <?php if ( $shortcode_pages > 1 ) : ?>
                <nav class="alpdt-pagination" aria-label="<?php echo esc_attr__( 'Saved shortcode pages', 'alp-dynamic-time-countdowns' ); ?>">
                  <span class="alpdt-pagination-summary"><?php echo esc_html( sprintf( /* translators: 1: first visible item, 2: last visible item, 3: total saved shortcodes. */ __( 'Showing %1$d–%2$d of %3$d', 'alp-dynamic-time-countdowns' ), ( $shortcode_page - 1 ) * 10 + 1, min( $shortcode_page * 10, $shortcode_count ), $shortcode_count ) ); ?></span>
                  <div class="alpdt-pagination-pages">
                    <?php for ( $page_num = 1; $page_num <= $shortcode_pages; $page_num++ ) :
                      $page_url = add_query_arg( array( 'page' => self::PAGE, 'alpdt_view' => 'shortcodes', 'alpdt_saved_page' => $page_num ), admin_url( 'admin.php' ) ); ?>
                      <a class="button<?php echo $page_num === $shortcode_page ? ' alpdt-current-page' : ''; ?>" href="<?php echo esc_url( $page_url ); ?>" <?php if ( $page_num === $shortcode_page ) : ?>aria-current="page"<?php endif; ?> aria-label="<?php echo esc_attr( sprintf( /* translators: %d: saved shortcode page number. */ __( 'Page %d', 'alp-dynamic-time-countdowns' ), $page_num ) ); ?>"><?php echo esc_html( $page_num ); ?></a>
                    <?php endfor; ?>
                  </div>
                </nav>
              <?php endif; ?>
              <?php endif; ?>
            </div>
          </section>
          <section role="tabpanel" tabindex="0" id="alpdt-panel-dates" aria-labelledby="alpdt-tab-dates" class="alpdt-panel" hidden>
            <div class="alpdt-section-intro"><h2><?php esc_html_e( 'Saved dates', 'alp-dynamic-time-countdowns' ); ?></h2><p><?php esc_html_e( 'Save an important date once, like the founding of your company. Use its shortcode in as many places as you like. If the date changes, edit it here and all those places will use the new date automatically.', 'alp-dynamic-time-countdowns' ); ?></p></div>
            <div class="alpdt-columns">
              <div class="alpdt-card"><div class="alpdt-card-heading"><span class="alpdt-step">1</span><h3 id="alpdt-form-heading"><?php esc_html_e( 'Save reusable date', 'alp-dynamic-time-countdowns' ); ?></h3></div>
                <p id="alpdt-edit-state" class="alpdt-info" hidden><?php esc_html_e( 'Editing a saved date. Its identifier stays the same so your existing shortcodes keep working.', 'alp-dynamic-time-countdowns' ); ?></p>
                <form id="alpdt-milestone-form" method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . self::PAGE ) ); ?>">
                  <?php wp_nonce_field( 'alpdt_save', 'alpdt_nonce' ); ?><input type="hidden" name="alpdt_action" value="save"/><input type="hidden" name="alpdt_original_slug" id="alpdt-original" value=""/>
                  <div class="alpdt-field"><label for="alpdt-slug"><?php esc_html_e( 'Identifier', 'alp-dynamic-time-countdowns' ); ?></label><input required maxlength="55" pattern="[a-z0-9_\-]+" name="alpdt_slug" id="alpdt-slug" aria-describedby="alpdt-slug-help" spellcheck="false" autocapitalize="off" autocomplete="off" placeholder="company-founded"/><p class="alpdt-field-help" id="alpdt-slug-help"><?php esc_html_e( 'Use lowercase letters, numbers, hyphens (-) or underscores (_), without spaces. Example: alex-ingreso. Spaces become hyphens automatically. The identifier cannot be changed after saving.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                  <div class="alpdt-field"><label for="alpdt-label"><?php esc_html_e( 'Label', 'alp-dynamic-time-countdowns' ); ?></label><input required maxlength="100" name="alpdt_label" id="alpdt-label" placeholder="Company founded"/><p class="alpdt-field-help"><?php esc_html_e( 'A friendly name to help you recognize this date in the list.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                  <div class="alpdt-field"><label for="alpdt-date"><?php esc_html_e( 'Date', 'alp-dynamic-time-countdowns' ); ?></label><input required name="alpdt_date" id="alpdt-date" type="date"/><p class="alpdt-field-help"><?php esc_html_e( 'Select the exact date of the event, such as the company founding day.', 'alp-dynamic-time-countdowns' ); ?></p></div>
                  <div class="alpdt-form-actions"><button id="alpdt-save" type="submit" class="button button-primary"><?php esc_html_e( 'Save date', 'alp-dynamic-time-countdowns' ); ?></button><button id="alpdt-cancel-edit" type="button" class="button" hidden><?php esc_html_e( 'Cancel editing', 'alp-dynamic-time-countdowns' ); ?></button></div>
                </form>
              </div>
              <div class="alpdt-card"><div class="alpdt-card-heading"><span class="alpdt-step">2</span><h3><?php esc_html_e( 'Your saved dates', 'alp-dynamic-time-countdowns' ); ?></h3></div>
                <?php if ( empty( $presets ) ) : ?><div class="alpdt-empty"><p><?php esc_html_e( 'You have not saved any dates yet. Add your first date using the form.', 'alp-dynamic-time-countdowns' ); ?></p></div><?php else : ?>
                <?php
                /* translators: %d: number of saved date presets. */
                $date_count_label = sprintf( _n( '%d saved date', '%d saved dates', count( $presets ), 'alp-dynamic-time-countdowns' ), count( $presets ) );
                ?>
                <div class="alpdt-list-toolbar"><span><?php echo esc_html( $date_count_label ); ?></span><a class="button" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'page' => self::PAGE, 'alpdt_export' => 1 ), admin_url( 'admin.php' ) ), 'alpdt_export' ) ); ?>"><?php esc_html_e( 'Export JSON', 'alp-dynamic-time-countdowns' ); ?></a></div>
                <div class="alpdt-presets">
                <?php foreach ( $presets as $slug => $p ) :
                    if ( ! is_array( $p ) || empty( $p['date'] ) ) { continue; }
                    /* translators: %s: identifier of the saved date that can be edited. */
                    $edit_date_label = sprintf( __( 'Edit %s', 'alp-dynamic-time-countdowns' ), $slug );
                    /* translators: %s: identifier of the saved date that can be deleted. */
                    $delete_date_label = sprintf( __( 'Delete %s', 'alp-dynamic-time-countdowns' ), $slug ); ?>
                  <div class="alpdt-preset"><div class="alpdt-preset-content"><strong><?php echo esc_html( isset( $p['label'] ) ? $p['label'] : $slug ); ?></strong><small><?php echo esc_html( $p['date'] ); ?></small><code><?php echo esc_html( '[alpdt_time id="' . $slug . '"]' ); ?></code></div><div class="alpdt-preset-actions"><?php self::copy_button( '[alpdt_time id="' . $slug . '"]' ); ?><button class="button alpdt-edit" type="button" data-alpdt-edit data-slug="<?php echo esc_attr( $slug ); ?>" data-date="<?php echo esc_attr( $p['date'] ); ?>" data-label="<?php echo esc_attr( isset( $p['label'] ) ? $p['label'] : $slug ); ?>" aria-label="<?php echo esc_attr( $edit_date_label ); ?>"><?php esc_html_e( 'Edit', 'alp-dynamic-time-countdowns' ); ?></button><form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . self::PAGE ) ); ?>" data-alpdt-delete><input type="hidden" name="alpdt_action" value="delete"/><input type="hidden" name="alpdt_slug" value="<?php echo esc_attr( $slug ); ?>"/><?php wp_nonce_field( 'alpdt_save', 'alpdt_nonce' ); ?><button class="button alpdt-danger" type="submit" aria-label="<?php echo esc_attr( $delete_date_label ); ?>"><?php esc_html_e( 'Delete', 'alp-dynamic-time-countdowns' ); ?></button></form></div></div>
                <?php endforeach; ?></div><?php endif; ?></div>
            </div>
          </section>
          <section role="tabpanel" tabindex="0" id="alpdt-panel-compat" aria-labelledby="alpdt-tab-compat" class="alpdt-panel" hidden>
            <div class="alpdt-section-intro"><h2><?php esc_html_e( 'Compatibility and migration', 'alp-dynamic-time-countdowns' ); ?></h2><p><?php esc_html_e( 'Keep your existing pages and check how the old shortcodes behave before making any changes.', 'alp-dynamic-time-countdowns' ); ?></p></div>
            <div class="alpdt-columns"><div class="alpdt-card"><h3><?php esc_html_e( 'Years Since - Timeless Texts plugin migration', 'alp-dynamic-time-countdowns' ); ?></h3><p><?php esc_html_e( 'The Years Since - Timeless Texts plugin was removed from the WordPress.org repository. ALP Dynamic Time & Countdowns independently supports its existing shortcode syntax so old pages can continue to work after you deactivate the original plugin. ALP Dynamic Time & Countdowns is an independent product, with no affiliation with, endorsement by, or relationship to the developers of the removed plugin.', 'alp-dynamic-time-countdowns' ); ?></p><p><?php echo $alias ? esc_html__( 'The old shortcode is handled by ALP Dynamic Time & Countdowns.', 'alp-dynamic-time-countdowns' ) : esc_html__( 'Another plugin currently handles the old shortcode.', 'alp-dynamic-time-countdowns' ); ?></p><p><?php esc_html_e( 'Content is never rewritten automatically. Review results before removing the old plugin.', 'alp-dynamic-time-countdowns' ); ?></p></div>
            <div class="alpdt-card"><h3><?php esc_html_e( 'Read-only legacy scan', 'alp-dynamic-time-countdowns' ); ?></h3><p><?php esc_html_e( 'The pages listed below contain shortcodes from the Years Since - Timeless Texts plugin. This scan only finds references and never edits your content.', 'alp-dynamic-time-countdowns' ); ?></p><?php self::scan(); ?></div></div>
            <div class="alpdt-card"><h3><?php esc_html_e( 'Runtime notes', 'alp-dynamic-time-countdowns' ); ?></h3><p><?php echo esc_html( 'PHP ' . PHP_VERSION . ' · WordPress ' . get_bloginfo( 'version' ) . ' · ' . wp_timezone_string() ); ?></p><p><?php esc_html_e( 'SEO filters: Yoast, Rank Math, SEOPress. Divi 4/5: inline shortcode in supported render contexts; Divi 5 standalone module is an experimental integration until verified on a live builder.', 'alp-dynamic-time-countdowns' ); ?></p><p><?php esc_html_e( 'For year/anniversary rollover, configure page-cache expiration or use live="true" for visible numbers. This does not guarantee refreshed SEO meta cached by a CDN.', 'alp-dynamic-time-countdowns' ); ?></p></div>
            <div class="alpdt-card alpdt-php-note" role="note"><h3><?php esc_html_e( 'Recommended PHP security version', 'alp-dynamic-time-countdowns' ); ?></h3><p><strong><?php esc_html_e( 'PHP 8.2 or newer is recommended; use a patched release.', 'alp-dynamic-time-countdowns' ); ?></strong></p><p><?php esc_html_e( 'For PHP2Shell (October 2026), use PHP 8.2.35+, 8.3.36+, 8.4.27+ or 8.5.12+, or a distribution-provided fixed package. PHP 8.2 security support ends on 31 December 2026; consider a supported newer branch. Changing PHP versions is a hosting task, not a plugin setting.', 'alp-dynamic-time-countdowns' ); ?></p><p><a href="https://www.php.net/supported-versions.php" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Check official PHP support dates', 'alp-dynamic-time-countdowns' ); ?></a></p></div>
          </section>
          <div id="alpdt-copy-notice" class="alpdt-copy-notice" role="status" aria-live="polite" aria-atomic="true" hidden>
            <span class="alpdt-copy-notice-mark" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span>
            <span id="alpdt-copy-notice-text"></span>
          </div>
          <dialog id="alpdt-save-shortcode-dialog" class="alpdt-dialog alpdt-save-dialog" aria-labelledby="alpdt-save-shortcode-title" aria-describedby="alpdt-save-shortcode-description">
            <h2 id="alpdt-save-shortcode-title"><?php esc_html_e( 'Save shortcode', 'alp-dynamic-time-countdowns' ); ?></h2>
            <p id="alpdt-save-shortcode-description"><?php esc_html_e( 'Name this configuration to find, copy or edit it later.', 'alp-dynamic-time-countdowns' ); ?></p>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . self::PAGE ) ); ?>" id="alpdt-save-shortcode-form">
              <?php wp_nonce_field( 'alpdt_save', 'alpdt_nonce' ); ?>
              <input type="hidden" name="alpdt_action" value="save_shortcode"/>
              <input type="hidden" name="alpdt_original_slug" id="alpdt-shortcode-original" value=""/>
              <input type="hidden" name="alpdt_code" id="alpdt-shortcode-to-save" value=""/>
              <div class="alpdt-field"><label for="alpdt-shortcode-name"><?php esc_html_e( 'Shortcode name', 'alp-dynamic-time-countdowns' ); ?></label><input required maxlength="100" name="alpdt_name" id="alpdt-shortcode-name" placeholder="New Year countdown"/></div>
              <div class="alpdt-field"><label for="alpdt-shortcode-slug"><?php esc_html_e( 'Identifier', 'alp-dynamic-time-countdowns' ); ?></label><input required maxlength="55" pattern="[a-z0-9][a-z0-9_\-]*" name="alpdt_slug" id="alpdt-shortcode-slug" aria-describedby="alpdt-shortcode-slug-help" spellcheck="false" autocapitalize="off" autocomplete="off" placeholder="new-year-countdown"/><p class="alpdt-field-help" id="alpdt-shortcode-slug-help"><?php esc_html_e( 'Use lowercase letters, numbers, hyphens (-) or underscores (_), without spaces. Example: alex-ingreso. Spaces become hyphens automatically. The identifier cannot be changed after saving.', 'alp-dynamic-time-countdowns' ); ?></p></div>
              <div class="alpdt-dialog-actions"><button class="button" type="button" id="alpdt-save-shortcode-cancel"><?php esc_html_e( 'Cancel', 'alp-dynamic-time-countdowns' ); ?></button><button class="button button-primary" type="submit"><?php esc_html_e( 'Save configuration', 'alp-dynamic-time-countdowns' ); ?></button></div>
            </form>
          </dialog>
          <section role="tabpanel" tabindex="0" id="alpdt-panel-help" aria-labelledby="alpdt-tab-help" class="alpdt-panel" hidden>
            <div class="alpdt-section-intro"><h2><?php esc_html_e( 'About & guide', 'alp-dynamic-time-countdowns' ); ?></h2><p><?php esc_html_e( 'Choose a topic to learn how to use dynamic dates and countdowns.', 'alp-dynamic-time-countdowns' ); ?></p></div>
            <div class="alpdt-guide-tabs" role="tablist" aria-label="<?php echo esc_attr__( 'Guide topics', 'alp-dynamic-time-countdowns' ); ?>">
              <button type="button" role="tab" id="alpdt-guide-tab-how" aria-controls="alpdt-guide-panel-how" aria-selected="true" tabindex="0" data-alpdt-guide-tab="how"><?php esc_html_e( 'How it works', 'alp-dynamic-time-countdowns' ); ?></button>
              <button type="button" role="tab" id="alpdt-guide-tab-examples" aria-controls="alpdt-guide-panel-examples" aria-selected="false" tabindex="-1" data-alpdt-guide-tab="examples"><?php esc_html_e( 'Examples', 'alp-dynamic-time-countdowns' ); ?></button>
              <button type="button" role="tab" id="alpdt-guide-tab-seo" aria-controls="alpdt-guide-panel-seo" aria-selected="false" tabindex="-1" data-alpdt-guide-tab="seo"><?php esc_html_e( 'SEO plugins', 'alp-dynamic-time-countdowns' ); ?></button>
              <button type="button" role="tab" id="alpdt-guide-tab-divi" aria-controls="alpdt-guide-panel-divi" aria-selected="false" tabindex="-1" data-alpdt-guide-tab="divi"><?php esc_html_e( 'Divi', 'alp-dynamic-time-countdowns' ); ?></button>
            </div>
            <section role="tabpanel" tabindex="0" id="alpdt-guide-panel-how" aria-labelledby="alpdt-guide-tab-how" class="alpdt-guide-panel">
            <div class="alpdt-card">
                <h3><?php esc_html_e( 'How it works', 'alp-dynamic-time-countdowns' ); ?></h3>
                <ol>
                  <li><?php esc_html_e( 'Choose what you want to show in Generator, such as the current year, years of experience, or a countdown.', 'alp-dynamic-time-countdowns' ); ?></li>
                  <li><?php esc_html_e( 'Enter the date if needed, and choose a format. The example and shortcode update as you change the options.', 'alp-dynamic-time-countdowns' ); ?></li>
                  <li><?php esc_html_e( 'Copy the shortcode into a WordPress or Divi field that supports shortcodes. The value is calculated when the page is displayed.', 'alp-dynamic-time-countdowns' ); ?></li>
                </ol>
                <p><?php esc_html_e( 'Need the same date in several places? Save it under Saved dates and use its identifier. Editing that date updates every reference.', 'alp-dynamic-time-countdowns' ); ?></p>
              </div>
            </section>
            <section role="tabpanel" tabindex="0" id="alpdt-guide-panel-examples" aria-labelledby="alpdt-guide-tab-examples" class="alpdt-guide-panel" hidden>
            <div class="alpdt-card"><h3><?php esc_html_e( 'Examples', 'alp-dynamic-time-countdowns' ); ?></h3>
              <?php foreach ( array(
                '[alpdt_time type="elapsed" from="2000" mode="calendar" text="true"]',
                '[alpdt_time from="2000-12-15" mode="anniversary" text="false"]',
                '[alpdt_time id="company-founded" mode="calendar"]',
                '[alpdt_time type="countdown" to="2027-01-01T18:30" countdown_display="full"]',
                '[alpdt_time type="copyright" copyright_style="current"]',
                '[alpdt_time type="copyright" from="1999" copyright_style="range"]',
                '[years-since y="2000" m="1" d="1" show="y"]',
              ) as $example ) { self::shortcode_example( $example ); } ?>
              <p><?php esc_html_e( 'Insert inline in content fields that process WordPress shortcodes; Divi and third-party builders must be tested in your installed versions.', 'alp-dynamic-time-countdowns' ); ?></p>
            </div>
            </section>
            <section role="tabpanel" tabindex="0" id="alpdt-guide-panel-seo" aria-labelledby="alpdt-guide-tab-seo" class="alpdt-guide-panel" hidden>
            <div class="alpdt-card">
                <h3><?php esc_html_e( 'Using it with SEO plugins', 'alp-dynamic-time-countdowns' ); ?></h3>
                <p><?php esc_html_e( 'In Rank Math, Yoast SEO or SEOPress, enter an ALP Dynamic Time & Countdowns shortcode inside an SEO title or meta description. The plugin replaces supported shortcodes in the published metadata.', 'alp-dynamic-time-countdowns' ); ?></p>
                <?php self::shortcode_example( 'More than [alpdt_time from="2000" mode="calendar" text="false"] years of experience' ); ?>
                <p><?php esc_html_e( 'For Rank Math and Yoast SEO, copy a registered dynamic variable below. The year and month follow your WordPress site timezone; the month name uses your site language. Each saved date also has its own variable.', 'alp-dynamic-time-countdowns' ); ?></p>
                <div class="alpdt-seo-quick-variables">
                  <div class="alpdt-seo-quick-heading"><strong><?php esc_html_e( 'Dynamic value', 'alp-dynamic-time-countdowns' ); ?></strong><strong>Rank Math</strong><strong>Yoast SEO</strong></div>
                  <div class="alpdt-seo-quick-row"><strong><?php esc_html_e( 'Current year', 'alp-dynamic-time-countdowns' ); ?></strong><div><code>%alpdt_year%</code><?php self::copy_button( '%alpdt_year%', true ); ?></div><div><code>%%alpdt_year%%</code><?php self::copy_button( '%%alpdt_year%%', true ); ?></div></div>
                  <div class="alpdt-seo-quick-row"><strong><?php esc_html_e( 'Current month', 'alp-dynamic-time-countdowns' ); ?></strong><div><code>%alpdt_month%</code><?php self::copy_button( '%alpdt_month%', true ); ?></div><div><code>%%alpdt_month%%</code><?php self::copy_button( '%%alpdt_month%%', true ); ?></div></div>
                </div>
                <p><?php esc_html_e( 'SEOPress: you can use the corresponding text shortcodes if its configured metadata field supports them.', 'alp-dynamic-time-countdowns' ); ?></p>
                <?php self::shortcode_example( '[alpdt_time type="month"]' ); ?>
                <p><?php esc_html_e( 'For a saved date with identifier company-founded, use %alpdt_since_company_founded% in Rank Math or %%alpdt_since_company_founded%% in Yoast. Create the saved date first. SEO editor character counters and previews may not reflect the resolved value; inspect the rendered metadata.', 'alp-dynamic-time-countdowns' ); ?></p>
                <p><?php esc_html_e( 'Saved Shortcodes now also provide native SEO variables for non-countdown text. Save your configuration, then open its SEO variables section in Saved Shortcodes to view and copy the marker for Rank Math or Yoast. Unsaved custom text has no registered SEO variable.', 'alp-dynamic-time-countdowns' ); ?></p>
                <p><?php esc_html_e( 'The SEO editor preview may still display the shortcode. Verify the published page source, including the title and meta description, because editor previews and caches vary. This does not automatically update an article publishing date or guarantee immediate reindexing by search engines.', 'alp-dynamic-time-countdowns' ); ?></p>
              </div>
            </section>
            <section role="tabpanel" tabindex="0" id="alpdt-guide-panel-divi" aria-labelledby="alpdt-guide-tab-divi" class="alpdt-guide-panel" hidden>
            <div class="alpdt-card">
              <h3><?php esc_html_e( 'Dates, countdowns and Divi', 'alp-dynamic-time-countdowns' ); ?></h3>
              <p><?php esc_html_e( 'Dates use YYYY-MM-DD: the first four digits are the year, the middle two are the month, and the final two are the day. For example, 2000-12-15 is 15 December 2000. Countdown targets can additionally include hours and minutes, using your WordPress site timezone.', 'alp-dynamic-time-countdowns' ); ?></p>
              <p><?php esc_html_e( 'Create and save your shortcode in the ALP Dynamic Time & Countdowns dashboard. In Divi 5, select that Saved Shortcode in the simplified ALP Dynamic Time & Countdowns module. Optional Design controls change only that module instance. You can also paste the shortcode into a Divi Code module. Test the Visual Builder and frontend with your installed Divi release.', 'alp-dynamic-time-countdowns' ); ?></p>
            </div>
            </section>
          </section>
          <footer class="alpdt-brand-footer" aria-label="<?php echo esc_attr__( 'ALP Dynamic Time & Countdowns', 'alp-dynamic-time-countdowns' ); ?>">
            <span class="alpdt-footer-product"><img class="alpdt-footer-logo" src="<?php echo esc_url( ALPDT_URL . 'assets/branding/logo-color.svg' ); ?>" alt="<?php echo esc_attr__( 'ALP Dynamic Time & Countdowns', 'alp-dynamic-time-countdowns' ); ?>" width="260" height="38" loading="lazy" decoding="async"></span>
            <span class="alpdt-footer-review"><svg class="alpdt-footer-review-star" viewBox="0 0 24 24" width="19" height="19" aria-hidden="true" focusable="false"><path d="m12 2.6 2.93 6.02 6.65.96-4.8 4.67 1.13 6.63L12 17.75l-5.91 3.13 1.13-6.63-4.8-4.67 6.65-.96z" fill="#F5C542" stroke="#CA8C06" stroke-width="1.1" stroke-linejoin="round"/></svg><span><?php esc_html_e( 'Enjoying the plugin? Help us with a review!', 'alp-dynamic-time-countdowns' ); ?></span></span>
            <span class="alpdt-footer-credit"><span class="alpdt-footer-company"><img class="alpdt-footer-company-logo" src="<?php echo esc_url( ALPDT_URL . 'assets/branding/alp-plugins-logo.svg' ); ?>" alt="<?php echo esc_attr__( 'ALP Plugins', 'alp-dynamic-time-countdowns' ); ?>" width="164" height="28" loading="lazy" decoding="async"><span class="alpdt-visually-hidden"><?php esc_html_e( 'ALP Plugins', 'alp-dynamic-time-countdowns' ); ?></span></span></span>
          </footer>
        </div><?php
    }

    /** Every displayed shortcode gets an ALP copy action, including saved dates and guides. */
    private static function shortcode_example( string $shortcode, bool $seo = false ): void {
        echo '<div class="alpdt-code-row"><code>' . esc_html( $shortcode ) . '</code>';
        self::copy_button( $shortcode, $seo );
        echo '</div>';
    }

    private static function copy_button( string $shortcode, bool $seo = false ): void {
        echo '<span class="alpdt-copy-action"><button type="button" class="button alpdt-copy-mini" data-alpdt-copy-kind="' . ( $seo ? 'variable' : 'shortcode' ) . '" data-alpdt-copy-value="' . esc_attr( $shortcode ) . '"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg>' . ( $seo ? esc_html__( 'Copy SEO variable', 'alp-dynamic-time-countdowns' ) : esc_html__( 'Copy shortcode', 'alp-dynamic-time-countdowns' ) ) . '</button></span>';
    }

    private static function scan(): void {
        global $wpdb;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only selects a read-only, capped page of published content.
        $page = isset( $_GET['alpdt_scan_page'] ) && is_scalar( $_GET['alpdt_scan_page'] ) ? min( 100, max( 1, absint( wp_unslash( (string) $_GET['alpdt_scan_page'] ) ) ) ) : 1;
        $offset = ( $page - 1 ) * 20;
        $needle = '%' . $wpdb->esc_like( '[years-since' ) . '%';
        // Read-only query, bounded to 20 posts/page, no postmeta/secret scanning.
        // The table identifier and all request-derived values use official
        // wpdb placeholders. %i is available since WP 6.2 (requires WP >= 6.5).
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Explicitly read-only, capped, prepared compatibility search; caching could leave the migration scan stale.
        $items = $wpdb->get_results( $wpdb->prepare(
            "SELECT ID, post_title FROM %i WHERE post_content LIKE %s AND post_type NOT IN ('revision','attachment') AND post_status NOT IN ('trash','auto-draft') ORDER BY ID DESC LIMIT %d OFFSET %d",
            $wpdb->posts, $needle, 20, $offset
        ) );
        if ( ! $items ) { echo '<p>' . esc_html__( 'No matching content in this batch.', 'alp-dynamic-time-countdowns' ) . '</p>'; return; }
        echo '<ul class="alpdt-scan">';
        foreach ( $items as $item ) {
            if ( ! current_user_can( 'edit_post', (int) $item->ID ) ) { continue; }
            $edit = get_edit_post_link( (int) $item->ID );
            if ( $edit ) { echo '<li><a href="' . esc_url( $edit ) . '">' . esc_html( $item->post_title ? $item->post_title : '#' . $item->ID ) . '</a></li>'; }
        }
        echo '</ul>';
        if ( count( $items ) === 20 && $page < 100 ) {
            echo '<a class="button" href="' . esc_url( add_query_arg( array( 'page' => self::PAGE, 'alpdt_scan_page' => $page + 1 ), admin_url( 'admin.php' ) ) ) . '#alpdt-panel-compat">' . esc_html__( 'Next batch', 'alp-dynamic-time-countdowns' ) . '</a>';
        }
    }
}

// Privileged AJAX handler: capability and nonce are checked in save_quick_colors().
add_action( 'wp_ajax_alpdt_save_quick_colors', array( 'ALPDT_Admin', 'save_quick_colors' ) );
