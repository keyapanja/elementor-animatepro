<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class EAP_Widget_Animated_Heading extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-animated-heading';
	}

	public function get_title() {
		return __( 'Animated Heading', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-t-letter';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => __( 'Content', 'elementor-animatepro' ) ) );
		$this->add_control( 'prefix_text', array( 'label' => __( 'Prefix', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Build', 'elementor-animatepro' ), 'label_block' => true ) );
		$this->add_control( 'highlight_text', array( 'label' => __( 'Highlighted Text', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Motion', 'elementor-animatepro' ), 'label_block' => true ) );
		$this->add_control( 'suffix_text', array( 'label' => __( 'Suffix', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'into every section', 'elementor-animatepro' ), 'label_block' => true ) );
		$this->add_control(
			'html_tag',
			array(
				'label'   => __( 'HTML Tag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array( 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4' ),
			)
		);
		$this->add_control(
			'animation_style',
			array(
				'label'   => __( 'Highlight Animation', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'underline',
				'options' => array(
					'underline' => __( 'Underline Sweep', 'elementor-animatepro' ),
					'fill'      => __( 'Background Fill', 'elementor-animatepro' ),
					'slide'     => __( 'Slide In', 'elementor-animatepro' ),
				),
			)
		);
		$this->add_alignment_control();
		$this->end_controls_section();

		$this->start_controls_section( 'style_section', array( 'label' => __( 'Style', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .eap-heading' ) );
		$this->add_control( 'text_color', array( 'label' => __( 'Text Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#111827', 'selectors' => array( '{{WRAPPER}} .eap-heading' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'highlight_color', array( 'label' => __( 'Highlight Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#ff6b2c', 'selectors' => array( '{{WRAPPER}} .eap-heading-highlight' => '--eap-highlight: {{VALUE}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$tag      = \Elementor\Utils::validate_html_tag( $settings['html_tag'] );
		?>
		<div class="eap-widget eap-animated-heading eap-highlight-<?php echo esc_attr( $settings['animation_style'] ); ?>">
			<<?php echo esc_html( $tag ); ?> class="eap-heading">
				<?php if ( ! empty( $settings['prefix_text'] ) ) : ?><span><?php echo esc_html( $settings['prefix_text'] ); ?></span><?php endif; ?>
				<span class="eap-heading-highlight"><?php echo esc_html( $settings['highlight_text'] ); ?></span>
				<?php if ( ! empty( $settings['suffix_text'] ) ) : ?><span><?php echo esc_html( $settings['suffix_text'] ); ?></span><?php endif; ?>
			</<?php echo esc_html( $tag ); ?>>
		</div>
		<?php
	}
}
