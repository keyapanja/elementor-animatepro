<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

/**
 * Site Logo — displays the site's Custom Logo (Appearance → Customize → Site
 * Identity), a custom uploaded image, or the site title as text, linked to the
 * homepage (or a custom URL / nothing). Falls back to the site title when no
 * Custom Logo is set. CSS-only, no script. Intended for header/footer templates.
 */
class EAP_Widget_Site_Logo extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-site-logo';
	}

	public function get_title() {
		return __( 'Site Logo', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-site-logo';
	}

	public function get_keywords() {
		return array( 'site', 'logo', 'brand', 'header', 'footer', 'identity', 'title' );
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'site-logo' );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_image_style();
		$this->register_title_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_logo',
			array(
				'label' => __( 'Site Logo', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'Source', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'site',
				'options' => array(
					'site'   => __( 'Site Logo', 'elementor-animatepro' ),
					'custom' => __( 'Custom Image', 'elementor-animatepro' ),
					'title'  => __( 'Site Title', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'site_logo_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(
					/* translators: %s: Customizer link. */
					__( 'Uses the logo from %s. If none is set, the site title shows instead.', 'elementor-animatepro' ),
					'<a href="' . esc_url( admin_url( 'customize.php?autofocus[section]=title_tagline' ) ) . '" target="_blank">' . esc_html__( 'Site Identity', 'elementor-animatepro' ) . '</a>'
				),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'source' => 'site' ),
			)
		);

		$this->add_control(
			'custom_image',
			array(
				'label'     => __( 'Image', 'elementor-animatepro' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'source' => 'custom' ),
			)
		);

		$this->add_control(
			'image_alt',
			array(
				'label'       => __( 'Alt Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Defaults to the image / site name', 'elementor-animatepro' ),
				'condition'   => array( 'source' => array( 'site', 'custom' ) ),
			)
		);

		$this->add_control(
			'link_to',
			array(
				'label'   => __( 'Link', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'home',
				'options' => array(
					'home'   => __( 'Home', 'elementor-animatepro' ),
					'custom' => __( 'Custom URL', 'elementor-animatepro' ),
					'none'   => __( 'None', 'elementor-animatepro' ),
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
					'{{WRAPPER}} .eap-site-logo' => 'text-align: {{VALUE}};',
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
				'label'     => __( 'Logo', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'source!' => 'title' ),
			)
		);

		$this->add_responsive_control(
			'logo_width',
			array(
				'label'      => __( 'Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 20, 'max' => 600 ),
					'%'  => array( 'min' => 5, 'max' => 100 ),
					'vw' => array( 'min' => 5, 'max' => 100 ),
				),
				'default'    => array( 'size' => 150, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-site-logo__img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'logo_max_width',
			array(
				'label'      => __( 'Max Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 20, 'max' => 800 ),
					'%'  => array( 'min' => 5, 'max' => 100 ),
				),
				'default'    => array( 'size' => 100, 'unit' => '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-site-logo__img' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'logo_height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 400 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-site-logo__img' => 'height: {{SIZE}}{{UNIT}};',
				),
				'description' => __( 'Leave empty to keep the natural height.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'object_fit',
			array(
				'label'     => __( 'Object Fit', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'contain',
				'options'   => array(
					'contain' => __( 'Contain', 'elementor-animatepro' ),
					'cover'   => __( 'Cover', 'elementor-animatepro' ),
					'fill'    => __( 'Fill', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-site-logo__img' => 'object-fit: {{VALUE}};',
				),
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
					'fade'      => __( 'Fade', 'elementor-animatepro' ),
					'zoom'      => __( 'Zoom', 'elementor-animatepro' ),
					'grayscale' => __( 'Grayscale → Color', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'hover_opacity',
			array(
				'label'     => __( 'Hover Opacity', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1,
				'step'      => 0.05,
				'default'   => 0.75,
				'selectors' => array(
					'{{WRAPPER}} .eap-site-logo' => '--eap-sl-hover-opacity: {{VALUE}};',
				),
				'condition' => array( 'hover_effect' => 'fade' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'logo_border',
				'selector' => '{{WRAPPER}} .eap-site-logo__img',
			)
		);

		$this->add_responsive_control(
			'logo_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-animatepro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-site-logo__img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'logo_shadow',
				'selector' => '{{WRAPPER}} .eap-site-logo__img',
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
				'condition' => array( 'source!' => 'custom' ),
			)
		);

		$this->add_control(
			'title_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Applies to the Site Title, and to the fallback text shown when no Site Logo is set.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1f2937',
				'selectors' => array(
					'{{WRAPPER}} .eap-site-logo__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.eap-site-logo__link:hover .eap-site-logo__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'selector'       => '{{WRAPPER}} .eap-site-logo__title',
				'fields_options' => array(
					'typography'  => array( 'default' => 'custom' ),
					'font_size'   => array( 'default' => array( 'size' => 26, 'unit' => 'px' ) ),
					'font_weight' => array( 'default' => '700' ),
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
		$source   = ! empty( $settings['source'] ) ? $settings['source'] : 'site';
		$link_to  = ! empty( $settings['link_to'] ) ? $settings['link_to'] : 'home';
		$hover    = ! empty( $settings['hover_effect'] ) ? $settings['hover_effect'] : 'none';

		$inner = $this->get_logo_markup( $settings, $source );
		if ( '' === $inner ) {
			return;
		}

		$this->add_render_attribute(
			'wrapper',
			'class',
			array(
				'eap-widget',
				'eap-site-logo',
				'eap-site-logo--source-' . $source,
				'eap-site-logo--hover-' . $hover,
			)
		);

		$has_link = ( 'home' === $link_to ) || ( 'custom' === $link_to && ! empty( $settings['custom_link']['url'] ) );
		$tag      = $has_link ? 'a' : 'span';

		$this->add_render_attribute( 'logo_link', 'class', 'eap-site-logo__link' );
		if ( 'home' === $link_to ) {
			$this->add_render_attribute( 'logo_link', 'href', esc_url( home_url( '/' ) ) );
			$this->add_render_attribute( 'logo_link', 'aria-label', get_bloginfo( 'name' ) );
		} elseif ( 'custom' === $link_to && ! empty( $settings['custom_link']['url'] ) ) {
			$this->add_link_attributes( 'logo_link', $settings['custom_link'] );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<<?php echo esc_html( $tag ); ?> <?php $this->print_render_attribute_string( 'logo_link' ); ?>>
				<?php echo $inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped helpers below. ?>
			</<?php echo esc_html( $tag ); ?>>
		</div>
		<?php
	}

	/**
	 * Build the inner logo markup (image or title), already escaped.
	 *
	 * @param array  $settings Settings.
	 * @param string $source   site|custom|title.
	 * @return string
	 */
	protected function get_logo_markup( $settings, $source ) {
		$alt = ! empty( $settings['image_alt'] ) ? $settings['image_alt'] : '';

		if ( 'title' === $source ) {
			return $this->title_markup();
		}

		if ( 'custom' === $source ) {
			$id  = ! empty( $settings['custom_image']['id'] ) ? (int) $settings['custom_image']['id'] : 0;
			$url = ! empty( $settings['custom_image']['url'] ) ? $settings['custom_image']['url'] : '';
			return $this->image_markup( $id, $url, $alt );
		}

		// Site logo from the Customizer.
		$logo_id = (int) get_theme_mod( 'custom_logo' );
		if ( $logo_id ) {
			return $this->image_markup( $logo_id, '', $alt );
		}

		// No custom logo set — fall back to the site title.
		return $this->title_markup();
	}

	protected function title_markup() {
		return '<span class="eap-site-logo__title">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
	}

	/**
	 * Render an image from an attachment id (with srcset) or a raw URL.
	 *
	 * @param int    $id  Attachment id (0 if none).
	 * @param string $url Fallback URL.
	 * @param string $alt Alt override.
	 * @return string
	 */
	protected function image_markup( $id, $url, $alt ) {
		if ( $id ) {
			$attr = array( 'class' => 'eap-site-logo__img' );
			if ( '' !== $alt ) {
				$attr['alt'] = $alt;
			}
			$img = wp_get_attachment_image( $id, 'full', false, $attr );
			if ( $img ) {
				return $img;
			}
			$url = $url ? $url : (string) wp_get_attachment_image_url( $id, 'full' );
		}

		if ( '' === $url ) {
			return '';
		}

		return sprintf(
			'<img class="eap-site-logo__img" src="%s" alt="%s" />',
			esc_url( $url ),
			esc_attr( $alt )
		);
	}
}
