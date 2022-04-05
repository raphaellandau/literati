(function( $ ) {
	'use strict';

	$(document).ready(function() {

		if($('#mwb_wgm_thankyouorder_enable').prop("checked") == true){
			$('.mwb_wgm_thankyouorder_row').show();
		}	
		if($('.mwb_wgm_thankyouorder_tbody > tr').length == 2){
			$( '.mwb_wgm_remove_thankyouorder_content' ).each( function() {
				$(this).hide();
			});
		}

		$(document).on('click','#mwb_wgm_thankyouorder_setting_save',function(event)
		{

			event.preventDefault();
			var response = check_validation_setting();
			
			if( response != undefined){
				
				$(this).closest("form#mainform" ).submit();
			}
		});

		$(document).on('click','.mwb_wgm_remove_thankyouorder',function()
		{

			if($('#mwb_wgm_thankyouorder_enable').prop("checked") == true)
			{
				$(this).closest('tr').remove();
				var tbody_length = $('.mwb_wgm_thankyouorder_tbody > tr').length;
				
				if( tbody_length == 2 ){
					$( '.mwb_wgm_remove_thankyouorder_content' ).each( function() {
						$(this).hide();
					});
				}
			}
		});

		$(document).on('change','#mwb_wgm_thankyouorder_enable',function()
		{
			if($(this).prop("checked") == true)
			{			
				$('.mwb_wgm_thankyouorder_row').show();
			}
			else
			{
				$('.mwb_wgm_thankyouorder_row').hide();
			}
		});	

		$(document).on('click','#mwb_wgm_add_more',function()
		{
			if($('#mwb_wgm_thankyouorder_enable').prop("checked") == true)
			{
				var response = check_validation_setting();
				if( response == true)
				{
					var tbody_length = $('.mwb_wgm_thankyouorder_tbody > tr').length;
					var new_row = '<tr valign="top"><td class="forminp forminp-text"><label for="mwb_wgm_thankyouorder_minimum"><input type="text" name="mwb_wgm_thankyouorder_minimum[]" class="mwb_wgm_thankyouorder_minimum input-text wc_input_price" required=""></label></td><td class="forminp forminp-text"><label for="mwb_wgm_thankyouorder_maximum"><input type="text" name="mwb_wgm_thankyouorder_maximum[]" class="mwb_wgm_thankyouorder_maximum input-text wc_input_price" required=""></label></td><td class="forminp forminp-text"><label for="mwb_wgm_thankyouorder_current_type"><input type="text" name="mwb_wgm_thankyouorder_current_type[]" class="mwb_wgm_thankyouorder_current_type input-text wc_input_price" required=""></label></td><td class="mwb_wgm_remove_thankyouorder_content forminp forminp-text"><input type="button" value="Remove" class="mwb_wgm_remove_thankyouorder button" ></td></tr>';
					
					if( tbody_length == 2 )
					{
						$( '.mwb_wgm_remove_thankyouorder_content' ).each( function() {
							$(this).show();
						});
					}
					$('.mwb_wgm_thankyouorder_tbody').append(new_row);
				}			
			}
		});	
	});
	var check_validation_setting = function(){
		if($('#mwb_wgm_thankyouorder_enable').prop("checked") == true){
			var tbody_length = $('.mwb_wgm_thankyouorder_tbody > tr').length;
			var i = 1;
			var min_arr = []; var max_arr = [];
			var empty_warning = false;
			var is_lesser = false;
			var num_valid = false;
			$('.mwb_wgm_thankyouorder_minimum').each(function(){
				min_arr.push($(this).val());
							
			});
			var order_number = $('#mwb_wgm_thankyouorder_number').val();
			if(order_number.length > 0)
			{	
				if(jQuery.isNumeric(order_number))
				{
					if(order_number < 1)
					{
						is_lesser = true;
					}
				}
				else
				{
					num_valid = true;
				}
			}
			if(is_lesser)
			{
				$('.notice.notice-error.is-dismissible').each(function(){
					$(this).remove();
				});
				$('.notice.notice-success.is-dismissible').each(function(){
					$(this).remove();
				});
				
				$('html, body').animate({
			        scrollTop: $(".woocommerce_page_mwb-wgc-setting").offset().top
			    }, 800);
			    var num_message = '<div class="notice notice-error is-dismissible"><p><strong>Number Of Orders should be greater than 1!</strong></p></div>';
			    $(num_message).insertAfter($('h1.mwb_wgm_setting_title'));
				return;
			}
			if(num_valid)
			{
				$('.notice.notice-error.is-dismissible').each(function(){
					$(this).remove();
				});
				$('.notice.notice-success.is-dismissible').each(function(){
					$(this).remove();
				});
				
				$('html, body').animate({
			        scrollTop: $(".woocommerce_page_mwb-wgc-setting").offset().top
			    }, 800);
			    var num_message = '<div class="notice notice-error is-dismissible"><p><strong>Number Of Orders should be in numbers !</strong></p></div>';
			    $(num_message).insertAfter($('h1.mwb_wgm_setting_title'));
				return;
			}
			var i = 1;
			
			$('.mwb_wgm_thankyouorder_maximum').each(function(){
				max_arr.push($(this).val());
				
				i++;			
			});
			var i = 1;
			var thankyouorder_arr = [];
			$('.mwb_wgm_thankyouorder_current_type').each(function(){
				thankyouorder_arr.push($(this).val());
				
				if(!$(this).val()){				
					$('.mwb_wgm_thankyouorder_tbody > tr:nth-child('+(i+1)+') .mwb_wgm_thankyouorder_current_type').css("border-color", "red");
					empty_warning = true;
				}
				else {
					$('.mwb_wgm_thankyouorder_tbody > tr:nth-child('+(i+1)+') .mwb_wgm_thankyouorder_current_type').css("border-color", "");				
				}
				i++;			
			});
			if(empty_warning) {
				$('.notice.notice-error.is-dismissible').each(function(){
					$(this).remove();
				});
				$('.notice.notice-success.is-dismissible').each(function(){
					$(this).remove();
				});
				
				$('html, body').animate({
			        scrollTop: $(".woocommerce_page_mwb-wgc-setting").offset().top
			    }, 800);
			    var empty_message = '<div class="notice notice-error is-dismissible"><p><strong>Some Fields are empty!</strong></p></div>';
			    $(empty_message).insertAfter($('h1.mwb_wgm_setting_title'));
				return;
			}
			var minmaxcheck = false;
			if(max_arr.length >0 && min_arr.length > 0)
			{
				if( min_arr.length == max_arr.length && max_arr.length == thankyouorder_arr.length) {
				
					for ( var j = 0; j < min_arr.length; j++) {
						
						if(parseInt(min_arr[j]) > parseInt(max_arr[j])) {
							minmaxcheck = true;
							$('.mwb_wgm_thankyouorder_tbody > tr:nth-child('+(j+2)+') .mwb_wgm_thankyouorder_minimum').css("border-color", "red");
							$('.mwb_wgm_thankyouorder_tbody > tr:nth-child('+(j+2)+') .mwb_wgm_thankyouorder_minimum').css("border-color", "red");
						}
						else{
							$('.mwb_wgm_thankyouorder_tbody > tr:nth-child('+(j+2)+') .mwb_wgm_thankyouorder_minimum').css("border-color", "");
							$('.mwb_wgm_thankyouorder_tbody > tr:nth-child('+(j+2)+') .mwb_wgm_thankyouorder_minimum').css("border-color", "");
						}
					}
				}
				else{
					$('.notice.notice-error.is-dismissible').each(function(){
						$(this).remove();
					});
					$('.notice.notice-success.is-dismissible').each(function(){
						$(this).remove();
					});
					
					$('html, body').animate({
				        scrollTop: $(".woocommerce_page_mwb-wgc-setting").offset().top
				    }, 800);
				    var empty_message = '<div class="notice notice-error is-dismissible"><p><strong>Some Fields are empty!</strong></p></div>';
				    $(empty_message).insertAfter($('h1.mwb_wgm_setting_title'));
					return;
				}
			}
			if(minmaxcheck) {
				$('.notice.notice-error.is-dismissible').each(function(){
					$(this).remove();
				});
				$('.notice.notice-success.is-dismissible').each(function(){
					$(this).remove();
				});
				
				$('html, body').animate({
			        scrollTop: $(".woocommerce_page_mwb-wgc-setting").offset().top
			    }, 800);
			    var empty_message = '<div class="notice notice-error is-dismissible"><p><strong>Minimum value cannot have value grater than Maximim value.</strong></p></div>';
			    $(empty_message).insertAfter($('h1.mwb_wgm_setting_title'));
				return;
			}
			return true;
		}
		else {
			return false;
		}
	};

})( jQuery );