<?php
/**
 * Moove_Controller File Doc Comment
 *
 * @category  Moove_Controller
 * @package   user-activity-tracking-and-log
 * @author    Moove Agency
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Moove_Controller Class Doc Comment
 *
 * @category Class
 * @package  Moove_Controller
 * @author   Moove Agency
 */
class Moove_Activity_Controller {
	/**
	 * Construct function
	 */
	public function __construct() {
	}

	/**
	 * Checking if database exists
	 *
	 * @return bool
	 */
	public static function moove_importer_check_database() {
		$has_database = get_option( 'moove_importer_has_database' ) ? true : false;
		return $has_database;
	}

	/**
	 * User options save
	 */
	public static function moove_activity_save_user_options() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_key( wp_unslash( $_POST['nonce'] ) ) : false;

		if ( ! wp_verify_nonce( $nonce, 'uat_screen_settings_ajax_nonce_field' ) ) :
			die( 'Invalid request!' );
		endif;

		if ( isset( $_POST['form_data'] ) ) :
			$form_data = wp_kses( wp_unslash( $_POST['form_data'] ), array() );
			$form_data = htmlspecialchars_decode( $form_data );
			parse_str( $form_data, $user_options );
			$sanitized_options = array();
			if ( is_array( $user_options ) ) :
				foreach ( $user_options as $uo_key => $uo_value ) :
					if ( $uo_key !== 'uat_geo_status' && $uo_key !== 'uat_geo_status_h' ) :
						$sanitized_options[ sanitize_key( wp_unslash( $uo_key ) ) ] = is_array( $uo_value ) ? $uo_value : sanitize_text_field( wp_unslash( $uo_value ) );
					endif;
				endforeach;
				if ( isset( $user_options['uat_geo_status_h'] ) ) :
					$gs = isset( $user_options['uat_geo_status'] ) ? 1 : 0;
					update_option( 'uat_geo_status', $gs );
				endif;
			endif;

			$user_id = intval( $sanitized_options['wp_user_id'] );
			if ( $user_id && $sanitized_options ) :
				update_user_meta( $user_id, 'moove_activity_screen_options', $sanitized_options );
			endif;
		endif;

		die();
	}

	/**
	 * Plugin details from WordPress.org repository
	 *
	 * @param string $plugin_slug Plugin slug.
	 */
	public static function get_plugin_details( $plugin_slug = '' ) {
		$plugin_return   = false;
		$wp_repo_plugins = '';
		$wp_response     = '';
		$wp_version      = get_bloginfo( 'version' );
		$transient       = get_transient( 'plugin_info_' . $plugin_slug );

		if ( $transient ) :
			$plugin_return = $transient;
		else :
			if ( $plugin_slug && $wp_version > 3.8 ) :
				$url  = 'http://api.wordpress.org/plugins/info/1.2/';
				$args = array(
					'author' => 'MooveAgency',
					'fields' => array(
						'downloaded'      => true,
						'active_installs' => true,
						'ratings'         => true,
					),
				);

				$url = add_query_arg(
					array(
						'action'  => 'query_plugins',
						'request' => $args,
					),
					$url
				);

				$http_url = $url;
				$ssl      = wp_http_supports( array( 'ssl' ) );
				if ( $ssl ) :
					$url = set_url_scheme( $url, 'https' );
			endif;

				$http_args = array(
					'timeout'    => 30,
					'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
				);
				$request   = wp_remote_get( $url, $http_args );

				if ( ! is_wp_error( $request ) ) :
					$response = json_decode( wp_remote_retrieve_body( $request ), true );
					if ( is_array( $response ) ) :
						$wp_repo_plugins = isset( $response['plugins'] ) && is_array( $response['plugins'] ) ? $response['plugins'] : array();
						foreach ( $wp_repo_plugins as $plugin_details ) :
							$plugin_details = (object) $plugin_details;
							if ( isset( $plugin_details->slug ) && $plugin_slug === $plugin_details->slug ) :
								$plugin_return = $plugin_details;
								set_transient( 'plugin_info_' . $plugin_slug, $plugin_return, 12 * HOUR_IN_SECONDS );
							endif;
						endforeach;
					endif;
				endif;
			endif;
		endif;
		return $plugin_return;
	}

	/**
	 * Importing logs stored in post_meta to database
	 *
	 * @return int $log_id Log_id.
	 */
	public static function import_log_to_database() {
		$post_types = get_post_types( array( 'public' => true ) );
		unset( $post_types['attachment'] );
		$uat_db_controller = new Moove_Activity_Database_Model();
		$log_id            = false;
		$query             = array(
			'post_type'      => $post_types,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array( // phpcs:ignore
				'relation' => 'OR',
				array(
					'key'     => 'ma_data',
					'value'   => null,
					'compare' => '!=',
				),
			),
		);
		$log_query         = new WP_Query( $query );

		if ( $log_query->have_posts() ) :
			while ( $log_query->have_posts() ) :
				$log_query->the_post();
				$_post_meta      = get_post_meta( get_the_ID(), 'ma_data' );
				$_ma_data_option = $_post_meta[0];
				$ma_data         = unserialize( $_ma_data_option ); // phpcs:ignore

				if ( $ma_data['log'] && is_array( $ma_data['log'] ) ) :
					foreach ( $ma_data['log'] as $log ) :
						$date               = gmdate( 'Y-m-d H:i:s', $log['time'] );
						$data_to_instert    = array(
							'post_id'      => get_the_ID(),
							'user_id'      => $log['uid'],
							'status'       => $log['response_status'],
							'user_ip'      => $log['show_ip'],
							'city'         => $log['city'],
							'display_name' => $log['display_name'],
							'post_type'    => get_post_type( get_the_ID() ),
							'referer'      => $log['referer'],
							'month_year'   => gmdate( 'm', $log['time'] ) . gmdate( 'Y', $log['time'] ),
							'visit_date'   => $date,
							'campaign_id'  => isset( $ma_data['campaign_id'] ) ? $ma_data['campaign_id'] : '',
						);
						$save_to_db_enabled = apply_filters( 'moove_uat_filter_data', $data_to_instert );
						if ( $save_to_db_enabled ) :
							$log_id = $uat_db_controller->insert( $data_to_instert );
						endif;
					endforeach;
				endif;
			endwhile;
		endif;
		wp_reset_postdata();
		update_option( 'moove_importer_has_database', true );
		return $log_id;
	}
	/**
	 * Create admin menu page
	 *
	 * @return void
	 */
	public static function moove_register_activity_menu_page() {
		$activity_perm = apply_filters( 'uat_activity_log_capability', 'manage_options' );

		add_menu_page(
			'Activity Tracking and Log', // Page_title.
			'Activity Tracking and Log', // Menu_title.
			$activity_perm, // Capability.
			'moove-activity-log', // Menu_slug.
			array( 'Moove_Activity_Controller', 'moove_activity_menu_page' ), // Function.
			'dashicons-visibility', // Icon_url.
			3 // Position.
		);
	}

	/**
	 * Pagination function for arrays.
	 *
	 * @param  array $display_array      Array to paginate.
	 * @param  int   $page                Start number.
	 * @param  int   $ppp                 Offset.
	 * @return array                    Paginated array
	 */
	public static function moove_pagination( $display_array, $page, $ppp ) {
		$page      = $page < 1 ? 1 : $page;
		$start     = ( ( $page - 1 ) * ( $ppp ) );
		$offset    = $ppp;
		$out_array = $display_array;
		if ( is_array( $display_array ) ) :
			$out_array = array_slice( $display_array, $start, $offset );
		endif;
		return $out_array;
	}

	/**
	 * Activity log page view
	 *
	 * @return  void
	 */
	public static function moove_activity_menu_page() {
		$uat_view = new Moove_Activity_View();
		echo $uat_view->load( // phpcs:ignore
			'moove.admin.settings.activity-log',
			null
		);
	}

	/**
	 * Tracking the user access when the post will be saved. (status = updated)
	 *
	 * @param int $post_id Post ID.
	 */
	public static function moove_track_user_access_save_post( $post_id ) {
		$log_id = false;
		if ( get_post_type( $post_id ) !== 'nav_menu_item' ) :
			$uat_controller = new Moove_Activity_Controller();
			$uat_shrotcodes = new Moove_Activity_Shortcodes();
			$uat_controller->moove_remove_old_logs( $post_id );
			$post_types = get_post_types( array( 'public' => true ) );
			unset( $post_types['attachment'] );
			// Trigger only on specified post types.
			if ( ! in_array( get_post_type(), $post_types, true ) ) :
				return;
			endif;
			$ma_data    = array();
			$_post_meta = get_post_meta( $post_id, 'ma_data' );
			if ( isset( $_post_meta[0] ) ) :
				$_ma_data_option = $_post_meta[0];
				$ma_data         = unserialize( $_ma_data_option ); // phpcs:ignore
			endif;
			$activity_status = 'updated';
			$ip              = $uat_shrotcodes->moove_get_the_user_ip();
			$ip_uf           = $uat_shrotcodes->moove_get_the_user_ip( false );
			$loc_enabled     = apply_filters( 'uat_show_location_by_ip', true );
			$details         = $loc_enabled ? $uat_shrotcodes->get_location_details( $ip_uf ) : false;
			$city            = $loc_enabled && isset( $details->city ) ? $details->city : '';
			$data            = array(
				'pid'    => intval( $post_id ),
				'uid'    => intval( get_current_user_id() ),
				'status' => esc_attr( $activity_status ),
				'uip'    => esc_attr( $ip ),
				'city'   => $city,
				'ref'    => esc_url( wp_get_referer() ),
			);

			if ( isset( $ma_data['campaign_id'] ) ) :
				$log_id = $uat_controller->moove_create_log_entry( $data );
			else :
				$is_disabled = intval( get_post_meta( $post_id, 'ma_disabled', true ) );
				if ( ! $is_disabled ) :
					$post_type = get_post_type( $post_id );
					$settings  = get_option( 'moove_post_act' );

					if ( isset( $settings[ $post_type ] ) && intval( $settings[ $post_type ] ) !== 0 ) :
						$ma_data                = array();
						$campaign_id            = time() . $post_id;
						$ma_data['campaign_id'] = $campaign_id;
						update_post_meta( $post_id, 'ma_data', serialize( $ma_data ) ); // phpcs:ignore
						$log_id = $uat_controller->moove_create_log_entry( $data );
					endif;
				endif;
			endif;
		endif;
	}

	/**
	 * Tracking the user access on the front end. (status = visited)
	 */
	public static function moove_track_user_access_ajax() {
		$uat_controller = new Moove_Activity_Controller();
		$uat_shrotcodes = new Moove_Activity_Shortcodes();
		$post_id        = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : false; // phpcs:ignore
		$is_page        = isset( $_POST['is_page'] ) ? sanitize_text_field( $_POST['is_page'] ) : false; // phpcs:ignore
		$is_single      = isset( $_POST['is_single'] ) ? sanitize_text_field( $_POST['is_single'] ) : false; // phpcs:ignore
		$user_id        = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : false; // phpcs:ignore
		$referrer       = isset( $_POST['referrer'] ) ? sanitize_text_field( $_POST['referrer'] ) : ''; // phpcs:ignore
		$log_id         = false;
		if ( $post_id ) :
			$uat_controller->moove_remove_old_logs( $post_id );
			// Run on singles or pages.
			if ( $is_page || $is_single ) :
				$post_types = get_post_types( array( 'public' => true ) );
				unset( $post_types['attachment'] );
				// Trigger only on specified post types.
				if ( ! in_array( get_post_type( $post_id ), $post_types, true ) ) :
					return;
				endif;
				$_post_meta      = get_post_meta( $post_id, 'ma_data' );
				$_ma_data_option = isset( $_post_meta[0] ) ? $_post_meta[0] : serialize( array() ); // phpcs:ignore
				$ma_data         = unserialize( $_ma_data_option ); // phpcs:ignore
				$activity_status = 'visited';
				$ip              = $uat_shrotcodes->moove_get_the_user_ip();
				$ip_uf           = $uat_shrotcodes->moove_get_the_user_ip( false );
				$loc_enabled     = apply_filters( 'uat_show_location_by_ip', true );
				$details         = $loc_enabled ? $uat_shrotcodes->get_location_details( $ip_uf ) : false;
				$city            = $loc_enabled && isset( $details->city ) ? $details->city : '';

				$data = array(
					'pid'    => $post_id,
					'uid'    => $user_id,
					'status' => $activity_status,
					'uip'    => esc_attr( $ip ),
					'city'   => $city,
					'ref'    => $referrer,
				);

				if ( isset( $ma_data['campaign_id'] ) ) :
					$log_id = $uat_controller->moove_create_log_entry( $data );
				else :
					$is_disabled = intval( get_post_meta( $post_id, 'ma_disabled', true ) );
					if ( ! $is_disabled ) :
						$post_type = get_post_type( $post_id );
						$settings  = get_option( 'moove_post_act' );

						if ( isset( $settings[ $post_type ] ) && intval( $settings[ $post_type ] ) !== 0 ) :
							$ma_data                = array();
							$campaign_id            = time() . $post_id;
							$ma_data['campaign_id'] = $campaign_id;
							update_post_meta( $post_id, 'ma_data', serialize( $ma_data ) ); // phpcs:ignore
							$log_id = $uat_controller->moove_create_log_entry( $data );
						endif;
					endif;
				endif;
			endif;
			wp_reset_postdata();
		endif;
		echo wp_kses( $log_id, array() );
		die();
	}

	/**
	 * Tracking the user unload event.
	 */
	public static function moove_activity_track_unload() {
		$uat_db_controller = new Moove_Activity_Database_Model();
		$log_id        	= isset( $_POST['log_id'] ) ? intval( $_POST['log_id'] ) : false; // phpcs:ignore
		if ( $log_id ) :
			echo intval( $uat_db_controller->update_log_unload( $log_id ) );
		endif;
	}

	/**
	 * Function to delete a custom post logsm or all logs (if the functions runs without params.)
	 *
	 * @param  int $post_types Post ID.
	 */
	public function moove_clear_logs( $post_types = false ) {
		$uat_db_controller = new Moove_Activity_Database_Model();
		$uat_content       = new Moove_Activity_Content();

		if ( ! $post_types ) :
			$post_types = get_post_types( array( 'public' => true ) );
			unset( $post_types['attachment'] );
		else :
			delete_post_meta( $post_types, 'ma_data' );
			$uat_db_controller->delete_log( 'post_id', $post_types );
			$uat_content->moove_save_post( $post_types, 'enable' );
			return;
		endif;

		$query = array(
			'post_type'      => $post_types,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array( // phpcs:ignore
				'relation' => 'OR',
				array(
					'key'     => 'ma_data',
					'value'   => null,
					'compare' => '!=',
				),
			),
		);

		$log_posts = new WP_Query( $query );
		if ( $log_posts->have_posts() ) :
			while ( $log_posts->have_posts() ) :
				$log_posts->the_post();

				$_post_meta      = get_post_meta( get_the_ID(), 'ma_data' );
				$_ma_data_option = isset( $_post_meta[0] ) ? $_post_meta[0] : serialize( array() ); // phpcs:ignore
				$ma_data         = unserialize( $_ma_data_option ); // phpcs:ignore
				$uat_db_controller->delete_log( 'post_id', get_the_ID() );
				if ( isset( $ma_data['campaign_id'] ) ) :
					delete_post_meta( get_the_ID(), 'ma_data' );
					$uat_content->moove_save_post( get_the_ID(), 'enable' );
				endif;
			endwhile;

		endif;
		wp_reset_postdata();
	}

	/**
	 * Remove the old logs. It calculates the difference between two dates,
	 * and if the difference between the log date and the current date is higher than
	 * the day(s) setted up on the settings page, than it remove that entry.
	 *
	 * @param  int $post_id Post ID.
	 */
	public static function moove_remove_old_logs( $post_id ) {
		$_post_meta        = get_post_meta( $post_id, 'ma_data' );
		$ma_data_old       = array();
		$uat_db_controller = new Moove_Activity_Database_Model();
		if ( isset( $_post_meta[0] ) ) :
			$_ma_data_option = $_post_meta[0];
			$ma_data_old     = unserialize( $_ma_data_option ); // phpcs:ignore
		endif;
		if ( isset( $ma_data_old['campaign_id'] ) ) :
			$post_type         = get_post_type( $post_id );
			$activity_settings = get_option( 'moove_post_act' );
			$days              = intval( $activity_settings[ $post_type . '_transient' ] );
			$today             = date_create( gmdate( 'm/d/Y', strtotime( 'timestamp' ) ) );
			$end_date          = gmdate( 'Y-m-d H:i:s', strtotime( "-$days days" ) );
			$uat_db_controller->remove_old_logs( $post_id, $end_date );
		endif;
	}

	/**
	 * Create the log file for post.
	 *
	 * @param  array $data Aarray with log data.
	 */
	protected function moove_create_log_entry( $data ) {
		$_post_meta        = get_post_meta( $data['pid'], 'ma_data' );
		$ma_data           = array();
		$uat_controller    = new Moove_Activity_Controller();
		$uat_db_controller = new Moove_Activity_Database_Model();
		$log_id            = 'false';
		if ( isset( $_post_meta[0] ) ) :
			$_ma_data_option = $_post_meta[0];
			$ma_data         = unserialize( $_ma_data_option ); // phpcs:ignore
		endif;
		$log = isset( $ma_data['log'] ) ? $ma_data['log'] : array();
		// We don't have anything set up.
		if ( '' === $log || ( is_array( $log ) && 0 === count( $log ) ) ) :
			$log = array();
		endif;
		$user = get_user_by( 'id', $data['uid'] );
		if ( $user ) :
			$username = $user->data->display_name;
		else :
			$username = __( 'Unknown', 'user-activity-tracking-and-log' );
		endif;

		if ( $data['city'] ) :
			$city_name = $data['city'];
		else :
			$city_name = __( 'Unknown', 'user-activity-tracking-and-log' );
		endif;
		$uat_controller->moove_remove_old_logs( $data['pid'] );

		$date               = gmdate( 'Y-m-d H:i:s', strtotime( 'now' ) );
		$data_to_instert    = array(
			'post_id'      => $data['pid'],
			'user_id'      => intval( $data['uid'] ),
			'status'       => esc_attr( $data['status'] ),
			'user_ip'      => esc_attr( $data['uip'] ),
			'display_name' => $username,
			'city'         => $city_name,
			'post_type'    => get_post_type( $data['pid'] ),
			'referer'      => $data['ref'],
			'month_year'   => gmdate( 'm' ) . gmdate( 'Y' ),
			'visit_date'   => $date,
			'campaign_id'  => isset( $ma_data['campaign_id'] ) ? $ma_data['campaign_id'] : '',
		);
		$save_to_db_enabled = apply_filters( 'moove_uat_filter_data', $data_to_instert );

		if ( $save_to_db_enabled ) :
			$log_id = $uat_db_controller->insert( $data_to_instert );
		endif;
		return $log_id;
	}

	/**
	 * Activity Dates
	 *
	 * @param array  $log_array Log array.
	 * @param string $active Active tab.
	 */
	public static function moove_get_activity_dates( $log_array, $active ) {
		ob_start();
		if ( is_array( $log_array ) && ! empty( $log_array ) ) :
			$date_array = array();
			foreach ( $log_array as $log_entry ) :
				if ( $log_entry['time'] ) :
					$time                          = strtotime( $log_entry['time'] );
					$month                         = gmdate( 'm', $time );
					$day                           = gmdate( 'd', $time );
					$year                          = gmdate( 'Y', $time );
					$month_name                    = gmdate( 'F', $time );
					$date_array[ $year ][ $month ] = array(
						'month_name' => $month_name,
						'year'       => $year,
					);
				endif;
			endforeach;
			krsort( $date_array );
			?>
			<select name="m" id="filter-by-date">
				<option selected="selected" value="0"><?php esc_html_e( 'All Dates', 'user-activity-tracking-and-log' ); ?></option>
				<?php
				foreach ( $date_array as $year => $year_entry ) :
					$_date_entry = $year_entry;
					krsort( $_date_entry );
					foreach ( $_date_entry as $month => $_ndate_entry ) :
						?>
						<?php
							$selected = '';
							$term     = $month . $year;
						if ( 0 !== $active && intval( $active ) === intval( $term ) ) :
							$selected = 'selected="selected"';
							endif;
						?>
						<option value="<?php echo esc_attr( $month . $year ); ?>" <?php echo esc_attr( $selected ); ?>>
							<?php echo esc_attr( $_ndate_entry['month_name'] . ' ' . $year ); ?>
						</option>
						<?php
					endforeach;
				endforeach;
				?>
			</select>
			<?php
		endif;

		return ob_get_clean();
	}

	/**
	 * Filter data
	 *
	 * @param array  $log_array Log array.
	 * @param int    $m Month.
	 * @param int    $uid User ID.
	 * @param int    $cat Category.
	 * @param string $search_term Search term.
	 * @param int    $role User role.
	 */
	public static function moove_get_filtered_array( $log_array, $m, $uid, $cat, $search_term, $role = -1 ) {
		$sorted_array      = array();
		$uat_db_controller = new Moove_Activity_Database_Model();
		$plugin_settings   = apply_filters( 'moove_uat_filter_plugin_settings', get_option( 'moove_post_act' ) );

		if ( 0 === $cat && intval( $m ) === 0 && '' === $search_term && intval( $uid ) === -1 && intval( $role ) === -1 ) {
			return $log_array;
		}

		$has_previous = false;
		$where        = array();

		if ( '0' !== $m && 0 !== $m ) :
			$has_previous = true;
			$where[]      = array(
				'key'   => 'month_year',
				'value' => $m,
			);
		endif;

		if ( '0' !== $cat && 0 !== $cat ) :
			if ( $has_previous ) :
				$where['relation'] = 'AND';
			endif;
			$where[]      = array(
				'key'   => 'post_type',
				'value' => $cat,
			);
			$has_previous = true;
		endif;

		if ( -1 !== intval( $uid ) && '-1' !== $uid ) :
			if ( $has_previous ) :
				$where['relation'] = 'AND';
			endif;
			$where[]      = array(
				'key'   => 'user_id',
				'value' => $uid,
			);
			$has_previous = true;
		endif;

		$results = $uat_db_controller->get_search_results( $where );

		if ( ! $has_previous ) :
			$results = $uat_db_controller->get_log( false, false );
		endif;

		$f_user_ids     = array();
		$has_role_filer = false;

		if ( -1 !== intval( $role ) && '-1' !== $role && is_string( $role ) ) :
			$filtered_users = get_users( 'role=' . $role );
			$has_role_filer = true;
			if ( is_array( $filtered_users ) ) :
				foreach ( $filtered_users as $f_user ) :
					$f_user_ids[] = $f_user->ID;
				endforeach;
			endif;
		endif;

		$return = array();
		if ( $results && is_array( $results ) ) :
			foreach ( $results as $log ) :

				$import_this = false;
				if ( '' !== $search_term ) :
					$title = strtolower( get_the_title( $log->post_id ) );
					if ( strpos( $title, strtolower( $search_term ) ) !== false ) :
						$import_this = true;
					elseif ( strpos( $log->user_ip, strtolower( $search_term ) ) !== false ) :
						$import_this = true;
					elseif ( strpos( strtolower( $log->display_name ), strtolower( $search_term ) ) !== false ) :
						$import_this = true;
					elseif ( strpos( strtolower( $log->city ), strtolower( $search_term ) ) !== false ) :
						$import_this = true;
					elseif ( strpos( get_permalink( $log->post_id ), strtolower( $search_term ) ) !== false ) :
						$import_this = true;
					elseif ( $log->user_id ) :
						$user = get_user_by( 'id', $log->user_id );
						if ( $user && ( strpos( strtolower( $user->display_name ), strtolower( $search_term ) ) !== false || strpos( strtolower( $user->user_email ), strtolower( $search_term ) ) !== false || strpos( strtolower( $user->first_name ), strtolower( $search_term ) ) !== false || strpos( strtolower( $user->last_name ), strtolower( $search_term ) ) !== false ) ) :
								$import_this = true;
						endif;
					endif;
				else :
					$import_this = true;
				endif;

				if ( $has_role_filer ) :
					if ( is_array( $f_user_ids ) && isset( $log->user_id ) && in_array( intval( $log->user_id ), $f_user_ids, true ) ) :
						$import_this = $import_this ? true : false;
					else :
						$import_this = false;
					endif;
				endif;

				if ( $import_this ) :
					$post_type = get_post_type( $log->post_id );
					if ( isset( $plugin_settings[ $post_type ] ) && intval( $plugin_settings[ $post_type ] ) === 1 ) :
						$return[] = array(
							'post_id'         => $log->post_id,
							'time'            => $log->visit_date,
							'uid'             => $log->user_id,
							'display_name'    => $log->display_name,
							'ip_address'      => $log->user_ip,
							'response_status' => $log->status,
							'referer'         => $log->referer,
							'city'            => $log->city,
							'user_id'         => $log->user_id,
							'time_spent'  		=> isset( $log->time_spent ) ? $log->time_spent : '',
						);
					endif;
				endif;

			endforeach;
		endif;

		return $return;
	}

}
new Moove_Activity_Controller();
