<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

/**
 * Interactive Animations extension.
 *
 * Adds an "Interactive Animations" section to the Advanced tab of EVERY element
 * and animates it in response to an interaction: as it scrolls through the
 * viewport, on hover, with the mouse (parallax), or toggled on click.
 *
 * Architecture — why this needs so little JS: every effect control writes a CSS
 * VARIABLE onto the element (`--eap-ia-x`, `--eap-ia-rotate`, …) through normal
 * Elementor `selectors`, and interactive-animations.css composes those variables
 * into a single transform per trigger. So:
 *   - Hover is 100% CSS (`:hover`), no JS at all;
 *   - Click only needs a class toggle;
 *   - Scroll only needs ONE number — a 0→1 progress written to `--eap-ia-p`,
 *     with the interpolation done in CSS calc();
 *   - Mouse Move only needs the pointer offset in `--eap-ia-mx` / `--eap-ia-my`.
 * That keeps the per-frame JS work to a single style property write.
 *
 * Enabled/disabled from the plugin's Extensions page via the
 * `interactive-animations` toggle (absent = on).
 */
class EAP_Interactive_Animations {

	const EXTENSION_KEY = 'interactive-animations';

	/**
	 * Elements already given the section this request, keyed by object hash.
	 *
	 * @var array<string, bool>
	 */
	protected $injected = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! self::is_enabled() ) {
			return;
		}

		add_action( 'elementor/element/after_section_end', array( $this, 'maybe_inject' ), 10, 2 );
		add_action( 'elementor/frontend/before_render', array( $this, 'attach' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Whether the extension is enabled (Extensions page toggle; absent = on).
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$states = get_option( EAP_Admin::EXTENSIONS_OPTION, array() );
		if ( ! is_array( $states ) ) {
			return true;
		}
		return ! array_key_exists( self::EXTENSION_KEY, $states ) || ! empty( $states[ self::EXTENSION_KEY ] );
	}

	/**
	 * Register + enqueue assets.
	 *
	 * Enqueued on wp_enqueue_scripts, not lazily during render: `before_render`
	 * runs after that hook, so a late enqueue prints the stylesheet in the footer
	 * and the element would flash un-transformed first.
	 *
	 * @return void
	 */
	public function register_assets() {
		wp_enqueue_style(
			'eap-interactive-animations',
			EAP_URL . 'assets/css/extensions/interactive-animations.css',
			array(),
			EAP_VERSION
		);

		wp_enqueue_script(
			'eap-interactive-animations',
			EAP_URL . 'assets/js/extensions/interactive-animations.js',
			array( 'eap-core-runtime' ),
			EAP_VERSION,
			true
		);
	}

	/* =====================================================================
	 * CONTROLS
	 * ================================================================== */

	/**
	 * Add the section once, after a section every element has.
	 *
	 * @param \Elementor\Controls_Stack $element    Element.
	 * @param string                    $section_id Section that just closed.
	 * @return void
	 */
	public function maybe_inject( $element, $section_id ) {
		if ( ! in_array( $section_id, array( '_section_responsive', '_section_style' ), true ) ) {
			return;
		}

		if ( ! is_object( $element ) || ! method_exists( $element, 'start_controls_section' ) ) {
			return;
		}

		$key = spl_object_hash( $element );
		if ( isset( $this->injected[ $key ] ) ) {
			return;
		}
		$this->injected[ $key ] = true;

		$this->add_controls( $element );
	}

	/**
	 * Register the controls.
	 *
	 * @param \Elementor\Controls_Stack $element Element.
	 * @return void
	 */
	protected function add_controls( $element ) {
		$element->start_controls_section(
			'eap_ia_section',
			array(
				'label' => __( 'Interactive Animations', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'eap_ia_enable',
			array(
				'label'        => __( 'Enable Interactive Animation', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$element->add_control(
			'eap_ia_trigger',
			array(
				'label'       => __( 'Trigger', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'scroll',
				'options'     => array(
					'scroll'    => __( 'Scroll (linked to viewport)', 'elementor-animatepro' ),
					'hover'     => __( 'Hover', 'elementor-animatepro' ),
					'mousemove' => __( 'Mouse Move (parallax)', 'elementor-animatepro' ),
					'click'     => __( 'Click (toggle)', 'elementor-animatepro' ),
				),
				'description' => __( 'Scroll animates from the values below to the element\'s resting state as it enters the viewport. Hover / Click animate to them.', 'elementor-animatepro' ),
				'condition'   => array( 'eap_ia_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_ia_effects_heading',
			array(
				'label'     => __( 'Effects', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'eap_ia_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'eap_ia_x',
			array(
				'label'      => __( 'Translate X', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => -400, 'max' => 400 ) ),
				'default'    => array( 'size' => 0, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-ia-x: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'eap_ia_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'eap_ia_y',
			array(
				'label'      => __( 'Translate Y', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => -400, 'max' => 400 ) ),
				'default'    => array( 'size' => 40, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-ia-y: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'eap_ia_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'eap_ia_rotate',
			array(
				'label'      => __( 'Rotate', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array( 'deg' => array( 'min' => -180, 'max' => 180 ) ),
				'default'    => array( 'size' => 0, 'unit' => 'deg' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-ia-rotate: {{SIZE}}deg;' ),
				'condition'  => array( 'eap_ia_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'eap_ia_scale',
			array(
				'label'      => __( 'Scale', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 3, 'step' => 0.05 ) ),
				'default'    => array( 'size' => 1 ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-ia-scale: {{SIZE}};' ),
				'condition'  => array( 'eap_ia_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'eap_ia_opacity',
			array(
				'label'      => __( 'Opacity', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ) ),
				'default'    => array( 'size' => 0 ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-ia-opacity: {{SIZE}};' ),
				'condition'  => array( 'eap_ia_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'eap_ia_blur',
			array(
				'label'      => __( 'Blur', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 0, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-ia-blur: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'eap_ia_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_ia_timing_heading',
			array(
				'label'     => __( 'Timing', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'eap_ia_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_ia_duration',
			array(
				'label'      => __( 'Duration', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array( 's' => array( 'min' => 0, 'max' => 3, 'step' => 0.05 ) ),
				'default'    => array( 'size' => 0.6, 'unit' => 's' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-ia-duration: {{SIZE}}s;' ),
				'condition'  => array( 'eap_ia_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_ia_delay',
			array(
				'label'      => __( 'Delay', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array( 's' => array( 'min' => 0, 'max' => 2, 'step' => 0.05 ) ),
				'default'    => array( 'size' => 0, 'unit' => 's' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-ia-delay: {{SIZE}}s;' ),
				'condition'  => array(
					'eap_ia_enable'   => 'yes',
					'eap_ia_trigger!' => array( 'scroll', 'mousemove' ),
				),
			)
		);

		$element->add_control(
			'eap_ia_easing',
			array(
				'label'     => __( 'Easing', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cubic-bezier(0.22, 1, 0.36, 1)',
				'options'   => array(
					'cubic-bezier(0.22, 1, 0.36, 1)'      => __( 'Ease Out (soft)', 'elementor-animatepro' ),
					'ease'                                => __( 'Ease', 'elementor-animatepro' ),
					'linear'                              => __( 'Linear', 'elementor-animatepro' ),
					'ease-in'                             => __( 'Ease In', 'elementor-animatepro' ),
					'ease-out'                            => __( 'Ease Out', 'elementor-animatepro' ),
					'ease-in-out'                         => __( 'Ease In Out', 'elementor-animatepro' ),
					'cubic-bezier(0.34, 1.56, 0.64, 1)'   => __( 'Back (overshoot)', 'elementor-animatepro' ),
				),
				'selectors' => array( '{{WRAPPER}}' => '--eap-ia-easing: {{VALUE}};' ),
				'condition' => array( 'eap_ia_enable' => 'yes' ),
			)
		);

		/* ---------- Scroll ---------- */

		$element->add_control(
			'eap_ia_scroll_heading',
			array(
				'label'     => __( 'Scroll Range', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'eap_ia_enable'  => 'yes',
					'eap_ia_trigger' => 'scroll',
				),
			)
		);

		$element->add_control(
			'eap_ia_scroll_start',
			array(
				'label'       => __( 'Start', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '%' ),
				'range'       => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
				'default'     => array( 'size' => 90, 'unit' => '%' ),
				'description' => __( 'How far down the viewport the element begins animating (100% = the very bottom edge).', 'elementor-animatepro' ),
				'condition'   => array(
					'eap_ia_enable'  => 'yes',
					'eap_ia_trigger' => 'scroll',
				),
			)
		);

		$element->add_control(
			'eap_ia_scroll_end',
			array(
				'label'       => __( 'End', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '%' ),
				'range'       => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
				'default'     => array( 'size' => 50, 'unit' => '%' ),
				'description' => __( 'Where it finishes. Must be smaller than Start.', 'elementor-animatepro' ),
				'condition'   => array(
					'eap_ia_enable'  => 'yes',
					'eap_ia_trigger' => 'scroll',
				),
			)
		);

		$element->add_control(
			'eap_ia_scroll_once',
			array(
				'label'        => __( 'Play Once', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Keep the finished state instead of reversing when scrolled back.', 'elementor-animatepro' ),
				'condition'    => array(
					'eap_ia_enable'  => 'yes',
					'eap_ia_trigger' => 'scroll',
				),
			)
		);

		/* ---------- Mouse move ---------- */

		$element->add_control(
			'eap_ia_mouse_scope',
			array(
				'label'     => __( 'Pointer Area', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'viewport',
				'options'   => array(
					'viewport' => __( 'Whole viewport', 'elementor-animatepro' ),
					'element'  => __( 'This element only', 'elementor-animatepro' ),
				),
				'separator' => 'before',
				'condition' => array(
					'eap_ia_enable'  => 'yes',
					'eap_ia_trigger' => 'mousemove',
				),
			)
		);

		$element->add_control(
			'eap_ia_reduced_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Animation is disabled automatically for visitors who prefer reduced motion.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'eap_ia_enable' => 'yes' ),
			)
		);

		$element->end_controls_section();
	}

	/* =====================================================================
	 * ATTACH
	 * ================================================================== */

	/**
	 * Stamp the trigger class + runtime config onto the element wrapper.
	 *
	 * @param \Elementor\Element_Base $element Element.
	 * @return void
	 */
	public function attach( $element ) {
		if ( ! is_object( $element ) || ! method_exists( $element, 'get_settings_for_display' ) ) {
			return;
		}

		$settings = $element->get_settings_for_display();

		if ( 'yes' !== ( $settings['eap_ia_enable'] ?? '' ) ) {
			return;
		}

		// Read into locals through ?? first — an element saved before a control
		// existed has no key, and a second unguarded read would warn.
		$trigger = (string) ( $settings['eap_ia_trigger'] ?? 'scroll' );
		if ( ! in_array( $trigger, array( 'scroll', 'hover', 'mousemove', 'click' ), true ) ) {
			$trigger = 'scroll';
		}

		$classes = array( 'eap-ia', 'eap-ia--' . $trigger );

		$element->add_render_attribute( '_wrapper', 'class', $classes );

		// Hover is pure CSS; the rest need the runtime.
		if ( 'hover' === $trigger ) {
			return;
		}

		$config = array( 'trigger' => $trigger );

		if ( 'scroll' === $trigger ) {
			$config['start'] = isset( $settings['eap_ia_scroll_start']['size'] ) ? (float) $settings['eap_ia_scroll_start']['size'] : 90;
			$config['end']   = isset( $settings['eap_ia_scroll_end']['size'] ) ? (float) $settings['eap_ia_scroll_end']['size'] : 50;
			$config['once']  = ( 'yes' === ( $settings['eap_ia_scroll_once'] ?? '' ) );
		}

		if ( 'mousemove' === $trigger ) {
			$config['scope'] = ( 'element' === ( $settings['eap_ia_mouse_scope'] ?? 'viewport' ) ) ? 'element' : 'viewport';
		}

		$element->add_render_attribute( '_wrapper', 'data-eap-ia', wp_json_encode( $config ) );
	}
}
