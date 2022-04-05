jQuery(document).ready(function($){

	$('#mwb_wgm_price').keyup(function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

	//variable price for giftcard.
	mwb_wgm_variable_price_change_page_load(jQuery('#mwb_wgm_price').val());
	jQuery('#mwb_wgm_price').change(function(){

		if (mwb_wgm.pricing_type.type == 'mwb_wgm_variable_price') {

			var mwb_wgm_price = jQuery(this).val();
			mwb_wgm_variable_price_change(mwb_wgm_price);
		}
	});

	function mwb_wgm_variable_price_change(mwb_wgm_price) {
		if (mwb_wgm.pricing_type.type == 'mwb_wgm_variable_price') {
			var product_id = mwb_wgm.product_id;
			block($('.summary.entry-summary'));
			var data = {
				action:'mwb_wgm_append_variable_price',
				mwb_wgm_price:mwb_wgm_price,
				product_id:product_id,
				mwb_nonce:mwb_wgm.mwb_wgm_nonce
			};
			$.ajax({
				url: mwb_wgm.ajaxurl, 
				type: "POST",  
				data: data,
				dataType: 'json',
				success: function(response) 
				{
					if(response.result == true)
					{
						jQuery('#mwb_wgm_text').html(response.new_price);
					} 
				},
				complete: function() 
				{
					unblock( $( '.summary.entry-summary' ) );
				}
			});
		}
	}
	function mwb_wgm_variable_price_change_page_load(mwb_wgm_price) {
		if (mwb_wgm.pricing_type.type == 'mwb_wgm_variable_price') {
			var product_id = mwb_wgm.product_id;
			var data = {
				action:'mwb_wgm_append_variable_price',
				mwb_wgm_price:mwb_wgm_price,
				product_id:product_id,
				mwb_nonce:mwb_wgm.mwb_wgm_nonce
			};
			$.ajax({
				url: mwb_wgm.ajaxurl, 
				type: "POST",  
				data: data,
				dataType: 'json',
				success: function(response) 
				{
					if(response.result == true)
					{
						jQuery('#mwb_wgm_text').html(response.new_price);
					} 
				},
				// complete: function() 
				// {
				// 	unblock( $( '.summary.entry-summary' ) );
				// }
			});
		}
	}

	var datenable = mwb_wgm.datenable;
	$('#mwb_wgm_send_date').datepicker({
		dateFormat : mwb_wgm.dateformat,
		minDate: 0
	}).datepicker("setDate", "0");
	$('.mwb_wgm_featured_img').click(function(){
		$('.mwb_wgm_selected_template').find('.mwb_wgm_featured_img').removeClass('mwb_wgm_pre_selected_temp')
		var img_id = $(this).attr('id');
		$('#'+img_id).addClass('mwb_wgm_pre_selected_temp');
		$('#mwb_wgm_selected_temp').val(img_id);
	});
	if(typeof mwb_wgm.pricing_type.type != 'undefined')
	{
		var datenable = mwb_wgm.datenable;
		$('#mwb_wgm_send_date').datepicker({
			dateFormat : mwb_wgm.dateformat,
			minDate: 0
		}).datepicker("setDate", "0");
	}
	$("#mwb_wgm_message").keyup(function(){
		var msg_length = $(document).find('#mwb_wgm_message').val().length;
		if(msg_length == 0){

			$('#mwb_box_char').text(0);
		}
		else{
			$('#mwb_box_char').text(msg_length);
		}

	});
	$(".single_add_to_cart_button").click(function(e){
		
		if(typeof mwb_wgm.pricing_type.type != 'undefined')
		{	
			e.preventDefault();
			$("#mwb_wgm_error_notice").hide();
			var mwb_wgm_method_enable = mwb_wgm.mwb_wgm_method_enable;
			var mwb_wgm_customer_selection = mwb_wgm.mwb_wgm_customer_selection;	
			var overwrite_mail = mwb_wgm.overwrite_mail;
			var overwrite_download = mwb_wgm.overwrite_download;
			var overwrite_shipping = mwb_wgm.overwrite_shipping;
			var from_mail = $("#mwb_wgm_from_name").val();
			var message = $("#mwb_wgm_message").val();
			message = message.trim();
			var price = $("#mwb_wgm_price").val();
			var error = false;
			var product_type = mwb_wgm.pricing_type.type;
			var decimal_separator = mwb_wgm.decimal_separator;
			var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,5})+$/;

			html = "<ul>";
			var regex = /^[^a-z][0-9,./!@#$%^&*()]*$/;
			var isValid = false;
			isValid = regex.test(price);
			if(isValid == false) {
				error = true;
				$("#mwb_wgm_price").addClass("mwb_wgm_error");
				html+="<li><b>";
				html+=mwb_wgm.invalid_price;
				html+="</li>";
			}

			if(price == null || price == "")
			{
				error = true;
				$("#mwb_wgm_price").addClass("mwb_wgm_error");
				html+="<li><b>";
				html+=mwb_wgm.price_field;
				html+="</li>";
			}
			if(mwb_wgm.schedule_date != "" && mwb_wgm.schedule_date == "on")
			{
				var send_date = $("#mwb_wgm_send_date").val();
				if(send_date == null || send_date == "")
				{
					error = true;
					$("#mwb_wgm_send_date").addClass("mwb_wgm_error");
					html+="<li><b>";
					html+=mwb_wgm.send_date;
					html+="</li>";
				}
			}
			if(mwb_wgm.remove_validation_to == 'off')
			{
				if( mwb_wgm_method_enable == "normal_mail" )
				{	

					var to_mail_name = $("#mwb_wgm_to_name_optional").val();
					var to_mail = $("#mwb_wgm_to_email").val();
					if(to_mail == null || to_mail == "")
					{
						error = true;
						$("#mwb_wgm_to_email").addClass("mwb_wgm_error");
						html+="<li><b>";
						html+=mwb_wgm.to_empty;
						html+="</li>";
					} 
					else if(!to_mail.match(mailformat))
					{
						error = true;
						$("#mwb_wgm_to_email").addClass("mwb_wgm_error");
						html+="<li><b>";
						html+=mwb_wgm.to_invalid;
						html+="</li>";
					}
				}
				else if( mwb_wgm_method_enable == "download" || mwb_wgm_method_enable == "shipping" )
				{	
					var to_mail = "";
					if( mwb_wgm_method_enable == "download" )
					{
						to_mail = $("#mwb_wgm_to_download").val();	
					}
					else if( mwb_wgm_method_enable == "shipping" )
					{
						to_mail = $("#mwb_wgm_to_ship").val();
					}
					to_mail = to_mail.trim();
					from_mail = from_mail.trim();
					if(to_mail == null || to_mail == "")
					{
						error = true;
						$("#mwb_wgm_to_email").addClass("mwb_wgm_error");
						html+="<li><b>";
						html+=mwb_wgm.to_empty_name;
						html+="</li>";
					}
				}
				else if( mwb_wgm_method_enable == "customer_choose" )
				{
					if( mwb_wgm_customer_selection['Email_to_recipient']== '1' || overwrite_mail == 'yes')
					{

						if($('#to_mail').is(':checked'))
						{
							var to_mail_name = $("#mwb_wgm_to_name_optional").val();
							var to_mail = $("#mwb_wgm_to_email").val();
							if(to_mail == null || to_mail == "")
							{
								error = true;
								$("#mwb_wgm_to_email").addClass("mwb_wgm_error");
								html+="<li><b>";
								html+=mwb_wgm.to_empty;
								html+="</li>";
							}
							else if(!to_mail.match(mailformat))
							{
								error = true;
								$("#mwb_wgm_to_email").addClass("mwb_wgm_error");
								html+="<li><b>";
								html+=mwb_wgm.to_invalid;
								html+="</li>";
							}	
						} 

					}				}
				}

			//validation for to name fields
			
			if(mwb_wgm.is_customizable_gift != 'yes' ) {
				if(mwb_wgm.remove_validation_to_name == 'off')
				{
					if(mwb_wgm_method_enable == "normal_mail")
					{	
						var to_mail_name = $("#mwb_wgm_to_name_optional").val();
						
						if(to_mail_name == null || to_mail_name == "")
						{
							error = true;
							$("#mwb_wgm_to_name_optional").addClass("mwb_wgm_error");
							html+="<li><b>";
							html+=mwb_wgm.to_empty_name;
							html+="</li>";
						}
					}
					else if( mwb_wgm_method_enable == "customer_choose" )
					{
						if( mwb_wgm_customer_selection['Email_to_recipient']== '1' || overwrite_mail == 'yes') 
						{
							if($('#to_mail').is(':checked'))
							{
								var to_mail_name = $("#mwb_wgm_to_name_optional").val();

								if(to_mail_name == null || to_mail_name == "")
								{
									error = true;
									$("#mwb_wgm_to_name_optional").addClass("mwb_wgm_error");
									html+="<li><b>";
									html+=mwb_wgm.to_empty_name;
									html+="</li>";
								}
							}
						}
						if( mwb_wgm_customer_selection['Downloadable'] == '1' || overwrite_mail == 'yes') 
						{
							if($('#mwb_wgm_send_giftcard_download').is(':checked'))
							{
								var to_mail_name = $("#mwb_wgm_to_download").val();

								if(to_mail_name == null || to_mail_name == "")
								{
									error = true;
									$("#mwb_wgm_to_name_optional").addClass("mwb_wgm_error");
									html+="<li><b>";
									html+= 'To: Name Field is empty.';
									html+="</li>";
								}
							}
						}		
						if( mwb_wgm_customer_selection['Shipping'] == '1' || overwrite_mail == 'yes') 
						{
							if($('#mwb_wgm_send_giftcard_ship').is(':checked'))
							{
								var to_mail_name = $("#mwb_wgm_to_ship").val();

								if(to_mail_name == null || to_mail_name == "")
								{
									error = true;
									$("#mwb_wgm_to_name_optional").addClass("mwb_wgm_error");
									html+="<li><b>";
									html+= 'To: Name Field is empty.';
									html+="</li>";
								}
							}
						}	
					}
				}
			}
			if (!$("input[name='mwb_wgm_send_giftcard']:checked").val()) 
			{
				error = true;
				$(".mwb_wgm_send_giftcard").addClass("mwb_wgm_error");
				html+="<li><b>";
				html+=mwb_wgm.method_empty;
				html+="</li>";
			}
			if(mwb_wgm.remove_validation_from == 'off')
			{
				if(from_mail == null || from_mail == "")
				{
					error = true;
					$("#mwb_wgm_from_name").addClass("mwb_wgm_error");
					html+="<li><b>";
					html+=mwb_wgm.from_empty;
					html+="</li>";
				}
			}    
			if(mwb_wgm.remove_validation_msg == 'off')
			{    
				if(message == null || message == "")
				{
					error = true;
					$("#mwb_wgm_message").addClass("mwb_wgm_error");
					html+="<li><b>";
					html+=mwb_wgm.msg_empty;
					html+="</li>";
				}
				else if( message.length > mwb_wgm.msg_length ){
					error = true;
					$("#mwb_wgm_message").addClass("mwb_wgm_error");
					html+="<li><b>";
					html+=mwb_wgm.msg_length_err;
					html+="</li>";
				}			
			}    
			if(product_type == "mwb_wgm_range_price")
			{
				var from = mwb_wgm.pricing_type.from;
				var to = mwb_wgm.pricing_type.to;
				from = parseFloat( from.replace( decimal_separator ,'.') );
				to = parseFloat( to.replace( decimal_separator ,'.') );
				price = parseFloat( price.replace( decimal_separator ,'.') );

				if(price > to || price < from)
				{
					error = true;
					$("#mwb_wgm_price").addClass("mwb_wgm_error");
					html+="<li><b>";
					html+=mwb_wgm.price_range;
					html+="</li>";
				}  	  
			}
			html += "</ul>";

			if(error)
			{
				$("#mwb_wgm_error_notice").html(html);
				$("#mwb_wgm_error_notice").show();
	        	//MWB code for woodmart theme
	        	$("#mwb_wgm_error_notice").removeClass('hidden-notice');
	        	//MWB code for woodmart theme
	        	jQuery('html, body').animate({
	        		scrollTop: jQuery(".woocommerce-page").offset().top
	        	}, 800);
	        	$(".single_add_to_cart_button").removeClass("loading");
	        }
	        else
	        {
	        	$("#mwb_wgm_error_notice").html("");
	        	$("#mwb_wgm_error_notice").hide();
	        	$(this).closest("form.cart" ).submit();
	        }

	    }
	});
$( '.mwb_wgm_send_mail_force' ).click( function() {

	var order_id = $(this).data('id');
	var item_id = $(this).data('num');
	$("#mwb_wgm_send_mail_force_notification_"+item_id).html("");

	$("#mwb_wgm_loader").show();
	var data = {
		action:'mwb_wgm_send_mail_force',
		order_id:order_id,
		item_id:item_id,
		mwb_nonce:mwb_wgm.mwb_wgm_nonce
	};
	$.ajax({
		url: mwb_wgm.ajaxurl, 
		type: "POST",  
		data: data,
		dataType :'json',	
		success: function(response) 
		{

			$("#mwb_wgm_loader").hide();
			if(response.result == true)
			{
				var message = response.message;
				var html = '<b style="color:green;">'+message+'</b>';
				$('#mwb_send_force_div_'+item_id).hide();
			}	
			else
			{
				var message = response.message;
				var html = '<b style="color:red;">'+message+'</b>';

			}	
			$("#mwb_wgm_send_mail_force_notification_"+item_id).html(html);
		}
	});
});
$( '#mwb_wgm_resend_mail_button' ).click( function() {

	$("#mwb_wgm_resend_mail_notification").html("");
	var order_id = $(this).data('id');
	$("#mwb_wgm_loader").show();
	var data = {
		action:'mwb_wgm_resend_mail',
		order_id:order_id,
		mwb_nonce:mwb_wgm.mwb_wgm_nonce
	};
	
	$.ajax({
		url: mwb_wgm.ajaxurl, 
		type: "POST",  
		data: data,
		dataType :'json',	
		success: function(response) 
		{
			$("#mwb_wgm_loader").hide();
			if(response.result == true)
			{
				var message = response.message;
				var html = '<b style="color:green;">'+message+'</b>';
			}	
			else
			{
				var message = response.message;
				var html = '<b style="color:red;">'+message+'</b>';

			}	
			$("#mwb_wgm_resend_mail_notification").html(html);
		}
	});
});
$("#mwb_wgm_browse_img").on("change", function()
{     
	var error = false;
	html = "<ul>";
	var image_br = $(this).val();
	var extension = image_br.substring(image_br.lastIndexOf('.') + 1).toLowerCase();
	var all_ext = ["gif", "png", "jpeg", "jpg", "pjpeg", "x-png"];
	var exists = all_ext.indexOf(extension);
	if(exists == -1 ){
		console.log(mwb_wgm.browse_error);
		$("#mwb_wgm_error_notice").hide();
		error = true;
		$("#mwb_wgm_to_email").addClass("mwb_wgm_error");
		html+="<li><b>";
		html+=mwb_wgm.browse_error;
		html+="</li>";
		$(this).val("");
	}
	if(error)
	{
		$("#mwb_wgm_error_notice").html(html);
		$("#mwb_wgm_error_notice").show();
		jQuery('html, body').animate({
			scrollTop: jQuery(".woocommerce-page").offset().top
		}, 800);
	}

});

$( '#mwg_wgm_preview_email' ).click( function() {

	$("#mwb_wgm_error_notice").hide();
	var overwrite_mail = mwb_wgm.overwrite_mail;
	var overwrite_download = mwb_wgm.overwrite_download;
	var overwrite_shipping = mwb_wgm.overwrite_shipping;
	var mwb_wgm_customer_selection = mwb_wgm.mwb_wgm_customer_selection;
	var browse_enable = mwb_wgm.browseenable;
	var mwb_wgm_method_enable = mwb_wgm.mwb_wgm_method_enable;
	var from_mail = $("#mwb_wgm_from_name").val();
	var to_mail = '';
	var message = $("#mwb_wgm_message").val();
	message = message.trim();
	var regex = /(<([^>]+)>)/ig;
	var message = message.replace(regex,'');
	var price = $("#mwb_wgm_price").val();
	var error = false;
	var product_type = mwb_wgm.pricing_type.type;
	var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,5})+$/;
	var to_mail_name = '';
	var decimal_separator = mwb_wgm.decimal_separator;
	html = "<ul>";
	var pattern = /^[^a-z][0-9,./!@#$%^&*()]*$/;
	var isValid = false;
	isValid = pattern.test(price);
	if(isValid == false) {
		error = true;
		$("#mwb_wgm_price").addClass("mwb_wgm_error");
		html+="<li><b>";
		html+=mwb_wgm.invalid_price;
		html+="</li>";
	}

	if(price == null || price == "")
	{
		error = true;
		$("#mwb_wgm_price").addClass("mwb_wgm_error");
		html+="<li><b>";
		html+=mwb_wgm.price_field;
		html+="</li>";
	}
	if(mwb_wgm.schedule_date != "" && mwb_wgm.schedule_date == "on")
	{
		var send_date = $("#mwb_wgm_send_date").val();
		if(send_date == null || send_date == "")
		{
			error = true;
			$("#mwb_wgm_send_date").addClass("mwb_wgm_error");
			html+="<li><b>";
			html+=mwb_wgm.send_date;
			html+="</li>";
		}
	}
	if(mwb_wgm.remove_validation_to == 'off')
	{
		if(mwb_wgm_method_enable == "normal_mail")
		{	
			var to_mail_name = $("#mwb_wgm_to_name_optional").val();
			var to_mail = $("#mwb_wgm_to_email").val();
			if(to_mail == null || to_mail == "")
			{
				error = true;
				$("#mwb_wgm_to_email").addClass("mwb_wgm_error");
				html+="<li><b>";
				html+=mwb_wgm.to_empty;
				html+="</li>";
			}
			else if(!to_mail.match(mailformat))
			{
				error = true;
				$("#mwb_wgm_to_email").addClass("mwb_wgm_error");
				html+="<li><b>";
				html+=mwb_wgm.to_invalid;
				html+="</li>";
			}

		}
		else if(mwb_wgm_method_enable == "download" || mwb_wgm_method_enable == "shipping")
		{	
			var to_mail = "";
			if( mwb_wgm_method_enable == "download" )
			{
				to_mail = $("#mwb_wgm_to_download").val();	
			}
			else if( mwb_wgm_method_enable == "shipping" )
			{
				to_mail = $("#mwb_wgm_to_ship").val();
			}
			to_mail = to_mail.trim();
			if(to_mail == null || to_mail == "")
			{
				error = true;
				$("#mwb_wgm_to_download").addClass("mwb_wgm_error");
				html+="<li><b>";
				html+=mwb_wgm.to_empty_name;
				html+="</li>";
			}
		}
		else if( mwb_wgm_method_enable == "customer_choose" )
		{

			if( mwb_wgm_customer_selection['Email_to_recipient']== '1' || overwrite_mail == 'yes')
			{
				if($('#to_mail').is(':checked'))
				{

					var to_mail_name = $("#mwb_wgm_to_name_optional").val();
					var to_mail = $("#mwb_wgm_to_email").val();

					if(to_mail == null || to_mail == "")
					{
						error = true;
						$("#mwb_wgm_to_email").addClass("mwb_wgm_error");
						html+="<li><b>";
						html+=mwb_wgm.to_empty;
						html+="</li>";
					}
					else if(!to_mail.match(mailformat))
					{
						error = true;
						$("#mwb_wgm_to_email").addClass("mwb_wgm_error");
						html+="<li><b>";
						html+=mwb_wgm.to_invalid;
						html+="</li>";
					}	
				}
			}	
		}
	}

		//validation for to name fields
		if(mwb_wgm.remove_validation_to_name == 'off')
		{
			if(mwb_wgm_method_enable == "normal_mail")
			{	
				var to_mail_name = $("#mwb_wgm_to_name_optional").val();

				if(to_mail_name == null || to_mail_name == "")
				{
					error = true;
					$("#mwb_wgm_to_name_optional").addClass("mwb_wgm_error");
					html+="<li><b>";
					html+=mwb_wgm.to_empty_name;
					html+="</li>";
				}
			}
			else if( mwb_wgm_method_enable == "customer_choose" )
			{
				if( mwb_wgm_customer_selection['Email_to_recipient']== '1' || overwrite_mail == 'yes') 
				{
					if($('#to_mail').is(':checked'))
					{
						var to_mail_name = $("#mwb_wgm_to_name_optional").val();

						if(to_mail_name == null || to_mail_name == "")
						{
							error = true;
							$("#mwb_wgm_to_name_optional").addClass("mwb_wgm_error");
							html+="<li><b>";
							html+=mwb_wgm.to_empty_name;
							html+="</li>";
						}
					}
				}
				if(mwb_wgm_customer_selection['Downloadable'] == '1' || overwrite_download == 'yes')
				{	
					if($('#mwb_wgm_send_giftcard_download').is(':checked'))
					{
						var to_mail = $("#mwb_wgm_to_download").val();
						to_mail = to_mail.trim();
						if(to_mail == null || to_mail == "")
						{
							error = true;
							$("#mwb_wgm_to_download").addClass("mwb_wgm_error");
							html+="<li><b>";
							html+='To: Name Field is empty.';
							html+="</li>";
						}
					}
				}	
				if( mwb_wgm_customer_selection['Shipping'] == '1' || overwrite_shipping == 'yes')
				{	
					if($('#mwb_wgm_send_giftcard_ship').is(':checked'))
					{
						var to_mail = $("#mwb_wgm_to_ship").val();
						to_mail = to_mail.trim();
						if(to_mail == null || to_mail == "")
						{
							error = true;
							$("#mwb_wgm_to_download").addClass("mwb_wgm_error");
							html+="<li><b>";
							html+='To: Name Field is empty.';
							html+="</li>";
						}
					}
				}	
			}
		}	

		if (!$("input[name='mwb_wgm_send_giftcard']:checked").val()) 
		{
			error = true;
			$(".mwb_wgm_send_giftcard").addClass("mwb_wgm_error");
			html+="<li><b>";
			html+=mwb_wgm.method_empty;
			html+="</li>";
		}
		if(mwb_wgm.remove_validation_msg == 'off')
		{
			if(message == null || message == "")
			{
				error = true;
				$("#mwb_wgm_message").addClass("mwb_wgm_error");
				html+="<li><b>";
				html+=mwb_wgm.msg_empty;
				html+="</li>";
			}
			else if( message.length > mwb_wgm.msg_length )
			{
				error = true;
				$("#mwb_wgm_message").addClass("mwb_wgm_error");
				html+="<li><b>";
				html+=mwb_wgm.msg_length_err;
				html+="</li>";
			}
		}
		if(mwb_wgm.remove_validation_from == 'off')
		{    
			if(from_mail == null || from_mail == "")
			{
				error = true;
				$("#mwb_wgm_from_name").addClass("mwb_wgm_error");
				html+="<li><b>";
				html+=mwb_wgm.from_empty;
				html+="</li>";
			}
		}
		if(product_type == "mwb_wgm_range_price")
		{
			var from = mwb_wgm.pricing_type.from;
			var to = mwb_wgm.pricing_type.to;
			from = parseFloat( from.replace( decimal_separator ,'.') );
			to = parseFloat( to.replace( decimal_separator ,'.') );
			price = parseFloat( price.replace( decimal_separator ,'.') );

			if(price > to || price < from)
			{
				error = true;
				$("#mwb_wgm_price").addClass("mwb_wgm_error");
				html+="<li><b>";
				html+=mwb_wgm.price_range;
				html+="</li>";
			} 	 
		}
		html += "</ul>";

		if(error)
		{
			$("#mwb_wgm_error_notice").html(html);
			$("#mwb_wgm_error_notice").show();
        	//MWB code for woodmart theme
        	$("#mwb_wgm_error_notice").removeClass('hidden-notice');
        	//MWB code for woodmart theme
        	jQuery('html, body').animate({
        		scrollTop: jQuery(".woocommerce-page").offset().top
        	}, 800);
        }
        else
        {	
        	var tempId = $(document).find('.mwb_wgm_pre_selected_temp').attr('id');
        	if(tempId !== undefined)
        	{
        		var product_id = mwb_wgm.product_id;
        		var to_mail_name = $("#mwb_wgm_to_name_optional").val();
        		var delivery_method = $("input[name='mwb_wgm_send_giftcard']:checked").val();
        		if(delivery_method == 'Mail to recipient')
        		{
        			to_mail = $("#mwb_wgm_to_email").val();
        			if(to_mail_name === '')
        			{
        				var to_option = to_mail;
        			}
        			else
        			{
        				var to_option = to_mail_name;
        			}
        		}
        		else if(delivery_method == 'Downloadable')
        		{
        			to_mail = $("#mwb_wgm_to_download").val();
        			var to_option = to_mail;
        		}
        		else if(delivery_method == 'Shipping')
        		{
        			to_mail = $("#mwb_wgm_to_ship").val();
        			var to_option = to_mail;
        		}

        		if(browse_enable == "on"){
        			var formData = new FormData();
        			formData.append('file', $('input[type=file]')[0].files[0]);
        			formData.append('action', 'mwb_wgm_preview_mail');
        			formData.append('price', price);
        			formData.append('to', to_option);
        			formData.append('from', from_mail);
        			formData.append('message', message);
        			formData.append('product_id', product_id);
        			formData.append('send_date', send_date);
        			formData.append('tempId', tempId);
        			$.ajax({
        				url: mwb_wgm.ajaxurl, 
        				type: "POST",  
        				data: formData,
        				processData: false,
        				contentType: false,
        				success: function(response) 
        				{
        					$("#mwg_wgm_preview_email").show();
        					tb_show("", response);
        				}
        			});
        		}
        		else
        		{	
        			var data = {
        				action:'mwb_wgm_preview_mail',
        				price:price,
        				to:to_option,
        				from:from_mail,
        				message:message,
        				product_id:product_id,
        				tempId:tempId,
        				send_date:send_date
        			};

        			$.ajax({
        				url: mwb_wgm.ajaxurl, 
        				type: "POST",  
        				data: data,
        				success: function(response) 
        				{
        					$("#mwg_wgm_preview_email").show();
        					tb_show("", response);
        				}
        			});
        		}
        	}
        	else
        	{	
        		var product_id = mwb_wgm.product_id;
        		var to_mail_name = $("#mwb_wgm_to_name_optional").val();
        		var delivery_method = $("input[name='mwb_wgm_send_giftcard']:checked").val();
        		if(delivery_method == 'Mail to recipient')
        		{
        			to_mail = $("#mwb_wgm_to_email").val();
        			if(to_mail_name === '')
        			{
        				var to_option = to_mail;
        			}
        			else
        			{
        				var to_option = to_mail_name;
        			}
        		}
        		else if(delivery_method == 'Downloadable')
        		{
        			to_mail = $("#mwb_wgm_to_download").val();
        			var to_option = to_mail;
        		}
        		else if(delivery_method == 'Shipping')
        		{
        			to_mail = $("#mwb_wgm_to_ship").val();
        			var to_option = to_mail;
        		}

        		if(browse_enable == "on"){
        			var formData = new FormData();
        			formData.append('file', $('input[type=file]')[0].files[0]);
        			formData.append('action', 'mwb_wgm_preview_mail');
        			formData.append('price', price);
        			formData.append('to', to_option);
        			formData.append('from', from_mail);
        			formData.append('message', message);
        			formData.append('product_id', product_id);
        			formData.append('send_date', send_date);

        			$.ajax({
        				url: mwb_wgm.ajaxurl, 
        				type: "POST",  
        				data: formData,
        				processData: false,
        				contentType: false,
        				success: function(response) 
        				{
        					$("#mwg_wgm_preview_email").show();
        					tb_show("", response);
        				}
        			});
        		}
        		else
        		{
        			var product_id = mwb_wgm.product_id;
        			var data = {
        				action:'mwb_wgm_preview_mail',
        				price:price,
        				to:to_option,
        				from:from_mail,
        				message:message,
        				product_id:product_id,
        				send_date:send_date
        			};
        			$.ajax({
        				url: mwb_wgm.ajaxurl, 
        				type: "POST",  
        				data: data,
        				success: function(response) 
        				{
        					$("#mwg_wgm_preview_email").show();
        					tb_show("", response);
        				}
        			});
        		}
        	}
        }	
    });
$( '#mwg_wgm_email_format_popup_thickbox' ).click( function() {
	$("#mwg_wgm_preview_email").show();
	$("#mwg_wgm_email_format_popup_thickbox").hide();
});
var radio_on_load = $("input[name='mwb_wgm_send_giftcard']:checked").val();
mwb_wgm_check_which_radio_has_been_selected(radio_on_load);
function mwb_wgm_check_which_radio_has_been_selected(radioVal){
	if(radioVal == "Mail to recipient"){
		$("#mwb_wgm_to_download").val("");
		$("#mwb_wgm_to_ship").val("");
		$(".mwb_wgm_delivery_via_admin").hide();
		$(".mwb_wgm_delivery_via_email").show();
		$(".mwb_wgm_delivery_via_buyer").hide();
		$("#mwb_wgm_to_email").attr("readonly", false);
		$("#mwb_wgm_to_name_optional").attr("readonly", false);


	}
	else if( radioVal == "Downloadable" ){
		$("#mwb_wgm_to_email").val("");
		$("#mwb_wgm_to_ship").val("");
		$("#mwb_wgm_to_name_optional").val("");
		$(".mwb_wgm_delivery_via_admin").hide();
		$(".mwb_wgm_delivery_via_email").hide();
		$(".mwb_wgm_delivery_via_buyer").show();
		$("#mwb_wgm_to_download").attr("readonly", false); 
	}
	else if( radioVal == "Shipping" ){

		$("#mwb_wgm_to_email").val("");
		$("#mwb_wgm_to_download").val("");
		$("#mwb_wgm_to_name_optional").val("");
		$("#mwb_wgm_to_ship").attr("readonly", false);
		$(".mwb_wgm_delivery_via_admin").show();
		$(".mwb_wgm_delivery_via_email").hide();
		$(".mwb_wgm_delivery_via_buyer").hide();
	}
}
$( '.mwb_wgm_send_giftcard' ).change( function(){
	var radioVal = $(this).val();
	mwb_wgm_check_which_radio_has_been_selected(radioVal);

} );
$(document).on('change','#mwb_wgm_price',function(){
	var mwb_wgm_price = $(this).val();
	var product_id = mwb_wgm.product_id;
	var mwb_wgm_discount = mwb_wgm.mwb_wgm_discount;
	var mwb_wgm_discount_enable = mwb_wgm.mwb_wgm_discount_enable;
	var html = '';
	var new_price = '';
	$(document).find('.mwb_wgm_price_content').remove();
	if(mwb_wgm_discount == 'yes' && mwb_wgm_discount_enable == 'on')
	{	
		block($('.summary.entry-summary'));
		var data = {
			action:'mwb_wgm_append_prices',
			mwb_wgm_price:mwb_wgm_price,
			product_id:product_id,
			mwb_nonce:mwb_wgm.mwb_wgm_nonce
		};
		$.ajax({
			url: mwb_wgm.ajaxurl, 
			type: "POST",  
			data: data,
			dataType: 'json',
			success: function(response) 
			{
	  				if(response.result == true)
	  				{
	  					var new_price = response.new_price;
	  					var mwb_wgm_price = response.mwb_wgm_price;
	  					var html = '';
	  					html+='<div class="mwb_wgm_price_content"><b style="color:green;">'+mwb_wgm.discount_price_message+'</b>';
	  					html+= '<b style="color:green;">'+new_price+'</b><br/>';
	  					html+='<b style="color:green;">'+mwb_wgm.coupon_message+'</b>';
	  					html+= '<b style="color:green;">'+mwb_wgm_price+'</b></div>';
	  				} 

	  				$(html).insertAfter($('p.price'));

	  			},
	  			complete: function() 
	  			{
	  				unblock( $( '.summary.entry-summary' ) );
	  			}
	  		});
	}
});	
});

var block = function( $node ) {
	if ( ! is_blocked( $node ) ) {
		$node.addClass( 'processing' ).block( {
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		} );
	}
};
var is_blocked = function( $node ) {
	return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
};
var unblock = function( $node ) {
	$node.removeClass( 'processing' ).unblock();
};
