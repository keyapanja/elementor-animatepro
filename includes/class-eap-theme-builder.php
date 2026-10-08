<?php
/**
 * Theme Builder.
 *
 * Wires together the parts that let a site replace its theme's header and
 * footer with layouts built in Elementor:
 *
 *  - EAP_TB_Types       the catalogue of template types.
 *  - EAP_TB_Post_Type   where templates are stored.
 *  - EAP_TB_Conditions  where each template is allowed to appear.
 *  - EAP_TB_Resolver    which template wins on the current request.
 *  - EAP_TB_Render      how it reaches the page.
 *  - EAP_TB_Documents   how Elementor edits it.
 *  - EAP_TB_Admin       the Theme Builder screen.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once EAP_PATH . 'includes/theme-builder/class-eap-tb-types.php';
require_once EAP_PATH . 'includes/theme-builder/class-eap-tb-conditions.php';
require_once EAP_PATH . 'includes/theme-builder/class-eap-tb-post-type.php';
require_once EAP_PATH . 'includes/theme-builder/class-eap-tb-resolver.php';
require_once EAP_PATH . 'includes/theme-builder/class-eap-tb-preview.php';
require_once EAP_PATH . 'includes/theme-builder/class-eap-tb-transfer.php';
require_once EAP_PATH . 'includes/theme-builder/class-eap-tb-render.php';
require_once EAP_PATH . 'includes/theme-builder/class-eap-tb-popups.php';
require_once EAP_PATH . 'includes/theme-builder/class-eap-tb-documents.php';
require_once EAP_PATH . 'includes/theme-builder/class-eap-tb-admin.php';

class EAP_Theme_Builder {

	/**
	 * Boot every part.
	 */
	public function __construct() {
		new EAP_TB_Post_Type();
		new EAP_TB_Documents();
		new EAP_TB_Render();
		new EAP_TB_Popups();
		new EAP_TB_Preview();

		if ( is_admin() ) {
			$admin = new EAP_TB_Admin();
			$admin->init();
		}
	}
}
