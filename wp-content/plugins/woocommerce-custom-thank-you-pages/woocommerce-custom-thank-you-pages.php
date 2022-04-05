<?php
/**
 * Plugin Name: 	WooCommerce Custom Thank You Pages
 * Plugin URI:      https://woocommerce.com/products/custom-thank-pages/
 * Description:     Create custom thank you pages for products. Upsell products / improve user experience / increase customer lifetime value.
 * Version: 		1.0.4
 * Author:          Jeroen Sormani
 * Author URI:      https://jeroensormani.com/
 * Text Domain: 	woocommerce-custom-thank-you-pages
 *
 * WC requires at least: 3.0
 * WC tested up to: 5.0
 * Woo: 3637139:37212b4fd8c946b70cfc1ca350f0c879
 */


/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}
/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '37212b4fd8c946b70cfc1ca350f0c879', '3637139' );



/**
 * Display PHP 5.5 required notice.
 *
 * Display a notice when the required PHP version is not met.
 *
 * @since  1.0.0
 */
function wcctyp_php_version_notices() {
	?><div class='updated'>
		<p><?php echo sprintf( __( 'WooCommerce Custom Thank You Pages requires PHP 5.5 or higher and your current PHP version is %s. Please (contact your host to) update your PHP version.', 'reviewer' ), PHP_VERSION ); ?></p>
	</div><?php
}

if ( version_compare( PHP_VERSION, '5.5', 'lt' ) ) {
	add_action( 'admin_notices', 'wcctyp_php_version_notices' );
	return;
}


/**
 * Show a notice at activation.
 */
function wcctyp_activation_notice() {
	global $pagenow;

	if ( $pagenow == 'plugins.php' && get_transient( 'wcctyp_activation_notice' ) ) {
		?><div class="updated notice is-dismissible">
		<p><?php echo sprintf( __( 'To start using WooCommerce Custom Thank You Pages, %s configure a custom \'Thank You\' page per product %s or %s configure a global custom thank you page %s.', 'add-to-cart-redirect-for-woocommerce' ),
				'<a href="' . esc_url( admin_url( 'edit.php?post_type=product' ) ) . '">', '</a>',
				'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=thank-you-pages' ) ) . '">', '</a>'
			);
			?><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'add-to-cart-redirect-for-woocommerce' ); ?></span></button></p>
		</div><?php

		delete_transient( 'wcctyp_activation_notice' );
	}

}
add_action( 'admin_notices', 'wcctyp_activation_notice' );

function wcctyp_on_activation() {
	set_transient( 'wcctyp_activation_notice', 1, 30 ); // 30 seconds
}
register_activation_hook( __FILE__, 'wcctyp_on_activation' );

define( 'WOOCOMMERCE_CUSTOM_THANK_YOU_PAGES_FILE', __FILE__ );
require_once 'includes/class-woocommerce-custom-thank-you-pages.php';
