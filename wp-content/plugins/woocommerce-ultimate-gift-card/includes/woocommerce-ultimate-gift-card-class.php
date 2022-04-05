<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'MWB_WGM_Card_Product' ) ) {

	/**
	 * This is class for managing order status and other functionalities .
	 *
	 * @name    MWB_WGM_Card_Product
	 * @category Class
	 * @author   makewebbetter <webmaster@makewebbetter.com>
	 */

	class MWB_WGM_Card_Product {

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
		 * This is function is used to add the whole functionality of backend
		 *
		 * @name mwb_wgm_load_woocommerce
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_load_woocommerce() {
			if ( function_exists( 'WC' ) ) {
				$this->add_hooks_and_filters();
			}
		}
		/**
		 * This is function is used to adding the hooks and filters
		 *
		 * @name add_hooks_and_filters
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function add_hooks_and_filters() {
			add_action( 'init', array( $this, 'mwb_wgm_giftcard_custompost' ) );
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'mwb_wgm_woocommerce_product_data_tabs' ), 10, 1 );
			add_action( 'edit_form_after_title', array( $this, 'mwb_wgm_edit_form_after_title' ), 10, 1 );
			add_action( 'admin_menu', array( $this, 'mwb_wgm_admin_menu' ), 20, 2 );
			add_filter( 'product_type_selector', array( $this, 'mwb_wgm_gift_card_product' ) );
			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'mwb_wgm_woocommerce_product_options_general_product_data' ), 10, 1 );
			add_action( 'admin_enqueue_scripts', array( $this, 'mwb_wgm_admin_enqueue_scripts' ), 10, 1 );

			add_action( 'save_post', array( $this, 'mwb_wgm_save_post' ), 10, 1 );
			add_action( 'woocommerce_after_order_itemmeta', array( $this, 'mwb_wgm_woocommerce_after_order_itemmeta' ), 10, 3 );
			add_action( 'add_meta_boxes', array( $this, 'mwb_wgm_add_meta_boxes' ), 10, 2 );
			add_action( 'wp_ajax_mwb_wgm_resend_mail', array( $this, 'mwb_wgm_resend_mail_process' ) );
			add_action( 'wp_ajax_nopriv_mwb_wgm_resend_mail', array( $this, 'mwb_wgm_resend_mail_process' ) );
			add_action( 'wp_ajax_mwb_wgm_append_default_template', array( $this, 'mwb_wgm_append_default_template' ) );
			add_action( 'wp_ajax_nopriv_mwb_wgm_append_default_template', array( $this, 'mwb_wgm_append_default_template' ) );
			add_action( 'wp_ajax_mwb_wgm_offline_resend_mail', array( $this, 'mwb_wgm_offline_resend_mail' ) );
			add_action( 'wp_ajax_nopriv_mwb_wgm_offline_resend_mail', array( $this, 'mwb_wgm_offline_resend_mail' ) );
			add_action( 'init', array( $this, 'get_all_woocommerce_orders' ) );
			add_action( 'save_post', array( $this, 'mwb_save_meta_fields' ) );
			add_action( 'add_meta_boxes', array( $this, 'mwb_css_metabox' ) );
			add_filter( 'post_row_actions', array( $this, 'mwb_custom_gift_post' ), 10, 2 );
			add_action( 'init', array( $this, 'mwb_wgm_preview_email_template' ) );
			add_action( 'wp_ajax_mwb_wgm_resend_coupon_amount', array( $this, 'mwb_wgm_resend_coupon_amount' ) );
			add_action( 'wp_ajax_nopriv_mwb_wgm_resend_coupon_amount', array( $this, 'mwb_wgm_resend_coupon_amount' ) );
			add_action( 'wp_ajax_mwb_wgm_hide_sidebar_forever', array( $this, 'mwb_wgm_hide_sidebar_forever' ) );
			add_action( 'wp_ajax_nopriv_mwb_wgm_hide_sidebar_forever', array( $this, 'mwb_wgm_hide_sidebar_forever' ) );
			add_action( 'manage_posts_extra_tablenav', array( $this, 'add_import_template_button' ) );
			$woo_ver = WC()->version;
			if ( $woo_ver < '3.0.0' ) {
				add_action( 'woocommerce_coupon_options_usage_limit', array( $this, 'mwb_wgm_manual_increment_usage_count_old_woo' ) );
			} else {
				add_action( 'woocommerce_coupon_options_usage_limit', array( $this, 'mwb_wgm_manual_increment_usage_count' ), 10, 2 );
			}
			add_action( 'save_post', array( $this, 'mwb_wgm_save_coupon_post' ) );
			add_action( 'wp_ajax_mwb_wgm_check_manual_code_exist', array( $this, 'mwb_wgm_check_manual_code_exist' ) );
			add_action( 'wp_ajax_nopriv_mwb_wgm_check_manual_code_exist', array( $this, 'mwb_wgm_check_manual_code_exist' ) );
			add_action( 'wp_ajax_mwb_wgm_update_item_meta_with_new_email', array( $this, 'mwb_wgm_update_item_meta_with_new_email' ) );
			add_action( 'wp_ajax_nopriv_mwb_wgm_update_item_meta_with_new_email', array( $this, 'mwb_wgm_update_item_meta_with_new_email' ) );
			add_action( 'wp_ajax_mwb_wgm_new_way_for_generating_pdfs', array( $this, 'mwb_wgm_new_way_for_generating_pdfs' ) );
			add_action( 'wp_ajax_mwb_wgm_next_step_for_generating_pdfs', array( $this, 'mwb_wgm_next_step_for_generating_pdfs' ) );
			add_action( 'restrict_manage_posts', array( $this, 'mwb_wgm_restrict_manage_posts' ) );
			add_filter( 'request', array( $this, 'mwb_wgm_request_query' ) );
			add_action( 'wp_ajax_mwb_wgm_register_license', array( $this, 'mwb_wgm_activate_license' ) );
			add_action( 'mwb_wgm_update_json', array( $this, 'mwb_wgm_update_json_process' ) );
		}

		/**
		 * This is used to add import button in Custom post type
		 *
		 * @name add_import_template_button
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function add_import_template_button() {
			global $typenow;
			$pagetemplate = get_option( 'mwb_wgm_new_pdf_support_templates_with_A4', false );

			if ( $pagetemplate == false ) {
				if ( 'giftcard' == $typenow ) {
					?>
					<input type="submit" name="import_templates" value="<?php _e( 'Import A4 Size PDF Supported Templates', 'woocommerce-ultimate-gift-card' ); ?>" class="mwb_import_templates button">
					<p class="description" style="color: #0073aa;"><?php _e( 'If you want to use pdf feature we suggest you to delete all previous templates and import pdf supported templates', 'woocommerce-ultimate-gift-card' ); ?></p>
					<?php
				}
			}
			$mwb_wgm_christmas = get_option( 'mwb_wgm_new_christmas_template', false );
			if ( $mwb_wgm_christmas == false ) {
				if ( 'giftcard' == $typenow ) {
					?>
					<input type="submit" name="import_christmas_templates" value="<?php _e( 'Import Christmas Template', 'woocommerce-ultimate-gift-card' ); ?>" class="mwb_import_templates button">
					<?php
				}
			}
			$mwb_wgm_coming_new_year = get_option( 'mwb_wgm_coming_new_year', false );
			if ( $mwb_wgm_coming_new_year == false ) {
				if ( 'giftcard' == $typenow ) {
					?>
					<input type="submit" name="import_horizontal_templates" value="<?php _e( 'Import New Year', 'woocommerce-ultimate-gift-card' ); ?>" class="mwb_import_templates button">
					<?php
				}
			}
			$mwb_wgm_simple_birthday = get_option( 'mwb_wgm_simple_birthday', false );
			if ( $mwb_wgm_simple_birthday == false ) {
				if ( 'giftcard' == $typenow ) {
					?>
					<input type="submit" name="import_simple_birthday_templates" value="<?php _e( 'Import New Format', 'woocommerce-ultimate-gift-card' ); ?>" class="mwb_import_templates button">
					<?php
				}
			}
			$mwb_wgm_new_mom_temp = get_option( 'mwb_wgm_new_mom_temp', false );
			if ( $mwb_wgm_new_mom_temp == false ) {
				if ( 'giftcard' == $typenow ) {
					?>
					<input type="submit" name="import_new_mother_template" value="<?php _e( 'Import Mothers Day', 'woocommerce-ultimate-gift-card' ); ?>" class="mwb_import_templates button">
					<?php
				}
			}
		}


		/**
		 * This is used to send gift card with more amount
		 *
		 * @name mwb_wgm_resend_coupon_amount
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_resend_coupon_amount() {
			check_ajax_referer( 'mwb-wgm-verify-nonce', 'mwb_nonce' );
			$response['result'] = false;
			$response['message'] = __( 'Mail sending failed due to some issue. Please try again.', 'woocommerce-ultimate-gift-card' );
			$mwb_wgm_change_admin_email_for_shipping = get_option( 'mwb_wgm_change_admin_email_for_shipping', '' );
			$woo_ver = WC()->version;
			if ( isset( $_POST['order_id'] ) && ! empty( $_POST['order_id'] ) ) {
				$order_id = sanitize_post( $_POST['order_id'] );
				$coupon_arr = $_POST['selectedcoupon'];
				$new_price = sanitize_post( $_POST['selectedprice'] );

				foreach ( $coupon_arr as $key => $value ) {
					$coupon_arr_detail = explode( '#mwb#', $value );
					$coupon_details = new WC_Coupon( $coupon_arr_detail[1] );
					if ( $woo_ver < '3.0.0' ) {
						$coupon_id = $coupon_details->id;
					} else {
						$coupon_id = $coupon_details->get_id();
					}
					update_post_meta( $coupon_id, 'coupon_amount', $new_price );
					$order_details = new WC_Order( $order_id );
					$order_items = $order_details->get_items();
					foreach ( $order_items as $item_id => $item ) {
						if ( $coupon_arr_detail[2] == $item_id ) {
							$mailsend = false;
							$woo_ver = WC()->version;
							$gift_img_name = '';
							if ( $woo_ver < '3.0.0' ) {
								$product = $order_details->get_product_from_item( $item );
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
								if ( isset( $item['item_meta']['Image'] ) && ! empty( $item['item_meta']['Image'] ) ) {
									$mailsend = true;
									$gift_img_name = $item['item_meta']['Image'][0];
								}
								if ( isset( $item['item_meta']['Message'] ) && ! empty( $item['item_meta']['Message'] ) ) {
									$mailsend = true;
									$gift_msg = $item['item_meta']['Message'][0];
								}
								if ( isset( $item['item_meta']['Delivery Method'] ) && ! empty( $item['item_meta']['Delivery Method'] ) ) {
									$mailsend = true;
									$delivery_method = $item['item_meta']['Delivery Method'][0];
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
							} else {
								$product = $item->get_product();
								$item_meta_data = $item->get_meta_data();
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
									if ( isset( $value->key ) && $value->key == 'Image' && ! empty( $value->value ) ) {
										$mailsend = true;
										$gift_img_name = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'Message' && ! empty( $value->value ) ) {
										$mailsend = true;
										$gift_msg = $value->value;
									}
									if ( isset( $value->key ) && $value->key == 'Delivery Method' && ! empty( $value->value ) ) {
										$mailsend = true;
										$delivery_method = $value->value;
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
							}
							if ( $mailsend ) {
								$gift_order = true;
								if ( $woo_ver < '3.0.0' ) {
									$product_id = $product->id;
								} else {
									$product_id = $product->get_id();
								}
								$gift_couponnumber = get_post_meta( $order_id, "$order_id#$item_id", true );
								if ( empty( $gift_couponnumber ) ) {
									$gift_couponnumber = get_post_meta( $order_id, "$order_id#$product_id", true );
								}
								foreach ( $gift_couponnumber as $coupon_key => $coupon_val ) {
									$the_coupon = new WC_Coupon( $coupon_val );
									if ( $woo_ver < '3.0.0' ) {
										$expiry_date_timestamp = $the_coupon->expiry_date;
										$couponamont = $the_coupon->coupon_amount;
									} else {
										$expiry_date_timestamp = $the_coupon->get_date_expires();
										$expiry_date_timestamp = date_format( $expiry_date_timestamp, 'Y-m-d' );

										$expiry_date_timestamp = strtotime( $expiry_date_timestamp );

										$couponamont = $the_coupon->get_amount();

									}

									if ( empty( $expiry_date_timestamp ) ) {
										$expirydate_format = __( 'No Expiration', 'woocommerce-ultimate-gift-card' );
									} else {
										$expirydate = date_i18n( 'Y-m-d', $expiry_date_timestamp );
										$expirydate_format = date_create( $expirydate );

										$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format_1', false );
										if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {

											$expirydate_format = date_i18n( $selected_date, strtotime( "$expirydate" ) );
										} else {
											$expirydate_format = date_format( $expirydate_format, 'Y-m-d' );
										}
									}
									if ( $woo_ver < '3.0.0' ) {
										$mwb_wgm_pricing = get_post_meta( $product->id, 'mwb_wgm_pricing', true );
									} else {
										$mwb_wgm_pricing = get_post_meta( $product->get_id(), 'mwb_wgm_pricing', true );
									}
									$templateid = $mwb_wgm_pricing['template'];
									if ( is_array( $templateid ) && array_key_exists( 0, $templateid ) ) {
										$temp = $templateid[0];
									} else {
										$temp = $templateid;
									}
									$currenttime = time();
									$args['from'] = $from;
									$args['to'] = isset( $to_name ) ? $to_name : $to;
									$args['message'] = stripcslashes( $gift_msg );
									$args['coupon'] = apply_filters( 'mwb_wgm_qrcode_coupon', $coupon_val );
									$args['expirydate'] = $expirydate_format;
									$args['amount'] = wc_price( $couponamont );
									$args['templateid'] = isset( $selected_template ) && ! empty( $selected_template ) ? $selected_template : $temp;
									$args['product_id'] = $product_id;
									$browse_enable = get_option( 'mwb_wgm_other_setting_browse', false );
									if ( $browse_enable == 'on' ) {
										if ( $gift_img_name != '' ) {
											$args['browse_image'] = $gift_img_name;
										}
									}
									$mwb_wgm_object = new MWB_WGM_Card_Product_Function();
									$message = $mwb_wgm_object->mwb_wgm_giftttemplate( $args );
									$mwb_wgm_pdf_enable = get_option( 'mwb_wgm_addition_pdf_enable', false );
									if ( isset( $mwb_wgm_pdf_enable ) && $mwb_wgm_pdf_enable == 'on' ) {
										$site_name = $_SERVER['SERVER_NAME'];
										$time = time();
										$this->mwb_wgm_attached_pdf( $message, $site_name, $time, $order_id, $coupon_val );
										$attachments = array( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $time . $site_name . '.pdf' );
									} else {
										$attachments = array();
									}
									$get_mail_status = true;
									$get_mail_status = apply_filters( 'mwb_send_mail_status', $get_mail_status );

									if ( $get_mail_status ) {
										if ( isset( $delivery_method ) && $delivery_method == 'Mail to recipient' ) {
											$subject = get_option( 'mwb_wgm_other_setting_giftcard_subject', false );
										}
										if ( isset( $delivery_method ) && $delivery_method == 'Downloadable' ) {
											$subject = get_option( 'mwb_wgm_other_setting_giftcard_subject_downloadable', false );
										}
										if ( isset( $delivery_method ) && $delivery_method == 'Shipping' ) {
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
										if ( isset( $delivery_method ) ) {
											if ( $delivery_method == 'Mail to recipient' ) {
												$woo_ver = WC()->version;
												if ( $woo_ver < '3.0.0' ) {
													$from = $order_details->billing_email;
												} else {
													$from = $order_details->get_billing_email();
												}
											}
											if ( $delivery_method == 'Downloadable' ) {
												$woo_ver = WC()->version;
												if ( $woo_ver < '3.0.0' ) {
													$to = $order_details->billing_email;
												} else {
													$to = $order_details->get_billing_email();
												}
											}
											if ( $delivery_method == 'Shipping' ) {
												$admin_email = get_option( 'admin_email' );
												$alternate_email = ! empty( $mwb_wgm_change_admin_email_for_shipping ) ? $mwb_wgm_change_admin_email_for_shipping : $admin_email;
												$to = $alternate_email;
											}
										}
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
										$mwb_wgm_disable_buyer_notification = get_option( 'mwb_wgm_disable_buyer_notification', 'off' );
										if ( $mwb_wgm_disable_buyer_notification == 'off' ) {
											wc_mail( $from, $subject, $message );
										}
									}
									$response['result'] = true;
									$response['message'] = __( 'Coupon amount is changed and Mail is Successfully Send.', 'woocommerce-ultimate-gift-card' );
									break;
								}
							}
						}
					}
				}
			}
			echo json_encode( $response );
			die;
		}
		/**
		 * This is used to display email template when clicked
		 *
		 * @name mwb_wgm_preview_email_template
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_preview_email_template() {
			if ( isset( $_GET['mwb_template'] ) ) {
				if ( $_GET['mwb_template'] == 'giftcard' ) {
					$post_id = $_GET['post_id'];
					$todaydate = date_i18n( 'Y-m-d' );
					$expiry_date = get_option( 'mwb_wgm_general_setting_giftcard_expiry', false );

					if ( $expiry_date > 0 || $expiry_date === 0 ) {
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
					$templateid = $post_id;

					$args['from'] = __( 'from@example.com', 'woocommerce-ultimate-gift-card' );
					$args['to'] = __( 'to@example.com', 'woocommerce-ultimate-gift-card' );
					$args['message'] = __( 'Your gift message will appear here which you send to your receiver. ', 'woocommerce-ultimate-gift-card' );
					$args['coupon'] = apply_filters( 'mwb_wgm_static_coupon_img', $coupon );
					$args['expirydate'] = $expirydate_format;
					$args['amount'] = wc_price( 100 );
					$args['templateid'] = $templateid;

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
		 * This is used to display email template
		 *
		 * @name mwb_wgm_giftttemplate
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_giftttemplate( $args ) {
			$templateid = $args['templateid'];

			$template = get_post( $templateid, ARRAY_A );
			$templatehtml = $template['post_content'];
			$giftcard_logo_html = '';

			$giftcard_upload_logo = get_option( 'mwb_wgm_other_setting_upload_logo', false );
			$giftcard_logo_height = get_option( 'mwb_wgm_other_setting_logo_height', false );
			$giftcard_logo_width = get_option( 'mwb_wgm_other_setting_logo_width', false );

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

			$featured_image = wp_get_attachment_url( get_post_thumbnail_id( $templateid ) );
			$background_image = get_option( 'mwb_wgm_other_setting_background_logo', false );

			$background_color = get_option( 'mwb_wgm_other_setting_background_color', false );
			$giftcard_event_html = '';
			if ( isset( $background_image ) && ! empty( $background_image ) ) {
				$giftcard_event_html = "<img src='$background_image' width='100%' />";
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
				$templatehtml = str_replace( '[MESSAGE]', $args['message'], $templatehtml );
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
				$templatehtml = str_replace( '[FROM]', '', $templatehtml );
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
			$templatehtml = str_replace( '[ORDERID]', '', $templatehtml );
			$templatehtml = str_replace( '[PRODUCTNAME]', '', $templatehtml );
			$templatehtml = str_replace( '[FROMEMAIL]', '', $templatehtml );
			$templatehtml = $giftcard_css . $templatehtml;

			return $templatehtml;
		}
		/**
		 * This is used to create link in gift card post
		 *
		 * @name mwb_custom_gift_post
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_custom_gift_post( $actions, $post ) {
			if ( 'giftcard' === $post->post_type ) {
				$actions['mwb_quick_view'] = '<a href="' . admin_url( 'edit.php?post_type=giftcardpost&post_id=' . $post->ID . '&mwb_template=giftcard&TB_iframe=true&width=630&height=500' ) . '" rel="permalink" class="thickbox">' . __( 'Preview', 'woocommerce-ultimate-gift-card' ) . '</a>';
			}
			return $actions;
		}
		/**
		 * This is used to save meta fields of templates
		 *
		 * @name mwb_save_meta_fields
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_save_meta_fields( $post_id ) {

			if ( array_key_exists( 'mwb_css_field', $_POST ) ) {
				if ( isset( $_POST['mwb_css_field'] ) ) {
					update_post_meta(
						$post_id,
						'mwb_css_field',
						trim( $_POST['mwb_css_field'] )
					);
				}
			}
		}
		/**
		 * This is the html of metabox
		 *
		 * @name template_css_metabox
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function template_css_metabox( $post ) {
			$value = get_post_meta( $post->ID, 'mwb_css_field', true );
			?>
			<table class="form-table">

				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_css_field"><?php _e( 'Custom CSS', 'woocommerce-ultimate-gift-card' ); ?></label>
					</th>
					<td class="forminp forminp-text">
						<label>
							<textarea name="mwb_css_field" id="mwb_css_field" class="mwb_css_field" style="width:308px;height:100px;">
								<?php echo trim( $value ); ?> 
							</textarea>				
						</label>
					</td>
				</tr>
			</table>
			<?php
		}
		/**
		 * This is used to add metabox
		 *
		 * @name template_css_metabox
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_css_metabox() {
			$screens = [ 'giftcard' ];
			foreach ( $screens as $screen ) {
				add_meta_box(
					'mwb_css_field',           // Unique ID
					__( 'Custom CSS', 'woocommerce-ultimate-gift-card' ),  // Box title
					array( $this, 'template_css_metabox' ),  // Content callback
					$screen                   // Post type
				);
			}
		}

		/**
		 * This function is used to export coupons details from order table
		 *
		 * @name get_all_woocommerce_orders
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function get_all_woocommerce_orders() {
			if ( strpos( $_SERVER['REQUEST_URI'], 'admin.php?page=mwb-wgc-setting&mwb_wugc_export_csv=' ) ) {
				$title = array();
				$content = array();
				$filename = 'mwb_export.csv';
				if ( $_GET['mwb_wugc_export_csv'] == 'mwb_woo_gift_card_report' ) {

					$gift_card_order_id = array();
					$coupons_args = array(
						'posts_per_page'   => -1,
						'orderby'          => 'title',
						'order'            => 'asc',
						'post_type'        => 'shop_coupon',
						'post_status'      => 'publish',
					);

					$coupons = get_posts( $coupons_args );

					if ( $coupons != null ) {

						foreach ( $coupons as $post_key ) {
							$giftcardcoupon = get_post_meta( $post_key->ID, 'mwb_wgm_giftcard_coupon', true );
							if ( ! empty( $giftcardcoupon ) ) {
								$gift_card_order_id[] = $giftcardcoupon;
							}
						}

						$gift_card_order_id = array_unique( $gift_card_order_id );

						$args = array(
							'post_type'   => wc_get_order_types(),
							'posts_per_page'  => -1,
							'post_status' => array_keys( wc_get_order_statuses() ),
							'post__in'    => $gift_card_order_id,
						);
						$loop = new WP_Query( $args );

						while ( $loop->have_posts() ) {
							$loop->the_post();

							$order = new WC_Order( $loop->post->ID );

							$order_items = $order->get_items();// Items Array

							$all_item_keys = array_keys( $order_items );// Items Keys

							$woo_ver = WC()->version;
							foreach ( $all_item_keys as $key => $value ) {
								$coupon_code = get_post_meta( $loop->post->ID, $loop->post->ID . '#' . $value, true );
								// check the coupon is array or not, as the previously it was just the string(before 2.4.3)
								if ( is_array( $coupon_code ) && ! empty( $coupon_code ) ) {
									foreach ( $coupon_code as $coupon_key => $coupon_val ) {
										if ( $coupon_val != null ) {
											$coupon = new WC_Coupon( $coupon_val );
											if ( $woo_ver < '3.0.0' ) {
												$usage_amount = $coupon->usage_count;
												if ( $coupon->usage_count == null ) {
													$usage_amount = 0;
												}
												$coupon_amount_ = $coupon->coupon_amount;
												$to_type = gettype( $order_items[ $value ]['To'] );
												$from_type = gettype( $order_items[ $value ]['From'] );
												if ( preg_match( '/<[^<]+>/', $order_items[ $value ]['To'] ) ) {
													$to = new SimpleXMLElement( $order_items[ $value ]['To'] );
													$to_arr = substr( $to['href'], 7 );
												} else {
													$to = $order_items[ $value ]['To'];
													$to_arr = $to;
												}
												if ( preg_match( '/<[^<]+>/', $order_items[ $value ]['From'] ) ) {
													$from = new SimpleXMLElement( $order_items[ $value ]['From'] );
													$from_arr = substr( $from['href'], 7 );
												} else {
													$from = $order_items[ $value ]['From'];
													$from_arr = $from;
												}

												$content[] = array(
													$loop->post->ID,
													$coupon_val,
													$to_arr,
													$from_arr,
													$order_items[ $value ]['Message'],
													$usage_amount,
													$coupon_amount_,
												);
											} else {
												$usage_amount = $coupon->get_usage_count();
												if ( $coupon->get_usage_count() == null ) {
													$usage_amount = 0;
												}
												$coupon_amount_ = $coupon->get_amount();
												$to = $order_items[ $value ]['To'];
												$from = $order_items[ $value ]['From'];
												$content[] = array(
													$loop->post->ID,
													$coupon_val,
													$to,
													$from,
													$order_items[ $value ]['Message'],
													$usage_amount,
													$coupon_amount_,
												);
											}
										}
									}
								} else {
									if ( $coupon_code != null ) {
										$coupon = new WC_Coupon( $coupon_code );
										if ( $woo_ver < '3.0.0' ) {
											$usage_amount = $coupon->usage_count;
											if ( $coupon->usage_count == null ) {
												$usage_amount = 0;
											}
											$coupon_amount_ = $coupon->coupon_amount;
											$to_type = gettype( $order_items[ $value ]['To'] );
											$from_type = gettype( $order_items[ $value ]['From'] );
											if ( preg_match( '/<[^<]+>/', $order_items[ $value ]['To'] ) ) {
												$to = new SimpleXMLElement( $order_items[ $value ]['To'] );
												$to_arr = substr( $to['href'], 7 );
											} else {
												$to = $order_items[ $value ]['To'];
												$to_arr = $to;
											}
											if ( preg_match( '/<[^<]+>/', $order_items[ $value ]['From'] ) ) {
												$from = new SimpleXMLElement( $order_items[ $value ]['From'] );
												$from_arr = substr( $from['href'], 7 );
											} else {
												$from = $order_items[ $value ]['From'];
												$from_arr = $from;
											}

											$content[] = array(
												$loop->post->ID,
												$coupon_code,
												$to_arr,
												$from_arr,
												$order_items[ $value ]['Message'],
												$usage_amount,
												$coupon_amount_,
											);

										} else {
											$usage_amount = $coupon->get_usage_count();
											if ( $coupon->get_usage_count() == null ) {
												$usage_amount = 0;
											}
											$coupon_amount_ = $coupon->get_amount();
											$to = $order_items[ $value ]['To'];
											$from = $order_items[ $value ]['From'];
											$content[] = array(
												$loop->post->ID,
												$coupon_code,
												$to,
												$from,
												$order_items[ $value ]['Message'],
												$usage_amount,
												$coupon_amount_,
											);
										}
									}
								}
							}
						}
					}
					$title = array(
						__( 'Order Id', 'woocommerce-ultimate-gift-card' ),
						__( 'Coupon Code', 'woocommerce-ultimate-gift-card' ),
						__( 'To', 'woocommerce-ultimate-gift-card' ),
						__( 'From', 'woocommerce-ultimate-gift-card' ),
						__( 'Message', 'woocommerce-ultimate-gift-card' ),
						__( 'Usage Count', 'woocommerce-ultimate-gift-card' ),
						__( 'Coupon Amount Left', 'woocommerce-ultimate-gift-card' ),
					);
					$filename = 'mwb_woo_gift_card_report.csv';
				}
				if ( $_GET['mwb_wugc_export_csv'] == 'mwb_woo_offline_gift_card_report' ) {

					global $wpdb;
					$table_name = $wpdb->prefix . 'offline_giftcard';
					$query = "SELECT * FROM $table_name";
					$giftresults = $wpdb->get_results( $query, ARRAY_A );
					foreach ( $giftresults as $key => $value ) {
						$content[] = array(
							$value['id'],
							$value['coupon'],
							$value['to'],
							$value['from'],
							$value['message'],
							$value['amount'],
						);
					}
					$filename = 'mwb_woo_offline_gift_card_report.csv';
					$title = array(
						__( 'Id', 'woocommerce-ultimate-gift-card' ),
						__( 'Coupon Code', 'woocommerce-ultimate-gift-card' ),
						__( 'To', 'woocommerce-ultimate-gift-card' ),
						__( 'From', 'woocommerce-ultimate-gift-card' ),
						__( 'Message', 'woocommerce-ultimate-gift-card' ),
						__( 'Coupon Amount', 'woocommerce-ultimate-gift-card' ),
					);
				}
				$uploadDirPath = MWB_UPLOAD_DIR . '/';
				$errorLogFolder = 'mwb_woo_gift_card_import_error/';

				$importErrorDir = $uploadDirPath . $errorLogFolder;
				if ( ! is_dir( $importErrorDir ) ) {
					mkdir( $importErrorDir, $permissions = 0777 );
				}

				$output = fopen( $importErrorDir . $filename, 'w' );
				fputcsv( $output, $title );
				foreach ( $content as $con ) {
					fputcsv( $output, $con );
				}
				$fileName = sanitize_text_field( $filename );
				$uploadDirPath = MWB_UPLOAD_DIR . '/';
				$errorLogFolder = 'mwb_woo_gift_card_import_error/';
				$pathOfFileToDownload = $uploadDirPath . $errorLogFolder . $fileName;

				if ( file_exists( $pathOfFileToDownload ) ) {
					header( 'Content-Description: File Transfer' );
					header( 'Content-Type: application/csv' );
					header( 'Content-Disposition: attachment; filename="' . basename( $pathOfFileToDownload ) . '"' );
					header( 'Expires: 0' );
					header( 'Cache-Control: must-revalidate' );
					header( 'Pragma: public' );
					header( 'Content-Length: ' . filesize( $pathOfFileToDownload ) );
					readfile( $pathOfFileToDownload );
					exit;
				}
			}
		}
		/**
		 * This function is used to resend mail for Offline giftcard
		 *
		 * @name mwb_wgm_offline_resend_mail
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */

		function mwb_wgm_offline_resend_mail() {
			check_ajax_referer( 'mwb-wgm-verify-nonce', 'mwb_nonce' );
			$response['result'] = false;
			$response['message'] = __( 'Mail sending failed due to some issue. Please try again.', 'woocommerce-ultimate-gift-card' );
			global $wpdb;
			$offline_orderid = sanitize_post( $_POST['id'] );
			$table_name = $wpdb->prefix . 'offline_giftcard';
			$query = "SELECT * FROM $table_name WHERE `id`=$offline_orderid";
			$giftresults = $wpdb->get_results( $query, ARRAY_A );
			$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format_1', false );
			$senddatetime = '';
			if ( isset( $giftresults[0] ) ) {

				$giftresult = $giftresults[0];

				if ( isset( $giftresult['mail'] ) && $giftresult['mail'] == null && $giftresult['mail'] != 1 ) {

					$schedule_date = $giftresult['schedule'];
					if ( is_string( $schedule_date ) ) {
						if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
							if ( $selected_date == 'd/m/Y' ) {
								$schedule_date = str_replace( '/', '-', $schedule_date );
							}
						}

						$senddatetime = strtotime( $schedule_date );
					}
					$senddate = date_i18n( 'Y-m-d', $senddatetime );
					$todaytime = time();
					$todaydate = date_i18n( 'Y-m-d', $todaytime );
					$senddatetime = strtotime( "$senddate" );
					$todaytime = strtotime( "$todaydate" );
					$giftdiff = $senddatetime - $todaytime;

					if ( $giftdiff > 0 ) {
						$response['result'] = false;
						$response['message'] = __( 'Mail not send as scheduled date is not reached.', 'woocommerce-ultimate-gift-card' );
						echo json_encode( $response );
						die;
					} else {
						$couponcreated = mwb_wgm_create_offlinegift_coupon( $giftresult['coupon'], $giftresult['amount'], $offline_orderid, $giftresult['template'], $giftresult['to'] );

					}
				}

				$woo_ver = WC()->version;
				$product_id = $giftresult['template'];
				$mwb_wgm_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );

				$templateid = $mwb_wgm_pricing['template'];
				if ( is_array( $templateid ) && array_key_exists( 0, $templateid ) ) {
					$temp = $templateid[0];
				} else {
					$temp = $templateid;
				}
				$args['from'] = $giftresult['from'];
				$args['to'] = $giftresult['to'];
				$args['message'] = stripcslashes( $giftresult['message'] );
				$args['coupon'] = apply_filters( 'mwb_wgm_qrcode_coupon', $giftresult['coupon'] );

				$to = $args['to'];
				$from = $args['from'];
				$couponcode = $giftresult['coupon'];
				$coupon = new WC_Coupon( $couponcode );

				if ( $woo_ver < '3.0.0' ) {
					$coupon_id = $coupon->id;

				} else {
					$coupon_id = $coupon->get_id();
				}
				if ( isset( $coupon_id ) ) {
					if ( $woo_ver < '3.0.0' ) {
						$expirydate = $coupon->expiry_date;
						if ( is_string( $expirydate ) ) {
							if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
								if ( $selected_date == 'd/m/Y' ) {
									$expirydate = str_replace( '/', '-', $expirydate );
								}
							}

							$expirydate = strtotime( $expirydate );
						}
					} else {
						$expirydate = $coupon->get_date_expires();
						if ( isset( $expirydate ) && ! empty( $expirydate ) ) {
							$expirydate = date_format( $expirydate, 'Y-m-d' );
							$expirydate = strtotime( $expirydate );
						}
					}

					if ( empty( $expirydate ) ) {
						$expirydate_format = __( 'No Expiration', 'woocommerce-ultimate-gift-card' );
					} else {
						$expiry_date = date_i18n( 'Y-m-d', $expirydate );
						$expirydate_format = date_create( $expiry_date );

						$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format_1', false );
						if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
							$expirydate_format = date_i18n( $selected_date, $expirydate);
						} else {
							$expirydate_format = date_format( $expirydate_format, 'jS M Y' );
						}
					}
					$args['expirydate'] = $expirydate_format;
					$args['amount'] = wc_price( $giftresult['amount'] );
					$args['templateid'] = $temp;
					$args['product_id'] = $product_id;

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
					$where = array( 'id' => $offline_orderid );
					$update_data = $wpdb->update( $table_name, $dataToupdate, $where );
					$response['result'] = true;
					$response['message'] = __( 'Mail Sent Successfully.', 'woocommerce-ultimate-gift-card' );
					echo json_encode( $response );
					wp_die();

				}
			}
			echo json_encode( $response );
			wp_die();
		}

		/**
		 * This function is used to process resend mail request
		 *
		 * @name mwb_wgm_resend_mail_process
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_resend_mail_process() {
			check_ajax_referer( 'mwb-wgm-verify-nonce', 'mwb_nonce' );
			$response['result'] = false;
			$response['message'] = __( 'Mail sending failed due to some issue. Please try again.', 'woocommerce-ultimate-gift-card' );
			$woo_ver = WC()->version;
			$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format_1', false );
			$mwb_wgm_change_admin_email_for_shipping = get_option( 'mwb_wgm_change_admin_email_for_shipping', '' );
			if ( isset( $_POST['order_id'] ) && ! empty( $_POST['order_id'] ) ) {

				$order_id = sanitize_post( $_POST['order_id'] );
				$order = wc_get_order( $order_id );
				foreach ( $order->get_items() as $item_id => $item ) {
					if ( $woo_ver < '3.0.0' ) {
						$product = $order->get_product_from_item( $item );
					} else {
						$product = $item->get_product();
					}
					$gift_img_name = '';
					$mailsend = false;
					$from = '';
					$gift_msg = '';
					$woo_ver = WC()->version;

					if ( $woo_ver < '3.0.0' ) {

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
									$response['message'] = __( 'Giftcard Scheduled Date has not been reached for some products.', 'woocommerce-ultimate-gift-card' );
									continue;
								}
							}
						}
					} else {
						$item_meta_data = $item->get_meta_data();
						$giftcard_date_check = false;
						$gift_date = '';
						$from = '';
						$gift_msg = '';
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
								$giftcard_date_check = true;
								$gift_date = $value->value;
							}
							if ( isset( $value->key ) && $value->key == 'Delivery Method' && ! empty( $value->value ) ) {
								$mailsend = true;
								$delivery_method = $value->value;
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
						if ( $giftcard_date_check ) {
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

							$giftdiff = $senddatetime - $todaytime;
							if ( isset( $delivery_method ) && $delivery_method == 'Mail to recipient' ) {
								if ( $giftdiff > 0 ) {
									$response['message'] = __( 'Giftcard Scheduled Date has not been reached for some products.', 'woocommerce-ultimate-gift-card' );
									continue;
								}
							}
						}
					}
					if ( $mailsend ) {
						$gift_order = true;
						$product_id = $product->get_id();
						$gift_couponnumber = get_post_meta( $order_id, "$order_id#$item_id", true );
						if ( empty( $gift_couponnumber ) ) {
							$gift_couponnumber = get_post_meta( $order_id, "$order_id#$product_id", true );
						}
						foreach ( $gift_couponnumber as $key => $value ) {
							$the_coupon = new WC_Coupon( $value );
							$currenttime = time();
							if ( $woo_ver < '3.0.0' ) {
								$expiry_date_timestamp = $the_coupon->expiry_date;
								if ( is_string( $expiry_date_timestamp ) ) {
									if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
										if ( $selected_date == 'd/m/Y' ) {
											$expiry_date_timestamp = str_replace( '/', '-', $expiry_date_timestamp );
										}
									}
									$expiry_date_timestamp = strtotime( $expiry_date_timestamp );
								}
								$couponamont = $the_coupon->coupon_amount;
							} else {
								$expiry_date_timestamp = $the_coupon->get_date_expires();
								if ( isset( $expiry_date_timestamp ) && ! empty( $expiry_date_timestamp ) ) {
									
									$expiry_date_timestamp = date_format( $expiry_date_timestamp, 'Y-m-d' );
									$expiry_date_timestamp = strtotime( $expiry_date_timestamp );
								}
								$couponamont = $the_coupon->get_amount();
							}
							if ( empty( $expiry_date_timestamp ) ) {
								$expirydate_format = __( 'No Expiration', 'woocommerce-ultimate-gift-card' );
							} else {
								$expirydate = date_i18n( 'Y-m-d', $expiry_date_timestamp );
								$expirydate_format = date_create( $expirydate );
								$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format_1', false );
								if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
									$expirydate_format = date_i18n( $selected_date, $expiry_date_timestamp );
									

								} else {
									$expirydate_format = date_format( $expirydate_format, 'jS M Y' );
								}
								if ( $currenttime > $expiry_date_timestamp ) {
									$response['result'] = false;
									$response['message'] = __( 'Your Giftcard Coupon is expired.', 'woocommerce-ultimate-gift-card' );
									echo json_encode( $response );
									die;
								}
							}
							$mwb_wgm_pricing = get_post_meta( $product->get_id(), 'mwb_wgm_pricing', true );
							$templateid = $mwb_wgm_pricing['template'];
							if ( is_array( $templateid ) && array_key_exists( 0, $templateid ) ) {
								$temp = $templateid[0];
							} else {
								$temp = $templateid;
							}
							$args['from'] = $from;
							$args['to'] = isset( $to_name ) ? $to_name : $to;
							$args['message'] = stripcslashes( $gift_msg );
							$args['coupon'] = apply_filters( 'mwb_wgm_qrcode_coupon', $value );
							$args['expirydate'] = $expirydate_format;
							$args['amount'] = wc_price( $couponamont );
							$args['templateid'] = isset( $selected_template ) && ! empty( $selected_template ) ? $selected_template : $temp;
							$args['product_id'] = $product_id;
							$args['order_id'] = $order_id;

							$browse_enable = get_option( 'mwb_wgm_other_setting_browse', false );
							if ( $browse_enable == 'on' ) {
								if ( $gift_img_name != '' ) {
									$args['browse_image'] = $gift_img_name;
								}
							}
							// Update the array according to the Customized Addon
							$updated_arr = apply_filters( 'mwb_wgm_resend_mail_arr_update', $args, $item );
							$mwb_wgm_object = new MWB_WGM_Card_Product_Function();
							$message = apply_filters( 'mwb_wgm_customized_email_cgc', $mwb_wgm_object->mwb_wgm_giftttemplate( $args ), $updated_arr );
							$mwb_wgm_pdf_enable = get_option( 'mwb_wgm_addition_pdf_enable', false );
							if ( isset( $mwb_wgm_pdf_enable ) && $mwb_wgm_pdf_enable == 'on' ) {
								$site_name = $_SERVER['SERVER_NAME'];
								$time = time();
								$this->mwb_wgm_attached_pdf( $message, $site_name, $time, $order_id, $value );
								$attachments = array( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $time . $site_name . '.pdf' );
							} else {
								$attachments = array();
							}
							$get_mail_status = true;
							$get_mail_status = apply_filters( 'mwb_send_mail_status', $get_mail_status );
							if ( $get_mail_status ) {
								if ( isset( $delivery_method ) && $delivery_method == 'Mail to recipient' ) {
									$subject = get_option( 'mwb_wgm_other_setting_giftcard_subject', false );
								}
								if ( isset( $delivery_method ) && $delivery_method == 'Downloadable' ) {
									$subject = get_option( 'mwb_wgm_other_setting_giftcard_subject_downloadable', false );
								}
								if ( isset( $delivery_method ) && $delivery_method == 'Shipping' ) {
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
								$subject = html_entity_decode( $subject, ENT_QUOTES, 'UTF-8' );
								$mwb_wgc_bcc_enable = get_option( 'mwb_wgm_addition_bcc_option_enable', false );
								if ( isset( $delivery_method ) ) {
									if ( $delivery_method == 'Mail to recipient' ) {
										$woo_ver = WC()->version;
										if ( $woo_ver < '3.0.0' ) {
											$from = $order->billing_email;
										} else {
											$from = $order->get_billing_email();
										}
									}
									if ( $delivery_method == 'Downloadable' ) {
										$woo_ver = WC()->version;
										if ( $woo_ver < '3.0.0' ) {
											$to = $order->billing_email;
											$from = $order->billing_email;
										} else {
											$to = $order->get_billing_email();
											$from = $order->get_billing_email();
										}
									}
									if ( $delivery_method == 'Shipping' ) {
										$admin_email = get_option( 'admin_email' );
										$alternate_email = ! empty( $mwb_wgm_change_admin_email_for_shipping ) ? $mwb_wgm_change_admin_email_for_shipping : $admin_email;
										$to = $alternate_email;
									}
								}
								if ( isset( $mwb_wgc_bcc_enable ) && $mwb_wgc_bcc_enable == 'on' ) {
									$headers[] = 'Bcc:' . $from;
									wc_mail( $to, $subject, $message, $headers, $attachments );
									do_action( 'mwb_wgm_mail_send_to_someone', $subject, $message, $attachments );
									if ( ! empty( $time ) && ! empty( $site_name ) ) {
										unlink( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $time . $site_name . '.pdf' );
									}
								} else {
									$headers = array( 'Content-Type: text/html; charset=UTF-8' );
									wc_mail( $to, $subject, $message, $headers, $attachments );
									do_action( 'mwb_wgm_mail_send_to_someone', $subject, $message, $attachments );
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
								$mwb_wgm_disable_buyer_notification = get_option( 'mwb_wgm_disable_buyer_notification', 'off' );
								if ( $mwb_wgm_disable_buyer_notification == 'off' ) {
									wc_mail( $from, $subject, $message );
								}
							}
							$response['result'] = true;
							$response['message'] = __( 'Email successfully sent', 'woocommerce-ultimate-gift-card' );
						}
					}
				}
			}
			echo json_encode( $response );
			die;
		}
		/**
		 * This function is used to add meta box on order detail page
		 *
		 * @name mwb_wgm_add_meta_boxes
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_add_meta_boxes( $post_type, $post ) {
			$woo_ver = WC()->version;
			global $post;
			if ( isset( $post->ID ) && $post->post_type == 'shop_order' ) {
				$order_id = $post->ID;
				$order = new WC_Order( $order_id );
				$order_status = $order->get_status();

				if ( $order_status == 'completed' || $order_status == 'processing' ) {
					$giftcard = false;
					foreach ( $order->get_items() as $item_id => $item ) {

						if ( $woo_ver < '3.0.0' ) {
							$_product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );

						} else {
							$_product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );

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
					}

					if ( $giftcard ) {
						add_meta_box( 'mwb_wgm_resend_mail', __( 'Resend Giftcard Mail', 'woocommerce-ultimate-gift-card' ), array( $this, 'mwb_wgm_resend_mail' ), 'shop_order' );
						add_meta_box( 'mwb_wgm_resend_coupon_add_more', __( 'Resend Giftcard by changing amount', 'woocommerce-ultimate-gift-card' ), array( $this, 'mwb_wgm_resend_coupon_add_more' ), 'shop_order' );
						add_meta_box( 'mwb_wgm_edit_email_address', __( 'Edit Email Address', 'woocommerce-ultimate-gift-card' ), array( $this, 'mwb_wgm_edit_email_address' ), 'shop_order' );

					}
				}
			}
		}
		/**
		 * This is used to add html for adding more amount to coupon
		 *
		 * @name mwb_wgm_resend_coupon_add_more
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_resend_coupon_add_more() {
			global $post;
			$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format_1', false );
			if ( isset( $post->ID ) ) {
				$order_id = $post->ID;
				$order = wc_get_order( $order_id );
				$select_coupon = array();
				$woo_ver = WC()->version;
				foreach ( $order->get_items() as $item_id => $item ) {
					if ( $woo_ver < '3.0.0' ) {
						$product = $order->get_product_from_item( $item );
						$product_title = $product->post->post_title;
						$product_id = $product->id;
					} else {
						$product = $item->get_product();
						$product_title = $product->get_name();
						$product_id = $product->get_id();
					}
					$giftcoupon = get_post_meta( $order_id, "$order_id#$item_id", true );
					if ( empty( $giftcoupon ) ) {
						$giftcoupon = get_post_meta( $order_id, "$order_id#$product_id", true );
					}
					if ( is_array( $giftcoupon ) && ! empty( $giftcoupon ) ) {
						foreach ( $giftcoupon as $key => $value ) {
							$coupon = new WC_Coupon( $value );
							$today = date_i18n( 'Y-m-d' );
							$today = strtotime( $today );
							if ( $woo_ver < '3.0.0' ) {
								$coupon_expiry = $coupon->expiry_date;
								if ( is_string( $coupon_expiry ) ) {
									if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
										if ( $selected_date == 'd/m/Y' ) {
											$coupon_expiry = str_replace( '/', '-', $coupon_expiry );
										}
									}
									$coupon_expiry = strtotime( $coupon_expiry );
								}
								if ( $coupon_expiry == null || $today < $coupon_expiry ) {

									if ( isset( $coupon->usage_count ) && $coupon->usage_count == null && $coupon->usage_count == '' && $coupon->usage_count < 1 ) {
										$select_coupon[ $product_title . '#mwb#' . $value . '#mwb#' . $item_id ] = $product_title . '#mwb#' . $value;
									}
								}
							} else {
								$coupon_expiry = $coupon->get_date_expires();

								if ( isset( $coupon_expiry ) && ! empty( $coupon_expiry ) ) {
									$coupon_expiry = date_format( $coupon_expiry, 'Y-m-d' );
									$coupon_expiry = strtotime( $coupon_expiry );
								}

								if ( $coupon_expiry == null || $today < $coupon_expiry ) {
									$usage_count = $coupon->get_usage_count();
									if ( isset( $usage_count ) && $usage_count == null && $usage_count == '' && $usage_count < 1 ) {
										$select_coupon[ $product_title . '#mwb#' . $value . '#mwb#' . $item_id ] = $product_title . '#mwb#' . $value;
									}
								}
							}
						}
					}
				}
				if ( ! empty( $select_coupon ) ) {
					?>
					<div id="mwb_wgm_loader" style="display: none;">
						<img src="<?php echo MWB_WGM_URL; ?>/assets/images/loading.gif">
					</div>
					<p><?php _e( 'You can resend the Gift Card Coupon by increasing its amount.', 'woocommerce-ultimate-gift-card' ); ?> </p>
					<p id="mwb_wgm_resend_coupon_amount_msg"></p>
					<table class="form-table">
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="mwb_select_coupon_product"><?php _e( 'Select the product', 'woocommerce-ultimate-gift-card' ); ?>
							</label>
						</th>
						<td class="forminp forminp-text">
							<?php
							$attribute_description = __( 'Select the product coupon for changing the amount', 'woocommerce-ultimate-gift-card' );
							echo wc_help_tip( $attribute_description );
							?>

							<select multiple="multiple" id="mwb_select_coupon_product" data-placeholder="<?php _e( 'Select Coupons', 'woocommerce-ultimate-gift-card' ); ?>" class="mwb_select_coupon_product wc-enhanced-select">
								<?php
								foreach ( $select_coupon as $key => $val ) {
									echo '<option value="' . esc_attr( $key ) . '">' . $val . '</option>';
								}

								?>
												
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="mwb_inc_amount"><?php _e( 'Enter the price', 'woocommerce-ultimate-gift-card' ); ?>
						</label>
					</th>
					<td class="forminp forminp-text">
						<?php
						$attribute_description = __( 'Enter the new amount of the coupon.', 'woocommerce-ultimate-gift-card' );
						echo wc_help_tip( $attribute_description );
						?>
						<input class="wc_input_price" style="" id="mwb_inc_amount" value="" placeholder="" type="text">
					</td>
				</tr>
				<tr valign="top">
					<td class="forminp forminp-text">
						<label for="mwb_inc_amount">
							<a href="javascript:void(0)" class="button" id="mwb_inc_money_coupon" data-id="<?php echo $order_id; ?>"><?php _e( 'Change amount and send mail', 'woocommerce-ultimate-gift-card' ); ?></a>
						</label>
					</td>
				</tr>							
			</table>
					<?php
				}
			}
		}
		/**
		 * This function is used to add resend email button on order detal page
		 *
		 * @name mwb_wgm_resend_mail
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_resend_mail() {
			global $post;
			if ( isset( $post->ID ) ) {
				$order_id = $post->ID;
				?>
				<div id="mwb_wgm_loader" style="display: none;">
					<img src="<?php echo MWB_WGM_URL; ?>/assets/images/loading.gif">
				</div>
				<p><?php _e( 'If user is not received giftcard email then resend mail.', 'woocommerce-ultimate-gift-card' ); ?> </p>
				<p id="mwb_wgm_resend_mail_notification"></p>
				<input type="button" data-id="<?php echo $order_id; ?>" id="mwb_wgm_resend_mail_button" class="button button-primary" value="<?php _e( 'Resend Mail', 'woocommerce-ultimate-gift-card' ); ?>">
				<?php
			}
		}


		/**
		 * This function is used show coupon code on order item
		 *
		 * @name mwb_wgm_woocommerce_after_order_itemmeta
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_woocommerce_after_order_itemmeta( $item_id, $item, $_product ) {
			$mwb_wgm_enable = mwb_wgm_giftcard_enable();
			if ( $mwb_wgm_enable ) {
				if ( isset( $_GET['post'] ) ) {
					$order_id = $_GET['post'];
					$order = new WC_Order( $order_id );
					$order_status = $order->get_status();

					if ( $order_status == 'completed' || $order_status == 'processing' ) {

						if ( $_product != null ) {
							$product_id = $_product->get_id();

							if ( isset( $product_id ) && ! empty( $product_id ) ) {

								$product_types = wp_get_object_terms( $product_id, 'product_type' );

								if ( isset( $product_types[0] ) ) {

									$product_type = $product_types[0]->slug;

									if ( $product_type == 'wgm_gift_card' ) {

										$giftcoupon = get_post_meta( $order_id, "$order_id#$item_id", true );
										if ( empty( $giftcoupon ) ) {
											$giftcoupon = get_post_meta( $order_id, "$order_id#$product_id", true );
										}

										if ( is_array( $giftcoupon ) && ! empty( $giftcoupon ) ) {
											?>

											<p style="margin:0;"><b><?php _e( 'Gift Coupon', 'woocommerce-ultimate-gift-card' ); ?> :</b>
												<?php
												foreach ( $giftcoupon as $key => $value ) {
													?>
													<span style="background: rgb(0, 115, 170) none repeat scroll 0% 0%; color: white; padding: 1px 5px 1px 6px; font-weight: bolder; margin-left: 10px;"><?php echo $value; ?></span>
													<?php
												}
												?>
											</p>
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

		/**
		 * Create custom post name Giftcard for creating Giftcard Template
		 *
		 * @name mwb_wgm_giftcard_custompost
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_giftcard_custompost() {
			$labels = array(
				'name'               => __( 'Gift Cards', 'post type general name', 'woocommerce-ultimate-gift-card' ),
				'singular_name'      => __( 'Gift Card', 'post type singular name', 'woocommerce-ultimate-gift-card' ),
				'menu_name'          => __( 'Gift Cards', 'admin menu', 'woocommerce-ultimate-gift-card' ),
				'name_admin_bar'     => __( 'Gift Card', 'add new on admin bar', 'woocommerce-ultimate-gift-card' ),
				'add_new'            => __( 'Add New', 'woocommerce-ultimate-gift-card' ),
				'add_new_item'       => __( 'Add New Gift Card', 'woocommerce-ultimate-gift-card' ),
				'new_item'           => __( 'New Gift Card', 'woocommerce-ultimate-gift-card' ),
				'edit_item'          => __( 'Edit Gift Card', 'woocommerce-ultimate-gift-card' ),
				'view_item'          => __( 'View Gift Card', 'woocommerce-ultimate-gift-card' ),
				'all_items'          => __( 'All Gift Cards', 'woocommerce-ultimate-gift-card' ),
				'search_items'       => __( 'Search Gift Cards', 'woocommerce-ultimate-gift-card' ),
				'parent_item_colon'  => __( 'Parent Gift Cards:', 'woocommerce-ultimate-gift-card' ),
				'not_found'          => __( 'No giftcards found.', 'woocommerce-ultimate-gift-card' ),
				'not_found_in_trash' => __( 'No giftcards found in Trash.', 'woocommerce-ultimate-gift-card' ),
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Description.', 'woocommerce-ultimate-gift-card' ),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'giftcard' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title', 'editor', 'thumbnail' ),
			);

			register_post_type( 'giftcard', $args );
		}

		/**
		 * This function is used to show tabs for giftcard product on product section
		 *
		 * @param $tabs
		 * @return string
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_woocommerce_product_data_tabs( $tabs ) {
			foreach ( $tabs as $key => $tab ) {
				if ( $key != 'general' && $key != 'advanced' && $key != 'shipping' && $key != 'attribute' && $key != 'inventory' ) {
					$tabs[ $key ]['class'][] = 'hide_if_wgm_gift_card';

				}
				if ( $key == 'inventory' ) {
					$tabs[ $key ]['class'][] = 'show_if_wgm_gift_card';
				}
			}
			return $tabs;
		}
		/**
		 * This function is to add meta field like field for instruction how to use shortcode in email template
		 *
		 * @name mwb_wgm_edit_form_after_title
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_edit_form_after_title( $post ) {
			$giftcard_posttype = get_post_type( $post );
			if ( $giftcard_posttype == 'giftcard' ) {
				?>
				<div class="postbox" id="mwb_wgm_mail_instruction" style="display: block;">
					<h2 class="hndle"><span><?php _e( 'Instruction for using Shortcode', 'woocommerce-ultimate-gift-card' ); ?></span></h2>
					<div class="inside">
						<table  class="form-table">
							<tr>
								<th><?php _e( 'SHORTCODE', 'woocommerce-ultimate-gift-card' ); ?></th>
								<th><?php _e( 'DESCRIPTION.', 'woocommerce-ultimate-gift-card' ); ?></th>			
							</tr>
							<tr>
								<td>[LOGO]</td>
								<td><?php _e( 'Replace with logo of company on email template.', 'woocommerce-ultimate-gift-card' ); ?></td>			
							</tr>
							<tr>
								<td>[TO]</td>
								<td><?php _e( 'Replace with email of user to which giftcard send.', 'woocommerce-ultimate-gift-card' ); ?></td>
							</tr>
							<tr>
								<td>[FROM]</td>
								<td><?php _e( 'Replace with name of the user who send the giftcard.', 'woocommerce-ultimate-gift-card' ); ?></td>
							</tr>
							<tr>
								<td>[FROMEMAIL]</td>
								<td><?php _e( 'Replace with email of the user who send the giftcard.', 'woocommerce-ultimate-gift-card' ); ?></td>
							</tr>
							<tr>
								<td>[MESSAGE]</td>
								<td><?php _e( 'Replace with Message of user who send the giftcard.', 'woocommerce-ultimate-gift-card' ); ?></td>
							</tr>
							<tr>
								<td>[AMOUNT]</td>
								<td><?php _e( 'Replace with Giftcard Amount.', 'woocommerce-ultimate-gift-card' ); ?></td>
							</tr>
							<tr>
								<td>[COUPON]</td>
								<td><?php _e( 'Replace with Giftcard Coupon Code.', 'woocommerce-ultimate-gift-card' ); ?></td>
							</tr>
							<tr>
								<td>[DEFAULTEVENT]</td>
								<td><?php _e( 'Replace with Default event image set on Setting.', 'woocommerce-ultimate-gift-card' ); ?></td>
							</tr>
							<tr>
								<td>[EXPIRYDATE]</td>
								<td><?php _e( 'Replace with Giftcard Expiry Date.', 'woocommerce-ultimate-gift-card' ); ?></td>
							</tr>
							<tr>
								<td>[DISCLAIMER]</td>
								<td><?php _e( 'Replace with Disclaimer on Giftcard.', 'woocommerce-ultimate-gift-card' ); ?></td>
							</tr>
							<tr>
								<td>[FEATUREDIMAGE]</td>
								<td><?php _e( 'Replace with Featured Image on Giftcard.', 'woocommerce-ultimate-gift-card' ); ?></td>
							</tr>
							<tr>
								<td>[PRODUCTNAME]</td>
								<td><?php _e( 'Replaced with Product Name having the link also', 'woocommerce-ultimate-gift-card' ); ?></td>
							</tr>
							<tr>
								<td>[ORDERID]</td>
								<td><?php _e( 'Replaced with Order ID', 'woocommerce-ultimate-gift-card' ); ?></td>
							</tr>
							
							<?php
							do_action( 'mwb_wgm_custom_shortcode' );
							?>
						</table>
					</div>
				</div>
				<?php
			}
		}

		/**
		 * This function is used to add setting submenu under woocommerce
		 *
		 * @name mwb_wgm_admin_menu
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_admin_menu() {
			// Remove the www from the Host Name
			$host_server = $_SERVER['HTTP_HOST'];
			if ( strpos( $host_server, 'www.' ) == 0 ) {

				$host_server = str_replace( 'www.', '', $host_server );
			}

			// get previous key exist before 2.4.6
			$mwb_wgm_license_hash_pre = get_option( 'mwb_wgm_license_hash' );
			$mwb_wgm_license_key_pre = get_option( 'mwb_wgm_license_key' );
			// fetch the keys related to the version
			$mwb_wgm_license_hash_saved = get_option( 'mwb_wgm_license_hash' . $host_server, $mwb_wgm_license_hash_pre );
			$mwb_wgm_license_key = get_option( 'mwb_wgm_license_key' . $host_server, $mwb_wgm_license_key_pre );
			$mwb_wgm_license_plugin = get_option( 'mwb_wgm_plugin_name' );
			$mwb_wgm_hash_today = md5( $host_server . $mwb_wgm_license_plugin . $mwb_wgm_license_key );
			$mwb_wgm_activated_time = get_option( 'mwb_wgm_activation_date_time', false );
			if ( ! $mwb_wgm_activated_time ) {
				$mwb_wgm_currenttime = current_time( 'timestamp' );
				update_option( 'mwb_wgm_activation_date_time', $mwb_wgm_currenttime );
				$mwb_wgm_activated_time = $mwb_wgm_currenttime;
			}
			$mwb_wgm_after_month = strtotime( '+30 days', $mwb_wgm_activated_time );
			$mwb_wgm_currenttime = current_time( 'timestamp' );
			if ( $mwb_wgm_license_hash_saved == $mwb_wgm_hash_today ) {
				add_submenu_page( 'woocommerce', __( 'WooCommerce Gift Manager', 'woocommerce-ultimate-gift-card' ), __( 'Gift Manager', 'woocommerce-ultimate-gift-card' ), 'manage_options', 'mwb-wgc-setting', array( $this, 'mwb_wgm_admin_setting' ) );

			} elseif ( ( $mwb_wgm_license_hash_saved == '' || $mwb_wgm_license_key == '' ) && ( $mwb_wgm_after_month > $mwb_wgm_currenttime ) ) {
				add_submenu_page( 'woocommerce', __( 'WooCommerce Gift Manager', 'woocommerce-ultimate-gift-card' ), __( 'Gift Manager', 'woocommerce-ultimate-gift-card' ), 'manage_options', 'mwb-wgc-setting', array( $this, 'mwb_wgm_admin_setting' ) );
			} else {
				delete_option( 'mwb_wgm_general_setting_enable' );
				add_submenu_page( 'woocommerce', __( 'WooCommerce Gift Manager', 'woocommerce-ultimate-gift-card' ), __( 'Gift Manager License', 'woocommerce-ultimate-gift-card' ), 'manage_options', 'mwb-wgc-setting', array( $this, 'mwb_wgm_admin_setting_activation' ) );
			}
		}

		/**
		 * This function is used to list all this setting at single page
		 *
		 * @name mwb_wgm_admin_setting
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_admin_setting() {
			include_once MWB_WGM_DIRPATH . '/admin/woocommerce-ultimate-gift-card-setting.php';
		}

		/**
		 * This function is used to give you activation panel if you haven't verified your purchase code with us.
		 *
		 * @name mwb_wgm_admin_setting
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_admin_setting_activation() {
			include_once MWB_WGM_DIRPATH . '/Shipping/admin/license-setting.php';
		}

		/**
		 * This function is to add custom product type 'Giftcard' in woocommerce
		 *
		 * @name mwb_wgm_gift_card_product
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 * @param array $types
		 * @return $types
		 */
		function mwb_wgm_gift_card_product( $types ) {
			$mwb_wgm_enable = mwb_wgm_giftcard_enable();
			if ( $mwb_wgm_enable ) {
				$types['wgm_gift_card'] = __( 'Gift Card', 'woocommerce-ultimate-gift-card' );
			}
			return $types;
		}

		/**
		 * This function is to add field for gift card product type product
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 * @name mwb_wgm_woocommerce_product_options_general_product_data
		 */

		function mwb_wgm_woocommerce_product_options_general_product_data() {
			global $post;
			$product_id = $post->ID;
			$mwb_wgm_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );
			$mwb_wgm_exclude_per_product = get_post_meta( $product_id, 'mwb_wgm_exclude_per_product', true );
			$mwb_wgm_exclude_per_category = get_post_meta( $product_id, 'mwb_wgm_exclude_per_category', array() );

			$mwb_wgm_include_per_product = get_post_meta( $product_id, 'mwb_wgm_include_per_product', true );
			$mwb_wgm_include_per_category = get_post_meta( $product_id, 'mwb_wgm_include_per_category', array() );

			$discount_enable = get_option( 'mwb_wgm_discount_enable', false );
			$selected_pricing = isset( $mwb_wgm_pricing['type'] ) ? $mwb_wgm_pricing['type'] : false;
			$giftcard_enable = get_option( 'mwb_wgm_general_setting_enable', false );
			$default_price = '';
			$from = '';
			$to = '';
			$price = '';
			$default_price  = isset( $mwb_wgm_pricing['default_price'] ) ? $mwb_wgm_pricing['default_price'] : 0;
			$selectedtemplate  = isset( $mwb_wgm_pricing['template'] ) ? $mwb_wgm_pricing['template'] : false;
			$default_selected = isset( $mwb_wgm_pricing['by_default_tem'] ) ? $mwb_wgm_pricing['by_default_tem'] : false;
			if ( $selected_pricing ) {
				switch ( $selected_pricing ) {
					case 'mwb_wgm_range_price':
						$from = isset( $mwb_wgm_pricing['from'] ) ? $mwb_wgm_pricing['from'] : 0;
						$to = isset( $mwb_wgm_pricing['to'] ) ? $mwb_wgm_pricing['to'] : 0;
						break;

					case 'mwb_wgm_selected_price':
						$price = isset( $mwb_wgm_pricing['price'] ) ? $mwb_wgm_pricing['price'] : 0;
						break;

					default:
						// nothing for default
				}
			}

			if ( $giftcard_enable == 'on' ) {
				echo '<div class="options_group show_if_wgm_gift_card"><div id="mwb_wgm_loader" style="display: none;">
				<img src="' . MWB_WGM_URL . '/assets/images/loading.gif">
				</div>';

				$previous_post = $post;
				$post = $previous_post;
				woocommerce_wp_text_input(
					array(
						'id' => 'mwb_wgm_default',
						'value' => "$default_price",
						'label' => __( 'Default Price', 'woocommerce-ultimate-gift-card' ),
						'placeholder' => wc_format_localized_price( 0 ),
						'description' => __( 'Gift card default price.', 'woocommerce-ultimate-gift-card' ),
						'data_type' => 'price',
						'desc_tip' => true,
					)
				);
				woocommerce_wp_select(
					array(
						'id' => 'mwb_wgm_pricing',
						'value' => "$selected_pricing",
						'label' => __( 'Pricing type', 'woocommerce-ultimate-gift-card' ),
						'options' => $this->mwb_wgm_get_pricing_type(),
					)
				);

				// Range Price.
				// StartFrom.
				woocommerce_wp_text_input(
					array(
						'id' => 'mwb_wgm_from_price',
						'value' => "$from",
						'label' => __( 'From Price', 'woocommerce-ultimate-gift-card' ),
						'placeholder' => wc_format_localized_price( 0 ),
						'description' => __( 'Gift card price range start from.', 'woocommerce-ultimate-gift-card' ),
						'data_type' => 'price',
						'desc_tip' => true,
					)
				);
				// EndTo
				woocommerce_wp_text_input(
					array(
						'id' => 'mwb_wgm_to_price',
						'value' => "$to",
						'label' => __( 'To Price', 'woocommerce-ultimate-gift-card' ),
						'placeholder' => wc_format_localized_price( 0 ),
						'description' => __( 'Gift card price range end to.', 'woocommerce-ultimate-gift-card' ),
						'data_type' => 'price',
						'desc_tip' => true,
					)
				);

				// Selected Price.
				woocommerce_wp_textarea_input(
					array(
						'id' => 'mwb_wgm_selected_price',
						'value' => "$price",
						'label' => __( 'Price', 'woocommerce-ultimate-gift-card' ),
						'desc_tip' => 'true',
						'description' => __( 'Enter an price using seperator |. Ex : (10 | 20)', 'woocommerce-ultimate-gift-card' ),
						'placeholder' => '10|20|30',
					)
				);

				// variable price.
				$variable_price_text = isset( $mwb_wgm_pricing['mwb_wgm_variation_text'] ) ? $mwb_wgm_pricing['mwb_wgm_variation_text'] : array();
				$variable_price_amt = isset( $mwb_wgm_pricing['mwb_wgm_variation_price'] ) ? $mwb_wgm_pricing['mwb_wgm_variation_price'] : array();
				?>
				<div id="mwb_variable_gift">
					<div class="mwb_variable_desc">
						<span><?php esc_html_e( 'Description', 'woocommerce-ultimate-gift-card' ); ?></span>
						<span><?php esc_html_e( 'Price', 'woocommerce-ultimate-gift-card' ); ?></span>
					</div>
					<?php
					if ( is_array( $variable_price_amt ) && empty( $variable_price_amt ) && count( $variable_price_amt ) == 0 ) {
						?>
						<div class="mwb_wgm_variation_giftcard">
							<input type="text" name="mwb_wgm_variation_text[]" placeholder="Enter Description" value="">
							<input type="text" class="mwb_wgm_variation_price wc_input_price" name="mwb_wgm_variation_price[]" placeholder="Enter Price" value="">
						</div>
						<?php
					} else {
						if ( is_array( $variable_price_amt ) && is_array( $variable_price_text ) && ! empty( $variable_price_amt ) && ! empty( $variable_price_text ) && count( $variable_price_amt ) >= 1 ) {
							foreach ( $variable_price_amt as $key => $value ) {
								?>
								<div class="mwb_wgm_variation_giftcard">
									<input type="text" name="mwb_wgm_variation_text[]" value="<?php echo $variable_price_text[ $key ]; ?>">
									<input type="text" class=" mwb_wgm_variation_price wc_input_price" name="mwb_wgm_variation_price[]" value="<?php echo $value; ?>">
									<?php if ( $key > 0 ) { ?>
									<a class="mwb_remove_more_price button" href="javascript:void(0)"><?php esc_html_e( 'Remove', 'woocommerce-ultimate-gift-card' ); ?></a>
								<?php } ?>
								</div>
								<?php
							}
						}
					}
					?>
					<a href="#" class="mwb_add_more_price button"><?php esc_html_e( 'Add', 'woocommerce-ultimate-gift-card' ); ?></a>
				</div>
				<?php
				// Regular Price.
				echo '<p class="form-field mwb_wgm_default_price_field">
				<label for="mwb_wgm_default_price"><b>' . __( 'Instruction', 'woocommerce-ultimate-gift-card' ) . '</b></label>
				<span class="description">' . __( 'WooCommerce Product regular price is used as a gift card price.', 'woocommerce-ultimate-gift-card' ) . '</span>
				</p>';
				// User Price.
				echo '<p class="form-field mwb_wgm_user_price_field ">
				<label for="mwb_wgm_user_price"><b>' . __( 'Instruction', 'woocommerce-ultimate-gift-card' ) . '</b></label>
				<span class="description">' . __( 'User can purchase any amount of Gift Card.', 'woocommerce-ultimate-gift-card' ) . '</span>
				</p>';
				if ( isset( $discount_enable ) && $discount_enable == 'on' ) {
					woocommerce_wp_checkbox(
						array(
							'id' => 'mwb_wgm_discount',
							'class' => 'mwb_wgm_discount',
							'label' => __( 'Give Discount ?', 'woocommerce-ultimate-gift-card' ),
							'name' => 'mwb_wgm_discount',
						)
					);
				}
				$is_customizable = get_post_meta( $product_id, 'woocommerce_customizable_gift_card', true );
				if ( empty( $is_customizable ) ) {
					?>
					<p class="form-field mwb_wgm_email_template">
						<label class = "mwb_wgm_email_template" for="mwb_wgm_email_template"><?php _e( 'Email Template', 'woocommerce-ultimate-gift-card' ); ?></label>
						<select id="mwb_wgm_email_template" multiple="multiple" name="mwb_wgm_email_template[]" class="mwb_wgm_email_template">
							<?php
							$args = array(
								'post_type' => 'giftcard',
								'posts_per_page' => -1,
							);
							$loop = new WP_Query( $args );
							$template = array();
							foreach ( $loop->posts as $key => $value ) {
								$template_id = $value->ID;

								$template_title = $value->post_title;
								$template[ $template_id ] = $template_title;
								$tempselect = '';
								if ( is_array( $selectedtemplate ) && $selectedtemplate != null && in_array( $template_id, $selectedtemplate ) ) {
									$tempselect = "selected='selected'";
								} else {
									if ( $template_id == $selectedtemplate ) {
										$tempselect = "selected='selected'";
									}
								}
								?>
								<option value="<?php echo $template_id; ?>"<?php echo $tempselect; ?>><?php echo $template_title; ?></option>
								<?php
							}
							?>
						</select>
					</p>
					<p class="form-field mwb_wgm_email_defualt_template">
						<label class = "mwb_wgm_email_defualt_template" for="mwb_wgm_email_defualt_template"><?php _e( 'Which template you want to be selected by default?', 'woocommerce-ultimate-gift-card' ); ?></label>

						<select id="mwb_wgm_email_defualt_template" name = "mwb_wgm_email_defualt_template" style="width: 50%">
							<?php

							if ( empty( $default_selected ) ) {
								?>
								<option value=""><?php _e( 'Select the template from above field', 'woocommerce-ultimate-gift-card' ); ?></option>
								<?php
							} elseif ( is_array( $selectedtemplate ) && ! empty( $selectedtemplate ) && ! empty( $default_selected ) ) {
								$args = array(
									'post_type' => 'giftcard',
									'post__in' => $selectedtemplate,
								);
								$loop = new WP_Query( $args );
								foreach ( $loop->posts as $key => $value ) {
									$template_id = $value->ID;
									$template_title = $value->post_title;
									$alreadyselected = '';
									if ( is_array( $selectedtemplate ) && in_array( $default_selected, $selectedtemplate ) && $default_selected == $template_id ) {
										$alreadyselected = " selected='selected'";
									}
									?>
									<option value="<?php echo $template_id; ?>"<?php echo $alreadyselected; ?>><?php echo $template_title; ?></option>
									<?php
								}
							}
							?>
						</select>
					</p>
					<?php
				}
				$woo_ver = WC()->version;
				if ( $woo_ver < '3.0.0' ) {
					?>
					<p class="form-field"><label><?php _e( 'Exclude Products', 'woocommerce-ultimate-gift-card' ); ?></label>
						<input type="hidden" class="wc-product-search" data-multiple="true" style="width: 50%;" name="mwb_wgm_exclude_per_product" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-ultimate-gift-card' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-selected="
																																													   <?php
																																														$product_ids = array_filter( array_map( 'absint', explode( ',', get_post_meta( $post->ID, 'mwb_wgm_exclude_per_product', true ) ) ) );
																																														$json_ids    = array();
																																														if ( isset( $product_ids ) && ! empty( $product_ids ) ) {
																																															foreach ( $product_ids as $product_id ) {
																																																$product = wc_get_product( $product_id );
																																																if ( is_object( $product ) ) {
																																																	$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
																																																}
																																															}
																																														}
																																														echo esc_attr( json_encode( $json_ids ) );
																																														?>
						" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" />
					</p>
					<?php
				} else {
					?>
					<p class="form-field mwb_wgm_exclude_per_product_field">
						<label class = "mwb_wgm_exclude_per_product" for="mwb_wgm_exclude_per_product"><?php _e( 'Exclude Products', 'woocommerce-ultimate-gift-card' ); ?></label>
						<select class="wc-product-search" multiple="multiple" style="width: 50%;" name="mwb_wgm_exclude_per_product[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-ultimate-gift-card' ); ?>" data-action="woocommerce_json_search_products_and_variations" id="mwb_wgm_exclude_per_product"> 
							<?php
							if ( isset( $mwb_wgm_exclude_per_product ) && ! empty( $mwb_wgm_exclude_per_product ) ) {
								foreach ( $mwb_wgm_exclude_per_product as $pro_id ) {
									$product      = wc_get_product( $pro_id );
									$product_title = $product->get_formatted_name();
									echo '<option value="' . esc_attr( $pro_id ) . '" selected="selected">' . esc_html( $product_title ) . '</option>';
								}
							}
							?>
						</select>
					</p>
					<?php
				}
				?>
				<p class="form-field mwb_wgm_exclude_per_category_field">
					<label class = "mwb_wgm_exclude_per_category" for="mwb_wgm_exclude_per_category"><?php _e( 'Exclude Category', 'woocommerce-ultimate-gift-card' ); ?></label>
					<select id="mwb_wgm_exclude_per_category" multiple="multiple" name="mwb_wgm_exclude_per_category[]">
						<?php
						$args = array( 'taxonomy' => 'product_cat' );
						$categories = get_terms( $args );
						if ( isset( $categories ) && ! empty( $categories ) ) {
							foreach ( $categories as $category ) {
								$catid = $category->term_id;
								$catname = $category->name;
								$catselect = '';
								if ( is_array( $mwb_wgm_exclude_per_category ) && ! empty( $mwb_wgm_exclude_per_category ) ) {
									if ( is_array( $mwb_wgm_exclude_per_category[0] ) && in_array( $catid, $mwb_wgm_exclude_per_category[0] ) ) {
										$catselect = "selected='selected'";
									}
								}
								?>
								<option value="<?php echo $catid; ?>" <?php echo $catselect; ?>><?php echo $catname; ?></option>
								<?php
							}
						}
						?>
					</select>
				</p>
				<?php

					// added code
				if ( $woo_ver < '3.0.0' ) {
					?>
					<p class="form-field"><label><?php _e( 'Include Products', 'woocommerce-ultimate-gift-card' ); ?></label>
						<input type="hidden" class="wc-product-search" data-multiple="true" style="width: 50%;" name="mwb_wgm_include_per_product" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-ultimate-gift-card' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-selected="
																																													   <?php
																																														$product_ids = array_filter( array_map( 'absint', explode( ',', get_post_meta( $post->ID, 'mwb_wgm_include_per_product', true ) ) ) );
																																														$json_ids    = array();
																																														if ( isset( $product_ids ) && ! empty( $product_ids ) ) {
																																															foreach ( $product_ids as $product_id ) {
																																																$product = wc_get_product( $product_id );
																																																if ( is_object( $product ) ) {
																																																	$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
																																																}
																																															}
																																														}
																																														echo esc_attr( json_encode( $json_ids ) );
																																														?>
						" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" />
					</p>
					<?php
				} else {
					?>
					<p class="form-field mwb_wgm_include_per_product_field">
						<label class = "mwb_wgm_include_per_product" for="mwb_wgm_include_per_product"><?php _e( 'Include Products', 'woocommerce-ultimate-gift-card' ); ?></label>
						<select class="wc-product-search" multiple="multiple" style="width: 50%;" name="mwb_wgm_include_per_product[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-ultimate-gift-card' ); ?>" data-action="woocommerce_json_search_products_and_variations" id="mwb_wgm_include_per_product"> 
							<?php
							if ( isset( $mwb_wgm_include_per_product ) && ! empty( $mwb_wgm_include_per_product ) ) {
								foreach ( $mwb_wgm_include_per_product as $pro_id ) {
									$product      = wc_get_product( $pro_id );
									$product_title = $product->get_formatted_name();
									echo '<option value="' . esc_attr( $pro_id ) . '" selected="selected">' . esc_html( $product_title ) . '</option>';
								}
							}
							?>
						</select>
					</p>
					<?php
				}
				?>
				<p class="form-field mwb_wgm_include_per_category_field">
					<label class = "mwb_wgm_include_per_category" for="mwb_wgm_include_per_category"><?php _e( 'Include Category', 'woocommerce-ultimate-gift-card' ); ?></label>
					<select id="mwb_wgm_include_per_category" multiple="multiple" name="mwb_wgm_include_per_category[]">
						<?php
						$args = array( 'taxonomy' => 'product_cat' );
						$categories = get_terms( $args );
						if ( isset( $categories ) && ! empty( $categories ) ) {
							foreach ( $categories as $category ) {
								$catid = $category->term_id;
								$catname = $category->name;
								$catselect = '';
								if ( is_array( $mwb_wgm_include_per_category ) && ! empty( $mwb_wgm_include_per_category ) ) {
									if ( is_array( $mwb_wgm_include_per_category[0] ) && in_array( $catid, $mwb_wgm_include_per_category[0] ) ) {
										$catselect = "selected='selected'";
									}
								}
								?>
								<option value="<?php echo $catid; ?>" <?php echo $catselect; ?>><?php echo $catname; ?></option>
								<?php
							}
						}
						?>
					</select>
				</p>

				<?php
				woocommerce_wp_checkbox(
					array(
						'id' => 'mwb_wgm_overwrite',
						'class' => 'mwb_wgm_overwrite',
						'label' => __( 'Overwrite Delivery', 'woocommerce-ultimate-gift-card' ),
						'name' => 'mwb_wgm_overwrite',
					)
				);
				woocommerce_wp_checkbox(
					array(
						'id' => 'mwb_wgm_email_to_recipient',
						'class' => 'mwb_wgm_email_to_recipient',
						'label' => __( 'Email To Recipient', 'woocommerce-ultimate-gift-card' ),
						'name' => 'mwb_wgm_email_to_recipient',
					)
				);
				woocommerce_wp_checkbox(
					array(
						'id' => 'mwb_wgm_download',
						'class' => 'mwb_wgm_download',
						'label' => __( 'Download', 'woocommerce-ultimate-gift-card' ),
						'name' => 'mwb_wgm_download',
					)
				);
				woocommerce_wp_checkbox(
					array(
						'id' => 'mwb_wgm_shipping',
						'class' => 'mwb_wgm_shipping',
						'label' => __( 'Shipping', 'woocommerce-ultimate-gift-card' ),
						'name' => 'mwb_wgm_shipping',
					)
				);

				do_action( 'mwb_wgm_giftcard_product_type_field' );
				echo '</div>';
			}
		}

		/**
		 * This function is used to add pricing type for giftcard
		 *
		 * @name mwb_wgm_get_pricing_type
		 * @return array
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_get_pricing_type() {
			$pricing_options = array(
				'mwb_wgm_default_price' => __( 'Default Price', 'woocommerce-ultimate-gift-card' ),
				'mwb_wgm_range_price' => __( 'Price Range', 'woocommerce-ultimate-gift-card' ),
				'mwb_wgm_selected_price' => __( 'Selected Price', 'woocommerce-ultimate-gift-card' ),
				'mwb_wgm_user_price' => __( 'User Price', 'woocommerce-ultimate-gift-card' ),
				'mwb_wgm_variable_price' => __( 'Variable Price', 'woocommerce-ultimate-gift-card' ),
			);

			return apply_filters( 'mwb_wgm_pricing_type', $pricing_options );
		}


		/**
		 * This function is used to enqueue js and css in admin
		 *
		 * @name mwb_wgm_admin_enqueue_scripts
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_admin_enqueue_scripts() {
			$screen = get_current_screen();
			if ( isset( $screen->id ) ) {
				$pagescreen = $screen->id;
				$locale  = localeconv();
				$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';
				$params = array(
					/* translators: %s: decimal */
					'i18n_decimal_error'                => sprintf( __( 'Please enter in decimal (%s) format without thousand separators.', 'woocommerce-ultimate-gift-card' ), $decimal ),
					/* translators: %s: price decimal separator */
					'i18n_mon_decimal_error'            => sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency symbols.', 'woocommerce-ultimate-gift-card' ), wc_get_price_decimal_separator() ),
					'i18n_country_iso_error'            => __( 'Please enter in country code with two capital letters.', 'woocommerce-ultimate-gift-card' ),
					'i18_sale_less_than_regular_error'  => __( 'Please enter in a value less than the regular price.', 'woocommerce-ultimate-gift-card' ),
					'decimal_point'                     => $decimal,
					'mon_decimal_point'                 => wc_get_price_decimal_separator(),
					'strings' => array(
						'import_products' => __( 'Import', 'woocommerce-ultimate-gift-card' ),
						'export_products' => __( 'Export', 'woocommerce-ultimate-gift-card' ),
					),
					'urls' => array(
						'import_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ),
						'export_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ),
					),
				);

				if ( $pagescreen == 'product' || $pagescreen == 'shop_order' ) {
					$jarray = array( 'jquery' );
					if ( $pagescreen == 'product' ) {
						$jarray = array( 'jquery' );
					}
					$giftcard_tax_cal_enable = get_option( 'mwb_wgm_general_setting_tax_cal_enable', 'off' );
					$mwb_wgm = array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
						'append_option_val' => __( 'Select the template from above field', 'woocommerce-ultimate-gift-card' ),
						'is_tax_enable_for_gift' => $giftcard_tax_cal_enable,
						'mwb_wgm_nonce' => wp_create_nonce( 'mwb-wgm-verify-nonce' ),
					);

					wp_register_script( 'mwb_wgm_product_script', MWB_WGM_URL . '/assets/js/woocommerce-ultimate-gift-card-product.js', $jarray );
					wp_localize_script( 'mwb_wgm_product_script', 'mwb_wgm', $mwb_wgm );
					wp_enqueue_script( 'mwb_wgm_product_script' );

					wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), WC_VERSION );
					wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );
					wp_enqueue_script( 'woocommerce_admin' );

				}
				if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'mwb-wgc-setting' ) || $pagescreen == 'edit-giftcard' ) {
					wp_enqueue_style( 'wp-color-picker' );
					wp_enqueue_style( 'thickbox' );
					wp_enqueue_script( 'thickbox' );
					wp_enqueue_script( 'jquery-ui-datepicker' );
					wp_enqueue_style( 'mwb_wgm_jquery-ui-datepicker', MWB_WGM_URL . 'assets/css/jquery-ui.css' );

					wp_register_script( 'mwb_gw_clipboard', MWB_WGM_URL . 'assets/js/clipboard.min.js' );

					wp_enqueue_script( 'mwb_gw_clipboard' );
					wp_enqueue_script( 'mwb_wgm_admin_select2_script', MWB_WGM_URL . '/assets/js/select2.min.js', array( 'jquery' ) );
					$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format', false );
					if ( ! isset( $selected_date ) || $selected_date == null || $selected_date == '' ) {
						$selected_date = 'yy/mm/dd';
					}
					if ($selected_date == 'd.m.Y') {
						$selected_date = 'd.m.yy';
					}
					$url = home_url( '/wp-admin/admin.php?page=mwb-wgc-setting' );
					$mwb_wgm = array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
						'dateformat' => $selected_date,
						'mwb_wgm_url' => $url,
						'mwb_wgm_nonce' => wp_create_nonce( 'mwb-wgm-verify-nonce' ),
					);
					wp_register_script( 'mwb_wgm_admin_script', MWB_WGM_URL . '/assets/js/woocommerce-ultimate-gift-card-admin.js', array( 'jquery', 'mwb_wgm_admin_select2_script', 'wp-color-picker', 'jquery-ui-datepicker', 'wc-enhanced-select' ) );
					wp_localize_script( 'mwb_wgm_admin_script', 'mwb_wgm', $mwb_wgm );
					wp_enqueue_script( 'mwb_wgm_admin_script' );
					wp_register_script( 'mwb_wgm_admin_thankyouorder_script', MWB_WGM_URL . '/assets/js/woocommerce-ultimate-giftcard-thankyou-order.js', array( 'jquery' ) );
					wp_enqueue_script( 'mwb_wgm_admin_thankyouorder_script' );
					wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
					wp_enqueue_style( 'woocommerce_admin_menu_styles' );
					wp_enqueue_style( 'woocommerce_admin_styles' );

					wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), WC_VERSION );
					wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.js', array( 'jquery' ), WC_VERSION, true );

					wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );
					wp_enqueue_script( 'woocommerce_admin' );
					wp_enqueue_style( 'mwb_wgm_admin_select2_css', MWB_WGM_URL . '/assets/css/select2.min.css' );

				}
				if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'mwb-wgc-setting' ) && isset( $_GET['tab'] ) && $_GET['tab'] == 'discount-tab' ) {
					wp_register_script( 'mwb_wgm_admin_discount_script', MWB_WGM_URL . '/assets/js/woocommerce-ultimate-gift-card-admin-discount.js', array( 'jquery' ) );
					wp_enqueue_script( 'mwb_wgm_admin_discount_script' );
					wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );
					wp_enqueue_script( 'woocommerce_admin' );
				}
				if ( $pagescreen == 'edit-giftcard' ) {
					wp_enqueue_style( 'thickbox' );
					wp_enqueue_script( 'thickbox' );
				}
				if ( $pagescreen != 'plugins' && ! is_cart() && ! is_checkout() && ! is_account_page() ) {
					wp_enqueue_style( 'mwb_wgm_admin_style', MWB_WGM_URL . 'assets/css/woocommerce-ultimate-gift-card-admin.css' );
					wp_enqueue_style( 'mwb_wgm_common_css', MWB_WGM_URL . 'assets/css/mwb_wgm_common.css' );
				}
				if ( $pagescreen == 'woocommerce_page_mwb-wgc-setting' && isset( $_GET['tab'] ) && $_GET['tab'] == 'addon' ) {
					wp_enqueue_style( 'mwb_addon', MWB_WGM_URL . 'assets/css/mwb_addon.css' );
				}
			}
			wp_enqueue_script( 'mwb_wgm_admin_script' );
			wp_enqueue_script( 'jquery-ui-sortable' );
		}
		/**
		 * This function is used to save custom product type data
		 *
		 * @name mwb_wgm_save_post
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wgm_save_post() {
			global $post;

			if ( isset( $post->ID ) ) {
				$product_id = $post->ID;
				if ( isset( $_POST['product-type'] ) ) {
					if ( $_POST['product-type'] == 'wgm_gift_card' ) {

						$mwb_wgm_categ_enable = get_option( 'mwb_wgm_general_setting_categ_enable', 'off' );

						if ( $mwb_wgm_categ_enable == 'off' ) {
							$term = __( 'Gift Card', 'woocommerce-ultimate-gift-card' );
							$taxonomy = 'product_cat';
							$term_exist = term_exists( $term, $taxonomy );
							if ( $term_exist == 0 || $term_exist == null ) {
								$args['slug'] = 'mwb_wgm_giftcard';
								$term_exist = wp_insert_term( $term, $taxonomy, $args );
							}

							wp_set_object_terms( $post->ID, $_POST['product-type'], 'product_type' );
							wp_set_post_terms( $product_id, $term_exist, $taxonomy );
						}
						$mwb_wgm_pricing = array();

						$selected_pricing = isset( $_POST['mwb_wgm_pricing'] ) ? $_POST['mwb_wgm_pricing'] : false;
						if ( $selected_pricing ) {
							$default_price = ! empty( $_POST['mwb_wgm_default'] ) ? $_POST['mwb_wgm_default'] : 0;
							update_post_meta( $product_id, '_regular_price', $default_price );
							update_post_meta( $product_id, '_price', $default_price );
							$mwb_wgm_pricing['default_price'] = $default_price;
							$mwb_wgm_pricing['type'] = $selected_pricing;
							if ( ! isset( $_POST['mwb_wgm_email_template'] ) || empty( $_POST['mwb_wgm_email_template'] ) ) {
								$args = array(
									'post_type' => 'giftcard',
									'posts_per_page' => -1,
								);
								$loop = new WP_Query( $args );
								$template = array();
								if ( $loop->have_posts() ) :
									while ( $loop->have_posts() ) :
										$loop->the_post();
										global $product;
										$template_id = $loop->post->ID;
										$template[] = $template_id;
									endwhile;
								endif;
								$mwb_wgm_pricing['template'] = $template[0];
							} else {
								$mwb_wgm_pricing['template'] = $_POST['mwb_wgm_email_template'];
							}
							$mwb_wgm_pricing['by_default_tem'] = isset( $_POST['mwb_wgm_email_defualt_template'] );

							switch ( $selected_pricing ) {
								case 'mwb_wgm_range_price':
									$from = isset( $_POST['mwb_wgm_from_price'] ) ? $_POST['mwb_wgm_from_price'] : 0;
									$to = isset( $_POST['mwb_wgm_to_price'] ) ? $_POST['mwb_wgm_to_price'] : 0;
									$mwb_wgm_pricing['type'] = $selected_pricing;
									$mwb_wgm_pricing['from'] = $from;
									$mwb_wgm_pricing['to'] = $to;
									break;

								case 'mwb_wgm_selected_price':
									$price = isset( $_POST['mwb_wgm_selected_price'] ) ? $_POST['mwb_wgm_selected_price'] : 0;
									$mwb_wgm_pricing['type'] = $selected_pricing;
									$mwb_wgm_pricing['price'] = $price;
									break;

								case 'mwb_wgm_user_price':
									$mwb_wgm_pricing['type'] = $selected_pricing;
									break;

								case 'mwb_wgm_variable_price':
									$mwb_wgm_pricing['mwb_wgm_variation_text'] = isset( $_POST['mwb_wgm_variation_text'] ) ? $_POST['mwb_wgm_variation_text'] : array();
									$mwb_wgm_pricing['mwb_wgm_variation_price'] = isset( $_POST['mwb_wgm_variation_price'] ) ? $_POST['mwb_wgm_variation_price'] : array();

								default:
									// nothing for default
							}
						}
						do_action( 'mwb_wgm_product_pricing', $mwb_wgm_pricing );
						$mwb_wgm_pricing = apply_filters( 'mwb_wgm_product_pricing', $mwb_wgm_pricing );
						update_post_meta( $product_id, 'mwb_wgm_pricing', $mwb_wgm_pricing );
						$is_overwrite = isset( $_POST['mwb_wgm_overwrite'] ) ? $_POST['mwb_wgm_overwrite'] : '';
						update_post_meta( $product_id, 'mwb_wgm_overwrite', $is_overwrite );
						if ( isset( $is_overwrite ) && ! empty( $is_overwrite ) ) {
							$mwb_wgm_email_to_recipient = $_POST['mwb_wgm_email_to_recipient'];
							$mwb_wgm_shipping = $_POST['mwb_wgm_shipping'];
							$mwb_wgm_download = $_POST['mwb_wgm_download'];

							if ( ! isset( $mwb_wgm_email_to_recipient ) && ! isset( $mwb_wgm_shipping ) && ! isset( $mwb_wgm_download ) ) {
								$mwb_wgm_email_to_recipient = 'yes';
							}

							update_post_meta( $product_id, 'mwb_wgm_email_to_recipient', $mwb_wgm_email_to_recipient );
							update_post_meta( $product_id, 'mwb_wgm_download', $mwb_wgm_download );
							update_post_meta( $product_id, 'mwb_wgm_shipping', $mwb_wgm_shipping );
						}

						$mwb_wgm_is_discount = isset( $_POST['mwb_wgm_discount'] ) ? $_POST['mwb_wgm_discount'] : 'no';
						if ( isset( $mwb_wgm_pricing['type'] ) ) {
							if ( $mwb_wgm_pricing['type'] == 'mwb_wgm_default_price' || $mwb_wgm_pricing['type'] == 'mwb_wgm_range_price' || $mwb_wgm_pricing['type'] == 'mwb_wgm_user_price' ) {
								update_post_meta( $product_id, 'mwb_wgm_discount', $mwb_wgm_is_discount );
							} else {
								$mwb_wgm_is_discount = 'no';
								update_post_meta( $product_id, 'mwb_wgm_discount', $mwb_wgm_is_discount );
							}
						}
						$mwb_wgm_exclude_per_product = array();
						$mwb_wgm_exclude_per_product = isset( $_POST['mwb_wgm_exclude_per_product'] ) ? $_POST['mwb_wgm_exclude_per_product'] : '';
						$giftcard_exclude_product_string = '';
						if ( isset( $mwb_wgm_exclude_per_product ) && ! empty( $mwb_wgm_exclude_per_product ) ) {

							foreach ( $mwb_wgm_exclude_per_product as $value ) {
								$giftcard_exclude_product_string .= $value . ',';
							}
							$giftcard_exclude_product_string = rtrim( $giftcard_exclude_product_string, ',' );
							update_post_meta( $product_id, 'mwb_wgm_exclude_per_pro_format', $giftcard_exclude_product_string );
							update_post_meta( $product_id, 'mwb_wgm_exclude_per_product', $mwb_wgm_exclude_per_product );
						} else {

							update_post_meta( $product_id, 'mwb_wgm_exclude_per_product', $mwb_wgm_exclude_per_product );
							update_post_meta( $product_id, 'mwb_wgm_exclude_per_pro_format', $giftcard_exclude_product_string );
						}

						$mwb_wgm_exclude_per_category = array();
						$mwb_wgm_exclude_per_category = isset( $_POST['mwb_wgm_exclude_per_category'] ) ? $_POST['mwb_wgm_exclude_per_category'] : array();
						if ( isset( $mwb_wgm_exclude_per_category ) && ! empty( $mwb_wgm_exclude_per_category ) ) {
							update_post_meta( $product_id, 'mwb_wgm_exclude_per_category', $mwb_wgm_exclude_per_category );
						} else {
							update_post_meta( $product_id, 'mwb_wgm_exclude_per_category', $mwb_wgm_exclude_per_category );
						}

						// added code

						$mwb_wgm_include_per_product = array();
						$mwb_wgm_include_per_product = isset( $_POST['mwb_wgm_include_per_product'] ) ? $_POST['mwb_wgm_include_per_product'] : '';
						$giftcard_include_product_string = '';
						if ( isset( $mwb_wgm_include_per_product ) && ! empty( $mwb_wgm_include_per_product ) ) {

							foreach ( $mwb_wgm_include_per_product as $value ) {
								$giftcard_include_product_string .= $value . ',';
							}
							$giftcard_include_product_string = rtrim( $giftcard_include_product_string, ',' );
							update_post_meta( $product_id, 'mwb_wgm_include_per_pro_format', $giftcard_include_product_string );
							update_post_meta( $product_id, 'mwb_wgm_include_per_product', $mwb_wgm_include_per_product );
						} else {
							update_post_meta( $product_id, 'mwb_wgm_include_per_product', $mwb_wgm_include_per_product );
							update_post_meta( $product_id, 'mwb_wgm_include_per_pro_format', $giftcard_include_product_string );
						}

						$mwb_wgm_include_per_category = array();
						$mwb_wgm_include_per_category = isset( $_POST['mwb_wgm_include_per_category'] ) ? $_POST['mwb_wgm_include_per_category'] : array();
						if ( isset( $mwb_wgm_include_per_category ) && ! empty( $mwb_wgm_include_per_category ) ) {
							update_post_meta( $product_id, 'mwb_wgm_include_per_category', $mwb_wgm_include_per_category );
						} else {
							update_post_meta( $product_id, 'mwb_wgm_include_per_category', $mwb_wgm_include_per_category );
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
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
				curl_setopt( $ch, CURLOPT_HEADER, true );
				curl_setopt( $ch, CURLOPT_POST, 1 );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $giftcard_pdf_content );
				$output = curl_exec( $ch );
				$uploadDirPath = MWB_UPLOAD_DIR . '/giftcard_pdf';
				if ( ! is_dir( $uploadDirPath ) ) {
					wp_mkdir_p( $uploadDirPath );
					chmod( $uploadDirPath, 0755 );
				}

				$handle = fopen( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $time . $site_name . '.pdf', 'w' ) or die( 'Cannot open file:  giftcard' . $time . $site_name . '.pdf' );

				fwrite( $handle, $output );
				fclose( $handle );

				if ( ! empty( $order_id ) && ! empty( $coupon_code ) ) {
					$dwnld_pdf = fopen( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $order_id . $coupon_code . '.pdf', 'w' ) or die( 'Cannot open file:  giftcard' . $order_id . $coupon_code . '.pdf' );
					fwrite( $dwnld_pdf, $output );
					fclose( $dwnld_pdf );
				}

				curl_close( $ch );
		}
		/**
		 * Hiding the sidebar forever
		 *
		 * @name mwb_wgm_hide_sidebar_forever
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_hide_sidebar_forever() {
			$response['result'] = __( 'Fail due to an error', 'woocommerce-ultimate-gift-card' );
			update_option( 'mwb_wgm_hide_sidebar_forever', 'yes' );
			$response['result'] = 'success';
			echo json_encode( $response );
			wp_die();
		}
		/**
		 * ajax request for handling the event templates
		 *
		 * @name mwb_wgm_append_default_template
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_append_default_template() {
			check_ajax_referer( 'mwb-wgm-verify-nonce', 'mwb_nonce' );
			$response['result'] = __( 'Fail due to an error', 'woocommerce-ultimate-gift-card' );
			$template_ids = $_POST['template_ids'];

			if ( isset( $template_ids ) && ! empty( $template_ids ) ) {
				$args = array(
					'post_type' => 'giftcard',
					'posts_per_page' => -1,
					'post__in' => $template_ids,
				);
				$loop = new WP_Query( $args );
				$template = array();
				if ( $loop->have_posts() ) :
					while ( $loop->have_posts() ) :
						$loop->the_post();
						global $product;
						$template_id = $loop->post->ID;
						$template_title = $loop->post->post_title;
						$template[ $template_id ] = $template_title;
					endwhile;
				endif;
				$response['templateid'] = $template;
				$response['result'] = 'success';
			} else if ( empty( $template_ids ) ) {
				$response['result'] = 'no_ids';
			}
			echo json_encode( $response );
			wp_die();
		}
		/**
		 * This function is used to add the manual increment option inside the Coupon Section for new woo version
		 *
		 * @name mwb_wgm_manual_increment_usage_count
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_manual_increment_usage_count( $coupon_id, $coupon ) {
			$mwb_wgm_manual_inc = get_option( 'mwb_wgm_manually_increment_usage', false );
			if ( isset( $mwb_wgm_manual_inc ) && $mwb_wgm_manual_inc == 'on' ) {
				woocommerce_wp_text_input(
					array(
						'id'                => 'manually_increment_usage',
						'label'             => __( 'Manually Increment Usage', 'woocommerce-ultimate-gift-card' ),
						'placeholder'       => esc_attr__( 'Increment Usage', 'woocommerce-ultimate-gift-card' ),
						'description'       => __( 'Number of times coupon has been used', 'woocommerce-ultimate-gift-card' ),
						'type'              => 'number',
						'desc_tip'          => true,
						'class'             => 'short',
						'custom_attributes' => array(
							'step'  => 1,
							'min'   => 0,
						),
						'value' => $coupon->get_usage_count() ? $coupon->get_usage_count() : 0,
					)
				);
			}
		}
		/**
		 * This function is used to add the manual increment option inside the Coupon Section for old woo version
		 *
		 * @name mwb_wgm_manual_increment_usage_count_old_woo
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_manual_increment_usage_count_old_woo() {
			$mwb_wgm_manual_inc = get_option( 'mwb_wgm_manually_increment_usage', false );
			if ( isset( $mwb_wgm_manual_inc ) && $mwb_wgm_manual_inc == 'on' ) {
				woocommerce_wp_text_input(
					array(
						'id'                => 'manually_increment_usage',
						'label'             => __( 'Manually Increment Usage', 'woocommerce-ultimate-gift-card' ),
						'placeholder'       => esc_attr__( 'Increment Usage', 'woocommerce-ultimate-gift-card' ),
						'description'       => __( 'Number of times coupon has been used', 'woocommerce-ultimate-gift-card' ),
						'type'              => 'number',
						'desc_tip'          => true,
						'class'             => 'short',
						'custom_attributes' => array(
							'step'  => 1,
							'min'   => 0,
						),
						'value' => $coupon->get_usage_count() ? $coupon->get_usage_count() : 0,
					)
				);
			}
		}
		/**
		 * This function is used to add/update the usage count (manual increment) manually
		 *
		 * @name mwb_wgm_save_coupon_post
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_save_coupon_post( $coupon_id ) {
			if ( isset( $_POST['manually_increment_usage'] ) && ! empty( $_POST['manually_increment_usage'] ) ) {
				$mwb_wgm_manual_value = sanitize_text_field( $_POST['manually_increment_usage'] );
				update_post_meta( $coupon_id, 'usage_count', $mwb_wgm_manual_value );
			}
		}

		/**
		 * This function is used for checking the entered code is existing or not for OFFLINE GIFT CARD section
		 *
		 * @name mwb_wgm_check_manual_code_exist
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_check_manual_code_exist() {
			$mwb_manual_code = $_POST['mwb_manual_code'];
			$response['result'] = 'Fail due to some error!';
			if ( isset( $mwb_manual_code ) && ! empty( $mwb_manual_code ) ) {
				$the_coupon = new WC_Coupon( $mwb_manual_code );
				$mwb_manual_code_id = $the_coupon->get_id();
				if ( $mwb_manual_code_id == 0 ) {
					$response['result'] = 'valid';
				} else {
					$response['result'] = 'invalid';
				}
				echo json_encode( $response );
				wp_die();
			}
		}
		/**
		 * This function is used for adding the HTML for providing another way to the Admin for editing the Email from backend, after the order has been placed successfully
		 *
		 * @name mwb_wgm_edit_email_address
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_edit_email_address() {
			global $post;
			if ( isset( $post->ID ) ) {
				$woo_ver = WC()->version;
				$order_id = $post->ID;
				$order = wc_get_order( $order_id );
				$order_items = $order->get_items();
				foreach ( $order_items as $item_id => $item ) {
					if ( $woo_ver < '3.0.0' ) {
						$product = $order->get_product_from_item( $item );
					} else {
						$product = $item->get_product();
					}
					if ( $woo_ver < '3.0.0' ) {
						if ( isset( $item['item_meta']['Delivery Method'] ) && ! empty( $item['item_meta']['Delivery Method'] ) ) {
							$delivery_method = $item['item_meta']['Delivery Method'][0];
						}
					} else {
						$item_meta_data = $item->get_meta_data();
						foreach ( $item_meta_data as $key => $value ) {
							if ( isset( $value->key ) && $value->key == 'Delivery Method' && ! empty( $value->value ) ) {
								$delivery_method = $value->value;
							}
						}
					}
				}
				if ( $delivery_method = 'Mail to recipient' ) {
					?>
					<div id="mwb_wgm_loader" style="display: none;">
						<img src="<?php echo MWB_WGM_URL; ?>/assets/images/loading.gif">
					</div>
					<p><?php _e( 'Enter your new Email Address if previous one was not correctly entered, After successfully updation you have to Resend the Email once', 'woocommerce-ultimate-gift-card' ); ?></p>
					<p id="mwb_wgm_resend_confirmation_msg"></p>
					<table class="form-table">
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="mwb_wgm_new_email"><?php _e( 'Enter the new Email', 'woocommerce-ultimate-gift-card' ); ?>
							</label>
						</th>
						<td class="forminp forminp-text">
							<input type="email" class="mwb_wgm_new_email" id="mwb_wgm_new_email">
						</td>
					</tr>
					<tr valign="top">
						<td class="forminp forminp-text">
							<label for="mwb_wgm_update_item_meta">
								<a href="javascript:void(0)" class="button button-primary" id="mwb_wgm_update_item_meta" data-id="<?php echo $order_id; ?>"><?php _e( 'Update Email', 'woocommerce-ultimate-gift-card' ); ?></a>
							</label>
						</td>
					</tr>							
				</table>
					<?php
				}
			}
		}
		/**
		 * This function is used for updating the Order_Item Meta for sending the gift card to the updated email id.
		 *
		 * @name mwb_wgm_update_item_meta_with_new_email
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_update_item_meta_with_new_email() {
			check_ajax_referer( 'mwb-wgm-verify-nonce', 'mwb_nonce' );
			$response['result'] = false;
			$response['message'] = __( 'Mail sending failed due to some issue. Please try again.', 'woocommerce-ultimate-gift-card' );
			$woo_ver = WC()->version;
			if ( isset( $_POST['order_id'] ) && ! empty( $_POST['order_id'] ) && isset( $_POST['new_email_id'] ) && ! empty( $_POST['new_email_id'] ) ) {
				$correct_email_format = $_POST['correct_email_format'];
				if ( $correct_email_format == 'true' ) {
					$order_id = sanitize_post( $_POST['order_id'] );
					$new_email_id = sanitize_post( $_POST['new_email_id'] );
					$order = wc_get_order( $order_id );
					$order_items = $order->get_items();
					foreach ( $order_items as $item_id => $item ) {

						$product = $order->get_product_from_item( $item );
						$product_id = $product->get_id();
						wc_update_order_item_meta( $item_id, 'To', $new_email_id );

						// Update the recipient email for "Check the Balance of Gift Card"
						$giftcoupon = get_post_meta( $order_id, "$order_id#$item_id", true );
						if ( is_array( $giftcoupon ) && ! empty( $giftcoupon ) ) {

							foreach ( $giftcoupon as $key => $value ) {

								$the_coupon = new WC_Coupon( $value );
								$coupon_id = $the_coupon->get_id();
								update_post_meta( $coupon_id, 'mwb_wgm_giftcard_coupon_mail_to', $new_email_id );

							}
						}
					}
					$response['result'] = true;
					$response['message'] = __( 'Email Id has been updated, now you may Resend your Email', 'woocommerce-ultimate-gift-card' );
				} else {
					$response['result'] = false;
					$response['message'] = __( 'Enter a valid Email Id', 'woocommerce-ultimate-gift-card' );
				}
			} else {
				$response['result'] = false;
				$response['message'] = __( 'Email field should not be empty', 'woocommerce-ultimate-gift-card' );
			}
			echo json_encode( $response );
			wp_die();
		}
		/**
		 * This function is used for handling the ajax request for generating pdf in a new way
		 *
		 * @name mwb_wgm_new_way_for_generating_pdfs
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_new_way_for_generating_pdfs() {
			if ( isset( $_POST['mwb_wgm_new_way_for_pdf'] ) && $_POST['mwb_wgm_new_way_for_pdf'] == 'yes' ) {
				$site_name = $_SERVER['SERVER_NAME'];
				$check = file_put_contents( MWB_WGM_DIRPATH . 'wkhtmltox.zip', fopen( 'https://makewebbetter.com/gift-card-pdf/download.php?download_file=wkhtmltox.zip&domain=' . $site_name, 'r' ) );
				if ( $check !== 0 && $check !== false ) {
					$response['result'] = true;
					$response['message'] = __( 'Process successfully completed!!', 'woocommerce-ultimate-gift-card' );
				} else {
					$response['result'] = false;
					$response['message'] = __( 'Fail due to some error, Please Try once and if it is hapennig again and again then please contact to our Support', 'woocommerce-ultimate-gift-card' );
				}
			} else {
				$response['result'] = false;
				$response['message'] = __( 'Fail due to some error, Please Try once and if it is hapennig again and again then please contact to our Support', 'woocommerce-ultimate-gift-card' );
			}
			echo json_encode( $response );
			wp_die();
		}
		/**
		 * This function is used for handling the next step via an ajax request for generating pdf in a new way
		 *
		 * @name mwb_wgm_next_step_for_generating_pdfs
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_next_step_for_generating_pdfs() {
			if ( isset( $_POST['mwb_wgm_next_step_for_pdf'] ) && $_POST['mwb_wgm_next_step_for_pdf'] == 'yes' ) {
				$mwb_wgm_zip = new ZipArchive();
				$result = $mwb_wgm_zip->open( MWB_WGM_DIRPATH . 'wkhtmltox.zip' );
				if ( $result === true ) {
					$mwb_wgm_zip->extractTo( MWB_WGM_DIRPATH );
					$mwb_wgm_file = chmod( MWB_WGM_DIRPATH . 'wkhtmltox/bin/wkhtmltopdf', 0777 );
					update_option( 'mwb_wgm_next_step_for_pdf_value', 'yes' );
					$response['result'] = true;
					$response['message'] = __( 'Process completed!!', 'woocommerce-ultimate-gift-card' );
				} else {
					$response['result'] = false;
					$response['message'] = __( 'Fail due to some error, Please Try once and if it happens again and again then please contact to our Support!', 'woocommerce-ultimate-gift-card' );
				}
			} else {
				$response['result'] = false;
				$response['message'] = __( 'Fail due to some error, Please Try once and if it happens again and again then please contact to our Support', 'woocommerce-ultimate-gift-card' );
			}
			echo json_encode( $response );
			wp_die();
		}
		/**
		 * This function is used for adding the  dropdown for filterization for Offline,Online, and Imported Coupons
		 *
		 * @name mwb_wgm_restrict_manage_posts
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_restrict_manage_posts() {
			global $typenow;
			global $post;
			if ( 'shop_coupon' == $typenow ) {
				$mwb_wgm_online_giftcards = false;
				$mwb_wgm_offline_giftcards = false;
				$mwb_wgm_imported_cpn = false;
				if ( isset( $_GET['mwb_wgm_coupon_type'] ) ) {
					if ( $_GET['mwb_wgm_coupon_type'] == 'online' ) {
						$mwb_wgm_online_giftcards = true;
					} elseif ( $_GET['mwb_wgm_coupon_type'] == 'offline' ) {
						$mwb_wgm_offline_giftcards = true;
					} elseif ( $_GET['mwb_wgm_coupon_type'] == 'importedcoupon' ) {
						$mwb_wgm_imported_cpn = true;
					}
				}
				?>
					
				<select name="mwb_wgm_coupon_type" id="mwb_wgm_dropdown_shop_coupon_type">
					<?php
					$alreadyselected = '';
					$alreadyselected1 = '';
					$alreadyselected2 = '';
					if ( $mwb_wgm_online_giftcards ) {
						$alreadyselected = " selected='selected'";
					} elseif ( $mwb_wgm_offline_giftcards ) {
						$alreadyselected1 = " selected='selected'";
					} elseif ( $mwb_wgm_imported_cpn ) {
						$alreadyselected2 = " selected='selected'";
					}
					?>
					<option><?php _e( 'Select Gift Cards', 'woocommerce-ultimate-gift-card' ); ?></option>
					<option value="online" <?php echo $alreadyselected; ?> ><?php _e( 'Online Gift Cards', 'woocommerce-ultimate-gift-card' ); ?></option>
					<option value="offline" <?php echo $alreadyselected1; ?> ><?php _e( 'Offline Gift Cards', 'woocommerce-ultimate-gift-card' ); ?></option>
					<option value="importedcoupon" <?php echo $alreadyselected2; ?> ><?php _e( 'Imported Gift Coupons', 'woocommerce-ultimate-gift-card' ); ?></option>
				</select>
				<?php
			}
		}
		/**
		 * This function is used for handle the requested query and return the result for ONline, Off and Imported Coupons
		 *
		 * @name mwb_wgm_request_query
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_request_query( $vars ) {
			global $typenow;
			if ( 'shop_coupon' === $typenow ) {
				if ( ! empty( $_GET['mwb_wgm_coupon_type'] ) && ( $_GET['mwb_wgm_coupon_type'] == 'online' || $_GET['mwb_wgm_coupon_type'] == 'offline' ) ) {
					$vars['meta_key']   = 'mwb_wgm_giftcard_coupon_unique';
					$vars['meta_value'] = wc_clean( $_GET['mwb_wgm_coupon_type'] );
				}
				if ( ! empty( $_GET['mwb_wgm_coupon_type'] ) && $_GET['mwb_wgm_coupon_type'] == 'importedcoupon' ) {
					$vars['meta_key']   = 'mwb_wgm_imported_coupon';
					$vars['meta_value'] = 'yes';
				}
			}
			return $vars;
		}

		public function mwb_wgm_activate_license() {
			check_ajax_referer( 'mwb-wgm-verify-nonce', 'mwb_nonce' );
			$mwb_license_key = sanitize_text_field( $_POST['license_key'] );
			$mwb_admin_name = '';
			$mwb_admin_email = get_option( 'admin_email', '' );
			$mwb_admin_details = get_user_by( 'email', $mwb_admin_email );
			if ( isset( $mwb_admin_details->data ) ) {
				if ( isset( $mwb_admin_details->data->display_name ) ) {
					$mwb_admin_name = $mwb_admin_details->data->display_name;
				}
			}

			$host_server = $_SERVER['HTTP_HOST'];
			if ( strpos( $host_server, 'www.' ) == 0 ) {

				$host_server = str_replace( 'www.', '', $host_server );
			}
			
			$mwb_license_arr = array(
				'license_key' => $mwb_license_key,
				'domain_name' => $host_server,
				'admin_name' => $mwb_admin_name,
				'admin_email' => $mwb_admin_email,
				'plugin_name' => 'WooCommerce Ultimate Gift Card',
			);
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://makewebbetter.com/codecanyon/validate_license.php",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => http_build_query($mwb_license_arr),
				CURLOPT_HTTPHEADER => array(
					"cache-control: no-cache",
					"content-type: application/x-www-form-urlencoded",
					"postman-token: d9d9a28b-94cc-7a0e-47b2-5bd10aa7aa89"
				),
			));

			$mwb_res = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				echo "cURL Error #:" . $err;
			}
			$mwb_res = json_decode($mwb_res,true);
			
			if( $mwb_res['status'] == true)
			{
				update_option('mwb_wgm_license_hash',$mwb_res['hash']);
				update_option('mwb_wgm_plugin_name','WooCommerce Ultimate Gift Card');
				update_option('mwb_wgm_license_key',$mwb_res['mwb_key']);
				update_option('mwb_wgm_plugin_verified','yes');
				echo json_encode( array('status'=>true,'msg'=>__('Successfully Verified','woocommerce-ultimate-gift-card') ) );
			}
			else if( $mwb_res['status'] == false )
			{
				update_option('mwb_wgm_plugin_verified','no');
				echo json_encode( array('status'=>false,'msg'=> $mwb_res['msg']) );
			}
			wp_die();
		}

		public function mwb_wgm_update_json_process() {
			$uploadDirPath = MWB_UPLOAD_DIR . '/mwb_wgm_json';
			if ( ! is_dir( $uploadDirPath ) ) {
				wp_mkdir_p( $uploadDirPath );
				chmod( $uploadDirPath, 0755 );
			}
			$data = file_get_contents( 'https://makewebbetter.com/pluginupdates/addons/gc/api.json' );
			$handle = fopen( MWB_UPLOAD_DIR . '/mwb_wgm_json/api.json', 'w' );
			fwrite( $handle, $data );
			fclose( $handle );
		}

	}
	new MWB_WGM_Card_Product();
}
?>
