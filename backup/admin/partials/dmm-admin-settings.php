<?php
/**
 * Admin template for plugin settings
 *
 * @link       https://alikokdeneysel.online
 * @since      1.0.0
 */

// Prevent direct access
if (!defined('WPINC')) {
    die;
}

global $wpdb;
$styles_table = $wpdb->prefix . 'dmm_styles';

// Get all styles for the dropdown
$styles = $wpdb->get_results("SELECT id, style_name FROM $styles_table ORDER BY style_name ASC");

// Get current settings
$display_navigation = get_option('dmm_display_navigation', 1);
$default_style = get_option('dmm_default_style', 1);
$excel_delimiter = get_option('dmm_excel_delimiter', ',');
$date_format = get_option('dmm_date_format', 'd/m/Y');
?>

<div class="wrap dmm-admin">
    <h1><?php _e('Daily Menu Manager', 'daily-menu-manager'); ?></h1>
    
    <div class="dmm-admin-header">
        <h2><?php _e('Settings', 'daily-menu-manager'); ?></h2>
    </div>
    
    <div class="dmm-admin-content">
        <form method="post" action="options.php">
            <?php settings_fields('dmm_settings'); ?>
            <?php do_settings_sections('dmm_settings'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Display Navigation', 'daily-menu-manager'); ?></th>
                    <td>
                        <label for="dmm_display_navigation">
                            <input type="checkbox" id="dmm_display_navigation" name="dmm_display_navigation" value="1" <?php checked($display_navigation, 1); ?>>
                            <?php _e('Show navigation buttons (Previous/Next) in menu display', 'daily-menu-manager'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="dmm_default_style"><?php _e('Default Style', 'daily-menu-manager'); ?></label></th>
                    <td>
                        <select id="dmm_default_style" name="dmm_default_style">
                            <?php foreach ($styles as $style) : ?>
                                <option value="<?php echo esc_attr($style->id); ?>" <?php selected($default_style, $style->id); ?>>
                                    <?php echo esc_html($style->style_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php _e('Select the default style to use for menu display', 'daily-menu-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="dmm_excel_delimiter"><?php _e('Excel Delimiter', 'daily-menu-manager'); ?></label></th>
                    <td>
                        <select id="dmm_excel_delimiter" name="dmm_excel_delimiter">
                            <option value="," <?php selected($excel_delimiter, ','); ?>><?php _e('Comma (,)', 'daily-menu-manager'); ?></option>
                            <option value=";" <?php selected($excel_delimiter, ';'); ?>><?php _e('Semicolon (;)', 'daily-menu-manager'); ?></option>
                            <option value="\t" <?php selected($excel_delimiter, '\t'); ?>><?php _e('Tab', 'daily-menu-manager'); ?></option>
                        </select>
                        <p class="description"><?php _e('Select the delimiter character used in Excel imports', 'daily-menu-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="dmm_date_format"><?php _e('Date Format', 'daily-menu-manager'); ?></label></th>
                    <td>
                        <select id="dmm_date_format" name="dmm_date_format">
                            <option value="d/m/Y" <?php selected($date_format, 'd/m/Y'); ?>><?php _e('DD/MM/YYYY (31/12/2025)', 'daily-menu-manager'); ?></option>
                            <option value="m/d/Y" <?php selected($date_format, 'm/d/Y'); ?>><?php _e('MM/DD/YYYY (12/31/2025)', 'daily-menu-manager'); ?></option>
                            <option value="Y-m-d" <?php selected($date_format, 'Y-m-d'); ?>><?php _e('YYYY-MM-DD (2025-12-31)', 'daily-menu-manager'); ?></option>
                            <option value="d.m.Y" <?php selected($date_format, 'd.m.Y'); ?>><?php _e('DD.MM.YYYY (31.12.2025)', 'daily-menu-manager'); ?></option>
                            <option value="d-m-Y" <?php selected($date_format, 'd-m-Y'); ?>><?php _e('DD-MM-YYYY (31-12-2025)', 'daily-menu-manager'); ?></option>
                        </select>
                        <p class="description"><?php _e('Select the date format for display and input', 'daily-menu-manager'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(__('Save Settings', 'daily-menu-manager')); ?>
        </form>
        
        <div class="dmm-settings-info">
            <h3><?php _e('Shortcode Usage', 'daily-menu-manager'); ?></h3>
            <p><?php _e('Use the following shortcode to display menus on your pages or posts:', 'daily-menu-manager'); ?></p>
            <code>[daily_menu]</code>
            
            <p><?php _e('Optional parameters:', 'daily-menu-manager'); ?></p>
            <ul>
                <li><code>location="Location Name"</code> - <?php _e('Display menus for a specific location', 'daily-menu-manager'); ?></li>
                <li><code>style_id="1"</code> - <?php _e('Use a specific style (by ID)', 'daily-menu-manager'); ?></li>
                <li><code>date="current"</code> - <?php _e('Show only current date\'s menu (or "all" for all dates)', 'daily-menu-manager'); ?></li>
                <li><code>navigation="1"</code> - <?php _e('Override navigation setting (1=show, 0=hide)', 'daily-menu-manager'); ?></li>
            </ul>
            
            <p><?php _e('Example:', 'daily-menu-manager'); ?></p>
            <code>[daily_menu location="Cafeteria" style_id="2" date="current" navigation="1"]</code>
            
            <h3><?php _e('User Permissions', 'daily-menu-manager'); ?></h3>
            <p><?php _e('This plugin uses the WordPress "manage_options" capability, so only Administrators can manage menus by default.', 'daily-menu-manager'); ?></p>
            
            <h3><?php _e('Support', 'daily-menu-manager'); ?></h3>
            <p><?php _e('For support or feature requests, please contact the plugin author:', 'daily-menu-manager'); ?></p>
            <p><a href="https://alikokdeneysel.online" target="_blank">alikokdeneysel.online</a></p>
        </div>
    </div>
</div>
