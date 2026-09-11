<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

/**
 * Author Box widget.
 *
 * A profile card for the CURRENT POST'S AUTHOR: avatar, name, tagline, bio,
 * post count, social links, and a link to their archive or website. Or pin it
 * to a chosen user for an "About the editor" block.
 *
 * WHERE THIS SITS: Post Meta Info shows the author as one inline item in a
 * meta row (name + avatar, linked); Team is a static grid of people you type
 * in. This is the author's full profile, resolved from the post.
 *
 * Social links come from the user's PROFILE, not from the widget: WordPress
 * contact methods (`user_contactmethods` — the fields SEO plugins and themes
 * add under Users → Profile) plus the profile Website. Keys are matched to
 * brand icons by name, so "twitter", "x", "github", "linkedin" and friends
 * light up without configuration, and anything unrecognised still renders as
 * a generic link icon rather than being dropped.
 */
class EAP_Widget_Author_Box extends EAP_Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-author-box';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Author Box', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-person';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'author', 'bio', 'profile', 'avatar', 'writer', 'user', 'post author', 'dynamic' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-author-box' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_source_section();
		$this->register_content_section();
		$this->register_layout_section();

		$this->register_box_style();
		$this->register_avatar_style();
		$this->register_text_style();
		$this->register_button_style();
		$this->register_social_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	/**
	 * Source section.
	 *
	 * @return void
	 */
	protected function register_source_section() {
		$this->start_controls_section(
			'section_source',
			array( 'label' => __( 'Author', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'Source', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'current',
				'options' => array(
					'current' => __( 'Current post’s author', 'elementor-animatepro' ),
					'custom'  => __( 'A specific user', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'user_id',
			array(
				'label'       => __( 'User', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => $this->get_user_options(),
				'condition'   => array( 'source' => 'custom' ),
			)
		);

		$this->add_control(
			'source_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'In the editor, with no post in context, the most recent post’s author is previewed.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'source' => 'current' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content section.
	 *
	 * @return void
	 */
	protected function register_content_section() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Content', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'show_avatar',
			array(
				'label'        => __( 'Avatar', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_name',
			array(
				'label'        => __( 'Name', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'name_tag',
			array(
				'label'     => __( 'Name HTML Tag', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h4',
				'options'   => array(
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'div'  => 'div',
					'span' => 'span',
				),
				'condition' => array( 'show_name' => 'yes' ),
			)
		);

		$this->add_control(
			'name_link',
			array(
				'label'     => __( 'Name Links To', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'archive',
				'options'   => array(
					'none'    => __( 'Nothing', 'elementor-animatepro' ),
					'archive' => __( 'Author archive', 'elementor-animatepro' ),
					'website' => __( 'Profile website', 'elementor-animatepro' ),
				),
				'condition' => array( 'show_name' => 'yes' ),
			)
		);

		$this->add_control(
			'show_tagline',
			array(
				'label'        => __( 'Tagline', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'tagline_meta',
			array(
				'label'       => __( 'Tagline User Meta Key', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'job_title',
				'description' => __( 'WordPress profiles have no job-title field; name the user meta key your profile plugin stores it in. Empty falls back to the text below.', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array( 'show_tagline' => 'yes' ),
			)
		);

		$this->add_control(
			'tagline_fallback',
			array(
				'label'     => __( 'Tagline Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Author', 'elementor-animatepro' ),
				'condition' => array( 'show_tagline' => 'yes' ),
			)
		);

		$this->add_control(
			'show_bio',
			array(
				'label'        => __( 'Biography', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
				'description'  => __( 'The Biographical Info from the user’s profile.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'bio_words',
			array(
				'label'       => __( 'Trim Bio To', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 300,
				'description' => __( 'Words. 0 shows all of it.', 'elementor-animatepro' ),
				'condition'   => array( 'show_bio' => 'yes' ),
			)
		);

		$this->add_control(
			'show_count',
			array(
				'label'        => __( 'Post Count', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'count_format',
			array(
				'label'       => __( 'Post Count Format', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( '%s posts', 'elementor-animatepro' ),
				'description' => __( '%s becomes the number.', 'elementor-animatepro' ),
				'condition'   => array( 'show_count' => 'yes' ),
			)
		);

		$this->add_control(
			'show_social',
			array(
				'label'        => __( 'Social Links', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
				'description'  => __( 'From the contact fields and Website on the user’s profile.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'social_include_website',
			array(
				'label'        => __( 'Include Website', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'show_social' => 'yes' ),
			)
		);

		$this->add_control(
			'show_button',
			array(
				'label'        => __( 'Button', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'     => __( 'Button Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'View all posts', 'elementor-animatepro' ),
				'condition' => array( 'show_button' => 'yes' ),
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'     => __( 'Button Links To', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'archive',
				'options'   => array(
					'archive' => __( 'Author archive', 'elementor-animatepro' ),
					'website' => __( 'Profile website', 'elementor-animatepro' ),
					'custom'  => __( 'Custom URL', 'elementor-animatepro' ),
				),
				'condition' => array( 'show_button' => 'yes' ),
			)
		);

		$this->add_control(
			'button_url',
			array(
				'label'     => __( 'Custom URL', 'elementor-animatepro' ),
				'type'      => Controls_Manager::URL,
				'condition' => array(
					'show_button' => 'yes',
					'button_link' => 'custom',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Layout section.
	 *
	 * @return void
	 */
	protected function register_layout_section() {
		$this->start_controls_section(
			'section_layout',
			array( 'label' => __( 'Layout', 'elementor-animatepro' ) )
		);

		$this->add_responsive_control(
			'avatar_position',
			array(
				'label'   => __( 'Avatar Position', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => array(
					'left'  => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'top'   => array(
						'title' => __( 'Top', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-top',
					),
					'right' => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'prefix_class' => 'eap-ab-avatar%s-',
				'condition'    => array( 'show_avatar' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Text Alignment', 'elementor-animatepro' ),
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
				'selectors_dictionary' => array(
					'left'   => 'text-align: left; --eap-ab-justify: flex-start;',
					'center' => 'text-align: center; --eap-ab-justify: center;',
					'right'  => 'text-align: right; --eap-ab-justify: flex-end;',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-ab' => '{{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_vertical',
			array(
				'label'     => __( 'Avatar Vertical Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => __( 'Middle', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-middle',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-ab' => 'align-items: {{VALUE}};',
				),
				'condition' => array( 'show_avatar' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Avatar Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
					'em' => array( 'min' => 0, 'max' => 4, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ab' => '--eap-ab-gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'show_avatar' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	/**
	 * Box style.
	 *
	 * @return void
	 */
	protected function register_box_style() {
		$this->start_controls_section(
			'style_box',
			array(
				'label' => __( 'Box', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'box_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-ab' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .eap-ab',
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ab' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .eap-ab',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Avatar style.
	 *
	 * @return void
	 */
	protected function register_avatar_style() {
		$this->start_controls_section(
			'style_avatar',
			array(
				'label'     => __( 'Avatar', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_avatar' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'avatar_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 32, 'max' => 300 ) ),
				'default'    => array(
					'unit' => 'px',
					'size' => 96,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ab' => '--eap-ab-avatar: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'avatar_shape',
			array(
				'label'   => __( 'Shape', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'circle',
				'options' => array(
					'circle'  => __( 'Circle', 'elementor-animatepro' ),
					'rounded' => __( 'Rounded', 'elementor-animatepro' ),
					'square'  => __( 'Square', 'elementor-animatepro' ),
				),
				'selectors_dictionary' => array(
					'circle'  => '--eap-ab-avatar-radius: 50%;',
					'rounded' => '--eap-ab-avatar-radius: 16px;',
					'square'  => '--eap-ab-avatar-radius: 0;',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-ab' => '{{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'avatar_border',
				'selector' => '{{WRAPPER}} .eap-ab__avatar img',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'avatar_shadow',
				'selector' => '{{WRAPPER}} .eap-ab__avatar img',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Text style.
	 *
	 * @return void
	 */
	protected function register_text_style() {
		$this->start_controls_section(
			'style_text',
			array(
				'label' => __( 'Text', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'name_heading',
			array(
				'label' => __( 'Name', 'elementor-animatepro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-ab__name, {{WRAPPER}} .eap-ab__name a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'name_color_hover',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-ab__name a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .eap-ab__name',
			)
		);

		$this->add_control(
			'tagline_heading',
			array(
				'label'     => __( 'Tagline', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'show_tagline' => 'yes' ),
			)
		);

		$this->add_control(
			'tagline_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-ab__tagline' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_tagline' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'tagline_typography',
				'selector'  => '{{WRAPPER}} .eap-ab__tagline',
				'condition' => array( 'show_tagline' => 'yes' ),
			)
		);

		$this->add_control(
			'bio_heading',
			array(
				'label'     => __( 'Biography', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'show_bio' => 'yes' ),
			)
		);

		$this->add_control(
			'bio_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-ab__bio' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_bio' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'bio_typography',
				'selector'  => '{{WRAPPER}} .eap-ab__bio',
				'condition' => array( 'show_bio' => 'yes' ),
			)
		);

		$this->add_control(
			'count_heading',
			array(
				'label'     => __( 'Post Count', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'show_count' => 'yes' ),
			)
		);

		$this->add_control(
			'count_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-ab__count' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_count' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'count_typography',
				'selector'  => '{{WRAPPER}} .eap-ab__count',
				'condition' => array( 'show_count' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'text_gap',
			array(
				'label'      => __( 'Spacing Between Lines', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'separator'  => 'before',
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
					'em' => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ab' => '--eap-ab-stack: {{SIZE}}{{UNIT}};',
				),
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
				'condition' => array( 'show_button' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .eap-ab__button',
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ab__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .eap-ab__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .eap-ab__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-ab__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border',
				'selector' => '{{WRAPPER}} .eap-ab__button',
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
					'{{WRAPPER}} .eap-ab__button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-ab__button:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_border_hover',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-ab__button:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Social icon style.
	 *
	 * @return void
	 */
	protected function register_social_style() {
		$this->start_controls_section(
			'style_social',
			array(
				'label'     => __( 'Social Links', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_social' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'social_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 32 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ab' => '--eap-ab-social-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'social_box',
			array(
				'label'      => __( 'Button Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 64 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ab' => '--eap-ab-social-box: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'social_gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ab' => '--eap-ab-social-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'social_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 32 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-ab__social a' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'social_tabs' );

		$this->start_controls_tab(
			'social_tab_normal',
			array( 'label' => __( 'Normal', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'social_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-ab__social a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-ab__social a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'social_tab_hover',
			array( 'label' => __( 'Hover', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'social_color_hover',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-ab__social a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-ab__social a:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* =====================================================================
	 * DATA
	 * ================================================================== */

	/**
	 * Users for the custom-source picker.
	 *
	 * Limited to roles that can write, which is what "author" means here, and
	 * capped so a membership site with thousands of subscribers does not
	 * build a giant select.
	 *
	 * @return array<int,string>
	 */
	protected function get_user_options() {
		$users = get_users(
			array(
				'capability' => 'edit_posts',
				'number'     => 200,
				'orderby'    => 'display_name',
				'fields'     => array( 'ID', 'display_name' ),
			)
		);

		$options = array();
		foreach ( $users as $user ) {
			$options[ (int) $user->ID ] = $user->display_name;
		}

		return $options;
	}

	/**
	 * Resolve the author to show.
	 *
	 * @param array $settings Settings.
	 * @return WP_User|null
	 */
	public function eap_resolve_author( $settings ) {
		if ( 'custom' === ( $settings['source'] ?? 'current' ) ) {
			$id = (int) ( $settings['user_id'] ?? 0 );
			$user = $id ? get_user_by( 'id', $id ) : false;
			return $user ? $user : null;
		}

		// An author archive has no post in context but does have an author.
		if ( is_author() ) {
			$queried = get_queried_object();
			if ( $queried instanceof WP_User ) {
				return $queried;
			}
		}

		$post_id = $this->eap_get_post_id();
		if ( ! $post_id ) {
			return null;
		}

		$author_id = (int) get_post_field( 'post_author', $post_id );
		$user      = $author_id ? get_user_by( 'id', $author_id ) : false;

		return $user ? $user : null;
	}

	/**
	 * Map a contact-method key to a Font Awesome brand icon class.
	 *
	 * Matched on substrings, because plugins name these fields freely:
	 * "twitter", "twitter_url", "x_twitter", "profile_linkedin" all resolve.
	 *
	 * @param string $key Contact method key.
	 * @return string Icon class, or '' for no match.
	 */
	protected function social_icon_for( $key ) {
		$key = strtolower( $key );

		// Only glyphs present in Elementor's bundled Font Awesome, in BOTH its
		// inline-SVG JSON and its font CSS. Bluesky has neither, so a Bluesky
		// field falls through to the generic link icon rather than a blank.
		$map = array(
			'twitter'   => 'fab fa-x-twitter',
			'x_'        => 'fab fa-x-twitter',
			'facebook'  => 'fab fa-facebook-f',
			'instagram' => 'fab fa-instagram',
			'linkedin'  => 'fab fa-linkedin-in',
			'youtube'   => 'fab fa-youtube',
			'github'    => 'fab fa-github',
			'pinterest' => 'fab fa-pinterest-p',
			'tiktok'    => 'fab fa-tiktok',
			'mastodon'  => 'fab fa-mastodon',
			'threads'   => 'fab fa-threads',
			'medium'    => 'fab fa-medium',
			'dribbble'  => 'fab fa-dribbble',
			'behance'   => 'fab fa-behance',
			'tumblr'    => 'fab fa-tumblr',
			'soundcloud' => 'fab fa-soundcloud',
			'twitch'    => 'fab fa-twitch',
			'vimeo'     => 'fab fa-vimeo-v',
			'wikipedia' => 'fab fa-wikipedia-w',
			'whatsapp'  => 'fab fa-whatsapp',
			'telegram'  => 'fab fa-telegram',
		);

		// "x_" covers keys like "x_url"; a bare "x" needs an exact match so
		// "xing" is not mistaken for it.
		if ( 'x' === $key ) {
			return 'fab fa-x-twitter';
		}

		foreach ( $map as $needle => $icon ) {
			if ( false !== strpos( $key, $needle ) ) {
				return $icon;
			}
		}

		return '';
	}

	/**
	 * The user's social links: contact methods with a value, plus the Website.
	 *
	 * @param WP_User $user            User.
	 * @param bool    $include_website Whether to add user_url.
	 * @return array[] Each {key, label, url, icon}.
	 */
	public function eap_get_social_links( $user, $include_website ) {
		$links   = array();
		$methods = wp_get_user_contact_methods( $user );

		foreach ( $methods as $key => $label ) {
			$raw = trim( (string) get_the_author_meta( $key, $user->ID ) );
			if ( '' === $raw ) {
				continue;
			}

			// A contact field may hold a URL, a bare domain, or a handle. Anything
			// with an explicit scheme must be http(s) or mailto; a schemeless value
			// that looks like a domain ("x.com/you") gets https:// in front; an
			// "@handle" is skipped. It cannot be linked without knowing the
			// network's URL pattern, and esc_url_raw() alone would turn it into a
			// dead "http://@handle".
			$scheme = strtolower( (string) wp_parse_url( $raw, PHP_URL_SCHEME ) );
			if ( '' === $scheme ) {
				if ( '@' === $raw[0] || false === strpos( $raw, '.' ) || preg_match( '/\s/', $raw ) ) {
					continue;
				}
				$raw    = 'https://' . ltrim( $raw, '/' );
				$scheme = 'https';
			}
			if ( ! in_array( $scheme, array( 'http', 'https', 'mailto' ), true ) ) {
				continue;
			}
			$url = esc_url_raw( $raw, array( 'http', 'https', 'mailto' ) );
			if ( '' === $url ) {
				continue;
			}

			$links[] = array(
				'key'   => $key,
				'label' => $label,
				'url'   => $url,
				'icon'  => $this->social_icon_for( $key ),
			);
		}

		if ( $include_website ) {
			$site = trim( (string) $user->user_url );
			if ( '' !== $site ) {
				$links[] = array(
					'key'   => 'website',
					'label' => __( 'Website', 'elementor-animatepro' ),
					'url'   => esc_url_raw( $site ),
					'icon'  => 'fas fa-globe',
				);
			}
		}

		return $links;
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
		$settings = $this->get_settings_for_display();
		$editor   = $this->eap_is_editor();
		$user     = $this->eap_resolve_author( $settings );

		if ( ! $user ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-ab eap-ab--empty">' . esc_html__( 'No author to show here. Pick a user under Author → Source, or use this widget in a single-post template.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$archive = get_author_posts_url( $user->ID );
		$website = trim( (string) $user->user_url );

		$name_href = '';
		switch ( $settings['name_link'] ?? 'archive' ) {
			case 'archive':
				$name_href = $archive;
				break;
			case 'website':
				$name_href = $website;
				break;
		}

		$button_href   = '';
		$button_target = '';
		if ( 'yes' === ( $settings['show_button'] ?? 'yes' ) ) {
			switch ( $settings['button_link'] ?? 'archive' ) {
				case 'archive':
					$button_href = $archive;
					break;
				case 'website':
					$button_href   = $website;
					$button_target = '_blank';
					break;
				case 'custom':
					$button_href = (string) ( $settings['button_url']['url'] ?? '' );
					if ( ! empty( $settings['button_url']['is_external'] ) ) {
						$button_target = '_blank';
					}
					break;
			}
		}

		$tagline = '';
		if ( 'yes' === ( $settings['show_tagline'] ?? '' ) ) {
			$meta_key = trim( (string) ( $settings['tagline_meta'] ?? '' ) );
			if ( '' !== $meta_key ) {
				$tagline = trim( (string) get_the_author_meta( $meta_key, $user->ID ) );
			}
			if ( '' === $tagline ) {
				$tagline = (string) ( $settings['tagline_fallback'] ?? '' );
			}
		}

		$bio = '';
		if ( 'yes' === ( $settings['show_bio'] ?? 'yes' ) ) {
			$bio   = (string) get_the_author_meta( 'description', $user->ID );
			$words = (int) ( $settings['bio_words'] ?? 0 );
			if ( $words > 0 && '' !== $bio ) {
				$bio = wp_trim_words( wp_strip_all_tags( $bio ), $words, '…' );
			}
			if ( '' === trim( $bio ) && $editor ) {
				$bio = __( 'This author has no biography yet — it is filled in under Users → Profile → Biographical Info.', 'elementor-animatepro' );
			}
		}

		$count_text = '';
		if ( 'yes' === ( $settings['show_count'] ?? '' ) ) {
			$count      = (int) count_user_posts( $user->ID, 'post', true );
			$format     = (string) ( $settings['count_format'] ?? '%s posts' );
			$count_text = false !== strpos( $format, '%s' ) ? sprintf( $format, number_format_i18n( $count ) ) : $format . ' ' . number_format_i18n( $count );
		}

		$socials = 'yes' === ( $settings['show_social'] ?? 'yes' )
			? $this->eap_get_social_links( $user, 'yes' === ( $settings['social_include_website'] ?? 'yes' ) )
			: array();

		// These icons come from code, not from an Icons control, so Elementor
		// does not know to load their stylesheet. Inline-SVG mode needs none;
		// font mode needs the Font Awesome brands and solid sheets.
		if ( ! empty( $socials ) && ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_font_icon_svg' ) ) {
			wp_enqueue_style( 'elementor-icons-fa-brands' );
			wp_enqueue_style( 'elementor-icons-fa-solid' );
		}

		$name_tag = in_array( $settings['name_tag'] ?? 'h4', array( 'h2', 'h3', 'h4', 'h5', 'div', 'span' ), true ) ? $settings['name_tag'] : 'h4';

		$this->add_render_attribute( 'wrapper', 'class', array( 'eap-widget', 'eap-ab' ), true );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( 'yes' === ( $settings['show_avatar'] ?? 'yes' ) ) : ?>
				<div class="eap-ab__avatar">
					<?php if ( '' !== $name_href ) : ?>
						<a href="<?php echo esc_url( $name_href ); ?>" tabindex="-1" aria-hidden="true">
							<?php echo get_avatar( $user->ID, 300, '', $user->display_name, array( 'loading' => 'lazy' ) ); ?>
						</a>
					<?php else : ?>
						<?php echo get_avatar( $user->ID, 300, '', $user->display_name, array( 'loading' => 'lazy' ) ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="eap-ab__body">
				<?php if ( 'yes' === ( $settings['show_name'] ?? 'yes' ) ) : ?>
					<<?php echo $name_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- whitelisted above. ?> class="eap-ab__name">
						<?php if ( '' !== $name_href ) : ?>
							<a href="<?php echo esc_url( $name_href ); ?>"><?php echo esc_html( $user->display_name ); ?></a>
						<?php else : ?>
							<?php echo esc_html( $user->display_name ); ?>
						<?php endif; ?>
					</<?php echo $name_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php endif; ?>

				<?php if ( '' !== $tagline ) : ?>
					<div class="eap-ab__tagline"><?php echo esc_html( $tagline ); ?></div>
				<?php endif; ?>

				<?php if ( '' !== trim( $bio ) ) : ?>
					<div class="eap-ab__bio"><?php echo wp_kses_post( wpautop( $bio ) ); ?></div>
				<?php endif; ?>

				<?php if ( '' !== $count_text ) : ?>
					<div class="eap-ab__count"><?php echo esc_html( $count_text ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $socials ) || '' !== $button_href ) : ?>
					<div class="eap-ab__foot">
						<?php if ( ! empty( $socials ) ) : ?>
							<ul class="eap-ab__social">
								<?php foreach ( $socials as $link ) : ?>
									<li>
										<a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $link['label'] ); ?>" title="<?php echo esc_attr( $link['label'] ); ?>">
											<?php if ( '' !== $link['icon'] ) : ?>
												<?php Icons_Manager::render_icon( array( 'value' => $link['icon'], 'library' => 0 === strpos( $link['icon'], 'fas ' ) ? 'fa-solid' : 'fa-brands' ), array( 'aria-hidden' => 'true' ) ); ?>
											<?php else : ?>
												<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 14a4 4 0 0 0 5.66 0l3-3a4 4 0 0 0-5.66-5.66l-1.5 1.5M14 10a4 4 0 0 0-5.66 0l-3 3a4 4 0 0 0 5.66 5.66l1.5-1.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
											<?php endif; ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( '' !== $button_href ) : ?>
							<a class="eap-ab__button" href="<?php echo esc_url( $button_href ); ?>"<?php echo '' !== $button_target ? ' target="_blank" rel="noopener"' : ''; ?>>
								<?php echo esc_html( $settings['button_text'] ?? __( 'View all posts', 'elementor-animatepro' ) ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
