<?php
/**
 * Registers the Theme Builder's Elementor document types.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_TB_Documents {

	/**
	 * Document class per template type.
	 *
	 * @var array<string, string>
	 */
	private static $classes = array(
		EAP_TB_Types::HEADER => 'EAP_TB_Document_Header',
		EAP_TB_Types::FOOTER => 'EAP_TB_Document_Footer',
		'single'             => 'EAP_TB_Document_Single',
		'archive'            => 'EAP_TB_Document_Archive',
		'search'             => 'EAP_TB_Document_Search',
		'404'                => 'EAP_TB_Document_404',
		'loop-item'          => 'EAP_TB_Document_Loop_Item',
		'popup'              => 'EAP_TB_Document_Popup',
	);

	/**
	 * Hook into Elementor.
	 */
	public function __construct() {
		add_action( 'elementor/documents/register', array( $this, 'register' ) );
	}

	/**
	 * Register one document type per available template type.
	 *
	 * @param \Elementor\Core\Documents_Manager $documents_manager Elementor's manager.
	 * @return void
	 */
	public function register( $documents_manager ) {
		require_once EAP_PATH . 'includes/theme-builder/class-eap-tb-document-types.php';

		foreach ( self::$classes as $type => $class_name ) {
			if ( ! EAP_TB_Types::is_available( $type ) || ! class_exists( $class_name ) ) {
				continue;
			}

			$documents_manager->register_document_type( EAP_TB_Types::document_name( $type ), $class_name );
		}
	}
}
