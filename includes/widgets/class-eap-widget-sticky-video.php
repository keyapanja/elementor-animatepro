<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Plugin;

/**
 * Sticky Video — an inline video (YouTube, Vimeo or self-hosted) that floats to
 * a corner of the screen when it is scrolled out of view while playing, then
 * returns to its place when scrolled back. It is one player instance moved via
 * CSS, so play/pause stay in sync between the inline and floating states. Play
 * state is read through the YouTube IFrame API / Vimeo Player API / the native
 * <video> events so it only floats while actually playing.
 */
class EAP_Widget_Sticky_Video extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-sticky-video';
	}

	public function get_title() {
		return __( 'Sticky Video', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-play';
	}

	public function get_keywords() {
		return array( 'video', 'sticky', 'float', 'youtube', 'vimeo', 'player', 'floating' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'sticky-video' );
	}

	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-sticky-video-script' );
	}

	protected function register_controls() {
		$this->register_video_controls();
		$this->register_options_controls();
		$this->register_sticky_controls();

		$this->register_video_style();
		$this->register_sticky_style();
		$this->register_close_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_video_controls() {
		$this->start_controls_section(
			'section_video',
			array(
				'label' => __( 'Video', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'video_source',
			array(
				'label'   => __( 'Source', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'youtube',
				'options' => array(
					'youtube' => __( 'YouTube', 'elementor-animatepro' ),
					'vimeo'   => __( 'Vimeo', 'elementor-animatepro' ),
					'hosted'  => __( 'Self Hosted', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'youtube_url',
			array(
				'label'       => __( 'YouTube URL', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				'placeholder' => __( 'https://www.youtube.com/watch?v=…', 'elementor-animatepro' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array( 'video_source' => 'youtube' ),
			)
		);

		$this->add_control(
			'vimeo_url',
			array(
				'label'       => __( 'Vimeo URL', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'https://vimeo.com/235215203',
				'placeholder' => __( 'https://vimeo.com/…', 'elementor-animatepro' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array( 'video_source' => 'vimeo' ),
			)
		);

		$this->add_control(
			'hosted_video',
			array(
				'label'      => __( 'Video File', 'elementor-animatepro' ),
				'type'       => Controls_Manager::MEDIA,
				'media_types' => array( 'video' ),
				'condition'  => array( 'video_source' => 'hosted' ),
			)
		);

		$this->add_control(
			'hosted_external',
			array(
				'label'       => __( 'External URL', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'https://example.com/video.mp4', 'elementor-animatepro' ),
				'description' => __( 'Optional — used instead of the file above.', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array( 'video_source' => 'hosted' ),
			)
		);

		$this->add_control(
			'poster',
			array(
				'label'     => __( 'Poster Image', 'elementor-animatepro' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'video_source' => 'hosted' ),
			)
		);

		$this->add_control(
			'aspect_ratio',
			array(
				'label'   => __( 'Aspect Ratio', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '169',
				'options' => array(
					'169' => '16:9',
					'43'  => '4:3',
					'219' => '21:9',
					'11'  => '1:1',
					'916' => '9:16',
				),
			)
		);

		$this->add_control(
			'start_time',
			array(
				'label'       => __( 'Start Time (seconds)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'default'     => '',
				'condition'   => array( 'video_source!' => 'vimeo' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_options_controls() {
		$this->start_controls_section(
			'section_options',
			array(
				'label' => __( 'Player Options', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Most browsers require the video to be muted to autoplay.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'mute',
			array(
				'label'        => __( 'Mute', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => __( 'Loop', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'controls',
			array(
				'label'        => __( 'Player Controls', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function register_sticky_controls() {
		$this->start_controls_section(
			'section_sticky',
			array(
				'label' => __( 'Sticky', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'sticky_enable',
			array(
				'label'        => __( 'Enable Sticky', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Float the video to a corner when it scrolls out of view.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'sticky_position',
			array(
				'label'     => __( 'Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom-right',
				'options'   => array(
					'bottom-right' => __( 'Bottom Right', 'elementor-animatepro' ),
					'bottom-left'  => __( 'Bottom Left', 'elementor-animatepro' ),
					'top-right'    => __( 'Top Right', 'elementor-animatepro' ),
					'top-left'     => __( 'Top Left', 'elementor-animatepro' ),
				),
				'condition' => array( 'sticky_enable' => 'yes' ),
			)
		);

		$this->add_control(
			'float_when_playing',
			array(
				'label'        => __( 'Only While Playing', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Float only if the video is currently playing.', 'elementor-animatepro' ),
				'condition'    => array( 'sticky_enable' => 'yes' ),
			)
		);

		$this->add_control(
			'show_close',
			array(
				'label'        => __( 'Close Button', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'sticky_enable' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'sticky_width',
			array(
				'label'      => __( 'Sticky Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 180, 'max' => 640 ) ),
				'default'    => array( 'size' => 340, 'unit' => 'px' ),
				'tablet_default' => array( 'size' => 300, 'unit' => 'px' ),
				'mobile_default' => array( 'size' => 220, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sticky-video' => '--eap-sv-sticky-width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'sticky_enable' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_video_style() {
		$this->start_controls_section(
			'section_video_style',
			array(
				'label' => __( 'Video', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_width',
			array(
				'label'      => __( 'Max Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 200, 'max' => 1200 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'size' => 100, 'unit' => '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sticky-video' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-sticky-video__wrap' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'video_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sticky-video__frame' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'video_border',
				'selector' => '{{WRAPPER}} .eap-sticky-video__frame',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'video_shadow',
				'selector' => '{{WRAPPER}} .eap-sticky-video__inner',
			)
		);

		$this->end_controls_section();
	}

	protected function register_sticky_style() {
		$this->start_controls_section(
			'section_sticky_style',
			array(
				'label'     => __( 'Sticky', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'sticky_enable' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'sticky_offset',
			array(
				'label'      => __( 'Distance From Edge', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'size' => 20, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sticky-video' => '--eap-sv-offset: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'sticky_animation',
			array(
				'label'   => __( 'Entrance', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => array(
					'none'  => __( 'None', 'elementor-animatepro' ),
					'fade'  => __( 'Fade', 'elementor-animatepro' ),
					'slide' => __( 'Slide', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'sticky_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sticky-video.is-sticky .eap-sticky-video__frame' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'           => 'sticky_shadow',
				'selector'       => '{{WRAPPER}} .eap-sticky-video.is-sticky .eap-sticky-video__inner',
				'fields_options' => array(
					'box_shadow_type' => array( 'default' => 'yes' ),
					'box_shadow'      => array(
						'default' => array(
							'horizontal' => 0,
							'vertical'   => 12,
							'blur'       => 40,
							'spread'     => 0,
							'color'      => 'rgba(0, 0, 0, 0.32)',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_close_style() {
		$this->start_controls_section(
			'section_close_style',
			array(
				'label'     => __( 'Close Button', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'sticky_enable' => 'yes',
					'show_close'    => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'close_size',
			array(
				'label'      => __( 'Button Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 16, 'max' => 64 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sticky-video__close' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'close_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 6, 'max' => 36 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sticky-video__close' => '--eap-sv-close-icon: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'close_offset',
			array(
				'label'      => __( 'Distance From Corner', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => -20, 'max' => 40 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sticky-video__close' => '--eap-sv-close-offset: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'close_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'default'    => array( 'size' => 50, 'unit' => '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sticky-video__close' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'close_border_width',
			array(
				'label'      => __( 'Border Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 10 ) ),
				'default'    => array( 'size' => 0, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-sticky-video__close' => 'border-style: solid; border-width: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'close_shadow',
				'selector' => '{{WRAPPER}} .eap-sticky-video__close',
			)
		);

		$this->start_controls_tabs( 'close_state_tabs' );

		$this->start_controls_tab( 'close_tab_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'close_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-sticky-video__close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'close_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-sticky-video__close' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'close_border_color',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-sticky-video__close' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'close_tab_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'close_color_hover',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-sticky-video__close:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'close_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-sticky-video__close:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'close_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-sticky-video__close:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* =====================================================================
	 * VIDEO SOURCES
	 * ================================================================== */

	protected function get_youtube_id( $url ) {
		$url = trim( (string) $url );
		if ( preg_match( '~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|v/|shorts/|live/))([A-Za-z0-9_-]{11})~', $url, $m ) ) {
			return $m[1];
		}
		if ( preg_match( '~^[A-Za-z0-9_-]{11}$~', $url ) ) {
			return $url;
		}
		return '';
	}

	protected function get_vimeo_id( $url ) {
		$url = trim( (string) $url );
		if ( preg_match( '~vimeo\.com/(?:video/|channels/[A-Za-z0-9]+/|groups/[A-Za-z0-9]+/videos/)?(\d+)~', $url, $m ) ) {
			return $m[1];
		}
		if ( preg_match( '~^\d+$~', $url ) ) {
			return $url;
		}
		return '';
	}

	protected function aspect_ratio_value( $key ) {
		$map = array(
			'169' => '16 / 9',
			'43'  => '4 / 3',
			'219' => '21 / 9',
			'11'  => '1 / 1',
			'916' => '9 / 16',
		);
		return $map[ $key ] ?? '16 / 9';
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$source   = ! empty( $settings['video_source'] ) ? $settings['video_source'] : 'youtube';

		$is_edit  = Plugin::$instance->editor->is_edit_mode();
		$autoplay = ! $is_edit && 'yes' === ( $settings['autoplay'] ?? '' );
		$mute     = 'yes' === ( $settings['mute'] ?? '' ) || $autoplay;
		$loop     = 'yes' === ( $settings['loop'] ?? '' );
		$controls = 'yes' === ( $settings['controls'] ?? 'yes' );
		$start    = isset( $settings['start_time'] ) && '' !== $settings['start_time'] ? absint( $settings['start_time'] ) : 0;

		$media_html = '';
		if ( 'youtube' === $source ) {
			$media_html = $this->render_youtube( $settings, compact( 'autoplay', 'mute', 'loop', 'controls', 'start' ) );
		} elseif ( 'vimeo' === $source ) {
			$media_html = $this->render_vimeo( $settings, compact( 'autoplay', 'mute', 'loop', 'controls' ) );
		} else {
			$media_html = $this->render_hosted( $settings, compact( 'autoplay', 'mute', 'loop', 'controls' ) );
		}

		if ( '' === $media_html ) {
			if ( $is_edit ) {
				echo '<div class="eap-sticky-video__placeholder">' . esc_html__( 'Add a valid video URL / file to preview the Sticky Video.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$sticky   = 'yes' === ( $settings['sticky_enable'] ?? 'yes' );
		$position = ! empty( $settings['sticky_position'] ) ? $settings['sticky_position'] : 'bottom-right';
		$animation = ! empty( $settings['sticky_animation'] ) ? $settings['sticky_animation'] : 'slide';
		$ar       = $this->aspect_ratio_value( $settings['aspect_ratio'] ?? '169' );

		$classes = array(
			'eap-sticky-video',
			'eap-sticky-video--' . $source,
			'eap-sticky-video--pos-' . $position,
			'eap-sticky-video--anim-' . $animation,
		);

		$this->add_render_attribute(
			'video',
			array(
				'class'                 => $classes,
				'style'                 => '--eap-sv-ar: ' . $ar . ';',
				'data-eap-sticky-video' => 'true',
				'data-eap-source'       => $source,
				'data-eap-sticky'       => $sticky ? 'on' : 'off',
				'data-eap-float-playing' => 'yes' === ( $settings['float_when_playing'] ?? 'yes' ) ? 'yes' : 'no',
			)
		);

		$show_close = $sticky && 'yes' === ( $settings['show_close'] ?? 'yes' );
		?>
		<div class="eap-sticky-video__wrap">
			<div <?php $this->print_render_attribute_string( 'video' ); ?>>
				<div class="eap-sticky-video__inner">
					<div class="eap-sticky-video__frame">
						<?php echo $media_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<?php if ( $show_close ) : ?>
						<span class="eap-sticky-video__close" data-eap-sv-close role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Close sticky video', 'elementor-animatepro' ); ?>">
							<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L12 14.83l-6.29 6.29-1.42-1.42 6.3-6.29-6.3-6.29 1.42-1.42L12 11.17l6.29-6.29z"/></svg>
						</span>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render_youtube( $settings, $opts ) {
		$id = $this->get_youtube_id( $settings['youtube_url'] ?? '' );
		if ( '' === $id ) {
			return '';
		}

		$params = array(
			'enablejsapi'    => 1,
			'rel'            => 0,
			'playsinline'    => 1,
			'modestbranding' => 1,
			'origin'         => home_url(),
		);
		if ( $opts['autoplay'] ) {
			$params['autoplay'] = 1;
		}
		if ( $opts['mute'] ) {
			$params['mute'] = 1;
		}
		if ( ! $opts['controls'] ) {
			$params['controls'] = 0;
		}
		if ( $opts['loop'] ) {
			$params['loop']     = 1;
			$params['playlist'] = $id;
		}
		if ( $opts['start'] > 0 ) {
			$params['start'] = $opts['start'];
		}

		$src = 'https://www.youtube.com/embed/' . rawurlencode( $id ) . '?' . http_build_query( $params );

		return sprintf(
			'<iframe class="eap-sticky-video__media" src="%s" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen title="%s"></iframe>',
			esc_url( $src ),
			esc_attr__( 'YouTube video player', 'elementor-animatepro' )
		);
	}

	protected function render_vimeo( $settings, $opts ) {
		$id = $this->get_vimeo_id( $settings['vimeo_url'] ?? '' );
		if ( '' === $id ) {
			return '';
		}

		$params = array(
			'title'    => 0,
			'byline'   => 0,
			'portrait' => 0,
			'dnt'      => 1,
		);
		if ( $opts['autoplay'] ) {
			$params['autoplay'] = 1;
		}
		if ( $opts['mute'] ) {
			$params['muted'] = 1;
		}
		if ( $opts['loop'] ) {
			$params['loop'] = 1;
		}
		if ( ! $opts['controls'] ) {
			$params['controls'] = 0;
		}

		$src = 'https://player.vimeo.com/video/' . rawurlencode( $id ) . '?' . http_build_query( $params );

		return sprintf(
			'<iframe class="eap-sticky-video__media" src="%s" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen title="%s"></iframe>',
			esc_url( $src ),
			esc_attr__( 'Vimeo video player', 'elementor-animatepro' )
		);
	}

	protected function render_hosted( $settings, $opts ) {
		$url = '';
		if ( ! empty( $settings['hosted_external'] ) ) {
			$url = trim( (string) $settings['hosted_external'] );
		} elseif ( ! empty( $settings['hosted_video']['url'] ) ) {
			$url = $settings['hosted_video']['url'];
		}

		if ( '' === $url ) {
			return '';
		}

		$attrs = array( 'class="eap-sticky-video__media"', 'playsinline' );
		if ( $opts['controls'] ) {
			$attrs[] = 'controls';
		}
		if ( $opts['autoplay'] ) {
			$attrs[] = 'autoplay';
		}
		if ( $opts['mute'] ) {
			$attrs[] = 'muted';
		}
		if ( $opts['loop'] ) {
			$attrs[] = 'loop';
		}
		if ( ! empty( $settings['poster']['url'] ) ) {
			$attrs[] = 'poster="' . esc_url( $settings['poster']['url'] ) . '"';
		}

		return sprintf(
			'<video %s src="%s"></video>',
			implode( ' ', $attrs ),
			esc_url( $url )
		);
	}
}
