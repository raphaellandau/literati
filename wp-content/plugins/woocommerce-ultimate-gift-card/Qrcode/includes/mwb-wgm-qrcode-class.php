<?php

/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'MWB_WGM_QR_Barcode_Card_Product' ) ) {

	/**
	 * This is class for managing order status and other functionalities .
	 *
	 * @name    MWB_WGM_QR_Barcode_Card_Product
	 * @category Class
	 * @author   makewebbetter <webmaster@makewebbetter.com>
	 */

	class MWB_WGM_QR_Barcode_Card_Product {

		/**
		 * This function sets qrcode image
		 *
		 * @name getqrcode()
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function getqrcode( $coupon, $qrcode_level, $qrcode_size, $qrcode_margin, $time_stamp, $site_name ) {

			$path = MWB_UPLOAD_DIR . '/qrcode_barcode/mwb__' . $time_stamp . $coupon . '.png';
			
			if ( $qrcode_level == 'L' ) {
				$qrcode_level = QR_ECLEVEL_L;
			} elseif ( $qrcode_level == 'M' ) {
				$qrcode_level = QR_ECLEVEL_M;
			} elseif ( $qrcode_level == 'Q' ) {
				$qrcode_level = QR_ECLEVEL_Q;
			} elseif ( $qrcode_level == 'H' ) {
				$qrcode_level = QR_ECLEVEL_H;
			}
			QRcode::png( $coupon, $path, $qrcode_level, $qrcode_size, $qrcode_margin );

		}
		/**
		 * This function sets barrcode image
		 *
		 * @name getbarcode()
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function getbarcode( $coupon, $barcode_display, $barcode_type, $barcode_size, $time_stamp, $site_name ) {
			$path = MWB_UPLOAD_DIR . '/qrcode_barcode/mwb__' . $time_stamp . $coupon . '.png';
			if ( $barcode_display == 'on' ) {
				$barcode_display = true;
			} else {
				$barcode_display = false;
			}
			barcode( $path, $coupon, $barcode_size, 'horizontal', $barcode_type, $barcode_display, 1 );
		}
	}
}
