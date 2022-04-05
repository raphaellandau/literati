<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $_POST['mwb_wgm_product_setting_save'] ) ) {
	unset( $_POST['mwb_wgm_product_setting_save'] );
	if ( isset( $_POST['mwb_wgm_product_setting_exclude_product'] ) ) {
		$giftcard_exclude_products = $_POST['mwb_wgm_product_setting_exclude_product'];


		$woo_ver = WC()->version;
		if ( $woo_ver < '3.0.0' ) {
			if ( isset( $giftcard_exclude_products ) && ! empty( $giftcard_exclude_products ) ) {
				update_option( 'mwb_wgm_product_setting_exclude_product_format', $giftcard_exclude_products );
			}
		} else {
			if ( isset( $giftcard_exclude_products ) && ! empty( $giftcard_exclude_products ) ) {
				$giftcard_exclude_product_string = '';
				foreach ( $giftcard_exclude_products as $giftcard_exclude_product ) {
					$giftcard_exclude_product_string .= $giftcard_exclude_product . ',';
				}
				$giftcard_exclude_product_string = rtrim( $giftcard_exclude_product_string, ',' );

				update_option( 'mwb_wgm_product_setting_exclude_product_format', $giftcard_exclude_product_string );
			}
		}
	} else {
		$_POST['mwb_wgm_product_setting_exclude_product'] = '';
		update_option( 'mwb_wgm_product_setting_exclude_product_format', $_POST['mwb_wgm_product_setting_exclude_product'] );

	}

	if ( isset( $_POST['mwb_wgm_product_setting_exclude_category'] ) ) {

	} else {
		$_POST['mwb_wgm_product_setting_exclude_category'] = '';
	}


	if ( ! isset( $_POST['mwb_wgm_general_setting_giftcard_ex_sale'] ) ) {
		$_POST['mwb_wgm_general_setting_giftcard_ex_sale'] = 'no';
	}

	if ( isset( $_POST['mwb_wgm_include_product'] ) ) {

		// formating of include products

		$include_product_string = '';
		if ( isset( $_POST['mwb_wgm_include_product'] ) && ! empty( $_POST['mwb_wgm_include_product'] ) ) {
			$include_product_string = '';
			foreach ( $_POST['mwb_wgm_include_product'] as $giftcard_include_product ) {
				$include_product_string .= $giftcard_include_product . ',';
			}
			$include_product_string = rtrim( $include_product_string, ',' );
			update_option( 'include_products_format', $include_product_string );
		} else {
			$_POST['mwb_wgm_include_product'] = '';
			update_option( 'include_products_format', $_POST['mwb_wgm_include_product'] );
		}
	} else {


		$_POST['mwb_wgm_include_product'] = '';
		update_option( 'include_products_format', $_POST['mwb_wgm_include_product'] );
	}

	if ( ! isset( $_POST['mwb_wgm_include_categories'] ) ) {

		$_POST['mwb_wgm_include_categories'] = '';
	}

	do_action( 'mwb_wgm_product_setting_save' );

	$postdata = $_POST;

	foreach ( $postdata as $key => $data ) {
		update_option( $key, $data );
	}
	?>
	<div class="notice notice-success is-dismissible"> 
		<p><strong><?php _e( 'Settings saved', 'woocommerce-ultimate-gift-card' ); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php _e( 'Dismiss this notice', 'woocommerce-ultimate-gift-card' ); ?></span>
		</button>
	</div>
	<?php
}
$giftcard_exclude_product = get_option( 'mwb_wgm_product_setting_exclude_product', array() );
$giftcard_exclude_category = get_option( 'mwb_wgm_product_setting_exclude_category', array() );
$giftcard_ex_sale = get_option( 'mwb_wgm_general_setting_giftcard_ex_sale', false );
$include_product = get_option( 'mwb_wgm_include_product', array() );
$include_categories = get_option( 'mwb_wgm_include_categories', array() );
?>
<div class="mwb_table">
<table class="form-table mwb_wgm_general_setting">
	<tbody>

		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_general_setting_giftcard_ex_sale"><?php _e( 'Exclude Sale Items', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box if the Giftcard Coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are no sale items in the cart.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_general_setting_giftcard_ex_sale">
					<input type="checkbox" <?php echo ( $giftcard_ex_sale == 'yes' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_general_setting_giftcard_ex_sale" id="mwb_wgm_general_setting_giftcard_ex_sale" class="input-text" value="yes"> <?php _e( 'Enable to exclude Sale Items', 'woocommerce-ultimate-gift-card' ); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_product_setting_exclude_product"><?php _e( 'Exclude Products', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
			<?php
				$attribute_description = __( 'Products which must not be in the cart to use Giftcard coupon or, for "Product Discounts", which products are not discounted.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				$woo_ver = WC()->version;
			if ( $woo_ver < '3.0.0' ) {
				?>
				<p class="form-field">
					<input type="hidden" class="wc-product-search" data-multiple="true" style="width: 50%;" name="mwb_wgm_product_setting_exclude_product" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-ultimate-gift-card' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-selected="
							<?php
							$product_ids = array_filter( array_map( 'absint', explode( ',', $giftcard_exclude_product ) ) );
							$json_ids    = array();
							if ( isset( $product_ids ) && ! empty( $product_ids ) ) {
								foreach ( $product_ids as $product_id ) {
									$product = wc_get_product( $product_id );
									if ( is_object( $product ) ) {
										$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
									}
								}
							}
							echo esc_attr( json_encode( $json_ids ) );
							?>
						" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" />
				</p>
				<?php
			} else {
				?>
					<label for="mwb_wgm_product_setting_exclude_product">
						<select class="wc-product-search" multiple="multiple" style="width: 50%;" name="mwb_wgm_product_setting_exclude_product[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-ultimate-gift-card' ); ?>" data-action="woocommerce_json_search_products_and_variations" id="mwb_wgm_product_setting_exclude_product">
						<?php
						if ( isset( $giftcard_exclude_product ) && ! empty( $giftcard_exclude_product ) ) {
							foreach ( $giftcard_exclude_product as $pro_id ) {
								$product      = wc_get_product( $pro_id );
								$product_title = $product->get_formatted_name();
								echo '<option value="' . esc_attr( $pro_id ) . '" selected="selected">' . esc_html( $product_title ) . '</option>';
							}
						}
						?>
						</select>
					</label>
				<?php
			}
			?>

			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_product_setting_exclude_category"><?php _e( 'Exclude Product Category', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Product must not be in this category for the Giftcard coupon to remain valid or, for "Product Discounts", products in these categories will not be discounted.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_product_setting_exclude_category">
					<select id="mwb_wgm_product_setting_exclude_category" multiple="multiple" name="mwb_wgm_product_setting_exclude_category[]">
					<?php
					$args = array( 'taxonomy' => 'product_cat' );
					$categories = get_terms( $args );
					if ( isset( $categories ) && ! empty( $categories ) ) {
						foreach ( $categories as $category ) {
							$catid = $category->term_id;
							$catname = $category->name;
							$catselect = '';

							if ( is_array( $giftcard_exclude_category ) && in_array( $catid, $giftcard_exclude_category ) ) {
								$catselect = "selected='selected'";
							}

							?>
							<option value="<?php echo $catid; ?>" <?php echo $catselect; ?>><?php echo $catname; ?></option>
							<?php
						}
					}
					?>
					</select>	
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_include_product"><?php _e( 'Include Products', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Product that the coupon will be applied to, or that need to be in the cart in order for the "Gift Card discount" to be applied.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_include_product">
					<select class="wc-product-search" multiple="multiple" style="width: 50%;" name="mwb_wgm_include_product[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-ultimate-gift-card' ); ?>" data-action="woocommerce_json_search_products_and_variations" id="mwb_wgm_include_product">
						<?php
						if ( isset( $include_product ) && ! empty( $include_product ) ) {
							foreach ( $include_product as $pro_id ) {
								$product      = wc_get_product( $pro_id );
								$product_title = $product->get_formatted_name();
								echo '<option value="' . esc_attr( $pro_id ) . '" selected="selected">' . esc_html( $product_title ) . '</option>';
							}
						}
						?>
					</select>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_include_categories"><?php _e( 'Include Product Category', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Product categories that the coupon will be applied to, or that need to be in the cart in order for the "Gift Card discount" to be applied.', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label class = "mwb_wgm_include_categories" for="mwb_wgm_include_categories">
					<select id="mwb_wgm_include_categories" multiple="multiple" name="mwb_wgm_include_categories[]">
					<?php
					$args = array( 'taxonomy' => 'product_cat' );
					$categories = get_terms( $args );
					if ( isset( $categories ) && ! empty( $categories ) ) {
						foreach ( $categories as $category ) {
							$catid = $category->term_id;
							$catname = $category->name;
							$catselect = '';
							if ( is_array( $include_categories ) && ! empty( $include_categories ) ) {
								if ( is_array( $include_categories ) && in_array( $catid, $include_categories ) ) {
									$catselect = "selected='selected'";
								}
							}
							?>
							<option value="<?php echo $catid; ?>" <?php echo $catselect; ?>><?php echo $catname; ?></option>
							<?php
						}
					}
					?>
					</select>
			</td>
		</tr>
		<?php
		do_action( 'mwb_wgm_product_setting' );
		?>
	</tbody>
</table>
</div>
<p class="submit">
	<input type="submit" value="<?php _e( 'Save changes', 'woocommerce-ultimate-gift-card' ); ?>" class="button-primary woocommerce-save-button" name="mwb_wgm_product_setting_save">
</p>

<div class="clear"></div>
	
