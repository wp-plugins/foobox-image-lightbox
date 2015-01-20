<?php
if ( !class_exists( 'FooGallery_FooBox_Free_Extension' ) ) {

	class FooGallery_FooBox_Free_Extension {

		function __construct() {
			//integration with FooGallery
			add_filter( 'foogallery_gallery_template_field_lightboxes', array($this, 'add_lightbox') );
		}

		function add_lightbox($lightboxes) {
			$lightboxes['foobox-free'] = __( 'FooBox Free', 'foobox-free' );
			return $lightboxes;
		}
	}
}