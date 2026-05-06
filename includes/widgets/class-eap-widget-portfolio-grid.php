<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

class EAP_Widget_Portfolio_Grid extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-portfolio-grid';
	}

	public function get_title() {
		return __( 'Portfolio Grid', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Items', 'elementor-animatepro' ) ) );
		$repeater = new Repeater();
		$repeater->add_control( 'title', array( 'label' => __( 'Title', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Project', 'elementor-animatepro' ) ) );
		$repeater->add_control( 'category', array( 'label' => __( 'Category', 'elementor-animatepro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Branding', 'elementor-animatepro' ) ) );
		$repeater->add_control( 'image', array( 'label' => __( 'Image', 'elementor-animatepro' ), 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => \Elementor\Utils::get_placeholder_image_src() ) ) );
		$repeater->add_control( 'link', array( 'label' => __( 'Link', 'elementor-animatepro' ), 'type' => Controls_Manager::URL, 'show_external' => true ) );
		$this->add_control(
			'items',
			array(
				'label'       => __( 'Portfolio Items', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array( 'title' => __( 'Northstar', 'elementor-animatepro' ), 'category' => __( 'Branding', 'elementor-animatepro' ) ),
					array( 'title' => __( 'Orbit', 'elementor-animatepro' ), 'category' => __( 'Motion', 'elementor-animatepro' ) ),
					array( 'title' => __( 'Canvas', 'elementor-animatepro' ), 'category' => __( 'UI', 'elementor-animatepro' ) ),
				),
			)
		);
		$this->add_control( 'show_filters', array( 'label' => __( 'Show Filters', 'elementor-animatepro' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ) );
		$this->add_responsive_control( 'columns', array( 'label' => __( 'Columns', 'elementor-animatepro' ), 'type' => Controls_Manager::SELECT, 'default' => '3', 'tablet_default' => '2', 'mobile_default' => '1', 'options' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ), 'selectors' => array( '{{WRAPPER}} .eap-portfolio-items' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ) ) );
		$this->add_control( 'hover_effect', array( 'label' => __( 'Image Hover Effect', 'elementor-animatepro' ), 'type' => Controls_Manager::SELECT, 'default' => 'zoom-in', 'options' => array( 'none' => __( 'None', 'elementor-animatepro' ), 'zoom-in' => __( 'Zoom In', 'elementor-animatepro' ), 'zoom-out' => __( 'Zoom Out', 'elementor-animatepro' ), 'fade' => __( 'Fade', 'elementor-animatepro' ), 'bw-color' => __( 'Black & White to Color', 'elementor-animatepro' ) ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'style_card', array( 'label' => __( 'Card', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Background::get_type(), array( 'name' => 'card_background', 'selector' => '{{WRAPPER}} .eap-portfolio-item' ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'card_border', 'selector' => '{{WRAPPER}} .eap-portfolio-item' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'style_category', array( 'label' => __( 'Category', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'category_typography', 'selector' => '{{WRAPPER}} .eap-portfolio-copy span' ) );
		$this->add_control( 'category_color', array( 'label' => __( 'Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#64748b', 'selectors' => array( '{{WRAPPER}} .eap-portfolio-copy span' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'style_title', array( 'label' => __( 'Title', 'elementor-animatepro' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .eap-portfolio-copy h3' ) );
		$this->add_control( 'title_color', array( 'label' => __( 'Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#0f172a', 'selectors' => array( '{{WRAPPER}} .eap-portfolio-copy h3' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'title_hover_color', array( 'label' => __( 'Hover Color', 'elementor-animatepro' ), 'type' => Controls_Manager::COLOR, 'default' => '#ff6b2c', 'selectors' => array( '{{WRAPPER}} .eap-portfolio-item:hover .eap-portfolio-copy h3' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$categories = array();
		foreach ( $settings['items'] as $item ) {
			if ( ! empty( $item['category'] ) ) {
				$categories[ sanitize_title( $item['category'] ) ] = $item['category'];
			}
		}
		?>
		<div class="eap-widget eap-portfolio-grid" data-eap-portfolio-hover="<?php echo esc_attr( $settings['hover_effect'] ); ?>">
			<?php if ( 'yes' === $settings['show_filters'] && ! empty( $categories ) ) : ?>
				<div class="eap-portfolio-filters"><button class="is-active" type="button" data-filter="all"><?php echo esc_html__( 'All', 'elementor-animatepro' ); ?></button><?php foreach ( $categories as $slug => $label ) : ?><button type="button" data-filter="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></button><?php endforeach; ?></div>
			<?php endif; ?>
			<div class="eap-portfolio-items">
				<?php foreach ( $settings['items'] as $item ) : ?>
					<?php $slug = ! empty( $item['category'] ) ? sanitize_title( $item['category'] ) : 'uncategorized'; ?>
					<article class="eap-portfolio-item eap-portfolio-hover-<?php echo esc_attr( $settings['hover_effect'] ); ?>" data-category="<?php echo esc_attr( $slug ); ?>">
						<?php if ( ! empty( $item['link']['url'] ) ) : ?><a href="<?php echo esc_url( $item['link']['url'] ); ?>"<?php echo ! empty( $item['link']['is_external'] ) ? ' target="_blank"' : ''; ?><?php echo ! empty( $item['link']['nofollow'] ) ? ' rel="nofollow"' : ''; ?>><?php endif; ?>
						<div class="eap-portfolio-media"><?php if ( ! empty( $item['image']['url'] ) ) : ?><img src="<?php echo esc_url( $item['image']['url'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" /><?php endif; ?></div>
						<div class="eap-portfolio-copy"><span><?php echo esc_html( $item['category'] ); ?></span><h3><?php echo esc_html( $item['title'] ); ?></h3></div>
						<?php if ( ! empty( $item['link']['url'] ) ) : ?></a><?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
