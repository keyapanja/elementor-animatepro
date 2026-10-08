<?php
/**
 * Exporting and importing templates.
 *
 * A template is its Elementor content plus everything the Theme Builder knows
 * about it — type, display conditions, preview target and document settings —
 * so an export carries all of that and an import puts it back. Anything that
 * comes back in is pushed through the same sanitisers the admin screen uses,
 * so a hand-edited file cannot store a rule or a preview target the type does
 * not support.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_TB_Transfer {

	/**
	 * Format version, so a later change can be told from an older file.
	 */
	const FORMAT = 1;

	/**
	 * Largest file we will read, in bytes.
	 */
	const MAX_BYTES = 5242880;

	/**
	 * Build the export payload for a template.
	 *
	 * @param int $template_id Template ID.
	 * @return array<string, mixed>|WP_Error
	 */
	public static function export( $template_id ) {
		$template_id = (int) $template_id;
		$post        = get_post( $template_id );

		if ( ! $post || EAP_TB_Post_Type::POST_TYPE !== $post->post_type ) {
			return new WP_Error( 'eap_tb_missing', __( 'That template no longer exists.', 'elementor-animatepro' ) );
		}

		$type = EAP_TB_Post_Type::get_type( $template_id );
		$data = get_post_meta( $template_id, '_elementor_data', true );

		return array(
			'format'    => self::FORMAT,
			'plugin'    => 'elementor-animatepro',
			'version'   => EAP_VERSION,
			'exported'  => gmdate( 'c' ),
			'title'     => $post->post_title,
			'type'      => $type,
			'content'   => is_string( $data ) ? json_decode( $data, true ) : $data,
			'settings'  => get_post_meta( $template_id, '_elementor_page_settings', true ),
			'conditions'=> EAP_TB_Conditions::get( $template_id ),
			'preview'   => EAP_TB_Preview::get( $template_id ),
		);
	}

	/**
	 * A filename for an export.
	 *
	 * @param int $template_id Template ID.
	 * @return string
	 */
	public static function filename( $template_id ) {
		$slug = sanitize_title( get_the_title( $template_id ) );

		if ( '' === $slug ) {
			$slug = 'template-' . (int) $template_id;
		}

		return 'animatepro-' . $slug . '.json';
	}

	/**
	 * Create a template from an export payload.
	 *
	 * @param mixed $raw Decoded JSON.
	 * @return int|WP_Error New template ID.
	 */
	public static function import( $raw ) {
		if ( ! is_array( $raw ) ) {
			return new WP_Error( 'eap_tb_bad_file', __( 'That file is not an AnimatePro template.', 'elementor-animatepro' ) );
		}

		if ( empty( $raw['plugin'] ) || 'elementor-animatepro' !== $raw['plugin'] ) {
			return new WP_Error( 'eap_tb_bad_file', __( 'That file was not exported from AnimatePro.', 'elementor-animatepro' ) );
		}

		$type = isset( $raw['type'] ) ? sanitize_key( $raw['type'] ) : '';

		if ( ! EAP_TB_Types::is_available( $type ) ) {
			return new WP_Error(
				'eap_tb_bad_type',
				sprintf(
					/* translators: %s: template type from the file. */
					__( 'This file is a %s template, which this version cannot create.', 'elementor-animatepro' ),
					'' !== $type ? $type : __( 'unknown', 'elementor-animatepro' )
				)
			);
		}

		$title = isset( $raw['title'] ) ? sanitize_text_field( (string) $raw['title'] ) : '';

		if ( '' === trim( $title ) ) {
			$title = EAP_TB_Types::label( $type );
		}

		$template_id = EAP_TB_Post_Type::create( $type, $title );

		if ( is_wp_error( $template_id ) ) {
			return $template_id;
		}

		$content = isset( $raw['content'] ) ? $raw['content'] : array();

		if ( is_array( $content ) ) {
			update_post_meta( $template_id, '_elementor_data', wp_slash( wp_json_encode( $content ) ) );
		}

		if ( ! empty( $raw['settings'] ) && is_array( $raw['settings'] ) ) {
			update_post_meta( $template_id, '_elementor_page_settings', $raw['settings'] );
		}

		update_post_meta( $template_id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : EAP_VERSION );

		// Both of these run through their own sanitisers, which drop anything
		// the type cannot use — so an edited file cannot smuggle a rule in.
		if ( isset( $raw['conditions'] ) ) {
			EAP_TB_Conditions::save( $template_id, $raw['conditions'] );
		}

		if ( isset( $raw['preview'] ) ) {
			EAP_TB_Preview::save( $template_id, $raw['preview'] );
		}

		// An import arrives switched off, so it cannot take over the site the
		// moment it lands.
		EAP_TB_Post_Type::set_enabled( $template_id, false );

		return $template_id;
	}

	/**
	 * Read and decode an uploaded export file.
	 *
	 * @param array<string, mixed> $file One entry from $_FILES.
	 * @return array<string, mixed>|WP_Error
	 */
	public static function read_upload( $file ) {
		if ( empty( $file ) || ! is_array( $file ) || ! isset( $file['tmp_name'] ) ) {
			return new WP_Error( 'eap_tb_no_file', __( 'No file was uploaded.', 'elementor-animatepro' ) );
		}

		if ( ! empty( $file['error'] ) ) {
			return new WP_Error( 'eap_tb_upload', __( 'That file could not be uploaded.', 'elementor-animatepro' ) );
		}

		if ( isset( $file['size'] ) && (int) $file['size'] > self::MAX_BYTES ) {
			return new WP_Error( 'eap_tb_too_big', __( 'That file is too large to be a template.', 'elementor-animatepro' ) );
		}

		if ( ! is_uploaded_file( $file['tmp_name'] ) ) {
			return new WP_Error( 'eap_tb_upload', __( 'That file could not be read.', 'elementor-animatepro' ) );
		}

		$contents = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		if ( false === $contents || '' === trim( (string) $contents ) ) {
			return new WP_Error( 'eap_tb_empty', __( 'That file is empty.', 'elementor-animatepro' ) );
		}

		$decoded = json_decode( $contents, true );

		if ( null === $decoded && JSON_ERROR_NONE !== json_last_error() ) {
			return new WP_Error( 'eap_tb_bad_json', __( 'That file is not valid JSON.', 'elementor-animatepro' ) );
		}

		return $decoded;
	}
}
