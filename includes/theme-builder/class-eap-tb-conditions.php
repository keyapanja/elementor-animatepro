<?php
/**
 * Theme Builder display conditions.
 *
 * A template carries a list of rules. Each rule is include or exclude, and each
 * one names a slice of the site. On the front end the resolver asks every
 * candidate template whether it matches the current request and how specific
 * that match is; the most specific include wins, and any matching exclude
 * removes the template from the running entirely.
 *
 * Rule shape: array( 'mode', 'scope', 'sub', 'value' ).
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_TB_Conditions {

	/**
	 * Post meta key holding the rule list.
	 *
	 * @var string
	 */
	const META_KEY = '_eap_template_conditions';

	/**
	 * Cached schema.
	 *
	 * @var array<string, array<string, mixed>>|null
	 */
	private static $schema = null;

	/**
	 * Rule schema: scope, then the sub-rules each scope offers.
	 *
	 * `value` names the kind of object the rule points at, which decides the
	 * control the editor shows. `weight` is the specificity used to pick a
	 * winner — higher beats lower.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function schema() {
		if ( null !== self::$schema ) {
			return self::$schema;
		}

		self::$schema = array(
			'entire'   => array(
				'label' => __( 'Entire Site', 'elementor-animatepro' ),
				'subs'  => array(
					'all' => array(
						'label'  => __( 'Entire Site', 'elementor-animatepro' ),
						'value'  => 'none',
						'weight' => 10,
					),
				),
			),
			'singular' => array(
				'label' => __( 'Single Content', 'elementor-animatepro' ),
				'subs'  => array(
					'all'       => array(
						'label'  => __( 'All Single Content', 'elementor-animatepro' ),
						'value'  => 'none',
						'weight' => 20,
					),
					'post_type' => array(
						'label'  => __( 'All of a Post Type', 'elementor-animatepro' ),
						'value'  => 'post_type',
						'weight' => 30,
					),
					'term'      => array(
						'label'  => __( 'Filed Under a Term', 'elementor-animatepro' ),
						'value'  => 'term',
						'weight' => 40,
					),
					'author'    => array(
						'label'  => __( 'Written by an Author', 'elementor-animatepro' ),
						'value'  => 'author',
						'weight' => 40,
					),
					'children'  => array(
						'label'  => __( 'Children of a Page', 'elementor-animatepro' ),
						'value'  => 'entry',
						'weight' => 45,
					),
					'entry'     => array(
						'label'  => __( 'A Specific Entry', 'elementor-animatepro' ),
						'value'  => 'entry',
						'weight' => 60,
					),
				),
			),
			'archive'  => array(
				'label' => __( 'Archives', 'elementor-animatepro' ),
				'subs'  => array(
					'all'       => array(
						'label'  => __( 'All Archives', 'elementor-animatepro' ),
						'value'  => 'none',
						'weight' => 20,
					),
					'post_type' => array(
						'label'  => __( 'A Post Type Archive', 'elementor-animatepro' ),
						'value'  => 'post_type',
						'weight' => 30,
					),
					'taxonomy'  => array(
						'label'  => __( 'A Whole Taxonomy', 'elementor-animatepro' ),
						'value'  => 'taxonomy',
						'weight' => 30,
					),
					'date'      => array(
						'label'  => __( 'Date Archives', 'elementor-animatepro' ),
						'value'  => 'none',
						'weight' => 30,
					),
					'author'    => array(
						'label'  => __( 'An Author Archive', 'elementor-animatepro' ),
						'value'  => 'author',
						'weight' => 40,
					),
					'blog'      => array(
						'label'  => __( 'Blog / Posts Page', 'elementor-animatepro' ),
						'value'  => 'none',
						'weight' => 40,
					),
					'term'      => array(
						'label'  => __( 'A Specific Term', 'elementor-animatepro' ),
						'value'  => 'term',
						'weight' => 50,
					),
				),
			),
			'special'  => array(
				'label' => __( 'Special Pages', 'elementor-animatepro' ),
				'subs'  => array(
					'front_page' => array(
						'label'  => __( 'Front Page', 'elementor-animatepro' ),
						'value'  => 'none',
						'weight' => 40,
					),
					'search'     => array(
						'label'  => __( 'Search Results', 'elementor-animatepro' ),
						'value'  => 'none',
						'weight' => 40,
					),
					'404'        => array(
						'label'  => __( '404 Page', 'elementor-animatepro' ),
						'value'  => 'none',
						'weight' => 40,
					),
				),
			),
		);

		return self::$schema;
	}

	/**
	 * Read a template's rules.
	 *
	 * @param int $template_id Template post ID.
	 * @return array<int, array<string, mixed>>
	 */
	public static function get( $template_id ) {
		$raw = get_post_meta( (int) $template_id, self::META_KEY, true );

		return self::sanitize( $raw );
	}

	/**
	 * Store a template's rules.
	 *
	 * @param int   $template_id Template post ID.
	 * @param mixed $rules       Raw rule list.
	 * @return array<int, array<string, mixed>> The sanitized list that was stored.
	 */
	public static function save( $template_id, $rules ) {
		$clean = self::sanitize( $rules );

		update_post_meta( (int) $template_id, self::META_KEY, $clean );

		return $clean;
	}

	/**
	 * Drop anything that is not a well-formed rule.
	 *
	 * @param mixed $raw Raw rule list.
	 * @return array<int, array<string, mixed>>
	 */
	public static function sanitize( $raw ) {
		if ( ! is_array( $raw ) ) {
			return array();
		}

		$schema = self::schema();
		$clean  = array();
		$seen   = array();

		foreach ( $raw as $rule ) {
			if ( ! is_array( $rule ) ) {
				continue;
			}

			$mode  = isset( $rule['mode'] ) ? sanitize_key( $rule['mode'] ) : 'include';
			$scope = isset( $rule['scope'] ) ? sanitize_key( $rule['scope'] ) : '';
			$sub   = isset( $rule['sub'] ) ? sanitize_key( $rule['sub'] ) : '';

			if ( ! isset( $schema[ $scope ]['subs'][ $sub ] ) ) {
				continue;
			}

			if ( 'exclude' !== $mode ) {
				$mode = 'include';
			}

			$value_type = $schema[ $scope ]['subs'][ $sub ]['value'];
			$value      = isset( $rule['value'] ) ? $rule['value'] : '';
			$value      = self::sanitize_value( $value_type, $value );

			if ( 'none' !== $value_type && '' === $value ) {
				continue;
			}

			$key = $mode . '|' . $scope . '|' . $sub . '|' . $value;
			if ( isset( $seen[ $key ] ) ) {
				continue;
			}
			$seen[ $key ] = true;

			$clean[] = array(
				'mode'  => $mode,
				'scope' => $scope,
				'sub'   => $sub,
				'value' => $value,
			);
		}

		return $clean;
	}

	/**
	 * Normalise a rule value for its kind.
	 *
	 * @param string $value_type Value kind.
	 * @param mixed  $value      Raw value.
	 * @return string
	 */
	private static function sanitize_value( $value_type, $value ) {
		switch ( $value_type ) {
			case 'none':
				return '';

			case 'post_type':
			case 'taxonomy':
				return sanitize_key( (string) $value );

			case 'entry':
			case 'author':
				$id = absint( $value );
				return $id ? (string) $id : '';

			case 'term':
				$parts = explode( ':', (string) $value );
				if ( 2 !== count( $parts ) ) {
					return '';
				}

				$taxonomy = sanitize_key( $parts[0] );
				$term_id  = absint( $parts[1] );

				return ( $taxonomy && $term_id ) ? $taxonomy . ':' . $term_id : '';
		}

		return '';
	}

	/**
	 * Score a template's rules against the current request.
	 *
	 * @param array<int, array<string, mixed>> $rules Rule list.
	 * @return int|false Specificity of the winning include, or false when the
	 *                   template does not apply here.
	 */
	public static function match( $rules ) {
		if ( empty( $rules ) ) {
			return false;
		}

		$best = false;

		foreach ( $rules as $rule ) {
			if ( ! self::rule_matches( $rule ) ) {
				continue;
			}

			if ( 'exclude' === $rule['mode'] ) {
				return false;
			}

			$weight = self::weight( $rule );
			if ( false === $best || $weight > $best ) {
				$best = $weight;
			}
		}

		return $best;
	}

	/**
	 * Specificity of one rule.
	 *
	 * @param array<string, mixed> $rule Rule.
	 * @return int
	 */
	public static function weight( $rule ) {
		$schema = self::schema();

		if ( ! isset( $schema[ $rule['scope'] ]['subs'][ $rule['sub'] ]['weight'] ) ) {
			return 0;
		}

		return (int) $schema[ $rule['scope'] ]['subs'][ $rule['sub'] ]['weight'];
	}

	/**
	 * Whether one rule describes the current request.
	 *
	 * @param array<string, mixed> $rule Rule.
	 * @return bool
	 */
	public static function rule_matches( $rule ) {
		$scope = $rule['scope'];
		$sub   = $rule['sub'];
		$value = $rule['value'];

		if ( 'entire' === $scope ) {
			return true;
		}

		if ( 'special' === $scope ) {
			switch ( $sub ) {
				case 'front_page':
					return is_front_page();
				case 'search':
					return is_search();
				case '404':
					return is_404();
			}

			return false;
		}

		if ( 'singular' === $scope ) {
			return self::singular_matches( $sub, $value );
		}

		if ( 'archive' === $scope ) {
			return self::archive_matches( $sub, $value );
		}

		return false;
	}

	/**
	 * Singular rules.
	 *
	 * @param string $sub   Sub-rule.
	 * @param string $value Rule value.
	 * @return bool
	 */
	private static function singular_matches( $sub, $value ) {
		if ( ! is_singular() ) {
			return false;
		}

		$post_id = (int) get_queried_object_id();
		if ( ! $post_id ) {
			return false;
		}

		switch ( $sub ) {
			case 'all':
				return true;

			case 'post_type':
				return is_singular( $value );

			case 'entry':
				return (int) $value === $post_id;

			case 'children':
				$parent = (int) $value;
				if ( ! $parent ) {
					return false;
				}

				return in_array( $parent, array_map( 'intval', (array) get_post_ancestors( $post_id ) ), true );

			case 'author':
				return (int) $value === (int) get_post_field( 'post_author', $post_id );

			case 'term':
				$parts = explode( ':', $value );
				if ( 2 !== count( $parts ) ) {
					return false;
				}

				return has_term( (int) $parts[1], $parts[0], $post_id );
		}

		return false;
	}

	/**
	 * Archive rules.
	 *
	 * The blog page is an archive for our purposes even though is_archive() is
	 * false there, so every branch that should cover it says so explicitly.
	 *
	 * @param string $sub   Sub-rule.
	 * @param string $value Rule value.
	 * @return bool
	 */
	private static function archive_matches( $sub, $value ) {
		switch ( $sub ) {
			case 'all':
				return is_archive() || is_home();

			case 'blog':
				return is_home();

			case 'date':
				return is_date();

			case 'author':
				return is_author( (int) $value );

			case 'post_type':
				if ( 'post' === $value ) {
					return is_home() || is_post_type_archive( 'post' );
				}

				return is_post_type_archive( $value );

			case 'taxonomy':
				if ( 'category' === $value ) {
					return is_category();
				}

				if ( 'post_tag' === $value ) {
					return is_tag();
				}

				return is_tax( $value );

			case 'term':
				$parts = explode( ':', $value );
				if ( 2 !== count( $parts ) ) {
					return false;
				}

				$taxonomy = $parts[0];
				$term_id  = (int) $parts[1];

				if ( 'category' === $taxonomy ) {
					return is_category( $term_id );
				}

				if ( 'post_tag' === $taxonomy ) {
					return is_tag( $term_id );
				}

				return is_tax( $taxonomy, $term_id );
		}

		return false;
	}

	/**
	 * One rule in plain words.
	 *
	 * @param array<string, mixed> $rule Rule.
	 * @return string
	 */
	public static function describe_rule( $rule ) {
		$schema = self::schema();

		if ( ! isset( $schema[ $rule['scope'] ]['subs'][ $rule['sub'] ] ) ) {
			return '';
		}

		$sub   = $schema[ $rule['scope'] ]['subs'][ $rule['sub'] ];
		$label = $sub['label'];
		$name  = self::value_label( $sub['value'], $rule['value'] );

		if ( '' !== $name ) {
			/* translators: 1: rule label, 2: the object it points at. */
			$label = sprintf( _x( '%1$s: %2$s', 'display condition', 'elementor-animatepro' ), $label, $name );
		}

		return $label;
	}

	/**
	 * The readable name of whatever a rule points at.
	 *
	 * @param string $value_type Value kind.
	 * @param string $value      Stored value.
	 * @return string
	 */
	public static function value_label( $value_type, $value ) {
		if ( 'none' === $value_type || '' === $value ) {
			return '';
		}

		switch ( $value_type ) {
			case 'post_type':
				$object = get_post_type_object( $value );
				return $object ? $object->labels->name : $value;

			case 'taxonomy':
				$object = get_taxonomy( $value );
				return $object ? $object->labels->name : $value;

			case 'entry':
				$title = get_the_title( (int) $value );
				return '' !== $title ? $title : sprintf( '#%d', (int) $value );

			case 'author':
				$user = get_userdata( (int) $value );
				return $user ? $user->display_name : sprintf( '#%d', (int) $value );

			case 'term':
				$parts = explode( ':', $value );
				if ( 2 !== count( $parts ) ) {
					return '';
				}

				$term = get_term( (int) $parts[1], $parts[0] );

				return ( $term && ! is_wp_error( $term ) ) ? $term->name : sprintf( '#%d', (int) $parts[1] );
		}

		return '';
	}

	/**
	 * A whole rule list in plain words, for the template list.
	 *
	 * @param array<int, array<string, mixed>> $rules Rule list.
	 * @return string
	 */
	public static function summarize( $rules ) {
		if ( empty( $rules ) ) {
			return __( 'Not displayed anywhere yet', 'elementor-animatepro' );
		}

		$includes = array();
		$excludes = array();

		foreach ( $rules as $rule ) {
			$text = self::describe_rule( $rule );
			if ( '' === $text ) {
				continue;
			}

			if ( 'exclude' === $rule['mode'] ) {
				$excludes[] = $text;
			} else {
				$includes[] = $text;
			}
		}

		if ( empty( $includes ) ) {
			return __( 'Not displayed anywhere yet', 'elementor-animatepro' );
		}

		$summary = implode( ', ', $includes );

		if ( ! empty( $excludes ) ) {
			/* translators: 1: where the template shows, 2: where it does not. */
			$summary = sprintf( __( '%1$s — except %2$s', 'elementor-animatepro' ), $summary, implode( ', ', $excludes ) );
		}

		return $summary;
	}
}
