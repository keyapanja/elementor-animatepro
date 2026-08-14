<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

/**
 * Post Rating — a read-only star rating for the CURRENT post. Source: a Manual
 * editorial score, a numeric Custom Field (post meta), or the Visitor Average
 * (aggregate collected by the future Post Rating Form via the shared EAP_Rating
 * store). Supports fractional ratings (half stars) through a clipped fill
 * overlay, a chosen icon (star / heart / custom) and optional numeric value +
 * vote count. CSS-only, no script.
 */
class EAP_Widget_Post_Rating extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-post-rating';
	}

	public function get_title() {
		return __( 'Post Rating', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-rating';
	}

	public function get_keywords() {
		return array( 'post', 'rating', 'stars', 'review', 'score', 'dynamic' );
	}

	public function get_style_depends() {
		return array( 'eap-core', 'eap-post-rating', 'elementor-icons-fa-solid' );
	}

	protected function register_controls() {
		$this->register_rating_section();
		$this->register_stars_style();
		$this->register_text_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_rating_section() {
		$this->start_controls_section(
			'section_rating',
			array(
				'label' => __( 'Rating', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'Source', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => array(
					'manual'  => __( 'Manual (Editorial)', 'elementor-animatepro' ),
					'field'   => __( 'Custom Field', 'elementor-animatepro' ),
					'visitor' => __( 'Visitor Average', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'rating_value',
			array(
				'label'     => __( 'Rating', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'step'      => 0.1,
				'default'   => 4.5,
				'condition' => array( 'source' => 'manual' ),
			)
		);

		$this->add_control(
			'field_key',
			array(
				'label'       => __( 'Meta Field Key', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => 'rating',
				'description' => __( 'A post custom-field (meta) key holding the numeric rating.', 'elementor-animatepro' ),
				'condition'   => array( 'source' => 'field' ),
			)
		);

		$this->add_control(
			'visitor_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Shows the average of visitor votes. Add the Post Rating Form widget so visitors can rate.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'source' => 'visitor' ),
			)
		);

		$this->add_control(
			'max_rating',
			array(
				'label'   => __( 'Out Of', 'elementor-animatepro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 10,
				'default' => 5,
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'   => __( 'Icon', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'star',
				'options' => array(
					'star'   => __( 'Star', 'elementor-animatepro' ),
					'heart'  => __( 'Heart', 'elementor-animatepro' ),
					'custom' => __( 'Custom', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'custom_icon',
			array(
				'label'     => __( 'Custom Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'icon_type' => 'custom' ),
			)
		);

		$this->add_control(
			'show_value',
			array(
				'label'        => __( 'Show Value', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'value_position',
			array(
				'label'     => __( 'Value Position', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => array(
					'before' => __( 'Before Stars', 'elementor-animatepro' ),
					'after'  => __( 'After Stars', 'elementor-animatepro' ),
				),
				'condition' => array( 'show_value' => 'yes' ),
			)
		);

		$this->add_control(
			'show_count',
			array(
				'label'        => __( 'Show Vote Count', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'manual_count',
			array(
				'label'     => __( 'Vote Count', 'elementor-animatepro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'default'   => 0,
				'condition' => array(
					'source'     => 'manual',
					'show_count' => 'yes',
				),
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
					'{{WRAPPER}} .eap-post-rating' => 'justify-content: {{VALUE}};',
				),
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	protected function register_stars_style() {
		$this->start_controls_section(
			'section_stars_style',
			array(
				'label' => __( 'Stars', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'star_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 60 ) ),
				'default'    => array( 'size' => 22, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-rating' => '--eap-rating-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'star_gap',
			array(
				'label'      => __( 'Star Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 20 ) ),
				'default'    => array( 'size' => 3, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-rating' => '--eap-rating-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'element_gap',
			array(
				'label'      => __( 'Element Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-rating' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'active_color',
			array(
				'label'     => __( 'Active Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f59e0b',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-rating' => '--eap-rating-active: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'inactive_color',
			array(
				'label'     => __( 'Inactive Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d1d5db',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-rating' => '--eap-rating-inactive: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_text_style() {
		$this->start_controls_section(
			'section_text_style',
			array(
				'label'     => __( 'Value & Count', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_value' => 'yes' ),
			)
		);

		$this->add_control(
			'value_color',
			array(
				'label'     => __( 'Value Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-rating__value' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'value_typography',
				'selector' => '{{WRAPPER}} .eap-post-rating__value',
			)
		);

		$this->add_control(
			'count_color',
			array(
				'label'     => __( 'Count Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9ca3af',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-rating__count' => 'color: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'count_typography',
				'selector' => '{{WRAPPER}} .eap-post-rating__count',
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$source   = ! empty( $settings['source'] ) ? $settings['source'] : 'manual';
		$max      = isset( $settings['max_rating'] ) && (int) $settings['max_rating'] > 0 ? (int) $settings['max_rating'] : 5;

		$value = 0.0;
		$count = 0;

		if ( 'manual' === $source ) {
			$value = isset( $settings['rating_value'] ) ? (float) $settings['rating_value'] : 0.0;
			$count = isset( $settings['manual_count'] ) ? (int) $settings['manual_count'] : 0;
		} elseif ( 'field' === $source ) {
			$post_id = $this->eap_get_post_id();
			$key     = ! empty( $settings['field_key'] ) ? $settings['field_key'] : '';
			if ( $post_id && '' !== $key ) {
				$value = (float) get_post_meta( $post_id, $key, true );
			}
		} else {
			$post_id = $this->eap_get_post_id();
			if ( $post_id && class_exists( 'EAP_Rating' ) ) {
				$stats = EAP_Rating::get_stats( $post_id );
				$value = (float) $stats['average'];
				$count = (int) $stats['count'];
			}
		}

		$value   = max( 0.0, min( (float) $max, $value ) );
		$percent = $max > 0 ? ( $value / $max ) * 100 : 0;

		$show_value = 'yes' === ( $settings['show_value'] ?? 'yes' );
		$show_count = 'yes' === ( $settings['show_count'] ?? '' );
		$position   = ! empty( $settings['value_position'] ) ? $settings['value_position'] : 'after';

		$formatted = ( floor( $value ) === $value ) ? number_format_i18n( $value ) : number_format_i18n( $value, 1 );

		$icon_html = $this->get_icon_html( $settings );
		$row       = $this->build_icon_row( $icon_html, $max );

		$this->add_render_attribute( 'wrapper', 'class', array( 'eap-widget', 'eap-post-rating', 'eap-post-rating--' . $source ) );

		$value_html = $show_value ? '<span class="eap-post-rating__value">' . esc_html( $formatted ) . '</span>' : '';
		$count_html = ( $show_count ) ? '<span class="eap-post-rating__count">' . esc_html( sprintf( '(%s)', number_format_i18n( $count ) ) ) . '</span>' : '';

		/* translators: 1: rating value, 2: maximum. */
		$aria = sprintf( __( 'Rating: %1$s out of %2$s', 'elementor-animatepro' ), $formatted, number_format_i18n( $max ) );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php
			if ( $show_value && 'before' === $position ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.
				echo $value_html;
			}
			?>
			<div class="eap-post-rating__stars" role="img" aria-label="<?php echo esc_attr( $aria ); ?>" style="--eap-rating: <?php echo esc_attr( sprintf( '%.2f', $percent ) ); ?>%;">
				<div class="eap-post-rating__track">
					<?php echo $row; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from icon helpers. ?>
				</div>
				<div class="eap-post-rating__fill" aria-hidden="true">
					<?php echo $row; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from icon helpers. ?>
				</div>
			</div>
			<?php
			if ( $show_value && 'after' === $position ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.
				echo $value_html;
			}
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.
			echo $count_html;
			?>
		</div>
		<?php
	}

	/**
	 * Resolve the icon markup for one star.
	 *
	 * @param array $settings Settings.
	 * @return string
	 */
	protected function get_icon_html( $settings ) {
		$type = ! empty( $settings['icon_type'] ) ? $settings['icon_type'] : 'star';

		if ( 'custom' === $type && ! empty( $settings['custom_icon']['value'] ) ) {
			ob_start();
			Icons_Manager::render_icon( $settings['custom_icon'], array( 'aria-hidden' => 'true' ) );
			$html = ob_get_clean();
			if ( $html ) {
				return $html;
			}
		}

		return $this->builtin_icon_svg( 'heart' === $type ? 'heart' : 'star' );
	}

	/**
	 * Repeat the icon $count times, each wrapped for sizing.
	 *
	 * @param string $icon_html Single icon markup.
	 * @param int    $count     Number of icons.
	 * @return string
	 */
	protected function build_icon_row( $icon_html, $count ) {
		$out = '';
		for ( $i = 0; $i < $count; $i++ ) {
			$out .= '<span class="eap-post-rating__icon">' . $icon_html . '</span>';
		}
		return $out;
	}

	/**
	 * Built-in star / heart SVG (uses currentColor).
	 *
	 * @param string $name star|heart.
	 * @return string
	 */
	protected function builtin_icon_svg( $name ) {
		if ( 'heart' === $name ) {
			return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';
		}

		return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 .587l3.668 7.431 8.2 1.193-5.934 5.787 1.401 8.168L12 18.896l-7.335 3.857 1.401-8.168L.132 9.211l8.2-1.193z"/></svg>';
	}
}
