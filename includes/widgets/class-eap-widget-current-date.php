<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

/**
 * Current Date widget.
 *
 * Today's date and/or the current time, in the site's timezone or one you pick.
 *
 * WHY THIS HAS JAVASCRIPT AT ALL: a server-rendered "current date" is wrong the
 * moment the page is cached. On any site with full-page caching the visitor can
 * be shown yesterday's date — and a clock rendered once is stale a second later.
 * So PHP renders the value (correct for no-JS, and it is what search engines and
 * the `datetime` attribute see) and current-date.js re-renders it on load and
 * then ticks, at an interval derived from the format: per second only when the
 * format actually shows seconds.
 *
 * The JS formats with the SAME PHP format string rather than a parallel set of
 * options, so the two can't drift. Timezone-correct parts come from
 * Intl.DateTimeFormat rather than date arithmetic.
 */
class EAP_Widget_Current_Date extends EAP_Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eap-current-date';
	}

	/**
	 * Widget label.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Current Date', 'elementor-animatepro' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-calendar';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'date', 'time', 'clock', 'today', 'now', 'timezone', 'year' );
	}

	/**
	 * Styles.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'eap-core', 'eap-current-date', 'elementor-icons-fa-solid' );
	}

	/**
	 * Scripts.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return array( 'eap-core-runtime', 'eap-current-date-script' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_content_section();
		$this->register_text_style();
		$this->register_icon_style();
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
			array( 'label' => __( 'Current Date', 'elementor-animatepro' ) )
		);

		$this->add_control(
			'display',
			array(
				'label'   => __( 'Show', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'     => __( 'Date', 'elementor-animatepro' ),
					'time'     => __( 'Time', 'elementor-animatepro' ),
					'datetime' => __( 'Date & Time', 'elementor-animatepro' ),
					'custom'   => __( 'Custom Format', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'date_format',
			array(
				'label'     => __( 'Date Format', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'site',
				'options'   => $this->get_date_format_options(),
				'condition' => array( 'display' => array( 'date', 'datetime' ) ),
			)
		);

		$this->add_control(
			'time_format',
			array(
				'label'     => __( 'Time Format', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'site',
				'options'   => $this->get_time_format_options(),
				'condition' => array( 'display' => array( 'time', 'datetime' ) ),
			)
		);

		$this->add_control(
			'separator_text',
			array(
				'label'     => __( 'Separator', 'elementor-animatepro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => ' — ',
				'condition' => array( 'display' => 'datetime' ),
			)
		);

		$this->add_control(
			'custom_format',
			array(
				'label'       => __( 'Custom Format', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'l, F j, Y',
				'placeholder' => 'l, F j, Y',
				'description' => sprintf(
					/* translators: %s: link to the PHP date format documentation. */
					__( 'Uses %s. Escape a literal letter with a backslash.', 'elementor-animatepro' ),
					'<a href="https://www.php.net/manual/datetime.format.php" target="_blank" rel="noopener">' . esc_html__( 'PHP date format characters', 'elementor-animatepro' ) . '</a>'
				),
				'condition'   => array( 'display' => 'custom' ),
			)
		);

		$this->add_control(
			'timezone',
			array(
				'label'       => __( 'Timezone', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'default'     => 'site',
				'options'     => $this->get_timezone_options(),
				'label_block' => true,
				'description' => __( 'Pick a specific zone to show another office\'s local time.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'live_update',
			array(
				'label'        => __( 'Keep It Current', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Refreshes in the browser, so a cached page cannot show a stale date and a clock keeps ticking. Turn off for a value that should be fixed at page render.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'prefix_text',
			array(
				'label'       => __( 'Before Text', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Today is', 'elementor-animatepro' ),
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'suffix_text',
			array(
				'label' => __( 'After Text', 'elementor-animatepro' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'type'      => Controls_Manager::ICONS,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'tag',
			array(
				'label'     => __( 'HTML Tag', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'div',
				'options'   => array(
					'div'  => 'div',
					'p'    => 'p',
					'span' => 'span',
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Alignment', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'elementor-animatepro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .eap-current-date' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * STYLE
	 * ================================================================== */

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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'value_typography',
				'label'    => __( 'Value Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-current-date__value',
			)
		);

		$this->add_control(
			'value_color',
			array(
				'label'     => __( 'Value Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-current-date__value' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'affix_typography',
				'label'    => __( 'Before / After Typography', 'elementor-animatepro' ),
				'selector' => '{{WRAPPER}} .eap-current-date__prefix, {{WRAPPER}} .eap-current-date__suffix',
			)
		);

		$this->add_control(
			'affix_color',
			array(
				'label'     => __( 'Before / After Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-current-date__prefix, {{WRAPPER}} .eap-current-date__suffix' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}' => '--eap-cd-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Icon style.
	 *
	 * @return void
	 */
	protected function register_icon_style() {
		$this->start_controls_section(
			'style_icon',
			array(
				'label'     => __( 'Icon', 'elementor-animatepro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'icon[value]!' => '' ),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Size', 'elementor-animatepro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 8, 'max' => 80 ),
					'em' => array( 'min' => 0.5, 'max' => 4, 'step' => 0.1 ),
				),
				'default'    => array( 'size' => 1, 'unit' => 'em' ),
				'selectors'  => array(
					'{{WRAPPER}}' => '--eap-cd-icon: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Color', 'elementor-animatepro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eap-current-date__icon' => 'color: {{VALUE}};',
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
		$settings = $this->get_settings_for_display();

		$format = $this->resolve_format( $settings );
		if ( '' === $format ) {
			return;
		}

		$timezone = $this->resolve_timezone( $settings );

		// wp_date() (not date_i18n()) — it is the timezone-aware, localised one.
		$now     = time();
		$value   = wp_date( $format, $now, $timezone );
		$machine = wp_date( 'c', $now, $timezone );

		$tag = $settings['tag'] ?? 'div';
		if ( ! in_array( $tag, array( 'div', 'p', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ) {
			$tag = 'div';
		}

		$prefix = trim( (string) ( $settings['prefix_text'] ?? '' ) );
		$suffix = trim( (string) ( $settings['suffix_text'] ?? '' ) );

		$live = 'yes' === ( $settings['live_update'] ?? 'yes' );

		$this->add_render_attribute( 'wrapper', 'class', array( 'eap-widget', 'eap-current-date-wrap' ) );

		$this->add_render_attribute( 'value', 'class', 'eap-current-date__value' );
		$this->add_render_attribute( 'value', 'datetime', $machine );

		if ( $live ) {
			$this->add_render_attribute(
				'value',
				'data-eap-current-date',
				wp_json_encode(
					array(
						'format'   => $format,
						'timeZone' => $timezone->getName(),
						'locale'   => $this->get_bcp47_locale(),
						'interval' => $this->resolve_interval( $format ),
					)
				)
			);
		}
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<<?php echo esc_html( $tag ); ?> class="eap-current-date">
				<?php
				if ( ! empty( $settings['icon']['value'] ) ) {
					echo '<span class="eap-current-date__icon">';
					Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) );
					echo '</span>';
				}
				?>
				<?php if ( '' !== $prefix ) : ?>
					<span class="eap-current-date__prefix"><?php echo esc_html( $prefix ); ?></span>
				<?php endif; ?>
				<time <?php $this->print_render_attribute_string( 'value' ); ?>><?php echo esc_html( $value ); ?></time>
				<?php if ( '' !== $suffix ) : ?>
					<span class="eap-current-date__suffix"><?php echo esc_html( $suffix ); ?></span>
				<?php endif; ?>
			</<?php echo esc_html( $tag ); ?>>
		</div>
		<?php
	}

	/**
	 * Build the PHP date format string for the chosen mode.
	 *
	 * @param array $settings Settings.
	 * @return string
	 */
	protected function resolve_format( $settings ) {
		$mode = $settings['display'] ?? 'date';

		if ( 'custom' === $mode ) {
			return trim( (string) ( $settings['custom_format'] ?? '' ) );
		}

		$date = $settings['date_format'] ?? 'site';
		$date = ( 'site' === $date ) ? (string) get_option( 'date_format', 'F j, Y' ) : $date;

		$time = $settings['time_format'] ?? 'site';
		$time = ( 'site' === $time ) ? (string) get_option( 'time_format', 'g:i a' ) : $time;

		if ( 'time' === $mode ) {
			return $time;
		}

		if ( 'datetime' === $mode ) {
			$separator = (string) ( $settings['separator_text'] ?? ' — ' );
			// Every character of the separator is escaped, or a letter in it
			// (the "n" of "on", say) would be read as a format token.
			return $date . $this->escape_format_literal( $separator ) . $time;
		}

		return $date;
	}

	/**
	 * Escape a literal string for use inside a PHP date format.
	 *
	 * @param string $text Literal text.
	 * @return string
	 */
	protected function escape_format_literal( $text ) {
		$out = '';
		$len = strlen( $text );

		for ( $i = 0; $i < $len; $i++ ) {
			$out .= '\\' . $text[ $i ];
		}

		return $out;
	}

	/**
	 * Resolve the timezone to render in.
	 *
	 * @param array $settings Settings.
	 * @return DateTimeZone
	 */
	protected function resolve_timezone( $settings ) {
		$choice = (string) ( $settings['timezone'] ?? 'site' );

		if ( '' !== $choice && 'site' !== $choice ) {
			try {
				return new DateTimeZone( $choice );
			} catch ( Exception $e ) {
				// Fall through to the site timezone.
				unset( $e );
			}
		}

		return wp_timezone();
	}

	/**
	 * How often the browser should re-render, in milliseconds.
	 *
	 * Driven by what the format actually shows: ticking every second for a value
	 * that only shows the date would be pure waste, and only updating every
	 * minute would make a seconds display visibly stutter.
	 *
	 * @param string $format PHP date format.
	 * @return int Milliseconds, 0 when nothing time-based is shown.
	 */
	protected function resolve_interval( $format ) {
		$tokens = $this->format_tokens( $format );

		if ( in_array( 's', $tokens, true ) || in_array( 'U', $tokens, true ) ) {
			return 1000;
		}

		foreach ( array( 'i', 'g', 'G', 'h', 'H', 'a', 'A' ) as $token ) {
			if ( in_array( $token, $tokens, true ) ) {
				return 5000;
			}
		}

		// Date only: still refreshed, just slowly — this is what stops a cached
		// page showing yesterday, and catches midnight rolling over.
		return 60000;
	}

	/**
	 * The unescaped format characters in a format string.
	 *
	 * @param string $format PHP date format.
	 * @return string[]
	 */
	protected function format_tokens( $format ) {
		$tokens = array();
		$len    = strlen( $format );

		for ( $i = 0; $i < $len; $i++ ) {
			if ( '\\' === $format[ $i ] ) {
				$i++;
				continue;
			}
			$tokens[] = $format[ $i ];
		}

		return $tokens;
	}

	/**
	 * The site locale as a BCP 47 tag for Intl in the browser.
	 *
	 * @return string
	 */
	protected function get_bcp47_locale() {
		$locale = get_locale();
		$locale = str_replace( '_', '-', $locale );

		// WordPress locales can carry a variant suffix ("de-DE-formal") that Intl
		// rejects; keep only the language and region.
		$bits = explode( '-', $locale );
		$bits = array_slice( $bits, 0, 2 );

		return implode( '-', $bits );
	}

	/**
	 * Date format presets.
	 *
	 * @return array<string, string>
	 */
	protected function get_date_format_options() {
		$now      = time();
		$timezone = wp_timezone();

		$formats = array( 'F j, Y', 'j F Y', 'd/m/Y', 'm/d/Y', 'Y-m-d', 'l, F j, Y', 'D, j M Y', 'M j', 'Y' );
		$options = array(
			/* translators: %s: the date rendered in the site's configured format. */
			'site' => sprintf( __( 'Site Default (%s)', 'elementor-animatepro' ), wp_date( (string) get_option( 'date_format', 'F j, Y' ), $now, $timezone ) ),
		);

		foreach ( $formats as $format ) {
			$options[ $format ] = wp_date( $format, $now, $timezone );
		}

		return $options;
	}

	/**
	 * Time format presets.
	 *
	 * @return array<string, string>
	 */
	protected function get_time_format_options() {
		$now      = time();
		$timezone = wp_timezone();

		$formats = array( 'g:i a', 'g:i A', 'H:i', 'g:i:s a', 'H:i:s' );
		$options = array(
			/* translators: %s: the time rendered in the site's configured format. */
			'site' => sprintf( __( 'Site Default (%s)', 'elementor-animatepro' ), wp_date( (string) get_option( 'time_format', 'g:i a' ), $now, $timezone ) ),
		);

		foreach ( $formats as $format ) {
			$options[ $format ] = wp_date( $format, $now, $timezone );
		}

		return $options;
	}

	/**
	 * Timezone choices.
	 *
	 * @return array<string, string>
	 */
	protected function get_timezone_options() {
		$options = array( 'site' => __( 'Site Default', 'elementor-animatepro' ) );

		foreach ( timezone_identifiers_list() as $zone ) {
			$options[ $zone ] = str_replace( '_', ' ', $zone );
		}

		return $options;
	}
}
