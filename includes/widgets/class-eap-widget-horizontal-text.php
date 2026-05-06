<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class EAP_Widget_Horizontal_Text extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-horizontal-text';
	}

	public function get_title() {
		return __( 'Horizontal Text Scroll', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-h-align-stretch';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'elementor-animatepro' ) ) );
		$this->add_control( 'text', array( 'label' => __( 'Long Text', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXTAREA, 'default' => __( 'This long paragraph keeps moving horizontally while the section stays in view, letting you create a storytelling track without custom code.', 'elementor-animatepro' ) ) );
		$this->add_control( 'height', array( 'label' => __( 'Section Height (vh)', 'elementor-animatepro' ), 'type' => Controls_Manager::NUMBER, 'default' => 220 ) );
		$this->add_control( 'start_offset', array( 'label' => __( 'Start Offset (px)', 'elementor-animatepro' ), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'description' => __( 'Leave at 0 to start from the true beginning of the text.', 'elementor-animatepro' ) ) );
		$this->add_control( 'end_offset', array( 'label' => __( 'End Offset (px)', 'elementor-animatepro' ), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'description' => __( 'Leave at 0 to scroll fully to the last part of the text.', 'elementor-animatepro' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'style', array( 'label' => __( 'Style', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typography', 'selector' => '{{WRAPPER}} .eap-horizontal-text-track' ) );
		$this->add_control( 'color', array( 'label' => __( 'Text Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#0f172a', 'selectors' => array( '{{WRAPPER}} .eap-horizontal-text-track' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="eap-widget eap-horizontal-stage" data-eap-horizontal="text" data-eap-horizontal-height="<?php echo esc_attr( absint( $settings['height'] ) ); ?>" data-eap-horizontal-start="<?php echo esc_attr( absint( $settings['start_offset'] ) ); ?>" data-eap-horizontal-end="<?php echo esc_attr( absint( $settings['end_offset'] ) ); ?>">
			<div class="eap-horizontal-sticky">
				<div class="eap-horizontal-track eap-horizontal-text-track"><?php echo esc_html( $settings['text'] ); ?></div>
			</div>
		</div>
		<?php
	}
}
