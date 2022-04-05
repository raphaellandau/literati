<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	define( 'MWB_WGM_SD_DOM', 'woocommerce-ultimate-gift-card' );
	define( 'MWB_WGM_SD_DIRPATH', plugin_dir_path( __FILE__ ) );
	define( 'MWB_WGM_SD_URL', plugin_dir_url( __FILE__ ) );
	include_once MWB_WGM_SD_DIRPATH . '/includes/woocommerce-shipping-addon-class.php';
	include_once MWB_WGM_SD_DIRPATH . '/public/mwb-wgm-shipping-public-manager.php';

