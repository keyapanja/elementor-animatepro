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
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-stacked-cards.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-social-share.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-site-logo.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-nav-menu.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-mega-menu.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-post-title.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-post-featured-image.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-post-excerpt.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-post-content.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-post-meta-info.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-post-comments.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-post-reactions.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-post-pagination.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-posts.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-post-rating.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-post-rating-form.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-advanced-posts.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-filterable-posts.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-featured-posts.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-archive-title.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-current-date.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-portfolio.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-toggle-switch.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-advanced-pricing-table.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-scroll-elements.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-filterable-gallery.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-breadcrumbs.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-table-of-contents.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-author-box.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-posts-timeline.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-posts-read-later.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-video-story.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-posts-slider.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-breaking-news-slider.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-category-slider.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-category-showcase.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-video-box-slider.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-filterable-slider.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-loop-grid.php';
		require_once EAP_PATH . 'includes/widgets/class-eap-widget-loop-carousel.php';

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
		$stacked_cards_on          = ! array_key_exists( 'stacked-cards', $widget_states ) || ! empty( $widget_states['stacked-cards'] );
		$social_share_on           = ! array_key_exists( 'social-share', $widget_states ) || ! empty( $widget_states['social-share'] );
		$site_logo_on              = ! array_key_exists( 'site-logo', $widget_states ) || ! empty( $widget_states['site-logo'] );
		$nav_menu_on               = ! array_key_exists( 'nav-menu', $widget_states ) || ! empty( $widget_states['nav-menu'] );
		$mega_menu_on              = ! array_key_exists( 'mega-menu', $widget_states ) || ! empty( $widget_states['mega-menu'] );
		$post_title_on             = ! array_key_exists( 'post-title', $widget_states ) || ! empty( $widget_states['post-title'] );
		$post_featured_image_on    = ! array_key_exists( 'post-featured-image', $widget_states ) || ! empty( $widget_states['post-featured-image'] );
		$post_excerpt_on           = ! array_key_exists( 'post-excerpt', $widget_states ) || ! empty( $widget_states['post-excerpt'] );
		$post_content_on           = ! array_key_exists( 'post-content', $widget_states ) || ! empty( $widget_states['post-content'] );
		$post_meta_info_on         = ! array_key_exists( 'post-meta-info', $widget_states ) || ! empty( $widget_states['post-meta-info'] );
		$post_comments_on          = ! array_key_exists( 'post-comments', $widget_states ) || ! empty( $widget_states['post-comments'] );
		$post_reactions_on         = ! array_key_exists( 'post-reactions', $widget_states ) || ! empty( $widget_states['post-reactions'] );
		$post_pagination_on        = ! array_key_exists( 'post-pagination', $widget_states ) || ! empty( $widget_states['post-pagination'] );
		$posts_on                  = ! array_key_exists( 'posts', $widget_states ) || ! empty( $widget_states['posts'] );
		$post_rating_on            = ! array_key_exists( 'post-rating', $widget_states ) || ! empty( $widget_states['post-rating'] );
		$post_rating_form_on       = ! array_key_exists( 'post-rating-form', $widget_states ) || ! empty( $widget_states['post-rating-form'] );
		$advanced_posts_on         = ! array_key_exists( 'advanced-posts', $widget_states ) || ! empty( $widget_states['advanced-posts'] );
		$filterable_posts_on       = ! array_key_exists( 'filterable-posts', $widget_states ) || ! empty( $widget_states['filterable-posts'] );
		$featured_posts_on         = ! array_key_exists( 'featured-posts', $widget_states ) || ! empty( $widget_states['featured-posts'] );
		$archive_title_on          = ! array_key_exists( 'archive-title', $widget_states ) || ! empty( $widget_states['archive-title'] );
		$current_date_on           = ! array_key_exists( 'current-date', $widget_states ) || ! empty( $widget_states['current-date'] );
		$portfolio_on              = ! array_key_exists( 'portfolio', $widget_states ) || ! empty( $widget_states['portfolio'] );
		$toggle_switch_on          = ! array_key_exists( 'toggle-switch', $widget_states ) || ! empty( $widget_states['toggle-switch'] );
		$adv_pricing_table_on      = ! array_key_exists( 'advanced-pricing-table', $widget_states ) || ! empty( $widget_states['advanced-pricing-table'] );
		$scroll_elements_on        = ! array_key_exists( 'scroll-elements', $widget_states ) || ! empty( $widget_states['scroll-elements'] );
		$filterable_gallery_on     = ! array_key_exists( 'filterable-gallery', $widget_states ) || ! empty( $widget_states['filterable-gallery'] );
		$breadcrumbs_on            = ! array_key_exists( 'breadcrumbs', $widget_states ) || ! empty( $widget_states['breadcrumbs'] );
		$table_of_content_on       = ! array_key_exists( 'table-of-content', $widget_states ) || ! empty( $widget_states['table-of-content'] );
		$author_box_on             = ! array_key_exists( 'author-box', $widget_states ) || ! empty( $widget_states['author-box'] );
		$posts_timeline_on         = ! array_key_exists( 'posts-timeline', $widget_states ) || ! empty( $widget_states['posts-timeline'] );
		$posts_read_later_on       = ! array_key_exists( 'posts-read-later', $widget_states ) || ! empty( $widget_states['posts-read-later'] );
		$video_story_on            = ! array_key_exists( 'video-story', $widget_states ) || ! empty( $widget_states['video-story'] );
		$posts_slider_on           = ! array_key_exists( 'posts-slider', $widget_states ) || ! empty( $widget_states['posts-slider'] );
		$breaking_news_on          = ! array_key_exists( 'breaking-news-slider', $widget_states ) || ! empty( $widget_states['breaking-news-slider'] );
		$category_slider_on        = ! array_key_exists( 'category-slider', $widget_states ) || ! empty( $widget_states['category-slider'] );
		$category_showcase_on      = ! array_key_exists( 'category-showcase', $widget_states ) || ! empty( $widget_states['category-showcase'] );
		$video_box_slider_on       = ! array_key_exists( 'video-box-slider', $widget_states ) || ! empty( $widget_states['video-box-slider'] );
		$filterable_slider_on      = ! array_key_exists( 'filterable-slider', $widget_states ) || ! empty( $widget_states['filterable-slider'] );
		$loop_grid_on              = ! array_key_exists( 'loop-grid', $widget_states ) || ! empty( $widget_states['loop-grid'] );
		$loop_carousel_on          = ! array_key_exists( 'loop-carousel', $widget_states ) || ! empty( $widget_states['loop-carousel'] );

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

		if ( $stacked_cards_on ) {
			$widgets_manager->register( new EAP_Widget_Stacked_Cards() );
		}

		if ( $social_share_on ) {
			$widgets_manager->register( new EAP_Widget_Social_Share() );
		}

		if ( $site_logo_on ) {
			$widgets_manager->register( new EAP_Widget_Site_Logo() );
		}

		if ( $nav_menu_on ) {
			$widgets_manager->register( new EAP_Widget_Nav_Menu() );
		}

		if ( $mega_menu_on ) {
			$widgets_manager->register( new EAP_Widget_Mega_Menu() );
		}

		if ( $post_title_on ) {
			$widgets_manager->register( new EAP_Widget_Post_Title() );
		}

		if ( $post_featured_image_on ) {
			$widgets_manager->register( new EAP_Widget_Post_Featured_Image() );
		}

		if ( $post_excerpt_on ) {
			$widgets_manager->register( new EAP_Widget_Post_Excerpt() );
		}

		if ( $post_content_on ) {
			$widgets_manager->register( new EAP_Widget_Post_Content() );
		}

		if ( $post_meta_info_on ) {
			$widgets_manager->register( new EAP_Widget_Post_Meta_Info() );
		}

		if ( $post_comments_on ) {
			$widgets_manager->register( new EAP_Widget_Post_Comments() );
		}

		if ( $post_reactions_on ) {
			$widgets_manager->register( new EAP_Widget_Post_Reactions() );
		}

		if ( $post_pagination_on ) {
			$widgets_manager->register( new EAP_Widget_Post_Pagination() );
		}

		if ( $posts_on ) {
			$widgets_manager->register( new EAP_Widget_Posts() );
		}

		if ( $post_rating_on ) {
			$widgets_manager->register( new EAP_Widget_Post_Rating() );
		}

		if ( $post_rating_form_on ) {
			$widgets_manager->register( new EAP_Widget_Post_Rating_Form() );
		}

		if ( $advanced_posts_on ) {
			$widgets_manager->register( new EAP_Widget_Advanced_Posts() );
		}

		if ( $filterable_posts_on ) {
			$widgets_manager->register( new EAP_Widget_Filterable_Posts() );
		}

		if ( $featured_posts_on ) {
			$widgets_manager->register( new EAP_Widget_Featured_Posts() );
		}

		if ( $archive_title_on ) {
			$widgets_manager->register( new EAP_Widget_Archive_Title() );
		}

		if ( $current_date_on ) {
			$widgets_manager->register( new EAP_Widget_Current_Date() );
		}

		if ( $portfolio_on ) {
			$widgets_manager->register( new EAP_Widget_Portfolio() );
		}

		if ( $toggle_switch_on ) {
			$widgets_manager->register( new EAP_Widget_Toggle_Switch() );
		}

		if ( $adv_pricing_table_on ) {
			$widgets_manager->register( new EAP_Widget_Advanced_Pricing_Table() );
		}

		if ( $scroll_elements_on ) {
			$widgets_manager->register( new EAP_Widget_Scroll_Elements() );
		}

		if ( $filterable_gallery_on ) {
			$widgets_manager->register( new EAP_Widget_Filterable_Gallery() );
		}

		if ( $breadcrumbs_on ) {
			$widgets_manager->register( new EAP_Widget_Breadcrumbs() );
		}

		if ( $table_of_content_on ) {
			$widgets_manager->register( new EAP_Widget_Table_Of_Contents() );
		}

		if ( $author_box_on ) {
			$widgets_manager->register( new EAP_Widget_Author_Box() );
		}

		if ( $posts_timeline_on ) {
			$widgets_manager->register( new EAP_Widget_Posts_Timeline() );
		}

		if ( $posts_read_later_on ) {
			$widgets_manager->register( new EAP_Widget_Posts_Read_Later() );
		}

		if ( $video_story_on ) {
			$widgets_manager->register( new EAP_Widget_Video_Story() );
		}

		if ( $posts_slider_on ) {
			$widgets_manager->register( new EAP_Widget_Posts_Slider() );
		}

		if ( $breaking_news_on ) {
			$widgets_manager->register( new EAP_Widget_Breaking_News_Slider() );
		}

		if ( $category_slider_on ) {
			$widgets_manager->register( new EAP_Widget_Category_Slider() );
		}

		if ( $category_showcase_on ) {
			$widgets_manager->register( new EAP_Widget_Category_Showcase() );
		}

		if ( $video_box_slider_on ) {
			$widgets_manager->register( new EAP_Widget_Video_Box_Slider() );
		}

		if ( $filterable_slider_on ) {
			$widgets_manager->register( new EAP_Widget_Filterable_Slider() );
		}

		if ( $loop_grid_on ) {
			$widgets_manager->register( new EAP_Widget_Loop_Grid() );
		}

		if ( $loop_carousel_on ) {
			$widgets_manager->register( new EAP_Widget_Loop_Carousel() );
		}

		// Content Toggle and Animated Off-Canvas are nested widgets — only load
		// them when Elementor's Nested Elements base class is available,
		// otherwise the classes would fatal on extend.
		if ( class_exists( '\Elementor\Modules\NestedElements\Base\Widget_Nested_Base' ) ) {
			require_once EAP_PATH . 'includes/widgets/class-eap-widget-content-toggle.php';
			require_once EAP_PATH . 'includes/widgets/class-eap-widget-animated-off-canvas.php';

			$content_toggle_on = ! array_key_exists( 'content-toggle', $widget_states ) || ! empty( $widget_states['content-toggle'] );
			$off_canvas_on     = ! array_key_exists( 'animated-off-canvas', $widget_states ) || ! empty( $widget_states['animated-off-canvas'] );

			if ( $content_toggle_on ) {
				$widgets_manager->register( new EAP_Widget_Content_Toggle() );
			}

			if ( $off_canvas_on ) {
				$widgets_manager->register( new EAP_Widget_Animated_Off_Canvas() );
			}
		}
	}
}
