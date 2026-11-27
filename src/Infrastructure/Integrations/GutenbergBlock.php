<?php
/**
 * Gutenberg block and Block Bindings integration.
 *
 * @package AlpDynamicTime\Infrastructure\Integrations
 */

declare(strict_types=1);

namespace AlpDynamicTime\Infrastructure\Integrations;

use AlpDynamicTime\Domain\DateEngine;
use AlpDynamicTime\Infrastructure\AssetManager;
use AlpDynamicTime\Infrastructure\Persistence\SavedShortcodeRepository;
use AlpDynamicTime\Presentation\Frontend\ShortcodeController;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the alpdt/time dynamic block and alpdt/date Block Bindings source.
 */
final class GutenbergBlock {

	public function __construct(
		private readonly AssetManager $assets,
		private readonly SavedShortcodeRepository $saved_shortcodes,
		private readonly ShortcodeController $shortcodes,
		private readonly DateEngine $engine,
	) {}

	/**
	 * Register block type and bindings on init without computing editor payloads on frontend requests.
	 */
	public function register(): void {
		$this->assets->register_block_editor();

		register_block_type(
			'alpdt/time',
			array(
				'api_version'     => 2,
				'editor_script'   => 'alpdt-block-editor',
				'render_callback' => array( $this, 'render' ),
				'attributes'      => array(
					'saved' => array(
						'type'    => 'string',
						'default' => '',
					),
				),
			)
		);

		if ( function_exists( 'register_block_bindings_source' ) ) {
			register_block_bindings_source(
				'alpdt/date',
				array(
					'label'              => __( 'ALP Dynamic Time & Countdowns', 'alp-dynamic-time-countdowns' ),
					'get_value_callback' => fn( mixed $args ): string => is_array( $args ) ? $this->engine->render( $args ) : '',
				)
			);
		}
	}

	/**
	 * Populate saved shortcode choices strictly inside the block editor context.
	 */
	public function enqueue_editor_data(): void {
		$choices = array();
		if ( current_user_can( 'edit_posts' ) ) {
			foreach ( $this->saved_shortcodes->all() as $slug => $entry ) {
				$name      = sanitize_text_field( (string) $entry['name'] );
				$choices[] = array(
					'value' => $slug,
					'label' => '' !== $name ? $name : $slug,
				);
			}
		}

		$this->assets->attach_block_editor_data(
			array(
				'savedShortcodes' => $choices,
				'manageUrl'       => current_user_can( 'manage_options' )
					? admin_url( 'admin.php?page=alpdt-time&alpdt_view=shortcodes' )
					: '',
			)
		);
	}

	/**
	 * Server-side render callback for alpdt/time block.
	 *
	 * @param mixed $attrs Block attributes.
	 */
	public function render( mixed $attrs ): string {
		if ( ! is_array( $attrs ) ) {
			return '';
		}
		$saved = $attrs['saved'] ?? '';
		if ( ! is_string( $saved ) || '' === $saved || null === $this->saved_shortcodes->resolve( $saved ) ) {
			return '';
		}
		$output = $this->shortcodes->main( array( 'saved' => $saved ) );
		return '' === $output ? '' : '<div class="alpdt-block">' . $output . '</div>';
	}
}
