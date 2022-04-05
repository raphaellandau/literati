<?php
/**
 * Plugin Name:  WooCommerce Ultimate Gift Card
 * Plugin URI: https://makewebbetter.com
 * Description: This woocommerce extension allow merchants to create and sell multiple Gift Card Product having multiple price variation.
 * Version: 2.7.7
 * Author: MakeWebBetter <webmaster@makewebbetter.com>
 * Author URI: https://makewebbetter.com
 * Requires at least: 4.0
 * Tested up to: 5.6
 * WC tested up to:   5.0
 * Text Domain: woocommerce-ultimate-gift-card
 * Domain Path: /languages
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$activated = true;
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		$activated = false;
	}
} else {
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$activated = false;
	}
}

/**
 * Check if WooCommerce is active
 */
if ( $activated ) {
	$wp_upload = wp_upload_dir();
	$wp_upload_dir = $wp_upload['basedir'];
	$wp_upload_url = $wp_upload['baseurl'];

	define( 'MWB_UPLOAD_DIR', $wp_upload_dir );
	define( 'MWB_UPLOAD_URL', $wp_upload_url );
	define( 'MWB_WGM_DIRPATH', plugin_dir_path( __FILE__ ) );
	define( 'MWB_WGM_URL', plugin_dir_url( __FILE__ ) );
	define( 'MWB_WGM_HOME_URL', admin_url() );
	define( 'MWB_WGM_PLUGIN_VERSION', '2.7.6' );
	include_once MWB_WGM_DIRPATH . '/includes/woocommerce-ultimate-gift-card-class.php';
	include_once MWB_WGM_DIRPATH . '/function/woocommerce-ultimate-gift-card-function.php';
	include_once MWB_WGM_DIRPATH . '/Qrcode/giftcard-qrcode-addon.php';
	include_once MWB_WGM_DIRPATH . '/Shipping/giftcard-shipping-addon.php';
	global $wp_version;
	if ( $wp_version >= '4.9.6' ) {
		include_once MWB_WGM_DIRPATH . 'mwb_wgm_gdpr.php';
	}

	/**
	 * This function is used to load language'.
	 *
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */

	function mwb_wgm_load_plugin_textdomain() {
		$domain = 'woocommerce-ultimate-gift-card';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, MWB_WGM_DIRPATH . 'languages/' . $domain . '-' . $locale . '.mo' );
		$var = load_plugin_textdomain( $domain, false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

	add_action( 'plugins_loaded', 'mwb_wgm_load_plugin_textdomain' );

	/**
	 * Dynamically Generate Coupon Code
	 *
	 * @name mwb_wgm_coupon_generator
	 * @param number $length
	 * @return string
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */
	function mwb_wgm_coupon_generator( $length = 5 ) {
		if ( $length == '' ) {
			$length = 5;
		}
		$password = '';
		$alphabets = range( 'A', 'Z' );
		$numbers = range( '0', '9' );
		$final_array = array_merge( $alphabets, $numbers );
		while ( $length-- ) {
			$key = array_rand( $final_array );
			$password .= $final_array[ $key ];
		}
		$giftcard_prefix = get_option( 'mwb_wgm_general_setting_giftcard_prefix', '' );
		$password = $giftcard_prefix . $password;
		$password = apply_filters( 'mwb_wgm_custom_coupon', $password );
		return $password;
	}

	/**
	 * This function is used to add a new custom product type in woocommerce
	 *
	 * @name mwb_wgm_register_gift_card_product_type
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */
	function mwb_wgm_register_gift_card_product_type() {
		class WC_Product_Wgm_gift_card extends WC_Product {
			/**
			 * Initialize simple product.
			 *
			 * @param mixed $product
			 */
			public function __construct( $product ) {
				$this->product_type = 'wgm_gift_card';
				parent::__construct( $product );
			}
		}
	}

	// on plugin load
	add_action( 'plugins_loaded', 'mwb_wgm_register_gift_card_product_type' );
	add_action( 'init', 'mwb_wgm_insert_new_templates' );
	add_action( 'init', 'mwb_wgm_simple_birthday_templates' );
	add_action( 'init', 'mwb_wgm_insert_christmas_template' );
	add_action( 'init', 'mwb_wgm_insert_horizontal_temp' );
	add_action( 'init', 'mwb_wgm_new_mom_template' );
	add_shortcode( 'mwb_check_your_gift_card_balance', 'mwb_gift_card_balance' );

	function mwb_gift_card_balance() {
		$html = '<div class="mwb_gift_card_balance_wrapper">';
		$html .= '<span class="gift_card_balance_email_desc">' . __( 'Enter buyer email or your email/name', 'woocommerce-ultimate-gift-card' ) . '</span>';
		$html .= '<p class="gift_card_balance_email"><input type="email" id="gift_card_balance_email" class="mwb_gift_balance" placeholder="' . __( 'Enter Email/Name', 'woocommerce-ultimate-gift-card' ) . '" required="required"></p>';
		$html .= '<span class="gift_card_balance_code_desc">' . __( 'Enter gift card coupon code', 'woocommerce-ultimate-gift-card' ) . '</span>';
		$html .= '<p class="gift_card_code"><input type="text" id="gift_card_code" class="mwb_gift_balance" placeholder="' . __( 'Enter Code', 'woocommerce-ultimate-gift-card' ) . '" required="required"></p>';

		$html .= '<p class="mwb_check_balance"><input type="button" id="mwb_check_balance" value="' . __( 'Check Balance', 'woocommerce-ultimate-gift-card' ) . '"><span id="mwb_notification"></span></p>';
		$html .= '<div style="display: none;" class="loading-style-bg" id="mwb_wgm_loader"><img src="' . MWB_WGM_URL . '/assets/images/loading.gif"></div></div>';
		return $html;
	}

	function mwb_wgm_new_mom_template() {
		if ( isset( $_GET['import_new_mother_template'] ) ) {
			$pagetemplate = get_option( 'mwb_wgm_new_mom_temp', false );
			if ( $pagetemplate == false ) {
				update_option( 'mwb_wgm_new_mom_temp', true );
				$filename = array( plugin_dir_path( __FILE__ ) . 'assets/images/mom.png' );
				foreach ( $filename as $key => $value ) {
					$upload_file = wp_upload_bits( basename( $value ), null, file_get_contents( $value ) );
					if ( ! $upload_file['error'] ) {
						$filename = $upload_file['file'];
						// The ID of the post this attachment is for.

						$parent_post_id = 0;

						// Check the type of file. We'll use this as the 'post_mime_type'.
						$filetype = wp_check_filetype( basename( $filename ), null );
						// Get the path to the upload directory.
						$wp_upload_dir = wp_upload_dir();
						// Prepare an array of post data for the attachment.
						$attachment = array(
							'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
							'post_mime_type' => $filetype['type'],
							'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),

							'post_status'    => 'inherit',
						);
						// Insert the attachment.

						$attach_id = wp_insert_attachment( $attachment, $filename, 0 );
						// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
						require_once( ABSPATH . 'wp-admin/includes/image.php' );

						// Generate the metadata for the attachment, and update the database record.
						$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );

						wp_update_attachment_metadata( $attach_id, $attach_data );
						$arr[] = $attach_id;
					}
				}
				$new_mom_html = '<div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">(Optional) This text will appear in the inbox preview, but not the email body.</div><table class="email-container table-wrap" style="margin: auto;" role="presentation" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#efefef;"><tbody><tr><td dir="ltr" style="border: 1px solid #00897b;" align="center" bgcolor="#efefef" width="100%"><table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center" class="logo-content-wrap"><tbody><tr><td class="stack-column-center logo-wrap" width="50%"><table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td dir="ltr" style="padding: 0px 25px; padding-left: 0;" valign="top"><p style="color: #00897b; font-size: 25px; font-family: sans-serif; margin: 0px; padding-left: 10px;"><strong>[LOGO] </strong></p></td></tr></tbody></table></td><td class="stack-column-center content-wrap" style="" width="50%"><table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; line-height: 20px; color: #ffffff; text-align: right !important; padding: 0px 20px;" valign="top"><span style="color: #535151; font-size: 14px; line-height: 18px; display:block;">From-[FROM]</span><span style="color: #535151; font-size: 14px; line-height: 18px; display:block;">To-[TO]</span></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table class="email-container table-wrap" style="margin: auto;" role="presentation" border="0" width="600" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td dir="ltr" style="padding-top: 15px;" align="center" valign="top" bgcolor="#00897B" width="100%"><table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center" class="img-content-wrap"><tbody><tr><td class="stack-column-center" width="50%"><table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td dir="ltr" style="padding: 0px 25px; padding-left: 0;" valign="top"><span class="img-wrap">[FEATUREDIMAGE]</span></td></tr></tbody></table></td><td class="stack-column-center" style="vertical-align: top;" width="50%"><table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #ffffff; padding: 0px 30px; text-align: left; " valign="top"><p style="color: rgb(255, 255, 255); font-size: 46px; line-height: 60px; margin-top: 15px; margin-bottom: 15px;">I Love You Mom</p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td dir="ltr" align="center" valign="top" bgcolor="#fff" width="100%" style="position: relative;"><table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="stack-column-center" style="vertical-align: top;" width="50%"><table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center" style="position:relative; z-index:999;"><tbody><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; line-height: 20px; color: #ffffff; padding: 0px 30px; text-align: left; background-color: #efefef;" valign="top"><p style="text-align: center; line-height: 25px; color: rgb(21, 21, 21); white-space: pre-line; font-size: 16px; padding: 20px;">[MESSAGE]</p></td></tr></tbody></table></td></tr><tr>[BACK]<td style="padding: 15px 10px; font-size: 26px; text-transform: uppercase; text-align: center; font-weight: bold; color: rgb(39, 39, 39); font-family: sans-serif; position: relative; z-index: 99;"><p style="letter-spacing: 1px; padding: 10px 10px; margin: 0px; text-transform: uppercase; text-align: center; color: #00897b; font-weight: bold; font-size: 13px;">coupon code</p>[COUPON]<p style="letter-spacing: 1px; padding: 15px 10px; margin: 0px; text-transform: uppercase; text-align: center; color: #00897b; font-weight: bold; font-size: 13px;">[EXPIRYDATE]</p></td></tr></tbody></table></td></tr><tr><td dir="ltr" style="padding-top: 12px; padding-bottom: 12px; background-color: #efefef;" align="center" valign="top" bgcolor="#fff" width="100%"><table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="stack-column-center" width="50%"><table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td dir="ltr" style="padding: 0px 25px; padding-right: 0;" valign="top"><p style="font-family: sans-serif; font-size: 25px; font-weight: bold; margin: 0px; padding: 5px; color: #272727; text-align: right;">[AMOUNT]</p></td></tr></tbody></table></td><td class="stack-column-center" style="vertical-align: top;" width="50%"><table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #ffffff; padding: 0px 30px; text-align: left; margin-top: 15px;" class="center-on-narrow arrow-img" valign="top">[ARROWIMAGE]</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table role="presentation" border="0" cellspacing="0" cellpadding="0" style="position:relative; z-index:999; background: rgb(0, 137, 123) none repeat scroll 0% 0%; color: rgb(255, 255, 255);" width="600" class="table-wrap footer-wrap"><tbody><tr><td style="padding: 10px; text-align: center; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly;"><p style="font-weight: bold; padding-top: 15px; padding-bottom: 15px; font-size: 16px;">[DISCLAIMER]</p></td></tr></tbody></table><style>.img-wrap > img{width:100%;}.back_bubble_img{bottom: 0;content: "";left: 0;margin: 0 auto;position: absolute;right: 0;}.back_bubble_img >img{width:100%;}@media screen and (max-width: 600px){.email-container{width: 100% !important;margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */.fluid{max-width: 90% !important;height: auto !important;margin-left: auto !important;margin-right: auto !important;}/* What it does: Forces table cells into full-width rows. */<br/>.stack-column,.stack-column-center{display: block !important;width: 100% !important;max-width: 100% !important;direction: ltr !important;}/* And center justify these ones. */.stack-column-center{text-align: center !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */.center-on-narrow{text-align: center !important;display: block !important;margin-left: auto !important;margin-right: auto !important;float: none !important;}table.center-on-narrow{display: inline-block !important;}.footer-wrap{width:100%;}}@media screen and (max-width: 500px){.img-content-wrap .stack-column-center{display: block; width: 100%;}.table-wrap{width:100%;}.logo-content-wrap .content-wrap{width:70%;}.logo-content-wrap .logo-wrap{width:30%;}.center-on-narrow.arrow-img{padding: 0 !important;}}</style>';
				$new_mom_css = ' html, body{margin: 0 auto !important; padding: 0 !important; height: 100% !important; width: 100% !important;}/* What it does: Stops email clients resizing small text. */ *{-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */ div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */ table, td{mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */ table{border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important; margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */ img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */ .mobile-link--footer a, a[x-apple-data-detectors]{color:inherit !important; text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */ .button-link{text-decoration: none !important;}.button-td, .button-a{transition: all 100ms ease-in;}.button-td:hover, .button-a:hover{background: #555555 !important; border-color: #555555 !important;}';
				$gifttemplate_new = array(
					'post_title' => __( 'Happy Mothers Day', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $new_mom_html,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);
				$parent_post_id = wp_insert_post( $gifttemplate_new );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $new_mom_css ) );
				set_post_thumbnail( $parent_post_id, $arr[0] );
			}
		}
	}

	function mwb_wgm_insert_horizontal_temp() {
		if ( isset( $_GET['import_horizontal_templates'] ) ) {
			$pagetemplate = get_option( 'mwb_wgm_coming_new_year', false );
			if ( $pagetemplate == false ) {
				update_option( 'mwb_wgm_coming_new_year', true );
				$filename = array( plugin_dir_path( __FILE__ ) . 'assets/images/fireworks.png' );
				foreach ( $filename as $key => $value ) {
					$upload_file = wp_upload_bits( basename( $value ), null, file_get_contents( $value ) );
					if ( ! $upload_file['error'] ) {
						$filename = $upload_file['file'];
						// The ID of the post this attachment is for.

						$parent_post_id = 0;

						// Check the type of file. We'll use this as the 'post_mime_type'.
						$filetype = wp_check_filetype( basename( $filename ), null );
						// Get the path to the upload directory.
						$wp_upload_dir = wp_upload_dir();
						// Prepare an array of post data for the attachment.
						$attachment = array(
							'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
							'post_mime_type' => $filetype['type'],
							'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),

							'post_status'    => 'inherit',
						);
						// Insert the attachment.

						$attach_id = wp_insert_attachment( $attachment, $filename, 0 );
						// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
						require_once( ABSPATH . 'wp-admin/includes/image.php' );

						// Generate the metadata for the attachment, and update the database record.
						$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );

						wp_update_attachment_metadata( $attach_id, $attach_data );
						$arr[] = $attach_id;
					}
				}
				$horizontal_html = '<div class="main"><table style="max-width: 700px; background-color: #1a1a56;" role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="left-sec"><span class="title-logo" style=" font-size: 55px; color: #fff; text-transform: capitalize; display: block; font-weight: bold; padding-left: 40px;">happy</span><span class="title-logo" style=" font-size: 55px; color: #fff; text-transform: capitalize; display: block; font-weight: bold; padding-left: 40px;">new year</span><table style="background-color: #1a1a56; height: 311px;" width="297"><tbody><tr class="detail-price"><td><p style="color: #fff; font-size: 20px; padding-left: 40px; white-space: pre-line;">[MESSAGE]</p></td></tr><tr class="detail-from" style="display: block;"><td style="color: #fff; font-size: 18px; display: block; padding-left: 40px;"><span style="vertical-align: top; display: inline-block; padding-right: 5px;">From:</span><span style="display: inline-block; word-break: break-all;">[FROM]</span></td></tr><tr class="detail-from" style="display: block;"><td style="color: #fff; sans-serif; font-size: 18px; display: block; padding-left: 40px;"><span style="display: inline-block; vertical-align: top; padding-right: 5px;">To:</span><span style="display: inline-block; word-break: break-all;">[TO]</span></td></tr><tr class="price"><td style="color: #fff; display: block; font-size: 48px; margin-top: 30px; font-weight: 900; padding-left: 40px;">[AMOUNT]</td></tr></tbody></table></td><td class="right-sec"><table style="text-align: center; background-color: #1a1a56; height: 324px;" width="278"><tbody><tr class="fireworks"><td>[FEATUREDIMAGE]</td></tr><tr><td><span style="color: #ffffff; font-size: 25px; margin: 25px 0px; display: block; font-weight: 600;">COUPON CODE</span></td></tr><tr><td><span style="color: #ffffff; font-size: 30px; margin: 5px 0px; padding: 10px 30px; text-align: center; font-weight: 600;">[COUPON]</span></td></tr><tr><td><span style="color: #ffffff; font-size: 25px; margin: 25px 0px; display: block; font-weight: 600;">ED:[EXPIRYDATE]</span></td></tr></tbody></table></td></tr></tbody><tfoot><tr style="border-top: solid 1px #090919; padding-top: 20px; padding-bottom: 20px; background-color: #141431;"><th style="font-size: 16px; color: #ffffff; text-align: center; line-height: 30px;" colspan="2"><div style="display: block; padding: 20px 0;">[DISCLAIMER]</div></th></tr></tfoot></table></div><style>@media only screen and (max-width: 700px){td.left-sec span.title-logo{font-size: 55px !important;}tr.detail-price td p{font-size: 17px !important;}}@media only screen and (max-width: 550px){td.left-sec{width: 100% !important; text-align: center; float:none;display: block !important;table-layout: fixed;}td.right-sec{width: 100% !important; text-align: center;float:none; display: block !important;table-layout: fixed;}td.right-sec table{width: 100%;}td.right-sec table tr.fireworks td img{max-width: 300px;}tr.detail-price td p{padding: 0px 15px !important;}td.left-sec span.title-logo{padding: 0px !important;}tr.price td{padding: 0px !important;}tr.detail-from td span{padding: 0px !important;}tr.bottom-contant td span{font-size: 14px !important; padding: 0px 10px; line-height: 20px !important;}td.left-sec table{width: 100%;text-align: center;}tr.price td{padding: 0px !important;margin-top: 0px !important;}}@media only screen and (max-width: 400px){td.left-sec span.title-logo{font-size: 44px !important;}}</style>';
				$horizontal_css = 'tr.fireworks td img{width: 100%;max-width: 250px;}td.right-sec{width: 50%; float: right;display:inline-block;}td.left-sec{width: 50%; float: left;display:inline-block;}';
				$gifttemplate_new = array(
					'post_title' => __( 'Horizontal Template', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $horizontal_html,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);
				$parent_post_id = wp_insert_post( $gifttemplate_new );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $horizontal_css ) );
				set_post_thumbnail( $parent_post_id, $arr[0] );
			}
		}
	}

	function mwb_wgm_simple_birthday_templates() {
		if ( isset( $_GET['import_simple_birthday_templates'] ) ) {
			$pagetemplate = get_option( 'mwb_wgm_simple_birthday', false );
			if ( $pagetemplate == false ) {
				update_option( 'mwb_wgm_simple_birthday', true );
				$filename = array( plugin_dir_path( __FILE__ ) . 'assets/images/simple_bdy.jpg' );
				foreach ( $filename as $key => $value ) {
					$upload_file = wp_upload_bits( basename( $value ), null, file_get_contents( $value ) );
					if ( ! $upload_file['error'] ) {
						$filename = $upload_file['file'];
						// The ID of the post this attachment is for.

						$parent_post_id = 0;

						// Check the type of file. We'll use this as the 'post_mime_type'.
						$filetype = wp_check_filetype( basename( $filename ), null );
						// Get the path to the upload directory.
						$wp_upload_dir = wp_upload_dir();
						// Prepare an array of post data for the attachment.
						$attachment = array(
							'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
							'post_mime_type' => $filetype['type'],
							'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),

							'post_status'    => 'inherit',
						);
						// Insert the attachment.

						$attach_id = wp_insert_attachment( $attachment, $filename, 0 );
						// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
						require_once( ABSPATH . 'wp-admin/includes/image.php' );

						// Generate the metadata for the attachment, and update the database record.
						$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
						wp_update_attachment_metadata( $attach_id, $attach_data );
						$arr[] = $attach_id;
					}
				}
				$simple_bdy_html = '<style>/* What it does: Remove spaces around the email design added by some email clients. */ /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */ html, body{margin: 0 auto !important; padding: 0 !important; height: 100% !important; width: 100% !important;}body *{box-sizing: border-box;}/* What it does: Stops email clients resizing small text. */ *{-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */ div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */ table, td{mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */ table{border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important; margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */ img{-ms-interpolation-mode:bicubic; width: 100%;}/* What it does: A work-around for iOS meddling in triggered links. */ .mobile-link--footer a, a[x-apple-data-detectors]{color:inherit !important; text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */ .button-link{text-decoration: none !important;}</style><style>/* What it does: Hover styles for buttons */ .button-td, .button-a{transition: all 100ms ease-in;}.button-td:hover, .button-a:hover{background: #555555 !important; border-color: #555555 !important;}/* Media Queries */ @media screen and (max-width: 599px){.email-container{width: 100% !important; margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */ .fluid{max-width: 100% !important; height: auto !important; margin-left: auto !important; margin-right: auto !important;}/* What it does: Forces table cells into full-width rows. */ .stack-column, .stack-column-center{display: block !important; width: 100% !important; max-width: 100% !important; direction: ltr !important;}/* And center justify these ones. */ .stack-column-center{text-align: center !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */ .center-on-narrow{text-align: center !important; display: block !important; margin-left: auto !important; margin-right: auto !important; float: none !important;}table.center-on-narrow{display: inline-block !important;}}</style><center style="width: 100%; background: #222222;"></center><div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">(Optional) This text will appear in the inbox preview, but not the email body.</div><table class="email-container" style="margin: auto;" role="presentation" border="0" width="600" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td align="center" bgcolor="#ffffff">[FEATUREDIMAGE]</td></tr><tr><td dir="ltr" align="center" valign="top" bgcolor="#ffffff" width="100%"><table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="line-height: 0; overflow: hidden; height: 30px;"></td></tr><tr><td class="stack-column-center" style="padding: 20px 0px; vertical-align: top; border-right: 1px solid #dddddd !important;" width="50%"><table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff; padding: 0 20px 20px;" valign="top"><p style="margin: 10px 0 30px 0; text-align: left; font-weight: bold; font-size: 28px;"><span style="color: #333333; margin: 20px 0;">[AMOUNT]</span></p></td></tr><tr><td dir="ltr" style="padding: 30px 20px 0 20px;" valign="top"><p style="color: #333333; font-family: sans-serif; margin: 0px; font-size: 16px;"><span style="font-weight: bold; display: inline-block; text-align: left; font-size: 14px; width: 130px;">COUPON CODE:</span><span style="font-weight: bold; text-transform: uppercase; display: inline-block; text-align: left; font-size: 14px;">[COUPON]</span></p><p style="color: #333333; font-family: sans-serif; margin-bottom: 30px; font-size: 16px;"><span style="font-weight: bold; display: inline-block; text-align: left; font-size: 14px; width: 130px;">EXPIRY DATE:</span><span style="font-weight: bold; text-transform: uppercase; display: inline-block; text-align: left; font-size: 14px;">[EXPIRYDATE]</span></p></td></tr></tbody></table></td><td class="stack-column-center" style="padding: 20px 0px;" valign="top" width="50%"><table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff; padding: 0px 30px 0 20px; min-height: 170px; height: auto;" valign="top"><p style="color: #333333; font-size: 15px;margin-bottom: 30px">[MESSAGE]</p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; padding: 0 0 0 20px; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #333333;" valign="top"><p style="margin-bottom: 0px; font-size: 16px; margin-top: 20px"><span style="font-weight: bold; display: inline-block; width: 20%; font-size: 15px;">From-</span><span style="display: inline-block; width: 75%; text-align: left; font-size: 14px;">[FROM]</span></p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; padding: 0 0 0 20px; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #333333;" valign="top"><p style="margin-top: 0px; font-size: 16px; line-height: 25px;"><span style="font-weight: bold; display: inline-block; width: 20%; font-size: 15px;">To-</span><span style="display: inline-block; width: 75%; text-align: left; font-size: 14px;">[TO]</span></p></td></tr></tbody></table></td></tr><tr><td style="line-height: 0; overflow: hidden; height: 30px;"></td></tr></tbody></table></td></tr><tr><td bgcolor="#ffffff"><table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="text-align: center; padding: 10px; border-top: 1px solid #dddddd !important; font-family: sans-serif; font-size: 16px; mso-height-rule: exactly; line-height: 20px; color: #333333;">[DISCLAIMER]</td></tr></tbody></table></td></tr></tbody></table>';
				// $simple_bdy_css = 'tr.fireworks td img{width: 100%;max-width: 250px;}td.right-sec{width: 50%; float: right;display:inline-block;}td.left-sec{width: 50%; float: left;display:inline-block;}';
				$gifttemplate_new = array(
					'post_title' => __( 'New Birthday Template', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $simple_bdy_html,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);
				$parent_post_id = wp_insert_post( $gifttemplate_new );
				set_post_thumbnail( $parent_post_id, $arr[0] );
			}
		}
	}

	function mwb_wgm_insert_christmas_template() {
		if ( isset( $_GET['import_christmas_templates'] ) ) {
			$pagetemplate = get_option( 'mwb_wgm_new_christmas_template', false );
			if ( $pagetemplate == false ) {
				update_option( 'mwb_wgm_new_christmas_template', true );
				$filename = array( plugin_dir_path( __FILE__ ) . 'assets/images/merry_christmas.jpg' );
				foreach ( $filename as $key => $value ) {
					$upload_file = wp_upload_bits( basename( $value ), null, file_get_contents( $value ) );
					if ( ! $upload_file['error'] ) {
						$filename = $upload_file['file'];
						// The ID of the post this attachment is for.

						$parent_post_id = 0;

						// Check the type of file. We'll use this as the 'post_mime_type'.
						$filetype = wp_check_filetype( basename( $filename ), null );

						// Get the path to the upload directory.
						$wp_upload_dir = wp_upload_dir();

						// Prepare an array of post data for the attachment.
						$attachment = array(
							'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
							'post_mime_type' => $filetype['type'],
							'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),

							'post_status'    => 'inherit',
						);
						// Insert the attachment.

						$attach_id = wp_insert_attachment( $attachment, $filename, 0 );
						// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
						require_once( ABSPATH . 'wp-admin/includes/image.php' );

						// Generate the metadata for the attachment, and update the database record.
						$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
						wp_update_attachment_metadata( $attach_id, $attach_data );
						$arr[] = $attach_id;
					}
				}
				$christmas_html = '&nbsp;<div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">(Optional) This text will appear in the inbox preview, but not the email body.</div><table class="email-container" style="margin: auto;" border="0" width="600" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding-top: 20px; text-align: center; color: #f48643; font-weight: bold; padding-left: 20px; font-size: 20px; font-family: sans-serif; position: absolute;">[LOGO]</td></tr></tbody></table><table class="email-container" style="margin: auto;" border="0" width="600" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#ffffff"><span class="feature_image" style="display: block; margin: 0px auto; width: 100%;"> [FEATUREDIMAGE] </span></td></tr><tr><td style="text-align: center; font-family: sans-serif; font-size: 15px; color: #1976e7; vertical-align: middle; display: table-cell; background: #7D0404;"><h2 style="font-size: 16px; display: block; text-align: center!important; border: 5px dashed #ffffff; padding: 15px 0px; margin: 0px; color: #fff;">COUPON CODE <span style="display: block; font-size: 24px; padding: 8px 0 0 0; color: #fff;">[COUPON]</span> <span style="display: block; font-size: 16px; padding: 8px 0 0 0;">(Ed:[EXPIRYDATE])</span></h2></td></tr><tr><td dir="ltr" style="padding: 22px 10px; background: #fff;" align="center" valign="top" bgcolor="#ffb001" width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="stack-column-center" valign="top" width="50%"><table border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="img_width_left_table" dir="ltr" style="padding: 0 10px 0 10px; width: 50%;" valign="top">[DEFAULTEVENT]</td></tr></tbody></table></td><td class="stack-column-center" valign="top" width="50%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; line-height: 20px; color: #ffffff; padding: 0px 30px 0px 0px; word-wrap: break-word; text-align: left;" valign="top"><p style="color: #000; font-size: 15px; height: auto; min-height: 180px; padding: 0px 0px 20px; text-align: left; word-break: break-word; white-space: pre-line;">[MESSAGE]</p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #000; word-wrap: break-word;" valign="top"><p style="margin-bottom: 0px; font-size: 16px; text-align: left; color: #000;"><span style="display: inline-block; text-align: right; font-size: 15px; vertical-align: top; color: #000;">From-</span><span style="display: inline-block; text-align: left; font-size: 14px; vertical-align: top; word-break: break-all; color: #000;">[FROM]</span></p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; word-wrap: break-word; color: #fff;" valign="top"><p style="margin-top: 0px; font-size: 16px; line-height: 25px; text-align: left;"><span style="display: inline-block; text-align: right; font-size: 15px; vertical-align: top; color: #000;">To-</span><span style="display: inline-block; text-align: left; font-size: 14px; vertical-align: top; word-break: break-all; color: #000;">[TO]</span></p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff; word-wrap: break-word;" valign="top"><p style="text-align: left; font-weight: bold; font-size: 28px;"><span style="color: #800505; margin: 20px 0; vertical-align: top;">[AMOUNT]/- </span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="background: #7D0404;"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 40px; font-family: sans-serif; font-size: 16px; mso-height-rule: exactly; line-height: 20px; color: #fff; text-align: center;">[DISCLAIMER]</td></tr></tbody></table></td></tr></tbody></table><style>@media screen and (max-width: 599px){.email-container{width: 100% !important;margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */.fluid{max-width: 100% !important;height: auto !important;margin-left: auto !important;margin-right: auto !important;}/* What it does: Forces table cells into full-width rows. */.stack-column,.stack-column-center{display: block !important;width: 100% !important;max-width: 100% !important;direction: ltr !important;}/* And center justify these ones. */.stack-column-center{text-align: center !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */.center-on-narrow{text-align: center !important;display: block !important;margin-left: auto !important;margin-right: auto !important;float: none !important;}table.center-on-narrow{display: inline-block !important;}}.feature_image > img{width: 100%!important;}</style>';
				$christmas_css = ' html,body{margin: 0 auto !important;padding: 0 !important;height: 100% !important;width: 100% !important;}/* What it does: Stops email clients resizing small text. */*{-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */table,td{mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */table{border-spacing: 0 !important;border-collapse: collapse !important;table-layout: fixed !important;margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */.mobile-link--footer a,a[x-apple-data-detectors]{color:inherit !important;text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */.button-link{text-decoration: none !important;}.button-td,.button-a{transition: all 100ms ease-in;}.button-td:hover,.button-a:hover{background: #555555 !important;border-color: #555555 !important;}table.email-container{border: solid 1px #ccc !important;}td.img_width_left_table{}';
				$gifttemplate_new = array(
					'post_title' => __( 'Merry Christmas', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $christmas_html,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);
				$parent_post_id = wp_insert_post( $gifttemplate_new );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $christmas_css ) );
				set_post_thumbnail( $parent_post_id, $arr[0] );
			}
		}
	}

	function mwb_wgm_insert_new_templates() {

		if ( isset( $_GET['import_templates'] ) ) {
			$pagetemplate = get_option( 'mwb_wgm_new_pdf_support_templates_with_A4', false );

			if ( $pagetemplate == false ) {
				update_option( 'mwb_wgm_new_pdf_support_templates_with_A4', true );
				// $filename should be the path to a file in the upload directory.
				$filename = array(
					plugin_dir_path( __FILE__ ) . 'assets/images/thanksgive.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/president-day.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/halloween.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/Group-1.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/Shape-4-copy-4.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/mother.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/christmas.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/banner.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/independence.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/newyear.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/birthday.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/anniversary.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/eid.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/giftimg.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/giftimg2.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/raksha-bandhan.png',
					plugin_dir_path( __FILE__ ) . 'assets/images/diwali.png',
				);
				foreach ( $filename as $key => $value ) {

					$upload_file = wp_upload_bits( basename( $value ), null, file_get_contents( $value ) );

					if ( ! $upload_file['error'] ) {

						$filename = $upload_file['file'];
						// The ID of the post this attachment is for.

						$parent_post_id = 0;

						// Check the type of file. We'll use this as the 'post_mime_type'.
						$filetype = wp_check_filetype( basename( $filename ), null );

						// Get the path to the upload directory.
						$wp_upload_dir = wp_upload_dir();

						// Prepare an array of post data for the attachment.
						$attachment = array(
							'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
							'post_mime_type' => $filetype['type'],
							'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),

							'post_status'    => 'inherit',
						);
						// Insert the attachment.

						$attach_id = wp_insert_attachment( $attachment, $filename, 0 );
						// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
						require_once( ABSPATH . 'wp-admin/includes/image.php' );

						// Generate the metadata for the attachment, and update the database record.
						$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );

						wp_update_attachment_metadata( $attach_id, $attach_data );
						$arr[] = $attach_id;
					}
				}
				$template_html11 = '<center style="width: 100%;"><div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;">(Optional) This text will appear in the inbox preview, but not the email body.</div><table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container"><tr><td style="padding-top: 20px; text-align: left; background-color: #ffffff;color:#f48643;font-weight:bold;padding-left:20px;font-size:20px;font-family:sans-serif;">[LOGO]</td></tr></table><table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container" align="center"> <tr> <td bgcolor="#ffffff"> <span style="display: block; margin: 0px auto; width: 100%;" class="feature_image"> [FEATUREDIMAGE] </span> </td></tr><tr><td style="background-color:#ffb001;padding:20px 0;"> </td></tr><tr><td style="text-align: center; font-family: sans-serif; font-size: 15px; color: rgb(25, 118, 231); vertical-align: middle; display: table-cell; background: #f27326; padding: 5px;"><h2 style="font-size: 16px; display: block; text-align:center!important; border: 2px dashed rgb(255, 255, 255); padding: 15px 0px; margin: 0px; color: rgb(255, 255, 255);">COUPON CODE <span style="display:block; font-size:24px; padding:8px 0 0 0;">[COUPON]</span> <span style="display:block;font-size:16px; padding:8px 0 0 0;">(Ed:[EXPIRYDATE])</span></h2></td></tr><tr > <td dir="ltr" style="padding:51px 10px;" width="100%" valign="top" bgcolor="#ffb001" align="center"><table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"><tr > <td class="stack-column-center" width="50%" valign="top"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"><tr ><td dir="ltr" valign="top" style="padding: 0 10px 0 10px;">[DEFAULTEVENT]</td></tr></table></td><td width="50%" valign="top" class="stack-column-center"><table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; line-height: 20px; color: rgb(255, 255, 255); padding: 0px 30px 0px 0px; word-wrap:break-word; text-align: left;" valign="top"><p style="color: rgb(255, 255, 255); font-size: 15px; height: auto; min-height: 180px; padding: 0px 0px 20px;text-align: left;"> [MESSAGE] </p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif;font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff;word-wrap: break-word; " valign="top"><p style="margin-bottom:0px;font-size:16px;text-align: left;"><span style="display: inline-block;text-align:right;font-size:15px;vertical-align:top;">From-</span><span style="display: inline-block; text-align:left;font-size:14px; vertical-align:top;">[FROM]</span></p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif;font-size: 15px; mso-height-rule: exactly; line-height: 20px;word-wrap: break-word;color: #fff; " valign="top"><p style="margin-top:0px;font-size:16px;line-height:25px;text-align: left;"><span style="display: inline-block; text-align: right;font-size:15px; vertical-align:top;">To-</span><span style="display: inline-block; text-align:left;font-size:14px;vertical-align:top;">[TO]</span></p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif;font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff; word-wrap: break-word;" valign="top"> <p style="text-align:left;font-weight:bold;font-size:28px;"> <span style="color:#dd6e00; margin:20px 0; vertical-align:top;">[AMOUNT]/- </span> </p></td></tr></table></td></tr></table></td></tr><tr ><td bgcolor="#f27326"><table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"><tr><td style="padding: 40px; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff; font-size:16px; text-align: center;">[DISCLAIMER]</td></tr></table></td></tr></table></center><style>@media screen and (max-width: 599px){.email-container{width: 100% !important;margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */.fluid{max-width: 100% !important;height: auto !important;margin-left: auto !important;margin-right: auto !important;}/* What it does: Forces table cells into full-width rows. */.stack-column,.stack-column-center{display: block !important;width: 100% !important;max-width: 100% !important;direction: ltr !important;}/* And center justify these ones. */.stack-column-center{text-align: center !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */.center-on-narrow{text-align: center !important;display: block !important;margin-left: auto !important;margin-right: auto !important;float: none !important;}table.center-on-narrow{display: inline-block !important;}}.feature_image > img{width: 100%!important;}</style>';

				$template11_css = 'html,body{margin: 0 auto !important;padding: 0 !important;height: 100% !important;width: 100% !important;}/* What it does: Stops email clients resizing small text. */*{-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */table,td{mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */table{border-spacing: 0 !important;border-collapse: collapse !important;table-layout: fixed !important;margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */.mobile-link--footer a,a[x-apple-data-detectors]{color:inherit !important;text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */.button-link{text-decoration: none !important;}.button-td,.button-a{transition: all 100ms ease-in;}.button-td:hover,.button-a:hover{background: #555555 !important;border-color: #555555 !important;}table.email-container{border: solid 1px #ccc !important;}span.feature_image img{width: 100%;height: 300px;}';

				$gifttemplate = array(
					'post_title' => __( 'Thanks Giving Day', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $template_html11,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $template11_css ) );
				set_post_thumbnail( $parent_post_id, $arr[0] );
				$template_html12 = '<center style="width: 100%;"><div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;">(Optional) This text will appear in the inbox preview, but not the email body.</div><table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container"><tr><td style="padding-top: 5px; text-align: left; padding-left: 20px; background-color: rgb(255, 255, 255); color: rgb(60, 100, 172); font-family: sans-serif; font-weight: bold; font-size: 20px;">[LOGO]</td></tr></table><table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container" align="center"><tr><td bgcolor="#fff"><span class="feature_img">[FEATUREDIMAGE]</span></td></tr><tr><td style="background-color:#fff;padding:5px 0;"></td></tr><tr><td style="text-align: center; font-family: sans-serif; font-size: 15px; color: rgb(25, 118, 231); vertical-align: middle; display: table-cell; background: #ce2a2b; padding: 5px;"><h2 style="font-size: 16px; text-align:center!important; display: block; color: rgb(25, 118, 231); background: rgb(206, 42, 43) none repeat scroll 0% 0%; margin: 0px; border: 2px dashed rgb(255, 255, 255); padding: 15px 0px; color: #ffffff;">COUPON CODE <span style="display:block; font-size:24px; padding:8px 0 0 0;">[COUPON]</span> <span style="display:block;font-size:16px; padding:8px 0 0 0;">(Ed:[EXPIRYDATE])</span></h2></td></tr><tr > <td dir="ltr" style="padding:20px 10px;" width="100%" valign="top" bgcolor="#ffffff" align="center"><table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"><tr ><td width="50%" class="stack-column-center" style="vertical-align: top;"><table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"><tr ><td dir="ltr" valign="top" style="padding: 0 10px 0 10px;">[DEFAULTEVENT]</td></tr></table></td><td width="50%" valign="top" class="stack-column-center"><table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td dir="ltr" valign="top" style="font-family: sans-serif;font-size: 15px; mso-height-rule: exactly; line-height: 20px;color: #000; margin:0px;word-wrap:break-word; text-align: left;" class="center-on-narrow"><p style="color:#000; font-size:15px;min-height:180px;height:auto; padding: 0px 0px 20px;margin:0; text-align: left;">[MESSAGE]</p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif;font-size: 15px; mso-height-rule: exactly;word-wrap: break-word;line-height: 20px; color: #000; " valign="top"><p style="margin-bottom:0px;font-size:16px;text-align: left;"><span style="display: inline-block; text-align:right;font-size:15px; vertical-align:top;">From-</span><span style="display: inline-block;width: 180px;text-align:left;font-size:14px; word-wrap: break-word; vertical-align:top;">[FROM]</span></p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif;font-size: 15px; mso-height-rule: exactly; line-height: 20px;word-wrap: break-word;color: #000; " valign="top"><p style="margin-top:0px;font-size:16px;line-height:25px;text-align: left;"><span style="display: inline-block; text-align: right;font-size:15px;vertical-align:top;">To-</span><span style="display: inline-block;width: 180px;text-align:left;font-size:14px; vertical-align:top;">[TO]</span></p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif;font-size: 15px; mso-height-rule: exactly; line-height: 20px;word-wrap: break-word;color: #fff;" valign="top"> <p style="text-align:left; vertical-align:top; font-weight:bold;font-size:28px;"> <span style="color:#c31a1a; margin:20px 0;">[AMOUNT]/- </span> </p></td></tr></table></td></tr></table></td></tr><tr ><td bgcolor="#ce2a2b"><table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"><tr><td style="padding: 10px 40px; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff; font-size:16px;">[DISCLAIMER]</td></tr></table></td></tr></table></center><style>@media screen and (max-width: 599px){.email-container{width: 100% !important;margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */.fluid{max-width: 100% !important;height: auto !important;margin-left: auto !important;margin-right: auto !important;}/* What it does: Forces table cells into full-width rows. */.stack-column,.stack-column-center{display: block !important;width: 100% !important;max-width: 100% !important;direction: ltr !important;}/* And center justify these ones. */.stack-column-center{text-align: center !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */.center-on-narrow{text-align: center !important;display: block !important;margin-left: auto !important;margin-right: auto !important;float: none !important;}table.center-on-narrow{display: inline-block !important;}}.feature_img > img{margin: 0 auto; display: block; width:100%;}</style>';

				$gifttemplate = array(
					'post_title' => __( 'President Day', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $template_html12,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);
				$template12_css = 'html,body{margin: 0 auto !important;padding: 0 !important;height: 100% !important;width: 100% !important;}/* What it does: Stops email clients resizing small text. */*{-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */table,td{mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */table{border-spacing: 0 !important;border-collapse: collapse !important;table-layout: fixed !important;margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */.mobile-link--footer a,a[x-apple-data-detectors]{color:inherit !important;text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */.button-link{text-decoration: none !important;}.button-td,.button-a{transition: all 100ms ease-in;}.button-td:hover,.button-a:hover{background: #555555 !important;border-color: #555555 !important;}table.email-container{border: solid 1px #ccc !important;}span.feature_img img{width: 35%;} ';

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $template12_css ) );
				set_post_thumbnail( $parent_post_id, $arr[1] );

				$template_html13 = '<center style="width: 100%;"> <div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;"> (Optional) This text will appear in the inbox preview, but not the email body. </div><table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container"><tr><td style="padding-top: 0px; text-align: left; background-color: #092845;color:#ffffff;font-weight:bold;padding-left:20px;font-size:20px;font-family:sans-serif;">[LOGO]</td></tr></table> <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container"> <tr> <td bgcolor="#00203e"> <span class="feature_img">[FEATUREDIMAGE]</span> </td></tr><tr> <td bgcolor="#ffffff" valign="middle" style="text-align: center; background-position: center center !important; background-size: cover !important;"><!--[if gte mso 9]><v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:600px;height:175px; background-position: center center !important;"><v:fill type="tile" src="http://placehold.it/600x230/222222/666666" color="#222222"/><v:textbox inset="0,0,0,0"><![endif]--><div> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td valign="middle" style="text-align: center; padding: 8px; font-family: sans-serif;mso-height-rule: exactly; line-height: 20px; color: rgb(54,79,103);"> <h1 style="border:2px dashed #14314d; text-align:center!important; padding:10px 33px; margin:0;font-size:15px;">COUPON <span style="display: block; padding: 15px 0;font-size:25px;">[COUPON]</span><span style="display: block;font-size:15px;">[EXPIRYDATE]</span></h1> </td></tr></table></div><!--[if gte mso 9]></v:textbox></v:rect><![endif]--></td></tr><tr> <td bgcolor=#00203e dir="ltr" align="center" valign="top" width="100%" style="padding: 20px 0;"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <td class="stack-column-center" style="vertical-align: top; width: 50%;"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td dir="ltr" valign="top" style="padding:0 15px;"> [DEFAULTEVENT] </td></tr></table> </td><td class="stack-column-center" style="vertical-align: top; width: 50%; "> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td dir="ltr" valign="top" style="font-family: sans-serif;font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff; min-height:180px;height:auto;padding:0 15px; word-wrap:break-word;" class="center-on-narrow"> <p style="word-spacing: 2px;text-align:left;font-size:16px;padding:0 0 20px 0; min-height:150px;" > [MESSAGE] </p></td></tr><tr> <td dir="ltr" valign="top" style="font-family: sans-serif;font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff;word-wrap: break-word; " class="center-on-narrow"> <p style="margin-bottom:0px;font-size:16px;text-align: left;"> <span style="display: inline-block; text-align:right;font-size:15px;vertical-align:top;">From-</span> <span style="display: inline-block;text-align:left;font-size:14px;vertical-align:top;"> [FROM]</span> </p></td></tr><tr> <td dir="ltr" valign="top" style="font-family: sans-serif;font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff;word-wrap: break-word;" class="center-on-narrow"> <p style="margin-top:0px;font-size:16px;line-height:25px;text-align: left;"> <span style="display: inline-block; text-align: right;font-size:15px; word-wrap: break-word; vertical-align:top;">To-</span> <span style="display: inline-block; vertical-align:top; width: 180px;text-align:left;font-size:14px;"> [TO]</span> </p></td></tr><td dir="ltr" valign="top" style="font-family: sans-serif;font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff;padding:5px 10px;word-wrap: break-word;" class="center-on-narrow"> <p style="text-align:left;font-weight:bold;font-size:30px;margin:10px 0; vertical-align:top;"> <span style="color:#fff; "><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol"></span>[AMOUNT]</span>/-</span> </p></td></tr></table> </td></tr></table> <tr > <td bgcolor="#fff"> <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"> <tr> <td style="padding: 20px 40px; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #074da4; font-size:16px; background: #03192d;"> [DISCLAIMER] </td></tr></table> </td></tr></table> </center><style>@media screen and (max-width: 600px){.email-container{width: 100% !important; margin: auto !important;}@media screen and (max-width: 599px){.stack-column, .stack-column-center{display: block !important; width: 100% !important; max-width: 100% !important; direction: ltr !important;}}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */ .fluid{max-width: 100% !important; height: auto !important; margin-left: auto !important; margin-right: auto !important;}/* What it does: Forces table cells into full-width rows. */ /* And center justify these ones. */ .stack-column-center{text-align: center !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */ .center-on-narrow{text-align: center !important; display: block !important; margin-left: auto !important; margin-right: auto !important; float: none !important;}table.center-on-narrow{display: inline-block !important;}}.feature_image > img{width: 100%!important;}</style>';

				$template13_css = 'html, body{margin: 0 auto !important; padding: 0 !important; height: 100% !important; width: 100% !important;}/* What it does: Stops email clients resizing small text. */ *{-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */ div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */ table, td{mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */ table{border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important; margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */ img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */ .mobile-link--footer a, a[x-apple-data-detectors]{color:inherit !important; text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */ .button-link{text-decoration: none !important;}.button-td, .button-a{transition: all 100ms ease-in;}.button-td:hover, .button-a:hover{background: #555555 !important; border-color: #555555 !important;}span.feature_img img{width: 100%;}';

				$gifttemplate = array(
					'post_title' => __( 'Halloween', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $template_html13,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $template13_css ) );
				set_post_thumbnail( $parent_post_id, $arr[2] );

				$template_html14 = ' <center style="width: 100%;"> <div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;"> (Optional) This text will appear in the inbox preview, but not the email body. </div><table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container"><tr><td style="padding-top: 10px; text-align: left; background-color: #074da4;color:#ffffff;font-weight:bold;padding-left:20px;font-size:20px;font-family:sans-serif;">[LOGO]</td></tr></table> <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container" align="center"> <td bgcolor="#074da4"> <span class="feature_img"> [FEATUREDIMAGE]</span> </td></tr><tr> <td bgcolor="#fff" style="text-align: center; font-family: sans-serif; font-size: 15px; color: rgb(25, 118, 231); vertical-align: middle; display: table-cell; background: rgb(25, 118, 231) none repeat scroll 0% 0%; padding: 20px 0px;"> <span style="display: block; border-top: 3px dashed #fff; padding: 5px;"></span> <h2 style="vertical-align: middle; text-align:center!important; font-size: 16px; display: block; color: rgb(25, 118, 231); background: rgb(255, 255, 255) none repeat scroll 0% 0%; padding:5px 0px; margin: 5px 0;">COUPON CODE <span style="display:block; font-size:24px; padding:8px 0 0 0;">[COUPON]</span> <span style="display:block;font-size:16px; padding:8px 0 0 0;">(Ed:[EXPIRYDATE])</span></h2> <span style="display: block; border-bottom: 3px dashed #fff; padding: 5px;"></span> </td></tr><tr > <td bgcolor="#1f81fa" dir="ltr" align="center" valign="top" width="100%" > <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr > <td width="50%" valign="top" class="stack-column-center" style="padding: 20px 0px;"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr > <td dir="ltr" valign="top" style="padding: 0 30px 0 30px;"> [DEFAULTEVENT] </td></tr></table> </td><td width="50%" valign="top" class="stack-column-center" style="padding: 20px 0px;text-align:left;"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td dir="ltr" valign="top" style="font-family: sans-serif;font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff; min-height:170px;height:auto; word-wrap:break-word; " class="center-on-narrow"> <p style="word-spacing: 1px;text-align:left;font-size:16px; min-height: 150px;"> [MESSAGE] </p></td></tr><tr> <td dir="ltr" valign="top" style="font-family: sans-serif;font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff;word-wrap: break-word; " class="center-on-narrow"> <p style="margin-bottom:0px;font-size:16px;text-align: left;"> <span style="display: inline-block; text-align:right;font-size:15px; vertical-align:top;">From-</span> <span style="display: inline-block;width: 180px;text-align:left;font-size:14px; vertical-align:top;"> [FROM]</span> </p></td></tr><tr> <td dir="ltr" valign="top" style="font-family: sans-serif;font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff;word-wrap: break-word; " class="center-on-narrow"> <p style="margin-top:0px;font-size:16px;line-height:25px;text-align: left;"> <span style="display: inline-block; text-align: right;font-size:15px; vertical-align:top;">To-</span> <span style="display: inline-block;width: 180px;text-align:left;font-size:14px; vertical-align:top;"> [TO]</span> </p></td></tr><td dir="ltr" valign="top" style="font-family: sans-serif;font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff;padding:5px 10px;word-wrap: break-word;" class="center-on-narrow"> <p style="text-align:left;font-weight:bold;font-size:28px; vertical-align:top;"> <span style="color:#fff; margin:20px 0;"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol"></span>[AMOUNT]</span>/-</span> </p></td></tr></table> </td></tr></table> </td></tr><tr > <td bgcolor="#074da4"> <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"> <tr> <td style="padding: 15px 40px; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff; font-size:16px; text-align: center;"> [DISCLAIMER] </td></tr></table> </td></tr></table> </center><style>/* Media Queries */ @media screen and (max-width: 600px){.email-container{width: 100% !important; margin: auto !important;}@media screen and (max-width: 599px){.stack-column, .stack-column-center{display: block !important; width: 100% !important; max-width: 100% !important; direction: ltr !important;}}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */ .fluid{max-width: 100% !important; height: auto !important; margin-left: auto !important; margin-right: auto !important;}/* What it does: Forces table cells into full-width rows. */ /* And center justify these ones. */ .stack-column-center{text-align: center !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */ .center-on-narrow{text-align: center !important; display: block !important; margin-left: auto !important; margin-right: auto !important; float: none !important;}table.center-on-narrow{display: inline-block !important;}}.feature_img > img{margin: 0 auto; display: block; max-width: 180px; width: 100%;}</style>';

				$template14_css = ' html, body{margin: 0 auto !important; padding: 0 !important; height: 100% !important; width: 100% !important;}/* What it does: Stops email clients resizing small text. */ *{-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */ div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */ table, td{mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */ table{border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important; margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */ img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */ .mobile-link--footer a, a[x-apple-data-detectors]{color:inherit !important; text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */ .button-link{text-decoration: none !important;}.button-td, .button-a{transition: all 100ms ease-in;}.button-td:hover, .button-a:hover{background: #555555 !important; border-color: #555555 !important;} ';

				$gifttemplate = array(
					'post_title' => __( 'Easter', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $template_html14,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $template14_css ) );
				set_post_thumbnail( $parent_post_id, $arr[3] );

				$template_html15 = '<center style="width: 100%;"> <div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;"> (Optional) This text will appear in the inbox preview, but not the email body. </div><table class="email-container" role="presentation" style="margin: auto;" width="600" cellspacing="0" cellpadding="0" border="0" align="center"> <tbody> <tr> <td style="text-align: center; background-color: #000000;"> <p style="height: auto; background: #000000; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555; font-size: 27.79px; text-transform: uppercase; color: #ffffff; font-weight: bold;" width="200" border="0" height="50">[LOGO]</p></td></tr></tbody> </table> <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container"> <tr> <td bgcolor="#000000"> <span class="feature_img">[FEATUREDIMAGE] </span> </td></tr><tr style="margin-top: 20px;"> <td bgcolor="#c31a1a" style="text-align: center; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555;"> <h1 style="color: #ffffff; padding: 13px 33px; margin: 10px; border: 2px dashed #ffffff; text-transform: uppercase; font-size: 15px; font-weight: 200; line-height: 27px; text-align:center!important;">Coupon<span style="display:block; font-size:25px; padding:5px 0;">[COUPON]</span><span style="display:block;">(Ed:[EXPIRYDATE])</span></h1> </td></tr><tr> <td background="http://placehold.it/600x230/222222/666666" bgcolor="#222222" valign="middle" style="text-align: center; background-position: center center !important; background-size: cover !important;"><!--[if gte mso 9]> <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:600px;height:175px; background-position: center center !important;"> <v:fill type="tile" src="http://placehold.it/600x230/222222/666666" color="#222222"/> <v:textbox inset="0,0,0,0"><![endif]--><!--[if gte mso 9]> </v:textbox> </v:rect><![endif]--> </td></tr><tr> <td bgcolor="#000000" dir="ltr" align="center" valign="top" width="100%" style="padding: 10px; text-align: center;"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td width="50%" class="stack-column-center" style="vertical-align:top;"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%" > <tr> <td dir="ltr" valign="top" style="padding: 0 14px; height: 202px; "> [DEFAULTEVENT] </td></tr></table> </td><td width="50%" class="stack-column-center" style="vertical-align:top;"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td dir="ltr" valign="top" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #ffffff; padding: 0px 10px; text-align: left;margin: 0 auto;word-wrap: break-word;" class="center-on-narrow"> <p style="font-size: 14.97px; letter-spacing: 0px; text-align:left; height:auto; min-height: 159px; margin: 0px;">[MESSAGE]</p><p style="font-size: 14.97px; letter-spacing: 0px; text-align:justify;margin: 0px;"><span style="display: inline-block; text-align: right; vertical-align:top;">From:</span><span style="display: inline-block; text-align: left; width: 180px; vertical-align:top;">[FROM]</span></p><p style="text-align: left;"> To:</span><span">[TO]</span></p><strong style="color:#c31a1a; font-size: 23.96px; display: block; vertical-align:top; text-align: left;">[AMOUNT]/-</strong> </td></tr></table> </td></tr></table> </td></tr><tr> <td style="font-size: 0; line-height: 0;"> &nbsp; </td></tr><tr> <td bgcolor="#c31a1a"> <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"> <tr> <td style=" padding:23px 28px; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #ffffff; text-align: center;"> [DISCLAIMER] </td></tr></table> </td></tr></table> </center><style>@media screen and (max-width: 600px){.email-container{width: 100% !important; margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */ .fluid{max-width: 100% !important; height: auto !important; margin-left: auto !important; margin-right: auto !important;}/* What it does: Forces table cells into full-width rows. */ /* And center justify these ones. */ .stack-column-center{text-align: center !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */ .center-on-narrow{text-align: center !important; display: block !important; margin-left: auto !important; margin-right: auto !important; float: none !important;}table.center-on-narrow{display: inline-block !important;}}@media screen and (max-width:460px){.stack-column, .stack-column-center{display: block !important; width: 100% !important; max-width: 100% !important; direction: ltr !important;}}</style>';

				$template15_css = ' html, body{margin: 0 auto !important; padding: 0 !important; height: 100% !important; width: 100% !important;}/* What it does: Stops email clients resizing small text. */ *{-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */ div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */ table, td{mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */ table{border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important; margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */ img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */ .mobile-link--footer a, a[x-apple-data-detectors]{color:inherit !important; text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */ .button-link{text-decoration: none !important;}.button-td, .button-a{transition: all 100ms ease-in;}.button-td:hover, .button-a:hover{background: #555555 !important; border-color: #555555 !important;}.feature_img img{display: block; margin: 0 auto; max-width: 270px; width: 100%; padding-bottom: 10px;} ';

				$gifttemplate = array(
					'post_title' => __( 'Fathers Day', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $template_html15,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $template15_css ) );
				set_post_thumbnail( $parent_post_id, $arr[4] );

				$template_html16 = '<center style="width: 100%;"> <div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;"> (Optional) This text will appear in the inbox preview, but not the email body. </div><table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container"> <tr> <td style="padding: 0px 0px 20px; text-align: center; background: #fff0cd "> <p style="color:#80150b; font-size: 25px; font-family: sans-serif; margin: 0px;"><strong>[LOGO] </strong></p></td></tr></table> <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container"> <tr> <td bgcolor="#fff0cd"> <span class="feature_img"> [FEATUREDIMAGE] </span> </td></tr><tr> <td bgcolor="#fff0cd" style="padding: 50px 0px 30px; text-align: center; font-family: sans-serif; font-size: 14.97px; mso-height-rule: exactly; line-height: 9px; color: #555555;"> <p style="background: #ffe7b1; border: 8px solid #ffd064; color: #80150b; font-size:15px; padding: 10px 0; text-transform: uppercase; margin: 0; line-height:29px; text-align:center!important;">Coupon Code<span style="display:block; font-size:25px;">[COUPON]</span> <span style="display:block; font-size:15px;">(Ed:[EXPIRYDATE])</span></p><br><br></td></tr><tr> <td bgcolor="#fff0cd" dir="ltr" align="center" valign="top" width="100%" style="padding-bottom: 44px;"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td width="50%" class="stack-column-center" style="vertical-align:top;"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td dir="ltr" valign="top" style="padding: 0px 25px;"> [DEFAULTEVENT] </td></tr></table> </td><td width="50%" class="stack-column-center" style="vertical-align: top;"> <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"> <tr> <td class="gift-baskets-content" style="font-size: 15px; line-height: 20px;word-wrap:break-word;"> <p style="color:#000;word-spacing: 5px;;height: auto; min-height: 200px; ">[MESSAGE]</p></td></tr><tr> <td class="mail-content" style="word-wrap:break-word";> <span style="color: #000; font-size: 15px; float: left; vertical-align:top;  text-align: right;display-inline:block; ">From- </span> <span style="color:#000;font-size:14px; vertical-align:top; display:inline-block; width:180px; float:left;">[FROM]</span> </td></tr><tr> <td style="word-wrap:break-word";> <p><span style="color: #000; font-size: 15px;">To- </span> <span style="color:#000;font-size:14px; vertical-align:top;">[TO]</span></p> </td></tr><tr><td style="word-wrap:break-word;"> <span style="color:#8a2814;font-size: 23.96px; vertical-align:top; "><strong>[AMOUNT]/-</strong> </span></td></tr></table> </td></td></tr></table> </td></tr><tr> <td bgcolor="#ffd064"> <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"> <tr> <td style="padding: 15px 30px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: rgb(0, 0, 0); text-align: center;"> [DISCLAIMER] </td></tr></table> </td></tr><style>@media screen and (max-width: 600px){.email-container{width: 100% !important; margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */ .fluid{max-width: 90% !important; height: auto !important; margin-left: auto !important; margin-right: auto !important;}/* What it does: Forces table cells into full-width rows. */ /* And center justify these ones. */ .stack-column-center{text-align: center !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */ .center-on-narrow{text-align: center !important; display: block !important; margin-left: auto !important; margin-right: auto !important; float: none !important;}table.center-on-narrow{display: inline-block !important;}}@media screen and (max-width: 476px){.stack-column, .stack-column-center{display: block !important; width: 100% !important; max-width: 100% !important; direction: ltr !important;}}</style>';

				$template16_css = ' html, body{margin: 0 auto !important; padding: 0 !important; height: 100% !important; width: 100% !important;}/* What it does: Stops email clients resizing small text. */ *{-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */ div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */ table, td{mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */ table{border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important; margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */ img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */ .mobile-link--footer a, a[x-apple-data-detectors]{color:inherit !important; text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */ .button-link{text-decoration: none !important;}.button-td, .button-a{transition: all 100ms ease-in;}.button-td:hover, .button-a:hover{background: #555555 !important; border-color: #555555 !important;}.feature_img img{display: block; margin: 0 auto; max-width: 400px; width: 100%;} ';

				$gifttemplate = array(
					'post_title' => __( 'Mothers Day', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $template_html16,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $template15_css ) );
				set_post_thumbnail( $parent_post_id, $arr[5] );

				$template_html17 = '<center style="width: 100%;"> <div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;"> (Optional) This text will appear in the inbox preview, but not the email body. </div><table role="presentation" style="margin: auto;" class="email-container" width="600" cellspacing="0" cellpadding="0" border="0" align="center"> <tbody> <tr style="background-color:#C9FFFB;"> <td style=" text-align:left; font-family: sans-serif;color: #AD1D20;"> <h2 style="margin:0px; padding-left:25px; padding-top:0;">[LOGO]</h2> </td></tr></tbody> </table> <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container"> <tr> <td bgcolor="#c9fffb" style="padding: 20px 0;"> <span style="display:block; margin:0 auto;" class="feature_image"> [FEATUREDIMAGE] </span> </td></tr><tr> <td background="http://placehold.it/600x230/222222/666666" bgcolor="#222222" valign="middle" style="text-align: center; background-position: center center !important; background-size: cover !important;"><!--[if gte mso 9]> <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:600px;height:175px; background-position: center center !important;"> <v:fill type="tile" src="http://placehold.it/600x230/222222/666666" color="#222222"/> <v:textbox inset="0,0,0,0"><![endif]--> <div> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td valign="middle" style="text-align: center; font-family: sans-serif; font-size: 22px; mso-height-rule: exactly; line-height: 20px; color: #ffffff; background-color: #ad1d20; letter-spacing: 0px;"> <p style="padding: 25px 20px; text-align:center!important; border: 2px dashed #fff; margin: 10px 10px;"><span style="font-size:22px;display:block">COUPON</span><span style="font-size:30px;display:block;padding:15px 0px;">[COUPON]</span><span style="font-size:22px;display:block">(Ed:[EXPIRYDATE])</span></p></td></tr></table> </div><!--[if gte mso 9]> </v:textbox> </v:rect><![endif]--> </td></tr><tr> <td bgcolor="#C9FFFB" dir="ltr" align="center" valign="top" width="100%" style="padding:31px 10px;"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td width="50%" class="stack-column-center" style="vertical-align:top;"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td dir="ltr" valign="top" style="padding: 0 10px; text-align: center;/*background: #fff;*/ border-radius: 5px;"> [DEFAULTEVENT] </td></tr></table> </td><td width="50%" class="stack-column-center" style="vertical-align: top"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td dir="ltr" valign="top" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 22px; color: #555555; padding:0 10px; text-align: left;word-wrap: break-word;" class="center-on-narrow"> <p style="font-size: 15px; min-height:172px; height:auto; margin:0;"> [MESSAGE]</p><p style="margin:0;"><span style="font-size: 15px; display:inline-block; text-align:right;">From: </span> <span style="display:inline-block; vertical-align:top;">[FROM]</span></p><p style="margin-top:0;"><span style="font-size: 15px;vertical-align:top; display:inline-block; text-align:right;">To: </span> <span style="display:inline-block; vertical-align:top;">[TO]</span><p> <table role="presentation" cellspacing="0" cellpadding="0" border="0" class="center-on-narrow" style="float:left;"> <tr> <td style="word-wrap:break-word";><p style="font-size: 26px; color: #ad1d20; margin:0px 0px 0px 0px;text-align: left;float: right;"><strong>[AMOUNT]/-</strong></p></td></tr></table> </td></tr></table> </td></tr></table> </td></tr><tr> <td bgcolor="#ffffff"> <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"> <tr> <td style="padding: 40px 30px; font-family: sans-serif; font-size: 16px; mso-height-rule: exactly; line-height: 20px; color: #fff; background-color:#ad1d20;"> <p style="font-size: 15px; letter-spacing: 0px; word-spacing: 1px;">[DISCLAIMER]</p></td></tr></table> </td></tr></table> </center><style>/* Media Queries */ @media screen and (max-width: 600px){.email-container{width: 100% !important; margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */ .fluid{max-width: 100% !important; height: auto !important; margin-left: auto !important; margin-right: auto !important;}/* What it does: Forces table cells into full-width rows. */ /* And center justify these ones. */ .stack-column-center{text-align: center !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */ .center-on-narrow{text-align: left !important; display: block !important; margin-left: auto !important; margin-right: auto !important; float: none !important;}table.center-on-narrow{display: inline-block !important;}}@media screen and (max-width: 599px){.stack-column, .stack-column-center{display:block !important; width: 100% !important; max-width: 100% !important; direction: ltr !important;}}</style>';

				$template17_css = ' html, body{margin: 0 auto !important; padding: 0 !important; height: 100% !important; width: 100% !important;}/* What it does: Stops email clients resizing small text. */ *{-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */ div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */ table, td{mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */ table{border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important; margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */ img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */ .mobile-link--footer a, a[x-apple-data-detectors]{color:inherit !important; text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */ .button-link{text-decoration: none !important;}.button-td, .button-a{transition: all 100ms ease-in;}.button-td:hover, .button-a:hover{background: #555555 !important; border-color: #555555 !important;}.feature_image img{display: block; margin: 0 auto; max-width: 550px; width: 100%;} ';

				$gifttemplate = array(
					'post_title' => __( 'Christmas', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $template_html17,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $template17_css ) );
				set_post_thumbnail( $parent_post_id, $arr[6] );

				$template_html18 = '<center style="width: 100%;"> <div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;"> (Optional) This text will appear in the inbox preview, but not the email body. </div><table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container"> <tr> <td style="padding: 20px 0 0 40px; text-align:left;color:#fff;background-color: #000;"> [LOGO] </td></tr></table> <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin:auto;padding:0;" class="email-container"> <tr> <td bgcolor="#000"> <span class="feature_img"> [FEATUREDIMAGE] </span> </td></tr><tr> <td bgcolor="#000" style="padding:0 10px 30px;text-align: center; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; color: #555555;"> <p style="font-size: 16px;border: 2px dashed red; text-align:center; line-height:40px; padding: 5% 20% ;margin: 0 auto;display:block;color:#fff;text-transform: uppercase;">coupon <span style="display:block;font-size: 28.75px;">[COUPON]</span> <span style="display:block;font-size:16px;">(Ed:[EXPIRYDATE])</span></p></td></tr><tr><td dir="ltr" style="padding-top: 10px; padding-bottom: 10px;" align="center" valign="top" bgcolor="#000" width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="stack-column-center" style="vertical-align: top;" width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td dir="ltr" style="padding: 0px 25px;" valign="top">[DEFAULTEVENT]</td></tr></tbody></table></td><td class="stack-column-center" style="vertical-align: top;" width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #ffffff; padding: 0px 15px; text-align: left;" valign="top"><p style="font-size: 15px; line-height: 24px; text-align: justify; color: #fff; min-height: 200px; white-space: pre-line;">[MESSAGE]</p></td></tr><tr><td class="mail-content" style="word-wrap: break-word; font-family: sans-serif; padding: 0px 15px;"><span style="color: #fff; font-size: 15px; float: left; vertical-align: top; text-align: right; display-inline: block; ">From- </span> <span style="color: #fff; font-size: 14px; vertical-align: top; display: inline-block; width: 180px; float: left;">[FROM]</span></td></tr><tr><td style="word-wrap: break-word; font-family: sans-serif; padding: 0px 15px;"><span style="color: #fff; font-size: 15px; max-width: 15%; float: left; margin-right: 2%; text-align: right; width: 100%; display: inline-block; vertical-align: top;">To- </span> <span style="color: #fff; font-size: 14px; width: 180px; float: left; vertical-align: top;">[TO]</span></td></tr><tr><td style="padding: 5px 10px; word-wrap: break-word;"><span style="color: #8a2814; font-size: 23.96px; vertical-align: top;"><strong>[AMOUNT]/-</strong> </span></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr> <td bgcolor="#ed1c24" > <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"> <tr> <td style="padding: 30px 35px; font-family: sans-serif; text-align:center; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff;"> [DISCLAIMER] </td></tr></table> </td></tr></table></center><style type="text/css">/* Media Queries */ @media screen and (max-width: 600px){.email-container{width: 100% !important; margin: auto !important;}@media screen and (max-width: 599px){.stack-column, .stack-column-center{display: block !important; width: 100% !important; max-width: 85% !important; direction: ltr !important;}}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */ .fluid{max-width: 100% !important; height: auto !important; margin-left: auto !important; margin-right: auto !important;}/* What it does: Forces table cells into full-width rows. */ /* And center justify these ones. */ .stack-column-center{text-align: center !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */ .center-on-narrow{text-align: center !important; display: block !important; margin-left: auto !important; margin-right: auto !important; float: none !important;}table.center-on-narrow{display: inline-block !important;}}@media screen and (max-width: 320px){.stack-column-center{display:block; width:100%; margin:0 auto;}}.feature_img img{display: block; margin: 0 auto; max-width: 100%; width:100%;}</style>';

				$template18_css = 'html, body{margin: 0 auto !important; padding: 0 !important; height: 100% !important; width: 100% !important;}/* What it does: Stops email clients resizing small text. */ *{-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */ div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */ table, td{mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */ table{border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important; margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */ img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */ .mobile-link--footer a, a[x-apple-data-detectors]{color:inherit !important; text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */ .button-link{text-decoration: none !important;}.button-td, .button-a{transition: all 100ms ease-in;}.button-td:hover, .button-a:hover{background: #555555 !important; border-color: #555555 !important;}span.feature_img img{width: 70%;} ';

				$gifttemplate = array(
					'post_title' => __( 'Black Friday', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $template_html18,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $template18_css ) );
				set_post_thumbnail( $parent_post_id, $arr[7] );

				$template_html19 = '<center style="width: 100%;"><div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">(Optional) This text will appear in the inbox preview, but not the email body.</div><table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container"><tr><td style="padding: 0px 0px 20px; text-align: center; background: #b2dbff "><p style="color:#80150b; font-size: 25px; font-family: sans-serif; margin: 0px;"><strong>[LOGO] </strong></p></td></tr></table>&nbsp;<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin-top:-21px !important;" class="email-container"><tr><td bgcolor="#b2dbff"><span class="feature_img"> [FEATUREDIMAGE] </span></td></tr><tr><td style="padding: 10px; text-align: center; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555;" bgcolor="#1d568c"><p style="background: #1d568c; border: 4px dashed #fff; color: #fff; font-size:15px; letter-spacing: 0px; padding: 30px 0; text-transform: uppercase; margin: 0; line-height:29px;">coupon code<span style="display:block; font-size:25px;">[COUPON]</span> <span style="display:block; font-size:15px;">(Ed:[EXPIRYDATE]</span></p></td></tr><tr><td align="center" valign="top" bgcolor="#b2dbff" style="padding:30px 0"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" align="center" border="0"><tbody><tr><td class="stack-column-center" style="width: 50%; vertical-align: top;"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" align="center" border="0"><tbody><tr><td style="padding: 0 15px; text-align: center;vertical-align:top;">[DEFAULTEVENT]</td></tr></tbody></table></td><td class="stack-column-center" style="vertical-align: top; width: 50%; "><table role="presentation" width="100%" cellspacing="0" cellpadding="0" align="center" border="0"><tbody><tr><td class="gift-baskets-content" style="padding: 0 15px;word-wrap:break-word;"><p style="color: #000; word-spacing: 5px; font-size: 16px;height:auto;min-height:180px;padding:0 0 20px 0;">[MESSAGE]</p></td></tr><tr><td style="word-wrap:break-word; padding: 0 15px;"><p style="font-size: 16px; color: #000; margin: 0;"><span style="display: inline-block; vertical-align:top; text-align: right;">From-</span><span style="font-size: 15px; display: inline-block; text-align: left;vertical-align:top;">[FROM]</span></p></td></tr><tr><td style="word-wrap:break-word; padding: 0 15px;"><p style="font-size: 16px; color: #000; margin: 0;"><span style="display: inline-block; text-align: right;">To-</span><span style="font-size: 15px; display: inline-block; text-align: left;vertical-align:top;">[TO]</span></p></td></tr><tr><td style="word-wrap:break-word";><p style="font-size: 31px; color: #1e578d;margin:0px 0px 0px 16px;"><strong>[AMOUNT]/-</strong></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td bgcolor="#1d568c"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 30px 35px; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff; text-align: center;">[DISCLAIMER]</td></tr></tbody></table></td></tr></tbody></table>&nbsp;</center><style>@media screen and (max-width: 599px){.email-container{width: 100% !important;margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */.fluid{max-width: 100% !important;height: auto !important;margin-left: auto !important;margin-right: auto !important;}/* What it does: Forces table cells into full-width rows. */.stack-column,.stack-column-center{display: block !important;width: 100% !important;max-width: 100% !important;direction: ltr !important;}/* And center justify these ones. */.stack-column-center{text-align: center !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */.center-on-narrow{text-align: center !important;display: block !important;margin-left: auto !important;margin-right: auto !important;float: none !important;}table.center-on-narrow{display: inline-block !important;}}.feature_img > img{margin: 0 auto;display: block;width: 100%;}</style>';

				$template19_css = '  html, body{margin: 0 auto !important; padding: 0 !important; height: 100% !important; width: 100% !important;}/* What it does: Stops email clients resizing small text. */ *{-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */ div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */ table, td{mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */ table{border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important; margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */ img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */ .mobile-link--footer a, a[x-apple-data-detectors]{color:inherit !important; text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */ .button-link{text-decoration: none !important;}.button-td, .button-a{transition: all 100ms ease-in;}.button-td:hover, .button-a:hover{background: #555555 !important; border-color: #555555 !important;}.feature_img{max-width: 600px;} ';

				$gifttemplate = array(
					'post_title' => __( 'Independence Day', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $template_html19,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $template19_css ) );
				set_post_thumbnail( $parent_post_id, $arr[8] );

				$newyear_temp_html = '<center style="width: 100%;"><div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">(Optional) This text will appear in the inbox preview, but not the email body.</div><table class="email-container" style="margin: auto;" border="0" width="600" cellspacing="0" cellpadding="0"><tbody><tr><td style="background: #311438; padding-left: 15px;"><p style="color: #ffffff; font-size: 25px; font-family: sans-serif; padding: 30px 0px 0px; margin: 0px; text-align: center;"><strong>[LOGO]</strong></p></td></tr></tbody></table><table class="email-container" style="margin: auto;" border="0" width="600" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding-bottom: 30px; text-align: center;" bgcolor="#311438"><span class="feature_img">[FEATUREDIMAGE]</span></td></tr><tr style="background-color: #afcb0c;"><td style="color: #fff; font-size: 20px; letter-spacing: 1px; margin: 0; text-transform: uppercase; background-color: #afcb0c; padding: 20px 10px; line-height: 15px;"><p style="border: 2px dashed #ffffff; color: #fff; font-size: 20px; letter-spacing: 0px; padding: 30px 10px; line-height: 30px; margin: 0; text-transform: uppercase; background-color: #afcb0c; text-align: center;">Coupon Code<span style="display: block; font-size: 25px;">[COUPON]</span><span style="display: block;">Ed:[EXPIRYDATE]</span></p></td></tr><tr><td dir="ltr" style="padding: 15px 0;" align="center" valign="top" bgcolor="#feffda" width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="stack-column-center" style="vertical-align: top;" width="50%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td dir="ltr" style="padding: 0px 25px;" valign="top">[DEFAULTEVENT]</td></tr></tbody></table></td><td class="stack-column-center" style="vertical-align: top;" width="50%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #ffffff; padding: 0px 15px; text-align: left;" valign="top"><p style="font-size: 15px; line-height: 24px; text-align: justify; color: #535151; min-height: 200px; white-space: pre-line;">[MESSAGE]</p></td></tr><tr><td class="mail-content" style="word-wrap: break-word; font-family: sans-serif; padding: 0px 15px;"><span style="color: #535151; font-size: 15px; float: left; vertical-align: top; text-align: right; display-inline: block; ">From- </span> <span style="color: #535151; font-size: 14px; vertical-align: top; display: inline-block; width: 180px; float: left;">[FROM]</span></td></tr><tr><td style="word-wrap: break-word; font-family: sans-serif; padding: 0px 15px;"><span style="color: #535151; font-size: 15px; max-width: 15%; float: left; margin-right: 2%; text-align: right; width: 100%; display: inline-block; vertical-align: top;">To- </span> <span style="color: #535151; font-size: 14px; width: 180px; float: left; vertical-align: top;">[TO]</span></td></tr><tr><td style="padding: 5px 10px; word-wrap: break-word;"><span style="color: #8a2814; font-size: 23.96px; vertical-align: top;"><strong>[AMOUNT]/-</strong> </span></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td bgcolor="#6f3e7f"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 10px 30px; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #ffffff;"><p style="font-weight: bold; text-align: center;">[DISCLAIMER]</p></td></tr></tbody></table></td></tr></tbody></table></center><style>@media screen and (max-width: 600px){.email-container{width: 100% !important;margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */.fluid{max-width: 90% !important;height: auto !important;margin-left: auto !important;margin-right: auto !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */.center-on-narrow{text-align: center !important;display: block !important;margin-left: auto !important;margin-right: auto !important;float: none !important;}table.center-on-narrow{display: inline-block !important;}}@media screen and (max-width: 476px){/* What it does: Forces table cells into full-width rows. */.stack-column,.stack-column-center{display: block !important;width: 100% !important;max-width: 100% !important;direction: ltr !important;}/* And center justify these ones. */.stack-column-center{text-align: center !important;}}</style>';

				$newyear_temp_css = 'html, body{margin: 0 auto !important; padding: 0 !important; height: 100% !important; width: 100% !important;}/* What it does: Stops email clients resizing small text. */ *{-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */ div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */ table, td{mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */ table{border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important; margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */ img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */ .mobile-link--footer a, a[x-apple-data-detectors]{color:inherit !important; text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */ .button-link{text-decoration: none !important;}.button-td, .button-a{transition: all 100ms ease-in;}.button-td:hover, .button-a:hover{background: #555555 !important; border-color: #555555 !important;}.feature_img > img{display: block; margin: 0 auto; max-width: 400px; width: 50%;} ';

				$gifttemplate = array(
					'post_title' => __( 'Happy New Year', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $newyear_temp_html,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $newyear_temp_css ) );
				set_post_thumbnail( $parent_post_id, $arr[9] );

				$birthday_temp_html = '<center style="width: 100%;"> <div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;"> (Optional) This text will appear in the inbox preview, but not the email body. </div><table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto" class="email-container"> <tr> <td style="text-align: center; background:#E25A9D "> <p style="color:#fff; font-size: 25px; font-family: sans-serif; padding: 15px 0 0; margin: 0px;"><strong>[LOGO]</strong></p></td></tr></table> <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container"> <tr> <td bgcolor="#E25A9D" style="padding-bottom: 15px;"> <span class="feature_img">[FEATUREDIMAGE]</span> </td></tr><tr> <td bgcolor="#FFE0EF" style="padding:18px; text-align: center; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; color: #555555; "></td><tr style="background-color: #E25A9D"><td style="color: #fff; font-size:20px; letter-spacing: 0px; margin:0; text-transform: uppercase; background-color: #E25A9D; padding:20px 10px; line-height: 0;"> <p style="border: 2px dashed #ffffff; color: #fff; font-size:20px; padding: 15px 10px; margin:0; text-transform: uppercase; background-color: #E25A9D; text-align: center; line-height: 30px;">Coupon Code<span style="display:block; font-size: 25px;">[COUPON]</span><span style="display:block;">Ed:[EXPIRYDATE]</span></p><br><br></td></tr><td bgcolor="#FFE0EF" style="padding-top: 25px; text-align: center; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; color: #555555; "> </td></tr><tr> <td bgcolor="#ffe0ef" dir="ltr" align="center" valign="top" width="100%" style="padding-bottom: 15px;"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td width="50%" class="stack-column-center" style="vertical-align:top;"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td dir="ltr" valign="top" style="padding: 0px 25px;"> [DEFAULTEVENT] </td></tr></table> </td><td width="50%" class="stack-column-center" style="vertical-align: top;"> <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td dir="ltr" valign="top" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #ffffff; padding: 0px 15px; text-align: left;" class="center-on-narrow"> <p style="font-size: 15px; line-height: 24px; text-align: justify; color: #535151; min-height: 200px;white-space: pre-line;">[MESSAGE] </p></td></tr><tr><td class="mail-content" style="word-wrap: break-word;font-family: sans-serif; padding: 0px 15px;"><span style="color: #535151; font-size: 15px; float: left; vertical-align: top; text-align: right; display-inline: block; ">From- </span> <span style="color: #535151; font-size: 14px; vertical-align: top; display: inline-block; float: left;">[FROM]</span></td></tr><tr><td style="word-wrap: break-word; font-family: sans-serif; padding: 0px 15px;"><span style="color:#535151; font-size: 15px; max-width: 15%; float: left; margin-right: 2%; text-align: right; width: 100%; display: inline-block; vertical-align: top;">To- </span> <span style="color: #535151; font-size: 14px; width: 180px; float: left; vertical-align: top;">[TO]</span></td></tr><tr><td style="padding: 5px 10px; word-wrap: break-word;"><span style="color: #8a2814; font-size: 23.96px; vertical-align: top;"><strong>[AMOUNT]/-</strong> </span></td></tr></table> </td></tr></table> </td></tr><tr> <td bgcolor="#e5609f"> <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"> <tr> <td style="padding: 10px; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #ffffff;"> <p style="font-weight: bold; text-align:center;"> [DISCLAIMER] </p></td></tr></table> </td></tr></table></center><style>@media screen and (max-width: 600px){.email-container{width: 100% !important; margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */ .fluid{max-width: 90% !important; height: auto !important; margin-left: auto !important; margin-right: auto !important;}/* What it does: Forces table cells into full-width rows. */ /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */ .center-on-narrow{text-align: center !important; display: block !important; margin-left: auto !important; margin-right: auto !important; float: none !important;}table.center-on-narrow{display: inline-block !important;}}@media screen and (max-width: 476px){/* What it does: Forces table cells into full-width rows. */ .stack-column, .stack-column-center{display: block !important; width: 100% !important; max-width: 100% !important; direction: ltr !important;}/* And center justify these ones. */ .stack-column-center{text-align: center !important;}}</style>';

				$birthday_temp_css = '  html, body{margin: 0 auto !important; padding: 0 !important; height: 100% !important; width: 100% !important;}/* What it does: Stops email clients resizing small text. */ *{-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */ div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */ table, td{mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */ table{border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important; margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */ img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */ .mobile-link--footer a, a[x-apple-data-detectors]{color:inherit !important; text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */ .button-link{text-decoration: none !important;}.button-td, .button-a{transition: all 100ms ease-in;}.button-td:hover, .button-a:hover{background: #555555 !important; border-color: #555555 !important;}.feature_img > img{display: block; margin: 0 auto; max-width: 400px; width: 100%;} ';

				$gifttemplate = array(
					'post_title' => __( 'Happy Birth Day', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $birthday_temp_html,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $birthday_temp_css ) );
				set_post_thumbnail( $parent_post_id, $arr[10] );

				$anniversary_temp_html = '<center style="width: 100%;"><div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">(Optional) This text will appear in the inbox preview, but not the email body.</div><table class="email-container" style="margin: auto;" border="0" width="600" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="text-align: center; background: #f6f6f6;"><p style="color: #192845; font-size: 25px; font-family: sans-serif; margin: 0px;"><strong>[LOGO]</strong></p></td></tr></tbody></table><table class="email-container" style="margin: auto;" border="0" width="600" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding-bottom: 30px;" bgcolor="#f6f6f6"><span class="feature_img">[FEATUREDIMAGE]</span></td></tr><tr></tr><tr style="background-color: #192845;"><td style="color: #fff; font-size: 20px; margin: 0; text-transform: uppercase; background-color: #192845; padding: 20px 10px; line-height: 0;"><p style="border: 2px dashed #ffffff; color: #fff; font-size: 20px; padding: 10px 10px; line-height: 25px; margin: 0; text-transform: uppercase; background-color: #192845; text-align: center;">Coupon Code<span style="display: block; font-size: 25px;">[COUPON]</span><span style="display: block;">Ed:[EXPIRYDATE]</span></p></td></tr><tr><td dir="ltr" style="padding-top: 10px; padding-bottom: 10px;" align="center" valign="top" bgcolor="#f6f6f6" width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="stack-column-center" style="vertical-align: top;" width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td dir="ltr" style="padding: 0px 25px;" valign="top">[DEFAULTEVENT]</td></tr></tbody></table></td><td class="stack-column-center" style="vertical-align: top;" width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #ffffff; padding: 0px 15px; text-align: left;" valign="top"><p style="font-size: 15px; line-height: 24px; text-align: justify; color: #535151; min-height: 200px; white-space: pre-line;">[MESSAGE]</p></td></tr><tr><td class="mail-content" style="word-wrap: break-word; font-family: sans-serif; padding: 0px 15px;"><span style="color: #535151; font-size: 15px; float: left; vertical-align: top; text-align: right; display-inline: block; ">From- </span> <span style="color: #535151; font-size: 14px; vertical-align: top; display: inline-block; width: 180px; float: left;">[FROM]</span></td></tr><tr><td style="word-wrap: break-word; font-family: sans-serif; padding: 0px 15px;"><span style="color: #535151; font-size: 15px; max-width: 15%; float: left; margin-right: 2%; text-align: right; width: 100%; display: inline-block; vertical-align: top;">To- </span> <span style="color: #535151; font-size: 14px; width: 180px; float: left; vertical-align: top;">[TO]</span></td></tr><tr><td style="padding: 5px 10px; word-wrap: break-word;"><span style="color: #8a2814; font-size: 23.96px; vertical-align: top;"><strong>[AMOUNT]/-</strong> </span></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td bgcolor="#e95261"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 10px 30px; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #ffffff;"><p style="font-weight: bold; text-align: center;">[DISCLAIMER]</p></td></tr></tbody></table></td></tr></tbody></table></center><style>@media screen and (max-width: 600px){.email-container{width: 100% !important; margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */ .fluid{max-width: 90% !important; height: auto !important; margin-left: auto !important; margin-right: auto !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */ .center-on-narrow{text-align: center !important; display: block !important; margin-left: auto !important; margin-right: auto !important; float: none !important;}table.center-on-narrow{display: inline-block !important;}}@media screen and (max-width: 476px){/* What it does: Forces table cells into full-width rows. */ .stack-column, .stack-column-center{display: block !important; width: 100% !important; max-width: 100% !important; direction: ltr !important;}/* And center justify these ones. */ .stack-column-center{text-align: center !important;}}</style>';

				$anniversary_temp_css = ' html, body{margin: 0 auto !important; padding: 0 !important; height: 100% !important; width: 100% !important;}/* What it does: Stops email clients resizing small text. */ *{-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */ div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */ table, td{mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */ table{border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important; margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */ img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */ .mobile-link--footer a, a[x-apple-data-detectors]{color:inherit !important; text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */ .button-link{text-decoration: none !important;}.button-td, .button-a{transition: all 100ms ease-in;}.button-td:hover, .button-a:hover{background: #555555 !important; border-color: #555555 !important;}.feature_img > img{display: block; margin: 0 auto; max-width: 400px; width: 50%;}table.email-container{border: solid 1px #ccc !important;} ';

				$gifttemplate = array(
					'post_title' => __( 'Happy Anniversary', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $anniversary_temp_html,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $anniversary_temp_css ) );
				set_post_thumbnail( $parent_post_id, $arr[11] );

				$eid_temp_html = '<center style="width: 100%;"><div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">(Optional) This text will appear in the inbox preview, but not the email body.</div><table class="email-container" style="margin: auto;" border="0" width="600" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="text-align: center; background: #0e0149;"><p style="color: #0e0149; font-size: 25px; font-family: sans-serif; margin: 0px; text-align: left;"><strong>[LOGO]</strong></p></td></tr></tbody></table><table class="email-container" style="margin: auto;" border="0" width="600" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding-bottom: 0px;" bgcolor="#f6f6f6"><span class="feature_img">[FEATUREDIMAGE]</span></td></tr><tr><td style="padding: 19px 30px; text-align: center; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; color: #555555;" bgcolor="#d6ccfd"></td></tr><tr style="background-color: #0e0149;"><td style="color: #fff; font-size: 20px; letter-spacing: 0px; margin: 0; text-transform: uppercase; background-color: #0e0149; padding: 20px 10px; line-height: 0;"><p style="border: 2px dashed #ffffff; color: #fff; font-size: 20px; letter-spacing: 0px; padding: 30px 10px; line-height: 30px; margin: 0; text-transform: uppercase; background-color: #0e0149; text-align: center;">Coupon Code<span style="display: block; font-size: 25px;">[COUPON]</span><span style="display: block;">Ed:[EXPIRYDATE]</span></p></td></tr><td bgcolor="#d6ccfd" style="padding-top: 35px; text-align: center; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; color: #555555; "></td><tr><td dir="ltr" style="padding-bottom: 34px;" align="center" valign="top" bgcolor="#d7ceff" width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="stack-column-center" style="vertical-align: top;" width="50%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td dir="ltr" style="padding: 0px 25px;" valign="top">[DEFAULTEVENT]</td></tr></tbody></table></td><td class="stack-column-center" style="vertical-align: top;" width="50%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #ffffff; padding: 0px 15px; text-align: left;" valign="top"><p style="font-size: 15px; line-height: 24px; text-align: justify; color: #535151; min-height: 150px; white-space: pre-line;">[MESSAGE]</p></td></tr><tr><td class="mail-content" style="word-wrap: break-word; font-family: sans-serif; padding: 0px 15px;"><span style="color: #535151; font-size: 15px; float: left; vertical-align: top; margin-right: 2% text-align: right; display-inline: block;">From- </span> <span style="color: #535151; font-size: 14px; vertical-align: top; display: inline-block; width: 180px; float: left;">[FROM]</span></td></tr><tr><td style="word-wrap: break-word; font-family: sans-serif; padding: 0px 15px;"><span style="color: #535151; font-size: 15px; max-width: 15%; float: left; margin-right: 2%; text-align: right; width: 100%; display: inline-block; vertical-align: top;">To- </span> <span style="color: #535151; font-size: 14px; width: 180px; float: left; vertical-align: top;">[TO]</span></td></tr><tr><td style="padding: 5px 10px; word-wrap: break-word;"><span style="color: #0e0149; font-size: 23.96px; vertical-align: top;"><strong>[AMOUNT]/-</strong> </span></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td bgcolor="#0e0149"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 20px 30px; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #ffffff;"><p style="font-weight: bold; text-align: center;">[DISCLAIMER]</p></td></tr></tbody></table></td></tr></tbody></table></center><style>@media screen and (max-width: 600px){.email-container{width: 100% !important; margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */ .fluid{max-width: 90% !important; height: auto !important; margin-left: auto !important; margin-right: auto !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */ .center-on-narrow{text-align: center !important; display: block !important; margin-left: auto !important; margin-right: auto !important; float: none !important;}table.center-on-narrow{display: inline-block !important;}}@media screen and (max-width: 476px){/* What it does: Forces table cells into full-width rows. */ .stack-column, .stack-column-center{display: block !important; width: 100% !important; max-width: 100% !important; direction: ltr !important;}/* And center justify these ones. */ .stack-column-center{text-align: center !important;}}</style>';

				$eid_temp_css = '  html, body{margin: 0 auto !important; padding: 0 !important; height: 100% !important; width: 100% !important;}/* What it does: Stops email clients resizing small text. */ *{-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */ div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */ table, td{mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */ table{border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important; margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */ img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */ .mobile-link--footer a, a[x-apple-data-detectors]{color:inherit !important; text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */ .button-link{text-decoration: none !important;}.button-td, .button-a{transition: all 100ms ease-in;}.button-td:hover, .button-a:hover{background: #555555 !important; border-color: #555555 !important;}span.feature_img img{width: 100%;} ';

				$gifttemplate = array(
					'post_title' => __( 'Happy Eid', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $eid_temp_html,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $eid_temp_css ) );
				set_post_thumbnail( $parent_post_id, $arr[12] );

				$template_html4 = '<table class="email-container" style="margin: 0 auto;background-color: #ff9898;border-spacing: 20px" border="0" width="600px;" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding:10px"><table class="email-container" style="border:2px dashed #ffffff!important;margin: 0 auto;width:100%;padding:10px 0px" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 10px 0;text-align: left"><a style="text-decoration: none;color: #ffffff;margin-left: 10px">[LOGO]</a></td></tr><tr><td style="padding: 20px 0;text-align: center"><span style="font-size: 22px;color: #ffffff;border-top: 1px solid #ffffff;border-bottom: 1px solid #ffffff;padding: 10px;font-family: Arial, Helvetica, sans-serif">Valentine’s Day </span></td></tr><tr><td class="img-block" style="text-align: center;padding: 10px 0px">[FEATUREDIMAGE]</td></tr></tbody></table></td></tr></tbody></table>&nbsp;<table class="email-container" style="margin: auto" border="0" width="600px;" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="text-align: center;font-family: sans-serif;font-size: 15px;line-height: 20px;color: #555555" bgcolor="#ffffff"></td></tr><tr><td style="text-align: center;background-color: #ffd5d5;padding:10px" valign="middle"><div><table style="border-spacing: 20px;border: 2px dashed #ffffff!important" border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding:10px 0px;font-family: sans-serif;line-height: 20px;color: #ff9898;text-align: center;border: 3px" valign="middle"><h2 style="padding: 0px;margin:10px 0px;font-family: Arial, Helvetica, sans-serif;font-size: 15px;text-align: center">COUPON CODE</h2><p style="font-size: 25px;font-weight:bold;margin:0px;text-align: center">[COUPON]</p><span style="font-size: 15px;text-align: center;padding:10px 0px">(Ed. [EXPIRYDATE])</span></td></tr></tbody></table></div></td></tr><tr><td style="padding: 15px" align="center" valign="top" bgcolor="#ffffff"><table border="0" width="100%;" cellspacing="0" cellpadding="0"><tbody><tr><td class="stack-column-center" style="vertical-align: top;width: 50%"><table border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 10px;text-align: center;width: 50%">[DEFAULTEVENT]</td></tr></tbody></table></td><td class="stack-column-center" style="vertical-align: top"><table border="0" cellspacing="0" cellpadding="0" width="100%"><tbody><tr><td class="center-on-narrow" style="font-family: sans-serif;font-size: 15px;line-height: 20px;color: #555555;padding: 10px;text-align: left"><p style="min-height: 180px">[MESSAGE]</p></td></tr><tr><td style="padding: 0 10px;color: #373737"><span style="float: left;padding: 0 3% 0 0;text-align: right">From :</span><span style="width:75%;float: left">[FROM]</span></td></tr><tr><td style="padding: 5px 10px;color: #373737"><span style="float: left;padding: 0 3% 0 0;text-align: right">To :</span><span style="width:75%;float: left">[TO]</span></td></tr><tr><td style="padding: 5px 10px;color: #373737"><h2 style="font-size: 30px;font-family: Arial, Helvetica, sans-serif">[AMOUNT]</h2></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="text-align: center;padding: 15px;color: #ffffff;background-color: #ff9898;font-size: 18px;font-family: Arial, Helvetica, sans-serif">[DISCLAIMER]</td></tr></tbody></table>';

				$template4_css = "html,body{margin: 0 auto !important;padding: 0 !important;height: 100% !important;width: 100% !important;}/* What it does: Stops email clients resizing small text. */*{-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */div[style*='margin: 16px 0']{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */table,td{mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */table{border-spacing: 0 !important;border-collapse: collapse !important;table-layout: fixed !important;margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */.mobile-link--footer a,a[x-apple-data-detectors]{color:inherit !important;text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */.button-link{text-decoration: none !important;}.mwb_coupon_section{text-align: center;background-position: center center !important;background-size: cover !important; padding-top: 27px;padding-bottom:27px;}@media screen and (max-width: 600px){.email-container{width: 100% !important;margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */.fluid{max-width: 100% !important;height: auto !important;margin-left: auto !important;margin-right: auto !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */.center-on-narrow{display: block !important;margin-left: auto !important;margin-right: auto !important;float: none !important;}table.center-on-narrow{display: inline-block !important;}}@media screen and (max-width: 480px){/* What it does: Forces table cells into full-width rows. */.stack-column,.stack-column-center{display: block !important;width: 100% !important;max-width: 100% !important;direction: ltr !important;}/* And center justify these ones. */.stack-column-center{text-align: center !important;}}.img-block > img{max-width: 300px; width: 100%;}";
				$gifttemplate = array(
					'post_title' => __( "Valentine's Day Special 1", 'woocommerce-ultimate-gift-card' ),
					'post_content' => $template_html4,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $template4_css ) );
				set_post_thumbnail( $parent_post_id, $arr[13] );

				$template_html5 = '<table class="email-container" style="margin: auto;text-align: center ! important;background-color: #fc3f3f;width: 600px" border="0" width="600px" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="text-align: left;padding: 10px">[LOGO]</td></tr><tr><td style="padding: 10px 0px !important">[FEATUREDIMAGE]</td></tr></tbody></table><table class="email-container" style="margin: auto" border="0" width="600" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="mwb_coupon_section" valign="middle" bgcolor="#fff"><div><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="width: 100%;text-align: center;padding: 0px 15px;font-family: sans-serif;font-size: 15px;line-height: 20px;color: #000" valign="middle"><h1 style="border: 2px dashed #fcd63f;text-align: center!important;padding: 33px;margin: 0;font-size: 15px;"><p style="color:#fc3f3f;font-size: 16px;margin: 0px">Coupon Code</p><p style="color: #fc3f3f;font-size: 18px;font-weight: bold;margin: 0px">[COUPON]</p><p style="font-family: sans-serif;color: #fc3f3f;font-size: 18px;font-weight: bold;margin: 0px">(Ed. [EXPIRYDATE] )</p></h1></td></tr></tbody></table></div></td></tr><tr><td style="padding: 10px" align="center" valign="top" bgcolor="#fcd63f"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td class="stack-column-center" style="width: 50%;vertical-align: top"><table border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 10px;text-align: center;width: 50%">[DEFAULTEVENT]</td></tr></tbody></table></td><td class="stack-column-center" style="width: 50%;vertical-align: top;padding: 10px"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td><p style="font-size: 15px;line-height: 1.5;font-family: sans-serif;min-height: 180px">[MESSAGE]</p></td></tr><tr><td style="padding: 25px 0px 0px 0px"><span style="float: left;padding: 0 3% 0 0;text-align: right"> From :</span> <span style="width:75%;float: left"> [FROM] </span></td></tr><tr><td><span style="float: left;padding: 0 3% 0 0;text-align: right">To :</span> <span style="width:75%;float: left"> [TO] </span></td></tr><tr><td><h2 style="font-size: 30px;font-family: Arial, Helvetica, sans-serif">[AMOUNT]</h2></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td bgcolor="#fc3f3f"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 20px 10px;text-align: center;font-family: sans-serif;font-size: 15px;line-height: 20px;color: #ffffff">[DISCLAIMER]</td></tr></tbody></table></td></tr></tbody></table>';

				$gifttemplate = array(
					'post_title' => __( "Valentine's Day Special 2", 'woocommerce-ultimate-gift-card' ),
					'post_content' => $template_html5,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);
				$template5_css = "html,body{margin: 0 auto !important; padding: 0 !important; height: 100% !important;width: 100% !important;}/* What it does: Stops email clients resizing small text. */ *{-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */ div[style*='margin: 16px 0']{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */ table,td{mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}table{border-spacing: 0 !important;border-collapse: collapse !important; table-layout: fixed !important;margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */ img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */ .mobile-link--footer a,a[x-apple-data-detectors]{color:inherit !important;text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */.button-link{text-decoration: none !important;}.mwb_coupon_section{text-align: center; padding-top: 27px; padding-bottom:27px; padding: 15px 0px;}@media screen and (max-width: 600px){.email-container{width: 100% !important; margin: auto !important;}.fluid{max-width: 100% !important; height: auto !important; margin-left: auto !important; margin-right: auto !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */ .center-on-narrow{text-align: center !important;display: block !important; margin-left: auto !important; margin-right: auto !important; float: none !important;}table.center-on-narrow{display: inline-block !important;}}@media screen and (max-width: 480px){/* What it does: Forces table cells into full-width rows. */ .stack-column,.stack-column-center{display: block !important; width: 100% !important;max-width: 100% !important;direction: ltr !important;}/* And center justify these ones. */.stack-column-center{text-align: center !important;}} ";

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $template5_css ) );
				set_post_thumbnail( $parent_post_id, $arr[14] );

				$rakhi_html = '&nbsp;<div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">(Optional) This text will appear in the inbox preview, but not the email body.</div><table class="email-container" style="margin: auto;" border="0" width="600" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding-top: 20px; text-align: center; color: #f48643; font-weight: bold; padding-left: 20px; font-size: 20px; font-family: sans-serif; position: absolute;">[LOGO]</td></tr></tbody></table><table class="email-container" style="margin: auto;" border="0" width="600" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#ffffff"><span class="feature_image" style="display: block; margin: 0px auto; width: 100%;"> [FEATUREDIMAGE] </span></td></tr><tr><td style="text-align: center; font-family: sans-serif; font-size: 15px; color: #1976e7; vertical-align: middle; display: table-cell; background: #ffebcc;"><h2 style="font-size: 16px; display: block; text-align: center!important; border: 22px solid #ffa412; padding: 15px 0px; margin: 0px; color: #000;">COUPON CODE <span style="display: block; font-size: 24px; padding: 8px 0 0 0; color: #000;">[COUPON]</span> <span style="display: block; font-size: 16px; padding: 8px 0 0 0;">(Ed:[EXPIRYDATE])</span></h2></td></tr><tr><td dir="ltr" style="padding: 22px 10px; background: #fff;" align="center" valign="top" bgcolor="#ffb001" width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="stack-column-center" valign="top" width="50%"><table border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="img_width_left_table" dir="ltr" style="padding: 0 10px 0 10px; width: 50%;" valign="top">[DEFAULTEVENT]</td></tr></tbody></table></td><td class="stack-column-center" valign="top" width="50%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; line-height: 20px; color: #ffffff; padding: 0px 30px 0px 0px; word-wrap: break-word; text-align: left;" valign="top"><p style="color: #000; font-size: 15px; height: auto; min-height: 180px; padding: 0px 0px 20px; text-align: left; word-break: break-word;">[MESSAGE]</p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #000; word-wrap: break-word;" valign="top"><p style="margin-bottom: 0px; font-size: 16px; text-align: left; color: #000;"><span style="display: inline-block; text-align: right; font-size: 15px; vertical-align: top; color: #000;">From-</span><span style="display: inline-block; text-align: left; font-size: 14px; vertical-align: top; word-break: break-all; color: #000;">[FROM]</span></p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; word-wrap: break-word; color: #fff;" valign="top"><p style="margin-top: 0px; font-size: 16px; line-height: 25px; text-align: left;"><span style="display: inline-block; text-align: right; font-size: 15px; vertical-align: top; color: #000;">To-</span><span style="display: inline-block; text-align: left; font-size: 14px; vertical-align: top; word-break: break-all; color: #000;">[TO]</span></p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #fff; word-wrap: break-word;" valign="top"><p style="text-align: left; font-weight: bold; font-size: 28px;"><span style="color: #dd6e00; margin: 20px 0; vertical-align: top;">[AMOUNT]/- </span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td bgcolor="#ffa412"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 40px; font-family: sans-serif; font-size: 16px; mso-height-rule: exactly; line-height: 20px; color: #fff; text-align: center;">[DISCLAIMER]</td></tr></tbody></table></td></tr></tbody></table>&nbsp;<style>@media screen and (max-width: 599px){.email-container{width: 100% !important;margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */.fluid{max-width: 100% !important;height: auto !important;margin-left: auto !important;margin-right: auto !important;}/* What it does: Forces table cells into full-width rows. */.stack-column,.stack-column-center{display: block !important;width: 100% !important;max-width: 100% !important;direction: ltr !important;}/* And center justify these ones. */.stack-column-center{text-align: center !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */.center-on-narrow{text-align: center !important;display: block !important;margin-left: auto !important;margin-right: auto !important;float: none !important;}table.center-on-narrow{display: inline-block !important;}}.feature_image > img{width: 100%!important;}</style>';
				$rakhi_css = 'html,body{margin: 0 auto !important;padding: 0 !important;height: 100% !important;width: 100% !important;}/* What it does: Stops email clients resizing small text. */*{-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */table,td{mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */table{border-spacing: 0 !important;border-collapse: collapse !important;table-layout: fixed !important;margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */.mobile-link--footer a,a[x-apple-data-detectors]{color:inherit !important;text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */.button-link{text-decoration: none !important;}.button-td,.button-a{transition: all 100ms ease-in;}.button-td:hover,.button-a:hover{background: #555555 !important;border-color: #555555 !important;}table.email-container{border: solid 1px #ccc !important;}td.img_width_left_table{} ';
				$gifttemplate = array(
					'post_title' => __( 'Happy Rakhi', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $rakhi_html,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);

				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $rakhi_css ) );
				set_post_thumbnail( $parent_post_id, $arr[15] );

				$diwali_html = '&nbsp;<div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">(Optional) This text will appear in the inbox preview, but not the email body.</div><table class="email-container email-container-logo" style="margin: auto;" border="0" width="600" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding-top: 20px; text-align: center; color: #f48643; font-weight: bold; padding-left: 20px; font-size: 20px; font-family: sans-serif; position: absolute;">[LOGO]</td></tr></tbody></table><table class="email-container" style="margin: auto;" border="0" width="600" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#5C007E"><span class="feature_image" style="display: block; margin: 0px auto; width: 100%;"> [FEATUREDIMAGE] </span></td></tr><tr><td style="text-align: center; font-family: sans-serif; font-size: 15px; color: #fff; vertical-align: middle; display: table-cell; background: #ffa40c; padding : 8px;"><h2 style="font-size: 16px; display: block; text-align: center!important; border: 2px dashed #fff; padding: 15px 0px; margin: 0px; color: #fff;">COUPON CODE <span style="display: block; font-size: 24px; padding: 8px 0 0 0; color: #fff;">[COUPON]</span> <span style="display: block; font-size: 16px; padding: 8px 0 0 0;">(Ed:[EXPIRYDATE])</span></h2></td></tr><tr><td dir="ltr" style="padding: 22px 10px; background: #fff;" align="center" valign="top" bgcolor="#ffb001" width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="stack-column-center" valign="top" width="50%"><table border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="img_width_left_table" dir="ltr" style="padding: 0 10px 0 10px; width: 50%;" valign="top">[DEFAULTEVENT]</td></tr></tbody></table></td><td class="stack-column-center" valign="top" width="50%"><table border="0" width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; line-height: 20px; color: #ffffff; padding: 0px 30px 0px 0px; word-wrap: break-word; text-align: left;" valign="top"><p style="color: #000; font-size: 15px; height: auto; min-height: 180px; padding: 0px 0px 20px; text-align: left; word-break: break-word;">[MESSAGE]</p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #000; word-wrap: break-word;" valign="top"><p style="margin-bottom: 0px; font-size: 16px; text-align: left; color: #000;"><span style="display: inline-block; text-align: right; font-size: 15px; vertical-align: top; color: #000;">From-</span><span style="display: inline-block; text-align: left; font-size: 14px; vertical-align: top; word-break: break-all; color: #000;">[FROM]</span></p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; word-wrap: break-word; color: #fff;" valign="top"><p style="margin-top: 0px; font-size: 16px; line-height: 25px; text-align: left;"><span style="display: inline-block; text-align: right; font-size: 15px; vertical-align: top; color: #000;">To-</span><span style="display: inline-block; text-align: left; font-size: 14px; vertical-align: top; word-break: break-all; color: #000;">[TO]</span></p></td></tr><tr><td class="center-on-narrow" dir="ltr" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 40px; color: #fff; word-wrap: break-word;" valign="top"><p style="text-align: left; font-weight: bold; font-size: 28px;"><span style="color: #301536; margin: 20px 0; vertical-align: top;">[AMOUNT]/- </span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td bgcolor="#5E0480"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 20px; font-family: sans-serif; font-size: 16px; mso-height-rule: exactly; line-height: 20px; color: #fff; text-align: center;">[DISCLAIMER]</td></tr></tbody></table></td></tr></tbody></table><style>@media screen and (max-width: 599px){.email-container{width: 100% !important;margin: auto !important;}/* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */.fluid{max-width: 100% !important;height: auto !important;margin-left: auto !important;margin-right: auto !important;}/* What it does: Forces table cells into full-width rows. */.stack-column,.stack-column-center{display: block !important;width: 100% !important;max-width: 100% !important;direction: ltr !important;}/* And center justify these ones. */.stack-column-center{text-align: center !important;}/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */.center-on-narrow{text-align: center !important;display: block !important;margin-left: auto !important;margin-right: auto !important;float: none !important;}table.center-on-narrow{display: inline-block !important;}}.feature_image > img{width: 100%!important;}</style>';

				$diwali_css = ' html,body{margin: 0 auto !important;padding: 0 !important;width: 100% !important;}/* What it does: Stops email clients resizing small text. */*{-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;}/* What is does: Centers email on Android 4.4 */div[style*="margin: 16px 0"]{margin:0 !important;}/* What it does: Stops Outlook from adding extra spacing to tables. */table,td{mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */table{border-spacing: 0 !important;border-collapse: collapse !important;table-layout: fixed !important;margin: 0 auto !important;}table table table{table-layout: auto;}/* What it does: Uses a better rendering method when resizing images in IE. */img{-ms-interpolation-mode:bicubic;}/* What it does: A work-around for iOS meddling in triggered links. */.mobile-link--footer a,a[x-apple-data-detectors]{color:inherit !important;text-decoration: underline !important;}/* What it does: Prevents underlining the button text in Windows 10 */.button-link{text-decoration: none !important;}.button-td,.button-a{transition: all 100ms ease-in;}.button-td:hover,.button-a:hover{background: #555555 !important;border-color: #555555 !important;}table.email-container{border: solid 1px #efefef !important;}td.img_width_left_table{}.email-container.email-container-logo{border: medium none !important;}';

				$gifttemplate = array(
					'post_title' => __( 'Happy Diwali', 'woocommerce-ultimate-gift-card' ),
					'post_content' => $diwali_html,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'giftcard',
				);
				$parent_post_id = wp_insert_post( $gifttemplate );
				update_post_meta( $parent_post_id, 'mwb_css_field', trim( $diwali_css ) );
				set_post_thumbnail( $parent_post_id, $arr[16] );
			}
		}
	}

	// activation hook
	register_activation_hook( __FILE__, 'mwb_wgm_create_gift_template' );
	/**
	 * This function is used to create default giftcard template on plugin activation
	 *
	 * @name mwb_wgm_create_gift_template
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */
	function mwb_wgm_create_gift_template() {
		$mwb_wgm_existing_time = get_option( 'mwb_wgm_activation_date_time', 'not_yet' );
		if ( isset( $mwb_wgm_existing_time ) && $mwb_wgm_existing_time == 'not_yet' ) {
			$mwb_wgm_current_datetime = current_time( 'timestamp' );
			update_option( 'mwb_wgm_activation_date_time', $mwb_wgm_current_datetime );
		}
		$pagetemplatecreated = get_option( 'mwb_wgm_giftcardtemplatepage', false );
		if ( $pagetemplatecreated == false ) {
			update_option( 'mwb_wgm_giftcardtemplatepage', true );

			$giftcardtemplateid = array();
			$template_html1 = ' <center style="width: 100%;"> <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto; width: 100%!important;" class="email-container"><tr> <td style="padding: 20px 10px; text-align: left">[LOGO] </td><td style="padding: 20px 10; text-align: right"><span style="font-size: 35px; font-family: arial; font-weight: bold; display: block;">[AMOUNT]</span><span style="font-size: 16px; font-family: arial; font-weight: bold; display: block;">(Ed: [EXPIRYDATE])</span> </td></tr></table> <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto; width: 100%!important;" class="email-container"> <tr><td align="center" style="padding: 40px 0px;"> <span style="font-size: 25px; font-family: "Roboto,Ubuntu,"Helvetica Neue",sans-serif"; font-weight: bold;">Coupon Code</span> <p style="background-color: #e91e63; color: #fff; padding: 10px 10px; font-size: 26px; font-family: arial; margin: 10px 0px 10px 0px; letter-spacing: 10px; word-wrap: break-word; word-break: break-all;">[COUPON]</p></td></tr><tr> <td align="center" valign="top" style="padding: 10px;"> <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"> <tr> <td class="stack-column-center" style="vertical-align: top; width: 50%;"> <table role="presentation" cellspacing="0" cellpadding="0" border="0"> <tr> <td style="padding: 10px; text-align: center"> [DEFAULTEVENT] </td></tr><tr> <td style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555; padding: 0 10px 10px; text-align: left;" class="center-on-narrow"> </td></tr></table> </td><td class="stack-column-center" style="vertical-align: top; width: 50%;"> <table width="100%" role="presentation" cellspacing="0" cellpadding="0" border="0"> <tr> <td style="padding: 10px;"> <p style="min-height: 190px;">[MESSAGE]</p></td></tr><tr> <td style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555; padding: 0 10px 10px; text-align: left;" class="center-on-narrow"><p style="font-size: 14px; font-family: sans-serif; margin: 0; font-weight: bold;"><span style="width:22%; float: left; word-wrap: break-word; word-break: break-all; padding: 0 3% 0 0; text-align: right;">From :</span><span style="width:75%; float: left; word-wrap: break-word; word-break: break-all;">[FROM]</span></p><p style="font-size: 14px; font-family: sans-serif; margin: 0; font-weight: bold;"><span style="width:22%; float: left; word-wrap: break-word; word-break: break-all; padding: 0 3% 0 0; text-align: right;">To :</span><span style="width:75%; float: left; word-wrap: break-word; word-break: break-all;">[TO]</span></p></td></tr></table> </td></tr></table> </td></tr></table> <table bgcolor="#333333" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto; width: 100%!important;" class="email-container"> <tr> <td bgcolor="#333333" style="padding: 10px 10px;width: 100%;font-size: 14px; font-weight: bold; font-family: sans-serif; mso-height-rule: exactly; line-height:18px; text-align: center; color: #ffffff;"> [DISCLAIMER] </td></tr></table> </center><style type="text/css">@media screen and (max-width: 480px){.stack-column,.stack-column-center{display: block !important;width: 100% !important;max-width: 100% !important;direction: ltr !important;}.stack-column-center{text-align: center !important;}}</style>';
			$gifttemplate = array(
				'post_title' => __( 'Template1', 'woocommerce-ultimate-gift-card' ),
				'post_content' => $template_html1,
				'post_status' => 'publish',
				'post_author' => get_current_user_id(),
				'post_type'     => 'giftcard',
			);

			$giftcardtemplateid[] = wp_insert_post( $gifttemplate );

			$template_html2 = ' <center style="width: 100%;"> <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto; width: 100%!important;" class="email-container"><tr> <td style="padding: 20px 10px; text-align: left">[LOGO] </td><td style="padding: 20px 10; text-align: right"><span style="font-size: 35px; font-family: arial; font-weight: bold; display: block;">[AMOUNT]</span><span style="font-size: 16px; font-family: arial; font-weight: bold; display: block;">(Ed: [EXPIRYDATE])</span> </td></tr></table> <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto; width: 100%!important;" class="email-container"> <tr><td align="center" style="padding: 40px 0px;"> <span style="font-size: 25px; font-family: "Roboto,Ubuntu,"Helvetica Neue",sans-serif"; font-weight: bold;">Coupon Code</span> <p style="background-color: #e91e63; color: #fff; padding: 10px 10px; font-size: 26px; font-family: arial; margin: 10px 0px 10px 0px; letter-spacing: 10px; word-wrap: break-word; word-break: break-all;">[COUPON]</p></td></tr><tr> <td align="center" valign="top" style="padding: 10px;"> <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"> <tr> <td class="stack-column-center" style="vertical-align: top; top; width: 50%;"> <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"> <tr> <td style="padding: 10px;"> <p style="min-height: 190px;">[MESSAGE]</p></td></tr><tr> <td style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555; padding: 0 10px 10px; text-align: left;" class="center-on-narrow"><p style="font-size: 14px; font-family: sans-serif; margin: 0; font-weight: bold;"><span style="width:22%; float: left; word-wrap: break-word; word-break: break-all; padding: 0 3% 0 0; text-align: right;">From :</span><span style="width:75%; float: left; word-wrap: break-word; word-break: break-all;">[FROM]</span></p><p style="font-size: 14px; font-family: sans-serif; margin: 0; font-weight: bold;"><span style="width:22%; float: left; word-wrap: break-word; word-break: break-all; padding: 0 3% 0 0; text-align: right;">To :</span><span style="width:75%; float: left; word-wrap: break-word; word-break: break-all;">[TO]</span></p></td></tr></table> </td><td class="stack-column-center" style="vertical-align: top; width: 50%;"> <table role="presentation" cellspacing="0" cellpadding="0" border="0"> <tr> <td style="padding: 10px; text-align: center"> [DEFAULTEVENT] </td></tr><tr> <td style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555; padding: 0 10px 10px; text-align: left;" class="center-on-narrow"> </td></tr></table> </td></tr></table> </td></tr></table> <table bgcolor="#333333" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto; width: 100%!important;" class="email-container"> <tr> <td bgcolor="#333333" style="padding: 10px 10px;width: 100%;font-size: 14px; font-weight: bold; font-family: sans-serif; mso-height-rule: exactly; line-height:18px; text-align: center; color: #ffffff;"> [DISCLAIMER] </td></tr></table> </center><style type="text/css">@media screen and (max-width: 480px){.stack-column,.stack-column-center{display: block !important;width: 100% !important;max-width: 100% !important;direction: ltr !important;}.stack-column-center{text-align: center !important;}}</style>';
			$gifttemplate = array(
				'post_title' => __( 'Template2', 'woocommerce-ultimate-gift-card' ),
				'post_content' => $template_html2,
				'post_status' => 'publish',
				'post_author' => get_current_user_id(),
				'post_type'     => 'giftcard',
			);

			$giftcardtemplateid[] = wp_insert_post( $gifttemplate );

			$template_html3 = ' <center style="width: 100%;"> <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto; width: 100%!important;" class="email-container"><tr> <td style="padding: 20px 10px; text-align: left">[LOGO] </td><td style="padding: 20px 10; text-align: right"><span style="font-size: 35px; font-family: arial; font-weight: bold; display: block;">[AMOUNT]</span><span style="font-size: 16px; font-family: arial; font-weight: bold; display: block;">(Ed: [EXPIRYDATE])</span> </td></tr></table> <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto; width: 100%!important;" class="email-container"> <tr><td align="center" style="padding: 40px 0px;"> <span style="font-size: 25px; font-family: "Roboto,Ubuntu,"Helvetica Neue",sans-serif"; font-weight: bold;">Coupon Code</span> <p style="background-color: #e91e63; color: #fff; padding: 10px 10px; font-size: 26px; font-family: arial; margin: 10px 0px 10px 0px; letter-spacing: 10px; word-wrap: break-word; word-break: break-all;">[COUPON]</p></td></tr><tr> <td align="center" valign="top" style="padding: 10px;"> <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"> <tr> <td class="stack-column-center" style="vertical-align: top; width: 50%;"> <table width="100%" role="presentation" cellspacing="0" cellpadding="0" border="0"><tr> <td class="stack-column-center" style="vertical-align: top; width: 50%;"> <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0"> <tr> <td style="padding: 10px; text-align: center"> [DEFAULTEVENT] </td></tr><tr> <td style="padding: 10px; text-align: center;"> <p>[MESSAGE]</p></td></tr></table> </td></tr><tr> <td style="text-align: center; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555; padding: 0 10px 10px; text-align: left;" class="center-on-narrow"><p style="text-align:center; font-size: 14px; font-family: sans-serif; margin: 0; font-weight: bold;"><span>From :</span><span>[FROM]</span></p><p style="text-align:center; font-size: 14px; font-family: sans-serif; margin: 0; font-weight: bold;"><span>To:</span><span>[TO]</span></p></td></tr></table> </td></tr></table> </td></tr></table> <table bgcolor="#333333" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto; width: 100%!important;" class="email-container"> <tr> <td bgcolor="#333333" style="padding: 10px 10px;width: 100%;font-size: 14px; font-weight: bold; font-family: sans-serif; mso-height-rule: exactly; line-height:18px; text-align: center; color: #ffffff;"> [DISCLAIMER] </td></tr></table> </center><style type="text/css">.stack-column-center img{max-width: 300px; width: 100%;}@media screen and (max-width: 480px){.stack-column,.stack-column-center{display: block !important;width: 100% !important;max-width: 100% !important;direction: ltr !important;}.stack-column-center{text-align: center !important;}}</style>';
			$gifttemplate = array(
				'post_title' => __( 'Template3', 'woocommerce-ultimate-gift-card' ),
				'post_content' => $template_html3,
				'post_status' => 'publish',
				'post_author' => get_current_user_id(),
				'post_type'     => 'giftcard',
			);

			$giftcardtemplateid[] = wp_insert_post( $gifttemplate );

			$term = __( 'Gift Card', 'woocommerce-ultimate-gift-card' );
			$taxonomy = 'product_cat';
			$term_exist = term_exists( $term, $taxonomy );
			if ( $term_exist == 0 || $term_exist == null ) {
				$args['slug'] = 'mwb_wgm_giftcard';
				$term_exist = wp_insert_term( $term, $taxonomy, $args );
			}

			$terms = get_term( $term_exist['term_id'], $taxonomy, ARRAY_A );
			$giftcard_category = $terms['slug'];
			$giftcard_content = "[product_category category='$giftcard_category']";

			$customer_reports = array(
				'post_author'    => get_current_user_id(),
				'post_name'      => __( 'Gift Card', 'woocommerce-ultimate-gift-card' ),
				'post_title'     => __( 'Gift Card', 'woocommerce-ultimate-gift-card' ),
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'post_content'   => $giftcard_content,
			);
			$page_id = wp_insert_post( $customer_reports );
		}
		if ( ! get_option( 'check_balance_page_created', false ) ) {

			/* ===== ====== Create the Check Gift Card Page ====== ======*/
			$balance_content = '[mwb_check_your_gift_card_balance]';

			$check_balance = array(
				'post_author'    => get_current_user_id(),
				'post_name'      => __( 'Gift Card Balance', 'woocommerce-ultimate-gift-card' ),
				'post_title'     => __( 'Gift Card Balance', 'woocommerce-ultimate-gift-card' ),
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'post_content'   => $balance_content,
			);
			$page_id = wp_insert_post( $check_balance );
			update_option( 'check_balance_page_created', true );
			/* ===== ====== End of Create the Gift Card Page ====== ======*/
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'offline_giftcard';
		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$wpdb->collate}";
		}
		$create_tbl = "
		CREATE TABLE IF NOT EXISTS `$table_name` (
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`to` text,
			`from` text,
			`message` text,
			`amount` text,
			`coupon` text,
			`template` text,
			`mail` text,	
			`date` datetime,
			`schedule` date,
			 PRIMARY KEY (`id`)
		);";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $create_tbl );
		$add_schedule = get_option( 'mwb_wgm_add_schedule', false );

		if ( $add_schedule == false ) {
			update_option( 'mwb_wgm_add_schedule', true );
			if ( ! empty( $wpdb->query ) ) {
				$wpdb->query( "ALTER TABLE $table_name ADD COLUMN `schedule` DATE" );
			}
		}

		// Schedule the even as per gmt Offset

		$offset = get_option( 'gmt_offset' );
		$time = time() + $offset * 60 * 60;

		if ( ! wp_next_scheduled( 'mwb_wgm_giftcard_cron_schedule' ) ) {
			wp_schedule_event( $time, 'hourly', 'mwb_wgm_giftcard_cron_schedule' );
		}

		if ( ! wp_next_scheduled( 'mwb_wgm_giftcard_cron_delete_images' ) ) {
			wp_schedule_event( $time, 'daily', 'mwb_wgm_giftcard_cron_delete_images' );
		}
		$uploadDirPath = MWB_UPLOAD_DIR . '/mwb_browse';
		if ( ! is_dir( $uploadDirPath ) ) {
			wp_mkdir_p( $uploadDirPath );
			chmod( $uploadDirPath, 0775 );
		}

		$uploadDirPath = MWB_UPLOAD_DIR . '/giftcard_pdf';
		if ( ! is_dir( $uploadDirPath ) ) {
			wp_mkdir_p( $uploadDirPath );
			chmod( $uploadDirPath, 0775 );
		}
		$uploadDirPath = MWB_UPLOAD_DIR . '/qrcode_barcode';
		if ( ! is_dir( $uploadDirPath ) ) {
			wp_mkdir_p( $uploadDirPath );
			chmod( $uploadDirPath, 0775 );
		}

		if ( ! wp_next_scheduled( 'mwb_wgm_update_json' ) ) {
			wp_schedule_event( time(), 'daily', 'mwb_wgm_update_json' );
		}

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
	register_deactivation_hook( __FILE__, 'mwb_wgm_remove_cron_schedule' );
	register_deactivation_hook( __FILE__, 'mwb_wgm_remove_cron_delete_images' );

	/**
	 * This function is used to remove the cron schedule
	 *
	 * @name mwb_wgm_giftcard_enable
	 * @return boolean
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */

	function mwb_wgm_remove_cron_schedule() {
		wp_clear_scheduled_hook( 'mwb_wgm_giftcard_cron_schedule' );
	}

	/**
	 * This function is used to remove the cron schedule for deleting images
	 *
	 * @name mwb_wgm_giftcard_enable
	 * @return boolean
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */

	function mwb_wgm_remove_cron_delete_images() {
		wp_clear_scheduled_hook( 'mwb_wgm_giftcard_cron_delete_images' );
	}
	/**
	 * This function is used to check that feature is enable or not
	 *
	 * @name mwb_wgm_giftcard_enable
	 * @return boolean
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */
	function mwb_wgm_giftcard_enable() {
		$giftcard_enable = get_option( 'mwb_wgm_general_setting_enable', false );
		if ( $giftcard_enable == 'on' ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add settings link on plugin page
	 *
	 * @name mwb_wgm_admin_settings()
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */

	function mwb_wgm_admin_settings( $actions, $plugin_file ) {
		static $plugin;
		if ( ! isset( $plugin ) ) {

			$plugin = plugin_basename( __FILE__ );
		}
		if ( $plugin == $plugin_file ) {
			$settings = array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=mwb-wgc-setting' ) . '">' . __( 'Settings', 'woocommerce-ultimate-gift-card' ) . '</a>',
			);
			$actions = array_merge( $settings, $actions );
		}
		return $actions;
	}

	// add link for settings
	add_filter( 'plugin_action_links', 'mwb_wgm_admin_settings', 10, 5 );

	/**
	 * This function is used to create giftcoupon for given amount
	 *
	 * @param $gift_couponnumber
	 * @param $couponamont
	 * @param $order_id
	 * @return boolean
	 */
	function mwb_wgm_create_offlinegift_coupon( $gift_couponnumber, $couponamount, $order_id, $product_id, $to ) {
		$mwb_wgm_enable = mwb_wgm_giftcard_enable();
		if ( $mwb_wgm_enable ) {
			$coupon_code = $gift_couponnumber; // Code
			$amount = $couponamount; // Amount
			$discount_type = 'fixed_cart';
			$coupon_description = "OFFLINE GIFTCARD ORDER #$order_id";

			$coupon = array(
				'post_title' => $coupon_code,
				'post_content' => $coupon_description,
				'post_excerpt' => $coupon_description,
				'post_status' => 'publish',
				'post_author' => get_current_user_id(),
				'post_type'     => 'shop_coupon',
			);

			$new_coupon_id = wp_insert_post( $coupon );

			$individual_use = get_option( 'mwb_wgm_general_setting_giftcard_individual_use', 'no' );
			$usage_limit = get_option( 'mwb_wgm_general_setting_giftcard_use', 1 );
			$expiry_date = get_option( 'mwb_wgm_general_setting_giftcard_expiry', 1 );
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

			update_post_meta( $new_coupon_id, 'apply_before_tax', $apply_before_tax );
			update_post_meta( $new_coupon_id, 'free_shipping', $free_shipping );
			update_post_meta( $new_coupon_id, 'minimum_amount', $minimum_amount );
			update_post_meta( $new_coupon_id, 'maximum_amount', $maximum_amount );
			update_post_meta( $new_coupon_id, 'exclude_sale_items', $exclude_sale_items );
			update_post_meta( $new_coupon_id, 'exclude_product_categories', $exclude_category );
			update_post_meta( $new_coupon_id, 'exclude_product_ids', $exclude_products );
			update_post_meta( $new_coupon_id, 'mwb_wgm_giftcard_coupon', $order_id );
			update_post_meta( $new_coupon_id, 'mwb_wgm_giftcard_coupon_unique', 'offline' );
			update_post_meta( $new_coupon_id, 'mwb_wgm_giftcard_coupon_product_id', $product_id );
			update_post_meta( $new_coupon_id, 'mwb_wgm_giftcard_coupon_mail_to', $to );
			return true;
		}
		return false;
	}

	function mwb_wgm_dismiss_admin_notices() {
		$user_id = get_current_user_id();
		if ( isset( $_GET['gifware-dismissed'] ) ) {
			add_user_meta( $user_id, 'giftware_notice_dismissed', 'true', true );
		}
	}

	add_action( 'admin_init', 'mwb_wgm_dismiss_admin_notices' );
	include( MWB_WGM_DIRPATH . '/includes/giftcard_redeem_api_addon.php' );
} else {
	/**
	 * Show warning message if woocommerce is not install
	 *
	 * @name mwb_wgm_plugin_error_notice()
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */

	function mwb_wgm_plugin_error_notice() {     ?>
		  <div class="error notice is-dismissible">
			 <p><?php _e( 'Woocommerce is not activated, Please activate Woocommerce first to install WooCommerce Ultimate Gift Card.', 'woocommerce-ultimate-gift-card' ); ?></p>
		   </div>
		   <style>
		   #message{display:none;}
		   </style>
		<?php
	}
	add_action( 'admin_init', 'mwb_wgm_plugin_deactivate' );
	/**
	 * Call Admin notices
	 *
	 * @name mwb_wgm_plugin_deactivate()
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */

	function mwb_wgm_plugin_deactivate() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'mwb_wgm_plugin_error_notice' );
	}
}
// Auto update
$mwb_wgm_license_key_pre = get_option( 'mwb_wgm_license_key', '' );
$mwb_wgm_license_key = get_option( 'mwb_wgm_license_key' . $_SERVER['HTTP_HOST'], $mwb_wgm_license_key_pre );
define( 'MWB_WGM_LICENSE_KEY', $mwb_wgm_license_key );
define( 'MWB_WGM_FILE', __FILE__ );
$mwb_wgm_update_check = 'https://makewebbetter.com/pluginupdates/codecanyon/woocommerce-ultimate-gift-card/update.php';
require_once( 'mwb-wgm-update.php' );
?>
