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
use Elementor\Utils;

class EAP_Widget_Testimonial extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-testimonial';
	}

	public function get_title() {
		return __( 'Testimonial Box', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-testimonial';
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'testimonial' );
	}

	public function get_script_depends() {
		return $this->get_visibility_script_depends();
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_animation_controls();
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
				'default' => 'background-overlay',
				'options' => array(
					'background-overlay' => __( 'Background Overlay', 'elementor-animatepro' ),
					'breakout-avatar'    => __( 'Floating Avatar', 'elementor-animatepro' ),
					'separator-card'     => __( 'Stars + Separator', 'elementor-animatepro' ),
					'quote-focus'        => __( 'Quote Focus', 'elementor-animatepro' ),
					'user-first'         => __( 'User First', 'elementor-animatepro' ),
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
			'testimonial_content',
			array(
				'label'       => __( 'Testimonial', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'ContentAI has revolutionized our content workflow. The quality of the articles is outstanding, and it saves us hours of work every week.', 'elementor-animatepro' ),
				'rows'        => 6,
				'label_block' => true,
			)
		);

		$this->add_control(
			'name',
			array(
				'label'       => __( 'Name', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'John Doe', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'designation',
			array(
				'label'       => __( 'Designation', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Marketing Director, TechCorp', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'avatar',
			array(
				'label'   => __( 'User Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'avatar_size',
				'default' => 'thumbnail',
			)
		);

		$this->add_control(
			'background_image',
			array(
				'label'     => __( 'Background Image', 'elementor-animatepro' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'layout' => 'background-overlay',
				),
			)
		);

		$this->add_control(
			'quote_icon',
			array(
				'label'     => __( 'Quote Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-quote-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'layout' => 'quote-focus',
				),
			)
		);

		$this->add_control(
			'quote_icon_align',
			array(
				'label'     => __( 'Quote Alignment', 'elementor-animatepro' ),
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
				'default'   => 'center',
				'condition' => array(
					'layout' => 'quote-focus',
				),
			)
		);

		$this->add_control(
			'rating',
			array(
				'label'      => __( 'Star Rating', 'elementor-animatepro' ),
				'type'       => Controls_Manager::NUMBER,
				'default'    => 4.5,
				'min'        => 0,
				'max'        => 5,
				'step'       => 0.5,
				'condition'  => array(
					'layout' => array( 'breakout-avatar', 'separator-card', 'user-first' ),
				),
			)
		);

		$this->add_control(
			'separator_style',
			array(
				'label'     => __( 'Separator Style', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'line',
				'options'   => array(
					'line'   => __( 'Line', 'elementor-animatepro' ),
					'dashed' => __( 'Dashed', 'elementor-animatepro' ),
					'dotted' => __( 'Dotted', 'elementor-animatepro' ),
				),
				'condition' => array(
					'layout' => 'separator-card',
				),
			)
		);

		$this->add_responsive_control(
			'breakout_overlap',
			array(
				'label'      => __( 'Avatar Outside Amount', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 44,
					'unit' => 'px',
				),
				'condition'  => array(
					'layout' => 'breakout-avatar',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-box' => '--eap-testimonial-breakout-offset: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'overlay_start',
			array(
				'label'      => __( 'Overlay Start', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array(
					'%' => array(
						'min' => 20,
						'max' => 95,
					),
				),
				'default'    => array(
					'size' => 58,
					'unit' => '%',
				),
				'condition'  => array(
					'layout' => 'background-overlay',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-box' => '--eap-testimonial-overlay-start: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'user_info_align',
			array(
				'label'     => __( 'User Info Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
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
				'default'   => 'left',
			)
		);

		$this->end_controls_section();
	}

	protected function register_animation_controls() {
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
				'default' => 'fade-up',
				'options' => array(
					'none'        => __( 'None', 'elementor-animatepro' ),
					'fade-up'     => __( 'Fade Up', 'elementor-animatepro' ),
					'fade-left'   => __( 'Fade Left', 'elementor-animatepro' ),
					'fade-right'  => __( 'Fade Right', 'elementor-animatepro' ),
					'slide-up'    => __( 'Slide Up', 'elementor-animatepro' ),
					'slide-down'  => __( 'Slide Down', 'elementor-animatepro' ),
					'slide-left'  => __( 'Slide Left', 'elementor-animatepro' ),
					'slide-right' => __( 'Slide Right', 'elementor-animatepro' ),
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
				'selector' => '{{WRAPPER}} .eap-testimonial-box-card',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .eap-testimonial-box-card',
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-box-card, {{WRAPPER}} .eap-testimonial-box__bg-image, {{WRAPPER}} .eap-testimonial-box__overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .eap-testimonial-box-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; --eap-testimonial-padding-top: {{TOP}}{{UNIT}}; --eap-testimonial-padding-right: {{RIGHT}}{{UNIT}}; --eap-testimonial-padding-bottom: {{BOTTOM}}{{UNIT}}; --eap-testimonial-padding-left: {{LEFT}}{{UNIT}};',
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
						'min' => 180,
						'max' => 900,
					),
					'vh' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-box-card' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .eap-testimonial-box-card',
			)
		);

		$this->add_control(
			'hover_background',
			array(
				'label'     => __( 'Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-box' => '--eap-testimonial-hover-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hover_translate_y',
			array(
				'label'   => __( 'Hover Offset Y', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => -8,
				'step'    => 1,
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-box' => '--eap-testimonial-hover-translate-y: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'hover_scale',
			array(
				'label'   => __( 'Hover Scale', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
				'step'    => 0.01,
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-box' => '--eap-testimonial-hover-scale: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hover_rotate',
			array(
				'label'   => __( 'Hover Rotate', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'step'    => 0.1,
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-box' => '--eap-testimonial-hover-rotate: {{VALUE}}deg;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_overlay_style',
			array(
				'label'     => __( 'Overlay', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => 'background-overlay',
				),
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.94)',
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-box' => '--eap-testimonial-overlay-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => __( 'Image', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'avatar_width',
			array(
				'label'      => __( 'Image Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 40,
						'max' => 320,
					),
					'%' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 72,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-box' => '--eap-testimonial-avatar-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_height',
			array(
				'label'      => __( 'Image Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 40,
						'max' => 320,
					),
					'%' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 72,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-box' => '--eap-testimonial-avatar-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_radius',
			array(
				'label'      => __( 'Image Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-box__avatar, {{WRAPPER}} .eap-testimonial-box__avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'avatar_border',
				'selector' => '{{WRAPPER}} .eap-testimonial-box__avatar',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'avatar_shadow',
				'selector' => '{{WRAPPER}} .eap-testimonial-box__avatar',
			)
		);

		$this->add_control(
			'image_hover_effect',
			array(
				'label'   => __( 'Image Hover Effect', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'zoom-in',
				'options' => array(
					'none'              => __( 'None', 'elementor-animatepro' ),
					'zoom-in'           => __( 'Zoom In', 'elementor-animatepro' ),
					'zoom-out'          => __( 'Zoom Out', 'elementor-animatepro' ),
					'fade'              => __( 'Fade', 'elementor-animatepro' ),
					'grayscale-to-color' => __( 'B/W to Color', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'image_hover_scale',
			array(
				'label'   => __( 'Image Hover Scale', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1.08,
				'step'    => 0.01,
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-box' => '--eap-testimonial-image-hover-scale: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_quote_style',
			array(
				'label'     => __( 'Quote Icon', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => 'quote-focus',
				),
			)
		);

		$this->add_responsive_control(
			'quote_icon_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 180,
					),
				),
				'default'    => array(
					'size' => 36,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-box__quote-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-testimonial-box__quote-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'quote_icon_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-box__quote-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-testimonial-box__quote-icon svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'quote_icon_space',
			array(
				'label'      => __( 'Bottom Space', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-box__quote-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'quote_content_width',
			array(
				'label'      => __( 'Content Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array(
						'min' => 40,
						'max' => 100,
					),
					'px' => array(
						'min' => 240,
						'max' => 1400,
					),
				),
				'default'    => array(
					'size' => 100,
					'unit' => '%',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-box' => '--eap-testimonial-quote-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_rating_style',
			array(
				'label'     => __( 'Stars', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => array( 'breakout-avatar', 'separator-card', 'user-first' ),
				),
			)
		);

		$this->add_responsive_control(
			'rating_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 60,
					),
				),
				'default'    => array(
					'size' => 22,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-box__rating' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'rating_color',
			array(
				'label'     => __( 'Active Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff5b2e',
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-box' => '--eap-testimonial-rating-active: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'rating_inactive_color',
			array(
				'label'     => __( 'Inactive Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d2d8e3',
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-box' => '--eap-testimonial-rating-inactive: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'rating_gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
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
					'{{WRAPPER}} .eap-testimonial-box' => '--eap-testimonial-rating-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_separator_style',
			array(
				'label'     => __( 'Separator', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => 'separator-card',
				),
			)
		);

		$this->add_control(
			'separator_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15, 23, 42, 0.12)',
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-box' => '--eap-testimonial-separator-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'separator_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'size' => 28,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-box__separator' => 'margin: {{SIZE}}{{UNIT}} 0;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			array(
				'label' => __( 'Testimonial Text', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'content_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-box__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .eap-testimonial-box__text',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_name_style',
			array(
				'label' => __( 'Name', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-box__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .eap-testimonial-box__name',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_designation_style',
			array(
				'label' => __( 'Designation', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'designation_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-box__designation' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'designation_typography',
				'selector' => '{{WRAPPER}} .eap-testimonial-box__designation',
			)
		);

		$this->end_controls_section();
	}

	protected function get_avatar_html( $settings ) {
		$avatar = Group_Control_Image_Size::get_attachment_image_html( $settings, 'avatar_size', 'avatar' );

		if ( empty( $avatar ) && ! empty( $settings['avatar']['url'] ) ) {
			$avatar = sprintf( '<img src="%1$s" alt="%2$s" />', esc_url( $settings['avatar']['url'] ), esc_attr( $settings['name'] ) );
		}

		return $avatar;
	}

	protected function render_user_info( $settings, $show_avatar = true ) {
		$avatar = $this->get_avatar_html( $settings );
		?>
		<div class="eap-testimonial-box__user">
			<?php if ( $show_avatar && ! empty( $avatar ) ) : ?>
				<div class="eap-testimonial-box__avatar">
					<?php echo wp_kses_post( $avatar ); ?>
				</div>
			<?php endif; ?>
			<div class="eap-testimonial-box__meta">
				<?php if ( ! empty( $settings['name'] ) ) : ?>
					<div class="eap-testimonial-box__name"><?php echo esc_html( $settings['name'] ); ?></div>
				<?php endif; ?>
				<?php if ( ! empty( $settings['designation'] ) ) : ?>
					<div class="eap-testimonial-box__designation"><?php echo esc_html( $settings['designation'] ); ?></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	protected function render_rating( $rating ) {
		$rating = max( 0, min( 5, (float) $rating ) );

		if ( $rating <= 0 ) {
			return;
		}
		?>
		<div class="eap-testimonial-box__rating" aria-label="<?php echo esc_attr( sprintf( __( '%s out of 5 stars', 'elementor-animatepro' ), $rating ) ); ?>">
			<?php for ( $index = 1; $index <= 5; $index++ ) : ?>
				<?php
				$state = 'empty';
				if ( $rating >= $index ) {
					$state = 'full';
				} elseif ( $rating >= ( $index - 0.5 ) ) {
					$state = 'half';
				}
				?>
				<span class="eap-testimonial-box__star is-<?php echo esc_attr( $state ); ?>" aria-hidden="true">
					<span class="eap-testimonial-box__star-base"><?php $this->render_star_icon(); ?></span>
					<span class="eap-testimonial-box__star-fill"><?php $this->render_star_icon(); ?></span>
				</span>
			<?php endfor; ?>
		</div>
		<?php
	}

	protected function render_star_icon() {
		?>
		<svg viewBox="0 0 24 24" role="presentation" focusable="false" aria-hidden="true">
			<path d="M12 1.75l3.18 6.44 7.1 1.03-5.14 5 1.21 7.06L12 18.03 5.65 21.28l1.21-7.06-5.14-5 7.1-1.03L12 1.75z"></path>
		</svg>
		<?php
	}

	protected function get_testimonial_wrapper_classes( $settings ) {
		$layout    = ! empty( $settings['layout'] ) ? $settings['layout'] : 'background-overlay';
		$animation = ! empty( $settings['starter_animation'] ) ? $settings['starter_animation'] : 'none';
		$classes   = array(
			'eap-widget',
			'eap-testimonial-box',
			'eap-testimonial-box--' . $layout,
			'eap-testimonial-box--info-align-' . ( ! empty( $settings['user_info_align'] ) ? $settings['user_info_align'] : 'left' ),
		);

		if ( 'none' !== $animation ) {
			$classes[] = 'eap-testimonial-box--animate-' . $animation;
		}

		return $classes;
	}

	protected function get_testimonial_card_style( $settings ) {
		$card_style = '--eap-testimonial-duration: ' . absint( ! empty( $settings['animation_duration'] ) ? $settings['animation_duration'] : 850 ) . 'ms; --eap-testimonial-delay: ' . absint( ! empty( $settings['animation_delay'] ) ? $settings['animation_delay'] : 0 ) . 'ms;';

		if ( ! empty( $settings['image_hover_effect'] ) ) {
			$card_style .= ' --eap-testimonial-image-effect: ' . esc_attr( $settings['image_hover_effect'] ) . ';';
		}

		return $card_style;
	}

	protected function render_testimonial_card_markup( $settings ) {
		$layout = ! empty( $settings['layout'] ) ? $settings['layout'] : 'background-overlay';
		$avatar = $this->get_avatar_html( $settings );
		?>
		<div class="eap-testimonial-card eap-testimonial-box-card" style="<?php echo esc_attr( $this->get_testimonial_card_style( $settings ) ); ?>">
			<?php if ( 'background-overlay' === $layout ) : ?>
				<?php if ( ! empty( $settings['background_image']['url'] ) ) : ?>
					<div class="eap-testimonial-box__bg-image">
						<img src="<?php echo esc_url( $settings['background_image']['url'] ); ?>" alt="<?php echo esc_attr( $settings['name'] ); ?>" />
					</div>
				<?php endif; ?>
				<div class="eap-testimonial-box__overlay" aria-hidden="true"></div>
				<div class="eap-testimonial-box__body">
					<div class="eap-testimonial-box__text"><?php echo esc_html( $settings['testimonial_content'] ); ?></div>
					<div class="eap-testimonial-box__separator"></div>
					<?php $this->render_user_info( $settings, false ); ?>
				</div>
			<?php elseif ( 'breakout-avatar' === $layout ) : ?>
				<?php if ( ! empty( $avatar ) ) : ?>
					<div class="eap-testimonial-box__breakout-avatar">
						<div class="eap-testimonial-box__avatar">
							<?php echo wp_kses_post( $avatar ); ?>
						</div>
					</div>
				<?php endif; ?>
				<div class="eap-testimonial-box__body">
					<div class="eap-testimonial-box__meta eap-testimonial-box__meta--stacked">
						<div class="eap-testimonial-box__name"><?php echo esc_html( $settings['name'] ); ?></div>
						<div class="eap-testimonial-box__designation"><?php echo esc_html( $settings['designation'] ); ?></div>
					</div>
					<div class="eap-testimonial-box__text"><?php echo esc_html( $settings['testimonial_content'] ); ?></div>
					<?php $this->render_rating( $settings['rating'] ); ?>
				</div>
			<?php elseif ( 'separator-card' === $layout ) : ?>
				<div class="eap-testimonial-box__body">
					<?php $this->render_rating( $settings['rating'] ); ?>
					<div class="eap-testimonial-box__text"><?php echo esc_html( $settings['testimonial_content'] ); ?></div>
					<div class="eap-testimonial-box__separator eap-testimonial-box__separator--<?php echo esc_attr( $settings['separator_style'] ); ?>"></div>
					<?php $this->render_user_info( $settings, true ); ?>
				</div>
			<?php elseif ( 'quote-focus' === $layout ) : ?>
				<div class="eap-testimonial-box__body">
					<?php if ( ! empty( $settings['quote_icon']['value'] ) ) : ?>
						<div class="eap-testimonial-box__quote-icon" style="justify-content: <?php echo esc_attr( $settings['quote_icon_align'] ); ?>;">
							<?php Icons_Manager::render_icon( $settings['quote_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</div>
					<?php endif; ?>
					<div class="eap-testimonial-box__text"><?php echo esc_html( $settings['testimonial_content'] ); ?></div>
					<?php $this->render_user_info( $settings, true ); ?>
				</div>
			<?php else : ?>
				<div class="eap-testimonial-box__body">
					<?php $this->render_user_info( $settings, true ); ?>
					<?php $this->render_rating( $settings['rating'] ); ?>
					<div class="eap-testimonial-box__text"><?php echo esc_html( $settings['testimonial_content'] ); ?></div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function render_testimonial_box_markup( $settings, $extra_classes = array(), $extra_attributes = array(), $attribute_key = 'wrapper' ) {
		$classes = array_merge( $this->get_testimonial_wrapper_classes( $settings ), $extra_classes );
		$attributes = array_merge(
			array(
				'class'                 => $classes,
				'data-eap-image-hover'  => ! empty( $settings['image_hover_effect'] ) ? $settings['image_hover_effect'] : 'zoom-in',
			),
			$extra_attributes
		);

		$this->add_render_attribute( $attribute_key, $attributes );
		?>
		<div <?php echo $this->get_render_attribute_string( $attribute_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php $this->render_testimonial_card_markup( $settings ); ?>
		</div>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$this->render_testimonial_box_markup( $settings );
	}
}
