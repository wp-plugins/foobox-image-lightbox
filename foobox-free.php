<?php
/*
Plugin Name: FooBox Free Image Lightbox
Plugin URI: http://fooplugins.com/plugins/foobox/
Description: The best responsive image lightbox for WordPress.
Version: 1.0.7
Author: FooPlugins
Author URI: http://fooplugins.com
License: GPL2
Text Domain: foobox-free
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
	define( 'FOOBOXFREE_VERSION', '1.0.7' );

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
				add_action('admin_notices', array($this, 'admin_notice'));
				add_action('admin_init', array($this, 'admin_notice_ignore'));
				add_action('admin_init', array($this, 'deactivate_if_pro_activated'), 99);

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

		function admin_notice() {
			if ( current_user_can( 'activate_plugins' ) ) {
				if ( ! get_user_meta( get_current_user_id(), 'foogallery_did_you_know2' ) ) {
					$image_url = FOOBOXFREE_URL . 'img/';
					?>
					<style>
						.foobox-admin-notice-wrapper { margin-top: 10px; display: table; }
						.foobox-admin-notice-wrapper > div { vertical-align:top; }
						.foobox-admin-notice {
							padding: 2px 20px 2px 10px;
							display: table-cell;
							position: relative;
							margin: 0;
							border: 5px solid #5A8F00;
							color: #333;
							background: #FFF;
							-webkit-border-radius: 10px;
							-moz-border-radius: 10px;
							border-radius: 10px;
						}
						.foobox-admin-notice:before {
							content: "";
							position: absolute;
							top: 10px;
							bottom: auto;
							left: -30px;
							border-width: 15px 30px 15px 0;
							border-color: rgba(0, 0, 0, 0) #5A8F00;
							border-style: solid;
							display: block;
							width: 0;
						}
						.foobox-admin-notice:after {
							content: "";
							position: absolute;
							top: 16px;
							bottom: auto;
							left: -21px;
							border-width: 9px 21px 9px 0;
							border-color: rgba(0, 0, 0, 0) #FFF;
							border-style: solid;
							display: block;
							width: 0;
						}
						.foobox-admin-notice-start {
							display: table-cell;
							width:55px;
							padding-right:25px;
							height: 100px;
							background: url(<?php echo $image_url; ?>foobot-notice.png) no-repeat;
						}
						.foobox-admin-notice-close {
							text-decoration: none;
							font-size: 2em;
							position: absolute;
							top: 2px;
							right: 4px;
							font-weight: bold;
							color: #000;
							outline: 0;
							box-shadow: none !important;
						}
					</style>
					<div class="foobox-admin-notice-wrapper">
					<div class="foobox-admin-notice-start"></div>
					<div class="foobox-admin-notice">
					<?php printf( __('Thanks for using %s, get 35%% off the PRO version by using the coupon %s!!', 'foobox-free'), '<strong>FooBox</strong>', '<strong><a target="_blank" href="http://fooplugins.com/plugins/foobox/?utm_source=fooboxfreeplugin&utm_medium=fooboxfreeprolink&utm_campaign=foobox_free_admin_notice">FOOBOXPRO35</a></strong>' ); ?>
					<?php if ( !class_exists( 'FooGallery_Plugin' ) ) { ?>
					<br /><br />
					<?php printf( __('Also, did you know about our free %s plugin?', 'foobox-free' ), '<strong><a target="_blank" href="http://foo.gallery">FooGallery</a></strong>' ); ?>
					<?php _e('It\'s an awesome new gallery plugin that supports image galleries and albums and integrates with FooBox seamlessly!', 'foobox-free' ); ?>
					<?php } ?>
					<a class="foobox-admin-notice-close" title="<?php _e('Hide this notice', 'foobox-free'); ?>" href="<?php echo esc_url( add_query_arg( 'foogallery_did_you_know_ignore', '0' ) ); ?>">&times;</a></div>
					</div><?php
				}
			}
		}

		function admin_notice_ignore() {
			/* If user clicks to dismiss the notice, add that to their user meta */
			if ( isset($_GET['foogallery_did_you_know_ignore']) && '0' == $_GET['foogallery_did_you_know_ignore'] ) {
				add_user_meta( get_current_user_id(), 'foogallery_did_you_know2', 'true', true);
				/* Gets where the user came from after they click Hide Notice */
				if ( wp_get_referer() ) {
					/* Redirects user to where they were before */
					wp_safe_redirect( wp_get_referer() );
				} else {
					/* just in case */
					wp_safe_redirect( admin_url() );
				}
			}
		}

		function deactivate_if_pro_activated() {
			if (class_exists('fooboxV2')) {
				deactivate_plugins( plugin_basename(__FILE__) ); // Deactivate me - FooBox PRO is running!
				wp_die( __('FooBox FREE was deactivated, as FooBox PRO is now running!', 'foobox-free') . ' <a href="' . wp_get_referer() . '">' . __('Back to safety', 'foobox-free'). '</a>' );
			}
		}
	}
}

Foobox_Free::get_instance();
