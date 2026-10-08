<?php
/**
 * Theme Builder template resolver.
 *
 * Answers one question per request: for this template type, which template
 * wins here? The most specific matching include wins; any matching exclude
 * takes a template out of the running. Ties go to the most recently edited
 * template, because get_templates() hands them back in that order and a strict
 * greater-than keeps the first.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_TB_Resolver {

	/**
	 * Resolved template per type for this request.
	 *
	 * @var array<string, int>
	 */
	private static $resolved = array();

	/**
	 * The winning template for a type on the current request.
	 *
	 * @param string $type Template type slug.
	 * @return int Template ID, or 0 when nothing matches.
	 */
	public static function get_template_id( $type ) {
		if ( isset( self::$resolved[ $type ] ) ) {
			return self::$resolved[ $type ];
		}

		self::$resolved[ $type ] = 0;

		// A template previewing itself must not pull other templates in.
		if ( is_singular( EAP_TB_Post_Type::POST_TYPE ) ) {
			return 0;
		}

		$best_id     = 0;
		$best_weight = -1;

		foreach ( EAP_TB_Post_Type::get_templates( $type ) as $template ) {
			if ( 'publish' !== $template->post_status ) {
				continue;
			}

			if ( ! EAP_TB_Post_Type::is_enabled( $template->ID ) ) {
				continue;
			}

			$weight = EAP_TB_Conditions::match( EAP_TB_Conditions::get( $template->ID ) );

			if ( false === $weight || $weight <= $best_weight ) {
				continue;
			}

			$best_weight = $weight;
			$best_id     = (int) $template->ID;
		}

		self::$resolved[ $type ] = $best_id;

		return $best_id;
	}

	/**
	 * Every template of a type that applies here, most specific first.
	 *
	 * Headers and bodies have exactly one winner; popups do not — a page can
	 * legitimately carry several.
	 *
	 * @param string $type Template type slug.
	 * @return int[]
	 */
	public static function get_all_matching( $type ) {
		if ( is_singular( EAP_TB_Post_Type::POST_TYPE ) ) {
			return array();
		}

		$matches = array();

		foreach ( EAP_TB_Post_Type::get_templates( $type ) as $template ) {
			if ( 'publish' !== $template->post_status || ! EAP_TB_Post_Type::is_enabled( $template->ID ) ) {
				continue;
			}

			$weight = EAP_TB_Conditions::match( EAP_TB_Conditions::get( $template->ID ) );

			if ( false === $weight ) {
				continue;
			}

			$matches[] = array(
				'id'     => (int) $template->ID,
				'weight' => $weight,
			);
		}

		usort(
			$matches,
			static function ( $a, $b ) {
				return $b['weight'] <=> $a['weight'];
			}
		);

		return wp_list_pluck( $matches, 'id' );
	}

	/**
	 * Forget what was resolved. Used by tests and harnesses.
	 *
	 * @return void
	 */
	public static function reset() {
		self::$resolved = array();
	}
}
