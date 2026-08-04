<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;
use Elementor\Modules\NestedElements\Controls\Control_Nested_Repeater;

/**
 * Content Toggle — a nested widget that switches between droppable containers,
 * one per toggle condition (e.g. Monthly / Yearly). Each condition is a full
 * Elementor container the user can drop any content into.
 *
 * This widget is only required/registered when Elementor's Nested Elements
 * base class is available (see EAP_Elementor::register_widgets()).
 */
class EAP_Widget_Content_Toggle extends Widget_Nested_Base {

	/**
	 * Per-item render settings collected during render().
	 *
	 * @var array
	 */
	private $toggle_item_settings = array();

	public function get_name() {
		return 'eap-content-toggle';
	}

	public function get_title() {
		return __( 'Content Toggle', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_keywords() {
		return array( 'toggle', 'switch', 'content', 'tabs', 'pricing', 'condition', 'nested' );
	}

	public function get_categories() {
		return array( 'eap-elements' );
	}

	public function get_style_depends(): array {
		return array( 'eap-core', 'eap-content-toggle' );
	}

	public function get_script_depends(): array {
		return array( 'eap-core-runtime', 'eap-content-toggle-script' );
	}

	public function show_in_panel(): bool {
		return Plugin::$instance->experiments->is_feature_active( 'nested-elements', true );
	}

	/**
	 * Match Elementor's own nested widgets (Tabs/Accordion): the child container
	 * views attach correctly only when the inner-wrapper flag mirrors the
	 * optimized-markup experiment. The default (always true) can prevent the
	 * nested content area from rendering.
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/* -------------------------------------------------------------------------
	 * Nested element configuration
	 * ---------------------------------------------------------------------- */

	protected function toggle_content_container( int $index ) {
		return array(
			'elType'   => 'container',
			'settings' => array(
				/* translators: %d: Condition index. */
				'_title'        => sprintf( __( 'Condition #%d', 'elementor-animatepro' ), $index ),
				'content_width' => 'full',
			),
		);
	}

	protected function get_default_children_elements() {
		return array(
			$this->toggle_content_container( 1 ),
			$this->toggle_content_container( 2 ),
		);
	}

	protected function get_default_repeater_title_setting_key() {
		return 'condition_title';
	}

	protected function get_default_children_title() {
		/* translators: %d: Condition index. */
		return esc_html__( 'Condition #%d', 'elementor-animatepro' );
	}

	protected function get_default_children_placeholder_selector() {
		return '.eap-content-toggle__content';
	}

	protected function get_initial_config(): array {
		return array_merge(
			parent::get_initial_config(),
			array(
				'support_improved_repeaters' => true,
				'target_container'           => array( '.eap-content-toggle__switcher' ),
				'node'                       => 'button',
			)
		);
	}

	/* -------------------------------------------------------------------------
	 * Controls
	 * ---------------------------------------------------------------------- */

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_switcher_style_controls();
		$this->register_content_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_conditions',
			array(
				'label' => __( 'Content Toggle', 'elementor-animatepro' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'condition_title',
			array(
				'label'       => __( 'Label', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Toggle', 'elementor-animatepro' ),
				'placeholder' => __( 'Label', 'elementor-animatepro' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'condition_id',
			array(
				'label'       => __( 'Custom ID', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'e.g. yearly', 'elementor-animatepro' ),
				'description' => __( 'Open this option from a link by adding #your-id to the page URL.', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'conditions',
			array(
				'label'       => __( 'Conditions', 'elementor-animatepro' ),
				'type'        => Control_Nested_Repeater::CONTROL_TYPE,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'condition_title' => __( 'Monthly', 'elementor-animatepro' ) ),
					array( 'condition_title' => __( 'Yearly', 'elementor-animatepro' ) ),
				),
				'title_field' => '{{{ condition_title }}}',
				'button_text' => __( 'Add Condition', 'elementor-animatepro' ),
				// A content toggle is a two-state switch (e.g. Monthly / Yearly):
				// lock it to exactly two conditions by hiding the add / duplicate /
				// remove item actions. The two default panes are created on insert
				// via get_default_children_elements(), independent of these actions.
				'item_actions' => array(
					'add'       => false,
					'duplicate' => false,
					'remove'    => false,
				),
			)
		);

		$this->add_control(
			'deep_link_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Deep link: give a condition a <strong>Custom ID</strong> above, then add <code>#your-id</code> to the end of a page URL (e.g. <code>example.com/pricing/#yearly</code>) — that option opens and the page scrolls to it automatically.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-control-field-description',
			)
		);

		$this->add_control(
			'default_active',
			array(
				'label'       => __( 'Default Active', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'default'     => 1,
				'description' => __( 'Which condition is open first (1 = first).', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'switcher_align',
			array(
				'label'     => __( 'Switcher Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-content-toggle' => '--eap-ct-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'switcher_gap',
			array(
				'label'      => __( 'Gap Below Switcher', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-content-toggle' => '--eap-ct-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_switcher_style_controls() {
		$this->start_controls_section(
			'section_switcher_style',
			array(
				'label' => __( 'Switcher', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .eap-content-toggle__label',
			)
		);

		$this->add_control(
			'track_bg',
			array(
				'label'     => __( 'Track Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eef1f6',
				'selectors' => array(
					'{{WRAPPER}} .eap-content-toggle' => '--eap-ct-track-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'thumb_bg',
			array(
				'label'     => __( 'Active Pill Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#18c964',
				'selectors' => array(
					'{{WRAPPER}} .eap-content-toggle' => '--eap-ct-thumb-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => __( 'Label Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#5b6675',
				'selectors' => array(
					'{{WRAPPER}} .eap-content-toggle' => '--eap-ct-label-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'label_active_color',
			array(
				'label'     => __( 'Active Label Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-content-toggle' => '--eap-ct-active-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'label_padding',
			array(
				'label'      => __( 'Label Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 10,
					'right'  => 26,
					'bottom' => 10,
					'left'   => 26,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-content-toggle__label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'track_padding',
			array(
				'label'      => __( 'Track Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 5, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-content-toggle' => '--eap-ct-pad: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'track_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'size' => 100, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-content-toggle' => '--eap-ct-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'track_border',
				'selector' => '{{WRAPPER}} .eap-content-toggle__switcher',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'track_shadow',
				'selector' => '{{WRAPPER}} .eap-content-toggle__switcher',
			)
		);

		$this->end_controls_section();
	}

	protected function register_content_style_controls() {
		$this->start_controls_section(
			'section_content_style',
			array(
				'label' => __( 'Content Area', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'content_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .eap-content-toggle__content',
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-content-toggle__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-content-toggle__content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------------------
	 * Frontend render
	 * ---------------------------------------------------------------------- */

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$conditions = ! empty( $settings['conditions'] ) && is_array( $settings['conditions'] ) ? $settings['conditions'] : array();
		$count      = count( $conditions );

		if ( 0 === $count ) {
			return;
		}

		$widget_number = $this->get_id_int();
		$default_index = (int) ( $settings['default_active'] ?? 1 ) - 1;
		$default_index = max( 0, min( $count - 1, $default_index ) );

		$this->add_render_attribute(
			'toggle',
			array(
				'class'              => 'eap-content-toggle',
				'data-widget-number' => $widget_number,
				'data-default-index' => (string) $default_index,
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'toggle' ); ?>>
			<div class="eap-content-toggle__switcher" role="tablist" style="--eap-ct-count: <?php echo esc_attr( $count ); ?>; --eap-ct-index: <?php echo esc_attr( $default_index ); ?>;">
				<span class="eap-content-toggle__thumb" aria-hidden="true"></span>
				<?php
				foreach ( $conditions as $index => $item ) {
					$tab_count     = $index + 1;
					$item_settings = array(
						'index'        => $index,
						'tab_count'    => $tab_count,
						'title_id'     => 'eap-ct-title-' . $widget_number . $tab_count,
						'container_id' => 'eap-ct-content-' . $widget_number . $tab_count,
						'is_active'    => $index === $default_index,
						'item'         => $item,
					);

					$this->toggle_item_settings[] = $item_settings;
					$this->render_switcher_label( $item_settings );
				}
				?>
			</div>
			<div class="eap-content-toggle__content">
				<?php $this->render_content_containers( $settings ); ?>
			</div>
		</div>
		<?php
	}

	protected function render_switcher_label( $item_settings ): void {
		$key = 'label_' . $item_settings['tab_count'];

		$this->add_render_attribute(
			$key,
			array(
				'id'            => $item_settings['title_id'],
				'class'         => 'eap-content-toggle__label',
				'role'          => 'tab',
				'data-tab-index' => $item_settings['tab_count'],
				'aria-controls' => $item_settings['container_id'],
				'aria-selected' => $item_settings['is_active'] ? 'true' : 'false',
				'tabindex'      => $item_settings['is_active'] ? '0' : '-1',
			)
		);

		// Optional per-condition deep-link target. Adding #<id> to the URL opens
		// this condition and scrolls to it (handled in content-toggle.js).
		$cid = isset( $item_settings['item']['condition_id'] ) ? preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $item_settings['item']['condition_id'] ) : '';
		if ( '' !== $cid ) {
			$this->add_render_attribute( $key, 'data-eap-ct-cid', $cid );
		}
		?>
		<button <?php $this->print_render_attribute_string( $key ); ?>>
			<span class="eap-content-toggle__label-text"><?php echo wp_kses_post( $item_settings['item']['condition_title'] ); ?></span>
		</button>
		<?php
	}

	protected function render_content_containers( $settings ): void {
		foreach ( $settings['conditions'] as $index => $item ) {
			if ( ! isset( $this->toggle_item_settings[ $index ] ) ) {
				continue;
			}

			$this->print_child( $index, $this->toggle_item_settings[ $index ] );
		}
	}

	/**
	 * Print a child container, tagging it with toggle attributes.
	 *
	 * @param int   $index         Child index.
	 * @param array $item_settings Item settings for this index.
	 */
	public function print_child( $index, $item_settings = array() ) {
		$children  = $this->get_children();
		$child_ids = array();

		foreach ( $children as $child ) {
			$child_ids[] = $child->get_id();
		}

		$add_attributes_to_container = function ( $should_render, $container ) use ( $item_settings, $child_ids ) {
			if ( in_array( $container->get_id(), $child_ids, true ) ) {
				$this->add_attributes_to_container( $container, $item_settings );
			}

			return $should_render;
		};

		add_filter( 'elementor/frontend/container/should_render', $add_attributes_to_container, 10, 3 );

		if ( isset( $children[ $index ] ) ) {
			$children[ $index ]->print_element();
		}

		remove_filter( 'elementor/frontend/container/should_render', $add_attributes_to_container );
	}

	protected function add_attributes_to_container( $container, $item_settings ): void {
		$container->add_render_attribute(
			'_wrapper',
			array(
				'id'              => $item_settings['container_id'],
				'role'            => 'tabpanel',
				'aria-labelledby' => $item_settings['title_id'],
				'data-tab-index'  => $item_settings['tab_count'],
				// `eap-ct-pane` is the frontend-only hook the CSS uses to hide
				// inactive panes; it is absent in the editor (content_template),
				// so every container stays visible/droppable while building.
				'class'           => ! empty( $item_settings['is_active'] ) ? 'eap-ct-pane e-active' : 'eap-ct-pane',
			)
		);
	}

	/* -------------------------------------------------------------------------
	 * Editor (client-side) templates
	 * ---------------------------------------------------------------------- */

	protected function content_template() {
		?>
		<#
		const elementUid = view.getIDInt().toString();
		const conditions = settings.conditions || [];
		const count = conditions.length;
		let defaultActive = ( parseInt( settings.default_active, 10 ) || 1 ) - 1;
		if ( defaultActive < 0 ) { defaultActive = 0; }
		if ( defaultActive > count - 1 ) { defaultActive = Math.max( 0, count - 1 ); }
		#>
		<div class="eap-content-toggle" data-widget-number="{{ elementUid }}" data-default-index="{{ defaultActive }}">
			<# if ( count ) { #>
			<div class="eap-content-toggle__switcher" role="tablist" style="--eap-ct-count: {{ count }}; --eap-ct-index: {{ defaultActive }};">
				<span class="eap-content-toggle__thumb" aria-hidden="true"></span>
				<# _.each( conditions, function( item, index ) {
					const tabIndex = index;
				#>
				<?php $this->content_template_single_item(); ?>
				<# } ); #>
			</div>
			<div class="eap-content-toggle__content"></div>
			<# } #>
		</div>
		<?php
	}

	protected function content_template_single_repeater_item() {
		?>
		<#
		const elementUid = view.getIDInt().toString();
		const tabIndex = view.collection.length;
		const item = data;
		#>
		<?php $this->content_template_single_item(); ?>
		<?php
	}

	private function content_template_single_item() {
		?>
		<#
		const tabCount = tabIndex + 1;
		const ctTitleId = 'eap-ct-title-' + elementUid + tabCount;
		const ctContainerId = 'eap-ct-content-' + elementUid + tabCount;
		const ctCid = ( item && item.condition_id ? String( item.condition_id ) : '' ).replace( /[^A-Za-z0-9_-]/g, '' );

		view.addRenderAttribute( 'ct-label-' + tabCount, {
			'id': ctTitleId,
			'class': [ 'eap-content-toggle__label' ],
			'role': 'tab',
			'data-tab-index': tabCount,
			'data-eap-ct-cid': ctCid,
			'aria-controls': ctContainerId,
			'aria-selected': 1 === tabCount ? 'true' : 'false',
			'tabindex': 1 === tabCount ? '0' : '-1',
		}, null, true );

		view.addRenderAttribute( 'ct-label-text-' + tabCount, {
			'class': [ 'eap-content-toggle__label-text' ],
		}, null, true );
		#>
		<button {{{ view.getRenderAttributeString( 'ct-label-' + tabCount ) }}}>
			<span {{{ view.getRenderAttributeString( 'ct-label-text-' + tabCount ) }}}>{{{ item.condition_title }}}</span>
		</button>
		<?php
	}
}
