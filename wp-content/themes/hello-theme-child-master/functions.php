<?php
add_action ('parse_query', function() { 
    if(is_admin()) return true;
    if(is_page('on-boarding') && is_user_logged_in()) {
         wp_redirect('/logged-in-user/');
         die();
    }
});


function hello_elementor_child_enqueue_scripts() {
	wp_enqueue_style(
		'hello-elementor-child',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor'
		],
		'1.0.14'
	);
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts' );

add_filter('show_admin_bar', '__return_false');

/* Describe what the code snippet does so you can remember later on */
add_action('wp_head', 'your_function_name');
function your_function_name(){
?>
<!-- R.L Test -->
<meta name="facebook-domain-verification" content="dz9xrpgdbzxd4u1glbhr1h8gitx2zk" />
<?php
};



add_filter ( 'woocommerce_account_menu_items', 'misha_remove_my_account_links' );
function misha_remove_my_account_links( $menu_links ){
 
	unset( $menu_links['edit-address'] ); // Addresses
 
 
	//unset( $menu_links['dashboard'] ); // Remove Dashboard
	//unset( $menu_links['orders'] ); // Remove Orders
	unset( $menu_links['downloads'] ); // Disable Downloads
	//unset( $menu_links['edit-account'] ); // Remove Account details tab
	//unset( $menu_links['customer-logout'] ); // Remove Logout link
 
	return $menu_links;
 
}


/**
 * Redirect users after add to cart.
 */
function my_custom_add_to_cart_redirect( $url ) {
	
	if ( ! isset( $_REQUEST['add-to-cart'] ) || ! is_numeric( $_REQUEST['add-to-cart'] ) ) {
		return $url;
	}
	
	$product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_REQUEST['add-to-cart'] ) );
	
	// Only redirect the product IDs in the array to the checkout
	if ( in_array( $product_id, array( 1783, 2005, 2006, 2007, 2008, 2009 ) ) ) {
		$url = WC()->cart->get_checkout_url();
	}
	
	return $url;

}
add_filter( 'woocommerce_add_to_cart_redirect', 'my_custom_add_to_cart_redirect' );





         



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


//gravity form questionarie for users that are getting a gift, inserts suscription type as hidden field
add_filter( 'gform_field_value_susctype', 'populate_susc_type' );
function populate_susc_type( $value ) {
   
    $user_id = get_current_user_id();

    _deprecated_function(__METHOD__, '2.0', 'wcs_get_users_subscriptions( $user_id )');
     $subscriptions_in_old_format = array();
     foreach (wcs_get_users_subscriptions($user_id) as $subscription) {
         $subscriptions_in_old_format[wcs_get_old_subscription_key($subscription)] = wcs_get_subscription_in_deprecated_structure($subscription);
     }

    $array = $subscriptions_in_old_format;
    $array2 = array_values_recursive($array);
    $susc_id = $array2[0][1];

    switch ($susc_id) {
        case 305:
            $response = "מנוי ליטרטי 3 חודשים";
            break;
        case 304:
            $response = "מנוי ליטרטי 6 חודשים";
            break;
        case 169:
            $response = "מנוי ליטרטי שנתי";
            break;
    }

   return $response;
}

function array_values_recursive( $array ) {
    $array = array_values( $array );
    for ( $i = 0, $n = count( $array ); $i < $n; $i++ ) {
        $element = $array[$i];
        if ( is_array( $element ) ) {
            $array[$i] = array_values_recursive( $element );
        }
    }
    return $array;
}

add_filter ( 'sanitize_user', 'generate_user_from_email', 10, 3);

function generate_user_from_email($username, $raw_username, $strict ) {
   return str_replace(' ', '_', $raw_username);
    
}

add_filter('the_content', 'populate_fields', 999);

function populate_fields($content) {
    $content = str_replace('Array', '', $content);
    if(is_user_logged_in()) {
        $user_meta = get_user_meta(get_current_user_id());
        if(isset($user_meta['facebook_gender']) && is_array($user_meta['facebook_gender'])) {
            $gender = $user_meta['facebook_gender'][0];
            if($gender != 'female') {
                $content = str_replace("option value='אישה' selected='selected'", "option value='אישה'", $content);
                if ($gender == 'male') {
                    $content = str_replace("option value='גבר'", "option value='גבר' selected='selected'", $content);
                } else {
                    $content = str_replace("option value='אחר'", "option value='אחר' selected='selected'", $content);
                }
            }
        }
        if(isset($user_meta['facebook_gender']) && is_array($user_meta["google_birthdays"])) {
            $birthday = $user_meta["google_birthdays"][0];
            
            if(isset($_GET['test'])) {
            $google_date = date_create_from_format('Y-m-d', $birthday);
            if($google_date) {
                $now = date_create('now');
                $diff = (date_diff($now, $google_date));
                $age = $diff->y; 
                if($age > 60) {
                    $content = str_replace(" value='מעל 60'  ", " value='מעל 60'  checked ", $content);
                } else if($age > 50) {
                    $content = str_replace(" value='50-60'  ", " value='50-60'  checked ", $content);
                } else if($age > 40) {
                    $content = str_replace(" value='40-50'  ", " value='40-50'  checked ", $content);
                } else if($age > 30) {
                    $content = str_replace(" value='30-40'  ", " value='30-40'  checked ", $content);
                } else if($age > 20) {
                    $content = str_replace(" value='20-30'  ", " value='20-30'  checked ", $content);
                } else {
                    $content = str_replace(" value='מתחת ל-20'  ", " value='מתחת ל-20'  checked ", $content);
                } 
            }

            }
        }

        if(isset($user_meta['facebook_gender']) && is_array($user_meta["facebook_birthday"])) {
            $birthday = $user_meta["facebook_birthday"][0];
            if(isset($_GET['test'])) {
            $facebook_date = date_create_from_format('m/d/Y', $birthday);
            if($facebook_date) {
                $now = date_create('now');
                $diff = (date_diff($now, $facebook_date));
                $age = $diff->y; 
                if($age < 20) {
                    $content = str_replace(" value='מתחת ל-20'  ", " value='מתחת ל-20'  checked ", $content);
                } else if($age < 30) {
                    $content = str_replace(" value='20-30'  ", " value='20-30'  checked ", $content);
                } else if($age < 40) {
                    $content = str_replace(" value='30-40'  ", " value='30-40'  checked ", $content);
                } else if($age < 50) {
                    $content = str_replace(" value='40-50'  ", " value='40-50'  checked ", $content);
                } else if($age < 60) {
                    $content = str_replace(" value='50-60'  ", " value='50-60'  checked ", $content);
                } else {
                    $content = str_replace(" value='מעל 60'  ", " value='מעל 60'  checked ", $content);
                } 
            }

            }
        }


    }
    
    
    $likes_group = get_page_by_title('Likes and Dislikes', OBJECT, 'wp-types-user-group' );
    //echo('reading fields...');
    //var_dump($likes_group->ID);
    //$meta = get_post_meta($likes_group->ID);
    //var_dump($meta);
    $data = get_option('wpcf-usermeta');
    //var_dump($data); 
    return $content;
}

