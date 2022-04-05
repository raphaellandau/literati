<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$generaltab = '';
$producttab = '';
$tab = '';
$emailtab = '';
$offlinetab = '';
$exporttab = '';
$additionaltab = '';
$discounttab = '';
$thankyouordertab = '';
$redeem_tab = '';
$exportcoupontabactive = false;
$generaltabactive = false;
$producttabactive = false;
$emailtabactive = false;
$offlinepaymenttabactive = false;
$additionalsettingtabactive = false;
$discounttabactive = false;
$thankyouordertabactive = false;
$redeem_tab_active = false;

if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
	$tab = $_GET['tab'];
	if ( $tab == 'general-setting' ) {
		$generaltab = 'nav-tab-active';
		$generaltabactive = true;
	}
	if ( $tab == 'product-setting' ) {
		$producttab = 'nav-tab-active';
		$producttabactive = true;
	}
	if ( $tab == 'email-setting' ) {
		$emailtab = 'nav-tab-active';
		$emailtabactive = true;
	}

	if ( $tab == 'offline-giftcard' ) {
		$offlinetab = 'nav-tab-active';
		$offlinepaymenttabactive = true;
	}
	if ( $tab == 'export-coupon' ) {
		$exporttab = 'nav-tab-active';
		$exportcoupontabactive = true;
	}
	if ( $tab == 'other-additional-setting' ) {
		$additionaltab = 'nav-tab-active';
		$additionalsettingtabactive = true;
	}
	if ( $tab == 'discount-tab' ) {
		$discounttab = 'nav-tab-active';
		$discounttabactive = true;
	}
	if ( $tab == 'thankyou-tab' ) {
		$thankyouordertab = 'nav-tab-active';
		$thankyouordertabactive = true;
	}
	if ( $tab == 'redeem_section' ) {
		$redeem_tab = 'nav-tab-active';
		$redeem_tab_active = true;
	}

	do_action( 'mwb_wgm_setting_tab_active' );
}
if ( empty( $tab ) ) {
	$generaltab = 'nav-tab-active';
	$generaltabactive = true;
}
$host_server = $_SERVER['HTTP_HOST'];
if ( strpos( $host_server, 'www.' ) == 0 ) {

	$host_server = str_replace( 'www.', '', $host_server );
}
$mwb_wgm_activated_time = get_option( 'mwb_wgm_activation_date_time', false );
$mwb_wgm_after_month = strtotime( '+ 30 days', $mwb_wgm_activated_time );
$mwb_wgm_currenttime = current_time( 'timestamp' );
$mwb_wgm_time_difference = $mwb_wgm_after_month - $mwb_wgm_currenttime;
$mwb_wgm_days_left = floor( $mwb_wgm_time_difference / ( 60 * 60 * 24 ) );
$mwb_wgm_plugin_verified_pre = get_option( 'mwb_wgm_plugin_verified', false );
if ( ! get_option( 'mwb_wgm_plugin_verified' . $host_server, $mwb_wgm_plugin_verified_pre ) ) {
	$mwb_wgm_msg = __( 'Activate the License Key before ', 'woocommerce-ultimate-gift-card' );
	?>
	<div class="update-nag">
		<?php echo '<strong>' . $mwb_wgm_msg; ?><a href="<?php get_admin_url(); ?>admin.php?page=mwb-wgc-setting&tab=validate_license"><?php echo $mwb_wgm_days_left; ?></a><?php _e( ' days of activation - You might risk losing data by then, and you will not be able to use the plugin !', 'woocommerce-ultimate-gift-card' ); ?><a href="<?php get_admin_url(); ?>admin.php?page=mwb-wgc-setting&tab=validate_license"><?php _e( ' Activate now ', 'woocommerce-ultimate-gift-card' ); ?></strong></a>
	</div>
	<?php
}
?>
<div class="wrap woocommerce" id="mwb_wgm_setting_wrapper">
	<div style="display: none;" class="loading-style-bg" id="mwb_wgm_loader">
		<img src="<?php echo MWB_WGM_URL; ?>/assets/images/loading.gif">
	</div>
	<form enctype="multipart/form-data" action="" id="mainform" method="post">
		<h1 class="mwb_wgm_setting_title"><?php _e( 'Giftcard Settings', 'woocommerce-ultimate-gift-card' ); ?></h1>
		<br/>
		<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
			<a class="nav-tab <?php echo $generaltab; ?>" href="?page=mwb-wgc-setting&tab=general-setting"><?php _e( 'General', 'woocommerce-ultimate-gift-card' ); ?></a>
			<a class="nav-tab <?php echo $producttab; ?>" href="?page=mwb-wgc-setting&tab=product-setting"><?php _e( 'Products', 'woocommerce-ultimate-gift-card' ); ?></a>
			<a class="nav-tab <?php echo $emailtab; ?>" href="?page=mwb-wgc-setting&tab=email-setting"><?php _e( 'Email Template', 'woocommerce-ultimate-gift-card' ); ?></a>
			<a class="nav-tab <?php echo $offlinetab; ?>" href="?page=mwb-wgc-setting&tab=offline-giftcard"><?php _e( 'Offline Giftcard', 'woocommerce-ultimate-gift-card' ); ?></a>
			<a class="nav-tab <?php echo $exporttab; ?>" href="?page=mwb-wgc-setting&tab=export-coupon"><?php _e( 'Import/Export', 'woocommerce-ultimate-gift-card' ); ?></a>
			<a class="nav-tab <?php echo $additionaltab; ?>" href="?page=mwb-wgc-setting&tab=other-additional-setting"><?php _e( 'Other Setting', 'woocommerce-ultimate-gift-card' ); ?></a>
			<a class="nav-tab <?php echo $discounttab; ?>" href="?page=mwb-wgc-setting&tab=discount-tab"><?php _e( 'Discount', 'woocommerce-ultimate-gift-card' ); ?></a>
			<a class="nav-tab <?php echo $thankyouordertab; ?>" href="?page=mwb-wgc-setting&tab=thankyou-tab"><?php _e( 'Thank You Order', 'woocommerce-ultimate-gift-card' ); ?></a>	
			<a class="nav-tab <?php echo $redeem_tab; ?>" href="?page=mwb-wgc-setting&tab=redeem_section"><?php _e( 'Gift Card Redeem', 'woocommerce-ultimate-gift-card' ); ?><span class="mwb_gw_new_badge"><?php esc_html_e( 'New', 'woocommerce-ultimate-gift-card' ); ?></span></a>
			<?php
			do_action( 'mwb_wgm_setting_tab' );
			?>
				
		</nav>
		<?php
		if ( $generaltabactive == true ) {
			include_once MWB_WGM_DIRPATH . '/admin/template/general-setting.php';
		}
		if ( $producttabactive == true ) {
			include_once MWB_WGM_DIRPATH . '/admin/template/product-setting.php';
		}
		if ( $emailtabactive == true ) {
			include_once MWB_WGM_DIRPATH . '/admin/template/email-setting.php';
		}
		if ( $offlinepaymenttabactive == true ) {
			include_once MWB_WGM_DIRPATH . '/admin/template/offline-gift.php';
		}
		if ( $exportcoupontabactive == true ) {
			include_once MWB_WGM_DIRPATH . '/admin/template/export-coupon.php';
		}
		if ( $additionalsettingtabactive == true ) {
			include_once MWB_WGM_DIRPATH . '/admin/template/other-additional-setting.php';
		}
		if ( $discounttabactive == true ) {
			include_once MWB_WGM_DIRPATH . '/admin/template/discount-setting.php';
		}
		if ( $thankyouordertabactive == true ) {
			include_once MWB_WGM_DIRPATH . '/admin/template/thankyou-order-setting.php';
		}
		if ( $redeem_tab_active == true ) {
			include_once MWB_WGM_DIRPATH . '/admin/template/redeem-giftcard-settings.php';
		}
		do_action( 'mwb_wgm_setting_tab_html' );
		?>
		
	</form>
</div>
