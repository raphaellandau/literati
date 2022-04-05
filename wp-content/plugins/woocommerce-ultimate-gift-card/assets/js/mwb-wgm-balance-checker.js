(function( $ ) {
	'use strict';

	$(document).ready(function() {
		$(document).on('click','#mwb_check_balance',function(){
			var email = $('#gift_card_balance_email').val();
			var coupon = $('#gift_card_code').val();
			$("#mwb_wgm_loader").show();
			var data = {
				      action:'mwb_wgm_check_giftcard',
					  email:email,
					  coupon:coupon,
					  mwb_nonce:mwb_check.mwb_wgm_nonce
				   };
			$.ajax({
				url: mwb_check.ajaxurl, 
				type: "POST",  
				data: data,
				dataType :'json',	
				success: function(response) {
					
					$("#mwb_wgm_loader").hide();
					if(response.result == true){
						var html = response.html;
					}	
					else{
						var message = response.message;
						var html = '<b style="color:red; margin-left:2%">'+message+'</b>';
					}	
					$("#mwb_notification").html(html);
				}
			});
		});
	});

})( jQuery );