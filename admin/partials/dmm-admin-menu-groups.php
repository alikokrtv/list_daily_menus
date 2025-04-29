<?php
/**
 * Admin template for managing menu groups
 *
 * @since      1.0.0
 * @package    Daily_Menu_Manager
 */

// Prevent direct access
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap">
    <h1><?php _e('Menu Groups', 'daily-menu-manager'); ?></h1>
    <p><?php _e('Create and manage menu groups. Each group can contain multiple daily menu entries.', 'daily-menu-manager'); ?></p>
    
    <div class="dmm-admin-container">
        <div class="dmm-group-form-container">
            <h2><?php _e('Add New Menu Group', 'daily-menu-manager'); ?></h2>
            <form id="dmm-menu-group-form" class="dmm-admin-form">
                <?php wp_nonce_field('dmm_nonce', 'dmm_nonce'); ?>
                <input type="hidden" name="group_id" id="group_id" value="0">
                
                <div class="dmm-form-field">
                    <label for="group_name"><?php _e('Group Name', 'daily-menu-manager'); ?>:</label>
                    <input type="text" id="group_name" name="group_name" required placeholder="<?php _e('e.g. May 2025 Menu', 'daily-menu-manager'); ?>">
                </div>
                
                <div class="dmm-form-field">
                    <label for="group_description"><?php _e('Description', 'daily-menu-manager'); ?>:</label>
                    <textarea id="group_description" name="group_description" rows="3" placeholder="<?php _e('Optional description for this menu group', 'daily-menu-manager'); ?>"></textarea>
                </div>
                
                <div class="dmm-form-row">
                    <div class="dmm-form-field">
                        <label for="start_date"><?php _e('Start Date', 'daily-menu-manager'); ?>:</label>
                        <input type="date" id="start_date" name="start_date" required>
                    </div>
                    
                    <div class="dmm-form-field">
                        <label for="end_date"><?php _e('End Date', 'daily-menu-manager'); ?>:</label>
                        <input type="date" id="end_date" name="end_date" required>
                    </div>
                </div>
                
                <div class="dmm-form-field">
                    <label>
                        <input type="checkbox" id="is_active" name="is_active" checked>
                        <?php _e('Active', 'daily-menu-manager'); ?>
                    </label>
                </div>
                
                <div class="dmm-form-submit">
                    <button type="submit" class="button button-primary"><?php _e('Save Menu Group', 'daily-menu-manager'); ?></button>
                    <button type="button" id="dmm-reset-form" class="button"><?php _e('Reset', 'daily-menu-manager'); ?></button>
                </div>
            </form>
        </div>
        
        <div class="dmm-group-list-container">
            <h2><?php _e('Existing Menu Groups', 'daily-menu-manager'); ?></h2>
            
            <?php if (empty($menu_groups)): ?>
                <p><?php _e('No menu groups found. Create your first menu group using the form.', 'daily-menu-manager'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped dmm-table">
                    <thead>
                        <tr>
                            <th><?php _e('Name', 'daily-menu-manager'); ?></th>
                            <th><?php _e('Date Range', 'daily-menu-manager'); ?></th>
                            <th><?php _e('Status', 'daily-menu-manager'); ?></th>
                            <th><?php _e('Actions', 'daily-menu-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($menu_groups as $group): ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($group->name); ?></strong>
                                    <?php if (!empty($group->description)): ?>
                                        <div class="dmm-group-description"><?php echo esc_html($group->description); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $start = date_i18n(get_option('date_format'), strtotime($group->start_date));
                                    $end = date_i18n(get_option('date_format'), strtotime($group->end_date));
                                    echo esc_html($start) . ' - ' . esc_html($end);
                                    ?>
                                </td>
                                <td>
                                    <?php echo $group->is_active ? '<span class="dmm-status-active">' . __('Active', 'daily-menu-manager') . '</span>' : '<span class="dmm-status-inactive">' . __('Inactive', 'daily-menu-manager') . '</span>'; ?>
                                </td>
                                <td>
                                    <div class="dmm-actions">
                                        <a href="<?php echo admin_url('admin.php?page=dmm-daily-entries&group_id=' . $group->id); ?>" class="button"><?php _e('Manage Entries', 'daily-menu-manager'); ?></a>
                                        <button class="button dmm-edit-group" data-id="<?php echo $group->id; ?>">
                                            <?php _e('Edit', 'daily-menu-manager'); ?>
                                        </button>
                                        <button class="button dmm-delete-group" data-id="<?php echo $group->id; ?>">
                                            <?php _e('Delete', 'daily-menu-manager'); ?>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Date picker initialization
        $('#start_date, #end_date').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });
        
        // Form submission handler
        $('#dmm-menu-group-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = $(this).serialize();
            formData += '&action=dmm_save_menu_group';
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        window.location.reload();
                    } else {
                        alert(response.message || 'An error occurred');
                    }
                },
                error: function() {
                    alert('Server error occurred');
                }
            });
        });
        
        // Edit group button handler
        $('.dmm-edit-group').on('click', function() {
            var groupId = $(this).data('id');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'dmm_get_menu_group',
                    nonce: $('#dmm_nonce').val(),
                    group_id: groupId
                },
                success: function(response) {
                    if (response.success) {
                        var group = response.data;
                        $('#group_id').val(group.id);
                        $('#group_name').val(group.name);
                        $('#group_description').val(group.description);
                        $('#start_date').val(group.start_date);
                        $('#end_date').val(group.end_date);
                        $('#is_active').prop('checked', group.is_active == 1);
                        
                        $('html, body').animate({
                            scrollTop: $('#dmm-menu-group-form').offset().top - 50
                        }, 500);
                    } else {
                        alert(response.message || 'Failed to load group data');
                    }
                },
                error: function() {
                    alert('Server error occurred');
                }
            });
        });
        
        // Delete group button handler
        $('.dmm-delete-group').on('click', function() {
            if (!confirm('Are you sure you want to delete this menu group? All associated daily entries will also be deleted.')) {
                return;
            }
            
            var groupId = $(this).data('id');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'dmm_delete_menu_group',
                    nonce: $('#dmm_nonce').val(),
                    group_id: groupId
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        window.location.reload();
                    } else {
                        alert(response.message || 'Failed to delete menu group');
                    }
                },
                error: function() {
                    alert('Server error occurred');
                }
            });
        });
        
        // Reset form button handler
        $('#dmm-reset-form').on('click', function() {
            $('#group_id').val(0);
            $('#dmm-menu-group-form')[0].reset();
            $('#is_active').prop('checked', true);
        });
    });
</script>
