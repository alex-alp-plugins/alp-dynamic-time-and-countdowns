<?php
defined( 'ABSPATH' ) || exit;

/** Command: wp alpdt render --type=elapsed --from=2000 --mode=calendar */
final class ALPDT_CLI {
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
     */
    public function render( array $args, array $assoc_args ): void {
        $allowed = array_flip( array( 'type', 'from', 'to', 'id', 'mode', 'unit', 'text', 'format', 'offset' ) );
        $values = array_intersect_key( $assoc_args, $allowed );
        \WP_CLI::line( ALPDT_Engine::render( $values ) );
    }

    /** Show how many saved milestones exist; no private data is printed. */
    public function count( array $args, array $assoc_args ): void {
        $presets = get_option( 'alpdt_presets', array() );
        \WP_CLI::line( (string) count( is_array( $presets ) ? $presets : array() ) );
    }
}
