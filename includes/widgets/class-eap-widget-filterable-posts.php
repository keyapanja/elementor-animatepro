<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * Filterable Posts widget.
 *
 * A post grid whose filter tabs act INSTANTLY on an already-loaded pool, with
 * the remaining cards animating into their new positions.
 *
 * How this differs from Advanced Posts, which also has filter tabs: that widget
 * re-queries the server on every tab and replaces the grid. This one queries
 * once and filters in the browser. The trade is deliberate — Advanced Posts can
 * page through thousands of posts, this one is instant but limited to the pool
 * it loaded.
 *
 * The pay-off is a problem Advanced Posts structurally cannot solve. Because a
 * re-querying filter builds its tabs from `get_terms()`, a tab can name a term
 * that has no post inside the loaded range, and clicking it yields an empty
 * grid (the same failure that shaped Filterable Slider). Here the tabs are
 * derived FROM THE POSTS THAT WERE ACTUALLY FETCHED, so every tab is guaranteed
 * to have something behind it — and the exact counts are already known, which is
 * why they can be shown on the tabs for free.
 *
 * Cards come from EAP_Posts_Query::render_card() and are styled by posts.css, so
 * this widget only owns the filter bar, the grid and the animation.
 */
class EAP_Widget_Filterable_Posts extends EAP_Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-filterable-posts';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Filterable Posts', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-filter';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'posts', 'filter', 'filterable', 'grid', 'masonry', 'category', 'isotope' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-posts', 'eap-filterable-posts' );
	}

	/**
	 * Scripts.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-filterable-posts-script' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_query_section();
		$this->register_filter_section();
		$this->register_layout_section();

		$this->register_grid_style();
		$this->register_filter_style();
		$this->register_card_style();
		$this->register_title_style();
		$this->register_meta_style();
		$this->register_excerpt_style();
		$this->register_button_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	/**
	 * Query section.
	 *
	 * @return void
	 */
	protected function register_query_section() {
		$this->start_controls_section(
			'section_query',
			array( 'label' => __( 'Query', 'elementor-animatepro' ) )
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
				'label'       => __( 'Posts To Load', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 12,
				'min'         => 1,
				'max'         => 60,
				'description' => __( 'Filtering happens in the browser, so this is the whole pool — every tab draws from these posts.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => __( 'Order By', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'     => __( 'Date', 'elementor-animatepro' ),
					'title'    => __( 'Title', 'elementor-animatepro' ),
					'rand'     => __( 'Random', 'elementor-animatepro' ),
					'modified' => __( 'Last Modified', 'elementor-animatepro' ),
					'comment_count' => __( 'Comment Count', 'elementor-animatepro' ),
					'menu_order'    => __( 'Menu Order', 'elementor-animatepro' ),
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
				'default' => 0,
				'min'     => 0,
			)
		);

		$this->add_control(
			'include_terms',
			array(
				'label'       => __( 'Include Terms', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => EAP_Posts_Query::get_term_options(),
				'label_block' => true,
			)
		);

		$this->add_control(
			'exclude_terms',
			array(
				'label'       => __( 'Exclude Terms', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => EAP_Posts_Query::get_term_options(),
				'label_block' => true,
			)
		);

		$this->add_control(
			'exclude_current',
			array(
				'label'        => __( 'Exclude Current Post', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'ignore_sticky',
			array(
				'label'        => __( 'Ignore Sticky Posts', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Filter bar section.
	 *
	 * @return void
	 */
	protected function register_filter_section() {
		$this->start_controls_section(
			'section_filter',
			array( 'label' => __( 'Filter', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'show_filters',
			array(
				'label'        => __( 'Show Filter Tabs', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
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
				'label'        => __( 'Show "All" Tab', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
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
			'show_counts',
			array(
				'label'        => __( 'Show Counts', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'The counts are exact: they come from the posts that were loaded, not from the term totals.', 'elementor-animatepro' ),
				'condition'    => array( 'show_filters' => 'yes' ),
			)
		);

		$this->add_control(
			'filter_align',
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
					'{{WRAPPER}} .eap-filterable-posts__filters' => 'justify-content: {{VALUE}};',
				),
				'condition' => array( 'show_filters' => 'yes' ),
			)
		);

		$this->add_control(
			'show_search',
			array(
				'label'        => __( 'Show Search Box', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'search_placeholder',
			array(
				'label'     => __( 'Search Placeholder', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Search posts…', 'elementor-animatepro' ),
				'condition' => array( 'show_search' => 'yes' ),
			)
		);

		$this->add_control(
			'empty_text',
			array(
				'label'     => __( 'Nothing-Found Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'No posts match that filter.', 'elementor-animatepro' ),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Layout section.
	 *
	 * @return void
	 */
	protected function register_layout_section() {
		$this->start_controls_section(
			'section_layout',
			array( 'label' => __( 'Layout', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'grid'    => __( 'Grid', 'elementor-animatepro' ),
					'masonry' => __( 'Masonry', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'elementor-animatepro' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
				),
				'selectors'      => array(
					'{{WRAPPER}}' => '--eap-posts-cols: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}' => '--eap-posts-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'anim_speed',
			array(
				'label'       => __( 'Filter Speed', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'ms' ),
				'range'       => array( 'ms' => array( 'min' => 0, 'max' => 1200, 'step' => 20 ) ),
				'default'     => array( 'size' => 400, 'unit' => 'ms' ),
				'selectors'   => array(
					'{{WRAPPER}}' => '--eap-fp-speed: {{SIZE}}ms;',
				),
				'description' => __( 'How long cards take to slide into their new positions.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'elements_heading',
			array(
				'label'     => __( 'Card Elements', 'elementor-animatepro' ),
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
					'return_value' => 'yes',
					'default'      => 'show_excerpt' === $key ? 'yes' : 'yes',
				)
			);
		}

		$this->add_control(
			'title_tag',
			array(
				'label'     => __( 'Title Tag', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
				),
				'separator' => 'before',
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label'     => __( 'Excerpt Words', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 18,
				'min'       => 4,
				'max'       => 100,
				'condition' => array( 'show_excerpt' => 'yes' ),
			)
		);

		$this->add_control(
			'meta_data',
			array(
				'label'    => __( 'Meta', 'elementor-animatepro' ),
				'type'     => Controls_Manager::SELECT2,
				'multiple' => true,
				'default'  => array( 'date', 'comments' ),
				'options'  => array(
					'author'   => __( 'Author', 'elementor-animatepro' ),
					'date'     => __( 'Date', 'elementor-animatepro' ),
					'comments' => __( 'Comments', 'elementor-animatepro' ),
				),
				'condition' => array( 'show_meta' => 'yes' ),
			)
		);

		$this->add_control(
			'readmore_text',
			array(
				'label'     => __( 'Read More Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read More', 'elementor-animatepro' ),
				'condition' => array( 'show_readmore' => 'yes' ),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'     => __( 'Image Size', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'medium_large',
				'options'   => $this->get_image_size_options(),
				'condition' => array( 'show_image' => 'yes' ),
			)
		);

		$this->add_control(
			'image_hover_zoom',
			array(
				'label'        => __( 'Zoom Image On Hover', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'show_image' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	/**
	 * Grid style.
	 *
	 * @return void
	 */
	protected function register_grid_style() {
		$this->start_controls_section(
			'style_grid',
			array(
				'label' => __( 'Grid', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'empty_color',
			array(
				'label'     => __( 'Nothing-Found Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-filterable-posts__empty' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'empty_typography',
				'selector' => '{{WRAPPER}} .eap-filterable-posts__empty',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Filter bar style.
	 *
	 * @return void
	 */
	protected function register_filter_style() {
		$this->start_controls_section(
			'style_filter',
			array(
				'label'     => __( 'Filter Bar', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_filters' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filter_typography',
				'selector' => '{{WRAPPER}} .eap-filterable-posts__filter',
			)
		);

		$this->add_responsive_control(
			'filter_gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-filterable-posts__filters' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 8,
					'right'    => 18,
					'bottom'   => 8,
					'left'     => 18,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-filterable-posts__filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 999, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-filterable-posts__filter' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_margin',
			array(
				'label'      => __( 'Bar Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-filterable-posts__filters' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'filter_tabs' );

		$this->start_controls_tab( 'filter_tab_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'filter_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-filterable-posts__filter' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-filterable-posts__filter' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_border',
				'selector' => '{{WRAPPER}} .eap-filterable-posts__filter',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'filter_tab_active', array( 'label' => __( 'Active', 'elementor-animatepro' ) ) );

		$this->add_control(
			'filter_color_active',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-filterable-posts__filter.is-active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_bg_active',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-filterable-posts__filter.is-active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_border_active',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-filterable-posts__filter.is-active' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'search_heading',
			array(
				'label'     => __( 'Search Box', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'show_search' => 'yes' ),
			)
		);

		$this->add_control(
			'search_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-filterable-posts__search' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_search' => 'yes' ),
			)
		);

		$this->add_control(
			'search_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-filterable-posts__search' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'show_search' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'search_border',
				'selector'  => '{{WRAPPER}} .eap-filterable-posts__search',
				'condition' => array( 'show_search' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Card style.
	 *
	 * @return void
	 */
	protected function register_card_style() {
		$this->start_controls_section(
			'style_card',
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
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__item' => 'border-radius: {{SIZE}}{{UNIT}};',
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

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eap-posts__item',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Title style.
	 *
	 * @return void
	 */
	protected function register_title_style() {
		$this->start_controls_section(
			'style_title',
			array(
				'label'     => __( 'Title', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-posts__title',
			)
		);

		// No default: a hardcoded one emits {{WRAPPER}}-scoped CSS that would beat
		// posts.css. The colour is only written when the user actually picks one.
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
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__item:hover .eap-posts__title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Meta style.
	 *
	 * @return void
	 */
	protected function register_meta_style() {
		$this->start_controls_section(
			'style_meta',
			array(
				'label'     => __( 'Meta', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_meta' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'meta_typography',
				'selector' => '{{WRAPPER}} .eap-posts__meta',
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

		$this->end_controls_section();
	}

	/**
	 * Excerpt style.
	 *
	 * @return void
	 */
	protected function register_excerpt_style() {
		$this->start_controls_section(
			'style_excerpt',
			array(
				'label'     => __( 'Excerpt', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_excerpt' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'excerpt_typography',
				'selector' => '{{WRAPPER}} .eap-posts__excerpt',
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

		$this->end_controls_section();
	}

	/**
	 * Read More style.
	 *
	 * @return void
	 */
	protected function register_button_style() {
		$this->start_controls_section(
			'style_button',
			array(
				'label'     => __( 'Read More', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_readmore' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'readmore_typography',
				'selector' => '{{WRAPPER}} .eap-posts__readmore',
			)
		);

		$this->add_control(
			'readmore_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
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

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	/**
	 * Render.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$editor   = $this->eap_is_editor();

		$spec = array(
			'source'          => 'latest',
			'post_type'       => ! empty( $settings['post_type'] ) ? $settings['post_type'] : 'post',
			'per_page'        => isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 12,
			'orderby'         => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date',
			'order'           => ! empty( $settings['order'] ) ? $settings['order'] : 'DESC',
			'offset'          => isset( $settings['offset'] ) ? (int) $settings['offset'] : 0,
			'include_terms'   => isset( $settings['include_terms'] ) ? $settings['include_terms'] : array(),
			'exclude_terms'   => isset( $settings['exclude_terms'] ) ? $settings['exclude_terms'] : array(),
			'filter_terms'    => array(),
			'exclude_current' => 'yes' === ( $settings['exclude_current'] ?? '' ),
			'current_id'      => $this->eap_get_post_id(),
			'ignore_sticky'   => 'yes' === ( $settings['ignore_sticky'] ?? 'yes' ),
		);

		$display = array(
			// posts.css only styles grid/list/overlay; masonry is this widget's own
			// wrapper modifier, so the CARDS always render as grid cards.
			'layout'         => 'grid',
			'image_position' => 'left',
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

		$args  = EAP_Posts_Query::build_query_args( $spec, 1 );
		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-filterable-posts eap-posts eap-posts--empty">' . esc_html__( 'No posts found for this query.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$post_ids = wp_list_pluck( $query->posts, 'ID' );
		$filter   = $this->get_filter_data( $settings, $post_ids );

		$layout  = ! empty( $settings['layout'] ) ? $settings['layout'] : 'grid';
		$classes = array(
			'eap-widget',
			'eap-filterable-posts',
			'eap-filterable-posts--' . $layout,
			'eap-posts',
			'eap-posts--grid',
		);
		if ( 'yes' === ( $settings['image_hover_zoom'] ?? 'yes' ) ) {
			$classes[] = 'eap-posts--zoom';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		$this->add_render_attribute( 'wrapper', 'data-eap-filterable-posts', '' );

		$show_search = 'yes' === ( $settings['show_search'] ?? '' );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $filter || $show_search ) : ?>
				<div class="eap-filterable-posts__bar">
					<?php $this->render_filters( $filter, $settings ); ?>
					<?php if ( $show_search ) : ?>
						<input
							type="search"
							class="eap-filterable-posts__search"
							placeholder="<?php echo esc_attr( $settings['search_placeholder'] ?? '' ); ?>"
							aria-label="<?php echo esc_attr( $settings['search_placeholder'] ?? __( 'Search posts', 'elementor-animatepro' ) ); ?>"
						>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="eap-filterable-posts__grid eap-posts__grid">
				<?php
				foreach ( $post_ids as $pid ) {
					$terms = isset( $filter['map'][ $pid ] ) ? $filter['map'][ $pid ] : array();
					?>
					<div class="eap-filterable-posts__item" data-terms="<?php echo esc_attr( implode( ' ', $terms ) ); ?>">
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built by EAP_Posts_Query with escaped helpers.
						echo EAP_Posts_Query::render_card( $pid, $display );
						?>
					</div>
					<?php
				}
				?>
			</div>

			<p class="eap-filterable-posts__empty" hidden>
				<?php echo esc_html( $settings['empty_text'] ?? __( 'No posts match that filter.', 'elementor-animatepro' ) ); ?>
			</p>
		</div>
		<?php
		wp_reset_postdata();
	}

	/**
	 * Build the filter tabs from the terms the LOADED posts actually carry.
	 *
	 * One `wp_get_object_terms()` call for the whole set rather than one per
	 * post, and it doubles as the post -> term map the markup needs.
	 *
	 * @param array $settings Settings.
	 * @param int[] $post_ids Loaded post IDs.
	 * @return array{terms:array,map:array,show_all:bool,all_label:string,total:int}|null
	 */
	protected function get_filter_data( $settings, $post_ids ) {
		if ( 'yes' !== ( $settings['show_filters'] ?? 'yes' ) || empty( $post_ids ) ) {
			return null;
		}

		$taxonomy = ! empty( $settings['filter_taxonomy'] ) ? $settings['filter_taxonomy'] : 'category';
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return null;
		}

		$related = wp_get_object_terms( $post_ids, $taxonomy, array( 'fields' => 'all_with_object_id' ) );
		if ( is_wp_error( $related ) || empty( $related ) ) {
			return null;
		}

		$terms = array();
		$map   = array();

		foreach ( $related as $term ) {
			$map[ $term->object_id ][] = (int) $term->term_id;

			if ( ! isset( $terms[ $term->term_id ] ) ) {
				$terms[ $term->term_id ] = array(
					'id'    => (int) $term->term_id,
					'name'  => $term->name,
					'count' => 0,
				);
			}
			$terms[ $term->term_id ]['count']++;
		}

		// Alphabetical, so the bar doesn't reshuffle when the query order changes.
		uasort(
			$terms,
			static function ( $a, $b ) {
				return strcasecmp( $a['name'], $b['name'] );
			}
		);

		return array(
			'terms'     => $terms,
			'map'       => $map,
			'show_all'  => 'yes' === ( $settings['show_all_tab'] ?? 'yes' ),
			'all_label' => ! empty( $settings['all_label'] ) ? $settings['all_label'] : __( 'All', 'elementor-animatepro' ),
			'total'     => count( $post_ids ),
		);
	}

	/**
	 * Render the filter tab bar.
	 *
	 * @param array|null $filter   Filter data.
	 * @param array      $settings Settings.
	 * @return void
	 */
	protected function render_filters( $filter, $settings ) {
		if ( ! $filter ) {
			return;
		}

		$counts = 'yes' === ( $settings['show_counts'] ?? 'yes' );

		// With no "All" tab the first term is active, so something is always shown.
		$first  = $filter['show_all'] ? 0 : (int) key( $filter['terms'] );
		?>
		<div class="eap-filterable-posts__filters" role="group">
			<?php if ( $filter['show_all'] ) : ?>
				<button type="button" class="eap-filterable-posts__filter is-active" data-filter="0" aria-pressed="true">
					<span class="eap-filterable-posts__filter-label"><?php echo esc_html( $filter['all_label'] ); ?></span>
					<?php if ( $counts ) : ?>
						<span class="eap-filterable-posts__filter-count"><?php echo esc_html( (string) $filter['total'] ); ?></span>
					<?php endif; ?>
				</button>
			<?php endif; ?>

			<?php foreach ( $filter['terms'] as $term ) : ?>
				<?php $active = ( $term['id'] === $first ); ?>
				<button
					type="button"
					class="eap-filterable-posts__filter<?php echo $active ? ' is-active' : ''; ?>"
					data-filter="<?php echo esc_attr( (string) $term['id'] ); ?>"
					aria-pressed="<?php echo $active ? 'true' : 'false'; ?>"
				>
					<span class="eap-filterable-posts__filter-label"><?php echo esc_html( $term['name'] ); ?></span>
					<?php if ( $counts ) : ?>
						<span class="eap-filterable-posts__filter-count"><?php echo esc_html( (string) $term['count'] ); ?></span>
					<?php endif; ?>
				</button>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Public taxonomies for the filter picker.
	 *
	 * @return array<string, string>
	 */
	protected function get_taxonomy_options() {
		$options = array();
		foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $tax ) {
			$options[ $tax->name ] = $tax->labels->singular_name;
		}
		return $options;
	}

	/**
	 * Registered image sizes.
	 *
	 * @return array<string, string>
	 */
	protected function get_image_size_options() {
		$options = array( 'full' => __( 'Full', 'elementor-animatepro' ) );
		foreach ( get_intermediate_image_sizes() as $size ) {
			$options[ $size ] = ucwords( str_replace( array( '_', '-' ), ' ', $size ) );
		}
		return $options;
	}
}
