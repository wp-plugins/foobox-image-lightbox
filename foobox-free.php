<?php
/*
Plugin Name: FooBox Free Image Lightbox
Plugin URI: http://fooplugins.com/plugins/foobox/
Description: The best responsive image lightbox for WordPress.
Version: 1.0.3
Author: FooPlugins
Author URI: http://fooplugins.com
License: GPL2
Text Domain: fooboxfree
Domain Path: /languages
*/

if ( ! defined( 'FOOBOX_FREE_PLUGIN_URL' ) ) {
	define( 'FOOBOX_FREE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if (!class_exists('Foobox_Free')) {

	define( 'FOOBOXFREE_SLUG', 'foobox-free' );
	define( 'FOOBOXFREE_PATH', plugin_dir_path( __FILE__ ));
	define( 'FOOBOXFREE_URL', plugin_dir_url( __FILE__ ));
	define( 'FOOBOXFREE_FILE', __FILE__ );
	define( 'FOOBOXFREE_VERSION', '1.0.3' );

	// Includes
	require_once FOOBOXFREE_PATH . "includes/class-settings.php";
	require_once FOOBOXFREE_PATH . "includes/class-script-generator.php";
	require_once FOOBOXFREE_PATH . "includes/class-foogallery-foobox-free-extension.php";
	require_once FOOBOXFREE_PATH . "includes/foopluginbase/bootstrapper.php";

	class Foobox_Free extends Foo_Plugin_Base_v2_1 {

		const JS                   = 'foobox.free.min.js';
		const CSS                  = 'foobox.free.min.css';
		const FOOBOX_URL           = 'http://fooplugins.com/plugins/foobox/?utm_source=fooboxfreeplugin&utm_medium=fooboxfreeprolink&utm_campaign=foobox_free_pro_tab';
		const BECOME_AFFILIATE_URL = 'http://fooplugins.com/affiliate-program/';

		private static $instance;

		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Foobox_Free ) ) {
				self::$instance = new Foobox_Free();
			}
			return self::$instance;
		}

		/**
		 * Initialize the plugin by setting localization, filters, and administration functions.
		 */
		private function __construct() {
			//init FooPluginBase
			$this->init( FOOBOXFREE_FILE, FOOBOXFREE_SLUG, FOOBOXFREE_VERSION, 'FooBox FREE' );

			if (is_admin()) {
				add_action('admin_head', array($this, 'admin_inline_content'));
				add_action('foobox-free-settings_custom_type_render', array($this, 'custom_admin_settings_render'));
				add_action('foobox-free-settings-sidebar', array($this, 'settings_sidebar'));
				new FooBox_Free_Settings();
			} else {

				// Render JS to the front-end pages
				add_action('wp_enqueue_scripts', array($this, 'frontend_print_scripts'), 20);
				add_action('foobox-free_inline_scripts', array($this, 'inline_dynamic_js'));

				// Render CSS to the front-end pages
				add_action('wp_enqueue_scripts', array($this, 'frontend_print_styles'));
			}
		}

		function custom_admin_settings_render($args = array()) {
			$type = '';

			extract($args);

			if ($type == 'debug_output') {
				echo '</td></tr><tr valign="top"><td colspan="2">';
				$this->render_debug_info();
			} else if ($type == 'upgrade') {
				echo '</td></tr><tr valign="top"><td colspan="2">';
				$this->render_upgrade_notice();
			} else if ($type == 'foobot_says') {
				echo '</td></tr><tr valign="top"><td colspan="2">';
				$this->render_foobot_recommendations();
			} else if ($type == 'poweredby') {
				echo '<input readonly disabled type="checkbox" value="on" checked /><small>' . __('This cannot be turned off in the FREE version', 'foobox-free') . '</small>';
			}
		}

		function generate_javascript($debug = false) {
			return FooBox_Free_Script_Generator::generate_javascript($this, $debug);
		}

		function render_for_archive() {
			if (is_admin()) return true;

			return !is_singular();
		}

		function render_debug_info() {

			echo '<strong>Javascript:<br /><pre style="width:600px; overflow:scroll;">';

			echo htmlentities($this->generate_javascript(true));

			echo '</pre><br />Settings:<br /><pre style="width:600px; overflow:scroll;">';

			echo htmlentities( print_r(get_option($this->plugin_slug), true) );

			echo '</pre>';
		}

		function render_upgrade_notice() {
			require_once FOOBOXFREE_PATH . "includes/upgrade.php";
		}

		function render_foobot_recommendations() {
			require_once FOOBOXFREE_PATH . "includes/recommend.php";
		}

		function settings_sidebar() {
			require_once FOOBOXFREE_PATH . "includes/settings-sidebar.php";
		}

		function frontend_init() {
			add_action('wp_head', array($this, 'inline_dynamic_js'));
		}

		function admin_print_styles() {
			parent::admin_print_styles();
			$this->frontend_print_styles();
		}

		function admin_print_scripts() {
			parent::admin_print_scripts();
			$this->register_and_enqueue_js( self::JS );
		}

		function admin_inline_content() {
			if ( foo_check_plugin_settings_page( FOOBOXFREE_SLUG ) ) {
				$this->inline_dynamic_js();
			}
		}

		function frontend_print_styles() {
			$this->register_and_enqueue_css( self::CSS );
		}

		function frontend_print_scripts() {
			$this->register_and_enqueue_js(
				$file = self::JS,
				$d = array('jquery'),
				$v = false,
				$f = false);
		}

		function inline_dynamic_js() {
			$foobox_js = $this->generate_javascript();
			echo '<script type="text/javascript">' . $foobox_js . '</script>';
		}

		/**
		 * PLEASE NOTE : This is only here to avoid the problem of hard-coded lightboxes.
		 * This is not meant to be malicious code to override all lightboxes in favour of FooBox.
		 * But sometimes theme authors hard code galleries to use their built-in lightbox of choice, which is not the desired solution for everyone.
		 * This can be turned off in the FooBox settings page
		 */
		function disable_other_lightboxes() {
			?>
			<script type="text/javascript">
				jQuery.fn.prettyPhoto = function () {
					return this;
				};
				jQuery.fn.fancybox = function () {
					return this;
				};
				jQuery.fn.fancyZoom = function () {
					return this;
				};
				jQuery.fn.colorbox = function () {
					return this;
				};
			</script>
		<?php
		}
	}
}

Foobox_Free::get_instance();

/**
 * Activation check - make sure the FREE and PRO versions are not both running
 */
function foobox_free_activation_check(){
	if (class_exists('fooboxV2')) {
		deactivate_plugins( plugin_basename(__FILE__) ); // Deactivate ourself
		die( __('Sorry, but you can\'t run the FREE and PRO version of FooBox at the same time!', 'foobox-free') );
	}
}

register_activation_hook(__FILE__, 'foobox_free_activation_check');