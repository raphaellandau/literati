<?php
namespace WooCommerce_Custom_Thank_You_Pages;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WooCommerce_Custom_Thank_You_Pages.
 *
 * Main WooCommerce_Custom_Thank_You_Pages class initializes the plugin.
 *
 * @class		WooCommerce_Custom_Thank_You_Pages
 * @version		1.0.0
 * @author		Jeroen Sormani
 */
class WooCommerce_Custom_Thank_You_Pages {

	public $version = '1.0.4';

	public $file = WOOCOMMERCE_CUSTOM_THANK_YOU_PAGES_FILE;

	private static $instance;


	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		$this->init();
	}


	/**
	 * Instance.
	 *
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.0.0
	 *
	 * @return WooCommerce_Custom_Thank_You_Pages Instance of the class.
	 */
	public static  function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Initialize plugin parts.
	 *
	 * @since  1.0.0
	 */
	public function init() {

		// Load textdomain
		$this->load_textdomain();

		// Include files
		$this->includes();

		// Add shortcodes
		$this->add_shortcodes();

		// Admin
		if ( is_admin() ) {
			$this->admin = new \WooCommerce_Custom_Thank_You_Pages\Admin\Admin();
		}
	}


	/**
	 * Textdomain.
	 *
	 * Load the textdomain based on WP language.
	 *
	 * @since  1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'woocommerce-custom-thank-you-pages', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}


	/**
	 * Include files.
	 *
	 * Include/require plugin files/classes.
	 *
	 * @since  1.0.0
	 */
	public function includes() {

		require_once plugin_dir_path( $this->file ) . 'includes/helper-functions.php';
		require_once plugin_dir_path( $this->file ) . 'includes/ajax-functions.php';
		require_once plugin_dir_path( $this->file ) . 'includes/admin/admin.php';
		require_once plugin_dir_path( $this->file ) . 'includes/shortcodes/confirmation-order-details.php';
		require_once plugin_dir_path( $this->file ) . 'includes/shortcodes/page-switcher.php';
		require_once plugin_dir_path( $this->file ) . 'includes/shortcodes/order-detail.php';
		require_once plugin_dir_path( $this->file ) . 'includes/core-functions.php';
	}


	/**
	 * Add shortcodes
	 *
	 * Add the shortcodes to WordPress with their callbacks to be initialised.
	 *
	 * @since  1.0.0
	 */
	public function add_shortcodes() {
		add_shortcode( 'order_detail', array( new \WooCommerce_Custom_Thank_You_Pages\Shortcodes\Order_Detail(), 'output' ) );
		add_shortcode( 'confirmation_order_details', array( new \WooCommerce_Custom_Thank_You_Pages\Shortcodes\Confirmation_Order_Details(), 'output' ) );
		add_shortcode( 'wcctyp_page_switcher', array( new \WooCommerce_Custom_Thank_You_Pages\Shortcodes\Page_Switcher(), 'output' ) );
	}


}

/**
 * The main function responsible for returning the WooCommerce_Custom_Thank_You_Pages object.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php WooCommerce_Custom_Thank_You_Pages()->method_name(); ?>
 *
 * @since 1.0.0
 *
 * @return WooCommerce_Custom_Thank_You_Pages Return the singleton WooCommerce_Custom_Thank_You_Pages object.
 */
function WooCommerce_Custom_Thank_You_Pages() {
	return WooCommerce_Custom_Thank_You_Pages::instance();
}
add_action( 'woocommerce_loaded', '\WooCommerce_Custom_Thank_You_Pages\WooCommerce_Custom_Thank_You_Pages' );
