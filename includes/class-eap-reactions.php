<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reactions backend for the Post Reactions widget.
 *
 * Stores per-post reaction counts in the `_eap_reactions` post-meta array and
 * handles the nonce-protected AJAX endpoint (`eap_react`) used to add / remove /
 * switch a visitor's reaction. Registered on bootstrap so the endpoint exists on
 * admin-ajax.php regardless of Elementor. One-reaction-per-visitor is enforced
 * client-side (localStorage); the server only applies clamped deltas, plus a
 * light per-IP rate limit to blunt trivial abuse (perfect anonymous dedupe is
 * not possible without login, which is the chosen "no login" model).
 */
class EAP_Reactions {

	const META_KEY = '_eap_reactions';

	/**
	 * Constructor — register the AJAX endpoint for logged-in and guest users.
	 */
	public function __construct() {
		add_action( 'wp_ajax_eap_react', array( $this, 'handle' ) );
		add_action( 'wp_ajax_nopriv_eap_react', array( $this, 'handle' ) );
	}

	/**
	 * Canonical reaction set (key => emoji + label). The widget shows a chosen
	 * subset; the server accepts any key in this set.
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function get_reactions() {
		return array(
			'like'  => array( 'emoji' => '👍', 'label' => __( 'Like', 'elementor-animatepro' ) ),
			'love'  => array( 'emoji' => '❤️', 'label' => __( 'Love', 'elementor-animatepro' ) ),
			'party' => array( 'emoji' => '🎉', 'label' => __( 'Celebrate', 'elementor-animatepro' ) ),
			'haha'  => array( 'emoji' => '😂', 'label' => __( 'Haha', 'elementor-animatepro' ) ),
			'wow'   => array( 'emoji' => '😮', 'label' => __( 'Wow', 'elementor-animatepro' ) ),
			'sad'   => array( 'emoji' => '😢', 'label' => __( 'Sad', 'elementor-animatepro' ) ),
			'angry' => array( 'emoji' => '😠', 'label' => __( 'Angry', 'elementor-animatepro' ) ),
			'clap'  => array( 'emoji' => '👏', 'label' => __( 'Clap', 'elementor-animatepro' ) ),
			'fire'  => array( 'emoji' => '🔥', 'label' => __( 'Fire', 'elementor-animatepro' ) ),
		);
	}

	/**
	 * Get the stored counts for a post as an int-keyed map.
	 *
	 * @param int $post_id Post ID.
	 * @return array<string, int>
	 */
	public static function get_counts( $post_id ) {
		$counts = get_post_meta( (int) $post_id, self::META_KEY, true );

		if ( ! is_array( $counts ) ) {
			return array();
		}

		$clean = array();
		foreach ( $counts as $key => $value ) {
			$clean[ sanitize_key( $key ) ] = max( 0, (int) $value );
		}

		return $clean;
	}

	/**
	 * AJAX handler: apply a reaction delta and return the fresh counts.
	 *
	 * @return void
	 */
	public function handle() {
		check_ajax_referer( 'eap-react', 'nonce' );

		$post_id  = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
		$reaction = isset( $_POST['reaction'] ) ? sanitize_key( wp_unslash( $_POST['reaction'] ) ) : '';
		$op       = isset( $_POST['op'] ) ? sanitize_key( wp_unslash( $_POST['op'] ) ) : 'add';
		$from     = isset( $_POST['from'] ) ? sanitize_key( wp_unslash( $_POST['from'] ) ) : '';

		$allowed = self::get_reactions();

		if ( ! $post_id || ! get_post( $post_id ) || ! isset( $allowed[ $reaction ] ) ) {
			wp_send_json_error( array( 'message' => 'invalid_request' ), 400 );
		}

		if ( ! in_array( $op, array( 'add', 'remove', 'switch' ), true ) ) {
			$op = 'add';
		}

		if ( $this->is_rate_limited() ) {
			wp_send_json_error( array( 'message' => 'rate_limited' ), 429 );
		}

		$counts = self::get_counts( $post_id );
		$get    = static function ( $key ) use ( $counts ) {
			return isset( $counts[ $key ] ) ? (int) $counts[ $key ] : 0;
		};

		if ( 'remove' === $op ) {
			$counts[ $reaction ] = max( 0, $get( $reaction ) - 1 );
		} elseif ( 'switch' === $op && isset( $allowed[ $from ] ) ) {
			$counts[ $from ]     = max( 0, $get( $from ) - 1 );
			$counts[ $reaction ] = $get( $reaction ) + 1;
		} else {
			$counts[ $reaction ] = $get( $reaction ) + 1;
		}

		update_post_meta( $post_id, self::META_KEY, $counts );

		wp_send_json_success( array( 'counts' => $counts ) );
	}

	/**
	 * Light per-IP rate limit: cap reaction writes per minute.
	 *
	 * @return bool True when the caller has exceeded the window.
	 */
	protected function is_rate_limited() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

		if ( '' === $ip ) {
			return false;
		}

		$key   = 'eap_react_rl_' . md5( $ip );
		$count = (int) get_transient( $key );

		if ( $count >= 40 ) {
			return true;
		}

		set_transient( $key, $count + 1, MINUTE_IN_SECONDS );

		return false;
	}
}
