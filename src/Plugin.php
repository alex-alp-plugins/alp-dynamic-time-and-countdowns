<?php
/**
 * Main plugin composition root and Clean Architecture orchestrator.
 *
 * @package AlpDynamicTime
 */

declare(strict_types=1);

namespace AlpDynamicTime;

use AlpDynamicTime\Domain\ClockRenderer;
use AlpDynamicTime\Domain\DateEngine;
use AlpDynamicTime\Domain\QuickColorsValidator;
use AlpDynamicTime\Domain\ScheduleCalculator;
use AlpDynamicTime\Domain\ShortcodeValidator;
use AlpDynamicTime\Infrastructure\AssetManager;
use AlpDynamicTime\Infrastructure\Cli\CliCommand;
use AlpDynamicTime\Infrastructure\HookManager;
use AlpDynamicTime\Infrastructure\Integrations\Divi5\Divi5Bootstrap;
use AlpDynamicTime\Infrastructure\Integrations\Divi5\Divi5DataAdapter;
use AlpDynamicTime\Infrastructure\Integrations\GutenbergBlock;
use AlpDynamicTime\Infrastructure\Integrations\SeoIntegration;
use AlpDynamicTime\Infrastructure\Persistence\LegacyScanRepository;
use AlpDynamicTime\Infrastructure\Persistence\PresetRepository;
use AlpDynamicTime\Infrastructure\Persistence\QuickColorsRepository;
use AlpDynamicTime\Infrastructure\Persistence\SavedShortcodeRepository;
use AlpDynamicTime\Infrastructure\Privacy\PrivacyService;
use AlpDynamicTime\Infrastructure\Rest\RestController;
use AlpDynamicTime\Presentation\Admin\AdminController;
use AlpDynamicTime\Presentation\Admin\AdminView;
use AlpDynamicTime\Presentation\Frontend\ShortcodeController;

defined( 'ABSPATH' ) || exit;

/**
 * Wires Domain, Infrastructure, and Presentation layers through the Container.
 */
final class Plugin {

	private static ?self $instance = null;

	private Container $container;

	private bool $booted = false;

	private function __construct() {
		$this->container = new Container();
		$this->register_services();
	}

	/**
	 * Retrieve singleton plugin instance.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Expose service container.
	 */
	public function container(): Container {
		return $this->container;
	}

	/**
	 * Boot hooks based on execution context (frontend, admin, WP-CLI).
	 */
	public function boot(): void {
		if ( $this->booted ) {
			return;
		}
		$this->booted = true;

		/** @var HookManager $hooks */
		$hooks = $this->container->get( HookManager::class );

		/** @var AssetManager $assets */
		$assets = $this->container->get( AssetManager::class );
		$hooks->add_action( 'wp_enqueue_scripts', array( $assets, 'register_frontend_assets' ), 5 );

		/** @var ShortcodeController $shortcodes */
		$shortcodes = $this->container->get( ShortcodeController::class );
		$hooks->add_action( 'init', array( $shortcodes, 'register' ), 20 );

		/** @var GutenbergBlock $block */
		$block = $this->container->get( GutenbergBlock::class );
		$hooks->add_action( 'init', array( $block, 'register' ) );
		$hooks->add_action( 'enqueue_block_editor_assets', array( $block, 'enqueue_editor_data' ) );

		/** @var SeoIntegration $seo */
		$seo = $this->container->get( SeoIntegration::class );
		$seo->register_hooks( $hooks );

		/** @var RestController $rest */
		$rest = $this->container->get( RestController::class );
		$hooks->add_action( 'rest_api_init', array( $rest, 'register' ) );

		/** @var PrivacyService $privacy */
		$privacy = $this->container->get( PrivacyService::class );
		$hooks->add_filter( 'wp_privacy_personal_data_exporters', array( $privacy, 'register_exporters' ) );
		$hooks->add_filter( 'wp_privacy_personal_data_erasers', array( $privacy, 'register_erasers' ) );

		/** @var Divi5Bootstrap $divi5 */
		$divi5 = $this->container->get( Divi5Bootstrap::class );
		$divi5->register_hooks( $hooks );

		/** @var LegacyScanRepository $legacy_scan */
		$legacy_scan = $this->container->get( LegacyScanRepository::class );
		$hooks->add_action( 'save_post', array( $legacy_scan, 'invalidate' ) );
		$hooks->add_action( 'deleted_post', array( $legacy_scan, 'invalidate' ) );

		if ( is_admin() ) {
			/** @var AdminController $admin */
			$admin = $this->container->get( AdminController::class );
			$admin->register_hooks( $hooks );
			$hooks->add_action( 'admin_init', array( $privacy, 'suggest_policy_content' ) );
		}

		if ( defined( 'WP_CLI' ) && WP_CLI && class_exists( 'WP_CLI' ) ) {
			/** @var CliCommand $cli */
			$cli = $this->container->get( CliCommand::class );
			\WP_CLI::add_command( 'alpdt', $cli );
		}

		$hooks->register();
	}

	private function register_services(): void {
		$c = $this->container;

		// Core infrastructure.
		$c->set( HookManager::class, static fn(): HookManager => new HookManager() );
		$c->set( AssetManager::class, static fn(): AssetManager => new AssetManager() );

		// Pure domain services.
		$c->set( ScheduleCalculator::class, static fn(): ScheduleCalculator => new ScheduleCalculator() );
		$c->set( ClockRenderer::class, static fn(): ClockRenderer => new ClockRenderer() );
		$c->set( QuickColorsValidator::class, static fn(): QuickColorsValidator => new QuickColorsValidator() );
		$c->set(
			ShortcodeValidator::class,
			static fn( Container $di ): ShortcodeValidator => new ShortcodeValidator(
				$di->get( ScheduleCalculator::class ),
				$di->get( ClockRenderer::class )
			)
		);

		// Persistence repositories.
		$c->set( PresetRepository::class, static fn(): PresetRepository => new PresetRepository() );
		$c->set(
			SavedShortcodeRepository::class,
			static fn( Container $di ): SavedShortcodeRepository => new SavedShortcodeRepository(
				$di->get( ShortcodeValidator::class )
			)
		);
		$c->set(
			QuickColorsRepository::class,
			static fn( Container $di ): QuickColorsRepository => new QuickColorsRepository(
				$di->get( QuickColorsValidator::class )
			)
		);
		$c->set( LegacyScanRepository::class, static fn(): LegacyScanRepository => new LegacyScanRepository() );

		// Domain engine.
		$c->set(
			DateEngine::class,
			static fn( Container $di ): DateEngine => new DateEngine(
				$di->get( PresetRepository::class ),
				$di->get( ScheduleCalculator::class ),
				$di->get( ClockRenderer::class )
			)
		);

		// Presentation & integrations.
		$c->set(
			ShortcodeController::class,
			static fn( Container $di ): ShortcodeController => new ShortcodeController(
				$di->get( DateEngine::class ),
				$di->get( ClockRenderer::class ),
				$di->get( ScheduleCalculator::class ),
				$di->get( SavedShortcodeRepository::class ),
				$di->get( AssetManager::class )
			)
		);
		$c->set(
			GutenbergBlock::class,
			static fn( Container $di ): GutenbergBlock => new GutenbergBlock(
				$di->get( AssetManager::class ),
				$di->get( SavedShortcodeRepository::class ),
				$di->get( ShortcodeController::class ),
				$di->get( DateEngine::class )
			)
		);
		$c->set(
			SeoIntegration::class,
			static fn( Container $di ): SeoIntegration => new SeoIntegration(
				$di->get( DateEngine::class ),
				$di->get( PresetRepository::class ),
				$di->get( SavedShortcodeRepository::class )
			)
		);
		$c->set(
			PrivacyService::class,
			static fn( Container $di ): PrivacyService => new PrivacyService(
				$di->get( QuickColorsValidator::class )
			)
		);
		$c->set(
			Divi5DataAdapter::class,
			static fn( Container $di ): Divi5DataAdapter => new Divi5DataAdapter(
				$di->get( SavedShortcodeRepository::class ),
				$di->get( ShortcodeController::class )
			)
		);
		$c->set(
			Divi5Bootstrap::class,
			static fn( Container $di ): Divi5Bootstrap => new Divi5Bootstrap(
				$di->get( AssetManager::class ),
				$di->get( Divi5DataAdapter::class )
			)
		);
		$c->set(
			RestController::class,
			static fn( Container $di ): RestController => new RestController(
				$di->get( DateEngine::class ),
				$di->get( ScheduleCalculator::class ),
				$di->get( Divi5DataAdapter::class )
			)
		);
		$c->set(
			CliCommand::class,
			static fn( Container $di ): CliCommand => new CliCommand(
				$di->get( DateEngine::class ),
				$di->get( PresetRepository::class )
			)
		);
		$c->set( AdminView::class, static fn(): AdminView => new AdminView() );
		$c->set(
			AdminController::class,
			static fn( Container $di ): AdminController => new AdminController(
				$di->get( AssetManager::class ),
				$di->get( PresetRepository::class ),
				$di->get( SavedShortcodeRepository::class ),
				$di->get( QuickColorsRepository::class ),
				$di->get( LegacyScanRepository::class ),
				$di->get( AdminView::class )
			)
		);
	}
}
