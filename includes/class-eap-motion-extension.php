<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Element_Base;

class EAP_Motion_Extension {

	/**
	 * Module manager.
	 *
	 * @var EAP_Module_Manager
	 */
	private $modules;

	/**
	 * Constructor.
	 *
	 * @param EAP_Module_Manager $modules Module manager.
	 */
	public function __construct( EAP_Module_Manager $modules ) {
		$this->modules = $modules;

		if ( ! $this->modules->is_enabled( 'extensions_motion' ) ) {
			return;
		}

		add_action( 'elementor/element/after_section_end', array( $this, 'register_controls' ), 10, 3 );
		add_action( 'elementor/frontend/widget/before_render', array( $this, 'apply_render_attributes' ) );
		add_action( 'elementor/frontend/container/before_render', array( $this, 'apply_render_attributes' ) );
		add_action( 'elementor/frontend/section/before_render', array( $this, 'apply_render_attributes' ) );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'apply_render_attributes' ) );
	}

	/**
	 * Register motion controls.
	 *
	 * @param Element_Base $element Element.
	 * @return void
	 */
	public function register_controls( $element, $section_id, $args ) {
		$element_name = method_exists( $element, 'get_name' ) ? $element->get_name() : '';
		$is_common    = 'common' === $element_name && '_section_style' === $section_id;
		$is_layout    = in_array( $element_name, array( 'section', 'column', 'container' ), true ) && 'section_advanced' === $section_id;

		if ( ! $is_common && ! $is_layout ) {
			return;
		}

		$element->start_controls_section(
			'eap_motion_section',
			array(
				'label' => __( 'AnimatePro Motion', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'eap_motion_effect',
			array(
				'label'   => __( 'Entrance', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''            => __( 'None', 'elementor-animatepro' ),
					'fade-up'     => __( 'Fade Up', 'elementor-animatepro' ),
					'fade-in'     => __( 'Fade In', 'elementor-animatepro' ),
					'slide-left'  => __( 'Slide Left', 'elementor-animatepro' ),
					'slide-right' => __( 'Slide Right', 'elementor-animatepro' ),
					'zoom-in'     => __( 'Zoom In', 'elementor-animatepro' ),
				),
				'description' => __( 'Choose how the element enters the viewport.', 'elementor-animatepro' ),
			)
		);

		$element->add_control(
			'eap_motion_duration',
			array(
				'label'   => __( 'Duration (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 700,
				'min'     => 100,
				'step'    => 50,
				'description' => __( 'How long the entrance animation takes after it starts.', 'elementor-animatepro' ),
			)
		);

		$element->add_control(
			'eap_motion_delay',
			array(
				'label'   => __( 'Delay (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'step'    => 50,
				'description' => __( 'Delay the entrance animation after the element enters view.', 'elementor-animatepro' ),
			)
		);

		$element->add_control(
			'eap_hover_effect',
			array(
				'label'   => __( 'Hover Effect', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''       => __( 'None', 'elementor-animatepro' ),
					'lift'   => __( 'Lift', 'elementor-animatepro' ),
					'grow'   => __( 'Grow', 'elementor-animatepro' ),
					'tilt'   => __( 'Tilt', 'elementor-animatepro' ),
					'glow'   => __( 'Glow', 'elementor-animatepro' ),
				),
				'description' => __( 'Apply a simple hover response without writing CSS.', 'elementor-animatepro' ),
			)
		);

		$element->add_control(
			'eap_pin_enable',
			array(
				'label'        => __( 'Pin Element on Scroll', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Keep the element sticky while the surrounding content scrolls.', 'elementor-animatepro' ),
			)
		);

		$element->add_control(
			'eap_pin_offset',
			array(
				'label'       => __( 'Pin Offset (px)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 24,
				'condition'   => array( 'eap_pin_enable' => 'yes' ),
				'description' => __( 'Distance from the top of the viewport while pinned.', 'elementor-animatepro' ),
			)
		);

		$element->add_control(
			'eap_scroll_transform_enable',
			array(
				'label'        => __( 'Transform on Scroll', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Animate movement, scale, rotation, and opacity based on scroll progress.', 'elementor-animatepro' ),
			)
		);

		$element->add_control(
			'eap_scroll_translate_x',
			array(
				'label'       => __( 'Translate X (px)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'condition'   => array( 'eap_scroll_transform_enable' => 'yes' ),
				'description' => __( 'Horizontal movement between the start and end of the effect.', 'elementor-animatepro' ),
			)
		);

		$element->add_control(
			'eap_scroll_translate_y',
			array(
				'label'       => __( 'Translate Y (px)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => -80,
				'condition'   => array( 'eap_scroll_transform_enable' => 'yes' ),
				'description' => __( 'Vertical movement between the start and end of the effect.', 'elementor-animatepro' ),
			)
		);

		$element->add_control(
			'eap_scroll_rotate',
			array(
				'label'       => __( 'Rotate (deg)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'condition'   => array( 'eap_scroll_transform_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_scroll_scale_from',
			array(
				'label'       => __( 'Scale From', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0.92,
				'step'        => 0.01,
				'condition'   => array( 'eap_scroll_transform_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_scroll_scale_to',
			array(
				'label'       => __( 'Scale To', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1,
				'step'        => 0.01,
				'condition'   => array( 'eap_scroll_transform_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_cursor_follower_enable',
			array(
				'label'        => __( 'Custom Cursor Follower', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Show a visual follower while hovering this element.', 'elementor-animatepro' ),
			)
		);

		$element->add_control(
			'eap_cursor_follower_type',
			array(
				'label'       => __( 'Follower Type', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'icon',
				'options'     => array(
					'icon'  => __( 'Icon', 'elementor-animatepro' ),
					'image' => __( 'Image', 'elementor-animatepro' ),
					'text'  => __( 'Text', 'elementor-animatepro' ),
				),
				'condition'   => array( 'eap_cursor_follower_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_cursor_follower_icon',
			array(
				'label'       => __( 'Follower Icon', 'elementor-animatepro' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
				'condition'   => array(
					'eap_cursor_follower_enable' => 'yes',
					'eap_cursor_follower_type'   => 'icon',
				),
			)
		);

		$element->add_control(
			'eap_cursor_follower_image',
			array(
				'label'       => __( 'Follower Image', 'elementor-animatepro' ),
				'type'        => Controls_Manager::MEDIA,
				'condition'   => array(
					'eap_cursor_follower_enable' => 'yes',
					'eap_cursor_follower_type'   => 'image',
				),
			)
		);

		$element->add_control(
			'eap_cursor_follower_text',
			array(
				'label'       => __( 'Follower Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'View', 'elementor-animatepro' ),
				'condition'   => array(
					'eap_cursor_follower_enable' => 'yes',
					'eap_cursor_follower_type'   => 'text',
				),
			)
		);

		$element->add_control(
			'eap_cursor_follower_size',
			array(
				'label'       => __( 'Follower Size (px)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 70,
				'condition'   => array( 'eap_cursor_follower_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_cursor_follower_offset_x',
			array(
				'label'       => __( 'Horizontal Distance (px)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 24,
				'condition'   => array( 'eap_cursor_follower_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_cursor_follower_offset_y',
			array(
				'label'       => __( 'Vertical Distance (px)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 24,
				'condition'   => array( 'eap_cursor_follower_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_wrapper_link',
			array(
				'label'         => __( 'Wrapper Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://example.com',
				'show_external' => true,
				'description'   => __( 'Turn the whole element into a clickable block.', 'elementor-animatepro' ),
			)
		);

		$element->end_controls_section();
	}

	/**
	 * Apply render attributes for motion features.
	 *
	 * @param Element_Base $element Element.
	 * @return void
	 */
	public function apply_render_attributes( $element ) {
		$settings = $element->get_settings_for_display();

		$effect = ! empty( $settings['eap_motion_effect'] ) ? sanitize_key( $settings['eap_motion_effect'] ) : '';
		$hover  = ! empty( $settings['eap_hover_effect'] ) ? sanitize_key( $settings['eap_hover_effect'] ) : '';
		$link   = ! empty( $settings['eap_wrapper_link']['url'] ) ? $settings['eap_wrapper_link'] : array();

		if ( ! $effect && ! $hover && empty( $link['url'] ) ) {
			$has_pin       = ! empty( $settings['eap_pin_enable'] );
			$has_transform = ! empty( $settings['eap_scroll_transform_enable'] );
			$has_cursor    = ! empty( $settings['eap_cursor_follower_enable'] );
			if ( ! $has_pin && ! $has_transform && ! $has_cursor ) {
				return;
			}
		}

		EAP_Assets::instance()->enqueue_runtime_assets();

		if ( $effect ) {
			$element->add_render_attribute(
				'_wrapper',
				array(
					'class'              => 'eap-motion eap-motion-' . $effect,
					'data-eap-effect'    => $effect,
					'data-eap-duration'  => isset( $settings['eap_motion_duration'] ) ? absint( $settings['eap_motion_duration'] ) : 700,
					'data-eap-delay'     => isset( $settings['eap_motion_delay'] ) ? absint( $settings['eap_motion_delay'] ) : 0,
				)
			);
		}

		if ( $hover ) {
			$element->add_render_attribute(
				'_wrapper',
				array(
					'class'            => 'eap-hover eap-hover-' . $hover,
					'data-eap-hover'   => $hover,
				)
			);
		}

		if ( ! empty( $link['url'] ) ) {
			$attrs = array(
				'class'            => 'eap-wrapper-link',
				'data-eap-link'    => esc_url( $link['url'] ),
			);

			if ( ! empty( $link['is_external'] ) ) {
				$attrs['data-eap-target'] = '_blank';
			}

			if ( ! empty( $link['nofollow'] ) ) {
				$attrs['data-eap-rel'] = 'nofollow';
			}

			$element->add_render_attribute( '_wrapper', $attrs );
		}

		if ( ! empty( $settings['eap_pin_enable'] ) ) {
			$element->add_render_attribute(
				'_wrapper',
				array(
					'class'               => 'eap-pin',
					'data-eap-pin'        => 'true',
					'data-eap-pin-offset' => isset( $settings['eap_pin_offset'] ) ? absint( $settings['eap_pin_offset'] ) : 24,
				)
			);
		}

		if ( ! empty( $settings['eap_scroll_transform_enable'] ) ) {
			$element->add_render_attribute(
				'_wrapper',
				array(
					'class'                    => 'eap-scroll-transform',
					'data-eap-transform'       => 'true',
					'data-eap-translate-x'     => isset( $settings['eap_scroll_translate_x'] ) ? floatval( $settings['eap_scroll_translate_x'] ) : 0,
					'data-eap-translate-y'     => isset( $settings['eap_scroll_translate_y'] ) ? floatval( $settings['eap_scroll_translate_y'] ) : -80,
					'data-eap-rotate'          => isset( $settings['eap_scroll_rotate'] ) ? floatval( $settings['eap_scroll_rotate'] ) : 0,
					'data-eap-scale-from'      => isset( $settings['eap_scroll_scale_from'] ) ? floatval( $settings['eap_scroll_scale_from'] ) : 0.92,
					'data-eap-scale-to'        => isset( $settings['eap_scroll_scale_to'] ) ? floatval( $settings['eap_scroll_scale_to'] ) : 1,
				)
			);
		}

		if ( ! empty( $settings['eap_cursor_follower_enable'] ) ) {
			$follower_markup = '';

			if ( ! empty( $settings['eap_cursor_follower_type'] ) && 'image' === $settings['eap_cursor_follower_type'] && ! empty( $settings['eap_cursor_follower_image']['url'] ) ) {
				$follower_markup = '<img src="' . esc_url( $settings['eap_cursor_follower_image']['url'] ) . '" alt="" />';
			} elseif ( ! empty( $settings['eap_cursor_follower_type'] ) && 'text' === $settings['eap_cursor_follower_type'] ) {
				$follower_markup = '<span>' . esc_html( $settings['eap_cursor_follower_text'] ) . '</span>';
			} elseif ( ! empty( $settings['eap_cursor_follower_icon']['value'] ) ) {
				$follower_markup = '<i class="' . esc_attr( $settings['eap_cursor_follower_icon']['value'] ) . '" aria-hidden="true"></i>';
			}

			$element->add_render_attribute(
				'_wrapper',
				array(
					'class'                    => 'eap-cursor-host',
					'data-eap-cursor'         => wp_kses_post( $follower_markup ),
					'data-eap-cursor-size'    => isset( $settings['eap_cursor_follower_size'] ) ? absint( $settings['eap_cursor_follower_size'] ) : 70,
					'data-eap-cursor-offset-x'=> isset( $settings['eap_cursor_follower_offset_x'] ) ? absint( $settings['eap_cursor_follower_offset_x'] ) : 24,
					'data-eap-cursor-offset-y'=> isset( $settings['eap_cursor_follower_offset_y'] ) ? absint( $settings['eap_cursor_follower_offset_y'] ) : 24,
				)
			);
		}
	}
}
