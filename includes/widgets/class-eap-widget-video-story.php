<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

/**
 * Video Story — a query-driven grid of portrait "story" cards (poster + gradient
 * + category badge + title + byline + play/duration). Each card carries a
 * self-hosted video:
 *
 *   - hover  -> the video plays as a MUTED background preview (overlays stay);
 *   - click the play button -> it UNMUTES and plays with sound, the overlays
 *     hide, native controls + a close × appear; on end / close it resets.
 *
 * Query uses the shared EAP_Posts_Query engine. Source defaults to the plugin's
 * `eap_video_story` custom post type (enable it on the Extensions page); each
 * story's video comes from the `_eap_video_url` meta the CPT stores, with a
 * widget-wide Fallback Video for anything missing — so it also works against
 * regular posts. The card markup is bespoke (video/poster/overlays), NOT the
 * shared render_card. CSS + JS.
 */
class EAP_Widget_Video_Story extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-video-story';
	}

	public function get_title() {
		return __( 'Video Story', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-play';
	}

	public function get_keywords() {
		return array( 'video', 'story', 'stories', 'reel', 'posts', 'hover', 'dynamic' );
	}

	public function get_style_depends() {
		return array( 'eap-core', 'eap-video-story', 'elementor-icons-fa-solid' );
	}

	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-video-story-script' );
	}

	protected function register_controls() {
		// Content.
		$this->register_query_section();
		$this->register_video_section();
		$this->register_layout_section();
		$this->register_settings_section();
		$this->register_title_content();
		$this->register_excerpt_content();
		$this->register_taxonomy_content();
		$this->register_meta_content();

		// Style.
		$this->register_layout_style();
		$this->register_video_style();
		$this->register_content_style();
		$this->register_title_style();
		$this->register_excerpt_style();
		$this->register_taxonomy_style();
		$this->register_meta_style();
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
			'source',
			array(
				'label'       => __( 'Source', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'eap_video_story',
				'options'     => EAP_Posts_Query::get_post_type_options(),
				'description' => __( 'Enable the Video Story post type on the plugin\'s Extensions page to fetch it here.', 'elementor-animatepro' ),
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
	 * CONTENT — VIDEO
	 * ================================================================== */

	protected function register_video_section() {
		$this->start_controls_section(
			'section_video',
			array(
				'label' => __( 'Video', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'fallback_video',
			array(
				'label'       => __( 'Fallback Video', 'elementor-animatepro' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'video' ),
				'description' => __( 'Used by any card without its own video. Set this to preview the effect immediately (MP4 / WebM).', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'video_field',
			array(
				'label'       => __( 'Video Field (meta key)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '_eap_video_url',
				'placeholder' => '_eap_video_url',
				'description' => __( 'Post-meta key holding a per-post video URL. The Video Story post type stores it in _eap_video_url.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'   => __( 'Poster Image Size', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'large',
				'options' => $this->get_image_size_options(),
			)
		);

		$this->add_control(
			'hover_autoplay',
			array(
				'label'        => __( 'Autoplay Preview on Hover', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_play',
			array(
				'label'        => __( 'Play Button', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_duration',
			array(
				'label'        => __( 'Duration', 'elementor-animatepro' ),
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
	 * CONTENT — LAYOUT (content order + aspect)
	 * ================================================================== */

	protected function register_layout_section() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'aspect_ratio',
			array(
				'label'     => __( 'Aspect Ratio', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '9 / 16',
				'options'   => array(
					'9 / 16' => '9:16 (Portrait)',
					'3 / 4'  => '3:4',
					'4 / 5'  => '4:5',
					'1 / 1'  => '1:1 (Square)',
					'16 / 9' => '16:9 (Landscape)',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-video-story__media' => 'aspect-ratio: {{VALUE}};',
				),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'vs_element',
			array(
				'label'   => __( 'Element', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'title',
				'options' => array(
					'taxonomy' => __( 'Taxonomy Badge', 'elementor-animatepro' ),
					'title'    => __( 'Title', 'elementor-animatepro' ),
					'excerpt'  => __( 'Excerpt', 'elementor-animatepro' ),
					'meta'     => __( 'Meta', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'content_order',
			array(
				'label'       => __( 'Content Order', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'vs_element' => 'taxonomy' ),
					array( 'vs_element' => 'title' ),
					array( 'vs_element' => 'meta' ),
				),
				'title_field' => '{{{ vs_element }}}',
				'description' => __( 'Drag to reorder the card content. Visibility is set under Settings.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — SETTINGS
	 * ================================================================== */

	protected function register_settings_section() {
		$this->start_controls_section(
			'section_settings',
			array(
				'label' => __( 'Settings', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => __( 'Posts Per Page', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 48,
				'default' => 6,
			)
		);

		foreach ( array(
			'show_title'    => array( __( 'Show Title', 'elementor-animatepro' ), 'yes' ),
			'show_excerpt'  => array( __( 'Show Excerpt', 'elementor-animatepro' ), '' ),
			'show_taxonomy' => array( __( 'Show Taxonomy', 'elementor-animatepro' ), 'yes' ),
			'show_meta'     => array( __( 'Show Meta', 'elementor-animatepro' ), 'yes' ),
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
	 * CONTENT — TITLE / EXCERPT / TAXONOMY / META
	 * ================================================================== */

	protected function register_title_content() {
		$this->start_controls_section(
			'section_title',
			array(
				'label'     => __( 'Title', 'elementor-animatepro' ),
				'condition' => array( 'show_title' => 'yes' ),
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

		$this->end_controls_section();
	}

	protected function register_excerpt_content() {
		$this->start_controls_section(
			'section_excerpt',
			array(
				'label'     => __( 'Excerpt', 'elementor-animatepro' ),
				'condition' => array( 'show_excerpt' => 'yes' ),
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label'   => __( 'Excerpt Length (words)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 60,
				'default' => 14,
			)
		);

		$this->end_controls_section();
	}

	protected function register_taxonomy_content() {
		$this->start_controls_section(
			'section_taxonomy',
			array(
				'label'     => __( 'Taxonomy', 'elementor-animatepro' ),
				'condition' => array( 'show_taxonomy' => 'yes' ),
			)
		);

		$this->add_control(
			'badge_taxonomy',
			array(
				'label'   => __( 'Taxonomy', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => $this->get_taxonomy_options(),
			)
		);

		$this->add_control(
			'badge_link',
			array(
				'label'        => __( 'Link to Term', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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

		$this->end_controls_section();
	}

	protected function register_meta_content() {
		$this->start_controls_section(
			'section_meta',
			array(
				'label'     => __( 'Meta', 'elementor-animatepro' ),
				'condition' => array( 'show_meta' => 'yes' ),
			)
		);

		$this->add_control(
			'meta_data',
			array(
				'label'       => __( 'Meta', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'default'     => array( 'author', 'date' ),
				'options'     => array(
					'author' => __( 'Author', 'elementor-animatepro' ),
					'date'   => __( 'Date', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'byline_prefix',
			array(
				'label'   => __( 'Author Prefix', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'By', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE — LAYOUT
	 * ================================================================== */

	protected function register_layout_style() {
		$this->start_controls_section(
			'section_layout_style',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'elementor-animatepro' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => array( 'px' => array( 'min' => 1, 'max' => 6, 'step' => 1 ) ),
				'default'        => array( 'size' => 4 ),
				'tablet_default' => array( 'size' => 2 ),
				'mobile_default' => array( 'size' => 1 ),
				'selectors'      => array(
					'{{WRAPPER}} .eap-video-story__grid' => '--eap-vs-cols: {{SIZE}};',
				),
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label'      => __( 'Columns Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 20, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-video-story__grid' => 'column-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'      => __( 'Rows Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 20, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-video-story__grid' => 'row-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors_dictionary' => array(
					'flex-start' => 'align-items: flex-start; text-align: left;',
					'center'     => 'align-items: center; text-align: center;',
					'flex-end'   => 'align-items: flex-end; text-align: right;',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-video-story__content' => '{{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE — VIDEO (media size + play/duration)
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
			'media_width',
			array(
				'label'       => __( 'Width', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'range'       => array( 'px' => array( 'min' => 100, 'max' => 600 ), '%' => array( 'min' => 20, 'max' => 100 ) ),
				'selectors'   => array(
					'{{WRAPPER}} .eap-video-story__media' => 'max-width: {{SIZE}}{{UNIT}}; margin-inline: auto;',
				),
				'description' => __( 'Optional. Leave empty to fill the column.', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'media_height',
			array(
				'label'       => __( 'Height', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'vh' ),
				'range'       => array( 'px' => array( 'min' => 120, 'max' => 800 ), 'vh' => array( 'min' => 20, 'max' => 100 ) ),
				'selectors'   => array(
					'{{WRAPPER}} .eap-video-story__media' => 'height: {{SIZE}}{{UNIT}}; aspect-ratio: auto;',
				),
				'description' => __( 'Optional. Overrides the aspect ratio when set.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'duration_heading',
			array(
				'label'     => __( 'Play & Duration', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'show_play' => 'yes' ),
			)
		);

		$this->add_control(
			'play_icon',
			array(
				'label'       => __( 'Play Icon', 'elementor-animatepro' ),
				'type'        => Controls_Manager::ICONS,
				'condition'   => array( 'show_play' => 'yes' ),
				'description' => __( 'Leave empty for the built-in play triangle.', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'play_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 40 ) ),
				'default'    => array( 'size' => 16, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-video-story__play' => '--eap-vs-play: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'show_play' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'play_gap',
			array(
				'label'      => __( 'Icon Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 24 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-video-story__play' => 'gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'show_play' => 'yes' ),
			)
		);

		$this->add_control(
			'play_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-story__play' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_play' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'duration_typography',
				'label'     => __( 'Duration Typography', 'elementor-animatepro' ),
				'selector'  => '{{WRAPPER}} .eap-video-story__duration',
				'condition' => array(
					'show_play'     => 'yes',
					'show_duration' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE — CONTENT (card + overlay)
	 * ================================================================== */

	protected function register_content_style() {
		$this->start_controls_section(
			'section_content_style',
			array(
				'label' => __( 'Content', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 18, 'right' => 18, 'bottom' => 18, 'left' => 18, 'unit' => 'px', 'isLinked' => true ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-video-story__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-video-story__media' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eap-video-story__media',
			)
		);

		$this->add_control(
			'scrim_color',
			array(
				'label'     => __( 'Gradient Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.85)',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-story__scrim' => '--eap-vs-scrim: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'scrim_height',
			array(
				'label'      => __( 'Gradient Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array( '%' => array( 'min' => 20, 'max' => 100 ) ),
				'default'    => array( 'size' => 70, 'unit' => '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-video-story__scrim' => '--eap-vs-scrim-h: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE — TITLE / EXCERPT / TAXONOMY / META
	 * ================================================================== */

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
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-story__title, {{WRAPPER}} .eap-video-story__title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-video-story__title',
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
				'default'   => 'rgba(255, 255, 255, 0.85)',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-story__excerpt' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'excerpt_typography',
				'selector' => '{{WRAPPER}} .eap-video-story__excerpt',
			)
		);

		$this->end_controls_section();
	}

	protected function register_taxonomy_style() {
		$this->start_controls_section(
			'section_taxonomy_style',
			array(
				'label'     => __( 'Taxonomy', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_taxonomy' => 'yes' ),
			)
		);

		$this->add_control(
			'badge_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-story__badge' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e11d2a',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-story__badge' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'badge_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 3, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-video-story__badge' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .eap-video-story__badge',
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
				'default'   => 'rgba(255, 255, 255, 0.8)',
				'selectors' => array(
					'{{WRAPPER}} .eap-video-story__meta' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'meta_typography',
				'selector' => '{{WRAPPER}} .eap-video-story__meta',
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
			'post_type'       => ! empty( $settings['source'] ) ? $settings['source'] : 'post',
			'per_page'        => isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 6,
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
				echo '<div class="eap-widget eap-video-story eap-video-story--empty">' . esc_html__( 'No posts found. Enable the Video Story post type and add stories, or choose another Source.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$show = array(
			'title'    => 'yes' === ( $settings['show_title'] ?? 'yes' ),
			'excerpt'  => 'yes' === ( $settings['show_excerpt'] ?? '' ),
			'taxonomy' => 'yes' === ( $settings['show_taxonomy'] ?? 'yes' ),
			'meta'     => 'yes' === ( $settings['show_meta'] ?? 'yes' ),
		);

		$cfg = array(
			'order_els'     => $this->get_content_order( $settings, $show ),
			'taxonomy'      => ! empty( $settings['badge_taxonomy'] ) ? $settings['badge_taxonomy'] : 'category',
			'badge_link'    => 'yes' === ( $settings['badge_link'] ?? 'yes' ),
			'title_tag'     => $this->safe_tag( $settings['title_tag'] ?? 'h3' ),
			'excerpt_len'   => isset( $settings['excerpt_length'] ) ? max( 1, (int) $settings['excerpt_length'] ) : 14,
			'meta_fields'   => isset( $settings['meta_data'] ) ? (array) $settings['meta_data'] : array( 'author', 'date' ),
			'byline_prefix' => isset( $settings['byline_prefix'] ) ? $settings['byline_prefix'] : __( 'By', 'elementor-animatepro' ),
			'video_field'   => isset( $settings['video_field'] ) ? trim( (string) $settings['video_field'] ) : '_eap_video_url',
			'fallback'      => isset( $settings['fallback_video']['url'] ) ? $settings['fallback_video']['url'] : '',
			'img_size'      => ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'large',
			'show_play'     => 'yes' === ( $settings['show_play'] ?? 'yes' ),
			'show_duration' => 'yes' === ( $settings['show_duration'] ?? 'yes' ),
			'play_icon'     => isset( $settings['play_icon'] ) ? $settings['play_icon'] : array(),
		);

		$classes = array( 'eap-widget', 'eap-video-story' );
		if ( 'yes' === ( $settings['hover_autoplay'] ?? 'yes' ) ) {
			$classes[] = 'eap-video-story--hover';
		}
		$this->add_render_attribute( 'wrapper', 'class', $classes );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="eap-video-story__grid">
				<?php
				foreach ( $query->posts as $post ) {
					$pid = is_object( $post ) ? (int) $post->ID : (int) $post;
					$this->render_card( $pid, $cfg, $editor );
				}
				?>
			</div>
		</div>
		<?php
		wp_reset_postdata();
	}

	/**
	 * Render a single story card.
	 *
	 * @param int   $pid    Post ID.
	 * @param array $cfg    Resolved config.
	 * @param bool  $editor Whether in the editor.
	 * @return void
	 */
	protected function render_card( $pid, $cfg, $editor ) {
		$title     = get_the_title( $pid );
		$permalink = get_permalink( $pid );
		$poster    = get_the_post_thumbnail_url( $pid, $cfg['img_size'] );

		if ( ! $poster && $editor && class_exists( '\Elementor\Utils' ) ) {
			$poster = \Elementor\Utils::get_placeholder_image_src();
		}

		$video = '';
		if ( '' !== $cfg['video_field'] ) {
			$meta = get_post_meta( $pid, $cfg['video_field'], true );
			if ( is_string( $meta ) && '' !== trim( $meta ) ) {
				$video = trim( $meta );
			}
		}
		if ( '' === $video && '' !== $cfg['fallback'] ) {
			$video = $cfg['fallback'];
		}
		$has_video = '' !== $video;
		?>
		<article class="eap-video-story__item"<?php echo $has_video ? ' data-eap-vs' : ''; ?>>
			<div class="eap-video-story__media">
				<?php if ( $poster ) : ?>
					<img class="eap-video-story__poster" src="<?php echo esc_url( $poster ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
				<?php else : ?>
					<span class="eap-video-story__poster eap-video-story__poster--ph" aria-hidden="true"></span>
				<?php endif; ?>

				<?php if ( $has_video ) : ?>
					<video class="eap-video-story__video" src="<?php echo esc_url( $video ); ?>"<?php echo $poster ? ' poster="' . esc_url( $poster ) . '"' : ''; ?> muted loop playsinline preload="metadata"></video>
				<?php endif; ?>

				<span class="eap-video-story__scrim" aria-hidden="true"></span>

				<div class="eap-video-story__content">
					<?php
					foreach ( $cfg['order_els'] as $el ) {
						$this->render_element( $el, $pid, $cfg, $title, $permalink );
					}

					if ( $cfg['show_play'] && $has_video ) :
						?>
						<button type="button" class="eap-video-story__play" aria-label="<?php echo esc_attr__( 'Play video', 'elementor-animatepro' ); ?>">
							<span class="eap-video-story__play-icon" aria-hidden="true"><?php $this->render_play_icon( $cfg ); ?></span>
							<?php if ( $cfg['show_duration'] ) : ?>
								<span class="eap-video-story__duration" aria-hidden="true"></span>
							<?php endif; ?>
						</button>
					<?php endif; ?>
				</div>

				<?php if ( $has_video ) : ?>
					<button type="button" class="eap-video-story__close" aria-label="<?php echo esc_attr__( 'Close video', 'elementor-animatepro' ); ?>"><span aria-hidden="true">&times;</span></button>
				<?php endif; ?>
			</div>
		</article>
		<?php
	}

	/**
	 * Render one content element (badge / title / excerpt / meta).
	 *
	 * @param string $el        Element key.
	 * @param int    $pid       Post ID.
	 * @param array  $cfg       Config.
	 * @param string $title     Post title.
	 * @param string $permalink Post permalink.
	 * @return void
	 */
	protected function render_element( $el, $pid, $cfg, $title, $permalink ) {
		switch ( $el ) {
			case 'taxonomy':
				echo $this->get_badge( $pid, $cfg ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
				break;

			case 'title':
				$tag = $cfg['title_tag'];
				printf(
					'<%1$s class="eap-video-story__title"><a href="%2$s">%3$s</a></%1$s>',
					esc_html( $tag ),
					esc_url( $permalink ),
					esc_html( $title )
				);
				break;

			case 'excerpt':
				$excerpt = $this->get_excerpt( $pid, $cfg['excerpt_len'] );
				if ( '' !== $excerpt ) {
					echo '<div class="eap-video-story__excerpt">' . esc_html( $excerpt ) . '</div>';
				}
				break;

			case 'meta':
				echo $this->get_meta( $pid, $cfg ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
				break;
		}
	}

	/**
	 * Output the play icon: the user's custom icon, or the built-in triangle.
	 *
	 * @param array $cfg Config.
	 * @return void
	 */
	protected function render_play_icon( $cfg ) {
		$icon = $cfg['play_icon'];

		if ( ! empty( $icon['value'] ) ) {
			\Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
			return;
		}

		echo '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M8 5v14l11-7z" fill="currentColor"/></svg>';
	}

	/**
	 * Taxonomy badge (first term of the chosen taxonomy).
	 *
	 * @param int   $pid Post ID.
	 * @param array $cfg Config.
	 * @return string
	 */
	protected function get_badge( $pid, $cfg ) {
		$taxonomy = $cfg['taxonomy'];
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return '';
		}

		$terms = get_the_terms( $pid, $taxonomy );
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}

		$term = $terms[0];

		if ( $cfg['badge_link'] ) {
			$url = get_term_link( $term );
			if ( ! is_wp_error( $url ) ) {
				return '<a class="eap-video-story__badge" href="' . esc_url( $url ) . '">' . esc_html( $term->name ) . '</a>';
			}
		}

		return '<span class="eap-video-story__badge">' . esc_html( $term->name ) . '</span>';
	}

	/**
	 * Byline meta ("By Author — Date").
	 *
	 * @param int   $pid Post ID.
	 * @param array $cfg Config.
	 * @return string
	 */
	protected function get_meta( $pid, $cfg ) {
		$parts = array();

		foreach ( $cfg['meta_fields'] as $field ) {
			if ( 'author' === $field ) {
				$name = get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $pid ) );
				if ( '' !== $name ) {
					$prefix  = '' !== trim( (string) $cfg['byline_prefix'] ) ? esc_html( $cfg['byline_prefix'] ) . ' ' : '';
					$parts[] = '<span class="eap-video-story__meta-author">' . $prefix . esc_html( $name ) . '</span>';
				}
			} elseif ( 'date' === $field ) {
				$parts[] = '<span class="eap-video-story__meta-date">' . esc_html( get_the_date( '', $pid ) ) . '</span>';
			}
		}

		if ( empty( $parts ) ) {
			return '';
		}

		return '<div class="eap-video-story__meta">' . implode( '<span class="eap-video-story__meta-sep" aria-hidden="true">—</span>', $parts ) . '</div>';
	}

	/**
	 * Trimmed excerpt.
	 *
	 * @param int $pid    Post ID.
	 * @param int $length Word count.
	 * @return string
	 */
	protected function get_excerpt( $pid, $length ) {
		$post = get_post( $pid );
		if ( ! $post ) {
			return '';
		}

		if ( '' !== trim( (string) $post->post_excerpt ) ) {
			return wp_trim_words( $post->post_excerpt, $length, '…' );
		}

		$raw = strip_shortcodes( wp_strip_all_tags( (string) $post->post_content ) );
		return wp_trim_words( $raw, $length, '…' );
	}

	/**
	 * Resolve the ordered, visible content elements.
	 *
	 * @param array $settings Settings.
	 * @param array $show     Visibility map.
	 * @return string[]
	 */
	protected function get_content_order( $settings, $show ) {
		$valid = array( 'taxonomy', 'title', 'excerpt', 'meta' );
		$order = array();

		$rows = isset( $settings['content_order'] ) && is_array( $settings['content_order'] ) ? $settings['content_order'] : array();
		foreach ( $rows as $row ) {
			$el = isset( $row['vs_element'] ) ? $row['vs_element'] : '';
			if ( in_array( $el, $valid, true ) && ! in_array( $el, $order, true ) && ! empty( $show[ $el ] ) ) {
				$order[] = $el;
			}
		}

		foreach ( array( 'taxonomy', 'title', 'meta', 'excerpt' ) as $el ) {
			if ( ! empty( $show[ $el ] ) && ! in_array( $el, $order, true ) ) {
				$order[] = $el;
			}
		}

		return $order;
	}

	/**
	 * Whitelist the title tag.
	 *
	 * @param string $tag Tag.
	 * @return string
	 */
	protected function safe_tag( $tag ) {
		return in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ), true ) ? $tag : 'h3';
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

	/**
	 * Registered image sizes for the poster select.
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
