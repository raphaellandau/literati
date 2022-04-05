<?php
namespace WooCommerce_Custom_Thank_You_Pages\Admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Admin {

	public $settings = null;


	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {

		// Include files
		$this->includes();

		$this->settings = new Settings();
	}


	/**
	 * Include files.
	 *
	 * Include/require plugin files/classes.
	 *
	 * @since  1.0.0
	 */
	public function includes() {
		require_once plugin_dir_path( __FILE__ ) . '/admin-functions.php';
		require_once plugin_dir_path( __FILE__ ) . '/product-functions.php';
		require_once plugin_dir_path( __FILE__ ) . '/help-functions.php';
		require_once plugin_dir_path( __FILE__ ) . '/settings.php';

	}


}
