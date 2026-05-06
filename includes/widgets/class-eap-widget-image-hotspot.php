<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class EAP_Widget_Image_Hotspot extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-image-hotspot';
	}

	public function get_title() {
		return __( 'Image Hotspot', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-image-rollover';
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'image-hotspot' );
	}

	public function get_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-visibility-script',
			'eap-image-hotspot-script',
		);
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_image',
			array(
				'label' => __( 'Image', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'image_size',
				'default' => 'full',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_motion',
			array(
				'label' => __( 'Image Motion', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'unfold_direction',
			array(
				'label'   => __( 'Unfold Direction', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'   => __( 'Left to Right', 'elementor-animatepro' ),
					'right'  => __( 'Right to Left', 'elementor-animatepro' ),
					'top'    => __( 'Top to Bottom', 'elementor-animatepro' ),
					'bottom' => __( 'Bottom to Top', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'label'   => __( 'Image Hover Effect', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'zoom-in',
				'options' => array(
					'zoom-in'  => __( 'Zoom In', 'elementor-animatepro' ),
					'zoom-out' => __( 'Zoom Out', 'elementor-animatepro' ),
					'fade'     => __( 'Fade In', 'elementor-animatepro' ),
					'bw-color' => __( 'Black & White to Color', 'elementor-animatepro' ),
					'none'     => __( 'None', 'elementor-animatepro' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_hotspots',
			array(
				'label' => __( 'Hotspot', 'elementor-animatepro' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'hotspot_label',
			array(
				'label'       => __( 'Item Label', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Item', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'hotspot_layout',
			array(
				'label'   => __( 'Hotspot Layout', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'elementor-animatepro' ),
					'icon'    => __( 'Icon', 'elementor-animatepro' ),
					'text'    => __( 'Text', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'hotspot_icon',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-plus',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'hotspot_layout' => 'icon',
				),
			)
		);

		$repeater->add_control(
			'tooltip_type',
			array(
				'label'   => __( 'Tooltip Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'content',
				'options' => array(
					'content' => __( 'Content', 'elementor-animatepro' ),
					'simple'  => __( 'Heading + Description', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'tooltip_content',
			array(
				'label'      => __( 'Tooltip Content', 'elementor-animatepro' ),
				'type'       => Controls_Manager::WYSIWYG,
				'default'    => __( '<p>Tooltip content</p>', 'elementor-animatepro' ),
				'condition'  => array(
					'tooltip_type' => 'content',
				),
			)
		);

		$repeater->add_control(
			'tooltip_heading',
			array(
				'label'       => __( 'Tooltip Heading', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'The ComPsych Design Guidelines', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array(
					'tooltip_type' => 'simple',
				),
			)
		);

		$repeater->add_control(
			'tooltip_description',
			array(
				'label'      => __( 'Tooltip Description', 'elementor-animatepro' ),
				'type'       => Controls_Manager::TEXTAREA,
				'default'    => __( 'Brand identity design a the key have to success whether you breath onfire arolax agency rebranding.', 'elementor-animatepro' ),
				'rows'       => 5,
				'condition'  => array(
					'tooltip_type' => 'simple',
				),
			)
		);

		$repeater->add_responsive_control(
			'position_x',
			array(
				'label'      => __( 'Horizontal Position', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 50,
					'unit' => '%',
				),
			)
		);

		$repeater->add_responsive_control(
			'position_y',
			array(
				'label'      => __( 'Vertical Position', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 50,
					'unit' => '%',
				),
			)
		);

		$this->add_control(
			'hotspots',
			array(
				'label'       => __( 'Hotspot Lists', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ hotspot_label }}}',
				'default'     => array(
					array(
						'hotspot_label'       => __( 'Item #1', 'elementor-animatepro' ),
						'hotspot_layout'      => 'default',
						'tooltip_type'        => 'simple',
						'position_x'          => array(
							'size' => 54,
							'unit' => '%',
						),
						'position_y'          => array(
							'size' => 37,
							'unit' => '%',
						),
						'tooltip_heading'     => __( 'The ComPsych Design Guidelines', 'elementor-animatepro' ),
						'tooltip_description' => __( 'Brand identity design a the key have to success whether you breath onfire arolax agency rebranding.', 'elementor-animatepro' ),
					),
				),
			)
		);

		$this->add_control(
			'hotspot_animation',
			array(
				'label'   => __( 'Animation', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'pulse',
				'options' => array(
					'none'   => __( 'None', 'elementor-animatepro' ),
					'pulse'  => __( 'Pulse', 'elementor-animatepro' ),
					'ripple' => __( 'Ripple', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'animation_speed',
			array(
				'label'   => __( 'Animation Speed', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3,
				'min'     => 0.3,
				'step'    => 0.1,
			)
		);

		$this->add_control(
			'pulse_color',
			array(
				'label'     => __( 'Pulse Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 107, 53, 0.35)',
				'condition' => array(
					'hotspot_animation!' => 'none',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tooltip_settings',
			array(
				'label' => __( 'Tooltip', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'tooltip_trigger',
			array(
				'label'   => __( 'Trigger Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => array(
					'hover' => __( 'Hover', 'elementor-animatepro' ),
					'click' => __( 'Click', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'tooltip_position',
			array(
				'label'   => __( 'Position', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom',
				'options' => array(
					'top'    => __( 'Top', 'elementor-animatepro' ),
					'right'  => __( 'Right', 'elementor-animatepro' ),
					'bottom' => __( 'Bottom', 'elementor-animatepro' ),
					'left'   => __( 'Left', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'tooltip_gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 18,
					'unit' => 'px',
				),
			)
		);

		$this->add_responsive_control(
			'tooltip_alignment',
			array(
				'label'   => __( 'Alignment', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => array(
					'left'   => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-image-hotspot__tooltip' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => __( 'Image', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-hotspot__media, {{WRAPPER}} .eap-image-hotspot__media img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} .eap-image-hotspot__media',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_hotspot_style',
			array(
				'label' => __( 'Hotspot', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'hotspot_background',
				'selector' => '{{WRAPPER}} .eap-image-hotspot__button',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'hotspot_border',
				'selector' => '{{WRAPPER}} .eap-image-hotspot__button',
			)
		);

		$this->add_responsive_control(
			'hotspot_border_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-hotspot__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hotspot_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-hotspot__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hotspot_dot_heading',
			array(
				'label'     => __( 'Dot', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'hotspot_dot_width',
			array(
				'label'      => __( 'Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 28,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-hotspot__button' => 'width: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'hotspot_dot_height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 28,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-hotspot__button' => 'height: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'hotspot_outer_bg_color',
			array(
				'label'     => __( 'Outer Circle Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-image-hotspot__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hotspot_outer_bg_hover_color',
			array(
				'label'     => __( 'Outer Circle Background Hover', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-image-hotspot__button:hover, {{WRAPPER}} .eap-image-hotspot__item.is-active .eap-image-hotspot__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hotspot_text_heading',
			array(
				'label'     => __( 'Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'hotspot_text_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-image-hotspot__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'hotspot_text_typography',
				'selector' => '{{WRAPPER}} .eap-image-hotspot__label',
			)
		);

		$this->add_control(
			'hotspot_icon_heading',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'hotspot_icon_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-image-hotspot__button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-image-hotspot__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-image-hotspot__icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-image-hotspot__dot-core' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eap-image-hotspot__icon svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .eap-image-hotspot__icon svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hotspot_icon_hover_color',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-image-hotspot__button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-image-hotspot__button:hover .eap-image-hotspot__icon, {{WRAPPER}} .eap-image-hotspot__item.is-active .eap-image-hotspot__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-image-hotspot__button:hover .eap-image-hotspot__icon i, {{WRAPPER}} .eap-image-hotspot__item.is-active .eap-image-hotspot__icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-image-hotspot__button:hover .eap-image-hotspot__icon svg, {{WRAPPER}} .eap-image-hotspot__item.is-active .eap-image-hotspot__icon svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .eap-image-hotspot__button:hover .eap-image-hotspot__icon svg *, {{WRAPPER}} .eap-image-hotspot__item.is-active .eap-image-hotspot__icon svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
					'{{WRAPPER}} .eap-image-hotspot__button:hover .eap-image-hotspot__dot-core, {{WRAPPER}} .eap-image-hotspot__item.is-active .eap-image-hotspot__dot-core' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'hotspot_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 6,
						'max' => 72,
					),
				),
				'default'    => array(
					'size' => 14,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-hotspot__icon' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .eap-image-hotspot__icon svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
					'{{WRAPPER}} .eap-image-hotspot__dot-core' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tooltip_style',
			array(
				'label' => __( 'Tooltip', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'tooltip_background',
				'selector' => '{{WRAPPER}} .eap-image-hotspot__tooltip',
			)
		);

		$this->add_responsive_control(
			'tooltip_width',
			array(
				'label'      => __( 'Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 120,
						'max' => 520,
					),
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 300,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-hotspot__tooltip' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'tooltip_border',
				'selector' => '{{WRAPPER}} .eap-image-hotspot__tooltip',
			)
		);

		$this->add_responsive_control(
			'tooltip_border_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-hotspot__tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tooltip_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-hotspot__tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'tooltip_shadow',
				'selector' => '{{WRAPPER}} .eap-image-hotspot__tooltip',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tooltip_heading_style',
			array(
				'label' => __( 'Tooltip Heading', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'tooltip_heading_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-image-hotspot__tooltip-heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tooltip_heading_typography',
				'selector' => '{{WRAPPER}} .eap-image-hotspot__tooltip-heading',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tooltip_description_style',
			array(
				'label' => __( 'Tooltip Description', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'tooltip_description_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-image-hotspot__tooltip-description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tooltip_description_typography',
				'selector' => '{{WRAPPER}} .eap-image-hotspot__tooltip-description',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tooltip_content_style',
			array(
				'label' => __( 'Tooltip Content', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'tooltip_content_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-image-hotspot__tooltip-content, {{WRAPPER}} .eap-image-hotspot__tooltip-content p, {{WRAPPER}} .eap-image-hotspot__tooltip-content li, {{WRAPPER}} .eap-image-hotspot__tooltip-content a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tooltip_content_typography',
				'selector' => '{{WRAPPER}} .eap-image-hotspot__tooltip-content, {{WRAPPER}} .eap-image-hotspot__tooltip-content p, {{WRAPPER}} .eap-image-hotspot__tooltip-content li',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$hotspots = ! empty( $settings['hotspots'] ) && is_array( $settings['hotspots'] ) ? $settings['hotspots'] : array();

		if ( empty( $settings['image']['url'] ) ) {
			return;
		}

		$tooltip_gap       = ! empty( $settings['tooltip_gap']['size'] ) ? $settings['tooltip_gap']['size'] . $settings['tooltip_gap']['unit'] : '18px';
		$animation_speed   = ! empty( $settings['animation_speed'] ) ? (float) $settings['animation_speed'] . 's' : '3s';
		$pulse_color       = ! empty( $settings['pulse_color'] ) ? $settings['pulse_color'] : 'rgba(255, 107, 53, 0.35)';
		$animation_class   = ! empty( $settings['hotspot_animation'] ) ? 'eap-image-hotspot--' . $settings['hotspot_animation'] : 'eap-image-hotspot--pulse';
		$position_class    = ! empty( $settings['tooltip_position'] ) ? 'eap-image-hotspot--tooltip-' . $settings['tooltip_position'] : 'eap-image-hotspot--tooltip-bottom';
		$alignment_class   = ! empty( $settings['tooltip_alignment'] ) ? 'eap-image-hotspot--align-' . $settings['tooltip_alignment'] : 'eap-image-hotspot--align-left';
		$trigger           = ! empty( $settings['tooltip_trigger'] ) ? $settings['tooltip_trigger'] : 'hover';

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'                     => array(
					'eap-widget',
					'eap-image-hotspot',
					$animation_class,
					$position_class,
					$alignment_class,
					'eap-image-hotspot--hover-' . $settings['hover_effect'],
					'eap-image-hotspot--unfold-' . $settings['unfold_direction'],
				),
				'data-eap-hotspot-trigger'  => $trigger,
				'style'                     => '--eap-hotspot-tooltip-gap: ' . esc_attr( $tooltip_gap ) . '; --eap-hotspot-animation-speed: ' . esc_attr( $animation_speed ) . '; --eap-hotspot-pulse-color: ' . esc_attr( $pulse_color ) . ';',
			)
		);
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="eap-image-hotspot__media">
				<?php echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $settings, 'image_size', 'image' ) ); ?>
			</div>
			<?php foreach ( $hotspots as $index => $hotspot ) : ?>
				<?php echo $this->render_hotspot_item( $hotspot, $index, $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endforeach; ?>
		</div>
		<?php
	}

	protected function render_hotspot_item( $hotspot, $index, $settings ) {
		$layout          = ! empty( $hotspot['hotspot_layout'] ) ? $hotspot['hotspot_layout'] : 'default';
		$tooltip_type    = ! empty( $hotspot['tooltip_type'] ) ? $hotspot['tooltip_type'] : 'content';
		$position_x      = isset( $hotspot['position_x']['size'] ) ? $hotspot['position_x']['size'] : 50;
		$position_y      = isset( $hotspot['position_y']['size'] ) ? $hotspot['position_y']['size'] : 50;
		$item_classes    = array(
			'eap-image-hotspot__item',
			'eap-image-hotspot__item--' . $layout,
		);
		$button_label    = ! empty( $hotspot['hotspot_label'] ) ? $hotspot['hotspot_label'] : sprintf( 'Hotspot %d', $index + 1 );
		$content_markup  = '';

		if ( 'icon' === $layout ) {
			$icon_html      = $this->build_hotspot_icon_markup( isset( $hotspot['hotspot_icon'] ) ? $hotspot['hotspot_icon'] : array() );
			$content_markup = '<span class="eap-image-hotspot__icon">' . $icon_html . '</span>';
		} elseif ( 'text' === $layout ) {
			$content_markup = '<span class="eap-image-hotspot__label">' . esc_html( $button_label ) . '</span>';
		} else {
			$content_markup = '<span class="eap-image-hotspot__dot-core"></span>';
		}

		$tooltip_markup = '';

		if ( 'simple' === $tooltip_type ) {
			$tooltip_markup .= '<div class="eap-image-hotspot__tooltip-inner">';
			if ( ! empty( $hotspot['tooltip_heading'] ) ) {
				$tooltip_markup .= '<div class="eap-image-hotspot__tooltip-heading">' . esc_html( $hotspot['tooltip_heading'] ) . '</div>';
			}
			if ( ! empty( $hotspot['tooltip_description'] ) ) {
				$tooltip_markup .= '<div class="eap-image-hotspot__tooltip-description">' . esc_html( $hotspot['tooltip_description'] ) . '</div>';
			}
			$tooltip_markup .= '</div>';
		} else {
			$tooltip_markup .= '<div class="eap-image-hotspot__tooltip-content">' . wp_kses_post( $hotspot['tooltip_content'] ) . '</div>';
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>" style="left: <?php echo esc_attr( $position_x ); ?>%; top: <?php echo esc_attr( $position_y ); ?>%;" data-eap-hotspot-item>
			<button class="eap-image-hotspot__button" type="button" aria-expanded="false" aria-label="<?php echo esc_attr( $button_label ); ?>">
				<?php echo wp_kses( $content_markup, $this->get_hotspot_button_allowed_html() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
			<div class="eap-image-hotspot__tooltip" data-eap-hotspot-tooltip>
				<?php echo wp_kses_post( $tooltip_markup ); ?>
			</div>
		</div>
		<?php

		return (string) ob_get_clean();
	}

	protected function build_hotspot_icon_markup( $icon ) {
		$icon_html = '';

		if ( ! empty( $icon ) ) {
			ob_start();
			Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
			$icon_html = trim( (string) ob_get_clean() );
		}

		if ( '' !== $icon_html ) {
			return $icon_html;
		}

		$icon_value = '';

		if ( is_array( $icon ) && ! empty( $icon['value'] ) ) {
			if ( is_string( $icon['value'] ) ) {
				$icon_value = trim( $icon['value'] );
			} elseif ( is_array( $icon['value'] ) && ! empty( $icon['value']['id'] ) ) {
				$icon_value = trim( (string) $icon['value']['id'] );
			}
		} elseif ( is_string( $icon ) ) {
			$icon_value = trim( $icon );
		}

		if ( '' !== $icon_value ) {
			return '<i class="' . esc_attr( $icon_value ) . '" aria-hidden="true"></i>';
		}

		return '<span class="eap-image-hotspot__icon-fallback">+</span>';
	}

	protected function get_hotspot_button_allowed_html() {
		return array(
			'span' => array(
				'class' => true,
				'style' => true,
				'aria-hidden' => true,
			),
			'i'    => array(
				'class' => true,
				'style' => true,
				'aria-hidden' => true,
			),
			'svg'  => array(
				'class' => true,
				'xmlns' => true,
				'width' => true,
				'height' => true,
				'viewBox' => true,
				'viewbox' => true,
				'aria-hidden' => true,
				'role' => true,
				'focusable' => true,
				'fill' => true,
				'stroke' => true,
			),
			'path' => array(
				'd' => true,
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
				'stroke-linejoin' => true,
				'transform' => true,
			),
			'g'    => array(
				'fill' => true,
				'stroke' => true,
				'transform' => true,
			),
			'circle' => array(
				'cx' => true,
				'cy' => true,
				'r' => true,
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
			),
			'rect' => array(
				'x' => true,
				'y' => true,
				'width' => true,
				'height' => true,
				'rx' => true,
				'ry' => true,
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
			),
			'polygon' => array(
				'points' => true,
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
			),
			'line' => array(
				'x1' => true,
				'y1' => true,
				'x2' => true,
				'y2' => true,
				'stroke' => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
			),
			'polyline' => array(
				'points' => true,
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
				'stroke-linejoin' => true,
			),
		);
	}
}
