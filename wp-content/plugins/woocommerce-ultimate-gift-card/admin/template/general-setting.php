<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( isset( $_GET['action'] ) && $_GET['action'] == 'not_now' ) {
	$url = admin_url( 'admin.php?page=mwb-wgc-setting' );
	header( "Location: $url" );
}
if ( isset( $_POST['mwb_wgm_general_setting_save'] ) ) {

	unset( $_POST['mwb_wgm_general_setting_save'] );
	if ( ! isset( $_POST['mwb_wgm_general_setting_enable'] ) ) {
		$_POST['mwb_wgm_general_setting_enable'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_general_setting_tax_cal_enable'] ) ) {
		$_POST['mwb_wgm_general_setting_tax_cal_enable'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_general_setting_shop_page_enable'] ) ) {
		$_POST['mwb_wgm_general_setting_shop_page_enable'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_general_setting_giftcard_individual_use'] ) ) {
		$_POST['mwb_wgm_general_setting_giftcard_individual_use'] = 'no';
	}
	if ( ! isset( $_POST['mwb_wgm_general_setting_giftcard_freeshipping'] ) ) {
		$_POST['mwb_wgm_general_setting_giftcard_freeshipping'] = 'no';
	}
	if ( ! isset( $_POST['mwb_wgm_general_setting_giftcard_applybeforetx'] ) ) {
		$_POST['mwb_wgm_general_setting_giftcard_applybeforetx'] = 'no';
	}
	if ( ! isset( $_POST['mwb_wgm_general_setting_giftcard_payment'] ) ) {
		$_POST['mwb_wgm_general_setting_giftcard_payment'] = '';
	}
	if ( ! isset( $_POST['mwb_wgm_general_setting_giftcard_minspend'] ) ) {
		$_POST['mwb_wgm_general_setting_giftcard_minspend'] = '';
	}
	if ( ! isset( $_POST['mwb_wgm_general_setting_enable_selected_date'] ) ) {
		$_POST['mwb_wgm_general_setting_enable_selected_date'] = 'off';

	}
	if ( ! isset( $_POST['mwb_wgm_general_setting_categ_enable'] ) ) {
		$_POST['mwb_wgm_general_setting_categ_enable'] = 'off';
	}
	do_action( 'mwb_wgm_general_setting_save' );
	$data_1 = '';
	$postdata = $_POST;

	foreach ( $postdata as $key => $data ) {
		if ( isset( $data ) && $data != null ) {
			if ( $key == 'mwb_wgm_general_setting_enable_selected_format' ) {
				if ( $data == 'yy/mm/dd' ) {
					$data_1 = 'Y/m/d';
				} elseif ( $data == 'mm/dd/yy' ) {
					$data_1 = 'm/d/Y';
				} elseif ( $data == 'd M, yy' ) {
					$data_1 = 'd M, Y';
				} elseif ( $data == 'DD, d MM, yy' ) {
					$data_1 = 'l, d F, Y';
				} elseif ( $data == 'yy-mm-dd' ) {
					$data_1 = 'Y-m-d';
				} elseif ( $data == 'dd/mm/yy' ) {
					$data_1 = 'd/m/Y';
				} elseif ( $data == 'd.m.Y' ) {
					$data_1 = 'd.m.Y';
				}
				update_option( $key . '_1', $data_1 );
			}
			update_option( $key, $data );
		} elseif ( $data == null ) {
			update_option( $key, $data );
			update_option( $key . '_1', '' );
		}
	}
	?>
	<div class="notice notice-success is-dismissible"> 
		<p><strong><?php _e( 'Settings saved', 'woocommerce-ultimate-gift-card' ); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php _e( 'Dismiss this notice', 'woocommerce-ultimate-gift-card' ); ?></span>
		</button>
	</div>
	<?php
}

$giftcard_selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_date', false );
$selected_date = get_option( 'mwb_wgm_general_setting_enable_selected_format', false );

$giftcard_enable = get_option( 'mwb_wgm_general_setting_enable', false );
$giftcard_tax_cal_enable = get_option( 'mwb_wgm_general_setting_tax_cal_enable', false );
$giftcard_individual_use = get_option( 'mwb_wgm_general_setting_giftcard_individual_use', false );
$giftcard_freeshipping = get_option( 'mwb_wgm_general_setting_giftcard_freeshipping', false );
$giftcard_prefix = get_option( 'mwb_wgm_general_setting_giftcard_prefix', false );
$giftcard_expiry = get_option( 'mwb_wgm_general_setting_giftcard_expiry', 0 );
$giftcard_use = get_option( 'mwb_wgm_general_setting_giftcard_use', 0 );

$giftcard_tax = get_option( 'mwb_wgm_general_setting_giftcard_applybeforetx', false );
$giftcard_minspend = get_option( 'mwb_wgm_general_setting_giftcard_minspend', false );
$giftcard_maxspend = get_option( 'mwb_wgm_general_setting_giftcard_maxspend', false );
$giftcard_shop_page = get_option( 'mwb_wgm_general_setting_shop_page_enable', false );

$giftcard_payment_gateways = get_option( 'mwb_wgm_general_setting_giftcard_payment', array() );
$giftcard_coupon_length = get_option( 'mwb_wgm_general_setting_giftcard_coupon_length', false );

$gift_categ_enable = get_option( 'mwb_wgm_general_setting_categ_enable', false );
$giftcard_available_gateways = array();
$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
if ( isset( $available_gateways ) ) {
	foreach ( $available_gateways as $key => $available_gateway ) {
		$giftcard_available_gateways[ $key ] = $available_gateway->title;
	}
}

?>

<div class="mwb_table">
<table class="form-table mwb_wgm_general_setting">
	<tbody>
		<tr valign="top">
		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_enable"><?php _e( 'Enable', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribut_description = __( 'Check this box to enable giftcard', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribut_description );
				?>
				<label for="mwb_wgm_general_setting_enable">
					<input type="checkbox" <?php echo ( $giftcard_enable == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_general_setting_enable" id="mwb_wgm_general_setting_enable" class="input-text"> <?php _e( 'Enable WooCommerce Gift Card', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_tax_cal_enable"><?php _e( 'Enable Tax', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box to enable tax for giftcard product.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_general_setting_tax_cal_enable">
					<input type="checkbox" <?php echo ( $giftcard_tax_cal_enable == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_general_setting_tax_cal_enable" id="mwb_wgm_general_setting_tax_cal_enable" class="input-text"> <?php _e( 'Enable Tax Calculation for Gift Card', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_tax_cal_enable"><?php _e( 'Enable Date feature', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box to enable giftcard send to receiver on selected date.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_general_setting_enable_selected_date">
					<input type="checkbox" <?php echo ( $giftcard_selected_date == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_general_setting_enable_selected_date" id="mwb_wgm_general_setting_enable_selected_date" class="input-text"> <?php _e( 'Enable Giftcard Product send on selected date', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_enable_selected_format"><?php _e( 'Date format', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Select the date format which is used on front end.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_general_setting_enable_selected_format">
					<select name="mwb_wgm_general_setting_enable_selected_format" class="mwb_wgm_new_woo_ver_style_select">
						<option value=""><?php _e( 'Select Date Format', 'woocommerce-ultimate-gift-card' ); ?></option>
						<option 
						<?php
						if ( isset( $selected_date ) ) {
							if ( $selected_date == 'yy/mm/dd' ) {
								?>
							selected="selected"
								<?php
							}
						}
						?>
								 value="yy/mm/dd">yyyy/mm/dd</option>
						<option 
						<?php
						if ( isset( $selected_date ) ) {
							if ( $selected_date == 'mm/dd/yy' ) {
								?>
							selected="selected"
								<?php
							}
						}
						?>
								 value="mm/dd/yy">mm/dd/yyyy</option>
						<option 
						<?php
						if ( isset( $selected_date ) ) {
							if ( $selected_date == 'd M, yy' ) {
								?>
							selected="selected"
								<?php
							}
						}
						?>
								 value="d M, yy">d M, yy</option>
						<option 
						<?php
						if ( isset( $selected_date ) ) {
							if ( $selected_date == 'DD, d MM, yy' ) {
								?>
							selected="selected"
								<?php
							}
						}
						?>
								 value="DD, d MM, yy">DD, d MM, yy</option>
						<option 
						<?php
						if ( isset( $selected_date ) ) {
							if ( $selected_date == 'yy-mm-dd' ) {
								?>
							selected="selected"
								<?php
							}
						}
						?>
								 value="yy-mm-dd">yy-mm-dd</option>
						<option 
						<?php
						if ( isset( $selected_date ) ) {
							if ( $selected_date == 'dd/mm/yy' ) {
								?>
							selected="selected"
								<?php
							}
						}
						?>
								 value="dd/mm/yy">dd/mm/yyyy</option>
						<option 
						<?php
						if ( isset( $selected_date ) ) {
							if ( $selected_date == 'd.m.Y' ) {
								?>
							selected="selected"
								<?php
							}
						}
						?>
								 value="d.m.Y">d.m.Y</option>
					</select>
				</label>						
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_shop_page_enable"><?php _e( 'Enable Listing Shop Page', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box to enable giftcard product listing on shop page.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_general_setting_shop_page_enable">
					<input type="checkbox" <?php echo ( $giftcard_shop_page == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_general_setting_shop_page_enable" id="mwb_wgm_general_setting_shop_page_enable" class="input-text"> <?php _e( 'Enable Giftcard Product listing on shop page', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_giftcard_individual_use"><?php _e( 'Individual Use', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box if the Giftcard Coupon cannot be used in conjunction with other Giftcards/Coupons.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_general_setting_giftcard_individual_use">
					<input type="checkbox" <?php echo ( $giftcard_individual_use == 'yes' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_general_setting_giftcard_individual_use" id="mwb_wgm_general_setting_giftcard_individual_use" class="input-text" value="yes"> <?php _e( 'Allow Giftcard to use Individually', 'woocommerce-ultimate-gift-card' ); ?>
				</label>
				
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_giftcard_freeshipping"><?php _e( 'Free Shipping', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box if the coupon grants free shipping. A free shipping method must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_general_setting_giftcard_freeshipping">
					<input type="checkbox" <?php echo ( $giftcard_freeshipping == 'yes' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_general_setting_giftcard_freeshipping" id="mwb_wgm_general_setting_giftcard_freeshipping" class="input-text" value="yes"> <?php _e( 'Allow Giftcard on Free Shipping', 'woocommerce-ultimate-gift-card' ); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_giftcard_applybeforetx"><?php _e( 'Giftcard Before Tax Calculation ', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box if Giftcard Coupon is applied after amount calculation.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_general_setting_giftcard_applybeforetx">
					<input type="checkbox" <?php echo ( $giftcard_tax == 'yes' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_general_setting_giftcard_applybeforetx" id="mwb_wgm_general_setting_giftcard_applybeforetx" class="input-text" value="yes"> <?php _e( 'Apply Giftcard Before Tax', 'woocommerce-ultimate-gift-card' ); ?>
				</label>
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_giftcard_coupon_length"><?php _e( 'Giftcard Coupon Length', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Enter giftcard coupon length excluding the prefix.(Minimum length is set to 5)', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="number" min="5" max="10" value="<?php echo $giftcard_coupon_length; ?>" name="mwb_wgm_general_setting_giftcard_coupon_length" id="mwb_wgm_general_setting_giftcard_coupon_length" class="input-text mwb_wgm_new_woo_ver_style_text" > 	
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_giftcard_prefix"><?php _e( 'Giftcard Prefix', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Enter Gift Card Prefix. Ex: PREFIX_CODE', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="text" value="<?php echo $giftcard_prefix; ?>" name="mwb_wgm_general_setting_giftcard_prefix" id="mwb_wgm_general_setting_giftcard_prefix" class="input-text mwb_wgm_new_woo_ver_style_text" style="width:160px"> 	
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_giftcard_expiry"><?php _e( 'Giftcard Expiry After Days', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Enter number of days after purchased Giftcard is expired. Keep value "1" for one day expiry when order is completed. Keep value "0" for no expiry.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="number" min="0" value="<?php echo $giftcard_expiry; ?>" name="mwb_wgm_general_setting_giftcard_expiry" id="mwb_wgm_general_setting_giftcard_expiry" class="input-text mwb_wgm_new_woo_ver_style_text" > 	
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_giftcard_minspend"><?php _e( 'Minimum Spend', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'This field allows you to set the minimum spend (subtotal, including taxes) allowed to use the Giftcard coupon.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="number" min="0" value="<?php echo $giftcard_minspend; ?>" name="mwb_wgm_general_setting_giftcard_minspend" id="mwb_wgm_general_setting_giftcard_minspend" class="input-text mwb_wgm_new_woo_ver_style_text" > 	
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_giftcard_maxspend"><?php _e( 'Maximum Spend', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'This field allows you to set the maximum spend (subtotal, including taxes) allowed when using the Giftcard coupon.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="number" min="0" value="<?php echo $giftcard_maxspend; ?>" name="mwb_wgm_general_setting_giftcard_maxspend" id="mwb_wgm_general_setting_giftcard_maxspend " class="input-text mwb_wgm_new_woo_ver_style_text" > 	
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_giftcard_use"><?php _e( 'Giftcard No of time usage', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'How many times this coupon can be used before Giftcard is void.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="number"  min="0" value="<?php echo $giftcard_use; ?>" name="mwb_wgm_general_setting_giftcard_use" id="mwb_wgm_general_setting_giftcard_use" class="input-text mwb_wgm_new_woo_ver_style_text" > 	
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_giftcard_payment"><?php _e( 'Enable Payment Gateways for Giftcard', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php

				$attribute_description = __( 'If you want to enable selected payment gateways then choose those payment gateways here Otherwise default payment gateways is enabled for giftcard.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				if ( isset( $giftcard_available_gateways ) && ! empty( $giftcard_available_gateways ) ) {
					?>
					<select name="mwb_wgm_general_setting_giftcard_payment[]" id="mwb_wgm_general_setting_giftcard_payment" multiple>
					<?php
					foreach ( $giftcard_available_gateways as $key => $giftcard_available_gateway ) {
							$selected = '';
						if ( is_array( $giftcard_payment_gateways ) && in_array( $key, $giftcard_payment_gateways ) ) {

							$selected = "selected='selected'";
						}
						if ( $key == 'cod' ) {
							$giftcard_available_gateway .= '[Testing Purpose]';
						}
						?>
							<option <?php echo $selected; ?> value="<?php echo $key; ?>"><?php echo $giftcard_available_gateway; ?></option>
							<?php
					}
					?>
					</select>
					<p><?php _e( 'Note: Enabling COD is just for testing purpose for Shipping functionality, Try to avoid it if Shipping functionality is not enable', 'woocommerce-ultimate-gift-card' ); ?></p>
					<?php
				}
				?>
			</td>
		</tr>
		<tr valign="top" class="mwb_categ_field">
		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_categ_enable"><?php _e( 'Disable Category', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box to do not assign Gift Card cateogory to Gift Card product forcefully.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_general_setting_categ_enable">
					<input type="checkbox" <?php echo ( $gift_categ_enable == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_general_setting_categ_enable" id="mwb_wgm_general_setting_categ_enable" class="input-text"> <?php _e( 'Enable it for changing the giftcard category.', 'woocommerce-ultimate-gift-card' ); ?>
				</label>
				<p><?php _e( 'Note: Check this box only if you want to change the category for Giftcard Product.You have to select the category everytime you create a Gift Card product. Default Gift Card category will not be assigned automatically.', 'woocommerce-ultimate-gift-card' ); ?></p>						
			</td>
		</tr>		
		<?php
		do_action( 'mwb_wgm_general_setting' );
		?>
		
	</tbody>
</table>
</div>
<div class="clear"></div>
<p class="submit">
	<input type="submit" value="<?php _e( 'Save changes', 'woocommerce-ultimate-gift-card' ); ?>" class="button-primary woocommerce-save-button" name="mwb_wgm_general_setting_save" id="mwb_wgm_general_setting_save" >
</p>
