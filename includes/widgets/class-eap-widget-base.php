<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

abstract class EAP_Widget_Base extends Widget_Base {

	/**
	 * Widget category.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'eap-elements' );
	}

	/**
	 * Shared style dependency.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core' );
	}

	/**
	 * Shared script dependency.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return array();
	}

	/**
	 * Shared runtime dependency for interactive widgets.
	 *
	 * @return string[]
	 */
	protected function get_runtime_script_depends() {
		return array( 'eap-core-runtime' );
	}

	/**
	 * Shared visibility dependency.
	 *
	 * @return string[]
	 */
	protected function get_visibility_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-visibility-script',
		);
	}

	/**
	 * Widget style dependency helper.
	 *
	 * @param string $handle Widget handle suffix.
	 * @return string[]
	 */
	protected function get_widget_style_depends( $handle ) {
		return array(
			'eap-core',
			'eap-' . $handle,
		);
	}

	/**
	 * Append Elementor's Swiper stylesheet to a widget's style dependencies.
	 *
	 * Elementor REGISTERS `e-swiper` but never enqueues it (includes/frontend.php)
	 * — its own carousels pull it in through get_style_depends(), e.g. Image
	 * Carousel returns [ 'e-swiper', 'widget-image-carousel' ]. A slider that
	 * doesn't declare it therefore gets NO Swiper CSS on a page that happens to
	 * contain no Elementor carousel, and every slide stacks full-width.
	 *
	 * `e-swiper` itself depends on the `swiper` library handle, so asking for it
	 * pulls both. The older `swiper` handle is the fallback for Elementor builds
	 * that predate `e-swiper`.
	 *
	 * @param string[] $deps Existing style handles.
	 * @return string[]
	 */
	protected function eap_with_swiper_style( $deps ) {
		$deps = (array) $deps;

		if ( wp_style_is( 'e-swiper', 'registered' ) ) {
			$deps[] = 'e-swiper';
		} elseif ( wp_style_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}

		return array_values( array_unique( $deps ) );
	}

	/**
	 * Whether Elementor is in editor edit-mode (the panel / canvas being edited).
	 *
	 * Dynamic widgets use this to render a sample/placeholder instead of the real
	 * (possibly missing) post data so the widget is never blank on the canvas.
	 *
	 * @return bool
	 */
	protected function eap_is_editor() {
		return class_exists( '\Elementor\Plugin' )
			&& isset( \Elementor\Plugin::$instance->editor )
			&& \Elementor\Plugin::$instance->editor->is_edit_mode();
	}

	/**
	 * Templates currently mid-render, keyed by ID.
	 *
	 * Shared by every loop widget (Loop Grid, Loop Carousel, …) so recursion is
	 * caught ACROSS widget types too: a Loop Carousel whose item template holds a
	 * Loop Grid pointing back at it would otherwise never terminate. Keying by
	 * template ID also catches A -> B -> A template chains, which a per-widget
	 * boolean cannot.
	 *
	 * @var array<int, bool>
	 */
	protected static $eap_rendering_templates = array();

	/**
	 * Claim a template for rendering.
	 *
	 * @param int $template_id Template ID.
	 * @return bool False when it is already being rendered further up the stack.
	 */
	protected function eap_template_guard_enter( $template_id ) {
		$template_id = (int) $template_id;

		if ( isset( self::$eap_rendering_templates[ $template_id ] ) ) {
			return false;
		}

		self::$eap_rendering_templates[ $template_id ] = true;

		return true;
	}

	/**
	 * Release a template claimed by eap_template_guard_enter().
	 *
	 * @param int $template_id Template ID.
	 * @return void
	 */
	protected function eap_template_guard_leave( $template_id ) {
		unset( self::$eap_rendering_templates[ (int) $template_id ] );
	}

	/**
	 * Saved Elementor templates, labelled with their template type.
	 *
	 * @return array<string, string>
	 */
	protected function eap_get_template_options() {
		$options = array( '' => __( '— Select a template —', 'elementor-animatepro' ) );

		if ( ! post_type_exists( 'elementor_library' ) ) {
			return $options;
		}

		$templates = get_posts(
			array(
				'post_type'      => 'elementor_library',
				'post_status'    => 'publish',
				'posts_per_page' => 100,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		foreach ( $templates as $template ) {
			$type  = get_post_meta( $template->ID, '_elementor_template_type', true );
			$label = '' !== $template->post_title ? $template->post_title : sprintf( '#%d', $template->ID );

			if ( $type ) {
				$label .= ' (' . ucwords( str_replace( array( '-', '_' ), ' ', $type ) ) . ')';
			}

			$options[ $template->ID ] = $label;
		}

		return $options;
	}

	/**
	 * Enqueue a template's generated CSS file.
	 *
	 * Called ONCE before a loop: passing $with_css to
	 * get_builder_content_for_display() would re-inline the whole stylesheet on
	 * every iteration instead.
	 *
	 * @param int $template_id Template ID.
	 * @return void
	 */
	protected function eap_enqueue_template_css( $template_id ) {
		if ( ! class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			return;
		}

		\Elementor\Core\Files\CSS\Post::create( (int) $template_id )->enqueue();
	}

	/**
	 * Render a saved template for the CURRENT post in the loop.
	 *
	 * @param int $template_id Template ID.
	 * @return string
	 */
	protected function eap_render_template( $template_id ) {
		if ( ! class_exists( '\Elementor\Plugin' ) || ! isset( \Elementor\Plugin::$instance->frontend ) ) {
			return '';
		}

		return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( (int) $template_id, false );
	}

	/**
	 * Resolve the "current post" for a dynamic widget.
	 *
	 * Front-end: the post in the loop (get_the_ID) or the queried object. In the
	 * editor, when there is no real post in context (e.g. editing a header/footer
	 * or a library template), fall back to the most recent published post so the
	 * preview shows live data instead of rendering empty.
	 *
	 * @return int Post ID, or 0 when nothing is available.
	 */
	protected function eap_get_post_id() {
		$post_id = (int) get_the_ID();

		if ( ! $post_id ) {
			$post_id = (int) get_queried_object_id();
		}

		if ( ! $post_id && $this->eap_is_editor() ) {
			$recent = get_posts(
				array(
					'numberposts'      => 1,
					'post_status'      => 'publish',
					'suppress_filters' => false,
				)
			);

			if ( ! empty( $recent ) ) {
				$post_id = (int) $recent[0]->ID;
			}
		}

		return $post_id;
	}

	/**
	 * Add alignment control.
	 *
	 * @return void
	 */
	protected function add_alignment_control() {
		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
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
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .eap-widget' => 'text-align: {{VALUE}};',
				),
			)
		);
	}
}
