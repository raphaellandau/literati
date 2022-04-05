<?php

/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	define( 'MWB_WGM_QR_DOM', 'woocommerce-ultimate-gift-card' );
	define( 'MWB_WGM_QR_DIRPATH', plugin_dir_path( __FILE__ ) );
	define( 'MWB_WGM_QR_URL', plugin_dir_url( __FILE__ ) );



	include_once MWB_WGM_QR_DIRPATH . '/includes/woocommerce-qrcode-addon-class.php';
	include_once MWB_WGM_QR_DIRPATH . '/includes/mwb-wgm-qrcode-class.php';
	include_once MWB_WGM_QR_DIRPATH . '/phpqrcode/qrlib.php';
	include_once MWB_WGM_QR_DIRPATH . '/php-barcode-master/barcode.php';



