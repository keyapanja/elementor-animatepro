<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_Elementor {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	/**
	 * Register AnimatePro category.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elementor manager.
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
	 * Register enabled widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widget manager.
	 * @return void
	 */
	public function register_widgets( $widgets_manager ) {
		if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
			return;
		}

		require_once EAP_PATH . 'includes/widgets/class-eap-widget-base.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-image-box.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-image-box-slider.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-icon-box.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-image-hotspot.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-social-icons.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-image.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-image-gallery.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-image-comparison.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-progress-bar.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-team.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-parallax-sections.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-text-hover-image.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-brand-slider.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-testimonial.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-testimonial-slider.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-animated-button.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-animated-text.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-advanced-animated-text.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-timeline.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-services-tabs.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-one-page-nav.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-advanced-testimonial-slider.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-advanced-slider.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-countdown.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-image-accordion.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-multi-buttons.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-price-box.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-data-table.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-feature-list.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-sticky-video.php';

		$widget_states = get_option( EAP_Admin::WIDGETS_OPTION, array() );
		$widget_states = is_array( $widget_states ) ? $widget_states : array();
		$image_box_on  = ! array_key_exists( 'image-box', $widget_states ) || ! empty( $widget_states['image-box'] );
		$image_box_slider_on = ! array_key_exists( 'image-box-slider', $widget_states ) || ! empty( $widget_states['image-box-slider'] );
		$icon_box_on        = ! array_key_exists( 'icon-box', $widget_states ) || ! empty( $widget_states['icon-box'] );
		$image_hotspot_on   = ! array_key_exists( 'image-hotspot', $widget_states ) || ! empty( $widget_states['image-hotspot'] );
		$social_icons_on    = ! array_key_exists( 'social-icons', $widget_states ) || ! empty( $widget_states['social-icons'] );
		$image_on           = ! array_key_exists( 'image', $widget_states ) || ! empty( $widget_states['image'] );
		$image_gallery_on   = ! array_key_exists( 'image-gallery', $widget_states ) || ! empty( $widget_states['image-gallery'] );
		$image_comparison_on = ! array_key_exists( 'image-comparison', $widget_states ) || ! empty( $widget_states['image-comparison'] );
		$progress_bar_on    = ! array_key_exists( 'progress-bar', $widget_states ) || ! empty( $widget_states['progress-bar'] );
		$team_on            = ! array_key_exists( 'team', $widget_states ) || ! empty( $widget_states['team'] );
		$parallax_sections_on = ! array_key_exists( 'parallax-sections', $widget_states ) || ! empty( $widget_states['parallax-sections'] );
		$text_hover_image_on = ! array_key_exists( 'text-hover-image', $widget_states ) || ! empty( $widget_states['text-hover-image'] );
		$brand_slider_on    = ! array_key_exists( 'brand-slider', $widget_states ) || ! empty( $widget_states['brand-slider'] );
		$testimonial_on     = ! array_key_exists( 'testimonial-box', $widget_states ) || ! empty( $widget_states['testimonial-box'] );
		$testimonial_slider_on = ! array_key_exists( 'testimonial-slider', $widget_states ) || ! empty( $widget_states['testimonial-slider'] );
		$advanced_button_on = ! array_key_exists( 'advanced-button', $widget_states ) || ! empty( $widget_states['advanced-button'] );
		$animated_text_on   = ! array_key_exists( 'animated-text', $widget_states ) || ! empty( $widget_states['animated-text'] );
		$advanced_animated_text_on = ! array_key_exists( 'advanced-animated-text', $widget_states ) || ! empty( $widget_states['advanced-animated-text'] );
		$timeline_on        = ! array_key_exists( 'timeline', $widget_states ) || ! empty( $widget_states['timeline'] );
		$services_tabs_on   = ! array_key_exists( 'services-tabs', $widget_states ) || ! empty( $widget_states['services-tabs'] );
		$one_page_nav_on    = ! array_key_exists( 'one-page-nav', $widget_states ) || ! empty( $widget_states['one-page-nav'] );
		$adv_testimonial_slider_on = ! array_key_exists( 'advanced-testimonial-slider', $widget_states ) || ! empty( $widget_states['advanced-testimonial-slider'] );
		$advanced_slider_on        = ! array_key_exists( 'advanced-slider', $widget_states ) || ! empty( $widget_states['advanced-slider'] );
		$countdown_on              = ! array_key_exists( 'countdown', $widget_states ) || ! empty( $widget_states['countdown'] );
		$image_accordion_on        = ! array_key_exists( 'image-accordion', $widget_states ) || ! empty( $widget_states['image-accordion'] );
		$multi_buttons_on          = ! array_key_exists( 'multi-buttons', $widget_states ) || ! empty( $widget_states['multi-buttons'] );
		$price_box_on              = ! array_key_exists( 'price-box', $widget_states ) || ! empty( $widget_states['price-box'] );
		$data_table_on             = ! array_key_exists( 'data-table', $widget_states ) || ! empty( $widget_states['data-table'] );
		$feature_list_on           = ! array_key_exists( 'feature-list', $widget_states ) || ! empty( $widget_states['feature-list'] );
		$sticky_video_on           = ! array_key_exists( 'sticky-video', $widget_states ) || ! empty( $widget_states['sticky-video'] );

		if ( $image_box_on ) {
			$widgets_manager->register( new EAP_Widget_Image_Box() );
		}

		if ( $image_box_slider_on ) {
			$widgets_manager->register( new EAP_Widget_Image_Box_Slider() );
		}

		if ( $icon_box_on ) {
			$widgets_manager->register( new EAP_Widget_Icon_Box() );
		}

		if ( $image_hotspot_on ) {
			$widgets_manager->register( new EAP_Widget_Image_Hotspot() );
		}

		if ( $social_icons_on ) {
			$widgets_manager->register( new EAP_Widget_Social_Icons() );
		}

		if ( $image_on ) {
			$widgets_manager->register( new EAP_Widget_Image() );
		}

		if ( $image_gallery_on ) {
			$widgets_manager->register( new EAP_Widget_Image_Gallery() );
		}

		if ( $image_comparison_on ) {
			$widgets_manager->register( new EAP_Widget_Image_Comparison() );
		}

		if ( $progress_bar_on ) {
			$widgets_manager->register( new EAP_Widget_Progress_Bar() );
		}

		if ( $team_on ) {
			$widgets_manager->register( new EAP_Widget_Team() );
		}

		if ( $parallax_sections_on ) {
			$widgets_manager->register( new EAP_Widget_Parallax_Sections() );
		}

		if ( $text_hover_image_on ) {
			$widgets_manager->register( new EAP_Widget_Text_Hover_Image() );
		}

		if ( $brand_slider_on ) {
			$widgets_manager->register( new EAP_Widget_Brand_Slider() );
		}

		if ( $testimonial_on ) {
			$widgets_manager->register( new EAP_Widget_Testimonial() );
		}

		if ( $testimonial_slider_on ) {
			$widgets_manager->register( new EAP_Widget_Testimonial_Slider() );
		}

		if ( $advanced_button_on ) {
			$widgets_manager->register( new EAP_Widget_Animated_Button() );
		}

		if ( $animated_text_on ) {
			$widgets_manager->register( new EAP_Widget_Animated_Text() );
		}

		if ( $advanced_animated_text_on ) {
			$widgets_manager->register( new EAP_Widget_Advanced_Animated_Text() );
		}

		if ( $timeline_on ) {
			$widgets_manager->register( new EAP_Widget_Timeline() );
		}

		if ( $services_tabs_on ) {
			$widgets_manager->register( new EAP_Widget_Services_Tabs() );
		}

		if ( $one_page_nav_on ) {
			$widgets_manager->register( new EAP_Widget_One_Page_Nav() );
		}

		if ( $adv_testimonial_slider_on ) {
			$widgets_manager->register( new EAP_Widget_Advanced_Testimonial_Slider() );
		}

		if ( $advanced_slider_on ) {
			$widgets_manager->register( new EAP_Widget_Advanced_Slider() );
		}

		if ( $countdown_on ) {
			$widgets_manager->register( new EAP_Widget_Countdown() );
		}

		if ( $image_accordion_on ) {
			$widgets_manager->register( new EAP_Widget_Image_Accordion() );
		}

		if ( $multi_buttons_on ) {
			$widgets_manager->register( new EAP_Widget_Multi_Buttons() );
		}

		if ( $price_box_on ) {
			$widgets_manager->register( new EAP_Widget_Price_Box() );
		}

		if ( $data_table_on ) {
			$widgets_manager->register( new EAP_Widget_Data_Table() );
		}

		if ( $feature_list_on ) {
			$widgets_manager->register( new EAP_Widget_Feature_List() );
		}

		if ( $sticky_video_on ) {
			$widgets_manager->register( new EAP_Widget_Sticky_Video() );
		}

		// Content Toggle is a nested widget — only load it when Elementor's
		// Nested Elements base class is available, otherwise the class would
		// fatal on extend.
		if ( class_exists( '\Elementor\Modules\NestedElements\Base\Widget_Nested_Base' ) ) {
			require_once EAP_PATH . 'includes/widgets/class-eap-widget-content-toggle.php';

			$content_toggle_on = ! array_key_exists( 'content-toggle', $widget_states ) || ! empty( $widget_states['content-toggle'] );

			if ( $content_toggle_on ) {
				$widgets_manager->register( new EAP_Widget_Content_Toggle() );
			}
		}
	}
}
