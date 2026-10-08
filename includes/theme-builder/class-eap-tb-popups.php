<?php
/**
 * Popup rendering.
 *
 * Unlike a header or a body, several popups can apply to one page, so every
 * matching template is rendered — each one inert in the markup until its
 * trigger fires. The behaviour comes from the document's own settings rather
 * than a separate admin screen, because whoever designs a popup is the person
 * deciding when it opens.
 *
 * Markup goes out on `wp_footer` so a popup never lands inside the theme's
 * layout, and the content is built during `wp_enqueue_scripts` so its widgets'
 * stylesheets still reach the head.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_TB_Popups {

	/**
	 * Matching popup IDs for this request.
	 *
	 * @var int[]
	 */
	private $ids = array();

	/**
	 * Pre-rendered HTML per popup.
	 *
	 * @var array<int, string>
	 */
	private $html = array();

	/**
	 * Hook into WordPress.
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'setup' ), 25 );
	}

	/**
	 * Find the popups for this request and book the work.
	 *
	 * @return void
	 */
	public function setup() {
		if ( ! $this->should_run() ) {
			return;
		}

		$this->ids = EAP_TB_Resolver::get_all_matching( 'popup' );

		if ( empty( $this->ids ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'prepare' ), 5 );
		add_action( 'wp_footer', array( $this, 'render' ), 20 );
	}

	/**
	 * Whether popups belong on this request at all.
	 *
	 * @return bool
	 */
	private function should_run() {
		if ( is_admin() || is_feed() || is_embed() || is_robots() || is_trackback() ) {
			return false;
		}

		if ( wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return false;
		}

		if ( is_singular( EAP_TB_Post_Type::POST_TYPE ) ) {
			return false;
		}

		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->preview ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return false;
		}

		return true;
	}

	/**
	 * Build each popup's HTML and load the assets.
	 *
	 * @return void
	 */
	public function prepare() {
		wp_enqueue_style( 'eap-theme-builder-popup' );
		wp_enqueue_script( 'eap-theme-builder-popup' );

		foreach ( $this->ids as $id ) {
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				\Elementor\Core\Files\CSS\Post::create( $id )->enqueue();
			}

			if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->frontend ) ) {
				$this->html[ $id ] = (string) \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id, false );
			}
		}
	}

	/**
	 * A popup's behaviour, read from its document settings.
	 *
	 * @param int $id Popup template ID.
	 * @return array<string, mixed>
	 */
	private function get_settings( $id ) {
		$defaults = array(
			'trigger'       => 'delay',
			'delay'         => 3,
			'scroll'        => 40,
			'inactivity'    => 30,
			'selector'      => '',
			'frequency'     => 'session',
			'days'          => 7,
			'position'      => 'center',
			'width'         => 640,
			'animation'     => 'fade',
			'closeButton'   => true,
			'closeOverlay'  => true,
			'closeEsc'      => true,
			'autoClose'     => 0,
		);

		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return $defaults;
		}

		$document = \Elementor\Plugin::$instance->documents->get( $id );

		if ( ! $document ) {
			return $defaults;
		}

		$get = static function ( $key, $fallback ) use ( $document ) {
			$value = $document->get_settings( $key );

			return ( null === $value || '' === $value ) ? $fallback : $value;
		};

		$settings = array(
			'trigger'      => (string) $get( 'eap_popup_trigger', $defaults['trigger'] ),
			'delay'        => (float) $get( 'eap_popup_delay', $defaults['delay'] ),
			'scroll'       => (int) $get( 'eap_popup_scroll', $defaults['scroll'] ),
			'inactivity'   => (int) $get( 'eap_popup_inactivity', $defaults['inactivity'] ),
			'selector'     => (string) $get( 'eap_popup_click_selector', $defaults['selector'] ),
			'frequency'    => (string) $get( 'eap_popup_frequency', $defaults['frequency'] ),
			'days'         => (int) $get( 'eap_popup_days', $defaults['days'] ),
			'position'     => (string) $get( 'eap_popup_position', $defaults['position'] ),
			'width'        => (int) $get( 'eap_popup_width', $defaults['width'] ),
			'animation'    => (string) $get( 'eap_popup_animation', $defaults['animation'] ),
			'closeButton'  => 'yes' === $document->get_settings( 'eap_popup_close_button' ),
			'closeOverlay' => 'yes' === $document->get_settings( 'eap_popup_close_overlay' ),
			'closeEsc'     => 'yes' === $document->get_settings( 'eap_popup_close_esc' ),
			'autoClose'    => (float) $get( 'eap_popup_auto_close', $defaults['autoClose'] ),
		);

		// A popup with no way out would trap the visitor, including anyone on a
		// keyboard. Put the close button back rather than honour that.
		if ( ! $settings['closeButton'] && ! $settings['closeOverlay'] && ! $settings['closeEsc'] && $settings['autoClose'] <= 0 ) {
			$settings['closeButton'] = true;
		}

		// A click trigger with nothing to click can never fire.
		if ( 'click' === $settings['trigger'] && '' === trim( $settings['selector'] ) ) {
			$settings['trigger'] = 'delay';
		}

		return $settings;
	}

	/**
	 * Print every matching popup.
	 *
	 * @return void
	 */
	public function render() {
		foreach ( $this->ids as $id ) {
			$html = isset( $this->html[ $id ] ) ? $this->html[ $id ] : '';

			if ( '' === trim( $html ) ) {
				continue;
			}

			$settings = $this->get_settings( $id );
			$title    = get_the_title( $id );
			?>
			<div
				class="eap-popup eap-popup--<?php echo esc_attr( $settings['position'] ); ?> eap-popup--<?php echo esc_attr( $settings['animation'] ); ?>"
				data-eap-popup="<?php echo esc_attr( wp_json_encode( $settings ) ); ?>"
				data-eap-popup-id="<?php echo esc_attr( $id ); ?>"
				hidden
			>
				<div class="eap-popup__overlay" data-eap-popup-overlay></div>
				<div
					class="eap-popup__box"
					role="dialog"
					aria-modal="true"
					aria-label="<?php echo esc_attr( '' !== $title ? $title : __( 'Popup', 'elementor-animatepro' ) ); ?>"
					style="--eap-popup-width: <?php echo esc_attr( $settings['width'] ); ?>px;"
				>
					<?php if ( $settings['closeButton'] ) : ?>
						<button type="button" class="eap-popup__close" data-eap-popup-close aria-label="<?php esc_attr_e( 'Close', 'elementor-animatepro' ); ?>">
							<span aria-hidden="true">&times;</span>
						</button>
					<?php endif; ?>
					<div class="eap-popup__content">
						<?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor document output. ?>
					</div>
				</div>
			</div>
			<?php
		}
	}
}
