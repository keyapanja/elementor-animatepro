<?php
/**
 * Plugin Name: Elementor AnimatePro
 * Plugin URI: https://placeholder.example.com/
 * Description: Blank starter shell for rebuilding Elementor AnimatePro from scratch.
 * Version: 1.20.50
 * Author: KP
 * Text Domain: elementor-animatepro
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EAP_VERSION', '1.20.50' );
define( 'EAP_FILE', __FILE__ );
define( 'EAP_PATH', plugin_dir_path( __FILE__ ) );
define( 'EAP_URL', plugin_dir_url( __FILE__ ) );

require_once EAP_PATH . 'includes/class-eap-admin.php';
require_once EAP_PATH . 'includes/class-eap-assets.php';
require_once EAP_PATH . 'includes/class-eap-elementor.php';
require_once EAP_PATH . 'includes/class-eap-reactions.php';
require_once EAP_PATH . 'includes/class-eap-posts-query.php';
require_once EAP_PATH . 'includes/class-eap-posts-ajax.php';
require_once EAP_PATH . 'includes/class-eap-rating.php';
require_once EAP_PATH . 'includes/class-eap-read-later.php';
require_once EAP_PATH . 'includes/class-eap-video-story-cpt.php';

/**
 * Load translations only.
 *
 * @return void
 */
function EAP_bootstrap() {
	load_plugin_textdomain( 'elementor-animatepro', false, dirname( plugin_basename( EAP_FILE ) ) . '/languages' );

	new EAP_Admin();
	EAP_Assets::instance();
	new EAP_Elementor();
	new EAP_Reactions();
	new EAP_Posts_Ajax();
	new EAP_Rating();
	new EAP_Read_Later();
	new EAP_Video_Story_CPT();
}

add_action( 'plugins_loaded', 'EAP_bootstrap' );
