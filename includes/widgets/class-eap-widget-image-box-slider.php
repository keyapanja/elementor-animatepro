<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class EAP_Widget_Image_Box_Slider extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-image-box-slider';
	}

	public function get_title() {
		return __( 'Image Box Slider', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_style_depends() {
		return $this->eap_with_swiper_style(
			array(
				'eap-core',
				'eap-image-box',
				'eap-image-box-slider',
			)
		);
	}

	public function get_script_depends() {
		$deps = array(
			'eap-core-runtime',
			'eap-visibility-script',
			'eap-image-box-script',
			'eap-image-box-slider-script',
		);
		if ( wp_script_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}

		return array_unique( $deps );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'standard',
				'options' => array(
					'standard'    => __( 'Standard Image Box', 'elementor-animatepro' ),
					'vertical'    => __( 'Vertical Image Box', 'elementor-animatepro' ),
					'interactive' => __( 'Interactive Image Box', 'elementor-animatepro' ),
					'classic'     => __( 'Classic Image Box', 'elementor-animatepro' ),
					'pointer'     => __( 'Pointer Image Box', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'vertical_media_position',
			array(
				'label'     => __( 'Image Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => array(
					'left'  => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'condition' => array(
					'layout' => 'vertical',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slides',
			array(
				'label' => __( 'Slides', 'elementor-animatepro' ),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);
		$repeater->add_control(
			'subtitle',
			array(
				'label'       => __( 'Subtitle', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Style Prefix', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Prefix Control', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'description',
			array(
				'label'       => __( 'Description', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Add stylish image storytelling with flexible hover transitions, layout switches, and modern content animation.', 'elementor-animatepro' ),
				'rows'        => 4,
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'tooltip_heading',
			array(
				'label'       => __( 'Pointer Tooltip Heading', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'View Project', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'tooltip_description',
			array(
				'label'       => __( 'Pointer Tooltip Description', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Follow the cursor inside the image box with a modern floating tooltip.', 'elementor-animatepro' ),
				'rows'        => 3,
			)
		);
		$repeater->add_control(
			'link',
			array(
				'label'         => __( 'Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
			)
		);

		$this->add_control(
			'slides',
			array(
				'label'       => __( 'Slides', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'title'    => __( 'Prefix Control', 'elementor-animatepro' ),
						'subtitle' => __( 'Style Prefix', 'elementor-animatepro' ),
					),
					array(
						'title'    => __( 'Image Story', 'elementor-animatepro' ),
						'subtitle' => __( 'Creative Layout', 'elementor-animatepro' ),
					),
					array(
						'title'    => __( 'Visual Motion', 'elementor-animatepro' ),
						'subtitle' => __( 'Subtle Transition', 'elementor-animatepro' ),
					),
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

		$this->start_controls_section(
			'section_slider_settings',
			array(
				'label' => __( 'Slider Settings', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'slides_per_view',
			array(
				'label'          => __( 'Slides To Show', 'elementor-animatepro' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
			)
		);

		$this->add_responsive_control(
			'space_between',
			array(
				'label'      => __( 'Space Between', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'size' => 24,
					'unit' => 'px',
				),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => __( 'Loop', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => __( 'Autoplay Delay (ms)', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4000,
				'condition' => array(
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => __( 'Pause On Hover', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => __( 'Transition Speed (ms)', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 650,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_navigation',
			array(
				'label' => __( 'Navigation & Pagination', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_arrows',
			array(
				'label'        => __( 'Arrows', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'prev_icon',
			array(
				'label'     => __( 'Previous Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'next_icon',
			array(
				'label'     => __( 'Next Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_pagination',
			array(
				'label'        => __( 'Pagination', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'     => __( 'Pagination Type', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bullets',
				'options'   => array(
					'bullets'     => __( 'Dots', 'elementor-animatepro' ),
					'fraction'    => __( 'Fraction', 'elementor-animatepro' ),
					'progressbar' => __( 'Progress Bar', 'elementor-animatepro' ),
				),
				'condition' => array(
					'show_pagination' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->register_shared_image_box_style_controls();
		$this->register_slider_nav_style_controls();
		$this->register_slider_pagination_style_controls();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$slider_id = 'eap-image-box-slider-' . $this->get_id();

		$data = array(
			'slidesDesktop' => ! empty( $settings['slides_per_view'] ) ? (int) $settings['slides_per_view'] : 3,
			'slidesTablet'  => ! empty( $settings['slides_per_view_tablet'] ) ? (int) $settings['slides_per_view_tablet'] : 2,
			'slidesMobile'  => ! empty( $settings['slides_per_view_mobile'] ) ? (int) $settings['slides_per_view_mobile'] : 1,
			'spaceBetween'  => ! empty( $settings['space_between']['size'] ) ? (int) $settings['space_between']['size'] : 24,
			'loop'          => 'yes' === $settings['loop'],
			'autoplay'      => 'yes' === $settings['autoplay'],
			'autoplayDelay' => ! empty( $settings['autoplay_delay'] ) ? (int) $settings['autoplay_delay'] : 4000,
			'pauseOnHover'  => 'yes' === $settings['pause_on_hover'],
			'speed'         => ! empty( $settings['speed'] ) ? (int) $settings['speed'] : 650,
			'pagination'    => 'yes' === $settings['show_pagination'],
			'paginationType'=> ! empty( $settings['pagination_type'] ) ? $settings['pagination_type'] : 'bullets',
			'navigation'    => 'yes' === $settings['show_arrows'],
		);

		$wrapper_classes = array(
			'eap-image-box-slider',
			'eap-image-box-slider--' . $settings['layout'],
		);
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-eap-image-box-slider-root>
			<div
				id="<?php echo esc_attr( $slider_id ); ?>"
				class="swiper eap-image-box-slider__swiper"
				data-eap-image-box-slider="<?php echo esc_attr( wp_json_encode( $data ) ); ?>"
			>
				<div class="swiper-wrapper">
					<?php foreach ( $settings['slides'] as $index => $slide ) : ?>
						<div class="swiper-slide">
							<?php echo $this->render_slide_item( $settings, $slide, $index ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endforeach; ?>
				</div>

				<?php if ( 'yes' === $settings['show_arrows'] ) : ?>
					<button type="button" class="eap-image-box-slider__arrow eap-image-box-slider__arrow--prev" aria-label="<?php esc_attr_e( 'Previous slide', 'elementor-animatepro' ); ?>">
						<?php Icons_Manager::render_icon( $settings['prev_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</button>
					<button type="button" class="eap-image-box-slider__arrow eap-image-box-slider__arrow--next" aria-label="<?php esc_attr_e( 'Next slide', 'elementor-animatepro' ); ?>">
						<?php Icons_Manager::render_icon( $settings['next_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</button>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_pagination'] ) : ?>
					<div class="eap-image-box-slider__pagination"></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a slide item.
	 *
	 * @param array $settings Widget settings.
	 * @param array $slide Slide settings.
	 * @param int   $index Slide index.
	 * @return string
	 */
	private function render_slide_item( $settings, $slide, $index ) {
		$layout = $settings['layout'];
		$link_open = '';
		$link_close = '';
		$item_key = 'slide_link_' . $index;
		$item_settings = array_merge( $settings, $slide );
		$image = Group_Control_Image_Size::get_attachment_image_html( $item_settings, 'image_size', 'image' );

		if ( empty( $image ) && ! empty( $slide['image']['url'] ) ) {
			$image = sprintf( '<img src="%1$s" alt="%2$s" />', esc_url( $slide['image']['url'] ), esc_attr( $slide['title'] ) );
		}

		if ( ! empty( $slide['link']['url'] ) ) {
			$this->add_link_attributes( $item_key, $slide['link'] );
			$link_open  = '<a class="eap-image-box-card" ' . $this->get_render_attribute_string( $item_key ) . '>';
			$link_close = '</a>';
		} else {
			$link_open  = '<div class="eap-image-box-card">';
			$link_close = '</div>';
		}

		$classes = array(
			'eap-widget',
			'eap-image-box',
			'eap-image-box-slide',
			'eap-image-box--' . $layout,
			'eap-image-box--hover-' . $settings['hover_effect'],
			'eap-image-box--unfold-' . $settings['unfold_direction'],
			'eap-image-box--height-' . $settings['image_height_mode'],
		);

		if ( 'vertical' === $layout ) {
			$classes[] = 'eap-image-box--media-' . $settings['vertical_media_position'];
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-eap-image-box>
			<?php echo $link_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div class="eap-image-box-media-wrap">
					<div class="eap-image-box-media">
						<?php echo wp_kses_post( $image ); ?>
					</div>
					<?php if ( 'vertical' === $layout && 'yes' === $settings['show_hover_icon'] ) : ?>
						<span class="eap-image-box-hover-icon eap-image-box-hover-icon--<?php echo esc_attr( $settings['hover_icon_position'] ); ?>">
							<span class="eap-image-box-hover-icon-inner">
								<?php Icons_Manager::render_icon( $settings['hover_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							</span>
						</span>
					<?php endif; ?>
					<?php if ( 'pointer' === $layout ) : ?>
						<span class="eap-image-box-tooltip">
							<?php if ( ! empty( $slide['tooltip_heading'] ) ) : ?>
								<span class="eap-image-box-tooltip-heading"><?php echo esc_html( $slide['tooltip_heading'] ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $slide['tooltip_description'] ) ) : ?>
								<span class="eap-image-box-tooltip-description"><?php echo esc_html( $slide['tooltip_description'] ); ?></span>
							<?php endif; ?>
						</span>
					<?php endif; ?>
				</div>
				<div class="eap-image-box-copy">
					<?php if ( ! empty( $slide['subtitle'] ) && in_array( $layout, array( 'vertical', 'classic' ), true ) ) : ?>
						<div class="eap-image-box-subtitle"><?php echo esc_html( $slide['subtitle'] ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( $slide['title'] ) ) : ?>
						<h3 class="eap-image-box-title"><?php echo esc_html( $slide['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $slide['description'] ) && 'pointer' !== $layout ) : ?>
						<div class="eap-image-box-description"><?php echo esc_html( $slide['description'] ); ?></div>
					<?php endif; ?>
				</div>
			<?php echo $link_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Register the shared image box styling controls.
	 *
	 * @return void
	 */
	private function register_shared_image_box_style_controls() {
		$this->start_controls_section(
			'section_box_style',
			array(
				'label' => __( 'Box', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'box_background',
				'selector' => '{{WRAPPER}} .eap-image-box-card',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .eap-image-box-card',
			)
		);

		$this->add_control(
			'box_hover_background',
			array(
				'label'     => __( 'Hover Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-slide:hover .eap-image-box-card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => __( 'Content Padding', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-copy' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'media_padding',
			array(
				'label'      => __( 'Image Outer Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card' => '--eap-image-box-media-space-top: {{TOP}}{{UNIT}}; --eap-image-box-media-space-right: {{RIGHT}}{{UNIT}}; --eap-image-box-media-space-bottom: {{BOTTOM}}{{UNIT}}; --eap-image-box-media-space-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => __( 'Image', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'image_height_mode',
			array(
				'label'   => __( 'Image Height Mode', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => array(
					'auto'   => __( 'Auto', 'elementor-animatepro' ),
					'custom' => __( 'Custom', 'elementor-animatepro' ),
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
					'px' => array(
						'min' => 120,
						'max' => 900,
					),
					'vh' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-media' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'image_height_mode' => 'custom',
				),
			)
		);

		$this->add_control(
			'image_object_fit',
			array(
				'label'     => __( 'Object Fit', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => array(
					'cover'   => __( 'Cover', 'elementor-animatepro' ),
					'contain' => __( 'Contain', 'elementor-animatepro' ),
					'fill'    => __( 'Fill', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-media img' => 'object-fit: {{VALUE}};',
				),
				'condition' => array(
					'image_height_mode' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'image_radius',
			array(
				'label'      => __( 'Image Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-media, {{WRAPPER}} .eap-image-box-media img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'unfold_direction',
			array(
				'label'   => __( 'Unfold Direction', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'   => __( 'Left to Right', 'elementor-animatepro' ),
					'right'  => __( 'Right to Left', 'elementor-animatepro' ),
					'top'    => __( 'Top to Bottom', 'elementor-animatepro' ),
					'bottom' => __( 'Bottom to Top', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'label'   => __( 'Image Hover Effect', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'zoom-in',
				'options' => array(
					'zoom-in'  => __( 'Zoom In', 'elementor-animatepro' ),
					'zoom-out' => __( 'Zoom Out', 'elementor-animatepro' ),
					'fade'     => __( 'Fade', 'elementor-animatepro' ),
					'bw-color' => __( 'Black & White to Color', 'elementor-animatepro' ),
					'none'     => __( 'None', 'elementor-animatepro' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_overlay_style',
			array(
				'label' => __( 'Overlay', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'interactive_overlay_color',
			array(
				'label'     => __( 'Interactive Overlay Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(7, 12, 20, 0.82)',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box--interactive .eap-image-box-copy::before' => 'background: linear-gradient(180deg, rgba(7, 12, 20, 0.08), {{VALUE}});',
				),
				'condition' => array(
					'layout' => 'interactive',
				),
			)
		);

		$this->add_control(
			'interactive_overlay_hover_color',
			array(
				'label'     => __( 'Interactive Overlay Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-slide:hover.eap-image-box--interactive .eap-image-box-copy::before' => 'background: linear-gradient(180deg, rgba(7, 12, 20, 0.08), {{VALUE}});',
				),
				'condition' => array(
					'layout' => 'interactive',
				),
			)
		);

		$this->add_control(
			'classic_overlay_color',
			array(
				'label'     => __( 'Classic Overlay Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(7, 12, 20, 0.82)',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box--classic .eap-image-box-copy::before' => 'background: linear-gradient(180deg, rgba(7, 12, 20, 0.08), {{VALUE}});',
				),
				'condition' => array(
					'layout' => 'classic',
				),
			)
		);

		$this->add_control(
			'classic_overlay_hover_color',
			array(
				'label'     => __( 'Classic Overlay Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-slide:hover.eap-image-box--classic .eap-image-box-copy::before' => 'background: linear-gradient(180deg, rgba(7, 12, 20, 0.08), {{VALUE}});',
				),
				'condition' => array(
					'layout' => 'classic',
				),
			)
		);

		$this->end_controls_section();

		$this->register_text_style_controls( 'subtitle', __( 'Subtitle', 'elementor-animatepro' ), '.eap-image-box-subtitle' );
		$this->register_text_style_controls( 'title', __( 'Title', 'elementor-animatepro' ), '.eap-image-box-title' );
		$this->register_text_style_controls( 'description', __( 'Description', 'elementor-animatepro' ), '.eap-image-box-description' );
		$this->register_text_style_controls( 'tooltip_heading', __( 'Tooltip Heading', 'elementor-animatepro' ), '.eap-image-box-tooltip-heading' );
		$this->register_text_style_controls( 'tooltip_description', __( 'Tooltip Description', 'elementor-animatepro' ), '.eap-image-box-tooltip-description' );

		$this->start_controls_section(
			'section_tooltip_style',
			array(
				'label'     => __( 'Pointer Tooltip', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => 'pointer',
				),
			)
		);

		$this->add_control(
			'tooltip_bg',
			array(
				'label'     => __( 'Background Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#09111f',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-tooltip' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tooltip_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-tooltip' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tooltip_typography',
				'selector' => '{{WRAPPER}} .eap-image-box-tooltip',
			)
		);

		$this->add_responsive_control(
			'tooltip_width',
			array(
				'label'      => __( 'Tooltip Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 80,
						'max' => 360,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-tooltip' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tooltip_radius',
			array(
				'label'      => __( 'Tooltip Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			array(
				'label'     => __( 'Hover Icon', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => 'vertical',
				),
			)
		);

		$this->add_control(
			'show_hover_icon',
			array(
				'label'        => __( 'Show Hover Icon', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'hover_icon',
			array(
				'label'     => __( 'Hover Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-hover-icon' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_glyph_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 72,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-hover-icon-inner' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .eap-image-box-hover-icon-inner svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_background',
			array(
				'label'     => __( 'Background Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff6b2c',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-hover-icon' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_rotate',
			array(
				'label'      => __( 'Hover Rotate', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default'    => array(
					'size' => 0,
					'unit' => 'deg',
				),
				'range'      => array(
					'deg' => array(
						'min' => -360,
						'max' => 360,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card:hover .eap-image-box-hover-icon-inner' => '--eap-hover-icon-rotate: {{SIZE}}deg;',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_scale',
			array(
				'label'      => __( 'Hover Scale', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'custom' ),
				'range'      => array(
					'custom' => array(
						'min'  => 0.2,
						'max'  => 3,
						'step' => 0.05,
					),
				),
				'default'    => array(
					'size' => 1,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card:hover .eap-image-box-hover-icon-inner' => '--eap-hover-icon-scale: {{SIZE}};',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_translate_x',
			array(
				'label'      => __( 'Hover Offset X', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => -120,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card:hover .eap-image-box-hover-icon-inner' => '--eap-hover-icon-translate-x: {{SIZE}}px;',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_translate_y',
			array(
				'label'      => __( 'Hover Offset Y', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => -120,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card:hover .eap-image-box-hover-icon-inner' => '--eap-hover-icon-translate-y: {{SIZE}}px;',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_skew_x',
			array(
				'label'      => __( 'Hover Skew X', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array(
						'min' => -60,
						'max' => 60,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card:hover .eap-image-box-hover-icon-inner' => '--eap-hover-icon-skew-x: {{SIZE}}deg;',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_skew_y',
			array(
				'label'      => __( 'Hover Skew Y', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array(
						'min' => -60,
						'max' => 60,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-card:hover .eap-image-box-hover-icon-inner' => '--eap-hover-icon-skew-y: {{SIZE}}deg;',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'hover_icon_size',
			array(
				'label'      => __( 'Icon Box Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 34,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-hover-icon' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'hover_icon_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-hover-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_icon_position',
			array(
				'label'     => __( 'Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top-right',
				'options'   => array(
					'top-left'      => __( 'Top Left', 'elementor-animatepro' ),
					'top-center'    => __( 'Top Center', 'elementor-animatepro' ),
					'top-right'     => __( 'Top Right', 'elementor-animatepro' ),
					'center-left'   => __( 'Center Left', 'elementor-animatepro' ),
					'center'        => __( 'Center', 'elementor-animatepro' ),
					'center-right'  => __( 'Center Right', 'elementor-animatepro' ),
					'bottom-left'   => __( 'Bottom Left', 'elementor-animatepro' ),
					'bottom-center' => __( 'Bottom Center', 'elementor-animatepro' ),
					'bottom-right'  => __( 'Bottom Right', 'elementor-animatepro' ),
				),
				'condition' => array(
					'show_hover_icon' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register title/subtitle/description controls.
	 *
	 * @param string $name Control key.
	 * @param string $label Section label.
	 * @param string $selector CSS selector.
	 * @return void
	 */
	private function register_text_style_controls( $name, $label, $selector ) {
		$this->start_controls_section(
			'section_style_' . $name,
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			$name . '_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $selector => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			$name . '_hover_color',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-slide:hover ' . $selector => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $name . '_typography',
				'selector' => '{{WRAPPER}} ' . $selector,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register arrow style controls.
	 *
	 * @return void
	 */
	private function register_slider_nav_style_controls() {
		$this->start_controls_section(
			'section_nav_style',
			array(
				'label'     => __( 'Navigation', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'nav_icon_color',
			array(
				'label'     => __( 'Icon Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-slider__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_background',
			array(
				'label'     => __( 'Container Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#09111f',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-slider__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_box_size',
			array(
				'label'      => __( 'Container Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 24,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-slider__arrow' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'nav_icon_size',
			array(
				'label'      => __( 'Icon Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 42,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-slider__arrow' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .eap-image-box-slider__arrow svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'nav_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-slider__arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register pagination style controls.
	 *
	 * @return void
	 */
	private function register_slider_pagination_style_controls() {
		$this->start_controls_section(
			'section_pagination_style',
			array(
				'label'     => __( 'Pagination', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_pagination' => 'yes',
				),
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#09111f',
				'selectors' => array(
					'{{WRAPPER}} .eap-image-box-slider__pagination' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-image-box-slider__pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eap-image-box-slider__pagination .swiper-pagination-progressbar-fill' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_size',
			array(
				'label'      => __( 'Dots / Number Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 6,
						'max' => 36,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-image-box-slider__pagination .swiper-pagination-bullet' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
					'{{WRAPPER}} .eap-image-box-slider__pagination' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .eap-image-box-slider__pagination.swiper-pagination-progressbar' => 'height: max(4px, calc({{SIZE}}px / 3));',
				),
			)
		);

		$this->end_controls_section();
	}
}
