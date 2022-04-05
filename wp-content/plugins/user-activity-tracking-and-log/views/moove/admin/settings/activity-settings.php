<?php
/**
 * Activity Screen Settings Doc Comment
 *
 * @category  Views
 * @package   user-activity-tracking
 * @author    Moove Agency
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>

<h2><?php esc_html_e( 'Activity Tracking Settings', 'user-activity-tracking-and-log' ); ?></h2>

<hr>
<p class="description" style="font-size: 14px; margin: 15px 0 20px;"><?php esc_html_e( 'Here you can set the activity tracking preferences by content type.', 'user-activity-tracking-and-log' ); ?></p>
<?php
if ( isset( $_POST ) && isset( $_POST['moove_uat_nonce'] ) ) :
	$nonce = sanitize_key( $_POST['moove_uat_nonce'] );
	if ( ! wp_verify_nonce( $nonce, 'moove_uat_nonce_field' ) ) :
		die( 'Security check' );
		else :
			if ( is_array( $_POST ) && isset( $_POST['uat_act_type'] ) && is_array( $_POST['uat_act_type'] ) ) :
				$post_types = array_map( 'sanitize_text_field', wp_unslash( $_POST['uat_act_type'] ) );
				foreach ( $post_types as $_post_type ) :
					$_post_type_name                              = sanitize_text_field( wp_unslash( $_post_type ) );
					$activity_settings_option[ $_post_type_name ] = isset( $_POST[ 'uat_act_' . $_post_type_name ] ) ? '1' : '0';
					$activity_settings_option[ $_post_type_name . '_transient' ] = isset( $_POST[ 'uat_act_' . $_post_type_name . '_transient' ] ) && intval( $_POST[ 'uat_act_' . $_post_type_name . '_transient' ] ) ? intval( $_POST[ 'uat_act_' . $_post_type_name . '_transient' ] ) : 7;
				endforeach;
				update_option( 'moove_post_act', $activity_settings_option );
				?>
				<script>location.reload(true);</script>
				<?php
			endif;
		endif;
	elseif ( isset( $_POST ) && isset( $_POST['moove_reset_uat_nonce'] ) && isset( $_POST['uat-reset-settings'] ) && intval( $_POST['uat-reset-settings'] ) === 1 ) :
		$nonce = sanitize_key( $_POST['moove_reset_uat_nonce'] );
		if ( ! wp_verify_nonce( $nonce, 'moove_reset_uat_nonce_field' ) ) :
			die( 'Security check' );
		else :
			delete_option( 'moove_post_act' );
			delete_option( 'moove-activity-timezone-offset' );
			delete_option( 'moove_tracking_settings_act' );
			delete_option( 'uat_log_permissions' );
			delete_option( 'uat_settings_permissions' );

			moove_set_options_values();
			delete_user_meta( get_current_user_id(), 'moove_activity_screen_options' );
		endif;
	endif;

	$activity_settings_option = get_option( 'moove_post_act' );
	$activity_settings        = array();
	$_post_types              = get_post_types( array( 'public' => true ) );
	unset( $_post_types['attachment'] );
	if ( is_array( $_post_types ) ) :
		foreach ( $_post_types as &$_post_type ) :
			$_post_type_object                = get_post_type_object( $_post_type );
			$activity_settings[ $_post_type ] = array(
				'post_type'       => $_post_type,
				'post_type_label' => $_post_type_object->label,
				'transient'       => isset( $activity_settings_option[ $_post_type . '_transient' ] ) ? $activity_settings_option[ $_post_type . '_transient' ] : '7',
				'status'          => isset( $activity_settings_option[ $_post_type ] ) ? $activity_settings_option[ $_post_type ] : '0',
			);
		endforeach;
	endif;
	?>
<br>
<form action="<?php echo esc_url( admin_url( '/admin.php?page=moove-activity-log&tab=activity-settings' ) ); ?>" method="post">
	<?php wp_nonce_field( 'moove_uat_nonce_field', 'moove_uat_nonce' ); ?>
	<table class="form-table uat-activity-settings-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Post Type', 'user-activity-tracking-and-log' ); ?></th>
				<th class="text-center"><?php esc_html_e( 'Status', 'user-activity-tracking-and-log' ); ?></th>
				<th><?php esc_html_e( 'Delete logs older than', 'user-activity-tracking-and-log' ); ?>: </th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $activity_settings as $_post_type => $uat_pt_data ) : ?>
				<tr>
					<th scope="row">
						<span><?php echo esc_attr( $uat_pt_data['post_type_label'] ); ?></span>
					</th>
					<td class="text-center">
						<label class="uat-checkbox-toggle">
							<input type="checkbox" name="uat_act_<?php echo esc_attr( $_post_type ); ?>" <?php echo intval( $uat_pt_data['status'] ) ? 'checked=""' : ''; ?> >
							<span class="uat-checkbox-slider" data-enable="Enabled" data-disable="Disabled"></span>
						</label>
						<input type="hidden" name="uat_act_type[]" value="<?php echo esc_attr( $_post_type ); ?>">
					</td>
					<td>
						<select name="uat_act_<?php echo esc_attr( $_post_type ); ?>_transient" id="<?php echo esc_attr( $_post_type ); ?>_transient" class="moove-activity-log-transient">
						<option value="1" <?php echo isset( $activity_settings_option[ $_post_type . '_transient' ] ) && 1 === intval( $activity_settings_option[ $_post_type . '_transient' ] ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( '1 day', 'user-activity-tracking-and-log' ); ?></option>
						<option value="2" <?php echo isset( $activity_settings_option[ $_post_type . '_transient' ] ) && 2 === intval( $activity_settings_option[ $_post_type . '_transient' ] ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( '2 days', 'user-activity-tracking-and-log' ); ?></option>
						<option value="3" <?php echo isset( $activity_settings_option[ $_post_type . '_transient' ] ) && 3 === intval( $activity_settings_option[ $_post_type . '_transient' ] ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( '3 days', 'user-activity-tracking-and-log' ); ?></option>
						<option value="4"<?php echo isset( $activity_settings_option[ $_post_type . '_transient' ] ) && 4 === intval( $activity_settings_option[ $_post_type . '_transient' ] ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( '4 days', 'user-activity-tracking-and-log' ); ?></option>
						<option value="5" <?php echo isset( $activity_settings_option[ $_post_type . '_transient' ] ) && 5 === intval( $activity_settings_option[ $_post_type . '_transient' ] ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( '5 days', 'user-activity-tracking-and-log' ); ?></option>
						<option value="6" <?php echo isset( $activity_settings_option[ $_post_type . '_transient' ] ) && 6 === intval( $activity_settings_option[ $_post_type . '_transient' ] ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( '6 days', 'user-activity-tracking-and-log' ); ?></option>
						<option value="7" <?php echo isset( $activity_settings_option[ $_post_type . '_transient' ] ) && 7 === intval( $activity_settings_option[ $_post_type . '_transient' ] ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( '1 week', 'user-activity-tracking-and-log' ); ?></option>
						<option value="14" <?php echo isset( $activity_settings_option[ $_post_type . '_transient' ] ) && 14 === intval( $activity_settings_option[ $_post_type . '_transient' ] ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( '2 weeks', 'user-activity-tracking-and-log' ); ?></option>
						<?php do_action( 'moove_activity_delete_options', $activity_settings_option, $_post_type ); ?>
						</select>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<br>
	<button type="submit" class="uat-orange-bnt" method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=moove-activity-log&tab=activity-settings' ) ); ?>">
		<?php esc_html_e( 'Save Settings', 'user-activity-tracking-and-log' ); ?>
	</button>
</form>

<form action="<?php echo esc_url( admin_url( '/admin.php?page=moove-activity-log&tab=activity-settings' ) ); ?>" method="post" class="uat-reset-settings-form">
	<input type="hidden" name="uat-reset-settings" value="1">
	<?php wp_nonce_field( 'moove_reset_uat_nonce_field', 'moove_reset_uat_nonce' ); ?>
	<button type="submit" class="uat-brown-bnt uat-button pullright">
		<?php esc_html_e( 'Reset Settings', 'user-activity-tracking-and-log' ); ?>
	</button>
</form>

<div class="uat-admin-popup uat-admin-popup-reset-settings" style="display: none;">
	<span class="uat-popup-overlay"></span>
	<div class="uat-popup-content">
		<div class="uat-popup-content-header">
			<a href="#" class="uat-popup-close"><span class="dashicons dashicons-no-alt"></span></a>
		</div>
		<!--  .uat-popup-content-header -->
		<div class="uat-popup-content-content">
			<h4><strong>Please confirm that you would like to reset the plugin settings to the default state</strong></h4>
			<br>
			<button class="button button-primary button-reset-settings-confirm-confirm">
				<?php esc_html_e( 'Reset Settings', 'import-uat-feed' ); ?>
			</button>
		</div>
		<!--  .uat-popup-content-content -->    
	</div>
	<!--  .uat-popup-content -->
</div>
<!--  .uat-admin-popup -->
