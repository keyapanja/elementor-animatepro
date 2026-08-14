<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared rating store for the Post Rating / Post Rating Form widgets.
 *
 * Defines the per-post meta contract — a running sum of submitted ratings and a
 * vote count — so the average can be derived. The Post Rating widget (display)
 * reads it here; the future Post Rating Form (input) will write to the SAME keys
 * via a submit()/AJAX method added to this class, keeping the two widgets in
 * sync. Kept as a small static reader for now (no hooks).
 */
class EAP_Rating {

	const SUM_KEY   = '_eap_rating_sum';
	const COUNT_KEY = '_eap_rating_count';

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
}
