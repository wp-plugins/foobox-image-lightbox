<?php
$instance = Foobox_Free::get_instance();
$info = $instance->get_plugin_info();
$title = sprintf( __( 'Welcome to FooBox FREE %s', 'foobox-image-lightbox' ), $info['version'] );
$tagline = sprintf( __( 'Thank you for choosing FooBox Image Lightbox! A great looking and responsive lightbox for your WordPress website!', 'foobox-image-lightbox' ) );
$settings_url = admin_url( 'options-general.php?page=foobox-free' );

?>
<style>
	.about-wrap img.fooboxfree-settings-screenshot {
		float:right;
		margin-left: 20px;
	}

	.fooboxfree-badge-foobot {
		position: absolute;
		top: 15px;
		right: 0;
		background:url(<?php echo FOOBOXFREE_URL; ?>assets/foobot.png) no-repeat;
		width:109px;
		height:200px;
	}
	.feature-section h2 {
		margin-top: 0;
	}

	.about-wrap h2.nav-tab-wrapper {
		margin-bottom: 20px;
	}

</style>
<div class="wrap about-wrap">
	<h1><?php echo $title; ?></h1>
	<div class="about-text">
		<?php echo $tagline; ?>
	</div>
	<div class="fooboxfree-badge-foobot"></div>
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab nav-tab-active" href="#">
			<?php _e( 'Getting Started', 'foobox-image-lightbox' ); ?>
		</a>
		<a target="_blank" class="nav-tab" href="http://fooplugins.com/plugins/foobox/?utm_source=fooboxfreeplugin&utm_medium=fooboxfreeupgradetoprolink&utm_campaign=foobox_free_getting_started">
			<?php _e( 'Get FooBox PRO!', 'foobox-image-lightbox' ); ?>
		</a>
		<a target="_blank" class="nav-tab" href="http://fooplugins.com">
			<?php _e( 'FooPlugins.com', 'foobox-image-lightbox' ); ?>
		</a>
	</h2>
	<div class="changelog">

		<div class="feature-section">

			<img src="<?php echo FOOBOXFREE_URL . 'assets/screenshots/admin_settings.jpg'; ?>" class="fooboxfree-settings-screenshot"/>

			<h2><?php _e( 'Zero Configuration!', 'foobox-image-lightbox' );?></h2>

			<p><?php _e( 'FooBox FREE will work out-of-the-box with all WordPress galleries and attachments - no configuration needed! Or customize the settings to your needs...', 'foobox-image-lightbox' ); ?></p>

			<h4><?php printf( __( '<a href="%s">Settings &rarr; FooBox FREE</a>', 'foobox-image-lightbox' ), esc_url ( $settings_url ) ); ?></h4>

			<h2><?php _e( 'Looking For More Features?', 'foobox-image-lightbox' );?></h2>
			<p>
				<?php _e( 'FooBox Free is a great way to display your images, but why not take it to the next level? Upgrade to FooBox Pro now and add social sharing and a slew of other advanced features to your images, video, and HTML content ...all inside the best responsive lightbox plugin available.', 'foobox-image-lightbox' );?>
				<a target="_blank" href="http://fooplugins.com/plugins/foobox/?utm_source=fooboxfreeplugin&utm_medium=fooboxfreeprolink&utm_campaign=foobox_free_getting_started"><?php _e( 'Purchase FooBox PRO now!', 'foobox-image-lightbox' );?></a>
			</p>
			<h2><?php _e( 'Get 35% Off FooBox PRO', 'foobox-image-lightbox' );?></h2>
			<p><?php _e( 'As a small thank you for using FooBox FREE, use the coupon code FOOBOXPRO35 to get 35% off the full PRO version.', 'foobox-image-lightbox' ); ?></p>

			<h2><?php _e( 'Do You Need Video Galleries?', 'foobox-image-lightbox' );?></h2>
			<p>
				<?php printf( __( 'Creating Video Galleries has never been easier with our free %s plugin and our premium %s extension, which both work beautifully with FooBox Free!', 'foobox-image-lightbox' ),
					'<strong><a target="_blank" href="http://foo.gallery?utm_source=fooboxfreeplugin&utm_medium=fooboxfreeprolink&utm_campaign=foobox_free_admin_notice">FooGallery</a></strong>',
					'<strong><a target="_blank" href="http://fooplugins.com/plugins/foovideo?utm_source=fooboxfreeplugin&utm_medium=fooboxfreefoovideolink&utm_campaign=foobox_free_admin_notice">FooVideo</a></strong>'); ?>
				<a target="_blank" href="http://fooplugins.com/plugins/foovideo?utm_source=fooboxfreeplugin&utm_medium=fooboxfreefoovideolink&utm_campaign=foobox_free_welcome_page"
			</p>
		</div>
	</div>
</div>
