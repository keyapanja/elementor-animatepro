<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

/**
 * Advanced Posts — the Posts grid plus AJAX filter tabs and extra layouts
 * (grid / list / overlay / featured / masonry). Built on the shared
 * EAP_Posts_Query engine (same query builder + card markup + styling) so it
 * stays consistent with the Posts widget; the reused `eap-posts` stylesheet
 * provides the card / grid base and `eap-advanced-posts` adds the tabs +
 * featured/masonry layouts. Clicking a filter tab re-queries the current post
 * type narrowed by that term (via the shared `eap_load_posts` endpoint) and
 * swaps the grid. Latest-posts query only. CSS + advanced-posts.js.
 */
class EAP_Widget_Advanced_Posts extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-advanced-posts';
	}

	public function get_title() {
		return __( 'Advanced Posts', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-posts-masonry';
	}

	public function get_keywords() {
		return array( 'posts', 'advanced', 'filter', 'tabs', 'grid', 'masonry', 'featured', 'dynamic' );
	}

	public function get_style_depends() {
		return array( 'eap-core', 'eap-posts', 'eap-advanced-posts' );
	}

	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-advanced-posts-script' );
	}

	protected function register_controls() {
		$this->register_query_section();
		$this->register_filter_section();
		$this->register_layout_section();
		$this->register_pagination_section();

		$this->register_grid_style();
		$this->register_filter_style();
		$this->register_card_style();
		$this->register_image_style();
		$this->register_title_style();
		$this->register_meta_style();
		$this->register_excerpt_style();
		$this->register_button_style();
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
				'label'   => __( 'Posts Per Page', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 48,
				'default' => 6,
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
					'DESC' => __( 'Descending', 'elementor-animatepro' ),
					'ASC'  => __( 'Ascending', 'elementor-animatepro' ),
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
				'description' => __( 'Restrict the whole grid to these terms (the filter tabs narrow further).', 'elementor-animatepro' ),
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
	 * CONTENT — FILTER TABS
	 * ================================================================== */

	protected function register_filter_section() {
		$this->start_controls_section(
			'section_filter',
			array(
				'label' => __( 'Filter Tabs', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_filters',
			array(
				'label'        => __( 'Filter Tabs', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'filter_taxonomy',
			array(
				'label'     => __( 'Filter By', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'category',
				'options'   => $this->get_taxonomy_options(),
				'condition' => array( 'show_filters' => 'yes' ),
			)
		);

		$this->add_control(
			'show_all_tab',
			array(
				'label'        => __( '"All" Tab', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'show_filters' => 'yes' ),
			)
		);

		$this->add_control(
			'all_label',
			array(
				'label'     => __( '"All" Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'All', 'elementor-animatepro' ),
				'condition' => array(
					'show_filters' => 'yes',
					'show_all_tab' => 'yes',
				),
			)
		);

		$this->add_control(
			'filter_hide_empty',
			array(
				'label'        => __( 'Only Terms With Posts', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'show_filters' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'filter_align',
			array(
				'label'                => __( 'Alignment', 'elementor-animatepro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'flex-start',
				'options'              => array(
					'flex-start' => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors'            => array(
					'{{WRAPPER}} .eap-advanced-posts__filters' => 'justify-content: {{VALUE}};',
				),
				'condition'            => array( 'show_filters' => 'yes' ),
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
				'default' => 'grid',
				'options' => array(
					'grid'     => __( 'Grid', 'elementor-animatepro' ),
					'list'     => __( 'List', 'elementor-animatepro' ),
					'overlay'  => __( 'Overlay', 'elementor-animatepro' ),
					'featured' => __( 'Featured (first large)', 'elementor-animatepro' ),
					'masonry'  => __( 'Masonry', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'elementor-animatepro' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => array( 'px' => array( 'min' => 1, 'max' => 6, 'step' => 1 ) ),
				'default'        => array( 'size' => 3 ),
				'tablet_default' => array( 'size' => 2 ),
				'mobile_default' => array( 'size' => 1 ),
				'selectors'      => array(
					'{{WRAPPER}} .eap-posts__grid' => '--eap-posts-cols: {{SIZE}};',
				),
				'condition'      => array( 'layout!' => 'list' ),
			)
		);

		$this->add_control(
			'image_position',
			array(
				'label'     => __( 'Image Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => array(
					'left'  => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'condition' => array( 'layout' => 'list' ),
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
				'default' => 18,
			)
		);

		$this->add_control(
			'meta_data',
			array(
				'label'       => __( 'Meta', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'default'     => array( 'date', 'comments' ),
				'options'     => array(
					'author'   => __( 'Author', 'elementor-animatepro' ),
					'date'     => __( 'Date', 'elementor-animatepro' ),
					'comments' => __( 'Comments', 'elementor-animatepro' ),
				),
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
	 * CONTENT — PAGINATION
	 * ================================================================== */

	protected function register_pagination_section() {
		$this->start_controls_section(
			'section_pagination',
			array(
				'label' => __( 'Pagination', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'   => __( 'Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'      => __( 'None', 'elementor-animatepro' ),
					'load_more' => __( 'Load More Button', 'elementor-animatepro' ),
					'infinite'  => __( 'Infinite Scroll', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'load_more_text',
			array(
				'label'     => __( 'Button Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Load More', 'elementor-animatepro' ),
				'condition' => array( 'pagination_type' => 'load_more' ),
			)
		);

		$this->add_responsive_control(
			'pagination_align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__pagination' => 'justify-content: {{VALUE}};',
				),
				'condition' => array( 'pagination_type!' => 'none' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_grid_style() {
		$this->start_controls_section(
			'section_grid_style',
			array(
				'label' => __( 'Grid', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label'      => __( 'Column Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__grid' => 'column-gap: {{SIZE}}{{UNIT}}; --eap-posts-col-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'      => __( 'Row Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__grid' => 'row-gap: {{SIZE}}{{UNIT}}; --eap-posts-row-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_filter_style() {
		$this->start_controls_section(
			'section_filter_style',
			array(
				'label'     => __( 'Filter Tabs', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_filters' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filter_typography',
				'selector' => '{{WRAPPER}} .eap-advanced-posts__filter',
			)
		);

		$this->add_responsive_control(
			'filter_gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-posts__filters' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_spacing',
			array(
				'label'      => __( 'Spacing Below', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-posts__filters' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 8, 'right' => 16, 'bottom' => 8, 'left' => 16, 'unit' => 'px', 'isLinked' => false ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-posts__filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
				'default'    => array( 'size' => 30, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-posts__filter' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'filter_tabs_style' );

		$this->start_controls_tab( 'filter_tab_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'filter_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#374151',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-posts__filter' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f3f4f6',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-posts__filter' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'filter_tab_active', array( 'label' => __( 'Active', 'elementor-animatepro' ) ) );

		$this->add_control(
			'filter_color_active',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-posts__filter.is-active, {{WRAPPER}} .eap-advanced-posts__filter:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_bg_active',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-posts__filter.is-active, {{WRAPPER}} .eap-advanced-posts__filter:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

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
				'label'       => __( 'Height', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'vh' ),
				'range'       => array( 'px' => array( 'min' => 80, 'max' => 600 ) ),
				'default'     => array( 'size' => 220, 'unit' => 'px' ),
				'selectors'   => array(
					'{{WRAPPER}} .eap-posts__image'                    => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-posts__img'                      => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;',
					'{{WRAPPER}} .eap-posts--overlay .eap-posts__item' => 'min-height: {{SIZE}}{{UNIT}};',
				),
				'description' => __( 'Fixed image height (ignored in Masonry, where images keep their ratio).', 'elementor-animatepro' ),
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

	protected function register_pagination_style() {
		$this->start_controls_section(
			'section_pagination_style',
			array(
				'label'     => __( 'Load More', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'pagination_type!' => 'none' ),
			)
		);

		$this->add_control(
			'lm_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#374151',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__load-more' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lm_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f3f4f6',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__load-more' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lm_bg_hover',
			array(
				'label'     => __( 'Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__load-more:hover' => 'background-color: {{VALUE}}; color: #fff; border-color: {{VALUE}};',
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
		$current  = $this->eap_get_post_id();

		$spec = array(
			'source'          => 'latest',
			'post_type'       => ! empty( $settings['post_type'] ) ? $settings['post_type'] : 'post',
			'per_page'        => isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 6,
			'orderby'         => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date',
			'order'           => ! empty( $settings['order'] ) ? $settings['order'] : 'DESC',
			'offset'          => isset( $settings['offset'] ) ? (int) $settings['offset'] : 0,
			'include_terms'   => isset( $settings['include_terms'] ) ? $settings['include_terms'] : array(),
			'exclude_terms'   => isset( $settings['exclude_terms'] ) ? $settings['exclude_terms'] : array(),
			'filter_terms'    => array(),
			'exclude_current' => 'yes' === ( $settings['exclude_current'] ?? '' ),
			'current_id'      => $current,
			'ignore_sticky'   => 'yes' === ( $settings['ignore_sticky'] ?? 'yes' ),
		);

		$display = array(
			'layout'         => ! empty( $settings['layout'] ) ? $settings['layout'] : 'grid',
			'image_position' => ! empty( $settings['image_position'] ) ? $settings['image_position'] : 'left',
			'show_image'     => 'yes' === ( $settings['show_image'] ?? 'yes' ),
			'show_badge'     => 'yes' === ( $settings['show_badge'] ?? 'yes' ),
			'show_title'     => 'yes' === ( $settings['show_title'] ?? 'yes' ),
			'show_meta'      => 'yes' === ( $settings['show_meta'] ?? 'yes' ),
			'show_excerpt'   => 'yes' === ( $settings['show_excerpt'] ?? 'yes' ),
			'show_readmore'  => 'yes' === ( $settings['show_readmore'] ?? 'yes' ),
			'title_tag'      => ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h3',
			'excerpt_length' => isset( $settings['excerpt_length'] ) ? (int) $settings['excerpt_length'] : 18,
			'meta'           => isset( $settings['meta_data'] ) ? $settings['meta_data'] : array( 'date', 'comments' ),
			'image_size'     => ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'medium_large',
			'readmore_text'  => isset( $settings['readmore_text'] ) ? $settings['readmore_text'] : __( 'Read More', 'elementor-animatepro' ),
		);

		$layout     = $display['layout'];
		$pagination = ! empty( $settings['pagination_type'] ) ? $settings['pagination_type'] : 'none';

		// Filter tabs: when the "All" tab is hidden the first term is active, so
		// the initial grid must already be narrowed to it (and its tab marked).
		$filter     = $this->get_filter_data( $settings );
		$query_spec = $spec;
		if ( $filter && $filter['active'] ) {
			$query_spec['filter_terms'] = array( $filter['active'] );
		}

		$args  = EAP_Posts_Query::build_query_args( $query_spec, 1 );
		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-advanced-posts eap-posts eap-posts--empty">' . esc_html__( 'No posts found for this query.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$zoom    = 'yes' === ( $settings['image_hover_zoom'] ?? 'yes' );
		$classes = array(
			'eap-widget',
			'eap-advanced-posts',
			'eap-posts',
			'eap-posts--' . $layout,
			'eap-posts--img-' . $display['image_position'],
			'eap-posts--pag-' . $pagination,
		);
		if ( $zoom ) {
			$classes[] = 'eap-posts--zoom';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		$ajax = in_array( $pagination, array( 'load_more', 'infinite' ), true );
		$this->add_render_attribute(
			'wrapper',
			array(
				'data-eap-advanced-posts' => '',
				'data-mode'               => $ajax ? $pagination : 'none',
				'data-page'               => '1',
				'data-max'                => (string) max( 1, (int) $query->max_num_pages ),
				'data-nonce'              => wp_create_nonce( 'eap-load-posts' ),
				'data-ajax-url'           => esc_url( admin_url( 'admin-ajax.php' ) ),
				'data-spec'               => wp_json_encode( $spec ),
				'data-display'            => wp_json_encode( $display ),
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php $this->render_filters( $filter ); ?>
			<div class="eap-posts__grid">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built by EAP_Posts_Query with escaped helpers.
				echo EAP_Posts_Query::render_cards( $query, $display );
				?>
			</div>
			<?php $this->render_pagination( $settings, $pagination, $query ); ?>
		</div>
		<?php
		wp_reset_postdata();
	}

	/**
	 * Compute the filter tab data (terms + which one is initially active).
	 *
	 * @param array $settings Settings.
	 * @return array{terms:WP_Term[],show_all:bool,all_label:string,active:int}|null
	 */
	protected function get_filter_data( $settings ) {
		if ( 'yes' !== ( $settings['show_filters'] ?? 'yes' ) ) {
			return null;
		}

		$taxonomy = ! empty( $settings['filter_taxonomy'] ) ? $settings['filter_taxonomy'] : 'category';
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return null;
		}

		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => 'yes' === ( $settings['filter_hide_empty'] ?? 'yes' ),
				'number'     => 50,
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return null;
		}

		$show_all = 'yes' === ( $settings['show_all_tab'] ?? 'yes' );

		return array(
			'terms'     => $terms,
			'show_all'  => $show_all,
			'all_label' => ! empty( $settings['all_label'] ) ? $settings['all_label'] : __( 'All', 'elementor-animatepro' ),
			'active'    => $show_all ? 0 : (int) $terms[0]->term_id,
		);
	}

	/**
	 * Render the filter tab bar from precomputed data.
	 *
	 * @param array|null $filter Filter data from get_filter_data().
	 * @return void
	 */
	protected function render_filters( $filter ) {
		if ( ! $filter ) {
			return;
		}
		?>
		<div class="eap-advanced-posts__filters" role="tablist">
			<?php if ( $filter['show_all'] ) : ?>
				<button type="button" class="eap-advanced-posts__filter is-active" data-term="0" role="tab" aria-selected="true"><?php echo esc_html( $filter['all_label'] ); ?></button>
			<?php endif; ?>
			<?php
			foreach ( $filter['terms'] as $term ) :
				$is_active = ( (int) $term->term_id === $filter['active'] );
				?>
				<button
					type="button"
					class="eap-advanced-posts__filter<?php echo $is_active ? ' is-active' : ''; ?>"
					data-term="<?php echo esc_attr( (string) $term->term_id ); ?>"
					role="tab"
					aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
				><?php echo esc_html( $term->name ); ?></button>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render the load-more / infinite control.
	 *
	 * @param array    $settings   Settings.
	 * @param string   $pagination Mode.
	 * @param WP_Query $query      Query.
	 * @return void
	 */
	protected function render_pagination( $settings, $pagination, $query ) {
		$max = max( 1, (int) $query->max_num_pages );

		if ( 'load_more' === $pagination ) {
			$text   = ! empty( $settings['load_more_text'] ) ? $settings['load_more_text'] : __( 'Load More', 'elementor-animatepro' );
			$hidden = $max <= 1 ? ' hidden' : '';
			echo '<div class="eap-posts__pagination eap-posts__pagination--load-more">';
			printf( '<button type="button" class="eap-posts__load-more%s">%s</button>', esc_attr( $hidden ), esc_html( $text ) );
			echo '</div>';
			return;
		}

		if ( 'infinite' === $pagination ) {
			$hidden = $max <= 1 ? ' hidden' : '';
			echo '<div class="eap-posts__pagination eap-posts__pagination--infinite">';
			echo '<div class="eap-posts__sentinel' . esc_attr( $hidden ) . '" aria-hidden="true"></div>';
			echo '<div class="eap-posts__spinner" aria-hidden="true"></div>';
			echo '</div>';
		}
	}

	/**
	 * Public taxonomies for the filter select.
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
