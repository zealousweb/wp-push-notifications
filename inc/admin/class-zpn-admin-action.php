<?php
	/**
	 * ZPN_Admin_Action Class
	 *
	 * Handles the admin functionality.
	 *
	 * @package WordPress
	 * @package ZealPush Notification For WordPress
	 * @since 1.0
	 */

	// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ZPN_Admin_Action' ) ) {

	/**
	 *  The ZPN_Admin_Action Class
	 */
	class ZPN_Admin_Action {

		function __construct() {

			add_action( 'admin_init', array( $this, 'action__admin_init' ) );
			add_action( 'admin_menu', array( $this, 'action__add_menu' ) );
			add_action( 'add_meta_boxes', array( $this, 'action__notification_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'action__wpn_save_notification_meta' ), 1, 2 );
		}

		/*
		   ###     ######  ######## ####  #######  ##    ##  ######
		  ## ##   ##    ##    ##     ##  ##     ## ###   ## ##    ##
		 ##   ##  ##          ##     ##  ##     ## ####  ## ##
		##     ## ##          ##     ##  ##     ## ## ## ##  ######
		######### ##          ##     ##  ##     ## ##  ####       ##
		##     ## ##    ##    ##     ##  ##     ## ##   ### ##    ##
		##     ##  ######     ##    ####  #######  ##    ##  ######
		*/

		/**
		 * Action: admin_init
		 *
		 * Register admin min js and admin min css
		 */
		function action__admin_init() {
			wp_register_script( ZPN_PREFIX . '_admin_js', ZPN_URL . 'assets/js/admin.min.js', array( 'jquery-core' ), ZPN_VERSION );
			wp_register_style( ZPN_PREFIX . '_admin_css', ZPN_URL . 'assets/css/admin.min.css', array(), ZPN_VERSION );
			wp_enqueue_media();
		}


		/**
		 * Register Notification Menu
		 *
		 * @method action__add_menu
		 *
		 * @param  object $post WP_Post
		 */
		function action__add_menu() {
			add_menu_page(
				__( 'Push Notification', 'zeal-push-notification' ),
				__( 'Push Notification', 'zeal-push-notification' ),
				'edit_posts',
				'web_push',
				array( $this, 'page_callback_function' ),
				'dashicons-megaphone'
			);
		}


		function action__notification_meta_boxes() {
			add_meta_box(
				'wpn_metabox',
				'Push Notification',
				array( $this, 'wpn_push_notification' ),
				array( 'post', 'page' ),
				'side',
				'high'
			);
		}


		/**
		 * Save the metabox data
		 */
		function action__wpn_save_notification_meta( $post_id, $post ) {

			if ( array_key_exists( 'wpn_post_notification', $_REQUEST ) && $_REQUEST['wpn_post_notification'] != '' ) {

				// Return if the user doesn't have edit permissions.
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return $post_id;
				}

				if ( ! wp_verify_nonce( $_REQUEST['_wpn_post_notification_nonce'], 'wpn_notification_nonce' ) ) {
					return $post_id;
				}

				$postType = get_post_type( $post_id );

				if ( $postType == 'post' || $postType == 'page' ) {

					if ( 'publish' == get_post_status( $post_id ) && isset( $_POST['wpn_post_notification'] ) && isset( $_POST['wpn_post_notification'] ) != '' ) {

						$this->send_notification( $post_id ); // send data for notification

					}
				}
			}
		}		

		/*
		######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######
		##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ##
		##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##
		######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######
		##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ##
		##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ##
		##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######
		*/

		/**
		 * Output the HTML for the metabox.
		 */
		function wpn_push_notification() {
			global $post;

			// Output the field
			echo '<input type="checkbox" id="wpn_post_notification" name="wpn_post_notification">  <label> Send WebPush Notification</label>
			<input type="hidden" id="_wpnonce" name="_wpn_post_notification_nonce" value="' . wp_create_nonce( 'wpn_notification_nonce' ) . '">';

		}


		function send_notification( $post_id ) {

			global $wpdb;

			$key = get_option( 'notification_server_key' );

			$table_name = $wpdb->prefix . 'web_notification';

			$get_token_id = $wpdb->get_results( "SELECT token FROM $table_name" );

			$browser_token = array();
			foreach ( $get_token_id as $token ) {
				$browser_token[] = $token->token;
			}

			if ( $browser_token ) {

				$url = 'https://fcm.googleapis.com/fcm/send';

				if ( get_option( 'wpn_enable_for_post' ) && ! empty( get_option( 'wpn_enable_for_post' ) ) ) {
					
					$post_title = get_the_title( $post_id );					

					if( has_excerpt( $post_id ) ) {
						$post_message = wp_trim_words( get_the_excerpt( $post_id ), 160, '...' );
					} elseif ( trim( get_option( 'wpn_post_message' ) ) == '{post_excerpt}' ) {
						$post_message = wp_trim_words( get_the_excerpt( $post_id ), 160, '...' );
					} else {
						$post_message = get_option( 'wpn_post_message' );
					}

					if ( trim( get_option( 'wpn_post_icon' ) ) == '{featured_image}' ) {
						$post_icon = get_the_post_thumbnail_url( $post_id, array( 256, 256 ) );
					} else {
						$post_icon = get_option( 'wpn_post_icon' );
					}		

					if( has_post_thumbnail( $post_id ) ) {
						$post_image = get_the_post_thumbnail_url( $post_id, array( 256, 256 ) );
					} elseif ( trim( get_option( 'wpn_post_image' ) ) == '{featured_image}' ) {
						$post_image = get_the_post_thumbnail_url( $post_id, array( 512, 512 ) );
					} else {
						$post_image = get_option( 'wpn_post_image' );
					}

				} else {
					$post_title   = get_the_title( $post_id );
					$post_message = wp_trim_words( get_the_excerpt( $post_id ), 160, '...' );
					$post_icon    = get_the_post_thumbnail_url( $post_id, array( 256, 256 ) );
					$post_image   = get_the_post_thumbnail_url( $post_id, array( 512, 512 ) );
				}

				$msg = array(
					'title'        => $post_title,
					'body'         => $post_message,
					'icon'         => $post_icon,
					'image'        => $post_image,
					'click_action' => get_the_permalink( $post_id ),
				);

				$payload = array(
					'registration_ids' => $browser_token,
					'data'             => $msg,
					'priority'         => 'high',
				);

				$send_notification = wp_remote_post(
					$url,
					array(
						'timeout'     => 120,
						'redirection' => 5,
						'method'      => 'POST',
						'headers'     => array(
							'Content-Type'  => 'application/json',
							'Authorization' => 'key=' . $key,
						),
						'httpversion' => '1.0',
						'sslverify'   => false,
						'body'        => json_encode( $payload ),
					)
				);
			}
		}

			/**
			 * Used to display the Notification configuration page.
			 */
		function page_callback_function() {

			require_once ZPN_DIR . '/inc/admin/template/' . ZPN_PREFIX . '-template.php';

		}


			/**
			 * Save plugin settings
			 */
		public function save_notification_setting() {

			if (
			! wp_verify_nonce( $_POST['setting_save'], 'notification_setting_save' )
			) {
				echo '<div class="error">
				<p>' . __( 'Sorry, your nonce was not correct. Please try again.', 'zeal-push-notification' ) . '</p>
				</div>';
				exit;

			} else {

				$notification_server_key = sanitize_text_field( $_POST['notification_server_key'] );
				$notification_apiKey     = sanitize_text_field( $_POST['notification_apiKey'] );
				$notification_projectId  = sanitize_text_field( $_POST['notification_projectId'] );
				$notification_senderId   = sanitize_text_field( $_POST['notification_senderId'] );
				$notification_appId      = sanitize_text_field( $_POST['notification_appId'] );

				if (
				! empty( $notification_server_key ) &&
				! empty( $notification_apiKey ) &&
				! empty( $notification_projectId ) &&
				! empty( $notification_senderId ) &&
				! empty( $notification_appId )
				) {
					update_option( 'notification_server_key', $notification_server_key );
					update_option( 'notification_apiKey', $notification_apiKey );
					update_option( 'notification_projectId', $notification_projectId );
					update_option( 'notification_senderId', $notification_senderId );
					update_option( 'notification_appId', $notification_appId );

					echo '<div class="updated">
					<p>' . __( 'Fields update successfully.', 'zeal-push-notification' ) . '</p>
					</div>';
				} else {
					echo '<div class="error">
					<p>' . __( 'Fill all required fields.', 'zeal-push-notification' ) . '</p>
					</div>';
				}
			}
		}


		/**
		 * Send Push Notification
		 */
		public function send_push_notification_manually() {

			if (
			! isset( $_POST['notification_fields'] ) ||
			! wp_verify_nonce( $_POST['notification_fields'], 'notification_fields_update' )
			) {
				echo '<div class="error">
				<p>' . __( 'Sorry, your nonce was not correct. Please try again.', 'zeal-push-notification' ) . '</p>
				</div>';
				exit;

			} else {

				$key = get_option( 'notification_server_key' );

				$validate_fields = $this->check_required_fields();
				$notification_title  = sanitize_text_field( $_POST['notification_title'] );
				$notification_desc   = sanitize_text_field( $_POST['notification_desc'] );
				$notification_link   = sanitize_text_field( $_POST['notification_link'] );
				$notification_icon   = sanitize_text_field( $_POST['notification_icon'] );
				$notification_image  = sanitize_text_field( $_POST['notification_image'] );

				if ( ! empty( $notification_desc ) && empty( $validate_fields ) ) {

					global $wpdb;

					$table_name = $wpdb->prefix . 'web_notification';

					$get_token_id = $wpdb->get_results( "SELECT token FROM $table_name" );

					$browser_token = array();
					foreach ( $get_token_id as $token ) {
						$browser_token[] = $token->token;
					}

					if ( $browser_token ) {

						$url = 'https://fcm.googleapis.com/fcm/send';

						$msg = array(
							'title'        => $notification_title,
							'body'         => substr( $notification_desc, 0, 160 ),
							'icon'         => $notification_icon,
							'image'        => $notification_image,
							'click_action' => $notification_link,
						);

						$payload = array(
							'registration_ids' => $browser_token,
							'data'             => $msg,
							'priority'         => 'high',
						);

						$send_notification = wp_remote_post(
							$url,
							array(
								'timeout'     => 120,
								'redirection' => 5,
								'method'      => 'POST',
								'headers'     => array(
									'Content-Type'  => 'application/json',
									'Authorization' => 'key=' . $key,
								),
								'httpversion' => '1.0',
								'sslverify'   => false,
								'body'        => json_encode( $payload ),
							)
						);

						$response_code    = wp_remote_retrieve_response_code( $send_notification );
						$response_message = wp_remote_retrieve_response_message( $send_notification );

						if ( $response_code != 200 ) {

							echo '<div class="error">' .
							'<p>' . $response_message . '</p>' .
							'</div>';

						} else {

							echo '<div class="updated">' .
							'<p>' . __( 'Push notification send sucessfully...!', 'zeal-push-notification' ) . '</p>' .
							'</div>';
						}
					} else {

						echo '<div class="error">
						<p>' . __( 'No user accept and allow for site notification...!', 'zeal-push-notification' ) . '</p>
						</div>';
					}
				} elseif ( $validate_fields ) {

					echo '<div class="error">' .
					'<p>' . __( 'For send notification please configure your all required fields first..!', 'zeal-push-notification' ) . '</p>' .
					'</div>';

				} else {

					echo '<div class="error">
					<p>' . __( 'Please fill out all required fields...!', 'zeal-push-notification' ) . '</p>
					</div>';

				}
			}
		}


		/**
		 * Save Configuration Setting
		 */
		function save_configuration_setting() {

			if (
			! isset( $_POST['new_post_configuration'] ) ||
			! wp_verify_nonce( $_POST['new_post_configuration'], 'post_configuration_fields' )
			) {
				echo '<div class="error">
				<p>' . __( 'Sorry, your nonce was not correct. Please try again.', 'zeal-push-notification' ) . '</p>
				</div>';
				exit;

			} else {

				$wpn_enable_for_post = isset( $_POST['wpn_enable_for_post'] ) ? sanitize_text_field( $_POST['wpn_enable_for_post'] ) : '';
				$wpn_post_message    = isset( $_POST['wpn_post_message'] ) ? sanitize_text_field( $_POST['wpn_post_message'] ) : '';
				$wpn_post_icon       = isset( $_POST['wpn_post_icon'] ) ? sanitize_text_field( $_POST['wpn_post_icon'] ) : '';
				$wpn_post_image      = isset( $_POST['wpn_post_image'] ) ? sanitize_text_field( $_POST['wpn_post_image'] ) : '';

				update_option( 'wpn_enable_for_post', $wpn_enable_for_post );
				update_option( 'wpn_post_message', $wpn_post_message );
				update_option( 'wpn_post_icon', $wpn_post_icon );
				update_option( 'wpn_post_image', $wpn_post_image );

			}
		}

		/**
		 * Validate fields check
		 */
		function check_required_fields() {

			$notification_server_key = sanitize_text_field( get_option( 'notification_server_key' ) );
			$notification_apiKey     = sanitize_text_field( get_option( 'notification_apiKey' ) );
			$notification_projectId  = sanitize_text_field( get_option( 'notification_projectId' ) );
			$notification_senderId   = sanitize_text_field( get_option( 'notification_senderId' ) );
			$notification_appId      = sanitize_text_field( get_option( 'notification_appId' ) );

			$error = true;

			if (
			empty( $notification_server_key ) ||
			empty( $notification_apiKey ) ||
			empty( $notification_projectId ) ||
			empty( $notification_senderId ) ||
			empty( $notification_appId )
			) {
				$error = false;
			}
		}
	}

	add_action(
		'plugins_loaded',
		function() {
			ZPN()->admin->action = new ZPN_Admin_Action();
		}
	);

}
