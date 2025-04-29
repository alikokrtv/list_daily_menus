<?php
/**
 * Admin template for managing daily menu entries
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
    <h1><?php _e('Daily Menu Entries', 'daily-menu-manager'); ?></h1>
    <p><?php _e('Manage daily menu entries within a menu group.', 'daily-menu-manager'); ?></p>
    
    <div class="dmm-admin-container">
        <div class="dmm-group-selector">
            <form method="get">
                <input type="hidden" name="page" value="dmm-daily-entries">
                
                <div class="dmm-form-field">
                    <label for="group_id"><?php _e('Select Menu Group', 'daily-menu-manager'); ?>:</label>
                    <select id="group_id" name="group_id" class="dmm-select">
                        <option value=""><?php _e('-- Select a Menu Group --', 'daily-menu-manager'); ?></option>
                        <?php foreach ($menu_groups as $group): ?>
                            <option value="<?php echo $group->id; ?>" <?php selected($selected_group_id, $group->id); ?>>
                                <?php echo esc_html($group->name); ?> 
                                (<?php echo date_i18n(get_option('date_format'), strtotime($group->start_date)); ?> - 
                                <?php echo date_i18n(get_option('date_format'), strtotime($group->end_date)); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="button"><?php _e('Load Entries', 'daily-menu-manager'); ?></button>
                </div>
            </form>
        </div>
        
        <?php if ($selected_group_id > 0): ?>
            <div class="dmm-entries-management">
                <div class="dmm-entries-header">
                    <h2>
                        <?php 
                        $group_name = '';
                        foreach ($menu_groups as $group) {
                            if ($group->id == $selected_group_id) {
                                $group_name = $group->name;
                                break;
                            }
                        }
                        printf(__('Entries for: %s', 'daily-menu-manager'), esc_html($group_name)); 
                        ?>
                    </h2>
                    
                    <div class="dmm-header-actions">
                        <button id="dmm-generate-entries" class="button button-primary" data-group-id="<?php echo $selected_group_id; ?>">
                            <?php _e('Generate Missing Entries', 'daily-menu-manager'); ?>
                        </button>
                    </div>
                </div>
                
                <?php if (empty($daily_entries)): ?>
                    <div class="dmm-no-entries">
                        <p><?php _e('No entries found for this menu group. Click "Generate Missing Entries" to create entries for each day in the group\'s date range.', 'daily-menu-manager'); ?></p>
                    </div>
                <?php else: ?>
                    <table class="wp-list-table widefat fixed striped dmm-table">
                        <thead>
                            <tr>
                                <th width="15%"><?php _e('Date', 'daily-menu-manager'); ?></th>
                                <th width="65%"><?php _e('Menu Items', 'daily-menu-manager'); ?></th>
                                <th width="10%"><?php _e('Status', 'daily-menu-manager'); ?></th>
                                <th width="10%"><?php _e('Actions', 'daily-menu-manager'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($daily_entries as $entry): ?>
                                <tr>
                                    <td>
                                        <?php echo date_i18n(get_option('date_format'), strtotime($entry->menu_date)); ?>
                                    </td>
                                    <td>
                                        <div class="dmm-menu-preview">
                                            <?php 
                                            if (!empty($entry->menu_items)) {
                                                $preview = substr(strip_tags($entry->menu_items), 0, 200);
                                                echo esc_html($preview);
                                                if (strlen(strip_tags($entry->menu_items)) > 200) {
                                                    echo '...';
                                                }
                                            } else {
                                                echo '<em>' . __('No menu items added yet', 'daily-menu-manager') . '</em>';
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo $entry->is_active ? 
                                            '<span class="dmm-status-active">' . __('Active', 'daily-menu-manager') . '</span>' : 
                                            '<span class="dmm-status-inactive">' . __('Inactive', 'daily-menu-manager') . '</span>'; 
                                        ?>
                                    </td>
                                    <td>
                                        <div class="dmm-actions">
                                            <button class="button dmm-edit-entry" data-id="<?php echo $entry->id; ?>">
                                                <?php _e('Edit', 'daily-menu-manager'); ?>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            
            <!-- Edit Entry Modal -->
            <div id="dmm-edit-entry-modal" class="dmm-modal" style="display:none;">
                <div class="dmm-modal-content">
                    <span class="dmm-modal-close">&times;</span>
                    <h2><?php _e('Edit Menu Entry', 'daily-menu-manager'); ?></h2>
                    
                    <form id="dmm-edit-entry-form">
                        <?php wp_nonce_field('dmm_nonce', 'dmm_entry_nonce'); ?>
                        <input type="hidden" id="entry_id" name="entry_id" value="">
                        
                        <div class="dmm-form-field">
                            <label for="entry_date"><?php _e('Date', 'daily-menu-manager'); ?>:</label>
                            <input type="text" id="entry_date" disabled>
                        </div>
                        
                        <div class="dmm-form-field">
                            <label for="menu_items"><?php _e('Menu Items', 'daily-menu-manager'); ?>:</label>
                            <?php 
                            wp_editor('', 'menu_items', array(
                                'textarea_name' => 'menu_items',
                                'textarea_rows' => 10,
                                'media_buttons' => false,
                                'teeny' => true,
                            )); 
                            ?>
                        </div>
                        
                        <div class="dmm-form-field">
                            <label>
                                <input type="checkbox" id="entry_is_active" name="is_active" checked>
                                <?php _e('Active', 'daily-menu-manager'); ?>
                            </label>
                        </div>
                        
                        <div class="dmm-form-submit">
                            <button type="submit" class="button button-primary"><?php _e('Save Entry', 'daily-menu-manager'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Generate entries button handler
        $('#dmm-generate-entries').on('click', function() {
            var groupId = $(this).data('group-id');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'dmm_generate_entries',
                    nonce: $('#dmm_nonce').val(),
                    group_id: groupId
                },
                beforeSend: function() {
                    $(this).prop('disabled', true).text('Processing...');
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        window.location.reload();
                    } else {
                        alert(response.message || 'Failed to generate entries');
                    }
                },
                error: function() {
                    alert('Server error occurred');
                },
                complete: function() {
                    $(this).prop('disabled', false).text('Generate Missing Entries');
                }
            });
        });
        
        // Edit entry button handler
        $('.dmm-edit-entry').on('click', function() {
            var entryId = $(this).data('id');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'dmm_get_menu_entry',
                    nonce: $('#dmm_entry_nonce').val(),
                    entry_id: entryId
                },
                success: function(response) {
                    if (response.success) {
                        var entry = response.data;
                        $('#entry_id').val(entry.id);
                        $('#entry_date').val(entry.formatted_date);
                        
                        // Set content in the wp_editor
                        if (typeof tinymce !== 'undefined' && tinymce.get('menu_items')) {
                            tinymce.get('menu_items').setContent(entry.menu_items);
                        } else {
                            $('#menu_items').val(entry.menu_items);
                        }
                        
                        $('#entry_is_active').prop('checked', entry.is_active == 1);
                        
                        // Show the modal
                        $('#dmm-edit-entry-modal').show();
                    } else {
                        alert(response.message || 'Failed to load entry data');
                    }
                },
                error: function() {
                    alert('Server error occurred');
                }
            });
        });
        
        // Close modal when clicking the X
        $('.dmm-modal-close').on('click', function() {
            $('#dmm-edit-entry-modal').hide();
        });
        
        // Close modal when clicking outside the modal content
        $(window).on('click', function(e) {
            if ($(e.target).is('.dmm-modal')) {
                $('.dmm-modal').hide();
            }
        });
        
        // Handle entry form submission
        $('#dmm-edit-entry-form').on('submit', function(e) {
            e.preventDefault();
            
            // Make sure to get content from tinymce if it's active
            if (typeof tinymce !== 'undefined' && tinymce.get('menu_items')) {
                var menuItems = tinymce.get('menu_items').getContent();
            } else {
                var menuItems = $('#menu_items').val();
            }
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'dmm_save_menu_entry',
                    nonce: $('#dmm_entry_nonce').val(),
                    entry_id: $('#entry_id').val(),
                    menu_items: menuItems,
                    is_active: $('#entry_is_active').is(':checked') ? 1 : 0
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#dmm-edit-entry-modal').hide();
                        window.location.reload();
                    } else {
                        alert(response.message || 'Failed to save entry');
                    }
                },
                error: function() {
                    alert('Server error occurred');
                }
            });
        });
    });
</script>
