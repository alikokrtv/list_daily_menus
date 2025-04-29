<?php
/*
Plugin Name: Daily Menu Manager
Description: Comprehensive menu management plugin with admin panel, styling options, and Excel import
Version: 1.0.0
Author: alikok
Author URI: https://profiles.wordpress.org/alikok/
Text Domain: daily-menu-manager
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('DMM_VERSION', '1.0.0');
define('DMM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DMM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DMM_ADMIN_URL', admin_url('admin.php?page=daily-menu-manager'));

// Include required files
require_once DMM_PLUGIN_DIR . 'includes/class-dmm-activator.php';
require_once DMM_PLUGIN_DIR . 'includes/class-dmm-deactivator.php';
require_once DMM_PLUGIN_DIR . 'includes/class-dmm-loader.php';
require_once DMM_PLUGIN_DIR . 'includes/class-dmm-i18n.php';
require_once DMM_PLUGIN_DIR . 'includes/class-daily-menu-manager.php';
require_once DMM_PLUGIN_DIR . 'admin/class-dmm-admin.php';
require_once DMM_PLUGIN_DIR . 'public/class-dmm-public.php';

// Activation hook
register_activation_hook(__FILE__, array('DMM_Activator', 'activate'));

// Deactivation hook
register_deactivation_hook(__FILE__, array('DMM_Deactivator', 'deactivate'));

/**
 * Start the plugin execution
 */
function run_daily_menu_manager() {
    $plugin = new Daily_Menu_Manager();
    $plugin->run();
}

run_daily_menu_manager();
