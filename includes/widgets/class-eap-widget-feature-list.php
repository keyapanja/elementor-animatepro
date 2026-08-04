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
use Elementor\Repeater;
use Elementor\Utils;

/**
 * Feature List — a vertical list of feature items, each with an icon (or image),
 * a title and a description. Supports icon on the left or right, several icon
 * shapes/views (circle / rounded / square / diamond, filled or framed), an
 * optional connector line joining the icons, per-item colour overrides, and
 * optional per-item links. CSS-only (no runtime script).
 */
class EAP_Widget_Feature_List extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-feature-list';
	}

	public function get_title() {
		return __( 'Feature List', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	public function get_keywords() {
		return array( 'feature', 'list', 'icon', 'services', 'checklist', 'steps', 'connector' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'feature-list' );
	}

	protected function register_controls() {
		$this->register_layout_controls();
		$this->register_items_controls();

		$this->register_item_style();
		$this->register_icon_style();
		$this->register_connector_style();
		$this->register_title_style();
		$this->register_desc_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'   => __( 'Icon Position', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
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
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'Title HTML Tag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
			)
		);

		$this->add_control(
			'show_connector',
			array(
				'label'        => __( 'Show Connector', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Draw a line joining each icon to the next.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_items_controls() {
		$this->start_controls_section(
			'section_items',
			array(
				'label' => __( 'List Items', 'elementor-animatepro' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon_type',
			array(
				'label'   => __( 'Media Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => array(
					'icon'  => __( 'Icon', 'elementor-animatepro' ),
					'image' => __( 'Image', 'elementor-animatepro' ),
					'none'  => __( 'None', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'selected_icon',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				),
				'condition' => array( 'icon_type' => 'icon' ),
			)
		);

		$repeater->add_control(
			'item_image',
			array(
				'label'     => __( 'Image', 'elementor-animatepro' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array( 'icon_type' => 'image' ),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Feature Title', 'elementor-animatepro' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label'   => __( 'Description', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => __( 'A simplified version of Info but comes with powerful features.', 'elementor-animatepro' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'         => __( 'Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'placeholder'   => __( 'https://example.com', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'item_style_heading',
			array(
				'label'     => __( 'Icon Colours', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$repeater->add_control(
			'item_icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .eap-feature-list__icon'     => 'color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} .eap-feature-list__icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'item_icon_bg',
			array(
				'label'     => __( 'Icon Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .eap-feature-list__icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'selected_icon' => array( 'value' => 'fas fa-check', 'library' => 'fa-solid' ),
						'title'         => __( 'Amazing Design', 'elementor-animatepro' ),
						'description'   => __( 'Use its fantastic layout to enhance your design and make it attractive to your audience.', 'elementor-animatepro' ),
					),
					array(
						'selected_icon' => array( 'value' => 'fas fa-layer-group', 'library' => 'fa-solid' ),
						'title'         => __( 'Feature Items', 'elementor-animatepro' ),
						'description'   => __( 'Add as many feature items as you want. Insert an icon or an image for each item.', 'elementor-animatepro' ),
					),
					array(
						'selected_icon' => array( 'value' => 'fas fa-project-diagram', 'library' => 'fa-solid' ),
						'title'         => __( 'Display Connector', 'elementor-animatepro' ),
						'description'   => __( 'Show the connector between each icon and give the whole layout a unique design.', 'elementor-animatepro' ),
					),
					array(
						'selected_icon' => array( 'value' => 'fas fa-sliders-h', 'library' => 'fa-solid' ),
						'title'         => __( 'Ease of Styling', 'elementor-animatepro' ),
						'description'   => __( 'Easily personalize every aspect of the feature list you are showcasing on your website.', 'elementor-animatepro' ),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_item_style() {
		$this->start_controls_section(
			'section_item_style',
			array(
				'label' => __( 'Items', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'item_spacing',
			array(
				'label'      => __( 'Spacing Between Items', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'    => array( 'size' => 26, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-feature-list__item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-feature-list' => '--eap-fl-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_gap',
			array(
				'label'      => __( 'Icon Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 20, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-feature-list__item' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'item_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .eap-feature-list__item',
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-feature-list__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .eap-feature-list__item',
			)
		);

		$this->add_responsive_control(
			'item_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-feature-list__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_icon_style() {
		$this->start_controls_section(
			'section_icon_style',
			array(
				'label' => __( 'Icon', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_view',
			array(
				'label'   => __( 'View', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'stacked',
				'options' => array(
					'default' => __( 'Default', 'elementor-animatepro' ),
					'stacked' => __( 'Stacked', 'elementor-animatepro' ),
					'framed'  => __( 'Framed', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'icon_shape',
			array(
				'label'     => __( 'Shape', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'circle',
				'options'   => array(
					'circle'  => __( 'Circle', 'elementor-animatepro' ),
					'rounded' => __( 'Rounded', 'elementor-animatepro' ),
					'square'  => __( 'Square', 'elementor-animatepro' ),
					'diamond' => __( 'Diamond', 'elementor-animatepro' ),
				),
				'condition' => array( 'icon_view!' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'icon_box_size',
			array(
				'label'      => __( 'Box Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 160 ) ),
				'default'    => array( 'size' => 56, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-feature-list__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-feature-list' => '--eap-fl-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 8, 'max' => 100 ),
					'em' => array( 'min' => 0.5, 'max' => 6 ),
				),
				'default'    => array( 'size' => 22, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-feature-list__icon' => '--eap-fl-glyph: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'default'    => array( 'size' => 16, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-feature-list--shape-rounded .eap-feature-list__icon' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'icon_view!'  => 'default',
					'icon_shape'  => 'rounded',
				),
			)
		);

		$this->start_controls_tabs( 'icon_state_tabs' );

		$this->start_controls_tab( 'icon_tab_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-feature-list__icon'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-feature-list__icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#29b6f6',
				'selectors' => array(
					'{{WRAPPER}} .eap-feature-list__icon' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'icon_view' => 'stacked' ),
			)
		);

		$this->add_control(
			'icon_border_color',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#29b6f6',
				'selectors' => array(
					'{{WRAPPER}} .eap-feature-list__icon' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'icon_view' => 'framed' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'icon_tab_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'icon_color_hover',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-feature-list__item:hover .eap-feature-list__icon'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-feature-list__item:hover .eap-feature-list__icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-feature-list__item:hover .eap-feature-list__icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-feature-list__item:hover .eap-feature-list__icon' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'icon_border_width',
			array(
				'label'      => __( 'Border Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 12 ) ),
				'default'    => array( 'size' => 2, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-feature-list__icon' => 'border-width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'icon_view' => 'framed' ),
				'separator'  => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'icon_shadow',
				'selector'  => '{{WRAPPER}} .eap-feature-list__icon',
				'condition' => array( 'icon_view!' => 'default' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_connector_style() {
		$this->start_controls_section(
			'section_connector_style',
			array(
				'label'     => __( 'Connector', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_connector' => 'yes' ),
			)
		);

		$this->add_control(
			'connector_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cbd5e1',
				'selectors' => array(
					'{{WRAPPER}} .eap-feature-list' => '--eap-fl-connector-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'connector_style',
			array(
				'label'     => __( 'Style', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => array(
					'solid'  => __( 'Solid', 'elementor-animatepro' ),
					'dashed' => __( 'Dashed', 'elementor-animatepro' ),
					'dotted' => __( 'Dotted', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-feature-list' => '--eap-fl-connector-style: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'connector_width',
			array(
				'label'      => __( 'Thickness', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 1, 'max' => 12 ) ),
				'default'    => array( 'size' => 2, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-feature-list' => '--eap-fl-connector-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_title_style() {
		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => __( 'Title', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1f2937',
				'selectors' => array(
					'{{WRAPPER}} .eap-feature-list__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-feature-list__item:hover .eap-feature-list__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'selector'       => '{{WRAPPER}} .eap-feature-list__title',
				'fields_options' => array(
					'typography'  => array( 'default' => 'custom' ),
					'font_weight' => array( 'default' => '700' ),
				),
			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 6, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-feature-list__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_desc_style() {
		$this->start_controls_section(
			'section_desc_style',
			array(
				'label' => __( 'Description', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array(
					'{{WRAPPER}} .eap-feature-list__desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .eap-feature-list__desc',
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();

		if ( empty( $items ) ) {
			return;
		}

		$position = ! empty( $settings['icon_position'] ) ? $settings['icon_position'] : 'left';
		$view     = ! empty( $settings['icon_view'] ) ? $settings['icon_view'] : 'stacked';
		$shape    = ! empty( $settings['icon_shape'] ) ? $settings['icon_shape'] : 'circle';

		$classes = array(
			'eap-widget',
			'eap-feature-list',
			'eap-feature-list--pos-' . ( 'right' === $position ? 'right' : 'left' ),
			'eap-feature-list--view-' . $view,
		);
		if ( 'default' !== $view ) {
			$classes[] = 'eap-feature-list--shape-' . $shape;
		}
		if ( 'yes' === ( $settings['show_connector'] ?? '' ) ) {
			$classes[] = 'eap-feature-list--connector';
		}

		$tag = ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h3';
		$tag = in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ), true ) ? $tag : 'h3';
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php
			foreach ( $items as $index => $item ) {
				$this->render_item( $item, $index, $tag );
			}
			?>
		</div>
		<?php
	}

	protected function render_item( $item, $index, $tag ) {
		$key      = 'item_' . $index;
		$item_id  = ! empty( $item['_id'] ) ? $item['_id'] : (string) $index;
		$has_link = ! empty( $item['link']['url'] );
		$item_tag = $has_link ? 'a' : 'div';

		$this->add_render_attribute(
			$key,
			'class',
			array(
				'eap-feature-list__item',
				'elementor-repeater-item-' . $item_id,
			)
		);

		if ( $has_link ) {
			$this->add_link_attributes( $key, $item['link'] );
		}

		$title = trim( (string) ( $item['title'] ?? '' ) );
		$desc  = trim( (string) ( $item['description'] ?? '' ) );
		?>
		<<?php echo esc_html( $item_tag ); ?> <?php $this->print_render_attribute_string( $key ); ?>>
			<span class="eap-feature-list__icon-col">
				<span class="eap-feature-list__icon"><?php $this->render_item_media( $item ); ?></span>
			</span>
			<div class="eap-feature-list__content">
				<?php if ( '' !== $title ) : ?>
					<<?php echo esc_html( $tag ); ?> class="eap-feature-list__title"><?php echo wp_kses_post( $title ); ?></<?php echo esc_html( $tag ); ?>>
				<?php endif; ?>
				<?php if ( '' !== $desc ) : ?>
					<div class="eap-feature-list__desc"><?php echo wp_kses_post( $desc ); ?></div>
				<?php endif; ?>
			</div>
		</<?php echo esc_html( $item_tag ); ?>>
		<?php
	}

	protected function render_item_media( $item ) {
		$type = ! empty( $item['icon_type'] ) ? $item['icon_type'] : 'icon';

		if ( 'image' === $type && ! empty( $item['item_image']['url'] ) ) {
			$src = $item['item_image']['url'];
			$alt = $item['title'] ?? '';
			printf(
				'<img class="eap-feature-list__img" src="%s" alt="%s" loading="lazy" />',
				esc_url( $src ),
				esc_attr( $alt )
			);
			return;
		}

		if ( 'icon' === $type && ! empty( $item['selected_icon']['value'] ) ) {
			Icons_Manager::render_icon( $item['selected_icon'], array( 'aria-hidden' => 'true' ) );
		}
	}
}
