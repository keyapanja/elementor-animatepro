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
