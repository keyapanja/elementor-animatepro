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

class EAP_Widget_Team extends EAP_Widget_Base {

	private function get_layouts_with_socials() {
		return array( 'spotlight-strip', 'social-card', 'hover-social' );
	}

	private function get_layouts_with_bio() {
		return array( 'social-card', 'numbered-hover', 'hover-social' );
	}

	private function get_layouts_with_overlay_panel() {
		return array( 'spotlight-strip', 'hover-social' );
	}

	private function get_layouts_with_button_overlay() {
		return array( 'numbered-hover' );
	}

	public function get_name() {
		return 'eap-team';
	}

	public function get_title() {
		return __( 'Team', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'team' );
	}

	public function get_script_depends() {
		$deps = array_merge(
			$this->get_visibility_script_depends(),
			array( 'eap-team-script' )
		);

		if ( wp_script_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}

		return array_unique( $deps );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_animation_controls();
		$this->register_style_controls();
		$this->register_slider_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'display_mode',
			array(
				'label'   => __( 'Display', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'grid',
				'options' => array(
					'grid' => array(
						'title' => __( 'Grid', 'elementor-animatepro' ),
						'icon'  => 'eicon-gallery-grid',
					),
					'slider' => array(
						'title' => __( 'Slider', 'elementor-animatepro' ),
						'icon'  => 'eicon-slider-push',
					),
				),
				'toggle' => false,
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'social-card',
				'options' => array(
					'spotlight-strip' => __( 'Spotlight Strip', 'elementor-animatepro' ),
					'minimal-circle'  => __( 'Minimal Circle', 'elementor-animatepro' ),
					'social-card'     => __( 'Classic Social Card', 'elementor-animatepro' ),
					'numbered-hover'  => __( 'Numbered Hover', 'elementor-animatepro' ),
					'hover-social'    => __( 'Hover Social Overlay', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'      => __( 'Columns', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SELECT,
				'default'    => '4',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'condition'  => array(
					'display_mode' => 'grid',
				),
				'options'    => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-team__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
					'rem' => array(
						'min' => 0,
						'max' => 6,
						'step' => 0.1,
					),
				),
				'default'    => array(
					'size' => 28,
					'unit' => 'px',
				),
				'condition'  => array(
					'display_mode' => 'grid',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-team__grid' => 'gap: {{SIZE}}{{UNIT}};',
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
				'default'    => array(
					'size' => 360,
					'unit' => 'px',
				),
				'condition'  => array(
					'layout!' => 'minimal-circle',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-team-card' => '--eap-team-image-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'circle_size',
			array(
				'label'      => __( 'Circle Image Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 80,
						'max' => 320,
					),
				),
				'default'    => array(
					'size' => 140,
					'unit' => 'px',
				),
				'condition'  => array(
					'layout' => 'minimal-circle',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-team-card' => '--eap-team-circle-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'member_image_size',
				'default' => 'full',
			)
		);

		$this->add_responsive_control(
			'social_card_text_alignment',
			array(
				'label'     => __( 'Text Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left' => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
 				'condition' => array(
 					'layout' => 'social-card',
 				),
				'prefix_class' => 'eap-team-social-card-align-',
 				'selectors' => array(
					'{{WRAPPER}} .eap-team--layout-social-card' => '--eap-team-social-card-text-align: {{VALUE}};',
 				),
 			)
 		);

		$this->add_control(
			'card_link_icon',
			array(
				'label'   => __( 'Card Link Icon', 'elementor-animatepro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'layout' => array( 'numbered-hover' ),
				),
			)
		);

		$this->add_control(
			'default_overlay_text',
			array(
				'label'       => __( 'Default Hover Button Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'View', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array(
					'layout' => 'numbered-hover',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_settings',
			array(
				'label' => __( 'Slider Settings', 'elementor-animatepro' ),
				'condition' => array(
					'display_mode' => 'slider',
				),
			)
		);

		$this->add_responsive_control(
			'slides_per_view',
			array(
				'label' => __( 'Slides To Show', 'elementor-animatepro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
			)
		);

		$this->add_responsive_control(
			'slider_space_between',
			array(
				'label' => __( 'Space Between', 'elementor-animatepro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default' => array(
					'size' => 24,
					'unit' => 'px',
				),
			)
		);

		$this->add_control(
			'slider_loop',
			array(
				'label' => __( 'Loop', 'elementor-animatepro' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			)
		);

		$this->add_control(
			'slider_autoplay',
			array(
				'label' => __( 'Autoplay', 'elementor-animatepro' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
			)
		);

		$this->add_control(
			'slider_autoplay_delay',
			array(
				'label' => __( 'Autoplay Delay (ms)', 'elementor-animatepro' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 3500,
				'condition' => array(
					'slider_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'slider_pause_on_hover',
			array(
				'label' => __( 'Pause On Hover', 'elementor-animatepro' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => array(
					'slider_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'slider_allow_touch_move',
			array(
				'label' => __( 'Allow Touch Move', 'elementor-animatepro' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			)
		);

		$this->add_control(
			'slider_speed',
			array(
				'label' => __( 'Transition Speed (ms)', 'elementor-animatepro' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 650,
				'min' => 100,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_navigation',
			array(
				'label' => __( 'Navigation & Pagination', 'elementor-animatepro' ),
				'condition' => array(
					'display_mode' => 'slider',
				),
			)
		);

		$this->add_control(
			'slider_navigation',
			array(
				'label' => __( 'Arrows', 'elementor-animatepro' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			)
		);

		$this->add_control(
			'slider_prev_icon',
			array(
				'label' => __( 'Previous Icon', 'elementor-animatepro' ),
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'slider_navigation' => 'yes',
				),
			)
		);

		$this->add_control(
			'slider_next_icon',
			array(
				'label' => __( 'Next Icon', 'elementor-animatepro' ),
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'slider_navigation' => 'yes',
				),
			)
		);

		$this->add_control(
			'slider_pagination',
			array(
				'label' => __( 'Pagination', 'elementor-animatepro' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			)
		);

		$this->add_control(
			'slider_pagination_type',
			array(
				'label' => __( 'Pagination Type', 'elementor-animatepro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'bullets',
				'options' => array(
					'bullets' => __( 'Dots', 'elementor-animatepro' ),
					'fraction' => __( 'Fraction', 'elementor-animatepro' ),
					'progressbar' => __( 'Progress', 'elementor-animatepro' ),
				),
				'condition' => array(
					'slider_pagination' => 'yes',
				),
			)
		);

		$this->add_control(
			'slider_pagination_position',
			array(
				'label' => __( 'Pagination Position', 'elementor-animatepro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'outside',
				'options' => array(
					'inside' => __( 'Inside', 'elementor-animatepro' ),
					'outside' => __( 'Outside', 'elementor-animatepro' ),
				),
				'condition' => array(
					'slider_pagination' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_members',
			array(
				'label' => __( 'Members', 'elementor-animatepro' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'member_image',
			array(
				'label'   => __( 'Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'member_name',
			array(
				'label'       => __( 'Name', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Emma Carter', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'member_role',
			array(
				'label'       => __( 'Role', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Product Designer', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'member_bio',
			array(
				'label'       => __( 'Short Bio', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => __( 'Leads product storytelling, design systems, and thoughtful brand communication.', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'member_link',
			array(
				'label'         => __( 'Profile Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'placeholder'   => 'https://',
			)
		);

		$repeater->add_control(
			'member_hover_text',
			array(
				'label'       => __( 'Hover Button Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'View', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'member_social_heading',
			array(
				'label' => __( 'Social Links', 'elementor-animatepro' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		for ( $social_index = 1; $social_index <= 5; $social_index++ ) {
			$default_icon = array(
				'value'   => '',
				'library' => 'fa-brands',
			);
			$default_link = array();

			if ( 1 === $social_index ) {
				$default_icon = array(
					'value'   => 'fab fa-linkedin-in',
					'library' => 'fa-brands',
				);
				$default_link = array(
					'url'         => 'https://linkedin.com/',
					'is_external' => true,
				);
			}

			$repeater->add_control(
				'member_social_' . $social_index . '_icon',
				array(
					'label'   => sprintf( __( 'Link %d Icon', 'elementor-animatepro' ), $social_index ),
					'type'    => Controls_Manager::ICONS,
					'default' => $default_icon,
				)
			);

			$repeater->add_control(
				'member_social_' . $social_index . '_link',
				array(
					'label'         => sprintf( __( 'Link %d URL', 'elementor-animatepro' ), $social_index ),
					'type'          => Controls_Manager::URL,
					'show_external' => true,
					'placeholder'   => 'https://',
					'default'       => $default_link,
				)
			);
		}

		$this->add_control(
			'members',
			array(
				'label'       => __( 'Team Members', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'button_text' => __( 'Add Member', 'elementor-animatepro' ),
				'title_field' => '{{{ member_name }}}',
				'default'     => array(
					array(
						'member_name' => __( 'Emma', 'elementor-animatepro' ),
						'member_role' => __( 'Product Designer', 'elementor-animatepro' ),
						'member_social_1_icon' => array(
							'value'   => 'fab fa-linkedin-in',
							'library' => 'fa-brands',
						),
						'member_social_1_link' => array(
							'url'         => 'https://linkedin.com/',
							'is_external' => true,
						),
					),
					array(
						'member_name' => __( 'Henry', 'elementor-animatepro' ),
						'member_role' => __( 'Lead Developer', 'elementor-animatepro' ),
						'member_social_1_icon' => array(
							'value'   => 'fab fa-behance',
							'library' => 'fa-brands',
						),
						'member_social_1_link' => array(
							'url'         => 'https://behance.net/',
							'is_external' => true,
						),
					),
					array(
						'member_name' => __( 'John', 'elementor-animatepro' ),
						'member_role' => __( 'Marketing Specialist', 'elementor-animatepro' ),
						'member_social_1_icon' => array(
							'value'   => 'fab fa-x-twitter',
							'library' => 'fa-brands',
						),
						'member_social_1_link' => array(
							'url'         => 'https://x.com/',
							'is_external' => true,
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_slider_style_controls() {
		$this->start_controls_section(
			'section_slider_navigation_style',
			array(
				'label' => __( 'Slider Navigation', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'display_mode' => 'slider',
					'slider_navigation' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'slider_nav_icon_size',
			array(
				'label' => __( 'Icon Size', 'elementor-animatepro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 80,
					),
				),
				'default' => array(
					'size' => 20,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-arrow-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'slider_nav_box_size',
			array(
				'label' => __( 'Container Size', 'elementor-animatepro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 24,
						'max' => 120,
					),
				),
				'default' => array(
					'size' => 48,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-arrow-box-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'slider_nav_tabs' );

		$this->start_controls_tab(
			'slider_nav_tab_normal',
			array(
				'label' => __( 'Normal', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'slider_nav_color',
			array(
				'label' => __( 'Color', 'elementor-animatepro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-arrow-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'slider_nav_background',
			array(
				'label' => __( 'Background', 'elementor-animatepro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-arrow-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'slider_nav_border_color',
			array(
				'label' => __( 'Border Color', 'elementor-animatepro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-arrow-border: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'slider_nav_tab_hover',
			array(
				'label' => __( 'Hover', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'slider_nav_hover_color',
			array(
				'label' => __( 'Color', 'elementor-animatepro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-arrow-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'slider_nav_hover_background',
			array(
				'label' => __( 'Background', 'elementor-animatepro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-arrow-hover-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'slider_nav_hover_border_color',
			array(
				'label' => __( 'Border Color', 'elementor-animatepro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-arrow-hover-border: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'slider_nav_border',
				'selector' => '{{WRAPPER}} .eap-team__arrow',
			)
		);

		$this->add_responsive_control(
			'slider_nav_radius',
			array(
				'label' => __( 'Border Radius', 'elementor-animatepro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .eap-team__arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'slider_nav_offset_x',
			array(
				'label' => __( 'Horizontal Offset', 'elementor-animatepro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => -200,
						'max' => 200,
					),
				),
				'default' => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-arrow-offset-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'slider_nav_offset_y',
			array(
				'label' => __( 'Vertical Offset', 'elementor-animatepro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => -200,
						'max' => 200,
					),
				),
				'default' => array(
					'size' => 0,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-arrow-offset-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_pagination_style',
			array(
				'label' => __( 'Slider Pagination', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'display_mode' => 'slider',
					'slider_pagination' => 'yes',
				),
			)
		);

		$this->add_control(
			'slider_pagination_color',
			array(
				'label' => __( 'Color', 'elementor-animatepro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-pagination-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'slider_pagination_active_color',
			array(
				'label' => __( 'Active Color', 'elementor-animatepro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-pagination-active-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'slider_pagination_size',
			array(
				'label' => __( 'Size', 'elementor-animatepro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 4,
						'max' => 30,
					),
				),
				'default' => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-pagination-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'slider_pagination_gap',
			array(
				'label' => __( 'Gap', 'elementor-animatepro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default' => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-pagination-gap: {{SIZE}}{{UNIT}};',
				),
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

		$this->add_control(
			'animation_stagger',
			array(
				'label'     => __( 'Stagger (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 90,
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
				'name'     => 'card_background',
				'selector' => '{{WRAPPER}} .eap-team-card',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .eap-team-card',
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-team-card, {{WRAPPER}} .eap-team-card__media img, {{WRAPPER}} .eap-team-card__media, {{WRAPPER}} .eap-team-card__content-panel, {{WRAPPER}} .eap-team-card__overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Content Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-team-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_min_height',
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
					'{{WRAPPER}} .eap-team-card' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eap-team-card',
			)
		);

		$this->add_control(
			'card_hover_background',
			array(
				'label'     => __( 'Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'layout!' => 'minimal-circle',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-hover-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_hover_offset_y',
			array(
				'label'     => __( 'Hover Offset Y', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => -10,
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-hover-offset-y: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'card_hover_scale',
			array(
				'label'     => __( 'Hover Scale', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1.02,
				'step'      => 0.01,
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-hover-scale: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_hover_rotate',
			array(
				'label'     => __( 'Hover Rotate', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-hover-rotate: {{VALUE}}deg;',
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
			'image_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'condition'  => array(
					'layout!' => 'minimal-circle',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-team-card__media, {{WRAPPER}} .eap-team-card__media img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_grayscale',
			array(
				'label'      => __( 'Grayscale', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => '%',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-team-card' => '--eap-team-image-grayscale: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'image_hover_scale',
			array(
				'label'     => __( 'Image Hover Scale', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1.06,
				'step'      => 0.01,
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-image-hover-scale: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_overlay_style',
			array(
				'label' => __( 'Overlay', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => array_merge( $this->get_layouts_with_overlay_panel(), $this->get_layouts_with_button_overlay() ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'overlay_background',
				'selector' => '{{WRAPPER}} .eap-team-card__content-panel, {{WRAPPER}} .eap-team-card__overlay',
				'condition' => array(
					'layout' => $this->get_layouts_with_overlay_panel(),
				),
			)
		);

		$this->add_control(
			'overlay_opacity',
			array(
				'label'     => __( 'Overlay Opacity', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => array( '' ),
				'range'     => array(
					'' => array(
						'min' => 0,
						'max' => 1,
						'step' => 0.01,
					),
				),
				'default'   => array(
					'size' => 0.92,
				),
				'condition' => array(
					'layout' => array( 'hover-social' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-overlay-opacity: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => __( 'Overlay Button Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'layout' => $this->get_layouts_with_button_overlay(),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-button-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'     => __( 'Overlay Button Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'layout' => $this->get_layouts_with_button_overlay(),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-button-bg: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_size',
			array(
				'label'      => __( 'Overlay Button Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 32,
						'max' => 180,
					),
				),
				'default'    => array(
					'size' => 96,
					'unit' => 'px',
				),
				'condition'  => array(
					'layout' => $this->get_layouts_with_button_overlay(),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-button-size: {{SIZE}}{{UNIT}};',
				),
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
					'{{WRAPPER}} .eap-team-card__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .eap-team-card__name',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_role_style',
			array(
				'label' => __( 'Role', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'role_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-team-card__role' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'role_typography',
				'selector' => '{{WRAPPER}} .eap-team-card__role',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_bio_style',
			array(
				'label' => __( 'Bio / Description', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => $this->get_layouts_with_bio(),
				),
			)
		);

		$this->add_control(
			'bio_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-team-card__bio' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'bio_typography',
				'selector' => '{{WRAPPER}} .eap-team-card__bio',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_social_style',
			array(
				'label' => __( 'Social Icons', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => $this->get_layouts_with_socials(),
				),
			)
		);

		$this->add_responsive_control(
			'social_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 64,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-social-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'social_box_size',
			array(
				'label'      => __( 'Container Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 88,
					),
				),
				'default'    => array(
					'size' => 34,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-social-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'social_icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-social-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_icon_background',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-social-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_icon_border_color',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-social-border: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'social_icon_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-team-card__social' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hover_social_position',
			array(
				'label'     => __( 'Overlay Icons Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center-center',
				'options'   => array(
					'center-center' => __( 'Center Center', 'elementor-animatepro' ),
					'top-left'      => __( 'Top Left', 'elementor-animatepro' ),
					'top-right'     => __( 'Top Right', 'elementor-animatepro' ),
					'bottom-left'   => __( 'Bottom Left', 'elementor-animatepro' ),
					'bottom-right'  => __( 'Bottom Right', 'elementor-animatepro' ),
				),
				'condition' => array(
					'layout' => 'hover-social',
				),
			)
		);

		$this->add_control(
			'hover_social_direction',
			array(
				'label'     => __( 'Overlay Icons Direction', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'row',
				'options'   => array(
					'row' => array(
						'title' => __( 'Horizontal', 'elementor-animatepro' ),
						'icon'  => 'eicon-ellipsis-h',
					),
					'column' => array(
						'title' => __( 'Vertical', 'elementor-animatepro' ),
						'icon'  => 'eicon-ellipsis-v',
					),
				),
				'condition' => array(
					'layout' => 'hover-social',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-team' => '--eap-team-hover-social-direction: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_number_style',
			array(
				'label' => __( 'Number', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => array( 'numbered-hover' ),
				),
			)
		);

		$this->add_control(
			'number_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-team-card__number' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'number_typography',
				'selector' => '{{WRAPPER}} .eap-team-card__number',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$members  = ! empty( $settings['members'] ) && is_array( $settings['members'] ) ? $settings['members'] : array();

		if ( empty( $members ) ) {
			return;
		}

		$layout = ! empty( $settings['layout'] ) ? $settings['layout'] : 'social-card';
		$display_mode = ! empty( $settings['display_mode'] ) ? $settings['display_mode'] : 'grid';
		$wrapper_style = '';

		if ( 'hover-social' === $layout ) {
			$position = ! empty( $settings['hover_social_position'] ) ? $settings['hover_social_position'] : 'center-center';
			$map = array(
				'center-center' => array( 'center', 'center' ),
				'top-left'      => array( 'flex-start', 'flex-start' ),
				'top-right'     => array( 'flex-end', 'flex-start' ),
				'bottom-left'   => array( 'flex-start', 'flex-end' ),
				'bottom-right'  => array( 'flex-end', 'flex-end' ),
			);
			$justify = 'center';
			$align   = 'center';
			if ( isset( $map[ $position ] ) ) {
				$justify = $map[ $position ][0];
				$align   = $map[ $position ][1];
			}

			$wrapper_style = sprintf(
				'--eap-team-hover-social-justify:%1$s; --eap-team-hover-social-align:%2$s;',
				esc_attr( $justify ),
				esc_attr( $align )
			);
		}
		?>
		<div class="eap-widget eap-team eap-team--layout-<?php echo esc_attr( $layout ); ?> eap-team--display-<?php echo esc_attr( $display_mode ); ?><?php echo ( 'slider' === $display_mode && ! empty( $settings['slider_pagination'] ) ) ? ' eap-team--pagination-' . esc_attr( $settings['slider_pagination_position'] ) : ''; ?>"<?php echo $wrapper_style ? ' style="' . esc_attr( $wrapper_style ) . '"' : ''; ?>>
			<?php if ( 'slider' === $display_mode ) : ?>
				<?php $slider_settings = $this->get_slider_settings( $settings, $members ); ?>
				<div class="eap-team__shell">
					<?php if ( ! empty( $settings['slider_navigation'] ) ) : ?>
						<button type="button" class="eap-team__arrow eap-team__arrow--prev" aria-label="<?php echo esc_attr__( 'Previous member', 'elementor-animatepro' ); ?>">
							<?php Icons_Manager::render_icon( ! empty( $settings['slider_prev_icon'] ) ? $settings['slider_prev_icon'] : array(), array( 'aria-hidden' => 'true' ) ); ?>
						</button>
					<?php endif; ?>

					<div class="eap-team__swiper swiper" data-eap-team-slider="<?php echo esc_attr( wp_json_encode( $slider_settings ) ); ?>">
						<div class="swiper-wrapper">
							<?php foreach ( $members as $index => $member ) : ?>
								<div class="eap-team__slide swiper-slide">
									<?php $this->render_member( $member, $settings, $index ); ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<?php if ( ! empty( $settings['slider_navigation'] ) ) : ?>
						<button type="button" class="eap-team__arrow eap-team__arrow--next" aria-label="<?php echo esc_attr__( 'Next member', 'elementor-animatepro' ); ?>">
							<?php Icons_Manager::render_icon( ! empty( $settings['slider_next_icon'] ) ? $settings['slider_next_icon'] : array(), array( 'aria-hidden' => 'true' ) ); ?>
						</button>
					<?php endif; ?>

					<?php if ( ! empty( $settings['slider_pagination'] ) ) : ?>
						<div class="eap-team__pagination"></div>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div class="eap-team__grid">
					<?php foreach ( $members as $index => $member ) : ?>
						<?php $this->render_member( $member, $settings, $index ); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	private function get_slider_settings( $settings, $members ) {
		$slides_desktop = ! empty( $settings['slides_per_view'] ) ? absint( $settings['slides_per_view'] ) : 3;
		$slides_tablet  = ! empty( $settings['slides_per_view_tablet'] ) ? absint( $settings['slides_per_view_tablet'] ) : 2;
		$slides_mobile  = ! empty( $settings['slides_per_view_mobile'] ) ? absint( $settings['slides_per_view_mobile'] ) : 1;

		$space_between = 24;
		if ( ! empty( $settings['slider_space_between']['size'] ) ) {
			$space_between = (int) $settings['slider_space_between']['size'];
		}

		return array(
			'slidesDesktop'  => max( 1, $slides_desktop ),
			'slidesTablet'   => max( 1, $slides_tablet ),
			'slidesMobile'   => max( 1, $slides_mobile ),
			'spaceBetween'   => max( 0, $space_between ),
			'loop'           => ! empty( $settings['slider_loop'] ),
			'autoplay'       => ! empty( $settings['slider_autoplay'] ),
			'autoplayDelay'  => ! empty( $settings['slider_autoplay_delay'] ) ? absint( $settings['slider_autoplay_delay'] ) : 3500,
			'pauseOnHover'   => ! empty( $settings['slider_pause_on_hover'] ),
			'allowTouchMove' => ! empty( $settings['slider_allow_touch_move'] ),
			'speed'          => ! empty( $settings['slider_speed'] ) ? absint( $settings['slider_speed'] ) : 650,
			'navigation'     => ! empty( $settings['slider_navigation'] ),
			'pagination'     => ! empty( $settings['slider_pagination'] ),
			'paginationType' => ! empty( $settings['slider_pagination_type'] ) ? $settings['slider_pagination_type'] : 'bullets',
			'slideCount'     => count( $members ),
		);
	}

	private function render_member( $member, $settings, $index ) {
		$layout      = ! empty( $settings['layout'] ) ? $settings['layout'] : 'social-card';
		$name        = ! empty( $member['member_name'] ) ? $member['member_name'] : __( 'Team Member', 'elementor-animatepro' );
		$role        = ! empty( $member['member_role'] ) ? $member['member_role'] : '';
		$bio         = ! empty( $member['member_bio'] ) ? $member['member_bio'] : '';
		$link        = ! empty( $member['member_link']['url'] ) ? $member['member_link'] : array();
		$button_text = ! empty( $member['member_hover_text'] ) ? $member['member_hover_text'] : ( ! empty( $settings['default_overlay_text'] ) ? $settings['default_overlay_text'] : __( 'View', 'elementor-animatepro' ) );
		$image_html  = Group_Control_Image_Size::get_attachment_image_html( $member, 'member_image_size', 'member_image' );
		$socials     = $this->get_member_socials( $member );
		$motion      = $this->get_motion_attributes( $settings, $index );
		$number      = sprintf( '%02d', $index + 1 );

		$card_classes = array(
			'eap-team-card',
			'eap-motion',
			'eap-motion-' . $this->get_animation_slug( $settings ),
			'eap-team-card--' . $layout,
		);
		?>
		<article class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>" <?php echo $motion; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="eap-team-card__media">
				<?php if ( $image_html ) : ?>
					<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>

				<?php if ( 'numbered-hover' === $layout && $button_text ) : ?>
					<?php if ( ! empty( $link['url'] ) ) : ?>
						<a class="eap-team-card__button" data-eap-team-cursor-button href="<?php echo esc_url( $link['url'] ); ?>"<?php echo ! empty( $link['is_external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
							<span><?php echo esc_html( $button_text ); ?></span>
						</a>
					<?php else : ?>
						<span class="eap-team-card__button" data-eap-team-cursor-button>
							<span><?php echo esc_html( $button_text ); ?></span>
						</span>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( 'hover-social' === $layout && ! empty( $socials ) ) : ?>
					<div class="eap-team-card__overlay">
						<?php $this->render_social_links( $socials ); ?>
					</div>
				<?php endif; ?>

				<?php if ( 'spotlight-strip' === $layout ) : ?>
					<div class="eap-team-card__content-panel">
						<div class="eap-team-card__topline">
							<div class="eap-team-card__text">
								<h3 class="eap-team-card__name"><?php echo esc_html( $name ); ?></h3>
								<?php if ( $role ) : ?>
									<div class="eap-team-card__role"><?php echo esc_html( $role ); ?></div>
								<?php endif; ?>
							</div>
							<?php if ( ! empty( $socials ) ) : ?>
								<?php $this->render_social_links( $socials ); ?>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( 'spotlight-strip' !== $layout ) : ?>
				<div class="eap-team-card__content">
					<?php if ( in_array( $layout, array( 'numbered-hover' ), true ) ) : ?>
						<div class="eap-team-card__topline">
							<div class="eap-team-card__text">
								<h3 class="eap-team-card__name"><?php echo esc_html( $name ); ?></h3>
								<?php if ( $role ) : ?>
									<div class="eap-team-card__role"><?php echo esc_html( $role ); ?></div>
								<?php endif; ?>
							</div>
							<?php if ( 'numbered-hover' === $layout ) : ?>
								<span class="eap-team-card__number"><?php echo esc_html( $number ); ?></span>
							<?php elseif ( ! empty( $link['url'] ) && ! empty( $settings['card_link_icon']['value'] ) ) : ?>
								<a class="eap-team-card__single-link" href="<?php echo esc_url( $link['url'] ); ?>"<?php echo ! empty( $link['is_external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
									<?php Icons_Manager::render_icon( $settings['card_link_icon'], array( 'aria-hidden' => 'true' ) ); ?>
								</a>
							<?php endif; ?>
						</div>
					<?php else : ?>
						<div class="eap-team-card__text">
							<h3 class="eap-team-card__name"><?php echo esc_html( $name ); ?></h3>
							<?php if ( $role ) : ?>
								<div class="eap-team-card__role"><?php echo esc_html( $role ); ?></div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $bio && ! in_array( $layout, array( 'minimal-circle' ), true ) ) : ?>
						<div class="eap-team-card__bio"><?php echo esc_html( $bio ); ?></div>
					<?php endif; ?>

					<?php if ( in_array( $layout, array( 'social-card' ), true ) && ! empty( $socials ) ) : ?>
						<?php $this->render_social_links( $socials ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</article>
		<?php
	}

	private function render_social_links( $socials ) {
		if ( empty( $socials ) ) {
			return;
		}
		?>
		<div class="eap-team-card__socials">
			<?php foreach ( $socials as $social ) : ?>
				<?php
				$url = ! empty( $social['social_link']['url'] ) ? $social['social_link']['url'] : '';
				$icon = ! empty( $social['social_icon']['value'] ) ? $social['social_icon'] : array();

				if ( ! $url || empty( $icon ) ) {
					continue;
				}
				?>
				<a class="eap-team-card__social" href="<?php echo esc_url( $url ); ?>"<?php echo ! empty( $social['social_link']['is_external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
					<?php Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) ); ?>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}

	private function get_member_socials( $member ) {
		$socials = array();

		for ( $social_index = 1; $social_index <= 5; $social_index++ ) {
			$icon_key = 'member_social_' . $social_index . '_icon';
			$link_key = 'member_social_' . $social_index . '_link';

			if ( empty( $member[ $link_key ]['url'] ) || empty( $member[ $icon_key ]['value'] ) ) {
				continue;
			}

			$socials[] = array(
				'social_icon' => $member[ $icon_key ],
				'social_link' => $member[ $link_key ],
			);
		}

		return $socials;
	}

	private function get_animation_slug( $settings ) {
		$animation = ! empty( $settings['starter_animation'] ) ? $settings['starter_animation'] : 'fade-up';
		return sanitize_html_class( 'none' === $animation ? 'fade-up' : $animation );
	}

	private function get_motion_attributes( $settings, $index ) {
		$animation = ! empty( $settings['starter_animation'] ) ? $settings['starter_animation'] : 'fade-up';

		if ( 'none' === $animation ) {
			return 'data-eap-no-motion="true"';
		}

		$duration = ! empty( $settings['animation_duration'] ) ? absint( $settings['animation_duration'] ) : 850;
		$delay    = ! empty( $settings['animation_delay'] ) ? absint( $settings['animation_delay'] ) : 0;
		$stagger  = ! empty( $settings['animation_stagger'] ) ? absint( $settings['animation_stagger'] ) : 90;
		$total_delay = $delay + ( $index * $stagger );

		return sprintf(
			'data-eap-duration="%1$d" data-eap-delay="%2$d"',
			$duration,
			$total_delay
		);
	}
}
