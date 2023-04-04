<?php
/**
 * ZPN_Front_Action Class
 *
 * Handles the Frontend Actions.
 *
 * @package WordPress
 * @subpackage Push Notifications For Web
 * @since 1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ZPN_Front_Action' ) ){

	/**
	 *  The ZPN_Front_Action Class
	 */
	class ZPN_Front_Action {

		function __construct()  {

			add_action( 'wp_enqueue_scripts', array( $this, 'action__wp_enqueue_scripts' ) );

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
		 * Action: wp_enqueue_scripts
		 *
		 * - enqueue script in front side
		 *
		 */
		function action__wp_enqueue_scripts() {

			wp_enqueue_script( ZPN_PREFIX . 'front_js', ZPN_URL . 'assets/js/front.min.js', array( 'jquery' ), ZPN_VERSION );
			wp_enqueue_script( ZPN_PREFIX . 'firebase_app', ZPN_URL . 'assets/js/firebase-app.js', array( 'jquery' ), '8.6.1' );
			wp_enqueue_script( ZPN_PREFIX . 'firebase_messaging', ZPN_URL . 'assets/js/firebase-messaging.js', array( 'jquery' ), '8.6.1' );
			wp_enqueue_script( ZPN_PREFIX . 'firebase_analytics', ZPN_URL . 'assets/js/firebase-analytics.js', array( 'jquery' ), '8.6.1' );
			wp_localize_script( ZPN_PREFIX . 'front_js', 'zealwpn_object',
				array(
					'ajax_url'                      => admin_url( 'admin-ajax.php' ),
					'pluginsUrl'                    => ZPN_URL,
					'notification_server_key'       => get_option('notification_server_key'),
					'notification_apiKey'           => get_option('notification_apiKey'),
					'notification_projectId'        => get_option('notification_projectId'),
					'notification_senderId'         => get_option('notification_senderId'),
					'notification_appId'            => get_option('notification_appId'),
				)
			);
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

	}

	add_action( 'plugins_loaded', function() {
		ZPN()->front->action = new ZPN_Front_Action;
	} );
}
