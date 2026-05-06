<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_Module_Manager {

	const OPTION_KEY = 'eap_modules';

	/**
	 * Module definitions.
	 *
	 * @return array<string, array<string, string>>
	 */
	public function get_definitions() {
		return array(
			'widgets_animated_heading'  => array(
				'label' => __( 'Animated Heading', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Headline block with highlighted motion styling.', 'elementor-animatepro' ),
			),
			'widgets_typewriter'        => array(
				'label' => __( 'Typewriter Text', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Loop through words or phrases with a typewriter effect.', 'elementor-animatepro' ),
			),
			'widgets_icon_box'          => array(
				'label' => __( 'Icon Box', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Icon-led content cards with links and styling.', 'elementor-animatepro' ),
			),
			'widgets_image_box'         => array(
				'label' => __( 'Image Box', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Image-led content card with title and description.', 'elementor-animatepro' ),
			),
			'widgets_counter'           => array(
				'label' => __( 'Counter', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Animated number counter for metrics and stats.', 'elementor-animatepro' ),
			),
			'widgets_testimonial'       => array(
				'label' => __( 'Testimonial', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Minimal testimonial card with avatar and role.', 'elementor-animatepro' ),
			),
			'widgets_testimonial_slider' => array(
				'label' => __( 'Testimonial Slider', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Slider version of the testimonial layouts with full navigation and pagination controls.', 'elementor-animatepro' ),
			),
			'widgets_pricing_table'     => array(
				'label' => __( 'Pricing Table', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Pricing card with feature list and call to action.', 'elementor-animatepro' ),
			),
			'widgets_portfolio_grid'    => array(
				'label' => __( 'Portfolio Grid', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Filterable image grid for portfolio entries.', 'elementor-animatepro' ),
			),
			'widgets_brand_marquee'     => array(
				'label' => __( 'Brand Marquee', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Continuous horizontal brand/logo marquee.', 'elementor-animatepro' ),
			),
			'widgets_accordion'         => array(
				'label' => __( 'Advanced Accordion', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Accordion with WYSIWYG content panels.', 'elementor-animatepro' ),
			),
			'widgets_table_of_contents' => array(
				'label' => __( 'Table of Contents', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Auto-build a TOC from page headings.', 'elementor-animatepro' ),
			),
			'widgets_animated_text'     => array(
				'label' => __( 'Animated Text', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Split text by words, characters, or lines and animate from multiple directions.', 'elementor-animatepro' ),
			),
			'widgets_horizontal_text'   => array(
				'label' => __( 'Horizontal Text Scroll', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Long-form text that scrolls horizontally while the section is in view.', 'elementor-animatepro' ),
			),
			'widgets_horizontal_gallery'=> array(
				'label' => __( 'Horizontal Gallery', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'A scrolling image gallery driven by vertical scroll.', 'elementor-animatepro' ),
			),
			'widgets_animated_button'   => array(
				'label' => __( 'Animated Button', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Buttons that swap text on hover or rotate through multiple labels.', 'elementor-animatepro' ),
			),
			'widgets_text_mask'         => array(
				'label' => __( 'Text Mask Reveal', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Reveal text through directional masks with scroll or load triggers.', 'elementor-animatepro' ),
			),
			'widgets_cursor_preview_list' => array(
				'label' => __( 'Cursor Preview List', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'List items show a dedicated image preview next to the cursor on hover.', 'elementor-animatepro' ),
			),
			'widgets_logo'              => array(
				'label' => __( 'Site Logo', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Display the brand logo or site title with animation controls.', 'elementor-animatepro' ),
			),
			'widgets_menu'              => array(
				'label' => __( 'Navigation Menu', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Render a WordPress menu with directional item animations.', 'elementor-animatepro' ),
			),
			'widgets_offcanvas'         => array(
				'label' => __( 'Offcanvas Panel', 'elementor-animatepro' ),
				'type'  => 'widget',
				'desc'  => __( 'Toggle an animated offcanvas menu or content panel.', 'elementor-animatepro' ),
			),
			'extensions_motion'         => array(
				'label' => __( 'Motion Effects', 'elementor-animatepro' ),
				'type'  => 'extension',
				'desc'  => __( 'Entrance, hover, pin, transform, and cursor effects for Elementor elements.', 'elementor-animatepro' ),
			),
		);
	}

	/**
	 * Seed defaults.
	 *
	 * @return void
	 */
	public function maybe_seed_defaults() {
		if ( false !== get_option( self::OPTION_KEY, false ) ) {
			return;
		}

		$defaults = array_fill_keys( array_keys( $this->get_definitions() ), '1' );
		add_option( self::OPTION_KEY, $defaults, '', false );
	}

	/**
	 * Get all modules.
	 *
	 * @return array<string, string>
	 */
	public function get_enabled_modules() {
		$stored = get_option( self::OPTION_KEY, array() );
		$stored = is_array( $stored ) ? $stored : array();

		return wp_parse_args( $stored, array_fill_keys( array_keys( $this->get_definitions() ), '1' ) );
	}

	/**
	 * Check whether a module is enabled.
	 *
	 * @param string $key Module key.
	 * @return bool
	 */
	public function is_enabled( $key ) {
		$modules = $this->get_enabled_modules();

		return isset( $modules[ $key ] ) && '1' === (string) $modules[ $key ];
	}
}
