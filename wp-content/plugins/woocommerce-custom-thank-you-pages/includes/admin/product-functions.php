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
 * Add thank you page option.
 *
 * Add the field that allows the user to set the custom thank you page URL/ID.
 *
 * @since 1.0.0
 */
function add_thank_you_page_option_to_product() {

	global $post;

	$product  = wc_get_product( $post );
	$value    = $product->get_meta( '_custom_thank_you_page' );
	$priority = $product->get_meta( '_custom_thank_you_page_priority' );
	if ( \WooCommerce_Custom_Thank_You_Pages\is_url( $value ) ) {
		$selected_value = esc_url( $value );
		$selected_text  = $selected_value;
	} elseif ( is_numeric( $value ) && $value != 0 ) {
		$selected_value = absint( $value );
		$selected_text  = get_the_title( $selected_value );
	} else {
		$selected_value = null;
		$selected_text  = null;
	}

	?><div class="options_group custom-thank-you-pages">
		<p class="form-field">
			<label><?php _e( 'Confirmation Page', 'woocommerce-custom-thank-you-pages' ); ?></label>

			<select
				style="width: 60%;"
				name="custom_thank_you_page"
				class="wcctyp-post-search"
				id="custom_thank_you_page"
				placeholder="<?php echo __( 'Search a page or enter a URL', 'woocommerce-custom-thank-you-pages' ) . '&hellip;'; ?>"
				data-multiple=false
			>
				<option value="<?php echo $selected_value; ?>"><?php echo $selected_text; ?></option>
				<option value=""></option>
			</select>
			<span class="woocommerce-help-tip" style="height: 30px; float: left;" data-tip="<?php echo esc_attr( __( 'Set a custom order \'Thank you\' page for when a order is placed.', 'woocommerce-custom-thank-you-pages' ) ); ?>"></span>

			<input
				type="number"
				name="custom_thank_you_page_priority"
				placeholder="<?php _e( 'Priority', 'woocommerce-custom-thank-you-pages' ); ?>"
			    style="width: 20%; margin-left: 10px; height: 32px;"
			    value="<?php echo ! empty( $priority ) ? absint( $priority ) : 10; ?>"
			><?php
			echo wc_help_tip( __( 'Priority determines which page is shown when multiple products are purchased or in which order when using navigation.', 'woocommerce-custom-thank-you-pages' ) );
		?></p>
	</div><?php

}
add_action( 'woocommerce_product_options_advanced', '\WooCommerce_Custom_Thank_You_Pages\Admin\add_thank_you_page_option_to_product' );


/**
 * Add thank you page option
 *
 * Add the Custom Thank You Page option to variations of a product.
 *
 * @since 1.0.0
 *
 * @param $loop
 * @param $variation_data
 * @param $variation
 */
function add_thank_you_page_to_variation( $loop, $variation_data, $variation ) {

	$variation = wc_get_product( $variation );
	$value = $variation->get_meta( '_custom_thank_you_page' );
	$priority = $variation->get_meta( '_custom_thank_you_page_priority' );
	if ( \WooCommerce_Custom_Thank_You_Pages\is_url( $value ) ) {
		$selected_value = esc_url( $value );
		$selected_text  = $selected_value;
	} elseif ( is_numeric( $value ) && $value != 0 ) {
		$selected_value = absint( $value );
		$selected_text  = get_the_title( $selected_value );
	} else {
		$selected_value = null;
		$selected_text  = null;
	}

	?><p class="form-row form-field form-row-full">
		<label style="min-width: 75%;">
			<?php _e( 'Confirmation Page', 'woocommerce-custom-thank-you-pages' ); ?>
			<?php echo wc_help_tip( __( 'Set the URL/page to use as thank you page. Leave empty for default behaviour', 'woocommerce-custom-thank-you-pages' ) ); ?>
		</label>
		<label style="width: calc( 25% - 14px ); margin-left: 10px;">
			<?php _e( 'Priority', 'woocommerce-custom-thank-you-pages' ); ?>
			<?php echo wc_help_tip( __( 'Priority determines which page is shown when multiple products are purchased or in which order when using navigation.', 'woocommerce-custom-thank-you-pages' ) ); ?>
		</label>

		<select
			style="width: 75%; float: left;"
			type="text"
			name="variable_custom_thank_you_page[<?php echo $loop; ?>]"
			class="wcctyp-post-search"
			id="custom_thank_you_page"
			placeholder="<?php echo __( 'Search a page or enter a URL', 'woocommerce-custom-thank-you-pages' ) . '&hellip;'; ?>"
			data-multiple=false
		>
			<option value="<?php echo $selected_value; ?>"><?php echo $selected_text; ?></option>
			<option value=""></option>
		</select>
		<input
			type="number"
			name="custom_thank_you_page_priority"
			placeholder="<?php _e( 'Priority', 'woocommerce-custom-thank-you-pages' ); ?>"
			style="width: calc( 25% - 14px ); margin: 0 0 0 10px; height: 32px; vertical-align: top;"
			value="<?php echo ! empty( $priority ) ? absint( $priority ) : 10; ?>"
		><?php
	?></p>
	<?php

}
add_action( 'woocommerce_product_after_variable_attributes', '\WooCommerce_Custom_Thank_You_Pages\Admin\add_thank_you_page_to_variation', 10, 3 );


/**
 * Save thank you page URL.
 *
 * Save the set than you page URL or post ID to the meta table.
 *
 * @since 1.0.0
 *
 * @param int $post_id ID of the post being saved.
 */
function save_custom_thank_you_page( $post_id ) {

	$product = wc_get_product( $post_id );
	if ( isset( $_POST['custom_thank_you_page'] ) && $_REQUEST['custom_thank_you_page'] != 'no_change' ) {
		$url = $_POST['custom_thank_you_page'];
		if ( \WooCommerce_Custom_Thank_You_Pages\is_url( $url ) ) {
			$product->update_meta_data( '_custom_thank_you_page', esc_url_raw( $url ) );
		} elseif ( get_post_status( $url ) !== false ) {
			$product->update_meta_data( '_custom_thank_you_page', absint( $url ) );
		} else {
			$product->update_meta_data( '_custom_thank_you_page', '' );
		}
	}

	if ( isset( $_POST['custom_thank_you_page_priority'] ) ) {
		$product->update_meta_data( '_custom_thank_you_page_priority', absint( $_POST['custom_thank_you_page_priority'] ) );
	}

	$product->save();
}
add_action( 'woocommerce_process_product_meta', 'WooCommerce_Custom_Thank_You_Pages\Admin\save_custom_thank_you_page', 10 );


/**
 * Save variation custom thank you page value.
 *
 * Save the set custom thank you page URL or post ID to the meta table.
 *
 * @since 1.0.0
 *
 * @param int $variation_id Variation ID being saved.
 * @param int $i       Index of variation.
 */
function save_variable_custom_thank_you_page( $variation_id, $i ) {

	$custom_thank_you_page = $_POST['variable_custom_thank_you_page'] ?: array();

	foreach ( $custom_thank_you_page as $k => $v ) {

		if ( ! isset( $custom_thank_you_page[ $i ] ) ) {
			continue;
		}

		$variation        = wc_get_product( $variation_id );
		$value            = $custom_thank_you_page[ $i ];
		if ( \WooCommerce_Custom_Thank_You_Pages\is_url( $value ) ) {
			$variation->update_meta_data( '_custom_thank_you_page', esc_url_raw( $value ) );
		} elseif ( get_post_status( $value ) !== false ) {
			$variation->update_meta_data( '_custom_thank_you_page', absint( $value ) );
		} else {
			$variation->update_meta_data( '_custom_thank_you_page', '' );
		}
		$variation->update_meta_data( '_custom_thank_you_page_priority', absint( $_POST['custom_thank_you_page_priority'] ) );

		$variation->save();
	}

}
add_action( 'woocommerce_save_product_variation', 'WooCommerce_Custom_Thank_You_Pages\Admin\save_variable_custom_thank_you_page', 10, 2 );
