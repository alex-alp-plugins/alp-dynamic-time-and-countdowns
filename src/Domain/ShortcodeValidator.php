<?php
/**
 * Pure domain validator for saved shortcode strings and reusable identifiers.
 *
 * @package AlpDynamicTime\Domain
 */

declare(strict_types=1);

namespace AlpDynamicTime\Domain;

defined( 'ABSPATH' ) || exit;

/**
 * Parses and validates [alpdt_time ...] attribute maps and reusable slugs.
 */
final class ShortcodeValidator {

	/** @var list<string> */
	private const ALLOWED_ATTRS = array(
		'type', 'from', 'to', 'id', 'mode', 'unit', 'text', 'format', 'offset', 'html', 'live',
		'before', 'on', 'after', 'copyright_style', 'expression', 'hemisphere', 'recurrence',
		'repeat_time', 'repeat_weekday', 'repeat_day', 'end_action', 'end_message', 'end_url',
		'countdown_display', 'clock_style', 'clock_layout', 'clock_width', 'clock_units',
		'celebration_duration', 'celebration_mode', 'celebration_message', 'digit_color',
		'label_color', 'card_color', 'accent_color', 'track_color', 'panel_color', 'clock_size',
		'digit_size', 'label_size', 'clock_radius', 'clock_radius_top_left', 'clock_radius_top_right', 'clock_radius_bottom_right', 'clock_radius_bottom_left', 'clock_padding', 'clock_padding_mode',
		'clock_padding_top', 'clock_padding_right', 'clock_padding_bottom', 'clock_padding_left',
		'countdown_padding_top', 'countdown_padding_right', 'countdown_padding_bottom',
		'countdown_padding_left', 'clock_font', 'clock_weight', 'clock_italic',
		'card_inner_spacing', 'bulb_card_gap', 'bulb_count', 'glow_background_color',
		'bg_gradient_type', 'bg_gradient_start', 'bg_gradient_end', 'bg_gradient_angle', 'bg_gradient_target',
		'blood_font', 'lights_mode',
		'christmas_1_color', 'christmas_2_color', 'christmas_3_color', 'christmas_4_color',
		'christmas_5_color', 'christmas_6_color', 'christmas_7_color', 'christmas_8_color',
		'christmas_9_color', 'christmas_10_color', 'christmas_11_color', 'christmas_12_color',
		'label_years', 'label_months', 'label_days', 'label_hours', 'label_minutes', 'label_seconds',
		'confetti_1_color', 'confetti_2_color', 'confetti_3_color', 'confetti_4_color',
		'confetti_5_color', 'confetti_6_color', 'confetti_7_color', 'confetti_8_color',
		'confetti_9_color', 'confetti_10_color',
		'balloon_1_color', 'balloon_2_color', 'balloon_3_color', 'balloon_4_color',
		'balloon_5_color', 'balloon_6_color', 'balloon_7_color', 'balloon_8_color',
		'balloon_9_color', 'balloon_10_color',
	);

	/** @var list<string> */
	private const ALLOWED_TYPES = array(
		'elapsed', 'year', 'month', 'date', 'countdown', 'copyright',
		'ordinal', 'anniversary_message', 'smart_date',
	);

	public function __construct(
		private readonly ScheduleCalculator $schedule,
		private readonly ClockRenderer $clock,
	) {}

	/**
	 * Validate a reusable slug (rejects canonical integer keys).
	 */
	public static function is_valid_slug( string $slug ): bool {
		return (string) (int) $slug !== $slug && 1 === preg_match( '/^[a-z0-9][a-z0-9_-]{0,54}$/D', $slug );
	}

	/**
	 * Strictly accept one ALP shortcode, with allowlisted double-quoted attributes.
	 *
	 * @param mixed $code Raw shortcode string.
	 * @return array<string,string>|null
	 */
	public function parse( mixed $code ): ?array {
		if ( ! is_string( $code ) || strlen( $code ) > 2400 || ! preg_match( '/^\[alpdt_time((?:\s+[a-z][a-z0-9_]*="[^"\[\]\r\n]*")*)\]$/D', trim( $code ), $match ) ) {
			return null;
		}

		$attrs = array();
		preg_match_all( '/\s+([a-z][a-z0-9_]*)="([^"\[\]\r\n]*)"/', $match[1], $found, PREG_SET_ORDER );
		foreach ( $found as $item ) {
			$key = $item[1];
			if ( ! in_array( $key, self::ALLOWED_ATTRS, true ) || isset( $attrs[ $key ] ) || strlen( $item[2] ) > 250 ) {
				return null;
			}
			$attrs[ $key ] = $item[2];
		}

		if ( ! isset( $attrs['type'] ) || ! in_array( $attrs['type'], self::ALLOWED_TYPES, true ) ) {
			return null;
		}
		if ( in_array( $attrs['type'], array( 'elapsed', 'ordinal', 'anniversary_message' ), true ) && empty( $attrs['from'] ) && empty( $attrs['id'] ) ) {
			return null;
		}
		if ( 'countdown' === $attrs['type'] && ( ( empty( $attrs['recurrence'] ) || 'none' === $attrs['recurrence'] ) ? ( empty( $attrs['to'] ) || ! DateEngine::target( $attrs['to'] ) ) : ! $this->schedule->next( $attrs ) ) ) {
			return null;
		}
		if ( 'copyright' === $attrs['type'] && ( $attrs['copyright_style'] ?? 'range' ) !== 'current' && empty( $attrs['from'] ) && empty( $attrs['id'] ) ) {
			return null;
		}
		if ( ! empty( $attrs['from'] ) && ! DateEngine::parse( $attrs['from'] ) ) {
			return null;
		}
		if ( isset( $attrs['clock_width'] ) && ! in_array( $attrs['clock_width'], array( 'auto', 'full' ), true ) ) {
			return null;
		}
		if ( isset( $attrs['clock_layout'] ) && ! in_array( $attrs['clock_layout'], array( 'default', 'horizontal' ), true ) ) {
			return null;
		}
		if ( isset( $attrs['blood_font'] ) && ! in_array( $attrs['blood_font'], array( 'creepster', 'nosifer' ), true ) ) {
			return null;
		}
		if ( isset( $attrs['clock_weight'] ) && ! in_array( $attrs['clock_weight'], array( '300', '400', '500', '600', '700', '800', '900' ), true ) ) {
			return null;
		}
		if ( isset( $attrs['clock_italic'] ) && ! in_array( $attrs['clock_italic'], array( 'normal', 'italic' ), true ) ) {
			return null;
		}
		foreach ( array( 'digit_size', 'label_size' ) as $font_key ) {
			if ( isset( $attrs[ $font_key ] ) && null === $this->clock->font_size( $attrs[ $font_key ], $font_key ) ) {
				return null;
			}
		}
		if ( isset( $attrs['bulb_card_gap'] ) && ! preg_match( '/^(?:[0-9]|[1-6][0-9]|70)$/D', $attrs['bulb_card_gap'] ) ) {
			return null;
		}
		if ( isset( $attrs['bulb_count'] ) && ( ! preg_match( '/^[0-9]{1,2}$/D', $attrs['bulb_count'] ) || (int) $attrs['bulb_count'] < 8 || (int) $attrs['bulb_count'] > 60 ) ) {
			return null;
		}
		if ( isset( $attrs['card_inner_spacing'] ) && ! preg_match( '/^(?:[0-9]|[12][0-9]|30)$/D', $attrs['card_inner_spacing'] ) ) {
			return null;
		}
		if ( isset( $attrs['lights_mode'] ) && ! in_array( $attrs['lights_mode'], array( 'multicolor', 'warm' ), true ) ) {
			return null;
		}
		if ( isset( $attrs['bg_gradient_type'] ) && ! in_array( $attrs['bg_gradient_type'], array( 'none', 'linear', 'radial' ), true ) ) {
			return null;
		}
		if ( isset( $attrs['bg_gradient_target'] ) && ! in_array( $attrs['bg_gradient_target'], array( 'container', 'cards' ), true ) ) {
			return null;
		}
		if ( isset( $attrs['bg_gradient_angle'] ) && ( ! preg_match( '/^[0-9]{1,3}$/D', $attrs['bg_gradient_angle'] ) || (int) $attrs['bg_gradient_angle'] > 360 ) ) {
			return null;
		}
		foreach ( array( 'bg_gradient_start', 'bg_gradient_end' ) as $grad_color_key ) {
			if ( isset( $attrs[ $grad_color_key ] ) && ! preg_match( '/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/D', $attrs[ $grad_color_key ] ) ) {
				return null;
			}
		}
		if ( isset( $attrs['clock_padding_mode'] ) && ! in_array( $attrs['clock_padding_mode'], array( 'global', 'individual' ), true ) ) {
			return null;
		}
		if ( isset( $attrs['clock_radius'] ) && ! preg_match( '/^(?:[0-9]|[1-7][0-9]|80)$/D', $attrs['clock_radius'] ) ) {
			return null;
		}
		foreach ( array( 'clock_radius_top_left', 'clock_radius_top_right', 'clock_radius_bottom_right', 'clock_radius_bottom_left' ) as $corner_key ) {
			if ( isset( $attrs[ $corner_key ] ) && ! preg_match( '/^(?:[0-9]|[1-7][0-9]|80)$/D', $attrs[ $corner_key ] ) ) {
				return null;
			}
		}
		foreach ( array( 'clock_padding', 'clock_padding_top', 'clock_padding_right', 'clock_padding_bottom', 'clock_padding_left', 'countdown_padding_top', 'countdown_padding_right', 'countdown_padding_bottom', 'countdown_padding_left' ) as $padding_key ) {
			if ( isset( $attrs[ $padding_key ] ) && ! preg_match( '/^(?:[0-9]|[1-7][0-9]|80)$/D', $attrs[ $padding_key ] ) ) {
				return null;
			}
		}
		if ( isset( $attrs['celebration_duration'] ) && ! in_array( $attrs['celebration_duration'], array( '3', '5', '8', '10' ), true ) ) {
			return null;
		}
		if ( isset( $attrs['celebration_mode'] ) && ! in_array( $attrs['celebration_mode'], array( 'end', 'loop' ), true ) ) {
			return null;
		}
		if ( 'smart_date' === $attrs['type'] && ! in_array( $attrs['expression'] ?? 'today', $this->schedule->expressions(), true ) ) {
			return null;
		}
		if ( 'smart_date' === $attrs['type'] && 'date_difference' === ( $attrs['expression'] ?? '' ) && ( ! DateEngine::parse( $attrs['from'] ?? '' ) || ! DateEngine::parse( $attrs['to'] ?? '' ) ) ) {
			return null;
		}
		if ( isset( $attrs['recurrence'] ) && ! in_array( $attrs['recurrence'], array( 'none', 'daily', 'weekly', 'monthly' ), true ) ) {
			return null;
		}
		if ( isset( $attrs['end_action'] ) && ! in_array( $attrs['end_action'], array( 'none', 'message', 'hide', 'redirect' ), true ) ) {
			return null;
		}
		if ( isset( $attrs['end_url'] ) && '' !== $attrs['end_url'] && '' === $this->schedule->redirect_path( $attrs['end_url'] ) ) {
			return null;
		}
		if ( isset( $attrs['hemisphere'] ) && ! in_array( $attrs['hemisphere'], array( 'north', 'south' ), true ) ) {
			return null;
		}

		foreach ( $attrs as $key => $value ) {
			if ( str_contains( $value, '<' ) || str_contains( $value, '>' ) || str_contains( $value, chr( 92 ) ) ) {
				return null;
			}
			if ( str_ends_with( $key, '_color' ) && 'transparent' !== $value && ! preg_match( '/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/D', $value ) ) {
				return null;
			}
		}

		return $attrs;
	}
}
