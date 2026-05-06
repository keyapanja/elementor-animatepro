<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

class EAP_Widget_Icon_Box extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-icon-box';
	}

	public function get_title() {
		return __( 'Icon Box', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-icon-box';
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'icon-box' );
	}

	public function get_script_depends() {
		return $this->get_visibility_script_depends();
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_starter_animation_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'simple',
				'options' => array(
					'simple'         => __( 'Simple Icon Box', 'elementor-animatepro' ),
					'animated'       => __( 'Animated Icon Box', 'elementor-animatepro' ),
					'hover-surface'  => __( 'Hover Effect Icon Box', 'elementor-animatepro' ),
					'icon-on-hover'  => __( 'Icon On Hover', 'elementor-animatepro' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'selected_icon',
			array(
				'label'   => __( 'Main Icon', 'elementor-animatepro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-bolt',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Brand Strategy', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'description',
			array(
				'label'       => __( 'Description', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Designing and implementing online stores or e-commerce platforms that are secure, easy to navigate visually appealing.', 'elementor-animatepro' ),
				'rows'        => 5,
				'label_block' => true,
			)
		);

		$this->add_control(
			'link',
			array(
				'label'         => __( 'Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
			)
		);

		$this->add_control(
			'read_more_text',
			array(
				'label'       => __( 'Read More Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Learn more', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array(
					'layout' => 'animated',
				),
			)
		);

		$this->add_control(
			'read_more_icon',
			array(
				'label'     => __( 'Read More Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'layout' => 'animated',
				),
			)
		);

		$this->add_control(
			'accent_icon',
			array(
				'label'     => __( 'Accent Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'layout' => array( 'hover-surface', 'icon-on-hover' ),
				),
			)
		);

		$this->add_control(
			'accent_position',
			array(
				'label'     => __( 'Accent Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top-right',
				'options'   => array(
					'top-left'     => __( 'Top Left', 'elementor-animatepro' ),
					'top-right'    => __( 'Top Right', 'elementor-animatepro' ),
					'bottom-left'  => __( 'Bottom Left', 'elementor-animatepro' ),
					'bottom-right' => __( 'Bottom Right', 'elementor-animatepro' ),
				),
				'condition' => array(
					'layout' => array( 'hover-surface', 'icon-on-hover' ),
				),
			)
		);

		$this->add_responsive_control(
			'accent_offset_x',
			array(
				'label'      => __( 'Accent Offset X', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 24,
					'unit' => 'px',
				),
				'condition'  => array(
					'layout' => array( 'hover-surface', 'icon-on-hover' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-icon-box-card' => '--eap-icon-box-accent-offset-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'accent_offset_y',
			array(
				'label'      => __( 'Accent Offset Y', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 24,
					'unit' => 'px',
				),
				'condition'  => array(
					'layout' => array( 'hover-surface', 'icon-on-hover' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-icon-box-card' => '--eap-icon-box-accent-offset-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hover_icon_opacity',
			array(
				'label'      => __( 'Hover Icon Opacity', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '' ),
				'range'      => array(
					'' => array(
						'min' => 0.05,
						'max' => 1,
						'step' => 0.01,
					),
				),
				'default'    => array(
					'size' => 0.16,
				),
				'condition'  => array(
					'layout' => 'icon-on-hover',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-icon-box-card' => '--eap-icon-box-hover-icon-opacity: {{SIZE}};',
				),
			)
		);

		$this->add_alignment_control();

		$this->end_controls_section();
	}

	protected function register_starter_animation_controls() {
		$this->start_controls_section(
			'section_starter_animations',
			array(
				'label' => __( 'Starter Animations', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'starter_animation',
			array(
				'label'   => __( 'Animation', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide-up',
				'options' => array(
					'none'        => __( 'None', 'elementor-animatepro' ),
					'fade-up'     => __( 'Fade Up', 'elementor-animatepro' ),
					'fade-left'   => __( 'Fade Left', 'elementor-animatepro' ),
					'fade-right'  => __( 'Fade Right', 'elementor-animatepro' ),
					'slide-up'    => __( 'Slide Up', 'elementor-animatepro' ),
					'scale-in'    => __( 'Scale In', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'animation_duration',
			array(
				'label'     => __( 'Duration (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 850,
				'condition' => array(
					'starter_animation!' => 'none',
				),
			)
		);

		$this->add_control(
			'animation_delay',
			array(
				'label'     => __( 'Delay (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'condition' => array(
					'starter_animation!' => 'none',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'section_box_style',
			array(
				'label' => __( 'Box', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'box_background',
				'selector' => '{{WRAPPER}} .eap-icon-box-card',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .eap-icon-box-card',
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-icon-box-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-icon-box-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_min_height',
			array(
				'label'      => __( 'Min Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 120,
						'max' => 720,
					),
					'vh' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-icon-box-card' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .eap-icon-box-card',
			)
		);

		$this->add_control(
			'box_hover_background',
			array(
				'label'     => __( 'Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-card' => '--eap-icon-box-hover-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'box_hover_text_color',
			array(
				'label'     => __( 'Hover Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-card' => '--eap-icon-box-hover-text: {{VALUE}};',
				),
			)
		);

		$this->add_hover_transform_controls( 'box_hover', __( 'Box Hover', 'elementor-animatepro' ), '{{WRAPPER}} .eap-icon-box-card' );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			array(
				'label' => __( 'Icon', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 180,
					),
				),
				'default'    => array(
					'size' => 48,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-icon-box-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-icon-box-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-icon-box-icon svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-card' => '--eap-icon-box-icon-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_background',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_background',
			array(
				'label'     => __( 'Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-card' => '--eap-icon-box-icon-hover-bg: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_box_size',
			array(
				'label'      => __( 'Icon Box Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 24,
						'max' => 220,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-icon-box-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_box_radius',
			array(
				'label'      => __( 'Icon Box Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-icon-box-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_hover_transform_controls( 'icon_hover', __( 'Icon Hover', 'elementor-animatepro' ), '{{WRAPPER}} .eap-icon-box-icon' );

		$this->end_controls_section();

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
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_hover_color',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-card' => '--eap-icon-box-title-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-icon-box-title',
			)
		);

		$this->add_hover_transform_controls( 'title_hover', __( 'Title Hover', 'elementor-animatepro' ), '{{WRAPPER}} .eap-icon-box-title' );

		$this->end_controls_section();

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
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'description_hover_color',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-card' => '--eap-icon-box-description-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .eap-icon-box-description',
			)
		);

		$this->add_hover_transform_controls( 'description_hover', __( 'Description Hover', 'elementor-animatepro' ), '{{WRAPPER}} .eap-icon-box-description' );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_read_more_style',
			array(
				'label'     => __( 'Read More', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => 'animated',
				),
			)
		);

		$this->add_control(
			'read_more_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-readmore' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'read_more_hover_color',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-card' => '--eap-icon-box-readmore-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'read_more_typography',
				'selector' => '{{WRAPPER}} .eap-icon-box-readmore',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_accent_style',
			array(
				'label'     => __( 'Accent Icon', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => array( 'hover-surface', 'icon-on-hover' ),
				),
			)
		);

		$this->add_responsive_control(
			'accent_size',
			array(
				'label'      => __( 'Accent Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 160,
					),
				),
				'default'    => array(
					'size' => 18,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-icon-box-accent' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-icon-box-accent svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'accent_box_size',
			array(
				'label'      => __( 'Accent Box Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 24,
						'max' => 200,
					),
				),
				'default'    => array(
					'size' => 46,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-icon-box-accent' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-icon-box-card'   => '--eap-icon-box-accent-box-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'accent_color',
			array(
				'label'     => __( 'Accent Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-card' => '--eap-icon-box-accent-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'accent_hover_color',
			array(
				'label'     => __( 'Accent Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-card' => '--eap-icon-box-accent-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'accent_background',
			array(
				'label'     => __( 'Accent Icon Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-card' => '--eap-icon-box-accent-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'accent_hover_background',
			array(
				'label'     => __( 'Accent Icon Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-card' => '--eap-icon-box-accent-hover-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'accent_cover_background',
			array(
				'label'     => __( 'Box Cover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-icon-box-card' => '--eap-icon-box-accent-surface: {{VALUE}};',
				),
				'condition' => array(
					'layout' => 'hover-surface',
				),
			)
		);

		$this->add_responsive_control(
			'accent_radius',
			array(
				'label'      => __( 'Accent Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-icon-box-accent' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_hover_transform_controls( 'accent_hover', __( 'Accent Hover', 'elementor-animatepro' ), '{{WRAPPER}} .eap-icon-box-accent' );

		$this->end_controls_section();
	}

	protected function add_hover_transform_controls( $prefix, $label, $selector ) {
		$this->add_control(
			$prefix . '_heading',
			array(
				'label'     => $label,
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			$prefix . '_scale',
			array(
				'label'     => __( 'Scale', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'step'      => 0.01,
				'selectors' => array(
					$selector => '--' . $prefix . '-scale: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			$prefix . '_rotate',
			array(
				'label'     => __( 'Rotate', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'selectors' => array(
					$selector => '--' . $prefix . '-rotate: {{VALUE}}deg;',
				),
			)
		);

		$this->add_control(
			$prefix . '_offset_x',
			array(
				'label'     => __( 'Offset X', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'selectors' => array(
					$selector => '--' . $prefix . '-translate-x: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			$prefix . '_offset_y',
			array(
				'label'     => __( 'Offset Y', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'selectors' => array(
					$selector => '--' . $prefix . '-translate-y: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			$prefix . '_skew_x',
			array(
				'label'     => __( 'Skew X', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'selectors' => array(
					$selector => '--' . $prefix . '-skew-x: {{VALUE}}deg;',
				),
			)
		);

		$this->add_control(
			$prefix . '_skew_y',
			array(
				'label'     => __( 'Skew Y', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'selectors' => array(
					$selector => '--' . $prefix . '-skew-y: {{VALUE}}deg;',
				),
			)
		);
	}

	protected function render() {
		$settings        = $this->get_settings_for_display();
		$layout          = ! empty( $settings['layout'] ) ? $settings['layout'] : 'simple';
		$animation       = ! empty( $settings['starter_animation'] ) ? $settings['starter_animation'] : 'none';
		$wrapper_classes = array(
			'eap-widget',
			'eap-icon-box',
			'eap-icon-box--' . $layout,
		);

		if ( 'none' !== $animation ) {
			$wrapper_classes[] = 'eap-icon-box--animate-' . $animation;
		}

		$this->add_render_attribute(
			'wrapper',
			array(
				'class' => $wrapper_classes,
			)
		);

		$this->add_render_attribute(
			'card',
			array(
				'class' => array(
					'eap-icon-box-card',
					in_array( $layout, array( 'hover-surface', 'icon-on-hover' ), true ) ? 'eap-icon-box-card--accent-' . $settings['accent_position'] : '',
				),
				'style' => '--eap-icon-box-duration: ' . absint( ! empty( $settings['animation_duration'] ) ? $settings['animation_duration'] : 850 ) . 'ms; --eap-icon-box-delay: ' . absint( ! empty( $settings['animation_delay'] ) ? $settings['animation_delay'] : 0 ) . 'ms;',
			)
		);

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'card', $settings['link'] );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( ! empty( $settings['link']['url'] ) ) : ?>
				<a <?php echo $this->get_render_attribute_string( 'card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php else : ?>
				<div <?php echo $this->get_render_attribute_string( 'card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php endif; ?>

				<?php if ( 'hover-surface' === $layout ) : ?>
					<span class="eap-icon-box-accent eap-icon-box-accent--<?php echo esc_attr( $settings['accent_position'] ); ?>" aria-hidden="true">
						<?php Icons_Manager::render_icon( $settings['accent_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php endif; ?>

				<?php if ( 'icon-on-hover' === $layout ) : ?>
					<span class="eap-icon-box-hover-ghost eap-icon-box-hover-ghost--<?php echo esc_attr( $settings['accent_position'] ); ?>" aria-hidden="true">
						<?php Icons_Manager::render_icon( $settings['accent_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php endif; ?>

				<div class="eap-icon-box-inner">
					<div class="eap-icon-box-icon" aria-hidden="true">
						<?php Icons_Manager::render_icon( $settings['selected_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</div>

					<div class="eap-icon-box-content">
						<h3 class="eap-icon-box-title"><?php echo esc_html( $settings['title'] ); ?></h3>
						<div class="eap-icon-box-description"><?php echo esc_html( $settings['description'] ); ?></div>
						<?php if ( 'animated' === $layout && ! empty( $settings['read_more_text'] ) ) : ?>
							<span class="eap-icon-box-readmore">
								<span class="eap-icon-box-readmore-text"><?php echo esc_html( $settings['read_more_text'] ); ?></span>
								<?php if ( ! empty( $settings['read_more_icon']['value'] ) ) : ?>
									<span class="eap-icon-box-readmore-icon" aria-hidden="true">
										<?php Icons_Manager::render_icon( $settings['read_more_icon'], array( 'aria-hidden' => 'true' ) ); ?>
									</span>
								<?php endif; ?>
							</span>
						<?php endif; ?>
					</div>
				</div>

			<?php if ( ! empty( $settings['link']['url'] ) ) : ?>
				</a>
			<?php else : ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
