<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

/**
 * Advanced Tooltip extension.
 *
 * Adds an "Advanced Tooltip" section to the Advanced tab of EVERY element —
 * widgets, containers, sections and columns alike — whose content can be plain
 * text, an icon, an image or a shortcode.
 *
 * How it attaches: unlike Image Masking (which is pure CSS and needs only
 * `selectors`), a tooltip has real MARKUP, and there is no single filter that
 * can append markup to widgets AND containers. So the content is rendered
 * server-side into a `data-eap-tooltip` attribute on the element wrapper during
 * `elementor/frontend/before_render`, and advanced-tooltip.js builds the tooltip
 * node from it. That one mechanism works identically for every element type.
 *
 * Showing/hiding is CSS (`:hover`), so the JS only builds the node and handles
 * the optional click trigger.
 *
 * Enabled/disabled from the plugin's Extensions page via the `advanced-tooltip`
 * toggle (absent = on).
 */
class EAP_Advanced_Tooltip {

	const EXTENSION_KEY = 'advanced-tooltip';

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
	 * Register + enqueue the tooltip assets.
	 *
	 * Enqueued up front rather than on first use: `before_render` runs after
	 * wp_enqueue_scripts, so enqueuing there would print the stylesheet in the
	 * footer and flash unstyled tooltips. Both files are deliberately tiny.
	 *
	 * @return void
	 */
	public function register_assets() {
		wp_enqueue_style(
			'eap-advanced-tooltip',
			EAP_URL . 'assets/css/extensions/advanced-tooltip.css',
			array(),
			EAP_VERSION
		);

		wp_enqueue_script(
			'eap-advanced-tooltip',
			EAP_URL . 'assets/js/extensions/advanced-tooltip.js',
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
	 * Register the tooltip controls on an element.
	 *
	 * @param \Elementor\Controls_Stack $element Element.
	 * @return void
	 */
	protected function add_controls( $element ) {
		$element->start_controls_section(
			'eap_tooltip_section',
			array(
				'label' => __( 'Advanced Tooltip', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'eap_tooltip_enable',
			array(
				'label'        => __( 'Enable Tooltip', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$element->add_control(
			'eap_tooltip_content_type',
			array(
				'label'     => __( 'Content', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'text',
				'options'   => array(
					'text'      => __( 'Text', 'elementor-animatepro' ),
					'icon'      => __( 'Icon', 'elementor-animatepro' ),
					'image'     => __( 'Image', 'elementor-animatepro' ),
					'shortcode' => __( 'Shortcode', 'elementor-animatepro' ),
				),
				'condition' => array( 'eap_tooltip_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_tooltip_text',
			array(
				'label'     => __( 'Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 3,
				'default'   => __( 'Tooltip text', 'elementor-animatepro' ),
				'condition' => array(
					'eap_tooltip_enable'       => 'yes',
					'eap_tooltip_content_type' => 'text',
				),
			)
		);

		$element->add_control(
			'eap_tooltip_icon',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array(
					'eap_tooltip_enable'       => 'yes',
					'eap_tooltip_content_type' => 'icon',
				),
			)
		);

		$element->add_control(
			'eap_tooltip_image',
			array(
				'label'     => __( 'Image', 'elementor-animatepro' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'eap_tooltip_enable'       => 'yes',
					'eap_tooltip_content_type' => 'image',
				),
			)
		);

		$element->add_control(
			'eap_tooltip_shortcode',
			array(
				'label'       => __( 'Shortcode', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'placeholder' => '[my_shortcode]',
				'condition'   => array(
					'eap_tooltip_enable'       => 'yes',
					'eap_tooltip_content_type' => 'shortcode',
				),
			)
		);

		$element->add_control(
			'eap_tooltip_position',
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
				'separator' => 'before',
				'condition' => array( 'eap_tooltip_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_tooltip_trigger',
			array(
				'label'     => __( 'Trigger', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'hover',
				'options'   => array(
					'hover' => __( 'Hover', 'elementor-animatepro' ),
					'click' => __( 'Click', 'elementor-animatepro' ),
				),
				'condition' => array( 'eap_tooltip_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_tooltip_arrow',
			array(
				'label'        => __( 'Arrow', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'eap_tooltip_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'eap_tooltip_offset',
			array(
				'label'      => __( 'Distance', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} > .eap-tooltip' => '--eap-tt-offset: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'eap_tooltip_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'eap_tooltip_width',
			array(
				'label'      => __( 'Max Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 60, 'max' => 600 ) ),
				'default'    => array( 'size' => 220, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} > .eap-tooltip' => '--eap-tt-width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'eap_tooltip_enable' => 'yes' ),
			)
		);

		/* ---------- Style ---------- */

		$element->add_control(
			'eap_tooltip_style_heading',
			array(
				'label'     => __( 'Style', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'eap_tooltip_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_tooltip_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					// A var, so the CSS arrow can inherit the same colour.
					'{{WRAPPER}} > .eap-tooltip' => '--eap-tt-bg: {{VALUE}};',
				),
				'condition' => array( 'eap_tooltip_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_tooltip_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} > .eap-tooltip' => 'color: {{VALUE}};',
				),
				'condition' => array( 'eap_tooltip_enable' => 'yes' ),
			)
		);

		$element->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'eap_tooltip_typography',
				'selector'  => '{{WRAPPER}} > .eap-tooltip',
				'condition' => array( 'eap_tooltip_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'eap_tooltip_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 8, 'right' => 12, 'bottom' => 8, 'left' => 12, 'unit' => 'px', 'isLinked' => false ),
				'selectors'  => array(
					'{{WRAPPER}} > .eap-tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array( 'eap_tooltip_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'eap_tooltip_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 6, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} > .eap-tooltip' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'eap_tooltip_enable' => 'yes' ),
			)
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'eap_tooltip_border',
				'selector'  => '{{WRAPPER}} > .eap-tooltip',
				'condition' => array( 'eap_tooltip_enable' => 'yes' ),
			)
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'eap_tooltip_shadow',
				'selector'  => '{{WRAPPER}} > .eap-tooltip',
				'condition' => array( 'eap_tooltip_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_tooltip_zindex',
			array(
				'label'       => __( 'Z-Index', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 99999,
				'default'     => 99,
				'selectors'   => array(
					'{{WRAPPER}} > .eap-tooltip' => 'z-index: {{VALUE}};',
				),
				'description' => __( 'Raise this if the tooltip is covered by a neighbouring element.', 'elementor-animatepro' ),
				'condition'   => array( 'eap_tooltip_enable' => 'yes' ),
			)
		);

		$element->end_controls_section();
	}

	/* =====================================================================
	 * ATTACH
	 * ================================================================== */

	/**
	 * Stamp the rendered tooltip content + options onto the element wrapper.
	 *
	 * @param \Elementor\Element_Base $element Element.
	 * @return void
	 */
	public function attach( $element ) {
		if ( ! is_object( $element ) || ! method_exists( $element, 'get_settings_for_display' ) ) {
			return;
		}

		$settings = $element->get_settings_for_display();

		if ( 'yes' !== ( $settings['eap_tooltip_enable'] ?? '' ) ) {
			return;
		}

		$content = $this->build_content( $settings );
		if ( '' === trim( $content ) ) {
			return;
		}

		$element->add_render_attribute(
			'_wrapper',
			array(
				'class'                => 'eap-tooltip-host',
				'data-eap-tooltip'     => $content,
				'data-eap-tt-position' => in_array( $settings['eap_tooltip_position'] ?? 'top', array( 'top', 'bottom', 'left', 'right' ), true ) ? $settings['eap_tooltip_position'] : 'top',
				'data-eap-tt-trigger'  => 'click' === ( $settings['eap_tooltip_trigger'] ?? 'hover' ) ? 'click' : 'hover',
				'data-eap-tt-arrow'    => 'yes' === ( $settings['eap_tooltip_arrow'] ?? 'yes' ) ? '1' : '0',
			)
		);
	}

	/**
	 * Build the tooltip's inner HTML from the chosen content type.
	 *
	 * @param array $settings Element settings.
	 * @return string
	 */
	protected function build_content( $settings ) {
		$type = $settings['eap_tooltip_content_type'] ?? 'text';

		switch ( $type ) {
			case 'icon':
				$icon = $settings['eap_tooltip_icon'] ?? array();
				if ( empty( $icon['value'] ) || ! class_exists( '\Elementor\Icons_Manager' ) ) {
					return '';
				}

				// Icon fonts may not be on the page otherwise — this element is not
				// necessarily an icon widget.
				wp_enqueue_style( 'elementor-icons-fa-solid' );
				wp_enqueue_style( 'elementor-icons-fa-regular' );
				wp_enqueue_style( 'elementor-icons-fa-brands' );

				ob_start();
				\Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
				return '<span class="eap-tooltip__icon">' . ob_get_clean() . '</span>';

			case 'image':
				$url = $settings['eap_tooltip_image']['url'] ?? '';
				if ( '' === $url ) {
					return '';
				}
				return '<img class="eap-tooltip__image" src="' . esc_url( $url ) . '" alt="" />';

			case 'shortcode':
				$shortcode = trim( (string) ( $settings['eap_tooltip_shortcode'] ?? '' ) );
				if ( '' === $shortcode ) {
					return '';
				}
				return do_shortcode( $shortcode );

			case 'text':
			default:
				return wp_kses_post( (string) ( $settings['eap_tooltip_text'] ?? '' ) );
		}
	}
}
