<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

/**
 * Loop Grid — a query-driven grid that repeats a SAVED ELEMENTOR TEMPLATE once
 * per post, instead of the plugin's fixed card markup.
 *
 * This is the one grid where the item design belongs to the user: build a
 * template out of the plugin's Dynamic widgets (Post Title, Post Featured Image,
 * Post Excerpt, Post Meta Info, …) and each iteration renders it with that post
 * as the current post, so those widgets resolve per item.
 *
 * Implementation notes:
 *   - the loop is a REAL WP loop (`$query->the_post()`), because that is what
 *     sets the global $post that `eap_get_post_id()` / `get_the_ID()` read;
 *   - the template's generated CSS is enqueued ONCE up front, so the per-item
 *     render can skip inlining it every iteration;
 *   - a static re-entry guard stops a template that contains a Loop Grid (or a
 *     Post Content widget rendering one) from recursing forever.
 *
 * Query comes from the shared EAP_Posts_Query engine. CSS only — no JS.
 */
class EAP_Widget_Loop_Grid extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-loop-grid';
	}

	public function get_title() {
		return __( 'Loop Grid', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_keywords() {
		return array( 'loop', 'grid', 'template', 'posts', 'query', 'builder', 'dynamic' );
	}

	public function get_style_depends() {
		return array( 'eap-core', 'eap-loop-grid' );
	}

	protected function register_controls() {
		$this->register_template_section();
		$this->register_query_section();
		$this->register_layout_section();
		$this->register_pagination_section();

		$this->register_grid_style();
		$this->register_item_style();
		$this->register_pagination_style();
	}

	/* =====================================================================
	 * CONTENT — TEMPLATE
	 * ================================================================== */

	protected function register_template_section() {
		$this->start_controls_section(
			'section_template',
			array(
				'label' => __( 'Loop Item', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'template_id',
			array(
				'label'       => __( 'Item Template', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => $this->eap_get_template_options(),
				'label_block' => true,
				'description' => __( 'A saved Elementor template rendered once per post. Build it from the Dynamic widgets (Post Title, Post Featured Image, …) so each item shows its own post.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'template_hint',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Create one under Templates → Saved Templates, then pick it here.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — QUERY
	 * ================================================================== */

	protected function register_query_section() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Query', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'post_type',
			array(
				'label'   => __( 'Post Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => EAP_Posts_Query::get_post_type_options(),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => __( 'Posts Per Page', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 48,
				'default' => 6,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => __( 'Order By', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'          => __( 'Date', 'elementor-animatepro' ),
					'modified'      => __( 'Last Modified', 'elementor-animatepro' ),
					'title'         => __( 'Title', 'elementor-animatepro' ),
					'menu_order'    => __( 'Menu Order', 'elementor-animatepro' ),
					'comment_count' => __( 'Comment Count', 'elementor-animatepro' ),
					'rand'          => __( 'Random', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => __( 'Order', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => __( 'Descending', 'elementor-animatepro' ),
					'ASC'  => __( 'Ascending', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'   => __( 'Offset', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'default' => 0,
			)
		);

		$this->add_control(
			'include_terms',
			array(
				'label'       => __( 'Include Terms', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => EAP_Posts_Query::get_term_options(),
			)
		);

		$this->add_control(
			'exclude_terms',
			array(
				'label'       => __( 'Exclude Terms', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => EAP_Posts_Query::get_term_options(),
			)
		);

		$this->add_control(
			'exclude_current',
			array(
				'label'        => __( 'Exclude Current Post', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'ignore_sticky',
			array(
				'label'        => __( 'Ignore Sticky Posts', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — LAYOUT
	 * ================================================================== */

	protected function register_layout_section() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'elementor-animatepro' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => array( 'px' => array( 'min' => 1, 'max' => 6, 'step' => 1 ) ),
				'default'        => array( 'size' => 3 ),
				'tablet_default' => array( 'size' => 2 ),
				'mobile_default' => array( 'size' => 1 ),
				'selectors'      => array(
					'{{WRAPPER}} .eap-loop-grid__grid' => '--eap-lg-cols: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'empty_text',
			array(
				'label'   => __( 'Empty Message', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'No posts found.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — PAGINATION
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
				'label'       => __( 'Type', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'options'     => array(
					'none'     => __( 'None', 'elementor-animatepro' ),
					'numbered' => __( 'Numbered', 'elementor-animatepro' ),
				),
				'description' => __( 'Numbered pagination uses the page URL, so use it on an archive or a page that is not itself paginated.', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'pagination_align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
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
				'selectors' => array(
					'{{WRAPPER}} .eap-loop-grid__pagination' => 'justify-content: {{VALUE}};',
				),
				'condition' => array( 'pagination_type' => 'numbered' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_grid_style() {
		$this->start_controls_section(
			'section_grid_style',
			array(
				'label' => __( 'Grid', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label'      => __( 'Column Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-loop-grid__grid' => 'column-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'      => __( 'Row Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-loop-grid__grid' => 'row-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_item_style() {
		$this->start_controls_section(
			'section_item_style',
			array(
				'label' => __( 'Item', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'item_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-loop-grid__item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-loop-grid__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .eap-loop-grid__item',
			)
		);

		$this->add_responsive_control(
			'item_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-loop-grid__item' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->start_controls_tabs( 'lg_item_tabs' );

		$this->start_controls_tab( 'lg_item_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .eap-loop-grid__item',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'lg_item_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_shadow_hover',
				'selector' => '{{WRAPPER}} .eap-loop-grid__item:hover',
			)
		);

		$this->add_responsive_control(
			'item_hover_lift',
			array(
				'label'      => __( 'Hover Lift', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 0, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-loop-grid__item:hover' => 'transform: translateY(-{{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_pagination_style() {
		$this->start_controls_section(
			'section_pagination_style',
			array(
				'label'     => __( 'Pagination', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'pagination_type' => 'numbered' ),
			)
		);

		$this->add_responsive_control(
			'pagination_spacing',
			array(
				'label'      => __( 'Spacing Above', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'size' => 36, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-loop-grid__pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pagination_typography',
				'selector' => '{{WRAPPER}} .eap-loop-grid__pagination .page-numbers',
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#374151',
				'selectors' => array(
					'{{WRAPPER}} .eap-loop-grid__pagination .page-numbers' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f3f4f6',
				'selectors' => array(
					'{{WRAPPER}} .eap-loop-grid__pagination .page-numbers' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_active_bg',
			array(
				'label'     => __( 'Active Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-loop-grid__pagination .page-numbers.current, {{WRAPPER}} .eap-loop-grid__pagination a.page-numbers:hover' => 'background-color: {{VALUE}}; color: #fff; border-color: {{VALUE}};',
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
		$editor   = $this->eap_is_editor();

		$template_id = isset( $settings['template_id'] ) ? (int) $settings['template_id'] : 0;

		if ( ! $template_id || ! get_post( $template_id ) ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-loop-grid eap-loop-grid--notice">' . esc_html__( 'Choose an item template to build the loop from.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
		if ( 'numbered' !== ( $settings['pagination_type'] ?? 'none' ) ) {
			$paged = 1;
		}

		$spec = array(
			'source'          => 'latest',
			'post_type'       => ! empty( $settings['post_type'] ) ? $settings['post_type'] : 'post',
			'per_page'        => isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 6,
			'orderby'         => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date',
			'order'           => ! empty( $settings['order'] ) ? $settings['order'] : 'DESC',
			'offset'          => isset( $settings['offset'] ) ? (int) $settings['offset'] : 0,
			'include_terms'   => isset( $settings['include_terms'] ) ? $settings['include_terms'] : array(),
			'exclude_terms'   => isset( $settings['exclude_terms'] ) ? $settings['exclude_terms'] : array(),
			'exclude_current' => 'yes' === ( $settings['exclude_current'] ?? '' ),
			'current_id'      => $this->eap_get_post_id(),
			'ignore_sticky'   => 'yes' === ( $settings['ignore_sticky'] ?? 'yes' ),
		);

		$args  = EAP_Posts_Query::build_query_args( $spec, $paged );
		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			printf(
				'<div class="eap-widget eap-loop-grid eap-loop-grid--notice">%s</div>',
				esc_html( $settings['empty_text'] ?? '' )
			);
			return;
		}

		// A template that contains this (or another) loop widget pointing back at
		// it would recurse forever. Shared, template-ID-keyed guard on the base.
		if ( ! $this->eap_template_guard_enter( $template_id ) ) {
			return;
		}

		// Enqueue the template's generated CSS once, so each iteration can render
		// content without re-inlining styles.
		$this->eap_enqueue_template_css( $template_id );

		$this->add_render_attribute( 'wrapper', 'class', array( 'eap-widget', 'eap-loop-grid' ) );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="eap-loop-grid__grid">
				<?php
				while ( $query->have_posts() ) :
					// A real loop, because this is what sets the global $post the
					// Dynamic widgets (eap_get_post_id / get_the_ID) read.
					$query->the_post();
					?>
					<div class="eap-loop-grid__item">
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor-rendered template content.
						echo $this->eap_render_template( $template_id );
						?>
					</div>
					<?php
				endwhile;
				?>
			</div>
			<?php $this->render_pagination( $settings, $query, $paged ); ?>
		</div>
		<?php
		wp_reset_postdata();
		$this->eap_template_guard_leave( $template_id );
	}

	/**
	 * Numbered pagination.
	 *
	 * @param array    $settings Settings.
	 * @param WP_Query $query    Query.
	 * @param int      $paged    Current page.
	 * @return void
	 */
	protected function render_pagination( $settings, $query, $paged ) {
		if ( 'numbered' !== ( $settings['pagination_type'] ?? 'none' ) ) {
			return;
		}

		$max = (int) $query->max_num_pages;
		if ( $max < 2 ) {
			return;
		}

		$links = paginate_links(
			array(
				'base'      => str_replace( PHP_INT_MAX, '%#%', esc_url( get_pagenum_link( PHP_INT_MAX ) ) ),
				'format'    => '?paged=%#%',
				'current'   => $paged,
				'total'     => $max,
				'type'      => 'array',
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
			)
		);

		if ( empty( $links ) ) {
			return;
		}

		echo '<nav class="eap-loop-grid__pagination">';
		foreach ( $links as $link ) {
			echo wp_kses_post( $link );
		}
		echo '</nav>';
	}

}
