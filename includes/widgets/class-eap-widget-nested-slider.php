<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;
use Elementor\Modules\NestedElements\Controls\Control_Nested_Repeater;

/**
 * Nested Slider — a slider whose slides are Elementor containers.
 *
 * Every slide is a real droppable container, so a slide holds anything: a
 * hero, a card grid, a form, another widget of ours. The other sliders in the
 * plugin render fixed slide markup from a repeater; this one renders whatever
 * the author builds.
 *
 * TWO LAYOUTS FROM ONE MARKUP:
 *
 * - On the page, Swiper (Elementor's bundled Swiper 8) runs the slider.
 * - In the editor, Swiper does NOT run. Its transforms and loop clones would
 *   sit inside Elementor's editor views and duplicate droppable containers;
 *   instead the slides are laid side by side in a scroll-snap strip, each one
 *   visible and droppable. The same strip is what a visitor gets if Swiper
 *   never loads, so no slide is ever unreachable.
 *
 * LOOP CLONES ARE RE-INITIALISED. Swiper 8's loop clones slides with
 * cloneNode(), which copies our `data-eap-*-bound` flags too — so a widget of
 * ours inside a clone would think it was already set up and stay dead. The
 * script clears those flags on each clone and runs our modules and
 * Elementor's element handlers over it.
 *
 * Only loaded when Elementor's Nested Elements base class exists (see
 * EAP_Elementor::register_widgets()). The editor needs the element type this
 * widget registers in nested-slider-editor.js — see content-toggle-editor.js
 * for why that registration's timing matters.
 */
class EAP_Widget_Nested_Slider extends Widget_Nested_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-nested-slider';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Nested Slider', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-slider-push';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'slider', 'carousel', 'nested', 'slides', 'swiper', 'container', 'slideshow' );
	}

	/**
	 * Category.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'eap-elements' );
	}

	/**
	 * Styles — including Swiper's, which Elementor registers but only its own
	 * carousels ask for.
	 *
	 * @return string[]
	 */
	public function get_style_depends(): array {
		$deps = array( 'eap-core', 'eap-nested-slider' );

		if ( wp_style_is( 'e-swiper', 'registered' ) ) {
			$deps[] = 'e-swiper';
		} elseif ( wp_style_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}

		return $deps;
	}

	/**
	 * Scripts — Swiper before ours.
	 *
	 * @return string[]
	 */
	public function get_script_depends(): array {
		$deps = array( 'eap-core-runtime' );

		if ( wp_script_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}

		$deps[] = 'eap-nested-slider-script';

		return $deps;
	}

	/**
	 * Only offered while Nested Elements is on.
	 *
	 * @return bool
	 */
	public function show_in_panel(): bool {
		return Plugin::$instance->experiments->is_feature_active( 'nested-elements', true );
	}

	/**
	 * Mirror the optimized-markup experiment, as Elementor's own nested
	 * widgets do (see Content Toggle).
	 *
	 * @return bool
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/* =====================================================================
	 * NESTED CONFIGURATION
	 * ================================================================== */

	/**
	 * One slide container.
	 *
	 * @param int $index 1-based.
	 * @return array
	 */
	protected function slide_container( int $index ) {
		return array(
			'elType'   => 'container',
			'settings' => array(
				/* translators: %d: slide number. */
				'_title'        => sprintf( __( 'Slide #%d', 'elementor-animatepro' ), $index ),
				'content_width' => 'full',
			),
		);
	}

	/**
	 * Three slides to start.
	 *
	 * @return array
	 */
	protected function get_default_children_elements() {
		return array(
			$this->slide_container( 1 ),
			$this->slide_container( 2 ),
			$this->slide_container( 3 ),
		);
	}

	/**
	 * Repeater field that names each slide in the navigator.
	 *
	 * @return string
	 */
	protected function get_default_repeater_title_setting_key() {
		return 'slide_title';
	}

	/**
	 * Navigator title for new slides.
	 *
	 * @return string
	 */
	protected function get_default_children_title() {
		/* translators: %d: slide number. */
		return esc_html__( 'Slide #%d', 'elementor-animatepro' );
	}

	/**
	 * Where the editor puts the slide containers: the track itself, so they
	 * are its direct children exactly as on the page.
	 *
	 * @return string
	 */
	protected function get_default_children_placeholder_selector() {
		return '.eap-ns__track';
	}

	/* =====================================================================
	 * CONTROLS
	 * ================================================================== */

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_slides_section();
		$this->register_layout_section();
		$this->register_behaviour_section();
		$this->register_navigation_section();

		$this->register_arrows_style();
		$this->register_pagination_style();
	}

	/**
	 * Slides section.
	 *
	 * @return void
	 */
	protected function register_slides_section() {
		$this->start_controls_section(
			'section_slides',
			array( 'label' => __( 'Slides', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'slides_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Each slide is a container — drop anything into it. While you edit, the slides sit side by side in a strip you can scroll; the slider runs on the page.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'slide_title',
			array(
				'label'       => __( 'Name', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Slide', 'elementor-animatepro' ),
				'label_block' => true,
				'description' => __( 'Shown in the Navigator.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'slides',
			array(
				'label'       => __( 'Slides', 'elementor-animatepro' ),
				'type'        => Control_Nested_Repeater::CONTROL_TYPE,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ slide_title }}}',
				'default'     => array(
					array( 'slide_title' => __( 'Slide #1', 'elementor-animatepro' ) ),
					array( 'slide_title' => __( 'Slide #2', 'elementor-animatepro' ) ),
					array( 'slide_title' => __( 'Slide #3', 'elementor-animatepro' ) ),
				),
			)
		);

		$this->add_control(
			'carousel_label',
			array(
				'label'       => __( 'Accessible Name', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Slides', 'elementor-animatepro' ),
				'description' => __( 'What a screen reader announces for this carousel, e.g. "Customer stories".', 'elementor-animatepro' ),
				'separator'   => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Layout section.
	 *
	 * @return void
	 */
	protected function register_layout_section() {
		$this->start_controls_section(
			'section_layout',
			array( 'label' => __( 'Layout', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'effect',
			array(
				'label'       => __( 'Effect', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'slide',
				'options'     => array(
					'slide' => __( 'Slide', 'elementor-animatepro' ),
					'fade'  => __( 'Fade', 'elementor-animatepro' ),
				),
				'description' => __( 'Fade shows one slide at a time.', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'slides_per_view',
			array(
				'label'          => __( 'Slides Per View', 'elementor-animatepro' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 1,
				'tablet_default' => 1,
				'mobile_default' => 1,
				'min'            => 1,
				'max'            => 6,
				'selectors'      => array(
					'{{WRAPPER}} .eap-ns' => '--eap-ns-per-view: {{VALUE}};',
				),
				'condition'      => array( 'effect' => 'slide' ),
			)
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			array(
				'label'     => __( 'Slides Per Step', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 6,
				'condition' => array( 'effect' => 'slide' ),
			)
		);

		$this->add_responsive_control(
			'space_between',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ns' => '--eap-ns-gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'effect' => 'slide' ),
			)
		);

		$this->add_control(
			'centered_slides',
			array(
				'label'        => __( 'Centre Active Slide', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'condition'    => array( 'effect' => 'slide' ),
			)
		);

		$this->add_control(
			'auto_height',
			array(
				'label'        => __( 'Auto Height', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'The slider resizes to each slide. Best with one slide per view.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Behaviour section.
	 *
	 * @return void
	 */
	protected function register_behaviour_section() {
		$this->start_controls_section(
			'section_behaviour',
			array( 'label' => __( 'Behaviour', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'loop',
			array(
				'label'        => __( 'Loop', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Loop copies slides at each end. Widgets inside the copies are started again, so interactive content keeps working.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => __( 'Transition Speed (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 500,
				'min'     => 0,
				'max'     => 3000,
				'step'    => 50,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator'    => 'before',
				'description'  => __( 'Off for visitors who ask their system for reduced motion.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => __( 'Delay (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'min'       => 1000,
				'max'       => 20000,
				'step'      => 250,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => __( 'Pause On Hover', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'pause_on_interaction',
			array(
				'label'        => __( 'Stop After Interaction', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Once a visitor swipes or clicks, autoplay stops for good.', 'elementor-animatepro' ),
				'condition'    => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'pause_button',
			array(
				'label'        => __( 'Pause Button', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Moving content that lasts more than five seconds needs a way to stop it (WCAG 2.2.2).', 'elementor-animatepro' ),
				'condition'    => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'grab_cursor',
			array(
				'label'        => __( 'Grab Cursor', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'keyboard',
			array(
				'label'        => __( 'Arrow Keys', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Left and right arrows move the slider while it is on screen.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Navigation section.
	 *
	 * @return void
	 */
	protected function register_navigation_section() {
		$this->start_controls_section(
			'section_navigation',
			array( 'label' => __( 'Navigation', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'arrows',
			array(
				'label'        => __( 'Arrows', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'arrow_prev',
			array(
				'label'     => __( 'Previous Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
				'condition' => array( 'arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'arrow_next',
			array(
				'label'     => __( 'Next Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				),
				'condition' => array( 'arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'arrows_position',
			array(
				'label'     => __( 'Arrow Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'inside',
				'options'   => array(
					'inside'  => __( 'Over the slides', 'elementor-animatepro' ),
					'outside' => __( 'Beside the slides', 'elementor-animatepro' ),
				),
				'condition' => array( 'arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'pagination',
			array(
				'label'     => __( 'Pagination', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bullets',
				'separator' => 'before',
				'options'   => array(
					'bullets'     => __( 'Dots', 'elementor-animatepro' ),
					'fraction'    => __( 'Fraction (2 / 5)', 'elementor-animatepro' ),
					'progressbar' => __( 'Progress bar', 'elementor-animatepro' ),
					'none'        => __( 'None', 'elementor-animatepro' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Arrow style.
	 *
	 * @return void
	 */
	protected function register_arrows_style() {
		$this->start_controls_section(
			'style_arrows',
			array(
				'label'     => __( 'Arrows', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'arrows' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'arrow_box',
			array(
				'label'      => __( 'Button Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 90 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ns' => '--eap-ns-arrow-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_icon',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 48 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ns' => '--eap-ns-arrow-icon: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_offset',
			array(
				'label'      => __( 'Distance From Edge', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ns' => '--eap-ns-arrow-offset: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'arrow_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 50 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ns__arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'arrow_tabs' );

		foreach ( array(
			'normal' => array( __( 'Normal', 'elementor-animatepro' ), '{{WRAPPER}} .eap-ns__arrow' ),
			'hover'  => array( __( 'Hover', 'elementor-animatepro' ), '{{WRAPPER}} .eap-ns__arrow:hover, {{WRAPPER}} .eap-ns__arrow:focus-visible' ),
		) as $state => $def ) {
			$this->start_controls_tab( 'arrow_tab_' . $state, array( 'label' => $def[0] ) );

			$this->add_control(
				'arrow_color_' . $state,
				array(
					'label'     => __( 'Color', 'elementor-animatepro' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						$def[1] => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'arrow_bg_' . $state,
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

		$this->end_controls_section();
	}

	/**
	 * Pagination style.
	 *
	 * @return void
	 */
	protected function register_pagination_style() {
		$this->start_controls_section(
			'style_pagination',
			array(
				'label'     => __( 'Pagination', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'pagination!' => 'none' ),
			)
		);

		$this->add_responsive_control(
			'pagination_spacing',
			array(
				'label'      => __( 'Space Above', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ns' => '--eap-ns-pagination-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bullet_size',
			array(
				'label'      => __( 'Dot Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 4, 'max' => 24 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ns' => '--eap-ns-bullet: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'pagination' => 'bullets' ),
			)
		);

		$this->add_control(
			'bullet_gap',
			array(
				'label'      => __( 'Dot Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ns' => '--eap-ns-bullet-gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'pagination' => 'bullets' ),
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-ns' => '--eap-ns-dot: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_active',
			array(
				'label'     => __( 'Active Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-ns' => '--eap-ns-dot-active: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'progress_height',
			array(
				'label'      => __( 'Bar Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 1, 'max' => 16 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ns' => '--eap-ns-progress: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'pagination' => 'progressbar' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'fraction_typography',
				'selector'  => '{{WRAPPER}} .eap-ns__pagination.swiper-pagination-fraction',
				'condition' => array( 'pagination' => 'fraction' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	/**
	 * A responsive value per device, each falling back to the next larger.
	 *
	 * @param array  $s         Settings.
	 * @param string $key       Base key.
	 * @param mixed  $default   Desktop default.
	 * @param bool   $is_slider SLIDER control (value lives in ['size']).
	 * @return array {desktop, tablet, mobile}
	 */
	protected function responsive_value( $s, $key, $default, $is_slider = false ) {
		$get = static function ( $k ) use ( $s, $is_slider ) {
			if ( ! isset( $s[ $k ] ) ) {
				return null;
			}
			$v = $is_slider ? ( is_array( $s[ $k ] ) ? ( $s[ $k ]['size'] ?? '' ) : '' ) : $s[ $k ];
			return ( '' === $v || null === $v ) ? null : $v;
		};

		$desktop = $get( $key ) ?? $default;
		$tablet  = $get( $key . '_tablet' ) ?? $desktop;
		$mobile  = $get( $key . '_mobile' ) ?? $tablet;

		return array(
			'desktop' => $desktop,
			'tablet'  => $tablet,
			'mobile'  => $mobile,
		);
	}

	/**
	 * Slider configuration for the script.
	 *
	 * @param array $s     Settings.
	 * @param int   $count Slide count.
	 * @return array
	 */
	protected function build_config( $s, $count ) {
		$fade = 'fade' === ( $s['effect'] ?? 'slide' );

		$clamp = static function ( $values, $min, $max ) {
			return array_map(
				static function ( $v ) use ( $min, $max ) {
					return max( $min, min( $max, (int) $v ) );
				},
				$values
			);
		};

		$per   = $fade ? array( 'desktop' => 1, 'tablet' => 1, 'mobile' => 1 ) : $clamp( $this->responsive_value( $s, 'slides_per_view', 1 ), 1, 6 );
		$group = $fade ? array( 'desktop' => 1, 'tablet' => 1, 'mobile' => 1 ) : $clamp( $this->responsive_value( $s, 'slides_to_scroll', 1 ), 1, 6 );
		$gap   = $fade ? array( 'desktop' => 0, 'tablet' => 0, 'mobile' => 0 ) : $clamp( $this->responsive_value( $s, 'space_between', 20, true ), 0, 200 );

		// Swiper's breakpoints are min-widths; Elementor's are max-widths.
		$mobile_max = 767;
		$tablet_max = 1024;
		if ( isset( Plugin::$instance->breakpoints ) ) {
			$active = Plugin::$instance->breakpoints->get_active_breakpoints();
			if ( isset( $active['mobile'] ) ) {
				$mobile_max = (int) $active['mobile']->get_value();
			}
			if ( isset( $active['tablet'] ) ) {
				$tablet_max = (int) $active['tablet']->get_value();
			}
		}

		// Loop needs more slides than fit at once, at every size.
		$loop = 'yes' === ( $s['loop'] ?? '' ) && $count > max( $per );

		return array(
			'perView'          => $per,
			'group'            => $group,
			'gap'              => $gap,
			'bp'               => array(
				'tablet'  => $mobile_max + 1,
				'desktop' => $tablet_max + 1,
			),
			'effect'           => $fade ? 'fade' : 'slide',
			'centered'         => ! $fade && 'yes' === ( $s['centered_slides'] ?? '' ),
			'autoHeight'       => 'yes' === ( $s['auto_height'] ?? '' ),
			'loop'             => $loop,
			'speed'            => max( 0, (int) ( $s['speed'] ?? 500 ) ),
			'autoplay'         => 'yes' === ( $s['autoplay'] ?? '' ),
			'delay'            => max( 1000, (int) ( $s['autoplay_delay'] ?? 5000 ) ),
			'pauseHover'       => 'yes' === ( $s['pause_on_hover'] ?? 'yes' ),
			'pauseInteraction' => 'yes' === ( $s['pause_on_interaction'] ?? 'yes' ),
			'grab'             => 'yes' === ( $s['grab_cursor'] ?? 'yes' ),
			'keyboard'         => 'yes' === ( $s['keyboard'] ?? 'yes' ),
			'pagination'       => in_array( $s['pagination'] ?? 'bullets', array( 'bullets', 'fraction', 'progressbar', 'none' ), true ) ? $s['pagination'] : 'bullets',
			'i18n'             => array(
				'prev'  => __( 'Previous slide', 'elementor-animatepro' ),
				'next'  => __( 'Next slide', 'elementor-animatepro' ),
				/* translators: {{index}} is replaced by the slide number. Keep the braces. */
				'goTo'  => __( 'Go to slide {{index}}', 'elementor-animatepro' ),
				/* translators: {{index}} and {{slidesLength}} are replaced. Keep the braces. */
				'slide' => __( '{{index}} of {{slidesLength}}', 'elementor-animatepro' ),
				'pause' => __( 'Pause the slider', 'elementor-animatepro' ),
				'play'  => __( 'Play the slider', 'elementor-animatepro' ),
			),
		);
	}

	/**
	 * Render.
	 *
	 * @return void
	 */
	protected function render() {
		$s      = $this->get_settings_for_display();
		$slides = ! empty( $s['slides'] ) && is_array( $s['slides'] ) ? array_values( $s['slides'] ) : array();
		$count  = count( $slides );

		if ( 0 === $count ) {
			return;
		}

		$config   = $this->build_config( $s, $count );
		$arrows   = 'yes' === ( $s['arrows'] ?? 'yes' );
		$outside  = $arrows && 'outside' === ( $s['arrows_position'] ?? 'inside' );
		$autoplay = $config['autoplay'];

		$this->add_render_attribute(
			'slider',
			array(
				'class'                => array( 'eap-widget', 'eap-ns', $outside ? 'eap-ns--arrows-outside' : 'eap-ns--arrows-inside' ),
				'data-eap-ns'          => wp_json_encode( $config ),
				'role'                 => 'region',
				'aria-roledescription' => __( 'carousel', 'elementor-animatepro' ),
				'aria-label'           => (string) ( $s['carousel_label'] ?? __( 'Slides', 'elementor-animatepro' ) ),
			),
			null,
			true
		);
		?>
		<div <?php $this->print_render_attribute_string( 'slider' ); ?>>
			<div class="eap-ns__stage">
				<div class="eap-ns__viewport swiper">
					<div class="eap-ns__track swiper-wrapper">
						<?php
						foreach ( $slides as $index => $slide ) {
							$this->print_child(
								$index,
								array(
									/* translators: 1: slide number, 2: slide count. */
									'label' => sprintf( __( '%1$d of %2$d', 'elementor-animatepro' ), $index + 1, $count ),
								)
							);
						}
						?>
					</div>
				</div>

				<?php if ( $arrows ) : ?>
					<button type="button" class="eap-ns__arrow eap-ns__arrow--prev" aria-label="<?php echo esc_attr( $config['i18n']['prev'] ); ?>">
						<?php $this->render_arrow_icon( $s['arrow_prev'] ?? array(), 'M15 5l-7 7 7 7' ); ?>
					</button>
					<button type="button" class="eap-ns__arrow eap-ns__arrow--next" aria-label="<?php echo esc_attr( $config['i18n']['next'] ); ?>">
						<?php $this->render_arrow_icon( $s['arrow_next'] ?? array(), 'M9 5l7 7-7 7' ); ?>
					</button>
				<?php endif; ?>

				<?php if ( $autoplay && 'yes' === ( $s['pause_button'] ?? 'yes' ) ) : ?>
					<button type="button" class="eap-ns__pause" aria-pressed="false" aria-label="<?php echo esc_attr( $config['i18n']['pause'] ); ?>" hidden>
						<svg class="eap-ns__pause-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5h3v14H8zM13 5h3v14h-3z" fill="currentColor"/></svg>
						<svg class="eap-ns__play-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5l11 7-11 7z" fill="currentColor"/></svg>
					</button>
				<?php endif; ?>
			</div>

			<?php if ( 'none' !== $config['pagination'] ) : ?>
				<div class="eap-ns__pagination"></div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * An arrow's icon, or a drawn chevron when none is set.
	 *
	 * @param array  $icon     Icons control value.
	 * @param string $fallback SVG path for the fallback chevron.
	 * @return void
	 */
	protected function render_arrow_icon( $icon, $fallback ) {
		if ( ! empty( $icon['value'] ) ) {
			Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
			return;
		}
		echo '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="' . esc_attr( $fallback ) . '" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	}

	/**
	 * Print one slide container, tagged as a Swiper slide.
	 *
	 * The container prints its own wrapper, so the slide classes and ARIA go on
	 * through the should_render filter — the same way Content Toggle tags its
	 * panes. Unrelated containers pass through untouched.
	 *
	 * @param int   $index Child index.
	 * @param array $slide {label}.
	 * @return void
	 */
	public function print_child( $index, $slide = array() ) {
		$children = $this->get_children();

		if ( empty( $children[ $index ] ) ) {
			return;
		}

		$target = $children[ $index ]->get_id();
		$label  = (string) ( $slide['label'] ?? '' );

		$tag = static function ( $should_render, $container ) use ( $target, $label ) {
			if ( $container->get_id() === $target ) {
				$container->add_render_attribute(
					'_wrapper',
					array(
						'class'                => array( 'swiper-slide', 'eap-ns__slide' ),
						'role'                 => 'group',
						'aria-roledescription' => __( 'slide', 'elementor-animatepro' ),
						'aria-label'           => $label,
					)
				);
			}

			return $should_render;
		};

		add_filter( 'elementor/frontend/container/should_render', $tag, 10, 2 );
		$children[ $index ]->print_element();
		remove_filter( 'elementor/frontend/container/should_render', $tag, 10 );
	}

	/**
	 * Editor template: the same shell with an empty track the editor fills
	 * with the slide containers. No arrows or dots — the strip is navigated by
	 * scrolling while editing.
	 *
	 * @return void
	 */
	protected function content_template() {
		?>
		<div class="eap-widget eap-ns eap-ns--editor">
			<div class="eap-ns__stage">
				<div class="eap-ns__viewport swiper">
					<div class="eap-ns__track swiper-wrapper"></div>
				</div>
			</div>
			<p class="eap-ns__editor-hint"><?php echo esc_html__( 'Slides sit side by side while you edit — scroll the strip to reach each one. The slider runs on the page.', 'elementor-animatepro' ); ?></p>
		</div>
		<?php
	}
}
