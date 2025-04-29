<?php
/**
 * Plugin Name: Daily Menu Manager
 * Plugin URI: https://profiles.wordpress.org/alikok/
 * Description: Comprehensive menu management plugin with admin panel, styling options, and Excel import
 * Version: 1.0.0
 * Author: alikok
 * Author URI: https://profiles.wordpress.org/alikok/
 * Text Domain: daily-menu-manager
 * Domain Path: /languages
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package Daily_Menu_Manager
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('DMM_VERSION', '1.0.0');
define('DMM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DMM_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_daily_menu_manager() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-dmm-activator.php';
    DMM_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_daily_menu_manager() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-dmm-deactivator.php';
    DMM_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_daily_menu_manager');
register_deactivation_hook(__FILE__, 'deactivate_daily_menu_manager');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-dmm.php';

/**
 * Begins execution of the plugin.
 */
function run_daily_menu_manager() {
    $plugin = new DMM();
    $plugin->run();
}

run_daily_menu_manager();
