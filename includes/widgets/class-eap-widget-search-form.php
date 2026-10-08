<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

/**
 * Search Form widget.
 *
 * A real WordPress search form — a GET form to the site root carrying `s` —
 * rather than a styled box that only looks like one. Three layouts: the button
 * beside the field, below it, or tucked inside the field's right edge.
 *
 * It can also scope the search to one post type, which WordPress supports
 * through a `post_type` parameter alongside `s`.
 *
 * The nearest siblings are Nav Menu and Mega Menu, which carry navigation, not
 * a query; nothing else in the plugin submits a search. CSS only — no
 * JavaScript, so it keeps working with scripts off.
 */
class EAP_Widget_Search_Form extends EAP_Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-search-form';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Search Form', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-site-search';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'search', 'form', 'find', 'input', 'field', 'query' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-search-form' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_content_section();
		$this->register_layout_style();
		$this->register_field_style();
		$this->register_button_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	/**
	 * Content section.
	 *
	 * @return void
	 */
	protected function register_content_section() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Search Form', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'placeholder',
			array(
				'label'   => __( 'Placeholder', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Search…', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'field_label',
			array(
				'label'       => __( 'Field Label', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Search for:', 'elementor-animatepro' ),
				'description' => __( 'Read out by screen readers. Hidden on screen unless you turn the next switch on — a placeholder alone is not a label.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_label',
			array(
				'label'        => __( 'Show Label', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'button_type',
			array(
				'label'   => __( 'Button', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => array(
					'icon' => __( 'Icon', 'elementor-animatepro' ),
					'text' => __( 'Text', 'elementor-animatepro' ),
					'both' => __( 'Icon and Text', 'elementor-animatepro' ),
					'none' => __( 'None', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'     => __( 'Button Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Search', 'elementor-animatepro' ),
				'condition' => array( 'button_type' => array( 'text', 'both' ) ),
			)
		);

		$this->add_control(
			'button_icon',
			array(
				'label'     => __( 'Button Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'eicon-search',
					'library' => 'eicons',
				),
				'condition' => array( 'button_type' => array( 'icon', 'both' ) ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => array(
					'inline' => __( 'Button Beside Field', 'elementor-animatepro' ),
					'inset'  => __( 'Button Inside Field', 'elementor-animatepro' ),
					'stacked' => __( 'Button Below Field', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'post_type',
			array(
				'label'       => __( 'Search Within', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => $this->get_post_type_options(),
				'description' => __( 'Leave on Everything for a normal site-wide search.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Post types a search can be scoped to.
	 *
	 * @return array<string, string>
	 */
	protected function get_post_type_options() {
		$options = array( '' => __( 'Everything', 'elementor-animatepro' ) );

		foreach ( get_post_types( array( 'public' => true, 'exclude_from_search' => false ), 'objects' ) as $post_type ) {
			if ( 'attachment' === $post_type->name ) {
				continue;
			}

			$options[ $post_type->name ] = $post_type->labels->name;
		}

		return $options;
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	/**
	 * Layout style section.
	 *
	 * @return void
	 */
	protected function register_layout_style() {
		$this->start_controls_section(
			'section_layout_style',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'max_width',
			array(
				'label'      => __( 'Max Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 160,
						'max' => 900,
					),
					'%'  => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sf' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .eap-search-form' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array(
					'unit' => 'px',
					'size' => 8,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sf' => '--eap-sf-gap: {{SIZE}}px;',
				),
				'condition'  => array( 'layout!' => 'inset' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Field style section.
	 *
	 * @return void
	 */
	protected function register_field_style() {
		$this->start_controls_section(
			'section_field_style',
			array(
				'label' => __( 'Field', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'field_typography',
				'selector' => '{{WRAPPER}} .eap-sf__input',
			)
		);

		$this->add_control(
			'field_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-sf__input' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'field_placeholder_color',
			array(
				'label'     => __( 'Placeholder Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-sf__input::placeholder' => 'color: {{VALUE}}; opacity: 1;',
				),
			)
		);

		$this->add_control(
			'field_background',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-sf__input' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'field_border',
				'selector' => '{{WRAPPER}} .eap-sf__input',
			)
		);

		$this->add_responsive_control(
			'field_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sf__input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'field_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sf__input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'field_focus_color',
			array(
				'label'     => __( 'Focus Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-sf__input:focus' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'field_shadow',
				'selector' => '{{WRAPPER}} .eap-sf__input',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Button style section.
	 *
	 * @return void
	 */
	protected function register_button_style() {
		$this->start_controls_section(
			'section_button_style',
			array(
				'label'     => __( 'Button', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'button_type!' => 'none' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .eap-sf__button',
			)
		);

		$this->add_responsive_control(
			'button_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 48 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sf__button svg'  => 'width: {{SIZE}}px; height: {{SIZE}}px;',
					'{{WRAPPER}} .eap-sf__button i'    => 'font-size: {{SIZE}}px;',
				),
				'condition'  => array( 'button_type' => array( 'icon', 'both' ) ),
			)
		);

		$this->start_controls_tabs( 'button_tabs' );

		$this->start_controls_tab(
			'button_tab_normal',
			array( 'label' => __( 'Normal', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-sf__button'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-sf__button svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-sf__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_tab_hover',
			array( 'label' => __( 'Hover', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'button_color_hover',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-sf__button:hover'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-sf__button:focus'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-sf__button:hover svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-sf__button:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eap-sf__button:focus' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'button_border',
				'selector'  => '{{WRAPPER}} .eap-sf__button',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sf__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sf__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
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

		$layout      = ! empty( $settings['layout'] ) ? $settings['layout'] : 'inline';
		$button_type = ! empty( $settings['button_type'] ) ? $settings['button_type'] : 'icon';
		$placeholder = (string) ( $settings['placeholder'] ?? '' );
		$label       = trim( (string) ( $settings['field_label'] ?? '' ) );
		$show_label  = 'yes' === ( $settings['show_label'] ?? '' );
		$post_type   = (string) ( $settings['post_type'] ?? '' );
		$button_text = (string) ( $settings['button_text'] ?? '' );

		if ( '' === $label ) {
			$label = __( 'Search for:', 'elementor-animatepro' );
		}

		// Unique per instance so the label points at this field and not at
		// another Search Form on the same page.
		$field_id = 'eap-sf-' . $this->get_id();

		$classes = array(
			'eap-sf',
			'eap-sf--' . $layout,
			'eap-sf--button-' . $button_type,
		);
		?>
		<div class="eap-widget eap-search-form">
			<form role="search" method="get" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label for="<?php echo esc_attr( $field_id ); ?>" class="eap-sf__label<?php echo $show_label ? '' : ' eap-sf__label--hidden'; ?>">
					<?php echo esc_html( $label ); ?>
				</label>

				<div class="eap-sf__row">
					<input
						type="search"
						id="<?php echo esc_attr( $field_id ); ?>"
						class="eap-sf__input"
						name="s"
						value="<?php echo esc_attr( get_search_query() ); ?>"
						placeholder="<?php echo esc_attr( $placeholder ); ?>"
					/>

					<?php if ( '' !== $post_type ) : ?>
						<input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>" />
					<?php endif; ?>

					<?php if ( 'none' !== $button_type ) : ?>
						<button type="submit" class="eap-sf__button">
							<?php if ( in_array( $button_type, array( 'icon', 'both' ), true ) ) : ?>
								<span class="eap-sf__icon" aria-hidden="true">
									<?php $this->eap_render_icon( $settings['button_icon'] ?? array() ); ?>
								</span>
							<?php endif; ?>

							<?php if ( in_array( $button_type, array( 'text', 'both' ), true ) && '' !== $button_text ) : ?>
								<span class="eap-sf__button-text"><?php echo esc_html( $button_text ); ?></span>
							<?php else : ?>
								<span class="eap-sf__label--hidden"><?php esc_html_e( 'Search', 'elementor-animatepro' ); ?></span>
							<?php endif; ?>
						</button>
					<?php endif; ?>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Print an icon through Elementor, so it works in both icon modes.
	 *
	 * @param array $icon Icon control value.
	 * @return void
	 */
	protected function eap_render_icon( $icon ) {
		if ( empty( $icon['value'] ) ) {
			return;
		}

		\Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
	}
}
