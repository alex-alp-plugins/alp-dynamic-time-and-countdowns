<?php
/**
 * WordPress REST API endpoints under alpdt/v1.
 *
 * @package AlpDynamicTime\Infrastructure\Rest
 */

declare(strict_types=1);

namespace AlpDynamicTime\Infrastructure\Rest;

use AlpDynamicTime\Domain\DateEngine;
use AlpDynamicTime\Domain\ScheduleCalculator;
use AlpDynamicTime\Infrastructure\Integrations\Divi5\Divi5DataAdapter;
use AlpDynamicTime\Infrastructure\Integrations\Divi5\Divi5Module;

defined( 'ABSPATH' ) || exit;

/**
 * Registers and handles /divi-preview, /next, and /render endpoints with strict schema validation.
 */
final class RestController {

	public function __construct(
		private readonly DateEngine $engine,
		private readonly ScheduleCalculator $schedule,
		private readonly Divi5DataAdapter $divi5_data,
	) {}

	/**
	 * Register REST routes on rest_api_init.
	 */
	public function register(): void {
		if ( class_exists( Divi5Module::class, false ) && function_exists( 'et_builder_d5_enabled' ) && et_builder_d5_enabled() ) {
			register_rest_route(
				'alpdt/v1',
				'/divi-preview',
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'permission_callback' => static fn(): bool => current_user_can( 'edit_posts' ),
					'callback'            => array( $this, 'divi_preview' ),
					'args'                => array(
						'savedKey'         => $this->string_arg( 'sanitize_key', 55 ),
						'localNumberColor' => $this->string_arg( 'sanitize_text_field', 768 ),
						'localLabelColor'  => $this->string_arg( 'sanitize_text_field', 768 ),
						'localAccentColor' => $this->string_arg( 'sanitize_text_field', 768 ),
						'localFontSize'    => $this->string_arg( 'sanitize_text_field', 16 ),
						'localFontFamily'  => $this->enum_arg( array( '', 'system', 'arial', 'verdana', 'georgia', 'trebuchet', 'lato', 'monospace' ), 'sanitize_key' ),
						'localAlignment'   => $this->enum_arg( array( '', 'left', 'center', 'right' ), 'sanitize_key' ),
						'localLabelSize'   => $this->string_arg( 'sanitize_text_field', 16 ),
						'sizingAlignment'  => $this->enum_arg( array( '', 'left', 'center', 'right' ), 'sanitize_key' ),
					),
				)
			);
		}

		register_rest_route(
			'alpdt/v1',
			'/next',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'permission_callback' => '__return_true',
				'callback'            => array( $this, 'next_occurrence' ),
				'args'                => array(
					'recurrence'     => $this->enum_arg( array( 'daily', 'weekly', 'monthly' ), 'sanitize_key' ),
					'repeat_time'    => $this->string_arg( 'sanitize_text_field', 10 ),
					'repeat_weekday' => $this->int_range_arg( 1, 7 ),
					'repeat_day'     => $this->int_range_arg( 1, 31 ),
				),
			)
		);

		register_rest_route(
			'alpdt/v1',
			'/render',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'permission_callback' => '__return_true',
				'callback'            => array( $this, 'render' ),
				'args'                => array(
					'type'              => $this->string_arg( 'sanitize_key', 32 ),
					'id'                => $this->string_arg( 'sanitize_key', 55 ),
					'from'              => $this->string_arg( 'sanitize_text_field', 32 ),
					'to'                => $this->string_arg( 'sanitize_text_field', 32 ),
					'mode'              => $this->enum_arg( array( '', 'calendar', 'anniversary' ), 'sanitize_key' ),
					'unit'              => $this->enum_arg( array( '', 'years', 'months', 'weeks', 'days' ), 'sanitize_key' ),
					'format'            => $this->string_arg( 'sanitize_text_field', 40 ),
					'text'              => $this->string_arg( 'sanitize_text_field', 16 ),
					'offset'            => $this->string_arg( 'sanitize_text_field', 16 ),
					'before'            => $this->string_arg( 'sanitize_text_field', 240 ),
					'on'                => $this->string_arg( 'sanitize_text_field', 240 ),
					'after'             => $this->string_arg( 'sanitize_text_field', 240 ),
					'countdown_display' => $this->enum_arg( array( '', 'days', 'full', 'custom' ), 'sanitize_key' ),
					'clock_units'       => $this->string_arg( 'sanitize_text_field', 80 ),
					'copyright_style'   => $this->enum_arg( array( '', 'range', 'current' ), 'sanitize_key' ),
					'expression'        => $this->string_arg( 'sanitize_key', 40 ),
					'hemisphere'        => $this->enum_arg( array( '', 'north', 'south' ), 'sanitize_key' ),
					'recurrence'        => $this->enum_arg( array( '', 'none', 'daily', 'weekly', 'monthly' ), 'sanitize_key' ),
					'repeat_time'       => $this->string_arg( 'sanitize_text_field', 10 ),
					'repeat_weekday'    => $this->int_range_arg( 1, 7 ),
					'repeat_day'        => $this->int_range_arg( 1, 31 ),
					'preview_message'   => $this->enum_arg( array( '', 'before', 'on', 'after' ), 'sanitize_key' ),
					'preview_date'      => $this->string_arg( 'sanitize_text_field', 10 ),
				),
			)
		);
	}

	/**
	 * Render Divi 5 Visual Builder preview payload for authorized editors.
	 */
	public function divi_preview( \WP_REST_Request $request ): \WP_REST_Response {
		$fields = array(
			'savedKey',
			'localNumberColor',
			'localLabelColor',
			'localAccentColor',
			'localFontSize',
			'localFontFamily',
			'localAlignment',
			'localLabelSize',
			'sizingAlignment',
		);
		$attrs = array();
		foreach ( $fields as $field ) {
			$value = $request->get_param( $field );
			$limit = in_array( $field, array( 'localNumberColor', 'localLabelColor', 'localAccentColor' ), true ) ? 768 : 100;
			if ( is_string( $value ) && strlen( $value ) <= $limit ) {
				$attrs[ $field ] = array(
					'innerContent' => array(
						'desktop' => array(
							'value' => sanitize_text_field( $value ),
						),
					),
				);
			}
		}

		$result = array(
			'html'    => $this->divi5_data->markup( $attrs ),
			'classes' => $this->divi5_data->wrapper_classes( $attrs ),
			'style'   => $this->divi5_data->inline_style( $attrs ),
		);
		$response = new \WP_REST_Response( $result, 200 );
		$response->header( 'Cache-Control', 'private, no-store, max-age=0' );
		return $response;
	}

	/**
	 * Public and read-only: no stored data, user identifiers or secrets are returned.
	 */
	public function next_occurrence( \WP_REST_Request $request ): \WP_REST_Response {
		$raw = array();
		foreach ( array( 'recurrence', 'repeat_time', 'repeat_weekday', 'repeat_day' ) as $key ) {
			$value = $request->get_param( $key );
			if ( is_scalar( $value ) && strlen( (string) $value ) <= 32 ) {
				$raw[ $key ] = (string) $value;
			}
		}
		$next     = $this->schedule->next( $raw );
		$response = new \WP_REST_Response( array( 'target' => $next ? $next->getTimestamp() : 0 ), $next ? 200 : 400 );
		$response->header( 'Cache-Control', 'no-store, max-age=0' );
		return $response;
	}

	/**
	 * Public read-only dynamic text/countdown calculation endpoint.
	 * Admin preview-only overrides require manage_options capability.
	 */
	public function render( \WP_REST_Request $request ): \WP_REST_Response {
		$fields = array(
			'type', 'id', 'from', 'to', 'mode', 'unit', 'format', 'text', 'offset',
			'before', 'on', 'after', 'countdown_display', 'copyright_style',
			'clock_units', 'expression', 'hemisphere', 'recurrence', 'repeat_time',
			'repeat_weekday', 'repeat_day',
		);
		$attrs = array();
		foreach ( $fields as $field ) {
			$value = $request->get_param( $field );
			$limit = in_array( $field, array( 'before', 'on', 'after' ), true ) ? 240 : 80;
			if ( is_scalar( $value ) && strlen( (string) $value ) <= $limit ) {
				$attrs[ $field ] = (string) $value;
			}
		}

		// Preview overrides belong strictly to authenticated administrators in the generator.
		$can_preview = current_user_can( 'manage_options' );
		if ( $can_preview ) {
			$preview_phase = $request->get_param( 'preview_message' );
			if ( 'anniversary_message' === ( $attrs['type'] ?? '' ) && is_string( $preview_phase ) && in_array( $preview_phase, array( 'before', 'on', 'after' ), true ) ) {
				$attrs['_alpdt_preview_phase'] = $preview_phase;
			}
		}

		$result = array( 'value' => $this->engine->render( $attrs ) );

		if ( $can_preview && ( $attrs['type'] ?? '' ) === 'smart_date' && in_array( $attrs['expression'] ?? '', array( 'post_published', 'post_modified' ), true ) ) {
			$sample = $request->get_param( 'preview_date' );
			$date   = is_string( $sample ) && strlen( $sample ) <= 10 ? DateEngine::parse( $sample ) : null;
			$format = $attrs['format'] ?? 'j F Y';
			if ( $date && strlen( $format ) <= 40 && preg_match( '/^[A-Za-z 0-9,.\/\-]+$/D', $format ) ) {
				$result['value'] = wp_date( $format, $date->getTimestamp(), wp_timezone() );
			}
		}

		if ( ( $attrs['type'] ?? '' ) === 'countdown' && in_array( $attrs['countdown_display'] ?? '', array( 'full', 'custom' ), true ) ) {
			$target = ! empty( $attrs['recurrence'] ) && 'none' !== $attrs['recurrence']
				? $this->schedule->next( $attrs )
				: DateEngine::target( $attrs['to'] ?? '' );
			if ( $target ) {
				$result['target'] = $target->getTimestamp();
			}
		}

		$response = new \WP_REST_Response( $result, 200 );
		$response->header( 'Cache-Control', 'no-store, max-age=0' );
		return $response;
	}

	/**
	 * @return array<string,mixed>
	 */
	private function string_arg( string $sanitizer, int $max_len ): array {
		return array(
			'type'              => 'string',
			'sanitize_callback' => $sanitizer,
			'validate_callback' => static fn( mixed $value ): bool => is_scalar( $value ) && strlen( (string) $value ) <= $max_len,
		);
	}

	/**
	 * @param list<string> $allowed Allowed scalar string values.
	 * @return array<string,mixed>
	 */
	private function enum_arg( array $allowed, string $sanitizer ): array {
		return array(
			'type'              => 'string',
			'sanitize_callback' => $sanitizer,
			'validate_callback' => static fn( mixed $value ): bool => is_string( $value ) && in_array( $value, $allowed, true ),
		);
	}

	/**
	 * @return array<string,mixed>
	 */
	private function int_range_arg( int $min, int $max ): array {
		return array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'validate_callback' => static function ( mixed $value ) use ( $min, $max ): bool {
				$parsed = filter_var( $value, FILTER_VALIDATE_INT );
				return false !== $parsed && (int) $parsed >= $min && (int) $parsed <= $max;
			},
		);
	}
}
