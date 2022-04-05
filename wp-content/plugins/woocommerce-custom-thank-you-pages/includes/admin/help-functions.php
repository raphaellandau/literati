<?php
namespace WooCommerce_Custom_Thank_You_Pages\Admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Help/doc functionality.
 */


/**
 * Add help tab.
 */
function add_help_tabs() {
	global $pagenow, $page;

	$screen = get_current_screen();

	ob_start();
		?><p><?php _e( 'This page is recognized as potentially being a custom thank you page.', 'woocommerce-custom-thank-you-pages' ); ?></p><?php
		?><p><?php _e( 'If you\'d like to add order specific data to this page, the following shortcode can be used:', 'woocommerce-custom-thank-you-pages' ); ?>
			<br><strong><code>[order_detail key="{KEY}"]</code></strong>
		<p><?php _e( 'The following keys can be used to display data;', 'woocommerce-custom-thank-you-pages' ); ?></p>
		<ul>
			<li><code>id</code></li>
			<li><code>total</code></li>
			<li><code>subtotal</code></li>
			<li><code>payment_method</code></li>
			<li><code>shipping_method</code></li>
			<li><code>status</code></li>
			<li><code>quantity</code></li>
			<li><code>shipping_address</code></li>
			<li><code>billing_address</code></li>
		</ul>
		<p><?php
			echo sprintf( __( 'For more information on how to use the WooCommerce Custom Thank You Pages plugin, please visit %s', 'woocommerce-custom-thank-you-pages' ),
				'<a href="#" target="_blank" rel="noreferrer noopener">woocommerce.com</a>');
		?></p>
		</p><?php
	$content = ob_get_clean();

	// Add my_help_tab if current screen is My Admin Page
	$screen->add_help_tab( array(
		'id'	    => 'wc-custom-thank-you-pages',
		'title'	   => __('WC Custom Thank You Pages'),
		'content'  => $content,
		'priority' => 59,
	) );
}
add_action( 'load-post.php', 'WooCommerce_Custom_Thank_You_Pages\Admin\add_help_tabs', 50 );
