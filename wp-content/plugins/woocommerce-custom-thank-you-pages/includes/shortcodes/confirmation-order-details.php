<?php
namespace WooCommerce_Custom_Thank_You_Pages\Shortcodes;


class Confirmation_Order_Details {


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
	 * @return string
	 */
	public function output( $atts, $content ) {
		global $wp;

		// Get the order.
		$order_id  = apply_filters( 'woocommerce_thankyou_order_id', absint( $wp->query_vars['order'] ) );
		$order_key = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['key'] ) ? '' : wc_clean( wp_unslash( $_GET['key'] ) ) ); // WPCS: input var ok, CSRF ok.

		if ( $order_id > 0 ) {
			$order = wc_get_order( $order_id );
			if ( ! $order || $order->get_order_key() !== $order_key ) {
				$order = false;
			}
		}

		ob_start();
			if ( ! empty( $order ) ) {
				\woocommerce_order_details_table( $order->get_id() );
			}
		$return = ob_get_clean();

		return $return;
	}


}
