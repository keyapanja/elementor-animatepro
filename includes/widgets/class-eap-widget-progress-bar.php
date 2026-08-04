<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

class EAP_Widget_Progress_Bar extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-progress-bar';
	}

	public function get_title() {
		return __( 'Progress Bar', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-skill-bar';
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'progress-bar' );
	}

	public function get_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-progress-bar-script',
		);
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'flat',
				'options' => array(
					'flat'     => __( 'Flat', 'elementor-animatepro' ),
					'circular' => __( 'Circular', 'elementor-animatepro' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Initial Satisfaction', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'progress_value',
			array(
				'label'   => __( 'Progress Value', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 30,
				'min'     => 0,
				'max'     => 100,
				'step'    => 1,
			)
		);

		$this->add_control(
			'flat_percentage_position',
			array(
				'label'     => __( 'Percentage Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'outside',
				'options'   => array(
					'inside'  => __( 'Inside', 'elementor-animatepro' ),
					'outside' => __( 'Outside', 'elementor-animatepro' ),
				),
				'condition' => array(
					'layout' => 'flat',
				),
			)
		);

		$this->add_control(
			'animation_duration',
			array(
				'label'       => __( 'Animation Duration (ms)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1200,
				'min'         => 100,
				'max'         => 6000,
				'step'        => 50,
			)
		);

		$this->add_control(
			'animation_delay',
			array(
				'label'       => __( 'Animation Delay (ms)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 4000,
				'step'        => 50,
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'section_box_style',
			array(
				'label' => __( 'Box', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'box_background',
				'selector' => '{{WRAPPER}} .eap-progress-bar',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .eap-progress-bar',
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-progress-bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-progress-bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .eap-progress-bar',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => __( 'Title', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-progress-bar__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-progress-bar__title',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_percentage_style',
			array(
				'label' => __( 'Percentage', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'percentage_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#facc15',
				'selectors' => array(
					'{{WRAPPER}} .eap-progress-bar__percentage, {{WRAPPER}} .eap-progress-bar__circle-value' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'percentage_typography',
				'selector' => '{{WRAPPER}} .eap-progress-bar__percentage, {{WRAPPER}} .eap-progress-bar__circle-value',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_flat_style',
			array(
				'label'     => __( 'Flat Bar', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => 'flat',
				),
			)
		);

		$this->add_control(
			'flat_track_color',
			array(
				'label'     => __( 'Track Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f3f4f6',
				'selectors' => array(
					'{{WRAPPER}} .eap-progress-bar' => '--eap-progress-track: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'flat_fill_color',
			array(
				'label'     => __( 'Fill Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#facc15',
				'selectors' => array(
					'{{WRAPPER}} .eap-progress-bar' => '--eap-progress-fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'flat_height',
			array(
				'label'      => __( 'Bar Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 6,
						'max' => 80,
					),
				),
				'default'    => array(
					'size' => 22,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-progress-bar' => '--eap-progress-flat-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'flat_radius',
			array(
				'label'      => __( 'Bar Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-progress-bar__flat-track, {{WRAPPER}} .eap-progress-bar__flat-fill' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'layout' => 'flat',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_circular_style',
			array(
				'label'     => __( 'Circular Ring', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => 'circular',
				),
			)
		);

		$this->add_responsive_control(
			'circle_size',
			array(
				'label'      => __( 'Circle Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 80,
						'max' => 420,
					),
				),
				'default'    => array(
					'size' => 140,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-progress-bar' => '--eap-progress-circle-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'circle_ring_width',
			array(
				'label'      => __( 'Ring Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 4,
						'max' => 32,
					),
				),
				'default'    => array(
					'size' => 14,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-progress-bar' => '--eap-progress-ring-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'circle_track_color',
			array(
				'label'     => __( 'Track Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#243462',
				'selectors' => array(
					'{{WRAPPER}} .eap-progress-bar' => '--eap-progress-track: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'circle_fill_color',
			array(
				'label'     => __( 'Fill Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7dd3d8',
				'selectors' => array(
					'{{WRAPPER}} .eap-progress-bar' => '--eap-progress-fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render_flat_layout( $settings, $value ) {
		$position = ! empty( $settings['flat_percentage_position'] ) ? $settings['flat_percentage_position'] : 'outside';
		?>
		<div class="eap-progress-bar__header">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<div class="eap-progress-bar__title"><?php echo esc_html( $settings['title'] ); ?></div>
			<?php endif; ?>
			<?php if ( 'outside' === $position ) : ?>
				<div class="eap-progress-bar__percentage eap-progress-bar__percentage--outside" data-eap-progress-value-text><?php echo esc_html( $value ); ?>%</div>
			<?php endif; ?>
		</div>
		<div class="eap-progress-bar__flat-track">
			<div class="eap-progress-bar__flat-fill" data-eap-progress-fill>
				<?php if ( 'inside' === $position ) : ?>
					<div class="eap-progress-bar__percentage eap-progress-bar__percentage--inside" data-eap-progress-value-text><?php echo esc_html( $value ); ?>%</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	protected function render_circular_layout( $settings, $value ) {
		?>
		<div class="eap-progress-bar__circle-wrap">
			<div class="eap-progress-bar__circle" data-eap-progress-circle>
				<svg viewBox="0 0 120 120" aria-hidden="true" focusable="false">
					<circle class="eap-progress-bar__circle-track" cx="60" cy="60" r="50"></circle>
					<circle class="eap-progress-bar__circle-fill" cx="60" cy="60" r="50" data-eap-progress-ring></circle>
				</svg>
				<div class="eap-progress-bar__circle-value" data-eap-progress-value-text><?php echo esc_html( $value ); ?>%</div>
			</div>
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<div class="eap-progress-bar__title eap-progress-bar__title--circle"><?php echo esc_html( $settings['title'] ); ?></div>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$layout   = ! empty( $settings['layout'] ) ? $settings['layout'] : 'flat';
		$value    = isset( $settings['progress_value'] ) ? max( 0, min( 100, (int) $settings['progress_value'] ) ) : 30;

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'                    => array(
					'eap-widget',
					'eap-progress-bar',
					'eap-progress-bar--' . $layout,
					'flat' === $layout ? 'eap-progress-bar--percent-' . ( ! empty( $settings['flat_percentage_position'] ) ? $settings['flat_percentage_position'] : 'outside' ) : '',
				),
				'data-eap-progress'        => 'true',
				'data-eap-layout'          => $layout,
				'data-eap-value'           => (string) $value,
				'data-eap-duration'        => (string) absint( ! empty( $settings['animation_duration'] ) ? $settings['animation_duration'] : 1200 ),
				'data-eap-delay'           => (string) absint( ! empty( $settings['animation_delay'] ) ? $settings['animation_delay'] : 0 ),
			)
		);
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php
			if ( 'circular' === $layout ) {
				$this->render_circular_layout( $settings, $value );
			} else {
				$this->render_flat_layout( $settings, $value );
			}
			?>
		</div>
		<?php
	}
}
