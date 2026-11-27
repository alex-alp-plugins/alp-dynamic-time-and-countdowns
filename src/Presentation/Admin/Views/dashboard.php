<?php
/**
 * Main admin dashboard template (hero, feedback, tabs, modals, ALP 3-zone footer).
 *
 * @package AlpDynamicTime\Presentation\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

return static function ( array $context, \AlpDynamicTime\Presentation\Admin\AdminView $view ): void {
	$notice   = isset( $context['notice'] ) && is_string( $context['notice'] ) ? $context['notice'] : '';
	$page     = isset( $context['page'] ) && is_string( $context['page'] ) ? $context['page'] : '';
	$messages = array(
	'saved'             => __( 'Date saved. You can reuse it anywhere on your site.', 'alp-dynamic-time-countdowns' ),
	'updated'           => __( 'Date updated. Every shortcode using this identifier will display the new value.', 'alp-dynamic-time-countdowns' ),
	'deleted'           => __( 'Date deleted.', 'alp-dynamic-time-countdowns' ),
	'invalid'           => __( 'Could not save. Check the date, required fields and identifier (it may already exist).', 'alp-dynamic-time-countdowns' ),
	'shortcode_saved'   => __( 'Shortcode saved. Copy its reusable identifier or its complete code.', 'alp-dynamic-time-countdowns' ),
	'shortcode_updated' => __( 'Shortcode updated. All pages using its reusable identifier will display the new configuration.', 'alp-dynamic-time-countdowns' ),
	'shortcode_deleted' => __( 'Saved shortcode deleted.', 'alp-dynamic-time-countdowns' ),
	'shortcode_invalid' => __( 'Could not save shortcode. Check the identifier, name, configuration and duplicates.', 'alp-dynamic-time-countdowns' ),
);
?>
<div class="wrap alpdt-admin">
	<div id="alpdt-external-notices" class="alpdt-external-notices" hidden></div>
	<header class="alpdt-hero">
		<div class="alpdt-hero-main">
			<p class="alpdt-eyebrow"><?php esc_html_e( 'ALP Plugins', 'alp-dynamic-time-countdowns' ); ?></p>
			<div class="alpdt-hero-title">
				<h1 class="alpdt-brand-heading">
					<img class="alpdt-hero-logo" src="<?php echo esc_url( ALPDT_URL . 'assets/branding/logo-white.svg' ); ?>" alt="" width="460" height="68" decoding="async">
					<span class="alpdt-visually-hidden"><?php esc_html_e( 'ALP Dynamic Time & Countdowns', 'alp-dynamic-time-countdowns' ); ?></span>
				</h1>
				<span class="alpdt-version"><?php echo esc_html( 'v' . ALPDT_VERSION ); ?></span>
			</div>
			<p class="alpdt-hero-description"><?php esc_html_e( 'Smart dates, reusable milestones and animated countdowns, all in one place.', 'alp-dynamic-time-countdowns' ); ?></p>
			<div class="alpdt-hero-meta" role="group" aria-label="<?php echo esc_attr__( 'Plugin highlights', 'alp-dynamic-time-countdowns' ); ?>" tabindex="0">
				<span class="alpdt-chip"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path d="m8 8-4 4 4 4m8-8 4 4-4 4M14 5l-4 14"/></svg><?php esc_html_e( 'Dynamic shortcodes', 'alp-dynamic-time-countdowns' ); ?></span>
				<span class="alpdt-chip"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><rect x="4" y="5" width="16" height="16" rx="2"/><path d="M8 3v4m8-4v4M4 10h16m-12 5 2 2 5-5"/></svg><?php esc_html_e( 'Reusable milestones', 'alp-dynamic-time-countdowns' ); ?></span>
				<span class="alpdt-chip"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path d="M3 9h18M3 15h18M8 4l-2 16M18 4l-2 16"/></svg><?php esc_html_e( 'SEO variables', 'alp-dynamic-time-countdowns' ); ?></span>
				<span class="alpdt-chip"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg><?php esc_html_e( 'Countdowns', 'alp-dynamic-time-countdowns' ); ?></span>
			</div>
		</div>
		<div class="alpdt-hero-art" aria-hidden="true"><span class="alpdt-clock-face"><span class="alpdt-clock-hand"></span><span class="alpdt-clock-minute-hand"></span></span><span class="alpdt-orbit alpdt-orbit-one"></span><span class="alpdt-orbit alpdt-orbit-two"></span></div>
	</header>
	<?php if ( isset( $messages[ $notice ] ) ) : ?>
		<?php $is_error = in_array( $notice, array( 'invalid', 'shortcode_invalid' ), true ); ?>
		<div class="<?php echo esc_attr( $is_error ? 'alpdt-feedback alpdt-feedback-error' : 'alpdt-feedback alpdt-feedback-success' ); ?>" role="<?php echo esc_attr( $is_error ? 'alert' : 'status' ); ?>" data-alpdt-feedback data-alpdt-feedback-type="<?php echo esc_attr( $is_error ? 'error' : 'success' ); ?>">
			<span><?php echo esc_html( $messages[ $notice ] ); ?></span>
		</div>
	<?php endif; ?>
	<div role="tablist" aria-label="<?php echo esc_attr__( 'ALP Dynamic Time & Countdowns', 'alp-dynamic-time-countdowns' ); ?>" class="alpdt-tabs">
		<button type="button" role="tab" id="alpdt-tab-create" aria-controls="alpdt-panel-create" aria-selected="true" tabindex="0" data-alpdt-tab="create"><span class="alpdt-tab-icon alpdt-tab-icon--brackets" aria-hidden="true"></span><span class="alpdt-tab-label"><?php esc_html_e( 'Generator', 'alp-dynamic-time-countdowns' ); ?></span></button>
		<button type="button" role="tab" id="alpdt-tab-shortcodes" aria-controls="alpdt-panel-shortcodes" aria-selected="false" tabindex="-1" data-alpdt-tab="shortcodes"><span class="alpdt-tab-icon alpdt-tab-icon--bookmark" aria-hidden="true"></span><span class="alpdt-tab-label"><?php esc_html_e( 'Saved shortcodes', 'alp-dynamic-time-countdowns' ); ?></span></button>
		<button type="button" role="tab" id="alpdt-tab-dates" aria-controls="alpdt-panel-dates" aria-selected="false" tabindex="-1" data-alpdt-tab="dates"><span class="alpdt-tab-icon alpdt-tab-icon--calendar" aria-hidden="true"></span><span class="alpdt-tab-label"><?php esc_html_e( 'Saved dates', 'alp-dynamic-time-countdowns' ); ?></span></button>
		<button type="button" role="tab" id="alpdt-tab-compat" aria-controls="alpdt-panel-compat" aria-selected="false" tabindex="-1" data-alpdt-tab="compat"><span class="alpdt-tab-icon alpdt-tab-icon--compatibility" aria-hidden="true"></span><span class="alpdt-tab-label"><?php esc_html_e( 'Compatibility', 'alp-dynamic-time-countdowns' ); ?></span></button>
		<button type="button" role="tab" id="alpdt-tab-help" aria-controls="alpdt-panel-help" aria-selected="false" tabindex="-1" data-alpdt-tab="help"><span class="alpdt-tab-icon alpdt-tab-icon--book" aria-hidden="true"></span><span class="alpdt-tab-label"><?php esc_html_e( 'About & guide', 'alp-dynamic-time-countdowns' ); ?></span></button>
	</div>
	<?php
	$view->partial( 'tab-generator', $context );
	$view->partial( 'tab-shortcodes', $context );
	$view->partial( 'tab-dates', $context );
	$view->partial( 'tab-compat', $context );
	?>
	<div id="alpdt-copy-notice" class="alpdt-copy-notice" role="status" aria-live="polite" aria-atomic="true" hidden>
		<span class="alpdt-copy-notice-mark" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span>
		<span id="alpdt-copy-notice-text"></span>
	</div>
	<dialog id="alpdt-save-shortcode-dialog" class="alpdt-dialog alpdt-save-dialog" aria-labelledby="alpdt-save-shortcode-title" aria-describedby="alpdt-save-shortcode-description">
		<h2 id="alpdt-save-shortcode-title"><?php esc_html_e( 'Save shortcode', 'alp-dynamic-time-countdowns' ); ?></h2>
		<p id="alpdt-save-shortcode-description"><?php esc_html_e( 'Name this configuration to find, copy or edit it later.', 'alp-dynamic-time-countdowns' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . $page ) ); ?>" id="alpdt-save-shortcode-form">
			<?php wp_nonce_field( 'alpdt_save', 'alpdt_nonce' ); ?>
			<input type="hidden" name="alpdt_action" value="save_shortcode"/>
			<input type="hidden" name="alpdt_original_slug" id="alpdt-shortcode-original" value=""/>
			<input type="hidden" name="alpdt_code" id="alpdt-shortcode-to-save" value=""/>
			<div class="alpdt-field"><label for="alpdt-shortcode-name"><?php esc_html_e( 'Shortcode name', 'alp-dynamic-time-countdowns' ); ?></label><input required maxlength="100" name="alpdt_name" id="alpdt-shortcode-name" placeholder="New Year countdown"/></div>
			<div class="alpdt-field"><label for="alpdt-shortcode-slug"><?php esc_html_e( 'Identifier', 'alp-dynamic-time-countdowns' ); ?></label><input required maxlength="55" pattern="[a-z0-9][a-z0-9_\-]*" name="alpdt_slug" id="alpdt-shortcode-slug" aria-describedby="alpdt-shortcode-slug-help" spellcheck="false" autocapitalize="off" autocomplete="off" placeholder="new-year-countdown"/><p class="alpdt-field-help" id="alpdt-shortcode-slug-help"><?php esc_html_e( 'Use lowercase letters, numbers, hyphens (-) or underscores (_), without spaces. Example: alex-ingreso. Spaces become hyphens automatically. The identifier cannot be changed after saving.', 'alp-dynamic-time-countdowns' ); ?></p></div>
			<div class="alpdt-dialog-actions"><button class="button" type="button" id="alpdt-save-shortcode-cancel"><?php esc_html_e( 'Cancel', 'alp-dynamic-time-countdowns' ); ?></button><button class="button button-primary" type="submit"><?php esc_html_e( 'Save configuration', 'alp-dynamic-time-countdowns' ); ?></button></div>
		</form>
	</dialog>
	<?php $view->partial( 'tab-help', $context ); ?>
	<footer class="alpdt-brand-footer" aria-label="<?php echo esc_attr__( 'ALP Dynamic Time & Countdowns', 'alp-dynamic-time-countdowns' ); ?>">
		<span class="alpdt-footer-product"><img class="alpdt-footer-logo" src="<?php echo esc_url( ALPDT_URL . 'assets/branding/logo-color.svg' ); ?>" alt="<?php echo esc_attr__( 'ALP Dynamic Time & Countdowns', 'alp-dynamic-time-countdowns' ); ?>" width="260" height="38" loading="lazy" decoding="async"></span>
		<span class="alpdt-footer-review"><a class="alpdt-footer-review-link" href="https://wordpress.org/support/plugin/alp-dynamic-time-countdowns/reviews/#new-post" target="_blank" rel="noopener noreferrer"><svg class="alpdt-footer-review-star" viewBox="0 0 24 24" width="17" height="17" aria-hidden="true" focusable="false"><path d="M12 4L14.12 10.09L20.56 10.22L15.42 14.11L17.29 20.28L12 16.6L6.71 20.28L8.58 14.11L3.44 10.22L9.88 10.09Z" fill="#F5C542" stroke="#CA8C06" stroke-width="1.15" stroke-linejoin="round"/></svg><span class="alpdt-footer-review-text"><?php esc_html_e( 'Enjoying the plugin? Help us with a review!', 'alp-dynamic-time-countdowns' ); ?></span></a></span>
		<span class="alpdt-footer-credit"><span class="alpdt-footer-company"><img class="alpdt-footer-company-logo" src="<?php echo esc_url( ALPDT_URL . 'assets/branding/alp-plugins-logo.svg' ); ?>" alt="<?php echo esc_attr__( 'ALP Plugins', 'alp-dynamic-time-countdowns' ); ?>" width="164" height="28" loading="lazy" decoding="async"><span class="alpdt-visually-hidden"><?php esc_html_e( 'ALP Plugins', 'alp-dynamic-time-countdowns' ); ?></span></span></span>
	</footer>
</div>
<?php
};
