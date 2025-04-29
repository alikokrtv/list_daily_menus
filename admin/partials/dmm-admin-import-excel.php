<?php
/**
 * Admin template for Excel import functionality
 *
 * @link       https://alikokdeneysel.online
 * @since      1.0.0
 */

// Prevent direct access
if (!defined('WPINC')) {
    die;
}

global $wpdb;
$locations_table = $wpdb->prefix . 'dmm_locations';

// Get available locations
$locations = $wpdb->get_results("SELECT * FROM $locations_table WHERE is_active = 1");
$date_format = get_option('dmm_date_format', 'd/m/Y');
$delimiter = get_option('dmm_excel_delimiter', ',');
?>

<div class="wrap dmm-admin">
    <h1><?php _e('Daily Menu Manager', 'daily-menu-manager'); ?></h1>
    
    <div class="dmm-admin-header">
        <h2><?php _e('Import Menus from Excel', 'daily-menu-manager'); ?></h2>
    </div>
    
    <div class="dmm-admin-content">
        <div class="dmm-import-instructions">
            <h3><?php _e('Instructions', 'daily-menu-manager'); ?></h3>
            <p><?php _e('You can import menu data from Excel by copying and pasting data from Excel into the text area below.', 'daily-menu-manager'); ?></p>
            <p><?php _e('Format your Excel data as follows:', 'daily-menu-manager'); ?></p>
            <ol>
                <li><?php printf(__('First column: Date (in format: %s)', 'daily-menu-manager'), $date_format); ?></li>
                <li><?php _e('Second column: Menu items (comma separated)', 'daily-menu-manager'); ?></li>
                <li><?php _e('Third column (optional): Special menu (1 for special, 0 or empty for normal)', 'daily-menu-manager'); ?></li>
            </ol>
            <p><?php printf(__('Columns should be separated by: %s', 'daily-menu-manager'), $delimiter); ?></p>
            
            <div class="dmm-excel-template">
                <h4><?php _e('Example:', 'daily-menu-manager'); ?></h4>
                <pre>
<?php echo esc_html("01/05/2025{$delimiter}Soup, Main Course, Side Dish, Dessert{$delimiter}0
02/05/2025{$delimiter}Another Soup, Another Main, Rice, Fruit{$delimiter}1
03/05/2025{$delimiter}Weekend Special Menu{$delimiter}1"); ?>
                </pre>
                <p>
                    <a href="#" id="dmm-download-template" class="button button-secondary">
                        <?php _e('Download Excel Template', 'daily-menu-manager'); ?>
                    </a>
                </p>
            </div>
        </div>
        
        <div class="dmm-import-form-container">
            <h3><?php _e('Import Data', 'daily-menu-manager'); ?></h3>
            <form id="dmm-import-form">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="import_location"><?php _e('Location', 'daily-menu-manager'); ?></label></th>
                        <td>
                            <select id="import_location" name="import_location" required>
                                <?php foreach ($locations as $location) : ?>
                                    <option value="<?php echo esc_attr($location->location_name); ?>">
                                        <?php echo esc_html($location->location_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('Select the location for these menus', 'daily-menu-manager'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="excel_data"><?php _e('Excel Data', 'daily-menu-manager'); ?></label></th>
                        <td>
                            <textarea id="excel_data" name="excel_data" rows="10" class="large-text" required></textarea>
                            <p class="description"><?php _e('Copy and paste data from Excel', 'daily-menu-manager'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Import Menus', 'daily-menu-manager'); ?></button>
                </p>
            </form>
            
            <div id="dmm-import-results" style="display: none;">
                <h3><?php _e('Import Results', 'daily-menu-manager'); ?></h3>
                <div id="dmm-import-message"></div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Handle form submission
    $('#dmm-import-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            'action': 'dmm_import_excel',
            'nonce': dmm_ajax.nonce,
            'excel_data': $('#excel_data').val(),
            'location': $('#import_location').val()
        };
        
        // Show loading indicator
        $('#dmm-import-results').show();
        $('#dmm-import-message').html('<p><?php _e('Importing data, please wait...', 'daily-menu-manager'); ?></p>');
        
        $.post(dmm_ajax.ajax_url, formData, function(response) {
            if (response.success) {
                $('#dmm-import-message').html('<div class="notice notice-success"><p>' + response.message + '</p></div>');
            } else {
                $('#dmm-import-message').html('<div class="notice notice-error"><p>' + 
                    (response.message || '<?php _e('An error occurred during import', 'daily-menu-manager'); ?>') + '</p></div>');
            }
        });
    });
    
    // Handle template download
    $('#dmm-download-template').on('click', function(e) {
        e.preventDefault();
        
        // Create CSV content
        const delimiter = '<?php echo esc_js($delimiter); ?>';
        const csvContent = 
            "<?php _e('Date', 'daily-menu-manager'); ?>" + delimiter + 
            "<?php _e('Menu Items', 'daily-menu-manager'); ?>" + delimiter + 
            "<?php _e('Special (1=yes, 0=no)', 'daily-menu-manager'); ?>\n" +
            "<?php echo esc_js("01/05/2025{$delimiter}Soup, Main Course, Side Dish, Dessert{$delimiter}0"); ?>\n" +
            "<?php echo esc_js("02/05/2025{$delimiter}Another Soup, Another Main, Rice, Fruit{$delimiter}1"); ?>\n" +
            "<?php echo esc_js("03/05/2025{$delimiter}Weekend Special Menu{$delimiter}1"); ?>";
        
        // Create download link
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", "menu_template.csv");
        document.body.appendChild(link);
        
        // Trigger download and clean up
        link.click();
        setTimeout(function() {
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        }, 100);
    });
});
</script>
