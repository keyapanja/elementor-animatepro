<?php
/**
 * Theme Builder template types.
 *
 * Single source of truth for every template type: the admin page, the Elementor
 * document registration, the conditions editor and the front-end resolver all
 * read this list, so a type is described in exactly one place.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_TB_Types {

	const HEADER = 'header';
	const FOOTER = 'footer';

	/**
	 * Cached type list.
	 *
	 * @var array<string, array<string, mixed>>|null
	 */
	private static $types = null;

	/**
	 * Every template type, built or planned.
	 *
	 * `available` marks a type that is implemented end to end. Planned types are
	 * listed so the admin page can show the roadmap, but they cannot be created.
	 *
	 * `location` tells the renderer where a matching template is output:
	 *  - header / footer: replaces that part of the theme.
	 *  - body: replaces the whole content area (phase 2+).
	 *  - part: rendered by a widget, never resolved against the request.
	 *  - overlay: rendered on top of the page (popup, phase 4).
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function all() {
		if ( null !== self::$types ) {
			return self::$types;
		}

		self::$types = array(
			self::HEADER      => array(
				'label'       => __( 'Header', 'elementor-animatepro' ),
				'plural'      => __( 'Headers', 'elementor-animatepro' ),
				'description' => __( 'Replaces the theme header, site-wide or on the pages you choose.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-align-full-width',
				'location'    => 'header',
				'available'   => true,
			),
			self::FOOTER      => array(
				'label'       => __( 'Footer', 'elementor-animatepro' ),
				'plural'      => __( 'Footers', 'elementor-animatepro' ),
				'description' => __( 'Replaces the theme footer, site-wide or on the pages you choose.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-align-full-width',
				'location'    => 'footer',
				'available'   => true,
			),
			'single'          => array(
				'label'       => __( 'Single', 'elementor-animatepro' ),
				'plural'      => __( 'Singles', 'elementor-animatepro' ),
				'description' => __( 'The layout of one piece of content — a post, a page or any custom post type.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-media-text',
				'location'    => 'body',
				'available'   => true,
			),
			'archive'         => array(
				'label'       => __( 'Archive', 'elementor-animatepro' ),
				'plural'      => __( 'Archives', 'elementor-animatepro' ),
				'description' => __( 'Category, tag, taxonomy, author, date and post type listings.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-grid-view',
				'location'    => 'body',
				'available'   => true,
			),
			'search'          => array(
				'label'       => __( 'Search Results', 'elementor-animatepro' ),
				'plural'      => __( 'Search Results', 'elementor-animatepro' ),
				'description' => __( 'The page visitors land on after using search.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-search',
				'location'    => 'body',
				'available'   => true,
			),
			'404'             => array(
				'label'       => __( '404 Page', 'elementor-animatepro' ),
				'plural'      => __( '404 Pages', 'elementor-animatepro' ),
				'description' => __( 'Shown when a URL matches nothing on the site.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-warning',
				'location'    => 'body',
				'available'   => true,
			),
			'loop-item'       => array(
				'label'       => __( 'Loop Item', 'elementor-animatepro' ),
				'plural'      => __( 'Loop Items', 'elementor-animatepro' ),
				'description' => __( 'The repeating card used by Loop Grid and Loop Carousel.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-screenoptions',
				'location'    => 'part',
				'available'   => true,
			),
			'popup'           => array(
				'label'       => __( 'Popup', 'elementor-animatepro' ),
				'plural'      => __( 'Popups', 'elementor-animatepro' ),
				'description' => __( 'An overlay with its own triggers and close rules.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-external',
				'location'    => 'overlay',
				'available'   => true,
			),
			'product'         => array(
				'label'       => __( 'Single Product', 'elementor-animatepro' ),
				'plural'      => __( 'Single Products', 'elementor-animatepro' ),
				'description' => __( 'The WooCommerce product page layout.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-cart',
				'location'    => 'body',
				'available'   => false,
			),
			'product-archive' => array(
				'label'       => __( 'Product Archive', 'elementor-animatepro' ),
				'plural'      => __( 'Product Archives', 'elementor-animatepro' ),
				'description' => __( 'The WooCommerce shop and product category listings.', 'elementor-animatepro' ),
				'icon'        => 'dashicons-cart',
				'location'    => 'body',
				'available'   => false,
			),
		);

		return self::$types;
	}

	/**
	 * Types that can be created today.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function available() {
		return array_filter(
			self::all(),
			static function ( $type ) {
				return ! empty( $type['available'] );
			}
		);
	}

	/**
	 * A single type definition.
	 *
	 * @param string $slug Type slug.
	 * @return array<string, mixed>|null
	 */
	public static function get( $slug ) {
		$types = self::all();

		return isset( $types[ $slug ] ) ? $types[ $slug ] : null;
	}

	/**
	 * Whether a type exists and is built.
	 *
	 * @param string $slug Type slug.
	 * @return bool
	 */
	public static function is_available( $slug ) {
		$type = self::get( $slug );

		return (bool) ( $type && ! empty( $type['available'] ) );
	}

	/**
	 * Human label for a type, falling back to the raw slug.
	 *
	 * @param string $slug Type slug.
	 * @return string
	 */
	public static function label( $slug ) {
		$type = self::get( $slug );

		return $type ? $type['label'] : ucfirst( str_replace( '-', ' ', (string) $slug ) );
	}

	/**
	 * Available types that render at a given location.
	 *
	 * @param string $location Location key.
	 * @return array<string, array<string, mixed>>
	 */
	public static function by_location( $location ) {
		return array_filter(
			self::available(),
			static function ( $type ) use ( $location ) {
				return $location === $type['location'];
			}
		);
	}

	/**
	 * The Elementor document type name for a template type.
	 *
	 * @param string $slug Type slug.
	 * @return string
	 */
	public static function document_name( $slug ) {
		return 'eap-' . $slug;
	}
}
