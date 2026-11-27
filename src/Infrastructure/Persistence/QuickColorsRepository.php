<?php
/**
 * Repository for per-user Quick Colors metadata.
 *
 * @package AlpDynamicTime\Infrastructure\Persistence
 */

declare(strict_types=1);

namespace AlpDynamicTime\Infrastructure\Persistence;

use AlpDynamicTime\Domain\QuickColorsValidator;

defined( 'ABSPATH' ) || exit;

/**
 * Persists and upgrades per-user Quick Colors palettes.
 */
final class QuickColorsRepository {

	public const META_KEY = 'alpdt_quick_colors';

	public function __construct(
		private readonly QuickColorsValidator $validator,
	) {}

	/**
	 * Read per-user palette. An explicitly empty array stays empty.
	 *
	 * @return list<string>
	 */
	public function for_user( int $user_id ): array {
		if ( $user_id <= 0 ) {
			return $this->validator->defaults();
		}
		$stored = get_user_meta( $user_id, self::META_KEY, true );
		if ( '' === $stored ) {
			return $this->validator->defaults();
		}
		$validated = $this->validator->validate( $stored );
		if ( null === $validated ) {
			return $this->validator->defaults();
		}
		$updated = $this->validator->upgrade_legacy( $validated );
		if ( $updated !== $validated ) {
			update_user_meta( $user_id, self::META_KEY, $updated );
		}
		return $updated;
	}

	/**
	 * Persist a validated palette for a user.
	 *
	 * @param list<string> $colors Validated palette.
	 */
	public function save_for_user( int $user_id, array $colors ): bool {
		if ( $user_id <= 0 ) {
			return false;
		}
		return false !== update_user_meta( $user_id, self::META_KEY, $colors );
	}
}
