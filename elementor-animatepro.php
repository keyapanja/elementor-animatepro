<?php
/**
 * Plugin Name: Elementor AnimatePro
 * Plugin URI: https://placeholder.example.com/
 * Description: Blank starter shell for rebuilding Elementor AnimatePro from scratch.
 * Version: 1.20.76
 * Author: KP
 * Text Domain: elementor-animatepro
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EAP_VERSION', '1.20.76' );
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
require_once EAP_PATH . 'includes/class-eap-portfolio-cpt.php';
require_once EAP_PATH . 'includes/class-eap-image-masking.php';
require_once EAP_PATH . 'includes/class-eap-advanced-tooltip.php';
require_once EAP_PATH . 'includes/class-eap-conditional-display.php';
require_once EAP_PATH . 'includes/class-eap-interactive-animations.php';
require_once EAP_PATH . 'includes/class-eap-hover-interaction.php';
require_once EAP_PATH . 'includes/class-eap-custom-cursor.php';

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
	new EAP_Portfolio_CPT();
	new EAP_Image_Masking();
	new EAP_Advanced_Tooltip();
	new EAP_Conditional_Display();
	new EAP_Interactive_Animations();
	new EAP_Hover_Interaction();
	new EAP_Custom_Cursor();
}

add_action( 'plugins_loaded', 'EAP_bootstrap' );
