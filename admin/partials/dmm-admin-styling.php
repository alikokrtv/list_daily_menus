<?php
/**
 * Admin template for styling options
 *
 * @link       https://alikokdeneysel.online
 * @since      1.0.0
 */

// Prevent direct access
if (!defined('WPINC')) {
    die;
}

global $wpdb;
$table_name = $wpdb->prefix . 'dmm_styles';

// Get all style presets
$styles = $wpdb->get_results("SELECT * FROM $table_name ORDER BY is_default DESC, style_name ASC");

// Get default style for the form
$default_style = $wpdb->get_row("SELECT * FROM $table_name WHERE is_default = 1");
if (!$default_style && !empty($styles)) {
    $default_style = $styles[0];
}

// Initialize an empty style if none exists
if (!$default_style) {
    $default_style = (object) array(
        'id' => 0,
        'style_name' => '',
        'font_family' => 'Arial, sans-serif',
        'font_size' => '16px',
        'text_color' => '#FFFFFF',
        'background_color' => 'rgba(0, 0, 0, 0.2)',
        'background_image' => '',
        'container_width' => '100%',
        'border_style' => 'none',
        'is_default' => 1
    );
}
?>

<div class="wrap dmm-admin">
    <h1><?php _e('Daily Menu Manager', 'daily-menu-manager'); ?></h1>
    
    <div class="dmm-admin-header">
        <h2><?php _e('Styling Options', 'daily-menu-manager'); ?></h2>
    </div>
    
    <div class="dmm-admin-content">
        <div class="dmm-style-form-container">
            <h3><?php _e('Create/Edit Style', 'daily-menu-manager'); ?></h3>
            <form id="dmm-style-form">
                <input type="hidden" name="style_id" id="style_id" value="0">
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="style_name"><?php _e('Style Name', 'daily-menu-manager'); ?></label></th>
                        <td>
                            <input type="text" id="style_name" name="style_name" class="regular-text" value="" required>
                            <p class="description"><?php _e('Enter a name for this style preset', 'daily-menu-manager'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="font_family"><?php _e('Font Family', 'daily-menu-manager'); ?></label></th>
                        <td>
                            <select id="font_family" name="font_family">
                                <option value="Arial, sans-serif"><?php _e('Arial', 'daily-menu-manager'); ?></option>
                                <option value="'Times New Roman', serif"><?php _e('Times New Roman', 'daily-menu-manager'); ?></option>
                                <option value="'Courier New', monospace"><?php _e('Courier New', 'daily-menu-manager'); ?></option>
                                <option value="Georgia, serif"><?php _e('Georgia', 'daily-menu-manager'); ?></option>
                                <option value="Verdana, sans-serif"><?php _e('Verdana', 'daily-menu-manager'); ?></option>
                                <option value="'Trebuchet MS', sans-serif"><?php _e('Trebuchet MS', 'daily-menu-manager'); ?></option>
                                <option value="Impact, sans-serif"><?php _e('Impact', 'daily-menu-manager'); ?></option>
                                <option value="'Open Sans', sans-serif"><?php _e('Open Sans', 'daily-menu-manager'); ?></option>
                                <option value="'Roboto', sans-serif"><?php _e('Roboto', 'daily-menu-manager'); ?></option>
                                <option value="'Lato', sans-serif"><?php _e('Lato', 'daily-menu-manager'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="font_size"><?php _e('Font Size', 'daily-menu-manager'); ?></label></th>
                        <td>
                            <input type="text" id="font_size" name="font_size" class="small-text" value="">
                            <p class="description"><?php _e('Enter font size with units (e.g., 16px, 1.2em)', 'daily-menu-manager'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="text_color"><?php _e('Text Color', 'daily-menu-manager'); ?></label></th>
                        <td>
                            <input type="text" id="text_color" name="text_color" class="dmm-color-picker" value="">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="background_color"><?php _e('Background Color', 'daily-menu-manager'); ?></label></th>
                        <td>
                            <input type="text" id="background_color" name="background_color" class="dmm-color-picker" value="">
                            <p class="description"><?php _e('You can use rgba() values for transparency', 'daily-menu-manager'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="background_image"><?php _e('Background Image', 'daily-menu-manager'); ?></label></th>
                        <td>
                            <input type="text" id="background_image" name="background_image" class="regular-text" value="">
                            <button type="button" class="button button-secondary" id="dmm-upload-image"><?php _e('Upload Image', 'daily-menu-manager'); ?></button>
                            <div id="dmm-image-preview" style="margin-top:10px; max-width:200px;"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="container_width"><?php _e('Container Width', 'daily-menu-manager'); ?></label></th>
                        <td>
                            <input type="text" id="container_width" name="container_width" class="small-text" value="">
                            <p class="description"><?php _e('Enter width with units (e.g., 100%, 500px)', 'daily-menu-manager'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="border_style"><?php _e('Border Style', 'daily-menu-manager'); ?></label></th>
                        <td>
                            <select id="border_style" name="border_style">
                                <option value="none"><?php _e('None', 'daily-menu-manager'); ?></option>
                                <option value="1px solid #000"><?php _e('Thin Black', 'daily-menu-manager'); ?></option>
                                <option value="2px solid #000"><?php _e('Medium Black', 'daily-menu-manager'); ?></option>
                                <option value="1px solid #888"><?php _e('Thin Gray', 'daily-menu-manager'); ?></option>
                                <option value="2px solid #888"><?php _e('Medium Gray', 'daily-menu-manager'); ?></option>
                                <option value="1px dashed #000"><?php _e('Dashed Black', 'daily-menu-manager'); ?></option>
                                <option value="1px dotted #000"><?php _e('Dotted Black', 'daily-menu-manager'); ?></option>
                                <option value="2px double #000"><?php _e('Double Black', 'daily-menu-manager'); ?></option>
                                <option value="3px ridge #888"><?php _e('Ridge Gray', 'daily-menu-manager'); ?></option>
                                <option value="3px groove #888"><?php _e('Groove Gray', 'daily-menu-manager'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Default Style', 'daily-menu-manager'); ?></th>
                        <td>
                            <label for="is_default">
                                <input type="checkbox" id="is_default" name="is_default">
                                <?php _e('Set as default style', 'daily-menu-manager'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
                
                <div class="dmm-style-preview">
                    <h3><?php _e('Preview', 'daily-menu-manager'); ?></h3>
                    <div id="dmm-preview-container" style="position: relative; width: 100%; height: 200px; border-radius: 12px; overflow: hidden; margin-bottom: 20px;">
                        <div id="dmm-preview-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;"></div>
                        <div id="dmm-preview-text" style="position: relative; color: white; font-weight: bold; text-align: center; z-index: 2; padding: 20px; display: flex; align-items: center; justify-content: center; height: 100%;">
                            <?php _e('Sample Menu: Soup, Main Course, Side Dish, Dessert', 'daily-menu-manager'); ?>
                        </div>
                    </div>
                </div>
                
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Save Style', 'daily-menu-manager'); ?></button>
                    <button type="button" id="dmm-cancel-edit-style" class="button" style="display:none;"><?php _e('Cancel', 'daily-menu-manager'); ?></button>
                </p>
            </form>
        </div>
        
        <div class="dmm-style-list-container">
            <h3><?php _e('Saved Styles', 'daily-menu-manager'); ?></h3>
            
            <?php if (empty($styles)) : ?>
                <p><?php _e('No styles found. Create your first style using the form.', 'daily-menu-manager'); ?></p>
            <?php else : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th scope="col"><?php _e('Style Name', 'daily-menu-manager'); ?></th>
                            <th scope="col"><?php _e('Default', 'daily-menu-manager'); ?></th>
                            <th scope="col"><?php _e('Actions', 'daily-menu-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($styles as $style) : ?>
                            <tr>
                                <td><?php echo esc_html($style->style_name); ?></td>
                                <td><?php echo $style->is_default ? '✓' : ''; ?></td>
                                <td>
                                    <button type="button" class="button dmm-edit-style" 
                                        data-id="<?php echo esc_attr($style->id); ?>"
                                        data-name="<?php echo esc_attr($style->style_name); ?>"
                                        data-font-family="<?php echo esc_attr($style->font_family); ?>"
                                        data-font-size="<?php echo esc_attr($style->font_size); ?>"
                                        data-text-color="<?php echo esc_attr($style->text_color); ?>"
                                        data-bg-color="<?php echo esc_attr($style->background_color); ?>"
                                        data-bg-image="<?php echo esc_attr($style->background_image); ?>"
                                        data-width="<?php echo esc_attr($style->container_width); ?>"
                                        data-border="<?php echo esc_attr($style->border_style); ?>"
                                        data-default="<?php echo esc_attr($style->is_default); ?>">
                                        <?php _e('Edit', 'daily-menu-manager'); ?>
                                    </button>
                                    <?php if (!$style->is_default) : ?>
                                        <button type="button" class="button dmm-delete-style" 
                                            data-id="<?php echo esc_attr($style->id); ?>"
                                            data-name="<?php echo esc_attr($style->style_name); ?>">
                                            <?php _e('Delete', 'daily-menu-manager'); ?>
                                        </button>
                                    <?php endif; ?>
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
    // Initialize color picker
    $('.dmm-color-picker').wpColorPicker({
        change: updatePreview
    });
    
    // Initialize with the default values
    $('#style_name').val('<?php echo esc_js($default_style->style_name); ?>');
    $('#font_family').val('<?php echo esc_js($default_style->font_family); ?>');
    $('#font_size').val('<?php echo esc_js($default_style->font_size); ?>');
    $('#text_color').val('<?php echo esc_js($default_style->text_color); ?>');
    $('#background_color').val('<?php echo esc_js($default_style->background_color); ?>');
    $('#background_image').val('<?php echo esc_js($default_style->background_image); ?>');
    $('#container_width').val('<?php echo esc_js($default_style->container_width); ?>');
    $('#border_style').val('<?php echo esc_js($default_style->border_style); ?>');
    $('#is_default').prop('checked', <?php echo $default_style->is_default ? 'true' : 'false'; ?>);
    
    updatePreview();
    updateImagePreview();
    
    // Update preview when inputs change
    $('#font_family, #font_size, #container_width, #border_style').on('change', updatePreview);
    $('#background_image').on('change', function() {
        updatePreview();
        updateImagePreview();
    });
    
    // Handle image upload
    $('#dmm-upload-image').on('click', function(e) {
        e.preventDefault();
        
        const frame = wp.media({
            title: '<?php _e('Select or Upload Background Image', 'daily-menu-manager'); ?>',
            button: {
                text: '<?php _e('Use this image', 'daily-menu-manager'); ?>'
            },
            multiple: false
        });
        
        frame.on('select', function() {
            const attachment = frame.state().get('selection').first().toJSON();
            $('#background_image').val(attachment.url);
            updatePreview();
            updateImagePreview();
        });
        
        frame.open();
    });
    
    // Function to update the preview
    function updatePreview() {
        const fontFamily = $('#font_family').val();
        const fontSize = $('#font_size').val();
        const textColor = $('#text_color').val();
        const bgColor = $('#background_color').val();
        const bgImage = $('#background_image').val();
        const containerWidth = $('#container_width').val();
        const borderStyle = $('#border_style').val();
        
        // Update preview container
        $('#dmm-preview-container').css({
            'width': containerWidth,
            'border': borderStyle,
            'background-image': bgImage ? 'url(' + bgImage + ')' : 'none',
            'background-size': 'cover',
            'background-position': 'center'
        });
        
        // Update overlay
        $('#dmm-preview-overlay').css({
            'background-color': bgColor
        });
        
        // Update text
        $('#dmm-preview-text').css({
            'font-family': fontFamily,
            'font-size': fontSize,
            'color': textColor
        });
    }
    
    // Function to update image preview
    function updateImagePreview() {
        const imageUrl = $('#background_image').val();
        if (imageUrl) {
            $('#dmm-image-preview').html('<img src="' + imageUrl + '" style="max-width:100%;">');
        } else {
            $('#dmm-image-preview').empty();
        }
    }
    
    // Handle form submission
    $('#dmm-style-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            'action': 'dmm_save_style',
            'nonce': dmm_ajax.nonce,
            'style_id': $('#style_id').val(),
            'style_name': $('#style_name').val(),
            'font_family': $('#font_family').val(),
            'font_size': $('#font_size').val(),
            'text_color': $('#text_color').val(),
            'background_color': $('#background_color').val(),
            'background_image': $('#background_image').val(),
            'container_width': $('#container_width').val(),
            'border_style': $('#border_style').val(),
            'is_default': $('#is_default').is(':checked') ? 1 : 0
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
    $('.dmm-edit-style').on('click', function() {
        const styleId = $(this).data('id');
        const styleName = $(this).data('name');
        const fontFamily = $(this).data('font-family');
        const fontSize = $(this).data('font-size');
        const textColor = $(this).data('text-color');
        const bgColor = $(this).data('bg-color');
        const bgImage = $(this).data('bg-image');
        const containerWidth = $(this).data('width');
        const borderStyle = $(this).data('border');
        const isDefault = $(this).data('default') == '1';
        
        $('#style_id').val(styleId);
        $('#style_name').val(styleName);
        $('#font_family').val(fontFamily);
        $('#font_size').val(fontSize);
        
        // Update color pickers
        $('#text_color').val(textColor).change();
        $('#background_color').val(bgColor).change();
        
        $('#background_image').val(bgImage);
        $('#container_width').val(containerWidth);
        $('#border_style').val(borderStyle);
        $('#is_default').prop('checked', isDefault);
        
        updatePreview();
        updateImagePreview();
        
        $('#dmm-cancel-edit-style').show();
        $('html, body').animate({
            scrollTop: $('#dmm-style-form').offset().top - 100
        }, 500);
    });
    
    // Handle cancel edit button click
    $('#dmm-cancel-edit-style').on('click', function() {
        $('#style_id').val(0);
        $('#style_name').val('');
        $('#font_family').val('Arial, sans-serif');
        $('#font_size').val('16px');
        $('#text_color').val('#FFFFFF').change();
        $('#background_color').val('rgba(0, 0, 0, 0.2)').change();
        $('#background_image').val('');
        $('#container_width').val('100%');
        $('#border_style').val('none');
        $('#is_default').prop('checked', false);
        
        updatePreview();
        updateImagePreview();
        
        $(this).hide();
    });
    
    // Handle delete button click
    $('.dmm-delete-style').on('click', function() {
        const styleId = $(this).data('id');
        const styleName = $(this).data('name');
        
        if (confirm('<?php _e('Are you sure you want to delete the style', 'daily-menu-manager'); ?> "' + styleName + '"?')) {
            const formData = {
                'action': 'dmm_delete_style',
                'nonce': dmm_ajax.nonce,
                'style_id': styleId
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
