<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/**
 * Post Excerpt — outputs the CURRENT post's excerpt (dynamic). Either the manual
 * excerpt (falling back to an auto-trim of the content) or an auto-trim of the
 * full content, capped to a word count, with an optional "Read More" link.
 * Intended for single / archive templates. CSS-only.
 */
class EAP_Widget_Post_Excerpt extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-post-excerpt';
	}

	public function get_title() {
		return __( 'Post Excerpt', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-post-excerpt';
	}

	public function get_keywords() {
		return array( 'post', 'excerpt', 'summary', 'dynamic', 'single', 'archive' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'post-excerpt' );
	}

	protected function register_controls() {
		$this->register_excerpt_section();
		$this->register_excerpt_style();
		$this->register_read_more_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_excerpt_section() {
		$this->start_controls_section(
			'section_excerpt',
			array(
				'label' => __( 'Post Excerpt', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'Source', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'excerpt',
				'options' => array(
					'excerpt' => __( 'Excerpt (manual, else auto)', 'elementor-animatepro' ),
					'content' => __( 'Trim from Content', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'length',
			array(
				'label'       => __( 'Max Length (words)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 200,
				'default'     => 30,
				'description' => __( 'Applies to auto-trimmed text. A manual excerpt is shown in full.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'ellipsis',
			array(
				'label'   => __( 'Ellipsis', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '…',
			)
		);

		$this->add_control(
			'show_read_more',
			array(
				'label'        => __( 'Read More Link', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'read_more_text',
			array(
				'label'     => __( 'Read More Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read More', 'elementor-animatepro' ),
				'condition' => array( 'show_read_more' => 'yes' ),
			)
		);

		$this->add_control(
			'read_more_inline',
			array(
				'label'        => __( 'Inline Link', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Place the link on the same line as the text.', 'elementor-animatepro' ),
				'condition'    => array( 'show_read_more' => 'yes' ),
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
					'{{WRAPPER}} .eap-post-excerpt' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_excerpt_style() {
		$this->start_controls_section(
			'section_excerpt_style',
			array(
				'label' => __( 'Text', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-post-excerpt__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .eap-post-excerpt__text',
			)
		);

		$this->end_controls_section();
	}

	protected function register_read_more_style() {
		$this->start_controls_section(
			'section_read_more_style',
			array(
				'label'     => __( 'Read More', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_read_more' => 'yes' ),
			)
		);

		$this->add_control(
			'more_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-excerpt__more' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'more_color_hover',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-post-excerpt__more:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'more_typography',
				'selector' => '{{WRAPPER}} .eap-post-excerpt__more',
			)
		);

		$this->add_responsive_control(
			'more_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-excerpt__more' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-post-excerpt--block .eap-post-excerpt__more' => 'margin-left: 0; margin-top: {{SIZE}}{{UNIT}};',
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

		$length   = isset( $settings['length'] ) && '' !== $settings['length'] ? (int) $settings['length'] : 30;
		$ellipsis = isset( $settings['ellipsis'] ) ? $settings['ellipsis'] : '…';
		$source   = ! empty( $settings['source'] ) ? $settings['source'] : 'excerpt';

		$is_manual = false;
		$text      = '';

		if ( $post_id ) {
			$post = get_post( $post_id );

			if ( 'excerpt' === $source && $post && '' !== trim( (string) $post->post_excerpt ) ) {
				$text      = $post->post_excerpt;
				$is_manual = true;
			} elseif ( $post ) {
				$raw  = strip_shortcodes( (string) $post->post_content );
				$raw  = wp_strip_all_tags( $raw );
				$text = wp_trim_words( $raw, $length, $ellipsis );
			}
		}

		if ( '' === trim( (string) $text ) && $this->eap_is_editor() ) {
			$text = __( 'This is a sample excerpt. On the live post it shows the current post\'s excerpt, trimmed to your chosen length. Replace this by adding an excerpt or content to the post.', 'elementor-animatepro' );
		}

		if ( '' === trim( (string) $text ) ) {
			return;
		}

		$show_more = 'yes' === ( $settings['show_read_more'] ?? '' );
		$inline    = 'yes' === ( $settings['read_more_inline'] ?? '' );

		$classes = array( 'eap-widget', 'eap-post-excerpt' );
		$classes[] = $inline ? 'eap-post-excerpt--inline' : 'eap-post-excerpt--block';
		$this->add_render_attribute( 'wrapper', 'class', $classes );

		// Body text: manual excerpts may carry basic HTML; auto-trims are plain.
		if ( $is_manual ) {
			$body = wp_kses_post( wpautop( $text ) );
		} else {
			$body = '<p>' . esc_html( $text ) . '</p>';
		}

		$more = '';
		if ( $show_more ) {
			$more_url  = $post_id ? get_permalink( $post_id ) : '#';
			$more_text = ! empty( $settings['read_more_text'] ) ? $settings['read_more_text'] : __( 'Read More', 'elementor-animatepro' );
			$more      = sprintf(
				'<a class="eap-post-excerpt__more" href="%s">%s</a>',
				esc_url( $more_url ),
				esc_html( $more_text )
			);
		}
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $inline && $show_more ) : ?>
				<span class="eap-post-excerpt__text"><?php echo wp_kses_post( $is_manual ? $text : ( esc_html( $text ) ) ); ?> </span><?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped helpers above.
				echo $more;
			else :
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from wp_kses_post / esc_html above.
				echo '<div class="eap-post-excerpt__text">' . $body . '</div>' . $more;
			endif;
			?>
		</div>
		<?php
	}
}
