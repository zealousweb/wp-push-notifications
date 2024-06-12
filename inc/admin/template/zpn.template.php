<?php
/**
* Template Used for Settings Page
*
* @package WordPress
* @subpackage Push Notifications For Web
* @since 1.0
**/

wp_enqueue_script( ZPN_PREFIX . '_admin_js' );
wp_enqueue_style( ZPN_PREFIX . '_admin_css' );

$active_tab = "zealwpn-setup";
if(isset( $_GET["tab"] ) ) {
	if( $_GET["tab"] == "zealwpn-setup" ) {
		$active_tab = "zealwpn-setup";
	}elseif( $_GET["tab"] == "zealwpn-configuration" ) {
		$active_tab = "zealwpn-configuration";
	} elseif( $_GET["tab"] == "zealwpn-send-notification" ) {
		$active_tab = "zealwpn-send-notification";
	}
}

?>

<div class="wrap zealwpn-notification">

	<div id="icon-options-general" class="icon32"></div>

	<h2><?php _e( "Web Push Notification", "push-notifications-for-web" ); ?></h2>
	<h2 class="nav-tab-wrapper">
		<a href="?page=web_push&tab=zealwpn-setup" class="nav-tab <?php if( $active_tab == 'zealwpn-setup' ){ echo 'nav-tab-active'; } ?> "><?php _e( 'Setup', 'push-notifications-for-web' ); ?></a>
		<a href="?page=web_push&tab=zealwpn-send-notification" class="nav-tab <?php if( $active_tab == 'zealwpn-send-notification' ){ echo 'nav-tab-active'; } ?>"><?php _e( 'Manual Push', 'push-notifications-for-web' ); ?></a>
		<a href="?page=web_push&tab=zealwpn-configuration" class="nav-tab <?php if( $active_tab == 'zealwpn-configuration' ){ echo 'nav-tab-active'; } ?>"><?php _e( 'Configuration', 'push-notifications-for-web' ); ?></a>
	</h2>

	<?php
	if( !isset( $_GET["tab"] ) ){
		$nonce_field = wp_nonce_field( 'save_notification_setting_action', 'save_notification_setting_nonce', true, false );
		if( isset( $_POST['save_notification_setting_nonce'] ) && wp_verify_nonce( $_POST['save_notification_setting_nonce'], 'save_notification_setting_action' ) && isset( $_POST['configuration'] ) && $_POST['configuration'] === 'true' ){
			$this->save_notification_setting();
		} ?>
		
		<div class="basic_hint">
			<b><?php echo __( 'To create firebase account, Please follow below steps', 'push-notifications-for-web' ); ?></b></br> <!--phpcs:ignore-->
			<ol>
				<li><?php echo __( "Please register on - <a href='https://console.firebase.google.com/'>https://console.firebase.google.com/</a> and create a project</li>", "push-notifications-for-web" ); ?><!--phpcs:ignore-->
				<li><?php echo __( "After creating project on firebase, create a APP by clicking on 'Add app' button</li>", "push-notifications-for-web" ); ?><!--phpcs:ignore-->
				<li><?php echo __( "When app platform appear, click the 'web' to create your app. Then follow the steps.</li>", "push-notifications-for-web" ); ?><!--phpcs:ignore-->
				<li><?php echo __( "After registered your app, you will see the following configuration field's value. Get these and setup the following configuration.</li>", "push-notifications-for-web" ); ?><!--phpcs:ignore-->
			</ol>
		</div>

		<form method="POST" autocomplete="off" class="configuration">
			<input type="hidden" name="configuration" value="true" />
			<?php wp_nonce_field( 'notification_setting_save', 'setting_save' ); ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="notification_server_key"><?php echo esc_html__( 'Server key', 'push-notifications-for-web' ).' *'; ?></label></th>
						<td>
							<input name="notification_server_key" id="notification_server_key" type="text" value="<?php echo esc_attr(get_option('notification_server_key') ) ; ?>" class="regular-text" required/><br>
							<b></be><?php echo esc_html__( "Enter server key from your firebase app configuration. e.g: AAAAHMbG.. You'll be able to get it from firebase app: settings -> Cloud Messaging section.", "push-notifications-for-web" ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_apiKey"><?php echo esc_html__( 'API key', 'push-notifications-for-web' ).' *'; ?></label></th>
						<td>
							<input name="notification_apiKey" id="notification_apiKey" type="text" value="<?php echo esc_attr(get_option('notification_apiKey') ); ?>" class="regular-text" required/><br>
							<b><?php echo esc_html__( 'Enter api key from your firebase app configuration. e.g: AIzaSyBzyx..', 'push-notifications-for-web' ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_projectId"><?php echo esc_html__( 'Project Id', 'push-notifications-for-web' ).' *'; ?></label></th>
						<td>
							<input name="notification_projectId" id="notification_projectId" type="text" value="<?php echo esc_attr(get_option('notification_projectId') ); ?>" class="regular-text" required/><br>
							<b><?php echo esc_html__( 'Enter project id from your firebase app configuration. e.g: push-notification', 'push-notifications-for-web' ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_senderId"><?php echo esc_html__( 'Sender Id', 'push-notifications-for-web' ).' *'; ?></label></th>
						<td>
							<input name="notification_senderId" id="notification_senderId" type="text" value="<?php echo esc_attr(get_option('notification_senderId')); ?>" class="regular-text" required/><br>
							<b><?php echo esc_html__( 'Enter messaging sender id from your firebase app configuration. e.g: 123594987504', 'push-notifications-for-web' ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_appId"><?php echo esc_html__( 'App Id', 'push-notifications-for-web' ).' *'; ?></label></th>
						<td>
							<input name="notification_appId" id="notification_appId" type="text" value="<?php echo esc_attr(get_option('notification_appId') ); ?>" class="regular-text" required/><br>
							<b><?php echo esc_html__( 'Enter app id from your firebase app configuration. e.g: 1:123593936504:web:62935156478b7848faf275', 'push-notifications-for-web' ); ?></b>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Setting">
			</p>
		</form>

	<?php }elseif( isset( $_GET["tab"] ) && $_GET["tab"] == "zealwpn-setup" ){

		if( isset( $_POST['configuration'] )  && $_POST['configuration'] === 'true' ){ 
			$this->save_notification_setting();
		} ?>

		<div class="basic_hint">
			<b><?php echo __( 'To create firebase account, Please follow below steps', 'push-notifications-for-web' ); ?></b></br><!--phpcs:ignore-->
			<ol>
				<li><?php echo __( "Please register on - <a href='https://console.firebase.google.com/'>https://console.firebase.google.com/</a> and create a project</li>", "push-notifications-for-web" ); ?> <!--phpcs:ignore-->
				<li><?php echo __( "After creating project on firebase, create a APP by clicking on 'Add app' button</li>", "push-notifications-for-web" ); ?> <!--phpcs:ignore-->
				<li><?php echo __( "When app platform appear, click the 'web' to create your app. Then follow the steps.</li>", "push-notifications-for-web" ); ?> <!--phpcs:ignore-->
				<li><?php echo __( "After registered your app, you will see the following configuration field's value. Get these and setup the following configuration.</li>", "push-notifications-for-web" ); ?> <!--phpcs:ignore-->
			</ol>
		</div>

		<form method="POST" autocomplete="off" class="configuration">
			<input type="hidden" name="configuration" value="true" />
			<?php wp_nonce_field( 'notification_setting_save', 'setting_save' ); ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="notification_server_key"><?php echo esc_html__( 'Server key', 'push-notifications-for-web' ).' *'; ?></label></th>
						<td>
							<input name="notification_server_key" id="notification_server_key" type="text" value="<?php echo esc_attr(get_option('notification_server_key') ); ?>" class="regular-text" required/><br>
							<b></be><?php echo esc_html__( "Enter server key from your firebase app configuration. e.g: AAAAHMbG.. You'll be able to get it from firebase app: settings -> Cloud Messaging section.", "push-notifications-for-web" ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_apiKey"><?php echo esc_html__( 'API key', 'push-notifications-for-web' ).' *'; ?></label></th>
						<td>
							<input name="notification_apiKey" id="notification_apiKey" type="text" value="<?php echo esc_attr(get_option('notification_apiKey') ); ?>" class="regular-text" required/><br>
							<b><?php echo esc_html__( 'Enter api key from your firebase app configuration. e.g: AIzaSyBzyx..', 'push-notifications-for-web' ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_projectId"><?php echo esc_html__( 'Project Id', 'push-notifications-for-web' ).' *'; ?></label></th>
						<td>
							<input name="notification_projectId" id="notification_projectId" type="text" value="<?php echo esc_attr(get_option('notification_projectId') ); ?>" class="regular-text" required/><br>
							<b><?php echo esc_html__( 'Enter project id from your firebase app configuration. e.g: push-notification', 'push-notifications-for-web' ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_senderId"><?php echo esc_html__( 'Sender Id', 'push-notifications-for-web' ).' *'; ?></label></th>
						<td>
							<input name="notification_senderId" id="notification_senderId" type="text" value="<?php echo esc_attr(get_option('notification_senderId') ); ?>" class="regular-text" required/><br>
							<b><?php echo esc_html__( 'Enter messaging sender id from your firebase app configuration. e.g: 123594987504', 'push-notifications-for-web' ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_appId"><?php echo esc_html__( 'App Id', 'push-notifications-for-web' ).' *'; ?></label></th>
						<td>
							<input name="notification_appId" id="notification_appId" type="text" value="<?php echo esc_attr(get_option('notification_appId') ); ?>" class="regular-text" required/><br>
							<b><?php echo esc_html__( 'Enter app id from your firebase app configuration. e.g: 1:123593936504:web:62935156478b7848faf275', 'push-notifications-for-web' ); ?></b>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Setting">
			</p>
		</form>
	<?php }


	if( isset( $_GET["tab"] ) && $_GET["tab"] == "zealwpn-send-notification" ) {

		if( isset($_POST['send_notification']) && $_POST['send_notification'] === 'true' ){ 
			$this->send_push_notification_manually();
		} ?>

		<form method="POST" class="send_notification" autocomplete="off" enctype="multipart/form-data" accept-charset="utf-8" >

			<input type="hidden" name="send_notification" value="true" />
			<?php wp_nonce_field( 'notification_fields_update', 'notification_fields' ); ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="notification_title"><?php echo esc_html__( 'Notification Title', 'push-notifications-for-web' ).' *'; ?></label></th>
						<td><input name="notification_title" id="notification_title" type="text" value="" class="regular-text" required/></td>
					</tr>
					<tr>
						<th>
							<label for="notification_desc">
								<?php echo esc_html__( 'Notification Description', 'push-notifications-for-web' ).' *'; ?>
							</label>
							<span class="zealwpn-tooltip hide-if-no-js " id="wpn_notification_desc"></span>
						</th>
						<td><input name="notification_desc" id="notification_desc" type="text" value="" class="regular-text" required/></td>
					</tr>
					<tr>
						<th><label for="notification_link"><?php echo esc_html__( 'Notification Link', 'push-notifications-for-web' ); ?></label></th>
						<td><input name="notification_link" id="notification_link" type="url" value="https://www.google.com" class="regular-text" required/></td>
					</tr>
					<tr>
						<th>
							<label for="notification_icon">
								<?php echo esc_html__( 'Notification Icon', 'push-notifications-for-web' ); ?>
							</label>
							<span class="zealwpn-tooltip hide-if-no-js " id="wpn_post_icon"></span>
							<br><span> <?php echo esc_html__( ' Support only png, jpg, jpeg image', 'push-notifications-for-web' ); ?> </span></th>
						<td>
							<?php
							echo '<a href="#" class="notificatio-img page-title-action">Upload</a>
							<a href="#" class="notificatio-img-rmv page-title-action" style="display:none">Remove</a>
							<input type="hidden" name="notification_icon" value="">';
							?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="notification_image">
								<?php echo esc_html__( 'Notification Image', 'push-notifications-for-web' ); ?>
							</label>
							<span class="zealwpn-tooltip hide-if-no-js " id="wpn_post_image"></span>
							<br><span><?php echo esc_html__( ' Support only png, jpg, jpeg image', 'push-notifications-for-web' ); ?> </span></th>
						<td>
							<?php
							echo '<a href="#" class="notificatio-img page-title-action">Upload</a>
							<a href="#" class="notificatio-img-rmv page-title-action" style="display:none">Remove</a>
							<input type="hidden" name="notification_image" value="">';
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Send Notification">
			</p>
		</form>
	<?php }

	if( isset( $_GET["tab"] ) && $_GET["tab"] == "zealwpn-configuration" ){

		if( isset( $_POST['set_configuration'] ) && $_POST['set_configuration'] === 'true' ){
			$this->save_configuration_setting();
		} ?>

		<h2><?php echo esc_html__( 'New Post Settings', 'push-notifications-for-web' ); ?></h2>

		<form method="POST" class="set_configuration" autocomplete="off">

			<input type="hidden" name="set_configuration" value="true" />
			<?php wp_nonce_field( 'post_configuration_fields', 'new_post_configuration' ); ?>

			<table class="form-table">
				<tbody>
					<tr>
						<th></th>
						<td>
							<input type="checkbox" name="wpn_enable_for_post" id="wpn_enable_for_post" <?php echo esc_attr(get_option('wpn_enable_for_post') ) ? "checked":''; ?>>
							<strong><?php echo esc_html__( 'Enable Web Push', 'push-notifications-for-web' ) ?></strong>
						</td>
					</tr>	

					<tr>
						<th scope="row">
							<label for="wpn_post_icon">
								<?php echo esc_html__( 'Notification Icon', 'push-notifications-for-web' ) ?>
							</label>
							<span class="zealwpn-tooltip hide-if-no-js " id="wpn_post_icon"></span>
						</th>
						<td>
							
							<?php if( get_option('wpn_post_icon') ) { ?>
								<a href="#" class="wpn_post_icon notificatio-img page-title-action">
									<img src="<?php echo esc_url(wp_get_attachment_image_url( get_option('wpn_post_icon') )); ?>" width="150" height="150">
								</a>	
								<a href="#" class="notificatio-img-rmv page-title-action">Remove</a>
							<?php } else { ?>
								<a href="#" class="wpn_post_icon notificatio-img page-title-action">Upload</a>
								<a href="#" class="notificatio-img-rmv page-title-action" style="display:none">Remove</a>	
							<?php } ?>
							<input type="hidden" name="wpn_post_icon" class="wpn_post_img" value="<?php echo esc_attr(get_option('wpn_post_icon') ) ; ?>">

						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="wpn_post_image">
								<?php echo esc_html__( 'Notification Image', 'push-notifications-for-web' ) ?>
							</label>
							<span class="zealwpn-tooltip hide-if-no-js " id="wpn_post_image"></span>
						</th>
						<td>
							<?php if( get_option('wpn_post_image') ) { ?>
								<a href="#" class="wpn_post_image notificatio-img page-title-action">
									<img src="<?php echo esc_url(wp_get_attachment_image_url( get_option('wpn_post_image') ) ) ; ?>" width="150" height="150">
								</a>	
								<a href="#" class="notificatio-img-rmv page-title-action">Remove</a>
							<?php } else { ?>
								<a href="#" class="wpn_post_image notificatio-img page-title-action">Upload</a>
								<a href="#" class="notificatio-img-rmv page-title-action" style="display:none">Remove</a>	
							<?php } ?>
							<input type="hidden" name="wpn_post_image" class="wpn_post_img" value="<?php echo esc_attr(get_option('wpn_post_image') ) ; ?>">

						</td>
					</tr>

				</tbody>
			</table>

			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Settings">
			</p>

		</form>

	<?php }

echo '</div>';


// Localize the script with new data
$translation_array = array(
	'wpn_post_icon'				=> __( '<h3>Notification Icon</h3>' .
							'<p>Please enter any of square image. example - 300*300</p>','push-notifications-for-web' ),
	'wpn_post_image'			=> __( '<h3>Notification Image </h3>' .
							'<p>Please enter  any of square image. example - 300*300</p>','push-notifications-for-web' ),
	'wpn_notification_desc'		=> __( '<h3>Notification Description</h3>' .
							'<p>Please enter maximum 160 character in notification.</p>','push-notifications-for-web' ),
	);

wp_enqueue_script( 'wp-pointer' );
wp_enqueue_style( 'wp-pointer' );
wp_localize_script( ZPN_PREFIX . '_admin_js', 'translate_string_wpn', $translation_array );