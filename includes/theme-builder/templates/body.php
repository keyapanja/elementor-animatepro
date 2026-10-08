<?php
/**
 * Page template for a Single or Archive template.
 *
 * Loaded through `template_include` in place of the theme's single.php or
 * archive.php. It still calls get_header() and get_footer(), so the theme's
 * own header and footer run unless a Theme Builder header or footer has also
 * matched — in which case those replace them in the usual way.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

EAP_TB_Render::the_body();

get_footer();
