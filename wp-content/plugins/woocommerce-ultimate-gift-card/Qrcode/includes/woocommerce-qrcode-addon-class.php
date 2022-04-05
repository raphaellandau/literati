<?php

/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'MWB_WGM_QR_Card_Product' ) ) {

	/**
	 * This is class for managing order status and other functionalities .
	 *
	 * @name    MWB_WGM_QR_Card_Product
	 * @category Class
	 * @author   makewebbetter <webmaster@makewebbetter.com>
	 */

	class MWB_WGM_QR_Card_Product {

		/**
		 * This is construct of class where all action and filter is defined
		 *
		 * @name __construct
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function __construct() {
			 $this->qrtab = '';
			$this->qrtabactive = false;
			add_action( 'mwb_wgm_setting_tab', array( $this, 'mwb_wgm_qr_setting_tab' ) );
			add_action( 'mwb_wgm_setting_tab_active', array( $this, 'mwb_wgm_setting_tab_active' ) );
			add_action( 'mwb_wgm_setting_tab_html', array( $this, 'mwb_wgm_setting_tab_html' ) );
			add_filter( 'mwb_wgm_qrcode_coupon', array( $this, 'mwb_wgm_qrcode_coupon' ) );
			add_filter( 'mwb_wgm_static_coupon_img', array( $this, 'mwb_wgm_static_coupon_img' ) );
		}

		/**
		 * Replaces coupon with qrcode or barcode
		 *
		 * @name mwb_wgm_qrcode_coupon()
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_qrcode_coupon( $coupon ) {
			$site_name = $_SERVER['SERVER_NAME'];
			$time_stamp = time();
			$qrcode_enable = get_option( 'mwb_wgm_qrcode_enable', false );

			if ( $qrcode_enable == 'qrcode' ) {
				$qrcode_level = get_option( 'mwb_wgm_qrcode_ecc_level', 'L' );
				$qrcode_size = get_option( 'mwb_wgm_qrcode_size', 3 );
				$qrcode_margin = get_option( 'mwb_wgm_qrcode_margin', 4 );
				$new = new MWB_WGM_QR_Barcode_Card_Product();
				$new->getqrcode( $coupon, $qrcode_level, $qrcode_size, $qrcode_margin, $time_stamp, $site_name );
				return '<img class = "mwb_wgm_coupon_img" id = "' . $time_stamp . $site_name . '" src="' . MWB_UPLOAD_URL . '/qrcode_barcode/mwb__' . $time_stamp . $coupon . '.png">';
			} elseif ( $qrcode_enable == 'barcode' ) {

				$barcode_display = get_option( 'mwb_wgm_barcode_display_enable', false );
				$barcode_type = get_option( 'mwb_wgm_barcode_codetype', 'code39' );
				$barcode_size = get_option( 'mwb_wgm_barcode_size', 20 );
				$new = new MWB_WGM_QR_Barcode_Card_Product();
				$new->getbarcode( $coupon, $barcode_display, $barcode_type, $barcode_size, $time_stamp, $site_name );
				return '<img class = "mwb_wgm_coupon_img" id = "' . $time_stamp . $site_name . '" src="' . MWB_UPLOAD_URL . '/qrcode_barcode/mwb__' . $time_stamp . $coupon . '.png">';
			} else {
				return $coupon;
			}
		}
		public function mwb_wgm_static_coupon_img( $coupon ) {
			$qrcode_enable = get_option( 'mwb_wgm_qrcode_enable', false );
			if ( $qrcode_enable == 'qrcode' ) {
				return '<img src="' . MWB_WGM_URL . '/assets/images/mwb_qrcode.png">';
			} elseif ( $qrcode_enable == 'barcode' ) {
				return '<img src="' . MWB_WGM_URL . '/assets/images/mwb_barcode.png">';
			} else {
				return $coupon;
			}
		}
		/**
		 * This function displays Setting Tab
		 *
		 * @name mwb_wgm_qr_setting_tab()
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_qr_setting_tab() {
			?>
				<a class="nav-tab <?php echo $this->qrtab; ?>" href="?page=mwb-wgc-setting&tab=qrcode"><?php _e( 'QRCode/Barcode', MWB_WGM_QR_DOM ); ?></a>
			<?php
		}
		/**
		 * This function sets Setting Tab as active
		 *
		 * @name mwb_wgm_setting_tab_active()
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_setting_tab_active() {

			if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
				if ( $tab == 'qrcode' ) {
					$this->qrtab = 'nav-tab-active';
					$this->qrtabactive = true;
				}
			}
		}
		/**
		 * This function includes setting page template
		 *
		 * @name mwb_wgm_setting_tab_html()
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_setting_tab_html() {
			if ( $this->qrtabactive == true ) {
				include_once MWB_WGM_QR_DIRPATH . '/admin/qrcode-setting.php';
			}

		}
	}
	new MWB_WGM_QR_Card_Product();
}
