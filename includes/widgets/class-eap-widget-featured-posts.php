<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * Featured Posts widget.
 *
 * An editorial hero block: one (or two) lead stories given real visual weight,
 * with the rest as supporting items.
 *
 * How this differs from Advanced Posts' "Featured" LAYOUT, which also enlarges a
 * first card: that one is a single full-width card followed by a uniform grid.
 * This widget is about the ARRANGEMENTS that layout cannot express — a hero
 * beside a stacked side list, two equal co-leads, or a mosaic — and about the
 * SOURCE: it can pull WordPress **sticky** posts or posts carrying a meta flag,
 * which is what "featured" usually means editorially and which no other widget
 * here can query.
 *
 * Both the hero and the supporting items are rendered by
 * EAP_Posts_Query::render_card() and styled by posts.css — they simply get
 * DIFFERENT display specs, which is what lets the hero show an excerpt while the
 * side list stays compact, with no duplicated card markup. Each section carries
 * its own `.eap-posts--{layout}` modifier so posts.css styles them independently
 * within the one widget.
 *
 * CSS only — no JavaScript.
 */
class EAP_Widget_Featured_Posts extends EAP_Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-featured-posts';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Featured Posts', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-post-list';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'featured', 'posts', 'hero', 'sticky', 'editorial', 'magazine', 'mosaic' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-posts', 'eap-featured-posts' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_query_section();
		$this->register_layout_section();
		$this->register_hero_content_section();
		$this->register_side_content_section();

		$this->register_block_style();
		$this->register_hero_style();
		$this->register_side_style();
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
			'source',
			array(
				'label'   => __( 'Source', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'sticky',
				'options' => array(
					'sticky' => __( 'Sticky Posts', 'elementor-animatepro' ),
					'meta'   => __( 'Meta Flag', 'elementor-animatepro' ),
					'manual' => __( 'Hand-picked', 'elementor-animatepro' ),
					'latest' => __( 'Latest', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'sticky_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Uses the posts you marked "Stick to the top of the blog". Falls back to the latest posts when none are sticky, so the block is never empty.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'source' => 'sticky' ),
			)
		);

		$this->add_control(
			'meta_key',
			array(
				'label'       => __( 'Meta Key', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'featured',
				'placeholder' => 'featured',
				'description' => __( 'Posts where this custom field exists and is truthy. Pairs with an ACF true/false field named the same.', 'elementor-animatepro' ),
				'condition'   => array( 'source' => 'meta' ),
			)
		);

		$this->add_control(
			'manual_ids',
			array(
				'label'       => __( 'Posts', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => EAP_Posts_Query::get_post_options(),
				'label_block' => true,
				'description' => __( 'The first one becomes the hero.', 'elementor-animatepro' ),
				'condition'   => array( 'source' => 'manual' ),
			)
		);

		$this->add_control(
			'post_type',
			array(
				'label'     => __( 'Post Type', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'post',
				'options'   => EAP_Posts_Query::get_post_type_options(),
				'condition' => array( 'source' => array( 'latest', 'meta' ) ),
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
					'title'         => __( 'Title', 'elementor-animatepro' ),
					'rand'          => __( 'Random', 'elementor-animatepro' ),
					'modified'      => __( 'Last Modified', 'elementor-animatepro' ),
					'comment_count' => __( 'Comment Count', 'elementor-animatepro' ),
				),
				'condition' => array( 'source' => array( 'latest', 'meta' ) ),
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
				'condition' => array( 'source' => array( 'latest', 'meta' ) ),
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
				'condition'   => array( 'source' => array( 'latest', 'meta' ) ),
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
			'arrangement',
			array(
				'label'       => __( 'Arrangement', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'hero-list',
				'options'     => array(
					'hero-list' => __( 'Hero + Side List', 'elementor-animatepro' ),
					'duo'       => __( 'Two Co-Leads', 'elementor-animatepro' ),
					'mosaic'    => __( 'Mosaic', 'elementor-animatepro' ),
				),
				'description' => __( 'For a wide lead card above a uniform grid, use the Advanced Posts widget with its Featured layout instead.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'hero_side',
			array(
				'label'     => __( 'Hero Side', 'elementor-animatepro' ),
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
				'condition' => array( 'arrangement' => array( 'hero-list', 'mosaic' ) ),
			)
		);

		$this->add_responsive_control(
			'hero_width',
			array(
				'label'      => __( 'Hero Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'fr' ),
				'range'      => array( 'fr' => array( 'min' => 1, 'max' => 3, 'step' => 0.1 ) ),
				'default'    => array( 'size' => 1.6, 'unit' => 'fr' ),
				'selectors'  => array(
					'{{WRAPPER}}' => '--eap-fp-hero-w: {{SIZE}}fr;',
				),
				'condition'  => array( 'arrangement' => array( 'hero-list', 'mosaic' ) ),
			)
		);

		$this->add_control(
			'side_count',
			array(
				'label'     => __( 'Supporting Posts', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4,
				'min'       => 1,
				'max'       => 8,
				'condition' => array( 'arrangement' => array( 'hero-list', 'mosaic' ) ),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}' => '--eap-fp-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hero_style',
			array(
				'label'     => __( 'Hero Style', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'overlay',
				'options'   => array(
					'overlay' => __( 'Overlay', 'elementor-animatepro' ),
					'card'    => __( 'Card', 'elementor-animatepro' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'side_style',
			array(
				'label'     => __( 'Supporting Style', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'list',
				'options'   => array(
					'list' => __( 'List (image beside text)', 'elementor-animatepro' ),
					'grid' => __( 'Card', 'elementor-animatepro' ),
				),
				'condition' => array( 'arrangement' => array( 'hero-list', 'mosaic' ) ),
			)
		);

		$this->add_control(
			'image_hover_zoom',
			array(
				'label'        => __( 'Zoom Image On Hover', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Hero elements.
	 *
	 * @return void
	 */
	protected function register_hero_content_section() {
		$this->start_controls_section(
			'section_hero_content',
			array( 'label' => __( 'Hero Elements', 'elementor-animatepro' ) )
		);

		$this->add_element_toggles( 'hero', true );

		$this->add_control(
			'hero_title_tag',
			array(
				'label'   => __( 'Title Tag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => $this->get_tag_options(),
			)
		);

		$this->add_control(
			'hero_excerpt_length',
			array(
				'label'     => __( 'Excerpt Words', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 24,
				'min'       => 4,
				'max'       => 100,
				'condition' => array( 'hero_show_excerpt' => 'yes' ),
			)
		);

		$this->add_control(
			'hero_image_size',
			array(
				'label'   => __( 'Image Size', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'large',
				'options' => $this->get_image_size_options(),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Supporting-item elements.
	 *
	 * @return void
	 */
	protected function register_side_content_section() {
		$this->start_controls_section(
			'section_side_content',
			array(
				'label'     => __( 'Supporting Elements', 'elementor-animatepro' ),
				'condition' => array( 'arrangement' => array( 'hero-list', 'mosaic' ) ),
			)
		);

		$this->add_element_toggles( 'side', false );

		$this->add_control(
			'side_title_tag',
			array(
				'label'   => __( 'Title Tag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h4',
				'options' => $this->get_tag_options(),
			)
		);

		$this->add_control(
			'side_excerpt_length',
			array(
				'label'     => __( 'Excerpt Words', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 12,
				'min'       => 4,
				'max'       => 60,
				'condition' => array( 'side_show_excerpt' => 'yes' ),
			)
		);

		$this->add_control(
			'side_image_size',
			array(
				'label'   => __( 'Image Size', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'medium',
				'options' => $this->get_image_size_options(),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * The per-section element toggles.
	 *
	 * Declared once and called for both sections, so hero and supporting items
	 * cannot drift apart — the only difference is which are on by default.
	 *
	 * @param string $prefix    'hero' | 'side'.
	 * @param bool   $rich      Whether excerpt and read-more default to on.
	 * @return void
	 */
	protected function add_element_toggles( $prefix, $rich ) {
		$elements = array(
			'show_image'    => array( __( 'Image', 'elementor-animatepro' ), true ),
			'show_badge'    => array( __( 'Category Badge', 'elementor-animatepro' ), true ),
			'show_title'    => array( __( 'Title', 'elementor-animatepro' ), true ),
			'show_meta'     => array( __( 'Meta', 'elementor-animatepro' ), true ),
			'show_excerpt'  => array( __( 'Excerpt', 'elementor-animatepro' ), $rich ),
			'show_readmore' => array( __( 'Read More', 'elementor-animatepro' ), $rich ),
		);

		foreach ( $elements as $key => $spec ) {
			$this->add_control(
				$prefix . '_' . $key,
				array(
					'label'        => $spec[0],
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => $spec[1] ? 'yes' : '',
				)
			);
		}

		$this->add_control(
			$prefix . '_meta_data',
			array(
				'label'     => __( 'Meta', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT2,
				'multiple'  => true,
				'default'   => array( 'date' ),
				'options'   => array(
					'author'   => __( 'Author', 'elementor-animatepro' ),
					'date'     => __( 'Date', 'elementor-animatepro' ),
					'comments' => __( 'Comments', 'elementor-animatepro' ),
				),
				'condition' => array( $prefix . '_show_meta' => 'yes' ),
			)
		);
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	/**
	 * Block style.
	 *
	 * @return void
	 */
	protected function register_block_style() {
		$this->start_controls_section(
			'style_block',
			array(
				'label' => __( 'Block', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'stack_below',
			array(
				'label'       => __( 'Stack Below', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 1200 ) ),
				'default'     => array( 'size' => 782, 'unit' => 'px' ),
				'selectors'   => array(
					'{{WRAPPER}}' => '--eap-fp-stack: {{SIZE}}{{UNIT}};',
				),
				'description' => __( 'Below this width the hero and supporting items stack into one column.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Card Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__item' => 'background-color: {{VALUE}};',
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
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
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
	 * Hero style.
	 *
	 * @return void
	 */
	protected function register_hero_style() {
		$this->start_controls_section(
			'style_hero',
			array(
				'label' => __( 'Hero', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'hero_min_height',
			array(
				'label'      => __( 'Minimum Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 160, 'max' => 800 ) ),
				'default'    => array( 'size' => 420, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}' => '--eap-fp-hero-h: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hero_scrim',
			array(
				'label'     => __( 'Scrim Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(17, 24, 39, 0.72)',
				'selectors' => array(
					'{{WRAPPER}}' => '--eap-fp-scrim: {{VALUE}};',
				),
				'condition' => array( 'hero_style' => 'overlay' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'hero_title_typography',
				'label'    => __( 'Title Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-featured-posts__hero .eap-posts__title',
			)
		);

		/*
		 * NO default: in Overlay style posts.css makes the title white over the
		 * scrim, and a control default would emit {{WRAPPER}}-scoped CSS that beats
		 * it and render dark-on-dark.
		 */
		$this->add_control(
			'hero_title_color',
			array(
				'label'     => __( 'Title Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-featured-posts__hero .eap-posts__title, {{WRAPPER}} .eap-featured-posts__hero .eap-posts__title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hero_excerpt_color',
			array(
				'label'     => __( 'Excerpt Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-featured-posts__hero .eap-posts__excerpt' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hero_meta_color',
			array(
				'label'     => __( 'Meta Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-featured-posts__hero .eap-posts__meta' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Supporting-item style.
	 *
	 * @return void
	 */
	protected function register_side_style() {
		$this->start_controls_section(
			'style_side',
			array(
				'label'     => __( 'Supporting Items', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'arrangement' => array( 'hero-list', 'mosaic' ) ),
			)
		);

		$this->add_responsive_control(
			'side_image_width',
			array(
				'label'      => __( 'List Image Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array( '%' => array( 'min' => 15, 'max' => 60 ) ),
				'default'    => array( 'size' => 38, 'unit' => '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-featured-posts__side' => '--eap-posts-list-img: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'side_style' => 'list' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'side_title_typography',
				'label'    => __( 'Title Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-featured-posts__side .eap-posts__title',
			)
		);

		$this->add_control(
			'side_title_color',
			array(
				'label'     => __( 'Title Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-featured-posts__side .eap-posts__title, {{WRAPPER}} .eap-featured-posts__side .eap-posts__title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'side_meta_color',
			array(
				'label'     => __( 'Meta Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-featured-posts__side .eap-posts__meta' => 'color: {{VALUE}};',
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

		$arrangement = in_array( $settings['arrangement'] ?? 'hero-list', array( 'hero-list', 'duo', 'mosaic' ), true )
			? $settings['arrangement']
			: 'hero-list';

		$hero_count = ( 'duo' === $arrangement ) ? 2 : 1;
		$side_count = ( 'duo' === $arrangement ) ? 0 : ( isset( $settings['side_count'] ) ? max( 1, (int) $settings['side_count'] ) : 4 );

		$ids = $this->resolve_ids( $settings, $hero_count + $side_count );

		if ( empty( $ids ) ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-featured-posts eap-featured-posts--empty">' . esc_html__( 'No posts found for this source.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$hero_ids = array_slice( $ids, 0, $hero_count );
		$side_ids = array_slice( $ids, $hero_count, $side_count );

		$hero_style = ( 'card' === ( $settings['hero_style'] ?? 'overlay' ) ) ? 'grid' : 'overlay';
		$side_style = ( 'grid' === ( $settings['side_style'] ?? 'list' ) ) ? 'grid' : 'list';

		$hero_display = $this->build_display( $settings, 'hero', $hero_style );
		$side_display = $this->build_display( $settings, 'side', $side_style );

		$zoom    = 'yes' === ( $settings['image_hover_zoom'] ?? 'yes' );
		$classes = array(
			'eap-widget',
			'eap-featured-posts',
			'eap-featured-posts--' . $arrangement,
			'eap-featured-posts--hero-' . ( 'right' === ( $settings['hero_side'] ?? 'left' ) ? 'right' : 'left' ),
		);

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		// Each section carries its own posts.css layout modifier, so one widget can
		// hold an overlay hero and a list of supporting items at the same time.
		$hero_classes = 'eap-featured-posts__hero eap-posts eap-posts--' . $hero_style . ( $zoom ? ' eap-posts--zoom' : '' );
		$side_classes = 'eap-featured-posts__side eap-posts eap-posts--' . $side_style . ( $zoom ? ' eap-posts--zoom' : '' );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="<?php echo esc_attr( $hero_classes ); ?>">
				<div class="eap-posts__grid">
					<?php
					foreach ( $hero_ids as $pid ) {
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built by EAP_Posts_Query with escaped helpers.
						echo EAP_Posts_Query::render_card( $pid, $hero_display );
					}
					?>
				</div>
			</div>

			<?php if ( ! empty( $side_ids ) ) : ?>
				<div class="<?php echo esc_attr( $side_classes ); ?>">
					<div class="eap-posts__grid">
						<?php
						foreach ( $side_ids as $pid ) {
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built by EAP_Posts_Query with escaped helpers.
							echo EAP_Posts_Query::render_card( $pid, $side_display );
						}
						?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Resolve the post IDs for the chosen source.
	 *
	 * Sticky and Meta both resolve to an ID list and then go through the shared
	 * `manual` path, which is publish-only — so neither can surface a draft or
	 * private post.
	 *
	 * @param array $settings Settings.
	 * @param int   $needed   How many posts the arrangement needs.
	 * @return int[]
	 */
	protected function resolve_ids( $settings, $needed ) {
		$source  = $settings['source'] ?? 'sticky';
		$current = $this->eap_get_post_id();
		$exclude = ( 'yes' === ( $settings['exclude_current'] ?? '' ) ) ? $current : 0;

		$spec = array(
			'source'          => 'latest',
			'post_type'       => ! empty( $settings['post_type'] ) ? $settings['post_type'] : 'post',
			'per_page'        => $needed,
			'orderby'         => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date',
			'order'           => ! empty( $settings['order'] ) ? $settings['order'] : 'DESC',
			'include_terms'   => isset( $settings['include_terms'] ) ? $settings['include_terms'] : array(),
			'exclude_current' => (bool) $exclude,
			'current_id'      => $current,
			'ignore_sticky'   => true,
		);

		if ( 'manual' === $source ) {
			$ids = array_map( 'intval', (array) ( $settings['manual_ids'] ?? array() ) );
			return $this->fetch_by_ids( $ids, $needed, $exclude );
		}

		if ( 'sticky' === $source ) {
			$ids = array_map( 'intval', (array) get_option( 'sticky_posts', array() ) );
			$out = $this->fetch_by_ids( $ids, $needed, $exclude );

			// A site with no sticky posts would otherwise render an empty block on
			// the front end, which reads as a broken widget rather than a choice.
			if ( ! empty( $out ) ) {
				return $out;
			}
		}

		if ( 'meta' === $source ) {
			$key = trim( (string) ( $settings['meta_key'] ?? 'featured' ) );
			if ( '' !== $key ) {
				$found = get_posts(
					array(
						'post_type'      => $spec['post_type'],
						'post_status'    => 'publish',
						'posts_per_page' => $needed,
						'fields'         => 'ids',
						'orderby'        => $spec['orderby'],
						'order'          => $spec['order'],
						'no_found_rows'  => true,
						'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
							array(
								'key'     => $key,
								'value'   => array( '', '0', 'false' ),
								'compare' => 'NOT IN',
							),
						),
					)
				);

				$found = array_diff( array_map( 'intval', $found ), array( $exclude ) );
				if ( ! empty( $found ) ) {
					return array_values( $found );
				}
			}
		}

		$query = new WP_Query( EAP_Posts_Query::build_query_args( $spec, 1 ) );
		$ids   = wp_list_pluck( $query->posts, 'ID' );
		wp_reset_postdata();

		return array_map( 'intval', $ids );
	}

	/**
	 * Fetch a specific ID list, published only, keeping the given order.
	 *
	 * @param int[] $ids     Candidate IDs.
	 * @param int   $needed  Max wanted.
	 * @param int   $exclude ID to drop, or 0.
	 * @return int[]
	 */
	protected function fetch_by_ids( $ids, $needed, $exclude ) {
		$ids = array_values( array_filter( array_map( 'intval', $ids ) ) );
		if ( $exclude ) {
			$ids = array_values( array_diff( $ids, array( $exclude ) ) );
		}
		if ( empty( $ids ) ) {
			return array();
		}

		$query = new WP_Query(
			EAP_Posts_Query::build_query_args(
				array(
					'source'     => 'manual',
					'manual_ids' => $ids,
					'per_page'   => $needed,
				),
				1
			)
		);
		$found = array_map( 'intval', wp_list_pluck( $query->posts, 'ID' ) );
		wp_reset_postdata();

		return $found;
	}

	/**
	 * Build one section's display spec.
	 *
	 * @param array  $settings Settings.
	 * @param string $prefix   'hero' | 'side'.
	 * @param string $layout   posts.css layout modifier.
	 * @return array
	 */
	protected function build_display( $settings, $prefix, $layout ) {
		$p = $prefix . '_';

		return array(
			'layout'         => $layout,
			'image_position' => 'left',
			'show_image'     => 'yes' === ( $settings[ $p . 'show_image' ] ?? 'yes' ),
			'show_badge'     => 'yes' === ( $settings[ $p . 'show_badge' ] ?? 'yes' ),
			'show_title'     => 'yes' === ( $settings[ $p . 'show_title' ] ?? 'yes' ),
			'show_meta'      => 'yes' === ( $settings[ $p . 'show_meta' ] ?? 'yes' ),
			'show_excerpt'   => 'yes' === ( $settings[ $p . 'show_excerpt' ] ?? '' ),
			'show_readmore'  => 'yes' === ( $settings[ $p . 'show_readmore' ] ?? '' ),
			'title_tag'      => ! empty( $settings[ $p . 'title_tag' ] ) ? $settings[ $p . 'title_tag' ] : ( 'hero' === $prefix ? 'h2' : 'h4' ),
			'excerpt_length' => isset( $settings[ $p . 'excerpt_length' ] ) ? (int) $settings[ $p . 'excerpt_length' ] : 20,
			'meta'           => isset( $settings[ $p . 'meta_data' ] ) ? $settings[ $p . 'meta_data' ] : array( 'date' ),
			'image_size'     => ! empty( $settings[ $p . 'image_size' ] ) ? $settings[ $p . 'image_size' ] : 'medium_large',
			'readmore_text'  => __( 'Read More', 'elementor-animatepro' ),
		);
	}

	/**
	 * Title tag options.
	 *
	 * @return array<string, string>
	 */
	protected function get_tag_options() {
		return array(
			'h1'   => 'H1',
			'h2'   => 'H2',
			'h3'   => 'H3',
			'h4'   => 'H4',
			'h5'   => 'H5',
			'h6'   => 'H6',
			'div'  => 'div',
			'span' => 'span',
		);
	}

	/**
	 * Registered image sizes.
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
