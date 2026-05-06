<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

class EAP_Widget_Horizontal_Gallery extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-horizontal-gallery';
	}

	public function get_title() {
		return __( 'Horizontal Gallery', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-gallery-justified';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Gallery', 'elementor-animatepro' ) ) );
		$repeater = new Repeater();
		$repeater->add_control( 'image', array( 'label' => __( 'Image', 'elementor-animatepro' ), 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => \Elementor\Utils::get_placeholder_image_src() ) ) );
		$repeater->add_control( 'caption', array( 'label' => __( 'Caption', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT ) );
		$this->add_control(
			'items',
			array(
				'label'       => __( 'Images', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ caption }}}',
				'default'     => array(
					array( 'caption' => __( 'Frame 1', 'elementor-animatepro' ) ),
					array( 'caption' => __( 'Frame 2', 'elementor-animatepro' ) ),
					array( 'caption' => __( 'Frame 3', 'elementor-animatepro' ) ),
				),
			)
		);
		$this->add_control( 'height', array( 'label' => __( 'Section Height (vh)', 'elementor-animatepro' ), 'type' => Controls_Manager::NUMBER, 'default' => 240 ) );
		$this->add_control( 'panel_height', array( 'label' => __( 'Panel Height (px)', 'elementor-animatepro' ), 'type' => Controls_Manager::NUMBER, 'default' => 420 ) );
		$this->add_control( 'start_offset', array( 'label' => __( 'Start Offset (px)', 'elementor-animatepro' ), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'description' => __( 'Leave at 0 to begin from the first gallery item.', 'elementor-animatepro' ) ) );
		$this->add_control( 'end_offset', array( 'label' => __( 'End Offset (px)', 'elementor-animatepro' ), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'description' => __( 'Leave at 0 to scroll fully to the last gallery item.', 'elementor-animatepro' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="eap-widget eap-horizontal-stage" data-eap-horizontal="gallery" data-eap-horizontal-height="<?php echo esc_attr( absint( $settings['height'] ) ); ?>" data-eap-horizontal-start="<?php echo esc_attr( absint( $settings['start_offset'] ) ); ?>" data-eap-horizontal-end="<?php echo esc_attr( absint( $settings['end_offset'] ) ); ?>">
			<div class="eap-horizontal-sticky">
				<div class="eap-horizontal-track eap-horizontal-gallery-track" style="--eap-gallery-height: <?php echo esc_attr( absint( $settings['panel_height'] ) ); ?>px;">
					<?php foreach ( $settings['items'] as $item ) : ?>
						<figure class="eap-horizontal-gallery-item">
							<img src="<?php echo esc_url( $item['image']['url'] ); ?>" alt="<?php echo esc_attr( $item['caption'] ); ?>" />
							<?php if ( ! empty( $item['caption'] ) ) : ?><figcaption><?php echo esc_html( $item['caption'] ); ?></figcaption><?php endif; ?>
						</figure>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
