<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

/**
 * Breaking News Slider — a news ticker: a pinned label ("Breaking News") beside a
 * one-at-a-time slider of post headlines that auto-advances horizontally or
 * vertically.
 *
 * Query comes from the shared EAP_Posts_Query engine, but the ticker item is
 * bespoke (headline link + optional thumb / badge / date), NOT the shared
 * render_card — a ticker row is nothing like a post card.
 *
 * Follows the plugin's slider convention: Elementor's bundled Swiper is added to
 * get_script_depends() only when registered, markup is
 * `.eap-breaking-news__swiper.swiper > .swiper-wrapper > .swiper-slide`, and the
 * config travels as JSON on `data-eap-breaking-news`. Pause-on-hover is done in
 * JS (rather than Swiper's `pauseOnMouseEnter`) so it works on the older Swiper
 * builds Elementor may bundle.
 */
class EAP_Widget_Breaking_News_Slider extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-breaking-news-slider';
	}

	public function get_title() {
		return __( 'Breaking News Slider', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-animated-headline';
	}

	public function get_keywords() {
		return array( 'breaking', 'news', 'ticker', 'slider', 'headlines', 'posts', 'dynamic' );
	}

	public function get_style_depends() {
		return $this->eap_with_swiper_style( array( 'eap-core', 'eap-breaking-news-slider', 'elementor-icons-fa-solid' ) );
	}

	public function get_script_depends() {
		$deps = array(
			'eap-core-runtime',
			'eap-breaking-news-slider-script',
		);
		if ( wp_script_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}

		return array_unique( $deps );
	}

	protected function register_controls() {
		$this->register_query_section();
		$this->register_label_section();
		$this->register_ticker_section();
		$this->register_item_section();

		$this->register_bar_style();
		$this->register_label_style();
		$this->register_title_style();
		$this->register_meta_style();
		$this->register_thumb_style();
		$this->register_arrows_style();
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
				'label'   => __( 'Number of Headlines', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 30,
				'default' => 8,
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
					'DESC' => __( 'Newest First', 'elementor-animatepro' ),
					'ASC'  => __( 'Oldest First', 'elementor-animatepro' ),
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

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — LABEL
	 * ================================================================== */

	protected function register_label_section() {
		$this->start_controls_section(
			'section_label',
			array(
				'label' => __( 'Label', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_label',
			array(
				'label'        => __( 'Label', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'label_text',
			array(
				'label'     => __( 'Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Breaking News', 'elementor-animatepro' ),
				'condition' => array( 'show_label' => 'yes' ),
			)
		);

		$this->add_control(
			'label_position',
			array(
				'label'     => __( 'Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => array(
					'left'  => __( 'Left', 'elementor-animatepro' ),
					'right' => __( 'Right', 'elementor-animatepro' ),
				),
				'condition' => array( 'show_label' => 'yes' ),
			)
		);

		$this->add_control(
			'label_icon',
			array(
				'label'       => __( 'Icon', 'elementor-animatepro' ),
				'type'        => Controls_Manager::ICONS,
				'condition'   => array( 'show_label' => 'yes' ),
				'description' => __( 'Optional. Shown before the label text.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_pulse',
			array(
				'label'        => __( 'Pulsing Dot', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'show_label' => 'yes' ),
				'description'  => __( 'A live-style blinking dot (respects reduced-motion).', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — TICKER
	 * ================================================================== */

	protected function register_ticker_section() {
		$this->start_controls_section(
			'section_ticker',
			array(
				'label' => __( 'Ticker', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'   => __( 'Direction', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => __( 'Horizontal (slide across)', 'elementor-animatepro' ),
					'vertical'   => __( 'Vertical (flip up)', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'ticker_height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 32, 'max' => 120 ) ),
				'default'    => array( 'size' => 52, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-breaking-news' => '--eap-bn-h: {{SIZE}}{{UNIT}};',
				),
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
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => __( 'Delay (s)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 20,
				'step'      => 0.5,
				'default'   => 4,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => __( 'Pause on Hover', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'autoplay' => 'yes' ),
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
			'show_arrows',
			array(
				'label'        => __( 'Arrows', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — HEADLINE ITEM
	 * ================================================================== */

	protected function register_item_section() {
		$this->start_controls_section(
			'section_item',
			array(
				'label' => __( 'Headline', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_thumb',
			array(
				'label'        => __( 'Thumbnail', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_badge',
			array(
				'label'        => __( 'Category Badge', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'badge_taxonomy',
			array(
				'label'     => __( 'Taxonomy', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'category',
				'options'   => $this->get_taxonomy_options(),
				'condition' => array( 'show_badge' => 'yes' ),
			)
		);

		$this->add_control(
			'show_date',
			array(
				'label'        => __( 'Date', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'date_format',
			array(
				'label'       => __( 'Date Format', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Site default', 'elementor-animatepro' ),
				'description' => __( 'PHP date format (e.g. M j, Y). Empty uses the site default.', 'elementor-animatepro' ),
				'condition'   => array( 'show_date' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_bar_style() {
		$this->start_controls_section(
			'section_bar_style',
			array(
				'label' => __( 'Bar', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bar_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-breaking-news__bar' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'bar_border',
				'selector' => '{{WRAPPER}} .eap-breaking-news__bar',
			)
		);

		$this->add_responsive_control(
			'bar_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 6, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-breaking-news__bar' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ticker_padding',
			array(
				'label'      => __( 'Headline Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 0, 'right' => 18, 'bottom' => 0, 'left' => 18, 'unit' => 'px', 'isLinked' => false ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-breaking-news__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_label_style() {
		$this->start_controls_section(
			'section_label_style',
			array(
				'label'     => __( 'Label', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_label' => 'yes' ),
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-breaking-news__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'label_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e11d2a',
				'selectors' => array(
					'{{WRAPPER}} .eap-breaking-news__label' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .eap-breaking-news__label',
			)
		);

		$this->add_responsive_control(
			'label_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 0, 'right' => 18, 'bottom' => 0, 'left' => 18, 'unit' => 'px', 'isLinked' => false ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-breaking-news__label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'label_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 32 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-breaking-news__label' => '--eap-bn-label-icon: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pulse_color',
			array(
				'label'     => __( 'Dot Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-breaking-news__pulse' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'show_pulse' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_title_style() {
		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => __( 'Headline', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-breaking-news__title' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .eap-breaking-news__item:hover .eap-breaking-news__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-breaking-news__title',
			)
		);

		$this->end_controls_section();
	}

	protected function register_meta_style() {
		$this->start_controls_section(
			'section_meta_style',
			array(
				'label' => __( 'Date & Badge', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'date_color',
			array(
				'label'     => __( 'Date Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9ca3af',
				'selectors' => array(
					'{{WRAPPER}} .eap-breaking-news__date' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_date' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'date_typography',
				'label'     => __( 'Date Typography', 'elementor-animatepro' ),
				'selector'  => '{{WRAPPER}} .eap-breaking-news__date',
				'condition' => array( 'show_date' => 'yes' ),
			)
		);

		$this->add_control(
			'badge_color',
			array(
				'label'     => __( 'Badge Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-breaking-news__badge' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_badge' => 'yes' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'badge_bg',
			array(
				'label'     => __( 'Badge Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-breaking-news__badge' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'show_badge' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_thumb_style() {
		$this->start_controls_section(
			'section_thumb_style',
			array(
				'label'     => __( 'Thumbnail', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_thumb' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'thumb_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 90 ) ),
				'default'    => array( 'size' => 34, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-breaking-news__thumb' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'thumb_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
				'default'    => array( 'size' => 4, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-breaking-news__thumb' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

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
			'arrow_size',
			array(
				'label'      => __( 'Button Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 60 ) ),
				'default'    => array( 'size' => 30, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-breaking-news__nav' => '--eap-bn-arrow: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'bn_arrow_tabs' );

		$this->start_controls_tab( 'bn_arrow_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'arrow_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#374151',
				'selectors' => array(
					'{{WRAPPER}} .eap-breaking-news__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f3f4f6',
				'selectors' => array(
					'{{WRAPPER}} .eap-breaking-news__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'bn_arrow_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'arrow_color_hover',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-breaking-news__arrow:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .eap-breaking-news__arrow:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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
			'per_page'        => isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 8,
			'orderby'         => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date',
			'order'           => ! empty( $settings['order'] ) ? $settings['order'] : 'DESC',
			'offset'          => isset( $settings['offset'] ) ? (int) $settings['offset'] : 0,
			'include_terms'   => isset( $settings['include_terms'] ) ? $settings['include_terms'] : array(),
			'exclude_terms'   => isset( $settings['exclude_terms'] ) ? $settings['exclude_terms'] : array(),
			'exclude_current' => 'yes' === ( $settings['exclude_current'] ?? '' ),
			'current_id'      => $current,
			'ignore_sticky'   => true,
		);

		$args  = EAP_Posts_Query::build_query_args( $spec, 1 );
		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-breaking-news eap-breaking-news--empty">' . esc_html__( 'No posts found for this query.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$direction   = ( 'vertical' === ( $settings['direction'] ?? 'horizontal' ) ) ? 'vertical' : 'horizontal';
		$show_label  = 'yes' === ( $settings['show_label'] ?? 'yes' );
		$label_right = 'right' === ( $settings['label_position'] ?? 'left' );
		$show_arrows = 'yes' === ( $settings['show_arrows'] ?? 'yes' );
		$show_thumb  = 'yes' === ( $settings['show_thumb'] ?? '' );
		$show_badge  = 'yes' === ( $settings['show_badge'] ?? '' );
		$show_date   = 'yes' === ( $settings['show_date'] ?? 'yes' );
		$date_format = isset( $settings['date_format'] ) ? trim( (string) $settings['date_format'] ) : '';
		$taxonomy    = ! empty( $settings['badge_taxonomy'] ) ? $settings['badge_taxonomy'] : 'category';

		$ticker_data = array(
			'direction'     => $direction,
			'speed'         => isset( $settings['speed'] ) ? max( 100, (int) $settings['speed'] ) : 600,
			'loop'          => 'yes' === ( $settings['loop'] ?? 'yes' ),
			'autoplay'      => 'yes' === ( $settings['autoplay'] ?? 'yes' ),
			'autoplayDelay' => isset( $settings['autoplay_delay'] ) ? (float) $settings['autoplay_delay'] : 4,
			'pauseOnHover'  => 'yes' === ( $settings['pause_on_hover'] ?? 'yes' ),
			'navigation'    => $show_arrows,
		);

		$classes = array(
			'eap-widget',
			'eap-breaking-news',
			'eap-breaking-news--' . $direction,
		);
		if ( $label_right ) {
			$classes[] = 'eap-breaking-news--label-right';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="eap-breaking-news__bar">
				<?php if ( $show_label ) : ?>
					<div class="eap-breaking-news__label">
						<?php if ( 'yes' === ( $settings['show_pulse'] ?? 'yes' ) ) : ?>
							<span class="eap-breaking-news__pulse" aria-hidden="true"></span>
						<?php endif; ?>
						<?php if ( ! empty( $settings['label_icon']['value'] ) ) : ?>
							<span class="eap-breaking-news__label-icon" aria-hidden="true"><?php \Elementor\Icons_Manager::render_icon( $settings['label_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
						<?php endif; ?>
						<span class="eap-breaking-news__label-text"><?php echo esc_html( $settings['label_text'] ?? '' ); ?></span>
					</div>
				<?php endif; ?>

				<div class="eap-breaking-news__swiper swiper" data-eap-breaking-news="<?php echo esc_attr( wp_json_encode( $ticker_data ) ); ?>">
					<div class="swiper-wrapper">
						<?php
						foreach ( $query->posts as $post ) :
							$pid = is_object( $post ) ? (int) $post->ID : (int) $post;
							?>
							<div class="swiper-slide eap-breaking-news__slide">
								<a class="eap-breaking-news__item" href="<?php echo esc_url( get_permalink( $pid ) ); ?>">
									<?php
									if ( $show_thumb ) {
										$thumb = get_the_post_thumbnail_url( $pid, 'thumbnail' );
										if ( $thumb ) {
											printf(
												'<img class="eap-breaking-news__thumb" src="%1$s" alt="%2$s" loading="lazy" />',
												esc_url( $thumb ),
												esc_attr( get_the_title( $pid ) )
											);
										}
									}

									if ( $show_badge ) {
										echo $this->get_badge( $pid, $taxonomy ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
									}
									?>
									<span class="eap-breaking-news__title"><?php echo esc_html( get_the_title( $pid ) ); ?></span>
									<?php if ( $show_date ) : ?>
										<span class="eap-breaking-news__date"><?php echo esc_html( get_the_date( $date_format, $pid ) ); ?></span>
									<?php endif; ?>
								</a>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( $show_arrows ) : ?>
					<div class="eap-breaking-news__nav">
						<button type="button" class="eap-breaking-news__arrow eap-breaking-news__arrow--prev" aria-label="<?php echo esc_attr__( 'Previous headline', 'elementor-animatepro' ); ?>">
							<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</button>
						<button type="button" class="eap-breaking-news__arrow eap-breaking-news__arrow--next" aria-label="<?php echo esc_attr__( 'Next headline', 'elementor-animatepro' ); ?>">
							<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</button>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
		wp_reset_postdata();
	}

	/**
	 * First term of the chosen taxonomy as a badge.
	 *
	 * @param int    $pid      Post ID.
	 * @param string $taxonomy Taxonomy.
	 * @return string
	 */
	protected function get_badge( $pid, $taxonomy ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return '';
		}

		$terms = get_the_terms( $pid, $taxonomy );
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}

		return '<span class="eap-breaking-news__badge">' . esc_html( $terms[0]->name ) . '</span>';
	}

	/**
	 * Public taxonomies for the badge select.
	 *
	 * @return array<string, string>
	 */
	protected function get_taxonomy_options() {
		$options = array();
		foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $tax ) {
			if ( in_array( $tax->name, array( 'post_format' ), true ) ) {
				continue;
			}
			$options[ $tax->name ] = $tax->labels->singular_name;
		}
		return $options;
	}
}
