<?php
/**
 * Theme Builder template post type.
 *
 * Templates live in their own post type rather than Elementor's library, so the
 * library stays the user's own saved blocks and our conditions never collide
 * with Elementor's template screen.
 *
 * `elementor` in the supports list is what makes a post type editable with
 * Elementor — Utils::is_post_type_support() reads exactly that, which is also
 * how Elementor registers its own library post type.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_TB_Post_Type {

	const POST_TYPE    = 'eap_template';
	const TYPE_META    = '_eap_template_type';
	const ENABLED_META = '_eap_template_enabled';

	/**
	 * Hook into WordPress.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register' ) );
		add_action( 'template_redirect', array( $this, 'guard_direct_access' ) );
		add_filter( 'template_include', array( $this, 'use_canvas_template' ), 999 );
		add_filter( 'wp_robots', array( $this, 'no_robots' ) );
	}

	/**
	 * Register the post type.
	 *
	 * Templates stay publicly queryable because Elementor's editor previews a
	 * document by loading its own permalink; they are kept out of search,
	 * menus, archives and robots instead, and direct hits are 404'd for anyone
	 * who cannot edit them.
	 *
	 * @return void
	 */
	public function register() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => array(
					'name'          => __( 'AnimatePro Templates', 'elementor-animatepro' ),
					'singular_name' => __( 'Template', 'elementor-animatepro' ),
					'add_new_item'  => __( 'Add New Template', 'elementor-animatepro' ),
					'edit_item'     => __( 'Edit Template', 'elementor-animatepro' ),
					'search_items'  => __( 'Search Templates', 'elementor-animatepro' ),
					'not_found'     => __( 'No templates found.', 'elementor-animatepro' ),
				),
				'public'              => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => false,
				'show_in_rest'        => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'rewrite'             => false,
				'query_var'           => false,
				'hierarchical'        => false,
				'capability_type'     => 'post',
				'map_meta_cap'        => true,
				'supports'            => array( 'title', 'elementor', 'author', 'revisions' ),
			)
		);
	}

	/**
	 * Keep templates out of search engines.
	 *
	 * @param array<string, mixed> $robots Robots directives.
	 * @return array<string, mixed>
	 */
	public function no_robots( $robots ) {
		if ( is_singular( self::POST_TYPE ) ) {
			return wp_robots_no_robots( $robots );
		}

		return $robots;
	}

	/**
	 * 404 a template URL for anyone who cannot edit it.
	 *
	 * Editors still reach it, which is what Elementor's preview needs.
	 *
	 * @return void
	 */
	public function guard_direct_access() {
		if ( ! is_singular( self::POST_TYPE ) ) {
			return;
		}

		if ( current_user_can( 'edit_post', get_queried_object_id() ) ) {
			return;
		}

		global $wp_query;

		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}

	/**
	 * Preview a template on Elementor's blank canvas.
	 *
	 * Without this the theme wraps the preview in its own header and footer,
	 * which is exactly what the template is meant to replace.
	 *
	 * @param string $template Template path.
	 * @return string
	 */
	public function use_canvas_template( $template ) {
		if ( ! is_singular( self::POST_TYPE ) || ! defined( 'ELEMENTOR_PATH' ) ) {
			return $template;
		}

		$canvas = ELEMENTOR_PATH . 'modules/page-templates/templates/canvas.php';

		return file_exists( $canvas ) ? $canvas : $template;
	}

	/**
	 * Create a template.
	 *
	 * @param string $type  Template type slug.
	 * @param string $title Template title.
	 * @return int|WP_Error New template ID.
	 */
	public static function create( $type, $title ) {
		if ( ! EAP_TB_Types::is_available( $type ) ) {
			return new WP_Error( 'eap_tb_bad_type', __( 'That template type is not available yet.', 'elementor-animatepro' ) );
		}

		$title = trim( wp_strip_all_tags( (string) $title ) );
		if ( '' === $title ) {
			$title = EAP_TB_Types::label( $type );
		}

		$post_id = wp_insert_post(
			array(
				'post_type'   => self::POST_TYPE,
				'post_title'  => $title,
				'post_status' => 'publish',
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		update_post_meta( $post_id, self::TYPE_META, $type );
		update_post_meta( $post_id, self::ENABLED_META, '1' );

		// Tell Elementor which document type this is, and that it is built
		// content rather than classic editor content.
		update_post_meta( $post_id, '_elementor_template_type', EAP_TB_Types::document_name( $type ) );
		update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );

		return (int) $post_id;
	}

	/**
	 * Duplicate a template, conditions and all, as a disabled copy.
	 *
	 * @param int $template_id Source template ID.
	 * @return int|WP_Error
	 */
	public static function duplicate( $template_id ) {
		$template_id = (int) $template_id;
		$source      = get_post( $template_id );

		if ( ! $source || self::POST_TYPE !== $source->post_type ) {
			return new WP_Error( 'eap_tb_missing', __( 'That template no longer exists.', 'elementor-animatepro' ) );
		}

		$new_id = wp_insert_post(
			array(
				'post_type'    => self::POST_TYPE,
				/* translators: %s: source template name. */
				'post_title'   => sprintf( __( '%s (copy)', 'elementor-animatepro' ), $source->post_title ),
				'post_status'  => 'publish',
				'post_content' => $source->post_content,
			),
			true
		);

		if ( is_wp_error( $new_id ) ) {
			return $new_id;
		}

		$copy_meta = array(
			self::TYPE_META,
			EAP_TB_Conditions::META_KEY,
			'_elementor_template_type',
			'_elementor_edit_mode',
			'_elementor_data',
			'_elementor_page_settings',
		);

		foreach ( $copy_meta as $key ) {
			$value = get_post_meta( $template_id, $key, true );
			if ( '' !== $value && array() !== $value ) {
				update_post_meta( $new_id, $key, $value );
			}
		}

		// A copy starts switched off so it cannot fight the original for the
		// same pages the moment it is created.
		update_post_meta( $new_id, self::ENABLED_META, '0' );

		return (int) $new_id;
	}

	/**
	 * Templates of a type, newest change first.
	 *
	 * @param string $type Template type slug, or '' for all.
	 * @return WP_Post[]
	 */
	public static function get_templates( $type = '' ) {
		$args = array(
			'post_type'              => self::POST_TYPE,
			'post_status'            => array( 'publish', 'draft' ),
			'posts_per_page'         => 200,
			'orderby'                => 'modified',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'suppress_filters'       => false,
		);

		if ( '' !== $type ) {
			$args['meta_key']   = self::TYPE_META; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['meta_value'] = $type; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		}

		return get_posts( $args );
	}

	/**
	 * A template's type slug.
	 *
	 * @param int $template_id Template ID.
	 * @return string
	 */
	public static function get_type( $template_id ) {
		return (string) get_post_meta( (int) $template_id, self::TYPE_META, true );
	}

	/**
	 * Whether a template is switched on.
	 *
	 * @param int $template_id Template ID.
	 * @return bool
	 */
	public static function is_enabled( $template_id ) {
		return '0' !== (string) get_post_meta( (int) $template_id, self::ENABLED_META, true );
	}

	/**
	 * Switch a template on or off.
	 *
	 * @param int  $template_id Template ID.
	 * @param bool $enabled     New state.
	 * @return void
	 */
	public static function set_enabled( $template_id, $enabled ) {
		update_post_meta( (int) $template_id, self::ENABLED_META, $enabled ? '1' : '0' );
	}

	/**
	 * Elementor editor URL for a template.
	 *
	 * @param int $template_id Template ID.
	 * @return string
	 */
	public static function edit_url( $template_id ) {
		return add_query_arg(
			array(
				'post'   => (int) $template_id,
				'action' => 'elementor',
			),
			admin_url( 'post.php' )
		);
	}
}
