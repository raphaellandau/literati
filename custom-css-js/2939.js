<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
jQuery(document).ready(function( $ ){
   
  jQuery(document).on('gform_page_loaded', function(){
    
    $(".gform_body .gform_page:nth-child(3) .gform_page_fields > ul > li, .gform_body .gform_page:nth-child(4) .gform_page_fields > ul > li, .gform_body .gform_page:nth-child(5) .gform_page_fields > ul > li, .gform_body .gform_page:nth-child(6) .gform_page_fields > ul > li, .gform_body .gform_page:nth-child(7) .gform_page_fields > ul > li").hover(function() {
      $(this).toggleClass("chosen");
    });
    
    $('.bookpage input').click(function(){
        var $radio = $(this);
        if ($radio.data('waschecked') == true)
        {
            $radio.prop('checked', false);
            $radio.data('waschecked', false);
        }
        else
            $radio.data('waschecked', true);
        $radio.siblings('.bookpage input').data('waschecked', false);
    });
    
    
   });
  
});</script>
<!-- end Simple Custom CSS and JS -->
