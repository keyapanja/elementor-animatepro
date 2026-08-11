<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared query + card renderer for the Posts widget.
 *
 * Deliberately Elementor-free so it can be used from BOTH the widget's render()
 * and the admin-ajax load-more handler (EAP_Posts_Ajax) — the paged-in cards are
 * then byte-identical to the first render. All input is normalised through
 * sanitize_spec() / sanitize_display() so the untrusted AJAX payload and the
 * trusted widget settings run the exact same validated path.
 */
class EAP_Posts_Query {

	/**
	 * Whitelisted orderby values.
	 *
	 * @return string[]
	 */
	protected static function orderby_whitelist() {
		return array( 'date', 'title', 'menu_order', 'rand', 'comment_count', 'modified', 'ID' );
	}

	/* =====================================================================
	 * NORMALISATION
	 * ================================================================== */

	/**
	 * Normalise a raw query spec (from widget settings or the AJAX payload).
	 *
	 * @param array $raw Raw spec.
	 * @return array
	 */
	public static function sanitize_spec( $raw ) {
		$raw = is_array( $raw ) ? $raw : array();

		$source = isset( $raw['source'] ) ? $raw['source'] : 'latest';
		if ( ! in_array( $source, array( 'latest', 'manual', 'current' ), true ) ) {
			$source = 'latest';
		}

		$post_type = isset( $raw['post_type'] ) ? sanitize_key( $raw['post_type'] ) : 'post';
		if ( ! post_type_exists( $post_type ) ) {
			$post_type = 'post';
		}

		$orderby = isset( $raw['orderby'] ) ? $raw['orderby'] : 'date';
		if ( ! in_array( $orderby, self::orderby_whitelist(), true ) ) {
			$orderby = 'date';
		}

		return array(
			'source'          => $source,
			'post_type'       => $post_type,
			'per_page'        => isset( $raw['per_page'] ) ? max( 1, min( 48, (int) $raw['per_page'] ) ) : 6,
			'orderby'         => $orderby,
			'order'           => ( isset( $raw['order'] ) && 'ASC' === strtoupper( $raw['order'] ) ) ? 'ASC' : 'DESC',
			'offset'          => isset( $raw['offset'] ) ? max( 0, (int) $raw['offset'] ) : 0,
			'include_terms'   => self::int_list( isset( $raw['include_terms'] ) ? $raw['include_terms'] : array() ),
			'exclude_terms'   => self::int_list( isset( $raw['exclude_terms'] ) ? $raw['exclude_terms'] : array() ),
			'manual_ids'      => self::int_list( isset( $raw['manual_ids'] ) ? $raw['manual_ids'] : array() ),
			'exclude_current' => self::truthy( isset( $raw['exclude_current'] ) ? $raw['exclude_current'] : false ),
			'current_id'      => isset( $raw['current_id'] ) ? (int) $raw['current_id'] : 0,
			'ignore_sticky'   => self::truthy( isset( $raw['ignore_sticky'] ) ? $raw['ignore_sticky'] : false ),
		);
	}

	/**
	 * Normalise the display spec.
	 *
	 * @param array $raw Raw display settings.
	 * @return array
	 */
	public static function sanitize_display( $raw ) {
		$raw = is_array( $raw ) ? $raw : array();

		$layout = isset( $raw['layout'] ) ? $raw['layout'] : 'grid';
		if ( ! in_array( $layout, array( 'grid', 'list', 'overlay' ), true ) ) {
			$layout = 'grid';
		}

		$tag = isset( $raw['title_tag'] ) ? $raw['title_tag'] : 'h3';
		if ( ! in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ), true ) ) {
			$tag = 'h3';
		}

		$image_size = isset( $raw['image_size'] ) ? sanitize_key( $raw['image_size'] ) : 'medium_large';
		$sizes      = array_merge( get_intermediate_image_sizes(), array( 'full' ) );
		if ( ! in_array( $image_size, $sizes, true ) ) {
			$image_size = 'medium_large';
		}

		$meta_raw = isset( $raw['meta'] ) ? $raw['meta'] : array( 'date', 'comments' );
		if ( is_string( $meta_raw ) ) {
			$meta_raw = '' === trim( $meta_raw ) ? array() : explode( ',', $meta_raw );
		}
		$meta = array();
		if ( is_array( $meta_raw ) ) {
			foreach ( $meta_raw as $m ) {
				$m = sanitize_key( $m );
				if ( in_array( $m, array( 'author', 'date', 'comments' ), true ) ) {
					$meta[] = $m;
				}
			}
		}

		return array(
			'layout'         => $layout,
			'image_position' => ( isset( $raw['image_position'] ) && 'right' === $raw['image_position'] ) ? 'right' : 'left',
			'show_image'     => self::truthy_default( $raw, 'show_image', true ),
			'show_badge'     => self::truthy_default( $raw, 'show_badge', true ),
			'show_title'     => self::truthy_default( $raw, 'show_title', true ),
			'show_meta'      => self::truthy_default( $raw, 'show_meta', true ),
			'show_excerpt'   => self::truthy_default( $raw, 'show_excerpt', true ),
			'show_readmore'  => self::truthy_default( $raw, 'show_readmore', true ),
			'title_tag'      => $tag,
			'excerpt_length' => isset( $raw['excerpt_length'] ) ? max( 1, min( 100, (int) $raw['excerpt_length'] ) ) : 20,
			'meta'           => $meta,
			'image_size'     => $image_size,
			'readmore_text'  => isset( $raw['readmore_text'] ) && '' !== $raw['readmore_text'] ? sanitize_text_field( $raw['readmore_text'] ) : __( 'Read More', 'elementor-animatepro' ),
		);
	}

	/* =====================================================================
	 * QUERY
	 * ================================================================== */

	/**
	 * Build WP_Query args for a normalised spec (latest / manual sources).
	 * The `current` source uses the global query and does not call this.
	 *
	 * @param array $spec  Raw or normalised spec.
	 * @param int   $paged Page number.
	 * @return array
	 */
	public static function build_query_args( $spec, $paged = 1 ) {
		$spec  = self::sanitize_spec( $spec );
		$paged = max( 1, (int) $paged );

		if ( 'manual' === $spec['source'] && ! empty( $spec['manual_ids'] ) ) {
			return array(
				'post_type'           => 'any',
				'post_status'         => 'publish',
				'post__in'            => $spec['manual_ids'],
				'orderby'             => 'post__in',
				'posts_per_page'      => count( $spec['manual_ids'] ),
				'ignore_sticky_posts' => 1,
				'no_found_rows'       => true,
			);
		}

		$args = array(
			'post_type'           => $spec['post_type'],
			'post_status'         => 'publish',
			'posts_per_page'      => $spec['per_page'],
			'orderby'             => $spec['orderby'],
			'order'               => $spec['order'],
			'paged'               => $paged,
			'ignore_sticky_posts' => $spec['ignore_sticky'] ? 1 : 0,
		);

		// Offset + pagination: WP ignores `paged` when `offset` is set, so page
		// the offset manually. (max_num_pages can then over-count by the offset;
		// acceptable — offset is rarely combined with numbered pagination.)
		if ( $spec['offset'] > 0 ) {
			$args['offset'] = $spec['offset'] + ( ( $paged - 1 ) * $spec['per_page'] );
		}

		$tax_query = self::build_tax_query( $spec['include_terms'], $spec['exclude_terms'] );
		if ( $tax_query ) {
			$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		}

		if ( $spec['exclude_current'] && $spec['current_id'] ) {
			$args['post__not_in'] = array( $spec['current_id'] );
		}

		return $args;
	}

	/**
	 * Build a tax_query from include / exclude term IDs, grouped by taxonomy.
	 *
	 * @param int[] $include Term IDs to include (OR across taxonomies).
	 * @param int[] $exclude Term IDs to exclude.
	 * @return array
	 */
	protected static function build_tax_query( $include, $exclude ) {
		$clauses = array();

		$inc = self::group_terms( $include );
		if ( $inc ) {
			$sub = array();
			foreach ( $inc as $tax => $ids ) {
				$sub[] = array(
					'taxonomy' => $tax,
					'field'    => 'term_id',
					'terms'    => $ids,
					'operator' => 'IN',
				);
			}
			if ( count( $sub ) > 1 ) {
				$sub['relation'] = 'OR';
			}
			$clauses[] = 1 === count( $sub ) ? $sub[0] : $sub;
		}

		$exc = self::group_terms( $exclude );
		foreach ( $exc as $tax => $ids ) {
			$clauses[] = array(
				'taxonomy' => $tax,
				'field'    => 'term_id',
				'terms'    => $ids,
				'operator' => 'NOT IN',
			);
		}

		if ( ! $clauses ) {
			return array();
		}

		if ( count( $clauses ) > 1 ) {
			$clauses['relation'] = 'AND';
		}

		return $clauses;
	}

	/**
	 * Group term IDs by their taxonomy.
	 *
	 * @param int[] $ids Term IDs.
	 * @return array<string, int[]>
	 */
	protected static function group_terms( $ids ) {
		$by_tax = array();
		foreach ( $ids as $tid ) {
			$term = get_term( (int) $tid );
			if ( $term && ! is_wp_error( $term ) ) {
				$by_tax[ $term->taxonomy ][] = (int) $tid;
			}
		}
		return $by_tax;
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	/**
	 * Render all cards in a query.
	 *
	 * @param WP_Query $query   The query.
	 * @param array    $display Display spec.
	 * @return string
	 */
	public static function render_cards( $query, $display ) {
		if ( ! ( $query instanceof WP_Query ) || empty( $query->posts ) ) {
			return '';
		}

		$display = self::sanitize_display( $display );
		$out     = '';

		foreach ( $query->posts as $post ) {
			$post_id = is_object( $post ) ? (int) $post->ID : (int) $post;
			$out    .= self::render_card( $post_id, $display, true );
		}

		return $out;
	}

	/**
	 * Render a single post card.
	 *
	 * @param int   $post_id     Post ID.
	 * @param array $display     Display spec.
	 * @param bool  $pre_cleaned Whether $display is already sanitised.
	 * @return string
	 */
	public static function render_card( $post_id, $display, $pre_cleaned = false ) {
		if ( ! $pre_cleaned ) {
			$display = self::sanitize_display( $display );
		}

		$post_id   = (int) $post_id;
		$permalink = get_permalink( $post_id );
		$title     = get_the_title( $post_id );
		$tag       = $display['title_tag'];
		$overlay   = 'overlay' === $display['layout'];

		ob_start();
		?>
		<article class="eap-posts__item">
			<?php
			if ( $display['show_image'] ) :
				$thumb = get_the_post_thumbnail( $post_id, $display['image_size'], array( 'class' => 'eap-posts__img', 'loading' => 'lazy' ) );
				if ( $thumb || $overlay ) :
					?>
					<a class="eap-posts__image" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
						<?php
						if ( $thumb ) {
							echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_the_post_thumbnail() markup.
						} else {
							echo '<span class="eap-posts__img-placeholder"></span>';
						}
						if ( $display['show_badge'] && $overlay ) {
							echo self::get_badge( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
						}
						?>
					</a>
					<?php
				endif;
			endif;
			?>
			<div class="eap-posts__body">
				<?php
				if ( $display['show_badge'] && ! $overlay ) {
					echo self::get_badge( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
				}
				?>
				<?php if ( $display['show_title'] ) : ?>
					<<?php echo esc_html( $tag ); ?> class="eap-posts__title"><a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a></<?php echo esc_html( $tag ); ?>>
				<?php endif; ?>
				<?php
				if ( $display['show_meta'] ) {
					echo self::render_meta( $post_id, $display['meta'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
				}
				if ( $display['show_excerpt'] ) {
					$excerpt = self::get_excerpt( $post_id, $display['excerpt_length'] );
					if ( '' !== $excerpt ) {
						echo '<div class="eap-posts__excerpt"><p>' . esc_html( $excerpt ) . '</p></div>';
					}
				}
				?>
				<?php if ( $display['show_readmore'] ) : ?>
					<a class="eap-posts__readmore" href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $display['readmore_text'] ); ?></a>
				<?php endif; ?>
			</div>
		</article>
		<?php
		return ob_get_clean();
	}

	/**
	 * Meta row (author / date / comments).
	 *
	 * @param int      $post_id Post ID.
	 * @param string[] $meta    Meta keys in order.
	 * @return string
	 */
	protected static function render_meta( $post_id, $meta ) {
		$parts = array();

		foreach ( $meta as $key ) {
			switch ( $key ) {
				case 'author':
					$name = get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) );
					if ( '' !== $name ) {
						$parts[] = '<span class="eap-posts__meta-item eap-posts__meta-author">' . esc_html( $name ) . '</span>';
					}
					break;
				case 'date':
					$parts[] = '<span class="eap-posts__meta-item eap-posts__meta-date">' . esc_html( get_the_date( '', $post_id ) ) . '</span>';
					break;
				case 'comments':
					$count   = (int) get_comments_number( $post_id );
					/* translators: %s: number of comments. */
					$parts[] = '<span class="eap-posts__meta-item eap-posts__meta-comments">' . esc_html( sprintf( _n( '%s Comment', '%s Comments', $count, 'elementor-animatepro' ), number_format_i18n( $count ) ) ) . '</span>';
					break;
			}
		}

		if ( empty( $parts ) ) {
			return '';
		}

		return '<div class="eap-posts__meta">' . implode( '<span class="eap-posts__meta-sep" aria-hidden="true">·</span>', $parts ) . '</div>';
	}

	/**
	 * First term of the post as a linked badge (prefers `category`).
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	protected static function get_badge( $post_id ) {
		$post_type = get_post_type( $post_id );
		$taxes     = array_values( array_unique( array_merge( array( 'category' ), get_object_taxonomies( $post_type ) ) ) );

		foreach ( $taxes as $tax ) {
			if ( ! taxonomy_exists( $tax ) ) {
				continue;
			}
			$terms = get_the_terms( $post_id, $tax );
			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				$term = $terms[0];
				$url  = get_term_link( $term );
				if ( is_wp_error( $url ) ) {
					return '<span class="eap-posts__badge">' . esc_html( $term->name ) . '</span>';
				}
				return '<a class="eap-posts__badge" href="' . esc_url( $url ) . '">' . esc_html( $term->name ) . '</a>';
			}
		}

		return '';
	}

	/**
	 * Trimmed excerpt (manual excerpt if set, else from content).
	 *
	 * @param int $post_id Post ID.
	 * @param int $length  Word count.
	 * @return string
	 */
	protected static function get_excerpt( $post_id, $length ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}

		if ( '' !== trim( (string) $post->post_excerpt ) ) {
			return wp_trim_words( $post->post_excerpt, $length, '…' );
		}

		$raw = strip_shortcodes( wp_strip_all_tags( (string) $post->post_content ) );
		return wp_trim_words( $raw, $length, '…' );
	}

	/* =====================================================================
	 * OPTION BUILDERS (for the widget's controls)
	 * ================================================================== */

	/**
	 * Public post types (excluding attachment).
	 *
	 * @return array<string, string>
	 */
	public static function get_post_type_options() {
		$options = array();
		foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $type ) {
			if ( 'attachment' === $type->name ) {
				continue;
			}
			$options[ $type->name ] = $type->labels->singular_name;
		}
		return $options;
	}

	/**
	 * Terms across public taxonomies, labelled "Taxonomy: Term".
	 *
	 * @return array<int, string>
	 */
	public static function get_term_options() {
		$options = array();
		foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $tax ) {
			$terms = get_terms(
				array(
					'taxonomy'   => $tax->name,
					'hide_empty' => false,
					'number'     => 100,
				)
			);
			if ( is_wp_error( $terms ) ) {
				continue;
			}
			foreach ( $terms as $term ) {
				$options[ $term->term_id ] = $tax->labels->singular_name . ': ' . $term->name;
			}
		}
		return $options;
	}

	/**
	 * Recent posts across public types for the manual picker.
	 *
	 * @param int $limit Max posts.
	 * @return array<int, string>
	 */
	public static function get_post_options( $limit = 100 ) {
		$types = array_keys( self::get_post_type_options() );
		$posts = get_posts(
			array(
				'post_type'   => $types ? $types : 'post',
				'post_status' => 'publish',
				'numberposts' => $limit,
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);

		$options = array();
		foreach ( $posts as $post ) {
			$options[ $post->ID ] = $post->post_title ? $post->post_title : sprintf( '#%d', $post->ID );
		}
		return $options;
	}

	/* =====================================================================
	 * HELPERS
	 * ================================================================== */

	/**
	 * Coerce a value (array or CSV string) to a unique list of positive ints.
	 *
	 * @param mixed $value Value.
	 * @return int[]
	 */
	protected static function int_list( $value ) {
		if ( is_string( $value ) ) {
			$value = '' === trim( $value ) ? array() : explode( ',', $value );
		}
		if ( ! is_array( $value ) ) {
			return array();
		}
		$out = array();
		foreach ( $value as $item ) {
			$int = (int) $item;
			if ( $int > 0 ) {
				$out[] = $int;
			}
		}
		return array_values( array_unique( $out ) );
	}

	/**
	 * Interpret a value as a boolean (handles 'yes' / '1' / 1 / true).
	 *
	 * @param mixed $value Value.
	 * @return bool
	 */
	protected static function truthy( $value ) {
		return in_array( $value, array( true, 1, '1', 'yes', 'true' ), true );
	}

	/**
	 * Truthy read with a default when the key is absent.
	 *
	 * @param array  $raw     Raw array.
	 * @param string $key     Key.
	 * @param bool   $default Default when the key is missing.
	 * @return bool
	 */
	protected static function truthy_default( $raw, $key, $default ) {
		if ( ! array_key_exists( $key, $raw ) ) {
			return $default;
		}
		return self::truthy( $raw[ $key ] );
	}
}
