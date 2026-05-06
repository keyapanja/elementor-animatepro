<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class EAP_Widget_Image_Gallery extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-image-gallery';
	}

	public function get_title() {
		return __( 'Image Gallery', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_style_depends() {
		return array(
			'eap-core',
			'eap-image-gallery',
			'eap-image',
		);
	}

	public function get_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-visibility-script',
			'eap-image-gallery-script',
		);
	}

	protected function register_controls() {
		$this->register_content_controls();
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
			'gallery_style',
			array(
				'label'   => __( 'Style', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'basic',
				'options' => array(
					'basic'   => __( 'Basic', 'elementor-animatepro' ),
					'masonry' => __( 'Masonry', 'elementor-animatepro' ),
					'zigzag'  => __( 'Zigzag', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'zigzag_offset',
			array(
				'label'      => __( 'Zigzag Offset', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'vw' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-gallery' => '--eap-gallery-zigzag-offset: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'gallery_style' => 'zigzag',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gallery',
			array(
				'label' => __( 'Image Gallery', 'elementor-animatepro' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_image',
			array(
				'label'   => __( 'Choose Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'item_link',
			array(
				'label'         => __( 'Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'placeholder'   => 'https://your-link.com',
			)
		);

		$this->add_control(
			'gallery_items',
			array(
				'label'       => __( 'Image Gallery', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ item_image.url ? "Item" : "Image" }}}',
				'default'     => array(
					array(),
					array(),
					array(),
					array(),
					array(),
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
			'open_lightbox',
			array(
				'label'        => __( 'Lightbox', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_icon',
			array(
				'label'        => __( 'Show Icon', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'hover_icon',
			array(
				'label'     => __( 'Hover Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array(
					'show_icon' => 'yes',
				),
				'default'   => array(
					'value'   => 'fab fa-instagram',
					'library' => 'fa-brands',
				),
			)
		);

		$this->add_control(
			'scroll_smooth',
			array(
				'label'        => __( 'Scroll Smooth', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'data_speed',
			array(
				'label'     => __( 'Data Speed', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0.9,
				'min'       => 0,
				'max'       => 3,
				'step'      => 0.1,
				'condition' => array(
					'scroll_smooth' => 'yes',
				),
			)
		);

		$this->add_control(
			'data_lag',
			array(
				'label'     => __( 'Data Lag', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0.5,
				'min'       => 0,
				'max'       => 3,
				'step'      => 0.1,
				'condition' => array(
					'scroll_smooth' => 'yes',
				),
			)
		);

		$this->end_controls_section();

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
				'default' => 'reveal',
				'options' => array(
					'none'        => __( 'None', 'elementor-animatepro' ),
					'reveal'      => __( 'Reveal', 'elementor-animatepro' ),
					'scale'       => __( 'Scale', 'elementor-animatepro' ),
					'slide'       => __( 'Slide', 'elementor-animatepro' ),
					'skew-reveal' => __( 'Skew Reveal', 'elementor-animatepro' ),
					'flip'        => __( 'Flip', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'animation_duration',
			array(
				'label'     => __( 'Duration (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1000,
				'min'       => 0,
				'step'      => 10,
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
				'min'       => 0,
				'step'      => 10,
				'condition' => array(
					'starter_animation!' => 'none',
				),
			)
		);

		$this->add_control(
			'animation_easing',
			array(
				'label'     => __( 'Easing', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'ease',
				'options'   => array(
					'ease'         => __( 'Ease (Default)', 'elementor-animatepro' ),
					'linear'       => __( 'Linear', 'elementor-animatepro' ),
					'ease-in'      => __( 'Ease In', 'elementor-animatepro' ),
					'ease-out'     => __( 'Ease Out', 'elementor-animatepro' ),
					'ease-in-out'  => __( 'Ease In Out', 'elementor-animatepro' ),
					'cubic-bezier(0.22, 1, 0.36, 1)' => __( 'Smooth Cubic', 'elementor-animatepro' ),
					'cubic-bezier(0.175, 0.885, 0.32, 1.275)' => __( 'Elastic Feel', 'elementor-animatepro' ),
				),
				'condition' => array(
					'starter_animation!' => 'none',
				),
			)
		);

		$this->add_control(
			'reveal_direction',
			array(
				'label'     => __( 'Direction', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom',
				'options'   => array(
					'bottom' => __( 'Bottom -> Top', 'elementor-animatepro' ),
					'top'    => __( 'Top -> Bottom', 'elementor-animatepro' ),
					'left'   => __( 'Left -> Right', 'elementor-animatepro' ),
					'right'  => __( 'Right -> Left', 'elementor-animatepro' ),
					'center' => __( 'Center Expand', 'elementor-animatepro' ),
				),
				'condition' => array(
					'starter_animation' => 'reveal',
				),
			)
		);

		$this->add_control(
			'enable_fade',
			array(
				'label'        => __( 'Enable Fade', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'starter_animation' => 'reveal',
				),
			)
		);

		$this->add_control(
			'scale_start',
			array(
				'label'     => __( 'Start Scale', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0.6,
				'min'       => 0,
				'max'       => 5,
				'step'      => 0.01,
				'condition' => array(
					'starter_animation' => 'scale',
				),
			)
		);

		$this->add_control(
			'scale_end',
			array(
				'label'     => __( 'End Scale', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 0,
				'max'       => 5,
				'step'      => 0.01,
				'condition' => array(
					'starter_animation' => 'scale',
				),
			)
		);

		$this->add_control(
			'scale_from',
			array(
				'label'     => __( 'Scale From', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => array(
					'center' => __( 'Center', 'elementor-animatepro' ),
					'top'    => __( 'Top', 'elementor-animatepro' ),
					'bottom' => __( 'Bottom', 'elementor-animatepro' ),
					'left'   => __( 'Left', 'elementor-animatepro' ),
					'right'  => __( 'Right', 'elementor-animatepro' ),
				),
				'condition' => array(
					'starter_animation' => 'scale',
				),
			)
		);

		$this->add_control(
			'scale_opacity',
			array(
				'label'        => __( 'Animate Opacity', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'starter_animation' => 'scale',
				),
			)
		);

		$this->add_control(
			'slide_direction',
			array(
				'label'     => __( 'Direction', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom',
				'options'   => array(
					'bottom' => __( 'Bottom -> Top', 'elementor-animatepro' ),
					'top'    => __( 'Top -> Bottom', 'elementor-animatepro' ),
					'left'   => __( 'Left -> Right', 'elementor-animatepro' ),
					'right'  => __( 'Right -> Left', 'elementor-animatepro' ),
				),
				'condition' => array(
					'starter_animation' => 'slide',
				),
			)
		);

		$this->add_control(
			'slide_distance',
			array(
				'label'     => __( 'Distance (px)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 40,
				'min'       => 0,
				'step'      => 1,
				'condition' => array(
					'starter_animation' => 'slide',
				),
			)
		);

		$this->add_control(
			'skew_angle',
			array(
				'label'     => __( 'Skew Angle', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 18,
				'min'       => -180,
				'max'       => 180,
				'step'      => 1,
				'condition' => array(
					'starter_animation' => 'skew-reveal',
				),
			)
		);

		$this->add_control(
			'skew_translate_distance',
			array(
				'label'     => __( 'Translate Distance', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 40,
				'min'       => 0,
				'step'      => 1,
				'condition' => array(
					'starter_animation' => 'skew-reveal',
				),
			)
		);

		$this->add_control(
			'flip_direction',
			array(
				'label'     => __( 'Flip Direction', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'flip-x',
				'options'   => array(
					'flip-x' => __( 'Flip X', 'elementor-animatepro' ),
					'flip-y' => __( 'Flip Y', 'elementor-animatepro' ),
				),
				'condition' => array(
					'starter_animation' => 'flip',
				),
			)
		);

		$this->add_control(
			'flip_angle',
			array(
				'label'     => __( 'Flip Angle', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 88,
				'min'       => 0,
				'max'       => 180,
				'step'      => 1,
				'condition' => array(
					'starter_animation' => 'flip',
				),
			)
		);

		$this->add_control(
			'flip_perspective',
			array(
				'label'     => __( 'Perspective', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 800,
				'min'       => 100,
				'step'      => 10,
				'condition' => array(
					'starter_animation' => 'flip',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'section_style_gallery',
			array(
				'label' => __( 'Image Gallery', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'     => __( 'Columns', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '3',
				'options'   => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-image-gallery' => '--eap-gallery-columns: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'columns_gap',
			array(
				'label'      => __( 'Columns Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 30,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-gallery' => '--eap-gallery-column-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'rows_gap',
			array(
				'label'      => __( 'Rows Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 30,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-gallery' => '--eap-gallery-row-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'border_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-gallery__link, {{WRAPPER}} .eap-image-gallery__media, {{WRAPPER}} .eap-image-gallery__media img, {{WRAPPER}} .eap-image-gallery__overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'label'   => __( 'Hover Effect', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'zoom-in',
				'options' => array(
					'none'     => __( 'None', 'elementor-animatepro' ),
					'zoom-in'  => __( 'Zoom In', 'elementor-animatepro' ),
					'zoom-out' => __( 'Zoom Out', 'elementor-animatepro' ),
					'fade'     => __( 'Fade', 'elementor-animatepro' ),
					'bw-color' => __( 'B/W to Color', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} .eap-image-gallery__media img',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_overlay_icon',
			array(
				'label' => __( 'Overlay & Icon', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15, 23, 42, 0.42)',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-gallery__overlay' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_color_hover',
			array(
				'label'     => __( 'Overlay Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15, 23, 42, 0.58)',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-gallery__link:hover .eap-image-gallery__overlay' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'condition' => array(
					'show_icon' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-image-gallery__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-image-gallery__icon svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color_hover',
			array(
				'label'     => __( 'Icon Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'condition' => array(
					'show_icon' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-image-gallery__link:hover .eap-image-gallery__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-image-gallery__link:hover .eap-image-gallery__icon svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_background',
			array(
				'label'     => __( 'Icon Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.08)',
				'condition' => array(
					'show_icon' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-image-gallery__icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_background_hover',
			array(
				'label'     => __( 'Icon Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.16)',
				'condition' => array(
					'show_icon' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-image-gallery__link:hover .eap-image-gallery__icon' => 'background-color: {{VALUE}};',
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
						'min' => 8,
						'max' => 120,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'condition'  => array(
					'show_icon' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-gallery__icon' => 'font-size: {{SIZE}}{{UNIT}};',
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
						'min' => 16,
						'max' => 180,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 56,
				),
				'condition'  => array(
					'show_icon' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-gallery__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_border_radius',
			array(
				'label'      => __( 'Icon Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'condition'  => array(
					'show_icon' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-gallery__icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'icon_border',
				'selector'  => '{{WRAPPER}} .eap-image-gallery__icon',
				'condition' => array(
					'show_icon' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	private function get_image_animation_data( $settings ) {
		$animation = ! empty( $settings['starter_animation'] ) ? $settings['starter_animation'] : 'none';
		$direction = 'bottom';
		$easing    = ! empty( $settings['animation_easing'] ) ? $settings['animation_easing'] : 'ease';
		$duration  = ! empty( $settings['animation_duration'] ) ? (int) $settings['animation_duration'] : 1000;
		$delay     = ! empty( $settings['animation_delay'] ) ? (int) $settings['animation_delay'] : 0;
		$style     = '--eap-image-anim-duration: ' . esc_attr( $duration ) . 'ms; --eap-image-anim-delay: ' . esc_attr( $delay ) . 'ms; --eap-image-anim-easing: ' . esc_attr( $easing ) . ';';
		$classes   = array(
			'eap-image-widget',
			'eap-image-gallery__item',
		);

		if ( 'reveal' === $animation ) {
			$direction = ! empty( $settings['reveal_direction'] ) ? $settings['reveal_direction'] : 'bottom';
		} elseif ( 'scale' === $animation ) {
			$direction = ! empty( $settings['scale_from'] ) ? $settings['scale_from'] : 'center';
			$style    .= ' --eap-image-scale-start: ' . esc_attr( isset( $settings['scale_start'] ) ? $settings['scale_start'] : 0.6 ) . ';';
			$style    .= ' --eap-image-scale-end: ' . esc_attr( isset( $settings['scale_end'] ) ? $settings['scale_end'] : 1 ) . ';';
		} elseif ( 'slide' === $animation ) {
			$direction = ! empty( $settings['slide_direction'] ) ? $settings['slide_direction'] : 'bottom';
			$style    .= ' --eap-image-slide-distance: ' . esc_attr( isset( $settings['slide_distance'] ) ? (int) $settings['slide_distance'] : 40 ) . 'px;';
		} elseif ( 'skew-reveal' === $animation ) {
			$style .= ' --eap-image-skew-angle: ' . esc_attr( isset( $settings['skew_angle'] ) ? $settings['skew_angle'] : 18 ) . 'deg;';
			$style .= ' --eap-image-skew-distance: ' . esc_attr( isset( $settings['skew_translate_distance'] ) ? (int) $settings['skew_translate_distance'] : 40 ) . 'px;';
		} elseif ( 'flip' === $animation ) {
			$direction = ! empty( $settings['flip_direction'] ) ? $settings['flip_direction'] : 'flip-x';
			$style    .= ' --eap-image-flip-angle: ' . esc_attr( isset( $settings['flip_angle'] ) ? $settings['flip_angle'] : 88 ) . 'deg;';
			$style    .= ' --eap-image-flip-perspective: ' . esc_attr( isset( $settings['flip_perspective'] ) ? (int) $settings['flip_perspective'] : 800 ) . 'px;';
		}

		if ( 'none' !== $animation ) {
			$classes[] = 'eap-image-widget--animated';
			$classes[] = 'eap-image-widget--' . $animation;
			$classes[] = 'eap-image-widget--dir-' . $direction;

			if ( 'yes' === $settings['enable_fade'] ) {
				$classes[] = 'eap-image-widget--fade';
			}

			if ( 'scale' === $animation && 'yes' === $settings['scale_opacity'] ) {
				$classes[] = 'eap-image-widget--fade';
			}
		}

		return array(
			'classes' => $classes,
			'style'   => $style,
		);
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['gallery_items'] ) && is_array( $settings['gallery_items'] ) ? $settings['gallery_items'] : array();
		$gallery_uid = 'eap-gallery-' . $this->get_id();

		if ( empty( $items ) ) {
			return;
		}

		$gallery_classes = array(
			'eap-widget',
			'eap-image-gallery',
			'eap-image-gallery--' . ( ! empty( $settings['gallery_style'] ) ? $settings['gallery_style'] : 'basic' ),
			'eap-image-gallery--hover-' . ( ! empty( $settings['hover_effect'] ) ? $settings['hover_effect'] : 'zoom-in' ),
		);

		$this->add_render_attribute(
			'gallery',
			array(
				'class'                   => $gallery_classes,
				'data-eap-gallery-smooth' => ! empty( $settings['scroll_smooth'] ) ? 'yes' : 'no',
			)
		);

		$animation_data = $this->get_image_animation_data( $settings );
		?>
		<div <?php echo $this->get_render_attribute_string( 'gallery' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$image_settings = array_merge( $settings, $item );
				$image_html     = Group_Control_Image_Size::get_attachment_image_html( $image_settings, 'image_size', 'item_image' );

				if ( empty( $image_html ) && ! empty( $item['item_image']['url'] ) ) {
					$image_html = sprintf( '<img src="%1$s" alt="" />', esc_url( $item['item_image']['url'] ) );
				}

				if ( empty( $image_html ) ) {
					continue;
				}

				$item_key = 'gallery_item_' . $index;
				$parallax_key = $item_key . '_parallax';
				$item_style = $animation_data['style'];

				if ( ! empty( $settings['scroll_smooth'] ) ) {
					$item_style .= ' --eap-gallery-speed: ' . esc_attr( isset( $settings['data_speed'] ) ? $settings['data_speed'] : 0.9 ) . ';';
					$item_style .= ' --eap-gallery-lag: ' . esc_attr( isset( $settings['data_lag'] ) ? $settings['data_lag'] : 0.5 ) . ';';
				}

				$this->add_render_attribute(
					$item_key,
					array(
						'class'                 => $animation_data['classes'],
						'style'                 => $item_style,
						'data-eap-gallery-item' => 'true',
					)
				);

				$this->add_render_attribute(
					$parallax_key,
					array(
						'class'                  => 'eap-image-gallery__parallax',
						'data-eap-gallery-speed' => isset( $settings['data_speed'] ) ? $settings['data_speed'] : 0.9,
						'data-eap-gallery-lag'   => isset( $settings['data_lag'] ) ? $settings['data_lag'] : 0.5,
					)
				);

				$link_key   = $item_key . '_link';
				$lightbox_enabled = 'yes' === $settings['open_lightbox'];
				$has_image_url    = ! empty( $item['item_image']['url'] );
				$has_link         = ! empty( $item['item_link']['url'] );
				$link_tag         = ( $lightbox_enabled && $has_image_url ) || $has_link ? 'a' : 'div';
				$link_attrs = array(
					'class' => 'eap-image-gallery__link',
				);

				if ( $lightbox_enabled && $has_image_url ) {
					$link_attrs['href']                           = esc_url( $item['item_image']['url'] );
					$link_attrs['data-elementor-open-lightbox']  = 'yes';
					$link_attrs['data-elementor-lightbox-slideshow'] = $gallery_uid;
				} elseif ( $has_link ) {
					$link_attrs['href'] = esc_url( $item['item_link']['url'] );

					if ( ! empty( $item['item_link']['is_external'] ) ) {
						$link_attrs['target'] = '_blank';
						$link_attrs['rel']    = 'noopener';
					}

					if ( ! empty( $item['item_link']['nofollow'] ) ) {
						$link_attrs['rel'] = isset( $link_attrs['rel'] ) ? $link_attrs['rel'] . ' nofollow' : 'nofollow';
					}
				}

				$this->add_render_attribute( $link_key, $link_attrs );
				?>
				<article <?php echo $this->get_render_attribute_string( $item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<div <?php echo $this->get_render_attribute_string( $parallax_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<<?php echo esc_html( $link_tag ); ?> <?php echo $this->get_render_attribute_string( $link_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<div class="eap-image-widget__media eap-image-gallery__media">
								<?php echo wp_kses_post( $image_html ); ?>
								<span class="eap-image-gallery__overlay" aria-hidden="true"></span>
								<?php if ( 'yes' === $settings['show_icon'] ) : ?>
									<span class="eap-image-gallery__icon" aria-hidden="true">
										<?php Icons_Manager::render_icon( $settings['hover_icon'], array( 'aria-hidden' => 'true' ) ); ?>
									</span>
								<?php endif; ?>
							</div>
						</<?php echo esc_html( $link_tag ); ?>>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
