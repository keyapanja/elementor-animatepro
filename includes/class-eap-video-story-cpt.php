<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Video Story custom post type.
 *
 * A bespoke post type for the Video Story widget: title + editor + featured image
 * (the poster) + excerpt + author, plus a "Video" meta box holding a self-hosted
 * video URL (`_eap_video_url`). Registration is gated on the `video-story-cpt`
 * toggle on the plugin's Extensions page (defaults on). Instantiated on bootstrap
 * so `init` registers the type on both the front end and admin.
 */
class EAP_Video_Story_CPT {

	const POST_TYPE     = 'eap_video_story';
	const META_KEY      = '_eap_video_url';
	const EXTENSION_KEY = 'video-story-cpt';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save_meta' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
	}

	/**
	 * Whether the CPT is enabled (Extensions page toggle; absent = on).
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
	 * Register the post type when enabled.
	 *
	 * @return void
	 */
	public function register_post_type() {
		if ( ! self::is_enabled() ) {
			return;
		}

		$labels = array(
			'name'               => __( 'Video Stories', 'elementor-animatepro' ),
			'singular_name'      => __( 'Video Story', 'elementor-animatepro' ),
			'menu_name'          => __( 'Video Stories', 'elementor-animatepro' ),
			'add_new'            => __( 'Add New', 'elementor-animatepro' ),
			'add_new_item'       => __( 'Add New Video Story', 'elementor-animatepro' ),
			'edit_item'          => __( 'Edit Video Story', 'elementor-animatepro' ),
			'new_item'           => __( 'New Video Story', 'elementor-animatepro' ),
			'view_item'          => __( 'View Video Story', 'elementor-animatepro' ),
			'search_items'       => __( 'Search Video Stories', 'elementor-animatepro' ),
			'not_found'          => __( 'No video stories found', 'elementor-animatepro' ),
			'not_found_in_trash' => __( 'No video stories found in Trash', 'elementor-animatepro' ),
			'all_items'          => __( 'All Video Stories', 'elementor-animatepro' ),
		);

		register_post_type(
			self::POST_TYPE,
			array(
				'labels'       => $labels,
				'public'       => true,
				'has_archive'  => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-video-alt3',
				'menu_position' => 25,
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author' ),
				'rewrite'      => array( 'slug' => 'video-story' ),
				'taxonomies'   => array( 'category', 'post_tag' ),
			)
		);

		// One-time rewrite flush so single/archive URLs resolve without a manual
		// Permalinks re-save.
		if ( ! get_option( 'eap_video_story_rewrites_flushed' ) ) {
			flush_rewrite_rules( false );
			update_option( 'eap_video_story_rewrites_flushed', 1, false );
		}
	}

	/**
	 * Register the Video meta box.
	 *
	 * @return void
	 */
	public function add_meta_box() {
		add_meta_box(
			'eap-video-story-video',
			__( 'Video', 'elementor-animatepro' ),
			array( $this, 'render_meta_box' ),
			self::POST_TYPE,
			'side',
			'high'
		);
	}

	/**
	 * Render the Video meta box.
	 *
	 * @param WP_Post $post Post.
	 * @return void
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'eap_video_story_meta', 'eap_video_story_nonce' );
		$value = get_post_meta( $post->ID, self::META_KEY, true );
		?>
		<p>
			<label for="eap-video-url"><strong><?php esc_html_e( 'Video URL (MP4 / WebM)', 'elementor-animatepro' ); ?></strong></label>
		</p>
		<input type="url" id="eap-video-url" name="eap_video_url" value="<?php echo esc_attr( $value ); ?>" class="widefat" placeholder="https://example.com/story.mp4" />
		<p>
			<button type="button" class="button" id="eap-video-url-upload"><?php esc_html_e( 'Select / Upload Video', 'elementor-animatepro' ); ?></button>
		</p>
		<p class="description"><?php esc_html_e( 'Self-hosted video for the hover preview and playback. Set a Featured Image as the poster.', 'elementor-animatepro' ); ?></p>
		<?php
	}

	/**
	 * Save the Video meta.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_meta( $post_id ) {
		if ( ! isset( $_POST['eap_video_story_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['eap_video_story_nonce'] ) ), 'eap_video_story_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$url = isset( $_POST['eap_video_url'] ) ? esc_url_raw( wp_unslash( $_POST['eap_video_url'] ) ) : '';

		if ( '' === $url ) {
			delete_post_meta( $post_id, self::META_KEY );
		} else {
			update_post_meta( $post_id, self::META_KEY, $url );
		}
	}

	/**
	 * Enqueue the media picker on the Video Story edit screen.
	 *
	 * @return void
	 */
	public function enqueue_admin() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || self::POST_TYPE !== $screen->post_type ) {
			return;
		}

		wp_enqueue_media();

		$js = <<<'JS'
( function () {
	var btn = document.getElementById( 'eap-video-url-upload' );
	var input = document.getElementById( 'eap-video-url' );
	if ( ! btn || ! input || ! window.wp || ! window.wp.media ) {
		return;
	}
	var frame;
	btn.addEventListener( 'click', function ( e ) {
		e.preventDefault();
		if ( frame ) { frame.open(); return; }
		frame = window.wp.media( {
			title: 'Select or Upload Video',
			library: { type: 'video' },
			button: { text: 'Use this video' },
			multiple: false
		} );
		frame.on( 'select', function () {
			var att = frame.state().get( 'selection' ).first().toJSON();
			if ( att && att.url ) { input.value = att.url; }
		} );
		frame.open();
	} );
} )();
JS;

		wp_add_inline_script( 'media-editor', $js );
	}
}
