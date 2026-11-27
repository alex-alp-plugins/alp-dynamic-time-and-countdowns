<?php
/**
 * Centralized asset registration and conditional enqueuing with filemtime cache busting.
 *
 * @package AlpDynamicTime\Infrastructure
 */

declare(strict_types=1);

namespace AlpDynamicTime\Infrastructure;

defined( 'ABSPATH' ) || exit;

/**
 * Registers CSS/JS handles once and enqueues them strictly on demand.
 */
final class AssetManager {

	/**
	 * Compute cache-busting asset version from filemtime, falling back to ALPDT_VERSION.
	 */
	public function asset_version( string $relative_path ): string {
		$full_path = ALPDT_DIR . ltrim( $relative_path, '/' );
		if ( is_readable( $full_path ) ) {
			$mtime = filemtime( $full_path );
			if ( false !== $mtime ) {
				return ALPDT_VERSION . '-' . $mtime;
			}
		}
		return ALPDT_VERSION;
	}

	/**
	 * Register public frontend assets without enqueuing them globally.
	 */
	public function register_frontend_assets(): void {
		$countdown_js  = 'assets/countdown' . ALPDT_ASSET_SUFFIX . '.js';
		$countdown_css = 'assets/countdown' . ALPDT_ASSET_SUFFIX . '.css';
		$live_js       = 'assets/live' . ALPDT_ASSET_SUFFIX . '.js';
		$clock_css     = 'assets/clock-ui-1026.css';

		wp_register_script(
			'alpdt-countdown',
			ALPDT_URL . $countdown_js,
			array(),
			$this->asset_version( $countdown_js ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
		wp_register_style(
			'alpdt-countdown',
			ALPDT_URL . $countdown_css,
			array(),
			$this->asset_version( $countdown_css )
		);
		wp_register_style(
			'alpdt-clock-ui-1026',
			ALPDT_URL . $clock_css,
			array( 'alpdt-countdown' ),
			$this->asset_version( $clock_css )
		);
		wp_register_script(
			'alpdt-live',
			ALPDT_URL . $live_js,
			array(),
			$this->asset_version( $live_js ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		$divi_css = 'divi5/module' . ALPDT_ASSET_SUFFIX . '.css';
		wp_register_style(
			'alpdt-divi5-local',
			ALPDT_URL . $divi_css,
			array(),
			$this->asset_version( $divi_css )
		);
	}

	/**
	 * Enqueue countdown assets on demand when a countdown shortcode/module renders.
	 */
	public function enqueue_countdown(): void {
		if ( ! wp_script_is( 'alpdt-countdown', 'registered' ) ) {
			$this->register_frontend_assets();
		}
		wp_enqueue_script( 'alpdt-countdown' );
		wp_enqueue_style( 'alpdt-countdown' );
		wp_enqueue_style( 'alpdt-clock-ui-1026' );
	}

	/**
	 * Enqueue live refresh script on demand when live="true" is active.
	 */
	public function enqueue_live(): void {
		if ( ! wp_script_is( 'alpdt-live', 'registered' ) ) {
			$this->register_frontend_assets();
		}
		wp_enqueue_script( 'alpdt-live' );
	}

	/**
	 * Enqueue Divi 5 module stylesheet only when needed.
	 */
	public function enqueue_divi5_local(): void {
		if ( ! wp_style_is( 'alpdt-divi5-local', 'registered' ) ) {
			$divi_css = 'divi5/module' . ALPDT_ASSET_SUFFIX . '.css';
			wp_register_style(
				'alpdt-divi5-local',
				ALPDT_URL . $divi_css,
				array(),
				$this->asset_version( $divi_css )
			);
		}
		wp_enqueue_style( 'alpdt-divi5-local' );
	}

	/**
	 * Enqueue tiny admin sidebar icon style.
	 */
	public function enqueue_admin_menu_icon(): void {
		$icon_css = 'assets/admin-menu-icon' . ALPDT_ASSET_SUFFIX . '.css';
		wp_register_style(
			'alpdt-menu-icon',
			ALPDT_URL . $icon_css,
			array(),
			$this->asset_version( $icon_css )
		);
		wp_enqueue_style( 'alpdt-menu-icon' );
	}

	/**
	 * Enqueue admin dashboard styles and scripts strictly on the plugin screen.
	 *
	 * @param array<string,mixed> $i18n Localized configuration payload.
	 */
	public function enqueue_admin_screen( array $i18n ): void {
		$admin_css     = 'assets/admin' . ALPDT_ASSET_SUFFIX . '.css';
		// An independent, uniquely named stylesheet bypasses stale page-optimizer copies
		// of admin.min.css without purging other plugins or the site's caches.
		$picker_css    = 'assets/color-picker-ui-1025.css';
		$admin_js      = 'assets/admin' . ALPDT_ASSET_SUFFIX . '.js';
		$countdown_css = 'assets/countdown' . ALPDT_ASSET_SUFFIX . '.css';
		$countdown_js  = 'assets/countdown' . ALPDT_ASSET_SUFFIX . '.js';

		wp_register_style( 'alpdt-admin', ALPDT_URL . $admin_css, array(), $this->asset_version( $admin_css ) );
		wp_register_style( 'alpdt-countdown', ALPDT_URL . $countdown_css, array(), $this->asset_version( $countdown_css ) );
		$clock_css = 'assets/clock-ui-1026.css';
		wp_register_style( 'alpdt-clock-ui-1026', ALPDT_URL . $clock_css, array( 'alpdt-countdown' ), $this->asset_version( $clock_css ) );
		wp_register_style( 'alpdt-picker-ui-1025', ALPDT_URL . $picker_css, array( 'alpdt-admin', 'alpdt-countdown', 'alpdt-clock-ui-1026' ), $this->asset_version( $picker_css ) );
		wp_register_script( 'alpdt-countdown', ALPDT_URL . $countdown_js, array(), $this->asset_version( $countdown_js ), true );
		wp_register_script( 'alpdt-admin', ALPDT_URL . $admin_js, array( 'alpdt-countdown' ), $this->asset_version( $admin_js ), true );

		wp_enqueue_style( 'alpdt-admin' );
		wp_enqueue_style( 'alpdt-countdown' );
		wp_enqueue_style( 'alpdt-clock-ui-1026' );
		wp_enqueue_style( 'alpdt-picker-ui-1025' );
		wp_enqueue_script( 'alpdt-countdown' );
		wp_enqueue_script( 'alpdt-admin' );

		wp_add_inline_script(
			'alpdt-admin',
			'window.alpdtAdmin=' . wp_json_encode( $i18n, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';',
			'before'
		);
	}

	/**
	 * Register Gutenberg block editor script without loading saved data yet.
	 */
	public function register_block_editor(): void {
		$editor_js = 'blocks/editor' . ALPDT_ASSET_SUFFIX . '.js';
		wp_register_script(
			'alpdt-block-editor',
			ALPDT_URL . $editor_js,
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
			$this->asset_version( $editor_js ),
			true
		);
		wp_set_script_translations( 'alpdt-block-editor', 'alp-dynamic-time-countdowns' );
	}

	/**
	 * Attach block editor data only when the block editor actually loads.
	 *
	 * @param array<string,mixed> $editor_data Serialized block selector data.
	 */
	public function attach_block_editor_data( array $editor_data ): void {
		if ( ! wp_script_is( 'alpdt-block-editor', 'registered' ) ) {
			$this->register_block_editor();
		}
		$icon_json = wp_json_encode( ALPDT_URL . 'assets/branding/isotype-color.svg', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
		$data_json = wp_json_encode( $editor_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
		wp_add_inline_script(
			'alpdt-block-editor',
			'window.alpdtBlockIconUrl=' . $icon_json . ';window.alpdtBlockData=' . $data_json . ';',
			'before'
		);
	}
}
