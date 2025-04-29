<?php
/**
 * Daily Menu Entries management page template
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

global $wpdb;
$menu_groups_table = $wpdb->prefix . 'dmm_menu_groups';
$menus_table = $wpdb->prefix . 'dmm_menus';

// Get the group ID from URL parameter
$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;

// If no group ID provided, show a message and list of groups
if ($group_id <= 0) {
    $menu_groups = $wpdb->get_results("SELECT * FROM $menu_groups_table ORDER BY period_start DESC");
    ?>
    <div class="wrap dmm-admin">
        <h1 class="wp-heading-inline">Daily Menu Entries</h1>
        <hr class="wp-header-end">
        
        <div class="notice notice-info">
            <p>Please select a menu group to manage its daily entries.</p>
        </div>
        
        <?php if (empty($menu_groups)): ?>
            <p>No menu groups found. <a href="<?php echo admin_url('admin.php?page=dmm-menu-groups'); ?>">Create a menu group</a> first.</p>
        <?php else: ?>
            <div class="dmm-list-container">
                <h2>Select a Menu Group</h2>
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
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return;
}

// Get the menu group information
$menu_group = $wpdb->get_row($wpdb->prepare("SELECT * FROM $menu_groups_table WHERE id = %d", $group_id));

// If group not found, show error
if (!$menu_group) {
    ?>
    <div class="wrap dmm-admin">
        <h1 class="wp-heading-inline">Daily Menu Entries</h1>
        <hr class="wp-header-end">
        
        <div class="notice notice-error">
            <p>Menu group not found. <a href="<?php echo admin_url('admin.php?page=dmm-menu-groups'); ?>">Go back to menu groups</a>.</p>
        </div>
    </div>
    <?php
    return;
}

// Handle form submission
if (isset($_POST['submit_daily_entry'])) {
    // Verify nonce
    if (!isset($_POST['dmm_daily_entry_nonce']) || !wp_verify_nonce($_POST['dmm_daily_entry_nonce'], 'dmm_save_daily_entry')) {
        wp_die('Security check failed');
    }
    
    $entry_id = isset($_POST['entry_id']) ? intval($_POST['entry_id']) : 0;
    $menu_date = sanitize_text_field($_POST['menu_date']);
    $menu_items = sanitize_textarea_field($_POST['menu_items']);
    $is_special = isset($_POST['is_special']) ? 1 : 0;
    
    $data = array(
        'group_id' => $group_id,
        'menu_date' => $menu_date,
        'menu_items' => $menu_items,
        'is_special' => $is_special
    );
    
    if ($entry_id > 0) {
        // Update existing entry
        $wpdb->update(
            $menus_table,
            $data,
            array('id' => $entry_id)
        );
        $message = 'Daily entry updated successfully.';
    } else {
        // Check if entry for this date already exists
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $menus_table WHERE group_id = %d AND menu_date = %s",
            $group_id,
            $menu_date
        ));
        
        if ($existing) {
            echo '<div class="notice notice-error is-dismissible"><p>A menu entry for this date already exists.</p></div>';
        } else {
            // Insert new entry
            $wpdb->insert(
                $menus_table,
                $data
            );
            $entry_id = $wpdb->insert_id;
            $message = 'New daily entry created successfully.';
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($message) . '</p></div>';
        }
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['entry_id'])) {
    $entry_id = intval($_GET['entry_id']);
    
    // Delete the menu entry
    $wpdb->delete(
        $menus_table,
        array('id' => $entry_id)
    );
    
    echo '<div class="notice notice-success is-dismissible"><p>Daily entry deleted successfully.</p></div>';
}

// Get edit data if we're editing
$edit_id = isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['entry_id']) ? intval($_GET['entry_id']) : 0;
$edit_data = array(
    'id' => 0,
    'menu_date' => date('Y-m-d'),
    'menu_items' => '',
    'is_special' => 0
);

if ($edit_id > 0) {
    $edit_entry = $wpdb->get_row($wpdb->prepare("SELECT * FROM $menus_table WHERE id = %d", $edit_id));
    if ($edit_entry) {
        $edit_data = array(
            'id' => $edit_entry->id,
            'menu_date' => $edit_entry->menu_date,
            'menu_items' => $edit_entry->menu_items,
            'is_special' => $edit_entry->is_special
        );
    }
}

// Get all daily entries for this group
$daily_entries = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $menus_table WHERE group_id = %d ORDER BY menu_date ASC",
    $group_id
));

// Get date range from the group
$start_date = new DateTime($menu_group->period_start);
$end_date = new DateTime($menu_group->period_end);
$interval = $start_date->diff($end_date);
$total_days = $interval->days + 1; // Include both start and end dates
?>

<div class="wrap dmm-admin">
    <h1 class="wp-heading-inline">
        Daily Entries for "<?php echo esc_html($menu_group->title); ?>"
    </h1>
    <a href="<?php echo admin_url('admin.php?page=dmm-daily-entries'); ?>" class="page-title-action">Back to Menu Groups</a>
    <hr class="wp-header-end">
    
    <div class="dmm-group-info">
        <p>
            <strong>Location:</strong> <?php echo esc_html($menu_group->location); ?><br>
            <strong>Period:</strong> <?php echo esc_html(date('M d, Y', strtotime($menu_group->period_start))); ?> - <?php echo esc_html(date('M d, Y', strtotime($menu_group->period_end))); ?><br>
            <strong>Status:</strong> <?php echo $menu_group->is_active ? 'Active' : 'Inactive'; ?><br>
            <strong>Total Days:</strong> <?php echo esc_html($total_days); ?><br>
            <strong>Entries Created:</strong> <?php echo esc_html(count($daily_entries)); ?> of <?php echo esc_html($total_days); ?>
        </p>
    </div>
    
    <div class="dmm-form-container">
        <h2><?php echo $edit_id ? 'Edit Daily Entry' : 'Add New Daily Entry'; ?></h2>
        
        <form method="post" id="daily-entry-form">
            <?php wp_nonce_field('dmm_save_daily_entry', 'dmm_daily_entry_nonce'); ?>
            <input type="hidden" name="entry_id" value="<?php echo esc_attr($edit_data['id']); ?>">
            
            <div class="dmm-form-field">
                <label for="menu_date">Date <span class="required">*</span></label>
                <input type="date" id="menu_date" name="menu_date" value="<?php echo esc_attr($edit_data['menu_date']); ?>" required
                    min="<?php echo esc_attr($menu_group->period_start); ?>" 
                    max="<?php echo esc_attr($menu_group->period_end); ?>">
                <p class="description">Select a date within the menu group period (<?php echo esc_html(date('M d, Y', strtotime($menu_group->period_start))); ?> - <?php echo esc_html(date('M d, Y', strtotime($menu_group->period_end))); ?>)</p>
            </div>
            
            <div class="dmm-form-field">
                <label for="menu_items">Menu Items <span class="required">*</span></label>
                <textarea id="menu_items" name="menu_items" rows="5" required><?php echo esc_textarea($edit_data['menu_items']); ?></textarea>
                <p class="description">Enter the menu items, one per line or separated by commas.</p>
            </div>
            
            <div class="dmm-form-field">
                <label for="is_special">Special Menu</label>
                <input type="checkbox" id="is_special" name="is_special" value="1" <?php checked($edit_data['is_special'], 1); ?>>
                <p class="description">Check if this is a special menu (will be highlighted differently).</p>
            </div>
            
            <div class="dmm-form-actions">
                <button type="submit" name="submit_daily_entry" class="button button-primary">
                    <?php echo $edit_id ? 'Update Entry' : 'Add Entry'; ?>
                </button>
                <?php if ($edit_id): ?>
                    <a href="<?php echo admin_url('admin.php?page=dmm-daily-entries&group_id=' . $group_id); ?>" class="button">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="dmm-list-container">
        <h2>Daily Entries</h2>
        
        <?php if (empty($daily_entries)): ?>
            <p>No daily entries found for this menu group. Add your first entry above.</p>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Menu Items</th>
                        <th>Special</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daily_entries as $entry): ?>
                        <tr>
                            <td><?php echo esc_html(date('l, M d, Y', strtotime($entry->menu_date))); ?></td>
                            <td><?php echo nl2br(esc_html($entry->menu_items)); ?></td>
                            <td><?php echo $entry->is_special ? 'Yes' : 'No'; ?></td>
                            <td class="actions">
                                <a href="<?php echo admin_url('admin.php?page=dmm-daily-entries&group_id=' . $group_id . '&action=edit&entry_id=' . $entry->id); ?>" class="button">Edit</a>
                                <a href="<?php echo admin_url('admin.php?page=dmm-daily-entries&group_id=' . $group_id . '&action=delete&entry_id=' . $entry->id); ?>" class="button delete" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <div class="dmm-batch-actions">
        <h2>Batch Operations</h2>
        <p>Create multiple daily entries at once:</p>
        
        <div class="dmm-batch-buttons">
            <a href="<?php echo admin_url('admin.php?page=dmm-import&group_id=' . $group_id); ?>" class="button">Import from Excel</a>
            <button id="generate-entries-btn" class="button">Generate Missing Entries</button>
        </div>
        
        <div id="generate-entries-form" style="display: none; margin-top: 15px;">
            <p>This will generate entries for all dates in the period that don't already have entries.</p>
            <label>
                <strong>Default Menu Items:</strong>
                <textarea id="default-menu-items" rows="3" placeholder="Enter default menu items..."></textarea>
            </label>
            <p class="description">These items will be used for all generated entries.</p>
            
            <button id="confirm-generate" class="button button-primary">Generate Entries</button>
            <button id="cancel-generate" class="button">Cancel</button>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Toggle generate entries form
    $('#generate-entries-btn').click(function() {
        $('#generate-entries-form').toggle();
    });
    
    $('#cancel-generate').click(function(e) {
        e.preventDefault();
        $('#generate-entries-form').hide();
    });
    
    $('#confirm-generate').click(function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to generate entries for all missing dates in this period?')) {
            return;
        }
        
        var defaultItems = $('#default-menu-items').val();
        
        // AJAX call to generate entries
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'dmm_generate_entries',
                group_id: <?php echo esc_js($group_id); ?>,
                default_items: defaultItems,
                nonce: '<?php echo wp_create_nonce('dmm_generate_entries'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('Generated ' + response.data.count + ' new entries successfully.');
                    window.location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
});
</script>
