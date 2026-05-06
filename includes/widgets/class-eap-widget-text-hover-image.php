<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

class EAP_Widget_Text_Hover_Image extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-text-hover-image';
	}

	public function get_title() {
		return __( 'Text Hover Image', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-image-rollover';
	}

	public function get_style_depends() {
		return $this->get_widget_style_depends( 'text-hover-image' );
	}

	public function get_script_depends() {
		return array(
			'eap-core-runtime',
			'eap-text-hover-image-script',
		);
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'before_hover_text',
			array(
				'label'       => __( 'Before Hover Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'I’m', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'hover_text',
			array(
				'label'       => __( 'Hover Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Mariya', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'after_hover_text',
			array(
				'label'       => __( 'After Hover Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'the awarded dancer', 'elementor-animatepro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'hover_image',
			array(
				'label'   => __( 'Hover Image', 'elementor-animatepro' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_control(
			'html_tag',
			array(
				'label'   => __( 'HTML Tag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'p'    => 'p',
					'span' => 'span',
				),
			)
		);

		$this->add_control(
			'link',
			array(
				'label'         => __( 'Link', 'elementor-animatepro' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'placeholder'   => __( 'Type or paste your URL', 'elementor-animatepro' ),
			)
		);

		$this->add_alignment_control();

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'section_style_text',
			array(
				'label' => __( 'Style', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-text-hover-image__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'text_hover_color',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-text-hover-image__wrap:hover .eap-text-hover-image__text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eap-text-hover-image__wrap.is-active .eap-text-hover-image__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .eap-text-hover-image__text',
			)
		);

		if ( class_exists( '\Elementor\Group_Control_Text_Stroke' ) ) {
			$this->add_group_control(
				Group_Control_Text_Stroke::get_type(),
				array(
					'name'     => 'text_stroke',
					'selector' => '{{WRAPPER}} .eap-text-hover-image__text',
				)
			);
		}

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'text_shadow',
				'selector' => '{{WRAPPER}} .eap-text-hover-image__text',
			)
		);

		$this->add_control(
			'blend_mode',
			array(
				'label'     => __( 'Blend Mode', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'normal',
				'options'   => array(
					'normal'      => __( 'Normal', 'elementor-animatepro' ),
					'multiply'    => __( 'Multiply', 'elementor-animatepro' ),
					'screen'      => __( 'Screen', 'elementor-animatepro' ),
					'overlay'     => __( 'Overlay', 'elementor-animatepro' ),
					'darken'      => __( 'Darken', 'elementor-animatepro' ),
					'lighten'     => __( 'Lighten', 'elementor-animatepro' ),
					'color-dodge' => __( 'Color Dodge', 'elementor-animatepro' ),
					'color-burn'  => __( 'Color Burn', 'elementor-animatepro' ),
					'hard-light'  => __( 'Hard Light', 'elementor-animatepro' ),
					'soft-light'  => __( 'Soft Light', 'elementor-animatepro' ),
					'difference'  => __( 'Difference', 'elementor-animatepro' ),
					'exclusion'   => __( 'Exclusion', 'elementor-animatepro' ),
					'hue'         => __( 'Hue', 'elementor-animatepro' ),
					'saturation'  => __( 'Saturation', 'elementor-animatepro' ),
					'color'       => __( 'Color', 'elementor-animatepro' ),
					'luminosity'  => __( 'Luminosity', 'elementor-animatepro' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-text-hover-image__text' => 'mix-blend-mode: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_hover_text',
			array(
				'label' => __( 'Hover Text', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'hover_text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-text-hover-image__trigger' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'hover_text_typography',
				'selector' => '{{WRAPPER}} .eap-text-hover-image__trigger',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			array(
				'label' => __( 'Image', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'      => __( 'Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 40,
						'max' => 1200,
					),
					'%'  => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 300,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-text-hover-image' => '--eap-text-hover-image-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => __( 'Height', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 40,
						'max' => 1200,
					),
					'%'  => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 360,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-text-hover-image' => '--eap-text-hover-image-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_position_top',
			array(
				'label'      => __( 'Position Top', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => -600,
						'max' => 600,
					),
					'%'  => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-text-hover-image' => '--eap-text-hover-image-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_position_left',
			array(
				'label'      => __( 'Position Left', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => -600,
						'max' => 600,
					),
					'%'  => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-text-hover-image' => '--eap-text-hover-image-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['hover_text'] ) ) {
			return;
		}

		$tag = ! empty( $settings['html_tag'] ) ? $settings['html_tag'] : 'h2';

		$this->add_render_attribute(
			'wrapper',
			array(
				'class' => array(
					'eap-widget',
					'eap-text-hover-image',
				),
			)
		);

		$image_html = '';
		if ( ! empty( $settings['hover_image']['url'] ) ) {
			$image_html = sprintf(
				'<img src="%1$s" alt="" loading="lazy" />',
				esc_url( $settings['hover_image']['url'] )
			);
		}

		$text_html  = '';
		$text_html .= '<span class="eap-text-hover-image__part">' . esc_html( $settings['before_hover_text'] ) . '</span> ';
		$text_html .= '<span class="eap-text-hover-image__trigger" tabindex="0">' . esc_html( $settings['hover_text'] ) . '</span> ';
		$text_html .= '<span class="eap-text-hover-image__part">' . esc_html( $settings['after_hover_text'] ) . '</span>';

		$link_open  = '';
		$link_close = '';
		if ( ! empty( $settings['link']['url'] ) ) {
			$link_attrs = ' href="' . esc_url( $settings['link']['url'] ) . '"';
			$rels       = array();

			if ( ! empty( $settings['link']['is_external'] ) ) {
				$link_attrs .= ' target="_blank"';
				$rels[]     = 'noopener';
			}
			if ( ! empty( $settings['link']['nofollow'] ) ) {
				$rels[] = 'nofollow';
			}
			if ( ! empty( $rels ) ) {
				$link_attrs .= ' rel="' . esc_attr( implode( ' ', array_unique( $rels ) ) ) . '"';
			}
			$link_open  = '<a class="eap-text-hover-image__link"' . $link_attrs . '>';
			$link_close = '</a>';
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="eap-text-hover-image__wrap" data-eap-text-hover-image>
				<?php echo wp_kses_post( $link_open ); ?>
				<<?php echo esc_html( $tag ); ?> class="eap-text-hover-image__text">
					<?php echo wp_kses_post( $text_html ); ?>
				</<?php echo esc_html( $tag ); ?>>
				<?php echo wp_kses_post( $link_close ); ?>
				<?php if ( $image_html ) : ?>
					<div class="eap-text-hover-image__preview" aria-hidden="true">
						<?php echo wp_kses_post( $image_html ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
