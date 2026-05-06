<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;

class EAP_Widget_Parallax_Sections extends EAP_Widget_Base {

	protected function get_parallax_image_url( $image ) {
		$image_url = ! empty( $image['id'] ) ? wp_get_attachment_image_url( $image['id'], 'full' ) : '';
		if ( empty( $image_url ) && ! empty( $image['url'] ) ) {
			$image_url = $image['url'];
		}

		return $image_url ? $image_url : Utils::get_placeholder_image_src();
	}

	protected function get_youtube_embed_url( $url ) {
		$video_id = '';
		$parts    = wp_parse_url( $url );

		if ( empty( $parts['host'] ) ) {
			return '';
		}

		$host = strtolower( $parts['host'] );
		if ( false !== strpos( $host, 'youtu.be' ) && ! empty( $parts['path'] ) ) {
			$video_id = trim( $parts['path'], '/' );
		} elseif ( false !== strpos( $host, 'youtube.com' ) ) {
			if ( ! empty( $parts['query'] ) ) {
				parse_str( $parts['query'], $query_args );
				if ( ! empty( $query_args['v'] ) ) {
					$video_id = $query_args['v'];
				}
			}

			if ( empty( $video_id ) && ! empty( $parts['path'] ) && false !== strpos( $parts['path'], '/embed/' ) ) {
				$segments = explode( '/embed/', $parts['path'] );
				$video_id = ! empty( $segments[1] ) ? trim( $segments[1], '/' ) : '';
			}
		}

		if ( empty( $video_id ) ) {
			return '';
		}

		return sprintf(
			'https://www.youtube.com/embed/%1$s?autoplay=1&mute=1&controls=0&loop=1&playlist=%1$s&playsinline=1&rel=0&modestbranding=1',
			rawurlencode( $video_id )
		);
	}

	protected function get_vimeo_embed_url( $url ) {
		$parts = wp_parse_url( $url );
		if ( empty( $parts['host'] ) || false === strpos( strtolower( $parts['host'] ), 'vimeo.com' ) || empty( $parts['path'] ) ) {
			return '';
		}

		$path     = trim( $parts['path'], '/' );
		$segments = explode( '/', $path );
		$video_id = '';

		foreach ( array_reverse( $segments ) as $segment ) {
			if ( ctype_digit( $segment ) ) {
				$video_id = $segment;
				break;
			}
		}

		if ( empty( $video_id ) ) {
			return '';
		}

		return sprintf(
			'https://player.vimeo.com/video/%1$s?background=1&autoplay=1&muted=1&loop=1&byline=0&title=0&portrait=0',
			rawurlencode( $video_id )
		);
	}

	protected function get_parallax_external_embed_url( $url ) {
		$youtube_url = $this->get_youtube_embed_url( $url );
		if ( ! empty( $youtube_url ) ) {
			return $youtube_url;
		}

		$vimeo_url = $this->get_vimeo_embed_url( $url );
		if ( ! empty( $vimeo_url ) ) {
			return $vimeo_url;
		}

		return '';
	}

	protected function is_direct_video_file_url( $url ) {
		$path = wp_parse_url( $url, PHP_URL_PATH );
		if ( empty( $path ) ) {
			return false;
		}

		$extension = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
		return in_array( $extension, array( 'mp4', 'webm', 'ogg', 'ogv', 'mov', 'm4v' ), true );
	}

	protected function get_parallax_video_markup( $video, $title = '' ) {
		$video_url = ! empty( $video['url'] ) ? $video['url'] : '';
		if ( empty( $video_url ) ) {
			return '';
		}

		if ( $this->is_direct_video_file_url( $video_url ) ) {
			return sprintf(
				'<video class="eap-parallax-section__video" autoplay muted loop playsinline><source src="%1$s" type="%2$s" /></video>',
				esc_url( $video_url ),
				esc_attr( ! empty( $video['mime_type'] ) ? $video['mime_type'] : 'video/mp4' )
			);
		}

		$embed_url = $this->get_parallax_external_embed_url( $video_url );
		if ( ! empty( $embed_url ) ) {
			return sprintf(
				'<iframe class="eap-parallax-section__iframe" src="%1$s" title="%2$s" loading="lazy" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>',
				esc_url( $embed_url ),
				esc_attr( $title ? $title : __( 'Parallax background video', 'elementor-animatepro' ) )
			);
		}

		return '';
	}

	protected function get_parallax_media_markup( $media_type, $image, $video, $title = '' ) {
		if ( 'video' === $media_type ) {
			$video_markup = $this->get_parallax_video_markup( $video, $title );
			if ( ! empty( $video_markup ) ) {
				return $video_markup;
			}
		}

		return sprintf(
			'<img class="eap-parallax-section__bg" src="%1$s" alt="%2$s" />',
			esc_url( $this->get_parallax_image_url( $image ) ),
			esc_attr( $title ? $title : __( 'Parallax section background', 'elementor-animatepro' ) )
		);
	}

	protected function get_parallax_template_options() {
		$options = array();
		$posts   = get_posts(
			array(
				'post_type'      => array( 'elementor_library', 'eap_template' ),
				'post_status'    => array( 'publish', 'draft', 'private' ),
				'posts_per_page' => 200,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		foreach ( $posts as $post ) {
			$options[ $post->ID ] = sprintf(
				'%1$s (%2$s)',
				$post->post_title ? $post->post_title : __( '(no title)', 'elementor-animatepro' ),
				ucfirst( str_replace( '_', ' ', $post->post_type ) )
			);
		}

		return $options;
	}

	protected function get_parallax_page_options() {
		$options = array();
		$posts   = get_posts(
			array(
				'post_type'      => array( 'page', 'post' ),
				'post_status'    => array( 'publish', 'draft', 'private' ),
				'posts_per_page' => 200,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		foreach ( $posts as $post ) {
			$options[ $post->ID ] = sprintf(
				'%1$s (%2$s)',
				$post->post_title ? $post->post_title : __( '(no title)', 'elementor-animatepro' ),
				ucfirst( str_replace( '_', ' ', $post->post_type ) )
			);
		}

		return $options;
	}

	protected function get_split_text_markup( $text, $split_by = 'word' ) {
		$text = (string) $text;
		if ( '' === trim( $text ) ) {
			return '';
		}

		$segments = array();

		if ( 'character' === $split_by ) {
			$chars = preg_split( '//u', $text, -1, PREG_SPLIT_NO_EMPTY );
			foreach ( $chars as $char ) {
				if ( preg_match( '/\s/u', $char ) ) {
					$segments[] = '<span class="eap-split-space">&nbsp;</span>';
				} else {
					$segments[] = '<span class="eap-split-piece">' . esc_html( $char ) . '</span>';
				}
			}
		} elseif ( 'line' === $split_by ) {
			$lines = preg_split( "/\r\n|\r|\n/", $text );
			if ( empty( $lines ) ) {
				$lines = array( $text );
			}

			foreach ( $lines as $line ) {
				$segments[] = '<span class="eap-split-piece">' . esc_html( $line ) . '</span>';
			}
		} else {
			$words = preg_split( '/\s+/', trim( $text ) );
			foreach ( $words as $word ) {
				$segments[] = '<span class="eap-split-piece">' . esc_html( $word ) . '</span>';
			}
		}

		if ( 'line' === $split_by ) {
			return '<span class="eap-split-lines">' . implode( '', $segments ) . '</span>';
		}

		return implode( '', $segments );
	}

	public function get_name() {
		return 'eap-parallax-sections';
	}

	public function get_title() {
		return __( 'Parallax Sections', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-slides';
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'parallax-sections' );
	}

	public function get_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-parallax-sections-script',
		);
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'background_source',
			array(
				'label'   => __( 'Background Source', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'shared',
				'options' => array(
					'shared'   => __( 'Same Background For All Sections', 'elementor-animatepro' ),
					'separate' => __( 'Separate Background Per Section', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'shared_media_type',
			array(
				'label'     => __( 'Shared Media Type', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'image',
				'options'   => array(
					'image' => __( 'Image', 'elementor-animatepro' ),
					'video' => __( 'Video', 'elementor-animatepro' ),
				),
				'condition' => array(
					'background_source' => 'shared',
				),
			)
		);

		$this->add_control(
			'shared_background_image',
			array(
				'label'     => __( 'Shared Background Image', 'elementor-animatepro' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'background_source' => 'shared',
					'shared_media_type' => 'image',
				),
			)
		);

		$this->add_control(
			'shared_background_video',
			array(
				'label'       => __( 'Shared Background Video', 'elementor-animatepro' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'video' ),
				'condition'   => array(
					'background_source' => 'shared',
					'shared_media_type' => 'video',
				),
			)
		);

		$this->add_responsive_control(
			'section_min_height',
			array(
				'label'      => __( 'Sections Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 240,
						'max' => 1200,
					),
					'vh' => array(
						'min' => 30,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'vh',
					'size' => 70,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-parallax-sections'          => '--eap-parallax-panel-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-parallax-sections__viewport' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'panel_transition',
			array(
				'label'   => __( 'Section Transition', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide-up',
				'options' => array(
					'fade'       => __( 'Fade', 'elementor-animatepro' ),
					'slide-up'   => __( 'Slide Up', 'elementor-animatepro' ),
					'slide-down' => __( 'Slide Down', 'elementor-animatepro' ),
					'scale'      => __( 'Scale', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'panel_transition_distance',
			array(
				'label'      => __( 'Transition Distance', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 240,
					),
					'%' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 72,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-parallax-sections' => '--eap-parallax-transition-distance: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'panel_transition_duration',
			array(
				'label'      => __( 'Transition Duration (ms)', 'elementor-animatepro' ),
				'type'       => Controls_Manager::NUMBER,
				'default'    => 520,
				'min'        => 100,
				'max'        => 3000,
				'step'       => 10,
				'selectors'  => array(
					'{{WRAPPER}} .eap-parallax-sections' => '--eap-parallax-transition-duration: {{VALUE}}ms;',
				),
			)
		);

		$this->add_responsive_control(
			'pin_top_offset',
			array(
				'label'      => __( 'Pin Top Offset', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 240,
					),
					'vh' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-parallax-sections'           => '--eap-parallax-pin-offset: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-parallax-sections__viewport' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sections_gap',
			array(
				'label'      => __( 'Sections Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-parallax-sections' => '--eap-parallax-stack-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_sections',
			array(
				'label' => __( 'Sections', 'elementor-animatepro' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'content_source',
			array(
				'label'   => __( 'Content Source', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'direct',
				'options' => array(
					'direct'   => __( 'Direct Content', 'elementor-animatepro' ),
					'template' => __( 'Template', 'elementor-animatepro' ),
					'page'     => __( 'Page / Post', 'elementor-animatepro' ),
					'shortcode' => __( 'Shortcode', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'eyebrow',
			array(
				'label'       => __( 'Pre Heading', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Parallax Story', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array(
					'content_source' => 'direct',
				),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Section Title', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array(
					'content_source' => 'direct',
				),
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label'       => __( 'Description', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Add immersive copy for this parallax section and let the motion build visual rhythm while users scroll.', 'elementor-animatepro' ),
				'rows'        => 4,
				'condition'   => array(
					'content_source' => 'direct',
				),
			)
		);

		$repeater->add_control(
			'button_text',
			array(
				'label'       => __( 'Button Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Explore More', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array(
					'content_source' => 'direct',
				),
			)
		);

		$repeater->add_control(
			'button_link',
			array(
				'label'         => __( 'Button Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url' => '#',
				),
				'condition'     => array(
					'content_source' => 'direct',
				),
			)
		);

		$repeater->add_control(
			'template_post_id',
			array(
				'label'       => __( 'Choose Template', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->get_parallax_template_options(),
				'label_block' => true,
				'multiple'    => false,
				'condition'   => array(
					'content_source' => 'template',
				),
			)
		);

		$repeater->add_control(
			'page_post_id',
			array(
				'label'       => __( 'Choose Page / Post', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->get_parallax_page_options(),
				'label_block' => true,
				'multiple'    => false,
				'condition'   => array(
					'content_source' => 'page',
				),
			)
		);

		$repeater->add_control(
			'shortcode_content',
			array(
				'label'       => __( 'Shortcode', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'placeholder' => __( '[your_shortcode]', 'elementor-animatepro' ),
				'condition'   => array(
					'content_source' => 'shortcode',
				),
			)
		);

		$repeater->add_control(
			'section_media_type',
			array(
				'label'   => __( 'Section Media Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => array(
					'image' => __( 'Image', 'elementor-animatepro' ),
					'video' => __( 'Video', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'section_background_image',
			array(
				'label'   => __( 'Section Background Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'section_media_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'section_background_video',
			array(
				'label'       => __( 'Section Background Video', 'elementor-animatepro' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'video' ),
				'condition'   => array(
					'section_media_type' => 'video',
				),
			)
		);

		$repeater->add_control(
			'content_position',
			array(
				'label'   => __( 'Content Vertical Position', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'center',
				'options' => array(
					'flex-start' => __( 'Top', 'elementor-animatepro' ),
					'center'     => __( 'Center', 'elementor-animatepro' ),
					'flex-end'   => __( 'Bottom', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'text_align',
			array(
				'label'   => __( 'Text Align', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'   => __( 'Left', 'elementor-animatepro' ),
					'center' => __( 'Center', 'elementor-animatepro' ),
					'right'  => __( 'Right', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'content_animation',
			array(
				'label'   => __( 'Content Animation', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fade-up',
				'options' => array(
					'none'       => __( 'None', 'elementor-animatepro' ),
					'fade-up'    => __( 'Fade Up', 'elementor-animatepro' ),
					'fade-down'  => __( 'Fade Down', 'elementor-animatepro' ),
					'fade-left'  => __( 'Fade Left', 'elementor-animatepro' ),
					'fade-right' => __( 'Fade Right', 'elementor-animatepro' ),
					'fade-in'    => __( 'Fade In', 'elementor-animatepro' ),
					'zoom-in'    => __( 'Zoom In', 'elementor-animatepro' ),
					'split-text' => __( 'Split Text', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'split_type',
			array(
				'label'     => __( 'Split By', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'word',
				'options'   => array(
					'word'      => __( 'Word', 'elementor-animatepro' ),
					'character' => __( 'Character', 'elementor-animatepro' ),
					'line'      => __( 'Line', 'elementor-animatepro' ),
				),
				'condition' => array(
					'content_animation' => 'split-text',
				),
			)
		);

		$repeater->add_control(
			'split_direction',
			array(
				'label'     => __( 'Split Direction', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom',
				'options'   => array(
					'bottom' => __( 'Bottom to Top', 'elementor-animatepro' ),
					'top'    => __( 'Top to Bottom', 'elementor-animatepro' ),
					'left'   => __( 'Left to Right', 'elementor-animatepro' ),
					'right'  => __( 'Right to Left', 'elementor-animatepro' ),
				),
				'condition' => array(
					'content_animation' => 'split-text',
				),
			)
		);

		$repeater->add_control(
			'content_duration',
			array(
				'label'   => __( 'Animation Duration (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 850,
				'min'     => 0,
				'step'    => 10,
			)
		);

		$repeater->add_control(
			'content_delay',
			array(
				'label'   => __( 'Animation Delay (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'step'    => 10,
			)
		);

		$repeater->add_control(
			'parallax_effect',
			array(
				'label'   => __( 'Parallax Effect', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'up',
				'options' => array(
					'up'       => __( 'Move Up', 'elementor-animatepro' ),
					'down'     => __( 'Move Down', 'elementor-animatepro' ),
					'left'     => __( 'Move Left', 'elementor-animatepro' ),
					'right'    => __( 'Move Right', 'elementor-animatepro' ),
					'zoom-in'  => __( 'Zoom In', 'elementor-animatepro' ),
					'zoom-out' => __( 'Zoom Out', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'parallax_speed',
			array(
				'label'   => __( 'Parallax Speed', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0.45,
				'min'     => 0,
				'max'     => 2,
				'step'    => 0.05,
			)
		);

		$repeater->add_control(
			'overlay_color',
			array(
				'label'   => __( 'Overlay Color', 'elementor-animatepro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => 'rgba(15, 23, 42, 0.45)',
			)
		);

		$this->add_control(
			'sections',
			array(
				'label'       => __( 'Parallax Sections', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title || "Section" }}}',
				'default'     => array(
					array(
						'eyebrow'            => __( 'Shared Atmosphere', 'elementor-animatepro' ),
						'title'              => __( 'Build smooth layered sections', 'elementor-animatepro' ),
						'description'        => __( 'Use one shared visual or give each panel its own media while keeping the scroll feel calm and controlled.', 'elementor-animatepro' ),
						'button_text'        => __( 'See Details', 'elementor-animatepro' ),
						'content_animation'  => 'fade-up',
						'parallax_effect'    => 'up',
						'parallax_speed'     => 0.45,
					),
					array(
						'eyebrow'            => __( 'Per-Section Motion', 'elementor-animatepro' ),
						'title'              => __( 'Mix directions and depth', 'elementor-animatepro' ),
						'description'        => __( 'Each section can move in a different direction or zoom style, while the content keeps its own scroll-triggered entrance motion.', 'elementor-animatepro' ),
						'button_text'        => __( 'View Motion', 'elementor-animatepro' ),
						'content_animation'  => 'fade-left',
						'parallax_effect'    => 'right',
						'parallax_speed'     => 0.35,
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'section_style_sections',
			array(
				'label' => __( 'Sections', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'fallback_background_color',
			array(
				'label'     => __( 'Fallback Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => array(
					'{{WRAPPER}} .eap-parallax-sections' => '--eap-parallax-fallback-bg: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'section_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-parallax-sections, {{WRAPPER}} .eap-parallax-sections__viewport, {{WRAPPER}} .eap-parallax-section, {{WRAPPER}} .eap-parallax-sections__shared-media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'section_border',
				'selector' => '{{WRAPPER}} .eap-parallax-sections__viewport',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'section_shadow',
				'selector' => '{{WRAPPER}} .eap-parallax-sections__viewport',
			)
		);

		$this->add_responsive_control(
			'content_width',
			array(
				'label'      => __( 'Content Max Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 240,
						'max' => 1200,
					),
					'%' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 640,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-parallax-section__content' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Content Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'vw' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-parallax-section__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					'top'      => 56,
					'right'    => 56,
					'bottom'   => 56,
					'left'     => 56,
					'unit'     => 'px',
					'isLinked' => false,
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_eyebrow',
			array(
				'label' => __( 'Pre Heading', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f8fafc',
				'selectors' => array(
					'{{WRAPPER}} .eap-parallax-section__eyebrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .eap-parallax-section__eyebrow',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			array(
				'label' => __( 'Title', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-parallax-section__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-parallax-section__title',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_description',
			array(
				'label' => __( 'Description', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.82)',
				'selectors' => array(
					'{{WRAPPER}} .eap-parallax-section__description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .eap-parallax-section__description',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => __( 'Button', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => array(
					'{{WRAPPER}} .eap-parallax-section__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-parallax-section__button' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_hover_text_color',
			array(
				'label'     => __( 'Hover Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-parallax-section__button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_hover_background',
			array(
				'label'     => __( 'Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => array(
					'{{WRAPPER}} .eap-parallax-section__button:hover' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .eap-parallax-section__button',
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-parallax-section__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$sections = ! empty( $settings['sections'] ) && is_array( $settings['sections'] ) ? $settings['sections'] : array();

		if ( empty( $sections ) ) {
			return;
		}

		$this->add_render_attribute(
			'wrapper',
			array(
				'class' => array(
					'eap-widget',
					'eap-parallax-sections',
					'eap-parallax-sections--' . sanitize_html_class( $settings['background_source'] ),
				),
				'data-eap-parallax-stack'      => 'true',
				'data-eap-panel-transition'    => ! empty( $settings['panel_transition'] ) ? $settings['panel_transition'] : 'slide-up',
				'style'                        => sprintf( '--eap-parallax-count:%d;', count( $sections ) ),
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="eap-parallax-sections__viewport">
				<?php if ( 'shared' === $settings['background_source'] ) : ?>
					<?php
					$shared_media_type = ! empty( $settings['shared_media_type'] ) ? $settings['shared_media_type'] : 'image';
					$shared_image      = ! empty( $settings['shared_background_image'] ) ? $settings['shared_background_image'] : array();
					$shared_video      = ! empty( $settings['shared_background_video'] ) ? $settings['shared_background_video'] : array();
					?>
					<div class="eap-parallax-sections__shared-media">
						<?php echo $this->get_parallax_media_markup( $shared_media_type, $shared_image, $shared_video, __( 'Shared parallax background', 'elementor-animatepro' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>
				<?php foreach ( $sections as $index => $section ) : ?>
					<?php
					$media_type   = ( 'shared' === $settings['background_source'] ) ? $settings['shared_media_type'] : $section['section_media_type'];
					$image        = ( 'shared' === $settings['background_source'] ) ? $settings['shared_background_image'] : $section['section_background_image'];
					$video        = ( 'shared' === $settings['background_source'] ) ? $settings['shared_background_video'] : $section['section_background_video'];
				$section_key  = 'section_' . $index;
				$content_key  = 'section_content_' . $index;
				$effect       = ! empty( $section['parallax_effect'] ) ? $section['parallax_effect'] : 'up';
				$speed        = isset( $section['parallax_speed'] ) ? (float) $section['parallax_speed'] : 0.45;
				$anim_class   = 'eap-motion';
				$animation    = ! empty( $section['content_animation'] ) ? $section['content_animation'] : 'fade-up';
				$split_type   = ! empty( $section['split_type'] ) ? $section['split_type'] : 'word';
				$split_dir    = ! empty( $section['split_direction'] ) ? $section['split_direction'] : 'bottom';
				$title_key    = 'section_title_' . $index;
				$content_source = ! empty( $section['content_source'] ) ? $section['content_source'] : 'direct';
				$dynamic_markup = '';

				if ( 'template' === $content_source && ! empty( $section['template_post_id'] ) ) {
					$template_post = get_post( (int) $section['template_post_id'] );
					if ( $template_post instanceof WP_Post ) {
						$dynamic_markup = apply_filters( 'the_content', $template_post->post_content );
					}
				} elseif ( 'page' === $content_source && ! empty( $section['page_post_id'] ) ) {
					$page_post = get_post( (int) $section['page_post_id'] );
					if ( $page_post instanceof WP_Post ) {
						$dynamic_markup = apply_filters( 'the_content', $page_post->post_content );
					}
				} elseif ( 'shortcode' === $content_source && ! empty( $section['shortcode_content'] ) ) {
					$dynamic_markup = do_shortcode( $section['shortcode_content'] );
				}

				if ( 'fade-left' === $animation ) {
					$anim_class .= ' eap-motion-slide-left';
				} elseif ( 'fade-right' === $animation ) {
					$anim_class .= ' eap-motion-slide-right';
				} elseif ( 'fade-down' === $animation ) {
					$anim_class .= ' eap-motion-slide-down';
				} elseif ( 'fade-in' === $animation ) {
					$anim_class .= ' eap-motion-fade-in';
				} elseif ( 'zoom-in' === $animation ) {
					$anim_class .= ' eap-motion-zoom-in';
				}

				if ( 'none' === $animation || 'split-text' === $animation ) {
					$anim_class = '';
				}

				$overlay = ! empty( $section['overlay_color'] ) ? $section['overlay_color'] : 'rgba(15, 23, 42, 0.45)';

				$this->add_render_attribute(
					$section_key,
					array(
						'class'                     => array(
							'eap-parallax-section',
							'eap-parallax-section--' . sanitize_html_class( $media_type ),
							( 'shared' === $settings['background_source'] ) ? 'eap-parallax-section--shared-media' : 'eap-parallax-section--own-media',
						),
						'data-eap-parallax-section' => 'true',
						'data-eap-parallax-effect'  => esc_attr( $effect ),
						'data-eap-parallax-speed'   => esc_attr( (string) $speed ),
						'data-eap-parallax-index'   => (string) $index,
						'style'                     => sprintf(
							'--eap-parallax-overlay:%1$s;--eap-parallax-content-align:%2$s;--eap-parallax-text-align:%3$s;--eap-parallax-layer:%4$d;',
							esc_attr( $overlay ),
							esc_attr( ! empty( $section['content_position'] ) ? $section['content_position'] : 'center' ),
							esc_attr( ! empty( $section['text_align'] ) ? $section['text_align'] : 'left' ),
							(int) ( $index + 1 )
						),
					)
				);

				$this->add_render_attribute(
					$content_key,
					array(
						'class' => array_filter(
							array(
								'eap-parallax-section__content',
								( 'direct' !== $content_source ) ? 'eap-parallax-section__content--dynamic' : '',
								$anim_class,
							)
						),
						'data-eap-parallax-content' => 'true',
					)
				);

				if ( ! empty( $section['content_duration'] ) ) {
					$this->add_render_attribute( $content_key, 'data-eap-duration', absint( $section['content_duration'] ) );
				}

				if ( isset( $section['content_delay'] ) ) {
					$this->add_render_attribute( $content_key, 'data-eap-delay', absint( $section['content_delay'] ) );
				}

				if ( 'split-text' === $animation ) {
					$this->add_render_attribute(
						$title_key,
						array(
							'class'                  => array(
								'eap-parallax-section__title',
								'eap-animated-text',
							),
							'data-eap-split-trigger' => 'scroll',
							'data-eap-split-direction' => esc_attr( $split_dir ),
							'data-eap-split-type'    => esc_attr( $split_type ),
							'data-eap-duration'      => absint( ! empty( $section['content_duration'] ) ? $section['content_duration'] : 850 ),
							'data-eap-delay'         => absint( isset( $section['content_delay'] ) ? $section['content_delay'] : 0 ),
							'data-eap-split-duration' => absint( ! empty( $section['content_duration'] ) ? $section['content_duration'] : 850 ),
							'data-eap-split-stagger' => ( 'character' === $split_type ) ? 28 : ( 'line' === $split_type ? 140 : 70 ),
						)
					);
				}

				$button_key = 'button_' . $index;
				if ( ! empty( $section['button_link']['url'] ) ) {
					$this->add_link_attributes( $button_key, $section['button_link'] );
					$this->add_render_attribute( $button_key, 'class', 'eap-parallax-section__button' );
				}
				?>
					<section <?php $this->print_render_attribute_string( $section_key ); ?>>
						<?php if ( 'shared' !== $settings['background_source'] ) : ?>
							<div class="eap-parallax-section__media">
								<?php echo $this->get_parallax_media_markup( $media_type, $image, $video, ! empty( $section['title'] ) ? $section['title'] : __( 'Parallax section background', 'elementor-animatepro' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						<?php endif; ?>
						<span class="eap-parallax-section__overlay" aria-hidden="true"></span>
						<div class="eap-parallax-section__inner">
							<div <?php $this->print_render_attribute_string( $content_key ); ?>>
								<?php if ( 'direct' !== $content_source ) : ?>
									<div class="eap-parallax-section__dynamic-content"><?php echo do_shortcode( $dynamic_markup ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
								<?php else : ?>
									<?php if ( ! empty( $section['eyebrow'] ) ) : ?>
										<div class="eap-parallax-section__eyebrow"><?php echo esc_html( $section['eyebrow'] ); ?></div>
									<?php endif; ?>
									<?php if ( ! empty( $section['title'] ) ) : ?>
										<?php if ( 'split-text' === $animation ) : ?>
											<h2 <?php $this->print_render_attribute_string( $title_key ); ?>><?php echo wp_kses_post( $this->get_split_text_markup( $section['title'], $split_type ) ); ?></h2>
										<?php else : ?>
											<h2 class="eap-parallax-section__title"><?php echo esc_html( $section['title'] ); ?></h2>
										<?php endif; ?>
									<?php endif; ?>
									<?php if ( ! empty( $section['description'] ) ) : ?>
										<div class="eap-parallax-section__description"><?php echo wp_kses_post( wpautop( $section['description'] ) ); ?></div>
									<?php endif; ?>
									<?php if ( ! empty( $section['button_text'] ) && ! empty( $section['button_link']['url'] ) ) : ?>
										<a <?php $this->print_render_attribute_string( $button_key ); ?>><?php echo esc_html( $section['button_text'] ); ?></a>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						</div>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
