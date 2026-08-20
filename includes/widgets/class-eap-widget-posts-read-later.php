<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

/**
 * Posts Read Later — a no-login "save for later" feature (consistent with Post
 * Reactions / Post Rating: state lives in the visitor's browser via
 * localStorage `eap_read_later`, no account needed). One widget, two Modes:
 *
 *  - button : a bookmark toggle for the CURRENT post (Save <-> Saved).
 *  - list   : the visitor's saved posts as cards (reusing EAP_Posts_Query's
 *             card markup + posts.css). Because the saved IDs are client-side,
 *             the list is filled by read-later.js via the standalone
 *             `eap_read_later` AJAX endpoint (EAP_Read_Later), which returns the
 *             same server-rendered cards (publish-only) each with a remove ×.
 *
 * The shared store in read-later.js keeps the button state, the list and the
 * saved-count in sync across the page. CSS + JS.
 */
class EAP_Widget_Posts_Read_Later extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-posts-read-later';
	}

	public function get_title() {
		return __( 'Posts Read Later', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-bookmark';
	}

	public function get_keywords() {
		return array( 'posts', 'read later', 'save', 'bookmark', 'reading list', 'wishlist', 'dynamic' );
	}

	public function get_style_depends() {
		return array( 'eap-core', 'eap-posts', 'eap-posts-read-later', 'elementor-icons-fa-solid' );
	}

	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-read-later-script' );
	}

	protected function register_controls() {
		$this->register_content_section();
		$this->register_card_section();

		$this->register_button_style();
		$this->register_list_style();
		$this->register_card_style();
		$this->register_image_style();
		$this->register_title_style();
		$this->register_meta_style();
		$this->register_excerpt_style();
		$this->register_readmore_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_content_section() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Read Later', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'mode',
			array(
				'label'   => __( 'Mode', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'button',
				'options' => array(
					'button' => __( 'Save Button (current post)', 'elementor-animatepro' ),
					'list'   => __( 'Reading List (saved posts)', 'elementor-animatepro' ),
				),
			)
		);

		/* ---- Button mode ---- */

		$this->add_control(
			'button_icon',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'mode' => 'button' ),
				'description' => __( 'Leave empty for the built-in bookmark (fills in when saved).', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'save_text',
			array(
				'label'     => __( 'Save Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Save', 'elementor-animatepro' ),
				'condition' => array( 'mode' => 'button' ),
			)
		);

		$this->add_control(
			'saved_text',
			array(
				'label'     => __( 'Saved Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Saved', 'elementor-animatepro' ),
				'condition' => array( 'mode' => 'button' ),
			)
		);

		$this->add_control(
			'show_icon',
			array(
				'label'        => __( 'Show Icon', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'mode' => 'button' ),
			)
		);

		$this->add_responsive_control(
			'button_align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
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
					'{{WRAPPER}} .eap-read-later--button' => 'text-align: {{VALUE}};',
				),
				'condition' => array( 'mode' => 'button' ),
			)
		);

		/* ---- List mode ---- */

		$this->add_control(
			'heading_text',
			array(
				'label'     => __( 'Heading', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Reading List', 'elementor-animatepro' ),
				'condition' => array( 'mode' => 'list' ),
			)
		);

		$this->add_control(
			'show_count',
			array(
				'label'        => __( 'Show Count', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'mode' => 'list' ),
			)
		);

		$this->add_control(
			'show_clear',
			array(
				'label'        => __( '"Clear All" Button', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'mode' => 'list' ),
			)
		);

		$this->add_control(
			'clear_text',
			array(
				'label'     => __( '"Clear All" Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Clear all', 'elementor-animatepro' ),
				'condition' => array(
					'mode'       => 'list',
					'show_clear' => 'yes',
				),
			)
		);

		$this->add_control(
			'empty_text',
			array(
				'label'     => __( 'Empty Message', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 2,
				'default'   => __( 'No saved posts yet — tap Save on any post to add it here.', 'elementor-animatepro' ),
				'condition' => array( 'mode' => 'list' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — CARD (list mode)
	 * ================================================================== */

	protected function register_card_section() {
		$this->start_controls_section(
			'section_card',
			array(
				'label'     => __( 'Card', 'elementor-animatepro' ),
				'condition' => array( 'mode' => 'list' ),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'elementor-animatepro' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => array( 'px' => array( 'min' => 1, 'max' => 6, 'step' => 1 ) ),
				'default'        => array( 'size' => 3 ),
				'tablet_default' => array( 'size' => 2 ),
				'mobile_default' => array( 'size' => 1 ),
				'selectors'      => array(
					'{{WRAPPER}} .eap-read-later__grid' => '--eap-posts-cols: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'   => __( 'Image Size', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'medium_large',
				'options' => $this->get_image_size_options(),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'Title Tag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label'   => __( 'Excerpt Length (words)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 100,
				'default' => 18,
			)
		);

		$this->add_control(
			'meta_data',
			array(
				'label'       => __( 'Meta', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'default'     => array( 'date' ),
				'options'     => array(
					'author'   => __( 'Author', 'elementor-animatepro' ),
					'date'     => __( 'Date', 'elementor-animatepro' ),
					'comments' => __( 'Comments', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'readmore_text',
			array(
				'label'   => __( 'Read More Text', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Read More', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'elements_heading',
			array(
				'label'     => __( 'Elements', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		foreach ( array(
			'show_image'    => __( 'Image', 'elementor-animatepro' ),
			'show_badge'    => __( 'Category Badge', 'elementor-animatepro' ),
			'show_title'    => __( 'Title', 'elementor-animatepro' ),
			'show_meta'     => __( 'Meta', 'elementor-animatepro' ),
			'show_excerpt'  => __( 'Excerpt', 'elementor-animatepro' ),
			'show_readmore' => __( 'Read More', 'elementor-animatepro' ),
		) as $key => $label ) {
			$this->add_control(
				$key,
				array(
					'label'        => $label,
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'elementor-animatepro' ),
					'label_off'    => __( 'Hide', 'elementor-animatepro' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
		}

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE — BUTTON (button mode)
	 * ================================================================== */

	protected function register_button_style() {
		$this->start_controls_section(
			'section_button_style',
			array(
				'label'     => __( 'Button', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'mode' => 'button' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .eap-read-later__button',
			)
		);

		$this->add_responsive_control(
			'button_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 48 ) ),
				'default'    => array( 'size' => 18, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-read-later__button' => '--eap-rl-icon: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'show_icon' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'button_icon_gap',
			array(
				'label'      => __( 'Icon Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-read-later__button' => 'gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'show_icon' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 10, 'right' => 18, 'bottom' => 10, 'left' => 18, 'unit' => 'px', 'isLinked' => false ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-read-later__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-read-later__button' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border',
				'selector' => '{{WRAPPER}} .eap-read-later__button',
			)
		);

		$this->start_controls_tabs( 'button_state_tabs' );

		$this->start_controls_tab( 'button_state_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Text / Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#374151',
				'selectors' => array(
					'{{WRAPPER}} .eap-read-later__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f3f4f6',
				'selectors' => array(
					'{{WRAPPER}} .eap-read-later__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'button_state_saved', array( 'label' => __( 'Saved', 'elementor-animatepro' ) ) );

		$this->add_control(
			'button_color_saved',
			array(
				'label'     => __( 'Text / Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-read-later__button.is-saved' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg_saved',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eef2ff',
				'selectors' => array(
					'{{WRAPPER}} .eap-read-later__button.is-saved' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_border_saved',
			array(
				'label'     => __( 'Border Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#c7d2fe',
				'selectors' => array(
					'{{WRAPPER}} .eap-read-later__button.is-saved' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE — LIST (list mode)
	 * ================================================================== */

	protected function register_list_style() {
		$this->start_controls_section(
			'section_list_style',
			array(
				'label'     => __( 'List', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'mode' => 'list' ),
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label'      => __( 'Column Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-read-later__grid' => 'column-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'      => __( 'Row Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-read-later__grid' => 'row-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_style_heading',
			array(
				'label'     => __( 'Heading', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => __( 'Heading Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-read-later__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'count_color',
			array(
				'label'     => __( 'Count Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array(
					'{{WRAPPER}} .eap-read-later__count' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .eap-read-later__heading',
			)
		);

		$this->add_control(
			'clear_style_heading',
			array(
				'label'     => __( '"Clear All" Button', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'show_clear' => 'yes' ),
			)
		);

		$this->add_control(
			'clear_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#374151',
				'selectors' => array(
					'{{WRAPPER}} .eap-read-later__clear' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_clear' => 'yes' ),
			)
		);

		$this->add_control(
			'clear_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f3f4f6',
				'selectors' => array(
					'{{WRAPPER}} .eap-read-later__clear' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'show_clear' => 'yes' ),
			)
		);

		$this->add_control(
			'clear_bg_hover',
			array(
				'label'     => __( 'Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ef4444',
				'selectors' => array(
					'{{WRAPPER}} .eap-read-later__clear:hover' => 'background-color: {{VALUE}}; color: #fff; border-color: {{VALUE}};',
				),
				'condition' => array( 'show_clear' => 'yes' ),
			)
		);

		$this->add_control(
			'empty_style_heading',
			array(
				'label'     => __( 'Empty Message', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'empty_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array(
					'{{WRAPPER}} .eap-read-later__empty' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'remove_style_heading',
			array(
				'label'     => __( 'Remove Button', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'remove_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 18, 'max' => 48 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-read-later__remove' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'remove_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-read-later__remove' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'remove_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(17, 24, 39, 0.6)',
				'selectors' => array(
					'{{WRAPPER}} .eap-read-later__remove' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'remove_bg_hover',
			array(
				'label'     => __( 'Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ef4444',
				'selectors' => array(
					'{{WRAPPER}} .eap-read-later__remove:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE — CARD / IMAGE / TITLE / META / EXCERPT / READ MORE (list mode)
	 * Targets the reused .eap-posts__* card.
	 * ================================================================== */

	protected function register_card_style() {
		$this->start_controls_section(
			'section_card_style',
			array(
				'label'     => __( 'Card', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'mode' => 'list' ),
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Body Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20, 'unit' => 'px', 'isLinked' => true ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .eap-posts__item',
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__item' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eap-posts__item',
			)
		);

		$this->end_controls_section();
	}

	protected function register_image_style() {
		$this->start_controls_section(
			'section_image_style',
			array(
				'label'     => __( 'Image', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'mode'       => 'list',
					'show_image' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array( 'px' => array( 'min' => 80, 'max' => 600 ) ),
				'default'    => array( 'size' => 200, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-posts__image' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-posts__img'   => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;',
				),
			)
		);

		$this->add_control(
			'image_hover_zoom',
			array(
				'label'        => __( 'Hover Zoom', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function register_title_style() {
		$this->start_controls_section(
			'section_title_style',
			array(
				'label'     => __( 'Title', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'mode'       => 'list',
					'show_title' => 'yes',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__title, {{WRAPPER}} .eap-posts__title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__title a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-posts__title',
			)
		);

		$this->end_controls_section();
	}

	protected function register_meta_style() {
		$this->start_controls_section(
			'section_meta_style',
			array(
				'label'     => __( 'Meta', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'mode'      => 'list',
					'show_meta' => 'yes',
				),
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__meta' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'meta_typography',
				'selector' => '{{WRAPPER}} .eap-posts__meta',
			)
		);

		$this->end_controls_section();
	}

	protected function register_excerpt_style() {
		$this->start_controls_section(
			'section_excerpt_style',
			array(
				'label'     => __( 'Excerpt', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'mode'         => 'list',
					'show_excerpt' => 'yes',
				),
			)
		);

		$this->add_control(
			'excerpt_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__excerpt' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'excerpt_typography',
				'selector' => '{{WRAPPER}} .eap-posts__excerpt',
			)
		);

		$this->end_controls_section();
	}

	protected function register_readmore_style() {
		$this->start_controls_section(
			'section_readmore_style',
			array(
				'label'     => __( 'Read More', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'mode'          => 'list',
					'show_readmore' => 'yes',
				),
			)
		);

		$this->add_control(
			'readmore_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-posts__readmore' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'readmore_typography',
				'selector'  => '{{WRAPPER}} .eap-posts__readmore',
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$editor   = $this->eap_is_editor();
		$mode     = ( 'list' === ( $settings['mode'] ?? 'button' ) ) ? 'list' : 'button';

		if ( 'button' === $mode ) {
			$this->render_button( $settings, $editor );
		} else {
			$this->render_list( $settings, $editor );
		}
	}

	/**
	 * Button mode: a bookmark toggle for the current post.
	 *
	 * @param array $settings Settings.
	 * @param bool  $editor   Whether in the editor.
	 * @return void
	 */
	protected function render_button( $settings, $editor ) {
		$post_id = $this->eap_get_post_id();

		if ( ! $post_id ) {
			if ( $editor ) {
				echo '<div class="eap-widget eap-read-later eap-posts--empty">' . esc_html__( 'No post in context to save.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$save_text  = ( isset( $settings['save_text'] ) && '' !== $settings['save_text'] ) ? $settings['save_text'] : __( 'Save', 'elementor-animatepro' );
		$saved_text = ( isset( $settings['saved_text'] ) && '' !== $settings['saved_text'] ) ? $settings['saved_text'] : __( 'Saved', 'elementor-animatepro' );
		$show_icon  = 'yes' === ( $settings['show_icon'] ?? 'yes' );

		$this->add_render_attribute( 'wrapper', 'class', array( 'eap-widget', 'eap-read-later', 'eap-read-later--button' ) );
		$this->add_render_attribute(
			'button',
			array(
				'type'             => 'button',
				'class'            => 'eap-read-later__button',
				'data-post'        => (string) $post_id,
				'data-label-save'  => $save_text,
				'data-label-saved' => $saved_text,
				'aria-pressed'     => 'false',
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<button <?php $this->print_render_attribute_string( 'button' ); ?>>
				<?php if ( $show_icon ) : ?>
					<span class="eap-read-later__icon"><?php $this->render_bookmark_icon( $settings ); ?></span>
				<?php endif; ?>
				<span class="eap-read-later__label"><?php echo esc_html( $save_text ); ?></span>
			</button>
		</div>
		<?php
	}

	/**
	 * List mode: the visitor's saved posts (filled client-side; sample in editor).
	 *
	 * @param array $settings Settings.
	 * @param bool  $editor   Whether in the editor.
	 * @return void
	 */
	protected function render_list( $settings, $editor ) {
		$display = array(
			'layout'         => 'grid',
			'show_image'     => 'yes' === ( $settings['show_image'] ?? 'yes' ),
			'show_badge'     => 'yes' === ( $settings['show_badge'] ?? 'yes' ),
			'show_title'     => 'yes' === ( $settings['show_title'] ?? 'yes' ),
			'show_meta'      => 'yes' === ( $settings['show_meta'] ?? 'yes' ),
			'show_excerpt'   => 'yes' === ( $settings['show_excerpt'] ?? 'yes' ),
			'show_readmore'  => 'yes' === ( $settings['show_readmore'] ?? 'yes' ),
			'title_tag'      => ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h3',
			'excerpt_length' => isset( $settings['excerpt_length'] ) ? (int) $settings['excerpt_length'] : 18,
			'meta'           => isset( $settings['meta_data'] ) ? $settings['meta_data'] : array( 'date' ),
			'image_size'     => ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'medium_large',
			'readmore_text'  => isset( $settings['readmore_text'] ) ? $settings['readmore_text'] : __( 'Read More', 'elementor-animatepro' ),
		);

		$heading    = isset( $settings['heading_text'] ) ? $settings['heading_text'] : '';
		$show_count = 'yes' === ( $settings['show_count'] ?? 'yes' );
		$show_clear = 'yes' === ( $settings['show_clear'] ?? 'yes' );
		$clear_text = ( isset( $settings['clear_text'] ) && '' !== $settings['clear_text'] ) ? $settings['clear_text'] : __( 'Clear all', 'elementor-animatepro' );
		$empty_text = ( isset( $settings['empty_text'] ) && '' !== $settings['empty_text'] ) ? $settings['empty_text'] : __( 'No saved posts yet.', 'elementor-animatepro' );
		$zoom       = 'yes' === ( $settings['image_hover_zoom'] ?? 'yes' );

		// In the editor localStorage is empty, so render a few latest posts as a
		// live-looking sample and skip the empty state; the JS list init skips the
		// editor so the sample stays put.
		$sample_html  = '';
		$sample_count = 0;
		if ( $editor && class_exists( 'EAP_Read_Later' ) ) {
			$sample_ids   = get_posts(
				array(
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'posts_per_page' => 3,
					'fields'         => 'ids',
				)
			);
			$sample_count = count( $sample_ids );
			$sample_html  = EAP_Read_Later::render_items( $sample_ids, $display );
		}

		$classes = array( 'eap-widget', 'eap-posts', 'eap-read-later', 'eap-read-later--list' );
		if ( $zoom ) {
			$classes[] = 'eap-posts--zoom';
		}
		if ( '' === $sample_html ) {
			$classes[] = 'is-empty';
		}
		$this->add_render_attribute( 'wrapper', 'class', $classes );

		$this->add_render_attribute(
			'list',
			array(
				'class'         => 'eap-read-later__list',
				'data-ajax-url' => esc_url( admin_url( 'admin-ajax.php' ) ),
				'data-nonce'    => wp_create_nonce( 'eap-read-later' ),
				'data-display'  => wp_json_encode( $display ),
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( '' !== $heading || $show_clear ) : ?>
				<div class="eap-read-later__head">
					<?php if ( '' !== $heading ) : ?>
						<h3 class="eap-read-later__heading"><?php echo esc_html( $heading ); ?><?php if ( $show_count ) : ?> <span class="eap-read-later__count"><?php echo esc_html( (string) $sample_count ); ?></span><?php endif; ?></h3>
					<?php endif; ?>
					<?php if ( $show_clear ) : ?>
						<button type="button" class="eap-read-later__clear"><?php echo esc_html( $clear_text ); ?></button>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div <?php $this->print_render_attribute_string( 'list' ); ?>>
				<div class="eap-read-later__grid eap-posts__grid">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built by EAP_Read_Later/EAP_Posts_Query with escaped helpers.
					echo $sample_html;
					?>
				</div>
				<p class="eap-read-later__empty"><?php echo esc_html( $empty_text ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Output the bookmark icon: the user's custom icon, or the built-in
	 * outline bookmark SVG (which fills in via CSS when saved).
	 *
	 * @param array $settings Settings.
	 * @return void
	 */
	protected function render_bookmark_icon( $settings ) {
		$icon = isset( $settings['button_icon'] ) ? $settings['button_icon'] : array();

		if ( ! empty( $icon['value'] ) ) {
			\Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
			return;
		}

		echo '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M6 3.5h12a1 1 0 0 1 1 1V21l-7-4.1L5 21V4.5a1 1 0 0 1 1-1z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>';
	}

	/**
	 * Registered image sizes for the size select.
	 *
	 * @return array<string, string>
	 */
	protected function get_image_size_options() {
		$options = array();
		foreach ( get_intermediate_image_sizes() as $size ) {
			$options[ $size ] = ucwords( str_replace( array( '_', '-' ), ' ', $size ) );
		}
		$options['full'] = __( 'Full', 'elementor-animatepro' );
		return $options;
	}
}
