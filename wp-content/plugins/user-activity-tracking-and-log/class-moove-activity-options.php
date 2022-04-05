<?php
/**
 * Moove_Activity_Options File Doc Comment
 *
 * @category 	Moove_Activity_Options
 * @package   user-activity-tracking-and-log
 * @author    Moove Agency
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Moove_Activity_Options Class Doc Comment
 *
 * @category Class
 * @package  Moove_Activity_Options
 * @author   Moove Agency
 */
class Moove_Activity_Options {
	/**
	 * Global options
	 *
	 * @var array
	 */
	private $options;
	/**
	 * Construct
	 */
	public function __construct() {
		add_action( 'uat_log_settings_capability', array( &$this, 'uat_custom_log_settings_capability' ), 10, 1 );
		add_action( 'uat_activity_log_capability', array( &$this, 'uat_custom_activity_log_capability' ), 10, 1 );
		add_action( 'update_option_moove_post_act', array( $this, 'moove_activity_check_settings' ), 10, 2 );
		add_action( 'plugins_loaded', array( $this, 'load_languages' ) );
	}

	/**
	 * Custom capability type for Activity log settings (Settings -> Activity log)
	 *
	 * @param string $capability Capability.
	 */
	public function uat_custom_log_settings_capability( $capability ) {
		return $capability;
	}

	/**
	 * Custom capability type for Activity log table (CMS -> Activity log)
	 *
	 * @param string $capability Capability.
	 */
	public function uat_custom_activity_log_capability( $capability ) {
		return $capability;
	}

	/**
	 * Plugin localization data
	 */
	public function load_languages() {
		load_plugin_textdomain( 'user-activity-tracking-and-log', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Callback function after settings page saved. If there is any changes,
	 * it change the selected post type posts by the settings page value.
	 *
	 * @param  mixt $old_value Old value.
	 * @param  mixt $new_value New value.
	 * @return  void
	 */
	public function moove_activity_check_settings( $old_value, $new_value ) {
		$activity_settings = get_option( 'moove_post_act' );
		$post_types        = get_post_types( array( 'public' => true ) );
		$uat_content       = new Moove_Activity_Content();
		unset( $post_types['attachment'] );
		foreach ( $post_types as $post_type => $value ) {
			if ( '1' === $activity_settings[ $post_type ] || 1 === $activity_settings[ $post_type ] ) :
				$args           = array(
					'post_type'      => $post_type,
					'posts_per_page' => -1,
					'post_status'    => 'publish',
				);
				$activity_posts = new WP_Query( $args );
				if ( $activity_posts->have_posts() ) :
					while ( $activity_posts->have_posts() ) :
						$activity_posts->the_post();
						global $post;
						$uat_content->moove_save_post( $post->ID, 'enable' );
					endwhile;
				endif;
				wp_reset_postdata();
			else :
				$args           = array(
					'post_type'      => $post_type,
					'posts_per_page' => -1,
					'post_status'    => 'publish',
				);
				$activity_posts = new WP_Query( $args );
				if ( $activity_posts->have_posts() ) :
					while ( $activity_posts->have_posts() ) :
						$activity_posts->the_post();
						global $post;
						delete_post_meta( $post->ID, 'ma_data' );
					endwhile;
				endif;
				wp_reset_postdata();
			endif;
		}
	}
}
new Moove_Activity_Options();
