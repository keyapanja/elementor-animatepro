<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class EAP_Widget_Text_Mask extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-text-mask';
	}

	public function get_title() {
		return __( 'Text Mask Reveal', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-text-area';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'elementor-animatepro' ) ) );
		$this->add_control( 'text', array( 'label' => __( 'Text', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXTAREA, 'default' => __( 'Directional text masking with clean editor controls.', 'elementor-animatepro' ) ) );
		$this->add_control( 'tag', array( 'label' => __( 'HTML Tag', 'elementor-animatepro' ), 'type' => Controls_Manager::SELECT, 'default' => 'h2', 'options' => array( 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'div' => 'DIV', 'p' => 'P', 'span' => 'SPAN' ) ) );
		$this->add_control( 'direction', array( 'label' => __( 'Mask Direction', 'elementor-animatepro' ), 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => array( 'left' => __( 'Left to Right', 'elementor-animatepro' ), 'right' => __( 'Right to Left', 'elementor-animatepro' ), 'top' => __( 'Top to Bottom', 'elementor-animatepro' ), 'bottom' => __( 'Bottom to Top', 'elementor-animatepro' ) ) ) );
		$this->add_control( 'trigger', array( 'label' => __( 'Trigger', 'elementor-animatepro' ), 'type' => Controls_Manager::SELECT, 'default' => 'scroll', 'options' => array( 'scroll' => __( 'On Scroll', 'elementor-animatepro' ), 'load' => __( 'On Load', 'elementor-animatepro' ) ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'style', array( 'label' => __( 'Style', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typography', 'selector' => '{{WRAPPER}} .eap-text-mask-text' ) );
		$this->add_control( 'color', array( 'label' => __( 'Text Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#0f172a', 'selectors' => array( '{{WRAPPER}} .eap-text-mask-text' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'mask_color', array( 'label' => __( 'Mask Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#ff6b2c', 'selectors' => array( '{{WRAPPER}} .eap-text-mask-reveal' => '--eap-mask-color: {{VALUE}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$tag      = \Elementor\Utils::validate_html_tag( $settings['tag'] );
		?>
		<div class="eap-widget eap-text-mask-reveal eap-mask-<?php echo esc_attr( $settings['direction'] ); ?>" data-eap-mask-trigger="<?php echo esc_attr( $settings['trigger'] ); ?>">
			<<?php echo esc_html( $tag ); ?> class="eap-text-mask-text"><?php echo esc_html( $settings['text'] ); ?></<?php echo esc_html( $tag ); ?>>
		</div>
		<?php
	}
}
