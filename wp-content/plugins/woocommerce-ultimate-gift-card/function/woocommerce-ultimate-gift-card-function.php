<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'MWB_WGM_Card_Product_Function' ) ) {
	/**
	 * This is class for managing front end giftcard functionality
	 *
	 * @name    MWB_WGM_Card_Product_Function
	 * @category Class
	 * @author   makewebbetter <webmaster@makewebbetter.com>
	 */
	class MWB_WGM_Card_Product_Function {

		/**
		 * This is construct of class where all action and filter is defined
		 *
		 * @name __construct
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'mwb_wgm_load_woocommerce' ) );
		}
		/**
		 * This is function is used for adding the overall functionality of this extension at frontend
		 *
		 * @name mwb_wgm_load_woocommerce
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_load_woocommerce() {
			if ( function_exists( 'WC' ) ) {
				$general_settings = get_option( 'mwb_wgm_general_setting_enable', true );
				if ( $general_settings == 'on' || $general_settings == 1 ) {
					$this->add_hooks_and_filters();
				}
			}
		}
		/**
		 * This is function is used for adding hooks and filters
		 *
		 * @name add_hooks_and_filters
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function add_hooks_and_filters() {
			$woo_ver = WC()->version;
			add_action( 'woocommerce_before_main_content', array( $this, 'mwb_wgm_woocommerce_before_main_content_notice' ), 30, 1 );
			add_action( 'wp_enqueue_scripts', array( $this, 'mwb_wgm_wp_enqueue_scripts' ), 10, 1 );
			add_action( 'woocommerce_product_query', array( $this, 'mwb_wgm_woocommerce_product_query' ) );
			add_filter( 'woocommerce_get_price_html', array( $this, 'mwb_wgm_woocommerce_get_price_html' ), 10, 2 );
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'mwb_wgm_woocommerce_loop_add_to_cart_link' ), 10, 2 );
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'mwb_wgm_woocommerce_before_add_to_cart_button' ), 10, 1 );
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'mwb_wgm_woocommerce_add_cart_item_data' ), 15, 3 );
			add_filter( 'woocommerce_get_item_data', array( $this, 'mwb_wgm_woocommerce_get_item_data' ), 10, 5 );
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'mwb_wgm_woocommerce_before_calculate_totals' ), 10, 1 );
			add_action( 'woocommerce_order_status_changed', array( $this, 'mwb_wgm_woocommerce_order_status_changed' ), 10, 3 );
			add_action( 'woocommerce_order_status_changed', array( $this, 'mwb_wgm_woocommerce_order_status_change' ), 10, 3 );
			if ( $woo_ver < '3.0.0' ) {
				add_action( 'woocommerce_order_add_coupon', array( $this, 'mwb_wgm_woocommerce_order_add_coupon' ), 10, 5 );
				add_action( 'woocommerce_add_order_item_meta', array( $this, 'mwb_wgm_woocommerce_add_order_item_meta' ), 10, 2 );
			} else {
				add_action( 'woocommerce_new_order_item', array( $this, 'mwb_wgm_woocommerce_order_add_coupon_new_ver' ), 10, 5 );
				add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'mwb_wgm_woocommerce_add_order_item_meta_new_ver' ), 10, 3 );
			}

			add_action( 'woocommerce_order_details_after_order_table', array( $this, 'mwb_wgm_woocommerce_order_details_after_order_table' ), 20, 1 );
			add_action( 'woocommerce_after_single_product_summary', array( $this, 'mwb_wgm_woocommerce_after_single_product_summary' ), 5, 1 );
			add_action( 'wp_ajax_mwb_wgm_preview_mail', array( $this, 'mwb_wgm_preview_mail' ) );
			add_action( 'wp_ajax_nopriv_mwb_wgm_preview_mail', array( $this, 'mwb_wgm_preview_mail' ) );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'mwb_wgm_woocommerce_after_shop_loop_item' ) );
			add_action( 'init', array( $this, 'mwb_wgm_preview_email' ) );
			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'mwb_wgm_woocommerce_available_payment_gateways' ), 5, 1 );
			add_filter( 'wc_shipping_enabled', array( $this, 'mwb_wgm_woocommerce_calculated_shipping' ), 10, 1 );
			add_filter( 'woocommerce_product_is_taxable', array( $this, 'mwb_wgm_woocommerce_product_is_taxable' ), 10, 2 );
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'mwb_wgm_woocommerce_add_to_cart_validation' ), 10, 3 );
			add_action( 'mwb_wgm_giftcard_cron_schedule', array( $this, 'mwb_wgm_do_this_hourly' ) );
			add_action( 'mwb_wgm_giftcard_cron_delete_images', array( $this, 'mwb_wgm_do_this_delete_img' ) );
			add_action( 'woocommerce_order_item_meta_end', array( $this, 'mwb_send_mail_forcefully' ), 10, 3 );
			add_action( 'wp_ajax_mwb_wgm_send_mail_force', array( $this, 'mwb_wgm_send_mail_force' ) );
			add_action( 'wp_ajax_nopriv_mwb_wgm_send_mail_force', array( $this, 'mwb_wgm_send_mail_force' ) );
			add_action( 'wp_ajax_mwb_wgm_append_prices', array( $this, 'mwb_wgm_append_prices' ) );
			add_action( 'wp_ajax_nopriv_mwb_wgm_append_prices', array( $this, 'mwb_wgm_append_prices' ) );
			add_action( 'woocommerce_wgm_gift_card_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'mwb_wgm_woocommerce_checkout_update_order_meta' ), 10, 2 );

			add_filter( 'woocommerce_cart_item_price', array( $this, 'mwb_wgm_return_actual_price' ), 10, 3 );
			add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'mwb_wgm_woocommerce_order_item_display_meta_key' ), 10, 1 );
			add_filter( 'woocommerce_order_item_display_meta_value', array( $this, 'mwb_wgm_woocommerce_order_item_display_meta_value' ), 10, 1 );
			$mwb_wgm_apply_coupon_disable = get_option( 'mwb_wgm_additional_apply_coupon_disable', 'off' );
			if ( $mwb_wgm_apply_coupon_disable == 'on' ) {
				add_filter( 'woocommerce_coupons_enabled', array( $this, 'mwb_wgm_hidding_coupon_field_on_cart' ), 10, 1 );
			}
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'mwb_wgm_woocommerce_hidden_order_itemmeta' ), 10, 1 );
			add_action( 'wp_ajax_mwb_wgm_check_giftcard', array( $this, 'mwb_wgm_check_giftcard' ) );
			add_action( 'wp_ajax_nopriv_mwb_wgm_check_giftcard', array( $this, 'mwb_wgm_check_giftcard' ) );
			add_filter( 'mwb_wgm_custom_gift_template', array( $this, 'mwb_wgm_custom_gift_template' ), 10, 2 );

			add_filter( 'woocommerce_order_item_get_formatted_meta_data', array( $this, 'mwb_wgm_woocommerce_hide_order_metafields' ), 10, 1 );

			add_filter( 'woocommerce_is_sold_individually', array( $this, 'mwb_wgm_hide_quantity_fields' ), 10, 2 );

			add_filter( 'wc_price_based_country_product_types_overriden', array( $this, 'mwb_wgm_price_based_country_giftcard' ) );

			add_action( 'wp_ajax_mwb_wgm_append_variable_price', array( $this, 'mwb_wgm_append_variable_price' ) );
			add_action( 'wp_ajax_nopriv_mwb_wgm_append_variable_price', array( $this, 'mwb_wgm_append_variable_price' ) );
		}

		public function mwb_wgm_price_based_country_giftcard( $array ) {

			$array[] = 'wgm_gift_card';
			return $array;
		}

		/** function to add terms and conditions */
		public function mwb_wgm_terms_condition() {
			?>
			<div class="mwb_wgm_expiry_notice clear">
				<h4><?php _e( 'Terms And Conditions', 'woocommerce-ultimate-gift-card' ); ?></h4>
			</div>
			<?php
		}
		/**
		 * This is function is used for hiding quantity field for gift card type product
		 *
		 * @name mwb_wgm_hide_quantity_fields
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */

		public function mwb_wgm_hide_quantity_fields( $return, $product ) {
			$mwb_wgm_disable_quantity_field = get_option( 'mwb_wgm_disable_quantity_field', 'off' );

			if ( $product->is_type( 'wgm_gift_card' ) && $mwb_wgm_disable_quantity_field == 'on' ) {
				return true;
			} else {
				return $return;
			}
		}
		/**
		 * This is function is used for hiding some  item meta from thankyou order page and also from Emails
		 *
		 * @name mwb_wgm_woocommerce_hidden_order_itemmeta
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_woocommerce_hide_order_metafields( $formatted_meta ) {

			$temp_metas = [];
			foreach ( $formatted_meta as $key => $meta ) {

				if ( isset( $meta->key ) && ! in_array( $meta->key, [ 'Delivery Method', 'Original Price', 'Selected Template', 'Image' ] ) ) {

					$temp_metas[ $key ] = $meta;
				}
			}
			return $temp_metas;
		}


		/**
		 * This is function is used for hiding some non-required item meta from order edit page
		 *
		 * @name mwb_wgm_woocommerce_hidden_order_itemmeta
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_woocommerce_hidden_order_itemmeta( $order_items ) {
			array_push( $order_items, 'Delivery Method', 'Selected Template', 'Original Price' );
			return $order_items;
		}
		 /**
		  * Hide coupon feilds from cart page if only giftcard products are there
		  *
		  * @name mwb_wgm_hidding_coupon_field_on_cart
		  * @author makewebbetter<webmaster@makewebbetter.com>
		  * @link http://www.makewebbetter.com/
		  */
		 public function mwb_wgm_hidding_coupon_field_on_cart( $enabled ) {
		 	$bool = false;
		 	$bool2 = false;
		 	$is_checkout = false;
		 	if( is_cart() || is_checkout() ){
		 		if ( ! empty( WC()->cart ) ) {
			 		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			 			$_product = wc_get_product( $cart_item['product_id'] );
			 			if ( $_product->is_type( 'wgm_gift_card' ) ) {
			 				$bool = true;
			 			} else {
			 				$bool2 = true;
			 			}
			 		}
			 	}
			}
		 	if ( $bool && is_cart() && ! $bool2 ) {
		 		$enabled = false;
		 	} elseif ( ! $bool && $bool2 && is_cart() ) {
		 		$enabled = true;
		 	} elseif ( $bool && $bool2 ) {
		 		$enabled = true;
		 	} elseif ( $bool && is_checkout() && ! $bool2 ) {
		 		$enabled = false;
		 	} elseif ( ! $bool && $bool2 && is_checkout() ) {
		 		$enabled = true;
		 	}
		 	return $enabled;
		 }

		/**
		 * Compatible with flatsome theme (wmini_cart)
		 *
		 * @name mwb_wgm_return_actual_price
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_return_actual_price( $price, $cart_item, $cart_item_key ) {
			if ( isset( $cart_item['data'] ) && ! empty( $cart_item['data'] ) ) {
				$product_type = $cart_item['data']->get_type();
				$test = get_option( 'woocommerce_prices_include_tax' );
				if ( $product_type == 'wgm_gift_card' ) {
					if ( 'excl' === WC()->cart->get_tax_price_display_mode() ) {
						return wc_price( ( $cart_item['line_subtotal'] ) / $cart_item['quantity'] );
					} else {
						return wc_price( ( $cart_item['line_subtotal'] + $cart_item['line_subtotal_tax'] ) / $cart_item['quantity'] );
					}
				} else {
					return $price;
				}
			} else {
				return $price;
			}
		}
		/**
		 * Send mail forcefully
		 *
		 * @name mwb_wgm_send_mail_force
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_send_mail_force() {
			check_ajax_referer( 'mwb-wgm-verify-nonce', 'mwb_nonce' );
			$response['result'] = false;
			$response['message'] = __( 'Mail sending failed due to some issue. Please try again.', 'woocommerce-ultimate-gift-card' );
			$discount_enable = get_option( 'mwb_wgm_discount_enable', false );
			if ( isset( $_POST['order_id'] ) && ! empty( $_POST['order_id'] ) && isset( $_POST['item_id'] ) && ! empty( $_POST['item_id'] ) ) {
				$order_id = sanitize_post( $_POST['order_id'] );
				$item_id = sanitize_post( $_POST['item_id'] );
				$order = wc_get_order( $order_id );

				foreach ( $order->get_items() as $item_id_arr => $item ) {
					if ( $item_id_arr == $item_id ) {
						$mailsend = false;
						$original_price = 0;
						$woo_ver = WC()->version;
						$gift_img_name = '';
						$from = '';
						$gift_msg = '';
						if ( $woo_ver < '3.0.0' ) {
							$item_quantity = $order->get_item_meta( $item_id, '_qty', true );
							$product = $order->get_product_from_item( $item );
							$pro_id = $product->id;
							if ( isset( $item['item_meta']['To'] ) && ! empty( $item['item_meta']['To'] ) ) {
								$mailsend = true;
								$to = $item['item_meta']['To'][0];
							}
							if ( isset( $item['item_meta']['To Name'] ) && ! empty( $item['item_meta']['To Name'] ) ) {
								$mailsend = true;
								$to_name = $item['item_meta']['To Name'][0];
							}
							if ( isset( $item['item_meta']['From'] ) && ! empty( $item['item_meta']['From'] ) ) {
								$mailsend = true;
								$from = $item['item_meta']['From'][0];
							}
							if ( isset( $item['item_meta']['Message'] ) && ! empty( $item['item_meta']['Message'] ) ) {
								$mailsend = true;
								$gift_msg = $item['item_meta']['Message'][0];
							}
							if ( isset( $item['item_meta']['Image'] ) && ! empty( $item['item_meta']['Image'] ) ) {
								$mailsend = true;
								$gift_img_name = $item['item_meta']['Image'][0];
							}
							if ( isset( $item['item_meta']['Delivery Method'] ) && ! empty( $item['item_meta']['Delivery Method'] ) ) {
								$mailsend = true;
								$delivery_method = $item['item_meta']['Delivery Method'][0];
							}
							if ( isset( $item['item_meta']['Original Price'] ) && ! empty( $item['item_meta']['Original Price'] ) ) {
								$mailsend = true;
								$original_price = $item['item_meta']['Original Price'][0];
							}
							if ( isset( $item['item_meta']['Selected Template'] ) && ! empty( $item['item_meta']['Selected Template'] ) ) {
								$mailsend = true;
								$selected_template = $item['item_meta']['Selected Template'][0];
							}
							if ( ! isset( $to ) && empty( $to ) ) {
								if ( $delivery_method == 'Mail to recipient' ) {
									$to = $order->billing_email();
								} else {
									$to = '';
								}
							}
							if ( isset( $item['item_meta']['Send Date'] ) && ! empty( $item['item_meta']['Send Date'] ) ) {
								$itemgiftsend = get_post_meta( $order_id, "$order_id#$item_id#send", true );
								if ( $itemgiftsend == 'send' ) {
									$response['result'] = false;
									$response['message'] = __( 'Mail already send on the scheduled date.', 'woocommerce-ultimate-gift-card' );
									echo json_encode( $response );
									die;
								}
								$mailsend = true;
								update_post_meta( $order_id, "$order_id#$item_id#send", 'send' );
							}
						} else {
							$item_quantity = wc_get_order_item_meta( $item_id, '_qty', true );
							$product = $item->get_product();
							$item_meta_data = $item->get_meta_data();
							$pro_id = $product->get_id();
							foreach ( $item_meta_data as $key => $value ) {
								if ( isset( $value->key ) && $value->key == 'To' && ! empty( $value->value ) ) {
									$mailsend = true;
									$to = $value->value;
								}
								if ( isset( $value->key ) && $value->key == 'To Name' && ! empty( $value->value ) ) {
									$mailsend = true;
									$to_name = $value->value;
								}
								if ( isset( $value->key ) && $value->key == 'From' && ! empty( $value->value ) ) {
									$mailsend = true;
									$from = $value->value;
								}
								if ( isset( $value->key ) && $value->key == 'Message' && ! empty( $value->value ) ) {
									$mailsend = true;
									$gift_msg = $value->value;
								}
								if ( isset( $value->key ) && $value->key == 'Image' && ! empty( $value->value ) ) {
									$mailsend = true;
									$gift_img_name = $value->value;
								}
								if ( isset( $value->key ) && $value->key == 'Delivery Method' && ! empty( $value->value ) ) {
									$mailsend = true;
									$delivery_method = $value->value;
								}
								if ( isset( $value->key ) && $value->key == 'Original Price' && ! empty( $value->value ) ) {
									$mailsend = true;
									$original_price = $value->value;
								}
								if ( isset( $value->key ) && $value->key == 'Selected Template' && ! empty( $value->value ) ) {
									$mailsend = true;
									$selected_template = $value->value;
								}
								if ( isset( $value->key ) && $value->key == 'Send Date' && ! empty( $value->value ) ) {
									$itemgiftsend = get_post_meta( $order_id, "$order_id#$item_id#send", true );
									if ( $itemgiftsend == 'send' ) {
										$response['result'] = false;
										$response['message'] = __( 'Mail already send on the scheduled date.', 'woocommerce-ultimate-gift-card' );
										echo json_encode( $response );
										die;
									}

									$mailsend = true;
									update_post_meta( $order_id, "$order_id#$item_id#send", 'send' );
								}
							}
							if ( ! isset( $to ) && empty( $to ) ) {
								if ( $delivery_method == 'Mail to recipient' ) {
									$to = $order->get_billing_email();
								} else {
									$to = '';
								}
							}
						}
						if ( $mailsend ) {
							$gift_order = true;
							// gift total
							$inc_tax_status = get_option( 'woocommerce_prices_include_tax', false );
							if ( $inc_tax_status == 'yes' ) {
								$inc_tax_status = true;
							} else {
								$inc_tax_status = false;
							}
							$couponamont = $original_price;
							$args = array(
								'posts_per_page'   => -1,
								'orderby'          => 'title',
								'order'            => 'asc',
								'post_type'        => 'shop_coupon',
								'post_status'      => 'publish',
							);
							$args['meta_query'] = array(
								array(
									'key' => 'mwb_wgm_imported_coupon',
									'value' => 'yes',
									'compare' => '==',
								),
							);
							$imported_coupons = get_posts( $args );
							$mwb_wgm_common_arr = array();
							$is_imported_product = get_post_meta( $pro_id, 'is_imported', true );
							$mwb_wgm_pricing = get_post_meta( $pro_id, 'mwb_wgm_pricing', true );
							$templateid = $mwb_wgm_pricing['template'];
							if ( is_array( $templateid ) && array_key_exists( 0, $templateid ) ) {
								$temp = $templateid[0];
							} else {
								$temp = $templateid;
							}
							if ( isset( $is_imported_product ) && ! empty( $is_imported_product ) && $is_imported_product == 'yes' ) {

								if ( class_exists( 'WOOCS' ) ) {
									$pro_price = get_post_meta( $pro_id, 'mwb_wgm_pricing', true );
									if ( isset( $pro_price ) && is_array( $pro_price ) ) {
										$couponamont = $pro_price['default_price'];
									}
								} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {
									$pro_price = get_post_meta( $pro_id, 'mwb_wgm_pricing', true );
									if ( isset( $pro_price ) && is_array( $pro_price ) ) {
										$couponamont = $pro_price['default_price'];
									}
								} else {
									$couponamont = $order->get_line_subtotal( $item, $inc_tax_status );
								}

								$gift_couponnumber = get_post_meta( $pro_id, 'coupon_code', true );
								if ( empty( $gift_couponnumber ) && ! isset( $gift_couponnumber ) ) {
									$gift_couponnumber = mwb_wgm_coupon_generator( $giftcard_coupon_length );
								}
								if ( $this->mwb_wgm_create_gift_coupon( $gift_couponnumber, $couponamont, $order_id, $item['product_id'], $to ) ) {
									$todaydate = date_i18n( 'Y-m-d' );
									$expiry_date = get_post_meta( $pro_id, 'expiry_after_days', true );
									$expirydate_format = $this->mwb_wgm_check_expiry_date( $expiry_date );
									wc_update_order_item_meta( $item_id, 'Send Date', $todaydate );
									$mwb_wgm_common_arr['order_id'] = $order_id;
									$mwb_wgm_common_arr['product_id'] = $pro_id;
									$mwb_wgm_common_arr['to'] = $to;
									$mwb_wgm_common_arr['from'] = $from;
									$mwb_wgm_common_arr['to_name'] = $to_name;
									$mwb_wgm_common_arr['gift_couponnumber'] = $gift_couponnumber;
									$mwb_wgm_common_arr['gift_msg'] = $gift_msg;
									$mwb_wgm_common_arr['expirydate_format'] = $expirydate_format;
									$mwb_wgm_common_arr['selected_template'] = ! empty( $selected_template ) ? $selected_template : $temp;
									$mwb_wgm_common_arr['couponamont'] = $couponamont;
									$mwb_wgm_common_arr['delivery_method'] = $delivery_method;
									$mwb_wgm_common_arr['gift_img_name'] = $gift_img_name;
									$mwb_wgm_common_arr['item_id'] = $item_id;
									$mwb_wgm_common_arr = apply_filters( 'mwb_wgm_customized_gc_cgc', $mwb_wgm_common_arr, $item, $order );
									if ( $this->mwb_wgm_common_functionality( $mwb_wgm_common_arr, $order ) ) {
										update_post_meta( $pro_id, '_stock_status', 'outofstock' );
										update_post_meta( $pro_id, '_stock_status', 'outofstock' );
										$response['result'] = true;
										$response['message'] = __( 'Gift card  is Sent Successfully', 'woocommerce-ultimate-gift-card' );
										echo json_encode( $response );
										die;
									}
								}
							} elseif ( ! empty( $imported_coupons ) ) {

								for ( $i = 0; $i < $item_quantity; $i++ ) {
									$imported_code = $imported_coupons[ $i ]->post_title;
									if ( isset( $imported_code ) && ! empty( $imported_code ) ) {
										$the_coupon = new WC_Coupon( $imported_code );
										if ( $woo_ver < '3.0.0' ) {
											$import_coupon_id = $the_coupon->id;
										} else {
											$import_coupon_id = $the_coupon->get_id();
										}
										$expiry_date = get_post_meta( $import_coupon_id, 'mwb_wgm_expiry_date', true );
										$expirydate_format = $this->mwb_wgm_check_expiry_date( $expiry_date );
										$mwb_wgm_common_arr['order_id'] = $order_id;
										$mwb_wgm_common_arr['product_id'] = $pro_id;
										$mwb_wgm_common_arr['to'] = $to;
										$mwb_wgm_common_arr['from'] = $from;
										$mwb_wgm_common_arr['to_name'] = $to_name;
										$mwb_wgm_common_arr['gift_couponnumber'] = $imported_code;
										$mwb_wgm_common_arr['gift_msg'] = $gift_msg;
										$mwb_wgm_common_arr['expirydate_format'] = $expirydate_format;
										$mwb_wgm_common_arr['selected_template'] = $selected_template;
										$mwb_wgm_common_arr['couponamont'] = $couponamont;
										$mwb_wgm_common_arr['delivery_method'] = $delivery_method;
										$mwb_wgm_common_arr['gift_img_name'] = $gift_img_name;
										$mwb_wgm_common_arr['item_id'] = $item_id;
										$mwb_wgm_common_arr = apply_filters( 'mwb_wgm_customized_gc_cgc', $mwb_wgm_common_arr, $item, $order );
										if ( $this->mwb_wgm_common_functionality( $mwb_wgm_common_arr, $order ) ) {
											update_post_meta( $import_coupon_id, 'coupon_amount', $couponamont );
											update_post_meta( $import_coupon_id, 'mwb_wgm_imported_coupon', 'purchased' );
											update_post_meta( $import_coupon_id, 'mwb_wgm_giftcard_coupon', $order_id );
											update_post_meta( $import_coupon_id, 'mwb_wgm_giftcard_coupon_unique', 'online' );
											update_post_meta( $import_coupon_id, 'mwb_wgm_giftcard_coupon_product_id', $product_id );
											update_post_meta( $import_coupon_id, 'mwb_wgm_giftcard_coupon_mail_to', $to );

											$woo_ver = WC()->version;
											if ( ! strtotime( $expirydate_format ) ) {

												$expirydate_format = null;
											}

											if ( $woo_ver < '3.6.0' ) {
												update_post_meta( $import_coupon_id, 'expiry_date', $expirydate_format );
											} else {
												$expirydate_format = strtotime( $expirydate_format );
												update_post_meta( $import_coupon_id, 'date_expires', $expirydate_format );
											}
										}
									} elseif ( empty( $imported_code ) ) {
										$giftcard_coupon_length = get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 );
										$random_code = mwb_wgm_coupon_generator( $giftcard_coupon_length );
										if ( $this->mwb_wgm_create_gift_coupon( $random_code, $couponamont, $order_id, $item['product_id'], $to ) ) {
											$todaydate = date_i18n( 'Y-m-d' );
											$expiry_date = get_option( 'mwb_wgm_general_setting_giftcard_expiry', false );
											$expirydate_format = $this->mwb_wgm_check_expiry_date( $expiry_date );
											$mwb_wgm_common_arr['order_id'] = $order_id;
											$mwb_wgm_common_arr['product_id'] = $pro_id;
											$mwb_wgm_common_arr['to'] = $to;
											$mwb_wgm_common_arr['from'] = $from;
											$mwb_wgm_common_arr['to_name'] = $to_name;
											$mwb_wgm_common_arr['gift_couponnumber'] = $random_code;
											$mwb_wgm_common_arr['gift_msg'] = $gift_msg;
											$mwb_wgm_common_arr['expirydate_format'] = $expirydate_format;
											$mwb_wgm_common_arr['selected_template'] = $selected_template;
											$mwb_wgm_common_arr['couponamont'] = $couponamont;
											$mwb_wgm_common_arr['delivery_method'] = $delivery_method;
											$mwb_wgm_common_arr['gift_img_name'] = $gift_img_name;
											$mwb_wgm_common_arr['item_id'] = $item_id;
											$mwb_wgm_common_arr = apply_filters( 'mwb_wgm_customized_gc_cgc', $mwb_wgm_common_arr, $item, $order );
											if ( $this->mwb_wgm_common_functionality( $mwb_wgm_common_arr, $order ) ) {
											}
										}
									}
								}
							} else {
								$giftcard_coupon_length = get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 );
								for ( $i = 1; $i <= $item_quantity; $i++ ) {
									$gift_couponnumber = mwb_wgm_coupon_generator( $giftcard_coupon_length );
									if ( $this->mwb_wgm_create_gift_coupon( $gift_couponnumber, $couponamont, $order_id, $item['product_id'], $to ) ) {
										$todaydate = date_i18n( 'Y-m-d' );
										$expiry_date = get_option( 'mwb_wgm_general_setting_giftcard_expiry', false );
										$expirydate_format = $this->mwb_wgm_check_expiry_date( $expiry_date );
										$mwb_wgm_common_arr['order_id'] = $order_id;
										$mwb_wgm_common_arr['product_id'] = $pro_id;
										$mwb_wgm_common_arr['to'] = $to;
										$mwb_wgm_common_arr['from'] = $from;
										$mwb_wgm_common_arr['to_name'] = $to_name;
										$mwb_wgm_common_arr['gift_couponnumber'] = $gift_couponnumber;
										$mwb_wgm_common_arr['gift_msg'] = $gift_msg;
										$mwb_wgm_common_arr['expirydate_format'] = $expirydate_format;
										$mwb_wgm_common_arr['selected_template'] = ! empty( $selected_template ) ? $selected_template : $temp;
										$mwb_wgm_common_arr['couponamont'] = $couponamont;
										$mwb_wgm_common_arr['delivery_method'] = $delivery_method;
										$mwb_wgm_common_arr['gift_img_name'] = $gift_img_name;
										$mwb_wgm_common_arr['item_id'] = $item_id;
										$mwb_wgm_common_arr = apply_filters( 'mwb_wgm_customized_gc_cgc', $mwb_wgm_common_arr, $item, $order );
										if ( $this->mwb_wgm_common_functionality( $mwb_wgm_common_arr, $order ) ) {
											$response['result'] = true;
											$response['message'] = __( 'Gift card  is Sent Successfully', 'woocommerce-ultimate-gift-card' );
											echo json_encode( $response );
											die;
										}
									}
								}
							}
						}
						break;
					}
				}
			}
			echo json_encode( $response );
			die;
		}

		/**
		 * Send mail forcefully html
		 *
		 * @name mwb_send_mail_forcefully
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_send_mail_forcefully( $item_id, $item, $order ) {
			$woo_ver = WC()->version;
			$order_status = $order->get_status();
			if ( $order_status == 'completed' || $order_status == 'processing' ) {

				if ( $woo_ver < '3.0.0' ) {
					$_product = $order->get_product_from_item( $item );
					$product_id = $_product->id;
				} else {
					$_product = $item->get_product();
					if ( ! empty( $_product ) ) {
						$product_id = $_product->get_id();
					}
				}
				if ( isset( $product_id ) && ! empty( $product_id ) ) {
					$product_types = wp_get_object_terms( $product_id, 'product_type' );
					if ( isset( $product_types[0] ) ) {
						$product_type = $product_types[0]->slug;
						if ( $product_type == 'wgm_gift_card' ) {

							if ( $woo_ver < '3.0.0' ) {

								if ( isset( $item['item_meta']['Send Date'] ) && ! empty( $item['item_meta']['Send Date'] ) ) {
									$order_id = $order->id;

									$itemgiftsend = get_post_meta( $order_id, "$order_id#$item_id#send", true );

									$mwb_wgm_sendtoday_disable = get_option( 'mwb_wgm_additional_sendtoday_disable', 'off' );
									if ( $itemgiftsend != 'send' ) {

										if ( $mwb_wgm_sendtoday_disable == 'off' ) {
											?>
											<div id="mwb_wgm_loader" style="display: none;">
												<img src="<?php echo MWB_WGM_URL; ?>/assets/images/loading.gif">
											</div>
											<p id="mwb_wgm_send_mail_force_notification_<?php echo $item_id; ?>"></p>
											<div id="mwb_send_force_div_<?php echo $item_id; ?>">
												<input type="button" data-id="<?php echo $order_id; ?>" data-num = "<?php echo $item_id; ?>" class="mwb_wgm_send_mail_force" class="button button-primary" value="<?php _e( 'Send Today', 'woocommerce-ultimate-gift-card' ); ?>">
											</div>
											<?php
										}
									}
								}
							} else {

								$item_data = $item->get_meta_data();
								$order_id = $order->get_id();
								foreach ( $item_data as $key => $value ) {

									if ( isset( $value->key ) && $value->key == 'Send Date' && ! empty( $value->value ) ) {
										$itemgiftsend = get_post_meta( $order_id, "$order_id#$item_id#send", true );
										$mwb_wgm_sendtoday_disable = get_option( 'mwb_wgm_additional_sendtoday_disable', 'off' );
										if ( $itemgiftsend != 'send' ) {
											if ( $mwb_wgm_sendtoday_disable == 'off' ) {
												?>
												<div id="mwb_wgm_loader" style="display: none;">
													<img src="<?php echo MWB_WGM_URL; ?>/assets/images/loading.gif">
												</div>
												<p id="mwb_wgm_send_mail_force_notification_<?php echo $item_id; ?>"></p>
												<div id="mwb_send_force_div_<?php echo $item_id; ?>">
													<input type="button" data-id="<?php echo $order_id; ?>" data-num = "<?php echo $item_id; ?>" class="mwb_wgm_send_mail_force" class="button button-primary" value="<?php _e( 'Send Today', 'woocommerce-ultimate-gift-card' ); ?>">
												</div>
												<?php
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		/**
		 * Cron for set giftcard on specific date
		 *
		 * @name mwb_wgm_do_this_hourly
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_do_this_hourly() {
			$woo_ver = WC()->version;
			$giftcard_selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_date', false );
			$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format_1', false );
			$discount_enable = get_option( 'mwb_wgm_discount_enable', false );
			if ( $giftcard_selected_date == 'on' ) {
				// fetch all giftcard order which need to be send on specific date
				$order_statuses = array(
					'wc-processing' => __( 'Processing', 'Order status', 'woocommerce' ),
					'wc-completed'  => __( 'Completed', 'Order status', 'woocommerce' ),
				);

				$shop_orders = get_posts(
					array(
						'numberposts' => -1,
						'meta_key'    => 'mwb_wgm_order_giftcard',
						'meta_value'  => 'notsend',
						'post_type'   => wc_get_order_types(),
						'post_status' => array_keys( $order_statuses ),
					)
				);

				if ( isset( $shop_orders ) && ! empty( $shop_orders ) ) {
					foreach ( $shop_orders as $shop_order ) {

						$order_id = $shop_order->ID;
						$gift_msg = '';
						$original_price = 0;
						$to = '';
						$from = '';
						$gift_order = false;
						$order = wc_get_order( $order_id );
						$datecheck = true;
						foreach ( $order->get_items() as $item_id => $item ) {

							$mailsend = false;
							$gift_img_name = '';
							if ( $woo_ver < '3.0.0' ) {
								$item_quantity = $order->get_item_meta( $item_id, '_qty', true );
								$product = $order->get_product_from_item( $item );
								$pro_id = $product->id;
								if ( isset( $item['item_meta']['To'] ) && ! empty( $item['item_meta']['To'] ) ) {
									$mailsend = true;
									$to = $item['item_meta']['To'][0];
								}
								if ( isset( $item['item_meta']['To Name'] ) && ! empty( $item['item_meta']['To Name'] ) ) {
									$mailsend = true;
									$to_name = $item['item_meta']['To Name'][0];
								}
								if ( isset( $item['item_meta']['From'] ) && ! empty( $item['item_meta']['From'] ) ) {
									$mailsend = true;
									$from = $item['item_meta']['From'][0];
								}
								if ( isset( $item['item_meta']['Message'] ) && ! empty( $item['item_meta']['Message'] ) ) {
									$mailsend = true;
									$gift_msg = $item['item_meta']['Message'][0];
								}
								if ( isset( $item['item_meta']['Image'] ) && ! empty( $item['item_meta']['Image'] ) ) {
									$mailsend = true;
									$gift_img_name = $item['item_meta']['Image'][0];
								}
								if ( isset( $item['item_meta']['Delivery Method'] ) && ! empty( $item['item_meta']['Delivery Method'] ) ) {
									$mailsend = true;
									$delivery_method = $item['item_meta']['Delivery Method'][0];
								}
								if ( isset( $item['item_meta']['Original Price'] ) && ! empty( $item['item_meta']['Original Price'] ) ) {
									$mailsend = true;
									$original_price = $item['item_meta']['Original Price'][0];
								}
								if ( isset( $item['item_meta']['Selected Template'] ) && ! empty( $item['item_meta']['Selected Template'] ) ) {
									$mailsend = true;
									$selected_template = $item['item_meta']['Selected Template'][0];
								}
								if ( ! isset( $to ) && empty( $to ) ) {
									if ( $delivery_method == 'Mail to recipient' ) {
										$to = $order->billing_email();
									} else {
										$to = '';
									}
								}
								if ( isset( $item['item_meta']['Send Date'] ) && ! empty( $item['item_meta']['Send Date'] ) ) {
									$itemgiftsend = get_post_meta( $order_id, "$order_id#$item_id#send", true );
									if ( $itemgiftsend == 'send' ) {
										continue;
									}

									$mailsend = true;
									$gift_date = $item['item_meta']['Send Date'][0];
									if ( is_string( $gift_date ) ) {
										if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
											if ( $selected_date == 'd/m/Y' ) {
												$gift_date = str_replace( '/', '-', $gift_date );
											}
										}
										$senddatetime = strtotime( $gift_date );
									}
									$senddate = date_i18n( 'Y-m-d', $senddatetime );
									$todaytime = time();
									$todaydate = date_i18n( 'Y-m-d', $todaytime );
									$senddatetime = strtotime( "$senddate" );
									$todaytime = strtotime( "$todaydate" );
									$giftdiff = $senddatetime - $todaytime;

									if ( isset( $delivery_method ) && $delivery_method == 'Mail to recipient' ) {
										if ( $giftdiff > 0 ) {
											$datecheck = false;

											update_post_meta( $order_id, "$order_id#$item_id#send", 'notsend' );
											continue;
										} else {
											update_post_meta( $order_id, "$order_id#$item_id#send", 'send' );
										}
									} else {
										update_post_meta( $order_id, "$order_id#$item_id#send", 'send' );
									}
								}
							} else {
								$item_quantity = wc_get_order_item_meta( $item_id, '_qty', true );
								$product = $item->get_product();
								$pro_id = '';
								if ( ! empty( $product ) ) {
									$pro_id = $product->get_id();
								}
								$item_meta_data = $item->get_meta_data();

								$gift_date_check = false;
								$gift_date = '';
								foreach ( $item_meta_data as $key => $value ) {

									if ( isset( $value->key ) && $value->key == 'To' && ! empty( $value->value ) ) {
										$mailsend = true;
										$to = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'To Name' && ! empty( $value->value ) ) {
										$mailsend = true;
										$to_name = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'From' && ! empty( $value->value ) ) {
										$mailsend = true;
										$from = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'Message' && ! empty( $value->value ) ) {
										$mailsend = true;
										$gift_msg = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'Image' && ! empty( $value->value ) ) {
										$mailsend = true;
										$gift_img_name = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'Send Date' && ! empty( $value->value ) ) {
										$gift_date_check = true;
										$gift_date = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'Delivery Method' && ! empty( $value->value ) ) {
										$mailsend = true;
										$delivery_method = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'Original Price' && ! empty( $value->value ) ) {
										$mailsend = true;
										$original_price = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'Selected Template' && ! empty( $value->value ) ) {
										$mailsend = true;
										$selected_template = $value->value;
									}
								}
								if ( ! isset( $to ) && empty( $to ) ) {
									if ( $delivery_method == 'Mail to recipient' ) {
										$to = $order->get_billing_email();
									} else {
										$to = '';
									}
								}
								if ( $gift_date_check ) {
									$itemgiftsend = get_post_meta( $order_id, "$order_id#$item_id#send", true );
									if ( $itemgiftsend == 'send' ) {
										continue;
									}

									$mailsend = true;
									if ( is_string( $gift_date ) ) {
										if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
											if ( $selected_date == 'd/m/Y' ) {
												$gift_date = str_replace( '/', '-', $gift_date );
											}
										}
										$senddatetime = strtotime( $gift_date );
									}
									$senddate = date_i18n( 'Y-m-d', $senddatetime );
									$todaytime = time();
									$todaydate = date_i18n( 'Y-m-d', $todaytime );
									$senddatetime = strtotime( "$senddate" );
									$todaytime = strtotime( "$todaydate" );

									$giftdiff = $senddatetime - $todaytime;

									if ( $delivery_method == 'Mail to recipient' ) {
										if ( $giftdiff > 0 ) {

											$datecheck = false;

											update_post_meta( $order_id, "$order_id#$item_id#send", 'notsend' );
											continue;
										} else {
											update_post_meta( $order_id, "$order_id#$item_id#send", 'send' );
										}
									} else {
										update_post_meta( $order_id, "$order_id#$item_id#send", 'send' );
									}
								}
							}
							if ( $mailsend ) {

								$gift_order = true;
								$inc_tax_status = get_option( 'woocommerce_prices_include_tax', false );
								if ( $inc_tax_status == 'yes' ) {
									$inc_tax_status = true;
								} else {
									$inc_tax_status = false;
								}
								$mwb_wgm_discount = get_post_meta( $item['product_id'], 'mwb_wgm_discount', false );
								$couponamont = $original_price;	
							$args = array(
								'posts_per_page'   => -1,
								'orderby'          => 'title',
								'order'            => 'asc',
								'post_type'        => 'shop_coupon',
								'post_status'      => 'publish',
							);
							$args['meta_query'] = array(
								array(
									'key' => 'mwb_wgm_imported_coupon',
									'value' => 'yes',
									'compare' => '==',
								),
							);
							$imported_coupons = get_posts( $args );
							$mwb_wgm_common_arr = array();
							$is_imported_product = get_post_meta( $pro_id, 'is_imported', true );
							$mwb_wgm_pricing = get_post_meta( $pro_id, 'mwb_wgm_pricing', true );
							$templateid = $mwb_wgm_pricing['template'];
							if ( is_array( $templateid ) && array_key_exists( 0, $templateid ) ) {
								$temp = $templateid[0];
							} else {
								$temp = $templateid;
							}
							if ( isset( $is_imported_product ) && ! empty( $is_imported_product ) && $is_imported_product == 'yes' ) {

								if ( class_exists( 'WOOCS' ) ) {
									$pro_price = get_post_meta( $pro_id, 'mwb_wgm_pricing', true );
									if ( isset( $pro_price ) && is_array( $pro_price ) ) {
										$couponamont = $pro_price['default_price'];
									}
								} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {
									$pro_price = get_post_meta( $pro_id, 'mwb_wgm_pricing', true );
									if ( isset( $pro_price ) && is_array( $pro_price ) ) {
										$couponamont = $pro_price['default_price'];
									}
								} else {
									$couponamont = $order->get_line_subtotal( $item, $inc_tax_status );
								}

								$gift_couponnumber = get_post_meta( $pro_id, 'coupon_code', true );
								if ( empty( $gift_couponnumber ) && ! isset( $gift_couponnumber ) ) {
									$gift_couponnumber = mwb_wgm_coupon_generator( $giftcard_coupon_length );

								}
								if ( $this->mwb_wgm_create_gift_coupon( $gift_couponnumber, $couponamont, $order_id, $item['product_id'], $to ) ) {
									$todaydate = date_i18n( 'Y-m-d' );
									$expiry_date = get_post_meta( $pro_id, 'expiry_after_days', true );
									$expirydate_format = $this->mwb_wgm_check_expiry_date( $expiry_date );
									$mwb_wgm_common_arr['order_id'] = $order_id;
									$mwb_wgm_common_arr['product_id'] = $pro_id;
									$mwb_wgm_common_arr['to'] = $to;
									$mwb_wgm_common_arr['from'] = $from;
									$mwb_wgm_common_arr['to_name'] = $to_name;
									$mwb_wgm_common_arr['gift_couponnumber'] = $gift_couponnumber;
									$mwb_wgm_common_arr['gift_msg'] = $gift_msg;
									$mwb_wgm_common_arr['expirydate_format'] = $expirydate_format;
									$mwb_wgm_common_arr['selected_template'] = ! empty( $selected_template ) ? $selected_template : $temp;
									$mwb_wgm_common_arr['couponamont'] = $couponamont;
									$mwb_wgm_common_arr['delivery_method'] = $delivery_method;
									$mwb_wgm_common_arr['gift_img_name'] = $gift_img_name;
									$mwb_wgm_common_arr['item_id'] = $item_id;
									$mwb_wgm_common_arr = apply_filters( 'mwb_wgm_customized_gc_cgc', $mwb_wgm_common_arr, $item, $order );
									if ( $this->mwb_wgm_common_functionality( $mwb_wgm_common_arr, $order ) ) {
										update_post_meta( $pro_id, '_stock_status', 'outofstock' );
									}
								}
							} elseif ( ! empty( $imported_coupons ) ) {
								for ( $i = 0; $i < $item_quantity; $i++ ) {
									$imported_code = $imported_coupons[ $i ]->post_title;
									if ( isset( $imported_code ) && ! empty( $imported_code ) ) {
										$the_coupon = new WC_Coupon( $imported_code );
										if ( $woo_ver < '3.0.0' ) {
											$import_coupon_id = $the_coupon->id;
										} else {
											$import_coupon_id = $the_coupon->get_id();
										}
										$expiry_date = get_post_meta( $import_coupon_id, 'mwb_wgm_expiry_date', true );
										$expirydate_format = $this->mwb_wgm_check_expiry_date( $expiry_date );
										$mwb_wgm_common_arr['order_id'] = $order_id;
										$mwb_wgm_common_arr['product_id'] = $pro_id;
										$mwb_wgm_common_arr['to'] = $to;
										$mwb_wgm_common_arr['from'] = $from;
										$mwb_wgm_common_arr['to_name'] = $to_name;
										$mwb_wgm_common_arr['gift_couponnumber'] = $imported_code;
										$mwb_wgm_common_arr['gift_msg'] = $gift_msg;
										$mwb_wgm_common_arr['expirydate_format'] = $expirydate_format;
										$mwb_wgm_common_arr['selected_template'] = $selected_template;
										$mwb_wgm_common_arr['couponamont'] = $couponamont;
										$mwb_wgm_common_arr['delivery_method'] = $delivery_method;
										$mwb_wgm_common_arr['gift_img_name'] = $gift_img_name;
										$mwb_wgm_common_arr['item_id'] = $item_id;
										$mwb_wgm_common_arr = apply_filters( 'mwb_wgm_customized_gc_cgc', $mwb_wgm_common_arr, $item, $order );
										if ( $this->mwb_wgm_common_functionality( $mwb_wgm_common_arr, $order ) ) {
											update_post_meta( $import_coupon_id, 'coupon_amount', $couponamont );
											update_post_meta( $import_coupon_id, 'mwb_wgm_imported_coupon', 'purchased' );
											update_post_meta( $import_coupon_id, 'mwb_wgm_giftcard_coupon', $order_id );
											update_post_meta( $import_coupon_id, 'mwb_wgm_giftcard_coupon_unique', 'online' );
											update_post_meta( $import_coupon_id, 'mwb_wgm_giftcard_coupon_product_id', $product_id );
											update_post_meta( $import_coupon_id, 'mwb_wgm_giftcard_coupon_mail_to', $to );

											$woo_ver = WC()->version;

											if ( ! strtotime( $expirydate_format ) ) {

												$expirydate_format = null;
											}

											if ( $woo_ver < '3.6.0' ) {
												update_post_meta( $import_coupon_id, 'expiry_date', $expirydate_format );
											} else {
												$expirydate_format = strtotime( $expirydate_format );
												update_post_meta( $import_coupon_id, 'date_expires', $expirydate_format );
											}
										}
									} elseif ( empty( $imported_code ) ) {
										$giftcard_coupon_length = get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 );
										$random_code = mwb_wgm_coupon_generator( $giftcard_coupon_length );
										if ( $this->mwb_wgm_create_gift_coupon( $random_code, $couponamont, $order_id, $item['product_id'], $to ) ) {
											$todaydate = date_i18n( 'Y-m-d' );
											$expiry_date = get_option( 'mwb_wgm_general_setting_giftcard_expiry', false );
											$expirydate_format = $this->mwb_wgm_check_expiry_date( $expiry_date );
											$mwb_wgm_common_arr['order_id'] = $order_id;
											$mwb_wgm_common_arr['product_id'] = $pro_id;
											$mwb_wgm_common_arr['to'] = $to;
											$mwb_wgm_common_arr['from'] = $from;
											$mwb_wgm_common_arr['to_name'] = $to_name;
											$mwb_wgm_common_arr['gift_couponnumber'] = $random_code;
											$mwb_wgm_common_arr['gift_msg'] = $gift_msg;
											$mwb_wgm_common_arr['expirydate_format'] = $expirydate_format;
											$mwb_wgm_common_arr['selected_template'] = $selected_template;
											$mwb_wgm_common_arr['couponamont'] = $couponamont;
											$mwb_wgm_common_arr['delivery_method'] = $delivery_method;
											$mwb_wgm_common_arr['gift_img_name'] = $gift_img_name;
											$mwb_wgm_common_arr['item_id'] = $item_id;
											$mwb_wgm_common_arr = apply_filters( 'mwb_wgm_customized_gc_cgc', $mwb_wgm_common_arr, $item, $order );
											if ( $this->mwb_wgm_common_functionality( $mwb_wgm_common_arr, $order ) ) {
											}
										}
									}
								}
							} else {
								$giftcard_coupon_length = get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 );
								for ( $i = 1; $i <= $item_quantity; $i++ ) {
									$gift_couponnumber = mwb_wgm_coupon_generator( $giftcard_coupon_length );
									if ( $this->mwb_wgm_create_gift_coupon( $gift_couponnumber, $couponamont, $order_id, $item['product_id'], $to ) ) {
										$todaydate = date_i18n( 'Y-m-d' );
										$expiry_date = get_option( 'mwb_wgm_general_setting_giftcard_expiry', false );
										$expirydate_format = $this->mwb_wgm_check_expiry_date( $expiry_date );
										$mwb_wgm_common_arr['order_id'] = $order_id;
										$mwb_wgm_common_arr['product_id'] = $pro_id;
										$mwb_wgm_common_arr['to'] = $to;
										$mwb_wgm_common_arr['from'] = $from;
										$mwb_wgm_common_arr['to_name'] = $to_name;
										$mwb_wgm_common_arr['gift_couponnumber'] = $gift_couponnumber;
										$mwb_wgm_common_arr['gift_msg'] = $gift_msg;
										$mwb_wgm_common_arr['expirydate_format'] = $expirydate_format;
										$mwb_wgm_common_arr['selected_template'] = ! empty( $selected_template ) ? $selected_template : $temp;
										$mwb_wgm_common_arr['couponamont'] = $couponamont;
										$mwb_wgm_common_arr['delivery_method'] = $delivery_method;
										$mwb_wgm_common_arr['gift_img_name'] = $gift_img_name;
										$mwb_wgm_common_arr['item_id'] = $item_id;
										$mwb_wgm_common_arr = apply_filters( 'mwb_wgm_customized_gc_cgc', $mwb_wgm_common_arr, $item, $order );
										if ( $this->mwb_wgm_common_functionality( $mwb_wgm_common_arr, $order ) ) {
										}
									}
								}
							}
						}
					}
					if ( $gift_order && $datecheck ) {
						update_post_meta( $order_id, 'mwb_wgm_order_giftcard', 'send' );
					}
				}
			}
		}
		global $wpdb;
		$table_name = $wpdb->prefix . 'offline_giftcard';
		$query = "SELECT * FROM $table_name WHERE `mail` != 1";
		$giftresults = $wpdb->get_results( $query, ARRAY_A );

		if ( isset( $giftresults ) && ! empty( $giftresults ) && $giftresults != null ) {
			foreach ( $giftresults as $key => $value ) {

				if ( isset( $value['schedule'] ) && $value['schedule'] != null && $value['schedule'] != '' ) {
					$schedule_date = $value['schedule'];
					if ( is_string( $schedule_date ) ) {
						if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
							if ( $selected_date == 'd/m/Y' ) {
								$gift_date = str_replace( '/', '-', $schedule_date );
							}
						}
						$senddatetime = strtotime( $schedule_date );
					}
				} else {
					$schedule_date = date_i18n( 'Y-m-d' );
					$senddatetime = strtotime( $schedule_date );
				}
				$senddate = date_i18n( 'Y-m-d', $senddatetime );
				$todaytime = time();
				$todaydate = date_i18n( 'Y-m-d', $todaytime );
				$senddatetime = strtotime( "$senddate" );
				$todaytime = strtotime( "$todaydate" );
				$giftdiff = $senddatetime - $todaytime;

				if ( $giftdiff <= 0 ) {

					$couponcreated = mwb_wgm_create_offlinegift_coupon( $value['coupon'], $value['amount'], $value['id'], $value['template'], $value['to'] );
					$product_id = $value['template'];
					$mwb_wgm_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );
					$templateid = $mwb_wgm_pricing['template'];
					if ( is_array( $templateid ) && array_key_exists( 0, $templateid ) ) {
						$temp = $templateid[0];
					} else {
						$temp = $templateid;
					}
					$args['from'] = $value['from'];
					$args['to'] = $value['to'];
					$args['message'] = stripcslashes( $value['message'] );
					$args['coupon'] = apply_filters( 'mwb_wgm_qrcode_coupon', $value['coupon'] );
					$to = $args['to'];
					$from = $args['from'];
					$couponcode = $value['coupon'];
					$coupon = new WC_Coupon( $couponcode );
					if ( isset( $coupon->id ) ) {
						$expirydate = $coupon->expiry_date;

						if ( empty( $expirydate ) ) {
							$expirydate_format = __( 'No Expiration', 'woocommerce-ultimate-gift-card' );
						} else {
							$expirydate = date_i18n( 'Y-m-d', $expirydate );
							$expirydate_format = date_create( $expirydate );

							$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format_1', false );
							if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
								$expirydate_format = date_i18n( $selected_date, strtotime( "$todaydate +$expiry_date day" ) );
							} else {
								$expirydate_format = date_format( $expirydate_format, 'jS M Y' );
							}
						}
						$args['expirydate'] = $expirydate_format;
						$args['amount'] = wc_price( $value['amount'] );
						$args['templateid'] = $temp;
						$args['product_id'] = $product_id;
						$args['order_id'] = '';
						$giftcardfunction = new MWB_WGM_Card_Product_Function();
						$message = $giftcardfunction->mwb_wgm_giftttemplate( $args );
						$mwb_wgm_pdf_enable = get_option( 'mwb_wgm_addition_pdf_enable', false );
						if ( isset( $mwb_wgm_pdf_enable ) && $mwb_wgm_pdf_enable == 'on' ) {
							$site_name = $_SERVER['SERVER_NAME'];
							$time = time();
							$this->mwb_wgm_attached_pdf( $message, $site_name, $time );
							$attachments = array( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $time . $site_name . '.pdf' );
						} else {
							$attachments = array();
						}
						$subject = get_option( 'mwb_wgm_other_setting_giftcard_subject', false );
						$bloginfo = get_bloginfo();
						if ( empty( $subject ) || ! isset( $subject ) ) {

							$subject = "$bloginfo:";
							$subject .= __( ' Hurry!!! Giftcard is Received', 'woocommerce-ultimate-gift-card' );
						}
						$subject = str_replace( '[SITENAME]', $bloginfo, $subject );
						$subject = str_replace( '[BUYEREMAILADDRESS]', $from, $subject );
						$subject = stripcslashes( $subject );
						$subject = html_entity_decode( $subject, ENT_QUOTES, 'UTF-8' );
							// Send mail to Receiver
						$mwb_wgc_bcc_enable = get_option( 'mwb_wgm_addition_bcc_option_enable', false );
						if ( isset( $mwb_wgc_bcc_enable ) && $mwb_wgc_bcc_enable == 'on' ) {
							$headers[] = 'Bcc:' . $from;
							wc_mail( $to, $subject, $message, $headers, $attachments );
							if ( ! empty( $time ) && ! empty( $site_name ) ) {
								unlink( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $time . $site_name . '.pdf' );
							}
						} else {
							$headers = array( 'Content-Type: text/html; charset=UTF-8' );
							wc_mail( $to, $subject, $message, $headers, $attachments );
							if ( ! empty( $time ) && ! empty( $site_name ) ) {
								unlink( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $time . $site_name . '.pdf' );
							}
						}

						$subject = get_option( 'mwb_wgm_other_setting_receive_subject', false );
						$message = get_option( 'mwb_wgm_other_setting_receive_message', false );
						if ( empty( $subject ) || ! isset( $subject ) ) {

							$subject = "$bloginfo:";
							$subject .= __( ' Gift Card is Sent Successfully', 'woocommerce-ultimate-gift-card' );
						}

						if ( empty( $message ) || ! isset( $message ) ) {

							$message = "$bloginfo:";
							$message .= __( ' Gift Card is Sent Successfully to the Email Id: [TO]', 'woocommerce-ultimate-gift-card' );
						}

						$message = stripcslashes( $message );
						$message = str_replace( '[TO]', $to, $message );
						$subject = stripcslashes( $subject );
							// send acknowledge mail to sender
						$mwb_wgm_disable_buyer_notification = get_option( 'mwb_wgm_disable_buyer_notification', 'off' );
						if ( $mwb_wgm_disable_buyer_notification == 'off' ) {
							wc_mail( $from, $subject, $message );
						}
						$dataToupdate = array( 'mail' => 1 );
						$where = array( 'id' => $value['id'] );
						$update_data = $wpdb->update( $table_name, $dataToupdate, $where );
					}
				}
			}
		}
	}
		/**
		 * This function is used to trim input fields
		 *
		 * @name test_input
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function test_input( $data ) {
			$data = trim( $data );
			$data = stripslashes( $data );
			$data = htmlspecialchars( $data );
			return $data;
		}
		/**
		 * This function is for validating the ajax add to cart request on single product page
		 *
		 * @param $validate
		 * @param $product_id
		 * @param $quantity
		 * @return boolean
		 * @name mwb_wgm_woocommerce_add_to_cart_validation
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_woocommerce_add_to_cart_validation( $validate, $product_id, $quantity ) {
			$mwb_wgm_remove_validation_to = get_option( 'mwb_wgm_remove_validation_to', false );
			$mwb_wgm_remove_validation_from = get_option( 'mwb_wgm_remove_validation_from', false );
			$mwb_wgm_remove_validation_msg = get_option( 'mwb_wgm_remove_validation_msg', false );

			$mwb_wgm_remove_validation_to_name = get_option( 'mwb_wgm_remove_validation_to_name', 'on' );

			$product_types = wp_get_object_terms( $product_id, 'product_type' );
			if ( isset( $product_types[0] ) ) {
				$product_type = $product_types[0]->slug;
				if ( $product_type == 'wgm_gift_card' ) {
					$is_customizable = get_post_meta( $product_id, 'woocommerce_customizable_gift_card', true );

					$giftcard_message_length = trim( get_option( 'mwb_wgm_other_setting_giftcard_message_length', 300 ) );
					if ( empty( $giftcard_message_length ) ) {
						$giftcard_message_length = 300;
					}

					if ( ! isset( $_POST['mwb_wgm_send_giftcard'] ) || empty( $_POST['mwb_wgm_send_giftcard'] ) ) {
						$validate = false;
						wc_add_notice( __( 'Delivery Method: Please Select One Method', 'woocommerce-ultimate-gift-card' ), 'error' );
					} else {
						$mwb_wgm_method = $_POST['mwb_wgm_send_giftcard'];
						if ( $mwb_wgm_remove_validation_to == 'off' ) {
							if ( $mwb_wgm_method == 'Mail to recipient' ) {
								if ( ! isset( $_POST['mwb_wgm_to_email'] ) || empty( $_POST['mwb_wgm_to_email'] ) ) {
									$validate = false;
									wc_add_notice( __( 'Recipient Email: Field is empty.', 'woocommerce-ultimate-gift-card' ), 'error' );
								} elseif ( ! filter_var( $this->test_input( $_POST['mwb_wgm_to_email'] ), FILTER_VALIDATE_EMAIL ) ) {
									$validate = false;
									wc_add_notice( __( 'Recipient Email: Invalid email format', 'woocommerce-ultimate-gift-card' ), 'error' );
								}
							}
						}

						if ( $is_customizable != 'yes' ) {
							if ( $mwb_wgm_method == 'Mail to recipient' ) {
								if ( $mwb_wgm_remove_validation_to_name == 'off' ) {
									if ( ! isset( $_POST['mwb_wgm_to_name_optional'] ) || empty( $_POST['mwb_wgm_to_name_optional'] ) ) {
										$validate = false;
										wc_add_notice( __( 'Recipient Name: Field is empty.', 'woocommerce-ultimate-gift-card' ), 'error' );
									}
								}
							}
						}
					}
					if ( $mwb_wgm_remove_validation_msg == 'off' ) {
						if ( ! isset( $_POST['mwb_wgm_message'] ) || empty( $_POST['mwb_wgm_message'] ) ) {
							$validate = false;
							wc_add_notice( __( 'Message: Field is empty.', 'woocommerce-ultimate-gift-card' ), 'error' );
						} elseif ( strlen( trim( $_POST['mwb_wgm_message'] ) ) > $giftcard_message_length ) {
							$validate = false;
							$error_mesage = sprintf( __( '%1$sMessage: %2$sMessage length cannot exceed %3$s characters.', 'woocommerce-ultimate-gift-card' ), '<b>', '</b>', $giftcard_message_length );
							wc_add_notice( $error_mesage, 'error' );
						}
					}
					if ( $mwb_wgm_remove_validation_from == 'off' ) {
						if ( ! isset( $_POST['mwb_wgm_from_name'] ) || empty( $_POST['mwb_wgm_from_name'] ) ) {
							$validate = false;
							wc_add_notice( __( 'From: Field is empty.', 'woocommerce-ultimate-gift-card' ), 'error' );
						}
					}
				}
			}
			return $validate;
		}
		/**
		 * This function is used to remove tax from giftcard product
		 *
		 * @name mwb_wgm_woocommerce_product_is_taxable
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 * @return boolean
		 */

		function mwb_wgm_woocommerce_product_is_taxable( $taxable, $product ) {
			$giftcard_tax_cal_enable = get_option( 'mwb_wgm_general_setting_tax_cal_enable', 'off' );
			if ( $giftcard_tax_cal_enable == 'off' ) {
				$product_id = $product->get_id();
				$product_types = wp_get_object_terms( $product_id, 'product_type' );

				if ( isset( $product_types[0] ) ) {
					$product_type = $product_types[0]->slug;
					if ( $product_type == 'wgm_gift_card' ) {
						$taxable = false;
					}
				}
			}
			return $taxable;
		}
		/**
		 * Free shipping for giftcard product
		 *
		 * @name mwb_wgm_woocommerce_calculated_shipping
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 * @return boolean
		 */

		function mwb_wgm_woocommerce_calculated_shipping( $enable ) {
			$mwb_wgm_remove_validation = get_option( 'mwb_wgm_remove_validation', false );
			if ( is_checkout() || is_cart() ) {
				global $woocommerce;
				$gift_bool = false;
				$other_bool = false;
				$gift_bool_ship = false;
				$whole_cart = WC()->cart->cart_contents;
				if ( isset( $whole_cart ) && ! empty( $whole_cart ) ) {
					foreach ( $whole_cart as $cart_item_key => $cart_item ) {

						$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
						$product_types = wp_get_object_terms( $product_id, 'product_type' );
						if ( isset( $product_types[0] ) ) {
							$product_type = $product_types[0]->slug;
							if ( $product_type == 'wgm_gift_card' ) {
								if ( $cart_item['product_meta']['meta_data']['delivery_method'] == 'Mail to recipient' || $cart_item['product_meta']['meta_data']['delivery_method'] == 'Downloadable' ) {
									$gift_bool = true;
								} elseif ( $cart_item['product_meta']['meta_data']['delivery_method'] == 'Shipping' ) {
									$gift_bool_ship = true;
								}
							} else if ( ! $cart_item['data']->is_virtual() ) {
								$other_bool = true;
							}
						}
					}
					if ( $gift_bool && ! $gift_bool_ship && ! $other_bool ) {
						$enable = false;
					} else {
						$enable = true;
					}
				}
			}
			return $enable;
		}

		/**
		 * Enable the selected payment gateways for giftcard product
		 *
		 * @name mwb_wgm_woocommerce_available_payment_gateways
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */

		function mwb_wgm_woocommerce_available_payment_gateways( $payment_gateways ) {
			global $product_type;
			global $woocommerce;
			$mwb_wgm_gift_exist = false;
			$whole_cart = WC()->cart;
			if ( isset( $whole_cart ) && ! empty( $whole_cart ) ) {
				$count_1 = false;
				$get_cart = $whole_cart->cart_contents;
				foreach ( $get_cart as $cart_item_key => $cart_item ) {
					$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

					$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					$product_types = wp_get_object_terms( $product_id, 'product_type' );
					if ( isset( $product_types[0] ) ) {
						$product_type = $product_types[0]->slug;

						if ( $product_type == 'wgm_gift_card' ) {
							$mwb_wgm_gift_exist = true;
						} else {
							$count_1 = true;
						}
					}
				}

				if ( $mwb_wgm_gift_exist ) {
					if ( is_checkout() ) {
						if ( ! $count_1 ) {
							$giftcard_payment_gateways = get_option( 'mwb_wgm_general_setting_giftcard_payment', false );

							if ( isset( $giftcard_payment_gateways ) && ! empty( $giftcard_payment_gateways ) ) {
								if ( isset( $payment_gateways ) && ! empty( $payment_gateways ) ) {
									foreach ( $payment_gateways as $key => $payment_gateway ) {
										if ( ! in_array( $key, $giftcard_payment_gateways ) ) {
											unset( $payment_gateways[ $key ] );
										}
									}
								}
							}
						} else {
							$giftcard_payment_gateways = get_option( 'mwb_wgm_general_setting_giftcard_payment', false );

							if ( isset( $giftcard_payment_gateways ) && ! empty( $giftcard_payment_gateways ) ) {
								if ( isset( $payment_gateways ) && ! empty( $payment_gateways ) ) {
									foreach ( $payment_gateways as $key => $payment_gateway ) {
										if ( ! in_array( $key, $giftcard_payment_gateways ) && $key == 'cod' ) {
											unset( $payment_gateways['cod'] );
										}
									}
								}
							} else {
								if ( isset( $payment_gateways ) && ! empty( $payment_gateways ) ) {
									foreach ( $payment_gateways as $key => $payment_gateway ) {

										unset( $payment_gateways['cod'] );
									}
								}
							}
						}
					}
				}
			}
			return $payment_gateways;
		}
		/**
		 * This function is used to view email template shop page
		 *
		 * @name mwb_wgm_preview_email
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_preview_email() {
			$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format_1', false );
			if ( isset( $_GET['mwb_wgm_preview_email'] ) ) {
				if ( $_GET['mwb_wgm_preview_email'] == 'mwb_wgm_preview_email' ) {
					$product_id = $_GET['product_id'];

					$todaydate = date_i18n( 'Y-m-d' );
					$expiry_date = get_option( 'mwb_wgm_general_setting_giftcard_expiry', false );
					if ( $expiry_date > 0 || $expiry_date === 0 ) {
						if ( isset( $_GET['send_date'] ) && $_GET['send_date'] != null && $_GET['send_date'] != '' ) {
							$todaydate = $_GET['send_date'];
							if ( is_string( $todaydate ) ) {
								if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
									if ( $selected_date == 'd/m/Y' ) {
										$todaydate = str_replace( '/', '-', $todaydate );
									}
								}
							}
						}
						$expirydate = date_i18n( 'Y-m-d', strtotime( "$todaydate +$expiry_date day" ) );
						$expirydate_format = date_create( $expirydate );

						$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format_1', false );
						if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {

							$expirydate_format = date_i18n( $selected_date, strtotime( "$todaydate +$expiry_date day" ) );
						} else {
							$expirydate_format = date_format( $expirydate_format, 'jS M Y' );
						}
					} else {
						$expirydate_format = __( 'No Expiration', 'woocommerce-ultimate-gift-card' );
					}
					$giftcard_coupon_length_display = trim( get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 ) );
					if ( $giftcard_coupon_length_display == '' ) {
						$giftcard_coupon_length_display = 5;
					}
					$password = '';
					for ( $i = 0;$i < $giftcard_coupon_length_display;$i++ ) {
						$password .= 'x';
					}
					$giftcard_prefix = get_option( 'mwb_wgm_general_setting_giftcard_prefix', '' );
					$coupon = $giftcard_prefix . $password;

					$mwb_wgm_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );

					$templateids = isset( $mwb_wgm_pricing['template'] ) && ! empty( $mwb_wgm_pricing['template'] ) ? $mwb_wgm_pricing['template'] : false;

					$preferedid = isset( $mwb_wgm_pricing['by_default_tem'] ) ? $mwb_wgm_pricing['by_default_tem'] : '';

					$prefered_template_id = '';
					if ( is_array( $templateids ) && ! empty( $templateids ) ) {
						$prefered_template_id = $templateids[0];
					} elseif ( is_array( $templateids ) && ! empty( $preferedid ) ) {
						$prefered_template_id = $preferedid;

					} elseif ( is_array( $templateids ) && empty( $preferedid ) ) {
						$prefered_template_id = $templateids[0];
					} elseif ( ! is_array( $templateids ) && ! empty( $templateids ) ) {
						$prefered_template_id = $templateids;
					}

					$args['from'] = __( 'from@example.com', 'woocommerce-ultimate-gift-card' );
					$args['to'] = __( 'to@example.com', 'woocommerce-ultimate-gift-card' );
					$args['message'] = __( 'Your gift message will appear here which you send to your receiver. ', 'woocommerce-ultimate-gift-card' );
					$args['coupon'] = apply_filters( 'mwb_wgm_static_coupon_img', $coupon );
					$args['expirydate'] = $expirydate_format;
					$args['amount'] = wc_price( 100 );
					$args['templateid'] = $prefered_template_id;
					$args['product_id'] = $product_id;
					$args['order_id'] = '';
					$style = '<style>table, th, tr, td {
						border: medium none;
					}
					table, th, tr, td {
						border: 0px !important;
					}
						#mwb_wgm_email {
					width: 630px !important;
				}
				</style>';

				$giftcard_custom_css = get_option( 'mwb_wgm_other_setting_mail_style', false );
				$giftcard_custom_css = stripcslashes( $giftcard_custom_css );
				$style .= "<style>$giftcard_custom_css</style>";

				$message = $this->mwb_wgm_giftttemplate( $args );

				echo $finalhtml = $style . $message;
				die;
			}
			if ( $_GET['mwb_wgm_preview_email'] == 'mwb_wgm_single_page_popup' ) {
				$product_id = $_GET['product_id'];
				$product_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );
				$product_pricing_type = $product_pricing['type'];

				$is_imported = get_post_meta( $product_id, 'is_imported', true );
				if ( isset( $is_imported ) && ! empty( $is_imported ) && $is_imported == 'yes' ) {
					$coupon = 'XXXXX';
					$imported_exp_date = get_post_meta( $product_id, 'expiry_after_days', true );
					$expirydate_format = $this->mwb_wgm_check_expiry_date( $imported_exp_date );
				} else {
					$giftcard_coupon_length_display = trim( get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 ) );
					if ( $giftcard_coupon_length_display == '' ) {
						$giftcard_coupon_length_display = 5;
					}
					$password = '';
					for ( $i = 0;$i < $giftcard_coupon_length_display;$i++ ) {
						$password .= 'x';
					}
					$giftcard_prefix = get_option( 'mwb_wgm_general_setting_giftcard_prefix', '' );
					$coupon = $giftcard_prefix . $password;
					$expiry_date = get_option( 'mwb_wgm_general_setting_giftcard_expiry', false );
					$expirydate_format = $this->mwb_wgm_check_expiry_date( $expiry_date );
				}
				$tempId = isset( $_GET['tempId'] ) ? $_GET['tempId'] : '';
				if ( isset( $_GET['gift_manual_code'] ) && ! empty( $_GET['gift_manual_code'] ) ) {
					$coupon = $_GET['gift_manual_code'];
				}
				$mwb_wgm_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );
				$templateid = $mwb_wgm_pricing['template'];
				if ( is_array( $templateid ) && array_key_exists( 0, $templateid ) ) {
					$temp = $templateid[0];
				} else {
					$temp = $templateid;
				}
				$args['from'] = $_GET['from'];
				$args['to'] = $_GET['to'];
				$args['message'] = stripcslashes( $_GET['message'] );
				$args['coupon'] = apply_filters( 'mwb_wgm_qrcode_coupon', $coupon );
				$args['expirydate'] = $expirydate_format;

					// Added for currency switcher
				if ( class_exists( 'WOOCS' ) ) {
					global $WOOCS;
					$rate = 1;
					$currency = $WOOCS->current_currency;
					$currencies = $WOOCS->get_currencies();
					$rate = $currencies[ $currency ]['rate'];
					$cur_cur = $WOOCS->current_currency;
					$WOOCS->reset_currency();
					$WOOCS->set_currency( $cur_cur );

					$amt = $_GET['price'];

					if ( $product_pricing_type == 'mwb_wgm_range_price' ) {
						$amt = $_GET['price'];
					} elseif ( $product_pricing_type == 'mwb_wgm_user_price' ) {
						$amt = $_GET['price'];
					} else {
						$amt = floatval( $amt * $rate );
					}
					$args['amount'] = wc_price( $amt );
					} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {  // Added for price based on country
						if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {

							if ( $product_pricing_type == 'mwb_wgm_range_price' ) {
								$amt = $_GET['price'];
							} elseif ( $product_pricing_type == 'mwb_wgm_user_price' ) {
								$amt = $_GET['price'];
							} else {
								$amt = $_GET['price'];
								$amt = wcpbc_the_zone()->get_exchange_rate_price( $amt );
							}
							$args['amount'] = wc_price( $amt );
						} else {
							$args['amount'] = wc_price( $_GET['price'] );
						}
					} else {
						$decimal_separator  = get_option( 'woocommerce_price_decimal_sep' );
						$mwb_price = floatval( str_replace( $decimal_separator , '.',  $_GET['price'] ) );
						$args['amount'] = wc_price( $mwb_price );
					}

					$args['templateid'] = isset( $tempId ) && ! empty( $tempId ) ? $tempId : $temp;
					$args['product_id'] = $product_id;
					$args['order_id'] = '';
					$browse_enable = get_option( 'mwb_wgm_other_setting_browse', false );
					if ( $browse_enable == 'on' ) {
						if ( isset( $_GET['name'] ) && $_GET['name'] != null ) {
							$args['browse_image'] = $_GET['name'];
						}
					}
					$style = '<style>table, th, tr, td {
						border: medium none;
					}
					table, th, tr, td {
						border: 0px !important;
					}
						#mwb_wgm_email {
					width: 630px !important;
				}
				</style>';
				$giftcard_custom_css = get_option( 'mwb_wgm_other_setting_mail_style', false );
				$giftcard_custom_css = stripcslashes( $giftcard_custom_css );
				$style .= "<style>$giftcard_custom_css</style>";
				$message = $this->mwb_wgm_giftttemplate( $args );
				echo $finalhtml = $style . $message;
				die;
			}
		}
	}
		/**
		 * This function is used to add preview link on shop page
		 *
		 * @name mwb_wgm_woocommerce_after_shop_loop_item
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_woocommerce_after_shop_loop_item() {
			$mwb_wgm_enable = mwb_wgm_giftcard_enable();
			$mwb_wgm_preview_disable = get_option( 'mwb_wgm_additional_preview_disable', 'off' );
			if ( $mwb_wgm_enable ) {
				global $post;
				$product_id = $post->ID;
				$product_types = wp_get_object_terms( $product_id, 'product_type' );
				if ( isset( $product_types[0] ) ) {
					$product_type = $product_types[0]->slug;
					if ( $product_type == 'wgm_gift_card' ) {
						add_thickbox();
						$is_customizable = get_post_meta( $product_id, 'woocommerce_customizable_gift_card', true );
						if ( $mwb_wgm_preview_disable == 'off' && $is_customizable != 'yes' ) {
							?>
							<span class="mwb_price" >
								<a href="<?php echo home_url( "?mwb_wgm_preview_email=mwb_wgm_preview_email&product_id=$product_id" ); ?>&TB_iframe=true&width=630&height=500" class="thickbox"><?php _e( 'Preview', 'woocommerce-ultimate-gift-card' ); ?></a>	
							</span>
							<?php
						}
					}
				}
			}
		}

		/**
		 * This function is used to generate preview template on product single page
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 * @name mwb_wgm_preview_mail
		 */
		function mwb_wgm_preview_mail() {
			unset( $_POST['action'] );
			$_POST['mwb_wgm_preview_email'] = 'mwb_wgm_single_page_popup';
			$_POST['tempId'] = isset( $_POST['tempId'] ) ? stripcslashes( $_POST['tempId'] ) : '';
			$_POST['message'] = stripcslashes( $_POST['message'] );
			$uploadDirPath = MWB_UPLOAD_DIR . '/mwb_browse';
			if ( ! is_dir( $uploadDirPath ) ) {
				wp_mkdir_p( $uploadDirPath );
				chmod( $uploadDirPath, 0775 );
			}
			$browse_enable = get_option( 'mwb_wgm_other_setting_browse', false );
			if ( $browse_enable == 'on' ) {

				if ( isset( $_FILES['file'] ) && ( ( $_FILES['file']['type'] == 'image/gif' )
					|| ( $_FILES['file']['type'] == 'image/jpeg' )
					|| ( $_FILES['file']['type'] == 'image/jpg' )
					|| ( $_FILES['file']['type'] == 'image/pjpeg' )
					|| ( $_FILES['file']['type'] == 'image/x-png' )
					|| ( $_FILES['file']['type'] == 'image/png' ) ) ) {
					$file_name = $_FILES['file']['name'];
				$file_name = sanitize_file_name( $file_name );
				if ( ! file_exists( MWB_UPLOAD_DIR . '/mwb_browse/' . $file_name ) ) {
					move_uploaded_file( $_FILES['file']['tmp_name'], MWB_UPLOAD_DIR . '/mwb_browse/' . $file_name );
				}
				$_POST['name'] = $file_name;
			}
		}
		$_POST['width'] = '630';
		$_POST['height'] = '530';
		$_POST['TB_iframe'] = true;
		$query = http_build_query( $_POST );
		echo $ajax_url = home_url( "?$query" );
		die;
	}
		/**
		 * This function is used to add notification about expiry days after product purchase
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 * @name mwb_wgm_woocommerce_after_single_product_summary
		 */
		function mwb_wgm_woocommerce_after_single_product_summary() {
			global $post;
			$woo_ver = WC()->version;
			$product_id = $post->ID;
			$product_types = wp_get_object_terms( $product_id, 'product_type' );

			$mwb_wgm_hide_giftcard_notice = get_option( 'mwb_wgm_hide_giftcard_notice', 'off' );
			if ( isset( $product_types[0] ) ) {
				$product_type = $product_types[0]->slug;

				if ( $product_type == 'wgm_gift_card' && $mwb_wgm_hide_giftcard_notice == 'off' ) {
					$args = array(
						'posts_per_page'   => -1,
						'orderby'          => 'title',
						'order'            => 'asc',
						'post_type'        => 'shop_coupon',
						'post_status'      => 'publish',
					);
					$args['meta_query'] = array(
						array(
							'key' => 'mwb_wgm_imported_coupon',
							'value' => 'yes',
							'compare' => '==',
						),
					);
					$imported_coupons = get_posts( $args );
					$is_imported = get_post_meta( $product_id, 'is_imported', true );
					if ( isset( $is_imported ) && ! empty( $is_imported ) && $is_imported == 'yes' ) {
						$giftcard_expiry = get_post_meta( $product_id, 'expiry_after_days', true );
					} elseif ( ! empty( $imported_coupons ) ) {
						$imported_code = $imported_coupons[0]->post_title;
						$the_coupon = new WC_Coupon( $imported_code );
						if ( $woo_ver < '3.0.0' ) {
							$import_coupon_id = $the_coupon->id;
						} else {
							$import_coupon_id = $the_coupon->get_id();
						}
						$giftcard_expiry = get_post_meta( $import_coupon_id, 'mwb_wgm_expiry_date', true );
					} else {
						$giftcard_expiry = get_option( 'mwb_wgm_general_setting_giftcard_expiry', false );
					}
					if ( $giftcard_expiry > 0 ) {
						$days = $giftcard_expiry;
						?>
						<div class="mwb_wgm_expiry_notice clear">
							<h4><?php _e( 'Giftcard Notice', 'woocommerce-ultimate-gift-card' ); ?></h4>
							<p><?php echo sprintf( __( 'This gift card will expire  %s days after purchase.', 'woocommerce-ultimate-gift-card' ), $days ); ?></p>
						</div>
						<?php

					} elseif ( $giftcard_expiry === 0 ) {
						$days = 'same';
						?>
						<div class="mwb_wgm_expiry_notice clear">
							<h4><?php _e( 'Giftcard Notice', 'woocommerce-ultimate-gift-card' ); ?></h4>
							<p><?php echo sprintf( __( 'This gift card will expire %s days after purchase.', 'woocommerce-ultimate-gift-card' ), $days ); ?></p>
						</div>
						<?php
					} else {
						?>
						<div class="mwb_wgm_expiry_notice clear">
							<h4><?php _e( 'Giftcard Notice', 'woocommerce-ultimate-gift-card' ); ?></h4>
							<p><?php echo sprintf( __( 'Giftcard has no expiration.', 'woocommerce-ultimate-gift-card' ) ); ?></p>
						</div>
						<?php
					}
				}

				if ( $product_type == 'wgm_gift_card' ) {
					do_action( 'mwb_wgm_terms_and_condition' );
				}
			}

		}

		/**
		 * This function is used to add resend email button at order detail page on front end
		 *
		 * @name mwb_wgm_woocommerce_order_details_after_order_table
		 * @param  $order
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_woocommerce_order_details_after_order_table( $order ) {
			$resend_view_gift = false;
			$resend_view_ship = false;
			$resend_view_other_pro = false;
			$resend_view = false;
			$woo_ver = WC()->version;
			if ( $woo_ver < '3.0.0' ) {
				$order_id = $order->id;
				$order_status = $order->status;
			} else {
				$order_id = $order->get_id();
				$order_status = $order->get_status();
			}
			if ( $order_status == 'completed' || $order_status == 'processing' ) {
				$giftcard = false;
				foreach ( $order->get_items() as $item_id => $item ) {
					if ( $woo_ver < '3.0.0' ) {
						$_product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
					} else {
						$_product = apply_filters( 'woocommerce_order_item_product', $product = $item->get_product(), $item );
					}
					if ( isset( $_product ) && ! empty( $_product ) ) {
						$product_id = $_product->get_id();
					}
					if ( isset( $product_id ) && ! empty( $product_id ) ) {

						$product_types = wp_get_object_terms( $product_id, 'product_type' );

						if ( isset( $product_types[0] ) ) {
							$product_type = $product_types[0]->slug;
							if ( $product_type == 'wgm_gift_card' ) {
								$giftcard = true;
							}
						}
					}
					if ( $woo_ver < '3.0.0' ) {
						$product = $order->get_product_from_item( $item );
						if ( isset( $item['item_meta']['Delivery Method'] ) && ! empty( $item['item_meta']['Delivery Method'] ) ) {
							$delivery_method = $item['item_meta']['Delivery Method'][0];
						}
					} else {
						$product = $item->get_product();
						$item_meta_data = $item->get_meta_data();

						foreach ( $item_meta_data as $key => $value ) {
							if ( isset( $value->key ) && $value->key == 'Delivery Method' && ! empty( $value->value ) ) {
								$delivery_method = $value->value;
							}
						}
					}
					if ( isset( $delivery_method ) ) {
						if ( $delivery_method == 'Mail to recipient' || $delivery_method == 'Downloadable' ) {
							$resend_view_gift = true;
						} else {
							$resend_view_ship = false;
						}
					} else {
						$resend_view_other_pro = false;
					}

					// DOWNLOAD PDF IN THANKYOU ORDER PAGE
					if ( isset( $delivery_method ) && ! empty( $delivery_method ) && $delivery_method == 'Downloadable' ) {
						$mwb_wgm_pdf_enable = get_option( 'mwb_wgm_addition_pdf_enable', false );
						if ( isset( $mwb_wgm_pdf_enable ) && $mwb_wgm_pdf_enable == 'on' ) {
							$giftcoupon = get_post_meta( $order_id, "$order_id#$item_id", true );
							if ( isset( $giftcoupon ) && ! empty( $giftcoupon ) ) {
								foreach ( $giftcoupon as $key => $value ) {
									$uploadDirPath = MWB_UPLOAD_URL . '/giftcard_pdf/giftcard' . $order_id . $value . '.pdf';
									?>
									<a style="margin: 5px;" href="<?php echo $uploadDirPath; ?>" target="_blank"><?php _e( 'Download PDF', 'woocommerce-ultimate-gift-card' ); ?></a><br/>
									<?php
								}
							}
						}
					}
				}
				if ( $resend_view_gift && ! $resend_view_ship && ! $resend_view_other_pro ) {
					$resend_view = true;
				} else {
					$resend_view = false;
				}
				$mwb_wgm_resend_disable = get_option( 'mwb_wgm_additional_resend_disable', 'off' );
				if ( $giftcard && $resend_view ) {
					?>
					<style>
					#mwb_wgm_loader {
						background-color: rgba(255, 255, 255, 0.6);
						bottom: 0;
						height: 100%;
						left: 0;
						position: fixed;
						right: 0;
						top: 0;
						width: 100%;
						z-index: 99999;
					}

					#mwb_wgm_loader img {
						display: block;
						left: 0;
						margin: 0 auto;
						position: absolute;
						right: 0;
						top: 40%;
					}
				</style>
				<?php
				if ( $mwb_wgm_resend_disable == 'off' ) {
					?>
					<header><h4><?php _e( 'Resend Giftcard Email', 'woocommerce-ultimate-gift-card' ); ?></h4></header>
					<div id="mwb_wgm_loader" style="display: none;">
						<img src="<?php echo MWB_WGM_URL; ?>/assets/images/loading.gif">
					</div>
					<p><?php _e( 'If the recipient has not received your giftcard email then you can resend the email.', 'woocommerce-ultimate-gift-card' ); ?> </p>
					<p id="mwb_wgm_resend_mail_notification"></p>
					<input type="button" data-id="<?php echo $order_id; ?>" id="mwb_wgm_resend_mail_button" class="button button-primary" value="<?php _e( 'Resend Mail', 'woocommerce-ultimate-gift-card' ); ?>">
					<?php
				}
			}
		}
	}
		/**
		 * This function is used to add error notice section on product single page
		 *
		 * @name  mwb_wgm_woocommerce_before_main_content_notice
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_woocommerce_before_main_content_notice() {
			global $post;
			if ( isset( $post->ID ) ) {
				$product_id = $post->ID;
				$product_types = wp_get_object_terms( $product_id, 'product_type' );

				if ( isset( $product_types[0] ) ) {
					$product_type = $product_types[0]->slug;
					if ( $product_type == 'wgm_gift_card' ) {
						?>
						<div class="woocommerce-error" id="mwb_wgm_error_notice" style="display:none;"></div>
						<?php
					}
				}
			}
		}

		/**
		 * This function is used to enqueue script on single product page
		 *
		 * @name mwb_wgm_wp_enqueue_scripts
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_wp_enqueue_scripts() {

			$giftcard_message_length = trim( get_option( 'mwb_wgm_other_setting_giftcard_message_length', 300 ) );
			$mwb_wgm_render_product_custom_page = get_option( 'mwb_wgm_render_product_custom_page', 'off' );
			if ( empty( $giftcard_message_length ) ) {
				$giftcard_message_length = 300;
			}
			$schedule_date = '';
			$giftcard_selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_date', false );
			if ( isset( $giftcard_selected_date ) && $giftcard_selected_date != null && $giftcard_selected_date != '' ) {
				$schedule_date = $giftcard_selected_date;
			}
			$mwb_gw_decimal_separator = get_option( 'woocommerce_price_decimal_sep' );
			$mwb_wgm = array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'pricing_type' => array(),
				'product_id' => 0,
				'price_field' => sprintf( __( 'Price: %sField is empty', 'woocommerce-ultimate-gift-card' ), '</b>' ),
				'send_date' => sprintf( __( 'Send Date: %sField is empty', 'woocommerce-ultimate-gift-card' ), '</b>' ),
				'to_empty' => sprintf( __( 'Recipient Email: %sField is empty.', 'woocommerce-ultimate-gift-card' ), '</b>' ),
				'to_empty_name' => sprintf( __( 'Recipient Name: %sField is empty.', 'woocommerce-ultimate-gift-card' ), '</b>' ),
				'to_invalid' => sprintf( __( 'Recipient Email: %sInvalid email format.', 'woocommerce-ultimate-gift-card' ), '</b>' ),
				'from_empty' => sprintf( __( 'From: %sField is empty.', 'woocommerce-ultimate-gift-card' ), '</b>' ),
				'method_empty' => sprintf( __( 'Delivery Method: %sPlease Select One Method', 'woocommerce-ultimate-gift-card' ), '</b>' ),

				'msg_empty' => sprintf( __( 'Message: %sField is empty.', 'woocommerce-ultimate-gift-card' ), '</b>' ),
				'msg_length_err' => sprintf( __( 'Message: %1$sMessage length cannot exceed %2$s characters.', 'woocommerce-ultimate-gift-card' ), '</b>', $giftcard_message_length ),
				'msg_length' => $giftcard_message_length,
				'price_range' => sprintf( __( 'Price Range: %sPlease enter price within Range.', 'woocommerce-ultimate-gift-card' ), '</b>' ),
				'schedule_date' => $giftcard_selected_date,
				'browse_error' => __( 'Please browse image files only', 'woocommerce-ultimate-gift-card' ),
				'discount_price_message' => __( 'Discounted Giftcard Price: ', 'woocommerce-ultimate-gift-card' ),
				'coupon_message' => __( 'Giftcard Value: ', 'woocommerce-ultimate-gift-card' ),
				'price_range'=>sprintf( __("Price Range: %sPlease enter price within Range.",'giftware'),"</b>"),
				'invalid_price'=>sprintf( __("Price: %sEnter Valid Price",'giftware'),"</b>"),
				'decimal_separator' => $mwb_gw_decimal_separator,
			);

			if ( is_product() ) {

				global $post;
				$product_id = $post->ID;
				$product_types = wp_get_object_terms( $product_id, 'product_type' );
				if ( isset( $product_types[0] ) ) {
					$product_type = $product_types[0]->slug;
					if ( $product_type == 'wgm_gift_card' ) {
						$is_customizable = get_post_meta( $product_id, 'woocommerce_customizable_gift_card', true );
						wp_enqueue_script( 'jquery-ui-datepicker' );
						wp_enqueue_style( 'thickbox' );
						wp_enqueue_script( 'thickbox' );
						wp_enqueue_style( 'jquery-ui-css', MWB_WGM_URL . '/assets/css/jquery-ui.css' );
						$giftcard_selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_date', false );
						$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format', false );
						if ( ! isset( $selected_date ) || $selected_date == null || $selected_date == '' ) {
							$selected_date = 'yy/mm/dd';
						}
						if ( $selected_date == 'd.m.Y' ) {
							$selected_date = 'dd.mm.yy';
						}
						if ( class_exists( 'WOOCS' ) ) {
							global $WOOCS;
							$rate = 1;
							$currency = $WOOCS->current_currency;
							$currencies = $WOOCS->get_currencies();
							$rate = $currencies[ $currency ]['rate'];
							$decimal = $currencies[ $currency ]['decimals'];
							$mwb_wgm_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );

							if ( isset( $mwb_wgm_pricing['type'] ) ) {

								if ( $mwb_wgm_pricing['type'] == 'mwb_wgm_range_price' ) {
									$from_price = $mwb_wgm_pricing['from'];
									$to_price = $mwb_wgm_pricing['to'];

									$from_price = round( floatval( $from_price * $rate ), $decimal );
									$to_price = round( floatval( $to_price * $rate ), $decimal );

									$mwb_wgm_pricing['from'] = $from_price;
									$mwb_wgm_pricing['to'] = $to_price;
								}
							}
						} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {
							$mwb_wgm_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );
							if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
								if ( isset( $mwb_wgm_pricing['type'] ) ) {
									$product_pricing_type = $mwb_wgm_pricing['type'];
									if ( $product_pricing_type == 'mwb_wgm_range_price' ) {
										$from_price = $mwb_wgm_pricing['from'];
										$to_price = $mwb_wgm_pricing['to'];

										$from_price = wcpbc_the_zone()->get_exchange_rate_price( $from_price );
										$to_price = wcpbc_the_zone()->get_exchange_rate_price( $to_price );

										$mwb_wgm_pricing['from'] = $from_price;
										$mwb_wgm_pricing['to'] = $to_price;
									}
								}
							}
						} else {
							$mwb_wgm_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );
						}
						$mwb_wgm_email_to_recipient = get_post_meta( $product_id, 'mwb_wgm_email_to_recipient', true );
						$mwb_wgm_download = get_post_meta( $product_id, 'mwb_wgm_download', true );
						$mwb_wgm_shipping = get_post_meta( $product_id, 'mwb_wgm_shipping', true );
						$mwb_wgm_discount = get_post_meta( $product_id, 'mwb_wgm_discount', 'no' );
						$mwb_wgm_method_enable = get_option( 'mwb_wgm_send_giftcard', false );
						if ( $mwb_wgm_method_enable == false ) {
							$mwb_wgm_method_enable = 'normal_mail';
						}
						$discount_enable = get_option( 'mwb_wgm_discount_enable', false );
						$mwb_wgm_customer_selection = get_option( 'mwb_wgm_customer_selection', false );
						$gift_browse_enable = get_option( 'mwb_wgm_other_setting_browse', 'off' );
						$mwb_wgm_remove_validation_to = get_option( 'mwb_wgm_remove_validation_to', 'off' );

						$mwb_wgm_remove_validation_to_name = get_option( 'mwb_wgm_remove_validation_to_name', 'on' );

						$mwb_wgm_remove_validation_from = get_option( 'mwb_wgm_remove_validation_from', 'off' );
						$mwb_wgm_remove_validation_msg = get_option( 'mwb_wgm_remove_validation_msg', 'off' );
						$mwb_wgm['pricing_type'] = $mwb_wgm_pricing;
						$mwb_wgm['product_id'] = $product_id;
						$mwb_wgm['dateformat'] = $selected_date;
						$mwb_wgm['datenable'] = $giftcard_selected_date;
						$mwb_wgm['mwb_wgm_customer_selection'] = $mwb_wgm_customer_selection;
						$mwb_wgm['mwb_wgm_method_enable'] = $mwb_wgm_method_enable;
						$mwb_wgm['overwrite_mail'] = $mwb_wgm_email_to_recipient;
						$mwb_wgm['overwrite_download'] = $mwb_wgm_download;
						$mwb_wgm['overwrite_shipping'] = $mwb_wgm_shipping;
						$mwb_wgm['mwb_wgm_discount'] = $mwb_wgm_discount;
						$mwb_wgm['mwb_wgm_discount_enable'] = $discount_enable;
						$mwb_wgm['browseenable'] = $gift_browse_enable;
						$mwb_wgm['remove_validation_to'] = $mwb_wgm_remove_validation_to;

						$mwb_wgm['remove_validation_to_name'] = $mwb_wgm_remove_validation_to_name;
						$mwb_wgm['is_customizable_gift'] = $is_customizable;

						$mwb_wgm['remove_validation_from'] = $mwb_wgm_remove_validation_from;
						$mwb_wgm['remove_validation_msg'] = $mwb_wgm_remove_validation_msg;

						$mwb_wgm['mwb_wgm_nonce'] = wp_create_nonce( 'mwb-wgm-verify-nonce' );
						wp_register_script( 'mwb_wgm_product_single_script', MWB_WGM_URL . '/assets/js/woocommerce-ultimate-gift-card-product-single.js', array( 'jquery', 'jquery-ui-datepicker' ) );
						wp_localize_script( 'mwb_wgm_product_single_script', 'mwb_wgm', $mwb_wgm );
						wp_enqueue_script( 'mwb_wgm_product_single_script' );
						wp_enqueue_style( 'mwb_wgm_common_css', MWB_WGM_URL . 'assets/css/mwb_wgm_common.css' );
					}
				}
			} elseif ( $mwb_wgm_render_product_custom_page == 'on' ) {
				$mwb_wgm_selected_custom_page = get_option( 'mwb_wgm_custom_page_selection', array() );
				global $post;
				$slug = $post->post_name;
				$url = $_SERVER['REQUEST_URI'];
				$url_last_index = explode( '/', $url );
				$giftcard_selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_date', false );
				$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format', false );
				if ( ! isset( $selected_date ) || $selected_date == null || $selected_date == '' ) {
					$selected_date = 'yy/mm/dd';
				}
				$mwb_wgm['dateformat'] = $selected_date;
				$mwb_wgm['datenable'] = $giftcard_selected_date;
				if ( in_array( $slug, $url_last_index ) ) {
					wp_register_script( 'mwb_wgm_product_single_script', MWB_WGM_URL . '/assets/js/woocommerce-ultimate-gift-card-product-single.js', array( 'jquery', 'jquery-ui-datepicker' ) );
					wp_register_script( 'mwb_wgm_product_single_script', MWB_WGM_URL . '/assets/js/woocommerce-ultimate-gift-card-product-single.js', array( 'jquery', 'jquery-ui-datepicker' ) );
					wp_localize_script( 'mwb_wgm_product_single_script', 'mwb_wgm', $mwb_wgm );
					wp_enqueue_script( 'mwb_wgm_product_single_script' );
					wp_enqueue_style( 'mwb_wgm_common_css', MWB_WGM_URL . 'assets/css/mwb_wgm_common.css' );
					wp_enqueue_style( 'jquery-ui-css', MWB_WGM_URL . '/assets/css/jquery-ui.css' );
					wp_enqueue_style( 'thickbox' );
					wp_enqueue_script( 'thickbox' );
					wp_enqueue_style( 'mwb_wgm_common_css', MWB_WGM_URL . 'assets/css/mwb_wgm_common.css' );
				}
			}
			if ( is_checkout() || is_account_page() ) {
				$mwb_wgm['mwb_wgm_nonce'] = wp_create_nonce( 'mwb-wgm-verify-nonce' );
				wp_register_script( 'mwb_wgm_product_single_script', MWB_WGM_URL . '/assets/js/woocommerce-ultimate-gift-card-product-single.js', array( 'jquery', 'jquery-ui-datepicker' ) );
				wp_localize_script( 'mwb_wgm_product_single_script', 'mwb_wgm', $mwb_wgm );
				wp_enqueue_script( 'mwb_wgm_product_single_script' );
				wp_enqueue_style( 'mwb_wgm_common_css', MWB_WGM_URL . 'assets/css/mwb_wgm_common.css' );
			}
			if ( ! is_account_page() && ! is_cart() && ! is_checkout() && ! is_product() ) {

				$mwb_check = array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'empty'   => __( 'Fields cannot be empty!', 'woocommerce-ultimate-gift-card' ),
					'invalid_coupon'   => __( 'Entered Code is not Valid', 'woocommerce-ultimate-gift-card' ),
					'invalid_email'   => __( 'Entered Email is not Valid', 'woocommerce-ultimate-gift-card' ),
					'mwb_wgm_nonce'  => wp_create_nonce( 'mwb-wgm-verify-nonce' ),
				);
				wp_register_script( 'mwb_wgm_balance_check', MWB_WGM_URL . '/assets/js/mwb-wgm-balance-checker.js', array( 'jquery', 'jquery-ui-datepicker' ) );
				wp_localize_script( 'mwb_wgm_balance_check', 'mwb_check', $mwb_check );
				wp_enqueue_script( 'mwb_wgm_balance_check' );
				wp_enqueue_style( 'mwb_wgm_common_css', MWB_WGM_URL . 'assets/css/mwb_wgm_common.css' );

			}
		}
		/**
		 * This function is used to prevent the giftcard product to show on single page if disabled
		 *
		 * @name  mwb_wgm_woocommerce_before_main_content
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_woocommerce_before_main_content() {
			$mwb_wgm_enable = mwb_wgm_giftcard_enable();
			if ( $mwb_wgm_enable ) {
				$giftcard_shop_page = get_option( 'mwb_wgm_general_setting_shop_page_enable', 'off' );
				if ( $giftcard_shop_page != 'on' ) {
					if ( is_shop() ) {
						if ( ! is_product() ) {
							$term = __( 'Gift Card', 'woocommerce-ultimate-gift-card' );
							$taxonomy = 'product_cat';
							$term_exist = term_exists( $term, $taxonomy );
							$terms = get_term( $term_exist['term_id'], $taxonomy, ARRAY_A );
							$giftcard_category = $terms['slug'];

							$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

							$args = array(
								'post_type' => 'product',
								'paged'   => $paged,
							);
							$args['tax_query'] = array(
								array(
									'taxonomy' => $taxonomy,
									'terms' => $giftcard_category,
									'field' => 'slug',
									'operator' => 'NOT IN',
								),
							);
							query_posts( $args );
						}
					}
				}
			}
		}

		/**
		 * This function is used to prevent the giftcard product to show on product page if disabled
		 *
		 * @name mwb_wgm_woocommerce_product_query
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 * @param $q
		 */
		function mwb_wgm_woocommerce_product_query( $q ) {
			$mwb_wgm_enable = mwb_wgm_giftcard_enable();
			if ( $mwb_wgm_enable ) {
				$giftcard_shop_page = get_option( 'mwb_wgm_general_setting_shop_page_enable', 'off' );

				if ( $giftcard_shop_page != 'on' ) {
					if ( is_shop() ) {
						$tax_query = (array) $q->get( 'tax_query' );

						$tax_query[] = array(
							'taxonomy' => 'product_type',
							'field' => 'slug',
							'terms' => array( 'wgm_gift_card' ), // Don't display products in the wgm_gift_card category on the shop page.
							'operator' => 'NOT IN',
						);

						$q->set( 'tax_query', $tax_query );
					}
				}
			}
		}
		/**
		 * This function is used to show the product price at shop as well as product single page
		 *
		 * @name mwb_wgm_woocommerce_get_price_html
		 * @param string       $price_html
		 * @param product type $product
		 * @return string $price_html
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_woocommerce_get_price_html( $price_html, $product ) {
			$mwb_wgm_enable = mwb_wgm_giftcard_enable();
			$discount_applicable = false;
			$discount_enable = get_option( 'mwb_wgm_discount_enable', false );
			if ( $mwb_wgm_enable ) {
				$product_id = $product->get_id();
				if ( isset( $product_id ) ) {

					$product_types = wp_get_object_terms( $product_id, 'product_type' );
					$mwb_wgm_discount = get_post_meta( $product_id, 'mwb_wgm_discount', false );
					$discount_min = get_option( 'mwb_wgm_discount_minimum', array() );
					$discount_max = get_option( 'mwb_wgm_discount_maximum', array() );
					$discount_value = get_option( 'mwb_wgm_discount_current_type', array() );
					$discount_type = get_option( 'mwb_wgm_discount_type', 'mwb_wgm_fixed' );
					if ( isset( $product_types[0] ) ) {
						$product_type = $product_types[0]->slug;
						if ( $product_type == 'wgm_gift_card' ) {
							$product_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );

							if ( isset( $product_pricing ) && ! empty( $product_pricing ) ) {
								if ( isset( $product_pricing['type'] ) ) {
									$product_pricing_type = $product_pricing['type'];
									if ( $product_pricing_type == 'mwb_wgm_default_price' ) {
										$new_price = '';
										$default_price = $product_pricing['default_price'];

										if ( isset( $mwb_wgm_discount[0] ) && $mwb_wgm_discount[0] == 'yes' ) {
											if ( isset( $discount_enable ) && $discount_enable == 'on' ) {
												if ( isset( $discount_min ) && $discount_min != null && isset( $discount_max ) && $discount_max != null && isset( $discount_value ) && $discount_value != null ) {
													foreach ( $discount_min as $key => $value ) {
														$decimal_separator  = get_option( 'woocommerce_price_decimal_sep' );
														$default_price = floatval( str_replace( $decimal_separator , '.', $default_price ) );
														$discount_mini = floatval( str_replace( $decimal_separator , '.', $discount_min[ $key ] ) );
														$discount_maxi = floatval( str_replace( $decimal_separator , '.', $discount_max[ $key ] ) );
														$discount_val = floatval( str_replace( $decimal_separator , '.', $discount_value[ $key ] ) );	
														if ( $discount_mini <= $default_price && $default_price <= $discount_maxi ) {
															if ( $discount_type == 'mwb_wgm_percentage' ) {
																$new_price = $default_price - ( $default_price * $discount_val ) / 100;
															} else {
																if( $default_price > $discount_val ) {
																	$new_price = $default_price - $discount_val;
																} else {
																	$new_price = 0;
																}																
															}
															$discount_applicable = true;
														}
													}
												}
											}
											if ( $discount_applicable ) {
												if ( class_exists( 'WOOCS' ) ) { // for currency switcher
													global $WOOCS;
													$mwb_rate = 1;
													$mwb_currency = $WOOCS->current_currency;
													$mwb_currencies = $WOOCS->get_currencies();
													$mwb_rate = $mwb_currencies[ $mwb_currency ]['rate'];
													$WOOCS->reset_currency();
													$WOOCS->set_currency( $mwb_currency );

													$default_price = floatval( $default_price * $mwb_rate );
													$new_price = floatval( $new_price * $mwb_rate );

													$price_html = '<del>' . wc_price( $default_price ) . $product->get_price_suffix() . '</del><ins>' . wc_price( $new_price ) . $product->get_price_suffix() . '</ins>';
												} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {  // for price based on country.

													if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
														$default_price = wcpbc_the_zone()->get_exchange_rate_price( $default_price );
														$new_price = wcpbc_the_zone()->get_exchange_rate_price( $new_price );
													}
													$price_html = '<del>' . wc_price( $default_price ) . $product->get_price_suffix() . '</del><ins>' . wc_price( $new_price ) . $product->get_price_suffix() . '</ins>';
												} else {
													$price_html = '<del>' . wc_price( $default_price ) . $product->get_price_suffix() . '</del><ins>' . wc_price( $new_price ) . $product->get_price_suffix() . '</ins>';
												}
											}
											else {

											}
										} 
										else {
											$price_html = $price_html;
										}
									}
									if ( $product_pricing_type == 'mwb_wgm_range_price' ) {
										$price_html = '';
										$from_price = $product_pricing['from'];
										$to_price = $product_pricing['to'];

										if ( class_exists( 'WOOCS' ) ) {
											global $WOOCS;
											$mwb_rate = 1;
											$mwb_currency = $WOOCS->current_currency;
											$mwb_currencies = $WOOCS->get_currencies();
											$mwb_rate = $mwb_currencies[ $mwb_currency ]['rate'];
											$WOOCS->reset_currency();
											$WOOCS->set_currency( $mwb_currency );

											$from_price = $product_pricing['from'];
											$from_price = floatval( $from_price * $mwb_rate );

											$to_price = $product_pricing['to'];
											$to_price = floatval( $to_price * $mwb_rate );

											$price_html .= '<ins><span class="woocommerce-Price-amount amount">' . wc_price( $from_price ) . ' - ' . wc_price( $to_price ) . '</span></ins>';

										} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {
											if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
												$from_price = wcpbc_the_zone()->get_exchange_rate_price( $from_price );
												$to_price = wcpbc_the_zone()->get_exchange_rate_price( $to_price );
											}
											$price_html .= '<ins><span class="woocommerce-Price-amount amount">' . wc_price( $from_price ) . ' - ' . wc_price( $to_price ) . '</span></ins>';
										} else {
											$decimal_separator  = get_option( 'woocommerce_price_decimal_sep' );
											$from_price = floatval( str_replace( $decimal_separator , '.', $from_price ) );	
											$to_price = floatval( str_replace( $decimal_separator , '.', $to_price ) );	
											$price_html .= '<ins><span class="woocommerce-Price-amount amount">' . wc_price( $from_price ) . ' - ' . wc_price( $to_price ) . '</span></ins>';
										}
									}
									if ( $product_pricing_type == 'mwb_wgm_selected_price' ) {
										$selected_price = $product_pricing['price'];
										if ( ! empty( $selected_price ) ) {
											$selected_prices = explode( '|', $selected_price );

											if ( isset( $selected_prices ) && ! empty( $selected_prices ) ) {
												$price_html = '';
												$price_html .= '<ins><span class="woocommerce-Price-amount amount">';
												$last_range = max( $selected_prices );
												$start_range = min( $selected_prices );
												if ( class_exists( 'WOOCS' ) ) {
													global $WOOCS;
													$mwb_rate = 1;
													$mwb_currency = $WOOCS->current_currency;
													$mwb_currencies = $WOOCS->get_currencies();
													$mwb_rate = $mwb_currencies[ $mwb_currency ]['rate'];
													$WOOCS->reset_currency();
													$WOOCS->set_currency( $mwb_currency );
													$last_range = floatval( $last_range * $mwb_rate );
													$start_range = floatval( $start_range * $mwb_rate );
													$price_html .= wc_price( $start_range ) . '-' . wc_price( $last_range );

												} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {

													if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
														$last_range = wcpbc_the_zone()->get_exchange_rate_price( $last_range );
														$start_range = wcpbc_the_zone()->get_exchange_rate_price( $start_range );
													}
													$price_html .= wc_price( $start_range ) . '-' . wc_price( $last_range );
												} else {

													$decimal_separator  = get_option( 'woocommerce_price_decimal_sep' );
													$start_range = floatval( str_replace( $decimal_separator , '.', $start_range ) );	
													$last_range = floatval( str_replace( $decimal_separator , '.', $last_range ) );		
													$price_html .= wc_price( $start_range ) . ' - ' . wc_price( $last_range );
												}
												$price_html .= '</span></ins>';
											}
										}
									}
									if ( $product_pricing_type == 'mwb_wgm_user_price' ) {
										$price_html = apply_filters( 'mwb_wgm_user_price_text', __( 'Enter Giftcard Value:', 'woocommerce-ultimate-gift-card' ) );
									}
									if ( $product_pricing_type == 'mwb_wgm_variable_price' ) {
										$mwb_variation_price = $product_pricing['mwb_wgm_variation_price'];
										if ( isset( $mwb_variation_price ) && ! empty( $mwb_variation_price ) && is_array( $mwb_variation_price ) ) {
											$start_price = min( $mwb_variation_price );
											$end_price = ( max( $mwb_variation_price ) == '' ) ? $start_price : max( $mwb_variation_price );
											if ( class_exists( 'WCPBC_Pricing_Zone' ) ) {
												if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
													$start_price = wcpbc_the_zone()->get_exchange_rate_price( $start_price );
													$end_price = wcpbc_the_zone()->get_exchange_rate_price( $end_price );
												}
											}
											$decimal_separator  = get_option( 'woocommerce_price_decimal_sep' );
											$start_price = floatval( str_replace( $decimal_separator , '.', $start_price ) );	
											$end_price = floatval( str_replace( $decimal_separator , '.', $end_price ) );		
											$price_html = '<span>' . wc_price( $start_price ) . ' - ' . wc_price( $end_price ) . '</span>';
										}
									}
								}
							}
						}
					}
				}
				$price_html = apply_filters( 'mwb_wgm_pricing_html', $price_html );
			}
			return $price_html;
		}

		/**
		 * This function is used to replace add to cart button to view giftcard
		 *
		 * @name mwb_wgm_woocommerce_loop_add_to_cart_link
		 * @param $link
		 * @param $product
		 * @return string
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_woocommerce_loop_add_to_cart_link( $link, $product ) {
			$mwb_wgm_enable = mwb_wgm_giftcard_enable();
			if ( $mwb_wgm_enable ) {
				$product_id = $product->get_id();
				if ( isset( $product_id ) ) {
					$product_types = wp_get_object_terms( $product_id, 'product_type' );
					if ( isset( $product_types[0] ) ) {
						$product_type = $product_types[0]->slug;
						if ( $product_type == 'wgm_gift_card' ) {
							$product_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );
							if ( isset( $product_pricing ) && ! empty( $product_pricing ) ) {
								$link = sprintf(
									'<a rel="nofollow" href="%s" class="%s">%s</a>',
									esc_url( get_the_permalink() ),
									esc_attr( isset( $class ) ? $class : 'button' ),
									esc_html( apply_filters( 'mwb_wgm_view_card_text', __( 'VIEW CARD', 'woocommerce-ultimate-gift-card' ) ) )
								);
							}
						}
					}
				}
			}
			return $link;
		}

		/**
		 * This function is used to add field on product single page
		 *
		 * @name mwb_wgm_woocommerce_before_add_to_cart_button
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 * @param $product
		 * @return string
		 */
		function mwb_wgm_woocommerce_before_add_to_cart_button( $product ) {
			global $product;
			if ( isset( $product ) && ! empty( $product ) ) {
				$mwb_wgm_enable = mwb_wgm_giftcard_enable();
				$discount_applicable = false;
				$discount_enable = get_option( 'mwb_wgm_discount_enable', false );
				$mwb_wgm_method_enable = get_option( 'mwb_wgm_send_giftcard', false );
				$discount_min = get_option( 'mwb_wgm_discount_minimum', array() );
				$discount_max = get_option( 'mwb_wgm_discount_maximum', array() );
				$discount_value = get_option( 'mwb_wgm_discount_current_type', array() );
				$discount_type = get_option( 'mwb_wgm_discount_type', 'mwb_wgm_fixed' );
				$mwb_wgm_mail_to_recipient_text = stripcslashes( get_option( 'mwb_wgm_mail_to_recipient_text', 'Email To Recipient' ) );
				$mwb_wgm_shipping_text = stripslashes( get_option( 'mwb_wgm_shipping_text', 'Want To Ship Your Card' ) );
				$mwb_wgm_downloadable_text = stripslashes( get_option( 'mwb_wgm_downloadable_text', 'You Print & Give To Recipient' ) );
				$mwb_wgm_mail_to_recipient_desc = stripslashes( get_option( 'mwb_wgm_mail_to_recipient_desc', 'We will send it to recipient email address.' ) );
				$mwb_wgm_downloadable_desc = stripslashes( get_option( 'mwb_wgm_downloadable_desc', 'After checking out, you can print your giftcard' ) );
				$mwb_wgm_shipping_desc = stripcslashes( get_option( 'mwb_wgm_shipping_desc', 'We will ship your card' ) );
				if ( $mwb_wgm_method_enable == false ) {
					$mwb_wgm_method_enable = 'normal_mail';
				}
				$mwb_wgm_customer_selection = get_option( 'mwb_wgm_customer_selection', false );
				if ( $mwb_wgm_enable ) {
					$woo_ver = WC()->version;
					if ( $woo_ver < '3.0.0' ) {
						$product_id = $product->id;

					} else {
						$product_id = $product->get_id();
					}
					if ( isset( $product_id ) ) {

						$product_types = wp_get_object_terms( $product_id, 'product_type' );
						$mwb_wgm_discount = get_post_meta( $product_id, 'mwb_wgm_discount', false );
						if ( isset( $product_types[0] ) ) {
							$product_type = $product_types[0]->slug;
							if ( $product_type == 'wgm_gift_card' ) {
								$product_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );
								$decimal_separator  = get_option( 'woocommerce_price_decimal_sep', true );	
								if ( isset( $product_pricing ) && ! empty( $product_pricing ) ) {
									?>
									<div class="mwb_wgm_added_wrapper">
										<?php
										if ( isset( $product_pricing['type'] ) ) {
											$product_pricing_type = $product_pricing['type'];
											if ( $product_pricing_type == 'mwb_wgm_range_price' ) {
												$default_price = $product_pricing['default_price'];

												$from_price = $product_pricing['from'];
												$to_price = $product_pricing['to'];

												$decimal_separator  = get_option( 'woocommerce_price_decimal_sep' );
												$from_price = floatval( str_replace( $decimal_separator , '.', $from_price ) );
												$to_price = floatval( str_replace( $decimal_separator , '.', $to_price ) );
												$default_price = floatval( str_replace( $decimal_separator , '.', $default_price ) );	
												$text_box_price = ( $default_price >= $from_price && $default_price <= $to_price ) ? $default_price : $from_price;
												
												if ( $discount_enable && $discount_enable == 'on' ) {
													if ( isset( $mwb_wgm_discount[0] ) && $mwb_wgm_discount[0] == 'yes' ) {
														if ( isset( $discount_min ) && $discount_min != null && isset( $discount_max ) && $discount_max != null && isset( $discount_value ) && $discount_value != null ) {
															foreach ( $discount_min as $key => $value ) {
																$discount_mini = floatval( str_replace( $decimal_separator , '.', $discount_min[ $key ] ) );
																$discount_maxi = floatval( str_replace( $decimal_separator , '.', $discount_max[ $key ] ) );
																$discount_val = floatval( str_replace( $decimal_separator , '.', $discount_value[ $key ] ) );													

																if ( $discount_mini <= $text_box_price && $text_box_price <= $discount_maxi ) {
																	if ( $discount_type == 'mwb_wgm_percentage' ) {
																		$new_price_range = $text_box_price - ( $text_box_price * $discount_val ) / 100;
																	} else {
																		if( $text_box_price > $discount_val ) {
																			$new_price_range = $text_box_price - $discount_val;	
																		} else{
																			$new_price_range = 0;
																		}
																	}
																	$discount_applicable = true;
																}
															}
														}
													}
												}

												if ( $discount_applicable ) {
													if ( class_exists( 'WOOCS' ) ) {
														global $WOOCS;
														$rate = 1;
														$currency = $WOOCS->current_currency;
														$currencies = $WOOCS->get_currencies();
														$rate = $currencies[ $currency ]['rate'];
														$cur_cur = $WOOCS->current_currency;
														$WOOCS->reset_currency();
														$WOOCS->set_currency( $cur_cur );
														$new_price_range = floatval( $new_price_range ) * $rate;
														$text_box_price = floatval( $text_box_price ) * $rate;

														?>
														<div class="mwb_wgm_price_content">
															<b style="color:green;">
																<?php
																_e( 'Discounted Gift Card Price: ', 'woocommerce-ultimate-gift-card' );
																echo wc_price( $new_price_range );
																?>
															</b><br/>
															<b style="color:green;">
																<?php
																_e( 'Giftcard Value: ', 'woocommerce-ultimate-gift-card' );
																echo wc_price( $text_box_price );
																?>
															</b>
														</div>

														<?php
													} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {
														if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
															$new_price_range = wcpbc_the_zone()->get_exchange_rate_price( $new_price_range );
															$text_box_price = wcpbc_the_zone()->get_exchange_rate_price( $text_box_price );
														}
														?>
														<div class="mwb_wgm_price_content">
															<b style="color:green;">
																<?php
																_e( 'Discounted Gift Card Price: ', 'woocommerce-ultimate-gift-card' );
																echo wc_price( $new_price_range );
																?>
															</b><br/>
															<b style="color:green;">
																<?php
																_e( 'Giftcard Value: ', 'woocommerce-ultimate-gift-card' );
																echo wc_price( $text_box_price );
																?>
															</b>
														</div>
														<?php
													} else {

														?>
														<div class="mwb_wgm_price_content">
															<b style="color:green;">
																<?php
																_e( 'Discounted Gift Card Price: ', 'woocommerce-ultimate-gift-card' );
																echo wc_price( $new_price_range );
																?>
															</b><br/>
															<b style="color:green;">
																<?php
																_e( 'Giftcard Value: ', 'woocommerce-ultimate-gift-card' );
																echo wc_price( $text_box_price );
																?>
															</b>
														</div>
														<?php
													}
												}
												if ( class_exists( 'WOOCS' ) ) {
													global $WOOCS;
													$rate = 1;
													$currency = $WOOCS->current_currency;
													$currencies = $WOOCS->get_currencies();
													$rate = $currencies[ $currency ]['rate'];
													$decimal = $currencies[ $currency ]['decimals'];
													$cur_cur = $WOOCS->current_currency;
													$WOOCS->reset_currency();
													$WOOCS->set_currency( $cur_cur );

													$default_price = round( floatval( $default_price * $rate ), $decimal );
													$from_price = round( floatval( $from_price * $rate ), $decimal );
													$to_price = round( floatval( $to_price * $rate ), $decimal );

													?>
													<p class="mwb_wgm_section">
														<label><?php _e( 'Enter Price Within Above Range', 'woocommerce-ultimate-gift-card' ); ?>:</label>	
														<input type="number"  class="input-text mwb_wgm_price" id="mwb_wgm_price" name="mwb_wgm_price" value="<?php echo ( $default_price >= $from_price && $default_price <= $to_price ) ? $default_price : $from_price; ?>" max="<?php echo $to_price; ?>" min="<?php echo $from_price; ?>">
													</p>
													<?php

												} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {
													if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
														$default_price = wcpbc_the_zone()->get_exchange_rate_price( $default_price );
														$to_price = wcpbc_the_zone()->get_exchange_rate_price( $to_price );
														$from_price = wcpbc_the_zone()->get_exchange_rate_price( $from_price );
													}
													?>
													<p class="mwb_wgm_section">
														<label><?php _e( 'Enter Price Within Above Range', 'woocommerce-ultimate-gift-card' ); ?>:</label>	
														<input type="number" class="input-text mwb_wgm_price" id="mwb_wgm_price" name="mwb_wgm_price" value="<?php echo ( $default_price >= $from_price && $default_price <= $to_price ) ? $default_price : $from_price; ?>" max="<?php echo $to_price; ?>" min="<?php echo $from_price; ?>">
													</p>
													<?php
												} else {
													$text_box_price = str_replace( '.' , $decimal_separator , $text_box_price );	
													?>
													<p class="mwb_wgm_section">
														<label><?php _e( 'Enter Price Within Above Range', 'woocommerce-ultimate-gift-card' ); ?>:</label>	
														<input type="text" class="input-text mwb_wgm_price" id="mwb_wgm_price" name="mwb_wgm_price" value="<?php echo $text_box_price; ?>">
													</p>
													<?php
												}
											}

											if ( $product_pricing_type == 'mwb_wgm_default_price' ) {
												$default_price = $product_pricing['default_price'];
												?>
												<input type="hidden" class="mwb_wgm_price" id="mwb_wgm_price" name="mwb_wgm_price" value="<?php echo $default_price; ?>">
												<?php
												if ( isset( $discount_enable ) && $discount_enable == 'on' ) {
													if ( isset( $mwb_wgm_discount[0] ) && $mwb_wgm_discount[0] == 'yes' ) {
													if ( class_exists( 'WOOCS' ) ) { // for currency switcher
														global $WOOCS;
														$rate = 1;
														$currency = $WOOCS->current_currency;
														$currencies = $WOOCS->get_currencies();
														$rate = $currencies[ $currency ]['rate'];
														$cur_cur = $WOOCS->current_currency;
														$WOOCS->reset_currency();
														$WOOCS->set_currency( $cur_cur );
														$default_price = floatval( $default_price ) * $rate;
														?>
														<span style="color:green;">
															<?php
															_e( 'Coupon Amount will be: ', 'woocommerce-ultimate-gift-card' );
															echo wc_price( $default_price );
															?>
														</span>
														<?php
													} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) { // for price based on country
														if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
															$default_price = wcpbc_the_zone()->get_exchange_rate_price( $default_price );
														}
														?>
														<span style="color:green;">
															<?php
															_e( 'Coupon Amount will be: ', 'woocommerce-ultimate-gift-card' );
															echo wc_price( $default_price );
															?>
														</span>
														<?php
													} else {
														?>
														<span style="color:green;">
															<?php
															_e( 'Coupon Amount will be: ', 'woocommerce-ultimate-gift-card' );
															echo $default_price;
															?>
														</span>
														<?php
													}
												}
											}
										}

										if ( $product_pricing_type == 'mwb_wgm_selected_price' ) {
											$default_price = $product_pricing['default_price'];
											$selected_price = $product_pricing['price'];
											if ( ! empty( $selected_price ) ) {
												?>
												<p class="mwb_wgm_section">
													<label><?php _e( 'Choose Gift Card Selected Price', 'woocommerce-ultimate-gift-card' ); ?>:</label><br/>	

													<?php
													$selected_prices = explode( '|', $selected_price );

													if ( isset( $selected_prices ) && ! empty( $selected_prices ) ) {
														?>
														<select name="mwb_wgm_price" class="mwb_wgm_price" id="mwb_wgm_price" >
															<?php
															foreach ( $selected_prices as $price ) {
																if ( class_exists( 'WOOCS' ) ) {
																	global $WOOCS;
																	$rate = 1;
																	$currency = $WOOCS->current_currency;
																	$currencies = $WOOCS->get_currencies();
																	$rate = $currencies[ $currency ]['rate'];
																	$cur_cur = $WOOCS->current_currency;
																	$WOOCS->reset_currency();
																	$WOOCS->set_currency( $cur_cur );
																	$default_price = $product_pricing['default_price'];
																	$default_price = floatval( $default_price ) * $rate;
																	$prices = floatval( $price ) * $rate;

																	if ( $prices == $default_price ) {
																		?>
																		<option  value="<?php echo $price; ?>" selected><?php echo wc_price( $prices ); ?></option>
																		<?php
																	} else {
																		?>
																		<option  value="<?php echo $price; ?>"><?php echo wc_price( $prices ); ?></option>
																		<?php
																	}
																} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {

																	if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
																		$default_price = wcpbc_the_zone()->get_exchange_rate_price( $default_price );
																		$prices = wcpbc_the_zone()->get_exchange_rate_price( $price );
																		if ( $prices == $default_price ) {
																			?>
																			<option  value="<?php echo $price; ?>" selected><?php echo wc_price( $prices ); ?></option>
																			<?php
																		} else {
																			?>
																			<option  value="<?php echo $price; ?>"><?php echo wc_price( $prices ); ?></option>
																			<?php
																		}
																	} else {
																		if ( $price == $default_price ) {
																			?>
																			<option  value="<?php echo $price; ?>" selected><?php echo wc_price( $price ); ?></option>
																			<?php
																		} else {
																			?>
																			<option  value="<?php echo $price; ?>"><?php echo wc_price( $price ); ?></option>
																			<?php
																		}
																	}
																} else {
																	$decimal_separator  = get_option( 'woocommerce_price_decimal_sep' );							
																	$price = floatval( str_replace( $decimal_separator , '.', $price ) );
																	if ( $price == $default_price ) {
																		?>
																		<option  value="<?php echo $price; ?>" selected><?php echo wc_price( $price ); ?></option>
																		<?php
																	} else {
																		?>
																		<option  value="<?php echo $price; ?>"><?php echo wc_price( $price ); ?></option>
																		<?php
																	}
																}
															}
															?>
														</select>
													<?php } ?>
												</p>	
												<?php
											}
										}
										if ( $product_pricing_type == 'mwb_wgm_user_price' ) {
											$default_price = $product_pricing['default_price'];

											if ( $discount_enable && $discount_enable == 'on' ) {

												if ( isset( $mwb_wgm_discount[0] ) && $mwb_wgm_discount[0] == 'yes' ) {

													if ( isset( $discount_min ) && $discount_min != null && isset( $discount_max ) && $discount_max != null && isset( $discount_value ) && $discount_value != null ) {
														foreach ( $discount_min as $key => $value ) {
															$decimal_separator  = get_option( 'woocommerce_price_decimal_sep' );
															$default_price = floatval( str_replace( $decimal_separator , '.', $default_price ) );
															$discount_mini = floatval( str_replace( $decimal_separator , '.', $discount_min[ $key ] ) );
															$discount_maxi = floatval( str_replace( $decimal_separator , '.', $discount_max[ $key ] ) );
															$discount_val = floatval( str_replace( $decimal_separator , '.', $discount_value[ $key ] ) );


															if ( $discount_mini <= $default_price && $default_price <= $discount_maxi ) {
																if ( $discount_type == 'mwb_wgm_percentage' ) {
																	$new_price_user = $default_price - ( $default_price * $discount_val ) / 100;
																} else {
																	if( $default_price > $discount_val ) {
																		$new_price_user = $default_price - $discount_val;
																	} else{
																		$new_price_user = 0;
																	}
																	
																}
																$discount_applicable = true;
															}
														}
													}
												}
											}

											if ( $discount_applicable ) {
												if ( class_exists( 'WOOCS' ) ) {
													global $WOOCS;
													$rate = 1;
													$currency = $WOOCS->current_currency;
													$currencies = $WOOCS->get_currencies();
													$rate = $currencies[ $currency ]['rate'];
													$cur_cur = $WOOCS->current_currency;
													$WOOCS->reset_currency();
													$WOOCS->set_currency( $cur_cur );

													$new_price_user = floatval( $new_price_user ) * $rate;
													$default_price = floatval( $default_price ) * $rate;

													?>
													<div class="mwb_wgm_price_content">

														<b style="color:green;">
															<?php
															_e( 'Discounted Gift Card Price: ', 'woocommerce-ultimate-gift-card' );
															echo wc_price( $new_price_user );
															?>
														</b><br/>
														<b style="color:green;">
															<?php
															_e( 'Giftcard Value: ', 'woocommerce-ultimate-gift-card' );
															echo wc_price( $default_price );
															?>
														</b>

													</div>
													<?php
												} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {
													if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
														$new_price_user = wcpbc_the_zone()->get_exchange_rate_price( $new_price_user );
														$default_price = wcpbc_the_zone()->get_exchange_rate_price( $default_price );
													}
													?>
													<div class="mwb_wgm_price_content">
														<b style="color:green;">
															<?php
															_e( 'Discounted Gift Card Price: ', 'woocommerce-ultimate-gift-card' );
															echo wc_price( $new_price_user );
															?>
														</b><br/>
														<b style="color:green;">
															<?php
															_e( 'Giftcard Value: ', 'woocommerce-ultimate-gift-card' );
															echo wc_price( $default_price );
															?>
														</b>
													</div>
													<?php
												} else {
													?>
													<div class="mwb_wgm_price_content">
														<b style="color:green;">
															<?php
															_e( 'Discounted Gift Card Price: ', 'woocommerce-ultimate-gift-card' );
															echo wc_price( $new_price_user );
															?>
														</b><br/>
														<b style="color:green;">
															<?php
															_e( 'Giftcard Value: ', 'woocommerce-ultimate-gift-card' );
															echo wc_price( $default_price );
															?>
														</b>

													</div>
													<?php
												}
											}
											if ( class_exists( 'WOOCS' ) ) {
												global $WOOCS;
												$rate = 1;
												$currency = $WOOCS->current_currency;
												$currencies = $WOOCS->get_currencies();
												$rate = $currencies[ $currency ]['rate'];
												$decimal = $currencies[ $currency ]['decimals'];
												$cur_cur = $WOOCS->current_currency;
												$WOOCS->reset_currency();
												$WOOCS->set_currency( $cur_cur );
												$default_price = $product_pricing['default_price'];
												$default_price = round( floatval( $default_price * $rate ), $decimal );
												?>
												<p class="mwb_wgm_section">
													<label><?php _e( 'Enter Gift Card Price', 'woocommerce-ultimate-gift-card' ); ?>:</label>	
													<input type="number" class="mwb_wgm_price" id="mwb_wgm_price" name="mwb_wgm_price" min="1" value=<?php echo $default_price; ?>>
												</p>	
												<?php
											} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {
												$default_price = $product_pricing['default_price'];
												if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
													$default_price = wcpbc_the_zone()->get_exchange_rate_price( $default_price );
												}
												?>
												<p class="mwb_wgm_section">
													<label><?php _e( 'Enter Gift Card Price', 'woocommerce-ultimate-gift-card' ); ?>:</label>	
													<input type="number" class="mwb_wgm_price" id="mwb_wgm_price" name="mwb_wgm_price" min="1" value=<?php echo $default_price; ?>>
												</p>	
												<?php
											} else {
													$default_price =  str_replace( '.', $decimal_separator , $default_price );
												?>
												<p class="mwb_wgm_section">
													<label><?php _e( 'Enter Gift Card Price', 'woocommerce-ultimate-gift-card' ); ?>:</label>	
													<input type="text" class="mwb_wgm_price" id="mwb_wgm_price" name="mwb_wgm_price" value=<?php echo $default_price; ?>>
												</p>	
												<?php
											}
										}
										if ( $product_pricing_type == 'mwb_wgm_variable_price' ) {
											?>
											<?php
											$variation_amount = $product_pricing['mwb_wgm_variation_price'];
											$varable_text = $product_pricing['mwb_wgm_variation_text'];
										
											if ( isset( $variation_amount ) && is_array( $variation_amount ) && ! empty( $variation_amount ) ) {
												$mwb_price = ( $variation_amount[0] != '' ) ? $variation_amount[0] : 0;
												if ( class_exists( 'WCPBC_Pricing_Zone' ) ) {
													if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
														$mwb_price = wcpbc_the_zone()->get_exchange_rate_price( $mwb_price );
													}
												}
												$decimal_separator  = get_option( 'woocommerce_price_decimal_sep' );
												$mwb_price = floatval( str_replace( $decimal_separator , '.', $mwb_price ) );
												?>
												<p class="mwb_wgm_section">
													<span id="mwb_wgm_text" class="mwb_variable_currency"><?php echo( wc_price( $mwb_price ) ); ?></span>
												</p>
												<p class="mwb_wgm_section">
													<select name="mwb_wgm_price" class="mwb_wgm_price" id="mwb_wgm_price">
														<?php
														foreach ( $variation_amount as $key => $value ) {
															if ( isset( $value ) && ! empty( $value ) ) {
																?>
																<option value="<?php echo $value; ?>"><?php echo ( $varable_text[ $key ] ); ?></option>
																<?php
															}
														}
														?>
													</select>
												</p>
												<?php
											}
										}
									}
									do_action( 'mwb_wgm_add_content_before_from_field' );
									$giftcard_selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_date', false );
									if ( $giftcard_selected_date == 'on' ) {
										?>

										<p class="mwb_wgm_section demo_theme_date_style">
											<label><?php _e( 'Select Date', 'woocommerce-ultimate-gift-card' ); ?>:</label>	
											<input type="text"  name="mwb_wgm_send_date" id="mwb_wgm_send_date" class="mwb_wgm_send_date" placeholder="">
											<span class="mwb_wgm_info"><?php _e( '(Recipient will receive the gift card on selected date)', 'woocommerce-ultimate-gift-card' ); ?></span>

										</p>

										<?php
									}

									?>
									<p class="mwb_wgm_section">
										<label class="mwb_wgm_from_label"><?php _e( 'From', 'woocommerce-ultimate-gift-card' ); ?>:</label>	
										<input type="text"  name="mwb_wgm_from_name" id="mwb_wgm_from_name" class="mwb_wgm_from_name" placeholder="<?php _e( 'Enter the sender name', 'woocommerce-ultimate-gift-card' ); ?>" required="required">
									</p>
									<p class="mwb_wgm_section mwb_wgm_msg_field">

										<label><?php _e( 'Gift Message', 'woocommerce-ultimate-gift-card' ); ?>:</label>	
										<textarea name="mwb_wgm_message" id="mwb_wgm_message" class="mwb_wgm_message"></textarea>
										<?php
										$giftcard_message_length = trim( get_option( 'mwb_wgm_other_setting_giftcard_message_length', 300 ) );
										$mwb_wgm_preview_disable = get_option( 'mwb_wgm_additional_preview_disable', 'off' );
										if ( empty( $giftcard_message_length ) ) {
											$giftcard_message_length = 300;
										}
										_e( 'Characters:', 'woocommerce-ultimate-gift-card' );

										?>
										(<span id="mwb_box_char">0</span>/<?php echo $giftcard_message_length; ?>)
									</p>

									<p>

										<label class = "mwb_gm_method"><?php _e( 'Delivery Method', 'woocommerce-ultimate-gift-card' ); ?>:</label>

										<?php
										if ( isset( $mwb_wgm_method_enable ) && $mwb_wgm_method_enable == 'normal_mail' ) {
											?>
											<div class="mwb_wgm_delivery_method">
												<input type="radio" name="mwb_wgm_send_giftcard" value="Mail to recipient" class="mwb_wgm_send_giftcard" checked="checked" id="to_mail" ><span class="mwb_wgm_method"><?php echo $mwb_wgm_mail_to_recipient_text; ?></span>
												<div class="mwb_wgm_delivery_via_email">
													<input type="text"  name="mwb_wgm_to_email" id="mwb_wgm_to_email" class="mwb_wgm_to_email"placeholder="<?php _e( 'Enter the Recipient Email (Required)', 'woocommerce-ultimate-gift-card' ); ?>">
													<input type="text"  name="mwb_wgm_to_name_optional" id="mwb_wgm_to_name_optional" class="mwb_wgm_to_email"placeholder="<?php _e( 'Enter the Recipient Name', 'woocommerce-ultimate-gift-card' ); ?>"><span class= "mwb_wgm_msg_info"><?php echo $mwb_wgm_mail_to_recipient_desc; ?></span></div>

												</div>
												<?php
											}
											?>
											<?php
											if ( isset( $mwb_wgm_method_enable ) && $mwb_wgm_method_enable == 'download' ) {
												?>
												<div class="mwb_wgm_delivery_method">
													<input type="radio" name="mwb_wgm_send_giftcard" value="Downloadable" class="mwb_wgm_send_giftcard" checked="checked" id="mwb_wgm_send_giftcard_download"><span class="mwb_wgm_method"><?php echo $mwb_wgm_downloadable_text; ?></span>
													<div class="mwb_wgm_delivery_via_buyer">
														<input type="text"  name="mwb_wgm_to_email_name" id="mwb_wgm_to_download" class="mwb_wgm_to_email" placeholder="<?php _e( 'Enter the Recipient Name', 'woocommerce-ultimate-gift-card' ); ?>"><span class= "mwb_wgm_msg_info"><?php echo $mwb_wgm_downloadable_desc; ?></span></div>
													</div>
													<?php
												}
												?>
												<?php
												if ( isset( $mwb_wgm_method_enable ) && $mwb_wgm_method_enable == 'shipping' ) {
													?>
													<div class="mwb_wgm_delivery_method">

														<input type="radio" name="mwb_wgm_send_giftcard" value="Shipping" class="mwb_wgm_send_giftcard" checked="checked" id="mwb_wgm_send_giftcard_ship"><span class="mwb_wgm_method"><?php echo $mwb_wgm_shipping_text; ?></span>
														<div class="mwb_wgm_delivery_via_admin">
															<input type="text"  name="mwb_wgm_to_email_ship" id="mwb_wgm_to_ship" class="mwb_wgm_to_email" placeholder="<?php _e( 'Enter the Recipient Name', 'woocommerce-ultimate-gift-card' ); ?>"><span class= "mwb_wgm_msg_info"><?php echo $mwb_wgm_shipping_desc; ?></span></div>
														</div>	
														<?php
													}
													?>
													<?php
													$mwb_wgm_is_overwrite = get_post_meta( $product_id, 'mwb_wgm_overwrite', true );
													$mwb_wgm_email_to_recipient = get_post_meta( $product_id, 'mwb_wgm_email_to_recipient', true );
													$mwb_wgm_download = get_post_meta( $product_id, 'mwb_wgm_download', true );
													$mwb_wgm_shipping = get_post_meta( $product_id, 'mwb_wgm_shipping', true );

													if ( isset( $mwb_wgm_method_enable ) && $mwb_wgm_method_enable == 'customer_choose' ) {
														if ( isset( $mwb_wgm_is_overwrite ) && $mwb_wgm_is_overwrite == 'yes' ) {
															if ( isset( $mwb_wgm_email_to_recipient ) && $mwb_wgm_email_to_recipient == 'yes' ) {
																?>
																<div class="mwb_wgm_delivery_method">

																	<input type="radio" name="mwb_wgm_send_giftcard" value="Mail to recipient" class="mwb_wgm_send_giftcard" id="to_mail" checked="checked" ><span class="mwb_wgm_method"><?php echo $mwb_wgm_mail_to_recipient_text; ?></span>	
																	<div class="mwb_wgm_delivery_via_email">
																		<input type="text"  name="mwb_wgm_to_email" id="mwb_wgm_to_email" class="mwb_wgm_to_email"placeholder="<?php _e( 'Enter the Recipient Email', 'woocommerce-ultimate-gift-card' ); ?>">
																		<input type="text"  name="mwb_wgm_to_name_optional" id="mwb_wgm_to_name_optional" class="mwb_wgm_to_email"placeholder="<?php _e( 'Enter the Recipient Name', 'woocommerce-ultimate-gift-card' ); ?>">
																		<span class= "mwb_wgm_msg_info"><?php echo $mwb_wgm_mail_to_recipient_desc; ?></span></div></div>
																		<?php
																	}
																	if ( isset( $mwb_wgm_download ) && $mwb_wgm_download == 'yes' ) {
																		?>
																		<div class="mwb_wgm_delivery_method">

																			<input type="radio" name="mwb_wgm_send_giftcard" value="Downloadable" class="mwb_wgm_send_giftcard" id="mwb_wgm_send_giftcard_download"><span class="mwb_wgm_method"><?php echo $mwb_wgm_downloadable_text; ?></span>
																			<div class="mwb_wgm_delivery_via_buyer">
																				<input type="text"  name="mwb_wgm_to_email_name" id="mwb_wgm_to_download" class="mwb_wgm_to_email mwb_wgm_disable" placeholder="<?php _e( 'Enter the Recipient Name', 'woocommerce-ultimate-gift-card' ); ?>" readonly><span class= "mwb_wgm_msg_info"><?php echo $mwb_wgm_downloadable_desc; ?></span></div>
																			</div>
																			<?php
																		}
																		if ( isset( $mwb_wgm_shipping ) && $mwb_wgm_shipping == 'yes' ) {
																			?>
																			<div class="mwb_wgm_delivery_method">
																				<input type="radio" name="mwb_wgm_send_giftcard" value="Shipping" class="mwb_wgm_send_giftcard" id="mwb_wgm_send_giftcard_ship">
																				<span class="mwb_wgm_method"><?php echo $mwb_wgm_shipping_text; ?></span>
																				<div class="mwb_wgm_delivery_via_admin">
																					<input type="text"  name="mwb_wgm_to_email_ship" id="mwb_wgm_to_ship" class="mwb_wgm_to_email mwb_wgm_disable" placeholder="<?php _e( 'Enter the Recipient Name', 'woocommerce-ultimate-gift-card' ); ?>" readonly><span class= "mwb_wgm_msg_info"><?php echo $mwb_wgm_shipping_desc; ?></span></div>
																				</div>
																				<?php
																			}
																		} else {
																			if ( isset( $mwb_wgm_customer_selection['Email_to_recipient'] ) && $mwb_wgm_customer_selection['Email_to_recipient'] == '1' ) {
																				?>
																				<div class="mwb_wgm_delivery_method">

																					<input type="radio" name="mwb_wgm_send_giftcard" value="Mail to recipient" class="mwb_wgm_send_giftcard" id="to_mail" checked="checked">
																					<span class="mwb_wgm_method"><?php echo $mwb_wgm_mail_to_recipient_text; ?>
																					<div class="mwb_wgm_delivery_via_email">
																						<input type="text"  name="mwb_wgm_to_email" id="mwb_wgm_to_email" class="mwb_wgm_to_email"placeholder="<?php _e( 'Enter the Recipient Email', 'woocommerce-ultimate-gift-card' ); ?>">
																						<input type="text"  name="mwb_wgm_to_name_optional" id="mwb_wgm_to_name_optional" class="mwb_wgm_to_email"placeholder="<?php _e( 'Enter the Recipient Name ', 'woocommerce-ultimate-gift-card' ); ?>">
																						<span class= "mwb_wgm_msg_info"><?php echo $mwb_wgm_mail_to_recipient_desc; ?></span></div>
																					</div>	
																					<?php
																				}
																				if ( isset( $mwb_wgm_customer_selection['Downloadable'] ) && $mwb_wgm_customer_selection['Downloadable'] == '1' ) {
																					?>
																					<div class="mwb_wgm_delivery_method">	

																						<input type="radio" name="mwb_wgm_send_giftcard" value="Downloadable" class="mwb_wgm_send_giftcard" id="mwb_wgm_send_giftcard_download">
																						<span class="mwb_wgm_method"><?php echo $mwb_wgm_downloadable_text; ?></span>
																						<div class="mwb_wgm_delivery_via_buyer">
																							<input type="text"  name="mwb_wgm_to_email_name" id="mwb_wgm_to_download" class="mwb_wgm_to_email mwb_wgm_disable" placeholder="<?php _e( 'Enter the Recipient Name', 'woocommerce-ultimate-gift-card' ); ?>" readonly><span class= "mwb_wgm_msg_info"><?php echo $mwb_wgm_downloadable_desc; ?></span></div>
																						</div>
																						<?php
																					}
																					if ( isset( $mwb_wgm_customer_selection['Shipping'] ) && $mwb_wgm_customer_selection['Shipping'] == '1' ) {
																						?>
																						<div class="mwb_wgm_delivery_method">
																							<input type="radio" name="mwb_wgm_send_giftcard" value="Shipping" class="mwb_wgm_send_giftcard" id="mwb_wgm_send_giftcard_ship"><span class="mwb_wgm_method"><?php echo $mwb_wgm_shipping_text; ?></span>
																							<div class="mwb_wgm_delivery_via_admin">
																								<input type="text"  name="mwb_wgm_to_email_ship" id="mwb_wgm_to_ship" class="mwb_wgm_to_email mwb_wgm_disable" placeholder="<?php _e( 'Enter the Recipient Name', 'woocommerce-ultimate-gift-card' ); ?>" readonly><span class= "mwb_wgm_msg_info"><?php echo $mwb_wgm_shipping_desc; ?></span></div>
																							</div>		
																							<?php
																						}
																					}
																				}
																				$gift_browse_enable = get_option( 'mwb_wgm_other_setting_browse', 'off' );
																				if ( $gift_browse_enable == 'on' ) {
																					?>
																					<div class="mwb_demo_browse">

																						<p class="mwb_wgm_section">
																							<label><?php _e( 'Upload Image', 'woocommerce-ultimate-gift-card' ); ?>:</label>	
																							<input type="file"  name="mwb_wgm_browse_img" id="mwb_wgm_browse_img" class="mwb_wgm_browse_img"><span class="mwb_wgm_info"><?php _e( '(Uploaded Image will replace the product image in template)', 'woocommerce-ultimate-gift-card' ); ?></span>
																							<img id="mwb_wgm_browse_src">
																						</p>

																					</div>
																					<?php
																				}

																				?>
																			</p>
																			<?php

																			$mwb_wgm_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );

																			$mwb_wgm_pricing = ! empty( $mwb_wgm_pricing ) ? $mwb_wgm_pricing : array();

																			$mwb_wgm_pricing['template'] = ! empty( $mwb_wgm_pricing['template'] ) ? $mwb_wgm_pricing['template'] : '';
																			$templateid = apply_filters( 'mwb_wgm_custom_gift_template', $mwb_wgm_pricing['template'], $product_id );
																			$assigned_temp = '';
																			$default_selected = isset( $mwb_wgm_pricing['by_default_tem'] ) ? $mwb_wgm_pricing['by_default_tem'] : false;
																			$mwb_wgm_hide_giftcard_thumbnail = get_option( 'mwb_wgm_hide_giftcard_thumbnail', 'off' );
																			if ( is_array( $templateid ) && ! empty( $templateid ) ) {
																				foreach ( $templateid as $key => $temp_id ) {
																					$featured_img = wp_get_attachment_image_src( get_post_thumbnail_id( $temp_id ), 'single-post-thumbnail' );
																					if ( empty( $featured_img[0] ) ) {
																						$featured_img[0] = MWB_WGM_URL . '/assets/images/placeholder.png';
																					}
																					$selected_class = '';
																					if ( isset( $default_selected ) && $default_selected != null && $default_selected == $temp_id ) {
																						$selected_class = 'mwb_wgm_pre_selected_temp';
																						$choosed_temp = $temp_id;
																					} else if ( empty( $default_selected ) && is_array( $templateid ) ) {
																						$selected_class = 'mwb_wgm_pre_selected_temp';
																					}
																					$assigned_temp .= '<img class = "mwb_wgm_featured_img ' . $selected_class . '" id="' . $temp_id . '" style="width: 70px; height: 70px;" src="' . $featured_img[0] . '">';
																				}
																			} elseif ( ! is_array( $templateid ) && ! empty( $templateid ) ) {
																				$featured_img = wp_get_attachment_image_src( get_post_thumbnail_id( $templateid ), 'single-post-thumbnail' );
																				if ( empty( $featured_img[0] ) ) {
																					$featured_img[0] = MWB_WGM_URL . '/assets/images/placeholder.png';
																				}
																				$assigned_temp .= '<img class = "mwb_wgm_featured_img mwb_wgm_pre_selected_temp" id="' . $templateid . '" style="width: 70px; height: 70px;" src="' . $featured_img[0] . '">';
																				$choosed_temp = $templateid;
																			}
																			if ( $mwb_wgm_hide_giftcard_thumbnail == 'off' ) {
																				echo '<div class="mwb_wgm_selected_template" style="display: inline-block; text-decoration: none; padding-right:20px;">' . $assigned_temp . '</div>';
																			} else {
																				echo '<div class="mwb_wgm_selected_template" style="display: none; text-decoration: none; padding-right:20px;">' . $assigned_temp . '</div>';
																			}
																			?>
																			<input name="add-to-cart" value="<?php echo $product_id; ?>" type="hidden" class="mwb_wgm_hidden_pro_id">
																			<?php if ( is_array( $templateid ) && ! empty( $templateid ) ) { ?>
																				<input name="mwb_wgm_selected_temp" id="mwb_wgm_selected_temp" value="<?php echo $choosed_temp; ?>" type="hidden">
																				<?php
																			}
																			if ( $mwb_wgm_preview_disable == 'off' ) {
																				?>
																				<br/><span class="mwg_wgm_preview_email" ><a id="mwg_wgm_preview_email" href="javascript:void(0);"><?php _e( 'Preview', 'woocommerce-ultimate-gift-card' ); ?></a> </span><?php } ?>
																			</p>
																		</div>
																		<?php
																		do_action( 'mwb_wgm_custom_product_field' );
																	}
																}
															}
														}
													}
												}
											}

		/**
		 * This function is used to add meta data in to cart
		 *
		 * @name mwb_wgm_woocommerce_add_cart_item_data
		 * @param $the_cart_data
		 * @param $product_id
		 * @return $the_cart_data
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_woocommerce_add_cart_item_data( $the_cart_data, $product_id, $variation_id ) {

			$mwb_wgm_enable = mwb_wgm_giftcard_enable();
			if ( $mwb_wgm_enable ) {
				$product_types = wp_get_object_terms( $product_id, 'product_type' );
				$is_customizable = get_post_meta( $product_id, 'woocommerce_customizable_gift_card', true );
				if ( isset( $product_types[0] ) ) {
					$product_type = $product_types[0]->slug;
					if ( $product_type == 'wgm_gift_card' ) {
						// custom code currency switcher
						if ( class_exists( 'WOOCS' ) ) {
							global $WOOCS;
							$mwb_currency = $WOOCS->current_currency;
							$mwb_currencies = $WOOCS->get_currencies();
							$mwb_rate = $mwb_currencies[ $mwb_currency ]['rate'];
							$decimal = $mwb_currencies[ $mwb_currency ]['decimals'];
							$product_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );
							$product_pricing_type = $product_pricing['type'];
							if ( $product_pricing_type == 'mwb_wgm_range_price' ) {
								if ( isset( $_POST['mwb_wgm_price'] ) and floatval( $_POST['mwb_wgm_price'] ) != 0.0 ) {
									$_POST['mwb_wgm_price'] = floatval( $_POST['mwb_wgm_price'] / $mwb_rate );
								}
							}
							if ( $product_pricing_type == 'mwb_wgm_user_price' ) {
								if ( isset( $_POST['mwb_wgm_price'] ) and floatval( $_POST['mwb_wgm_price'] ) != 0.0 ) {
									$_POST['mwb_wgm_price'] = floatval( $_POST['mwb_wgm_price'] / $mwb_rate );
								}
							}
						}
						// for price based on country
						if ( class_exists( 'WCPBC_Pricing_Zone' ) ) {
							if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
								$product_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );
								$product_pricing_type = $product_pricing['type'];
								if ( isset( $_POST['mwb_wgm_price'] ) && ! empty( $_POST['mwb_wgm_price'] ) ) {
									if ( $product_pricing_type == 'mwb_wgm_range_price' || $product_pricing_type == 'mwb_wgm_user_price' ) {
										$exchange_rate = wcpbc_the_zone()->get_exchange_rate();
										$_POST['mwb_wgm_price'] = floatval( $_POST['mwb_wgm_price'] / $exchange_rate );
									}
								}
							}
						}
						if ( isset( $_POST['mwb_wgm_to_email'] ) && ! empty( $_POST['mwb_wgm_to_email'] ) ) {
							$product_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );

							if ( isset( $product_pricing ) && ! empty( $product_pricing ) ) {
								if ( isset( $_POST['mwb_wgm_to_name_optional'] ) && ! empty( $_POST['mwb_wgm_to_name_optional'] ) ) {
									$item_meta['mwb_wgm_to_name_optional'] = $_POST['mwb_wgm_to_name_optional'];
								}
								$item_meta['mwb_wgm_to_email'] = sanitize_text_field( $_POST['mwb_wgm_to_email'] );
								$item_meta['mwb_wgm_from_name'] = sanitize_text_field( $_POST['mwb_wgm_from_name'] );
								$item_meta['mwb_wgm_message'] = sanitize_textarea_field( $_POST['mwb_wgm_message'] );
								$item_meta['delivery_method'] = sanitize_text_field( $_POST['mwb_wgm_send_giftcard'] );
								$giftcard_selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_date', false );

								if ( $giftcard_selected_date == 'on' ) {
									$item_meta['mwb_wgm_send_date'] = $_POST['mwb_wgm_send_date'];

								}
								if ( isset( $_POST['mwb_wgm_price'] ) ) {
									$item_meta['mwb_wgm_price'] = sanitize_text_field( $_POST['mwb_wgm_price'] );
								}
								if ( isset( $_POST['mwb_wgm_selected_temp'] ) && empty( $is_customizable ) ) {
									$item_meta['mwb_wgm_selected_temp'] = $_POST['mwb_wgm_selected_temp'];
								}
								$browse_img = get_option( 'mwb_wgm_other_setting_browse', 'off' );

								if ( $browse_img == 'on' && ! empty( $_FILES['mwb_wgm_browse_img']['type'] ) ) {
									$uploadDirPath = MWB_UPLOAD_DIR . '/mwb_browse';
									if ( ! is_dir( $uploadDirPath ) ) {
										wp_mkdir_p( $uploadDirPath );
										chmod( $uploadDirPath, 0775 );
									}
									if ( ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/gif' )
										|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/jpeg' )
										|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/jpg' )
										|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/pjpeg' )
										|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/x-png' )
										|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/png' ) ) {
										$file_name = $_FILES['mwb_wgm_browse_img']['name'];
									$file_name = sanitize_file_name( $file_name );
									if ( ! file_exists( MWB_UPLOAD_DIR . '/mwb_browse/' . $file_name ) ) {
										move_uploaded_file( $_FILES['mwb_wgm_browse_img']['tmp_name'], MWB_UPLOAD_DIR . '/mwb_browse/' . $file_name );
									}

									$item_meta['mwb_wgm_browse_img'] = $file_name;
								}
							}
							$item_meta = apply_filters( 'mwb_wgm_price_meta_data', $item_meta, $the_cart_data, $product_id, $variation_id );
							$the_cart_data ['product_meta'] = array( 'meta_data' => $item_meta );

						}
					} else if ( isset( $_POST['mwb_wgm_to_email_name'] ) && ! empty( $_POST['mwb_wgm_to_email_name'] ) ) {
						$product_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );

						if ( isset( $product_pricing ) && ! empty( $product_pricing ) ) {
							$item_meta['mwb_wgm_to_email'] = sanitize_text_field( $_POST['mwb_wgm_to_email_name'] );
							$item_meta['mwb_wgm_from_name'] = sanitize_text_field( $_POST['mwb_wgm_from_name'] );
							$item_meta['mwb_wgm_message'] = sanitize_textarea_field( $_POST['mwb_wgm_message'] );
							$item_meta['delivery_method'] = sanitize_text_field( $_POST['mwb_wgm_send_giftcard'] );
							$giftcard_selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_date', false );

							if ( $giftcard_selected_date == 'on' ) {
								$item_meta['mwb_wgm_send_date'] = $_POST['mwb_wgm_send_date'];
							}
							if ( isset( $_POST['mwb_wgm_price'] ) ) {
								$item_meta['mwb_wgm_price'] = $_POST['mwb_wgm_price'];
							}
							if ( isset( $_POST['mwb_wgm_selected_temp'] ) && empty( $is_customizable ) ) {
								$item_meta['mwb_wgm_selected_temp'] = $_POST['mwb_wgm_selected_temp'];
							}
							$browse_img = get_option( 'mwb_wgm_other_setting_browse', 'off' );

							if ( $browse_img == 'on' && ! empty( $_FILES['mwb_wgm_browse_img']['type'] ) ) {
								$uploadDirPath = MWB_UPLOAD_DIR . '/mwb_browse';
								if ( ! is_dir( $uploadDirPath ) ) {
									wp_mkdir_p( $uploadDirPath );
									chmod( $uploadDirPath, 0775 );
								}
								if ( ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/gif' )
									|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/jpeg' )
									|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/jpg' )
									|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/pjpeg' )
									|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/x-png' )
									|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/png' ) ) {
									$file_name = $_FILES['mwb_wgm_browse_img']['name'];
								$file_name = sanitize_file_name( $file_name );
								if ( ! file_exists( MWB_UPLOAD_DIR . '/mwb_browse/' . $file_name ) ) {
									move_uploaded_file( $_FILES['mwb_wgm_browse_img']['tmp_name'], MWB_UPLOAD_DIR . '/mwb_browse/' . $file_name );
								}

								$item_meta['mwb_wgm_browse_img'] = $file_name;
							}
						}
						$item_meta = apply_filters( 'mwb_wgm_price_meta_data', $item_meta, $the_cart_data, $product_id, $variation_id );

						$the_cart_data ['product_meta'] = array( 'meta_data' => $item_meta );

					}
				} else if ( isset( $_POST['mwb_wgm_to_email_ship'] ) && ! empty( $_POST['mwb_wgm_to_email_ship'] ) ) {
					$product_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );

					if ( isset( $product_pricing ) && ! empty( $product_pricing ) ) {
						$item_meta['mwb_wgm_to_email'] = sanitize_text_field( $_POST['mwb_wgm_to_email_ship'] );
						$item_meta['mwb_wgm_from_name'] = sanitize_text_field( $_POST['mwb_wgm_from_name'] );
						$item_meta['mwb_wgm_message'] = sanitize_textarea_field( $_POST['mwb_wgm_message'] );
						$item_meta['delivery_method'] = $_POST['mwb_wgm_send_giftcard'];
						$giftcard_selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_date', false );

						if ( $giftcard_selected_date == 'on' ) {
							$item_meta['mwb_wgm_send_date'] = $_POST['mwb_wgm_send_date'];

						}
						if ( isset( $_POST['mwb_wgm_price'] ) ) {
							$item_meta['mwb_wgm_price'] = sanitize_text_field( $_POST['mwb_wgm_price'] );
						}
						if ( isset( $_POST['mwb_wgm_selected_temp'] ) && empty( $is_customizable ) ) {
							$item_meta['mwb_wgm_selected_temp'] = $_POST['mwb_wgm_selected_temp'];
						}
						$browse_img = get_option( 'mwb_wgm_other_setting_browse', 'off' );

						if ( $browse_img == 'on' && ! empty( $_FILES['mwb_wgm_browse_img']['type'] ) ) {
							$uploadDirPath = MWB_UPLOAD_DIR . '/mwb_browse';
							if ( ! is_dir( $uploadDirPath ) ) {
								wp_mkdir_p( $uploadDirPath );
								chmod( $uploadDirPath, 0775 );
							}
							if ( ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/gif' )
								|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/jpeg' )
								|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/jpg' )
								|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/pjpeg' )
								|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/x-png' )
								|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/png' ) ) {
								$file_name = $_FILES['mwb_wgm_browse_img']['name'];
							$file_name = sanitize_file_name( $file_name );
							if ( ! file_exists( MWB_UPLOAD_DIR . '/mwb_browse/' . $file_name ) ) {
								move_uploaded_file( $_FILES['mwb_wgm_browse_img']['tmp_name'], MWB_UPLOAD_DIR . '/mwb_browse/' . $file_name );
							}

							$item_meta['mwb_wgm_browse_img'] = $file_name;
						}
					}
					$item_meta = apply_filters( 'mwb_wgm_price_meta_data', $item_meta, $the_cart_data, $product_id, $variation_id );

					$the_cart_data ['product_meta'] = array( 'meta_data' => $item_meta );

				}
			} elseif ( isset( $_POST['mwb_wgm_send_giftcard'] ) && ! empty( $_POST['mwb_wgm_send_giftcard'] ) ) {
				$product_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );

				if ( isset( $product_pricing ) && ! empty( $product_pricing ) ) {

					if ( isset( $_POST['mwb_wgm_to_name_optional'] ) && ! empty( $_POST['mwb_wgm_to_name_optional'] ) ) {
						$item_meta['mwb_wgm_to_name_optional'] = sanitize_text_field( $_POST['mwb_wgm_to_name_optional'] );
					}
					if ( isset( $_POST['mwb_wgm_to_email'] ) && ! empty( $_POST['mwb_wgm_to_email'] ) ) {
						$item_meta['mwb_wgm_to_email'] = sanitize_text_field( $_POST['mwb_wgm_to_email'] );
					}
					if ( isset( $_POST['mwb_wgm_from_name'] ) && ! empty( $_POST['mwb_wgm_from_name'] ) ) {
						$item_meta['mwb_wgm_from_name'] = sanitize_text_field( $_POST['mwb_wgm_from_name'] );
					}
					if ( isset( $_POST['mwb_wgm_message'] ) && ! empty( $_POST['mwb_wgm_message'] ) ) {
						$item_meta['mwb_wgm_message'] = sanitize_text_field( $_POST['mwb_wgm_message'] );
					}
					if ( isset( $_POST['mwb_wgm_message'] ) && ! empty( $_POST['mwb_wgm_message'] ) ) {
						$item_meta['mwb_wgm_message'] = sanitize_text_field( $_POST['mwb_wgm_message'] );
					}

					$item_meta['delivery_method'] = $_POST['mwb_wgm_send_giftcard'];
					$giftcard_selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_date', false );

					if ( $giftcard_selected_date == 'on' ) {
						$item_meta['mwb_wgm_send_date'] = $_POST['mwb_wgm_send_date'];

					}
					if ( isset( $_POST['mwb_wgm_price'] ) ) {
						$item_meta['mwb_wgm_price'] = sanitize_text_field( $_POST['mwb_wgm_price'] );
					}
					if ( isset( $_POST['mwb_wgm_selected_temp'] ) && empty( $is_customizable ) ) {
						$item_meta['mwb_wgm_selected_temp'] = $_POST['mwb_wgm_selected_temp'];
					}
					$browse_img = get_option( 'mwb_wgm_other_setting_browse', 'off' );

					if ( $browse_img == 'on' ) {
						$uploadDirPath = MWB_UPLOAD_DIR . '/mwb_browse';
						if ( ! is_dir( $uploadDirPath ) ) {
							wp_mkdir_p( $uploadDirPath );
							chmod( $uploadDirPath, 0775 );
						}
						if ( ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/gif' )
							|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/jpeg' )
							|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/jpg' )
							|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/pjpeg' )
							|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/x-png' )
							|| ( $_FILES['mwb_wgm_browse_img']['type'] == 'image/png' ) ) {
							$file_name = $_FILES['mwb_wgm_browse_img']['name'];
						$file_name = sanitize_file_name( $file_name );
						if ( ! file_exists( MWB_UPLOAD_DIR . '/mwb_browse/' . $file_name ) ) {
							move_uploaded_file( $_FILES['mwb_wgm_browse_img']['tmp_name'], MWB_UPLOAD_DIR . '/mwb_browse/' . $file_name );
						}

						$item_meta['mwb_wgm_browse_img'] = $file_name;
					}
				}
				$item_meta = apply_filters( 'mwb_wgm_price_meta_data', $item_meta, $the_cart_data, $product_id, $variation_id );

				$the_cart_data ['product_meta'] = array( 'meta_data' => $item_meta );
			}
		}
	}
}
}

return $the_cart_data;
}

		/**
		 * This function is used to add metadata with item
		 *
		 * @name mwb_wgm_woocommerce_get_item_data
		 * @param $item_meta
		 * @param $existing_item_meta
		 * @return $item_meta
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_woocommerce_get_item_data( $item_meta, $existing_item_meta ) {

			$mwb_wgm_enable = mwb_wgm_giftcard_enable();
			if ( $mwb_wgm_enable ) {
				if ( isset( $existing_item_meta ['product_meta']['meta_data'] ) ) {
					if ( $existing_item_meta ['product_meta']['meta_data'] ) {
						foreach ( $existing_item_meta['product_meta'] ['meta_data'] as $key => $val ) {
							if ( $key == 'mwb_wgm_send_date' ) {
								$item_meta [] = array(
									'name' => __( 'Send Date', 'woocommerce-ultimate-gift-card' ),
									'value' => stripslashes( $val ),
								);
							}
							if ( $key == 'mwb_wgm_to_name_optional' ) {
								$item_meta [] = array(
									'name' => __( 'To Name', 'woocommerce-ultimate-gift-card' ),
									'value' => stripslashes( $val ),
								);
							}
							if ( $key == 'mwb_wgm_to_email' ) {
								$item_meta [] = array(
									'name' => __( 'To', 'woocommerce-ultimate-gift-card' ),
									'value' => stripslashes( $val ),
								);
							}
							if ( $key == 'mwb_wgm_from_name' ) {
								$item_meta [] = array(
									'name' => __( 'From', 'woocommerce-ultimate-gift-card' ),
									'value' => stripslashes( $val ),
								);
							}

							if ( $key == 'mwb_wgm_message' ) {
								$item_meta [] = array(
									'name' => __( 'Gift Message', 'woocommerce-ultimate-gift-card' ),
									'value' => stripslashes( $val ),
								);
							}
							$item_meta = apply_filters( 'mwb_wgm_product_item_meta', $item_meta, $key, $val );
						}
					}
				}
			}

			return $item_meta;
		}

		/**
		 * This function is used update product price on cart page
		 *
		 * @name mwb_wgm_woocommerce_before_calculate_totals
		 * @param $cart
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_woocommerce_before_calculate_totals( $cart ) {
			$woo_ver = WC()->version;
			$mwb_wgm_enable = mwb_wgm_giftcard_enable();
			$discount_enable = get_option( 'mwb_wgm_discount_enable', false );
			if ( $mwb_wgm_enable ) {
				foreach ( $cart->cart_contents as $key => $value ) {
					$discount_min = get_option( 'mwb_wgm_discount_minimum', array() );
					$discount_max = get_option( 'mwb_wgm_discount_maximum', array() );
					$discount_value = get_option( 'mwb_wgm_discount_current_type', array() );
					$discount_type = get_option( 'mwb_wgm_discount_type', 'mwb_wgm_fixed' );
					$discount_applicable = false;
					$product_id = $value['product_id'];
					$pro_quant = $value['quantity'];
					$mwb_wgm_discount = get_post_meta( $product_id, 'mwb_wgm_discount', false );
					if ( isset( $value['product_meta']['meta_data'] ) ) {
						if ( isset( $value['product_meta']['meta_data']['mwb_wgm_price'] ) ) {
							$gift_price = $value['product_meta']['meta_data']['mwb_wgm_price'];
							if ( isset( $discount_enable ) && $discount_enable == 'on' ) {
								if ( isset( $mwb_wgm_discount[0] ) && $mwb_wgm_discount[0] == 'yes' ) {
									if ( isset( $discount_min ) && $discount_min != null && isset( $discount_max ) && $discount_max != null && isset( $discount_value ) && $discount_value != null ) {
										foreach ( $discount_min as $key => $values ) {	
											$decimal_separator  = get_option( 'woocommerce_price_decimal_sep' );
											$gift_price = floatval( str_replace( $decimal_separator , '.', $gift_price ) );
											$discount_val = floatval( str_replace( $decimal_separator , '.', $discount_value[$key] ) );
											$discount_mini = floatval( str_replace( $decimal_separator , '.', $discount_min[ $key ] ) );
											$discount_maxi = floatval( str_replace( $decimal_separator , '.', $discount_max[ $key ] ) );
											if ( $discount_mini <= $gift_price && $gift_price <= $discount_maxi) {
												if ( $discount_type == 'mwb_wgm_percentage' ) {
													$new_price = $gift_price - ( $gift_price * $discount_val ) / 100;
												} else {
													if( $gift_price > $discount_val ) {
														$new_price = $gift_price - $discount_val;
													} else{
														$new_price = 0;
													}
												}
												$discount_applicable = true;
											}
										}
									}
								}
							}
							if ( $discount_applicable ) {
								if ( $woo_ver < '3.0.0' ) {
									$value['data']->price = $new_price;
								} else {
									$value['data']->set_price( $new_price );
								}
							} else {
								if ( $woo_ver < '3.0.0' ) {
									$value['data']->price = $gift_price;
								} else {

									$value['data']->set_price( $gift_price );
								}
							}
							// Compatibility between WooCommerce Product Addon
							if ( isset( $value['addons'] ) && ! empty( $value['addons'] ) ) {

								foreach ( $value['addons'] as $addon ) {
									$price_type    = $addon['price_type'];

									if ( $addon['price'] && 'percentage_based' === $price_type && 0 != $gift_price ) {
										$price         = wp_kses_decode_entities( strip_tags( WC_Product_Addons_Helper::get_product_addon_price_for_display( ( $gift_price * $addon['price'] / 100 ), $value['data'], true ) ) );
									} else {
										$price = wp_kses_decode_entities( strip_tags( WC_Product_Addons_Helper::get_product_addon_price_for_display( $addon['price'], $value['data'], true ) ) );
									}

									$new_price = $price + $gift_price;
									$value['data']->set_price( $new_price );
								}
							}
						}
					}
				}
			}
		}
		/**
		 * This function is used to send giftcard mail when order is completed
		 *
		 * @name mwb_wgm_woocommerce_order_status_changed
		 * @param $order_id
		 * @param $old_status
		 * @param $new_status
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_woocommerce_order_status_changed( $order_id, $old_status, $new_status ) {
			$thnku_subject = __( 'Hurry! Coupon Code is received', 'woocommerce-ultimate-gift-card' );
			$message_thnku = get_option( 'mwb_wgm_thankyou_message', 'You have recieved a coupon [COUPONCODE], having amount of [COUPONAMOUNT] with the expiration date of [COUPONEXPIRY]' );
			$mail_header = __( 'Thankyou Giftcard Coupon', 'woocommerce-ultimate-gift-card' );
			$mail_footer = '';
			$message_thnku = '<html>
			<body>
			<style>
			body {
				box-shadow: 2px 2px 10px #ccc;
				color: #767676;
				font-family: Arial,sans-serif;
				margin: 80px auto;
				max-width: 700px;
				padding-bottom: 30px;
				width: 100%;
			}

			h2 {
				font-size: 30px;
				margin-top: 0;
				color: #fff;
				padding: 40px;
				background-color: #557da1;
			}

			h4 {
				color: #557da1;
				font-size: 20px;
				margin-bottom: 10px;
			}

			.content {
				padding: 0 40px;
			}

			.Customer-detail ul li p {
				margin: 0;
			}

			.details .Shipping-detail {
				width: 40%;
				float: right;
			}

			.details .Billing-detail {
				width: 60%;
				float: left;
			}

			.details .Shipping-detail ul li,.details .Billing-detail ul li {
				list-style-type: none;
				margin: 0;
			}

			.details .Billing-detail ul,.details .Shipping-detail ul {
				margin: 0;
				padding: 0;
			}

			.clear {
				clear: both;
			}

			table,td,th {
				border: 2px solid #ccc;
				padding: 15px;
				text-align: left;
			}

			table {
				border-collapse: collapse;
				width: 100%;
			}
			.info {
				display: inline-block;
			}

			.bold {
				font-weight: bold;
			}

			.footer {
				margin-top: 30px;
				text-align: center;
				color: #99B1D8;
				font-size: 12px;
			}
			dl.variation dd {
				font-size: 12px;
				margin: 0;
			}
			</style>

			<div style="padding: 36px 48px; background-color:#557DA1;color: #fff; font-size: 30px; font-weight: 300; font-family:helvetica;" class="header">
			' . $mail_header . '
			</div>		

			<div class="content">
			<div class="Order">
			<h4>Order #' . $order_id . '</h4>
			<table>
			<tbody>' . $message_thnku . '</tbody>
			</table>
			</div>
			</div>
			<div style="text-align: center; padding: 10px;" class="footer">
			' . $mail_footer . '
			</div>
			</body>
			</html>';
			$discount_enable = get_option( 'mwb_wgm_discount_enable', false );
			$mwb_wgm_enable = mwb_wgm_giftcard_enable();
			$mwb_wgm_method_enable = get_option( 'mwb_wgm_send_giftcard', false );
			$mwb_wgm_customer_selection = get_option( 'mwb_wgm_customer_selection', false );
			if ( $mwb_wgm_enable ) {
				if ( $old_status != $new_status ) {
					if ( $new_status == 'completed' || $new_status == 'processing' ) {
						$is_gift_card = false;
						$order = wc_get_order( $order_id );
						foreach ( $order->get_items() as $item_id => $item ) {
							$product = $item->get_product();
							if ($product->is_type('wgm_gift_card')) {
								$is_gift_card = true;
							}
						}
						if ( !$is_gift_card ) {
							return;
						}
						$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format_1', false );
						$mailalreadysend = get_post_meta( $order_id, 'mwb_wgm_order_giftcard', true );
						if ( $mailalreadysend == 'send' ) {
							return;
						} else {
							$datecheck = true;
							$giftcard_selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_date', false );
							if ( $giftcard_selected_date == 'on' ) {
								update_post_meta( $order_id, 'mwb_wgm_order_giftcard', 'notsend' );
							}
						}

						$gift_msg = '';
						$to = '';
						$from = '';
						$gift_order = false;
						$original_price = 0;
						$order = wc_get_order( $order_id );
						$user_id = $order->get_user_id();
						if ( isset( $user_id ) && ! empty( $user_id ) ) {

							if ( $new_status == 'completed' ) {
								$thankyou_user_order = get_user_meta( $user_id, 'thankyou_order_number', true );
								if ( isset( $thankyou_user_order ) && ! empty( $thankyou_user_order ) ) {
									$thankyou_user_order += 1;
									update_user_meta( $user_id, 'thankyou_order_number', $thankyou_user_order );
								} else {
									update_user_meta( $user_id, 'thankyou_order_number', 1 );
								}
							}
						}
						foreach ( $order->get_items() as $item_id => $item ) {

							$mailsend = false;
							$to_name = '';
							$woo_ver = WC()->version;
							$gift_img_name = '';
							if ( $woo_ver < '3.0.0' ) {
								$item_quantity = $order->get_item_meta( $item_id, '_qty', true );
								$product = $order->get_product_from_item( $item );
								$pro_id = $product->id;
								if ( isset( $item['item_meta']['To'] ) && ! empty( $item['item_meta']['To'] ) ) {
									$mailsend = true;
									$to = $item['item_meta']['To'][0];

								}
								if ( isset( $item['item_meta']['To Name'] ) && ! empty( $item['item_meta']['To Name'] ) ) {
									$mailsend = true;
									$to_name = $item['item_meta']['To Name'][0];
								}
								if ( isset( $item['item_meta']['From'] ) && ! empty( $item['item_meta']['From'] ) ) {
									$mailsend = true;
									$from = $item['item_meta']['From'][0];
								}
								if ( isset( $item['item_meta']['Message'] ) && ! empty( $item['item_meta']['Message'] ) ) {
									$mailsend = true;
									$gift_msg = $item['item_meta']['Message'][0];
								}
								if ( isset( $item['item_meta']['Image'] ) && ! empty( $item['item_meta']['Image'] ) ) {
									$mailsend = true;
									$gift_img_name = $item['item_meta']['Image'][0];
								}
								if ( isset( $item['item_meta']['Delivery Method'] ) && ! empty( $item['item_meta']['Delivery Method'] ) ) {
									$mailsend = true;
									$delivery_method = $item['item_meta']['Delivery Method'][0];
								}
								if ( isset( $item['item_meta']['Original Price'] ) && ! empty( $item['item_meta']['Original Price'] ) ) {
									$mailsend = true;
									$original_price = $item['item_meta']['Original Price'][0];
								}
								if ( isset( $item['item_meta']['Selected Template'] ) && ! empty( $item['item_meta']['Selected Template'] ) ) {
									$mailsend = true;
									$selected_template = $item['item_meta']['Selected Template'][0];
								}
								if ( empty( $to ) ) {
									if ( $delivery_method == 'Mail to recipient' ) {
										$to = $order->billing_email();
									}
								}
								if ( isset( $item['item_meta']['Send Date'] ) && ! empty( $item['item_meta']['Send Date'] ) ) {
									$itemgiftsend = get_post_meta( $order_id, "$order_id#$item_id#send", true );
									if ( $itemgiftsend == 'send' ) {
										continue;
									}

									$mailsend = true;
									$gift_date = $item['item_meta']['Send Date'][0];
									if ( is_string( $gift_date ) ) {
										if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
											if ( $selected_date == 'd/m/Y' ) {
												$gift_date = str_replace( '/', '-', $gift_date );
											}
										}
										$senddatetime = strtotime( $gift_date );
									}

									$senddate = date_i18n( 'Y-m-d', $senddatetime );
									$todaytime = time();
									$todaydate = date_i18n( 'Y-m-d', $todaytime );
									$senddatetime = strtotime( "$senddate" );
									$todaytime = strtotime( "$todaydate" );

									$giftdiff = $senddatetime - $todaytime;

									if ( isset( $delivery_method ) && $delivery_method == 'Mail to recipient' ) {
										if ( $giftdiff > 0 ) {
											$datecheck = false;

											update_post_meta( $order_id, "$order_id#$item_id#send", 'notsend' );
											continue;
										} else {
											update_post_meta( $order_id, "$order_id#$item_id#send", 'send' );
										}
									} else {
										update_post_meta( $order_id, "$order_id#$item_id#send", 'send' );
									}
								}
							} else {
								$item_quantity = wc_get_order_item_meta( $item_id, '_qty', true );
								$product = $item->get_product();
								$pro_id = $product->get_id();
								$item_meta_data = $item->get_meta_data();
								$gift_date_check = false;
								$gift_date = '';
								$original_price = 0;
								$to_name = '';
								$selected_template = '';
								$delivery_method = '';
								foreach ( $item_meta_data as $key => $value ) {
									if ( isset( $value->key ) && $value->key == 'To' && ! empty( $value->value ) ) {
										$mailsend = true;
										$to = $value->value;

									}
									if ( isset( $value->key ) && $value->key == 'To Name' && ! empty( $value->value ) ) {
										$mailsend = true;
										$to_name = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'From' && ! empty( $value->value ) ) {
										$mailsend = true;
										$from = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'Message' && ! empty( $value->value ) ) {
										$mailsend = true;
										$gift_msg = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'Image' && ! empty( $value->value ) ) {
										$mailsend = true;
										$gift_img_name = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'Send Date' && ! empty( $value->value ) ) {
										$gift_date_check = true;
										$gift_date = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'Delivery Method' && ! empty( $value->value ) ) {
										$mailsend = true;
										$delivery_method = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'Original Price' && ! empty( $value->value ) ) {
										$mailsend = true;
										$original_price = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'Selected Template' && ! empty( $value->value ) ) {
										$mailsend = true;
										$selected_template = $value->value;
									}
								}
								if ( empty( $to ) ) {
									if ( $delivery_method == 'Mail to recipient' ) {
										$to = $order->get_billing_email();
									}
								}
								if ( $gift_date_check ) {

									$itemgiftsend = get_post_meta( $order_id, "$order_id#$item_id#send", true );

									if ( $itemgiftsend == 'send' ) {
										continue;
									}
									$mailsend = true;
									if ( is_string( $gift_date ) ) {
										if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
											if ( $selected_date == 'd/m/Y' ) {
												$gift_date = str_replace( '/', '-', $gift_date );
											}
										}
										$senddatetime = strtotime( $gift_date );
									}
									$senddate = date_i18n( 'Y-m-d', $senddatetime );
									$todaytime = time();
									$todaydate = date_i18n( 'Y-m-d', $todaytime );
									$senddatetime = strtotime( "$senddate" );
									$todaytime = strtotime( "$todaydate" );

									$giftdiff = $senddatetime - $todaytime;

									if ( $delivery_method == 'Mail to recipient' ) {
										if ( $giftdiff > 0 ) {
											$datecheck = false;

											update_post_meta( $order_id, "$order_id#$item_id#send", 'notsend' );
											continue;
										} else {

											update_post_meta( $order_id, "$order_id#$item_id#send", 'send' );
										}
									} else {
										update_post_meta( $order_id, "$order_id#$item_id#send", 'send' );
									}
								}
							}
							if ( $mailsend ) {
								$gift_order = true;
								$inc_tax_status = get_option( 'woocommerce_prices_include_tax', false );
								if ( $inc_tax_status == 'yes' ) {
									$inc_tax_status = true;
								} else {
									$inc_tax_status = false;
								}

								$mwb_wgm_discount = get_post_meta( $item['product_id'], 'mwb_wgm_discount', false );
								$couponamont = $original_price;
								
							$args = array(
								'posts_per_page'   => -1,
								'orderby'          => 'title',
								'order'            => 'asc',
								'post_type'        => 'shop_coupon',
								'post_status'      => 'publish',
							);
							$args['meta_query'] = array(
								array(
									'key' => 'mwb_wgm_imported_coupon',
									'value' => 'yes',
									'compare' => '==',
								),
							);
							$imported_coupons = get_posts( $args );
							$mwb_wgm_common_arr = array();
							$is_imported_product = get_post_meta( $pro_id, 'is_imported', true );
							if ( isset( $is_imported_product ) && ! empty( $is_imported_product ) && $is_imported_product == 'yes' ) {

								if ( class_exists( 'WOOCS' ) ) {
									$pro_price = get_post_meta( $pro_id, 'mwb_wgm_pricing', true );
									if ( isset( $pro_price ) && is_array( $pro_price ) ) {
										$couponamont = $pro_price['default_price'];
									}
								} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {
									$pro_price = get_post_meta( $pro_id, 'mwb_wgm_pricing', true );
									if ( isset( $pro_price ) && is_array( $pro_price ) ) {
										$couponamont = $pro_price['default_price'];
									}
								} else {
									$couponamont = $order->get_line_subtotal( $item, $inc_tax_status );
								}

								$gift_couponnumber = get_post_meta( $pro_id, 'coupon_code', true );
								if ( empty( $gift_couponnumber ) && ! isset( $gift_couponnumber ) ) {
									$gift_couponnumber = mwb_wgm_coupon_generator( $giftcard_coupon_length );

								}

								if ( $this->mwb_wgm_create_gift_coupon( $gift_couponnumber, $couponamont, $order_id, $item['product_id'], $to ) ) {
									$todaydate = date_i18n( 'Y-m-d' );
									$expiry_date = get_post_meta( $pro_id, 'expiry_after_days', true );
									$expirydate_format = $this->mwb_wgm_check_expiry_date( $expiry_date );
									$mwb_wgm_common_arr['order_id'] = $order_id;
									$mwb_wgm_common_arr['product_id'] = $pro_id;
									$mwb_wgm_common_arr['to'] = $to;
									$mwb_wgm_common_arr['from'] = $from;
									$mwb_wgm_common_arr['to_name'] = $to_name;
									$mwb_wgm_common_arr['gift_couponnumber'] = $gift_couponnumber;
									$mwb_wgm_common_arr['gift_msg'] = $gift_msg;
									$mwb_wgm_common_arr['expirydate_format'] = $expirydate_format;
									$mwb_wgm_common_arr['selected_template'] = $selected_template;
									$mwb_wgm_common_arr['couponamont'] = $couponamont;
									$mwb_wgm_common_arr['delivery_method'] = $delivery_method;
									$mwb_wgm_common_arr['gift_img_name'] = $gift_img_name;
									$mwb_wgm_common_arr['item_id'] = $item_id;

									$mwb_wgm_common_arr = apply_filters( 'mwb_wgm_customized_gc_cgc', $mwb_wgm_common_arr, $item, $order );
									if ( $this->mwb_wgm_common_functionality( $mwb_wgm_common_arr, $order ) ) {

										update_post_meta( $pro_id, '_stock_status', 'outofstock' );
									}
								}
							} elseif ( ! empty( $imported_coupons ) ) {
								for ( $i = 0; $i < $item_quantity; $i++ ) {
									$imported_code = $imported_coupons[ $i ]->post_title;
									if ( isset( $imported_code ) && ! empty( $imported_code ) ) {
										$the_coupon = new WC_Coupon( $imported_code );
										if ( $woo_ver < '3.0.0' ) {
											$import_coupon_id = $the_coupon->id;
										} else {
											$import_coupon_id = $the_coupon->get_id();
										}
										$expiry_date = get_post_meta( $import_coupon_id, 'mwb_wgm_expiry_date', true );

										$expirydate_format = $this->mwb_wgm_check_expiry_date( $expiry_date );

										$mwb_wgm_common_arr['order_id'] = $order_id;
										$mwb_wgm_common_arr['product_id'] = $pro_id;
										$mwb_wgm_common_arr['to'] = $to;
										$mwb_wgm_common_arr['from'] = $from;
										$mwb_wgm_common_arr['to_name'] = $to_name;
										$mwb_wgm_common_arr['gift_couponnumber'] = $imported_code;
										$mwb_wgm_common_arr['gift_msg'] = $gift_msg;
										$mwb_wgm_common_arr['expirydate_format'] = $expirydate_format;
										$mwb_wgm_common_arr['selected_template'] = $selected_template;
										$mwb_wgm_common_arr['couponamont'] = $couponamont;
										$mwb_wgm_common_arr['delivery_method'] = $delivery_method;
										$mwb_wgm_common_arr['gift_img_name'] = $gift_img_name;
										$mwb_wgm_common_arr['item_id'] = $item_id;

										$mwb_wgm_common_arr = apply_filters( 'mwb_wgm_customized_gc_cgc', $mwb_wgm_common_arr, $item, $order );
										if ( $this->mwb_wgm_common_functionality( $mwb_wgm_common_arr, $order ) ) {
											update_post_meta( $import_coupon_id, 'coupon_amount', $couponamont );
											update_post_meta( $import_coupon_id, 'mwb_wgm_imported_coupon', 'purchased' );
											update_post_meta( $import_coupon_id, 'mwb_wgm_giftcard_coupon', $order_id );
											update_post_meta( $import_coupon_id, 'mwb_wgm_giftcard_coupon_unique', 'online' );
											update_post_meta( $import_coupon_id, 'mwb_wgm_giftcard_coupon_product_id', $pro_id );
											update_post_meta( $import_coupon_id, 'mwb_wgm_giftcard_coupon_mail_to', $to );

											$woo_ver = WC()->version;

											if ( ! strtotime( $expirydate_format ) ) {

												$expirydate_format = null;
											}

											if ( $woo_ver < '3.6.0' ) {
												update_post_meta( $import_coupon_id, 'expiry_date', $expirydate_format );
											} else {

												$expirydate_format = strtotime( $expirydate_format );

												update_post_meta( $import_coupon_id, 'date_expires', $expirydate_format );
											}
										}
									} elseif ( empty( $imported_code ) ) {
										$giftcard_coupon_length = get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 );
										$random_code = mwb_wgm_coupon_generator( $giftcard_coupon_length );
										if ( $this->mwb_wgm_create_gift_coupon( $random_code, $couponamont, $order_id, $item['product_id'], $to ) ) {
											$todaydate = date_i18n( 'Y-m-d' );
											$expiry_date = get_option( 'mwb_wgm_general_setting_giftcard_expiry', false );
											$expirydate_format = $this->mwb_wgm_check_expiry_date( $expiry_date );
											$mwb_wgm_common_arr['order_id'] = $order_id;
											$mwb_wgm_common_arr['product_id'] = $pro_id;
											$mwb_wgm_common_arr['to'] = $to;
											$mwb_wgm_common_arr['from'] = $from;
											$mwb_wgm_common_arr['to_name'] = $to_name;
											$mwb_wgm_common_arr['gift_couponnumber'] = $random_code;
											$mwb_wgm_common_arr['gift_msg'] = $gift_msg;
											$mwb_wgm_common_arr['expirydate_format'] = $expirydate_format;
											$mwb_wgm_common_arr['selected_template'] = $selected_template;
											$mwb_wgm_common_arr['couponamont'] = $couponamont;
											$mwb_wgm_common_arr['delivery_method'] = $delivery_method;
											$mwb_wgm_common_arr['gift_img_name'] = $gift_img_name;
											$mwb_wgm_common_arr['item_id'] = $item_id;
											$mwb_wgm_common_arr = apply_filters( 'mwb_wgm_customized_gc_cgc', $mwb_wgm_common_arr, $item, $order );
											if ( $this->mwb_wgm_common_functionality( $mwb_wgm_common_arr, $order ) ) {
											}
										}
									}
								}
							} else {
								$giftcard_coupon_length = get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 );

								for ( $i = 1; $i <= $item_quantity; $i++ ) {
									$gift_couponnumber = mwb_wgm_coupon_generator( $giftcard_coupon_length );
									if ( $this->mwb_wgm_create_gift_coupon( $gift_couponnumber, $couponamont, $order_id, $item['product_id'], $to ) ) {
										$todaydate = date_i18n( 'Y-m-d' );
										$expiry_date = get_option( 'mwb_wgm_general_setting_giftcard_expiry', false );
										$expirydate_format = $this->mwb_wgm_check_expiry_date( $expiry_date );
										$mwb_wgm_common_arr['order_id'] = $order_id;
										$mwb_wgm_common_arr['product_id'] = $pro_id;
										$mwb_wgm_common_arr['to'] = $to;
										$mwb_wgm_common_arr['from'] = $from;
										$mwb_wgm_common_arr['to_name'] = $to_name;
										$mwb_wgm_common_arr['gift_couponnumber'] = $gift_couponnumber;
										$mwb_wgm_common_arr['gift_msg'] = $gift_msg;
										$mwb_wgm_common_arr['expirydate_format'] = $expirydate_format;
										$mwb_wgm_common_arr['selected_template'] = $selected_template;
										$mwb_wgm_common_arr['couponamont'] = $couponamont;
										$mwb_wgm_common_arr['delivery_method'] = $delivery_method;
										$mwb_wgm_common_arr['gift_img_name'] = $gift_img_name;
										$mwb_wgm_common_arr['item_id'] = $item_id;
										$mwb_wgm_common_arr = apply_filters( 'mwb_wgm_customized_gc_cgc', $mwb_wgm_common_arr, $item, $order );
										if ( $this->mwb_wgm_common_functionality( $mwb_wgm_common_arr, $order ) ) {
										}
									}
								}
							}
						}
					}
					if ( $gift_order && $datecheck ) {
						update_post_meta( $order_id, 'mwb_wgm_order_giftcard', 'send' );
					}
					$thankyouorder_enable = get_option( 'mwb_wgm_thankyouorder_enable', false );
					if ( isset( $thankyouorder_enable ) && ! empty( $thankyouorder_enable ) && $thankyouorder_enable == 'on' ) {

						$thankyouorder_type = get_option( 'mwb_wgm_thankyouorder_type', 'mwb_wgm_fixed_thankyou' );
						$thankyouorder_time = get_option( 'mwb_wgm_thankyouorder_time', 'mwb_wgm_complete_status' );

						if ( class_exists( 'WOOCS' ) ) {
							global $WOOCS;
							$rate = 1;
							$currency = $WOOCS->current_currency;
							$currencies = $WOOCS->get_currencies();
							$rate = $currencies[ $currency ]['rate'];
							$decimal = $currencies[ $currency ]['decimals'];
							$cur_cur = $WOOCS->current_currency;
							$WOOCS->reset_currency();
							$WOOCS->set_currency( $cur_cur );

							$thankyouorder_min = get_option( 'mwb_wgm_thankyouorder_minimum', array() );
							$thankyouorder_max = get_option( 'mwb_wgm_thankyouorder_maximum', array() );
							$thankyouorder_value = get_option( 'mwb_wgm_thankyouorder_current_type', array() );

							if ( is_array( $thankyouorder_value ) && ! empty( $thankyouorder_value ) && count( $thankyouorder_value ) ) {
								foreach ( $thankyouorder_value as $key => $value ) {
									if ( isset( $thankyouorder_min[ $key ] ) && ! empty( $thankyouorder_min[ $key ] ) && isset( $thankyouorder_max[ $key ] ) && ! empty( $thankyouorder_max[ $key ] ) ) {
										$thankyouorder_min[ $key ] = round( floatval( $thankyouorder_min[ $key ] * $rate ), $decimal );
										$thankyouorder_max[ $key ] = round( floatval( $thankyouorder_max[ $key ] * $rate ), $decimal );

									} else if ( isset( $thankyouorder_min[ $key ] ) && ! empty( $thankyouorder_min[ $key ] ) && empty( $thankyouorder_max[ $key ] ) ) {
										$thankyouorder_min[ $key ] = round( floatval( $thankyouorder_min[ $key ] * $rate ), $decimal );
									}
								}
							}
						} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {

							if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {

								$thankyouorder_min = get_option( 'mwb_wgm_thankyouorder_minimum', array() );
								$thankyouorder_max = get_option( 'mwb_wgm_thankyouorder_maximum', array() );
								$thankyouorder_value = get_option( 'mwb_wgm_thankyouorder_current_type', array() );
								if ( is_array( $thankyouorder_value ) && ! empty( $thankyouorder_value ) && count( $thankyouorder_value ) ) {
									foreach ( $thankyouorder_value as $key => $value ) {
										if ( isset( $thankyouorder_min[ $key ] ) && ! empty( $thankyouorder_min[ $key ] ) && isset( $thankyouorder_max[ $key ] ) && ! empty( $thankyouorder_max[ $key ] ) ) {
											$thankyouorder_min[ $key ] = wcpbc_the_zone()->get_exchange_rate_price( $thankyouorder_min[ $key ] );
											$thankyouorder_max[ $key ] = wcpbc_the_zone()->get_exchange_rate_price( $thankyouorder_max[ $key ] );

										} else if ( isset( $thankyouorder_min[ $key ] ) && ! empty( $thankyouorder_min[ $key ] ) && empty( $thankyouorder_max[ $key ] ) ) {

											$thankyouorder_min[ $key ] = wcpbc_the_zone()->get_exchange_rate_price( $thankyouorder_min[ $key ] );
										}
									}
								}
							}
						} else {
							$thankyouorder_min = get_option( 'mwb_wgm_thankyouorder_minimum', array() );
							$thankyouorder_max = get_option( 'mwb_wgm_thankyouorder_maximum', array() );
							$thankyouorder_value = get_option( 'mwb_wgm_thankyouorder_current_type', array() );
						}

						$mwb_wgm_thankyouorder_number = (int) get_option( 'mwb_wgm_thankyouorder_number', 1 );
						if ( $thankyouorder_time == 'mwb_wgm_processing_status' || $thankyouorder_time == 'mwb_wgm_complete_status' ) {

							$coupon_alreadycreated = get_post_meta( $order_id, 'mwb_wgm_thnkyou_coupon_created', true );
							if ( $coupon_alreadycreated == 'send' ) {
								return;
							}
							$order_total = $order->get_total();

							$thankyou_user_order = (int) get_user_meta( $user_id, 'thankyou_order_number', true );
							$user = get_user_by( 'ID', $user_id );
							$user_email = $user->user_email;

							if ( $thankyou_user_order >= $mwb_wgm_thankyouorder_number ) {

								if ( is_array( $thankyouorder_value ) && ! empty( $thankyouorder_value ) ) {
									foreach ( $thankyouorder_value as $key => $value ) {
										if ( $coupon_alreadycreated == 'send' ) {
											return;
										}
										if ( isset( $thankyouorder_min[ $key ] ) && ! empty( $thankyouorder_min[ $key ] ) && isset( $thankyouorder_max[ $key ] ) && ! empty( $thankyouorder_max[ $key ] ) ) {
											if ( $thankyouorder_min[ $key ] <= $order_total && $order_total <= $thankyouorder_max[ $key ] ) {
												$thnku_coupon_length = get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 );
												$thanku_couponnumber = mwb_wgm_coupon_generator( $thnku_coupon_length );
												$thnku_couponamount = $thankyouorder_value[ $key ];
												if ( $this->mwb_wgm_create_thnku_coupon( $thanku_couponnumber, $thnku_couponamount, $order_id, $thankyouorder_type, $user_id ) ) {
													$coupon_creation = true;
													$the_coupon = new WC_Coupon( $thanku_couponnumber );
													$thnku_couponamount = $the_coupon->get_amount();

													$expiry_date_timestamp = $the_coupon->get_date_expires();
													$date_format = get_option( 'date_format' );
													if ( ! isset( $date_format ) && empty( $date_format ) ) {
														$date_format = 'Y-m-d';
													}
													if ( ! empty( $expiry_date_timestamp ) && isset( $expiry_date_timestamp ) ) {
														$expiry_date_timestamp = strtotime( $expiry_date_timestamp );
													}
													if ( empty( $expiry_date_timestamp ) ) {
														$expirydate_format = __( 'No Expiration', 'woocommerce-ultimate-gift-card' );
													} else {
														$expirydate_format = date_i18n( $date_format, $expiry_date_timestamp );
													}
													$bloginfo = get_bloginfo();
													$headers = array( 'Content-Type: text/html; charset=UTF-8' );
													$message_thnku = str_replace( '[COUPONCODE]', $thanku_couponnumber, $message_thnku );
													if ( $thankyouorder_type == 'mwb_wgm_fixed_thankyou' ) {
														$message_thnku = str_replace( '[COUPONAMOUNT]', wc_price( $thnku_couponamount ), $message_thnku );
													} else if ( $thankyouorder_type == 'mwb_wgm_percentage_thankyou' ) {
														$message_thnku = str_replace( '[COUPONAMOUNT]', $thnku_couponamount . '%', $message_thnku );
													}
													$message_thnku = str_replace( '[COUPONEXPIRY]', $expirydate_format, $message_thnku );
													wc_mail( $user_email, $thnku_subject, $message_thnku, $headers );
													update_post_meta( $order_id, 'mwb_wgm_thnkyou_coupon_created', 'send' );
												}
											}
										} else if ( isset( $thankyouorder_min[ $key ] ) && ! empty( $thankyouorder_min[ $key ] ) && empty( $thankyouorder_max[ $key ] ) ) {

											if ( $thankyouorder_min[ $key ] <= $order_total ) {
												$thnku_coupon_length = get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 );
												$thanku_couponnumber = mwb_wgm_coupon_generator( $thnku_coupon_length );
												$thnku_couponamount = $thankyouorder_value[ $key ];
												if ( $this->mwb_wgm_create_thnku_coupon( $thanku_couponnumber, $thnku_couponamount, $order_id, $thankyouorder_type, $user_id ) ) {
													$coupon_creation = true;
													$the_coupon = new WC_Coupon( $thanku_couponnumber );
													$thnku_couponamount = $the_coupon->get_amount();
													$expiry_date_timestamp = $the_coupon->get_date_expires();
													$date_format = get_option( 'date_format' );
													if ( ! isset( $date_format ) && empty( $date_format ) ) {
														$date_format = 'Y-m-d';
													}
													if ( ! empty( $expiry_date_timestamp ) && isset( $expiry_date_timestamp ) ) {
														$expiry_date_timestamp = strtotime( $expiry_date_timestamp );
													}
													if ( empty( $expiry_date_timestamp ) ) {
														$expirydate_format = __( 'No Expiration', 'woocommerce-ultimate-gift-card' );
													} else {
														$expirydate_format = date_i18n( $date_format, $expiry_date_timestamp );
													}
													$bloginfo = get_bloginfo();
													$headers = array( 'Content-Type: text/html; charset=UTF-8' );
													$message_thnku = str_replace( '[COUPONCODE]', $thanku_couponnumber, $message_thnku );
													if ( $thankyouorder_type == 'mwb_wgm_fixed_thankyou' ) {
														$message_thnku = str_replace( '[COUPONAMOUNT]', wc_price( $thnku_couponamount ), $message_thnku );
													} else if ( $thankyouorder_type == 'mwb_wgm_percentage_thankyou' ) {
														$message_thnku = str_replace( '[COUPONAMOUNT]', $thnku_couponamount . '%', $message_thnku );
													}
													$message_thnku = str_replace( '[COUPONEXPIRY]', $expirydate_format, $message_thnku );
													wc_mail( $user_email, $thnku_subject, $message_thnku, $headers );
													update_post_meta( $order_id, 'mwb_wgm_thnkyou_coupon_created', 'send' );
												}
											}
										} else if ( isset( $thankyouorder_value[ $key ] ) && ! empty( $thankyouorder_value[ $key ] ) && empty( $thankyouorder_min[ $key ] ) && empty( $thankyouorder_max[ $key ] ) ) {
											$thnku_coupon_length = get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 );
											$thanku_couponnumber = mwb_wgm_coupon_generator( $thnku_coupon_length );
											$thnku_couponamount = $thankyouorder_value[ $key ];
											if ( $this->mwb_wgm_create_thnku_coupon( $thanku_couponnumber, $thnku_couponamount, $order_id, $thankyouorder_type, $user_id ) ) {
												$coupon_creation = true;
												$the_coupon = new WC_Coupon( $thanku_couponnumber );
												$thnku_couponamount = $the_coupon->get_amount();
												$expiry_date_timestamp = $the_coupon->get_date_expires();
												$date_format = get_option( 'date_format' );
												if ( ! isset( $date_format ) && empty( $date_format ) ) {
													$date_format = 'Y-m-d';
												}
												if ( ! empty( $expiry_date_timestamp ) && isset( $expiry_date_timestamp ) ) {
													$expiry_date_timestamp = strtotime( $expiry_date_timestamp );
												}
												if ( empty( $expiry_date_timestamp ) ) {
													$expirydate_format = __( 'No Expiration', 'woocommerce-ultimate-gift-card' );
												} else {
													$expirydate_format = date_i18n( $date_format, $expiry_date_timestamp );
												}
												$bloginfo = get_bloginfo();
												$headers = array( 'Content-Type: text/html; charset=UTF-8' );
												$message_thnku = str_replace( '[COUPONCODE]', $thanku_couponnumber, $message_thnku );
												if ( $thankyouorder_type == 'mwb_wgm_fixed_thankyou' ) {
													$message_thnku = str_replace( '[COUPONAMOUNT]', wc_price( $thnku_couponamount ), $message_thnku );
												} else if ( $thankyouorder_type == 'mwb_wgm_percentage_thankyou' ) {
													$message_thnku = str_replace( '[COUPONAMOUNT]', $thnku_couponamount . '%', $message_thnku );
												}
												$message_thnku = str_replace( '[COUPONEXPIRY]', $expirydate_format, $message_thnku );
												wc_mail( $user_email, $thnku_subject, $message_thnku, $headers );
												update_post_meta( $order_id, 'mwb_wgm_thnkyou_coupon_created', 'send' );
											}
										}
									}
								}
							}
								/*
								if($coupon_creation)
								{
								update_post_meta($order_id,'mwb_wgm_thnkyou_coupon_created','send');
							}*/
						}
					}
				}
			}
		}
	}
		/**
		 * This function is used to create giftcoupon for given amount
		 *
		 * @param $gift_couponnumber
		 * @param $couponamont
		 * @param $order_id
		 * @return boolean
		 */
		function mwb_wgm_create_gift_coupon( $gift_couponnumber, $couponamont, $order_id, $product_id, $to ) {
			$mwb_wgm_enable = mwb_wgm_giftcard_enable();
			if ( $mwb_wgm_enable ) {
				$alreadycreated = get_post_meta( $order_id, 'mwb_wgm_order_giftcard', true );
				if ( $alreadycreated != 'send' ) {
					$coupon_code = $gift_couponnumber; // Code
					$amount = $couponamont; // Amount
					$discount_type = 'fixed_cart';
					$coupon_description = "GIFTCARD ORDER #$order_id";

					$coupon = array(
						'post_title' => $coupon_code,
						'post_content' => $coupon_description,
						'post_excerpt' => $coupon_description,
						'post_status' => 'publish',
						'post_author' => get_current_user_id(),
						'post_type'     => 'shop_coupon',
					);
					$new_coupon_id = wp_insert_post( $coupon );
					//Added in WooCommerce 3.9.0
					if ( $new_coupon_id ) {
						$coupon_obj = new WC_Coupon( $new_coupon_id );
						$coupon_obj->save();
					}
					$individual_use = get_option( 'mwb_wgm_general_setting_giftcard_individual_use', 'no' );
					$usage_limit = get_option( 'mwb_wgm_general_setting_giftcard_use', 1 );
					$expiry_date = get_option( 'mwb_wgm_general_setting_giftcard_expiry', 10 );
					$free_shipping = get_option( 'mwb_wgm_general_setting_giftcard_freeshipping', 1 );
					$apply_before_tax = get_option( 'mwb_wgm_general_setting_giftcard_applybeforetx', 'yes' );
					$minimum_amount = get_option( 'mwb_wgm_general_setting_giftcard_minspend', '' );
					$maximum_amount = get_option( 'mwb_wgm_general_setting_giftcard_maxspend', '' );
					$exclude_sale_items = get_option( 'mwb_wgm_general_setting_giftcard_ex_sale', 'no' );
					$mwb_wgm_exclude_per_product = get_post_meta( $product_id, 'mwb_wgm_exclude_per_pro_format', true );
					$mwb_wgm_exclude_per_category = get_post_meta( $product_id, 'mwb_wgm_exclude_per_category', array() );
					$exclude_products = get_option( 'mwb_wgm_product_setting_exclude_product_format', '' );

					$exclude_category = get_option( 'mwb_wgm_product_setting_exclude_category', '' );
					$todaydate = date_i18n( 'Y-m-d' );
					$include_products_format = get_option( 'include_products_format', '' );

					$include_categories = get_option( 'mwb_wgm_include_categories', array() );
					$mwb_wgm_include_per_product = get_post_meta( $product_id, 'mwb_wgm_include_per_pro_format', true );
					$mwb_wgm_include_per_category = get_post_meta( $product_id, 'mwb_wgm_include_per_category', array() );

					// to check the giftcard is imported.
					$is_imported_product = get_post_meta( $product_id, 'is_imported', true );

					// set the expiry date of imported giftcard.
					if ( isset( $is_imported_product ) && ! empty( $is_imported_product ) && $is_imported_product == 'yes' ) {

						$expiry_date = get_post_meta( $product_id, 'expiry_after_days', true );

						if ( $expiry_date > 0 || $expiry_date === 0 ) {
							$expirydate = date_i18n( 'Y-m-d', strtotime( "$todaydate +$expiry_date day" ) );
						} else {
							$expirydate = '';
						}
					} else {
						if ( $expiry_date > 0 || $expiry_date === 0 ) {
							$expirydate = date_i18n( 'Y-m-d', strtotime( "$todaydate +$expiry_date day" ) );
						} else {
							$expirydate = '';
						}
					}
					// Add meta
					// price based on country
					if ( class_exists( 'WCPBC_Pricing_Zone' ) ) {

						update_post_meta( $new_coupon_id, 'zone_pricing_type', 'exchange_rate' );
					}

					update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
					update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
					update_post_meta( $new_coupon_id, 'individual_use', $individual_use );
					update_post_meta( $new_coupon_id, 'usage_limit', $usage_limit );

					$woo_ver = WC()->version;

					if ( $woo_ver < '3.6.0' ) {

						update_post_meta( $new_coupon_id, 'expiry_date', $expirydate );
					} else {
						$expirydate = strtotime( $expirydate );
						update_post_meta( $new_coupon_id, 'date_expires', $expirydate );
					}

					update_post_meta( $new_coupon_id, 'apply_before_tax', $apply_before_tax );
					update_post_meta( $new_coupon_id, 'free_shipping', $free_shipping );
					update_post_meta( $new_coupon_id, 'minimum_amount', $minimum_amount );
					update_post_meta( $new_coupon_id, 'maximum_amount', $maximum_amount );
					update_post_meta( $new_coupon_id, 'exclude_sale_items', $exclude_sale_items );

					if ( isset( $mwb_wgm_exclude_per_product ) && $mwb_wgm_exclude_per_product != '' ) {
						update_post_meta( $new_coupon_id, 'exclude_product_ids', $mwb_wgm_exclude_per_product );
					} else {
						update_post_meta( $new_coupon_id, 'exclude_product_ids', $exclude_products );
					}

					if ( isset( $mwb_wgm_exclude_per_category[0] ) && is_array( $mwb_wgm_exclude_per_category[0] ) && ! empty( $mwb_wgm_exclude_per_category[0] ) ) {
						update_post_meta( $new_coupon_id, 'exclude_product_categories', $mwb_wgm_exclude_per_category[0] );
					} else {
						update_post_meta( $new_coupon_id, 'exclude_product_categories', $exclude_category );
					}

					if ( isset( $mwb_wgm_include_per_product ) && $mwb_wgm_include_per_product != '' ) {
						update_post_meta( $new_coupon_id, 'product_ids', $mwb_wgm_include_per_product );
					} else {
						update_post_meta( $new_coupon_id, 'product_ids', $include_products_format );
					}

					if ( isset( $mwb_wgm_include_per_category[0] ) && is_array( $mwb_wgm_include_per_category[0] ) && ! empty( $mwb_wgm_include_per_category[0] ) ) {
						update_post_meta( $new_coupon_id, 'product_categories', $mwb_wgm_include_per_category[0] );
					} else {
						update_post_meta( $new_coupon_id, 'product_categories', $include_categories );
					}

					update_post_meta( $new_coupon_id, 'mwb_wgm_giftcard_coupon', $order_id );
					update_post_meta( $new_coupon_id, 'mwb_wgm_giftcard_coupon_unique', 'online' );
					update_post_meta( $new_coupon_id, 'mwb_wgm_giftcard_coupon_product_id', $product_id );
					update_post_meta( $new_coupon_id, 'mwb_wgm_giftcard_coupon_mail_to', $to );

					return true;
				} else {
					return false;
				}
			}
		}

		/**
		 * This function is used to create the dynamic Email template
		 *
		 * @name mwb_wgm_giftttemplate
		 * @param $args
		 * @return string
		 */
		function mwb_wgm_giftttemplate( $args ) {

			$mwb_wgm_enable = mwb_wgm_giftcard_enable();
			if ( $mwb_wgm_enable ) {
				$templateid = $args['templateid'];

				$product_id = $args['product_id'];

				$template = get_post( $templateid, ARRAY_A );
				$templatehtml = $template['post_content'];
				$giftcard_logo_html = '';
				$order_id = isset( $args['order_id'] ) ? $args['order_id'] : '';
				$from_email = '';
				if ( ! empty( $order_id ) ) {
					$order = wc_get_order( $order_id );
					$from_email = $order->get_billing_email();
				}

				$giftcard_upload_logo = get_option( 'mwb_wgm_other_setting_upload_logo', false );
				$giftcard_logo_height = get_option( 'mwb_wgm_other_setting_logo_height', false );
				$giftcard_logo_width = get_option( 'mwb_wgm_other_setting_logo_width', false );

				// PRODUCTNAME SHORTCODE.
				$product = wc_get_product( $product_id );
				$product_title = '';
				$pro_permalink = '';
				$product_format = '';
				if ( ! empty( $product ) ) {
					$product_title = $product->get_name();
					$pro_permalink = $product->get_permalink();
					$product_format = "<a href='$pro_permalink'>$product_title</a>";
				}

				if ( empty( $giftcard_logo_height ) ) {
					$giftcard_logo_height = 70;
				}
				if ( empty( $giftcard_logo_width ) ) {
					$giftcard_logo_width = 70;
				}

				if ( isset( $giftcard_upload_logo ) && ! empty( $giftcard_upload_logo ) ) {
					$giftcard_logo_html = "<img src='$giftcard_upload_logo' width='" . $giftcard_logo_width . "px' height='" . $giftcard_logo_height . "px'/>";
				}
				$giftcard_disclaimer = get_option( 'mwb_wgm_other_setting_disclaimer', false );
				$giftcard_disclaimer = stripcslashes( $giftcard_disclaimer );

				$background_image = wp_get_attachment_url( get_post_thumbnail_id( $product_id ) );
				$featured_image = wp_get_attachment_url( get_post_thumbnail_id( $templateid ) );
				if ( empty( $background_image ) ) {
					$background_image = get_option( 'mwb_wgm_other_setting_background_logo', false );
				}

				$background_color = get_option( 'mwb_wgm_other_setting_background_color', false );
				$giftcard_event_html = '';
				if ( isset( $background_image ) && ! empty( $background_image ) ) {
					$giftcard_event_html = "<img src='$background_image' width='100%' />";
				}

				$browse_enable = get_option( 'mwb_wgm_other_setting_browse', false );
				if ( $browse_enable == 'on' ) {
					if ( isset( $args['browse_image'] ) && $args['browse_image'] != null ) {
						$giftcard_event_html = "<img src='" . content_url( 'uploads/mwb_browse/' . $args['browse_image'] ) . "' width='100%' />";
					}
				}

				$giftcard_featured = '';
				if ( isset( $featured_image ) && ! empty( $featured_image ) ) {
					$giftcard_featured = "<img src='$featured_image'/>";
				}
				$template_css = get_post_meta( $templateid, 'mwb_css_field', true );

				if ( $template_css != null && $template_css != '' ) {
					$giftcard_css = "<style>$template_css</style>";
				} else {
					$giftcard_css = "<style>
					table{
						background-color: $background_color ;
					}
					</style>";
					$giftcard_custom_css = get_option( 'mwb_wgm_other_setting_mail_style', false );
					$giftcard_custom_css = stripcslashes( $giftcard_custom_css );
					$giftcard_css .= "<style>$giftcard_custom_css</style>";
				}
				if ( isset( $args['message'] ) && ! empty( $args['message'] ) ) {
					$templatehtml = str_replace( '[MESSAGE]', nl2br( $args['message'] ), $templatehtml );
				} else {
					$templatehtml = str_replace( '[MESSAGE]', '', $templatehtml );
				}
				if ( isset( $args['to'] ) && ! empty( $args['to'] ) ) {
					$templatehtml = str_replace( '[TO]', $args['to'], $templatehtml );
				} else {
					$templatehtml = str_replace( 'To:', '', $templatehtml );
					$templatehtml = str_replace( 'To :', '', $templatehtml );
					$templatehtml = str_replace( 'To-', '', $templatehtml );
					$templatehtml = str_replace( '[TO]', '', $templatehtml );
				}
				if ( isset( $args['from'] ) && ! empty( $args['from'] ) ) {
					$templatehtml = str_replace( '[FROM]', $args['from'], $templatehtml );
				} else {
					$templatehtml = str_replace( 'From :', '', $templatehtml );
					$templatehtml = str_replace( 'From:', '', $templatehtml );
					$templatehtml = str_replace( 'From-', '', $templatehtml );
					$templatehtml = str_replace( '[FROM]', '', $templatehtml );
				}
				if ( is_rtl() ) {
					$templatehtml = str_replace( 'ltr', 'rtl', $templatehtml );
				}

				// Background Image for Mothers Day
				$mothers_day_backimg = MWB_WGM_URL . 'assets/images/back.png';
				$mothers_day_backimg = "<span class='back_bubble_img'><img src='$mothers_day_backimg'/></span>";
				// Arrow Image for Mothers Day
				$arrow_img = MWB_WGM_URL . 'assets/images/arrow.png';
				$arrow_img = "<img src='$arrow_img'  class='center-on-narrow' style='height: auto;font-family: sans-serif; font-size: 15px; line-height: 20px; color: rgb(85, 85, 85); border-radius: 5px;' width='135' height='170' border='0'>";
				$templatehtml = str_replace( '[ARROWIMAGE]', $arrow_img, $templatehtml );
				$templatehtml = str_replace( '[BACK]', $mothers_day_backimg, $templatehtml );
				$templatehtml = str_replace( '[LOGO]', $giftcard_logo_html, $templatehtml );
				$templatehtml = str_replace( '[AMOUNT]', $args['amount'], $templatehtml );
				$templatehtml = str_replace( '[COUPON]', $args['coupon'], $templatehtml );
				$templatehtml = str_replace( '[EXPIRYDATE]', $args['expirydate'], $templatehtml );
				$templatehtml = str_replace( '[DISCLAIMER]', $giftcard_disclaimer, $templatehtml );
				$templatehtml = str_replace( '[DEFAULTEVENT]', $giftcard_event_html, $templatehtml );
				$templatehtml = str_replace( '[FEATUREDIMAGE]', $giftcard_featured, $templatehtml );
				$templatehtml = str_replace( '[PRODUCTNAME]', $product_format, $templatehtml );
				$templatehtml = str_replace( '[ORDERID]', $order_id, $templatehtml );
				$templatehtml = str_replace( '[FROMEMAIL]', $from_email, $templatehtml );
				$templatehtml = $giftcard_css . $templatehtml;

				$templatehtml = apply_filters( 'mwb_wgm_email_template_html', $templatehtml, $product, $order_id );

				return $templatehtml;
			}
		}

		/**
		 * This function is used to change the coupon amount when coupon is applied
		 *
		 * @name mwb_wgm_woocommerce_order_add_coupon
		 * @param $order_id
		 * @param $item_id
		 * @param $coupon_code
		 * @param $discount_amount
		 * @param $discount_amount_tax
		 */
		function mwb_wgm_woocommerce_order_add_coupon( $order_id, $item_id, $coupon_code, $discount_amount, $discount_amount_tax ) {
			$the_coupon = new WC_Coupon( $coupon_code );

			$coupon_id = $the_coupon->id;
			if ( isset( $coupon_id ) ) {
				$rate = 1;
				if ( class_exists( 'WOOCS' ) ) {
					global $WOOCS;
					$order_currency = $WOOCS->current_currency;
					$currencies = $WOOCS->get_currencies();
					$rate = $currencies[ $order_currency ]['rate'];
				}
				$giftcardcoupon = get_post_meta( $coupon_id, 'mwb_wgm_giftcard_coupon', true );
				$to = get_post_meta( $coupon_id, 'mwb_wgm_giftcard_coupon_mail_to', true );
				$subject = get_option( 'mwb_wgm_other_setting_receive_coupon_subject', false );
				$message = get_option( 'mwb_wgm_other_setting_receive_coupon_message', false );
				$giftcard_disclaimer = get_option( 'mwb_wgm_other_setting_disclaimer', false );
				$bloginfo = get_bloginfo();
				if ( empty( $subject ) || ! isset( $subject ) ) {
					$subject = "$bloginfo:";
					$subject .= __( 'Coupon Amount Notification', 'woocommerce-ultimate-gift-card' );
				}
				if ( empty( $message ) ) {
					$message = file_get_contents( MWB_WGM_DIRPATH . '/admin/template/coupon_template.php' );
				}
				if ( empty( $giftcard_disclaimer ) || ! isset( $giftcard_disclaimer ) ) {

					$giftcard_disclaimer = __( 'Disclaimer Text', 'woocommerce-ultimate-gift-card' );

				}

				if ( ! empty( $giftcardcoupon ) ) {
					$amount = get_post_meta( $coupon_id, 'coupon_amount', true );
					$total_discount = $discount_amount / $rate;
					if ( $amount < $total_discount ) {
						$remaining_amount = 0;
					} else {
						$remaining_amount = $amount - $total_discount;
						$remaining_amount = round( $remaining_amount, 2 );
					}
					update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount );
					$subject = str_replace( '[SITENAME]', $bloginfo, $subject );
					$subject = stripcslashes( $subject );
					$subject = html_entity_decode( $subject, ENT_QUOTES, 'UTF-8' );

					$currency_symbol = get_woocommerce_currency_symbol();
					if ( class_exists( 'WOOCS' ) ) {
						global $WOOCS;
						$currency = $WOOCS->default_currency;
						$currencies = $WOOCS->get_currencies();
						$currency_symbol = $currencies[ $currency ]['symbol'];
					}
					$message = str_replace( '[COUPONAMOUNT]', $currency_symbol . $remaining_amount, $message );
					$message = str_replace( '[SITENAME]', $bloginfo, $message );
					$message = str_replace( '[DISCLAIMER]', $giftcard_disclaimer, $message );
					$message = stripcslashes( $message );
					$message = html_entity_decode( $message, ENT_QUOTES, 'UTF-8' );
					$mwb_wgc_bcc_enable = get_option( 'mwb_wgm_addition_bcc_option_enable', false );
					if ( isset( $mwb_wgc_bcc_enable ) && $mwb_wgc_bcc_enable == 'on' ) {
						$headers[] = 'Bcc:' . $from;
						wc_mail( $to, $subject, $message, $headers );
					} else {
						wc_mail( $to, $subject, $message );
					}
				} else {
					
					$couponpost = get_post( $coupon_id );
					$couponcontent = $couponpost->post_content;
					if ( ( strpos( $couponcontent, 'GIFTCARD ORDER #' ) !== false ) || ( strpos( $couponcontent, 'OFFLINE GIFTCARD ORDER #' ) !== false ) || ( strpos( $couponcontent, 'Imported Offline Coupon' ) !== false ) ) {
						$amount = get_post_meta( $coupon_id, 'coupon_amount', true );
						// $total_discount = $discount_amount+$discount_amount_tax;
						$total_discount = $discount_amount / $rate;
						if ( $amount < $total_discount ) {
							$remaining_amount = 0;
						} else {
							$remaining_amount = $amount - $total_discount;
							$remaining_amount = round( $remaining_amount, 2 );
						}
						update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount );
						$subject = str_replace( '[SITENAME]', $bloginfo, $subject );
						$subject = stripcslashes( $subject );
						$subject = html_entity_decode( $subject, ENT_QUOTES, 'UTF-8' );

						$currency_symbol = get_woocommerce_currency_symbol();
						if ( class_exists( 'WOOCS' ) ) {
							global $WOOCS;
							$currency = $WOOCS->default_currency;
							$currencies = $WOOCS->get_currencies();
							$currency_symbol = $currencies[ $currency ]['symbol'];
						}
						$message = str_replace( '[COUPONAMOUNT]', $currency_symbol . $remaining_amount, $message );
						// $message = str_replace('[COUPONAMOUNT]', get_woocommerce_currency_symbol().$remaining_amount, $message);
						$message = str_replace( '[SITENAME]', $bloginfo, $message );
						$message = str_replace( '[DISCLAIMER]', $giftcard_disclaimer, $message );
						$message = stripcslashes( $message );
						$message = html_entity_decode( $message, ENT_QUOTES, 'UTF-8' );
						$mwb_wgc_bcc_enable = get_option( 'mwb_wgm_addition_bcc_option_enable', false );
						if ( isset( $mwb_wgc_bcc_enable ) && $mwb_wgc_bcc_enable == 'on' ) {
							$headers[] = 'Bcc:' . $from;
							wc_mail( $to, $subject, $message, $headers );
						} else {
							wc_mail( $to, $subject, $message );
						}
					}
				}
			}
		}

		/**
		 * This function is used to change the coupon amount when coupon is applied in new version of woocommerce
		 *
		 * @name mwb_wgm_woocommerce_order_add_coupon_new_ver
		 * @param $order_id
		 * @param $item_id
		 * @param $coupon_code
		 * @param $discount_amount
		 * @param $discount_amount_tax
		 */
		function mwb_wgm_woocommerce_order_add_coupon_new_ver( $item_id, $item, $order_id ) {
			if ( get_class( $item ) == 'WC_Order_Item_Coupon' ) {

				// added code for currency switcher
				$rate = 1;
				if ( class_exists( 'WOOCS' ) ) {
					global $WOOCS;
					$order_currency = $WOOCS->current_currency;
					$currencies = $WOOCS->get_currencies();
					$rate = $currencies[ $order_currency ]['rate'];
				}
				if ( class_exists( 'WCPBC_Pricing_Zone' ) ) {

					if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {

						$rate = wcpbc_the_zone()->get_exchange_rate();

					}
				}

				$coupon_code = $item->get_code();
				$the_coupon = new WC_Coupon( $coupon_code );
				$coupon_id = $the_coupon->get_id();

				if ( isset( $coupon_id ) ) {

					$giftcardcoupon = get_post_meta( $coupon_id, 'mwb_wgm_giftcard_coupon', true );

					$to = get_post_meta( $coupon_id, 'mwb_wgm_giftcard_coupon_mail_to', true );
					$subject = get_option( 'mwb_wgm_other_setting_receive_coupon_subject', false );
					$message = get_option( 'mwb_wgm_other_setting_receive_coupon_message', false );
					$giftcard_disclaimer = get_option( 'mwb_wgm_other_setting_disclaimer', false );
					$bloginfo = get_bloginfo();
					if ( empty( $subject ) || ! isset( $subject ) ) {
						$subject = "$bloginfo:";
						$subject .= __( 'Coupon Amount Notification', 'woocommerce-ultimate-gift-card' );
					}
					if ( empty( $message ) ) {

						$message = file_get_contents( MWB_WGM_DIRPATH . '/admin/template/coupon_template.php' );
					}

					if ( empty( $giftcard_disclaimer ) || ! isset( $giftcard_disclaimer ) ) {

						$giftcard_disclaimer = __( 'Disclaimer Text', 'woocommerce-ultimate-gift-card' );

					}

					if ( ! empty( $giftcardcoupon ) ) {
						
						$mwb_ugc_discount = $item->get_discount();
						$mwb_ugc_discount_tax = $item->get_discount_tax();
						$amount = get_post_meta( $coupon_id, 'coupon_amount', true );
						$total_discount = $this->mwb_calculate_coupon_discount( $mwb_ugc_discount, $mwb_ugc_discount_tax );

							$total_discount = $total_discount / $rate;

						if ( $amount < $total_discount ) {
							$remaining_amount = 0;
						} else {
							$remaining_amount = $amount - $total_discount;
							$remaining_amount = round( $remaining_amount, 2 );
						}
						
						update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount);
						$subject = str_replace( '[SITENAME]', $bloginfo, $subject );
						$subject = stripcslashes( $subject );
						$subject = html_entity_decode( $subject, ENT_QUOTES, 'UTF-8' );

						// Added code for currency switcher

						if ( class_exists( 'WOOCS' ) ) {
							global $WOOCS;
							$rate = 1;
							$currency = $WOOCS->current_currency;
							$currencies = $WOOCS->get_currencies();
							$currency_symbol = $currencies[ $currency ]['symbol'];
							$rate = $currencies[ $currency ]['rate'];
							$decimal = $currencies[ $currency ]['decimals'];
							$cur_cur = $WOOCS->current_currency;
							$WOOCS->reset_currency();
							$WOOCS->set_currency( $cur_cur );

							$amt = $remaining_amount;
							$amt = floatval( $amt * $rate );
							$remaining_amount = round( $amt, $decimal );
							$message = str_replace( '[COUPONAMOUNT]', $currency_symbol . $remaining_amount, $message );

						} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {

							if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {

								$remaining_amount = wcpbc_the_zone()->get_exchange_rate_price( $remaining_amount );

								$message = str_replace( '[COUPONAMOUNT]', get_woocommerce_currency_symbol() . $remaining_amount, $message );
							} else {
								$message = str_replace( '[COUPONAMOUNT]', get_woocommerce_currency_symbol() . $remaining_amount, $message );
							}
						} else {
							$message = str_replace( '[COUPONAMOUNT]', get_woocommerce_currency_symbol() . $remaining_amount, $message );
						}

						$message = str_replace( '[SITENAME]', $bloginfo, $message );
						$message = str_replace( '[DISCLAIMER]', $giftcard_disclaimer, $message );
						$message = stripcslashes( $message );
						$message = html_entity_decode( $message, ENT_QUOTES, 'UTF-8' );
						$mwb_wgc_bcc_enable = get_option( 'mwb_wgm_addition_bcc_option_enable', false );
						if ( isset( $mwb_wgc_bcc_enable ) && $mwb_wgc_bcc_enable == 'on' ) {
							$headers[] = 'Bcc:' . $from;
							wc_mail( $to, $subject, $message, $headers );
						} else {
							wc_mail( $to, $subject, $message );
						}
					} else {
						$mwb_ugc_discount = $item->get_discount();
						$mwb_ugc_discount_tax = $item->get_discount_tax();
						$couponpost = get_post( $coupon_id );
						$couponcontent = $couponpost->post_content;

						if ( ( strpos( $couponcontent, 'GIFTCARD ORDER #' ) !== false ) || ( strpos( $couponcontent, 'OFFLINE GIFTCARD ORDER #' ) !== false ) || ( strpos( $couponcontent, 'Imported Offline Coupon' ) !== false ) ) {
							$amount = get_post_meta( $coupon_id, 'coupon_amount', true );
							$total_discount = $this->mwb_calculate_coupon_discount( $mwb_ugc_discount, $mwb_ugc_discount_tax );

							// added for currency switcher
							$total_discount = $total_discount / $rate;

							if ( $amount < $total_discount ) {
								$remaining_amount = 0;
							} else {
								$remaining_amount = $amount - $total_discount;
								$remaining_amount = round( $remaining_amount, 2 );
							}
							update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount );
							$subject = str_replace( '[SITENAME]', $bloginfo, $subject );
							$subject = stripcslashes( $subject );
							$subject = html_entity_decode( $subject, ENT_QUOTES, 'UTF-8' );

							if ( class_exists( 'WOOCS' ) ) {
								global $WOOCS;
								$rate = 1;
								$currency = $WOOCS->current_currency;
								$currencies = $WOOCS->get_currencies();
								$currency_symbol = $currencies[ $currency ]['symbol'];
								$rate = $currencies[ $currency ]['rate'];
								$decimal = $currencies[ $currency ]['decimals'];
								$cur_cur = $WOOCS->current_currency;
								$WOOCS->reset_currency();
								$WOOCS->set_currency( $cur_cur );

								$amt = $remaining_amount;
								$amt = floatval( $amt * $rate );
								$remaining_amount = round( $amt, $decimal );
								$message = str_replace( '[COUPONAMOUNT]', $currency_symbol . $remaining_amount, $message );

							} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {

								if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {

									$remaining_amount = wcpbc_the_zone()->get_exchange_rate_price( $remaining_amount );

									$message = str_replace( '[COUPONAMOUNT]', get_woocommerce_currency_symbol() . $remaining_amount, $message );
								} else {
									$message = str_replace( '[COUPONAMOUNT]', get_woocommerce_currency_symbol() . $remaining_amount, $message );
								}
							} else {
								$message = str_replace( '[COUPONAMOUNT]', get_woocommerce_currency_symbol() . $remaining_amount, $message );
							}
							$message = str_replace( '[SITENAME]', $bloginfo, $message );
							$message = str_replace( '[DISCLAIMER]', $giftcard_disclaimer, $message );
							$message = stripcslashes( $message );
							$message = html_entity_decode( $message, ENT_QUOTES, 'UTF-8' );
							$mwb_wgc_bcc_enable = get_option( 'mwb_wgm_addition_bcc_option_enable', false );
							if ( isset( $mwb_wgc_bcc_enable ) && $mwb_wgc_bcc_enable == 'on' ) {
								$headers[] = 'Bcc:' . $from;
								wc_mail( $to, $subject, $message, $headers );
							} else {
								wc_mail( $to, $subject, $message );
							}
						}
					}
				}
			}
		}
		/**
		 * This function is used to save the item metadata with order.
		 *
		 * @name mwb_wgm_woocommerce_add_order_item_meta
		 * @param $item_id
		 * @param $cart_item
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_woocommerce_add_order_item_meta( $item_id, $cart_item ) {

			$mwb_wgm_enable = mwb_wgm_giftcard_enable();
			if ( $mwb_wgm_enable ) {
				if ( isset( $cart_item ['product_meta'] ) ) {
					foreach ( $cart_item ['product_meta'] ['meta_data'] as $key => $val ) {
						$order_val = stripslashes( $val );

						if ( $val ) {
							if ( $key == 'mwb_wgm_to_email' ) {
								wc_add_order_item_meta( $item_id, 'To', $order_val );
							}
							if ( $key == 'mwb_wgm_to_name_optional' ) {
								wc_add_order_item_meta( $item_id, 'To Name', $order_val );
							}
							if ( $key == 'mwb_wgm_from_name' ) {
								wc_add_order_item_meta( $item_id, 'From', $order_val );
							}
							if ( $key == 'mwb_wgm_message' ) {
								wc_add_order_item_meta( $item_id, 'Message', $order_val );
							}
							if ( $key == 'mwb_wgm_browse_img' ) {
								wc_add_order_item_meta( $item_id, 'Image', $order_val );
							}
							if ( $key == 'mwb_wgm_send_date' ) {
								wc_add_order_item_meta( $item_id, 'Send Date', $order_val );
							}
							if ( $key == 'delivery_method' ) {
								wc_add_order_item_meta( $item_id, 'Delivery Method', $order_val );
							}
							if ( $key == 'mwb_wgm_price' ) {
								wc_add_order_item_meta( $item_id, 'Original Price', $order_val );
							}
							if ( $key == 'mwb_wgm_selected_temp' ) {
								wc_add_order_item_meta( $item_id, 'Selected Template', $order_val );
							}
						}
					}
				}
			}
		}

		/**
		 * This function is used to save the item metadata with order in woocommerce new version
		 *
		 * @name mwb_wgm_woocommerce_add_order_item_meta_new_ver
		 * @param $item_id
		 * @param $cart_item
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_woocommerce_add_order_item_meta_new_ver( $item, $cart_key, $values ) {

			$mwb_wgm_enable = mwb_wgm_giftcard_enable();
			if ( $mwb_wgm_enable ) {
				if ( isset( $values ['product_meta'] ) ) {
					foreach ( $values ['product_meta'] ['meta_data'] as $key => $val ) {
						$order_val = stripslashes( $val );
						if ( $val ) {
							if ( $key == 'mwb_wgm_to_email' ) {
								$item->add_meta_data( 'To', $order_val );
							}
							if ( $key == 'mwb_wgm_to_name_optional' ) {
								$item->add_meta_data( 'To Name', $order_val );
							}

							if ( $key == 'mwb_wgm_from_name' ) {
								$item->add_meta_data( 'From', $order_val );
							}

							if ( $key == 'mwb_wgm_message' ) {
								$item->add_meta_data( 'Message', $order_val );
							}
							if ( $key == 'mwb_wgm_send_date' ) {
								$item->add_meta_data( 'Send Date', $order_val );
							}
							if ( $key == 'mwb_wgm_browse_img' ) {
								$item->add_meta_data( 'Image', $order_val );
							}
							if ( $key == 'delivery_method' ) {
								$item->add_meta_data( 'Delivery Method', $order_val );
							}
							if ( $key == 'mwb_wgm_price' ) {
								$item->add_meta_data( 'Original Price', $order_val );
							}
							if ( $key == 'mwb_wgm_selected_temp' ) {
								$item->add_meta_data( 'Selected Template', $order_val );
							}
							if ( $key == 'mwb_cgc_image' ) {
								$item->add_meta_data( 'Choosen Image', $order_val );
							}
							if ( $key == 'mwb_cgc_custom_img' ) {
								$item->add_meta_data( 'File Name', $order_val );
							}
						}
					}
				}
			}
		}
		/**
		 * This function is used to convert the templates to pdf format
		 *
		 * @name mwb_wgm_attached_pdf
		 * @param $message
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_attached_pdf( $message, $site_name, $time, $order_id = '', $coupon_code = '' ) {

			$mwb_wgm_wkhtmltopdf = file_exists( MWB_WGM_DIRPATH . 'wkhtmltox/bin/wkhtmltopdf' );
			$mwb_wgm_new_way_of_pdf = get_option( 'mwb_wgm_next_step_for_pdf_value', 'no' );

				$mwb_wgm_pdf_template_size = get_option( 'mwb_wgm_pdf_template_size', 'A3' );
				$giftcard_pdf_content = $message;

				$url = 'https://makewebbetter.com/gift-card-api/api.php?f=get_giftcart_pdf&domain=' . $site_name . '&type=' . $mwb_wgm_pdf_template_size;
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL, $url );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_HEADER, true );
				curl_setopt( $ch, CURLOPT_POST, 1 );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $giftcard_pdf_content );
				$output = curl_exec( $ch );
				$uploadDirPath = MWB_UPLOAD_DIR . '/giftcard_pdf';
				if ( ! is_dir( $uploadDirPath ) ) {
					wp_mkdir_p( $uploadDirPath );
					chmod( $uploadDirPath, 0755 );
				}

				$email_pdf = fopen( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $time . $site_name . '.pdf', 'w' ) or die( 'Cannot open file:  giftcard' . $time . $site_name . '.pdf' );
				fwrite( $email_pdf, $output );
				fclose( $email_pdf );

				if ( ! empty( $order_id ) && ! empty( $coupon_code ) ) {
					$dwnld_pdf = fopen( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $order_id . $coupon_code . '.pdf', 'w' ) or die( 'Cannot open file:  giftcard' . $order_id . $coupon_code . '.pdf' );
					fwrite( $dwnld_pdf, $output );
					fclose( $dwnld_pdf );
				}
				curl_close( $ch );
		}
		/**
		 * This function is used to append the prices through ajax request
		 *
		 * @name mwb_wgm_append_prices
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_append_prices() {
			check_ajax_referer( 'mwb-wgm-verify-nonce', 'mwb_nonce' );
			$response['result'] = false;
			$new_price = '';
			$discount_applicable = false;
			if ( class_exists( 'WOOCS' ) ) {
				global $WOOCS;
				$currency = $WOOCS->current_currency;
				$currencies = $WOOCS->get_currencies();
				$rate = $currencies[ $currency ]['rate'];
				$decimal = $currencies[ $currency ]['decimals'];
				$mwb_wgm_range_price = $_POST['mwb_wgm_price'];
				if ( isset( $mwb_wgm_range_price ) and floatval( $mwb_wgm_range_price ) != 0.0 ) {
					$mwb_wgm_range_price = round( floatval( $mwb_wgm_range_price / $rate ), $decimal );
				}
			} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {
				if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
					$mwb_wgm_range_price = $_POST['mwb_wgm_price'];
					if ( isset( $mwb_wgm_range_price ) && ! empty( $mwb_wgm_range_price ) ) {
						$exchange_rate = wcpbc_the_zone()->get_exchange_rate();
						$mwb_wgm_range_price = floatval( $mwb_wgm_range_price / $exchange_rate );
					}
				}
			} else {
				$mwb_wgm_range_price = $_POST['mwb_wgm_price'];
			}

			$discount_min = get_option( 'mwb_wgm_discount_minimum', array() );
			$discount_max = get_option( 'mwb_wgm_discount_maximum', array() );
			$discount_value = get_option( 'mwb_wgm_discount_current_type', array() );
			$discount_type = get_option( 'mwb_wgm_discount_type', 'mwb_wgm_fixed' );
			if ( isset( $mwb_wgm_range_price ) && ! empty( $mwb_wgm_range_price ) ) {
				if ( isset( $discount_min ) && $discount_min != null && isset( $discount_max ) && $discount_max != null && isset( $discount_value ) && $discount_value != null ) {
					foreach ( $discount_min as $key => $value ) {

						$decimal_separator  = get_option( 'woocommerce_price_decimal_sep' );
						$mwb_wgm_range_price = floatval( str_replace( $decimal_separator , '.', $mwb_wgm_range_price ) );
						$discount_mini = floatval( str_replace( $decimal_separator , '.', $discount_min[ $key ] ) );
						$discount_maxi = floatval( str_replace( $decimal_separator , '.', $discount_max[ $key ] ) );
						$discount_val = floatval( str_replace( $decimal_separator , '.', $discount_value[ $key ] ) );

						if ( $discount_mini <= $mwb_wgm_range_price && $mwb_wgm_range_price <= $discount_maxi ) {
							if ( $discount_type == 'mwb_wgm_percentage' ) {
								$new_price = $mwb_wgm_range_price - ( $mwb_wgm_range_price * $discount_val ) / 100;
							} else {
								$new_price = $mwb_wgm_range_price - $discount_val;
							}
							$discount_applicable = true;
						}
					}
				}
				if ( $discount_applicable ) {
					if ( class_exists( 'WOOCS' ) ) {
						global $WOOCS;
						$currency = $WOOCS->current_currency;
						$currencies = $WOOCS->get_currencies();
						$rate = $currencies[ $currency ]['rate'];
						$decimal = $currencies[ $currency ]['decimals'];

						$new_price = round( floatval( $new_price * $rate ), $decimal );
						$mwb_wgm_range_price = round( floatval( $mwb_wgm_range_price * $rate ), $decimal );

						$response['result'] = true;
						$response['new_price'] = wc_price( $new_price );
						$response['mwb_wgm_price'] = wc_price( $mwb_wgm_range_price );
						echo json_encode( $response );
					} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {
						if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
							$new_price = wcpbc_the_zone()->get_exchange_rate_price( $new_price );
							$mwb_wgm_range_price = wcpbc_the_zone()->get_exchange_rate_price( $mwb_wgm_range_price );
						}
						$response['result'] = true;
						$response['new_price'] = wc_price( $new_price );
						$response['mwb_wgm_price'] = wc_price( $mwb_wgm_range_price );
						echo json_encode( $response );
					} else {
						$response['result'] = true;
						$response['new_price'] = wc_price( $new_price );
						$response['mwb_wgm_price'] = wc_price( $mwb_wgm_range_price );
						echo json_encode( $response );
					}
				} else {
					$response['result'] = false;
					echo json_encode( $response );
				}
				wp_die();
			}
		}
		/**
		 * This function is used to send a Thankyou Gift Coupon to customers when the option is selected "Order Creation"
		 *
		 * @name mwb_wgm_woocommerce_checkout_update_order_meta
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_woocommerce_checkout_update_order_meta( $order_id, $data ) {
			$subject = __( 'Hurry! Coupon Code is received', 'woocommerce-ultimate-gift-card' );
			$message_thnku = get_option( 'mwb_wgm_thankyou_message', 'You have recieved a coupon [COUPONCODE], having amount of [COUPONAMOUNT] with the expiration date of [COUPONEXPIRY]' );
			$mail_header = __( 'Thankyou Giftcard Coupon', 'woocommerce-ultimate-gift-card' );
			$mail_footer = '';
			$message_thnku = '<html>
			<body>
			<style>
			body {
				box-shadow: 2px 2px 10px #ccc;
				color: #767676;
				font-family: Arial,sans-serif;
				margin: 80px auto;
				max-width: 700px;
				padding-bottom: 30px;
				width: 100%;
			}

			h2 {
				font-size: 30px;
				margin-top: 0;
				color: #fff;
				padding: 40px;
				background-color: #557da1;
			}

			h4 {
				color: #557da1;
				font-size: 20px;
				margin-bottom: 10px;
			}

			.content {
				padding: 0 40px;
			}

			.Customer-detail ul li p {
				margin: 0;
			}

			.details .Shipping-detail {
				width: 40%;
				float: right;
			}

			.details .Billing-detail {
				width: 60%;
				float: left;
			}

			.details .Shipping-detail ul li,.details .Billing-detail ul li {
				list-style-type: none;
				margin: 0;
			}

			.details .Billing-detail ul,.details .Shipping-detail ul {
				margin: 0;
				padding: 0;
			}

			.clear {
				clear: both;
			}

			table,td,th {
				border: 2px solid #ccc;
				padding: 15px;
				text-align: left;
			}

			table {
				border-collapse: collapse;
				width: 100%;
			}

			.info {
				display: inline-block;
			}

			.bold {
				font-weight: bold;
			}

			.footer {
				margin-top: 30px;
				text-align: center;
				color: #99B1D8;
				font-size: 12px;
			}
			dl.variation dd {
				font-size: 12px;
				margin: 0;
			}
			</style>

			<div style="padding: 36px 48px; background-color:#557DA1;color: #fff; font-size: 30px; font-weight: 300; font-family:helvetica;" class="header">
			' . $mail_header . '
			</div>		

			<div class="content">
			<div class="Order">
			<h4>Order #' . $order_id . '</h4>
			<table>
			<tbody>' . $message_thnku . '</tbody>
			</table>
			</div>
			</div>
			<div style="text-align: center; padding: 10px;" class="footer">
			' . $mail_footer . '
			</div>
			</body>
			</html>';
			$thankyouorder_enable = get_option( 'mwb_wgm_thankyouorder_enable', false );
			$thankyouorder_type = get_option( 'mwb_wgm_thankyouorder_type', 'mwb_wgm_fixed_thankyou' );
			$thankyouorder_time = get_option( 'mwb_wgm_thankyouorder_time', 'mwb_wgm_complete_status' );
			// $thankyouorder_min = get_option("mwb_wgm_thankyouorder_minimum", array());
			// $thankyouorder_max = get_option("mwb_wgm_thankyouorder_maximum", array());
			// $thankyouorder_value = get_option("mwb_wgm_thankyouorder_current_type", array());

			if ( class_exists( 'WOOCS' ) ) {
				global $WOOCS;
				$rate = 1;
				$currency = $WOOCS->current_currency;
				$currencies = $WOOCS->get_currencies();
				$rate = $currencies[ $currency ]['rate'];
				$decimal = $currencies[ $currency ]['decimals'];
				$cur_cur = $WOOCS->current_currency;
				$WOOCS->reset_currency();
				$WOOCS->set_currency( $cur_cur );

				$thankyouorder_min = get_option( 'mwb_wgm_thankyouorder_minimum', array() );
				$thankyouorder_max = get_option( 'mwb_wgm_thankyouorder_maximum', array() );
				$thankyouorder_value = get_option( 'mwb_wgm_thankyouorder_current_type', array() );

				if ( is_array( $thankyouorder_value ) && ! empty( $thankyouorder_value ) && count( $thankyouorder_value ) ) {
					foreach ( $thankyouorder_value as $key => $value ) {
						if ( isset( $thankyouorder_min[ $key ] ) && ! empty( $thankyouorder_min[ $key ] ) && isset( $thankyouorder_max[ $key ] ) && ! empty( $thankyouorder_max[ $key ] ) ) {
							$thankyouorder_min[ $key ] = round( floatval( $thankyouorder_min[ $key ] * $rate ), $decimal );
							$thankyouorder_max[ $key ] = round( floatval( $thankyouorder_max[ $key ] * $rate ), $decimal );

						} else if ( isset( $thankyouorder_min[ $key ] ) && ! empty( $thankyouorder_min[ $key ] ) && empty( $thankyouorder_max[ $key ] ) ) {
							$thankyouorder_min[ $key ] = round( floatval( $thankyouorder_min[ $key ] * $rate ), $decimal );
						}
					}
				}
			} elseif ( class_exists( 'WCPBC_Pricing_Zone' ) ) {

				if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {

					$thankyouorder_min = get_option( 'mwb_wgm_thankyouorder_minimum', array() );
					$thankyouorder_max = get_option( 'mwb_wgm_thankyouorder_maximum', array() );
					$thankyouorder_value = get_option( 'mwb_wgm_thankyouorder_current_type', array() );
					if ( is_array( $thankyouorder_value ) && ! empty( $thankyouorder_value ) && count( $thankyouorder_value ) ) {
						foreach ( $thankyouorder_value as $key => $value ) {
							if ( isset( $thankyouorder_min[ $key ] ) && ! empty( $thankyouorder_min[ $key ] ) && isset( $thankyouorder_max[ $key ] ) && ! empty( $thankyouorder_max[ $key ] ) ) {
								$thankyouorder_min[ $key ] = wcpbc_the_zone()->get_exchange_rate_price( $thankyouorder_min[ $key ] );
								$thankyouorder_max[ $key ] = wcpbc_the_zone()->get_exchange_rate_price( $thankyouorder_max[ $key ] );

							} else if ( isset( $thankyouorder_min[ $key ] ) && ! empty( $thankyouorder_min[ $key ] ) && empty( $thankyouorder_max[ $key ] ) ) {

								$thankyouorder_min[ $key ] = wcpbc_the_zone()->get_exchange_rate_price( $thankyouorder_min[ $key ] );
							}
						}
					}
				}
			} else {
				$thankyouorder_min = get_option( 'mwb_wgm_thankyouorder_minimum', array() );
				$thankyouorder_max = get_option( 'mwb_wgm_thankyouorder_maximum', array() );
				$thankyouorder_value = get_option( 'mwb_wgm_thankyouorder_current_type', array() );
			}

			$mwb_wgm_thankyouorder_number = (int) get_option( 'mwb_wgm_thankyouorder_number', 1 );
			$order = wc_get_order( $order_id );
			$order_total = $order->get_total();
			if ( isset( $thankyouorder_enable ) && ! empty( $thankyouorder_enable ) && $thankyouorder_enable == 'on' ) {
				$coupon_alreadycreated = get_post_meta( $order_id, 'mwb_wgm_thnkyou_coupon_created', true );
				if ( $coupon_alreadycreated == 'send' ) {
					return;
				}
				$user_id = $order->get_user_id();
				$user = get_user_by( 'ID', $user_id );
				$user_email = $user->user_email;
				if ( $thankyouorder_time == 'mwb_wgm_order_creation' ) {
					$thankyou_user_order = (int) get_user_meta( $user_id, 'thankyou_order_number', true );
					if ( $thankyou_user_order >= $mwb_wgm_thankyouorder_number ) {

						if ( is_array( $thankyouorder_value ) && ! empty( $thankyouorder_value ) ) {
							foreach ( $thankyouorder_value as $key => $value ) {
								$coupon_alreadycreated = get_post_meta( $order_id, 'mwb_wgm_thnkyou_coupon_created', true );
								if ( $coupon_alreadycreated == 'send' ) {
									return;
								}

								if ( isset( $thankyouorder_min[ $key ] ) && ! empty( $thankyouorder_min[ $key ] ) && isset( $thankyouorder_max[ $key ] ) && ! empty( $thankyouorder_max[ $key ] ) ) {
									if ( $thankyouorder_min[ $key ] <= $order_total && $order_total <= $thankyouorder_max[ $key ] ) {
										$thnku_coupon_length = get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 );
										$thanku_couponnumber = mwb_wgm_coupon_generator( $thnku_coupon_length );
										$thnku_couponamount = $thankyouorder_value[ $key ];
										if ( $this->mwb_wgm_create_thnku_coupon( $thanku_couponnumber, $thnku_couponamount, $order_id, $thankyouorder_type, $user_id ) ) {
											$coupon_creation = true;
											$the_coupon = new WC_Coupon( $thanku_couponnumber );
											$thnku_couponamount = $the_coupon->get_amount();
											$expiry_date_timestamp = $the_coupon->get_date_expires();
											$date_format = get_option( 'date_format' );
											if ( ! isset( $date_format ) && empty( $date_format ) ) {
												$date_format = 'Y-m-d';
											}
											if ( ! empty( $expiry_date_timestamp ) && isset( $expiry_date_timestamp ) ) {
												$expiry_date_timestamp = strtotime( $expiry_date_timestamp );
											}
											if ( empty( $expiry_date_timestamp ) ) {
												$expirydate_format = __( 'No Expiartion', 'woocommerce-ultimate-gift-card' );
											} else {
												$expirydate_format = date_i18n( $date_format, $expiry_date_timestamp );
											}
											$bloginfo = get_bloginfo();
											$headers = array( 'Content-Type: text/html; charset=UTF-8' );
											$message_thnku = str_replace( '[COUPONCODE]', $thanku_couponnumber, $message_thnku );
											if ( $thankyouorder_type == 'mwb_wgm_fixed_thankyou' ) {
												$message_thnku = str_replace( '[COUPONAMOUNT]', wc_price( $thnku_couponamount ), $message_thnku );
											} else if ( $thankyouorder_type == 'mwb_wgm_percentage_thankyou' ) {
												$message_thnku = str_replace( '[COUPONAMOUNT]', $thnku_couponamount . '%', $message_thnku );
											}
											$message_thnku = str_replace( '[COUPONEXPIRY]', $expirydate_format, $message_thnku );
											wc_mail( $user_email, $subject, $message_thnku, $headers );
											update_post_meta( $order_id, 'mwb_wgm_thnkyou_coupon_created', 'send' );
										}
									}
								} else if ( isset( $thankyouorder_min[ $key ] ) && ! empty( $thankyouorder_min[ $key ] ) && empty( $thankyouorder_max[ $key ] ) ) {

									if ( $thankyouorder_min[ $key ] <= $order_total ) {
										$thnku_coupon_length = get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 );
										$thanku_couponnumber = mwb_wgm_coupon_generator( $thnku_coupon_length );
										$thnku_couponamount = $thankyouorder_value[ $key ];
										if ( $this->mwb_wgm_create_thnku_coupon( $thanku_couponnumber, $thnku_couponamount, $order_id, $thankyouorder_type, $user_id ) ) {
											$coupon_creation = true;
											$the_coupon = new WC_Coupon( $thanku_couponnumber );
											$thnku_couponamount = $the_coupon->get_amount();
											$expiry_date_timestamp = $the_coupon->get_date_expires();
											$date_format = get_option( 'date_format' );
											if ( ! isset( $date_format ) && empty( $date_format ) ) {
												$date_format = 'Y-m-d';
											}
											if ( ! empty( $expiry_date_timestamp ) && isset( $expiry_date_timestamp ) ) {
												$expiry_date_timestamp = strtotime( $expiry_date_timestamp );
											}
											if ( empty( $expiry_date_timestamp ) ) {
												$expirydate_format = __( 'No Expiration', 'woocommerce-ultimate-gift-card' );
											} else {
												$expirydate_format = date_i18n( $date_format, $expiry_date_timestamp );
											}
											$bloginfo = get_bloginfo();
											$headers = array( 'Content-Type: text/html; charset=UTF-8' );
											$message_thnku = str_replace( '[COUPONCODE]', $thanku_couponnumber, $message_thnku );
											if ( $thankyouorder_type == 'mwb_wgm_fixed_thankyou' ) {
												$message_thnku = str_replace( '[COUPONAMOUNT]', wc_price( $thnku_couponamount ), $message_thnku );
											} else if ( $thankyouorder_type == 'mwb_wgm_percentage_thankyou' ) {
												$message_thnku = str_replace( '[COUPONAMOUNT]', $thnku_couponamount . '%', $message_thnku );
											}
											$message_thnku = str_replace( '[COUPONEXPIRY]', $expirydate_format, $message_thnku );
											wc_mail( $user_email, $subject, $message_thnku, $headers );
											update_post_meta( $order_id, 'mwb_wgm_thnkyou_coupon_created', 'send' );
										}
									}
								} else if ( isset( $thankyouorder_value[ $key ] ) && ! empty( $thankyouorder_value[ $key ] ) && empty( $thankyouorder_min[ $key ] ) && empty( $thankyouorder_max[ $key ] ) ) {
									$thnku_coupon_length = get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 );
									$thanku_couponnumber = mwb_wgm_coupon_generator( $thnku_coupon_length );
									$thnku_couponamount = $thankyouorder_value[ $key ];
									if ( $this->mwb_wgm_create_thnku_coupon( $thanku_couponnumber, $thnku_couponamount, $order_id, $thankyouorder_type, $user_id ) ) {
										$coupon_creation = true;
										$the_coupon = new WC_Coupon( $thanku_couponnumber );
										$thnku_couponamount = $the_coupon->get_amount();
										$expiry_date_timestamp = $the_coupon->get_date_expires();
										$date_format = get_option( 'date_format' );
										if ( ! isset( $date_format ) && empty( $date_format ) ) {
											$date_format = 'Y-m-d';
										}
										if ( ! empty( $expiry_date_timestamp ) && isset( $expiry_date_timestamp ) ) {
											$expiry_date_timestamp = strtotime( $expiry_date_timestamp );
										}
										if ( empty( $expiry_date_timestamp ) ) {
											$expirydate_format = __( 'No Expiration', 'woocommerce-ultimate-gift-card' );
										} else {
											$expirydate_format = date_i18n( $date_format, $expiry_date_timestamp );
										}
										$bloginfo = get_bloginfo();
										$headers = array( 'Content-Type: text/html; charset=UTF-8' );
										$message_thnku = str_replace( '[COUPONCODE]', $thanku_couponnumber, $message_thnku );
										if ( $thankyouorder_type == 'mwb_wgm_fixed_thankyou' ) {
											$message_thnku = str_replace( '[COUPONAMOUNT]', wc_price( $thnku_couponamount ), $message_thnku );
										} else if ( $thankyouorder_type == 'mwb_wgm_percentage_thankyou' ) {
											$message_thnku = str_replace( '[COUPONAMOUNT]', $thnku_couponamount . '%', $message_thnku );
										}
										$message_thnku = str_replace( '[COUPONEXPIRY]', $expirydate_format, $message_thnku );
										wc_mail( $user_email, $subject, $message_thnku, $headers );
										update_post_meta( $order_id, 'mwb_wgm_thnkyou_coupon_created', 'send' );
									}
								}
							}
						}
					}	
				}
			}
		}
		/**
		 * This function is used to generate a Thankyou Gift Coupon
		 *
		 * @name mwb_wgm_create_thnku_coupon
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_create_thnku_coupon( $thnku_couponnumber, $thnku_couponamont, $order_id, $thankyouorder_type, $user_id ) {
			$thankyouorder_enable = get_option( 'mwb_wgm_thankyouorder_enable', false );
			if ( isset( $thankyouorder_enable ) && ! empty( $thankyouorder_enable ) && $thankyouorder_enable == 'on' ) {
				$alreadycreated = get_post_meta( $order_id, 'mwb_wgm_thnkyou_coupon_created', true );
				if ( $alreadycreated != 'send' ) {
					$coupon_code = $thnku_couponnumber; // Code
					$amount = $thnku_couponamont; // Amount
					if ( $thankyouorder_type == 'mwb_wgm_fixed_thankyou' ) {
						$discount_type = 'fixed_cart';
					} else if ( $thankyouorder_type == 'mwb_wgm_percentage_thankyou' ) {
						$discount_type = 'percent';
					}
					$coupon_description = "ThankYou ORDER #$order_id";

					$coupon = array(
						'post_title' => $coupon_code,
						'post_content' => $coupon_description,
						'post_excerpt' => $coupon_description,
						'post_status' => 'publish',
						'post_author' => get_current_user_id(),
						'post_type'     => 'shop_coupon',
					);

					$new_coupon_id = wp_insert_post( $coupon );
					//Added in WooCommerce 3.9.0
					if ( $new_coupon_id ) {
						$coupon_obj = new WC_Coupon( $new_coupon_id );
						$coupon_obj->save();
					}
					$individual_use = get_option( 'mwb_wgm_general_setting_giftcard_individual_use', 'no' );
					$usage_limit = get_option( 'mwb_wgm_general_setting_giftcard_use', 1 );
					$expiry_date = get_option( 'mwb_wgm_thnku_giftcard_expiry', 1 );
					$free_shipping = get_option( 'mwb_wgm_general_setting_giftcard_freeshipping', 1 );
					$apply_before_tax = get_option( 'mwb_wgm_general_setting_giftcard_applybeforetx', 'yes' );
					$minimum_amount = get_option( 'mwb_wgm_general_setting_giftcard_minspend', '' );
					$maximum_amount = get_option( 'mwb_wgm_general_setting_giftcard_maxspend', '' );
					$exclude_sale_items = get_option( 'mwb_wgm_general_setting_giftcard_ex_sale', 'no' );
					$exclude_products = get_option( 'mwb_wgm_product_setting_exclude_product_format', '' );
					$exclude_category = get_option( 'mwb_wgm_product_setting_exclude_category', '' );

					$todaydate = date_i18n( 'Y-m-d' );
					if ( $expiry_date > 0 || $expiry_date === 0 ) {
						$expirydate = date_i18n( 'Y-m-d', strtotime( "$todaydate +$expiry_date day" ) );
					} else {
						$expirydate = '';
					}

					// Add meta

					update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
					update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
					update_post_meta( $new_coupon_id, 'individual_use', $individual_use );
					update_post_meta( $new_coupon_id, 'usage_limit', $usage_limit );

					$woo_ver = WC()->version;

					if ( $woo_ver < '3.6.0' ) {
						update_post_meta( $new_coupon_id, 'expiry_date', $expirydate );
					} else {
						$expirydate = strtotime( $expirydate );
						update_post_meta( $new_coupon_id, 'date_expires', $expirydate );
					}

					// update_post_meta( $new_coupon_id, 'expiry_date', $expirydate );
					update_post_meta( $new_coupon_id, 'apply_before_tax', $apply_before_tax );
					update_post_meta( $new_coupon_id, 'free_shipping', $free_shipping );
					update_post_meta( $new_coupon_id, 'minimum_amount', $minimum_amount );
					update_post_meta( $new_coupon_id, 'maximum_amount', $maximum_amount );
					update_post_meta( $new_coupon_id, 'exclude_sale_items', $exclude_sale_items );
					update_post_meta( $new_coupon_id, 'mwb_wgm_thankyou_coupon', $order_id );
					update_post_meta( $new_coupon_id, 'mwb_wgm_thankyou_coupon_user', $user_id );
					return true;
				} else {
					return false;
				}
			}
		}
		/**
		 * This function is used to delete old images via a scheduler
		 *
		 * @name mwb_wgm_do_this_delete_img
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_do_this_delete_img() {
			$time = time();
			$files = glob( MWB_UPLOAD_DIR . '/qrcode_barcode/*.*' );
			foreach ( $files as $filename ) {
				$fil1 = explode( 'mwb__', $filename );
				$time = time();
				$timestamp = array();
				$timestamp[] = end( $fil1 );
				foreach ( $timestamp as $key => $val ) {
					if ( end( $fil1 ) < $time . '.png' ) {
						unlink( MWB_UPLOAD_DIR . '/qrcode_barcode/mwb__' . $val );
					}
				}
			}
		}
		/**
		 * This function is used to make the meta keys translatable
		 *
		 * @name mwb_wgm_woocommerce_order_item_display_meta_key
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_woocommerce_order_item_display_meta_key( $display_key ) {
			if ( $display_key == 'To Name' ) {
				$display_key = __( 'To Name', 'woocommerce-ultimate-gift-card' );
			}
			if ( $display_key == 'To' ) {
				$display_key = __( 'To', 'woocommerce-ultimate-gift-card' );
			}
			if ( $display_key == 'From' ) {
				$display_key = __( 'From', 'woocommerce-ultimate-gift-card' );
			}
			if ( $display_key == 'Message' ) {
				$display_key = __( 'Message', 'woocommerce-ultimate-gift-card' );
			}
			if ( $display_key == 'Delivery Method' ) {
				$display_key = __( 'Delivery Method', 'woocommerce-ultimate-gift-card' );
			}
			if ( $display_key == 'Send Date' ) {
				$display_key = __( 'Send Date', 'woocommerce-ultimate-gift-card' );
			}
			if ( $display_key == 'Original Price' ) {
				$display_key = __( 'Original Price', 'woocommerce-ultimate-gift-card' );
			}
			if ( $display_key == 'Selected Template' ) {
				$display_key = __( 'Selected Template', 'woocommerce-ultimate-gift-card' );
			}
			if ( $display_key == 'Image' ) {
				$display_key = __( 'Image', 'woocommerce-ultimate-gift-card' );
			}

			return $display_key;
		}
		/**
		 * This function is used to make the meta values translatable
		 *
		 * @name mwb_wgm_woocommerce_order_item_display_meta_value
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_woocommerce_order_item_display_meta_value( $display_value ) {
			if ( $display_value == 'Mail to recipient' ) {
				$display_value = __( 'Mail to recipient', 'woocommerce-ultimate-gift-card' );
			}
			if ( $display_value == 'Downloadable' ) {
				$display_value = __( 'Downloadable', 'woocommerce-ultimate-gift-card' );
			}
			if ( $display_value == 'Shipping' ) {
				$display_value = __( 'Shipping', 'woocommerce-ultimate-gift-card' );
			}
			return $display_value;
		}
		/**
		 * This function is common function which has been used to handling the mail functionality for Gift Card Emails
		 *
		 * @name mwb_wgm_common_functionality
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_common_functionality( $mwb_wgm_common_arr, $order ) {
			if ( ! empty( $mwb_wgm_common_arr ) ) {
				$to = $mwb_wgm_common_arr['to'];
				$from = $mwb_wgm_common_arr['from'];
				$item_id = $mwb_wgm_common_arr['item_id'];
				$product_id = $mwb_wgm_common_arr['product_id'];
				$mwb_wgm_change_admin_email_for_shipping = get_option( 'mwb_wgm_change_admin_email_for_shipping', '' );
				$mwb_wgm_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );
				$templateid = $mwb_wgm_pricing['template'];
				$args['from'] = $from;
				if ( isset( $mwb_wgm_common_arr['to_name'] ) && ! empty( $mwb_wgm_common_arr['to_name'] ) ) {
					$args['to'] = $mwb_wgm_common_arr['to_name'];
				} else {
					$args['to'] = $to;
				}
				$args['order_id'] = isset( $mwb_wgm_common_arr['order_id'] ) ? $mwb_wgm_common_arr['order_id'] : '';
				$args['message'] = stripcslashes( $mwb_wgm_common_arr['gift_msg'] );
				$args['coupon'] = apply_filters( 'mwb_wgm_qrcode_coupon', $mwb_wgm_common_arr['gift_couponnumber'] );

				$args['expirydate'] = $mwb_wgm_common_arr['expirydate_format'];
				// $args['amount'] =  wc_price($mwb_wgm_common_arr['couponamont']);

				// added for currency switcher
				if ( class_exists( 'WOOCS' ) ) {
					global $WOOCS;
					$mwb_rate = 1;
					$mwb_currency = $WOOCS->current_currency;
					$mwb_currencies = $WOOCS->get_currencies();
					$mwb_rate = $mwb_currencies[ $mwb_currency ]['rate'];
					$WOOCS->reset_currency();
					$WOOCS->set_currency( $mwb_currency );

					$amt = $mwb_wgm_common_arr['couponamont'];
					$amt = floatval( $amt * $mwb_rate );
					$args['amount'] = wc_price( $amt );
				} elseif ( class_exists( 'WCPBC_Pricing_Zones' ) ) {

					$billing_country = $order->get_billing_country();
					// $shipping_country = $order->get_shipping_country();

					$wcpbc_the_zone = WCPBC_Pricing_Zones::get_zone_by_country( $billing_country );
					if ( isset( $wcpbc_the_zone ) && $wcpbc_the_zone != null ) {
						$cur = $wcpbc_the_zone->get_currency();
						$amt = $wcpbc_the_zone->get_exchange_rate_price( $mwb_wgm_common_arr['couponamont'] );
						$args['amount'] = get_woocommerce_currency_symbol( $cur ) . $amt;
					} else {
						$args['amount'] = wc_price( $mwb_wgm_common_arr['couponamont'] );
					}
				} else {
					$mwb_price = $mwb_wgm_common_arr['couponamont'] ;
					$decimal_separator  = get_option( 'woocommerce_price_decimal_sep' ); 
					$args['amount'] = wc_price ( floatval( str_replace( $decimal_separator , '.', $mwb_price ) ) );	
				}
				$args['templateid'] = isset( $mwb_wgm_common_arr['selected_template'] ) && ! empty( $mwb_wgm_common_arr['selected_template'] ) ? $mwb_wgm_common_arr['selected_template'] : $templateid;
				$args['product_id'] = $product_id;
				$args['choosen_image'] = ! empty( $mwb_wgm_common_arr['choosen_image'] ) ? $mwb_wgm_common_arr['choosen_image'] : '';
				$args['cgc_file_name'] = ! empty( $mwb_wgm_common_arr['cgc_file_name'] ) ? $mwb_wgm_common_arr['cgc_file_name'] : '';
				$browse_enable = get_option( 'mwb_wgm_other_setting_browse', false );
				if ( $browse_enable == 'on' ) {
					if ( $mwb_wgm_common_arr['gift_img_name'] != '' ) {
						$args['browse_image'] = $mwb_wgm_common_arr['gift_img_name'];
					}
				}
				$message = apply_filters( 'mwb_wgm_customized_email_cgc', $this->mwb_wgm_giftttemplate( $args ), $args );

				$order_id = $mwb_wgm_common_arr['order_id'];
				$mwb_wgm_pre_gift_num = get_post_meta( $order_id, "$order_id#$item_id", true );

				if ( is_array( $mwb_wgm_pre_gift_num ) && ! empty( $mwb_wgm_pre_gift_num ) ) {
					$mwb_wgm_pre_gift_num[] = $mwb_wgm_common_arr['gift_couponnumber'];
					update_post_meta( $order_id, "$order_id#$item_id", $mwb_wgm_pre_gift_num );
				} else {
					$mwb_wgm_code_arr = array();
					$mwb_wgm_code_arr[] = $mwb_wgm_common_arr['gift_couponnumber'];
					update_post_meta( $order_id, "$order_id#$item_id", $mwb_wgm_code_arr );
				}
				$mwb_wgm_pdf_enable = get_option( 'mwb_wgm_addition_pdf_enable', false );
				if ( isset( $mwb_wgm_pdf_enable ) && $mwb_wgm_pdf_enable == 'on' ) {

					$site_name = $_SERVER['SERVER_NAME'];
					$time = time();
					$this->mwb_wgm_attached_pdf( $message, $site_name, $time, $order_id, $mwb_wgm_common_arr['gift_couponnumber'] );

					$attachments = array( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $time . $site_name . '.pdf' );
				} else {
					$attachments = array();
				}
				$get_mail_status = true;
				$get_mail_status = apply_filters( 'mwb_send_mail_status', $get_mail_status );

				if ( $get_mail_status ) {
					if ( isset( $mwb_wgm_common_arr['delivery_method'] ) && $mwb_wgm_common_arr['delivery_method'] == 'Mail to recipient' ) {
						$subject = get_option( 'mwb_wgm_other_setting_giftcard_subject', false );
					}
					if ( isset( $mwb_wgm_common_arr['delivery_method'] ) && $mwb_wgm_common_arr['delivery_method'] == 'Downloadable' ) {
						$subject = get_option( 'mwb_wgm_other_setting_giftcard_subject_downloadable', false );
					}
					if ( isset( $mwb_wgm_common_arr['delivery_method'] ) && $mwb_wgm_common_arr['delivery_method'] == 'Shipping' ) {
						$subject = get_option( 'mwb_wgm_other_setting_giftcard_subject_shipping', false );
					}
					$bloginfo = get_bloginfo();
					if ( empty( $subject ) || ! isset( $subject ) ) {

						$subject = "$bloginfo:";
						$subject .= __( ' Hurry!!! Giftcard is Received', 'woocommerce-ultimate-gift-card' );
					}
					$subject = str_replace( '[SITENAME]', $bloginfo, $subject );
					$subject = str_replace( '[BUYEREMAILADDRESS]', $from, $subject );
					$subject = str_replace( '[ORDERID]', $order_id, $subject );
					$subject = stripcslashes( $subject );
					$subject = html_entity_decode( $subject, ENT_QUOTES, 'UTF-8' );
					$mwb_wgc_bcc_enable = get_option( 'mwb_wgm_addition_bcc_option_enable', false );
					if ( isset( $mwb_wgm_common_arr['delivery_method'] ) ) {
						if ( $mwb_wgm_common_arr['delivery_method'] == 'Mail to recipient' ) {
							$woo_ver = WC()->version;
							if ( $woo_ver < '3.0.0' ) {
								$from = $order->billing_email;
								if ( empty( $to ) ) {
									$to = $order->get_billing_email();
								}
							} else {
								$from = $order->get_billing_email();
								if ( empty( $to ) ) {
									$to = $order->get_billing_email();
								}
							}
						}
						if ( $mwb_wgm_common_arr['delivery_method'] == 'Downloadable' ) {
							$woo_ver = WC()->version;
							if ( $woo_ver < '3.0.0' ) {
								$to = $order->billing_email;
								$from = $order->billing_email;
							} else {
								$to = $order->get_billing_email();
								$from = $order->get_billing_email();
							}
						}
						if ( $mwb_wgm_common_arr['delivery_method'] == 'Shipping' ) {
							$admin_email = get_option( 'admin_email' );
							$alternate_email = ! empty( $mwb_wgm_change_admin_email_for_shipping ) ? $mwb_wgm_change_admin_email_for_shipping : $admin_email;
							$to = $alternate_email;
						}
					}
					if ( isset( $mwb_wgc_bcc_enable ) && $mwb_wgc_bcc_enable == 'on' ) {
						$headers[] = 'Bcc:' . $from;
						wc_mail( $to, $subject, $message, $headers, $attachments );
						do_action( 'mwb_wgm_mail_send_to_someone', $subject, $message, $attachments );
						if ( isset( $time ) && ! empty( $time ) && isset( $site_name ) && ! empty( $site_name ) ) {
							unlink( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $time . $site_name . '.pdf' );
						}
					} else {
						$headers = array( 'Content-Type: text/html; charset=UTF-8' );
						wc_mail( $to, $subject, $message, $headers, $attachments );
						do_action( 'mwb_wgm_mail_send_to_someone', $subject, $message, $attachments );
						if ( isset( $time ) && ! empty( $time ) && isset( $site_name ) && ! empty( $site_name ) ) {
							unlink( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $time . $site_name . '.pdf' );
						}
					}
					$subject = get_option( 'mwb_wgm_other_setting_receive_subject', false );
					$message = get_option( 'mwb_wgm_other_setting_receive_message', false );
					if ( empty( $subject ) || ! isset( $subject ) ) {

						$subject = "$bloginfo:";
						$subject .= __( ' Gift Card is Sent Successfully', 'woocommerce-ultimate-gift-card' );
					}

					if ( empty( $message ) || ! isset( $message ) ) {

						$message = "$bloginfo:";
						$message .= __( ' Gift Card is Sent Successfully to the Email Id: [TO]', 'woocommerce-ultimate-gift-card' );
					}

					$message = stripcslashes( $message );
					$message = str_replace( '[TO]', $to, $message );
					$subject = stripcslashes( $subject );
					$mwb_wgm_disable_buyer_notification = get_option( 'mwb_wgm_disable_buyer_notification', 'off' );
					if ( $mwb_wgm_disable_buyer_notification == 'off' && $mwb_wgm_common_arr['delivery_method'] == 'Mail to recipient' ) {
						wc_mail( $from, $subject, $message );
					}
				}
				return true;
			} else {
				return false;
			}
		}

		/**
		 * This function is used to check the expiration date for Gift Coupon Codes
		 *
		 * @name mwb_wgm_check_expiry_date
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_check_expiry_date( $expiry_date ) {

			$todaydate = date_i18n( 'Y-m-d' );
			if ( isset( $expiry_date ) && ! empty( $expiry_date ) ) {
				if ( $expiry_date > 0 || $expiry_date === 0 ) {

					$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format_1', false );
					if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
						$expirydate = date_i18n( $selected_date, strtotime( "$todaydate +$expiry_date day" ) );
					} else {
						$expirydate = date_i18n( 'Y-m-d', strtotime( "$todaydate +$expiry_date day" ) );
					}
				}
			} else {
				$expirydate = __( 'No Expiration', 'woocommerce-ultimate-gift-card' );
			}

			return $expirydate;
		}

		/**
		 * This function is used to return the remaining coupon amount according to Tax setting you have in your system
		 *
		 * @name mwb_calculate_coupon_discount
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_calculate_coupon_discount( $mwb_ugc_discount, $mwb_ugc_discount_tax ) {
			$price_in_ex_option = get_option( 'woocommerce_prices_include_tax' );
			$tax_display_shop = get_option( 'woocommerce_tax_display_shop', 'excl' );
			$tax_display_cart = get_option( 'woocommerce_tax_display_cart', 'excl' );

			if ( isset( $tax_display_shop ) && isset( $tax_display_cart ) ) {
				if ( $tax_display_cart == 'excl' && $tax_display_shop == 'excl' ) {

					if ( $price_in_ex_option == 'yes' || $price_in_ex_option == 'no' ) {

						return $total_discount = $mwb_ugc_discount;
					}
				} elseif ( $tax_display_cart == 'incl' && $tax_display_shop == 'incl' ) {

					if ( $price_in_ex_option == 'yes' || $price_in_ex_option == 'no' ) {

						return $total_discount = $mwb_ugc_discount + $mwb_ugc_discount_tax;
					}
				} else {
					return $total_discount = $mwb_ugc_discount;
				}
			}
		}

		/**
		 * This function is used to check the remaining balance of Gift Card
		 *
		 * @name mwb_wgm_check_giftcard
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_check_giftcard() {
			check_ajax_referer( 'mwb-wgm-verify-nonce', 'mwb_nonce' );
			$response['result'] = false;
			$response['message'] = __( 'Balance cannot be checked yet, Please Try again later!', 'woocommerce-ultimate-gift-card' );
			$entered_email = sanitize_post( $_POST['email'] );
			$coupon = sanitize_post( $_POST['coupon'] );

			if ( isset( $coupon ) && ! empty( $coupon ) && isset( $entered_email ) && ! empty( $entered_email ) ) {
				$the_coupon = new WC_Coupon( $coupon );
				if ( isset( $the_coupon ) && ! empty( $coupon ) ) {
					$coupon_id = $the_coupon->get_id();
					if ( isset( $coupon_id ) && ! empty( $coupon_id ) && $coupon_id != 0 ) {
						$left_amount = $the_coupon->get_amount();
						$order_id = get_post_meta( $coupon_id, 'mwb_wgm_giftcard_coupon', true );
						$recipnt_email = get_post_meta( $coupon_id, 'mwb_wgm_giftcard_coupon_mail_to', true );
						if ( isset( $order_id ) && ! empty( $order_id ) ) {
							$order = wc_get_order( $order_id );

							$user_id = $order->get_user_id();
							$mwb_user_name = get_userdata( $user_id );
							$mwb_sender_name = $mwb_user_name->first_name . ' ' . $mwb_user_name->last_name;

							$user_email = $order->get_billing_email();
							if ( isset( $user_email ) && ! empty( $user_email ) ) {

								if ( strcasecmp( $user_email, $entered_email ) == 0 ) {
									$html = '<div class="amount_wrapper">' . __( 'Amount Left is: ', 'woocommerce-ultimate-gift-card' ) . wc_price( $left_amount ) . '</div>';
									$response['result'] = true;
									$response['html'] = $html;
									$response['message'] = __( 'Data Match Successfully!!', 'woocommerce-ultimate-gift-card' );
								} elseif ( strcasecmp( $recipnt_email, $entered_email ) == 0 ) {
									$html = '<div class="amount_wrapper">' . __( 'Amount Left is: ', 'woocommerce-ultimate-gift-card' ) . wc_price( $left_amount ) . '</div>';
									$response['result'] = true;
									$response['html'] = $html;
									$response['message'] = __( 'Data Match Successfully!!', 'woocommerce-ultimate-gift-card' );
								} elseif ( strcasecmp( $mwb_sender_name, $entered_email ) == 0 ) {
									$html = '<div class="amount_wrapper">' . __( 'Amount Left is: ', 'woocommerce-ultimate-gift-card' ) . wc_price( $left_amount ) . '</div>';
									$response['result'] = true;
									$response['html'] = $html;
									$response['message'] = __( 'Data Match Successfully!!', 'woocommerce-ultimate-gift-card' );
								} else {
									$response['result'] = false;
									$response['message'] = __( 'Details Do Not Match!', 'woocommerce-ultimate-gift-card' );
								}
							}
						}
					} else {
						$response['result'] = false;
						$response['message'] = __( 'Coupon is Invalid!', 'woocommerce-ultimate-gift-card' );
					}
				}
			} else {
				$response['result'] = false;
				$response['message'] = __( 'Fields cannot be empty!', 'woocommerce-ultimate-gift-card' );
			}
			echo json_encode( $response );
			wp_die();
		}

		public function mwb_wgm_custom_gift_template( $template_ids, $product_id ) {

			$is_customizable = get_post_meta( $product_id, 'woocommerce_customizable_gift_card', true );

			if ( isset( $is_customizable ) && ! empty( $is_customizable ) && $is_customizable == 'yes' ) {
				$args = array(
					'post_type' => 'giftcard',
					'posts_per_page' => -1,
				);
				$template_list = new WP_Query( $args );
				$template = array();
				foreach ( $template_list->posts as $key => $value ) {

					if ( ! empty( $value->ID ) ) {
						return $template_ids = $value->ID;
					}
				}
			}
			return $template_ids;
		}

		// append price for variable price type.
		public function mwb_wgm_append_variable_price() {
			check_ajax_referer( 'mwb-wgm-verify-nonce', 'mwb_nonce' );
			$response['result'] = false;
			$mwb_wgm_price = isset( $_POST['mwb_wgm_price'] ) ? $_POST['mwb_wgm_price'] : '';
			$decimal_separator  = get_option( 'woocommerce_price_decimal_sep' );
			$mwb_wgm_price = floatval( str_replace( $decimal_separator , '.', $mwb_wgm_price ) );
			
			if ( isset( $mwb_wgm_price ) && ! empty( $mwb_wgm_price ) ) {
				// for price based on country.
				if ( class_exists( 'WCPBC_Pricing_Zone' ) ) {
					if ( wcpbc_the_zone() != null && wcpbc_the_zone() ) {
						$mwb_wgm_price = wcpbc_the_zone()->get_exchange_rate_price( $mwb_wgm_price );
					}
				}
				$response['result'] = true;
				$response['new_price'] = wc_price( $mwb_wgm_price );
				echo json_encode( $response );
				wp_die();
			} else {
				echo json_encode( $response );
				wp_die();
			}
		}

		public function mwb_wgm_woocommerce_order_status_change( $order_id,$old_status,$new_status ) {
			$order = new WC_Order( $order_id );
			$coupon_code = $order->get_coupon_codes();
			
			if ( ! empty( $coupon_code ) ) {
				$the_coupon = new WC_Coupon( $coupon_code[0] );
				$coupon_id = $the_coupon->get_id();
				if ( ! metadata_exists( 'post', $order_id, 'coupon_used' ) ) {
					$coupon_used = 1 ;
					update_post_meta( $order_id, 'coupon_used', $coupon_used );

				} else {
					$coupon_used =  get_post_meta( $order_id, 'coupon_used' )[0] ;
				}
				
				if ( ( $new_status == 'cancelled' || $new_status == 'failed' ) && $coupon_used == 1 ) {
				
					$amount = get_post_meta( $coupon_id, 'coupon_amount', true );
					$total_discount = get_post_meta( $order_id, '_cart_discount', true );
					
					$remaining_amount = $amount + $total_discount;
					$remaining_amount = round( $remaining_amount, 2 );
					update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount);
					$coupon_used = 0;
					update_post_meta( $order_id, 'coupon_used', $coupon_used );

				} else if ( ( $new_status == 'pending' || $new_status == 'processing' || $new_status == 'on-hold' || $new_status == 'completed' ) && $coupon_used == 0 ) {
					
					$amount = get_post_meta( $coupon_id, 'coupon_amount', true );
					$total_discount = get_post_meta( $order_id, '_cart_discount', true );
					
					if ( $amount < $total_discount ) {
						$remaining_amount = 0;
					} else {
						$remaining_amount = $amount - $total_discount;
						$remaining_amount = round( $remaining_amount, 2 );
					}
					update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount);
					$coupon_used = 1;
					update_post_meta( $order_id, 'coupon_used', $coupon_used );
				}
			}
		}
	}
	new MWB_WGM_Card_Product_Function();
}
?>
