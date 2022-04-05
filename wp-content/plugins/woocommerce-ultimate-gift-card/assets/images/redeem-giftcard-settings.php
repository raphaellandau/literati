<?php
// This is Redeem Section tab page ...

if ( ! defined( 'ABSPATH') ) {
	exit;
}

if(isset($_POST['wcgm_generate_offine_redeem_url'])){
	global $woocommerce;
	$client_name = isset($_POST['wcgm_offine_redeem_name'])? sanitize_text_field( $_POST['wcgm_offine_redeem_name'] ):'';
	$client_email = isset($_POST['wcgm_offine_redeem_email'])? sanitize_text_field( $_POST['wcgm_offine_redeem_email'] ):'';

	$enable = isset($_POST['wcgm_offine_redeem_enable'])? sanitize_text_field( $_POST['wcgm_offine_redeem_enable'] ):'';

	$client_license_code = get_option( 'mwb_gw_lcns_key');
	$client_domain = home_url();

	$currency = get_option('woocommerce_currency');
	$client_currency = get_woocommerce_currency_symbol();
	
	$curl_data = array(
		'user_name' => $client_name,
		'email' => $client_email,
		'license' => $client_license_code,
		'domain' => $client_domain,
		'currency' => $client_currency,
		
	);

	$redeem_data = get_option('giftcard_offline_redeem_link',true);

	$url ='https://gifting.makewebbetter.com/api/generate';
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_data);

    $response = curl_exec($ch);
    $response = json_decode( $response  );

 
    if( isset( $response->status) && $response->status == 'success'){    	
    	$mwb_redeem_link['shop_url'] = $response->shop_url;
    	$mwb_redeem_link['embed_url'] = $response->embed_url;
    	$mwb_redeem_link['user_id'] = $response->user_id;
    	update_option( 'giftcard_offline_redeem_link',$mwb_redeem_link);
    }
  
	update_option( 'giftcard_offline_redeem_settings',$curl_data);
}
else if(isset($_POST['remove_giftcard_redeem_details']) ){


	global $woocommerce;
	$offine_giftcard_redeem_details = get_option( 'giftcard_offline_redeem_link');
	$userid = $offine_giftcard_redeem_details['user_id'];
		$client_domain = home_url();
		$url ='https://gifting.makewebbetter.com/api/generate/remove';
	
		$curl_data = array('user_id' =>$userid ,'domain' => $client_domain);
		
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_data);

	    $response = curl_exec($ch);
	    $response = json_decode( $response  );
	   
      	if( isset( $response->status) && $response->status == 'success'){
      		delete_option( 'giftcard_offline_redeem_link');
      		delete_option( 'giftcard_offline_redeem_settings');
      	}
		else if(isset( $response->status) && $response->status == 'error'){
			echo $response->message;
		}
	
}

$offline_giftcard_settings = get_option('giftcard_offline_redeem_settings',true);
$current_user = wp_get_current_user(); 
$offine_giftcard_redeem_link = get_option('giftcard_offline_redeem_link',true);

?>
<h3 class="mwb_wgm_overview_heading text-center"><?php _e('Gift Card  Redeem / Recharge ', 'giftware')?></h3>
<div class="mwb_table">
	<div style="display: none;" class="loading-style-bg" id="mwb_gw_loader">
		<img src="<?php echo MWB_GW_URL;?>assets/images/loading.gif">
	</div>

	<div class="mwb_redeem_div_wrapper">
		<?php if( !isset($offine_giftcard_redeem_link ['shop_url']) ||  $offine_giftcard_redeem_link['shop_url'] == ''){  ?>
			<div>
				<div class="mwb_redeem_main_content">
					<h2 class="text-left">Hello Dear</h2>	
					<p> We are thrilled to announce that we have launched a <span class="mwb-reddem-free-text">FREE</span>  service to simplify the problem of redeeming   giftcards at retail store </p>

					<p> We have made this just on your demand so we would love your suggestion to improve it. </p>
				</div>

				
				<h3 class="text-center">What it Contains </h3>	
				<ul class="mwb_redeem_listing">	
					<li class="mwb_redeem_item scan"> <div class="mwb_redeem_content">Scan</div> <div class="mwb_redeem_arrow"><i class="fas fa-arrows-alt-h"></i></div></li>	
					<li class="mwb_redeem_item redeem"> <div class="mwb_redeem_content">Redeem</div> <div class="mwb_redeem_arrow"><i class="fas fa-arrows-alt-h"></i></div></li>
					<li class="mwb_redeem_item recharge"> <div class="mwb_redeem_content">Recharge</div> <div class="mwb_redeem_arrow"><i class="fas fa-arrows-alt-h"></i></div></li>
					<li class="mwb_redeem_item reports"> <div class="mwb_redeem_content">Reports</div></li>
				</ul>
			</div>	
		<?php  } ?>		
		

		<div>
			<?php if( !isset($offine_giftcard_redeem_link ['shop_url']) ||  $offine_giftcard_redeem_link['shop_url'] == ''){  ?>
				<div class="mwb-giftware-reddem-image text-center">
					<img src="<?php echo MWB_WGM_URL.'/assets/images/giftware-redeem-image.png'?>" alt="GiftWare">
					<div class="mwb_giftware_reddem_link_wrapper">
						<a href="#" class="generate_link"><i class="fas fa-link"></i> Get me My FREE redeem Link</a>
						<span>(you can delete your redeem link anytime)</span>
					</div>
				</div>
					
				
			<?php }
			else { ?>
				<table class="mwb_redeem_details">
			
					<thead>
						<tr>
							<th colspan="2"> Your Gift Card Redeem Details </th>
						</tr>
					</thead>
					<tbody>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="wcgw_plugin_enable"><?php _e('Giftcard Redeem Link', 'giftware')?></label>
							</th>
							<td class="forminp forminp-text">
								<?php
								$attribut_description = __('please open the link to redeem the giftcard','giftware');
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
								<label for="wcgw_plugin_enable"><?php _e('Embeded Link', 'giftware')?></label>
							</th>
							<td class="forminp forminp-text">
								<?php
								$attribut_description = __('Enter this code to add the redeem page in your site','giftware');
								echo wc_help_tip( $attribut_description );
								?>
								<textarea cols="20" rows="3" id="mwb_gw_embeded_input_text"><?php if(isset($offine_giftcard_redeem_link ['embed_url']) &&  $offine_giftcard_redeem_link['embed_url'] !== ''){ echo trim($offine_giftcard_redeem_link['embed_url']);  } ?>
								</textarea>
								<div class="mwb-giftware-copy-icon">									
									<button  class="mwb_embeded_copy" data-clipboard-target="#mwb_gw_embeded_input_text" title="copy">
										<i class="far fa-copy" ></i>
									</button>
								</div>
							</td>
						</tr>
						<tr valign="top">
							<td>
								<input type="submit" name="remove_giftcard_redeem_details" class="remove_giftcard_redeem_details"  class="input-text" value = 'Remove Details' >
							</td>
							<td>
								<a target="_blank" href="<?php if(isset($offine_giftcard_redeem_link ['shop_url']) &&  $offine_giftcard_redeem_link['shop_url'] !== ''){ echo $offine_giftcard_redeem_link['shop_url'];  } ?>" class= "mwb_gw_open_redeem_link"> Open Shop</a>
							</td>	

						</tr>
					</tbody>
				</table>
			<?php	} ?>
		</div>
		<div class="mwb-reedem-video text-center">
			<h3> See it in Action </h3>
			<video width="320" height="190" controls >  
			  <source src="<?php echo MWB_GW_URL.'/assets/video/simple.mp4'; ?>" type="video/mp4">
			 
			</video>
		</div>	
		<!-- <div class="text-center">
			<H2>How it Work </H2>	
			<ul class="text-left mwb-readem-work-listing">
				<li><i class="far fa-check-circle"></i> Generate the Link</li>
				<li><i class="far fa-check-circle"></i> Genertaed link will be mailed to you</li>
				<li><i class="far fa-check-circle"></i> Click on the link</li>
				<li><i class="far fa-check-circle"></i> Login</li>
				<li><i class="far fa-check-circle"></i> Enjoy</li>
			</ul>
		</div> -->
		
	</div>


	<div class="mwb_redeem_registraion_div" style="display:none;">
	<div class=" mwb_gw_general_setting">
			<table class="form-table">
			
				<tbody>			
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcgw_plugin_enable"><?php _e('Email', 'giftware')?></label>
						</th>
						<td class="forminp forminp-text">
							<?php
							$attribut_description = __('Enter the email for account creation','giftware');
							echo wc_help_tip( $attribut_description );
							?>
							<label for="wcgw_plugin_enable">
								<input type="email" name="wcgm_offine_redeem_email" id="wcgm_offine_redeem_email" class="input-text" value="<?php  echo $current_user->user_email; ?> ">
							</label>						
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcgw_plugin_enable"><?php _e('Name', 'giftware')?></label>
						</th>
						<td class="forminp forminp-text">
							<?php
							$attribut_description = __('Enter the name for account creation','giftware');
							echo wc_help_tip( $attribut_description );
							?>
							<label for="wcgw_plugin_enable">
								<input type="text" name="wcgm_offine_redeem_name" id="wcgm_offine_redeem_name" class="input-text" value="<?php  echo $current_user->display_name; ?> ">
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