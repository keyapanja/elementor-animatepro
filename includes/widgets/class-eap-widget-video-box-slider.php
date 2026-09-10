<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

/**
 * Video Box Slider — a Swiper carousel of hand-authored video boxes (poster +
 * play button + title/description). Clicking play opens the video in a LIGHTBOX
 * (default) or swaps it in INLINE.
 *
 * Manual repeater, following the Image Box Slider precedent — the "Box" widgets
 * in this plugin are author-driven, unlike the query-driven Posts/Category
 * sliders. Supports YouTube, Vimeo and self-hosted MP4/WebM.
 *
 * The provider + video ID are parsed SERVER-side by parse_video() and stamped on
 * the item as data-provider / data-video-id / data-src, so the JS only has to
 * build an embed URL — no URL parsing (or unescaped interpolation) in the
 * browser. When a YouTube box has no poster, its thumbnail is used automatically
 * so the slider looks right with zero setup.
 *
 * Follows the house slider convention: bundled Swiper only when registered,
 * `.eap-video-box-slider__swiper.swiper` markup, JSON config on
 * `data-eap-video-box-slider`.
 */
class EAP_Widget_Video_Box_Slider extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-video-box-slider';
	}

	public function get_title() {
		return __( 'Video Box Slider', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-video-playlist';
	}

	public function get_keywords() {
		return array( 'video', 'box', 'slider', 'carousel', 'youtube', 'vimeo', 'lightbox' );
	}

	public function get_style_depends() {
		return $this->eap_with_swiper_style( array( 'eap-core', 'eap-video-box-slider' ) );
	}

	public function get_script_depends() {
		$deps = array(
			'eap-core-runtime',
			'eap-video-box-slider-script',
		);
		if ( wp_script_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}

		return array_unique( $deps );
	}

	protected function register_controls() {
		$this->register_videos_section();
		$this->register_layout_section();
		$this->register_slider_section();

		$this->register_card_style();
		$this->register_media_style();
		$this->register_play_style();
		$this->register_title_style();
		$this->register_desc_style();
		$this->register_meta_style();
		$this->register_lightbox_style();
		$this->register_arrows_style();
		$this->register_pagination_style();
	}

	/* =====================================================================
	 * CONTENT — VIDEOS
	 * ================================================================== */

	protected function register_videos_section() {
		$this->start_controls_section(
			'section_videos',
			array(
				'label' => __( 'Videos', 'elementor-animatepro' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'video_type',
			array(
				'label'   => __( 'Source', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'youtube',
				'options' => array(
					'youtube' => __( 'YouTube', 'elementor-animatepro' ),
					'vimeo'   => __( 'Vimeo', 'elementor-animatepro' ),
					'hosted'  => __( 'Self-hosted', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'video_url',
			array(
				'label'       => __( 'Video URL', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => 'https://www.youtube.com/watch?v=…',
				'condition'   => array( 'video_type!' => 'hosted' ),
			)
		);

		$repeater->add_control(
			'hosted_video',
			array(
				'label'       => __( 'Video File', 'elementor-animatepro' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'video' ),
				'condition'   => array( 'video_type' => 'hosted' ),
			)
		);

		$repeater->add_control(
			'poster',
			array(
				'label'       => __( 'Poster Image', 'elementor-animatepro' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Optional for YouTube — its thumbnail is used automatically.', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Video title', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label' => __( 'Description', 'elementor-animatepro' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 3,
			)
		);

		$repeater->add_control(
			'duration',
			array(
				'label'       => __( 'Duration', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '3:24',
			)
		);

		$repeater->add_control(
			'badge',
			array(
				'label'       => __( 'Badge', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'e.g. Tutorial', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'videos',
			array(
				'label'       => __( 'Video Boxes', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'title' => __( 'Video one', 'elementor-animatepro' ) ),
					array( 'title' => __( 'Video two', 'elementor-animatepro' ) ),
					array( 'title' => __( 'Video three', 'elementor-animatepro' ) ),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — LAYOUT
	 * ================================================================== */

	protected function register_layout_section() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'play_mode',
			array(
				'label'   => __( 'Play In', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'lightbox',
				'options' => array(
					'lightbox' => __( 'Lightbox', 'elementor-animatepro' ),
					'inline'   => __( 'Inline (in the box)', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'aspect_ratio',
			array(
				'label'     => __( 'Aspect Ratio', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '16 / 9',
				'options'   => array(
					'16 / 9' => '16:9',
					'4 / 3'  => '4:3',
					'1 / 1'  => '1:1',
					'3 / 4'  => '3:4',
					'9 / 16' => '9:16',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-video-box-slider__media' => 'aspect-ratio: {{VALUE}};',
				),
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
				),
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
			'show_title'    => array( __( 'Title', 'elementor-animatepro' ), 'yes' ),
			'show_desc'     => array( __( 'Description', 'elementor-animatepro' ), 'yes' ),
			'show_duration' => array( __( 'Duration', 'elementor-animatepro' ), 'yes' ),
			'show_badge'    => array( __( 'Badge', 'elementor-animatepro' ), 'yes' ),
		) as $key => $data ) {
			$this->add_control(
				$key,
				array(
					'label'        => $data[0],
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'elementor-animatepro' ),
					'label_off'    => __( 'Hide', 'elementor-animatepro' ),
					'return_value' => 'yes',
					'default'      => $data[1],
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
				'default'      => '',
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
				'default'   => 5,
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
	 * STYLE
	 * ================================================================== */

	protected function register_card_style() {
		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => __( 'Box', 'elementor-animatepro' ),
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
					'{{WRAPPER}} .eap-video-box-slider__item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Content Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 18, 'right' => 18, 'bottom' => 18, 'left' => 18, 'unit' => 'px', 'isLinked' => true ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-video-box-slider__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .eap-video-box-slider__item',
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
					'{{WRAPPER}} .eap-video-box-slider__item' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eap-video-box-slider__item',
			)
		);

		$this->add_responsive_control(
			'card_hover_lift',
			array(
				'label'      => __( 'Hover Lift', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 6, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-video-box-slider__item:hover' => 'transform: translateY(-{{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_media_style() {
		$this->start_controls_section(
			'section_media_style',
			array(
				'label' => __( 'Poster', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'scrim_color',
			array(
				'label'     => __( 'Overlay Tint', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.25)',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-box-slider__scrim' => 'background-color: {{VALUE}};',
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

	protected function register_play_style() {
		$this->start_controls_section(
			'section_play_style',
			array(
				'label' => __( 'Play Button', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'play_box',
			array(
				'label'      => __( 'Button Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 30, 'max' => 120 ) ),
				'default'    => array( 'size' => 62, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-video-box-slider__play' => '--eap-vb-play: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'play_icon',
			array(
				'label'       => __( 'Icon', 'elementor-animatepro' ),
				'type'        => Controls_Manager::ICONS,
				'description' => __( 'Leave empty for the built-in play triangle.', 'elementor-animatepro' ),
			)
		);

		$this->start_controls_tabs( 'vb_play_tabs' );

		$this->start_controls_tab( 'vb_play_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'play_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-box-slider__play' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'play_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.92)',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-box-slider__play' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'vb_play_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'play_color_hover',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-box-slider__item:hover .eap-video-box-slider__play' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'play_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e11d2a',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-box-slider__item:hover .eap-video-box-slider__play' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-box-slider__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-video-box-slider__title',
			)
		);

		$this->end_controls_section();
	}

	protected function register_desc_style() {
		$this->start_controls_section(
			'section_desc_style',
			array(
				'label'     => __( 'Description', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_desc' => 'yes' ),
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4b5563',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-box-slider__desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .eap-video-box-slider__desc',
			)
		);

		$this->end_controls_section();
	}

	protected function register_meta_style() {
		$this->start_controls_section(
			'section_meta_style',
			array(
				'label' => __( 'Duration & Badge', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'duration_color',
			array(
				'label'     => __( 'Duration Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-box-slider__duration' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_duration' => 'yes' ),
			)
		);

		$this->add_control(
			'duration_bg',
			array(
				'label'     => __( 'Duration Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.7)',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-box-slider__duration' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'show_duration' => 'yes' ),
			)
		);

		$this->add_control(
			'badge_color',
			array(
				'label'     => __( 'Badge Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-box-slider__badge' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .eap-video-box-slider__badge' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'show_badge' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_lightbox_style() {
		$this->start_controls_section(
			'section_lightbox_style',
			array(
				'label'     => __( 'Lightbox', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'play_mode' => 'lightbox' ),
			)
		);

		$this->add_control(
			'lightbox_backdrop',
			array(
				'label'     => __( 'Backdrop', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.85)',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-box-slider__backdrop' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'lightbox_width',
			array(
				'label'      => __( 'Max Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 320, 'max' => 1600 ), '%' => array( 'min' => 30, 'max' => 100 ) ),
				'default'    => array( 'size' => 960, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-video-box-slider__dialog' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'lightbox_close_color',
			array(
				'label'     => __( 'Close Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-box-slider__close' => 'color: {{VALUE}};',
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
			'arrow_box',
			array(
				'label'      => __( 'Button Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 80 ) ),
				'default'    => array( 'size' => 44, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-video-box-slider__arrow' => '--eap-vb-arrow: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .eap-video-box-slider__arrow' => '--eap-vb-arrow-offset: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'vb_arrow_tabs' );

		$this->start_controls_tab( 'vb_arrow_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'arrow_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-box-slider__arrow' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .eap-video-box-slider__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'vb_arrow_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'arrow_color_hover',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-box-slider__arrow:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .eap-video-box-slider__arrow:hover' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .eap-video-box-slider__pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .eap-video-box-slider__pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}}; opacity: 1;',
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
					'{{WRAPPER}} .eap-video-box-slider__pagination .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eap-video-box-slider__pagination .swiper-pagination-progressbar-fill' => 'background-color: {{VALUE}};',
				),
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
		$videos   = isset( $settings['videos'] ) && is_array( $settings['videos'] ) ? $settings['videos'] : array();

		if ( empty( $videos ) ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-video-box-slider eap-video-box-slider--empty">' . esc_html__( 'Add a video box to get started.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$mode            = ( 'inline' === ( $settings['play_mode'] ?? 'lightbox' ) ) ? 'inline' : 'lightbox';
		$show_arrows     = 'yes' === ( $settings['show_arrows'] ?? 'yes' );
		$show_pagination = 'yes' === ( $settings['show_pagination'] ?? 'yes' );
		$show_title      = 'yes' === ( $settings['show_title'] ?? 'yes' );
		$show_desc       = 'yes' === ( $settings['show_desc'] ?? 'yes' );
		$show_duration   = 'yes' === ( $settings['show_duration'] ?? 'yes' );
		$show_badge      = 'yes' === ( $settings['show_badge'] ?? 'yes' );
		$title_tag       = $this->safe_tag( $settings['title_tag'] ?? 'h3' );

		$slider_data = array(
			'slidesDesktop'  => isset( $settings['slides_desktop'] ) ? max( 1, (int) $settings['slides_desktop'] ) : 3,
			'slidesTablet'   => isset( $settings['slides_tablet'] ) ? max( 1, (int) $settings['slides_tablet'] ) : 2,
			'slidesMobile'   => isset( $settings['slides_mobile'] ) ? max( 1, (int) $settings['slides_mobile'] ) : 1,
			'spaceBetween'   => isset( $settings['space_between']['size'] ) ? (int) $settings['space_between']['size'] : 24,
			'speed'          => isset( $settings['speed'] ) ? max( 100, (int) $settings['speed'] ) : 600,
			'loop'           => 'yes' === ( $settings['loop'] ?? '' ),
			'centered'       => 'yes' === ( $settings['centered_slides'] ?? '' ),
			'autoplay'       => 'yes' === ( $settings['autoplay'] ?? '' ),
			'autoplayDelay'  => isset( $settings['autoplay_delay'] ) ? (float) $settings['autoplay_delay'] : 5,
			'pauseOnHover'   => 'yes' === ( $settings['pause_on_hover'] ?? 'yes' ),
			'navigation'     => $show_arrows,
			'pagination'     => $show_pagination,
			'paginationType' => ! empty( $settings['pagination_type'] ) ? $settings['pagination_type'] : 'bullets',
		);

		$classes = array( 'eap-widget', 'eap-video-box-slider', 'eap-video-box-slider--' . $mode );
		if ( 'yes' === ( $settings['image_hover_zoom'] ?? 'yes' ) ) {
			$classes[] = 'eap-video-box-slider--zoom';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		$this->add_render_attribute( 'wrapper', 'data-eap-vb-mode', $mode );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="eap-video-box-slider__swiper swiper" data-eap-video-box-slider="<?php echo esc_attr( wp_json_encode( $slider_data ) ); ?>">
				<div class="swiper-wrapper">
					<?php
					foreach ( $videos as $item ) :
						$video  = $this->parse_video( $item );
						$poster = $this->get_poster( $item, $video );
						?>
						<div class="swiper-slide eap-video-box-slider__slide">
							<div class="eap-video-box-slider__item"
								<?php if ( $video['provider'] ) : ?>
									data-provider="<?php echo esc_attr( $video['provider'] ); ?>"
									<?php if ( '' !== $video['id'] ) : ?>
										data-video-id="<?php echo esc_attr( $video['id'] ); ?>"
									<?php endif; ?>
									<?php if ( '' !== $video['src'] ) : ?>
										data-src="<?php echo esc_url( $video['src'] ); ?>"
									<?php endif; ?>
								<?php endif; ?>
							>
								<div class="eap-video-box-slider__media">
									<?php if ( $poster ) : ?>
										<img class="eap-video-box-slider__poster" src="<?php echo esc_url( $poster ); ?>" alt="<?php echo esc_attr( $item['title'] ?? '' ); ?>" loading="lazy" />
									<?php else : ?>
										<span class="eap-video-box-slider__poster eap-video-box-slider__poster--ph" aria-hidden="true"></span>
									<?php endif; ?>

									<span class="eap-video-box-slider__scrim" aria-hidden="true"></span>

									<?php if ( $show_badge && ! empty( $item['badge'] ) ) : ?>
										<span class="eap-video-box-slider__badge"><?php echo esc_html( $item['badge'] ); ?></span>
									<?php endif; ?>

									<?php if ( $show_duration && ! empty( $item['duration'] ) ) : ?>
										<span class="eap-video-box-slider__duration"><?php echo esc_html( $item['duration'] ); ?></span>
									<?php endif; ?>

									<?php if ( $video['provider'] ) : ?>
										<button type="button" class="eap-video-box-slider__play" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: video title. */ __( 'Play %s', 'elementor-animatepro' ), $item['title'] ?? '' ) ); ?>">
											<span class="eap-video-box-slider__play-icon" aria-hidden="true"><?php $this->render_play_icon( $settings ); ?></span>
										</button>
										<div class="eap-video-box-slider__embed"></div>
									<?php endif; ?>
								</div>

								<?php if ( ( $show_title && ! empty( $item['title'] ) ) || ( $show_desc && ! empty( $item['description'] ) ) ) : ?>
									<div class="eap-video-box-slider__content">
										<?php if ( $show_title && ! empty( $item['title'] ) ) : ?>
											<<?php echo esc_html( $title_tag ); ?> class="eap-video-box-slider__title"><?php echo esc_html( $item['title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
										<?php endif; ?>
										<?php if ( $show_desc && ! empty( $item['description'] ) ) : ?>
											<div class="eap-video-box-slider__desc"><?php echo wp_kses_post( $item['description'] ); ?></div>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( $show_arrows ) : ?>
				<button type="button" class="eap-video-box-slider__arrow eap-video-box-slider__arrow--prev" aria-label="<?php echo esc_attr__( 'Previous', 'elementor-animatepro' ); ?>">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
				<button type="button" class="eap-video-box-slider__arrow eap-video-box-slider__arrow--next" aria-label="<?php echo esc_attr__( 'Next', 'elementor-animatepro' ); ?>">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
			<?php endif; ?>

			<?php if ( $show_pagination ) : ?>
				<div class="eap-video-box-slider__pagination"></div>
			<?php endif; ?>

			<?php if ( 'lightbox' === $mode ) : ?>
				<div class="eap-video-box-slider__lightbox" hidden>
					<div class="eap-video-box-slider__backdrop" aria-hidden="true"></div>
					<div class="eap-video-box-slider__dialog" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr__( 'Video', 'elementor-animatepro' ); ?>">
						<button type="button" class="eap-video-box-slider__close" aria-label="<?php echo esc_attr__( 'Close video', 'elementor-animatepro' ); ?>"><span aria-hidden="true">&times;</span></button>
						<div class="eap-video-box-slider__frame"></div>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Resolve a repeater item to a provider + id / src.
	 *
	 * Parsing happens here (not in JS) so the browser only ever builds an embed
	 * URL from an already-validated id.
	 *
	 * @param array $item Repeater item.
	 * @return array{provider:string,id:string,src:string}
	 */
	protected function parse_video( $item ) {
		$empty = array(
			'provider' => '',
			'id'       => '',
			'src'      => '',
		);

		$type = isset( $item['video_type'] ) ? $item['video_type'] : 'youtube';

		if ( 'hosted' === $type ) {
			$src = isset( $item['hosted_video']['url'] ) ? $item['hosted_video']['url'] : '';
			if ( '' === $src ) {
				return $empty;
			}
			return array(
				'provider' => 'hosted',
				'id'       => '',
				'src'      => $src,
			);
		}

		$url = isset( $item['video_url'] ) ? trim( (string) $item['video_url'] ) : '';
		if ( '' === $url ) {
			return $empty;
		}

		if ( 'youtube' === $type ) {
			// youtu.be/ID, /watch?v=ID, /embed/ID, /shorts/ID, or a bare ID.
			if ( preg_match( '#(?:youtu\.be/|youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/))([A-Za-z0-9_-]{6,})#i', $url, $m ) ) {
				return array(
					'provider' => 'youtube',
					'id'       => $m[1],
					'src'      => '',
				);
			}
			if ( preg_match( '#^[A-Za-z0-9_-]{6,}$#', $url ) ) {
				return array(
					'provider' => 'youtube',
					'id'       => $url,
					'src'      => '',
				);
			}
			return $empty;
		}

		// Vimeo: vimeo.com/ID, player.vimeo.com/video/ID, or a bare numeric ID.
		if ( preg_match( '#vimeo\.com/(?:video/)?(\d+)#i', $url, $m ) ) {
			return array(
				'provider' => 'vimeo',
				'id'       => $m[1],
				'src'      => '',
			);
		}
		if ( preg_match( '#^\d+$#', $url ) ) {
			return array(
				'provider' => 'vimeo',
				'id'       => $url,
				'src'      => '',
			);
		}

		return $empty;
	}

	/**
	 * Poster image: the chosen one, else YouTube's own thumbnail.
	 *
	 * @param array $item  Repeater item.
	 * @param array $video Parsed video.
	 * @return string
	 */
	protected function get_poster( $item, $video ) {
		if ( ! empty( $item['poster']['url'] ) ) {
			return $item['poster']['url'];
		}

		if ( 'youtube' === $video['provider'] && '' !== $video['id'] ) {
			return 'https://img.youtube.com/vi/' . $video['id'] . '/hqdefault.jpg';
		}

		return '';
	}

	/**
	 * The play icon: a custom Icons_Manager icon, or the built-in triangle.
	 *
	 * @param array $settings Settings.
	 * @return void
	 */
	protected function render_play_icon( $settings ) {
		if ( ! empty( $settings['play_icon']['value'] ) ) {
			\Elementor\Icons_Manager::render_icon( $settings['play_icon'], array( 'aria-hidden' => 'true' ) );
			return;
		}

		echo '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M8 5v14l11-7z" fill="currentColor"/></svg>';
	}

	/**
	 * Whitelist the title tag.
	 *
	 * @param string $tag Tag.
	 * @return string
	 */
	protected function safe_tag( $tag ) {
		return in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span' ), true ) ? $tag : 'h3';
	}
}
