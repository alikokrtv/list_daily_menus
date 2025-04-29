<?php
/**
 * Admin template for managing locations
 *
 * @link       https://alikokdeneysel.online
 * @since      1.0.0
 */

// Prevent direct access
if (!defined('WPINC')) {
    die;
}

global $wpdb;
$table_name = $wpdb->prefix . 'dmm_locations';

// Get all locations
$locations = $wpdb->get_results("SELECT * FROM $table_name ORDER BY location_name ASC");
?>

<div class="wrap dmm-admin">
    <h1><?php _e('Daily Menu Manager', 'daily-menu-manager'); ?></h1>
    
    <div class="dmm-admin-header">
        <h2><?php _e('Manage Locations', 'daily-menu-manager'); ?></h2>
    </div>
    
    <div class="dmm-admin-content">
        <div class="dmm-locations-form-container">
            <h3><?php _e('Add/Edit Location', 'daily-menu-manager'); ?></h3>
            <form id="dmm-location-form">
                <input type="hidden" name="location_id" id="location_id" value="0">
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="location_name"><?php _e('Location Name', 'daily-menu-manager'); ?></label></th>
                        <td>
                            <input type="text" id="location_name" name="location_name" class="regular-text" required>
                            <p class="description"><?php _e('Enter a name for this location (e.g., "Main Building", "Cafeteria", "Restaurant")', 'daily-menu-manager'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="location_description"><?php _e('Description', 'daily-menu-manager'); ?></label></th>
                        <td>
                            <textarea id="location_description" name="location_description" rows="3" class="large-text"></textarea>
                            <p class="description"><?php _e('Optional description for this location', 'daily-menu-manager'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Status', 'daily-menu-manager'); ?></th>
                        <td>
                            <label for="is_active">
                                <input type="checkbox" id="is_active" name="is_active" checked>
                                <?php _e('Active', 'daily-menu-manager'); ?>
                            </label>
                            <p class="description"><?php _e('Inactive locations will not be available for new menus', 'daily-menu-manager'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Save Location', 'daily-menu-manager'); ?></button>
                    <button type="button" id="dmm-cancel-edit-location" class="button" style="display:none;"><?php _e('Cancel', 'daily-menu-manager'); ?></button>
                </p>
            </form>
        </div>
        
        <div class="dmm-locations-list-container">
            <h3><?php _e('Existing Locations', 'daily-menu-manager'); ?></h3>
            
            <?php if (empty($locations)) : ?>
                <p><?php _e('No locations found. Add your first location using the form.', 'daily-menu-manager'); ?></p>
            <?php else : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th scope="col"><?php _e('Location Name', 'daily-menu-manager'); ?></th>
                            <th scope="col"><?php _e('Description', 'daily-menu-manager'); ?></th>
                            <th scope="col"><?php _e('Status', 'daily-menu-manager'); ?></th>
                            <th scope="col"><?php _e('Actions', 'daily-menu-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($locations as $location) : ?>
                            <tr>
                                <td><?php echo esc_html($location->location_name); ?></td>
                                <td><?php echo esc_html($location->location_description); ?></td>
                                <td><?php echo $location->is_active ? __('Active', 'daily-menu-manager') : __('Inactive', 'daily-menu-manager'); ?></td>
                                <td>
                                    <button type="button" class="button dmm-edit-location" 
                                        data-id="<?php echo esc_attr($location->id); ?>"
                                        data-name="<?php echo esc_attr($location->location_name); ?>"
                                        data-description="<?php echo esc_attr($location->location_description); ?>"
                                        data-active="<?php echo esc_attr($location->is_active); ?>">
                                        <?php _e('Edit', 'daily-menu-manager'); ?>
                                    </button>
                                    <button type="button" class="button dmm-delete-location" 
                                        data-id="<?php echo esc_attr($location->id); ?>"
                                        data-name="<?php echo esc_attr($location->location_name); ?>">
                                        <?php _e('Delete', 'daily-menu-manager'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Handle form submission
    $('#dmm-location-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            'action': 'dmm_save_location',
            'nonce': dmm_ajax.nonce,
            'location_id': $('#location_id').val(),
            'location_name': $('#location_name').val(),
            'location_description': $('#location_description').val(),
            'is_active': $('#is_active').is(':checked') ? 1 : 0
        };
        
        $.post(dmm_ajax.ajax_url, formData, function(response) {
            if (response.success) {
                alert(response.message);
                window.location.reload();
            } else {
                alert(response.message || '<?php _e('An error occurred', 'daily-menu-manager'); ?>');
            }
        });
    });
    
    // Handle edit button click
    $('.dmm-edit-location').on('click', function() {
        const locationId = $(this).data('id');
        const locationName = $(this).data('name');
        const locationDescription = $(this).data('description');
        const isActive = $(this).data('active') == '1';
        
        $('#location_id').val(locationId);
        $('#location_name').val(locationName);
        $('#location_description').val(locationDescription);
        $('#is_active').prop('checked', isActive);
        
        $('#dmm-cancel-edit-location').show();
        $('html, body').animate({
            scrollTop: $('#dmm-location-form').offset().top - 100
        }, 500);
    });
    
    // Handle cancel edit button click
    $('#dmm-cancel-edit-location').on('click', function() {
        $('#location_id').val(0);
        $('#location_name').val('');
        $('#location_description').val('');
        $('#is_active').prop('checked', true);
        $(this).hide();
    });
    
    // Handle delete button click
    $('.dmm-delete-location').on('click', function() {
        const locationId = $(this).data('id');
        const locationName = $(this).data('name');
        
        if (confirm('<?php _e('Are you sure you want to delete the location', 'daily-menu-manager'); ?> "' + locationName + '"?\n<?php _e('This will not delete any menus associated with this location.', 'daily-menu-manager'); ?>')) {
            const formData = {
                'action': 'dmm_delete_location',
                'nonce': dmm_ajax.nonce,
                'location_id': locationId
            };
            
            $.post(dmm_ajax.ajax_url, formData, function(response) {
                if (response.success) {
                    alert(response.message);
                    window.location.reload();
                } else {
                    alert(response.message || '<?php _e('An error occurred', 'daily-menu-manager'); ?>');
                }
            });
        }
    });
});
</script>
