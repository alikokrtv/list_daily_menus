<?php
/**
 * Menu Groups management page template
 *
 * @link       mailto:alikokrtv@gmail.com
 * @since      1.0.0
 *
 * @package    Daily_Menu_Manager
 * @subpackage Daily_Menu_Manager/admin/partials
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Get menu groups
global $wpdb;
$menu_groups_table = $wpdb->prefix . 'dmm_menu_groups';
$menu_groups = $wpdb->get_results("SELECT * FROM $menu_groups_table ORDER BY period_start DESC");

// Handle form submission
if (isset($_POST['submit_menu_group'])) {
    // Verify nonce
    if (!isset($_POST['dmm_menu_group_nonce']) || !wp_verify_nonce($_POST['dmm_menu_group_nonce'], 'dmm_save_menu_group')) {
        wp_die('Security check failed');
    }
    
    $group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;
    $title = sanitize_text_field($_POST['title']);
    $location = sanitize_text_field($_POST['location']);
    $period_start = sanitize_text_field($_POST['period_start']);
    $period_end = sanitize_text_field($_POST['period_end']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $bg_image = sanitize_text_field($_POST['bg_image']);
    $allowed_roles = isset($_POST['allowed_roles']) ? implode(',', array_map('sanitize_text_field', $_POST['allowed_roles'])) : '';
    
    $data = array(
        'title' => $title,
        'location' => $location,
        'period_start' => $period_start,
        'period_end' => $period_end,
        'is_active' => $is_active,
        'bg_image' => $bg_image,
        'allowed_roles' => $allowed_roles
    );
    
    if ($group_id > 0) {
        // Update existing group
        $wpdb->update(
            $menu_groups_table,
            $data,
            array('id' => $group_id)
        );
        $message = 'Menu group updated successfully.';
    } else {
        // Insert new group
        $wpdb->insert(
            $menu_groups_table,
            $data
        );
        $group_id = $wpdb->insert_id;
        $message = 'New menu group created successfully.';
    }
    
    // Redirect to prevent form resubmission
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($message) . '</p></div>';
    $menu_groups = $wpdb->get_results("SELECT * FROM $menu_groups_table ORDER BY period_start DESC");
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $group_id = intval($_GET['id']);
    
    // Delete the menu group
    $wpdb->delete(
        $menu_groups_table,
        array('id' => $group_id)
    );
    
    // Also delete all associated daily menu entries
    $menus_table = $wpdb->prefix . 'dmm_menus';
    $wpdb->delete(
        $menus_table,
        array('group_id' => $group_id)
    );
    
    echo '<div class="notice notice-success is-dismissible"><p>Menu group deleted successfully.</p></div>';
    $menu_groups = $wpdb->get_results("SELECT * FROM $menu_groups_table ORDER BY period_start DESC");
}

// Get edit data if we're editing
$edit_id = isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id']) ? intval($_GET['id']) : 0;
$edit_data = array(
    'id' => 0,
    'title' => '',
    'location' => '',
    'period_start' => date('Y-m-d'),
    'period_end' => date('Y-m-d', strtotime('+30 days')),
    'is_active' => 1,
    'bg_image' => '',
    'allowed_roles' => ''
);

if ($edit_id > 0) {
    $edit_group = $wpdb->get_row($wpdb->prepare("SELECT * FROM $menu_groups_table WHERE id = %d", $edit_id));
    if ($edit_group) {
        $edit_data = array(
            'id' => $edit_group->id,
            'title' => $edit_group->title,
            'location' => $edit_group->location,
            'period_start' => $edit_group->period_start,
            'period_end' => $edit_group->period_end,
            'is_active' => $edit_group->is_active,
            'bg_image' => $edit_group->bg_image,
            'allowed_roles' => $edit_group->allowed_roles
        );
    }
}

// Get WordPress roles
global $wp_roles;
$all_roles = $wp_roles->roles;
$editable_roles = apply_filters('editable_roles', $all_roles);
$allowed_roles_array = !empty($edit_data['allowed_roles']) ? explode(',', $edit_data['allowed_roles']) : array();
?>

<div class="wrap dmm-admin">
    <h1 class="wp-heading-inline"><?php echo $edit_id ? 'Edit Menu Group' : 'Add New Menu Group'; ?></h1>
    <a href="<?php echo admin_url('admin.php?page=dmm-menu-groups'); ?>" class="page-title-action">Back to List</a>
    <hr class="wp-header-end">
    
    <div class="dmm-form-container">
        <form method="post" id="menu-group-form">
            <?php wp_nonce_field('dmm_save_menu_group', 'dmm_menu_group_nonce'); ?>
            <input type="hidden" name="group_id" value="<?php echo esc_attr($edit_data['id']); ?>">
            
            <div class="dmm-form-field">
                <label for="title">Menu Group Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" value="<?php echo esc_attr($edit_data['title']); ?>" required>
                <p class="description">Example: "May 2025 Menu" or "Summer 2025 Menu"</p>
            </div>
            
            <div class="dmm-form-field">
                <label for="location">Location <span class="required">*</span></label>
                <input type="text" id="location" name="location" value="<?php echo esc_attr($edit_data['location']); ?>" required>
                <p class="description">Example: "Main Restaurant" or "Cafeteria B"</p>
            </div>
            
            <div class="dmm-form-field">
                <label for="period_start">Start Date <span class="required">*</span></label>
                <input type="date" id="period_start" name="period_start" value="<?php echo esc_attr($edit_data['period_start']); ?>" required>
            </div>
            
            <div class="dmm-form-field">
                <label for="period_end">End Date <span class="required">*</span></label>
                <input type="date" id="period_end" name="period_end" value="<?php echo esc_attr($edit_data['period_end']); ?>" required>
            </div>
            
            <div class="dmm-form-field">
                <label for="is_active">Active</label>
                <input type="checkbox" id="is_active" name="is_active" value="1" <?php checked($edit_data['is_active'], 1); ?>>
                <p class="description">Only active menu groups will be displayed on the frontend.</p>
            </div>
            
            <div class="dmm-form-field">
                <label for="bg_image">Background Image URL</label>
                <div class="dmm-media-field">
                    <input type="text" id="bg_image" name="bg_image" value="<?php echo esc_attr($edit_data['bg_image']); ?>">
                    <button type="button" class="button dmm-upload-btn">Select Image</button>
                </div>
                <div class="dmm-image-preview">
                    <?php if (!empty($edit_data['bg_image'])): ?>
                        <img src="<?php echo esc_url($edit_data['bg_image']); ?>" alt="Background Preview">
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="dmm-form-field">
                <label>Access Control</label>
                <div class="dmm-roles-container">
                    <?php foreach ($editable_roles as $role_key => $role): ?>
                        <label class="dmm-role-checkbox">
                            <input type="checkbox" name="allowed_roles[]" value="<?php echo esc_attr($role_key); ?>" <?php checked(in_array($role_key, $allowed_roles_array)); ?>>
                            <?php echo esc_html(translate_user_role($role['name'])); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p class="description">Select which user roles can view this menu group. Leave empty to allow all users.</p>
            </div>
            
            <div class="dmm-form-actions">
                <button type="submit" name="submit_menu_group" class="button button-primary">
                    <?php echo $edit_id ? 'Update Menu Group' : 'Create Menu Group'; ?>
                </button>
            </div>
        </form>
    </div>
    
    <?php if (!$edit_id): ?>
    <div class="dmm-list-container">
        <h2>Menu Groups</h2>
        
        <?php if (empty($menu_groups)): ?>
            <p>No menu groups found. Create your first menu group above.</p>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Period</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menu_groups as $group): ?>
                        <tr>
                            <td><?php echo esc_html($group->title); ?></td>
                            <td><?php echo esc_html($group->location); ?></td>
                            <td>
                                <?php 
                                echo esc_html(date('M d, Y', strtotime($group->period_start))); 
                                echo ' - '; 
                                echo esc_html(date('M d, Y', strtotime($group->period_end))); 
                                ?>
                            </td>
                            <td><?php echo $group->is_active ? '<span class="dmm-status active">Active</span>' : '<span class="dmm-status inactive">Inactive</span>'; ?></td>
                            <td class="actions">
                                <a href="<?php echo admin_url('admin.php?page=dmm-daily-entries&group_id=' . $group->id); ?>" class="button">Manage Daily Entries</a>
                                <a href="<?php echo admin_url('admin.php?page=dmm-menu-groups&action=edit&id=' . $group->id); ?>" class="button">Edit</a>
                                <a href="<?php echo admin_url('admin.php?page=dmm-menu-groups&action=delete&id=' . $group->id); ?>" class="button delete" onclick="return confirm('Are you sure you want to delete this menu group? All daily entries within this group will also be deleted.');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    // Media uploader for background image
    $('.dmm-upload-btn').click(function(e) {
        e.preventDefault();
        
        var button = $(this);
        var imageField = button.siblings('input');
        var previewContainer = button.closest('.dmm-form-field').find('.dmm-image-preview');
        
        var frame = wp.media({
            title: 'Select or Upload Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });
        
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            imageField.val(attachment.url);
            
            previewContainer.html('<img src="' + attachment.url + '" alt="Background Preview">');
        });
        
        frame.open();
    });
    
    // Date validation
    $('#menu-group-form').submit(function() {
        var startDate = new Date($('#period_start').val());
        var endDate = new Date($('#period_end').val());
        
        if (endDate < startDate) {
            alert('End date cannot be earlier than start date');
            return false;
        }
        
        return true;
    });
});
</script>
