<?php
/**
 * Fired during plugin activation
 *
 * @link       https://alikokdeneysel.online
 * @since      1.0.0
 */

class DMM_Activator {

    /**
     * Create necessary database tables and set up default options during activation
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Check if we have the old table structure
        $old_table_name = $wpdb->prefix . 'dmm_menus';
        $old_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$old_table_name'") === $old_table_name;
        
        if ($old_table_exists) {
            // Check if the old table has the location column (old structure)
            $old_columns = $wpdb->get_results("SHOW COLUMNS FROM $old_table_name");
            $has_old_structure = false;
            foreach ($old_columns as $column) {
                if ($column->Field === 'location') {
                    $has_old_structure = true;
                    break;
                }
            }
            
            if ($has_old_structure) {
                // This is the old structure, we need to convert it
                self::upgrade_database();
                return;
            }
        }
        
        // If we get here, either we're doing a fresh install or the tables are already in the new structure
        
        // Create the menu_groups table (for monthly/period menus)
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}dmm_menu_groups (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(100) NOT NULL,
            location varchar(100) NOT NULL,
            period_start date NOT NULL,
            period_end date NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            bg_image varchar(255) DEFAULT '',
            allowed_roles text DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql);
        
        // Create the daily menus table (for daily entries within a menu group)
        $table_name = $wpdb->prefix . 'dmm_menus';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            group_id mediumint(9) NOT NULL,
            menu_date date NOT NULL,
            menu_items text NOT NULL,
            is_special tinyint(1) DEFAULT 0,
            PRIMARY KEY (id),
            KEY group_id (group_id),
            KEY menu_date (menu_date)
        ) $charset_collate;";
        dbDelta($sql);
        
        // Create styles table
        $style_table = $wpdb->prefix . 'dmm_styles';
        $sql = "CREATE TABLE IF NOT EXISTS $style_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            style_name varchar(100) NOT NULL,
            font_family varchar(255) DEFAULT 'Arial, sans-serif',
            font_size varchar(50) DEFAULT '16px',
            text_color varchar(50) DEFAULT '#000000',
            background_color varchar(50) DEFAULT '#FFFFFF',
            container_width varchar(50) DEFAULT '100%',
            border_style varchar(255) DEFAULT 'none',
            is_default tinyint(1) DEFAULT 0,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        // Create locations table
        $locations_table = $wpdb->prefix . 'dmm_locations';
        $locations_sql = "CREATE TABLE $locations_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            location_name varchar(255) NOT NULL,
            location_description text DEFAULT '',
            is_active tinyint(1) DEFAULT 1,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        dbDelta($style_sql);
        dbDelta($locations_sql);
        
        // Insert default style if none exists
        $style_count = $wpdb->get_var("SELECT COUNT(*) FROM $style_table");
        if ($style_count == 0) {
            $wpdb->insert(
                $style_table,
                array(
                    'style_name' => 'Default Style',
                    'font_family' => 'Arial, sans-serif',
                    'font_size' => '16px',
                    'text_color' => '#FFFFFF',
                    'background_color' => 'rgba(0, 0, 0, 0.2)',
                    'background_image' => 'https://portal.pluskitchen.com.tr/wp-content/uploads/brizy/imgs/i-624x417x0x40x624x337x1713185391.webp',
                    'container_width' => '100%',
                    'border_style' => 'none',
                    'is_default' => 1
                )
            );
        }
        
        // Add default location
        $default_location_exists = $wpdb->get_var("SELECT COUNT(*) FROM $locations_table");
        if ($default_location_exists == 0) {
            $wpdb->insert(
                $locations_table,
                array(
                    'location_name' => 'Default Location',
                    'location_description' => 'Default location for menus',
                    'is_active' => 1
                )
            );
        }
        
        // Add default options
        add_option('dmm_display_navigation', 1);
        add_option('dmm_default_style', 1);
        add_option('dmm_excel_delimiter', ',');
        add_option('dmm_date_format', 'd/m/Y');
    }
}
