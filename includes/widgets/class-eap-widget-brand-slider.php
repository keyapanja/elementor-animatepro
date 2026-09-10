<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class EAP_Widget_Brand_Slider extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-brand-slider';
	}

	public function get_title() {
		return __( 'Brand Slider', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_style_depends() {
		return $this->eap_with_swiper_style( $this->get_widget_style_depends( 'brand-slider' ) );
	}

	public function get_script_depends() {
		$deps = array(
			'eap-core-runtime',
			'eap-brand-slider-script',
		);
		if ( wp_script_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}

		return array_unique( $deps );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_brand_slider',
			array(
				'label' => __( 'Brand Slider', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'slide_content',
			array(
				'label'   => __( 'Slide Content', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => array(
					'text'  => __( 'Text', 'elementor-animatepro' ),
					'image' => __( 'Image', 'elementor-animatepro' ),
				),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'text',
			array(
				'label'       => __( 'Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Content', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'         => __( 'Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Items', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array( 'text' => __( 'Content', 'elementor-animatepro' ) ),
					array( 'text' => __( '(Health Advisor & Coach)', 'elementor-animatepro' ) ),
					array( 'text' => __( 'News', 'elementor-animatepro' ) ),
					array( 'text' => __( 'Creative Director', 'elementor-animatepro' ) ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'image_size',
				'default'   => 'full',
				'condition' => array(
					'slide_content' => 'image',
				),
			)
		);

		$this->add_control(
			'text_separator',
			array(
				'label'   => __( 'Text Separator', 'elementor-animatepro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'far fa-star',
					'library' => 'fa-regular',
				),
			)
		);

		$this->add_control(
			'brand_slider_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<strong>' . esc_html__( 'Notice', 'elementor-animatepro' ) . '</strong><br />' . esc_html__( 'Avoid using "Auto" for Slides Per View, as it may cause autoplay to stop in Safari browsers.', 'elementor-animatepro' ),
				'content_classes' => 'eap-control-notice',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_options',
			array(
				'label' => __( 'Slider Options', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'slides_per_view',
			array(
				'label'          => __( 'Slides to Show', 'elementor-animatepro' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'auto' => __( 'Auto', 'elementor-animatepro' ),
					'1'    => '1',
					'2'    => '2',
					'3'    => '3',
					'4'    => '4',
					'5'    => '5',
					'6'    => '6',
				),
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'   => __( 'Autoplay', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'yes',
				'options' => array(
					'yes' => __( 'Yes', 'elementor-animatepro' ),
					'no'  => __( 'No', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => __( 'Autoplay Delay', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 0,
				'condition' => array(
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'autoplay_interaction',
			array(
				'label'     => __( 'Autoplay Interaction', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'yes',
				'options'   => array(
					'yes' => __( 'Yes', 'elementor-animatepro' ),
					'no'  => __( 'No', 'elementor-animatepro' ),
				),
				'condition' => array(
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'allow_touch_move',
			array(
				'label'   => __( 'Allow Touch Move', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'no',
				'options' => array(
					'yes' => __( 'Yes', 'elementor-animatepro' ),
					'no'  => __( 'No', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'   => __( 'Loop', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'yes',
				'options' => array(
					'yes' => __( 'Yes', 'elementor-animatepro' ),
					'no'  => __( 'No', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'mousewheel',
			array(
				'label'        => __( 'Mousewheel', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'If you want to use mousewheel, please disable loop.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'animation_speed',
			array(
				'label'   => __( 'Animation Speed (s)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5,
				'min'     => 0.1,
				'step'    => 0.1,
			)
		);

		$this->add_responsive_control(
			'space_between',
			array(
				'label'      => __( 'Space Between', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 160,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
			)
		);

		$this->add_control(
			'enable_grid',
			array(
				'label'        => __( 'Enable Grid', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'navigation',
			array(
				'label'        => __( 'Navigation', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'prev_icon',
			array(
				'label'     => __( 'Previous Arrow Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'navigation' => 'yes',
				),
			)
		);

		$this->add_control(
			'next_icon',
			array(
				'label'     => __( 'Next Arrow Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'navigation' => 'yes',
				),
			)
		);

		$this->add_control(
			'pagination',
			array(
				'label'        => __( 'Pagination', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'     => __( 'Pagination Type', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bullets',
				'options'   => array(
					'bullets'  => __( 'Bullets', 'elementor-animatepro' ),
					'fraction' => __( 'Fraction', 'elementor-animatepro' ),
					'progressbar' => __( 'Progress Bar', 'elementor-animatepro' ),
				),
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);

		$this->add_control(
			'direction_mode',
			array(
				'label'   => __( 'Left/Right Direction', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => __( 'Left', 'elementor-animatepro' ),
					'right' => __( 'Right', 'elementor-animatepro' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'section_style_text',
			array(
				'label' => __( 'Text', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-brand-slider__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'text_hover_color',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-brand-slider__item-link:hover .eap-brand-slider__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .eap-brand-slider__text',
			)
		);

		if ( class_exists( '\Elementor\Group_Control_Text_Stroke' ) ) {
			$this->add_group_control(
				Group_Control_Text_Stroke::get_type(),
				array(
					'name'     => 'text_stroke',
					'selector' => '{{WRAPPER}} .eap-brand-slider__text',
				)
			);
		}

		$this->add_control(
			'separator_color',
			array(
				'label'     => __( 'Separator Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-brand-slider__separator' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-brand-slider__separator svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'separator_size',
			array(
				'label'      => __( 'Separator Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-brand-slider__separator' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			array(
				'label'     => __( 'Image', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'slide_content' => 'image',
				),
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'      => __( 'Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 1200,
					),
					'%'  => array(
						'min' => 5,
						'max' => 100,
					),
					'vw' => array(
						'min' => 5,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-brand-slider__image-wrap img' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%;',
				),
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 800,
					),
					'vh' => array(
						'min' => 5,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-brand-slider__image-wrap img' => 'height: {{SIZE}}{{UNIT}}; object-fit: contain;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			array(
				'label' => __( 'Slider Navigation', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'nav_icon_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 8, 'max' => 80 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-brand-slider__arrow' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_circle_size',
			array(
				'label'      => __( 'Circle Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 20, 'max' => 200 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-brand-slider__arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'nav_border',
				'selector' => '{{WRAPPER}} .eap-brand-slider__arrow',
			)
		);

		$this->add_responsive_control(
			'nav_border_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-brand-slider__arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-brand-slider__navigation' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_nav_colors' );

		$this->start_controls_tab(
			'tab_nav_normal',
			array(
				'label' => __( 'Normal', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'nav_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-brand-slider__arrow' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-brand-slider__arrow svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'nav_background',
				'selector' => '{{WRAPPER}} .eap-brand-slider__arrow',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_nav_hover',
			array(
				'label' => __( 'Hover', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'nav_color_hover',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-brand-slider__arrow:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-brand-slider__arrow:hover svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'nav_background_hover',
				'selector' => '{{WRAPPER}} .eap-brand-slider__arrow:hover',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'nav_position_type',
			array(
				'label'   => __( 'Position Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default'  => __( 'Default', 'elementor-animatepro' ),
					'absolute' => __( 'Absolute', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-brand-slider__navigation' => 'position: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_alignment',
			array(
				'label'   => __( 'Alignment', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Start', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'End', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-right',
					),
					'space-between' => array(
						'title' => __( 'Space', 'elementor-animatepro' ),
						'icon'  => 'eicon-justify-space-between-h',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .eap-brand-slider__navigation' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 120 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-brand-slider__navigation' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pagination',
			array(
				'label' => __( 'Slider Pagination', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-brand-slider__pagination .swiper-pagination-bullet' => 'background: {{VALUE}};',
					'{{WRAPPER}} .eap-brand-slider__pagination' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_active_color',
			array(
				'label'     => __( 'Active Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-brand-slider__pagination .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
					'{{WRAPPER}} .eap-brand-slider__pagination .swiper-pagination-progressbar-fill' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 4, 'max' => 40 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-brand-slider__pagination .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-brand-slider__pagination' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pagination_border',
				'selector' => '{{WRAPPER}} .eap-brand-slider__pagination .swiper-pagination-bullet',
			)
		);

		$this->add_responsive_control(
			'pagination_border_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-brand-slider__pagination .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pagination_direction',
			array(
				'label'   => __( 'Direction', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'row',
				'options' => array(
					'row'    => __( 'Row', 'elementor-animatepro' ),
					'column' => __( 'Column', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-brand-slider__pagination' => 'flex-direction: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_position',
			array(
				'label'   => __( 'Position', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default'  => __( 'Default', 'elementor-animatepro' ),
					'absolute' => __( 'Absolute', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-brand-slider__pagination' => 'position: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_alignment',
			array(
				'label'   => __( 'Alignment', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
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
				'selectors' => array(
					'{{WRAPPER}} .eap-brand-slider__pagination-wrap' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();

		if ( empty( $items ) ) {
			return;
		}

		$config = array(
			'slidesDesktop' => ( isset( $settings['slides_per_view'] ) && 'auto' === $settings['slides_per_view'] ) ? 'auto' : ( isset( $settings['slides_per_view'] ) ? (int) $settings['slides_per_view'] : 3 ),
			'slidesTablet'  => ( isset( $settings['slides_per_view_tablet'] ) && 'auto' === $settings['slides_per_view_tablet'] ) ? 'auto' : ( isset( $settings['slides_per_view_tablet'] ) ? (int) $settings['slides_per_view_tablet'] : 2 ),
			'slidesMobile'  => ( isset( $settings['slides_per_view_mobile'] ) && 'auto' === $settings['slides_per_view_mobile'] ) ? 'auto' : ( isset( $settings['slides_per_view_mobile'] ) ? (int) $settings['slides_per_view_mobile'] : 1 ),
			'spaceBetween'  => isset( $settings['space_between']['size'] ) ? (int) $settings['space_between']['size'] : 20,
			'loop'          => 'yes' === $settings['loop'],
			'autoplay'      => 'yes' === $settings['autoplay'],
			'autoplayDelay' => isset( $settings['autoplay_delay'] ) ? (int) $settings['autoplay_delay'] * 1000 : 1000,
			'autoplayInteraction' => 'yes' === $settings['autoplay_interaction'],
			'allowTouchMove' => 'yes' === $settings['allow_touch_move'],
			'mousewheel'    => 'yes' === $settings['mousewheel'],
			'speed'         => isset( $settings['animation_speed'] ) ? (float) $settings['animation_speed'] * 1000 : 5000,
			'grid'          => 'yes' === $settings['enable_grid'],
			'navigation'    => 'yes' === $settings['navigation'],
			'pagination'    => 'yes' === $settings['pagination'],
			'paginationType' => ! empty( $settings['pagination_type'] ) ? $settings['pagination_type'] : 'bullets',
			'directionMode' => ! empty( $settings['direction_mode'] ) ? $settings['direction_mode'] : 'left',
		);

		$this->add_render_attribute(
			'wrapper',
			array(
				'class' => array(
					'eap-widget',
					'eap-brand-slider',
					'eap-brand-slider--' . ( ! empty( $settings['slide_content'] ) ? $settings['slide_content'] : 'text' ),
				),
				'style' => '--eap-brand-space:' . ( isset( $settings['space_between']['size'] ) ? (int) $settings['space_between']['size'] : 20 ) . 'px;',
			)
		);

		$this->add_render_attribute(
			'swiper',
			array(
				'class'                  => 'eap-brand-slider__swiper swiper',
				'data-eap-brand-slider'  => wp_json_encode( $config ),
			)
		);
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div <?php echo $this->get_render_attribute_string( 'swiper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div class="swiper-wrapper">
					<?php foreach ( $items as $index => $item ) : ?>
						<?php
						$link_open  = '';
						$link_close = '';
						if ( ! empty( $item['link']['url'] ) ) {
							$attrs = ' href="' . esc_url( $item['link']['url'] ) . '"';
							$rels  = array();
							if ( ! empty( $item['link']['is_external'] ) ) {
								$attrs .= ' target="_blank"';
								$rels[] = 'noopener';
							}
							if ( ! empty( $item['link']['nofollow'] ) ) {
								$rels[] = 'nofollow';
							}
							if ( ! empty( $rels ) ) {
								$attrs .= ' rel="' . esc_attr( implode( ' ', array_unique( $rels ) ) ) . '"';
							}
							$link_open  = '<a class="eap-brand-slider__item-link"' . $attrs . '>';
							$link_close = '</a>';
						}
						?>
						<div class="swiper-slide eap-brand-slider__slide">
							<div class="eap-brand-slider__item">
								<?php echo wp_kses_post( $link_open ); ?>
								<?php if ( 'image' === $settings['slide_content'] ) : ?>
									<div class="eap-brand-slider__image-wrap">
										<?php echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( array_merge( $settings, $item ), 'image_size', 'image' ) ); ?>
									</div>
								<?php else : ?>
									<div class="eap-brand-slider__text"><?php echo esc_html( isset( $item['text'] ) ? $item['text'] : '' ); ?></div>
								<?php endif; ?>
								<?php echo wp_kses_post( $link_close ); ?>
								<?php if ( 'text' === $settings['slide_content'] ) : ?>
									<span class="eap-brand-slider__separator" aria-hidden="true">
										<?php Icons_Manager::render_icon( $settings['text_separator'], array( 'aria-hidden' => 'true' ) ); ?>
									</span>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( 'yes' === $settings['navigation'] ) : ?>
				<div class="eap-brand-slider__navigation">
					<button type="button" class="eap-brand-slider__arrow eap-brand-slider__arrow--prev" aria-label="<?php esc_attr_e( 'Previous slide', 'elementor-animatepro' ); ?>">
						<?php Icons_Manager::render_icon( $settings['prev_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</button>
					<button type="button" class="eap-brand-slider__arrow eap-brand-slider__arrow--next" aria-label="<?php esc_attr_e( 'Next slide', 'elementor-animatepro' ); ?>">
						<?php Icons_Manager::render_icon( $settings['next_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</button>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['pagination'] ) : ?>
				<div class="eap-brand-slider__pagination-wrap">
					<div class="eap-brand-slider__pagination"></div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
