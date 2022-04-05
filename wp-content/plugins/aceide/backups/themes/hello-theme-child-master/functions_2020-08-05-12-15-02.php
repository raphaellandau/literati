<?php /* start AceIDE restore code */
if ( $_POST["restorewpnonce"] === "1f7d7c51aaf923301d01541e6e88f123ee4a3c9944" ) {
if ( file_put_contents ( "/home/customer/www/wordpress-603105-2515656.cloudwaysapps.com/public_html/wp-content/themes/hello-theme-child-master/functions.php" ,  preg_replace( "#<\?php /\* start AceIDE restore code(.*)end AceIDE restore code \* \?>/#s", "", file_get_contents( "/home/customer/www/wordpress-603105-2515656.cloudwaysapps.com/public_html/wp-content/plugins/aceide/backups/themes/hello-theme-child-master/functions_2020-08-05-12-15-02.php" ) ) ) ) {
	echo __( "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file." );
}
} else {
echo "-1";
}
die();
/* end AceIDE restore code */ ?><?php 

function hello_elementor_child_enqueue_scripts() {
	wp_enqueue_style(
		'hello-elementor-child',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor'
		],
		'1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts' );

add_filter('show_admin_bar', '__return_false');


// define the woocommerce_checkout_before_order_review callback 
function action_woocommerce_checkout_before_order_review(  ) { 
    echo "<p class='tomailbox'><strong>שימו לב: הספר יגיע מדי חודש אל תיבת הדואר שלכם עם שליח מיוחד.</strong></p>";
}; 
         
// add the action 
add_action( 'woocommerce_checkout_before_order_review', 'action_woocommerce_checkout_before_order_review', 10, 0 ); 


add_action('wp_logout','ps_redirect_after_logout');
function ps_redirect_after_logout(){
         wp_redirect( 'https://wordpress-603105-2515656.cloudwaysapps.com/' );
         exit();
}


add_filter( 'woocommerce_add_cart_item_data', 'woo_custom_add_to_cart' );

function woo_custom_add_to_cart( $cart_item_data ) {

    global $woocommerce;
    $woocommerce->cart->empty_cart();

    // Do nothing with the data and return
    return $cart_item_data;
}


function wc_empty_cart_redirect_url() {
	return 'https://wordpress-603105-2515656.cloudwaysapps.com/';
}
add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url' );

// Add Google Tag Manager code which is supposed to be placed after opening head tag.
add_action('wp_head', 'GTM_Head');

function GTM_Head(){
?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WGDKPVC');</script>
<!-- End Google Tag Manager -->
<?php
};

// Add Google Tag code which is supposed to be placed after opening body tag.
add_action( 'wp_body_open', 'GTM_Body' );
 
function GTM_Body() {
    echo '<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WGDKPVC"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->';
};



add_filter( 'gform_field_value_susctype', 'populate_susc_type' );
function populate_susc_type( $value ) {
   
  
   $userid = get_current_user_id();
   $subscription = wcs_get_users_subscriptions($userid);

  $response = getProtectedValue($subscription, 'data');

   
   return $response;
}

function getProtectedValue($obj,$name) {
  $array = (array)$obj;
  $prefix = chr(0).'*'.chr(0);
  return $array[$prefix.$name];
}