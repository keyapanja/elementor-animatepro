<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;

/**
 * Data Table — a fully styleable table with two data sources (row/column
 * repeaters or CSV/TSV paste) and optional sort / search / pagination. All
 * interactive features are per-instance toggles; with them off it's a lean,
 * static styled table. Responsive as horizontal scroll or stacked cards.
 */
class EAP_Widget_Data_Table extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-data-table';
	}

	public function get_title() {
		return __( 'Data Table', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-table';
	}

	public function get_keywords() {
		return array( 'table', 'data', 'grid', 'csv', 'sort', 'filter', 'pagination', 'compare' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'data-table' );
	}

	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-data-table-script' );
	}

	protected function register_controls() {
		$this->register_data_controls();
		$this->register_feature_controls();
		$this->register_responsive_controls();

		$this->register_table_style();
		$this->register_header_style();
		$this->register_body_style();
		$this->register_row_style();
		$this->register_sort_style();
		$this->register_search_style();
		$this->register_pagination_style();
	}

	/* =====================================================================
	 * CONTENT — data
	 * ================================================================== */

	protected function register_data_controls() {
		$this->start_controls_section(
			'section_data',
			array(
				'label' => __( 'Table Data', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'data_source',
			array(
				'label'   => __( 'Data Source', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'repeater',
				'options' => array(
					'repeater' => __( 'Build in panel (columns + rows)', 'elementor-animatepro' ),
					'csv'      => __( 'Paste CSV / TSV', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'show_header',
			array(
				'label'        => __( 'Show Header Row', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		/* ---- Repeater source: columns ---- */
		$columns = new Repeater();

		$columns->add_control(
			'col_title',
			array(
				'label'       => __( 'Header', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Column', 'elementor-animatepro' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$columns->add_control(
			'col_icon',
			array(
				'label' => __( 'Header Icon', 'elementor-animatepro' ),
				'type'  => Controls_Manager::ICONS,
				'skin'  => 'inline',
			)
		);

		$columns->add_control(
			'col_align',
			array(
				'label'   => __( 'Alignment', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => '',
				'options' => array(
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
			)
		);

		$columns->add_control(
			'col_width',
			array(
				'label'       => __( 'Width', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'e.g. 20% or 120px', 'elementor-animatepro' ),
				'default'     => '',
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'       => __( 'Columns', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $columns->get_controls(),
				'title_field' => '{{{ col_title }}}',
				'default'     => array(
					array( 'col_title' => __( 'Name', 'elementor-animatepro' ) ),
					array( 'col_title' => __( 'Role', 'elementor-animatepro' ) ),
					array( 'col_title' => __( 'Location', 'elementor-animatepro' ) ),
					array( 'col_title' => __( 'Status', 'elementor-animatepro' ) ),
				),
				'condition'   => array( 'data_source' => 'repeater' ),
			)
		);

		/* ---- Repeater source: rows ---- */
		$rows = new Repeater();

		$rows->add_control(
			'row_cells',
			array(
				'label'       => __( 'Cells', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => '',
				'description' => __( 'One row. Separate the cells with the delimiter chosen below (default | ). Basic HTML like links is allowed.', 'elementor-animatepro' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$rows->add_control(
			'row_highlight',
			array(
				'label'        => __( 'Highlight Row', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$rows->add_control(
			'row_link',
			array(
				'label'         => __( 'Row Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'placeholder'   => __( 'https://example.com', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'rows',
			array(
				'label'       => __( 'Rows', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rows->get_controls(),
				'title_field' => '{{{ row_cells }}}',
				'default'     => array(
					array( 'row_cells' => __( 'Ada Lovelace | Engineer | London | Active', 'elementor-animatepro' ) ),
					array( 'row_cells' => __( 'Alan Turing | Researcher | Manchester | Active', 'elementor-animatepro' ) ),
					array( 'row_cells' => __( 'Grace Hopper | Admiral | New York | Away', 'elementor-animatepro' ) ),
				),
				'condition'   => array( 'data_source' => 'repeater' ),
			)
		);

		$this->add_control(
			'cell_delimiter',
			array(
				'label'     => __( 'Cell Delimiter', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'pipe',
				'options'   => array(
					'pipe'      => __( 'Pipe  |', 'elementor-animatepro' ),
					'comma'     => __( 'Comma  ,', 'elementor-animatepro' ),
					'semicolon' => __( 'Semicolon  ;', 'elementor-animatepro' ),
					'tab'       => __( 'Tab', 'elementor-animatepro' ),
				),
				'condition' => array( 'data_source' => 'repeater' ),
			)
		);

		/* ---- CSV source ---- */
		$this->add_control(
			'csv_data',
			array(
				'label'       => __( 'Table Data', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 10,
				'default'     => "Name,Role,Location,Status\nAda Lovelace,Engineer,London,Active\nAlan Turing,Researcher,Manchester,Active\nGrace Hopper,Admiral,New York,Away",
				'description' => __( 'One row per line, cells separated by the delimiter below. The first line is the header when "Show Header Row" is on.', 'elementor-animatepro' ),
				'condition'   => array( 'data_source' => 'csv' ),
			)
		);

		$this->add_control(
			'csv_delimiter',
			array(
				'label'     => __( 'Delimiter', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'comma',
				'options'   => array(
					'comma'     => __( 'Comma  ,', 'elementor-animatepro' ),
					'tab'       => __( 'Tab', 'elementor-animatepro' ),
					'semicolon' => __( 'Semicolon  ;', 'elementor-animatepro' ),
					'pipe'      => __( 'Pipe  |', 'elementor-animatepro' ),
				),
				'condition' => array( 'data_source' => 'csv' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — features
	 * ================================================================== */

	protected function register_feature_controls() {
		$this->start_controls_section(
			'section_features',
			array(
				'label' => __( 'Features', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'enable_sort',
			array(
				'label'        => __( 'Sortable Columns', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Click a header to sort by that column (numbers and text both handled).', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'enable_search',
			array(
				'label'        => __( 'Search / Filter', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'search_placeholder',
			array(
				'label'     => __( 'Search Placeholder', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Search…', 'elementor-animatepro' ),
				'condition' => array( 'enable_search' => 'yes' ),
			)
		);

		$this->add_control(
			'no_results_text',
			array(
				'label'     => __( 'No Results Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'No matching records found.', 'elementor-animatepro' ),
				'condition' => array( 'enable_search' => 'yes' ),
			)
		);

		$this->add_control(
			'enable_pagination',
			array(
				'label'        => __( 'Pagination', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'rows_per_page',
			array(
				'label'     => __( 'Rows Per Page', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'default'   => 10,
				'condition' => array( 'enable_pagination' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * CONTENT — responsive
	 * ================================================================== */

	protected function register_responsive_controls() {
		$this->start_controls_section(
			'section_responsive',
			array(
				'label' => __( 'Responsive', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'responsive_mode',
			array(
				'label'       => __( 'Small Screen Behaviour', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'scroll',
				'options'     => array(
					'scroll' => __( 'Horizontal scroll', 'elementor-animatepro' ),
					'stack'  => __( 'Stack into cards', 'elementor-animatepro' ),
					'none'   => __( 'None', 'elementor-animatepro' ),
				),
				'description' => __( 'How the table adapts on narrow screens.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'stack_breakpoint',
			array(
				'label'     => __( 'Stack Below', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'mobile',
				'options'   => array(
					'tablet' => __( 'Tablet (1024px)', 'elementor-animatepro' ),
					'mobile' => __( 'Mobile (767px)', 'elementor-animatepro' ),
				),
				'condition' => array( 'responsive_mode' => 'stack' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_table_style() {
		$this->start_controls_section(
			'section_table_style',
			array(
				'label' => __( 'Table', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'table_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .eap-data-table__table',
				'fields_options' => array(
					'background' => array( 'default' => 'classic' ),
					'color'      => array( 'default' => '#ffffff' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'table_border',
				'selector'       => '{{WRAPPER}} .eap-data-table__scroll',
				'fields_options' => array(
					'border' => array( 'default' => 'solid' ),
					'width'  => array(
						'default' => array(
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'unit'     => 'px',
							'isLinked' => true,
						),
					),
					'color'  => array( 'default' => '#e5e7eb' ),
				),
			)
		);

		$this->add_responsive_control(
			'table_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-data-table__scroll' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'table_shadow',
				'selector' => '{{WRAPPER}} .eap-data-table__scroll',
			)
		);

		$this->end_controls_section();
	}

	protected function register_header_style() {
		$this->start_controls_section(
			'section_header_style',
			array(
				'label'     => __( 'Header', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_header' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'header_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .eap-data-table__thead',
				'fields_options' => array(
					'background' => array( 'default' => 'classic' ),
					'color'      => array( 'default' => '#4f46e5' ),
				),
			)
		);

		$this->add_control(
			'header_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__th' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'header_typography',
				'selector'       => '{{WRAPPER}} .eap-data-table__th',
				'fields_options' => array(
					'typography'  => array( 'default' => 'custom' ),
					'font_weight' => array( 'default' => '600' ),
				),
			)
		);

		$this->add_responsive_control(
			'header_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 14,
					'right'  => 18,
					'bottom' => 14,
					'left'   => 18,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-data-table__th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_body_style() {
		$this->start_controls_section(
			'section_body_style',
			array(
				'label' => __( 'Body Cells', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'cell_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4b5563',
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__td' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cell_link_color',
			array(
				'label'     => __( 'Link Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__td a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cell_typography',
				'selector' => '{{WRAPPER}} .eap-data-table__td',
			)
		);

		$this->add_responsive_control(
			'cell_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 12,
					'right'  => 18,
					'bottom' => 12,
					'left'   => 18,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-data-table__td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'cell_border_color',
			array(
				'label'     => __( 'Row Divider Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eceff3',
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__tbody .eap-data-table__tr:not(:last-child) .eap-data-table__td' => 'border-bottom: var(--eap-dt-divider-w, 1px) solid {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'cell_border_width',
			array(
				'label'      => __( 'Row Divider Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 8 ) ),
				'default'    => array( 'size' => 1, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-data-table' => '--eap-dt-divider-w: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'column_divider',
			array(
				'label'        => __( 'Vertical Dividers', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'column_divider_color',
			array(
				'label'     => __( 'Column Divider Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eceff3',
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__th:not(:last-child), {{WRAPPER}} .eap-data-table__td:not(:last-child)' => 'border-right: 1px solid {{VALUE}};',
				),
				'condition' => array( 'column_divider' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_row_style() {
		$this->start_controls_section(
			'section_row_style',
			array(
				'label' => __( 'Rows', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'stripe',
			array(
				'label'        => __( 'Striped Rows', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'stripe_bg',
			array(
				'label'     => __( 'Even Row Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f7f8fa',
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__tbody .eap-data-table__tr:nth-child(even) .eap-data-table__td' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'stripe' => 'yes' ),
			)
		);

		$this->add_control(
			'hover_bg',
			array(
				'label'     => __( 'Row Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eef2ff',
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__tbody .eap-data-table__tr:hover .eap-data-table__td' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'highlight_bg',
			array(
				'label'     => __( 'Highlighted Row Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff7ed',
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__tr--highlight .eap-data-table__td' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'highlight_color',
			array(
				'label'     => __( 'Highlighted Row Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__tr--highlight .eap-data-table__td' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_sort_style() {
		$this->start_controls_section(
			'section_sort_style',
			array(
				'label'     => __( 'Sort Icons', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'enable_sort' => 'yes' ),
			)
		);

		$this->add_control(
			'sort_icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.5)',
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__sort' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sort_icon_active',
			array(
				'label'     => __( 'Active Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__th[aria-sort="ascending"] .eap-data-table__sort, {{WRAPPER}} .eap-data-table__th[aria-sort="descending"] .eap-data-table__sort' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_search_style() {
		$this->start_controls_section(
			'section_search_style',
			array(
				'label'     => __( 'Search Box', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'enable_search' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'search_align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-end',
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
					'stretch'    => array(
						'title' => __( 'Full Width', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__toolbar' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'search_width',
			array(
				'label'      => __( 'Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 120, 'max' => 600 ),
					'%'  => array( 'min' => 10, 'max' => 100 ),
				),
				'default'    => array( 'size' => 260, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-data-table__search' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'search_spacing',
			array(
				'label'      => __( 'Spacing Below', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 16, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-data-table__toolbar' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'search_typography',
				'selector' => '{{WRAPPER}} .eap-data-table__search',
			)
		);

		$this->add_control(
			'search_text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1f2937',
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__search' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__search' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'search_border',
				'selector'       => '{{WRAPPER}} .eap-data-table__search',
				'fields_options' => array(
					'border' => array( 'default' => 'solid' ),
					'width'  => array(
						'default' => array(
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'unit'     => 'px',
							'isLinked' => true,
						),
					),
					'color'  => array( 'default' => '#d1d5db' ),
				),
			)
		);

		$this->add_responsive_control(
			'search_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-data-table__search' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'search_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 10,
					'right'  => 14,
					'bottom' => 10,
					'left'   => 14,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-data-table__search' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_pagination_style() {
		$this->start_controls_section(
			'section_pagination_style',
			array(
				'label'     => __( 'Pagination', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'enable_pagination' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'pagination_align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
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
					'{{WRAPPER}} .eap-data-table__pagination' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_spacing',
			array(
				'label'      => __( 'Spacing Above', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 18, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-data-table__pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pagination_typography',
				'selector' => '{{WRAPPER}} .eap-data-table__page',
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4b5563',
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__page' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__page' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_active_color',
			array(
				'label'     => __( 'Active Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__page.is-active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_active_bg',
			array(
				'label'     => __( 'Active Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'selectors' => array(
					'{{WRAPPER}} .eap-data-table__page.is-active' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'pagination_border',
				'selector'       => '{{WRAPPER}} .eap-data-table__page',
				'fields_options' => array(
					'border' => array( 'default' => 'solid' ),
					'width'  => array(
						'default' => array(
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'unit'     => 'px',
							'isLinked' => true,
						),
					),
					'color'  => array( 'default' => '#d1d5db' ),
				),
			)
		);

		$this->add_responsive_control(
			'pagination_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-data-table__page' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 24 ) ),
				'default'    => array( 'size' => 6, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-data-table__pagination' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * DATA
	 * ================================================================== */

	protected function delimiter_char( $key ) {
		switch ( $key ) {
			case 'comma':
				return ',';
			case 'semicolon':
				return ';';
			case 'tab':
				return "\t";
			case 'pipe':
			default:
				return '|';
		}
	}

	/**
	 * Normalise both data sources into { headers, rows, col_count, show_header }.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function get_table_data( $settings ) {
		$show_header = 'yes' === ( $settings['show_header'] ?? 'yes' );
		$source      = $settings['data_source'] ?? 'repeater';
		$headers     = array();
		$rows        = array();
		$col_count   = 0;

		if ( 'csv' === $source ) {
			$raw   = (string) ( $settings['csv_data'] ?? '' );
			$delim = $this->delimiter_char( $settings['csv_delimiter'] ?? 'comma' );
			$lines = preg_split( '/\r\n|\r|\n/', trim( $raw ) );
			$lines = array_values( array_filter( (array) $lines, static function ( $line ) {
				return '' !== trim( $line );
			} ) );

			$matrix = array();
			foreach ( $lines as $line ) {
				$matrix[] = array_map( 'trim', explode( $delim, $line ) );
			}

			if ( $show_header && ! empty( $matrix ) ) {
				$head = array_shift( $matrix );
				foreach ( $head as $title ) {
					$headers[] = array( 'title' => $title );
				}
			}

			$col_count = count( $headers );
			foreach ( $matrix as $cells ) {
				$col_count = max( $col_count, count( $cells ) );
				$rows[]    = array( 'cells' => $cells );
			}
		} else {
			$cols = ( ! empty( $settings['columns'] ) && is_array( $settings['columns'] ) ) ? $settings['columns'] : array();
			foreach ( $cols as $col ) {
				$headers[] = array(
					'title' => $col['col_title'] ?? '',
					'align' => $col['col_align'] ?? '',
					'width' => $col['col_width'] ?? '',
					'icon'  => $col['col_icon'] ?? array(),
				);
			}

			$col_count = count( $headers );
			$delim     = $this->delimiter_char( $settings['cell_delimiter'] ?? 'pipe' );
			$data_rows = ( ! empty( $settings['rows'] ) && is_array( $settings['rows'] ) ) ? $settings['rows'] : array();

			foreach ( $data_rows as $row ) {
				$cells     = array_map( 'trim', explode( $delim, (string) ( $row['row_cells'] ?? '' ) ) );
				$col_count = max( $col_count, count( $cells ) );
				$rows[]    = array(
					'cells'     => $cells,
					'highlight' => 'yes' === ( $row['row_highlight'] ?? '' ),
					'link'      => $row['row_link'] ?? array(),
				);
			}
		}

		return array(
			'headers'     => $headers,
			'rows'        => $rows,
			'col_count'   => max( 1, $col_count ),
			'show_header' => $show_header && ! empty( $headers ),
		);
	}

	protected function sanitize_align( $align ) {
		return in_array( $align, array( 'left', 'center', 'right', 'justify' ), true ) ? $align : '';
	}

	protected function sanitize_width( $width ) {
		$width = trim( (string) $width );
		if ( '' === $width ) {
			return '';
		}
		if ( 'auto' === $width || preg_match( '/^[0-9.]+(px|%|em|rem|ch|vw)$/', $width ) ) {
			return $width;
		}
		return '';
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$data     = $this->get_table_data( $settings );

		$headers     = $data['headers'];
		$rows        = $data['rows'];
		$col_count   = $data['col_count'];
		$show_header = $data['show_header'];

		if ( empty( $rows ) && empty( $headers ) ) {
			return;
		}

		$sort     = 'yes' === ( $settings['enable_sort'] ?? '' );
		$search   = 'yes' === ( $settings['enable_search'] ?? '' );
		$paginate = 'yes' === ( $settings['enable_pagination'] ?? '' );
		$per_page = max( 1, absint( $settings['rows_per_page'] ?? 10 ) );

		$responsive = $settings['responsive_mode'] ?? 'scroll';
		$stack_bp   = $settings['stack_breakpoint'] ?? 'mobile';

		// Per-column alignment for body cells (repeater source only).
		$col_align = array();
		foreach ( $headers as $i => $h ) {
			$col_align[ $i ] = $this->sanitize_align( $h['align'] ?? '' );
		}

		$classes = array( 'eap-widget', 'eap-data-table' );
		if ( 'scroll' === $responsive ) {
			$classes[] = 'eap-data-table--scroll';
		} elseif ( 'stack' === $responsive ) {
			$classes[] = 'eap-data-table--stack';
			$classes[] = 'eap-data-table--stack-' . ( 'tablet' === $stack_bp ? 'tablet' : 'mobile' );
		}

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'                => $classes,
				'data-eap-data-table'  => 'true',
			)
		);
		if ( $sort ) {
			$this->add_render_attribute( 'wrapper', 'data-eap-dt-sort', '1' );
		}
		if ( $paginate ) {
			$this->add_render_attribute( 'wrapper', 'data-eap-dt-page-size', (string) $per_page );
		}
		if ( $search ) {
			$this->add_render_attribute( 'wrapper', 'data-eap-dt-empty', esc_attr( $settings['no_results_text'] ?? '' ) );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $search ) : ?>
				<div class="eap-data-table__toolbar">
					<input type="search" class="eap-data-table__search" data-eap-dt-search
						placeholder="<?php echo esc_attr( $settings['search_placeholder'] ?? '' ); ?>"
						aria-label="<?php echo esc_attr( $settings['search_placeholder'] ?? __( 'Search', 'elementor-animatepro' ) ); ?>">
				</div>
			<?php endif; ?>

			<div class="eap-data-table__scroll">
				<table class="eap-data-table__table">
					<?php if ( $show_header ) : ?>
						<thead class="eap-data-table__thead">
							<tr>
								<?php
								for ( $i = 0; $i < $col_count; $i++ ) {
									$this->render_header_cell( isset( $headers[ $i ] ) ? $headers[ $i ] : array(), $i, $sort );
								}
								?>
							</tr>
						</thead>
					<?php endif; ?>
					<tbody class="eap-data-table__tbody">
						<?php
						foreach ( $rows as $row ) {
							$this->render_row( $row, $headers, $col_align, $col_count );
						}
						?>
					</tbody>
				</table>
			</div>

			<?php if ( $search ) : ?>
				<div class="eap-data-table__empty" data-eap-dt-empty-msg hidden><?php echo esc_html( $settings['no_results_text'] ?? '' ); ?></div>
			<?php endif; ?>

			<?php if ( $paginate ) : ?>
				<div class="eap-data-table__pagination" data-eap-dt-pagination></div>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function render_header_cell( $header, $index, $sortable ) {
		$title = $header['title'] ?? '';
		$align = $this->sanitize_align( $header['align'] ?? '' );
		$width = $this->sanitize_width( $header['width'] ?? '' );

		$style = '';
		if ( '' !== $align ) {
			$style .= 'text-align:' . $align . ';';
		}
		if ( '' !== $width ) {
			$style .= 'width:' . $width . ';';
		}

		$attrs = 'class="eap-data-table__th" data-col="' . esc_attr( $index ) . '"';
		if ( '' !== $style ) {
			$attrs .= ' style="' . esc_attr( $style ) . '"';
		}
		if ( $sortable ) {
			$attrs .= ' data-eap-dt-th tabindex="0" role="columnheader" aria-sort="none"';
		}

		echo '<th ' . $attrs . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<span class="eap-data-table__th-inner">';

		if ( ! empty( $header['icon']['value'] ) ) {
			echo '<span class="eap-data-table__th-icon">';
			Icons_Manager::render_icon( $header['icon'], array( 'aria-hidden' => 'true' ) );
			echo '</span>';
		}

		echo '<span class="eap-data-table__th-text">' . wp_kses_post( $title ) . '</span>';

		if ( $sortable ) {
			echo '<span class="eap-data-table__sort" aria-hidden="true"></span>';
		}

		echo '</span></th>';
	}

	protected function render_row( $row, $headers, $col_align, $col_count ) {
		$cells = isset( $row['cells'] ) && is_array( $row['cells'] ) ? $row['cells'] : array();

		$tr_classes = array( 'eap-data-table__tr' );
		if ( ! empty( $row['highlight'] ) ) {
			$tr_classes[] = 'eap-data-table__tr--highlight';
		}

		$row_attrs = 'class="' . esc_attr( implode( ' ', $tr_classes ) ) . '"';

		if ( ! empty( $row['link']['url'] ) ) {
			$row_attrs .= ' data-eap-dt-href="' . esc_url( $row['link']['url'] ) . '" tabindex="0" role="link"';
			if ( ! empty( $row['link']['is_external'] ) ) {
				$row_attrs .= ' data-eap-dt-target="_blank"';
			}
		}

		echo '<tr ' . $row_attrs . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		for ( $i = 0; $i < $col_count; $i++ ) {
			$value = isset( $cells[ $i ] ) ? $cells[ $i ] : '';
			$align = isset( $col_align[ $i ] ) ? $col_align[ $i ] : '';
			$label = isset( $headers[ $i ]['title'] ) ? $headers[ $i ]['title'] : '';

			$style = '' !== $align ? ' style="text-align:' . esc_attr( $align ) . ';"' : '';

			echo '<td class="eap-data-table__td" data-label="' . esc_attr( wp_strip_all_tags( (string) $label ) ) . '"' . $style . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wp_kses_post( $value );
			echo '</td>';
		}

		echo '</tr>';
	}
}
