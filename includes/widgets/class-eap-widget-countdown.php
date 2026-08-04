<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

class EAP_Widget_Countdown extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-countdown';
	}

	public function get_title() {
		return __( 'Countdown', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-countdown';
	}

	public function get_keywords() {
		return array( 'countdown', 'timer', 'flip', 'clock', 'counter', 'date' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'countdown' );
	}

	public function get_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-countdown-script',
		);
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_expire_controls();
		$this->register_animation_controls();
		$this->register_general_style_controls();
		$this->register_number_style_controls();
		$this->register_flip_style_controls();
		$this->register_box_style_controls();
		$this->register_circle_style_controls();
		$this->register_panel_style_controls();
		$this->register_label_style_controls();
		$this->register_separator_style_controls();
		$this->register_message_style_controls();
	}

	/* -------------------------------------------------------------------------
	 * Content
	 * ---------------------------------------------------------------------- */

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Countdown', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'flip',
				'options' => array(
					'flip'    => __( 'Flip Clock', 'elementor-animatepro' ),
					'boxed'   => __( 'Boxed', 'elementor-animatepro' ),
					'minimal' => __( 'Minimal', 'elementor-animatepro' ),
					'circle'  => __( 'Circle', 'elementor-animatepro' ),
					'panel'   => __( 'Panel (Slide)', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'countdown_source',
			array(
				'label'       => __( 'Countdown Type', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'date',
				'options'     => array(
					'date'      => __( 'Until a date', 'elementor-animatepro' ),
					'evergreen' => __( 'Time period (evergreen)', 'elementor-animatepro' ),
				),
				'description' => __( '"Until a date" ends at the same fixed moment for everyone. "Time period" is a per-visitor timer that starts on each visitor\'s first visit (remembered with a cookie/session).', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'due_date',
			array(
				'label'       => __( 'Due Date', 'elementor-animatepro' ),
				'type'        => Controls_Manager::DATE_TIME,
				'description' => __( 'Set the date/time the countdown should end. Uses your WordPress timezone. Leave empty for a 10-day demo timer.', 'elementor-animatepro' ),
				'condition'   => array( 'countdown_source' => 'date' ),
			)
		);

		$this->add_control(
			'evergreen_heading',
			array(
				'label'     => __( 'Timer Duration', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array( 'countdown_source' => 'evergreen' ),
			)
		);

		$this->add_control(
			'evergreen_days',
			array(
				'label'     => __( 'Days', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'default'   => 0,
				'condition' => array( 'countdown_source' => 'evergreen' ),
			)
		);

		$this->add_control(
			'evergreen_hours',
			array(
				'label'     => __( 'Hours', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 23,
				'default'   => 0,
				'condition' => array( 'countdown_source' => 'evergreen' ),
			)
		);

		$this->add_control(
			'evergreen_minutes',
			array(
				'label'     => __( 'Minutes', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 59,
				'default'   => 20,
				'condition' => array( 'countdown_source' => 'evergreen' ),
			)
		);

		$this->add_control(
			'evergreen_seconds',
			array(
				'label'     => __( 'Seconds', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 59,
				'default'   => 0,
				'condition' => array( 'countdown_source' => 'evergreen' ),
			)
		);

		$this->add_control(
			'evergreen_recurring',
			array(
				'label'        => __( 'Restart When It Ends', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'On: the timer loops — it restarts for the visitor each time it ends. Off: it stays expired and the On Expire action runs.', 'elementor-animatepro' ),
				'condition'    => array( 'countdown_source' => 'evergreen' ),
			)
		);

		$this->add_control(
			'evergreen_storage',
			array(
				'label'     => __( 'Remember Per', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cookie',
				'options'   => array(
					'cookie'  => __( 'Visitor (persists across sessions)', 'elementor-animatepro' ),
					'session' => __( 'Browser session (resets when the browser closes)', 'elementor-animatepro' ),
				),
				'condition' => array( 'countdown_source' => 'evergreen' ),
			)
		);

		$this->add_control(
			'evergreen_id',
			array(
				'label'       => __( 'Shared Timer ID', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'e.g. spring-sale', 'elementor-animatepro' ),
				'description' => __( 'Optional. Use the same ID on multiple pages/widgets so one timer continues across them. Leave empty to keep it unique to this widget.', 'elementor-animatepro' ),
				'condition'   => array( 'countdown_source' => 'evergreen' ),
			)
		);

		$this->add_control(
			'units_heading',
			array(
				'label'     => __( 'Units', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_days',
			array(
				'label'        => __( 'Days', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_hours',
			array(
				'label'        => __( 'Hours', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_minutes',
			array(
				'label'        => __( 'Minutes', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_seconds',
			array(
				'label'        => __( 'Seconds', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'labels_heading',
			array(
				'label'     => __( 'Labels', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_labels',
			array(
				'label'        => __( 'Show Labels', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'label_days',
			array(
				'label'     => __( 'Days Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Days', 'elementor-animatepro' ),
				'condition' => array( 'show_labels' => 'yes' ),
			)
		);

		$this->add_control(
			'label_hours',
			array(
				'label'     => __( 'Hours Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Hours', 'elementor-animatepro' ),
				'condition' => array( 'show_labels' => 'yes' ),
			)
		);

		$this->add_control(
			'label_minutes',
			array(
				'label'     => __( 'Minutes Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Minutes', 'elementor-animatepro' ),
				'condition' => array( 'show_labels' => 'yes' ),
			)
		);

		$this->add_control(
			'label_seconds',
			array(
				'label'     => __( 'Seconds Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Seconds', 'elementor-animatepro' ),
				'condition' => array( 'show_labels' => 'yes' ),
			)
		);

		$this->add_control(
			'separator_heading',
			array(
				'label'     => __( 'Separator', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'layout' => array( 'flip', 'minimal', 'panel' ) ),
			)
		);

		$this->add_control(
			'show_separator',
			array(
				'label'        => __( 'Show Separator', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'layout' => array( 'flip', 'minimal', 'panel' ) ),
			)
		);

		$this->add_control(
			'separator_text',
			array(
				'label'     => __( 'Separator Character', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => ':',
				'condition' => array(
					'layout'         => array( 'flip', 'minimal', 'panel' ),
					'show_separator' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Expiration
	 * ---------------------------------------------------------------------- */

	protected function register_expire_controls() {
		$this->start_controls_section(
			'section_expire',
			array(
				'label' => __( 'On Expire', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'expire_action',
			array(
				'label'   => __( 'Action', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'message',
				'options' => array(
					'none'     => __( 'Keep showing zeros', 'elementor-animatepro' ),
					'message'  => __( 'Show a message', 'elementor-animatepro' ),
					'redirect' => __( 'Redirect to URL', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'expire_message',
			array(
				'label'     => __( 'Message', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => __( 'This offer has ended.', 'elementor-animatepro' ),
				'condition' => array( 'expire_action' => 'message' ),
			)
		);

		$this->add_control(
			'redirect_url',
			array(
				'label'       => __( 'Redirect URL', 'elementor-animatepro' ),
				'type'        => Controls_Manager::URL,
				'options'     => array( '' ),
				'placeholder' => 'https://example.com/',
				'condition'   => array( 'expire_action' => 'redirect' ),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Entrance animation
	 * ---------------------------------------------------------------------- */

	protected function register_animation_controls() {
		$this->start_controls_section(
			'section_animation',
			array(
				'label' => __( 'Entrance Animation', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'entrance',
			array(
				'label'   => __( 'Animation', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => array(
					'none'  => __( 'None', 'elementor-animatepro' ),
					'fade'  => __( 'Fade In', 'elementor-animatepro' ),
					'zoom'  => __( 'Zoom In', 'elementor-animatepro' ),
					'slide' => __( 'Slide Up', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'entrance_duration',
			array(
				'label'     => __( 'Duration (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 700,
				'min'       => 100,
				'max'       => 4000,
				'step'      => 50,
				'condition' => array( 'entrance!' => 'none' ),
			)
		);

		$this->add_control(
			'entrance_delay',
			array(
				'label'     => __( 'Delay (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'min'       => 0,
				'max'       => 4000,
				'step'      => 50,
				'condition' => array( 'entrance!' => 'none' ),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: general
	 * ---------------------------------------------------------------------- */

	protected function register_general_style_controls() {
		$this->start_controls_section(
			'section_general_style',
			array(
				'label' => __( 'General', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-countdown' => '--eap-cd-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'unit_gap',
			array(
				'label'      => __( 'Gap Between Digits', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 6, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown__digits' => 'gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'layout' => array( 'flip', 'panel' ) ),
			)
		);

		$this->add_responsive_control(
			'group_gap',
			array(
				'label'      => __( 'Gap Between Units', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'    => array( 'size' => 18, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown__row' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: number (shared across layouts via .eap-cd-num)
	 * ---------------------------------------------------------------------- */

	protected function register_number_style_controls() {
		$this->start_controls_section(
			'section_number_style',
			array(
				'label' => __( 'Number', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'number_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-cd-num' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'number_typography',
				'selector' => '{{WRAPPER}} .eap-cd-num',
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: flip card
	 * ---------------------------------------------------------------------- */

	protected function register_flip_style_controls() {
		$this->start_controls_section(
			'section_flip_style',
			array(
				'label'     => __( 'Flip Card', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'flip' ),
			)
		);

		$this->add_control(
			'flip_card_bg',
			array(
				'label'     => __( 'Card Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#20242e',
				'selectors' => array(
					'{{WRAPPER}} .eap-cd-flip__static, {{WRAPPER}} .eap-cd-flip__fold' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'flip_card_width',
			array(
				'label'      => __( 'Card Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 28, 'max' => 200 ) ),
				'default'    => array( 'size' => 60, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown' => '--eap-cd-card-w: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'flip_card_height',
			array(
				'label'      => __( 'Card Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 36, 'max' => 240 ) ),
				'default'    => array( 'size' => 80, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown' => '--eap-cd-card-h: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'flip_card_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown' => '--eap-cd-card-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'flip_divider_color',
			array(
				'label'     => __( 'Divider Line Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.35)',
				'selectors' => array(
					'{{WRAPPER}} .eap-countdown' => '--eap-cd-divider-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'flip_divider_size',
			array(
				'label'      => __( 'Divider Line Thickness', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 10 ) ),
				'default'    => array( 'size' => 2, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown' => '--eap-cd-divider-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'flip_speed',
			array(
				'label'      => __( 'Flip Speed (ms)', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 200, 'max' => 1500 ) ),
				'default'    => array( 'size' => 600, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown' => '--eap-cd-flip-speed: {{SIZE}}ms;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'flip_border',
				'selector' => '{{WRAPPER}} .eap-cd-flip',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'flip_shadow',
				'fields_options' => array(
					'box_shadow_type' => array(
						'default' => 'yes',
					),
					'box_shadow'      => array(
						'default' => array(
							'horizontal' => 0,
							'vertical'   => 10,
							'blur'       => 24,
							'spread'     => 0,
							'color'      => 'rgba(0, 0, 0, 0.25)',
						),
					),
				),
				'selector' => '{{WRAPPER}} .eap-cd-flip',
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: boxed
	 * ---------------------------------------------------------------------- */

	protected function register_box_style_controls() {
		$this->start_controls_section(
			'section_box_style',
			array(
				'label'     => __( 'Box', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'boxed' ),
			)
		);

		$this->add_control(
			'box_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#20242e',
				'selectors' => array(
					'{{WRAPPER}} .eap-countdown__box' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_min_width',
			array(
				'label'      => __( 'Min Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 40, 'max' => 260 ) ),
				'default'    => array( 'size' => 96, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown__box' => 'min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 18,
					'right'  => 14,
					'bottom' => 18,
					'left'   => 14,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown__box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => 14,
					'right'  => 14,
					'bottom' => 14,
					'left'   => 14,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown__box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .eap-countdown__box',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .eap-countdown__box',
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: circle
	 * ---------------------------------------------------------------------- */

	protected function register_circle_style_controls() {
		$this->start_controls_section(
			'section_circle_style',
			array(
				'label'     => __( 'Circle', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'circle' ),
			)
		);

		$this->add_responsive_control(
			'circle_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 70, 'max' => 260 ) ),
				'default'    => array( 'size' => 120, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown' => '--eap-cd-ring-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'circle_ring_width',
			array(
				'label'      => __( 'Ring Thickness', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 2, 'max' => 24 ) ),
				'default'    => array( 'size' => 6, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown' => '--eap-cd-ring-width: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'circle_ring_color',
			array(
				'label'     => __( 'Ring Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7dd3d8',
				'selectors' => array(
					'{{WRAPPER}} .eap-countdown' => '--eap-cd-ring-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'circle_track_color',
			array(
				'label'     => __( 'Track Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.12)',
				'selectors' => array(
					'{{WRAPPER}} .eap-countdown' => '--eap-cd-track-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'circle_bg',
			array(
				'label'     => __( 'Inner Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => array(
					'{{WRAPPER}} .eap-countdown__circle' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: panel
	 * ---------------------------------------------------------------------- */

	protected function register_panel_style_controls() {
		$this->start_controls_section(
			'section_panel_style',
			array(
				'label'     => __( 'Panel', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'panel' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'panel_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .eap-countdown__panel',
			)
		);

		$this->add_responsive_control(
			'panel_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 48,
					'right'  => 56,
					'bottom' => 48,
					'left'   => 56,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown__panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'panel_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => 18,
					'right'  => 18,
					'bottom' => 18,
					'left'   => 18,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown__panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'panel_border',
				'selector' => '{{WRAPPER}} .eap-countdown__panel',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'           => 'panel_shadow',
				'fields_options' => array(
					'box_shadow_type' => array( 'default' => 'yes' ),
					'box_shadow'      => array(
						'default' => array(
							'horizontal' => 0,
							'vertical'   => 30,
							'blur'       => 60,
							'spread'     => 0,
							'color'      => 'rgba(0, 0, 0, 0.25)',
						),
					),
				),
				'selector'       => '{{WRAPPER}} .eap-countdown__panel',
			)
		);

		$this->add_responsive_control(
			'roll_speed',
			array(
				'label'      => __( 'Slide Speed (ms)', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 150, 'max' => 2000 ) ),
				'default'    => array( 'size' => 650, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown' => '--eap-cd-roll-speed: {{SIZE}}ms;',
				),
			)
		);

		$this->add_control(
			'panel_icon_heading',
			array(
				'label'     => __( 'Corner Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'panel_show_icon',
			array(
				'label'        => __( 'Show Icon', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'panel_icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.5)',
				'selectors' => array(
					'{{WRAPPER}} .eap-countdown__panel-icon' => 'color: {{VALUE}};',
				),
				'condition' => array( 'panel_show_icon' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'panel_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 60 ) ),
				'default'    => array( 'size' => 18, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-countdown__panel-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'panel_show_icon' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: labels
	 * ---------------------------------------------------------------------- */

	protected function register_label_style_controls() {
		$this->start_controls_section(
			'section_label_style',
			array(
				'label'     => __( 'Label', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_labels' => 'yes' ),
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9aa5b1',
				'selectors' => array(
					'{{WRAPPER}} .eap-cd-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .eap-cd-label',
			)
		);

		$this->add_responsive_control(
			'label_gap',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-cd-label' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: separator
	 * ---------------------------------------------------------------------- */

	protected function register_separator_style_controls() {
		$this->start_controls_section(
			'section_separator_style',
			array(
				'label'     => __( 'Separator', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout'         => array( 'flip', 'minimal', 'panel' ),
					'show_separator' => 'yes',
				),
			)
		);

		$this->add_control(
			'separator_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-cd-sep' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'separator_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 120 ) ),
				'default'    => array( 'size' => 32, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-cd-sep' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: expire message
	 * ---------------------------------------------------------------------- */

	protected function register_message_style_controls() {
		$this->start_controls_section(
			'section_message_style',
			array(
				'label'     => __( 'Expire Message', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'expire_action' => 'message' ),
			)
		);

		$this->add_control(
			'message_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-countdown__message' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'message_typography',
				'selector' => '{{WRAPPER}} .eap-countdown__message',
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Render
	 * ---------------------------------------------------------------------- */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$layout   = ! empty( $settings['layout'] ) ? $settings['layout'] : 'flip';
		$source   = ! empty( $settings['countdown_source'] ) ? $settings['countdown_source'] : 'date';

		$now_ts = time();

		if ( 'evergreen' === $source ) {
			// Per-visitor timer: the server renders the full duration; the JS then
			// adjusts to each visitor's stored start time (cookie/session cookie).
			$remaining = $this->get_evergreen_seconds( $settings );
			$target_ts = $now_ts + $remaining;
		} else {
			$target_ts = $this->get_target_timestamp( $settings );
			$remaining = max( 0, $target_ts - $now_ts );
		}

		$parts = $this->get_parts( $remaining );

		$units = $this->get_active_units( $settings, $parts );

		if ( empty( $units ) ) {
			return;
		}

		$entrance = ! empty( $settings['entrance'] ) ? $settings['entrance'] : 'fade';
		$duration = isset( $settings['entrance_duration'] ) ? absint( $settings['entrance_duration'] ) : 700;
		$delay    = isset( $settings['entrance_delay'] ) ? absint( $settings['entrance_delay'] ) : 0;

		$show_sep   = 'yes' === ( $settings['show_separator'] ?? '' ) && in_array( $layout, array( 'flip', 'minimal', 'panel' ), true );
		$sep_text   = isset( $settings['separator_text'] ) && '' !== $settings['separator_text'] ? $settings['separator_text'] : ':';
		$show_labels = 'yes' === ( $settings['show_labels'] ?? 'yes' );

		$expire   = ! empty( $settings['expire_action'] ) ? $settings['expire_action'] : 'message';
		$redirect = '';
		if ( 'redirect' === $expire && ! empty( $settings['redirect_url']['url'] ) ) {
			$redirect = $settings['redirect_url']['url'];
		}

		$classes = array(
			'eap-widget',
			'eap-countdown',
			'eap-countdown--' . $layout,
		);
		if ( 'none' !== $entrance ) {
			$classes[] = 'eap-countdown--anim-' . $entrance;
		}

		$wrapper_attrs = array(
			'class'              => $classes,
			'data-eap-countdown' => 'true',
			'data-eap-layout'    => $layout,
			'data-eap-source'    => $source,
			'data-eap-now'       => (string) ( $now_ts * 1000 ),
			'data-eap-expire'    => $expire,
			'style'              => sprintf( '--eap-cd-anim-duration:%dms;--eap-cd-anim-delay:%dms;', $duration, $delay ),
		);

		if ( 'evergreen' === $source ) {
			$wrapper_attrs['data-eap-duration']  = (string) ( $remaining * 1000 );
			$wrapper_attrs['data-eap-id']        = $this->get_evergreen_id( $settings );
			$wrapper_attrs['data-eap-storage']   = ( 'session' === ( $settings['evergreen_storage'] ?? 'cookie' ) ) ? 'session' : 'cookie';
			$wrapper_attrs['data-eap-recurring'] = ( 'yes' === ( $settings['evergreen_recurring'] ?? '' ) ) ? 'yes' : 'no';
		} else {
			$wrapper_attrs['data-eap-target'] = (string) ( $target_ts * 1000 );
		}

		$this->add_render_attribute( 'wrapper', $wrapper_attrs );

		if ( '' !== $redirect ) {
			$this->add_render_attribute( 'wrapper', 'data-eap-redirect', esc_url( $redirect ) );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( 'panel' === $layout ) : ?>
			<div class="eap-countdown__panel">
				<?php $this->render_panel_icon( $settings ); ?>
			<?php endif; ?>
			<div class="eap-countdown__row" data-eap-countdown-row>
				<?php
				$last = count( $units ) - 1;
				foreach ( $units as $index => $unit ) {
					$this->render_unit( $unit, $layout, $show_labels );

					if ( $show_sep && $index < $last ) {
						echo '<span class="eap-cd-sep eap-countdown__sep" aria-hidden="true">' . esc_html( $sep_text ) . '</span>';
					}
				}
				?>
			</div>
			<?php if ( 'panel' === $layout ) : ?>
			</div>
			<?php endif; ?>
			<?php if ( 'message' === $expire ) : ?>
				<div class="eap-countdown__message" data-eap-countdown-message hidden>
					<?php echo esc_html( $settings['expire_message'] ?? '' ); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render a single time unit for the chosen layout.
	 *
	 * @param array  $unit        Unit data (key, value, label, digits, max).
	 * @param string $layout      Layout key.
	 * @param bool   $show_labels Whether labels are shown.
	 * @return void
	 */
	protected function render_unit( $unit, $layout, $show_labels ) {
		$value  = (int) $unit['value'];
		$digits = (int) $unit['digits'];
		$padded = str_pad( (string) $value, $digits, '0', STR_PAD_LEFT );

		$label_html = ( $show_labels && ! empty( $unit['label'] ) )
			? '<span class="eap-cd-label eap-countdown__label">' . esc_html( $unit['label'] ) . '</span>'
			: '';

		echo '<div class="eap-countdown__unit eap-countdown__unit--' . esc_attr( $unit['key'] ) . '">';

		// Panel layout places the label above the digits.
		if ( 'panel' === $layout && '' !== $label_html ) {
			echo $label_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		if ( 'flip' === $layout ) {
			echo '<div class="eap-countdown__digits" data-unit="' . esc_attr( $unit['key'] ) . '">';
			foreach ( str_split( $padded ) as $char ) {
				$this->render_flip_digit( $char );
			}
			echo '</div>';
		} elseif ( 'boxed' === $layout ) {
			echo '<div class="eap-countdown__box">';
			echo '<span class="eap-cd-num eap-countdown__number" data-eap-cd-number data-unit="' . esc_attr( $unit['key'] ) . '" data-digits="' . esc_attr( $digits ) . '">' . esc_html( $padded ) . '</span>';
			echo '</div>';
		} elseif ( 'circle' === $layout ) {
			$this->render_circle( $unit, $padded );
		} elseif ( 'panel' === $layout ) {
			echo '<div class="eap-countdown__digits" data-unit="' . esc_attr( $unit['key'] ) . '">';
			foreach ( str_split( $padded ) as $char ) {
				$this->render_roll_digit( $char );
			}
			echo '</div>';
		} else { // minimal.
			echo '<span class="eap-cd-num eap-countdown__number" data-eap-cd-number data-unit="' . esc_attr( $unit['key'] ) . '" data-digits="' . esc_attr( $digits ) . '">' . esc_html( $padded ) . '</span>';
		}

		// Non-panel layouts place the label below the digits.
		if ( 'panel' !== $layout && '' !== $label_html ) {
			echo $label_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div>';
	}

	/**
	 * Render one flip digit card (4 stacked pieces the JS animates).
	 *
	 * @param string $char Single numeric character.
	 * @return void
	 */
	protected function render_flip_digit( $char ) {
		$char = esc_html( $char );
		?>
		<span class="eap-cd-flip" data-eap-cd-digit data-val="<?php echo $char; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
			<span class="eap-cd-flip__static eap-cd-flip__static--top"><span class="eap-cd-num eap-cd-flip__num"><?php echo $char; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></span>
			<span class="eap-cd-flip__static eap-cd-flip__static--bottom"><span class="eap-cd-num eap-cd-flip__num"><?php echo $char; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></span>
			<span class="eap-cd-flip__fold eap-cd-flip__fold--top"><span class="eap-cd-num eap-cd-flip__num"><?php echo $char; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></span>
			<span class="eap-cd-flip__fold eap-cd-flip__fold--bottom"><span class="eap-cd-num eap-cd-flip__num"><?php echo $char; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></span>
		</span>
		<?php
	}

	/**
	 * Render the circular SVG ring layout for a unit.
	 *
	 * @param array  $unit   Unit data.
	 * @param string $padded Zero-padded value.
	 * @return void
	 */
	protected function render_circle( $unit, $padded ) {
		$max     = max( 1, (int) $unit['max'] );
		$value   = (int) $unit['value'];
		$radius  = 45;
		$circ    = 2 * M_PI * $radius;
		$offset  = $circ * ( 1 - min( 1, $value / $max ) );
		?>
		<div class="eap-countdown__circle">
			<svg class="eap-countdown__ring" viewBox="0 0 100 100" aria-hidden="true" focusable="false">
				<circle class="eap-countdown__ring-track" cx="50" cy="50" r="<?php echo esc_attr( $radius ); ?>"></circle>
				<circle class="eap-countdown__ring-fill" cx="50" cy="50" r="<?php echo esc_attr( $radius ); ?>"
					data-eap-cd-ring
					style="stroke-dasharray: <?php echo esc_attr( $circ ); ?>; stroke-dashoffset: <?php echo esc_attr( $offset ); ?>;"></circle>
			</svg>
			<span class="eap-cd-num eap-countdown__number eap-countdown__circle-num" data-eap-cd-number data-unit="<?php echo esc_attr( $unit['key'] ); ?>" data-max="<?php echo esc_attr( $max ); ?>" data-digits="2"><?php echo esc_html( $padded ); ?></span>
		</div>
		<?php
	}

	/**
	 * Render one vertical slide/roll digit (a 0-9 reel the JS translates).
	 *
	 * @param string $char Single numeric character.
	 * @return void
	 */
	protected function render_roll_digit( $char ) {
		$idx = (int) $char;
		echo '<span class="eap-cd-roll" data-eap-cd-roll data-val="' . esc_attr( (string) $idx ) . '">';
		echo '<span class="eap-cd-roll__reel" style="transform: translateY(calc(' . esc_attr( (string) $idx ) . ' * -1em));">';
		for ( $i = 0; $i <= 9; $i++ ) {
			echo '<span class="eap-cd-num eap-cd-roll__cell">' . esc_html( (string) $i ) . '</span>';
		}
		echo '</span></span>';
	}

	/**
	 * Render the panel corner icon (hourglass).
	 *
	 * @param array $settings Widget settings.
	 * @return void
	 */
	protected function render_panel_icon( $settings ) {
		if ( 'yes' !== ( $settings['panel_show_icon'] ?? 'yes' ) ) {
			return;
		}
		?>
		<span class="eap-countdown__panel-icon" aria-hidden="true">
			<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M6 3h12M6 21h12M8 3v3.5c0 1.5 1.2 2.6 2.5 3.6.9.7.9 1.1 0 1.8C9.2 14 8 15.1 8 16.6V21M16 3v3.5c0 1.5-1.2 2.6-2.5 3.6-.9.7-.9 1.1 0 1.8C14.8 14 16 15.1 16 16.6V21" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</span>
		<?php
	}

	/* -------------------------------------------------------------------------
	 * Helpers
	 * ---------------------------------------------------------------------- */

	/**
	 * Resolve the target timestamp (UTC seconds) from the due-date setting.
	 *
	 * @param array $settings Widget settings.
	 * @return int
	 */
	protected function get_target_timestamp( $settings ) {
		$fallback = time() + ( 10 * DAY_IN_SECONDS );
		$due      = isset( $settings['due_date'] ) ? trim( (string) $settings['due_date'] ) : '';

		if ( '' === $due ) {
			return $fallback;
		}

		try {
			$dt = new DateTime( $due, wp_timezone() );
			return $dt->getTimestamp();
		} catch ( Exception $e ) {
			return $fallback;
		}
	}

	/**
	 * Total duration (seconds) for an evergreen timer. Falls back to 20 minutes
	 * when nothing is set so the widget never renders a zero-length timer.
	 *
	 * @param array $settings Widget settings.
	 * @return int
	 */
	protected function get_evergreen_seconds( $settings ) {
		$days    = isset( $settings['evergreen_days'] ) ? absint( $settings['evergreen_days'] ) : 0;
		$hours   = isset( $settings['evergreen_hours'] ) ? absint( $settings['evergreen_hours'] ) : 0;
		$minutes = isset( $settings['evergreen_minutes'] ) ? absint( $settings['evergreen_minutes'] ) : 0;
		$seconds = isset( $settings['evergreen_seconds'] ) ? absint( $settings['evergreen_seconds'] ) : 0;

		$total = ( $days * DAY_IN_SECONDS ) + ( $hours * HOUR_IN_SECONDS ) + ( $minutes * MINUTE_IN_SECONDS ) + $seconds;

		return $total > 0 ? $total : ( 20 * MINUTE_IN_SECONDS );
	}

	/**
	 * Cookie/storage key for an evergreen timer. A custom "Shared Timer ID" lets
	 * one timer span several pages/instances; otherwise it is unique per widget.
	 *
	 * @param array $settings Widget settings.
	 * @return string
	 */
	protected function get_evergreen_id( $settings ) {
		$custom = isset( $settings['evergreen_id'] ) ? trim( (string) $settings['evergreen_id'] ) : '';

		if ( '' !== $custom ) {
			$custom = preg_replace( '/[^A-Za-z0-9_-]/', '', $custom );
			if ( '' !== $custom ) {
				return $custom;
			}
		}

		return 'w' . $this->get_id();
	}

	/**
	 * Break a remaining-seconds value into days/hours/minutes/seconds.
	 *
	 * @param int $remaining Remaining seconds.
	 * @return array<string,int>
	 */
	protected function get_parts( $remaining ) {
		$remaining = (int) $remaining;

		return array(
			'days'    => (int) floor( $remaining / DAY_IN_SECONDS ),
			'hours'   => (int) floor( ( $remaining % DAY_IN_SECONDS ) / HOUR_IN_SECONDS ),
			'minutes' => (int) floor( ( $remaining % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS ),
			'seconds' => (int) floor( $remaining % MINUTE_IN_SECONDS ),
		);
	}

	/**
	 * Build the ordered list of enabled units with display metadata.
	 *
	 * @param array $settings Widget settings.
	 * @param array $parts    Computed time parts.
	 * @return array<int,array<string,mixed>>
	 */
	protected function get_active_units( $settings, $parts ) {
		$config = array(
			'days'    => array(
				'enabled' => 'yes' === ( $settings['show_days'] ?? 'yes' ),
				'label'   => $settings['label_days'] ?? __( 'Days', 'elementor-animatepro' ),
				'digits'  => max( 2, strlen( (string) $parts['days'] ) ),
				'max'     => max( 1, $parts['days'] ),
			),
			'hours'   => array(
				'enabled' => 'yes' === ( $settings['show_hours'] ?? 'yes' ),
				'label'   => $settings['label_hours'] ?? __( 'Hours', 'elementor-animatepro' ),
				'digits'  => 2,
				'max'     => 24,
			),
			'minutes' => array(
				'enabled' => 'yes' === ( $settings['show_minutes'] ?? 'yes' ),
				'label'   => $settings['label_minutes'] ?? __( 'Minutes', 'elementor-animatepro' ),
				'digits'  => 2,
				'max'     => 60,
			),
			'seconds' => array(
				'enabled' => 'yes' === ( $settings['show_seconds'] ?? 'yes' ),
				'label'   => $settings['label_seconds'] ?? __( 'Seconds', 'elementor-animatepro' ),
				'digits'  => 2,
				'max'     => 60,
			),
		);

		$units = array();
		foreach ( $config as $key => $data ) {
			if ( ! $data['enabled'] ) {
				continue;
			}

			$units[] = array(
				'key'    => $key,
				'value'  => $parts[ $key ],
				'label'  => $data['label'],
				'digits' => $data['digits'],
				'max'    => $data['max'],
			);
		}

		return $units;
	}
}
