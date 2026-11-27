<?php
/**
 * Minimal PSR-11-compatible lazy singleton dependency container.
 *
 * @package AlpDynamicTime
 */

declare(strict_types=1);

namespace AlpDynamicTime;

defined( 'ABSPATH' ) || exit;

/**
 * Lightweight service container for plugin wiring without external runtime bloat.
 */
final class Container {

	/** @var array<string, callable(self): mixed> */
	private array $factories = array();

	/** @var array<string, mixed> */
	private array $instances = array();

	/**
	 * Register a lazy singleton factory.
	 *
	 * @param string               $id      Service identifier (usually FQCN).
	 * @param callable(self):mixed $factory Factory returning the service instance.
	 */
	public function set( string $id, callable $factory ): void {
		$this->factories[ $id ] = $factory;
		unset( $this->instances[ $id ] );
	}

	/**
	 * Determine whether a service is registered.
	 *
	 * @param string $id Service identifier.
	 */
	public function has( string $id ): bool {
		return isset( $this->factories[ $id ] ) || array_key_exists( $id, $this->instances );
	}

	/**
	 * Retrieve a resolved singleton service.
	 *
	 * @param string $id Service identifier.
	 * @return mixed
	 *
	 * @throws \InvalidArgumentException When the service ID is unknown.
	 */
	public function get( string $id ): mixed {
		if ( array_key_exists( $id, $this->instances ) ) {
			return $this->instances[ $id ];
		}
		if ( ! isset( $this->factories[ $id ] ) ) {
			throw new \InvalidArgumentException( sprintf( 'Service "%s" is not registered in container.', esc_html( $id ) ) );
		}
		$this->instances[ $id ] = ( $this->factories[ $id ] )( $this );
		return $this->instances[ $id ];
	}
}
