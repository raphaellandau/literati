<?php
namespace Add_To_Cart_Redirect_For_WooCommerce\Admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Search through posts.
 *
 * Search through all the posts to give as suggestions to the user.
 *
 * @since 1.0.0
 */
function custom_thank_you_page_post_search() {

	global $wpdb;

	check_ajax_referer( 'wcctyp-ajax-nonce', 'security' );

	$term = (string) wc_clean( stripslashes( $_GET['term']['term'] ) );

	if ( empty( $term ) ) {
		die();
	}

	$like_term = '%' . $wpdb->esc_like( $term ) . '%';

	$post_types = get_post_types( array( 'public' => true ) );
	$post_types = implode( "','", $post_types );
	$query      = $wpdb->prepare( "
		SELECT ID, post_title, post_type FROM {$wpdb->posts} as posts
		WHERE posts.post_status = 'publish'
		AND post_type IN ('$post_types')
		AND (
			posts.ID = %s
			OR posts.post_title LIKE %s
		)
	", $term, $like_term );

	if ( ! empty( $_GET['limit'] ) ) {
		$query .= ' LIMIT ' . intval( $_GET['limit'] );
	}

	$posts = $wpdb->get_results( $query );

	$found_posts = array();
	foreach ( $posts as $values ) {
		$found_posts[] = array(
			'id'   => $values->ID,
			'text' => $values->post_title,
			'type' => $values->post_type,
		);
	}

	// Custom option - Is done through JS right now
	if ( esc_url_raw( $term ) != '' ) {
		$found_posts[] = array(
			'id'   => esc_url_raw( $term ),
			'text' => esc_url_raw( $term ),
			'type' => 'custom',
		);
	}

	wp_send_json( $found_posts );

}
add_action( 'wp_ajax_wcctyp_search_posts', 'Add_To_Cart_Redirect_For_WooCommerce\Admin\custom_thank_you_page_post_search' );
add_action( 'wp_ajax_nopriv_wcctyp_search_posts', 'Add_To_Cart_Redirect_For_WooCommerce\Admin\custom_thank_you_page_post_search' );
