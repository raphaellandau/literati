<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $_POST['mwb_wgm_other_setting_save'] ) ) {
	unset( $_POST['mwb_wgm_other_setting_save'] );
	do_action( 'mwb_wgm_other_setting_save' );

	$postdata = $_POST;

	foreach ( $postdata as $key => $data ) {
			update_option( $key, $data );
		
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
$giftcard_upload_logo = get_option( 'mwb_wgm_other_setting_upload_logo', false );
$giftcard_disclaimer = get_option( 'mwb_wgm_other_setting_disclaimer', false );
$giftcard_background = get_option( 'mwb_wgm_other_setting_background_logo', false );
$giftcard_background_color = get_option( 'mwb_wgm_other_setting_background_color', false );
$giftcard_receive_subject = get_option( 'mwb_wgm_other_setting_receive_subject', false );
$giftcard_receive_subject = stripcslashes( $giftcard_receive_subject );

$giftcard_coupon_subject = get_option( 'mwb_wgm_other_setting_receive_coupon_subject', false );
$giftcard_coupon_subject = stripcslashes( $giftcard_coupon_subject );
$giftcard_receive_coupon_message = get_option( 'mwb_wgm_other_setting_receive_coupon_message', false );
if ( empty( $giftcard_receive_coupon_message ) || $giftcard_receive_coupon_message == null ) {
	$giftcard_receive_coupon_message = '<center style="width: 100%;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 600px; background-color:#0467A2;">
                <tr>
                    <td style="padding: 20px 0; text-align: center">
                       <p style="font-size: 20px; color: #fff; font-family: sans-serif; text-align: center;">[SITENAME]</p>
                    </td>
                </tr>
            </table>
                  <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 600px;">
                <tr>
                    <td style="padding: 40px 10px;width: 100%;font-size: 12px; font-family: sans-serif; mso-height-rule: exactly; line-height:18px; text-align: center; color: #888888;">
                      <p style="font-size: 18px; color: #575757; text-align: center; font-family: sans-serif;">' . __( 'Hello, This is the notfication for your coupon amount. ', 'woocommerce-ultimate-gift-card' ) . '<br/>' . __( 'You have left with amount of ', 'woocommerce-ultimate-gift-card' ) . '[COUPONAMOUNT]</p>
                      <span style="font-size: 16px; color: #575757; text-align: center; font-family: sans-serif;">' . __( 'Thank You', 'woocommerce-ultimate-gift-card' ) . '</span>
                    </td>
                </tr>
            </table>
            <!-- Email Header : END -->
            <!-- Email Footer : BEGIN -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 600px; background-color: #FCD347;">
                <tr>
                    <td style="padding: 10px 10px;width: 100%;font-size: 12px; font-family: sans-serif; mso-height-rule: exactly; line-height:18px; text-align: center; color: #888888;">
                        <p style="font-size: 14px; font-family: sans-serif; color: #fff; text-align: center;">[DISCLAIMER]</p>
                    </td>
                </tr>
            </table>
        </div>
    </center>';
}
$giftcard_receive_message = get_option( 'mwb_wgm_other_setting_receive_message', false );

$giftcard_giftcard_subject = get_option( 'mwb_wgm_other_setting_giftcard_subject', false );
$giftcard_giftcard_subject = stripcslashes( $giftcard_giftcard_subject );
$giftcard_logo_height = get_option( 'mwb_wgm_other_setting_logo_height', false );
$giftcard_logo_width = get_option( 'mwb_wgm_other_setting_logo_width', false );
$giftcard_custom_css = get_option( 'mwb_wgm_other_setting_mail_style', false );
$giftcard_custom_css = stripcslashes( $giftcard_custom_css );
$giftcard_message_length = trim( get_option( 'mwb_wgm_other_setting_giftcard_message_length', 300 ) );

$giftcard_down_subject = get_option( 'mwb_wgm_other_setting_giftcard_subject_downloadable', false );
$giftcard_ship_subject = get_option( 'mwb_wgm_other_setting_giftcard_subject_shipping', '' );
?>
<div class="mwb_table">
<h2 id="mwb_wgm_manage_template" class="mwb_wgm_mail_setting_tab"><?php _e( 'Manage Template', 'woocommerce-ultimate-gift-card' ); ?></h2>
<div id="mwb_wgm_manage_template_wrapper">
<table class="form-table mwb_wgm_other_setting">
	<tbody>

		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_other_setting_upload_logo"><?php _e( 'Upload Default Logo', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Upload the image which is used as logo on your Email Template.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="text" readonly class="mwb_wgm_other_setting_upload_logo_value mwb_wgm_new_woo_ver_style_text" id="mwb_wgm_other_setting_upload_logo" name="mwb_wgm_other_setting_upload_logo" value="<?php echo $giftcard_upload_logo; ?>"/>
				<input class="mwb_wgm_other_setting_upload_logo button"  type="button" value="<?php _e( 'Upload Logo', 'woocommerce-ultimate-gift-card' ); ?>" />
				
				<p id="mwb_wgm_other_setting_remove_logo">
					<span class="mwb_wgm_other_setting_remove_logo">
						<img src="" width="150px" height="150px" id="mwb_wgm_other_setting_upload_image">
						<span class="mwb_wgm_other_setting_remove_logo_span">X</span>
					</span>
				</p>
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_other_setting_upload_logo"><?php _e( 'Logo Dimension', 'woocommerce-ultimate-gift-card' ); ?> (in "px")</label>
			</th>
			<td class="forminp forminp-text">
				<label for="mwb_wgm_other_setting_upload_logo"><?php _e( 'Height', 'woocommerce-ultimate-gift-card' ); ?>: </label><input type="number" class="mwb_wgm_new_woo_ver_style_text" name="mwb_wgm_other_setting_logo_height" min="0" value="<?php echo $giftcard_logo_height; ?>"/>
				<label for="mwb_wgm_other_setting_upload_logo"><?php _e( 'Width', 'woocommerce-ultimate-gift-card' ); ?>: </label><input type="number" class="mwb_wgm_new_woo_ver_style_text" name="mwb_wgm_other_setting_logo_width" min="0" value="<?php echo $giftcard_logo_width; ?>"/>
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_other_setting_background_logo"><?php _e( 'Email Default Event Image', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Upload image which is used as a default Event/Occasion in Email Template.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="text" class="mwb_wgm_new_woo_ver_style_text" readonly class="mwb_wgm_other_setting_background_logo_value" id="mwb_wgm_other_setting_background_logo_value" name="mwb_wgm_other_setting_background_logo" value="<?php echo $giftcard_background; ?>"/>
				<input class="mwb_wgm_other_setting_background_logo button"  type="button" value="<?php _e( 'Upload Image', 'woocommerce-ultimate-gift-card' ); ?>" />
				<p id="mwb_wgm_other_setting_remove_background">
					<span class="mwb_wgm_other_setting_remove_background">
						<img src="" width="150px" height="150px" id="mwb_wgm_other_setting_background_logo_image">
						<span class="mwb_wgm_other_setting_remove_background_span">X</span>
					</span>
				</p>						
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_other_setting_background_color"><?php _e( 'Email Background Color', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
					$attribute_description = __( 'Set the background color of Email Template.', 'woocommerce-ultimate-gift-card' );
					echo wc_help_tip( $attribute_description );
				?>
				<label>
					<input type="text" class="mwb_wgm_new_woo_ver_style_text" id="mwb_wgm_mailcolor" name="mwb_wgm_other_setting_background_color" value="<?php echo $giftcard_background_color; ?>">					
				</label>
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_other_setting_mail_style"><?php _e( 'Custom CSS', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
					$attribute_description = __( 'Write the custom css for Email Template.', 'woocommerce-ultimate-gift-card' );
					echo wc_help_tip( $attribute_description );
				?>
				<label>
					<textarea id="mwb_wgm_other_setting_mail_style" name="mwb_wgm_other_setting_mail_style"><?php echo $giftcard_custom_css; ?></textarea>					
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_other_setting_giftcard_message_length"><?php _e( 'Giftcard Message Length', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Enter the Gift Card Message length, used to limit the number of characters entered by the customers.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<input type="number" min="0" value="<?php echo $giftcard_message_length; ?>" name="mwb_wgm_other_setting_giftcard_message_length" id="mwb_wgm_other_setting_giftcard_message_length" class="input-text mwb_wgm_new_woo_ver_style_text" > 	
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_other_setting_disclaimer"><?php _e( 'Disclaimer Text', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Set the Disclaimer Text for Email Template.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_other_setting_disclaimer">
					<?php
					$content = stripcslashes( $giftcard_disclaimer );
					$editor_id = 'mwb_wgm_other_setting_disclaimer';
					$settings = array(
						'media_buttons'    => false,
						'drag_drop_upload' => true,
						'dfw'              => true,
						'teeny'            => true,
						'editor_height'    => 200,
						'editor_class'     => 'mwb_wgm_new_woo_ver_style_textarea',
						'textarea_name'    => 'mwb_wgm_other_setting_disclaimer',
					);
					wp_editor( $content, $editor_id, $settings );
					?>
				</label>						
			</td>
		</tr>
	</table>
</div>	
<h2 id="mwb_wgm_mail_setting" class="mwb_wgm_mail_setting_tab"><?php _e( 'Mail Setting', 'woocommerce-ultimate-gift-card' ); ?></h2>
<div id="mwb_wgm_mail_setting_wrapper">
	<table class="form-table mwb_wgm_other_setting">	
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_other_setting_giftcard_subject"><?php _e( 'Giftcard Email Subject', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<label for="mwb_wgm_other_setting_giftcard_subject">
					<?php
					$attribute_description = __( 'Email Subject for Giftcard Mail.', 'woocommerce-ultimate-gift-card' );
					echo wc_help_tip( $attribute_description );
					?>
					<input type="text" class= "mwb_wgm_new_woo_ver_style_text" value="<?php echo $giftcard_giftcard_subject; ?>" name="mwb_wgm_other_setting_giftcard_subject" class="mwb_wgm_other_setting_giftcard_subject" id="mwb_wgm_other_setting_giftcard_subject">
					
				</label>
				<p class="description"><?php _e( 'Use [SITENAME] shortcode as the name of the site and [BUYEREMAILADDRESS] shortcode as buyer email address to be placed dynamically.', 'woocommerce-ultimate-gift-card' ); ?></p>					
			</td>
		</tr>
		
		
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_other_setting_receive_subject"><?php _e( 'Email Subject to Sender', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<label for="mwb_wgm_other_setting_receive_subject">
					<?php
					$attribute_description = __( 'Email Subject for notifying receiver about Giftcard Mail send.', 'woocommerce-ultimate-gift-card' );
					echo wc_help_tip( $attribute_description );
					?>
					<input type="text" class="mwb_wgm_new_woo_ver_style_text" value='<?php echo $giftcard_receive_subject; ?>' name="mwb_wgm_other_setting_receive_subject" class="mwb_wgm_other_setting_receive_subject" id="mwb_wgm_other_setting_receive_subject">
				</label>						
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_other_setting_receive_message"><?php _e( 'Email Notification to Sender', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<label for="mwb_wgm_other_setting_receive_message">
					<?php
					$attribute_description = __( 'Write the Email Content for Buyer who should acknowledge that his/her Gift Card has been sent successflly', 'woocommerce-ultimate-gift-card' );
					echo wc_help_tip( $attribute_description );
					?>
					<span class="description mwb_wgm_desc"><?php _e( 'You may use shortcode [TO] for placing the Recipient Email dynamically', 'woocommerce-ultimate-gift-card' ); ?></span>
					<?php
					$content = stripslashes( $giftcard_receive_message );
					$editor_id = 'mwb_wgm_other_setting_receive_message';
					$settings = array(
						'media_buttons'    => false,
						'drag_drop_upload' => true,
						'dfw'              => true,
						'teeny'            => true,
						'editor_height'    => 200,
						'editor_class'     => 'mwb_wgm_new_woo_ver_style_textarea',
						'textarea_name'    => 'mwb_wgm_other_setting_receive_message',
					);
					wp_editor( $content, $editor_id, $settings );
					?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_other_setting_giftcard_subject_downloadable"><?php _e( 'Downloadable Gift Card Email Subject for Buyer', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<label for="mwb_wgm_other_setting_giftcard_subject_downloadable">
					<?php
					$attribute_description = __( 'Downloadable Gift Card Email Subject for Giftcard Mail when received by the buyer.', 'woocommerce-ultimate-gift-card' );
					echo wc_help_tip( $attribute_description );
					?>
					<input type="text" value='<?php echo $giftcard_down_subject; ?>' name="mwb_wgm_other_setting_giftcard_subject_downloadable" class="mwb_wgm_other_setting_giftcard_subject_downloadable mwb_wgm_new_woo_ver_style_text" id="mwb_wgm_other_setting_giftcard_subject_downloadable">
					
				</label>
				<p class="description"><?php _e( 'Use [SITENAME] shortcode as the name of the site  to be placed dynamically.', 'woocommerce-ultimate-gift-card' ); ?></p>					
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_other_setting_giftcard_subject_shipping"><?php _e( 'Gift Card Email Subject for Admin', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<label for="mwb_wgm_other_setting_giftcard_subject_shipping">
					<?php
					$attribute_description = __( 'This is the subject of the Gift Card mail that will be send to the admin when buyer purchases the Gift Card so that he can ship it to the dhipping address.', 'woocommerce-ultimate-gift-card' );
					echo wc_help_tip( $attribute_description );
					?>
					<input type="text" value='<?php echo $giftcard_ship_subject; ?>' name="mwb_wgm_other_setting_giftcard_subject_shipping" class="input-text mwb_wgm_new_woo_ver_style_text" id="mwb_wgm_other_setting_giftcard_subject_shipping">
					
				</label>
				<p class="description"><?php _e( 'Use [SITENAME] shortcode as the name of the site and [ORDERID] shortcode as the order id of the product to be placed dynamically.', 'woocommerce-ultimate-gift-card' ); ?></p>					
			</td>
		</tr>
		<?php
		do_action( 'mwb_wgm_other_setting' );
		?>
	</tbody>
</table>
</div>	
<h2 id="mwb_wgm_coupon_mail_setting" class="mwb_wgm_coupon_mail_setting_tab"><?php _e( ' Coupon Mail Setting', 'woocommerce-ultimate-gift-card' ); ?></h2>
<div id="mwb_wgm_coupon_mail_setting_wrapper">
	<table class="form-table mwb_wgm_other_setting">	
		 <tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_other_setting_receive_coupon_subject"><?php _e( 'Coupon Email Subject', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<label for="mwb_wgm_other_setting_receive_coupon_subject">
					<?php
					$attribute_description = __( 'Email Subject for Coupon Mail.', 'woocommerce-ultimate-gift-card' );
					echo wc_help_tip( $attribute_description );
					?>
					<input type="text" value='<?php echo $giftcard_coupon_subject; ?>' name="mwb_wgm_other_setting_receive_coupon_subject" class="mwb_wgm_other_setting_receive_coupon_subject mwb_wgm_new_woo_ver_style_text" id="mwb_wgm_other_setting_receive_coupon_subject">
					
				</label>
				<p class="description"><?php _e( 'Use [SITENAME] shortcode as the name of the site to be placed dynamically.', 'woocommerce-ultimate-gift-card' ); ?></p>					
			</td>
		</tr>
				
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_other_setting_receive_coupon_message"><?php _e( 'Email Notification to Sender', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<label for="mwb_wgm_other_setting_receive_coupon_message
				">
					<?php
					$attribute_description = __( 'Write the Email Content to notify the user about their usage of coupon amount.', 'woocommerce-ultimate-gift-card' );
					echo wc_help_tip( $attribute_description );
					?>
					<span class="description"><?php _e( 'Use [COUPONAMOUNT] shortcode as coupon amount to be placed dynamically. Here the [DISCLAIMER] shortcode would be replaced by above Disclaimer text field.', 'woocommerce-ultimate-gift-card' ); ?></span>	
					<?php
					$content = stripslashes( $giftcard_receive_coupon_message );
					$editor_id = 'mwb_wgm_other_setting_receive_coupon_message';
					$settings = array(
						'media_buttons'    => false,
						'drag_drop_upload' => true,
						'dfw'              => true,
						'teeny'            => true,
						'editor_height'    => 200,
						'editor_class'     => 'mwb_wgm_new_woo_ver_style_textarea',
						'textarea_name'    => 'mwb_wgm_other_setting_receive_coupon_message',
					);
					wp_editor( $content, $editor_id, $settings );
					?>
				</label>						
			</td>
		</tr>
		<?php
		do_action( 'mwb_wgm_other_setting' );
		?>
	</tbody>
</table>
</div>
</div>
<p class="submit mwb_wgm_email_template">
	<input type="submit" value="<?php _e( 'Save changes', 'woocommerce-ultimate-gift-card' ); ?>" class="button-primary woocommerce-save-button" name="mwb_wgm_other_setting_save">
</p>
<div class="clear"></div>
