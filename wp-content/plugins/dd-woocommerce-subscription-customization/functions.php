<?php
/**
 * Plugin Name: WooCommerce Subscriptions Customization
 * Description: WooCommerce Subscriptions Customization
 * version: 1.0.0
 * Plugin URI: https://codecanyon.net/user/devdiggers/portfolio
 * Author: DevDiggers
 * Author URI: https://codecanyon.net/user/devdiggers
 * Domain Path: /i18n
 * Text Domain: ddwc-subscription-customization
 * WC requires at least: 3.0.0
 * WC tested up to: 5.6.x
 * WP requires at least: 4.0.0
 * WP tested up to: 5.8.x
 *
 * @package WooCommerce Subscriptions Customization
 */

// DDWCSC: DevDiggers WooCommerce Subscriptions Customization.
defined( 'ABSPATH' ) || exit();

// Define Constants.
defined( 'DDWCSC_PLUGIN_FILE' ) || define( 'DDWCSC_PLUGIN_FILE', plugin_dir_path( __FILE__ ) );
defined( 'DDWCSC_PLUGIN_URL' ) || define( 'DDWCSC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
defined( 'DDWCSC_SCRIPT_VERSION' ) || define( 'DDWCSC_SCRIPT_VERSION', '1.0.0' );

if ( ! function_exists( 'ddwcscincludes' ) ) {
    function ddwcsc_email_content_type() {
        return 'text/html';
    }

	/**
	 * Includes function
	 *
	 * @return void
	 */
	function ddwcscincludes() {

		load_plugin_textdomain( 'ddwc-subscription-customization', false, basename( dirname( __FILE__ ) ) . '/i18n' );

		if ( false && ! class_exists( 'DDWCPreOrder\Includes\DDWCPO_File_Handler' ) ) {
			add_action( 'admin_notices', function () {
				?>
				<div class="error">
					<p><?php echo esc_html__( 'WooCommerce Subscriptions Customization is enabled but not effective. It requires WooCommerce Subscriptions in order to use its functionalities.', 'ddwc-subscription-customization' ); ?></p>
				</div>
				<?php
			} );
		} else {

            function ddwcsc_section_options_callback() {
                ?>
                <p><?php esc_html_e( 'This field details will be put in the bottom of the mail send to receiver when some customer gift subsciption to any other user.', 'ddwc-subscription-customization' ) ?></p>
                <?php
            }

            // add_action( 'admin_enqueue_scripts', function() {
            //     wp_enqueue_script( 'ddwcsc-admin-script', DDWCSC_PLUGIN_URL . 'assets/js/admin.js', [], DDWCSC_SCRIPT_VERSION );
            //     wp_enqueue_script( 'tinymce_js', includes_url( 'js/tinymce/' ) . 'wp-tinymce.php', [ 'jquery' ], false, true );
            // } );

            add_action( 'admin_init', function() {
                add_settings_section(  
                    'ddwcsc_gift_extra_detail_section_name', // Section ID 
                    esc_html__( 'Gift Card Mail Details', 'ddwc-subscription-customization' ),
                    'ddwcsc_section_options_callback', // Callback
                    'general' // What Page?  This makes the section show up on the General Settings Page
                );

                add_settings_field(
                    'ddwcsc_receiver_gift_card_content',
                    esc_html__( 'Gift Card Receiver Mail Content', 'ddwc-subscription-customization' ),
                    function() {
                        wp_editor(
                            html_entity_decode( get_option( 'ddwcsc_receiver_gift_card_content' ) ),
                            'ddwcsc_receiver_gift_card_content',
                            array(
                                // 'media_buttons' => false,
                                'textarea_rows' => 8,
                                'textarea_name' => 'ddwcsc_receiver_gift_card_content',
                                'tabindex' => 4,
                                // 'tinymce' => array(
                                //     'theme_advanced_buttons1' => 'bold, italic, ul, pH, temp',
                                // ),
                            )
                        );
                        ?>
                        <!-- <textarea id="ddwcsc_receiver_gift_card_content" name="ddwcsc_receiver_gift_card_content"><?php //echo html_entity_decode( get_option( 'ddwcsc_receiver_gift_card_content' ) ); ?></textarea> -->
                        <p class="description"><?php esc_html_e( 'Placeholders can be used are - {site_title} {site_address} {site_url} {receiver_name} {product_name} {giver_name} {greeting_text} {new_account_text} {new_account_username} {new_account_password_reset_link}', 'ddwc-subscription-customization' ); ?></p>
                        <?php
                    },
                    'general',
                    'ddwcsc_gift_extra_detail_section_name',
                );

                add_settings_field(
                    'ddwcsc_buyer_gift_card_content',
                    esc_html__( 'Gift Card Buyer Mail Content', 'ddwc-subscription-customization' ),
                    function() {
                        wp_editor(
                            html_entity_decode( get_option( 'ddwcsc_buyer_gift_card_content' ) ),
                            'ddwcsc_buyer_gift_card_content',
                            array(
                                // 'media_buttons' => false,
                                'textarea_rows' => 8,
                                'textarea_name' => 'ddwcsc_buyer_gift_card_content',
                                'tabindex' => 4,
                                // 'tinymce' => array(
                                //     'theme_advanced_buttons1' => 'bold, italic, ul, pH, temp',
                                // ),
                            )
                        );
                        ?>
                        <!-- <textarea id="ddwcsc_buyer_gift_card_content" name="ddwcsc_buyer_gift_card_content"><?php //echo html_entity_decode( get_option( 'ddwcsc_buyer_gift_card_content' ) ); ?></textarea> -->

                        <p class="description"><?php esc_html_e( 'Placeholders can be used are - {site_title} {site_address} {site_url} {receiver_name} {product_name} {giver_name} {greeting_text} {new_account_text} {new_account_username} {new_account_password_reset_link}', 'ddwc-subscription-customization' ); ?></p>
                        <?php
                    },
                    'general',
                    'ddwcsc_gift_extra_detail_section_name',
                );

                register_setting( 'general','ddwcsc_receiver_gift_card_content', 'esc_attr' );
                register_setting( 'general','ddwcsc_buyer_gift_card_content', 'esc_attr' );
            } );

            add_action( 'woocommerce_after_order_notes', function( $checkout ) {
                if ( ! WC_Subscriptions_Cart::cart_contains_subscription() ) {
                    return;
                }

                ?>
                <div id="ddwcsc-gift-fields-wrapper">
                    <h2><?php esc_html_e( 'Gift Fields', 'ddwc-subscription-customization' ) ?></h2>

                    <?php
                    woocommerce_form_field( 'ddwcsc_receiver_name', [
                        'type'        => 'text',
                        'required'    => true,
                        'class'       => [ 'form-row-wide' ],
                        'label'       => esc_html__( 'Receiver Name', 'ddwc-subscription-customization' ),
                        'placeholder' => esc_html__( 'Enter name', 'ddwc-subscription-customization' ),
                    ], $checkout->get_value( 'ddwcsc_receiver_name' ) );

                    woocommerce_form_field( 'ddwcsc_receiver_email', [
                        'type'        => 'email',
                        'required'    => true,
                        'class'       => [ 'form-row-wide' ],
                        'label'       => esc_html__( 'Receiver Email', 'ddwc-subscription-customization' ),
                        'placeholder' => esc_html__( 'Enter email', 'ddwc-subscription-customization' ),
                    ], $checkout->get_value( 'ddwcsc_receiver_email' ) );

                    woocommerce_form_field( 'ddwcsc_receiver_message', [
                        'type'        => 'textarea',
                        'required'    => false,
                        'class'       => [ 'form-row-wide' ],
                        'label'       => esc_html__( 'Receiver Message', 'ddwc-subscription-customization' ),
                        'placeholder' => esc_html__( 'Enter message', 'ddwc-subscription-customization' ),
                    ], $checkout->get_value( 'ddwcsc_receiver_message' ) );

                    woocommerce_form_field( 'ddwcsc_sending_date', [
                        'type'        => 'date',
                        'required'    => false,
                        'class'       => [ 'form-row-wide' ],
                        'label'       => esc_html__( 'Sending Date', 'ddwc-subscription-customization' ),
                        'placeholder' => esc_html__( 'Select Date', 'ddwc-subscription-customization' ),
                    ], $checkout->get_value( 'ddwcsc_sending_date' ) );

                    woocommerce_form_field( 'ddwcsc_buyer_mail', [
                        'type'        => 'checkbox',
                        'required'    => false,
                        'class'       => [ 'form-row-wide' ],
                        'label'       => esc_html__( 'I want to print the gift as a gift card', 'ddwc-subscription-customization' ),
                        'placeholder' => '',
                    ], $checkout->get_value( 'ddwcsc_buyer_mail' ) );
                    ?>
                </div>
                <?php
            } );

            // add_action( 'woocommerce_checkout_process', function() {
            //     if ( ! WC_Subscriptions_Cart::cart_contains_subscription() ) {
            //         return;
            //     }

            //     if ( empty( $_POST[ 'ddwcsc_receiver_name' ] ) ) {
            //         wc_add_notice( esc_html__( 'Please enter receiver name for the gift.', 'ddwc-subscription-customization' ), 'error' );
            //     }

            //     if ( empty( $_POST[ 'ddwcsc_receiver_email' ] ) ) {
            //         wc_add_notice( esc_html__( 'Please enter receiver email for the gift.', 'ddwc-subscription-customization' ), 'error' );
            //     }

            //     if ( empty( $_POST[ 'ddwcsc_receiver_message' ] ) ) {
            //         wc_add_notice( esc_html__( 'Please enter receiver message for the gift.', 'ddwc-subscription-customization' ), 'error' );
            //     }

            //     if ( empty( $_POST[ 'ddwcsc_sending_date' ] ) ) {
            //         wc_add_notice( esc_html__( 'Please enter sending date for the gift.', 'ddwc-subscription-customization' ), 'error' );
            //     }
            // } );

            // woocommerce_order_status_processing
            add_action( 'woocommerce_checkout_order_processed', function( $order_id, $posted_data = array() ) {
                if ( ! WC_Subscriptions_Cart::cart_contains_subscription() ) {
                    return;
                }

                $receiver_name    = ! empty( $_POST[ 'ddwcsc_receiver_name' ] ) ? $_POST[ 'ddwcsc_receiver_name' ] : '';
                $receiver_email   = ! empty( $_POST[ 'ddwcsc_receiver_email' ] ) ? $_POST[ 'ddwcsc_receiver_email' ] : '';
                $receiver_message = ! empty( $_POST[ 'ddwcsc_receiver_message' ] ) ? $_POST[ 'ddwcsc_receiver_message' ] : '';
                $sending_date     = ! empty( $_POST[ 'ddwcsc_sending_date' ] ) ? $_POST[ 'ddwcsc_sending_date' ] : current_time( 'Y-m-d' );
                $buyer_mail       = ! empty( $_POST[ 'ddwcsc_buyer_mail' ] ) ? $_POST[ 'ddwcsc_buyer_mail' ] : '';
                $sender_id        = get_current_user_id();
                $sender           = get_userdata( $sender_id );
                $giver_name       = $sender->display_name;

                if ( ! empty( $receiver_email ) ) {
                    if ( ! email_exists( $receiver_email ) ) {
                        $username       = wc_create_new_customer_username( $receiver_email );
                        $password       = wp_generate_password();
                        $email_instance = WC_Emails::instance();
                        remove_action( 'woocommerce_created_customer_notification', array( $email_instance, 'customer_new_account' ), 10, 3 );
                        $user_id  = wc_create_new_customer( $receiver_email, $username, $password );

                        update_post_meta( $order_id, '_ddwcsc_account_created', 'yes' );

                        $account_created = 'yes';
                    } else {
                        $user_id = get_user_by( 'email', $receiver_email )->ID;
                    }

                    update_post_meta( $order_id, '_ddwcsc_receiver_name', $receiver_name );
                    update_post_meta( $order_id, '_ddwcsc_receiver_email', $receiver_email );
                    update_post_meta( $order_id, '_ddwcsc_receiver_message', $receiver_message );
                    update_post_meta( $order_id, '_ddwcsc_sending_date', $sending_date );
                    update_post_meta( $order_id, '_ddwcsc_buyer_mail', $buyer_mail );
                    update_post_meta( $order_id, '_ddwcsc_sender_id', get_current_user_id() );

                    update_user_meta( $user_id, '_ddwcsc_gift_order_id', $order_id );
                    update_user_meta( $user_id, '_ddwcsc_gift_date', $sending_date );

                    $order = wc_get_order( $order_id );

                    $order->set_customer_id( $user_id );

                    $order->save();
                }

                if ( ! empty( $_POST[ 'ddwcsc_buyer_mail' ] ) && ! empty( get_option( 'ddwcsc_buyer_gift_card_content' ) ) ) {
                    $order       = wc_get_order( $order_id );
                    $order_items = $order->get_items();

                    $product_name = '';

                    foreach ( $order_items as $key => $value ) {
                        $item_data    = $value->get_data();
                        $product_name = $item_data[ 'name' ];
                    }

                    $message = html_entity_decode( get_option( 'ddwcsc_buyer_gift_card_content' ) );
                    $headers = [ 'Content-Type: text/html; charset=UTF-8' ];
                    // $headers = [ "Content-Type: text/html\r\n" ];
                    // $headers = array(
                    //     'X-Mailer-Type:WPMailSMTP/Admin/Test',
                    // );

                    $domain = wp_parse_url( home_url(), PHP_URL_HOST );

                    $new_account_text                = '';
                    $new_account_username            = '';
                    $new_account_password_reset_link = '';

                    if ( ! empty( $account_created ) ) {
                        $receiver = get_userdata( $user_id );

                        $new_account_text = esc_html__( 'text for in case a new account had been created', 'ddwc-subscription-customization' );
                        $new_account_username = sprintf( esc_html__( 'Your username: %s', 'ddwc-subscription-customization' ), $receiver->user_login );

                        $object = new \WP_User( $user_id );

                        // Generate a magic link so user can set initial password.
                        $key = get_password_reset_key( $object );
                        if ( ! is_wp_error( $key ) ) {
                            $action                 = 'newaccount';
                            $new_account_password_reset_link = wc_get_account_endpoint_url( 'lost-password' ) . "?action=$action&key=$key&login=" . rawurlencode( $object->user_login );
                            $new_account_password_reset_link = sprintf( esc_html__( 'Your password reset link: %s', 'ddwc-subscription-customization' ), $new_account_password_reset_link );
                        }
                        // $new_account_password_reset_link = make_clickable( esc_url( wc_get_page_permalink( 'myaccount' ) ) );
                        // $new_account_info = sprintf( esc_html__( 'Thanks for creating an account on %1$s. Your username is %2$s. You can access your account area to view orders, change your password, and more at: %3$s', 'ddwc-subscription-customization' ), esc_html( get_option( 'blogname' ) ), '<strong>' . esc_html( $receiver->user_login ) . '</strong>', make_clickable( esc_url( wc_get_page_permalink( 'myaccount' ) ) ) );
                    }

                    $message = str_replace(
                        array(
                            '{site_title}',
                            '{site_address}',
                            '{site_url}',
                            '{receiver_name}',
                            '{product_name}',
                            '{giver_name}',
                            '{greeting_text}',
                            '{new_account_text}',
                            '{new_account_username}',
                            '{new_account_password_reset_link}',
                            // "\n",
                        ),
                        array(
                            wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
                            $domain,
                            $domain,
                            $receiver_name,
                            $product_name,
                            $giver_name,
                            $receiver_message,
                            $new_account_text,
                            $new_account_username,
                            $new_account_password_reset_link,
                            // '<br />',
                        ),
                        $message
                    );

                    // ob_start();
                    // wc_get_template( 'emails/email-styles.php' );
                    // $css = ob_get_clean();

                    // $emogrifier_class = 'Pelago\\Emogrifier';

                    // if ( class_exists( $emogrifier_class ) ) {
                    //     try {
                    //         $emogrifier = new $emogrifier_class( $message, $css );

                    //         $message    = $emogrifier->emogrify();
                    //         $html_prune = \Pelago\Emogrifier\HtmlProcessor\HtmlPruner::fromHtml( $message );
                    //         $html_prune->removeElementsWithDisplayNone();
                    //         $message    = $html_prune->render();
                    //     } catch ( Exception $e ) {
                    //         $logger = wc_get_logger();
                    //         $logger->error( $e->getMessage(), array( 'source' => 'emogrifier' ) );
                    //     }
                    // } else {
                    //     $message = '<style type="text/css">' . $css . '</style>' . $message;
                    // }

                    // add_filter( 'wp_mail_content_type', 'ddwcsc_email_content_type' );

                    wp_mail( $sender->user_email, __( 'Notification for Gift Card', 'ddwc-subscription-customization' ), $message, $headers );

                    // remove_filter( 'wp_mail_content_type', 'ddwcsc_email_content_type' );

                    // $email_data = [
                    //     'email' => $sender->user_email,
                    //     'message' => $message
                    // ];

                    // do_action( 'ddwcwm_mail', $email_data );

                    // var_dump( $message );
                    // die;
                }
            }, 10, 2 );

            add_action( 'init', function() {
                global $wpdb;
                // $results = $wpdb->get_results( $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}usermeta WHERE meta_key=%s AND meta_value > date(%s)", '_ddwcsc_gift_date', current_time( 'Y-m-d H:i:s' ) ), ARRAY_A );
                $results = $wpdb->get_results( $wpdb->prepare( "SELECT user_id, meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key=%s AND meta_value <= %s", '_ddwcsc_gift_date', current_time( 'Y-m-d' ) ), ARRAY_A );

                if ( ! empty( $results ) ) {
                    foreach ( $results as $key => $value ) {
                        $user_id = $value[ 'user_id' ];

                        $order_id = get_user_meta( $user_id, '_ddwcsc_gift_order_id', true );

                        $receiver_name    = get_post_meta( $order_id, '_ddwcsc_receiver_name', true );
                        $receiver_email   = get_post_meta( $order_id, '_ddwcsc_receiver_email', true );
                        $receiver_message = get_post_meta( $order_id, '_ddwcsc_receiver_message', true );
                        // $sending_date     = get_post_meta( $order_id, '_ddwcsc_sending_date', true );
                        $sender_id       = get_post_meta( $order_id, '_ddwcsc_sender_id', true );
                        $buyer_mail      = get_post_meta( $order_id, '_ddwcsc_buyer_mail', true );
                        $account_created = get_post_meta( $order_id, '_ddwcsc_account_created', true );

                        if ( empty( $buyer_mail ) ) {
                            $order       = wc_get_order( $order_id );
                            $order_items = $order->get_items();

                            $product_name = '';

                            foreach ( $order_items as $key => $value ) {
                                $item_data    = $value->get_data();
                                $product_name = $item_data[ 'name' ];
                            }
                            $sender_id        = get_current_user_id();
                            $sender           = get_userdata( $sender_id );
                            $giver_name       = $sender->display_name;
                            // $message = '';
                            // $message .= sprintf( esc_html__( "Hello %s", 'ddwc-subscription-customization' ), $receiver_name ? $receiver_name : $receiver_email ) . '<br><br>';

                            // $message .= sprintf( esc_html__( "You've been received a gift from %s", 'ddwc-subscription-customization' ), $giver_name ) . '<br><br>';

                            // $message .= sprintf( esc_html__( "Message from Sender: %s", 'ddwc-subscription-customization' ), $receiver_message ) . '<br><br>';

                            // if ( ! empty( $sending_date ) ) {
                            //     $message .= sprintf( esc_html__( "Sending Date: %s", 'ddwc-subscription-customization' ), $sending_date ) . '<br><br>';
                            // }

                            $message = html_entity_decode( get_option( 'ddwcsc_receiver_gift_card_content' ) );

                            // if ( ! empty( $extra_details ) ) {
                            //     $message .= html_entity_decode( $extra_details ) . '<br><br>';
                            // }

                            // $message .= esc_html__( "Thanks", 'ddwc-subscription-customization' );

                            $headers = [ 'Content-Type: text/html; charset=UTF-8' ];

                            $domain = wp_parse_url( home_url(), PHP_URL_HOST );

                            $new_account_text                = '';
                            $new_account_username            = '';
                            $new_account_password_reset_link = '';

                            if ( ! empty( $account_created ) ) {
                                $receiver = get_userdata( $user_id );

                                $new_account_text = esc_html__( 'text for in case a new account had been created', 'ddwc-subscription-customization' );
                                $new_account_username = sprintf( esc_html__( 'Your username: %s', 'ddwc-subscription-customization' ), $receiver->user_login );

                                $object = new \WP_User( $user_id );

                                // Generate a magic link so user can set initial password.
                                $key = get_password_reset_key( $object );
                                if ( ! is_wp_error( $key ) ) {
                                    $action                 = 'newaccount';
                                    $new_account_password_reset_link = wc_get_account_endpoint_url( 'lost-password' ) . "?action=$action&key=$key&login=" . rawurlencode( $object->user_login );
                                    $new_account_password_reset_link = sprintf( esc_html__( 'Your password reset link: %s', 'ddwc-subscription-customization' ), $new_account_password_reset_link );

                                }
                                // $new_account_password_reset_link = make_clickable( esc_url( wc_get_page_permalink( 'myaccount' ) ) );
                                // $new_account_info = sprintf( esc_html__( 'Thanks for creating an account on %1$s. Your username is %2$s. You can access your account area to view orders, change your password, and more at: %3$s', 'ddwc-subscription-customization' ), esc_html( get_option( 'blogname' ) ), '<strong>' . esc_html( $receiver->user_login ) . '</strong>', make_clickable( esc_url( wc_get_page_permalink( 'myaccount' ) ) ) );
                            }

                            $message = str_replace(
                                array(
                                    '{site_title}',
                                    '{site_address}',
                                    '{site_url}',
                                    '{receiver_name}',
                                    '{product_name}',
                                    '{giver_name}',
                                    '{greeting_text}',
                                    '{new_account_text}',
                                    '{new_account_username}',
                                    '{new_account_password_reset_link}',
                                    // "\n",
                                ),
                                array(
                                    wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
                                    $domain,
                                    $domain,
                                    $receiver_name,
                                    $product_name,
                                    $giver_name,
                                    $receiver_message,
                                    $new_account_text,
                                    $new_account_username,
                                    $new_account_password_reset_link,
                                    // '<br />',
                                ),
                                $message
                            );

                            wp_mail( $receiver_email, __( 'Notification for Gift Card', 'ddwc-subscription-customization' ), $message, $headers );

                            // $new_customer_data = apply_filters(
                            //     'woocommerce_new_customer_data',
                            //     array_merge(
                            //         array(),
                            //         array(
                            //             'user_login' => $receiver->user_login,
                            //             'user_email' => $receiver->user_email,
                            //             'role'       => 'customer',
                            //         )
                            //     )
                            // );

                            // do_action( 'woocommerce_created_customer', $user_id, $new_customer_data, true );
                            // var_dump( $message );
                            // die;
                        }
                        delete_user_meta( $user_id, '_ddwcsc_gift_date' );
                    }
                }
            } );

            // add_filter( 'wcs_recurring_cart_start_date', function( $date, $cart ) {
            //     return '2021-09-01 00:01:00';
            //     echo '<pre>';
            //     print_r( $date );
            //     print_r( $cart );
            //     echo '</pre><br>';
            //     die;
            // }, 100, 2);

            add_action( 'woocommerce_subscription_payment_complete', function( $subscription ) {
                $order = $subscription->get_parent();

                if ( ! empty( get_post_meta( $order->get_id(), '_ddwcsc_sending_date', true ) ) ) {
                    $dates = [
                        'start' => date( 'Y-m-d H:i:s', strtotime( get_post_meta( $order->get_id(), '_ddwcsc_sending_date', true ) ) ),
                    ];

                    $new_start_date_offset = strtotime( $dates[ 'start' ] ) - $subscription->get_time( 'start' );

                    if ( WC_Subscriptions_Synchroniser::subscription_contains_synced_product( $subscription ) ) {

                        $trial_end    = $subscription->get_time( 'trial_end' );
                        $next_payment = $subscription->get_time( 'next_payment' );

                        // if either there is a free trial date or a next payment date that falls before now, we need to recalculate all the sync'd dates
                        if ( ( $trial_end > 0 && $trial_end < wcs_date_to_time( $dates['start'] ) ) || ( $next_payment > 0 && $next_payment < wcs_date_to_time( $dates['start'] ) ) ) {

                            foreach ( $subscription->get_items() as $item ) {
                                $product_id = wcs_get_canonical_product_id( $item );

                                if ( WC_Subscriptions_Synchroniser::is_product_synced( $product_id ) ) {
                                    $dates['trial_end']    = WC_Subscriptions_Product::get_trial_expiration_date( $product_id, $dates['start'] );
                                    $dates['next_payment'] = WC_Subscriptions_Synchroniser::calculate_first_payment_date( $product_id, 'mysql', $dates['start'] );
                                    $dates['end']          = WC_Subscriptions_Product::get_expiration_date( $product_id, $dates['start'] );
                                    break;
                                }
                            }
                        }
                    } else {
                        // No sync'ing to mess about with, just add the offset to the existing dates
                        foreach ( array( 'trial_end', 'next_payment', 'end' ) as $date_type ) {
                            if ( 0 != $subscription->get_time( $date_type ) ) {
                                $dates[ $date_type ] = gmdate( 'Y-m-d H:i:s', $subscription->get_time( $date_type ) + $new_start_date_offset );
                            }
                        }
                    }

                    $subscription->update_dates( $dates );

                    $subscription->save();
                }
            } );
		}
	}

	add_action( 'plugins_loaded', 'ddwcscincludes' );
}
