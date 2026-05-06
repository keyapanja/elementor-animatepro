<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once EAP_PATH . 'includes/class-eap-module-manager.php';
require_once EAP_PATH . 'includes/class-eap-assets.php';
require_once EAP_PATH . 'includes/class-eap-motion-extension.php';
require_once EAP_PATH . 'includes/class-eap-elementor-manager.php';
require_once EAP_PATH . 'includes/class-eap-settings-page.php';
require_once EAP_PATH . 'includes/class-eap-theme-builder.php';

class EAP_Plugin {

	/**
	 * Instance.
	 *
	 * @var EAP_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Module manager.
	 *
	 * @var EAP_Module_Manager
	 */
	private $modules;

	/**
	 * Get instance.
	 *
	 * @return EAP_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->modules = new EAP_Module_Manager();

		register_activation_hook( EAP_FILE, array( $this, 'activate' ) );

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'plugins_loaded', array( $this, 'boot' ) );
	}

	/**
	 * Activation hook.
	 *
	 * @return void
	 */
	public function activate() {
		$this->modules->maybe_seed_defaults();
	}

	/**
	 * Load translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'elementor-animatepro', false, dirname( plugin_basename( EAP_FILE ) ) . '/languages' );
	}

	/**
	 * Boot plugin services.
	 *
	 * @return void
	 */
	public function boot() {
		EAP_Assets::instance();
		new EAP_Settings_Page( $this->modules );
		new EAP_Theme_Builder();

		if ( did_action( 'elementor/loaded' ) ) {
			new EAP_Motion_Extension( $this->modules );
			new EAP_Elementor_Manager( $this->modules );
		} else {
			add_action( 'admin_notices', array( $this, 'render_missing_elementor_notice' ) );
		}
	}

	/**
	 * Missing Elementor notice.
	 *
	 * @return void
	 */
	public function render_missing_elementor_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		?>
		<div class="notice notice-warning">
			<p><?php echo esc_html__( 'Elementor AnimatePro requires Elementor to be installed and active.', 'elementor-animatepro' ); ?></p>
		</div>
		<?php
	}
}
