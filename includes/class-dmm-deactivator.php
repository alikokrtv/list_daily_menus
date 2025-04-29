<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://alikokdeneysel.online
 * @since      1.0.0
 */

class DMM_Deactivator {

    /**
     * Plugin deactivation functionality
     * Note: This does NOT delete any data to prevent accidental data loss
     */
    public static function deactivate() {
        // We intentionally don't delete data on deactivation
        // Data cleanup happens on uninstall only
    }
}
