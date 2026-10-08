<?php
/**
 * Registers the dynamic tags.
 *
 * Dynamic tags let live data fill an ordinary control: a Heading showing the
 * post title, a Button taking its URL from a custom field, an Image widget
 * bound to the featured image. Free Elementor ships the whole API; it is only
 * the tags themselves that have to come from a plugin.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EAP_TB_Tags {

	/**
	 * Panel group the tags appear under.
	 */
	const GROUP = 'eap-animatepro';

	/**
	 * Tag classes to register.
	 *
	 * @var string[]
	 */
	private static $classes = array(
		'EAP_TB_Tag_Post_Title',
		'EAP_TB_Tag_Post_Excerpt',
		'EAP_TB_Tag_Post_Date',
		'EAP_TB_Tag_Author_Name',
		'EAP_TB_Tag_Custom_Field',
		'EAP_TB_Tag_Archive_Title',
		'EAP_TB_Tag_Archive_Description',
		'EAP_TB_Tag_Site_Title',
		'EAP_TB_Tag_Site_Tagline',
		'EAP_TB_Tag_Post_URL',
		'EAP_TB_Tag_Author_URL',
		'EAP_TB_Tag_Site_URL',
		'EAP_TB_Tag_Featured_Image',
	);

	/**
	 * Hook into Elementor.
	 */
	public function __construct() {
		add_action( 'elementor/dynamic_tags/register', array( $this, 'register' ) );
	}

	/**
	 * Register the group and every tag.
	 *
	 * @param \Elementor\Core\DynamicTags\Manager $dynamic_tags Elementor's manager.
	 * @return void
	 */
	public function register( $dynamic_tags ) {
		require_once EAP_PATH . 'includes/theme-builder/class-eap-tb-tag-types.php';

		$dynamic_tags->register_group(
			self::GROUP,
			array( 'title' => __( 'AnimatePro', 'elementor-animatepro' ) )
		);

		foreach ( self::$classes as $class_name ) {
			if ( class_exists( $class_name ) ) {
				$dynamic_tags->register( new $class_name() );
			}
		}
	}
}
