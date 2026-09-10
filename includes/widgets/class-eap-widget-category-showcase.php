<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * Category Showcase widget.
 *
 * Taxonomy terms laid out as a showcase grid — the static counterpart to
 * Category Slider, which puts the same terms in a carousel. Both resolve term
 * images through EAP_Widget_Base::eap_get_term_image(), so a site that has its
 * category images configured for one gets them in the other for free.
 *
 * Three layouts: Grid, Masonry (CSS columns) and Featured, where the first term
 * spans the full width as a lead tile.
 *
 * CSS only — no JavaScript.
 */
class EAP_Widget_Category_Showcase extends EAP_Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-category-showcase';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Category Showcase', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'category', 'categories', 'taxonomy', 'terms', 'showcase', 'grid', 'masonry' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-category-showcase' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_query_section();
		$this->register_layout_section();

		$this->register_grid_style();
		$this->register_card_style();
		$this->register_image_style();
		$this->register_name_style();
		$this->register_count_style();
		$this->register_description_style();
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
				'label'   => __( 'Number Of Terms', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 8,
				'min'     => 1,
				'max'     => 60,
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
				'label'        => __( 'Hide Empty Terms', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'top_level_only',
			array(
				'label'        => __( 'Top Level Only', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Skip child terms and show only top-level ones.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'include_terms',
			array(
				'label'       => __( 'Include Only', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => EAP_Posts_Query::get_term_options(),
				'label_block' => true,
			)
		);

		$this->add_control(
			'exclude_terms',
			array(
				'label'       => __( 'Exclude', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => EAP_Posts_Query::get_term_options(),
				'label_block' => true,
			)
		);

		/* ---------- Image resolution ---------- */

		$this->add_control(
			'image_heading',
			array(
				'label'     => __( 'Term Image', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'image_meta_key',
			array(
				'label'       => __( 'Image Meta Key', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'category_image',
				'description' => __( 'A term-meta field holding an attachment ID or URL — for example an ACF image field on the term.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'auto_image',
			array(
				'label'        => __( 'Use Newest Post Image', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'When a term has no image of its own, borrow the featured image of its newest post. Costs one query per term.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'fallback_image',
			array(
				'label' => __( 'Fallback Image', 'elementor-animatepro' ),
				'type'  => Controls_Manager::MEDIA,
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
					'grid'     => __( 'Grid', 'elementor-animatepro' ),
					'masonry'  => __( 'Masonry', 'elementor-animatepro' ),
					'featured' => __( 'Featured', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'card_style',
			array(
				'label'   => __( 'Card Style', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'overlay',
				'options' => array(
					'overlay' => __( 'Overlay', 'elementor-animatepro' ),
					'card'    => __( 'Card', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'elementor-animatepro' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'selectors'      => array(
					'{{WRAPPER}}' => '--eap-cs-cols: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 20, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}' => '--eap-cs-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'show_count',
			array(
				'label'        => __( 'Show Post Count', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
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
			'show_description',
			array(
				'label'        => __( 'Show Description', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'description_length',
			array(
				'label'     => __( 'Description Words', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 14,
				'min'       => 3,
				'max'       => 60,
				'condition' => array( 'show_description' => 'yes' ),
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

		$this->add_control(
			'hover_effect',
			array(
				'label'     => __( 'Hover Effect', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'zoom',
				'options'   => array(
					'none' => __( 'None', 'elementor-animatepro' ),
					'zoom' => __( 'Zoom Image', 'elementor-animatepro' ),
					'lift' => __( 'Lift Card', 'elementor-animatepro' ),
					'both' => __( 'Zoom + Lift', 'elementor-animatepro' ),
				),
				'separator' => 'before',
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

		$this->add_responsive_control(
			'item_height',
			array(
				'label'      => __( 'Tile Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 100, 'max' => 600 ) ),
				'default'    => array( 'size' => 220, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}' => '--eap-cs-h: {{SIZE}}{{UNIT}};',
				),
				'description' => __( 'Ignored in Masonry, where tiles size to their image.', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'featured_height',
			array(
				'label'      => __( 'Featured Tile Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 140, 'max' => 800 ) ),
				'default'    => array( 'size' => 340, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}' => '--eap-cs-h-featured: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'layout' => 'featured' ),
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
					'{{WRAPPER}} .eap-category-showcase__item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 48 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}' => '--eap-cs-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .eap-category-showcase__item',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eap-category-showcase__item',
			)
		);

		$this->add_control(
			'scrim_heading',
			array(
				'label'     => __( 'Overlay Scrim', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'card_style' => 'overlay' ),
			)
		);

		$this->add_control(
			'scrim_color',
			array(
				'label'     => __( 'Scrim Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(17, 24, 39, 0.55)',
				'selectors' => array(
					'{{WRAPPER}}' => '--eap-cs-scrim: {{VALUE}};',
				),
				'condition' => array( 'card_style' => 'overlay' ),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Content Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 18,
					'right'    => 18,
					'bottom'   => 18,
					'left'     => 18,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-category-showcase__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'content_align',
			array(
				'label'     => __( 'Content Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
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
				'selectors_dictionary' => array(
					'flex-start' => 'align-items: flex-start; text-align: left;',
					'center'     => 'align-items: center; text-align: center;',
					'flex-end'   => 'align-items: flex-end; text-align: right;',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-category-showcase__content' => '{{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Image style.
	 *
	 * @return void
	 */
	protected function register_image_style() {
		$this->start_controls_section(
			'style_image',
			array(
				'label' => __( 'Image', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'image_fit',
			array(
				'label'     => __( 'Object Fit', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => array(
					'cover'   => __( 'Cover', 'elementor-animatepro' ),
					'contain' => __( 'Contain', 'elementor-animatepro' ),
					'fill'    => __( 'Fill', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-category-showcase__img' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'image_brightness',
			array(
				'label'     => __( 'Brightness', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0.2, 'max' => 1.4, 'step' => 0.05 ) ),
				'default'   => array( 'size' => 1 ),
				'selectors' => array(
					'{{WRAPPER}} .eap-category-showcase__img' => 'filter: brightness({{SIZE}});',
				),
			)
		);

		$this->add_control(
			'placeholder_from',
			array(
				'label'     => __( 'Placeholder Gradient From', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6366f1',
				'selectors' => array(
					'{{WRAPPER}}' => '--eap-cs-ph-from: {{VALUE}};',
				),
				'description' => __( 'Used when a term resolves no image at all.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'placeholder_to',
			array(
				'label'     => __( 'Placeholder Gradient To', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0ea5e9',
				'selectors' => array(
					'{{WRAPPER}}' => '--eap-cs-ph-to: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Name style.
	 *
	 * @return void
	 */
	protected function register_name_style() {
		$this->start_controls_section(
			'style_name',
			array(
				'label' => __( 'Name', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .eap-category-showcase__name',
			)
		);

		/*
		 * NO default. In Overlay style the name sits on a dark scrim and the
		 * stylesheet makes it white; a hardcoded default here would emit
		 * {{WRAPPER}}-scoped CSS that beats that rule and render dark-on-dark.
		 * (The same trap that bit Category Slider and the Posts widget.)
		 */
		$this->add_control(
			'name_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-category-showcase__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'name_color_hover',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-category-showcase__item:hover .eap-category-showcase__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Count style.
	 *
	 * @return void
	 */
	protected function register_count_style() {
		$this->start_controls_section(
			'style_count',
			array(
				'label'     => __( 'Post Count', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_count' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'count_typography',
				'selector' => '{{WRAPPER}} .eap-category-showcase__count',
			)
		);

		$this->add_control(
			'count_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-category-showcase__count' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'count_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-category-showcase__count' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Description style.
	 *
	 * @return void
	 */
	protected function register_description_style() {
		$this->start_controls_section(
			'style_description',
			array(
				'label'     => __( 'Description', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_description' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .eap-category-showcase__desc',
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-category-showcase__desc' => 'color: {{VALUE}};',
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

		$taxonomy = ! empty( $settings['taxonomy'] ) ? $settings['taxonomy'] : 'category';
		if ( ! taxonomy_exists( $taxonomy ) ) {
			$taxonomy = 'category';
		}

		$args = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => 'yes' === ( $settings['hide_empty'] ?? 'yes' ),
			'orderby'    => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'name',
			'order'      => ( 'DESC' === ( $settings['order'] ?? 'ASC' ) ) ? 'DESC' : 'ASC',
			'number'     => isset( $settings['limit'] ) ? max( 1, (int) $settings['limit'] ) : 8,
		);

		$include = $this->int_list( $settings['include_terms'] ?? array() );
		$exclude = $this->int_list( $settings['exclude_terms'] ?? array() );
		if ( $include ) {
			$args['include'] = $include;
		}
		if ( $exclude ) {
			$args['exclude'] = $exclude;
		}
		if ( 'yes' === ( $settings['top_level_only'] ?? '' ) ) {
			$args['parent'] = 0;
		}

		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-category-showcase eap-category-showcase--empty">' . esc_html__( 'No terms found for this query.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$layout     = in_array( $settings['layout'] ?? 'grid', array( 'grid', 'masonry', 'featured' ), true ) ? $settings['layout'] : 'grid';
		$card_style = ( 'card' === ( $settings['card_style'] ?? 'overlay' ) ) ? 'card' : 'overlay';
		$hover      = in_array( $settings['hover_effect'] ?? 'zoom', array( 'none', 'zoom', 'lift', 'both' ), true ) ? $settings['hover_effect'] : 'zoom';

		$show_count = 'yes' === ( $settings['show_count'] ?? 'yes' );
		$show_desc  = 'yes' === ( $settings['show_description'] ?? '' );
		$suffix     = isset( $settings['count_suffix'] ) ? (string) $settings['count_suffix'] : '';
		$name_tag   = $this->safe_tag( $settings['name_tag'] ?? 'h3' );
		$desc_words = isset( $settings['description_length'] ) ? max( 3, (int) $settings['description_length'] ) : 14;

		$img_cfg = array(
			'meta_key' => isset( $settings['image_meta_key'] ) ? trim( (string) $settings['image_meta_key'] ) : '',
			'auto'     => 'yes' === ( $settings['auto_image'] ?? 'yes' ),
			'fallback' => isset( $settings['fallback_image']['url'] ) ? $settings['fallback_image']['url'] : '',
			'size'     => ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'medium_large',
			'taxonomy' => $taxonomy,
		);

		$classes = array(
			'eap-widget',
			'eap-category-showcase',
			'eap-category-showcase--' . $layout,
			'eap-category-showcase--' . $card_style,
			'eap-category-showcase--hover-' . $hover,
		);

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="eap-category-showcase__grid">
				<?php
				$index = 0;
				foreach ( $terms as $term ) :
					$link  = get_term_link( $term );
					$link  = is_wp_error( $link ) ? '#' : $link;
					$image = $this->eap_get_term_image( $term, $img_cfg );

					$item_classes = array( 'eap-category-showcase__item' );
					if ( 'featured' === $layout && 0 === $index ) {
						$item_classes[] = 'is-featured';
					}
					if ( '' === $image ) {
						// Lets the stylesheet swap in the gradient placeholder and
						// keep the text readable without an image behind it.
						$item_classes[] = 'is-imageless';
					}
					$index++;
					?>
					<a class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>" href="<?php echo esc_url( $link ); ?>">
						<span class="eap-category-showcase__image">
							<?php if ( '' !== $image ) : ?>
								<img class="eap-category-showcase__img" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" loading="lazy" />
							<?php else : ?>
								<span class="eap-category-showcase__img eap-category-showcase__img--ph" aria-hidden="true"></span>
							<?php endif; ?>
						</span>

						<span class="eap-category-showcase__content">
							<<?php echo esc_html( $name_tag ); ?> class="eap-category-showcase__name"><?php echo esc_html( $term->name ); ?></<?php echo esc_html( $name_tag ); ?>>

							<?php if ( $show_count ) : ?>
								<span class="eap-category-showcase__count"><?php echo esc_html( trim( number_format_i18n( (int) $term->count ) . ' ' . $suffix ) ); ?></span>
							<?php endif; ?>

							<?php
							if ( $show_desc ) :
								$desc = trim( wp_strip_all_tags( (string) $term->description ) );
								if ( '' !== $desc ) :
									?>
									<span class="eap-category-showcase__desc"><?php echo esc_html( wp_trim_words( $desc, $desc_words, '…' ) ); ?></span>
									<?php
								endif;
							endif;
							?>
						</span>
					</a>
					<?php
				endforeach;
				?>
			</div>
		</div>
		<?php
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
