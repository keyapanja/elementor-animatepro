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
					'classic'           => __( 'Classic Button', 'elementor-animatepro' ),
					'circle'            => __( 'Circle Button', 'elementor-animatepro' ),
					'text-swap'         => __( 'Text Swap Button', 'elementor-animatepro' ),
					'icon-motion'       => __( 'Icon Motion Button', 'elementor-animatepro' ),
					'icon-motion-fill'  => __( 'Icon Motion + Fill Button', 'elementor-animatepro' ),
					'icon-expand'       => __( 'Icon Expand Button', 'elementor-animatepro' ),
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
					'layout' => array( 'icon-motion', 'icon-motion-fill', 'circle', 'icon-expand' ),
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
					'layout' => array( 'icon-motion', 'icon-motion-fill' ),
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
			'circle_text',
			array(
				'label'       => __( 'Orbital Text', 'elementor-animatepro' ),
				'description' => __( 'Text that rotates around the icon. Leave empty to fall back to the main button text.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( "Let's Talk Our Expert", 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array( 'layout' => 'circle' ),
			)
		);

		$this->add_control(
			'circle_connector',
			array(
				'label'     => __( 'Text Connector', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'plus',
				'options'   => array(
					'plus'     => __( '+ (Plus)', 'elementor-animatepro' ),
					'star'     => __( '★ (Star)', 'elementor-animatepro' ),
					'sparkle'  => __( '✦ (Sparkle)', 'elementor-animatepro' ),
					'asterisk' => __( '∗ (Asterisk)', 'elementor-animatepro' ),
					'bullet'   => __( '• (Bullet)', 'elementor-animatepro' ),
					'dot'      => __( '· (Mid Dot)', 'elementor-animatepro' ),
					'dash'     => __( '— (Em Dash)', 'elementor-animatepro' ),
					'slash'    => __( '/ (Slash)', 'elementor-animatepro' ),
					'pipe'     => __( '| (Pipe)', 'elementor-animatepro' ),
					'arrow'    => __( '→ (Arrow)', 'elementor-animatepro' ),
					'space'    => __( '  (Spaces Only)', 'elementor-animatepro' ),
					'custom'   => __( 'Custom…', 'elementor-animatepro' ),
				),
				'condition' => array( 'layout' => 'circle' ),
			)
		);

		$this->add_control(
			'circle_connector_custom',
			array(
				'label'     => __( 'Custom Connector', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '•',
				'condition' => array(
					'layout'           => 'circle',
					'circle_connector' => 'custom',
				),
			)
		);

		$this->add_control(
			'circle_repetitions',
			array(
				'label'     => __( 'Text Repetitions', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 6,
				'default'   => 2,
				'condition' => array( 'layout' => 'circle' ),
			)
		);

		$this->add_control(
			'circle_rotation_speed',
			array(
				'label'       => __( 'Rotation Duration (seconds)', 'elementor-animatepro' ),
				'description' => __( 'Time for one full rotation. Higher = slower.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 2, 'max' => 60, 'step' => 0.5 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 14 ),
				'condition'   => array( 'layout' => 'circle' ),
				'selectors'   => array(
					'{{WRAPPER}} .eap-advanced-button--circle' => '--eap-ab-orbit-speed: {{SIZE}}s;',
				),
			)
		);

		$this->add_control(
			'circle_rotation_direction',
			array(
				'label'     => __( 'Rotation Direction', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cw',
				'options'   => array(
					'cw'  => __( 'Clockwise', 'elementor-animatepro' ),
					'ccw' => __( 'Counter-Clockwise', 'elementor-animatepro' ),
				),
				'condition' => array( 'layout' => 'circle' ),
			)
		);

		$this->add_control(
			'circle_pause_on_hover',
			array(
				'label'     => __( 'Pause Rotation on Hover', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array( 'layout' => 'circle' ),
			)
		);

		$this->add_control(
			'circle_icon_hover_effect',
			array(
				'label'     => __( 'Icon Hover Effect', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'diagonal',
				'options'   => array(
					'none'     => __( 'None', 'elementor-animatepro' ),
					'rotate'   => __( 'Rotate', 'elementor-animatepro' ),
					'scale'    => __( 'Scale Up', 'elementor-animatepro' ),
					'diagonal' => __( 'Diagonal Slide (Out & In)', 'elementor-animatepro' ),
					'pulse'    => __( 'Pulse', 'elementor-animatepro' ),
				),
				'condition' => array( 'layout' => 'circle' ),
			)
		);

		$this->add_control(
			'circle_rotate_degrees',
			array(
				'label'       => __( 'Rotation Degrees', 'elementor-animatepro' ),
				'description' => __( 'How far the icon rotates on hover.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'deg' ),
				'range'       => array( 'deg' => array( 'min' => -360, 'max' => 360, 'step' => 5 ) ),
				'default'     => array( 'unit' => 'deg', 'size' => 45 ),
				'condition'   => array(
					'layout'                  => 'circle',
					'circle_icon_hover_effect' => 'rotate',
				),
				'selectors'   => array(
					'{{WRAPPER}} .eap-advanced-button--circle' => '--eap-ab-orbit-icon-rotate: {{SIZE}}{{UNIT}};',
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
					'layout' => 'icon-motion-fill',
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
					'layout' => 'icon-motion-fill',
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
					'layout' => 'icon-motion-fill',
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
					'layout!' => array( 'circle', 'icon-expand' ),
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
				'condition'  => array(
					'layout!' => array( 'circle', 'icon-expand' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'button_shadow',
				'selector'  => '{{WRAPPER}} .eap-advanced-button',
				'condition' => array( 'layout!' => array( 'circle', 'icon-expand' ) ),
			)
		);

		$this->end_controls_section();

		$this->register_circle_style_controls();
		$this->register_icon_expand_style_controls();

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
					'layout' => array( 'icon-motion', 'icon-motion-fill' ),
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
					'layout' => array( 'icon-motion', 'icon-motion-fill' ),
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
					'layout' => array( 'icon-motion', 'icon-motion-fill' ),
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
					'layout' => array( 'icon-motion', 'icon-motion-fill' ),
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
					'layout' => array( 'icon-motion', 'icon-motion-fill' ),
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

	/**
	 * Circle Button — dedicated style section.
	 * All controls show only when Layout = Circle. Provides full control over
	 * outer ring, inner disc, icon, orbital text, hover states, and effects.
	 */
	private function register_circle_style_controls() {
		$this->start_controls_section(
			'section_circle_style',
			array(
				'label'     => __( 'Circle Button', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'circle' ),
			)
		);

		/* --- Sizes --- */
		$this->add_responsive_control(
			'circle_outer_size',
			array(
				'label'      => __( 'Outer Ring Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 120, 'max' => 600 ) ),
				'default'    => array( 'size' => 240, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button--circle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; padding: 0;',
				),
			)
		);

		$this->add_responsive_control(
			'circle_inner_size',
			array(
				'label'      => __( 'Inner Disc Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 40, 'max' => 400 ) ),
				'default'    => array( 'size' => 96, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button--circle' => '--eap-ab-orbit-inner-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'circle_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 12, 'max' => 80 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button--circle .eap-advanced-button__orbit-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-advanced-button--circle .eap-advanced-button__orbit-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'circle_outer_padding_heading',
			array(
				'label'     => __( 'Outer Ring', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		/* --- Outer ring states (Normal / Hover) --- */
		$this->start_controls_tabs( 'circle_outer_states' );

		$this->start_controls_tab( 'circle_outer_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'circle_ring_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button--circle' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'circle_text_color',
			array(
				'label'     => __( 'Orbital Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button--circle .eap-advanced-button__orbit-text' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'circle_outer_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'circle_ring_hover_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button--circle:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'circle_text_hover_color',
			array(
				'label'     => __( 'Orbital Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button--circle:hover .eap-advanced-button__orbit-text' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'circle_text_typography',
				'label'    => __( 'Orbital Text Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-advanced-button--circle .eap-advanced-button__orbit-text',
				// Font size is intentionally hidden — SVG text size scales automatically
				// with the outer ring (font-size is in viewBox user units).
				'exclude'  => array( 'font_size' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'circle_outer_border',
				'label'    => __( 'Outer Ring Border', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-advanced-button--circle',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'circle_outer_shadow',
				'label'    => __( 'Outer Ring Shadow', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-advanced-button--circle',
			)
		);

		/* --- Inner disc --- */
		$this->add_control(
			'circle_inner_heading',
			array(
				'label'     => __( 'Inner Disc', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'circle_inner_states' );

		$this->start_controls_tab( 'circle_inner_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'circle_inner_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button--circle .eap-advanced-button__orbit-inner' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'circle_icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button--circle .eap-advanced-button__orbit-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'circle_inner_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'circle_inner_hover_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button--circle:hover .eap-advanced-button__orbit-inner' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'circle_icon_hover_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button--circle:hover .eap-advanced-button__orbit-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'circle_inner_radius',
			array(
				'label'      => __( 'Inner Disc Radius', 'elementor-animatepro' ),
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
					'{{WRAPPER}} .eap-advanced-button--circle .eap-advanced-button__orbit-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'circle_inner_border',
				'label'    => __( 'Inner Disc Border', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-advanced-button--circle .eap-advanced-button__orbit-inner',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'circle_inner_shadow',
				'label'    => __( 'Inner Disc Shadow', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-advanced-button--circle .eap-advanced-button__orbit-inner',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Icon Expand Button — dedicated style section.
	 * Pill button with a small icon container that expands across the button
	 * on hover, while the text translates out.
	 */
	private function register_icon_expand_style_controls() {
		$this->start_controls_section(
			'section_icon_expand_style',
			array(
				'label'     => __( 'Icon Expand Button', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'icon-expand' ),
			)
		);

		/* --- Dimensions --- */
		$this->add_responsive_control(
			'ie_width',
			array(
				'label'      => __( 'Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 60, 'max' => 400 ) ),
				'default'    => array( 'size' => 100, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ie_height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 28, 'max' => 120 ) ),
				'default'    => array( 'size' => 40, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ie_text_width',
			array(
				'label'       => __( 'Text Width', 'elementor-animatepro' ),
				'description' => __( 'Fixed width of the text portion. Icon container + text are centered as a group inside the button.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 20, 'max' => 240 ) ),
				'default'     => array( 'size' => 60, 'unit' => 'px' ),
				'selectors'   => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand' => '--eap-ab-ie-text-w: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ie_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 999,
					'right'    => 999,
					'bottom'   => 999,
					'left'     => 999,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ie_transition_duration',
			array(
				'label'      => __( 'Transition Duration (ms)', 'elementor-animatepro' ),
				'type'       => Controls_Manager::NUMBER,
				'min'        => 50,
				'max'        => 1500,
				'step'       => 25,
				'default'    => 300,
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand' => '--eap-ab-ie-duration: {{VALUE}}ms;',
				),
			)
		);

		$this->add_control(
			'ie_active_scale',
			array(
				'label'   => __( 'Active (Click) Scale', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0.5,
				'max'     => 1,
				'step'    => 0.01,
				'default' => 0.95,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand' => '--eap-ab-ie-active-scale: {{VALUE}};',
				),
			)
		);

		/* --- Button surface --- */
		$this->add_control(
			'ie_button_heading',
			array(
				'label'     => __( 'Button Surface', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'ie_button_states' );

		$this->start_controls_tab( 'ie_button_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'ie_button_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0c0c0c',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand' => '--eap-ab-ie-bg: {{VALUE}}; background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ie_text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand .eap-advanced-button__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'ie_button_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'ie_button_hover_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand' => '--eap-ab-ie-bg-hover: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ie_text_hover_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand:hover .eap-advanced-button__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ie_text_typography',
				'label'    => __( 'Text Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-advanced-button--icon-expand .eap-advanced-button__text',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'ie_border',
				'selector' => '{{WRAPPER}} .eap-advanced-button--icon-expand',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'ie_shadow',
				'selector' => '{{WRAPPER}} .eap-advanced-button--icon-expand',
			)
		);

		/* --- Icon container (pill) --- */
		$this->add_control(
			'ie_pill_heading',
			array(
				'label'     => __( 'Icon Container', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'ie_pill_width',
			array(
				'label'      => __( 'Width (Default)', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 18, 'max' => 80 ) ),
				'default'    => array( 'size' => 30, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand' => '--eap-ab-ie-pill-w: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ie_pill_height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 18, 'max' => 100 ) ),
				'default'    => array( 'size' => 30, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand' => '--eap-ab-ie-pill-h: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ie_pill_width_hover',
			array(
				'label'       => __( 'Width (Hover)', 'elementor-animatepro' ),
				'description' => __( 'How wide the icon container becomes on hover. Use a value close to the button width for the full sweep effect.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array( 'min' => 40, 'max' => 400 ),
					'%'  => array( 'min' => 40, 'max' => 100 ),
				),
				'default'    => array( 'size' => 90, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand' => '--eap-ab-ie-pill-w-hover: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ie_pill_radius',
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
					'{{WRAPPER}} .eap-advanced-button--icon-expand .eap-advanced-button__icon-pill' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'ie_pill_bg',
				'label'    => __( 'Container Background', 'elementor-animatepro' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .eap-advanced-button--icon-expand .eap-advanced-button__icon-pill',
				'fields_options' => array(
					'background' => array(
						'default' => 'gradient',
					),
					'color' => array(
						'default' => '#ff88ff',
					),
					'color_b' => array(
						'default' => '#ac46ff',
					),
					'gradient_angle' => array(
						'default' => array( 'unit' => 'deg', 'size' => 180 ),
					),
				),
			)
		);

		/* --- Icon --- */
		$this->add_control(
			'ie_icon_heading',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'ie_icon_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 48 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand .eap-advanced-button__icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-advanced-button--icon-expand .eap-advanced-button__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ie_icon_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand .eap-advanced-button__icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ie_icon_hover_color',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-advanced-button--icon-expand:hover .eap-advanced-button__icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings          = $this->get_settings_for_display();
		$layout            = ! empty( $settings['layout'] ) ? $settings['layout'] : 'classic';
		// Legacy: surface-fill was removed as a standalone option and merged
		// into icon-motion-fill. Existing buttons continue to render correctly.
		if ( 'surface-fill' === $layout ) {
			$layout = 'icon-motion-fill';
		}
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

		if ( in_array( $layout, array( 'surface-fill', 'icon-motion-fill' ), true ) && ! empty( $settings['accent_position'] ) ) {
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

		if ( 'circle' === $layout ) {
			$link_classes[] = 'eap-advanced-button--orbit-' . ( ! empty( $settings['circle_rotation_direction'] ) ? esc_attr( $settings['circle_rotation_direction'] ) : 'cw' );
			if ( ! empty( $settings['circle_icon_hover_effect'] ) && 'none' !== $settings['circle_icon_hover_effect'] ) {
				$link_classes[] = 'eap-advanced-button--orbit-icon-' . esc_attr( $settings['circle_icon_hover_effect'] );
			}
			if ( ! empty( $settings['circle_pause_on_hover'] ) && 'yes' === $settings['circle_pause_on_hover'] ) {
				$link_classes[] = 'eap-advanced-button--orbit-pause';
			}
			// Refresh the link class attribute since we added classes after the
			// initial add_render_attribute call below would normally happen.
			$this->set_render_attribute( 'link', 'class', $link_classes );
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
		// Build orbital text payload for circle layout.
		$orbit_text_full = '';
		$orbit_path_id   = '';
		$orbit_path_d    = 'M 100,100 m -82,0 a 82,82 0 1,1 164,0 a 82,82 0 1,1 -164,0';
		if ( 'circle' === $layout ) {
			$base_text = trim( ! empty( $settings['circle_text'] ) ? $settings['circle_text'] : $settings['text'] );
			if ( '' !== $base_text ) {
				$connector_key = ! empty( $settings['circle_connector'] ) ? $settings['circle_connector'] : 'plus';
				$connector_map = array(
					'plus'     => ' + ',
					'star'     => ' ★ ',
					'sparkle'  => ' ✦ ',
					'asterisk' => ' ∗ ',
					'bullet'   => ' • ',
					'dot'      => ' · ',
					'dash'     => ' — ',
					'slash'    => ' / ',
					'pipe'     => ' | ',
					'arrow'    => ' → ',
					'space'    => '    ',
					'custom'   => ' ' . ( ! empty( $settings['circle_connector_custom'] ) ? $settings['circle_connector_custom'] : '•' ) . ' ',
				);
				$connector  = isset( $connector_map[ $connector_key ] ) ? $connector_map[ $connector_key ] : ' • ';
				$reps       = isset( $settings['circle_repetitions'] ) ? max( 1, min( 6, (int) $settings['circle_repetitions'] ) ) : 2;
				$segments   = array_fill( 0, $reps, $base_text );
				// Prepend AND append connector so the wrap point at the seam of
				// the rotating ring always shows a visible separator between the
				// last repetition's end and the first repetition's beginning.
				$orbit_text_full = $connector . implode( $connector, $segments ) . $connector;
				$orbit_path_id   = 'eap-orbit-' . esc_attr( $this->get_id() );

				// Auto-compute path radius so the text always sits visually centred
				// between the inner disc and the outer ring. viewBox is 200x200 —
				// outer radius in viewBox units = 100; inner radius is proportional
				// to inner_size / outer_size. Path baseline is then the midpoint,
				// nudged slightly inward (text characters extend outward from the
				// baseline along the path normal, so this keeps the visible band
				// centered).
				$outer_px = isset( $settings['circle_outer_size']['size'] )
					? (float) $settings['circle_outer_size']['size']
					: 240.0;
				$inner_px = isset( $settings['circle_inner_size']['size'] )
					? (float) $settings['circle_inner_size']['size']
					: 96.0;
				if ( $outer_px <= 0 ) {
					$outer_px = 240.0;
				}
				$inner_ratio    = max( 0.0, min( 0.95, $inner_px / $outer_px ) );
				$inner_radius_v = $inner_ratio * 100.0;
				$midpoint       = ( $inner_radius_v + 100.0 ) / 2.0;
				$radius         = (int) round( max( 30.0, min( 96.0, $midpoint - 4.0 ) ) );
				$radius_2x      = $radius * 2;
				$orbit_path_d   = sprintf(
					'M 100,100 m -%1$d,0 a %1$d,%1$d 0 1,1 %2$d,0 a %1$d,%1$d 0 1,1 -%2$d,0',
					$radius,
					$radius_2x
				);
			}
		}
		?>
		<div <?php $this->print_render_attribute_string( 'wrap' ); ?>>
			<a <?php $this->print_render_attribute_string( 'link' ); ?>>
				<?php if ( 'circle' === $layout ) : ?>
					<?php if ( '' !== $orbit_text_full ) : ?>
						<span class="eap-advanced-button__orbit-ring" aria-hidden="true">
							<svg class="eap-advanced-button__orbit-svg" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
								<defs>
									<path id="<?php echo esc_attr( $orbit_path_id ); ?>" d="<?php echo esc_attr( $orbit_path_d ); ?>"/>
								</defs>
								<text class="eap-advanced-button__orbit-text">
									<textPath href="#<?php echo esc_attr( $orbit_path_id ); ?>" xlink:href="#<?php echo esc_attr( $orbit_path_id ); ?>" startOffset="0"><?php echo esc_html( $orbit_text_full ); ?></textPath>
								</text>
							</svg>
						</span>
					<?php endif; ?>
					<span class="eap-advanced-button__orbit-inner" aria-hidden="true">
						<?php if ( $icon_html ) : ?>
							<span class="eap-advanced-button__orbit-icon"><?php echo wp_kses( $icon_html, $this->get_button_icon_allowed_html() ); ?></span>
							<span class="eap-advanced-button__orbit-icon eap-advanced-button__orbit-icon--ghost" aria-hidden="true"><?php echo wp_kses( $icon_html, $this->get_button_icon_allowed_html() ); ?></span>
						<?php endif; ?>
					</span>
					<span class="screen-reader-text"><?php echo esc_html( $settings['text'] ); ?></span>
				<?php elseif ( 'icon-expand' === $layout ) : ?>
					<span class="eap-advanced-button__icon-pill" aria-hidden="true">
						<?php if ( $icon_html ) : ?>
							<span class="eap-advanced-button__icon"><?php echo wp_kses( $icon_html, $this->get_button_icon_allowed_html() ); ?></span>
						<?php endif; ?>
					</span>
					<span class="eap-advanced-button__text"><?php echo esc_html( $settings['text'] ); ?></span>
				<?php elseif ( 'icon-motion-fill' === $layout ) : ?>
					<span class="eap-advanced-button__cover" aria-hidden="true"></span>
				<?php endif; ?>

				<?php if ( ! in_array( $layout, array( 'circle', 'icon-expand' ), true ) ) : ?>
				<span class="eap-advanced-button__content">
					<?php if ( in_array( $layout, array( 'icon-motion', 'icon-motion-fill' ), true ) && 'before' === $settings['icon_position'] && $icon_html ) : ?>
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

					<?php if ( in_array( $layout, array( 'icon-motion', 'icon-motion-fill' ), true ) && 'after' === $settings['icon_position'] && $icon_html ) : ?>
						<span class="eap-advanced-button__icon-shell" aria-hidden="true">
							<span class="eap-advanced-button__icon"><?php echo wp_kses( $icon_html, $this->get_button_icon_allowed_html() ); ?></span>
						</span>
					<?php endif; ?>
				</span>
				<?php endif; /* End non-circle content block */ ?>
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
