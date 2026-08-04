<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class EAP_Widget_Advanced_Slider extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-advanced-slider';
	}

	public function get_title() {
		return __( 'Advanced Slider', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-slider-3d';
	}

	public function get_keywords() {
		return array( 'advanced', 'slider', 'coverflow', 'card', 'stack', '3d', 'cards', 'eap', 'animatepro' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'advanced-slider' );
	}

	public function get_script_depends() {
		$deps = array(
			'eap-core-runtime',
			'eap-advanced-slider-script',
		);
		if ( wp_script_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}
		return $deps;
	}

	protected function register_controls() {
		$this->register_layout_section();
		$this->register_items_section();
		$this->register_coverflow_effect_section();
		$this->register_card_stack_effect_section();
		$this->register_vertical_effect_section();
		$this->register_slider_settings_section();
		$this->register_navigation_section();

		$this->register_card_style_section();
		$this->register_image_style_section();
		$this->register_title_style_section();
		$this->register_subtitle_style_section();
		$this->register_description_style_section();
		$this->register_button_style_section();
		$this->register_slider_nav_style_section();
		$this->register_slider_pagination_style_section();
	}

	/* ============================================================ *
	 * Content tab
	 * ============================================================ */

	private function register_layout_section() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'coverflow',
				'options' => array(
					'coverflow'  => __( 'Coverflow (3D Tilt)', 'elementor-animatepro' ),
					'card-stack' => __( 'Card Stack (Fanned)', 'elementor-animatepro' ),
					'vertical'   => __( 'Vertical (Stacked Scroll)', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'content_position',
			array(
				'label'     => __( 'Content Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'below-active',
				'options'   => array(
					'below-active' => __( 'Below Active Slide Only', 'elementor-animatepro' ),
					'overlay'      => __( 'Overlay on Each Slide', 'elementor-animatepro' ),
					'below-all'    => __( 'Below Every Slide', 'elementor-animatepro' ),
					'hidden'       => __( 'Hidden (Image Only)', 'elementor-animatepro' ),
				),
				'condition' => array( 'layout' => array( 'coverflow', 'card-stack' ) ),
			)
		);

		$this->add_control(
			'vertical_image_position',
			array(
				'label'     => __( 'Image Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
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
				'default'   => 'left',
				'toggle'    => false,
				'condition' => array( 'layout' => 'vertical' ),
			)
		);

		$this->end_controls_section();
	}

	private function register_items_section() {
		$this->start_controls_section(
			'section_items',
			array(
				'label' => __( 'Slides', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
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
				'label'       => __( 'Subtitle / Eyebrow', 'elementor-animatepro' ),
				'description' => __( 'Short text above the title (e.g. category, badge).', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Slide Title', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label' => __( 'Description', 'elementor-animatepro' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 3,
			)
		);

		$repeater->add_control(
			'button_text',
			array(
				'label'   => __( 'Button Text', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$repeater->add_control(
			'button_link',
			array(
				'label'         => __( 'Button Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'default'       => array( 'url' => '' ),
				'show_external' => true,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Slides', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array( 'title' => __( 'Slide One', 'elementor-animatepro' ) ),
					array( 'title' => __( 'Slide Two', 'elementor-animatepro' ) ),
					array( 'title' => __( 'Slide Three', 'elementor-animatepro' ) ),
					array( 'title' => __( 'Slide Four', 'elementor-animatepro' ) ),
					array( 'title' => __( 'Slide Five', 'elementor-animatepro' ) ),
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

		$this->end_controls_section();
	}

	private function register_coverflow_effect_section() {
		$this->start_controls_section(
			'section_coverflow_effect',
			array(
				'label'     => __( 'Coverflow Effect', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'layout' => 'coverflow' ),
			)
		);

		$this->add_control(
			'cf_rotate',
			array(
				'label'       => __( 'Rotate (degrees)', 'elementor-animatepro' ),
				'description' => __( 'Tilt angle of side slides. 0 = flat, 50 = strong tilt.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 90, 'step' => 1 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 35 ),
			)
		);

		$this->add_control(
			'cf_stretch',
			array(
				'label'       => __( 'Stretch (px)', 'elementor-animatepro' ),
				'description' => __( 'Horizontal overlap of side slides. Negative pulls them inward.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => -200, 'max' => 200, 'step' => 5 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 0 ),
			)
		);

		$this->add_control(
			'cf_depth',
			array(
				'label'       => __( 'Depth (px)', 'elementor-animatepro' ),
				'description' => __( 'How far back side slides push along the Z axis.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 500, 'step' => 10 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 150 ),
			)
		);

		$this->add_control(
			'cf_modifier',
			array(
				'label'       => __( 'Effect Modifier', 'elementor-animatepro' ),
				'description' => __( 'Multiplier for the overall effect intensity.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0.1,
				'max'         => 3,
				'step'        => 0.1,
				'default'     => 1,
			)
		);

		$this->add_control(
			'cf_shadows',
			array(
				'label'       => __( 'Slide Shadows', 'elementor-animatepro' ),
				'description' => __( 'Swiper built-in dark shadows on tilted side slides.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
			)
		);

		$this->end_controls_section();
	}

	private function register_card_stack_effect_section() {
		$this->start_controls_section(
			'section_card_stack_effect',
			array(
				'label'     => __( 'Card Stack Effect', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'layout' => 'card-stack' ),
			)
		);

		$this->add_control(
			'cs_rotate_per_slide',
			array(
				'label'       => __( 'Rotation per Slide (degrees)', 'elementor-animatepro' ),
				'description' => __( 'How much each card behind tilts. 0 = no tilt.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 30, 'step' => 1 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 4 ),
			)
		);

		$this->add_control(
			'cs_offset_per_slide',
			array(
				'label'       => __( 'Offset per Slide (px)', 'elementor-animatepro' ),
				'description' => __( 'How far each card behind shifts. Higher = wider visible stack.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 80, 'step' => 1 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 10 ),
			)
		);

		$this->add_control(
			'cs_shadows',
			array(
				'label'       => __( 'Built-in Slide Shadows', 'elementor-animatepro' ),
				'description' => __( 'Swiper-rendered shadow overlays on cards behind the active. Turn off if you have your own card shadow set.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => '',
			)
		);

		$this->end_controls_section();
	}

	private function register_vertical_effect_section() {
		$this->start_controls_section(
			'section_vertical_effect',
			array(
				'label'     => __( 'Vertical Layout', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'layout' => 'vertical' ),
			)
		);

		$this->add_responsive_control(
			'vertical_slides_per_view',
			array(
				'label'          => __( 'Visible Slides', 'elementor-animatepro' ),
				'description'    => __( 'How many cards are visible in the viewport at once. Odd numbers center the active card.', 'elementor-animatepro' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
				),
			)
		);

		$this->add_responsive_control(
			'vertical_height',
			array(
				'label'      => __( 'Viewport Height', 'elementor-animatepro' ),
				'description' => __( 'Total height of the slider viewport. Each visible card divides this evenly.', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 300, 'max' => 1200, 'step' => 10 ),
					'vh' => array( 'min' => 40,  'max' => 100,  'step' => 1 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 720 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider--vertical .eap-adv-slider__swiper' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'vertical_image_width',
			array(
				'label'      => __( 'Image Width', 'elementor-animatepro' ),
				'description' => __( 'Width of the image column inside each card.', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array( 'min' => 20, 'max' => 60 ),
					'px' => array( 'min' => 120, 'max' => 500 ),
				),
				'default'    => array( 'unit' => '%', 'size' => 35 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider--vertical .eap-adv-slider__media'   => 'flex: 0 0 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-adv-slider--vertical .eap-adv-slider__content' => 'flex: 1 1 auto;',
				),
			)
		);

		$this->add_control(
			'vertical_centered',
			array(
				'label'       => __( 'Center Active Card', 'elementor-animatepro' ),
				'description' => __( 'Keep the active card vertically centered in the viewport.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
			)
		);

		$this->add_control(
			'vertical_arrows_position',
			array(
				'label'       => __( 'Arrows Position', 'elementor-animatepro' ),
				'description' => __( 'Where to anchor the prev / next arrows.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'split',
				'options'     => array(
					'split' => __( 'Split (Prev Left, Next Right)', 'elementor-animatepro' ),
					'left'  => __( 'Both on Left', 'elementor-animatepro' ),
					'right' => __( 'Both on Right', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'vertical_scroll_nav',
			array(
				'label'       => __( 'Scroll to Navigate', 'elementor-animatepro' ),
				'description' => __( 'Use the mousewheel / trackpad to move between slides while hovering the slider.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
			)
		);

		$this->end_controls_section();
	}

	private function register_slider_settings_section() {
		$this->start_controls_section(
			'section_slider_settings',
			array(
				'label' => __( 'Slider Settings', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'space_between',
			array(
				'label'      => __( 'Space Between', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 24 ),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'   => __( 'Loop', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'   => __( 'Autoplay', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => __( 'Autoplay Delay (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3500,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'     => __( 'Pause on Hover', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'allow_touch_move',
			array(
				'label'   => __( 'Allow Touch Drag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
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

	private function register_navigation_section() {
		$this->start_controls_section(
			'section_navigation',
			array(
				'label' => __( 'Navigation & Pagination', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_arrows',
			array(
				'label'   => __( 'Show Arrows', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'prev_icon',
			array(
				'label'     => __( 'Previous Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array( 'value' => 'fas fa-chevron-left', 'library' => 'fa-solid' ),
				'condition' => array( 'show_arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'next_icon',
			array(
				'label'     => __( 'Next Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array( 'value' => 'fas fa-chevron-right', 'library' => 'fa-solid' ),
				'condition' => array( 'show_arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'show_pagination',
			array(
				'label'   => __( 'Show Pagination', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'     => __( 'Pagination Type', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bullets',
				'options'   => array(
					'bullets'     => __( 'Bullets', 'elementor-animatepro' ),
					'fraction'    => __( 'Fraction', 'elementor-animatepro' ),
					'progressbar' => __( 'Progress Bar', 'elementor-animatepro' ),
				),
				'condition' => array( 'show_pagination' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* ============================================================ *
	 * Style tab
	 * ============================================================ */

	private function register_card_style_section() {
		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => __( 'Card', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'card_width',
			array(
				'label'      => __( 'Card Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 180, 'max' => 800 ),
					'%'  => array( 'min' => 20, 'max' => 80 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 360 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider__slide' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-adv-slider--card-stack .eap-adv-slider__swiper' => 'max-width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'layout' => array( 'coverflow', 'card-stack' ) ),
			)
		);

		$this->add_responsive_control(
			'card_height',
			array(
				'label'      => __( 'Card Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 220, 'max' => 900 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 500 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider__media' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'layout' => array( 'coverflow', 'card-stack' ) ),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top' => 14, 'right' => 14, 'bottom' => 14, 'left' => 14,
					'unit' => 'px', 'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider__card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Card Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-slider__card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Card Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .eap-adv-slider__card',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eap-adv-slider__card',
				'fields_options' => array(
					'box_shadow_type' => array( 'default' => 'yes' ),
					'box_shadow'      => array(
						'default' => array(
							'horizontal' => 0,
							'vertical'   => 30,
							'blur'       => 60,
							'spread'     => 0,
							'color'      => 'rgba(0,0,0,0.35)',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_image_style_section() {
		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => __( 'Card Image', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'image_object_fit',
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
					'{{WRAPPER}} .eap-adv-slider__media img' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'image_object_position',
			array(
				'label'     => __( 'Object Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center center',
				'options'   => array(
					'center center' => __( 'Center', 'elementor-animatepro' ),
					'center top'    => __( 'Top', 'elementor-animatepro' ),
					'center bottom' => __( 'Bottom', 'elementor-animatepro' ),
					'left center'   => __( 'Left', 'elementor-animatepro' ),
					'right center'  => __( 'Right', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-slider__media img' => 'object-position: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'image_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-slider__media' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider__media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'image_border',
				'label'    => __( 'Border', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-adv-slider__media',
			)
		);

		$this->add_responsive_control(
			'image_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider__media' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_title_style_section() {
		$this->start_controls_section(
			'section_title_style',
			array(
				'label'     => __( 'Card Title', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'content_position!' => 'hidden' ),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-slider__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-adv-slider__title',
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top' => 0, 'right' => 0, 'bottom' => 8, 'left' => 0,
					'unit' => 'px', 'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_subtitle_style_section() {
		$this->start_controls_section(
			'section_subtitle_style',
			array(
				'label'     => __( 'Card Subtitle', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'content_position!' => 'hidden' ),
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.7)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-slider__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .eap-adv-slider__subtitle',
			)
		);

		$this->add_responsive_control(
			'subtitle_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top' => 0, 'right' => 0, 'bottom' => 4, 'left' => 0,
					'unit' => 'px', 'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider__subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_description_style_section() {
		$this->start_controls_section(
			'section_description_style',
			array(
				'label'     => __( 'Card Description', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'content_position!' => 'hidden' ),
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.85)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-slider__description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .eap-adv-slider__description',
			)
		);

		$this->add_responsive_control(
			'desc_max_width',
			array(
				'label'      => __( 'Max Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 240, 'max' => 1200 ),
					'%'  => array( 'min' => 40, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 560 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider__description' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'desc_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top' => 0, 'right' => 0, 'bottom' => 16, 'left' => 0,
					'unit' => 'px', 'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider__description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_button_style_section() {
		$this->start_controls_section(
			'section_button_style',
			array(
				'label'     => __( 'Card Button', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'content_position!' => 'hidden' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'btn_typography',
				'selector' => '{{WRAPPER}} .eap-adv-slider__button',
			)
		);

		$this->start_controls_tabs( 'btn_states' );

		$this->start_controls_tab( 'btn_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'btn_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-slider__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-slider__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'btn_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'btn_color_hover',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-slider__button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-slider__button:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'btn_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24,
					'unit' => 'px', 'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'btn_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top' => 999, 'right' => 999, 'bottom' => 999, 'left' => 999,
					'unit' => 'px', 'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'btn_border',
				'selector' => '{{WRAPPER}} .eap-adv-slider__button',
			)
		);

		$this->end_controls_section();
	}

	private function register_slider_nav_style_section() {
		$this->start_controls_section(
			'section_slider_nav_style',
			array(
				'label'     => __( 'Slider Navigation', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_arrows' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'nav_box_size',
			array(
				'label'      => __( 'Button Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 28, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 42 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider' => '--eap-asl-arrow-box: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 32 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 16 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider' => '--eap-asl-arrow-icon: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'nav_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-slider__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15,17,22,0.78)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-slider__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_offset_x',
			array(
				'label'      => __( 'Side Offset', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => -60, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 0 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider' => '--eap-asl-arrow-offset-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_slider_pagination_style_section() {
		$this->start_controls_section(
			'section_slider_pagination_style',
			array(
				'label'     => __( 'Slider Pagination', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_pagination' => 'yes' ),
			)
		);

		$this->add_control(
			'pag_color',
			array(
				'label'     => __( 'Bullet Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.45)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-slider__pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pag_active_color',
			array(
				'label'     => __( 'Active Bullet Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-slider__pagination .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pag_size',
			array(
				'label'      => __( 'Bullet Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 4, 'max' => 24 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 10 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider' => '--eap-asl-pag-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pag_gap',
			array(
				'label'      => __( 'Gap Between Bullets', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 8 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider__pagination' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pag_margin_top',
			array(
				'label'      => __( 'Distance From Slider', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 18 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-slider__pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* ============================================================ *
	 * Render
	 * ============================================================ */

	private function get_image_html( $settings, $image_data ) {
		if ( empty( $image_data ) || ( empty( $image_data['url'] ) && empty( $image_data['id'] ) ) ) {
			return '';
		}
		$merged = array_merge( $settings, array( 'image' => $image_data ) );
		$html   = Group_Control_Image_Size::get_attachment_image_html( $merged, 'image_size', 'image' );
		if ( empty( $html ) && ! empty( $image_data['url'] ) ) {
			$html = '<img src="' . esc_url( $image_data['url'] ) . '" alt="" />';
		}
		return $html;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();

		if ( empty( $items ) ) {
			return;
		}

		$layout = ! empty( $settings['layout'] ) ? $settings['layout'] : 'coverflow';

		// Build the Swiper-config payload.
		$slider_data = array(
			'spaceBetween'   => isset( $settings['space_between']['size'] ) ? (int) $settings['space_between']['size'] : 24,
			'loop'           => ! empty( $settings['loop'] ) && 'yes' === $settings['loop'],
			'autoplay'       => ! empty( $settings['autoplay'] ) && 'yes' === $settings['autoplay'],
			'autoplayDelay'  => isset( $settings['autoplay_delay'] ) ? (int) $settings['autoplay_delay'] : 3500,
			'pauseOnHover'   => ! empty( $settings['pause_on_hover'] ) && 'yes' === $settings['pause_on_hover'],
			'allowTouchMove' => ! empty( $settings['allow_touch_move'] ) && 'yes' === $settings['allow_touch_move'],
			'speed'          => isset( $settings['speed'] ) ? (int) $settings['speed'] : 650,
			'navigation'     => ! empty( $settings['show_arrows'] ) && 'yes' === $settings['show_arrows'],
			'pagination'     => ! empty( $settings['show_pagination'] ) && 'yes' === $settings['show_pagination'],
			'paginationType' => ! empty( $settings['pagination_type'] ) ? $settings['pagination_type'] : 'bullets',
		);

		if ( 'coverflow' === $layout ) {
			$slider_data['effect']    = 'coverflow';
			$slider_data['cfRotate']  = isset( $settings['cf_rotate']['size'] ) ? (int) $settings['cf_rotate']['size'] : 35;
			$slider_data['cfStretch'] = isset( $settings['cf_stretch']['size'] ) ? (int) $settings['cf_stretch']['size'] : 0;
			$slider_data['cfDepth']   = isset( $settings['cf_depth']['size'] ) ? (int) $settings['cf_depth']['size'] : 150;
			$slider_data['cfModifier'] = isset( $settings['cf_modifier'] ) ? (float) $settings['cf_modifier'] : 1;
			$slider_data['cfShadows'] = ! empty( $settings['cf_shadows'] ) && 'yes' === $settings['cf_shadows'];
		} elseif ( 'card-stack' === $layout ) {
			$rotate  = isset( $settings['cs_rotate_per_slide']['size'] ) ? (int) $settings['cs_rotate_per_slide']['size'] : 4;
			$offset  = isset( $settings['cs_offset_per_slide']['size'] ) ? (int) $settings['cs_offset_per_slide']['size'] : 10;
			$shadows = ! empty( $settings['cs_shadows'] ) && 'yes' === $settings['cs_shadows'];

			$slider_data['effect']         = 'cards';
			$slider_data['creativeRotate'] = $rotate;
			$slider_data['creativeOffset'] = $offset;
			$slider_data['creativeShadows'] = $shadows;
		} else {
			// Vertical layout.
			$per_view        = isset( $settings['vertical_slides_per_view'] ) ? max( 1, (int) $settings['vertical_slides_per_view'] ) : 3;
			$per_view_tablet = isset( $settings['vertical_slides_per_view_tablet'] ) ? max( 1, (int) $settings['vertical_slides_per_view_tablet'] ) : 2;
			$per_view_mobile = isset( $settings['vertical_slides_per_view_mobile'] ) ? max( 1, (int) $settings['vertical_slides_per_view_mobile'] ) : 1;

			$slider_data['effect']               = 'slide';
			$slider_data['direction']            = 'vertical';
			$slider_data['verticalSlides']       = $per_view;
			$slider_data['verticalSlidesTablet'] = $per_view_tablet;
			$slider_data['verticalSlidesMobile'] = $per_view_mobile;
			$slider_data['verticalCentered']     = ! empty( $settings['vertical_centered'] ) && 'yes' === $settings['vertical_centered'];
			$slider_data['verticalScrollNav']    = ! empty( $settings['vertical_scroll_nav'] ) && 'yes' === $settings['vertical_scroll_nav'];
		}

		// Vertical has its own internal layout (image-side, content-side); the
		// content_position class only applies to coverflow / card-stack.
		$content_position = ( 'vertical' === $layout )
			? 'vertical'
			: ( ! empty( $settings['content_position'] ) ? $settings['content_position'] : 'below-active' );

		$wrapper_classes = array(
			'eap-widget',
			'eap-adv-slider',
			'eap-adv-slider--' . esc_attr( $layout ),
		);

		if ( 'vertical' !== $layout ) {
			$wrapper_classes[] = 'eap-adv-slider--content-' . esc_attr( $content_position );
		}

		if ( 'vertical' === $layout ) {
			$vertical_image_pos = ! empty( $settings['vertical_image_position'] ) ? $settings['vertical_image_position'] : 'left';
			$wrapper_classes[]  = 'eap-adv-slider--image-' . esc_attr( $vertical_image_pos );

			$arrows_pos        = ! empty( $settings['vertical_arrows_position'] ) ? $settings['vertical_arrows_position'] : 'split';
			$wrapper_classes[] = 'eap-adv-slider--arrows-' . esc_attr( $arrows_pos );
		}

		ob_start();
		if ( ! empty( $settings['prev_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['prev_icon'], array( 'aria-hidden' => 'true' ) );
		}
		$prev_icon_html = ob_get_clean();

		ob_start();
		if ( ! empty( $settings['next_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['next_icon'], array( 'aria-hidden' => 'true' ) );
		}
		$next_icon_html = ob_get_clean();
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-eap-asl-root>
			<div class="eap-adv-slider__shell">
				<?php if ( $slider_data['navigation'] ) : ?>
					<button type="button" class="eap-adv-slider__arrow eap-adv-slider__arrow--prev" aria-label="<?php esc_attr_e( 'Previous slide', 'elementor-animatepro' ); ?>">
						<?php echo $prev_icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				<?php endif; ?>

				<div class="eap-adv-slider__swiper swiper" data-eap-asl="<?php echo esc_attr( wp_json_encode( $slider_data ) ); ?>">
					<div class="swiper-wrapper">
						<?php foreach ( $items as $index => $item ) :
							$image     = ! empty( $item['image'] ) ? $item['image'] : array();
							$image_html = $this->get_image_html( $settings, $image );

							$title    = ! empty( $item['title'] ) ? $item['title'] : '';
							$subtitle = ! empty( $item['subtitle'] ) ? $item['subtitle'] : '';
							$desc     = ! empty( $item['description'] ) ? $item['description'] : '';
							$btn_text = ! empty( $item['button_text'] ) ? $item['button_text'] : '';
							$btn_link = ! empty( $item['button_link']['url'] ) ? $item['button_link'] : null;
							?>
							<div class="swiper-slide eap-adv-slider__slide">
								<div class="eap-adv-slider__card">
									<div class="eap-adv-slider__media">
										<?php if ( $image_html ) : ?>
											<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php endif; ?>
									</div>
									<?php if ( 'hidden' !== $content_position ) : ?>
										<div class="eap-adv-slider__content">
											<?php if ( $subtitle ) : ?>
												<div class="eap-adv-slider__subtitle"><?php echo esc_html( $subtitle ); ?></div>
											<?php endif; ?>
											<?php if ( $title ) : ?>
												<h3 class="eap-adv-slider__title"><?php echo esc_html( $title ); ?></h3>
											<?php endif; ?>
											<?php if ( $desc ) : ?>
												<div class="eap-adv-slider__description"><?php echo wp_kses_post( wpautop( $desc ) ); ?></div>
											<?php endif; ?>
											<?php if ( $btn_text ) :
												if ( $btn_link ) {
													$target   = ! empty( $btn_link['is_external'] ) ? ' target="_blank"' : '';
													$nofollow = ! empty( $btn_link['nofollow'] ) ? ' rel="nofollow"' : '';
													?>
													<a class="eap-adv-slider__button" href="<?php echo esc_url( $btn_link['url'] ); ?>"<?php echo $target . $nofollow; ?>>
														<span><?php echo esc_html( $btn_text ); ?></span>
													</a>
													<?php
												} else {
													?>
													<span class="eap-adv-slider__button">
														<span><?php echo esc_html( $btn_text ); ?></span>
													</span>
													<?php
												}
											endif; ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( $slider_data['navigation'] ) : ?>
					<button type="button" class="eap-adv-slider__arrow eap-adv-slider__arrow--next" aria-label="<?php esc_attr_e( 'Next slide', 'elementor-animatepro' ); ?>">
						<?php echo $next_icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				<?php endif; ?>
			</div>

			<?php if ( $slider_data['pagination'] ) : ?>
				<div class="eap-adv-slider__pagination"></div>
			<?php endif; ?>
		</div>
		<?php
	}
}
