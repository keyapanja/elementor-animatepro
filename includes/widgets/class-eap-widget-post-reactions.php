<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

/**
 * Post Reactions — a row of emoji reactions for the CURRENT post with live
 * counts. Visitors can react without logging in; one reaction per visitor is
 * tracked in localStorage (click again to remove, a different one to switch).
 * Counts persist to post meta via a nonce-protected AJAX endpoint (see
 * EAP_Reactions). CSS + post-reactions.js.
 */
class EAP_Widget_Post_Reactions extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-post-reactions';
	}

	public function get_title() {
		return __( 'Post Reactions', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-favorite';
	}

	public function get_keywords() {
		return array( 'post', 'reactions', 'emoji', 'like', 'love', 'react', 'dynamic' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'post-reactions' );
	}

	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-post-reactions-script' );
	}

	protected function register_controls() {
		$this->register_reactions_section();
		$this->register_button_style();
		$this->register_text_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_reactions_section() {
		$this->start_controls_section(
			'section_reactions',
			array(
				'label' => __( 'Reactions', 'elementor-animatepro' ),
			)
		);

		$options = array();
		foreach ( EAP_Reactions::get_reactions() as $key => $def ) {
			$options[ $key ] = $def['emoji'] . '  ' . $def['label'];
		}

		$repeater = new Repeater();

		$repeater->add_control(
			'reaction',
			array(
				'label'   => __( 'Reaction', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'like',
				'options' => $options,
			)
		);

		$repeater->add_control(
			'custom_label',
			array(
				'label'       => __( 'Label', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Defaults to the reaction name', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'reactions',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ reaction }}}',
				'default'     => array(
					array( 'reaction' => 'like' ),
					array( 'reaction' => 'love' ),
					array( 'reaction' => 'party' ),
					array( 'reaction' => 'wow' ),
					array( 'reaction' => 'sad' ),
				),
			)
		);

		$this->add_control(
			'show_count',
			array(
				'label'        => __( 'Show Counts', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'show_label',
			array(
				'label'        => __( 'Show Labels', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'                => __( 'Alignment', 'elementor-animatepro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'left',
				'options'              => array(
					'left'   => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors'            => array(
					'{{WRAPPER}} .eap-post-reactions' => 'justify-content: {{VALUE}};',
				),
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_button_style() {
		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => __( 'Buttons', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-reactions' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'emoji_size',
			array(
				'label'      => __( 'Emoji Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 12, 'max' => 48 ) ),
				'default'    => array( 'size' => 20, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-reactions__emoji' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'btn_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 8, 'right' => 14, 'bottom' => 8, 'left' => 14, 'unit' => 'px', 'isLinked' => false ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-reactions__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'btn_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 50 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'default'    => array( 'size' => 30, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-reactions__btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'btn_tabs' );

		$this->start_controls_tab( 'btn_tab_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'btn_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f3f4f6',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-reactions__btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'btn_tab_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'btn_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e5e7eb',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-reactions__btn:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'btn_tab_active', array( 'label' => __( 'Selected', 'elementor-animatepro' ) ) );

		$this->add_control(
			'btn_bg_active',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e0e7ff',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-reactions__btn.is-active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_active_border',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-reactions__btn.is-active' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'btn_border',
				'selector'  => '{{WRAPPER}} .eap-post-reactions__btn',
				'separator' => 'before',
				'fields_options' => array(
					'border' => array( 'default' => 'solid' ),
					'width'  => array( 'default' => array( 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1, 'isLinked' => true ) ),
					'color'  => array( 'default' => '#e5e7eb' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_text_style() {
		$this->start_controls_section(
			'section_text_style',
			array(
				'label' => __( 'Count & Label', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#374151',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-reactions__count, {{WRAPPER}} .eap-post-reactions__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .eap-post-reactions__count, {{WRAPPER}} .eap-post-reactions__label',
				'fields_options' => array(
					'typography'  => array( 'default' => 'custom' ),
					'font_weight' => array( 'default' => '600' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['reactions'] ) && is_array( $settings['reactions'] ) ? $settings['reactions'] : array();

		if ( empty( $items ) ) {
			return;
		}

		$post_id = $this->eap_get_post_id();
		if ( ! $post_id ) {
			return;
		}

		$defs       = EAP_Reactions::get_reactions();
		$counts     = EAP_Reactions::get_counts( $post_id );
		$show_count = 'yes' === ( $settings['show_count'] ?? 'yes' );
		$show_label = 'yes' === ( $settings['show_label'] ?? '' );

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'             => array( 'eap-widget', 'eap-post-reactions' ),
				'data-eap-reactions' => '',
				'data-post'         => (string) $post_id,
				'data-nonce'        => wp_create_nonce( 'eap-react' ),
				'data-ajax-url'     => esc_url( admin_url( 'admin-ajax.php' ) ),
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php
			foreach ( $items as $item ) {
				$key = ! empty( $item['reaction'] ) ? $item['reaction'] : 'like';
				if ( ! isset( $defs[ $key ] ) ) {
					continue;
				}
				$emoji = $defs[ $key ]['emoji'];
				$label = ! empty( $item['custom_label'] ) ? $item['custom_label'] : $defs[ $key ]['label'];
				$count = isset( $counts[ $key ] ) ? (int) $counts[ $key ] : 0;
				?>
				<button type="button" class="eap-post-reactions__btn" data-reaction="<?php echo esc_attr( $key ); ?>" aria-label="<?php echo esc_attr( $label ); ?>">
					<span class="eap-post-reactions__emoji" aria-hidden="true"><?php echo esc_html( $emoji ); ?></span>
					<?php if ( $show_label ) : ?>
						<span class="eap-post-reactions__label"><?php echo esc_html( $label ); ?></span>
					<?php endif; ?>
					<?php if ( $show_count ) : ?>
						<span class="eap-post-reactions__count"><?php echo esc_html( (string) $count ); ?></span>
					<?php endif; ?>
				</button>
				<?php
			}
			?>
		</div>
		<?php
	}
}
