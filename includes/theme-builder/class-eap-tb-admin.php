<?php
/**
 * Theme Builder admin screen.
 *
 * Renders the body of the Theme Builder page inside the existing AnimatePro
 * admin shell, and handles every template action (create, rename, duplicate,
 * enable, delete, save conditions) through admin-post.php.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_TB_Admin {

	const ACTION     = 'eap_tb_action';
	const NONCE      = 'eap_tb_action';
	const AJAX_NONCE = 'eap_tb';
	const CAPABILITY = 'manage_options';

	/**
	 * Hook into WordPress.
	 *
	 * Kept out of the constructor so that render_body() can build a throwaway
	 * instance without registering a second set of handlers.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_post_' . self::ACTION, array( $this, 'handle_action' ) );
		add_action( 'wp_ajax_eap_tb_search', array( $this, 'ajax_search' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 20 );
	}

	/**
	 * Load the Theme Builder styles and script on its own page.
	 *
	 * @return void
	 */
	public function enqueue() {
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( EAP_Admin::THEME_BUILDER_SLUG !== $page ) {
			return;
		}

		wp_enqueue_style(
			'eap-theme-builder',
			EAP_URL . 'assets/css/theme-builder.css',
			array( 'eap-admin' ),
			EAP_VERSION
		);

		wp_enqueue_script(
			'eap-theme-builder',
			EAP_URL . 'assets/js/theme-builder.js',
			array(),
			EAP_VERSION,
			true
		);

		wp_localize_script( 'eap-theme-builder', 'EAPThemeBuilder', $this->get_script_data() );
	}

	/**
	 * Everything the conditions editor needs client-side.
	 *
	 * @return array<string, mixed>
	 */
	private function get_script_data() {
		$schema = array();

		foreach ( EAP_TB_Conditions::schema() as $scope => $definition ) {
			$subs = array();

			foreach ( $definition['subs'] as $sub => $sub_definition ) {
				$subs[ $sub ] = array(
					'label' => $sub_definition['label'],
					'value' => $sub_definition['value'],
				);
			}

			$schema[ $scope ] = array(
				'label' => $definition['label'],
				'subs'  => $subs,
			);
		}

		$definitions = EAP_TB_Conditions::schema();
		$templates   = array();

		foreach ( EAP_TB_Post_Type::get_templates() as $template ) {
			$rules   = EAP_TB_Conditions::get( $template->ID );
			$payload = array();

			foreach ( $rules as $rule ) {
				// The editor needs the readable name of whatever a rule points
				// at, so its picker can show the current choice before any
				// search has run.
				$value_type    = isset( $definitions[ $rule['scope'] ]['subs'][ $rule['sub'] ]['value'] ) ? $definitions[ $rule['scope'] ]['subs'][ $rule['sub'] ]['value'] : 'none';
				$rule['label'] = EAP_TB_Conditions::value_label( $value_type, $rule['value'] );
				$payload[]     = $rule;
			}

			$type    = EAP_TB_Post_Type::get_type( $template->ID );
			$preview = EAP_TB_Preview::get( $template->ID );
			$kinds   = EAP_TB_Preview::kinds_for( $type );

			if ( '' !== $preview['kind'] && isset( $kinds[ $preview['kind'] ] ) ) {
				$preview['label'] = EAP_TB_Conditions::value_label( $kinds[ $preview['kind'] ]['value'], $preview['value'] );
			} else {
				$preview['label'] = '';
			}

			$templates[ (string) $template->ID ] = array(
				'name'    => $template->post_title,
				'type'    => $type,
				'rules'   => $payload,
				'preview' => $preview,
			);
		}

		$preview_kinds = array();

		foreach ( array_keys( EAP_TB_Types::available() ) as $slug ) {
			if ( EAP_TB_Preview::supports( $slug ) ) {
				$preview_kinds[ $slug ] = EAP_TB_Preview::kinds_for( $slug );
			}
		}

		$type_labels = array();

		foreach ( EAP_TB_Types::available() as $slug => $type ) {
			$type_labels[ $slug ] = $type['label'];
		}

		return array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( self::AJAX_NONCE ),
			'schema'     => $schema,
			'postTypes'  => $this->get_post_type_options(),
			'taxonomies' => $this->get_taxonomy_options(),
			'templates'  => $templates,
			'types'      => $type_labels,
			'previews'   => $preview_kinds,
			'i18n'       => array(
				'include'     => __( 'Show on', 'elementor-animatepro' ),
				'exclude'     => __( 'Hide on', 'elementor-animatepro' ),
				'remove'      => __( 'Remove condition', 'elementor-animatepro' ),
				'choose'      => __( '— Choose —', 'elementor-animatepro' ),
				'search'      => __( 'Type to search…', 'elementor-animatepro' ),
				'emptyRules'  => __( 'No conditions yet, so this template is not displayed anywhere.', 'elementor-animatepro' ),
				'deleteCheck' => __( 'Delete this template? This cannot be undone.', 'elementor-animatepro' ),
				/* translators: %s: template type, e.g. Header. */
				'newTitle'    => __( 'Name your %s', 'elementor-animatepro' ),
				'renameTitle' => __( 'Rename template', 'elementor-animatepro' ),
				'createCta'   => __( 'Create and Edit', 'elementor-animatepro' ),
				'saveCta'     => __( 'Save Name', 'elementor-animatepro' ),
				'previewNone' => __( 'Nothing chosen', 'elementor-animatepro' ),
			),
		);
	}

	/**
	 * Post types a condition can point at.
	 *
	 * @return array<string, string>
	 */
	private function get_post_type_options() {
		$options = array();

		foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
			if ( in_array( $post_type->name, array( EAP_TB_Post_Type::POST_TYPE, 'attachment', 'elementor_library' ), true ) ) {
				continue;
			}

			$options[ $post_type->name ] = $post_type->labels->name;
		}

		return $options;
	}

	/**
	 * Taxonomies a condition can point at.
	 *
	 * @return array<string, string>
	 */
	private function get_taxonomy_options() {
		$options = array();

		foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $taxonomy ) {
			if ( in_array( $taxonomy->name, array( 'post_format', 'elementor_library_type', 'elementor_library_category' ), true ) ) {
				continue;
			}

			$options[ $taxonomy->name ] = $taxonomy->labels->name;
		}

		return $options;
	}

	/**
	 * Search entries, terms or authors for the conditions editor.
	 *
	 * @return void
	 */
	public function ajax_search() {
		check_ajax_referer( self::AJAX_NONCE, 'nonce' );

		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_send_json_error( array(), 403 );
		}

		$kind   = isset( $_GET['kind'] ) ? sanitize_key( wp_unslash( $_GET['kind'] ) ) : '';
		$search = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
		$items  = array();

		if ( 'entry' === $kind ) {
			$posts = get_posts(
				array(
					'post_type'        => array_keys( $this->get_post_type_options() ),
					'post_status'      => array( 'publish', 'draft', 'private' ),
					'posts_per_page'   => 30,
					's'                => $search,
					'orderby'          => 'title',
					'order'            => 'ASC',
					'suppress_filters' => false,
				)
			);

			foreach ( $posts as $post ) {
				$object  = get_post_type_object( $post->post_type );
				$items[] = array(
					'value' => (string) $post->ID,
					'label' => sprintf(
						'%1$s: %2$s',
						$object ? $object->labels->singular_name : $post->post_type,
						'' !== $post->post_title ? $post->post_title : sprintf( '#%d', $post->ID )
					),
				);
			}
		} elseif ( 'term' === $kind ) {
			$terms = get_terms(
				array(
					'taxonomy'   => array_keys( $this->get_taxonomy_options() ),
					'search'     => $search,
					'number'     => 30,
					'hide_empty' => false,
				)
			);

			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$taxonomy = get_taxonomy( $term->taxonomy );
					$items[]  = array(
						'value' => $term->taxonomy . ':' . $term->term_id,
						'label' => sprintf(
							'%1$s: %2$s',
							$taxonomy ? $taxonomy->labels->singular_name : $term->taxonomy,
							$term->name
						),
					);
				}
			}
		} elseif ( 'author' === $kind ) {
			$users = get_users(
				array(
					'capability' => array( 'edit_posts' ),
					'search'     => '' !== $search ? '*' . $search . '*' : '',
					'number'     => 30,
					'orderby'    => 'display_name',
				)
			);

			foreach ( $users as $user ) {
				$items[] = array(
					'value' => (string) $user->ID,
					'label' => $user->display_name,
				);
			}
		} else {
			wp_send_json_error( array(), 400 );
		}

		wp_send_json_success( $items );
	}

	/**
	 * Handle a template action posted from the Theme Builder page.
	 *
	 * @return void
	 */
	public function handle_action() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You are not allowed to manage templates.', 'elementor-animatepro' ), 403 );
		}

		check_admin_referer( self::NONCE );

		$task        = isset( $_POST['eap_tb_task'] ) ? sanitize_key( wp_unslash( $_POST['eap_tb_task'] ) ) : '';
		$template_id = isset( $_POST['template_id'] ) ? absint( wp_unslash( $_POST['template_id'] ) ) : 0;

		if ( $template_id && EAP_TB_Post_Type::POST_TYPE !== get_post_type( $template_id ) ) {
			$this->redirect_back( 'missing' );
		}

		switch ( $task ) {
			case 'create':
				$this->do_create();
				break;

			case 'rename':
				$this->do_rename( $template_id );
				break;

			case 'duplicate':
				$result = EAP_TB_Post_Type::duplicate( $template_id );
				$this->redirect_back( is_wp_error( $result ) ? 'error' : 'duplicated' );
				break;

			case 'toggle':
				$enabled = isset( $_POST['enabled'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['enabled'] ) );
				EAP_TB_Post_Type::set_enabled( $template_id, $enabled );
				$this->redirect_back( $enabled ? 'enabled' : 'disabled' );
				break;

			case 'delete':
				wp_delete_post( $template_id, true );
				$this->redirect_back( 'deleted' );
				break;

			case 'save_preview':
				EAP_TB_Preview::save(
					$template_id,
					array(
						'kind'  => isset( $_POST['preview_kind'] ) ? sanitize_key( wp_unslash( $_POST['preview_kind'] ) ) : '',
						'value' => isset( $_POST['preview_value'] ) ? sanitize_text_field( wp_unslash( $_POST['preview_value'] ) ) : '',
					)
				);
				$this->redirect_back( 'preview' );
				break;

			case 'save_conditions':
				$raw = isset( $_POST['rules'] ) ? wp_unslash( $_POST['rules'] ) : array(); // phpcs:ignore WordPress.Security.ValidationSanitization.MissingUnslash,WordPress.Security.ValidationSanitization.InputNotSanitized -- sanitized by EAP_TB_Conditions::save().
				EAP_TB_Conditions::save( $template_id, $raw );
				$this->redirect_back( 'conditions' );
				break;
		}

		$this->redirect_back( '' );
	}

	/**
	 * Create a template and go straight to the editor.
	 *
	 * @return void
	 */
	private function do_create() {
		$type  = isset( $_POST['template_type'] ) ? sanitize_key( wp_unslash( $_POST['template_type'] ) ) : '';
		$title = isset( $_POST['template_title'] ) ? sanitize_text_field( wp_unslash( $_POST['template_title'] ) ) : '';

		$template_id = EAP_TB_Post_Type::create( $type, $title );

		if ( is_wp_error( $template_id ) ) {
			$this->redirect_back( 'error' );
		}

		wp_safe_redirect( EAP_TB_Post_Type::edit_url( $template_id ) );
		exit;
	}

	/**
	 * Rename a template.
	 *
	 * @param int $template_id Template ID.
	 * @return void
	 */
	private function do_rename( $template_id ) {
		$title = isset( $_POST['template_title'] ) ? sanitize_text_field( wp_unslash( $_POST['template_title'] ) ) : '';
		$title = trim( $title );

		if ( '' === $title || ! $template_id ) {
			$this->redirect_back( 'error' );
		}

		wp_update_post(
			array(
				'ID'         => $template_id,
				'post_title' => $title,
			)
		);

		$this->redirect_back( 'renamed' );
	}

	/**
	 * Back to the Theme Builder page with a message.
	 *
	 * @param string $message Message key.
	 * @return void
	 */
	private function redirect_back( $message ) {
		$url = admin_url( 'admin.php?page=' . EAP_Admin::THEME_BUILDER_SLUG );

		if ( '' !== $message ) {
			$url = add_query_arg( 'eap-tb-msg', $message, $url );
		}

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Render the Theme Builder page body.
	 *
	 * @return void
	 */
	public static function render_body() {
		$instance = new self();
		$instance->render();
	}

	/**
	 * Page body.
	 *
	 * @return void
	 */
	private function render() {
		$types     = EAP_TB_Types::all();
		$templates = array();
		$total     = 0;

		foreach ( $types as $slug => $type ) {
			$templates[ $slug ] = empty( $type['available'] ) ? array() : EAP_TB_Post_Type::get_templates( $slug );
			$total             += count( $templates[ $slug ] );
		}

		// Every action that comes back here acted on a template, so land on the
		// list rather than making the user find the tab again.
		$active = '' !== $this->get_message_key() ? 'templates' : 'create';
		?>
		<div class="eap-widgets-shell eap-tb">
			<div class="eap-widgets-head">
				<div>
					<h1><?php esc_html_e( 'Theme Builder', 'elementor-animatepro' ); ?></h1>
					<p>
						<?php
						echo esc_html(
							sprintf(
								/* translators: %d: number of templates. */
								_n( '%d Template', '%d Templates', $total, 'elementor-animatepro' ),
								$total
							)
						);
						?>
					</p>
				</div>
			</div>

			<?php $this->render_message(); ?>
			<?php $this->render_environment_notices(); ?>

			<div class="eap-tb-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Theme Builder sections', 'elementor-animatepro' ); ?>">
				<button type="button" id="eap-tb-tab-create" class="eap-tb-tab<?php echo 'create' === $active ? ' is-active' : ''; ?>" role="tab" aria-controls="eap-tb-panel-create" aria-selected="<?php echo 'create' === $active ? 'true' : 'false'; ?>" data-eap-tb-panel="create">
					<?php esc_html_e( 'Add New', 'elementor-animatepro' ); ?>
				</button>
				<button type="button" id="eap-tb-tab-templates" class="eap-tb-tab<?php echo 'templates' === $active ? ' is-active' : ''; ?>" role="tab" aria-controls="eap-tb-panel-templates" aria-selected="<?php echo 'templates' === $active ? 'true' : 'false'; ?>" data-eap-tb-panel="templates">
					<?php esc_html_e( 'My Templates', 'elementor-animatepro' ); ?>
					<span class="eap-tb-tab__count"><?php echo esc_html( number_format_i18n( $total ) ); ?></span>
				</button>
			</div>

			<div class="eap-tb-panel" id="eap-tb-panel-create" role="tabpanel" aria-labelledby="eap-tb-tab-create" data-eap-tb-panel-body="create" <?php echo 'create' === $active ? '' : 'hidden'; ?>>
			<div class="eap-tb-types">
				<?php foreach ( $types as $slug => $type ) : ?>
					<?php $available = ! empty( $type['available'] ); ?>
					<div class="eap-tb-type<?php echo $available ? '' : ' is-coming-soon'; ?>">
						<span class="eap-tb-type__icon dashicons <?php echo esc_attr( $type['icon'] ); ?>" aria-hidden="true"></span>
						<div class="eap-tb-type__body">
							<strong>
								<?php echo esc_html( $type['label'] ); ?>
								<?php if ( ! $available ) : ?>
									<span class="eap-widget-card__tag"><?php esc_html_e( 'Coming Soon', 'elementor-animatepro' ); ?></span>
								<?php endif; ?>
							</strong>
							<small><?php echo esc_html( $type['description'] ); ?></small>
						</div>
						<?php if ( $available ) : ?>
							<button type="button" class="eap-btn eap-btn--ghost" data-eap-tb-create="<?php echo esc_attr( $slug ); ?>">
								<?php esc_html_e( 'Add New', 'elementor-animatepro' ); ?>
							</button>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
			</div>

			<div class="eap-tb-panel" id="eap-tb-panel-templates" role="tabpanel" aria-labelledby="eap-tb-tab-templates" data-eap-tb-panel-body="templates" <?php echo 'templates' === $active ? '' : 'hidden'; ?>>
				<?php foreach ( EAP_TB_Types::available() as $slug => $type ) : ?>
					<section class="eap-tb-group">
						<div class="eap-tb-group__header">
							<h2><?php echo esc_html( $type['plural'] ); ?></h2>
							<button type="button" class="eap-btn eap-btn--primary" data-eap-tb-create="<?php echo esc_attr( $slug ); ?>">
								<?php esc_html_e( 'Add New', 'elementor-animatepro' ); ?>
							</button>
						</div>
						<?php if ( empty( $templates[ $slug ] ) ) : ?>
							<p class="eap-tb-empty">
								<?php
								echo esc_html(
									sprintf(
										/* translators: %s: plural template type, lowercase, e.g. headers. */
										__( 'No %s yet.', 'elementor-animatepro' ),
										strtolower( $type['plural'] )
									)
								);
								?>
							</p>
						<?php else : ?>
							<div class="eap-tb-rows">
								<?php foreach ( $templates[ $slug ] as $template ) : ?>
									<?php $this->render_row( $template ); ?>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</section>
				<?php endforeach; ?>
				<p class="eap-tb-no-match" data-eap-tb-no-match hidden><?php esc_html_e( 'No templates match your search.', 'elementor-animatepro' ); ?></p>
			</div>

			<?php $this->render_forms(); ?>
		</div>
		<?php
	}

	/**
	 * One template row.
	 *
	 * @param WP_Post $template Template post.
	 * @return void
	 */
	private function render_row( $template ) {
		$rules   = EAP_TB_Conditions::get( $template->ID );
		$enabled = EAP_TB_Post_Type::is_enabled( $template->ID );
		$summary = EAP_TB_Conditions::summarize( $rules );
		$orphan  = empty( $rules );
		?>
		<div class="eap-tb-row<?php echo $enabled ? '' : ' is-off'; ?>" data-eap-tb-template="<?php echo esc_attr( $template->ID ); ?>">
			<div class="eap-tb-row__main">
				<a class="eap-tb-row__name" href="<?php echo esc_url( EAP_TB_Post_Type::edit_url( $template->ID ) ); ?>">
					<?php echo esc_html( '' !== $template->post_title ? $template->post_title : __( '(no title)', 'elementor-animatepro' ) ); ?>
				</a>
				<small class="eap-tb-row__conditions<?php echo $orphan ? ' is-warning' : ''; ?>"><?php echo esc_html( $summary ); ?></small>
				<?php $preview = EAP_TB_Preview::summarize( $template->ID ); ?>
				<?php if ( '' !== $preview ) : ?>
					<small class="eap-tb-row__preview">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: the post, term or author a template previews against. */
								__( 'Previews against %s', 'elementor-animatepro' ),
								$preview
							)
						);
						?>
					</small>
				<?php endif; ?>
			</div>
			<div class="eap-tb-row__actions">
				<button type="button" class="eap-btn eap-btn--ghost" data-eap-tb-conditions="<?php echo esc_attr( $template->ID ); ?>">
					<?php esc_html_e( 'Conditions', 'elementor-animatepro' ); ?>
				</button>
				<?php if ( EAP_TB_Preview::supports( EAP_TB_Post_Type::get_type( $template->ID ) ) ) : ?>
					<button type="button" class="eap-btn eap-btn--ghost" data-eap-tb-preview="<?php echo esc_attr( $template->ID ); ?>">
						<?php esc_html_e( 'Preview', 'elementor-animatepro' ); ?>
					</button>
				<?php endif; ?>
				<a class="eap-btn eap-btn--ghost" href="<?php echo esc_url( EAP_TB_Post_Type::edit_url( $template->ID ) ); ?>">
					<?php esc_html_e( 'Edit', 'elementor-animatepro' ); ?>
				</a>
				<button type="button" class="eap-btn eap-btn--ghost" data-eap-tb-rename="<?php echo esc_attr( $template->ID ); ?>" data-eap-tb-name="<?php echo esc_attr( $template->post_title ); ?>">
					<?php esc_html_e( 'Rename', 'elementor-animatepro' ); ?>
				</button>
				<button type="button" class="eap-btn eap-btn--ghost" data-eap-tb-task="duplicate" data-eap-tb-id="<?php echo esc_attr( $template->ID ); ?>">
					<?php esc_html_e( 'Duplicate', 'elementor-animatepro' ); ?>
				</button>
				<button type="button" class="eap-btn eap-btn--ghost is-danger" data-eap-tb-task="delete" data-eap-tb-id="<?php echo esc_attr( $template->ID ); ?>" data-eap-tb-confirm="1">
					<?php esc_html_e( 'Delete', 'elementor-animatepro' ); ?>
				</button>
				<label class="eap-switch eap-tb-row__switch">
					<input type="checkbox" data-eap-tb-task="toggle" data-eap-tb-id="<?php echo esc_attr( $template->ID ); ?>" <?php checked( $enabled ); ?> />
					<span class="eap-switch__slider" aria-hidden="true"></span>
					<span class="screen-reader-text"><?php esc_html_e( 'Enable template', 'elementor-animatepro' ); ?></span>
				</label>
			</div>
		</div>
		<?php
	}

	/**
	 * The hidden forms and modals every row action posts through.
	 *
	 * @return void
	 */
	private function render_forms() {
		?>
		<form class="eap-tb-task-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-eap-tb-task-form hidden>
			<?php wp_nonce_field( self::NONCE ); ?>
			<input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>" />
			<input type="hidden" name="eap_tb_task" value="" />
			<input type="hidden" name="template_id" value="" />
			<input type="hidden" name="enabled" value="" />
		</form>

		<div class="eap-tb-modal" data-eap-tb-modal="name" hidden>
			<div class="eap-tb-modal__box" role="dialog" aria-modal="true" aria-labelledby="eap-tb-name-title">
				<h2 id="eap-tb-name-title" data-eap-tb-name-title><?php esc_html_e( 'Name your template', 'elementor-animatepro' ); ?></h2>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( self::NONCE ); ?>
					<input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>" />
					<input type="hidden" name="eap_tb_task" value="create" data-eap-tb-name-task />
					<input type="hidden" name="template_type" value="" data-eap-tb-name-type />
					<input type="hidden" name="template_id" value="" data-eap-tb-name-id />
					<label class="eap-tb-field">
						<span><?php esc_html_e( 'Template name', 'elementor-animatepro' ); ?></span>
						<input type="text" name="template_title" value="" required data-eap-tb-name-input />
					</label>
					<div class="eap-tb-modal__actions">
						<button type="button" class="eap-btn eap-btn--ghost" data-eap-tb-close><?php esc_html_e( 'Cancel', 'elementor-animatepro' ); ?></button>
						<button type="submit" class="eap-btn eap-btn--primary" data-eap-tb-name-submit><?php esc_html_e( 'Create and Edit', 'elementor-animatepro' ); ?></button>
					</div>
				</form>
			</div>
		</div>

		<div class="eap-tb-modal" data-eap-tb-modal="preview" hidden>
			<div class="eap-tb-modal__box" role="dialog" aria-modal="true" aria-labelledby="eap-tb-preview-title">
				<h2 id="eap-tb-preview-title"><?php esc_html_e( 'Preview Settings', 'elementor-animatepro' ); ?></h2>
				<p class="eap-tb-modal__subtitle" data-eap-tb-preview-name></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( self::NONCE ); ?>
					<input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>" />
					<input type="hidden" name="eap_tb_task" value="save_preview" />
					<input type="hidden" name="template_id" value="" data-eap-tb-preview-id />
					<p class="eap-tb-hint">
						<?php esc_html_e( 'Elementor has no post or archive to work from while you edit a template, so the dynamic widgets fall back to the newest post. Pick what this template should stand in for instead. It changes the editor only — never the live site.', 'elementor-animatepro' ); ?>
					</p>
					<label class="eap-tb-field">
						<span><?php esc_html_e( 'Preview against', 'elementor-animatepro' ); ?></span>
						<select name="preview_kind" data-eap-tb-preview-kind></select>
					</label>
					<div class="eap-tb-field">
						<span><?php esc_html_e( 'Which one', 'elementor-animatepro' ); ?></span>
						<div class="eap-tb-rule__value eap-tb-rule__value--search" data-eap-tb-preview-value></div>
					</div>
					<div class="eap-tb-modal__actions">
						<button type="button" class="eap-btn eap-btn--ghost" data-eap-tb-close><?php esc_html_e( 'Cancel', 'elementor-animatepro' ); ?></button>
						<button type="submit" class="eap-btn eap-btn--primary"><?php esc_html_e( 'Save Preview', 'elementor-animatepro' ); ?></button>
					</div>
				</form>
			</div>
		</div>

		<div class="eap-tb-modal" data-eap-tb-modal="conditions" hidden>
			<div class="eap-tb-modal__box eap-tb-modal__box--wide" role="dialog" aria-modal="true" aria-labelledby="eap-tb-conditions-title">
				<h2 id="eap-tb-conditions-title"><?php esc_html_e( 'Display Conditions', 'elementor-animatepro' ); ?></h2>
				<p class="eap-tb-modal__subtitle" data-eap-tb-conditions-name></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( self::NONCE ); ?>
					<input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>" />
					<input type="hidden" name="eap_tb_task" value="save_conditions" />
					<input type="hidden" name="template_id" value="" data-eap-tb-conditions-id />
					<div class="eap-tb-rules" data-eap-tb-rules></div>
					<button type="button" class="eap-btn eap-btn--ghost" data-eap-tb-add-rule><?php esc_html_e( 'Add Condition', 'elementor-animatepro' ); ?></button>
					<div class="eap-tb-modal__actions">
						<button type="button" class="eap-btn eap-btn--ghost" data-eap-tb-close><?php esc_html_e( 'Cancel', 'elementor-animatepro' ); ?></button>
						<button type="submit" class="eap-btn eap-btn--primary"><?php esc_html_e( 'Save Conditions', 'elementor-animatepro' ); ?></button>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * The message key the last action redirected back with.
	 *
	 * @return string
	 */
	private function get_message_key() {
		return isset( $_GET['eap-tb-msg'] ) ? sanitize_key( wp_unslash( $_GET['eap-tb-msg'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Show the result of the last action.
	 *
	 * @return void
	 */
	private function render_message() {
		$key = $this->get_message_key();

		$messages = array(
			'duplicated' => __( 'Template duplicated. The copy is switched off until you turn it on.', 'elementor-animatepro' ),
			'renamed'    => __( 'Template renamed.', 'elementor-animatepro' ),
			'deleted'    => __( 'Template deleted.', 'elementor-animatepro' ),
			'enabled'    => __( 'Template switched on.', 'elementor-animatepro' ),
			'disabled'   => __( 'Template switched off.', 'elementor-animatepro' ),
			'conditions' => __( 'Display conditions saved.', 'elementor-animatepro' ),
			'preview'    => __( 'Preview settings saved.', 'elementor-animatepro' ),
			'missing'    => __( 'That template no longer exists.', 'elementor-animatepro' ),
			'error'      => __( 'That did not work. Please try again.', 'elementor-animatepro' ),
		);

		if ( ! isset( $messages[ $key ] ) ) {
			return;
		}
		?>
		<div class="eap-admin-notice" data-eap-admin-notice>
			<span class="eap-admin-notice__icon" aria-hidden="true">✓</span>
			<div class="eap-admin-notice__content">
				<strong><?php echo esc_html( $messages[ $key ] ); ?></strong>
			</div>
		</div>
		<?php
	}

	/**
	 * Warn about things in this install that stop a template reaching the page.
	 *
	 * @return void
	 */
	private function render_environment_notices() {
		$warnings = array();

		if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			$warnings[] = __( 'Your active theme is a block theme. AnimatePro swaps the header and footer template parts, which works on most block themes but not all of them. If your template does not appear, a classic theme is the reliable option for now.', 'elementor-animatepro' );
		}

		if ( function_exists( 'hfe_header_enabled' ) ) {
			$warnings[] = __( 'Header Footer Elementor is active. Where it is already replacing the header or footer, AnimatePro stands down so the two plugins cannot both take over the page.', 'elementor-animatepro' );
		}

		if ( ! did_action( 'elementor/loaded' ) ) {
			$warnings[] = __( 'Elementor is not loaded, so templates cannot be edited or displayed.', 'elementor-animatepro' );
		}

		if ( empty( $warnings ) ) {
			return;
		}
		?>
		<div class="eap-tb-warnings">
			<?php foreach ( $warnings as $warning ) : ?>
				<p><span class="dashicons dashicons-info-outline" aria-hidden="true"></span><?php echo esc_html( $warning ); ?></p>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
