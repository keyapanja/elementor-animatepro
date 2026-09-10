<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

/**
 * Hover Interaction extension.
 *
 * Adds a "Hover Interaction" section to the Advanced tab of every element, with
 * a NORMAL and a HOVER value for each effect — Opacity, Filter (blur, contrast,
 * grayscale, invert, saturate, sepia), Offset (top / left) and Transform
 * (rotate X/Y/Z, scale X/Y, skew X/Y) — plus a cursor-following 3D tilt that is
 * hover-only, and duration / delay / easing.
 *
 * Design notes, all of which come from reading Elementor's own source:
 *
 * 1. Every control writes a CSS VARIABLE and hover-interaction.css composes them
 *    ONCE into a single transform + filter. The hover state therefore only has to
 *    override variables — the composed rule is never duplicated — which is what
 *    keeps ~32 paired controls down to one short stylesheet. Elementor's own
 *    Transform controls use exactly this architecture (the `--e-transform-*`
 *    family in frontend.css), and our composition folds those variables in so
 *    the two systems stack instead of cancelling each other out.
 *
 * 2. State is carried by `prefix_class`, not by add_render_attribute(). In the
 *    editor the element wrapper is built by Backbone, whose className() is just
 *    'elementor-element elementor-element-edit-mode ' + uniqueID — PHP-added
 *    _wrapper classes and data attributes do not exist on the canvas at all.
 *    `prefix_class` is applied by BOTH element-base.php (front end) and the
 *    editor view (which swaps the class live on change), so it is the only way
 *    to get a class that behaves identically in both places.
 *
 * 3. The effects land on `.elementor-widget-container`, not on the wrapper,
 *    mirroring Elementor's `.e-transform .elementor-widget-container` rule. That
 *    keeps the wrapper untransformed so the hover hit-area stays still while the
 *    content moves — otherwise a shrinking element flickers in and out of its
 *    own :hover.
 *
 * Everything except the tilt is pure CSS; the JS exists only to sample the cursor.
 *
 * Enabled/disabled from the plugin's Extensions page via the `hover-interaction`
 * toggle (absent = on).
 */
class EAP_Hover_Interaction {

	const EXTENSION_KEY = 'hover-interaction';

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
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
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
	 * On wp_enqueue_scripts rather than lazily during render: `before_render` runs
	 * after that hook, so a late enqueue prints the stylesheet in the footer and
	 * the element would flash untransformed first. The editor canvas is a real
	 * front-end render, so this covers it too.
	 *
	 * @return void
	 */
	public function register_assets() {
		wp_enqueue_style(
			'eap-hover-interaction',
			EAP_URL . 'assets/css/extensions/hover-interaction.css',
			array(),
			EAP_VERSION
		);

		/*
		 * No dependency on eap-core-runtime: the tilt binds through document-level
		 * delegation rather than a per-scope scan, so it needs nothing from the
		 * shared runtime — and declaring the handle anyway would drag core.js onto
		 * every page that loads this extension.
		 */
		wp_enqueue_script(
			'eap-hover-interaction',
			EAP_URL . 'assets/js/extensions/hover-interaction.js',
			array(),
			EAP_VERSION,
			true
		);
	}

	/* =====================================================================
	 * EFFECT SPEC
	 * ================================================================== */

	/**
	 * The effects offered for BOTH the normal and the hover state.
	 *
	 * Declared once and looped over per state so the two tabs cannot drift apart
	 * and ~32 controls stay readable.
	 *
	 * On the "X, Y and Z" the docs list for every transform: rotate gets all
	 * three because all three are real, but scaleZ only affects 3D-transformed
	 * descendants (a no-op on a widget) and skewZ does not exist in CSS at all,
	 * so those two are deliberately not offered rather than shipped as controls
	 * that do nothing.
	 *
	 * key => [ label, css var, default, min, max, step, unit, group ]
	 *
	 * @return array<string, array>
	 */
	protected function get_effects() {
		return array(
			'opacity'     => array( __( 'Opacity', 'elementor-animatepro' ), '--eap-hi-opacity', 1, 0, 1, 0.05, '', 'opacity' ),

			'blur'        => array( __( 'Blur', 'elementor-animatepro' ), '--eap-hi-blur', 0, 0, 30, 0.5, 'px', 'filter' ),
			'contrast'    => array( __( 'Contrast', 'elementor-animatepro' ), '--eap-hi-contrast', 100, 0, 200, 1, '%', 'filter' ),
			'grayscale'   => array( __( 'Grayscale', 'elementor-animatepro' ), '--eap-hi-grayscale', 0, 0, 100, 1, '%', 'filter' ),
			'invert'      => array( __( 'Invert', 'elementor-animatepro' ), '--eap-hi-invert', 0, 0, 100, 1, '%', 'filter' ),
			'saturate'    => array( __( 'Saturate', 'elementor-animatepro' ), '--eap-hi-saturate', 100, 0, 200, 1, '%', 'filter' ),
			'sepia'       => array( __( 'Sepia', 'elementor-animatepro' ), '--eap-hi-sepia', 0, 0, 100, 1, '%', 'filter' ),

			'offset_top'  => array( __( 'Offset Top', 'elementor-animatepro' ), '--eap-hi-top', 0, -200, 200, 1, 'px', 'offset' ),
			'offset_left' => array( __( 'Offset Left', 'elementor-animatepro' ), '--eap-hi-left', 0, -200, 200, 1, 'px', 'offset' ),

			'rotate_x'    => array( __( 'Rotate X', 'elementor-animatepro' ), '--eap-hi-rx', 0, -180, 180, 1, 'deg', 'transform' ),
			'rotate_y'    => array( __( 'Rotate Y', 'elementor-animatepro' ), '--eap-hi-ry', 0, -180, 180, 1, 'deg', 'transform' ),
			'rotate_z'    => array( __( 'Rotate Z', 'elementor-animatepro' ), '--eap-hi-rz', 0, -180, 180, 1, 'deg', 'transform' ),
			'scale_x'     => array( __( 'Scale X', 'elementor-animatepro' ), '--eap-hi-sx', 1, 0, 3, 0.05, '', 'transform' ),
			'scale_y'     => array( __( 'Scale Y', 'elementor-animatepro' ), '--eap-hi-sy', 1, 0, 3, 0.05, '', 'transform' ),
			'skew_x'      => array( __( 'Skew X', 'elementor-animatepro' ), '--eap-hi-kx', 0, -60, 60, 1, 'deg', 'transform' ),
			'skew_y'      => array( __( 'Skew Y', 'elementor-animatepro' ), '--eap-hi-ky', 0, -60, 60, 1, 'deg', 'transform' ),
		);
	}

	/**
	 * Headings that introduce each effect group.
	 *
	 * @return array<string, string>
	 */
	protected function get_group_headings() {
		return array(
			'filter'    => __( 'Filter', 'elementor-animatepro' ),
			'offset'    => __( 'Offset', 'elementor-animatepro' ),
			'transform' => __( 'Transform', 'elementor-animatepro' ),
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
			'eap_hi_section',
			array(
				'label' => __( 'Hover Interaction', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'eap_hi_enable',
			array(
				'label'        => __( 'Enable Hover Interaction', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'active',
				'default'      => '',
				'prefix_class' => 'eap-hi-',
			)
		);

		$element->add_control(
			'eap_hi_preview',
			array(
				'label'        => __( 'Show Hover State in Editor', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'active',
				'default'      => '',
				'prefix_class' => 'eap-hi-preview-',
				'description'  => __( 'Holds the hover state open on the canvas so you can tune it without keeping the mouse still. Editor only — it is scoped to the canvas and never applies on the front end.', 'elementor-animatepro' ),
				'condition'    => array( 'eap_hi_enable' => 'active' ),
			)
		);

		$element->start_controls_tabs(
			'eap_hi_tabs',
			array( 'condition' => array( 'eap_hi_enable' => 'active' ) )
		);

		$element->start_controls_tab(
			'eap_hi_tab_normal',
			array(
				'label'     => __( 'Normal', 'elementor-animatepro' ),
				'condition' => array( 'eap_hi_enable' => 'active' ),
			)
		);
		$this->add_state_controls( $element, 'normal', '{{WRAPPER}}' );
		$element->end_controls_tab();

		$element->start_controls_tab(
			'eap_hi_tab_hover',
			array(
				'label'     => __( 'Hover', 'elementor-animatepro' ),
				'condition' => array( 'eap_hi_enable' => 'active' ),
			)
		);
		/*
		 * The second selector is the editor preview. `elementor-element-edit-mode`
		 * is added by the editor's Backbone className() and so exists ONLY on the
		 * canvas; pairing it with the preview class scopes the held-open hover
		 * state to the editor without an is_edit_mode() branch in PHP — which
		 * matters, because the generated CSS is cached per post and would
		 * otherwise carry an editor-only rule to the front end.
		 */
		$this->add_state_controls(
			$element,
			'hover',
			'{{WRAPPER}}:hover, {{WRAPPER}}.elementor-element-edit-mode.eap-hi-preview-active'
		);
		$element->end_controls_tab();

		$element->end_controls_tabs();

		/* ---------- Tilt (hover only) ---------- */

		$element->add_control(
			'eap_hi_tilt_heading',
			array(
				'label'     => __( 'Tilt', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'eap_hi_enable' => 'active' ),
			)
		);

		$element->add_control(
			'eap_hi_tilt',
			array(
				'label'        => __( 'Cursor Tilt', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'On', 'elementor-animatepro' ),
				'label_off'    => __( 'Off', 'elementor-animatepro' ),
				'return_value' => 'active',
				'default'      => '',
				'prefix_class' => 'eap-hi-tilt-',
				'description'  => __( 'Tilts the element in 3D towards the cursor. Hover only, and it adds to the rotation set above.', 'elementor-animatepro' ),
				'condition'    => array( 'eap_hi_enable' => 'active' ),
			)
		);

		$element->add_control(
			'eap_hi_tilt_max',
			array(
				'label'     => __( 'Tilt Amount', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 1, 'max' => 45, 'step' => 1 ) ),
				'default'   => array( 'size' => 12 ),
				// Deliberately unitless: the JS reads it back and multiplies by 1deg.
				'selectors' => array( '{{WRAPPER}}' => '--eap-hi-tilt-max: {{SIZE}};' ),
				'condition' => array(
					'eap_hi_enable' => 'active',
					'eap_hi_tilt'   => 'active',
				),
			)
		);

		$element->add_control(
			'eap_hi_tilt_reverse',
			array(
				'label'        => __( 'Reverse Tilt', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'active',
				'default'      => '',
				'prefix_class' => 'eap-hi-tiltrev-',
				'condition'    => array(
					'eap_hi_enable' => 'active',
					'eap_hi_tilt'   => 'active',
				),
			)
		);

		/* ---------- General ---------- */

		$element->add_control(
			'eap_hi_general_heading',
			array(
				'label'     => __( 'General', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'eap_hi_enable' => 'active' ),
			)
		);

		$element->add_control(
			'eap_hi_duration',
			array(
				'label'      => __( 'Duration', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array( 's' => array( 'min' => 0, 'max' => 3, 'step' => 0.05 ) ),
				'default'    => array( 'size' => 0.4, 'unit' => 's' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-hi-duration: {{SIZE}}s;' ),
				'condition'  => array( 'eap_hi_enable' => 'active' ),
			)
		);

		$element->add_control(
			'eap_hi_delay',
			array(
				'label'      => __( 'Delay', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array( 's' => array( 'min' => 0, 'max' => 2, 'step' => 0.05 ) ),
				'default'    => array( 'size' => 0, 'unit' => 's' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-hi-delay: {{SIZE}}s;' ),
				'condition'  => array( 'eap_hi_enable' => 'active' ),
			)
		);

		$element->add_control(
			'eap_hi_easing',
			array(
				'label'     => __( 'Easing', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'ease',
				'options'   => array(
					'ease'                              => __( 'Ease', 'elementor-animatepro' ),
					'linear'                            => __( 'Linear', 'elementor-animatepro' ),
					'ease-in'                           => __( 'Ease In', 'elementor-animatepro' ),
					'ease-out'                          => __( 'Ease Out', 'elementor-animatepro' ),
					'ease-in-out'                       => __( 'Ease In Out', 'elementor-animatepro' ),
					'cubic-bezier(0.22, 1, 0.36, 1)'    => __( 'Ease Out (soft)', 'elementor-animatepro' ),
					'cubic-bezier(0.34, 1.56, 0.64, 1)' => __( 'Back (overshoot)', 'elementor-animatepro' ),
				),
				'selectors' => array( '{{WRAPPER}}' => '--eap-hi-easing: {{VALUE}};' ),
				'condition' => array( 'eap_hi_enable' => 'active' ),
			)
		);

		$element->add_control(
			'eap_hi_perspective',
			array(
				'label'       => __( 'Perspective', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 200, 'max' => 3000 ) ),
				'default'     => array( 'size' => 1000, 'unit' => 'px' ),
				'selectors'   => array( '{{WRAPPER}}' => '--eap-hi-perspective: {{SIZE}}px;' ),
				'description' => __( 'Depth for the 3D rotations and the tilt. Lower is more dramatic.', 'elementor-animatepro' ),
				'condition'   => array( 'eap_hi_enable' => 'active' ),
			)
		);

		$element->end_controls_section();
	}

	/**
	 * Register one state's worth of effect controls.
	 *
	 * @param \Elementor\Controls_Stack $element  Element.
	 * @param string                    $state    'normal' | 'hover'.
	 * @param string                    $selector Selector the variables are written to.
	 * @return void
	 */
	protected function add_state_controls( $element, $state, $selector ) {
		$headings = $this->get_group_headings();
		$seen     = array();

		foreach ( $this->get_effects() as $key => $effect ) {
			list( $label, $var, $default, $min, $max, $step, $unit, $group ) = $effect;

			// One heading per group, the first time that group appears.
			if ( isset( $headings[ $group ] ) && ! isset( $seen[ $group ] ) ) {
				$seen[ $group ] = true;
				$element->add_control(
					'eap_hi_' . $state . '_' . $group . '_heading',
					array(
						'label'     => $headings[ $group ],
						'type'      => Controls_Manager::HEADING,
						'separator' => 'before',
						'condition' => array( 'eap_hi_enable' => 'active' ),
					)
				);
			}

			$args = array(
				'label'     => $label,
				'type'      => Controls_Manager::SLIDER,
				'default'   => array( 'size' => $default ),
				'range'     => array( 'px' => array( 'min' => $min, 'max' => $max, 'step' => $step ) ),
				'selectors' => array( $selector => $var . ': {{SIZE}}' . $unit . ';' ),
				'condition' => array( 'eap_hi_enable' => 'active' ),
			);

			if ( '' !== $unit ) {
				$args['size_units']      = array( $unit );
				$args['default']['unit'] = $unit;
				$args['range']           = array( $unit => array( 'min' => $min, 'max' => $max, 'step' => $step ) );
			}

			$element->add_control( 'eap_hi_' . $state . '_' . $key, $args );
		}
	}
}
