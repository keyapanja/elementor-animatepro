<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

class EAP_Widget_Advanced_Animated_Text extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-advanced-animated-text';
	}

	public function get_title() {
		return __( 'Advanced Animated Text', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-animated-headline';
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'advanced-animated-text' );
	}

	public function get_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-visibility-script',
			'eap-advanced-animated-text-script',
		);
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => __( 'Content', 'elementor-animatepro' ) ) );
		$this->add_control(
			'effect',
			array(
				'label'   => __( 'Effect', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'typewriter',
				'options' => array(
					'typewriter' => __( 'Typewriter', 'elementor-animatepro' ),
					'rainbow'    => __( 'Rainbow', 'elementor-animatepro' ),
					'shadow'     => __( 'Animated Shadow', 'elementor-animatepro' ),
					'changing'   => __( 'Changing Text', 'elementor-animatepro' ),
					'neon'       => __( 'Glowing Neon', 'elementor-animatepro' ),
					'glitch'     => __( 'Glitch', 'elementor-animatepro' ),
					'shine'      => __( 'Shine / Sweep', 'elementor-animatepro' ),
				),
			)
		);
		$this->add_control( 'before_text', array( 'label' => __( 'Before Text', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'We build', 'elementor-animatepro' ), 'label_block' => true ) );
		$this->add_control(
			'main_text',
			array(
				'label'       => __( 'Main Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'beautiful motion experiences', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array(
					'effect!' => array( 'typewriter', 'changing' ),
				),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control( 'text', array( 'label' => __( 'Text', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'animated websites', 'elementor-animatepro' ), 'label_block' => true ) );
		$this->add_control(
			'animated_terms',
			array(
				'label'       => __( 'Animated Texts', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'text' => __( 'animated websites', 'elementor-animatepro' ) ),
					array( 'text' => __( 'smooth interfaces', 'elementor-animatepro' ) ),
					array( 'text' => __( 'creative sections', 'elementor-animatepro' ) ),
				),
				'title_field' => '{{{ text }}}',
				'condition'   => array(
					'effect' => array( 'typewriter', 'changing' ),
				),
			)
		);
		$this->add_control( 'after_text', array( 'label' => __( 'After Text', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => '', 'label_block' => true ) );
		$this->add_control(
			'tag',
			array(
				'label'   => __( 'HTML Tag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array( 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6', 'div' => 'DIV', 'p' => 'P', 'span' => 'SPAN' ),
			)
		);
		$this->add_control(
			'animation_speed',
			array(
				'label'     => __( 'Animation Speed (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 90,
				'min'       => 20,
				'condition' => array(
					'effect' => array( 'typewriter', 'changing' ),
				),
			)
		);
		$this->add_control(
			'pause',
			array(
				'label'     => __( 'Pause Between Texts (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1500,
				'min'       => 200,
				'condition' => array(
					'effect' => array( 'typewriter', 'changing' ),
				),
			)
		);
		$this->add_control(
			'effect_duration',
			array(
				'label'      => __( 'Effect Duration', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array(
					's' => array(
						'min'  => 1,
						'max'  => 12,
						'step' => 0.1,
					),
				),
				'default'    => array(
					'size' => 4,
					'unit' => 's',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-advanced-animated-text' => '--eap-advanced-text-duration: {{SIZE}}s;',
				),
				'condition'  => array(
					'effect!' => array( 'typewriter', 'changing' ),
				),
			)
		);
		$this->add_alignment_control();
		$this->end_controls_section();

		$this->start_controls_section(
			'starter_animation_section',
			array(
				'label' => __( 'Starter Animation', 'elementor-animatepro' ),
			)
		);
		$this->add_control(
			'starter_animation',
			array(
				'label'       => __( 'Animation', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'options'     => array(
					'none'        => __( 'None', 'elementor-animatepro' ),
					'fade-up'     => __( 'Fade Up', 'elementor-animatepro' ),
					'fade-in'     => __( 'Fade In', 'elementor-animatepro' ),
					'slide-left'  => __( 'Slide From Left', 'elementor-animatepro' ),
					'slide-right' => __( 'Slide From Right', 'elementor-animatepro' ),
					'slide-down'  => __( 'Slide Down', 'elementor-animatepro' ),
					'zoom-in'     => __( 'Zoom In', 'elementor-animatepro' ),
				),
				'description' => __( 'A simple entrance animation for the whole text line. It plays when this widget enters the viewport.', 'elementor-animatepro' ),
			)
		);
		$this->add_control(
			'starter_duration',
			array(
				'label'     => __( 'Duration (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 700,
				'min'       => 100,
				'step'      => 50,
				'condition' => array(
					'starter_animation!' => 'none',
				),
			)
		);
		$this->add_control(
			'starter_delay',
			array(
				'label'     => __( 'Delay (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'min'       => 0,
				'step'      => 50,
				'condition' => array(
					'starter_animation!' => 'none',
				),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'style_section', array( 'label' => __( 'Style', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typography', 'selector' => '{{WRAPPER}} .eap-advanced-animated-text__line' ) );
		$this->add_control( 'text_color', array( 'label' => __( 'Text Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#0f172a', 'selectors' => array( '{{WRAPPER}} .eap-advanced-animated-text__line' => 'color: {{VALUE}};', '{{WRAPPER}} .eap-advanced-animated-text' => '--eap-advanced-text-base: {{VALUE}};' ) ) );
		$this->add_control( 'accent_color', array( 'label' => __( 'Accent Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#ff6b2c', 'selectors' => array( '{{WRAPPER}} .eap-advanced-animated-text' => '--eap-advanced-text-accent: {{VALUE}};' ) ) );
		$this->add_control( 'second_color', array( 'label' => __( 'Second Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#00c2ff', 'selectors' => array( '{{WRAPPER}} .eap-advanced-animated-text' => '--eap-advanced-text-second: {{VALUE}};' ) ) );
		$this->add_control( 'third_color', array( 'label' => __( 'Third Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#8b5cf6', 'selectors' => array( '{{WRAPPER}} .eap-advanced-animated-text' => '--eap-advanced-text-third: {{VALUE}};' ) ) );
		$this->add_control( 'caret_color', array( 'label' => __( 'Caret Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#ff6b2c', 'selectors' => array( '{{WRAPPER}} .eap-advanced-animated-text__caret' => 'background-color: {{VALUE}};' ), 'condition' => array( 'effect' => 'typewriter' ) ) );
		$this->add_group_control( Group_Control_Text_Shadow::get_type(), array( 'name' => 'text_shadow', 'selector' => '{{WRAPPER}} .eap-advanced-animated-text__line' ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$effect   = isset( $settings['effect'] ) ? sanitize_key( $settings['effect'] ) : 'typewriter';
		$tag      = \Elementor\Utils::validate_html_tag( $settings['tag'] );
		$terms    = $this->get_terms( $settings );
		$text     = ! empty( $settings['main_text'] ) ? sanitize_textarea_field( $settings['main_text'] ) : '';
		$before   = ! empty( $settings['before_text'] ) ? sanitize_text_field( $settings['before_text'] ) : '';
		$after    = ! empty( $settings['after_text'] ) ? sanitize_text_field( $settings['after_text'] ) : '';
		$speed    = isset( $settings['animation_speed'] ) ? absint( $settings['animation_speed'] ) : 90;
		$pause    = isset( $settings['pause'] ) ? absint( $settings['pause'] ) : 1500;
		$starter  = isset( $settings['starter_animation'] ) ? sanitize_key( $settings['starter_animation'] ) : 'none';
		$duration = isset( $settings['starter_duration'] ) ? absint( $settings['starter_duration'] ) : 700;
		$delay    = isset( $settings['starter_delay'] ) ? absint( $settings['starter_delay'] ) : 0;
		$classes  = array(
			'eap-widget',
			'eap-advanced-animated-text',
			'eap-advanced-animated-text--' . $effect,
		);

		if ( 'none' !== $starter ) {
			$classes[] = 'eap-motion';

			if ( 'fade-up' !== $starter ) {
				$classes[] = 'eap-motion-' . $starter;
			}
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-eap-advanced-text="<?php echo esc_attr( wp_json_encode( $terms ) ); ?>" data-eap-advanced-text-effect="<?php echo esc_attr( $effect ); ?>" data-eap-speed="<?php echo esc_attr( $speed ); ?>" data-eap-pause="<?php echo esc_attr( $pause ); ?>" data-eap-duration="<?php echo esc_attr( $duration ); ?>" data-eap-delay="<?php echo esc_attr( $delay ); ?>">
			<<?php echo esc_html( $tag ); ?> class="eap-advanced-animated-text__line">
				<?php if ( '' !== $before ) : ?>
					<span class="eap-advanced-animated-text__before"><?php echo esc_html( $before ); ?> </span>
				<?php endif; ?>
				<?php if ( in_array( $effect, array( 'typewriter', 'changing' ), true ) ) : ?>
					<span class="eap-advanced-animated-text__dynamic"><?php echo esc_html( ! empty( $terms[0] ) ? $terms[0] : __( 'animated text', 'elementor-animatepro' ) ); ?></span>
					<?php if ( 'typewriter' === $effect ) : ?>
						<span class="eap-advanced-animated-text__caret" aria-hidden="true"></span>
					<?php endif; ?>
				<?php else : ?>
					<span class="eap-advanced-animated-text__static" data-text="<?php echo esc_attr( $text ); ?>"><?php echo nl2br( esc_html( $text ) ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== $after ) : ?>
					<span class="eap-advanced-animated-text__after"> <?php echo esc_html( $after ); ?></span>
				<?php endif; ?>
			</<?php echo esc_html( $tag ); ?>>
		</div>
		<?php
	}

	/**
	 * Get repeater text terms.
	 *
	 * @param array<string, mixed> $settings Widget settings.
	 * @return array<int, string>
	 */
	private function get_terms( $settings ) {
		$terms = array();

		if ( ! empty( $settings['animated_terms'] ) && is_array( $settings['animated_terms'] ) ) {
			foreach ( $settings['animated_terms'] as $item ) {
				if ( ! empty( $item['text'] ) ) {
					$terms[] = sanitize_text_field( $item['text'] );
				}
			}
		}

		return $terms;
	}
}
