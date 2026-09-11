<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

/**
 * Table of Contents widget.
 *
 * Discovers the headings already on the page and lists them, nested by level,
 * with smooth scrolling, the current heading highlighted as the reader moves,
 * a minimise button, and an optional floating box pinned to one side.
 *
 * WHY THIS EXISTS ALONGSIDE ONE PAGE NAV AND SCROLL ELEMENTS:
 * - One Page Nav points at sections you name by typing a CSS selector per
 *   item — the nav is authored.
 * - Scroll Elements OWNS its sections; you write the content inside it.
 * - This widget authors nothing: it reads the H2–H6 that are already in the
 *   content and keeps itself in step with them. Add a heading, the entry
 *   appears; rename one, the entry follows.
 *
 * Everything that depends on the page's DOM (collecting headings, giving them
 * ids, nesting, scroll-spy) happens in the browser, because the headings are
 * not available on the server: they live in other widgets, in the theme, in
 * the post content — none of which this widget can see at render time.
 *
 * The scroll-spy is EAPFrontend.scrollSpy() from core.js, shared with Scroll
 * Elements.
 */
class EAP_Widget_Table_Of_Contents extends EAP_Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-table-of-contents';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Table of Contents', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'toc', 'table of contents', 'headings', 'index', 'anchor', 'jump links', 'outline' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-table-of-content' );
	}

	/**
	 * Scripts.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-table-of-content-script' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_content_section();
		$this->register_box_section();
		$this->register_scroll_section();

		$this->register_box_style();
		$this->register_title_style();
		$this->register_list_style();
		$this->register_floating_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	/**
	 * Headings section.
	 *
	 * @return void
	 */
	protected function register_content_section() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Table of Contents', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'sibling_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Lists the headings already on the page and follows them as they change. To point at sections by selector, use <strong>One Page Nav</strong>; to write the sections inside the widget, use <strong>Scroll Elements</strong>.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Table of Contents', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'Title HTML Tag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'div',
				'options' => array(
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'div' => 'div',
					'p'   => 'p',
				),
				'description' => __( 'A heading tag would list the title in itself; "div" keeps it out of the trail.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'levels',
			array(
				'label'       => __( 'Heading Levels', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'default'     => array( 'h2', 'h3' ),
				'options'     => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
			)
		);

		$this->add_control(
			'container',
			array(
				'label'       => __( 'Look Inside', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Whole page', 'elementor-animatepro' ),
				'description' => __( 'A CSS selector. Leave empty to scan the whole page; set it to keep headers, footers and sidebars out.', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'exclude',
			array(
				'label'       => __( 'Exclude', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '.no-toc, .widget-title',
				'description' => __( 'Headings matching, or inside, these selectors are skipped.', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'marker',
			array(
				'label'   => __( 'Markers', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'numbers',
				'options' => array(
					'numbers' => __( 'Numbers (1, 1.1, 1.2)', 'elementor-animatepro' ),
					'bullets' => __( 'Bullets', 'elementor-animatepro' ),
					'none'    => __( 'None', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'hierarchical',
			array(
				'label'        => __( 'Nested View', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Indent H3s under their H2, and so on. Off gives one flat list.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'collapse_subitems',
			array(
				'label'        => __( 'Collapse Sub-items', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Show a heading’s children only while the reader is inside it.', 'elementor-animatepro' ),
				'condition'    => array( 'hierarchical' => 'yes' ),
			)
		);

		$this->add_control(
			'min_headings',
			array(
				'label'       => __( 'Minimum Headings', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 2,
				'min'         => 1,
				'max'         => 20,
				'description' => __( 'With fewer than this on the page, the widget hides itself.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'empty_text',
			array(
				'label'   => __( 'Editor Placeholder', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'No headings found on this page yet.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Box section.
	 *
	 * @return void
	 */
	protected function register_box_section() {
		$this->start_controls_section(
			'section_box',
			array( 'label' => __( 'Box', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'position',
			array(
				'label'   => __( 'Position', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => array(
					'inline'   => __( 'In the content', 'elementor-animatepro' ),
					'floating' => __( 'Floating at the side', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'floating_side',
			array(
				'label'     => __( 'Side', 'elementor-animatepro' ),
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
				'condition' => array( 'position' => 'floating' ),
			)
		);

		$this->add_control(
			'floating_open',
			array(
				'label'        => __( 'Start Open', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Off: only the tab shows until it is clicked.', 'elementor-animatepro' ),
				'condition'    => array( 'position' => 'floating' ),
			)
		);

		$this->add_control(
			'floating_icon',
			array(
				'label'     => __( 'Tab Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-list-ul',
					'library' => 'fa-solid',
				),
				'condition' => array( 'position' => 'floating' ),
			)
		);

		$this->add_control(
			'sticky',
			array(
				'label'        => __( 'Sticky', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Stays in view while its column scrolls — put the widget in a column beside the content.', 'elementor-animatepro' ),
				'condition'    => array( 'position' => 'inline' ),
			)
		);

		$this->add_control(
			'sticky_offset',
			array(
				'label'      => __( 'Sticky Offset', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 300 ) ),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-toc' => '--eap-toc-sticky: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'position' => 'inline',
					'sticky'   => 'yes',
				),
			)
		);

		$this->add_control(
			'minimize_button',
			array(
				'label'        => __( 'Minimise Button', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'start_minimized',
			array(
				'label'        => __( 'Start Minimised', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'condition'    => array( 'minimize_button' => 'yes' ),
			)
		);

		$this->add_control(
			'max_height',
			array(
				'label'      => __( 'Max Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 100, 'max' => 900 ),
					'vh' => array( 'min' => 20, 'max' => 90 ),
				),
				'description' => __( 'The list scrolls inside the box past this.', 'elementor-animatepro' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-toc__body' => 'max-height: {{SIZE}}{{UNIT}}; overflow: auto;',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Scrolling section.
	 *
	 * @return void
	 */
	protected function register_scroll_section() {
		$this->start_controls_section(
			'section_scroll',
			array( 'label' => __( 'Scrolling', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'scroll_offset',
			array(
				'label'       => __( 'Scroll Offset', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 90,
				'min'         => 0,
				'max'         => 400,
				'description' => __( 'Pixels left above a heading when jumped to — the height of a fixed header.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'smooth',
			array(
				'label'        => __( 'Smooth Scroll', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'highlight',
			array(
				'label'        => __( 'Highlight Current', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Mark the heading the reader is on as they scroll.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'update_hash',
			array(
				'label'        => __( 'Update URL On Click', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Puts #heading in the address bar so the spot is linkable.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	/**
	 * Box style.
	 *
	 * @return void
	 */
	protected function register_box_style() {
		$this->start_controls_section(
			'style_box',
			array(
				'label' => __( 'Box', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'box_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-toc' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_width',
			array(
				'label'      => __( 'Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 180, 'max' => 600 ),
					'%'  => array( 'min' => 10, 'max' => 100 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-toc' => '--eap-toc-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-toc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .eap-toc',
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-toc' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .eap-toc',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Title style.
	 *
	 * @return void
	 */
	protected function register_title_style() {
		$this->start_controls_section(
			'style_title',
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
				'selectors' => array(
					'{{WRAPPER}} .eap-toc__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-toc__title',
			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label'      => __( 'Space Below', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
					'em' => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-toc' => '--eap-toc-title-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'toggle_color',
			array(
				'label'     => __( 'Minimise Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-toc__toggle' => 'color: {{VALUE}};',
				),
				'condition' => array( 'minimize_button' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * List style.
	 *
	 * @return void
	 */
	protected function register_list_style() {
		$this->start_controls_section(
			'style_list',
			array(
				'label' => __( 'List', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_typography',
				'selector' => '{{WRAPPER}} .eap-toc__link',
			)
		);

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => __( 'Item Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 30 ),
					'em' => array( 'min' => 0, 'max' => 2, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-toc' => '--eap-toc-item-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'indent',
			array(
				'label'      => __( 'Nesting Indent', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
					'em' => array( 'min' => 0, 'max' => 4, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-toc' => '--eap-toc-indent: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'hierarchical' => 'yes' ),
			)
		);

		$this->start_controls_tabs( 'item_tabs' );

		$this->start_controls_tab(
			'item_tab_normal',
			array( 'label' => __( 'Normal', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'item_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-toc__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'marker_color',
			array(
				'label'     => __( 'Marker Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-toc' => '--eap-toc-marker: {{VALUE}};',
				),
				'condition' => array( 'marker!' => 'none' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'item_tab_hover',
			array( 'label' => __( 'Hover', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'item_color_hover',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-toc__link:hover, {{WRAPPER}} .eap-toc__link:focus-visible' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'item_tab_active',
			array( 'label' => __( 'Active', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'item_color_active',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-toc__item.is-active > .eap-toc__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_bg_active',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-toc__item.is-active > .eap-toc__link' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_weight_active',
			array(
				'label'     => __( 'Bold', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'return_value' => 'yes',
				'selectors_dictionary' => array(
					'yes' => '--eap-toc-active-weight: 600;',
					''    => '--eap-toc-active-weight: inherit;',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-toc' => '{{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'link_padding',
			array(
				'label'      => __( 'Link Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .eap-toc__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'link_radius',
			array(
				'label'      => __( 'Link Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-toc__link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Floating tab style.
	 *
	 * @return void
	 */
	protected function register_floating_style() {
		$this->start_controls_section(
			'style_floating',
			array(
				'label'     => __( 'Floating Tab', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'position' => 'floating' ),
			)
		);

		$this->add_control(
			'tab_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-toc__tab' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-toc__tab' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'tab_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 32, 'max' => 80 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-toc' => '--eap-toc-tab: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tab_top',
			array(
				'label'      => __( 'Vertical Position', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array( 'min' => 0, 'max' => 100 ),
					'px' => array( 'min' => 0, 'max' => 800 ),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-toc' => '--eap-toc-float-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	/**
	 * Render.
	 *
	 * The list itself is empty here: it is filled in the browser from the
	 * headings on the page.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$editor   = $this->eap_is_editor();

		$levels = isset( $settings['levels'] ) && is_array( $settings['levels'] ) ? $settings['levels'] : array( 'h2', 'h3' );
		$levels = array_values( array_intersect( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), $levels ) );
		if ( empty( $levels ) ) {
			$levels = array( 'h2', 'h3' );
		}

		$position = 'floating' === ( $settings['position'] ?? 'inline' ) ? 'floating' : 'inline';
		$side     = 'left' === ( $settings['floating_side'] ?? 'right' ) ? 'left' : 'right';
		$marker   = in_array( $settings['marker'] ?? 'numbers', array( 'numbers', 'bullets', 'none' ), true ) ? $settings['marker'] : 'numbers';

		$minimize  = 'yes' === ( $settings['minimize_button'] ?? 'yes' );
		$minimized = $minimize && 'yes' === ( $settings['start_minimized'] ?? '' );
		$open      = 'inline' === $position || 'yes' === ( $settings['floating_open'] ?? '' );

		$config = array(
			'levels'    => $levels,
			'container' => trim( (string) ( $settings['container'] ?? '' ) ),
			'exclude'   => trim( (string) ( $settings['exclude'] ?? '' ) ),
			'nested'    => 'yes' === ( $settings['hierarchical'] ?? 'yes' ),
			'collapse'  => 'yes' === ( $settings['collapse_subitems'] ?? '' ),
			'min'       => max( 1, (int) ( $settings['min_headings'] ?? 2 ) ),
			'offset'    => max( 0, (int) ( $settings['scroll_offset'] ?? 90 ) ),
			'smooth'    => 'yes' === ( $settings['smooth'] ?? 'yes' ),
			'highlight' => 'yes' === ( $settings['highlight'] ?? 'yes' ),
			'hash'      => 'yes' === ( $settings['update_hash'] ?? 'yes' ),
			'editor'    => $editor,
		);

		$classes = array(
			'eap-widget',
			'eap-toc',
			'eap-toc--' . $position,
			'eap-toc--marker-' . $marker,
			$config['nested'] ? 'eap-toc--nested' : 'eap-toc--flat',
		);
		if ( 'floating' === $position ) {
			$classes[] = 'eap-toc--' . $side;
		}
		if ( 'inline' === $position && 'yes' === ( $settings['sticky'] ?? '' ) ) {
			$classes[] = 'eap-toc--sticky';
		}
		if ( $minimized ) {
			$classes[] = 'is-minimized';
		}
		if ( ! $open ) {
			$classes[] = 'is-closed';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes, true );
		$this->add_render_attribute( 'wrapper', 'data-eap-toc', wp_json_encode( $config ), true );

		$title     = (string) ( $settings['title'] ?? '' );
		$title_tag = in_array( $settings['title_tag'] ?? 'div', array( 'h2', 'h3', 'h4', 'div', 'p' ), true ) ? $settings['title_tag'] : 'div';
		$uid       = 'eap-toc-' . $this->get_id();
		?>
		<nav <?php $this->print_render_attribute_string( 'wrapper' ); ?> aria-label="<?php echo esc_attr( '' !== $title ? $title : __( 'Table of contents', 'elementor-animatepro' ) ); ?>">
			<?php if ( 'floating' === $position ) : ?>
				<button type="button" class="eap-toc__tab" data-eap-toc-tab aria-expanded="<?php echo $open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $uid ); ?>-box" aria-label="<?php echo esc_attr( '' !== $title ? $title : __( 'Table of contents', 'elementor-animatepro' ) ); ?>">
					<?php
					if ( ! empty( $settings['floating_icon']['value'] ) ) {
						Icons_Manager::render_icon( $settings['floating_icon'], array( 'aria-hidden' => 'true' ) );
					} else {
						echo '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
					}
					?>
				</button>
			<?php endif; ?>

			<div class="eap-toc__box" id="<?php echo esc_attr( $uid ); ?>-box">
				<?php if ( '' !== $title || $minimize ) : ?>
					<div class="eap-toc__head">
						<?php if ( '' !== $title ) : ?>
							<<?php echo $title_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- whitelisted above. ?> class="eap-toc__title"><?php echo esc_html( $title ); ?></<?php echo $title_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php endif; ?>

						<?php if ( $minimize ) : ?>
							<button type="button" class="eap-toc__toggle" data-eap-toc-toggle aria-expanded="<?php echo $minimized ? 'false' : 'true'; ?>" aria-controls="<?php echo esc_attr( $uid ); ?>-body" aria-label="<?php esc_attr_e( 'Toggle table of contents', 'elementor-animatepro' ); ?>">
								<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</button>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="eap-toc__body" id="<?php echo esc_attr( $uid ); ?>-body"<?php echo $minimized ? ' hidden' : ''; ?>>
					<ol class="eap-toc__list" data-eap-toc-list></ol>
					<p class="eap-toc__empty" data-eap-toc-empty hidden><?php echo esc_html( $settings['empty_text'] ?? '' ); ?></p>
				</div>
			</div>
		</nav>
		<?php
	}
}
