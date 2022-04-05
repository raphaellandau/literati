jQuery(document).ready(function($){
	$("#mwb_wgm_product_setting_exclude_category").select2();
  $("#mwb_wgm_include_categories").select2();
	 $('#mwb_wgm_mailcolor').wpColorPicker();
	
	var imageurl = $("#mwb_wgm_other_setting_upload_logo").val();
	if(imageurl != null && imageurl != "")
	{	
		$("#mwb_wgm_other_setting_upload_image").attr("src",imageurl);
	    $("#mwb_wgm_other_setting_remove_logo").show();
	    
	}
    var imageurl = $("#mwb_wgm_other_setting_background_logo_value").val();
	if(imageurl != null && imageurl != "")
	{
    	$("#mwb_wgm_other_setting_background_logo_image").attr("src",imageurl);
    	$("#mwb_wgm_other_setting_remove_background").show();
	}   
    
  jQuery('.mwb_wgm_other_setting_upload_logo').click(function(){
    var imageurl = $("#mwb_wgm_other_setting_upload_logo").val();

        tb_show('', 'media-upload.php?TB_iframe=true');

        window.send_to_editor = function(html)
        {
           var imageurl = jQuery(html).attr('href');
          
           if(typeof imageurl == 'undefined')
           {
             imageurl = jQuery(html).attr('src');
           }
           var last_index = imageurl.lastIndexOf('/');
            var url_last_part = imageurl.substr(last_index+1);
            if( url_last_part == '' ){
              
              imageurl = jQuery(html).children("img").attr("src");  
            }   
           $("#mwb_wgm_other_setting_upload_logo").val(imageurl);
           $("#mwb_wgm_other_setting_upload_image").attr("src",imageurl);
           $("#mwb_wgm_other_setting_remove_logo").show();
           tb_remove();
        };
        return false;
  });
	jQuery('.mwb_wgm_other_setting_background_logo').click(function(){
		var imageurl = $("#mwb_wgm_other_setting_background_logo_value").val();
        tb_show('', 'media-upload.php?TB_iframe=true');
        window.send_to_editor = function(html)
        {
           var imageurl = jQuery(html).attr('href');
           if(typeof imageurl == 'undefined')
           {
        	   imageurl = jQuery(html).attr('src');
           }	  
           $("#mwb_wgm_other_setting_background_logo_value").val(imageurl);
           $("#mwb_wgm_other_setting_background_logo_image").attr("src",imageurl);
           $("#mwb_wgm_other_setting_remove_background").show();
           tb_remove();
        };
        return false;
	});
  if($('#mwb_wgm_general_setting_downloable_enable').prop("checked") == true){
    $('.mwb_name_field').show();
  }
  $('#mwb_wgm_general_setting_downloable_enable').change(function(){
      
      if($(this).prop("checked") == true){
        $('.mwb_name_field').show();
      }
      else{
        $('.mwb_name_field').hide();
      }
  });
	
	jQuery( document.body ).trigger( 'init_tooltips' );
	
	jQuery(".mwb_wgm_other_setting_remove_logo_span").click(function(){
		jQuery("#mwb_wgm_other_setting_remove_logo").hide();
		jQuery("#mwb_wgm_other_setting_upload_logo").val("");
	});
	
	jQuery(".mwb_wgm_other_setting_remove_background_span").click(function(){
		jQuery("#mwb_wgm_other_setting_remove_background").hide();
		jQuery("#mwb_wgm_other_setting_background_logo_value").val("");
		
	});
	
	jQuery("#mwb_wgm_manage_template").click(function(){
		jQuery("#mwb_wgm_manage_template_wrapper").slideToggle();
	});
	
	jQuery("#mwb_wgm_mail_setting").click(function(){
		jQuery("#mwb_wgm_mail_setting_wrapper").slideToggle();
	});

  jQuery("#mwb_wgm_coupon_mail_setting").click(function(){
    jQuery("#mwb_wgm_coupon_mail_setting_wrapper").slideToggle();
  });
	
	jQuery("#mwb_wgm_general_setting_giftcard_payment").select2();
	
	jQuery("#mwb_wgm_offline_gift_preview").click(function(){
		var error = true;
		var to_mail = jQuery("#mwb_wgm_offline_gift_to").val().trim();
		var from_mail = jQuery("#mwb_wgm_offline_gift_from").val().trim();
		var price = jQuery("#mwb_wgm_offline_gift_amount").val().trim();
		var message = jQuery("#mwb_wgm_offline_gift_message").val().trim();
		var product_id = jQuery("#mwb_wgm_offline_gift_template").val();
    var gift_manual_code = jQuery("#mwb_wgm_offline_gift_coupon_manual").val();
		
		
		if(price == null || price == "")
    {
    	error = false;
    	jQuery("#mwb_wgm_offline_gift_amount").addClass("mwb_wgm_error");
    }
		else
		{
			jQuery("#mwb_wgm_offline_gift_amount").removeClass("mwb_wgm_error");
		}	
        
        if(to_mail == null || to_mail == "")
        {
        	error = false;
        	jQuery("#mwb_wgm_offline_gift_to").addClass("mwb_wgm_error");
        }
        else
    		{
    			jQuery("#mwb_wgm_offline_gift_to").removeClass("mwb_wgm_error");
    		}
        if(from_mail == null || from_mail == "")
        {
        	error = false;
        	jQuery("#mwb_wgm_offline_gift_from").addClass("mwb_wgm_error");
        }
        else
    		{
    			jQuery("#mwb_wgm_offline_gift_from").removeClass("mwb_wgm_error");
    		}
        if(message == null || message == "")
        {
        	error = false;
        	jQuery("#mwb_wgm_offline_gift_message").addClass("mwb_wgm_error");
        	
        }
        else
        {
        	jQuery("#mwb_wgm_offline_gift_message").removeClass("mwb_wgm_error");
        }	
        
        if(product_id == null || product_id == "")
        {
        	error = false;
        	jQuery("#mwb_wgm_offline_gift_template").addClass("mwb_wgm_error");
        	
        }
        else
        {
        	jQuery("#mwb_wgm_offline_gift_template").removeClass("mwb_wgm_error");
        }	
        var send_date = $("#mwb_wgm_offline_gift_schedule").val();
        if(error)
        {
        	var data = {
				      action:'mwb_wgm_preview_mail',
  					  price:price,
  					  to:to_mail,
  					  from:from_mail,
  					  message:message,
  					  product_id:product_id,
              send_date:send_date,
              gift_manual_code:gift_manual_code
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
        
		
	});
	
	jQuery(".mwb_wgm_offline_resend_mail").click(function(){
		
		jQuery("#mwb_wgm_loader").show();
		var id = jQuery(this).data("id");
		var current = jQuery(this);
		var data = {
			      action:'mwb_wgm_offline_resend_mail',
			      id:id,
            mwb_nonce:mwb_wgm.mwb_wgm_nonce
				};
		$.ajax({
  			url: mwb_wgm.ajaxurl, 
  			type: "POST",  
  			data: data,
        dataType: 'json',
  			success: function(response) 
  			{
  				jQuery("#mwb_wgm_loader").hide();
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
          current.next().html(html);

  			}
  		});
	});
	jQuery("#mwb_wgm_offline_gift_save").click(function(e){
		
		var error = true;
		var to_mail = jQuery("#mwb_wgm_offline_gift_to").val();
		var from_mail = jQuery("#mwb_wgm_offline_gift_from").val();
		var price = jQuery("#mwb_wgm_offline_gift_amount").val();
		var message = jQuery("#mwb_wgm_offline_gift_message").val();
		var product_id = jQuery("#mwb_wgm_offline_gift_template").val();
		if(price == null || price == ""){
      	error = false;
      	jQuery("#mwb_wgm_offline_gift_amount").addClass("mwb_wgm_error");
    }
		else{
			jQuery("#mwb_wgm_offline_gift_amount").removeClass("mwb_wgm_error");
		}	
    if(to_mail == null || to_mail == ""){
    	error = false;
    	jQuery("#mwb_wgm_offline_gift_to").addClass("mwb_wgm_error");
    }
    else{
			jQuery("#mwb_wgm_offline_gift_to").removeClass("mwb_wgm_error");
		}
    if(from_mail == null || from_mail == ""){
    	error = false;
    	jQuery("#mwb_wgm_offline_gift_from").addClass("mwb_wgm_error");
    }
    else{
			jQuery("#mwb_wgm_offline_gift_from").removeClass("mwb_wgm_error");
		}
    if(message == null || message == ""){
    	error = false;
    	jQuery("#mwb_wgm_offline_gift_message").addClass("mwb_wgm_error");
    	
    }
    else
    {
    	jQuery("#mwb_wgm_offline_gift_message").removeClass("mwb_wgm_error");
    }	   
    if(product_id == null || product_id == ""){
    	error = false;
    	jQuery("#mwb_wgm_offline_gift_template").addClass("mwb_wgm_error");
    	
    }
    else{
    	jQuery("#mwb_wgm_offline_gift_template").removeClass("mwb_wgm_error");
    }
    /*if(mwb_manual_code !== null){

    }*/
    if(!error){
    	e.preventDefault();
    }	
    });
  $('#mwb_wgm_offline_gift_schedule').datepicker({
     dateFormat : mwb_wgm.dateformat,
      minDate: 0
  }).datepicker("setDate", "0");
  /*$('#TB_closeWindowButton').click(function(){

  });*/
 
 $("#TB_closeWindowButton").click(function() {
    alert();
});
  jQuery('#mwb_wgm_offline_gift_coupon_manual').on('change',function(){
   var mwb_manual_code = jQuery("#mwb_wgm_offline_gift_coupon_manual").val();
   var html_err = '<span style="color:red;">Gift Coupon Code already exist! Try another</span>';
   var html_succ = '<span style="color:green;">Valid Code</span>';
   if(mwb_manual_code !== null){
     jQuery.ajax({
           url:mwb_wgm.ajaxurl,
           type:"POST",
           dataType :'json',
           data:{
             action:'mwb_wgm_check_manual_code_exist',
             mwb_manual_code:mwb_manual_code
           },success : function(response){
             if(response.result == 'invalid'){
               $("#mwb_wgm_invalid_code_notice").html(html_err);
             }
             else if(response.result == 'valid'){
               $("#mwb_wgm_invalid_code_notice").html(html_succ);
             }
           }
         });
   }
 });

  jQuery('#mwb_wgm_pdf_deprecated').on('click',function(){
    var html = '';
    jQuery("#mwb_wgm_loader").show();
    jQuery.ajax({
           url:mwb_wgm.ajaxurl,
           type:"POST",
           dataType :'json',
           data:{
             action:'mwb_wgm_new_way_for_generating_pdfs',
             'mwb_wgm_new_way_for_pdf':'yes'
           },success : function(response){
              jQuery("#mwb_wgm_loader").hide();
             if(response.result == true){
              html = '<td></td><td><input type="button" name="mwb_wgm_pdf_deprecated_next_step" class="mwb_wgm_pdf_deprecated_next_step" id="mwb_wgm_pdf_deprecated_next_step" value="Next Step"></td>';
              $(".mwb_wgm_pdf_deprecated_row").html(html);
             }
             else if(response.result == false){
              var message = response.message;
              message =+ '<b style="color:red;">'+message+'</b>';
              $(".mwb_wgm_pdf_deprecated_row").html(message);
             }
           }
         });
  });
  jQuery(document).on('click','#mwb_wgm_pdf_deprecated_next_step',function(){
    jQuery("#mwb_wgm_loader").show();
    jQuery.ajax({
           url:mwb_wgm.ajaxurl,
           type:"POST",
           dataType :'json',
           data:{
             action:'mwb_wgm_next_step_for_generating_pdfs',
             'mwb_wgm_next_step_for_pdf':'yes'
           },success : function(response){
            jQuery("#mwb_wgm_loader").hide();
             if(response.result == true){
              var message = response.message;
              var append_message = '<th></th><td><b style="color:green;">'+message+'</b></td>';
              $(".mwb_wgm_pdf_deprecated_row").html(append_message);
             }
             else if(response.result == false){
                var message = response.message;
                var append_message = '<th></th><td><b style="color:red;">'+message+'</b></td>';
                $(".mwb_wgm_pdf_deprecated_row").html(append_message);
             }
           }
         });
  });

  /////////////// License Activation ////////////////
  $('#mwb_wgm_license_save').on('click',function(){
    $('.licennse_notification').html('');
    var mwb_license = $('#mwb_wgm_license_key').val();
    if( mwb_license == '' )
    {
      $('#mwb_wgm_license_key').css('border','1px solid red');
      return false;
    }
    else
    {
      $('#mwb_wgm_license_key').css('border','none');
    }
    $('.loading_image').show();
    $.ajax({
      url: mwb_wgm.ajaxurl, 
      type: "POST",  
      dataType: 'json',
      data:{
      'action':'mwb_wgm_register_license',
      'mwb_nonce':mwb_wgm.mwb_wgm_nonce,
      'license_key':mwb_license
      },success: function(response) 
      {
        if( response.msg == '' ){
          response.msg = 'Something Went Wrong! Please try again';
        }
        $('.loading_image').hide();
        console.log(response);
        if(response.status == true )
        {
          $('.licennse_notification').css('color','green');
          $('.licennse_notification').html(response.msg);
          window.location.href = mwb_wgm.mwb_wgm_url;
        }
        else
        { 
          $('.licennse_notification').css('color','red');
          $('.licennse_notification').html(response.msg);
        }
      }
    });
  });
  ////////////////End License authentication///////////////

  ////////   ADDON SUGGESTION FORM  /////////////////

      $(document).on('click','.mwb_wgm_addon_suggestion',function(){
        $(".mwb_suggestion_form").css('display','block');
      });
      $(document).on('click','.mwb_wgm_close_modal',function(){
        $(".mwb_suggestion_form").css('display','none');
      });
  ///////  END OF ADDON SUGGESTION FORM ////////

      $(document).on('click','.generate_link',function(){
    $('.mwb_redeem_registraion_div').show();
  });
  $(document).on('click','.mwb-redeem-pop-close',function(){
    $('.mwb_redeem_registraion_div').hide();
  });

  $(document).on('click', '.remove_giftcard_redeem_details' , function (e) {
   
    var res = confirm("Are you sure ! want to delete the account details  ");
    if (res == true) {
      $(document).find('#mainform').submit();
    } else {
      return false;
    } 
  });

  
    var clipboard = new Clipboard('.mwb_link_copy');
    



     var clipboard = new Clipboard('.mwb_embeded_copy');
    // document.execCommand("copy");
    $(document).on('click', 'button', function(event) {
      event.preventDefault();
      
    });



});
