<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_Admin {

	/**
	 * Option key for widget states.
	 *
	 * @var string
	 */
	const WIDGETS_OPTION = 'eap_widget_states';

	/**
	 * Option key for extension states.
	 *
	 * @var string
	 */
	const EXTENSIONS_OPTION = 'eap_extension_states';

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	const MENU_SLUG = 'elementor-animatepro';

	/**
	 * Widgets page slug.
	 *
	 * @var string
	 */
	const WIDGETS_SLUG = 'elementor-animatepro-widgets';

	/**
	 * Extensions page slug.
	 *
	 * @var string
	 */
	const EXTENSIONS_SLUG = 'elementor-animatepro-extensions';

	/**
	 * Theme builder page slug.
	 *
	 * @var string
	 */
	const THEME_BUILDER_SLUG = 'elementor-animatepro-theme-builder';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'handle_form_submission' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'in_admin_header', array( $this, 'suppress_foreign_admin_notices' ), 1 );
		add_filter( 'plugin_action_links_' . plugin_basename( EAP_FILE ), array( $this, 'add_plugin_action_links' ) );
	}

	/**
	 * Register options for widgets and extensions.
	 *
	 * @return void
	 */
	public function register_settings() {
		if ( false === get_option( self::WIDGETS_OPTION, false ) ) {
			add_option( self::WIDGETS_OPTION, $this->get_default_widget_states(), '', false );
		} else {
			$this->activate_newly_built_widgets();
		}

		if ( false === get_option( self::EXTENSIONS_OPTION, false ) ) {
			add_option( self::EXTENSIONS_OPTION, $this->get_default_extension_states(), '', false );
		}
	}

	/**
	 * Enable widgets that moved from coming soon to built in this release.
	 *
	 * @return void
	 */
	private function activate_newly_built_widgets() {
		$states = get_option( self::WIDGETS_OPTION, array() );
		$states = is_array( $states ) ? $states : array();
		$done   = get_option( 'eap_widget_migrations', array() );
		$done   = is_array( $done ) ? $done : array();

		if ( empty( $done['animated-text-built'] ) ) {
			$states['animated-text'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['animated-text-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['advanced-animated-text-built'] ) ) {
			$states['advanced-animated-text'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['advanced-animated-text-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['testimonial-box-built'] ) ) {
			$states['testimonial-box'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['testimonial-box-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['testimonial-slider-built'] ) ) {
			$states['testimonial-slider'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['testimonial-slider-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['progress-bar-built'] ) ) {
			$states['progress-bar'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['progress-bar-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['team-built'] ) ) {
			$states['team'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['team-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['timeline-built'] ) ) {
			$states['timeline'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['timeline-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['services-tabs-built'] ) ) {
			$states['services-tabs'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['services-tabs-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['one-page-nav-built'] ) ) {
			$states['one-page-nav'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['one-page-nav-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['advanced-testimonial-slider-built'] ) ) {
			$states['advanced-testimonial-slider'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['advanced-testimonial-slider-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['advanced-slider-built'] ) ) {
			$states['advanced-slider'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['advanced-slider-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['countdown-built'] ) ) {
			$states['countdown'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['countdown-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['image-accordion-built'] ) ) {
			$states['image-accordion'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['image-accordion-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['content-toggle-built'] ) ) {
			$states['content-toggle'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['content-toggle-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['multi-buttons-built'] ) ) {
			$states['multi-buttons'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['multi-buttons-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['price-box-built'] ) ) {
			$states['price-box'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['price-box-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['data-table-built'] ) ) {
			$states['data-table'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['data-table-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['feature-list-built'] ) ) {
			$states['feature-list'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['feature-list-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['sticky-video-built'] ) ) {
			$states['sticky-video'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['sticky-video-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['stacked-cards-built'] ) ) {
			$states['stacked-cards'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['stacked-cards-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['social-share-built'] ) ) {
			$states['social-share'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['social-share-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['site-logo-built'] ) ) {
			$states['site-logo'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['site-logo-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['nav-menu-built'] ) ) {
			$states['nav-menu'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['nav-menu-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['mega-menu-built'] ) ) {
			$states['mega-menu'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['mega-menu-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['animated-off-canvas-built'] ) ) {
			$states['animated-off-canvas'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['animated-off-canvas-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['post-title-built'] ) ) {
			$states['post-title'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['post-title-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['post-featured-image-built'] ) ) {
			$states['post-featured-image'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['post-featured-image-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['post-excerpt-built'] ) ) {
			$states['post-excerpt'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['post-excerpt-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['post-content-built'] ) ) {
			$states['post-content'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['post-content-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['post-meta-info-built'] ) ) {
			$states['post-meta-info'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['post-meta-info-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['post-comments-built'] ) ) {
			$states['post-comments'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['post-comments-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['post-reactions-built'] ) ) {
			$states['post-reactions'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['post-reactions-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['post-pagination-built'] ) ) {
			$states['post-pagination'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['post-pagination-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['posts-built'] ) ) {
			$states['posts'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['posts-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['post-rating-built'] ) ) {
			$states['post-rating'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['post-rating-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['post-rating-form-built'] ) ) {
			$states['post-rating-form'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['post-rating-form-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['advanced-posts-built'] ) ) {
			$states['advanced-posts'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['advanced-posts-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['filterable-posts-built'] ) ) {
			$states['filterable-posts'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['filterable-posts-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['featured-posts-built'] ) ) {
			$states['featured-posts'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['featured-posts-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['archive-title-built'] ) ) {
			$states['archive-title'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['archive-title-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['current-date-built'] ) ) {
			$states['current-date'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['current-date-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['posts-timeline-built'] ) ) {
			$states['posts-timeline'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['posts-timeline-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['posts-read-later-built'] ) ) {
			$states['posts-read-later'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['posts-read-later-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['video-story-built'] ) ) {
			$states['video-story'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['video-story-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['posts-slider-built'] ) ) {
			$states['posts-slider'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['posts-slider-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['breaking-news-slider-built'] ) ) {
			$states['breaking-news-slider'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['breaking-news-slider-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['category-slider-built'] ) ) {
			$states['category-slider'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['category-slider-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['category-showcase-built'] ) ) {
			$states['category-showcase'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['category-showcase-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['video-box-slider-built'] ) ) {
			$states['video-box-slider'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['video-box-slider-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['filterable-slider-built'] ) ) {
			$states['filterable-slider'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['filterable-slider-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['loop-grid-built'] ) ) {
			$states['loop-grid'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['loop-grid-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}

		if ( empty( $done['loop-carousel-built'] ) ) {
			$states['loop-carousel'] = 1;
			update_option( self::WIDGETS_OPTION, $states, false );
			$done['loop-carousel-built'] = 1;
			update_option( 'eap_widget_migrations', $done, false );
		}
	}

	/**
	 * Handle widgets/extensions form submissions.
	 *
	 * @return void
	 */
	public function handle_form_submission() {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( empty( $_POST['eap_admin_action'] ) ) {
			return;
		}

		$action = sanitize_key( wp_unslash( $_POST['eap_admin_action'] ) );
		if ( ! in_array( $action, array( 'save_widgets', 'save_extensions' ), true ) ) {
			return;
		}

		check_admin_referer( 'eap_save_admin_settings', 'eap_admin_nonce' );

		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

		if ( 'save_widgets' === $action ) {
			$input = isset( $_POST[ self::WIDGETS_OPTION ] ) ? (array) wp_unslash( $_POST[ self::WIDGETS_OPTION ] ) : array();
			update_option( self::WIDGETS_OPTION, $this->sanitize_widget_states( $input ), false );
			$target = admin_url( 'admin.php?page=' . self::WIDGETS_SLUG . '&eap-updated=1' );
		} else {
			$input = isset( $_POST[ self::EXTENSIONS_OPTION ] ) ? (array) wp_unslash( $_POST[ self::EXTENSIONS_OPTION ] ) : array();
			update_option( self::EXTENSIONS_OPTION, $this->sanitize_extension_states( $input ), false );
			$target = admin_url( 'admin.php?page=' . self::EXTENSIONS_SLUG . '&eap-updated=1' );
		}

		if ( self::WIDGETS_SLUG !== $page && self::EXTENSIONS_SLUG !== $page ) {
			$target = admin_url( 'admin.php?page=' . self::MENU_SLUG );
		}

		wp_safe_redirect( $target );
		exit;
	}

	/**
	 * Register admin menu pages.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_menu_page(
			__( 'Elementor AnimatePro', 'elementor-animatepro' ),
			__( 'AnimatePro', 'elementor-animatepro' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'render_dashboard_page' ),
			'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCI+PHBhdGggZD0iTTEyIDEuNUw0LjUgMTFoNC43bC0xLjYgNy41TDE1LjUgOWgtNC43TDEyIDEuNXoiIGZpbGw9ImJsYWNrIi8+PC9zdmc+',
			58
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'Dashboard', 'elementor-animatepro' ),
			__( 'Dashboard', 'elementor-animatepro' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'render_dashboard_page' )
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'Widgets', 'elementor-animatepro' ),
			__( 'Widgets', 'elementor-animatepro' ),
			'manage_options',
			self::WIDGETS_SLUG,
			array( $this, 'render_widgets_page' )
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'Extensions', 'elementor-animatepro' ),
			__( 'Extensions', 'elementor-animatepro' ),
			'manage_options',
			self::EXTENSIONS_SLUG,
			array( $this, 'render_extensions_page' )
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'Theme Builder', 'elementor-animatepro' ),
			__( 'Theme Builder', 'elementor-animatepro' ),
			'manage_options',
			self::THEME_BUILDER_SLUG,
			array( $this, 'render_theme_builder_page' )
		);
	}

	/**
	 * Enqueue admin assets on AnimatePro pages.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		$allowed_pages = array(
			self::MENU_SLUG,
			self::WIDGETS_SLUG,
			self::EXTENSIONS_SLUG,
			self::THEME_BUILDER_SLUG,
		);

		$current_page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		$screen       = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		$is_eap_page  = in_array( $current_page, $allowed_pages, true );

		if ( ! $is_eap_page && $screen && isset( $screen->base ) ) {
			$is_eap_page = false !== strpos( (string) $screen->base, self::MENU_SLUG );
		}

		if ( ! $is_eap_page && 'toplevel_page_' . self::MENU_SLUG !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'eap-admin-fonts',
			'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap',
			array(),
			EAP_VERSION
		);

		wp_enqueue_style(
			'eap-admin',
			EAP_URL . 'assets/css/admin.css',
			array( 'eap-admin-fonts' ),
			EAP_VERSION
		);

		wp_enqueue_script(
			'eap-admin',
			EAP_URL . 'assets/js/admin.js',
			array(),
			EAP_VERSION,
			true
		);
	}

	/**
	 * Add a Settings link on the plugins screen.
	 *
	 * @param array $links Existing action links.
	 * @return array
	 */
	public function add_plugin_action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( admin_url( 'admin.php?page=' . self::MENU_SLUG ) ),
			esc_html__( 'Settings', 'elementor-animatepro' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Hide third-party admin notices on AnimatePro screens for a clean, branded
	 * dashboard. Runs on `in_admin_header`, which fires before any of the notice
	 * hooks. Our own "settings saved" notice is printed directly in the page
	 * body (not via these hooks), so it is unaffected.
	 *
	 * @return void
	 */
	public function suppress_foreign_admin_notices() {
		if ( ! $this->is_eap_admin_screen() ) {
			return;
		}

		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
		remove_all_actions( 'user_admin_notices' );
		remove_all_actions( 'network_admin_notices' );
	}

	/**
	 * Whether the current admin screen is one of the AnimatePro pages.
	 *
	 * @return bool
	 */
	private function is_eap_admin_screen() {
		$allowed_pages = array(
			self::MENU_SLUG,
			self::WIDGETS_SLUG,
			self::EXTENSIONS_SLUG,
			self::THEME_BUILDER_SLUG,
		);

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current_page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

		if ( in_array( $current_page, $allowed_pages, true ) ) {
			return true;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		return $screen && isset( $screen->base ) && false !== strpos( (string) $screen->base, self::MENU_SLUG );
	}

	/**
	 * Render dashboard page.
	 *
	 * @return void
	 */
	public function render_dashboard_page() {
		$cards = $this->get_admin_cards();
		?>
		<div class="wrap eap-admin">
			<?php $this->render_topbar( self::MENU_SLUG ); ?>
			<?php $this->render_page_header( __( 'Dashboard', 'elementor-animatepro' ), __( 'A clean control center for rebuilding Elementor AnimatePro one module at a time.', 'elementor-animatepro' ) ); ?>
			<div class="eap-admin-grid eap-admin-grid--cards">
				<?php foreach ( $cards as $card ) : ?>
					<a class="eap-admin-card" href="<?php echo esc_url( $card['url'] ); ?>">
						<span class="eap-admin-card__eyebrow"><?php echo esc_html( $card['eyebrow'] ); ?></span>
						<h2><?php echo esc_html( $card['title'] ); ?></h2>
						<p><?php echo esc_html( $card['description'] ); ?></p>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render widgets page.
	 *
	 * @return void
	 */
	public function render_widgets_page() {
		$categories = $this->get_widget_categories();
		$states     = $this->get_widget_states();
		$total      = 0;
		foreach ( $categories as $category ) {
			$total += count( $category['widgets'] );
		}
		?>
		<div class="wrap eap-admin">
			<?php $this->render_topbar( self::WIDGETS_SLUG, __( 'Search for Widgets', 'elementor-animatepro' ) ); ?>
			<?php $this->render_update_notice(); ?>
			<form class="eap-widgets-shell" method="post" action="" data-eap-settings-form>
				<?php wp_nonce_field( 'eap_save_admin_settings', 'eap_admin_nonce' ); ?>
				<input type="hidden" name="eap_admin_action" value="save_widgets" />
				<div class="eap-widgets-head">
					<div>
						<h1><?php esc_html_e( 'Widgets', 'elementor-animatepro' ); ?></h1>
						<p><?php echo esc_html( sprintf( __( '%d Total Widgets', 'elementor-animatepro' ), $total ) ); ?></p>
					</div>
					<div class="eap-widgets-actions">
						<button type="button" class="eap-btn eap-btn--ghost eap-bulk-toggle" data-eap-scope="all" data-eap-toggle="enable"><?php esc_html_e( 'Enable All', 'elementor-animatepro' ); ?></button>
						<button type="button" class="eap-btn eap-btn--ghost eap-bulk-toggle" data-eap-scope="all" data-eap-toggle="disable"><?php esc_html_e( 'Disable All', 'elementor-animatepro' ); ?></button>
						<label class="eap-autosave">
							<input type="checkbox" data-eap-autosave />
							<span><?php esc_html_e( 'Auto Save', 'elementor-animatepro' ); ?></span>
						</label>
						<button type="submit" class="eap-btn eap-btn--primary"><?php esc_html_e( 'Save Changes', 'elementor-animatepro' ); ?></button>
					</div>
				</div>

				<div class="eap-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Widget Categories', 'elementor-animatepro' ); ?>">
					<button type="button" class="eap-tab is-active" data-eap-tab="all"><?php esc_html_e( 'All', 'elementor-animatepro' ); ?></button>
					<?php foreach ( $categories as $slug => $category ) : ?>
						<button type="button" class="eap-tab" data-eap-tab="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $category['tab'] ); ?></button>
					<?php endforeach; ?>
				</div>

				<div class="eap-widget-groups">
					<?php foreach ( $categories as $slug => $category ) : ?>
						<section class="eap-widget-group" data-eap-panel="<?php echo esc_attr( $slug ); ?>">
							<div class="eap-widget-group__header">
								<h2><?php echo esc_html( $category['title'] ); ?></h2>
								<button type="button" class="eap-inline-toggle eap-group-toggle" data-eap-scope="<?php echo esc_attr( $slug ); ?>" data-eap-state="enabled">
									<span class="eap-inline-toggle__switch" aria-hidden="true"></span>
									<span class="eap-inline-toggle__label"><?php esc_html_e( 'Disable All', 'elementor-animatepro' ); ?></span>
								</button>
							</div>
							<div class="eap-widget-grid">
								<?php foreach ( $category['widgets'] as $widget ) : ?>
									<?php
									$widget_key = $this->get_widget_key( $widget );
									$is_built   = $this->is_built_widget( $widget );
									?>
									<label class="eap-widget-card<?php echo $is_built ? '' : ' is-coming-soon'; ?>" data-eap-widget-card data-eap-widget-name="<?php echo esc_attr( strtolower( $widget ) ); ?>" data-eap-widget-group="<?php echo esc_attr( $slug ); ?>" data-eap-available="<?php echo $is_built ? 'yes' : 'no'; ?>">
										<span class="eap-widget-card__icon dashicons <?php echo esc_attr( $this->get_widget_icon_class( $widget ) ); ?>" aria-hidden="true"></span>
										<span class="eap-widget-card__content">
											<strong><span><?php echo esc_html( $widget ); ?></span><?php if ( ! $is_built ) : ?><span class="eap-widget-card__tag"><?php esc_html_e( 'Coming Soon', 'elementor-animatepro' ); ?></span><?php endif; ?></strong>
											<small><?php esc_html_e( 'Documentation • Preview', 'elementor-animatepro' ); ?></small>
										</span>
										<span class="eap-switch">
											<input type="checkbox" name="<?php echo esc_attr( self::WIDGETS_OPTION . '[' . $widget_key . ']' ); ?>" value="1" data-eap-toggle-input data-eap-group="<?php echo esc_attr( $slug ); ?>" <?php checked( $is_built && ! empty( $states[ $widget_key ] ) ); ?> <?php disabled( ! $is_built ); ?> />
											<span class="eap-switch__slider" aria-hidden="true"></span>
										</span>
									</label>
								<?php endforeach; ?>
							</div>
						</section>
					<?php endforeach; ?>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Render extensions page.
	 *
	 * @return void
	 */
	public function render_extensions_page() {
		$extensions = $this->get_extensions();
		$states     = $this->get_extension_states();
		?>
		<div class="wrap eap-admin">
			<?php $this->render_topbar( self::EXTENSIONS_SLUG, __( 'Search for Extensions', 'elementor-animatepro' ) ); ?>
			<?php $this->render_update_notice(); ?>
			<form class="eap-widgets-shell" method="post" action="" data-eap-settings-form>
				<?php wp_nonce_field( 'eap_save_admin_settings', 'eap_admin_nonce' ); ?>
				<input type="hidden" name="eap_admin_action" value="save_extensions" />
				<div class="eap-widgets-head">
					<div>
						<h1><?php esc_html_e( 'Extensions', 'elementor-animatepro' ); ?></h1>
						<p><?php echo esc_html( sprintf( __( '%d Total Extensions', 'elementor-animatepro' ), count( $extensions ) ) ); ?></p>
					</div>
					<div class="eap-widgets-actions">
						<button type="button" class="eap-btn eap-btn--ghost eap-bulk-toggle" data-eap-scope="all" data-eap-toggle="enable"><?php esc_html_e( 'Enable All', 'elementor-animatepro' ); ?></button>
						<button type="button" class="eap-btn eap-btn--ghost eap-bulk-toggle" data-eap-scope="all" data-eap-toggle="disable"><?php esc_html_e( 'Disable All', 'elementor-animatepro' ); ?></button>
						<label class="eap-autosave">
							<input type="checkbox" data-eap-autosave />
							<span><?php esc_html_e( 'Auto Save', 'elementor-animatepro' ); ?></span>
						</label>
						<button type="submit" class="eap-btn eap-btn--primary"><?php esc_html_e( 'Save Changes', 'elementor-animatepro' ); ?></button>
					</div>
				</div>
				<div class="eap-widget-groups">
					<section class="eap-widget-group" data-eap-panel="extensions">
						<div class="eap-widget-group__header">
							<h2><?php esc_html_e( 'Extensions', 'elementor-animatepro' ); ?></h2>
							<button type="button" class="eap-inline-toggle eap-group-toggle" data-eap-scope="extensions" data-eap-state="enabled">
								<span class="eap-inline-toggle__switch" aria-hidden="true"></span>
								<span class="eap-inline-toggle__label"><?php esc_html_e( 'Disable All', 'elementor-animatepro' ); ?></span>
							</button>
						</div>
						<div class="eap-widget-grid">
							<?php foreach ( $extensions as $extension ) : ?>
								<label class="eap-widget-card" data-eap-widget-card data-eap-widget-name="<?php echo esc_attr( strtolower( $extension['label'] ) ); ?>" data-eap-widget-group="extensions">
									<span class="eap-widget-card__icon dashicons <?php echo esc_attr( $extension['icon'] ); ?>" aria-hidden="true"></span>
									<span class="eap-widget-card__content">
										<strong><?php echo esc_html( $extension['label'] ); ?></strong>
										<small><?php echo esc_html( $extension['description'] ); ?></small>
									</span>
									<span class="eap-switch">
										<input type="checkbox" name="<?php echo esc_attr( self::EXTENSIONS_OPTION . '[' . $extension['key'] . ']' ); ?>" value="1" data-eap-toggle-input data-eap-group="extensions" <?php checked( ! empty( $states[ $extension['key'] ] ) ); ?> />
										<span class="eap-switch__slider" aria-hidden="true"></span>
									</span>
								</label>
							<?php endforeach; ?>
						</div>
					</section>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Render theme builder page.
	 *
	 * @return void
	 */
	public function render_theme_builder_page() {
		?>
		<div class="wrap eap-admin">
			<?php $this->render_topbar( self::THEME_BUILDER_SLUG ); ?>
			<?php $this->render_page_header( __( 'Theme Builder', 'elementor-animatepro' ), __( 'This page will manage headers, footers, display conditions, and template assignment.', 'elementor-animatepro' ) ); ?>
			<div class="eap-admin-panel">
				<h3><?php esc_html_e( 'Theme Builder', 'elementor-animatepro' ); ?></h3>
				<p><?php esc_html_e( 'This section will be designed next using the same control-shell and AnimatePro admin system.', 'elementor-animatepro' ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render top navigation bar.
	 *
	 * @param string $current_slug Current page slug.
	 * @param string $search_placeholder Search placeholder.
	 * @return void
	 */
	private function render_topbar( $current_slug, $search_placeholder = '' ) {
		$items = array(
			self::MENU_SLUG          => __( 'Dashboard', 'elementor-animatepro' ),
			self::WIDGETS_SLUG       => __( 'Widgets', 'elementor-animatepro' ),
			self::EXTENSIONS_SLUG    => __( 'Extensions', 'elementor-animatepro' ),
			self::THEME_BUILDER_SLUG => __( 'Theme Builder', 'elementor-animatepro' ),
		);
		?>
		<?php
		/*
		 * WordPress relocates every admin notice (incl. other plugins') to just
		 * after `.wp-header-end`, or — when that marker is missing — after the
		 * first h1/h2 inside `.wrap`, which dropped foreign notices into the
		 * middle of our page. This invisible anchor, rendered first inside every
		 * AnimatePro page, keeps notices pinned to the very top instead.
		 */
		?>
		<hr class="wp-header-end" />
		<div class="eap-topbar">
			<div class="eap-topbar__brand">
				<span class="eap-topbar__logo">AP</span>
			</div>
			<nav class="eap-topbar__nav" aria-label="<?php esc_attr_e( 'AnimatePro Admin Navigation', 'elementor-animatepro' ); ?>">
				<?php foreach ( $items as $slug => $label ) : ?>
					<a class="eap-topbar__link<?php echo $current_slug === $slug ? ' is-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $slug ) ); ?>">
						<?php echo esc_html( $label ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
			<?php if ( ! empty( $search_placeholder ) ) : ?>
				<div class="eap-topbar__searchwrap">
					<button type="button" class="eap-topbar__search" data-eap-search-toggle aria-label="<?php esc_attr_e( 'Open search', 'elementor-animatepro' ); ?>" aria-expanded="false">
						<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
							<path d="M10.5 4a6.5 6.5 0 1 0 4.03 11.6l4.43 4.42 1.06-1.06-4.42-4.43A6.5 6.5 0 0 0 10.5 4Zm0 1.5a5 5 0 1 1 0 10 5 5 0 0 1 0-10Z"></path>
						</svg>
					</button>
					<label class="eap-topbar__searchpanel" data-eap-search-panel>
						<span class="screen-reader-text"><?php echo esc_html( $search_placeholder ); ?></span>
						<input type="search" id="eap-global-search" data-eap-global-search placeholder="<?php echo esc_attr( $search_placeholder ); ?>" />
					</label>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render shared page header.
	 *
	 * @param string $title Title.
	 * @param string $description Description.
	 * @return void
	 */
	private function render_page_header( $title, $description ) {
		?>
		<div class="eap-admin-hero">
			<div>
				<span class="eap-admin-hero__eyebrow"><?php esc_html_e( 'Elementor AnimatePro', 'elementor-animatepro' ); ?></span>
				<h1><?php echo esc_html( $title ); ?></h1>
				<p><?php echo esc_html( $description ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a simple update notice after save.
	 *
	 * @return void
	 */
	private function render_update_notice() {
		$updated = isset( $_GET['eap-updated'] ) ? sanitize_text_field( wp_unslash( $_GET['eap-updated'] ) ) : '';
		if ( '1' !== $updated ) {
			return;
		}
		?>
		<div class="eap-admin-notice" data-eap-admin-notice>
			<span class="eap-admin-notice__icon" aria-hidden="true">✓</span>
			<div class="eap-admin-notice__content">
				<strong><?php esc_html_e( 'Settings saved', 'elementor-animatepro' ); ?></strong>
				<p><?php esc_html_e( 'Your module settings have been updated successfully.', 'elementor-animatepro' ); ?></p>
			</div>
			<button type="button" class="eap-admin-notice__dismiss" data-eap-notice-dismiss aria-label="<?php esc_attr_e( 'Dismiss notice', 'elementor-animatepro' ); ?>">×</button>
		</div>
		<?php
	}

	/**
	 * Get dashboard cards.
	 *
	 * @return array<int, array<string, string>>
	 */
	private function get_admin_cards() {
		return array(
			array(
				'eyebrow'     => __( 'Modules', 'elementor-animatepro' ),
				'title'       => __( 'Widgets', 'elementor-animatepro' ),
				'description' => __( 'Browse widget groups, categories, and future activation controls.', 'elementor-animatepro' ),
				'url'         => admin_url( 'admin.php?page=' . self::WIDGETS_SLUG ),
			),
			array(
				'eyebrow'     => __( 'Effects', 'elementor-animatepro' ),
				'title'       => __( 'Extensions', 'elementor-animatepro' ),
				'description' => __( 'Configure animation systems, utility controls, and editor extensions.', 'elementor-animatepro' ),
				'url'         => admin_url( 'admin.php?page=' . self::EXTENSIONS_SLUG ),
			),
			array(
				'eyebrow'     => __( 'Templates', 'elementor-animatepro' ),
				'title'       => __( 'Theme Builder', 'elementor-animatepro' ),
				'description' => __( 'Manage header, footer, and display-assignment templates from one place.', 'elementor-animatepro' ),
				'url'         => admin_url( 'admin.php?page=' . self::THEME_BUILDER_SLUG ),
			),
		);
	}

	/**
	 * Get widget category data.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function get_widget_categories() {
		return array(
			'general'       => array(
				'tab'     => __( 'General', 'elementor-animatepro' ),
				'title'   => __( 'General Widgets', 'elementor-animatepro' ),
				'widgets' => array(
					'Image Box',
					'Image Box Slider',
					'Image Hotspot',
					'Social Icons',
					'Image',
					'Image Gallery',
					'Text Hover Image',
					'Brand Slider',
					'Icon Box',
					'Animated Text',
					'Advanced Animated Text',
					'Testimonial Box',
					'Testimonial Slider',
					'Advanced Testimonial Slider',
					'Advanced Slider',
					'Advanced Button',
					'Image Comparison',
					'Parallax Sections',
					'Progress Bar',
					'Team',
					'One Page Nav',
					'Timeline',
					'Services Tabs',
					'Countdown',
					'Image Accordion',
					'Content Toggle',
					'Multi Buttons',
					'Price Box',
					'Data Table',
					'Feature List',
					'Sticky Video',
					'Stacked Cards',
					'Social Share',
				),
			),
			'header-footer' => array(
				'tab'     => __( 'Header & Footer', 'elementor-animatepro' ),
				'title'   => __( 'Header & Footer Widgets', 'elementor-animatepro' ),
				'widgets' => array(
					'Animated Off-Canvas',
					'Site Logo',
					'Nav Menu',
					'Mega Menu',
				),
			),
			'slider'        => array(
				'tab'     => __( 'Slider', 'elementor-animatepro' ),
				'title'   => __( 'Slider', 'elementor-animatepro' ),
				'widgets' => array(
					'Posts Slider',
					'Breaking News Slider',
					'Category Slider',
					'Video Box Slider',
					'Filterable Slider',
				),
			),
			'dynamic'       => array(
				'tab'     => __( 'Dynamic', 'elementor-animatepro' ),
				'title'   => __( 'Dynamic Widgets', 'elementor-animatepro' ),
				'widgets' => array(
					'Post Title',
					'Post Featured Image',
					'Post Excerpt',
					'Post Content',
					'Post Comments',
					'Post Reactions',
					'Post Meta Info',
					'Post Pagination',
					'Social Share',
					'Posts',
					'Advanced Posts',
					'Posts Timeline',
					'Posts Read Later',
					'Video Story',
					'Filterable Posts',
					'Post Rating Form',
					'Post Rating',
					'Category Showcase',
					'Current Date',
					'Featured Posts',
					'Archive Title',
					'Portfolio',
				),
			),
			'advanced'      => array(
				'tab'     => __( 'Advanced', 'elementor-animatepro' ),
				'title'   => __( 'Advanced Widgets', 'elementor-animatepro' ),
				'widgets' => array(
					'Loop Grid',
					'Loop Carousel',
					'Toggle Switch',
					'Advanced Pricing Table',
					'Scroll Elements',
					'Advanced Portfolio',
					'Filterable Gallery',
					'Breadcrumbs',
					'Table Of Content',
					'Author Box',
					'Flip Box',
					'Advanced Accordion',
					'Nested Slider',
					'Stacked Cards',
					'Scrollmotion Cards',
					'Nested Motion Card',
					'Vertical Marquee',
				),
			),
		);
	}

	/**
	 * Sanitize widget option values.
	 *
	 * @param mixed $input Submitted input.
	 * @return array<string, int>
	 */
	public function sanitize_widget_states( $input ) {
		$defaults = $this->get_default_widget_states();
		$clean    = array();

		foreach ( $defaults as $key => $value ) {
			$clean[ $key ] = isset( $input[ $key ] ) ? 1 : 0;

			if ( ! $this->is_built_widget_key( $key ) ) {
				$clean[ $key ] = 0;
			}
		}

		return $clean;
	}

	/**
	 * Sanitize extension option values.
	 *
	 * @param mixed $input Submitted input.
	 * @return array<string, int>
	 */
	public function sanitize_extension_states( $input ) {
		$defaults = $this->get_default_extension_states();
		$clean    = array();

		foreach ( $defaults as $key => $value ) {
			$clean[ $key ] = isset( $input[ $key ] ) ? 1 : 0;
		}

		return $clean;
	}

	/**
	 * Get stored widget states.
	 *
	 * @return array<string, int>
	 */
	private function get_widget_states() {
		return wp_parse_args( get_option( self::WIDGETS_OPTION, array() ), $this->get_default_widget_states() );
	}

	/**
	 * Get stored extension states.
	 *
	 * @return array<string, int>
	 */
	private function get_extension_states() {
		return wp_parse_args( get_option( self::EXTENSIONS_OPTION, array() ), $this->get_default_extension_states() );
	}

	/**
	 * Get default widget states.
	 *
	 * @return array<string, int>
	 */
	private function get_default_widget_states() {
		$defaults = array();

		foreach ( $this->get_widget_categories() as $category ) {
			foreach ( $category['widgets'] as $widget ) {
				$key              = $this->get_widget_key( $widget );
				$defaults[ $key ] = $this->is_built_widget( $widget ) ? 1 : 0;
			}
		}

		return $defaults;
	}

	/**
	 * Determine whether a widget is currently implemented.
	 *
	 * @param string $widget Widget label.
	 * @return bool
	 */
	private function is_built_widget( $widget ) {
		return $this->is_built_widget_key( $this->get_widget_key( $widget ) );
	}

	/**
	 * Determine whether a widget key is currently implemented.
	 *
	 * @param string $widget_key Widget key.
	 * @return bool
	 */
	private function is_built_widget_key( $widget_key ) {
		$built = array(
			'image-box',
			'image-box-slider',
			'image-hotspot',
			'social-icons',
			'image',
			'image-gallery',
			'image-comparison',
			'parallax-sections',
			'text-hover-image',
			'brand-slider',
			'icon-box',
			'testimonial-box',
			'testimonial-slider',
			'progress-bar',
			'team',
			'advanced-button',
			'animated-text',
			'advanced-animated-text',
			'timeline',
			'services-tabs',
			'one-page-nav',
			'advanced-testimonial-slider',
			'advanced-slider',
			'countdown',
			'image-accordion',
			'content-toggle',
			'multi-buttons',
			'price-box',
			'data-table',
			'feature-list',
			'sticky-video',
			'stacked-cards',
			'social-share',
			'site-logo',
			'nav-menu',
			'mega-menu',
			'animated-off-canvas',
			'post-title',
			'post-featured-image',
			'post-excerpt',
			'post-content',
			'post-meta-info',
			'post-comments',
			'post-reactions',
			'post-pagination',
			'posts',
			'post-rating',
			'post-rating-form',
			'advanced-posts',
			'filterable-posts',
			'featured-posts',
			'archive-title',
			'current-date',
			'posts-timeline',
			'posts-read-later',
			'video-story',
			'posts-slider',
			'breaking-news-slider',
			'category-slider',
			'category-showcase',
			'video-box-slider',
			'filterable-slider',
			'loop-grid',
			'loop-carousel',
		);

		return in_array( $widget_key, $built, true );
	}

	/**
	 * Get default extension states.
	 *
	 * @return array<string, int>
	 */
	private function get_default_extension_states() {
		$defaults = array();

		foreach ( $this->get_extensions() as $extension ) {
			$defaults[ $extension['key'] ] = 1;
		}

		return $defaults;
	}

	/**
	 * Get widget key.
	 *
	 * @param string $widget Widget label.
	 * @return string
	 */
	private function get_widget_key( $widget ) {
		return sanitize_title( $widget );
	}

	/**
	 * Get icon class for a widget label.
	 *
	 * @param string $widget Widget label.
	 * @return string
	 */
	private function get_widget_icon_class( $widget ) {
		$widget_name = strtolower( $widget );

		$map = array(
			'image'        => 'dashicons-format-image',
			'gallery'      => 'dashicons-format-gallery',
			'slider'       => 'dashicons-images-alt2',
			'video'        => 'dashicons-video-alt3',
			'button'       => 'dashicons-button',
			'progress'     => 'dashicons-chart-bar',
			'testimonial'  => 'dashicons-format-quote',
			'tabs'         => 'dashicons-index-card',
			'timeline'     => 'dashicons-backup',
			'menu'         => 'dashicons-menu',
			'logo'         => 'dashicons-admin-site-alt3',
			'portfolio'    => 'dashicons-portfolio',
			'parallax'     => 'dashicons-align-wide',
			'search'       => 'dashicons-search',
			'form'         => 'dashicons-feedback',
			'post'         => 'dashicons-media-document',
			'share'        => 'dashicons-share',
			'accordion'    => 'dashicons-editor-ol',
			'team'         => 'dashicons-groups',
			'notification' => 'dashicons-bell',
			'lottie'       => 'dashicons-format-status',
			'mailchimp'    => 'dashicons-email',
			'breadcrumbs'  => 'dashicons-arrow-right-alt2',
			'countdown'    => 'dashicons-clock',
			'author'       => 'dashicons-admin-users',
			'price'        => 'dashicons-money-alt',
			'pricing'      => 'dashicons-money-alt',
			'table'        => 'dashicons-editor-table',
			'feature'      => 'dashicons-list-view',
			'text'         => 'dashicons-editor-textcolor',
			'icon'         => 'dashicons-star-filled',
			'brand'        => 'dashicons-megaphone',
		);

		foreach ( $map as $needle => $icon ) {
			if ( false !== strpos( $widget_name, $needle ) ) {
				return $icon;
			}
		}

		return 'dashicons-screenoptions';
	}

	/**
	 * Get extensions data.
	 *
	 * @return array<int, array<string, string>>
	 */
	private function get_extensions() {
		return array(
			array(
				'key'         => 'motion-effects',
				'label'       => __( 'Motion Effects', 'elementor-animatepro' ),
				'description' => __( 'Entrance, scroll, and interactive motion controls.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-controls-repeat',
			),
			array(
				'key'         => 'wrapper-link',
				'label'       => __( 'Wrapper Link', 'elementor-animatepro' ),
				'description' => __( 'Turn full containers or cards into clickable wrappers.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-admin-links',
			),
			array(
				'key'         => 'custom-cursor',
				'label'       => __( 'Custom Cursor', 'elementor-animatepro' ),
				'description' => __( 'Replace the pointer over any element — circle, icon, image or SVG, with a separate hover state and eight trail effects.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-editor-customchar',
			),
			array(
				'key'         => 'pin-elements',
				'label'       => __( 'Pin Elements', 'elementor-animatepro' ),
				'description' => __( 'Pin sections or inner elements during scroll interaction.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-admin-post',
			),
			array(
				'key'         => 'scroll-transforms',
				'label'       => __( 'Scroll Transforms', 'elementor-animatepro' ),
				'description' => __( 'Translate, rotate, scale, and fade elements on scroll.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-image-rotate',
			),
			array(
				'key'         => 'video-story-cpt',
				'label'       => __( 'Video Story Post Type', 'elementor-animatepro' ),
				'description' => __( 'Register a Video Story post type (title, video, poster) the Video Story widget can fetch.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-video-alt3',
			),
			array(
				'key'         => 'image-masking',
				'label'       => __( 'Image Masking', 'elementor-animatepro' ),
				'description' => __( 'Mask images with a clip-path shape or your own mask image, with a separate hover state.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-art',
			),
			array(
				'key'         => 'advanced-tooltip',
				'label'       => __( 'Advanced Tooltip', 'elementor-animatepro' ),
				'description' => __( 'Add a text, icon, image or shortcode tooltip to any widget or container.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-format-status',
			),
			array(
				'key'         => 'conditional-display',
				'label'       => __( 'Conditional Display', 'elementor-animatepro' ),
				'description' => __( 'Show or hide any element by user, role, page, date, device, browser, URL or country.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-visibility',
			),
			array(
				'key'         => 'interactive-animations',
				'label'       => __( 'Interactive Animations', 'elementor-animatepro' ),
				'description' => __( 'Animate any element on scroll, hover, click or with the mouse.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-image-filter',
			),
			array(
				'key'         => 'hover-interaction',
				'label'       => __( 'Hover Interaction', 'elementor-animatepro' ),
				'description' => __( 'Give any element a hover state: opacity, filters, offset, 3D transform and cursor tilt.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-image-flip-horizontal',
			),
		);
	}
}
