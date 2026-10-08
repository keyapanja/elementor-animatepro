<?php
/**
 * Dynamic tag classes.
 *
 * Loaded only from the `elementor/dynamic_tags/register` callback, because
 * these extend Elementor classes that do not exist until Elementor has loaded.
 *
 * A tag fills an ordinary control with live data, so a plain Heading can show
 * the post title and a Button can take its URL from a custom field. Every tag
 * resolves "the current post" the same way the dynamic widgets do, including
 * the Theme Builder's preview target, so they behave while editing a template
 * as well as on the page.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

abstract class EAP_TB_Tag_Base extends Tag {

	/**
	 * Panel group.
	 *
	 * @return string
	 */
	public function get_group() {
		return EAP_TB_Tags::GROUP;
	}

	/**
	 * The post a tag stands for.
	 *
	 * @return int
	 */
	protected function eap_post_id() {
		$post_id = (int) get_the_ID();

		if ( ! $post_id ) {
			$post_id = (int) get_queried_object_id();
		}

		if ( ! $post_id ) {
			// Editing a template: whatever it is set to preview against.
			$post_id = (int) apply_filters( 'eap_editor_preview_post_id', 0 );
		}

		return $post_id;
	}
}

class EAP_TB_Tag_Post_Title extends EAP_TB_Tag_Base {

	public function get_name() {
		return 'eap-post-title';
	}

	public function get_title() {
		return esc_html__( 'Post Title', 'elementor-animatepro' );
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	public function render() {
		$post_id = $this->eap_post_id();

		if ( $post_id ) {
			echo wp_kses_post( get_the_title( $post_id ) );
		}
	}
}

class EAP_TB_Tag_Post_Excerpt extends EAP_TB_Tag_Base {

	public function get_name() {
		return 'eap-post-excerpt';
	}

	public function get_title() {
		return esc_html__( 'Post Excerpt', 'elementor-animatepro' );
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	public function render() {
		$post_id = $this->eap_post_id();

		if ( ! $post_id ) {
			return;
		}

		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		// get_the_excerpt() needs the post in hand to generate one from the
		// content when the field itself is empty.
		echo wp_kses_post( get_the_excerpt( $post ) );
	}
}

class EAP_TB_Tag_Post_Date extends EAP_TB_Tag_Base {

	public function get_name() {
		return 'eap-post-date';
	}

	public function get_title() {
		return esc_html__( 'Post Date', 'elementor-animatepro' );
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	protected function register_controls() {
		$this->add_control(
			'eap_date_type',
			array(
				'label'   => esc_html__( 'Date', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'published',
				'options' => array(
					'published' => esc_html__( 'Published', 'elementor-animatepro' ),
					'modified'  => esc_html__( 'Last Modified', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'eap_date_format',
			array(
				'label'       => esc_html__( 'Format', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => get_option( 'date_format' ),
				'description' => esc_html__( 'Leave empty to use the format set in WordPress.', 'elementor-animatepro' ),
			)
		);
	}

	public function render() {
		$post_id = $this->eap_post_id();

		if ( ! $post_id ) {
			return;
		}

		$format = trim( (string) $this->get_settings( 'eap_date_format' ) );

		if ( '' === $format ) {
			$format = get_option( 'date_format' );
		}

		$date = 'modified' === $this->get_settings( 'eap_date_type' )
			? get_the_modified_date( $format, $post_id )
			: get_the_date( $format, $post_id );

		echo esc_html( (string) $date );
	}
}

class EAP_TB_Tag_Author_Name extends EAP_TB_Tag_Base {

	public function get_name() {
		return 'eap-author-name';
	}

	public function get_title() {
		return esc_html__( 'Author Name', 'elementor-animatepro' );
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	public function render() {
		$post_id = $this->eap_post_id();

		if ( ! $post_id ) {
			return;
		}

		$author = (int) get_post_field( 'post_author', $post_id );
		$user   = $author ? get_userdata( $author ) : null;

		if ( $user ) {
			echo esc_html( $user->display_name );
		}
	}
}

class EAP_TB_Tag_Custom_Field extends EAP_TB_Tag_Base {

	public function get_name() {
		return 'eap-custom-field';
	}

	public function get_title() {
		return esc_html__( 'Custom Field', 'elementor-animatepro' );
	}

	public function get_categories() {
		return array(
			TagsModule::TEXT_CATEGORY,
			TagsModule::POST_META_CATEGORY,
			TagsModule::URL_CATEGORY,
			TagsModule::NUMBER_CATEGORY,
		);
	}

	protected function register_controls() {
		$this->add_control(
			'eap_meta_key',
			array(
				'label'       => esc_html__( 'Field Name', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'subtitle',
				'description' => esc_html__( 'The meta key to read from the current post.', 'elementor-animatepro' ),
			)
		);
	}

	public function render() {
		$key     = trim( (string) $this->get_settings( 'eap_meta_key' ) );
		$post_id = $this->eap_post_id();

		if ( '' === $key || ! $post_id ) {
			return;
		}

		$value = get_post_meta( $post_id, $key, true );

		// A field holding an array or an object has no single sensible
		// rendering, so it is left to the fallback rather than printed as
		// "Array".
		if ( is_array( $value ) || is_object( $value ) ) {
			return;
		}

		echo wp_kses_post( (string) $value );
	}
}

class EAP_TB_Tag_Archive_Title extends EAP_TB_Tag_Base {

	public function get_name() {
		return 'eap-archive-title';
	}

	public function get_title() {
		return esc_html__( 'Archive Title', 'elementor-animatepro' );
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	public function render() {
		if ( is_search() ) {
			/* translators: %s: the search term. */
			printf( esc_html__( 'Search results for: %s', 'elementor-animatepro' ), esc_html( get_search_query() ) );
			return;
		}

		echo wp_kses_post( get_the_archive_title() );
	}
}

class EAP_TB_Tag_Archive_Description extends EAP_TB_Tag_Base {

	public function get_name() {
		return 'eap-archive-description';
	}

	public function get_title() {
		return esc_html__( 'Archive Description', 'elementor-animatepro' );
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	public function render() {
		echo wp_kses_post( get_the_archive_description() );
	}
}

class EAP_TB_Tag_Site_Title extends EAP_TB_Tag_Base {

	public function get_name() {
		return 'eap-site-title';
	}

	public function get_title() {
		return esc_html__( 'Site Title', 'elementor-animatepro' );
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	public function render() {
		echo esc_html( get_bloginfo( 'name' ) );
	}
}

class EAP_TB_Tag_Site_Tagline extends EAP_TB_Tag_Base {

	public function get_name() {
		return 'eap-site-tagline';
	}

	public function get_title() {
		return esc_html__( 'Site Tagline', 'elementor-animatepro' );
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	public function render() {
		echo esc_html( get_bloginfo( 'description' ) );
	}
}

class EAP_TB_Tag_Post_URL extends EAP_TB_Tag_Base {

	public function get_name() {
		return 'eap-post-url';
	}

	public function get_title() {
		return esc_html__( 'Post URL', 'elementor-animatepro' );
	}

	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}

	public function render() {
		$post_id = $this->eap_post_id();

		if ( $post_id ) {
			echo esc_url( (string) get_permalink( $post_id ) );
		}
	}
}

class EAP_TB_Tag_Author_URL extends EAP_TB_Tag_Base {

	public function get_name() {
		return 'eap-author-url';
	}

	public function get_title() {
		return esc_html__( 'Author Archive URL', 'elementor-animatepro' );
	}

	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}

	public function render() {
		$post_id = $this->eap_post_id();
		$author  = $post_id ? (int) get_post_field( 'post_author', $post_id ) : 0;

		if ( $author ) {
			echo esc_url( get_author_posts_url( $author ) );
		}
	}
}

class EAP_TB_Tag_Site_URL extends EAP_TB_Tag_Base {

	public function get_name() {
		return 'eap-site-url';
	}

	public function get_title() {
		return esc_html__( 'Site URL', 'elementor-animatepro' );
	}

	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}

	public function render() {
		echo esc_url( home_url( '/' ) );
	}
}

class EAP_TB_Tag_Featured_Image extends Data_Tag {

	public function get_name() {
		return 'eap-featured-image';
	}

	public function get_title() {
		return esc_html__( 'Featured Image', 'elementor-animatepro' );
	}

	public function get_group() {
		return EAP_TB_Tags::GROUP;
	}

	public function get_categories() {
		return array( TagsModule::IMAGE_CATEGORY );
	}

	/**
	 * An image control wants an id/url pair, not a string.
	 *
	 * @param array $options Unused.
	 * @return array<string, mixed>
	 */
	public function get_value( array $options = array() ) {
		$post_id = (int) get_the_ID();

		if ( ! $post_id ) {
			$post_id = (int) get_queried_object_id();
		}

		if ( ! $post_id ) {
			$post_id = (int) apply_filters( 'eap_editor_preview_post_id', 0 );
		}

		$thumbnail_id = $post_id ? (int) get_post_thumbnail_id( $post_id ) : 0;

		if ( ! $thumbnail_id ) {
			return array(
				'id'  => '',
				'url' => \Elementor\Utils::get_placeholder_image_src(),
			);
		}

		return array(
			'id'  => $thumbnail_id,
			'url' => wp_get_attachment_image_url( $thumbnail_id, 'full' ),
		);
	}
}
