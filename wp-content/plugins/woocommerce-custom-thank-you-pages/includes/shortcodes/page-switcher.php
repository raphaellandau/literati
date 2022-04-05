<?php
namespace WooCommerce_Custom_Thank_You_Pages\Shortcodes;

use function WooCommerce_Custom_Thank_You_Pages\get_thank_you_pages_in_order;
use function WooCommerce_Custom_Thank_You_Pages\thank_you_page_navigation;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Page_Switcher {


	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {

	}


	/**
	 * Output shortcode content.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $atts
	 * @param  string $content
	 * @return string|void
	 */
	public function output( $atts, $content ) {
		global $wp;

		if ( ! isset( $wp->query_vars['order'] ) ) {
			return '';
		}

		$order_id = $wp->query_vars['order'];

		ob_start();
			thank_you_page_navigation( $order_id );
		$return = ob_get_clean();

		return $return;
	}


}