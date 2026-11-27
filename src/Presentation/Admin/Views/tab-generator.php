<?php
/**
 * Generator tab template.
 *
 * @package AlpDynamicTime\Presentation\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

return static function ( array $context, \AlpDynamicTime\Presentation\Admin\AdminView $view ): void {
	$presets = is_array( $context['presets'] ?? null ) ? $context['presets'] : array();
?>
<section role="tabpanel" tabindex="0" id="alpdt-panel-create" aria-labelledby="alpdt-tab-create" class="alpdt-panel">
	<div class="alpdt-section-intro"><h2><?php esc_html_e( 'Shortcode generator', 'alp-dynamic-time-countdowns' ); ?></h2><p><?php esc_html_e( 'Choose what you want to show, check the result, then copy the shortcode into a page, post or compatible Divi text field.', 'alp-dynamic-time-countdowns' ); ?></p></div>
	<div class="alpdt-columns">
		<div class="alpdt-card alpdt-generator-card">
			<div class="alpdt-card-heading"><span class="alpdt-step">1</span><h3><?php esc_html_e( 'Choose the date and format', 'alp-dynamic-time-countdowns' ); ?></h3></div>
			<div class="alpdt-field">
				<label for="alpdt-type"><?php esc_html_e( 'What do you want to display?', 'alp-dynamic-time-countdowns' ); ?></label>
				<select id="alpdt-type" aria-describedby="alpdt-type-help">
					<option value="elapsed"><?php esc_html_e( 'Elapsed time', 'alp-dynamic-time-countdowns' ); ?></option>
					<option value="year"><?php esc_html_e( 'Current year', 'alp-dynamic-time-countdowns' ); ?></option>
					<option value="month"><?php esc_html_e( 'Current month', 'alp-dynamic-time-countdowns' ); ?></option>
					<option value="date"><?php esc_html_e( 'Current date', 'alp-dynamic-time-countdowns' ); ?></option>
					<option value="smart_date"><?php esc_html_e( 'Smart date / calendar expression', 'alp-dynamic-time-countdowns' ); ?></option>
					<option value="countdown"><?php esc_html_e( 'Countdown', 'alp-dynamic-time-countdowns' ); ?></option>
					<option value="copyright"><?php esc_html_e( 'Copyright years', 'alp-dynamic-time-countdowns' ); ?></option>
					<option value="ordinal"><?php esc_html_e( 'Anniversary number', 'alp-dynamic-time-countdowns' ); ?></option>
					<option value="anniversary_message"><?php esc_html_e( 'Anniversary message', 'alp-dynamic-time-countdowns' ); ?></option>
				</select>
				<p id="alpdt-type-help" class="alpdt-field-help"></p>
			</div>
			<?php if ( $presets ) : ?>
			<div class="alpdt-field" data-alpdt-field="saved">
				<label for="alpdt-saved"><?php esc_html_e( 'Use a saved date (optional)', 'alp-dynamic-time-countdowns' ); ?></label>
				<select id="alpdt-saved">
					<option value=""><?php esc_html_e( 'Enter a date below', 'alp-dynamic-time-countdowns' ); ?></option>
					<?php foreach ( $presets as $slug => $p ) : ?>
						<?php
						if ( ! is_array( $p ) || empty( $p['date'] ) ) {
							continue;
						}
						?>
						<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( isset( $p['label'] ) ? $p['label'] : $slug ); ?></option>
					<?php endforeach; ?>
				</select>
				<p class="alpdt-field-help"><?php esc_html_e( 'Saved dates can be changed once and updated everywhere.', 'alp-dynamic-time-countdowns' ); ?></p>
			</div>
			<?php endif; ?>
			<div class="alpdt-field" data-alpdt-field="from">
				<label for="alpdt-from"><?php esc_html_e( 'Start year or date', 'alp-dynamic-time-countdowns' ); ?></label>
				<input id="alpdt-from" type="text" value="2000" maxlength="10" placeholder="2000 or 2000-12-15 (YYYY-MM-DD)" aria-describedby="alpdt-from-help"/>
				<p id="alpdt-from-help" class="alpdt-field-help"><?php esc_html_e( 'Enter a year such as 2000, or a full date in YYYY-MM-DD order. Example: 2000-12-15 means year 2000, month 12 (December), day 15. The middle number is always the month; the final number is always the day.', 'alp-dynamic-time-countdowns' ); ?></p>
			</div>
			<div class="alpdt-field" data-alpdt-field="to">
				<label for="alpdt-to"><?php esc_html_e( 'Event or end date and time', 'alp-dynamic-time-countdowns' ); ?></label>
				<input id="alpdt-to" type="datetime-local" step="60" value="2027-01-01T12:00" aria-describedby="alpdt-to-help"/>
				<p id="alpdt-to-help" class="alpdt-field-help"><?php esc_html_e( 'Choose the exact day and hour of your event. The clock follows the time zone set in WordPress Settings → General. For example, 15 December 2026 at 18:30.', 'alp-dynamic-time-countdowns' ); ?></p>
			</div>
			<fieldset class="alpdt-field" data-alpdt-field="date_difference" hidden>
				<legend><?php esc_html_e( 'Dates to compare', 'alp-dynamic-time-countdowns' ); ?></legend>
				<div class="alpdt-date-difference-grid">
					<div><label for="alpdt-difference-from"><?php esc_html_e( 'Start date', 'alp-dynamic-time-countdowns' ); ?></label><input type="date" id="alpdt-difference-from" value="2026-01-01"/></div>
					<div><label for="alpdt-difference-to"><?php esc_html_e( 'End date', 'alp-dynamic-time-countdowns' ); ?></label><input type="date" id="alpdt-difference-to" value="2026-12-31"/></div>
				</div>
				<p class="alpdt-field-help"><?php esc_html_e( 'Displays signed calendar days. A negative value means the end date precedes the start date.', 'alp-dynamic-time-countdowns' ); ?></p>
			</fieldset>
			<div class="alpdt-field" data-alpdt-field="live" hidden>
				<label class="alpdt-check" for="alpdt-live"><input type="checkbox" id="alpdt-live"/><span><?php esc_html_e( 'Refresh the displayed date after cached pages load', 'alp-dynamic-time-countdowns' ); ?></span></label>
				<p class="alpdt-field-help"><?php esc_html_e( 'Useful with page caching. Dates from individual posts cannot be refreshed through the public endpoint.', 'alp-dynamic-time-countdowns' ); ?></p>
			</div>
			<div class="alpdt-field" data-alpdt-field="smart_expr" hidden>
				<label for="alpdt-expression"><?php esc_html_e( 'Date expression', 'alp-dynamic-time-countdowns' ); ?></label>
				<select id="alpdt-expression">
					<?php
					foreach ( array(
						'today'           => __( 'Today', 'alp-dynamic-time-countdowns' ),
						'tomorrow'        => __( 'Tomorrow', 'alp-dynamic-time-countdowns' ),
						'in_7_days'       => __( 'In 7 days', 'alp-dynamic-time-countdowns' ),
						'in_30_days'      => __( 'In 30 days', 'alp-dynamic-time-countdowns' ),
						'next_monday'     => __( 'Next Monday', 'alp-dynamic-time-countdowns' ),
						'next_tuesday'    => __( 'Next Tuesday', 'alp-dynamic-time-countdowns' ),
						'next_wednesday'  => __( 'Next Wednesday', 'alp-dynamic-time-countdowns' ),
						'next_thursday'   => __( 'Next Thursday', 'alp-dynamic-time-countdowns' ),
						'next_friday'     => __( 'Next Friday', 'alp-dynamic-time-countdowns' ),
						'next_saturday'   => __( 'Next Saturday', 'alp-dynamic-time-countdowns' ),
						'next_sunday'     => __( 'Next Sunday', 'alp-dynamic-time-countdowns' ),
						'month_end'       => __( 'Last day of this month', 'alp-dynamic-time-countdowns' ),
						'quarter'         => __( 'Current quarter (1–4)', 'alp-dynamic-time-countdowns' ),
						'season'          => __( 'Season of the year', 'alp-dynamic-time-countdowns' ),
						'post_published'  => __( 'Current post publication date', 'alp-dynamic-time-countdowns' ),
						'post_modified'   => __( 'Current post last modified', 'alp-dynamic-time-countdowns' ),
						'date_difference' => __( 'Days between two dates', 'alp-dynamic-time-countdowns' ),
					) as $key => $expr_label ) :
						?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $expr_label ); ?></option>
					<?php endforeach; ?>
				</select>
				<p id="alpdt-expression-help" class="alpdt-field-help"><?php esc_html_e( 'Uses your WordPress timezone. Most expressions need no manually entered date. Publication and modification dates require a published post or page.', 'alp-dynamic-time-countdowns' ); ?></p>
			</div>
			<div class="alpdt-field" data-alpdt-field="post_preview_date" hidden>
				<label for="alpdt-preview-post-date"><?php esc_html_e( 'Example post date (preview only)', 'alp-dynamic-time-countdowns' ); ?></label>
				<input id="alpdt-preview-post-date" type="date" value="2026-10-25" aria-describedby="alpdt-preview-post-help" />
				<p id="alpdt-preview-post-help" class="alpdt-field-help"><?php esc_html_e( 'Choose any sample date to test the format here. This value is never saved in the shortcode: on a published post or page, its real publication or modification date is displayed automatically.', 'alp-dynamic-time-countdowns' ); ?></p>
			</div>
			<div class="alpdt-field" data-alpdt-field="hemisphere" hidden>
				<label for="alpdt-hemisphere"><?php esc_html_e( 'Hemisphere', 'alp-dynamic-time-countdowns' ); ?></label>
				<select id="alpdt-hemisphere"><option value="north"><?php esc_html_e( 'Northern', 'alp-dynamic-time-countdowns' ); ?></option><option value="south"><?php esc_html_e( 'Southern', 'alp-dynamic-time-countdowns' ); ?></option></select>
			</div>
			<div class="alpdt-field" data-alpdt-field="recurrence" hidden>
				<label for="alpdt-recurrence"><?php esc_html_e( 'Countdown schedule', 'alp-dynamic-time-countdowns' ); ?></label>
				<select id="alpdt-recurrence"><option value="none"><?php esc_html_e( 'Fixed event date', 'alp-dynamic-time-countdowns' ); ?></option><option value="daily"><?php esc_html_e( 'Every day', 'alp-dynamic-time-countdowns' ); ?></option><option value="weekly"><?php esc_html_e( 'Every week', 'alp-dynamic-time-countdowns' ); ?></option><option value="monthly"><?php esc_html_e( 'Every month', 'alp-dynamic-time-countdowns' ); ?></option></select>
				<p class="alpdt-field-help"><?php esc_html_e( 'Repeats in the WordPress timezone. A nonexistent daylight-saving time is skipped. Monthly day 31 means the last day of shorter months.', 'alp-dynamic-time-countdowns' ); ?></p>
			</div>
			<div class="alpdt-field" data-alpdt-field="repeat_time" hidden><label for="alpdt-repeat-time"><?php esc_html_e( 'Repeats at local time', 'alp-dynamic-time-countdowns' ); ?></label><input id="alpdt-repeat-time" type="time" value="12:00" step="60"/></div>
			<div class="alpdt-field" data-alpdt-field="repeat_weekday" hidden><label for="alpdt-repeat-weekday"><?php esc_html_e( 'Day of the week', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-repeat-weekday"><?php foreach ( array( __( 'Monday', 'alp-dynamic-time-countdowns' ), __( 'Tuesday', 'alp-dynamic-time-countdowns' ), __( 'Wednesday', 'alp-dynamic-time-countdowns' ), __( 'Thursday', 'alp-dynamic-time-countdowns' ), __( 'Friday', 'alp-dynamic-time-countdowns' ), __( 'Saturday', 'alp-dynamic-time-countdowns' ), __( 'Sunday', 'alp-dynamic-time-countdowns' ) ) as $index => $day ) : ?><option value="<?php echo esc_attr( (string) ( $index + 1 ) ); ?>"><?php echo esc_html( $day ); ?></option><?php endforeach; ?></select></div>
			<div class="alpdt-field" data-alpdt-field="repeat_day" hidden><label for="alpdt-repeat-day"><?php esc_html_e( 'Day of the month (1–31)', 'alp-dynamic-time-countdowns' ); ?></label><input id="alpdt-repeat-day" type="number" min="1" max="31" step="1" value="1"/></div>
			<div class="alpdt-field" data-alpdt-field="end_action" hidden><label for="alpdt-end-action"><?php esc_html_e( 'When fixed countdown finishes', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-end-action"><option value="none"><?php esc_html_e( 'Keep showing zero', 'alp-dynamic-time-countdowns' ); ?></option><option value="message"><?php esc_html_e( 'Show a message', 'alp-dynamic-time-countdowns' ); ?></option><option value="hide"><?php esc_html_e( 'Hide countdown', 'alp-dynamic-time-countdowns' ); ?></option><option value="redirect"><?php esc_html_e( 'Redirect to a page on this website', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Only for fixed dates. Recurring timers automatically continue to the next occurrence.', 'alp-dynamic-time-countdowns' ); ?></p></div>
			<div class="alpdt-field" data-alpdt-field="end_message" hidden><label for="alpdt-end-message"><?php esc_html_e( 'End message', 'alp-dynamic-time-countdowns' ); ?></label><input id="alpdt-end-message" type="text" maxlength="120" placeholder="Time is up!"/></div>
			<div class="alpdt-field" data-alpdt-field="end_url" hidden><label for="alpdt-end-url"><?php esc_html_e( 'Internal page path', 'alp-dynamic-time-countdowns' ); ?></label><input id="alpdt-end-url" type="text" maxlength="240" placeholder="/thank-you/" pattern="/[A-Za-z0-9/_-]*"/><p class="alpdt-field-help"><?php esc_html_e( 'Use an internal path starting with /, never an external address. Redirects occur only if the timer was running on the page.', 'alp-dynamic-time-countdowns' ); ?></p></div>
			<div class="alpdt-field" data-alpdt-field="countdown_display"><label for="alpdt-countdown-display"><?php esc_html_e( 'Countdown display', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-countdown-display"><option value="days"><?php esc_html_e( 'Days remaining (number only)', 'alp-dynamic-time-countdowns' ); ?></option><option value="full"><?php esc_html_e( 'Live clock: days, hours, minutes and seconds', 'alp-dynamic-time-countdowns' ); ?></option><option value="custom"><?php esc_html_e( 'Custom live clock: choose which units to display', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Live clock updates each second on the page without reloading it. The animation pauses if reduced motion is enabled.', 'alp-dynamic-time-countdowns' ); ?></p></div>
			<div class="alpdt-clock-settings" data-alpdt-field="clock_tabs">
				<div class="alpdt-clock-tabs" role="tablist" aria-label="<?php echo esc_attr__( 'Countdown settings', 'alp-dynamic-time-countdowns' ); ?>">
					<button type="button" role="tab" id="alpdt-clock-tab-units" aria-controls="alpdt-clock-panel-units" aria-selected="true" tabindex="0" data-alpdt-clock-tab="units"><?php esc_html_e( 'Countdown Units', 'alp-dynamic-time-countdowns' ); ?></button>
					<button type="button" role="tab" id="alpdt-clock-tab-style" aria-controls="alpdt-clock-panel-style" aria-selected="false" tabindex="-1" data-alpdt-clock-tab="style"><?php esc_html_e( 'Style', 'alp-dynamic-time-countdowns' ); ?></button>
					<button type="button" role="tab" id="alpdt-clock-tab-labels" aria-controls="alpdt-clock-panel-labels" aria-selected="false" tabindex="-1" data-alpdt-clock-tab="labels"><?php esc_html_e( 'Unit Labels', 'alp-dynamic-time-countdowns' ); ?></button>
				</div>
				<section class="alpdt-clock-panel" role="tabpanel" tabindex="0" id="alpdt-clock-panel-units" aria-labelledby="alpdt-clock-tab-units">
					<fieldset class="alpdt-field" data-alpdt-field="clock_units">
						<legend><?php esc_html_e( 'Choose countdown units', 'alp-dynamic-time-countdowns' ); ?></legend>
						<p class="alpdt-field-help"><?php esc_html_e( 'Select any combination. Unselected larger units are included in the next unit you choose. For example: months and days, or days and hours.', 'alp-dynamic-time-countdowns' ); ?></p>
						<div class="alpdt-clock-units">
							<?php foreach ( array( 'years' => __( 'Years', 'alp-dynamic-time-countdowns' ), 'months' => __( 'Months', 'alp-dynamic-time-countdowns' ), 'days' => __( 'Days', 'alp-dynamic-time-countdowns' ), 'hours' => __( 'Hours', 'alp-dynamic-time-countdowns' ), 'minutes' => __( 'Minutes', 'alp-dynamic-time-countdowns' ), 'seconds' => __( 'Seconds', 'alp-dynamic-time-countdowns' ) ) as $key => $title ) : ?>
								<label class="alpdt-check"><input type="checkbox" data-alpdt-clock-unit="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, array( 'days', 'hours', 'minutes', 'seconds' ), true ) ); ?>/><span><?php echo esc_html( $title ); ?></span></label>
							<?php endforeach; ?>
						</div>
					</fieldset>
				</section>
				<section class="alpdt-clock-panel" role="tabpanel" tabindex="0" id="alpdt-clock-panel-style" aria-labelledby="alpdt-clock-tab-style" hidden>
					<div class="alpdt-field" data-alpdt-field="clock_style">
						<label for="alpdt-clock-style" class="alpdt-clock-style-label"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 6.5V12l3.6 2.1"/></svg><span><?php esc_html_e( 'Clock appearance', 'alp-dynamic-time-countdowns' ); ?></span></label>
						<select id="alpdt-clock-style"><option value="simple"><?php esc_html_e( 'Minimal, inline text', 'alp-dynamic-time-countdowns' ); ?></option><option value="glow"><?php esc_html_e( 'Soft glow', 'alp-dynamic-time-countdowns' ); ?></option><option value="flip"><?php esc_html_e( 'Flip digits', 'alp-dynamic-time-countdowns' ); ?></option><option value="slide"><?php esc_html_e( 'Sliding numbers', 'alp-dynamic-time-countdowns' ); ?></option><option value="pulse"><?php esc_html_e( 'Gentle pulse', 'alp-dynamic-time-countdowns' ); ?></option><option value="sweep"><?php esc_html_e( 'Animated underline', 'alp-dynamic-time-countdowns' ); ?></option><option value="ring"><?php esc_html_e( 'Circular progress dial', 'alp-dynamic-time-countdowns' ); ?></option><option value="odometer"><?php esc_html_e( '3D Odometer', 'alp-dynamic-time-countdowns' ); ?></option><option value="neon"><?php esc_html_e( 'Neon Digital', 'alp-dynamic-time-countdowns' ); ?></option><option value="liquid"><?php esc_html_e( 'Liquid', 'alp-dynamic-time-countdowns' ); ?></option><option value="matrix"><?php esc_html_e( 'Pixel Matrix', 'alp-dynamic-time-countdowns' ); ?></option><option value="morph"><?php esc_html_e( 'Particle Morph', 'alp-dynamic-time-countdowns' ); ?></option><option value="orbit"><?php esc_html_e( 'Orbit Motion', 'alp-dynamic-time-countdowns' ); ?></option><option value="glitch"><?php esc_html_e( 'Cyber Glitch', 'alp-dynamic-time-countdowns' ); ?></option><option value="hourglass"><?php esc_html_e( 'Hourglass', 'alp-dynamic-time-countdowns' ); ?></option><option value="snowfall"><?php esc_html_e( 'Snowfall', 'alp-dynamic-time-countdowns' ); ?></option><option value="christmas_lights"><?php esc_html_e( 'Christmas Lights', 'alp-dynamic-time-countdowns' ); ?></option><option value="celebration"><?php esc_html_e( 'Fireworks celebration', 'alp-dynamic-time-countdowns' ); ?></option><option value="confetti"><?php esc_html_e( 'Celebration confetti & streamers', 'alp-dynamic-time-countdowns' ); ?></option><option value="blood"><?php esc_html_e( 'Blood Dripping', 'alp-dynamic-time-countdowns' ); ?></option></select>
						<p class="alpdt-field-help"><?php esc_html_e( 'Choose an appearance for your countdown. Use the Layout option below to place the first letter of each unit beside the number while keeping the selected style and animation. Decorative motion pauses off-screen and respects reduced-motion settings.', 'alp-dynamic-time-countdowns' ); ?></p>
					</div>
					<div class="alpdt-field" data-alpdt-field="clock_layout" hidden>
						<label for="alpdt-clock-layout"><?php esc_html_e( 'Countdown layout', 'alp-dynamic-time-countdowns' ); ?></label>
						<select id="alpdt-clock-layout"><option value="default"><?php esc_html_e( 'Default', 'alp-dynamic-time-countdowns' ); ?></option><option value="horizontal"><?php esc_html_e( 'Minimalist / Horizontal', 'alp-dynamic-time-countdowns' ); ?></option></select>
						<p class="alpdt-field-help"><?php esc_html_e( 'Shows the initial of each unit to the right of the number, without changing the selected clock animation. Ideal for compact promotional top bars.', 'alp-dynamic-time-countdowns' ); ?></p>
					</div>
					<div class="alpdt-celebration-settings" data-alpdt-field="celebration" hidden>
						<div class="alpdt-field"><label for="alpdt-celebration-message"><?php esc_html_e( 'Message when countdown ends', 'alp-dynamic-time-countdowns' ); ?></label><input type="text" id="alpdt-celebration-message" maxlength="120" value="" placeholder="<?php echo esc_attr__( 'It’s time to celebrate!', 'alp-dynamic-time-countdowns' ); ?>"/><p class="alpdt-field-help"><?php esc_html_e( 'Shown when the timer reaches zero. Leave empty for the default message.', 'alp-dynamic-time-countdowns' ); ?></p></div>
						<div class="alpdt-field"><label for="alpdt-celebration-mode"><?php esc_html_e( 'When to play celebration', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-celebration-mode"><option value="end"><?php esc_html_e( 'Once when countdown ends', 'alp-dynamic-time-countdowns' ); ?></option><option value="loop"><?php esc_html_e( 'Loop while counting down', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Loop plays only while the countdown is running and visible, with a pause between bursts. Reduced-motion preferences disable decorative motion.', 'alp-dynamic-time-countdowns' ); ?></p></div>
						<div class="alpdt-field"><label for="alpdt-celebration-duration"><?php esc_html_e( 'Celebration burst duration', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-celebration-duration"><option value="5" selected><?php esc_html_e( '5 seconds', 'alp-dynamic-time-countdowns' ); ?></option><option value="8"><?php esc_html_e( '8 seconds', 'alp-dynamic-time-countdowns' ); ?></option><option value="10"><?php esc_html_e( '10 seconds', 'alp-dynamic-time-countdowns' ); ?></option></select></div>
						<fieldset class="alpdt-field alpdt-celebration-palette" data-alpdt-confetti-palette>
							<legend><?php esc_html_e( 'Confetti colors', 'alp-dynamic-time-countdowns' ); ?></legend>
							<p class="alpdt-field-help"><?php esc_html_e( 'Customize confetti colors, and use Add confetti color for more variety.', 'alp-dynamic-time-countdowns' ); ?></p>
							<div class="alpdt-design-grid">
								<?php foreach ( array( '#FFCC00', '#F33B49', '#053197', '#07B57B' ) as $index => $color ) : ?>
									<?php
									$name           = 'confetti_' . ( $index + 1 ) . '_color';
									/* translators: %d: color position in the palette. */
									$caption        = sprintf( __( 'Confetti color %d', 'alp-dynamic-time-countdowns' ), $index + 1 );
									/* translators: %s: label of the color picker, e.g. Confetti color 1. */
									$choose_caption = sprintf( __( 'Choose %s', 'alp-dynamic-time-countdowns' ), $caption );
									?>
									<div class="alpdt-design-field alpdt-design-color">
										<label for="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>"><?php echo esc_html( $caption ); ?></label>
										<div class="alpdt-color-controls"><input type="text" maxlength="9" inputmode="text" spellcheck="false" autocomplete="off" pattern="#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?" id="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>" data-alpdt-design="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $color ); ?>" aria-label="<?php echo esc_attr( $caption . ' (HEX)' ); ?>"/><button type="button" class="alpdt-color-swatch" data-alpdt-color-picker="<?php echo esc_attr( $name ); ?>" data-alpdt-color="<?php echo esc_attr( $color ); ?>" aria-label="<?php echo esc_attr( $choose_caption ); ?>" aria-expanded="false" style="--alpdt-swatch:<?php echo esc_attr( $color ); ?>"><span aria-hidden="true"></span></button></div>
									</div>
								<?php endforeach; ?>
								<?php foreach ( range( 5, 10 ) as $extra_index ) : ?>
									<?php
									$extra_name    = 'confetti_' . $extra_index . '_color';
									$extra_default = array( '#F78AB5', '#7B5DE8', '#FF8844', '#6AC9FF', '#C3FC6D', '#FFFFFF' )[ ( $extra_index - 5 ) % 6 ];
									/* translators: %d: palette color position */
									$extra_label   = sprintf( __( 'Confetti color %d', 'alp-dynamic-time-countdowns' ), $extra_index );
									?>
									<div class="alpdt-design-field alpdt-design-color" data-alpdt-extra-color-field="confetti:<?php echo esc_attr( (string) $extra_index ); ?>" hidden>
										<label for="alpdt-<?php echo esc_attr( str_replace( '_', '-', $extra_name ) ); ?>"><?php echo esc_html( $extra_label ); ?></label>
										<div class="alpdt-color-controls"><input type="text" maxlength="9" spellcheck="false" autocomplete="off" id="alpdt-<?php echo esc_attr( str_replace( '_', '-', $extra_name ) ); ?>" data-alpdt-design="<?php echo esc_attr( $extra_name ); ?>" value="<?php echo esc_attr( $extra_default ); ?>" aria-label="<?php echo esc_attr( $extra_label . ' (HEX)' ); ?>"/><button type="button" class="alpdt-color-swatch" data-alpdt-color-picker="<?php echo esc_attr( $extra_name ); ?>" data-alpdt-color="<?php echo esc_attr( $extra_default ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: color name */ __( 'Choose %s', 'alp-dynamic-time-countdowns' ), $extra_label ) ); ?>" aria-expanded="false" style="--alpdt-swatch:<?php echo esc_attr( $extra_default ); ?>"><span aria-hidden="true"></span></button></div>
									</div>
								<?php endforeach; ?>
							</div>
							<button type="button" class="button alpdt-palette-add" data-alpdt-add-color="confetti" data-alpdt-max="10"><span aria-hidden="true">+</span> <?php esc_html_e( 'Add confetti color', 'alp-dynamic-time-countdowns' ); ?></button>
						</fieldset>
						<fieldset class="alpdt-field alpdt-celebration-palette" data-alpdt-confetti-palette>
							<legend><?php esc_html_e( 'Balloon colors', 'alp-dynamic-time-countdowns' ); ?></legend>
							<p class="alpdt-field-help"><?php esc_html_e( 'Customize balloon colors independently and add more as needed.', 'alp-dynamic-time-countdowns' ); ?></p>
							<div class="alpdt-design-grid">
								<?php foreach ( array( '#F78AB5', '#7B5DE8', '#07B57B', '#FFCC00' ) as $index => $color ) : ?>
									<?php
									$name           = 'balloon_' . ( $index + 1 ) . '_color';
									/* translators: %d: color position in the palette. */
									$caption        = sprintf( __( 'Balloon color %d', 'alp-dynamic-time-countdowns' ), $index + 1 );
									/* translators: %s: label of the color picker, e.g. Balloon color 1. */
									$choose_caption = sprintf( __( 'Choose %s', 'alp-dynamic-time-countdowns' ), $caption );
									?>
									<div class="alpdt-design-field alpdt-design-color">
										<label for="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>"><?php echo esc_html( $caption ); ?></label>
										<div class="alpdt-color-controls"><input type="text" maxlength="9" inputmode="text" spellcheck="false" autocomplete="off" pattern="#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?" id="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>" data-alpdt-design="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $color ); ?>" aria-label="<?php echo esc_attr( $caption . ' (HEX)' ); ?>"/><button type="button" class="alpdt-color-swatch" data-alpdt-color-picker="<?php echo esc_attr( $name ); ?>" data-alpdt-color="<?php echo esc_attr( $color ); ?>" aria-label="<?php echo esc_attr( $choose_caption ); ?>" aria-expanded="false" style="--alpdt-swatch:<?php echo esc_attr( $color ); ?>"><span aria-hidden="true"></span></button></div>
									</div>
								<?php endforeach; ?>
								<?php foreach ( range( 5, 10 ) as $extra_index ) : ?>
									<?php
									$extra_name    = 'balloon_' . $extra_index . '_color';
									$extra_default = array( '#FF8844', '#47C9F1', '#FA68BE', '#A4E45C', '#E8C3FF', '#FDDC71' )[ ( $extra_index - 5 ) % 6 ];
									/* translators: %d: palette color position */
									$extra_label   = sprintf( __( 'Balloon color %d', 'alp-dynamic-time-countdowns' ), $extra_index );
									?>
									<div class="alpdt-design-field alpdt-design-color" data-alpdt-extra-color-field="balloon:<?php echo esc_attr( (string) $extra_index ); ?>" hidden>
										<label for="alpdt-<?php echo esc_attr( str_replace( '_', '-', $extra_name ) ); ?>"><?php echo esc_html( $extra_label ); ?></label>
										<div class="alpdt-color-controls"><input type="text" maxlength="9" spellcheck="false" autocomplete="off" id="alpdt-<?php echo esc_attr( str_replace( '_', '-', $extra_name ) ); ?>" data-alpdt-design="<?php echo esc_attr( $extra_name ); ?>" value="<?php echo esc_attr( $extra_default ); ?>" aria-label="<?php echo esc_attr( $extra_label . ' (HEX)' ); ?>"/><button type="button" class="alpdt-color-swatch" data-alpdt-color-picker="<?php echo esc_attr( $extra_name ); ?>" data-alpdt-color="<?php echo esc_attr( $extra_default ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: color name */ __( 'Choose %s', 'alp-dynamic-time-countdowns' ), $extra_label ) ); ?>" aria-expanded="false" style="--alpdt-swatch:<?php echo esc_attr( $extra_default ); ?>"><span aria-hidden="true"></span></button></div>
									</div>
								<?php endforeach; ?>
							</div>
							<button type="button" class="button alpdt-palette-add" data-alpdt-add-color="balloon" data-alpdt-max="10"><span aria-hidden="true">+</span> <?php esc_html_e( 'Add balloon color', 'alp-dynamic-time-countdowns' ); ?></button>
						</fieldset>
						<button type="button" class="button alpdt-celebration-preview-button" id="alpdt-celebration-preview"><?php esc_html_e( 'Preview celebration', 'alp-dynamic-time-countdowns' ); ?></button>
						<p class="alpdt-field-help"><?php esc_html_e( 'Choose when the effect runs. The original completion-only behavior remains the default.', 'alp-dynamic-time-countdowns' ); ?></p>
					</div>
					<div class="alpdt-field alpdt-flip-panel-field" data-alpdt-field="flip_panel_background">
						<label for="alpdt-panel-color"><?php esc_html_e( 'Clock panel background', 'alp-dynamic-time-countdowns' ); ?></label>
						<p class="alpdt-field-help"><?php esc_html_e( 'Set the background behind Flip, Fireworks or Confetti. To remove it, choose Transparent below.', 'alp-dynamic-time-countdowns' ); ?></p>
						<div class="alpdt-color-controls">
							<input type="text" maxlength="9" pattern="#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?" id="alpdt-panel-color" value="#0F0F0F" data-alpdt-design="panel_color" spellcheck="false" autocomplete="off" aria-label="<?php echo esc_attr__( 'Clock panel background (HEX)', 'alp-dynamic-time-countdowns' ); ?>" />
							<button type="button" class="alpdt-color-swatch" data-alpdt-color-picker="panel_color" data-alpdt-color="#0f0f0f" data-alpdt-supports-gradient="1" data-alpdt-gradient-role="container" aria-label="<?php echo esc_attr__( 'Choose clock panel background', 'alp-dynamic-time-countdowns' ); ?>" aria-expanded="false" style="--alpdt-swatch:#0f0f0f"><span aria-hidden="true"></span></button>
						</div>
						<label class="alpdt-panel-clear"><input type="checkbox" id="alpdt-panel-transparent" /> <span><?php esc_html_e( 'Transparent / remove panel background', 'alp-dynamic-time-countdowns' ); ?></span></label>
					</div>
					<fieldset class="alpdt-field alpdt-appearance" data-alpdt-field="clock_design">
						<legend><?php esc_html_e( 'Clock colors, size and typography', 'alp-dynamic-time-countdowns' ); ?></legend>
						<p class="alpdt-field-help"><?php esc_html_e( 'Only controls relevant to the selected clock appearance are shown. Blank spacing retains its default.', 'alp-dynamic-time-countdowns' ); ?></p>
						<div class="alpdt-design-sections">
							<section class="alpdt-design-section" data-alpdt-design-section="colors" aria-labelledby="alpdt-design-title-colors">
								<div class="alpdt-design-section-head"><h4 id="alpdt-design-title-colors"><?php esc_html_e( 'Colors', 'alp-dynamic-time-countdowns' ); ?></h4><p><?php esc_html_e( 'Customize numbers, labels and surfaces.', 'alp-dynamic-time-countdowns' ); ?></p></div>
								<div class="alpdt-design-grid">
									<?php
									foreach ( array(
										'digit_color'  => array( __( 'Number color', 'alp-dynamic-time-countdowns' ), '#de4848' ),
										'label_color'  => array( __( 'Label color', 'alp-dynamic-time-countdowns' ), '#243665' ),
										'card_color'   => array( __( 'Card / circle background', 'alp-dynamic-time-countdowns' ), '#ffffff' ),
										'accent_color' => array( __( 'Progress / accent color', 'alp-dynamic-time-countdowns' ), '#2fc5b5' ),
										'track_color'  => array( __( 'Progress track color', 'alp-dynamic-time-countdowns' ), '#e1e8f0' ),
									) as $name => $config ) :
										/* translators: %s: clock color setting label, e.g. Number color. */
										$choose_color_label = sprintf( __( 'Choose %s', 'alp-dynamic-time-countdowns' ), $config[0] );
										$supports_gradient  = 'card_color' === $name;
										?>
										<div class="alpdt-design-field alpdt-design-color" data-alpdt-appearance-only="<?php echo esc_attr( $name ); ?>"><label for="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>"><?php echo esc_html( $config[0] ); ?></label><div class="alpdt-color-controls"><input type="text" inputmode="text" spellcheck="false" autocomplete="off" maxlength="9" pattern="#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?" id="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>" data-alpdt-design="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( strtoupper( $config[1] ) ); ?>" aria-label="<?php echo esc_attr( $config[0] . ' (HEX)' ); ?>"/><button type="button" class="alpdt-color-swatch" id="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>-picker" data-alpdt-color-picker="<?php echo esc_attr( $name ); ?>" data-alpdt-color="<?php echo esc_attr( $config[1] ); ?>"<?php echo $supports_gradient ? ' data-alpdt-supports-gradient="1" data-alpdt-gradient-role="dynamic"' : ''; ?> aria-label="<?php echo esc_attr( $choose_color_label ); ?>" aria-expanded="false" aria-haspopup="true" style="--alpdt-swatch:<?php echo esc_attr( $config[1] ); ?>"><span aria-hidden="true"></span></button></div></div>
									<?php endforeach; ?>
								</div>
								<div class="alpdt-design-field alpdt-design-color alpdt-design-full" data-alpdt-appearance-only="glow_background_color"><label for="alpdt-glow-background-color"><?php esc_html_e( 'Soft Glow background color', 'alp-dynamic-time-countdowns' ); ?></label><div class="alpdt-color-controls"><input type="text" id="alpdt-glow-background-color" data-alpdt-design="glow_background_color" maxlength="9" spellcheck="false" autocomplete="off" value="" placeholder="#EAF7F8" aria-label="<?php esc_attr_e( 'Soft Glow background color (HEX)', 'alp-dynamic-time-countdowns' ); ?>"/><button type="button" class="alpdt-color-swatch" data-alpdt-color-picker="glow_background_color" data-alpdt-color="#EAF7F8" data-alpdt-supports-gradient="1" data-alpdt-gradient-role="container" aria-label="<?php esc_attr_e( 'Choose Soft Glow background', 'alp-dynamic-time-countdowns' ); ?>" aria-expanded="false" style="--alpdt-swatch:#EAF7F8"><span aria-hidden="true"></span></button></div><p class="alpdt-field-help"><?php esc_html_e( 'Leave empty to keep the original pale green-blue glow. Use the color picker for a solid color or gradient.', 'alp-dynamic-time-countdowns' ); ?></p></div>
							</section>
							<section class="alpdt-design-section" data-alpdt-design-section="typography" aria-labelledby="alpdt-design-title-typography">
								<div class="alpdt-design-section-head"><h4 id="alpdt-design-title-typography"><?php esc_html_e( 'Typography', 'alp-dynamic-time-countdowns' ); ?></h4><p><?php esc_html_e( 'Choose a system font or a seasonal display face.', 'alp-dynamic-time-countdowns' ); ?></p></div>
								<div class="alpdt-design-grid">
									<?php foreach ( array( 'digit_size' => array( __( 'Number size', 'alp-dynamic-time-countdowns' ), '50', 'alpdt-digit-size' ), 'label_size' => array( __( 'Label size', 'alp-dynamic-time-countdowns' ), '14', 'alpdt-label-size' ) ) as $size_key => $size_config ) : ?>
									<div class="alpdt-design-field alpdt-font-size-field">
										<label for="<?php echo esc_attr( $size_config[2] ); ?>"><?php echo esc_html( $size_config[0] ); ?></label>
										<div class="alpdt-font-size-controls">
											<input type="number" inputmode="decimal" min="<?php echo esc_attr( 'digit_size' === $size_key ? '18' : '6' ); ?>" max="<?php echo esc_attr( 'digit_size' === $size_key ? '110' : '200' ); ?>" step="1" autocomplete="off" id="<?php echo esc_attr( $size_config[2] ); ?>" data-alpdt-design="<?php echo esc_attr( $size_key ); ?>" value="<?php echo esc_attr( $size_config[1] ); ?>" aria-describedby="<?php echo esc_attr( $size_config[2] ); ?>-help"/>
											<select id="<?php echo esc_attr( $size_config[2] ); ?>-unit" data-alpdt-font-unit="<?php echo esc_attr( $size_key ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: number size or label size. */ __( '%s unit', 'alp-dynamic-time-countdowns' ), $size_config[0] ) ); ?>">
												<?php foreach ( array( 'px', 'rem', 'em', '%', 'vw', 'vh', 'clamp' ) as $size_unit ) : ?>
													<option value="<?php echo esc_attr( $size_unit ); ?>"><?php echo esc_html( 'clamp' === $size_unit ? 'clamp()' : $size_unit ); ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<p class="alpdt-field-help" id="<?php echo esc_attr( $size_config[2] ); ?>-help"><?php esc_html_e( 'Choose a unit or clamp() for fluid sizing, e.g. clamp(20px, 4vw, 72px).', 'alp-dynamic-time-countdowns' ); ?></p>
									</div>
									<?php endforeach; ?>
									<div class="alpdt-design-field alpdt-font-style-field" data-alpdt-appearance-only="clock_weight"><label for="alpdt-clock-weight"><?php esc_html_e( 'Font weight', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-clock-weight" data-alpdt-design="clock_weight"><option value=""><?php esc_html_e( 'Style default', 'alp-dynamic-time-countdowns' ); ?></option><option value="300"><?php esc_html_e( 'Light · 300', 'alp-dynamic-time-countdowns' ); ?></option><option value="400">Regular · 400</option><option value="500">Medium · 500</option><option value="600">Semi-bold · 600</option><option value="700">Bold · 700</option><option value="800">Extra bold · 800</option><option value="900">Black · 900</option></select></div>
									<div class="alpdt-design-field alpdt-font-style-field" data-alpdt-appearance-only="clock_italic"><label for="alpdt-clock-italic"><?php esc_html_e( 'Font style', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-clock-italic" data-alpdt-design="clock_italic"><option value=""><?php esc_html_e( 'Style default', 'alp-dynamic-time-countdowns' ); ?></option><option value="normal"><?php esc_html_e( 'Normal', 'alp-dynamic-time-countdowns' ); ?></option><option value="italic"><?php esc_html_e( 'Italic', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Decorative fonts may use synthetic bold or italic if the selected face has only one style.', 'alp-dynamic-time-countdowns' ); ?></p></div>
									<div class="alpdt-design-field alpdt-design-full alpdt-blood-font-field" data-alpdt-blood-font hidden><label for="alpdt-blood-font"><?php esc_html_e( 'Blood Dripping font', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-blood-font" data-alpdt-design="blood_font"><option value="creepster">Creepster · Regular 400</option><option value="nosifer">Nosifer · Regular 400</option></select><p class="alpdt-field-help"><?php esc_html_e( 'These two fonts are exclusive to Blood Dripping.', 'alp-dynamic-time-countdowns' ); ?></p></div>
									<div class="alpdt-design-field alpdt-font-field"><label for="alpdt-clock-font"><?php esc_html_e( 'Font family', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-clock-font" data-alpdt-design="clock_font"><option value="auto"><?php esc_html_e( 'Style default (recommended)', 'alp-dynamic-time-countdowns' ); ?></option><option value="system"><?php esc_html_e( 'System · UI sans serif', 'alp-dynamic-time-countdowns' ); ?></option><option value="arial"><?php esc_html_e( 'System · Arial', 'alp-dynamic-time-countdowns' ); ?></option><option value="verdana"><?php esc_html_e( 'System · Verdana', 'alp-dynamic-time-countdowns' ); ?></option><option value="georgia"><?php esc_html_e( 'System · Georgia', 'alp-dynamic-time-countdowns' ); ?></option><option value="trebuchet"><?php esc_html_e( 'System · Trebuchet MS', 'alp-dynamic-time-countdowns' ); ?></option><option value="monospace"><?php esc_html_e( 'System · Monospace', 'alp-dynamic-time-countdowns' ); ?></option><option value="inter"><?php esc_html_e( 'Modern · Inter', 'alp-dynamic-time-countdowns' ); ?></option><option value="poppins"><?php esc_html_e( 'Modern · Poppins', 'alp-dynamic-time-countdowns' ); ?></option><option value="montserrat"><?php esc_html_e( 'Modern · Montserrat', 'alp-dynamic-time-countdowns' ); ?></option><option value="lato"><?php esc_html_e( 'Modern · Lato', 'alp-dynamic-time-countdowns' ); ?></option><option value="fredoka"><?php esc_html_e( 'Display · Fredoka', 'alp-dynamic-time-countdowns' ); ?></option><option value="orbitron"><?php esc_html_e( 'Display · Orbitron', 'alp-dynamic-time-countdowns' ); ?></option><option value="snowburst"><?php esc_html_e( 'Christmas · Snowburst One', 'alp-dynamic-time-countdowns' ); ?></option><option value="almonte_snow"><?php esc_html_e( 'Christmas · Almonte Snow', 'alp-dynamic-time-countdowns' ); ?></option><option value="mountains_christmas"><?php esc_html_e( 'Christmas · Mountains of Christmas', 'alp-dynamic-time-countdowns' ); ?></option><option value="creepster"><?php esc_html_e( 'Halloween · Creepster', 'alp-dynamic-time-countdowns' ); ?></option><option value="nosifer"><?php esc_html_e( 'Halloween · Nosifer', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Fonts load only when used by a countdown. Special drawing effects may retain their own typography.', 'alp-dynamic-time-countdowns' ); ?></p></div>
								</div>
							</section>
							<section class="alpdt-design-section" data-alpdt-design-section="size" aria-labelledby="alpdt-design-title-size">
								<div class="alpdt-design-section-head"><h4 id="alpdt-design-title-size"><?php esc_html_e( 'Size & shape', 'alp-dynamic-time-countdowns' ); ?></h4><p><?php esc_html_e( 'Circle dimensions for compatible styles.', 'alp-dynamic-time-countdowns' ); ?></p></div>
								<div class="alpdt-design-grid">
									<div class="alpdt-design-field" data-alpdt-appearance-only="clock_size"><label for="alpdt-clock-size"><?php esc_html_e( 'Circle diameter (px)', 'alp-dynamic-time-countdowns' ); ?></label><input type="number" min="90" max="300" step="2" id="alpdt-clock-size" data-alpdt-design="clock_size" value="170"/></div>
								</div>
							</section>
							<section class="alpdt-design-section" data-alpdt-design-section="spacing" aria-labelledby="alpdt-design-title-spacing">
								<div class="alpdt-design-section-head"><h4 id="alpdt-design-title-spacing"><?php esc_html_e( 'Spacing', 'alp-dynamic-time-countdowns' ); ?></h4><p><?php esc_html_e( 'Adjust number cards, corners and individual sides.', 'alp-dynamic-time-countdowns' ); ?></p></div>
								<div class="alpdt-design-grid">
									<div class="alpdt-design-field alpdt-design-full alpdt-clock-width-field">
										<label for="alpdt-clock-width"><?php esc_html_e( 'Countdown width', 'alp-dynamic-time-countdowns' ); ?></label>
										<select id="alpdt-clock-width" data-alpdt-design="clock_width"><option value="auto"><?php esc_html_e( 'Automatic (content width)', 'alp-dynamic-time-countdowns' ); ?></option><option value="full"><?php esc_html_e( 'Full width (100%)', 'alp-dynamic-time-countdowns' ); ?></option></select>
										<p class="alpdt-field-help" data-alpdt-style-help="width:default"><?php esc_html_e( 'Full width expands the countdown to its parent container; the design remains centered.', 'alp-dynamic-time-countdowns' ); ?></p>
                                        <p class="alpdt-field-help" data-alpdt-style-help="width:christmas_lights" hidden><?php esc_html_e( 'Christmas Lights stretch across the available width while numbers and labels remain centered.', 'alp-dynamic-time-countdowns' ); ?></p>
									</div>
									<?php
									foreach ( array(
										'countdown' => array( __( 'Countdown padding (px)', 'alp-dynamic-time-countdowns' ), __( 'Space around the entire countdown. The number card padding below is separate.', 'alp-dynamic-time-countdowns' ), 'countdown_padding_' ),
										'card'      => array( __( 'Number card padding (px)', 'alp-dynamic-time-countdowns' ), __( 'Space inside the number cards. The outside spacing between cards is separate.', 'alp-dynamic-time-countdowns' ), 'clock_padding_' ),
									) as $padding_scope => $padding_group ) :
										?>
									<div class="alpdt-design-field alpdt-design-full alpdt-padding-group" data-alpdt-padding-group="<?php echo esc_attr( $padding_scope ); ?>">
										<div class="alpdt-padding-heading">
                                            <span class="alpdt-padding-title"><?php echo esc_html( $padding_group[0] ); ?></span>
                                            <p class="alpdt-field-help" <?php if ( 'card' === $padding_scope ) : ?>data-alpdt-style-help="card:default"<?php endif; ?>><?php echo esc_html( $padding_group[1] ); ?></p>
                                            <?php if ( 'card' === $padding_scope ) : ?>
                                                <?php
                                                $card_padding_help = array(
                                                    'odometer'         => __( 'Increase the space inside each Odometer digit card, around its number. The gap between digit cards does not change.', 'alp-dynamic-time-countdowns' ),
                                                    'flip'             => __( 'Add space around the flip-number group. The moving digit halves keep their own proportions.', 'alp-dynamic-time-countdowns' ),
                                                    'liquid'           => __( 'Add clearance between the circular Liquid designs, not inside their circles. The circle diameter and number placement stay unchanged.', 'alp-dynamic-time-countdowns' ),
                                                    'ring'             => __( 'Add clearance around the circular progress dials without changing their diameter.', 'alp-dynamic-time-countdowns' ),
                                                    'orbit'            => __( 'Add clearance around the circular Orbit designs without altering their diameter.', 'alp-dynamic-time-countdowns' ),
                                                    'snowfall'         => __( 'Adjust the space inside each Snowfall number card. The background snowfall remains unchanged.', 'alp-dynamic-time-countdowns' ),
                                                    'christmas_lights' => __( 'Adjust the space inside each Christmas Lights number card. The bulbs and their distance to the cards have separate controls.', 'alp-dynamic-time-countdowns' ),
                                                );
                                                foreach ( $card_padding_help as $help_style => $help_text ) :
                                                    ?>
                                                    <p class="alpdt-field-help" data-alpdt-style-help="card:<?php echo esc_attr( $help_style ); ?>" hidden><?php echo esc_html( $help_text ); ?></p>
                                                    <?php
                                                endforeach;
                                                ?>
                                            <?php endif; ?>
                                        </div>
										<div class="alpdt-padding-group-grid">
											<?php
											foreach ( array(
												'vertical'   => array( 'top' => __( 'Top', 'alp-dynamic-time-countdowns' ), 'bottom' => __( 'Bottom', 'alp-dynamic-time-countdowns' ) ),
												'horizontal' => array( 'left' => __( 'Left', 'alp-dynamic-time-countdowns' ), 'right' => __( 'Right', 'alp-dynamic-time-countdowns' ) ),
											) as $axis => $sides ) :
												?>
											<div class="alpdt-padding-pair-controls">
												<?php $side_index = 0; foreach ( $sides as $side => $caption ) : ?>
													<?php if ( $side_index++ ) : ?>
														<button type="button" class="alpdt-padding-link" data-alpdt-padding-link="<?php echo esc_attr( $padding_scope . ':' . $axis ); ?>" aria-pressed="false" aria-label="<?php echo esc_attr( sprintf( /* translators: 1: countdown or number card; 2: vertical or horizontal */ __( 'Link %1$s %2$s padding values', 'alp-dynamic-time-countdowns' ), $padding_group[0], 'vertical' === $axis ? __( 'vertical', 'alp-dynamic-time-countdowns' ) : __( 'horizontal', 'alp-dynamic-time-countdowns' ) ) ); ?>" title="<?php echo esc_attr__( 'Link or unlink padding values', 'alp-dynamic-time-countdowns' ); ?>"><svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 8H8a4 4 0 0 0 0 8h2"/><path d="M14 8h2a4 4 0 0 1 0 8h-2"/><path d="M9 12h6"/></svg></button>
													<?php endif; ?>
													<div class="alpdt-padding-side"><label for="alpdt-<?php echo esc_attr( $padding_scope . '-padding-' . $side ); ?>"><?php echo esc_html( $caption ); ?></label><input type="number" min="0" max="80" step="1" id="alpdt-<?php echo esc_attr( $padding_scope . '-padding-' . $side ); ?>" data-alpdt-design="<?php echo esc_attr( $padding_group[2] . $side ); ?>" value="" placeholder="0"/></div>
												<?php endforeach; ?>
											</div>
											<?php endforeach; ?>
										</div>
									</div>
									<?php endforeach; ?>
									<div class="alpdt-design-field alpdt-design-full" data-alpdt-appearance-only="bulb_count"><label for="alpdt-bulb-count"><?php esc_html_e( 'Number of Christmas bulbs', 'alp-dynamic-time-countdowns' ); ?></label><input type="number" min="8" max="60" step="1" id="alpdt-bulb-count" data-alpdt-design="bulb_count" value="" placeholder="15"/><p class="alpdt-field-help"><?php esc_html_e( 'More bulbs reduce the horizontal space between them, including at 100% countdown width. Leave empty for 15 bulbs.', 'alp-dynamic-time-countdowns' ); ?></p></div>
									<div class="alpdt-design-field alpdt-design-full" data-alpdt-appearance-only="bulb_card_gap"><label for="alpdt-bulb-card-gap"><?php esc_html_e( 'Bulbs to number cards spacing (px)', 'alp-dynamic-time-countdowns' ); ?></label><input type="number" min="0" max="70" step="1" id="alpdt-bulb-card-gap" data-alpdt-design="bulb_card_gap" value="" placeholder="<?php echo esc_attr__( 'Style default', 'alp-dynamic-time-countdowns' ); ?>"/><p class="alpdt-field-help"><?php esc_html_e( 'Christmas Lights only. Set 0 for the tightest vertical gap beneath the bulbs. Leave empty for the original spacing.', 'alp-dynamic-time-countdowns' ); ?></p></div>
									<input type="hidden" id="alpdt-card-inner-spacing" data-alpdt-design="card_inner_spacing" value=""/>
									<div class="alpdt-design-field alpdt-design-full alpdt-padding-group alpdt-radius-group" data-alpdt-appearance-only="clock_radius">
										<div class="alpdt-padding-heading">
											<span class="alpdt-padding-title"><?php esc_html_e( 'Countdown border radius (px)', 'alp-dynamic-time-countdowns' ); ?></span>
											<p class="alpdt-field-help"><?php esc_html_e( 'Link corners in pairs or set them individually. Leave fields empty to retain the original design. Circular artwork stays round.', 'alp-dynamic-time-countdowns' ); ?></p>
										</div>
										<div class="alpdt-radius-default">
											<label for="alpdt-clock-radius"><?php esc_html_e( 'All corners', 'alp-dynamic-time-countdowns' ); ?></label>
											<input type="number" min="0" max="80" step="1" id="alpdt-clock-radius" data-alpdt-design="clock_radius" value="" placeholder="<?php echo esc_attr__( 'Default', 'alp-dynamic-time-countdowns' ); ?>"/>
										</div>
										<div class="alpdt-padding-group-grid alpdt-radius-corners">
											<?php
											foreach ( array(
												'top' => array( 'top_left' => __( 'Top left', 'alp-dynamic-time-countdowns' ), 'top_right' => __( 'Top right', 'alp-dynamic-time-countdowns' ) ),
												'bottom' => array( 'bottom_left' => __( 'Bottom left', 'alp-dynamic-time-countdowns' ), 'bottom_right' => __( 'Bottom right', 'alp-dynamic-time-countdowns' ) ),
											) as $edge => $corners ) :
												?>
												<div class="alpdt-padding-pair-controls">
													<?php $corner_index = 0; foreach ( $corners as $corner => $corner_caption ) : ?>
														<?php if ( $corner_index++ ) : ?>
															<button type="button" class="alpdt-padding-link alpdt-radius-link" data-alpdt-radius-link="<?php echo esc_attr( $edge ); ?>" aria-pressed="false" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: top or bottom edge */ __( 'Link %s corner radii', 'alp-dynamic-time-countdowns' ), $edge ) ); ?>" title="<?php echo esc_attr__( 'Link or unlink corner radius values', 'alp-dynamic-time-countdowns' ); ?>"><svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 8H8a4 4 0 0 0 0 8h2"/><path d="M14 8h2a4 4 0 0 1 0 8h-2"/><path d="M9 12h6"/></svg></button>
														<?php endif; ?>
														<div class="alpdt-padding-side"><label for="alpdt-radius-<?php echo esc_attr( str_replace( '_', '-', $corner ) ); ?>"><?php echo esc_html( $corner_caption ); ?></label><input type="number" min="0" max="80" step="1" id="alpdt-radius-<?php echo esc_attr( str_replace( '_', '-', $corner ) ); ?>" data-alpdt-design="clock_radius_<?php echo esc_attr( $corner ); ?>" value="" placeholder="<?php echo esc_attr__( 'Default', 'alp-dynamic-time-countdowns' ); ?>"/></div>
													<?php endforeach; ?>
												</div>
											<?php endforeach; ?>
									</div>
								</div>
								</div>
							</section>
							<section class="alpdt-design-section" data-alpdt-design-section="bulbs" aria-labelledby="alpdt-design-title-bulbs" hidden>
								<div class="alpdt-design-section-head"><h4 id="alpdt-design-title-bulbs"><?php esc_html_e( 'Christmas bulb colors', 'alp-dynamic-time-countdowns' ); ?></h4><p><?php esc_html_e( 'Five default colors repeat across the strand. Add more colors for extra variety.', 'alp-dynamic-time-countdowns' ); ?></p></div>
								<div class="alpdt-design-grid">
									<?php
									foreach ( array(
										'christmas_1_color' => array( __( 'Bulb color 1', 'alp-dynamic-time-countdowns' ), '#FF4958' ),
										'christmas_2_color' => array( __( 'Bulb color 2', 'alp-dynamic-time-countdowns' ), '#21ED8E' ),
										'christmas_3_color' => array( __( 'Bulb color 3', 'alp-dynamic-time-countdowns' ), '#FFDB52' ),
										'christmas_4_color' => array( __( 'Bulb color 4', 'alp-dynamic-time-countdowns' ), '#63B5FF' ),
										'christmas_5_color' => array( __( 'Bulb color 5', 'alp-dynamic-time-countdowns' ), '#ED71F9' ),
									) as $name => $config ) :
										/* translators: %s: Christmas bulb color setting label. */
										$choose_color_label = sprintf( __( 'Choose %s', 'alp-dynamic-time-countdowns' ), $config[0] );
										?>
										<div class="alpdt-design-field alpdt-design-color" data-alpdt-appearance-only="<?php echo esc_attr( $name ); ?>"><label for="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>"><?php echo esc_html( $config[0] ); ?></label><div class="alpdt-color-controls"><input type="text" inputmode="text" spellcheck="false" autocomplete="off" maxlength="9" pattern="#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?" id="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>" data-alpdt-design="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( strtoupper( $config[1] ) ); ?>" aria-label="<?php echo esc_attr( $config[0] . ' (HEX)' ); ?>"/><button type="button" class="alpdt-color-swatch" id="alpdt-<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>-picker" data-alpdt-color-picker="<?php echo esc_attr( $name ); ?>" data-alpdt-color="<?php echo esc_attr( $config[1] ); ?>" aria-label="<?php echo esc_attr( $choose_color_label ); ?>" aria-expanded="false" aria-haspopup="true" style="--alpdt-swatch:<?php echo esc_attr( $config[1] ); ?>"><span aria-hidden="true"></span></button></div></div>
									<?php endforeach; ?>
									<?php foreach ( range( 6, 12 ) as $extra_index ) : ?>
										<?php
										$extra_name    = 'christmas_' . $extra_index . '_color';
										$extra_default = array( '#FF8833', '#47DAF1', '#FC76CE', '#A2EC54', '#FFB1B1', '#D7ACFF', '#FFFC8E' )[ ( $extra_index - 6 ) % 7 ];
										/* translators: %d: palette color position */
										$extra_label   = sprintf( __( 'Bulb color %d', 'alp-dynamic-time-countdowns' ), $extra_index );
										?>
										<div class="alpdt-design-field alpdt-design-color" data-alpdt-extra-color-field="christmas:<?php echo esc_attr( (string) $extra_index ); ?>" hidden>
											<label for="alpdt-<?php echo esc_attr( str_replace( '_', '-', $extra_name ) ); ?>"><?php echo esc_html( $extra_label ); ?></label>
											<div class="alpdt-color-controls"><input type="text" maxlength="9" spellcheck="false" autocomplete="off" id="alpdt-<?php echo esc_attr( str_replace( '_', '-', $extra_name ) ); ?>" data-alpdt-design="<?php echo esc_attr( $extra_name ); ?>" value="<?php echo esc_attr( $extra_default ); ?>" aria-label="<?php echo esc_attr( $extra_label . ' (HEX)' ); ?>"/><button type="button" class="alpdt-color-swatch" data-alpdt-color-picker="<?php echo esc_attr( $extra_name ); ?>" data-alpdt-color="<?php echo esc_attr( $extra_default ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: color name */ __( 'Choose %s', 'alp-dynamic-time-countdowns' ), $extra_label ) ); ?>" aria-expanded="false" style="--alpdt-swatch:<?php echo esc_attr( $extra_default ); ?>"><span aria-hidden="true"></span></button></div>
										</div>
									<?php endforeach; ?>
								</div>
								<button type="button" class="button alpdt-palette-add" data-alpdt-add-color="christmas" data-alpdt-max="12"><span aria-hidden="true">+</span> <?php esc_html_e( 'Add bulb color', 'alp-dynamic-time-countdowns' ); ?></button>
							</section>
						</div>
						<input type="hidden" id="alpdt-bg-gradient-type" data-alpdt-design="bg_gradient_type" value="none"/>
						<input type="hidden" id="alpdt-bg-gradient-target" data-alpdt-design="bg_gradient_target" value="container"/>
						<input type="hidden" id="alpdt-bg-gradient-start" data-alpdt-design="bg_gradient_start" value="#053197"/>
						<input type="hidden" id="alpdt-bg-gradient-end" data-alpdt-design="bg_gradient_end" value="#2FC5B5"/>
						<input type="hidden" id="alpdt-bg-gradient-angle" data-alpdt-design="bg_gradient_angle" value="135"/>
						<div class="alpdt-reset-row"><button type="button" class="button alpdt-reset-appearance" id="alpdt-reset-appearance"><?php esc_html_e( 'Reset appearance to defaults', 'alp-dynamic-time-countdowns' ); ?></button><p class="alpdt-field-help"><?php esc_html_e( 'Restores the clock colors, sizes and font. Your dates and countdown settings stay unchanged.', 'alp-dynamic-time-countdowns' ); ?></p></div>
					</fieldset>
				</section>
				<section class="alpdt-clock-panel" role="tabpanel" tabindex="0" id="alpdt-clock-panel-labels" aria-labelledby="alpdt-clock-tab-labels" hidden>
					<fieldset class="alpdt-field alpdt-clock-labels" data-alpdt-field="clock_labels">
						<legend><?php esc_html_e( 'Countdown unit labels (optional)', 'alp-dynamic-time-countdowns' ); ?></legend>
						<p class="alpdt-field-help"><?php esc_html_e( 'Labels follow the WordPress site language automatically. You can replace any label with your own text, in any language. Leave a field empty for the automatic translation.', 'alp-dynamic-time-countdowns' ); ?></p>
						<div class="alpdt-labels-grid">
							<?php foreach ( array( 'years' => __( 'Years', 'alp-dynamic-time-countdowns' ), 'months' => __( 'Months', 'alp-dynamic-time-countdowns' ), 'days' => __( 'Days', 'alp-dynamic-time-countdowns' ), 'hours' => __( 'Hours', 'alp-dynamic-time-countdowns' ), 'minutes' => __( 'Minutes', 'alp-dynamic-time-countdowns' ), 'seconds' => __( 'Seconds', 'alp-dynamic-time-countdowns' ) ) as $unit => $unit_label ) : ?>
								<div class="alpdt-label-override"><label for="alpdt-label-<?php echo esc_attr( $unit ); ?>"><?php echo esc_html( $unit_label ); ?></label><input type="text" maxlength="32" id="alpdt-label-<?php echo esc_attr( $unit ); ?>" data-alpdt-unit-label="<?php echo esc_attr( $unit ); ?>" placeholder="<?php echo esc_attr( $unit_label ); ?>"/></div>
							<?php endforeach; ?>
						</div>
					</fieldset>
				</section>
			</div>
			<div class="alpdt-field" data-alpdt-field="copyright_style"><label for="alpdt-copyright-style"><?php esc_html_e( 'Copyright display', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-copyright-style"><option value="range"><?php esc_html_e( 'Starting year – current year (example: 1999–2026)', 'alp-dynamic-time-countdowns' ); ?></option><option value="current"><?php esc_html_e( 'Current year only (example: 2026)', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Choose the current year for © 2026 or a year range for © 1999–2026. Next year updates automatically.', 'alp-dynamic-time-countdowns' ); ?></p></div>
			<div class="alpdt-field" data-alpdt-field="mode"><label for="alpdt-mode"><?php esc_html_e( 'Year calculation', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-mode"><option value="calendar"><?php esc_html_e( 'Calendar-year difference', 'alp-dynamic-time-countdowns' ); ?></option><option value="anniversary"><?php esc_html_e( 'Completed anniversaries', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Calendar: 2026 minus 2000 = 26. Completed anniversaries: count only birthdays or anniversaries that have already happened.', 'alp-dynamic-time-countdowns' ); ?></p></div>
			<div class="alpdt-field" data-alpdt-field="unit"><label for="alpdt-unit"><?php esc_html_e( 'Unit', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-unit"><option value="years"><?php esc_html_e( 'Years', 'alp-dynamic-time-countdowns' ); ?></option><option value="months"><?php esc_html_e( 'Months', 'alp-dynamic-time-countdowns' ); ?></option><option value="weeks"><?php esc_html_e( 'Weeks', 'alp-dynamic-time-countdowns' ); ?></option><option value="days"><?php esc_html_e( 'Days', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Choose whether to show years of experience, months, weeks or days. Calendar-year mode applies only to years.', 'alp-dynamic-time-countdowns' ); ?></p></div>
			<div class="alpdt-field" data-alpdt-field="offset"><label for="alpdt-offset"><?php esc_html_e( 'Year offset (optional)', 'alp-dynamic-time-countdowns' ); ?></label><input id="alpdt-offset" type="number" value="0" min="-100" max="100" step="1"/><p class="alpdt-field-help"><?php esc_html_e( 'Use zero for the current year, or 1 for next year.', 'alp-dynamic-time-countdowns' ); ?></p></div>
			<div class="alpdt-field" data-alpdt-field="format"><label for="alpdt-format"><?php esc_html_e( 'Date format', 'alp-dynamic-time-countdowns' ); ?></label><select id="alpdt-format"><option value="j F Y"><?php esc_html_e( 'Day month year (25 October 2026)', 'alp-dynamic-time-countdowns' ); ?></option><option value="F j, Y"><?php esc_html_e( 'Month day, year (October 25, 2026)', 'alp-dynamic-time-countdowns' ); ?></option><option value="d/m/Y"><?php esc_html_e( 'Day/month/year (25/10/2026)', 'alp-dynamic-time-countdowns' ); ?></option><option value="m/d/Y"><?php esc_html_e( 'Month/day/year (10/25/2026)', 'alp-dynamic-time-countdowns' ); ?></option><option value="Y-m-d"><?php esc_html_e( 'ISO year-month-day (2026-10-25)', 'alp-dynamic-time-countdowns' ); ?></option><option value="l, j F Y"><?php esc_html_e( 'Weekday, day month year (Sunday, 25 October 2026)', 'alp-dynamic-time-countdowns' ); ?></option><option value="F Y"><?php esc_html_e( 'Month and year (October 2026)', 'alp-dynamic-time-countdowns' ); ?></option></select><p class="alpdt-field-help"><?php esc_html_e( 'Choose the way the date is written. The examples show the day, month and year so you can distinguish 15 December from 12 May. Month and weekday names use your website language.', 'alp-dynamic-time-countdowns' ); ?></p></div>
			<fieldset class="alpdt-field alpdt-anniversary-fields" data-alpdt-field="anniversary_messages">
				<legend><?php esc_html_e( 'Your anniversary messages', 'alp-dynamic-time-countdowns' ); ?></legend>
				<p class="alpdt-field-help"><?php esc_html_e( 'Enter the full start date (YYYY-MM-DD) to choose the actual anniversary day. Each message is selected automatically before, on or after that date.', 'alp-dynamic-time-countdowns' ); ?></p>
				<div class="alpdt-anniversary-placeholders" role="group" aria-label="<?php echo esc_attr__( 'Anniversary variables', 'alp-dynamic-time-countdowns' ); ?>">
					<strong><?php esc_html_e( 'Insert a dynamic variable in your message', 'alp-dynamic-time-countdowns' ); ?></strong>
					<p class="alpdt-field-help"><?php esc_html_e( 'Use {number} for an ordinal (30.º), {years} for a plain number (30), and {current_year} for the calendar year. Copy a marker into any message. Old {year} markers still work as {years}.', 'alp-dynamic-time-countdowns' ); ?></p>
					<?php
					foreach ( array(
						'{number}'       => __( 'Ordinal anniversary, like 30.º in Spanish or 30th in English (same as Anniversary number).', 'alp-dynamic-time-countdowns' ),
						'{years}'        => __( 'Completed anniversary years as a plain number, for example 30.', 'alp-dynamic-time-countdowns' ),
						'{current_year}' => __( 'Current calendar year, for example 2026.', 'alp-dynamic-time-countdowns' ),
					) as $token => $description ) :
						?>
					<div class="alpdt-anniversary-variable">
						<code><?php echo esc_html( $token ); ?></code><span><?php echo esc_html( $description ); ?></span>
						<span class="alpdt-copy-action"><button class="button alpdt-anniversary-copy" type="button" data-alpdt-copy-kind="variable" data-alpdt-copy-value="<?php echo esc_attr( $token ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: dynamic variable name. */ __( 'Copy variable %s', 'alp-dynamic-time-countdowns' ), $token ) ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg><?php esc_html_e( 'Copy', 'alp-dynamic-time-countdowns' ); ?></button></span>
					</div>
					<?php endforeach; ?>
				</div>
				<div class="alpdt-field"><label for="alpdt-message-before"><?php esc_html_e( 'Before the anniversary', 'alp-dynamic-time-countdowns' ); ?></label><input type="text" id="alpdt-message-before" maxlength="240" placeholder="Our {years}th anniversary is coming soon"/><p class="alpdt-field-help"><?php esc_html_e( 'Displayed until the day before the anniversary.', 'alp-dynamic-time-countdowns' ); ?></p></div>
				<div class="alpdt-field"><label for="alpdt-message-on"><?php esc_html_e( 'On the anniversary', 'alp-dynamic-time-countdowns' ); ?></label><input type="text" id="alpdt-message-on" maxlength="240" placeholder="Today we celebrate {years} years!"/><p class="alpdt-field-help"><?php esc_html_e( 'Displayed on the exact anniversary date.', 'alp-dynamic-time-countdowns' ); ?></p></div>
				<div class="alpdt-field"><label for="alpdt-message-after"><?php esc_html_e( 'After the anniversary', 'alp-dynamic-time-countdowns' ); ?></label><input type="text" id="alpdt-message-after" maxlength="240" placeholder="Thank you for {years} amazing years"/><p class="alpdt-field-help"><?php esc_html_e( 'Displayed after the anniversary until the next anniversary year.', 'alp-dynamic-time-countdowns' ); ?></p></div>
			</fieldset>
			<div class="alpdt-field" data-alpdt-field="text"><label class="alpdt-check"><input id="alpdt-text" type="checkbox" checked /> <span><?php esc_html_e( 'Include unit names', 'alp-dynamic-time-countdowns' ); ?></span></label><p class="alpdt-field-help"><?php esc_html_e( 'Turn on for “26 years” or off to show only “26”.', 'alp-dynamic-time-countdowns' ); ?></p></div>
		</div>
		<div class="alpdt-card alpdt-preview-card">
			<div class="alpdt-card-heading"><span class="alpdt-step">2</span><h3><?php esc_html_e( 'Live preview', 'alp-dynamic-time-countdowns' ); ?></h3></div>
			<div class="alpdt-preview-surface"><p id="alpdt-preview" class="alpdt-preview" aria-live="polite">…</p><p class="alpdt-field-help" id="alpdt-preview-caption" aria-live="polite"><?php esc_html_e( 'This is how the dynamic value will appear on your site.', 'alp-dynamic-time-countdowns' ); ?></p></div>
			<div class="alpdt-output">
				<label for="alpdt-shortcode"><?php esc_html_e( 'Shortcode', 'alp-dynamic-time-countdowns' ); ?></label>
				<textarea id="alpdt-shortcode" rows="3" readonly spellcheck="false"></textarea>
				<div class="alpdt-generator-actions"><button type="button" id="alpdt-copy" class="button button-primary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg><?php esc_html_e( 'Copy shortcode', 'alp-dynamic-time-countdowns' ); ?></button><button type="button" id="alpdt-save-shortcode" class="button alpdt-save-shortcode"><?php esc_html_e( 'Save shortcode', 'alp-dynamic-time-countdowns' ); ?></button><button type="button" id="alpdt-save-as-new" class="button" hidden><?php esc_html_e( 'Save as new', 'alp-dynamic-time-countdowns' ); ?></button></div>
				<p id="alpdt-status" class="alpdt-copy-status" role="status" aria-live="polite"></p>
			</div>
		</div>
	</div>
</section>
<?php
};
