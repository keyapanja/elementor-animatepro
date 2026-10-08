<?php
/**
 * Replacement for the theme's footer.php.
 *
 * Loaded by EAP_TB_Render::override_footer() on the `get_footer` action. It
 * closes the document, because the theme's footer — the file that normally
 * does — never runs.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

EAP_TB_Render::the_part( 'footer' );

wp_footer();

?>
</body>
</html>
