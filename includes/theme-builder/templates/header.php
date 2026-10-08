<?php
/**
 * Replacement for the theme's header.php.
 *
 * Loaded by EAP_TB_Render::override_header() on the `get_header` action, before
 * the theme's own header is swallowed. It has to open the document itself,
 * because the theme's header — the file that normally does — never runs.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eap_viewport = apply_filters( 'eap_tb_viewport_content', 'width=device-width, initial-scale=1' );

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="<?php echo esc_attr( $eap_viewport ); ?>" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php EAP_TB_Render::skip_link(); ?>
<?php EAP_TB_Render::the_part( 'header' ); ?>
