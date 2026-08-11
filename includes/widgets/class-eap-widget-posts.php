<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

/**
 * Posts — a query-driven grid / list / overlay of multiple posts (the blog-grid
 * builder). Query source: Latest (post type + taxonomy filters + order), Manual
 * (hand-picked) or Current Query (archive / search context). Per-element cards
 * (image, category badge, title, meta, excerpt, read-more) and four pagination
 * modes (none / numbered / load-more / infinite). Query building + card markup
 * live in the Elementor-free EAP_Posts_Query so the AJAX-paged cards match the
 * first render exactly. CSS + posts.js (for load-more / infinite).
 */
class EAP_Widget_Posts extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-posts';
	}

	public function get_title() {
		return __( 'Posts', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_keywords() {
		return array( 'posts', 'blog', 'grid', 'query', 'loop', 'archive', 'dynamic' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'posts' );
	}

	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-posts-script' );
	}

	protected function register_controls() {
		$this->register_query_section();
		$this->register_layout_section();
		$this->register_pagination_section();

		$this->register_grid_style();
		$this->register_card_style();
		$this->register_image_style();
		$this->register_badge_style();
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
			'query_source',
			array(
				'label'   => __( 'Source', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'latest',
				'options' => array(
					'latest'  => __( 'Latest Posts', 'elementor-animatepro' ),
					'manual'  => __( 'Manual Selection', 'elementor-animatepro' ),
					'current' => __( 'Current Query (Archive)', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'current_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Shows the posts of the current archive / search / blog page. In the editor a latest-posts preview is shown.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'query_source' => 'current' ),
			)
		);

		$this->add_control(
			'post_type',
			array(
				'label'     => __( 'Post Type', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'post',
				'options'   => EAP_Posts_Query::get_post_type_options(),
				'condition' => array( 'query_source' => 'latest' ),
			)
		);

		$this->add_control(
			'manual_posts',
			array(
				'label'       => __( 'Posts', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => EAP_Posts_Query::get_post_options(),
				'condition'   => array( 'query_source' => 'manual' ),
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
				'description' => __( 'Categories, tags or custom taxonomy terms to include.', 'elementor-animatepro' ),
				'condition'   => array( 'query_source' => 'latest' ),
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
				'condition'   => array( 'query_source' => 'latest' ),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => __( 'Order By', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => array(
					'date'          => __( 'Date', 'elementor-animatepro' ),
					'modified'      => __( 'Last Modified', 'elementor-animatepro' ),
					'title'         => __( 'Title', 'elementor-animatepro' ),
					'menu_order'    => __( 'Menu Order', 'elementor-animatepro' ),
					'comment_count' => __( 'Comment Count', 'elementor-animatepro' ),
					'rand'          => __( 'Random', 'elementor-animatepro' ),
				),
				'condition' => array( 'query_source' => 'latest' ),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'     => __( 'Order', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'DESC',
				'options'   => array(
					'DESC' => __( 'Descending', 'elementor-animatepro' ),
					'ASC'  => __( 'Ascending', 'elementor-animatepro' ),
				),
				'condition' => array( 'query_source' => 'latest' ),
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'     => __( 'Offset', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'default'   => 0,
				'condition' => array( 'query_source' => 'latest' ),
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
				'condition'    => array( 'query_source!' => 'manual' ),
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
				'condition'    => array( 'query_source' => 'latest' ),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'       => __( 'Posts Per Page', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 48,
				'default'     => 6,
				'condition'   => array( 'query_source!' => 'manual' ),
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
					'grid'    => __( 'Grid', 'elementor-animatepro' ),
					'list'    => __( 'List', 'elementor-animatepro' ),
					'overlay' => __( 'Overlay', 'elementor-animatepro' ),
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

		$this->add_responsive_control(
			'list_image_width',
			array(
				'label'      => __( 'Image Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array( '%' => array( 'min' => 20, 'max' => 60 ) ),
				'default'    => array( 'size' => 40, 'unit' => '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts--list .eap-posts__item' => '--eap-posts-list-img: {{SIZE}}%;',
				),
				'condition'  => array( 'layout' => 'list' ),
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
					'numbered'  => __( 'Numbered', 'elementor-animatepro' ),
					'load_more' => __( 'Load More Button', 'elementor-animatepro' ),
					'infinite'  => __( 'Infinite Scroll', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'pagination_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Load More / Infinite apply to Latest Posts. Manual selection has no pagination; Current Query uses numbered pages.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'pagination_type!' => 'none' ),
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

		$this->add_control(
			'num_prev_text',
			array(
				'label'     => __( 'Previous Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( '‹ Prev', 'elementor-animatepro' ),
				'condition' => array( 'pagination_type' => 'numbered' ),
			)
		);

		$this->add_control(
			'num_next_text',
			array(
				'label'     => __( 'Next Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Next ›', 'elementor-animatepro' ),
				'condition' => array( 'pagination_type' => 'numbered' ),
			)
		);

		$this->add_control(
			'mid_size',
			array(
				'label'     => __( 'Adjacent Pages', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 6,
				'default'   => 2,
				'condition' => array( 'pagination_type' => 'numbered' ),
			)
		);

		$this->add_responsive_control(
			'pagination_align',
			array(
				'label'                => __( 'Alignment', 'elementor-animatepro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'center',
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
					'{{WRAPPER}} .eap-posts__pagination' => 'justify-content: {{VALUE}};',
				),
				'condition'            => array( 'pagination_type!' => 'none' ),
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
					'{{WRAPPER}} .eap-posts__grid' => 'column-gap: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .eap-posts__grid' => 'row-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

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
				'description' => __( 'Fixed image height for uniform cards (drives the card height in Overlay).', 'elementor-animatepro' ),
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

	protected function register_badge_style() {
		$this->start_controls_section(
			'section_badge_style',
			array(
				'label'     => __( 'Badge', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_badge' => 'yes' ),
			)
		);

		$this->add_control(
			'badge_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__badge' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__badge' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .eap-posts__badge',
			)
		);

		$this->add_responsive_control(
			'badge_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 4, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__badge' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
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

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__title' => 'margin: 0 0 {{SIZE}}{{UNIT}};',
				),
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

		$this->add_responsive_control(
			'meta_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__meta' => 'margin: 0 0 {{SIZE}}{{UNIT}};',
				),
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

		$this->add_responsive_control(
			'excerpt_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 16, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__excerpt' => 'margin: 0 0 {{SIZE}}{{UNIT}};',
				),
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

		$this->start_controls_tabs( 'readmore_tabs' );

		$this->start_controls_tab( 'readmore_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

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
			'readmore_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__readmore' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'readmore_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'readmore_color_hover',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__readmore:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'readmore_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__readmore:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'readmore_typography',
				'selector'  => '{{WRAPPER}} .eap-posts__readmore',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'readmore_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'readmore_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__readmore' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_pagination_style() {
		$this->start_controls_section(
			'section_pagination_style',
			array(
				'label'     => __( 'Pagination', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'pagination_type!' => 'none' ),
			)
		);

		$this->add_responsive_control(
			'pagination_spacing',
			array(
				'label'      => __( 'Top Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'size' => 36, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#374151',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__pagination .page-numbers, {{WRAPPER}} .eap-posts__load-more' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f3f4f6',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__pagination .page-numbers, {{WRAPPER}} .eap-posts__load-more' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_active_bg',
			array(
				'label'     => __( 'Active / Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__pagination .page-numbers.current, {{WRAPPER}} .eap-posts__pagination a.page-numbers:hover, {{WRAPPER}} .eap-posts__load-more:hover' => 'background-color: {{VALUE}}; color: #fff; border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pagination_typography',
				'selector'  => '{{WRAPPER}} .eap-posts__pagination .page-numbers, {{WRAPPER}} .eap-posts__load-more',
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
			'source'          => ! empty( $settings['query_source'] ) ? $settings['query_source'] : 'latest',
			'post_type'       => ! empty( $settings['post_type'] ) ? $settings['post_type'] : 'post',
			'per_page'        => isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 6,
			'orderby'         => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date',
			'order'           => ! empty( $settings['order'] ) ? $settings['order'] : 'DESC',
			'offset'          => isset( $settings['offset'] ) ? (int) $settings['offset'] : 0,
			'include_terms'   => isset( $settings['include_terms'] ) ? $settings['include_terms'] : array(),
			'exclude_terms'   => isset( $settings['exclude_terms'] ) ? $settings['exclude_terms'] : array(),
			'manual_ids'      => isset( $settings['manual_posts'] ) ? $settings['manual_posts'] : array(),
			'exclude_current' => 'yes' === ( $settings['exclude_current'] ?? '' ),
			'current_id'      => $current,
			'ignore_sticky'   => 'yes' === ( $settings['ignore_sticky'] ?? '' ),
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
			'excerpt_length' => isset( $settings['excerpt_length'] ) ? (int) $settings['excerpt_length'] : 20,
			'meta'           => isset( $settings['meta_data'] ) ? $settings['meta_data'] : array( 'date', 'comments' ),
			'image_size'     => ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'medium_large',
			'readmore_text'  => isset( $settings['readmore_text'] ) ? $settings['readmore_text'] : __( 'Read More', 'elementor-animatepro' ),
		);

		// Effective pagination: manual has none; current cannot AJAX.
		$pagination = ! empty( $settings['pagination_type'] ) ? $settings['pagination_type'] : 'none';
		if ( 'manual' === $spec['source'] ) {
			$pagination = 'none';
		} elseif ( 'current' === $spec['source'] && in_array( $pagination, array( 'load_more', 'infinite' ), true ) ) {
			$pagination = 'numbered';
		}

		$paged = $this->get_paged();

		// Resolve the query.
		$use_current = ( 'current' === $spec['source'] && ! $editor );
		if ( $use_current ) {
			global $wp_query;
			$query = $wp_query;
			if ( ! ( $query instanceof WP_Query ) || empty( $query->posts ) ) {
				$use_current = false;
			}
		}

		if ( ! $use_current ) {
			$args  = EAP_Posts_Query::build_query_args( $spec, $paged );
			$query = new WP_Query( $args );
		} else {
			$paged = max( 1, (int) $query->get( 'paged' ) );
		}

		if ( ! $query->have_posts() ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-posts eap-posts--empty">' . esc_html__( 'No posts found for this query.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$this->render_wrapper( $settings, $spec, $display, $pagination, $query, $paged );
	}

	/**
	 * Output the widget wrapper, grid and pagination.
	 *
	 * @param array    $settings   Settings.
	 * @param array    $spec       Query spec.
	 * @param array    $display    Display spec.
	 * @param string   $pagination Effective pagination type.
	 * @param WP_Query $query      Query.
	 * @param int      $paged      Current page.
	 * @return void
	 */
	protected function render_wrapper( $settings, $spec, $display, $pagination, $query, $paged ) {
		$zoom    = 'yes' === ( $settings['image_hover_zoom'] ?? 'yes' );
		$classes = array(
			'eap-widget',
			'eap-posts',
			'eap-posts--' . $display['layout'],
			'eap-posts--img-' . $display['image_position'],
			'eap-posts--pag-' . $pagination,
		);
		if ( $zoom ) {
			$classes[] = 'eap-posts--zoom';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		$is_ajax = in_array( $pagination, array( 'load_more', 'infinite' ), true );
		if ( $is_ajax ) {
			$this->add_render_attribute(
				'wrapper',
				array(
					'data-eap-posts' => '',
					'data-mode'      => $pagination,
					'data-page'      => (string) $paged,
					'data-max'       => (string) max( 1, (int) $query->max_num_pages ),
					'data-nonce'     => wp_create_nonce( 'eap-load-posts' ),
					'data-ajax-url'  => esc_url( admin_url( 'admin-ajax.php' ) ),
					'data-spec'      => wp_json_encode( $spec ),
					'data-display'   => wp_json_encode( $display ),
				)
			);
		}
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="eap-posts__grid">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built by EAP_Posts_Query with escaped helpers.
				echo EAP_Posts_Query::render_cards( $query, $display );
				?>
			</div>
			<?php $this->render_pagination( $settings, $pagination, $query, $paged ); ?>
		</div>
		<?php
		wp_reset_postdata();
	}

	/**
	 * Render the pagination control for the chosen mode.
	 *
	 * @param array    $settings   Settings.
	 * @param string   $pagination Mode.
	 * @param WP_Query $query      Query.
	 * @param int      $paged      Current page.
	 * @return void
	 */
	protected function render_pagination( $settings, $pagination, $query, $paged ) {
		$max = max( 1, (int) $query->max_num_pages );

		if ( 'numbered' === $pagination && $max > 1 ) {
			$prev  = ! empty( $settings['num_prev_text'] ) ? $settings['num_prev_text'] : __( '‹ Prev', 'elementor-animatepro' );
			$next  = ! empty( $settings['num_next_text'] ) ? $settings['num_next_text'] : __( 'Next ›', 'elementor-animatepro' );
			$mid   = isset( $settings['mid_size'] ) && '' !== $settings['mid_size'] ? (int) $settings['mid_size'] : 2;
			$big   = 999999999;
			$links = paginate_links(
				array(
					'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format'    => '?paged=%#%',
					'current'   => max( 1, $paged ),
					'total'     => $max,
					'prev_text' => $prev,
					'next_text' => $next,
					'mid_size'  => $mid,
					'type'      => 'array',
				)
			);

			if ( ! empty( $links ) ) {
				echo '<div class="eap-posts__pagination eap-posts__pagination--numbered">';
				foreach ( $links as $link ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core paginate_links() markup.
					echo $link;
				}
				echo '</div>';
			}
			return;
		}

		if ( 'load_more' === $pagination ) {
			$text     = ! empty( $settings['load_more_text'] ) ? $settings['load_more_text'] : __( 'Load More', 'elementor-animatepro' );
			$disabled = $paged >= $max ? ' hidden' : '';
			echo '<div class="eap-posts__pagination eap-posts__pagination--load-more">';
			printf(
				'<button type="button" class="eap-posts__load-more%s">%s</button>',
				esc_attr( $disabled ),
				esc_html( $text )
			);
			echo '</div>';
			return;
		}

		if ( 'infinite' === $pagination ) {
			$hidden = $paged >= $max ? ' hidden' : '';
			echo '<div class="eap-posts__pagination eap-posts__pagination--infinite">';
			echo '<div class="eap-posts__sentinel' . esc_attr( $hidden ) . '" aria-hidden="true"></div>';
			echo '<div class="eap-posts__spinner" aria-hidden="true"></div>';
			echo '</div>';
		}
	}

	/**
	 * Current page number from the URL (main query paged/page vars).
	 *
	 * @return int
	 */
	protected function get_paged() {
		$paged = (int) get_query_var( 'paged' );
		if ( ! $paged ) {
			$paged = (int) get_query_var( 'page' );
		}
		return max( 1, $paged );
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
