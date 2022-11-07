jQuery(document).ready(function( $ ){
  
/*
  $(".hidden-in-gravity").each(function() {
    $('#new_password').attr("autocomplete", "off");
    setTimeout(function(){
      $("#new_password").val("").focus(); }, 2000);
  });
  
  */
  
$("select.price-change").on("change", function() {
    var productLink = $(this).find("option:selected").attr("product-link");
    var priceOne = $(this).find("option:selected").attr("priceone");
    var priceTwo = $(this).find("option:selected").attr("pricetwo");
    $(this).parent().parent().parent().find(".price-one h2").text(priceOne);
    $(this).parent().parent().parent().find(".price-two h2").text(priceTwo);
    $(this).parent().parent().parent().find(".giftit a").attr("href", productLink);
});
  
$('.books-carousel').slick({
    slidesToShow: 7,
    slidesToScroll: 1,
    dots: false,
    centerMode: true,
    arrows: true,
    autoplay: true,
    rtl: true,
    autoplaySpeed: 3000,
    responsive: [
    {
        breakpoint: 767,
        settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
  ]
  });
  
  
$('.testies-carousel').slick({
  slidesToShow: 4,
  slidesToScroll: 4,
    dots: false,
    centerMode: true,
    arrows: true,
  autoplay: true,
    rtl: true,
  autoplaySpeed: 5500,
   responsive: [
    {
      breakpoint: 2200,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 3,
      },
    },
     {
      breakpoint: 1350,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2,
      },
    },
    {
      breakpoint: 950,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
      },
    },
  ],
});
  
  
  
  $(".more-trigger").on("click", function() {
    $(".more-thoughts").toggle();
    $(this).text(function(i, v){
  return v === 'סגירה' ? 'קראו עוד...' : 'סגירה'
  })
  });
  
  
    if ($("body").hasClass("logged-in")) {
     $(".personal a").attr("href", "https://literati.co.il/logged-in-user/"); 
      $(".reciever a").each(function() {
        $(this).attr("href", "https://literati.co.il/logged-in-giftcard/"); 
      });
    } else {
      $(".logoutlink").hide();
    }
  
  $("#gform_submit_button_3").on("click", function() {
    var firstName = $("#input_3_2_3").val();
    $("#first_name_field #first_name").val(firstName);
  
  	var lastName = $("#input_3_2_6").val();
    $("#last_name_field #last_name").val(lastName);
  
    $("#shipping_country").val("IL");
  
  	var userAddress = $("#input_3_39_1").val();
  	$("#shipping_address_1").val(userAddress);
  
  	var userPostal = $("#input_3_39_5").val();
  	$("#shipping_postcode").val(userPostal);
  
  	var userCity = $("#input_3_39_3").val();
  	$("#shipping_city").val(userCity);
    
    $("input[name='save_address']").trigger("click");
  });
  
	var firstName = $("#input_3_2_3").val();
    $("#first_name_field #first_name").val(firstName);
  
  	var lastName = $("#input_3_2_6").val();
    $("#last_name_field #last_name").val(lastName);
  
    $("#shipping_country").val("IL");
  
  	var userAddress = $("#input_3_39_1").val();
  	$("#shipping_address_1").val(userAddress);
  
  	var userPostal = $("#input_3_39_5").val();
  	$("#shipping_postcode").val(userPostal);
  
  	var userCity = $("#input_3_39_3").val();
  	$("#shipping_city").val(userCity);
  
  jQuery( document.body ).on( 'updated_checkout', function(){
    
    if(window.location.href.indexOf("&is=giftcard") > -1) {
     $(".woocommerce-form-coupon-toggle").addClass("hide-stuff");
     $(".woocommerce-form-coupon").addClass("show-stuff");
      if($('#billing_address_1').val() &&
         $('#billing_postcode').val() &&
         $('#billing_city').val() &&
         $('#billing_phone').val()
        ) {
      $("#customer_details").addClass("hide-stuff");
      }
      $("#order_review_heading").addClass("hide-stuff");
      $("#order_review table").addClass("hide-stuff");
  }
  
  $("p#ddwcsc_receiver_name_field").removeClass('validate-required');
    $("p#ddwcsc_receiver_email_field").removeClass('validate-required');
    $("#ddwcsc-gift-fields-wrapper").hide();
  
  if(window.location.href.indexOf("&for=me") > -1) {
    $("p#ddwcsc_receiver_name_field").removeClass('validate-required');
    $("p#ddwcsc_receiver_email_field").removeClass('validate-required');
    $("#ddwcsc-gift-fields-wrapper").hide();
    $(".woocommerce-shipping-fields").show();
    $(".woocommerce-additional-fields__field-wrapper").show();
  }
    else if(window.location.href.indexOf("&for=other") > -1) {
      $("#ddwcsc-gift-fields-wrapper").show();
      $("p#ddwcsc_receiver_name_field").addClass('validate-required');
      $("p#ddwcsc_receiver_email_field").addClass('validate-required');
      $(".woocommerce-shipping-fields").hide();
      $(".woocommerce-additional-fields__field-wrapper").hide();
    }
    
  });
  
  $("p#ddwcsc_receiver_name_field").removeClass('validate-required');
    $("p#ddwcsc_receiver_email_field").removeClass('validate-required');
    $("#ddwcsc-gift-fields-wrapper").hide();
  
  if(window.location.href.indexOf("&for=me") > -1) {
    $("p#ddwcsc_receiver_name_field").removeClass('validate-required');
    $("p#ddwcsc_receiver_email_field").removeClass('validate-required');
    $("#ddwcsc-gift-fields-wrapper").hide();
    $(".woocommerce-shipping-fields").show();
    $(".woocommerce-additional-fields__field-wrapper").show();
  }
    else if(window.location.href.indexOf("&for=other") > -1) {
      $("#ddwcsc-gift-fields-wrapper").show();
      $("p#ddwcsc_receiver_name_field").addClass('validate-required');
      $("p#ddwcsc_receiver_email_field").addClass('validate-required');
      $(".woocommerce-shipping-fields").hide();
      $(".woocommerce-additional-fields__field-wrapper").hide();
    }
  
  $( document.body ).on( 'updated_cart_totals', function(){
   
    $("p#ddwcsc_receiver_name_field").removeClass('validate-required');
    $("p#ddwcsc_receiver_email_field").removeClass('validate-required');
    $("#ddwcsc-gift-fields-wrapper").hide();
  
  if(window.location.href.indexOf("&for=me") > -1) {
    $("p#ddwcsc_receiver_name_field").removeClass('validate-required');
    $("p#ddwcsc_receiver_email_field").removeClass('validate-required');
    $("#ddwcsc-gift-fields-wrapper").hide();
    $(".woocommerce-shipping-fields").show();
    $(".woocommerce-additional-fields__field-wrapper").show();
  }
    else if(window.location.href.indexOf("&for=other") > -1) {
      $("#ddwcsc-gift-fields-wrapper").show();
      $("p#ddwcsc_receiver_name_field").addClass('validate-required');
      $("p#ddwcsc_receiver_email_field").addClass('validate-required');
      $(".woocommerce-shipping-fields").hide();
      $(".woocommerce-additional-fields__field-wrapper").hide();
    }
    
});
    
jQuery(document).on('gform_page_loaded', function(event, formId, stepId){
    changeSteps();  

    if($('body').hasClass('page-on-boarding') || $('body').hasClass('page-logged-in-user') || $('body').hasClass('page-edit-onboarding')) {
        if(stepId > 3 && stepId < 8) {
            jQuery('#gf_step_' + formId + '_3').addClass('gf_step gf_step_active');
        }
    }

    if($('body').hasClass('page-gift-card-receiver') || $('body').hasClass('page-logged-in-giftcard')) {
        if(stepId > 5 && stepId < 10) {
            jQuery('#gf_step_' + formId + '_5').addClass('gf_step gf_step_active');
        }
    }

  $(".choices .fa-heart").click(function() {
        $(".gform_wrapper .books-question input[type='checkbox']:not(:checked)").parent().find("label").removeClass("liked");
        $(".gform_wrapper .books-question input[type='checkbox']:checked").parent().find("label").addClass("liked");
    });
    $(".gform_wrapper .books-question input[type='checkbox']:checked").parent().find("label").addClass("liked");
  
    $(".gform_wrapper ul.gfield_radio input[type='radio']").click(function() {
        $(".gform_wrapper ul.gfield_radio input[type='radio']:not(:checked)").parent().find("label").removeClass("hlighted");
        $(".gform_wrapper ul.gfield_radio input[type='radio']:checked").parent().find("label").addClass("hlighted");
    });
    $(".gform_wrapper ul.gfield_radio input[type='radio']:checked").parent().find("label").addClass("hlighted");
  
    $(".gform_wrapper ul.gfield_checkbox input[type='checkbox']").click(function() {
        $(".gform_wrapper ul.gfield_checkbox input[type='radio']:not(:checked)").parent().find("label").removeClass("hlighted");
        $(".gform_wrapper ul.gfield_checkbox input[type='radio']:checked").parent().find("label").addClass("hlighted");
    });
    $(".gform_wrapper ul.gfield_checkbox input[type='radio']:checked").parent().find("label").addClass("hlighted");
  
  /*
  $(".gform_wrapper .books-question input[type='checkbox']").click(function() {
    $(".gform_wrapper .books-question input[type='checkbox']:not(:checked)").parent().find("label").removeClass("chosen");
    $(".gform_wrapper .books-question input[type='checkbox']:checked").parent().find("label").addClass("chosen");
    });
  $(".gform_wrapper .books-question input[type='checkbox']:checked").parent().find("label").addClass("chosen");
  */
  
    $("#gform_submit_button_3").on("click", function() {
    var firstName = $("#input_3_2_3").val();
    $("#first_name_field #first_name").val(firstName);
  
  	var lastName = $("#input_3_2_6").val();
    $("#last_name_field #last_name").val(lastName);
  
    $("#shipping_country").val("IL");
  
  	var userAddress = $("#input_3_39_1").val();
  	$("#shipping_address_1").val(userAddress);
  
  	var userPostal = $("#input_3_39_5").val();
  	$("#shipping_postcode").val(userPostal);
  
  	var userCity = $("#input_3_39_3").val();
  	$("#shipping_city").val(userCity);
      
      $("input[name='save_address']").trigger("click");
  });
  
  	var firstName = $("#input_3_2_3").val();
    $("#first_name_field #first_name").val(firstName);
  
  	var lastName = $("#input_3_2_6").val();
    $("#last_name_field #last_name").val(lastName);
  
    $("#shipping_country").val("IL");
  
  	var userAddress = $("#input_3_39_1").val();
  	$("#shipping_address_1").val(userAddress);
  
  	var userPostal = $("#input_3_39_5").val();
  	$("#shipping_postcode").val(userPostal);
  
  	var userCity = $("#input_3_39_3").val();
  	$("#shipping_city").val(userCity);

  
});
  
  jQuery(document).on('gform_confirmation_loaded', function(event, formId){
    $("#gform_submit_button_3").on("click", function() {
    var firstName = $("#input_3_2_3").val();
    $("#first_name_field #first_name").val(firstName);
  
  	var lastName = $("#input_3_2_6").val();
    $("#last_name_field #last_name").val(lastName);
  
    $("#shipping_country").val("IL");
  
  	var userAddress = $("#input_3_39_1").val();
  	$("#shipping_address_1").val(userAddress);
  
  	var userPostal = $("#input_3_39_5").val();
  	$("#shipping_postcode").val(userPostal);
  
  	var userCity = $("#input_3_39_3").val();
  	$("#shipping_city").val(userCity);
      
      $("input[name='save_address']").trigger("click");
  });

});
  
  jQuery( document ).ajaxComplete(function() {
    if(window.location.href.indexOf("&for=other") > -1) {
$(".forother").show();
    $(".forme").hide();
      $(".tomailbox").hide();
    
    $('input.recipient_email').each(function() {
      if( !$(this).val() ) {
        $('#place_order').attr('disabled',true);
      }
      else {
        $('#place_order').attr('disabled',false);
      }
    });
    
    
    
    $('input.recipient_email').keyup(function(){
        if($(this).val().length !=0){
            $('#place_order').attr('disabled', false);
        }
        else
        {
            $('#place_order').attr('disabled', true);        
        }
    })
      
    } else {
      $(".forother").hide();
    $(".forme").show();
      $(".tomailbox").show();
    }
    
  });
  
  if($('body').hasClass('page-gift-card-receiver')){
     var urlParams = new URLSearchParams(window.location.search);
     var month = urlParams.get('month');
     var delivery = urlParams.get('delivery');         
     if( ((month == '3') || (month == '6') || (month == '12')) && ((delivery == 'post') || (delivery == 'courier'))){
       $('body').addClass('gift-card-receiver-with-parameters');
       $('#gform_page_14_1 .gform_next_button').attr('onclick','jQuery("#gform_target_page_number_14").val("4"); jQuery("#gform_14").trigger("submit",[true]); ');
       $('#gform_page_14_1 .gform_next_button').attr('onkeypress','if( event.keyCode == 13 ){ jQuery("#gform_target_page_number_14").val("4");  jQuery("#gform_14").trigger("submit",[true]); } '); 
       $('#gform_page_14_4 .gform_previous_button').attr('onclick','jQuery("#gform_target_page_number_14").val("1");  jQuery("#gform_14").trigger("submit",[true]); ');
       $('#gform_page_14_4 .gform_previous_button').attr('onkeypress','if( event.keyCode == 13 ){ jQuery("#gform_target_page_number_14").val("1");  jQuery("#gform_14").trigger("submit",[true]); } ');
       switch(month) {
        case '3':
          $('#choice_14_151_0').attr('checked', 'checked');
          break;
        case '6':
          $('#choice_14_151_1').attr('checked', 'checked');
          break;
        case '12':
          $('#choice_14_151_2').attr('checked', 'checked');
          break;
       }
       switch(delivery) {
        case 'post':
          $('#choice_14_152_0').attr('checked', 'checked');
          break;
        case 'courier':
          $('#choice_14_152_1').attr('checked', 'checked');
          break;
       }
    }
  }
  
  function changeSteps(){
    if($('.gf_page_steps').length){
      if($('body').hasClass('page-on-boarding') || $('body').hasClass('page-logged-in-user') || $('body').hasClass('page-edit-onboarding')) {
        $('.gf_page_steps .gf_step .gf_step_number').each(function() {
          if($(this).text()>= 8){
            $(this).text($(this).text()-4);
          }
        });
      } 
      
    if($('body').hasClass('page-gift-card-receiver') || $('body').hasClass('page-logged-in-giftcard')) {
        $('.gf_page_steps .gf_step .gf_step_number').each(function() {
          if($(this).text()>= 10){
            $(this).text($(this).text()-4);
          }
        });
      } 
      
      if($('body').hasClass('gift-card-receiver-with-parameters')) {
        $('.gf_page_steps .gf_step .gf_step_number').each(function() {
          if($(this).text()>= 4){
            $(this).text($(this).text()-2);
          }
        });
      } 
    }
  }
  changeSteps();
  
  if($('body').hasClass('home')){
    var stickyTop = $('#sticky').offset().top;
  var windowHeight = $(window).height();
  var stickyHeight = $('#sticky').height();
  $('#sticky').after('<div style="height:'+ stickyHeight + 'px"></div>');
  $(window).scroll(function() {
    var windowTop = $(window).scrollTop();
    
    if (windowTop > (stickyTop - 2500) && (windowTop + windowHeight - stickyHeight ) < stickyTop ) {
      $('#sticky').css({
        'position': 'fixed',
        'width': '100%',
        'bottom': '0',
        'opacity': 1,
        'pointer-events': 'auto'
      });
    } else {
      if((windowTop + windowHeight - stickyHeight ) < stickyTop ){
        $('#sticky').css({
          'position': 'fixed',
          'width': '100%',
          'bottom': '0',
          'pointer-events': 'none',
          'opacity': 0
        });
      }
      else {
        $('#sticky').css({
          'position': 'absolute',
          'bottom': 'auto',
          'opacity': 1,
          'pointer-events': 'auto'
        });
      }
     
    }
  });
  }
  
});


  
