<?php
/**
 * ZPN_Admin_Filter Class
 *
 * Handles the admin functionality.
 *
 * @package WordPress
 * @subpackage Push Notifications For Web
 * @since 1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ZPN_Admin_Filter' ) ) {

	/**
	 *  The ZPN_Admin_Filter Class
	 */
	class ZPN_Admin_Filter {

		function __construct() {
			add_filter( 'plugin_action_links_'.ZPN_PLUGIN_BASENAME,	array( $this,'filter__zealwpn_admin_plugin_links'), 10, 2 );
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

		/**
		* Filter: plugin_action_links
		*
		* - Used to add links on Plugins listing page.
		*
		* @method filter__zealwpn_admin_plugin_links
		*
		* @param  array $actions
		*	
		* @return string
		*/
		function filter__zealwpn_admin_plugin_links( $links, $file ) {

			if ( $file != ZPN_PLUGIN_BASENAME ) {
				return $links;
			}

			$settingPage = admin_url("admin.php?page=web_push");

			$settingpageLink = '<a  href="'.$settingPage.'">' . __( 'Settings Page', 'push-notifications-for-web' ) . '</a>';
			array_unshift( $links , $settingpageLink);

			$documentLink = '<a target="_blank" href="'.ZPN_DOCUMENT.'">' . __( 'Document Link', 'push-notifications-for-web' ) . '</a>';
			array_unshift( $links , $documentLink);
		
			return $links;
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
		ZPN()->admin->filter = new ZPN_Admin_Filter;
	} );
}
