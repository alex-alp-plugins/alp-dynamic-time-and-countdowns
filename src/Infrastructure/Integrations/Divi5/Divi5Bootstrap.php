<?php
/**
 * Conditional Divi 5 integration bootstrapper.
 *
 * @package AlpDynamicTime\Infrastructure\Integrations\Divi5
 */

declare(strict_types=1);

namespace AlpDynamicTime\Infrastructure\Integrations\Divi5;

use AlpDynamicTime\Infrastructure\AssetManager;
use AlpDynamicTime\Infrastructure\HookManager;

defined( 'ABSPATH' ) || exit;

/**
 * Wires Divi 5 dependency registration, VB settings, and conditional asset loading.
 */
final class Divi5Bootstrap {

	public function __construct(
		private readonly AssetManager $assets,
		private readonly Divi5DataAdapter $adapter,
	) {}

	/**
	 * Queue Divi 5 hooks via HookManager.
	 */
	public function register_hooks( HookManager $hooks ): void {
		$hooks->add_action( 'divi_module_library_modules_dependency_tree', array( $this, 'register_dependency' ) );
		$hooks->add_filter( 'divi_visual_builder_settings_data', array( $this, 'inject_builder_settings' ), 20, 1 );
		$hooks->add_filter( 'block_type_metadata_settings', array( $this, 'inject_block_metadata_choices' ), 20, 1 );
		$hooks->add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_conditional_frontend_style' ) );
		$hooks->add_action( 'divi_visual_builder_assets_before_enqueue_scripts', array( $this, 'enqueue_visual_builder_assets' ) );
	}

	/**
	 * Register Divi 5 module dependency when Divi 5 is active.
	 *
	 * @param mixed $tree Divi dependency tree.
	 */
	public function register_dependency( mixed $tree ): void {
		if ( ! function_exists( 'et_builder_d5_enabled' ) || ! et_builder_d5_enabled() || ! is_object( $tree ) || ! method_exists( $tree, 'add_dependency' ) ) {
			return;
		}
		if ( class_exists( Divi5Module::class ) ) {
			$tree->add_dependency( new Divi5Module() );
		}
	}

	/**
	 * Expose saved shortcode choices and preview endpoint only to editors in VB.
	 *
	 * @param mixed $settings Builder settings array.
	 * @return mixed
	 */
	public function inject_builder_settings( mixed $settings ): mixed {
		if ( ! is_array( $settings ) || ! class_exists( Divi5Module::class, false ) ) {
			return $settings;
		}
		if ( current_user_can( 'edit_posts' ) ) {
			$settings['alpdtSavedShortcodeChoices'] = $this->adapter->choices();
			$settings['alpdtPreviewEndpoint']       = esc_url_raw( rest_url( 'alpdt/v1/divi-preview' ) );
			$settings['alpdtPreviewNonce']          = wp_create_nonce( 'wp_rest' );
		}
		return $settings;
	}

	/**
	 * Keep server-side block definition options in sync with saved shortcodes.
	 *
	 * @param mixed $settings Block metadata settings.
	 * @return mixed
	 */
	public function inject_block_metadata_choices( mixed $settings ): mixed {
		if ( ! is_array( $settings ) || ( $settings['name'] ?? '' ) !== 'alpdt/dynamic-time' || ! current_user_can( 'edit_posts' ) ) {
			return $settings;
		}
		if ( isset( $settings['attributes']['savedKey']['settings']['innerContent']['item']['component']['props'] ) ) {
			$settings['attributes']['savedKey']['settings']['innerContent']['item']['component']['props']['options'] = array_map(
				static fn( string $label ): array => array( 'label' => $label ),
				$this->adapter->choices()
			);
		}
		return $settings;
	}

	/**
	 * Enqueue Divi 5 module stylesheet in <head> only when the page uses the module or Visual Builder.
	 * Theme Builder layouts are covered on-demand by Divi5Module::render_callback().
	 */
	public function enqueue_conditional_frontend_style(): void {
		if ( ! class_exists( Divi5Module::class, false ) || ! function_exists( 'et_builder_d5_enabled' ) || ! et_builder_d5_enabled() ) {
			return;
		}
		$is_vb     = function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled();
		$has_block = function_exists( 'has_block' ) && has_block( 'alpdt/dynamic-time' );
		if ( $is_vb || $has_block ) {
			$this->assets->enqueue_divi5_local();
		}
	}

	/**
	 * Enqueue Divi 5 Visual Builder React bundle and countdown preview assets.
	 */
	public function enqueue_visual_builder_assets(): void {
		if ( ! class_exists( Divi5Module::class, false ) ||
			! function_exists( 'et_core_is_fb_enabled' ) || ! et_core_is_fb_enabled() ||
			! function_exists( 'et_builder_d5_enabled' ) || ! et_builder_d5_enabled() ||
			! class_exists( '\\ET\\Builder\\VisualBuilder\\Assets\\PackageBuildManager' ) ) {
			return;
		}

		$this->assets->enqueue_countdown();
		$this->assets->enqueue_divi5_local();

		$builder_js = 'divi5/builder' . ALPDT_ASSET_SUFFIX . '.js';
		\ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
			array(
				'name'    => 'alpdt-divi5-builder',
				'version' => $this->assets->asset_version( $builder_js ),
				'script'  => array(
					'src'                => ALPDT_URL . $builder_js,
					'deps'               => array( 'divi-module-library', 'divi-vendor-wp-hooks' ),
					'enqueue_top_window' => false,
					'enqueue_app_window' => true,
					'args'               => array( 'in_footer' => false ),
				),
			)
		);
	}
}
