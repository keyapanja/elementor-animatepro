<?php
/**
 * What a template previews against while it is being edited.
 *
 * A Single template has no post and an Archive template has no archive when it
 * is opened in Elementor — the editor renders widgets over AJAX, where there is
 * no main query at all. Without this, a Post Title shows whatever the newest
 * post happens to be and a listing shows the latest posts, which makes a
 * template hard to design against.
 *
 * So each template can nominate a target. Two hooks carry it into the widgets:
 * `eap_editor_preview_post_id` for the dynamic widgets that stand in for one
 * post, and `eap_posts_query_args` for the listings.
 *
 * None of this touches the front end — a live request has a real query.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_TB_Preview {

	const META = '_eap_template_preview';

	/**
	 * Resolved post ID per template, for this request.
	 *
	 * @var array<int, int>
	 */
	private static $resolved = array();

	/**
	 * Hook into the widgets.
	 */
	public function __construct() {
		add_filter( 'eap_editor_preview_post_id', array( $this, 'preview_post_id' ) );
		add_filter( 'eap_posts_query_args', array( $this, 'preview_query_args' ), 10, 2 );
	}

	/**
	 * What a type is allowed to preview against.
	 *
	 * @param string $type Template type slug.
	 * @return array<string, array<string, string>>
	 */
	public static function kinds_for( $type ) {
		if ( 'search' === $type ) {
			return array(
				'search_term' => array(
					'label' => __( 'A Search Term', 'elementor-animatepro' ),
					'value' => 'text',
				),
			);
		}

		if ( 'archive' === $type ) {
			return array(
				'term'      => array(
					'label' => __( 'A Term', 'elementor-animatepro' ),
					'value' => 'term',
				),
				'author'    => array(
					'label' => __( 'An Author', 'elementor-animatepro' ),
					'value' => 'author',
				),
				'post_type' => array(
					'label' => __( 'A Post Type', 'elementor-animatepro' ),
					'value' => 'post_type',
				),
			);
		}

		return array(
			'entry' => array(
				'label' => __( 'A Specific Entry', 'elementor-animatepro' ),
				'value' => 'entry',
			),
		);
	}

	/**
	 * Whether a template type can have a preview target at all.
	 *
	 * A header or footer shows the same thing everywhere, so it has nothing to
	 * preview against.
	 *
	 * @param string $type Template type slug.
	 * @return bool
	 */
	public static function supports( $type ) {
		return in_array( $type, array( 'single', 'archive', 'search', 'loop-item' ), true );
	}

	/**
	 * A template's stored target.
	 *
	 * @param int $template_id Template ID.
	 * @return array{kind: string, value: string}
	 */
	public static function get( $template_id ) {
		$raw = get_post_meta( (int) $template_id, self::META, true );

		return self::sanitize( $raw, EAP_TB_Post_Type::get_type( $template_id ) );
	}

	/**
	 * Store a template's target.
	 *
	 * @param int   $template_id Template ID.
	 * @param mixed $raw         Raw target.
	 * @return array{kind: string, value: string}
	 */
	public static function save( $template_id, $raw ) {
		$clean = self::sanitize( $raw, EAP_TB_Post_Type::get_type( $template_id ) );

		update_post_meta( (int) $template_id, self::META, $clean );
		unset( self::$resolved[ (int) $template_id ] );

		return $clean;
	}

	/**
	 * Keep only a target this template type can actually use.
	 *
	 * @param mixed  $raw  Raw target.
	 * @param string $type Template type slug.
	 * @return array{kind: string, value: string}
	 */
	public static function sanitize( $raw, $type ) {
		$empty = array(
			'kind'  => '',
			'value' => '',
		);

		if ( ! is_array( $raw ) || empty( $raw['kind'] ) ) {
			return $empty;
		}

		$kind  = sanitize_key( $raw['kind'] );
		$kinds = self::kinds_for( $type );

		if ( ! isset( $kinds[ $kind ] ) ) {
			return $empty;
		}

		$value = isset( $raw['value'] ) ? (string) $raw['value'] : '';

		// Sanitise by the kind of thing the value points at, not by the rule
		// name, so a new kind only has to declare which control it uses.
		switch ( $kinds[ $kind ]['value'] ) {
			case 'post_type':
			case 'taxonomy':
				$value = sanitize_key( $value );
				break;

			case 'term':
				$parts = explode( ':', $value );
				$value = ( 2 === count( $parts ) && sanitize_key( $parts[0] ) && absint( $parts[1] ) )
					? sanitize_key( $parts[0] ) . ':' . absint( $parts[1] )
					: '';
				break;

			case 'text':
				$value = sanitize_text_field( $value );
				break;

			default:
				$value = absint( $value ) ? (string) absint( $value ) : '';
				break;
		}

		if ( '' === $value ) {
			return $empty;
		}

		return array(
			'kind'  => $kind,
			'value' => $value,
		);
	}

	/**
	 * A target in plain words, for the template list.
	 *
	 * @param int $template_id Template ID.
	 * @return string
	 */
	public static function summarize( $template_id ) {
		$target = self::get( $template_id );

		if ( '' === $target['kind'] ) {
			return '';
		}

		$kinds = self::kinds_for( EAP_TB_Post_Type::get_type( $template_id ) );

		if ( 'text' === $kinds[ $target['kind'] ]['value'] ) {
			return '"' . $target['value'] . '"';
		}

		return EAP_TB_Conditions::value_label( $kinds[ $target['kind'] ]['value'], $target['value'] );
	}

	/**
	 * The template currently open in the editor, if it is one of ours.
	 *
	 * @return int
	 */
	private function editing_template_id() {
		if ( ! class_exists( '\Elementor\Plugin' ) || ! isset( \Elementor\Plugin::$instance->editor ) ) {
			return 0;
		}

		$post_id = (int) \Elementor\Plugin::$instance->editor->get_post_id();

		if ( ! $post_id || EAP_TB_Post_Type::POST_TYPE !== get_post_type( $post_id ) ) {
			return 0;
		}

		return $post_id;
	}

	/**
	 * Stand in for a real post while a Single template is being edited.
	 *
	 * @param int $post_id Incoming resolution.
	 * @return int
	 */
	public function preview_post_id( $post_id ) {
		if ( $post_id ) {
			return $post_id;
		}

		$template_id = $this->editing_template_id();

		if ( ! $template_id ) {
			return $post_id;
		}

		if ( isset( self::$resolved[ $template_id ] ) ) {
			return self::$resolved[ $template_id ];
		}

		self::$resolved[ $template_id ] = $this->resolve_post( $template_id );

		return self::$resolved[ $template_id ];
	}

	/**
	 * Find a post that represents the template's target.
	 *
	 * For an archive target that means a post from inside it, so the dynamic
	 * widgets in a listing have something real to show.
	 *
	 * @param int $template_id Template ID.
	 * @return int
	 */
	private function resolve_post( $template_id ) {
		$target = self::get( $template_id );

		if ( '' === $target['kind'] ) {
			return 0;
		}

		if ( 'entry' === $target['kind'] ) {
			return (int) $target['value'];
		}

		$args = array(
			'posts_per_page'   => 1,
			'post_status'      => 'publish',
			'fields'           => 'ids',
			'suppress_filters' => false,
		);

		if ( 'search_term' === $target['kind'] ) {
			$args['s'] = $target['value'];
		} elseif ( 'author' === $target['kind'] ) {
			$args['author'] = (int) $target['value'];
		} elseif ( 'post_type' === $target['kind'] ) {
			$args['post_type'] = $target['value'];
		} elseif ( 'term' === $target['kind'] ) {
			$parts = explode( ':', $target['value'] );

			$args['post_type'] = 'any';
			$args['tax_query']  = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => $parts[0],
					'field'    => 'term_id',
					'terms'    => array( (int) $parts[1] ),
				),
			);
		}

		$found = get_posts( $args );

		return ! empty( $found ) ? (int) $found[0] : 0;
	}

	/**
	 * Point a listing at the archive its template previews against.
	 *
	 * @param array $args Query arguments.
	 * @param array $spec Query spec.
	 * @return array
	 */
	public function preview_query_args( $args, $spec ) {
		unset( $spec );

		$template_id = $this->editing_template_id();

		if ( ! $template_id ) {
			return $args;
		}

		if ( ! in_array( EAP_TB_Post_Type::get_type( $template_id ), array( 'archive', 'search' ), true ) ) {
			return $args;
		}

		$target = self::get( $template_id );

		if ( '' === $target['kind'] ) {
			return $args;
		}

		if ( 'search_term' === $target['kind'] ) {
			$args['s'] = $target['value'];
		} elseif ( 'author' === $target['kind'] ) {
			$args['author'] = (int) $target['value'];
		} elseif ( 'post_type' === $target['kind'] ) {
			$args['post_type'] = $target['value'];
		} elseif ( 'term' === $target['kind'] ) {
			$parts = explode( ':', $target['value'] );

			$args['post_type'] = 'any';
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => $parts[0],
					'field'    => 'term_id',
					'terms'    => array( (int) $parts[1] ),
				),
			);
		}

		return $args;
	}
}
