/**
 * Canadian Routing Number Handler
 * Handles the display and validation of Canadian routing numbers (Institution ID + Transit Number)
 */
$(document).ready(function() {
    
    // Check initial country setting and show/hide appropriate fields
    toggleCanadianFields();
    
    // Monitor country changes
    $("#country").change(function() {
        toggleCanadianFields();
    });
    
    // Monitor Canadian routing number fields for changes
    $("#institution_number, #transit_number").on('input', function() {
        updateCombinedRoutingNumber();
    });

    // Monitor the standard routing checkbox
    $("#use_standard_routing").on('change', function() {
        toggleStandardRoutingForCanada();
    });

    // Form submission handler to ensure proper routing number is submitted
    $('#createPaymentMethodForm').on('submit', function(e) {
        if (isCanadianCountry()) {
            if ($("#use_standard_routing").is(':checked')) {
                // Using standard routing, validate that field
                var standardRouting = $('#routing_number').val().trim();
                if (standardRouting.length !== 9) {
                    e.preventDefault();
                    showError(Lang.get('billing.routingStandardInvalid'));
                    return false;
                }
            } else {
                // Using Canadian format
                updateCombinedRoutingNumber();
                
                var institution = $('#institution_number').val().trim();
                var transit = $('#transit_number').val().trim();
                
                if (institution.length !== 3 || transit.length !== 5) {
                    e.preventDefault();
                    showError(Lang.get('billing.routingCanadianInvalid'));
                    return false;
                }
                
                // Set the combined routing number to the main routing_number field
                var combinedRouting = $('#routing_number_canadian').val();
                $('#routing_number').val(combinedRouting);
            }
        }
    });
    
    function isCanadianCountry() {
        var selectedCountry = $("#country").val();
        return selectedCountry === 'CA';
    }
    
    function toggleCanadianFields() {
        if (isCanadianCountry()) {
            // Show Canadian fields and hide standard field initially
            $("#canadian-routing-section").show();
            $("#standard-routing-section").hide();
            
            // Remove validation from the standard routing number field
            $('#routing_number').removeAttr('required');
            
            // Add validation to Canadian fields initially
            $('#institution_number').attr('required', 'required');
            $('#transit_number').attr('required', 'required');
            
            // Clear the standard routing number field to avoid conflicts
            $('#routing_number').val('');
        } else {
            // Show standard field and hide Canadian fields
            $("#standard-routing-section").show();
            $("#canadian-routing-section").hide();
            
            // Add validation back to standard routing number field
            $('#routing_number').attr('required', 'required');
            
            // Remove validation from Canadian fields
            $('#institution_number').removeAttr('required');
            $('#transit_number').removeAttr('required');
            
            // Clear Canadian fields when switching away from Canada
            $('#institution_number').val('');
            $('#transit_number').val('');
            $('#routing_number_canadian').val('');
            $('#routing-display').remove();
            $('#use_standard_routing').prop('checked', false);
        }
    }

    function toggleStandardRoutingForCanada() {
        if ($("#use_standard_routing").is(':checked')) {
            // Hide Canadian input fields and label, show standard routing field
            $("#canadian-input-fields").fadeOut(300);
            $("#standard-routing-section").fadeIn(300);
            $('#routing_number').attr('required', 'required');

            // Hide the Canadian routing placeholder label
            $(".canada-routing-label").hide();

            // Remove validation from Canadian fields
            $('#institution_number').removeAttr('required');
            $('#transit_number').removeAttr('required');

            // Clear Canadian fields and visual feedback
            $('#institution_number').val('').removeClass('is-valid is-invalid');
            $('#transit_number').val('').removeClass('is-valid is-invalid');
            $('#routing_number_canadian').val('');
            $('#routing-display').remove();
        } else {
            // Show Canadian input fields and label, hide standard routing field
            $("#canadian-input-fields").fadeIn(300);
            $("#standard-routing-section").fadeOut(300);
            $('#routing_number').removeAttr('required');
            $('#routing_number').val('').removeClass('is-valid is-invalid');

            // Show the Canadian routing placeholder label
            $(".canada-routing-label").show();

            // Add validation back to Canadian fields
            $('#institution_number').attr('required', 'required');
            $('#transit_number').attr('required', 'required');
        }
    }
    
    function updateCombinedRoutingNumber() {
        if (isCanadianCountry()) {
            var institutionRaw = $('#institution_number').val().trim();
            var transitRaw = $('#transit_number').val().trim();
            
            if (institutionRaw.length > 0 && transitRaw.length > 0) {
                var institution = institutionRaw.padStart(3, '0');
                var transit = transitRaw.padStart(5, '0');
                
                if (institution.length === 3 && transit.length === 5) {
                    // Canadian format: 0 + institution number (3 digits) + transit number (5 digits)
                    var combinedRouting = '0' + institution + transit; // backend expects leading 0
                    var displayRouting = institution + transit; // UI should hide the leading 0
                    $('#routing_number_canadian').val(combinedRouting);

                    // Provide visual feedback (without leading 0)
                    updateRoutingDisplay(institution, transit, displayRouting);
                    return;
                }
            }
            
            $('#routing_number_canadian').val('');
            updateRoutingDisplay('', '', '');
        }
    }
    
    function updateRoutingDisplay(institution, transit, combined) {
        var displayElement = $('#routing-display');
        
        if (combined) {
            // 'combined' passed here is display-only (without leading 0)
            var displayText = Lang.get('billing.routingDisplayLabel', {routing: combined, institution: institution, transit: transit});
            
            if (displayElement.length === 0) {
                $('<small id="routing-display" class="form-text text-success mt-2"><i class="fa fa-check-circle"></i> <span id="routing-text"></span></small>')
                    .insertAfter('#canadian-input-fields');
            }
            $('#routing-text').text(displayText);
            displayElement.removeClass('text-warning').addClass('text-success');
        } else {
            if (displayElement.length > 0) {
                if ($('#institution_number').val().length > 0 || $('#transit_number').val().length > 0) {
                    $('#routing-text').text(Lang.get('billing.routingIncomplete'));
                    displayElement.removeClass('text-success').addClass('text-warning');
                } else {
                    displayElement.remove();
                }
            }
        }
    }
    
    // Input validation for Canadian fields
    $('#institution_number').on('input', function() {
        var value = $(this).val();
        // Allow only numeric input, max 3 digits
        var cleaned = value.replace(/[^\d]/g, '').substring(0, 3);
        $(this).val(cleaned);
        
        // Add visual feedback for validation
        updateFieldValidation($(this), cleaned.length === 3);
    });
    
    $('#transit_number').on('input', function() {
        var value = $(this).val();
        // Allow only numeric input, max 5 digits  
        var cleaned = value.replace(/[^\d]/g, '').substring(0, 5);
        $(this).val(cleaned);
        
        // Add visual feedback for validation
        updateFieldValidation($(this), cleaned.length === 5);
    });
    
    function updateFieldValidation(field, isValid) {
        var formGroup = field.closest('.form-group');
        
        if (field.val().length === 0) {
            // Remove validation classes if field is empty
            field.removeClass('is-valid is-invalid');
            return;
        }
        
        if (isValid) {
            field.removeClass('is-invalid').addClass('is-valid');
        } else {
            field.removeClass('is-valid').addClass('is-invalid');
        }
    }
    
    function showError(message) {
        // You can customize this to match your error display method
        if (typeof swal !== 'undefined') {
            swal(Lang.get('headers.error'), message, 'error');
        } else {
            alert(message);
        }
    }
});