<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;

class EAP_Widget_Timeline extends EAP_Widget_Base {

	/**
	 * Widget name (used by Elementor as data-widget_type prefix).
	 *
	 * Important: keep the `eap-` prefix so the EAP editor badge picks it up
	 * automatically via the [data-widget_type*="eap-"] selector.
	 */
	public function get_name() {
		return 'eap-timeline';
	}

	public function get_title() {
		return __( 'Timeline', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	public function get_keywords() {
		return array( 'timeline', 'history', 'roadmap', 'milestone', 'eap', 'animatepro' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'timeline' );
	}

	public function get_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-gsap',
			'eap-gsap-scrolltrigger',
			'eap-timeline-script',
		);
	}

	/**
	 * Register all controls.
	 */
	protected function register_controls() {
		$this->register_layout_controls();
		$this->register_items_controls();
		$this->register_animation_controls();

		$this->register_line_style_controls();
		$this->register_marker_style_controls();
		$this->register_card_style_controls();
		$this->register_image_style_controls();
		$this->register_content_style_controls();
		$this->register_date_style_controls();
		$this->register_title_style_controls();
		$this->register_subtitle_style_controls();
		$this->register_description_style_controls();
	}

	/* ------------------------------------------------------------------ *
	 * Content controls
	 * ------------------------------------------------------------------ */

	private function register_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'center',
				'options' => array(
					'left'       => __( 'Left Aligned', 'elementor-animatepro' ),
					'center'     => __( 'Center (Alternating)', 'elementor-animatepro' ),
					'horizontal' => __( 'Horizontal Scroll', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'card_position_center',
			array(
				'label'        => __( 'First Item Side', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'left',
				'options'      => array(
					'left'  => __( 'Left', 'elementor-animatepro' ),
					'right' => __( 'Right', 'elementor-animatepro' ),
				),
				'condition'    => array( 'layout' => 'center' ),
				'description'  => __( 'Controls which side the first item starts on in the alternating layout.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'horizontal_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( '<strong>Heads up:</strong> the horizontal timeline needs the full viewport width to scroll properly. If your Elementor container is boxed/constrained, enable <em>Force Full Width</em> below or set the parent container to <strong>Full Width</strong>.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition'       => array( 'layout' => 'horizontal' ),
			)
		);

		$this->add_control(
			'horizontal_force_fullwidth',
			array(
				'label'        => __( 'Force Full Width', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'description'  => __( 'Breaks the widget out of a boxed container so it spans the full browser viewport. Recommended for the horizontal layout.', 'elementor-animatepro' ),
				'condition'    => array( 'layout' => 'horizontal' ),
			)
		);

		$this->add_responsive_control(
			'horizontal_height',
			array(
				'label'      => __( 'Horizontal Viewport Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 300, 'max' => 1200 ),
					'vh' => array( 'min' => 40, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'vh', 'size' => 90 ),
				'condition'  => array( 'layout' => 'horizontal' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline--horizontal .eap-timeline__horizontal-viewport' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'horizontal_pin_position',
			array(
				'label'        => __( 'Pin Position', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'center',
				'options'      => array(
					'center' => __( 'Vertically Centered', 'elementor-animatepro' ),
					'top'    => __( 'Stick to Top', 'elementor-animatepro' ),
				),
				'condition'    => array( 'layout' => 'horizontal' ),
				'description'  => __( 'Where the timeline sits on screen while horizontally scrolling. Centered keeps space above and below so card content does not get clipped.', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'horizontal_card_width',
			array(
				'label'      => __( 'Horizontal Card Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 220, 'max' => 600 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 340 ),
				'condition'  => array( 'layout' => 'horizontal' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline--horizontal .eap-timeline__item' => '--eap-tl-card-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'horizontal_connector_size',
			array(
				'label'      => __( 'Connector Length', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 60 ),
				'condition'  => array( 'layout' => 'horizontal' ),
				'description'  => __( 'Length of the vertical line that connects each marker to its card.', 'elementor-animatepro' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline--horizontal' => '--eap-tl-h-connector: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'horizontal_connector_thickness',
			array(
				'label'      => __( 'Connector Thickness', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 1, 'max' => 12 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 2 ),
				'condition'  => array( 'layout' => 'horizontal' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline--horizontal .eap-timeline__connector' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'horizontal_gap',
			array(
				'label'      => __( 'Horizontal Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 200 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 80 ),
				'condition'  => array( 'layout' => 'horizontal' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline--horizontal .eap-timeline__items' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_vertical_align',
			array(
				'label'     => __( 'Content Vertical Align', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .eap-timeline__card' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => __( 'Vertical Gap Between Items', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 60 ),
				'condition'  => array( 'layout!' => 'horizontal' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline:not(.eap-timeline--horizontal) .eap-timeline__items' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_items_controls() {
		$this->start_controls_section(
			'section_items',
			array(
				'label' => __( 'Items', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_image',
			array(
				'label'   => __( 'Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$repeater->add_control(
			'item_date',
			array(
				'label'       => __( 'Date', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Jan 01, 2021', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'item_title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Journey Started at New York', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'item_subtitle',
			array(
				'label'   => __( 'Subtitle', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Designer', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'item_description',
			array(
				'label'   => __( 'Description', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus ac pulvinar elit, nec pharetra diam.', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'item_link',
			array(
				'label'   => __( 'Link', 'elementor-animatepro' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '' ),
			)
		);

		$repeater->add_control(
			'item_marker_text',
			array(
				'label'       => __( 'Marker Label', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Optional text/number shown inside the dot (e.g. 01, 02).', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'item_marker_icon',
			array(
				'label' => __( 'Marker Icon', 'elementor-animatepro' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Items', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ item_title }}}',
				'default'     => array(
					array(
						'item_date'        => __( 'Jan 01, 2021', 'elementor-animatepro' ),
						'item_title'       => __( 'Journey Started at New York', 'elementor-animatepro' ),
						'item_subtitle'    => __( 'Designer', 'elementor-animatepro' ),
						'item_description' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus ac pulvinar elit, nec pharetra diam.', 'elementor-animatepro' ),
						'item_marker_text' => '01',
					),
					array(
						'item_date'        => __( 'Mar 18, 2022', 'elementor-animatepro' ),
						'item_title'       => __( 'Launched First Product', 'elementor-animatepro' ),
						'item_subtitle'    => __( 'Founder', 'elementor-animatepro' ),
						'item_description' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus ac pulvinar elit, nec pharetra diam.', 'elementor-animatepro' ),
						'item_marker_text' => '02',
					),
					array(
						'item_date'        => __( 'Sep 04, 2023', 'elementor-animatepro' ),
						'item_title'       => __( 'Opened New Office', 'elementor-animatepro' ),
						'item_subtitle'    => __( 'CEO', 'elementor-animatepro' ),
						'item_description' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus ac pulvinar elit, nec pharetra diam.', 'elementor-animatepro' ),
						'item_marker_text' => '03',
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'item_image_size',
				'default' => 'medium_large',
			)
		);

		$this->end_controls_section();
	}

	private function register_animation_controls() {
		$this->start_controls_section(
			'section_animation',
			array(
				'label' => __( 'Animation', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'enable_line_fill',
			array(
				'label'        => __( 'Animated Line Fill', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'description'  => __( 'Line progressively fills as the user scrolls (GSAP ScrollTrigger).', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'enable_marker_follow',
			array(
				'label'        => __( 'Animated Marker', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'description'  => __( 'A travelling marker dot follows the scroll position along the line.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'intro_animation',
			array(
				'label'   => __( 'Item Intro Animation', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fade-up',
				'options' => array(
					'none'      => __( 'None', 'elementor-animatepro' ),
					'fade'      => __( 'Fade', 'elementor-animatepro' ),
					'fade-up'   => __( 'Fade Up', 'elementor-animatepro' ),
					'fade-side' => __( 'Fade From Side', 'elementor-animatepro' ),
					'zoom-in'   => __( 'Zoom In', 'elementor-animatepro' ),
					'flip-up'   => __( 'Flip Up', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'intro_duration',
			array(
				'label'   => __( 'Intro Duration (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 100,
				'max'     => 3000,
				'step'    => 50,
				'default' => 800,
			)
		);

		$this->add_control(
			'intro_stagger',
			array(
				'label'   => __( 'Stagger Between Items (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 1000,
				'step'    => 25,
				'default' => 120,
			)
		);

		$this->add_control(
			'intro_distance',
			array(
				'label'   => __( 'Intro Travel Distance (px)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 300,
				'step'    => 4,
				'default' => 50,
			)
		);

		$this->add_control(
			'horizontal_scrub',
			array(
				'label'        => __( 'Horizontal Scroll Speed', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SLIDER,
				'range'        => array( 'px' => array( 'min' => 0.5, 'max' => 3, 'step' => 0.1 ) ),
				'default'      => array( 'unit' => 'px', 'size' => 1 ),
				'condition'    => array( 'layout' => 'horizontal' ),
				'description'  => __( 'Multiplier for how much scroll length is needed for the horizontal track. Higher = slower scroll-through.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------ *
	 * Style controls
	 * ------------------------------------------------------------------ */

	private function register_line_style_controls() {
		$this->start_controls_section(
			'section_line_style',
			array(
				'label' => __( 'Line', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'line_thickness',
			array(
				'label'      => __( 'Thickness', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 1, 'max' => 12 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 2 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline' => '--eap-tl-line-thickness: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'line_style',
			array(
				'label'     => __( 'Style', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'dashed',
				'options'   => array(
					'solid'  => __( 'Solid', 'elementor-animatepro' ),
					'dashed' => __( 'Dashed', 'elementor-animatepro' ),
					'dotted' => __( 'Dotted', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'line_track_color',
			array(
				'label'     => __( 'Track Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3d3d3d',
				'selectors' => array(
					'{{WRAPPER}} .eap-timeline' => '--eap-tl-line-track: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'line_fill_color',
			array(
				'label'     => __( 'Fill Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff5722',
				'selectors' => array(
					'{{WRAPPER}} .eap-timeline' => '--eap-tl-line-fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_marker_style_controls() {
		$this->start_controls_section(
			'section_marker_style',
			array(
				'label' => __( 'Marker', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'marker_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 18, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 32 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline' => '--eap-tl-marker-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'marker_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff5722',
				'selectors' => array(
					'{{WRAPPER}} .eap-timeline' => '--eap-tl-marker-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'marker_color',
			array(
				'label'     => __( 'Label / Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-timeline' => '--eap-tl-marker-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'marker_typography',
				'selector' => '{{WRAPPER}} .eap-timeline__marker',
			)
		);

		$this->add_control(
			'marker_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 50,
					'right'    => 50,
					'bottom'   => 50,
					'left'     => 50,
					'unit'     => '%',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline__marker' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'marker_border',
				'selector' => '{{WRAPPER}} .eap-timeline__marker',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'marker_shadow',
				'selector' => '{{WRAPPER}} .eap-timeline__marker',
			)
		);

		$this->end_controls_section();
	}

	private function register_card_style_controls() {
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
				'default'   => 'transparent',
				'selectors' => array(
					'{{WRAPPER}} .eap-timeline__card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 8,
					'right'    => 8,
					'bottom'   => 8,
					'left'     => 8,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline__card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .eap-timeline__card',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eap-timeline__card',
			)
		);

		$this->add_responsive_control(
			'card_max_width',
			array(
				'label'      => __( 'Max Width (Center/Left)', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 180, 'max' => 800 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 420 ),
				'condition'  => array( 'layout!' => 'horizontal' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline:not(.eap-timeline--horizontal) .eap-timeline__item' => '--eap-tl-card-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_image_style_controls() {
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
				'range'      => array(
					'px' => array( 'min' => 100, 'max' => 700 ),
					'vh' => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 260 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline__media' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'image_object_fit',
			array(
				'label'     => __( 'Object Fit', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => array(
					'cover'    => __( 'Cover', 'elementor-animatepro' ),
					'contain'  => __( 'Contain', 'elementor-animatepro' ),
					'fill'     => __( 'Fill', 'elementor-animatepro' ),
					'none'     => __( 'None', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-timeline__media img' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline__media' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_content_style_controls() {
		$this->start_controls_section(
			'section_content_style',
			array(
				'label' => __( 'Content', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline__content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'content_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-timeline__content' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline__content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_date_style_controls() {
		$this->start_controls_section(
			'section_date_style',
			array(
				'label' => __( 'Date', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'date_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9ca3af',
				'selectors' => array(
					'{{WRAPPER}} .eap-timeline__date' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'date_typography',
				'selector' => '{{WRAPPER}} .eap-timeline__date',
			)
		);

		$this->add_responsive_control(
			'date_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline__date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_title_style_controls() {
		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => __( 'Title', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-timeline__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-timeline__title',
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_subtitle_style_controls() {
		$this->start_controls_section(
			'section_subtitle_style',
			array(
				'label' => __( 'Subtitle', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9ca3af',
				'selectors' => array(
					'{{WRAPPER}} .eap-timeline__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .eap-timeline__subtitle',
			)
		);

		$this->add_responsive_control(
			'subtitle_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline__subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_description_style_controls() {
		$this->start_controls_section(
			'section_description_style',
			array(
				'label' => __( 'Description', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cbd5e1',
				'selectors' => array(
					'{{WRAPPER}} .eap-timeline__description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .eap-timeline__description',
			)
		);

		$this->add_responsive_control(
			'description_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-timeline__description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------ *
	 * Render
	 * ------------------------------------------------------------------ */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();

		if ( empty( $items ) ) {
			return;
		}

		$layout         = ! empty( $settings['layout'] ) ? $settings['layout'] : 'center';
		$first_side     = ! empty( $settings['card_position_center'] ) ? $settings['card_position_center'] : 'left';
		$line_fill_on   = ! empty( $settings['enable_line_fill'] ) && 'yes' === $settings['enable_line_fill'];
		$marker_on      = ! empty( $settings['enable_marker_follow'] ) && 'yes' === $settings['enable_marker_follow'];
		$intro          = ! empty( $settings['intro_animation'] ) ? $settings['intro_animation'] : 'fade-up';
		$intro_duration = isset( $settings['intro_duration'] ) ? (int) $settings['intro_duration'] : 800;
		$intro_stagger  = isset( $settings['intro_stagger'] ) ? (int) $settings['intro_stagger'] : 120;
		$intro_distance = isset( $settings['intro_distance'] ) ? (int) $settings['intro_distance'] : 50;
		$horiz_scrub    = ! empty( $settings['horizontal_scrub']['size'] ) ? (float) $settings['horizontal_scrub']['size'] : 1;

		$line_style       = ! empty( $settings['line_style'] ) ? $settings['line_style'] : 'dashed';
		$force_fullwidth  = 'horizontal' === $layout && ! empty( $settings['horizontal_force_fullwidth'] ) && 'yes' === $settings['horizontal_force_fullwidth'];

		$wrapper_classes = array(
			'eap-timeline',
			'eap-timeline--' . esc_attr( $layout ),
			'eap-timeline--line-' . esc_attr( $line_style ),
		);
		if ( $force_fullwidth ) {
			$wrapper_classes[] = 'eap-timeline--force-fullwidth';
		}

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'                  => $wrapper_classes,
				'data-layout'            => esc_attr( $layout ),
				'data-first-side'        => esc_attr( $first_side ),
				'data-line-fill'         => $line_fill_on ? '1' : '0',
				'data-marker-follow'     => $marker_on ? '1' : '0',
				'data-intro'             => esc_attr( $intro ),
				'data-intro-duration'    => (string) $intro_duration,
				'data-intro-stagger'     => (string) $intro_stagger,
				'data-intro-distance'    => (string) $intro_distance,
				'data-horizontal-scrub'  => (string) $horiz_scrub,
				'data-force-fullwidth'   => $force_fullwidth ? '1' : '0',
				'data-pin-position'      => ! empty( $settings['horizontal_pin_position'] ) ? esc_attr( $settings['horizontal_pin_position'] ) : 'center',
			)
		);
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( 'horizontal' === $layout ) : ?>
				<div class="eap-timeline__horizontal-viewport">
					<div class="eap-timeline__horizontal-track">
						<?php $this->render_line( $settings ); ?>
						<?php $this->render_items( $settings, $items, $layout, $first_side ); ?>
					</div>
				</div>
			<?php else : ?>
				<div class="eap-timeline__track">
					<?php $this->render_line( $settings ); ?>
					<?php $this->render_items( $settings, $items, $layout, $first_side ); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render the line + fill + travelling marker.
	 */
	protected function render_line( $settings ) {
		?>
		<div class="eap-timeline__line">
			<div class="eap-timeline__line-fill"></div>
			<?php if ( ! empty( $settings['enable_marker_follow'] ) && 'yes' === $settings['enable_marker_follow'] ) : ?>
				<div class="eap-timeline__line-marker" aria-hidden="true"></div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render the items collection.
	 */
	protected function render_items( $settings, $items, $layout, $first_side ) {
		?>
		<div class="eap-timeline__items">
			<?php
			$index = 0;
			foreach ( $items as $item ) {
				$this->render_item( $item, $layout, $first_side, $index );
				$index++;
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render single timeline item.
	 */
	protected function render_item( $item, $layout, $first_side, $index ) {
		$side = 'left';
		if ( 'center' === $layout ) {
			$even = ( $index % 2 === 0 );
			if ( 'left' === $first_side ) {
				$side = $even ? 'left' : 'right';
			} else {
				$side = $even ? 'right' : 'left';
			}
		} elseif ( 'horizontal' === $layout ) {
			$side = ( $index % 2 === 0 ) ? 'top' : 'bottom';
		}

		$item_key = 'item_' . $index;
		$this->add_render_attribute( $item_key, 'class', array( 'eap-timeline__item', 'eap-timeline__item--' . $side ) );
		$this->add_render_attribute( $item_key, 'data-side', $side );
		$this->add_render_attribute( $item_key, 'data-index', (string) $index );

		$image_html = '';
		if ( ! empty( $item['item_image']['url'] ) || ! empty( $item['item_image']['id'] ) ) {
			$settings        = $this->get_settings_for_display();
			$image_settings  = array_merge(
				$settings,
				array( 'item_image' => $item['item_image'] )
			);
			$image_html      = Group_Control_Image_Size::get_attachment_image_html(
				$image_settings,
				'item_image_size',
				'item_image'
			);
			if ( empty( $image_html ) && ! empty( $item['item_image']['url'] ) ) {
				$image_html = '<img src="' . esc_url( $item['item_image']['url'] ) . '" alt="" />';
			}
		}

		$has_link  = ! empty( $item['item_link']['url'] );
		$link_open = '';
		$link_close = '';
		if ( $has_link ) {
			$target   = ! empty( $item['item_link']['is_external'] ) ? ' target="_blank"' : '';
			$nofollow = ! empty( $item['item_link']['nofollow'] ) ? ' rel="nofollow"' : '';
			$link_open  = '<a class="eap-timeline__link" href="' . esc_url( $item['item_link']['url'] ) . '"' . $target . $nofollow . '>';
			$link_close = '</a>';
		}

		$marker_inner = '';
		if ( ! empty( $item['item_marker_icon']['value'] ) ) {
			ob_start();
			\Elementor\Icons_Manager::render_icon( $item['item_marker_icon'], array( 'aria-hidden' => 'true' ) );
			$marker_inner = ob_get_clean();
		} elseif ( ! empty( $item['item_marker_text'] ) ) {
			$marker_inner = esc_html( $item['item_marker_text'] );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( $item_key ); ?>>
			<div class="eap-timeline__marker">
				<span class="eap-timeline__marker-inner"><?php echo $marker_inner; ?></span>
			</div>
			<div class="eap-timeline__connector" aria-hidden="true"></div>
			<div class="eap-timeline__card">
				<?php echo $link_open; ?>
				<?php if ( $image_html ) : ?>
					<div class="eap-timeline__media"><?php echo $image_html; ?></div>
				<?php endif; ?>
				<div class="eap-timeline__content">
					<?php if ( ! empty( $item['item_date'] ) ) : ?>
						<div class="eap-timeline__date"><?php echo esc_html( $item['item_date'] ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( $item['item_title'] ) ) : ?>
						<h3 class="eap-timeline__title"><?php echo esc_html( $item['item_title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $item['item_subtitle'] ) ) : ?>
						<div class="eap-timeline__subtitle"><?php echo esc_html( $item['item_subtitle'] ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( $item['item_description'] ) ) : ?>
						<div class="eap-timeline__description"><?php echo wp_kses_post( $item['item_description'] ); ?></div>
					<?php endif; ?>
				</div>
				<?php echo $link_close; ?>
			</div>
		</div>
		<?php
	}
}
