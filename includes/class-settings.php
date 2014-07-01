<?php

if ( !class_exists( 'FooBox_Free_Settings' ) ) {

	class FooBox_Free_Settings {

		function __construct() {
			add_filter('foobox-free-admin_settings', array($this, 'create_settings'));
		}

		function create_settings() {
			//region General Tab
			$tabs['general'] = __('General', 'foobox-free');

			$sections['attach'] = array(
				'tab' => 'general',
				'name' => __('What do you want to attach FooBox to?', 'foobox-free')
			);

			$settings[] = array(
				'id'      => 'enable_galleries',
				'title'   => __( 'WordPress Galleries', 'foobox-free' ),
				'desc'    => __( 'Enable FooBox for all WordPress image galleries.', 'foobox-free' ),
				'default' => 'on',
				'type'    => 'checkbox',
				'section' => 'attach',
				'tab'     => 'general'
			);

			$settings[] = array(
				'id'      => 'enable_captions',
				'title'   => __( 'WordPress Images With Captions', 'foobox-free' ),
				'desc'    => __( 'Enable FooBox for all WordPress images that have captions.', 'foobox-free' ),
				'default' => 'on',
				'type'    => 'checkbox',
				'section' => 'attach',
				'tab'     => 'general'
			);

			$settings[] = array(
				'id'      => 'enable_attachments',
				'title'   => __( 'Attachment Images', 'foobox-free' ),
				'desc'    => __( 'Enable FooBox for all media images included in posts or pages.', 'foobox-free' ),
				'default' => 'on',
				'type'    => 'checkbox',
				'section' => 'attach',
				'tab'     => 'general'
			);

			$sections['show_love'] = array(
				'tab' => 'settings',
				'name' => __('Show us some love! We would really appreciate your support.', 'foobox-free')
			);

			$settings[] = array(
				'id'      => 'powered_by_link',
				'title'   => __( 'Show "powered by" link', 'foobox-free' ),
				'desc'    => __( 'Help support this free plugin by displaying a small "powered by foobox" link under the lightbox.', 'foobox-free' )
					. '<br />' . __('View the demo on this page to see the "powered by" link in action.', 'foobox-free'),
				'default' => 'off',
				'type'    => 'checkbox',
				'section' => 'show_love',
				'tab'     => 'general'
			);

			$become_affiliate_link = sprintf( '<br /><a target="_blank" href="%s">%s</a> %s',
				Foobox_Free::BECOME_AFFILIATE_URL,
				__( 'Become an affiliate', 'foobox-free' ),
				__( ' and paste in your affiliate URL above.', 'foobox-free' )
			);

			$settings[] = array(
				'id'      => 'powered_by_url',
				'title'   => __( 'Affiliate Link', 'foobox-free' ),
				'desc'    => __( 'If you show the "powered by" link, you can promote FooBox and make a commission from sales. Everybody wins!', 'foobox-free' ) . $become_affiliate_link,
				'type'    => 'text',
				'section' => 'show_love',
				'tab'     => 'general'
			);

			$sections['settings'] = array(
				'tab' => 'settings',
				'name' => __('Display Settings', 'foobox-free')
			);

			$settings[] = array(
				'id'      => 'fit_to_screen',
				'title'   => __( 'Fit To Screen', 'foobox-free' ),
				'desc'    => __( 'Force smaller images to fit the screen dimensions.', 'foobox-free' ),
				'default' => 'off',
				'type'    => 'checkbox',
				'section' => 'settings',
				'tab'     => 'general'
			);

			$settings[] = array(
				'id'      => 'hide_scrollbars',
				'title'   => __( 'Hide Page Scrollbars', 'foobox-free' ),
				'desc'    => __( 'Hide the page\'s scrollbars when FooBox is visible.', 'foobox-free' ),
				'default' => 'on',
				'type'    => 'checkbox',
				'section' => 'settings',
				'tab'     => 'general'
			);

			$settings[] = array(
				'id'      => 'show_count',
				'title'   => __( 'Show Counter', 'foobox-free' ),
				'desc'    => __( 'Shows a counter under the FooBox modal when viewing a gallery of images.', 'foobox-free' ),
				'default' => 'on',
				'type'    => 'checkbox',
				'section' => 'settings',
				'tab'     => 'general'
			);
			$settings[] = array(
				'id'      => 'captions_show_on_hover',
				'title'   => __( 'Show Captions On Hover', 'foobox-free' ),
				'desc'    => __( 'Only show the caption when hovering over the image.', 'foobox-free' ),
				'type'    => 'checkbox',
				'section' => 'settings',
				'tab'     => 'general'
			);

			$settings[] = array(
				'id'      => 'error_message',
				'title'   => __( 'Error Message', 'foobox-free' ),
				'desc'    => __( 'The error message to display when an image has trouble loading.', 'foobox-free' ),
				'default' => __( 'Could not load the item', 'foobox-free' ),
				'type'    => 'text',
				'section' => 'settings',
				'tab'     => 'general'
			);

			//endregion

			//region Advanced Tab

			$tabs['advanced'] = __('Advanced', 'foobox-free');

			$settings[] = array(
				'id'      => 'close_overlay_click',
				'title'   => __( 'Close On Overlay Click', 'foobox-free' ),
				'desc'    => __( 'Should the FooBox lightbox close when the overlay is clicked.', 'foobox-free' ),
				'default' => 'on',
				'type'    => 'checkbox',
				'tab'     => 'advanced'
			);

			$settings[] = array(
				'id'      => 'disable_others',
				'title'   => __( 'Disable Other Lightboxes', 'foobox-free' ),
				'desc'    => __( 'Certain themes and plugins use a hard-coded lightbox, which make it very difficult to override.<br>By enabling this setting, we inject a small amount of javascript onto the page which attempts to get around this issue.<br>But please note this is not guaranteed, as we cannot account for every lightbox solution out there :)', 'foobox-free' ),
				'type'    => 'checkbox',
				'tab'     => 'advanced'
			);

			$settings[] = array(
				'id'      => 'enable_debug',
				'title'   => __( 'Enable Debug Mode', 'foobox-free' ),
				'desc'    => __( 'Show an extra debug information tab to help debug any issues.', 'foobox-free' ),
				'type'    => 'checkbox',
				'tab'     => 'advanced'
			);

			//endregion

			//region Debug Tab
			$foobox_free = Foobox_Free::get_instance();

			if ( $foobox_free->options()->is_checked( 'enable_debug', false ) ) {

				$tabs['debug'] = __('Debug', 'foobox-free');

				$settings[] = array(
					'id'      => 'debug_output',
					'title'   => __( 'Debug Information', 'foobox-free' ),
					'type'    => 'debug_output',
					'tab'     => 'debug'
				);
			}
			//endregion

			//region 'FooBot Says' tab
			$tabs['foobot_says'] = __('FooBot Says...', 'foobox-free');

			$settings[] = array(
				'id'    => 'upgrade',
				'title' => '',
				'type'  => 'foobot_says',
				'tab'   => 'foobot_says'
			);
			//endregion

			//region Upgrade tab
			$tabs['upgrade'] = __('Upgrade to PRO!', 'foobox-free');

			$link = sprintf( '<p><a target="_blank" href="%s">%s</a></p><br>', FooBox_Free::FOOBOX_URL, __('Upgrade to the PRO version!', 'foobox-free') );

			$settings[] = array(
				'id'    => 'upgrade',
				'title' => $link . __('There are tons of reasons...', 'foobox-free'),
				'type'  => 'upgrade',
				'tab'   => 'upgrade'
			);
			//endregion

			return array(
				'tabs' => $tabs,
				'sections' => $sections,
				'settings' => $settings
			);
		}
	}
}