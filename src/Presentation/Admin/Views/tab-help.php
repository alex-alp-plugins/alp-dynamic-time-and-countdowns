<?php
/**
 * About & Guide tab template.
 *
 * @package AlpDynamicTime\Presentation\Admin\Views
 */

defined( 'ABSPATH' ) || exit;

return static function ( array $context, \AlpDynamicTime\Presentation\Admin\AdminView $view ): void {
?>
<section role="tabpanel" tabindex="0" id="alpdt-panel-help" aria-labelledby="alpdt-tab-help" class="alpdt-panel" hidden>
	<div class="alpdt-section-intro"><h2><?php esc_html_e( 'About & guide', 'alp-dynamic-time-countdowns' ); ?></h2><p><?php esc_html_e( 'Choose a topic to learn how to use dynamic dates and countdowns.', 'alp-dynamic-time-countdowns' ); ?></p></div>
	<div class="alpdt-guide-tabs" role="tablist" aria-label="<?php echo esc_attr__( 'Guide topics', 'alp-dynamic-time-countdowns' ); ?>">
		<button type="button" role="tab" id="alpdt-guide-tab-how" aria-controls="alpdt-guide-panel-how" aria-selected="true" tabindex="0" data-alpdt-guide-tab="how"><?php esc_html_e( 'How it works', 'alp-dynamic-time-countdowns' ); ?></button>
		<button type="button" role="tab" id="alpdt-guide-tab-examples" aria-controls="alpdt-guide-panel-examples" aria-selected="false" tabindex="-1" data-alpdt-guide-tab="examples"><?php esc_html_e( 'Examples', 'alp-dynamic-time-countdowns' ); ?></button>
		<button type="button" role="tab" id="alpdt-guide-tab-seo" aria-controls="alpdt-guide-panel-seo" aria-selected="false" tabindex="-1" data-alpdt-guide-tab="seo"><?php esc_html_e( 'SEO plugins', 'alp-dynamic-time-countdowns' ); ?></button>
		<button type="button" role="tab" id="alpdt-guide-tab-divi" aria-controls="alpdt-guide-panel-divi" aria-selected="false" tabindex="-1" data-alpdt-guide-tab="divi"><?php esc_html_e( 'Divi', 'alp-dynamic-time-countdowns' ); ?></button>
	</div>
	<section role="tabpanel" tabindex="0" id="alpdt-guide-panel-how" aria-labelledby="alpdt-guide-tab-how" class="alpdt-guide-panel">
		<div class="alpdt-card">
			<h3><?php esc_html_e( 'How it works', 'alp-dynamic-time-countdowns' ); ?></h3>
			<ol>
				<li><?php esc_html_e( 'Choose what you want to show in Generator, such as the current year, years of experience, or a countdown.', 'alp-dynamic-time-countdowns' ); ?></li>
				<li><?php esc_html_e( 'Enter the date if needed, and choose a format. The example and shortcode update as you change the options.', 'alp-dynamic-time-countdowns' ); ?></li>
				<li><?php esc_html_e( 'Copy the shortcode into a WordPress or Divi field that supports shortcodes. The value is calculated when the page is displayed.', 'alp-dynamic-time-countdowns' ); ?></li>
			</ol>
			<p><?php esc_html_e( 'Need the same date in several places? Save it under Saved dates and use its identifier. Editing that date updates every reference.', 'alp-dynamic-time-countdowns' ); ?></p>
		</div>
	</section>
	<section role="tabpanel" tabindex="0" id="alpdt-guide-panel-examples" aria-labelledby="alpdt-guide-tab-examples" class="alpdt-guide-panel" hidden>
		<div class="alpdt-card">
			<h3><?php esc_html_e( 'Examples', 'alp-dynamic-time-countdowns' ); ?></h3>
			<?php
			foreach ( array(
				'[alpdt_time type="elapsed" from="2000" mode="calendar" text="true"]',
				'[alpdt_time from="2000-12-15" mode="anniversary" text="false"]',
				'[alpdt_time id="company-founded" mode="calendar"]',
				'[alpdt_time type="countdown" to="2027-01-01T18:30" countdown_display="full"]',
				'[alpdt_time type="copyright" copyright_style="current"]',
				'[alpdt_time type="copyright" from="1999" copyright_style="range"]',
				'[years-since y="2000" m="1" d="1" show="y"]',
			) as $example ) {
				$view->shortcode_example( $example );
			}
			?>
			<p><?php esc_html_e( 'Insert inline in content fields that process WordPress shortcodes; Divi and third-party builders must be tested in your installed versions.', 'alp-dynamic-time-countdowns' ); ?></p>
		</div>
	</section>
	<section role="tabpanel" tabindex="0" id="alpdt-guide-panel-seo" aria-labelledby="alpdt-guide-tab-seo" class="alpdt-guide-panel" hidden>
		<div class="alpdt-card">
			<h3><?php esc_html_e( 'Using it with SEO plugins', 'alp-dynamic-time-countdowns' ); ?></h3>
			<p><?php esc_html_e( 'In Rank Math, Yoast SEO or SEOPress, enter an ALP Dynamic Time & Countdowns shortcode inside an SEO title or meta description. The plugin replaces supported shortcodes in the published metadata.', 'alp-dynamic-time-countdowns' ); ?></p>
			<?php $view->shortcode_example( 'More than [alpdt_time from="2000" mode="calendar" text="false"] years of experience' ); ?>
			<p><?php esc_html_e( 'For Rank Math and Yoast SEO, copy a registered dynamic variable below. The year and month follow your WordPress site timezone; the month name uses your site language. Each saved date also has its own variable.', 'alp-dynamic-time-countdowns' ); ?></p>
			<div class="alpdt-seo-quick-variables">
				<div class="alpdt-seo-quick-heading"><strong><?php esc_html_e( 'Dynamic value', 'alp-dynamic-time-countdowns' ); ?></strong><strong>Rank Math</strong><strong>Yoast SEO</strong></div>
				<div class="alpdt-seo-quick-row"><strong><?php esc_html_e( 'Current year', 'alp-dynamic-time-countdowns' ); ?></strong><div><code>%alpdt_year%</code><?php $view->copy_button( '%alpdt_year%', true ); ?></div><div><code>%%alpdt_year%%</code><?php $view->copy_button( '%%alpdt_year%%', true ); ?></div></div>
				<div class="alpdt-seo-quick-row"><strong><?php esc_html_e( 'Current month', 'alp-dynamic-time-countdowns' ); ?></strong><div><code>%alpdt_month%</code><?php $view->copy_button( '%alpdt_month%', true ); ?></div><div><code>%%alpdt_month%%</code><?php $view->copy_button( '%%alpdt_month%%', true ); ?></div></div>
			</div>
			<p><?php esc_html_e( 'SEOPress: you can use the corresponding text shortcodes if its configured metadata field supports them.', 'alp-dynamic-time-countdowns' ); ?></p>
			<?php $view->shortcode_example( '[alpdt_time type="month"]' ); ?>
			<p><?php esc_html_e( 'For a saved date with identifier company-founded, use %alpdt_since_company_founded% in Rank Math or %%alpdt_since_company_founded%% in Yoast. Create the saved date first. SEO editor character counters and previews may not reflect the resolved value; inspect the rendered metadata.', 'alp-dynamic-time-countdowns' ); ?></p>
			<p><?php esc_html_e( 'Saved Shortcodes now also provide native SEO variables for non-countdown text. Save your configuration, then open its SEO variables section in Saved Shortcodes to view and copy the marker for Rank Math or Yoast. Unsaved custom text has no registered SEO variable.', 'alp-dynamic-time-countdowns' ); ?></p>
			<p><?php esc_html_e( 'The SEO editor preview may still display the shortcode. Verify the published page source, including the title and meta description, because editor previews and caches vary. This does not automatically update an article publishing date or guarantee immediate reindexing by search engines.', 'alp-dynamic-time-countdowns' ); ?></p>
		</div>
	</section>
	<section role="tabpanel" tabindex="0" id="alpdt-guide-panel-divi" aria-labelledby="alpdt-guide-tab-divi" class="alpdt-guide-panel" hidden>
		<div class="alpdt-card">
			<h3><?php esc_html_e( 'Dates, countdowns and Divi', 'alp-dynamic-time-countdowns' ); ?></h3>
			<p><?php esc_html_e( 'Dates use YYYY-MM-DD: the first four digits are the year, the middle two are the month, and the final two are the day. For example, 2000-12-15 is 15 December 2000. Countdown targets can additionally include hours and minutes, using your WordPress site timezone.', 'alp-dynamic-time-countdowns' ); ?></p>
			<p><?php esc_html_e( 'Create and save your shortcode in the ALP Dynamic Time & Countdowns dashboard. In Divi 5, select that Saved Shortcode in the simplified ALP Dynamic Time & Countdowns module. Optional Design controls change only that module instance. You can also paste the shortcode into a Divi Code module. Test the Visual Builder and frontend with your installed Divi release.', 'alp-dynamic-time-countdowns' ); ?></p>
		</div>
	</section>
</section>
<?php
};
