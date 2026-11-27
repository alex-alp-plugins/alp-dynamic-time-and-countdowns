<?php
/**
 * Admin view renderer and reusable UI helpers (copy buttons, shortcode examples).
 *
 * @package AlpDynamicTime\Presentation\Admin
 */

declare(strict_types=1);

namespace AlpDynamicTime\Presentation\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Renders modular admin templates with isolated scope.
 */
final class AdminView {

	/**
	 * Render the dashboard wrapper template.
	 *
	 * @param array<string,mixed> $context Template variables.
	 */
	public function render( array $context ): void {
		$this->partial( 'dashboard', $context );
	}

	/**
	 * Render a named view partial from Views/.
	 *
	 * @param string              $template Template basename without extension.
	 * @param array<string,mixed> $context  Template variables.
	 */
	public function partial( string $template, array $context = array() ): void {
		$base = realpath( __DIR__ . '/Views' );
		if ( false === $base ) {
			return;
		}
		$safe_name = preg_replace( '/[^a-z0-9\-]/', '', $template );
		if ( ! is_string( $safe_name ) || '' === $safe_name ) {
			return;
		}
		$file = realpath( $base . DIRECTORY_SEPARATOR . $safe_name . '.php' );
		if ( false === $file || ! str_starts_with( $file, $base . DIRECTORY_SEPARATOR ) || ! is_file( $file ) ) {
			return;
		}

		$renderer = include $file;
		if ( is_callable( $renderer ) ) {
			$renderer( $context, $this );
		}
	}

	/**
	 * Render a copyable shortcode or SEO variable row.
	 */
	public function shortcode_example( string $shortcode, bool $seo = false ): void {
		echo '<div class="alpdt-code-row"><code>' . esc_html( $shortcode ) . '</code>';
		$this->copy_button( $shortcode, $seo );
		echo '</div>';
	}

	/**
	 * Render a copy button with inline SVG icon.
	 */
	public function copy_button( string $shortcode, bool $seo = false ): void {
		echo '<span class="alpdt-copy-action"><button type="button" class="button alpdt-copy-mini" data-alpdt-copy-kind="' . ( $seo ? 'variable' : 'shortcode' ) . '" data-alpdt-copy-value="' . esc_attr( $shortcode ) . '"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg>' . ( $seo ? esc_html__( 'Copy SEO variable', 'alp-dynamic-time-countdowns' ) : esc_html__( 'Copy shortcode', 'alp-dynamic-time-countdowns' ) ) . '</button></span>';
	}
}
