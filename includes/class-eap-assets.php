<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_Assets {

	/**
	 * Instance.
	 *
	 * @var EAP_Assets|null
	 */
	private static $instance = null;

	/**
	 * Whether runtime assets were enqueued.
	 *
	 * @var bool
	 */
	private $runtime_assets_loaded = false;

	/**
	 * Get instance.
	 *
	 * @return EAP_Assets
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->register_frontend_assets();
		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_assets' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'register_frontend_assets' ) );
		add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'register_editor_assets' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'register_editor_assets' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_editor_assets' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_editor_assets' ) );
	}

	/**
	 * Register assets only.
	 *
	 * @return void
	 */
	public function register_frontend_assets() {
		wp_register_style(
			'eap-core',
			EAP_URL . 'assets/css/core.css',
			array(),
			EAP_VERSION
		);

		wp_register_script(
			'eap-core-runtime',
			EAP_URL . 'assets/js/core.js',
			array(),
			EAP_VERSION,
			true
		);

		$this->register_widget_styles();
		$this->register_widget_scripts();
	}

	/**
	 * Register widget styles.
	 *
	 * @return void
	 */
	private function register_widget_styles() {
		$styles = array(
			'testimonial',
			'testimonial-slider',
			'icon-box',
			'image-box',
			'image-box-slider',
			'image-hotspot',
			'social-icons',
			'image-gallery',
			'image-comparison',
			'parallax-sections',
			'text-hover-image',
			'brand-slider',
			'image',
			'animated-text',
			'advanced-animated-text',
			'advanced-button',
		);

		foreach ( $styles as $style ) {
			wp_register_style(
				'eap-' . $style,
				EAP_URL . 'assets/css/widgets/' . $style . '.css',
				array( 'eap-core' ),
				EAP_VERSION
			);
		}
	}

	/**
	 * Register widget scripts.
	 *
	 * @return void
	 */
	private function register_widget_scripts() {
		$scripts = array(
			'visibility',
			'testimonial-slider',
			'animated-text',
			'advanced-animated-text',
			'text-hover-image',
			'image-box',
			'image-box-slider',
			'image-hotspot',
			'image-gallery',
			'image-comparison',
			'brand-slider',
			'parallax-sections',
		);

		foreach ( $scripts as $script ) {
			wp_register_script(
				'eap-' . $script . '-script',
				EAP_URL . 'assets/js/widgets/' . $script . '.js',
				array( 'eap-core-runtime' ),
				EAP_VERSION,
				true
			);
		}
	}

	/**
	 * Register editor-only assets.
	 *
	 * @return void
	 */
	public function register_editor_assets() {
		wp_register_style(
			'eap-editor',
			EAP_URL . 'assets/css/editor.css',
			array(),
			EAP_VERSION
		);

		wp_register_script(
			'eap-editor',
			EAP_URL . 'assets/js/editor.js',
			array(),
			EAP_VERSION,
			true
		);
	}

	/**
	 * Enqueue editor-only assets.
	 *
	 * @return void
	 */
	public function enqueue_editor_assets() {
		wp_enqueue_style( 'eap-editor' );
		wp_enqueue_script( 'eap-editor' );
	}

	/**
	 * Enqueue shared runtime assets.
	 *
	 * @return void
	 */
	public function enqueue_runtime_assets() {
		if ( $this->runtime_assets_loaded ) {
			return;
		}

		$this->runtime_assets_loaded = true;

		wp_enqueue_style( 'eap-core' );
		wp_enqueue_script( 'eap-core-runtime' );
	}
}
