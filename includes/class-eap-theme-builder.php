<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_Theme_Builder {

	const POST_TYPE = 'eap_template';

	/**
	 * Whether the custom header has already been rendered.
	 *
	 * @var bool
	 */
	private $header_rendered = false;

	/**
	 * Whether the custom footer has already been rendered.
	 *
	 * @var bool
	 */
	private $footer_rendered = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'elementor_cpt_support', array( $this, 'register_elementor_support' ) );
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save_template_meta' ) );
		add_action( 'wp_head', array( $this, 'print_replacement_styles' ), 1 );
		add_action( 'wp_body_open', array( $this, 'render_header_template' ), 1 );
		add_action( 'loop_start', array( $this, 'render_header_template_fallback' ), 1 );
		add_action( 'wp_footer', array( $this, 'render_footer_template' ), 5 );
		add_filter( 'body_class', array( $this, 'add_body_classes' ) );
	}

	/**
	 * Add template CPT to Elementor support list.
	 *
	 * @param array $post_types Supported post types.
	 * @return array
	 */
	public function register_elementor_support( $post_types ) {
		$post_types[] = self::POST_TYPE;
		return array_unique( $post_types );
	}

	/**
	 * Register template post type.
	 *
	 * @return void
	 */
	public function register_post_type() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels' => array(
					'name'          => __( 'Header & Footer', 'elementor-animatepro' ),
					'singular_name' => __( 'Template', 'elementor-animatepro' ),
					'add_new_item'  => __( 'Add New Template', 'elementor-animatepro' ),
					'edit_item'     => __( 'Edit Template', 'elementor-animatepro' ),
				),
				'public'              => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => 'elementor-animatepro',
				'show_in_rest'        => true,
				'has_archive'         => false,
				'rewrite'             => false,
				'query_var'           => true,
				'exclude_from_search' => true,
				'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions' ),
				'menu_position'       => 58,
			)
		);
	}

	/**
	 * Register display settings meta box.
	 *
	 * @return void
	 */
	public function register_meta_boxes() {
		add_meta_box(
			'eap-template-conditions',
			__( 'Display Settings', 'elementor-animatepro' ),
			array( $this, 'render_meta_box' ),
			self::POST_TYPE,
			'side',
			'high'
		);
	}

	/**
	 * Render display settings.
	 *
	 * @param WP_Post $post Post object.
	 * @return void
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'eap_template_meta', 'eap_template_meta_nonce' );

		$type          = get_post_meta( $post->ID, '_eap_template_type', true );
		$condition     = get_post_meta( $post->ID, '_eap_template_condition', true );
		$post_types    = get_post_meta( $post->ID, '_eap_template_post_types', true );
		$specific_ids  = get_post_meta( $post->ID, '_eap_template_specific_ids', true );
		$post_types    = is_array( $post_types ) ? $post_types : array();
		$specific_ids  = is_array( $specific_ids ) ? array_map( 'absint', $specific_ids ) : array();
		$available_types = $this->get_available_post_types();
		$entries         = $this->get_available_entries();
		?>
		<p>
			<label for="eap-template-type"><strong><?php esc_html_e( 'Template Type', 'elementor-animatepro' ); ?></strong></label>
			<select name="eap_template_type" id="eap-template-type" class="widefat">
				<option value="header" <?php selected( $type, 'header' ); ?>><?php esc_html_e( 'Header', 'elementor-animatepro' ); ?></option>
				<option value="footer" <?php selected( $type, 'footer' ); ?>><?php esc_html_e( 'Footer', 'elementor-animatepro' ); ?></option>
			</select>
		</p>
		<p>
			<label for="eap-template-condition"><strong><?php esc_html_e( 'Display Condition', 'elementor-animatepro' ); ?></strong></label>
			<select name="eap_template_condition" id="eap-template-condition" class="widefat">
				<option value="entire_site" <?php selected( $condition, 'entire_site' ); ?>><?php esc_html_e( 'Entire Website', 'elementor-animatepro' ); ?></option>
				<option value="post_types" <?php selected( $condition, 'post_types' ); ?>><?php esc_html_e( 'Selected Post Types', 'elementor-animatepro' ); ?></option>
				<option value="specific_entries" <?php selected( $condition, 'specific_entries' ); ?>><?php esc_html_e( 'Selected Entries', 'elementor-animatepro' ); ?></option>
			</select>
		</p>
		<div data-eap-condition-panel="post_types">
			<strong><?php esc_html_e( 'Post Types', 'elementor-animatepro' ); ?></strong>
			<p class="description"><?php esc_html_e( 'Choose the content types where this template should appear. Internal builder/template post types are excluded.', 'elementor-animatepro' ); ?></p>
			<div style="max-height:140px; overflow:auto; border:1px solid #e5e7eb; padding:8px; border-radius:8px;">
				<?php foreach ( $available_types as $slug => $label ) : ?>
					<label style="display:block; margin-bottom:6px;">
						<input type="checkbox" name="eap_template_post_types[]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( in_array( $slug, $post_types, true ) ); ?> />
						<?php echo esc_html( $label ); ?>
					</label>
				<?php endforeach; ?>
			</div>
		</div>
		<div style="margin-top:12px;" data-eap-condition-panel="specific_entries">
			<strong><?php esc_html_e( 'Specific Entries', 'elementor-animatepro' ); ?></strong>
			<p class="description"><?php esc_html_e( 'Use this only when Display Condition is set to Selected Entries.', 'elementor-animatepro' ); ?></p>
			<div style="max-height:180px; overflow:auto; border:1px solid #e5e7eb; padding:8px; border-radius:8px;">
				<?php foreach ( $entries as $entry ) : ?>
					<label style="display:block; margin-bottom:6px;">
						<input type="checkbox" name="eap_template_specific_ids[]" value="<?php echo esc_attr( $entry['id'] ); ?>" <?php checked( in_array( $entry['id'], $specific_ids, true ) ); ?> />
						<?php echo esc_html( $entry['label'] ); ?>
					</label>
				<?php endforeach; ?>
			</div>
		</div>
		<script>
			document.addEventListener('DOMContentLoaded', function () {
				var condition = document.getElementById('eap-template-condition');
				if (!condition) {
					return;
				}

				var syncPanels = function () {
					document.querySelectorAll('[data-eap-condition-panel]').forEach(function (panel) {
						var mode = panel.getAttribute('data-eap-condition-panel');
						panel.style.display = mode === condition.value ? 'block' : 'none';
					});
				};

				condition.addEventListener('change', syncPanels);
				syncPanels();
			});
		</script>
		<?php
	}

	/**
	 * Save template metadata.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_template_meta( $post_id ) {
		if ( ! isset( $_POST['eap_template_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['eap_template_meta_nonce'] ) ), 'eap_template_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$type         = isset( $_POST['eap_template_type'] ) ? sanitize_key( wp_unslash( $_POST['eap_template_type'] ) ) : 'header';
		$condition    = isset( $_POST['eap_template_condition'] ) ? sanitize_key( wp_unslash( $_POST['eap_template_condition'] ) ) : 'entire_site';
		$post_types   = isset( $_POST['eap_template_post_types'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['eap_template_post_types'] ) ) : array();
		$specific_ids = isset( $_POST['eap_template_specific_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['eap_template_specific_ids'] ) ) : array();

		update_post_meta( $post_id, '_eap_template_type', $type );
		update_post_meta( $post_id, '_eap_template_condition', $condition );
		update_post_meta( $post_id, '_eap_template_post_types', $post_types );
		update_post_meta( $post_id, '_eap_template_specific_ids', $specific_ids );
	}

	/**
	 * Add body classes when replacement is active.
	 *
	 * @param array $classes Body classes.
	 * @return array
	 */
	public function add_body_classes( $classes ) {
		if ( is_admin() || $this->is_builder_context() ) {
			return $classes;
		}

		if ( $this->get_matching_template_id( 'header' ) ) {
			$classes[] = 'eap-has-custom-header';
		}

		if ( $this->get_matching_template_id( 'footer' ) ) {
			$classes[] = 'eap-has-custom-footer';
		}

		return $classes;
	}

	/**
	 * Print replacement styles to suppress common theme chrome.
	 *
	 * @return void
	 */
	public function print_replacement_styles() {
		if ( is_admin() || $this->is_builder_context() ) {
			return;
		}

		$has_header = (bool) $this->get_matching_template_id( 'header' );
		$has_footer = (bool) $this->get_matching_template_id( 'footer' );

		if ( ! $has_header && ! $has_footer ) {
			return;
		}
		?>
		<style id="eap-theme-replacements">
			<?php if ( $has_header ) : ?>
			body.eap-has-custom-header header.site-header,
			body.eap-has-custom-header #masthead,
			body.eap-has-custom-header .site-header,
			body.eap-has-custom-header #site-header,
			body.eap-has-custom-header [role="banner"],
			body.eap-has-custom-header > header,
			body.eap-has-custom-header .main-header-bar-wrap,
			body.eap-has-custom-header .site-header-wrap,
			body.eap-has-custom-header .site-header-row,
			body.eap-has-custom-header .header-wrapper,
			body.eap-has-custom-header .elementor-location-header,
			body.eap-has-custom-header .site-header-primary-section-left,
			body.eap-has-custom-header .site-header-primary-section-right {
				display: none !important;
			}
			<?php endif; ?>
			<?php if ( $has_footer ) : ?>
			body.eap-has-custom-footer footer.site-footer,
			body.eap-has-custom-footer #colophon,
			body.eap-has-custom-footer .site-footer,
			body.eap-has-custom-footer #site-footer,
			body.eap-has-custom-footer [role="contentinfo"],
			body.eap-has-custom-footer > footer,
			body.eap-has-custom-footer .site-primary-footer-wrap,
			body.eap-has-custom-footer .site-below-footer-wrap,
			body.eap-has-custom-footer .footer-wrapper,
			body.eap-has-custom-footer .elementor-location-footer {
				display: none !important;
			}
			<?php endif; ?>
		</style>
		<?php
	}

	/**
	 * Render matching header template.
	 *
	 * @return void
	 */
	public function render_header_template() {
		if ( $this->header_rendered || is_admin() || $this->is_builder_context() ) {
			return;
		}

		$template_id = $this->get_matching_template_id( 'header' );
		if ( $template_id ) {
			$this->header_rendered = true;
			echo '<div class="eap-theme-part eap-theme-header">';
			echo $this->get_template_content( $template_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>';
		}
	}

	/**
	 * Render the header before content if the theme does not call wp_body_open().
	 *
	 * @return void
	 */
	public function render_header_template_fallback() {
		$this->render_header_template();
	}

	/**
	 * Render matching footer template.
	 *
	 * @return void
	 */
	public function render_footer_template() {
		if ( $this->footer_rendered || is_admin() || $this->is_builder_context() ) {
			return;
		}

		$template_id = $this->get_matching_template_id( 'footer' );
		if ( $template_id ) {
			$this->footer_rendered = true;
			echo '<div class="eap-theme-part eap-theme-footer">';
			echo $this->get_template_content( $template_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>';
		}
	}

	/**
	 * Get matching template ID.
	 *
	 * @param string $type Template type.
	 * @return int
	 */
	private function get_matching_template_id( $type ) {
		$query = new WP_Query(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'meta_query'     => array(
					array(
						'key'   => '_eap_template_type',
						'value' => $type,
					),
				),
			)
		);

		if ( ! $query->have_posts() ) {
			return 0;
		}

		$current_id        = get_queried_object_id();
		$current_post_type = $this->get_current_display_post_type();

		if ( self::POST_TYPE === $current_post_type ) {
			return 0;
		}

		foreach ( $query->posts as $post ) {
			$condition    = get_post_meta( $post->ID, '_eap_template_condition', true );
			$post_types   = get_post_meta( $post->ID, '_eap_template_post_types', true );
			$specific_ids = get_post_meta( $post->ID, '_eap_template_specific_ids', true );

			$post_types   = is_array( $post_types ) ? $post_types : array();
			$specific_ids = is_array( $specific_ids ) ? array_map( 'absint', $specific_ids ) : array();

			if ( 'entire_site' === $condition ) {
				return (int) $post->ID;
			}

			if ( 'post_types' === $condition && $current_post_type && in_array( $current_post_type, $post_types, true ) ) {
				return (int) $post->ID;
			}

			if ( 'specific_entries' === $condition && $current_id && in_array( $current_id, $specific_ids, true ) ) {
				return (int) $post->ID;
			}
		}

		return 0;
	}

	/**
	 * Get template content.
	 *
	 * @param int $template_id Template ID.
	 * @return string
	 */
	private function get_template_content( $template_id ) {
		if ( class_exists( '\Elementor\Plugin' ) && did_action( 'elementor/loaded' ) ) {
			return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id, true );
		}

		return apply_filters( 'the_content', get_post_field( 'post_content', $template_id ) );
	}

	/**
	 * Determine whether the current request is a builder/editor context.
	 *
	 * @return bool
	 */
	private function is_builder_context() {
		if ( is_admin() ) {
			return true;
		}

		if ( isset( $_GET['elementor-preview'] ) || isset( $_GET['action'] ) && 'elementor' === sanitize_key( wp_unslash( $_GET['action'] ) ) ) {
			return true;
		}

		if ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the current content type used for display conditions.
	 *
	 * @return string
	 */
	private function get_current_display_post_type() {
		$current_id = get_queried_object_id();
		if ( $current_id ) {
			$post_type = get_post_type( $current_id );
			if ( $post_type ) {
				return $post_type;
			}
		}

		$queried_post_type = get_query_var( 'post_type' );
		if ( is_string( $queried_post_type ) && ! empty( $queried_post_type ) ) {
			return $queried_post_type;
		}

		if ( is_array( $queried_post_type ) && ! empty( $queried_post_type[0] ) ) {
			return sanitize_key( $queried_post_type[0] );
		}

		if ( is_home() || is_category() || is_tag() || is_author() || is_date() || is_search() ) {
			return 'post';
		}

		if ( function_exists( 'is_shop' ) && is_shop() ) {
			return 'product';
		}

		return '';
	}

	/**
	 * Available post types for visibility conditions.
	 *
	 * @return array<string, string>
	 */
	private function get_available_post_types() {
		$post_types = get_post_types(
			array(
				'publicly_queryable' => true,
				'show_ui'             => true,
			),
			'objects'
		);

		$options = array();
		$excluded = array(
			self::POST_TYPE,
			'attachment',
			'elementor_library',
			'elementskit_template',
			'floating_element',
			'wp_template',
			'wp_template_part',
			'wp_block',
		);
		foreach ( $post_types as $post_type ) {
			if ( in_array( $post_type->name, $excluded, true ) ) {
				continue;
			}

			if ( 0 === strpos( $post_type->name, 'elementor_' ) || 0 === strpos( $post_type->name, 'elementskit_' ) ) {
				continue;
			}

			if ( false !== stripos( $post_type->label, 'template' ) ) {
				continue;
			}

			if ( false !== stripos( $post_type->label, 'kit' ) ) {
				continue;
			}

			$options[ $post_type->name ] = $post_type->labels->singular_name;
		}

		return $options;
	}

	/**
	 * Available entries for specific conditions.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function get_available_entries() {
		$options = array();
		$post_types = array_keys( $this->get_available_post_types() );
		if ( empty( $post_types ) ) {
			return $options;
		}

		$posts = get_posts(
			array(
				'post_type'      => $post_types,
				'post_status'    => array( 'publish', 'draft', 'private' ),
				'posts_per_page' => 150,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		foreach ( $posts as $post ) {
			$label = get_post_type_object( $post->post_type );
			$options[] = array(
				'id'    => (int) $post->ID,
				'label' => sprintf( '%1$s: %2$s', $label ? $label->labels->singular_name : $post->post_type, $post->post_title ? $post->post_title : '#' . $post->ID ),
			);
		}

		return $options;
	}
}
