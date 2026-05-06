<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

class EAP_Widget_Offcanvas extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-offcanvas';
	}

	public function get_title() {
		return __( 'Offcanvas Panel', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-menu-toggle';
	}

	protected function register_controls() {
		$menus     = wp_get_nav_menus();
		$menu_opts = array();
		foreach ( $menus as $menu ) {
			$menu_opts[ $menu->term_id ] = $menu->name;
		}

		$template_opts = $this->get_template_options();

		$this->start_controls_section( 'content', array( 'label' => __( 'Panel', 'elementor-animatepro' ) ) );
		$this->add_control( 'button_text', array( 'label' => __( 'Toggle Text', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Menu', 'elementor-animatepro' ) ) );
		$this->add_control( 'animation', array( 'label' => __( 'Panel Direction', 'elementor-animatepro' ), 'type' => Controls_Manager::SELECT, 'default' => 'right', 'options' => array( 'left' => __( 'Slide From Left', 'elementor-animatepro' ), 'right' => __( 'Slide From Right', 'elementor-animatepro' ), 'top' => __( 'Slide From Top', 'elementor-animatepro' ), 'bottom' => __( 'Slide From Bottom', 'elementor-animatepro' ), 'fade' => __( 'Fade', 'elementor-animatepro' ) ) ) );
		$this->add_control( 'content_source', array( 'label' => __( 'Panel Content Source', 'elementor-animatepro' ), 'type' => Controls_Manager::SELECT, 'default' => 'menu', 'options' => array( 'menu' => __( 'Menu + Text', 'elementor-animatepro' ), 'template' => __( 'Saved Template', 'elementor-animatepro' ) ) ) );
		$this->add_control( 'menu_id', array( 'label' => __( 'Menu', 'elementor-animatepro' ), 'type' => Controls_Manager::SELECT, 'options' => $menu_opts, 'condition' => array( 'content_source' => 'menu' ) ) );
		$this->add_control( 'panel_title', array( 'label' => __( 'Panel Title', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Navigate', 'elementor-animatepro' ), 'condition' => array( 'content_source' => 'menu' ) ) );
		$this->add_control( 'panel_text', array( 'label' => __( 'Panel Text', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXTAREA, 'default' => __( 'Add your menu and supporting copy for a modern offcanvas interaction.', 'elementor-animatepro' ), 'condition' => array( 'content_source' => 'menu' ) ) );
		$this->add_control( 'template_id', array( 'label' => __( 'Saved Template', 'elementor-animatepro' ), 'type' => Controls_Manager::SELECT, 'options' => $template_opts, 'condition' => array( 'content_source' => 'template' ), 'description' => __( 'Select an Elementor template to render inside the panel.', 'elementor-animatepro' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'style_button', array( 'label' => __( 'Toggle Button', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'button_typography', 'selector' => '{{WRAPPER}} .eap-offcanvas-toggle' ) );
		$this->add_control( 'button_color', array( 'label' => __( 'Text Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => array( '{{WRAPPER}} .eap-offcanvas-toggle' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Background::get_type(), array( 'name' => 'button_background', 'selector' => '{{WRAPPER}} .eap-offcanvas-toggle' ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'button_border', 'selector' => '{{WRAPPER}} .eap-offcanvas-toggle' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'style_panel', array( 'label' => __( 'Panel', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Background::get_type(), array( 'name' => 'panel_background', 'selector' => '{{WRAPPER}} .eap-offcanvas-panel' ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'panel_border', 'selector' => '{{WRAPPER}} .eap-offcanvas-panel' ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .eap-offcanvas-panel h3' ) );
		$this->add_control( 'title_color', array( 'label' => __( 'Title Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#0f172a', 'selectors' => array( '{{WRAPPER}} .eap-offcanvas-panel h3' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'text_typography', 'selector' => '{{WRAPPER}} .eap-offcanvas-panel p' ) );
		$this->add_control( 'text_color', array( 'label' => __( 'Paragraph Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#64748b', 'selectors' => array( '{{WRAPPER}} .eap-offcanvas-panel p' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'link_typography', 'selector' => '{{WRAPPER}} .eap-offcanvas-menu a' ) );
		$this->add_control( 'link_color', array( 'label' => __( 'Menu Link Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#0f172a', 'selectors' => array( '{{WRAPPER}} .eap-offcanvas-menu a' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$id       = 'eap-offcanvas-' . $this->get_id();
		$menu_id  = ! empty( $settings['menu_id'] ) ? absint( $settings['menu_id'] ) : 0;
		$items    = $menu_id ? wp_get_nav_menu_items( $menu_id ) : array();
		?>
		<div class="eap-offcanvas" data-eap-offcanvas="<?php echo esc_attr( $settings['animation'] ); ?>">
			<button class="eap-offcanvas-toggle" type="button" data-eap-offcanvas-toggle="<?php echo esc_attr( $id ); ?>" aria-expanded="false" aria-controls="<?php echo esc_attr( $id ); ?>">
				<?php echo esc_html( $settings['button_text'] ); ?>
			</button>
			<div class="eap-offcanvas-overlay" data-eap-offcanvas-close="<?php echo esc_attr( $id ); ?>"></div>
			<aside id="<?php echo esc_attr( $id ); ?>" class="eap-offcanvas-panel eap-offcanvas-panel-<?php echo esc_attr( $settings['animation'] ); ?>" aria-hidden="true">
				<button class="eap-offcanvas-close" type="button" data-eap-offcanvas-close="<?php echo esc_attr( $id ); ?>" aria-label="<?php esc_attr_e( 'Close panel', 'elementor-animatepro' ); ?>">&times;</button>
				<?php if ( 'template' === $settings['content_source'] && ! empty( $settings['template_id'] ) ) : ?>
					<div class="eap-offcanvas-template"><?php echo $this->render_template_content( absint( $settings['template_id'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php else : ?>
					<?php if ( ! empty( $settings['panel_title'] ) ) : ?><h3><?php echo esc_html( $settings['panel_title'] ); ?></h3><?php endif; ?>
					<?php if ( ! empty( $settings['panel_text'] ) ) : ?><p><?php echo esc_html( $settings['panel_text'] ); ?></p><?php endif; ?>
					<?php if ( ! empty( $items ) ) : ?>
						<nav class="eap-offcanvas-menu">
							<?php foreach ( $items as $item ) : ?>
								<a href="<?php echo esc_url( $item->url ); ?>"><?php echo esc_html( $item->title ); ?></a>
							<?php endforeach; ?>
						</nav>
					<?php endif; ?>
				<?php endif; ?>
			</aside>
		</div>
		<?php
	}

	/**
	 * Get template options.
	 *
	 * @return array
	 */
	private function get_template_options() {
		$options = array();
		$posts   = get_posts(
			array(
				'post_type'      => array( 'elementor_library', 'eap_template' ),
				'post_status'    => 'publish',
				'posts_per_page' => 100,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		foreach ( $posts as $post ) {
			$options[ $post->ID ] = $post->post_title ? $post->post_title : '#' . $post->ID;
		}

		return $options;
	}

	/**
	 * Render saved template content.
	 *
	 * @param int $template_id Template ID.
	 * @return string
	 */
	private function render_template_content( $template_id ) {
		if ( class_exists( '\Elementor\Plugin' ) && did_action( 'elementor/loaded' ) ) {
			return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id, true );
		}

		return apply_filters( 'the_content', get_post_field( 'post_content', $template_id ) );
	}
}
