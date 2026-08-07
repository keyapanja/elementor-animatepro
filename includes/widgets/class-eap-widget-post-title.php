<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;

/**
 * Post Title — outputs the CURRENT post's title (dynamic) in a chosen HTML tag,
 * optionally linked to the post permalink or a custom URL. Intended for single
 * post / page templates. In the Elementor editor, when there is no real post in
 * context, it shows a sample title so the widget is never blank. CSS-only.
 */
class EAP_Widget_Post_Title extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-post-title';
	}

	public function get_title() {
		return __( 'Post Title', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-post-title';
	}

	public function get_keywords() {
		return array( 'post', 'title', 'heading', 'dynamic', 'single', 'archive' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'post-title' );
	}

	protected function register_controls() {
		$this->register_title_section();
		$this->register_title_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_title_section() {
		$this->start_controls_section(
			'section_title',
			array(
				'label' => __( 'Post Title', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'html_tag',
			array(
				'label'   => __( 'HTML Tag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
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

		$this->add_control(
			'link_to',
			array(
				'label'   => __( 'Link', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'   => __( 'None', 'elementor-animatepro' ),
					'post'   => __( 'Post URL', 'elementor-animatepro' ),
					'custom' => __( 'Custom URL', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'custom_link',
			array(
				'label'         => __( 'Custom URL', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'placeholder'   => __( 'https://example.com', 'elementor-animatepro' ),
				'condition'     => array( 'link_to' => 'custom' ),
			)
		);

		$this->add_control(
			'fallback_text',
			array(
				'label'       => __( 'Fallback Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Shown when the post has no title', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justified', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-post-title' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_title_style() {
		$this->start_controls_section(
			'section_title_style',
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
					'{{WRAPPER}} .eap-post-title, {{WRAPPER}} .eap-post-title__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-post-title__link:hover' => 'color: {{VALUE}};',
				),
				'condition' => array( 'link_to!' => 'none' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-post-title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'title_shadow',
				'selector' => '{{WRAPPER}} .eap-post-title',
			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label'      => __( 'Spacing (Margin)', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$post_id  = $this->eap_get_post_id();

		$title = $post_id ? get_the_title( $post_id ) : '';

		if ( '' === $title ) {
			if ( ! empty( $settings['fallback_text'] ) ) {
				$title = $settings['fallback_text'];
			} elseif ( $this->eap_is_editor() ) {
				$title = __( 'Sample Post Title', 'elementor-animatepro' );
			}
		}

		if ( '' === $title ) {
			return;
		}

		$tag = $this->get_valid_tag( $settings['html_tag'] ?? 'h2' );

		$this->add_render_attribute( 'title', 'class', array( 'eap-widget', 'eap-post-title' ) );

		// Resolve the link.
		$link_to = ! empty( $settings['link_to'] ) ? $settings['link_to'] : 'none';
		$url     = '';

		if ( 'post' === $link_to && $post_id ) {
			$url = get_permalink( $post_id );
			$this->add_render_attribute( 'title_link', 'href', esc_url( $url ) );
		} elseif ( 'custom' === $link_to && ! empty( $settings['custom_link']['url'] ) ) {
			$this->add_link_attributes( 'title_link', $settings['custom_link'] );
			$url = $settings['custom_link']['url'];
		}

		$this->add_render_attribute( 'title_link', 'class', 'eap-post-title__link' );

		$text = esc_html( $title );
		if ( '' !== $url ) {
			$text = '<a ' . $this->get_render_attribute_string( 'title_link' ) . '>' . $text . '</a>';
		}

		printf(
			'<%1$s %2$s>%3$s</%1$s>',
			esc_html( $tag ),
			$this->get_render_attribute_string( 'title' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor-built attribute string.
			$text // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Title escaped above; link built from escaped helpers.
		);
	}

	/**
	 * Whitelist the output tag.
	 *
	 * @param string $tag Requested tag.
	 * @return string
	 */
	protected function get_valid_tag( $tag ) {
		$allowed = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' );
		return in_array( $tag, $allowed, true ) ? $tag : 'h2';
	}
}
