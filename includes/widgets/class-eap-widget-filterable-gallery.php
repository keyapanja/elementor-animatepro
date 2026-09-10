<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;

/**
 * Filterable Gallery widget.
 *
 * Hand-picked media with filter tabs, an optional search box, a filter-aware
 * lightbox and Load More.
 *
 * WHY THIS EXISTS ALONGSIDE THE OTHER FOUR:
 *
 * - Image Gallery holds the same curated media but has NO filtering at all.
 *   Reach for it when the set is small enough to show at once.
 * - Portfolio and Filterable Posts filter content that ALREADY LIVES IN THE
 *   DATABASE — a post type and its taxonomy terms. Their tabs come from the
 *   terms the loaded posts carry.
 * - This widget is for media that is NOT in the database as posts: the author
 *   picks images and types the categories. Nothing has to be registered first.
 *
 * FILTER TABS ARE DERIVED FROM THE ITEMS, not typed twice.
 *
 * EA's own widget has a separate tab repeater plus a free-text "Control Name"
 * per item, which the author has to keep in sync by hand — a typo silently
 * drops the item out of every tab. Here the tabs ARE the item categories, so a
 * tab can never open onto an empty grid and an item can never carry a category
 * no tab shows. Tab order follows the items (drag to reorder) or the alphabet.
 *
 * The reflow is EAPFrontend.flipFilter() from core.js, shared with Portfolio
 * and Filterable Posts rather than reimplemented.
 */
class EAP_Widget_Filterable_Gallery extends EAP_Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-filterable-gallery';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Filterable Gallery', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-justified';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'gallery', 'filter', 'filterable', 'images', 'masonry', 'lightbox', 'sort', 'portfolio', 'video' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-filterable-gallery' );
	}

	/**
	 * Scripts.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-filterable-gallery-script' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_layout_section();
		$this->register_filter_section();
		$this->register_items_section();
		$this->register_load_more_section();

		$this->register_general_style();
		$this->register_filter_style();
		$this->register_item_style();
		$this->register_icon_style();
		$this->register_load_more_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	/**
	 * Layout section.
	 *
	 * @return void
	 */
	protected function register_layout_section() {
		$this->start_controls_section(
			'section_layout',
			array( 'label' => __( 'Layout', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'sibling_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'For media you pick and categorise by hand. To filter posts that are already in the database, use <strong>Portfolio</strong> or <strong>Filterable Posts</strong>; for a gallery with no filtering, use <strong>Image Gallery</strong>.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'overlay',
				'options' => array(
					'overlay'  => __( 'Overlay — caption over the image', 'elementor-animatepro' ),
					'card'     => __( 'Card — caption below the image', 'elementor-animatepro' ),
					'harmonic' => __( 'Harmonic — cards with wide feature tiles', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'grid_mode',
			array(
				'label'     => __( 'Grid Style', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'grid',
				'options'   => array(
					'grid'    => __( 'Grid — equal tiles', 'elementor-animatepro' ),
					'masonry' => __( 'Masonry — natural heights', 'elementor-animatepro' ),
				),
				'condition' => array( 'layout!' => 'harmonic' ),
			)
		);

		$this->add_control(
			'feature_every',
			array(
				'label'       => __( 'Feature Every', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 5,
				'min'         => 2,
				'max'         => 12,
				'description' => __( 'Every Nth tile spans two columns.', 'elementor-animatepro' ),
				'condition'   => array( 'layout' => 'harmonic' ),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'elementor-animatepro' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'min'            => 1,
				'max'            => 6,
				'selectors'      => array(
					'{{WRAPPER}} .eap-fg' => '--eap-fg-cols: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fg' => '--eap-fg-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => __( 'Image Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 100, 'max' => 700 ),
					'vh' => array( 'min' => 10, 'max' => 90 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 260,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fg' => '--eap-fg-media-h: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'grid_mode!' => 'masonry' ),
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'label'   => __( 'Hover Effect', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'zoom',
				'options' => array(
					'none' => __( 'None', 'elementor-animatepro' ),
					'zoom' => __( 'Zoom Image', 'elementor-animatepro' ),
					'lift' => __( 'Lift Tile', 'elementor-animatepro' ),
					'both' => __( 'Zoom & Lift', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'anim_duration',
			array(
				'label'      => __( 'Animation Duration', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'ms' ),
				'range'      => array( 'ms' => array( 'min' => 0, 'max' => 1200, 'step' => 50 ) ),
				'default'    => array(
					'unit' => 'ms',
					'size' => 400,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fg' => '--eap-fg-speed: {{SIZE}}ms;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'image_size',
				'default' => 'large',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Filter controls section.
	 *
	 * @return void
	 */
	protected function register_filter_section() {
		$this->start_controls_section(
			'section_filter',
			array( 'label' => __( 'Filter Controls', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'show_filter',
			array(
				'label'        => __( 'Filter Tabs', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'description'  => __( 'Tabs are built from the categories typed on the items below.', 'elementor-animatepro' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'all_label',
			array(
				'label'     => __( 'All Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'All', 'elementor-animatepro' ),
				'condition' => array( 'show_filter' => 'yes' ),
			)
		);

		$this->add_control(
			'tab_order',
			array(
				'label'     => __( 'Tab Order', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'appearance',
				'options'   => array(
					'appearance'   => __( 'Item order', 'elementor-animatepro' ),
					'alphabetical' => __( 'A → Z', 'elementor-animatepro' ),
				),
				'condition' => array( 'show_filter' => 'yes' ),
			)
		);

		$this->add_control(
			'show_count',
			array(
				'label'        => __( 'Show Counts', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'condition'    => array( 'show_filter' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'filter_align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
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
					'{{WRAPPER}} .eap-fg' => '--eap-fg-bar-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'show_search',
			array(
				'label'        => __( 'Search Box', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator'    => 'before',
				'description'  => __( 'Searches the item titles and captions.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'search_placeholder',
			array(
				'label'     => __( 'Search Placeholder', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Search…', 'elementor-animatepro' ),
				'condition' => array( 'show_search' => 'yes' ),
			)
		);

		$this->add_control(
			'empty_text',
			array(
				'label'     => __( 'Nothing Found Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Nothing matches that filter.', 'elementor-animatepro' ),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Gallery items section.
	 *
	 * @return void
	 */
	protected function register_items_section() {
		$this->start_controls_section(
			'section_items',
			array( 'label' => __( 'Gallery Items', 'elementor-animatepro' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_image',
			array(
				'label'   => __( 'Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$repeater->add_control(
			'item_categories',
			array(
				'label'       => __( 'Categories', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Nature, Travel', 'elementor-animatepro' ),
				'description' => __( 'Comma separated. Each one becomes a filter tab.', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'item_title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Gallery Item', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'item_content',
			array(
				'label'       => __( 'Caption', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'item_lightbox',
			array(
				'label'        => __( 'Lightbox Button', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$repeater->add_control(
			'item_video',
			array(
				'label'       => __( 'Video URL', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'https://…',
				'description' => __( 'Optional. The lightbox plays this instead of the image, using the image as the poster.', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array( 'item_lightbox' => 'yes' ),
			)
		);

		$repeater->add_control(
			'item_link_show',
			array(
				'label'        => __( 'Link Button', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$repeater->add_control(
			'item_link',
			array(
				'label'       => __( 'Link', 'elementor-animatepro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://…',
				'condition'   => array( 'item_link_show' => 'yes' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ item_title || item_categories || "Item" }}}',
				'default'     => array(
					array(
						'item_title'      => __( 'Mountain Ridge', 'elementor-animatepro' ),
						'item_categories' => __( 'Nature', 'elementor-animatepro' ),
						'item_image'      => array( 'url' => Utils::get_placeholder_image_src() ),
						'item_lightbox'   => 'yes',
					),
					array(
						'item_title'      => __( 'Harbour Lights', 'elementor-animatepro' ),
						'item_categories' => __( 'Travel', 'elementor-animatepro' ),
						'item_image'      => array( 'url' => Utils::get_placeholder_image_src() ),
						'item_lightbox'   => 'yes',
					),
					array(
						'item_title'      => __( 'Old Town', 'elementor-animatepro' ),
						'item_categories' => __( 'Travel, Heritage', 'elementor-animatepro' ),
						'item_image'      => array( 'url' => Utils::get_placeholder_image_src() ),
						'item_lightbox'   => 'yes',
					),
				),
			)
		);

		$this->add_control(
			'lightbox_icon',
			array(
				'label'     => __( 'Lightbox Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'separator' => 'before',
				'default'   => array(
					'value'   => 'eicon-zoom-in-bold',
					'library' => 'eicons',
				),
			)
		);

		$this->add_control(
			'link_icon',
			array(
				'label'   => __( 'Link Icon', 'elementor-animatepro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'eicon-editor-link',
					'library' => 'eicons',
				),
			)
		);

		$this->add_control(
			'full_image_clickable',
			array(
				'label'        => __( 'Full Image Opens Lightbox', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
				'description'  => __( 'Off: only the lightbox button opens it.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Load more section.
	 *
	 * @return void
	 */
	protected function register_load_more_section() {
		$this->start_controls_section(
			'section_load_more',
			array( 'label' => __( 'Load More', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'show_load_more',
			array(
				'label'        => __( 'Load More Button', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Every item is already on the page — this reveals them in batches, and the batch always follows the active filter.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'per_page',
			array(
				'label'     => __( 'Images Per Batch', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 6,
				'min'       => 1,
				'max'       => 48,
				'condition' => array( 'show_load_more' => 'yes' ),
			)
		);

		$this->add_control(
			'load_more_text',
			array(
				'label'     => __( 'Button Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Load More', 'elementor-animatepro' ),
				'condition' => array( 'show_load_more' => 'yes' ),
			)
		);

		$this->add_control(
			'no_more_text',
			array(
				'label'     => __( 'No More Items Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'That is everything.', 'elementor-animatepro' ),
				'condition' => array( 'show_load_more' => 'yes' ),
			)
		);

		$this->add_control(
			'load_more_icon',
			array(
				'label'     => __( 'Button Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'show_load_more' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'load_more_align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
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
					'{{WRAPPER}} .eap-fg__more' => 'justify-content: {{VALUE}};',
				),
				'condition' => array( 'show_load_more' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	/**
	 * General style.
	 *
	 * @return void
	 */
	protected function register_general_style() {
		$this->start_controls_section(
			'style_general',
			array(
				'label' => __( 'General', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'general_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'general_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'general_border',
				'selector' => '{{WRAPPER}} .eap-fg',
			)
		);

		$this->add_responsive_control(
			'general_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'general_shadow',
				'selector' => '{{WRAPPER}} .eap-fg',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Filter tab style.
	 *
	 * @return void
	 */
	protected function register_filter_style() {
		$this->start_controls_section(
			'style_filter',
			array(
				'label' => __( 'Filter Controls', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filter_typography',
				'selector' => '{{WRAPPER}} .eap-fg__filter',
			)
		);

		$this->add_responsive_control(
			'filter_gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fg' => '--eap-fg-filter-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fg__filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fg__filter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'filter_tabs' );

		$this->start_controls_tab(
			'filter_tab_normal',
			array( 'label' => __( 'Normal', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'filter_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__filter' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__filter' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_border',
				'selector' => '{{WRAPPER}} .eap-fg__filter',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'filter_tab_hover',
			array( 'label' => __( 'Hover', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'filter_color_hover',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__filter:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__filter:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_border_hover',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__filter:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'filter_tab_active',
			array( 'label' => __( 'Active', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'filter_color_active',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__filter.is-active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_bg_active',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__filter.is-active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_border_active',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__filter.is-active' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'search_heading',
			array(
				'label'     => __( 'Search Box', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'show_search' => 'yes' ),
			)
		);

		$this->add_control(
			'search_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__search' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_search' => 'yes' ),
			)
		);

		$this->add_control(
			'search_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__search' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'show_search' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'search_border',
				'selector'  => '{{WRAPPER}} .eap-fg__search',
				'condition' => array( 'show_search' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Item style.
	 *
	 * @return void
	 */
	protected function register_item_style() {
		$this->start_controls_section(
			'style_item',
			array(
				'label' => __( 'Items', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'item_bg',
			array(
				'label'     => __( 'Tile Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fg__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .eap-fg__item',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .eap-fg__item',
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Overlay', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__overlay' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'body_padding',
			array(
				'label'      => __( 'Caption Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fg__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'body_align',
			array(
				'label'     => __( 'Caption Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__body' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_heading',
			array(
				'label'     => __( 'Title', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-fg__title',
			)
		);

		$this->add_control(
			'text_heading',
			array(
				'label'     => __( 'Caption', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .eap-fg__text',
			)
		);

		$this->add_control(
			'empty_heading',
			array(
				'label'     => __( 'Nothing Found', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'empty_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__empty' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'empty_typography',
				'selector' => '{{WRAPPER}} .eap-fg__empty',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Action icon style.
	 *
	 * @return void
	 */
	protected function register_icon_style() {
		$this->start_controls_section(
			'style_icons',
			array(
				'label' => __( 'Action Icons', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fg' => '--eap-fg-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_box',
			array(
				'label'      => __( 'Button Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 80 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fg' => '--eap-fg-icon-box: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fg__action' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'icon_tabs' );

		$this->start_controls_tab(
			'icon_tab_normal',
			array( 'label' => __( 'Normal', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__action' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__action' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_tab_hover',
			array( 'label' => __( 'Hover', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'icon_color_hover',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__action:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__action:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Load more button style.
	 *
	 * @return void
	 */
	protected function register_load_more_style() {
		$this->start_controls_section(
			'style_load_more',
			array(
				'label'     => __( 'Load More Button', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_load_more' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'more_typography',
				'selector' => '{{WRAPPER}} .eap-fg__more-btn',
			)
		);

		$this->add_responsive_control(
			'more_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fg__more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'more_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-fg__more-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'more_tabs' );

		$this->start_controls_tab(
			'more_tab_normal',
			array( 'label' => __( 'Normal', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'more_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__more-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'more_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__more-btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'more_border',
				'selector' => '{{WRAPPER}} .eap-fg__more-btn',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'more_tab_hover',
			array( 'label' => __( 'Hover', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'more_color_hover',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__more-btn:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'more_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__more-btn:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'more_border_hover',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-fg__more-btn:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	/**
	 * Build the tab list and the per-item category keys.
	 *
	 * Both come from the SAME pass over the items, so the tabs and the item
	 * keys can never disagree.
	 *
	 * @param array  $items Repeater rows.
	 * @param string $order 'appearance' or 'alphabetical'.
	 * @return array {tabs: array<string,array>, map: array<int,string[]>}
	 */
	protected function build_filter_data( $items, $order ) {
		$tabs = array();
		$map  = array();

		foreach ( $items as $index => $item ) {
			$raw  = isset( $item['item_categories'] ) ? (string) $item['item_categories'] : '';
			$keys = array();

			foreach ( explode( ',', $raw ) as $part ) {
				$label = trim( $part );
				if ( '' === $label ) {
					continue;
				}

				$key = sanitize_title( $label );
				if ( '' === $key || in_array( $key, $keys, true ) ) {
					// Empty after sanitising, or the author repeated it on this
					// row — either way it must not be counted twice.
					continue;
				}

				if ( ! isset( $tabs[ $key ] ) ) {
					$tabs[ $key ] = array(
						'label' => $label,
						'count' => 0,
					);
				}

				$keys[]                = $key;
				$tabs[ $key ]['count'] = $tabs[ $key ]['count'] + 1;
			}

			$map[ $index ] = $keys;
		}

		if ( 'alphabetical' === $order ) {
			uasort(
				$tabs,
				static function ( $a, $b ) {
					return strcasecmp( $a['label'], $b['label'] );
				}
			);
		}

		return array(
			'tabs' => $tabs,
			'map'  => $map,
		);
	}

	/**
	 * Render.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = isset( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();

		if ( empty( $items ) ) {
			if ( $this->eap_is_editor() ) {
				echo '<div class="eap-widget eap-fg eap-fg--empty">'
					. esc_html__( 'Add gallery items to get started.', 'elementor-animatepro' )
					. '</div>';
			}
			return;
		}

		$layout = in_array( $settings['layout'] ?? 'overlay', array( 'overlay', 'card', 'harmonic' ), true )
			? $settings['layout']
			: 'overlay';

		// Harmonic places wide tiles, which CSS columns cannot span, so it is
		// always a real grid regardless of the (hidden) grid_mode value.
		$grid_mode = ( 'harmonic' !== $layout && 'masonry' === ( $settings['grid_mode'] ?? 'grid' ) ) ? 'masonry' : 'grid';

		$hover = in_array( $settings['hover_effect'] ?? 'zoom', array( 'none', 'zoom', 'lift', 'both' ), true )
			? $settings['hover_effect']
			: 'zoom';

		$show_filter = 'yes' === ( $settings['show_filter'] ?? 'yes' );
		$show_search = 'yes' === ( $settings['show_search'] ?? '' );
		$show_more   = 'yes' === ( $settings['show_load_more'] ?? '' );

		$filter = $this->build_filter_data( $items, $settings['tab_order'] ?? 'appearance' );

		// A single tab plus "All" is the same view twice — not worth the row.
		$has_tabs = $show_filter && count( $filter['tabs'] ) > 1;

		$feature_every = max( 2, (int) ( $settings['feature_every'] ?? 5 ) );

		$config = array(
			'duration' => (int) ( $settings['anim_duration']['size'] ?? 400 ),
			'loadMore' => $show_more,
			'perPage'  => max( 1, (int) ( $settings['per_page'] ?? 6 ) ),
		);

		$classes = array(
			'eap-widget',
			'eap-fg',
			'eap-fg--' . $layout,
			'eap-fg--' . $grid_mode,
			'eap-fg--hover-' . $hover,
		);

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		$this->add_render_attribute( 'wrapper', 'data-eap-filterable-gallery', wp_json_encode( $config ) );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $has_tabs || $show_search ) : ?>
				<div class="eap-fg__bar">
					<?php if ( $has_tabs ) : ?>
						<div class="eap-fg__filters" role="group" aria-label="<?php esc_attr_e( 'Filter gallery', 'elementor-animatepro' ); ?>">
							<button type="button" class="eap-fg__filter is-active" data-eap-fg-filter="" aria-pressed="true">
								<span class="eap-fg__filter-label"><?php echo esc_html( $settings['all_label'] ?? __( 'All', 'elementor-animatepro' ) ); ?></span>
								<?php if ( 'yes' === ( $settings['show_count'] ?? '' ) ) : ?>
									<span class="eap-fg__filter-count"><?php echo esc_html( (string) count( $items ) ); ?></span>
								<?php endif; ?>
							</button>

							<?php foreach ( $filter['tabs'] as $key => $tab ) : ?>
								<button type="button" class="eap-fg__filter" data-eap-fg-filter="<?php echo esc_attr( $key ); ?>" aria-pressed="false">
									<span class="eap-fg__filter-label"><?php echo esc_html( $tab['label'] ); ?></span>
									<?php if ( 'yes' === ( $settings['show_count'] ?? '' ) ) : ?>
										<span class="eap-fg__filter-count"><?php echo esc_html( (string) $tab['count'] ); ?></span>
									<?php endif; ?>
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( $show_search ) : ?>
						<label class="eap-fg__search">
							<span class="screen-reader-text"><?php esc_html_e( 'Search the gallery', 'elementor-animatepro' ); ?></span>
							<input
								type="search"
								class="eap-fg__search-input"
								data-eap-fg-search
								placeholder="<?php echo esc_attr( $settings['search_placeholder'] ?? __( 'Search…', 'elementor-animatepro' ) ); ?>"
							>
						</label>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="eap-fg__grid">
				<?php
				foreach ( $items as $index => $item ) {
					$this->render_item( $item, $index, $settings, $filter, $layout, $feature_every );
				}
				?>
			</div>

			<p class="eap-fg__empty" hidden>
				<?php echo esc_html( $settings['empty_text'] ?? __( 'Nothing matches that filter.', 'elementor-animatepro' ) ); ?>
			</p>

			<?php if ( $show_more ) : ?>
				<div class="eap-fg__more">
					<button type="button" class="eap-fg__more-btn" data-eap-fg-more hidden>
						<span class="eap-fg__more-label"><?php echo esc_html( $settings['load_more_text'] ?? __( 'Load More', 'elementor-animatepro' ) ); ?></span>
						<?php if ( ! empty( $settings['load_more_icon']['value'] ) ) : ?>
							<span class="eap-fg__more-icon" aria-hidden="true">
								<?php Icons_Manager::render_icon( $settings['load_more_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							</span>
						<?php endif; ?>
					</button>
					<p class="eap-fg__nomore" data-eap-fg-nomore hidden>
						<?php echo esc_html( $settings['no_more_text'] ?? __( 'That is everything.', 'elementor-animatepro' ) ); ?>
					</p>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render one gallery item.
	 *
	 * @param array  $item          Repeater row.
	 * @param int    $index         Row index.
	 * @param array  $settings      Widget settings.
	 * @param array  $filter        Filter data.
	 * @param string $layout        Resolved layout.
	 * @param int    $feature_every Feature tile interval.
	 * @return void
	 */
	protected function render_item( $item, $index, $settings, $filter, $layout, $feature_every ) {
		$image_url = ! empty( $item['item_image']['url'] ) ? $item['item_image']['url'] : '';
		if ( '' === $image_url ) {
			return;
		}

		$image_id = ! empty( $item['item_image']['id'] ) ? (int) $item['item_image']['id'] : 0;

		$title   = isset( $item['item_title'] ) ? (string) $item['item_title'] : '';
		$caption = isset( $item['item_content'] ) ? (string) $item['item_content'] : '';

		/*
		 * The sized image for the tile; the lightbox always gets the original.
		 *
		 * Not Group_Control_Image_Size::get_attachment_image_html() — that reads
		 * the image out of the WIDGET settings, and this one lives in a repeater
		 * row. wp_get_attachment_image() takes the id directly and still emits
		 * srcset/sizes, while letting us set the class and alt.
		 */
		$thumb = '';
		if ( $image_id ) {
			$size  = ! empty( $settings['image_size_size'] ) ? $settings['image_size_size'] : 'large';
			$thumb = wp_get_attachment_image(
				$image_id,
				$size,
				false,
				array(
					'class'   => 'eap-fg__img',
					'loading' => 'lazy',
					'alt'     => $title,
				)
			);
		}

		$full = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : $image_url;
		if ( ! $full ) {
			$full = $image_url;
		}

		$video = isset( $item['item_video'] ) ? trim( (string) $item['item_video'] ) : '';
		$video = '' !== $video ? esc_url_raw( $video, array( 'http', 'https' ) ) : '';

		$has_lightbox = 'yes' === ( $item['item_lightbox'] ?? '' );
		$has_link     = 'yes' === ( $item['item_link_show'] ?? '' ) && ! empty( $item['item_link']['url'] );

		$keys      = isset( $filter['map'][ $index ] ) ? $filter['map'][ $index ] : array();
		$full_open = $has_lightbox && 'yes' === ( $settings['full_image_clickable'] ?? 'yes' );

		$item_classes = array( 'eap-fg__item' );
		if ( 'harmonic' === $layout && 0 === ( ( $index + 1 ) % $feature_every ) ) {
			$item_classes[] = 'is-feature';
		}
		?>
		<figure
			class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>"
			data-eap-fg-cats="<?php echo esc_attr( implode( ' ', $keys ) ); ?>"
			data-eap-fg-full="<?php echo esc_url( $full ); ?>"
			<?php if ( '' !== $video ) : ?>
				data-eap-fg-video="<?php echo esc_url( $video ); ?>"
			<?php endif; ?>
			data-eap-fg-caption="<?php echo esc_attr( $title ); ?>"
			<?php if ( $has_lightbox ) : ?>data-eap-fg-has-lightbox="1"<?php endif; ?>
		>
			<div class="eap-fg__media<?php echo $full_open ? ' is-clickable' : ''; ?>">
				<?php
				if ( $thumb ) {
					echo wp_kses_post( $thumb );
				} else {
					printf(
						'<img class="eap-fg__img" src="%1$s" alt="%2$s" loading="lazy">',
						esc_url( $image_url ),
						esc_attr( $title )
					);
				}
				?>

				<span class="eap-fg__overlay" aria-hidden="true"></span>

				<?php if ( $has_lightbox || $has_link ) : ?>
					<div class="eap-fg__actions">
						<?php if ( $has_lightbox ) : ?>
							<button
								type="button"
								class="eap-fg__action eap-fg__action--zoom"
								data-eap-fg-zoom
								aria-label="<?php echo esc_attr( '' !== $title ? sprintf( /* translators: %s: item title. */ __( 'Open %s', 'elementor-animatepro' ), $title ) : __( 'Open item', 'elementor-animatepro' ) ); ?>"
							>
								<?php Icons_Manager::render_icon( $settings['lightbox_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							</button>
						<?php endif; ?>

						<?php
						if ( $has_link ) {
							$this->add_link_attributes( 'link_' . $index, $item['item_link'] );
							$this->add_render_attribute( 'link_' . $index, 'class', array( 'eap-fg__action', 'eap-fg__action--link' ) );
							?>
							<a <?php $this->print_render_attribute_string( 'link_' . $index ); ?>>
								<?php Icons_Manager::render_icon( $settings['link_icon'], array( 'aria-hidden' => 'true' ) ); ?>
								<span class="screen-reader-text">
									<?php echo esc_html( '' !== $title ? $title : __( 'Visit link', 'elementor-animatepro' ) ); ?>
								</span>
							</a>
							<?php
						}
						?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( '' !== $title || '' !== $caption ) : ?>
				<figcaption class="eap-fg__body">
					<?php if ( '' !== $title ) : ?>
						<h3 class="eap-fg__title"><?php echo esc_html( $title ); ?></h3>
					<?php endif; ?>
					<?php if ( '' !== $caption ) : ?>
						<div class="eap-fg__text"><?php echo wp_kses_post( $caption ); ?></div>
					<?php endif; ?>
				</figcaption>
			<?php endif; ?>
		</figure>
		<?php
	}
}
