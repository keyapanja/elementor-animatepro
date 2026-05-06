<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class EAP_Widget_Curve_Swipe extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-curve-swipe';
	}

	public function get_title() {
		return __( 'Curve Swipe Divider', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-divider-shape';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Layout', 'elementor-animatepro' ) ) );
		$this->add_control( 'height', array( 'label' => __( 'Height (px)', 'elementor-animatepro' ), 'type' => Controls_Manager::NUMBER, 'default' => 280 ) );
		$this->add_control( 'direction', array( 'label' => __( 'Swipe Direction', 'elementor-animatepro' ), 'type' => Controls_Manager::SELECT, 'default' => 'up', 'options' => array( 'up' => __( 'Rise Up', 'elementor-animatepro' ), 'down' => __( 'Drop Down', 'elementor-animatepro' ) ) ) );
		$this->end_controls_section();
		$this->start_controls_section( 'style', array( 'label' => __( 'Style', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'fill', array( 'label' => __( 'Fill Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#0f172a' ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="eap-widget eap-curve-swipe" data-eap-curve="<?php echo esc_attr( $settings['direction'] ); ?>" style="height: <?php echo esc_attr( absint( $settings['height'] ) ); ?>px;">
			<svg viewBox="0 0 1440 320" preserveAspectRatio="none" aria-hidden="true">
				<path fill="<?php echo esc_attr( $settings['fill'] ); ?>" d="M0,224L80,224C160,224,320,224,480,197.3C640,171,800,117,960,90.7C1120,64,1280,64,1360,64L1440,64L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
			</svg>
		</div>
		<?php
	}
}
