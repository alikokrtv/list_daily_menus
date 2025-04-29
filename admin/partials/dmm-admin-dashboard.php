<?php
/**
 * Admin dashboard template - Menu management
 *
 * @link       https://alikokdeneysel.online
 * @since      1.0.0
 */

// Prevent direct access
if (!defined('WPINC')) {
    die;
}

global $wpdb;
$table_name = $wpdb->prefix . 'dmm_menus';
$locations_table = $wpdb->prefix . 'dmm_locations';

// Get available locations
$locations = $wpdb->get_results("SELECT * FROM $locations_table WHERE is_active = 1");
if (empty($locations)) {
    // Create default location if none exists
    $wpdb->insert(
        $locations_table,
        array(
            'location_name' => 'Default Location',
            'location_description' => 'Default location for menus',
            'is_active' => 1
        )
    );
    $locations = $wpdb->get_results("SELECT * FROM $locations_table WHERE is_active = 1");
}

// Get selected location (default to first one)
$selected_location = isset($_GET['location']) ? sanitize_text_field($_GET['location']) : $locations[0]->location_name;

// Get menus for the selected location
$date_format = get_option('dmm_date_format', 'd/m/Y');
$menus = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $table_name WHERE location = %s ORDER BY menu_date ASC",
    $selected_location
));
?>

<div class="wrap dmm-admin">
    <h1><?php _e('Daily Menu Manager', 'daily-menu-manager'); ?></h1>
    
    <div class="dmm-admin-header">
        <h2><?php _e('Manage Menus', 'daily-menu-manager'); ?></h2>
        <div class="dmm-location-selector">
            <form method="get">
                <input type="hidden" name="page" value="daily-menu-manager">
                <label for="location"><?php _e('Select Location:', 'daily-menu-manager'); ?></label>
                <select name="location" id="location" onchange="this.form.submit()">
                    <?php foreach ($locations as $location) : ?>
                        <option value="<?php echo esc_attr($location->location_name); ?>" <?php selected($location->location_name, $selected_location); ?>>
                            <?php echo esc_html($location->location_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>
    
    <div class="dmm-admin-content">
        <div class="dmm-menu-form-container">
            <h3><?php _e('Add/Edit Menu', 'daily-menu-manager'); ?></h3>
            <form id="dmm-menu-form">
                <input type="hidden" name="menu_id" id="menu_id" value="0">
                <input type="hidden" name="location" id="menu_location" value="<?php echo esc_attr($selected_location); ?>">
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="menu_date"><?php _e('Date', 'daily-menu-manager'); ?></label></th>
                        <td>
                            <input type="text" id="menu_date" name="menu_date" class="regular-text dmm-datepicker" 
                                placeholder="<?php echo esc_attr($date_format); ?>" required>
                            <p class="description"><?php printf(__('Enter date in format: %s', 'daily-menu-manager'), $date_format); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="menu_items"><?php _e('Menu Items', 'daily-menu-manager'); ?></label></th>
                        <td>
                            <textarea id="menu_items" name="menu_items" rows="5" class="large-text" required></textarea>
                            <p class="description"><?php _e('Enter menu items separated by commas', 'daily-menu-manager'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Special Menu', 'daily-menu-manager'); ?></th>
                        <td>
                            <label for="is_special">
                                <input type="checkbox" id="is_special" name="is_special">
                                <?php _e('Mark as special menu (will be highlighted)', 'daily-menu-manager'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Access Control', 'daily-menu-manager'); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php _e('User roles that can view this menu', 'daily-menu-manager'); ?></legend>
                                <?php
                                // Get all available roles
                                $roles = wp_roles()->get_names();
                                foreach ($roles as $role_id => $role_name) :
                                ?>
                                <label>
                                    <input type="checkbox" name="allowed_roles[]" value="<?php echo esc_attr($role_id); ?>" class="allowed-roles-cb">
                                    <?php echo esc_html($role_name); ?>
                                </label><br>
                                <?php endforeach; ?>
                                <p class="description"><?php _e('Leave all unchecked to allow all users to view this menu.', 'daily-menu-manager'); ?></p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Background Image', 'daily-menu-manager'); ?></th>
                        <td>
                            <input type="text" id="background_image" name="background_image" class="regular-text" value="">
                            <button type="button" id="upload-bg-image" class="button"><?php _e('Upload Image', 'daily-menu-manager'); ?></button>
                            <p class="description"><?php _e('Select a background image for this menu (optional)', 'daily-menu-manager'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Preview', 'daily-menu-manager'); ?></th>
                        <td>
                            <div id="menu-preview-container" style="position: relative; width: 100%; height: 150px; background-size: cover; background-position: center; border-radius: 10px; overflow: hidden; margin-bottom: 10px;">
                                <div id="menu-preview-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.2); z-index: 1;"></div>
                                <div id="menu-preview-text" style="position: relative; color: white; font-weight: bold; text-align: center; z-index: 2; padding: 20px; display: flex; align-items: center; justify-content: center; height: 100%;"></div>
                            </div>
                            <p class="description"><?php _e('Live preview of how the menu will appear on the frontend.', 'daily-menu-manager'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Save Menu', 'daily-menu-manager'); ?></button>
                    <button type="button" id="dmm-cancel-edit" class="button" style="display:none;"><?php _e('Cancel', 'daily-menu-manager'); ?></button>
                </p>
            </form>
        </div>
        
        <div class="dmm-menu-list-container">
            <h3><?php _e('Existing Menus', 'daily-menu-manager'); ?></h3>
            
            <?php if (empty($menus)) : ?>
                <p><?php _e('No menus found for this location. Add your first menu using the form.', 'daily-menu-manager'); ?></p>
            <?php else : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th scope="col"><?php _e('Date', 'daily-menu-manager'); ?></th>
                            <th scope="col"><?php _e('Menu Items', 'daily-menu-manager'); ?></th>
                            <th scope="col"><?php _e('Special', 'daily-menu-manager'); ?></th>
                            <th scope="col"><?php _e('Actions', 'daily-menu-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($menus as $menu) : 
                            // Convert SQL date to display format
                            $display_date = date($date_format, strtotime($menu->menu_date));
                        ?>
                            <tr>
                                <td><?php echo esc_html($display_date); ?></td>
                                <td><?php echo esc_html($menu->menu_items); ?></td>
                                <td><?php echo $menu->is_special ? '✓' : ''; ?></td>
                                <td>
                                    <button type="button" class="button dmm-edit-menu" 
                                        data-id="<?php echo esc_attr($menu->id); ?>"
                                        data-date="<?php echo esc_attr($display_date); ?>"
                                        data-items="<?php echo esc_attr($menu->menu_items); ?>"
                                        data-special="<?php echo esc_attr($menu->is_special); ?>"
                                        data-roles="<?php echo esc_attr($menu->allowed_roles ? $menu->allowed_roles : ''); ?>">
                                        <?php _e('Edit', 'daily-menu-manager'); ?>
                                    </button>
                                    <button type="button" class="button dmm-delete-menu" 
                                        data-id="<?php echo esc_attr($menu->id); ?>"
                                        data-date="<?php echo esc_attr($display_date); ?>">
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
    
    <div id="dmm-confirmation-dialog" title="<?php _e('Confirm Delete', 'daily-menu-manager'); ?>" style="display:none;">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>
        <?php _e('Are you sure you want to delete this menu?', 'daily-menu-manager'); ?></p>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize datepicker
    if ($.fn.datepicker) {
        $('.dmm-datepicker').datepicker({
            dateFormat: '<?php echo esc_js($date_format); ?>',
            changeMonth: true,
            changeYear: true
        });
    }
    
    // Live preview functionality
    function updatePreview() {
        var menuItems = $('#menu_items').val();
        var isSpecial = $('#is_special').is(':checked');
        var bgImage = $('#background_image').val();
        
        // Update preview text
        $('#menu-preview-text').text(menuItems);
        
        // Update special styling if needed
        if(isSpecial) {
            $('#menu-preview-text').addClass('dmm-special-preview');
        } else {
            $('#menu-preview-text').removeClass('dmm-special-preview');
        }
        
        // Update background image if provided
        if(bgImage) {
            $('#menu-preview-container').css({
                'background-image': 'url(' + bgImage + ')'
            });
        } else {
            $('#menu-preview-container').css({
                'background-image': 'none',
                'background-color': '#f5f5f5'
            });
        }
    }
    
    // Trigger preview updates when menu items change
    $('#menu_items').on('input', updatePreview);
    $('#is_special').on('change', updatePreview);
    $('#background_image').on('input', updatePreview);
    
    // Handle background image upload button
    $('#upload-bg-image').on('click', function(e) {
        e.preventDefault();
        
        var image_frame;
        
        if(image_frame) {
            image_frame.open();
            return;
        }
        
        // Create the media frame
        image_frame = wp.media({
            title: '<?php _e("Select or Upload Background Image", "daily-menu-manager"); ?>',
            button: {
                text: '<?php _e("Use this image", "daily-menu-manager"); ?>'
            },
            multiple: false
        });
        
        // When an image is selected in the media frame
        image_frame.on('select', function() {
            // Get media attachment details from the frame state
            var attachment = image_frame.state().get('selection').first().toJSON();
            
            // Set image url in the input field
            $('#background_image').val(attachment.url);
            
            // Update preview
            updatePreview();
        });
        
        // Open the modal
        image_frame.open();        
    });
    
    // Add CSS for the preview special menu animation
    $('head').append('<style>\n\
        .dmm-special-preview {\n\
            animation: dmm-preview-pulse 2s infinite;\n\
        }\n\
        @keyframes dmm-preview-pulse {\n\
            0% { text-shadow: 0 0 5px rgba(255,255,255,0.5); }\n\
            50% { text-shadow: 0 0 20px rgba(255,255,255,0.8); }\n\
            100% { text-shadow: 0 0 5px rgba(255,255,255,0.5); }\n\
        }\n\
    </style>');
    
    // Initialize preview on page load
    updatePreview();
    
    // Handle form submission
    $('#dmm-menu-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            'action': 'dmm_save_menu',
            'nonce': dmm_ajax.nonce,
            'menu_id': $('#menu_id').val(),
            'menu_date': $('#menu_date').val(),
            'menu_items': $('#menu_items').val(),
            'is_special': $('#is_special').is(':checked') ? 1 : 0,
            'location': $('#menu_location').val()
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
    $('.dmm-edit-menu').on('click', function() {
        const menuId = $(this).data('id');
        const menuDate = $(this).data('date');
        const menuItems = $(this).data('items');
        const isSpecial = $(this).data('special') == '1';
        const allowedRoles = $(this).data('roles');
        
        $('#menu_id').val(menuId);
        $('#menu_date').val(menuDate);
        $('#menu_items').val(menuItems);
        $('#is_special').prop('checked', isSpecial);
        
        // Reset all role checkboxes
        $('.allowed-roles-cb').prop('checked', false);
        
        // Set the appropriate role checkboxes if roles are defined
        if (allowedRoles) {
            try {
                const rolesArray = JSON.parse(allowedRoles);
                rolesArray.forEach(function(role) {
                    $('.allowed-roles-cb[value="' + role + '"]').prop('checked', true);
                });
            } catch (e) {
                console.error('Error parsing roles JSON:', e);
            }
        }
        
        // Update preview
        updatePreview();
        
        $('#dmm-cancel-edit').show();
        $('html, body').animate({
            scrollTop: $('#dmm-menu-form').offset().top - 100
        }, 500);
    });
    
    // Handle cancel edit button click
    $('#dmm-cancel-edit').on('click', function() {
        $('#menu_id').val(0);
        $('#menu_date').val('');
        $('#menu_items').val('');
        $('#is_special').prop('checked', false);
        $(this).hide();
    });
    
    // Handle delete button click
    $('.dmm-delete-menu').on('click', function() {
        const menuId = $(this).data('id');
        const menuDate = $(this).data('date');
        
        if (confirm('<?php _e('Are you sure you want to delete the menu for', 'daily-menu-manager'); ?> ' + menuDate + '?')) {
            const formData = {
                'action': 'dmm_delete_menu',
                'nonce': dmm_ajax.nonce,
                'menu_id': menuId
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
