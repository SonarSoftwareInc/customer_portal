$(document).ready(function(){
    var cardholderName = document.getElementById('name');
    var cardContainer = document.getElementById('stripe_container');
    var paymentMethod = document.getElementById('payment_method');

    var clientSecret = cardContainer.dataset.secret; // SetupIntent client secret
    var key = cardContainer.dataset.key; // Publishable Stripe API key

    var stripe = Stripe(key);
    var elements = stripe.elements();
    var cardElement = elements.create('card');
    cardElement.mount('#card-element');

    // Initially hide the table and show the "show-text"
    $('.invoicesTable').hide();

    // Click to toggle the table
    $("#toggleInvoices").click(function(){
        $('.invoicesTable').slideToggle();
        $('.show-text').toggle();
        $('.hide-text').toggle();
    });

    //Payment Submit form
    $('#paymentForm').submit(async function(event) {
        $('#submit_payment').prop('disabled', 'disabled')

        //Only do stripe tings if its a new card
        if (paymentMethod.value !== 'new_card') {
            return true;
        }

        event.preventDefault();
        const paymentForm = event.target;

        /** Confirm the Card prior to Payment */
        let result = await stripe.confirmCardSetup(
            clientSecret,
            {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: cardholderName.value,
                    },
                },
            }
        );

        if (result.error) {
            // Display error.message in your UI.
            console.error(result.error);
            $('#stripe_errors').text(result.error.message);

        } else {
            //Grab the card details with our payment method ID
            let cardDetails = await $.get('/portal/billing/stripe/' + result.setupIntent.payment_method);

            //Add hidden fields to form
            $('<input>').attr({
                type: 'hidden',
                id: 'customerId',
                name: 'customerId',
                value: cardDetails.customer,
            }).appendTo('#paymentForm');

            $('<input>').attr({
                type: 'hidden',
                id: 'token',
                name: 'token',
                value: cardDetails.id,
            }).appendTo('#paymentForm');

            $('<input>').attr({
                type: 'hidden',
                id: 'identifier',
                name: 'identifier',
                value: cardDetails.card.last4,
            }).appendTo('#paymentForm');

            $('<input>').attr({
                type: 'hidden',
                id: 'expirationDate',
                name: 'expirationDate',
                value: `${cardDetails.card.exp_month}/${cardDetails.card.exp_year}`,
            }).appendTo('#paymentForm');

            $('<input>').attr({
                type: 'hidden',
                id: 'zip',
                name: 'zip',
                value: cardDetails.billing_details.address.postal_code,
            }).appendTo('#paymentForm');

            $('<input>').attr({
                type: 'hidden',
                id: 'cardType',
                name: 'cardType',
                value: `${cardDetails.card.brand}`,
            }).appendTo('#paymentForm');

            //Submit form time
            paymentForm.submit();
        }
    });

    //Creation Form
    $('#createStripePaymentMethodForm').submit(async function(event) {

        event.preventDefault();
        const form = event.target;

        //Display loading spinna

        let result = await stripe.confirmCardSetup(
            clientSecret,
            {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: cardholderName.value,
                    },
                },
            }
        );

        if (result.error) {
            // Display error.message in your UI.
            console.error(result.error);
            $('#stripe_errors').text(result.error.message);

        } else {
            let cardDetails = await $.get('/portal/billing/stripe/' + result.setupIntent.payment_method);

            //cardDetails

            //Add hidden fields to form
            $('<input>').attr({
                type: 'hidden',
                id: 'customerId',
                name: 'customerId',
                value: cardDetails.customer,
            }).appendTo('#createStripePaymentMethodForm');

            $('<input>').attr({
                type: 'hidden',
                id: 'token',
                name: 'token',
                value: cardDetails.id,
            }).appendTo('#createStripePaymentMethodForm');

            $('<input>').attr({
                type: 'hidden',
                id: 'identifier',
                name: 'identifier',
                value: cardDetails.card.last4,
            }).appendTo('#createStripePaymentMethodForm');

            $('<input>').attr({
                type: 'hidden',
                id: 'expirationDate',
                name: 'expirationDate',
                value: `${cardDetails.card.exp_month}/${cardDetails.card.exp_year}`,
            }).appendTo('#createStripePaymentMethodForm');

            $('<input>').attr({
                type: 'hidden',
                id: 'cardType',
                name: 'cardType',
                value: `${cardDetails.card.brand}`,
            }).appendTo('#createStripePaymentMethodForm');

            //Submit form time
            form.submit();
        }
    });

    // updatePaymentForm();

    $("#country").change(function(){
        updateSubdivisions();
    });

    $("#payment_method").change(function(){
        updatePaymentForm();
    });

    // Enable the submit button when the payment method or amount to pay field changes
    $('#payment_method, #amount').change(function () {
        $('#submit_payment').prop('disabled', false);
    });

});

function updateSubdivisions()
{
    var country = $("#country").val();
    $("#state").prop('disabled',true);
    var jqxhr = $.get("/portal/billing/subdivisions/" + country, function(data) {
        $("#state").empty();
        var show = false;
        let initialValue = "";
        $.each(data.subdivisions, function (index, value) {
            // When updating subdivisions there needs to be a default or we submit an invalid country / state combo
            let selected = "";
            if (show === false) {
                selected = " selected=\"selected\"";
                initialValue = index;
            }
            show = true;
            $("#state").append("<option value='" + index + "'" + selected + ">" + value + "</option>");
        });
        if (show === true) {
            $("#state").val(initialValue);
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
    var selectedOption = $('#payment_method').find(':selected');
    var paymentType = selectedOption.data('type');

    // Perform actions based on the payment type
    if (paymentType === 'bank_account') {
        $('.credit-card-autopay').hide();
        $('.bank-account-payment').show();
        $('.credit-card-images').hide();
        $('.new_card').hide();
    } else if (paymentType === 'paypal') {
        $('.credit-card-autopay').hide();
        $('.bank-account-payment').hide();
        $('.credit-card-images').hide();
        $('.new_card').hide();
    } else if (paymentType === 'new_card') {
        $('.credit-card-autopay').show();
        $('.bank-account-payment').hide();
        $('.credit-card-images').show();
        $('.new_card').show();
    } else {
        $('.credit-card-autopay').hide();
        $('.bank-account-payment').hide();
        $('.credit-card-images').hide();
        $('.new_card').hide();
    }
}
