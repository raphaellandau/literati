<?php
namespace WooCommerce_Custom_Thank_You_Pages;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Check given URL validity.
 *
 * Check if the passed URL is a valid URL. If so return true, false otherwise.
 *
 * @since 1.0.0
 *
 * @param  string $url URL to verify and validate.
 * @return bool        True when the URL is valid, false otherwise.
 */
function is_url( $url ) {

	if ( filter_var( $url, FILTER_VALIDATE_URL ) === false ) {
		return false;
	}

	return true;

}
