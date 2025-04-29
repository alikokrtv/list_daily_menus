<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://alikokdeneysel.online
 * @since      1.0.0
 */

class DMM_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/dmm-admin.css', array(), $this->version, 'all');
        // Include WordPress color picker
        wp_enqueue_style('wp-color-picker');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/dmm-admin.js', array('jquery', 'jquery-ui-datepicker', 'wp-color-picker'), $this->version, false);
        
        // Localize the script with new data
        wp_localize_script($this->plugin_name, 'dmm_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
        
        // Include WordPress media uploader
        wp_enqueue_media();
    }
    
    /**
     * Add menu pages to the admin area
     */
    public function register_menu_pages() {
        add_menu_page(
            'Daily Menu Manager',
            'Menu Manager',
            'manage_options',
            'dmm-dashboard',
            array($this, 'display_dashboard'),
            'dashicons-food',
            30
        );
        
        add_submenu_page(
            'dmm-dashboard',
            'Menu Groups',
            'Menu Groups',
            'manage_options',
            'dmm-menu-groups',
            array($this, 'display_menu_groups_page')
        );
        
        add_submenu_page(
            'dmm-dashboard',
            'Daily Entries',
            'Daily Entries',
            'manage_options',
            'dmm-daily-entries',
            array($this, 'display_daily_entries_page')
        );
        
        add_submenu_page(
            'dmm-dashboard',
            'Menu Styles',
            'Menu Styles',
            'manage_options',
            'dmm-styling',
            array($this, 'display_plugin_styling')
        );
        
        // Submenu - Locations
        add_submenu_page(
            'daily-menu-manager',
            __('Manage Locations', 'daily-menu-manager'),
            __('Locations', 'daily-menu-manager'),
            'manage_options',
            'dmm-locations',
            array($this, 'display_plugin_locations')
        );
        
        // Submenu - Settings
        add_submenu_page(
            'daily-menu-manager',
            __('Settings', 'daily-menu-manager'),
            __('Settings', 'daily-menu-manager'),
            'manage_options',
            'dmm-settings',
            array($this, 'display_plugin_settings')
        );
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        // Register setting for plugin settings page
        register_setting('dmm_settings', 'dmm_display_navigation', array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '1'
        ));
        register_setting('dmm_settings', 'dmm_default_style', array(
            'sanitize_callback' => 'absint',
            'default' => 1
        ));
        register_setting('dmm_settings', 'dmm_excel_delimiter', array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ','
        ));
        register_setting('dmm_settings', 'dmm_date_format', array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'd/m/Y'
        ));
    }
    
    /**
     * Render the admin dashboard page
     */
    public function display_plugin_admin_dashboard() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/dmm-admin-dashboard.php';
    }
    
    /**
     * Render the import excel page
     */
    public function display_plugin_import_excel() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/dmm-admin-import-excel.php';
    }
    
    /**
     * Render the styling page
     */
    public function display_plugin_styling() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/dmm-admin-styling.php';
    }
    
    /**
     * Render the locations page
     */
    public function display_plugin_locations() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/dmm-admin-locations.php';
    }
    
    /**
     * Render the settings page
     */
    public function display_plugin_settings() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/dmm-admin-settings.php';
    }

    /**
     * AJAX handler for saving menu
     */
    public function ajax_save_menu() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'dmm_nonce')) {
            wp_die(__('Security check failed', 'daily-menu-manager'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'dmm_menus';
        
        $menu_id = isset($_POST['menu_id']) ? intval($_POST['menu_id']) : 0;
        $menu_date = sanitize_text_field($_POST['menu_date']);
        $menu_items = sanitize_textarea_field($_POST['menu_items']);
        $is_special = isset($_POST['is_special']) ? 1 : 0;
        $location = sanitize_text_field($_POST['location']);
        
        // Handle allowed roles
        $allowed_roles = isset($_POST['allowed_roles']) ? (array) $_POST['allowed_roles'] : array();
        $allowed_roles = array_map('sanitize_text_field', $allowed_roles);
        $allowed_roles_json = !empty($allowed_roles) ? json_encode($allowed_roles) : null;
        
        // Convert date format if needed
        $date_format = get_option('dmm_date_format', 'd/m/Y');
        $menu_date_obj = DateTime::createFromFormat($date_format, $menu_date);
        if ($menu_date_obj) {
            $menu_date = $menu_date_obj->format('Y-m-d');
        }
        
        $data = array(
            'menu_date' => $menu_date,
            'menu_items' => $menu_items,
            'is_special' => $is_special,
            'location' => $location,
            'allowed_roles' => $allowed_roles_json
        );
        
        $format = array(
            '%s',
            '%s',
            '%d',
            '%s',
            '%s'
        );
        
        if ($menu_id > 0) {
            // Update existing menu
            $wpdb->update(
                $table_name,
                $data,
                array('id' => $menu_id),
                $format,
                array('%d')
            );
            $response = array(
                'success' => true,
                'message' => __('Menu updated successfully', 'daily-menu-manager')
            );
        } else {
            // Insert new menu
            $wpdb->insert(
                $table_name,
                $data,
                $format
            );
            $response = array(
                'success' => true,
                'message' => __('Menu added successfully', 'daily-menu-manager'),
                'menu_id' => $wpdb->insert_id
            );
        }
        
        wp_send_json($response);
    }
    
    /**
     * AJAX handler for deleting menu
     */
    public function ajax_delete_menu() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'dmm_nonce')) {
            wp_die(__('Security check failed', 'daily-menu-manager'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'dmm_menus';
        
        $menu_id = intval($_POST['menu_id']);
        
        $wpdb->delete(
            $table_name,
            array('id' => $menu_id),
            array('%d')
        );
        
        $response = array(
            'success' => true,
            'message' => __('Menu deleted successfully', 'daily-menu-manager')
        );
        
        wp_send_json($response);
    }
    
    /**
     * AJAX handler for saving style
     */
    public function ajax_save_style() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'dmm_nonce')) {
            wp_die(__('Security check failed', 'daily-menu-manager'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'dmm_styles';
        
        $style_id = isset($_POST['style_id']) ? intval($_POST['style_id']) : 0;
        $style_name = sanitize_text_field($_POST['style_name']);
        $font_family = sanitize_text_field($_POST['font_family']);
        $font_size = sanitize_text_field($_POST['font_size']);
        $text_color = sanitize_text_field($_POST['text_color']);
        $background_color = sanitize_text_field($_POST['background_color']);
        $background_image = esc_url_raw($_POST['background_image']);
        $container_width = sanitize_text_field($_POST['container_width']);
        $border_style = sanitize_text_field($_POST['border_style']);
        $is_default = isset($_POST['is_default']) ? 1 : 0;
        
        $data = array(
            'style_name' => $style_name,
            'font_family' => $font_family,
            'font_size' => $font_size,
            'text_color' => $text_color,
            'background_color' => $background_color,
            'background_image' => $background_image,
            'container_width' => $container_width,
            'border_style' => $border_style,
            'is_default' => $is_default
        );
        
        $format = array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d'
        );
        
        // If setting as default, unset all other defaults
        if ($is_default) {
            $wpdb->update(
                $table_name,
                array('is_default' => 0),
                array('is_default' => 1),
                array('%d'),
                array('%d')
            );
        }
        
        if ($style_id > 0) {
            // Update existing style
            $wpdb->update(
                $table_name,
                $data,
                array('id' => $style_id),
                $format,
                array('%d')
            );
            $response = array(
                'success' => true,
                'message' => __('Style updated successfully', 'daily-menu-manager')
            );
        } else {
            // Insert new style
            $wpdb->insert(
                $table_name,
                $data,
                $format
            );
            $response = array(
                'success' => true,
                'message' => __('Style added successfully', 'daily-menu-manager'),
                'style_id' => $wpdb->insert_id
            );
        }
        
        wp_send_json($response);
    }
    
    /**
     * AJAX handler for deleting style
     */
    public function ajax_delete_style() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'dmm_nonce')) {
            wp_die(__('Security check failed', 'daily-menu-manager'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'dmm_styles';
        
        $style_id = intval($_POST['style_id']);
        
        // Check if style is default
        $is_default = $wpdb->get_var($wpdb->prepare("SELECT is_default FROM $table_name WHERE id = %d", $style_id));
        
        if ($is_default) {
            $response = array(
                'success' => false,
                'message' => __('Cannot delete the default style', 'daily-menu-manager')
            );
        } else {
            $wpdb->delete(
                $table_name,
                array('id' => $style_id),
                array('%d')
            );
            
            $response = array(
                'success' => true,
                'message' => __('Style deleted successfully', 'daily-menu-manager')
            );
        }
        
        wp_send_json($response);
    }
    
    /**
     * AJAX handler for importing from Excel
     */
    public function ajax_import_excel() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'dmm_nonce')) {
            wp_die(__('Security check failed', 'daily-menu-manager'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'dmm_menus';
        
        $excel_data = sanitize_textarea_field($_POST['excel_data']);
        $location = sanitize_text_field($_POST['location']);
        $delimiter = get_option('dmm_excel_delimiter', ',');
        $date_format = get_option('dmm_date_format', 'd/m/Y');
        
        $rows = explode("\n", $excel_data);
        $success_count = 0;
        $error_count = 0;
        
        foreach ($rows as $row) {
            if (empty(trim($row))) continue;
            
            // Use a custom delimiter recognition to properly handle menu items with commas
            // First column is the date
            $date_pos = strpos($row, $delimiter);
            if ($date_pos === false) {
                $error_count++;
                continue;
            }
            
            $menu_date = trim(substr($row, 0, $date_pos));
            
            // Last column might be the special flag (0 or 1)
            $remaining = substr($row, $date_pos + 1);
            $is_special = 0;
            
            // Check if the last character is 0 or 1 preceded by a delimiter
            if (preg_match('/,\s*(0|1)\s*$/', $remaining, $matches)) {
                $is_special = (int)trim($matches[1]);
                // Remove the special flag from the menu items
                $remaining = preg_replace('/,\s*(0|1)\s*$/', '', $remaining);
            }
            
            // Everything between first delimiter and special flag (or end) is the menu items
            $menu_items = trim($remaining);
            
            // Validate and format date
            $menu_date_obj = DateTime::createFromFormat($date_format, $menu_date);
            if (!$menu_date_obj) {
                $error_count++;
                continue;
            }
            
            $formatted_menu_date = $menu_date_obj->format('Y-m-d');
            
            // Check if menu for this date and location already exists
            $existing_menu = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table_name WHERE menu_date = %s AND location = %s",
                $formatted_menu_date,
                $location
            ));
            
            $data = array(
                'menu_date' => $formatted_menu_date,
                'menu_items' => $menu_items,
                'is_special' => $is_special,
                'location' => $location
            );
            
            $format = array('%s', '%s', '%d', '%s');
            
            if ($existing_menu) {
                // Update existing menu
                $wpdb->update(
                    $table_name,
                    $data,
                    array('id' => $existing_menu),
                    $format,
                    array('%d')
                );
            } else {
                // Insert new menu
                $wpdb->insert(
                    $table_name,
                    $data,
                    $format
                );
            }
            
            $success_count++;
        }
        
        $response = array(
            'success' => true,
            'message' => sprintf(
                __('%d menus imported successfully. %d errors encountered.', 'daily-menu-manager'),
                $success_count,
                $error_count
            )
        );
        
        wp_send_json($response);
    }
    
    /**
     * AJAX handler for saving location
     */
    public function ajax_save_location() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'dmm_nonce')) {
            wp_die(__('Security check failed', 'daily-menu-manager'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'dmm_locations';
        
        $location_id = isset($_POST['location_id']) ? intval($_POST['location_id']) : 0;
        $location_name = sanitize_text_field($_POST['location_name']);
        $location_description = sanitize_textarea_field($_POST['location_description']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $data = array(
            'location_name' => $location_name,
            'location_description' => $location_description,
            'is_active' => $is_active
        );
        
        $format = array('%s', '%s', '%d');
        
        if ($location_id > 0) {
            // Update existing location
            $wpdb->update(
                $table_name,
                $data,
                array('id' => $location_id),
                $format,
                array('%d')
            );
            $response = array(
                'success' => true,
                'message' => __('Location updated successfully', 'daily-menu-manager')
            );
        } else {
            // Insert new location
            $wpdb->insert(
                $table_name,
                $data,
                $format
            );
            $response = array(
                'success' => true,
                'message' => __('Location added successfully', 'daily-menu-manager'),
                'location_id' => $wpdb->insert_id
            );
        }
        
        wp_send_json($response);
    }
    
    /**
     * AJAX handler for deleting location
     */
    public function ajax_delete_location() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'dmm_nonce')) {
            wp_die(__('Security check failed', 'daily-menu-manager'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'dmm_locations';
        
        $location_id = intval($_POST['location_id']);
        
        // Check if there are menus using this location
        $menus_table = $wpdb->prefix . 'dmm_menus';
        $location_name = $wpdb->get_var($wpdb->prepare("SELECT location_name FROM $table_name WHERE id = %d", $location_id));
        
        $menu_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $menus_table WHERE location = %s", $location_name));
        
        if ($menu_count > 0) {
            $response = array(
                'success' => false,
                'message' => sprintf(
                    __('Cannot delete location. There are %d menus using this location.', 'daily-menu-manager'),
                    $menu_count
                )
            );
        } else {
            $wpdb->delete(
                $table_name,
                array('id' => $location_id),
                array('%d')
            );
            
            $response = array(
                'success' => true,
                'message' => __('Location deleted successfully', 'daily-menu-manager')
            );
        }
        
        wp_send_json($response);
    }
}
