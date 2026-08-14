<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

/**
 * Post Rating Form — an interactive star rating input for the CURRENT post.
 * Visitors click a star (1..max) to submit a rating without logging in; the vote
 * is saved to the shared EAP_Rating store via a nonce-protected AJAX endpoint and
 * the running average updates live. One vote per visitor (localStorage + a
 * per-IP-per-post server guard); after voting the stars lock and a thank-you +
 * average are shown. Pairs with the Post Rating (display) widget's Visitor
 * Average source. CSS + post-rating-form.js.
 */
class EAP_Widget_Post_Rating_Form extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-post-rating-form';
	}

	public function get_title() {
		return __( 'Post Rating Form', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-rating';
	}

	public function get_keywords() {
		return array( 'post', 'rating', 'form', 'stars', 'vote', 'review', 'dynamic' );
	}

	public function get_style_depends() {
		return array( 'eap-core', 'eap-post-rating-form', 'elementor-icons-fa-solid' );
	}

	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-post-rating-form-script' );
	}

	protected function register_controls() {
		$this->register_form_section();
		$this->register_stars_style();
		$this->register_text_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_form_section() {
		$this->start_controls_section(
			'section_form',
			array(
				'label' => __( 'Rating Form', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'max_rating',
			array(
				'label'   => __( 'Stars', 'elementor-animatepro' ),
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
			'show_prompt',
			array(
				'label'        => __( 'Prompt', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'prompt_text',
			array(
				'label'     => __( 'Prompt Text', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Rate this post', 'elementor-animatepro' ),
				'condition' => array( 'show_prompt' => 'yes' ),
			)
		);

		$this->add_control(
			'thanks_text',
			array(
				'label'   => __( 'Thank-You Text', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Thanks for rating!', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'show_average',
			array(
				'label'        => __( 'Show Average', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
					'{{WRAPPER}} .eap-post-rating-form' => 'align-items: {{VALUE}}; text-align: {{VALUE}};',
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
				'range'      => array( 'px' => array( 'min' => 14, 'max' => 72 ) ),
				'default'    => array( 'size' => 30, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-rating-form' => '--eap-rf-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'star_gap',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 24 ) ),
				'default'    => array( 'size' => 4, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-rating-form__stars' => 'gap: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .eap-post-rating-form' => '--eap-rf-inactive: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hover_color',
			array(
				'label'     => __( 'Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fbbf24',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-rating-form' => '--eap-rf-hover: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'active_color',
			array(
				'label'     => __( 'Selected Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f59e0b',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-rating-form' => '--eap-rf-active: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_text_style() {
		$this->start_controls_section(
			'section_text_style',
			array(
				'label' => __( 'Text', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'prompt_color',
			array(
				'label'     => __( 'Prompt Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-rating-form__prompt' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'prompt_typography',
				'selector' => '{{WRAPPER}} .eap-post-rating-form__prompt',
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => __( 'Average / Thanks Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-rating-form__meta' => 'color: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'meta_typography',
				'selector' => '{{WRAPPER}} .eap-post-rating-form__meta',
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
		$max      = isset( $settings['max_rating'] ) && (int) $settings['max_rating'] > 0 ? min( 10, (int) $settings['max_rating'] ) : 5;

		$stats   = ( $post_id && class_exists( 'EAP_Rating' ) ) ? EAP_Rating::get_stats( $post_id ) : array( 'average' => 0.0, 'count' => 0 );
		$average = (float) $stats['average'];
		$count   = (int) $stats['count'];

		$show_prompt  = 'yes' === ( $settings['show_prompt'] ?? 'yes' );
		$show_average = 'yes' === ( $settings['show_average'] ?? 'yes' );
		$prompt       = ! empty( $settings['prompt_text'] ) ? $settings['prompt_text'] : __( 'Rate this post', 'elementor-animatepro' );
		$thanks       = ! empty( $settings['thanks_text'] ) ? $settings['thanks_text'] : __( 'Thanks for rating!', 'elementor-animatepro' );

		$icon_html = $this->get_icon_html( $settings );
		$formatted = ( floor( $average ) === $average ) ? number_format_i18n( $average ) : number_format_i18n( $average, 1 );

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'                    => array( 'eap-widget', 'eap-post-rating-form' ),
				'data-eap-rating-form'     => '',
				'data-post'                => (string) $post_id,
				'data-max'                 => (string) $max,
				'data-nonce'               => wp_create_nonce( 'eap-rating' ),
				'data-ajax-url'            => esc_url( admin_url( 'admin-ajax.php' ) ),
				'data-thanks'              => $thanks,
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $show_prompt ) : ?>
				<div class="eap-post-rating-form__prompt"><?php echo esc_html( $prompt ); ?></div>
			<?php endif; ?>

			<div class="eap-post-rating-form__stars" role="radiogroup" aria-label="<?php echo esc_attr( $prompt ); ?>">
				<?php for ( $i = 1; $i <= $max; $i++ ) : ?>
					<button
						type="button"
						class="eap-post-rating-form__star"
						data-value="<?php echo esc_attr( (string) $i ); ?>"
						role="radio"
						aria-checked="false"
						aria-label="<?php
						/* translators: %d: star number. */
						echo esc_attr( sprintf( _n( '%d star', '%d stars', $i, 'elementor-animatepro' ), $i ) );
						?>"
					>
						<span class="eap-post-rating-form__icon"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inline SVG / Icons_Manager output. ?></span>
					</button>
				<?php endfor; ?>
			</div>

			<div class="eap-post-rating-form__meta">
				<span class="eap-post-rating-form__thanks" hidden><?php echo esc_html( $thanks ); ?></span>
				<?php if ( $show_average ) : ?>
					<span class="eap-post-rating-form__stats"<?php echo $count > 0 ? '' : ' hidden'; ?>>
						<span class="eap-post-rating-form__average"><?php echo esc_html( $formatted ); ?></span>
						<span class="eap-post-rating-form__count"><?php
							/* translators: %s: number of votes. */
							echo esc_html( sprintf( _n( '(%s vote)', '(%s votes)', $count, 'elementor-animatepro' ), number_format_i18n( $count ) ) );
						?></span>
					</span>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Resolve one star's icon markup.
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

		if ( 'heart' === $type ) {
			return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';
		}

		return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 .587l3.668 7.431 8.2 1.193-5.934 5.787 1.401 8.168L12 18.896l-7.335 3.857 1.401-8.168L.132 9.211l8.2-1.193z"/></svg>';
	}
}
