<?php
/*
Plugin Name: FooBox Free Image Lightbox
Plugin URI: http://fooplugins.com/plugins/foobox/
Description: The best responsive image lightbox for WordPress.
Version: 1.0.8
Author: FooPlugins
Author URI: http://fooplugins.com
License: GPL2
Text Domain: foobox-image-lightbox
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
	define( 'FOOBOXFREE_VERSION', '1.0.8' );
	define( 'FOOBOXFREE_ACTIVATION_REDIRECT_TRANSIENT_KEY', '_fooboxfree_activation_redirect' );

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

			//register activation hook
			register_activation_hook( __FILE__, array( 'Foobox_Free', 'activate' ) );

			if (is_admin()) {

				add_action('admin_head', array($this, 'admin_inline_content'));
				add_action('foobox-free-settings_custom_type_render', array($this, 'custom_admin_settings_render'));
				add_action('foobox-free-settings-sidebar', array($this, 'settings_sidebar'));
				new FooBox_Free_Settings();
				add_action('admin_notices', array($this, 'admin_notice_upgrade'));
				add_action('admin_init', array($this, 'admin_notice_upgrade_ignore'));
				add_action('admin_init', array($this, 'deactivate_if_pro_activated'), 99);

				add_action( 'admin_notices', array( $this, 'admin_notice_foogallery_lightboxes' ) );
				add_action( 'wp_ajax_foobox_foogallery_lightboxes_ignore_notice', array( $this, 'admin_notice_foogallery_lightboxes_ignore' ) );
				add_action( 'wp_ajax_foobox_foogallery_lightboxes_update', array( $this, 'admin_notice_foogallery_lightboxes_update' ) );
				add_action( 'admin_print_scripts', array( $this, 'admin_notice_foogallery_lightboxes_inline_js' ), 999 );

				add_action( 'plugins_loaded',  array( $this, 'new_version_check' ) );
				add_action( 'admin_init', array( $this, 'check_for_redirect' ) );
				add_action( 'admin_menu', array( $this, 'register_menu_items' ) );

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
			} else if ($type == 'poweredby') {
				echo '<input readonly disabled type="checkbox" value="on" checked /><small>' . __('This cannot be turned off in the FREE version', 'foobox-image-lightbox') . '</small>';
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

		function admin_notice_upgrade() {
			if ( 'settings_page_fooboxfree-help' === foo_current_screen_id() )
				return;

			if ( current_user_can( 'activate_plugins' ) ) {
				if ( ! get_user_meta( get_current_user_id(), 'foogallery_did_you_know2' ) ) {
					$image_url = FOOBOXFREE_URL . 'img/';
					?>
					<style>
						.foobox-admin-notice-wrapper { margin-top: 10px; display: table; }
						.foobox-admin-notice-wrapper > div { vertical-align: middle; text-align: center; }
						.foobox-admin-notice {
							display: table-cell;
							position: relative;
							margin: 0;
							border: 5px solid #5A8F00;
							color: #333;
							background: #FFF;
							-webkit-border-radius: 10px;
							-moz-border-radius: 10px;
							border-radius: 10px;
							line-height: 0.8em;
							padding:5px;
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
						<?php printf( __('Thanks for using %s, get 35%% off the PRO version by using the coupon %s', 'foobox-image-lightbox'), '<strong>FooBox</strong>', '<strong><a target="_blank" href="http://fooplugins.com/plugins/foobox/?utm_source=fooboxfreeplugin&utm_medium=fooboxfreeprolink&utm_campaign=foobox_free_admin_notice">FOOBOXPRO35</a></strong>' ); ?>
						<br />

						<h3><?php _e('Would you like to create image and video galleries easier than ever before?', 'foobox-image-lightbox' ); ?></h3>

						<?php printf( __('Try our free %s plugin and our premium %s extension, which both work beautifully with FooBox!', 'foobox-image-lightbox' ),
							'<strong><a target="_blank" href="http://foo.gallery?utm_source=fooboxfreeplugin&utm_medium=fooboxfreeprolink&utm_campaign=foobox_free_admin_notice">FooGallery</a></strong>',
							'<strong><a target="_blank" href="http://fooplugins.com/plugins/foovideo?utm_source=fooboxfreeplugin&utm_medium=fooboxfreefoovideolink&utm_campaign=foobox_free_admin_notice">FooVideo</a></strong>'); ?>
						<a class="foobox-admin-notice-close" title="<?php _e('Hide this notice', 'foobox-image-lightbox'); ?>" href="<?php echo esc_url( add_query_arg( 'foogallery_did_you_know_ignore', '0' ) ); ?>">&times;</a></div>
					</div><?php
				}
			}
		}

		function admin_notice_upgrade_ignore() {
			if ( current_user_can( 'activate_plugins' ) ) {
				/* If user clicks to dismiss the notice, add that to their user meta */
				if ( isset( $_GET['foogallery_did_you_know_ignore'] ) && '0' == $_GET['foogallery_did_you_know_ignore'] ) {
					add_user_meta( get_current_user_id(), 'foogallery_did_you_know2', 'true', true );
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
		}

		function deactivate_if_pro_activated() {
			if (class_exists('fooboxV2')) {
				deactivate_plugins( plugin_basename(__FILE__) ); // Deactivate me - FooBox PRO is running!
				wp_die( __('FooBox FREE was deactivated, as FooBox PRO is now running!', 'foobox-image-lightbox') . ' <a href="' . wp_get_referer() . '">' . __('Back to safety', 'foobox-image-lightbox'). '</a>' );
			}
		}

		function admin_notice_foogallery_lightboxes() {
			if ( ! current_user_can( 'activate_plugins' ) || ! class_exists( 'FooGallery_Plugin' ) )
				return;

			if ( !get_user_meta( get_current_user_id(), 'foogallery_fooboxfree_lightbox_ignore' ) ) {
				$galleries = foogallery_get_all_galleries();
				$gallery_count = 0;
				foreach ( $galleries as $gallery ) {
					$template = $gallery->gallery_template;
					$lightbox = $gallery->get_meta( "{$template}_lightbox", 'none' );
					if ( strpos( $lightbox, 'foobox-free' ) === false ) {
						$gallery_count++;
					}
				}

				if ( $gallery_count > 0 ) {
					?>
					<style>
						.foobox-foogallery-lightboxes .spinner {
							float: none;
							margin: 0 10px;;
						}


					</style>
					<div class="foobox-foogallery-lightboxes notice error is-dismissible">
						<p>
							<strong><?php _e( 'FooBox + FooGallery Alert : ', 'foobox-image-lightbox' ); ?></strong>
							<?php echo sprintf( _n( 'We noticed that you have 1 FooGallery that is NOT using FooBox!', 'We noticed that you have %s FooGalleries that are NOT using FooBox!', $gallery_count, 'foobox-image-lightbox' ), $gallery_count ); ?>

							<a class="foobox-foogallery-update-lightbox"
							   href="#update_galleries"><?php echo _n( 'Update it to use FooBox now!', 'Update them to use FooBox now!', $gallery_count, 'foobox-image-lightbox' ); ?></a>
							<span class="spinner"></span>
						</p>
					</div>
					<?php
				}
			}
		}

		function admin_notice_foogallery_lightboxes_ignore() {
			if ( check_admin_referer( 'foobox_foogallery_lightboxes_ignore_notice' ) ) {
				add_user_meta( get_current_user_id(), 'foogallery_fooboxfree_lightbox_ignore', 'true', true );
			}
		}

		function admin_notice_foogallery_lightboxes_update() {
			if ( check_admin_referer( 'foobox_foogallery_lightboxes_update', 'foobox_foogallery_lightboxes_update_nonce' ) ) {
				//update all galleries to use foobox!
				$galleries = foogallery_get_all_galleries();
				$gallery_update_count = 0;
				foreach ( $galleries as $gallery ) {
					$template = $gallery->gallery_template;
					$meta_key = "{$template}_lightbox";
					$lightbox = $gallery->get_meta( $meta_key, 'none' );
					if ( strpos( $lightbox, 'foobox' ) === false ) {
						$gallery->settings[$meta_key] = 'foobox-free';
						update_post_meta( $gallery->ID, FOOGALLERY_META_SETTINGS, $gallery->settings );
						$gallery_update_count++;
					}
				}

				//return JSON here
				$json_array = array(
					'success' => true,
					'updated' => sprintf( _n( '1 FooGallery successfully updated to use FooBox!', '%s FooGalleries successfully updated to use FooBox!', $gallery_update_count, 'foobox-image-lightbox' ), $gallery_update_count )
				);

				header('Content-type: application/json');
				echo json_encode($json_array);
				die;
			}
		}

		public function admin_notice_foogallery_lightboxes_inline_js() {
			if ( ! current_user_can( 'activate_plugins' ) || ! class_exists( 'FooGallery_Plugin' ) )
				return;

			if ( get_user_meta( get_current_user_id(), 'foogallery_fooboxfree_lightbox_ignore' ) )
				return;

			if ( 'settings_page_fooboxfree-help' === foo_current_screen_id() )
				return;

			?>
			<script type="text/javascript">
				( function ( $ ) {
					$( document ).ready( function () {
						$( '.foobox-foogallery-lightboxes.is-dismissible' )
							.on( 'click', '.notice-dismiss', function ( e ) {
								e.preventDefault();
								$.post( ajaxurl, {
									action: 'foobox_foogallery_lightboxes_ignore_notice',
									url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
									_wpnonce: '<?php echo wp_create_nonce( 'foobox_foogallery_lightboxes_ignore_notice' ); ?>'
								} );
							} )

							.on( 'click', '.foobox-foogallery-update-lightbox', function ( e ) {
								e.preventDefault();
								var $spinner = $(this).parents('div:first').find('.spinner');
								$spinner.addClass('is-active');

								var data = 'action=foobox_foogallery_lightboxes_update' +
									'&foobox_foogallery_lightboxes_update_nonce=<?php echo wp_create_nonce( 'foobox_foogallery_lightboxes_update' ); ?>' +
									'&_wp_http_referer=' + encodeURIComponent($('input[name="_wp_http_referer"]').val());

								$.ajax({
									type: "POST",
									url: ajaxurl,
									data: data,
									success: function(data) {
										$('.foobox-foogallery-lightboxes').slideUp();
										alert(data.updated);
										$spinner.removeClass('is-active');
									}
								});
							} );
						} );
				} )( jQuery );
			</script>
			<?php
		}

		/**
		 * Check if a new version is now running and do some stuff!
		 */
		function new_version_check() {
			if ( get_site_option( 'foobox-free-version' ) != FOOBOXFREE_VERSION ) {
				//This code will run every time the plugin is updated

				//clear some user meta, so our notices are shown again
				delete_user_meta( get_current_user_id(), 'foogallery_fooboxfree_lightbox_ignore' );
				delete_user_meta( get_current_user_id(), 'foogallery_did_you_know2' );
				delete_user_meta( get_current_user_id(), 'foogallery_did_you_know' );

				//set the current version, so that this does not run again until the next update!
				update_site_option( 'foobox-free-version', FOOBOXFREE_VERSION );
			}
		}

		/**
		 * Fired when the plugin is activated.
		 *
		 * @since    1.0.7
		 *
		 * @param    boolean    $network_wide    True if WPMU superadmin uses
		 *                                       "Network Activate" action, false if
		 *                                       WPMU is disabled or plugin is
		 *                                       activated on an individual blog.
		 */
		public static function activate( $network_wide ) {
			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				//do something for multisite!
			} else {
				self::single_activate( false );
			}
		}

		/**
		 * Fired for each blog when the plugin is activated.
		 *
		 * @since    1.0.0
		 */
		private static function single_activate( $multisite = true ) {
			if ( false === $multisite ) {
				//Make sure we redirect to the welcome page
				set_transient( FOOBOXFREE_ACTIVATION_REDIRECT_TRANSIENT_KEY, true, 30 );
			}
		}

		function check_for_redirect() {
			// Bail if no activation redirect
			if ( ! get_transient( FOOBOXFREE_ACTIVATION_REDIRECT_TRANSIENT_KEY ) ) {
				return;
			}

			// Delete the redirect transient
			delete_transient( FOOBOXFREE_ACTIVATION_REDIRECT_TRANSIENT_KEY );

			// Bail if activating from network, or bulk
			if ( is_network_admin() || isset($_GET['activate-multi']) ) {
				return;
			}

			$url = admin_url( 'options-general.php?page=fooboxfree-help' );

			wp_safe_redirect( $url );
			exit;
		}

		function register_menu_items() {
			add_submenu_page( null, __( 'Welcome to FooBox Free', 'foobox-image-lightbox' ), __( 'FooBox Free Help', 'foobox-image-lightbox' ), 'manage_options', 'fooboxfree-help', array( $this, 'render_page_fooboxfree_help' ) );
		}

		function render_page_fooboxfree_help() {
			require_once FOOBOXFREE_PATH . 'includes/view-help.php';
		}
	}
}

Foobox_Free::get_instance();
