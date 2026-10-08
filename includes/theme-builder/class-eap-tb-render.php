<?php
/**
 * Theme Builder front-end rendering.
 *
 * Replacing a theme's header means getting in front of header.php without
 * leaving the theme's own markup behind. The trick, on a classic theme, is to
 * act on `get_header`: print our document head and header template, then
 * require the theme's header.php inside a discarded buffer. Because both that
 * call and core's own use require_once, core's load right after the action is a
 * no-op and the theme's header never reaches the page. Footers work the same
 * way through `get_footer`.
 *
 * Block themes call neither hook, so there the header and footer template-part
 * blocks are swapped as they render.
 *
 * Template HTML is built during wp_enqueue_scripts rather than at output time,
 * so the stylesheets its widgets ask for are still early enough for the head.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_TB_Render {

	/**
	 * Instance, so the template files can reach the rendered parts.
	 *
	 * @var EAP_TB_Render|null
	 */
	private static $instance = null;

	/**
	 * Winning template ID per part.
	 *
	 * @var array<string, int>
	 */
	private $parts = array(
		'header' => 0,
		'footer' => 0,
		'body'   => 0,
	);

	/**
	 * Which template type is supplying the body, if any.
	 *
	 * @var string
	 */
	private $body_type = '';

	/**
	 * Pre-rendered HTML per part.
	 *
	 * @var array<string, string>
	 */
	private $html = array();

	/**
	 * Parts already printed, so a stray second call cannot duplicate one.
	 *
	 * @var array<string, bool>
	 */
	private $printed = array();

	/**
	 * Hook into WordPress.
	 */
	public function __construct() {
		self::$instance = $this;

		add_action( 'wp', array( $this, 'setup' ), 20 );
	}

	/**
	 * Decide what this request needs and attach the right adapter.
	 *
	 * @return void
	 */
	public function setup() {
		if ( ! $this->should_run() ) {
			return;
		}

		foreach ( array( 'header', 'footer' ) as $part ) {
			if ( $this->is_taken_by_other_plugin( $part ) ) {
				continue;
			}

			$this->parts[ $part ] = EAP_TB_Resolver::get_template_id( $part );
		}

		// The body is whichever of Single or Archive fits this request; only
		// one of them ever can.
		$this->body_type = $this->get_body_type();

		if ( '' !== $this->body_type ) {
			$this->parts['body'] = EAP_TB_Resolver::get_template_id( $this->body_type );
		}

		if ( ! array_filter( $this->parts ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'prepare' ), 5 );
		add_filter( 'body_class', array( $this, 'body_class' ) );

		if ( $this->parts['body'] ) {
			add_filter( 'template_include', array( $this, 'body_template' ), 999 );
		}

		if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			add_filter( 'render_block_core/template-part', array( $this, 'swap_block_part' ), 10, 2 );
			return;
		}

		if ( $this->parts['header'] ) {
			add_action( 'get_header', array( $this, 'override_header' ) );
		}

		if ( $this->parts['footer'] ) {
			add_action( 'get_footer', array( $this, 'override_footer' ) );
		}
	}

	/**
	 * Which template type may supply the body on this request.
	 *
	 * @return string Type slug, or '' when the body is the theme's business.
	 */
	private function get_body_type() {
		// Order matters: a 404 and a search are neither singular nor archives
		// as far as WordPress is concerned, and a search that found nothing is
		// still a search rather than a 404.
		if ( is_404() ) {
			return '404';
		}

		if ( is_search() ) {
			return 'search';
		}

		if ( is_singular() ) {
			return 'single';
		}

		// is_home() is the blog page, where is_archive() is false but a listing
		// is exactly what is being shown.
		if ( is_archive() || is_home() ) {
			return 'archive';
		}

		return '';
	}

	/**
	 * Hand WordPress our own page template when a body template matched.
	 *
	 * @param string $template Template path.
	 * @return string
	 */
	public function body_template( $template ) {
		$ours = EAP_PATH . 'includes/theme-builder/templates/body.php';

		return file_exists( $ours ) ? $ours : $template;
	}

	/**
	 * Print the body template, with the loop set up for single content.
	 *
	 * A Single template reads the current post through the usual loop globals,
	 * so the post has to be set up first. An Archive template must NOT consume
	 * the loop — its listing widget runs the main query itself.
	 *
	 * @return void
	 */
	public static function the_body() {
		if ( ! self::$instance ) {
			return;
		}

		self::$instance->print_body();
	}

	/**
	 * @return void
	 */
	private function print_body() {
		if ( 'single' === $this->body_type && have_posts() ) {
			the_post();
		}

		$this->print_part( 'body' );
	}

	/**
	 * Whether the Theme Builder should touch this request at all.
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

		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return false;
		}

		// Editing or previewing a template: it must appear on its own.
		if ( is_singular( EAP_TB_Post_Type::POST_TYPE ) ) {
			return false;
		}

		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->preview ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return false;
		}

		return true;
	}

	/**
	 * Whether another header/footer plugin is already replacing this part.
	 *
	 * Two plugins both replacing header.php would each print a document head,
	 * so we stand down and say so on the Theme Builder screen instead.
	 *
	 * @param string $part Part key.
	 * @return bool
	 */
	private function is_taken_by_other_plugin( $part ) {
		if ( 'header' === $part && function_exists( 'hfe_header_enabled' ) ) {
			return (bool) hfe_header_enabled();
		}

		if ( 'footer' === $part && function_exists( 'hfe_footer_enabled' ) ) {
			return (bool) hfe_footer_enabled();
		}

		return false;
	}

	/**
	 * Build each part's HTML while stylesheets can still reach the head.
	 *
	 * @return void
	 */
	public function prepare() {
		foreach ( $this->parts as $part => $template_id ) {
			if ( ! $template_id ) {
				continue;
			}

			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				\Elementor\Core\Files\CSS\Post::create( $template_id )->enqueue();
			}

			$this->html[ $part ] = $this->build_html( $template_id );
		}
	}

	/**
	 * Render one template document.
	 *
	 * @param int $template_id Template ID.
	 * @return string
	 */
	private function build_html( $template_id ) {
		if ( ! class_exists( '\Elementor\Plugin' ) || ! isset( \Elementor\Plugin::$instance->frontend ) ) {
			return '';
		}

		return (string) \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( (int) $template_id, false );
	}

	/**
	 * Print a part. Called from the replacement template files.
	 *
	 * @param string $part Part key.
	 * @return void
	 */
	public static function the_part( $part ) {
		if ( ! self::$instance ) {
			return;
		}

		self::$instance->print_part( $part );
	}

	/**
	 * Print a part once.
	 *
	 * @param string $part Part key.
	 * @return void
	 */
	private function print_part( $part ) {
		if ( ! empty( $this->printed[ $part ] ) || empty( $this->parts[ $part ] ) ) {
			return;
		}

		$this->printed[ $part ] = true;

		$html = isset( $this->html[ $part ] ) ? $this->html[ $part ] : $this->build_html( $this->parts[ $part ] );

		if ( '' === trim( $html ) ) {
			return;
		}

		$tags = array(
			'header' => 'header',
			'footer' => 'footer',
			'body'   => 'main',
		);

		$tag = isset( $tags[ $part ] ) ? $tags[ $part ] : 'div';

		// The body carries #content because the theme's own content area — the
		// usual target of the header's skip link — never runs.
		$id = 'body' === $part ? ' id="content"' : '';

		printf( '<%1$s%2$s class="eap-theme-part eap-theme-%3$s">', esc_html( $tag ), $id, esc_attr( $part ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- literal.
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor document output.
		printf( '</%s>', esc_html( $tag ) );
	}

	/**
	 * Print the skip link the theme's own header would have carried.
	 *
	 * Styled inline because `.screen-reader-text` is a theme convention, not
	 * something WordPress guarantees.
	 *
	 * @return void
	 */
	public static function skip_link() {
		$target = apply_filters( 'eap_tb_skip_link_target', '#content' );
		$text   = apply_filters( 'eap_tb_skip_link_text', __( 'Skip to content', 'elementor-animatepro' ) );

		if ( '' === $target || '' === $text ) {
			return;
		}
		?>
		<style id="eap-tb-skip-link-style">
			.eap-skip-link{position:absolute;width:1px;height:1px;margin:-1px;padding:0;border:0;overflow:hidden;clip:rect(0,0,0,0);clip-path:inset(50%);white-space:nowrap;}
			.eap-skip-link:focus{position:fixed;top:0;inset-inline-start:0;width:auto;height:auto;margin:0;padding:.75em 1.5em;overflow:visible;clip:auto;clip-path:none;white-space:normal;z-index:100000;background:#fff;color:#0b5cab;font-size:14px;text-decoration:underline;border-radius:0 0 3px 0;outline:2px solid #0b5cab;outline-offset:-2px;}
		</style>
		<?php
		printf( '<a class="eap-skip-link" href="%1$s">%2$s</a>', esc_url( $target ), esc_html( $text ) );
	}

	/**
	 * Replace the theme's header.php.
	 *
	 * @param string|null $name Header variation name passed to get_header().
	 * @return void
	 */
	public function override_header( $name = null ) {
		// A theme that calls get_header() twice must not get two documents.
		if ( empty( $this->printed['header'] ) ) {
			require EAP_PATH . 'includes/theme-builder/templates/header.php';

			// Our template already fired wp_head; stop the discarded theme
			// header from firing it a second time.
			remove_all_actions( 'wp_head' );
		}

		$this->swallow_theme_template( 'header', $name );
	}

	/**
	 * Replace the theme's footer.php.
	 *
	 * @param string|null $name Footer variation name passed to get_footer().
	 * @return void
	 */
	public function override_footer( $name = null ) {
		if ( empty( $this->printed['footer'] ) ) {
			require EAP_PATH . 'includes/theme-builder/templates/footer.php';

			remove_all_actions( 'wp_footer' );
		}

		$this->swallow_theme_template( 'footer', $name );
	}

	/**
	 * Load the theme's own part into a discarded buffer.
	 *
	 * locate_template() loads with require_once, so core's identical call right
	 * after this action does nothing and the theme's markup never prints.
	 *
	 * @param string      $part Part key.
	 * @param string|null $name Variation name.
	 * @return void
	 */
	private function swallow_theme_template( $part, $name ) {
		$templates = array();
		$name      = (string) $name;

		if ( '' !== $name ) {
			$templates[] = $part . '-' . $name . '.php';
		}

		$templates[] = $part . '.php';

		ob_start();
		locate_template( $templates, true );
		ob_get_clean();
	}

	/**
	 * Swap a block theme's header/footer template part for ours.
	 *
	 * @param string               $block_content Rendered block HTML.
	 * @param array<string, mixed> $block         Parsed block.
	 * @return string
	 */
	public function swap_block_part( $block_content, $block ) {
		$attrs = isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();
		$area  = '';

		foreach ( array( 'area', 'slug', 'tagName' ) as $key ) {
			if ( empty( $attrs[ $key ] ) ) {
				continue;
			}

			$candidate = sanitize_key( $attrs[ $key ] );
			if ( 'header' === $candidate || 'footer' === $candidate ) {
				$area = $candidate;
				break;
			}
		}

		if ( '' === $area || empty( $this->parts[ $area ] ) || ! empty( $this->printed[ $area ] ) ) {
			return $block_content;
		}

		ob_start();
		$this->print_part( $area );
		$replacement = ob_get_clean();

		return '' !== trim( (string) $replacement ) ? $replacement : $block_content;
	}

	/**
	 * Flag the active parts on the body.
	 *
	 * @param string[] $classes Body classes.
	 * @return string[]
	 */
	public function body_class( $classes ) {
		foreach ( $this->parts as $part => $template_id ) {
			if ( ! $template_id ) {
				continue;
			}

			// The body says which kind it is, since Single and Archive style
			// very differently.
			$classes[] = 'body' === $part ? 'eap-has-' . $this->body_type : 'eap-has-' . $part;
		}

		return $classes;
	}
}
