<?php
/**
 * Centralized WordPress hook registrar.
 *
 * @package AlpDynamicTime\Infrastructure
 */

declare(strict_types=1);

namespace AlpDynamicTime\Infrastructure;

defined( 'ABSPATH' ) || exit;

/**
 * Collects actions and filters and registers them uniformly during bootstrap.
 */
final class HookManager {

	/** @var list<array{hook:string,callback:callable,priority:int,accepted_args:int}> */
	private array $actions = array();

	/** @var list<array{hook:string,callback:callable,priority:int,accepted_args:int}> */
	private array $filters = array();

	/**
	 * Queue an action hook.
	 */
	public function add_action( string $hook, callable $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->actions[] = array(
			'hook'          => $hook,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
	}

	/**
	 * Queue a filter hook.
	 */
	public function add_filter( string $hook, callable $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->filters[] = array(
			'hook'          => $hook,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
	}

	/**
	 * Bind all queued hooks to WordPress and flush queues.
	 */
	public function register(): void {
		foreach ( $this->actions as $item ) {
			add_action( $item['hook'], $item['callback'], $item['priority'], $item['accepted_args'] );
		}
		foreach ( $this->filters as $item ) {
			add_filter( $item['hook'], $item['callback'], $item['priority'], $item['accepted_args'] );
		}
		$this->actions = array();
		$this->filters = array();
	}
}
