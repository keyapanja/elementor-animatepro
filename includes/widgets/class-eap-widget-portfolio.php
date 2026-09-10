<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * Portfolio widget.
 *
 * Image-led project tiles for the bundled `eap_portfolio` post type (or any post
 * type), with a hover overlay, an optional lightbox, and filter tabs.
 *
 * The tiles are BESPOKE rather than EAP_Posts_Query cards: a portfolio tile is
 * an image with an overlay of actions, not a card with a body, and it surfaces
 * the project fields the CPT adds (client, project URL, completed date). The
 * query still goes through EAP_Posts_Query::build_query_args() so the post-type
 * and term handling stay shared.
 *
 * Filter tabs are derived from the terms the LOADED projects actually carry, so
 * a tab can never open onto an empty grid, and the counts are exact — the same
 * approach as Filterable Posts. The reflow itself is EAPFrontend.flipFilter()
 * from core.js, shared with that widget rather than reimplemented.
 */
class EAP_Widget_Portfolio extends EAP_Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-portfolio';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Portfolio', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-masonry';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'portfolio', 'projects', 'work', 'gallery', 'case study', 'filter', 'masonry' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-portfolio' );
	}

	/**
	 * Scripts.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-portfolio-script' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_query_section();
		$this->register_layout_section();
		$this->register_content_section();

		$this->register_tile_style();
		$this->register_overlay_style();
		$this->register_filter_style();
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
				'default' => EAP_Portfolio_CPT::POST_TYPE,
				'options' => EAP_Posts_Query::get_post_type_options(),
			)
		);

		$this->add_control(
			'cpt_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'The bundled Portfolio post type is switched on under AnimatePro → Extensions.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'       => __( 'Projects To Show', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 9,
				'min'         => 1,
				'max'         => 48,
				'description' => __( 'Filtering happens in the browser, so this is the whole pool.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'       => __( 'Order By', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'menu_order',
				'options'     => array(
					'menu_order'    => __( 'Manual Order', 'elementor-animatepro' ),
					'date'          => __( 'Date', 'elementor-animatepro' ),
					'title'         => __( 'Title', 'elementor-animatepro' ),
					'rand'          => __( 'Random', 'elementor-animatepro' ),
					'modified'      => __( 'Last Modified', 'elementor-animatepro' ),
				),
				'description' => __( 'Manual Order uses the Order field on each project.', 'elementor-animatepro' ),
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
				'options'        => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5' ),
				'selectors'      => array( '{{WRAPPER}}' => '--eap-pf-cols: {{VALUE}};' ),
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
				'selectors'  => array( '{{WRAPPER}}' => '--eap-pf-gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'tile_height',
			array(
				'label'       => __( 'Tile Height', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 120, 'max' => 700 ) ),
				'default'     => array( 'size' => 280, 'unit' => 'px' ),
				'selectors'   => array( '{{WRAPPER}}' => '--eap-pf-h: {{SIZE}}{{UNIT}};' ),
				'description' => __( 'Ignored in Masonry, where tiles keep their image proportions.', 'elementor-animatepro' ),
				'condition'   => array( 'layout' => 'grid' ),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'   => __( 'Image Size', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'large',
				'options' => $this->get_image_size_options(),
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'label'     => __( 'Hover Effect', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'zoom',
				'options'   => array(
					'none'  => __( 'None', 'elementor-animatepro' ),
					'zoom'  => __( 'Zoom Image', 'elementor-animatepro' ),
					'lift'  => __( 'Lift Tile', 'elementor-animatepro' ),
					'both'  => __( 'Zoom + Lift', 'elementor-animatepro' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'overlay_reveal',
			array(
				'label'   => __( 'Overlay', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => array(
					'hover'  => __( 'Reveal On Hover', 'elementor-animatepro' ),
					'always' => __( 'Always Visible', 'elementor-animatepro' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content section.
	 *
	 * @return void
	 */
	protected function register_content_section() {
		$this->start_controls_section(
			'section_fields',
			array( 'label' => __( 'Content', 'elementor-animatepro' ) )
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
				'default'   => EAP_Portfolio_CPT::TAXONOMY,
				'options'   => $this->get_taxonomy_options(),
				'condition' => array( 'show_filters' => 'yes' ),
			)
		);

		$this->add_control(
			'all_label',
			array(
				'label'     => __( '"All" Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'All', 'elementor-animatepro' ),
				'condition' => array( 'show_filters' => 'yes' ),
			)
		);

		$this->add_control(
			'show_counts',
			array(
				'label'        => __( 'Show Counts', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'show_filters' => 'yes' ),
			)
		);

		$this->add_control(
			'fields_heading',
			array(
				'label'     => __( 'Tile', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		foreach ( array(
			'show_title'  => array( __( 'Title', 'elementor-animatepro' ), 'yes' ),
			'show_terms'  => array( __( 'Categories', 'elementor-animatepro' ), 'yes' ),
			'show_client' => array( __( 'Client', 'elementor-animatepro' ), '' ),
			'show_date'   => array( __( 'Completed Date', 'elementor-animatepro' ), '' ),
		) as $key => $spec ) {
			$this->add_control(
				$key,
				array(
					'label'        => $spec[0],
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => $spec[1],
				)
			);
		}

		$this->add_control(
			'date_format',
			array(
				'label'     => __( 'Date Format', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'F Y',
				'condition' => array( 'show_date' => 'yes' ),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'Title Tag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => array(
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'div'  => 'div',
					'span' => 'span',
				),
			)
		);

		$this->add_control(
			'actions_heading',
			array(
				'label'     => __( 'Actions', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'tile_link',
			array(
				'label'       => __( 'Tile Links To', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'permalink',
				'options'     => array(
					'permalink' => __( 'The Project Page', 'elementor-animatepro' ),
					'project'   => __( 'The Project URL', 'elementor-animatepro' ),
					'none'      => __( 'Nothing', 'elementor-animatepro' ),
				),
				'description' => __( 'Project URL comes from the Project Details box, and falls back to the project page when empty.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_lightbox',
			array(
				'label'        => __( 'Lightbox Button', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Adds a zoom control that opens the full image.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_link_action',
			array(
				'label'        => __( 'Link Button', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'empty_text',
			array(
				'label'     => __( 'Nothing-Found Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'No projects match that filter.', 'elementor-animatepro' ),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	/**
	 * Tile style.
	 *
	 * @return void
	 */
	protected function register_tile_style() {
		$this->start_controls_section(
			'style_tile',
			array(
				'label' => __( 'Tile', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'tile_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-pf-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'tile_border',
				'selector' => '{{WRAPPER}} .eap-portfolio__tile',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'tile_shadow',
				'selector' => '{{WRAPPER}} .eap-portfolio__tile',
			)
		);

		$this->add_control(
			'placeholder_color',
			array(
				'label'       => __( 'Imageless Tile Color', 'elementor-animatepro' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#e5e7eb',
				'selectors'   => array( '{{WRAPPER}}' => '--eap-pf-ph: {{VALUE}};' ),
				'description' => __( 'Used when a project has no featured image.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Overlay style.
	 *
	 * @return void
	 */
	protected function register_overlay_style() {
		$this->start_controls_section(
			'style_overlay',
			array(
				'label' => __( 'Overlay', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(17, 24, 39, 0.72)',
				'selectors' => array( '{{WRAPPER}}' => '--eap-pf-overlay: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'overlay_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 20,
					'right'    => 20,
					'bottom'   => 20,
					'left'     => 20,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-portfolio__overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'overlay_align',
			array(
				'label'                => __( 'Content Position', 'elementor-animatepro' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'flex-end',
				'options'              => array(
					'flex-start' => __( 'Top', 'elementor-animatepro' ),
					'center'     => __( 'Middle', 'elementor-animatepro' ),
					'flex-end'   => __( 'Bottom', 'elementor-animatepro' ),
				),
				'selectors'            => array(
					'{{WRAPPER}} .eap-portfolio__overlay' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pf_title_typography',
				'label'    => __( 'Title Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-portfolio__title',
			)
		);

		/*
		 * NO default: the stylesheet puts white text on the overlay, and a control
		 * default would emit {{WRAPPER}}-scoped CSS that beats it — the same trap
		 * as the overlay layouts in Posts, Category Slider and Featured Posts.
		 */
		$this->add_control(
			'pf_title_color',
			array(
				'label'     => __( 'Title Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-portfolio__title, {{WRAPPER}} .eap-portfolio__title a' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'pf_meta_color',
			array(
				'label'     => __( 'Meta Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-portfolio__meta' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'action_color',
			array(
				'label'     => __( 'Action Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-portfolio__action' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'action_bg',
			array(
				'label'     => __( 'Action Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-portfolio__action' => 'background-color: {{VALUE}};' ),
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

		$this->add_control(
			'filter_align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'flex-start' => array( 'title' => __( 'Left', 'elementor-animatepro' ), 'icon' => 'eicon-text-align-left' ),
					'center'     => array( 'title' => __( 'Center', 'elementor-animatepro' ), 'icon' => 'eicon-text-align-center' ),
					'flex-end'   => array( 'title' => __( 'Right', 'elementor-animatepro' ), 'icon' => 'eicon-text-align-right' ),
				),
				'selectors' => array( '{{WRAPPER}} .eap-portfolio__filters' => 'justify-content: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filter_typography',
				'selector' => '{{WRAPPER}} .eap-portfolio__filter',
			)
		);

		$this->start_controls_tabs( 'pf_filter_tabs' );

		$this->start_controls_tab( 'pf_filter_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'filter_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-portfolio__filter' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'filter_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-portfolio__filter' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'pf_filter_active', array( 'label' => __( 'Active', 'elementor-animatepro' ) ) );

		$this->add_control(
			'filter_color_active',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-portfolio__filter.is-active' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'filter_bg_active',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-portfolio__filter.is-active' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

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

		$post_type = ! empty( $settings['post_type'] ) ? $settings['post_type'] : EAP_Portfolio_CPT::POST_TYPE;
		if ( ! post_type_exists( $post_type ) ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-portfolio eap-portfolio--empty">'
					. esc_html__( 'The Portfolio post type is switched off. Enable it under AnimatePro → Extensions, or pick another post type.', 'elementor-animatepro' )
					. '</div>';
			}
			return;
		}

		$spec = array(
			'source'        => 'latest',
			'post_type'     => $post_type,
			'per_page'      => isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 9,
			'orderby'       => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'menu_order',
			'order'         => ! empty( $settings['order'] ) ? $settings['order'] : 'ASC',
			'include_terms' => isset( $settings['include_terms'] ) ? $settings['include_terms'] : array(),
			'exclude_terms' => isset( $settings['exclude_terms'] ) ? $settings['exclude_terms'] : array(),
			'ignore_sticky' => true,
		);

		$query = new WP_Query( EAP_Posts_Query::build_query_args( $spec, 1 ) );

		if ( ! $query->have_posts() ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-portfolio eap-portfolio--empty">' . esc_html__( 'No projects found for this query.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$post_ids = wp_list_pluck( $query->posts, 'ID' );
		$filter   = $this->get_filter_data( $settings, $post_ids );

		$layout  = ( 'masonry' === ( $settings['layout'] ?? 'grid' ) ) ? 'masonry' : 'grid';
		$hover   = in_array( $settings['hover_effect'] ?? 'zoom', array( 'none', 'zoom', 'lift', 'both' ), true ) ? $settings['hover_effect'] : 'zoom';
		$reveal  = ( 'always' === ( $settings['overlay_reveal'] ?? 'hover' ) ) ? 'always' : 'hover';

		$classes = array(
			'eap-widget',
			'eap-portfolio',
			'eap-portfolio--' . $layout,
			'eap-portfolio--hover-' . $hover,
			'eap-portfolio--overlay-' . $reveal,
		);

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		$this->add_render_attribute( 'wrapper', 'data-eap-portfolio', '' );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php $this->render_filters( $filter, $settings ); ?>

			<div class="eap-portfolio__grid">
				<?php
				foreach ( $post_ids as $pid ) {
					$this->render_tile( $pid, $settings, $filter );
				}
				?>
			</div>

			<p class="eap-portfolio__empty" hidden>
				<?php echo esc_html( $settings['empty_text'] ?? __( 'No projects match that filter.', 'elementor-animatepro' ) ); ?>
			</p>
		</div>
		<?php
		wp_reset_postdata();
	}

	/**
	 * Render one project tile.
	 *
	 * @param int   $pid      Post ID.
	 * @param array $settings Settings.
	 * @param array|null $filter Filter data (for the term map).
	 * @return void
	 */
	protected function render_tile( $pid, $settings, $filter ) {
		$permalink = get_permalink( $pid );
		$title     = get_the_title( $pid );

		$project_url = (string) get_post_meta( $pid, EAP_Portfolio_CPT::META_URL, true );
		$client      = (string) get_post_meta( $pid, EAP_Portfolio_CPT::META_CLIENT, true );
		$done        = (string) get_post_meta( $pid, EAP_Portfolio_CPT::META_DATE, true );

		$link_mode = $settings['tile_link'] ?? 'permalink';
		$href      = $permalink;
		if ( 'project' === $link_mode && '' !== $project_url ) {
			$href = $project_url;
		} elseif ( 'none' === $link_mode ) {
			$href = '';
		}

		$size  = ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'large';
		$thumb = get_the_post_thumbnail( $pid, $size, array( 'class' => 'eap-portfolio__img', 'loading' => 'lazy' ) );

		$thumb_id = get_post_thumbnail_id( $pid );
		$full     = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'full' ) : '';

		$terms = isset( $filter['map'][ $pid ] ) ? $filter['map'][ $pid ] : array();

		$term_names = array();
		if ( 'yes' === ( $settings['show_terms'] ?? 'yes' ) && ! empty( $filter['terms'] ) ) {
			foreach ( $terms as $tid ) {
				if ( isset( $filter['terms'][ $tid ] ) ) {
					$term_names[] = $filter['terms'][ $tid ]['name'];
				}
			}
		}

		$meta = array();
		if ( 'yes' === ( $settings['show_client'] ?? '' ) && '' !== $client ) {
			$meta[] = $client;
		}
		if ( 'yes' === ( $settings['show_date'] ?? '' ) && '' !== $done ) {
			$format = ! empty( $settings['date_format'] ) ? $settings['date_format'] : 'F Y';
			$stamp  = strtotime( $done . ' 12:00:00' );
			if ( $stamp ) {
				$meta[] = wp_date( $format, $stamp );
			}
		}
		if ( ! empty( $term_names ) ) {
			array_unshift( $meta, implode( ', ', $term_names ) );
		}

		$tag = $settings['title_tag'] ?? 'h3';
		if ( ! in_array( $tag, array( 'h2', 'h3', 'h4', 'h5', 'div', 'span' ), true ) ) {
			$tag = 'h3';
		}
		?>
		<div class="eap-portfolio__item" data-terms="<?php echo esc_attr( implode( ' ', array_map( 'intval', $terms ) ) ); ?>">
			<div class="eap-portfolio__tile">
				<?php if ( $thumb ) : ?>
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_the_post_thumbnail() markup.
					echo $thumb;
					?>
				<?php else : ?>
					<span class="eap-portfolio__img eap-portfolio__img--ph" aria-hidden="true"></span>
				<?php endif; ?>

				<?php if ( '' !== $href ) : ?>
					<a class="eap-portfolio__link" href="<?php echo esc_url( $href ); ?>"
						<?php echo ( 'project' === $link_mode && $href === $project_url ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
						<span class="screen-reader-text"><?php echo esc_html( $title ); ?></span>
					</a>
				<?php endif; ?>

				<div class="eap-portfolio__overlay">
					<div class="eap-portfolio__text">
						<?php if ( 'yes' === ( $settings['show_title'] ?? 'yes' ) ) : ?>
							<<?php echo esc_html( $tag ); ?> class="eap-portfolio__title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $tag ); ?>>
						<?php endif; ?>
						<?php if ( ! empty( $meta ) ) : ?>
							<div class="eap-portfolio__meta"><?php echo esc_html( implode( ' · ', $meta ) ); ?></div>
						<?php endif; ?>
					</div>

					<div class="eap-portfolio__actions">
						<?php if ( 'yes' === ( $settings['show_lightbox'] ?? 'yes' ) && '' !== $full ) : ?>
							<button type="button" class="eap-portfolio__action eap-portfolio__action--zoom"
								data-eap-pf-full="<?php echo esc_url( $full ); ?>"
								data-eap-pf-caption="<?php echo esc_attr( $title ); ?>"
								aria-label="<?php echo esc_attr( sprintf( /* translators: %s: project title. */ __( 'View image of %s', 'elementor-animatepro' ), $title ) ); ?>">
								<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M11 4a7 7 0 1 0 0 14 7 7 0 0 0 0-14zm9 16-4.35-4.35M11 8v6M8 11h6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</button>
						<?php endif; ?>

						<?php if ( 'yes' === ( $settings['show_link_action'] ?? 'yes' ) ) : ?>
							<?php
							$action_href   = '' !== $project_url ? $project_url : $permalink;
							$action_target = ( '' !== $project_url ) ? ' target="_blank" rel="noopener noreferrer"' : '';
							?>
							<a class="eap-portfolio__action eap-portfolio__action--link" href="<?php echo esc_url( $action_href ); ?>"<?php echo $action_target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static markup. ?>
								aria-label="<?php echo esc_attr( sprintf( /* translators: %s: project title. */ __( 'Open %s', 'elementor-animatepro' ), $title ) ); ?>">
								<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 4h6v6M20 4l-8 8M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Build filter tabs from the terms the LOADED projects carry.
	 *
	 * One wp_get_object_terms() call for the whole set, which doubles as the
	 * post -> term map the tiles need.
	 *
	 * @param array $settings Settings.
	 * @param int[] $post_ids Loaded IDs.
	 * @return array|null
	 */
	protected function get_filter_data( $settings, $post_ids ) {
		$taxonomy = ! empty( $settings['filter_taxonomy'] ) ? $settings['filter_taxonomy'] : EAP_Portfolio_CPT::TAXONOMY;
		if ( ! taxonomy_exists( $taxonomy ) || empty( $post_ids ) ) {
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

		uasort(
			$terms,
			static function ( $a, $b ) {
				return strcasecmp( $a['name'], $b['name'] );
			}
		);

		return array(
			'terms' => $terms,
			'map'   => $map,
			'total' => count( $post_ids ),
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
		if ( ! $filter || 'yes' !== ( $settings['show_filters'] ?? 'yes' ) || empty( $filter['terms'] ) ) {
			return;
		}

		$counts = 'yes' === ( $settings['show_counts'] ?? '' );
		?>
		<div class="eap-portfolio__filters" role="group">
			<button type="button" class="eap-portfolio__filter is-active" data-filter="0" aria-pressed="true">
				<span><?php echo esc_html( ! empty( $settings['all_label'] ) ? $settings['all_label'] : __( 'All', 'elementor-animatepro' ) ); ?></span>
				<?php if ( $counts ) : ?>
					<span class="eap-portfolio__filter-count"><?php echo esc_html( (string) $filter['total'] ); ?></span>
				<?php endif; ?>
			</button>
			<?php foreach ( $filter['terms'] as $term ) : ?>
				<button type="button" class="eap-portfolio__filter" data-filter="<?php echo esc_attr( (string) $term['id'] ); ?>" aria-pressed="false">
					<span><?php echo esc_html( $term['name'] ); ?></span>
					<?php if ( $counts ) : ?>
						<span class="eap-portfolio__filter-count"><?php echo esc_html( (string) $term['count'] ); ?></span>
					<?php endif; ?>
				</button>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Public taxonomies.
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
