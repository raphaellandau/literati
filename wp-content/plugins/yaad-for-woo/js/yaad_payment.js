(function ($) {
    $(function () {
        
        $(document.body).on( "click", "input[type=radio][name=wc-yaad_payment-payment-token]", function() {
            if ($( this ).val() == 'new') {
                $("#yaadpay-installment_dropdown").hide();
                $("#yaadpay-customer-id").hide();
            } else {
                $("#yaadpay-installment_dropdown").show();
                $("#yaadpay-customer-id").show();
            }
        });
        
        $(document.body).on('updated_checkout wc-credit-card-form-init', function () {
            if ($("input[name='wc-yaad_payment-payment-token']:checked").val() == 'new' || typeof $("input[name='wc-yaad_payment-payment-token']:checked").val() === 'undefined') {
                $("#yaadpay-installment_dropdown").hide();
                $("#yaadpay-customer-id").hide();
            } else {
                $("#yaadpay-installment_dropdown").show();
                $("#yaadpay-customer-id").show();
            }

        });
        
    });
})(jQuery);
