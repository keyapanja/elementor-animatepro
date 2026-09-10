<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * Advanced Pricing Table widget.
 *
 * A multi-plan pricing COMPARISON, where every plan is scored against one shared
 * list of features.
 *
 * How this differs from Price Box, which is also pricing: that widget is a
 * single card, and the documented way to build a table with it is to drop
 * several into columns. That works visually right up until the feature lists
 * differ in length — then the rows stop lining up, and a pricing table whose
 * rows do not line up cannot be read across. This widget owns ONE feature list
 * and scores each plan against it, so the rows align by construction. Use Price
 * Box for a lone plan or for cards that need individually different content.
 *
 * Elementor repeaters cannot nest, so per-plan values are a DELIMITED STRING on
 * each plan, positionally matched to the features repeater — the same approach
 * Data Table uses for its rows. A value of yes/no renders a tick or a cross;
 * anything else renders as text, so "10 GB" or "Unlimited" work as well.
 */
class EAP_Widget_Advanced_Pricing_Table extends EAP_Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-advanced-pricing-table';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Advanced Pricing Table', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-price-table';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'pricing', 'price', 'table', 'plans', 'compare', 'comparison', 'tiers' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-advanced-pricing-table' );
	}

	/**
	 * Scripts.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-advanced-pricing-table-script' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_plans_section();
		$this->register_features_section();
		$this->register_layout_section();

		$this->register_plan_style();
		$this->register_price_style();
		$this->register_features_style();
		$this->register_button_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	/**
	 * Plans section.
	 *
	 * @return void
	 */
	protected function register_plans_section() {
		$this->start_controls_section(
			'section_plans',
			array( 'label' => __( 'Plans', 'elementor-animatepro' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'plan_name',
			array(
				'label'   => __( 'Name', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Starter', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'plan_description',
			array(
				'label' => __( 'Description', 'elementor-animatepro' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'plan_price',
			array(
				'label'   => __( 'Price', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '$19',
			)
		);

		$repeater->add_control(
			'plan_period',
			array(
				'label'   => __( 'Period', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '/month', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'plan_price_alt',
			array(
				'label'       => __( 'Alternate Price', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '$190',
				'description' => __( 'Shown when the billing toggle is switched. Leave empty to keep the same price.', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'plan_period_alt',
			array(
				'label'   => __( 'Alternate Period', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '/year', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'plan_values',
			array(
				'label'       => __( 'Feature Values', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 6,
				'default'     => "yes\nyes\nno\n10 GB",
				'description' => __( 'One value per feature, in the same order as the Features list. Use <code>yes</code> or <code>no</code> for a tick or a cross; anything else shows as text.', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'plan_button_text',
			array(
				'label'   => __( 'Button Text', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Choose plan', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'plan_button_link',
			array(
				'label'   => __( 'Button Link', 'elementor-animatepro' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$repeater->add_control(
			'plan_highlight',
			array(
				'label'        => __( 'Highlight This Plan', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$repeater->add_control(
			'plan_badge',
			array(
				'label'       => __( 'Badge', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Most popular', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'plans',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ plan_name }}}',
				'default'     => array(
					array(
						'plan_name'        => __( 'Starter', 'elementor-animatepro' ),
						'plan_price'       => '$19',
						'plan_price_alt'   => '$190',
						'plan_values'      => "yes\nyes\nno\n10 GB",
						'plan_button_text' => __( 'Choose plan', 'elementor-animatepro' ),
					),
					array(
						'plan_name'        => __( 'Growth', 'elementor-animatepro' ),
						'plan_price'       => '$49',
						'plan_price_alt'   => '$490',
						'plan_values'      => "yes\nyes\nyes\n100 GB",
						'plan_highlight'   => 'yes',
						'plan_badge'       => __( 'Most popular', 'elementor-animatepro' ),
						'plan_button_text' => __( 'Choose plan', 'elementor-animatepro' ),
					),
					array(
						'plan_name'        => __( 'Scale', 'elementor-animatepro' ),
						'plan_price'       => '$99',
						'plan_price_alt'   => '$990',
						'plan_values'      => "yes\nyes\nyes\nUnlimited",
						'plan_button_text' => __( 'Contact us', 'elementor-animatepro' ),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Features section.
	 *
	 * @return void
	 */
	protected function register_features_section() {
		$this->start_controls_section(
			'section_features',
			array( 'label' => __( 'Features', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'features_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'These are the rows every plan is scored against. Each plan\'s Feature Values list matches this order, line by line — which is what keeps the rows aligned.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'feature_label',
			array(
				'label'   => __( 'Feature', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Feature', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'feature_hint',
			array(
				'label'       => __( 'Hint', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Shown as a native tooltip on the feature name.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'features',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ feature_label }}}',
				'default'     => array(
					array( 'feature_label' => __( 'Unlimited projects', 'elementor-animatepro' ) ),
					array( 'feature_label' => __( 'Email support', 'elementor-animatepro' ) ),
					array( 'feature_label' => __( 'Priority support', 'elementor-animatepro' ) ),
					array( 'feature_label' => __( 'Storage', 'elementor-animatepro' ) ),
				),
			)
		);

		$this->end_controls_section();
	}

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
			'layout',
			array(
				'label'       => __( 'Layout', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'cards',
				'options'     => array(
					'cards' => __( 'Plan Cards', 'elementor-animatepro' ),
					'table' => __( 'Comparison Table', 'elementor-animatepro' ),
				),
				'description' => __( 'Cards keep the feature rows aligned across plans. The table adds a leading column of feature names.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'feature_column_label',
			array(
				'label'     => __( 'Feature Column Heading', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Features', 'elementor-animatepro' ),
				'condition' => array( 'layout' => 'table' ),
			)
		);

		$this->add_control(
			'show_labels_in_cards',
			array(
				'label'        => __( 'Show Feature Names On Each Card', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'layout' => 'cards' ),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 20, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-apt-gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'stack_below',
			array(
				'label'       => __( 'Stack Below', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 1200 ) ),
				'default'     => array( 'size' => 782, 'unit' => 'px' ),
				'description' => __( 'Below this width the plans stack into one column.', 'elementor-animatepro' ),
			)
		);

		/* ---------- Billing toggle ---------- */

		$this->add_control(
			'billing_heading',
			array(
				'label'     => __( 'Billing Toggle', 'elementor-animatepro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_billing',
			array(
				'label'        => __( 'Show Billing Toggle', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Switches every plan between its Price and Alternate Price. For a switch that also controls things outside this widget, use the Toggle Switch widget instead.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'billing_label',
			array(
				'label'     => __( 'Primary Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Monthly', 'elementor-animatepro' ),
				'condition' => array( 'show_billing' => 'yes' ),
			)
		);

		$this->add_control(
			'billing_label_alt',
			array(
				'label'     => __( 'Alternate Label', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Yearly', 'elementor-animatepro' ),
				'condition' => array( 'show_billing' => 'yes' ),
			)
		);

		$this->add_control(
			'billing_note',
			array(
				'label'       => __( 'Note', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Save 20%', 'elementor-animatepro' ),
				'condition'   => array( 'show_billing' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	/**
	 * Plan card style.
	 *
	 * @return void
	 */
	protected function register_plan_style() {
		$this->start_controls_section(
			'style_plan',
			array(
				'label' => __( 'Plan', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'plan_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}}' => '--eap-apt-bg: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'plan_highlight_bg',
			array(
				'label'     => __( 'Highlighted Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array( '{{WRAPPER}}' => '--eap-apt-hl-bg: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'plan_highlight_fg',
			array(
				'label'     => __( 'Highlighted Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}}' => '--eap-apt-hl-fg: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'plan_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-apt-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'plan_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 26,
					'right'    => 24,
					'bottom'   => 26,
					'left'     => 24,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-apt-pad: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'plan_border',
				'selector' => '{{WRAPPER}} .eap-apt__plan',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'plan_shadow',
				'selector' => '{{WRAPPER}} .eap-apt__plan',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'plan_name_typography',
				'label'    => __( 'Name Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-apt__name',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Price style.
	 *
	 * @return void
	 */
	protected function register_price_style() {
		$this->start_controls_section(
			'style_price',
			array(
				'label' => __( 'Price', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_typography',
				'selector' => '{{WRAPPER}} .eap-apt__amount',
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-apt__amount' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'period_color',
			array(
				'label'     => __( 'Period Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-apt__period' => 'color: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Feature row style.
	 *
	 * @return void
	 */
	protected function register_features_style() {
		$this->start_controls_section(
			'style_features',
			array(
				'label' => __( 'Features', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'feature_typography',
				'selector' => '{{WRAPPER}} .eap-apt__cell, {{WRAPPER}} .eap-apt__feature-name',
			)
		);

		$this->add_control(
			'feature_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}}' => '--eap-apt-feature: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'yes_color',
			array(
				'label'     => __( 'Tick Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#059669',
				'selectors' => array( '{{WRAPPER}}' => '--eap-apt-yes: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'no_color',
			array(
				'label'     => __( 'Cross Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d1d5db',
				'selectors' => array( '{{WRAPPER}}' => '--eap-apt-no: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'row_divider',
			array(
				'label'     => __( 'Row Divider', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(17, 24, 39, 0.09)',
				'selectors' => array( '{{WRAPPER}}' => '--eap-apt-divider: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'row_height',
			array(
				'label'      => __( 'Row Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 28, 'max' => 90 ) ),
				'default'    => array( 'size' => 46, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-apt-row: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Button style.
	 *
	 * @return void
	 */
	protected function register_button_style() {
		$this->start_controls_section(
			'style_button',
			array(
				'label' => __( 'Button', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .eap-apt__button',
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}}' => '--eap-apt-btn-fg: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array( '{{WRAPPER}}' => '--eap-apt-btn-bg: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-apt-btn-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();
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

		$plans    = is_array( $settings['plans'] ?? null ) ? $settings['plans'] : array();
		$features = is_array( $settings['features'] ?? null ) ? $settings['features'] : array();

		if ( empty( $plans ) ) {
			if ( $this->eap_is_editor() ) {
				echo '<div class="eap-widget eap-apt eap-apt--empty">' . esc_html__( 'Add at least one plan.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$layout   = ( 'table' === ( $settings['layout'] ?? 'cards' ) ) ? 'table' : 'cards';
		$billing  = 'yes' === ( $settings['show_billing'] ?? 'yes' );
		$labels   = $layout === 'cards' && 'yes' === ( $settings['show_labels_in_cards'] ?? 'yes' );
		$stack    = isset( $settings['stack_below']['size'] ) ? (int) $settings['stack_below']['size'] : 782;

		$columns = count( $plans ) + ( 'table' === $layout ? 1 : 0 );

		$classes = array( 'eap-widget', 'eap-apt', 'eap-apt--' . $layout );
		if ( $labels ) {
			$classes[] = 'eap-apt--labelled';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		/*
		 * Both counts drive the grid template. The ROW count matters as much as
		 * the column count: the parent grid must declare one row per feature for
		 * the columns' `grid-template-rows: subgrid` to have rows to inherit.
		 */
		$this->add_render_attribute(
			'wrapper',
			'style',
			'--eap-apt-cols:' . (int) $columns . ';--eap-apt-rows:' . max( 1, count( $features ) ) . ';'
		);
		if ( $billing ) {
			$this->add_render_attribute( 'wrapper', 'data-eap-apt', '' );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $billing ) : ?>
				<div class="eap-apt__billing">
					<div class="eap-apt__billing-pills" role="group">
						<button type="button" class="eap-apt__billing-pill is-active" data-billing="primary" aria-pressed="true">
							<?php echo esc_html( $settings['billing_label'] ?? '' ); ?>
						</button>
						<button type="button" class="eap-apt__billing-pill" data-billing="alt" aria-pressed="false">
							<?php echo esc_html( $settings['billing_label_alt'] ?? '' ); ?>
						</button>
					</div>
					<?php if ( ! empty( $settings['billing_note'] ) ) : ?>
						<span class="eap-apt__billing-note"><?php echo esc_html( $settings['billing_note'] ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="eap-apt__scroll">
				<div class="eap-apt__grid">
					<?php
					if ( 'table' === $layout ) {
						$this->render_feature_column( $settings, $features );
					}

					foreach ( $plans as $plan ) {
						$this->render_plan( $plan, $features, $settings, $layout, $labels );
					}
					?>
				</div>
			</div>
		</div>

		<?php
		/*
		 * The stacking breakpoint is per-instance, so it cannot live in the
		 * stylesheet — a media query cannot read a CSS variable. This is the one
		 * place the widget prints CSS itself.
		 */
		?>
		<style>
			@media (max-width: <?php echo (int) $stack; ?>px) {
				<?php echo '.elementor-element-' . esc_html( $this->get_id() ); ?> .eap-apt__grid {
					grid-template-columns: minmax(0, 1fr);
				}
				<?php echo '.elementor-element-' . esc_html( $this->get_id() ); ?> .eap-apt__feature-column {
					display: none;
				}
			}
		</style>
		<?php
	}

	/**
	 * Render the leading feature-name column (table layout).
	 *
	 * @param array $settings Settings.
	 * @param array $features Features.
	 * @return void
	 */
	protected function render_feature_column( $settings, $features ) {
		?>
		<div class="eap-apt__feature-column">
			<div class="eap-apt__head">
				<span class="eap-apt__name"><?php echo esc_html( $settings['feature_column_label'] ?? '' ); ?></span>
			</div>
				<?php foreach ( $features as $feature ) : ?>
				<div class="eap-apt__row">
						<span class="eap-apt__feature-name"
							<?php echo ! empty( $feature['feature_hint'] ) ? ' title="' . esc_attr( $feature['feature_hint'] ) . '"' : ''; ?>>
							<?php echo esc_html( $feature['feature_label'] ?? '' ); ?>
						</span>
				</div>
				<?php endforeach; ?>
			<div class="eap-apt__foot"></div>
		</div>
		<?php
	}

	/**
	 * Render one plan column.
	 *
	 * @param array  $plan     Plan.
	 * @param array  $features Features.
	 * @param array  $settings Settings.
	 * @param string $layout   Layout.
	 * @param bool   $labels   Whether to repeat feature names in each card.
	 * @return void
	 */
	protected function render_plan( $plan, $features, $settings, $layout, $labels ) {
		$values    = $this->split_values( $plan['plan_values'] ?? '' );
		$highlight = 'yes' === ( $plan['plan_highlight'] ?? '' );

		$price     = (string) ( $plan['plan_price'] ?? '' );
		$price_alt = (string) ( $plan['plan_price_alt'] ?? '' );
		$period    = (string) ( $plan['plan_period'] ?? '' );
		$period_alt = (string) ( $plan['plan_period_alt'] ?? '' );

		// An empty alternate means "same price on both" rather than a blank card.
		$price_alt  = '' !== $price_alt ? $price_alt : $price;
		$period_alt = '' !== $period_alt ? $period_alt : $period;

		$link   = $plan['plan_button_link'] ?? array();
		$url    = ! empty( $link['url'] ) ? $link['url'] : '';
		$target = ! empty( $link['is_external'] ) ? ' target="_blank"' : '';
		$rel    = ! empty( $link['nofollow'] ) ? ' rel="nofollow"' : '';
		?>
		<div class="eap-apt__plan<?php echo $highlight ? ' is-highlighted' : ''; ?>">
			<?php if ( ! empty( $plan['plan_badge'] ) ) : ?>
				<span class="eap-apt__badge"><?php echo esc_html( $plan['plan_badge'] ); ?></span>
			<?php endif; ?>

			<div class="eap-apt__head">
				<span class="eap-apt__name"><?php echo esc_html( $plan['plan_name'] ?? '' ); ?></span>

				<?php if ( ! empty( $plan['plan_description'] ) ) : ?>
					<span class="eap-apt__description"><?php echo esc_html( $plan['plan_description'] ); ?></span>
				<?php endif; ?>

				<span class="eap-apt__price">
					<span class="eap-apt__amount"
						data-primary="<?php echo esc_attr( $price ); ?>"
						data-alt="<?php echo esc_attr( $price_alt ); ?>"><?php echo esc_html( $price ); ?></span>
					<span class="eap-apt__period"
						data-primary="<?php echo esc_attr( $period ); ?>"
						data-alt="<?php echo esc_attr( $period_alt ); ?>"><?php echo esc_html( $period ); ?></span>
				</span>
			</div>

				<?php
				foreach ( $features as $index => $feature ) :
					$raw  = isset( $values[ $index ] ) ? $values[ $index ] : '';
					$kind = $this->value_kind( $raw );
					?>
					<div class="eap-apt__row eap-apt__row--<?php echo esc_attr( $kind ); ?>">
						<?php if ( $labels && 'cards' === $layout ) : ?>
							<span class="eap-apt__feature-name"
								<?php echo ! empty( $feature['feature_hint'] ) ? ' title="' . esc_attr( $feature['feature_hint'] ) . '"' : ''; ?>>
								<?php echo esc_html( $feature['feature_label'] ?? '' ); ?>
							</span>
						<?php endif; ?>

						<span class="eap-apt__cell">
							<?php
							if ( 'yes' === $kind ) {
								echo '<svg class="eap-apt__icon eap-apt__icon--yes" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>';
								echo '<span class="screen-reader-text">' . esc_html__( 'Included', 'elementor-animatepro' ) . '</span>';
							} elseif ( 'no' === $kind ) {
								echo '<svg class="eap-apt__icon eap-apt__icon--no" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6 6 18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>';
								echo '<span class="screen-reader-text">' . esc_html__( 'Not included', 'elementor-animatepro' ) . '</span>';
							} else {
								echo esc_html( $raw );
							}
							?>
						</span>
				</div>
				<?php endforeach; ?>

			<div class="eap-apt__foot">
				<?php if ( '' !== $url && ! empty( $plan['plan_button_text'] ) ) : ?>
					<a class="eap-apt__button" href="<?php echo esc_url( $url ); ?>"<?php echo $target . $rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static markup. ?>>
						<?php echo esc_html( $plan['plan_button_text'] ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Split a plan's value list.
	 *
	 * Newline separated, but a comma-separated single line is accepted too —
	 * pasting from a spreadsheet is the obvious way to fill this in.
	 *
	 * @param string $raw Raw textarea value.
	 * @return string[]
	 */
	protected function split_values( $raw ) {
		$raw = (string) $raw;

		$parts = ( false !== strpos( $raw, "\n" ) )
			? preg_split( '/\r\n|\r|\n/', $raw )
			: explode( ',', $raw );

		return array_map( 'trim', (array) $parts );
	}

	/**
	 * Classify one value: a tick, a cross, or literal text.
	 *
	 * @param string $value Value.
	 * @return string 'yes' | 'no' | 'text'
	 */
	protected function value_kind( $value ) {
		$normal = strtolower( trim( (string) $value ) );

		if ( in_array( $normal, array( 'yes', 'true', '1', 'y', '✓', '✔' ), true ) ) {
			return 'yes';
		}

		if ( in_array( $normal, array( 'no', 'false', '0', 'n', '-', '–', '—', '✕', '✗', '' ), true ) ) {
			return 'no';
		}

		return 'text';
	}
}
