<?php
/**
 * PSR-4 autoloader fallback when Composer vendor/autoload.php is absent.
 *
 * @package AlpDynamicTime\Infrastructure
 */

declare(strict_types=1);

namespace AlpDynamicTime\Infrastructure;

defined( 'ABSPATH' ) || exit;

/**
 * Autoloader mapping AlpDynamicTime\* to src/*.php with path traversal protection.
 */
final class Autoloader {

	private const PREFIX = 'AlpDynamicTime\\';

	private static bool $registered = false;

	/**
	 * Register the SPL autoloader once.
	 */
	public static function register(): void {
		if ( self::$registered ) {
			return;
		}
		self::$registered = true;
		spl_autoload_register( array( self::class, 'load' ) );
	}

	/**
	 * Resolve and require a class file inside src/.
	 *
	 * @param string $class Fully-qualified class name.
	 */
	public static function load( string $class ): void {
		if ( ! str_starts_with( $class, self::PREFIX ) ) {
			return;
		}
		$relative = substr( $class, strlen( self::PREFIX ) );
		if ( false === $relative || '' === $relative ) {
			return;
		}
		$base_dir = realpath( ALPDT_DIR . 'src' );
		if ( false === $base_dir ) {
			return;
		}
		$candidate = $base_dir . DIRECTORY_SEPARATOR . str_replace( '\\', DIRECTORY_SEPARATOR, $relative ) . '.php';
		$resolved  = realpath( $candidate );
		if ( false === $resolved || ! str_starts_with( $resolved, $base_dir . DIRECTORY_SEPARATOR ) || ! is_file( $resolved ) ) {
			return;
		}
		require_once $resolved;
	}
}
