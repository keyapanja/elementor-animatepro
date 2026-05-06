<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

class EAP_Widget_Image extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-image';
	}

	public function get_title() {
		return __( 'Image', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-image';
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'image' );
	}

	public function get_script_depends() {
		return $this->get_visibility_script_depends();
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_image',
			array(
				'label' => __( 'Image', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'image_size',
				'default' => 'full',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_starter_animations',
			array(
				'label' => __( 'Starter Animations', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'starter_animation',
			array(
				'label'   => __( 'Animation', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'reveal',
				'options' => array(
					'none'        => __( 'None', 'elementor-animatepro' ),
					'reveal'      => __( 'Reveal', 'elementor-animatepro' ),
					'scale'       => __( 'Scale', 'elementor-animatepro' ),
					'slide'       => __( 'Slide', 'elementor-animatepro' ),
					'skew-reveal' => __( 'Skew Reveal', 'elementor-animatepro' ),
					'flip'        => __( 'Flip', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'animation_duration',
			array(
				'label'     => __( 'Duration (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1000,
				'min'       => 0,
				'step'      => 10,
				'condition' => array(
					'starter_animation!' => 'none',
				),
			)
		);

		$this->add_control(
			'animation_delay',
			array(
				'label'     => __( 'Delay (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'min'       => 0,
				'step'      => 10,
				'condition' => array(
					'starter_animation!' => 'none',
				),
			)
		);

		$this->add_control(
			'animation_easing',
			array(
				'label'     => __( 'Easing', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'ease',
				'options'   => array(
					'ease'         => __( 'Ease (Default)', 'elementor-animatepro' ),
					'linear'       => __( 'Linear', 'elementor-animatepro' ),
					'ease-in'      => __( 'Ease In', 'elementor-animatepro' ),
					'ease-out'     => __( 'Ease Out', 'elementor-animatepro' ),
					'ease-in-out'  => __( 'Ease In Out', 'elementor-animatepro' ),
					'cubic-bezier(0.22, 1, 0.36, 1)' => __( 'Smooth Cubic', 'elementor-animatepro' ),
					'cubic-bezier(0.175, 0.885, 0.32, 1.275)' => __( 'Elastic Feel', 'elementor-animatepro' ),
				),
				'condition' => array(
					'starter_animation!' => 'none',
				),
			)
		);

		$this->add_control(
			'reveal_direction',
			array(
				'label'     => __( 'Direction', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom',
				'options'   => array(
					'bottom' => __( 'Bottom -> Top', 'elementor-animatepro' ),
					'top'    => __( 'Top -> Bottom', 'elementor-animatepro' ),
					'left'   => __( 'Left -> Right', 'elementor-animatepro' ),
					'right'  => __( 'Right -> Left', 'elementor-animatepro' ),
					'center' => __( 'Center Expand', 'elementor-animatepro' ),
				),
				'condition' => array(
					'starter_animation' => 'reveal',
				),
			)
		);

		$this->add_control(
			'enable_fade',
			array(
				'label'        => __( 'Enable Fade', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'starter_animation' => 'reveal',
				),
			)
		);

		$this->add_control(
			'scale_start',
			array(
				'label'     => __( 'Start Scale', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0.6,
				'min'       => 0,
				'max'       => 5,
				'step'      => 0.01,
				'condition' => array(
					'starter_animation' => 'scale',
				),
			)
		);

		$this->add_control(
			'scale_end',
			array(
				'label'     => __( 'End Scale', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 0,
				'max'       => 5,
				'step'      => 0.01,
				'condition' => array(
					'starter_animation' => 'scale',
				),
			)
		);

		$this->add_control(
			'scale_from',
			array(
				'label'     => __( 'Scale From', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => array(
					'center' => __( 'Center', 'elementor-animatepro' ),
					'top'    => __( 'Top', 'elementor-animatepro' ),
					'bottom' => __( 'Bottom', 'elementor-animatepro' ),
					'left'   => __( 'Left', 'elementor-animatepro' ),
					'right'  => __( 'Right', 'elementor-animatepro' ),
				),
				'condition' => array(
					'starter_animation' => 'scale',
				),
			)
		);

		$this->add_control(
			'scale_opacity',
			array(
				'label'        => __( 'Animate Opacity', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'starter_animation' => 'scale',
				),
			)
		);

		$this->add_control(
			'slide_direction',
			array(
				'label'     => __( 'Direction', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom',
				'options'   => array(
					'bottom' => __( 'Bottom -> Top', 'elementor-animatepro' ),
					'top'    => __( 'Top -> Bottom', 'elementor-animatepro' ),
					'left'   => __( 'Left -> Right', 'elementor-animatepro' ),
					'right'  => __( 'Right -> Left', 'elementor-animatepro' ),
				),
				'condition' => array(
					'starter_animation' => 'slide',
				),
			)
		);

		$this->add_control(
			'slide_distance',
			array(
				'label'     => __( 'Distance (px)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 40,
				'min'       => 0,
				'step'      => 1,
				'condition' => array(
					'starter_animation' => 'slide',
				),
			)
		);

		$this->add_control(
			'skew_angle',
			array(
				'label'     => __( 'Skew Angle', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 18,
				'min'       => -180,
				'max'       => 180,
				'step'      => 1,
				'condition' => array(
					'starter_animation' => 'skew-reveal',
				),
			)
		);

		$this->add_control(
			'skew_translate_distance',
			array(
				'label'     => __( 'Translate Distance', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 40,
				'min'       => 0,
				'step'      => 1,
				'condition' => array(
					'starter_animation' => 'skew-reveal',
				),
			)
		);

		$this->add_control(
			'flip_direction',
			array(
				'label'     => __( 'Flip Direction', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'flip-x',
				'options'   => array(
					'flip-x' => __( 'Flip X', 'elementor-animatepro' ),
					'flip-y' => __( 'Flip Y', 'elementor-animatepro' ),
				),
				'condition' => array(
					'starter_animation' => 'flip',
				),
			)
		);

		$this->add_control(
			'flip_angle',
			array(
				'label'     => __( 'Flip Angle', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 88,
				'min'       => 0,
				'max'       => 180,
				'step'      => 1,
				'condition' => array(
					'starter_animation' => 'flip',
				),
			)
		);

		$this->add_control(
			'flip_perspective',
			array(
				'label'     => __( 'Perspective', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 800,
				'min'       => 100,
				'step'      => 10,
				'condition' => array(
					'starter_animation' => 'flip',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'section_style_image',
			array(
				'label' => __( 'Image', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'width',
			array(
				'label'      => __( 'Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px', 'vw' ),
				'range'      => array(
					'%'  => array( 'min' => 1, 'max' => 100 ),
					'px' => array( 'min' => 10, 'max' => 2000 ),
					'vw' => array( 'min' => 1, 'max' => 100 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-widget__media' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'max_width',
			array(
				'label'      => __( 'Max Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px', 'vw' ),
				'range'      => array(
					'%'  => array( 'min' => 1, 'max' => 100 ),
					'px' => array( 'min' => 10, 'max' => 2400 ),
					'vw' => array( 'min' => 1, 'max' => 100 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-widget__media' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh', '%' ),
				'range'      => array(
					'px' => array( 'min' => 10, 'max' => 2000 ),
					'vh' => array( 'min' => 1, 'max' => 100 ),
					'%'  => array( 'min' => 1, 'max' => 100 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-widget__media img' => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_image_states' );

		$this->start_controls_tab(
			'tab_image_normal',
			array(
				'label' => __( 'Normal', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'opacity',
			array(
				'label'      => __( 'Opacity', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'custom' ),
				'range'      => array(
					'custom' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.01,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-widget__media img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .eap-image-widget__media img',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'image_border',
				'selector' => '{{WRAPPER}} .eap-image-widget__media img',
			)
		);

		$this->add_responsive_control(
			'image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-widget__media, {{WRAPPER}} .eap-image-widget__media img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'image_box_shadow',
				'selector' => '{{WRAPPER}} .eap-image-widget__media img',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_image_hover',
			array(
				'label' => __( 'Hover', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'opacity_hover',
			array(
				'label'      => __( 'Opacity', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'custom' ),
				'range'      => array(
					'custom' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.01,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-widget__media:hover img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .eap-image-widget__media:hover img',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'image_border_hover',
				'selector' => '{{WRAPPER}} .eap-image-widget__media:hover img',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'image_box_shadow_hover',
				'selector' => '{{WRAPPER}} .eap-image-widget__media:hover img',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$image    = Group_Control_Image_Size::get_attachment_image_html( $settings, 'image_size', 'image' );

		if ( empty( $image ) && ! empty( $settings['image']['url'] ) ) {
			$image = sprintf( '<img src="%1$s" alt="" />', esc_url( $settings['image']['url'] ) );
		}

		if ( empty( $image ) ) {
			return;
		}

		$animation = ! empty( $settings['starter_animation'] ) ? $settings['starter_animation'] : 'none';
		$direction = 'bottom';
		$easing    = ! empty( $settings['animation_easing'] ) ? $settings['animation_easing'] : 'ease';
		$duration  = ! empty( $settings['animation_duration'] ) ? (int) $settings['animation_duration'] : 1000;
		$delay     = ! empty( $settings['animation_delay'] ) ? (int) $settings['animation_delay'] : 0;
		$style     = '--eap-image-anim-duration: ' . esc_attr( $duration ) . 'ms; --eap-image-anim-delay: ' . esc_attr( $delay ) . 'ms; --eap-image-anim-easing: ' . esc_attr( $easing ) . ';';

		if ( 'reveal' === $animation ) {
			$direction = ! empty( $settings['reveal_direction'] ) ? $settings['reveal_direction'] : 'bottom';
		} elseif ( 'scale' === $animation ) {
			$direction = ! empty( $settings['scale_from'] ) ? $settings['scale_from'] : 'center';
			$style    .= ' --eap-image-scale-start: ' . esc_attr( isset( $settings['scale_start'] ) ? $settings['scale_start'] : 0.6 ) . ';';
			$style    .= ' --eap-image-scale-end: ' . esc_attr( isset( $settings['scale_end'] ) ? $settings['scale_end'] : 1 ) . ';';
		} elseif ( 'slide' === $animation ) {
			$direction = ! empty( $settings['slide_direction'] ) ? $settings['slide_direction'] : 'bottom';
			$style    .= ' --eap-image-slide-distance: ' . esc_attr( isset( $settings['slide_distance'] ) ? (int) $settings['slide_distance'] : 40 ) . 'px;';
		} elseif ( 'skew-reveal' === $animation ) {
			$style .= ' --eap-image-skew-angle: ' . esc_attr( isset( $settings['skew_angle'] ) ? $settings['skew_angle'] : 18 ) . 'deg;';
			$style .= ' --eap-image-skew-distance: ' . esc_attr( isset( $settings['skew_translate_distance'] ) ? (int) $settings['skew_translate_distance'] : 40 ) . 'px;';
		} elseif ( 'flip' === $animation ) {
			$direction = ! empty( $settings['flip_direction'] ) ? $settings['flip_direction'] : 'flip-x';
			$style    .= ' --eap-image-flip-angle: ' . esc_attr( isset( $settings['flip_angle'] ) ? $settings['flip_angle'] : 88 ) . 'deg;';
			$style    .= ' --eap-image-flip-perspective: ' . esc_attr( isset( $settings['flip_perspective'] ) ? (int) $settings['flip_perspective'] : 800 ) . 'px;';
		}

		$classes = array(
			'eap-widget',
			'eap-image-widget',
		);

		if ( 'none' !== $animation ) {
			$classes[] = 'eap-image-widget--animated';
			$classes[] = 'eap-image-widget--' . $animation;
			$classes[] = 'eap-image-widget--dir-' . $direction;

			if ( 'yes' === $settings['enable_fade'] ) {
				$classes[] = 'eap-image-widget--fade';
			}

			if ( 'scale' === $animation && 'yes' === $settings['scale_opacity'] ) {
				$classes[] = 'eap-image-widget--fade';
			}
		}

		$this->add_render_attribute(
			'wrapper',
			array(
				'class' => $classes,
				'style' => $style,
			)
		);
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="eap-image-widget__media">
				<?php echo wp_kses_post( $image ); ?>
			</div>
		</div>
		<?php
	}
}
