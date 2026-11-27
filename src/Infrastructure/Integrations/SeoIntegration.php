<?php
/**
 * Scoped text-only replacements and native variable registration for supported SEO plugins.
 *
 * @package AlpDynamicTime\Infrastructure\Integrations
 */

declare(strict_types=1);

namespace AlpDynamicTime\Infrastructure\Integrations;

use AlpDynamicTime\Domain\DateEngine;
use AlpDynamicTime\Infrastructure\HookManager;
use AlpDynamicTime\Infrastructure\Persistence\PresetRepository;
use AlpDynamicTime\Infrastructure\Persistence\SavedShortcodeRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Integrates dynamic text variables with Yoast SEO, Rank Math, SEOPress, and widgets.
 */
final class SeoIntegration {

	/** @var list<string> */
	private const TEXT_FILTERS = array(
		'wpseo_title',
		'wpseo_metadesc',
		'wpseo_opengraph_title',
		'wpseo_opengraph_desc',
		'wpseo_twitter_title',
		'wpseo_twitter_description',
		'rank_math/frontend/title',
		'rank_math/frontend/description',
		'seopress_titles_title',
		'seopress_titles_desc',
		'widget_text',
		'widget_title',
		'alpdt_dynamic_text',
	);

	public function __construct(
		private readonly DateEngine $engine,
		private readonly PresetRepository $presets,
		private readonly SavedShortcodeRepository $saved_shortcodes,
	) {}

	/**
	 * Queue SEO and widget replacement hooks.
	 */
	public function register_hooks( HookManager $hooks ): void {
		foreach ( self::TEXT_FILTERS as $hook ) {
			$hooks->add_filter( $hook, array( $this, 'replace' ), 20, 1 );
		}
		$hooks->add_action( 'rank_math/vars/register_extra_replacements', array( $this, 'register_rank_math' ) );
		$hooks->add_action( 'wpseo_register_extra_replacements', array( $this, 'register_yoast' ) );
	}

	/**
	 * Stable collision-resistant name for a saved text shortcode.
	 */
	public static function saved_variable_name( string $slug ): string {
		return 'alpdt_saved_' . str_replace( '-', '_', $slug ) . '_' . substr( hash( 'sha256', $slug ), 0, 8 );
	}

	/**
	 * Register variables with Rank Math SEO.
	 */
	public function register_rank_math(): void {
		if ( ! function_exists( 'rank_math_register_var_replacement' ) ) {
			return;
		}
		foreach ( $this->seo_variables() as $name => $config ) {
			rank_math_register_var_replacement(
				$name,
				array(
					'name'        => $config['label'],
					'description' => __( 'Dynamic text calculated by ALP Dynamic Time & Countdowns.', 'alp-dynamic-time-countdowns' ),
					'variable'    => $name,
					'example'     => call_user_func( $config['callback'] ),
				),
				$config['callback']
			);
		}
	}

	/**
	 * Register variables with Yoast SEO.
	 */
	public function register_yoast(): void {
		if ( ! function_exists( 'wpseo_register_var_replacement' ) ) {
			return;
		}
		foreach ( $this->seo_variables() as $name => $config ) {
			wpseo_register_var_replacement( '%%' . $name . '%%', $config['callback'], 'advanced', $config['label'] );
		}
	}

	/**
	 * Replace inline [alpdt_time ...] tokens in text filters safely.
	 *
	 * @param mixed $value Filtered text value.
	 * @return mixed
	 */
	public function replace( mixed $value ): mixed {
		if ( ! is_string( $value ) || strlen( $value ) > 20000 || ! str_contains( $value, '[alpdt_time' ) ) {
			return $value;
		}
		return preg_replace_callback(
			'/\[alpdt_time((?:\s+[^\[\]]{0,600})?)\]/i',
			function ( array $match ): string {
				$attributes = shortcode_parse_atts( $match[1] );
				if ( ! is_array( $attributes ) ) {
					$attributes = array();
				}
				if ( isset( $attributes['saved'] ) ) {
					$attributes = $this->saved_shortcodes->resolve( $attributes['saved'] );
					if ( null === $attributes ) {
						return '';
					}
				}
				return esc_html( sanitize_text_field( $this->engine->render( $attributes ) ) );
			},
			$value
		);
	}

	/**
	 * Build the list of native SEO variable definitions.
	 *
	 * @return array<string,array{label:string,callback:callable():string}>
	 */
	private function seo_variables(): array {
		$vars = array(
			'alpdt_year'  => array(
				'label'    => __( 'ALP Dynamic Time & Countdowns: current year', 'alp-dynamic-time-countdowns' ),
				'callback' => fn(): string => sanitize_text_field( $this->engine->render( array( 'type' => 'year' ) ) ),
			),
			'alpdt_month' => array(
				'label'    => __( 'ALP Dynamic Time & Countdowns: current month', 'alp-dynamic-time-countdowns' ),
				'callback' => fn(): string => sanitize_text_field( $this->engine->render( array( 'type' => 'month' ) ) ),
			),
		);

		$n = 0;
		foreach ( $this->presets->all() as $slug => $preset ) {
			if ( ++$n > 100 ) {
				break;
			}
			if ( ! is_string( $slug ) || ! preg_match( '/^[a-z0-9_-]{1,55}$/D', $slug ) || ! is_array( $preset ) || empty( $preset['date'] ) ) {
				continue;
			}
			$name = 'alpdt_since_' . str_replace( '-', '_', $slug );
			if ( isset( $vars[ $name ] ) ) {
				continue;
			}
			$vars[ $name ] = array(
				/* translators: %s: identifier of saved date. */
				'label'    => sprintf( __( 'ALP Dynamic Time & Countdowns: years since %s', 'alp-dynamic-time-countdowns' ), $slug ),
				'callback' => fn(): string => sanitize_text_field(
					$this->engine->render(
						array(
							'type' => 'elapsed',
							'id'   => $slug,
							'mode' => 'calendar',
							'text' => 'false',
						)
					)
				),
			);
		}

		$n = 0;
		foreach ( $this->saved_shortcodes->all() as $slug => $entry ) {
			if ( ++$n > SavedShortcodeRepository::MAX ) {
				break;
			}
			if ( ! is_string( $slug ) || ! preg_match( '/^[a-z0-9][a-z0-9_-]{0,54}$/D', $slug ) ) {
				continue;
			}
			$attrs = $this->saved_shortcodes->resolve( $slug );
			if ( ! $attrs || 'countdown' === ( $attrs['type'] ?? '' ) ) {
				continue;
			}
			$name          = self::saved_variable_name( $slug );
			$display_name  = sanitize_text_field( is_string( $entry['name'] ) ? $entry['name'] : $slug );
			$vars[ $name ] = array(
				/* translators: %s: saved shortcode name or identifier. */
				'label'    => sprintf( __( 'ALP Dynamic Time & Countdowns: saved text %s', 'alp-dynamic-time-countdowns' ), $display_name ),
				'callback' => function () use ( $slug ): string {
					$config = $this->saved_shortcodes->resolve( $slug );
					if ( ! $config || 'countdown' === ( $config['type'] ?? '' ) ) {
						return '';
					}
					return sanitize_text_field( wp_strip_all_tags( $this->engine->render( $config ) ) );
				},
			);
		}

		return $vars;
	}
}
