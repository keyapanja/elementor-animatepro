<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

/**
 * Post Featured Image — outputs the CURRENT post's featured image (dynamic) at a
 * chosen registered size, optionally linked (post / media file / custom) with an
 * optional caption. Falls back to a chosen image when the post has none, and
 * shows a placeholder in the editor so the widget is never blank. CSS-only.
 */
class EAP_Widget_Post_Featured_Image extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-post-featured-image';
	}

	public function get_title() {
		return __( 'Post Featured Image', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-featured-image';
	}

	public function get_keywords() {
		return array( 'post', 'featured', 'image', 'thumbnail', 'dynamic', 'single' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'post-featured-image' );
	}

	protected function register_controls() {
		$this->register_image_section();
		$this->register_image_style();
		$this->register_caption_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_image_section() {
		$this->start_controls_section(
			'section_image',
			array(
				'label' => __( 'Featured Image', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'   => __( 'Image Size', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'large',
				'options' => $this->get_image_size_options(),
			)
		);

		$this->add_control(
			'link_to',
			array(
				'label'   => __( 'Link', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'   => __( 'None', 'elementor-animatepro' ),
					'post'   => __( 'Post URL', 'elementor-animatepro' ),
					'media'  => __( 'Media File', 'elementor-animatepro' ),
					'custom' => __( 'Custom URL', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'custom_link',
			array(
				'label'         => __( 'Custom URL', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'placeholder'   => __( 'https://example.com', 'elementor-animatepro' ),
				'condition'     => array( 'link_to' => 'custom' ),
			)
		);

		$this->add_control(
			'show_caption',
			array(
				'label'        => __( 'Caption', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Uses the attachment caption from the Media Library.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'fallback_image',
			array(
				'label'       => __( 'Fallback Image', 'elementor-animatepro' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Shown when the post has no featured image.', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'                => __( 'Alignment', 'elementor-animatepro' ),
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
				'selectors'            => array(
					'{{WRAPPER}} .eap-post-featured-image' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_image_style() {
		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => __( 'Image', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'img_width',
			array(
				'label'      => __( 'Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 20, 'max' => 1200 ),
					'%'  => array( 'min' => 5, 'max' => 100 ),
					'vw' => array( 'min' => 5, 'max' => 100 ),
				),
				'default'    => array( 'size' => 100, 'unit' => '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-featured-image__img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'img_max_width',
			array(
				'label'      => __( 'Max Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 20, 'max' => 1400 ),
					'%'  => array( 'min' => 5, 'max' => 100 ),
				),
				'default'    => array( 'size' => 100, 'unit' => '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-featured-image__img' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'img_height',
			array(
				'label'       => __( 'Height', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'vh' ),
				'range'       => array( 'px' => array( 'min' => 40, 'max' => 900 ) ),
				'selectors'   => array(
					'{{WRAPPER}} .eap-post-featured-image__img' => 'height: {{SIZE}}{{UNIT}};',
				),
				'description' => __( 'Leave empty to keep the natural aspect ratio.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'object_fit',
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
					'{{WRAPPER}} .eap-post-featured-image__img' => 'object-fit: {{VALUE}};',
				),
				'condition' => array( 'img_height[size]!' => '' ),
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'label'   => __( 'Hover Effect', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'      => __( 'None', 'elementor-animatepro' ),
					'zoom'      => __( 'Zoom', 'elementor-animatepro' ),
					'lift'      => __( 'Lift', 'elementor-animatepro' ),
					'grayscale' => __( 'Grayscale → Color', 'elementor-animatepro' ),
					'fade'      => __( 'Fade', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'img_border',
				'selector' => '{{WRAPPER}} .eap-post-featured-image__img',
			)
		);

		$this->add_responsive_control(
			'img_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-featured-image__img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'img_shadow',
				'selector' => '{{WRAPPER}} .eap-post-featured-image__img',
			)
		);

		$this->end_controls_section();
	}

	protected function register_caption_style() {
		$this->start_controls_section(
			'section_caption_style',
			array(
				'label'     => __( 'Caption', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_caption' => 'yes' ),
			)
		);

		$this->add_control(
			'caption_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-featured-image__caption' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'caption_typography',
				'selector' => '{{WRAPPER}} .eap-post-featured-image__caption',
			)
		);

		$this->add_responsive_control(
			'caption_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-featured-image__caption' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$post_id  = $this->eap_get_post_id();

		$size     = ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'large';
		$thumb_id = $post_id ? (int) get_post_thumbnail_id( $post_id ) : 0;

		$img_html    = '';
		$caption_src = 0;

		if ( $thumb_id ) {
			$img_html    = wp_get_attachment_image( $thumb_id, $size, false, array( 'class' => 'eap-post-featured-image__img' ) );
			$caption_src = $thumb_id;
		} elseif ( ! empty( $settings['fallback_image']['id'] ) ) {
			$fid         = (int) $settings['fallback_image']['id'];
			$img_html    = wp_get_attachment_image( $fid, $size, false, array( 'class' => 'eap-post-featured-image__img' ) );
			$caption_src = $fid;
		} elseif ( ! empty( $settings['fallback_image']['url'] ) ) {
			$img_html = sprintf(
				'<img class="eap-post-featured-image__img" src="%s" alt="" />',
				esc_url( $settings['fallback_image']['url'] )
			);
		} elseif ( $this->eap_is_editor() ) {
			$img_html = sprintf(
				'<img class="eap-post-featured-image__img" src="%s" alt="" />',
				esc_url( Utils::get_placeholder_image_src() )
			);
		}

		if ( '' === $img_html ) {
			return;
		}

		$hover = ! empty( $settings['hover_effect'] ) ? $settings['hover_effect'] : 'none';

		$this->add_render_attribute(
			'wrapper',
			'class',
			array(
				'eap-widget',
				'eap-post-featured-image',
				'eap-post-featured-image--hover-' . $hover,
			)
		);

		// Resolve link.
		$link_to = ! empty( $settings['link_to'] ) ? $settings['link_to'] : 'none';
		$open    = '';
		$close   = '';

		if ( 'post' === $link_to && $post_id ) {
			$open  = '<a class="eap-post-featured-image__link" href="' . esc_url( get_permalink( $post_id ) ) . '">';
			$close = '</a>';
		} elseif ( 'media' === $link_to && $thumb_id ) {
			$full  = wp_get_attachment_image_url( $thumb_id, 'full' );
			$open  = '<a class="eap-post-featured-image__link" href="' . esc_url( $full ) . '">';
			$close = '</a>';
		} elseif ( 'custom' === $link_to && ! empty( $settings['custom_link']['url'] ) ) {
			$this->add_render_attribute( 'custom_link', 'class', 'eap-post-featured-image__link' );
			$this->add_link_attributes( 'custom_link', $settings['custom_link'] );
			$open  = '<a ' . $this->get_render_attribute_string( 'custom_link' ) . '>';
			$close = '</a>';
		}

		$caption = '';
		if ( 'yes' === ( $settings['show_caption'] ?? '' ) && $caption_src ) {
			$cap = wp_get_attachment_caption( $caption_src );
			if ( $cap ) {
				$caption = '<figcaption class="eap-post-featured-image__caption">' . esc_html( $cap ) . '</figcaption>';
			}
		}
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<figure class="eap-post-featured-image__figure">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from wp_get_attachment_image() / escaped helpers above.
				echo $open . $img_html . $close . $caption;
				?>
			</figure>
		</div>
		<?php
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
