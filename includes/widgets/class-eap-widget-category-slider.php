<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

/**
 * Category Slider — a Swiper carousel of TAXONOMY TERMS (not posts): each slide
 * is a category card (image + name + post count) linking to the term archive.
 *
 * Because WordPress core terms have no image field, the term image is resolved
 * in this order by get_term_image():
 *   1. a term-meta key the user names (accepts an attachment ID or a URL — this
 *      is what ACF / theme "category image" fields write);
 *   2. the featured image of the most recent post in that term (zero-setup, so
 *      the widget looks right out of the box) — toggleable, since it costs one
 *      small query per term;
 *   3. a widget-wide Fallback Image;
 *   4. a CSS gradient placeholder.
 *
 * Follows the plugin's slider convention: bundled Swiper only when registered,
 * `.eap-category-slider__swiper.swiper` markup, JSON config on
 * `data-eap-category-slider`.
 */
class EAP_Widget_Category_Slider extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-category-slider';
	}

	public function get_title() {
		return __( 'Category Slider', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-folder';
	}

	public function get_keywords() {
		return array( 'category', 'categories', 'taxonomy', 'terms', 'slider', 'carousel', 'dynamic' );
	}

	public function get_style_depends() {
		return $this->eap_with_swiper_style( array( 'eap-core', 'eap-category-slider' ) );
	}

	public function get_script_depends() {
		$deps = array(
			'eap-core-runtime',
			'eap-category-slider-script',
		);
		if ( wp_script_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}

		return array_unique( $deps );
	}

	protected function register_controls() {
		$this->register_query_section();
		$this->register_layout_section();
		$this->register_slider_section();

		$this->register_card_style();
		$this->register_image_style();
		$this->register_name_style();
		$this->register_count_style();
		$this->register_arrows_style();
		$this->register_pagination_style();
	}

	/* =====================================================================
	 * CONTENT — QUERY
	 * ================================================================== */

	protected function register_query_section() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Query', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'taxonomy',
			array(
				'label'   => __( 'Taxonomy', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => $this->get_taxonomy_options(),
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'   => __( 'Number of Categories', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 40,
				'default' => 10,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => __( 'Order By', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => array(
					'name'    => __( 'Name', 'elementor-animatepro' ),
					'count'   => __( 'Post Count', 'elementor-animatepro' ),
					'slug'    => __( 'Slug', 'elementor-animatepro' ),
					'term_id' => __( 'ID', 'elementor-animatepro' ),
					'include' => __( 'Selected Order', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => __( 'Order', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => array(
					'ASC'  => __( 'Ascending', 'elementor-animatepro' ),
					'DESC' => __( 'Descending', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'hide_empty',
			array(
				'label'        => __( 'Only Categories With Posts', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'include_terms',
			array(
				'label'       => __( 'Only These Categories', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => EAP_Posts_Query::get_term_options(),
				'description' => __( 'Leave empty for all. Pair with Order By → Selected Order to keep this order.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'exclude_terms',
			array(
				'label'       => __( 'Exclude Categories', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => EAP_Posts_Query::get_term_options(),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — LAYOUT
	 * ================================================================== */

	protected function register_layout_section() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'overlay',
				'options' => array(
					'overlay' => __( 'Overlay (text on image)', 'elementor-animatepro' ),
					'card'    => __( 'Card (text below image)', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'image_meta_key',
			array(
				'label'       => __( 'Image Field (term meta key)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'e.g. category_image',
				'description' => __( 'Term-meta key holding an attachment ID or image URL (ACF / theme category-image fields).', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'auto_image',
			array(
				'label'        => __( 'Use Latest Post Image', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'When a category has no image of its own, use the featured image of its newest post.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'fallback_image',
			array(
				'label'       => __( 'Fallback Image', 'elementor-animatepro' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Used when a category still has no image.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'   => __( 'Image Size', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'medium_large',
				'options' => $this->get_image_size_options(),
			)
		);

		$this->add_control(
			'show_count',
			array(
				'label'        => __( 'Post Count', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'count_suffix',
			array(
				'label'     => __( 'Count Suffix', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Posts', 'elementor-animatepro' ),
				'condition' => array( 'show_count' => 'yes' ),
			)
		);

		$this->add_control(
			'name_tag',
			array(
				'label'   => __( 'Name Tag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — SLIDER
	 * ================================================================== */

	protected function register_slider_section() {
		$this->start_controls_section(
			'section_slider',
			array(
				'label' => __( 'Slider', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'slides_desktop',
			array(
				'label'   => __( 'Slides (Desktop)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 8,
				'default' => 4,
			)
		);

		$this->add_control(
			'slides_tablet',
			array(
				'label'   => __( 'Slides (Tablet)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 8,
				'default' => 3,
			)
		);

		$this->add_control(
			'slides_mobile',
			array(
				'label'   => __( 'Slides (Mobile)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 4,
				'default' => 2,
			)
		);

		$this->add_control(
			'space_between',
			array(
				'label'      => __( 'Space Between', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 20, 'unit' => 'px' ),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => __( 'Transition Speed (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 100,
				'max'     => 3000,
				'step'    => 50,
				'default' => 600,
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => __( 'Loop', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'centered_slides',
			array(
				'label'        => __( 'Centered Slides', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => __( 'Autoplay Delay (s)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 20,
				'step'      => 0.5,
				'default'   => 4,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => __( 'Pause on Hover', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'show_arrows',
			array(
				'label'        => __( 'Arrows', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'show_pagination',
			array(
				'label'        => __( 'Pagination', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'     => __( 'Pagination Type', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bullets',
				'options'   => array(
					'bullets'     => __( 'Bullets', 'elementor-animatepro' ),
					'fraction'    => __( 'Fraction', 'elementor-animatepro' ),
					'progressbar' => __( 'Progress Bar', 'elementor-animatepro' ),
				),
				'condition' => array( 'show_pagination' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_card_style() {
		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => __( 'Card', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-category-slider__item' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'layout' => 'card' ),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Content Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16, 'unit' => 'px', 'isLinked' => true ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-category-slider__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .eap-category-slider__item',
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-category-slider__item' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eap-category-slider__item',
			)
		);

		$this->add_responsive_control(
			'card_hover_lift',
			array(
				'label'      => __( 'Hover Lift', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 6, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-category-slider__item:hover' => 'transform: translateY(-{{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_image_style() {
		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => __( 'Image', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array( 'px' => array( 'min' => 80, 'max' => 600 ) ),
				'default'    => array( 'size' => 220, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-category-slider__image' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-category-slider--overlay .eap-category-slider__item' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'image_hover_zoom',
			array(
				'label'        => __( 'Hover Zoom', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'scrim_color',
			array(
				'label'     => __( 'Overlay Gradient', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.6)',
				'selectors' => array(
					'{{WRAPPER}} .eap-category-slider__item' => '--eap-cs-scrim: {{VALUE}};',
				),
				'condition' => array( 'layout' => 'overlay' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_name_style() {
		$this->start_controls_section(
			'section_name_style',
			array(
				'label' => __( 'Name', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		// No default: the overlay layout needs light text from CSS, and a
		// hardcoded default would emit {{WRAPPER}}-scoped CSS that beats it.
		$this->add_control(
			'name_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-category-slider__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'name_color_hover',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-category-slider__item:hover .eap-category-slider__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .eap-category-slider__name',
			)
		);

		$this->end_controls_section();
	}

	protected function register_count_style() {
		$this->start_controls_section(
			'section_count_style',
			array(
				'label'     => __( 'Post Count', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_count' => 'yes' ),
			)
		);

		$this->add_control(
			'count_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-category-slider__count' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'count_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-category-slider__count' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'count_typography',
				'selector' => '{{WRAPPER}} .eap-category-slider__count',
			)
		);

		$this->end_controls_section();
	}

	protected function register_arrows_style() {
		$this->start_controls_section(
			'section_arrows_style',
			array(
				'label'     => __( 'Arrows', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_arrows' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'arrow_box',
			array(
				'label'      => __( 'Button Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 80 ) ),
				'default'    => array( 'size' => 44, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-category-slider__arrow' => '--eap-cs-arrow: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_offset',
			array(
				'label'      => __( 'Horizontal Offset', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => -60, 'max' => 40 ) ),
				'default'    => array( 'size' => -10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-category-slider__arrow' => '--eap-cs-arrow-offset: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'cs_arrow_tabs' );

		$this->start_controls_tab( 'cs_arrow_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'arrow_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-category-slider__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-category-slider__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'cs_arrow_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'arrow_color_hover',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-category-slider__arrow:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-category-slider__arrow:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_pagination_style() {
		$this->start_controls_section(
			'section_pagination_style',
			array(
				'label'     => __( 'Pagination', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_pagination' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'pagination_spacing',
			array(
				'label'      => __( 'Spacing Above', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 26, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-category-slider__pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bullet_size',
			array(
				'label'      => __( 'Bullet Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 4, 'max' => 24 ) ),
				'default'    => array( 'size' => 9, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-category-slider__pagination .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bullet_color',
			array(
				'label'     => __( 'Bullet Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d1d5db',
				'selectors' => array(
					'{{WRAPPER}} .eap-category-slider__pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}}; opacity: 1;',
				),
			)
		);

		$this->add_control(
			'bullet_color_active',
			array(
				'label'     => __( 'Active Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-category-slider__pagination .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eap-category-slider__pagination .swiper-pagination-progressbar-fill' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$editor   = $this->eap_is_editor();

		$taxonomy = ! empty( $settings['taxonomy'] ) ? $settings['taxonomy'] : 'category';
		if ( ! taxonomy_exists( $taxonomy ) ) {
			$taxonomy = 'category';
		}

		$term_args = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => 'yes' === ( $settings['hide_empty'] ?? 'yes' ),
			'orderby'    => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'name',
			'order'      => ( 'DESC' === ( $settings['order'] ?? 'ASC' ) ) ? 'DESC' : 'ASC',
			'number'     => isset( $settings['limit'] ) ? max( 1, (int) $settings['limit'] ) : 10,
		);

		$include = $this->int_list( $settings['include_terms'] ?? array() );
		$exclude = $this->int_list( $settings['exclude_terms'] ?? array() );
		if ( $include ) {
			$term_args['include'] = $include;
		}
		if ( $exclude ) {
			$term_args['exclude'] = $exclude;
		}

		$terms = get_terms( $term_args );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-category-slider eap-category-slider--empty">' . esc_html__( 'No categories found for this query.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$layout          = ( 'card' === ( $settings['layout'] ?? 'overlay' ) ) ? 'card' : 'overlay';
		$show_arrows     = 'yes' === ( $settings['show_arrows'] ?? 'yes' );
		$show_pagination = 'yes' === ( $settings['show_pagination'] ?? '' );
		$show_count      = 'yes' === ( $settings['show_count'] ?? 'yes' );
		$count_suffix    = isset( $settings['count_suffix'] ) ? $settings['count_suffix'] : __( 'Posts', 'elementor-animatepro' );
		$name_tag        = $this->safe_tag( $settings['name_tag'] ?? 'h3' );

		$img_cfg = array(
			'meta_key'   => isset( $settings['image_meta_key'] ) ? trim( (string) $settings['image_meta_key'] ) : '',
			'auto'       => 'yes' === ( $settings['auto_image'] ?? 'yes' ),
			'fallback'   => isset( $settings['fallback_image']['url'] ) ? $settings['fallback_image']['url'] : '',
			'size'       => ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'medium_large',
			'taxonomy'   => $taxonomy,
		);

		$slider_data = array(
			'slidesDesktop'  => isset( $settings['slides_desktop'] ) ? max( 1, (int) $settings['slides_desktop'] ) : 4,
			'slidesTablet'   => isset( $settings['slides_tablet'] ) ? max( 1, (int) $settings['slides_tablet'] ) : 3,
			'slidesMobile'   => isset( $settings['slides_mobile'] ) ? max( 1, (int) $settings['slides_mobile'] ) : 2,
			'spaceBetween'   => isset( $settings['space_between']['size'] ) ? (int) $settings['space_between']['size'] : 20,
			'speed'          => isset( $settings['speed'] ) ? max( 100, (int) $settings['speed'] ) : 600,
			'loop'           => 'yes' === ( $settings['loop'] ?? 'yes' ),
			'centered'       => 'yes' === ( $settings['centered_slides'] ?? '' ),
			'autoplay'       => 'yes' === ( $settings['autoplay'] ?? '' ),
			'autoplayDelay'  => isset( $settings['autoplay_delay'] ) ? (float) $settings['autoplay_delay'] : 4,
			'pauseOnHover'   => 'yes' === ( $settings['pause_on_hover'] ?? 'yes' ),
			'navigation'     => $show_arrows,
			'pagination'     => $show_pagination,
			'paginationType' => ! empty( $settings['pagination_type'] ) ? $settings['pagination_type'] : 'bullets',
		);

		$classes = array(
			'eap-widget',
			'eap-category-slider',
			'eap-category-slider--' . $layout,
		);
		if ( 'yes' === ( $settings['image_hover_zoom'] ?? 'yes' ) ) {
			$classes[] = 'eap-category-slider--zoom';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="eap-category-slider__swiper swiper" data-eap-category-slider="<?php echo esc_attr( wp_json_encode( $slider_data ) ); ?>">
				<div class="swiper-wrapper">
					<?php
					foreach ( $terms as $term ) :
						$link  = get_term_link( $term );
						$link  = is_wp_error( $link ) ? '#' : $link;
						$image = $this->get_term_image( $term, $img_cfg );
						?>
						<div class="swiper-slide eap-category-slider__slide">
							<a class="eap-category-slider__item" href="<?php echo esc_url( $link ); ?>">
								<span class="eap-category-slider__image">
									<?php if ( $image ) : ?>
										<img class="eap-category-slider__img" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" loading="lazy" />
									<?php else : ?>
										<span class="eap-category-slider__img eap-category-slider__img--ph" aria-hidden="true"></span>
									<?php endif; ?>
								</span>
								<span class="eap-category-slider__content">
									<<?php echo esc_html( $name_tag ); ?> class="eap-category-slider__name"><?php echo esc_html( $term->name ); ?></<?php echo esc_html( $name_tag ); ?>>
									<?php if ( $show_count ) : ?>
										<span class="eap-category-slider__count"><?php echo esc_html( number_format_i18n( (int) $term->count ) . ( '' !== trim( (string) $count_suffix ) ? ' ' . $count_suffix : '' ) ); ?></span>
									<?php endif; ?>
								</span>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( $show_arrows ) : ?>
				<button type="button" class="eap-category-slider__arrow eap-category-slider__arrow--prev" aria-label="<?php echo esc_attr__( 'Previous', 'elementor-animatepro' ); ?>">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
				<button type="button" class="eap-category-slider__arrow eap-category-slider__arrow--next" aria-label="<?php echo esc_attr__( 'Next', 'elementor-animatepro' ); ?>">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
			<?php endif; ?>

			<?php if ( $show_pagination ) : ?>
				<div class="eap-category-slider__pagination"></div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Resolve a term's image: named term-meta (ID or URL) -> newest post's
	 * featured image in that term -> widget fallback -> '' (CSS placeholder).
	 *
	 * @param WP_Term $term Term.
	 * @param array   $cfg  Image config.
	 * @return string Image URL, or '' when there is none.
	 */
	protected function get_term_image( $term, $cfg ) {
		// 1. A named term-meta field (ACF / theme category-image fields).
		if ( '' !== $cfg['meta_key'] ) {
			$value = get_term_meta( $term->term_id, $cfg['meta_key'], true );

			if ( is_array( $value ) && isset( $value['url'] ) ) {
				$value = $value['url'];
			}

			if ( is_numeric( $value ) ) {
				$url = wp_get_attachment_image_url( (int) $value, $cfg['size'] );
				if ( $url ) {
					return $url;
				}
			} elseif ( is_string( $value ) && '' !== trim( $value ) ) {
				return trim( $value );
			}
		}

		// 2. The newest post in this term that actually has a featured image.
		if ( $cfg['auto'] ) {
			$posts = get_posts(
				array(
					'post_type'      => 'any',
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'orderby'        => 'date',
					'order'          => 'DESC',
					'no_found_rows'  => true,
					'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						array(
							'taxonomy' => $cfg['taxonomy'],
							'field'    => 'term_id',
							'terms'    => array( (int) $term->term_id ),
						),
					),
					'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
						array(
							'key'     => '_thumbnail_id',
							'compare' => 'EXISTS',
						),
					),
				)
			);

			if ( ! empty( $posts ) ) {
				$url = get_the_post_thumbnail_url( (int) $posts[0], $cfg['size'] );
				if ( $url ) {
					return $url;
				}
			}
		}

		// 3. The widget-wide fallback.
		return $cfg['fallback'];
	}

	/**
	 * Coerce a control value to a list of positive ints.
	 *
	 * @param mixed $value Value.
	 * @return int[]
	 */
	protected function int_list( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$out = array();
		foreach ( $value as $item ) {
			$int = (int) $item;
			if ( $int > 0 ) {
				$out[] = $int;
			}
		}

		return array_values( array_unique( $out ) );
	}

	/**
	 * Whitelist the name tag.
	 *
	 * @param string $tag Tag.
	 * @return string
	 */
	protected function safe_tag( $tag ) {
		return in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span' ), true ) ? $tag : 'h3';
	}

	/**
	 * Public taxonomies for the taxonomy select.
	 *
	 * @return array<string, string>
	 */
	protected function get_taxonomy_options() {
		$options = array();
		foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $tax ) {
			if ( in_array( $tax->name, array( 'post_format' ), true ) ) {
				continue;
			}
			$options[ $tax->name ] = $tax->labels->singular_name;
		}
		return $options;
	}

	/**
	 * Registered image sizes for the size select.
	 *
	 * @return array<string, string>
	 */
	protected function get_image_size_options() {
		$options = array();
		foreach ( get_intermediate_image_sizes() as $size ) {
			$options[ $size ] = ucwords( str_replace( array( '_', '-' ), ' ', $size ) );
		}
		$options['full'] = __( 'Full', 'elementor-animatepro' );
		return $options;
	}
}
