<?php
/**
 * Divi 5 saved-shortcode module registration and frontend renderer.
 *
 * @package AlpDynamicTime\Infrastructure\Integrations\Divi5
 */

declare(strict_types=1);

namespace AlpDynamicTime\Infrastructure\Integrations\Divi5;

use AlpDynamicTime\Infrastructure\AssetManager;
use AlpDynamicTime\Plugin;

defined( 'ABSPATH' ) || exit;

if ( ! interface_exists( '\\ET\\Builder\\Framework\\DependencyManagement\\Interfaces\\DependencyInterface' ) ||
	! class_exists( '\\ET\\Builder\\Packages\\ModuleLibrary\\ModuleRegistration' ) ) {
	return;
}

/**
 * Registers the alpdt/dynamic-time module with Divi 5 ModuleRegistration.
 */
final class Divi5Module implements \ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface {

	/**
	 * Load hook required by Divi's DependencyInterface.
	 */
	public function load(): void {
		if ( did_action( 'init' ) ) {
			self::register_module();
		} else {
			add_action( 'init', array( __CLASS__, 'register_module' ), 100 );
		}
	}

	/**
	 * Register module metadata directory (divi5/module.json) and render callback.
	 */
	public static function register_module(): void {
		\ET\Builder\Packages\ModuleLibrary\ModuleRegistration::register_module(
			ALPDT_DIR . 'divi5',
			array(
				'render_callback' => array( __CLASS__, 'render_callback' ),
			)
		);
	}

	/**
	 * Emit Divi 5 module styles.
	 *
	 * @param mixed $args Divi render args.
	 */
	public static function module_styles( mixed $args ): void {
		if ( ! is_array( $args ) ) {
			return;
		}
		$elements = $args['elements'] ?? null;
		if ( ! is_object( $elements ) || ! method_exists( $elements, 'style' ) ) {
			return;
		}
		\ET\Builder\FrontEnd\Module\Style::add(
			array(
				'id'            => $args['id'] ?? '',
				'name'          => $args['name'] ?? '',
				'orderIndex'    => $args['orderIndex'] ?? 0,
				'storeInstance' => $args['storeInstance'] ?? '',
				'styles'        => array(
					$elements->style( array( 'attrName' => 'module' ) ),
					$elements->style( array( 'attrName' => 'clockTypography' ) ),
					$elements->style( array( 'attrName' => 'labelTypography' ) ),
				),
			)
		);
	}

	/**
	 * Emit Divi 5 module script data.
	 *
	 * @param mixed $args Divi render args.
	 */
	public static function module_script_data( mixed $args ): void {
		if ( ! is_array( $args ) ) {
			return;
		}
		if ( isset( $args['elements'] ) && is_object( $args['elements'] ) && method_exists( $args['elements'], 'script_data' ) ) {
			$args['elements']->script_data( array( 'attrName' => 'module' ) );
		}
	}

	/**
	 * Emit Divi 5 module classnames.
	 *
	 * @param mixed $args Divi render args.
	 */
	public static function module_classnames( mixed $args ): void {
		if ( ! is_array( $args ) ) {
			return;
		}
		if ( ! isset( $args['classnamesInstance'] ) || ! is_object( $args['classnamesInstance'] ) ) {
			return;
		}
		$attrs = is_array( $args['attrs'] ?? null ) ? $args['attrs'] : array();
		$args['classnamesInstance']->add(
			\ET\Builder\Packages\Module\Options\Element\ElementClassnames::classnames(
				array(
					'attrs' => $attrs['module']['decoration'] ?? array(),
				)
			)
		);
	}

	/**
	 * Render callback invoked by Divi 5 on the frontend.
	 *
	 * @param mixed $attrs    Module attributes.
	 * @param mixed $content  Inner content.
	 * @param mixed $block    Parsed block object.
	 * @param mixed $elements ModuleElements instance.
	 */
	public static function render_callback( mixed $attrs, mixed $content, mixed $block, mixed $elements ): string {
		$attrs = is_array( $attrs ) ? $attrs : array();

		$container = Plugin::instance()->container();
		/** @var AssetManager $assets */
		$assets = $container->get( AssetManager::class );
		/** @var Divi5DataAdapter $adapter */
		$adapter = $container->get( Divi5DataAdapter::class );

		$assets->enqueue_divi5_local();
		$style = $adapter->inline_style( $attrs );
		$html  = '<div class="et_pb_module_inner"><div class="alpdt_d5_value"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>' . $adapter->markup( $attrs ) . '</div></div>';

		$block_data = is_object( $block ) && isset( $block->parsed_block ) && is_array( $block->parsed_block ) ? $block->parsed_block : array();
		$type       = is_object( $block ) && isset( $block->block_type ) && is_object( $block->block_type ) ? $block->block_type : (object) array();

		return \ET\Builder\Packages\Module\Module::render(
			array(
				'orderIndex'          => $block_data['orderIndex'] ?? 0,
				'storeInstance'       => $block_data['storeInstance'] ?? '',
				'attrs'               => $attrs,
				'elements'            => $elements,
				'id'                  => $block_data['id'] ?? '',
				'moduleClassName'     => $adapter->wrapper_classes( $attrs ),
				'name'                => $type->name ?? 'alpdt/dynamic-time',
				'classnamesFunction'  => array( __CLASS__, 'module_classnames' ),
				'moduleCategory'      => $type->category ?? 'module',
				'stylesComponent'     => array( __CLASS__, 'module_styles' ),
				'scriptDataComponent' => array( __CLASS__, 'module_script_data' ),
				'children'            => ( is_object( $elements ) && method_exists( $elements, 'style_components' )
					? $elements->style_components( array( 'attrName' => 'module' ) )
						. $elements->style_components( array( 'attrName' => 'clockTypography' ) )
						. $elements->style_components( array( 'attrName' => 'labelTypography' ) )
					: '' ) . $html,
			)
		);
	}
}
