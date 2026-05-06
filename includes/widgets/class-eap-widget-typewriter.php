<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

class EAP_Widget_Typewriter extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-typewriter';
	}

	public function get_title() {
		return __( 'Typewriter Text', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-animation-text';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => __( 'Content', 'elementor-animatepro' ) ) );
		$this->add_control( 'prefix_text', array( 'label' => __( 'Before Text', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'We create', 'elementor-animatepro' ), 'label_block' => true ) );

		$repeater = new Repeater();
		$repeater->add_control( 'term', array( 'label' => __( 'Term', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'interactive websites', 'elementor-animatepro' ), 'label_block' => true ) );
		$this->add_control(
			'terms',
			array(
				'label'       => __( 'Typing Terms', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'term' => __( 'interactive websites', 'elementor-animatepro' ) ),
					array( 'term' => __( 'motion-rich landing pages', 'elementor-animatepro' ) ),
					array( 'term' => __( 'conversion-first experiences', 'elementor-animatepro' ) ),
				),
				'title_field' => '{{{ term }}}',
			)
		);
		$this->add_control( 'speed', array( 'label' => __( 'Typing Speed (ms)', 'elementor-animatepro' ), 'type' => Controls_Manager::NUMBER, 'default' => 90, 'min' => 20 ) );
		$this->add_control( 'pause', array( 'label' => __( 'Pause (ms)', 'elementor-animatepro' ), 'type' => Controls_Manager::NUMBER, 'default' => 1400, 'min' => 250 ) );
		$this->add_alignment_control();
		$this->end_controls_section();

		$this->start_controls_section( 'style_section', array( 'label' => __( 'Style', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typography', 'selector' => '{{WRAPPER}} .eap-typewriter-line' ) );
		$this->add_control( 'text_color', array( 'label' => __( 'Text Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#0f172a', 'selectors' => array( '{{WRAPPER}} .eap-typewriter-line' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'accent_color', array( 'label' => __( 'Accent Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#ff6b2c', 'selectors' => array( '{{WRAPPER}} .eap-typewriter-target' => 'color: {{VALUE}};', '{{WRAPPER}} .eap-typewriter-caret' => 'background-color: {{VALUE}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$terms    = array();
		if ( ! empty( $settings['terms'] ) ) {
			foreach ( $settings['terms'] as $item ) {
				if ( ! empty( $item['term'] ) ) {
					$terms[] = wp_strip_all_tags( $item['term'] );
				}
			}
		}
		?>
		<div class="eap-widget eap-typewriter" data-eap-typewriter="<?php echo esc_attr( wp_json_encode( $terms ) ); ?>" data-eap-speed="<?php echo esc_attr( absint( $settings['speed'] ) ); ?>" data-eap-pause="<?php echo esc_attr( absint( $settings['pause'] ) ); ?>">
			<div class="eap-typewriter-line"><span><?php echo esc_html( $settings['prefix_text'] ); ?> </span><span class="eap-typewriter-target"></span><span class="eap-typewriter-caret" aria-hidden="true"></span></div>
		</div>
		<?php
	}
}
