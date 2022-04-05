<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( isset( $_POST['mwb_wgm_additional_setting_save'] ) ) {

	unset( $_POST['mwb_wgm_additional_setting_save'] );
	if ( ! isset( $_POST['mwb_wgm_addition_bcc_option_enable'] ) ) {
		$_POST['mwb_wgm_addition_bcc_option_enable'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_additional_apply_coupon_disable'] ) ) {
		$_POST['mwb_wgm_additional_apply_coupon_disable'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_additional_resend_disable'] ) ) {
		$_POST['mwb_wgm_additional_resend_disable'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_additional_sendtoday_disable'] ) ) {
		$_POST['mwb_wgm_additional_sendtoday_disable'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_additional_preview_disable'] ) ) {
		$_POST['mwb_wgm_additional_preview_disable'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_addition_pdf_enable'] ) ) {
		$_POST['mwb_wgm_addition_pdf_enable'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_other_setting_browse'] ) ) {
		$_POST['mwb_wgm_other_setting_browse'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_remove_validation_to'] ) ) {
		$_POST['mwb_wgm_remove_validation_to'] = 'off';
	}

	if ( ! isset( $_POST['mwb_wgm_remove_validation_to_name'] ) ) {
		$_POST['mwb_wgm_remove_validation_to_name'] = 'off';
	}

	if ( ! isset( $_POST['mwb_wgm_remove_validation_from'] ) ) {
		$_POST['mwb_wgm_remove_validation_from'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_remove_validation_msg'] ) ) {
		$_POST['mwb_wgm_remove_validation_msg'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_pdf_template_size'] ) ) {
		$_POST['mwb_wgm_pdf_template_size'] = 'A3';
	}
	if ( ! isset( $_POST['mwb_wgm_manually_increment_usage'] ) ) {
		$_POST['mwb_wgm_manually_increment_usage'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_custom_page_selection'] ) ) {
		$_POST['mwb_wgm_custom_page_selection'] = array();
	}
	if ( ! isset( $_POST['mwb_wgm_render_product_custom_page'] ) ) {
		$_POST['mwb_wgm_render_product_custom_page'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_hide_giftcard_notice'] ) ) {
		$_POST['mwb_wgm_hide_giftcard_notice'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_hide_giftcard_thumbnail'] ) ) {
		$_POST['mwb_wgm_hide_giftcard_thumbnail'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_disable_buyer_notification'] ) ) {
		$_POST['mwb_wgm_disable_buyer_notification'] = 'off';
	}

	if ( ! isset( $_POST['mwb_wgm_disable_quantity_field'] ) ) {
		$_POST['mwb_wgm_disable_quantity_field'] = 'off';
	}



	$postdata = $_POST;

	foreach ( $postdata as $key => $value ) {
		update_option( $key, $value );
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
$mwb_wgc_bcc_enable = get_option( 'mwb_wgm_addition_bcc_option_enable', false );
$mwb_wgm_apply_coupon_disable = get_option( 'mwb_wgm_additional_apply_coupon_disable', false );
$mwb_wgm_resend_disable = get_option( 'mwb_wgm_additional_resend_disable', false );
$mwb_wgm_sendtoday_disable = get_option( 'mwb_wgm_additional_sendtoday_disable', false );
$mwb_wgm_preview_disable = get_option( 'mwb_wgm_additional_preview_disable', false );
$mwb_wgm_pdf_enable = get_option( 'mwb_wgm_addition_pdf_enable', false );
$browse_enable = get_option( 'mwb_wgm_other_setting_browse', false );
$mwb_wgm_mail_to_recipient_text = stripslashes( get_option( 'mwb_wgm_mail_to_recipient_text', 'Email To Recipient' ) );
$mwb_wgm_shipping_text = stripslashes( get_option( 'mwb_wgm_shipping_text', 'Want To Ship Your Card' ) );
$mwb_wgm_downloadable_text = stripslashes( get_option( 'mwb_wgm_downloadable_text', 'You Print & Give To Recipient' ) );
$mwb_wgm_mail_to_recipient_desc = stripslashes( get_option( 'mwb_wgm_mail_to_recipient_desc', 'We will send it to recipient email address.' ) );
$mwb_wgm_downloadable_desc = stripslashes( get_option( 'mwb_wgm_downloadable_desc', 'After checking out, you can print your giftcard' ) );
$mwb_wgm_shipping_desc = stripslashes( get_option( 'mwb_wgm_shipping_desc', 'We will ship your card' ) );
$mwb_wgm_remove_validation_to = get_option( 'mwb_wgm_remove_validation_to', false );
$mwb_wgm_remove_validation_to_name = get_option( 'mwb_wgm_remove_validation_to_name', 'on' );
$mwb_wgm_remove_validation_from = get_option( 'mwb_wgm_remove_validation_from', false );
$mwb_wgm_remove_validation_msg = get_option( 'mwb_wgm_remove_validation_msg', false );
$mwb_wgm_pdf_template_size = get_option( 'mwb_wgm_pdf_template_size', 'A3' );
$mwb_wgm_manually_increment_usage = get_option( 'mwb_wgm_manually_increment_usage', false );
$mwb_wgm_selected_custom_page = get_option( 'mwb_wgm_custom_page_selection', array() );
$mwb_wgm_render_product_custom_page = get_option( 'mwb_wgm_render_product_custom_page', 'off' );
$mwb_wgm_hide_giftcard_thumbnail = get_option( 'mwb_wgm_hide_giftcard_thumbnail', 'off' );
$mwb_wgm_hide_giftcard_notice = get_option( 'mwb_wgm_hide_giftcard_notice', 'off' );
$mwb_wgm_disable_buyer_notification = get_option( 'mwb_wgm_disable_buyer_notification', 'off' );

$mwb_wgm_disable_quantity_field = get_option( 'mwb_wgm_disable_quantity_field', 'off' );




$mwb_wgm_change_admin_email_for_shipping = get_option( 'mwb_wgm_change_admin_email_for_shipping', '' );
?>

<div class="mwb_table">
<table class="form-table mwb_wgm_general_setting">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_addition_bcc_option_enable"><?php _e( 'Enable Bcc option for Giftcard Mails', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'After Enabling this buyer will get exact same mail as recipient', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_addition_bcc_option_enable">
					<input type="checkbox" <?php echo ( $mwb_wgc_bcc_enable == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_addition_bcc_option_enable" id="mwb_wgm_addition_bcc_option_enable" class="input-text"> <?php _e( 'Enable Bcc Option For Gift Card Mails', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_additional_apply_coupon_disable"><?php _e( 'Disable Apply Coupon Fields ', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this if you want to disable Apply Coupon Fields if there only GifCard Products are in Cart Page', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_additional_apply_coupon_disable">
					<input type="checkbox" <?php echo ( $mwb_wgm_apply_coupon_disable == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_additional_apply_coupon_disable" id="mwb_wgm_additional_apply_coupon_disable" class="input-text"> <?php _e( 'Disable Apply Coupon Fields', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_additional_resend_disable"><?php _e( 'Disable Resend Button', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this if you want to disable Resend Button At Front End', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_additional_resend_disable">
					<input type="checkbox" <?php echo ( $mwb_wgm_resend_disable == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_additional_resend_disable" id="mwb_wgm_additional_resend_disable" class="input-text"> <?php _e( 'Disable Resend Button', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_additional_sendtoday_disable"><?php _e( 'Disable Send Today Button', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this if you want to disable Send Today Button At Front End', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_additional_sendtoday_disable">
					<input type="checkbox" <?php echo ( $mwb_wgm_sendtoday_disable == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_additional_sendtoday_disable" id="mwb_wgm_additional_sendtoday_disable" class="input-text"> <?php _e( 'Disable Send Today Button', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_additional_preview_disable"><?php _e( 'Disable Preview Button', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this if you want to disable Preview Button At Front End', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_additional_preview_disable">
					<input type="checkbox" <?php echo ( $mwb_wgm_preview_disable == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_additional_preview_disable" id="mwb_wgm_additional_preview_disable" class="input-text"> <?php _e( 'Disable Preview Button', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_addition_pdf_enable"><?php _e( 'Enable Pdf Feature', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'After Enabling this customer will get giftacrd mails along with attached pdf', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_addition_pdf_enable">
					<input type="checkbox" <?php echo ( $mwb_wgm_pdf_enable == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_addition_pdf_enable" id="mwb_wgm_addition_pdf_enable" class="input-text"> <?php _e( 'Enable PDF option for Gift Card Mails ( Please import pdf supported templates. )', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_pdf_template_size"><?php _e( 'Select the Pdf Template Size', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Select the Pdf Template Size (i.e A3 or A4)', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_pdf_template_size">
					<select name="mwb_wgm_pdf_template_size" style="width:160px; padding: 0px;">
						<option value="A3" <?php selected( $mwb_wgm_pdf_template_size, 'A3' ); ?>><?php _e( 'A3 Format', 'woocommerce-ultimate-gift-card' ); ?></option>
						<option value="A4" <?php selected( $mwb_wgm_pdf_template_size, 'A4' ); ?>><?php _e( 'A4 Format', 'woocommerce-ultimate-gift-card' ); ?></option>
					</select>
				</label>						
			</td>
		</tr>
		<?php
		$mwb_wgm_new_pdf = get_option( 'mwb_wgm_next_step_for_pdf_value', 'no' );
		$mwb_wgm_wkhtmltopdf = file_exists( MWB_WGM_DIRPATH . 'wkhtmltox/bin/wkhtmltopdf' );
		if ( $mwb_wgm_new_pdf !== 'yes' || ! $mwb_wgm_wkhtmltopdf ) {
			?>
		<tr valign="top" class="mwb_wgm_pdf_deprecated_row">
			<td></td>
			<td>
				<span><?php _e( 'Sooner the way we do generate the PDFs is going to be deprecated, Please try new way', 'woocommerce-ultimate-gift-card' ); ?></span><input type="button" name="mwb_wgm_pdf_deprecated" class="mwb_wgm_pdf_deprecated" id="mwb_wgm_pdf_deprecated" value="Try Now">
			</td>
		</tr>
			<?php
		}
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_other_setting_browse"><?php _e( 'Enable Browse Image for Gift Card', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box to enable image browse option for customers on purchasing Gift Card.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_other_setting_browse">
					<input type="checkbox" <?php echo ( $browse_enable == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_other_setting_browse" id="mwb_wgm_other_setting_browse" class="input-text"> <?php _e( 'Enable Browse image for customers for gift card products.', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_mail_to_recipient_text"><?php _e( '"Email To Recipient" Text', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Entered text will get displayed at Giftacrd Product Single Page', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="text" value="<?php echo $mwb_wgm_mail_to_recipient_text; ?>" name="mwb_wgm_mail_to_recipient_text" id="mwb_wgm_mail_to_recipient_text" class="input-text" style="width:160px"> 	
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_mail_to_recipient_desc"><?php _e( 'Description for Above', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Entered text will get displayed at Giftacrd Product Single Page,below the "Email to Recipient Option"', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="text" value="<?php echo $mwb_wgm_mail_to_recipient_desc; ?>" name="mwb_wgm_mail_to_recipient_desc" id="mwb_wgm_mail_to_recipient_desc" class="input-text" style="width:160px"> 	
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_downloadable_text"><?php _e( '"You Print & Give" Text', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Entered text will get displayed at Giftacrd Product Single Page', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="text" value="<?php echo $mwb_wgm_downloadable_text; ?>" name="mwb_wgm_downloadable_text" id="mwb_wgm_downloadable_text" class="input-text" style="width:160px">	
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_downloadable_desc"><?php _e( 'Description for Above', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Entered text will get displayed at Giftacrd Product Single Page,below the "You Print & give to recepient" option', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="text" value="<?php echo $mwb_wgm_downloadable_desc; ?>" name="mwb_wgm_downloadable_desc" id="mwb_wgm_downloadable_desc" class="input-text" style="width:160px"> 	
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_shipping_text"><?php _e( '"Ship Your Card" Text', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Entered text will get displayed at Giftacrd Product Single Page', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="text" value="<?php echo $mwb_wgm_shipping_text; ?>" name="mwb_wgm_shipping_text" id="mwb_wgm_shipping_text" class="input-text" style="width:160px"> 	
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_shipping_desc"><?php _e( 'Description for Above', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Entered text will get displayed at Giftacrd Product Single Page,below the "Want to ship your card" option', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="text" value="<?php echo $mwb_wgm_shipping_desc; ?>" name="mwb_wgm_shipping_desc" id="mwb_wgm_shipping_desc" class="input-text" style="width:160px"> 	
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_remove_validation_to"><?php _e( 'Making Optional "To" Field', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this if you want to remove validation from "To" Field', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_remove_validation_to">
					<input type="checkbox" <?php echo ( $mwb_wgm_remove_validation_to == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_remove_validation_to" id="mwb_wgm_remove_validation_to" class="input-text"> <?php _e( 'Remove Validation from "To" Field', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>


		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_remove_validation_to_name"><?php _e( 'Making Optional "To Name" Field', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this if you want to remove validation from "To Name" Field', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_remove_validation_to_name">
					<input type="checkbox" <?php echo ( $mwb_wgm_remove_validation_to_name == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_remove_validation_to_name" id="mwb_wgm_remove_validation_to_name" class="input-text"> <?php _e( 'Remove Validation from "To Name" Field', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>



		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_remove_validation_from"><?php _e( 'Making Optional "From" Field', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this if you want to remove validation from "From" field', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_remove_validation_from">
					<input type="checkbox" <?php echo ( $mwb_wgm_remove_validation_from == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_remove_validation_from" id="mwb_wgm_remove_validation_from" class="input-text"> <?php _e( 'Remove Validation from "From" Field', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>






		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_remove_validation_msg"><?php _e( 'Making Optional "Gift Message" Field', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this if you want to remove validation from "Gift Message"', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_remove_validation_msg">
					<input type="checkbox" <?php echo ( $mwb_wgm_remove_validation_msg == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_remove_validation_msg" id="mwb_wgm_remove_validation_msg" class="input-text"> <?php _e( 'Remove Validation from "Gift Message" Field', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_manually_increment_usage"><?php _e( 'Manual Increment usage count for Gift Coupon', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this if you want to increment usage count of gift coupons manually', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_manually_increment_usage">
					<input type="checkbox" <?php echo ( $mwb_wgm_manually_increment_usage == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_manually_increment_usage" id="mwb_wgm_manually_increment_usage" class="input-text"> <?php _e( 'Update usage count for Gift Coupons manually', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_render_product_custom_page"><?php _e( 'Enable Product for Custom Page', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this, If you want to display the giftcard product in any custom pages you want (like: through product_page id="xyz" shortcode)', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_render_product_custom_page">
					<input type="checkbox" <?php echo ( $mwb_wgm_render_product_custom_page == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_render_product_custom_page" id="mwb_wgm_render_product_custom_page" class="input-text"> <?php _e( 'Display Giftcard Product in custom page', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_custom_page_selection"><?php _e( 'Select Custom Page', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Select that custom page where you want to display the giftcard with the shortcode product_page id="xyz"', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_custom_page_selection">
					<select id="mwb_wgm_custom_page_selection" name="mwb_wgm_custom_page_selection[]" style="width:160px; padding: 2px;">
						<option><?php _e( 'Select Custom Page', 'woocommerce-ultimate-gift-card' ); ?></option>
						<?php
						$args = array(
							'post_type'        => 'page',
							'post_status'      => 'publish',
						);

						$loop = new WP_Query( $args );
						if ( $loop->have_posts() ) :
							while ( $loop->have_posts() ) :
								$loop->the_post();
								global $product;
								$page_id = $loop->post->ID;
								$page_title = $loop->post->post_title;
								$pageselect = '';
								if ( is_array( $mwb_wgm_selected_custom_page ) && in_array( $page_id, $mwb_wgm_selected_custom_page ) ) {
									$pageselect = "selected='selected'";
								}
								?>
								<option value="<?php echo $page_id; ?>"<?php echo $pageselect; ?>><?php echo $page_title; ?></option>
								<?php
							endwhile;
						endif;
						?>
					</select>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_hide_giftcard_notice"><?php _e( 'Hide Giftcard Notice', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this if you want to hide "Giftcard Notice" from product page', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_hide_giftcard_notice">
					<input type="checkbox" <?php echo ( $mwb_wgm_hide_giftcard_notice == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_hide_giftcard_notice" id="mwb_wgm_hide_giftcard_notice" class="input-text"> <?php _e( 'Hide Giftcard Notice', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_hide_giftcard_thumbnail"><?php _e( 'Hide Featured/Thumbnail Image', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this if you want to hide "Featured/Thumbnail image from Single Product Page" from product page', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_hide_giftcard_thumbnail">
					<input type="checkbox" <?php echo ( $mwb_wgm_hide_giftcard_thumbnail == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_hide_giftcard_thumbnail" id="mwb_wgm_hide_giftcard_thumbnail" class="input-text"> <?php _e( 'Hide Featured/Thumbnail Image', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_disable_buyer_notification"><?php _e( 'Disable Buyer Notification', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this if you want to disable the Buyer Notification about the "Gift Card has been sent"', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_disable_buyer_notification">
					<input type="checkbox" <?php echo ( $mwb_wgm_disable_buyer_notification == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_disable_buyer_notification" id="mwb_wgm_disable_buyer_notification" class="input-text"> <?php _e( 'Disable the Notification', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>

		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_disable_quantity_field"><?php _e( 'Disable Quantity field', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this if you want to disable the Quantity field"', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_disable_quantity_field">
					<input type="checkbox" <?php echo ( $mwb_wgm_disable_quantity_field == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_disable_quantity_field" id="mwb_wgm_disable_quantity_field" class="input-text"> <?php _e( 'Disable the Quantity field', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>

		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_change_admin_email_for_shipping"><?php _e( 'Email for Ship your Card Delivery Method', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Enter the email where you want to email your gift card when the customer has chosen the "Ship Your Card" delivery method, Leave blank if you want to send this to Admin Default Email-Id', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="email" value="<?php echo $mwb_wgm_change_admin_email_for_shipping; ?>" name="mwb_wgm_change_admin_email_for_shipping" id="mwb_wgm_change_admin_email_for_shipping" class="input-text" style="width:160px">	
			</td>
		</tr>
	</tbody>
</table>
</div>
<p class="submit">
	<input type="submit" value="<?php _e( 'Save changes', 'woocommerce-ultimate-gift-card' ); ?>" class="button-primary woocommerce-save-button" name="mwb_wgm_additional_setting_save" id="mwb_wgm_additional_setting_save" >
</p>
<div class="clear"></div>
