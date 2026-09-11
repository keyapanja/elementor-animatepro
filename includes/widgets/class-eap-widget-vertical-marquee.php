<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;

/**
 * Vertical Marquee widget.
 *
 * Columns of cards, images or text that scroll continuously — up, down, or
 * alternating column by column — the "wall of testimonials" effect.
 *
 * - SPEED IS IN PIXELS PER SECOND. The script measures each column and sets
 *   its duration from that, so a column with twice the content moves at the
 *   same pace instead of twice as fast.
 * - THE LOOP IS SEAMLESS. Each column is its list plus an identical copy,
 *   animated by exactly one list height; a column shorter than the viewport is
 *   topped up first, so the seam never shows as a gap.
 * - COLUMNS ARE BUILT IN THE BROWSER. How many columns there are is a
 *   responsive setting the server cannot resolve per device, so the page
 *   carries one list and the script deals it into as many columns as the
 *   current breakpoint asks for, re-dealing when that changes.
 * - EACH ITEM IS READ ONCE. Screen readers get one copy of every item; the
 *   loop copies are hidden from them and taken out of the tab order.
 * - IT CAN BE STOPPED. Hover and keyboard focus pause it, there is a pause
 *   button (moving content needs one — WCAG 2.2.2), and reduced motion turns
 *   it into columns you scroll yourself. With no script, it is a plain list.
 */
class EAP_Widget_Vertical_Marquee extends EAP_Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-vertical-marquee';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Vertical Marquee', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-scroll';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'marquee', 'vertical', 'ticker', 'scroll', 'testimonials', 'wall', 'logos', 'loop' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-vertical-marquee' );
	}

	/**
	 * Scripts.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-vertical-marquee-script' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_items_section();
		$this->register_layout_section();
		$this->register_motion_section();

		$this->register_item_style();
		$this->register_image_style();
		$this->register_text_style();
		$this->register_pause_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	/**
	 * Items section.
	 *
	 * @return void
	 */
	protected function register_items_section() {
		$this->start_controls_section(
			'section_items',
			array( 'label' => __( 'Items', 'elementor-animatepro' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_type',
			array(
				'label'   => __( 'Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'card',
				'options' => array(
					'card'  => __( 'Card', 'elementor-animatepro' ),
					'image' => __( 'Image', 'elementor-animatepro' ),
					'text'  => __( 'Text', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label'     => __( 'Image', 'elementor-animatepro' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'item_type' => array( 'card', 'image' ) ),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Item title', 'elementor-animatepro' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'For an image, this is its alt text.', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'text',
			array(
				'label'     => __( 'Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 3,
				'dynamic'   => array( 'active' => true ),
				'condition' => array( 'item_type' => 'card' ),
			)
		);

		$repeater->add_control(
			'meta',
			array(
				'label'       => __( 'Byline', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Name, role', 'elementor-animatepro' ),
				'label_block' => true,
				'condition'   => array( 'item_type' => 'card' ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'   => __( 'Link', 'elementor-animatepro' ),
				'type'    => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
			)
		);

		$samples = array(
			array( __( 'Set up in an afternoon', 'elementor-animatepro' ), __( 'We had the whole page live before the coffee went cold.', 'elementor-animatepro' ), __( 'Priya, founder', 'elementor-animatepro' ) ),
			array( __( 'Sharp on every screen', 'elementor-animatepro' ), __( 'Looks as good on a phone as it does on the big monitor.', 'elementor-animatepro' ), __( 'Tom, designer', 'elementor-animatepro' ) ),
			array( __( 'Support that answers', 'elementor-animatepro' ), __( 'A real reply the same day, every time we asked.', 'elementor-animatepro' ), __( 'Lena, marketing lead', 'elementor-animatepro' ) ),
			array( __( 'No more copy-paste', 'elementor-animatepro' ), __( 'One change updates every page that uses it.', 'elementor-animatepro' ), __( 'Omar, developer', 'elementor-animatepro' ) ),
			array( __( 'Fast, really fast', 'elementor-animatepro' ), __( 'Pages load before you finish clicking.', 'elementor-animatepro' ), __( 'Jun, product owner', 'elementor-animatepro' ) ),
			array( __( 'Easy to hand over', 'elementor-animatepro' ), __( 'The client edits it themselves now.', 'elementor-animatepro' ), __( 'Sara, agency owner', 'elementor-animatepro' ) ),
		);

		$defaults = array();
		foreach ( $samples as $sample ) {
			$defaults[] = array(
				'item_type' => 'card',
				'title'     => $sample[0],
				'text'      => $sample[1],
				'meta'      => $sample[2],
			);
		}

		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => $defaults,
			)
		);

		$this->add_control(
			'region_label',
			array(
				'label'       => __( 'Accessible Name', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Scrolling highlights', 'elementor-animatepro' ),
				'description' => __( 'What a screen reader announces for this area.', 'elementor-animatepro' ),
				'separator'   => 'before',
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

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'elementor-animatepro' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'min'            => 1,
				'max'            => 4,
				'selectors'      => array(
					'{{WRAPPER}} .eap-vm' => '--eap-vm-cols: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'distribute',
			array(
				'label'       => __( 'Items In Columns', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'split',
				'options'     => array(
					'split'  => __( 'Share them out', 'elementor-animatepro' ),
					'repeat' => __( 'Every column shows all', 'elementor-animatepro' ),
				),
				'description' => __( 'Sharing needs at least one item per column; with few items, show all in every column.', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 150, 'max' => 1200 ),
					'vh' => array( 'min' => 10, 'max' => 100 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 520,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-vm' => '--eap-vm-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => __( 'Space Between Items', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-vm' => '--eap-vm-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label'      => __( 'Space Between Columns', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-vm' => '--eap-vm-col-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'fade_edges',
			array(
				'label'        => __( 'Fade Top & Bottom', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_responsive_control(
			'fade_size',
			array(
				'label'      => __( 'Fade Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 300 ),
					'%'  => array( 'min' => 0, 'max' => 40 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-vm' => '--eap-vm-fade: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'fade_edges' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Motion section.
	 *
	 * @return void
	 */
	protected function register_motion_section() {
		$this->start_controls_section(
			'section_motion',
			array( 'label' => __( 'Motion', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'direction',
			array(
				'label'   => __( 'Direction', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'up',
				'toggle'  => false,
				'options' => array(
					'up'   => array(
						'title' => __( 'Up', 'elementor-animatepro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'down' => array(
						'title' => __( 'Down', 'elementor-animatepro' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
			)
		);

		$this->add_control(
			'alternate',
			array(
				'label'        => __( 'Alternate Columns', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Every other column runs the opposite way.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'      => __( 'Speed', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 5, 'max' => 300 ) ),
				'default'    => array(
					'unit' => 'px',
					'size' => 40,
				),
				'description' => __( 'Pixels per second. The same pace however much is in a column.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'stagger',
			array(
				'label'        => __( 'Offset Columns', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Start each column at a different point so rows never line up.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => __( 'Pause On Hover', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
				'description'  => __( 'Keyboard focus inside always pauses it.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'pause_button',
			array(
				'label'        => __( 'Pause Button', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Moving content needs a way to stop it (WCAG 2.2.2); hover does not help touch or keyboard users.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	/**
	 * Item style.
	 *
	 * @return void
	 */
	protected function register_item_style() {
		$this->start_controls_section(
			'style_items',
			array(
				'label' => __( 'Items', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'item_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-vm__card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-vm__card' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => __( 'Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-vm__item--card .eap-vm__card, {{WRAPPER}} .eap-vm__item--text .eap-vm__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .eap-vm__card',
			)
		);

		$this->add_responsive_control(
			'item_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-vm__card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .eap-vm__card',
			)
		);

		$this->add_responsive_control(
			'item_align',
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
					'{{WRAPPER}} .eap-vm__card' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Image style.
	 *
	 * @return void
	 */
	protected function register_image_style() {
		$this->start_controls_section(
			'style_image',
			array(
				'label' => __( 'Images', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'card_image_width',
			array(
				'label'       => __( 'Width On Cards', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array( 'min' => 16, 'max' => 400 ),
					'%'  => array( 'min' => 5, 'max' => 100 ),
				),
				'description' => __( 'An avatar or logo on a card. Image items always fill their column.', 'elementor-animatepro' ),
				'selectors'   => array(
					'{{WRAPPER}} .eap-vm__item--card .eap-vm__img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'image_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 100 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-vm__item--card .eap-vm__img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Text style.
	 *
	 * @return void
	 */
	protected function register_text_style() {
		$this->start_controls_section(
			'style_text',
			array(
				'label' => __( 'Text', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		foreach ( array(
			'title' => array( __( 'Title', 'elementor-animatepro' ), '.eap-vm__title' ),
			'body'  => array( __( 'Text', 'elementor-animatepro' ), '.eap-vm__text' ),
			'meta'  => array( __( 'Byline', 'elementor-animatepro' ), '.eap-vm__meta' ),
		) as $key => $def ) {
			$this->add_control(
				$key . '_heading',
				array(
					'label'     => $def[0],
					'type'      => Controls_Manager::HEADING,
					'separator' => 'title' === $key ? 'none' : 'before',
				)
			);

			$this->add_control(
				$key . '_color',
				array(
					'label'     => __( 'Color', 'elementor-animatepro' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $def[1] => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => $key . '_typography',
					'selector' => '{{WRAPPER}} ' . $def[1],
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Pause button style.
	 *
	 * @return void
	 */
	protected function register_pause_style() {
		$this->start_controls_section(
			'style_pause',
			array(
				'label'     => __( 'Pause Button', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'pause_button' => 'yes' ),
			)
		);

		$this->add_control(
			'pause_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-vm__pause' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pause_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-vm__pause' => 'background-color: {{VALUE}};',
				),
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
		$s     = $this->get_settings_for_display();
		$items = isset( $s['items'] ) && is_array( $s['items'] ) ? array_values( $s['items'] ) : array();

		if ( empty( $items ) ) {
			if ( $this->eap_is_editor() ) {
				echo '<div class="eap-widget eap-vm eap-vm--empty">' . esc_html__( 'Add items to get started.', 'elementor-animatepro' ) . '</div>';
			}
			return;
		}

		$direction = 'down' === ( $s['direction'] ?? 'up' ) ? 'down' : 'up';

		$config = array(
			'speed'      => max( 1, (int) ( $s['speed']['size'] ?? 40 ) ),
			'direction'  => $direction,
			'alternate'  => 'yes' === ( $s['alternate'] ?? 'yes' ),
			'stagger'    => 'yes' === ( $s['stagger'] ?? 'yes' ),
			'distribute' => 'repeat' === ( $s['distribute'] ?? 'split' ) ? 'repeat' : 'split',
			'i18n'       => array(
				'pause' => __( 'Pause the scrolling', 'elementor-animatepro' ),
				'play'  => __( 'Play the scrolling', 'elementor-animatepro' ),
			),
		);

		$classes = array( 'eap-widget', 'eap-vm' );
		if ( 'yes' === ( $s['fade_edges'] ?? 'yes' ) ) {
			$classes[] = 'eap-vm--fade';
		}
		if ( 'yes' === ( $s['pause_on_hover'] ?? 'yes' ) ) {
			$classes[] = 'eap-vm--hover-pause';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes, true );
		$this->add_render_attribute( 'wrapper', 'data-eap-vm', wp_json_encode( $config ), true );
		$this->add_render_attribute( 'wrapper', 'role', 'region', true );
		$this->add_render_attribute( 'wrapper', 'aria-label', (string) ( $s['region_label'] ?? __( 'Scrolling highlights', 'elementor-animatepro' ) ), true );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="eap-vm__viewport">
				<ul class="eap-vm__source" role="list">
					<?php
					foreach ( $items as $index => $item ) {
						$this->render_item( $item, $index );
					}
					?>
				</ul>
			</div>

			<?php if ( 'yes' === ( $s['pause_button'] ?? 'yes' ) ) : ?>
				<button type="button" class="eap-vm__pause" aria-pressed="false" aria-label="<?php echo esc_attr( $config['i18n']['pause'] ); ?>" hidden>
					<svg class="eap-vm__pause-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5h3v14H8zM13 5h3v14h-3z" fill="currentColor"/></svg>
					<svg class="eap-vm__play-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5l11 7-11 7z" fill="currentColor"/></svg>
				</button>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * One item.
	 *
	 * @param array $item  Repeater row.
	 * @param int   $index Row index.
	 * @return void
	 */
	protected function render_item( $item, $index ) {
		$type  = in_array( $item['item_type'] ?? 'card', array( 'card', 'image', 'text' ), true ) ? $item['item_type'] : 'card';
		$title = trim( (string) ( $item['title'] ?? '' ) );
		$text  = trim( (string) ( $item['text'] ?? '' ) );
		$meta  = trim( (string) ( $item['meta'] ?? '' ) );

		$image = '';
		if ( 'text' !== $type ) {
			$image_id = ! empty( $item['image']['id'] ) ? (int) $item['image']['id'] : 0;
			if ( $image_id ) {
				$image = wp_get_attachment_image(
					$image_id,
					'image' === $type ? 'large' : 'thumbnail',
					false,
					array(
						'class'   => 'eap-vm__img',
						'loading' => 'lazy',
						'alt'     => $title,
					)
				);
			} elseif ( ! empty( $item['image']['url'] ) ) {
				$image = sprintf( '<img class="eap-vm__img" src="%1$s" alt="%2$s" loading="lazy">', esc_url( $item['image']['url'] ), esc_attr( $title ) );
			} elseif ( 'image' === $type ) {
				$image = sprintf( '<img class="eap-vm__img" src="%1$s" alt="%2$s" loading="lazy">', esc_url( Utils::get_placeholder_image_src() ), esc_attr( $title ) );
			}
		}

		$has_link = ! empty( $item['link']['url'] );
		$key      = 'vm_link_' . $index;
		if ( $has_link ) {
			$this->remove_render_attribute( $key );
			$this->add_link_attributes( $key, $item['link'] );
			$this->add_render_attribute( $key, 'class', 'eap-vm__card' );
		}
		?>
		<li class="eap-vm__item eap-vm__item--<?php echo esc_attr( $type ); ?>">
			<?php if ( $has_link ) : ?>
				<a <?php $this->print_render_attribute_string( $key ); ?>>
			<?php else : ?>
				<div class="eap-vm__card">
			<?php endif; ?>

				<?php
				if ( '' !== $image ) {
					echo wp_kses_post( $image );
				}
				?>

				<?php if ( 'image' !== $type && '' !== $title ) : ?>
					<span class="eap-vm__title"><?php echo esc_html( $title ); ?></span>
				<?php endif; ?>

				<?php if ( 'card' === $type && '' !== $text ) : ?>
					<span class="eap-vm__text"><?php echo esc_html( $text ); ?></span>
				<?php endif; ?>

				<?php if ( 'card' === $type && '' !== $meta ) : ?>
					<span class="eap-vm__meta"><?php echo esc_html( $meta ); ?></span>
				<?php endif; ?>

			<?php if ( $has_link ) : ?>
				</a>
			<?php else : ?>
				</div>
			<?php endif; ?>
		</li>
		<?php
	}
}
