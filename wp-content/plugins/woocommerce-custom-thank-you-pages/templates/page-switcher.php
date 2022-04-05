<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


?>
<style>
	.wcctyp-page-switcher {
		float: right;
	}
	.wcctyp-previous-page,
	.wcctyp-next-page {
		padding: 10px 20px;
		background: #ddd;
		font-weight: bold;
		font-size: 2em;
		color: #555;
		margin-left: 5px;
	}
	.wcctyp-previous-page:hover,
	.wcctyp-next-page:hover {
		background: #ccc;
		color: #333;
	}
	.wcctyp-previous-page.disabled,
	.wcctyp-next-page.disabled {
		background: #eee;
		color: #ccc;
	}
</style>
<div class="wcctyp-page-switcher"><?php
	if ( ! empty( $previous_link ) ) {
		?><a href="<?php echo esc_url( $previous_link ); ?>" class="wcctyp-previous-page" title="<?php _e( 'Previous page', 'woocommerce-custom-thank-you-pages' ); ?>">&lt;</a><?php
	} else {
		?><a href="javascript:void(0);" class="wcctyp-previous-page disabled" title="<?php _e( 'Previous page', 'woocommerce-custom-thank-you-pages' ); ?>">&lt;</a><?php
	}

	if ( ! empty( $next_link ) ) {
		?><a href="<?php echo esc_url( $next_link ); ?>" class="wcctyp-next-page" title="<?php _e( 'Next page', 'woocommerce-custom-thank-you-pages' ); ?>">&gt;</a><?php
	} else {
		?><a href="javascript:void(0);" class="wcctyp-next-page disabled" title="<?php _e( 'Next page', 'woocommerce-custom-thank-you-pages' ); ?>">&gt;</a><?php
	}

?></div>
