<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://alikokdeneysel.online
 * @since      1.0.0
 */

class DMM_Public {

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
     * @param    string    $plugin_name       The name of the plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/dmm-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/dmm-public.js', array('jquery'), $this->version, false);
        
        // Pass variables to JavaScript
        wp_localize_script($this->plugin_name, 'dmm_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'date_format' => get_option('dmm_date_format', 'd/m/Y')
        ));
    }
    
    /**
     * Register shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('daily_menu', array($this, 'daily_menu_shortcode'));
    }
    
    /**
     * Shortcode for displaying daily menus
     */
    public function daily_menu_shortcode($atts) {
        // Extract shortcode attributes
        $atts = shortcode_atts(array(
            'location' => '',
            'style_id' => '',
            'date' => 'all',  // 'all', 'current', or specific date
            'navigation' => '', // '1' or '0' to override settings
            'roles' => '',     // comma-separated list of roles that can see this menu
        ), $atts, 'daily_menu');
        
        global $wpdb;
        
        // Get default style if not specified
        if (empty($atts['style_id'])) {
            $atts['style_id'] = get_option('dmm_default_style', 1);
        }
        
        // Get style settings
        $styles_table = $wpdb->prefix . 'dmm_styles';
        $style = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $styles_table WHERE id = %d",
            $atts['style_id']
        ));
        
        // If style not found, get default style
        if (!$style) {
            $style = $wpdb->get_row("SELECT * FROM $styles_table WHERE is_default = 1");
            if (!$style && $wpdb->get_var("SELECT COUNT(*) FROM $styles_table") > 0) {
                $style = $wpdb->get_row("SELECT * FROM $styles_table LIMIT 1");
            }
        }
        
        // Create a default style if none exists
        if (!$style) {
            $style = (object) array(
                'id' => 1,
                'font_family' => 'Arial, sans-serif',
                'font_size' => '16px',
                'text_color' => '#FFFFFF',
                'background_color' => 'rgba(0, 0, 0, 0.2)',
                'background_image' => '',
                'container_width' => '100%',
                'border_style' => 'none'
            );
        }
        
        // Get locations
        $locations_table = $wpdb->prefix . 'dmm_locations';
        if (empty($atts['location'])) {
            // Use first active location if none specified
            $location = $wpdb->get_var("SELECT location_name FROM $locations_table WHERE is_active = 1 LIMIT 1");
            if (!$location) {
                $location = 'Default Location';
            }
        } else {
            $location = $atts['location'];
        }
        
        // Build query for menus
        $menus_table = $wpdb->prefix . 'dmm_menus';
        $query = "SELECT * FROM $menus_table WHERE location = %s";
        $query_params = array($location);
        
        // Filter by date if needed
        if ($atts['date'] == 'current') {
            $today = date('Y-m-d');
            $query .= " AND menu_date = %s";
            $query_params[] = $today;
        } elseif ($atts['date'] != 'all' && $atts['date'] != '') {
            // Convert date format if needed
            $date_format = get_option('dmm_date_format', 'd/m/Y');
            $date_obj = DateTime::createFromFormat($date_format, $atts['date']);
            if ($date_obj) {
                $formatted_date = $date_obj->format('Y-m-d');
                $query .= " AND menu_date = %s";
                $query_params[] = $formatted_date;
            }
        }
        
        // Order by date
        $query .= " ORDER BY menu_date ASC";
        
        // Get menus
        $menus = $wpdb->get_results($wpdb->prepare($query, $query_params));
        
        // Filter menus based on user role access
        $filtered_menus = array();
        $current_user = wp_get_current_user();
        $current_user_roles = $current_user->roles;
        
        foreach ($menus as $menu) {
            // If no roles are specified, menu is visible to all
            if (empty($menu->allowed_roles)) {
                $filtered_menus[] = $menu;
                continue;
            }
            
            // Check if user has permission to view this menu
            $allowed_roles = json_decode($menu->allowed_roles, true);
            if (empty($allowed_roles)) {
                $filtered_menus[] = $menu;
                continue;
            }
            
            // Check if any of the user's roles match allowed roles
            $has_access = false;
            foreach ($current_user_roles as $role) {
                if (in_array($role, $allowed_roles)) {
                    $has_access = true;
                    break;
                }
            }
            
            // Add menu if user has access
            if ($has_access) {
                $filtered_menus[] = $menu;
            }
        }
        
        // Replace original menus with filtered ones
        $menus = $filtered_menus;
        
        // Check if we should show navigation buttons
        $show_navigation = false;
        if ($atts['navigation'] === '1') {
            $show_navigation = true;
        } elseif ($atts['navigation'] === '0') {
            $show_navigation = false;
        } else {
            $show_navigation = get_option('dmm_display_navigation', 1);
        }
        
        // Check if current user has permission to view this menu based on shortcode roles attribute
        if (!empty($atts['roles'])) {
            $allowed_roles = explode(',', $atts['roles']);
            $current_user = wp_get_current_user();
            $current_user_roles = $current_user->roles;
            
            $has_access = false;
            foreach ($current_user_roles as $role) {
                if (in_array($role, $allowed_roles)) {
                    $has_access = true;
                    break;
                }
            }
            
            if (!$has_access) {
                return '<div class="dmm-no-access">' . __('You do not have permission to view this menu.', 'daily-menu-manager') . '</div>';
            }
        }
        
        // Start building the output
        $output = '<div class="dmm-container" style="width: ' . esc_attr($style->container_width) . ';">';
        
        // If no menus found
        if (empty($menus)) {
            $output .= '<div class="dmm-no-menus" style="text-align: center; padding: 20px; font-family: ' . esc_attr($style->font_family) . ';">';
            $output .= __('No menus found for this location.', 'daily-menu-manager');
            $output .= '</div>';
            $output .= '</div>'; // Close .dmm-container
            return $output;
        }
        
        // Create date options for select
        $date_format = get_option('dmm_date_format', 'd/m/Y');
        $select_options = '';
        $special_dates = array();
        
        foreach ($menus as $menu) {
            $display_date = date($date_format, strtotime($menu->menu_date));
            $selected = ($menu->menu_date == date('Y-m-d')) ? ' selected' : '';
            $bold = $menu->is_special ? ' style="font-weight:bold;"' : '';
            $select_options .= '<option value="' . esc_attr($menu->id) . '"' . $selected . $bold . '>' . esc_html($display_date) . '</option>';
            
            if ($menu->is_special) {
                $special_dates[] = $menu->id;
            }
        }
        
        // Define JavaScript for menu display
        $output .= '<script>';
        $output .= 'document.addEventListener("DOMContentLoaded", function() {';
        
        // Build menu data object
        $output .= 'const menuData = {';
        foreach ($menus as $menu) {
            $menu_id = $menu->id;
            $menu_date = date($date_format, strtotime($menu->menu_date));
            $menu_items = addslashes($menu->menu_items);
            $output .= '"' . $menu_id . '": {';
            $output .= '"date": "' . $menu_date . '",';
            $output .= '"items": "' . $menu_items . '",';
            $output .= '"special": ' . ($menu->is_special ? 'true' : 'false');
            $output .= '},';
        }
        $output .= '};';
        
        // Create array of special dates
        $output .= 'const specialDates = ' . json_encode($special_dates) . ';';
        
        // Add function to update displayed menu
        $output .= 'function updateMenu(menuId) {';
        $output .= 'const menu = menuData[menuId];';
        $output .= 'if (menu) {';
        $output .= 'document.getElementById("dmm-selected-menu-text").textContent = menu.items;';
        
        // Handle special menu styling
        $output .= 'if (specialDates.includes(parseInt(menuId))) {';
        $output .= 'document.getElementById("dmm-selected-menu-text").classList.add("dmm-special-menu");';
        $output .= '} else {';
        $output .= 'document.getElementById("dmm-selected-menu-text").classList.remove("dmm-special-menu");';
        $output .= '}';
        
        $output .= '}';
        $output .= '}';
        
        // Set up event handlers
        $output .= 'const dateSelector = document.getElementById("dmm-menu-selector");';
        $output .= 'dateSelector.addEventListener("change", function() {';
        $output .= 'updateMenu(this.value);';
        $output .= '});';
        
        // Handle navigation buttons
        if ($show_navigation) {
            $output .= 'const prevButton = document.getElementById("dmm-prev-date");';
            $output .= 'const nextButton = document.getElementById("dmm-next-date");';
            
            $output .= 'prevButton.addEventListener("click", function() {';
            $output .= 'const currentIndex = dateSelector.selectedIndex;';
            $output .= 'if (currentIndex > 0) {';
            $output .= 'dateSelector.selectedIndex = currentIndex - 1;';
            $output .= 'updateMenu(dateSelector.value);';
            $output .= '}';
            $output .= '});';
            
            $output .= 'nextButton.addEventListener("click", function() {';
            $output .= 'const currentIndex = dateSelector.selectedIndex;';
            $output .= 'if (currentIndex < dateSelector.options.length - 1) {';
            $output .= 'dateSelector.selectedIndex = currentIndex + 1;';
            $output .= 'updateMenu(dateSelector.value);';
            $output .= '}';
            $output .= '});';
        }
        
        // Initialize with the first menu (or today's menu if available)
        $output .= 'updateMenu(dateSelector.value);';
        
        $output .= '});';
        $output .= '</script>';
        
        // Create form with date selector
        $output .= '<div class="dmm-selector-container" style="text-align:center; margin-bottom:15px; font-family:' . esc_attr($style->font_family) . ';">';
        $output .= '<form id="dmm-menu-form">';
        $output .= '<label for="dmm-menu-selector">' . __('Select Date:', 'daily-menu-manager') . '</label>';
        
        // Add navigation and selector controls
        $output .= '<div style="display: flex; align-items: center; justify-content: center; margin-top: 5px;">';
        
        if ($show_navigation) {
            $output .= '<button type="button" id="dmm-prev-date" class="dmm-nav-button" style="margin-right: 10px;">&lt;</button>';
        }
        
        $output .= '<select id="dmm-menu-selector" name="menu_id" style="text-align:center; font-size:' . esc_attr($style->font_size) . '; font-family:' . esc_attr($style->font_family) . ';">';
        $output .= $select_options;
        $output .= '</select>';
        
        if ($show_navigation) {
            $output .= '<button type="button" id="dmm-next-date" class="dmm-nav-button" style="margin-left: 10px;">&gt;</button>';
        }
        
        $output .= '</div>'; // Close flex container
        $output .= '</form>';
        $output .= '</div>'; // Close .dmm-selector-container
        
        // Create menu display container with style applied
        $output .= '<div id="dmm-menu-container" style="
            position: relative;
            background-image: ' . (empty($style->background_image) ? 'none' : 'url(\'' . esc_url($style->background_image) . '\')') . ';
            background-size: cover;
            background-position: center;
            border: ' . esc_attr($style->border_style) . ';
            border-radius: 12px;
            height: 200px;
            padding: 10px;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        ">';
        
        // Add overlay for background color
        $output .= '<div style="
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: ' . esc_attr($style->background_color) . ';
            z-index: 1;
        "></div>';
        
        // Add menu text container
        $output .= '<div id="dmm-selected-menu-text" style="
            position: relative;
            color: ' . esc_attr($style->text_color) . ';
            font-family: ' . esc_attr($style->font_family) . ';
            font-size: ' . esc_attr($style->font_size) . ';
            font-weight: bold;
            text-align: center;
            z-index: 2;
            padding: 15px;
        "></div>';
        
        $output .= '</div>'; // Close #dmm-menu-container
        
        // Add CSS for special menu animation
        $output .= '<style>
            .dmm-special-menu {
                animation: dmm-pulse 2s infinite;
            }
            @keyframes dmm-pulse {
                0% { text-shadow: 0 0 5px rgba(255,255,255,0.5); }
                50% { text-shadow: 0 0 20px rgba(255,255,255,0.8); }
                100% { text-shadow: 0 0 5px rgba(255,255,255,0.5); }
            }
        </style>';
        
        $output .= '</div>'; // Close .dmm-container
        
        return $output;
    }
}
