<?php
/**
 * Plugin Name: Yaad WooCommerce Payment Gateway
 * Plugin URI: https://www.directpay.co.il/
 * Description: WooCommerce payment gateway for Yaad
 * Version: 1.2.2
 * Author: directpay.co.il
 * Author URI: https://www.directpay.co.il/
 * WC requires at least: 3.3.0
 * WC tested up to: 5.0.0
 * Tested up to: 5.6
 * Text Domain: yaad_for_woo
 * Domain Path: /lang
 */

if (!defined('ABSPATH')) {
    //Exit if accessed directly
    exit;
}

define( 'WC_YAAD_VERSION', '1.2.2' );
$plugin = plugin_basename( __FILE__ );
add_filter("plugin_action_links_$plugin", 'yaad_add_settings_link');
add_action('plugins_loaded', 'woocommerce_yaad_init');
add_action('admin_enqueue_scripts', 'yaad_for_woo_scripts');

function yaad_add_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=yaad_for_woo">'.__('Settings','yaad_for_woo').'</a>';
  	array_push( $links, $settings_link );
  	return $links;
}

function woocommerce_yaad_init() {
    if (!class_exists('WC_Payment_Gateway')){
        return;
    }

    load_plugin_textdomain('yaad_for_woo', false, dirname(plugin_basename(__FILE__)) . '/lang/');
    add_filter('woocommerce_payment_gateways', 'woocommerce_yaad_add_gateway');
    
    require_once('inc/config.php');
    require_once('inc/class-woocommerce-yaad.php');
    require_once('inc/capture_payment.php');
    require_once('inc/yaad_for_woo_panel.php');
    require_once('inc/checklicence.php');
    if (!class_exists('wp_auto_update')) {
        require_once ('inc/wp_autoupdate.php');
    }
    require_once ('inc/version.php');
}

function woocommerce_yaad_add_gateway($methods) {
    $methods[] = 'WC_Yaad_Gateway';
    return $methods;
}

// Plugin CSS and JS
function yaad_for_woo_scripts() {
    wp_enqueue_style('yaad-for-woo', plugins_url('/css/style.css', __FILE__));
    if(is_rtl()){
        wp_enqueue_style('yaad-for-woo-rtl', plugins_url('/css/rtl.css', __FILE__));
    }
}

global $wpdb;
$table_name = $wpdb->prefix . 'yaad_for_woo_licence';
$module = get_option($table_name);

if (isset($module['active']) && $module['active'] == 0) {
    add_action('admin_notices', 'yaad_for_woo_notice');
}

function yaad_for_woo_notice() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'yaad_for_woo_licence';
    $module = get_option($table_name);
    echo '<div class="notice notice-error is-dismissible"><p>' . $module['message'] . '</p></div>';
}
?>