<?php
/**
* Template Used for Settings Page
*
* @package WordPress
* @subpackage ZealPush Notification For WordPress
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

	<h2><?php _e( "Web Push Notification", "zeal-push-notification" ); ?></h2>
	<h2 class="nav-tab-wrapper">
		<a href="?page=web_push&tab=zealwpn-setup" class="nav-tab <?php if( $active_tab == 'zealwpn-setup' ){ echo 'nav-tab-active'; } ?> "><?php _e( 'Setup', 'zeal-push-notification' ); ?></a>
		<a href="?page=web_push&tab=zealwpn-send-notification" class="nav-tab <?php if( $active_tab == 'zealwpn-send-notification' ){ echo 'nav-tab-active'; } ?>"><?php _e( 'Manual Push', 'zeal-push-notification' ); ?></a>
		<a href="?page=web_push&tab=zealwpn-configuration" class="nav-tab <?php if( $active_tab == 'zealwpn-configuration' ){ echo 'nav-tab-active'; } ?>"><?php _e( 'Configuration', 'zeal-push-notification' ); ?></a>
	</h2>

	<?php
	if( !isset( $_GET["tab"] ) ){

		if( isset( $_POST['configuration'] ) && $_POST['configuration'] === 'true' ){
			$this->save_notification_setting();
		} ?>
		
		<div class="basic_hint">
			<b><?php echo __( 'To create firebase account, Please follow below steps', 'zeal-push-notification' ); ?></b></br>
			<ol>
				<li><?php echo __( "Please register on - <a href='https://console.firebase.google.com/'>https://console.firebase.google.com/</a> and create a project</li>", "zeal-push-notification" ); ?>
				<li><?php echo __( "After creating project on firebase, create a APP by clicking on 'Add app' button</li>", "zeal-push-notification" ); ?>
				<li><?php echo __( "When app platform appear, click the 'web' to create your app. Then follow the steps.</li>", "zeal-push-notification" ); ?>
				<li><?php echo __( "After registered your app, you will see the following configuration field's value. Get these and setup the following configuration.</li>", "zeal-push-notification" ); ?>
			</ol>
		</div>

		<form method="POST" autocomplete="off" class="configuration">
			<input type="hidden" name="configuration" value="true" />
			<?php wp_nonce_field( 'notification_setting_save', 'setting_save' ); ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="notification_server_key"><?php echo __( 'Server key', 'zeal-push-notification' ).' *'; ?></label></th>
						<td>
							<input name="notification_server_key" id="notification_server_key" type="text" value="<?php echo get_option('notification_server_key'); ?>" class="regular-text" required/><br>
							<b></be><?php echo __( "Enter server key from your firebase app configuration. e.g: AAAAHMbG.. You'll be able to get it from firebase app: settings -> Cloud Messaging section.", "zeal-push-notification" ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_apiKey"><?php echo __( 'API key', 'zeal-push-notification' ).' *'; ?></label></th>
						<td>
							<input name="notification_apiKey" id="notification_apiKey" type="text" value="<?php echo get_option('notification_apiKey'); ?>" class="regular-text" required/><br>
							<b><?php echo __( 'Enter api key from your firebase app configuration. e.g: AIzaSyBzyx..', 'zeal-push-notification' ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_projectId"><?php echo __( 'Project Id', 'zeal-push-notification' ).' *'; ?></label></th>
						<td>
							<input name="notification_projectId" id="notification_projectId" type="text" value="<?php echo get_option('notification_projectId'); ?>" class="regular-text" required/><br>
							<b><?php echo __( 'Enter project id from your firebase app configuration. e.g: push-notification', 'zeal-push-notification' ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_senderId"><?php echo __( 'Sender Id', 'zeal-push-notification' ).' *'; ?></label></th>
						<td>
							<input name="notification_senderId" id="notification_senderId" type="text" value="<?php echo get_option('notification_senderId'); ?>" class="regular-text" required/><br>
							<b><?php echo __( 'Enter messaging sender id from your firebase app configuration. e.g: 123594987504', 'zeal-push-notification' ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_appId"><?php echo __( 'App Id', 'zeal-push-notification' ).' *'; ?></label></th>
						<td>
							<input name="notification_appId" id="notification_appId" type="text" value="<?php echo get_option('notification_appId'); ?>" class="regular-text" required/><br>
							<b><?php echo __( 'Enter app id from your firebase app configuration. e.g: 1:123593936504:web:62935156478b7848faf275', 'zeal-push-notification' ); ?></b>
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
			<b><?php echo __( 'To create firebase account, Please follow below steps', 'zeal-push-notification' ); ?></b></br>
			<ol>
				<li><?php echo __( "Please register on - <a href='https://console.firebase.google.com/'>https://console.firebase.google.com/</a> and create a project</li>", "zeal-push-notification" ); ?>
				<li><?php echo __( "After creating project on firebase, create a APP by clicking on 'Add app' button</li>", "zeal-push-notification" ); ?>
				<li><?php echo __( "When app platform appear, click the 'web' to create your app. Then follow the steps.</li>", "zeal-push-notification" ); ?>
				<li><?php echo __( "After registered your app, you will see the following configuration field's value. Get these and setup the following configuration.</li>", "zeal-push-notification" ); ?>
			</ol>
		</div>

		<form method="POST" autocomplete="off" class="configuration">
			<input type="hidden" name="configuration" value="true" />
			<?php wp_nonce_field( 'notification_setting_save', 'setting_save' ); ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="notification_server_key"><?php echo __( 'Server key', 'zeal-push-notification' ).' *'; ?></label></th>
						<td>
							<input name="notification_server_key" id="notification_server_key" type="text" value="<?php echo get_option('notification_server_key'); ?>" class="regular-text" required/><br>
							<b></be><?php echo __( "Enter server key from your firebase app configuration. e.g: AAAAHMbG.. You'll be able to get it from firebase app: settings -> Cloud Messaging section.", "zeal-push-notification" ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_apiKey"><?php echo __( 'API key', 'zeal-push-notification' ).' *'; ?></label></th>
						<td>
							<input name="notification_apiKey" id="notification_apiKey" type="text" value="<?php echo get_option('notification_apiKey'); ?>" class="regular-text" required/><br>
							<b><?php echo __( 'Enter api key from your firebase app configuration. e.g: AIzaSyBzyx..', 'zeal-push-notification' ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_projectId"><?php echo __( 'Project Id', 'zeal-push-notification' ).' *'; ?></label></th>
						<td>
							<input name="notification_projectId" id="notification_projectId" type="text" value="<?php echo get_option('notification_projectId'); ?>" class="regular-text" required/><br>
							<b><?php echo __( 'Enter project id from your firebase app configuration. e.g: push-notification', 'zeal-push-notification' ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_senderId"><?php echo __( 'Sender Id', 'zeal-push-notification' ).' *'; ?></label></th>
						<td>
							<input name="notification_senderId" id="notification_senderId" type="text" value="<?php echo get_option('notification_senderId'); ?>" class="regular-text" required/><br>
							<b><?php echo __( 'Enter messaging sender id from your firebase app configuration. e.g: 123594987504', 'zeal-push-notification' ); ?></b>
						</td>
					</tr>
					<tr>
						<th><label for="notification_appId"><?php echo __( 'App Id', 'zeal-push-notification' ).' *'; ?></label></th>
						<td>
							<input name="notification_appId" id="notification_appId" type="text" value="<?php echo get_option('notification_appId'); ?>" class="regular-text" required/><br>
							<b><?php echo __( 'Enter app id from your firebase app configuration. e.g: 1:123593936504:web:62935156478b7848faf275', 'zeal-push-notification' ); ?></b>
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
						<th><label for="notification_title"><?php echo __( 'Notification Title', 'zeal-push-notification' ).' *'; ?></label></th>
						<td><input name="notification_title" id="notification_title" type="text" value="" class="regular-text" required/></td>
					</tr>
					<tr>
						<th>
							<label for="notification_desc">
								<?php echo __( 'Notification Description', 'zeal-push-notification' ).' *'; ?>
							</label>
							<span class="zealwpn-tooltip hide-if-no-js " id="wpn_notification_desc"></span>
						</th>
						<td><input name="notification_desc" id="notification_desc" type="text" value="" class="regular-text" required/></td>
					</tr>
					<tr>
						<th><label for="notification_link"><?php echo __( 'Notification Link', 'zeal-push-notification' ); ?></label></th>
						<td><input name="notification_link" id="notification_link" type="url" value="https://www.google.com" class="regular-text" required/></td>
					</tr>
					<tr>
						<th>
							<label for="notification_icon">
								<?php echo __( 'Notification Icon', 'zeal-push-notification' ); ?>
							</label>
							<span class="zealwpn-tooltip hide-if-no-js " id="wpn_post_icon"></span>
							<br><span> <?php echo __( ' Support only png, jpg, jpeg image', 'zeal-push-notification' ); ?> </span></th>
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
								<?php echo __( 'Notification Image', 'zeal-push-notification' ); ?>
							</label>
							<span class="zealwpn-tooltip hide-if-no-js " id="wpn_post_image"></span>
							<br><span><?php echo __( ' Support only png, jpg, jpeg image', 'zeal-push-notification' ); ?> </span></th>
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

		<h2><?php echo __( 'New Post Settings', 'zeal-push-notification' ); ?></h2>

		<form method="POST" class="set_configuration" autocomplete="off">

			<input type="hidden" name="set_configuration" value="true" />
			<?php wp_nonce_field( 'post_configuration_fields', 'new_post_configuration' ); ?>

			<table class="form-table">
				<tbody>
					<tr>
						<th></th>
						<td>
							<input type="checkbox" name="wpn_enable_for_post" id="wpn_enable_for_post" <?php echo get_option('wpn_enable_for_post') ? "checked":''; ?>>
							<strong><?php echo __( 'Enable Web Push', 'zeal-push-notification' ) ?></strong>
						</td>
					</tr>	

					<tr>
						<th scope="row">
							<label for="wpn_post_icon">
								<?php echo __( 'Notification Icon', 'zeal-push-notification' ) ?>
							</label>
							<span class="zealwpn-tooltip hide-if-no-js " id="wpn_post_icon"></span>
						</th>
						<td>
							
							<?php if( get_option('wpn_post_icon') ) { ?>
								<a href="#" class="wpn_post_icon notificatio-img page-title-action">
									<img src="<?php echo wp_get_attachment_image_url( get_option('wpn_post_icon') ); ?>" width="150" height="150">
								</a>	
								<a href="#" class="notificatio-img-rmv page-title-action">Remove</a>
							<?php } else { ?>
								<a href="#" class="wpn_post_icon notificatio-img page-title-action">Upload</a>
								<a href="#" class="notificatio-img-rmv page-title-action" style="display:none">Remove</a>	
							<?php } ?>
							<input type="hidden" name="wpn_post_icon" class="wpn_post_img" value="<?php echo get_option('wpn_post_icon'); ?>">

						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="wpn_post_image">
								<?php echo __( 'Notification Image', 'zeal-push-notification' ) ?>
							</label>
							<span class="zealwpn-tooltip hide-if-no-js " id="wpn_post_image"></span>
						</th>
						<td>
							<?php if( get_option('wpn_post_image') ) { ?>
								<a href="#" class="wpn_post_image notificatio-img page-title-action">
									<img src="<?php echo wp_get_attachment_image_url( get_option('wpn_post_image') ); ?>" width="150" height="150">
								</a>	
								<a href="#" class="notificatio-img-rmv page-title-action">Remove</a>
							<?php } else { ?>
								<a href="#" class="wpn_post_image notificatio-img page-title-action">Upload</a>
								<a href="#" class="notificatio-img-rmv page-title-action" style="display:none">Remove</a>	
							<?php } ?>
							<input type="hidden" name="wpn_post_image" class="wpn_post_img" value="<?php echo get_option('wpn_post_image'); ?>">

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
							'<p>Please enter any of square image. example - 300*300</p>','zeal-push-notification' ),
	'wpn_post_image'			=> __( '<h3>Notification Image </h3>' .
							'<p>Please enter  any of square image. example - 300*300</p>','zeal-push-notification' ),
	'wpn_notification_desc'		=> __( '<h3>Notification Description</h3>' .
							'<p>Please enter maximum 160 character in notification.</p>','zeal-push-notification' ),
	);

wp_enqueue_script( 'wp-pointer' );
wp_enqueue_style( 'wp-pointer' );
wp_localize_script( ZPN_PREFIX . '_admin_js', 'translate_string_wpn', $translation_array );