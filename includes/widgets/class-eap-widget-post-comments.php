<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

/**
 * Post Comments — outputs the CURRENT post's native comment list and reply form
 * (wp_list_comments + comment_form) with full styling control over the heading,
 * comment items and form. Intended for single post / page templates. In the
 * editor it shows a styled sample (a live comment_form render in the editor's
 * AJAX context is unreliable), so the layout stays designable.
 */
class EAP_Widget_Post_Comments extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-post-comments';
	}

	public function get_title() {
		return __( 'Post Comments', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-comments';
	}

	public function get_keywords() {
		return array( 'post', 'comments', 'discussion', 'reply', 'dynamic', 'single' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'post-comments' );
	}

	protected function register_controls() {
		$this->register_comments_section();
		$this->register_title_style();
		$this->register_list_style();
		$this->register_form_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_comments_section() {
		$this->start_controls_section(
			'section_comments',
			array(
				'label' => __( 'Comments', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'comments_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Outputs the current post\'s comment list and reply form on the live page. The editor shows a sample so you can style it.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'show_title',
			array(
				'label'        => __( 'Comments Heading', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_avatar',
			array(
				'label'        => __( 'Avatars', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'avatar_size',
			array(
				'label'     => __( 'Avatar Size', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 24,
				'max'       => 120,
				'default'   => 48,
				'condition' => array( 'show_avatar' => 'yes' ),
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
				'label'     => __( 'Heading', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-comments__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-post-comments__title',
			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-comments__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_list_style() {
		$this->start_controls_section(
			'section_list_style',
			array(
				'label' => __( 'Comment Items', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'author_color',
			array(
				'label'     => __( 'Author Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-comments .comment-author .fn, {{WRAPPER}} .eap-post-comments .comment-author .fn a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => __( 'Meta Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9ca3af',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-comments .comment-metadata, {{WRAPPER}} .eap-post-comments .comment-metadata a, {{WRAPPER}} .eap-post-comments .comment-meta .says' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#374151',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-comments .comment-content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .eap-post-comments .comment-content',
			)
		);

		$this->add_control(
			'reply_color',
			array(
				'label'     => __( 'Reply Link Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-comments .comment-reply-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_radius',
			array(
				'label'      => __( 'Avatar Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'default'    => array( 'size' => 50, 'unit' => '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-comments .comment-author .avatar' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'show_avatar' => 'yes' ),
			)
		);

		$this->add_control(
			'item_divider_color',
			array(
				'label'     => __( 'Divider Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e5e7eb',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-comments .comment-body' => 'border-bottom-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_spacing',
			array(
				'label'      => __( 'Item Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 20, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-comments .comment-body' => 'padding-bottom: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_form_style() {
		$this->start_controls_section(
			'section_form_style',
			array(
				'label' => __( 'Reply Form', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'form_title_color',
			array(
				'label'     => __( 'Form Title Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-comments .comment-reply-title, {{WRAPPER}} .eap-post-comments .comment-reply-title small a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => __( 'Label Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#374151',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-comments .comment-form label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'input_heading',
			array(
				'label'     => __( 'Fields', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'input_text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-comments .comment-form input:not([type=submit]), {{WRAPPER}} .eap-post-comments .comment-form textarea' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'input_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-comments .comment-form input:not([type=submit]), {{WRAPPER}} .eap-post-comments .comment-form textarea' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'input_border',
				'selector'  => '{{WRAPPER}} .eap-post-comments .comment-form input:not([type=submit]), {{WRAPPER}} .eap-post-comments .comment-form textarea',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'input_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-comments .comment-form input:not([type=submit]), {{WRAPPER}} .eap-post-comments .comment-form textarea' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'input_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-comments .comment-form input:not([type=submit]), {{WRAPPER}} .eap-post-comments .comment-form textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'submit_heading',
			array(
				'label'     => __( 'Submit Button', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'submit_tabs' );

		$this->start_controls_tab( 'submit_tab_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'submit_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-comments .form-submit .submit' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-comments .form-submit .submit' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'submit_tab_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'submit_color_hover',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-comments .form-submit .submit:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4338ca',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-comments .form-submit .submit:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'submit_typography',
				'selector'  => '{{WRAPPER}} .eap-post-comments .form-submit .submit',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'submit_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-comments .form-submit .submit' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'submit_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 12, 'right' => 26, 'bottom' => 12, 'left' => 26, 'unit' => 'px', 'isLinked' => false ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-comments .form-submit .submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$show_title = 'yes' === ( $settings['show_title'] ?? 'yes' );

		if ( $this->eap_is_editor() ) {
			$this->render_editor_sample( $show_title );
			return;
		}

		$post_id = $this->eap_get_post_id();
		if ( ! $post_id ) {
			return;
		}

		if ( post_password_required( $post_id ) ) {
			return;
		}

		$post = get_post( $post_id );
		if ( ! $post ) {
			return;
		}

		$comments = get_comments(
			array(
				'post_id' => $post_id,
				'status'  => 'approve',
				'order'   => 'ASC',
			)
		);

		if ( ! comments_open( $post_id ) && empty( $comments ) ) {
			return;
		}

		// Comment template functions rely on the global post.
		$original_post          = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
		$GLOBALS['post']        = $post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		setup_postdata( $post );

		$show_avatar = 'yes' === ( $settings['show_avatar'] ?? 'yes' );
		$avatar_size = isset( $settings['avatar_size'] ) && '' !== $settings['avatar_size'] ? (int) $settings['avatar_size'] : 48;
		$count       = count( $comments );

		$this->add_render_attribute( 'wrapper', 'class', array( 'eap-widget', 'eap-post-comments' ) );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $show_title && $count ) : ?>
				<h3 class="eap-post-comments__title">
					<?php
					/* translators: %s: number of comments. */
					echo esc_html( sprintf( _n( '%s Comment', '%s Comments', $count, 'elementor-animatepro' ), number_format_i18n( $count ) ) );
					?>
				</h3>
			<?php endif; ?>

			<?php if ( $count ) : ?>
				<ol class="eap-post-comments__list comment-list">
					<?php
					wp_list_comments(
						array(
							'style'       => 'ol',
							'avatar_size' => $avatar_size,
							'short_ping'  => true,
							'avatar'      => $show_avatar,
						),
						$comments
					);
					?>
				</ol>
			<?php endif; ?>

			<div class="eap-post-comments__form">
				<?php comment_form( array(), $post_id ); ?>
			</div>
		</div>
		<?php
		wp_reset_postdata();

		// Restore the previous global post.
		if ( null !== $original_post ) {
			$GLOBALS['post'] = $original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
	}

	/**
	 * Editor preview: a styled sample list + form so the widget is designable.
	 *
	 * @param bool $show_title Whether the heading is shown.
	 * @return void
	 */
	protected function render_editor_sample( $show_title ) {
		$avatar = get_avatar( 0, 48 );
		?>
		<div class="eap-widget eap-post-comments eap-post-comments--editor">
			<?php if ( $show_title ) : ?>
				<h3 class="eap-post-comments__title"><?php esc_html_e( '2 Comments', 'elementor-animatepro' ); ?></h3>
			<?php endif; ?>
			<ol class="eap-post-comments__list comment-list">
				<li class="comment">
					<div class="comment-body">
						<div class="comment-author vcard">
							<?php echo $avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar() output. ?>
							<b class="fn"><?php esc_html_e( 'Jane Doe', 'elementor-animatepro' ); ?></b>
						</div>
						<div class="comment-metadata"><?php esc_html_e( 'January 1, 2026 at 10:00 am', 'elementor-animatepro' ); ?></div>
						<div class="comment-content"><p><?php esc_html_e( 'This is a sample comment. On the live post, real approved comments are shown here.', 'elementor-animatepro' ); ?></p></div>
						<div class="reply"><a class="comment-reply-link" href="#"><?php esc_html_e( 'Reply', 'elementor-animatepro' ); ?></a></div>
					</div>
				</li>
				<li class="comment">
					<div class="comment-body">
						<div class="comment-author vcard">
							<?php echo $avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar() output. ?>
							<b class="fn"><?php esc_html_e( 'John Smith', 'elementor-animatepro' ); ?></b>
						</div>
						<div class="comment-metadata"><?php esc_html_e( 'January 2, 2026 at 9:15 am', 'elementor-animatepro' ); ?></div>
						<div class="comment-content"><p><?php esc_html_e( 'Great write-up — thanks for sharing!', 'elementor-animatepro' ); ?></p></div>
						<div class="reply"><a class="comment-reply-link" href="#"><?php esc_html_e( 'Reply', 'elementor-animatepro' ); ?></a></div>
					</div>
				</li>
			</ol>
			<div class="eap-post-comments__form">
				<div class="comment-respond">
					<h3 class="comment-reply-title"><?php esc_html_e( 'Leave a Comment', 'elementor-animatepro' ); ?></h3>
					<form class="comment-form" action="#" onsubmit="return false;">
						<p class="comment-form-comment">
							<label><?php esc_html_e( 'Comment', 'elementor-animatepro' ); ?></label>
							<textarea rows="5"></textarea>
						</p>
						<p class="comment-form-author">
							<label><?php esc_html_e( 'Name', 'elementor-animatepro' ); ?></label>
							<input type="text" />
						</p>
						<p class="comment-form-email">
							<label><?php esc_html_e( 'Email', 'elementor-animatepro' ); ?></label>
							<input type="text" />
						</p>
						<p class="form-submit">
							<button type="button" class="submit"><?php esc_html_e( 'Post Comment', 'elementor-animatepro' ); ?></button>
						</p>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
}
