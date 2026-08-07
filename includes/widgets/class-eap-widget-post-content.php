<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/**
 * Post Content — outputs the CURRENT post's content (dynamic), run through the
 * the_content filters (shortcodes, embeds, wpautop). Intended for single
 * templates. Guards against infinite recursion when the post is itself built
 * with Elementor (its the_content filter would re-render the whole layout, which
 * may include this widget) via a static re-entry flag plus detaching Elementor's
 * content filter for the render call. In the editor it shows a note + sample so
 * the widget is styleable without a heavy / recursive live render.
 */
class EAP_Widget_Post_Content extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-post-content';
	}

	public function get_title() {
		return __( 'Post Content', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-post-content';
	}

	public function get_keywords() {
		return array( 'post', 'content', 'body', 'dynamic', 'single' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'post-content' );
	}

	protected function register_controls() {
		$this->register_content_section();
		$this->register_content_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_content_section() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Post Content', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'content_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Outputs the current post\'s content. On the live page it renders the full content; in the editor a sample is shown.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
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
					'{{WRAPPER}} .eap-post-content' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_content_style() {
		$this->start_controls_section(
			'section_content_style',
			array(
				'label' => __( 'Content', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-post-content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .eap-post-content',
			)
		);

		$this->add_responsive_control(
			'paragraph_spacing',
			array(
				'label'      => __( 'Paragraph Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
					'em' => array( 'min' => 0, 'max' => 6 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-content > p' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'link_heading',
			array(
				'label'     => __( 'Links', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => __( 'Link Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-post-content a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_color_hover',
			array(
				'label'     => __( 'Link Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-post-content a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	protected function render() {
		static $rendering = false;

		$post_id = $this->eap_get_post_id();

		// Editor: never attempt a live (possibly recursive / heavy) content
		// render — show a note + sample so the widget is styleable.
		if ( $this->eap_is_editor() ) {
			$this->render_editor_sample();
			return;
		}

		if ( ! $post_id ) {
			return;
		}

		// Hard backstop against re-entry (e.g. widget placed inside the very post
		// whose content it renders).
		if ( $rendering ) {
			return;
		}

		if ( post_password_required( $post_id ) ) {
			echo '<div class="eap-widget eap-post-content">' . get_the_password_form( $post_id ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core-generated form.
			return;
		}

		$rendering = true;

		$content = get_the_content( null, false, $post_id );

		// If the post is built with Elementor, its the_content filter would
		// re-render the whole layout (including possibly this widget). Detach
		// Elementor's content filter for this single apply_filters() call.
		$frontend = ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->frontend ) )
			? \Elementor\Plugin::$instance->frontend
			: null;
		$reattach = false;

		if ( $frontend && has_filter( 'the_content', array( $frontend, 'apply_builder_in_content' ) ) ) {
			remove_filter( 'the_content', array( $frontend, 'apply_builder_in_content' ) );
			$reattach = true;
		}

		/** This filter is documented in wp-includes/post-template.php */
		$content = apply_filters( 'the_content', $content );
		$content = str_replace( ']]>', ']]&gt;', $content );

		if ( $reattach ) {
			add_filter( 'the_content', array( $frontend, 'apply_builder_in_content' ) );
		}

		$rendering = false;

		if ( '' === trim( (string) $content ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output of the_content filters (same as core the_content()).
		echo '<div class="eap-widget eap-post-content">' . $content . '</div>';
	}

	/**
	 * Editor placeholder: a note plus sample body so the widget is styleable.
	 *
	 * @return void
	 */
	protected function render_editor_sample() {
		?>
		<div class="eap-widget eap-post-content eap-post-content--editor">
			<div class="eap-post-content__notice">
				<?php esc_html_e( 'Post Content — the current post\'s content renders here on the live page.', 'elementor-animatepro' ); ?>
			</div>
			<p><?php esc_html_e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.', 'elementor-animatepro' ); ?></p>
			<p><?php esc_html_e( 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt.', 'elementor-animatepro' ); ?></p>
		</div>
		<?php
	}
}
