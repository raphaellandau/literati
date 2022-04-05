<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class TT_Example_List_Table extends WP_List_Table {

	public $example_data;

	function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'id':
				return $item[ $column_name ];
			case 'date':
				return $item[ $column_name ];
			case 'coupon':
				return '<b>' . $item[ $column_name ] . '</b>';
			case 'to':
				return $item[ $column_name ];
			case 'from':
				return $item[ $column_name ];
			case 'message':
				return stripcslashes( $item[ $column_name ] );
			case 'schedule':
				if ( isset( $item[ $column_name ] ) && $item[ $column_name ] != null ) {
					return $item[ $column_name ];
				} else {
					return __( 'Not Scheduled', 'woocommerce-ultimate-gift-card' );
				}

			case 'amount':
				return '<b>' . wc_price( $item[ $column_name ] ) . '</b>';
			case 'resend':
				 $text = __( 'RESEND', 'woocommerce-ultimate-gift-card' );

				 $html = '<input type="button" value="' . $text . '" data-id="' . $item['id'] . '" class="button button-primary button-large mwb_wgm_offline_resend_mail"><p class="resendmail"></p>';

				return $html;
			default:
				return false;
		}
	}

	function get_columns() {
		$columns = array(
			'cb'      => '<input type="checkbox" />',
			'id'    => __( 'ID', 'woocommerce-ultimate-gift-card' ),
			'date'  => __( 'Order Date', 'woocommerce-ultimate-gift-card' ),
			'to'     => __( 'To', 'woocommerce-ultimate-gift-card' ),
			'from'   => __( 'From', 'woocommerce-ultimate-gift-card' ),
			'message' => __( 'Messsage', 'woocommerce-ultimate-gift-card' ),
			'amount' => __( 'Price', 'woocommerce-ultimate-gift-card' ),
			'coupon' => __( 'Giftcard Coupon', 'woocommerce-ultimate-gift-card' ),
			'schedule' => __( 'Schedule Date', 'woocommerce-ultimate-gift-card' ),
			'resend' => __( 'Resend', 'woocommerce-ultimate-gift-card' ),
		);
		return $columns;
	}
	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="mwb_offline_ids[]" value="%s" />',
			$item['id']
		);
	}
	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => __( 'Delete', 'woocommerce-ultimate-gift-card' ),
		];
		return $actions;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'id'    => array( 'id', false ),
			'date'  => array( 'date', false ),
		);
		return $sortable_columns;
	}
	public function process_bulk_action() {

		if ( 'bulk-delete' === $this->current_action() ) {
			if ( isset( $_POST['mwb_offline_ids'] ) && ! empty( $_POST['mwb_offline_ids'] ) ) {
				$offline_ids = $_POST['mwb_offline_ids'];
				global $wpdb;
				$table_name = $wpdb->prefix . 'offline_giftcard';
				foreach ( $offline_ids as $key => $value ) {
					$wpdb->delete( $table_name, array( 'id' => $value ) );
				}
			}
			?>
		<div class="notice notice-success is-dismissible"> 
			<p><strong><?php echo __( 'Offline Gift Card Deleted', 'woocommerce-ultimate-gift-card' ); ?></strong></p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text"><?php echo __( 'Dismiss this notice.', 'woocommerce-ultimate-gift-card' ); ?></span>
			</button>
		</div>
			<?php
		}
	}

	function prepare_items() {
		global $wpdb; // This is used only if making any database queries
		$per_page = 10;
		$columns = $this->get_columns();

		$hidden = array();

		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

		$table_name = $wpdb->prefix . 'offline_giftcard';

		$query = "SELECT * FROM $table_name";

		$giftresults = $wpdb->get_results( $query, ARRAY_A );

		$this->example_data = $giftresults;
		$data = $this->example_data;

		usort( $data, array( $this, 'mwb_wgm_usort_reorder' ) );

		$current_page = $this->get_pagenum();
		$total_items = count( $data );
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		$this->items = $data;
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

	function mwb_wgm_usort_reorder( $cloumna, $cloumnb ) {
		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'id';
		$order = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'dsc';
		$result = strcmp( $cloumna[ $orderby ], $cloumnb[ $orderby ] );
		return ( $order === 'asc' ) ? $result : -$result;
	}
}

$display_message = array();
global $display_message;
if ( isset( $_POST['mwb_wgm_csv_custom_import'] ) && ! empty( $_POST['mwb_wgm_csv_custom_import'] ) ) {
	if ( isset( $_POST['mwb_wgm_offline_gift_template'] ) && $_POST['mwb_wgm_offline_gift_template'] != null ) {


		if ( ! empty( $_FILES['csv_import']['tmp_name'] ) ) {

			$csv_mimetypes = array(
				'text/csv',
				'application/csv',
				'text/comma-separated-values',
				'application/excel',
				'application/vnd.ms-excel',
				'application/vnd.msexcel',
				'application/octet-stream',
			);

			if ( in_array( $_FILES['csv_import']['type'], $csv_mimetypes ) ) {
				$file = $_FILES['csv_import']['tmp_name'];
				if ( file_exists( $file ) ) {
					$row = 1;
					ini_set( 'auto_detect_line_endings', true );
					$handle = fopen( $file, 'r' );
					if ( $handle ) {
						$count = 0;
						$postedValues = array();

						while ( ( $data = fgetcsv( $handle, 1000 ) ) !== false ) {
							if ( $row == 1 ) {
								$row++;
								continue;
							}
							if ( isset( $data ) && ! empty( $data ) && count( $data ) == 5 ) {
								$postedValues[ $count ]['to'] = $data[0];
								$postedValues[ $count ]['from'] = $data[1];
								$postedValues[ $count ]['message'] = sanitize_text_field( $data[2] );
								$postedValues[ $count ]['amount'] = sanitize_text_field( $data[3] );
								$postedValues[ $count ]['template'] = $_POST['mwb_wgm_offline_gift_template'];
								if ( isset( $data[4] ) && $data[4] != '' && $data[4] != null ) {
									$postedValues[ $count ]['schedule'] = $data[4];
								} else {
									$postedValues[ $count ]['schedule'] = null;
								}
							} else {
								$display_message['class'] = 'notice-error';
								$display_message['message'] = "<b style='color:red;'>" . __( 'File not imported due to some error.', 'woocommerce-ultimate-gift-card' ) . '</b>';

							}
							$count++;
						}

						fclose( $handle );
					} else {
						$display_message['class'] = 'notice-error';
						$display_message['message'] = "<b style='color:red;'>" . __( 'File not imported due to some error.', 'woocommerce-ultimate-gift-card' ) . '</b>';
					}
					add_offline_data_to_table( $postedValues );
				} else {
					$display_message['class'] = 'notice-error';
					$display_message['message'] = "<b style='color:red;'>" . __( 'File not imported due to some error.', 'woocommerce-ultimate-gift-card' ) . '</b>';
				}
			} else {
				$display_message['class'] = 'notice-error';
				$display_message['message'] = "<b style='color:red;'>" . __( 'File not imported due to some error.', 'woocommerce-ultimate-gift-card' ) . '</b>';
			}
		} else {
			$display_message['class'] = 'notice-error';
			$display_message['message'] = __( 'File not imported due to some error.', 'woocommerce-ultimate-gift-card' );

		}
	} else {
		$display_message['class'] = 'notice-error';
		$display_message['message'] = __( 'Please create Gift Card Product First.', 'woocommerce-ultimate-gift-card' );
	}
}

function add_offline_data_to_table( $postedValues ) {

	global $wpdb,$display_message;
	$table_name = $wpdb->prefix . 'offline_giftcard';

	if ( isset( $postedValues ) && ! empty( $postedValues ) ) {
		foreach ( $postedValues as $key => $value ) {

			if ( isset( $value['template'] ) && $value['template'] != null && $value['template'] != '' ) {
				$giftcard_coupon_length = get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 );
				$gift_couponnumber = mwb_wgm_coupon_generator( $giftcard_coupon_length );
				$value['coupon'] = $gift_couponnumber;
				$value['date'] = date_i18n( 'Y-m-d h:i:s' );
				$value['mail'] = false;

				$insert_id = $wpdb->insert( $table_name, $value );
			}
		}
		$display_message['class'] = 'notice-success';
		$display_message['message'] = __( 'File imported successfully.', 'woocommerce-ultimate-gift-card' );

	}

}

$args = array(
	'post_type' => 'product',
	'posts_per_page' => -1,
	'meta_key' => 'mwb_wgm_pricing',
);

$gift_products = array();
$loop = new WP_Query( $args );
if ( $loop->have_posts() ) :
	while ( $loop->have_posts() ) :
		$loop->the_post();
		global $product;
		$product_id = $loop->post->ID;
		$product_title = $loop->post->post_title;
		$product_types = wp_get_object_terms( $product_id, 'product_type' );
		if ( isset( $product_types[0] ) ) {
			$product_type = $product_types[0]->slug;
			if ( $product_type == 'wgm_gift_card' ) {
				$gift_products[ $product_id ] = $product_title;
			}
		}
	endwhile;
endif;
if ( isset( $_GET['action'] ) ) {
	if ( $_GET['action'] == 'add' ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'offline_giftcard';

		// save new offline giftcard order

		if ( isset( $_POST['mwb_wgm_offline_gift_save'] ) ) {
			if ( isset( $_POST['mwb_wgm_offline_gift_template'] ) && $_POST['mwb_wgm_offline_gift_template'] != null ) {

				$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format_1', false );
				$giftcard_coupon_length = get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', 5 );
				$gift_manual_code = sanitize_text_field( $_POST['mwb_wgm_offline_gift_coupon_manual'] );
				if ( isset( $gift_manual_code ) && ! empty( $gift_manual_code ) ) {
					$gift_couponnumber = $gift_manual_code;
				} else {
					$gift_couponnumber = mwb_wgm_coupon_generator( $giftcard_coupon_length );
				}
				$data['to'] = sanitize_text_field( $_POST['mwb_wgm_offline_gift_to'] );
				$data['from'] = sanitize_text_field( $_POST['mwb_wgm_offline_gift_from'] );
				$data['amount'] = sanitize_text_field( $_POST['mwb_wgm_offline_gift_amount'] );
				$data['message'] = sanitize_text_field( $_POST['mwb_wgm_offline_gift_message'] );
				$data['template'] = $_POST['mwb_wgm_offline_gift_template'];
				$data['coupon'] = $gift_couponnumber;
				$data['date'] = date_i18n( 'Y-m-d h:i:s' );
				$data['schedule'] = null;
				$schedule_date = $_POST['mwb_wgm_offline_gift_schedule'];

				$counter_date = 'mwb_default';
				$counter_schedule = true;

				if ( isset( $schedule_date ) && $schedule_date != '' && $schedule_date != null ) {
					$counter_schedule = false;
					if ( is_string( $schedule_date ) ) {
						if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
							if ( $selected_date == 'd/m/Y' ) {
								$schedule_date = str_replace( '/', '-', $schedule_date );
							}
						}
						$senddatetime = strtotime( $schedule_date );
					}
					$senddatetime = strtotime( $schedule_date );
					$senddate = date_i18n( 'Y-m-d', $senddatetime );
					$todaytime = time();
					$todaydate = date_i18n( 'Y-m-d', $todaytime );
					$senddatetime = strtotime( "$senddate" );
					$todaytime = strtotime( "$todaydate" );
					$giftdiff = $senddatetime - $todaytime;
					if ( $giftdiff > 0 ) {
						$counter_date = 'mwb_schedule';
					} else {
						$counter_date = 'mwb_schedule_today';
					}
					$data['schedule'] = $_POST['mwb_wgm_offline_gift_schedule'];
					if ( is_string( $data['schedule'] ) ) {
						if ( isset( $selected_date ) && $selected_date != null && $selected_date != '' ) {
							if ( $selected_date == 'd/m/Y' ) {
								$data['schedule'] = str_replace( '/', '-', $data['schedule'] );
							}
						}
						$data['schedule'] = strtotime( $data['schedule'] );
					}

					$data['schedule'] = date_i18n( 'Y-m-d', $data['schedule'] );
				}
				$send_mail = true;
				if ( $counter_date == 'mwb_schedule' && ! $counter_schedule ) {
					$send_mail = false;
				}

				$data['mail'] = false;
				if ( $send_mail ) {
					$to = $data['to'];
					$from = $data['from'];
					$subject = get_option( 'mwb_wgm_other_setting_giftcard_subject', false );
					$bloginfo = get_bloginfo();
					if ( empty( $subject ) || ! isset( $subject ) ) {

						$subject = __( "$bloginfo: Hurry!!! Giftcard is Received", 'woocommerce-ultimate-gift-card' );
					}
					$subject = str_replace( '[SITENAME]', $bloginfo, $subject );
					$subject = str_replace( '[BUYEREMAILADDRESS]', $from, $subject );
					$subject = stripcslashes( $subject );
					$subject = html_entity_decode( $subject, ENT_QUOTES, 'UTF-8' );
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
						$expirydate_format = __( 'No Expiry', 'woocommerce-ultimate-gift-card' );
					}
					$product_id = $data['template'];
					$mwb_wgm_pricing = get_post_meta( $product_id, 'mwb_wgm_pricing', true );
					$templateid = $mwb_wgm_pricing['template'];
					if ( is_array( $templateid ) && array_key_exists( 0, $templateid ) ) {
						$temp = $templateid[0];
					} else {
						$temp = $templateid;
					}
					$args['from'] = $data['from'];
					$args['to'] = $data['to'];
					$args['message'] = stripcslashes( $data['message'] );
					$args['coupon'] = apply_filters( 'mwb_wgm_qrcode_coupon', $gift_couponnumber );
					$args['expirydate'] = $expirydate_format;
					$args['amount'] = wc_price( $data['amount'] );
					$args['templateid'] = $temp;

					$args['product_id'] = $product_id;
					$giftcardfunction = new MWB_WGM_Card_Product_Function();
					$message = $giftcardfunction->mwb_wgm_giftttemplate( $args );
					$mwb_wgm_pdf_enable = get_option( 'mwb_wgm_addition_pdf_enable', false );
					if ( isset( $mwb_wgm_pdf_enable ) && $mwb_wgm_pdf_enable == 'on' ) {
						$site_name = $_SERVER['SERVER_NAME'];
						$time = time();
						$giftcardfunction->mwb_wgm_attached_pdf( $message, $site_name, $time );
						$attachments = array( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $time . $site_name . '.pdf' );

					} else {
						$attachments = array();
					}
					// send mail to receiver
					$mwb_wgc_bcc_enable = get_option( 'mwb_wgm_addition_bcc_option_enable', false );
					if ( isset( $mwb_wgc_bcc_enable ) && $mwb_wgc_bcc_enable == 'on' ) {
						$headers[] = 'Bcc:' . $from;
						wc_mail( $to, $subject, $message, $headers, $attachments );
						if ( ! empty( $time ) && ! empty( $site_name ) ) {
							unlink( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $time . $site_name . '.pdf' );
						}
					} else {
						$headers = array( 'Content-Type: text/html; charset=UTF-8' );
						wc_mail( $to, $subject, $message, $attachments );
						if ( ! empty( $time ) && ! empty( $site_name ) ) {
							unlink( MWB_UPLOAD_DIR . '/giftcard_pdf/giftcard' . $time . $site_name . '.pdf' );
						}
					}

					$data['mail'] = true;
					$insert_id = $wpdb->insert( $table_name, $data );
					$insert_id = $wpdb->insert_id;

					// coupon is created
					$couponcreated = mwb_wgm_create_offlinegift_coupon( $gift_couponnumber, $data['amount'], $insert_id, $product_id, $to );

					$subject = get_option( 'mwb_wgm_other_setting_receive_subject', false );
					$message = get_option( 'mwb_wgm_other_setting_receive_message', false );
					if ( empty( $subject ) || ! isset( $subject ) ) {

						$subject = __( "$bloginfo: Giftcard is Send Successfully", 'woocommerce-ultimate-gift-card' );
					}

					if ( empty( $message ) || ! isset( $message ) ) {

						$message = __( "$bloginfo: Giftcard is Send Successfully", 'woocommerce-ultimate-gift-card' );
					}

					$message = stripcslashes( $message );
					$subject = stripcslashes( $subject );
					$mwb_wgm_disable_buyer_notification = get_option( 'mwb_wgm_disable_buyer_notification', 'off' );
					if ( $mwb_wgm_disable_buyer_notification == 'off' ) {
						wc_mail( $from, $subject, $message );
					}
					// send acknowledge mail to sender

					?>
					<div class="updated notice notice-success is-dismissible" id="message">
						<p>
							<?php _e( 'Giftcard Created and Mail is send to Sender and Receiver. Code is', 'woocommerce-ultimate-gift-card' ); ?> : <a href="javascript:void(0);"><?php echo $gift_couponnumber; ?></a>
						</p>
					</div>
					<?php
				} else {
					$insert_id = $wpdb->insert( $table_name, $data );
					?>
					<div class="updated notice notice-success is-dismissible" id="message">
						<p>
							<?php _e( 'Giftcard Created and Mail will be send on scheduled date.', 'woocommerce-ultimate-gift-card' ); ?>
						</p>
					</div>
					<?php
				}
			} else {
				?>
					<div class="updated notice notice-success is-dismissible" id="message">
						<p>
							<?php _e( 'Please create Gift Card Product First.', 'woocommerce-ultimate-gift-card' ); ?>
						</p>
					</div>
				<?php
			}
		}


		?>
			<form method="post" action="">
				<table class="form-table mwb_wgm_offline_gift_to">
					<tbody>
						<tr>
							<th id="mwb_wgm_add_offline" colspan="2">
								<h3 class="wp-heading-inline" ><?php _e( 'Add New Giftcard', 'woocommerce-ultimate-gift-card' ); ?></h3>
								<a  href="<?php echo MWB_WGM_HOME_URL; ?>admin.php?page=mwb-wgc-setting&tab=offline-giftcard" class="button button-primary"><?php _e( 'VIEW LIST', 'woocommerce-ultimate-gift-card' ); ?>
							</th>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="mwb_wgm_offline_gift_schedule"><?php _e( 'Schedule Date', 'woocommerce-ultimate-gift-card' ); ?></label>
							</th>
							<td class="forminp forminp-text">
								<?php
									$attribute_description = __( 'Select the schedule date for sending the gift card on the specified date.', 'woocommerce-ultimate-gift-card' );
									echo wc_help_tip( $attribute_description );
								?>
								<label for="mwb_wgm_offline_gift_schedule">
									<input type="text" name="mwb_wgm_offline_gift_schedule" id="mwb_wgm_offline_gift_schedule" class="input-text mwb_wgm_new_woo_ver_style_text">
									<p class="description"><?php _e( 'Leave this field empty if you want to send the Gift Card right now.', 'woocommerce-ultimate-gift-card' ); ?></p>
								</label>						
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="mwb_wgm_offline_gift_to"><?php _e( 'To', 'woocommerce-ultimate-gift-card' ); ?></label>
							</th>
							<td class="forminp forminp-text">
								<?php
									$attribute_description = __( 'Enter the email id of the recipient.', 'woocommerce-ultimate-gift-card' );
									echo wc_help_tip( $attribute_description );
								?>
								<label for="mwb_wgm_offline_gift_to">
									<input type="email" name="mwb_wgm_offline_gift_to" id="mwb_wgm_offline_gift_to" class="input-text mwb_wgm_new_woo_ver_style_text" placeholder="<?php _e( 'to@example.com', 'woocommerce-ultimate-gift-card' ); ?>">
								</label>						
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="mwb_wgm_offline_gift_from"><?php _e( 'From', 'woocommerce-ultimate-gift-card' ); ?></label>
							</th>
							<td class="forminp forminp-text">
								<?php
									$attribute_description = __( 'Enter the email id of the sender.', 'woocommerce-ultimate-gift-card' );
									echo wc_help_tip( $attribute_description );
								?>
								<label for="mwb_wgm_offline_gift_from">
									<input type="email" name="mwb_wgm_offline_gift_from" id="mwb_wgm_offline_gift_from" class="input-text mwb_wgm_new_woo_ver_style_text" placeholder="<?php _e( 'from@example.com', 'woocommerce-ultimate-gift-card' ); ?>">
								</label>						
							</td>
						</tr>
						
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="mwb_wgm_offline_gift_amount"><?php _e( 'Amount', 'woocommerce-ultimate-gift-card' ); ?></label>
							</th>
							<td class="forminp forminp-text">
								<?php
									$attribute_description = __( 'Enter the Gift Card amount.', 'woocommerce-ultimate-gift-card' );
									echo wc_help_tip( $attribute_description );
								?>
								<label for="mwb_wgm_offline_gift_amount">
									<input type="number" name="mwb_wgm_offline_gift_amount" id="mwb_wgm_offline_gift_amount" class="input-text mwb_wgm_new_woo_ver_style_text" min="0">
								</label>						
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="mwb_wgm_offline_gift_coupon_manual"><?php _e( 'Custom Coupon Code', 'woocommerce-ultimate-gift-card' ); ?></label>
							</th>
							<td class="forminp forminp-text">
								<?php
									$attribute_description = __( 'Enter the Gift Coupon Manual, Leave blank if you need system generated code', 'woocommerce-ultimate-gift-card' );
									echo wc_help_tip( $attribute_description );
								?>
								<label for="mwb_wgm_offline_gift_coupon_manual">
									<input type="text" name="mwb_wgm_offline_gift_coupon_manual" id="mwb_wgm_offline_gift_coupon_manual" class="input-text mwb_wgm_new_woo_ver_style_text">
									<div id="mwb_wgm_invalid_code_notice" style="display: inline;"></div>
								</label>						
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="mwb_wgm_offline_gift_message"><?php _e( 'Message', 'woocommerce-ultimate-gift-card' ); ?></label>
							</th>
							<td class="forminp forminp-text">
								<?php
									$attribute_description = __( 'Enter the Gift Card message.', 'woocommerce-ultimate-gift-card' );
									echo wc_help_tip( $attribute_description );
								?>
								<label for="mwb_wgm_offline_gift_message">
									<textarea name="mwb_wgm_offline_gift_message" id="mwb_wgm_offline_gift_message" class="input-text" rows="2" cols="3"></textarea>
								</label>						
							</td>
						</tr>
						
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="mwb_wgm_offline_gift_template"><?php _e( 'Giftcard', 'woocommerce-ultimate-gift-card' ); ?></label>
							</th>
							<td class="forminp forminp-text">
								<?php
									$attribute_description = __( 'Select the Gift card product.', 'woocommerce-ultimate-gift-card' );
									echo wc_help_tip( $attribute_description );
								?>
								<label for="mwb_wgm_offline_gift_template">
									<?php
									if ( isset( $gift_products ) && ! empty( $gift_products ) ) {
										?>
										<select name="mwb_wgm_offline_gift_template" id="mwb_wgm_offline_gift_template" class="input-text mwb_wgm_new_woo_ver_style_select">
										<?php
										foreach ( $gift_products as $id => $gift_product ) {
											?>
											<option value="<?php echo $id; ?>"><?php echo $gift_product; ?></option>										
											<?php
										}
										?>
										</select>
										<?php
									} else {
										echo '<p style=color:red>' . __( 'No Gift Card Product Present, Please Add Gift Card Product first', 'woocommerce-ultimate-gift-card' ) . '</p>';
									}
									?>
									</select>
								</label>						
							</td>
						</tr>
						
						<tr valign="top">
							<th></th>
							<td scope="row" class="titledesc">
								<label for="mwb_wgm_offline_gift_preview"><a id="mwb_wgm_offline_gift_preview" href="javascript:void(0);"><?php _e( 'Preview', 'woocommerce-ultimate-gift-card' ); ?></a></label>
							</td>
						</tr>
						
					</tbody>
				</table>	
				<p class="submit">
					<input type="submit" name="mwb_wgm_offline_gift_save" id="mwb_wgm_offline_gift_save"  class="button-primary woocommerce-save-button" value="<?php _e( 'Save & Send', 'woocommerce-ultimate-gift-card' ); ?>">
				</p>	
			</form>
		<?php
	}
} else {
	global $display_message;

	?>
	<div class="mwb_wgm_import_giftcoupons">
	<h3 class="mwb_wgm_heading"><?php _e( 'Import Offline Coupons', 'woocommerce-ultimate-gift-card' ); ?></h3>
	<table class="form-table mwb_wgm_general_setting">
		<tbody>
			<tr valign="top">
				<td colspan="3" class="mwb_wgm_instructions_tabledata">	
					<h3><?php _e( 'Instructions', 'woocommerce-ultimate-gift-card' ); ?></h3>
					<p> 1- <?php _e( 'It just provide you the way from where you can import your coupons in bulk and can provide them Manually to your Customers. You need to choose a CSV file and click Import', 'woocommerce-ultimate-gift-card' ); ?></p>
					<p>2- <?php _e( 'CSV for Offline Coupons must have 4 columns in this order ( Coupon Code, Expiry Date, Usage Limit, Price. Also first row must be the respective headings. )', 'woocommerce-ultimate-gift-card' ); ?> </p>
					<p>3- <?php _e( 'You may leave Expiry Date field empty if you want to set your gift coupons with "No Expiration". The Expiry Date format must be in (YYYY-MM-DD), also may leave Usage Limit for setting this for "No Usage Limit".', 'woocommerce-ultimate-gift-card' ); ?> </p>
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Choose a CSV file:', 'woocommerce-ultimate-gift-card' ); ?>
				</th>
				<td>
					<input class="mwb_wgm_csv_offlinecoupon_import" name="offlinecoupon_csv_import" id="offlinecoupon_csv_import" type="file" size="25" value="" aria-required="true" /> 
					
					<input type="hidden" value="134217728" name="max_file_size">
					<small><?php echo _e( 'Maximum size:128 MB', 'woocommerce-ultimate-gift-card' ); ?></small>
				</td>
				<td>
					<a href="<?php echo MWB_WGM_URL . 'uploads/mwb_wgm_offline_coupon_import.csv'; ?>"><?php _e( 'Export Demo CSV', 'woocommerce-ultimate-gift-card' ); ?>
						<span class="mwb_sample_export"><img src="<?php echo MWB_WGM_URL . 'assets/images/download.png'; ?>"></span>
					</a>
				</td>
			</tr>
			<tr>
				<td>
					<p><input name="mwb_wgm_csv_offlinecoupon_import" id = "mwb_wgm_csv_offlinecoupon_import" class="button-primary woocommerce-save-button" type="submit" value="<?php _e( 'Import', 'woocommerce-ultimate-gift-card' ); ?>"/></p>
				</td><td></td><td></td>								
			</tr>
		</tbody>
	</table>
	</div>
	<?php
	if ( isset( $_POST['mwb_wgm_csv_offlinecoupon_import'] ) && ! empty( $_POST['mwb_wgm_csv_offlinecoupon_import'] ) ) {
		if ( ! empty( $_FILES['offlinecoupon_csv_import']['tmp_name'] ) ) {
				$csv_mimetypes = array(
					'text/csv',
					'application/csv',
					'text/comma-separated-values',
					'application/excel',
					'application/vnd.ms-excel',
					'application/vnd.msexcel',
					'application/octet-stream',
				);
				if ( in_array( $_FILES['offlinecoupon_csv_import']['type'], $csv_mimetypes ) ) {
					$coupon_imported = false;
					$file = $_FILES['offlinecoupon_csv_import']['tmp_name'];
					if ( file_exists( $file ) ) {
						$row = 1;
						ini_set( 'auto_detect_line_endings', true );
						$handle = fopen( $file, 'r' );
						$csv_data = array();
						if ( $handle ) {
							while ( ( $data = fgetcsv( $handle, 1000, ',' ) ) !== false ) {
								$num_of_col = count( $data );
								if ( $row == 1 ) {
									$row++;
									continue;
								}
								if ( $num_of_col == 4 && isset( $data ) && ! empty( $data ) ) {
									$coupon_code = sanitize_text_field( $data[0] );
									$coupon_exp = sanitize_text_field( $data[1] );
									$usage_limit = sanitize_text_field( $data[2] );
									$coupon_amount = sanitize_text_field( $data[3] );
									if ( mwb_wgm_generate_coupon_via_csv( $coupon_code, $coupon_exp, $usage_limit, $coupon_amount ) ) {
										$display_message['class'] = 'notice-success';
										$display_message['message'] = "<b style='color:green;'>" . __( 'Coupons imported successfully.', 'woocommerce-ultimate-gift-card' ) . '</b>';
									} else {
										$display_message['class'] = 'notice-error';
										$display_message['message'] = "<b style='color:red;'>" . __( 'Fail due to some error', 'woocommerce-ultimate-gift-card' ) . '</b>';
									}
								} else {
									$display_message['class'] = 'notice-error';
									$display_message['message'] = "<b style='color:red;'>" . __( 'Columns are not appropriate.', 'woocommerce-ultimate-gift-card' ) . '</b>';
								}
							}
						} else {
							$display_message['class'] = 'notice-error';
							$display_message['message'] = "<b style='color:red;'>" . __( 'File cannot be opened.', 'woocommerce-ultimate-gift-card' ) . '</b>';
						}
					} else {
						$display_message['class'] = 'notice-error';
						$display_message['message'] = "<b style='color:red;'>" . __( 'File does not exist.', 'woocommerce-ultimate-gift-card' ) . '</b>';
					}
				} else {
					$display_message['class'] = 'notice-error';
					$display_message['message'] = "<b style='color:red;'>" . __( 'File type not supported.', 'woocommerce-ultimate-gift-card' ) . '</b>';
				}
		} else {
			$display_message['class'] = 'notice-error';
			$display_message['message'] = "<b style='color:red;'>" . __( 'Please choose a valid file', 'woocommerce-ultimate-gift-card' ) . '</b>';
		}
	}
	?>
		<h1 class="wp-heading-inline" id="mwb_wgm_add_new_card_heading"><?php _e( 'Offline Giftcard List', 'woocommerce-ultimate-gift-card' ); ?></h1><br>
		<?php
		if ( isset( $display_message ) && $display_message != null ) {

			?>
					<div class="notice <?php echo $display_message['class']; ?> is-dismissible"> 
						<p><strong><?php echo $display_message['message']; ?></strong></p>
						<button type="button" class="notice-dismiss">
						<span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'woocommerce-ultimate-gift-card' ); ?></span>
						</button>
					</div>
				<?php
		}

		?>
		<table class="form-table">
			<tr valign="top">
				<td class="forminput">
					
					<h3><?php _e( 'Instructions', 'woocommerce-ultimate-gift-card' ); ?></h3>
					<p> 1- <?php _e( 'Import Offline Gift Card CSV for sending offline gift cards . You need to choose a CSV file and click Upload.', 'woocommerce-ultimate-gift-card' ); ?></p>
					<p>2- <?php _e( 'CSV for Offline Gift Card must have 5 columns in this order ( To, From, Message, Price, Schedule Date. Also first row must be the respective headings. )', 'woocommerce-ultimate-gift-card' ); ?> </p>
					<p>3- <?php _e( 'You may leave Schedule Date field empty if you want to send gift card today. The Schedule Date format must be in (YYYY-MM-DD).', 'woocommerce-ultimate-gift-card' ); ?> </p>
				</td>
			</tr>
			<tr>
				<td>
					<table class="widefat">
						<tbody>
							<tr>
								<th scope="row" class="titledesc">
									<label for="mwb_wgm_offline_gift_template"><?php _e( 'Giftcard', 'woocommerce-ultimate-gift-card' ); ?></label>
								</th>
								<td class="forminp forminp-text">
									<?php
										$attribute_description = __( 'Select the Gift card product.', 'woocommerce-ultimate-gift-card' );
										echo wc_help_tip( $attribute_description );
									?>
									<label for="mwb_wgm_offline_gift_template">
										<?php
										if ( isset( $gift_products ) && ! empty( $gift_products ) ) {
											?>
											<select name="mwb_wgm_offline_gift_template" id="mwb_wgm_offline_gift_template" class="input-text mwb_wgm_new_woo_ver_style_select">
											<?php
											foreach ( $gift_products as $id => $gift_product ) {
												?>
												<option value="<?php echo $id; ?>"><?php echo $gift_product; ?></option>										
												<?php
											}
											?>
											</select>
											<?php
										} else {
											_e( 'No Gift Card Product Present', 'woocommerce-ultimate-gift-card' );
										}
										?>
										</select>
									</label>						
								</td>
							</tr>
							<tr>
								<th><?php echo _e( 'Choose a CSV file:', 'woocommerce-ultimate-gift-card' ); ?>
								</th>
								<td>
									<input class="mwb_wgm_csv_custom_import" name="csv_import" id="csv_import" type="file" size="25" value="" aria-required="true" /> 
									
									<input type="hidden" value="134217728" name="max_file_size">
									<small><?php echo _e( 'Maximum size:128 MB', 'woocommerce-ultimate-gift-card' ); ?></small>
								</td>
								<td>
									<a href="<?php echo MWB_WGM_URL . 'uploads/mwb_gift_card_sample.csv'; ?>"><?php _e( 'Export Demo CSV', 'woocommerce-ultimate-gift-card' ); ?>
										<span class="mwb_sample_export"><img src="<?php echo MWB_WGM_URL . 'assets/images/download.png'; ?>"></img></span>
									</a>
								</td>
							</tr>
							<tr>
								<td>
									<p><input name="mwb_wgm_csv_custom_import" id = "mwb_wgm_import_button" class="button-primary woocommerce-save-button" type="submit" value="<?php _e( 'Import', 'woocommerce-ultimate-gift-card' ); ?>" name="mwb_wgm_import_button"/></p>
								</td>								
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</table>
		<a id="mwb_wgm_add_new_card_button" class="page-title-action button button-primary button-large" href="<?php echo MWB_WGM_HOME_URL; ?>admin.php?page=mwb-wgc-setting&tab=offline-giftcard&action=add"><?php _e( 'Add New', 'woocommerce-ultimate-gift-card' ); ?></a>
		  <form method="post">
			<input type="hidden" name="page" value="ttest_list_table">
			<?php
			$myListTable = new TT_Example_List_Table();
			$myListTable->prepare_items();
			$myListTable->display();
			?>
		</form>
	<?php
}
function mwb_wgm_generate_coupon_via_csv( $coupon_code, $coupon_exp, $usage_limit, $coupon_amount ) {
		$the_coupon = new WC_Coupon( $coupon_code );
		$woo_ver = WC()->version;
	if ( $woo_ver < '3.0.0' ) {
		$coupon_id = $the_coupon->id;
	} else {
		$coupon_id = $the_coupon->get_id();
	}
	if ( isset( $coupon_id ) && $coupon_id == 0 ) {

		$coupon_description = 'Imported Offline Coupon';
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
		update_post_meta( $new_coupon_id, 'discount_type', 'fixed_cart' );
		update_post_meta( $new_coupon_id, 'coupon_amount', $coupon_amount );

		$woo_ver = WC()->version;

		if ( $woo_ver < '3.6.0' ) {
			update_post_meta( $new_coupon_id, 'expiry_date', $coupon_exp );
		} else {

			$expirydate = strtotime( $coupon_exp );
			update_post_meta( $new_coupon_id, 'date_expires', $expirydate );
		}
		update_post_meta( $new_coupon_id, 'mwb_wgm_imported_offline', 'yes' );
		update_post_meta( $new_coupon_id, 'usage_limit', $usage_limit );
		return true;
	} else {
		return false;
	}
}
?>
