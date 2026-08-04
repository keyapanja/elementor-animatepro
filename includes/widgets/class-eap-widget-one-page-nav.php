<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;

class EAP_Widget_One_Page_Nav extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-one-page-nav';
	}

	public function get_title() {
		return __( 'One Page Nav', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_keywords() {
		return array( 'one', 'page', 'nav', 'menu', 'sidebar', 'sticky', 'dot', 'navigation', 'eap', 'animatepro' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'one-page-nav' );
	}

	public function get_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-one-page-nav-script',
		);
	}

	/* ------------------------------------------------------------------ *
	 * Controls
	 * ------------------------------------------------------------------ */

	protected function register_controls() {
		$this->register_navigation_controls();
		$this->register_settings_controls();

		$this->register_wrapper_style_controls();
		$this->register_item_style_controls();
		$this->register_tooltip_style_controls();
	}

	private function register_navigation_controls() {
		$this->start_controls_section(
			'section_navigation',
			array(
				'label' => __( 'Navigation', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_label',
			array(
				'label'       => __( 'Label', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Home', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'item_target',
			array(
				'label'       => __( 'Section ID / Selector', 'elementor-animatepro' ),
				'description' => __( 'CSS selector of the target section (e.g. <code>#home</code> or <code>.section-home</code>). Use the section\'s CSS ID set in its Advanced tab.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '#home',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'item_icon',
			array(
				'label' => __( 'Icon', 'elementor-animatepro' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'One Page Nav', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ item_label }}}',
				'default'     => array(
					array(
						'item_label'  => __( 'Home', 'elementor-animatepro' ),
						'item_target' => '#home',
					),
					array(
						'item_label'  => __( 'About', 'elementor-animatepro' ),
						'item_target' => '#about',
					),
					array(
						'item_label'  => __( 'FAQ', 'elementor-animatepro' ),
						'item_target' => '#faq',
					),
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_settings_controls() {
		$this->start_controls_section(
			'section_settings',
			array(
				'label' => __( 'Settings', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'sticky_mode',
			array(
				'label'       => __( 'Sticky Scope', 'elementor-animatepro' ),
				'description' => __( '<strong>Whole Page</strong> sticks to the viewport across the entire page. <strong>Section Only</strong> sticks inside a specific section and only watches sections inside that container.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'page',
				'options'     => array(
					'page'    => __( 'Whole Page', 'elementor-animatepro' ),
					'section' => __( 'Section Only', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'sticky_container',
			array(
				'label'       => __( 'Section Container Selector', 'elementor-animatepro' ),
				'description' => __( 'CSS selector of the parent section the nav should stick inside. e.g. <code>#features</code> or <code>.eap-services-section</code>.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'condition'   => array( 'sticky_mode' => 'section' ),
			)
		);

		$this->add_control(
			'attach_edge',
			array(
				'label'   => __( 'Attach to Edge', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'top'    => array(
						'title' => __( 'Top', 'elementor-animatepro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'right'  => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-arrow-right',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'elementor-animatepro' ),
						'icon'  => 'eicon-arrow-down',
					),
					'left'   => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-arrow-left',
					),
				),
				'default' => 'right',
				'toggle'  => false,
			)
		);

		$this->add_control(
			'align_horizontal',
			array(
				'label'   => __( 'Horizontal Alignment', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'condition' => array( 'attach_edge' => array( 'top', 'bottom' ) ),
			)
		);

		$this->add_control(
			'align_vertical',
			array(
				'label'   => __( 'Vertical Alignment', 'elementor-animatepro' ),
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
				),
				'default'   => 'center',
				'toggle'    => false,
				'condition' => array( 'attach_edge' => array( 'left', 'right' ) ),
			)
		);

		$this->add_responsive_control(
			'edge_offset',
			array(
				'label'      => __( 'Offset From Edge', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 400 ),
					'%'  => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 32 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-onepage-nav' => '--eap-opn-edge-offset: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'show_label',
			array(
				'label'   => __( 'Show Labels', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_icon',
			array(
				'label'   => __( 'Show Icons', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'hide_label_show_tooltip',
			array(
				'label'        => __( 'Hide Labels, Show Tooltip on Hover', 'elementor-animatepro' ),
				'description'  => __( 'Replaces inline labels with a hover tooltip — useful when only icons are visible.', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'condition'    => array( 'show_label' => 'yes' ),
			)
		);

		$this->add_control(
			'tooltip_position',
			array(
				'label'     => __( 'Tooltip Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'auto',
				'options'   => array(
					'auto'   => __( 'Auto (Opposite Edge)', 'elementor-animatepro' ),
					'top'    => __( 'Top', 'elementor-animatepro' ),
					'right'  => __( 'Right', 'elementor-animatepro' ),
					'bottom' => __( 'Bottom', 'elementor-animatepro' ),
					'left'   => __( 'Left', 'elementor-animatepro' ),
				),
				'condition' => array( 'hide_label_show_tooltip' => 'yes' ),
			)
		);

		$this->add_control(
			'smooth_scroll',
			array(
				'label'   => __( 'Smooth Scroll on Click', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'scroll_offset',
			array(
				'label'       => __( 'Scroll Offset (px)', 'elementor-animatepro' ),
				'description' => __( 'Pixels to subtract when scrolling to a target — useful when a fixed header is in the way.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 400,
				'default'     => 80,
			)
		);

		$this->add_control(
			'active_offset',
			array(
				'label'       => __( 'Active Detection Offset (% of viewport)', 'elementor-animatepro' ),
				'description' => __( 'How far down the viewport a section\'s top must reach before it becomes active. Lower = activates sooner.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 5,
				'max'         => 80,
				'default'     => 35,
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
				'label' => __( 'Navigation Wrapper', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'wrapper_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 12,
					'right'    => 12,
					'bottom'   => 12,
					'left'     => 12,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-onepage-nav__list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_gap',
			array(
				'label'      => __( 'Gap Between Items', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 6 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-onepage-nav__list' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'wrapper_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15, 18, 22, 0.92)',
				'selectors' => array(
					'{{WRAPPER}} .eap-onepage-nav__list' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 14,
					'right'    => 14,
					'bottom'   => 14,
					'left'     => 14,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-onepage-nav__list' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'wrapper_border',
				'selector' => '{{WRAPPER}} .eap-onepage-nav__list',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'wrapper_shadow',
				'selector' => '{{WRAPPER}} .eap-onepage-nav__list',
			)
		);

		$this->end_controls_section();
	}

	private function register_item_style_controls() {
		$this->start_controls_section(
			'section_item_style',
			array(
				'label' => __( 'Navigation Item', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_typography',
				'selector' => '{{WRAPPER}} .eap-onepage-nav__link',
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 10,
					'right'    => 14,
					'bottom'   => 10,
					'left'     => 14,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-onepage-nav__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 48 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 16 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-onepage-nav__icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-onepage-nav__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_icon_gap',
			array(
				'label'      => __( 'Icon Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 10 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-onepage-nav__link' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'item_states' );

		$this->start_controls_tab(
			'item_state_normal',
			array( 'label' => __( 'Normal', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'item_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-onepage-nav__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => array(
					'{{WRAPPER}} .eap-onepage-nav__link' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'item_state_hover',
			array( 'label' => __( 'Hover', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'item_hover_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-onepage-nav__link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_hover_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.08)',
				'selectors' => array(
					'{{WRAPPER}} .eap-onepage-nav__link:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'item_state_active',
			array( 'label' => __( 'Active', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'item_active_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff5722',
				'selectors' => array(
					'{{WRAPPER}} .eap-onepage-nav__item.is-active .eap-onepage-nav__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_active_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => array(
					'{{WRAPPER}} .eap-onepage-nav__item.is-active .eap-onepage-nav__link' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'item_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 8,
					'right'    => 8,
					'bottom'   => 8,
					'left'     => 8,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-onepage-nav__link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .eap-onepage-nav__link',
			)
		);

		$this->end_controls_section();
	}

	private function register_tooltip_style_controls() {
		$this->start_controls_section(
			'section_tooltip_style',
			array(
				'label'     => __( 'Tooltip', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'hide_label_show_tooltip' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tooltip_typography',
				'selector' => '{{WRAPPER}} .eap-onepage-nav__tooltip',
			)
		);

		$this->add_control(
			'tooltip_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-onepage-nav__tooltip' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tooltip_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111418',
				'selectors' => array(
					'{{WRAPPER}} .eap-onepage-nav__tooltip' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'tooltip_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 6,
					'right'    => 10,
					'bottom'   => 6,
					'left'     => 10,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-onepage-nav__tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tooltip_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 6,
					'right'    => 6,
					'bottom'   => 6,
					'left'     => 6,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-onepage-nav__tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tooltip_offset',
			array(
				'label'      => __( 'Offset From Item', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 14 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-onepage-nav' => '--eap-opn-tooltip-offset: {{SIZE}}{{UNIT}};',
				),
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

		$sticky_mode     = ! empty( $settings['sticky_mode'] ) ? $settings['sticky_mode'] : 'page';
		$sticky_container = isset( $settings['sticky_container'] ) ? trim( (string) $settings['sticky_container'] ) : '';
		$attach_edge     = ! empty( $settings['attach_edge'] ) ? $settings['attach_edge'] : 'right';
		$show_label      = ! empty( $settings['show_label'] ) && 'yes' === $settings['show_label'];
		$show_icon       = ! empty( $settings['show_icon'] ) && 'yes' === $settings['show_icon'];
		$use_tooltip     = $show_label && ! empty( $settings['hide_label_show_tooltip'] ) && 'yes' === $settings['hide_label_show_tooltip'];
		$tooltip_pos     = ! empty( $settings['tooltip_position'] ) ? $settings['tooltip_position'] : 'auto';
		$smooth_scroll   = ! empty( $settings['smooth_scroll'] ) && 'yes' === $settings['smooth_scroll'];
		$scroll_offset   = isset( $settings['scroll_offset'] ) ? (int) $settings['scroll_offset'] : 80;
		$active_offset   = isset( $settings['active_offset'] ) ? max( 5, min( 80, (int) $settings['active_offset'] ) ) : 35;
		$align_h         = ! empty( $settings['align_horizontal'] ) ? $settings['align_horizontal'] : 'center';
		$align_v         = ! empty( $settings['align_vertical'] ) ? $settings['align_vertical'] : 'center';

		$is_horizontal = in_array( $attach_edge, array( 'top', 'bottom' ), true );

		// Pick the alignment that applies to this edge.
		$align = $is_horizontal ? $align_h : $align_v;

		// Auto-resolve tooltip position to opposite edge if requested.
		if ( 'auto' === $tooltip_pos ) {
			$opposite = array(
				'left'   => 'right',
				'right'  => 'left',
				'top'    => 'bottom',
				'bottom' => 'top',
			);
			$tooltip_pos = isset( $opposite[ $attach_edge ] ) ? $opposite[ $attach_edge ] : 'right';
		}

		$wrapper_classes = array(
			'eap-onepage-nav',
			'eap-onepage-nav--edge-' . esc_attr( $attach_edge ),
			'eap-onepage-nav--align-' . esc_attr( str_replace( 'flex-', '', $align ) ),
			'eap-onepage-nav--orient-' . ( $is_horizontal ? 'horizontal' : 'vertical' ),
			'eap-onepage-nav--mode-' . esc_attr( $sticky_mode ),
		);
		if ( ! $show_label ) {
			$wrapper_classes[] = 'eap-onepage-nav--no-label';
		}
		if ( $show_icon ) {
			$wrapper_classes[] = 'eap-onepage-nav--has-icon';
		}
		if ( $use_tooltip ) {
			$wrapper_classes[] = 'eap-onepage-nav--has-tooltip';
			$wrapper_classes[] = 'eap-onepage-nav--tooltip-' . esc_attr( $tooltip_pos );
		}

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'                => $wrapper_classes,
				'data-sticky-mode'     => esc_attr( $sticky_mode ),
				'data-container'       => esc_attr( $sticky_container ),
				'data-smooth-scroll'   => $smooth_scroll ? '1' : '0',
				'data-scroll-offset'   => (string) $scroll_offset,
				'data-active-offset'   => (string) $active_offset,
				'aria-label'           => esc_attr__( 'One page navigation', 'elementor-animatepro' ),
			)
		);
		?>
		<nav <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<ul class="eap-onepage-nav__list">
				<?php foreach ( $items as $index => $item ) :
					$label  = isset( $item['item_label'] ) ? $item['item_label'] : '';
					$target = isset( $item['item_target'] ) ? trim( (string) $item['item_target'] ) : '';
					if ( '' === $target ) {
						continue;
					}
					// Normalise target so users can enter "home", "#home", ".class".
					if ( '#' !== substr( $target, 0, 1 ) && '.' !== substr( $target, 0, 1 ) ) {
						$target = '#' . $target;
					}
					$is_first = ( 0 === $index );
					?>
					<li class="eap-onepage-nav__item<?php echo $is_first ? ' is-active' : ''; ?>" data-target="<?php echo esc_attr( $target ); ?>">
						<a class="eap-onepage-nav__link" href="<?php echo esc_attr( $target ); ?>" data-eap-onepage-link>
							<?php if ( $show_icon && ! empty( $item['item_icon']['value'] ) ) : ?>
								<span class="eap-onepage-nav__icon" aria-hidden="true">
									<?php Icons_Manager::render_icon( $item['item_icon'], array( 'aria-hidden' => 'true' ) ); ?>
								</span>
							<?php endif; ?>
							<?php if ( $show_label && ! $use_tooltip ) : ?>
								<span class="eap-onepage-nav__label"><?php echo esc_html( $label ); ?></span>
							<?php endif; ?>
							<?php if ( $use_tooltip ) : ?>
								<span class="eap-onepage-nav__tooltip" role="tooltip"><?php echo esc_html( $label ); ?></span>
							<?php endif; ?>
							<span class="screen-reader-text"><?php echo esc_html( $label ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
		<?php
	}
}
