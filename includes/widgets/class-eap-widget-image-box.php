<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Utils;

class EAP_Widget_Image_Box extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-image-box';
	}

	public function get_title() {
		return __( 'Image Box', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'image-box' );
	}

	public function get_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-visibility-script',
			'eap-image-box-script',
		);
	}

	protected function register_controls() {
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
				'default' => 'standard',
				'options' => array(
					'standard'    => __( 'Standard Image Box', 'elementor-animatepro' ),
					'vertical'    => __( 'Vertical Image Box', 'elementor-animatepro' ),
					'interactive' => __( 'Interactive Image Box', 'elementor-animatepro' ),
					'classic'     => __( 'Classic Image Box', 'elementor-animatepro' ),
					'pointer'     => __( 'Pointer Image Box', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'vertical_media_position',
			array(
				'label'     => __( 'Image Position', 'elementor-animatepro' ),
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
				'condition' => array(
					'layout' => 'vertical',
				),
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'elementor-animatepro' ),
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
				'default' => 'large',
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label'       => __( 'Subtitle', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Style Prefix', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array(
					'layout' => array( 'vertical', 'classic' ),
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Prefix Control', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'description',
			array(
				'label'       => __( 'Description', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Add stylish image storytelling with flexible hover transitions, layout switches, and modern content animation.', 'elementor-animatepro' ),
				'rows'        => 5,
				'placeholder' => __( 'Enter description', 'elementor-animatepro' ),
				'condition'   => array(
					'layout!' => 'pointer',
				),
			)
		);

		$this->add_control(
			'tooltip_heading',
			array(
				'label'       => __( 'Pointer Tooltip Heading', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'View Project', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array(
					'layout' => 'pointer',
				),
			)
		);

		$this->add_control(
			'tooltip_description',
			array(
				'label'       => __( 'Pointer Tooltip Description', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Follow the cursor inside the image box with a modern floating tooltip.', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array(
					'layout' => 'pointer',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_motion',
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

		$this->add_control(
			'show_hover_icon',
			array(
				'label'        => __( 'Show Hover Icon', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'layout' => 'vertical',
				),
			)
		);

		$this->add_control(
			'hover_icon',
			array(
				'label'     => __( 'Hover Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'layout'          => 'vertical',
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->end_controls_section();

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
				'selector' => '{{WRAPPER}} .eap-image-box-card',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .eap-image-box-card',
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card, {{WRAPPER}} .eap-image-box-media, {{WRAPPER}} .eap-image-box-media img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => __( 'Content Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-copy' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'media_padding',
			array(
				'label'      => __( 'Image Outer Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card' => '--eap-image-box-media-space-top: {{TOP}}{{UNIT}}; --eap-image-box-media-space-right: {{RIGHT}}{{UNIT}}; --eap-image-box-media-space-bottom: {{BOTTOM}}{{UNIT}}; --eap-image-box-media-space-left: {{LEFT}}{{UNIT}};',
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

		$this->add_control(
			'image_height_mode',
			array(
				'label'   => __( 'Image Height Mode', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => array(
					'auto'   => __( 'Auto', 'elementor-animatepro' ),
					'custom' => __( 'Custom', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => __( 'Image Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 120,
						'max' => 900,
					),
					'vh' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-media' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'image_height_mode' => 'custom',
				),
			)
		);

		$this->add_control(
			'image_object_fit',
			array(
				'label'   => __( 'Object Fit', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => array(
					'cover'   => __( 'Cover', 'elementor-animatepro' ),
					'contain' => __( 'Contain', 'elementor-animatepro' ),
					'fill'    => __( 'Fill', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-media img' => 'object-fit: {{VALUE}};',
				),
				'condition' => array(
					'image_height_mode' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'image_radius',
			array(
				'label'      => __( 'Image Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-media, {{WRAPPER}} .eap-image-box-media img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_overlay_style',
			array(
				'label' => __( 'Overlay', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'interactive_overlay_color',
			array(
				'label'     => __( 'Interactive Overlay Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(7, 12, 20, 0.82)',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box--interactive .eap-image-box-copy::before' => 'background: linear-gradient(180deg, rgba(7, 12, 20, 0.08), {{VALUE}});',
				),
				'condition' => array(
					'layout' => 'interactive',
				),
			)
		);

		$this->add_control(
			'classic_overlay_color',
			array(
				'label'     => __( 'Classic Overlay Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(7, 12, 20, 0.82)',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box--classic .eap-image-box-copy::before' => 'background: linear-gradient(180deg, rgba(7, 12, 20, 0.08), {{VALUE}});',
				),
				'condition' => array(
					'layout' => 'classic',
				),
			)
		);

		$this->end_controls_section();

		$this->register_text_style_controls( 'subtitle', __( 'Subtitle', 'elementor-animatepro' ), '.eap-image-box-subtitle' );
		$this->register_text_style_controls( 'title', __( 'Title', 'elementor-animatepro' ), '.eap-image-box-title' );
		$this->register_text_style_controls( 'description', __( 'Description', 'elementor-animatepro' ), '.eap-image-box-description' );

		$this->start_controls_section(
			'section_tooltip_style',
			array(
				'label'     => __( 'Pointer Tooltip', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => 'pointer',
				),
			)
		);

		$this->add_control(
			'tooltip_bg',
			array(
				'label'     => __( 'Background Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#09111f',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-tooltip' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tooltip_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-tooltip' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tooltip_typography',
				'selector' => '{{WRAPPER}} .eap-image-box-tooltip',
			)
		);

		$this->add_responsive_control(
			'tooltip_width',
			array(
				'label'      => __( 'Tooltip Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 80,
						'max' => 360,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-tooltip' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tooltip_padding',
			array(
				'label'      => __( 'Tooltip Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tooltip_radius',
			array(
				'label'      => __( 'Tooltip Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->register_text_style_controls( 'tooltip_heading', __( 'Tooltip Heading', 'elementor-animatepro' ), '.eap-image-box-tooltip-heading' );
		$this->register_text_style_controls( 'tooltip_description', __( 'Tooltip Description', 'elementor-animatepro' ), '.eap-image-box-tooltip-description' );

		$this->start_controls_section(
			'section_icon_style',
			array(
				'label'     => __( 'Hover Icon', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout'          => 'vertical',
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-hover-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hover_icon_glyph_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 72,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-hover-icon-inner' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .eap-image-box-hover-icon-inner svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_background',
			array(
				'label'     => __( 'Background Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff6b2c',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-hover-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'hover_icon_size',
			array(
				'label'      => __( 'Icon Box Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 34,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-hover-icon' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'hover_icon_rotate',
			array(
				'label'      => __( 'Hover Rotate', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default'    => array(
					'size' => 0,
					'unit' => 'deg',
				),
				'range'      => array(
					'deg' => array(
						'min' => -360,
						'max' => 360,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card:hover .eap-image-box-hover-icon-inner' => '--eap-hover-icon-rotate: {{SIZE}}deg;',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_scale',
			array(
				'label'      => __( 'Hover Scale', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'custom' ),
				'range'      => array(
					'custom' => array(
						'min'  => 0.2,
						'max'  => 3,
						'step' => 0.05,
					),
				),
				'default'    => array(
					'size' => 1,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card:hover .eap-image-box-hover-icon-inner' => '--eap-hover-icon-scale: {{SIZE}};',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_translate_x',
			array(
				'label'      => __( 'Hover Offset X', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => -120,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card:hover .eap-image-box-hover-icon-inner' => '--eap-hover-icon-translate-x: {{SIZE}}px;',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_translate_y',
			array(
				'label'      => __( 'Hover Offset Y', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => -120,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card:hover .eap-image-box-hover-icon-inner' => '--eap-hover-icon-translate-y: {{SIZE}}px;',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_skew_x',
			array(
				'label'      => __( 'Hover Skew X', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array(
						'min' => -60,
						'max' => 60,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card:hover .eap-image-box-hover-icon-inner' => '--eap-hover-icon-skew-x: {{SIZE}}deg;',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_skew_y',
			array(
				'label'      => __( 'Hover Skew Y', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array(
						'min' => -60,
						'max' => 60,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card:hover .eap-image-box-hover-icon-inner' => '--eap-hover-icon-skew-y: {{SIZE}}deg;',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'hover_icon_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-hover-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hover_icon_position',
			array(
				'label'   => __( 'Position', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top-right',
				'options' => array(
					'top-left'      => __( 'Top Left', 'elementor-animatepro' ),
					'top-center'    => __( 'Top Center', 'elementor-animatepro' ),
					'top-right'     => __( 'Top Right', 'elementor-animatepro' ),
					'center-left'   => __( 'Center Left', 'elementor-animatepro' ),
					'center'        => __( 'Center', 'elementor-animatepro' ),
					'center-right'  => __( 'Center Right', 'elementor-animatepro' ),
					'bottom-left'   => __( 'Bottom Left', 'elementor-animatepro' ),
					'bottom-center' => __( 'Bottom Center', 'elementor-animatepro' ),
					'bottom-right'  => __( 'Bottom Right', 'elementor-animatepro' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings     = $this->get_settings_for_display();
		$layout       = $settings['layout'];
		$link_open    = '';
		$link_close   = '';
		$media_markup = Group_Control_Image_Size::get_attachment_image_html( $settings, 'image_size', 'image' );

		if ( empty( $media_markup ) && ! empty( $settings['image']['url'] ) ) {
			$media_markup = sprintf( '<img src="%1$s" alt="%2$s" />', esc_url( $settings['image']['url'] ), esc_attr( $settings['title'] ) );
		}

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'image_box_link', $settings['link'] );
			$link_open  = '<a class="eap-image-box-card" ' . $this->get_render_attribute_string( 'image_box_link' ) . '>';
			$link_close = '</a>';
		} else {
			$link_open  = '<div class="eap-image-box-card">';
			$link_close = '</div>';
		}

		$wrapper_classes = array(
			'eap-widget',
			'eap-image-box',
			'eap-image-box--' . $layout,
			'eap-image-box--hover-' . $settings['hover_effect'],
			'eap-image-box--unfold-' . $settings['unfold_direction'],
			'eap-image-box--height-' . $settings['image_height_mode'],
		);

		if ( 'vertical' === $layout ) {
			$wrapper_classes[] = 'eap-image-box--media-' . $settings['vertical_media_position'];
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-eap-image-box>
			<?php echo $link_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div class="eap-image-box-media-wrap">
					<div class="eap-image-box-media">
						<?php echo wp_kses_post( $media_markup ); ?>
					</div>
					<?php if ( 'vertical' === $layout && 'yes' === $settings['show_hover_icon'] ) : ?>
						<span class="eap-image-box-hover-icon eap-image-box-hover-icon--<?php echo esc_attr( $settings['hover_icon_position'] ); ?>">
							<span class="eap-image-box-hover-icon-inner">
								<?php Icons_Manager::render_icon( $settings['hover_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							</span>
						</span>
					<?php endif; ?>
					<?php if ( 'pointer' === $layout ) : ?>
						<span class="eap-image-box-tooltip">
							<?php if ( ! empty( $settings['tooltip_heading'] ) ) : ?>
								<span class="eap-image-box-tooltip-heading"><?php echo esc_html( $settings['tooltip_heading'] ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $settings['tooltip_description'] ) ) : ?>
								<span class="eap-image-box-tooltip-description"><?php echo esc_html( $settings['tooltip_description'] ); ?></span>
							<?php endif; ?>
						</span>
					<?php endif; ?>
				</div>
				<div class="eap-image-box-copy">
					<?php if ( ! empty( $settings['subtitle'] ) && in_array( $layout, array( 'vertical', 'classic' ), true ) ) : ?>
						<div class="eap-image-box-subtitle"><?php echo esc_html( $settings['subtitle'] ); ?></div>
					<?php endif; ?>

					<?php if ( ! empty( $settings['title'] ) ) : ?>
						<h3 class="eap-image-box-title"><?php echo esc_html( $settings['title'] ); ?></h3>
					<?php endif; ?>

					<?php if ( ! empty( $settings['description'] ) && 'pointer' !== $layout ) : ?>
						<div class="eap-image-box-description"><?php echo esc_html( $settings['description'] ); ?></div>
					<?php endif; ?>
				</div>
			<?php echo $link_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php
	}

	/**
	 * Register shared text style controls.
	 *
	 * @param string $name Section key.
	 * @param string $label Section label.
	 * @param string $selector CSS selector.
	 * @return void
	 */
	private function register_text_style_controls( $name, $label, $selector ) {
		$this->start_controls_section(
			'section_style_' . $name,
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			$name . '_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $selector => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $name . '_typography',
				'selector' => '{{WRAPPER}} ' . $selector,
			)
		);

		$this->end_controls_section();
	}
}
