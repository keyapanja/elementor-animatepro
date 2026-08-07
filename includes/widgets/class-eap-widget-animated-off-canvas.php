<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;

/**
 * Animated Off-Canvas — a trigger button that opens a panel sliding in from a
 * screen edge, over a dimming overlay. The panel content is either a droppable
 * Elementor container (Nested Elements, edited live) or a saved Elementor
 * template. On the frontend the panel is portaled into the `.elementor` page
 * wrapper so its position:fixed truly covers the viewport while staying inside
 * the {{WRAPPER}} CSS scope; in the editor it renders open inline so the
 * droppable area stays editable.
 *
 * Only registered when Elementor's Nested Elements base class is available
 * (see EAP_Elementor::register_widgets()).
 */
class EAP_Widget_Animated_Off_Canvas extends Widget_Nested_Base {

	public function get_name() {
		return 'eap-animated-off-canvas';
	}

	public function get_title() {
		return __( 'Animated Off-Canvas', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-sidebar';
	}

	public function get_keywords() {
		return array( 'off-canvas', 'offcanvas', 'drawer', 'slide', 'panel', 'sidebar', 'menu', 'cart', 'nested' );
	}

	public function get_categories() {
		return array( 'eap-elements' );
	}

	public function get_style_depends(): array {
		return array( 'eap-core', 'eap-animated-off-canvas' );
	}

	public function get_script_depends(): array {
		return array( 'eap-core-runtime', 'eap-animated-off-canvas-script' );
	}

	public function show_in_panel(): bool {
		return Plugin::$instance->experiments->is_feature_active( 'nested-elements', true );
	}

	/**
	 * Mirror Elementor's own nested widgets so the child container view attaches
	 * (see Content Toggle for the rationale).
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/* -------------------------------------------------------------------------
	 * Nested element configuration — a single droppable panel body.
	 * ---------------------------------------------------------------------- */

	protected function get_default_children_elements() {
		return array(
			array(
				'elType'   => 'container',
				'settings' => array(
					'_title'        => __( 'Off-Canvas Content', 'elementor-animatepro' ),
					'content_width' => 'full',
				),
			),
		);
	}

	protected function get_default_repeater_title_setting_key() {
		return 'panel_title';
	}

	protected function get_default_children_title() {
		return esc_html__( 'Off-Canvas Content', 'elementor-animatepro' );
	}

	protected function get_default_children_placeholder_selector() {
		return '.eap-offcanvas__body';
	}

	/**
	 * Saved Elementor templates as id => title (template content source).
	 *
	 * @return array
	 */
	protected function get_template_options() {
		$options = array( '' => __( '— Select a Template —', 'elementor-animatepro' ) );

		$templates = get_posts(
			array(
				'post_type'      => 'elementor_library',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);

		foreach ( $templates as $template ) {
			$options[ $template->ID ] = $template->post_title ? $template->post_title : sprintf( '#%d', $template->ID );
		}

		return $options;
	}

	/* -------------------------------------------------------------------------
	 * Controls
	 * ---------------------------------------------------------------------- */

	protected function register_controls() {
		$this->register_content_section();
		$this->register_trigger_section();
		$this->register_panel_section();

		$this->register_trigger_style();
		$this->register_panel_style();
		$this->register_overlay_style();
		$this->register_close_style();
	}

	protected function register_content_section() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'content_source',
			array(
				'label'   => __( 'Content Source', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'nested',
				'options' => array(
					'nested'   => __( 'Droppable (build here)', 'elementor-animatepro' ),
					'template' => __( 'Elementor Template', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'oc_template',
			array(
				'label'       => __( 'Template', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->get_template_options(),
				'label_block' => true,
				'condition'   => array( 'content_source' => 'template' ),
			)
		);

		$this->add_control(
			'nested_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Drop any widgets into the panel area on the canvas. On the front-end it slides in from the chosen edge.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'content_source' => 'nested' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_trigger_section() {
		$this->start_controls_section(
			'section_trigger',
			array(
				'label' => __( 'Trigger', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'trigger_icon',
			array(
				'label'       => __( 'Icon', 'elementor-animatepro' ),
				'type'        => Controls_Manager::ICONS,
				'description' => __( 'Optional — defaults to a hamburger.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'trigger_label',
			array(
				'label'       => __( 'Label', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Menu', 'elementor-animatepro' ),
				'placeholder' => __( 'Optional label', 'elementor-animatepro' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'trigger_icon_position',
			array(
				'label'     => __( 'Icon Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'before',
				'options'   => array(
					'before' => __( 'Before Label', 'elementor-animatepro' ),
					'after'  => __( 'After Label', 'elementor-animatepro' ),
				),
				'condition' => array( 'trigger_label!' => '' ),
			)
		);

		$this->add_responsive_control(
			'trigger_align',
			array(
				'label'                => __( 'Alignment', 'elementor-animatepro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'left',
				'options'              => array(
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
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
				'selectors'            => array(
					'{{WRAPPER}} .eap-offcanvas__trigger-wrap' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'trigger_full_width',
			array(
				'label'        => __( 'Full Width', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'selectors'    => array(
					'{{WRAPPER}} .eap-offcanvas__trigger' => 'width: 100%; justify-content: center;',
				),
			)
		);

		$this->add_control(
			'behaviour_heading',
			array(
				'label'     => __( 'Behaviour', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'open_on_load',
			array(
				'label'        => __( 'Open On Page Load', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'hash_id',
			array(
				'label'       => __( 'Open Via URL Hash', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'e.g. cart', 'elementor-animatepro' ),
				'description' => __( 'Add #your-id to any link or the page URL to open this panel.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_panel_section() {
		$this->start_controls_section(
			'section_panel',
			array(
				'label' => __( 'Panel', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'slide_from',
			array(
				'label'   => __( 'Slide From', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'right',
				'options' => array(
					'left'   => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right'  => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-right',
					),
					'top'    => array(
						'title' => __( 'Top', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-top',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'toggle'  => false,
			)
		);

		$this->add_responsive_control(
			'panel_width',
			array(
				'label'      => __( 'Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw', '%' ),
				'range'      => array(
					'px' => array( 'min' => 200, 'max' => 900 ),
					'vw' => array( 'min' => 20, 'max' => 100 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'size' => 360, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-offcanvas__panel' => '--eap-oc-width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'slide_from' => array( 'left', 'right' ) ),
			)
		);

		$this->add_responsive_control(
			'panel_height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh', '%' ),
				'range'      => array(
					'px' => array( 'min' => 160, 'max' => 900 ),
					'vh' => array( 'min' => 20, 'max' => 100 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'size' => 340, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-offcanvas__panel' => '--eap-oc-height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'slide_from' => array( 'top', 'bottom' ) ),
			)
		);

		// NOTE: do not name this control `animation` — Elementor reserves
		// `animation`/`_animation` for entrance animations and would stamp the
		// whole widget with `elementor-invisible` (permanently hidden).
		$this->add_control(
			'panel_animation',
			array(
				'label'   => __( 'Animation', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => array(
					'slide' => __( 'Slide', 'elementor-animatepro' ),
					'fade'  => __( 'Fade', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'anim_duration',
			array(
				'label'   => __( 'Duration (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 1500,
				'step'    => 10,
				'default' => 350,
			)
		);

		$this->add_control(
			'overlay',
			array(
				'label'        => __( 'Overlay', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'overlay_close',
			array(
				'label'        => __( 'Click Overlay To Close', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'overlay' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style
	 * ---------------------------------------------------------------------- */

	protected function register_trigger_style() {
		$this->start_controls_section(
			'section_trigger_style',
			array(
				'label' => __( 'Trigger', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'trigger_typography',
				'selector' => '{{WRAPPER}} .eap-offcanvas__trigger',
			)
		);

		$this->add_responsive_control(
			'trigger_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 60 ) ),
				'default'    => array( 'size' => 22, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-offcanvas__trigger-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-offcanvas__trigger-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'trigger_icon_gap',
			array(
				'label'      => __( 'Icon Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-offcanvas__trigger' => 'gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'trigger_label!' => '' ),
			)
		);

		$this->start_controls_tabs( 'trigger_state_tabs' );

		$this->start_controls_tab( 'trigger_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );
		$this->add_control(
			'trigger_color',
			array(
				'label'     => __( 'Text / Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-offcanvas__trigger' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'trigger_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-offcanvas__trigger' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'trigger_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );
		$this->add_control(
			'trigger_color_hover',
			array(
				'label'     => __( 'Text / Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-offcanvas__trigger:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'trigger_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4338ca',
				'selectors' => array(
					'{{WRAPPER}} .eap-offcanvas__trigger:hover' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'trigger_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 12,
					'right'    => 18,
					'bottom'   => 12,
					'left'     => 18,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-offcanvas__trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->add_responsive_control(
			'trigger_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-offcanvas__trigger' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'trigger_border',
				'selector' => '{{WRAPPER}} .eap-offcanvas__trigger',
			)
		);

		$this->end_controls_section();
	}

	protected function register_panel_style() {
		$this->start_controls_section(
			'section_panel_style',
			array(
				'label' => __( 'Panel', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'panel_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-offcanvas__panel' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'panel_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => 40,
					'right'    => 32,
					'bottom'   => 32,
					'left'     => 32,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-offcanvas__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'           => 'panel_shadow',
				'selector'       => '{{WRAPPER}} .eap-offcanvas__panel',
				'fields_options' => array(
					'box_shadow_type' => array( 'default' => 'yes' ),
					'box_shadow'      => array(
						'default' => array(
							'horizontal' => 0,
							'vertical'   => 0,
							'blur'       => 50,
							'spread'     => 0,
							'color'      => 'rgba(0,0,0,0.25)',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_overlay_style() {
		$this->start_controls_section(
			'section_overlay_style',
			array(
				'label'     => __( 'Overlay', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'overlay' => 'yes' ),
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(17,24,39,0.5)',
				'selectors' => array(
					'{{WRAPPER}} .eap-offcanvas__overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_blur',
			array(
				'label'      => __( 'Backdrop Blur', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 20 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-offcanvas__overlay' => 'backdrop-filter: blur({{SIZE}}{{UNIT}}); -webkit-backdrop-filter: blur({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_close_style() {
		$this->start_controls_section(
			'section_close_style',
			array(
				'label' => __( 'Close Button', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'close_placement',
			array(
				'label'   => __( 'Placement', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top-right',
				'options' => array(
					'top-right' => __( 'Top Right', 'elementor-animatepro' ),
					'top-left'  => __( 'Top Left', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'close_icon',
			array(
				'label'       => __( 'Icon', 'elementor-animatepro' ),
				'type'        => Controls_Manager::ICONS,
				'description' => __( 'Optional — defaults to ✕.', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'close_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 12, 'max' => 48 ) ),
				'default'    => array( 'size' => 20, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-offcanvas__close' => '--eap-oc-close-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'close_box',
			array(
				'label'      => __( 'Box Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 80 ) ),
				'default'    => array( 'size' => 40, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-offcanvas__close' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'close_tabs' );

		$this->start_controls_tab( 'close_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );
		$this->add_control(
			'close_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1f2937',
				'selectors' => array(
					'{{WRAPPER}} .eap-offcanvas__close' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'close_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-offcanvas__close' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'close_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );
		$this->add_control(
			'close_color_hover',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-offcanvas__close:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'close_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-offcanvas__close:hover' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'close_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 50 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-offcanvas__close' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'close_offset',
			array(
				'label'      => __( 'Distance From Edge', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-offcanvas__panel' => '--eap-oc-close-offset: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Frontend render
	 * ---------------------------------------------------------------------- */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$source   = ! empty( $settings['content_source'] ) ? $settings['content_source'] : 'nested';
		$slide    = ! empty( $settings['slide_from'] ) ? $settings['slide_from'] : 'right';
		$anim     = ! empty( $settings['panel_animation'] ) ? $settings['panel_animation'] : 'slide';
		$overlay  = 'yes' === ( $settings['overlay'] ?? 'yes' );
		$duration = isset( $settings['anim_duration'] ) && '' !== $settings['anim_duration'] ? (int) $settings['anim_duration'] : 350;
		$close_pl = ! empty( $settings['close_placement'] ) ? $settings['close_placement'] : 'top-right';
		$widget_id = $this->get_id();
		$panel_id  = 'eap-oc-panel-' . $widget_id;
		$hash      = isset( $settings['hash_id'] ) ? preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $settings['hash_id'] ) : '';

		$stage_classes = array(
			'eap-offcanvas__stage',
			'eap-offcanvas__stage--from-' . $slide,
			'eap-offcanvas__stage--anim-' . $anim,
			'eap-offcanvas__stage--close-' . $close_pl,
		);
		if ( $overlay ) {
			$stage_classes[] = 'eap-offcanvas__stage--has-overlay';
		}

		$this->add_render_attribute(
			'root',
			array(
				'class'               => array( 'eap-widget', 'eap-offcanvas' ),
				'data-eap-off-canvas' => '',
				'data-slide'          => $slide,
				'data-overlay-close'  => ( $overlay && 'yes' === ( $settings['overlay_close'] ?? 'yes' ) ) ? '1' : '0',
				'data-open-load'      => 'yes' === ( $settings['open_on_load'] ?? '' ) ? '1' : '0',
			)
		);
		if ( '' !== $hash ) {
			$this->add_render_attribute( 'root', 'data-hash', $hash );
		}

		$this->add_render_attribute(
			'stage',
			array(
				'class' => $stage_classes,
				'style' => '--eap-oc-duration: ' . $duration . 'ms;',
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'root' ); ?>>
			<div class="eap-offcanvas__trigger-wrap">
				<button type="button" class="eap-offcanvas__trigger" aria-haspopup="dialog" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>">
					<?php $this->render_trigger_inner( $settings ); ?>
				</button>
			</div>
			<div <?php $this->print_render_attribute_string( 'stage' ); ?>>
				<?php if ( $overlay ) : ?>
					<div class="eap-offcanvas__overlay" aria-hidden="true"></div>
				<?php endif; ?>
				<div class="eap-offcanvas__panel" id="<?php echo esc_attr( $panel_id ); ?>" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( $settings['trigger_label'] ? $settings['trigger_label'] : __( 'Off-canvas panel', 'elementor-animatepro' ) ); ?>" tabindex="-1">
					<button type="button" class="eap-offcanvas__close" aria-label="<?php esc_attr_e( 'Close', 'elementor-animatepro' ); ?>"><?php echo $this->get_close_button_icon( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
					<div class="eap-offcanvas__body">
						<?php $this->render_panel_body( $settings, $source ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render_trigger_inner( $settings ) {
		$label    = isset( $settings['trigger_label'] ) ? $settings['trigger_label'] : '';
		$icon_pos = ! empty( $settings['trigger_icon_position'] ) ? $settings['trigger_icon_position'] : 'before';
		$icon     = '<span class="eap-offcanvas__trigger-icon" aria-hidden="true">' . $this->get_trigger_icon( $settings ) . '</span>';
		$text     = '' !== $label ? '<span class="eap-offcanvas__trigger-label">' . esc_html( $label ) . '</span>' : '';

		if ( 'after' === $icon_pos ) {
			echo $text . $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			echo $icon . $text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	protected function render_panel_body( $settings, $source ) {
		if ( 'template' === $source ) {
			$tid = isset( $settings['oc_template'] ) ? absint( $settings['oc_template'] ) : 0;
			if ( $tid && class_exists( '\Elementor\Plugin' ) ) {
				echo Plugin::$instance->frontend->get_builder_content_for_display( $tid, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			return;
		}

		// Nested: render the single droppable child container.
		$children = $this->get_children();
		if ( isset( $children[0] ) ) {
			$children[0]->print_element();
		}
	}

	protected function get_trigger_icon( $settings ) {
		if ( ! empty( $settings['trigger_icon']['value'] ) ) {
			ob_start();
			Icons_Manager::render_icon( $settings['trigger_icon'], array( 'aria-hidden' => 'true' ) );
			return ob_get_clean();
		}
		return '<span class="eap-offcanvas__bars"><span></span><span></span><span></span></span>';
	}

	protected function get_close_button_icon( $settings ) {
		if ( ! empty( $settings['close_icon']['value'] ) ) {
			ob_start();
			Icons_Manager::render_icon( $settings['close_icon'], array( 'aria-hidden' => 'true' ) );
			return ob_get_clean();
		}
		return '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 5l10 10M15 5 5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
	}

	/* -------------------------------------------------------------------------
	 * Editor (client-side) template — renders the trigger and, for the droppable
	 * source, an inline-open panel whose `.eap-offcanvas__body` is the child
	 * placeholder the nested view injects into.
	 * ---------------------------------------------------------------------- */

	protected function content_template() {
		?>
		<#
		const source = settings.content_source || 'nested';
		const slide = settings.slide_from || 'right';
		const anim = settings.panel_animation || 'slide';
		const closePl = settings.close_placement || 'top-right';
		const overlay = settings.overlay === 'yes';
		const overlayClose = ( overlay && settings.overlay_close === 'yes' ) ? '1' : '0';
		const dur = settings.anim_duration || 350;
		const label = settings.trigger_label || '';
		const iconPos = settings.trigger_icon_position || 'before';
		let stageClass = 'eap-offcanvas__stage eap-offcanvas__stage--from-' + slide + ' eap-offcanvas__stage--anim-' + anim + ' eap-offcanvas__stage--close-' + closePl;
		if ( overlay ) { stageClass += ' eap-offcanvas__stage--has-overlay'; }
		#>
		<div class="eap-widget eap-offcanvas" data-eap-off-canvas data-slide="{{ slide }}" data-overlay-close="{{ overlayClose }}" data-open-load="0">
			<div class="eap-offcanvas__trigger-wrap">
				<button type="button" class="eap-offcanvas__trigger" aria-haspopup="dialog" aria-expanded="false">
					<# if ( iconPos === 'after' && label ) { #>
						<span class="eap-offcanvas__trigger-label">{{{ label }}}</span>
						<span class="eap-offcanvas__trigger-icon"><span class="eap-offcanvas__bars"><span></span><span></span><span></span></span></span>
					<# } else { #>
						<span class="eap-offcanvas__trigger-icon"><span class="eap-offcanvas__bars"><span></span><span></span><span></span></span></span>
						<# if ( label ) { #><span class="eap-offcanvas__trigger-label">{{{ label }}}</span><# } #>
					<# } #>
				</button>
			</div>
			<# if ( source === 'template' ) { #>
				<div class="eap-offcanvas__editor-note"><?php echo esc_html__( 'Template mode — the selected Elementor template renders in the panel on the front-end. Click the trigger to preview it.', 'elementor-animatepro' ); ?></div>
			<# } else { #>
				<div class="{{ stageClass }}" style="--eap-oc-duration: {{ dur }}ms;">
					<# if ( overlay ) { #><div class="eap-offcanvas__overlay" aria-hidden="true"></div><# } #>
					<div class="eap-offcanvas__panel" role="dialog" aria-modal="true" tabindex="-1">
						<button type="button" class="eap-offcanvas__close" aria-label="<?php esc_attr_e( 'Close', 'elementor-animatepro' ); ?>"><svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M5 5l10 10M15 5 5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button>
						<div class="eap-offcanvas__body"></div>
					</div>
				</div>
			<# } #>
		</div>
		<?php
	}
}
