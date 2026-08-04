<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class EAP_Widget_Advanced_Testimonial_Slider extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-advanced-testimonial-slider';
	}

	public function get_title() {
		return __( 'Advanced Testimonial Slider', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_keywords() {
		return array( 'testimonial', 'slider', 'video', 'review', 'quote', 'eap', 'animatepro' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'advanced-testimonial-slider' );
	}

	public function get_script_depends() {
		$deps = array(
			'eap-core-runtime',
			'eap-advanced-testimonial-slider-script',
		);
		if ( wp_script_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}
		return $deps;
	}

	/* ------------------------------------------------------------------ *
	 * Controls
	 * ------------------------------------------------------------------ */

	protected function register_controls() {
		$this->register_layout_section();
		$this->register_slides_section();
		$this->register_read_more_section();
		$this->register_slider_settings_section();
		$this->register_navigation_section();

		// Dual Marquee — content controls
		$this->register_marquee_row_section( 1 );
		$this->register_marquee_row_section( 2 );
		$this->register_marquee_settings_section();

		$this->register_card_style_section();
		$this->register_overlay_style_section();
		$this->register_content_style_section();
		$this->register_user_info_style_section();
		$this->register_separator_style_section();
		$this->register_read_more_style_section();
		$this->register_play_button_style_section();
		$this->register_slider_nav_style_section();
		$this->register_slider_pagination_style_section();
		$this->register_video_popup_style_section();
		$this->register_content_popup_style_section();

		// Dual Marquee — style controls
		$this->register_marquee_card_style_section();
		$this->register_marquee_quote_style_section();
		$this->register_marquee_content_style_section();
		$this->register_marquee_user_info_style_section();

		// Brand Quote — style controls
		$this->register_brand_quote_layout_style_section();
		$this->register_brand_quote_brand_style_section();
		$this->register_brand_quote_text_style_section();
		$this->register_brand_quote_author_style_section();
		$this->register_brand_quote_image_style_section();
	}

	private function register_layout_section() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'background-overlay',
				'options' => array(
					'background-overlay' => __( 'Background Overlay (Slider)', 'elementor-animatepro' ),
					'dual-marquee'       => __( 'Dual Marquee (Two-Row Scroll)', 'elementor-animatepro' ),
					'brand-quote'        => __( 'Brand Quote (Side-by-Side)', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'overlay_start',
			array(
				'label'      => __( 'Overlay Start', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array( '%' => array( 'min' => 20, 'max' => 95 ) ),
				'default'    => array( 'unit' => '%', 'size' => 58 ),
				'condition'  => array( 'layout' => 'background-overlay' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide' => '--eap-ats-overlay-start: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'user_info_align',
			array(
				'label'     => __( 'User Info Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'toggle'    => false,
				'condition' => array( 'layout' => 'background-overlay' ),
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__body' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_slides_section() {
		$this->start_controls_section(
			'section_slides',
			array(
				'label'     => __( 'Slides', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'layout' => array( 'background-overlay', 'brand-quote' ) ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'bg_type',
			array(
				'label'   => __( 'Background Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => array(
					'image' => __( 'Image', 'elementor-animatepro' ),
					'video' => __( 'Video', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'background_image',
			array(
				'label'   => __( 'Background Image', 'elementor-animatepro' ),
				'description' => __( 'For video background, this is used as the poster while the video loads.', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$repeater->add_control(
			'background_video',
			array(
				'label'       => __( 'Background Video (MP4)', 'elementor-animatepro' ),
				'description' => __( 'Self-hosted .mp4 plays muted on loop behind the slide. Used in the popup with audio.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'video' ),
				'condition'   => array( 'bg_type' => 'video' ),
			)
		);

		$repeater->add_control(
			'brand_name',
			array(
				'label'       => __( 'Brand / Company Name', 'elementor-animatepro' ),
				'description' => __( 'Shown as the eyebrow label in the Brand Quote layout. Leave empty if not using that layout.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label'       => __( 'Name', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'John Doe', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'designation',
			array(
				'label'   => __( 'Designation', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Marketing Director, TechCorp', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'avatar',
			array(
				'label'   => __( 'Avatar', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$repeater->add_control(
			'testimonial_content',
			array(
				'label'   => __( 'Testimonial Content', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'ContentAI has revolutionized our content workflow. The quality of the articles is outstanding, and it saves us hours of work every week.', 'elementor-animatepro' ),
				'rows'    => 5,
			)
		);

		$repeater->add_control(
			'rating',
			array(
				'label'   => __( 'Rating', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 5,
				'step'    => 0.5,
				'default' => 4.5,
			)
		);

		$this->add_control(
			'slides',
			array(
				'label'       => __( 'Slides', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => array(
					array(
						'name'        => 'John Doe',
						'designation' => 'Marketing Director, TechCorp',
					),
					array(
						'name'        => 'Donald Jackman',
						'designation' => 'Content Creator',
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'avatar_size',
				'default' => 'thumbnail',
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'background_image_size',
				'default' => 'large',
			)
		);

		$this->end_controls_section();
	}

	private function register_read_more_section() {
		$this->start_controls_section(
			'section_read_more',
			array(
				'label'     => __( 'Read More', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'layout' => 'background-overlay' ),
			)
		);

		$this->add_control(
			'enable_read_more',
			array(
				'label'   => __( 'Enable Read More', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_responsive_control(
			'content_line_clamp',
			array(
				'label'       => __( 'Visible Lines', 'elementor-animatepro' ),
				'description' => __( 'Maximum number of lines shown on the slide. Content longer than this gets a Read More button.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 20,
				'default'     => 3,
				'condition'   => array( 'enable_read_more' => 'yes' ),
				'selectors'   => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__text' => '--eap-ats-clamp: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'read_more_text',
			array(
				'label'     => __( 'Button Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read More', 'elementor-animatepro' ),
				'condition' => array( 'enable_read_more' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	private function register_slider_settings_section() {
		$this->start_controls_section(
			'section_slider_settings',
			array(
				'label'     => __( 'Slider Settings', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'layout' => array( 'background-overlay', 'brand-quote' ) ),
			)
		);

		$this->add_responsive_control(
			'slides_per_view',
			array(
				'label'              => __( 'Slides Per View', 'elementor-animatepro' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '3',
				'tablet_default'     => '2',
				'mobile_default'     => '1',
				'options'            => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
				),
				'condition'          => array( 'layout' => 'background-overlay' ),
			)
		);

		$this->add_responsive_control(
			'space_between',
			array(
				'label'      => __( 'Space Between', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 24 ),
				'condition'  => array( 'layout' => 'background-overlay' ),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'   => __( 'Loop', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'   => __( 'Autoplay', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => __( 'Autoplay Delay (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3500,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'     => __( 'Pause on Hover', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'allow_touch_move',
			array(
				'label'   => __( 'Allow Touch Drag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => __( 'Transition Speed (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 650,
			)
		);

		$this->end_controls_section();
	}

	private function register_navigation_section() {
		$this->start_controls_section(
			'section_navigation',
			array(
				'label'     => __( 'Navigation & Pagination', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'layout' => array( 'background-overlay', 'brand-quote' ) ),
			)
		);

		$this->add_control(
			'show_arrows',
			array(
				'label'   => __( 'Show Arrows', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'prev_icon',
			array(
				'label'     => __( 'Previous Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array( 'value' => 'fas fa-chevron-left', 'library' => 'fa-solid' ),
				'condition' => array( 'show_arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'next_icon',
			array(
				'label'     => __( 'Next Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array( 'value' => 'fas fa-chevron-right', 'library' => 'fa-solid' ),
				'condition' => array( 'show_arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'show_pagination',
			array(
				'label'   => __( 'Show Pagination', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
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

		$this->add_control(
			'pagination_position',
			array(
				'label'     => __( 'Pagination Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'outside',
				'options'   => array(
					'inside'  => __( 'Inside', 'elementor-animatepro' ),
					'outside' => __( 'Outside', 'elementor-animatepro' ),
				),
				'condition' => array( 'show_pagination' => 'yes' ),
			)
		);

		// Play button icon (for video slides).
		$this->add_control(
			'play_icon',
			array(
				'label'   => __( 'Video Play Icon', 'elementor-animatepro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array( 'value' => 'fas fa-play', 'library' => 'fa-solid' ),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------ *
	 * Dual Marquee — content controls
	 * ------------------------------------------------------------------ */

	private function register_marquee_row_section( $row_num ) {
		$row_num = (int) $row_num;
		$section_id = 'section_marquee_row_' . $row_num;
		$control_id = 'marquee_row_' . $row_num . '_items';

		$this->start_controls_section(
			$section_id,
			array(
				/* translators: %d row number */
				'label'     => sprintf( __( 'Row %d Items', 'elementor-animatepro' ), $row_num ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'layout' => 'dual-marquee' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'name',
			array(
				'label'       => __( 'Name', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'John Doe', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'designation',
			array(
				'label'   => __( 'Designation', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'CEO and Co-founder of ABC Company', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'avatar',
			array(
				'label'   => __( 'Avatar', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$repeater->add_control(
			'testimonial_content',
			array(
				'label'   => __( 'Testimonial', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => __( 'Their ability to capture our brand essence in every project is unparalleled — an invaluable creative collaborator.', 'elementor-animatepro' ),
			)
		);

		$defaults_row_1 = array(
			array( 'name' => 'Isabella Rodriguez',  'testimonial_content' => "Their ability to capture our brand essence in every project is unparalleled — an invaluable creative collaborator." ),
			array( 'name' => 'Gabrielle Williams',  'testimonial_content' => "Creative geniuses who listen, understand, and craft captivating visuals — an agency that truly understands our needs." ),
			array( 'name' => 'Samantha Johnson',    'testimonial_content' => "Exceeded our expectations with innovative designs that brought our vision to life — a truly remarkable creative agency." ),
		);
		$defaults_row_2 = array(
			array( 'name' => 'Natalie Martinez',    'testimonial_content' => "From concept to execution, their creativity knows no bounds — a game-changer for our brand's success." ),
			array( 'name' => 'Victoria Thompson',   'testimonial_content' => "A refreshing and imaginative agency that consistently delivers exceptional results — highly recommended for any project." ),
			array( 'name' => 'John Peter',          'testimonial_content' => "Their team's artistic flair and strategic approach resulted in remarkable campaigns — a reliable creative partner." ),
		);

		$this->add_control(
			$control_id,
			array(
				/* translators: %d row number */
				'label'       => sprintf( __( 'Row %d Items', 'elementor-animatepro' ), $row_num ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => 1 === $row_num ? $defaults_row_1 : $defaults_row_2,
			)
		);

		$this->end_controls_section();
	}

	private function register_marquee_settings_section() {
		$this->start_controls_section(
			'section_marquee_settings',
			array(
				'label'     => __( 'Marquee Settings', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'layout' => 'dual-marquee' ),
			)
		);

		$this->add_control(
			'marquee_quote_icon',
			array(
				'label'   => __( 'Quote Icon', 'elementor-animatepro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array( 'value' => 'fas fa-quote-left', 'library' => 'fa-solid' ),
			)
		);

		$this->add_control(
			'marquee_row_gap',
			array(
				'label'       => __( 'Gap Between Rows', 'elementor-animatepro' ),
				'description' => __( 'Extra gap added between the two rows. Each row already includes vertical space for card shadows.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 0 ),
				'selectors'   => array(
					'{{WRAPPER}} .eap-adv-ts-marquee-wrap' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'marquee_row_padding_y',
			array(
				'label'       => __( 'Row Vertical Padding', 'elementor-animatepro' ),
				'description' => __( 'Space above and below the cards in each row — needed so the card box-shadow isn\'t clipped. Lower this if your cards have no shadow, raise it for heavier shadows.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 12 ),
				'selectors'   => array(
					'{{WRAPPER}} .eap-adv-ts-marquee' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'marquee_card_gap',
			array(
				'label'      => __( 'Gap Between Cards', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 20 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-ts-marquee__track' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'marquee_pause_on_hover',
			array(
				'label'   => __( 'Pause on Hover', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		// Per-row controls — Row 1
		$this->add_control(
			'marquee_row_1_heading',
			array(
				'label'     => __( 'Row 1', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'marquee_row_1_direction',
			array(
				'label'   => __( 'Direction', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => __( 'Right → Left', 'elementor-animatepro' ),
					'right' => __( 'Left → Right', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'marquee_row_1_duration',
			array(
				'label'       => __( 'Duration (seconds)', 'elementor-animatepro' ),
				'description' => __( 'Higher value = slower scroll.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 5, 'max' => 120, 'step' => 1 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 40 ),
				'selectors'   => array(
					'{{WRAPPER}} .eap-adv-ts-marquee--row-1' => '--eap-marquee-duration: {{SIZE}}s;',
				),
			)
		);

		// Per-row controls — Row 2
		$this->add_control(
			'marquee_row_2_heading',
			array(
				'label'     => __( 'Row 2', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'marquee_row_2_direction',
			array(
				'label'   => __( 'Direction', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'left'  => __( 'Right → Left', 'elementor-animatepro' ),
					'right' => __( 'Left → Right', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'marquee_row_2_duration',
			array(
				'label'       => __( 'Duration (seconds)', 'elementor-animatepro' ),
				'description' => __( 'Higher value = slower scroll.', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 5, 'max' => 120, 'step' => 1 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 50 ),
				'selectors'   => array(
					'{{WRAPPER}} .eap-adv-ts-marquee--row-2' => '--eap-marquee-duration: {{SIZE}}s;',
				),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------ *
	 * Dual Marquee — style controls
	 * ------------------------------------------------------------------ */

	private function register_marquee_card_style_section() {
		$this->start_controls_section(
			'section_marquee_card_style',
			array(
				'label'     => __( 'Marquee Card', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'dual-marquee' ),
			)
		);

		$this->add_responsive_control(
			'marquee_card_width',
			array(
				'label'      => __( 'Card Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 220, 'max' => 540 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 320 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-ts-marquee__card' => 'width: {{SIZE}}{{UNIT}}; flex: 0 0 {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'marquee_card_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top' => 28, 'right' => 28, 'bottom' => 24, 'left' => 28,
					'unit' => 'px', 'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-ts-marquee__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'marquee_card_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-ts-marquee__card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'marquee_card_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top' => 14, 'right' => 14, 'bottom' => 14, 'left' => 14,
					'unit' => 'px', 'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-ts-marquee__card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'marquee_card_border',
				'selector' => '{{WRAPPER}} .eap-adv-ts-marquee__card',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'marquee_card_shadow',
				'selector' => '{{WRAPPER}} .eap-adv-ts-marquee__card',
				'fields_options' => array(
					'box_shadow_type' => array(
						'default' => 'yes',
					),
					'box_shadow' => array(
						'default' => array(
							'horizontal' => 0,
							'vertical'   => 4,
							'blur'       => 24,
							'spread'     => 0,
							'color'      => 'rgba(15, 23, 42, 0.08)',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_marquee_quote_style_section() {
		$this->start_controls_section(
			'section_marquee_quote_style',
			array(
				'label'     => __( 'Marquee Quote Icon', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'dual-marquee' ),
			)
		);

		$this->add_responsive_control(
			'marquee_quote_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 12, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 30 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-ts-marquee__quote-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-adv-ts-marquee__quote-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'marquee_quote_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-ts-marquee__quote-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'marquee_quote_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top' => 0, 'right' => 0, 'bottom' => 16, 'left' => 0,
					'unit' => 'px', 'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-ts-marquee__quote-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_marquee_content_style_section() {
		$this->start_controls_section(
			'section_marquee_content_style',
			array(
				'label'     => __( 'Marquee Content', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'dual-marquee' ),
			)
		);

		$this->add_control(
			'marquee_content_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-ts-marquee__content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'marquee_content_typography',
				'selector' => '{{WRAPPER}} .eap-adv-ts-marquee__content',
			)
		);

		$this->add_responsive_control(
			'marquee_content_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top' => 0, 'right' => 0, 'bottom' => 24, 'left' => 0,
					'unit' => 'px', 'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-ts-marquee__content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_marquee_user_info_style_section() {
		$this->start_controls_section(
			'section_marquee_user_info_style',
			array(
				'label'     => __( 'Marquee User Info', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'dual-marquee' ),
			)
		);

		$this->add_responsive_control(
			'marquee_avatar_size',
			array(
				'label'      => __( 'Avatar Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 96 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 40 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-ts-marquee__avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'marquee_user_gap',
			array(
				'label'      => __( 'Gap (Avatar / Text)', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 12 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-ts-marquee__user-info' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'marquee_name_heading',
			array(
				'label'     => __( 'Name', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'marquee_name_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-ts-marquee__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'marquee_name_typography',
				'selector' => '{{WRAPPER}} .eap-adv-ts-marquee__name',
			)
		);

		$this->add_control(
			'marquee_role_heading',
			array(
				'label'     => __( 'Designation', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'marquee_role_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-ts-marquee__role' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'marquee_role_typography',
				'selector' => '{{WRAPPER}} .eap-adv-ts-marquee__role',
			)
		);

		$this->end_controls_section();
	}

	private function register_card_style_section() {
		$this->start_controls_section(
			'section_card_style',
			array(
				'label'     => __( 'Card', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'background-overlay' ),
			)
		);

		$this->add_responsive_control(
			'card_height',
			array(
				'label'      => __( 'Card Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 280, 'max' => 900 ),
					'vh' => array( 'min' => 30, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 480 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top' => 28, 'right' => 28, 'bottom' => 28, 'left' => 28,
					'unit' => 'px', 'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16,
					'unit' => 'px', 'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .eap-adv-testimonial-slide',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eap-adv-testimonial-slide',
			)
		);

		$this->end_controls_section();
	}

	private function register_overlay_style_section() {
		$this->start_controls_section(
			'section_overlay_style',
			array(
				'label'     => __( 'Overlay', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'background-overlay' ),
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15, 17, 22, 0.85)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide' => '--eap-ats-overlay-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_content_style_section() {
		$this->start_controls_section(
			'section_content_style',
			array(
				'label'     => __( 'Content', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'background-overlay' ),
			)
		);

		$this->add_control(
			'content_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .eap-adv-testimonial-slide__text',
			)
		);

		$this->add_responsive_control(
			'content_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_user_info_style_section() {
		$this->start_controls_section(
			'section_user_info_style',
			array(
				'label'     => __( 'User Info', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'background-overlay' ),
			)
		);

		$this->add_responsive_control(
			'avatar_size_px',
			array(
				'label'      => __( 'Avatar Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 120 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 44 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => __( 'Name Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'label'    => __( 'Name Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-adv-testimonial-slide__name',
			)
		);

		$this->add_control(
			'designation_color',
			array(
				'label'     => __( 'Designation Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.75)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__role' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'designation_typography',
				'label'    => __( 'Designation Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-adv-testimonial-slide__role',
			)
		);

		$this->end_controls_section();
	}

	private function register_separator_style_section() {
		$this->start_controls_section(
			'section_separator_style',
			array(
				'label'     => __( 'Separator', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'background-overlay' ),
			)
		);

		$this->add_control(
			'separator_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.18)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__separator' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'separator_height',
			array(
				'label'      => __( 'Thickness', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 1, 'max' => 6 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 1 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__separator' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'separator_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top' => 16, 'right' => 0, 'bottom' => 16, 'left' => 0,
					'unit' => 'px', 'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__separator' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_read_more_style_section() {
		$this->start_controls_section(
			'section_read_more_style',
			array(
				'label'     => __( 'Read More Button', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout'           => 'background-overlay',
					'enable_read_more' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'rm_typography',
				'selector' => '{{WRAPPER}} .eap-adv-testimonial-slide__read-more',
			)
		);

		$this->start_controls_tabs( 'rm_states' );

		$this->start_controls_tab( 'rm_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'rm_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__read-more' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'rm_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__read-more' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'rm_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'rm_color_hover',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__read-more:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'rm_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__read-more:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'rm_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top' => 6, 'right' => 12, 'bottom' => 6, 'left' => 12,
					'unit' => 'px', 'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__read-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'rm_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top' => 999, 'right' => 999, 'bottom' => 999, 'left' => 999,
					'unit' => 'px', 'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__read-more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'rm_border',
				'selector' => '{{WRAPPER}} .eap-adv-testimonial-slide__read-more',
			)
		);

		$this->add_responsive_control(
			'rm_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top' => 8, 'right' => 0, 'bottom' => 0, 'left' => 0,
					'unit' => 'px', 'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__read-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_play_button_style_section() {
		$this->start_controls_section(
			'section_play_style',
			array(
				'label'     => __( 'Video Play Button', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'background-overlay' ),
			)
		);

		$this->add_responsive_control(
			'play_size',
			array(
				'label'      => __( 'Button Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 200 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 48 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__play' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'play_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 100 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 16 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__play' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-adv-testimonial-slide__play svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->start_controls_tabs( 'play_states' );

		$this->start_controls_tab( 'play_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'play_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__play' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'play_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__play' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'play_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'play_color_hover',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__play:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'play_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__play:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'play_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50,
					'unit' => '%', 'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slide__play' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'play_shadow',
				'selector' => '{{WRAPPER}} .eap-adv-testimonial-slide__play',
			)
		);

		$this->end_controls_section();
	}

	private function register_slider_nav_style_section() {
		$this->start_controls_section(
			'section_slider_nav_style',
			array(
				'label'     => __( 'Slider Navigation', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout'      => array( 'background-overlay', 'brand-quote' ),
					'show_arrows' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'nav_box_size',
			array(
				'label'      => __( 'Button Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 28, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 42 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slider' => '--eap-ats-arrow-box: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 32 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 16 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slider' => '--eap-ats-arrow-icon: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'nav_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slider__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15,17,22,0.78)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slider__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_offset_x',
			array(
				'label'      => __( 'Side Offset', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => -60, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 0 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slider' => '--eap-ats-arrow-offset-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_slider_pagination_style_section() {
		$this->start_controls_section(
			'section_slider_pagination_style',
			array(
				'label'     => __( 'Slider Pagination', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout'          => array( 'background-overlay', 'brand-quote' ),
					'show_pagination' => 'yes',
				),
			)
		);

		$this->add_control(
			'pag_color',
			array(
				'label'     => __( 'Bullet Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.45)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slider__pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pag_active_color',
			array(
				'label'     => __( 'Active Bullet Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slider__pagination .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pag_size',
			array(
				'label'      => __( 'Bullet Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 4, 'max' => 24 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 10 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slider' => '--eap-ats-pag-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pag_gap',
			array(
				'label'      => __( 'Gap Between Bullets', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 8 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slider__pagination' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pag_margin_top',
			array(
				'label'      => __( 'Distance From Slider', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 18 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-slider__pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_video_popup_style_section() {
		$this->start_controls_section(
			'section_video_popup_style',
			array(
				'label'     => __( 'Video Popup', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'background-overlay' ),
			)
		);

		$this->add_control(
			'vp_backdrop',
			array(
				'label'     => __( 'Backdrop Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.85)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--video .eap-adv-testimonial-popup__backdrop' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'vp_max_width',
			array(
				'label'      => __( 'Max Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 320, 'max' => 1600 ),
					'%'  => array( 'min' => 40, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 960 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--video .eap-adv-testimonial-popup__panel' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'vp_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12,
					'unit' => 'px', 'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--video .eap-adv-testimonial-popup__panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'vp_close_heading',
			array(
				'label'     => __( 'Close Button', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'vp_close_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--video .eap-adv-testimonial-popup__close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'vp_close_bg',
			array(
				'label'     => __( 'Background Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.45)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--video .eap-adv-testimonial-popup__close' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'vp_close_hover_color',
			array(
				'label'     => __( 'Hover Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--video .eap-adv-testimonial-popup__close:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'vp_close_hover_bg',
			array(
				'label'     => __( 'Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.7)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--video .eap-adv-testimonial-popup__close:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_content_popup_style_section() {
		$this->start_controls_section(
			'section_content_popup_style',
			array(
				'label'     => __( 'Content Popup', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout'           => 'background-overlay',
					'enable_read_more' => 'yes',
				),
			)
		);

		$this->add_control(
			'cp_backdrop',
			array(
				'label'     => __( 'Backdrop Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.78)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__backdrop' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cp_bg',
			array(
				'label'     => __( 'Panel Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1a1d24',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__panel' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cp_text_color',
			array(
				'label'     => __( 'Content Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e6e8eb',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cp_content_typography',
				'label'    => __( 'Content Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__content',
			)
		);

		$this->add_control(
			'cp_name_heading',
			array(
				'label'     => __( 'Name', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'cp_name_color',
			array(
				'label'     => __( 'Name Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cp_name_typography',
				'label'    => __( 'Name Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__name',
			)
		);

		$this->add_control(
			'cp_role_heading',
			array(
				'label'     => __( 'Designation', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'cp_role_color',
			array(
				'label'     => __( 'Designation Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.7)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__role' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cp_role_typography',
				'label'    => __( 'Designation Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__role',
			)
		);

		$this->add_responsive_control(
			'cp_max_width',
			array(
				'label'      => __( 'Max Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 320, 'max' => 1000 ),
					'%'  => array( 'min' => 40, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 620 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__panel' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cp_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top' => 36, 'right' => 36, 'bottom' => 36, 'left' => 36,
					'unit' => 'px', 'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cp_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top' => 14, 'right' => 14, 'bottom' => 14, 'left' => 14,
					'unit' => 'px', 'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cp_avatar_size',
			array(
				'label'      => __( 'Avatar Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 40, 'max' => 160 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 64 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'cp_close_heading',
			array(
				'label'     => __( 'Close Button', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'cp_close_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cp_close_bg',
			array(
				'label'     => __( 'Background Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.45)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__close' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cp_close_hover_color',
			array(
				'label'     => __( 'Hover Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__close:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cp_close_hover_bg',
			array(
				'label'     => __( 'Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.7)',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-popup--content .eap-adv-testimonial-popup__close:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------ *
	 * Brand Quote — style controls
	 * ------------------------------------------------------------------ */

	private function register_brand_quote_layout_style_section() {
		$this->start_controls_section(
			'section_brand_quote_layout_style',
			array(
				'label'     => __( 'Brand Quote Layout', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'brand-quote' ),
			)
		);

		$this->add_control(
			'bq_image_position',
			array(
				'label'   => __( 'Image Position', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'  => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default' => 'right',
				'toggle'  => false,
			)
		);

		$this->add_responsive_control(
			'bq_text_width',
			array(
				'label'      => __( 'Text Column Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array( 'min' => 40, 'max' => 85 ),
					'px' => array( 'min' => 300, 'max' => 1200 ),
				),
				'default'    => array( 'unit' => '%', 'size' => 65 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-tbq__text' => 'flex: 0 0 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bq_columns_gap',
			array(
				'label'      => __( 'Gap Between Columns', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 80 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-tbq' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bq_vertical_align',
			array(
				'label'   => __( 'Vertical Alignment', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Top', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => __( 'Middle', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-tbq' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'bq_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-tbq' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bq_pagination_align',
			array(
				'label'   => __( 'Pagination Alignment', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
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
				'default'   => 'flex-start',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-testimonial-slider--brand-quote .eap-adv-testimonial-slider__pagination' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_brand_quote_brand_style_section() {
		$this->start_controls_section(
			'section_brand_quote_brand_style',
			array(
				'label'     => __( 'Brand Label', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'brand-quote' ),
			)
		);

		$this->add_control(
			'bq_brand_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-tbq__brand-name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'bq_brand_typography',
				'selector' => '{{WRAPPER}} .eap-adv-tbq__brand-name',
			)
		);

		$this->add_responsive_control(
			'bq_brand_line_width',
			array(
				'label'      => __( 'Accent Line Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 32 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-tbq__brand-line' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bq_brand_line_thickness',
			array(
				'label'      => __( 'Accent Line Thickness', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 1, 'max' => 6 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 1 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-tbq__brand-line' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bq_brand_line_color',
			array(
				'label'     => __( 'Accent Line Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-tbq__brand-line' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'bq_brand_gap',
			array(
				'label'      => __( 'Gap (Line / Text)', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 16 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-tbq__brand' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bq_brand_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top' => 0, 'right' => 0, 'bottom' => 32, 'left' => 0,
					'unit' => 'px', 'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-tbq__brand' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_brand_quote_text_style_section() {
		$this->start_controls_section(
			'section_brand_quote_text_style',
			array(
				'label'     => __( 'Quote Text', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'brand-quote' ),
			)
		);

		$this->add_control(
			'bq_quote_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-tbq__quote' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'bq_quote_typography',
				'selector' => '{{WRAPPER}} .eap-adv-tbq__quote',
			)
		);

		$this->add_responsive_control(
			'bq_quote_margin',
			array(
				'label'      => __( 'Margin', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top' => 0, 'right' => 0, 'bottom' => 36, 'left' => 0,
					'unit' => 'px', 'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-tbq__quote' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_brand_quote_author_style_section() {
		$this->start_controls_section(
			'section_brand_quote_author_style',
			array(
				'label'     => __( 'Author Info', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'brand-quote' ),
			)
		);

		$this->add_control(
			'bq_author_line_heading',
			array(
				'label' => __( 'Accent Line', 'elementor-animatepro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'bq_author_line_width',
			array(
				'label'      => __( 'Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 40 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-tbq__author-line' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bq_author_line_thickness',
			array(
				'label'      => __( 'Thickness', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 1, 'max' => 6 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 1 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-tbq__author-line' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bq_author_line_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d1d5db',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-tbq__author-line' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'bq_author_gap',
			array(
				'label'      => __( 'Gap (Line / Text)', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 16 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-tbq__author' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bq_name_heading',
			array(
				'label'     => __( 'Name', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'bq_name_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-tbq__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'bq_name_typography',
				'selector' => '{{WRAPPER}} .eap-adv-tbq__name',
			)
		);

		$this->add_control(
			'bq_role_heading',
			array(
				'label'     => __( 'Designation', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'bq_role_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-tbq__role' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'bq_role_typography',
				'selector' => '{{WRAPPER}} .eap-adv-tbq__role',
			)
		);

		$this->end_controls_section();
	}

	private function register_brand_quote_image_style_section() {
		$this->start_controls_section(
			'section_brand_quote_image_style',
			array(
				'label'     => __( 'Image', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'brand-quote' ),
			)
		);

		$this->add_responsive_control(
			'bq_image_width',
			array(
				'label'      => __( 'Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 120, 'max' => 600 ),
					'%'  => array( 'min' => 15, 'max' => 60 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 280 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-tbq__media' => 'flex: 0 0 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bq_image_height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 160, 'max' => 700 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 340 ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-tbq__media' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bq_image_object_fit',
			array(
				'label'     => __( 'Object Fit', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => array(
					'cover'   => __( 'Cover', 'elementor-animatepro' ),
					'contain' => __( 'Contain', 'elementor-animatepro' ),
					'fill'    => __( 'Fill', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-adv-tbq__media img' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'bq_image_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top' => 18, 'right' => 18, 'bottom' => 18, 'left' => 18,
					'unit' => 'px', 'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-adv-tbq__media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'bq_image_border',
				'selector' => '{{WRAPPER}} .eap-adv-tbq__media',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'bq_image_shadow',
				'selector' => '{{WRAPPER}} .eap-adv-tbq__media',
			)
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------ *
	 * Render helpers
	 * ------------------------------------------------------------------ */

	private function get_slider_settings_data( $settings ) {
		$slides_desktop = isset( $settings['slides_per_view'] ) ? (int) $settings['slides_per_view'] : 3;
		$slides_tablet  = isset( $settings['slides_per_view_tablet'] ) ? (int) $settings['slides_per_view_tablet'] : 2;
		$slides_mobile  = isset( $settings['slides_per_view_mobile'] ) ? (int) $settings['slides_per_view_mobile'] : 1;
		$space          = isset( $settings['space_between']['size'] ) ? (int) $settings['space_between']['size'] : 24;

		return array(
			'slidesDesktop'  => max( 1, $slides_desktop ),
			'slidesTablet'   => max( 1, $slides_tablet ),
			'slidesMobile'   => max( 1, $slides_mobile ),
			'spaceBetween'   => max( 0, $space ),
			'loop'           => ! empty( $settings['loop'] ) && 'yes' === $settings['loop'],
			'autoplay'       => ! empty( $settings['autoplay'] ) && 'yes' === $settings['autoplay'],
			'autoplayDelay'  => ! empty( $settings['autoplay_delay'] ) ? (int) $settings['autoplay_delay'] : 3500,
			'pauseOnHover'   => ! empty( $settings['pause_on_hover'] ) && 'yes' === $settings['pause_on_hover'],
			'allowTouchMove' => ! empty( $settings['allow_touch_move'] ) && 'yes' === $settings['allow_touch_move'],
			'speed'          => isset( $settings['speed'] ) ? (int) $settings['speed'] : 650,
			'navigation'     => ! empty( $settings['show_arrows'] ) && 'yes' === $settings['show_arrows'],
			'pagination'     => ! empty( $settings['show_pagination'] ) && 'yes' === $settings['show_pagination'],
			'paginationType' => ! empty( $settings['pagination_type'] ) ? $settings['pagination_type'] : 'bullets',
		);
	}

	private function get_image_html( $settings, $image_data, $size_key ) {
		if ( empty( $image_data ) || ( empty( $image_data['url'] ) && empty( $image_data['id'] ) ) ) {
			return '';
		}
		$merged = array_merge( $settings, array( 'image' => $image_data ) );
		$html   = Group_Control_Image_Size::get_attachment_image_html( $merged, $size_key, 'image' );
		if ( empty( $html ) && ! empty( $image_data['url'] ) ) {
			$html = '<img src="' . esc_url( $image_data['url'] ) . '" alt="" />';
		}
		return $html;
	}

	private function render_rating( $rating ) {
		$rating = (float) $rating;
		if ( $rating <= 0 ) {
			return '';
		}
		ob_start();
		?>
		<div class="eap-adv-testimonial-slide__rating" aria-label="<?php echo esc_attr( sprintf( '%s out of 5 stars', $rating ) ); ?>">
			<?php
			for ( $i = 1; $i <= 5; $i++ ) :
				$state = 'empty';
				if ( $rating >= $i ) {
					$state = 'full';
				} elseif ( $rating >= ( $i - 0.5 ) ) {
					$state = 'half';
				}
				?>
				<span class="eap-adv-testimonial-slide__star eap-adv-testimonial-slide__star--<?php echo esc_attr( $state ); ?>" aria-hidden="true"></span>
			<?php endfor; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/* ------------------------------------------------------------------ *
	 * Render
	 * ------------------------------------------------------------------ */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$layout   = ! empty( $settings['layout'] ) ? $settings['layout'] : 'background-overlay';

		if ( 'dual-marquee' === $layout ) {
			$this->render_dual_marquee( $settings );
			return;
		}

		if ( 'brand-quote' === $layout ) {
			$this->render_brand_quote( $settings );
			return;
		}

		$slides = ! empty( $settings['slides'] ) && is_array( $settings['slides'] ) ? $settings['slides'] : array();

		if ( empty( $slides ) ) {
			return;
		}

		$widget_id   = $this->get_id();
		$slider_data = $this->get_slider_settings_data( $settings );

		$pagination_position = ! empty( $settings['pagination_position'] ) ? $settings['pagination_position'] : 'outside';

		$wrapper_classes = array(
			'eap-widget',
			'eap-adv-testimonial-slider',
			'eap-adv-testimonial-slider--pagination-' . esc_attr( $pagination_position ),
		);

		$enable_rm = ! empty( $settings['enable_read_more'] ) && 'yes' === $settings['enable_read_more'];
		$rm_text   = ! empty( $settings['read_more_text'] ) ? $settings['read_more_text'] : __( 'Read More', 'elementor-animatepro' );
		$clamp     = isset( $settings['content_line_clamp'] ) ? max( 1, (int) $settings['content_line_clamp'] ) : 3;

		// Pre-render the play icon and read-more icon HTML for reuse.
		ob_start();
		if ( ! empty( $settings['play_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['play_icon'], array( 'aria-hidden' => 'true' ) );
		}
		$play_icon_html = ob_get_clean();

		ob_start();
		if ( ! empty( $settings['prev_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['prev_icon'], array( 'aria-hidden' => 'true' ) );
		}
		$prev_icon_html = ob_get_clean();

		ob_start();
		if ( ! empty( $settings['next_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['next_icon'], array( 'aria-hidden' => 'true' ) );
		}
		$next_icon_html = ob_get_clean();
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-eap-ats-root>
			<div class="eap-adv-testimonial-slider__shell">
				<?php if ( $slider_data['navigation'] ) : ?>
					<button type="button" class="eap-adv-testimonial-slider__arrow eap-adv-testimonial-slider__arrow--prev" aria-label="<?php esc_attr_e( 'Previous slide', 'elementor-animatepro' ); ?>">
						<?php echo $prev_icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				<?php endif; ?>

				<div class="eap-adv-testimonial-slider__swiper swiper" data-eap-ats="<?php echo esc_attr( wp_json_encode( $slider_data ) ); ?>">
					<div class="swiper-wrapper">
						<?php foreach ( $slides as $index => $slide ) :
							$bg_type   = ! empty( $slide['bg_type'] ) ? $slide['bg_type'] : 'image';
							$is_video  = 'video' === $bg_type && ! empty( $slide['background_video']['url'] );
							$video_url = $is_video ? $slide['background_video']['url'] : '';
							$image     = ! empty( $slide['background_image'] ) ? $slide['background_image'] : array();
							$avatar    = ! empty( $slide['avatar'] ) ? $slide['avatar'] : array();
							$bg_image_html = $this->get_image_html( $settings, $image, 'background_image_size' );
							$avatar_html   = $this->get_image_html( $settings, $avatar, 'avatar_size' );

							$content_text = ! empty( $slide['testimonial_content'] ) ? $slide['testimonial_content'] : '';
							$name         = ! empty( $slide['name'] ) ? $slide['name'] : '';
							$role         = ! empty( $slide['designation'] ) ? $slide['designation'] : '';

							$content_popup_id = sprintf( 'eap-ats-cp-%s-%d', esc_attr( $widget_id ), $index );
							$video_popup_id   = sprintf( 'eap-ats-vp-%s-%d', esc_attr( $widget_id ), $index );
							?>
							<div class="swiper-slide eap-adv-testimonial-slider__slide">
								<div class="eap-adv-testimonial-slide" data-bg-type="<?php echo esc_attr( $bg_type ); ?>">
									<div class="eap-adv-testimonial-slide__bg" aria-hidden="true">
										<?php if ( $is_video ) : ?>
											<video class="eap-adv-testimonial-slide__bg-video"
												src="<?php echo esc_url( $video_url ); ?>"
												<?php if ( ! empty( $image['url'] ) ) : ?>poster="<?php echo esc_url( $image['url'] ); ?>"<?php endif; ?>
												muted
												loop
												playsinline
												preload="metadata"
												autoplay>
											</video>
										<?php elseif ( $bg_image_html ) : ?>
											<?php echo $bg_image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php endif; ?>
									</div>
									<div class="eap-adv-testimonial-slide__overlay" aria-hidden="true"></div>
									<div class="eap-adv-testimonial-slide__body">
										<?php if ( $content_text ) : ?>
											<div class="eap-adv-testimonial-slide__text" data-eap-ats-clamp="<?php echo (int) $clamp; ?>"><?php echo wp_kses_post( wpautop( $content_text ) ); ?></div>
										<?php endif; ?>

										<?php if ( $enable_rm && $content_text ) : ?>
											<button type="button"
												class="eap-adv-testimonial-slide__read-more"
												data-eap-ats-open="<?php echo esc_attr( $content_popup_id ); ?>"
												hidden>
												<?php echo esc_html( $rm_text ); ?>
											</button>
										<?php endif; ?>

										<div class="eap-adv-testimonial-slide__separator" aria-hidden="true"></div>

										<div class="eap-adv-testimonial-slide__footer">
											<div class="eap-adv-testimonial-slide__user-info">
												<?php if ( $avatar_html ) : ?>
													<span class="eap-adv-testimonial-slide__avatar"><?php echo $avatar_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
												<?php endif; ?>
												<span class="eap-adv-testimonial-slide__user-text">
													<?php if ( $name ) : ?>
														<span class="eap-adv-testimonial-slide__name"><?php echo esc_html( $name ); ?></span>
													<?php endif; ?>
													<?php if ( $role ) : ?>
														<span class="eap-adv-testimonial-slide__role"><?php echo esc_html( $role ); ?></span>
													<?php endif; ?>
												</span>
											</div>

											<?php if ( $is_video ) : ?>
												<button type="button"
													class="eap-adv-testimonial-slide__play"
													data-eap-ats-open="<?php echo esc_attr( $video_popup_id ); ?>"
													aria-label="<?php esc_attr_e( 'Play video', 'elementor-animatepro' ); ?>">
													<?php echo $play_icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
												</button>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( $slider_data['navigation'] ) : ?>
					<button type="button" class="eap-adv-testimonial-slider__arrow eap-adv-testimonial-slider__arrow--next" aria-label="<?php esc_attr_e( 'Next slide', 'elementor-animatepro' ); ?>">
						<?php echo $next_icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				<?php endif; ?>
			</div>

			<?php if ( $slider_data['pagination'] ) : ?>
				<div class="eap-adv-testimonial-slider__pagination"></div>
			<?php endif; ?>

			<?php
			// Render popups OUTSIDE the swiper so Swiper clones (for loop mode)
			// don't duplicate them. Each slide's buttons reference popups by ID
			// scoped to the widget instance.
			foreach ( $slides as $index => $slide ) :
				$bg_type   = ! empty( $slide['bg_type'] ) ? $slide['bg_type'] : 'image';
				$is_video  = 'video' === $bg_type && ! empty( $slide['background_video']['url'] );
				$video_url = $is_video ? $slide['background_video']['url'] : '';
				$image     = ! empty( $slide['background_image'] ) ? $slide['background_image'] : array();
				$avatar    = ! empty( $slide['avatar'] ) ? $slide['avatar'] : array();
				$avatar_html = $this->get_image_html( $settings, $avatar, 'avatar_size' );

				$content_text = ! empty( $slide['testimonial_content'] ) ? $slide['testimonial_content'] : '';
				$name         = ! empty( $slide['name'] ) ? $slide['name'] : '';
				$role         = ! empty( $slide['designation'] ) ? $slide['designation'] : '';

				$content_popup_id = sprintf( 'eap-ats-cp-%s-%d', esc_attr( $widget_id ), $index );
				$video_popup_id   = sprintf( 'eap-ats-vp-%s-%d', esc_attr( $widget_id ), $index );
				?>
				<?php if ( $enable_rm && $content_text ) : ?>
					<div class="eap-adv-testimonial-popup eap-adv-testimonial-popup--content" id="<?php echo esc_attr( $content_popup_id ); ?>" role="dialog" aria-modal="true" aria-hidden="true" data-eap-ats-popup>
						<div class="eap-adv-testimonial-popup__backdrop" data-eap-ats-close></div>
						<div class="eap-adv-testimonial-popup__panel" role="document">
							<button type="button" class="eap-adv-testimonial-popup__close" data-eap-ats-close aria-label="<?php esc_attr_e( 'Close', 'elementor-animatepro' ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/></svg></button>
							<div class="eap-adv-testimonial-popup__header">
								<?php if ( $avatar_html ) : ?>
									<span class="eap-adv-testimonial-popup__avatar"><?php echo $avatar_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<?php endif; ?>
								<div class="eap-adv-testimonial-popup__user">
									<?php if ( $name ) : ?>
										<div class="eap-adv-testimonial-popup__name"><?php echo esc_html( $name ); ?></div>
									<?php endif; ?>
									<?php if ( $role ) : ?>
										<div class="eap-adv-testimonial-popup__role"><?php echo esc_html( $role ); ?></div>
									<?php endif; ?>
								</div>
							</div>
							<div class="eap-adv-testimonial-popup__content"><?php echo wp_kses_post( wpautop( $content_text ) ); ?></div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $is_video ) : ?>
					<div class="eap-adv-testimonial-popup eap-adv-testimonial-popup--video" id="<?php echo esc_attr( $video_popup_id ); ?>" role="dialog" aria-modal="true" aria-hidden="true" data-eap-ats-popup>
						<div class="eap-adv-testimonial-popup__backdrop" data-eap-ats-close></div>
						<div class="eap-adv-testimonial-popup__panel" role="document">
							<button type="button" class="eap-adv-testimonial-popup__close" data-eap-ats-close aria-label="<?php esc_attr_e( 'Close', 'elementor-animatepro' ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/></svg></button>
							<video class="eap-adv-testimonial-popup__video"
								src="<?php echo esc_url( $video_url ); ?>"
								controls
								playsinline
								preload="metadata"
								<?php if ( ! empty( $image['url'] ) ) : ?>poster="<?php echo esc_url( $image['url'] ); ?>"<?php endif; ?>>
							</video>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/* ------------------------------------------------------------------ *
	 * Brand Quote renderer
	 * ------------------------------------------------------------------ */

	protected function render_brand_quote( $settings ) {
		$slides = ! empty( $settings['slides'] ) && is_array( $settings['slides'] ) ? $settings['slides'] : array();
		if ( empty( $slides ) ) {
			return;
		}

		$slider_data    = $this->get_slider_settings_data( $settings );
		$image_position = ! empty( $settings['bq_image_position'] ) ? $settings['bq_image_position'] : 'right';

		$pagination_position = ! empty( $settings['pagination_position'] ) ? $settings['pagination_position'] : 'outside';

		$wrapper_classes = array(
			'eap-widget',
			'eap-adv-testimonial-slider',
			'eap-adv-testimonial-slider--brand-quote',
			'eap-adv-tbq--image-' . esc_attr( $image_position ),
			'eap-adv-testimonial-slider--pagination-' . esc_attr( $pagination_position ),
		);

		ob_start();
		if ( ! empty( $settings['prev_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['prev_icon'], array( 'aria-hidden' => 'true' ) );
		}
		$prev_icon_html = ob_get_clean();

		ob_start();
		if ( ! empty( $settings['next_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['next_icon'], array( 'aria-hidden' => 'true' ) );
		}
		$next_icon_html = ob_get_clean();
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-eap-ats-root>
			<div class="eap-adv-testimonial-slider__shell">
				<?php if ( $slider_data['navigation'] ) : ?>
					<button type="button" class="eap-adv-testimonial-slider__arrow eap-adv-testimonial-slider__arrow--prev" aria-label="<?php esc_attr_e( 'Previous slide', 'elementor-animatepro' ); ?>">
						<?php echo $prev_icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				<?php endif; ?>

				<div class="eap-adv-testimonial-slider__swiper swiper" data-eap-ats="<?php echo esc_attr( wp_json_encode( $slider_data ) ); ?>">
					<div class="swiper-wrapper">
						<?php foreach ( $slides as $slide ) :
							$avatar        = ! empty( $slide['avatar'] ) ? $slide['avatar'] : array();
							$avatar_html   = $this->get_image_html( $settings, $avatar, 'avatar_size' );
							$brand_name    = ! empty( $slide['brand_name'] ) ? $slide['brand_name'] : '';
							$content_text  = ! empty( $slide['testimonial_content'] ) ? $slide['testimonial_content'] : '';
							$name          = ! empty( $slide['name'] ) ? $slide['name'] : '';
							$role          = ! empty( $slide['designation'] ) ? $slide['designation'] : '';
							?>
							<div class="swiper-slide eap-adv-testimonial-slider__slide">
								<div class="eap-adv-tbq" data-eap-tbq-advance>
									<div class="eap-adv-tbq__text">
										<?php if ( $brand_name ) : ?>
											<div class="eap-adv-tbq__brand">
												<span class="eap-adv-tbq__brand-line" aria-hidden="true"></span>
												<span class="eap-adv-tbq__brand-name"><?php echo esc_html( $brand_name ); ?></span>
											</div>
										<?php endif; ?>

										<?php if ( $content_text ) : ?>
											<div class="eap-adv-tbq__quote"><?php echo wp_kses_post( wpautop( $content_text ) ); ?></div>
										<?php endif; ?>

										<?php if ( $name || $role ) : ?>
											<div class="eap-adv-tbq__author">
												<span class="eap-adv-tbq__author-line" aria-hidden="true"></span>
												<div class="eap-adv-tbq__author-text">
													<?php if ( $name ) : ?>
														<span class="eap-adv-tbq__name"><?php echo esc_html( $name ); ?></span>
													<?php endif; ?>
													<?php if ( $role ) : ?>
														<span class="eap-adv-tbq__role"><?php echo esc_html( $role ); ?></span>
													<?php endif; ?>
												</div>
											</div>
										<?php endif; ?>
									</div>

									<?php if ( $avatar_html ) : ?>
										<div class="eap-adv-tbq__media-wrap">
											<div class="eap-adv-tbq__media">
												<?php echo $avatar_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											</div>
											<span class="eap-adv-tbq__next-indicator" aria-hidden="true">
												<span class="eap-adv-tbq__next-text"><?php esc_html_e( 'Next', 'elementor-animatepro' ); ?></span>
												<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M7 17L17 7M9 7h8v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
											</span>
										</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( $slider_data['navigation'] ) : ?>
					<button type="button" class="eap-adv-testimonial-slider__arrow eap-adv-testimonial-slider__arrow--next" aria-label="<?php esc_attr_e( 'Next slide', 'elementor-animatepro' ); ?>">
						<?php echo $next_icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				<?php endif; ?>
			</div>

			<?php if ( $slider_data['pagination'] ) : ?>
				<div class="eap-adv-testimonial-slider__pagination"></div>
			<?php endif; ?>
		</div>
		<?php
	}

	/* ------------------------------------------------------------------ *
	 * Dual Marquee renderer
	 * ------------------------------------------------------------------ */

	protected function render_dual_marquee( $settings ) {
		$row_1_items = ! empty( $settings['marquee_row_1_items'] ) && is_array( $settings['marquee_row_1_items'] ) ? $settings['marquee_row_1_items'] : array();
		$row_2_items = ! empty( $settings['marquee_row_2_items'] ) && is_array( $settings['marquee_row_2_items'] ) ? $settings['marquee_row_2_items'] : array();

		if ( empty( $row_1_items ) && empty( $row_2_items ) ) {
			return;
		}

		$pause_on_hover = ! empty( $settings['marquee_pause_on_hover'] ) && 'yes' === $settings['marquee_pause_on_hover'];
		$row_1_dir      = ! empty( $settings['marquee_row_1_direction'] ) ? $settings['marquee_row_1_direction'] : 'left';
		$row_2_dir      = ! empty( $settings['marquee_row_2_direction'] ) ? $settings['marquee_row_2_direction'] : 'right';

		// Pre-render the quote icon once and reuse per card.
		ob_start();
		if ( ! empty( $settings['marquee_quote_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['marquee_quote_icon'], array( 'aria-hidden' => 'true' ) );
		}
		$quote_icon_html = ob_get_clean();

		$wrapper_classes = array(
			'eap-widget',
			'eap-adv-testimonial-slider',
			'eap-adv-testimonial-slider--dual-marquee',
		);
		if ( $pause_on_hover ) {
			$wrapper_classes[] = 'eap-adv-testimonial-slider--marquee-pause';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
			<div class="eap-adv-ts-marquee-wrap">
				<?php if ( ! empty( $row_1_items ) ) : ?>
					<?php $this->render_marquee_row( $settings, $row_1_items, 1, $row_1_dir, $quote_icon_html ); ?>
				<?php endif; ?>

				<?php if ( ! empty( $row_2_items ) ) : ?>
					<?php $this->render_marquee_row( $settings, $row_2_items, 2, $row_2_dir, $quote_icon_html ); ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	protected function render_marquee_row( $settings, $items, $row_num, $direction, $quote_icon_html ) {
		$row_num   = (int) $row_num;
		$direction = 'right' === $direction ? 'right' : 'left';
		?>
		<div class="eap-adv-ts-marquee eap-adv-ts-marquee--row-<?php echo (int) $row_num; ?>"
			data-direction="<?php echo esc_attr( $direction ); ?>">
			<div class="eap-adv-ts-marquee__track">
				<?php
				// Render the items twice for a seamless infinite loop. The
				// animation translates -50% so the second copy ends up exactly
				// where the first started.
				for ( $copy = 0; $copy < 2; $copy++ ) :
					foreach ( $items as $index => $item ) :
						$avatar_html = $this->get_image_html( $settings, ! empty( $item['avatar'] ) ? $item['avatar'] : array(), 'avatar_size' );
						$is_clone    = $copy === 1;
						?>
						<div class="eap-adv-ts-marquee__card"<?php echo $is_clone ? ' aria-hidden="true"' : ''; ?>>
							<?php if ( $quote_icon_html ) : ?>
								<span class="eap-adv-ts-marquee__quote-icon" aria-hidden="true"><?php echo $quote_icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $item['testimonial_content'] ) ) : ?>
								<div class="eap-adv-ts-marquee__content"><?php echo wp_kses_post( $item['testimonial_content'] ); ?></div>
							<?php endif; ?>
							<div class="eap-adv-ts-marquee__user-info">
								<?php if ( $avatar_html ) : ?>
									<span class="eap-adv-ts-marquee__avatar"><?php echo $avatar_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<?php endif; ?>
								<span class="eap-adv-ts-marquee__user-text">
									<?php if ( ! empty( $item['name'] ) ) : ?>
										<span class="eap-adv-ts-marquee__name"><?php echo esc_html( $item['name'] ); ?></span>
									<?php endif; ?>
									<?php if ( ! empty( $item['designation'] ) ) : ?>
										<span class="eap-adv-ts-marquee__role"><?php echo esc_html( $item['designation'] ); ?></span>
									<?php endif; ?>
								</span>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endfor; ?>
			</div>
		</div>
		<?php
	}
}
