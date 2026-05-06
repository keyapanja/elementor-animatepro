<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

class EAP_Widget_Brand_Marquee extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-brand-marquee';
	}

	public function get_title() {
		return __( 'Brand Marquee', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-logo';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Brands', 'elementor-animatepro' ) ) );
		$repeater = new Repeater();
		$repeater->add_control( 'name', array( 'label' => __( 'Name', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Brand', 'elementor-animatepro' ) ) );
		$repeater->add_control( 'logo', array( 'label' => __( 'Logo', 'elementor-animatepro' ), 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => \Elementor\Utils::get_placeholder_image_src() ) ) );
		$repeater->add_control( 'link', array( 'label' => __( 'Link', 'elementor-animatepro' ), 'type' => Controls_Manager::URL, 'show_external' => true ) );
		$this->add_control(
			'brands',
			array(
				'label'       => __( 'Brand Items', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => array( array( 'name' => 'Studio One' ), array( 'name' => 'Halo Labs' ), array( 'name' => 'Northstar' ), array( 'name' => 'Orbit' ) ),
			)
		);
		$this->add_control( 'speed', array( 'label' => __( 'Speed (seconds)', 'elementor-animatepro' ), 'type' => Controls_Manager::NUMBER, 'default' => 18 ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$brands   = array_merge( $settings['brands'], $settings['brands'] );
		?>
		<div class="eap-widget eap-brand-marquee" style="--eap-marquee-duration: <?php echo esc_attr( max( 6, absint( $settings['speed'] ) ) ); ?>s;">
			<div class="eap-brand-track">
				<?php foreach ( $brands as $brand ) : ?>
					<div class="eap-brand-item">
						<?php if ( ! empty( $brand['link']['url'] ) ) : ?><a href="<?php echo esc_url( $brand['link']['url'] ); ?>"<?php echo ! empty( $brand['link']['is_external'] ) ? ' target="_blank"' : ''; ?><?php echo ! empty( $brand['link']['nofollow'] ) ? ' rel="nofollow"' : ''; ?>><?php endif; ?>
						<?php if ( ! empty( $brand['logo']['url'] ) ) : ?><img src="<?php echo esc_url( $brand['logo']['url'] ); ?>" alt="<?php echo esc_attr( $brand['name'] ); ?>" /><?php else : ?><span><?php echo esc_html( $brand['name'] ); ?></span><?php endif; ?>
						<?php if ( ! empty( $brand['link']['url'] ) ) : ?></a><?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
