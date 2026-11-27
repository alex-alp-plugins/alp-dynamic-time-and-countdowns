<?php
/**
 * Backward-compatibility facades for legacy ALPDT_* global class names.
 *
 * Preserves public contracts if third-party code calls static methods such as
 * ALPDT_Engine::render() or ALPDT_Shortcodes::main().
 *
 * @package AlpDynamicTime
 */

defined( 'ABSPATH' ) || exit;

use AlpDynamicTime\Domain\ClockRenderer;
use AlpDynamicTime\Domain\DateEngine;
use AlpDynamicTime\Domain\QuickColorsValidator;
use AlpDynamicTime\Domain\ScheduleCalculator;
use AlpDynamicTime\Domain\ShortcodeValidator;
use AlpDynamicTime\Infrastructure\Integrations\Divi5\Divi5DataAdapter;
use AlpDynamicTime\Infrastructure\Integrations\GutenbergBlock;
use AlpDynamicTime\Infrastructure\Integrations\SeoIntegration;
use AlpDynamicTime\Infrastructure\Persistence\PresetRepository;
use AlpDynamicTime\Infrastructure\Persistence\QuickColorsRepository;
use AlpDynamicTime\Infrastructure\Persistence\SavedShortcodeRepository;
use AlpDynamicTime\Infrastructure\Privacy\PrivacyService;
use AlpDynamicTime\Infrastructure\Rest\RestController;
use AlpDynamicTime\Plugin;
use AlpDynamicTime\Presentation\Admin\AdminController;
use AlpDynamicTime\Presentation\Frontend\ShortcodeController;

final class ALPDT_Engine {
	public static function today( ?DateTimeImmutable $override = null ): DateTimeImmutable {
		return DateEngine::today( $override );
	}
	public static function parse( string $input, ?DateTimeZone $zone = null ): ?DateTimeImmutable {
		return DateEngine::parse( $input, $zone );
	}
	public static function target( string $input, ?DateTimeZone $zone = null ): ?DateTimeImmutable {
		return DateEngine::target( $input, $zone );
	}
	public static function countdown_clock( int $seconds ): string {
		return DateEngine::countdown_clock( $seconds );
	}
	public static function anniversary_number( int $years ): string {
		return DateEngine::anniversary_number( $years );
	}
	public static function render( array $raw, ?DateTimeImmutable $today = null ): string {
		return Plugin::instance()->container()->get( DateEngine::class )->render( $raw, $today );
	}
}

final class ALPDT_Clock {
	public static function supports_horizontal( string $style ): bool {
		return Plugin::instance()->container()->get( ClockRenderer::class )->supports_horizontal( $style );
	}
	public static function units( string $raw ): array {
		return Plugin::instance()->container()->get( ClockRenderer::class )->units( $raw );
	}
	public static function style( string $style ): string {
		return Plugin::instance()->container()->get( ClockRenderer::class )->style( $style );
	}
	public static function font_size( mixed $raw, string $key ): ?string {
		return Plugin::instance()->container()->get( ClockRenderer::class )->font_size( $raw, $key );
	}
	public static function appearance( array $input ): array {
		return Plugin::instance()->container()->get( ClockRenderer::class )->appearance( $input );
	}
	public static function inline_css( array $design ): string {
		return Plugin::instance()->container()->get( ClockRenderer::class )->inline_css( $design );
	}
	public static function parts( DateTimeImmutable $now, DateTimeImmutable $target, array $requested ): array {
		return Plugin::instance()->container()->get( ClockRenderer::class )->parts( $now, $target, $requested );
	}
	public static function markup( DateTimeImmutable $target, string $raw_units, string $raw_style, ?DateTimeImmutable $now = null, array $design = array() ): string {
		return Plugin::instance()->container()->get( ClockRenderer::class )->markup( $target, $raw_units, $raw_style, $now, $design );
	}
}

final class ALPDT_Schedule {
	public static function expressions(): array {
		return Plugin::instance()->container()->get( ScheduleCalculator::class )->expressions();
	}
	public static function dynamic( array $raw, ?DateTimeImmutable $reference = null ): string {
		return Plugin::instance()->container()->get( ScheduleCalculator::class )->dynamic( $raw, $reference );
	}
	public static function next( array $raw, ?DateTimeImmutable $reference = null ): ?DateTimeImmutable {
		return Plugin::instance()->container()->get( ScheduleCalculator::class )->next( $raw, $reference );
	}
	public static function redirect_path( mixed $path ): string {
		return Plugin::instance()->container()->get( ScheduleCalculator::class )->redirect_path( $path );
	}
}

final class ALPDT_Saved_Shortcodes {
	public const MAX = SavedShortcodeRepository::MAX;
	public static function all(): array {
		return Plugin::instance()->container()->get( SavedShortcodeRepository::class )->all();
	}
	public static function parse( mixed $code ): ?array {
		return Plugin::instance()->container()->get( ShortcodeValidator::class )->parse( $code );
	}
	public static function save( string $slug, string $name, string $code, string $original = '' ): bool {
		return Plugin::instance()->container()->get( SavedShortcodeRepository::class )->save( $slug, $name, $code, $original );
	}
	public static function delete( string $slug ): bool {
		return Plugin::instance()->container()->get( SavedShortcodeRepository::class )->delete( $slug );
	}
	public static function resolve( mixed $slug ): ?array {
		return Plugin::instance()->container()->get( SavedShortcodeRepository::class )->resolve( $slug );
	}
}

final class ALPDT_Shortcodes {
	public static function main( mixed $atts = array() ): string {
		return Plugin::instance()->container()->get( ShortcodeController::class )->main( $atts );
	}
	public static function legacy( mixed $atts = array() ): string {
		return Plugin::instance()->container()->get( ShortcodeController::class )->legacy( $atts );
	}
	public static function legacy_gb( mixed $atts = array(), mixed $content = null ): string {
		return Plugin::instance()->container()->get( ShortcodeController::class )->legacy_gb( $atts, $content );
	}
}

final class ALPDT_Integrations {
	public static function saved_variable_name( string $slug ): string {
		return SeoIntegration::saved_variable_name( $slug );
	}
	public static function replace( mixed $value ): mixed {
		return Plugin::instance()->container()->get( SeoIntegration::class )->replace( $value );
	}
}

final class ALPDT_Block {
	public static function render( array $attrs ): string {
		return Plugin::instance()->container()->get( GutenbergBlock::class )->render( $attrs );
	}
}

final class ALPDT_API {
	public static function divi_preview( WP_REST_Request $request ): WP_REST_Response {
		return Plugin::instance()->container()->get( RestController::class )->divi_preview( $request );
	}
	public static function next_occurrence( WP_REST_Request $request ): WP_REST_Response {
		return Plugin::instance()->container()->get( RestController::class )->next_occurrence( $request );
	}
	public static function render( WP_REST_Request $request ): WP_REST_Response {
		return Plugin::instance()->container()->get( RestController::class )->render( $request );
	}
}

final class ALPDT_Privacy {
	public static function export_quick_colors( mixed $email_address, mixed $page = 1 ): array {
		return Plugin::instance()->container()->get( PrivacyService::class )->export_quick_colors( $email_address, $page );
	}
	public static function erase_quick_colors( mixed $email_address, mixed $page = 1 ): array {
		return Plugin::instance()->container()->get( PrivacyService::class )->erase_quick_colors( $email_address, $page );
	}
}

final class ALPDT_Divi5_Data {
	public static function plain( array $attrs, string $field, string $default = '' ): string {
		return Plugin::instance()->container()->get( Divi5DataAdapter::class )->plain( $attrs, $field, $default );
	}
	public static function slug( array $attrs ): string {
		return Plugin::instance()->container()->get( Divi5DataAdapter::class )->slug( $attrs );
	}
	public static function safe_color( string $input ): string {
		return Plugin::instance()->container()->get( Divi5DataAdapter::class )->safe_color( $input );
	}
	public static function local_style( array $attrs ): array {
		return Plugin::instance()->container()->get( Divi5DataAdapter::class )->local_style( $attrs );
	}
	public static function markup( array $attrs ): string {
		return Plugin::instance()->container()->get( Divi5DataAdapter::class )->markup( $attrs );
	}
	public static function wrapper_classes( array $attrs ): string {
		return Plugin::instance()->container()->get( Divi5DataAdapter::class )->wrapper_classes( $attrs );
	}
	public static function inline_style( array $attrs ): string {
		return Plugin::instance()->container()->get( Divi5DataAdapter::class )->inline_style( $attrs );
	}
	public static function choices(): array {
		return Plugin::instance()->container()->get( Divi5DataAdapter::class )->choices();
	}
}

final class ALPDT_Admin {
	public static function default_quick_colors(): array {
		return Plugin::instance()->container()->get( QuickColorsValidator::class )->defaults();
	}
	public static function valid_quick_colors( mixed $colors ): ?array {
		return Plugin::instance()->container()->get( QuickColorsValidator::class )->validate( $colors );
	}
	public static function quick_colors(): array {
		return Plugin::instance()->container()->get( QuickColorsRepository::class )->for_user( get_current_user_id() );
	}
}
