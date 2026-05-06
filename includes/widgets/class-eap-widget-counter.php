<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class EAP_Widget_Counter extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-counter';
	}

	public function get_title() {
		return __( 'Counter', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-counter-circle';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'elementor-animatepro' ) ) );
		$this->add_control( 'number', array( 'label' => __( 'End Number', 'elementor-animatepro' ), 'type' => Controls_Manager::NUMBER, 'default' => 245 ) );
		$this->add_control( 'prefix', array( 'label' => __( 'Prefix', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$this->add_control( 'suffix', array( 'label' => __( 'Suffix', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => '+' ) );
		$this->add_control( 'label', array( 'label' => __( 'Label', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Projects launched', 'elementor-animatepro' ) ) );
		$this->add_control( 'duration', array( 'label' => __( 'Duration (ms)', 'elementor-animatepro' ), 'type' => Controls_Manager::NUMBER, 'default' => 1800 ) );
		$this->add_alignment_control();
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="eap-widget eap-counter-card" data-eap-counter="<?php echo esc_attr( absint( $settings['number'] ) ); ?>" data-eap-duration="<?php echo esc_attr( absint( $settings['duration'] ) ); ?>">
			<div class="eap-counter-number"><span><?php echo esc_html( $settings['prefix'] ); ?></span><span class="eap-counter-value">0</span><span><?php echo esc_html( $settings['suffix'] ); ?></span></div>
			<div class="eap-counter-label"><?php echo esc_html( $settings['label'] ); ?></div>
		</div>
		<?php
	}
}
