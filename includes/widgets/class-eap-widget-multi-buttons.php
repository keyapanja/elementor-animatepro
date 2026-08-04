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
use Elementor\Repeater;

class EAP_Widget_Multi_Buttons extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-multi-buttons';
	}

	public function get_title() {
		return __( 'Multi Buttons', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_keywords() {
		return array( 'button', 'buttons', 'group', 'cta', 'multi', 'toggle' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'multi-buttons' );
	}

	protected function register_controls() {
		$this->register_layout_controls();
		$this->register_buttons_controls();
		$this->register_group_style_controls();
	}

	/* -------------------------------------------------------------------------
	 * Content: layout
	 * ---------------------------------------------------------------------- */

	protected function register_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Group Style', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'separate',
				'options' => array(
					'separate' => __( 'Separate', 'elementor-animatepro' ),
					'joined'   => __( 'Joined', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
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
					'{{WRAPPER}} .eap-multi-buttons' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'equal_width',
			array(
				'label'        => __( 'Equal Width', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'selectors'    => array(
					'{{WRAPPER}} .eap-multi-buttons__group' => 'width: 100%;',
					'{{WRAPPER}} .eap-multi-button'         => 'flex: 1 1 0; justify-content: center;',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-multi-buttons--separate .eap-multi-buttons__group' => 'gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'layout' => 'separate' ),
			)
		);

		$this->add_control(
			'stack_on',
			array(
				'label'   => __( 'Stack On', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'   => __( 'None', 'elementor-animatepro' ),
					'tablet' => __( 'Tablet & Down', 'elementor-animatepro' ),
					'mobile' => __( 'Mobile', 'elementor-animatepro' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Content: buttons repeater (with per-item styling)
	 * ---------------------------------------------------------------------- */

	protected function register_buttons_controls() {
		$this->start_controls_section(
			'section_buttons',
			array(
				'label' => __( 'Buttons', 'elementor-animatepro' ),
			)
		);

		$repeater = new Repeater();

		$selector = '{{WRAPPER}} {{CURRENT_ITEM}}.eap-multi-button';

		// --- Content ---
		$repeater->add_control(
			'text',
			array(
				'label'       => __( 'Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Button', 'elementor-animatepro' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'elementor-animatepro' ),
				'type'    => Controls_Manager::ICONS,
				'skin'    => 'inline',
			)
		);

		$repeater->add_control(
			'icon_position',
			array(
				'label'     => __( 'Icon Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'before',
				'options'   => array(
					'before' => __( 'Before', 'elementor-animatepro' ),
					'after'  => __( 'After', 'elementor-animatepro' ),
				),
				'condition' => array( 'icon[value]!' => '' ),
			)
		);

		$repeater->add_responsive_control(
			'icon_spacing',
			array(
				'label'      => __( 'Icon Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					$selector => 'gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'icon[value]!' => '' ),
			)
		);

		$repeater->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 8, 'max' => 60 ),
					'em' => array( 'min' => 0.5, 'max' => 4 ),
				),
				'selectors'  => array(
					$selector => '--eap-mb-icon-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'icon[value]!' => '' ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'         => __( 'Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'placeholder'   => __( 'https://example.com', 'elementor-animatepro' ),
			)
		);

		// --- Style ---
		$repeater->add_control(
			'style_heading',
			array(
				'label'     => __( 'Style', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => $selector,
			)
		);

		$repeater->start_controls_tabs( 'button_state_tabs' );

		$repeater->start_controls_tab( 'tab_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$repeater->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => 'color: {{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'bg_color',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => 'background-color: {{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'border_color',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => 'border-color: {{VALUE}};',
				),
			)
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'tab_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$repeater->add_control(
			'text_color_hover',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$selector . ':hover, ' . $selector . ':focus' => 'color: {{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'bg_color_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$selector . ':hover, ' . $selector . ':focus' => 'background-color: {{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'border_color_hover',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$selector . ':hover, ' . $selector . ':focus' => 'border-color: {{VALUE}};',
				),
			)
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$repeater->add_responsive_control(
			'border_width',
			array(
				'label'      => __( 'Border Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'separator'  => 'before',
				'selectors'  => array(
					$selector => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$repeater->add_responsive_control(
			'border_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 50,
					'right'    => 50,
					'bottom'   => 50,
					'left'     => 50,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$repeater->add_responsive_control(
			'item_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 14,
					'right'  => 34,
					'bottom' => 14,
					'left'   => 34,
					'unit'   => 'px',
				),
				'selectors'  => array(
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => $selector,
			)
		);

		$this->add_control(
			'buttons',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array(
						'text'             => __( 'Get Started', 'elementor-animatepro' ),
						'text_color'       => '#ffffff',
						'bg_color'         => '#ef3e56',
						'text_color_hover' => '#ffffff',
						'bg_color_hover'   => '#d92e45',
					),
					array(
						'text'               => __( 'Know More', 'elementor-animatepro' ),
						'text_color'         => '#1f2937',
						'bg_color'           => '#ffffff',
						'border_color'       => '#d1d5db',
						'border_width'       => array(
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'unit'     => 'px',
							'isLinked' => true,
						),
						'text_color_hover'   => '#ffffff',
						'bg_color_hover'     => '#1f2937',
						'border_color_hover' => '#1f2937',
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Style: button group (joined / toolbar)
	 * ---------------------------------------------------------------------- */

	protected function register_group_style_controls() {
		$this->start_controls_section(
			'section_group_style',
			array(
				'label'     => __( 'Joined Buttons', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'joined' ),
			)
		);

		$this->add_responsive_control(
			'group_radius',
			array(
				'label'      => __( 'Outer Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-multi-buttons__group' => '--eap-mb-group-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'divider_color',
			array(
				'label'     => __( 'Divider Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.12)',
				'selectors' => array(
					'{{WRAPPER}} .eap-multi-buttons__group' => '--eap-mb-divider-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'divider_width',
			array(
				'label'      => __( 'Divider Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 8 ) ),
				'default'    => array( 'size' => 1, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-multi-buttons__group' => '--eap-mb-divider-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Render
	 * ---------------------------------------------------------------------- */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$buttons  = ! empty( $settings['buttons'] ) && is_array( $settings['buttons'] ) ? $settings['buttons'] : array();

		if ( empty( $buttons ) ) {
			return;
		}

		$layout   = ! empty( $settings['layout'] ) ? $settings['layout'] : 'separate';
		$stack_on = ! empty( $settings['stack_on'] ) ? $settings['stack_on'] : 'none';

		$classes = array(
			'eap-widget',
			'eap-multi-buttons',
			'eap-multi-buttons--' . $layout,
		);
		if ( 'none' !== $stack_on ) {
			$classes[] = 'eap-multi-buttons--stack-' . $stack_on;
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<div class="eap-multi-buttons__group">
				<?php
				foreach ( $buttons as $index => $item ) {
					$this->render_button( $item, $index );
				}
				?>
			</div>
		</div>
		<?php
	}

	protected function render_button( $item, $index ) {
		$key      = 'button_' . $index;
		$item_id  = ! empty( $item['_id'] ) ? $item['_id'] : (string) $index;
		$position = ! empty( $item['icon_position'] ) ? $item['icon_position'] : 'before';

		$classes = array(
			'eap-multi-button',
			'elementor-repeater-item-' . $item_id,
			'eap-multi-button--icon-' . $position,
		);

		$this->add_render_attribute( $key, 'class', $classes );

		$tag = 'span';
		if ( ! empty( $item['link']['url'] ) ) {
			$tag = 'a';
			$this->add_link_attributes( $key, $item['link'] );
		}

		$icon_html = '';
		if ( ! empty( $item['icon']['value'] ) ) {
			ob_start();
			Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) );
			$icon_html = '<span class="eap-multi-button__icon">' . ob_get_clean() . '</span>';
		}

		$text_html = '';
		if ( '' !== trim( (string) $item['text'] ) ) {
			$text_html = '<span class="eap-multi-button__text">' . esc_html( $item['text'] ) . '</span>';
		}
		?>
		<<?php echo esc_html( $tag ); ?> <?php $this->print_render_attribute_string( $key ); ?>>
			<?php
			if ( 'after' === $position ) {
				echo $text_html . $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo $icon_html . $text_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</<?php echo esc_html( $tag ); ?>>
		<?php
	}
}
