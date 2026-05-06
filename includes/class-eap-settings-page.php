<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_Settings_Page {

	/**
	 * Module manager.
	 *
	 * @var EAP_Module_Manager
	 */
	private $modules;

	/**
	 * Constructor.
	 *
	 * @param EAP_Module_Manager $modules Module manager.
	 */
	public function __construct( EAP_Module_Manager $modules ) {
		$this->modules = $modules;

		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register menu pages.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_menu_page(
			__( 'Elementor AnimatePro', 'elementor-animatepro' ),
			__( 'Elementor AnimatePro', 'elementor-animatepro' ),
			'manage_options',
			'elementor-animatepro',
			array( $this, 'render_dashboard_page' ),
			'dashicons-format-image',
			58
		);

		add_submenu_page(
			'elementor-animatepro',
			__( 'Dashboard', 'elementor-animatepro' ),
			__( 'Dashboard', 'elementor-animatepro' ),
			'manage_options',
			'elementor-animatepro',
			array( $this, 'render_dashboard_page' )
		);

		add_submenu_page(
			'elementor-animatepro',
			__( 'Widgets', 'elementor-animatepro' ),
			__( 'Widgets', 'elementor-animatepro' ),
			'manage_options',
			'elementor-animatepro-widgets',
			array( $this, 'render_widgets_page' )
		);

		add_submenu_page(
			'elementor-animatepro',
			__( 'Extensions', 'elementor-animatepro' ),
			__( 'Extensions', 'elementor-animatepro' ),
			'manage_options',
			'elementor-animatepro-extensions',
			array( $this, 'render_extensions_page' )
		);
	}

	/**
	 * Register module option.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'eap_settings',
			EAP_Module_Manager::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_modules' ),
				'default'           => array(),
			)
		);
	}

	/**
	 * Sanitize modules.
	 *
	 * @param mixed $input Submitted value.
	 * @return array<string, string>
	 */
	public function sanitize_modules( $input ) {
		$clean       = array();
		$definitions = $this->modules->get_definitions();

		foreach ( $definitions as $key => $definition ) {
			$clean[ $key ] = isset( $input[ $key ] ) ? '1' : '0';
		}

		return $clean;
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook_suffix Hook suffix.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		$is_eap = in_array(
			$hook_suffix,
			array(
				'toplevel_page_elementor-animatepro',
				'elementor-animatepro_page_elementor-animatepro-widgets',
				'elementor-animatepro_page_elementor-animatepro-extensions',
			),
			true
		);
		$is_template_screen = $screen && isset( $screen->post_type ) && 'eap_template' === $screen->post_type;

		if ( ! $is_eap && ! $is_template_screen ) {
			return;
		}

		wp_enqueue_style(
			'eap-admin-fonts',
			'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
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
	 * Render dashboard.
	 *
	 * @return void
	 */
	public function render_dashboard_page() {
		$definitions = $this->modules->get_definitions();
		$widgets     = $this->filter_definitions_by_type( $definitions, 'widget' );
		$extensions  = $this->filter_definitions_by_type( $definitions, 'extension' );
		?>
		<div class="wrap eap-admin">
			<?php $this->render_hero( __( 'Dashboard', 'elementor-animatepro' ), __( 'Control your widget library, motion extensions, and header/footer templates from a cleaner workspace.', 'elementor-animatepro' ) ); ?>
			<div class="eap-admin-kpis">
				<div class="eap-kpi-card"><strong><?php echo esc_html( count( $widgets ) ); ?></strong><span><?php esc_html_e( 'Widgets', 'elementor-animatepro' ); ?></span></div>
				<div class="eap-kpi-card"><strong><?php echo esc_html( count( $extensions ) ); ?></strong><span><?php esc_html_e( 'Extensions', 'elementor-animatepro' ); ?></span></div>
				<div class="eap-kpi-card"><strong><?php esc_html_e( 'Templates', 'elementor-animatepro' ); ?></strong><span><?php esc_html_e( 'Header and footer layouts with display conditions.', 'elementor-animatepro' ); ?></span></div>
			</div>
			<div class="eap-overview-grid">
				<a class="eap-overview-card" href="<?php echo esc_url( admin_url( 'admin.php?page=elementor-animatepro-widgets' ) ); ?>">
					<h2><?php esc_html_e( 'Widgets', 'elementor-animatepro' ); ?></h2>
					<p><?php esc_html_e( 'Enable, disable, and review every AnimatePro widget in one place.', 'elementor-animatepro' ); ?></p>
				</a>
				<a class="eap-overview-card" href="<?php echo esc_url( admin_url( 'admin.php?page=elementor-animatepro-extensions' ) ); ?>">
					<h2><?php esc_html_e( 'Extensions', 'elementor-animatepro' ); ?></h2>
					<p><?php esc_html_e( 'Manage effect controls for scroll, pinning, transforms, and cursor interactions.', 'elementor-animatepro' ); ?></p>
				</a>
				<a class="eap-overview-card" href="<?php echo esc_url( admin_url( 'edit.php?post_type=eap_template' ) ); ?>">
					<h2><?php esc_html_e( 'Header & Footer', 'elementor-animatepro' ); ?></h2>
					<p><?php esc_html_e( 'Build reusable theme parts and choose where they should appear.', 'elementor-animatepro' ); ?></p>
				</a>
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
		$definitions = $this->filter_definitions_by_type( $this->modules->get_definitions(), 'widget' );
		$this->render_modules_page(
			__( 'Widgets', 'elementor-animatepro' ),
			__( 'Turn individual widgets on or off and keep the editor focused on what you actually use.', 'elementor-animatepro' ),
			$definitions,
			'widget'
		);
	}

	/**
	 * Render extensions page.
	 *
	 * @return void
	 */
	public function render_extensions_page() {
		$definitions = $this->filter_definitions_by_type( $this->modules->get_definitions(), 'extension' );
		$this->render_modules_page(
			__( 'Extensions', 'elementor-animatepro' ),
			__( 'Manage advanced controls and behavioral add-ons separately from widgets.', 'elementor-animatepro' ),
			$definitions,
			'extension'
		);
	}

	/**
	 * Render modules page.
	 *
	 * @param string $title Page title.
	 * @param string $description Page description.
	 * @param array  $definitions Module definitions.
	 * @param string $type Group type.
	 * @return void
	 */
	private function render_modules_page( $title, $description, $definitions, $type ) {
		$enabled = $this->modules->get_enabled_modules();
		?>
		<div class="wrap eap-admin">
			<?php $this->render_hero( $title, $description ); ?>
			<form method="post" action="options.php">
				<?php settings_fields( 'eap_settings' ); ?>
				<div class="eap-admin-panel eap-admin-panel--wide">
					<div class="eap-panel-toolbar">
						<div>
							<h2><?php echo esc_html( $title ); ?></h2>
							<p><?php esc_html_e( 'Use bulk toggles or control modules one by one.', 'elementor-animatepro' ); ?></p>
						</div>
						<div class="eap-toolbar-actions">
							<button type="button" class="button eap-bulk-toggle" data-eap-toggle="enable" data-eap-group="<?php echo esc_attr( $type ); ?>"><?php esc_html_e( 'Enable All', 'elementor-animatepro' ); ?></button>
							<button type="button" class="button eap-bulk-toggle" data-eap-toggle="disable" data-eap-group="<?php echo esc_attr( $type ); ?>"><?php esc_html_e( 'Disable All', 'elementor-animatepro' ); ?></button>
						</div>
					</div>
					<div class="eap-module-grid eap-module-grid--dense">
						<?php foreach ( $definitions as $key => $definition ) : ?>
							<label class="eap-module-card eap-module-card--grid">
								<input type="checkbox" data-eap-group="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( EAP_Module_Manager::OPTION_KEY . '[' . $key . ']' ); ?>" value="1" <?php checked( isset( $enabled[ $key ] ) ? $enabled[ $key ] : '0', '1' ); ?> />
								<span class="eap-module-card__content">
									<strong><?php echo esc_html( $definition['label'] ); ?></strong>
									<small><?php echo esc_html( $definition['desc'] ); ?></small>
								</span>
							</label>
						<?php endforeach; ?>
					</div>
				</div>
				<?php submit_button( __( 'Save Changes', 'elementor-animatepro' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Filter definitions by type.
	 *
	 * @param array  $definitions Definitions.
	 * @param string $type Type.
	 * @return array
	 */
	private function filter_definitions_by_type( $definitions, $type ) {
		return array_filter(
			$definitions,
			static function( $item ) use ( $type ) {
				return $type === $item['type'];
			}
		);
	}

	/**
	 * Render hero block.
	 *
	 * @param string $title Title.
	 * @param string $description Description.
	 * @return void
	 */
	private function render_hero( $title, $description ) {
		?>
		<div class="eap-admin-hero">
			<div>
				<h1><?php echo esc_html( $title ); ?></h1>
				<p><?php echo esc_html( $description ); ?></p>
			</div>
			<div class="eap-admin-pill"><?php esc_html_e( 'Aurora Slate UI', 'elementor-animatepro' ); ?></div>
		</div>
		<?php
	}
}
