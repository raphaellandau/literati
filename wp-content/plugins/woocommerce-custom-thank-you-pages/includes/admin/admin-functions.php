<?php
namespace WooCommerce_Custom_Thank_You_Pages\Admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Admin functions.
 *
 * All functions for the admin of this plugin.
 *
 * @author		Jeroen Sormani
 * @since		1.0.0
 */


/**
 * Add plugin action links.
 *
 * Add a link to the settings page on the plugins.php page.
 *
 * @since 1.0.0
 *
 * @param  array $links List of existing plugin action links.
 * @return array        List of modified plugin action links.
 */
function plugin_action_links( $links ) {
	$links = array_merge( array(
		'<a href="' . esc_url( admin_url( '/admin.php?page=wc-settings&tab=products&tab=checkout&section=thank-you-pages' ) ) . '">' . __( 'Settings', 'woocommerce-custom-thank-you-pages' ) . '</a>'
	), $links );

	return $links;
}
add_action( 'plugin_action_links_' . plugin_basename( WOOCOMMERCE_CUSTOM_THANK_YOU_PAGES_FILE ), 'WooCommerce_Custom_Thank_You_Pages\Admin\plugin_action_links' );


/**
 * Enqueue admin scripts.
 *
 * Enqueue the required javascripts and stylesheets.
 *
 * @since 1.0.0
 *
 * @param $hook
 */
function enqueue_scripts( $hook ) {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_register_script( 'custom-thank-you-pages', plugins_url( 'assets/admin/js/woocommerce-custom-thank-you-pages' . $suffix . '.js', \WooCommerce_Custom_Thank_You_Pages\WooCommerce_Custom_Thank_You_Pages()->file ), array( 'jquery' ), \WooCommerce_Custom_Thank_You_Pages\WooCommerce_Custom_Thank_You_Pages()->version, true );
	wp_localize_script( 'custom-thank-you-pages', 'wcctyp', array(
		'nonce' => wp_create_nonce( 'wcctyp-ajax-nonce' ),
		'i18n' => array(
			'noChange' => __( '&mdash; No Change &mdash;' ),
		),
	) );

	if ( get_post_type() == 'product' || ( isset( $_GET['page'] ) && $_GET['page'] == 'wc-settings' && isset( $_GET['section'] ) && $_GET['section'] == 'thank-you-pages' ) ) {
		wp_enqueue_script( 'select2' );
		wp_enqueue_script( 'custom-thank-you-pages' );
	}
}
add_action( 'admin_enqueue_scripts', 'WooCommerce_Custom_Thank_You_Pages\Admin\enqueue_scripts' );


/**
 * Inline scripts.
 *
 * Print inline scripts for this plugin. It only adds them on the required pages.
 * Inline scripts have been chosen as they are too small to warrant extra GET requests.
 *
 * @since 1.0.0
 */
function admin_footer_scripts() {

	if ( get_post_type() !== 'product' && ( isset( $_GET['page'] ) && $_GET['page'] !== 'wc-settings' ) || ( isset( $_GET['section'] ) && $_GET['section'] !== 'thank-you-pages' ) ) {
		return;
	}

	?><style>
		#custom_thank_you_page + span + .woocommerce-help-tip:after {
			line-height: 30px;
		}
		.custom-thank-you-pages .select2-container {
			width: 80%;
		}
		.wcctyp-post-search .select2-choice {
			min-height: 25px;
		}
		.wcctyp-select2-match {
			width: 100%;
			display: block;
		}
		.wcctyp-select2-match .select2-result-type {
			float: right;
			color: #bbb;
		}
	</style><?php

}
add_action( 'admin_print_footer_scripts', 'WooCommerce_Custom_Thank_You_Pages\Admin\admin_footer_scripts' );


/**
 * Add bulk/quick edit option.
 *
 * Add a new bulk and quick edit option for the 'Confirmation Page' setting.
 *
 * @since 1.0.0
 */
function bulk_quick_edit_option() {
	?><br class="clear" />
	<div class="custom-thank-you-pages-field">
		<label>
			<span class="title"><?php _e( 'Confirmation Page', 'woocommerce-custom-thank-you-pages' ); ?></span>
			<span class="input-text-wrap">
				<select
					style="width: 100%;"
					name="custom_thank_you_page"
					class="wcctyp-post-search-quick-edit text"
					placeholder="<?php echo __( 'Search a page or enter a URL', 'woocommerce-custom-thank-you-pages' ) . '&hellip;'; ?>"
					data-multiple=false
					data-selected="<?php _e( 'No change', 'woocommerce-custom-thank-you-pages' ); ?>"
				>
					<option value="no_change"><?php _e( '&mdash; No Change &mdash;' ); ?></option>
				</select>
			</span>
		</label>
	</div>
	<br class="clear" />
	<div class="custom-thank-you-pages-field">
		<label>
			<span class="title"><?php _e( 'Priority', 'woocommerce-custom-thank-you-pages' ); ?></span>
			<span class="input-text-wrap">
				<input
					type="number"
					name="custom_thank_you_page_priority"
					placeholder="<?php _e( 'Priority', 'woocommerce-custom-thank-you-pages' ); ?>"
				>
			</span>
		</label>
	</div><?php
}
add_action( 'woocommerce_product_quick_edit_end', 'WooCommerce_Custom_Thank_You_Pages\Admin\bulk_quick_edit_option', 10 );
add_action( 'woocommerce_product_bulk_edit_end', 'WooCommerce_Custom_Thank_You_Pages\Admin\bulk_quick_edit_option', 10 );


/**
 * Save quick edit changes.
 *
 * Save the changes made in the 'Confirmation Page' custom box during
 * quick edit.
 *
 * @since 1.0.0
 *
 * @param \WC_Product $product Product object.
 */
function quick_edit_save( $product ) {

	if ( $_REQUEST['custom_thank_you_page'] != 'no_change' || ! empty( $_REQUEST['custom_thank_you_page_priority'] ) ) {
		save_custom_thank_you_page( $product->get_id() );
	}
}
add_action( 'woocommerce_product_quick_edit_save', 'WooCommerce_Custom_Thank_You_Pages\Admin\quick_edit_save', 10 );


/**
 * Save bulk edit.
 *
 * Save the custom bulk edit field.
 *
 * @since 1.0.0
 *
 * @param \WC_Product $product Product object.
 */
function bulk_edit_save( $product ) {

	if ( $_REQUEST['custom_thank_you_page'] != 'no_change' ) {
		$product->update_meta_data( '_custom_thank_you_page', wc_clean( $_REQUEST['custom_thank_you_page'] ) );
	}
	if ( ! empty( $_REQUEST['custom_thank_you_page_priority'] ) ) {
		$product->update_meta_data( '_custom_thank_you_page_priority', absint( $_REQUEST['custom_thank_you_page_priority'] ) );
	}

	$product->save();
}
add_action( 'woocommerce_product_bulk_edit_save', 'WooCommerce_Custom_Thank_You_Pages\Admin\bulk_edit_save', 10 );
