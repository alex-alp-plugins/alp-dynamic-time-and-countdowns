<?php
/** Clear only personal Quick Colors upon uninstall; preserve saved public dates. */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}
// User preferences belong to the departing plugin; deleting all user-meta
// rows for this exact key does not delete users or unrelated preferences.
delete_metadata( 'user', 0, 'alpdt_quick_colors', '', true );
// Site-wide alpdt_presets and saved shortcodes intentionally remain intact.
