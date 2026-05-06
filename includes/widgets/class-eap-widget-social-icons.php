<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Repeater;

class EAP_Widget_Social_Icons extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-social-icons';
	}

	public function get_title() {
		return __( 'Social Icons', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-social-icons';
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'social-icons' );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_icons',
			array(
				'label' => __( 'Social Icons', 'elementor-animatepro' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'social_icon',
			array(
				'label'   => __( 'Icon', 'elementor-animatepro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fab fa-facebook-f',
					'library' => 'fa-brands',
				),
			)
		);

		$repeater->add_control(
			'label',
			array(
				'label'       => __( 'Label', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Facebook', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'         => __( 'Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'placeholder'   => 'https://',
				'default'       => array(
					'url'         => '',
					'is_external' => 'on',
				),
			)
		);

		$this->add_control(
			'icons',
			array(
				'label'       => __( 'Icons', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(
					array(
						'label'       => __( 'Facebook', 'elementor-animatepro' ),
						'social_icon' => array(
							'value'   => 'fab fa-facebook-f',
							'library' => 'fa-brands',
						),
						'link'        => array(
							'url'         => 'https://facebook.com/',
							'is_external' => 'on',
						),
					),
					array(
						'label'       => __( 'Instagram', 'elementor-animatepro' ),
						'social_icon' => array(
							'value'   => 'fab fa-instagram',
							'library' => 'fa-brands',
						),
						'link'        => array(
							'url'         => 'https://instagram.com/',
							'is_external' => 'on',
						),
					),
					array(
						'label'       => __( 'X', 'elementor-animatepro' ),
						'social_icon' => array(
							'value'   => 'fab fa-x-twitter',
							'library' => 'fa-brands',
						),
						'link'        => array(
							'url'         => 'https://x.com/',
							'is_external' => 'on',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'inline',
				'options' => array(
					'inline' => array(
						'title' => __( 'Inline', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-center',
					),
					'stacked' => array(
						'title' => __( 'Stacked', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-middle',
					),
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'   => __( 'Alignment', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-social-icons' => 'justify-content: {{VALUE}};',
				),
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'size' => 12,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-icons' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'section_icon_style',
			array(
				'label' => __( 'Icons', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 18,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-icons__icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-social-icons__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'container_size',
			array(
				'label'      => __( 'Container Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 24,
						'max' => 180,
					),
				),
				'default'    => array(
					'size' => 48,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-icons__link' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'border_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-icons__link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_icon_states' );

		$this->start_controls_tab(
			'tab_icon_normal',
			array(
				'label' => __( 'Normal', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-social-icons__link' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-social-icons__icon svg, {{WRAPPER}} .eap-social-icons__icon svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'icon_background',
				'selector' => '{{WRAPPER}} .eap-social-icons__link',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'icon_border',
				'selector' => '{{WRAPPER}} .eap-social-icons__link',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'icon_shadow',
				'selector' => '{{WRAPPER}} .eap-social-icons__link',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_icon_hover',
			array(
				'label' => __( 'Hover', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-social-icons__link:hover, {{WRAPPER}} .eap-social-icons__item.is-active .eap-social-icons__link' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-social-icons__link:hover .eap-social-icons__icon svg, {{WRAPPER}} .eap-social-icons__link:hover .eap-social-icons__icon svg *, {{WRAPPER}} .eap-social-icons__item.is-active .eap-social-icons__icon svg, {{WRAPPER}} .eap-social-icons__item.is-active .eap-social-icons__icon svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'icon_hover_background',
				'selector' => '{{WRAPPER}} .eap-social-icons__link:hover, {{WRAPPER}} .eap-social-icons__item.is-active .eap-social-icons__link',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'icon_hover_border',
				'selector' => '{{WRAPPER}} .eap-social-icons__link:hover, {{WRAPPER}} .eap-social-icons__item.is-active .eap-social-icons__link',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'icon_hover_shadow',
				'selector' => '{{WRAPPER}} .eap-social-icons__link:hover, {{WRAPPER}} .eap-social-icons__item.is-active .eap-social-icons__link',
			)
		);

		$this->add_control(
			'hover_scale',
			array(
				'label'      => __( 'Hover Scale', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'custom' ),
				'range'      => array(
					'custom' => array(
						'min'  => 0.2,
						'max'  => 3,
						'step' => 0.05,
					),
				),
				'default'    => array(
					'size' => 1,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-icons' => '--eap-social-hover-scale: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'hover_rotate',
			array(
				'label'      => __( 'Hover Rotate', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array(
						'min' => -360,
						'max' => 360,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => 'deg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-icons' => '--eap-social-hover-rotate: {{SIZE}}deg;',
				),
			)
		);

		$this->add_responsive_control(
			'hover_offset_x',
			array(
				'label'      => __( 'Hover Offset X', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-icons' => '--eap-social-hover-offset-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hover_offset_y',
			array(
				'label'      => __( 'Hover Offset Y', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-icons' => '--eap-social-hover-offset-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hover_skew_x',
			array(
				'label'      => __( 'Hover Skew X', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => 'deg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-icons' => '--eap-social-hover-skew-x: {{SIZE}}deg;',
				),
			)
		);

		$this->add_control(
			'hover_skew_y',
			array(
				'label'      => __( 'Hover Skew Y', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => 'deg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-icons' => '--eap-social-hover-skew-y: {{SIZE}}deg;',
				),
			)
		);

		$this->add_control(
			'hover_transition_duration',
			array(
				'label'      => __( 'Transition Duration', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'ms' ),
				'range'      => array(
					'ms' => array(
						'min' => 50,
						'max' => 2000,
						'step' => 25,
					),
				),
				'default'    => array(
					'size' => 260,
					'unit' => 'ms',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-icons' => '--eap-social-hover-duration: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$icons = ! empty( $settings['icons'] ) && is_array( $settings['icons'] ) ? $settings['icons'] : array();

		if ( empty( $icons ) ) {
			return;
		}

		$wrapper_classes = array(
			'eap-widget',
			'eap-social-icons',
			'eap-social-icons--' . ( ! empty( $settings['layout'] ) ? $settings['layout'] : 'inline' ),
		);
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
			<?php foreach ( $icons as $index => $item ) : ?>
				<?php
				$item_key = 'social_icon_link_' . $index;
				if ( ! empty( $item['link']['url'] ) ) {
					$this->add_link_attributes( $item_key, $item['link'] );
				}
				?>
				<div class="eap-social-icons__item">
					<a class="eap-social-icons__link" <?php echo $this->get_render_attribute_string( $item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-label="<?php echo esc_attr( ! empty( $item['label'] ) ? $item['label'] : __( 'Social icon', 'elementor-animatepro' ) ); ?>">
						<span class="eap-social-icons__icon">
							<?php Icons_Manager::render_icon( $item['social_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
