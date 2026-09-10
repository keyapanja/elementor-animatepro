<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;

/**
 * Scroll Elements widget.
 *
 * A sticky section nav beside its own scrolling content: you write the sections
 * here and the widget renders both halves and wires them together.
 *
 * How this differs from One Page Nav, which also highlights the active section:
 * that widget is a NAV ONLY. You build the sections yourself somewhere on the
 * page, give each a CSS selector, and type those selectors into the nav — it
 * points at content it does not own. This widget OWNS the content, so the
 * anchors are generated, the nav can never point at a section that was renamed
 * or deleted, and there are no selectors to keep in sync. Use One Page Nav to
 * navigate sections built elsewhere on the page; use this for a self-contained
 * document like terms, a privacy policy or a spec.
 */
class EAP_Widget_Scroll_Elements extends EAP_Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-scroll-elements';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Scroll Elements', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'scroll', 'sections', 'spy', 'sticky', 'nav', 'anchor', 'terms', 'policy' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-scroll-elements', 'elementor-icons-fa-solid' );
	}

	/**
	 * Scripts.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-scroll-elements-script' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_sections_section();
		$this->register_layout_section();

		$this->register_nav_style();
		$this->register_content_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	/**
	 * Sections repeater.
	 *
	 * @return void
	 */
	protected function register_sections_section() {
		$this->start_controls_section(
			'section_items',
			array( 'label' => __( 'Sections', 'elementor-animatepro' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_title',
			array(
				'label'   => __( 'Title', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Section', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'item_nav_label',
			array(
				'label'       => __( 'Nav Label', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Optional shorter label for the nav. Falls back to the title.', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'item_icon',
			array(
				'label' => __( 'Nav Icon', 'elementor-animatepro' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$repeater->add_control(
			'item_content',
			array(
				'label'   => __( 'Content', 'elementor-animatepro' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => __( 'Write this section here.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ item_title }}}',
				'default'     => array(
					array( 'item_title' => __( 'Data controller', 'elementor-animatepro' ) ),
					array( 'item_title' => __( 'Digital services', 'elementor-animatepro' ) ),
					array( 'item_title' => __( 'Data update', 'elementor-animatepro' ) ),
					array( 'item_title' => __( 'Rights of the user', 'elementor-animatepro' ) ),
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
			'nav_position',
			array(
				'label'   => __( 'Nav Position', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => __( 'Left', 'elementor-animatepro' ),
					'right' => __( 'Right', 'elementor-animatepro' ),
					'top'   => __( 'Above Content', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_responsive_control(
			'nav_width',
			array(
				'label'      => __( 'Nav Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 140, 'max' => 480 ),
					'%'  => array( 'min' => 15, 'max' => 50 ),
				),
				'default'    => array( 'size' => 280, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-se-nav-w: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'nav_position!' => 'top' ),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'size' => 48, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-se-gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'sticky_nav',
			array(
				'label'        => __( 'Sticky Nav', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'sticky_offset',
			array(
				'label'       => __( 'Sticky Offset', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 300 ) ),
				'default'     => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'   => array( '{{WRAPPER}}' => '--eap-se-sticky: {{SIZE}}{{UNIT}};' ),
				'description' => __( 'Distance from the top when stuck. Increase it to clear a fixed header.', 'elementor-animatepro' ),
				'condition'   => array( 'sticky_nav' => 'yes' ),
			)
		);

		$this->add_control(
			'scroll_offset',
			array(
				'label'       => __( 'Scroll Offset', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 90,
				'min'         => 0,
				'max'         => 400,
				'description' => __( 'How far above a section to stop when it is clicked, so a fixed header does not cover the heading.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_titles',
			array(
				'label'        => __( 'Show Section Titles', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'     => __( 'Title Tag', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h2',
				'options'   => array(
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'div' => 'div',
				),
				'condition' => array( 'show_titles' => 'yes' ),
			)
		);

		$this->add_control(
			'stack_below',
			array(
				'label'      => __( 'Stack Below', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 1200 ) ),
				'default'    => array( 'size' => 782, 'unit' => 'px' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	/**
	 * Nav style.
	 *
	 * @return void
	 */
	protected function register_nav_style() {
		$this->start_controls_section(
			'style_nav',
			array(
				'label' => __( 'Nav', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'nav_typography',
				'selector' => '{{WRAPPER}} .eap-se__link',
			)
		);

		$this->add_responsive_control(
			'nav_padding',
			array(
				'label'      => __( 'Item Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 16,
					'right'    => 22,
					'bottom'   => 16,
					'left'     => 22,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-se__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_radius',
			array(
				'label'      => __( 'Item Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-se-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'nav_spacing',
			array(
				'label'      => __( 'Space Between Items', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-se-item-gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->start_controls_tabs( 'nav_tabs' );

		$this->start_controls_tab( 'nav_tab_normal', array( 'label' => __( 'Normal', 'elementor-animatepro' ) ) );

		$this->add_control(
			'nav_color',
			array(
				'label'     => __( 'Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}}' => '--eap-se-fg: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'nav_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}}' => '--eap-se-bg: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'nav_border',
				'selector' => '{{WRAPPER}} .eap-se__link',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'nav_tab_active', array( 'label' => __( 'Active', 'elementor-animatepro' ) ) );

		$this->add_control(
			'nav_color_active',
			array(
				'label'     => __( 'Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}}' => '--eap-se-fg-active: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'nav_bg_active',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f4552b',
				'selectors' => array( '{{WRAPPER}}' => '--eap-se-bg-active: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'nav_border_active',
			array(
				'label'     => __( 'Border', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-se__item.is-active .eap-se__link' => 'border-color: {{VALUE}};' ),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Content style.
	 *
	 * @return void
	 */
	protected function register_content_style() {
		$this->start_controls_section(
			'style_content',
			array(
				'label' => __( 'Content', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_title_typography',
				'label'    => __( 'Title Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-se__title',
			)
		);

		$this->add_control(
			'content_title_color',
			array(
				'label'     => __( 'Title Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-se__title' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_typography',
				'label'    => __( 'Body Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-se__body',
			)
		);

		$this->add_control(
			'content_color',
			array(
				'label'     => __( 'Body Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .eap-se__body' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'section_spacing',
			array(
				'label'      => __( 'Space Between Sections', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 140 ) ),
				'default'    => array( 'size' => 40, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}}' => '--eap-se-section-gap: {{SIZE}}{{UNIT}};' ),
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
		$items    = is_array( $settings['items'] ?? null ) ? $settings['items'] : array();

		if ( empty( $items ) ) {
			if ( $this->eap_is_editor() ) {
				echo '<div class="eap-widget eap-se eap-se--empty">' . esc_html__( 'Add at least one section.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$position = in_array( $settings['nav_position'] ?? 'left', array( 'left', 'right', 'top' ), true )
			? $settings['nav_position']
			: 'left';

		$sticky = 'yes' === ( $settings['sticky_nav'] ?? 'yes' ) && 'top' !== $position;
		$titles = 'yes' === ( $settings['show_titles'] ?? 'yes' );
		$offset = isset( $settings['scroll_offset'] ) ? (int) $settings['scroll_offset'] : 90;
		$stack  = isset( $settings['stack_below']['size'] ) ? (int) $settings['stack_below']['size'] : 782;

		$tag = $settings['title_tag'] ?? 'h2';
		if ( ! in_array( $tag, array( 'h2', 'h3', 'h4', 'h5', 'div' ), true ) ) {
			$tag = 'h2';
		}

		$classes = array( 'eap-widget', 'eap-se', 'eap-se--' . $position );
		if ( $sticky ) {
			$classes[] = 'eap-se--sticky';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		$this->add_render_attribute( 'wrapper', 'data-eap-scroll-elements', wp_json_encode( array( 'offset' => $offset ) ) );

		$base = 'eap-se-' . $this->get_id();
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<nav class="eap-se__nav" aria-label="<?php echo esc_attr__( 'Section navigation', 'elementor-animatepro' ); ?>">
				<ul class="eap-se__list">
					<?php foreach ( $items as $index => $item ) : ?>
						<?php
						$id    = $base . '-' . $index;
						$label = ! empty( $item['item_nav_label'] ) ? $item['item_nav_label'] : ( $item['item_title'] ?? '' );
						?>
						<li class="eap-se__item<?php echo 0 === $index ? ' is-active' : ''; ?>">
							<?php
							/*
							 * A real anchor, not a button: the nav still works with
							 * JavaScript off or before it runs, and the section is
							 * reachable and linkable by URL fragment.
							 */
							?>
							<a class="eap-se__link" href="#<?php echo esc_attr( $id ); ?>"
								data-eap-se-link="<?php echo esc_attr( $id ); ?>"
								aria-current="<?php echo 0 === $index ? 'true' : 'false'; ?>">
								<?php if ( ! empty( $item['item_icon']['value'] ) ) : ?>
									<span class="eap-se__icon">
										<?php Icons_Manager::render_icon( $item['item_icon'], array( 'aria-hidden' => 'true' ) ); ?>
									</span>
								<?php endif; ?>
								<span class="eap-se__label"><?php echo esc_html( $label ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<div class="eap-se__content">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php $id = $base . '-' . $index; ?>
					<section class="eap-se__section" id="<?php echo esc_attr( $id ); ?>" data-eap-se-section="<?php echo esc_attr( $id ); ?>">
						<?php if ( $titles && ! empty( $item['item_title'] ) ) : ?>
							<<?php echo esc_html( $tag ); ?> class="eap-se__title"><?php echo esc_html( $item['item_title'] ); ?></<?php echo esc_html( $tag ); ?>>
						<?php endif; ?>
						<div class="eap-se__body">
							<?php echo wp_kses_post( $item['item_content'] ?? '' ); ?>
						</div>
					</section>
				<?php endforeach; ?>
			</div>
		</div>

		<?php
		/*
		 * Per-instance breakpoint, so it cannot live in the stylesheet — a media
		 * query cannot read a CSS variable.
		 */
		?>
		<style>
			@media (max-width: <?php echo (int) $stack; ?>px) {
				<?php echo '.elementor-element-' . esc_html( $this->get_id() ); ?> .eap-se {
					grid-template-columns: minmax(0, 1fr);
				}
				<?php echo '.elementor-element-' . esc_html( $this->get_id() ); ?> .eap-se__nav {
					position: static;
					order: 0;
				}
			}
		</style>
		<?php
	}
}
