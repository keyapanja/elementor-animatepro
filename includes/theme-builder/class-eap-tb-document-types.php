<?php
/**
 * Elementor document types for Theme Builder templates.
 *
 * Loaded only from the `elementor/documents/register` callback, because these
 * classes extend an Elementor class that does not exist until Elementor has
 * loaded.
 *
 * A document type is what makes the editor treat a template as its own kind of
 * thing rather than a generic page. Elementor picks the type from the
 * `_elementor_template_type` meta, which EAP_TB_Post_Type::create() writes.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class EAP_TB_Document_Base extends \Elementor\Core\DocumentTypes\PageBase {

	/**
	 * Document properties.
	 *
	 * Page templates are off because a header or footer is a fragment, not a
	 * page; `register_type` is off because these documents live in our own post
	 * type rather than Elementor's template library.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['admin_tab_group']           = '';
		$properties['support_wp_page_templates'] = false;
		$properties['register_type']             = false;
		$properties['show_in_finder']            = false;
		$properties['support_kit']               = true;

		return $properties;
	}
}

class EAP_TB_Document_Header extends EAP_TB_Document_Base {

	/**
	 * Document type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'eap-header';
	}

	/**
	 * Document title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'Header', 'elementor-animatepro' );
	}

	/**
	 * Plural document title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Headers', 'elementor-animatepro' );
	}
}

class EAP_TB_Document_Single extends EAP_TB_Document_Base {

	/**
	 * Document type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'eap-single';
	}

	/**
	 * Document title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'Single', 'elementor-animatepro' );
	}

	/**
	 * Plural document title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Singles', 'elementor-animatepro' );
	}
}

class EAP_TB_Document_Archive extends EAP_TB_Document_Base {

	/**
	 * Document type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'eap-archive';
	}

	/**
	 * Document title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'Archive', 'elementor-animatepro' );
	}

	/**
	 * Plural document title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Archives', 'elementor-animatepro' );
	}
}

class EAP_TB_Document_Loop_Item extends EAP_TB_Document_Base {

	/**
	 * Document type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'eap-loop-item';
	}

	/**
	 * Document title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'Loop Item', 'elementor-animatepro' );
	}

	/**
	 * Plural document title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Loop Items', 'elementor-animatepro' );
	}
}

class EAP_TB_Document_Search extends EAP_TB_Document_Base {

	/**
	 * Document type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'eap-search';
	}

	/**
	 * Document title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'Search Results', 'elementor-animatepro' );
	}

	/**
	 * Plural document title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Search Results', 'elementor-animatepro' );
	}
}

class EAP_TB_Document_404 extends EAP_TB_Document_Base {

	/**
	 * Document type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'eap-404';
	}

	/**
	 * Document title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( '404 Page', 'elementor-animatepro' );
	}

	/**
	 * Plural document title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( '404 Pages', 'elementor-animatepro' );
	}
}

class EAP_TB_Document_Footer extends EAP_TB_Document_Base {

	/**
	 * Document type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'eap-footer';
	}

	/**
	 * Document title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'Footer', 'elementor-animatepro' );
	}

	/**
	 * Plural document title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Footers', 'elementor-animatepro' );
	}
}
