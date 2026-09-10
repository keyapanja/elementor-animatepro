<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Portfolio post type.
 *
 * Registers `eap_portfolio` plus its own `eap_portfolio_category` taxonomy and a
 * Project Details meta box (client, project URL, completed date).
 *
 * The taxonomy is DEDICATED rather than reusing the blog `category`: a portfolio
 * is filtered by things like "Branding" or "Web Design", and mixing those into
 * the post categories pollutes the blog's archives and category widgets. (Video
 * Story shares `category` because a video story really is editorial content;
 * project work is not.)
 *
 * `page-attributes` is supported so items can be hand-ordered with menu_order —
 * portfolios are curated far more often than they are chronological.
 *
 * Gated by the `portfolio-cpt` toggle on the Extensions page, following the
 * pattern EAP_Video_Story_CPT established.
 */
class EAP_Portfolio_CPT {

	const POST_TYPE     = 'eap_portfolio';
	const TAXONOMY      = 'eap_portfolio_category';
	const EXTENSION_KEY = 'portfolio-cpt';

	const META_CLIENT = '_eap_portfolio_client';
	const META_URL    = '_eap_portfolio_url';
	const META_DATE   = '_eap_portfolio_date';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save_meta' ), 10, 1 );
	}

	/**
	 * Whether the post type is enabled (Extensions page toggle; absent = on).
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$states = get_option( EAP_Admin::EXTENSIONS_OPTION, array() );
		if ( ! is_array( $states ) ) {
			return true;
		}
		return ! array_key_exists( self::EXTENSION_KEY, $states ) || ! empty( $states[ self::EXTENSION_KEY ] );
	}

	/**
	 * Register the post type and its taxonomy.
	 *
	 * @return void
	 */
	public function register_post_type() {
		if ( ! self::is_enabled() ) {
			return;
		}

		register_post_type(
			self::POST_TYPE,
			array(
				'labels'        => array(
					'name'               => __( 'Portfolio', 'elementor-animatepro' ),
					'singular_name'      => __( 'Project', 'elementor-animatepro' ),
					'menu_name'          => __( 'Portfolio', 'elementor-animatepro' ),
					'add_new'            => __( 'Add New', 'elementor-animatepro' ),
					'add_new_item'       => __( 'Add New Project', 'elementor-animatepro' ),
					'edit_item'          => __( 'Edit Project', 'elementor-animatepro' ),
					'new_item'           => __( 'New Project', 'elementor-animatepro' ),
					'view_item'          => __( 'View Project', 'elementor-animatepro' ),
					'search_items'       => __( 'Search Projects', 'elementor-animatepro' ),
					'not_found'          => __( 'No projects found', 'elementor-animatepro' ),
					'not_found_in_trash' => __( 'No projects found in Trash', 'elementor-animatepro' ),
					'all_items'          => __( 'All Projects', 'elementor-animatepro' ),
				),
				'public'        => true,
				'has_archive'   => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-portfolio',
				'menu_position' => 26,
				'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'page-attributes' ),
				'rewrite'       => array( 'slug' => 'portfolio' ),
			)
		);

		register_taxonomy(
			self::TAXONOMY,
			self::POST_TYPE,
			array(
				'labels'            => array(
					'name'          => __( 'Project Categories', 'elementor-animatepro' ),
					'singular_name' => __( 'Project Category', 'elementor-animatepro' ),
					'menu_name'     => __( 'Categories', 'elementor-animatepro' ),
					'add_new_item'  => __( 'Add New Project Category', 'elementor-animatepro' ),
					'edit_item'     => __( 'Edit Project Category', 'elementor-animatepro' ),
					'search_items'  => __( 'Search Project Categories', 'elementor-animatepro' ),
					'all_items'     => __( 'All Project Categories', 'elementor-animatepro' ),
				),
				'public'            => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'portfolio-category' ),
			)
		);

		// One-time rewrite flush so single/archive URLs resolve without a manual
		// Permalinks re-save. Keyed to its own option so it runs once per site.
		if ( ! get_option( 'eap_portfolio_rewrites_flushed' ) ) {
			flush_rewrite_rules( false );
			update_option( 'eap_portfolio_rewrites_flushed', 1, false );
		}
	}

	/**
	 * Register the Project Details meta box.
	 *
	 * @return void
	 */
	public function add_meta_box() {
		if ( ! self::is_enabled() ) {
			return;
		}

		add_meta_box(
			'eap_portfolio_details',
			__( 'Project Details', 'elementor-animatepro' ),
			array( $this, 'render_meta_box' ),
			self::POST_TYPE,
			'side',
			'default'
		);
	}

	/**
	 * Render the meta box.
	 *
	 * @param WP_Post $post Post.
	 * @return void
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'eap_portfolio_details', 'eap_portfolio_details_nonce' );

		$client = (string) get_post_meta( $post->ID, self::META_CLIENT, true );
		$url    = (string) get_post_meta( $post->ID, self::META_URL, true );
		$date   = (string) get_post_meta( $post->ID, self::META_DATE, true );
		?>
		<p>
			<label for="eap-portfolio-client"><strong><?php esc_html_e( 'Client', 'elementor-animatepro' ); ?></strong></label><br>
			<input type="text" id="eap-portfolio-client" name="eap_portfolio_client" class="widefat"
				value="<?php echo esc_attr( $client ); ?>" placeholder="<?php esc_attr_e( 'Acme Inc.', 'elementor-animatepro' ); ?>">
		</p>
		<p>
			<label for="eap-portfolio-url"><strong><?php esc_html_e( 'Project URL', 'elementor-animatepro' ); ?></strong></label><br>
			<input type="url" id="eap-portfolio-url" name="eap_portfolio_url" class="widefat"
				value="<?php echo esc_attr( $url ); ?>" placeholder="https://">
			<span class="description"><?php esc_html_e( 'The live project. The widget can link tiles here instead of to this item.', 'elementor-animatepro' ); ?></span>
		</p>
		<p>
			<label for="eap-portfolio-date"><strong><?php esc_html_e( 'Completed', 'elementor-animatepro' ); ?></strong></label><br>
			<input type="date" id="eap-portfolio-date" name="eap_portfolio_date" class="widefat"
				value="<?php echo esc_attr( $date ); ?>">
		</p>
		<?php
	}

	/**
	 * Save the meta box.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_meta( $post_id ) {
		if ( ! isset( $_POST['eap_portfolio_details_nonce'] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['eap_portfolio_details_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'eap_portfolio_details' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$client = isset( $_POST['eap_portfolio_client'] ) ? sanitize_text_field( wp_unslash( $_POST['eap_portfolio_client'] ) ) : '';
		$url    = isset( $_POST['eap_portfolio_url'] ) ? esc_url_raw( wp_unslash( $_POST['eap_portfolio_url'] ) ) : '';
		$date   = isset( $_POST['eap_portfolio_date'] ) ? sanitize_text_field( wp_unslash( $_POST['eap_portfolio_date'] ) ) : '';

		// Only ever store an ISO date, so the widget can format it predictably.
		if ( '' !== $date && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
			$date = '';
		}

		$this->save_or_delete( $post_id, self::META_CLIENT, $client );
		$this->save_or_delete( $post_id, self::META_URL, $url );
		$this->save_or_delete( $post_id, self::META_DATE, $date );
	}

	/**
	 * Write a meta value, or remove the row when it is empty.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key.
	 * @param string $value   Value.
	 * @return void
	 */
	protected function save_or_delete( $post_id, $key, $value ) {
		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
			return;
		}
		update_post_meta( $post_id, $key, $value );
	}
}
