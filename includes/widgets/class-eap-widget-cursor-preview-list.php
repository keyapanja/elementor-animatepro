<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

class EAP_Widget_Cursor_Preview_List extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-cursor-preview-list';
	}

	public function get_title() {
		return __( 'Cursor Preview List', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Items', 'elementor-animatepro' ) ) );
		$repeater = new Repeater();
		$repeater->add_control( 'title', array( 'label' => __( 'Title', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'List item', 'elementor-animatepro' ) ) );
		$repeater->add_control( 'meta', array( 'label' => __( 'Meta Text', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Hover preview', 'elementor-animatepro' ) ) );
		$repeater->add_control( 'image', array( 'label' => __( 'Preview Image', 'elementor-animatepro' ), 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => \Elementor\Utils::get_placeholder_image_src() ) ) );
		$repeater->add_control( 'link', array( 'label' => __( 'Link', 'elementor-animatepro' ), 'type' => Controls_Manager::URL, 'show_external' => true ) );
		$this->add_control(
			'items',
			array(
				'label'       => __( 'List Items', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array( 'title' => __( 'Northstar', 'elementor-animatepro' ), 'meta' => __( 'Brand identity', 'elementor-animatepro' ) ),
					array( 'title' => __( 'Orbit', 'elementor-animatepro' ), 'meta' => __( 'Product motion', 'elementor-animatepro' ) ),
				),
			)
		);
		$this->add_control( 'preview_size', array( 'label' => __( 'Preview Size (px)', 'elementor-animatepro' ), 'type' => Controls_Manager::NUMBER, 'default' => 220 ) );
		$this->end_controls_section();

		$this->start_controls_section( 'style_title', array( 'label' => __( 'Title', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .eap-preview-item__title' ) );
		$this->add_control( 'title_color', array( 'label' => __( 'Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#0f172a', 'selectors' => array( '{{WRAPPER}} .eap-preview-item__title' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'title_hover_color', array( 'label' => __( 'Hover Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#ff6b2c', 'selectors' => array( '{{WRAPPER}} .eap-preview-item:hover .eap-preview-item__title' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'style_meta', array( 'label' => __( 'Meta Text', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'meta_typography', 'selector' => '{{WRAPPER}} .eap-preview-item__meta' ) );
		$this->add_control( 'meta_color', array( 'label' => __( 'Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#64748b', 'selectors' => array( '{{WRAPPER}} .eap-preview-item__meta' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'meta_hover_color', array( 'label' => __( 'Hover Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#0f172a', 'selectors' => array( '{{WRAPPER}} .eap-preview-item:hover .eap-preview-item__meta' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'style_item', array( 'label' => __( 'Item Card', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Background::get_type(), array( 'name' => 'item_background', 'selector' => '{{WRAPPER}} .eap-preview-item' ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'item_border', 'selector' => '{{WRAPPER}} .eap-preview-item' ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="eap-widget eap-cursor-preview-list" data-eap-preview-size="<?php echo esc_attr( absint( $settings['preview_size'] ) ); ?>">
			<?php foreach ( $settings['items'] as $item ) : ?>
				<div class="eap-preview-item" data-eap-preview-image="<?php echo esc_url( $item['image']['url'] ); ?>">
					<?php if ( ! empty( $item['link']['url'] ) ) : ?><a href="<?php echo esc_url( $item['link']['url'] ); ?>"<?php echo ! empty( $item['link']['is_external'] ) ? ' target="_blank"' : ''; ?><?php echo ! empty( $item['link']['nofollow'] ) ? ' rel="nofollow"' : ''; ?>><?php endif; ?>
					<div class="eap-preview-item__title"><?php echo esc_html( $item['title'] ); ?></div>
					<?php if ( ! empty( $item['meta'] ) ) : ?><div class="eap-preview-item__meta"><?php echo esc_html( $item['meta'] ); ?></div><?php endif; ?>
					<?php if ( ! empty( $item['link']['url'] ) ) : ?></a><?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
