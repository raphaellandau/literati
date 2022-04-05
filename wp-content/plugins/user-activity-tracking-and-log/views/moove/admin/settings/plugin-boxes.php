<?php
/**
 * Plugin Boxes Doc Comment
 *
 * @category  Views
 * @package   user-activity-tracking
 * @author    Moove Agency
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$activity_controller = new Moove_Activity_Controller();
$plugin_details      = $activity_controller->get_plugin_details( 'user-activity-tracking-and-log' );
?>
<div class="moove-uat-plugins-info-boxes">

	<?php ob_start(); ?>
	<div class="m-plugin-box m-plugin-box-highlighted">
		<div class="box-header">
			<h4>Premium Add-On</h4>
		</div>
		<!--  .box-header -->
		<div class="box-content">
			<ul class="plugin-features">
				<li><strong>NEW Time-tracking:</strong> see the duration of user visits</li>
				<li><strong>NEW Event-tracking:</strong> setup event triggers for button clicks, PDF downloads and more</li>			
				<li>Keep logs for up to 4 years </li>
				<li>Anonymize IP addresses (GDPR)</li>
				<li>Export logs to CSV</li>
				<li>Filter activity by user</li>
				<li>Track logged-in users only</li>
				<li>Exclude users from tracking by user role</li>
				<li>Rest API to see activity logs in JSON format</li>
				<li>Set timezone</li>
			</ul>
			<hr />
			<strong>Buy now for only <span>£49</span></strong>
			<a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log/" target="_blank" class="plugin-buy-now-btn">Buy Now</a>
		</div>
		<!--  .box-content -->
	</div>
	<!--  .m-plugin-box -->
	<?php
		$premium_box = apply_filters( 'uat_premium_section', ob_get_clean() );
		echo wp_kses( $premium_box, wp_kses_allowed_html( 'post' ) );
	?>

	<?php $support_class = $premium_box ? '' : 'm-plugin-box-highlighted'; ?>

	<div class="m-plugin-box m-plugin-box-support <?php echo esc_attr( $support_class ); ?>">
		<div class="box-header">
			<h4>Need Support or New Feature?</h4>
		</div>
		<!--  .box-header -->
		<div class="box-content">
			<?php
			$forum_link = apply_filters( 'uat_forum_section_link', 'https://support.mooveagency.com/forum/user-activity-tracking-and-log/' );
			?>
			<div class="uat-faq-forum-content">

				<p><span class="uat-chevron-left">&#8250;</span> Create a support ticket or request new features in our <a href="<?php echo esc_url( $forum_link ); ?>" target="_blank">Support Forum</a></p>
			</div>
			<!--  .uat-faq-forum-content -->
			<span class="uat-review-container" >
				<a href="<?php echo esc_url( $forum_link ); ?>#new-post" target="_blank" class="uat-review-bnt ">Create Support Ticket</a>
			</span>
		</div>
		<!--  .box-content -->
	</div>
	<!--  .m-plugin-box -->
	<div class="m-plugin-box">
		<div class="box-header">
			<h4>Help to improve this plugin!</h4>
		</div>
		<!--  .box-header -->
		<div class="box-content">
			<p>Love this plugin? <br />Help us by <a href="https://wordpress.org/support/plugin/user-activity-tracking-and-log/reviews/?rate=5#new-post" class="uat_admin_link" target="_blank">rating this plugin on wordpress.org.</a></p>
			<hr />
			<?php if ( $plugin_details ) : ?>
				<div class="plugin-stats">
					<div class="plugin-downloads">
						Downloads: <strong><?php echo number_format( $plugin_details->downloaded, 0, '', ',' ); ?></strong>
					</div>
					<!--  .plugin-downloads -->
					<div class="plugin-active-installs">
						Active installations: <strong><?php echo number_format( $plugin_details->active_installs, 0, '', ',' ); ?>+</strong>
					</div>
					<!--  .plugin-downloads -->
					<div class="plugin-rating">
						<?php
						$rating_val = $plugin_details->rating * 5 / 100;
						if ( $rating_val > 0 ) :
							$args   = array(
								'rating' => $rating_val,
								'number' => $plugin_details->num_ratings,
								'echo'   => false,
							);
							$rating = wp_star_rating( $args );
						endif;
						?>
						<?php if ( $rating ) : ?>
							<?php echo wp_kses( $rating, wp_kses_allowed_html( 'post' ) ); ?>
						<?php endif; ?>
					</div>
					<!--  .plugin-rating -->
				</div>
				<!--  .plugin-stats -->
			<?php endif; ?>
		</div>
		<!--  .box-content -->
	</div>
	<!--  .m-plugin-box -->

</div>
<!--  .moove-plugins-info-boxes -->
