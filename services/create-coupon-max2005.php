<?php

require_once('../wp-load.php');

if(!is_user_logged_in()) {
    wp_redirect( wp_login_url('/services/create-coupon-max2005.php') );
    die();
}

if(empty($_POST['coupon_list'])) {
    ?>
    <style>
        form {width: 450px; max-width: 100%; margin:10px 20px;}
        input, textarea {margin:8px 0px; width: 100%;}
        label {font-family: arial, sans-serif; font-weight: 700;}
        textarea {height: 400px;}
    </style>
    <form method="POST" enctype="multipart/form-data">
    <h1>מנוי מתנה שלושה חודשים עם שליח 2005</h1>
    <label>Upload a text file or paste coupon codes (one per line)</label>     
    <textarea rows="10" name="coupon_list"></textarea><br>
    <button>Submit</button>
    </form>
    <?php
    
} else {

    $content = $_POST['coupon_list'];
    

    $codes = preg_split("/\r\n|\n|\r/", $content);
    foreach($codes as $code) {
        $coupon_code = trim($code); // Code
        $amount = '100';
        //$discount_type = 'fixed_cart';
        $discount_type = 'percent';
        $coupon = array(
            'post_title' => $coupon_code,
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'shop_coupon'
        );
        $new_coupon_id = wp_insert_post( $coupon );
        update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
        update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
        update_post_meta( $new_coupon_id, 'individual_use', 'no' );
        update_post_meta( $new_coupon_id, 'product_ids', 2005 );
        //update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
        update_post_meta( $new_coupon_id, 'usage_limit', '1' );
        update_post_meta( $new_coupon_id, 'expiry_date', date('Y-m-d', strtotime('16 July 2022')));
        update_post_meta( $new_coupon_id, 'free_shipping', 'yes' );
        echo('Coupon created: ' . $coupon_code . '<br>');
    }
}