<?php
/**
 * Shortcode presentation controller for [alpdt_time], [years-since], and [years-since-gb].
 *
 * @package AlpDynamicTime\Presentation\Frontend
 */

declare(strict_types=1);

namespace AlpDynamicTime\Presentation\Frontend;

use AlpDynamicTime\Domain\ClockRenderer;
use AlpDynamicTime\Domain\DateEngine;
use AlpDynamicTime\Domain\ScheduleCalculator;
use AlpDynamicTime\Infrastructure\AssetManager;
use AlpDynamicTime\Infrastructure\Persistence\SavedShortcodeRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Registers shortcodes and sanitizes rendered HTML via a strict KSES boundary.
 */
final class ShortcodeController {

	public function __construct(
		private readonly DateEngine $engine,
		private readonly ClockRenderer $clock,
		private readonly ScheduleCalculator $schedule,
		private readonly SavedShortcodeRepository $saved_shortcodes,
		private readonly AssetManager $assets,
	) {}

	/**
	 * Register public shortcodes without overwriting existing third-party aliases.
	 */
	public function register(): void {
		if ( ! shortcode_exists( 'alpdt_time' ) ) {
			add_shortcode( 'alpdt_time', array( $this, 'main' ) );
		}
		if ( ! shortcode_exists( 'years-since' ) ) {
			add_shortcode( 'years-since', array( $this, 'legacy' ) );
		}
		if ( ! shortcode_exists( 'years-since-gb' ) ) {
			add_shortcode( 'years-since-gb', array( $this, 'legacy_gb' ) );
		}
	}

	/**
	 * Render [alpdt_time ...] shortcode.
	 *
	 * @param mixed $atts Shortcode attributes.
	 */
	public function main( mixed $atts = array() ): string {
		$atts = is_array( $atts ) ? $atts : array();
		if ( isset( $atts['saved'] ) ) {
			$resolved = $this->saved_shortcodes->resolve( $atts['saved'] );
			if ( null === $resolved ) {
				return '';
			}
			$atts = $resolved;
		}

		$options = shortcode_atts(
			array(
				'type' => '', 'from' => '', 'to' => '', 'id' => '', 'mode' => 'anniversary',
				'unit' => 'years', 'text' => 'true', 'format' => 'j F Y', 'offset' => '0',
				'html' => '', 'live' => 'false', 'before' => '', 'on' => '', 'after' => '',
				'copyright_style' => 'range', 'expression' => 'today', 'hemisphere' => 'north',
				'recurrence' => 'none', 'repeat_time' => '12:00', 'repeat_weekday' => '1', 'repeat_day' => '1',
				'end_action' => 'none', 'end_message' => '', 'end_url' => '',
				'countdown_display' => 'days', 'clock_style' => 'simple', 'clock_layout' => 'default',
				'clock_width' => 'auto', 'clock_units' => 'days,hours,minutes,seconds',
				'celebration_duration' => '5', 'celebration_mode' => 'end', 'celebration_message' => '',
				'digit_color' => '', 'label_color' => '', 'card_color' => '', 'accent_color' => '',
				'track_color' => '', 'panel_color' => '', 'clock_size' => '', 'digit_size' => '',
				'label_size' => '', 'clock_radius' => '', 'clock_radius_top_left' => '', 'clock_radius_top_right' => '', 'clock_radius_bottom_right' => '', 'clock_radius_bottom_left' => '', 'clock_padding' => '', 'clock_padding_mode' => 'global',
				'clock_padding_top' => '', 'clock_padding_right' => '', 'clock_padding_bottom' => '', 'clock_padding_left' => '',
				'countdown_padding_top' => '', 'countdown_padding_right' => '', 'countdown_padding_bottom' => '', 'countdown_padding_left' => '',
				'clock_font' => '', 'clock_weight' => '', 'clock_italic' => '', 'card_inner_spacing' => '',
				'bulb_card_gap' => '', 'bulb_count' => '', 'glow_background_color' => '',
				'bg_gradient_type' => 'none', 'bg_gradient_start' => '', 'bg_gradient_end' => '', 'bg_gradient_angle' => '135', 'bg_gradient_target' => 'container',
				'blood_font' => '', 'lights_mode' => 'multicolor',
				'christmas_1_color' => '', 'christmas_2_color' => '', 'christmas_3_color' => '', 'christmas_4_color' => '',
				'christmas_5_color' => '', 'christmas_6_color' => '', 'christmas_7_color' => '', 'christmas_8_color' => '',
				'christmas_9_color' => '', 'christmas_10_color' => '', 'christmas_11_color' => '', 'christmas_12_color' => '',
				'confetti_1_color' => '', 'confetti_2_color' => '', 'confetti_3_color' => '', 'confetti_4_color' => '',
				'confetti_5_color' => '', 'confetti_6_color' => '', 'confetti_7_color' => '', 'confetti_8_color' => '',
				'confetti_9_color' => '', 'confetti_10_color' => '',
				'balloon_1_color' => '', 'balloon_2_color' => '', 'balloon_3_color' => '', 'balloon_4_color' => '',
				'balloon_5_color' => '', 'balloon_6_color' => '', 'balloon_7_color' => '', 'balloon_8_color' => '',
				'balloon_9_color' => '', 'balloon_10_color' => '',
				'label_years' => '', 'label_months' => '', 'label_days' => '', 'label_hours' => '', 'label_minutes' => '', 'label_seconds' => '',
			),
			$atts,
			'alpdt_time'
		);

		if ( '' === $options['type'] ) {
			$options['type'] = 'elapsed';
		}

		$result = $this->engine->render( $options );
		if ( '' === $result ) {
			return '';
		}

		$escaped             = esc_html( $result );
		$has_countdown_timer = 'countdown' === $options['type'] && (
			in_array( $options['countdown_display'], array( 'full', 'custom' ), true ) ||
			'none' !== $options['recurrence'] ||
			'none' !== $options['end_action']
		);

		if ( $has_countdown_timer ) {
			$target = 'none' !== $options['recurrence']
				? $this->schedule->next( $options )
				: DateEngine::target( (string) $options['to'] );
			if ( ! $target ) {
				return '';
			}

			$this->assets->enqueue_countdown();

			$rich_styles = array(
				'flip', 'ring', 'slide', 'pulse', 'sweep', 'minimalist', 'promo_dark', 'promo_light',
				'promo_outline', 'celebration', 'confetti', 'blood', 'odometer', 'neon', 'liquid',
				'matrix', 'orbit', 'glitch', 'morph', 'hourglass', 'snowfall', 'christmas_lights',
			);
			$needs_clock_markup = 'custom' === $options['countdown_display'] || (
				'full' === $options['countdown_display'] && (
					( 'horizontal' === $options['clock_layout'] && $this->clock->supports_horizontal( (string) $options['clock_style'] ) ) ||
					! empty( $this->clock->appearance( $options ) ) ||
					in_array( $options['clock_style'], $rich_styles, true )
				)
			);

			if ( $needs_clock_markup ) {
				$units   = 'full' === $options['countdown_display'] ? 'days,hours,minutes,seconds' : (string) $options['clock_units'];
				$escaped = $this->clock->markup( $target, $units, (string) $options['clock_style'], null, $options );
			} else {
				$clock_class = 'glow' === $options['clock_style'] ? ' alpdt-countdown-glow' : '';
				$escaped     = '<span class="alpdt-countdown' . $clock_class . '" role="timer" aria-live="off" data-alpdt-display="' . esc_attr( $options['countdown_display'] ) . '" data-alpdt-target="' . esc_attr( (string) $target->getTimestamp() ) . '">' . $escaped . '</span>';
			}

			$extra = '';
			if ( 'none' !== $options['recurrence'] && in_array( $options['recurrence'], array( 'daily', 'weekly', 'monthly' ), true ) ) {
				$extra .= ' data-alpdt-recurrence="' . esc_attr( $options['recurrence'] ) . '" data-alpdt-repeat-time="' . esc_attr( $options['repeat_time'] ) . '" data-alpdt-repeat-weekday="' . esc_attr( $options['repeat_weekday'] ) . '" data-alpdt-repeat-day="' . esc_attr( $options['repeat_day'] ) . '" data-alpdt-next-url="' . esc_url( rest_url( 'alpdt/v1/next' ) ) . '"';
			} else {
				$action = in_array( $options['end_action'], array( 'none', 'message', 'hide', 'redirect' ), true ) ? $options['end_action'] : 'none';
				$path   = $this->schedule->redirect_path( $options['end_url'] );
				if ( 'redirect' === $action && '' === $path ) {
					$action = 'none';
				}
				$extra .= ' data-alpdt-end-action="' . esc_attr( $action ) . '"';
				if ( 'message' === $action ) {
					$message = sanitize_text_field( (string) $options['end_message'] );
					if ( '' === $message ) {
						$message = __( 'Time is up!', 'alp-dynamic-time-countdowns' );
					}
					$message = function_exists( 'mb_substr' ) ? mb_substr( $message, 0, 120 ) : substr( $message, 0, 120 );
					$extra  .= ' data-alpdt-end-message="' . esc_attr( $message ) . '"';
				}
				if ( 'redirect' === $action ) {
					$extra .= ' data-alpdt-end-url="' . esc_attr( $path ) . '"';
				}
			}

			// Use preg_replace_callback so $0/$1/\0 in user end_message never act as PCRE backreferences.
			$escaped = (string) preg_replace_callback(
				'/^<span\b/',
				static fn(): string => '<span' . $extra,
				$escaped,
				1
			);
		}

		$tag = in_array( $options['html'], array( 'span', 'strong', 'em', 'b', 'i', 'small', 'sup' ), true ) ? $options['html'] : '';
		if ( ( 'true' === $options['live'] || '1' === $options['live'] ) &&
			! ( 'smart_date' === $options['type'] && in_array( $options['expression'], array( 'post_published', 'post_modified' ), true ) ) &&
			! $has_countdown_timer ) {
			$this->assets->enqueue_live();
			$safe    = array_intersect_key(
				$options,
				array_flip(
					array(
						'type', 'from', 'to', 'id', 'mode', 'unit', 'text', 'format', 'offset',
						'before', 'on', 'after', 'copyright_style', 'countdown_display', 'clock_units',
						'expression', 'hemisphere',
					)
				)
			);
			$payload = esc_attr( (string) wp_json_encode( $safe ) );
			$escaped = '<span class="alpdt-live" data-alpdt-zone="' . esc_attr( wp_timezone()->getName() ) . '" data-alpdt-endpoint="' . esc_url( rest_url( 'alpdt/v1/render' ) ) . '" data-alpdt-config="' . $payload . '">' . $escaped . '</span>';
		}

		if ( $tag ) {
			$escaped = '<' . $tag . '>' . $escaped . '</' . $tag . '>';
		}

		// Scope the custom CSS allowance to this KSES call only.
		// Keep the filtering callback outside the shortcode renderer so its
		// boolean results cannot be confused with rendered HTML returns.
		$css_filter = array( self::class, 'allow_alpdt_css_declaration' );
		add_filter( 'safecss_filter_attr_allow_css', $css_filter, 10, 2 );
		try {
			$clean_markup = wp_kses( $escaped, $this->allowed_output_html() );
		} finally {
			remove_filter( 'safecss_filter_attr_allow_css', $css_filter, 10 );
		}
		return $clean_markup;
	}

	/**
	 * Allow only ALP-owned, value-restricted CSS custom declarations in KSES.
	 * The filter remains active exclusively while main() sanitizes its output.
	 *
	 * @param mixed $allow_css Existing WordPress safe-CSS decision.
	 * @param mixed $css_test_string Proposed CSS declaration.
	 */
	public static function allow_alpdt_css_declaration( mixed $allow_css, mixed $css_test_string ): bool {
		if ( true === $allow_css ) {
			return true;
		}
		if ( ! is_string( $css_test_string ) ) {
			return false;
		}
		$decl = trim( $css_test_string );
		return 1 === preg_match( '/^--alpdt-[a-z0-9-]+:\s*[^;{}<>]+$/iD', $decl )
			&& false === stripos( $decl, 'url(' )
			&& false === stripos( $decl, 'expression(' )
			&& false === stripos( $decl, 'javascript:' );
	}

	/**
	 * Years Since - Timeless Texts legacy shortcode compatibility.
	 *
	 * @param mixed $atts Shortcode attributes.
	 */
	public function legacy( mixed $atts = array() ): string {
		$atts = is_array( $atts ) ? $atts : array();
		$year = isset( $atts['y'] ) && is_scalar( $atts['y'] ) ? (string) $atts['y'] : '';
		if ( ! preg_match( '/^[0-9]{4}$/D', $year ) ) {
			return '';
		}
		$month = isset( $atts['m'] ) && is_scalar( $atts['m'] ) ? (string) $atts['m'] : '1';
		$day   = isset( $atts['d'] ) && is_scalar( $atts['d'] ) ? (string) $atts['d'] : '1';
		if ( ! ctype_digit( $month ) || ! ctype_digit( $day ) ) {
			return '';
		}
		$date_str = sprintf( '%s-%02d-%02d', $year, (int) $month, (int) $day );
		$date_obj = DateEngine::parse( $date_str );
		if ( ! $date_obj || $date_obj > DateEngine::today() ) {
			return '';
		}

		$text = ! isset( $atts['text'] ) || 'false' !== strtolower( (string) $atts['text'] );
		$show = isset( $atts['show'] ) ? (string) $atts['show'] : '';
		$diff = $date_obj->diff( DateEngine::today() );

		if ( ! $text ) {
			$output = (string) $diff->y;
		} elseif ( 'y' === $show || 'ym' === $show || 'ymd' === $show ) {
			$parts = array( $this->unit( $diff->y, 'year' ) );
			if ( 'ym' === $show || 'ymd' === $show ) {
				$parts[] = $this->unit( $diff->m, 'month' );
			}
			if ( 'ymd' === $show ) {
				$parts[] = $this->unit( $diff->d, 'day' );
			}
			$output = implode( ' ', $parts );
		} elseif ( $diff->y >= 1 ) {
			$parts = array( $this->unit( $diff->y, 'year' ) );
			if ( $diff->m ) {
				$parts[] = $this->unit( $diff->m, 'month' );
			}
			if ( $diff->d ) {
				$parts[] = $this->unit( $diff->d, 'day' );
			}
			$output = implode( ' ', $parts );
		} elseif ( $diff->m >= 1 ) {
			$output = $this->unit( $diff->m, 'month' );
		} else {
			$days   = (int) $diff->format( '%a' );
			$output = $days >= 7 ? $this->unit( intdiv( $days, 7 ), 'week' ) : $this->unit( $days, 'day' );
		}

		$tag     = isset( $atts['html'] ) && is_scalar( $atts['html'] ) ? (string) $atts['html'] : '';
		$allowed = array( 'span', 'div', 'p', 'strong', 'em', 'strike', 'abbr', 'acronym', 'b', 'blockquote', 'cite', 'code', 'del', 'i', 'q', 's', 'u' );
		$formatted = in_array( $tag, $allowed, true )
			? '<' . $tag . '>' . esc_html( $output ) . '</' . $tag . '>'
			: esc_html( $output );

		return wp_kses( $formatted, array_fill_keys( $allowed, array() ) );
	}

	/**
	 * Years Since wrapper shortcode compatibility.
	 *
	 * @param mixed $atts    Attributes.
	 * @param mixed $content Inner content.
	 */
	public function legacy_gb( mixed $atts = array(), mixed $content = null ): string {
		if ( ! is_string( $content ) ) {
			return '';
		}
		$allowed_html           = wp_kses_allowed_html( 'post' );
		$allowed_html['svg']    = array(
			'class'       => true,
			'viewbox'     => true,
			'focusable'   => true,
			'aria-hidden' => true,
		);
		$allowed_html['circle'] = array(
			'class'      => true,
			'cx'         => true,
			'cy'         => true,
			'r'          => true,
			'pathlength' => true,
		);
		return wp_kses( do_shortcode( $content ), $allowed_html );
	}

	private function unit( int $n, string $unit ): string {
		return match ( $unit ) {
			/* translators: %d: number of years in the elapsed interval. */
			'year'  => sprintf( _n( '%d year', '%d years', $n, 'alp-dynamic-time-countdowns' ), $n ),
			/* translators: %d: number of months in the elapsed interval. */
			'month' => sprintf( _n( '%d month', '%d months', $n, 'alp-dynamic-time-countdowns' ), $n ),
			/* translators: %d: number of weeks in the elapsed interval. */
			'week'  => sprintf( _n( '%d week', '%d weeks', $n, 'alp-dynamic-time-countdowns' ), $n ),
			/* translators: %d: number of days in the elapsed interval. */
			default => sprintf( _n( '%d day', '%d days', $n, 'alp-dynamic-time-countdowns' ), $n ),
		};
	}

	/**
	 * Strict HTML surface produced by ClockRenderer and ShortcodeController.
	 *
	 * @return array<string,array<string,bool>>
	 */
	private function allowed_output_html(): array {
		return array(
			'span'   => array(
				'class'                         => true,
				'style'                         => true,
				'role'                          => true,
				'aria-live'                     => true,
				'aria-hidden'                   => true,
				'data-alpdt-display'            => true,
				'data-alpdt-target'             => true,
				'data-alpdt-zone'               => true,
				'data-alpdt-units'              => true,
				'data-alpdt-unit'               => true,
				'data-alpdt-endpoint'           => true,
				'data-alpdt-config'             => true,
				'data-alpdt-recurrence'         => true,
				'data-alpdt-repeat-time'        => true,
				'data-alpdt-repeat-weekday'     => true,
				'data-alpdt-repeat-day'         => true,
				'data-alpdt-next-url'           => true,
				'data-alpdt-end-action'         => true,
				'data-alpdt-end-message'        => true,
				'data-alpdt-end-url'            => true,
				'data-alpdt-celebration-duration' => true,
				'data-alpdt-celebration-mode'   => true,
				'data-alpdt-celebration-message' => true,
				'data-alpdt-confetti-colors'    => true,
				'data-alpdt-balloon-colors'     => true,
				'data-alpdt-blood-font'         => true,
			),
			'strong' => array(),
			'em'     => array(),
			'b'      => array(),
			'i'      => array(),
			'small'  => array(),
			'sup'    => array(),
			'svg'    => array(
				'class'       => true,
				'viewbox'     => true,
				'focusable'   => true,
				'aria-hidden' => true,
			),
			'circle' => array(
				'class'      => true,
				'cx'         => true,
				'cy'         => true,
				'r'          => true,
				'pathlength' => true,
			),
		);
	}
}
