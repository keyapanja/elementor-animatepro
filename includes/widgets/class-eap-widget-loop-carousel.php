<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * Loop Carousel — Loop Grid's per-post template rendering on a Swiper carousel:
 * each slide is a SAVED ELEMENTOR TEMPLATE rendered with that post as the
 * current post.
 *
 * Shares the loop-template plumbing with Loop Grid through EAP_Widget_Base
 * (eap_get_template_options / eap_enqueue_template_css / eap_render_template and
 * the template-ID-keyed recursion guard), and the slider plumbing with the
 * Slider group (bundled Swiper only when registered,
 * `.eap-loop-carousel__swiper.swiper` markup, JSON config on
 * `data-eap-loop-carousel`, mobile-first breakpoints).
 *
 * As in Loop Grid, the loop is a REAL WP loop so the global $post is set for the
 * Dynamic widgets inside each template.
 */
class EAP_Widget_Loop_Carousel extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-loop-carousel';
	}

	public function get_title() {
		return __( 'Loop Carousel', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-slider-album';
	}

	public function get_keywords() {
		return array( 'loop', 'carousel', 'slider', 'template', 'posts', 'query', 'builder', 'dynamic' );
	}

	public function get_style_depends() {
		return array( 'eap-core', 'eap-loop-carousel' );
	}

	public function get_script_depends() {
		$deps = array(
			'eap-core-runtime',
			'eap-loop-carousel-script',
		);
		if ( wp_script_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}

		return array_unique( $deps );
	}

	protected function register_controls() {
		$this->register_template_section();
		$this->register_query_section();
		$this->register_slider_section();

		$this->register_item_style();
		$this->register_arrows_style();
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
				'description' => __( 'A saved Elementor template rendered once per post. Build it from the Dynamic widgets (Post Title, Post Featured Image, …) so each slide shows its own post.', 'elementor-animatepro' ),
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
				'label'   => __( 'Number of Posts', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 48,
				'default' => 9,
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
	 * CONTENT — SLIDER
	 * ================================================================== */

	protected function register_slider_section() {
		$this->start_controls_section(
			'section_slider',
			array(
				'label' => __( 'Slider', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'slides_desktop',
			array(
				'label'   => __( 'Slides (Desktop)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 6,
				'default' => 3,
			)
		);

		$this->add_control(
			'slides_tablet',
			array(
				'label'   => __( 'Slides (Tablet)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 6,
				'default' => 2,
			)
		);

		$this->add_control(
			'slides_mobile',
			array(
				'label'   => __( 'Slides (Mobile)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 4,
				'default' => 1,
			)
		);

		$this->add_control(
			'space_between',
			array(
				'label'      => __( 'Space Between', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => __( 'Transition Speed (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 100,
				'max'     => 3000,
				'step'    => 50,
				'default' => 600,
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => __( 'Loop', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'centered_slides',
			array(
				'label'        => __( 'Centered Slides', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => __( 'Autoplay Delay (s)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 20,
				'step'      => 0.5,
				'default'   => 4,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => __( 'Pause on Hover', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'show_arrows',
			array(
				'label'        => __( 'Arrows', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'show_pagination',
			array(
				'label'        => __( 'Pagination', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'     => __( 'Pagination Type', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bullets',
				'options'   => array(
					'bullets'     => __( 'Bullets', 'elementor-animatepro' ),
					'fraction'    => __( 'Fraction', 'elementor-animatepro' ),
					'progressbar' => __( 'Progress Bar', 'elementor-animatepro' ),
				),
				'condition' => array( 'show_pagination' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

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
					'{{WRAPPER}} .eap-loop-carousel__item' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .eap-loop-carousel__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .eap-loop-carousel__item',
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
					'{{WRAPPER}} .eap-loop-carousel__item' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .eap-loop-carousel__item',
			)
		);

		$this->end_controls_section();
	}

	protected function register_arrows_style() {
		$this->start_controls_section(
			'section_arrows_style',
			array(
				'label'     => __( 'Arrows', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_arrows' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'arrow_box',
			array(
				'label'      => __( 'Button Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 80 ) ),
				'default'    => array( 'size' => 44, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-loop-carousel__arrow' => '--eap-lc-arrow: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_offset',
			array(
				'label'      => __( 'Horizontal Offset', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => -60, 'max' => 40 ) ),
				'default'    => array( 'size' => -10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-loop-carousel__arrow' => '--eap-lc-arrow-offset: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'lc_arrow_tabs' );

		$this->start_controls_tab( 'lc_arrow_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'arrow_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-loop-carousel__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-loop-carousel__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'lc_arrow_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );

		$this->add_control(
			'arrow_color_hover',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-loop-carousel__arrow:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-loop-carousel__arrow:hover' => 'background-color: {{VALUE}};',
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
				'condition' => array( 'show_pagination' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'pagination_spacing',
			array(
				'label'      => __( 'Spacing Above', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 26, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-loop-carousel__pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bullet_size',
			array(
				'label'      => __( 'Bullet Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 4, 'max' => 24 ) ),
				'default'    => array( 'size' => 9, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-loop-carousel__pagination .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bullet_color',
			array(
				'label'     => __( 'Bullet Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d1d5db',
				'selectors' => array(
					'{{WRAPPER}} .eap-loop-carousel__pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}}; opacity: 1;',
				),
			)
		);

		$this->add_control(
			'bullet_color_active',
			array(
				'label'     => __( 'Active Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-loop-carousel__pagination .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eap-loop-carousel__pagination .swiper-pagination-progressbar-fill' => 'background-color: {{VALUE}};',
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
				echo '<div class="eap-widget eap-loop-carousel eap-loop-carousel--notice">' . esc_html__( 'Choose an item template to build the carousel from.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$spec = array(
			'source'          => 'latest',
			'post_type'       => ! empty( $settings['post_type'] ) ? $settings['post_type'] : 'post',
			'per_page'        => isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 9,
			'orderby'         => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date',
			'order'           => ! empty( $settings['order'] ) ? $settings['order'] : 'DESC',
			'offset'          => isset( $settings['offset'] ) ? (int) $settings['offset'] : 0,
			'include_terms'   => isset( $settings['include_terms'] ) ? $settings['include_terms'] : array(),
			'exclude_terms'   => isset( $settings['exclude_terms'] ) ? $settings['exclude_terms'] : array(),
			'exclude_current' => 'yes' === ( $settings['exclude_current'] ?? '' ),
			'current_id'      => $this->eap_get_post_id(),
			'ignore_sticky'   => 'yes' === ( $settings['ignore_sticky'] ?? 'yes' ),
		);

		$args  = EAP_Posts_Query::build_query_args( $spec, 1 );
		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			printf(
				'<div class="eap-widget eap-loop-carousel eap-loop-carousel--notice">%s</div>',
				esc_html( $settings['empty_text'] ?? '' )
			);
			return;
		}

		// A template containing this (or another) loop widget pointing back at it
		// would recurse forever. Shared, template-ID-keyed guard on the base.
		if ( ! $this->eap_template_guard_enter( $template_id ) ) {
			return;
		}

		// Enqueue the template's generated CSS once, so each iteration can render
		// content without re-inlining styles.
		$this->eap_enqueue_template_css( $template_id );

		$show_arrows     = 'yes' === ( $settings['show_arrows'] ?? 'yes' );
		$show_pagination = 'yes' === ( $settings['show_pagination'] ?? 'yes' );

		$slider_data = array(
			'slidesDesktop'  => isset( $settings['slides_desktop'] ) ? max( 1, (int) $settings['slides_desktop'] ) : 3,
			'slidesTablet'   => isset( $settings['slides_tablet'] ) ? max( 1, (int) $settings['slides_tablet'] ) : 2,
			'slidesMobile'   => isset( $settings['slides_mobile'] ) ? max( 1, (int) $settings['slides_mobile'] ) : 1,
			'spaceBetween'   => isset( $settings['space_between']['size'] ) ? (int) $settings['space_between']['size'] : 24,
			'speed'          => isset( $settings['speed'] ) ? max( 100, (int) $settings['speed'] ) : 600,
			'loop'           => 'yes' === ( $settings['loop'] ?? '' ),
			'centered'       => 'yes' === ( $settings['centered_slides'] ?? '' ),
			'autoplay'       => 'yes' === ( $settings['autoplay'] ?? '' ),
			'autoplayDelay'  => isset( $settings['autoplay_delay'] ) ? (float) $settings['autoplay_delay'] : 4,
			'pauseOnHover'   => 'yes' === ( $settings['pause_on_hover'] ?? 'yes' ),
			'navigation'     => $show_arrows,
			'pagination'     => $show_pagination,
			'paginationType' => ! empty( $settings['pagination_type'] ) ? $settings['pagination_type'] : 'bullets',
		);

		$this->add_render_attribute( 'wrapper', 'class', array( 'eap-widget', 'eap-loop-carousel' ) );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="eap-loop-carousel__swiper swiper" data-eap-loop-carousel="<?php echo esc_attr( wp_json_encode( $slider_data ) ); ?>">
				<div class="swiper-wrapper">
					<?php
					while ( $query->have_posts() ) :
						// A real loop, because this is what sets the global $post the
						// Dynamic widgets inside the template read.
						$query->the_post();
						?>
						<div class="swiper-slide eap-loop-carousel__slide">
							<div class="eap-loop-carousel__item">
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor-rendered template content.
								echo $this->eap_render_template( $template_id );
								?>
							</div>
						</div>
						<?php
					endwhile;
					?>
				</div>
			</div>

			<?php if ( $show_arrows ) : ?>
				<button type="button" class="eap-loop-carousel__arrow eap-loop-carousel__arrow--prev" aria-label="<?php echo esc_attr__( 'Previous', 'elementor-animatepro' ); ?>">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
				<button type="button" class="eap-loop-carousel__arrow eap-loop-carousel__arrow--next" aria-label="<?php echo esc_attr__( 'Next', 'elementor-animatepro' ); ?>">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
			<?php endif; ?>

			<?php if ( $show_pagination ) : ?>
				<div class="eap-loop-carousel__pagination"></div>
			<?php endif; ?>
		</div>
		<?php
		wp_reset_postdata();
		$this->eap_template_guard_leave( $template_id );
	}
}
