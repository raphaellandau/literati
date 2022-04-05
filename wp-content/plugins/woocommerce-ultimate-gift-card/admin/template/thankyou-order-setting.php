<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $_POST['mwb_wgm_thankyouorder_setting_save_hidden'] ) ) {
	unset( $_POST['mwb_wgm_thankyouorder_setting_save_hidden'] );
	if ( ! isset( $_POST['mwb_wgm_thankyouorder_enable'] ) ) {
		$_POST['mwb_wgm_thankyouorder_enable'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_thankyouorder_type'] ) ) {
		$_POST['mwb_wgm_thankyouorder_type'] = 'mwb_wgm_fixed_thankyou';
	}
	if ( ! isset( $_POST['mwb_wgm_thankyouorder_minimum'] ) ) {
		$_POST['mwb_wgm_thankyouorder_minimum'] = array();
	}
	if ( ! isset( $_POST['mwb_wgm_thankyouorder_maximum'] ) ) {
		$_POST['mwb_wgm_thankyouorder_maximum'] = array();
	}
	if ( ! isset( $_POST['mwb_wgm_thankyouorder_current_type'] ) ) {
		$_POST['mwb_wgm_thankyouorder_current_type'] = array();
	}
	if ( ! isset( $_POST['mwb_wgm_thankyouorder_time'] ) ) {
		$_POST['mwb_wgm_thankyouorder_time'] = 'mwb_wgm_complete_status';
	}
	if ( ! isset( $_POST['mwb_wgm_thankyou_message'] ) ) {
		$_POST['mwb_wgm_thankyou_message'] = 'You have recieved a coupon [COUPONCODE], having amount of [COUPONAMOUNT] with the expiration date of [COUPONEXPIRY]';
	}
	do_action( 'mwb_wgm_thankyouorder_setting_save' );
	$postdata = $_POST;
	foreach ( $postdata as $key => $data ) {
		update_option( $key, $data );
	}
	?>
	<div class="notice notice-success is-dismissible"> 
		<p><strong><?php _e( 'Settings Saved Successfully!', 'woocommerce-ultimate-gift-card' ); ?></strong></p>
	</div>
	<?php
}
$thankyouorder_enable = get_option( 'mwb_wgm_thankyouorder_enable', false );
$thankyouorder_type = get_option( 'mwb_wgm_thankyouorder_type', 'mwb_wgm_fixed_thankyou' );
$thankyouorder_time = get_option( 'mwb_wgm_thankyouorder_time', 'mwb_wgm_complete_status' );
$thankyouorder_min = get_option( 'mwb_wgm_thankyouorder_minimum', array() );
$thankyouorder_max = get_option( 'mwb_wgm_thankyouorder_maximum', array() );
$thankyouorder_value = get_option( 'mwb_wgm_thankyouorder_current_type', array() );
$mwb_wgm_thankyouorder_number = get_option( 'mwb_wgm_thankyouorder_number', 1 );
$thnku_giftcard_expiry = get_option( 'mwb_wgm_thnku_giftcard_expiry', 0 );
$mwb_wgm_thankyou_message = get_option( 'mwb_wgm_thankyou_message', 'You have recieved a coupon [COUPONCODE], having amount of [COUPONAMOUNT] with the expiration date of [COUPONEXPIRY]' );
if ( empty( $mwb_wgm_thankyouorder_number ) ) {
	$mwb_wgm_thankyouorder_number = 1;
}
?>
<table class="form-table mwb_wgm_thankyouorder_setting wp-list-table widefat  striped">
	<tbody>	
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_thankyouorder_enable"><?php _e( 'Want to give ThankYou Gift coupon to your customers ?', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box to enable gift coupon for those customers who had placed orders in your site', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_thankyouorder_enable">
					<input type="checkbox" <?php echo ( $thankyouorder_enable == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_thankyouorder_enable" id="mwb_wgm_thankyouorder_enable" class="input-text"> <?php _e( 'Enable ThankYou Gift Coupon to Customers.', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_thankyouorder_time"><?php _e( 'Select the Order Status', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Select the status when the ThankYou Gift Coupon would be send', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_thankyouorder_time">
					<select name="mwb_wgm_thankyouorder_time" class="mwb_wgm_new_woo_ver_style_select">
						<option value="mwb_wgm_order_creation" <?php selected( $thankyouorder_time, 'mwb_wgm_order_creation' ); ?>><?php _e( 'Order Creation', 'woocommerce-ultimate-gift-card' ); ?></option>
						<option value="mwb_wgm_processing_status" <?php selected( $thankyouorder_time, 'mwb_wgm_processing_status' ); ?>><?php _e( 'Order is in Processing', 'woocommerce-ultimate-gift-card' ); ?></option>
						<option value="mwb_wgm_complete_status" <?php selected( $thankyouorder_time, 'mwb_wgm_complete_status' ); ?>><?php _e( 'Order is in Complete', 'woocommerce-ultimate-gift-card' ); ?></option>
					</select>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_thankyouorder_number"><?php _e( 'Number of Orders, after which the thankyou giftcard would sent', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Enter the number of orders, after that you want to give a thank you giftcard to your customers', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="number" min="1" value="<?php echo $mwb_wgm_thankyouorder_number; ?>" name="mwb_wgm_thankyouorder_number" id="mwb_wgm_thankyouorder_number" class="input-text mwb_wgm_new_woo_ver_style_text"> 	
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_thnku_giftcard_expiry"><?php _e( 'ThankYou Coupon Expiry', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Enter number of days for Coupon Expiry,  Keep value "1" for one day expiry after genarating coupon, Keep value "0" for no expiry.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="number" min="0" value="<?php echo $thnku_giftcard_expiry; ?>" name="mwb_wgm_thnku_giftcard_expiry" id="mwb_wgm_thnku_giftcard_expiry" class="input-text mwb_wgm_new_woo_ver_style_text" > 	
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_thankyouorder_type"><?php _e( 'Select ThankYou Gift Coupon Type', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Choose the ThankYou Gift Coupon Type for Customers', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_thankyouorder_type">
					<select name="mwb_wgm_thankyouorder_type" class="mwb_wgm_new_woo_ver_style_select">
						<option value="mwb_wgm_fixed_thankyou" <?php selected( $thankyouorder_type, 'mwb_wgm_fixed_thankyou' ); ?>><?php _e( 'Fixed', 'woocommerce-ultimate-gift-card' ); ?></option>
						<option value="mwb_wgm_percentage_thankyou" <?php selected( $thankyouorder_type, 'mwb_wgm_percentage_thankyou' ); ?>><?php _e( 'Percentage', 'woocommerce-ultimate-gift-card' ); ?></option>
					</select>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_thankyou_message"><?php _e( 'Enter a Thankyou Message for your customers', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'This message will print inside the Thankyou Giftcoupon Template', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<span class="description"><?php _e( 'You may use shortcodes [COUPONCODE], [COUPONAMOUNT] and [COUPONEXPIRY]', 'woocommerce-ultimate-gift-card' ); ?></span>
				<label for="mwb_wgm_thankyou_message">
					<textarea cols="35" rows="5" name="mwb_wgm_thankyou_message" id="mwb_wgm_thankyou_message" class="input-text" ><?php echo $mwb_wgm_thankyou_message; ?></textarea>
				</label>						
			</td>
		</tr>
		<tr valign="top" class="mwb_wgm_thankyouorder_row" style="display: none;">
			<th>
				<label for="mwb_wgm_thankyouorder_fields"><?php _e( 'Enter Coupon Amount within Order Range', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<table class="form-table wp-list-table widefat fixed striped">
					<tbody class="mwb_wgm_thankyouorder_tbody">	
						<tr valign="top">
							<th><?php _e( 'Minimum', 'woocommerce-ultimate-gift-card' ); ?></th>
							<th><?php _e( 'Maximum', 'woocommerce-ultimate-gift-card' ); ?></th>
							<?php
							if ( $thankyouorder_type == 'mwb_wgm_fixed_thankyou' ) {
								?>
										<th><?php _e( 'ThankYou Gift Coupon Amount', 'woocommerce-ultimate-gift-card' ); ?></th>
									<?php
							} else if ( $thankyouorder_type == 'mwb_wgm_percentage_thankyou' ) {
								?>
										<th><?php _e( 'ThankYou Gift Coupon Percentage(%)', 'woocommerce-ultimate-gift-card' ); ?></th>
									<?php
							}
							?>
							<th class="mwb_wgm_remove_thankyouorder_content"><?php _e( 'Action', 'woocommerce-ultimate-gift-card' ); ?></th>
						</tr>
						<?php
						if ( isset( $thankyouorder_min ) && $thankyouorder_min != null && isset( $thankyouorder_max ) && $thankyouorder_max != null && isset( $thankyouorder_value ) && $thankyouorder_value != null ) {
							if ( count( $thankyouorder_min ) == count( $thankyouorder_max ) && count( $thankyouorder_max ) == count( $thankyouorder_value ) ) {
								foreach ( $thankyouorder_min as $key => $value ) {
									?>
											<tr valign="top">
												<td class="forminp forminp-text">
													<label for="mwb_wgm_thankyouorder_minimum">
														<input type="text" name="mwb_wgm_thankyouorder_minimum[]" class="mwb_wgm_thankyouorder_minimum input-text wc_input_price" required="" placeholder = "No minimum" value="<?php echo $thankyouorder_min[ $key ]; ?>">
													</label>
												</td>
												<td class="forminp forminp-text">
													<label for="mwb_wgm_thankyouorder_maximum">
														<input type="text" name="mwb_wgm_thankyouorder_maximum[]" class="mwb_wgm_thankyouorder_maximum input-text wc_input_price" required="" placeholder = "No maximum" value="<?php echo $thankyouorder_max[ $key ]; ?>">
													</label>
												</td>
												<td class="forminp forminp-text">
													<label for="mwb_wgm_thankyouorder_current_type">
														<input type="text" name="mwb_wgm_thankyouorder_current_type[]" class="mwb_wgm_thankyouorder_current_type input-text wc_input_price" required=""  value="<?php echo $thankyouorder_value[ $key ]; ?>">
													</label>
												</td>							
												<td class="mwb_wgm_remove_thankyouorder_content forminp forminp-text">
													<input type="button" value="<?php _e( 'Remove', 'woocommerce-ultimate-gift-card' ); ?>" class="mwb_wgm_remove_thankyouorder button" >
												</td>
											</tr>
										<?php
								}
							}
						} else {
							?>
									<tr valign="top">
										<td class="forminp forminp-text">
											<label for="mwb_wgm_thankyouorder_minimum">
												<input type="text" name="mwb_wgm_thankyouorder_minimum[]" class="mwb_wgm_thankyouorder_minimum input-text wc_input_price" required="">
											</label>
										</td>
										<td class="forminp forminp-text">
											<label for="mwb_wgm_thankyouorder_maximum">
												<input type="text" name="mwb_wgm_thankyouorder_maximum[]" class="mwb_wgm_thankyouorder_maximum input-text wc_input_price" required="">
											</label>
										</td>
										<td class="forminp forminp-text">
											<label for="mwb_wgm_thankyouorder_current_type">
												<input type="text" name="mwb_wgm_thankyouorder_current_type[]" class="mwb_wgm_thankyouorder_current_type input-text wc_input_price" required="">
											</label>
										</td>							
										<td class="mwb_wgm_remove_thankyouorder_content forminp forminp-text">
											<input type="button" value="<?php _e( 'Remove', 'woocommerce-ultimate-gift-card' ); ?>" class="mwb_wgm_remove_thankyouorder button" >
										</td>
									</tr>
								<?php
						}
						?>
					</tbody>
				</table>
				<input type="button" value="<?php _e( 'Add More', 'woocommerce-ultimate-gift-card' ); ?>" class="mwb_wgm_add_more button" id="mwb_wgm_add_more">
			</td>
		</tr>
	</tbody>
</table>
<p class="submit">
	<input type="hidden" name="mwb_wgm_thankyouorder_setting_save_hidden">
	<input type="submit" value="<?php _e( 'Save changes', 'woocommerce-ultimate-gift-card' ); ?>" class="button-primary woocommerce-save-button" name="mwb_wgm_thankyouorder_setting_save" id="mwb_wgm_thankyouorder_setting_save" >
</p>
