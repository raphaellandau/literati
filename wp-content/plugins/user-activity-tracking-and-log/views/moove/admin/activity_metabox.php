<?php
  $ma_data = $data['activity'];

?>
<div class="ma-metabox-wrapper">
    <span class="ma-global-protection">
      <strong><?php _e('Global settings','user-activity-tracking-and-log'); ?>:</strong>
      <i><?php echo ( intval( $data['global_setup'] ) !== 0 ) ? __('Enabled','user-activity-tracking-and-log') : __('Disabled','user-activity-tracking-and-log'); ?></i>
    </span>

    <div class="ma-trigger-campaign">
        <?php if (isset($ma_data['campaign_id']) && $ma_data['campaign_id'] != ''): ?>
            <?php _e('Activity Session ID','user-activity-tracking-and-log'); ?>: <b><?php echo $ma_data['campaign_id'] ?></b>
            <div class="uncheck" style="float: right;">
                <label for="ma-delete-campaign">
                  <?php _e('Disable tracking and delete log data for this page'); ?>
                  <input type="checkbox" id="ma-delete-campaign" name="ma-delete-campaign" class="ma-checkbox" value="1">
                </label>
            </div>
        <?php else : ?>
            <label for="ma-trigger-campaign">
              <input type="checkbox" id="ma-trigger-campaign" name="ma-trigger-campaign" value="1">
              <?php _e('Create activity tracking session for this post','user-activity-tracking-and-log'); ?>
            </label>

        <?php endif; ?>
    </div>
    <div class="ma-log">
        <?php if (isset($ma_data['log']) && count($ma_data['log']) > 0): ?>
            <table class="ma-table wp-list-table widefat fixed striped">
               <thead>
               <tr>
                   <td><?php _e('Time','user-activity-tracking-and-log'); ?></td>
                   <td><?php _e('User','user-activity-tracking-and-log'); ?></td>
                   <td><?php _e('Activity','user-activity-tracking-and-log'); ?></td>
                   <td><?php _e('IP Address','user-activity-tracking-and-log'); ?></td>
                   <?php 
                      $loc_enabled     = apply_filters('uat_show_location_by_ip', true); 
                      if ( $loc_enabled ) :
                        ?>
                        <td><?php _e('Location','user-activity-tracking-and-log'); ?></td>
                        <?php 
                      endif; 
                    ?>
                   <td><?php _e('Referrer','user-activity-tracking-and-log'); ?></td>
               </tr>
               </thead>
               <tbody>
               <?php 
                $screen_options   = get_user_meta( get_current_user_id(), 'moove_activity_screen_options', true );
                $selected_val     = isset( $screen_options['moove-activity-dtf'] ) ? $screen_options['moove-activity-dtf'] : 'a';
               foreach ($ma_data['log'] as $log_entry): ?>
                   <tr>
                       <td><?php echo moove_activity_convert_date( $selected_val, $log_entry['time'], $screen_options ); ?></td>
                       <td><?php echo $log_entry['display_name']; ?></td>
                       <td>
                         <span style="color:green;"><?php echo $log_entry['response_status']; ?></span>
                       </td>
                      <td><?php echo $log_entry['show_ip']; ?></td>
                      <?php 
                      $loc_enabled     = apply_filters('uat_show_location_by_ip', true); 
                      if ( $loc_enabled ) : ?>
                      <td><?php echo $log_entry['city']; ?></td>
                      <?php endif; ?>
                      <td><?php echo wp_kses( moove_activity_get_referrer_link_by_url( $log_entry['referer'] ), wp_kses_allowed_html( 'post' ) ); ?></td>
                   </tr>
               <?php endforeach; ?>
               </tbody>
            </table>
            <br />
            <a href="<?php echo admin_url( 'admin.php?page=moove-activity-log&tab='.get_post_type( get_the_ID() ).'#moove-accordion-' . get_the_ID() ); ?>" target="_blank" class="button button-secondary">More details</a>
        <?php else : ?>
            <?php _e("You don't have any entries in this log yet.","user-activity-tracking-and-log"); ?>
        <?php endif; ?>
    </div>
</div>
<div class="uat-admin-popup uat-admin-popup-clear-log-confirm" style="display: none;">
  <span class="uat-popup-overlay"></span>
  <div class="uat-popup-content">
    <div class="uat-popup-content-header">
      <a href="#" class="uat-popup-close"><span class="dashicons dashicons-no-alt"></span></a>
    </div>
    <!--  .uat-popup-content-header -->
    <div class="uat-popup-content-content">
      <h4><strong>Please confirm that you would like to <strong>disable tracking</strong> for this page and <strong>delete all logs</strong> associated with this page</strong></h4><br>
      <button class="button button-primary button-disable-tracking-individual-post">
        <?php _e('Disable & Delete All Logs','import-uat-feed'); ?>
      </button>
    </div>
    <!--  .uat-popup-content-content -->    
  </div>
  <!--  .uat-popup-content -->
</div>
<!--  .uat-admin-popup -->
