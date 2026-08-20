<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Backend for the Posts Read Later widget (list mode).
 *
 * The visitor's saved post IDs live only in their browser (localStorage), so
 * the reading list is built client-side: read-later.js POSTs the saved IDs to
 * the `eap_read_later` endpoint, which returns the same server-rendered cards
 * (via EAP_Posts_Query) each wrapped with a remove control. Registered on
 * bootstrap so the endpoint exists on admin-ajax.php independently of Elementor.
 *
 * The IDs are run through EAP_Posts_Query's `manual`/`post__in` path, which
 * forces `post_status=publish` — so an arbitrary ID list can only ever surface
 * public, published posts (no private-data risk). `valid_ids` is returned so the
 * client can prune saved IDs that are no longer published.
 */
class EAP_Read_Later {

	/**
	 * Constructor — register the endpoint for logged-in and guest users.
	 */
	public function __construct() {
		add_action( 'wp_ajax_eap_read_later', array( $this, 'handle' ) );
		add_action( 'wp_ajax_nopriv_eap_read_later', array( $this, 'handle' ) );
	}

	/**
	 * AJAX handler: render the cards for the visitor's saved IDs.
	 *
	 * @return void
	 */
	public function handle() {
		check_ajax_referer( 'eap-read-later', 'nonce' );

		if ( ! class_exists( 'EAP_Posts_Query' ) ) {
			require_once EAP_PATH . 'includes/class-eap-posts-query.php';
		}

		$ids_raw = isset( $_POST['ids'] ) ? json_decode( wp_unslash( $_POST['ids'] ), true ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Cast to positive ints below.
		$display = isset( $_POST['display'] ) ? json_decode( wp_unslash( $_POST['display'] ), true ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Re-sanitised by EAP_Posts_Query::sanitize_display().

		if ( ! is_array( $ids_raw ) ) {
			$ids_raw = array();
		}
		if ( ! is_array( $display ) ) {
			$display = array();
		}

		$ids = array();
		foreach ( $ids_raw as $value ) {
			$id = (int) $value;
			if ( $id > 0 ) {
				$ids[] = $id;
			}
		}
		$ids = array_slice( array_values( array_unique( $ids ) ), 0, 100 );

		if ( empty( $ids ) ) {
			wp_send_json_success(
				array(
					'html'      => '',
					'valid_ids' => array(),
				)
			);
		}

		$args  = EAP_Posts_Query::build_query_args(
			array(
				'source'     => 'manual',
				'manual_ids' => $ids,
			),
			1
		);
		$query = new WP_Query( $args );

		$valid = array();
		$html  = '';
		foreach ( $query->posts as $post ) {
			$post_id = is_object( $post ) ? (int) $post->ID : (int) $post;
			$valid[] = $post_id;
			$html   .= self::render_item( $post_id, $display );
		}

		wp_reset_postdata();

		wp_send_json_success(
			array(
				'html'      => $html,
				'valid_ids' => $valid,
			)
		);
	}

	/**
	 * Wrap one post card with the remove control.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $display Display spec (sanitised by render_card).
	 * @return string
	 */
	public static function render_item( $post_id, $display ) {
		if ( ! class_exists( 'EAP_Posts_Query' ) ) {
			require_once EAP_PATH . 'includes/class-eap-posts-query.php';
		}

		$post_id = (int) $post_id;
		$card    = EAP_Posts_Query::render_card( $post_id, $display );
		$label   = esc_attr__( 'Remove from list', 'elementor-animatepro' );

		return '<div class="eap-read-later__item">'
			. '<button type="button" class="eap-read-later__remove" data-post="' . esc_attr( (string) $post_id ) . '" aria-label="' . $label . '"><span aria-hidden="true">&times;</span></button>'
			. $card // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_card escapes internally.
			. '</div>';
	}

	/**
	 * Render several saved-post cards (used by the widget's editor sample).
	 *
	 * @param int[] $ids     Post IDs.
	 * @param array $display Display spec.
	 * @return string
	 */
	public static function render_items( $ids, $display ) {
		$out = '';
		foreach ( (array) $ids as $id ) {
			$out .= self::render_item( (int) $id, $display );
		}
		return $out;
	}
}
