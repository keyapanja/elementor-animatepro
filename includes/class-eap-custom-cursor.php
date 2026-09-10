<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

/**
 * Custom Cursor extension.
 *
 * Replaces the pointer with a custom cursor while it is over the element. Two
 * states — NORMAL and POINTER (the latter while over anything clickable inside
 * the element) — each of which can be Default, Circle, Icon, Image or SVG Code,
 * plus an optional motion trail with eight effects.
 *
 * FRONT END ONLY, deliberately. A custom cursor on the editor canvas would hide
 * the real pointer that is needed to drag, resize and right-click the very
 * element being edited, so `attach()` bails in edit mode.
 *
 * That decision is also what settles the transport question. The Hover
 * Interaction extension had to carry its state in `prefix_class`, because the
 * editor builds element wrappers in Backbone and PHP-added `_wrapper` attributes
 * simply do not exist on the canvas (see class-eap-hover-interaction.php). Here
 * the effect never runs on the canvas, so `_wrapper` is free to use — and it is
 * the only transport that can carry what this feature actually needs: icon and
 * SVG MARKUP, which no class or CSS variable can express.
 *
 * A single shared cursor node is created once per page by custom-cursor.js and
 * adopts whichever element the pointer is currently over, so N elements on a
 * page still cost one node.
 *
 * Enabled/disabled from the plugin's Extensions page via the `custom-cursor`
 * toggle (absent = on).
 */
class EAP_Custom_Cursor {

	const EXTENSION_KEY = 'custom-cursor';

	/**
	 * Elements already given the section this request, keyed by object hash.
	 *
	 * @var array<string, bool>
	 */
	protected $injected = array();

	/**
	 * Whether any element on this request actually asked for a cursor.
	 *
	 * @var bool
	 */
	protected $used = false;

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
	 * Register the assets.
	 *
	 * Registered (not enqueued) here, then enqueued on demand by attach() — the
	 * cursor is a per-element opt-in and most pages will use none, so unlike the
	 * other extensions there is nothing to gain from loading it everywhere.
	 * Registering on wp_enqueue_scripts keeps the handles available to the late
	 * enqueue; a cursor appearing a frame after the footer parses is harmless,
	 * which is exactly why this one can be lazy where Hover Interaction could not.
	 *
	 * @return void
	 */
	public function register_assets() {
		wp_register_style(
			'eap-custom-cursor',
			EAP_URL . 'assets/css/extensions/custom-cursor.css',
			array(),
			EAP_VERSION
		);

		wp_register_script(
			'eap-custom-cursor',
			EAP_URL . 'assets/js/extensions/custom-cursor.js',
			array(),
			EAP_VERSION,
			true
		);
	}

	/* =====================================================================
	 * SPEC
	 * ================================================================== */

	/**
	 * Cursor types offered for each state.
	 *
	 * @return array<string, string>
	 */
	protected function get_types() {
		return array(
			'default' => __( 'Default', 'elementor-animatepro' ),
			'circle'  => __( 'Circle', 'elementor-animatepro' ),
			'icon'    => __( 'Icon', 'elementor-animatepro' ),
			'image'   => __( 'Image', 'elementor-animatepro' ),
			'svg'     => __( 'SVG Code', 'elementor-animatepro' ),
		);
	}

	/**
	 * Trail effects.
	 *
	 * The keys are the CSS modifier suffixes (`.eap-cc-p--ink` etc.) and the JS
	 * spawn-parameter keys, so all three stay in step from one list.
	 *
	 * @return array<string, string>
	 */
	protected function get_trail_effects() {
		return array(
			'ink'       => __( 'Ink Trail', 'elementor-animatepro' ),
			'particles' => __( 'Trail Particles', 'elementor-animatepro' ),
			'smoke'     => __( 'Phantom Smoke', 'elementor-animatepro' ),
			'echo'      => __( 'Spirit Echo', 'elementor-animatepro' ),
			'blocks'    => __( 'Glow Blocks', 'elementor-animatepro' ),
			'orbs'      => __( 'Chroma Orbs', 'elementor-animatepro' ),
			'frost'     => __( 'Frost Sparkles', 'elementor-animatepro' ),
			'comet'     => __( 'Dot Comet', 'elementor-animatepro' ),
		);
	}

	/**
	 * Elements that count as "clickable" and so switch the cursor to its Pointer
	 * state. Mirrors what a browser would itself show a hand pointer over.
	 *
	 * @return string
	 */
	public static function get_pointer_selector() {
		return 'a[href], button, [role="button"], input:not([type="hidden"]), select, textarea, label, summary, [onclick], .elementor-button';
	}

	/**
	 * Tags and attributes permitted in the SVG Code field.
	 *
	 * The field is authored by someone who can already edit posts, but "can edit
	 * posts" is not "may inject script into every visitor's page", so the value is
	 * run through wp_kses on OUTPUT with this allowlist. Nothing scriptable is
	 * included: no <script>, no <foreignObject>, no href/xlink:href (which accept
	 * javascript: URLs), and because on* handlers are simply absent from every
	 * tag's attribute list, wp_kses strips them.
	 *
	 * Elementor's own Svg_Handler::sanitize_svg() is not reusable here — it takes
	 * an uploaded FILENAME, not an inline string.
	 *
	 * @return array<string, array<string, bool>>
	 */
	protected function get_svg_allowed_html() {
		$shared = array(
			'fill'             => true,
			'fill-opacity'     => true,
			'fill-rule'        => true,
			'clip-rule'        => true,
			'stroke'           => true,
			'stroke-width'     => true,
			'stroke-opacity'   => true,
			'stroke-linecap'   => true,
			'stroke-linejoin'  => true,
			'stroke-dasharray' => true,
			'opacity'          => true,
			'transform'        => true,
			'class'            => true,
			'style'            => true,
			'id'               => true,
		);

		return array(
			'svg'            => array_merge(
				$shared,
				array(
					'xmlns'   => true,
					'viewbox' => true,
					'width'   => true,
					'height'  => true,
				)
			),
			'g'              => $shared,
			'path'           => array_merge( $shared, array( 'd' => true ) ),
			'circle'         => array_merge( $shared, array( 'cx' => true, 'cy' => true, 'r' => true ) ),
			'ellipse'        => array_merge( $shared, array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ) ),
			'rect'           => array_merge( $shared, array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true ) ),
			'line'           => array_merge( $shared, array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true ) ),
			'polyline'       => array_merge( $shared, array( 'points' => true ) ),
			'polygon'        => array_merge( $shared, array( 'points' => true ) ),
			'defs'           => $shared,
			'lineargradient' => array_merge( $shared, array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'gradientunits' => true ) ),
			'radialgradient' => array_merge( $shared, array( 'cx' => true, 'cy' => true, 'r' => true, 'fx' => true, 'fy' => true, 'gradientunits' => true ) ),
			'stop'           => array_merge( $shared, array( 'offset' => true, 'stop-color' => true, 'stop-opacity' => true ) ),
			'title'          => array(),
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
			'eap_cc_section',
			array(
				'label' => __( 'Custom Cursor', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'eap_cc_enable',
			array(
				'label'        => __( 'Enable Custom Cursor', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$element->add_control(
			'eap_cc_editor_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Shows on the front end only — the canvas keeps the real pointer so you can still drag and resize this element.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'eap_cc_enable' => 'yes' ),
			)
		);

		$element->start_controls_tabs(
			'eap_cc_tabs',
			array( 'condition' => array( 'eap_cc_enable' => 'yes' ) )
		);

		$element->start_controls_tab(
			'eap_cc_tab_normal',
			array(
				'label'     => __( 'Normal', 'elementor-animatepro' ),
				'condition' => array( 'eap_cc_enable' => 'yes' ),
			)
		);
		$this->add_state_controls( $element, 'n', 'circle' );
		$element->end_controls_tab();

		$element->start_controls_tab(
			'eap_cc_tab_pointer',
			array(
				'label'     => __( 'Pointer', 'elementor-animatepro' ),
				'condition' => array( 'eap_cc_enable' => 'yes' ),
			)
		);
		$element->add_control(
			'eap_cc_p_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Used while the pointer is over a link, button or field inside this element.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'eap_cc_enable' => 'yes' ),
			)
		);
		$this->add_state_controls( $element, 'p', 'circle' );
		$element->end_controls_tab();

		$element->end_controls_tabs();

		/* ---------- Trail ---------- */

		$element->add_control(
			'eap_cc_trail_heading',
			array(
				'label'     => __( 'Trail', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'eap_cc_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_cc_trail',
			array(
				'label'        => __( 'Enable Trail', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'eap_cc_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_cc_trail_effect',
			array(
				'label'     => __( 'Effect', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'particles',
				'options'   => $this->get_trail_effects(),
				'condition' => array(
					'eap_cc_enable' => 'yes',
					'eap_cc_trail'  => 'yes',
				),
			)
		);

		$element->add_control(
			'eap_cc_trail_color',
			array(
				'label'     => __( 'Trail Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6366f1',
				'condition' => array(
					'eap_cc_enable' => 'yes',
					'eap_cc_trail'  => 'yes',
				),
			)
		);

		$element->add_control(
			'eap_cc_trail_size',
			array(
				'label'      => __( 'Trail Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 4, 'max' => 80 ) ),
				'default'    => array( 'size' => 18, 'unit' => 'px' ),
				'condition'  => array(
					'eap_cc_enable' => 'yes',
					'eap_cc_trail'  => 'yes',
				),
			)
		);

		/* ---------- General ---------- */

		$element->add_control(
			'eap_cc_general_heading',
			array(
				'label'     => __( 'General', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'eap_cc_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_cc_hide_native',
			array(
				'label'        => __( 'Hide Real Cursor', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'eap_cc_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_cc_speed',
			array(
				'label'       => __( 'Follow Speed', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 0.05, 'max' => 1, 'step' => 0.05 ) ),
				'default'     => array( 'size' => 0.2 ),
				'description' => __( 'How quickly the cursor catches up. 1 pins it to the pointer; lower values trail behind.', 'elementor-animatepro' ),
				'condition'   => array( 'eap_cc_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_cc_blend',
			array(
				'label'     => __( 'Blend Mode', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'normal',
				'options'   => array(
					'normal'     => __( 'Normal', 'elementor-animatepro' ),
					'difference' => __( 'Difference', 'elementor-animatepro' ),
					'exclusion'  => __( 'Exclusion', 'elementor-animatepro' ),
					'multiply'   => __( 'Multiply', 'elementor-animatepro' ),
					'screen'     => __( 'Screen', 'elementor-animatepro' ),
					'overlay'    => __( 'Overlay', 'elementor-animatepro' ),
				),
				'condition' => array( 'eap_cc_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_cc_z_index',
			array(
				'label'     => __( 'Z-Index', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 99999,
				'min'       => 1,
				'condition' => array( 'eap_cc_enable' => 'yes' ),
			)
		);

		$element->end_controls_section();
	}

	/**
	 * Register one state's worth of controls.
	 *
	 * @param \Elementor\Controls_Stack $element Element.
	 * @param string                    $p       Key prefix: 'n' (normal) | 'p' (pointer).
	 * @param string                    $default Default cursor type.
	 * @return void
	 */
	protected function add_state_controls( $element, $p, $default ) {
		$base = array( 'eap_cc_enable' => 'yes' );
		$k    = 'eap_cc_' . $p . '_';

		$element->add_control(
			$k . 'type',
			array(
				'label'     => __( 'Cursor Type', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => $default,
				'options'   => $this->get_types(),
				'condition' => $base,
			)
		);

		$element->add_control(
			$k . 'icon',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array( 'value' => 'fas fa-circle', 'library' => 'fa-solid' ),
				'condition' => $base + array( $k . 'type' => 'icon' ),
			)
		);

		$element->add_control(
			$k . 'image',
			array(
				'label'     => __( 'Image', 'elementor-animatepro' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => $base + array( $k . 'type' => 'image' ),
			)
		);

		$element->add_control(
			$k . 'svg',
			array(
				'label'       => __( 'SVG Code', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 6,
				'placeholder' => '<svg viewBox="0 0 24 24">…</svg>',
				'description' => __( 'Paste an inline SVG. Scripts, event handlers and links are stripped.', 'elementor-animatepro' ),
				'condition'   => $base + array( $k . 'type' => 'svg' ),
			)
		);

		$element->add_control(
			$k . 'size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 4, 'max' => 200 ) ),
				'default'    => array( 'size' => 'circle' === $default ? 28 : 40, 'unit' => 'px' ),
				'condition'  => $base + array( $k . 'type!' => 'default' ),
			)
		);

		$element->add_control(
			$k . 'color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'condition' => $base + array( $k . 'type' => array( 'icon', 'svg' ) ),
			)
		);

		$element->add_control(
			$k . 'bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'circle' === $default ? 'rgba(99, 102, 241, 0.25)' : '',
				'condition' => $base + array( $k . 'type' => array( 'circle', 'icon', 'svg' ) ),
			)
		);

		$element->add_control(
			$k . 'border_width',
			array(
				'label'      => __( 'Border Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 12 ) ),
				'default'    => array( 'size' => 2, 'unit' => 'px' ),
				'condition'  => $base + array( $k . 'type' => 'circle' ),
			)
		);

		$element->add_control(
			$k . 'border_color',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6366f1',
				'condition' => $base + array( $k . 'type' => 'circle' ),
			)
		);

		$element->add_control(
			$k . 'radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array( 'min' => 0, 'max' => 50 ),
					'px' => array( 'min' => 0, 'max' => 100 ),
				),
				'default'    => array( 'size' => 50, 'unit' => '%' ),
				'condition'  => $base + array( $k . 'type' => array( 'circle', 'icon', 'image', 'svg' ) ),
			)
		);

		$element->add_control(
			$k . 'opacity',
			array(
				'label'     => __( 'Opacity', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ) ),
				'default'   => array( 'size' => 1 ),
				'condition' => $base + array( $k . 'type!' => 'default' ),
			)
		);
	}

	/* =====================================================================
	 * ATTACH
	 * ================================================================== */

	/**
	 * Stamp the resolved cursor config onto the element wrapper.
	 *
	 * @param \Elementor\Element_Base $element Element.
	 * @return void
	 */
	public function attach( $element ) {
		if ( ! is_object( $element ) || ! method_exists( $element, 'get_settings_for_display' ) ) {
			return;
		}

		$settings = $element->get_settings_for_display();

		if ( 'yes' !== ( $settings['eap_cc_enable'] ?? '' ) ) {
			return;
		}

		// Never on the canvas: it would take away the pointer needed to edit.
		if ( class_exists( '\Elementor\Plugin' )
			&& isset( \Elementor\Plugin::$instance->editor )
			&& \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return;
		}

		$normal  = $this->build_state( $settings, 'n' );
		$pointer = $this->build_state( $settings, 'p' );
		$trail   = 'yes' === ( $settings['eap_cc_trail'] ?? '' );

		// Nothing to show and nothing to trail: don't pay for a listener.
		if ( 'default' === $normal['type'] && 'default' === $pointer['type'] && ! $trail ) {
			return;
		}

		$effects = $this->get_trail_effects();
		$fx      = $settings['eap_cc_trail_effect'] ?? 'particles';

		$config = array(
			'normal'  => $normal,
			'pointer' => $pointer,
			'trail'   => $trail ? array(
				'fx'    => isset( $effects[ $fx ] ) ? $fx : 'particles',
				'color' => $settings['eap_cc_trail_color'] ?? '#6366f1',
				'size'  => $this->size_of( $settings, 'eap_cc_trail_size', 18 ),
			) : null,
			'speed'   => min( 1, max( 0.05, (float) $this->size_of( $settings, 'eap_cc_speed', 0.2 ) ) ),
			'blend'   => $settings['eap_cc_blend'] ?? 'normal',
			'hide'    => 'yes' === ( $settings['eap_cc_hide_native'] ?? 'yes' ),
			'z'       => absint( $settings['eap_cc_z_index'] ?? 99999 ),
			'pointerSelector' => self::get_pointer_selector(),
		);

		$element->add_render_attribute( '_wrapper', 'data-eap-cc', wp_json_encode( $config ) );

		if ( ! $this->used ) {
			$this->used = true;
			wp_enqueue_style( 'eap-custom-cursor' );
			wp_enqueue_script( 'eap-custom-cursor' );
		}
	}

	/**
	 * Resolve one state's settings into the shape the JS consumes.
	 *
	 * @param array<string, mixed> $settings Element settings.
	 * @param string               $p        'n' | 'p'.
	 * @return array<string, mixed>
	 */
	protected function build_state( $settings, $p ) {
		$k     = 'eap_cc_' . $p . '_';
		$types = $this->get_types();
		$type  = $settings[ $k . 'type' ] ?? 'default';

		if ( ! isset( $types[ $type ] ) ) {
			$type = 'default';
		}

		$state = array(
			'type'    => $type,
			'html'    => '',
			'size'    => $this->size_of( $settings, $k . 'size', 32 ),
			'color'   => $settings[ $k . 'color' ] ?? '',
			'bg'      => $settings[ $k . 'bg' ] ?? '',
			'bw'      => $this->size_of( $settings, $k . 'border_width', 0 ),
			'bc'      => $settings[ $k . 'border_color' ] ?? '',
			'radius'  => $this->unit_size_of( $settings, $k . 'radius', '50%' ),
			'opacity' => $this->size_of( $settings, $k . 'opacity', 1 ),
		);

		if ( 'icon' === $type ) {
			$state['html'] = $this->render_icon( $settings[ $k . 'icon' ] ?? array() );
			// An unresolvable icon renders nothing at all; without this the cursor
			// would become an empty coloured box. (Same trap as Advanced Tooltip.)
			if ( '' === $state['html'] ) {
				$state['type'] = 'default';
			}
		} elseif ( 'image' === $type ) {
			$url = $settings[ $k . 'image' ]['url'] ?? '';
			if ( '' === $url ) {
				$state['type'] = 'default';
			} else {
				$state['url'] = esc_url( $url );
			}
		} elseif ( 'svg' === $type ) {
			$code = trim( (string) ( $settings[ $k . 'svg' ] ?? '' ) );
			$code = '' === $code ? '' : trim( wp_kses( $code, $this->get_svg_allowed_html() ) );
			if ( '' === $code ) {
				$state['type'] = 'default';
			} else {
				$state['html'] = $code;
			}
		}

		return $state;
	}

	/**
	 * Render an Icons_Manager icon to markup.
	 *
	 * @param array<string, mixed> $icon Icon control value.
	 * @return string Markup, or '' when the icon cannot be resolved.
	 */
	protected function render_icon( $icon ) {
		if ( empty( $icon['value'] ) || ! class_exists( '\Elementor\Icons_Manager' ) ) {
			return '';
		}

		ob_start();
		Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
		return trim( (string) ob_get_clean() );
	}

	/**
	 * Read a SLIDER control's numeric size.
	 *
	 * @param array<string, mixed> $settings Settings.
	 * @param string               $key      Control key.
	 * @param float                $fallback Fallback.
	 * @return float
	 */
	protected function size_of( $settings, $key, $fallback ) {
		$size = $settings[ $key ]['size'] ?? null;
		return ( is_numeric( $size ) ) ? (float) $size : (float) $fallback;
	}

	/**
	 * Read a SLIDER control as a CSS length, keeping its unit.
	 *
	 * @param array<string, mixed> $settings Settings.
	 * @param string               $key      Control key.
	 * @param string               $fallback Fallback.
	 * @return string
	 */
	protected function unit_size_of( $settings, $key, $fallback ) {
		$size = $settings[ $key ]['size'] ?? null;
		if ( ! is_numeric( $size ) ) {
			return $fallback;
		}
		$unit = $settings[ $key ]['unit'] ?? 'px';
		return (float) $size . ( in_array( $unit, array( 'px', '%', 'em', 'rem' ), true ) ? $unit : 'px' );
	}
}
