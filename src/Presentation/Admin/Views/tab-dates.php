<?php
/**
 * Saved Dates tab template.
 *
 * @package AlpDynamicTime\Presentation\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

return static function ( array $context, \AlpDynamicTime\Presentation\Admin\AdminView $view ): void {
	$presets = is_array( $context['presets'] ?? null ) ? $context['presets'] : array();
	$page    = is_string( $context['page'] ?? null ) ? $context['page'] : '';
?>
<section role="tabpanel" tabindex="0" id="alpdt-panel-dates" aria-labelledby="alpdt-tab-dates" class="alpdt-panel" hidden>
	<div class="alpdt-section-intro"><h2><?php esc_html_e( 'Saved dates', 'alp-dynamic-time-countdowns' ); ?></h2><p><?php esc_html_e( 'Save an important date once, like the founding of your company. Use its shortcode in as many places as you like. If the date changes, edit it here and all those places will use the new date automatically.', 'alp-dynamic-time-countdowns' ); ?></p></div>
	<div class="alpdt-columns">
		<div class="alpdt-card">
			<div class="alpdt-card-heading"><span class="alpdt-step">1</span><h3 id="alpdt-form-heading"><?php esc_html_e( 'Save reusable date', 'alp-dynamic-time-countdowns' ); ?></h3></div>
			<p id="alpdt-edit-state" class="alpdt-info" hidden><?php esc_html_e( 'Editing a saved date. Its identifier stays the same so your existing shortcodes keep working.', 'alp-dynamic-time-countdowns' ); ?></p>
			<form id="alpdt-milestone-form" method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . $page ) ); ?>">
				<?php wp_nonce_field( 'alpdt_save', 'alpdt_nonce' ); ?><input type="hidden" name="alpdt_action" value="save"/><input type="hidden" name="alpdt_original_slug" id="alpdt-original" value=""/>
				<div class="alpdt-field"><label for="alpdt-slug"><?php esc_html_e( 'Identifier', 'alp-dynamic-time-countdowns' ); ?></label><input required maxlength="55" pattern="[a-z0-9_\-]+" name="alpdt_slug" id="alpdt-slug" aria-describedby="alpdt-slug-help" spellcheck="false" autocapitalize="off" autocomplete="off" placeholder="company-founded"/><p class="alpdt-field-help" id="alpdt-slug-help"><?php esc_html_e( 'Use lowercase letters, numbers, hyphens (-) or underscores (_), without spaces. Example: alex-ingreso. Spaces become hyphens automatically. The identifier cannot be changed after saving.', 'alp-dynamic-time-countdowns' ); ?></p></div>
				<div class="alpdt-field"><label for="alpdt-label"><?php esc_html_e( 'Label', 'alp-dynamic-time-countdowns' ); ?></label><input required maxlength="100" name="alpdt_label" id="alpdt-label" placeholder="Company founded"/><p class="alpdt-field-help"><?php esc_html_e( 'A friendly name to help you recognize this date in the list.', 'alp-dynamic-time-countdowns' ); ?></p></div>
				<div class="alpdt-field"><label for="alpdt-date"><?php esc_html_e( 'Date', 'alp-dynamic-time-countdowns' ); ?></label><input required name="alpdt_date" id="alpdt-date" type="date"/><p class="alpdt-field-help"><?php esc_html_e( 'Select the exact date of the event, such as the company founding day.', 'alp-dynamic-time-countdowns' ); ?></p></div>
				<div class="alpdt-form-actions"><button id="alpdt-save" type="submit" class="button button-primary"><?php esc_html_e( 'Save date', 'alp-dynamic-time-countdowns' ); ?></button><button id="alpdt-cancel-edit" type="button" class="button" hidden><?php esc_html_e( 'Cancel editing', 'alp-dynamic-time-countdowns' ); ?></button></div>
			</form>
		</div>
		<div class="alpdt-card">
			<div class="alpdt-card-heading"><span class="alpdt-step">2</span><h3><?php esc_html_e( 'Your saved dates', 'alp-dynamic-time-countdowns' ); ?></h3></div>
			<?php if ( empty( $presets ) ) : ?>
				<div class="alpdt-empty"><p><?php esc_html_e( 'You have not saved any dates yet. Add your first date using the form.', 'alp-dynamic-time-countdowns' ); ?></p></div>
			<?php else : ?>
				<?php
				/* translators: %d: number of saved date presets. */
				$date_count_label = sprintf( _n( '%d saved date', '%d saved dates', count( $presets ), 'alp-dynamic-time-countdowns' ), count( $presets ) );
				?>
				<div class="alpdt-list-toolbar"><span><?php echo esc_html( $date_count_label ); ?></span><a class="button" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'page' => $page, 'alpdt_export' => 1 ), admin_url( 'admin.php' ) ), 'alpdt_export' ) ); ?>"><?php esc_html_e( 'Export JSON', 'alp-dynamic-time-countdowns' ); ?></a></div>
				<div class="alpdt-presets">
				<?php foreach ( $presets as $slug => $p ) : ?>
					<?php
					if ( ! is_array( $p ) || empty( $p['date'] ) ) {
						continue;
					}
					/* translators: %s: identifier of the saved date that can be edited. */
					$edit_date_label = sprintf( __( 'Edit %s', 'alp-dynamic-time-countdowns' ), $slug );
					/* translators: %s: identifier of the saved date that can be deleted. */
					$delete_date_label = sprintf( __( 'Delete %s', 'alp-dynamic-time-countdowns' ), $slug );
					?>
					<div class="alpdt-preset">
						<div class="alpdt-preset-content"><strong><?php echo esc_html( isset( $p['label'] ) ? $p['label'] : $slug ); ?></strong><small><?php echo esc_html( $p['date'] ); ?></small><code><?php echo esc_html( '[alpdt_time id="' . $slug . '"]' ); ?></code></div>
						<div class="alpdt-preset-actions"><?php $view->copy_button( '[alpdt_time id="' . $slug . '"]' ); ?><button class="button alpdt-edit" type="button" data-alpdt-edit data-slug="<?php echo esc_attr( $slug ); ?>" data-date="<?php echo esc_attr( $p['date'] ); ?>" data-label="<?php echo esc_attr( isset( $p['label'] ) ? $p['label'] : $slug ); ?>" aria-label="<?php echo esc_attr( $edit_date_label ); ?>"><?php esc_html_e( 'Edit', 'alp-dynamic-time-countdowns' ); ?></button><form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . $page ) ); ?>" data-alpdt-delete><input type="hidden" name="alpdt_action" value="delete"/><input type="hidden" name="alpdt_slug" value="<?php echo esc_attr( $slug ); ?>"/><?php wp_nonce_field( 'alpdt_save', 'alpdt_nonce' ); ?><button class="button alpdt-danger" type="submit" aria-label="<?php echo esc_attr( $delete_date_label ); ?>"><?php esc_html_e( 'Delete', 'alp-dynamic-time-countdowns' ); ?></button></form></div>
					</div>
				<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php
};
