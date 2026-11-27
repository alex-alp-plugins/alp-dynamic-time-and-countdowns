<?php
/**
 * Administration controller, loaded strictly when is_admin() is true.
 *
 * @package AlpDynamicTime\Presentation\Admin
 */

declare(strict_types=1);

namespace AlpDynamicTime\Presentation\Admin;

use AlpDynamicTime\Domain\QuickColorsValidator;
use AlpDynamicTime\Infrastructure\AssetManager;
use AlpDynamicTime\Infrastructure\HookManager;
use AlpDynamicTime\Infrastructure\Persistence\LegacyScanRepository;
use AlpDynamicTime\Infrastructure\Persistence\PresetRepository;
use AlpDynamicTime\Infrastructure\Persistence\QuickColorsRepository;
use AlpDynamicTime\Infrastructure\Persistence\SavedShortcodeRepository;
use AlpDynamicTime\Presentation\Frontend\ShortcodeController;

defined( 'ABSPATH' ) || exit;

/**
 * Handles wp-admin menu, Settings action link, POST/AJAX handlers, and localized assets.
 */
final class AdminController {

	public const PAGE = 'alpdt-time';

	public function __construct(
		private readonly AssetManager $assets,
		private readonly PresetRepository $presets,
		private readonly SavedShortcodeRepository $saved_shortcodes,
		private readonly QuickColorsRepository $quick_colors,
		private readonly LegacyScanRepository $legacy_scan,
		private readonly AdminView $view,
	) {}

	/**
	 * Queue wp-admin hooks via HookManager.
	 */
	public function register_hooks( HookManager $hooks ): void {
		$hooks->add_action( 'admin_menu', array( $this, 'menu' ) );
		$hooks->add_action( 'admin_init', array( $this, 'redirect_legacy_menu_url' ), 5 );
		$hooks->add_action( 'admin_init', array( $this, 'post_actions' ) );
		$hooks->add_filter( 'plugin_action_links_' . plugin_basename( ALPDT_FILE ), array( $this, 'settings_link' ) );
		$hooks->add_action( 'admin_enqueue_scripts', array( $this, 'menu_icon_assets' ) );
		$hooks->add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		$hooks->add_action( 'wp_ajax_alpdt_save_quick_colors', array( $this, 'save_quick_colors' ) );
	}

	/**
	 * Native Settings action in the WordPress plugins list.
	 *
	 * @param mixed $links Existing action links.
	 * @return array<string,string>
	 */
	public function settings_link( mixed $links ): array {
		$links = is_array( $links ) ? $links : array();
		if ( current_user_can( 'manage_options' ) ) {
			array_unshift(
				$links,
				'<a href="' . esc_url( admin_url( 'admin.php?page=' . self::PAGE ) ) . '">' . esc_html__( 'Settings', 'alp-dynamic-time-countdowns' ) . '</a>'
			);
		}
		return $links;
	}

	/**
	 * Persist quick colors for the current administrator via AJAX.
	 */
	public function save_quick_colors(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to edit Quick Colors.', 'alp-dynamic-time-countdowns' ) ), 403 );
		}
		if ( ! check_ajax_referer( 'alpdt_quick_colors', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Your session has expired. Reload the page and try again.', 'alp-dynamic-time-countdowns' ) ), 403 );
		}
		if ( ! isset( $_POST['colors'] ) || ! is_string( $_POST['colors'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid color palette.', 'alp-dynamic-time-countdowns' ) ), 400 );
		}

		$raw_colors = sanitize_text_field( wp_unslash( $_POST['colors'] ) );
		if ( strlen( $raw_colors ) > 2048 ) {
			wp_send_json_error( array( 'message' => __( 'Invalid color palette.', 'alp-dynamic-time-countdowns' ) ), 400 );
		}

		$decoded   = json_decode( $raw_colors );
		$validator = new QuickColorsValidator();
		$colors    = $validator->validate( $decoded );
		if ( null === $colors ) {
			wp_send_json_error( array( 'message' => __( 'Invalid color palette.', 'alp-dynamic-time-countdowns' ) ), 400 );
		}

		$this->quick_colors->save_for_user( get_current_user_id(), $colors );
		wp_send_json_success( array( 'colors' => $colors ) );
	}

	/**
	 * Register top-level admin menu entry.
	 */
	public function menu(): void {
		add_menu_page(
			__( 'ALP Dynamic Time & Countdowns', 'alp-dynamic-time-countdowns' ),
			__( 'ALP Dynamic Time & CD', 'alp-dynamic-time-countdowns' ),
			'manage_options',
			self::PAGE,
			array( $this, 'screen' ),
			ALPDT_URL . 'assets/branding/isotype-white.svg',
			58
		);
	}

	/**
	 * Redirect saved bookmarks from the old Settings location without adding a second menu entry.
	 */
	public function redirect_legacy_menu_url(): void {
		global $pagenow;
		if ( 'options-general.php' !== $pagenow || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$request_method = isset( $_SERVER['REQUEST_METHOD'] )
			? strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) )
			: '';
		if ( 'GET' !== $request_method ) {
			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only GET navigation redirect; destination verifies nonces before actions.
		if ( ! isset( $_GET['page'] ) || ! is_string( $_GET['page'] ) || self::PAGE !== sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
			return;
		}

		$args = array( 'page' => self::PAGE );
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

	/**
	 * Enqueue tiny sidebar icon stylesheet across wp-admin.
	 */
	public function menu_icon_assets(): void {
		$this->assets->enqueue_admin_menu_icon();
	}

	/**
	 * Enqueue dashboard CSS/JS strictly on the plugin admin screen.
	 *
	 * @param mixed $hook Current admin page hook suffix.
	 */
	public function assets( mixed $hook = '' ): void {
		if ( ! is_string( $hook ) || 'toplevel_page_' . self::PAGE !== $hook ) {
			return;
		}

		$i18n = array(
			'restUrl'                  => esc_url_raw( rest_url( 'alpdt/v1/render' ) ),
			'timezone'                 => wp_timezone()->getName(),
			'copied'                   => __( 'Shortcode copied.', 'alp-dynamic-time-countdowns' ),
			'copyGeneric'              => __( 'Copy shortcode', 'alp-dynamic-time-countdowns' ),
			'seoCopied'                => __( 'SEO variable copied.', 'alp-dynamic-time-countdowns' ),
			'anniversaryVariableCopied' => __( 'Variable copied.', 'alp-dynamic-time-countdowns' ),
			'manageColors'             => __( 'Manage colors', 'alp-dynamic-time-countdowns' ),
			'doneManaging'             => __( 'Done', 'alp-dynamic-time-countdowns' ),
			'addColor'                 => __( 'Add color', 'alp-dynamic-time-countdowns' ),
			'deleteTitle'              => __( 'Delete saved date?', 'alp-dynamic-time-countdowns' ),
			'deleteHelp'               => __( 'This date will be permanently removed. Pages using its identifier will no longer display a value.', 'alp-dynamic-time-countdowns' ),
			'cancel'                   => __( 'Cancel', 'alp-dynamic-time-countdowns' ),
			'delete'                   => __( 'Delete', 'alp-dynamic-time-countdowns' ),
			'copyFallback'             => __( 'Copy was blocked. Select the text and copy it manually.', 'alp-dynamic-time-countdowns' ),
			'previewUnavailable'       => __( 'Preview unavailable. Please check your connection.', 'alp-dynamic-time-countdowns' ),
			'previewEmpty'             => __( 'Enter a valid date to see the result.', 'alp-dynamic-time-countdowns' ),
			'previewPostDateNeeded'    => __( 'Select a sample date to preview the post date. On your site the shortcode automatically uses the actual post date.', 'alp-dynamic-time-countdowns' ),
			'previewOrdinaryCaption'   => __( 'This is how the dynamic value will appear on your site.', 'alp-dynamic-time-countdowns' ),
			'previewAnniversaryBefore' => __( 'Previewing the before-anniversary message. On your site, the date determines which message appears.', 'alp-dynamic-time-countdowns' ),
			'previewAnniversaryOn'     => __( 'Previewing the anniversary-day message. On your site, the date determines which message appears.', 'alp-dynamic-time-countdowns' ),
			'previewAnniversaryAfter'  => __( 'Previewing the after-anniversary message. On your site, the date determines which message appears.', 'alp-dynamic-time-countdowns' ),
			'smartDateHelp'            => __( 'Calendar expressions calculate automatically. For a difference between dates, enter two dates below.', 'alp-dynamic-time-countdowns' ),
			'editDate'                 => __( 'Edit saved date', 'alp-dynamic-time-countdowns' ),
			'saveChanges'              => __( 'Save changes', 'alp-dynamic-time-countdowns' ),
			'saveDate'                 => __( 'Save date', 'alp-dynamic-time-countdowns' ),
			'clockPreview'             => __( 'Live clock', 'alp-dynamic-time-countdowns' ),
			'snowfallBackground'       => __( 'Snowfall background', 'alp-dynamic-time-countdowns' ),
			'christmasBackground'      => __( 'Christmas Lights background', 'alp-dynamic-time-countdowns' ),
			'cardBackground'           => __( 'Card / circle background', 'alp-dynamic-time-countdowns' ),
			'circleDiameter'           => __( 'Circle diameter (px)', 'alp-dynamic-time-countdowns' ),
			'liquidDiameter'           => __( 'Liquid circle diameter (px)', 'alp-dynamic-time-countdowns' ),
			'orbitDiameter'            => __( 'Orbit diameter (px)', 'alp-dynamic-time-countdowns' ),
			'unitLabels'               => array(
				'years'   => __( 'Years', 'alp-dynamic-time-countdowns' ),
				'months'  => __( 'Months', 'alp-dynamic-time-countdowns' ),
				'days'    => __( 'Days', 'alp-dynamic-time-countdowns' ),
				'hours'   => __( 'Hours', 'alp-dynamic-time-countdowns' ),
				'minutes' => __( 'Minutes', 'alp-dynamic-time-countdowns' ),
				'seconds' => __( 'Seconds', 'alp-dynamic-time-countdowns' ),
			),
			'copyrightFromLabel'       => __( 'Copyright starting year', 'alp-dynamic-time-countdowns' ),
			'copyrightFromHelp'        => __( 'Enter the first copyright year, for example 1999. You may also enter a full date in YYYY-MM-DD order: 1999-12-15 means 15 December 1999 (month 12, day 15). The copyright range uses only the year.', 'alp-dynamic-time-countdowns' ),
			'enterYear'                => __( 'Enter a starting year below', 'alp-dynamic-time-countdowns' ),
			'selectOption'             => __( 'Choose an option', 'alp-dynamic-time-countdowns' ),
			'hexValue'                 => __( 'HEX color', 'alp-dynamic-time-countdowns' ),
			'hue'                      => __( 'Hue', 'alp-dynamic-time-countdowns' ),
			'saturationBrightness'     => __( 'Saturation and brightness', 'alp-dynamic-time-countdowns' ),
			'closeColor'               => __( 'Close color picker', 'alp-dynamic-time-countdowns' ),
			'colorPicker'              => __( 'Color Picker', 'alp-dynamic-time-countdowns' ),
			'opacity'                  => __( 'Opacity', 'alp-dynamic-time-countdowns' ),
			'solidColor'               => __( 'Solid', 'alp-dynamic-time-countdowns' ),
			'gradientColor'            => __( 'Gradient', 'alp-dynamic-time-countdowns' ),
			'gradientStart'            => __( 'Start', 'alp-dynamic-time-countdowns' ),
			'gradientEnd'              => __( 'End', 'alp-dynamic-time-countdowns' ),
			'gradientLinear'           => __( 'Linear', 'alp-dynamic-time-countdowns' ),
			'gradientRadial'           => __( 'Radial', 'alp-dynamic-time-countdowns' ),
			'gradientAngle'            => __( 'Angle', 'alp-dynamic-time-countdowns' ),
			'gradientModeLabel'        => __( 'Fill mode', 'alp-dynamic-time-countdowns' ),
			'gradientTypeLabel'        => __( 'Gradient type', 'alp-dynamic-time-countdowns' ),
			'gradientStopsLabel'       => __( 'Gradient stops', 'alp-dynamic-time-countdowns' ),
			'clearBackground'          => __( 'Use style default', 'alp-dynamic-time-countdowns' ),
			'quickColors'              => __( 'Quick colors', 'alp-dynamic-time-countdowns' ),
			'quickColorValues'         => $this->quick_colors->for_user( get_current_user_id() ),
			'quickColorsUrl'           => admin_url( 'admin-ajax.php' ),
			'quickColorsNonce'         => wp_create_nonce( 'alpdt_quick_colors' ),
			'addQuickColor'            => __( 'Add current color to Quick Colors', 'alp-dynamic-time-countdowns' ),
			'addGradient'             => __( 'Add gradient', 'alp-dynamic-time-countdowns' ),
			'addQuickGradient'        => __( 'Save current gradient to Quick Colors', 'alp-dynamic-time-countdowns' ),
			'gradientOnlyField'         => __( 'This saved gradient can only be applied to a background that supports gradients.', 'alp-dynamic-time-countdowns' ),
			/* translators: %s: HEX color value to remove from the Quick Colors list. */
			'removeQuickColor'         => __( 'Remove color %s from Quick Colors', 'alp-dynamic-time-countdowns' ),
			/* translators: %s: Display label of the saved color or gradient preset to remove from Quick Colors. */
			'removeQuickPreset'        => __( 'Remove saved preset %s from Quick Colors', 'alp-dynamic-time-countdowns' ),
			'removeQuickColorTitle'    => __( 'Remove this saved color or gradient?', 'alp-dynamic-time-countdowns' ),
			'removeQuickColorHelp'     => __( 'The color will disappear from your personal Quick Colors palette. It will not change the colors already used in clocks or shortcodes.', 'alp-dynamic-time-countdowns' ),
			'removeQuickColorConfirm'  => __( 'Remove color', 'alp-dynamic-time-countdowns' ),
			'quickColorAdded'          => __( 'Color added to Quick Colors.', 'alp-dynamic-time-countdowns' ),
			'quickGradientAdded'        => __( 'Gradient added to Quick Colors.', 'alp-dynamic-time-countdowns' ),
			'quickColorRemoved'        => __( 'Color removed from Quick Colors.', 'alp-dynamic-time-countdowns' ),
			'quickColorAlreadySaved'   => __( 'This preset is already saved.', 'alp-dynamic-time-countdowns' ),
			'quickColorLimit'          => __( 'You can save up to 24 colors and gradients.', 'alp-dynamic-time-countdowns' ),
			'quickColorFailed'         => __( 'Could not save Quick Colors. Try again.', 'alp-dynamic-time-countdowns' ),
			'quickColorEmpty'          => __( 'No quick colors saved. Use + to add one.', 'alp-dynamic-time-countdowns' ),
			'saveShortcode'            => __( 'Save shortcode', 'alp-dynamic-time-countdowns' ),
			'updateShortcode'          => __( 'Update saved shortcode', 'alp-dynamic-time-countdowns' ),
			'shortcodeNameRequired'    => __( 'Enter a name to save this shortcode.', 'alp-dynamic-time-countdowns' ),
			'shortcodeInvalid'         => __( 'Check the date and options before saving.', 'alp-dynamic-time-countdowns' ),
			'fontSizeInvalid'          => __( 'Enter a valid font size for the selected unit.', 'alp-dynamic-time-countdowns' ),
			'bloodUnitFontHelp'        => __( 'In Blood Dripping, this font applies to the unit labels (Days, Hours, Minutes, Seconds). Numbers keep the Blood Dripping font above.', 'alp-dynamic-time-countdowns' ),
			'deleteShortcodeTitle'     => __( 'Delete saved shortcode?', 'alp-dynamic-time-countdowns' ),
			'deleteShortcodeHelp'      => __( 'Pages using the reusable shortcode identifier will stop displaying a value. A copied full shortcode will keep working.', 'alp-dynamic-time-countdowns' ),
			'shortcodeEdited'          => __( 'Editing saved shortcode', 'alp-dynamic-time-countdowns' ),
			'shortcodeSaveAction'      => __( 'Save configuration', 'alp-dynamic-time-countdowns' ),
			'saveAsNew'                => __( 'Save as new', 'alp-dynamic-time-countdowns' ),
			'confirmDelete'            => __( 'Delete this date', 'alp-dynamic-time-countdowns' ),
			'helpTypes'                => array(
				'elapsed'             => __( 'Shows how much time has passed since a date, such as 26 years of experience.', 'alp-dynamic-time-countdowns' ),
				'year'                => __( 'Shows the current year and updates automatically every January.', 'alp-dynamic-time-countdowns' ),
				'month'               => __( 'Shows the name of the current month in your site language.', 'alp-dynamic-time-countdowns' ),
				'date'                => __( 'Shows today’s date in the format you choose.', 'alp-dynamic-time-countdowns' ),
				'countdown'           => __( 'Count down to an event with an animated live clock or remaining days; you choose its deadline and appearance.', 'alp-dynamic-time-countdowns' ),
				'smart_date'          => __( 'Shows an automatic calendar date: today, a future weekday, a season, two dates compared, or the published/modified date of the current post. Only date comparison needs two entered dates.', 'alp-dynamic-time-countdowns' ),
				'copyright'           => __( 'Choose just the current year or a range from your first year to today, ideal for your website footer.', 'alp-dynamic-time-countdowns' ),
				'ordinal'             => __( 'Shows only the anniversary number with an ordinal suffix, like 30.º or 30th. Ideal inside a headline: Our 30th anniversary.', 'alp-dynamic-time-countdowns' ),
				'anniversary_message' => __( 'Displays one of three custom messages before, on, or after your anniversary date. Insert {number} for 30.º or {years} for 30.', 'alp-dynamic-time-countdowns' ),
			),
		);

		$this->assets->enqueue_admin_screen( $i18n );
	}

	/**
	 * Handle admin POST mutations and JSON export.
	 */
	public function post_actions(): void {
		if ( ! is_admin() || ! isset( $_GET['page'] ) || ! is_string( $_GET['page'] ) || self::PAGE !== sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
			return;
		}

		if ( isset( $_GET['alpdt_export'] ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Access denied.', 'alp-dynamic-time-countdowns' ), '', 403 );
			}
			check_admin_referer( 'alpdt_export' );
			$presets = $this->presets->all();
			nocache_headers();
			header( 'X-Content-Type-Options: nosniff' );
			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename="alpdt-dates.json"' );
			echo wp_json_encode( $presets, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
			exit;
		}

		if ( ! isset( $_POST['alpdt_action'] ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Access denied.', 'alp-dynamic-time-countdowns' ), '', 403 );
		}
		check_admin_referer( 'alpdt_save', 'alpdt_nonce' );
		if ( ! is_string( $_POST['alpdt_action'] ) ) {
			return;
		}

		$action = sanitize_key( wp_unslash( $_POST['alpdt_action'] ) );

		if ( in_array( $action, array( 'save_shortcode', 'delete_shortcode' ), true ) ) {
			$slug = isset( $_POST['alpdt_slug'] ) && is_string( $_POST['alpdt_slug'] ) ? sanitize_key( wp_unslash( $_POST['alpdt_slug'] ) ) : '';
			if ( 'save_shortcode' === $action ) {
				$name     = isset( $_POST['alpdt_name'] ) && is_string( $_POST['alpdt_name'] ) ? sanitize_text_field( wp_unslash( $_POST['alpdt_name'] ) ) : '';
				$code     = isset( $_POST['alpdt_code'] ) && is_string( $_POST['alpdt_code'] ) ? sanitize_text_field( wp_unslash( $_POST['alpdt_code'] ) ) : '';
				$original = isset( $_POST['alpdt_original_slug'] ) && is_string( $_POST['alpdt_original_slug'] ) ? sanitize_key( wp_unslash( $_POST['alpdt_original_slug'] ) ) : '';
				$notice   = $this->saved_shortcodes->save( $slug, $name, $code, $original )
					? ( '' === $original ? 'shortcode_saved' : 'shortcode_updated' )
					: 'shortcode_invalid';
			} else {
				$notice = $this->saved_shortcodes->delete( $slug ) ? 'shortcode_deleted' : 'shortcode_invalid';
			}
			wp_safe_redirect( add_query_arg( array( 'page' => self::PAGE, 'alpdt_message' => $notice, 'alpdt_view' => 'shortcodes' ), admin_url( 'admin.php' ) ) );
			exit;
		}

		$notice = 'invalid';
		if ( 'save' === $action ) {
			$slug     = isset( $_POST['alpdt_slug'] ) && is_string( $_POST['alpdt_slug'] ) ? sanitize_key( wp_unslash( $_POST['alpdt_slug'] ) ) : '';
			$date     = isset( $_POST['alpdt_date'] ) && is_string( $_POST['alpdt_date'] ) ? sanitize_text_field( wp_unslash( $_POST['alpdt_date'] ) ) : '';
			$label    = isset( $_POST['alpdt_label'] ) && is_string( $_POST['alpdt_label'] ) ? sanitize_text_field( wp_unslash( $_POST['alpdt_label'] ) ) : '';
			$original = isset( $_POST['alpdt_original_slug'] ) && is_string( $_POST['alpdt_original_slug'] ) ? sanitize_key( wp_unslash( $_POST['alpdt_original_slug'] ) ) : '';
			$notice   = $this->presets->save( $slug, $date, $label, $original );
		} elseif ( 'delete' === $action ) {
			$slug = isset( $_POST['alpdt_slug'] ) && is_string( $_POST['alpdt_slug'] ) ? sanitize_key( wp_unslash( $_POST['alpdt_slug'] ) ) : '';
			if ( $this->presets->delete( $slug ) ) {
				$notice = 'deleted';
			}
		}

		wp_safe_redirect( add_query_arg( array( 'page' => self::PAGE, 'alpdt_message' => $notice ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Render the plugin administration screen.
	 */
	public function screen(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$presets           = $this->presets->all();
		$stored_shortcodes = $this->saved_shortcodes->all();
		$shortcode_count   = count( $stored_shortcodes );
		$shortcode_pages   = max( 1, (int) ceil( $shortcode_count / 10 ) );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only pagination, not a state change.
		$requested_page     = isset( $_GET['alpdt_saved_page'] ) && is_scalar( $_GET['alpdt_saved_page'] ) ? absint( wp_unslash( $_GET['alpdt_saved_page'] ) ) : 1;
		$shortcode_page     = min( $shortcode_pages, max( 1, $requested_page ) );
		$visible_shortcodes = array_slice( $stored_shortcodes, ( $shortcode_page - 1 ) * 10, 10, true );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only status message; no form data is written here.
		$notice = isset( $_GET['alpdt_message'] ) && is_string( $_GET['alpdt_message'] ) ? sanitize_key( wp_unslash( $_GET['alpdt_message'] ) ) : '';

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only selects a read-only, capped page of published content.
		$scan_page     = isset( $_GET['alpdt_scan_page'] ) && is_scalar( $_GET['alpdt_scan_page'] ) ? min( 100, max( 1, absint( wp_unslash( (string) $_GET['alpdt_scan_page'] ) ) ) ) : 1;
		$scan_items    = $this->legacy_scan->find_legacy_posts( $scan_page );
		$tag_handler   = $GLOBALS['shortcode_tags']['years-since'] ?? null;
		$handles_alias = shortcode_exists( 'years-since' ) && is_array( $tag_handler ) && isset( $tag_handler[0] ) && (
			$tag_handler[0] instanceof ShortcodeController ||
			ShortcodeController::class === $tag_handler[0] ||
			'ALPDT_Shortcodes' === $tag_handler[0]
		);

		$this->view->render(
			array(
				'page'               => self::PAGE,
				'presets'            => $presets,
				'stored_shortcodes'  => $stored_shortcodes,
				'shortcode_count'    => $shortcode_count,
				'shortcode_pages'    => $shortcode_pages,
				'shortcode_page'     => $shortcode_page,
				'visible_shortcodes' => $visible_shortcodes,
				'notice'             => $notice,
				'handles_alias'      => $handles_alias,
				'scan_page'          => $scan_page,
				'scan_items'         => $scan_items,
			)
		);
	}
}
