<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class EAP_Widget_Testimonial_Slider extends EAP_Widget_Testimonial {

	public function get_name() {
		return 'eap-testimonial-slider';
	}

	public function get_title() {
		return __( 'Testimonial Slider', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_style_depends() {
		return array(
			'eap-core',
			'eap-testimonial',
			'eap-testimonial-slider',
		);
	}

	public function get_script_depends() {
		$deps = array(
			'eap-core-runtime',
			'eap-visibility-script',
			'eap-testimonial-slider-script',
		);

		if ( wp_script_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}

		return array_unique( $deps );
	}

	protected function register_controls() {
		$this->register_slider_layout_controls();
		$this->register_slider_content_controls();
		$this->register_slider_settings_controls();
		$this->register_slider_navigation_controls();
		$this->register_animation_controls();
		$this->register_style_controls();
		$this->register_slider_style_controls();
	}

	protected function register_slider_layout_controls() {
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
				'label'   => __( 'User Info Alignment', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'   => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default' => 'left',
			)
		);

		$this->end_controls_section();
	}

	protected function register_slider_content_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Slides', 'elementor-animatepro' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'testimonial_content',
			array(
				'label'       => __( 'Testimonial', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'ContentAI has revolutionized our content workflow. The quality of the articles is outstanding, and it saves us hours of work every week.', 'elementor-animatepro' ),
				'rows'        => 5,
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label'       => __( 'Name', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'John Doe', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'designation',
			array(
				'label'       => __( 'Designation', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Marketing Director, TechCorp', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'avatar',
			array(
				'label'   => __( 'User Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'background_image',
			array(
				'label'   => __( 'Background Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'rating',
			array(
				'label'   => __( 'Star Rating', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4.5,
				'min'     => 0,
				'max'     => 5,
				'step'    => 0.5,
			)
		);

		$this->add_control(
			'slides',
			array(
				'label'       => __( 'Testimonials', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => array(
					array(
						'name'        => __( 'John Doe', 'elementor-animatepro' ),
						'designation' => __( 'Marketing Director, TechCorp', 'elementor-animatepro' ),
					),
					array(
						'name'        => __( 'Donald Jackman', 'elementor-animatepro' ),
						'designation' => __( 'Content Creator', 'elementor-animatepro' ),
					),
					array(
						'name'        => __( 'Theo Balick', 'elementor-animatepro' ),
						'designation' => __( '@theo_b', 'elementor-animatepro' ),
					),
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

		$this->end_controls_section();
	}

	protected function register_slider_settings_controls() {
		$this->start_controls_section(
			'section_slider_settings',
			array(
				'label' => __( 'Slider Settings', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'slides_per_view',
			array(
				'label'          => __( 'Slides To Show', 'elementor-animatepro' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
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
						'max' => 80,
					),
				),
				'default'    => array(
					'size' => 24,
					'unit' => 'px',
				),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => __( 'Loop', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => __( 'Autoplay Delay (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3500,
				'condition' => array(
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => __( 'Pause On Hover', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'allow_touch_move',
			array(
				'label'        => __( 'Allow Touch Move', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => __( 'Transition Speed (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 650,
			)
		);

		$this->end_controls_section();
	}

	protected function register_slider_navigation_controls() {
		$this->start_controls_section(
			'section_navigation',
			array(
				'label' => __( 'Navigation & Pagination', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_arrows',
			array(
				'label'        => __( 'Arrows', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'prev_icon',
			array(
				'label'     => __( 'Previous Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'next_icon',
			array(
				'label'     => __( 'Next Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_pagination',
			array(
				'label'        => __( 'Pagination', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
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
					'bullets'     => __( 'Dots', 'elementor-animatepro' ),
					'fraction'    => __( 'Fraction', 'elementor-animatepro' ),
					'progressbar' => __( 'Progress', 'elementor-animatepro' ),
				),
				'condition' => array(
					'show_pagination' => 'yes',
				),
			)
		);

		$this->add_control(
			'pagination_position',
			array(
				'label'     => __( 'Pagination Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'outside',
				'options'   => array(
					'inside'  => __( 'Inside', 'elementor-animatepro' ),
					'outside' => __( 'Outside', 'elementor-animatepro' ),
				),
				'condition' => array(
					'show_pagination' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_slider_style_controls() {
		$this->start_controls_section(
			'section_slider_nav_style',
			array(
				'label'     => __( 'Slider Navigation', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_arrows' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 60,
					),
				),
				'default'    => array(
					'size' => 18,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-slider' => '--eap-testimonial-arrow-icon-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-testimonial-slider__arrow' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-testimonial-slider__arrow .e-font-icon-svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-testimonial-slider__arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-testimonial-slider__arrow svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_box_size',
			array(
				'label'      => __( 'Container Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 24,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 48,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-slider' => '--eap-testimonial-arrow-box-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-testimonial-slider__arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'arrow_colors_tabs' );

		$this->start_controls_tab(
			'arrow_colors_normal',
			array(
				'label' => __( 'Normal', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-slider__arrow' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-testimonial-slider__arrow svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_background',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-slider__arrow' => 'background: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'arrow_colors_hover',
			array(
				'label' => __( 'Hover', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'arrow_hover_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-slider__arrow:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-testimonial-slider__arrow:hover svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_hover_background',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-slider__arrow:hover' => 'background: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'arrow_border',
				'selector' => '{{WRAPPER}} .eap-testimonial-slider__arrow',
			)
		);

		$this->add_responsive_control(
			'arrow_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-slider__arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_horizontal_offset',
			array(
				'label'      => __( 'Horizontal Offset', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => -120,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'condition'  => array(
					'show_arrows' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-slider' => '--eap-testimonial-arrow-offset-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_vertical_offset',
			array(
				'label'      => __( 'Vertical Offset', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => -120,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => 'px',
				),
				'condition'  => array(
					'show_arrows' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-slider' => '--eap-testimonial-arrow-offset-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_pagination_style',
			array(
				'label'     => __( 'Slider Pagination', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_pagination' => 'yes',
				),
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-slider__pagination' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-testimonial-slider__pagination .swiper-pagination-bullet' => 'background: {{VALUE}};',
					'{{WRAPPER}} .eap-testimonial-slider__pagination.swiper-pagination-progressbar' => 'background: color-mix(in srgb, {{VALUE}} 20%, transparent);',
				),
			)
		);

		$this->add_control(
			'pagination_active_color',
			array(
				'label'     => __( 'Active Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-testimonial-slider__pagination .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
					'{{WRAPPER}} .eap-testimonial-slider__pagination .swiper-pagination-progressbar-fill' => 'background: {{VALUE}};',
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
					'px' => array(
						'min' => 4,
						'max' => 36,
					),
				),
				'default'    => array(
					'size' => 10,
					'unit' => 'px',
				),
				'condition'  => array(
					'show_pagination' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-slider' => '--eap-testimonial-pagination-size: {{SIZE}}{{UNIT}};',
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
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'size' => 10,
					'unit' => 'px',
				),
				'condition'  => array(
					'show_pagination' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-slider' => '--eap-testimonial-pagination-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_spacing_top',
			array(
				'label'      => __( 'Top Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'size' => 18,
					'unit' => 'px',
				),
				'condition'  => array(
					'show_pagination' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-testimonial-slider__pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function get_slide_settings( $settings, $slide ) {
		$mapped = $settings;
		$mapped['testimonial_content'] = ! empty( $slide['testimonial_content'] ) ? $slide['testimonial_content'] : '';
		$mapped['name']                = ! empty( $slide['name'] ) ? $slide['name'] : '';
		$mapped['designation']         = ! empty( $slide['designation'] ) ? $slide['designation'] : '';
		$mapped['avatar']              = ! empty( $slide['avatar'] ) ? $slide['avatar'] : array();
		$mapped['background_image']    = ! empty( $slide['background_image'] ) ? $slide['background_image'] : array();
		$mapped['rating']              = isset( $slide['rating'] ) ? $slide['rating'] : 0;

		return $mapped;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$slides   = ! empty( $settings['slides'] ) && is_array( $settings['slides'] ) ? $settings['slides'] : array();

		if ( empty( $slides ) ) {
			return;
		}

		$slider_settings = array(
			'slidesDesktop'  => isset( $settings['slides_per_view'] ) ? (int) $settings['slides_per_view'] : 3,
			'slidesTablet'   => isset( $settings['slides_per_view_tablet'] ) ? (int) $settings['slides_per_view_tablet'] : ( isset( $settings['slides_per_view'] ) ? (int) $settings['slides_per_view'] : 2 ),
			'slidesMobile'   => isset( $settings['slides_per_view_mobile'] ) ? (int) $settings['slides_per_view_mobile'] : 1,
			'spaceBetween'   => ! empty( $settings['space_between']['size'] ) ? (int) $settings['space_between']['size'] : 24,
			'loop'           => ! empty( $settings['loop'] ),
			'autoplay'       => ! empty( $settings['autoplay'] ),
			'autoplayDelay'  => ! empty( $settings['autoplay_delay'] ) ? (int) $settings['autoplay_delay'] : 3500,
			'pauseOnHover'   => ! empty( $settings['pause_on_hover'] ),
			'allowTouchMove' => ! empty( $settings['allow_touch_move'] ),
			'speed'          => ! empty( $settings['speed'] ) ? (int) $settings['speed'] : 650,
			'navigation'     => ! empty( $settings['show_arrows'] ),
			'pagination'     => ! empty( $settings['show_pagination'] ),
			'paginationType' => ! empty( $settings['pagination_type'] ) ? $settings['pagination_type'] : 'bullets',
		);

		$this->add_render_attribute(
			'slider',
			array(
				'class'                     => array(
					'eap-widget',
					'eap-testimonial-slider',
					'eap-testimonial-slider--layout-' . ( ! empty( $settings['layout'] ) ? $settings['layout'] : 'background-overlay' ),
					'eap-testimonial-slider--pagination-' . ( ! empty( $settings['pagination_position'] ) ? $settings['pagination_position'] : 'outside' ),
				),
			)
		);

		$this->add_render_attribute(
			'swiper',
			array(
				'class'                       => array(
					'eap-testimonial-slider__swiper',
					'swiper',
				),
				'data-eap-testimonial-slider' => wp_json_encode( $slider_settings ),
			)
		);
		?>
		<div <?php echo $this->get_render_attribute_string( 'slider' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="eap-testimonial-slider__shell">
				<?php if ( ! empty( $settings['show_arrows'] ) ) : ?>
					<button class="eap-testimonial-slider__arrow eap-testimonial-slider__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous testimonial', 'elementor-animatepro' ); ?>">
						<?php Icons_Manager::render_icon( $settings['prev_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</button>
				<?php endif; ?>

				<div <?php echo $this->get_render_attribute_string( 'swiper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<div class="swiper-wrapper">
						<?php foreach ( $slides as $index => $slide ) : ?>
							<?php $slide_settings = $this->get_slide_settings( $settings, $slide ); ?>
							<div class="swiper-slide eap-testimonial-slider__slide">
								<?php $this->render_testimonial_box_markup( $slide_settings, array( 'eap-testimonial-slider__item' ), array(), 'testimonial_slider_' . $index ); ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( ! empty( $settings['show_arrows'] ) ) : ?>
					<button class="eap-testimonial-slider__arrow eap-testimonial-slider__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next testimonial', 'elementor-animatepro' ); ?>">
						<?php Icons_Manager::render_icon( $settings['next_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</button>
				<?php endif; ?>

				<?php if ( ! empty( $settings['show_pagination'] ) && 'inside' === ( ! empty( $settings['pagination_position'] ) ? $settings['pagination_position'] : 'outside' ) ) : ?>
					<div class="eap-testimonial-slider__pagination"></div>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $settings['show_pagination'] ) && 'outside' === ( ! empty( $settings['pagination_position'] ) ? $settings['pagination_position'] : 'outside' ) ) : ?>
				<div class="eap-testimonial-slider__pagination"></div>
			<?php endif; ?>
		</div>
		<?php
	}
}
