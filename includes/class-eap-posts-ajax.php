<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load-more / infinite-scroll backend for the Posts widget.
 *
 * Registered on bootstrap so `eap_load_posts` exists on admin-ajax.php
 * independently of Elementor. Receives the compact query spec + display spec
 * (JSON) the widget stamped on its wrapper, plus the next page number; forces
 * the source to `latest` (manual/current do not AJAX-paginate), rebuilds the
 * query through the shared EAP_Posts_Query and returns the rendered cards so the
 * appended markup is identical to the first render. All input is re-sanitised by
 * EAP_Posts_Query — the payload can only surface public, published posts.
 */
class EAP_Posts_Ajax {

	/**
	 * Constructor — register the endpoint for logged-in and guest users.
	 */
	public function __construct() {
		add_action( 'wp_ajax_eap_load_posts', array( $this, 'handle' ) );
		add_action( 'wp_ajax_nopriv_eap_load_posts', array( $this, 'handle' ) );
	}

	/**
	 * AJAX handler: render the next page of cards.
	 *
	 * @return void
	 */
	public function handle() {
		check_ajax_referer( 'eap-load-posts', 'nonce' );

		if ( ! class_exists( 'EAP_Posts_Query' ) ) {
			require_once EAP_PATH . 'includes/class-eap-posts-query.php';
		}

		$page = isset( $_POST['page'] ) ? max( 1, (int) $_POST['page'] ) : 1;

		$spec    = isset( $_POST['spec'] ) ? json_decode( wp_unslash( $_POST['spec'] ), true ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Re-sanitised by EAP_Posts_Query::sanitize_spec().
		$display = isset( $_POST['display'] ) ? json_decode( wp_unslash( $_POST['display'] ), true ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Re-sanitised by EAP_Posts_Query::sanitize_display().

		if ( ! is_array( $spec ) ) {
			$spec = array();
		}
		if ( ! is_array( $display ) ) {
			$display = array();
		}

		// Only the Latest source paginates over AJAX.
		$spec['source'] = 'latest';

		$args  = EAP_Posts_Query::build_query_args( $spec, $page );
		$query = new WP_Query( $args );

		$html = EAP_Posts_Query::render_cards( $query, $display );
		$max  = (int) $query->max_num_pages;

		wp_reset_postdata();

		wp_send_json_success(
			array(
				'html'      => $html,
				'has_more'  => $page < $max,
				'max_pages' => $max,
			)
		);
	}
}
