<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

class EAP_Widget_Image_Comparison extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-image-comparison';
	}

	public function get_title() {
		return __( 'Image Comparison', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-image-rollover';
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'image-comparison' );
	}

	public function get_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-image-comparison-script',
		);
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'before_image',
			array(
				'label'   => __( 'Before Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_control(
			'after_image',
			array(
				'label'   => __( 'After Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'comparison_image',
				'default' => 'full',
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'   => __( 'Comparison Direction', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => __( 'Left / Right', 'elementor-animatepro' ),
					'vertical'   => __( 'Top / Bottom', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'max_height',
			array(
				'label'      => __( 'Max Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 120,
						'max' => 1200,
					),
					'vh' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-comparison' => '--eap-compare-max-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'start_position',
			array(
				'label'      => __( 'Start Position', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 50,
					'unit' => '%',
				),
			)
		);

		$this->add_control(
			'show_labels',
			array(
				'label'        => __( 'Show Before / After Labels', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'before_text',
			array(
				'label'     => __( 'Before Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Before', 'elementor-animatepro' ),
				'condition' => array(
					'show_labels' => 'yes',
				),
			)
		);

		$this->add_control(
			'after_text',
			array(
				'label'     => __( 'After Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'After', 'elementor-animatepro' ),
				'condition' => array(
					'show_labels' => 'yes',
				),
			)
		);

		$this->add_control(
			'label_position',
			array(
				'label'     => __( 'Label Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top',
				'options'   => array(
					'top'    => __( 'Top', 'elementor-animatepro' ),
					'bottom' => __( 'Bottom', 'elementor-animatepro' ),
				),
				'condition' => array(
					'show_labels' => 'yes',
				),
			)
		);

		$this->add_control(
			'handle_icon',
			array(
				'label'   => __( 'Handle Icon', 'elementor-animatepro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-arrows-left-right',
					'library' => 'fa-solid',
				),
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
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .eap-image-comparison',
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-comparison, {{WRAPPER}} .eap-image-comparison__media, {{WRAPPER}} .eap-image-comparison__image, {{WRAPPER}} .eap-image-comparison__overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .eap-image-comparison',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_handle_style',
			array(
				'label' => __( 'Handle', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'divider_color',
			array(
				'label'     => __( 'Divider Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-comparison' => '--eap-compare-divider: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'handle_background',
			array(
				'label'     => __( 'Handle Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-comparison' => '--eap-compare-handle-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'handle_color',
			array(
				'label'     => __( 'Handle Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-comparison' => '--eap-compare-handle-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'handle_size',
			array(
				'label'      => __( 'Handle Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 24,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 34,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-comparison' => '--eap-compare-handle-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_label_style',
			array(
				'label'     => __( 'Labels', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_labels' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .eap-image-comparison__label',
			)
		);

		$this->add_control(
			'label_text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-comparison' => '--eap-compare-label-text: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'label_background',
				'selector' => '{{WRAPPER}} .eap-image-comparison__label',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'label_border',
				'selector' => '{{WRAPPER}} .eap-image-comparison__label',
			)
		);

		$this->add_responsive_control(
			'label_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-comparison__label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'label_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-comparison__label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'label_offset_x',
			array(
				'label'      => __( 'Horizontal Offset', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 20,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-comparison' => '--eap-compare-label-offset-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'label_offset_y',
			array(
				'label'      => __( 'Vertical Offset', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 20,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-comparison' => '--eap-compare-label-offset-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$direction     = ! empty( $settings['direction'] ) ? $settings['direction'] : 'horizontal';
		$position      = isset( $settings['start_position']['size'] ) ? (float) $settings['start_position']['size'] : 50;
		$label_position = ! empty( $settings['label_position'] ) ? $settings['label_position'] : 'top';
		$show_labels   = ! empty( $settings['show_labels'] ) && 'yes' === $settings['show_labels'];
		$before_url    = ! empty( $settings['before_image']['id'] ) ? \Elementor\Group_Control_Image_Size::get_attachment_image_src( $settings['before_image']['id'], 'comparison_image', $settings ) : $settings['before_image']['url'];
		$after_url     = ! empty( $settings['after_image']['id'] ) ? \Elementor\Group_Control_Image_Size::get_attachment_image_src( $settings['after_image']['id'], 'comparison_image', $settings ) : $settings['after_image']['url'];
		$handle_icon   = ! empty( $settings['handle_icon']['value'] ) ? $settings['handle_icon'] : array();

		if ( empty( $before_url ) ) {
			$before_url = \Elementor\Utils::get_placeholder_image_src();
		}

		if ( empty( $after_url ) ) {
			$after_url = \Elementor\Utils::get_placeholder_image_src();
		}
		?>
		<div class="eap-widget eap-image-comparison eap-image-comparison--<?php echo esc_attr( $direction ); ?> eap-image-comparison--labels-<?php echo esc_attr( $label_position ); ?>" data-eap-image-comparison data-eap-direction="<?php echo esc_attr( $direction ); ?>" data-eap-position="<?php echo esc_attr( $position ); ?>">
			<div class="eap-image-comparison__media">
				<img class="eap-image-comparison__image eap-image-comparison__image--base" src="<?php echo esc_url( $after_url ); ?>" alt="<?php echo esc_attr( ! empty( $settings['after_text'] ) ? $settings['after_text'] : __( 'After image', 'elementor-animatepro' ) ); ?>" />
				<div class="eap-image-comparison__overlay" data-eap-image-comparison-overlay>
					<img class="eap-image-comparison__image eap-image-comparison__image--overlay" src="<?php echo esc_url( $before_url ); ?>" alt="<?php echo esc_attr( ! empty( $settings['before_text'] ) ? $settings['before_text'] : __( 'Before image', 'elementor-animatepro' ) ); ?>" />
				</div>

				<?php if ( $show_labels ) : ?>
					<span class="eap-image-comparison__label eap-image-comparison__label--before"><?php echo esc_html( ! empty( $settings['before_text'] ) ? $settings['before_text'] : __( 'Before', 'elementor-animatepro' ) ); ?></span>
					<span class="eap-image-comparison__label eap-image-comparison__label--after"><?php echo esc_html( ! empty( $settings['after_text'] ) ? $settings['after_text'] : __( 'After', 'elementor-animatepro' ) ); ?></span>
				<?php endif; ?>

				<button type="button" class="eap-image-comparison__handle" aria-label="<?php esc_attr_e( 'Drag comparison handle', 'elementor-animatepro' ); ?>" data-eap-image-comparison-handle>
					<span class="eap-image-comparison__divider"></span>
					<span class="eap-image-comparison__knob">
						<span class="eap-image-comparison__knob-icon" aria-hidden="true">
							<?php if ( ! empty( $handle_icon ) ) : ?>
								<?php Icons_Manager::render_icon( $handle_icon, array( 'aria-hidden' => 'true' ) ); ?>
							<?php elseif ( 'vertical' === $direction ) : ?>
								<span class="eap-image-comparison__knob-fallback">&#8597;</span>
							<?php else : ?>
								<span class="eap-image-comparison__knob-fallback">&#8596;</span>
							<?php endif; ?>
						</span>
					</span>
				</button>
			</div>
		</div>
		<?php
	}
}
