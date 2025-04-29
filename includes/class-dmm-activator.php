<?php
/**
 * Fired during plugin activation
 *
 * @link       https://alikokdeneysel.online
 * @since      1.0.0
 */

class DMM_Activator {

    /**
     * Called when the plugin is activated.
     * Create necessary database tables and default data.
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
            name varchar(100) NOT NULL,
            description text DEFAULT '',
            start_date date NOT NULL,
            end_date date NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
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
        dbDelta($sql);
        
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
                    'background_color' => 'rgba(0, 0, 0, 0.6)',
                    'container_width' => '100%',
                    'border_style' => '1px solid #ddd',
                    'is_default' => 1
                )
            );
        }
        
        // Add default options
        add_option('dmm_display_navigation', 1);
        add_option('dmm_default_style', 1);
        add_option('dmm_excel_delimiter', ',');
        add_option('dmm_date_format', 'd/m/Y');
    }
    
    /**
     * Upgrade the database from old structure to new structure
     */
    public static function upgrade_database() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // First, create the new tables without disturbing the existing ones
        $temp_group_table = $wpdb->prefix . 'dmm_menu_groups_temp';
        $sql = "CREATE TABLE IF NOT EXISTS $temp_group_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            description text DEFAULT '',
            start_date date NOT NULL,
            end_date date NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        $wpdb->query($sql);
        
        $temp_menu_table = $wpdb->prefix . 'dmm_menus_temp';
        $sql = "CREATE TABLE IF NOT EXISTS $temp_menu_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            group_id mediumint(9) NOT NULL,
            menu_date date NOT NULL,
            menu_items text NOT NULL,
            is_special tinyint(1) DEFAULT 0,
            PRIMARY KEY (id),
            KEY group_id (group_id),
            KEY menu_date (menu_date)
        ) $charset_collate;";
        $wpdb->query($sql);
        
        // Get all existing menus
        $old_table = $wpdb->prefix . 'dmm_menus';
        $old_menus = $wpdb->get_results("SELECT * FROM $old_table ORDER BY menu_date ASC");
        
        if (!empty($old_menus)) {
            // Create a group for existing menus
            $today = date('Y-m-d');
            $thirty_days_ago = date('Y-m-d', strtotime('-30 days'));
            $sixty_days_ahead = date('Y-m-d', strtotime('+60 days'));
            
            // Get unique locations
            $locations = array();
            foreach ($old_menus as $menu) {
                if (!empty($menu->location) && !in_array($menu->location, $locations)) {
                    $locations[] = $menu->location;
                }
            }
            
            // If no locations defined, use a default
            if (empty($locations)) {
                $locations[] = 'Default Location';
            }
            
            // Create a group for each location
            foreach ($locations as $location) {
                // Insert into the temp table
                $wpdb->insert(
                    $temp_group_table,
                    array(
                        'title' => 'Imported Menu - ' . $location,
                        'location' => $location,
                        'period_start' => $thirty_days_ago,
                        'period_end' => $sixty_days_ahead,
                        'is_active' => 1,
                        'bg_image' => '',
                        'allowed_roles' => '',
                        'created_at' => current_time('mysql')
                    )
                );
                
                $group_id = $wpdb->insert_id;
                
                // Add menus for this location to the new structure
                foreach ($old_menus as $menu) {
                    if ((empty($menu->location) && $location === 'Default Location') || $menu->location === $location) {
                        $wpdb->insert(
                            $temp_menu_table,
                            array(
                                'group_id' => $group_id,
                                'menu_date' => $menu->menu_date,
                                'menu_items' => $menu->menu_items,
                                'is_special' => $menu->is_special
                            )
                        );
                    }
                }
            }
        }
        
        // Now we can safely rename tables
        $wpdb->query("RENAME TABLE $old_table TO {$old_table}_backup");
        $wpdb->query("RENAME TABLE $temp_menu_table TO $old_table");
        $wpdb->query("RENAME TABLE $temp_group_table TO {$wpdb->prefix}dmm_menu_groups");
    }
}
