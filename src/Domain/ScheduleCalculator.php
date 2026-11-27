<?php
/**
 * Strict, timezone-aware dynamic date expressions and recurring countdown schedules.
 *
 * @package AlpDynamicTime\Domain
 */

declare(strict_types=1);

namespace AlpDynamicTime\Domain;

defined( 'ABSPATH' ) || exit;

/**
 * Evaluates smart calendar expressions and recurring countdown target timestamps.
 */
final class ScheduleCalculator {

	/** @var list<string> */
	private const EXPRESSIONS = array(
		'today', 'tomorrow', 'in_7_days', 'in_30_days',
		'next_monday', 'next_tuesday', 'next_wednesday', 'next_thursday',
		'next_friday', 'next_saturday', 'next_sunday',
		'month_end', 'quarter', 'season',
		'post_published', 'post_modified', 'date_difference',
	);

	/**
	 * Supported smart-date expression identifiers.
	 *
	 * @return list<string>
	 */
	public function expressions(): array {
		return self::EXPRESSIONS;
	}

	/**
	 * Render a smart-date expression.
	 *
	 * @param array<string,mixed>     $raw       Raw shortcode attributes.
	 * @param \DateTimeImmutable|null $reference Optional clock override for deterministic tests.
	 */
	public function dynamic( array $raw, ?\DateTimeImmutable $reference = null ): string {
		$zone   = wp_timezone();
		$now    = ( $reference ?: new \DateTimeImmutable( 'now', $zone ) )->setTimezone( $zone );
		$expr   = isset( $raw['expression'] ) && is_string( $raw['expression'] ) ? $raw['expression'] : '';
		if ( ! in_array( $expr, self::EXPRESSIONS, true ) ) {
			return '';
		}

		$format = isset( $raw['format'] ) && is_scalar( $raw['format'] ) ? (string) $raw['format'] : 'j F Y';
		if ( strlen( $format ) > 40 || ! preg_match( '/^[A-Za-z 0-9,.\/\-]+$/D', $format ) ) {
			return '';
		}

		if ( 'quarter' === $expr ) {
			return (string) (int) ceil( (int) $now->format( 'n' ) / 3 );
		}

		if ( 'season' === $expr ) {
			$month = (int) $now->format( 'n' );
			$north = array(
				12 => 'winter', 1 => 'winter', 2 => 'winter',
				3  => 'spring', 4 => 'spring', 5 => 'spring',
				6  => 'summer', 7 => 'summer', 8 => 'summer',
				9  => 'autumn', 10 => 'autumn', 11 => 'autumn',
			);
			$key = $north[ $month ];
			if ( isset( $raw['hemisphere'] ) && 'south' === $raw['hemisphere'] ) {
				$key = array(
					'winter' => 'summer',
					'summer' => 'winter',
					'spring' => 'autumn',
					'autumn' => 'spring',
				)[ $key ];
			}
			$names = array(
				'winter' => __( 'Winter', 'alp-dynamic-time-countdowns' ),
				'spring' => __( 'Spring', 'alp-dynamic-time-countdowns' ),
				'summer' => __( 'Summer', 'alp-dynamic-time-countdowns' ),
				'autumn' => __( 'Autumn', 'alp-dynamic-time-countdowns' ),
			);
			return $names[ $key ];
		}

		if ( 'date_difference' === $expr ) {
			$from = isset( $raw['from'] ) && is_scalar( $raw['from'] ) ? DateEngine::parse( (string) $raw['from'] ) : null;
			$to   = isset( $raw['to'] ) && is_scalar( $raw['to'] ) ? DateEngine::parse( (string) $raw['to'] ) : null;
			if ( ! $from || ! $to ) {
				return '';
			}
			$diff = $from->diff( $to );
			return ( $diff->invert ? '-' : '' ) . $diff->format( '%a' );
		}

		if ( in_array( $expr, array( 'post_published', 'post_modified' ), true ) ) {
			if ( ! function_exists( 'get_post_datetime' ) || ! function_exists( 'get_post' ) ) {
				return '';
			}
			$post = get_post();
			if ( ! $post || ( isset( $post->post_status ) && 'publish' !== $post->post_status ) ) {
				return '';
			}
			$date = get_post_datetime( $post, 'post_modified' === $expr ? 'modified' : 'date', 'local' );
			return $date instanceof \DateTimeInterface ? wp_date( $format, $date->getTimestamp(), $zone ) : '';
		}

		$date = match ( true ) {
			'tomorrow' === $expr               => $now->modify( '+1 day' ),
			'in_7_days' === $expr              => $now->modify( '+7 days' ),
			'in_30_days' === $expr             => $now->modify( '+30 days' ),
			'month_end' === $expr              => $now->modify( 'last day of this month' ),
			str_starts_with( $expr, 'next_' )  => $now->modify( str_replace( '_', ' ', $expr ) ),
			default                            => $now,
		};

		return (string) wp_date( $format, $date->getTimestamp(), $zone );
	}

	/**
	 * Return the first upcoming local-time occurrence. Invalid DST spring-forward times are skipped.
	 *
	 * @param array<string,mixed>     $raw       Recurrence configuration.
	 * @param \DateTimeImmutable|null $reference Optional clock override.
	 */
	public function next( array $raw, ?\DateTimeImmutable $reference = null ): ?\DateTimeImmutable {
		$frequency = isset( $raw['recurrence'] ) && is_scalar( $raw['recurrence'] ) ? (string) $raw['recurrence'] : '';
		if ( ! in_array( $frequency, array( 'daily', 'weekly', 'monthly' ), true ) ) {
			return null;
		}

		$time = isset( $raw['repeat_time'] ) && is_scalar( $raw['repeat_time'] ) ? (string) $raw['repeat_time'] : '12:00';
		if ( ! preg_match( '/^([01][0-9]|2[0-3]):([0-5][0-9])$/D', $time, $match ) ) {
			return null;
		}

		$weekday = isset( $raw['repeat_weekday'] ) && is_scalar( $raw['repeat_weekday'] ) ? (string) $raw['repeat_weekday'] : '1';
		$day     = isset( $raw['repeat_day'] ) && is_scalar( $raw['repeat_day'] ) ? (string) $raw['repeat_day'] : '1';
		if ( ! preg_match( '/^[1-7]$/D', $weekday ) || ! preg_match( '/^(?:[1-9]|[12][0-9]|3[01])$/D', $day ) ) {
			return null;
		}

		$zone     = wp_timezone();
		$now      = ( $reference ?: new \DateTimeImmutable( 'now', $zone ) )->setTimezone( $zone );
		$midnight = $now->setTime( 0, 0, 0 );

		// At most 3 months ahead. Loop over calendar dates rather than seconds to respect DST.
		for ( $i = 0; $i <= 95; ++$i ) {
			$date = $midnight->modify( '+' . $i . ' days' );
			if ( 'weekly' === $frequency && (int) $date->format( 'N' ) !== (int) $weekday ) {
				continue;
			}
			if ( 'monthly' === $frequency ) {
				$last = (int) $date->format( 't' );
				if ( (int) $date->format( 'j' ) !== min( (int) $day, $last ) ) {
					continue;
				}
			}
			$stamp     = $date->format( 'Y-m-d' ) . ' ' . $match[1] . ':' . $match[2] . ':00';
			$candidate = \DateTimeImmutable::createFromFormat( '!Y-m-d H:i:s', $stamp, $zone );
			if ( $candidate instanceof \DateTimeImmutable && $candidate->format( 'Y-m-d H:i:s' ) === $stamp && $candidate > $now ) {
				return $candidate;
			}
		}

		return null;
	}

	/**
	 * Validate a same-site redirect path, rejecting scheme-relative URLs, backslashes and control characters.
	 *
	 * @param mixed $path Candidate path.
	 */
	public function redirect_path( mixed $path ): string {
		if ( ! is_string( $path ) || strlen( $path ) > 240 || ! preg_match( '~^/(?!/)[a-zA-Z0-9/_\-]*$~D', $path ) ) {
			return '';
		}
		return $path;
	}
}
