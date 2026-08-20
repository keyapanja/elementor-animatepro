<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

/**
 * Posts Timeline — the Posts loop arranged down a vertical timeline: a line
 * with dated nodes and year dividers, in an Alternating (centered, zig-zag) or
 * One-sided (left rail) arrangement. Built on the shared EAP_Posts_Query engine
 * (same query builder + card markup + styling) so every card matches the Posts
 * widget; the reused `eap-posts` stylesheet provides the card base and
 * `eap-posts-timeline` adds the line / dots / date pills / year bands. Latest
 * query only, no pagination (shows the latest N). CSS only — no JS.
 */
class EAP_Widget_Posts_Timeline extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-posts-timeline';
	}

	public function get_title() {
		return __( 'Posts Timeline', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	public function get_keywords() {
		return array( 'posts', 'timeline', 'chronological', 'date', 'blog', 'feed', 'dynamic' );
	}

	public function get_style_depends() {
		return array( 'eap-core', 'eap-posts', 'eap-posts-timeline' );
	}

	protected function register_controls() {
		$this->register_query_section();
		$this->register_timeline_section();
		$this->register_card_section();

		$this->register_timeline_style();
		$this->register_card_style();
		$this->register_image_style();
		$this->register_title_style();
		$this->register_meta_style();
		$this->register_excerpt_style();
		$this->register_button_style();
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
			'post_type',
			array(
				'label'   => __( 'Post Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => EAP_Posts_Query::get_post_type_options(),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => __( 'Number of Posts', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 48,
				'default' => 8,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => __( 'Order By', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'          => __( 'Date', 'elementor-animatepro' ),
					'modified'      => __( 'Last Modified', 'elementor-animatepro' ),
					'title'         => __( 'Title', 'elementor-animatepro' ),
					'menu_order'    => __( 'Menu Order', 'elementor-animatepro' ),
					'comment_count' => __( 'Comment Count', 'elementor-animatepro' ),
					'rand'          => __( 'Random', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => __( 'Order', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => __( 'Newest First', 'elementor-animatepro' ),
					'ASC'  => __( 'Oldest First', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'   => __( 'Offset', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'default' => 0,
			)
		);

		$this->add_control(
			'include_terms',
			array(
				'label'       => __( 'Include Terms', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => EAP_Posts_Query::get_term_options(),
			)
		);

		$this->add_control(
			'exclude_terms',
			array(
				'label'       => __( 'Exclude Terms', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => EAP_Posts_Query::get_term_options(),
			)
		);

		$this->add_control(
			'exclude_current',
			array(
				'label'        => __( 'Exclude Current Post', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'ignore_sticky',
			array(
				'label'        => __( 'Ignore Sticky Posts', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — TIMELINE
	 * ================================================================== */

	protected function register_timeline_section() {
		$this->start_controls_section(
			'section_timeline',
			array(
				'label' => __( 'Timeline', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'arrangement',
			array(
				'label'   => __( 'Arrangement', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'alternating',
				'options' => array(
					'alternating' => __( 'Alternating (centered)', 'elementor-animatepro' ),
					'one-sided'   => __( 'One-sided (left)', 'elementor-animatepro' ),
				),
				'description' => __( 'Alternating collapses to one-sided on mobile.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_date',
			array(
				'label'        => __( 'Date Pill', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'date_format',
			array(
				'label'       => __( 'Date Format', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Site default', 'elementor-animatepro' ),
				'description' => __( 'PHP date format (e.g. M j, Y). Leave empty for the site default.', 'elementor-animatepro' ),
				'condition'   => array( 'show_date' => 'yes' ),
			)
		);

		$this->add_control(
			'show_year',
			array(
				'label'        => __( 'Year Dividers', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — CARD
	 * ================================================================== */

	protected function register_card_section() {
		$this->start_controls_section(
			'section_card',
			array(
				'label' => __( 'Card', 'elementor-animatepro' ),
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
			'title_tag',
			array(
				'label'   => __( 'Title Tag', 'elementor-animatepro' ),
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
					'p'    => 'p',
				),
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label'   => __( 'Excerpt Length (words)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 100,
				'default' => 20,
			)
		);

		$this->add_control(
			'meta_data',
			array(
				'label'       => __( 'Meta', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'default'     => array( 'author', 'comments' ),
				'options'     => array(
					'author'   => __( 'Author', 'elementor-animatepro' ),
					'date'     => __( 'Date', 'elementor-animatepro' ),
					'comments' => __( 'Comments', 'elementor-animatepro' ),
				),
				'description' => __( 'The post date already appears on the line; add it here only if you want it in the card too.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'readmore_text',
			array(
				'label'   => __( 'Read More Text', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Read More', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'elements_heading',
			array(
				'label'     => __( 'Elements', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		foreach ( array(
			'show_image'    => __( 'Image', 'elementor-animatepro' ),
			'show_badge'    => __( 'Category Badge', 'elementor-animatepro' ),
			'show_title'    => __( 'Title', 'elementor-animatepro' ),
			'show_meta'     => __( 'Meta', 'elementor-animatepro' ),
			'show_excerpt'  => __( 'Excerpt', 'elementor-animatepro' ),
			'show_readmore' => __( 'Read More', 'elementor-animatepro' ),
		) as $key => $label ) {
			$this->add_control(
				$key,
				array(
					'label'        => $label,
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'elementor-animatepro' ),
					'label_off'    => __( 'Hide', 'elementor-animatepro' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
		}

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE — TIMELINE
	 * ================================================================== */

	protected function register_timeline_style() {
		$this->start_controls_section(
			'section_timeline_style',
			array(
				'label' => __( 'Timeline', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'tl_gap',
			array(
				'label'      => __( 'Vertical Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 120 ) ),
				'default'    => array( 'size' => 36, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts-timeline' => '--eap-tl-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'tl_line_heading',
			array(
				'label' => __( 'Line & Dot', 'elementor-animatepro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'tl_line_color',
			array(
				'label'     => __( 'Line Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e5e7eb',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts-timeline' => '--eap-tl-line: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tl_line_width',
			array(
				'label'      => __( 'Line Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 1, 'max' => 10 ) ),
				'default'    => array( 'size' => 2, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts-timeline' => '--eap-tl-line-w: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'tl_dot_color',
			array(
				'label'     => __( 'Dot Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts-timeline' => '--eap-tl-dot: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tl_dot_size',
			array(
				'label'      => __( 'Dot Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 6, 'max' => 40 ) ),
				'default'    => array( 'size' => 16, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts-timeline' => '--eap-tl-dot-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'tl_date_heading',
			array(
				'label'     => __( 'Date Pill', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'show_date' => 'yes' ),
			)
		);

		$this->add_control(
			'tl_date_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts-timeline__date' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_date' => 'yes' ),
			)
		);

		$this->add_control(
			'tl_date_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eef2ff',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts-timeline__date' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'show_date' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'tl_date_typography',
				'selector'  => '{{WRAPPER}} .eap-posts-timeline__date',
				'condition' => array( 'show_date' => 'yes' ),
			)
		);

		$this->add_control(
			'tl_year_heading',
			array(
				'label'     => __( 'Year Divider', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'show_year' => 'yes' ),
			)
		);

		$this->add_control(
			'tl_year_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts-timeline__year span' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_year' => 'yes' ),
			)
		);

		$this->add_control(
			'tl_year_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts-timeline__year span' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'show_year' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'tl_year_typography',
				'selector'  => '{{WRAPPER}} .eap-posts-timeline__year span',
				'condition' => array( 'show_year' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE — CARD (targets the reused .eap-posts__* card)
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
					'{{WRAPPER}} .eap-posts__item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Body Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20, 'unit' => 'px', 'isLinked' => true ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .eap-posts__item',
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
					'{{WRAPPER}} .eap-posts__item' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'card_shadow_tabs' );

		$this->start_controls_tab( 'card_shadow_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eap-posts__item',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'card_shadow_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow_hover',
				'selector' => '{{WRAPPER}} .eap-posts__item:hover',
			)
		);

		$this->add_control(
			'card_hover_lift',
			array(
				'label'      => __( 'Hover Lift', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 6, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__item:hover' => 'transform: translateY(-{{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_image_style() {
		$this->start_controls_section(
			'section_image_style',
			array(
				'label'     => __( 'Image', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_image' => 'yes' ),
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
					'{{WRAPPER}} .eap-posts__image' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-posts__img'   => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;',
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

		$this->end_controls_section();
	}

	protected function register_title_style() {
		$this->start_controls_section(
			'section_title_style',
			array(
				'label'     => __( 'Title', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__title, {{WRAPPER}} .eap-posts__title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__title a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-posts__title',
			)
		);

		$this->end_controls_section();
	}

	protected function register_meta_style() {
		$this->start_controls_section(
			'section_meta_style',
			array(
				'label'     => __( 'Meta', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_meta' => 'yes' ),
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__meta' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'meta_typography',
				'selector' => '{{WRAPPER}} .eap-posts__meta',
			)
		);

		$this->end_controls_section();
	}

	protected function register_excerpt_style() {
		$this->start_controls_section(
			'section_excerpt_style',
			array(
				'label'     => __( 'Excerpt', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_excerpt' => 'yes' ),
			)
		);

		$this->add_control(
			'excerpt_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__excerpt' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'excerpt_typography',
				'selector' => '{{WRAPPER}} .eap-posts__excerpt',
			)
		);

		$this->end_controls_section();
	}

	protected function register_button_style() {
		$this->start_controls_section(
			'section_button_style',
			array(
				'label'     => __( 'Read More', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_readmore' => 'yes' ),
			)
		);

		$this->add_control(
			'readmore_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__readmore' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'readmore_color_hover',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__readmore:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'readmore_typography',
				'selector'  => '{{WRAPPER}} .eap-posts__readmore',
				'separator' => 'before',
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
		$current  = $this->eap_get_post_id();

		$spec = array(
			'source'          => 'latest',
			'post_type'       => ! empty( $settings['post_type'] ) ? $settings['post_type'] : 'post',
			'per_page'        => isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 8,
			'orderby'         => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date',
			'order'           => ! empty( $settings['order'] ) ? $settings['order'] : 'DESC',
			'offset'          => isset( $settings['offset'] ) ? (int) $settings['offset'] : 0,
			'include_terms'   => isset( $settings['include_terms'] ) ? $settings['include_terms'] : array(),
			'exclude_terms'   => isset( $settings['exclude_terms'] ) ? $settings['exclude_terms'] : array(),
			'exclude_current' => 'yes' === ( $settings['exclude_current'] ?? '' ),
			'current_id'      => $current,
			'ignore_sticky'   => 'yes' === ( $settings['ignore_sticky'] ?? 'yes' ),
		);

		$display = array(
			'layout'         => 'grid',
			'show_image'     => 'yes' === ( $settings['show_image'] ?? 'yes' ),
			'show_badge'     => 'yes' === ( $settings['show_badge'] ?? 'yes' ),
			'show_title'     => 'yes' === ( $settings['show_title'] ?? 'yes' ),
			'show_meta'      => 'yes' === ( $settings['show_meta'] ?? 'yes' ),
			'show_excerpt'   => 'yes' === ( $settings['show_excerpt'] ?? 'yes' ),
			'show_readmore'  => 'yes' === ( $settings['show_readmore'] ?? 'yes' ),
			'title_tag'      => ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h3',
			'excerpt_length' => isset( $settings['excerpt_length'] ) ? (int) $settings['excerpt_length'] : 20,
			'meta'           => isset( $settings['meta_data'] ) ? $settings['meta_data'] : array( 'author', 'comments' ),
			'image_size'     => ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'medium_large',
			'readmore_text'  => isset( $settings['readmore_text'] ) ? $settings['readmore_text'] : __( 'Read More', 'elementor-animatepro' ),
		);

		$args  = EAP_Posts_Query::build_query_args( $spec, 1 );
		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-posts-timeline eap-posts eap-posts--empty">' . esc_html__( 'No posts found for this query.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$arrangement = ( 'one-sided' === ( $settings['arrangement'] ?? 'alternating' ) ) ? 'one' : 'alt';
		$show_date   = 'yes' === ( $settings['show_date'] ?? 'yes' );
		$show_year   = 'yes' === ( $settings['show_year'] ?? 'yes' );
		$date_format = isset( $settings['date_format'] ) ? trim( (string) $settings['date_format'] ) : '';
		$zoom        = 'yes' === ( $settings['image_hover_zoom'] ?? 'yes' );

		$classes = array(
			'eap-widget',
			'eap-posts',
			'eap-posts-timeline',
			'eap-posts-timeline--' . $arrangement,
		);
		if ( $zoom ) {
			$classes[] = 'eap-posts--zoom';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php
			$i         = 0;
			$last_year = null;

			foreach ( $query->posts as $post ) :
				$pid  = is_object( $post ) ? (int) $post->ID : (int) $post;
				$year = get_the_date( 'Y', $pid );

				if ( $show_year && $year !== $last_year ) :
					$last_year = $year;
					?>
					<div class="eap-posts-timeline__year"><span><?php echo esc_html( $year ); ?></span></div>
					<?php
				endif;

				$side = ( 'alt' === $arrangement && 1 === ( $i % 2 ) ) ? 'right' : 'left';
				?>
				<div class="eap-posts-timeline__item eap-posts-timeline__item--<?php echo esc_attr( $side ); ?>">
					<span class="eap-posts-timeline__dot" aria-hidden="true"></span>
					<?php if ( $show_date ) : ?>
						<span class="eap-posts-timeline__date"><?php echo esc_html( get_the_date( $date_format, $pid ) ); ?></span>
					<?php endif; ?>
					<div class="eap-posts-timeline__card">
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built by EAP_Posts_Query with escaped helpers.
						echo EAP_Posts_Query::render_card( $pid, $display );
						?>
					</div>
				</div>
				<?php
				$i++;
			endforeach;
			?>
		</div>
		<?php
		wp_reset_postdata();
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
