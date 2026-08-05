<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

/**
 * Nav Menu — renders a WordPress menu (Appearance → Menus) with multi-level
 * dropdowns and an accessible mobile hamburger that opens either a dropdown
 * panel or a full-screen / slide-in overlay (chosen per instance). Desktop
 * dropdowns are CSS hover + :focus-within; nav-menu.js adds the hamburger,
 * tap-accordion submenus, keyboard/Escape support and the responsive switch.
 */
class EAP_Widget_Nav_Menu extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-nav-menu';
	}

	public function get_title() {
		return __( 'Nav Menu', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_keywords() {
		return array( 'nav', 'menu', 'navigation', 'header', 'dropdown', 'hamburger', 'mobile' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'nav-menu' );
	}

	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-nav-menu-script' );
	}

	/**
	 * Registered menus as id => name.
	 *
	 * @return array
	 */
	protected function get_menu_options() {
		$options = array( '' => __( '— Select a Menu —', 'elementor-animatepro' ) );
		if ( function_exists( 'wp_get_nav_menus' ) ) {
			foreach ( wp_get_nav_menus() as $menu ) {
				$options[ $menu->term_id ] = $menu->name;
			}
		}
		return $options;
	}

	protected function register_controls() {
		$this->register_menu_section();
		$this->register_mobile_section();

		$this->register_item_style();
		$this->register_pointer_style();
		$this->register_dropdown_style();
		$this->register_toggle_style();
		$this->register_close_style();
		$this->register_mobile_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_menu_section() {
		$this->start_controls_section(
			'section_menu',
			array(
				'label' => __( 'Menu', 'elementor-animatepro' ),
			)
		);

		$menus = $this->get_menu_options();

		if ( count( $menus ) <= 1 ) {
			$this->add_control(
				'menu_empty_note',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf(
						/* translators: %s: Menus screen link. */
						__( 'There are no menus yet. Create one under %s, then select it here.', 'elementor-animatepro' ),
						'<a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '" target="_blank">' . esc_html__( 'Appearance → Menus', 'elementor-animatepro' ) . '</a>'
					),
					'content_classes' => 'elementor-descriptor',
				)
			);
		}

		$this->add_control(
			'menu_id',
			array(
				'label'   => __( 'Select Menu', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $menus,
				'default' => '',
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => __( 'Horizontal', 'elementor-animatepro' ),
					'vertical'   => __( 'Vertical', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'                => __( 'Alignment', 'elementor-animatepro' ),
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
					'{{WRAPPER}} .eap-nav-menu:not(.is-mobile) > .eap-nav-menu__list' => 'justify-content: {{VALUE}};',
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
					'double'     => __( 'Double Line', 'elementor-animatepro' ),
					'background' => __( 'Background', 'elementor-animatepro' ),
					'text'       => __( 'Text Color', 'elementor-animatepro' ),
				),
			)
		);

		$this->end_controls_section();
	}

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
				'label'   => __( 'Breakpoint', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '768',
				'options' => array(
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
				'label'   => __( 'Opens As', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'dropdown',
				'options' => array(
					'dropdown'   => __( 'Dropdown Panel', 'elementor-animatepro' ),
					'fullscreen' => __( 'Full-screen Overlay', 'elementor-animatepro' ),
				),
				'condition' => array( 'breakpoint!' => '0' ),
			)
		);

		$this->add_control(
			'mobile_align',
			array(
				'label'                => __( 'Menu Alignment', 'elementor-animatepro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'center',
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
					'{{WRAPPER}} .eap-nav-menu.is-mobile .eap-nav-menu__row' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .eap-nav-menu--mobile-fullscreen.is-mobile > .eap-nav-menu__list' => 'align-items: {{VALUE}};',
				),
				'description'          => __( 'How the menu items align inside the mobile panel / overlay.', 'elementor-animatepro' ),
				'condition'            => array( 'breakpoint!' => '0' ),
			)
		);

		$this->add_control(
			'fullscreen_position',
			array(
				'label'       => __( 'Menu Position', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'center',
				'options'     => array(
					'top'    => __( 'Top', 'elementor-animatepro' ),
					'center' => __( 'Center', 'elementor-animatepro' ),
					'bottom' => __( 'Bottom', 'elementor-animatepro' ),
				),
				'description' => __( 'Vertical position of the menu in the overlay. Combine with Menu Alignment for corners (e.g. Top + Left).', 'elementor-animatepro' ),
				'condition'   => array( 'mobile_mode' => 'fullscreen', 'breakpoint!' => '0' ),
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
					'{{WRAPPER}} .eap-nav-menu.is-mobile' => 'justify-content: {{VALUE}};',
				),
				'condition'            => array( 'breakpoint!' => '0' ),
			)
		);

		$this->add_control(
			'toggle_icon',
			array(
				'label'     => __( 'Toggle Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'breakpoint!' => '0' ),
				'description' => __( 'Optional — defaults to a hamburger.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_item_style() {
		$this->start_controls_section(
			'section_item_style',
			array(
				'label' => __( 'Menu Items', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_typography',
				'selector' => '{{WRAPPER}} .eap-nav-menu__list .eap-nav-menu__link',
			)
		);

		$this->start_controls_tabs( 'item_state_tabs' );

		$this->start_controls_tab( 'item_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );
		$this->add_control(
			'item_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1f2937',
				'selectors' => array(
					'{{WRAPPER}} .eap-nav-menu__list > .eap-nav-menu__item > .eap-nav-menu__row > .eap-nav-menu__link' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'item_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );
		$this->add_control(
			'item_color_hover',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-nav-menu__list > .eap-nav-menu__item > .eap-nav-menu__row > .eap-nav-menu__link:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'item_active', array( 'label' => __( 'Active', 'elementor-animatepro' ) ) );
		$this->add_control(
			'item_color_active',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-nav-menu__list > .eap-nav-menu__item > .eap-nav-menu__row > .eap-nav-menu__link.is-current' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-nav-menu:not(.is-mobile) > .eap-nav-menu__list' => 'gap: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => __( 'Item Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 10,
					'right'    => 14,
					'bottom'   => 10,
					'left'     => 14,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-nav-menu__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
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
					'{{WRAPPER}} .eap-nav-menu' => '--eap-nm-pointer-color: {{VALUE}};',
				),
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
					'{{WRAPPER}} .eap-nav-menu' => '--eap-nm-pointer-weight: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'pointer!' => 'background' ),
			)
		);

		$this->add_control(
			'pointer_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eef2ff',
				'selectors' => array(
					'{{WRAPPER}} .eap-nav-menu--pointer-background .eap-nav-menu__list > .eap-nav-menu__item > .eap-nav-menu__row > .eap-nav-menu__link' => '--eap-nm-pointer-bg: {{VALUE}};',
				),
				'condition' => array( 'pointer' => 'background' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_dropdown_style() {
		$this->start_controls_section(
			'section_dropdown_style',
			array(
				'label' => __( 'Dropdown', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'dropdown_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-nav-menu__sub' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->start_controls_tabs( 'dropdown_item_tabs' );

		$this->start_controls_tab( 'dropdown_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );
		$this->add_control(
			'dropdown_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#374151',
				'selectors' => array(
					'{{WRAPPER}} .eap-nav-menu__sub .eap-nav-menu__link' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'dropdown_hover', array( 'label' => __( 'Hover', 'elementor-animatepro' ) ) );
		$this->add_control(
			'dropdown_color_hover',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-nav-menu__sub .eap-nav-menu__link:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'dropdown_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5f6ff',
				'selectors' => array(
					'{{WRAPPER}} .eap-nav-menu__sub .eap-nav-menu__row:hover' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'dropdown_width',
			array(
				'label'      => __( 'Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 140, 'max' => 400 ) ),
				'default'    => array( 'size' => 220, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-nav-menu:not(.is-mobile) .eap-nav-menu__sub' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'dropdown_border',
				'selector' => '{{WRAPPER}} .eap-nav-menu__sub',
			)
		);

		$this->add_responsive_control(
			'dropdown_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-nav-menu:not(.is-mobile) .eap-nav-menu__sub' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'dropdown_shadow',
				'selector'  => '{{WRAPPER}} .eap-nav-menu:not(.is-mobile) .eap-nav-menu__sub',
			)
		);

		$this->end_controls_section();
	}

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
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-nav-menu__toggle' => '--eap-nm-toggle-size: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .eap-nav-menu__toggle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-nav-menu .eap-nav-menu__toggle' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .eap-nav-menu .eap-nav-menu__toggle' => 'padding: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .eap-nav-menu .eap-nav-menu__toggle' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .eap-nav-menu__close' => '--eap-nm-close-size: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .eap-nav-menu__close' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .eap-nav-menu__close' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'close_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-nav-menu .eap-nav-menu__close' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .eap-nav-menu__close:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'close_bg_hover',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-nav-menu .eap-nav-menu__close:hover' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .eap-nav-menu .eap-nav-menu__close' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .eap-nav-menu' => '--eap-nm-close-offset: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .eap-nav-menu.is-mobile .eap-nav-menu__list' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'mobile_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-nav-menu.is-mobile .eap-nav-menu__link' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .eap-nav-menu--mobile-fullscreen.is-mobile .eap-nav-menu__list' => 'background-color: {{VALUE}};',
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
		$menu_id  = ! empty( $settings['menu_id'] ) ? absint( $settings['menu_id'] ) : 0;
		$is_editor = class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode();

		if ( ! $menu_id ) {
			if ( $is_editor ) {
				echo '<div class="eap-nav-menu__notice">' . esc_html__( 'Select a menu in the widget settings.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$items = wp_get_nav_menu_items( $menu_id, array( 'update_post_term_cache' => false ) );
		if ( empty( $items ) ) {
			if ( $is_editor ) {
				echo '<div class="eap-nav-menu__notice">' . esc_html__( 'This menu has no items yet.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		if ( function_exists( '_wp_menu_item_classes_by_context' ) ) {
			_wp_menu_item_classes_by_context( $items );
		}

		$children = array();
		foreach ( $items as $item ) {
			$children[ (int) $item->menu_item_parent ][] = $item;
		}
		$top = isset( $children[0] ) ? $children[0] : array();
		if ( empty( $top ) ) {
			return;
		}

		$layout      = ! empty( $settings['layout'] ) ? $settings['layout'] : 'horizontal';
		$pointer     = ! empty( $settings['pointer'] ) ? $settings['pointer'] : 'underline';
		$mobile_mode = ! empty( $settings['mobile_mode'] ) ? $settings['mobile_mode'] : 'dropdown';
		$breakpoint  = isset( $settings['breakpoint'] ) ? (int) $settings['breakpoint'] : 768;
		$indicator   = 'yes' === ( $settings['show_indicator'] ?? 'yes' );
		$fs_position = ! empty( $settings['fullscreen_position'] ) ? $settings['fullscreen_position'] : 'center';
		$close_place = ! empty( $settings['close_placement'] ) ? $settings['close_placement'] : 'top-right';

		$chevron   = $this->get_chevron( $settings );
		$menu_obj  = wp_get_nav_menu_object( $menu_id );
		$aria_name = $menu_obj ? $menu_obj->name : __( 'Menu', 'elementor-animatepro' );
		$list_id   = 'eap-nav-menu-' . $this->get_id();

		$classes = array(
			'eap-widget',
			'eap-nav-menu',
			'eap-nav-menu--' . ( 'vertical' === $layout ? 'vertical' : 'horizontal' ),
			'eap-nav-menu--pointer-' . $pointer,
			'eap-nav-menu--mobile-' . $mobile_mode,
		);
		if ( $indicator ) {
			$classes[] = 'eap-nav-menu--indicator';
		}
		if ( 'fullscreen' === $mobile_mode ) {
			$classes[] = 'eap-nav-menu--fs-' . $fs_position;
			$classes[] = 'eap-nav-menu--close-' . $close_place;
		}

		$this->add_render_attribute(
			'nav',
			array(
				'class'             => $classes,
				'data-eap-nav-menu' => '',
				'data-breakpoint'   => (string) $breakpoint,
				'data-mobile-mode'  => $mobile_mode,
				'aria-label'        => $aria_name,
			)
		);
		?>
		<nav <?php $this->print_render_attribute_string( 'nav' ); ?>>
			<?php if ( 0 !== $breakpoint ) : ?>
				<button type="button" class="eap-nav-menu__toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $list_id ); ?>" aria-label="<?php esc_attr_e( 'Menu', 'elementor-animatepro' ); ?>">
					<span class="eap-nav-menu__toggle-open" aria-hidden="true"><?php echo $this->get_toggle_icon( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span class="eap-nav-menu__toggle-close" aria-hidden="true"><?php echo $this->get_close_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				</button>
			<?php endif; ?>
			<ul class="eap-nav-menu__list" id="<?php echo esc_attr( $list_id ); ?>">
				<?php
				foreach ( $top as $item ) {
					$this->render_item( $item, $children, 0, $indicator, $chevron );
				}
				?>
			</ul>
			<?php if ( 'fullscreen' === $mobile_mode && 0 !== $breakpoint ) : ?>
				<button type="button" class="eap-nav-menu__close" aria-label="<?php esc_attr_e( 'Close menu', 'elementor-animatepro' ); ?>"><?php echo $this->get_close_button_icon( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			<?php endif; ?>
		</nav>
		<?php
	}

	protected function get_close_button_icon( $settings ) {
		if ( ! empty( $settings['close_icon']['value'] ) ) {
			ob_start();
			Icons_Manager::render_icon( $settings['close_icon'], array( 'aria-hidden' => 'true' ) );
			return ob_get_clean();
		}
		return $this->get_close_icon();
	}

	protected function render_item( $item, $children, $depth, $indicator, $chevron ) {
		$kids         = isset( $children[ (int) $item->ID ] ) ? $children[ (int) $item->ID ] : array();
		$has_children = ! empty( $kids );

		$item_classes = (array) ( isset( $item->classes ) ? $item->classes : array() );
		$is_current   = ! empty( $item->current )
			|| in_array( 'current-menu-item', $item_classes, true )
			|| in_array( 'current-menu-ancestor', $item_classes, true )
			|| in_array( 'current-menu-parent', $item_classes, true );

		$li_classes = array( 'eap-nav-menu__item' );
		if ( $has_children ) {
			$li_classes[] = 'eap-nav-menu__item--has-children';
		}
		if ( $is_current ) {
			$li_classes[] = 'is-current';
		}

		$link_key = 'link_' . $item->ID;
		$this->add_render_attribute( $link_key, 'class', 'eap-nav-menu__link' );
		if ( $is_current ) {
			$this->add_render_attribute( $link_key, 'class', 'is-current' );
			$this->add_render_attribute( $link_key, 'aria-current', 'page' );
		}
		if ( ! empty( $item->url ) ) {
			$this->add_render_attribute( $link_key, 'href', $item->url );
		}
		if ( ! empty( $item->target ) ) {
			$this->add_render_attribute( $link_key, 'target', $item->target );
		}
		if ( ! empty( $item->xfn ) ) {
			$this->add_render_attribute( $link_key, 'rel', $item->xfn );
		}
		if ( $has_children ) {
			$this->add_render_attribute( $link_key, 'aria-haspopup', 'true' );
		}
		?>
		<li class="<?php echo esc_attr( implode( ' ', $li_classes ) ); ?>">
			<div class="eap-nav-menu__row">
				<a <?php $this->print_render_attribute_string( $link_key ); ?>>
					<span class="eap-nav-menu__text"><?php echo esc_html( $item->title ); ?></span>
					<?php if ( $has_children && $indicator ) : ?>
						<span class="eap-nav-menu__indicator" aria-hidden="true"><?php echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>
				</a>
				<?php if ( $has_children ) : ?>
					<button type="button" class="eap-nav-menu__sub-toggle" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle submenu', 'elementor-animatepro' ); ?>">
						<span class="eap-nav-menu__sub-toggle-icon" aria-hidden="true"><?php echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</button>
				<?php endif; ?>
			</div>
			<?php if ( $has_children ) : ?>
				<ul class="eap-nav-menu__sub">
					<?php
					foreach ( $kids as $kid ) {
						$this->render_item( $kid, $children, $depth + 1, $indicator, $chevron );
					}
					?>
				</ul>
			<?php endif; ?>
		</li>
		<?php
	}

	protected function get_chevron( $settings ) {
		if ( ! empty( $settings['indicator_icon']['value'] ) ) {
			ob_start();
			Icons_Manager::render_icon( $settings['indicator_icon'], array( 'aria-hidden' => 'true' ) );
			return ob_get_clean();
		}
		return '<svg class="eap-nav-menu__chevron" width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1.5 5 5.5 9 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	}

	protected function get_toggle_icon( $settings ) {
		if ( ! empty( $settings['toggle_icon']['value'] ) ) {
			ob_start();
			Icons_Manager::render_icon( $settings['toggle_icon'], array( 'aria-hidden' => 'true' ) );
			return ob_get_clean();
		}
		return '<span class="eap-nav-menu__bars"><span></span><span></span><span></span></span>';
	}

	protected function get_close_icon() {
		return '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 5l10 10M15 5 5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
	}
}
