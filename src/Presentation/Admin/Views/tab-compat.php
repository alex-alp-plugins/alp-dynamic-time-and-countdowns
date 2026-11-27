<?php
/**
 * Compatibility & Migration tab template.
 *
 * @package AlpDynamicTime\Presentation\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

use AlpDynamicTime\Infrastructure\Persistence\LegacyScanRepository;

return static function ( array $context, \AlpDynamicTime\Presentation\Admin\AdminView $view ): void {
	$handles_alias = ! empty( $context['handles_alias'] );
	$scan_items    = is_array( $context['scan_items'] ?? null ) ? $context['scan_items'] : array();
	$scan_page     = (int) ( $context['scan_page'] ?? 1 );
	$page          = is_string( $context['page'] ?? null ) ? $context['page'] : '';
?>
<section role="tabpanel" tabindex="0" id="alpdt-panel-compat" aria-labelledby="alpdt-tab-compat" class="alpdt-panel" hidden>
	<div class="alpdt-section-intro"><h2><?php esc_html_e( 'Compatibility and migration', 'alp-dynamic-time-countdowns' ); ?></h2><p><?php esc_html_e( 'Keep your existing pages and check how the old shortcodes behave before making any changes.', 'alp-dynamic-time-countdowns' ); ?></p></div>
	<div class="alpdt-columns">
		<div class="alpdt-card">
			<h3><?php esc_html_e( 'Years Since - Timeless Texts plugin migration', 'alp-dynamic-time-countdowns' ); ?></h3>
			<p><?php esc_html_e( 'The Years Since - Timeless Texts plugin was removed from the WordPress.org repository. ALP Dynamic Time & Countdowns independently supports its existing shortcode syntax so old pages can continue to work after you deactivate the original plugin. ALP Dynamic Time & Countdowns is an independent product, with no affiliation with, endorsement by, or relationship to the developers of the removed plugin.', 'alp-dynamic-time-countdowns' ); ?></p>
			<p><?php echo $handles_alias ? esc_html__( 'The old shortcode is handled by ALP Dynamic Time & Countdowns.', 'alp-dynamic-time-countdowns' ) : esc_html__( 'Another plugin currently handles the old shortcode.', 'alp-dynamic-time-countdowns' ); ?></p>
			<p><?php esc_html_e( 'Content is never rewritten automatically. Review results before removing the old plugin.', 'alp-dynamic-time-countdowns' ); ?></p>
		</div>
		<div class="alpdt-card">
			<h3><?php esc_html_e( 'Read-only legacy scan', 'alp-dynamic-time-countdowns' ); ?></h3>
			<p><?php esc_html_e( 'The pages listed below contain shortcodes from the Years Since - Timeless Texts plugin. This scan only finds references and never edits your content.', 'alp-dynamic-time-countdowns' ); ?></p>
			<?php if ( empty( $scan_items ) ) : ?>
				<p><?php esc_html_e( 'No matching content in this batch.', 'alp-dynamic-time-countdowns' ); ?></p>
			<?php else : ?>
				<ul class="alpdt-scan">
				<?php foreach ( $scan_items as $item ) : ?>
					<?php
					if ( ! current_user_can( 'edit_post', (int) $item->ID ) ) {
						continue;
					}
					$edit = get_edit_post_link( (int) $item->ID );
					if ( $edit ) :
						?>
						<li><a href="<?php echo esc_url( $edit ); ?>"><?php echo esc_html( '' !== $item->post_title ? $item->post_title : '#' . $item->ID ); ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
				</ul>
				<?php if ( count( $scan_items ) === LegacyScanRepository::PER_PAGE && $scan_page < 100 ) : ?>
					<a class="button" href="<?php echo esc_url( add_query_arg( array( 'page' => $page, 'alpdt_scan_page' => $scan_page + 1 ), admin_url( 'admin.php' ) ) ); ?>#alpdt-panel-compat"><?php esc_html_e( 'Next batch', 'alp-dynamic-time-countdowns' ); ?></a>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="alpdt-card">
		<h3><?php esc_html_e( 'Runtime notes', 'alp-dynamic-time-countdowns' ); ?></h3>
		<p><?php echo esc_html( 'PHP ' . PHP_VERSION . ' · WordPress ' . get_bloginfo( 'version' ) . ' · ' . wp_timezone_string() ); ?></p>
		<p><?php esc_html_e( 'SEO filters: Yoast, Rank Math, SEOPress. Divi 4/5: inline shortcode in supported render contexts; Divi 5 standalone module is an experimental integration until verified on a live builder.', 'alp-dynamic-time-countdowns' ); ?></p>
		<p><?php esc_html_e( 'For year/anniversary rollover, configure page-cache expiration or use live="true" for visible numbers. This does not guarantee refreshed SEO meta cached by a CDN.', 'alp-dynamic-time-countdowns' ); ?></p>
	</div>
	<div class="alpdt-card alpdt-php-note" role="note">
		<h3><?php esc_html_e( 'Recommended PHP security version', 'alp-dynamic-time-countdowns' ); ?></h3>
		<p><strong><?php esc_html_e( 'PHP 8.2 or newer is recommended; use a patched release.', 'alp-dynamic-time-countdowns' ); ?></strong></p>
		<p><?php esc_html_e( 'For PHP2Shell (October 2026), use PHP 8.2.35+, 8.3.36+, 8.4.27+ or 8.5.12+, or a distribution-provided fixed package. PHP 8.2 security support ends on 31 December 2026; consider a supported newer branch. Changing PHP versions is a hosting task, not a plugin setting.', 'alp-dynamic-time-countdowns' ); ?></p>
		<p><a href="https://www.php.net/supported-versions.php" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Check official PHP support dates', 'alp-dynamic-time-countdowns' ); ?></a></p>
	</div>
</section>
<?php
};
