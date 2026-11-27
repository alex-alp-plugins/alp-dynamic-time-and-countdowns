<?php
/**
 * Saved Shortcodes tab template.
 *
 * @package AlpDynamicTime\Presentation\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

use AlpDynamicTime\Domain\ShortcodeValidator;
use AlpDynamicTime\Infrastructure\Integrations\SeoIntegration;
use AlpDynamicTime\Plugin;

return static function ( array $context, \AlpDynamicTime\Presentation\Admin\AdminView $view ): void {
	$stored_shortcodes  = is_array( $context['stored_shortcodes'] ?? null ) ? $context['stored_shortcodes'] : array();
	$visible_shortcodes = is_array( $context['visible_shortcodes'] ?? null ) ? $context['visible_shortcodes'] : array();
	$shortcode_pages    = (int) ( $context['shortcode_pages'] ?? 1 );
	$shortcode_page     = (int) ( $context['shortcode_page'] ?? 1 );
	$shortcode_count    = (int) ( $context['shortcode_count'] ?? count( $stored_shortcodes ) );
	$page               = is_string( $context['page'] ?? null ) ? $context['page'] : '';
?>
<section role="tabpanel" tabindex="0" id="alpdt-panel-shortcodes" aria-labelledby="alpdt-tab-shortcodes" class="alpdt-panel" hidden>
	<div class="alpdt-section-intro"><h2><?php esc_html_e( 'Saved shortcodes', 'alp-dynamic-time-countdowns' ); ?></h2><p><?php esc_html_e( 'Save your complete countdowns and dynamic texts with a name. Copy the reusable identifier to update every linked page when you edit its configuration.', 'alp-dynamic-time-countdowns' ); ?></p></div>
	<div class="alpdt-card">
		<?php
		/* translators: %d: number of valid reusable shortcode configurations. */
		$saved_count_label = sprintf( _n( '%d saved shortcode', '%d saved shortcodes', count( $stored_shortcodes ), 'alp-dynamic-time-countdowns' ), count( $stored_shortcodes ) );
		?>
		<div class="alpdt-list-toolbar"><strong><?php echo esc_html( $saved_count_label ); ?></strong><button type="button" class="button button-primary" data-alpdt-open-generator><?php esc_html_e( 'Create new shortcode', 'alp-dynamic-time-countdowns' ); ?></button></div>
		<?php if ( empty( $stored_shortcodes ) ) : ?>
			<div class="alpdt-empty"><p><?php esc_html_e( 'No saved shortcodes yet. Create one in Generator and select Save shortcode.', 'alp-dynamic-time-countdowns' ); ?></p></div>
		<?php else : ?>
			<?php
			/** @var ShortcodeValidator $validator */
			$validator = Plugin::instance()->container()->get( ShortcodeValidator::class );
			?>
			<div class="alpdt-presets alpdt-shortcodes-unclipped">
			<?php foreach ( $visible_shortcodes as $slug => $item ) : ?>
				<?php
				$stored_attrs = is_string( $slug ) && is_array( $item ) && isset( $item['code'], $item['name'] ) ? $validator->parse( $item['code'] ) : null;
				if ( null === $stored_attrs ) {
					continue;
				}
				$alias_shortcode = '[alpdt_time saved="' . $slug . '"]';
				?>
				<div class="alpdt-preset alpdt-shortcode-item">
					<div class="alpdt-preset-content">
						<strong><?php echo esc_html( $item['name'] ); ?></strong>
						<small><?php esc_html_e( 'Reusable shortcode', 'alp-dynamic-time-countdowns' ); ?></small>
						<code><?php echo esc_html( $alias_shortcode ); ?></code>
						<div class="alpdt-saved-code"><details><summary><?php esc_html_e( 'Show complete configuration', 'alp-dynamic-time-countdowns' ); ?></summary><code><?php echo esc_html( $item['code'] ); ?></code><?php $view->copy_button( $item['code'] ); ?></details></div>
						<?php if ( 'countdown' !== $stored_attrs['type'] ) : ?>
							<?php $seo_name = SeoIntegration::saved_variable_name( $slug ); ?>
							<details class="alpdt-seo-saved">
								<summary><?php esc_html_e( 'SEO variables for this text', 'alp-dynamic-time-countdowns' ); ?></summary>
								<p class="alpdt-field-help"><?php esc_html_e( 'Optional: use these variables in the SEO title or description of the selected plugin.', 'alp-dynamic-time-countdowns' ); ?></p>
								<div class="alpdt-seo-saved-inner"><span>Rank Math</span><code><?php echo esc_html( '%' . $seo_name . '%' ); ?></code><?php $view->copy_button( '%' . $seo_name . '%', true ); ?><span>Yoast SEO</span><code><?php echo esc_html( '%%' . $seo_name . '%%' ); ?></code><?php $view->copy_button( '%%' . $seo_name . '%%', true ); ?></div>
							</details>
						<?php endif; ?>
					</div>
					<div class="alpdt-preset-actions alpdt-action-toolbar">
						<?php $view->copy_button( $alias_shortcode ); ?>
						<button class="button" type="button" data-alpdt-edit-shortcode data-slug="<?php echo esc_attr( $slug ); ?>" data-name="<?php echo esc_attr( $item['name'] ); ?>" data-code="<?php echo esc_attr( $item['code'] ); ?>"><?php esc_html_e( 'Edit', 'alp-dynamic-time-countdowns' ); ?></button>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . $page ) ); ?>" data-alpdt-delete data-alpdt-delete-kind="shortcode"><input type="hidden" name="alpdt_action" value="delete_shortcode"/><input type="hidden" name="alpdt_slug" value="<?php echo esc_attr( $slug ); ?>"/><?php wp_nonce_field( 'alpdt_save', 'alpdt_nonce' ); ?><button class="button alpdt-danger" type="submit"><?php esc_html_e( 'Delete', 'alp-dynamic-time-countdowns' ); ?></button></form>
					</div>
				</div>
			<?php endforeach; ?>
			</div>
			<?php if ( $shortcode_pages > 1 ) : ?>
				<nav class="alpdt-pagination" aria-label="<?php echo esc_attr__( 'Saved shortcode pages', 'alp-dynamic-time-countdowns' ); ?>">
					<span class="alpdt-pagination-summary"><?php echo esc_html( sprintf( /* translators: 1: first visible item, 2: last visible item, 3: total saved shortcodes. */ __( 'Showing %1$d–%2$d of %3$d', 'alp-dynamic-time-countdowns' ), ( $shortcode_page - 1 ) * 10 + 1, min( $shortcode_page * 10, $shortcode_count ), $shortcode_count ) ); ?></span>
					<div class="alpdt-pagination-pages">
						<?php for ( $page_num = 1; $page_num <= $shortcode_pages; $page_num++ ) : ?>
							<?php $page_url = add_query_arg( array( 'page' => $page, 'alpdt_view' => 'shortcodes', 'alpdt_saved_page' => $page_num ), admin_url( 'admin.php' ) ); ?>
							<a class="<?php echo esc_attr( $page_num === $shortcode_page ? 'button alpdt-current-page' : 'button' ); ?>" href="<?php echo esc_url( $page_url ); ?>" <?php if ( $page_num === $shortcode_page ) : ?>aria-current="page"<?php endif; ?> aria-label="<?php echo esc_attr( sprintf( /* translators: %d: saved shortcode page number. */ __( 'Page %d', 'alp-dynamic-time-countdowns' ), $page_num ) ); ?>"><?php echo esc_html( (string) $page_num ); ?></a>
						<?php endfor; ?>
					</div>
				</nav>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
<?php
};
