<?php
/**
 * Plugin Name: Push Notifications For Web
 * Plugin URL: https://wordpress.org/plugin-url/
 * Description: Best platform for sending web push notifications.
 * Version: 1.1
 * Author: ZealousWeb
 * Author URI: https://www.zealousweb.com
 * Developer: The Zealousweb Team
 * Developer E-Mail: opensource@zealousweb.com
 * Text Domain: push-notifications-for-web
 * Domain Path: /languages
 *
 * Copyright: © 2009-2022 ZealousWeb.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit; 

/**
 * Basic plugin definitions
 *
 * @package Web Push Notification
 * @since 1.1
 */

if ( !defined( 'ZPN_VERSION' ) ) {
	define( 'ZPN_VERSION', '1.1' ); // Version of plugin
}

if ( !defined( 'ZPN_FILE' ) ) {
	define( 'ZPN_FILE', __FILE__ ); // Plugin File
}

if ( !defined( 'ZPN_DIR' ) ) {
	define( 'ZPN_DIR', dirname( __FILE__ ) ); // Plugin dir
}

if ( !defined( 'ZPN_URL' ) ) {
	define( 'ZPN_URL', plugin_dir_url( __FILE__ ) ); // Plugin url
}

if ( !defined( 'ZPN_PLUGIN_BASENAME' ) ) {
	define( 'ZPN_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); // Plugin base name
}

if ( !defined( 'ZPN_META_PREFIX' ) ) {
	define( 'ZPN_META_PREFIX', 'zpn_' ); // Plugin metabox prefix
}

if ( !defined( 'ZPN_PREFIX' ) ) {
	define( 'ZPN_PREFIX', 'zpn' ); // Plugin prefix
}

if ( !defined( 'ZPN_POST_TYPE' ) ) {
	define( 'ZPN_POST_TYPE', 'zpn_data' ); // Plugin post type
}

if ( !defined( 'ZPN_SUPPORT' ) ) {
	define( 'ZPN_SUPPORT', 'https://zealousweb.com/support/' ); // Plugin Support Link
}

if ( !defined( 'ZPN_DOCUMENT' ) ) {
	define( 'ZPN_DOCUMENT', '#' ); // Plugin Document Link
}

if ( !defined( 'ZPN_PRODUCT_LINK' ) ) {
	define( 'ZPN_PRODUCT_LINK', '#' ); // Plugin Product Link
}


/**
 * Initialize the main class
 */
if ( !function_exists( 'ZPN' ) ) {

	if ( is_admin() ) {
		require_once( ZPN_DIR . '/inc/admin/class.' . ZPN_PREFIX . '.admin.php' );
		require_once( ZPN_DIR . '/inc/admin/class.' . ZPN_PREFIX . '.admin.action.php' );
		require_once( ZPN_DIR . '/inc/admin/class.' . ZPN_PREFIX . '.admin.filter.php' );
	} else {
		require_once( ZPN_DIR . '/inc/front/class.' . ZPN_PREFIX . '.front.php' );
		require_once( ZPN_DIR . '/inc/front/class.' . ZPN_PREFIX . '.front.action.php' );
		require_once( ZPN_DIR . '/inc/front/class.' . ZPN_PREFIX . '.front.filter.php' );
	}

	require_once( ZPN_DIR . '/inc/lib/class.' . ZPN_PREFIX . '.lib.php' );

	//Initialize all the things.
	require_once( ZPN_DIR . '/inc/class.' . ZPN_PREFIX . '.php' );
}
