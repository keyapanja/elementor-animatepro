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

class EAP_Widget_Services_Tabs extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-services-tabs';
	}

	public function get_title() {
		return __( 'Services Tabs', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_keywords() {
		return array( 'services', 'tabs', 'feature', 'showcase', 'eap', 'animatepro' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'services-tabs' );
	}

	public function get_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-services-tabs-script',
		);
	}

	/* ------------------------------------------------------------------ *
	 * Controls
	 * ------------------------------------------------------------------ */

	protected function register_controls() {
		$this->register_layout_controls();
		$this->register_items_controls();
		$this->register_behavior_controls();

		$this->register_wrapper_style_controls();
		$this->register_tabs_style_controls();
		$this->register_indicator_style_controls();
		$this->register_number_style_controls();
		$this->register_title_style_controls();
		$this->register_subtitle_style_controls();
		$this->register_description_style_controls();
		$this->register_button_style_controls();
		$this->register_media_style_controls();
	}

	private function register_layout_controls() {
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
				'default' => 'bold-list',
				'options' => array(
					'bold-list'   => __( 'Bold List (Big Titles)', 'elementor-animatepro' ),
					'rule-stack'  => __( 'Rule Stack (Side Line)', 'elementor-animatepro' ),
					'numbered'    => __( 'Numbered List', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'image_position',
			array(
				'label'   => __( 'Image Position', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'  => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default' => 'right',
				'toggle'  => false,
			)
		);

		$this->add_control(
			'show_numbers',
			array(
				'label'        => __( 'Show Numbers', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'condition'    => array( 'layout!' => 'rule-stack' ),
			)
		);

		$this->add_control(
			'reveal_description',
			array(
				'label'        => __( 'Reveal Description on Active Only', 'elementor-animatepro' ),
				'description'  => __( 'When ON, description only appears for the active tab. Useful for the Bold List look.', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'condition'    => array( 'layout' => 'bold-list' ),
			)
		);

		$this->add_control(
			'show_button',
			array(
				'label'   => __( 'Show CTA Button', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition'    => array( 'layout' => 'bold-list' ),
			)
		);

		$this->add_responsive_control(
			'tabs_width',
			array(
				'label'      => __( 'Tabs Column Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array( 'min' => 25, 'max' => 75 ),
					'px' => array( 'min' => 200, 'max' => 1000 ),
				),
				'default'    => array( 'unit' => '%', 'size' => 50 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs__list' => 'flex: 0 0 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'media_gap',
			array(
				'label'      => __( 'Gap Between Columns', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 60 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_items_controls() {
		$this->start_controls_section(
			'section_items',
			array(
				'label' => __( 'Items', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Service Title', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'item_subtitle',
			array(
				'label'   => __( 'Subtitle', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$repeater->add_control(
			'item_description',
			array(
				'label'   => __( 'Description', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Short description for this service item.', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'item_image',
			array(
				'label'   => __( 'Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$repeater->add_control(
			'item_button_text',
			array(
				'label'   => __( 'Button Text', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Know More', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'item_button_link',
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
				'label'       => __( 'Items', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ item_title }}}',
				'default'     => array(
					array(
						'item_title'       => __( 'Interior Design', 'elementor-animatepro' ),
						'item_description' => __( 'Creating thoughtful, functional and beautiful spaces tailored to your style.', 'elementor-animatepro' ),
						'item_button_text' => __( 'Know More', 'elementor-animatepro' ),
					),
					array(
						'item_title'       => __( 'Planning', 'elementor-animatepro' ),
						'item_description' => __( 'Layouts, flow and zoning crafted to make every square foot count.', 'elementor-animatepro' ),
						'item_button_text' => __( 'Know More', 'elementor-animatepro' ),
					),
					array(
						'item_title'       => __( 'Visualization', 'elementor-animatepro' ),
						'item_description' => __( 'See your space before it\'s built with photorealistic 3D renders.', 'elementor-animatepro' ),
						'item_button_text' => __( 'Know More', 'elementor-animatepro' ),
					),
					array(
						'item_title'       => __( 'Lighting', 'elementor-animatepro' ),
						'item_description' => __( 'Layered lighting plans that bring atmosphere to every room.', 'elementor-animatepro' ),
						'item_button_text' => __( 'Know More', 'elementor-animatepro' ),
					),
					array(
						'item_title'       => __( 'Decoration', 'elementor-animatepro' ),
						'item_description' => __( 'Decoration is the act of adding ornamental elements to a space or an object to enhance its visual appeal.', 'elementor-animatepro' ),
						'item_button_text' => __( 'Know More', 'elementor-animatepro' ),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'item_image_size',
				'default' => 'large',
			)
		);

		$this->add_control(
			'default_active_index',
			array(
				'label'       => __( 'Default Active Index', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'default'     => 1,
				'description' => __( 'Which item starts active (1-based).', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	private function register_behavior_controls() {
		$this->start_controls_section(
			'section_behavior',
			array(
				'label' => __( 'Behavior', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'trigger',
			array(
				'label'   => __( 'Switch Trigger', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => array(
					'hover' => __( 'On Hover', 'elementor-animatepro' ),
					'click' => __( 'On Click', 'elementor-animatepro' ),
					'auto'  => __( 'Auto (Cycle)', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'       => __( 'Auto Cycle Delay (ms)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 800,
				'max'         => 15000,
				'step'        => 100,
				'default'     => 3500,
				'condition'   => array( 'trigger' => 'auto' ),
			)
		);

		$this->add_control(
			'autoplay_pause_on_hover',
			array(
				'label'     => __( 'Pause on Hover', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array( 'trigger' => 'auto' ),
			)
		);

		$this->add_control(
			'transition_duration',
			array(
				'label'      => __( 'Transition Duration (ms)', 'elementor-animatepro' ),
				'type'       => Controls_Manager::NUMBER,
				'min'        => 100,
				'max'        => 2500,
				'step'       => 50,
				'default'    => 500,
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs' => '--eap-st-transition: {{VALUE}}ms;',
				),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------ *
	 * Style controls
	 * ------------------------------------------------------------------ */

	private function register_wrapper_style_controls() {
		$this->start_controls_section(
			'section_wrapper_style',
			array(
				'label' => __( 'Wrapper', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'wrapper_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'wrapper_border',
				'selector' => '{{WRAPPER}} .eap-services-tabs',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'wrapper_shadow',
				'selector' => '{{WRAPPER}} .eap-services-tabs',
			)
		);

		$this->add_responsive_control(
			'wrapper_align',
			array(
				'label'   => __( 'Vertical Align', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Top', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-bottom',
					),
					'stretch'    => array(
						'title' => __( 'Stretch', 'elementor-animatepro' ),
						'icon'  => 'eicon-grow',
					),
				),
				'default'   => 'stretch',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_tabs_style_controls() {
		$this->start_controls_section(
			'section_tabs_style',
			array(
				'label' => __( 'Tab Item', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => __( 'Gap Between Items', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 28 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs__list' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => __( 'Item Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_bg_normal',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_bg_active',
			array(
				'label'     => __( 'Active Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__item.is-active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .eap-services-tabs__item',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .eap-services-tabs__item',
			)
		);

		$this->end_controls_section();
	}

	private function register_indicator_style_controls() {
		$this->start_controls_section(
			'section_indicator_style',
			array(
				'label'     => __( 'Side Indicator (Rule Stack)', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'rule-stack' ),
			)
		);

		$this->add_responsive_control(
			'indicator_thickness',
			array(
				'label'      => __( 'Thickness', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 1, 'max' => 12 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 2 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs--rule-stack .eap-services-tabs__item::before' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'indicator_offset',
			array(
				'label'      => __( 'Offset From Content', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 24 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs--rule-stack .eap-services-tabs__item' => 'padding-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'indicator_track_color',
			array(
				'label'     => __( 'Track Color (Inactive)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.18)',
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs--rule-stack .eap-services-tabs__item::before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'indicator_active_color',
			array(
				'label'     => __( 'Active Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs--rule-stack .eap-services-tabs__item.is-active::before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_number_style_controls() {
		$this->start_controls_section(
			'section_number_style',
			array(
				'label'     => __( 'Number', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_numbers' => 'yes',
					'layout!'      => 'rule-stack',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'number_typography',
				'selector' => '{{WRAPPER}} .eap-services-tabs__number',
			)
		);

		$this->add_control(
			'number_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.45)',
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__number' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'number_active_color',
			array(
				'label'     => __( 'Active Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__item.is-active .eap-services-tabs__number' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'number_gap',
			array(
				'label'      => __( 'Gap After Number', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 22 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs__item' => '--eap-st-number-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_title_style_controls() {
		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => __( 'Title', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-services-tabs__title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.45)',
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_active_color',
			array(
				'label'     => __( 'Active Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__item.is-active .eap-services-tabs__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_hover_color',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__item:hover .eap-services-tabs__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_subtitle_style_controls() {
		$this->start_controls_section(
			'section_subtitle_style',
			array(
				'label' => __( 'Subtitle', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .eap-services-tabs__subtitle',
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.55)',
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'subtitle_active_color',
			array(
				'label'     => __( 'Active Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__item.is-active .eap-services-tabs__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'subtitle_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs__subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_description_style_controls() {
		$this->start_controls_section(
			'section_description_style',
			array(
				'label' => __( 'Description', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .eap-services-tabs__description',
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.7)',
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'description_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs__description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'description_max_width',
			array(
				'label'      => __( 'Max Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 200, 'max' => 900 ),
					'%'  => array( 'min' => 25, 'max' => 100 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs__description' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_button_style_controls() {
		$this->start_controls_section(
			'section_button_style',
			array(
				'label'     => __( 'Button', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout'      => 'bold-list',
					'show_button' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .eap-services-tabs__button',
			)
		);

		$this->start_controls_tabs( 'button_states' );

		$this->start_controls_tab(
			'button_state_normal',
			array( 'label' => __( 'Normal', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_border_color',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__button' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_state_hover',
			array( 'label' => __( 'Hover', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'button_hover_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_hover_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__button:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_hover_border',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__button:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 18,
					'right'    => 36,
					'bottom'   => 18,
					'left'     => 36,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_radius',
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
					'{{WRAPPER}} .eap-services-tabs__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_border_width',
			array(
				'label'      => __( 'Border Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 8 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 1 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs__button' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
				),
			)
		);

		$this->add_responsive_control(
			'button_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 24,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs__button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_media_style_controls() {
		$this->start_controls_section(
			'section_media_style',
			array(
				'label' => __( 'Image', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'media_match_list_height',
			array(
				'label'        => __( 'Match Tab List Height', 'elementor-animatepro' ),
				'description'  => __( 'Image stretches to equal the full height of the tab list column (top of first tab to bottom of last tab).', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
			)
		);

		$this->add_responsive_control(
			'media_height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 200, 'max' => 1200 ),
					'vh' => array( 'min' => 30, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 560 ),
				'condition'  => array( 'media_match_list_height!' => 'yes' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs__media' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'media_object_fit',
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
					'{{WRAPPER}} .eap-services-tabs__image img' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'media_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-services-tabs__media' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'media_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 12,
					'right'    => 12,
					'bottom'   => 12,
					'left'     => 12,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-services-tabs__media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'media_border',
				'selector' => '{{WRAPPER}} .eap-services-tabs__media',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'media_shadow',
				'selector' => '{{WRAPPER}} .eap-services-tabs__media',
			)
		);

		$this->add_control(
			'media_zoom_active',
			array(
				'label'        => __( 'Subtle Zoom on Active', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'description'  => __( 'Active image scales slightly from 1.04 to 1.0 for a gentle reveal.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------ *
	 * Render
	 * ------------------------------------------------------------------ */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();

		if ( empty( $items ) ) {
			return;
		}

		$layout         = ! empty( $settings['layout'] ) ? $settings['layout'] : 'bold-list';
		$image_position = ! empty( $settings['image_position'] ) ? $settings['image_position'] : 'right';
		$show_numbers   = 'rule-stack' !== $layout && ! empty( $settings['show_numbers'] ) && 'yes' === $settings['show_numbers'];
		$reveal_desc    = 'bold-list' === $layout && ! empty( $settings['reveal_description'] ) && 'yes' === $settings['reveal_description'];
		$show_button    = 'bold-list' === $layout && ! empty( $settings['show_button'] ) && 'yes' === $settings['show_button'];
		$trigger        = ! empty( $settings['trigger'] ) ? $settings['trigger'] : 'hover';
		$autoplay_delay = isset( $settings['autoplay_delay'] ) ? (int) $settings['autoplay_delay'] : 3500;
		$pause_on_hover = ! empty( $settings['autoplay_pause_on_hover'] ) && 'yes' === $settings['autoplay_pause_on_hover'];
		$active_index   = isset( $settings['default_active_index'] ) ? max( 0, (int) $settings['default_active_index'] - 1 ) : 0;
		$active_index   = min( $active_index, count( $items ) - 1 );
		$zoom_on_active = ! empty( $settings['media_zoom_active'] ) && 'yes' === $settings['media_zoom_active'];
		$match_height   = ! empty( $settings['media_match_list_height'] ) && 'yes' === $settings['media_match_list_height'];

		$wrapper_classes = array(
			'eap-services-tabs',
			'eap-services-tabs--' . esc_attr( $layout ),
			'eap-services-tabs--image-' . esc_attr( $image_position ),
		);
		if ( $reveal_desc ) {
			$wrapper_classes[] = 'eap-services-tabs--reveal-desc';
		}
		if ( $zoom_on_active ) {
			$wrapper_classes[] = 'eap-services-tabs--media-zoom';
		}
		if ( $match_height ) {
			$wrapper_classes[] = 'eap-services-tabs--media-stretch';
		}

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'                 => $wrapper_classes,
				'data-trigger'          => esc_attr( $trigger ),
				'data-autoplay-delay'   => (string) $autoplay_delay,
				'data-pause-on-hover'   => $pause_on_hover ? '1' : '0',
				'data-default-active'   => (string) $active_index,
				'role'                  => 'tablist',
			)
		);

		$widget_id = $this->get_id();
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div class="eap-services-tabs__list">
				<?php foreach ( $items as $index => $item ) :
					$is_active = ( $index === $active_index );
					$item_classes = array( 'eap-services-tabs__item' );
					if ( $is_active ) {
						$item_classes[] = 'is-active';
					}
					$tab_id   = sprintf( 'eap-st-tab-%s-%d', $widget_id, $index );
					$panel_id = sprintf( 'eap-st-panel-%s-%d', $widget_id, $index );
					?>
					<button
						type="button"
						class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>"
						data-index="<?php echo (int) $index; ?>"
						id="<?php echo esc_attr( $tab_id ); ?>"
						role="tab"
						aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
						aria-controls="<?php echo esc_attr( $panel_id ); ?>"
						tabindex="<?php echo $is_active ? '0' : '-1'; ?>"
					>
						<?php if ( $show_numbers ) : ?>
							<span class="eap-services-tabs__number" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
						<?php endif; ?>
						<span class="eap-services-tabs__content">
							<?php if ( ! empty( $item['item_title'] ) ) : ?>
								<span class="eap-services-tabs__title"><?php echo esc_html( $item['item_title'] ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $item['item_subtitle'] ) ) : ?>
								<span class="eap-services-tabs__subtitle"><?php echo esc_html( $item['item_subtitle'] ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $item['item_description'] ) ) : ?>
								<span class="eap-services-tabs__description"><?php echo wp_kses_post( $item['item_description'] ); ?></span>
							<?php endif; ?>
							<?php if ( $show_button && ! empty( $item['item_button_text'] ) ) :
								$link = ! empty( $item['item_button_link']['url'] ) ? $item['item_button_link'] : null;
								if ( $link ) {
									$target   = ! empty( $link['is_external'] ) ? ' target="_blank"' : '';
									$nofollow = ! empty( $link['nofollow'] ) ? ' rel="nofollow"' : '';
									?>
									<a class="eap-services-tabs__button" href="<?php echo esc_url( $link['url'] ); ?>"<?php echo $target . $nofollow; ?> tabindex="-1">
										<span><?php echo esc_html( $item['item_button_text'] ); ?></span>
									</a>
									<?php
								} else {
									?>
									<span class="eap-services-tabs__button">
										<span><?php echo esc_html( $item['item_button_text'] ); ?></span>
									</span>
									<?php
								}
							endif; ?>
						</span>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="eap-services-tabs__media" role="presentation">
				<?php foreach ( $items as $index => $item ) :
					$is_active = ( $index === $active_index );
					$panel_id  = sprintf( 'eap-st-panel-%s-%d', $widget_id, $index );
					$tab_id    = sprintf( 'eap-st-tab-%s-%d', $widget_id, $index );

					$image_settings = array_merge(
						$settings,
						array( 'item_image' => ! empty( $item['item_image'] ) ? $item['item_image'] : array() )
					);
					$image_html = '';
					if ( ! empty( $item['item_image']['url'] ) || ! empty( $item['item_image']['id'] ) ) {
						$image_html = Group_Control_Image_Size::get_attachment_image_html(
							$image_settings,
							'item_image_size',
							'item_image'
						);
						if ( empty( $image_html ) ) {
							$image_html = '<img src="' . esc_url( $item['item_image']['url'] ) . '" alt="" />';
						}
					}
					?>
					<div
						class="eap-services-tabs__image<?php echo $is_active ? ' is-active' : ''; ?>"
						data-index="<?php echo (int) $index; ?>"
						id="<?php echo esc_attr( $panel_id ); ?>"
						role="tabpanel"
						aria-labelledby="<?php echo esc_attr( $tab_id ); ?>"
						aria-hidden="<?php echo $is_active ? 'false' : 'true'; ?>"
					>
						<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
