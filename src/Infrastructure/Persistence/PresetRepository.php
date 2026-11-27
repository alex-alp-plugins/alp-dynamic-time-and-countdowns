<?php
/**
 * Repository for reusable saved date presets (alpdt_presets option).
 *
 * @package AlpDynamicTime\Infrastructure\Persistence
 */

declare(strict_types=1);

namespace AlpDynamicTime\Infrastructure\Persistence;

use AlpDynamicTime\Domain\DateEngine;
use AlpDynamicTime\Domain\ShortcodeValidator;

defined( 'ABSPATH' ) || exit;

/**
 * Manages persisted date presets with per-request memoization.
 */
final class PresetRepository {

	public const OPTION = 'alpdt_presets';
	public const MAX    = 200;

	/** @var array<string,mixed>|null */
	private ?array $cache = null;

	/**
	 * Return all stored presets.
	 *
	 * @return array<string,mixed>
	 */
	public function all(): array {
		if ( null === $this->cache ) {
			$raw         = get_option( self::OPTION, array() );
			$this->cache = is_array( $raw ) ? $raw : array();
		}
		return $this->cache;
	}

	/**
	 * Save or update a reusable preset date.
	 */
	public function save( string $slug, string $date, string $label, string $original = '' ): string {
		$presets     = $this->all();
		$editing     = '' !== $original;
		$label_len   = function_exists( 'mb_strlen' ) ? mb_strlen( $label, 'UTF-8' ) : strlen( $label );
		$is_valid    = ShortcodeValidator::is_valid_slug( $slug )
			&& '' !== trim( $label )
			&& $label_len <= 100
			&& null !== DateEngine::parse( $date );

		if ( $editing ) {
			$is_valid = $is_valid && $slug === $original && isset( $presets[ $original ] );
		} else {
			$is_valid = $is_valid && ! isset( $presets[ $slug ] ) && count( $presets ) < self::MAX;
		}

		if ( ! $is_valid ) {
			return 'invalid';
		}

		$presets[ $slug ] = array(
			'date'  => $date,
			'label' => $label,
		);
		update_option( self::OPTION, $presets, false );
		$this->cache = $presets;

		return $editing ? 'updated' : 'saved';
	}

	/**
	 * Delete a preset by slug.
	 */
	public function delete( string $slug ): bool {
		$presets = $this->all();
		if ( '' === $slug || ! isset( $presets[ $slug ] ) ) {
			return false;
		}
		unset( $presets[ $slug ] );
		update_option( self::OPTION, $presets, false );
		$this->cache = $presets;
		return true;
	}

	/**
	 * Clear per-request memoized cache.
	 */
	public function clear_cache(): void {
		$this->cache = null;
	}
}
