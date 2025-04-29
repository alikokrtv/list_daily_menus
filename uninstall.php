<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://alikokdeneysel.online
 * @since      1.0.0
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('dmm_display_navigation');
delete_option('dmm_default_style');
delete_option('dmm_excel_delimiter');
delete_option('dmm_date_format');

// Delete plugin database tables
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dmm_menus");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dmm_styles");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dmm_locations");
