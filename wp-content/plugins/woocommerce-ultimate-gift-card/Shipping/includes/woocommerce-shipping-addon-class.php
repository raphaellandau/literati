<?php

/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'MWB_WGM_Shipping_Card_Product' ) ) {

	/**
	 * This is class for managing order status and other functionalities .
	 *
	 * @name    MWB_WGM_Shipping_Card_Product
	 * @category Class
	 * @author   makewebbetter <webmaster@makewebbetter.com>
	 */

	class MWB_WGM_Shipping_Card_Product {

		/**
		 * This is construct of class where all action and filter is defined
		 *
		 * @name __construct
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function __construct() {
			 $this->shippingtab = '';
			$this->shippingtabactive = false;
			$this->licensetab = '';
			$this->licensetabactive = false;
			$this->addontab = '';
			$this->addonactive = false;
			add_action( 'mwb_wgm_setting_tab', array( $this, 'mwb_wgm_shipping_setting_tab' ) );
			add_action( 'mwb_wgm_setting_tab_active', array( $this, 'mwb_wgm_shipping_setting_tab_active' ) );
			add_action( 'mwb_wgm_setting_tab_html', array( $this, 'mwb_wgm_shipping_setting_tab_html' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'mwb_wgm_shipping_enqueue_scripts' ), 10, 1 );
		}
		/**
		 * This is function where scripts are enqueued
		 *
		 * @name mwb_wgm_shipping_enqueue_scripts
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_shipping_enqueue_scripts() {
			$screen = get_current_screen();
			if ( isset( $screen->id ) ) {
				$pagescreen = $screen->id;
				if ( $pagescreen == 'woocommerce_page_mwb-wgc-setting' && isset( $_GET['tab'] ) && $_GET['tab'] == 'shipping' ) {
					wp_register_script( 'mwb_wgm_shipping', MWB_WGM_SD_URL . 'assets/js/woocommerce-ultimate-gift-card-shipping.js' );
					wp_enqueue_script( 'mwb_wgm_shipping' );
				}
			}
		}

		/**
		 * This is function is used to add shipping section in settings
		 *
		 * @name mwb_wgm_shipping_setting_tab
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_shipping_setting_tab() {
			$mwb_wgm_plugin_verified_pre = get_option( 'mwb_wgm_plugin_verified', false );
			?>
				<a class="nav-tab <?php echo $this->shippingtab; ?>" href="?page=mwb-wgc-setting&tab=shipping"><?php _e( 'Delivery Method', MWB_WGM_SD_DOM ); ?></a>
				<?php
				$host_server = $_SERVER['HTTP_HOST'];
				if ( strpos( $host_server, 'www.' ) == 0 ) {

					$host_server = str_replace( 'www.', '', $host_server );
				}
				if ( ! get_option( 'mwb_wgm_plugin_verified' . $host_server, $mwb_wgm_plugin_verified_pre ) ) {
					?>
					<a class="nav-tab <?php echo $this->licensetab; ?>" href="?page=mwb-wgc-setting&tab=validate_license"><?php _e( 'Add License', MWB_WGM_SD_DOM ); ?></a>
				<?php } ?>
				<a class="nav-tab <?php echo $this->addontab; ?>" href="?page=mwb-wgc-setting&tab=addon"><?php _e( 'New Addon', MWB_WGM_SD_DOM ); ?></a>
				<?php
		}
		/**
		 * This is function is used to add active class to shipping section in settings
		 *
		 * @name mwb_wgm_shipping_setting_tab_active
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_shipping_setting_tab_active() {
			if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
				if ( $tab == 'shipping' ) {
					$this->shippingtab = 'nav-tab-active';
					$this->shippingtabactive = true;
				}
				if ( $tab == 'validate_license' ) {
					$this->licensetab = 'nav-tab-active';
					$this->licensetabactive = true;
				}
				if ( $tab == 'addon' ) {
					$this->addontab = 'nav-tab-active';
					$this->addonactive = true;
				}
			}
		}
		/**
		 * This is function is used to add template to shipping section in settings
		 *
		 * @name mwb_wgm_shipping_setting_tab_html
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wgm_shipping_setting_tab_html() {
			if ( $this->shippingtabactive == true ) {
				include_once MWB_WGM_SD_DIRPATH . '/admin/shipping-setting.php';
			}
			if ( $this->licensetabactive == true ) {
				include_once MWB_WGM_SD_DIRPATH . '/admin/license-setting.php';
			}
			if ( $this->addonactive == true ) {
				include_once MWB_WGM_SD_DIRPATH . '/admin/addon.php';
			}
		}
	}
	new MWB_WGM_Shipping_Card_Product();
}
