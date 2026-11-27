<?php
/**
 * WP-CLI command interface for ALP Dynamic Time & Countdowns.
 *
 * @package AlpDynamicTime\Infrastructure\Cli
 */

declare(strict_types=1);

namespace AlpDynamicTime\Infrastructure\Cli;

use AlpDynamicTime\Domain\DateEngine;
use AlpDynamicTime\Infrastructure\Persistence\PresetRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Command: wp alpdt render --type=elapsed --from=2000 --mode=calendar
 */
final class CliCommand {

	public function __construct(
		private readonly DateEngine $engine,
		private readonly PresetRepository $presets,
	) {}

	/**
	 * Render a dynamic time expression using WordPress site timezone.
	 *
	 * ## OPTIONS
	 *
	 * [--type=<type>]
	 * : elapsed, year, month, date, countdown, copyright, ordinal.
	 *
	 * [--from=<date>]
	 * : Start year or full date.
	 *
	 * [--to=<date>]
	 * : Countdown target.
	 *
	 * [--id=<slug>]
	 * : Saved date.
	 *
	 * [--mode=<mode>]
	 * : calendar or anniversary.
	 *
	 * [--text=<text>]
	 * : true or false.
	 *
	 * ## EXAMPLES
	 *
	 *     wp alpdt render --from=2000 --mode=calendar --text=false
	 *
	 * @param array<int,string>    $args       Positional args.
	 * @param array<string,string> $assoc_args Associative flags.
	 */
	public function render( array $args, array $assoc_args ): void {
		$allowed = array_flip( array( 'type', 'from', 'to', 'id', 'mode', 'unit', 'text', 'format', 'offset' ) );
		$values  = array_intersect_key( $assoc_args, $allowed );
		\WP_CLI::line( $this->engine->render( $values ) );
	}

	/**
	 * Show how many saved milestones exist; no private data is printed.
	 *
	 * @param array<int,string>    $args       Positional args.
	 * @param array<string,string> $assoc_args Associative flags.
	 */
	public function count( array $args, array $assoc_args ): void {
		\WP_CLI::line( (string) count( $this->presets->all() ) );
	}
}
