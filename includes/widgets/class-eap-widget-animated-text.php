<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;

class EAP_Widget_Animated_Text extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-animated-text';
	}

	public function get_title() {
		return __( 'Animated Text', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-animation-text';
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'animated-text' );
	}

	public function get_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-visibility-script',
			'eap-animated-text-script',
		);
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'text',
			array(
				'label'       => __( 'Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Minimal motion, clear controls, and editor-friendly animations.', 'elementor-animatepro' ),
				'label_block' => true,
				'description' => __( 'Line breaks are respected for normal text and used as split units when Split Unit is set to Lines.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'tag',
			array(
				'label'   => __( 'HTML Tag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'DIV',
					'p'    => 'P',
					'span' => 'SPAN',
				),
			)
		);

		$this->add_control(
			'animation',
			array(
				'label'       => __( 'Animation', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'fade',
				'options'     => array(
					'none'   => __( 'None', 'elementor-animatepro' ),
					'fade'   => __( 'Fade', 'elementor-animatepro' ),
					'slide'  => __( 'Slide', 'elementor-animatepro' ),
					'split'  => __( 'Split Text', 'elementor-animatepro' ),
					'zoom'   => __( 'Zoom', 'elementor-animatepro' ),
					'unfold' => __( 'Unfold', 'elementor-animatepro' ),
					'reveal' => __( 'Reveal', 'elementor-animatepro' ),
					'blur'   => __( 'Blur', 'elementor-animatepro' ),
				),
				'description' => __( 'Fade, slide, and split can enter from a selected direction. Zoom, unfold, reveal, and blur use their own natural motion.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'     => __( 'Direction', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'up',
				'options'   => array(
					'up'    => __( 'Bottom to Top', 'elementor-animatepro' ),
					'down'  => __( 'Top to Bottom', 'elementor-animatepro' ),
					'left'  => __( 'Right to Left', 'elementor-animatepro' ),
					'right' => __( 'Left to Right', 'elementor-animatepro' ),
				),
				'condition' => array(
					'animation' => array( 'fade', 'slide', 'split' ),
				),
			)
		);

		$this->add_control(
			'split_unit',
			array(
				'label'       => __( 'Split Unit', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'words',
				'options'     => array(
					'words' => __( 'Words', 'elementor-animatepro' ),
					'chars' => __( 'Characters', 'elementor-animatepro' ),
					'lines' => __( 'Lines', 'elementor-animatepro' ),
				),
				'description' => __( 'Words animate word by word, characters animate letter by letter, and lines use each line break as a separate animated line.', 'elementor-animatepro' ),
				'condition'   => array(
					'animation' => 'split',
				),
			)
		);

		$this->add_control(
			'distance',
			array(
				'label'      => __( 'Movement Distance', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 220,
						'step' => 1,
					),
				),
				'default'    => array(
					'size' => 34,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-animated-text-v2' => '--eap-at-distance: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'animation' => array( 'fade', 'slide', 'split' ),
				),
			)
		);

		$this->add_control(
			'duration',
			array(
				'label'   => __( 'Duration (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 850,
				'min'     => 100,
				'step'    => 50,
			)
		);

		$this->add_control(
			'delay',
			array(
				'label'   => __( 'Delay (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'step'    => 50,
			)
		);

		$this->add_control(
			'stagger',
			array(
				'label'     => __( 'Split Stagger (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 55,
				'min'       => 0,
				'step'      => 5,
				'condition' => array(
					'animation' => 'split',
				),
			)
		);

		$this->add_control(
			'easing',
			array(
				'label'   => __( 'Easing', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'smooth',
				'options' => array(
					'smooth'  => __( 'Smooth', 'elementor-animatepro' ),
					'ease'    => __( 'Ease', 'elementor-animatepro' ),
					'linear'  => __( 'Linear', 'elementor-animatepro' ),
					'bouncy'  => __( 'Soft Bounce', 'elementor-animatepro' ),
					'elastic' => __( 'Elastic Feel', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'play_once',
			array(
				'label'        => __( 'Play Once', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Turn this on if the text should stay visible after its first entrance animation.', 'elementor-animatepro' ),
			)
		);

		$this->add_alignment_control();
		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			array(
				'label' => __( 'Style', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .eap-animated-text-v2__line',
			)
		);

		$this->add_control(
			'color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => array(
					'{{WRAPPER}} .eap-animated-text-v2__line' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hover_color',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff6b2c',
				'selectors' => array(
					'{{WRAPPER}} .eap-animated-text-v2__unit:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'text_shadow',
				'selector' => '{{WRAPPER}} .eap-animated-text-v2__line',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$text      = isset( $settings['text'] ) ? sanitize_textarea_field( $settings['text'] ) : '';
		$text      = '' !== trim( $text ) ? $text : __( 'Animated text', 'elementor-animatepro' );
		$tag       = \Elementor\Utils::validate_html_tag( $settings['tag'] );
		$animation = isset( $settings['animation'] ) ? sanitize_key( $settings['animation'] ) : 'fade';
		$direction = isset( $settings['direction'] ) ? sanitize_key( $settings['direction'] ) : 'up';
		$split     = isset( $settings['split_unit'] ) ? sanitize_key( $settings['split_unit'] ) : 'words';
		$easing    = isset( $settings['easing'] ) ? sanitize_key( $settings['easing'] ) : 'smooth';
		$allowed   = array( 'none', 'fade', 'slide', 'split', 'zoom', 'unfold', 'reveal', 'blur' );

		if ( ! in_array( $animation, $allowed, true ) ) {
			$animation = 'fade';
		}

		if ( ! in_array( $direction, array( 'up', 'down', 'left', 'right' ), true ) ) {
			$direction = 'up';
		}

		if ( ! in_array( $split, array( 'words', 'chars', 'lines' ), true ) ) {
			$split = 'words';
		}

		$classes = array(
			'eap-widget',
			'eap-animated-text-v2',
			'eap-animated-text-v2--' . $animation,
			'eap-animated-text-v2--dir-' . $direction,
			'eap-animated-text-v2--ease-' . $easing,
		);

		if ( 'split' === $animation ) {
			$classes[] = 'eap-animated-text-v2--split-' . $split;
		}
		?>
		<div
			class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			data-eap-animated-text-v2="true"
			data-eap-animation="<?php echo esc_attr( $animation ); ?>"
			data-eap-duration="<?php echo esc_attr( absint( $settings['duration'] ) ); ?>"
			data-eap-delay="<?php echo esc_attr( absint( $settings['delay'] ) ); ?>"
			data-eap-stagger="<?php echo esc_attr( absint( $settings['stagger'] ) ); ?>"
			data-eap-play-once="<?php echo esc_attr( 'yes' === ( $settings['play_once'] ?? '' ) ? 'true' : 'false' ); ?>"
		>
			<<?php echo esc_html( $tag ); ?> class="eap-animated-text-v2__line">
				<?php
				if ( 'split' === $animation ) {
					$this->render_split_text( $text, $split );
				} else {
					echo '<span class="eap-animated-text-v2__unit">' . nl2br( esc_html( $text ) ) . '</span>';
				}
				?>
			</<?php echo esc_html( $tag ); ?>>
		</div>
		<?php
	}

	private function render_split_text( $text, $split ) {
		$parts = $this->get_text_parts( $text, $split );
		$last  = count( $parts ) - 1;

		foreach ( $parts as $index => $part ) {
			$output = 'chars' === $split && ' ' === $part ? '&nbsp;' : esc_html( $part );
			echo '<span class="eap-animated-text-v2__unit">' . $output . '</span>';

			if ( 'words' === $split && $index < $last ) {
				echo ' ';
			}
		}
	}

	private function get_text_parts( $text, $split ) {
		if ( 'chars' === $split ) {
			return preg_split( '//u', $text, -1, PREG_SPLIT_NO_EMPTY );
		}

		if ( 'lines' === $split ) {
			return array_values( array_filter( preg_split( '/\r\n|\r|\n/', $text ), 'strlen' ) );
		}

		return preg_split( '/\s+/', trim( $text ), -1, PREG_SPLIT_NO_EMPTY );
	}
}
