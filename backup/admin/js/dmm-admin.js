/**
 * JavaScript for the admin area of the Daily Menu Manager plugin
 */
(function($) {
    'use strict';

    // Initialize datepicker on date fields (if jQuery UI is available)
    if ($.fn.datepicker) {
        $(document).on('focus', '.dmm-datepicker', function() {
            $(this).datepicker({
                dateFormat: dmm_admin_params.date_format || 'dd/mm/yy',
                changeMonth: true,
                changeYear: true
            });
        });
    }

    // Initialize color pickers
    if ($.fn.wpColorPicker) {
        $('.dmm-color-picker').wpColorPicker({
            change: function(event, ui) {
                // Trigger change event for preview updates
                $(this).trigger('change');
            }
        });
    }

    // Initialize media uploader for background images
    $(document).on('click', '.dmm-upload-media', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var field = $(this).data('target');
        
        // Create the media frame
        var file_frame = wp.media.frames.file_frame = wp.media({
            title: dmm_admin_params.media_title || 'Select or Upload Media',
            button: {
                text: dmm_admin_params.media_button || 'Use this media'
            },
            multiple: false
        });

        // When an image is selected, run a callback
        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            $('#' + field).val(attachment.url).trigger('change');
            if (button.data('preview')) {
                $('#' + button.data('preview')).html('<img src="' + attachment.url + '" style="max-width:100%;">');
            }
        });

        // Finally, open the modal
        file_frame.open();
    });

    // Excel template download
    $(document).on('click', '#dmm-download-template', function(e) {
        e.preventDefault();
        
        var delimiter = dmm_admin_params.delimiter || ',';
        var dateFormat = dmm_admin_params.date_format_label || 'DD/MM/YYYY';
        
        var content = '';
        content += dmm_admin_params.date_label + delimiter;
        content += dmm_admin_params.menu_items_label + delimiter;
        content += dmm_admin_params.is_special_label + "\n";
        
        // Add sample data rows
        var today = new Date();
        for (var i = 0; i < 3; i++) {
            var date = new Date();
            date.setDate(today.getDate() + i);
            
            // Format date according to the selected format
            var formattedDate = formatDate(date, dmm_admin_params.date_format);
            
            content += formattedDate + delimiter;
            if (i === 0) {
                content += dmm_admin_params.sample_menu_1 + delimiter;
            } else if (i === 1) {
                content += dmm_admin_params.sample_menu_2 + delimiter;
            } else {
                content += dmm_admin_params.sample_menu_3 + delimiter;
            }
            content += (i === 1 ? '1' : '0') + "\n";
        }
        
        var blob = new Blob([content], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', 'menu_template.csv');
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
    
    // Helper function to format dates
    function formatDate(date, format) {
        var day = ('0' + date.getDate()).slice(-2);
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var year = date.getFullYear();
        
        var formatted = format
            .replace('dd', day)
            .replace('mm', month)
            .replace('yyyy', year);
            
        return formatted;
    }

    // Preview updates for styling page
    $(document).on('change', '#font_family, #font_size, #text_color, #background_color, #background_image, #container_width, #border_style', function() {
        updateStylePreview();
    });

    function updateStylePreview() {
        var fontFamily = $('#font_family').val();
        var fontSize = $('#font_size').val();
        var textColor = $('#text_color').val();
        var bgColor = $('#background_color').val();
        var bgImage = $('#background_image').val();
        var containerWidth = $('#container_width').val();
        var borderStyle = $('#border_style').val();
        
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

    // Handle AJAX form submissions with error handling
    $(document).on('submit', '.dmm-ajax-form', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitButton = $form.find('button[type="submit"]');
        var $feedbackArea = $('#' + $form.data('feedback'));
        
        // Disable submit button and show loading message
        $submitButton.prop('disabled', true);
        
        if ($feedbackArea.length) {
            $feedbackArea.html('<p>' + dmm_admin_params.loading_message + '</p>').show();
        }
        
        // Gather form data
        var formData = new FormData(this);
        formData.append('action', $form.data('action'));
        formData.append('nonce', dmm_admin_params.nonce);
        
        // Send AJAX request
        $.ajax({
            url: dmm_admin_params.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $submitButton.prop('disabled', false);
                
                if (response.success) {
                    if ($feedbackArea.length) {
                        $feedbackArea.html('<div class="notice notice-success is-dismissible"><p>' + response.message + '</p></div>');
                    } else {
                        alert(response.message);
                    }
                    
                    if ($form.data('reload')) {
                        window.location.reload();
                    }
                    
                    if ($form.data('reset')) {
                        $form[0].reset();
                    }
                } else {
                    if ($feedbackArea.length) {
                        $feedbackArea.html('<div class="notice notice-error is-dismissible"><p>' + 
                            (response.message || dmm_admin_params.error_message) + '</p></div>');
                    } else {
                        alert(response.message || dmm_admin_params.error_message);
                    }
                }
            },
            error: function(xhr, status, error) {
                $submitButton.prop('disabled', false);
                
                if ($feedbackArea.length) {
                    $feedbackArea.html('<div class="notice notice-error is-dismissible"><p>' + 
                        dmm_admin_params.ajax_error + '</p></div>');
                } else {
                    alert(dmm_admin_params.ajax_error);
                }
                
                console.error(xhr.responseText);
            }
        });
    });

    // Initialize confirmation dialogs
    $(document).on('click', '.dmm-confirm', function(e) {
        e.preventDefault();
        
        var confirmText = $(this).data('confirm') || dmm_admin_params.confirm_default;
        
        if (confirm(confirmText)) {
            if ($(this).is('a')) {
                window.location.href = $(this).attr('href');
            } else if ($(this).is('button[type="submit"]')) {
                $(this).closest('form').submit();
            }
        }
    });

})(jQuery);
