<?php
/**
 * Pure domain validator for per-user Quick Colors palettes.
 *
 * @package AlpDynamicTime\Domain
 */

declare(strict_types=1);

namespace AlpDynamicTime\Domain;

defined( 'ABSPATH' ) || exit;

/**
 * Validates, normalizes, and upgrades HEX and tightly allowlisted gradient presets.
 */
final class QuickColorsValidator {

	public const MAX_COLORS = 24;

	/**
	 * Default ALP Quick Colors (black, red, blue, green, yellow, white).
	 *
	 * @return list<string>
	 */
	public function defaults(): array {
		return array( '#000000', '#F33B49', '#053197', '#07B57B', '#FFCC00', '#FFFFFF' );
	}

	/**
	 * Validate an untrusted palette value (e.g., from AJAX or user_meta).
	 *
	 * @param mixed $colors Candidate palette.
	 * @return list<string>|null Normalized palette values or null when invalid.
	 */
	public function validate( mixed $colors ): ?array {
		if ( ! is_array( $colors ) || ! array_is_list( $colors ) || count( $colors ) > self::MAX_COLORS ) {
			return null;
		}
		$clean = array();
		foreach ( $colors as $color ) {
			if ( ! is_string( $color ) ) {
				return null;
			}
			if ( preg_match( '/^#[0-9a-fA-F]{6}(?:[0-9a-fA-F]{2})?$/D', $color ) ) {
				$normalized = strtoupper( $color );
			} elseif ( preg_match( '/^gradient:(linear|radial):([0-9]{1,3}):(#[0-9a-fA-F]{6}(?:[0-9a-fA-F]{2})?):(#[0-9a-fA-F]{6}(?:[0-9a-fA-F]{2})?)$/iD', $color, $matches ) && (int) $matches[2] <= 360 ) {
				$normalized = 'gradient:' . strtolower( $matches[1] ) . ':' . (int) $matches[2] . ':' . strtoupper( $matches[3] ) . ':' . strtoupper( $matches[4] );
			} else {
				return null;
			}
			if ( ! in_array( $normalized, $clean, true ) ) {
				$clean[] = $normalized;
			}
		}
		return $clean;
	}

	/**
	 * Upgrade legacy pastel preset swatches without discarding user-added colors.
	 *
	 * @param list<string> $colors Validated palette.
	 * @return list<string>
	 */
	public function upgrade_legacy( array $colors ): array {
		$replacements = array(
			'#F5A6AD' => '#F33B49',
			'#A5C8F2' => '#053197',
			'#A5DFC4' => '#07B57B',
		);

		$contains_legacy = false;
		$updated         = array();

		foreach ( $colors as $color ) {
			if ( isset( $replacements[ $color ] ) ) {
				$contains_legacy = true;
				$color           = $replacements[ $color ];
			}
			if ( ! in_array( $color, $updated, true ) ) {
				$updated[] = $color;
			}
		}

		if ( ! $contains_legacy ) {
			return $colors;
		}

		if ( ! in_array( '#FFCC00', $updated, true ) && count( $updated ) < self::MAX_COLORS ) {
			$white_position = array_search( '#FFFFFF', $updated, true );
			if ( false === $white_position ) {
				$updated[] = '#FFCC00';
			} else {
				array_splice( $updated, $white_position, 0, array( '#FFCC00' ) );
			}
		}

		return $updated;
	}
}
