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

/**
 * Social Share — buttons that share the CURRENT page (not link to your profiles;
 * that is the Social Icons widget). Supports the major networks plus utility
 * actions (Email, Copy Link, Print, and the mobile "Share…" Web Share API).
 * Two layouts: inline (drop anywhere) and a fixed floating rail. Network links
 * open in a centered popup; Copy/Print/Native are handled by social-share.js.
 * Official brand colours are on by default.
 */
class EAP_Widget_Social_Share extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-social-share';
	}

	public function get_title() {
		return __( 'Social Share', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-share';
	}

	public function get_keywords() {
		return array( 'social', 'share', 'sharing', 'facebook', 'twitter', 'x', 'whatsapp', 'copy', 'floating' );
	}

	public function get_style_depends() {
		return array( 'eap-core', 'eap-social-share', 'elementor-icons-fa-solid', 'elementor-icons-fa-brands' );
	}

	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-social-share-script' );
	}

	/**
	 * Network definitions: label, Font Awesome icon, brand colour, behaviour
	 * type, and share-URL template with {url}/{title}/{image} tokens.
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function get_networks() {
		return array(
			'facebook'  => array( 'label' => 'Facebook', 'icon' => 'fab fa-facebook-f', 'color' => '#1877f2', 'type' => 'popup', 'url' => 'https://www.facebook.com/sharer/sharer.php?u={url}' ),
			'x'         => array( 'label' => 'X', 'icon' => 'fab fa-x-twitter', 'color' => '#000000', 'type' => 'popup', 'url' => 'https://twitter.com/intent/tweet?url={url}&text={title}' ),
			'linkedin'  => array( 'label' => 'LinkedIn', 'icon' => 'fab fa-linkedin-in', 'color' => '#0a66c2', 'type' => 'popup', 'url' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}' ),
			'whatsapp'  => array( 'label' => 'WhatsApp', 'icon' => 'fab fa-whatsapp', 'color' => '#25d366', 'type' => 'popup', 'url' => 'https://api.whatsapp.com/send?text={title}%20{url}' ),
			'telegram'  => array( 'label' => 'Telegram', 'icon' => 'fab fa-telegram', 'color' => '#0088cc', 'type' => 'popup', 'url' => 'https://t.me/share/url?url={url}&text={title}' ),
			'pinterest' => array( 'label' => 'Pinterest', 'icon' => 'fab fa-pinterest-p', 'color' => '#e60023', 'type' => 'popup', 'url' => 'https://pinterest.com/pin/create/button/?url={url}&media={image}&description={title}' ),
			'reddit'    => array( 'label' => 'Reddit', 'icon' => 'fab fa-reddit-alien', 'color' => '#ff4500', 'type' => 'popup', 'url' => 'https://www.reddit.com/submit?url={url}&title={title}' ),
			'tumblr'    => array( 'label' => 'Tumblr', 'icon' => 'fab fa-tumblr', 'color' => '#35465c', 'type' => 'popup', 'url' => 'https://www.tumblr.com/share/link?url={url}&name={title}' ),
			'vk'        => array( 'label' => 'VK', 'icon' => 'fab fa-vk', 'color' => '#0077ff', 'type' => 'popup', 'url' => 'https://vk.com/share.php?url={url}&title={title}' ),
			'email'     => array( 'label' => 'Email', 'icon' => 'fas fa-envelope', 'color' => '#777777', 'type' => 'mailto', 'url' => 'mailto:?subject={title}&body={url}' ),
			'copy'      => array( 'label' => 'Copy Link', 'icon' => 'fas fa-link', 'color' => '#4a5568', 'type' => 'copy', 'url' => '' ),
			'print'     => array( 'label' => 'Print', 'icon' => 'fas fa-print', 'color' => '#4a5568', 'type' => 'print', 'url' => '' ),
			'native'    => array( 'label' => 'Share', 'icon' => 'fas fa-share-nodes', 'color' => '#4a5568', 'type' => 'native', 'url' => '' ),
			'custom'    => array( 'label' => 'Custom', 'icon' => 'fas fa-share', 'color' => '#4a5568', 'type' => 'popup', 'url' => '' ),
		);
	}

	protected function register_controls() {
		$this->register_buttons_section();
		$this->register_target_section();
		$this->register_layout_section();

		$this->register_button_style();
		$this->register_label_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_buttons_section() {
		$this->start_controls_section(
			'section_buttons',
			array(
				'label' => __( 'Share Buttons', 'elementor-animatepro' ),
			)
		);

		$options = array();
		foreach ( self::get_networks() as $key => $net ) {
			$options[ $key ] = $net['label'];
		}

		$repeater = new Repeater();

		$repeater->add_control(
			'network',
			array(
				'label'   => __( 'Network', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'facebook',
				'options' => $options,
			)
		);

		$repeater->add_control(
			'custom_url',
			array(
				'label'         => __( 'Custom URL', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'condition'     => array( 'network' => 'custom' ),
				'description'   => __( 'You can use {url} and {title} tokens.', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'custom_label',
			array(
				'label'       => __( 'Label', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Defaults to the network name', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'custom_icon',
			array(
				'label'       => __( 'Icon', 'elementor-animatepro' ),
				'type'        => Controls_Manager::ICONS,
				'description' => __( 'Optional — overrides the default network icon.', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'item_color',
			array(
				'label'       => __( 'Brand Color', 'elementor-animatepro' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'Optional — overrides this button\'s official colour.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'share_buttons',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ network }}}',
				'default'     => array(
					array( 'network' => 'facebook' ),
					array( 'network' => 'x' ),
					array( 'network' => 'linkedin' ),
					array( 'network' => 'whatsapp' ),
					array( 'network' => 'copy' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_target_section() {
		$this->start_controls_section(
			'section_target',
			array(
				'label' => __( 'Share Content', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'share_source',
			array(
				'label'   => __( 'Share', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'current',
				'options' => array(
					'current' => __( 'Current Page', 'elementor-animatepro' ),
					'custom'  => __( 'Custom', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'custom_share_url',
			array(
				'label'         => __( 'URL', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'placeholder'   => __( 'https://example.com/page', 'elementor-animatepro' ),
				'condition'     => array( 'share_source' => 'custom' ),
			)
		);

		$this->add_control(
			'custom_share_title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => array( 'share_source' => 'custom' ),
			)
		);

		$this->add_control(
			'custom_share_desc',
			array(
				'label'     => __( 'Description', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'condition' => array( 'share_source' => 'custom' ),
			)
		);

		$this->add_control(
			'share_image',
			array(
				'label'       => __( 'Image (Pinterest)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Used by Pinterest. Defaults to the current post\'s featured image.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_layout_section() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'layout_mode',
			array(
				'label'   => __( 'Mode', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => array(
					'inline'   => __( 'Inline', 'elementor-animatepro' ),
					'floating' => __( 'Floating Bar', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'button_content',
			array(
				'label'   => __( 'Button Content', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => array(
					'icon'       => __( 'Icon Only', 'elementor-animatepro' ),
					'icon_label' => __( 'Icon + Label', 'elementor-animatepro' ),
					'label'      => __( 'Label Only', 'elementor-animatepro' ),
				),
			)
		);

		/* --- Inline options --- */
		$this->add_responsive_control(
			'direction',
			array(
				'label'     => __( 'Direction', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'row',
				'options'   => array(
					'row'    => array(
						'title' => __( 'Row', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-center',
					),
					'column' => array(
						'title' => __( 'Column', 'elementor-animatepro' ),
						'icon'  => 'eicon-v-align-middle',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-social-share' => 'flex-direction: {{VALUE}};',
				),
				'condition' => array( 'layout_mode' => 'inline' ),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'                => __( 'Alignment', 'elementor-animatepro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'left',
				'options'              => array(
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
				'selectors'            => array(
					'{{WRAPPER}} .eap-social-share' => 'justify-content: {{VALUE}};',
				),
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
				'condition'            => array( 'layout_mode' => 'inline' ),
			)
		);

		/* --- Floating options --- */
		$this->add_control(
			'float_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'The floating bar is fixed to the screen edge on the live page. In the editor it shows inline so you can style it.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'layout_mode' => 'floating' ),
			)
		);

		$this->add_control(
			'float_side',
			array(
				'label'     => __( 'Side', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => array(
					'left'  => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'condition' => array( 'layout_mode' => 'floating' ),
			)
		);

		$this->add_control(
			'float_valign',
			array(
				'label'     => __( 'Vertical Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'middle',
				'options'   => array(
					'top'    => __( 'Top', 'elementor-animatepro' ),
					'middle' => __( 'Middle', 'elementor-animatepro' ),
					'bottom' => __( 'Bottom', 'elementor-animatepro' ),
				),
				'condition' => array( 'layout_mode' => 'floating' ),
			)
		);

		$this->add_control(
			'float_offset',
			array(
				'label'      => __( 'Edge Offset', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'size' => 0, 'unit' => 'px' ),
				'condition'  => array( 'layout_mode' => 'floating' ),
			)
		);

		$this->add_control(
			'float_zindex',
			array(
				'label'     => __( 'Z-Index', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 99,
				'min'       => 0,
				'condition' => array( 'layout_mode' => 'floating' ),
			)
		);

		$this->add_control(
			'float_hide_mobile',
			array(
				'label'        => __( 'Hide on Mobile', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'layout_mode' => 'floating' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_button_style() {
		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => __( 'Buttons', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'use_brand_colors',
			array(
				'label'        => __( 'Official Brand Colors', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'On', 'elementor-animatepro' ),
				'label_off'    => __( 'Off', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'shape',
			array(
				'label'   => __( 'Shape', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'rounded',
				'options' => array(
					'square'  => __( 'Square', 'elementor-animatepro' ),
					'rounded' => __( 'Rounded', 'elementor-animatepro' ),
					'circle'  => __( 'Circle', 'elementor-animatepro' ),
					'pill'    => __( 'Pill', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'button_size',
			array(
				'label'      => __( 'Button Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 28, 'max' => 90 ) ),
				'default'    => array( 'size' => 44, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-share' => '--eap-ss-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 48 ) ),
				'default'    => array( 'size' => 18, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-share__icon'     => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-social-share__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-share' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'h_padding',
			array(
				'label'      => __( 'Horizontal Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-share__btn' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
				),
				'description' => __( 'Overrides the default. Icon-only buttons stay square at 0.', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 50 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-share--shape-rounded .eap-social-share__btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'shape' => 'rounded' ),
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'label'   => __( 'Hover Effect', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grow',
				'options' => array(
					'none' => __( 'None', 'elementor-animatepro' ),
					'grow' => __( 'Grow', 'elementor-animatepro' ),
					'lift' => __( 'Lift', 'elementor-animatepro' ),
				),
			)
		);

		/* --- Custom colours (only when brand colours are off) --- */
		$this->start_controls_tabs(
			'color_tabs',
			array( 'condition' => array( 'use_brand_colors!' => 'yes' ) )
		);

		$this->start_controls_tab( 'color_tab_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ), 'condition' => array( 'use_brand_colors!' => 'yes' ) ) );

		$this->add_control(
			'btn_color',
			array(
				'label'     => __( 'Icon / Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-social-share:not(.eap-social-share--brand) .eap-social-share__btn'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-social-share:not(.eap-social-share--brand) .eap-social-share__icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4a5568',
				'selectors' => array(
					'{{WRAPPER}} .eap-social-share:not(.eap-social-share--brand) .eap-social-share__btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'color_tab_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ), 'condition' => array( 'use_brand_colors!' => 'yes' ) ) );

		$this->add_control(
			'btn_color_hover',
			array(
				'label'     => __( 'Icon / Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-social-share:not(.eap-social-share--brand) .eap-social-share__btn:hover'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-social-share:not(.eap-social-share--brand) .eap-social-share__btn:hover .eap-social-share__icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-social-share:not(.eap-social-share--brand) .eap-social-share__btn:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'btn_border',
				'selector' => '{{WRAPPER}} .eap-social-share__btn',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'btn_shadow',
				'selector' => '{{WRAPPER}} .eap-social-share__btn',
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
				'condition' => array( 'button_content!' => 'icon' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .eap-social-share__label',
			)
		);

		$this->add_responsive_control(
			'label_gap',
			array(
				'label'      => __( 'Icon Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 24 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-social-share__btn' => '--eap-ss-label-gap: {{SIZE}}{{UNIT}};',
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
		$items    = ! empty( $settings['share_buttons'] ) && is_array( $settings['share_buttons'] ) ? $settings['share_buttons'] : array();

		if ( empty( $items ) ) {
			return;
		}

		$networks = self::get_networks();
		$target   = $this->resolve_target( $settings );

		$mode      = ! empty( $settings['layout_mode'] ) ? $settings['layout_mode'] : 'inline';
		$content   = ! empty( $settings['button_content'] ) ? $settings['button_content'] : 'icon';
		$shape     = ! empty( $settings['shape'] ) ? $settings['shape'] : 'rounded';
		$brand     = 'yes' === ( $settings['use_brand_colors'] ?? 'yes' );
		$hover     = ! empty( $settings['hover_effect'] ) ? $settings['hover_effect'] : 'grow';
		$is_editor = class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode();
		$floating  = ( 'floating' === $mode ) && ! $is_editor;

		$classes = array(
			'eap-widget',
			'eap-social-share',
			'eap-social-share--content-' . $content,
			'eap-social-share--shape-' . $shape,
			'eap-social-share--hover-' . $hover,
		);
		if ( $brand ) {
			$classes[] = 'eap-social-share--brand';
		}
		if ( 'floating' === $mode ) {
			$classes[] = 'eap-social-share--floating-mode';
		}
		if ( $floating ) {
			$side   = ( 'right' === ( $settings['float_side'] ?? 'left' ) ) ? 'right' : 'left';
			$valign = in_array( $settings['float_valign'] ?? 'middle', array( 'top', 'middle', 'bottom' ), true ) ? $settings['float_valign'] : 'middle';
			$classes[] = 'eap-social-share--floating';
			$classes[] = 'eap-social-share--side-' . $side;
			$classes[] = 'eap-social-share--valign-' . $valign;
			if ( 'yes' === ( $settings['float_hide_mobile'] ?? 'yes' ) ) {
				$classes[] = 'eap-social-share--hide-mobile';
			}
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		$this->add_render_attribute( 'wrapper', 'data-eap-social-share', '' );

		if ( $floating ) {
			$offset = isset( $settings['float_offset']['size'] ) ? (float) $settings['float_offset']['size'] : 0;
			$zidx   = isset( $settings['float_zindex'] ) && '' !== $settings['float_zindex'] ? (int) $settings['float_zindex'] : 99;
			$this->add_render_attribute( 'wrapper', 'style', '--eap-ss-offset:' . $offset . 'px;--eap-ss-z:' . $zidx . ';' );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php
			foreach ( $items as $index => $item ) {
				$this->render_button( $item, $index, $networks, $target, $content );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Resolve the URL / title / description / image to share.
	 *
	 * @param array $settings Settings.
	 * @return array{url:string,title:string,desc:string,image:string}
	 */
	protected function resolve_target( $settings ) {
		$url   = '';
		$title = '';
		$desc  = '';
		$image = '';

		if ( 'custom' === ( $settings['share_source'] ?? 'current' ) ) {
			$url   = ! empty( $settings['custom_share_url']['url'] ) ? $settings['custom_share_url']['url'] : '';
			$title = ! empty( $settings['custom_share_title'] ) ? $settings['custom_share_title'] : '';
			$desc  = ! empty( $settings['custom_share_desc'] ) ? $settings['custom_share_desc'] : '';
		} else {
			$post_id = get_queried_object_id();
			if ( $post_id && is_singular() ) {
				$url   = get_permalink( $post_id );
				$title = get_the_title( $post_id );
			} else {
				$url   = home_url( isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/' );
				$title = wp_get_document_title();
			}
		}

		if ( ! empty( $settings['share_image']['url'] ) ) {
			$image = $settings['share_image']['url'];
		} elseif ( 'custom' !== ( $settings['share_source'] ?? 'current' ) ) {
			$post_id = get_queried_object_id();
			if ( $post_id && has_post_thumbnail( $post_id ) ) {
				$image = get_the_post_thumbnail_url( $post_id, 'large' );
			}
		}

		if ( '' === $desc ) {
			$desc = $title;
		}

		return array(
			'url'   => $url,
			'title' => $title,
			'desc'  => $desc,
			'image' => $image,
		);
	}

	protected function render_button( $item, $index, $networks, $target, $content ) {
		$key = ! empty( $item['network'] ) ? $item['network'] : 'custom';
		$def = isset( $networks[ $key ] ) ? $networks[ $key ] : $networks['custom'];

		$item_id = ! empty( $item['_id'] ) ? $item['_id'] : (string) $index;
		$type    = $def['type'];
		$label   = ! empty( $item['custom_label'] ) ? $item['custom_label'] : $def['label'];
		$color   = ! empty( $item['item_color'] ) ? $item['item_color'] : $def['color'];

		// Build the href.
		$href = '#';
		if ( 'custom' === $key ) {
			$template = ! empty( $item['custom_url']['url'] ) ? $item['custom_url']['url'] : '';
			$href     = '' !== $template ? $this->fill_tokens( $template, $target ) : '#';
		} elseif ( in_array( $type, array( 'popup', 'mailto' ), true ) && ! empty( $def['url'] ) ) {
			$href = $this->fill_tokens( $def['url'], $target );
		}

		$btn_key = 'btn_' . $index;
		$this->add_render_attribute(
			$btn_key,
			array(
				'class' => array(
					'eap-social-share__btn',
					'eap-social-share__btn--' . $key,
					'elementor-repeater-item-' . $item_id,
				),
				'href'  => $href,
				'style' => '--eap-ss-brand:' . $color . ';',
				'data-eap-share' => $type,
			)
		);

		if ( 'popup' === $type ) {
			$this->add_render_attribute( $btn_key, array( 'target' => '_blank', 'rel' => 'noopener noreferrer' ) );
		}
		if ( 'copy' === $type ) {
			$this->add_render_attribute( $btn_key, 'data-eap-share-url', $target['url'] );
		}
		if ( in_array( $type, array( 'copy', 'print', 'native' ), true ) ) {
			$this->add_render_attribute( $btn_key, 'role', 'button' );
		}
		if ( 'native' === $type ) {
			$this->add_render_attribute(
				$btn_key,
				array(
					'data-eap-share-title' => $target['title'],
					'data-eap-share-url'   => $target['url'],
				)
			);
		}

		$aria = $label;
		$this->add_render_attribute( $btn_key, 'aria-label', $aria );
		?>
		<a <?php $this->print_render_attribute_string( $btn_key ); ?>>
			<?php if ( 'label' !== $content ) : ?>
				<span class="eap-social-share__icon"><?php $this->render_icon( $def['icon'], $item['custom_icon'] ?? array() ); ?></span>
			<?php endif; ?>
			<?php if ( 'icon' !== $content ) : ?>
				<span class="eap-social-share__label"><?php echo esc_html( $label ); ?></span>
			<?php endif; ?>
			<?php if ( 'copy' === $type ) : ?>
				<span class="eap-social-share__copied"><?php echo esc_html__( 'Copied!', 'elementor-animatepro' ); ?></span>
			<?php endif; ?>
		</a>
		<?php
	}

	/**
	 * Replace {url}/{title}/{image}/{desc} tokens with URL-encoded values.
	 *
	 * @param string $template Template.
	 * @param array  $target   Target data.
	 * @return string
	 */
	protected function fill_tokens( $template, $target ) {
		return str_replace(
			array( '{url}', '{title}', '{image}', '{desc}' ),
			array(
				rawurlencode( $target['url'] ),
				rawurlencode( $target['title'] ),
				rawurlencode( $target['image'] ),
				rawurlencode( $target['desc'] ),
			),
			$template
		);
	}

	protected function render_icon( $default_class, $custom_icon ) {
		if ( ! empty( $custom_icon['value'] ) ) {
			Icons_Manager::render_icon( $custom_icon, array( 'aria-hidden' => 'true' ) );
			return;
		}
		if ( $default_class ) {
			printf( '<i class="%s" aria-hidden="true"></i>', esc_attr( $default_class ) );
		}
	}
}
