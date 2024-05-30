<?php
/**
 * ZPN Class
 *
 * Handles the plugin functionality.
 *
 * @package WordPress
 * @package Web Push Notification
 * @since 1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


if ( !class_exists( 'ZPN' ) ) {

	/**
	 * The main ZPN class
	 */
	class ZPN {

		private static $_instance = null;

		var $admin = null,
			$front = null,
			$lib   = null;

		public static function instance() {

			if ( is_null( self::$_instance ) )
				self::$_instance = new self();

			return self::$_instance;
		}

		function __construct() {

			add_action( 'plugins_loaded', array( $this, 'action__plugins_loaded' ), 1 );

			# Register plugin activation hook
			register_activation_hook( ZPN_FILE, array( $this, 'action__plugin_activation' ) );

			add_action('wp_ajax_notification_token', array( $this,'action__notification_token' ) ); // wp_ajax_{action}
			add_action('wp_ajax_nopriv_notification_token', array( $this,'action__notification_token' ) ); // wp_ajax_nopriv_{action}

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
		 * Action: plugins_loaded
		 *
		 * -
		 *
		 * @return [type] [description]
		 */
		function action__plugins_loaded() {

			# Load Paypal SDK on int action

			# Action to load custom post type
			add_action( 'init', array( $this, 'action__init' ) );

			global $wp_version;

			# Set filter for plugin's languages directory
			$ZPN_lang_dir = dirname( ZPN_PLUGIN_BASENAME ) . '/languages/';
			$ZPN_lang_dir = apply_filters( 'ZPN_languages_directory', $ZPN_lang_dir );

			# Traditional WordPress plugin locale filter.
			$get_locale = get_locale();

			if ( $wp_version >= 4.7 ) {
				$get_locale = get_user_locale();
			}

			# Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale',  $get_locale, 'push-notifications-for-web' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'push-notifications-for-web', $locale );

			# Setup paths to current locale file
			$mofile_global = WP_LANG_DIR . '/plugins/' . basename( ZPN_DIR ) . '/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				# Look in global /wp-content/languages/plugin-name folder
				load_textdomain( 'push-notifications-for-web', $mofile_global );
			} else {
				# Load the default language files
				load_plugin_textdomain( 'push-notifications-for-web', false, $ZPN_lang_dir );
			}
		}

		/**
		 * Action: init
		 *
		 * - If license found then action run
		 *
		 */
		function action__init() {

			flush_rewrite_rules(); //phpcs:ignore

			# Post Type: Here you add your post type

		}

		/**
		 * Action: register_activation_hook
		 *
		 * - When active plugin
		 *
		 */
		function action__plugin_activation() {

			global $wpdb;

			$table_name = $wpdb->prefix . 'web_notification';

			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			device_name varchar(255) NOT NULL,
			unique_id varchar(255) NOT NULL,
			token varchar(255) NOT NULL,
			date timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
			PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			
			$filename = ZPN_DIR . '/assets/js/firebase-messaging-sw.js';
			
			$notification_apiKey     = sanitize_text_field( get_option( 'notification_apiKey' ) );
			$notification_projectId  = sanitize_text_field( get_option( 'notification_projectId' ) );
			$notification_senderId   = sanitize_text_field( get_option( 'notification_senderId' ) );
			$notification_appId      = sanitize_text_field( get_option( 'notification_appId' ) );

			if( $notification_apiKey ) {
				$this->replace_sw_file_string( $filename, 'Enter api key from your firebase app configuration', $notification_apiKey );
			} else {
				$this->replace_sw_file_string( $filename, 'Enter api key from your firebase app configuration', 'Enter api key from your firebase app configuration' );
			}

			if( $notification_projectId ) {
				$this->replace_sw_file_string( $filename, 'Enter project id from your firebase app configuration', $notification_projectId );
			} else {
				$this->replace_sw_file_string( $filename, 'Enter project id from your firebase app configuration', 'Enter project id from your firebase app configuration' );
			}

			if( $notification_senderId ) {
				$this->replace_sw_file_string( $filename, 'Enter messaging sender id from your firebase app configuration', $notification_senderId );
			} else {
				$this->replace_sw_file_string( $filename, 'Enter messaging sender id from your firebase app configuration', 'Enter messaging sender id from your firebase app configuration' );
			}

			if( $notification_appId ) {
				$this->replace_sw_file_string( $filename, 'Enter app id from your firebase app configuration', $notification_appId );
			} else {
				$this->replace_sw_file_string( $filename, 'Enter app id from your firebase app configuration', 'Enter app id from your firebase app configuration' );
			}

		}

		/**
		 * Action: action__notification_token
		 *
		 * - When user accept allow receive notification
		 *
		 */
		function action__notification_token() {

			global $wpdb;

			$table_name  = $wpdb->prefix."web_notification";
			$token       = sanitize_text_field( $_POST['token'] ); //phpcs:ignore
			$unique_id   = sanitize_text_field( $_POST['unique_id'] ); //phpcs:ignore
			$device_name = $this->get_the_browser();

			if( $token ) {

				$unique_check_id = $wpdb->get_results("SELECT * FROM $table_name WHERE ( unique_id = '".$unique_id."' || token = '".$token."')", ARRAY_A); //phpcs:ignore

				if( sizeof($unique_check_id) > 0 ){

					$result = $wpdb->update(
						$table_name,
						array(
							'token' => $token,
						),
						array(
							"unique_id" => $unique_id,
							"token" => $token
						)
					);
					$lastid = $wpdb->insert_id;
					$curid = $unique_check_id[0]['id'];
					echo esc_html( $unique_check_id[0]['unique_id'].$curid );

				}else{

					$unique_id = $this->generate_unique_id(36);
					$test = $wpdb->insert($table_name, array(
						'token' => $token,
						'device_name' => $device_name,
						'unique_id' => $unique_id,
					));
					$lastid = $wpdb->insert_id;
					echo esc_html( $unique_id.$lastid );
				}
			}
			die;
		}



		/*
		######## #### ##       ######## ######## ########   ######
		##        ##  ##          ##    ##       ##     ## ##    ##
		##        ##  ##          ##    ##       ##     ## ##
		######    ##  ##          ##    ######   ########   ######
		##        ##  ##          ##    ##       ##   ##         ##
		##        ##  ##          ##    ##       ##    ##  ##    ##
		##       #### ########    ##    ######## ##     ##  ######
		*/



		/*
		######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######
		##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ##
		##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##
		######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######
		##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ##
		##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ##
		##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######
		*/

		/************ Web Push Notification ************/


		/**
		 * Function: get_the_browser
		 *
		 * - Used for get current user system.
		 *
		 */

		function get_the_browser() {
			if (isset($_SERVER['HTTP_USER_AGENT'])) {
				$user_agent = $_SERVER['HTTP_USER_AGENT'];
		
				if (strpos($user_agent, 'MSIE') !== false) {
					return 'Internet Explorer';
				} elseif (strpos($user_agent, 'Trident') !== false) {
					return 'Internet Explorer';
				} elseif (strpos($user_agent, 'Firefox') !== false) {
					return 'Mozilla Firefox';
				} elseif (strpos($user_agent, 'Chrome') !== false) {
					return 'Google Chrome';
				} elseif (strpos($user_agent, 'Opera Mini') !== false) {
					return 'Opera Mini';
				} elseif (strpos($user_agent, 'Opera') !== false) {
					return 'Opera';
				} elseif (strpos($user_agent, 'Safari') !== false) {
					return 'Safari';
				} else {
					return 'Other';
				}
			}
		}
		
		/**
		 * Function: random_string
		 *
		 * - Used for get current user system.
		 *
		 */
		function generate_unique_id( $length ) {
			$key = '';
			$keys = array_merge(range(0, 9), range('a', 'z'));
			for ($i = 0; $i < $length; $i++) {
				$key .= $keys[array_rand($keys)];
			}
			return $key;
		}

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
	}
}

function ZPN() {
	return ZPN::instance();
}

ZPN();
