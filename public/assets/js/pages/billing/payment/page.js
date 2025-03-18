$(document).ready(function(){
    var ccNumberField = $("#cc-number");
    var expirationField = $("#expirationDate");
    var ccIcon = $("#ccIcon");

    ccNumberField.payment('formatCardNumber');
    expirationField.payment('formatCardExpiry');

    updatePaymentForm();

    $("#country").change(function(){
        updateSubdivisions();
    });

    // Initially hide the table and show the "show-text"
    $('.invoicesTable').hide();

    // Click to toggle the table
    $("#toggleInvoices").click(function(){
        $('.invoicesTable').slideToggle();
        $('.show-text').toggle();
        $('.hide-text').toggle();
    });

    ccNumberField.keyup(function (e){
        var cardType = $.payment.cardType($("#cc-number").val());
        ccIcon.removeClass();
        switch (cardType) {
            case "visa":
            case "visaelectron":
                ccIcon.addClass("fa fa-cc-visa");
                break;
            case "mastercard":
                ccIcon.addClass("fa fa-cc-mastercard");
                break;
            case "amex":
                ccIcon.addClass("fa fa-cc-amex");
                break;
            case "dinersclub":
                ccIcon.addClass("fa fa-cc-diners-club");
                break;
            case "discover":
                ccIcon.addClass("fa fa-cc-discover");
                break;
            case "jcb":
                ccIcon.addClass("fa fa-cc-jcb");
                break;
            default:
                ccIcon.addClass("fa fa-cc");
                break;
        }
    });

    function handlePaymentMethodChange() {
        var selectedOption = $('#payment_method').find(':selected');
        var paymentType = selectedOption.data('type');

        console.log('Selected Payment Type:', paymentType);

        // Perform actions based on the payment type
        if (paymentType === 'credit_card') {
            $('.credit-card-autopay').hide();
            $('.bank-account-payment').hide();
        } else if (paymentType === 'bank_account') {
            $('.credit-card-autopay').hide();
            $('.bank-account-payment').show();
        } else if (paymentType === 'paypal') {
            $('.credit-card-autopay').hide();
            $('.bank-account-payment').hide();
        } else {
            $('.credit-card-autopay').show();
            $('.bank-account-payment').hide();
        }
    }

    $('#payment_method').change(function () {
        handlePaymentMethodChange();
    });

    handlePaymentMethodChange();

    $("#payment_method").change(function(){
        updatePaymentForm();
    });

    $("#paymentForm").submit(function () {
        var selectedPaymentMethod = $("#payment_method").val();
        switch (selectedPaymentMethod) {
            case "new_card":
                $(".new_card").show();
                $(".non_paypal").show();
                $(".paypal").remove();
                break;
            case "paypal":
                $(".new_card").remove();
                $(".non_paypal").remove();
                $(".paypal").show();
                break;
            default:
                //Existing card
                $(".new_card").remove();
                $(".non_paypal").show();
                $(".paypal").remove();
                break;
        }


        var allClear = true;
        var elements = document.querySelectorAll("#paymentForm input");
        for (var i = 0, element; element = elements[i++];) {
            if (element.value === "") {
                allClear = false;
                break;
            }
        }
        $("#submit_payment").prop('disabled', allClear);
    });

});

function updateSubdivisions()
{
    var country = $("#country").val();
    $("#state").prop('disabled',true);
    var jqxhr = $.get("/portal/billing/subdivisions/" + country, function(data) {
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

function updatePaymentForm() {
    var paymentMethodSelect = $("#payment_method");

    var selectedPaymentMethod = paymentMethodSelect.val();
    switch (selectedPaymentMethod) {
        case "new_card":
            $(".new_card").show();
            $(".non_paypal").show();
            $(".paypal").hide();
            break;
        case "paypal":
            $(".new_card").hide();
            $(".non_paypal").hide();
            $(".paypal").show();
            break;
        default:
            // Existing card
            $(".new_card").hide();
            $(".non_paypal").show();
            $(".paypal").hide();
            break;
    }
}
