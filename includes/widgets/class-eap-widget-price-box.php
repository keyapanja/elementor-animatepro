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

/**
 * Price Box — a single, fully styleable pricing card.
 *
 * Designed like Elementor Pro's Price Table: each widget is ONE card (header,
 * price, feature list, ribbon, CTA + footer note). Drop several side by side in
 * a flex container / columns to build a pricing table. Everything is editable
 * via the Style tab. CSS-only (tooltips and ribbon are pure CSS), so it has no
 * script dependency.
 */
class EAP_Widget_Price_Box extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-price-box';
	}

	public function get_title() {
		return __( 'Price Box', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_keywords() {
		return array( 'price', 'pricing', 'table', 'plan', 'package', 'box', 'product' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'price-box' );
	}

	protected function register_controls() {
		// Content.
		$this->register_header_controls();
		$this->register_pricing_controls();
		$this->register_features_controls();
		$this->register_footer_controls();
		$this->register_ribbon_controls();
		$this->register_tooltip_controls();

		// Style.
		$this->register_box_style();
		$this->register_header_style();
		$this->register_pricing_style();
		$this->register_features_style();
		$this->register_tooltip_style();
		$this->register_ribbon_style();
		$this->register_button_style();
		$this->register_footer_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_header_controls() {
		$this->start_controls_section(
			'section_header',
			array(
				'label' => __( 'Header', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label' => __( 'Icon', 'elementor-animatepro' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Professional', 'elementor-animatepro' ),
				'placeholder' => __( 'Plan name', 'elementor-animatepro' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
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
			'show_subtitle',
			array(
				'label'        => __( 'Show Description', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label'       => __( 'Description', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => __( 'Free trial 30 days.', 'elementor-animatepro' ),
				'condition'   => array( 'show_subtitle' => 'yes' ),
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();
	}

	protected function register_pricing_controls() {
		$this->start_controls_section(
			'section_pricing',
			array(
				'label' => __( 'Pricing', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'price',
			array(
				'label'       => __( 'Price', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '99',
				'placeholder' => '99',
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'offering_discount',
			array(
				'label'        => __( 'Offering Discount?', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'original_price',
			array(
				'label'       => __( 'Original Price', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '149',
				'placeholder' => '149',
				'condition'   => array( 'offering_discount' => 'yes' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'currency_symbol',
			array(
				'label'   => __( 'Currency Symbol', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '$',
				'options' => array(
					''       => __( 'None', 'elementor-animatepro' ),
					'$'      => '&#36; ' . __( 'Dollar', 'elementor-animatepro' ),
					'€'      => '&#8364; ' . __( 'Euro', 'elementor-animatepro' ),
					'£'      => '&#163; ' . __( 'Pound', 'elementor-animatepro' ),
					'¥'      => '&#165; ' . __( 'Yen / Yuan', 'elementor-animatepro' ),
					'₹'      => '&#8377; ' . __( 'Rupee', 'elementor-animatepro' ),
					'₽'      => '&#8381; ' . __( 'Ruble', 'elementor-animatepro' ),
					'₩'      => '&#8361; ' . __( 'Won', 'elementor-animatepro' ),
					'R$'     => 'R$ ' . __( 'Real', 'elementor-animatepro' ),
					'custom' => __( 'Custom', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'currency_custom',
			array(
				'label'       => __( 'Custom Symbol', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'condition'   => array( 'currency_symbol' => 'custom' ),
			)
		);

		$this->add_control(
			'currency_format',
			array(
				'label'   => __( 'Currency Position', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'raised',
				'options' => array(
					'normal' => __( 'Normal', 'elementor-animatepro' ),
					'raised' => __( 'Raised', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'period',
			array(
				'label'       => __( 'Period / Duration', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( '/month', 'elementor-animatepro' ),
				'placeholder' => __( '/month', 'elementor-animatepro' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'period_position',
			array(
				'label'     => __( 'Period Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'beside',
				'options'   => array(
					'beside' => __( 'Beside', 'elementor-animatepro' ),
					'below'  => __( 'Below', 'elementor-animatepro' ),
				),
				'condition' => array( 'period!' => '' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_features_controls() {
		$this->start_controls_section(
			'section_features',
			array(
				'label' => __( 'Features', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'list_icon',
			array(
				'label'   => __( 'Default List Icon', 'elementor-animatepro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'       => __( 'Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'List Item', 'elementor-animatepro' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'item_icon',
			array(
				'label'       => __( 'Icon', 'elementor-animatepro' ),
				'type'        => Controls_Manager::ICONS,
				'description' => __( 'Leave empty to use the default list icon.', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'excluded',
			array(
				'label'        => __( 'Excluded', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Show this feature as not included (muted / struck through).', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'tooltip_text',
			array(
				'label'       => __( 'Tooltip', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __( 'Optional hint shown on hover (enable Tooltip section).', 'elementor-animatepro' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'features',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array( 'text' => __( 'Free for lifetime', 'elementor-animatepro' ) ),
					array( 'text' => __( '5 user accounts', 'elementor-animatepro' ) ),
					array( 'text' => __( '10 MB storage', 'elementor-animatepro' ) ),
					array( 'text' => __( 'Monthly backups', 'elementor-animatepro' ) ),
					array(
						'text'     => __( 'No encryption', 'elementor-animatepro' ),
						'excluded' => 'yes',
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_footer_controls() {
		$this->start_controls_section(
			'section_footer',
			array(
				'label' => __( 'Footer', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => __( 'Button Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Get Started', 'elementor-animatepro' ),
				'placeholder' => __( 'Get Started', 'elementor-animatepro' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'       => __( 'Button Link', 'elementor-animatepro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'https://example.com', 'elementor-animatepro' ),
				'default'     => array( 'url' => '#' ),
			)
		);

		$this->add_control(
			'button_icon',
			array(
				'label' => __( 'Button Icon', 'elementor-animatepro' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'button_icon_position',
			array(
				'label'     => __( 'Icon Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => array(
					'before' => __( 'Before', 'elementor-animatepro' ),
					'after'  => __( 'After', 'elementor-animatepro' ),
				),
				'condition' => array( 'button_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'footer_text',
			array(
				'label'       => __( 'Additional Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => '',
				'placeholder' => __( 'e.g. Money Back Guarantee!', 'elementor-animatepro' ),
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();
	}

	protected function register_ribbon_controls() {
		$this->start_controls_section(
			'section_ribbon',
			array(
				'label' => __( 'Ribbon', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_ribbon',
			array(
				'label'        => __( 'Show Ribbon', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'ribbon_style',
			array(
				'label'     => __( 'Style', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'corner',
				'options'   => array(
					'corner' => __( 'Corner Ribbon', 'elementor-animatepro' ),
					'circle' => __( 'Circular Ribbon', 'elementor-animatepro' ),
				),
				'condition' => array( 'show_ribbon' => 'yes' ),
			)
		);

		$this->add_control(
			'ribbon_text',
			array(
				'label'     => __( 'Title', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'NEW', 'elementor-animatepro' ),
				'condition' => array( 'show_ribbon' => 'yes' ),
				'dynamic'   => array( 'active' => true ),
			)
		);

		$this->add_control(
			'ribbon_position',
			array(
				'label'     => __( 'Horizontal Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'right',
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
				'condition' => array( 'show_ribbon' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'ribbon_circle_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 50, 'max' => 160 ) ),
				'default'    => array( 'size' => 84, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__ribbon--circle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'show_ribbon'  => 'yes',
					'ribbon_style' => 'circle',
				),
			)
		);

		$this->add_responsive_control(
			'ribbon_distance',
			array(
				'label'      => __( 'Distance', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => -20, 'max' => 60 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__ribbon--circle' => '--eap-pb-ribbon-distance: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'show_ribbon'  => 'yes',
					'ribbon_style' => 'circle',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_tooltip_controls() {
		$this->start_controls_section(
			'section_tooltip',
			array(
				'label' => __( 'Tooltip', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'enable_tooltip',
			array(
				'label'        => __( 'Enable Tooltip', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Show the per-feature Tooltip text on hover.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'tooltip_position',
			array(
				'label'     => __( 'Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top',
				'options'   => array(
					'top'    => __( 'Top', 'elementor-animatepro' ),
					'bottom' => __( 'Bottom', 'elementor-animatepro' ),
					'left'   => __( 'Left', 'elementor-animatepro' ),
					'right'  => __( 'Right', 'elementor-animatepro' ),
				),
				'condition' => array( 'enable_tooltip' => 'yes' ),
			)
		);

		$this->add_control(
			'tooltip_arrow',
			array(
				'label'        => __( 'Arrow', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'enable_tooltip' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'tooltip_distance',
			array(
				'label'      => __( 'Distance', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__features' => '--eap-pb-tip-distance: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'enable_tooltip' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_box_style() {
		$this->start_controls_section(
			'section_box_style',
			array(
				'label' => __( 'Box', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'box_align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
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
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'box_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .eap-price-box',
				'fields_options' => array(
					'background' => array( 'default' => 'classic' ),
					'color'      => array( 'default' => '#ffffff' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'box_border',
				'selector'       => '{{WRAPPER}} .eap-price-box',
				'fields_options' => array(
					'border' => array( 'default' => 'solid' ),
					'width'  => array(
						'default' => array(
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'unit'     => 'px',
							'isLinked' => true,
						),
					),
					'color'  => array( 'default' => '#e5e7eb' ),
				),
			)
		);

		$this->add_responsive_control(
			'box_radius',
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
					'{{WRAPPER}} .eap-price-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; --eap-pb-rtl: {{TOP}}{{UNIT}}; --eap-pb-rtr: {{RIGHT}}{{UNIT}}; --eap-pb-rbr: {{BOTTOM}}{{UNIT}}; --eap-pb-rbl: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .eap-price-box',
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_header_style() {
		$this->start_controls_section(
			'section_header_style',
			array(
				'label' => __( 'Header', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'header_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .eap-price-box__header',
			)
		);

		$this->add_responsive_control(
			'header_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 30,
					'right'  => 24,
					'bottom' => 24,
					'left'   => 24,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		// Icon.
		$this->add_control(
			'icon_heading',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'icon[value]!' => '' ),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 12, 'max' => 120 ),
					'em' => array( 'min' => 1, 'max' => 8 ),
				),
				'default'    => array( 'size' => 48, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__icon' => '--eap-pb-icon-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-price-box__icon svg' => 'fill: {{VALUE}};',
				),
				'condition' => array( 'icon[value]!' => '' ),
			)
		);

		$this->add_responsive_control(
			'icon_spacing',
			array(
				'label'      => __( 'Icon Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 16, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'icon[value]!' => '' ),
			)
		);

		// Title.
		$this->add_control(
			'title_heading',
			array(
				'label'     => __( 'Title', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1f2937',
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-price-box__title',
			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		// Subtitle.
		$this->add_control(
			'subtitle_heading',
			array(
				'label'     => __( 'Description', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'show_subtitle' => 'yes' ),
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__subtitle' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_subtitle' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'subtitle_typography',
				'selector'  => '{{WRAPPER}} .eap-price-box__subtitle',
				'condition' => array( 'show_subtitle' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_pricing_style() {
		$this->start_controls_section(
			'section_pricing_style',
			array(
				'label' => __( 'Pricing', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pricing_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .eap-price-box__pricing',
			)
		);

		$this->add_responsive_control(
			'pricing_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 16,
					'right'  => 24,
					'bottom' => 16,
					'left'   => 24,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__pricing' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		// Price.
		$this->add_control(
			'price_heading',
			array(
				'label'     => __( 'Price', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1f2937',
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__price' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				// Target the whole price unit so the currency symbol, fraction and
				// "beside" period (sized in em) scale together with the number.
				'name'           => 'price_typography',
				'selector'       => '{{WRAPPER}} .eap-price-box__price',
				'fields_options' => array(
					'typography' => array( 'default' => 'custom' ),
					'font_size'  => array(
						'default' => array(
							'size' => 48,
							'unit' => 'px',
						),
					),
					'font_weight' => array( 'default' => '700' ),
				),
			)
		);

		// Currency.
		$this->add_control(
			'currency_heading',
			array(
				'label'     => __( 'Currency', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'currency_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__currency' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'currency_typography',
				'selector' => '{{WRAPPER}} .eap-price-box__currency',
			)
		);

		// Fraction.
		$this->add_control(
			'fraction_heading',
			array(
				'label'     => __( 'Fraction', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'fraction_typography',
				'selector' => '{{WRAPPER}} .eap-price-box__fraction',
			)
		);

		// Period.
		$this->add_control(
			'period_heading',
			array(
				'label'     => __( 'Period', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'period_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__period' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'period_typography',
				'selector' => '{{WRAPPER}} .eap-price-box__period',
			)
		);

		// Original price.
		$this->add_control(
			'original_heading',
			array(
				'label'     => __( 'Original Price', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'offering_discount' => 'yes' ),
			)
		);

		$this->add_control(
			'original_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9ca3af',
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__original' => 'color: {{VALUE}};',
				),
				'condition' => array( 'offering_discount' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'original_typography',
				'selector'  => '{{WRAPPER}} .eap-price-box__original',
				'condition' => array( 'offering_discount' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_features_style() {
		$this->start_controls_section(
			'section_features_style',
			array(
				'label' => __( 'Features', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'features_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 8,
					'right'  => 24,
					'bottom' => 8,
					'left'   => 24,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__features' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'feature_row_padding',
			array(
				'label'      => __( 'Row Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 12,
					'right'  => 0,
					'bottom' => 12,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__feature' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'feature_align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
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
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__feature' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'feature_text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4b5563',
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__feature-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'feature_typography',
				'selector' => '{{WRAPPER}} .eap-price-box__feature-text',
			)
		);

		$this->add_control(
			'feature_icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#22c55e',
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__feature-icon'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-price-box__feature-icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'feature_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 8, 'max' => 40 ),
					'em' => array( 'min' => 0.5, 'max' => 3 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__feature-icon' => '--eap-pb-feat-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'feature_icon_gap',
			array(
				'label'      => __( 'Icon Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__feature' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'feature_excluded_color',
			array(
				'label'     => __( 'Excluded Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#b0b7c3',
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__feature--excluded .eap-price-box__feature-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-price-box__feature--excluded .eap-price-box__feature-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-price-box__feature--excluded .eap-price-box__feature-icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		// Divider.
		$this->add_control(
			'feature_divider_heading',
			array(
				'label'     => __( 'Divider', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'feature_divider',
			array(
				'label'        => __( 'Show Divider', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'feature_divider_color',
			array(
				'label'     => __( 'Divider Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eceff3',
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__feature:not(:last-child)' => 'border-bottom: var(--eap-pb-divider-width, 1px) solid {{VALUE}};',
				),
				'condition' => array( 'feature_divider' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'feature_divider_width',
			array(
				'label'      => __( 'Divider Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 8 ) ),
				'default'    => array( 'size' => 1, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__features' => '--eap-pb-divider-width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'feature_divider' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_tooltip_style() {
		$this->start_controls_section(
			'section_tooltip_style',
			array(
				'label'     => __( 'Tooltip', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'enable_tooltip' => 'yes' ),
			)
		);

		$this->add_control(
			'tooltip_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1f2937',
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__feature[data-eap-tooltip]::after'  => 'background: {{VALUE}};',
					'{{WRAPPER}} .eap-price-box__feature[data-eap-tooltip]::before' => '--eap-pb-tip-bg: {{VALUE}};',
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
					'{{WRAPPER}} .eap-price-box__feature[data-eap-tooltip]::after' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tooltip_typography',
				'selector' => '{{WRAPPER}} .eap-price-box__feature[data-eap-tooltip]::after',
			)
		);

		$this->add_responsive_control(
			'tooltip_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 6, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__feature[data-eap-tooltip]::after' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'top'    => 8,
					'right'  => 12,
					'bottom' => 8,
					'left'   => 12,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__feature[data-eap-tooltip]::after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tooltip_width',
			array(
				'label'      => __( 'Max Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 80, 'max' => 400 ) ),
				'default'    => array( 'size' => 200, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__feature[data-eap-tooltip]::after' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_ribbon_style() {
		$this->start_controls_section(
			'section_ribbon_style',
			array(
				'label'     => __( 'Ribbon', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_ribbon' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'ribbon_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .eap-price-box__ribbon-inner',
				'fields_options' => array(
					'background' => array( 'default' => 'classic' ),
					'color'      => array( 'default' => '#1f2937' ),
				),
			)
		);

		$this->add_control(
			'ribbon_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__ribbon-inner' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ribbon_typography',
				'selector' => '{{WRAPPER}} .eap-price-box__ribbon-inner',
			)
		);

		$this->end_controls_section();
	}

	protected function register_button_style() {
		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => __( 'Button', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'button_align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
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
					'stretch'    => array(
						'title' => __( 'Justified', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__footer' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .eap-price-box__button',
			)
		);

		$this->start_controls_tabs( 'button_style_tabs' );

		$this->start_controls_tab( 'button_tab_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'button_background',
				'types'          => array( 'classic', 'gradient' ),
				'selector'       => '{{WRAPPER}} .eap-price-box__button',
				'fields_options' => array(
					'background' => array( 'default' => 'classic' ),
					'color'      => array( 'default' => '#4f46e5' ),
				),
			)
		);

		$this->add_control(
			'button_border_color',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__button' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'button_tab_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'button_color_hover',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__button:hover, {{WRAPPER}} .eap-price-box__button:focus' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'button_background_hover',
				'types'          => array( 'classic', 'gradient' ),
				'selector'       => '{{WRAPPER}} .eap-price-box__button:hover, {{WRAPPER}} .eap-price-box__button:focus',
				'fields_options' => array(
					'background' => array( 'default' => 'classic' ),
					'color'      => array( 'default' => '#4338ca' ),
				),
			)
		);

		$this->add_control(
			'button_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__button:hover, {{WRAPPER}} .eap-price-box__button:focus' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_border_width',
			array(
				'label'      => __( 'Border Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__button' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'top'      => 50,
					'right'    => 50,
					'bottom'   => 50,
					'left'     => 50,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 14,
					'right'  => 36,
					'bottom' => 14,
					'left'   => 36,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .eap-price-box__button',
			)
		);

		$this->add_responsive_control(
			'footer_padding',
			array(
				'label'      => __( 'Footer Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'separator'  => 'before',
				'default'    => array(
					'top'    => 24,
					'right'  => 24,
					'bottom' => 30,
					'left'   => 24,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_footer_style() {
		$this->start_controls_section(
			'section_footer_style',
			array(
				'label'     => __( 'Additional Text', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'footer_text!' => '' ),
			)
		);

		$this->add_control(
			'footer_text_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9ca3af',
				'selectors' => array(
					'{{WRAPPER}} .eap-price-box__footer-note' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'footer_text_typography',
				'selector' => '{{WRAPPER}} .eap-price-box__footer-note',
			)
		);

		$this->add_responsive_control(
			'footer_text_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-price-box__footer-note' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	protected function render() {
		$settings = $this->get_settings_for_display();

		$classes = array(
			'eap-widget',
			'eap-price-box',
			'eap-price-box--period-' . ( ! empty( $settings['period_position'] ) ? $settings['period_position'] : 'beside' ),
			'eap-price-box--currency-' . ( ! empty( $settings['currency_format'] ) ? $settings['currency_format'] : 'raised' ),
		);

		if ( 'yes' === ( $settings['enable_tooltip'] ?? '' ) ) {
			$classes[] = 'eap-price-box--tooltip';
			$classes[] = 'eap-price-box--tip-' . ( ! empty( $settings['tooltip_position'] ) ? $settings['tooltip_position'] : 'top' );
			if ( 'yes' === ( $settings['tooltip_arrow'] ?? '' ) ) {
				$classes[] = 'eap-price-box--tip-arrow';
			}
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php
			$this->render_ribbon( $settings );
			$this->render_header( $settings );
			$this->render_pricing( $settings );
			$this->render_features( $settings );
			$this->render_footer( $settings );
			?>
		</div>
		<?php
	}

	protected function render_ribbon( $settings ) {
		if ( 'yes' !== ( $settings['show_ribbon'] ?? '' ) || '' === trim( (string) ( $settings['ribbon_text'] ?? '' ) ) ) {
			return;
		}

		$style    = ! empty( $settings['ribbon_style'] ) ? $settings['ribbon_style'] : 'corner';
		$position = ! empty( $settings['ribbon_position'] ) ? $settings['ribbon_position'] : 'right';
		?>
		<div class="eap-price-box__ribbon eap-price-box__ribbon--<?php echo esc_attr( $style ); ?> eap-price-box__ribbon--<?php echo esc_attr( $position ); ?>">
			<span class="eap-price-box__ribbon-inner"><?php echo esc_html( $settings['ribbon_text'] ); ?></span>
		</div>
		<?php
	}

	protected function render_header( $settings ) {
		$has_icon  = ! empty( $settings['icon']['value'] );
		$has_title = '' !== trim( (string) ( $settings['title'] ?? '' ) );
		$has_sub   = 'yes' === ( $settings['show_subtitle'] ?? '' ) && '' !== trim( (string) ( $settings['subtitle'] ?? '' ) );

		if ( ! $has_icon && ! $has_title && ! $has_sub ) {
			return;
		}

		$tag = ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h3';
		$tag = in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ), true ) ? $tag : 'h3';
		?>
		<div class="eap-price-box__header">
			<?php if ( $has_icon ) : ?>
				<div class="eap-price-box__icon">
					<?php Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $has_title ) : ?>
				<<?php echo esc_html( $tag ); ?> class="eap-price-box__title"><?php echo esc_html( $settings['title'] ); ?></<?php echo esc_html( $tag ); ?>>
			<?php endif; ?>

			<?php if ( $has_sub ) : ?>
				<div class="eap-price-box__subtitle"><?php echo wp_kses_post( $settings['subtitle'] ); ?></div>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function render_pricing( $settings ) {
		$price = trim( (string) ( $settings['price'] ?? '' ) );

		if ( '' === $price && '' === trim( (string) ( $settings['period'] ?? '' ) ) ) {
			return;
		}

		// Currency symbol.
		$symbol = $settings['currency_symbol'] ?? '$';
		if ( 'custom' === $symbol ) {
			$symbol = (string) ( $settings['currency_custom'] ?? '' );
		}

		// Split integer / fractional part.
		$integer  = $price;
		$fraction = '';
		if ( false !== strpos( $price, '.' ) ) {
			$parts    = explode( '.', $price, 2 );
			$integer  = $parts[0];
			$fraction = '.' . $parts[1];
		}

		$has_discount   = 'yes' === ( $settings['offering_discount'] ?? '' ) && '' !== trim( (string) ( $settings['original_price'] ?? '' ) );
		$period         = trim( (string) ( $settings['period'] ?? '' ) );
		$period_below   = 'below' === ( $settings['period_position'] ?? 'beside' );
		?>
		<div class="eap-price-box__pricing">
			<?php if ( $has_discount ) : ?>
				<span class="eap-price-box__original">
					<?php if ( '' !== $symbol ) : ?><span class="eap-price-box__original-currency"><?php echo esc_html( $symbol ); ?></span><?php endif; ?><?php echo esc_html( $settings['original_price'] ); ?>
				</span>
			<?php endif; ?>

			<span class="eap-price-box__price">
				<?php if ( '' !== $symbol ) : ?><span class="eap-price-box__currency"><?php echo esc_html( $symbol ); ?></span><?php endif; ?><span class="eap-price-box__amount"><?php echo esc_html( $integer ); ?></span><?php if ( '' !== $fraction ) : ?><span class="eap-price-box__fraction"><?php echo esc_html( $fraction ); ?></span><?php endif; ?><?php if ( '' !== $period && ! $period_below ) : ?><span class="eap-price-box__period eap-price-box__period--beside"><?php echo esc_html( $period ); ?></span><?php endif; ?>
			</span>

			<?php if ( '' !== $period && $period_below ) : ?>
				<span class="eap-price-box__period eap-price-box__period--below"><?php echo esc_html( $period ); ?></span>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function render_features( $settings ) {
		$features = ! empty( $settings['features'] ) && is_array( $settings['features'] ) ? $settings['features'] : array();

		if ( empty( $features ) ) {
			return;
		}

		$default_icon  = $settings['list_icon'] ?? array();
		$tooltip_on    = 'yes' === ( $settings['enable_tooltip'] ?? '' );
		?>
		<ul class="eap-price-box__features">
			<?php
			foreach ( $features as $index => $item ) {
				$item_classes = array(
					'eap-price-box__feature',
					'elementor-repeater-item-' . ( $item['_id'] ?? $index ),
				);
				if ( 'yes' === ( $item['excluded'] ?? '' ) ) {
					$item_classes[] = 'eap-price-box__feature--excluded';
				}

				$tooltip_attr = '';
				if ( $tooltip_on && '' !== trim( (string) ( $item['tooltip_text'] ?? '' ) ) ) {
					$tooltip_attr = ' data-eap-tooltip="' . esc_attr( $item['tooltip_text'] ) . '" tabindex="0"';
				}

				// Per-item icon, falling back to the default list icon.
				$icon = ( ! empty( $item['item_icon']['value'] ) ) ? $item['item_icon'] : $default_icon;
				?>
				<li class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>"<?php echo $tooltip_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php if ( ! empty( $icon['value'] ) ) : ?>
						<span class="eap-price-box__feature-icon"><?php Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) ); ?></span>
					<?php endif; ?>
					<span class="eap-price-box__feature-text"><?php echo esc_html( $item['text'] ?? '' ); ?></span>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
	}

	protected function render_footer( $settings ) {
		$button_text = trim( (string) ( $settings['button_text'] ?? '' ) );
		$footer_text = trim( (string) ( $settings['footer_text'] ?? '' ) );

		if ( '' === $button_text && '' === $footer_text ) {
			return;
		}
		?>
		<div class="eap-price-box__footer">
			<?php
			if ( '' !== $button_text ) {
				$tag = 'a';
				$this->add_render_attribute( 'button', 'class', 'eap-price-box__button' );

				if ( ! empty( $settings['button_link']['url'] ) ) {
					$this->add_link_attributes( 'button', $settings['button_link'] );
				} else {
					$tag = 'span';
				}

				$icon_pos  = ! empty( $settings['button_icon_position'] ) ? $settings['button_icon_position'] : 'after';
				$icon_html = '';
				if ( ! empty( $settings['button_icon']['value'] ) ) {
					ob_start();
					Icons_Manager::render_icon( $settings['button_icon'], array( 'aria-hidden' => 'true' ) );
					$icon_html = '<span class="eap-price-box__button-icon">' . ob_get_clean() . '</span>';
				}
				?>
				<<?php echo esc_html( $tag ); ?> <?php $this->print_render_attribute_string( 'button' ); ?>>
					<?php
					if ( 'before' === $icon_pos ) {
						echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
					<span class="eap-price-box__button-text"><?php echo esc_html( $button_text ); ?></span>
					<?php
					if ( 'after' === $icon_pos ) {
						echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</<?php echo esc_html( $tag ); ?>>
				<?php
			}

			if ( '' !== $footer_text ) {
				echo '<div class="eap-price-box__footer-note">' . wp_kses_post( $footer_text ) . '</div>';
			}
			?>
		</div>
		<?php
	}
}
