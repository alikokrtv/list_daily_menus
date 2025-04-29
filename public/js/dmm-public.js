/**
 * Public JavaScript for Daily Menu Manager
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Enhance the menu selector with additional features
        $('.dmm-selector-container select').on('change', function() {
            // The main menu update is handled by the inline script
            // This is for any additional behavior needed
        });

        // Make menu containers responsive
        $(window).on('resize', function() {
            responsiveAdjustments();
        });
        
        // Initial call for responsive adjustments
        responsiveAdjustments();
        
        // Handle special dates highlighting
        highlightSpecialDates();
    });
    
    /**
     * Function to make responsive adjustments to the menu display
     */
    function responsiveAdjustments() {
        if ($(window).width() < 600) {
            $('.dmm-container').css('width', '100%');
            $('#dmm-menu-container').css({
                'min-height': '200px',
                'height': 'auto'
            });
        }
    }
    
    /**
     * Highlight special dates in the dropdown
     */
    function highlightSpecialDates() {
        // The special dates are already styled with bold in the PHP,
        // but we can add additional highlighting here if needed
        $('.dmm-selector-container select option[style*="bold"]').each(function() {
            $(this).css('background-color', '#f9f9f9');
        });
    }

})(jQuery);
