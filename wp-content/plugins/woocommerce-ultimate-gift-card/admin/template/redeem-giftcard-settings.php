<?php
// This is Redeem Section tab page ...

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $_POST['wcgm_generate_offine_redeem_url'] ) ) {
	global $woocommerce;
	$client_name = isset( $_POST['wcgm_offine_redeem_name'] ) ? sanitize_text_field( $_POST['wcgm_offine_redeem_name'] ) : '';
	$client_email = isset( $_POST['wcgm_offine_redeem_email'] ) ? sanitize_text_field( $_POST['wcgm_offine_redeem_email'] ) : '';

	$enable = isset( $_POST['wcgm_offine_redeem_enable'] ) ? sanitize_text_field( $_POST['wcgm_offine_redeem_enable'] ) : '';
	
	$client_license_code = '';
	$host_server = $_SERVER['HTTP_HOST'];
	if ( strpos( $host_server, 'www.' ) == 0 ) {

		$host_server = str_replace( 'www.', '', $host_server );
	}
	$client_license_code = get_option( 'mwb_wgm_license_hash' . $host_server );
	$client_domain = home_url();

	$currency = get_option( 'woocommerce_currency' );

	$client_currency = get_woocommerce_currency_symbol();

	$curl_data = array(
		'user_name' => $client_name,
		'email' => $client_email,
		'license' => $client_license_code,
		'domain' => $client_domain,
		'currency' => $client_currency,

	);

	$redeem_data = get_option( 'giftcard_offline_redeem_link', true );

	$url = 'https://gifting.makewebbetter.com/api/generate';
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $curl_data );

	$response = curl_exec( $ch );
	$response = json_decode( $response );


	if ( isset( $response->status ) && $response->status == 'success' ) {

		$mwb_redeem_link['shop_url'] = $response->shop_url;
		$mwb_redeem_link['embed_url'] = $response->embed_url;
		$mwb_redeem_link['user_id'] = $response->user_id;
		$mwb_redeem_link ['license'] = $client_license_code;
		update_option( 'giftcard_offline_redeem_link', $mwb_redeem_link );
	}

	update_option( 'giftcard_offline_redeem_settings', $curl_data );
} else if ( isset( $_POST['remove_giftcard_redeem_details'] ) ) {


	global $woocommerce;
	$offine_giftcard_redeem_details = get_option( 'giftcard_offline_redeem_link' );
	$userid = $offine_giftcard_redeem_details['user_id'];
		$client_domain = home_url();
		$url = 'https://gifting.makewebbetter.com/api/generate/remove';

		$curl_data = array(
			'user_id' => $userid,
			'domain' => $client_domain,
		);

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $curl_data );

		$response = curl_exec( $ch );
		$response = json_decode( $response );

		if ( isset( $response->status ) && $response->status == 'success' ) {
			delete_option( 'giftcard_offline_redeem_link' );
			delete_option( 'giftcard_offline_redeem_settings' );
		} else if ( isset( $response->status ) && $response->status == 'error' ) {
			echo $response->message;
		}
} else if ( isset( $_POST['update_giftcard_redeem_details'] ) ) {

	$offine_giftcard_redeem_details = get_option( 'giftcard_offline_redeem_link' );
	$userid = $offine_giftcard_redeem_details['user_id'];
	$client_domain = home_url();
	$url = 'https://gifting.makewebbetter.com/api/generate/update';

	
	$host_server = $_SERVER['HTTP_HOST'];
	if ( strpos( $host_server, 'www.' ) == 0 ) {

		$host_server = str_replace( 'www.', '', $host_server );
	}
	$client_license_code = get_option( 'mwb_wgm_license_hash' . $host_server );

	if ( $client_license_code != '' ) {
		$curl_data = array(
			'user_id' => $userid,
			'domain' => $client_domain,
			'license' => $client_license_code,
		);

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $curl_data );

		$response = curl_exec( $ch );
		$response = json_decode( $response );
		if ( isset( $response->status ) && $response->status == 'success' ) {
			$offine_giftcard_redeem_details ['license'] = $client_license_code;
			update_option( 'giftcard_offline_redeem_link', $offine_giftcard_redeem_details );
		} else if ( isset( $response->status ) && $response->status == 'error' ) {
			echo $response->message;
		}
	}
}

$offline_giftcard_settings = get_option( 'giftcard_offline_redeem_settings', true );
$current_user = wp_get_current_user();
$offine_giftcard_redeem_link = get_option( 'giftcard_offline_redeem_link', true );
?>

<div class="mwb_table">
	<div style="display: none;" class="loading-style-bg" id="mwb_gw_loader">
		<img src="<?php echo MWB_WGM_URL; ?>assets/images/loading.gif">
	</div>

	<div class="mwb_redeem_div_wrapper">
		<?php if ( ! isset( $offine_giftcard_redeem_link ['shop_url'] ) || $offine_giftcard_redeem_link['shop_url'] == '' ) { ?>
			<div>
				<div class="mwb-giftware-reddem-image text-center">
					<img src="<?php echo MWB_WGM_URL . '/assets/images/giftware-redeem-image.png'; ?>" alt="GiftWare">
					<div class="mwb_giftware_reddem_link_wrapper">
						<a href="#" class="generate_link"><i class="fas fa-link"></i><?php esc_html_e( 'Get me My FREE redeem Link', 'woocommerce-ultimate-gift-card' ); ?></a>
						<span><?php esc_html_e( '(you can delete your redeem link anytime)', 'woocommerce-ultimate-gift-card' ); ?></span>
					</div>
				</div>

				<div class="mwb_redeem_main_content">
					<h2 class="text-left"><?php esc_html_e( 'Hello Dear', 'woocommerce-ultimate-gift-card' ); ?></h2>	
					<p><?php esc_html_e( 'We are thrilled to announce that we have launched a', 'woocommerce-ultimate-gift-card' ); ?><span class="mwb-reddem-free-text"><?php esc_html_e( 'FREE', 'woocommerce-ultimate-gift-card' ); ?></span><?php esc_html_e( 'service to simplify the problem of redeeming giftcards at retail store', 'woocommerce-ultimate-gift-card' ); ?> </p>

					<p><?php esc_html_e( 'We have made this just on your demand so we would love your suggestion to improve it.', 'woocommerce-ultimate-gift-card' ); ?></p>
				</div>
				<h3 class="text-center"><?php esc_html_e( 'What it Contains', 'woocommerce-ultimate-gift-card' ); ?> </h3>	
				<ul class="mwb_redeem_listing">	
					<li class="mwb_redeem_item scan"> <div class="mwb_redeem_content"><?php esc_html_e( 'Scan', 'woocommerce-ultimate-gift-card' ); ?></div> <div class="mwb_redeem_arrow"><i class="fas fa-arrows-alt-h"></i></div></li>	
					<li class="mwb_redeem_item redeem"> <div class="mwb_redeem_content"><?php esc_html_e( 'Redeem', 'woocommerce-ultimate-gift-card' ); ?></div> <div class="mwb_redeem_arrow"><i class="fas fa-arrows-alt-h"></i></div></li>
					<li class="mwb_redeem_item recharge"> <div class="mwb_redeem_content"><?php esc_html_e( 'Recharge', 'woocommerce-ultimate-gift-card' ); ?></div> <div class="mwb_redeem_arrow"><i class="fas fa-arrows-alt-h"></i></div></li>
					<li class="mwb_redeem_item reports"> <div class="mwb_redeem_content"><?php esc_html_e( 'Reports', 'woocommerce-ultimate-gift-card' ); ?></div></li>
				</ul>
			</div>	
		<?php } else { ?>
			<div>
				<table class="mwb_redeem_details">
					<thead>
						<tr>
							<th colspan="2"><?php esc_html_e( 'Your Gift Card Redeem Details', 'woocommerce-ultimate-gift-card' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="wcgw_plugin_enable"><?php esc_html_e( 'Giftcard Redeem Link', 'woocommerce-ultimate-gift-card' ); ?></label>
							</th>
							<td class="forminp forminp-text">
								<?php
								$attribut_description = __( 'please open the link to redeem the giftcard', 'woocommerce-ultimate-gift-card' );
								echo wc_help_tip( $attribut_description );
								?>
								<label for="wcgw_plugin_enable">
									<input type="text" name="wcgm_offine_redeem_link" id="wcgm_offine_redeem_link" class="input-text" value="<?php if(isset($offine_giftcard_redeem_link ['shop_url']) &&  $offine_giftcard_redeem_link['shop_url'] !== ''){ echo $offine_giftcard_redeem_link['shop_url'];  } ?>">
									<div class="mwb-giftware-copy-icon" >
										<button  class="mwb_link_copy" data-clipboard-target="#wcgm_offine_redeem_link" title="copy">
											<i class="far fa-copy" ></i>
										</button>
										
									</div>	
								</label>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="wcgw_plugin_enable"><?php esc_html_e( 'Embedded Link', 'woocommerce-ultimate-gift-card' ); ?></label>
							</th>
							<td class="forminp forminp-text">
								<?php
								$attribut_description = __( 'Enter this code to add the redeem page in your site', 'woocommerce-ultimate-gift-card' );
								echo wc_help_tip( $attribut_description );
								?>
								<textarea cols="20" rows="3" id="mwb_gw_embeded_input_text"><?php if( isset( $offine_giftcard_redeem_link ['embed_url'] ) && $offine_giftcard_redeem_link['embed_url'] !== '' ) {
									echo trim( $offine_giftcard_redeem_link['embed_url'] ); } ?>
								</textarea>
								<div class="mwb-giftware-copy-icon">									
									<button  class="mwb_embeded_copy" data-clipboard-target="#mwb_gw_embeded_input_text" title="copy">
										<i class="far fa-copy" ></i>
									</button>
								</div>
							</td>
						</tr>
						<tr valign="top">
							<td colspan="2">
								<input type="submit" name="remove_giftcard_redeem_details" class="remove_giftcard_redeem_details"  class="input-text" value = 'Remove Details' >
								<a target="_blank" href="
								<?php
								if ( isset( $offine_giftcard_redeem_link ['shop_url'] ) && $offine_giftcard_redeem_link['shop_url'] !== '' ) {
									echo $offine_giftcard_redeem_link['shop_url'];  }
								?>
								" class= "mwb_gw_open_redeem_link"> Open Shop</a>
								<?php if ( isset( $offine_giftcard_redeem_link['license'] ) && $offine_giftcard_redeem_link['license'] == '' ) { ?>
									<input type="submit" name="update_giftcard_redeem_details" class="update_giftcard_redeem_details"  class="input-text" value ='Update License' >
								<?php } ?>
							</td>

						</tr>
					</tbody>
				</table>
				<p><b><?php esc_html_e( 'To use redeem link as it is, follow the steps below', 'woocommerce-ultimate-gift-card' ); ?></b></p>
				<ol>
					<li><?php esc_html_e( 'Click on Open Shop button and login using the credentials provided in the received email', 'woocommerce-ultimate-gift-card' ); ?></li>
					<li><?php esc_html_e( 'Start Scan/Fetch and Redeem/Recharge', 'woocommerce-ultimate-gift-card' ); ?></li>
				</ol>
				<p><b><?php esc_html_e( 'To use the redeem link on the web store follow the steps below', 'woocommerce-ultimate-gift-card' ); ?></b></p>
				<ol>
					<li><?php esc_html_e( 'Create a page', 'woocommerce-ultimate-gift-card' ); ?></li>
					<li><?php esc_html_e( 'Copy the embed link and paste it in the created page', 'woocommerce-ultimate-gift-card' ); ?></li>
					<li>
					<?php
					esc_html_e(
						'Login using the credentials given in the received email</li>
					<li>Start Scan/Fetch and Redeem/Recharge',
						'woocommerce-ultimate-gift-card'
					);
					?>
						</li>
				</ol>

				<p><b><?php esc_html_e( 'To use the redeem link on this POS system, follow the steps below', 'woocommerce-ultimate-gift-card' ); ?></b></p>
				<ol>
					<li><?php esc_html_e( 'Copy the embed link and paste it on any page at POS', 'woocommerce-ultimate-gift-card' ); ?></li>
					<li><?php esc_html_e( 'Login using the credentials given in the received email', 'woocommerce-ultimate-gift-card' ); ?></li>
					<li><?php esc_html_e( 'Start Scan/Fetch and Redeem/Recharge', 'woocommerce-ultimate-gift-card' ); ?></li>
				</ol>
			</div>
			<?php	} ?>
		
		<div class="mwb_wgm_video_wrapper">
			<h3><?php esc_html_e( 'See it in Action', 'woocommerce-ultimate-gift-card' ); ?>  </h3>
			<iframe height="411" src="https://www.youtube.com/embed/H1cYF4F5JA8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		</div>	
	</div>
	<div class="mwb_redeem_registraion_div" style="display:none;">
	<div class=" mwb_gw_general_setting">
			<table class="form-table">
			
				<tbody>			
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcgw_plugin_enable"><?php _e( 'Email', 'woocommerce-ultimate-gift-card' ); ?></label>
						</th>
						<td class="forminp forminp-text">
							<?php
							$attribut_description = __( 'Enter the email for account creation', 'woocommerce-ultimate-gift-card' );
							echo wc_help_tip( $attribut_description );
							?>
							<label for="wcgw_plugin_enable">
								<input type="email" name="wcgm_offine_redeem_email" id="wcgm_offine_redeem_email" class="input-text" value="<?php echo $current_user->user_email; ?> ">
							</label>						
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcgw_plugin_enable"><?php _e( 'Name', 'woocommerce-ultimate-gift-card' ); ?></label>
						</th>
						<td class="forminp forminp-text">
							<?php
							$attribut_description = __( 'Enter the name for account creation', 'woocommerce-ultimate-gift-card' );
							echo wc_help_tip( $attribut_description );
							?>
							<label for="wcgw_plugin_enable">
								<input type="text" name="wcgm_offine_redeem_name" id="wcgm_offine_redeem_name" class="input-text" value="<?php echo $current_user->display_name; ?> ">
							</label>						
						</td>
					</tr>			

					<tr valign="top">
						
						<td class="forminp forminp-text text-center" colspan="2">
						
							<label for="wcgw_plugin_enable">
								<input type="submit" name="wcgm_generate_offine_redeem_url" id="wcgm_generate_offine_redeem_url" class="input-text" value = 'Generate Link'>
							</label>						
						</td>
					</tr>				
				</tbody>				
			</table>
			<span class="mwb-redeem-pop-close"><i class="fas fa-times"></i></span>
		</div>
	</div>			
</div>
