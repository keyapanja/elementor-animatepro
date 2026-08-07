<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;

/**
 * Post Meta Info — a configurable row (or stack) of CURRENT-post meta: author
 * (with avatar), published / modified date, categories, tags, comment count and
 * reading time. Each item is a repeater row with an optional prefix label and
 * icon override; items are joined by a chosen separator. Links point to the
 * author / term archives when enabled. Intended for single / archive templates.
 */
class EAP_Widget_Post_Meta_Info extends EAP_Widget_Base {

	public function get_name() {
		return 'eap-post-meta-info';
	}

	public function get_title() {
		return __( 'Post Meta Info', 'elementor-animatepro' );
	}

	public function get_icon() {
		return 'eicon-post-info';
	}

	public function get_keywords() {
		return array( 'post', 'meta', 'author', 'date', 'category', 'tag', 'comments', 'reading', 'dynamic' );
	}

	public function get_style_depends() {
		return array( 'eap-core', 'eap-post-meta-info', 'elementor-icons-fa-solid' );
	}

	/**
	 * Meta type → default Font Awesome icon.
	 *
	 * @return array<string, string>
	 */
	protected function meta_icons() {
		return array(
			'author'       => 'fas fa-user',
			'date'         => 'fas fa-calendar',
			'modified'     => 'fas fa-calendar-check',
			'categories'   => 'fas fa-folder',
			'tags'         => 'fas fa-tags',
			'comments'     => 'fas fa-comment',
			'reading_time' => 'fas fa-clock',
		);
	}

	protected function register_controls() {
		$this->register_items_section();
		$this->register_general_style();
		$this->register_icon_style();
		$this->register_separator_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	protected function register_items_section() {
		$this->start_controls_section(
			'section_items',
			array(
				'label' => __( 'Meta Items', 'elementor-animatepro' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'meta_type',
			array(
				'label'   => __( 'Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'author',
				'options' => array(
					'author'       => __( 'Author', 'elementor-animatepro' ),
					'date'         => __( 'Published Date', 'elementor-animatepro' ),
					'modified'     => __( 'Modified Date', 'elementor-animatepro' ),
					'categories'   => __( 'Categories', 'elementor-animatepro' ),
					'tags'         => __( 'Tags', 'elementor-animatepro' ),
					'comments'     => __( 'Comment Count', 'elementor-animatepro' ),
					'reading_time' => __( 'Reading Time', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'before_text',
			array(
				'label'       => __( 'Prefix', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'e.g. By, Posted on', 'elementor-animatepro' ),
			)
		);

		$repeater->add_control(
			'custom_icon',
			array(
				'label'       => __( 'Icon', 'elementor-animatepro' ),
				'type'        => Controls_Manager::ICONS,
				'description' => __( 'Optional — overrides the default icon for this item.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'meta_items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ meta_type }}}',
				'default'     => array(
					array( 'meta_type' => 'author', 'before_text' => __( 'By', 'elementor-animatepro' ) ),
					array( 'meta_type' => 'date' ),
					array( 'meta_type' => 'comments' ),
				),
			)
		);

		$this->add_control(
			'show_icons',
			array(
				'label'        => __( 'Show Icons', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'show_avatar',
			array(
				'label'        => __( 'Author Avatar', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-animatepro' ),
				'label_off'    => __( 'Hide', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'enable_links',
			array(
				'label'        => __( 'Enable Links', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Link the author and terms to their archives.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'date_format',
			array(
				'label'       => __( 'Date Format', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => get_option( 'date_format' ),
				'description' => __( 'PHP date format. Leave empty for the site default.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'separator_type',
			array(
				'label'   => __( 'Separator', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'dot',
				'options' => array(
					'none'   => __( 'None', 'elementor-animatepro' ),
					'dot'    => __( 'Dot', 'elementor-animatepro' ),
					'slash'  => __( 'Slash', 'elementor-animatepro' ),
					'dash'   => __( 'Dash', 'elementor-animatepro' ),
					'pipe'   => __( 'Pipe', 'elementor-animatepro' ),
					'custom' => __( 'Custom', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'separator_custom',
			array(
				'label'     => __( 'Custom Separator', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '·',
				'condition' => array( 'separator_type' => 'custom' ),
			)
		);

		$this->add_responsive_control(
			'layout',
			array(
				'label'                => __( 'Layout', 'elementor-animatepro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'row',
				'options'              => array(
					'row'    => array(
						'title' => __( 'Inline', 'elementor-animatepro' ),
						'icon'  => 'eicon-ellipsis-h',
					),
					'column' => array(
						'title' => __( 'Stacked', 'elementor-animatepro' ),
						'icon'  => 'eicon-ellipsis-v',
					),
				),
				'selectors'            => array(
					'{{WRAPPER}} .eap-post-meta' => 'flex-direction: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'                => __( 'Alignment', 'elementor-animatepro' ),
				'type'                 => Controls_Manager::CHOOSE,
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
					'{{WRAPPER}} .eap-post-meta' => 'justify-content: {{VALUE}}; align-items: {{VALUE}};',
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

	protected function register_general_style() {
		$this->start_controls_section(
			'section_general_style',
			array(
				'label' => __( 'Text', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array(
					'{{WRAPPER}} .eap-post-meta' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => __( 'Link Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-post-meta__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_color_hover',
			array(
				'label'     => __( 'Link Hover Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-post-meta__link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .eap-post-meta',
			)
		);

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => __( 'Item Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-meta' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_icon_style() {
		$this->start_controls_section(
			'section_icon_style',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_icons' => 'yes' ),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-post-meta__icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 40 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-meta__icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eap-post-meta__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_gap',
			array(
				'label'      => __( 'Icon Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 24 ) ),
				'default'    => array( 'size' => 6, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-meta__item' => '--eap-meta-icon-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_size',
			array(
				'label'      => __( 'Avatar Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 16, 'max' => 64 ) ),
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-post-meta__avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'show_avatar' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_separator_style() {
		$this->start_controls_section(
			'section_separator_style',
			array(
				'label'     => __( 'Separator', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'separator_type!' => 'none' ),
			)
		);

		$this->add_control(
			'separator_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-post-meta__sep' => 'color: {{VALUE}};',
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
		$items    = ! empty( $settings['meta_items'] ) && is_array( $settings['meta_items'] ) ? $settings['meta_items'] : array();

		if ( empty( $items ) ) {
			return;
		}

		$post_id = $this->eap_get_post_id();
		if ( ! $post_id ) {
			return;
		}

		$show_icons  = 'yes' === ( $settings['show_icons'] ?? 'yes' );
		$show_avatar = 'yes' === ( $settings['show_avatar'] ?? 'yes' );
		$links       = 'yes' === ( $settings['enable_links'] ?? 'yes' );
		$date_format = ! empty( $settings['date_format'] ) ? $settings['date_format'] : get_option( 'date_format' );
		$icons       = $this->meta_icons();

		$pieces = array();

		foreach ( $items as $item ) {
			$type  = ! empty( $item['meta_type'] ) ? $item['meta_type'] : 'author';
			$value = $this->get_meta_value( $type, $post_id, $links, $date_format );

			if ( '' === $value ) {
				continue;
			}

			// Icon / avatar slot.
			$icon_html = '';
			if ( 'author' === $type && $show_avatar ) {
				$author_id = (int) get_post_field( 'post_author', $post_id );
				$icon_html = get_avatar( $author_id, 48, '', '', array( 'class' => 'eap-post-meta__avatar' ) );
			} elseif ( $show_icons ) {
				if ( ! empty( $item['custom_icon']['value'] ) ) {
					ob_start();
					Icons_Manager::render_icon( $item['custom_icon'], array( 'aria-hidden' => 'true' ) );
					$icon_html = ob_get_clean();
				} elseif ( isset( $icons[ $type ] ) ) {
					$icon_html = '<i class="' . esc_attr( $icons[ $type ] ) . '" aria-hidden="true"></i>';
				}
			}

			$before = ! empty( $item['before_text'] ) ? '<span class="eap-post-meta__prefix">' . esc_html( $item['before_text'] ) . '</span> ' : '';

			$piece  = '<span class="eap-post-meta__item eap-post-meta__item--' . esc_attr( $type ) . '">';
			if ( '' !== $icon_html ) {
				$piece .= '<span class="eap-post-meta__icon">' . $icon_html . '</span>';
			}
			$piece .= '<span class="eap-post-meta__text">' . $before . $value . '</span>';
			$piece .= '</span>';

			$pieces[] = $piece;
		}

		if ( empty( $pieces ) ) {
			return;
		}

		$separator = $this->get_separator( $settings );

		$this->add_render_attribute( 'wrapper', 'class', array( 'eap-widget', 'eap-post-meta' ) );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php
			$last = count( $pieces ) - 1;
			foreach ( $pieces as $i => $piece ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped helpers above.
				echo $piece;
				if ( '' !== $separator && $i < $last ) {
					echo '<span class="eap-post-meta__sep" aria-hidden="true">' . esc_html( $separator ) . '</span>';
				}
			}
			?>
		</div>
		<?php
	}

	/**
	 * Build the inner value HTML for one meta type.
	 *
	 * @param string $type        Meta type.
	 * @param int    $post_id     Post ID.
	 * @param bool   $links       Whether to link author / terms.
	 * @param string $date_format Date format.
	 * @return string HTML (already escaped), or '' when empty.
	 */
	protected function get_meta_value( $type, $post_id, $links, $date_format ) {
		switch ( $type ) {
			case 'author':
				$author_id = (int) get_post_field( 'post_author', $post_id );
				$name      = get_the_author_meta( 'display_name', $author_id );
				if ( '' === $name ) {
					return '';
				}
				if ( $links ) {
					return '<a class="eap-post-meta__link" href="' . esc_url( get_author_posts_url( $author_id ) ) . '">' . esc_html( $name ) . '</a>';
				}
				return esc_html( $name );

			case 'date':
				return esc_html( get_the_date( $date_format, $post_id ) );

			case 'modified':
				return esc_html( get_the_modified_date( $date_format, $post_id ) );

			case 'categories':
				return $this->term_list( $post_id, 'category', $links );

			case 'tags':
				return $this->term_list( $post_id, 'post_tag', $links );

			case 'comments':
				$count = (int) get_comments_number( $post_id );
				/* translators: %s: number of comments. */
				return esc_html( sprintf( _n( '%s Comment', '%s Comments', $count, 'elementor-animatepro' ), number_format_i18n( $count ) ) );

			case 'reading_time':
				$content = get_post_field( 'post_content', $post_id );
				$words   = str_word_count( wp_strip_all_tags( strip_shortcodes( (string) $content ) ) );
				$minutes = max( 1, (int) ceil( $words / 200 ) );
				/* translators: %d: reading time in minutes. */
				return esc_html( sprintf( _n( '%d min read', '%d min read', $minutes, 'elementor-animatepro' ), $minutes ) );
		}

		return '';
	}

	/**
	 * Comma-joined term list for a taxonomy.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy.
	 * @param bool   $links    Whether to link terms.
	 * @return string
	 */
	protected function term_list( $post_id, $taxonomy, $links ) {
		$terms = get_the_terms( $post_id, $taxonomy );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}

		$out = array();
		foreach ( $terms as $term ) {
			if ( $links ) {
				$url = get_term_link( $term );
				if ( ! is_wp_error( $url ) ) {
					$out[] = '<a class="eap-post-meta__link" href="' . esc_url( $url ) . '">' . esc_html( $term->name ) . '</a>';
					continue;
				}
			}
			$out[] = esc_html( $term->name );
		}

		return implode( '<span class="eap-post-meta__term-sep">, </span>', $out );
	}

	/**
	 * Resolve the separator character.
	 *
	 * @param array $settings Settings.
	 * @return string
	 */
	protected function get_separator( $settings ) {
		$type = ! empty( $settings['separator_type'] ) ? $settings['separator_type'] : 'dot';

		switch ( $type ) {
			case 'none':
				return '';
			case 'slash':
				return '/';
			case 'dash':
				return '—';
			case 'pipe':
				return '|';
			case 'custom':
				return isset( $settings['separator_custom'] ) ? $settings['separator_custom'] : '·';
			case 'dot':
			default:
				return '·';
		}
	}
}
