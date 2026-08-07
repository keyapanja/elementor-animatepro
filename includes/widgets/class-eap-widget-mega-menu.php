<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;

/**
 * Mega Menu — a horizontal menu bar whose top-level items open large,
 * multi-column panels. The whole menu is authored in one ordered list
 * (the "mega_nodes" repeater) where order = layout: a Top-level item is
 * followed by the Columns / Links / Promo blocks that make up its panel.
 * Each top item's panel is either built from those columns or rendered from
 * a saved Elementor template. Desktop panels open on hover or click; below
 * the breakpoint mega-menu.js collapses everything to a hamburger with an
 * accordion drawer or a full-screen overlay.
 */
class EAP_Widget_Mega_Menu extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-mega-menu';
	}

	public function get_title() {
		return __( 'Mega Menu', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_keywords() {
		return array( 'mega', 'menu', 'navigation', 'header', 'dropdown', 'panel', 'columns', 'hamburger' );
	}

	public function get_style_depends() {
		return array_merge(
			$this->get_widget_style_depends( 'mega-menu' ),
			array( 'elementor-icons-fa-solid', 'elementor-icons-fa-brands' )
		);
	}

	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-mega-menu-script' );
	}

	/**
	 * Saved Elementor templates as id => title.
	 *
	 * @return array
	 */
	protected function get_template_options() {
		$options = array( '' => __( '— Select a Template —', 'elementor-animatepro' ) );

		$templates = get_posts(
			array(
				'post_type'      => 'elementor_library',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);

		foreach ( $templates as $template ) {
			$options[ $template->ID ] = $template->post_title ? $template->post_title : sprintf( '#%d', $template->ID );
		}

		return $options;
	}

	protected function register_controls() {
		$this->register_menu_section();
		$this->register_content_section();
		$this->register_mobile_section();

		$this->register_bar_style();
		$this->register_top_style();
		$this->register_pointer_style();
		$this->register_panel_style();
		$this->register_column_style();
		$this->register_link_style();
		$this->register_promo_style();
		$this->register_toggle_style();
		$this->register_close_style();
		$this->register_mobile_style();
	}

	/* =====================================================================
	 * CONTENT — Menu
	 * ================================================================== */

	protected function register_menu_section() {
		$this->start_controls_section(
			'section_menu',
			array(
				'label' => __( 'Menu', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'trigger',
			array(
				'label'   => __( 'Open Panels On', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => array(
					'hover' => __( 'Hover', 'elementor-animatepro' ),
					'click' => __( 'Click', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'                => __( 'Bar Alignment', 'elementor-animatepro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'left',
				'options'              => array(
					'left'    => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justified', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'selectors_dictionary' => array(
					'left'    => 'flex-start',
					'center'  => 'center',
					'right'   => 'flex-end',
					'justify' => 'space-between',
				),
				'selectors'            => array(
					'{{WRAPPER}} .eap-mega-menu:not(.is-mobile) > .eap-mega-menu__list' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'show_indicator',
			array(
				'label'        => __( 'Submenu Indicator', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'indicator_icon',
			array(
				'label'       => __( 'Indicator Icon', 'elementor-animatepro' ),
				'type'        => Controls_Manager::ICONS,
				'description' => __( 'Optional — defaults to a chevron.', 'elementor-animatepro' ),
				'condition'   => array( 'show_indicator' => 'yes' ),
			)
		);

		$this->add_control(
			'pointer',
			array(
				'label'   => __( 'Hover Pointer', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'underline',
				'options' => array(
					'none'       => __( 'None', 'elementor-animatepro' ),
					'underline'  => __( 'Underline', 'elementor-animatepro' ),
					'overline'   => __( 'Overline', 'elementor-animatepro' ),
					'background' => __( 'Background', 'elementor-animatepro' ),
					'text'       => __( 'Text Color', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'panel_animation',
			array(
				'label'     => __( 'Panel Animation', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'slide',
				'options'   => array(
					'none'  => __( 'None', 'elementor-animatepro' ),
					'fade'  => __( 'Fade', 'elementor-animatepro' ),
					'slide' => __( 'Fade + Slide', 'elementor-animatepro' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'panel_anim_duration',
			array(
				'label'       => __( 'Animation Duration (ms)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 1200,
				'step'        => 10,
				'default'     => 250,
				'selectors'   => array(
					'{{WRAPPER}} .eap-mega-menu' => '--eap-mm-anim: {{VALUE}}ms;',
				),
				'condition'   => array( 'panel_animation!' => 'none' ),
			)
		);

		$this->add_control(
			'panel_align',
			array(
				'label'       => __( 'Panel Alignment', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'item',
				'options'     => array(
					'item'   => __( 'Align to menu item', 'elementor-animatepro' ),
					'center' => __( 'Centre in container', 'elementor-animatepro' ),
					'left'   => __( 'Container left', 'elementor-animatepro' ),
				),
				'description' => __( 'Where a Container-width panel sits when it is narrower than the container (via Max Width). Full-width and dropdown panels ignore this.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'hover_delay',
			array(
				'label'       => __( 'Hover Close Delay (ms)', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 800,
				'step'        => 10,
				'default'     => 160,
				'description' => __( 'Grace period before a panel closes when the pointer leaves it.', 'elementor-animatepro' ),
				'condition'   => array( 'trigger' => 'hover' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — Items & panels
	 * ================================================================== */

	protected function register_content_section() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Menu Content', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'content_help',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Build the menu as one ordered list. Add a <strong>Top-level item</strong>, then the <strong>Column</strong>, <strong>Link</strong> and <strong>Promo</strong> rows below it make up that item\'s panel. Order = layout. For a template-powered panel, set a Top-level item\'s source to <strong>Template</strong>.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'node_type',
			array(
				'label'   => __( 'Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'top'    => __( '● Top-level item', 'elementor-animatepro' ),
					'column' => __( '▸ Column (heading)', 'elementor-animatepro' ),
					'link'   => __( '— Link', 'elementor-animatepro' ),
					'promo'  => __( '★ Promo / CTA', 'elementor-animatepro' ),
				),
			)
		);

		/* --- Top-level item --- */
		$repeater->add_control(
			'top_label',
			array(
				'label'       => __( 'Label', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Menu Item', 'elementor-animatepro' ),
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array( 'node_type' => 'top' ),
			)
		);

		$repeater->add_control(
			'top_icon',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'node_type' => 'top' ),
			)
		);

		$repeater->add_control(
			'top_link',
			array(
				'label'         => __( 'Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://example.com',
				'condition'     => array( 'node_type' => 'top' ),
			)
		);

		$repeater->add_control(
			'top_enable_panel',
			array(
				'label'        => __( 'Has Panel', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'node_type' => 'top' ),
			)
		);

		$repeater->add_control(
			'top_panel_source',
			array(
				'label'     => __( 'Panel Source', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'columns',
				'options'   => array(
					'columns'  => __( 'Columns (below)', 'elementor-animatepro' ),
					'template' => __( 'Elementor Template', 'elementor-animatepro' ),
				),
				'condition' => array( 'node_type' => 'top', 'top_enable_panel' => 'yes' ),
			)
		);

		$repeater->add_control(
			'top_template',
			array(
				'label'       => __( 'Template', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->get_template_options(),
				'label_block' => true,
				'condition'   => array( 'node_type' => 'top', 'top_enable_panel' => 'yes', 'top_panel_source' => 'template' ),
			)
		);

		$repeater->add_control(
			'top_panel_width',
			array(
				'label'       => __( 'Panel Width', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'container',
				'options'     => array(
					'container' => __( 'Container width', 'elementor-animatepro' ),
					'full'      => __( 'Full width (viewport)', 'elementor-animatepro' ),
					'dropdown'  => __( 'Dropdown (under item)', 'elementor-animatepro' ),
				),
				'description' => __( 'Container width follows the parent container (capped by Panel → Max Width). Full width spans the whole viewport.', 'elementor-animatepro' ),
				'condition'   => array( 'node_type' => 'top', 'top_enable_panel' => 'yes' ),
			)
		);

		/* --- Column --- */
		$repeater->add_control(
			'col_heading',
			array(
				'label'       => __( 'Column Heading', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Heading', 'elementor-animatepro' ),
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array( 'node_type' => 'column' ),
			)
		);

		$repeater->add_control(
			'col_heading_icon',
			array(
				'label'     => __( 'Heading Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'node_type' => 'column' ),
			)
		);

		$repeater->add_control(
			'col_heading_link',
			array(
				'label'       => __( 'Heading Link', 'elementor-animatepro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com',
				'condition'   => array( 'node_type' => 'column' ),
			)
		);

		/* --- Link --- */
		$repeater->add_control(
			'link_icon',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'node_type' => 'link' ),
			)
		);

		$repeater->add_control(
			'link_label',
			array(
				'label'       => __( 'Label', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Link', 'elementor-animatepro' ),
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array( 'node_type' => 'link' ),
			)
		);

		$repeater->add_control(
			'link_desc',
			array(
				'label'       => __( 'Description', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array( 'node_type' => 'link' ),
			)
		);

		$repeater->add_control(
			'link_badge',
			array(
				'label'     => __( 'Badge', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'condition' => array( 'node_type' => 'link' ),
			)
		);

		$repeater->add_control(
			'link_url',
			array(
				'label'       => __( 'Link', 'elementor-animatepro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com',
				'condition'   => array( 'node_type' => 'link' ),
			)
		);

		/* --- Promo --- */
		$repeater->add_control(
			'promo_image',
			array(
				'label'     => __( 'Image', 'elementor-animatepro' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'node_type' => 'promo' ),
			)
		);

		$repeater->add_control(
			'promo_title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array( 'node_type' => 'promo' ),
			)
		);

		$repeater->add_control(
			'promo_text',
			array(
				'label'     => __( 'Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'dynamic'   => array( 'active' => true ),
				'rows'      => 3,
				'condition' => array( 'node_type' => 'promo' ),
			)
		);

		$repeater->add_control(
			'promo_btn_text',
			array(
				'label'     => __( 'Button Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'condition' => array( 'node_type' => 'promo' ),
			)
		);

		$repeater->add_control(
			'promo_btn_link',
			array(
				'label'       => __( 'Button Link', 'elementor-animatepro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com',
				'condition'   => array( 'node_type' => 'promo' ),
			)
		);

		$this->add_control(
			'mega_nodes',
			array(
				'label'       => __( 'Content', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->get_default_nodes(),
				'title_field' => '<# var names={top:"' . esc_js( __( 'Item', 'elementor-animatepro' ) ) . '",column:"' . esc_js( __( 'Column', 'elementor-animatepro' ) ) . '",link:"' . esc_js( __( 'Link', 'elementor-animatepro' ) ) . '",promo:"' . esc_js( __( 'Promo', 'elementor-animatepro' ) ) . '"}; var labels={top:top_label,column:col_heading,link:link_label,promo:promo_title}; var nm=names[node_type]||"Node"; var lb=labels[node_type]||""; #>{{{ nm }}}{{{ lb ? " — " + lb : "" }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Starter content so a dropped widget shows a real mega panel.
	 *
	 * @return array
	 */
	protected function get_default_nodes() {
		return array(
			array(
				'node_type'        => 'top',
				'top_label'        => __( 'Products', 'elementor-animatepro' ),
				'top_enable_panel' => 'yes',
				'top_panel_source' => 'columns',
				'top_panel_width'  => 'container',
			),
			array(
				'node_type'   => 'column',
				'col_heading' => __( 'Analytics', 'elementor-animatepro' ),
			),
			array(
				'node_type'  => 'link',
				'link_label' => __( 'Dashboards', 'elementor-animatepro' ),
				'link_desc'  => __( 'See everything at a glance', 'elementor-animatepro' ),
			),
			array(
				'node_type'  => 'link',
				'link_label' => __( 'Reports', 'elementor-animatepro' ),
				'link_desc'  => __( 'Build & schedule reports', 'elementor-animatepro' ),
			),
			array(
				'node_type'   => 'column',
				'col_heading' => __( 'Automation', 'elementor-animatepro' ),
			),
			array(
				'node_type'  => 'link',
				'link_label' => __( 'Workflows', 'elementor-animatepro' ),
				'link_desc'  => __( 'Automate the busywork', 'elementor-animatepro' ),
			),
			array(
				'node_type'  => 'link',
				'link_label' => __( 'Integrations', 'elementor-animatepro' ),
				'link_badge' => __( 'New', 'elementor-animatepro' ),
			),
			array(
				'node_type'      => 'promo',
				'promo_title'    => __( 'Go Pro', 'elementor-animatepro' ),
				'promo_text'     => __( 'Unlock every feature with a 14-day trial.', 'elementor-animatepro' ),
				'promo_btn_text' => __( 'Start free trial', 'elementor-animatepro' ),
			),
			array(
				'node_type'        => 'top',
				'top_label'        => __( 'Solutions', 'elementor-animatepro' ),
				'top_enable_panel' => 'yes',
				'top_panel_source' => 'columns',
				'top_panel_width'  => 'container',
			),
			array(
				'node_type'   => 'column',
				'col_heading' => __( 'By team', 'elementor-animatepro' ),
			),
			array(
				'node_type'  => 'link',
				'link_label' => __( 'Marketing', 'elementor-animatepro' ),
			),
			array(
				'node_type'  => 'link',
				'link_label' => __( 'Engineering', 'elementor-animatepro' ),
			),
			array(
				'node_type'        => 'top',
				'top_label'        => __( 'Pricing', 'elementor-animatepro' ),
				'top_enable_panel' => '',
			),
		);
	}

	/* =====================================================================
	 * CONTENT — Mobile
	 * ================================================================== */

	protected function register_mobile_section() {
		$this->start_controls_section(
			'section_mobile',
			array(
				'label' => __( 'Mobile', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'breakpoint',
			array(
				'label'       => __( 'Breakpoint', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '1024',
				'options'     => array(
					'768'  => __( 'Mobile (≤ 768px)', 'elementor-animatepro' ),
					'1024' => __( 'Tablet (≤ 1024px)', 'elementor-animatepro' ),
					'0'    => __( 'Never (always full menu)', 'elementor-animatepro' ),
				),
				'description' => __( 'Below this width the menu collapses to a hamburger.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'mobile_mode',
			array(
				'label'     => __( 'Opens As', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'accordion',
				'options'   => array(
					'accordion'  => __( 'Accordion Drawer', 'elementor-animatepro' ),
					'fullscreen' => __( 'Full-screen Overlay', 'elementor-animatepro' ),
				),
				'condition' => array( 'breakpoint!' => '0' ),
			)
		);

		$this->add_control(
			'mobile_align',
			array(
				'label'                => __( 'Items Alignment', 'elementor-animatepro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'left',
				'options'              => array(
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
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
				'selectors'            => array(
					'{{WRAPPER}} .eap-mega-menu.is-mobile .eap-mega-menu__list' => '--eap-mm-mobile-align: {{VALUE}};',
				),
				'condition'            => array( 'breakpoint!' => '0' ),
			)
		);

		$this->add_control(
			'fullscreen_position',
			array(
				'label'     => __( 'Menu Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top',
				'options'   => array(
					'top'    => __( 'Top', 'elementor-animatepro' ),
					'center' => __( 'Center', 'elementor-animatepro' ),
					'bottom' => __( 'Bottom', 'elementor-animatepro' ),
				),
				'condition' => array( 'mobile_mode' => 'fullscreen', 'breakpoint!' => '0' ),
			)
		);

		$this->add_control(
			'close_placement',
			array(
				'label'     => __( 'Close Icon Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top-right',
				'options'   => array(
					'top-right'  => __( 'Top Right', 'elementor-animatepro' ),
					'top-left'   => __( 'Top Left', 'elementor-animatepro' ),
					'top-center' => __( 'Top Center', 'elementor-animatepro' ),
				),
				'condition' => array( 'mobile_mode' => 'fullscreen', 'breakpoint!' => '0' ),
			)
		);

		$this->add_control(
			'close_icon',
			array(
				'label'       => __( 'Close Icon', 'elementor-animatepro' ),
				'type'        => Controls_Manager::ICONS,
				'description' => __( 'Optional — defaults to ✕.', 'elementor-animatepro' ),
				'condition'   => array( 'mobile_mode' => 'fullscreen', 'breakpoint!' => '0' ),
			)
		);

		$this->add_responsive_control(
			'toggle_align',
			array(
				'label'                => __( 'Toggle Alignment', 'elementor-animatepro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'right',
				'options'              => array(
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
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
				'selectors'            => array(
					'{{WRAPPER}} .eap-mega-menu.is-mobile' => 'justify-content: {{VALUE}};',
				),
				'condition'            => array( 'breakpoint!' => '0' ),
			)
		);

		$this->add_control(
			'toggle_icon',
			array(
				'label'       => __( 'Toggle Icon', 'elementor-animatepro' ),
				'type'        => Controls_Manager::ICONS,
				'description' => __( 'Optional — defaults to a hamburger.', 'elementor-animatepro' ),
				'condition'   => array( 'breakpoint!' => '0' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE — Bar
	 * ================================================================== */

	protected function register_bar_style() {
		$this->start_controls_section(
			'section_bar_style',
			array(
				'label' => __( 'Bar', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bar_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu:not(.is-mobile) > .eap-mega-menu__list' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'bar_gap',
			array(
				'label'      => __( 'Item Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 4, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu:not(.is-mobile) > .eap-mega-menu__list' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bar_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu:not(.is-mobile) > .eap-mega-menu__list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'bar_border',
				'selector' => '{{WRAPPER}} .eap-mega-menu:not(.is-mobile) > .eap-mega-menu__list',
			)
		);

		$this->add_responsive_control(
			'bar_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu:not(.is-mobile) > .eap-mega-menu__list' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE — Top-level items
	 * ================================================================== */

	protected function register_top_style() {
		$this->start_controls_section(
			'section_top_style',
			array(
				'label' => __( 'Top Items', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'top_typography',
				'selector' => '{{WRAPPER}} .eap-mega-menu__top-link',
			)
		);

		$this->start_controls_tabs( 'top_state_tabs' );

		$this->start_controls_tab( 'top_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );
		$this->add_control(
			'top_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1f2937',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__list > .eap-mega-menu__item > .eap-mega-menu__row > .eap-mega-menu__top-link' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'top_hover', array( 'label' => __( 'Hover / Active', 'elementor-animatepro' ) ) );
		$this->add_control(
			'top_color_hover',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__list > .eap-mega-menu__item > .eap-mega-menu__row > .eap-mega-menu__top-link:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-mega-menu__list > .eap-mega-menu__item.is-active > .eap-mega-menu__row > .eap-mega-menu__top-link' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'top_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu__top-link .eap-mega-menu__icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-mega-menu__top-link .eap-mega-menu__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->add_responsive_control(
			'top_padding',
			array(
				'label'      => __( 'Item Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 12,
					'right'    => 16,
					'bottom'   => 12,
					'left'     => 16,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu__top-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'top_indicator_color',
			array(
				'label'     => __( 'Indicator Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__indicator' => 'color: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	protected function register_pointer_style() {
		$this->start_controls_section(
			'section_pointer_style',
			array(
				'label'     => __( 'Pointer', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'pointer!' => array( 'none', 'text' ) ),
			)
		);

		$this->add_control(
			'pointer_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu' => '--eap-mm-pointer-color: {{VALUE}};',
				),
				'condition' => array( 'pointer!' => 'background' ),
			)
		);

		$this->add_responsive_control(
			'pointer_weight',
			array(
				'label'      => __( 'Thickness', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 1, 'max' => 12 ) ),
				'default'    => array( 'size' => 2, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu' => '--eap-mm-pointer-weight: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'pointer' => array( 'underline', 'overline' ) ),
			)
		);

		$this->add_control(
			'pointer_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eef2ff',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu--pointer-background .eap-mega-menu__list > .eap-mega-menu__item > .eap-mega-menu__row > .eap-mega-menu__top-link' => '--eap-mm-pointer-bg: {{VALUE}};',
				),
				'condition' => array( 'pointer' => 'background' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE — Panel
	 * ================================================================== */

	protected function register_panel_style() {
		$this->start_controls_section(
			'section_panel_style',
			array(
				'label' => __( 'Panel', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'panel_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__panel' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'panel_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 28,
					'right'    => 28,
					'bottom'   => 28,
					'left'     => 28,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu__panel-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'panel_col_gap',
			array(
				'label'      => __( 'Column Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'size' => 32, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu__panel-inner' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'panel_max_width',
			array(
				'label'       => __( 'Max Width', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 480, 'max' => 1600 ) ),
				'default'     => array( 'size' => 1200, 'unit' => 'px' ),
				'selectors'   => array(
					'{{WRAPPER}} .eap-mega-menu' => '--eap-mm-max-width: {{SIZE}}{{UNIT}};',
				),
				'description' => __( 'Caps “Container width” panels; use Menu → Panel Alignment to place them. Full-width panels ignore this; clear it to fill the container.', 'elementor-animatepro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'panel_border',
				'selector' => '{{WRAPPER}} .eap-mega-menu__panel',
			)
		);

		$this->add_responsive_control(
			'panel_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu:not(.is-mobile) .eap-mega-menu__panel--dropdown, {{WRAPPER}} .eap-mega-menu:not(.is-mobile) .eap-mega-menu__panel--container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'description' => __( 'Applies to dropdown and container-width panels.', 'elementor-animatepro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'panel_shadow',
				'selector' => '{{WRAPPER}} .eap-mega-menu:not(.is-mobile) .eap-mega-menu__panel',
				'fields_options' => array(
					'box_shadow_type' => array( 'default' => 'yes' ),
					'box_shadow'      => array(
						'default' => array(
							'horizontal' => 0,
							'vertical'   => 18,
							'blur'       => 40,
							'spread'     => 0,
							'color'      => 'rgba(0,0,0,0.12)',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_column_style() {
		$this->start_controls_section(
			'section_column_style',
			array(
				'label' => __( 'Column Headings', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'colhead_typography',
				'selector' => '{{WRAPPER}} .eap-mega-menu__col-heading',
			)
		);

		$this->add_control(
			'colhead_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__col-heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'colhead_spacing',
			array(
				'label'      => __( 'Spacing Below', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu__col-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'colhead_divider',
			array(
				'label'        => __( 'Divider', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'colhead_divider_color',
			array(
				'label'     => __( 'Divider Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e5e7eb',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__col-heading' => 'border-bottom: 1px solid {{VALUE}}; padding-bottom: 8px;',
				),
				'condition' => array( 'colhead_divider' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_link_style() {
		$this->start_controls_section(
			'section_link_style',
			array(
				'label' => __( 'Panel Links', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'label'    => __( 'Label Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-mega-menu__link-label',
			)
		);

		$this->start_controls_tabs( 'link_state_tabs' );

		$this->start_controls_tab( 'link_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );
		$this->add_control(
			'link_color',
			array(
				'label'     => __( 'Label Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#374151',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__link-label' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'link_icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__link .eap-mega-menu__icon' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'link_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );
		$this->add_control(
			'link_color_hover',
			array(
				'label'     => __( 'Label Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__link:hover .eap-mega-menu__link-label' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'link_bg_hover',
			array(
				'label'     => __( 'Row Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5f6ff',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__link:hover' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_desc_typography',
				'label'    => __( 'Description Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-mega-menu__link-desc',
			)
		);

		$this->add_control(
			'link_desc_color',
			array(
				'label'     => __( 'Description Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9ca3af',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__link-desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'link_padding',
			array(
				'label'      => __( 'Row Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 8,
					'right'    => 10,
					'bottom'   => 8,
					'left'     => 10,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->add_responsive_control(
			'link_radius',
			array(
				'label'      => __( 'Row Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 24 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu__link' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'badge_heading',
			array(
				'label'     => __( 'Badge', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'badge_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__badge' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__badge' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_promo_style() {
		$this->start_controls_section(
			'section_promo_style',
			array(
				'label' => __( 'Promo / CTA', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'promo_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f9fafb',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__promo' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'promo_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 18,
					'right'    => 18,
					'bottom'   => 18,
					'left'     => 18,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu__promo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'promo_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu__promo' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-mega-menu__promo-img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'promo_title_color',
			array(
				'label'     => __( 'Title Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__promo-title' => 'color: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'promo_title_typography',
				'label'    => __( 'Title Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-mega-menu__promo-title',
			)
		);

		$this->add_control(
			'promo_text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__promo-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'promo_btn_heading',
			array(
				'label'     => __( 'Button', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'promo_btn_tabs' );

		$this->start_controls_tab( 'promo_btn_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );
		$this->add_control(
			'promo_btn_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__promo-btn' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'promo_btn_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__promo-btn' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'promo_btn_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );
		$this->add_control(
			'promo_btn_color_hover',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__promo-btn:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'promo_btn_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4338ca',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__promo-btn:hover' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'promo_btn_padding',
			array(
				'label'      => __( 'Button Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 10,
					'right'    => 18,
					'bottom'   => 10,
					'left'     => 18,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu__promo-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'promo_btn_radius',
			array(
				'label'      => __( 'Button Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu__promo-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE — Toggle / Close / Mobile (parity with Nav Menu)
	 * ================================================================== */

	protected function register_toggle_style() {
		$this->start_controls_section(
			'section_toggle_style',
			array(
				'label'     => __( 'Hamburger Toggle', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'breakpoint!' => '0' ),
			)
		);

		$this->add_responsive_control(
			'toggle_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 14, 'max' => 48 ) ),
				'default'    => array( 'size' => 26, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu__toggle' => '--eap-mm-toggle-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'toggle_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1f2937',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__toggle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu .eap-mega-menu__toggle' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'toggle_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu .eap-mega-menu__toggle' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'toggle_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu .eap-mega-menu__toggle' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_close_style() {
		$this->start_controls_section(
			'section_close_style',
			array(
				'label'     => __( 'Close Button', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'mobile_mode' => 'fullscreen', 'breakpoint!' => '0' ),
			)
		);

		$this->add_responsive_control(
			'close_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 14, 'max' => 60 ) ),
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu__close' => '--eap-mm-close-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'close_box',
			array(
				'label'      => __( 'Box Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 90 ) ),
				'default'    => array( 'size' => 44, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu__close' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'close_tabs' );

		$this->start_controls_tab( 'close_tab_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );
		$this->add_control(
			'close_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1f2937',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__close' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'close_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu .eap-mega-menu__close' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'close_tab_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );
		$this->add_control(
			'close_color_hover',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu__close:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'close_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu .eap-mega-menu__close:hover' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'close_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 50 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu .eap-mega-menu__close' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'close_offset',
			array(
				'label'      => __( 'Distance From Edge', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 18, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-mega-menu' => '--eap-mm-close-offset: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_mobile_style() {
		$this->start_controls_section(
			'section_mobile_style',
			array(
				'label'     => __( 'Mobile Panel', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'breakpoint!' => '0' ),
			)
		);

		$this->add_control(
			'mobile_bg',
			array(
				'label'     => __( 'Panel Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu.is-mobile .eap-mega-menu__list' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'mobile_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu.is-mobile .eap-mega-menu__top-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'mobile_overlay_bg',
			array(
				'label'     => __( 'Full-screen Overlay', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.98)',
				'selectors' => array(
					'{{WRAPPER}} .eap-mega-menu--mobile-fullscreen.is-mobile .eap-mega-menu__list' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'mobile_mode' => 'fullscreen' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$nodes    = isset( $settings['mega_nodes'] ) && is_array( $settings['mega_nodes'] ) ? $settings['mega_nodes'] : array();
		$is_editor = class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode();

		$items = $this->group_nodes( $nodes );

		if ( empty( $items ) ) {
			if ( $is_editor ) {
				echo '<div class="eap-mega-menu__notice">' . esc_html__( 'Add a “Top-level item” under Menu Content to build your mega menu.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$trigger     = ! empty( $settings['trigger'] ) ? $settings['trigger'] : 'hover';
		$pointer     = ! empty( $settings['pointer'] ) ? $settings['pointer'] : 'underline';
		$animation   = ! empty( $settings['panel_animation'] ) ? $settings['panel_animation'] : 'slide';
		$mobile_mode = ! empty( $settings['mobile_mode'] ) ? $settings['mobile_mode'] : 'accordion';
		$breakpoint  = isset( $settings['breakpoint'] ) ? (int) $settings['breakpoint'] : 1024;
		$indicator   = 'yes' === ( $settings['show_indicator'] ?? 'yes' );
		$fs_position = ! empty( $settings['fullscreen_position'] ) ? $settings['fullscreen_position'] : 'top';
		$close_place = ! empty( $settings['close_placement'] ) ? $settings['close_placement'] : 'top-right';
		$hover_delay = isset( $settings['hover_delay'] ) && '' !== $settings['hover_delay'] ? (int) $settings['hover_delay'] : 160;
		$panel_align = ! empty( $settings['panel_align'] ) ? $settings['panel_align'] : 'item';
		$chevron     = $this->get_chevron( $settings );

		$classes = array(
			'eap-widget',
			'eap-mega-menu',
			'eap-mega-menu--trigger-' . $trigger,
			'eap-mega-menu--pointer-' . $pointer,
			'eap-mega-menu--anim-' . $animation,
			'eap-mega-menu--mobile-' . $mobile_mode,
		);
		if ( $indicator ) {
			$classes[] = 'eap-mega-menu--indicator';
		}
		if ( 'fullscreen' === $mobile_mode ) {
			$classes[] = 'eap-mega-menu--fs-' . $fs_position;
			$classes[] = 'eap-mega-menu--close-' . $close_place;
		}

		$list_id = 'eap-mega-menu-' . $this->get_id();

		$this->add_render_attribute(
			'nav',
			array(
				'class'            => $classes,
				'data-eap-mega-menu' => '',
				'data-trigger'     => $trigger,
				'data-breakpoint'  => (string) $breakpoint,
				'data-mobile-mode' => $mobile_mode,
				'data-hover-delay' => (string) $hover_delay,
				'data-panel-align' => $panel_align,
				'aria-label'       => __( 'Main menu', 'elementor-animatepro' ),
			)
		);
		?>
		<nav <?php $this->print_render_attribute_string( 'nav' ); ?>>
			<?php if ( 0 !== $breakpoint ) : ?>
				<button type="button" class="eap-mega-menu__toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $list_id ); ?>" aria-label="<?php esc_attr_e( 'Menu', 'elementor-animatepro' ); ?>">
					<span class="eap-mega-menu__toggle-open" aria-hidden="true"><?php echo $this->get_toggle_icon( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span class="eap-mega-menu__toggle-close" aria-hidden="true"><?php echo $this->get_close_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				</button>
			<?php endif; ?>
			<ul class="eap-mega-menu__list" id="<?php echo esc_attr( $list_id ); ?>">
				<?php
				foreach ( $items as $index => $item ) {
					$this->render_item( $item, $index, $indicator, $chevron, $is_editor );
				}
				?>
			</ul>
			<?php if ( 'fullscreen' === $mobile_mode && 0 !== $breakpoint ) : ?>
				<button type="button" class="eap-mega-menu__close" aria-label="<?php esc_attr_e( 'Close menu', 'elementor-animatepro' ); ?>"><?php echo $this->get_close_button_icon( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			<?php endif; ?>
		</nav>
		<?php
	}

	/**
	 * Turn the flat node list into top-level items with grouped panel cells.
	 *
	 * @param array $nodes Repeater rows.
	 * @return array
	 */
	protected function group_nodes( $nodes ) {
		$items       = array();
		$current     = null;
		$current_col = null;

		foreach ( $nodes as $node ) {
			$type = isset( $node['node_type'] ) ? $node['node_type'] : 'top';

			if ( 'top' === $type ) {
				if ( null !== $current ) {
					$items[] = $current;
				}
				$current = array(
					'top'   => $node,
					'cells' => array(),
				);
				$current_col = null;
				continue;
			}

			// Skip content that appears before the first top-level item.
			if ( null === $current ) {
				continue;
			}

			if ( 'column' === $type ) {
				$current['cells'][] = array(
					'kind'    => 'column',
					'heading' => $node,
					'links'   => array(),
				);
				$current_col = count( $current['cells'] ) - 1;
				continue;
			}

			if ( 'link' === $type ) {
				if ( null === $current_col ) {
					// Links before any column → implicit headless column.
					$current['cells'][] = array(
						'kind'    => 'column',
						'heading' => null,
						'links'   => array(),
					);
					$current_col = count( $current['cells'] ) - 1;
				}
				$current['cells'][ $current_col ]['links'][] = $node;
				continue;
			}

			if ( 'promo' === $type ) {
				$current['cells'][] = array(
					'kind'  => 'promo',
					'promo' => $node,
				);
				$current_col = null;
			}
		}

		if ( null !== $current ) {
			$items[] = $current;
		}

		return $items;
	}

	protected function render_item( $item, $index, $indicator, $chevron, $is_editor ) {
		$top          = $item['top'];
		$label        = isset( $top['top_label'] ) ? $top['top_label'] : '';
		$enable_panel = 'yes' === ( $top['top_enable_panel'] ?? '' );
		$source       = ! empty( $top['top_panel_source'] ) ? $top['top_panel_source'] : 'columns';
		$width        = ! empty( $top['top_panel_width'] ) ? $top['top_panel_width'] : 'full';
		$has_panel    = $enable_panel && ( 'template' === $source ? ! empty( $top['top_template'] ) : ! empty( $item['cells'] ) || $is_editor );

		$li_classes = array( 'eap-mega-menu__item' );
		if ( $has_panel ) {
			$li_classes[] = 'eap-mega-menu__item--has-panel';
		}

		$link_key = 'top_link_' . $index;
		$this->add_render_attribute( $link_key, 'class', 'eap-mega-menu__top-link' );
		$this->build_link_attributes( $link_key, $top['top_link'] ?? array(), true );
		if ( $has_panel ) {
			$this->add_render_attribute( $link_key, 'aria-haspopup', 'true' );
			$this->add_render_attribute( $link_key, 'aria-expanded', 'false' );
		}
		?>
		<li class="<?php echo esc_attr( implode( ' ', $li_classes ) ); ?>">
			<div class="eap-mega-menu__row">
				<a <?php $this->print_render_attribute_string( $link_key ); ?>>
					<?php if ( ! empty( $top['top_icon']['value'] ) ) : ?>
						<span class="eap-mega-menu__icon" aria-hidden="true"><?php echo $this->render_icon( $top['top_icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>
					<span class="eap-mega-menu__top-text"><?php echo esc_html( $label ); ?></span>
					<?php if ( $has_panel && $indicator ) : ?>
						<span class="eap-mega-menu__indicator" aria-hidden="true"><?php echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>
				</a>
				<?php if ( $has_panel ) : ?>
					<button type="button" class="eap-mega-menu__sub-toggle" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle panel', 'elementor-animatepro' ); ?>">
						<span class="eap-mega-menu__sub-toggle-icon" aria-hidden="true"><?php echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</button>
				<?php endif; ?>
			</div>
			<?php if ( $has_panel ) : ?>
				<div class="eap-mega-menu__panel eap-mega-menu__panel--<?php echo esc_attr( $width ); ?>">
					<div class="eap-mega-menu__panel-inner">
						<?php
						if ( 'template' === $source ) {
							$this->render_template_panel( $top['top_template'] );
						} else {
							$this->render_columns_panel( $item['cells'] );
						}
						?>
					</div>
				</div>
			<?php endif; ?>
		</li>
		<?php
	}

	protected function render_template_panel( $template_id ) {
		$template_id = absint( $template_id );
		if ( ! $template_id || ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}
		echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	protected function render_columns_panel( $cells ) {
		foreach ( $cells as $cell ) {
			if ( 'promo' === $cell['kind'] ) {
				$this->render_promo_cell( $cell['promo'] );
			} else {
				$this->render_column_cell( $cell );
			}
		}
	}

	protected function render_column_cell( $cell ) {
		$heading = $cell['heading'];
		?>
		<div class="eap-mega-menu__col">
			<?php if ( $heading && '' !== ( $heading['col_heading'] ?? '' ) ) : ?>
				<div class="eap-mega-menu__col-heading">
					<?php if ( ! empty( $heading['col_heading_icon']['value'] ) ) : ?>
						<span class="eap-mega-menu__icon" aria-hidden="true"><?php echo $this->render_icon( $heading['col_heading_icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>
					<?php
					$heading_text = esc_html( $heading['col_heading'] );
					if ( ! empty( $heading['col_heading_link']['url'] ) ) {
						$hk = 'colhead_' . wp_rand();
						$this->build_link_attributes( $hk, $heading['col_heading_link'], false );
						echo '<a ' . $this->get_render_attribute_string( $hk ) . '>' . $heading_text . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {
						echo '<span>' . $heading_text . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $cell['links'] ) ) : ?>
				<ul class="eap-mega-menu__links">
					<?php foreach ( $cell['links'] as $i => $link ) : ?>
						<li>
							<?php
							$lk = 'link_' . wp_rand();
							$this->add_render_attribute( $lk, 'class', 'eap-mega-menu__link' );
							$this->build_link_attributes( $lk, $link['link_url'] ?? array(), false );
							?>
							<a <?php echo $this->get_render_attribute_string( $lk ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								<?php if ( ! empty( $link['link_icon']['value'] ) ) : ?>
									<span class="eap-mega-menu__icon" aria-hidden="true"><?php echo $this->render_icon( $link['link_icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<?php endif; ?>
								<span class="eap-mega-menu__link-body">
									<span class="eap-mega-menu__link-label">
										<?php echo esc_html( $link['link_label'] ?? '' ); ?>
										<?php if ( '' !== ( $link['link_badge'] ?? '' ) ) : ?>
											<span class="eap-mega-menu__badge"><?php echo esc_html( $link['link_badge'] ); ?></span>
										<?php endif; ?>
									</span>
									<?php if ( '' !== ( $link['link_desc'] ?? '' ) ) : ?>
										<span class="eap-mega-menu__link-desc"><?php echo esc_html( $link['link_desc'] ); ?></span>
									<?php endif; ?>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function render_promo_cell( $promo ) {
		$img_url = ! empty( $promo['promo_image']['url'] ) ? $promo['promo_image']['url'] : '';
		?>
		<div class="eap-mega-menu__col eap-mega-menu__promo">
			<?php if ( $img_url ) : ?>
				<img class="eap-mega-menu__promo-img" src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $promo['promo_title'] ?? '' ); ?>" loading="lazy" />
			<?php endif; ?>
			<?php if ( '' !== ( $promo['promo_title'] ?? '' ) ) : ?>
				<span class="eap-mega-menu__promo-title"><?php echo esc_html( $promo['promo_title'] ); ?></span>
			<?php endif; ?>
			<?php if ( '' !== ( $promo['promo_text'] ?? '' ) ) : ?>
				<span class="eap-mega-menu__promo-text"><?php echo esc_html( $promo['promo_text'] ); ?></span>
			<?php endif; ?>
			<?php if ( '' !== ( $promo['promo_btn_text'] ?? '' ) ) : ?>
				<?php
				$bk = 'promo_btn_' . wp_rand();
				$this->add_render_attribute( $bk, 'class', 'eap-mega-menu__promo-btn' );
				$this->build_link_attributes( $bk, $promo['promo_btn_link'] ?? array(), false );
				?>
				<a <?php echo $this->get_render_attribute_string( $bk ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $promo['promo_btn_text'] ); ?></a>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Populate href/target/rel on a render attribute key from a URL control.
	 *
	 * @param string $key       Render attribute key.
	 * @param array  $url_field Elementor URL control value.
	 * @param bool   $allow_empty Whether an empty URL should fall back to '#'.
	 * @return void
	 */
	protected function build_link_attributes( $key, $url_field, $allow_empty ) {
		$url = is_array( $url_field ) && ! empty( $url_field['url'] ) ? $url_field['url'] : '';

		if ( '' === $url ) {
			if ( $allow_empty ) {
				$this->add_render_attribute( $key, 'href', '#' );
			}
			return;
		}

		// Raw URL — Elementor's render-attribute printer escapes with esc_attr;
		// pre-escaping with esc_url here would double-encode query ampersands.
		$this->add_render_attribute( $key, 'href', $url );

		if ( is_array( $url_field ) ) {
			if ( ! empty( $url_field['is_external'] ) ) {
				$this->add_render_attribute( $key, 'target', '_blank' );
			}
			if ( ! empty( $url_field['nofollow'] ) ) {
				$this->add_render_attribute( $key, 'rel', 'nofollow' );
			}
		}
	}

	protected function render_icon( $icon ) {
		if ( empty( $icon['value'] ) ) {
			return '';
		}
		ob_start();
		Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
		return ob_get_clean();
	}

	protected function get_chevron( $settings ) {
		if ( ! empty( $settings['indicator_icon']['value'] ) ) {
			return $this->render_icon( $settings['indicator_icon'] );
		}
		return '<svg class="eap-mega-menu__chevron" width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1.5 5 5.5 9 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	}

	protected function get_toggle_icon( $settings ) {
		if ( ! empty( $settings['toggle_icon']['value'] ) ) {
			return $this->render_icon( $settings['toggle_icon'] );
		}
		return '<span class="eap-mega-menu__bars"><span></span><span></span><span></span></span>';
	}

	protected function get_close_button_icon( $settings ) {
		if ( ! empty( $settings['close_icon']['value'] ) ) {
			return $this->render_icon( $settings['close_icon'] );
		}
		return $this->get_close_svg();
	}

	protected function get_close_svg() {
		return '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 5l10 10M15 5 5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
	}
}
