<?php
/**
 * Define the internationalization functionality
 *
 * @link       https://alikokdeneysel.online
 * @since      1.0.0
 */

class DMM_i18n {

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'daily-menu-manager',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}
