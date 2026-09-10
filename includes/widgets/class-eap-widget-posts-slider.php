<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

/**
 * Posts Slider — the Posts loop as a Swiper carousel.
 *
 * Built on the shared EAP_Posts_Query engine: the query and the card markup come
 * from `build_query_args()` + `render_card()`, and posts.css supplies the card /
 * overlay styling — so slides are identical to the Posts widget's cards (the
 * Overlay layout comes free from the `eap-posts--overlay` wrapper modifier).
 * posts-slider.css only adds the slide sizing, arrows and pagination.
 *
 * Follows the plugin's slider convention: Elementor's bundled Swiper is added to
 * get_script_depends() only when registered, the markup is
 * `.eap-posts-slider__swiper.swiper > .swiper-wrapper > .swiper-slide`, and the
 * config travels as JSON on `data-eap-posts-slider` for posts-slider.js.
 */
class EAP_Widget_Posts_Slider extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-posts-slider';
	}

	public function get_title() {
		return __( 'Posts Slider', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-post-slider';
	}

	public function get_keywords() {
		return array( 'posts', 'slider', 'carousel', 'swiper', 'blog', 'dynamic' );
	}

	public function get_style_depends() {
		return array( 'eap-core', 'eap-posts', 'eap-posts-slider' );
	}

	public function get_script_depends() {
		$deps = array(
			'eap-core-runtime',
			'eap-posts-slider-script',
		);
		if ( wp_script_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}

		return array_unique( $deps );
	}

	protected function register_controls() {
		$this->register_query_section();
		$this->register_layout_section();
		$this->register_slider_section();

		$this->register_card_style();
		$this->register_image_style();
		$this->register_title_style();
		$this->register_meta_style();
		$this->register_excerpt_style();
		$this->register_button_style();
		$this->register_arrows_style();
		$this->register_pagination_style();
	}

	/* =====================================================================
	 * CONTENT — QUERY
	 * ================================================================== */

	protected function register_query_section() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Query', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'post_type',
			array(
				'label'   => __( 'Post Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => EAP_Posts_Query::get_post_type_options(),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => __( 'Number of Posts', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 48,
				'default' => 9,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => __( 'Order By', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'          => __( 'Date', 'elementor-animatepro' ),
					'modified'      => __( 'Last Modified', 'elementor-animatepro' ),
					'title'         => __( 'Title', 'elementor-animatepro' ),
					'menu_order'    => __( 'Menu Order', 'elementor-animatepro' ),
					'comment_count' => __( 'Comment Count', 'elementor-animatepro' ),
					'rand'          => __( 'Random', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => __( 'Order', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => __( 'Descending', 'elementor-animatepro' ),
					'ASC'  => __( 'Ascending', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'   => __( 'Offset', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'default' => 0,
			)
		);

		$this->add_control(
			'include_terms',
			array(
				'label'       => __( 'Include Terms', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => EAP_Posts_Query::get_term_options(),
			)
		);

		$this->add_control(
			'exclude_terms',
			array(
				'label'       => __( 'Exclude Terms', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => EAP_Posts_Query::get_term_options(),
			)
		);

		$this->add_control(
			'exclude_current',
			array(
				'label'        => __( 'Exclude Current Post', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'ignore_sticky',
			array(
				'label'        => __( 'Ignore Sticky Posts', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — LAYOUT / CARD
	 * ================================================================== */

	protected function register_layout_section() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Card Layout', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'grid'    => __( 'Card', 'elementor-animatepro' ),
					'overlay' => __( 'Overlay', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'   => __( 'Image Size', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'medium_large',
				'options' => $this->get_image_size_options(),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'Title Tag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label'   => __( 'Excerpt Length (words)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 100,
				'default' => 18,
			)
		);

		$this->add_control(
			'meta_data',
			array(
				'label'       => __( 'Meta', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'default'     => array( 'date', 'comments' ),
				'options'     => array(
					'author'   => __( 'Author', 'elementor-animatepro' ),
					'date'     => __( 'Date', 'elementor-animatepro' ),
					'comments' => __( 'Comments', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'readmore_text',
			array(
				'label'   => __( 'Read More Text', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Read More', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'elements_heading',
			array(
				'label'     => __( 'Elements', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		foreach ( array(
			'show_image'    => __( 'Image', 'elementor-animatepro' ),
			'show_badge'    => __( 'Category Badge', 'elementor-animatepro' ),
			'show_title'    => __( 'Title', 'elementor-animatepro' ),
			'show_meta'     => __( 'Meta', 'elementor-animatepro' ),
			'show_excerpt'  => __( 'Excerpt', 'elementor-animatepro' ),
			'show_readmore' => __( 'Read More', 'elementor-animatepro' ),
		) as $key => $label ) {
			$this->add_control(
				$key,
				array(
					'label'        => $label,
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'elementor-animatepro' ),
					'label_off'    => __( 'Hide', 'elementor-animatepro' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
		}

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — SLIDER
	 * ================================================================== */

	protected function register_slider_section() {
		$this->start_controls_section(
			'section_slider',
			array(
				'label' => __( 'Slider', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'slides_desktop',
			array(
				'label'   => __( 'Slides (Desktop)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 6,
				'default' => 3,
			)
		);

		$this->add_control(
			'slides_tablet',
			array(
				'label'   => __( 'Slides (Tablet)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 6,
				'default' => 2,
			)
		);

		$this->add_control(
			'slides_mobile',
			array(
				'label'   => __( 'Slides (Mobile)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 4,
				'default' => 1,
			)
		);

		$this->add_control(
			'space_between',
			array(
				'label'      => __( 'Space Between', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => __( 'Transition Speed (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 100,
				'max'     => 3000,
				'step'    => 50,
				'default' => 600,
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => __( 'Loop', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'centered_slides',
			array(
				'label'        => __( 'Centered Slides', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => __( 'Autoplay Delay (s)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 20,
				'step'      => 0.5,
				'default'   => 4,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'pause_on_interaction',
			array(
				'label'        => __( 'Resume After Interaction', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'show_arrows',
			array(
				'label'        => __( 'Arrows', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'show_pagination',
			array(
				'label'        => __( 'Pagination', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'     => __( 'Pagination Type', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bullets',
				'options'   => array(
					'bullets'     => __( 'Bullets', 'elementor-animatepro' ),
					'fraction'    => __( 'Fraction', 'elementor-animatepro' ),
					'progressbar' => __( 'Progress Bar', 'elementor-animatepro' ),
				),
				'condition' => array( 'show_pagination' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE — CARD (targets the reused .eap-posts__* card)
	 * ================================================================== */

	protected function register_card_style() {
		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => __( 'Card', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Body Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20, 'unit' => 'px', 'isLinked' => true ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .eap-posts__item',
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__item' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'card_shadow_tabs' );

		$this->start_controls_tab( 'card_shadow_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eap-posts__item',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'card_shadow_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow_hover',
				'selector' => '{{WRAPPER}} .eap-posts__item:hover',
			)
		);

		$this->add_control(
			'card_hover_lift',
			array(
				'label'      => __( 'Hover Lift', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 0, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__item:hover' => 'transform: translateY(-{{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_image_style() {
		$this->start_controls_section(
			'section_image_style',
			array(
				'label'     => __( 'Image', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_image' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array( 'px' => array( 'min' => 80, 'max' => 600 ) ),
				'default'    => array( 'size' => 220, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__image'                    => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-posts__img'                      => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;',
					'{{WRAPPER}} .eap-posts--overlay .eap-posts__item' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'image_hover_zoom',
			array(
				'label'        => __( 'Hover Zoom', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function register_title_style() {
		$this->start_controls_section(
			'section_title_style',
			array(
				'label'     => __( 'Title', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		// NOTE: no default — a hardcoded default would emit {{WRAPPER}}-scoped CSS
		// that beats posts.css's light-on-dark overlay colours.
		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__title, {{WRAPPER}} .eap-posts__title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__title a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-posts__title',
			)
		);

		$this->end_controls_section();
	}

	protected function register_meta_style() {
		$this->start_controls_section(
			'section_meta_style',
			array(
				'label'     => __( 'Meta', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_meta' => 'yes' ),
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__meta' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'meta_typography',
				'selector' => '{{WRAPPER}} .eap-posts__meta',
			)
		);

		$this->end_controls_section();
	}

	protected function register_excerpt_style() {
		$this->start_controls_section(
			'section_excerpt_style',
			array(
				'label'     => __( 'Excerpt', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_excerpt' => 'yes' ),
			)
		);

		$this->add_control(
			'excerpt_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__excerpt' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'excerpt_typography',
				'selector' => '{{WRAPPER}} .eap-posts__excerpt',
			)
		);

		$this->end_controls_section();
	}

	protected function register_button_style() {
		$this->start_controls_section(
			'section_button_style',
			array(
				'label'     => __( 'Read More', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_readmore' => 'yes' ),
			)
		);

		$this->add_control(
			'readmore_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__readmore' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'readmore_color_hover',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__readmore:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'readmore_typography',
				'selector'  => '{{WRAPPER}} .eap-posts__readmore',
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE — ARROWS / PAGINATION
	 * ================================================================== */

	protected function register_arrows_style() {
		$this->start_controls_section(
			'section_arrows_style',
			array(
				'label'     => __( 'Arrows', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_arrows' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'arrow_box',
			array(
				'label'      => __( 'Button Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 80 ) ),
				'default'    => array( 'size' => 44, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts-slider__arrow' => '--eap-ps-arrow: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 40 ) ),
				'default'    => array( 'size' => 18, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts-slider__arrow' => '--eap-ps-arrow-icon: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_offset',
			array(
				'label'      => __( 'Horizontal Offset', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => -60, 'max' => 40 ) ),
				'default'    => array( 'size' => -10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts-slider__arrow' => '--eap-ps-arrow-offset: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
				'default'    => array( 'size' => 50, 'unit' => '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts-slider__arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'arrow_tabs' );

		$this->start_controls_tab( 'arrow_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'arrow_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts-slider__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts-slider__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'arrow_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'arrow_color_hover',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts-slider__arrow:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts-slider__arrow:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_pagination_style() {
		$this->start_controls_section(
			'section_pagination_style',
			array(
				'label'     => __( 'Pagination', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_pagination' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'pagination_spacing',
			array(
				'label'      => __( 'Spacing Above', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 26, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts-slider__pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bullet_size',
			array(
				'label'      => __( 'Bullet Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 4, 'max' => 24 ) ),
				'default'    => array( 'size' => 9, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts-slider__pagination .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bullet_color',
			array(
				'label'     => __( 'Bullet Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d1d5db',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts-slider__pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}}; opacity: 1;',
				),
			)
		);

		$this->add_control(
			'bullet_color_active',
			array(
				'label'     => __( 'Active Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts-slider__pagination .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eap-posts-slider__pagination .swiper-pagination-progressbar-fill' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'fraction_color',
			array(
				'label'     => __( 'Fraction Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#374151',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts-slider__pagination' => 'color: {{VALUE}};',
				),
				'condition' => array( 'pagination_type' => 'fraction' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$editor   = $this->eap_is_editor();
		$current  = $this->eap_get_post_id();

		$spec = array(
			'source'          => 'latest',
			'post_type'       => ! empty( $settings['post_type'] ) ? $settings['post_type'] : 'post',
			'per_page'        => isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 9,
			'orderby'         => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date',
			'order'           => ! empty( $settings['order'] ) ? $settings['order'] : 'DESC',
			'offset'          => isset( $settings['offset'] ) ? (int) $settings['offset'] : 0,
			'include_terms'   => isset( $settings['include_terms'] ) ? $settings['include_terms'] : array(),
			'exclude_terms'   => isset( $settings['exclude_terms'] ) ? $settings['exclude_terms'] : array(),
			'exclude_current' => 'yes' === ( $settings['exclude_current'] ?? '' ),
			'current_id'      => $current,
			'ignore_sticky'   => 'yes' === ( $settings['ignore_sticky'] ?? 'yes' ),
		);

		$layout = ( 'overlay' === ( $settings['layout'] ?? 'grid' ) ) ? 'overlay' : 'grid';

		$display = array(
			'layout'         => $layout,
			'show_image'     => 'yes' === ( $settings['show_image'] ?? 'yes' ),
			'show_badge'     => 'yes' === ( $settings['show_badge'] ?? 'yes' ),
			'show_title'     => 'yes' === ( $settings['show_title'] ?? 'yes' ),
			'show_meta'      => 'yes' === ( $settings['show_meta'] ?? 'yes' ),
			'show_excerpt'   => 'yes' === ( $settings['show_excerpt'] ?? 'yes' ),
			'show_readmore'  => 'yes' === ( $settings['show_readmore'] ?? 'yes' ),
			'title_tag'      => ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h3',
			'excerpt_length' => isset( $settings['excerpt_length'] ) ? (int) $settings['excerpt_length'] : 18,
			'meta'           => isset( $settings['meta_data'] ) ? $settings['meta_data'] : array( 'date', 'comments' ),
			'image_size'     => ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'medium_large',
			'readmore_text'  => isset( $settings['readmore_text'] ) ? $settings['readmore_text'] : __( 'Read More', 'elementor-animatepro' ),
		);

		$args  = EAP_Posts_Query::build_query_args( $spec, 1 );
		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-posts eap-posts-slider eap-posts--empty">' . esc_html__( 'No posts found for this query.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$show_arrows     = 'yes' === ( $settings['show_arrows'] ?? 'yes' );
		$show_pagination = 'yes' === ( $settings['show_pagination'] ?? 'yes' );

		$slider_data = array(
			'slidesDesktop'    => isset( $settings['slides_desktop'] ) ? max( 1, (int) $settings['slides_desktop'] ) : 3,
			'slidesTablet'     => isset( $settings['slides_tablet'] ) ? max( 1, (int) $settings['slides_tablet'] ) : 2,
			'slidesMobile'     => isset( $settings['slides_mobile'] ) ? max( 1, (int) $settings['slides_mobile'] ) : 1,
			'spaceBetween'     => isset( $settings['space_between']['size'] ) ? (int) $settings['space_between']['size'] : 24,
			'speed'            => isset( $settings['speed'] ) ? max( 100, (int) $settings['speed'] ) : 600,
			'loop'             => 'yes' === ( $settings['loop'] ?? 'yes' ),
			'centered'         => 'yes' === ( $settings['centered_slides'] ?? '' ),
			'autoplay'         => 'yes' === ( $settings['autoplay'] ?? '' ),
			'autoplayDelay'    => isset( $settings['autoplay_delay'] ) ? (float) $settings['autoplay_delay'] : 4,
			'resumeAfter'      => 'yes' === ( $settings['pause_on_interaction'] ?? 'yes' ),
			'navigation'       => $show_arrows,
			'pagination'       => $show_pagination,
			'paginationType'   => ! empty( $settings['pagination_type'] ) ? $settings['pagination_type'] : 'bullets',
		);

		$classes = array(
			'eap-widget',
			'eap-posts',
			'eap-posts-slider',
			'eap-posts--' . $layout,
		);
		if ( 'yes' === ( $settings['image_hover_zoom'] ?? 'yes' ) ) {
			$classes[] = 'eap-posts--zoom';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="eap-posts-slider__swiper swiper" data-eap-posts-slider="<?php echo esc_attr( wp_json_encode( $slider_data ) ); ?>">
				<div class="swiper-wrapper">
					<?php foreach ( $query->posts as $post ) : ?>
						<?php $pid = is_object( $post ) ? (int) $post->ID : (int) $post; ?>
						<div class="swiper-slide eap-posts-slider__slide">
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built by EAP_Posts_Query with escaped helpers.
							echo EAP_Posts_Query::render_card( $pid, $display );
							?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( $show_arrows ) : ?>
				<button type="button" class="eap-posts-slider__arrow eap-posts-slider__arrow--prev" aria-label="<?php echo esc_attr__( 'Previous', 'elementor-animatepro' ); ?>">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
				<button type="button" class="eap-posts-slider__arrow eap-posts-slider__arrow--next" aria-label="<?php echo esc_attr__( 'Next', 'elementor-animatepro' ); ?>">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
			<?php endif; ?>

			<?php if ( $show_pagination ) : ?>
				<div class="eap-posts-slider__pagination"></div>
			<?php endif; ?>
		</div>
		<?php
		wp_reset_postdata();
	}

	/**
	 * Registered image sizes for the size select.
	 *
	 * @return array<string, string>
	 */
	protected function get_image_size_options() {
		$options = array();
		foreach ( get_intermediate_image_sizes() as $size ) {
			$options[ $size ] = ucwords( str_replace( array( '_', '-' ), ' ', $size ) );
		}
		$options['full'] = __( 'Full', 'elementor-animatepro' );
		return $options;
	}
}
