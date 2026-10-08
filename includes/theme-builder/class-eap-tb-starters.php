<?php
/**
 * Starter layouts.
 *
 * A starter is an export file that ships with the plugin, so it travels through
 * exactly the same import path a user's own file does — no second format, and
 * no second set of sanitisers to keep in step.
 *
 * Starters carry no display conditions on purpose: where a header belongs is
 * the site's decision, not the layout's.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_TB_Starters {

	/**
	 * Where the bundled files live, relative to the plugin.
	 */
	const DIR = 'includes/theme-builder/starters/';

	/**
	 * Cached index.
	 *
	 * @var array<string, array<string, string>>|null
	 */
	private static $index = null;

	/**
	 * Every starter, keyed by slug.
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function all() {
		if ( null !== self::$index ) {
			return self::$index;
		}

		self::$index = array();

		$files = glob( EAP_PATH . self::DIR . '*.json' );

		if ( empty( $files ) ) {
			return self::$index;
		}

		foreach ( $files as $file ) {
			$slug = sanitize_key( basename( $file, '.json' ) );
			$raw  = self::read( $slug );

			if ( is_wp_error( $raw ) || empty( $raw['type'] ) ) {
				continue;
			}

			self::$index[ $slug ] = array(
				'slug'  => $slug,
				'type'  => sanitize_key( $raw['type'] ),
				'title' => isset( $raw['title'] ) ? (string) $raw['title'] : $slug,
			);
		}

		return self::$index;
	}

	/**
	 * The starters offered for one template type.
	 *
	 * @param string $type Template type slug.
	 * @return array<string, string> Slug => label.
	 */
	public static function for_type( $type ) {
		$options = array();

		foreach ( self::all() as $slug => $starter ) {
			if ( $starter['type'] === $type ) {
				$options[ $slug ] = $starter['title'];
			}
		}

		return $options;
	}

	/**
	 * Read one starter file.
	 *
	 * @param string $slug Starter slug.
	 * @return array<string, mixed>|WP_Error
	 */
	public static function read( $slug ) {
		$slug = sanitize_key( $slug );
		$path = EAP_PATH . self::DIR . $slug . '.json';

		// sanitize_key() already rules out traversal; this makes sure the file
		// really is one of ours rather than trusting that alone.
		if ( '' === $slug || ! file_exists( $path ) || 0 !== strpos( realpath( $path ), realpath( EAP_PATH . self::DIR ) ) ) {
			return new WP_Error( 'eap_tb_no_starter', __( 'That starter layout is not available.', 'elementor-animatepro' ) );
		}

		$contents = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		if ( false === $contents ) {
			return new WP_Error( 'eap_tb_no_starter', __( 'That starter layout could not be read.', 'elementor-animatepro' ) );
		}

		$decoded = json_decode( $contents, true );

		if ( ! is_array( $decoded ) ) {
			return new WP_Error( 'eap_tb_no_starter', __( 'That starter layout is damaged.', 'elementor-animatepro' ) );
		}

		return $decoded;
	}

	/**
	 * Create a template from a starter.
	 *
	 * @param string $slug  Starter slug.
	 * @param string $type  The type the user asked for.
	 * @param string $title The name the user gave it.
	 * @return int|WP_Error
	 */
	public static function create( $slug, $type, $title ) {
		$raw = self::read( $slug );

		if ( is_wp_error( $raw ) ) {
			return $raw;
		}

		// A starter for one type must not be used to make another.
		if ( empty( $raw['type'] ) || sanitize_key( $raw['type'] ) !== $type ) {
			return new WP_Error( 'eap_tb_starter_type', __( 'That starter layout is for a different template type.', 'elementor-animatepro' ) );
		}

		if ( '' !== trim( (string) $title ) ) {
			$raw['title'] = $title;
		}

		$template_id = EAP_TB_Transfer::import( $raw );

		if ( is_wp_error( $template_id ) ) {
			return $template_id;
		}

		// An import arrives disabled so it cannot surprise a live site; a
		// template the user just chose to create is different — they are about
		// to edit it, and its conditions are empty anyway.
		EAP_TB_Post_Type::set_enabled( $template_id, true );

		return $template_id;
	}
}
