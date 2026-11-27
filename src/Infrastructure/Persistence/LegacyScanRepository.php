<?php
/**
 * Read-only, prepared and transient-cached scanner for legacy [years-since] shortcodes.
 *
 * @package AlpDynamicTime\Infrastructure\Persistence
 */

declare(strict_types=1);

namespace AlpDynamicTime\Infrastructure\Persistence;

defined( 'ABSPATH' ) || exit;

/**
 * Queries wp_posts safely for legacy shortcodes and caches raw post stubs briefly.
 */
final class LegacyScanRepository {

	private const TRANSIENT_PREFIX = 'alpdt_legacy_scan_p';
	private const CACHE_TTL        = 300; // 5 minutes.
	private const MAX_PAGE         = 100;
	public const PER_PAGE          = 20;

	/**
	 * Fetch a bounded batch of posts containing [years-since].
	 * Authorization (`current_user_can('edit_post', $id)`) is never cached and must be checked per viewer.
	 *
	 * @return list<object{ID:int,post_title:string}>
	 */
	public function find_legacy_posts( int $page, bool $force_refresh = false ): array {
		$page   = min( self::MAX_PAGE, max( 1, $page ) );
		$key    = self::TRANSIENT_PREFIX . $page;
		$cached = $force_refresh ? false : get_transient( $key );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		global $wpdb;
		$offset = ( $page - 1 ) * self::PER_PAGE;
		$needle = '%' . $wpdb->esc_like( '[years-since' ) . '%';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Prepared bounded read-only scan with explicit transient caching below.
		$items = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_title FROM %i WHERE post_content LIKE %s AND post_type NOT IN ('revision','attachment') AND post_status NOT IN ('trash','auto-draft') ORDER BY ID DESC LIMIT %d OFFSET %d",
				$wpdb->posts,
				$needle,
				self::PER_PAGE,
				$offset
			)
		);

		$rows = array();
		if ( is_array( $items ) ) {
			foreach ( $items as $item ) {
				if ( is_object( $item ) && isset( $item->ID ) ) {
					$rows[] = (object) array(
						'ID'         => (int) $item->ID,
						'post_title' => isset( $item->post_title ) ? (string) $item->post_title : '',
					);
				}
			}
		}

		set_transient( $key, $rows, self::CACHE_TTL );
		return $rows;
	}

	/**
	 * Invalidate cached scan pages when post content changes.
	 *
	 * @param mixed $post_id Optional post ID from save_post / deleted_post hooks.
	 */
	public function invalidate( mixed $post_id = 0 ): void {
		$id = is_numeric( $post_id ) ? (int) $post_id : 0;
		if ( $id > 0 && ( ( function_exists( 'wp_is_post_revision' ) && wp_is_post_revision( $id ) ) || ( function_exists( 'wp_is_post_autosave' ) && wp_is_post_autosave( $id ) ) ) ) {
			return;
		}
		global $wpdb;
		if ( ! isset( $wpdb ) ) {
			return;
		}
		$like_transient = $wpdb->esc_like( '_transient_' . self::TRANSIENT_PREFIX ) . '%';
		$like_timeout   = $wpdb->esc_like( '_transient_timeout_' . self::TRANSIENT_PREFIX ) . '%';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Targeted invalidation of plugin-owned transient keys only.
		$wpdb->query(
			$wpdb->prepare(
				'DELETE FROM %i WHERE option_name LIKE %s OR option_name LIKE %s',
				$wpdb->options,
				$like_transient,
				$like_timeout
			)
		);
	}
}
