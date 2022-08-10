$(document).on('ready', function () {
    
    $('.submitPayment').on('click', function (event) {
        console.log("here");
        $(this).attr("disabled", true);
        event.preventDefault();
        var options = {
            name: $('#cardholderName').val(),
            address_line1: $('#address').val(),
        };

        stripe.createToken(card, options).then(function (result) {
            if (result.error) {
                $('.subitPayment').attr('disabled', false);
                // Inform the user if there was an error.
                var errorElement = document.getElementById("card-errors");
                errorElement.textContent = result.error.message;
            } else {
                // Send the token to your server.
                stripeTokenHandler(result.token, this);
            }
        });
    });

    // Submit the form with the token ID.
    function stripeTokenHandler(token, target) {
        // Insert the token ID into the form so it gets submitted to the server
        var form = document.getElementById('payment-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);
        performPurchase(hiddenInput, form, function () {

            var number = $('#card-errors').children().length;
            if ($('#card-errors').children().length == 0) {
                $('form').submit();
            }
        })
    }

    function performPurchase(hiddenInput, form, callback) {
        var form = document.getElementById("payment-form");
        form.appendChild(hiddenInput);
        callback();
    }
});