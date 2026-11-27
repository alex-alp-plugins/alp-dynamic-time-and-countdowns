<?php
/**
 * Repository for saved named shortcodes (alpdt_saved_shortcodes option).
 *
 * @package AlpDynamicTime\Infrastructure\Persistence
 */

declare(strict_types=1);

namespace AlpDynamicTime\Infrastructure\Persistence;

use AlpDynamicTime\Domain\ShortcodeValidator;

defined( 'ABSPATH' ) || exit;

/**
 * Manages persisted shortcode configurations with per-request validation memoization.
 */
final class SavedShortcodeRepository {

	private const OPTION = 'alpdt_saved_shortcodes';
	public const MAX     = 100;

	/** @var array<string,mixed>|null */
	private ?array $raw_cache = null;

	/** @var array<string,array{name:string,code:string}>|null */
	private ?array $valid_cache = null;

	/** @var array<string,array<string,string>|null> */
	private array $resolved_cache = array();

	public function __construct(
		private readonly ShortcodeValidator $validator,
	) {}

	/**
	 * Raw stored records, including old invalid shortcode aliases. Never silently delete them.
	 *
	 * @return array<string,mixed>
	 */
	private function stored(): array {
		if ( null === $this->raw_cache ) {
			$items           = get_option( self::OPTION, array() );
			$this->raw_cache = is_array( $items ) ? $items : array();
		}
		return $this->raw_cache;
	}

	/**
	 * Only usable saved configurations should be listed, counted or offered to Divi/Gutenberg.
	 *
	 * @return array<string,array{name:string,code:string}>
	 */
	public function all(): array {
		if ( null !== $this->valid_cache ) {
			return $this->valid_cache;
		}
		$valid = array();
		foreach ( $this->stored() as $slug => $entry ) {
			if ( ! is_string( $slug ) ) {
				continue;
			}
			$parsed = $this->parse_record( $slug, $entry );
			if ( null === $parsed ) {
				continue;
			}
			$this->resolved_cache[ $slug ] = $parsed;
			$valid[ $slug ]                = $entry;
		}
		$this->valid_cache = $valid;
		return $valid;
	}

	/**
	 * Validate and persist a saved shortcode configuration.
	 */
	public function save( string $slug, string $name, string $code, string $original = '' ): bool {
		$name_len = function_exists( 'mb_strlen' ) ? mb_strlen( $name, 'UTF-8' ) : strlen( $name );
		if ( ! ShortcodeValidator::is_valid_slug( $slug ) || '' === trim( $name ) || $name_len > 120 || null === $this->validator->parse( $code ) ) {
			return false;
		}
		$items = $this->stored();
		$valid = $this->all();
		if ( '' === $original ) {
			if ( isset( $valid[ $slug ] ) || count( $valid ) >= self::MAX ) {
				return false;
			}
		} elseif ( $original !== $slug || ! isset( $valid[ $slug ] ) ) {
			return false;
		}

		$items[ $slug ] = array(
			'name' => $name,
			'code' => trim( $code ),
		);
		update_option( self::OPTION, $items, false );
		$this->clear_cache();
		return true;
	}

	/**
	 * Delete a valid saved shortcode configuration.
	 */
	public function delete( string $slug ): bool {
		$items = $this->stored();
		if ( ! isset( $this->all()[ $slug ] ) ) {
			return false;
		}
		unset( $items[ $slug ] );
		update_option( self::OPTION, $items, false );
		$this->clear_cache();
		return true;
	}

	/**
	 * Resolve a saved shortcode slug into its validated attribute map.
	 *
	 * @param mixed $slug Candidate slug.
	 * @return array<string,string>|null
	 */
	public function resolve( mixed $slug ): ?array {
		if ( ! is_string( $slug ) || '' === $slug ) {
			return null;
		}
		if ( array_key_exists( $slug, $this->resolved_cache ) ) {
			return $this->resolved_cache[ $slug ];
		}
		$items                         = $this->stored();
		$resolved                      = $this->parse_record( $slug, $items[ $slug ] ?? null );
		$this->resolved_cache[ $slug ] = $resolved;
		return $resolved;
	}

	/**
	 * Clear per-request memoized caches.
	 */
	public function clear_cache(): void {
		$this->raw_cache      = null;
		$this->valid_cache    = null;
		$this->resolved_cache = array();
	}

	/**
	 * Validate a stored record before exposing it through a shortcode or selector.
	 *
	 * @param mixed $entry Stored entry.
	 * @return array<string,string>|null
	 */
	private function parse_record( string $slug, mixed $entry ): ?array {
		if ( ! ShortcodeValidator::is_valid_slug( $slug ) || ! is_array( $entry ) ||
			! isset( $entry['code'], $entry['name'] ) || ! is_string( $entry['name'] ) ||
			'' === trim( $entry['name'] ) ) {
			return null;
		}
		return $this->validator->parse( $entry['code'] );
	}
}
