<?php
/**
 * Recipient new subscription(s) notification email
 *
 * @author James Allan
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'שלום,', 'woocommerce-subscriptions-gifting' ) ); ?></p>
<p><?php printf( esc_html__( 'איזה כיף לך! %1$s שלח לך במתנה מנוי ספרים בליטרטי!', 'woocommerce-subscriptions-gifting' ), wp_kses( $subscription_purchaser, wp_kses_allowed_html( 'user_description' ) ), esc_html( _n( 'a subscription', 'subscriptions', count( $subscriptions ), 'woocommerce-subscriptions-gifting' ) ), esc_html( $blogname ) ); ?>
<?php printf( esc_html__( ' פרטי המנוי שלך מופיעים בטבלה כאן למטה.', 'woocommerce-subscriptions-gifting' ), esc_html( _n( 'subscription', 'subscriptions', count( $subscriptions ), 'woocommerce-subscriptions-gifting' ) ) ); ?>
</p>
<?php

$new_recipient = get_user_meta( $recipient_user->ID, 'wcsg_update_account', true );

if ( 'true' == $new_recipient ) : ?>

<p><?php esc_html_e( 'עוד רגע תקבל\י מייל ובו כל הפרטים הנדרשים לכניסה אל החשבון שלך, כדי שאנחנו בליטרטי נוכל להתחיל לשלוח אליך ספר חדש ומפתיע בכל חודש.', 'woocommerce-subscriptions-gifting' ); ?></p>

<?php else : ?>

<p><?php printf( esc_html__( 'כדי להתחיל להשתמש ב %1$s שלך יש להיכנס לכאן: %2$sהחשבון שלי%3$s.', 'woocommerce-subscriptions-gifting' ),
	esc_html( _n( 'subscription', 'subscriptions', count( $subscriptions ), 'woocommerce-subscriptions-gifting' ) ),
	'<a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">',
	'</a>'
); ?></p>

<?php endif;

foreach ( $subscriptions as $subscription_id ) {
	$subscription = wcs_get_subscription( $subscription_id );

	do_action( 'wcs_gifting_email_order_details', $subscription, $sent_to_admin, $plain_text, $email );

	if ( is_callable( array( 'WC_Subscriptions_Email', 'order_download_details' ) ) ) {
		WC_Subscriptions_Email::order_download_details( $subscription, $sent_to_admin, $plain_text, $email );
	}
}

do_action( 'woocommerce_email_footer', $email );
