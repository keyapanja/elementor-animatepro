<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/**
 * Archive Title widget.
 *
 * The heading for whatever archive is being viewed — category, tag, taxonomy,
 * author, date, post-type archive, search results, the blog page or a 404.
 *
 * It does NOT reimplement WordPress's archive-title logic. `get_the_archive_title()`
 * computes the title and its prefix SEPARATELY and hands both to the
 * `get_the_archive_title` filter as `$original_title` and `$prefix` (see
 * wp-includes/general-template.php), so this widget captures them through that
 * filter rather than string-parsing the composed output. That is what lets the
 * prefix and the title be styled independently, which is the main reason to use
 * this instead of a plain heading.
 *
 * It also covers the cases core's function does not: search results, 404 and the
 * blog page all fall through to a bare "Archives" there.
 *
 * CSS only — no JavaScript.
 */
class EAP_Widget_Archive_Title extends EAP_Widget_Base {

	/**
	 * Captured title/prefix from the WordPress filter.
	 *
	 * @var array<string, string>
	 */
	protected $captured = array(
		'title'  => '',
		'prefix' => '',
	);

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-archive-title';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Archive Title', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-archive-title';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'archive', 'title', 'heading', 'category', 'tag', 'author', 'search', 'taxonomy' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-archive-title' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_content_section();
		$this->register_title_style();
		$this->register_prefix_style();
		$this->register_count_style();
		$this->register_description_style();
	}

	/* =====================================================================
	 * CONTENT
	 * ================================================================== */

	/**
	 * Content section.
	 *
	 * @return void
	 */
	protected function register_content_section() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Archive Title', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'tag',
			array(
				'label'   => __( 'HTML Tag', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h1',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
			)
		);

		$this->add_control(
			'prefix_mode',
			array(
				'label'       => __( 'Prefix', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'hide',
				'options'     => array(
					'default' => __( 'WordPress Default', 'elementor-animatepro' ),
					'hide'    => __( 'Hide', 'elementor-animatepro' ),
					'custom'  => __( 'Custom', 'elementor-animatepro' ),
				),
				'description' => __( 'The prefix WordPress adds — "Category:", "Tag:", "Author:" and so on.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'custom_prefix',
			array(
				'label'       => __( 'Custom Prefix', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Browsing', 'elementor-animatepro' ),
				'placeholder' => __( 'Browsing', 'elementor-animatepro' ),
				'condition'   => array( 'prefix_mode' => 'custom' ),
			)
		);

		$this->add_control(
			'search_format',
			array(
				'label'       => __( 'Search Results Format', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Search results for: %s', 'elementor-animatepro' ),
				'description' => __( '%s is replaced with the search term. Used on the search results page, which WordPress\'s own archive title does not cover.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'not_found_text',
			array(
				'label'   => __( '404 Text', 'elementor-animatepro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Page not found', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'fallback_title',
			array(
				'label'       => __( 'Fallback Title', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Archives', 'elementor-animatepro' ),
				'description' => __( 'Shown if nothing else resolves — for example if this widget is placed outside an archive.', 'elementor-animatepro' ),
			)
		);

		$this->add_responsive_control(
			'align',
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
				/*
				 * Written to the WRAPPER, not the heading, so the description
				 * follows the alignment too — and paired with the inline margins
				 * a max-width description needs to actually sit under the title
				 * rather than staying flush left.
				 */
				'selectors_dictionary' => array(
					'left'   => 'text-align: left; --eap-at-desc-mi: 0 auto;',
					'center' => 'text-align: center; --eap-at-desc-mi: auto auto;',
					'right'  => 'text-align: right; --eap-at-desc-mi: auto 0;',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-archive-title-wrap' => '{{VALUE}}',
				),
				'separator' => 'before',
			)
		);

		/* ---------- Count ---------- */

		$this->add_control(
			'show_count',
			array(
				'label'        => __( 'Show Post Count', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'count_format',
			array(
				'label'       => __( 'Count Format', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( '%s posts', 'elementor-animatepro' ),
				'description' => __( '%s is replaced with the number of results.', 'elementor-animatepro' ),
				'condition'   => array( 'show_count' => 'yes' ),
			)
		);

		/* ---------- Description ---------- */

		$this->add_control(
			'show_description',
			array(
				'label'        => __( 'Show Description', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'The term description, author bio or post-type description for this archive.', 'elementor-animatepro' ),
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'description_length',
			array(
				'label'       => __( 'Trim To Words', 'elementor-animatepro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 200,
				'description' => __( '0 shows the whole description.', 'elementor-animatepro' ),
				'condition'   => array( 'show_description' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

	/**
	 * Title style.
	 *
	 * @return void
	 */
	protected function register_title_style() {
		$this->start_controls_section(
			'style_title',
			array(
				'label' => __( 'Title', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eap-archive-title__text',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-archive-title__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Prefix style.
	 *
	 * @return void
	 */
	protected function register_prefix_style() {
		$this->start_controls_section(
			'style_prefix',
			array(
				'label'      => __( 'Prefix', 'elementor-animatepro' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'prefix_mode',
							'operator' => '===',
							'value'    => 'default',
						),
						array(
							'name'     => 'prefix_mode',
							'operator' => '===',
							'value'    => 'custom',
						),
					),
				),
			)
		);

		$this->add_control(
			'prefix_display',
			array(
				'label'     => __( 'Display', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'inline',
				'options'   => array(
					'inline' => __( 'Same Line', 'elementor-animatepro' ),
					'block'  => __( 'Own Line', 'elementor-animatepro' ),
				),
				/*
				 * The spacing switches with the display, so both are set here
				 * rather than in a stylesheet rule keyed off the display value —
				 * control CSS lands in a generated stylesheet, so an attribute
				 * selector on an inline style would never match.
				 */
				'selectors_dictionary' => array(
					'inline' => 'display: inline; margin-inline-end: var(--eap-at-gap); margin-block-end: 0;',
					'block'  => 'display: block; margin-inline-end: 0; margin-block-end: calc(var(--eap-at-gap) / 2);',
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-archive-title__prefix' => '{{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'prefix_typography',
				'selector' => '{{WRAPPER}} .eap-archive-title__prefix',
			)
		);

		$this->add_control(
			'prefix_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-archive-title__prefix' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'prefix_gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}' => '--eap-at-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Count style.
	 *
	 * @return void
	 */
	protected function register_count_style() {
		$this->start_controls_section(
			'style_count',
			array(
				'label'     => __( 'Post Count', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_count' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'count_typography',
				'selector' => '{{WRAPPER}} .eap-archive-title__count',
			)
		);

		$this->add_control(
			'count_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-archive-title__count' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'count_bg',
			array(
				'label'     => __( 'Background', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-archive-title__count' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Description style.
	 *
	 * @return void
	 */
	protected function register_description_style() {
		$this->start_controls_section(
			'style_description',
			array(
				'label'     => __( 'Description', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_description' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .eap-archive-title__description',
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-archive-title__description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'description_max_width',
			array(
				'label'      => __( 'Max Width', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 200, 'max' => 1200 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .eap-archive-title__description' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'description_spacing',
			array(
				'label'      => __( 'Spacing', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eap-archive-title__description' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ================================================================== */

	/**
	 * Capture the title and prefix WordPress computed.
	 *
	 * Registered on `get_the_archive_title` purely to read its arguments —
	 * `$title` is returned untouched so other plugins' filters are unaffected.
	 *
	 * @param string $title          Composed title.
	 * @param string $original_title Title without the prefix.
	 * @param string $prefix         Prefix.
	 * @return string
	 */
	public function capture_archive_title( $title, $original_title = '', $prefix = '' ) {
		$this->captured = array(
			'title'  => (string) $original_title,
			'prefix' => (string) $prefix,
		);
		return $title;
	}

	/**
	 * Render.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$parts = $this->resolve( $settings );

		if ( '' === $parts['title'] ) {
			return;
		}

		$tag = $settings['tag'] ?? 'h1';
		if ( ! in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ), true ) ) {
			$tag = 'h1';
		}

		$this->add_render_attribute( 'wrapper', 'class', array( 'eap-widget', 'eap-archive-title-wrap' ) );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<<?php echo esc_html( $tag ); ?> class="eap-archive-title">
				<?php if ( '' !== $parts['prefix'] ) : ?>
					<span class="eap-archive-title__prefix"><?php echo esc_html( $parts['prefix'] ); ?></span>
				<?php endif; ?>
				<span class="eap-archive-title__text"><?php echo esc_html( $parts['title'] ); ?></span>
				<?php if ( '' !== $parts['count'] ) : ?>
					<span class="eap-archive-title__count"><?php echo esc_html( $parts['count'] ); ?></span>
				<?php endif; ?>
			</<?php echo esc_html( $tag ); ?>>

			<?php if ( '' !== $parts['description'] ) : ?>
				<div class="eap-archive-title__description"><?php echo wp_kses_post( $parts['description'] ); ?></div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Resolve the title, prefix, count and description for this request.
	 *
	 * @param array $settings Settings.
	 * @return array{title:string,prefix:string,count:string,description:string}
	 */
	protected function resolve( $settings ) {
		$out = array(
			'title'       => '',
			'prefix'      => '',
			'count'       => '',
			'description' => '',
		);

		$editor = $this->eap_is_editor();

		if ( is_search() ) {
			$format      = $settings['search_format'] ?? '';
			$format      = '' !== trim( (string) $format ) ? $format : __( 'Search results for: %s', 'elementor-animatepro' );
			$out['title'] = sprintf( $format, get_search_query() );
		} elseif ( is_404() ) {
			$out['title'] = (string) ( $settings['not_found_text'] ?? __( 'Page not found', 'elementor-animatepro' ) );
		} elseif ( is_home() && ! is_front_page() ) {
			// The posts page: core's archive title returns a bare "Archives" here.
			$page_id      = (int) get_option( 'page_for_posts' );
			$out['title'] = $page_id ? get_the_title( $page_id ) : __( 'Blog', 'elementor-animatepro' );
		} elseif ( is_archive() ) {
			add_filter( 'get_the_archive_title', array( $this, 'capture_archive_title' ), 999, 3 );
			get_the_archive_title();
			remove_filter( 'get_the_archive_title', array( $this, 'capture_archive_title' ), 999 );

			$out['title']  = $this->captured['title'];
			$out['prefix'] = $this->captured['prefix'];
		}

		// No archive context — most often the editor canvas. Preview against a
		// real term where possible so the widget is designable, rather than
		// rendering an empty heading (the same reasoning as eap_get_post_id()).
		if ( '' === $out['title'] && $editor ) {
			$terms = get_terms(
				array(
					'taxonomy'   => 'category',
					'hide_empty' => false,
					'number'     => 1,
				)
			);

			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				$out['title']       = $terms[0]->name;
				$out['prefix']      = _x( 'Category:', 'category archive title prefix' );
				$out['description'] = (string) $terms[0]->description;
			} else {
				$out['title'] = (string) ( $settings['fallback_title'] ?? __( 'Archives', 'elementor-animatepro' ) );
			}
		}

		if ( '' === $out['title'] ) {
			$out['title'] = (string) ( $settings['fallback_title'] ?? '' );
		}

		$out['prefix'] = $this->resolve_prefix( $settings, $out['prefix'] );

		if ( 'yes' === ( $settings['show_count'] ?? '' ) ) {
			$found = isset( $GLOBALS['wp_query'] ) ? (int) $GLOBALS['wp_query']->found_posts : 0;
			if ( $editor && ! $found ) {
				$found = 12;
			}
			$format       = $settings['count_format'] ?? '%s posts';
			$out['count'] = sprintf( $format, number_format_i18n( $found ) );
		}

		if ( 'yes' === ( $settings['show_description'] ?? '' ) ) {
			$description = '' !== $out['description'] ? $out['description'] : (string) get_the_archive_description();
			$description = trim( $description );

			$words = isset( $settings['description_length'] ) ? (int) $settings['description_length'] : 0;
			if ( $words > 0 && '' !== $description ) {
				$description = wp_trim_words( wp_strip_all_tags( $description ), $words, '…' );
			}

			$out['description'] = $description;
		} else {
			$out['description'] = '';
		}

		return $out;
	}

	/**
	 * Apply the chosen prefix mode.
	 *
	 * @param array  $settings Settings.
	 * @param string $prefix   Prefix WordPress computed.
	 * @return string
	 */
	protected function resolve_prefix( $settings, $prefix ) {
		$mode = $settings['prefix_mode'] ?? 'hide';

		if ( 'hide' === $mode ) {
			return '';
		}

		if ( 'custom' === $mode ) {
			return trim( (string) ( $settings['custom_prefix'] ?? '' ) );
		}

		// WordPress ships its prefixes with the colon already in them
		// ("Category:"), so nothing is appended here.
		return trim( (string) $prefix );
	}
}
