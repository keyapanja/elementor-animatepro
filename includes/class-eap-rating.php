<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared rating store + submission backend for the Post Rating / Post Rating
 * Form widgets.
 *
 * Per-post meta contract: a running sum of submitted ratings and a vote count,
 * so the average is sum / count. Post Rating (display) reads it via get_stats();
 * Post Rating Form (input) submits through the nonce-protected AJAX endpoint
 * (`eap_submit_rating`), which is registered on bootstrap so it exists on
 * admin-ajax.php independently of Elementor — same standalone-handler pattern as
 * EAP_Reactions / EAP_Posts_Ajax. One vote per visitor is enforced client-side
 * (localStorage) plus a light per-IP-per-post transient here; perfect anonymous
 * dedupe is impossible without login, which is the chosen "no login" model.
 */
class EAP_Rating {

	const SUM_KEY   = '_eap_rating_sum';
	const COUNT_KEY = '_eap_rating_count';

	/**
	 * Hard cap on a single submitted rating value (widgets use <= this).
	 */
	const MAX_VALUE = 10;

	/**
	 * How long a single IP is treated as "already voted" for a post.
	 */
	const GUARD_TTL = DAY_IN_SECONDS;

	/**
	 * Constructor — register the submission endpoint.
	 */
	public function __construct() {
		add_action( 'wp_ajax_eap_submit_rating', array( $this, 'handle' ) );
		add_action( 'wp_ajax_nopriv_eap_submit_rating', array( $this, 'handle' ) );
	}

	/**
	 * Aggregate rating stats for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return array{average:float,count:int}
	 */
	public static function get_stats( $post_id ) {
		$post_id = (int) $post_id;
		$count   = (int) get_post_meta( $post_id, self::COUNT_KEY, true );
		$sum     = (float) get_post_meta( $post_id, self::SUM_KEY, true );

		$average = $count > 0 ? ( $sum / $count ) : 0.0;

		return array(
			'average' => $average,
			'count'   => $count,
		);
	}

	/**
	 * AJAX handler: record a vote and return the fresh stats.
	 *
	 * @return void
	 */
	public function handle() {
		check_ajax_referer( 'eap-rating', 'nonce' );

		$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
		$rating  = isset( $_POST['rating'] ) ? (int) round( (float) wp_unslash( $_POST['rating'] ) ) : 0;

		if ( ! $post_id || ! get_post( $post_id ) || $rating < 1 ) {
			wp_send_json_error( array( 'message' => 'invalid_request' ), 400 );
		}

		$rating = min( self::MAX_VALUE, max( 1, $rating ) );

		// Per-IP-per-post guard: if this IP already voted, return the current
		// stats unchanged rather than erroring (client then locks its UI).
		$guard_key = $this->guard_key( $post_id );
		if ( '' !== $guard_key && get_transient( $guard_key ) ) {
			$stats = self::get_stats( $post_id );
			wp_send_json_success(
				array(
					'average' => round( $stats['average'], 2 ),
					'count'   => $stats['count'],
					'already' => true,
				)
			);
		}

		$count = (int) get_post_meta( $post_id, self::COUNT_KEY, true );
		$sum   = (float) get_post_meta( $post_id, self::SUM_KEY, true );

		$count++;
		$sum += $rating;

		update_post_meta( $post_id, self::COUNT_KEY, $count );
		update_post_meta( $post_id, self::SUM_KEY, $sum );

		if ( '' !== $guard_key ) {
			set_transient( $guard_key, 1, self::GUARD_TTL );
		}

		wp_send_json_success(
			array(
				'average' => round( $count > 0 ? $sum / $count : 0, 2 ),
				'count'   => $count,
				'already' => false,
			)
		);
	}

	/**
	 * Transient key for the per-IP-per-post vote guard.
	 *
	 * @param int $post_id Post ID.
	 * @return string Empty when the IP is unknown.
	 */
	protected function guard_key( $post_id ) {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

		if ( '' === $ip ) {
			return '';
		}

		return 'eap_rated_' . (int) $post_id . '_' . md5( $ip );
	}
}
