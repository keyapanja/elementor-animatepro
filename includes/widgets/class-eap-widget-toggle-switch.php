<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/**
 * Toggle Switch widget.
 *
 * A standalone two-state control that drives OTHER elements on the page.
 *
 * How this differs from Content Toggle, which is also a two-state switch:
 * Content Toggle CONTAINS its states — each is a droppable Elementor container
 * nested inside the widget. This one contains nothing; it points at elements
 * elsewhere. That is what you want when the two states are sections you have
 * already built, when they are not adjacent, or when the thing being switched
 * is not content at all (a dark-mode class on <html>). Neither replaces the
 * other, and the panel says which to reach for.
 *
 * Three actions:
 *   - Toggle Class     — add/remove a class on whatever a selector matches.
 *   - Show / Hide      — reveal one target and hide the other.
 *   - Dark Mode        — a Toggle Class preset aimed at <html>, remembered.
 */
class EAP_Widget_Toggle_Switch extends EAP_Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-toggle-switch';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Toggle Switch', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-toggle';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'toggle', 'switch', 'dark mode', 'show', 'hide', 'checkbox' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-toggle-switch' );
	}

	/**
	 * Scripts.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-toggle-switch-script' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_content_section();
		$this->register_action_section();

		$this->register_track_style();
		$this->register_label_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	/**
	 * Appearance section.
	 *
	 * @return void
	 */
	protected function register_content_section() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Switch', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'style_mode',
			array(
				'label'   => __( 'Style', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'switch',
				'options' => array(
					'switch' => __( 'Switch', 'elementor-animatepro' ),
					'pills'  => __( 'Two Pills', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'label_off',
			array(
				'label'   => __( 'Off Label', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Monthly', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'label_on',
			array(
				'label'   => __( 'On Label', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Yearly', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'default_state',
			array(
				'label'   => __( 'Starts', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'off',
				'options' => array(
					'off' => __( 'Off', 'elementor-animatepro' ),
					'on'  => __( 'On', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'remember',
			array(
				'label'        => __( 'Remember Choice', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Stores the state in the browser so it survives a reload.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'remember_key',
			array(
				'label'       => __( 'Storage Key', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'eap-toggle',
				'description' => __( 'Give two switches the same key to keep them in step, or different keys to keep them independent.', 'elementor-animatepro' ),
				'condition'   => array( 'remember' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'flex-start' => array( 'title' => __( 'Left', 'elementor-animatepro' ), 'icon' => 'eicon-text-align-left' ),
					'center'     => array( 'title' => __( 'Center', 'elementor-animatepro' ), 'icon' => 'eicon-text-align-center' ),
					'flex-end'   => array( 'title' => __( 'Right', 'elementor-animatepro' ), 'icon' => 'eicon-text-align-right' ),
				),
				'selectors' => array( '{{WRAPPER}} .eap-toggle-switch' => 'justify-content: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Action section.
	 *
	 * @return void
	 */
	protected function register_action_section() {
		$this->start_controls_section(
			'section_action',
			array( 'label' => __( 'What It Controls', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'action',
			array(
				'label'   => __( 'Action', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'visibility',
				'options' => array(
					'visibility' => __( 'Show One, Hide The Other', 'elementor-animatepro' ),
					'class'      => __( 'Toggle A Class', 'elementor-animatepro' ),
					'dark'       => __( 'Dark Mode', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'nested_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'This switch controls elements elsewhere on the page. If you would rather build both states <em>inside</em> the widget as droppable containers, use the Content Toggle widget instead.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		/* ---------- Show / hide ---------- */

		$this->add_control(
			'target_off',
			array(
				'label'       => __( 'Shown When Off', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '.monthly-pricing',
				'description' => __( 'A CSS selector — usually a CSS class you added to a container under Advanced → CSS Classes.', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array( 'action' => 'visibility' ),
			)
		);

		$this->add_control(
			'target_on',
			array(
				'label'       => __( 'Shown When On', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '.yearly-pricing',
				'label_block' => true,
				'condition'   => array( 'action' => 'visibility' ),
			)
		);

		/* ---------- Class ---------- */

		$this->add_control(
			'target_selector',
			array(
				'label'       => __( 'Target', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'body',
				'placeholder' => 'body',
				'description' => __( 'A CSS selector. Every match gets the class.', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array( 'action' => 'class' ),
			)
		);

		$this->add_control(
			'class_name',
			array(
				'label'       => __( 'Class To Toggle', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'is-toggled',
				'placeholder' => 'is-toggled',
				'label_block' => true,
				'condition'   => array( 'action' => 'class' ),
			)
		);

		/* ---------- Dark mode ---------- */

		$this->add_control(
			'dark_class',
			array(
				'label'       => __( 'Dark Class', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'eap-dark',
				'description' => __( 'Added to the &lt;html&gt; element. Style your dark theme from it, for example <code>.eap-dark body { … }</code>.', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array( 'action' => 'dark' ),
			)
		);

		$this->add_control(
			'dark_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Dark Mode remembers the choice automatically. The class is applied by script, so a reload can briefly show the light theme before it lands.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'action' => 'dark' ),
			)
		);

		$this->add_control(
			'hide_animation',
			array(
				'label'     => __( 'Switch Animation', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => array(
					'none' => __( 'None', 'elementor-animatepro' ),
					'fade' => __( 'Fade', 'elementor-animatepro' ),
				),
				'separator' => 'before',
				'condition' => array( 'action' => 'visibility' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	/**
	 * Track / knob style.
	 *
	 * @return void
	 */
	protected function register_track_style() {
		$this->start_controls_section(
			'style_track',
			array(
				'label' => __( 'Control', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'track_width',
			array(
				'label'      => __( 'Track Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 34, 'max' => 120 ) ),
				'default'    => array( 'size' => 56, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-ts-w: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'style_mode' => 'switch' ),
			)
		);

		$this->add_responsive_control(
			'track_height',
			array(
				'label'      => __( 'Track Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 18, 'max' => 64 ) ),
				'default'    => array( 'size' => 30, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-ts-h: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'style_mode' => 'switch' ),
			)
		);

		$this->add_control(
			'track_off_color',
			array(
				'label'     => __( 'Track Off', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d1d5db',
				'selectors' => array( '{{WRAPPER}}' => '--eap-ts-off: {{VALUE}};' ),
				'condition' => array( 'style_mode' => 'switch' ),
			)
		);

		$this->add_control(
			'track_on_color',
			array(
				'label'     => __( 'Track On', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array( '{{WRAPPER}}' => '--eap-ts-on: {{VALUE}};' ),
				'condition' => array( 'style_mode' => 'switch' ),
			)
		);

		$this->add_control(
			'knob_color',
			array(
				'label'     => __( 'Knob', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}}' => '--eap-ts-knob: {{VALUE}};' ),
				'condition' => array( 'style_mode' => 'switch' ),
			)
		);

		/* ---------- Pills ---------- */

		$this->add_control(
			'pill_color',
			array(
				'label'     => __( 'Pill Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-toggle-switch__pill' => 'color: {{VALUE}};' ),
				'condition' => array( 'style_mode' => 'pills' ),
			)
		);

		$this->add_control(
			'pill_bg',
			array(
				'label'     => __( 'Pill Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-toggle-switch__pills' => 'background-color: {{VALUE}};' ),
				'condition' => array( 'style_mode' => 'pills' ),
			)
		);

		$this->add_control(
			'pill_active_color',
			array(
				'label'     => __( 'Active Pill Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-toggle-switch__pill.is-active' => 'color: {{VALUE}};' ),
				'condition' => array( 'style_mode' => 'pills' ),
			)
		);

		$this->add_control(
			'pill_active_bg',
			array(
				'label'     => __( 'Active Pill Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array( '{{WRAPPER}} .eap-toggle-switch__pill.is-active' => 'background-color: {{VALUE}};' ),
				'condition' => array( 'style_mode' => 'pills' ),
			)
		);

		$this->add_responsive_control(
			'pill_padding',
			array(
				'label'      => __( 'Pill Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 9,
					'right'    => 22,
					'bottom'   => 9,
					'left'     => 22,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-toggle-switch__pill' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array( 'style_mode' => 'pills' ),
			)
		);

		$this->add_responsive_control(
			'radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 999, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-ts-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Label style.
	 *
	 * @return void
	 */
	protected function register_label_style() {
		$this->start_controls_section(
			'style_labels',
			array(
				'label' => __( 'Labels', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .eap-toggle-switch__label, {{WRAPPER}} .eap-toggle-switch__pill',
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-toggle-switch__label' => 'color: {{VALUE}};' ),
				'condition' => array( 'style_mode' => 'switch' ),
			)
		);

		$this->add_control(
			'label_active_color',
			array(
				'label'     => __( 'Active Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-toggle-switch__label.is-active' => 'color: {{VALUE}};' ),
				'condition' => array( 'style_mode' => 'switch' ),
			)
		);

		$this->add_responsive_control(
			'label_gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-ts-gap: {{SIZE}}{{UNIT}};' ),
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
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$mode   = ( 'pills' === ( $settings['style_mode'] ?? 'switch' ) ) ? 'pills' : 'switch';
		$action = in_array( $settings['action'] ?? 'visibility', array( 'visibility', 'class', 'dark' ), true )
			? $settings['action']
			: 'visibility';

		$on       = ( 'on' === ( $settings['default_state'] ?? 'off' ) );
		$label_on = (string) ( $settings['label_on'] ?? '' );
		$label_off = (string) ( $settings['label_off'] ?? '' );

		// Dark mode is meaningless without persistence, so it is always remembered
		// regardless of the toggle — the control is hidden for it in the panel.
		$remember = ( 'dark' === $action ) || 'yes' === ( $settings['remember'] ?? '' );

		$config = array(
			'action'    => $action,
			'on'        => $on,
			'remember'  => $remember,
			'key'       => $remember ? $this->storage_key( $settings, $action ) : '',
			'animate'   => ( 'none' !== ( $settings['hide_animation'] ?? 'fade' ) ),
			'targetOff' => trim( (string) ( $settings['target_off'] ?? '' ) ),
			'targetOn'  => trim( (string) ( $settings['target_on'] ?? '' ) ),
			'selector'  => trim( (string) ( $settings['target_selector'] ?? 'body' ) ),
			'className' => $this->sanitize_class_list( $settings['class_name'] ?? '' ),
			'darkClass' => $this->sanitize_class_list( $settings['dark_class'] ?? 'eap-dark' ),
		);

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'                  => array( 'eap-widget', 'eap-toggle-switch-wrap', 'eap-toggle-switch--' . $mode ),
				'data-eap-toggle-switch' => wp_json_encode( $config ),
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="eap-toggle-switch">
				<?php if ( 'pills' === $mode ) : ?>
					<div class="eap-toggle-switch__pills" role="group">
						<button type="button" class="eap-toggle-switch__pill<?php echo $on ? '' : ' is-active'; ?>"
							data-state="off" aria-pressed="<?php echo $on ? 'false' : 'true'; ?>">
							<?php echo esc_html( $label_off ); ?>
						</button>
						<button type="button" class="eap-toggle-switch__pill<?php echo $on ? ' is-active' : ''; ?>"
							data-state="on" aria-pressed="<?php echo $on ? 'true' : 'false'; ?>">
							<?php echo esc_html( $label_on ); ?>
						</button>
					</div>
				<?php else : ?>
					<?php if ( '' !== $label_off ) : ?>
						<span class="eap-toggle-switch__label eap-toggle-switch__label--off<?php echo $on ? '' : ' is-active'; ?>"><?php echo esc_html( $label_off ); ?></span>
					<?php endif; ?>

					<?php
					/*
					 * A real button with role="switch" — it is focusable, Space and
					 * Enter activate it for free, and screen readers announce the
					 * state. A styled div would have needed all of that added back by
					 * hand and would still not be announced correctly.
					 */
					?>
					<button type="button" class="eap-toggle-switch__control" role="switch"
						aria-checked="<?php echo $on ? 'true' : 'false'; ?>"
						aria-label="<?php echo esc_attr( $this->control_label( $label_off, $label_on ) ); ?>">
						<span class="eap-toggle-switch__knob" aria-hidden="true"></span>
					</button>

					<?php if ( '' !== $label_on ) : ?>
						<span class="eap-toggle-switch__label eap-toggle-switch__label--on<?php echo $on ? ' is-active' : ''; ?>"><?php echo esc_html( $label_on ); ?></span>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Accessible name for the switch.
	 *
	 * @param string $off Off label.
	 * @param string $on  On label.
	 * @return string
	 */
	protected function control_label( $off, $on ) {
		if ( '' !== $off && '' !== $on ) {
			/* translators: 1: off label, 2: on label. */
			return sprintf( __( 'Switch between %1$s and %2$s', 'elementor-animatepro' ), $off, $on );
		}
		return __( 'Toggle', 'elementor-animatepro' );
	}

	/**
	 * The localStorage key for this switch.
	 *
	 * Dark mode gets a shared, fixed key so every dark-mode switch on the site
	 * agrees; other actions use the author's key.
	 *
	 * @param array  $settings Settings.
	 * @param string $action   Action.
	 * @return string
	 */
	protected function storage_key( $settings, $action ) {
		if ( 'dark' === $action ) {
			return 'eap-dark-mode';
		}

		$key = trim( (string) ( $settings['remember_key'] ?? '' ) );
		return '' !== $key ? sanitize_key( $key ) : 'eap-toggle';
	}

	/**
	 * Reduce a free-text class field to a safe, space-separated class list.
	 *
	 * The value reaches the browser as JSON and is handed to classList, which
	 * throws on an empty or whitespace-bearing token — so anything that is not a
	 * plain class name is dropped here rather than breaking the switch at runtime.
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	protected function sanitize_class_list( $value ) {
		$parts = preg_split( '/\s+/', trim( (string) $value ) );
		$out   = array();

		foreach ( (array) $parts as $part ) {
			$clean = sanitize_html_class( $part );
			if ( '' !== $clean ) {
				$out[] = $clean;
			}
		}

		return implode( ' ', $out );
	}
}
