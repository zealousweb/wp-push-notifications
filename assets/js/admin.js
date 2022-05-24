( function($) {
	"use strict";

	// Show Image Gallery and prview of selected image
	$(document).on( 'click', '.notificatio-img', function(e){
 
		e.preventDefault();
 
		var button = $(this),
		custom_uploader = wp.media({
			title: 'Insert image',
			library : {
				// uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
				type : 'image'
			},
			button: {
				text: 'Use this image' // button label text
			},
			multiple: false
		}).on('select', function() { // it also has "open" and "close" events
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			if ( attachment.subtype == 'png' || attachment.subtype == 'jpg' || attachment.subtype == 'jpeg' ) {
				button.next().show();
				button.html('<img src="' + attachment.url + '">').next().next().show();
				button.next().next().val(attachment.id);
			} else { 
				alert( 'Uploaded image is not supported, please try with new one...!' )
			}
		}).open();
 
	});
 
	// Remove uploaded images for notification
	$(document).on('click', '.notificatio-img-rmv', function(e){
 
		e.preventDefault();
 
		var button = $(this);
		button.next().val(''); // emptying the hidden field
		button.hide().prev().html('Upload');
	});

	// Check image type before submit notification
	$(document).on('submit', '.send_notification', function() {
		if(confirm('Do you really want to send the notification ?')) {
			return true;
		}
		return false;
	});

	jQuery( '#wpn_post_icon' ).on( 'mouseenter click', function() {

		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#wpn_post_icon' ).pointer({
			pointerClass: 'wp-pointer zealwpn-pointer',
			content: translate_string_wpn.wpn_post_icon,
			position: 'left center',
		} ).pointer('open');
	});

	jQuery( '#wpn_post_image' ).on( 'mouseenter click', function() {

		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#wpn_post_image' ).pointer({
			pointerClass: 'wp-pointer zealwpn-pointer',
			content: translate_string_wpn.wpn_post_image,
			position: 'left center',
		} ).pointer('open');
	});

	jQuery( '#wpn_notification_desc' ).on( 'mouseenter click', function() {

		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#wpn_notification_desc' ).pointer({
			pointerClass: 'wp-pointer zealwpn-pointer',
			content: translate_string_wpn.wpn_notification_desc,
			position: 'left center',
		} ).pointer('open');
	});

	

} )( jQuery );
