<?php
namespace WooCommerce_Custom_Thank_You_Pages;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Redirect to the custom Thank You page.
 *
 * This is THE function that redirects the customer to the custom
 * thank you page when available.
 *
 * @since 1.0.0
 *
 * @param $url
 * @param $order
 * @return array
 */
function order_confirmation_success_redirect( $url, $order ) {
	// Search for products with redirects
	$pages = get_thank_you_pages_in_order( $order->get_id() );

	if ( ! empty( $pages ) ) {
		$first_redirect = reset( $pages );
		return $first_redirect['url'];
	}

	return $url;
}
add_filter( 'woocommerce_get_checkout_order_received_url', '\WooCommerce_Custom_Thank_You_Pages\order_confirmation_success_redirect', 10, 2 );


/**
 * Get all pages in a order.
 *
 * Find all the possible thank you pages in a order based on the
 * purchased products.
 *
 * @since 1.0.0
 *
 * @param int|\WC_Order $order
 * @return array List of redirect URLs with their priority
 */
function get_thank_you_pages_in_order( $order ) {
	$pages = array();

	if ( ! $order = wc_get_order( $order ) ) {
		return array();
	}

	foreach ( $order->get_items() as $key => $item ) {
		/** @var \WC_Order_Item_Product $item */
		$product = $item->get_product();
		$redirect = $product->get_meta( '_custom_thank_you_page' );
		$priority = $product->get_meta( '_custom_thank_you_page_priority' );

		// Check for parent product
		if ( empty( $redirect ) && $product->is_type( 'variation' ) ) {
			$parent = wc_get_product( $product->get_parent_id() );
			$redirect = $parent->get_meta( '_custom_thank_you_page' );
			$priority = $parent->get_meta( '_custom_thank_you_page_priority' );
		}

		// Check for global custom thank you page
		if ( empty( $redirect ) ) {
			$global = get_option( 'wcctyp_global_custom_thank_you_page', null );
			if ( ! empty( $global ) ) {
				$redirect = get_option( 'wcctyp_global_custom_thank_you_page', null );
				$priority = 10;
			}
		}

		if ( ! empty( $redirect ) ) {
			$redirect = is_numeric( $redirect ) ? apply_filters( 'wpml_object_id', $redirect, 'post', true ) : $redirect; // WPML support?
			$url = is_url( $redirect ) ? $redirect : get_permalink( $redirect );
			$url = add_query_arg( array( 'order' => $order->get_id(), 'key' => $order->get_order_key() ), $url );
			$pages[] = array(
				'url' => esc_url_raw( $url ),
				'priority' => absint( $priority ),
			);
		}
	}

	// Sort pages
	usort( $pages, function( $a, $b ) {
		return $a['priority'] - $b['priority'];
	});

	return apply_filters( 'WCCTYP/thank_you_pages_in_order', $pages, $order );
}


/**
 * Output thank you page navigation.
 *
 * Output navigation when there are multiple thank you pages for the provided order.
 *
 * @since 1.0.0
 *
 * @param int|\WC_Order $order Order to output the pagination for.
 */
function thank_you_page_navigation( $order ) {
	$order = wc_get_order( $order );
	$pages = get_thank_you_pages_in_order( $order );

	if ( count( $pages ) <= 1 ) {
		return;
	}

	$current_page_index = ! empty( $_GET['page_index'] ) ? absint( $_GET['page_index'] ) : null;
	$previous_page = isset( $pages[ $current_page_index - 1 ] ) ? $pages[ $current_page_index - 1 ] : null;
	$next_page = isset( $pages[ $current_page_index + 1 ] ) ? $pages[ $current_page_index + 1 ] : null;

	$previous_page_link = ! is_null( $previous_page ) ? add_query_arg( 'page_index', $current_page_index - 1, $previous_page['url'] ) : null;
	$next_page_link = ! is_null( $next_page ) ? add_query_arg( 'page_index', $current_page_index + 1, $next_page['url'] ) : null;

	$path = plugin_dir_path( WooCommerce_Custom_Thank_You_Pages()->file ) . 'templates/';
	wc_get_template( 'page-switcher.php', array(
		'order' => $order,
		'previous_link' => $previous_page_link,
		'next_link' => $next_page_link,
	), 'woocommerce-custom-thank-you-pages', $path );
}


/**
 * Maybe add navigation.
 *
 * Add thank you page navigation at before the content
 * when enabled for multiple thank you pages.
 *
 * @since 1.0.0
 *
 * @param string $content Post content.
 * @return string Post content, possibly prefixed with navigation.
 */
function maybe_add_thank_you_page_navigation( $content ) {
	global $wp;

	if ( 'yes' !== get_option( 'custom-thank-you-page-navigation', false ) ) {
		return $content;
	}

	if ( ! isset( $wp->query_vars['order'] ) || ! is_numeric( $wp->query_vars['order'] ) ) {
		return $content;
	}

	if ( is_cart() ) { // Present to prevent cancelled payment error on the cart page
		return $content;
	}

	$order_id = $wp->query_vars['order'];

	ob_start();
		thank_you_page_navigation( $order_id );
	$page_switcher = ob_get_clean();

	return $page_switcher . $content;
}
add_filter( 'the_content', '\WooCommerce_Custom_Thank_You_Pages\maybe_add_thank_you_page_navigation', -10 );


/**
 * Clear cart on thank you page.
 *
 * Helps with Subscriptions compatibility when products can only be purchased once.
 *
 * @since 1.0.1
 * @since 1.0.4 - Add check for Stripe (iDeal and possibly others) to prevent emptying cart on failed payment
 */
function clear_cart_thank_you_page() {
	if ( ! empty( $_GET['key'] ) && ! empty( $_GET['order'] ) && ( ! isset( $_GET['redirect_status'] ) || $_GET['redirect_status'] !== 'failed' ) ) {

		$order_id  = absint( $_GET['order'] );
		$order_key = wc_clean( wp_unslash( $_GET['key'] ) );

		if ( $order_id && $order = wc_get_order( $order_id ) ) {
			if ( $order && $order->get_order_key() === $order_key ) {
				WC()->cart->empty_cart();
			}
		}
	}
}
add_action( 'wp_loaded', '\WooCommerce_Custom_Thank_You_Pages\clear_cart_thank_you_page', 1 );


/**
 * Set order-received param.
 *
 * Helps with Subscriptions compatibility when products can only be purchased once.
 * Can also help with other extensions that rely on the 'order-received' parameter.
 *
 * @since 1.0.3
 */
function custom_thank_you_page_order_received_param() {

	if ( ! empty( $_GET['key'] ) && ! empty( $_GET['order'] ) ) {
		$order_id  = absint( $_GET['order'] );
		$order_key = wc_clean( wp_unslash( $_GET['key'] ) );

		if ( $order_id && $order = wc_get_order( $order_id ) ) {
			if ( $order && $order->get_order_key() === $order_key ) {
				global $wp;
				$wp->query_vars['order-received'] = $order_id;
			}
		}
	}
}
add_action( 'wp', '\WooCommerce_Custom_Thank_You_Pages\custom_thank_you_page_order_received_param', 1 );


/**
 * Make sure custom thank you pages are recognized as order received pages.
 *
 * @since 1.0.2
 *
 * @param $is_page
 * @return bool
 */
function order_received_page( $is_page ) {
	if ( ! $is_page && isset( $_GET['order'], $_GET['key'] ) ) {
		$order_id = absint( $_GET['order'] );
		$order = wc_get_order( $order_id );

		if ( $order && $order->get_order_key() === $_GET['key'] ) {
			return true;
		}
	}

	return $is_page;
}
add_filter( 'woocommerce_is_order_received_page', '\WooCommerce_Custom_Thank_You_Pages\order_received_page' );


/**
 * Fix page not found.
 *
 * Since setting the 'order-received' parameter is set a 'Page not found' error occurs when
 * the page is not pre-defined as a endpoint (in WC()->query->query_vars). This resolves that.
 *
 * @since 1.0.3
 *
 * @param $found
 * @return false
 */
function account_endpoint_page_not_found( $found ) {
	if ( order_received_page( false ) ) {
		return false;
	}

	return $found;
}
add_filter( 'woocommerce_account_endpoint_page_not_found', '\WooCommerce_Custom_Thank_You_Pages\account_endpoint_page_not_found' );


/**
 * Fix page title.
 *
 * Due to setting the 'order-received' parameter WC overrides the page title with the
 * endpoint page title. This re-instates the custom thank you page title.
 *
 * @since 1.0.3
 *
 * @param $title
 * @return mixed
 */
function fix_wrong_title( $title ) {
	if ( order_received_page( false ) ) {
		remove_filter( 'the_title', 'wc_page_endpoint_title' );
	}

	return $title;
}
add_filter( 'the_title', '\WooCommerce_Custom_Thank_You_Pages\fix_wrong_title', 5 );
