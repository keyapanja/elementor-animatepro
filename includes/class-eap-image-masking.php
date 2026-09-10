<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

/**
 * Image Masking extension.
 *
 * Injects an "Image Masking" section into the Advanced tab of every element,
 * masking its `<img>` elements with either a CSS clip-path shape or an uploaded
 * mask image, with separate Normal / Hover states. (It cannot be limited to
 * image widgets — see maybe_inject() — but is inert without an image.)
 *
 * This is an EXTENSION, not a widget: it has no render of its own, no CSS file
 * and no JS. Masking is pure CSS, so every control simply declares `selectors`
 * and Elementor writes the rules into the page's generated stylesheet. Both the
 * `-webkit-` and unprefixed properties are emitted, since Safari still needs the
 * prefixed mask properties.
 *
 * Enabled/disabled from the plugin's Extensions page via the `image-masking`
 * toggle (absent = on), the same gate the Video Story post type uses.
 */
class EAP_Image_Masking {

	const EXTENSION_KEY = 'image-masking';

	/**
	 * Elements already given the section this request, keyed by object hash.
	 *
	 * The controls are injected on whichever anchor section fires first, so this
	 * stops a second anchor adding them twice.
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
	 * Add the section once, after a section every element has.
	 *
	 * NOTE: this cannot be limited to image widgets. `_section_style` and
	 * `_section_responsive` are registered on Elementor's COMMON controls stack
	 * (includes/widgets/common-base.php), so the element passed here reports
	 * get_name() === 'common' for every widget — a per-widget name check can
	 * never match, and silently injects nowhere. The section is therefore
	 * registered for all elements; the controls only ever emit CSS for `img`, so
	 * it is inert on anything without an image.
	 *
	 * @param \Elementor\Controls_Stack $element    Element.
	 * @param string                    $section_id Section that just closed.
	 * @return void
	 */
	public function maybe_inject( $element, $section_id ) {
		// Anchors on the Advanced tab that every element carries; whichever fires
		// first wins, and $injected keeps it to one.
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
	 * Register the Image Masking controls on an element.
	 *
	 * @param \Elementor\Controls_Stack $element Element.
	 * @return void
	 */
	protected function add_controls( $element ) {
		$element->start_controls_section(
			'eap_image_masking',
			array(
				'label' => __( 'Image Masking', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'eap_mask_enable',
			array(
				'label'        => __( 'Enable Image Masking', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$element->add_control(
			'eap_mask_type',
			array(
				'label'     => __( 'Type', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'clip',
				'options'   => array(
					'clip' => array(
						'title' => __( 'Clip Path', 'elementor-animatepro' ),
						'icon'  => 'eicon-shape',
					),
					'mask' => array(
						'title' => __( 'Mask Image', 'elementor-animatepro' ),
						'icon'  => 'eicon-image',
					),
				),
				'toggle'    => false,
				'condition' => array( 'eap_mask_enable' => 'yes' ),
			)
		);

		$element->start_controls_tabs(
			'eap_mask_tabs',
			array( 'condition' => array( 'eap_mask_enable' => 'yes' ) )
		);

		/* ---------- Normal ---------- */

		$element->start_controls_tab(
			'eap_mask_tab_normal',
			array(
				'label'     => __( 'Normal', 'elementor-animatepro' ),
				'condition' => array( 'eap_mask_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_mask_shape',
			array(
				'label'                => __( 'Shape', 'elementor-animatepro' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'circle',
				'options'              => self::get_shape_labels(),
				'selectors_dictionary' => self::get_shape_values(),
				'selectors'            => array(
					'{{WRAPPER}} img' => '-webkit-clip-path: {{VALUE}}; clip-path: {{VALUE}};',
				),
				'condition'            => array(
					'eap_mask_enable' => 'yes',
					'eap_mask_type'   => 'clip',
				),
			)
		);

		$element->add_control(
			'eap_mask_custom_clip_enable',
			array(
				'label'        => __( 'Use Custom Clip Path', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'eap_mask_enable' => 'yes',
					'eap_mask_type'   => 'clip',
				),
			)
		);

		$element->add_control(
			'eap_mask_custom_clip',
			array(
				'label'       => __( 'Custom Clip Path', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'placeholder' => 'polygon(50% 0%, 100% 100%, 0% 100%)',
				'description' => __( 'Any CSS clip-path value — polygon(), circle(), ellipse() or path().', 'elementor-animatepro' ),
				'selectors'   => array(
					'{{WRAPPER}} img' => '-webkit-clip-path: {{VALUE}}; clip-path: {{VALUE}};',
				),
				'condition'   => array(
					'eap_mask_enable'             => 'yes',
					'eap_mask_type'               => 'clip',
					'eap_mask_custom_clip_enable' => 'yes',
				),
			)
		);

		$element->add_control(
			'eap_mask_image',
			array(
				'label'     => __( 'Mask Image', 'elementor-animatepro' ),
				'type'      => Controls_Manager::MEDIA,
				'selectors' => array(
					'{{WRAPPER}} img' => '-webkit-mask-image: url({{URL}}); mask-image: url({{URL}});',
				),
				'condition' => array(
					'eap_mask_enable' => 'yes',
					'eap_mask_type'   => 'mask',
				),
			)
		);

		$element->end_controls_tab();

		/* ---------- Hover ---------- */

		$element->start_controls_tab(
			'eap_mask_tab_hover',
			array(
				'label'     => __( 'Hover', 'elementor-animatepro' ),
				'condition' => array( 'eap_mask_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_mask_shape_hover',
			array(
				'label'                => __( 'Shape', 'elementor-animatepro' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => '',
				'options'              => array( '' => __( 'Same as normal', 'elementor-animatepro' ) ) + self::get_shape_labels(),
				'selectors_dictionary' => self::get_shape_values(),
				'selectors'            => array(
					'{{WRAPPER}}:hover img' => '-webkit-clip-path: {{VALUE}}; clip-path: {{VALUE}};',
				),
				'condition'            => array(
					'eap_mask_enable' => 'yes',
					'eap_mask_type'   => 'clip',
				),
			)
		);

		$element->add_control(
			'eap_mask_image_hover',
			array(
				'label'     => __( 'Mask Image', 'elementor-animatepro' ),
				'type'      => Controls_Manager::MEDIA,
				'selectors' => array(
					'{{WRAPPER}}:hover img' => '-webkit-mask-image: url({{URL}}); mask-image: url({{URL}});',
				),
				'condition' => array(
					'eap_mask_enable' => 'yes',
					'eap_mask_type'   => 'mask',
				),
			)
		);

		$element->add_control(
			'eap_mask_transition',
			array(
				'label'      => __( 'Transition Duration', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array( 's' => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ) ),
				'default'    => array( 'size' => 0.4, 'unit' => 's' ),
				'selectors'  => array(
					'{{WRAPPER}} img' => 'transition: clip-path {{SIZE}}{{UNIT}} ease, -webkit-clip-path {{SIZE}}{{UNIT}} ease, -webkit-mask-position {{SIZE}}{{UNIT}} ease, mask-position {{SIZE}}{{UNIT}} ease;',
				),
				'description' => __( 'Clip-path only animates between shapes with the same number of points.', 'elementor-animatepro' ),
				'condition'  => array( 'eap_mask_enable' => 'yes' ),
			)
		);

		$element->end_controls_tab();

		$element->end_controls_tabs();

		/* ---------- Mask image placement ---------- */

		$element->add_control(
			'eap_mask_size',
			array(
				'label'     => __( 'Size', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'contain',
				'options'   => array(
					'contain' => __( 'Contain', 'elementor-animatepro' ),
					'cover'   => __( 'Cover', 'elementor-animatepro' ),
					'auto'    => __( 'Auto', 'elementor-animatepro' ),
					'100% 100%' => __( 'Stretch', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} img' => '-webkit-mask-size: {{VALUE}}; mask-size: {{VALUE}};',
				),
				'separator' => 'before',
				'condition' => array(
					'eap_mask_enable' => 'yes',
					'eap_mask_type'   => 'mask',
				),
			)
		);

		$element->add_control(
			'eap_mask_position',
			array(
				'label'     => __( 'Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center center',
				'options'   => array(
					'center center' => __( 'Center Center', 'elementor-animatepro' ),
					'center left'   => __( 'Center Left', 'elementor-animatepro' ),
					'center right'  => __( 'Center Right', 'elementor-animatepro' ),
					'top center'    => __( 'Top Center', 'elementor-animatepro' ),
					'top left'      => __( 'Top Left', 'elementor-animatepro' ),
					'top right'     => __( 'Top Right', 'elementor-animatepro' ),
					'bottom center' => __( 'Bottom Center', 'elementor-animatepro' ),
					'bottom left'   => __( 'Bottom Left', 'elementor-animatepro' ),
					'bottom right'  => __( 'Bottom Right', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} img' => '-webkit-mask-position: {{VALUE}}; mask-position: {{VALUE}};',
				),
				'condition' => array(
					'eap_mask_enable' => 'yes',
					'eap_mask_type'   => 'mask',
				),
			)
		);

		$element->add_control(
			'eap_mask_repeat',
			array(
				'label'     => __( 'Repeat', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'no-repeat',
				'options'   => array(
					'no-repeat' => __( 'No-repeat', 'elementor-animatepro' ),
					'repeat'    => __( 'Repeat', 'elementor-animatepro' ),
					'repeat-x'  => __( 'Repeat-x', 'elementor-animatepro' ),
					'repeat-y'  => __( 'Repeat-y', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} img' => '-webkit-mask-repeat: {{VALUE}}; mask-repeat: {{VALUE}};',
				),
				'condition' => array(
					'eap_mask_enable' => 'yes',
					'eap_mask_type'   => 'mask',
				),
			)
		);

		$element->end_controls_section();
	}

	/**
	 * Shape labels for the select.
	 *
	 * @return array<string, string>
	 */
	public static function get_shape_labels() {
		return array(
			'circle'        => __( 'Circle', 'elementor-animatepro' ),
			'ellipse'       => __( 'Ellipse', 'elementor-animatepro' ),
			'triangle'      => __( 'Triangle', 'elementor-animatepro' ),
			'diamond'       => __( 'Diamond', 'elementor-animatepro' ),
			'pentagon'      => __( 'Pentagon', 'elementor-animatepro' ),
			'hexagon'       => __( 'Hexagon', 'elementor-animatepro' ),
			'heptagon'      => __( 'Heptagon', 'elementor-animatepro' ),
			'octagon'       => __( 'Octagon', 'elementor-animatepro' ),
			'star'          => __( 'Star', 'elementor-animatepro' ),
			'plus'          => __( 'Plus', 'elementor-animatepro' ),
			'cross'         => __( 'Cross', 'elementor-animatepro' ),
			'message'       => __( 'Message', 'elementor-animatepro' ),
			'chevron-left'  => __( 'Chevron Left', 'elementor-animatepro' ),
			'chevron-right' => __( 'Chevron Right', 'elementor-animatepro' ),
			'arrow-right'   => __( 'Arrow Right', 'elementor-animatepro' ),
			'trapezoid'     => __( 'Trapezoid', 'elementor-animatepro' ),
			'parallelogram' => __( 'Parallelogram', 'elementor-animatepro' ),
			'bevel'         => __( 'Bevel', 'elementor-animatepro' ),
			'rabbet'        => __( 'Rabbet', 'elementor-animatepro' ),
			'frame'         => __( 'Frame', 'elementor-animatepro' ),
		);
	}

	/**
	 * Shape key => CSS clip-path value.
	 *
	 * @return array<string, string>
	 */
	public static function get_shape_values() {
		return array(
			'circle'        => 'circle(50% at 50% 50%)',
			'ellipse'       => 'ellipse(45% 40% at 50% 50%)',
			'triangle'      => 'polygon(50% 0%, 0% 100%, 100% 100%)',
			'diamond'       => 'polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%)',
			'pentagon'      => 'polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%)',
			'hexagon'       => 'polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%)',
			'heptagon'      => 'polygon(50% 0%, 90% 20%, 100% 60%, 75% 100%, 25% 100%, 0% 60%, 10% 20%)',
			'octagon'       => 'polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%)',
			'star'          => 'polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%)',
			'plus'          => 'polygon(35% 0%, 65% 0%, 65% 35%, 100% 35%, 100% 65%, 65% 65%, 65% 100%, 35% 100%, 35% 65%, 0% 65%, 0% 35%, 35% 35%)',
			'cross'         => 'polygon(20% 0%, 0% 20%, 30% 50%, 0% 80%, 20% 100%, 50% 70%, 80% 100%, 100% 80%, 70% 50%, 100% 20%, 80% 0%, 50% 30%)',
			'message'       => 'polygon(0% 0%, 100% 0%, 100% 75%, 75% 75%, 75% 100%, 50% 75%, 0% 75%)',
			'chevron-left'  => 'polygon(100% 0%, 75% 50%, 100% 100%, 25% 100%, 0% 50%, 25% 0%)',
			'chevron-right' => 'polygon(75% 0%, 100% 50%, 75% 100%, 0% 100%, 25% 50%, 0% 0%)',
			'arrow-right'   => 'polygon(0% 20%, 60% 20%, 60% 0%, 100% 50%, 60% 100%, 60% 80%, 0% 80%)',
			'trapezoid'     => 'polygon(20% 0%, 80% 0%, 100% 100%, 0% 100%)',
			'parallelogram' => 'polygon(25% 0%, 100% 0%, 75% 100%, 0% 100%)',
			'bevel'         => 'polygon(20% 0%, 80% 0%, 100% 20%, 100% 80%, 80% 100%, 20% 100%, 0% 80%, 0% 20%)',
			'rabbet'        => 'polygon(0% 15%, 15% 15%, 15% 0%, 85% 0%, 85% 15%, 100% 15%, 100% 85%, 85% 85%, 85% 100%, 15% 100%, 15% 85%, 0% 85%)',
			'frame'         => 'polygon(0% 0%, 0% 100%, 25% 100%, 25% 25%, 75% 25%, 75% 75%, 25% 75%, 25% 100%, 100% 100%, 100% 0%)',
		);
	}
}
