<?php
/**
 * ZPN_Admin_Action Class
 *
 * Handles the admin functionality.
 *
 * @package WordPress
 * @package Push Notifications For Web
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

			add_action( 'admin_init'     , array( $this, 'action__admin_init' ) );
			add_action( 'admin_menu'     , array( $this, 'action__add_menu' ) );
			add_action( 'add_meta_boxes' , array( $this, 'action__notification_meta_boxes' ) );
			add_action( 'publish_post'   , array( $this, 'action__send_notification' ) );
			add_action( 'publish_page'   , array( $this, 'action__send_notification' ) );
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
			if ( !is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
				wp_enqueue_media();
			}
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
				__( 'Push Notification', 'push-notifications-for-web' ),
				__( 'Push Notification', 'push-notifications-for-web' ),
				'edit_posts',
				'web_push',
				array( $this, 'page_callback_function' ),
				'dashicons-megaphone'
			);
		}

		/**
		 * Register Notification Metabox
		 *
		 * @return void
		 */
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
		 * Action For Send Notification function
		 *
		 * @param [type] $post_id notification post_id
		 * @return array
		 */
		function action__send_notification( $post_id ){

			$postType = get_post_type( $post_id );

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

			if ( ! wp_verify_nonce( $_REQUEST['_wpn_post_notification_nonce'], 'wpn_notification_nonce' ) ) { //phpcs:ignore
				return $post_id;
			}

			if ( $postType == 'post' || $postType == 'page' ) {
				if ( 'publish' == get_post_status( $post_id ) && isset( $_POST['wpn_post_notification'] ) && $_POST['wpn_post_notification'] != '' ) {
					$this->send_notification( $post_id ); // send data for notification
				}
			}
		}

		/**
		 * Plugin__upgrade_function function
		 *
		 * @param [type] $upgrader_object
		 * @param [type] $options
		 * @return void
		 */
		function plugin__upgrade_function( $upgrader_object, $options ) {

			$current_plugin_path_name = ZPN_PLUGIN_BASENAME;

			if ($options['action'] == 'update' && $options['type'] == 'plugin' ) {

				foreach($options['plugins'] as $each_plugin) {

					if ( $each_plugin  == $current_plugin_path_name ) {

						$filename = ZPN_DIR . '/assets/js/firebase-messaging-sw.js';

						$notification_authDomain    = sanitize_text_field( get_option( 'notification_authDomain' ) );
						$notification_apiKey     	= sanitize_text_field( get_option( 'notification_apiKey' ) );
						$notification_projectId  	= sanitize_text_field( get_option( 'notification_projectId' ) );
						$notification_storageBucket = sanitize_text_field( get_option( 'notification_storageBucket' ) );
						$notification_senderId   	= sanitize_text_field( get_option( 'notification_senderId' ) );
						$notification_appId      	= sanitize_text_field( get_option( 'notification_appId' ) );

						if( $notification_authDomain ) {
							$this->replace_sw_file_string( $filename, 'Enter authdomain key from your firebase app configuration', $notification_authDomain );
						}

						if( $notification_apiKey ) {
							$this->replace_sw_file_string( $filename, 'Enter api key from your firebase app configuration', $notification_apiKey );
						}

						if( $notification_projectId ) {
							$this->replace_sw_file_string( $filename, 'Enter project id from your firebase app configuration', $notification_projectId );
						}

						if( $notification_storageBucket ) {
							$this->replace_sw_file_string( $filename, 'Enter storageBucket id from your firebase app configuration', $notification_storageBucket );
						}

						if( $notification_senderId ) {
							$this->replace_sw_file_string( $filename, 'Enter messaging sender id from your firebase app configuration', $notification_senderId );
						}

						if( $notification_appId ) {
							$this->replace_sw_file_string( $filename, 'Enter app id from your firebase app configuration', $notification_appId );
						}
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
		 * Replace string in file
		 *
		 * @return void
		 */
		function replace_sw_file_string( $filename, $string_to_replace, $replace_with ) {
			$content        = file_get_contents( $filename );
			$content_chunks = explode( $string_to_replace, $content );
			$content        = implode( $replace_with, $content_chunks );
			file_put_contents( $filename, $content );
		}


		/**
		 * Output the HTML for the metabox.
		 */
		function wpn_push_notification() {
			global $post;

			// Output the field
			echo '<input type="checkbox" id="wpn_post_notification" name="wpn_post_notification">  <label> Send WebPush Notification</label>
			<input type="hidden" id="_wpnonce" name="_wpn_post_notification_nonce" value="' . esc_attr(wp_create_nonce( 'wpn_notification_nonce' ) ). '">';
		}

		/**
		 * Generate Google Access Token
		 */
		 function get_firebase_access_token() {

			$notification_jsonfile = get_option('notification_jsonfile');

			 // echo "<br>";
			// Convert the URL to the absolute file path
			$upload_dir = wp_upload_dir(); // Get the WordPress uploads directory
			$file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $notification_jsonfile);

			if (!file_exists($file_path)) {
			    echo '<div class="error">
			        <p>' . esc_html__( 'Please Upload *.json file.', 'push-notifications-for-web' ) . '</p>
			    </div>';
			    return null;
			}
		     $service_account = json_decode(file_get_contents($file_path), true);

		     $jwt_header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
		     $jwt_claim = json_encode([
		         'iss' => $service_account['client_email'],
		         'scope' => 'https://www.googleapis.com/auth/cloud-platform',
		         'aud' => 'https://oauth2.googleapis.com/token',
		         'exp' => time() + 3600,
		         'iat' => time(),
		     ]);

		     $base64_header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwt_header));
		     $base64_claim = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwt_claim));
		     $signature_input = $base64_header . '.' . $base64_claim;

		     $private_key = $service_account['private_key'];
		     $signature = '';
		     openssl_sign($signature_input, $signature, $private_key, 'sha256');
		     $jwt = $signature_input . '.' . str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

		     $response = wp_remote_post('https://oauth2.googleapis.com/token', [
		         'body' => [
		             'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
		             'assertion' => $jwt,
		         ],
		     ]);

		     if (is_wp_error($response)) {
		         return null;
		     }

		     $response_body = json_decode(wp_remote_retrieve_body($response), true);
		     return $response_body['access_token'] ?? null;
		 }


		/**
		 * Send Notification function
		 *
		 * @param [type] $post_id get post_id send in notification.
		 * @return void
		 */
		function send_notification( $post_id ) {

			$access_token = $this->get_firebase_access_token();

			global $wpdb;

			$table_name = $wpdb->prefix . 'web_notification';
			$get_token_id = $wpdb->get_results( "SELECT token, device_name FROM $table_name" ); //phpcs:ignore

			$key = get_option( 'notification_server_key' );
			$projectid = get_option('notification_projectId');

			$url = 'https://fcm.googleapis.com/v1/projects/'.$projectid.'/messages:send';

			if ( get_option( 'wpn_enable_for_post' ) && ! empty( get_option( 'wpn_enable_for_post' ) ) ) {

				$post_title = get_the_title( $post_id );

				if( has_excerpt( $post_id ) ) {
					$post_message = wp_trim_words( get_the_excerpt( $post_id ), 160, '...' );
				} elseif ( trim( get_option( 'wpn_post_message' ) ) ) {
					$post_message = wp_trim_words( get_the_excerpt( $post_id ), 160, '...' );
				} else {
					$post_message = get_option( 'wpn_post_message' );
				}

				if ( get_option( 'wpn_post_icon' ) ) {
					$post_icon = wp_get_attachment_image_url( get_option( 'wpn_post_icon' ), 'thumbnail' );
				} elseif( get_option( 'wpn_post_image' ) ) {
					$post_icon = wp_get_attachment_image_url( get_option( 'wpn_post_image' ), 'thumbnail' );
				}else{
					$post_icon = get_the_post_thumbnail_url( $post_id, 'thumbnail' );
				}

				if( has_post_thumbnail( $post_id ) ) {
					$post_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
				} elseif ( get_option( 'wpn_post_image' ) ) {
					$post_image = wp_get_attachment_image_url( get_option( 'wpn_post_image' ),  'medium_large' );
				} else {
					$post_image = wp_get_attachment_image_url( get_option( 'wpn_post_image' ), 'medium_large' );
				}

			} else {
				$post_title   = get_the_title( $post_id );
				$post_message = wp_trim_words( get_the_excerpt( $post_id ), 160, '...' );
				$post_icon    = get_the_post_thumbnail_url( $post_id, array( 256, 256 ) );
				$post_image   = get_the_post_thumbnail_url( $post_id, array( 512, 512 ) );
			}

			$browser_token = array();
			foreach ( $get_token_id as $token ) {
				$browser_token[] = $token->token;

				$device_name = $token->device_name;
				$tokenDB = $token->token;

				
					// Notification payload for mobile webpush ONLY
					$payload = [
						'message' => [
							"data"=> [
								"title" => $post_title,
								"body" => stripslashes( $post_message ),
								"icon" => $post_icon,
								"image" => $post_image,
								"click_action" => get_the_permalink( $post_id ),
							],
							'token' => $tokenDB
						]
					];


				$response = wp_remote_post($url, [
			        'headers' => [
			            'Authorization' => 'Bearer ' . $access_token,
			            'Content-Type'  => 'application/json',
			        ],
			        'body' => json_encode($payload),
			    ]);

			    if (is_wp_error($response)) {
			        error_log('FCM Notification Error: ' . $response->get_error_message());
			        return false;
			    }

			    $response_code = wp_remote_retrieve_response_code($response);
			    $response_body = wp_remote_retrieve_body($response);

			}

		}

		/**
		 * Used to display the Notification configuration page.
		 */
		function page_callback_function() {
			require_once ZPN_DIR . '/inc/admin/template/' . ZPN_PREFIX . '.template.php';
		}

		/**
		 * Save plugin settings
		 */
		public function save_notification_setting() {

			if ( ! wp_verify_nonce( $_POST['setting_save'], 'notification_setting_save' )  //phpcs:ignore
			) {
				echo '<div class="error">
				<p>' . esc_html__( 'Sorry, your nonce was not correct. Please try again.', 'push-notifications-for-web' ) . '</p>
				</div>';
				exit;

			} else {

				$notification_server_key 	 = sanitize_text_field( $_POST['notification_server_key'] ); //phpcs:ignore
				$notification_authDomain 	 = sanitize_text_field( $_POST['notification_authDomain'] ); //phpcs:ignore
				$notification_apiKey     	 = sanitize_text_field( $_POST['notification_apiKey'] );     //phpcs:ignore
				$notification_projectId  	 = sanitize_text_field( $_POST['notification_projectId'] );  //phpcs:ignore
				$notification_storageBucket  = sanitize_text_field( $_POST['notification_storageBucket'] );  //phpcs:ignore
				$notification_senderId   	 = sanitize_text_field( $_POST['notification_senderId'] );   //phpcs:ignore
				$notification_appId      	 = sanitize_text_field( $_POST['notification_appId'] );      //phpcs:ignore
				$notification_jsonfile 		 = isset($_FILES['notification_jsonfile']) ? $_FILES['notification_jsonfile'] : null;
				$notification_jsonfile_url 	 = sanitize_text_field(get_option('notification_jsonfile'));

				$allowed_types = [
				    'json' => 'application/json',
				];

				// Validate file size
				$max_file_size = 2 * 1024 * 1024; // 2MB

				$has_error = false;

				if ($notification_jsonfile && ! empty($notification_jsonfile['tmp_name'])) {
				    $file_name = sanitize_file_name($notification_jsonfile['name']);
				    $file_type = wp_check_filetype($file_name, $allowed_types);

				    if ($notification_jsonfile['size'] > $max_file_size) {
				        echo '<div class="error">
				        <p>' . esc_html__('JSON file size exceeds the allowed limit of 2MB.', 'push-notifications-for-web') . '</p>
				        </div>';
				        $has_error = true;
				    } elseif ($file_type['ext'] !== 'json' || $file_type['type'] !== 'application/json') {
				        echo '<div class="error">
				        <p>' . esc_html__('Invalid file type. Only JSON files are allowed.', 'push-notifications-for-web') . '</p>
				        </div>';
				        $has_error = true;
				    } else {
				        // File is valid; proceed with uploading
				        $upload = wp_handle_upload($notification_jsonfile, ['test_form' => false]);
				        if (isset($upload['url'])) {
				            $notification_jsonfile_url = $upload['url']; // Use the uploaded file URL
				        }
				    }
				} else {
				    if (!empty($notification_jsonfile_url)) {
				        // Use the existing URL from options
				        $file_type = wp_check_filetype($notification_jsonfile_url, $allowed_types);
				        if ($file_type['ext'] !== 'json' || $file_type['type'] !== 'application/json') {
				            echo '<div class="error">
				            <p>' . esc_html__('The existing file is not a valid JSON file.', 'push-notifications-for-web') . '</p>
				            </div>';
				            $has_error = true;
				        }
				    } else {
				        echo '<div class="error">
				        <p>' . esc_html__('No file uploaded and no existing file found.', 'push-notifications-for-web') . '</p>
				        </div>';
				        $has_error = true;
				    }
				}

				if (
					! $has_error &&
				    ! empty( $notification_server_key ) &&
				    ! empty( $notification_authDomain ) &&
				    ! empty( $notification_apiKey ) &&
				    ! empty( $notification_projectId ) &&
				    ! empty( $notification_storageBucket ) &&
				    ! empty( $notification_senderId ) &&
				    ! empty( $notification_appId ) &&
				    ! empty( $notification_jsonfile_url )
				) {

					$filename = ZPN_DIR . '/assets/js/firebase-messaging-sw.js';

					$notification_authDomain_old 	= sanitize_text_field( get_option( 'notification_authDomain' ) );
					$notification_apiKey_old     	= sanitize_text_field( get_option( 'notification_apiKey' ) );
					$notification_projectId_old  	= sanitize_text_field( get_option( 'notification_projectId' ) );
					$notification_storageBucket_old = sanitize_text_field( get_option( 'notification_storageBucket' ) );
					$notification_senderId_old   	= sanitize_text_field( get_option( 'notification_senderId' ) );
					$notification_appId_old      	= sanitize_text_field( get_option( 'notification_appId' ) );

					if( $notification_authDomain_old ) {
						$this->replace_sw_file_string( $filename, $notification_authDomain_old, $notification_authDomain );
					} else {
						$this->replace_sw_file_string( $filename, 'Enter authdomain key from your firebase app configuration', $notification_authDomain );
					}

					if( $notification_apiKey_old ) {
						$this->replace_sw_file_string( $filename, $notification_apiKey_old, $notification_apiKey );
					} else {
						$this->replace_sw_file_string( $filename, 'Enter api key from your firebase app configuration', $notification_apiKey );
					}

					if( $notification_projectId_old ) {
						$this->replace_sw_file_string( $filename, $notification_projectId_old, $notification_projectId );
					} else {
						$this->replace_sw_file_string( $filename, 'Enter project id from your firebase app configuration', $notification_projectId );
					}

					if( $notification_storageBucket_old ) {
						$this->replace_sw_file_string( $filename, $notification_storageBucket_old, $notification_storageBucket );
					} else {
						$this->replace_sw_file_string( $filename, 'Enter storageBucket id from your firebase app configuration', $notification_storageBucket );
					}

					if( $notification_senderId_old ) {
						$this->replace_sw_file_string( $filename, $notification_senderId_old, $notification_senderId );
					} else {
						$this->replace_sw_file_string( $filename, 'Enter messaging sender id from your firebase app configuration', $notification_senderId );
					}

					if( $notification_appId_old ) {
						$this->replace_sw_file_string( $filename, $notification_appId_old, $notification_appId );
					} else {
						$this->replace_sw_file_string( $filename, 'Enter app id from your firebase app configuration', $notification_appId );
					}

					update_option( 'notification_server_key', $notification_server_key );
					update_option( 'notification_authDomain', $notification_authDomain );
					update_option( 'notification_apiKey', $notification_apiKey );
					update_option( 'notification_projectId', $notification_projectId );
					update_option( 'notification_storageBucket', $notification_storageBucket );
					update_option( 'notification_senderId', $notification_senderId );
					update_option( 'notification_appId', $notification_appId );
					update_option( 'notification_jsonfile', $notification_jsonfile_url);

					echo '<div class="updated">
					<p>' . esc_html__( 'Fields update successfully.', 'push-notifications-for-web' ) . '</p>
					</div>';

				} else {

					if(
						! empty( $notification_server_key ) ||
						! empty( $notification_authDomain ) ||
						! empty( $notification_apiKey ) ||
						! empty( $notification_projectId ) ||
						! empty( $notification_storageBucket ) ||
						! empty( $notification_senderId ) ||
						! empty( $notification_appId )

					) {
						echo '<div class="error">
						<p>' . esc_html__( 'Fill all required fields.', 'push-notifications-for-web' ) . '</p>
						</div>';
					}

				}
			}
		}


		/**
		 * Send Push Notification
		 */
		public function send_push_notification_manually() {

			if ( ! isset( $_POST['notification_fields'] ) ||
			! wp_verify_nonce( $_POST['notification_fields'], 'notification_fields_update' ) //phpcs:ignore
			) {
				echo '<div class="error">
				<p>' . esc_html__( 'Sorry, your nonce was not correct. Please try again.', 'push-notifications-for-web' ) . '</p>
				</div>';
				exit;

			} else {
				//Get Firebase Access Token
				$access_token = $this->get_firebase_access_token();

				$key = get_option( 'notification_server_key' );
				$projectid = get_option('notification_projectId');

				$validate_fields 	 = $this->check_required_fields();
				$notification_title  = sanitize_text_field( $_POST['notification_title'] );//phpcs:ignore
				$notification_desc   = sanitize_text_field( $_POST['notification_desc'] ); //phpcs:ignore
				$notification_link   = sanitize_text_field( $_POST['notification_link'] ); //phpcs:ignore
				$notification_icon   = sanitize_text_field( $_POST['notification_icon'] ); //phpcs:ignore
				$notification_image  = sanitize_text_field( $_POST['notification_image'] );//phpcs:ignore

				//Get Image url from image id
				if($notification_icon){
					$notification_icon	= wp_get_attachment_url($notification_icon);
				}

				if($notification_image){
					$notification_image = wp_get_attachment_url($notification_image);
				}

				if ( ! empty( $notification_desc ) && empty( $validate_fields ) ) {

					global $wpdb;

					$table_name = $wpdb->prefix . 'web_notification';

					$get_token_id = $wpdb->get_results( "SELECT token, device_name FROM $table_name" );  //phpcs:ignore

					$url = 'https://fcm.googleapis.com/v1/projects/'.$projectid.'/messages:send';

					if ( $get_token_id ) {
						$browser_token = array();
						foreach ( $get_token_id as $token ) {

							$device_name = $token->device_name;
							$tokenDB = $token->token;

							$browser_token[] = $token->token;

							if( $device_name == 'Google Chrome' || $device_name == 'Mozilla Firefox'){
								// Notification payload for mobile webpush ONLY
								$payload = [
							        'message' => [
							            "data"=> [
							                "title" => $notification_title,
							                "body" => substr( stripslashes( $notification_desc ), 0, 160 ),
							                "icon" => $notification_icon,
							                "image" => $notification_image,
							                "click_action" => $notification_link
							            ],
							            'token' => $tokenDB
							        ]
							    ];

							} else {
								// Notification payload for mobile android & ios
								$payload = [
									'message' => [
										'token' => $tokenDB,
										'notification' => [
											'title' => $notification_title,
											'body' => substr( stripslashes( $notification_desc ), 0, 160 ),
											'image' => $notification_image
										],
										"apns" => [
										    "payload" => [
										        "aps" => [
										            "mutable-content" => 1
										        ],
										        "image_url" => $notification_image
										    ]
										],
										"data"=> [
											"post_id" => '',
											"icon" => $notification_icon,
											"click_action" => $notification_link
										],
									]
								];
							}

							$response = wp_remote_post($url, [
						        'headers' => [
						            'Authorization' => 'Bearer ' . $access_token,
						            'Content-Type'  => 'application/json',
						        ],
						        'body' => json_encode($payload),
						    ]);

						    if (is_wp_error($response)) {
						        error_log('FCM Notification Error: ' . $response->get_error_message());
						        return false;
						    }

						    $response_code[] = wp_remote_retrieve_response_code($response);
						    $response_body = wp_remote_retrieve_body($response);

						}
					} else {
						echo '<div class="error">
						<p>' . esc_html__( 'No user accept and allow for site notification...!', 'push-notifications-for-web' ) . '</p>
						</div>';
					}

					if(in_array('200', $response_code)){
						echo '<div class="updated">' .
						'<p>' . esc_html__( 'Push notification send successfully...!', 'push-notifications-for-web' ) . '</p>' .
						'</div>';
					}


				} elseif ( $validate_fields ) {

					echo '<div class="error">' .
					'<p>' . esc_html__( 'For send notification please configure your all required fields first..!', 'push-notifications-for-web' ) . '</p>' .
					'</div>';

				} else {

					echo '<div class="error">
					<p>' . esc_html__( 'Please fill out all required fields...!', 'push-notifications-for-web' ) . '</p>
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
			! wp_verify_nonce( $_POST['new_post_configuration'], 'post_configuration_fields' )  //phpcs:ignore
			) {
				echo '<div class="error">
				<p>' . esc_html__( 'Sorry, your nonce was not correct. Please try again.', 'push-notifications-for-web' ) . '</p>
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

			$notification_server_key 	= sanitize_text_field( get_option( 'notification_server_key' ) );
			$notification_authDomain 	= sanitize_text_field( get_option( 'notification_authDomain' ) );
			$notification_apiKey     	= sanitize_text_field( get_option( 'notification_apiKey' ) );
			$notification_projectId  	= sanitize_text_field( get_option( 'notification_projectId' ) );
			$notification_storageBucket = sanitize_text_field( get_option( 'notification_storageBucket' ) );
			$notification_senderId   	= sanitize_text_field( get_option( 'notification_senderId' ) );
			$notification_appId      	= sanitize_text_field( get_option( 'notification_appId' ) );

			$error = true;

			if (
			empty( $notification_server_key ) ||
			empty( $notification_authDomain ) ||
			empty( $notification_apiKey ) ||
			empty( $notification_projectId ) ||
			empty( $notification_storageBucket ) ||
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
