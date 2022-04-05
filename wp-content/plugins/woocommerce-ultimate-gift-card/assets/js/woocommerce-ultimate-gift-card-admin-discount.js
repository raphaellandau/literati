(function( $ ) {
	'use strict';

	$(document).ready(function() {
		
		if($('#mwb_wgm_discount_enable').prop("checked") == true){
			$('.mwb_wgm_discount_row').show();
		}	
		if($('.mwb_wgm_discount_tbody > tr').length == 2){
			$( '.mwb_wgm_remove_discount_content' ).each( function() {
				$(this).hide();
			});
		}

		$(document).on('click','#mwb_wgm_discount_setting_save',function(event)
		{

			event.preventDefault();
			var response = check_validation_setting();
			
			if( response != undefined){
				
				$(this).closest("form#mainform" ).submit();
			}
		});

		$(document).on('click','.mwb_wgm_remove_discount',function()
		{

			if($('#mwb_wgm_discount_enable').prop("checked") == true)
			{
				$(this).closest('tr').remove();
				var tbody_length = $('.mwb_wgm_discount_tbody > tr').length;
				
				if( tbody_length == 2 ){
					$( '.mwb_wgm_remove_discount_content' ).each( function() {
						$(this).hide();
					});
				}
			}
		});

		$(document).on('change','#mwb_wgm_discount_enable',function()
		{
			if($(this).prop("checked") == true)
			{			
				$('.mwb_wgm_discount_row').show();
			}
			else
			{
				$('.mwb_wgm_discount_row').hide();
			}
		});	

		$(document).on('click','#mwb_wgm_add_more',function()
		{
			if($('#mwb_wgm_discount_enable').prop("checked") == true)
			{
				var response = check_validation_setting();
				if( response == true)
				{
					var tbody_length = $('.mwb_wgm_discount_tbody > tr').length;
					var new_row = '<tr valign="top"><td class="forminp forminp-text"><label for="mwb_wgm_discount_minimum"><input type="text" name="mwb_wgm_discount_minimum[]" class="mwb_wgm_discount_minimum input-text wc_input_price" required=""></label></td><td class="forminp forminp-text"><label for="mwb_wgm_discount_maximum"><input type="text" name="mwb_wgm_discount_maximum[]" class="mwb_wgm_discount_maximum input-text wc_input_price" required=""></label></td><td class="forminp forminp-text"><label for="mwb_wgm_discount_current_type"><input type="text" name="mwb_wgm_discount_current_type[]" class="mwb_wgm_discount_current_type input-text wc_input_price" required=""></label></td><td class="mwb_wgm_remove_discount_content forminp forminp-text"><input type="button" value="Remove" class="mwb_wgm_remove_discount button" ></td></tr>';
					
					if( tbody_length == 2 )
					{
						$( '.mwb_wgm_remove_discount_content' ).each( function() {
							$(this).show();
						});
					}
					$('.mwb_wgm_discount_tbody').append(new_row);
				}			
			}
		});	
	});
	var check_validation_setting = function(){

		if($('#mwb_wgm_discount_enable').prop("checked") == true){
			var tbody_length = $('.mwb_wgm_discount_tbody > tr').length;
			var i = 1;
			var min_arr = [];
			var empty_warning = false;
			$('.mwb_wgm_discount_minimum').each(function(){
				min_arr.push($(this).val());
				
				if(!$(this).val()){				
					$('.mwb_wgm_discount_tbody > tr:nth-child('+(i+1)+') .mwb_wgm_discount_minimum').css("border-color", "red");
					empty_warning = true;
				}
				else{				
					$('.mwb_wgm_discount_tbody > tr:nth-child('+(i+1)+') .mwb_wgm_discount_minimum').css("border-color", "");
				}
				i++;			
			});
			
			var i = 1;
			var max_arr = [];
			$('.mwb_wgm_discount_maximum').each(function(){
				max_arr.push($(this).val());
				
				if(!$(this).val()){				
					$('.mwb_wgm_discount_tbody > tr:nth-child('+(i+1)+') .mwb_wgm_discount_maximum').css("border-color", "red");
					empty_warning = true;
				}
				else {
					$('.mwb_wgm_discount_tbody > tr:nth-child('+(i+1)+') .mwb_wgm_discount_maximum').css("border-color", "");				
				}
				i++;			
			});
			var i = 1;
			var discount_arr = [];
			$('.mwb_wgm_discount_current_type').each(function(){
				discount_arr.push($(this).val());
				
				if(!$(this).val()){				
					$('.mwb_wgm_discount_tbody > tr:nth-child('+(i+1)+') .mwb_wgm_discount_current_type').css("border-color", "red");
					empty_warning = true;
				}
				else {
					$('.mwb_wgm_discount_tbody > tr:nth-child('+(i+1)+') .mwb_wgm_discount_current_type').css("border-color", "");				
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
			if( min_arr.length == max_arr.length && max_arr.length == discount_arr.length) {
				
				for ( var j = 0; j < min_arr.length; j++) {
					
					if(parseInt(min_arr[j]) > parseInt(max_arr[j])) {
						minmaxcheck = true;
						$('.mwb_wgm_discount_tbody > tr:nth-child('+(j+2)+') .mwb_wgm_discount_minimum').css("border-color", "red");
						$('.mwb_wgm_discount_tbody > tr:nth-child('+(j+2)+') .mwb_wgm_discount_minimum').css("border-color", "red");
					}
					else{
						$('.mwb_wgm_discount_tbody > tr:nth-child('+(j+2)+') .mwb_wgm_discount_minimum').css("border-color", "");
						$('.mwb_wgm_discount_tbody > tr:nth-child('+(j+2)+') .mwb_wgm_discount_minimum').css("border-color", "");
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