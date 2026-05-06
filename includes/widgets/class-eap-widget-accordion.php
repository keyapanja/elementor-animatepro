<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

class EAP_Widget_Accordion extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-accordion';
	}

	public function get_title() {
		return __( 'Advanced Accordion', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-accordion';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Items', 'elementor-animatepro' ) ) );
		$repeater = new Repeater();
		$repeater->add_control( 'title', array( 'label' => __( 'Title', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Accordion Title', 'elementor-animatepro' ) ) );
		$repeater->add_control( 'content', array( 'label' => __( 'Content', 'elementor-animatepro' ), 'type' => Controls_Manager::WYSIWYG, 'default' => __( 'Accordion content goes here.', 'elementor-animatepro' ) ) );
		$this->add_control(
			'items',
			array(
				'label'       => __( 'Accordion Items', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array( 'title' => __( 'How are assets loaded?', 'elementor-animatepro' ), 'content' => __( 'Each widget depends on shared assets only when it renders, which keeps pages lighter.', 'elementor-animatepro' ) ),
					array( 'title' => __( 'Can modules be disabled?', 'elementor-animatepro' ), 'content' => __( 'Yes. The settings page lets you turn widgets and extensions on or off individually.', 'elementor-animatepro' ) ),
				),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="eap-widget eap-accordion">
			<?php foreach ( $settings['items'] as $index => $item ) : ?>
				<div class="eap-accordion-item<?php echo 0 === $index ? ' is-open' : ''; ?>">
					<button class="eap-accordion-trigger" type="button" aria-expanded="<?php echo 0 === $index ? 'true' : 'false'; ?>"><span><?php echo esc_html( $item['title'] ); ?></span></button>
					<div class="eap-accordion-panel"><div class="eap-accordion-panel-inner"><?php echo wp_kses_post( $item['content'] ); ?></div></div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
