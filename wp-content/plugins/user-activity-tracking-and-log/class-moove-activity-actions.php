<?php
/**
 * Moove_Activity_Actions File Doc Comment
 *
 * @category  Moove_Activity_Actions
 * @package   user-activity-tracking-and-log
 * @author    Moove Agency
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Moove_Activity_Actions Class Doc Comment
 *
 * @category Class
 * @package  Moove_Activity_Actions
 * @author   Moove Agency
 */
class Moove_Activity_Actions {
	/**
	 * Global variable used in localization
	 *
	 * @var array
	 */
	public $activity_loc_data;
	/**
	 * Construct
	 */
	public function __construct() {
		$this->moove_register_scripts();

		add_action( 'wp_ajax_moove_activity_track_pageview', array( 'Moove_Activity_Controller', 'moove_track_user_access_ajax' ) );
		add_action( 'wp_ajax_nopriv_moove_activity_track_pageview', array( 'Moove_Activity_Controller', 'moove_track_user_access_ajax' ) );

		add_action( 'wp_ajax_moove_activity_track_unload', array( 'Moove_Activity_Controller', 'moove_activity_track_unload' ) );
		add_action( 'wp_ajax_nopriv_moove_activity_track_unload', array( 'Moove_Activity_Controller', 'moove_activity_track_unload' ) );

		add_action( 'uat_extend_activity_screen_nav_middle', array( &$this, 'uat_extend_et_screen_nav' ), 10, 1 );
		add_action( 'moove_activity_tab_content', array( &$this, 'moove_activity_tab_content' ), 999, 1 );
		add_action( 'moove-activity-tab-content', array( &$this, 'moove_activity_tab_content' ), 999, 1 );
		add_action( 'moove_activity_tab_extensions', array( &$this, 'moove_activity_tab_extensions' ), 5, 1 );
		add_action( 'moove_activity_filters', array( &$this, 'moove_activity_filters' ), 5, 2 );
		add_action( 'moove-activity-top-filters', array( &$this, 'moove_activity_top_filters' ) );
		add_action( 'moove_activity_check_extensions', array( &$this, 'moove_activity_check_extensions' ), 10, 2 );
		add_action( 'moove_activity_premium_section_ads', array( &$this, 'moove_activity_premium_section_ads' ) );
		add_action( 'moove_uat_filter_plugin_settings', array( &$this, 'moove_uat_filter_plugin_settings' ), 10, 1 );
		// Custom meta box for protection.
		add_action( 'add_meta_boxes', array( 'Moove_Activity_Content', 'moove_activity_meta_boxes' ) );
		add_action( 'save_post', array( 'Moove_Activity_Content', 'moove_save_post' ) );
		add_action( 'moove_activity_check_tab_content', array( &$this, 'moove_activity_check_tab_content' ), 10, 2 );

		add_action( 'uat_licence_action_button', array( 'Moove_Activity_Content', 'uat_licence_action_button' ), 10, 2 );
		add_action( 'uat_get_alertbox', array( 'Moove_Activity_Content', 'uat_get_alertbox' ), 10, 3 );
		add_action( 'uat_licence_input_field', array( 'Moove_Activity_Content', 'uat_licence_input_field' ), 10, 2 );
		add_action( 'uat_premium_update_alert', array( 'Moove_Activity_Content', 'uat_premium_update_alert' ) );
		add_action( 'uat_activity_log_restriction_content', array( 'Moove_Activity_Content', 'uat_activity_log_restriction_content' ), 10, 1 );
		add_action( 'uat_log_settings_restriction_content', array( 'Moove_Activity_Content', 'uat_log_settings_restriction_content' ), 10, 1 );

		add_action( 'uat_activity_screen_options_extension', array( &$this, 'uat_activity_screen_options_extension' ), 10, 1 );

		/**
		 * Version incompatibility & deprecation notice
		 */
		add_action(
			'uat_premium_update_alert',
			function() {
				if ( defined( 'MOOVE_UAT_PREMIUM_VERSION' ) && floatval( MOOVE_UAT_PREMIUM_VERSION ) < 2 ) :
					?>
						<div class="uat_license_log_alert">
							<div class="uat-admin-alert uat-admin-alert-error">
								<h3 style="margin: 5px 0 10px; color: inherit;">New version of the <strong style="color: #000">User Activity Tracking and Log - Premium </strong> Plugin is available.</h3>
								<p>Your current version is no longer supported and may have compatibility issues. Please update to the <a href="<?php echo esc_url( admin_url( '/plugins.php' ) ); ?>" target="_blank">latest version</a>.</p>
							</div>
							<!-- .uat-admin-alert uat-admin-alert-error -->
						</div>
						<!--  .uat-cookie-alert -->
					<?php
				endif;
			}
		);

		/**
		 * Legacy "Activity Settings" page redirect in admin area to new location.
		 */
		add_action(
			'admin_menu',
			function() {
				if ( is_admin() && isset( $_GET['page'] ) ) : // phpcs:ignore
					$page = sanitize_text_field( wp_unslash( $_GET['page'] ) ); // phpcs:ignore
					if ( 'moove-activity' === $page ) :
						$tab        = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : ''; // phpcs:ignore
						$plugin_url = admin_url( '/admin.php?page=moove-activity-log' );
						$plugin_url = $tab ? add_query_arg( 'tab', $tab, $plugin_url ) : $plugin_url;
						wp_safe_redirect( $plugin_url, 307 );
						exit();
					endif;
				endif;
			}
		);

		add_action( 'uat_licence_key_visibility', array( &$this, 'uat_licence_key_visibility_hide' ), 10, 1 );

		$uat_default_content = new Moove_Activity_Content();
		$option_key          = $uat_default_content->moove_uat_get_key_name();
		$uat_key             = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );

		if ( $uat_key && ! isset( $uat_key['deactivation'] ) ) :
			do_action( 'uat_plugin_loaded' );
		endif;

		add_action( 'admin_menu', array( 'Moove_Activity_Controller', 'moove_register_activity_menu_page' ) );
		add_action( 'save_post', array( 'Moove_Activity_Controller', 'moove_track_user_access_save_post' ), 100 );
		add_action( 'wp_ajax_moove_activity_save_user_options', array( 'Moove_Activity_Controller', 'moove_activity_save_user_options' ) );
	}

	/**
	 * Licence key asterisks hide in admin area
	 *
	 * @param string $key Licence key.
	 */
	public static function uat_licence_key_visibility_hide( $key ) {
		if ( $key ) :
			$_key = explode( '-', $key );
			if ( $_key && is_array( $_key ) ) :
				$_hidden_key = array();
				$key_count   = count( $_key );
				for ( $i = 0; $i < $key_count; $i++ ) :
					if ( 0 === $i || ( $key_count - 1 ) === $i ) :
						$_hidden_key[] = $_key[ $i ];
					else :
						$_hidden_key[] = '****';
					endif;
				endfor;
				$key = implode( '-', $_hidden_key );
			endif;
		endif;
		return $key;
	}

	/**
	 * Event Tracking Navigation Menu
	 *
	 * @param string $active_tab Active Tab slug.
	 *
	 * @return void
	 */
	public static function uat_extend_et_screen_nav( $active_tab ) {

		$tab_data = array(
			array(
				'name'  => esc_html__( 'Event Tracking Log', 'user-activity-tracking-and-log' ),
				'icon'  => 'dashicons dashicons-chart-line',
				'class' => 'nav-tab nav-tab-separator nav-tab-first',
				'slug'  => 'et-log',
			),
			array(
				'name'  => esc_html__( 'Triggers Setup', 'user-activity-tracking-and-log' ),
				'icon'  => '',
				'class' => 'nav-tab nav-cc-premium nav-tab-disabled',
				'slug'  => 'et-triggers',
			),
			array(
				'name'  => esc_html__( 'Triggers Log', 'user-activity-tracking-and-log' ),
				'icon'  => '',
				'class' => 'nav-tab nav-cc-premium nav-tab-disabled',
				'slug'  => 'et-triggers-log',
			),
			array(
				'name'  => esc_html__( 'Users', 'user-activity-tracking-and-log' ),
				'icon'  => '',
				'class' => 'nav-tab nav-cc-premium nav-tab-disabled',
				'slug'  => 'et-users',
			),

			array(
				'name'  => esc_html__( 'Help', 'user-activity-tracking-and-log' ),
				'icon'  => 'dashicons dashicons-editor-help',
				'class' => 'nav-tab nav-tab-dark',
				'slug'  => 'et-help',
			),

			array(
				'name'  => esc_html__( 'Video Tutorial', 'user-activity-tracking-and-log' ),
				'icon'  => 'dashicons dashicons-format-video',
				'class' => 'nav-tab nav-tab-dark',
				'slug'  => 'et-video-tutorial',
			),
		);

		foreach ( $tab_data as $tab ) :
			ob_start();
			?>
				<a href="<?php echo esc_url( admin_url( '/admin.php?page=moove-activity-log&tab=' . $tab['slug'] ) ); ?>" class="<?php echo isset( $tab['class'] ) ? esc_attr( $tab['class'] ) : ''; ?> <?php echo $active_tab === $tab['slug'] ? 'nav-tab-active' : ''; ?>">
					<?php if ( isset( $tab['icon'] ) && $tab['icon'] ) : ?>
						<i class="<?php echo esc_attr( $tab['icon'] ); ?>"></i>
					<?php endif; ?>
					<?php echo esc_html( $tab['name'] ); ?>
				</a>
			<?php
			$content = ob_get_clean();
			echo apply_filters( 'moove_activity_check_extensions', $content, $tab['slug'] ); // phpcs:ignore
		endforeach;
	}

	/**
	 * Default values for User Screen options
	 *
	 * @param array $screen_options Screen options array.
	 */
	public static function uat_activity_screen_options_extension( $screen_options = array() ) {
		if ( ! function_exists( 'moove_uat_addon_get_plugin_dir' ) ) :
			$screen_options                       = is_array( $screen_options ) ? $screen_options : array();
			$screen_options['moove-activity-dtf'] = 'b';
		endif;
		return $screen_options;
	}

	/**
	 * Disabled post type visiblity
	 *
	 * @param array $global_settings Global settings.
	 */
	public function moove_uat_filter_plugin_settings( $global_settings ) {
		$show_disabled = apply_filters( 'uat_show_disabled_cpt', false );
		if ( $show_disabled ) :
			$post_types = get_post_types( array( 'public' => true ) );
			unset( $post_types['attachment'] );
			foreach ( $post_types as $post_type ) :
				if ( isset( $global_settings[ $post_type ] ) ) :
					$global_settings[ $post_type ] = '1';
				endif;
			endforeach;
		endif;
		return $global_settings;
	}

	/**
	 * Top filters hook
	 */
	public function moove_activity_top_filters() {
		echo '';
	}

	/**
	 * Premium restriction if add-on is not installed
	 */
	public function moove_activity_premium_section_ads() {

		if ( class_exists( 'Moove_Activity_Addon_View' ) ) :
			$add_on_view  = new Moove_Activity_Addon_View();
			$slug         = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : false; // phpcs:ignore
			$view_content = false;

			if ( function_exists( 'uat_addon_get_plugin_directory' ) ) :
				if ( file_exists( uat_addon_get_plugin_directory() . '/views/moove/admin/settings/' . $slug . '.php' ) ) :
					$view_content = true;
				endif;
			else :
				$add_on_view  = new Moove_Activity_Addon_View();
				if ( $slug === 'activity-screen-settings' && defined( 'MOOVE_UAT_PREMIUM_VERSION' ) && MOOVE_UAT_PREMIUM_VERSION < '2.2' ) :
					$slug = 'activity_screen_settings';
				elseif ( $slug === 'tracking-settings' && defined( 'MOOVE_UAT_PREMIUM_VERSION' ) && MOOVE_UAT_PREMIUM_VERSION < '2.2' ) :
					$slug = 'tracking_settings';
				endif;
				$view_content = $add_on_view->load( 'moove.admin.settings.' . $slug, array() );
			endif;

			if ( ! $view_content && $slug && 'help' !== $slug ) :
				?>
				<div class="uat-locked-section">
					<span>
					<i class="dashicons dashicons-lock"></i>
					<h4>This feature is not supported in this version of the Premium Add-on.</h4>
					<p><strong><a href="<?php echo esc_url( admin_url( 'admin.php?page=moove-activity-log&tab=licence' ) ); ?>" class="uat_admin_link">Activate your licence</a> to download the latest version of the Premium Add-on.</strong></p>
					<p class="uat_license_info">Don’t have a valid licence key yet? <br><a href="<?php echo esc_url( MOOVE_SHOP_URL ); ?>/my-account" target="_blank" class="uat_admin_link">Login to your account</a> to generate the key or <a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log" class="uat_admin_link" target="_blank">buy a new licence here</a>.</p>
					<br />
					<a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log" target="_blank" class="plugin-buy-now-btn">Buy Now</a>
					</span>
				</div>
				<!--  .uat-locked-section -->
				<?php
			endif;
		else :
			?>
			<div class="muat-locked-section">
				<span>
					<i class="dashicons dashicons-lock"></i>
					<h4>This feature is part of the Premium Add-on</h4>
					<?php
					$uat_default_content = new Moove_Activity_Content();
					$option_key          = $uat_default_content->moove_uat_get_key_name();
					$uat_key             = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );
					?>
					<?php if ( isset( $uat_key['deactivation'] ) || $uat_key['activation'] ) : ?>
						<p><strong><a href="<?php echo esc_url( admin_url( '/admin.php?page=moove-activity-log&tab=licence' ) ); ?>" class="uat_admin_link">Activate your licence</a> or <a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log" class="uat_admin_link" target="_blank">buy a new licence here</a></strong>.</p>
						<?php else : ?>
						<p><strong>Do you have a licence key? <br />Insert your license key to the "<a href="<?php echo esc_url( admin_url( '/admin.php?page=moove-activity-log&tab=licence' ) ); ?>" class="uat_admin_link">Licence Manager</a>" and activate it.</strong></p>
					<?php endif; ?>
					<br />

					<a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log" target="_blank" class="plugin-buy-now-btn">Buy Now</a>
				</span>

			</div>
			<!--  .uat-locked-section -->
			<?php
		endif;
	}

	/**
	 * Filter applied to $content returned by View controller, trimmed version
	 *
	 * @param string $content Content.
	 * @param string $slug Active tab slug.
	 */
	public function moove_activity_check_extensions( $content, $slug ) {
		$return = $content;
		if ( class_exists( 'Moove_Activity_Addon_View' ) ) :
			if ( function_exists( 'uat_addon_get_plugin_directory' ) ) :
				;
				if ( file_exists( uat_addon_get_plugin_directory() . '/views/moove/admin/settings/' . $slug . '.php' ) ) :
					$return = '';
				endif;
			else :
				$add_on_view  = new Moove_Activity_Addon_View();
				$view_content = $add_on_view->load( 'moove.admin.settings.' . $slug, array() );
				if ( $view_content ) :
					$return = '';
				endif;
			endif;
		endif;
		return $return;
	}

	/**
	 * Filter applied to $content returned by View controller, non-trimmed version
	 *
	 * @param string $content Content.
	 * @param string $slug Active tab slug.
	 */
	public function moove_activity_check_tab_content( $content, $slug ) {
		$_return = $content;
		if ( class_exists( 'Moove_Activity_Addon_View' ) ) :
			$add_on_view = new Moove_Activity_Addon_View();
			if ( function_exists( 'uat_addon_get_plugin_directory' ) ) :
				if ( file_exists( uat_addon_get_plugin_directory() . '/views/moove/admin/settings/' . $slug . '.php' ) ) :
					$_return = '';
				endif;
			else :
				$view_content = $add_on_view->load( 'moove.admin.settings.' . $slug, array() );
				if ( $view_content ) :
					$_return = '';
				endif;
			endif;
		endif;
		return $_return;
	}

	/**
	 * Tab content filter
	 *
	 * @param string $data Data.
	 * @param string $active_tab Active tab slug.
	 */
	public function moove_activity_tab_content( $data, $active_tab = '' ) {
		$uat_view = new Moove_Activity_View();
		$content  = $uat_view->load( 'moove.admin.settings.' . $data['tab'], true );
		echo apply_filters( 'moove_activity_check_tab_content', $content, $data['tab'] ); // phpcs:ignore
	}

	/**
	 * Activity navigation extensions
	 *
	 * @param string $active_tab Active tab.
	 */
	public function moove_activity_tab_extensions( $active_tab ) {
		$tab_data = array(
			array(
				'name' => esc_html__( 'Activity Log Screen Options', 'user-activity-tracking-and-log' ),
				'slug' => 'activity-screen-settings',
			),
			array(
				'name' => esc_html__( 'User Tracking Settings', 'user-activity-tracking-and-log' ),
				'slug' => 'tracking-settings',
			),
			array(
				'name' => esc_html__( 'Permissions', 'user-activity-tracking-and-log' ),
				'slug' => 'permissions',
			),
			array(
				'name' => esc_html__( 'Advanced Settings', 'user-activity-tracking-and-log' ),
				'slug' => 'advanced-settings',
			),
		);
		foreach ( $tab_data as $tab ) :
			ob_start();
			?>
				<a href="<?php echo esc_url( admin_url( '/admin.php?page=moove-activity-log&tab=' . $tab['slug'] ) ); ?>" class="nav-tab nav-cc-premium nav-tab-disabled <?php echo $active_tab === $tab['slug'] ? 'nav-tab-active' : ''; ?>">
					<?php echo esc_html( $tab['name'] ); ?>
				</a>
			<?php
			$content = ob_get_clean();
			echo apply_filters( 'moove_activity_check_extensions', $content, $tab['slug'] ); // phpcs:ignore
		endforeach;
	}

	/**
	 * Register Front-end / Back-end scripts
	 *
	 * @return void
	 */
	public function moove_register_scripts() {
		if ( is_admin() ) :
			add_action( 'admin_enqueue_scripts', array( &$this, 'moove_activity_admin_scripts' ) );
		else :
			add_action( 'wp_enqueue_scripts', array( &$this, 'moove_frontend_activity_scripts' ) );
		endif;
	}

	/**
	 * Activity filter hook
	 *
	 * @param string $filters Filters.
	 * @param string $content Content.
	 */
	public function moove_activity_filters( $filters, $content ) {
		echo $filters; // phpcs:ignore
	}

	/**
	 * Register global variables to head, AJAX, Form validation messages
	 *
	 * @param  string $ascript The registered script handle you are attaching the data for.
	 * @return void
	 */
	public function moove_localize_script( $ascript ) {
		$activity_loc_data       = array(
			'activityoptions' => get_option( 'moove_activity-options' ),
			'referer'         => esc_url( uat_get_referer() ),
			'ajaxurl'         => admin_url( 'admin-ajax.php' ),
			'post_id'         => get_the_ID(),
			'is_page'         => is_page(),
			'is_single'       => is_single(),
			'current_user'    => get_current_user_id(),
			'referrer'        => esc_url( uat_get_referer() ),
			'extras'          => wp_json_encode( array() ),
		);
		$this->activity_loc_data = apply_filters( 'moove_uat_extend_loc_data', $activity_loc_data );

		wp_localize_script( $ascript, 'moove_frontend_activity_scripts', $this->activity_loc_data );
	}

	/**
	 * Registe FRONT-END Javascripts and Styles
	 *
	 * @return void
	 */
	public function moove_frontend_activity_scripts() {
		wp_enqueue_script( 'moove_activity_frontend', plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/js/moove_activity_frontend.js', array( 'jquery' ), MOOVE_UAT_VERSION, true );
		$this->moove_localize_script( 'moove_activity_frontend' );
	}

	/**
	 * Registe BACK-END Javascripts and Styles
	 *
	 * @return void
	 */
	public function moove_activity_admin_scripts() {
		wp_enqueue_script( 'moove_activity_backend', plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/js/moove_activity_backend.js', array( 'jquery' ), MOOVE_UAT_VERSION, true );
		wp_enqueue_style( 'moove_activity_backend', plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/css/moove_activity_backend.css', '', MOOVE_UAT_VERSION );
	}
}
new Moove_Activity_Actions();

