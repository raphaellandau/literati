<?php
namespace WooCommerce_Custom_Thank_You_Pages\Admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Admin settings.
 *
 * Handle functions for admin settings page.
 *
 * @author		Jeroen Sormani
 * @version		1.0.0
 */
class Settings {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init();
	}


	/**
	 * Initialize class.
	 *
	 * Initialize the class components/hooks on admin_init so its called once.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Save settings
		add_action( 'woocommerce_settings_save_checkout', array( $this, 'update_options' ) );

		// Add section
		add_action( 'woocommerce_get_sections_checkout', array( $this, 'add_section' ) );

		// Output settings in section
		add_action( 'woocommerce_settings_checkout', array( $this, 'output_settings' ) );

		// Save the proper value
		add_filter( 'woocommerce_admin_settings_sanitize_option_wcctyp_global_custom_thank_you_page', array( $this, 'save_sanitize_global_setting' ), 10, 4 );
	}


	/**
	 * Settings page array.
	 *
	 * Get settings page fields array.
	 *
	 * @since 1.0.0
	 */
	public function get_settings() {
		$value          = get_option( 'wcctyp_global_custom_thank_you_page' );
		$selected_value = \WooCommerce_Custom_Thank_You_pages\is_url( $value ) ? esc_url( $value ) : absint( $value );
		$selected_text  = \WooCommerce_Custom_Thank_You_pages\is_url( $value ) ? $selected_value : get_the_title( $selected_value );

		$settings = apply_filters( 'WCCTYP/admin/settings', array(

			array(
				'title' => __( 'Custom Thank You Pages', 'woocommerce-custom-thank-you-pages' ),
				'type'  => 'title',
			),

			array(
				'title'    => __( 'Thank you page navigation', 'woocommerce-custom-thank-you-pages' ),
				'desc'     => __( 'Enable navigation when multiple products with custom pages are configured.', 'woocommerce-custom-thank-you-pages' ),
				'id'       => 'custom-thank-you-page-navigation',
				'default'  => false,
				'type'     => 'checkbox',
				'autoload' => true,
			),

			array(
				'title'             => __( 'Global custom thank you page', 'woocommerce-custom-thank-you-pages' ),
				'desc'              => __( 'Set a global custom thank you page. This can also be overridden per product (variation).', 'woocommerce-custom-thank-you-pages' ),
				'id'                => 'wcctyp_global_custom_thank_you_page',
				'class'             => 'wcctyp-post-search',
				'css'               => 'min-width:300px; min-height: 25px;',
				'default'           => '',
				'type'              => 'select',
				'desc_tip'          =>  true,
				'custom_attributes' => array(
					'placeholder'   =>  __( 'No global custom thank you page', 'woocommerce-custom-thank-you-pages' ),
					'data-selected' => $selected_text,
				),
				'options' => array(
					'' => '',
					$selected_value => $selected_text,
				),
			),

			array(
				'type' => 'sectionend',
			),

		) );

		return $settings;
	}


	/**
	 * Save settings.
	 *
	 * Save settings based on WooCommerce save_fields() method.
	 *
	 * @since 1.0.0
	 */
	public function update_options() {
		global $current_section;

		if ( $current_section == 'thank-you-pages' ) {
			\WC_Admin_Settings::save_fields( $this->get_settings() );
		}
	}


	/**
	 * Add shipping section.
	 *
	 * Add a new 'extra shipping options' section under the shipping tab.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $sections List of existing shipping sections.
	 * @return array           List of modified shipping sections.
	 */
	public function add_section( $sections ) {
		$sections['thank-you-pages'] = __( 'Thank you pages', 'woocommerce-custom-thank-you-pages' );

		return $sections;
	}


	/**
	 * Output settings.
	 *
	 * Add the settings to the Extra Shipping Options shipping section.
	 *
	 * @since 1.0.0
	 *
	 * @param string $current_section Slug of the current section
	 */
	public function output_settings( $current_section ) {
		global $current_section;

		if ( 'thank-you-pages' === $current_section ) {
			$settings = $this->get_settings();
			\WC_Admin_Settings::output_fields( $settings );
		}
	}


	/**
	 * Override default save sanitize.
	 *
	 * Override is needed in order to allow a variable value that is not
	 * pre-known in the 'options' argument. This is due to the
	 * AJAX search / free URL input features.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Current value sanitized by Woo (invalid in this case).
	 * @param string $option Option ID / name.
	 * @param mixed $raw_value Raw value.
	 * @return array|string Sanitized value.
	 */
	function save_sanitize_global_setting( $value, $option, $raw_value ) {
		return wc_clean( $raw_value );
	}
}
