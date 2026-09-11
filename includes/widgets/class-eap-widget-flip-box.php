<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Utils;

/**
 * Flip Box widget.
 *
 * Two faces — each either built from icon/image, title, text and (on the
 * back) a link, or a saved Elementor template — that flip, zoom or fade on
 * hover or click.
 *
 * Deliberate departures from the usual flip box:
 *
 * - HEIGHT BY CONSTRUCTION. Both faces sit in the same CSS grid cell, so the
 *   box is always as tall as its taller face. The common approach stacks the
 *   faces absolutely, which takes them out of flow, clips long content, and
 *   then needs JavaScript to measure and a resize handler to keep measuring.
 *   "Visible side" height (the box resizes to whichever face is showing) is
 *   the one mode that does need measuring, and uses a ResizeObserver.
 *
 * - THE HIDDEN FACE IS INERT. A back-side button is otherwise still in the tab
 *   order while the front shows, so keyboard focus lands on something the
 *   reader cannot see. The script keeps the face that is not showing `inert`.
 *
 * - ONE STATE, EVERY INPUT. The script owns the flipped state for mouse hover,
 *   touch taps (where there is no hover, a tap flips), and the keyboard (the
 *   box is focusable; Enter or Space flips, Escape flips back). CSS only draws
 *   the state. Without the script, hover still flips through a CSS fallback.
 *
 * - REDUCED MOTION gets a short crossfade instead of a rotation.
 */
class EAP_Widget_Flip_Box extends EAP_Widget_Base {

	/**
	 * Transition types.
	 *
	 * @var string[]
	 */
	const TYPES = array( 'flip-left', 'flip-right', 'flip-up', 'flip-down', 'zoom-in', 'zoom-out', 'fade' );

	/**
	 * Types that rotate in 3D (the rest swap faces in place).
	 *
	 * @var string[]
	 */
	const ROTATE_TYPES = array( 'flip-left', 'flip-right', 'flip-up', 'flip-down' );

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-flip-box';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Flip Box', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-flip-box';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'flip', 'flip box', 'flipbox', 'card', 'hover', 'rotate', '3d', 'back', 'front' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-flip-box' );
	}

	/**
	 * Scripts.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-flip-box-script' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_settings_section();

		$this->register_side_content(
			'front',
			__( 'Front', 'elementor-animatepro' ),
			array(
				'media' => 'icon',
				'title' => __( 'Flip Box', 'elementor-animatepro' ),
				'text'  => '<p>' . __( 'Hover over me, or tap, to see the other side.', 'elementor-animatepro' ) . '</p>',
			)
		);

		$this->register_side_content(
			'back',
			__( 'Back', 'elementor-animatepro' ),
			array(
				'media' => 'none',
				'title' => __( 'The Other Side', 'elementor-animatepro' ),
				'text'  => '<p>' . __( 'Put a link, a button or a whole saved template on this side.', 'elementor-animatepro' ) . '</p>',
			)
		);

		$this->register_link_section();

		$this->register_faces_style();
		$this->register_side_style( 'front', __( 'Front Side', 'elementor-animatepro' ) );
		$this->register_side_style( 'back', __( 'Back Side', 'elementor-animatepro' ) );
		$this->register_button_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	/**
	 * Settings section.
	 *
	 * @return void
	 */
	protected function register_settings_section() {
		$this->start_controls_section(
			'section_settings',
			array( 'label' => __( 'Settings', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'trigger',
			array(
				'label'       => __( 'Flip On', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'hover',
				'options'     => array(
					'hover' => __( 'Hover', 'elementor-animatepro' ),
					'click' => __( 'Click / tap', 'elementor-animatepro' ),
				),
				'description' => __( 'On touch screens a tap always flips, and the keyboard always works: Enter or Space to flip, Escape to flip back.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'flip_type',
			array(
				'label'   => __( 'Transition', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'flip-left',
				'options' => array(
					'flip-left'  => __( 'Flip left', 'elementor-animatepro' ),
					'flip-right' => __( 'Flip right', 'elementor-animatepro' ),
					'flip-up'    => __( 'Flip up', 'elementor-animatepro' ),
					'flip-down'  => __( 'Flip down', 'elementor-animatepro' ),
					'zoom-in'    => __( 'Zoom in', 'elementor-animatepro' ),
					'zoom-out'   => __( 'Zoom out', 'elementor-animatepro' ),
					'fade'       => __( 'Fade', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'depth',
			array(
				'label'        => __( '3D Depth', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Lifts the content off the card so it moves with parallax as it turns.', 'elementor-animatepro' ),
				'condition'    => array( 'flip_type' => self::ROTATE_TYPES ),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'      => __( 'Speed', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'ms' ),
				'range'      => array( 'ms' => array( 'min' => 150, 'max' => 2000, 'step' => 50 ) ),
				'default'    => array(
					'unit' => 'ms',
					'size' => 650,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fb' => '--eap-fb-speed: {{SIZE}}ms;',
				),
			)
		);

		$this->add_control(
			'easing',
			array(
				'label'   => __( 'Easing', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'smooth',
				'options' => array(
					'smooth'  => __( 'Smooth', 'elementor-animatepro' ),
					'ease'    => __( 'Ease in-out', 'elementor-animatepro' ),
					'springy' => __( 'Springy (overshoots)', 'elementor-animatepro' ),
					'linear'  => __( 'Linear', 'elementor-animatepro' ),
				),
				'selectors_dictionary' => array(
					'smooth'  => '--eap-fb-ease: cubic-bezier(0.4, 0.2, 0.2, 1);',
					'ease'    => '--eap-fb-ease: ease-in-out;',
					'springy' => '--eap-fb-ease: cubic-bezier(0.34, 1.4, 0.64, 1);',
					'linear'  => '--eap-fb-ease: linear;',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-fb' => '{{VALUE}}',
				),
			)
		);

		$this->add_control(
			'perspective',
			array(
				'label'      => __( 'Perspective', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 400, 'max' => 3000, 'step' => 50 ) ),
				'default'    => array(
					'unit' => 'px',
					'size' => 1200,
				),
				'description' => __( 'Lower is more dramatic.', 'elementor-animatepro' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fb' => '--eap-fb-perspective: {{SIZE}}px;',
				),
				'condition'  => array( 'flip_type' => self::ROTATE_TYPES ),
			)
		);

		$this->add_control(
			'height_mode',
			array(
				'label'       => __( 'Height', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'auto',
				'separator'   => 'before',
				'options'     => array(
					'auto'    => __( 'Fit the taller side', 'elementor-animatepro' ),
					'visible' => __( 'Fit the side showing', 'elementor-animatepro' ),
					'fixed'   => __( 'Fixed', 'elementor-animatepro' ),
				),
				'description' => __( 'Fit keeps every word visible on both sides. Fixed can clip long content.', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'      => __( 'Fixed Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 120, 'max' => 900 ),
					'vh' => array( 'min' => 10, 'max' => 100 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 320,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fb' => '--eap-fb-height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'height_mode' => 'fixed' ),
			)
		);

		$this->add_responsive_control(
			'min_height',
			array(
				'label'      => __( 'Minimum Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 900 ),
					'vh' => array( 'min' => 0, 'max' => 100 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fb' => '--eap-fb-min: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'height_mode!' => 'fixed' ),
			)
		);

		$this->add_control(
			'stretch',
			array(
				'label'        => __( 'Stretch To Column Height', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'eap-fb-stretch-',
				'description'  => __( 'Makes flip boxes side by side in a row the same height.', 'elementor-animatepro' ),
				'condition'    => array( 'height_mode' => 'auto' ),
			)
		);

		$this->add_control(
			'preview_back',
			array(
				'label'        => __( 'Show Back In Editor', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator'    => 'before',
				'description'  => __( 'Editor only: holds the back side in view while you style it.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * One side's content controls.
	 *
	 * @param string $side     'front' or 'back'.
	 * @param string $label    Section label.
	 * @param array  $defaults {media, title, text}.
	 * @return void
	 */
	protected function register_side_content( $side, $label, $defaults ) {
		$p       = $side . '_';
		$content = array( $p . 'content_type' => 'content' );

		$this->start_controls_section(
			'section_' . $side,
			array( 'label' => $label )
		);

		$this->add_control(
			$p . 'content_type',
			array(
				'label'   => __( 'Content', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'content',
				'options' => array(
					'content'  => __( 'Build it here', 'elementor-animatepro' ),
					'template' => __( 'Saved template', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			$p . 'template',
			array(
				'label'       => __( 'Template', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => $this->eap_get_template_options(),
				'label_block' => true,
				'condition'   => array( $p . 'content_type' => 'template' ),
			)
		);

		$this->add_control(
			$p . 'media',
			array(
				'label'     => __( 'Graphic', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => $defaults['media'],
				'toggle'    => false,
				'options'   => array(
					'none'  => array(
						'title' => __( 'None', 'elementor-animatepro' ),
						'icon'  => 'eicon-ban',
					),
					'icon'  => array(
						'title' => __( 'Icon', 'elementor-animatepro' ),
						'icon'  => 'eicon-star',
					),
					'image' => array(
						'title' => __( 'Image', 'elementor-animatepro' ),
						'icon'  => 'eicon-image',
					),
				),
				'condition' => $content,
			)
		);

		$this->add_control(
			$p . 'icon',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-lightbulb',
					'library' => 'fa-solid',
				),
				'condition' => array_merge( $content, array( $p . 'media' => 'icon' ) ),
			)
		);

		$this->add_control(
			$p . 'image',
			array(
				'label'     => __( 'Image', 'elementor-animatepro' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array_merge( $content, array( $p . 'media' => 'image' ) ),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => $p . 'image_size',
				'default'   => 'medium',
				'condition' => array_merge( $content, array( $p . 'media' => 'image' ) ),
			)
		);

		$this->add_control(
			$p . 'title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $defaults['title'],
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'separator'   => 'before',
				'condition'   => $content,
			)
		);

		$this->add_control(
			$p . 'title_tag',
			array(
				'label'     => __( 'Title HTML Tag', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => array(
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'condition' => $content,
			)
		);

		$this->add_control(
			$p . 'text',
			array(
				'label'     => __( 'Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::WYSIWYG,
				'default'   => $defaults['text'],
				'dynamic'   => array( 'active' => true ),
				'condition' => $content,
			)
		);

		$this->add_responsive_control(
			$p . 'valign',
			array(
				'label'     => __( 'Vertical Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'separator' => 'before',
				'options'   => array(
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
				'selectors' => array(
					'{{WRAPPER}} .eap-fb__face--' . $side => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			$p . 'align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
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
				'selectors_dictionary' => array(
					'left'   => 'text-align: left; --eap-fb-items: flex-start;',
					'center' => 'text-align: center; --eap-fb-items: center;',
					'right'  => 'text-align: right; --eap-fb-items: flex-end;',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-fb__face--' . $side => '{{VALUE}}',
				),
				'condition' => $content,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Back-side link section.
	 *
	 * @return void
	 */
	protected function register_link_section() {
		$this->start_controls_section(
			'section_link',
			array(
				'label'     => __( 'Link', 'elementor-animatepro' ),
				'condition' => array( 'back_content_type' => 'content' ),
			)
		);

		$this->add_control(
			'link_type',
			array(
				'label'       => __( 'Link', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'button',
				'options'     => array(
					'none'   => __( 'None', 'elementor-animatepro' ),
					'button' => __( 'Button on the back', 'elementor-animatepro' ),
					'title'  => __( 'Back title', 'elementor-animatepro' ),
					'box'    => __( 'Whole back side', 'elementor-animatepro' ),
				),
				'description' => __( 'Links live on the back: the front is what flips. With Click, a click on the front flips and a click on the back follows the link.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'link',
			array(
				'label'     => __( 'URL', 'elementor-animatepro' ),
				'type'      => Controls_Manager::URL,
				'dynamic'   => array( 'active' => true ),
				'default'   => array( 'url' => '#' ),
				'condition' => array( 'link_type!' => 'none' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'     => __( 'Button Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Learn More', 'elementor-animatepro' ),
				'condition' => array( 'link_type' => 'button' ),
			)
		);

		$this->add_control(
			'button_icon',
			array(
				'label'     => __( 'Button Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'link_type' => 'button' ),
			)
		);

		$this->add_control(
			'button_icon_position',
			array(
				'label'     => __( 'Icon Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => array(
					'before' => __( 'Before', 'elementor-animatepro' ),
					'after'  => __( 'After', 'elementor-animatepro' ),
				),
				'condition' => array( 'link_type' => 'button' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	/**
	 * Styles shared by both faces.
	 *
	 * @return void
	 */
	protected function register_faces_style() {
		$this->start_controls_section(
			'style_faces',
			array(
				'label' => __( 'Box', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'face_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fb__face' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'face_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fb__face' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'face_border',
				'selector' => '{{WRAPPER}} .eap-fb__face',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'face_shadow',
				'selector' => '{{WRAPPER}} .eap-fb__face',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * One side's style controls.
	 *
	 * @param string $side  'front' or 'back'.
	 * @param string $label Section label.
	 * @return void
	 */
	protected function register_side_style( $side, $label ) {
		$p    = $side . '_';
		$face = '{{WRAPPER}} .eap-fb__face--' . $side;

		$this->start_controls_section(
			'style_' . $side,
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => $p . 'bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => $face,
			)
		);

		$this->add_control(
			$p . 'overlay',
			array(
				'label'       => __( 'Overlay', 'elementor-animatepro' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'Tints a background image so text stays readable.', 'elementor-animatepro' ),
				'selectors'   => array(
					$face . '::before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			$p . 'color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$face => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			$p . 'media_heading',
			array(
				'label'     => __( 'Graphic', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			$p . 'icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 12, 'max' => 160 ) ),
				'selectors'  => array(
					$face => '--eap-fb-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			$p . 'icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$face . ' .eap-fb__icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			$p . 'icon_bg',
			array(
				'label'     => __( 'Icon Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$face . ' .eap-fb__icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			$p . 'icon_padding',
			array(
				'label'      => __( 'Icon Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
					'em' => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ),
				),
				'selectors'  => array(
					$face . ' .eap-fb__icon' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			$p . 'icon_radius',
			array(
				'label'      => __( 'Icon Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'selectors'  => array(
					$face . ' .eap-fb__icon' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			$p . 'image_width',
			array(
				'label'      => __( 'Image Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 20, 'max' => 600 ),
					'%'  => array( 'min' => 5, 'max' => 100 ),
				),
				'selectors'  => array(
					$face . ' .eap-fb__media img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			$p . 'image_radius',
			array(
				'label'      => __( 'Image Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 120 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'selectors'  => array(
					$face . ' .eap-fb__media img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			$p . 'media_gap',
			array(
				'label'      => __( 'Space Below Graphic', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
					'em' => array( 'min' => 0, 'max' => 4, 'step' => 0.1 ),
				),
				'selectors'  => array(
					$face => '--eap-fb-media-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			$p . 'title_heading',
			array(
				'label'     => __( 'Title', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			$p . 'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$face . ' .eap-fb__title, ' . $face . ' .eap-fb__title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $p . 'title_typography',
				'selector' => $face . ' .eap-fb__title',
			)
		);

		$this->add_responsive_control(
			$p . 'title_gap',
			array(
				'label'      => __( 'Space Below Title', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
					'em' => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ),
				),
				'selectors'  => array(
					$face => '--eap-fb-title-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			$p . 'text_heading',
			array(
				'label'     => __( 'Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			$p . 'text_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$face . ' .eap-fb__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $p . 'text_typography',
				'selector' => $face . ' .eap-fb__text',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Button style.
	 *
	 * @return void
	 */
	protected function register_button_style() {
		$this->start_controls_section(
			'style_button',
			array(
				'label'     => __( 'Button', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'back_content_type' => 'content',
					'link_type'         => 'button',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .eap-fb__button',
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fb__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fb__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_gap',
			array(
				'label'      => __( 'Space Above', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
					'em' => array( 'min' => 0, 'max' => 4, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fb' => '--eap-fb-button-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'button_tabs' );

		$this->start_controls_tab(
			'button_tab_normal',
			array( 'label' => __( 'Normal', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fb__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fb__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border',
				'selector' => '{{WRAPPER}} .eap-fb__button',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_tab_hover',
			array( 'label' => __( 'Hover', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'button_color_hover',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fb__button:hover, {{WRAPPER}} .eap-fb__button:focus-visible' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fb__button:hover, {{WRAPPER}} .eap-fb__button:focus-visible' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_border_hover',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fb__button:hover, {{WRAPPER}} .eap-fb__button:focus-visible' => 'border-color: {{VALUE}};',
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

	/**
	 * Render.
	 *
	 * @return void
	 */
	protected function render() {
		$s      = $this->get_settings_for_display();
		$editor = $this->eap_is_editor();

		$type    = in_array( $s['flip_type'] ?? 'flip-left', self::TYPES, true ) ? $s['flip_type'] : 'flip-left';
		$rotate  = in_array( $type, self::ROTATE_TYPES, true );
		$trigger = 'click' === ( $s['trigger'] ?? 'hover' ) ? 'click' : 'hover';
		$height  = in_array( $s['height_mode'] ?? 'auto', array( 'auto', 'visible', 'fixed' ), true ) ? $s['height_mode'] : 'auto';
		$preview = $editor && 'yes' === ( $s['preview_back'] ?? '' );

		$classes = array(
			'eap-widget',
			'eap-fb',
			'eap-fb--' . $trigger,
			'eap-fb--' . $type,
			$rotate ? 'eap-fb--rotate' : 'eap-fb--swap',
			'eap-fb--h-' . $height,
		);
		if ( $rotate && 'yes' === ( $s['depth'] ?? '' ) ) {
			$classes[] = 'eap-fb--depth';
		}
		if ( $preview ) {
			$classes[] = 'is-flipped';
		}

		$front_title = '';
		if ( 'content' === ( $s['front_content_type'] ?? 'content' ) ) {
			$front_title = trim( wp_strip_all_tags( (string) ( $s['front_title'] ?? '' ) ) );
		}

		$uid = 'eap-fb-' . $this->get_id();

		$config = array(
			'trigger'    => $trigger,
			'heightMode' => $height,
			'lock'       => $preview,
		);

		// Overwrite rather than append: attributes accumulate across renders
		// of one instance, which would double every value on a second render.
		$this->add_render_attribute( 'wrapper', 'class', $classes, true );
		$this->add_render_attribute( 'wrapper', 'data-eap-flip-box', wp_json_encode( $config ), true );
		$this->add_render_attribute( 'wrapper', 'tabindex', '0', true );
		$this->add_render_attribute( 'wrapper', 'role', 'group', true );
		$this->add_render_attribute( 'wrapper', 'aria-roledescription', __( 'flip card', 'elementor-animatepro' ), true );
		$this->add_render_attribute( 'wrapper', 'aria-label', '' !== $front_title ? $front_title : __( 'Flip card', 'elementor-animatepro' ), true );
		$this->add_render_attribute( 'wrapper', 'aria-describedby', $uid . '-hint', true );

		$hint = 'click' === $trigger
			? __( 'Press Enter to flip. Escape flips it back.', 'elementor-animatepro' )
			: __( 'Hover, or press Enter, to flip. Escape flips it back.', 'elementor-animatepro' );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<span class="screen-reader-text" id="<?php echo esc_attr( $uid ); ?>-hint"><?php echo esc_html( $hint ); ?></span>
			<div class="eap-fb__inner">
				<?php
				$this->render_face( 'front', $s, $editor );
				$this->render_face( 'back', $s, $editor );
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * The back side's link, when it has one.
	 *
	 * @param array $s Settings.
	 * @return string '' when there is no link.
	 */
	protected function link_type( $s ) {
		if ( 'content' !== ( $s['back_content_type'] ?? 'content' ) ) {
			return '';
		}
		$type = $s['link_type'] ?? 'button';
		if ( ! in_array( $type, array( 'button', 'title', 'box' ), true ) ) {
			return '';
		}
		if ( '' === trim( (string) ( $s['link']['url'] ?? '' ) ) ) {
			return '';
		}
		return $type;
	}

	/**
	 * One face.
	 *
	 * @param string $side   'front' or 'back'.
	 * @param array  $s      Settings.
	 * @param bool   $editor In the editor.
	 * @return void
	 */
	protected function render_face( $side, $s, $editor ) {
		$p = $side . '_';
		?>
		<div class="eap-fb__face eap-fb__face--<?php echo esc_attr( $side ); ?>">
			<div class="eap-fb__body">
				<?php
				if ( 'template' === ( $s[ $p . 'content_type' ] ?? 'content' ) ) {
					$this->render_face_template( (int) ( $s[ $p . 'template' ] ?? 0 ), $editor );
				} else {
					$this->render_face_content( $side, $s );
				}
				?>
			</div>
			<?php
			if ( 'back' === $side && 'box' === $this->link_type( $s ) ) {
				$label = trim( wp_strip_all_tags( (string) ( $s['back_title'] ?? '' ) ) );
				$this->remove_render_attribute( 'box_link' );
				$this->add_link_attributes( 'box_link', $s['link'] );
				$this->add_render_attribute( 'box_link', 'class', 'eap-fb__box-link' );
				?>
				<a <?php $this->print_render_attribute_string( 'box_link' ); ?>><span class="screen-reader-text"><?php echo esc_html( '' !== $label ? $label : __( 'Learn more', 'elementor-animatepro' ) ); ?></span></a>
				<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * A saved template as a face.
	 *
	 * @param int  $template_id Template ID.
	 * @param bool $editor      In the editor.
	 * @return void
	 */
	protected function render_face_template( $template_id, $editor ) {
		if ( ! $template_id ) {
			if ( $editor ) {
				echo '<p class="eap-fb__notice">' . esc_html__( 'Choose a saved template for this side.', 'elementor-animatepro' ) . '</p>';
			}
			return;
		}

		// A template that contains this flip box would render itself forever.
		if ( ! $this->eap_template_guard_enter( $template_id ) ) {
			if ( $editor ) {
				echo '<p class="eap-fb__notice">' . esc_html__( 'This template contains the flip box it is placed in, so it is not rendered here.', 'elementor-animatepro' ) . '</p>';
			}
			return;
		}

		$this->eap_enqueue_template_css( $template_id );
		echo $this->eap_render_template( $template_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor builder output.
		$this->eap_template_guard_leave( $template_id );
	}

	/**
	 * A face built from the widget's own fields.
	 *
	 * @param string $side 'front' or 'back'.
	 * @param array  $s    Settings.
	 * @return void
	 */
	protected function render_face_content( $side, $s ) {
		$p     = $side . '_';
		$media = $s[ $p . 'media' ] ?? 'none';
		$link  = 'back' === $side ? $this->link_type( $s ) : '';

		if ( 'icon' === $media && ! empty( $s[ $p . 'icon' ]['value'] ) ) {
			?>
			<div class="eap-fb__media eap-fb__media--icon">
				<span class="eap-fb__icon"><?php Icons_Manager::render_icon( $s[ $p . 'icon' ], array( 'aria-hidden' => 'true' ) ); ?></span>
			</div>
			<?php
		} elseif ( 'image' === $media && ! empty( $s[ $p . 'image' ]['url'] ) ) {
			?>
			<div class="eap-fb__media eap-fb__media--image">
				<?php echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $s, $p . 'image_size', $p . 'image' ) ); ?>
			</div>
			<?php
		}

		$title = (string) ( $s[ $p . 'title' ] ?? '' );
		if ( '' !== trim( $title ) ) {
			$tag = in_array( $s[ $p . 'title_tag' ] ?? 'h3', array( 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ), true ) ? $s[ $p . 'title_tag' ] : 'h3';

			echo '<' . $tag . ' class="eap-fb__title">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- whitelisted above.
			if ( 'title' === $link ) {
				$this->remove_render_attribute( 'title_link' );
				$this->add_link_attributes( 'title_link', $s['link'] );
				echo '<a ' . $this->get_render_attribute_string( 'title_link' ) . '>' . wp_kses_post( $title ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute string is escaped by Elementor.
			} else {
				echo wp_kses_post( $title );
			}
			echo '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$text = (string) ( $s[ $p . 'text' ] ?? '' );
		if ( '' !== trim( wp_strip_all_tags( $text ) ) ) {
			echo '<div class="eap-fb__text">' . wp_kses_post( $this->parse_text_editor( $text ) ) . '</div>';
		}

		if ( 'button' === $link ) {
			$this->remove_render_attribute( 'button' );
			$this->add_link_attributes( 'button', $s['link'] );
			$this->add_render_attribute( 'button', 'class', 'eap-fb__button' );

			$has_icon = ! empty( $s['button_icon']['value'] );
			$before   = $has_icon && 'before' === ( $s['button_icon_position'] ?? 'after' );
			?>
			<div class="eap-fb__actions">
				<a <?php $this->print_render_attribute_string( 'button' ); ?>>
					<?php if ( $before ) : ?>
						<span class="eap-fb__button-icon"><?php Icons_Manager::render_icon( $s['button_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
					<?php endif; ?>
					<span class="eap-fb__button-text"><?php echo esc_html( $s['button_text'] ?? __( 'Learn More', 'elementor-animatepro' ) ); ?></span>
					<?php if ( $has_icon && ! $before ) : ?>
						<span class="eap-fb__button-icon"><?php Icons_Manager::render_icon( $s['button_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
					<?php endif; ?>
				</a>
			</div>
			<?php
		}
	}
}
