<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

class EAP_Widget_Animated_Button extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-advanced-button';
	}

	public function get_title() {
		return __( 'Advanced Button', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'advanced-button' );
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
				'label'   => __( 'Button Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'classic',
				'options' => array(
					'classic'      => __( 'Classic Button', 'elementor-animatepro' ),
					'circle'       => __( 'Circle Button', 'elementor-animatepro' ),
					'text-swap'    => __( 'Text Swap Button', 'elementor-animatepro' ),
					'icon-motion'  => __( 'Icon Motion Button', 'elementor-animatepro' ),
					'surface-fill' => __( 'Surface Fill Button', 'elementor-animatepro' ),
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
			'text',
			array(
				'label'       => __( 'Button Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Let’s talk', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'hover_text',
			array(
				'label'       => __( 'Hover Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Start now', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array(
					'layout' => 'text-swap',
				),
			)
		);

		$this->add_control(
			'selected_icon',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'layout' => array( 'icon-motion', 'surface-fill' ),
				),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'     => __( 'Icon Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => array(
					'before' => __( 'Before Text', 'elementor-animatepro' ),
					'after'  => __( 'After Text', 'elementor-animatepro' ),
				),
				'condition' => array(
					'layout' => 'icon-motion',
				),
			)
		);

		$this->add_control(
			'classic_hover_effect',
			array(
				'label'     => __( 'Hover Effect', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'rollover',
				'options'   => array(
					'none'     => __( 'None', 'elementor-animatepro' ),
					'rollover' => __( 'Rollover Effect', 'elementor-animatepro' ),
				),
				'condition' => array(
					'layout' => 'classic',
				),
			)
		);

		$this->add_control(
			'classic_direction',
			array(
				'label'     => __( 'Effect Direction', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => array(
					'left'   => __( 'Left to Right', 'elementor-animatepro' ),
					'right'  => __( 'Right to Left', 'elementor-animatepro' ),
					'top'    => __( 'Top to Bottom', 'elementor-animatepro' ),
					'bottom' => __( 'Bottom to Top', 'elementor-animatepro' ),
				),
				'condition' => array(
					'layout'                => 'classic',
					'classic_hover_effect!' => 'none',
				),
			)
		);

		$this->add_control(
			'circle_origin',
			array(
				'label'     => __( 'Fill Origin', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top-right',
				'options'   => array(
					'top-left'     => __( 'Top Left', 'elementor-animatepro' ),
					'top-right'    => __( 'Top Right', 'elementor-animatepro' ),
					'bottom-left'  => __( 'Bottom Left', 'elementor-animatepro' ),
					'bottom-right' => __( 'Bottom Right', 'elementor-animatepro' ),
				),
				'condition' => array(
					'layout' => 'circle',
				),
			)
		);

		$this->add_control(
			'underline_direction',
			array(
				'label'     => __( 'Underline Direction', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => array(
					'left'   => __( 'Left to Right', 'elementor-animatepro' ),
					'right'  => __( 'Right to Left', 'elementor-animatepro' ),
					'center' => __( 'Center Out', 'elementor-animatepro' ),
				),
				'condition' => array(
					'layout' => 'underline',
				),
			)
		);

		$this->add_control(
			'text_swap_animation',
			array(
				'label'     => __( 'Text Change Animation', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'vertical',
				'options'   => array(
					'vertical'   => __( 'Vertical Slide', 'elementor-animatepro' ),
					'horizontal' => __( 'Horizontal Slide', 'elementor-animatepro' ),
					'fade'       => __( 'Fade Swap', 'elementor-animatepro' ),
				),
				'condition' => array(
					'layout' => 'text-swap',
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
					'layout' => 'surface-fill',
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
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 18,
					'unit' => 'px',
				),
				'condition'  => array(
					'layout' => 'surface-fill',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-accent-offset-x: {{SIZE}}{{UNIT}};',
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
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 18,
					'unit' => 'px',
				),
				'condition'  => array(
					'layout' => 'surface-fill',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-accent-offset-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'link',
			array(
				'label'         => __( 'Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url' => '#',
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
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
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button-wrap' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_starter_animation_controls() {
		$this->start_controls_section(
			'section_starter_animation',
			array(
				'label' => __( 'Starter Animations', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'starter_animation',
			array(
				'label'   => __( 'Animation', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'       => __( 'None', 'elementor-animatepro' ),
					'fade-up'    => __( 'Fade Up', 'elementor-animatepro' ),
					'fade-left'  => __( 'Fade Left', 'elementor-animatepro' ),
					'fade-right' => __( 'Fade Right', 'elementor-animatepro' ),
					'slide-up'   => __( 'Slide Up', 'elementor-animatepro' ),
					'scale-in'   => __( 'Scale In', 'elementor-animatepro' ),
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
				'selector' => '{{WRAPPER}} .eap-advanced-button__text, {{WRAPPER}} .eap-advanced-button__swap-text',
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-text: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_hover_text_color',
			array(
				'label'     => __( 'Hover Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-text-hover: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_hover_background',
			array(
				'label'     => __( 'Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff6b2c',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-bg-hover: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border',
				'selector' => '{{WRAPPER}} .eap-advanced-button',
				'condition' => array(
					'layout!' => 'underline',
				),
			)
		);

		$this->add_control(
			'button_hover_border_color',
			array(
				'label'     => __( 'Hover Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-border-hover: {{VALUE}};',
				),
				'condition' => array(
					'layout!' => 'underline',
				),
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'layout!' => 'circle',
				),
			)
		);

		$this->add_responsive_control(
			'button_min_width',
			array(
				'label'      => __( 'Min Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 40,
						'max' => 720,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button' => 'min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'circle_size',
			array(
				'label'      => __( 'Circle Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 70,
						'max' => 420,
					),
				),
				'default'    => array(
					'size' => 180,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button--circle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; padding: 0;',
				),
				'condition'  => array(
					'layout' => 'circle',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .eap-advanced-button',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_underline_style',
			array(
				'label'     => __( 'Underline', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => 'underline',
				),
			)
		);

		$this->add_control(
			'underline_color',
			array(
				'label'     => __( 'Underline Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-underline: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'underline_height',
			array(
				'label'      => __( 'Underline Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'default'    => array(
					'size' => 2,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-underline-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'underline_gap',
			array(
				'label'      => __( 'Underline Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 24,
					),
				),
				'default'    => array(
					'size' => 4,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-underline-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => array( 'icon-motion', 'surface-fill' ),
				),
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
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 18,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button__icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-advanced-button__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-icon: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => __( 'Icon Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-icon-hover: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_background',
			array(
				'label'     => __( 'Icon Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-icon-bg: {{VALUE}};',
				),
				'condition' => array(
					'layout' => array( 'icon-motion', 'surface-fill' ),
				),
			)
		);

		$this->add_control(
			'icon_hover_background',
			array(
				'label'     => __( 'Icon Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-icon-hover-bg: {{VALUE}};',
				),
				'condition' => array(
					'layout' => array( 'icon-motion', 'surface-fill' ),
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
						'max' => 140,
					),
				),
				'default'    => array(
					'size' => 44,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-icon-box-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'layout' => array( 'icon-motion', 'surface-fill' ),
				),
			)
		);

		$this->add_responsive_control(
			'icon_radius',
			array(
				'label'      => __( 'Icon Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button__icon-shell' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'layout' => array( 'icon-motion', 'surface-fill' ),
				),
			)
		);

		$this->add_control(
			'icon_hover_scale',
			array(
				'label'     => __( 'Icon Hover Scale', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'step'      => 0.01,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-icon-scale-hover: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_rotate',
			array(
				'label'     => __( 'Icon Hover Rotate', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-icon-rotate-hover: {{VALUE}}deg;',
				),
			)
		);

		$this->add_control(
			'icon_hover_offset_x',
			array(
				'label'     => __( 'Icon Hover Offset X', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-icon-offset-x-hover: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'icon_hover_offset_y',
			array(
				'label'     => __( 'Icon Hover Offset Y', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button' => '--eap-ab-icon-offset-y-hover: {{VALUE}}px;',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings          = $this->get_settings_for_display();
		$layout            = ! empty( $settings['layout'] ) ? $settings['layout'] : 'classic';
		$starter_animation = ! empty( $settings['starter_animation'] ) ? $settings['starter_animation'] : 'none';
		$button_classes    = array(
			'eap-widget',
			'eap-advanced-button-wrap',
			'eap-advanced-button-wrap--' . $layout,
		);
		$link_classes      = array(
			'eap-advanced-button',
			'eap-advanced-button--' . $layout,
		);

		if ( 'none' !== $starter_animation ) {
			$button_classes[] = 'eap-advanced-button-wrap--animate-' . $starter_animation;
		}

		if ( 'classic' === $layout && ! empty( $settings['classic_hover_effect'] ) ) {
			if ( 'cross' === $settings['classic_hover_effect'] ) {
				$settings['classic_hover_effect'] = 'rollover';
			}
			$link_classes[] = 'eap-advanced-button--effect-' . $settings['classic_hover_effect'];
		}

		if ( 'surface-fill' === $layout && ! empty( $settings['accent_position'] ) ) {
			$link_classes[] = 'eap-advanced-button--accent-' . $settings['accent_position'];
		}

		$this->add_render_attribute(
			'wrap',
			array(
				'class'                => $button_classes,
				'data-eap-anim-duration' => ! empty( $settings['animation_duration'] ) ? absint( $settings['animation_duration'] ) : 850,
				'data-eap-anim-delay'    => ! empty( $settings['animation_delay'] ) ? absint( $settings['animation_delay'] ) : 0,
			)
		);

		$this->add_render_attribute(
			'link',
			array(
				'class' => $link_classes,
			)
		);

		if ( ! empty( $settings['classic_direction'] ) ) {
			$this->add_render_attribute( 'link', 'data-eap-direction', $settings['classic_direction'] );
		}

		if ( ! empty( $settings['circle_origin'] ) ) {
			$this->add_render_attribute( 'link', 'data-eap-origin', $settings['circle_origin'] );
		}

		if ( ! empty( $settings['underline_direction'] ) ) {
			$this->add_render_attribute( 'link', 'data-eap-underline-direction', $settings['underline_direction'] );
		}

		if ( ! empty( $settings['text_swap_animation'] ) ) {
			$this->add_render_attribute( 'link', 'data-eap-swap-animation', $settings['text_swap_animation'] );
		}

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['link'] );
		} else {
			$this->add_render_attribute( 'link', 'href', '#' );
		}

		$icon_html = '';
		if ( ! empty( $settings['selected_icon']['value'] ) ) {
			ob_start();
			Icons_Manager::render_icon(
				$settings['selected_icon'],
				array(
					'aria-hidden' => 'true',
				)
			);
			$icon_html = ob_get_clean();
		}
		?>
		<div <?php $this->print_render_attribute_string( 'wrap' ); ?>>
			<a <?php $this->print_render_attribute_string( 'link' ); ?>>
				<?php if ( 'surface-fill' === $layout && $icon_html ) : ?>
					<span class="eap-advanced-button__cover" aria-hidden="true"></span>
					<span class="eap-advanced-button__icon-shell" aria-hidden="true">
						<span class="eap-advanced-button__icon"><?php echo wp_kses( $icon_html, $this->get_button_icon_allowed_html() ); ?></span>
					</span>
				<?php endif; ?>

				<span class="eap-advanced-button__content">
					<?php if ( 'icon-motion' === $layout && 'before' === $settings['icon_position'] && $icon_html ) : ?>
						<span class="eap-advanced-button__icon-shell" aria-hidden="true">
							<span class="eap-advanced-button__icon"><?php echo wp_kses( $icon_html, $this->get_button_icon_allowed_html() ); ?></span>
						</span>
					<?php endif; ?>

					<?php if ( 'text-swap' === $layout ) : ?>
						<span class="eap-advanced-button__swap">
							<span class="eap-advanced-button__swap-text eap-advanced-button__swap-text--primary"><?php echo esc_html( $settings['text'] ); ?></span>
							<span class="eap-advanced-button__swap-text eap-advanced-button__swap-text--secondary"><?php echo esc_html( ! empty( $settings['hover_text'] ) ? $settings['hover_text'] : $settings['text'] ); ?></span>
						</span>
					<?php else : ?>
						<span class="eap-advanced-button__text"><?php echo esc_html( $settings['text'] ); ?></span>
					<?php endif; ?>

					<?php if ( 'icon-motion' === $layout && 'after' === $settings['icon_position'] && $icon_html ) : ?>
						<span class="eap-advanced-button__icon-shell" aria-hidden="true">
							<span class="eap-advanced-button__icon"><?php echo wp_kses( $icon_html, $this->get_button_icon_allowed_html() ); ?></span>
						</span>
					<?php endif; ?>
				</span>
			</a>
		</div>
		<?php
	}

	private function get_button_icon_allowed_html() {
		return array(
			'span' => array(
				'class'       => true,
				'aria-hidden' => true,
			),
			'i'    => array(
				'class'       => true,
				'aria-hidden' => true,
			),
			'svg'  => array(
				'class'        => true,
				'aria-hidden'  => true,
				'aria-label'   => true,
				'role'         => true,
				'xmlns'        => true,
				'width'        => true,
				'height'       => true,
				'viewBox'      => true,
				'viewbox'      => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'focusable'    => true,
			),
			'path' => array(
				'd'            => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
				'stroke-linejoin' => true,
			),
			'g'    => array(
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'transform'    => true,
			),
			'circle' => array(
				'cx'           => true,
				'cy'           => true,
				'r'            => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
			),
			'rect' => array(
				'x'            => true,
				'y'            => true,
				'rx'           => true,
				'ry'           => true,
				'width'        => true,
				'height'       => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
			),
			'line' => array(
				'x1'           => true,
				'x2'           => true,
				'y1'           => true,
				'y2'           => true,
				'stroke'       => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
			),
			'polyline' => array(
				'points'       => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
				'stroke-linejoin' => true,
			),
			'polygon' => array(
				'points'       => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
				'stroke-linejoin' => true,
			),
		);
	}
}
