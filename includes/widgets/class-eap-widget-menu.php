<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class EAP_Widget_Menu extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-menu';
	}

	public function get_title() {
		return __( 'Navigation Menu', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	protected function register_controls() {
		$menus   = wp_get_nav_menus();
		$options = array();
		foreach ( $menus as $menu ) {
			$options[ $menu->term_id ] = $menu->name;
		}

		$this->start_controls_section( 'content', array( 'label' => __( 'Menu', 'elementor-animatepro' ) ) );
		$this->add_control( 'menu_id', array( 'label' => __( 'Select Menu', 'elementor-animatepro' ), 'type' => Controls_Manager::SELECT, 'options' => $options ) );
		$this->add_control( 'layout', array( 'label' => __( 'Layout', 'elementor-animatepro' ), 'type' => Controls_Manager::SELECT, 'default' => 'horizontal', 'options' => array( 'horizontal' => __( 'Horizontal', 'elementor-animatepro' ), 'vertical' => __( 'Vertical', 'elementor-animatepro' ) ) ) );
		$this->add_control( 'animation', array( 'label' => __( 'Item Animation', 'elementor-animatepro' ), 'type' => Controls_Manager::SELECT, 'default' => 'fade', 'options' => array( 'none' => __( 'None', 'elementor-animatepro' ), 'fade' => __( 'Fade', 'elementor-animatepro' ), 'slide-left' => __( 'Slide Left', 'elementor-animatepro' ), 'slide-right' => __( 'Slide Right', 'elementor-animatepro' ), 'slide-up' => __( 'Slide Up', 'elementor-animatepro' ) ) ) );
		$this->add_control( 'stagger', array( 'label' => __( 'Stagger (ms)', 'elementor-animatepro' ), 'type' => Controls_Manager::NUMBER, 'default' => 80 ) );
		$this->end_controls_section();

		$this->start_controls_section( 'style', array( 'label' => __( 'Style', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typography', 'selector' => '{{WRAPPER}} .eap-menu a' ) );
		$this->add_control( 'text_color', array( 'label' => __( 'Text Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#0f172a', 'selectors' => array( '{{WRAPPER}} .eap-menu a' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'gap', array( 'label' => __( 'Gap', 'elementor-animatepro' ), 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'range' => array( 'px' => array( 'min' => 0, 'max' => 60 ) ), 'selectors' => array( '{{WRAPPER}} .eap-menu' => 'gap: {{SIZE}}px;' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$menu_id  = ! empty( $settings['menu_id'] ) ? absint( $settings['menu_id'] ) : 0;
		if ( ! $menu_id ) {
			return;
		}

		$items = wp_get_nav_menu_items( $menu_id );
		if ( empty( $items ) ) {
			return;
		}
		?>
		<nav class="eap-menu eap-menu-<?php echo esc_attr( $settings['layout'] ); ?>" aria-label="<?php esc_attr_e( 'Navigation Menu', 'elementor-animatepro' ); ?>">
			<?php foreach ( $items as $index => $item ) : ?>
				<a class="eap-nav-animate eap-nav-animate-<?php echo esc_attr( $settings['animation'] ); ?>" href="<?php echo esc_url( $item->url ); ?>" style="transition-delay: <?php echo esc_attr( absint( $settings['stagger'] ) * $index ); ?>ms;">
					<?php echo esc_html( $item->title ); ?>
				</a>
			<?php endforeach; ?>
		</nav>
		<?php
	}
}
