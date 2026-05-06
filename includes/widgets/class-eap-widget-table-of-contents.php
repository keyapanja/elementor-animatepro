<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class EAP_Widget_Table_Of_Contents extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-table-of-contents';
	}

	public function get_title() {
		return __( 'Table of Contents', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Settings', 'elementor-animatepro' ) ) );
		$this->add_control( 'selector', array( 'label' => __( 'Heading Selector', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => 'h2, h3', 'description' => __( 'Headings will be collected from the current page content.', 'elementor-animatepro' ) ) );
		$this->add_control( 'title', array( 'label' => __( 'Title', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'On this page', 'elementor-animatepro' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<nav class="eap-widget eap-toc" data-eap-toc="<?php echo esc_attr( $settings['selector'] ); ?>">
			<div class="eap-toc-title"><?php echo esc_html( $settings['title'] ); ?></div>
			<ul class="eap-toc-list"></ul>
		</nav>
		<?php
	}
}
