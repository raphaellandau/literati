<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $_POST['mwb_wgm_discount_setting_save_hidden'] ) ) {
	unset( $_POST['mwb_wgm_discount_setting_save_hidden'] );
	if ( ! isset( $_POST['mwb_wgm_discount_enable'] ) ) {
		$_POST['mwb_wgm_discount_enable'] = 'off';
	}
	if ( ! isset( $_POST['mwb_wgm_discount_type'] ) ) {
		$_POST['mwb_wgm_discount_type'] = 'mwb_wgm_fixed';
	}
	if ( ! isset( $_POST['mwb_wgm_discount_minimum'] ) ) {
		$_POST['mwb_wgm_discount_minimum'] = array();
	}
	if ( ! isset( $_POST['mwb_wgm_discount_maximum'] ) ) {
		$_POST['mwb_wgm_discount_maximum'] = array();
	}
	if ( ! isset( $_POST['mwb_wgm_discount_current_type'] ) ) {
		$_POST['mwb_wgm_discount_current_type'] = array();
	}
	do_action( 'mwb_wgm_discount_setting_save' );
	$postdata = $_POST;
	foreach ( $postdata as $key => $data ) {
		update_option( $key, $data );
	}
	?>
	<div class="notice notice-success is-dismissible"> 
		<p><strong><?php _e( 'Settings Saved Successfully!', 'woocommerce-ultimate-gift-card' ); ?></strong></p>
	</div>
	<?php
}
$discount_enable = get_option( 'mwb_wgm_discount_enable', false );
$discount_type = get_option( 'mwb_wgm_discount_type', 'mwb_wgm_fixed' );
$discount_min = get_option( 'mwb_wgm_discount_minimum', array() );
$discount_max = get_option( 'mwb_wgm_discount_maximum', array() );
$discount_value = get_option( 'mwb_wgm_discount_current_type', array() );
?>
<table class="form-table mwb_wgm_discount_setting wp-list-table widefat  striped">
	<tbody>	
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_discount_enable"><?php _e( 'Enable Discount on Giftcard Produts', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Check this box to enable Discount for Giftcard Products', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_discount_enable">
					<input type="checkbox" <?php echo ( $discount_enable == 'on' ) ? "checked='checked'" : ''; ?> name="mwb_wgm_discount_enable" id="mwb_wgm_discount_enable" class="input-text"> <?php _e( 'Enable Discount on Giftcard Products.', 'woocommerce-ultimate-gift-card' ); ?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_discount_type"><?php _e( 'Select Discount Type', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				$attribute_description = __( 'Choose the Discount Type for Giftcard Products', 'woocommerce-ultimate-gift-card' );
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_discount_type">
					<select name="mwb_wgm_discount_type" class="mwb_wgm_new_woo_ver_style_select">
						<option value="mwb_wgm_fixed" <?php selected( $discount_type, 'mwb_wgm_fixed' ); ?>><?php _e( 'Fixed', 'woocommerce-ultimate-gift-card' ); ?></option>
						<option value="mwb_wgm_percentage" <?php selected( $discount_type, 'mwb_wgm_percentage' ); ?>><?php _e( 'Percentage', 'woocommerce-ultimate-gift-card' ); ?></option>
					</select>
				</label>						
			</td>
		</tr>
		<tr valign="top" class="mwb_wgm_discount_row" style="display: none;">
			<th>
				<label for="mwb_wgm_discount_fields"><?php _e( 'Enter Discount within Price Range', 'woocommerce-ultimate-gift-card' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<table class="form-table wp-list-table widefat fixed striped">
					<tbody class="mwb_wgm_discount_tbody">	
						<tr valign="top">
							<th><?php _e( 'Minimum', 'woocommerce-ultimate-gift-card' ); ?></th>
							<th><?php _e( 'Maximum', 'woocommerce-ultimate-gift-card' ); ?></th>
							<?php
							if ( $discount_type == 'mwb_wgm_fixed' ) {
								?>
										<th><?php _e( 'Discount Amount', 'woocommerce-ultimate-gift-card' ); ?></th>
									<?php
							} else if ( $discount_type == 'mwb_wgm_percentage' ) {
								?>
										<th><?php _e( 'Discount Percentage(%)', 'woocommerce-ultimate-gift-card' ); ?></th>
									<?php
							}
							?>
							<th class="mwb_wgm_remove_discount_content"><?php _e( 'Action', 'woocommerce-ultimate-gift-card' ); ?></th>
						</tr>
						<?php
						if ( isset( $discount_min ) && $discount_min != null && isset( $discount_max ) && $discount_max != null && isset( $discount_value ) && $discount_value != null ) {
							if ( count( $discount_min ) == count( $discount_max ) && count( $discount_max ) == count( $discount_value ) ) {

								foreach ( $discount_min as $key => $value ) {
									?>
											<tr valign="top">
												<td class="forminp forminp-text">
													<label for="mwb_wgm_discount_minimum">
														<input type="text" name="mwb_wgm_discount_minimum[]" class="mwb_wgm_discount_minimum input-text wc_input_price" required="" value="<?php echo $discount_min[ $key ]; ?>">
													</label>
												</td>
												<td class="forminp forminp-text">
													<label for="mwb_wgm_discount_maximum">
														<input type="text" name="mwb_wgm_discount_maximum[]" class="mwb_wgm_discount_maximum input-text wc_input_price" required="" value="<?php echo $discount_max[ $key ]; ?>">
													</label>
												</td>
												<td class="forminp forminp-text">
													<label for="mwb_wgm_discount_current_type">
														<input type="text" name="mwb_wgm_discount_current_type[]" class="mwb_wgm_discount_current_type input-text wc_input_price" required=""  value="<?php echo $discount_value[ $key ]; ?>">
													</label>
												</td>							
												<td class="mwb_wgm_remove_discount_content forminp forminp-text">
													<input type="button" value="<?php _e( 'Remove', 'woocommerce-ultimate-gift-card' ); ?>" class="mwb_wgm_remove_discount button" >
												</td>
											</tr>
										<?php
								}
							}
						} else {
							?>
									<tr valign="top">
										<td class="forminp forminp-text">
											<label for="mwb_wgm_discount_minimum">
												<input type="text" name="mwb_wgm_discount_minimum[]" class="mwb_wgm_discount_minimum input-text wc_input_price" required="">
											</label>
										</td>
										<td class="forminp forminp-text">
											<label for="mwb_wgm_discount_maximum">
												<input type="text" name="mwb_wgm_discount_maximum[]" class="mwb_wgm_discount_maximum input-text wc_input_price" required="">
											</label>
										</td>
										<td class="forminp forminp-text">
											<label for="mwb_wgm_discount_current_type">
												<input type="text" name="mwb_wgm_discount_current_type[]" class="mwb_wgm_discount_current_type input-text wc_input_price" required="">
											</label>
										</td>							
										<td class="mwb_wgm_remove_discount_content forminp forminp-text">
											<input type="button" value="<?php _e( 'Remove', 'woocommerce-ultimate-gift-card' ); ?>" class="mwb_wgm_remove_discount button" >
										</td>
									</tr>
								<?php
						}
						?>
					</tbody>
				</table>
				<input type="button" value="<?php _e( 'Add More', 'woocommerce-ultimate-gift-card' ); ?>" class="mwb_wgm_add_more button" id="mwb_wgm_add_more">
			</td>
		</tr>
	</tbody>
</table>
<p class="submit">
	<input type="hidden" name="mwb_wgm_discount_setting_save_hidden">
	<input type="submit" value="<?php _e( 'Save changes', 'woocommerce-ultimate-gift-card' ); ?>" class="button-primary woocommerce-save-button" name="mwb_wgm_discount_setting_save" id="mwb_wgm_discount_setting_save" >
</p>
