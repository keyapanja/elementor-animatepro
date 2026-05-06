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
