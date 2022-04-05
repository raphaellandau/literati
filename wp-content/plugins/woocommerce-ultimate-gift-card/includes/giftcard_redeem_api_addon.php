<?php
// Redeem api work...

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'gifting',
			'/redeem-giftcard',
			array(

				'methods'  => 'POST',
				'callback' => 'mwb_redeem_giftcard_offline',
				'permission_callback' => 'mwb_permission_check',

			)
		);
		register_rest_route(
			'gifting',
			'/get-giftcard',
			array(

				'methods'  => 'POST',
				'callback' => 'mwb_get_giftcard_details',
				'permission_callback' => 'mwb_permission_check',

			)
		);
		register_rest_route(
			'gifting',
			'/recharge-giftcard',
			array(

				'methods'  => 'POST',
				'callback' => 'mwb_recharge_giftcard_offine',
				'permission_callback' => 'mwb_permission_check',

			)
		);
	}
);

function mwb_get_giftcard_details( $request ) {

	global $woocommerce;
	$request_params = $request->get_params();

	$coupon_code = $request_params['coupon_code'];
	$coupon_code = strtolower( $coupon_code );

	$coupon_details = new WC_Coupon( $coupon_code );
	$coupon_id = $coupon_details->get_id();

	if ( $coupon_id !== '' && $coupon_id !== 0 ) {
		$giftcard_coupon = get_post_meta( $coupon_id, 'mwb_wgm_giftcard_coupon', true);
		if( '' !== $giftcard_coupon ){
			$woo_ver = WC()->version;
			if ( $woo_ver < '3.6.0' ) {
				$coupon_expiry = get_post_meta( $coupon_id, 'expiry_date', true );
			} 
			else{
				$coupon_expiry = get_post_meta( $coupon_id, 'date_expires', true );
			}
			$coupon_type = get_post_meta($coupon_id, 'mwb_wgm_giftcard_coupon_unique',true);

			if( 'online' == $coupon_type ) {
				$order_id = get_post_meta($coupon_id, 'mwb_wgm_giftcard_coupon',true);
				$online_coupon_data = mwb_wgm_get_online_coupon_data( $order_id );
				$data = array(
					'status' => 200,
					'remaining_amount' => $coupon_details->amount,
					'discount_type'    => $coupon_details->discount_type,
					'usage_count'      => $coupon_details->usage_count ,
					'usage_limit'      => $coupon_details->usage_limit,
					'description'      => $coupon_details->description,
					'coupon_expiry'    => $coupon_expiry,
					'coupon_type'      => $coupon_type,
					'meta_data' 	=> array(
						'online' => $online_coupon_data,
					),			
				);
			} else{
				$coupon_type = get_post_meta($coupon_id, 'mwb_wgm_giftcard_coupon_unique',true);
				$mwb_gw_offline_coupon_data = mwb_wgm_get_offline_coupon_data( $coupon_id );
				$data = array(
					'status' => 200,
					'remaining_amount' => $coupon_details->amount,
					'discount_type'    => $coupon_details->discount_type,
					'usage_count'      => $coupon_details->usage_count ,
					'usage_limit'      => $coupon_details->usage_limit,
					'description'      => $coupon_details->description,
					'coupon_expiry'    => $coupon_expiry,
					'coupon_type'      => $coupon_type,
					'meta_data' 	=> array(
						'offline' => $mwb_gw_offline_coupon_data,
					),			
				);
			}
			$response['code'] = 'success';
			$response['message'] = 'There is Giftcard Details ';
			$response['data'] = $data;
			$response = new WP_REST_Response( $response );
		}
		else{
			$response['code'] = 'error';
			$response['message'] = 'Coupon is not valid  Giftcard Coupon';

			$data = array(
				'status' => 404,

			);
			$response['data'] = $data;
			$response = new WP_REST_Response( $response );
		}

	} else {

		$response['code'] = 'error';
		$response['message'] = 'Coupon is not valid  Giftcard Coupon';

		$data = array(
			'status' => 404,

		);
		$response['data'] = $data;
		$response = new WP_REST_Response( $response );

	}
	return $response;
}
function mwb_wgm_get_offline_coupon_data( $coupon_id ){
	$product_id = get_post_meta($coupon_id, 'mwb_wgm_giftcard_coupon_product_id',true);
	$product = wc_get_product( $product_id );
	$user_email = get_post_meta($coupon_id, 'mwb_wgm_giftcard_coupon_mail_to',true);
	$mwb_gw_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );
	$temp = '';
	$templateid = array_key_exists('template', $mwb_gw_pricing ) ? $mwb_gw_pricing['template'] : '';
	if(is_array($templateid) && array_key_exists(0, $templateid))
	{
		$temp = $templateid[0];
	}
	$mwb_order_data = array(
		'product_id' => $product_id,
		'product_name' => $product->get_name(),
		'product_type' => $product->get_type(),
		'to' => $user_email,
		'template' => $temp,
	);
	return $mwb_order_data;
}

function mwb_wgm_get_online_coupon_data( $order_id ){
	$mwb_order_data = array();
	if( isset( $order_id ) && !empty( $order_id ) ){
		$order = wc_get_order( $order_id );
		foreach( $order->get_items() as $item_id => $item ){
			$product = $item->get_product();
			$product_id = $product->get_id();
			$product_name = $product->get_name();
			$product_type = $product->get_type();			
			$item_meta_data = $item->get_meta_data();
			$gift_date = "";
			$original_price = 0;
			$to_name = '';
			$gift_img_name = '';
			$selected_template = '';
			foreach ($item_meta_data as $key => $value)
			{	
				if(isset($value->key) && $value->key=="To" && !empty($value->value))
				{
					$to = $value->value;
				}
				if(isset($value->key) && $value->key=="To Name" && !empty($value->value))
				{
					$to_name = $value->value;
				}
				if(isset($value->key) && $value->key=="From" && !empty($value->value))
				{
					$from = $value->value;
				}
				if(isset($value->key) && $value->key=="Message" && !empty($value->value))
				{
					$gift_msg = $value->value;
				}
				if(isset($value->key) && $value->key=="Image" && !empty($value->value))
				{
					$gift_img_name = $value->value;
				}
				if(isset($value->key) && $value->key=="Send Date" && !empty($value->value))
				{
					$gift_date = $value->value;				
				}
				if(isset($value->key) && $value->key=="Delivery Method" && !empty($value->value))
				{
					$delivery_method = $value->value;				
				}
				if(isset($value->key) && $value->key=="Original Price" && !empty($value->value))
				{
					$original_price = $value->value;				
				}
				if(isset($value->key) && $value->key=="Selected Template" && !empty($value->value))
				{
					$selected_template = $value->value;				
				}							
			}
			if(!isset($to) && empty($to))
			{
				if($delivery_method == 'Mail to recipient')
				{
					$to=$order->get_billing_email();
				}
				else
				{
					$to = '';
				}
			}
			$mwb_order_data = array(
				'product_id' => $product_id,
				'product_name' => $product_name,
				'product_type' => $product_type,
				'to' => $to,
				'to_name' => $to_name,
				'from' => $from,
				'message' => $gift_msg,
				'image' => $gift_img_name,
				'send_date' => $gift_date,
				'delivery_method' => $delivery_method,
				'original_price' => $original_price,
				'selected_template' => $selected_template,
			);
		}
	}
	return $mwb_order_data;
}


function mwb_redeem_giftcard_offline( $request ) {

	global $woocommerce;

	$request_params = $request->get_params();

	$coupon_code = $request_params['coupon_code'];
	$redeem_amount = $request_params['redeem_amount'];
	$coupon_code = strtolower( $coupon_code );

	$the_coupon = new WC_Coupon( $coupon_code );
	$coupon_id = $the_coupon->get_id();
	if ( $coupon_id !== '' && $coupon_id !== 0 ) {
		$coupon_amount = get_post_meta( $coupon_id, 'coupon_amount', true );
		$coupon_usage_count = get_post_meta( $coupon_id, 'usage_count', true );
		$coupon_usage_limit = get_post_meta( $coupon_id, 'usage_limit', true );

		if ( $coupon_usage_limit == 0 || $coupon_usage_limit > $coupon_usage_count ) {

			$woo_ver = WC()->version;

			$coupon_expiry = '';
			if ( $woo_ver < '3.6.0' ) {

				$coupon_expiry = get_post_meta( $coupon_id, 'expiry_date', true );

			} else {
				$coupon_expiry = get_post_meta( $coupon_id, 'date_expires', true );
			}

			$giftcardcoupon_order_id = get_post_meta( $coupon_id, 'mwb_wgm_giftcard_coupon', true );

			if ( isset( $giftcardcoupon_order_id ) && $giftcardcoupon_order_id != '' ) {

				if ( $coupon_expiry == '' || $coupon_expiry > current_time( 'timestamp' ) ) {

					if ( $coupon_amount >= $redeem_amount ) {

						$remaining_amount = $coupon_amount - $redeem_amount;

						update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount );
						$coupon_usage_count = $coupon_usage_count + 1;
						update_post_meta( $coupon_id, 'usage_count', $coupon_usage_count );

						$response['code'] = 'success';
						$response['message'] = 'Coupon is successfully Redeemed';

						$data = array(
							'status' => 200,
							'remaining_amount' => $remaining_amount,
							'discount_type' => $the_coupon->discount_type,
							'usage_count' => $coupon_usage_count,
							'usage_limit' => $the_coupon->usage_limit,
							'description' => $the_coupon->description,
							'coupon_expiry' => $coupon_expiry,
						);
						$response['data'] = $data;

						$response = new WP_REST_Response( $response );

					} else {

						$response['code'] = 'error';
						$response['message'] = 'Redeem amount is greater than Coupon amount';

						$data = array(
							'status' => 404,

						);
						$response['data'] = $data;
						$response = new WP_REST_Response( $response );
					}
				} else {

					$response['code'] = 'error';
					$response['message'] = 'Coupon is expired';

					$data = array(
						'status' => 404,

					);
					$response['data'] = $data;
					$response = new WP_REST_Response( $response );

				}
			} else {

				$response['code'] = 'error';
				$response['message'] = 'Coupon is not valid Giftcard Coupon';

				$data = array(
					'status' => 404,

				);
				$response['data'] = $data;
				$response = new WP_REST_Response( $response );

			}
		} else {

			$response['code'] = 'error';
			$response['message'] = 'Coupon is already used';

			$data = array(
				'status' => 404,

			);
			$response['data'] = $data;
			$response = new WP_REST_Response( $response );

		}
	} else {
		$response['code'] = 'error';
		$response['message'] = 'Coupon is not valid Giftcard Coupon';

		$data = array(
			'status' => 404,

		);
		$response['data'] = $data;
		$response = new WP_REST_Response( $response );

	}
	return $response;

}


function mwb_recharge_giftcard_offine( $request ) {

	global $woocommerce;
	$request_params = $request->get_params();

	$coupon_code = $request_params['coupon_code'];
	$recharge_amount = $request_params['recharge_amount'];
	$coupon_expiry = ( $request_params['coupon_expiry'] !== '' ) ? $request_params['coupon_expiry'] : null;
	$usage_limit = ( $request_params['usage_limit'] !== '' ) ? $request_params['usage_limit'] : 0;

	$coupon_code = strtolower( $coupon_code );

	$the_coupon = new WC_Coupon( $coupon_code );
	$coupon_id = $the_coupon->get_id();

	if ( $coupon_id !== '' && $coupon_id !== 0 ) {

		$coupon_amount = get_post_meta( $coupon_id, 'coupon_amount', true );

		$coupon_expiry = '';
		$woo_ver = WC()->version;

		if ( $woo_ver < '3.6.0' ) {

			$coupon_expiry = get_post_meta( $coupon_id, 'expiry_date', true );

		} else {
			$coupon_expiry = get_post_meta( $coupon_id, 'date_expires', true );
		}

		$giftcardcoupon_order_id = get_post_meta( $coupon_id, 'mwb_wgm_giftcard_coupon', true );

		if ( isset( $giftcardcoupon_order_id ) && $giftcardcoupon_order_id != '' ) {
			if ( $coupon_expiry == '' || $coupon_expiry > current_time( 'timestamp' ) ) {

				$updated_amount = $coupon_amount + $recharge_amount;

				update_post_meta( $coupon_id, 'coupon_amount', $updated_amount );

				update_post_meta( $coupon_id, 'usage_limit', $usage_limit );
				update_post_meta( $coupon_id, 'usage_count', 0 );

				if ( $woo_ver < '3.6.0' ) {
					update_post_meta( $coupon_id, 'date_expires', $coupon_expiry );
				} else {
					update_post_meta( $coupon_id, 'date_expires', $coupon_expiry );
				}

				$response['code'] = 'success';
				$response['message'] = 'Coupon is successfully Recharged';

				$data = array(
					'status' => 200,
					'remaining_amount' => $updated_amount,
					'discount_type' => $the_coupon->discount_type,
					'usage_count' => 0,
					'usage_limit' => $usage_limit,
					'description' => $the_coupon->description,
					'coupon_expiry' => $coupon_expiry,
				);
				$response['data'] = $data;
				$response = new WP_REST_Response( $response );

			} else {

				$response['code'] = 'error';
				$response['message'] = 'Coupon is expired';

				$data = array(
					'status' => 404,

				);
				$response['data'] = $data;
				$response = new WP_REST_Response( $response );
			}
		} else {

			$response['code'] = 'error';
			$response['message'] = 'Coupon is not valid  Giftcard Coupon';

			$data = array(
				'status' => 404,

			);
			$response['data'] = $data;
			$response = new WP_REST_Response( $response );

		}
	} else {
		$response['code'] = 'error';
		$response['message'] = 'Coupon is not valid  Giftcard Coupon';

		$data = array(
			'status' => 404,

		);
		$response['data'] = $data;
		$response = new WP_REST_Response( $response );
	}
	return $response;
}


function mwb_permission_check( $request ) {
	$license = $request->get_header( 'licensecode' );
	$client_license_code = '';
	$host_server = $_SERVER['HTTP_HOST'];
	if ( strpos( $host_server, 'www.' ) == 0 ) {

		$host_server = str_replace( 'www.', '', $host_server );
	}
	$client_license_code = get_option( 'mwb_wgm_license_hash' . $host_server );
	
	if ( $license == '' ) {
		return true;
	}
	elseif ( trim( $client_license_code ) === trim( $license ) ) {
		return true;
	} 
	else {
		return false;
	}
}
