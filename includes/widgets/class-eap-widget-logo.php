<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;

class EAP_Widget_Logo extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-logo';
	}

	public function get_title() {
		return __( 'Site Logo', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-site-logo';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Logo', 'elementor-animatepro' ) ) );
		$this->add_control(
			'source',
			array(
				'label'   => __( 'Source', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'site',
				'options' => array(
					'site'   => __( 'Site Logo', 'elementor-animatepro' ),
					'custom' => __( 'Custom Image', 'elementor-animatepro' ),
					'text'   => __( 'Text Only', 'elementor-animatepro' ),
				),
			)
		);
		$this->add_control( 'image', array( 'label' => __( 'Custom Logo', 'elementor-animatepro' ), 'type' => Controls_Manager::MEDIA, 'condition' => array( 'source' => 'custom' ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'logo_image', 'condition' => array( 'source' => 'custom' ) ) );
		$this->add_control( 'text', array( 'label' => __( 'Logo Text', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => get_bloginfo( 'name' ), 'condition' => array( 'source' => 'text' ) ) );
		$this->add_control( 'link_home', array( 'label' => __( 'Link To Homepage', 'elementor-animatepro' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ) );
		$this->add_control( 'animation', array( 'label' => __( 'Entrance Animation', 'elementor-animatepro' ), 'type' => Controls_Manager::SELECT, 'default' => 'fade', 'options' => array( 'none' => __( 'None', 'elementor-animatepro' ), 'fade' => __( 'Fade', 'elementor-animatepro' ), 'slide-left' => __( 'Slide From Left', 'elementor-animatepro' ), 'slide-right' => __( 'Slide From Right', 'elementor-animatepro' ), 'slide-up' => __( 'Slide From Bottom', 'elementor-animatepro' ) ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'style', array( 'label' => __( 'Style', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typography', 'selector' => '{{WRAPPER}} .eap-logo-text' ) );
		$this->add_control( 'text_color', array( 'label' => __( 'Text Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#0f172a', 'selectors' => array( '{{WRAPPER}} .eap-logo-text' => 'color: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'max_width', array( 'label' => __( 'Max Width', 'elementor-animatepro' ), 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', '%' ), 'range' => array( 'px' => array( 'min' => 40, 'max' => 500 ) ), 'selectors' => array( '{{WRAPPER}} .eap-logo img' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$link_tag = 'yes' === $settings['link_home'] ? 'a' : 'div';
		$href     = 'yes' === $settings['link_home'] ? ' href="' . esc_url( home_url( '/' ) ) . '"' : '';
		$classes  = 'eap-logo eap-nav-animate eap-nav-animate-' . $settings['animation'];
		?>
		<<?php echo esc_html( $link_tag ); ?> class="<?php echo esc_attr( $classes ); ?>"<?php echo wp_kses_post( $href ); ?>>
			<?php if ( 'custom' === $settings['source'] && ! empty( $settings['image']['id'] ) ) : ?>
				<?php echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $settings, 'logo_image', 'image' ) ); ?>
			<?php elseif ( 'text' === $settings['source'] ) : ?>
				<span class="eap-logo-text"><?php echo esc_html( $settings['text'] ); ?></span>
			<?php elseif ( has_custom_logo() ) : ?>
				<?php echo wp_kses_post( get_custom_logo() ); ?>
			<?php else : ?>
				<span class="eap-logo-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
			<?php endif; ?>
		</<?php echo esc_html( $link_tag ); ?>>
		<?php
	}
}
