<?php
/**
 * Recipient customer new account email
 *
 * @author  James Allan
 * @package WooCommerce Subscriptions Gifting/Templates/Emails
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'שלום,', 'woocommerce-subscriptions-gifting' ) ); ?></p>
<p><?php printf( esc_html__( 'איזה כיף לך! %1$s שלח לך במתנה מנוי ספרים בליטרטי!', 'woocommerce-subscriptions-gifting' ), wp_kses( $subscription_purchaser, wp_kses_allowed_html( 'user_description' ) ), esc_html( $blogname ) ); ?>
</p>

<p><?php printf( esc_html__( 'אנחנו בליטרטי כבר יצרנו לך חשבון באתר. שם המשתמש שלך הוא: %s', 'woocommerce-subscriptions-gifting' ), '<strong>' . esc_html( $user_login ) . '</strong>' ); ?></p>
<p><a class="link" href="<?php echo esc_url( add_query_arg( array( 'key' => $reset_key, 'id' => $user_id ), wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) ) ); ?>">
	<?php esc_html_e( 'כדי להתחיל, עליך לבחור לך סיסמה חדשה כאן.', 'woocommerce-subscriptions-gifting' ); ?></a></p>

<p><?php printf( esc_html__( 'בשלב הבא, יש להיכנס אל החשבון עם הפרטים החדשים. חשוב! כדי שהמערכת שלנו תדע להמשיך איתך הלאה, תהיו חייבים להתחבר לחשבון עם הסיסמה החדשה שלכם. אנחנו נשאל אותך כל מיני דברים על הספרים שאת/ה אוהב/ת או רוצה לקרוא, ומדי חודש נשלח לך הביתה ספר משובח שנבחר במיוחד בשבילך. אם אינך מצליח\ה להיכנס אל החשבון, יש ללחוץ כאן: %s.', 'woocommerce-subscriptions-gifting' ),
'<a href="' . esc_url( wc_get_endpoint_url( 'new-recipient-account', '', wc_get_page_permalink( 'myaccount' ) ) ) . '">' . esc_html__( 'אל החשבון שלי', 'woocommerce-subscriptions-gifting' ) . '</a>' ); ?></p>




<?php do_action( 'woocommerce_email_footer', $email ); ?>
