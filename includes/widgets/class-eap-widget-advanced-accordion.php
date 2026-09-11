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

/**
 * Advanced Accordion widget.
 *
 * Items of text or saved templates, one-at-a-time (accordion) or many-open
 * (toggle), with open/closed icons, per-item default state, linkable item
 * IDs, expand/collapse-all, and optional FAQPage structured data.
 *
 * BUILT ON NATIVE <details>/<summary>. That is the point of it:
 *
 * - It works with no JavaScript at all. Accordion mode puts every item in one
 *   `name` group, which browsers keep exclusive natively.
 * - The browser's find-in-page (Chrome) opens a closed item when the match
 *   is inside it. Content hidden by a script is invisible to find.
 * - Keyboard and screen-reader semantics come from the platform, not from
 *   hand-rolled ARIA.
 *
 * The script is an enhancement: animated open/close, animated exclusivity
 * (it takes over the `name` group so the item closing can animate instead of
 * snapping shut), deep links with a scroll offset, keep-in-view, and the
 * expand/collapse-all buttons.
 *
 * WHERE THIS SITS: Elementor's own Accordion (nested) makes every item a
 * droppable container. Reach for that to design items freely out of widgets;
 * reach for this for text and FAQ content, or to reuse saved templates.
 */
class EAP_Widget_Advanced_Accordion extends EAP_Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-advanced-accordion';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Advanced Accordion', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-accordion';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'accordion', 'toggle', 'faq', 'collapse', 'expand', 'questions', 'details', 'tabs' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-advanced-accordion' );
	}

	/**
	 * Scripts.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-advanced-accordion-script' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_items_section();
		$this->register_settings_section();

		$this->register_items_style();
		$this->register_head_style();
		$this->register_icon_style();
		$this->register_content_style();
		$this->register_tools_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	/**
	 * Items section.
	 *
	 * @return void
	 */
	protected function register_items_section() {
		$this->start_controls_section(
			'section_items',
			array( 'label' => __( 'Items', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'sibling_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'For text, FAQs and saved templates. To build each item freely out of widgets, use Elementor’s own Accordion.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Accordion item', 'elementor-animatepro' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'content_type',
			array(
				'label'   => __( 'Content', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => array(
					'text'     => __( 'Text', 'elementor-animatepro' ),
					'template' => __( 'Saved template', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'content',
			array(
				'label'     => __( 'Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::WYSIWYG,
				'default'   => '<p>' . __( 'Item content goes here.', 'elementor-animatepro' ) . '</p>',
				'dynamic'   => array( 'active' => true ),
				'condition' => array( 'content_type' => 'text' ),
			)
		);

		$repeater->add_control(
			'template',
			array(
				'label'       => __( 'Template', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => $this->eap_get_template_options(),
				'label_block' => true,
				'condition'   => array( 'content_type' => 'template' ),
			)
		);

		$repeater->add_control(
			'default_open',
			array(
				'label'        => __( 'Open By Default', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator'    => 'before',
				'description'  => __( 'In accordion mode only the first item marked open starts open.', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'title_icon',
			array(
				'label' => __( 'Title Icon', 'elementor-animatepro' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$repeater->add_control(
			'item_id',
			array(
				'label'       => __( 'Item ID', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'shipping',
				'description' => __( 'Link to /page/#shipping to open this item and scroll to it.', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'schema_answer',
			array(
				'label'       => __( 'Schema Answer', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'description' => __( 'Optional. Used in the FAQ structured data instead of the text.', 'elementor-animatepro' ),
				'condition'   => array( 'content_type' => 'text' ),
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
						'title'        => __( 'What is an accordion?', 'elementor-animatepro' ),
						'content'      => '<p>' . __( 'A stack of headings that each reveal their own content, so a long page stays short.', 'elementor-animatepro' ) . '</p>',
						'default_open' => 'yes',
					),
					array(
						'title'   => __( 'Can more than one item be open?', 'elementor-animatepro' ),
						'content' => '<p>' . __( 'Yes — switch Type to Toggle under Settings.', 'elementor-animatepro' ) . '</p>',
					),
					array(
						'title'   => __( 'Can I link straight to an item?', 'elementor-animatepro' ),
						'content' => '<p>' . __( 'Give it an Item ID, then link to #that-id. The item opens and scrolls into view.', 'elementor-animatepro' ) . '</p>',
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Settings section.
	 *
	 * @return void
	 */
	protected function register_settings_section() {
		$this->start_controls_section(
			'section_settings',
			array( 'label' => __( 'Settings', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'type',
			array(
				'label'   => __( 'Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'accordion',
				'options' => array(
					'accordion' => __( 'Accordion — one open at a time', 'elementor-animatepro' ),
					'toggle'    => __( 'Toggle — open as many as you like', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'keep_one_open',
			array(
				'label'        => __( 'Keep One Open', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Clicking the open item leaves it open, so one is always showing.', 'elementor-animatepro' ),
				'condition'    => array( 'type' => 'accordion' ),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'       => __( 'Title HTML Tag', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h3',
				'options'     => array(
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'span' => 'span',
				),
				'description' => __( 'A summary may hold a heading or inline text only, so div and p are not offered.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_icon',
			array(
				'label'        => __( 'Open / Close Icon', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'icon_closed',
			array(
				'label'     => __( 'Closed', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-plus',
					'library' => 'fa-solid',
				),
				'condition' => array( 'show_icon' => 'yes' ),
			)
		);

		$this->add_control(
			'icon_open',
			array(
				'label'     => __( 'Open', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-minus',
					'library' => 'fa-solid',
				),
				'condition' => array( 'show_icon' => 'yes' ),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'     => __( 'Icon Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'right',
				'toggle'    => false,
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
				'condition' => array( 'show_icon' => 'yes' ),
			)
		);

		$this->add_control(
			'animation',
			array(
				'label'     => __( 'Animation', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'slide',
				'separator' => 'before',
				'options'   => array(
					'none'  => __( 'None', 'elementor-animatepro' ),
					'slide' => __( 'Slide', 'elementor-animatepro' ),
					'fade'  => __( 'Slide and fade', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'      => __( 'Speed', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'ms' ),
				'range'      => array( 'ms' => array( 'min' => 100, 'max' => 1200, 'step' => 50 ) ),
				'default'    => array(
					'unit' => 'ms',
					'size' => 300,
				),
				'condition'  => array( 'animation!' => 'none' ),
			)
		);

		$this->add_control(
			'scroll_offset',
			array(
				'label'       => __( 'Scroll Offset', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 90,
				'min'         => 0,
				'max'         => 400,
				'separator'   => 'before',
				'description' => __( 'Pixels left above an item when a link jumps to it — the height of a fixed header.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'keep_in_view',
			array(
				'label'        => __( 'Keep Opened Item In View', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'When closing the item above pulls the page up, scroll the one just opened back into view.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'update_hash',
			array(
				'label'        => __( 'Put Item ID In The URL', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Opening an item makes the address linkable to it.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'expand_all',
			array(
				'label'        => __( 'Expand / Collapse All Buttons', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator'    => 'before',
				'condition'    => array( 'type' => 'toggle' ),
			)
		);

		$this->add_control(
			'expand_label',
			array(
				'label'     => __( 'Expand Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Expand all', 'elementor-animatepro' ),
				'condition' => array(
					'type'       => 'toggle',
					'expand_all' => 'yes',
				),
			)
		);

		$this->add_control(
			'collapse_label',
			array(
				'label'     => __( 'Collapse Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Collapse all', 'elementor-animatepro' ),
				'condition' => array(
					'type'       => 'toggle',
					'expand_all' => 'yes',
				),
			)
		);

		$this->add_control(
			'faq_schema',
			array(
				'label'        => __( 'FAQ Structured Data', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator'    => 'before',
				'description'  => __( 'Adds FAQPage JSON-LD built from the text items. Use it on one FAQ per page, and not alongside an SEO plugin’s FAQ block.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	/**
	 * Item style.
	 *
	 * @return void
	 */
	protected function register_items_style() {
		$this->start_controls_section(
			'style_items',
			array(
				'label' => __( 'Items', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => __( 'Space Between', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
					'em' => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-acc' => '--eap-acc-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-acc__item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .eap-acc__item',
			)
		);

		$this->add_responsive_control(
			'item_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-acc__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .eap-acc__item',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Heading style.
	 *
	 * @return void
	 */
	protected function register_head_style() {
		$this->start_controls_section(
			'style_head',
			array(
				'label' => __( 'Title', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-acc__title',
			)
		);

		$this->add_responsive_control(
			'head_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-acc__head' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'head_gap',
			array(
				'label'      => __( 'Gap Between Icon And Title', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-acc' => '--eap-acc-head-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'head_tabs' );

		foreach ( array(
			'normal' => array( __( 'Normal', 'elementor-animatepro' ), '{{WRAPPER}} .eap-acc__head' ),
			'hover'  => array( __( 'Hover', 'elementor-animatepro' ), '{{WRAPPER}} .eap-acc__head:hover' ),
			'open'   => array( __( 'Open', 'elementor-animatepro' ), '{{WRAPPER}} .eap-acc__item[open] > .eap-acc__head' ),
		) as $state => $def ) {
			$this->start_controls_tab(
				'head_tab_' . $state,
				array( 'label' => $def[0] )
			);

			$this->add_control(
				'head_color_' . $state,
				array(
					'label'     => __( 'Text Color', 'elementor-animatepro' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						$def[1] => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'head_bg_' . $state,
				array(
					'label'     => __( 'Background', 'elementor-animatepro' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						$def[1] => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'title_icon_heading',
			array(
				'label'     => __( 'Title Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'title_icon_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 60 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-acc' => '--eap-acc-title-icon: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'title_icon_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-acc__title-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Open/close icon style.
	 *
	 * @return void
	 */
	protected function register_icon_style() {
		$this->start_controls_section(
			'style_icon',
			array(
				'label'     => __( 'Open / Close Icon', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_icon' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 48 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-acc' => '--eap-acc-icon: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-acc__icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color_open',
			array(
				'label'     => __( 'Color When Open', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-acc__item[open] > .eap-acc__head .eap-acc__icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content style.
	 *
	 * @return void
	 */
	protected function register_content_style() {
		$this->start_controls_section(
			'style_content',
			array(
				'label' => __( 'Content', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .eap-acc__content',
			)
		);

		$this->add_control(
			'content_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-acc__content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'content_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-acc__panel' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-acc__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'divider_color',
			array(
				'label'     => __( 'Divider Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-acc' => '--eap-acc-divider: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'divider_width',
			array(
				'label'      => __( 'Divider Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 6 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-acc' => '--eap-acc-divider-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Expand/collapse-all style.
	 *
	 * @return void
	 */
	protected function register_tools_style() {
		$this->start_controls_section(
			'style_tools',
			array(
				'label'     => __( 'Expand / Collapse Buttons', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'type'       => 'toggle',
					'expand_all' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'tools_align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
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
				'selectors' => array(
					'{{WRAPPER}} .eap-acc__tools' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tool_typography',
				'selector' => '{{WRAPPER}} .eap-acc__tool',
			)
		);

		$this->add_control(
			'tool_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-acc__tool' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tool_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-acc__tool' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tool_border_color',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-acc__tool' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	/**
	 * Item IDs: the author's (slugged, de-duplicated) or generated.
	 *
	 * @param array[] $items Repeater rows.
	 * @return string[]
	 */
	protected function item_ids( $items ) {
		$ids  = array();
		$used = array();

		foreach ( $items as $index => $item ) {
			$base = sanitize_title( (string) ( $item['item_id'] ?? '' ) );
			if ( '' === $base ) {
				$base = 'eap-acc-' . $this->get_id() . '-' . ( $index + 1 );
			}

			$id = $base;
			$n  = 2;
			while ( isset( $used[ $id ] ) ) {
				$id = $base . '-' . $n;
				++$n;
			}

			$used[ $id ]     = true;
			$ids[ $index ] = $id;
		}

		return $ids;
	}

	/**
	 * Render.
	 *
	 * @return void
	 */
	protected function render() {
		$s      = $this->get_settings_for_display();
		$items  = isset( $s['items'] ) && is_array( $s['items'] ) ? array_values( $s['items'] ) : array();
		$editor = $this->eap_is_editor();

		if ( empty( $items ) ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-acc eap-acc--empty">' . esc_html__( 'Add items to get started.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$type      = 'toggle' === ( $s['type'] ?? 'accordion' ) ? 'toggle' : 'accordion';
		$anim      = in_array( $s['animation'] ?? 'slide', array( 'none', 'slide', 'fade' ), true ) ? $s['animation'] : 'slide';
		$icon_side = 'left' === ( $s['icon_position'] ?? 'right' ) ? 'left' : 'right';
		$show_icon = 'yes' === ( $s['show_icon'] ?? 'yes' );
		$tag       = in_array( $s['title_tag'] ?? 'h3', array( 'h2', 'h3', 'h4', 'h5', 'h6', 'span' ), true ) ? $s['title_tag'] : 'h3';
		$group     = 'eap-acc-' . $this->get_id();
		$ids       = $this->item_ids( $items );

		$config = array(
			'type'    => $type,
			'keepOne' => 'accordion' === $type && 'yes' === ( $s['keep_one_open'] ?? '' ),
			'anim'    => $anim,
			'speed'   => max( 0, (int) ( $s['speed']['size'] ?? 300 ) ),
			'offset'  => max( 0, (int) ( $s['scroll_offset'] ?? 90 ) ),
			'inView'  => 'yes' === ( $s['keep_in_view'] ?? '' ),
			'hash'    => 'yes' === ( $s['update_hash'] ?? '' ),
		);

		$this->add_render_attribute( 'wrapper', 'class', array( 'eap-widget', 'eap-acc', 'eap-acc--' . $type, 'eap-acc--icon-' . $icon_side ), true );
		$this->add_render_attribute( 'wrapper', 'data-eap-acc', wp_json_encode( $config ), true );

		$icons = '';
		if ( $show_icon && ( ! empty( $s['icon_closed']['value'] ) || ! empty( $s['icon_open']['value'] ) ) ) {
			ob_start();
			echo '<span class="eap-acc__icon" aria-hidden="true">';
			echo '<span class="eap-acc__icon-closed">';
			Icons_Manager::render_icon( $s['icon_closed'], array( 'aria-hidden' => 'true' ) );
			echo '</span><span class="eap-acc__icon-open">';
			Icons_Manager::render_icon( ! empty( $s['icon_open']['value'] ) ? $s['icon_open'] : $s['icon_closed'], array( 'aria-hidden' => 'true' ) );
			echo '</span></span>';
			$icons = ob_get_clean();
		}

		$opened = false;
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( 'toggle' === $type && 'yes' === ( $s['expand_all'] ?? '' ) ) : ?>
				<div class="eap-acc__tools">
					<button type="button" class="eap-acc__tool" data-eap-acc-expand><?php echo esc_html( $s['expand_label'] ?? __( 'Expand all', 'elementor-animatepro' ) ); ?></button>
					<button type="button" class="eap-acc__tool" data-eap-acc-collapse><?php echo esc_html( $s['collapse_label'] ?? __( 'Collapse all', 'elementor-animatepro' ) ); ?></button>
				</div>
			<?php endif; ?>

			<?php
			foreach ( $items as $index => $item ) :
				$open = 'yes' === ( $item['default_open'] ?? '' );
				if ( $open && 'accordion' === $type ) {
					// One open at a time, from the very first paint.
					$open   = ! $opened;
					$opened = true;
				}
				?>
				<details class="eap-acc__item" id="<?php echo esc_attr( $ids[ $index ] ); ?>"<?php echo 'accordion' === $type ? ' name="' . esc_attr( $group ) . '"' : ''; ?><?php echo $open ? ' open' : ''; ?>>
					<summary class="eap-acc__head">
						<?php if ( ! empty( $item['title_icon']['value'] ) ) : ?>
							<span class="eap-acc__title-icon" aria-hidden="true"><?php Icons_Manager::render_icon( $item['title_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
						<?php endif; ?>
						<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- whitelisted above. ?> class="eap-acc__title"><?php echo wp_kses_post( (string) ( $item['title'] ?? '' ) ); ?></<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo $icons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from Icons_Manager output. ?>
					</summary>
					<div class="eap-acc__panel">
						<div class="eap-acc__content">
							<?php $this->render_item_content( $item, $editor ); ?>
						</div>
					</div>
				</details>
			<?php endforeach; ?>
		</div>
		<?php
		if ( 'yes' === ( $s['faq_schema'] ?? '' ) && ! $editor ) {
			$this->render_faq_schema( $items );
		}
	}

	/**
	 * One item's content.
	 *
	 * @param array $item   Repeater row.
	 * @param bool  $editor In the editor.
	 * @return void
	 */
	protected function render_item_content( $item, $editor ) {
		if ( 'template' !== ( $item['content_type'] ?? 'text' ) ) {
			echo wp_kses_post( $this->parse_text_editor( (string) ( $item['content'] ?? '' ) ) );
			return;
		}

		$template_id = (int) ( $item['template'] ?? 0 );

		if ( ! $template_id ) {
			if ( $editor ) {
				echo '<p class="eap-acc__notice">' . esc_html__( 'Choose a saved template for this item.', 'elementor-animatepro' ) . '</p>';
			}
			return;
		}

		// A template that contains this accordion would render itself forever.
		if ( ! $this->eap_template_guard_enter( $template_id ) ) {
			if ( $editor ) {
				echo '<p class="eap-acc__notice">' . esc_html__( 'This template contains the accordion it is placed in, so it is not rendered here.', 'elementor-animatepro' ) . '</p>';
			}
			return;
		}

		$this->eap_enqueue_template_css( $template_id );
		echo $this->eap_render_template( $template_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor builder output.
		$this->eap_template_guard_leave( $template_id );
	}

	/**
	 * FAQPage JSON-LD from the text items.
	 *
	 * Slashes stay escaped (no JSON_UNESCAPED_SLASHES): that is what turns a
	 * "</script>" inside an answer into "<\/script>", which cannot end the tag.
	 *
	 * @param array[] $items Repeater rows.
	 * @return void
	 */
	protected function render_faq_schema( $items ) {
		$entities = array();

		foreach ( $items as $item ) {
			if ( 'template' === ( $item['content_type'] ?? 'text' ) ) {
				continue;
			}

			$question = trim( wp_strip_all_tags( (string) ( $item['title'] ?? '' ) ) );
			$answer   = trim( (string) ( $item['schema_answer'] ?? '' ) );
			if ( '' === $answer ) {
				$answer = trim( wp_strip_all_tags( $this->parse_text_editor( (string) ( $item['content'] ?? '' ) ) ) );
			}

			if ( '' === $question || '' === $answer ) {
				continue;
			}

			$entities[] = array(
				'@type'          => 'Question',
				'name'           => $question,
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( $answer ),
				),
			);
		}

		if ( empty( $entities ) ) {
			return;
		}

		echo '<script type="application/ld+json">' . wp_json_encode(
			array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => $entities,
			),
			JSON_UNESCAPED_UNICODE
		) . '</script>';
	}
}
