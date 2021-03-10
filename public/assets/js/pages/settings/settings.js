$(document).ready(function(){
    $("#country").change(function(){
        updateSubdivisions();
    });
    $("#mail_password")[0].type = "password";
    $("#mail_password")[0].autocomplete = "current-password";
    $("#passwd-reveal").change(function() {
      $("#mail_password")[0].type = ($(this).prop("checked")) ? "text" : "password";
    });

    $('#bank_payments_enabled').on('change', updateBankPaymentsSubFields);
    updateBankPaymentsSubFields();
});

function updateBankPaymentsSubFields() {
    var inputValue = $('#bank_payments_enabled')[0].checked;
    var wrapperEl = $('#bank_payments_only_before_wrapper')[0];

    wrapperEl.style.display = inputValue ? 'block' : 'none';
}

function updateSubdivisions()
{
    var country = $("#country").val();
    $("#state").prop('disabled',true);
    var jqxhr = $.get("/settings/subdivisions/" + country, function(data) {
        $("#state").empty();
        var show = false;
        $.each(data.subdivisions, function (index, value) {
            show = true;
           $("#state").append("<option value='" + index + "'>" + value + "</option>");
        });
        if (show === true) {
            $("#stateWrapper").show();
        }
        else {
            $("#stateWrapper").hide();
        }
    })
    .fail(function() {
        swal({
            title: Lang.get("headers.error"),
            text: Lang.get("errors.failedToLookupSubdivision"),
            type: "error",
            showCancelButton: false
        },
        function(isConfirm) {
            if (isConfirm) {
                window.location.reload();
            }
        });
    })
    .always(function() {
        $("#state").prop('disabled',false);
    });
}
