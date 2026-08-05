<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;

/**
 * Stacked Cards — the classic GSAP "stacking cards" scroll effect. A vertical
 * run of full-bleed colour cards; as the page scrolls each card pins near the
 * top of the viewport and the next card rises over it, while the covered cards
 * shrink and their coloured tops peek out to form a neat deck.
 *
 * Content is a structured repeater (icon, subtitle, title, description, button
 * and image with a per-card side + colour override). The animation is driven by
 * a single ScrollTrigger pin on the widget with a scrubbed transform timeline
 * (assets/js/widgets/stacked-cards.js); with no GSAP, reduced motion, in the
 * editor, or below a chosen breakpoint it degrades to a plain vertical list.
 */
class EAP_Widget_Stacked_Cards extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-stacked-cards';
	}

	public function get_title() {
		return __( 'Stacked Cards', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-nested-elements';
	}

	public function get_keywords() {
		return array( 'stacked', 'cards', 'stack', 'scroll', 'gsap', 'pin', 'sticky', 'deck' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'stacked-cards' );
	}

	public function get_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-gsap',
			'eap-gsap-scrolltrigger',
			'eap-stacked-cards-script',
		);
	}

	protected function register_controls() {
		$this->register_cards_section();
		$this->register_layout_section();
		$this->register_animation_section();

		$this->register_style_cards();
		$this->register_style_media();
		$this->register_style_icon();
		$this->register_style_subtitle();
		$this->register_style_title();
		$this->register_style_desc();
		$this->register_style_button();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_cards_section() {
		$this->start_controls_section(
			'section_cards',
			array(
				'label' => __( 'Cards', 'elementor-animatepro' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'card_icon',
			array(
				'label'   => __( 'Icon', 'elementor-animatepro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(),
			)
		);

		$repeater->add_control(
			'card_subtitle',
			array(
				'label'       => __( 'Subtitle', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'card_title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Card Title', 'elementor-animatepro' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'card_description',
			array(
				'label'   => __( 'Description', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => __( 'Unwind with the perfect balance of sweetness and fizz in every sip. Crafted to elevate your mood and keep you refreshed all day long.', 'elementor-animatepro' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'card_button_text',
			array(
				'label'       => __( 'Button Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Learn More', 'elementor-animatepro' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'card_button_link',
			array(
				'label'         => __( 'Button Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'placeholder'   => __( 'https://example.com', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'card_image',
			array(
				'label'   => __( 'Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$repeater->add_control(
			'card_image_side',
			array(
				'label'   => __( 'Image Side', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inherit',
				'options' => array(
					'inherit' => __( 'Inherit', 'elementor-animatepro' ),
					'left'    => __( 'Left', 'elementor-animatepro' ),
					'right'   => __( 'Right', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'card_colors_heading',
			array(
				'label'     => __( 'Colours', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$repeater->add_control(
			'card_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-stacked-cards__card{{CURRENT_ITEM}}' => 'background-color: {{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'card_text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-stacked-cards__card{{CURRENT_ITEM}} .eap-stacked-cards__subtitle' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-stacked-cards__card{{CURRENT_ITEM}} .eap-stacked-cards__title'    => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-stacked-cards__card{{CURRENT_ITEM}} .eap-stacked-cards__desc'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-stacked-cards__card{{CURRENT_ITEM}} .eap-stacked-cards__icon'     => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cards',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ card_title }}}',
				'default'     => $this->get_default_cards(),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Default demo cards, coloured like the reference deck.
	 *
	 * @return array
	 */
	protected function get_default_cards() {
		$img = Utils::get_placeholder_image_src();

		return array(
			array(
				'card_icon'        => array( 'value' => 'fas fa-leaf', 'library' => 'fa-solid' ),
				'card_subtitle'    => __( 'Original Taste', 'elementor-animatepro' ),
				'card_title'       => __( 'A Sip Of Pure Refreshment & Enjoyment', 'elementor-animatepro' ),
				'card_description' => __( 'Unwind with the perfect balance of sweetness and fizz in every sip. Our soft drinks are crafted to elevate your mood and keep you refreshed all day long.', 'elementor-animatepro' ),
				'card_button_text' => __( 'Grab A Bottle', 'elementor-animatepro' ),
				'card_image'       => array( 'url' => $img ),
				'card_bg'          => '#7cc04f',
				'card_text_color'  => '#ffffff',
			),
			array(
				'card_icon'        => array( 'value' => 'fas fa-bolt', 'library' => 'fa-solid' ),
				'card_subtitle'    => __( 'Bold & Fizzy', 'elementor-animatepro' ),
				'card_title'       => __( 'Flavor That Pops Your Mind Away', 'elementor-animatepro' ),
				'card_description' => __( 'Experience bold flavors and fizzy delight in every sip. Our soft drinks are crafted to bring you ultimate refreshment anytime, anywhere.', 'elementor-animatepro' ),
				'card_button_text' => __( 'Get Fizzed Now', 'elementor-animatepro' ),
				'card_image'       => array( 'url' => $img ),
				'card_bg'          => '#4653d4',
				'card_text_color'  => '#ffffff',
			),
			array(
				'card_icon'        => array( 'value' => 'fas fa-apple-alt', 'library' => 'fa-solid' ),
				'card_subtitle'    => __( 'Crisp & Cool', 'elementor-animatepro' ),
				'card_title'       => __( 'Cool, Crisp & Refreshing Sips For You', 'elementor-animatepro' ),
				'card_description' => __( 'Every bottle is packed with vibrant taste and a satisfying fizz that keeps you coming back for more. Refreshment has never felt this good.', 'elementor-animatepro' ),
				'card_button_text' => __( 'Taste It', 'elementor-animatepro' ),
				'card_image'       => array( 'url' => $img ),
				'card_bg'          => '#e8524f',
				'card_text_color'  => '#ffffff',
			),
			array(
				'card_icon'        => array( 'value' => 'fas fa-sun', 'library' => 'fa-solid' ),
				'card_subtitle'    => __( 'Summer Ready', 'elementor-animatepro' ),
				'card_title'       => __( 'Hello Summer, Hello Sunshine', 'elementor-animatepro' ),
				'card_description' => __( 'Bright, breezy and bursting with flavour. Grab a cold one and make every sunny moment a little more delicious.', 'elementor-animatepro' ),
				'card_button_text' => __( 'Shop The Range', 'elementor-animatepro' ),
				'card_image'       => array( 'url' => $img ),
				'card_bg'          => '#f0a92c',
				'card_text_color'  => '#ffffff',
			),
		);
	}

	protected function register_layout_section() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'image_position',
			array(
				'label'   => __( 'Default Image Side', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'alternate',
				'options' => array(
					'left'      => __( 'Left', 'elementor-animatepro' ),
					'right'     => __( 'Right', 'elementor-animatepro' ),
					'alternate' => __( 'Alternate', 'elementor-animatepro' ),
				),
				'description' => __( 'Each card can override this from its own Image Side control.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'content_alignment',
			array(
				'label'   => __( 'Text Alignment', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => array(
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
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'Title HTML Tag', 'elementor-animatepro' ),
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

	protected function register_animation_section() {
		$this->start_controls_section(
			'section_animation',
			array(
				'label' => __( 'Stack Animation', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'anim_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'The stacking effect runs on the live page (front end). In the editor, on touch/small screens below the chosen breakpoint, or with reduced-motion, the cards show as a simple vertical list so you can edit them.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'peek',
			array(
				'label'      => __( 'Peek Amount', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 22, 'unit' => 'px' ),
				'description' => __( 'How much of each covered card peeks out at the top.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'scale_step',
			array(
				'label'       => __( 'Depth Scale Step', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 0.15,
				'step'        => 0.005,
				'default'     => 0.05,
				'description' => __( 'How much smaller each deeper card gets (0 – 0.15). Set 0 for no shrink.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'dim',
			array(
				'label'       => __( 'Depth Dim', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 0.6,
				'step'        => 0.02,
				'default'     => 0,
				'description' => __( 'Darken deeper cards for extra depth (0 – 0.6). Set 0 for none.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'tilt',
			array(
				'label'       => __( 'Alternate Tilt (deg)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 20,
				'step'        => 0.5,
				'default'     => 0,
				'description' => __( 'Rotate the stacked (behind) cards, alternating left/right by card, for a fanned-deck look. The front card stays straight. 0 = off.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'scroll_length',
			array(
				'label'      => __( 'Scroll Length Per Card', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array( '%' => array( 'min' => 40, 'max' => 200 ) ),
				'default'    => array( 'size' => 100, 'unit' => '%' ),
				'description' => __( 'Scroll distance between each card, as a percentage of the screen height. Higher = slower.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'scrub',
			array(
				'label'       => __( 'Scrub Smoothing', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 2,
				'step'        => 0.1,
				'default'     => 0.6,
				'description' => __( 'How softly the animation follows the scroll (0 – 2). 0 = locked to scroll.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'disable_below',
			array(
				'label'   => __( 'Disable Stacking Below', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'mobile',
				'options' => array(
					'none'   => __( 'Never (always stack)', 'elementor-animatepro' ),
					'mobile' => __( 'Mobile (768px)', 'elementor-animatepro' ),
					'tablet' => __( 'Tablet (1025px)', 'elementor-animatepro' ),
				),
				'description' => __( 'On narrower screens the cards show as a plain vertical list instead of stacking.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_style_cards() {
		$this->start_controls_section(
			'section_style_cards',
			array(
				'label' => __( 'Cards', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'card_max_width',
			array(
				'label'      => __( 'Max Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 400, 'max' => 1600 ),
					'%'  => array( 'min' => 40, 'max' => 100 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'card_min_height',
			array(
				'label'      => __( 'Card Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 240, 'max' => 900 ),
					'vh' => array( 'min' => 30, 'max' => 100 ),
				),
				'default'    => array( 'size' => 440, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards__card' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => 48,
					'right'    => 56,
					'bottom'   => 48,
					'left'     => 56,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'top'      => 28,
					'right'    => 28,
					'bottom'   => 28,
					'left'     => 28,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards__card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'card_bg_color',
			array(
				'label'     => __( 'Default Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7cc04f',
				'selectors' => array(
					'{{WRAPPER}} .eap-stacked-cards__card' => 'background-color: {{VALUE}};',
				),
				'description' => __( 'Fallback colour for cards that do not set their own Background.', 'elementor-animatepro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .eap-stacked-cards__card',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eap-stacked-cards__card',
			)
		);

		$this->add_responsive_control(
			'card_fallback_gap',
			array(
				'label'      => __( 'Gap (list mode)', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards:not(.is-pinned) .eap-stacked-cards__card:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'description' => __( 'Spacing between cards when they are shown as a plain list.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_media() {
		$this->start_controls_section(
			'section_style_media',
			array(
				'label' => __( 'Image', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'      => __( 'Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array( 'min' => 15, 'max' => 70 ),
					'px' => array( 'min' => 120, 'max' => 700 ),
				),
				'default'    => array( 'size' => 42, 'unit' => '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards' => '--eap-sc-img-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'media_gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'    => array( 'size' => 40, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards' => '--eap-sc-media-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'image_fit',
			array(
				'label'     => __( 'Object Fit', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => array(
					'cover'   => __( 'Cover', 'elementor-animatepro' ),
					'contain' => __( 'Contain', 'elementor-animatepro' ),
					'fill'    => __( 'Fill', 'elementor-animatepro' ),
					'none'    => __( 'None', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-stacked-cards__img' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 80, 'max' => 700 ),
					'%'  => array( 'min' => 10, 'max' => 100 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards__img' => 'height: {{SIZE}}{{UNIT}};',
				),
				'description' => __( 'Leave empty to keep the natural image height.', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'image_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 16,
					'right'    => 16,
					'bottom'   => 16,
					'left'     => 16,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards__img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_icon() {
		$this->start_controls_section(
			'section_style_icon',
			array(
				'label' => __( 'Icon', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 12, 'max' => 120 ) ),
				'default'    => array( 'size' => 40, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards__icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-stacked-cards__icon'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-stacked-cards__icon svg' => 'fill: {{VALUE}};',
				),
				'description' => __( 'Overrides the per-card text colour for the icon only.', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'icon_box_size',
			array(
				'label'      => __( 'Box Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 160 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'description' => __( 'Set a box size to show a filled/rounded icon badge. Leave empty for a bare icon.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'icon_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-stacked-cards__icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_box_radius',
			array(
				'label'      => __( 'Box Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 100 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards__icon' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 20, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards__icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_subtitle() {
		$this->start_controls_section(
			'section_style_subtitle',
			array(
				'label' => __( 'Subtitle', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-stacked-cards__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .eap-stacked-cards__subtitle',
			)
		);

		$this->add_responsive_control(
			'subtitle_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards__subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_title() {
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
				'selectors' => array(
					'{{WRAPPER}} .eap-stacked-cards__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'selector'       => '{{WRAPPER}} .eap-stacked-cards__title',
				'fields_options' => array(
					'typography'  => array( 'default' => 'custom' ),
					'font_size'   => array( 'default' => array( 'size' => 34, 'unit' => 'px' ) ),
					'font_weight' => array( 'default' => '700' ),
					'line_height' => array( 'default' => array( 'size' => 1.2, 'unit' => 'em' ) ),
				),
			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
				'default'    => array( 'size' => 16, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_desc() {
		$this->start_controls_section(
			'section_style_desc',
			array(
				'label' => __( 'Description', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-stacked-cards__desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .eap-stacked-cards__desc',
			)
		);

		$this->add_responsive_control(
			'desc_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 26, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards__desc' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_button() {
		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => __( 'Button', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'button_typography',
				'selector'       => '{{WRAPPER}} .eap-stacked-cards__button',
				'fields_options' => array(
					'typography'  => array( 'default' => 'custom' ),
					'font_weight' => array( 'default' => '600' ),
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 14,
					'right'    => 32,
					'bottom'   => 14,
					'left'     => 32,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 40,
					'right'    => 40,
					'bottom'   => 40,
					'left'     => 40,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'button_border',
				'selector'       => '{{WRAPPER}} .eap-stacked-cards__button',
				'fields_options' => array(
					'border' => array( 'default' => 'solid' ),
					'width'  => array( 'default' => array( 'top' => 2, 'right' => 2, 'bottom' => 2, 'left' => 2, 'unit' => 'px', 'isLinked' => true ) ),
					'color'  => array( 'default' => 'rgba(255,255,255,0.85)' ),
				),
			)
		);

		$this->start_controls_tabs( 'button_tabs' );

		$this->start_controls_tab( 'button_tab_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-stacked-cards__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0)',
				'selectors' => array(
					'{{WRAPPER}} .eap-stacked-cards__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'button_tab_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'button_color_hover',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1f2937',
				'selectors' => array(
					'{{WRAPPER}} .eap-stacked-cards__button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-stacked-cards__button:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-stacked-cards__button:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_spacing',
			array(
				'label'      => __( 'Top Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-stacked-cards__button' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$cards    = ( ! empty( $settings['cards'] ) && is_array( $settings['cards'] ) ) ? $settings['cards'] : array();

		if ( empty( $cards ) ) {
			return;
		}

		$img_default = ! empty( $settings['image_position'] ) ? $settings['image_position'] : 'alternate';
		$align       = ! empty( $settings['content_alignment'] ) ? $settings['content_alignment'] : 'left';

		$tag = ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h3';
		$tag = in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ), true ) ? $tag : 'h3';

		$disable_map   = array(
			'none'   => 0,
			'mobile' => 768,
			'tablet' => 1025,
		);
		$disable_below = isset( $disable_map[ $settings['disable_below'] ?? 'mobile' ] ) ? $disable_map[ $settings['disable_below'] ] : 768;

		$this->add_render_attribute(
			'wrapper',
			'class',
			array(
				'eap-widget',
				'eap-stacked-cards',
				'eap-stacked-cards--align-' . $align,
			)
		);

		$this->add_render_attribute(
			'wrapper',
			array(
				'data-eap-stacked-cards' => '',
				'data-peek'              => (string) $this->slider_size( $settings, 'peek', 22 ),
				'data-scale-step'        => (string) $this->num_setting( $settings, 'scale_step', 0.05 ),
				'data-dim'               => (string) $this->num_setting( $settings, 'dim', 0 ),
				'data-tilt'              => (string) $this->num_setting( $settings, 'tilt', 0 ),
				'data-scroll-length'     => (string) $this->slider_size( $settings, 'scroll_length', 100 ),
				'data-scrub'             => (string) $this->num_setting( $settings, 'scrub', 0.6 ),
				'data-disable-below'     => (string) $disable_below,
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="eap-stacked-cards__stage">
				<?php
				foreach ( $cards as $index => $card ) {
					$this->render_card( $card, $index, $img_default, $tag );
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Read a SLIDER control's numeric size with a fallback.
	 *
	 * @param array  $settings Settings.
	 * @param string $key      Control key.
	 * @param float  $default  Default value.
	 * @return float
	 */
	protected function slider_size( $settings, $key, $default ) {
		if ( isset( $settings[ $key ]['size'] ) && '' !== $settings[ $key ]['size'] ) {
			return (float) $settings[ $key ]['size'];
		}

		return (float) $default;
	}

	/**
	 * Read a NUMBER control's value with a fallback.
	 *
	 * @param array  $settings Settings.
	 * @param string $key      Control key.
	 * @param float  $default  Default value.
	 * @return float
	 */
	protected function num_setting( $settings, $key, $default ) {
		if ( isset( $settings[ $key ] ) && '' !== $settings[ $key ] && is_numeric( $settings[ $key ] ) ) {
			return (float) $settings[ $key ];
		}

		return (float) $default;
	}

	protected function render_card( $card, $index, $img_default, $tag ) {
		$item_id = ! empty( $card['_id'] ) ? $card['_id'] : (string) $index;

		$side = ! empty( $card['card_image_side'] ) ? $card['card_image_side'] : 'inherit';
		if ( 'inherit' === $side ) {
			if ( 'alternate' === $img_default ) {
				$side = ( 0 === $index % 2 ) ? 'left' : 'right';
			} else {
				$side = ( 'right' === $img_default ) ? 'right' : 'left';
			}
		}

		$key = 'card_' . $index;
		$this->add_render_attribute(
			$key,
			'class',
			array(
				'eap-stacked-cards__card',
				'elementor-repeater-item-' . $item_id,
				'eap-stacked-cards__card--img-' . $side,
			)
		);
		$this->add_render_attribute( $key, 'data-index', (string) $index );

		$subtitle = trim( (string) ( $card['card_subtitle'] ?? '' ) );
		$title    = trim( (string) ( $card['card_title'] ?? '' ) );
		$desc     = trim( (string) ( $card['card_description'] ?? '' ) );
		$btn_text = trim( (string) ( $card['card_button_text'] ?? '' ) );

		$has_icon = ! empty( $card['card_icon']['value'] );
		$has_img  = ! empty( $card['card_image']['url'] );
		$has_btn  = ( '' !== $btn_text );
		?>
		<div <?php $this->print_render_attribute_string( $key ); ?>>
			<div class="eap-stacked-cards__inner">
				<?php if ( $has_img ) : ?>
					<div class="eap-stacked-cards__media">
						<img class="eap-stacked-cards__img" src="<?php echo esc_url( $card['card_image']['url'] ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
					</div>
				<?php endif; ?>
				<div class="eap-stacked-cards__body">
					<?php if ( $has_icon ) : ?>
						<span class="eap-stacked-cards__icon"><?php Icons_Manager::render_icon( $card['card_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $subtitle ) : ?>
						<div class="eap-stacked-cards__subtitle"><?php echo wp_kses_post( $subtitle ); ?></div>
					<?php endif; ?>
					<?php if ( '' !== $title ) : ?>
						<<?php echo esc_html( $tag ); ?> class="eap-stacked-cards__title"><?php echo wp_kses_post( $title ); ?></<?php echo esc_html( $tag ); ?>>
					<?php endif; ?>
					<?php if ( '' !== $desc ) : ?>
						<div class="eap-stacked-cards__desc"><?php echo wp_kses_post( wpautop( $desc ) ); ?></div>
					<?php endif; ?>
					<?php
					if ( $has_btn ) {
						$btn_key = $key . '_btn';
						$this->add_render_attribute( $btn_key, 'class', 'eap-stacked-cards__button' );
						if ( ! empty( $card['card_button_link']['url'] ) ) {
							$this->add_link_attributes( $btn_key, $card['card_button_link'] );
						}
						?>
						<a <?php $this->print_render_attribute_string( $btn_key ); ?>><?php echo wp_kses_post( $btn_text ); ?></a>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}
}
