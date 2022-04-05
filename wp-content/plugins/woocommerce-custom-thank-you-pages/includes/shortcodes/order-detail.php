<?php
namespace WooCommerce_Custom_Thank_You_Pages\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Order_Detail {


	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {}


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

		// Get the order.
		$order_id  = apply_filters( 'woocommerce_thankyou_order_id', absint( $wp->query_vars['order'] ) );
		$order_key = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['key'] ) ? '' : wc_clean( wp_unslash( $_GET['key'] ) ) ); // WPCS: input var ok, CSRF ok.

		if ( $order_id > 0 ) {
			$order = wc_get_order( $order_id );
			if ( ! $order || $order->get_order_key() !== $order_key ) {
				$order = false;
			}
		}

		if ( empty( $order ) ) {
			return false;
		}

		WC()->payment_gateways(); // Make sure gateways are loaded in.

		$atts = shortcode_atts( array(
			'key' => 'id',
		), $atts, 'order_detail' );

		switch ( $atts['key'] ) {
			case 'id' :
				$return = $order->get_id();
				break;
			case 'order_number' :
				$return = $order->get_order_number();
				break;
			case 'order_date' :
				if ( $date = $order->get_date_created() ) {
					$date = $date->format( get_option( 'date_format' ) );
				}

				$return = $date;
				break;
			case 'total':
				$return = $order->get_formatted_order_total();
				break;
			case 'subtotal':
				$return = $order->get_subtotal_to_display();
				break;
			case 'payment_method':
				$return = $order->get_payment_method_title();
				break;
			case 'shipping_method':
				$return = $order->get_shipping_method();
				break;
			case 'status':
				$statuses = wc_get_order_statuses();
				$return = isset( $statuses[ 'wc-' . $order->get_status() ] ) ? $statuses[ 'wc-' . $order->get_status() ] : $order->get_status();
				break;
			case 'quantity':
				$return = count( $order->get_items() );
				break;
			case 'shipping_address':
				$return = wp_kses_post( $order->get_formatted_shipping_address( __( 'N/A', 'woocommerce' ) ) );
				break;
			case 'billing_address':
				$return = wp_kses_post( $order->get_formatted_billing_address( __( 'N/A', 'woocommerce' ) ) );
				break;
			case 'gateway_action' :
				ob_start();
					do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
				$return = ob_get_clean();
			break;
			case 'thankyou_action' :
				ob_start();
					do_action( 'woocommerce_thankyou', $order->get_id() );
				$return = ob_get_clean();
			break;
			case 'thankyou_order_received_text' :
				$return = apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), $order );
			break;
			case 'download_urls' :
				ob_start();
					woocommerce_order_downloads_table( $order->get_downloadable_items() );
				$return = ob_get_clean();
				break;
			default :
				if ( method_exists( $order, 'get_' . $atts['key'] ) ) {
					$return = call_user_func( array( $order, 'get_' . $atts['key'] ) );
				} else {
					$return = $order->get_meta( $atts['key'] );
				}

				break;
		}

		return $return;
	}


}
