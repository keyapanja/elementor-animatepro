<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

class EAP_Widget_Pricing_Table extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-pricing-table';
	}

	public function get_title() {
		return __( 'Pricing Table', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Plan', 'elementor-animatepro' ) ) );
		$this->add_control( 'badge', array( 'label' => __( 'Badge', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Popular', 'elementor-animatepro' ) ) );
		$this->add_control( 'title', array( 'label' => __( 'Title', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Studio', 'elementor-animatepro' ) ) );
		$this->add_control( 'price', array( 'label' => __( 'Price', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => '$49' ) );
		$this->add_control( 'period', array( 'label' => __( 'Period', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( '/month', 'elementor-animatepro' ) ) );

		$repeater = new Repeater();
		$repeater->add_control( 'feature', array( 'label' => __( 'Feature', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'One-click module toggles', 'elementor-animatepro' ) ) );
		$this->add_control(
			'features',
			array(
				'label'       => __( 'Features', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'feature' => __( 'One-click module toggles', 'elementor-animatepro' ) ),
					array( 'feature' => __( 'Motion controls for Elementor elements', 'elementor-animatepro' ) ),
					array( 'feature' => __( 'No activation server required', 'elementor-animatepro' ) ),
				),
				'title_field' => '{{{ feature }}}',
			)
		);
		$this->add_control( 'button_text', array( 'label' => __( 'Button Text', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Choose plan', 'elementor-animatepro' ) ) );
		$this->add_control( 'button_link', array( 'label' => __( 'Button Link', 'elementor-animatepro' ), 'type' => Controls_Manager::URL, 'show_external' => true ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="eap-widget eap-pricing-card">
			<?php if ( ! empty( $settings['badge'] ) ) : ?><div class="eap-pricing-badge"><?php echo esc_html( $settings['badge'] ); ?></div><?php endif; ?>
			<h3><?php echo esc_html( $settings['title'] ); ?></h3>
			<div class="eap-pricing-price"><?php echo esc_html( $settings['price'] ); ?><span><?php echo esc_html( $settings['period'] ); ?></span></div>
			<ul class="eap-pricing-features"><?php foreach ( $settings['features'] as $item ) : ?><li><?php echo esc_html( $item['feature'] ); ?></li><?php endforeach; ?></ul>
			<?php if ( ! empty( $settings['button_link']['url'] ) ) : ?>
				<a class="eap-button" href="<?php echo esc_url( $settings['button_link']['url'] ); ?>"<?php echo ! empty( $settings['button_link']['is_external'] ) ? ' target="_blank"' : ''; ?><?php echo ! empty( $settings['button_link']['nofollow'] ) ? ' rel="nofollow"' : ''; ?>><?php echo esc_html( $settings['button_text'] ); ?></a>
			<?php else : ?>
				<span class="eap-button"><?php echo esc_html( $settings['button_text'] ); ?></span>
			<?php endif; ?>
		</div>
		<?php
	}
}
