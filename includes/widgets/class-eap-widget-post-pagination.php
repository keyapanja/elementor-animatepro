<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

/**
 * Post Pagination — three modes:
 *  - Post Navigation: previous / next single-post links (with optional titles and
 *    same-term restriction).
 *  - Numbered: archive page numbers (paginate_links) for listing templates.
 *  - In-Post Pages: links for content split with <!--nextpage--> (wp_link_pages).
 * Intended for single / archive templates. In the editor each mode shows a
 * representative sample so it stays styleable without a real query context.
 */
class EAP_Widget_Post_Pagination extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-post-pagination';
	}

	public function get_title() {
		return __( 'Post Pagination', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-post-navigation';
	}

	public function get_keywords() {
		return array( 'post', 'pagination', 'navigation', 'prev', 'next', 'numbered', 'dynamic' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'post-pagination' );
	}

	protected function register_controls() {
		$this->register_pagination_section();
		$this->register_link_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_pagination_section() {
		$this->start_controls_section(
			'section_pagination',
			array(
				'label' => __( 'Pagination', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'   => __( 'Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post_nav',
				'options' => array(
					'post_nav'   => __( 'Post Navigation (Prev / Next)', 'elementor-animatepro' ),
					'numbered'   => __( 'Numbered (Archive)', 'elementor-animatepro' ),
					'post_pages' => __( 'In-Post Pages', 'elementor-animatepro' ),
				),
			)
		);

		/* --- Post Navigation --- */
		$this->add_control(
			'show_arrows',
			array(
				'label'        => __( 'Show Arrows', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'pagination_type' => 'post_nav' ),
			)
		);

		$this->add_control(
			'prev_label',
			array(
				'label'     => __( 'Previous Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Previous', 'elementor-animatepro' ),
				'condition' => array( 'pagination_type' => 'post_nav' ),
			)
		);

		$this->add_control(
			'next_label',
			array(
				'label'     => __( 'Next Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Next', 'elementor-animatepro' ),
				'condition' => array( 'pagination_type' => 'post_nav' ),
			)
		);

		$this->add_control(
			'show_post_title',
			array(
				'label'        => __( 'Show Post Titles', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'pagination_type' => 'post_nav' ),
			)
		);

		$this->add_control(
			'in_same_term',
			array(
				'label'        => __( 'Same Category Only', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'pagination_type' => 'post_nav' ),
			)
		);

		/* --- Numbered --- */
		$this->add_control(
			'num_prev_text',
			array(
				'label'     => __( 'Previous Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( '‹ Prev', 'elementor-animatepro' ),
				'condition' => array( 'pagination_type' => 'numbered' ),
			)
		);

		$this->add_control(
			'num_next_text',
			array(
				'label'     => __( 'Next Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Next ›', 'elementor-animatepro' ),
				'condition' => array( 'pagination_type' => 'numbered' ),
			)
		);

		$this->add_control(
			'mid_size',
			array(
				'label'     => __( 'Adjacent Pages', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 6,
				'default'   => 2,
				'condition' => array( 'pagination_type' => 'numbered' ),
			)
		);

		/* --- In-Post Pages --- */
		$this->add_control(
			'pages_label',
			array(
				'label'     => __( 'Pages Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Pages:', 'elementor-animatepro' ),
				'condition' => array( 'pagination_type' => 'post_pages' ),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'                => __( 'Alignment', 'elementor-animatepro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'left',
				'options'              => array(
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
					'between' => array(
						'title' => __( 'Space Between', 'elementor-animatepro' ),
						'icon'  => 'eicon-justify-space-between-h',
					),
				),
				'selectors'            => array(
					'{{WRAPPER}} .eap-post-pagination' => 'justify-content: {{VALUE}};',
				),
				'selectors_dictionary' => array(
					'left'    => 'flex-start',
					'center'  => 'center',
					'right'   => 'flex-end',
					'between' => 'space-between',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_link_style() {
		$this->start_controls_section(
			'section_link_style',
			array(
				'label' => __( 'Links', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-pagination' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'selector' => '{{WRAPPER}} .eap-post-pagination__link, {{WRAPPER}} .eap-post-pagination .page-numbers, {{WRAPPER}} .eap-post-pagination__pages a',
			)
		);

		$this->add_responsive_control(
			'link_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 10, 'right' => 16, 'bottom' => 10, 'left' => 16, 'unit' => 'px', 'isLinked' => false ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-pagination__link, {{WRAPPER}} .eap-post-pagination .page-numbers, {{WRAPPER}} .eap-post-pagination__pages a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'link_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 50 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-pagination__link, {{WRAPPER}} .eap-post-pagination .page-numbers, {{WRAPPER}} .eap-post-pagination__pages a' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'link_border',
				'selector' => '{{WRAPPER}} .eap-post-pagination__link, {{WRAPPER}} .eap-post-pagination .page-numbers, {{WRAPPER}} .eap-post-pagination__pages a',
			)
		);

		$this->start_controls_tabs( 'link_tabs' );

		$this->start_controls_tab( 'link_tab_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'link_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#374151',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-pagination__link, {{WRAPPER}} .eap-post-pagination .page-numbers, {{WRAPPER}} .eap-post-pagination__pages a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f3f4f6',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-pagination__link, {{WRAPPER}} .eap-post-pagination .page-numbers, {{WRAPPER}} .eap-post-pagination__pages a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'link_tab_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'link_color_hover',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-pagination__link:hover, {{WRAPPER}} .eap-post-pagination .page-numbers:hover, {{WRAPPER}} .eap-post-pagination__pages a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-pagination__link:hover, {{WRAPPER}} .eap-post-pagination .page-numbers:hover, {{WRAPPER}} .eap-post-pagination__pages a:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'link_tab_active',
			array(
				'label'     => __( 'Current', 'elementor-animatepro' ),
				'condition' => array( 'pagination_type' => 'numbered' ),
			)
		);

		$this->add_control(
			'link_color_current',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-pagination .page-numbers.current' => 'color: {{VALUE}};',
				),
				'condition' => array( 'pagination_type' => 'numbered' ),
			)
		);

		$this->add_control(
			'link_bg_current',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-pagination .page-numbers.current' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				),
				'condition' => array( 'pagination_type' => 'numbered' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Post Title Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-pagination__title' => 'color: {{VALUE}};',
				),
				'separator' => 'before',
				'condition' => array(
					'pagination_type' => 'post_nav',
					'show_post_title' => 'yes',
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
		$type     = ! empty( $settings['pagination_type'] ) ? $settings['pagination_type'] : 'post_nav';

		$this->add_render_attribute(
			'wrapper',
			'class',
			array( 'eap-widget', 'eap-post-pagination', 'eap-post-pagination--' . $type )
		);

		$editor = $this->eap_is_editor();

		echo '<nav ' . $this->get_render_attribute_string( 'wrapper' ) . ' aria-label="' . esc_attr__( 'Post pagination', 'elementor-animatepro' ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor attribute string.

		switch ( $type ) {
			case 'numbered':
				$this->render_numbered( $settings, $editor );
				break;
			case 'post_pages':
				$this->render_post_pages( $settings, $editor );
				break;
			case 'post_nav':
			default:
				$this->render_post_nav( $settings, $editor );
				break;
		}

		echo '</nav>';
	}

	/**
	 * Previous / next single-post links.
	 *
	 * @param array $settings Settings.
	 * @param bool  $editor   Editor mode.
	 * @return void
	 */
	protected function render_post_nav( $settings, $editor ) {
		$arrows   = 'yes' === ( $settings['show_arrows'] ?? 'yes' );
		$titles   = 'yes' === ( $settings['show_post_title'] ?? '' );
		$prev_lbl = ! empty( $settings['prev_label'] ) ? $settings['prev_label'] : __( 'Previous', 'elementor-animatepro' );
		$next_lbl = ! empty( $settings['next_label'] ) ? $settings['next_label'] : __( 'Next', 'elementor-animatepro' );

		if ( $editor ) {
			echo $this->post_nav_link( 'prev', '#', $prev_lbl, __( 'Sample Previous Post', 'elementor-animatepro' ), $arrows, $titles ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped helpers.
			echo $this->post_nav_link( 'next', '#', $next_lbl, __( 'Sample Next Post', 'elementor-animatepro' ), $arrows, $titles ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped helpers.
			return;
		}

		$post_id = $this->eap_get_post_id();
		if ( ! $post_id ) {
			return;
		}

		// Adjacency uses the global post.
		$original        = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
		$GLOBALS['post'] = get_post( $post_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		setup_postdata( $GLOBALS['post'] );

		$same = 'yes' === ( $settings['in_same_term'] ?? '' );
		$prev = get_previous_post( $same );
		$next = get_next_post( $same );

		if ( $prev instanceof WP_Post ) {
			echo $this->post_nav_link( 'prev', get_permalink( $prev ), $prev_lbl, get_the_title( $prev ), $arrows, $titles ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped helpers.
		}
		if ( $next instanceof WP_Post ) {
			echo $this->post_nav_link( 'next', get_permalink( $next ), $next_lbl, get_the_title( $next ), $arrows, $titles ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped helpers.
		}

		wp_reset_postdata();
		if ( null !== $original ) {
			$GLOBALS['post'] = $original; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
	}

	/**
	 * Build one prev/next link's markup (already escaped).
	 *
	 * @param string $dir    prev|next.
	 * @param string $url    Link URL.
	 * @param string $label  Prev/Next label.
	 * @param string $title  Post title.
	 * @param bool   $arrows Show arrow.
	 * @param bool   $titles Show post title.
	 * @return string
	 */
	protected function post_nav_link( $dir, $url, $label, $title, $arrows, $titles ) {
		$arrow = 'prev' === $dir ? '‹' : '›';

		$out  = '<a class="eap-post-pagination__link eap-post-pagination__link--' . esc_attr( $dir ) . '" href="' . esc_url( $url ) . '">';
		if ( $arrows && 'prev' === $dir ) {
			$out .= '<span class="eap-post-pagination__arrow" aria-hidden="true">' . esc_html( $arrow ) . '</span>';
		}
		$out .= '<span class="eap-post-pagination__meta">';
		$out .= '<span class="eap-post-pagination__label">' . esc_html( $label ) . '</span>';
		if ( $titles && '' !== $title ) {
			$out .= '<span class="eap-post-pagination__title">' . esc_html( $title ) . '</span>';
		}
		$out .= '</span>';
		if ( $arrows && 'next' === $dir ) {
			$out .= '<span class="eap-post-pagination__arrow" aria-hidden="true">' . esc_html( $arrow ) . '</span>';
		}
		$out .= '</a>';

		return $out;
	}

	/**
	 * Numbered archive pagination.
	 *
	 * @param array $settings Settings.
	 * @param bool  $editor   Editor mode.
	 * @return void
	 */
	protected function render_numbered( $settings, $editor ) {
		$prev = ! empty( $settings['num_prev_text'] ) ? $settings['num_prev_text'] : __( '‹ Prev', 'elementor-animatepro' );
		$next = ! empty( $settings['num_next_text'] ) ? $settings['num_next_text'] : __( 'Next ›', 'elementor-animatepro' );
		$mid  = isset( $settings['mid_size'] ) && '' !== $settings['mid_size'] ? (int) $settings['mid_size'] : 2;

		if ( $editor ) {
			echo '<span class="page-numbers">' . esc_html( $prev ) . '</span>';
			echo '<span class="page-numbers">1</span>';
			echo '<span class="page-numbers current">2</span>';
			echo '<span class="page-numbers">3</span>';
			echo '<span class="page-numbers dots">…</span>';
			echo '<span class="page-numbers">' . esc_html( $next ) . '</span>';
			return;
		}

		$links = paginate_links(
			array(
				'type'      => 'array',
				'prev_text' => $prev,
				'next_text' => $next,
				'mid_size'  => $mid,
			)
		);

		if ( empty( $links ) ) {
			return;
		}

		foreach ( $links as $link ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core paginate_links() markup.
			echo $link;
		}
	}

	/**
	 * In-post page links (<!--nextpage-->).
	 *
	 * @param array $settings Settings.
	 * @param bool  $editor   Editor mode.
	 * @return void
	 */
	protected function render_post_pages( $settings, $editor ) {
		$label = ! empty( $settings['pages_label'] ) ? $settings['pages_label'] : __( 'Pages:', 'elementor-animatepro' );

		if ( $editor ) {
			echo '<div class="eap-post-pagination__pages">';
			echo '<span class="eap-post-pagination__pages-label">' . esc_html( $label ) . '</span> ';
			echo '<span class="post-page-numbers current">1</span> <a href="#">2</a> <a href="#">3</a>';
			echo '</div>';
			return;
		}

		$links = wp_link_pages(
			array(
				'echo'        => 0,
				'before'      => '<div class="eap-post-pagination__pages"><span class="eap-post-pagination__pages-label">' . esc_html( $label ) . '</span> ',
				'after'       => '</div>',
				'separator'   => ' ',
			)
		);

		if ( '' === trim( (string) $links ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core wp_link_pages() markup with an escaped label.
		echo $links;
	}
}
