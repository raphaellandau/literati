<?php
/**
 * Activity Log Doc Comment
 *
 * @category  Views
 * @package   user-activity-tracking
 * @author    Moove Agency
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$uat_controller    = new Moove_Activity_Controller();
$uat_db_controller = new Moove_Activity_Database_Model();
$plugin_settings   = apply_filters( 'moove_uat_filter_plugin_settings', get_option( 'moove_post_act' ) );

$activity_perm = apply_filters( 'uat_activity_log_capability', 'manage_options' );

$settings_perm = apply_filters( 'uat_log_settings_capability', 'manage_options' );

$logs_imported = $uat_controller->moove_importer_check_database();
if ( ! $logs_imported ) :
	$uat_controller->import_log_to_database();
endif;

$screen_options     = get_user_meta( get_current_user_id(), 'moove_activity_screen_options', true );
$screen_options     = apply_filters( 'uat_activity_screen_options_extension', $screen_options );
$selected_val       = isset( $screen_options['moove-activity-dtf'] ) ? $screen_options['moove-activity-dtf'] : 'b';
$query_post_types   = array();
$server_host        = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
$server_request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
$page_url           = htmlspecialchars( "//{$server_host}{$server_request_uri}", ENT_QUOTES, 'UTF-8' );
$selected_date      = 0;
$selected_post_type = 0;
$selected_user      = -1;
$search_term        = '';
$selected_role      = -1;
$no_results         = true;
$user_options       = get_user_meta( get_current_user_id(), 'moove_activity_screen_options', true );
$custom_order       = false;

$default_tabs = array( 'all_logs', 'settings' );
wp_verify_nonce( 'uat_log_nonce', 'uat_log_nonce_f' );
if ( isset( $_GET['tab'] ) ) :
	$active_tab = rawurlencode( sanitize_text_field( wp_unslash( $_GET['tab'] ) ) );
	$active_tab = $active_tab ? $active_tab : 'all_logs';
else :
	$active_tab = 'all_logs';
endif;

if ( $active_tab === 'activity-screen-settings' && defined( 'MOOVE_UAT_PREMIUM_VERSION' ) && MOOVE_UAT_PREMIUM_VERSION < '2.2' ) :
	$active_tab = 'activity_screen_settings';
elseif ( $active_tab === 'tracking-settings' && defined( 'MOOVE_UAT_PREMIUM_VERSION' ) && MOOVE_UAT_PREMIUM_VERSION < '2.2' ) :
	$active_tab = 'tracking_settings';
endif;

if ( isset( $_GET['clear-all-logs'] ) ) :
	$uat_controller->moove_clear_logs();
endif;
$_orderby = 'date';
$_order   = 'asc';
if ( isset( $_GET['orderby'] ) && isset( $_GET['order'] ) ) :
	$custom_order   = true;
	$enabled_values = array( 'time', 'title', 'posttype', 'display_name', 'response_status', 'ip_address', 'city', 'referer' );
	$_orderby       = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
	$_orderby       = is_array( $enabled_values ) && ! empty( $enabled_values ) && in_array( $_orderby, $enabled_values ) ? $_orderby : 'date';
	$_order         = sanitize_text_field( wp_unslash( $_GET['order'] ) ) === 'desc' ? 'desc' : 'asc';
endif;

if ( isset( $_GET['clear-log'] ) ) :
	$uat_controller->moove_clear_logs( intval( $_GET['clear-log'] ) );
endif;

if ( isset( $_GET['m'] ) && sanitize_text_field( wp_unslash( $_GET['m'] ) ) ) :
	$page_url = remove_query_arg( 'm' );
	if ( sanitize_text_field( wp_unslash( $_GET['m'] ) ) !== '0' ) :
		$selected_date = rawurlencode( sanitize_text_field( wp_unslash( $_GET['m'] ) ) );
		$page_url      = add_query_arg( 'm', $selected_date, $page_url );
	endif;
endif;

if ( isset( $_GET['cat'] ) && sanitize_text_field( wp_unslash( $_GET['cat'] ) ) ) :
	$page_url = remove_query_arg( 'cat' );
	if ( ! intval( $_GET['cat'] ) ) :
		$selected_post_type = rawurlencode( sanitize_text_field( wp_unslash( $_GET['cat'] ) ) );
		$page_url           = add_query_arg( 'cat', $selected_post_type, $page_url );
	endif;
endif;

if ( isset( $_GET['uid'] ) ) :

	$page_url = remove_query_arg( 'uid' );

	$selected_user = intval( $_GET['uid'] );
	$page_url      = add_query_arg( 'uid', $selected_user, $page_url );

endif;

if ( isset( $_GET['user_role'] ) && sanitize_text_field( wp_unslash( $_GET['user_role'] ) !== 'undefined' ) ) :
	$page_url      = remove_query_arg( 'user_role' );
	$selected_role = rawurlencode( sanitize_text_field( wp_unslash( $_GET['user_role'] ) ) );
	$page_url      = add_query_arg( 'user_role', $selected_role, $page_url );
endif;

if ( isset( $_GET['s'] ) && sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) :
	$page_url = remove_query_arg( 's' );
	if ( sanitize_text_field( wp_unslash( $_GET['s'] ) !== '' ) ) :
		$search_term = urldecode( rawurldecode( sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) );
		$page_url    = add_query_arg( 's', $search_term, $page_url );
	endif;
endif;
?>

<!-- Wrap for notifications -->
<div class="wrap" style="margin: 0; border: none;">
	<h2 class="nav-tab-wrapper" style="border: none; opacity: 0; padding: 0; height: 0;"></h2>
</div>
<!-- .wrap -->

<!-- .nav-tab-wrapper -->
<link rel="stylesheet" type="text/css" href="<?php echo esc_url( moove_activity_get_plugin_dir() ); ?>/assets/css/moove_activity_backend_select2.css" >

<div class="uat-admin-header-section">
	<h2><?php esc_html_e( 'User Activity Tracking and Log', 'user-activity-tracking-and-log' ); ?> <span class="uat-plugin-version"><?php echo 'v' . esc_attr( MOOVE_UAT_VERSION ); ?></span></h2>
	<br>
</div>
<!--  .uat-header-section -->

<div id="moove-activity-message-cnt"></div>
<!-- #moove-activity-message-cnt -->

<div class="wrap moove-activity-plugin-wrap <?php echo isset( $_GET['collapsed'] ) ? 'uat-collapsed' : ''; ?>" id="uat-settings-cnt">

	<div class="uat-tab-section-cnt">
		<?php do_action( 'uat_premium_update_alert' ); ?>
		<h2 class="nav-tab-wrapper">        
			<span class="navt-tab-wrapper navt-tab-wrapper-top">
				<a href="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>?page=moove-activity-log&tab=all_logs" class="nav-tab nav-tab-separator nav-tab-first <?php echo 'all_logs' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<i class="dashicons dashicons-visibility"></i> 
					<?php esc_html_e( 'Activity Log', 'user-activity-tracking-and-log' ); ?>
				</a>
				<?php
					$_post_types = get_post_types( array( 'public' => true ) );
					unset( $_post_types['attachment'] );
					foreach ( $_post_types as $_post_type ) :
						if ( isset( $plugin_settings[ $_post_type ] ) && intval( $plugin_settings[ $_post_type ] ) === 1 ) :
							$_post_type_object = get_post_type_object( $_post_type );
							?>
								<a href="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>?page=moove-activity-log&tab=<?php echo esc_attr( $_post_type ); ?>" class="nav-tab <?php echo $active_tab === $_post_type ? 'nav-tab-active' : ''; ?>">
									<?php echo esc_attr( $_post_type_object->label ); ?>
								</a>
							<?php
							$default_tabs[] = $_post_type;
						endif;
					endforeach;
					do_action( 'uat_extend_activity_screen_nav', $active_tab );
				?>
			</span>
			<!-- .navt-tab-wrapper-top -->
			<span class="navt-tab-wrapper navt-tab-wrapper-middle navt-tab-event-tracking">
				<?php do_action( 'uat_extend_activity_screen_nav_middle', $active_tab ); ?>
			</span>
			<!-- .navt-tab-wrapper-middle -->
			<span class="navt-tab-wrapper navt-tab-wrapper-bottom">

				<a href="<?php echo esc_url( admin_url( '/admin.php?page=moove-activity-log&tab=activity-settings' ) ); ?>" class="nav-tab nav-tab-separator menu-margin-separator <?php echo 'activity-settings' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<i class="dashicons dashicons-admin-settings"></i> <?php esc_html_e( 'Settings', 'user-activity-tracking-and-log' ); ?>
				</a>

				<?php do_action( 'moove_activity_tab_extensions', $active_tab ); ?>

				<a href="<?php echo esc_url( admin_url( '/admin.php?page=moove-activity-log&tab=video-tutorial' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'video-tutorial' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<span class="dashicons dashicons-format-video"></span>
					<?php esc_html_e( 'Video Tutorial', 'user-activity-tracking-and-log' ); ?>
				</a>

				<a href="<?php echo esc_url( admin_url( '/admin.php?page=moove-activity-log&tab=licence' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'licence' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<span class="dashicons dashicons-admin-network"></span>
					<?php esc_html_e( 'Licence Manager', 'user-activity-tracking-and-log' ); ?>
				</a>
				<span class="nav-tab nav-tab-collapse">
					<i class="dashicons dashicons-admin-collapse"></i>
					<span><?php esc_html_e( 'Collapse Menu', 'user-activity-tracking-and-log' ); ?></span>
				</span>
			</span>
			<!-- .navt-tab-wrapper-bottom -->
		</h2>
		<div class="moove-form-container <?php echo esc_attr( $active_tab ); ?>">
			<div class="moove-activity-log-report">
				<?php
				if ( 'all_logs' === $active_tab ) :
					if ( current_user_can( $activity_perm ) ) : 
						?>
							<div class="all-logs-header">
								<h2><?php esc_html_e( 'All Logs', 'user-activity-tracking-and-log' ); ?></h2>
								<?php if ( isset( $_GET['tab'] ) ) : ?>
									<form action="<?php echo esc_url( $page_url ); ?>" class="search-box" method="post" id="uat-search-box">
										<label class="screen-reader-text" for="post-search-input">
											<?php esc_html_e( 'Search Posts', 'user-activity-tracking-and-log' ); ?>:
										</label>
										<input type="search" id="post-search-input" name="s" placeholder="<?php esc_html_e( 'Search Logs...', 'user-activity-tracking-and-log' ); ?>" value="<?php echo esc_attr( $search_term ); ?>">
										<button type="submit" id="search-submit" data-pageurl="<?php echo esc_url( $page_url ); ?>"><span class="dashicons dashicons-search"></span></button>
									</form>
								<?php endif; ?>
								<hr>
							</div>
							<!-- .all-logs-header -->
						<?php 
					endif;
				endif;

			if ( in_array( $active_tab, $default_tabs ) ) :
				if ( current_user_can( $activity_perm ) ) :
					if ( 'all_logs' === $active_tab && isset( $_GET['tab'] ) ) :
						$log_array  = array();
						$activity_all_logs = $uat_db_controller->get_all_logs( $_post_types );
						$activity_all_logs = $activity_all_logs && is_array( $activity_all_logs ) ? $activity_all_logs : array();
						$activity_all_logs = array_map( 'uat_filter_data_entry', $activity_all_logs, $activity_all_logs );

						$log_array           = is_array( $activity_all_logs ) ? $activity_all_logs : array();
						$untouched_log_array = $log_array;
						$log_array           = $uat_controller->moove_get_filtered_array( $log_array, $selected_date, $selected_user, $selected_post_type, $search_term, $selected_role );

						$selected_date       = $selected_date ? $selected_date : '';
						$date_filter_content = $uat_controller->moove_get_activity_dates( $untouched_log_array, $selected_date );
						if ( $custom_order ) :
							if ( is_array( $log_array ) ) :
								usort( $log_array, array( new Moove_Activity_Array_Order( $_orderby ), 'custom_order' ) );
								if ( 'asc' === $_order ) :
									$log_array = array_reverse( $log_array );
								endif;
							endif;
						else :
							if ( is_array( $log_array ) ) :
								usort( $log_array, 'moove_desc_sort' );
							endif;
						endif;

						$log_array_count = count( $log_array );
						$original_array  = $log_array;

						$post_per_page = isset( $user_options['wp_screen_options']['value'] ) && intval( $user_options['wp_screen_options']['value'] ) ? intval( $user_options['wp_screen_options']['value'] ) : 10;
						$max_num_pages = ceil( count( $log_array ) / $post_per_page );
						if ( isset( $_GET['offset'] ) ) :
							$offset = intval( $_GET['offset'] );
						else :
							$offset = 1;
						endif;

						$log_array = $uat_controller->moove_pagination( $log_array, $offset, $post_per_page );

						?>

						<div class="tablenav top">
							<div class="alignleft actions">
								<?php ob_start(); ?>
								<label for="filter-by-date" class="screen-reader-text">
									<?php esc_html_e( 'Filter by date', 'user-activity-tracking-and-log' ); ?>
								</label>
								<?php
									echo wp_kses(
										$date_filter_content,
										array(
											'select' => array(
												'id'   => array(),
												'name' => array(),
											),
											'option' => array(
												'selected' => array(),
												'value'    => array(),
											),
										)
									);
								?>

								<label class="screen-reader-text" for="cat">
									<?php esc_html_e( 'Filter by post type', 'user-activity-tracking-and-log' ); ?>
								</label>
								<select name="post_types" id="post_types" class="postform">
									<option value="0"><?php esc_html_e( 'All Post Types', 'user-activity-tracking-and-log' ); ?></option>
									<?php
										$_post_types = get_post_types( array( 'public' => true ) );
										unset( $_post_types['attachment'] );

										foreach ( $_post_types as $_post_type ) :
											if ( isset( $plugin_settings[ $_post_type ] ) && intval( $plugin_settings[ $_post_type ] ) === 1 ) :
												$selected = '';
												if ( $selected_post_type === $_post_type && 0 !== $selected_post_type ) :
													$selected = 'selected="selected"';
												endif;
												?>
													<option class="level-0" value="<?php echo esc_attr( $_post_type ); ?>" <?php echo esc_attr( $selected ); ?>>
														<?php echo esc_attr( ucfirst( $_post_type ) ); ?>
													</option>
												<?php
											endif;
										endforeach;
									?>
								</select>
								<?php
									$filters = ob_get_clean();
									do_action( 'moove_activity_filters', $filters, $date_filter_content );
								?>
								<button type="submit" name="filter_action" id="post-query-submit" class="uat-orange-bnt" data-pageurl="<?php echo esc_url( $page_url ); ?>">
									<span class="dashicons dashicons-filter"></span>
									<?php esc_html_e( 'Filter', 'user-activity-tracking-and-log' ); ?>
								</button>
							</div>
							<!-- .alignleft actions -->

							<div class="tablenav-pages one-page">
								<span class="displaying-num">
									<?php
									/* translators: %d: items */
									printf( esc_html__( '%d items', 'user-activity-tracking-and-log' ), intval( $log_array_count ) );
									?>
								</span>
							</div>
							<!-- .tablenav-pages one-page -->
							<br class="clear">
						</div>
						<!-- tablenav -->
						<div class="uat-responsive-table-wrap">
							<table class="moove-activity-log-table wp-list-table widefat fixed striped" id="moove-activity-log-table-global">
								<?php
									$t_heading_row = '';
									if ( count( $log_array ) ) :
										$no_results = false;
										?>
										<thead>
											<?php ob_start(); ?>
											<tr>
												<?php 
													$page_url 			= remove_query_arg( array( 'orderby', 'order' ), false ); 
													$current_order  = moove_activity_current_order( 'time', $custom_order, $_order, $_orderby );
													$reversed_order = 'asc' === $current_order ? 'desc' : 'asc';
												?>
												<th class="manage-column column-date column-primary sortable <?php echo esc_attr( $current_order ); ?>">
													<a href="
														<?php
															echo esc_url(
																add_query_arg(
																	array(
																		'orderby' => 'time',
																		'order'   => $reversed_order,
																	),
																	$page_url
																)
															);
														?>
														">
														<span><?php esc_html_e( 'Date / Time', 'user-activity-tracking-and-log' ); ?></span>
														<span class="sorting-indicator"></span>
													</a>
												</th>

												<?php
													$current_order  = moove_activity_current_order( 'title', $custom_order, $_order, $_orderby );
													$reversed_order = 'asc' === $current_order ? 'desc' : 'asc';
												?>
												<th class="column-title manage-column sortable <?php echo esc_attr( $current_order ); ?>">
													<a href="
														<?php
															echo esc_url(
																add_query_arg(
																	array(
																		'orderby' => 'title',
																		'order'   => $reversed_order,
																	),
																	$page_url
																)
															);
														?>
														">
														<span><?php esc_html_e( 'Post Title', 'user-activity-tracking-and-log' ); ?></span>
														<span class="sorting-indicator"></span>
													</a>
												</th>

												<?php
													$current_order  = moove_activity_current_order( 'posttype', $custom_order, $_order, $_orderby );
													$reversed_order = 'asc' === $current_order ? 'desc' : 'asc';
												?>
												<th class="column-posttype manage-column sortable <?php echo esc_attr( $current_order ); ?> <?php echo ( isset( $user_options['posttype-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
													<a href="
														<?php
															echo esc_url(
																add_query_arg(
																	array(
																		'orderby' => 'posttype',
																		'order'   => $reversed_order,
																	),
																	$page_url
																)
															);
															?>
														">
														<span><?php esc_html_e( 'Post Type', 'user-activity-tracking-and-log' ); ?></span>
														<span class="sorting-indicator"></span>
													</a>
												</th>

												<?php
													$current_order  = moove_activity_current_order( 'display_name', $custom_order, $_order, $_orderby );
													$reversed_order = 'asc' === $current_order ? 'desc' : 'asc';
												?>
												<th class="column-user manage-column sortable <?php echo esc_attr( $current_order ); ?> <?php echo ( isset( $user_options['user-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
													<a href="
														<?php
															echo esc_url(
																add_query_arg(
																	array(
																		'orderby' => 'display_name',
																		'order'   => $reversed_order,
																	),
																	$page_url
																)
															);
														?>
														">
														<span><?php esc_html_e( 'Display Name', 'user-activity-tracking-and-log' ); ?></span>
														<span class="sorting-indicator"></span>
													</a>
												</th>

												<?php
													$current_order  = moove_activity_current_order( 'response_status', $custom_order, $_order, $_orderby );
													$reversed_order = 'asc' === $current_order ? 'desc' : 'asc';
												?>
												<th class="column-activity manage-column sortable <?php echo esc_attr( $current_order ); ?> <?php echo ( isset( $user_options['activity-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
													<a href="
															<?php
																echo esc_url(
																	add_query_arg(
																		array(
																			'orderby' => 'response_status',
																			'order'   => $reversed_order,
																		),
																		$page_url
																	)
																);
															?>
														">
														<span><?php esc_html_e( 'Activity', 'user-activity-tracking-and-log' ); ?></span>
														<span class="sorting-indicator"></span>
													</a>
												</th>

												<?php
													$current_order  = moove_activity_current_order( 'ip_address', $custom_order, $_order, $_orderby );
													$reversed_order = 'asc' === $current_order ? 'desc' : 'asc';
												?>
												<th class="column-ip manage-column sortable <?php echo esc_attr( $current_order ); ?> <?php echo ( isset( $user_options['ip-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
													<a href="
														<?php
															echo esc_url(
																add_query_arg(
																	array(
																		'orderby' => 'ip_address',
																		'order'   => $reversed_order,
																	),
																	$page_url
																)
															);
														?>
														">
														<span><?php esc_html_e( 'IP address', 'user-activity-tracking-and-log' ); ?></span>
														<span class="sorting-indicator"></span>
													</a>
												</th>

												<?php
												$loc_enabled = apply_filters( 'uat_show_location_by_ip', true );
												if ( $loc_enabled ) :
													$current_order  = moove_activity_current_order( 'city', $custom_order, $_order, $_orderby );
													$reversed_order = 'asc' === $current_order ? 'desc' : 'asc';
													?>
													<th class="column-city manage-column sortable <?php echo esc_attr( $current_order ); ?> <?php echo ( isset( $user_options['city-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
														<a href="
															<?php
																echo esc_url(
																	add_query_arg(
																		array(
																			'orderby' => 'city',
																			'order'   => $reversed_order,
																		),
																		$page_url
																	)
																);
															?>
															">
															<span><?php esc_html_e( 'Location', 'user-activity-tracking-and-log' ); ?></span>
															<span class="sorting-indicator"></span>
														</a>
													</th>
													<?php
												endif;

												$current_order  = moove_activity_current_order( 'referer', $custom_order, $_order, $_orderby );
												$reversed_order = 'asc' === $current_order ? 'desc' : 'asc';
												?>
												<th class="column-referrer manage-column sortable <?php echo esc_attr( $current_order ); ?> <?php echo ( isset( $user_options['referrer-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
													<a href="
														<?php
															echo esc_attr(
																add_query_arg(
																	array(
																		'orderby' => 'referer',
																		'order'   => $reversed_order,
																	),
																	$page_url
																)
															);
														?>
														">
														<span><?php esc_html_e( 'Referrer', 'user-activity-tracking-and-log' ); ?></span>
														<span class="sorting-indicator"></span>
													</a>
												</th>
												<?php do_action( 'uat_activity_table_head_ext', $custom_order, $_order, $_orderby, $page_url, $user_options ); ?>
											</tr>
											<?php 
											$t_heading_row = ob_get_clean(); 
											echo $t_heading_row;
											?>
										</thead>
										<?php 
									else : 
										?>
										<tbody>
											<tr class="no-items">
												<td class="" colspan="3"><h4 style="margin: 10px;"><?php esc_html_e( 'No logs were found.', 'user-activity-tracking-and-log' ); ?></h4></td>
											</tr>
										</tbody>
										<?php 
									endif; 

									if ( is_array( $log_array ) ) :
										?>
										<tbody>
											<?php
											$display_results = '';
											foreach ( $log_array as $log_entry ) :
												$_cache_key       = 'uat-pt-' . $log_entry['post_id'];
												$_post_type_cache = wp_cache_get( $_cache_key );
												if ( ! $_post_type_cache ) :
													$__post_type = get_post_type( $log_entry['post_id'] );
													wp_cache_set( $_cache_key, $__post_type );
												else :
													$__post_type = $_post_type_cache;
												endif;
												?>
													<tr>
														<td class="column-date">
															<?php echo esc_attr( moove_activity_convert_date( $selected_val, $log_entry['time'], $screen_options ) ); ?>
														</td>

														<td class="column-title">
															<a href="<?php echo esc_url( get_permalink( $log_entry['post_id'] ) ); ?>" target="_blank">
																<?php echo esc_attr( get_the_title( $log_entry['post_id'] ) ); ?>
															</a>
														</td>

														<td class="column-posttype <?php echo ( isset( $user_options['posttype-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
															<?php echo esc_attr( $__post_type ); ?>
														</td>

														<td class="column-user <?php echo ( isset( $user_options['user-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
															<?php echo wp_kses( apply_filters( 'uat_user_display_name', $log_entry['display_name'], $log_entry['ip_address'] ), array( 'a' => array( 'href' => array() ) ) ); ?>  
														</td>

														<td class="column-activity <?php echo ( isset( $user_options['activity-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
															<span style="color:green;"><?php echo esc_attr( $log_entry['response_status'] ); ?></span>
														</td>

														<td class="column-ip <?php echo ( isset( $user_options['ip-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
															<?php echo esc_attr( $log_entry['ip_address'] ); ?>
														</td>
														<?php
															$loc_enabled = apply_filters( 'uat_show_location_by_ip', true );
															if ( $loc_enabled ) :
																?>
																	<td class="column-city <?php echo ( isset( $user_options['city-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
																		<?php echo esc_attr( $log_entry['city'] ); ?>
																	</td>
																<?php
															endif;
														?>

														<td class="column-referrer <?php echo ( isset( $user_options['referrer-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
															<?php echo wp_kses( moove_activity_get_referrer_link_by_url( $log_entry['referer'] ), wp_kses_allowed_html( 'post' ) ); ?>
														</td>

														<?php do_action( 'uat_activity_table_data_ext', $log_entry, $user_options ); ?>
													</tr>
												<?php
											endforeach;
											?>
										</tbody>
										<tfoot>
											<?php echo $t_heading_row; ?>
										</tfoot>
										<?php
									endif;
								?>
							</table>
						</div>
						<!-- .uat-responsive-table-wrap -->    

						<?php 
						if ( ! $no_results ) : 
							?>
								<div id="moove-activity-buttons-container">
									<br>
									<?php 
										if ( $max_num_pages !== $offset ) : 
											?>
												<a href="<?php echo esc_url( $page_url ); ?>" class="uat-orange-bnt uat-button load-more" data-max="<?php echo esc_attr( $max_num_pages ); ?>" data-offset="<?php echo esc_attr( $offset ); ?>">
													<span class="dashicons dashicons-image-filter"></span>
													<?php esc_html_e( 'Load more', 'user-activity-tracking-and-log' ); ?>
												</a>
											<?php
										endif;

										$settings_perm = apply_filters( 'uat_log_settings_capability', 'manage_options' );
										if ( current_user_can( $settings_perm ) ) :
											?>
											<a href="<?php echo esc_url( $page_url ); ?>" class="uat-brown-bnt uat-button pullright clear-all-logs">
												<span class="dashicons dashicons-trash"></span>
												<?php esc_html_e( 'Delete All Logs', 'user-activity-tracking-and-log' ); ?>
											</a>
											<?php
										endif;

										do_action( 'moove_activity_extra_buttons', $page_url );
									?>
								</div>
								<!-- #moove-activity-buttons-container  -->
							<?php
						endif;
					else :
						if ( isset( $_GET['tab'] ) ) :
							$log_enabled        = 0;
							$server_host        = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
							$server_request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
							$page_url           = htmlspecialchars( "//{$server_host}{$server_request_uri}", ENT_QUOTES, 'UTF-8' );
							?>
							<h2>
								<?php
									$_post_type = get_post_type_object( $active_tab );
									echo $_post_type ? esc_attr( $_post_type->label ) : '';
								?>
							</h2>
							<hr>
							<div class="moove-accordion-cnt">
								<div class="moove-accordion">
									<div class="moove-accordion-section">
										<?php
										if ( is_array( $active_tab ) && count( $active_tab ) > 1 ) :
											echo '<h2>' . esc_attr( ucfirst( $ptlog ) ) . '</h2>';
										endif;

										$log_array  = array();
										$activity_all_logs = $uat_db_controller->get_all_logs( array( $active_tab ) );
										$activity_all_logs = $activity_all_logs && is_array( $activity_all_logs ) ? $activity_all_logs : array();
										$activity_all_logs = array_map( 'uat_filter_data_entry', $activity_all_logs, $activity_all_logs );
										if ( $activity_all_logs && ! empty( $activity_all_logs ) ) :
											$accordion_labels 	= array();
											$accordion_logs 		= array();
											foreach ( $activity_all_logs as $log_data ) :
												$accordion_logs[ $log_data['post_id'] ][] = $log_data;
											endforeach;
											foreach ( $accordion_logs as $_post_id => $log_array ) :
												$uat_controller->moove_remove_old_logs( $_post_id );
												if ( isset( $accordion_logs[ $_post_id ] ) ) :
													$permalink = get_permalink( $_post_id );
													?>
													<a class="moove-accordion-section-title" href="#moove-accordion-<?php echo intval( $_post_id ); ?>">
														<?php echo get_the_title( $_post_id ); ?>
													</a>

													<div id="moove-accordion-<?php echo intval( $_post_id ); ?>" class="moove-accordion-section-content">
														<div class="view_post">
															<strong><?php esc_html_e( 'Permalink', 'user-activity-tracking-and-log' ); ?>:</strong>
															<span><a href="<?php echo $permalink; ?>"><strong><?php echo $permalink; ?></strong></a></span>
															<br /><br />
														</div>
														<!--  .view_post -->
														<div class="uat-responsive-table-wrap">
															<table class="moove-activity-log-table-<?php echo intval( $_post_id ); ?> wp-list-table widefat fixed striped" id="moove-activity-log-table-<?php echo intval( $_post_id ); ?>">
																<?php 
																$t_head 		= '';
																if ( count( $log_array ) ) :
																	$log_array           = isset( $log_array ) ? $log_array : array();
																	$untouched_log_array = $log_array;
																	$selected_date       = $selected_date ? $selected_date : '';
																	$date_filter_content = $uat_controller->moove_get_activity_dates( $untouched_log_array, $selected_date );
																	if ( $custom_order ) :
																		if ( is_array( $log_array ) ) :
																			usort( $log_array, array( new Moove_Activity_Array_Order( $_orderby ), 'custom_order' ) );
																			if ( 'asc' === $_order ) :
																				$log_array = array_reverse( $log_array );
																			endif;
																		endif;
																	else :
																		if ( is_array( $log_array ) ) :
																			usort( $log_array, 'moove_desc_sort' );
																		endif;
																	endif;

																	$log_array_count = count( $log_array );
																	$original_array  = $log_array;

																	$post_per_page = isset( $user_options['wp_screen_options']['value'] ) && intval( $user_options['wp_screen_options']['value'] ) ? intval( $user_options['wp_screen_options']['value'] ) : 10;
																	$max_num_pages = ceil( count( $log_array ) / $post_per_page );
																	if ( isset( $_GET['offset'] ) ) :
																		$offset = intval( $_GET['offset'] );
																	else :
																		$offset = 1;
																	endif;

																	$log_array 	= $uat_controller->moove_pagination( $log_array, $offset, $post_per_page );
																	$t_head 		= '';
																	?>

																	<thead>
																		<?php ob_start(); ?>
																		<tr>
																			<?php 
																				$page_url = remove_query_arg( array( 'orderby', 'order' ), false ); 
																				$page_url = strtok( $page_url, "#" );
																				$current_order  = moove_activity_current_order( 'time', $custom_order, $_order, $_orderby );
																				$reversed_order = 'asc' === $current_order ? 'desc' : 'asc';
																			?>
																			<th class="manage-column column-date column-primary sortable <?php echo esc_attr( $current_order ); ?>">
																				<a href="
																					<?php
																						echo esc_url(
																							add_query_arg(
																								array(
																									'orderby' => 'time',
																									'order'   => $reversed_order,
																								),
																								$page_url
																							)
																						);
																					?>#moove-accordion-<?php echo $_post_id; ?>
																					">
																					<span><?php esc_html_e( 'Date / Time', 'user-activity-tracking-and-log' ); ?></span>
																					<span class="sorting-indicator"></span>
																				</a>
																			</th>

																			<?php
																				$current_order  = moove_activity_current_order( 'display_name', $custom_order, $_order, $_orderby );
																				$reversed_order = 'asc' === $current_order ? 'desc' : 'asc';
																			?>
																			<th class="column-user manage-column sortable <?php echo esc_attr( $current_order ); ?> <?php echo ( isset( $user_options['user-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
																				<a href="
																					<?php
																						echo esc_url(
																							add_query_arg(
																								array(
																									'orderby' => 'display_name',
																									'order'   => $reversed_order,
																								),
																								$page_url
																							)
																						);
																						?>#moove-accordion-<?php echo $_post_id; ?>
																					">
																					<span><?php esc_html_e( 'Display Name', 'user-activity-tracking-and-log' ); ?></span>
																					<span class="sorting-indicator"></span>
																				</a>
																			</th>

																			<?php
																				$current_order  = moove_activity_current_order( 'response_status', $custom_order, $_order, $_orderby );
																				$reversed_order = 'asc' === $current_order ? 'desc' : 'asc';
																			?>
																			<th class="column-activity manage-column sortable <?php echo esc_attr( $current_order ); ?> <?php echo ( isset( $user_options['activity-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
																				<a href="
																					<?php
																						echo esc_url(
																							add_query_arg(
																								array(
																									'orderby' => 'response_status',
																									'order'   => $reversed_order,
																								),
																								$page_url
																							)
																						);
																					?>#moove-accordion-<?php echo $_post_id; ?>
																					">
																					<span><?php esc_html_e( 'Activity', 'user-activity-tracking-and-log' ); ?></span>
																					<span class="sorting-indicator"></span>
																				</a>
																			</th>

																			<?php
																				$current_order  = moove_activity_current_order( 'ip_address', $custom_order, $_order, $_orderby );
																				$reversed_order = 'asc' === $current_order ? 'desc' : 'asc';
																			?>
																			<th class="column-ip manage-column sortable <?php echo esc_attr( $current_order ); ?> <?php echo ( isset( $user_options['ip-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
																				<a href="
																					<?php
																						echo esc_url(
																							add_query_arg(
																								array(
																									'orderby' => 'ip_address',
																									'order'   => $reversed_order,
																								),
																								$page_url
																							)
																						);
																					?>#moove-accordion-<?php echo $_post_id; ?>
																					">
																					<span><?php esc_html_e( 'IP address', 'user-activity-tracking-and-log' ); ?></span>
																					<span class="sorting-indicator"></span>
																				</a>
																			</th>

																			<?php
																			$loc_enabled = apply_filters( 'uat_show_location_by_ip', true );
																			if ( $loc_enabled ) :
																				$current_order  = moove_activity_current_order( 'city', $custom_order, $_order, $_orderby );
																				$reversed_order = 'asc' === $current_order ? 'desc' : 'asc';
																				?>
																				<th class="column-city manage-column sortable <?php echo esc_attr( $current_order ); ?> <?php echo ( isset( $user_options['city-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
																					<a href="
																						<?php
																							echo esc_url(
																								add_query_arg(
																									array(
																										'orderby' => 'city',
																										'order'   => $reversed_order,
																									),
																									$page_url
																								)
																							);
																						?>#moove-accordion-<?php echo $_post_id; ?>
																						">
																						<span><?php esc_html_e( 'Location', 'user-activity-tracking-and-log' ); ?></span>
																						<span class="sorting-indicator"></span>
																					</a>
																				</th>
																				<?php
																			endif;

																			$current_order  = moove_activity_current_order( 'referer', $custom_order, $_order, $_orderby );
																			$reversed_order = 'asc' === $current_order ? 'desc' : 'asc';
																			?>
																			<th class="column-referrer manage-column sortable <?php echo esc_attr( $current_order ); ?> <?php echo ( isset( $user_options['referrer-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
																				<a href="
																					<?php
																						echo esc_url(
																							add_query_arg(
																								array(
																									'orderby' => 'referer',
																									'order'   => $reversed_order,
																								),
																								$page_url
																							)
																						);
																					?>#moove-accordion-<?php echo $_post_id; ?>
																					">
																					<span><?php esc_html_e( 'Referrer', 'user-activity-tracking-and-log' ); ?></span>
																					<span class="sorting-indicator"></span>
																				</a>
																			</th>
																		</tr>
																		<?php 
																			$t_head = ob_get_clean();
																			echo $t_head;
																		?>
																	</thead>
																	<tbody>
																		<?php	foreach ( $log_array as $key => $log_entry ) : ?>
																			<tr>
																				<td class="column-date">
																					<?php echo esc_attr( moove_activity_convert_date( $selected_val, $log_entry['time'], $screen_options ) ); ?>
																				</td>

																				<td class="column-user <?php echo ( isset( $user_options['user-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
																					<?php echo esc_attr( $log_entry['display_name'] ); ?>
																				</td>

																				<td class="column-activity <?php echo ( isset( $user_options['activity-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
																					<span style="color:green;"><?php echo esc_attr( $log_entry['response_status'] ); ?></span>
																				</td>

																				<td class="column-ip <?php echo ( isset( $user_options['ip-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
																					<?php echo esc_attr( $log_entry['ip_address'] ); ?>
																				</td>

																				<?php
																					$loc_enabled = apply_filters( 'uat_show_location_by_ip', true );
																					if ( $loc_enabled ) :
																						?>
																							<td class="column-city <?php echo ( isset( $user_options['city-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
																								<?php echo esc_attr( $log_entry['city'] ); ?>
																							</td>
																						<?php
																					endif;
																				?>

																				<td class="column-referrer <?php echo ( isset( $user_options['referrer-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>">
																					<?php echo wp_kses( moove_activity_get_referrer_link_by_url( $log_entry['referer'] ), wp_kses_allowed_html( 'post' ) ); ?>
																				</td>
																			</tr>
																		<?php endforeach; ?>
																	</tbody>
																	<tfoot>
																		<?php echo $t_head; ?>
																	</tfoot>
																<?php else : // log is empty. ?>
																	<tbody id="the-list">
																		<tr class="no-items">
																			<td class="colspanchange">
																				<?php esc_html_e( 'No posts found', 'user-activity-tracking-and-log' ); ?>
																			</td>
																		</tr>
																	</tbody>
																<?php endif; // log is not empty. ?>
															</table>
														</div>
														<!-- .uat-responsive-table-wrap -->
														<br>
														<?php 
														if ( $max_num_pages !== $offset ) : 
															?>
																<a href="<?php echo esc_url( $page_url ); ?>" class="uat-orange-bnt uat-button load-more" data-max="<?php echo esc_attr( $max_num_pages ); ?>" data-offset="<?php echo esc_attr( $offset ); ?>">
																	<span class="dashicons dashicons-image-filter"></span>
																	<?php esc_html_e( 'Load more', 'user-activity-tracking-and-log' ); ?>
																</a>
															<?php
														endif;
														$settings_perm = apply_filters( 'uat_log_settings_capability', 'manage_options' );
														if ( current_user_can( $settings_perm ) ) :
															?>
																<a href="<?php echo esc_url( $page_url ); ?>" class="uat-brown-bnt uat-button clear-log" data-pid="<?php echo intval( $_post_id ); ?>">
																	<span class="dashicons dashicons-trash"></span>
																	<?php esc_html_e( 'Delete Logs', 'user-activity-tracking-and-log' ); ?>
																</a>
															<?php
														endif;
														?>
													</div>
													<!-- accordion-section-content-->
													<?php
												endif;
												$log_enabled++;
											endforeach;
										else : // no post found.
											// esc_html_e( 'No results were found', 'user-activity-tracking-and-log' );
											$log_enabled = false;
										endif;
										// Check if there is no posts found with logging enabled.
										if ( ! $log_enabled ) :
											?>
											<div class="uat-responsive-table-wrap">
												<table class="wp-list-table widefat fixed striped">
													<thead>
														<tr>
															<th class="column-date"><?php esc_html_e( 'Date / Time', 'user-activity-tracking-and-log' ); ?></th>
															<th class="column-user <?php echo ( isset( $user_options['user-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>"><?php esc_html_e( 'Display Name', 'user-activity-tracking-and-log' ); ?></th>
															<th class="column-activity <?php echo ( isset( $user_options['activity-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>"><?php esc_html_e( 'Activity', 'user-activity-tracking-and-log' ); ?></th>
															<th class="column-ip <?php echo ( isset( $user_options['ip-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>"><?php esc_html_e( 'IP address', 'user-activity-tracking-and-log' ); ?></th>
															<?php
															$loc_enabled = apply_filters( 'uat_show_location_by_ip', true );
															if ( $loc_enabled ) :
																?>
																<th class="column-city <?php echo ( isset( $user_options['city-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>"><?php esc_html_e( 'Location', 'user-activity-tracking-and-log' ); ?></th>
																<?php
															endif;
															?>
															<th class="column-referrer <?php echo ( isset( $user_options['referrer-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>"><?php esc_html_e( 'Referrer', 'user-activity-tracking-and-log' ); ?></th>
														</tr>
													</thead>
													<tbody id="the-list">
														<tr class="no-items"><td class="colspanchange" colspan="6"><?php esc_html_e( 'No posts found', 'user-activity-tracking-and-log' ); ?></td></tr>
													</tbody>

													<tfoot>
														<tr>
															<th class="column-date"><?php esc_html_e( 'Date / Time', 'user-activity-tracking-and-log' ); ?></th>
															<th class="column-user <?php echo ( isset( $user_options['user-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>"><?php esc_html_e( 'Display Name', 'user-activity-tracking-and-log' ); ?></th>
															<th class="column-activity <?php echo ( isset( $user_options['activity-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>"><?php esc_html_e( 'Activity', 'user-activity-tracking-and-log' ); ?></th>
															<th class="column-ip <?php echo ( isset( $user_options['ip-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>"><?php esc_html_e( 'IP address', 'user-activity-tracking-and-log' ); ?></th>
															<?php
															$loc_enabled = apply_filters( 'uat_show_location_by_ip', true );
															if ( $loc_enabled ) :
																?>
																<th class="column-city <?php echo ( isset( $user_options['city-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>"><?php esc_html_e( 'Location', 'user-activity-tracking-and-log' ); ?></th>
															<?php endif; ?>

															<th class="column-referrer <?php echo ( isset( $user_options['referrer-hide'] ) || ! is_array( $user_options ) ) ? '' : 'hidden'; ?>"><?php esc_html_e( 'Referrer', 'user-activity-tracking-and-log' ); ?></th>
														</tr>
													</tfoot>
												</table>
											</div>
											<!-- .uat-responsive-table-wrap -->
											<?php
										endif;
										?>
									</div>
									<!-- accordion-section-->
								</div>
								<!-- accordion-->
							</div>
							<!-- moove-accordion-cnt -->
							<?php
						else :
							?>
							<a href="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>?page=moove-activity-log&tab=all_logs" class="uat-orange-bnt">Load Activity Logs</a>
							<?php
						endif;
					endif; // post types.
				else :
					do_action( 'uat_activity_log_restriction_content', $active_tab );
				endif;
			else :
				if ( current_user_can( $activity_perm ) ) :
					do_action( 'uat_extend_activity_screen_table', $active_tab );
				else :
					do_action( 'uat_activity_log_restriction_content', $active_tab );
				endif;
			endif;

	?>
		<div class="load-more-container"></div>
		<?php
		$content = array(
			'tab'  => $active_tab,
			'data' => $data,
		);
		if ( current_user_can( $settings_perm ) ) :
			do_action( 'moove_activity_tab_content', $content, $active_tab );
		else :
			do_action( 'uat_log_settings_restriction_content', $active_tab );
		endif;
		?>
	</div><!-- .moove-activity-log-report -->
</div>
<!-- moove-form-container -->

</div>
<!--  .uat-tab-section-cnt -->
<?php
$view_cnt = new Moove_Activity_View();
echo wp_kses( $view_cnt->load( 'moove.admin.settings.plugin-boxes', array() ), wp_kses_allowed_html( 'post' ) );
?>

</div>
<!-- wrap -->

<div class="uat-admin-popup uat-admin-popup-clear-log-confirm" style="display: none;">
	<span class="uat-popup-overlay"></span>
	<div class="uat-popup-content">
		<div class="uat-popup-content-header">
			<a href="#" class="uat-popup-close"><span class="dashicons dashicons-no-alt"></span></a>
		</div>
		<!--  .uat-popup-content-header -->
		<div class="uat-popup-content-content">
			<?php if ( 'all_logs' === $active_tab ) : ?>
				<h4><strong>Please confirm that you would like to <br> <span class="uat-h">delete all logs</span></strong></h4>
				<br>
				<button class="button button-primary button-clear-log-confirm-confirm clear-all-logs">
					<?php esc_html_e( 'Delete All Logs', 'import-uat-feed' ); ?>
				</button>
				<?php else : ?>
					<h4><strong>Please confirm that you would like to <span class="uat-h">delete logs</span> associated with this post</strong></h4>
					<br>
					<button class="button button-primary button-clear-log-confirm-confirm clear-all-logs">
						<?php esc_html_e( 'Delete Logs', 'import-uat-feed' ); ?>
					</button>
				<?php endif; ?>
			</div>
			<!--  .uat-popup-content-content -->    
		</div>
		<!--  .uat-popup-content -->
	</div>
	<!--  .uat-admin-popup -->

	<script type="text/javascript" src="<?php echo esc_url( moove_activity_get_plugin_dir() ); ?>/assets/js/moove_activity_backend_select2.js"></script>
