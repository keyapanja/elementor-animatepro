<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;

class EAP_Widget_Image_Accordion extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-image-accordion';
	}

	public function get_title() {
		return __( 'Image Accordion', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	public function get_keywords() {
		return array( 'image', 'accordion', 'hover', 'expand', 'panels', 'gallery' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'image-accordion' );
	}

	public function get_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-image-accordion-script',
		);
	}

	protected function register_controls() {
		$this->register_layout_controls();
		$this->register_items_controls();
		$this->register_animation_controls();
		$this->register_panel_style_controls();
		$this->register_image_style_controls();
		$this->register_overlay_style_controls();
		$this->register_content_style_controls();
		$this->register_subtitle_style_controls();
		$this->register_title_style_controls();
		$this->register_description_style_controls();
		$this->register_button_style_controls();
	}

	/* -------------------------------------------------------------------------
	 * Content: layout
	 * ---------------------------------------------------------------------- */

	protected function register_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'orientation',
			array(
				'label'   => __( 'Orientation', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => __( 'Horizontal', 'elementor-animatepro' ),
					'vertical'   => __( 'Vertical', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'trigger',
			array(
				'label'   => __( 'Expand On', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => array(
					'hover' => __( 'Hover', 'elementor-animatepro' ),
					'click' => __( 'Click', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'hover_rest',
			array(
				'label'     => __( 'When Mouse Leaves', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => array(
					'default' => __( 'Reopen Default Panel', 'elementor-animatepro' ),
					'last'    => __( 'Keep Last Hovered', 'elementor-animatepro' ),
				),
				'condition' => array( 'trigger' => 'hover' ),
			)
		);

		$this->add_control(
			'closed_content',
			array(
				'label'       => __( 'Closed Panels Show', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'title',
				'options'     => array(
					'none'     => __( 'Nothing', 'elementor-animatepro' ),
					'title'    => __( 'Title', 'elementor-animatepro' ),
					'subtitle' => __( 'Subtitle', 'elementor-animatepro' ),
					'both'     => __( 'Title + Subtitle', 'elementor-animatepro' ),
				),
				'description' => __( 'What stays visible on collapsed panels. The open panel always shows the full caption.', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 160, 'max' => 900 ),
					'vh' => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'size' => 440, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-accordion__list' => '--eap-ia-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'active_grow',
			array(
				'label'       => __( 'Expanded Ratio', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'custom' ),
				'range'       => array(
					'custom' => array( 'min' => 1.5, 'max' => 10, 'step' => 0.5 ),
				),
				'default'     => array( 'size' => 4 ),
				'description' => __( 'How much larger the active panel grows compared to the collapsed ones.', 'elementor-animatepro' ),
				'selectors'   => array(
					'{{WRAPPER}} .eap-image-accordion' => '--eap-ia-active-grow: {{SIZE}};',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap Between Panels', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-accordion__list' => '--eap-ia-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'      => __( 'Transition Speed (ms)', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 150, 'max' => 1500 ) ),
				'default'    => array( 'size' => 600, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-accordion' => '--eap-ia-speed: {{SIZE}}ms;',
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Content: items
	 * ---------------------------------------------------------------------- */

	protected function register_items_controls() {
		$this->start_controls_section(
			'section_items',
			array(
				'label' => __( 'Panels', 'elementor-animatepro' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$repeater->add_control(
			'subtitle',
			array(
				'label'       => __( 'Subtitle', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Category', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Panel Title', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label'   => __( 'Description', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'A short supporting line that appears when this panel is open.', 'elementor-animatepro' ),
				'rows'    => 4,
			)
		);

		$repeater->add_control(
			'button_text',
			array(
				'label'       => __( 'Button Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Read More', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'         => __( 'Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'placeholder'   => __( 'https://example.com', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'default_active',
			array(
				'label'        => __( 'Open By Default', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'The first panel with this enabled starts expanded.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'title'          => __( 'Mountains', 'elementor-animatepro' ),
						'subtitle'       => __( 'Nature', 'elementor-animatepro' ),
						'default_active' => 'yes',
					),
					array(
						'title'    => __( 'Forest', 'elementor-animatepro' ),
						'subtitle' => __( 'Nature', 'elementor-animatepro' ),
					),
					array(
						'title'    => __( 'Ocean', 'elementor-animatepro' ),
						'subtitle' => __( 'Nature', 'elementor-animatepro' ),
					),
					array(
						'title'    => __( 'Desert', 'elementor-animatepro' ),
						'subtitle' => __( 'Nature', 'elementor-animatepro' ),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Content: entrance animation
	 * ---------------------------------------------------------------------- */

	protected function register_animation_controls() {
		$this->start_controls_section(
			'section_animation',
			array(
				'label' => __( 'Entrance Animation', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'entrance',
			array(
				'label'   => __( 'Animation', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => array(
					'none'  => __( 'None', 'elementor-animatepro' ),
					'fade'  => __( 'Fade In', 'elementor-animatepro' ),
					'slide' => __( 'Slide Up', 'elementor-animatepro' ),
					'zoom'  => __( 'Zoom In', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'entrance_duration',
			array(
				'label'     => __( 'Duration (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 700,
				'min'       => 100,
				'max'       => 4000,
				'step'      => 50,
				'condition' => array( 'entrance!' => 'none' ),
			)
		);

		$this->add_control(
			'entrance_stagger',
			array(
				'label'     => __( 'Stagger (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 120,
				'min'       => 0,
				'max'       => 1000,
				'step'      => 10,
				'condition' => array( 'entrance!' => 'none' ),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: panels (box)
	 * ---------------------------------------------------------------------- */

	protected function register_panel_style_controls() {
		$this->start_controls_section(
			'section_panel_style',
			array(
				'label' => __( 'Panels', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'panel_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => 16,
					'right'  => 16,
					'bottom' => 16,
					'left'   => 16,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-accordion__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'panel_border',
				'selector' => '{{WRAPPER}} .eap-image-accordion__item',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'panel_shadow',
				'selector' => '{{WRAPPER}} .eap-image-accordion__item',
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: image
	 * ---------------------------------------------------------------------- */

	protected function register_image_style_controls() {
		$this->start_controls_section(
			'section_image_style',
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
					'{{WRAPPER}} .eap-image-accordion__media img' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'image_zoom',
			array(
				'label'        => __( 'Zoom Active Image', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'inactive_grayscale',
			array(
				'label'        => __( 'Grayscale Collapsed', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'selectors'    => array(
					'{{WRAPPER}} .eap-image-accordion__item:not(.is-active) .eap-image-accordion__media img' => 'filter: grayscale(100%);',
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: overlay
	 * ---------------------------------------------------------------------- */

	protected function register_overlay_style_controls() {
		$this->start_controls_section(
			'section_overlay_style',
			array(
				'label' => __( 'Overlay', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Collapsed Overlay', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(10, 12, 20, 0.25)',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-accordion__overlay' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'overlay_active',
				'label'    => __( 'Active Overlay', 'elementor-animatepro' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .eap-image-accordion__item.is-active .eap-image-accordion__overlay',
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: content box
	 * ---------------------------------------------------------------------- */

	protected function register_content_style_controls() {
		$this->start_controls_section(
			'section_content_style',
			array(
				'label' => __( 'Content', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_valign',
			array(
				'label'     => __( 'Vertical Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-end',
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => __( 'Middle', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-image-accordion__content' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'start',
				'options'   => array(
					'start'  => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'end'    => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-image-accordion__content' => 'align-items: {{VALUE}}; text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 28,
					'right'  => 28,
					'bottom' => 28,
					'left'   => 28,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-accordion__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: subtitle
	 * ---------------------------------------------------------------------- */

	protected function register_subtitle_style_controls() {
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
				'default'   => 'rgba(255, 255, 255, 0.75)',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-accordion__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .eap-image-accordion__subtitle',
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: title
	 * ---------------------------------------------------------------------- */

	protected function register_title_style_controls() {
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
					'{{WRAPPER}} .eap-image-accordion__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-image-accordion__title',
			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-accordion__title' => 'margin: 0 0 {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: description
	 * ---------------------------------------------------------------------- */

	protected function register_description_style_controls() {
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
				'default'   => 'rgba(255, 255, 255, 0.82)',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-accordion__desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .eap-image-accordion__desc',
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: button
	 * ---------------------------------------------------------------------- */

	protected function register_button_style_controls() {
		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => __( 'Button', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .eap-image-accordion__button',
			)
		);

		$this->start_controls_tabs( 'button_tabs' );

		$this->start_controls_tab(
			'button_tab_normal',
			array( 'label' => __( 'Normal', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-accordion__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-accordion__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_tab_hover',
			array( 'label' => __( 'Hover', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'button_color_hover',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-accordion__button:hover, {{WRAPPER}} .eap-image-accordion__button:focus' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff6b2c',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-accordion__button:hover, {{WRAPPER}} .eap-image-accordion__button:focus' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'separator'  => 'before',
				'default'    => array(
					'top'    => 10,
					'right'  => 20,
					'bottom' => 10,
					'left'   => 20,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-accordion__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => 50,
					'right'  => 50,
					'bottom' => 50,
					'left'   => 50,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-accordion__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Render
	 * ---------------------------------------------------------------------- */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();

		if ( empty( $items ) ) {
			return;
		}

		$orientation = ! empty( $settings['orientation'] ) ? $settings['orientation'] : 'horizontal';
		$trigger     = ! empty( $settings['trigger'] ) ? $settings['trigger'] : 'hover';
		$hover_rest  = ! empty( $settings['hover_rest'] ) ? $settings['hover_rest'] : 'default';
		$zoom        = 'yes' === ( $settings['image_zoom'] ?? 'yes' );
		$closed      = ! empty( $settings['closed_content'] ) ? $settings['closed_content'] : 'title';
		$entrance    = ! empty( $settings['entrance'] ) ? $settings['entrance'] : 'fade';
		$duration    = isset( $settings['entrance_duration'] ) ? absint( $settings['entrance_duration'] ) : 700;
		$stagger     = isset( $settings['entrance_stagger'] ) ? absint( $settings['entrance_stagger'] ) : 120;

		$active_index = 0;
		foreach ( $items as $index => $item ) {
			if ( 'yes' === ( $item['default_active'] ?? '' ) ) {
				$active_index = $index;
				break;
			}
		}

		$classes = array(
			'eap-widget',
			'eap-image-accordion',
			'eap-image-accordion--' . $orientation,
			'eap-image-accordion--trigger-' . $trigger,
		);
		if ( $zoom ) {
			$classes[] = 'eap-image-accordion--zoom';
		}
		if ( 'none' !== $closed ) {
			$classes[] = 'eap-image-accordion--closed-' . $closed;
		}
		if ( 'none' !== $entrance ) {
			$classes[] = 'eap-image-accordion--anim-' . $entrance;
		}

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'                  => $classes,
				'data-eap-image-accordion' => 'true',
				'data-trigger'           => $trigger,
				'data-hover-rest'        => $hover_rest,
				'data-default-index'     => (string) $active_index,
				'style'                  => sprintf( '--eap-ia-anim-duration:%dms;--eap-ia-stagger:%dms;', $duration, $stagger ),
			)
		);
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="eap-image-accordion__list">
				<?php
				foreach ( $items as $index => $item ) {
					$this->render_item( $item, $index, $index === $active_index );
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a single accordion panel.
	 *
	 * @param array $item      Repeater item.
	 * @param int   $index     Item index.
	 * @param bool  $is_active Whether this panel starts expanded.
	 * @return void
	 */
	protected function render_item( $item, $index, $is_active ) {
		$item_classes = array( 'eap-image-accordion__item' );
		if ( $is_active ) {
			$item_classes[] = 'is-active';
		}

		$image_url = ! empty( $item['image']['url'] ) ? $item['image']['url'] : Utils::get_placeholder_image_src();
		$title     = isset( $item['title'] ) ? $item['title'] : '';
		$subtitle  = isset( $item['subtitle'] ) ? $item['subtitle'] : '';
		$desc      = isset( $item['description'] ) ? $item['description'] : '';
		$btn_text  = isset( $item['button_text'] ) ? $item['button_text'] : '';

		$button_html = '';
		if ( '' !== trim( (string) $btn_text ) ) {
			$btn_key = 'btn_' . $index;
			$this->add_render_attribute( $btn_key, 'class', 'eap-image-accordion__button' );

			if ( ! empty( $item['link']['url'] ) ) {
				$this->add_link_attributes( $btn_key, $item['link'] );
				$button_html = '<a ' . $this->get_render_attribute_string( $btn_key ) . '>' . esc_html( $btn_text ) . '</a>';
			} else {
				$button_html = '<span ' . $this->get_render_attribute_string( $btn_key ) . '>' . esc_html( $btn_text ) . '</span>';
			}
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>" data-eap-accordion-item style="--eap-ia-i: <?php echo esc_attr( $index ); ?>;" tabindex="0">
			<div class="eap-image-accordion__media">
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
			</div>
			<span class="eap-image-accordion__overlay" aria-hidden="true"></span>
			<div class="eap-image-accordion__content">
				<?php if ( '' !== trim( (string) $subtitle ) ) : ?>
					<span class="eap-image-accordion__subtitle"><?php echo esc_html( $subtitle ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== trim( (string) $title ) ) : ?>
					<h3 class="eap-image-accordion__title"><?php echo esc_html( $title ); ?></h3>
				<?php endif; ?>
				<?php if ( '' !== trim( (string) $desc ) ) : ?>
					<div class="eap-image-accordion__desc"><?php echo esc_html( $desc ); ?></div>
				<?php endif; ?>
				<?php if ( '' !== $button_html ) : ?>
					<div class="eap-image-accordion__actions"><?php echo $button_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
