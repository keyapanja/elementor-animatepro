<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_Elementor_Manager {

	/**
	 * Module manager.
	 *
	 * @var EAP_Module_Manager
	 */
	private $modules;

	/**
	 * Widget map.
	 *
	 * @var array<string, array<string, string>>
	 */
	private $widget_map = array(
		'widgets_animated_heading'  => array(
			'file'  => 'class-eap-widget-animated-heading.php',
			'class' => 'EAP_Widget_Animated_Heading',
		),
		'widgets_typewriter'        => array(
			'file'  => 'class-eap-widget-typewriter.php',
			'class' => 'EAP_Widget_Typewriter',
		),
		'widgets_icon_box'          => array(
			'file'  => 'class-eap-widget-icon-box.php',
			'class' => 'EAP_Widget_Icon_Box',
		),
		'widgets_image_box'         => array(
			'file'  => 'class-eap-widget-image-box.php',
			'class' => 'EAP_Widget_Image_Box',
		),
		'widgets_counter'           => array(
			'file'  => 'class-eap-widget-counter.php',
			'class' => 'EAP_Widget_Counter',
		),
		'widgets_testimonial'       => array(
			'file'  => 'class-eap-widget-testimonial.php',
			'class' => 'EAP_Widget_Testimonial',
		),
		'widgets_testimonial_slider' => array(
			'file'  => 'class-eap-widget-testimonial-slider.php',
			'class' => 'EAP_Widget_Testimonial_Slider',
		),
		'widgets_pricing_table'     => array(
			'file'  => 'class-eap-widget-pricing-table.php',
			'class' => 'EAP_Widget_Pricing_Table',
		),
		'widgets_portfolio_grid'    => array(
			'file'  => 'class-eap-widget-portfolio-grid.php',
			'class' => 'EAP_Widget_Portfolio_Grid',
		),
		'widgets_brand_marquee'     => array(
			'file'  => 'class-eap-widget-brand-marquee.php',
			'class' => 'EAP_Widget_Brand_Marquee',
		),
		'widgets_accordion'         => array(
			'file'  => 'class-eap-widget-accordion.php',
			'class' => 'EAP_Widget_Accordion',
		),
		'widgets_table_of_contents' => array(
			'file'  => 'class-eap-widget-table-of-contents.php',
			'class' => 'EAP_Widget_Table_Of_Contents',
		),
		'widgets_animated_text'     => array(
			'file'  => 'class-eap-widget-animated-text.php',
			'class' => 'EAP_Widget_Animated_Text',
		),
		'widgets_horizontal_text'   => array(
			'file'  => 'class-eap-widget-horizontal-text.php',
			'class' => 'EAP_Widget_Horizontal_Text',
		),
		'widgets_horizontal_gallery' => array(
			'file'  => 'class-eap-widget-horizontal-gallery.php',
			'class' => 'EAP_Widget_Horizontal_Gallery',
		),
		'widgets_animated_button'   => array(
			'file'  => 'class-eap-widget-animated-button.php',
			'class' => 'EAP_Widget_Animated_Button',
		),
		'widgets_text_mask'         => array(
			'file'  => 'class-eap-widget-text-mask.php',
			'class' => 'EAP_Widget_Text_Mask',
		),
		'widgets_cursor_preview_list' => array(
			'file'  => 'class-eap-widget-cursor-preview-list.php',
			'class' => 'EAP_Widget_Cursor_Preview_List',
		),
		'widgets_logo'              => array(
			'file'  => 'class-eap-widget-logo.php',
			'class' => 'EAP_Widget_Logo',
		),
		'widgets_menu'              => array(
			'file'  => 'class-eap-widget-menu.php',
			'class' => 'EAP_Widget_Menu',
		),
		'widgets_offcanvas'         => array(
			'file'  => 'class-eap-widget-offcanvas.php',
			'class' => 'EAP_Widget_Offcanvas',
		),
	);

	/**
	 * Constructor.
	 *
	 * @param EAP_Module_Manager $modules Module manager.
	 */
	public function __construct( EAP_Module_Manager $modules ) {
		$this->modules = $modules;

		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	/**
	 * Register category.
	 *
	 * @param Elementor\Elements_Manager $elements_manager Manager.
	 * @return void
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'eap-elements',
			array(
				'title' => __( 'AnimatePro', 'elementor-animatepro' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * Register widgets.
	 *
	 * @param Elementor\Widgets_Manager $widgets_manager Widgets manager.
	 * @return void
	 */
	public function register_widgets( $widgets_manager ) {
		if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
			return;
		}

		require_once EAP_PATH . 'includes/widgets/class-eap-widget-base.php';

		foreach ( $this->widget_map as $module_key => $widget ) {
			if ( ! $this->modules->is_enabled( $module_key ) ) {
				continue;
			}

			require_once EAP_PATH . 'includes/widgets/' . $widget['file'];
			$widgets_manager->register( new $widget['class']() );
		}
	}
}
