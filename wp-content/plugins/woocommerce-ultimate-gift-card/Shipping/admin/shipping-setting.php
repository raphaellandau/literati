<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$saved = false;
$mwb_down = false;
if ( isset( $_POST['mwb_wgm_shipping_setting_save'] ) ) {

	unset( $_POST['mwb_wgm_shipping_setting_save'] );
	$mwb_wgm_email_to_recipient = isset( $_POST['mwb_wgm_email_to_recipient'] ) ? 1 : 0;
	$mwb_wgm_shipping = isset( $_POST['mwb_wgm_shipping'] ) ? 1 : 0;
	$mwb_wgm_downloadable = isset( $_POST['mwb_wgm_downloadable'] ) ? 1 : 0;

	$mwb_wgm_customer_selection = array(
		'Email_to_recipient' => $mwb_wgm_email_to_recipient,
		'Downloadable' => $mwb_wgm_downloadable,
		'Shipping' => $mwb_wgm_shipping,
	);

	if ( ! isset( $_POST['mwb_wgm_send_giftcard'] ) ) {
		$_POST['mwb_wgm_send_giftcard'] = 'normal_mail';
	} elseif ( isset( $_POST['mwb_wgm_send_giftcard'] ) && $_POST['mwb_wgm_send_giftcard'] == 'normal_mail' ) {
		$_POST['mwb_wgm_send_giftcard'] = 'normal_mail';
	} elseif ( isset( $_POST['mwb_wgm_send_giftcard'] ) && $_POST['mwb_wgm_send_giftcard'] == 'download' ) {
		$_POST['mwb_wgm_send_giftcard'] = 'download';
	} elseif ( isset( $_POST['mwb_wgm_send_giftcard'] ) && $_POST['mwb_wgm_send_giftcard'] == 'shipping' ) {
		$_POST['mwb_wgm_send_giftcard'] = 'shipping';
	} elseif ( isset( $_POST['mwb_wgm_send_giftcard'] ) && $_POST['mwb_wgm_send_giftcard'] == 'customer_choose' ) {
		$_POST['mwb_wgm_send_giftcard'] = 'customer_choose';
	}
	if ( ! isset( $_POST['mwb_wgm_other_setting_giftcard_subject_shipping'] ) ) {
		$_POST['mwb_wgm_other_setting_giftcard_subject_shipping'] = '';
	}
	if ( ! isset( $_POST['mwb_wgm_general_cart_shipping_enable'] ) ) {
		$_POST['mwb_wgm_general_cart_shipping_enable'] = 'off';
	}
	$postdata = $_POST;

	foreach ( $postdata as $key => $data ) {
		update_option( $key, $data );
		if ( ! $mwb_down ) {
			$saved = true;
		}
	}
	$mwb_wgm_method_enable = get_option( 'mwb_wgm_send_giftcard', false );

	if ( $mwb_wgm_method_enable == 'customer_choose' ) {

		if ( $mwb_wgm_email_to_recipient == '0' && $mwb_wgm_shipping == '0' && $mwb_wgm_downloadable == '0' ) {
			$mwb_wgm_customer_selection = array(
				'Email_to_recipient' => '1',
				'Downloadable' => '0',
				'Shipping'  => '0',
			);
		}
		update_option( 'mwb_wgm_customer_selection', $mwb_wgm_customer_selection );
	} else {
		$mwb_wgm_customer_selection = array(
			'Email_to_recipient' => '0',
			'Downloadable' => '0',
			'Shipping'  => '0',
		);
		update_option( 'mwb_wgm_customer_selection', $mwb_wgm_customer_selection );
	}
}
if ( $saved ) {
	?>
	<div class="notice notice-success is-dismissible"> 
		<p><strong><?php _e( 'Settings saved', 'woocommerce-ultimate-gift-card' ); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php _e( 'Dismiss this notice', 'woocommerce-ultimate-gift-card' ); ?></span>
		</button>
	</div>
	<?php
}

$gift_cart_ship = get_option( 'mwb_wgm_general_cart_shipping_enable', false );
$mwb_wgm_method_enable = get_option( 'mwb_wgm_send_giftcard', 'normal_mail' );

$mwb_wgm_customer_selection = get_option( 'mwb_wgm_customer_selection', false );

?>
<div class="mwb_table">
<table class="mwb_shippingaddon form-table mwb_wgm_general_setting">
	<tbody>		
		<tr valign="top">		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_email_to_recipient_setting_enable"><?php _e( 'Enable Email To Recipient', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box to enable normal functionality for sending mails to recipients on Gift Card Products.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_email_to_recipient_setting_enable">
					<input type="radio" <?php echo ( $mwb_wgm_method_enable == 'normal_mail' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_send_giftcard" value="normal_mail"class="mwb_wgm_send_giftcard" id="mwb_wgm_email_to_recipient_setting_enable"><?php _e( 'Enable Email To Recipient.', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_downladable_setting_enable"><?php _e( 'Enable Downloadable', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box to enable downladable feature for  Gift Card Products.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_downladable_setting_enable">
					<input type="radio" <?php echo ( $mwb_wgm_method_enable == 'download' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_send_giftcard" value="download" class="mwb_wgm_send_giftcard" id="mwb_wgm_downladable_setting_enable"><?php _e( 'Enable Downloadable feature', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_shipping_setting_enable"><?php _e( 'Enable Shipping on Gift Card', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box to enable Shipping on Gift Card Products.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_shipping_setting_enable">
					<input type="radio" <?php echo ( $mwb_wgm_method_enable == 'shipping' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_send_giftcard" class="mwb_wgm_send_giftcard" value="shipping" id="mwb_wgm_shipping_setting_enable"><?php _e( 'Enable Shipping for Gift Card.', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_customer_choose_setting_enable"><?php _e( 'Allow customer to choose', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box to provide the facility to select the above three methods for giftcard products', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_customer_choose_setting_enable">
					<input type="radio" <?php echo ( isset( $mwb_wgm_method_enable ) && $mwb_wgm_method_enable == 'customer_choose' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_send_giftcard" class="mwb_wgm_send_giftcard" value="customer_choose" id="mwb_wgm_customer_choose_setting_enable"><?php _e( 'Customer can select below methods', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_customer_select_setting_enable"><?php _e( 'Customer can select', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box to allow customer to select methods on Gift Card Products.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_email_to_recipient">
					<input type="checkbox" <?php checked( isset( $mwb_wgm_customer_selection['Email_to_recipient'] ) ? $mwb_wgm_customer_selection['Email_to_recipient'] : 0 ); ?> name="mwb_wgm_email_to_recipient" id="mwb_wgm_email_to_recipient" class="input-text"><?php _e( 'Email To Recipient', 'woocommerce-ultimate-gift-card' ); ?>
				</label>
				<label for="mwb_wgm_downloadable">
					<input type="checkbox" <?php checked( isset( $mwb_wgm_customer_selection['Downloadable'] ) ? $mwb_wgm_customer_selection['Downloadable'] : 0 ); ?> name="mwb_wgm_downloadable" id="mwb_wgm_downloadable" class="input-text"><?php _e( 'Downloadable', 'woocommerce-ultimate-gift-card' ); ?>
				</label>
				<label for="mwb_wgm_shipping">
					<input type="checkbox" <?php checked( isset( $mwb_wgm_customer_selection['Shipping'] ) ? $mwb_wgm_customer_selection['Shipping'] : 0 ); ?> name="mwb_wgm_shipping" id="mwb_wgm_shipping" class="input-text"><?php _e( 'Shipping', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_cart_shipping_enable"><?php _e( 'Apply Coupon on Shipping & Tax', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box to enable the Coupon to be applied on Shipping and Tax. The coupon will be applied on the Cart Total.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_general_cart_shipping_enable">
					<input type="checkbox" <?php echo ( $gift_cart_ship == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_general_cart_shipping_enable" id="mwb_wgm_general_cart_shipping_enable" class="input-text"> <?php _e( 'Enable this field to apply Coupon on Cart Total instead of Cart Subtotal.', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
	</tbody>
</table>
</div>
<p class="submit">
	<input type="submit" value="<?php _e( 'Save changes', 'woocommerce-ultimate-gift-card' ); ?>" class="button-primary woocommerce-save-button" name="mwb_wgm_shipping_setting_save" id="mwb_wgm_shipping_setting_save">
</p>
<div class="clear"></div>
