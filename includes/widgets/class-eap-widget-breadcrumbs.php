<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

/**
 * Breadcrumbs widget.
 *
 * A trail from Home to the current page, built for every context WordPress
 * has: static/posts front page, page hierarchies, posts (through their
 * category ancestors), custom post types (through their archive and their
 * hierarchical taxonomy), attachments, term archives with ancestors, post type
 * archives, author, day/month/year, search and 404. WooCommerce products get
 * the Shop page as their archive crumb without depending on WooCommerce's own
 * breadcrumb function.
 *
 * The trail is built as DATA first (`eap_build_trail()`), then rendered — which
 * is what lets the same trail feed both the `<ol>` and the Schema.org
 * BreadcrumbList JSON-LD, and what makes it testable without a request.
 *
 * Differences from the usual theme snippet, on purpose:
 * - the post type archive link comes from get_post_type_archive_link(), not
 *   home_url . '/' . slug, so it is right with plain permalinks and honours
 *   `has_archive`;
 * - the category shown for a post is the SEO plugin's primary term when one is
 *   set (Yoast, Rank Math), otherwise the DEEPEST term rather than whichever
 *   happens to be first;
 * - hierarchical custom taxonomies get the same ancestor walk as categories;
 * - it is a real `<nav><ol>` with `aria-current="page"` on the last crumb.
 */
class EAP_Widget_Breadcrumbs extends EAP_Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-breadcrumbs';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Breadcrumbs', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-navigation-horizontal';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'breadcrumbs', 'breadcrumb', 'navigation', 'trail', 'path', 'schema', 'seo' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-breadcrumbs' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_general_section();
		$this->register_labels_section();
		$this->register_schema_section();

		$this->register_container_style();
		$this->register_items_style();
		$this->register_separator_style();
		$this->register_prefix_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	/**
	 * General section.
	 *
	 * @return void
	 */
	protected function register_general_section() {
		$this->start_controls_section(
			'section_general',
			array( 'label' => __( 'General', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'home_label',
			array(
				'label'   => __( 'Home Label', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Home', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_home',
			array(
				'label'        => __( 'Show Home', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_current',
			array(
				'label'        => __( 'Show Current Page', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_on_front',
			array(
				'label'        => __( 'Show On Front Page', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Off: the widget renders nothing on the front page.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_type_archive',
			array(
				'label'        => __( 'Show Archive Link', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'When the post type has an archive, add it as a crumb before the post (the Shop page for products, the posts page for posts). Off hides that link.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'archive_url',
			array(
				'label'       => __( 'Archive Link', 'elementor-animatepro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'Detected automatically', 'elementor-animatepro' ),
				'description' => __( 'Point the archive crumb somewhere else — a landing page instead of the raw archive — or add one for a post type that has no archive.', 'elementor-animatepro' ),
				'options'     => false,
				'condition'   => array( 'show_type_archive' => 'yes' ),
			)
		);

		$this->add_control(
			'archive_label',
			array(
				'label'       => __( 'Archive Label', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Post type name', 'elementor-animatepro' ),
				'condition'   => array( 'show_type_archive' => 'yes' ),
			)
		);

		$this->add_control(
			'max_length',
			array(
				'label'       => __( 'Trim Current To', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 200,
				'description' => __( 'Characters. 0 leaves the current page title whole.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'prefix_heading',
			array(
				'label'     => __( 'Prefix', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'prefix_type',
			array(
				'label'   => __( 'Prefix', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none' => __( 'None', 'elementor-animatepro' ),
					'icon' => __( 'Icon (links home)', 'elementor-animatepro' ),
					'text' => __( 'Text', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'prefix_icon',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-home',
					'library' => 'fa-solid',
				),
				'condition' => array( 'prefix_type' => 'icon' ),
			)
		);

		$this->add_control(
			'prefix_text',
			array(
				'label'     => __( 'Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'You are here:', 'elementor-animatepro' ),
				'condition' => array( 'prefix_type' => 'text' ),
			)
		);

		$this->add_control(
			'separator_heading',
			array(
				'label'     => __( 'Separator', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'separator_type',
			array(
				'label'   => __( 'Separator', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => array(
					'text' => __( 'Text', 'elementor-animatepro' ),
					'icon' => __( 'Icon', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'separator_text',
			array(
				'label'     => __( 'Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '/',
				'condition' => array( 'separator_type' => 'text' ),
			)
		);

		$this->add_control(
			'separator_icon',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'eicon-chevron-right',
					'library' => 'eicons',
				),
				'condition' => array( 'separator_type' => 'icon' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Archive label section.
	 *
	 * @return void
	 */
	protected function register_labels_section() {
		$this->start_controls_section(
			'section_labels',
			array( 'label' => __( 'Archive Labels', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'labels_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Text placed before the archive name on its own crumb. Leave one empty to show the name alone.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'category_label',
			array(
				'label'       => __( 'Category', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Category:', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'tag_label',
			array(
				'label'       => __( 'Tag', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Tag:', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'author_label',
			array(
				'label'   => __( 'Author', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Posts by', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'search_label',
			array(
				'label'   => __( 'Search', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Search results for', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'not_found_label',
			array(
				'label'   => __( '404', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Page not found', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'blog_label',
			array(
				'label'       => __( 'Posts Page', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Uses the page title', 'elementor-animatepro' ),
				'description' => __( 'Overrides the title of the page set as your posts page.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'wrap_quotes',
			array(
				'label'        => __( 'Quote Search & Term Names', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Search results for “blue” rather than Search results for blue.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Schema section.
	 *
	 * @return void
	 */
	protected function register_schema_section() {
		$this->start_controls_section(
			'section_schema',
			array( 'label' => __( 'Structured Data', 'elementor-animatepro' ) )
		);

		$seo_plugin = defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' );

		$this->add_control(
			'json_ld',
			array(
				'label'        => __( 'BreadcrumbList JSON-LD', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => $seo_plugin ? '' : 'yes',
				'return_value' => 'yes',
				'description'  => $seo_plugin
					? __( 'Off by default because Yoast SEO / Rank Math already emits one; two on a page is a duplicate.', 'elementor-animatepro' )
					: __( 'Schema.org markup search engines use to show the trail in results.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	/**
	 * Container style.
	 *
	 * @return void
	 */
	protected function register_container_style() {
		$this->start_controls_section(
			'style_container',
			array(
				'label' => __( 'Container', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-bc' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'container_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-bc' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'container_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-bc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'container_border',
				'selector' => '{{WRAPPER}} .eap-bc',
			)
		);

		$this->add_responsive_control(
			'container_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-bc' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Crumb style.
	 *
	 * @return void
	 */
	protected function register_items_style() {
		$this->start_controls_section(
			'style_items',
			array(
				'label' => __( 'Crumbs', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_typography',
				'selector' => '{{WRAPPER}} .eap-bc__item',
			)
		);

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
					'em' => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-bc' => '--eap-bc-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => __( 'Link Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-bc__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_color_hover',
			array(
				'label'     => __( 'Link Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-bc__link:hover, {{WRAPPER}} .eap-bc__link:focus-visible' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_underline',
			array(
				'label'     => __( 'Underline Links', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'hover',
				'options'   => array(
					'never'  => __( 'Never', 'elementor-animatepro' ),
					'hover'  => __( 'On hover', 'elementor-animatepro' ),
					'always' => __( 'Always', 'elementor-animatepro' ),
				),
				'selectors_dictionary' => array(
					'never'  => '--eap-bc-ul: none; --eap-bc-ul-hover: none;',
					'hover'  => '--eap-bc-ul: none; --eap-bc-ul-hover: underline;',
					'always' => '--eap-bc-ul: underline; --eap-bc-ul-hover: underline;',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-bc' => '{{VALUE}}',
				),
			)
		);

		$this->add_control(
			'current_color',
			array(
				'label'     => __( 'Current Page Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .eap-bc__current' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'current_typography',
				'label'    => __( 'Current Page Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-bc__current',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Separator style.
	 *
	 * @return void
	 */
	protected function register_separator_style() {
		$this->start_controls_section(
			'style_separator',
			array(
				'label' => __( 'Separator', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'separator_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-bc__sep' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'separator_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 6, 'max' => 40 ),
					'em' => array( 'min' => 0.4, 'max' => 3, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-bc__sep' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'separator_offset',
			array(
				'label'      => __( 'Vertical Offset', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => -10, 'max' => 10 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-bc__sep' => 'transform: translateY({{SIZE}}px);',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Prefix style.
	 *
	 * @return void
	 */
	protected function register_prefix_style() {
		$this->start_controls_section(
			'style_prefix',
			array(
				'label'     => __( 'Prefix', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'prefix_type!' => 'none' ),
			)
		);

		$this->add_control(
			'prefix_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-bc__prefix' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'prefix_color_hover',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.eap-bc__prefix:hover' => 'color: {{VALUE}};',
				),
				'condition' => array( 'prefix_type' => 'icon' ),
			)
		);

		$this->add_responsive_control(
			'prefix_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 8, 'max' => 48 ),
					'em' => array( 'min' => 0.5, 'max' => 3, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-bc__prefix' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'prefix_typography',
				'selector'  => '{{WRAPPER}} .eap-bc__prefix',
				'condition' => array( 'prefix_type' => 'text' ),
			)
		);

		$this->add_responsive_control(
			'prefix_gap',
			array(
				'label'      => __( 'Gap After', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
					'em' => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-bc__prefix' => 'margin-inline-end: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * TRAIL
	 * ================================================================== */

	/**
	 * A crumb.
	 *
	 * @param string $label Label.
	 * @param string $url   URL, '' for the current page.
	 * @return array
	 */
	protected function crumb( $label, $url = '' ) {
		return array(
			'label' => (string) $label,
			'url'   => (string) $url,
		);
	}

	/**
	 * Wrap a name in the configured quotes.
	 *
	 * @param string $name     Name.
	 * @param array  $settings Settings.
	 * @return string
	 */
	protected function quoted( $name, $settings ) {
		if ( 'yes' !== ( $settings['wrap_quotes'] ?? '' ) ) {
			return $name;
		}
		/* translators: %s: quoted text. */
		return sprintf( _x( '“%s”', 'quoted crumb', 'elementor-animatepro' ), $name );
	}

	/**
	 * A label with an optional prefix in front.
	 *
	 * @param string $prefix Prefix (may be '').
	 * @param string $name   Name.
	 * @return string
	 */
	protected function prefixed( $prefix, $name ) {
		$prefix = trim( (string) $prefix );
		return '' === $prefix ? $name : $prefix . ' ' . $name;
	}

	/**
	 * The term to show for a post in a taxonomy: the SEO plugin's primary term
	 * if one is set, otherwise the deepest of the post's terms.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy.
	 * @return WP_Term|null
	 */
	protected function pick_term( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );
		if ( ! is_array( $terms ) || empty( $terms ) ) {
			return null;
		}

		foreach ( array( '_yoast_wpseo_primary_' . $taxonomy, 'rank_math_primary_' . $taxonomy ) as $key ) {
			$primary = (int) get_post_meta( $post_id, $key, true );
			if ( $primary ) {
				foreach ( $terms as $term ) {
					if ( (int) $term->term_id === $primary ) {
						return $term;
					}
				}
			}
		}

		$best  = $terms[0];
		$depth = -1;
		foreach ( $terms as $term ) {
			$d = count( get_ancestors( $term->term_id, $taxonomy, 'taxonomy' ) );
			if ( $d > $depth ) {
				$depth = $d;
				$best  = $term;
			}
		}

		return $best;
	}

	/**
	 * Crumbs for a term's ancestors then the term itself.
	 *
	 * @param WP_Term $term       Term.
	 * @param bool    $link_last  Link the term itself (true when it is not the current page).
	 * @param string  $last_label Override for the term's own label.
	 * @return array[]
	 */
	protected function term_crumbs( $term, $link_last, $last_label = '' ) {
		$out       = array();
		$ancestors = array_reverse( get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' ) );

		foreach ( $ancestors as $ancestor_id ) {
			$ancestor = get_term( $ancestor_id, $term->taxonomy );
			if ( $ancestor && ! is_wp_error( $ancestor ) ) {
				$link  = get_term_link( $ancestor );
				$out[] = $this->crumb( $ancestor->name, is_wp_error( $link ) ? '' : $link );
			}
		}

		$label = '' !== $last_label ? $last_label : $term->name;
		$link  = $link_last ? get_term_link( $term ) : '';
		$out[] = $this->crumb( $label, is_wp_error( $link ) ? '' : $link );

		return $out;
	}

	/**
	 * The archive crumb for a post type, if it has one.
	 *
	 * Products use the Shop page rather than get_post_type_archive_link(), which
	 * WooCommerce points at the same place but only when the Shop page is set.
	 *
	 * @param string $post_type Post type.
	 * @return array|null
	 */
	protected function type_archive_crumb( $post_type ) {
		if ( 'post' === $post_type ) {
			$page_id = (int) get_option( 'page_for_posts' );
			if ( $page_id && 'page' === get_option( 'show_on_front' ) ) {
				return $this->crumb( get_the_title( $page_id ), get_permalink( $page_id ) );
			}
			return null;
		}

		if ( 'product' === $post_type && function_exists( 'wc_get_page_id' ) ) {
			$shop = (int) wc_get_page_id( 'shop' );
			if ( $shop > 0 ) {
				return $this->crumb( get_the_title( $shop ), get_permalink( $shop ) );
			}
		}

		$object = get_post_type_object( $post_type );
		if ( ! $object ) {
			return null;
		}

		$link = get_post_type_archive_link( $post_type );
		if ( ! $link ) {
			return null;
		}

		return $this->crumb( $object->labels->name, $link );
	}

	/**
	 * The archive crumb after the author's overrides: a custom link replaces
	 * (or supplies) the URL, a custom label replaces the name.
	 *
	 * @param string $post_type Post type.
	 * @param array  $settings  Settings.
	 * @return array|null
	 */
	protected function archive_crumb( $post_type, $settings ) {
		$auto  = $this->type_archive_crumb( $post_type );
		$url   = trim( (string) ( $settings['archive_url']['url'] ?? '' ) );
		$label = trim( (string) ( $settings['archive_label'] ?? '' ) );

		if ( '' === $url && ! $auto ) {
			return null;
		}

		if ( '' === $label ) {
			if ( $auto ) {
				$label = $auto['label'];
			} else {
				$object = get_post_type_object( $post_type );
				$label  = $object ? $object->labels->name : $post_type;
			}
		}

		return $this->crumb( $label, '' !== $url ? $url : $auto['url'] );
	}

	/**
	 * The first public hierarchical taxonomy attached to a post type, else the
	 * first public one at all.
	 *
	 * @param string $post_type Post type.
	 * @return string Taxonomy name or ''.
	 */
	protected function pick_taxonomy( $post_type ) {
		if ( 'post' === $post_type ) {
			return 'category';
		}
		if ( 'product' === $post_type && taxonomy_exists( 'product_cat' ) ) {
			return 'product_cat';
		}

		$taxonomies = get_object_taxonomies( $post_type, 'objects' );
		$flat       = '';

		foreach ( $taxonomies as $tax ) {
			if ( empty( $tax->public ) || 'post_format' === $tax->name ) {
				continue;
			}
			if ( ! empty( $tax->hierarchical ) ) {
				return $tax->name;
			}
			if ( '' === $flat ) {
				$flat = $tax->name;
			}
		}

		return $flat;
	}

	/**
	 * Crumbs for a page's ancestors, oldest first.
	 *
	 * @param int $post_id Post ID.
	 * @return array[]
	 */
	protected function ancestor_crumbs( $post_id ) {
		$out = array();
		foreach ( array_reverse( get_post_ancestors( $post_id ) ) as $ancestor_id ) {
			$out[] = $this->crumb( get_the_title( $ancestor_id ), get_permalink( $ancestor_id ) );
		}
		return $out;
	}

	/**
	 * Build the trail for the current request as data.
	 *
	 * Every entry is {label, url}; the last one is the current page and has no
	 * url. Public so the trail can be inspected without rendering.
	 *
	 * @param array $settings Settings.
	 * @return array[] Empty when nothing should render.
	 */
	public function eap_build_trail( $settings ) {
		$home = $this->crumb( $settings['home_label'] ?? __( 'Home', 'elementor-animatepro' ), home_url( '/' ) );

		if ( is_front_page() ) {
			if ( 'yes' !== ( $settings['show_on_front'] ?? 'yes' ) ) {
				return array();
			}
			return array( $this->crumb( $home['label'] ) );
		}

		$trail = array( $home );

		if ( is_home() ) {
			// The posts page, when a static front page is set.
			$page_id = (int) get_option( 'page_for_posts' );
			$label   = trim( (string) ( $settings['blog_label'] ?? '' ) );
			if ( '' === $label ) {
				$label = $page_id ? get_the_title( $page_id ) : __( 'Blog', 'elementor-animatepro' );
			}
			$trail[] = $this->crumb( $label );
			return $trail;
		}

		if ( is_404() ) {
			$trail[] = $this->crumb( $settings['not_found_label'] ?? __( 'Page not found', 'elementor-animatepro' ) );
			return $trail;
		}

		if ( is_search() ) {
			$trail[] = $this->crumb( $this->prefixed( $settings['search_label'] ?? '', $this->quoted( get_search_query(), $settings ) ) );
			return $trail;
		}

		if ( is_author() ) {
			$author = get_queried_object();
			$name   = $author instanceof WP_User ? $author->display_name : '';
			$trail[] = $this->crumb( $this->prefixed( $settings['author_label'] ?? '', $name ) );
			return $trail;
		}

		if ( is_day() || is_month() || is_year() ) {
			$year  = get_query_var( 'year' );
			$month = get_query_var( 'monthnum' );
			$day   = get_query_var( 'day' );

			if ( is_year() ) {
				$trail[] = $this->crumb( $year );
			} elseif ( is_month() ) {
				$trail[] = $this->crumb( $year, get_year_link( $year ) );
				$trail[] = $this->crumb( wp_date( 'F', mktime( 0, 0, 0, (int) $month, 1, (int) $year ) ) );
			} else {
				$trail[] = $this->crumb( $year, get_year_link( $year ) );
				$trail[] = $this->crumb( wp_date( 'F', mktime( 0, 0, 0, (int) $month, 1, (int) $year ) ), get_month_link( $year, $month ) );
				$trail[] = $this->crumb( wp_date( 'j', mktime( 0, 0, 0, (int) $month, (int) $day, (int) $year ) ) );
			}
			return $trail;
		}

		if ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term instanceof WP_Term ) {
				$label = $term->name;
				if ( is_category() ) {
					$label = $this->prefixed( $settings['category_label'] ?? '', $this->quoted( $term->name, $settings ) );
				} elseif ( is_tag() ) {
					$label = $this->prefixed( $settings['tag_label'] ?? '', $this->quoted( $term->name, $settings ) );
				}

				// A taxonomy that belongs to exactly one post type with an archive
				// gets that archive in front of it (Shop before a product category).
				if ( 'yes' === ( $settings['show_type_archive'] ?? 'yes' ) ) {
					$tax   = get_taxonomy( $term->taxonomy );
					$types = $tax ? array_values( array_diff( (array) $tax->object_type, array( 'post' ) ) ) : array();
					if ( 1 === count( $types ) ) {
						$archive = $this->archive_crumb( $types[0], $settings );
						if ( $archive ) {
							$trail[] = $archive;
						}
					}
				}

				$trail = array_merge( $trail, $this->term_crumbs( $term, false, $label ) );
			}
			return $trail;
		}

		if ( is_post_type_archive() ) {
			$object  = get_queried_object();
			$trail[] = $this->crumb( $object && isset( $object->labels ) ? $object->labels->name : post_type_archive_title( '', false ) );
			return $trail;
		}

		if ( is_singular() ) {
			$post_id = $this->eap_get_post_id();
			$post    = $post_id ? get_post( $post_id ) : null;

			if ( ! $post ) {
				return $trail;
			}

			$type = $post->post_type;

			if ( 'attachment' === $type ) {
				if ( $post->post_parent ) {
					$trail   = array_merge( $trail, $this->ancestor_crumbs( $post->post_parent ) );
					$trail[] = $this->crumb( get_the_title( $post->post_parent ), get_permalink( $post->post_parent ) );
				}
				$trail[] = $this->crumb( get_the_title( $post ) );
				return $trail;
			}

			if ( 'page' === $type ) {
				$trail   = array_merge( $trail, $this->ancestor_crumbs( $post->ID ) );
				$trail[] = $this->crumb( get_the_title( $post ) );
				return $trail;
			}

			// Posts and custom types: [archive] › [term ancestors › term] › title.
			if ( 'yes' === ( $settings['show_type_archive'] ?? 'yes' ) ) {
				$archive = $this->archive_crumb( $type, $settings );
				if ( $archive ) {
					$trail[] = $archive;
				}
			}

			if ( is_post_type_hierarchical( $type ) ) {
				$trail = array_merge( $trail, $this->ancestor_crumbs( $post->ID ) );
			} else {
				$taxonomy = $this->pick_taxonomy( $type );
				if ( '' !== $taxonomy ) {
					$term = $this->pick_term( $post->ID, $taxonomy );
					if ( $term ) {
						$trail = array_merge( $trail, $this->term_crumbs( $term, true ) );
					}
				}
			}

			$trail[] = $this->crumb( get_the_title( $post ) );
			return $trail;
		}

		return $trail;
	}

	/**
	 * A stand-in trail for the editor when there is no page context, built
	 * from a real page hierarchy when the site has one.
	 *
	 * @param array $home Home crumb.
	 * @return array[]
	 */
	protected function sample_trail( $home ) {
		$child = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'numberposts'    => 1,
				'post_parent__not_in' => array( 0 ),
			)
		);

		if ( ! empty( $child ) ) {
			$trail   = array( $home );
			$trail   = array_merge( $trail, $this->ancestor_crumbs( $child[0]->ID ) );
			$trail[] = $this->crumb( get_the_title( $child[0] ) );
			return $trail;
		}

		return array(
			$home,
			$this->crumb( __( 'Section', 'elementor-animatepro' ), '#' ),
			$this->crumb( __( 'Current Page', 'elementor-animatepro' ) ),
		);
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	/**
	 * Render.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$editor   = $this->eap_is_editor();

		$trail = $this->eap_build_trail( $settings );

		// Only Home means no context to build from — the editor canvas for a
		// header template, say. Show something designable.
		if ( $editor && count( $trail ) <= 1 && ! is_front_page() ) {
			$trail = $this->sample_trail( $this->crumb( $settings['home_label'] ?? __( 'Home', 'elementor-animatepro' ), home_url( '/' ) ) );
		}

		if ( empty( $trail ) ) {
			return;
		}

		if ( 'yes' !== ( $settings['show_home'] ?? 'yes' ) && count( $trail ) > 1 ) {
			array_shift( $trail );
		}

		$show_current = 'yes' === ( $settings['show_current'] ?? 'yes' );
		if ( ! $show_current && count( $trail ) > 1 ) {
			array_pop( $trail );
			// The new last crumb keeps its link: it is not the current page.
		}

		$max = (int) ( $settings['max_length'] ?? 0 );
		if ( $max > 0 && $show_current ) {
			$last = count( $trail ) - 1;
			if ( mb_strlen( $trail[ $last ]['label'] ) > $max ) {
				$trail[ $last ]['label'] = rtrim( mb_substr( $trail[ $last ]['label'], 0, $max ) ) . '…';
			}
		}

		$separator = $this->separator_html( $settings );
		$last      = count( $trail ) - 1;

		$this->add_render_attribute( 'wrapper', 'class', array( 'eap-widget', 'eap-bc' ), true );
		$this->add_render_attribute( 'wrapper', 'aria-label', __( 'Breadcrumb', 'elementor-animatepro' ), true );
		?>
		<nav <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php $this->render_prefix( $settings ); ?>
			<ol class="eap-bc__list">
				<?php foreach ( $trail as $i => $crumb ) : ?>
					<li class="eap-bc__item<?php echo $i === $last ? ' is-current' : ''; ?>">
						<?php if ( $i > 0 ) : ?>
							<span class="eap-bc__sep" aria-hidden="true"><?php echo $separator; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts. ?></span>
						<?php endif; ?>
						<?php if ( '' !== $crumb['url'] && $i !== $last ) : ?>
							<a class="eap-bc__link" href="<?php echo esc_url( $crumb['url'] ); ?>"><?php echo esc_html( $crumb['label'] ); ?></a>
						<?php elseif ( '' !== $crumb['url'] ) : ?>
							<a class="eap-bc__link eap-bc__current" href="<?php echo esc_url( $crumb['url'] ); ?>" aria-current="page"><?php echo esc_html( $crumb['label'] ); ?></a>
						<?php else : ?>
							<span class="eap-bc__current" aria-current="page"><?php echo esc_html( $crumb['label'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		</nav>
		<?php
		if ( 'yes' === ( $settings['json_ld'] ?? '' ) && ! $editor ) {
			$this->render_json_ld( $trail );
		}
	}

	/**
	 * Separator markup, escaped.
	 *
	 * @param array $settings Settings.
	 * @return string
	 */
	protected function separator_html( $settings ) {
		if ( 'icon' === ( $settings['separator_type'] ?? 'text' ) && ! empty( $settings['separator_icon']['value'] ) ) {
			ob_start();
			Icons_Manager::render_icon( $settings['separator_icon'], array( 'aria-hidden' => 'true' ) );
			return ob_get_clean();
		}
		return esc_html( $settings['separator_text'] ?? '/' );
	}

	/**
	 * Prefix markup.
	 *
	 * @param array $settings Settings.
	 * @return void
	 */
	protected function render_prefix( $settings ) {
		$type = $settings['prefix_type'] ?? 'none';

		if ( 'icon' === $type && ! empty( $settings['prefix_icon']['value'] ) ) {
			?>
			<a class="eap-bc__prefix eap-bc__prefix--icon" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php Icons_Manager::render_icon( $settings['prefix_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				<span class="screen-reader-text"><?php echo esc_html( $settings['home_label'] ?? __( 'Home', 'elementor-animatepro' ) ); ?></span>
			</a>
			<?php
		} elseif ( 'text' === $type && '' !== trim( (string) ( $settings['prefix_text'] ?? '' ) ) ) {
			?>
			<span class="eap-bc__prefix eap-bc__prefix--text"><?php echo esc_html( $settings['prefix_text'] ); ?></span>
			<?php
		}
	}

	/**
	 * Schema.org BreadcrumbList.
	 *
	 * The last item carries no `item` when it has no URL, which the spec allows
	 * for the current page.
	 *
	 * @param array[] $trail Trail.
	 * @return void
	 */
	protected function render_json_ld( $trail ) {
		$items = array();
		foreach ( $trail as $i => $crumb ) {
			$item = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => wp_strip_all_tags( $crumb['label'] ),
			);
			if ( '' !== $crumb['url'] ) {
				$item['item'] = $crumb['url'];
			}
			$items[] = $item;
		}

		$data = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		);

		echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
	}
}
