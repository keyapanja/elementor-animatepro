<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

/**
 * Conditional Display extension.
 *
 * Adds a "Conditional Display" section to the Advanced tab of EVERY element —
 * widgets, containers, sections and columns — and shows or hides that element
 * per visitor based on a list of conditions.
 *
 * How it suppresses output: Elementor has no generic `should_render` filter, only
 * a per-type one (`elementor/frontend/{$element_type}/should_render`), so all
 * four types are hooked. Returning false there means the element's markup is
 * never printed at all, rather than being hidden with CSS — restricted content
 * therefore never reaches the page source.
 *
 * Evaluation is SKIPPED in the editor, otherwise a hidden element could not be
 * selected or edited on the canvas.
 *
 * IMPORTANT (documented in the README too): conditions are evaluated
 * server-side, so a full-page cache will serve whichever variant it cached to
 * everyone. Exclude pages using visitor-specific conditions (login, user, cart,
 * country, query string) from full-page caching.
 *
 * Enabled/disabled from the plugin's Extensions page via the
 * `conditional-display` toggle (absent = on).
 */
class EAP_Conditional_Display {

	const EXTENSION_KEY = 'conditional-display';

	/**
	 * Elements already given the section this request, keyed by object hash.
	 *
	 * @var array<string, bool>
	 */
	protected $injected = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! self::is_enabled() ) {
			return;
		}

		add_action( 'elementor/element/after_section_end', array( $this, 'maybe_inject' ), 10, 2 );

		// No generic filter exists — each element type has its own.
		foreach ( array( 'widget', 'section', 'column', 'container' ) as $type ) {
			add_filter( "elementor/frontend/{$type}/should_render", array( $this, 'should_render' ), 10, 2 );
		}
	}

	/**
	 * Whether the extension is enabled (Extensions page toggle; absent = on).
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$states = get_option( EAP_Admin::EXTENSIONS_OPTION, array() );
		if ( ! is_array( $states ) ) {
			return true;
		}
		return ! array_key_exists( self::EXTENSION_KEY, $states ) || ! empty( $states[ self::EXTENSION_KEY ] );
	}

	/* =====================================================================
	 * CONTROLS
	 * ================================================================== */

	/**
	 * Add the section once, after a section every element has.
	 *
	 * @param \Elementor\Controls_Stack $element    Element.
	 * @param string                    $section_id Section that just closed.
	 * @return void
	 */
	public function maybe_inject( $element, $section_id ) {
		if ( ! in_array( $section_id, array( '_section_responsive', '_section_style' ), true ) ) {
			return;
		}

		if ( ! is_object( $element ) || ! method_exists( $element, 'start_controls_section' ) ) {
			return;
		}

		$key = spl_object_hash( $element );
		if ( isset( $this->injected[ $key ] ) ) {
			return;
		}
		$this->injected[ $key ] = true;

		$this->add_controls( $element );
	}

	/**
	 * Register the conditional display controls.
	 *
	 * @param \Elementor\Controls_Stack $element Element.
	 * @return void
	 */
	protected function add_controls( $element ) {
		$element->start_controls_section(
			'eap_cd_section',
			array(
				'label' => __( 'Conditional Display', 'elementor-animatepro' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'eap_cd_enable',
			array(
				'label'        => __( 'Enable Conditional Display', 'elementor-animatepro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-animatepro' ),
				'label_off'    => __( 'No', 'elementor-animatepro' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$element->add_control(
			'eap_cd_action',
			array(
				'label'     => __( 'Action', 'elementor-animatepro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'show',
				'options'   => array(
					'show' => array(
						'title' => __( 'Show when conditions match', 'elementor-animatepro' ),
						'icon'  => 'eicon-preview-medium',
					),
					'hide' => array(
						'title' => __( 'Hide when conditions match', 'elementor-animatepro' ),
						'icon'  => 'eicon-ban',
					),
				),
				'toggle'    => false,
				'condition' => array( 'eap_cd_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_cd_relation',
			array(
				'label'     => __( 'Match', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'all',
				'options'   => array(
					'all' => __( 'All conditions (AND)', 'elementor-animatepro' ),
					'any' => __( 'Any condition (OR)', 'elementor-animatepro' ),
				),
				'condition' => array( 'eap_cd_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_cd_conditions',
			array(
				'label'       => __( 'Logics', 'elementor-animatepro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $this->get_condition_fields(),
				'default'     => array(
					array( 'eap_cd_type' => 'login_status' ),
				),
				'title_field' => '{{{ eap_cd_type }}}',
				'condition'   => array( 'eap_cd_enable' => 'yes' ),
			)
		);

		$element->add_control(
			'eap_cd_cache_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Conditions are evaluated on the server. Exclude pages using visitor-specific conditions (login, user, country, query string) from full-page caching.', 'elementor-animatepro' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'eap_cd_enable' => 'yes' ),
			)
		);

		$element->end_controls_section();
	}

	/**
	 * The repeater's per-condition fields.
	 *
	 * @return array
	 */
	protected function get_condition_fields() {
		$repeater = new Repeater();

		$repeater->add_control(
			'eap_cd_type',
			array(
				'label'   => __( 'Type', 'elementor-animatepro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'login_status',
				'options' => array(
					'login_status' => __( 'Login Status', 'elementor-animatepro' ),
					'user_role'    => __( 'User Role', 'elementor-animatepro' ),
					'user'         => __( 'Specific User', 'elementor-animatepro' ),
					'page_type'    => __( 'Page Type', 'elementor-animatepro' ),
					'post_type'    => __( 'Post Type', 'elementor-animatepro' ),
					'post'         => __( 'Specific Post / Page', 'elementor-animatepro' ),
					'taxonomy'     => __( 'Taxonomy Term', 'elementor-animatepro' ),
					'date_time'    => __( 'Date & Time', 'elementor-animatepro' ),
					'day_of_week'  => __( 'Day of Week', 'elementor-animatepro' ),
					'browser'      => __( 'Browser', 'elementor-animatepro' ),
					'os'           => __( 'Operating System', 'elementor-animatepro' ),
					'device'       => __( 'Device', 'elementor-animatepro' ),
					'query_string' => __( 'Query String', 'elementor-animatepro' ),
					'url'          => __( 'URL / Referrer', 'elementor-animatepro' ),
					'country'      => __( 'Country', 'elementor-animatepro' ),
				),
			)
		);

		$repeater->add_control(
			'eap_cd_operator',
			array(
				'label'   => __( 'Condition', 'elementor-animatepro' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'is',
				'options' => array(
					'is'     => array(
						'title' => __( 'Is', 'elementor-animatepro' ),
						'icon'  => 'eicon-check',
					),
					'is_not' => array(
						'title' => __( 'Is not', 'elementor-animatepro' ),
						'icon'  => 'eicon-close',
					),
				),
				'toggle'  => false,
			)
		);

		/* ---- Login status ---- */
		$repeater->add_control(
			'eap_cd_login_status',
			array(
				'label'     => __( 'Login Status', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'logged_in',
				'options'   => array(
					'logged_in'  => __( 'Logged In', 'elementor-animatepro' ),
					'logged_out' => __( 'Logged Out', 'elementor-animatepro' ),
				),
				'condition' => array( 'eap_cd_type' => 'login_status' ),
			)
		);

		/* ---- User role ---- */
		$repeater->add_control(
			'eap_cd_roles',
			array(
				'label'       => __( 'Roles', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => self::get_role_options(),
				'condition'   => array( 'eap_cd_type' => 'user_role' ),
			)
		);

		/* ---- Specific user ---- */
		$repeater->add_control(
			'eap_cd_users',
			array(
				'label'       => __( 'Users', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => self::get_user_options(),
				'condition'   => array( 'eap_cd_type' => 'user' ),
			)
		);

		/* ---- Page type ---- */
		$repeater->add_control(
			'eap_cd_page_type',
			array(
				'label'     => __( 'Page Type', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'front_page',
				'options'   => array(
					'front_page' => __( 'Front Page', 'elementor-animatepro' ),
					'blog'       => __( 'Blog / Posts Page', 'elementor-animatepro' ),
					'singular'   => __( 'Single Post / Page', 'elementor-animatepro' ),
					'archive'    => __( 'Any Archive', 'elementor-animatepro' ),
					'category'   => __( 'Category Archive', 'elementor-animatepro' ),
					'tag'        => __( 'Tag Archive', 'elementor-animatepro' ),
					'author'     => __( 'Author Archive', 'elementor-animatepro' ),
					'date'       => __( 'Date Archive', 'elementor-animatepro' ),
					'search'     => __( 'Search Results', 'elementor-animatepro' ),
					'not_found'  => __( '404 Page', 'elementor-animatepro' ),
				),
				'condition' => array( 'eap_cd_type' => 'page_type' ),
			)
		);

		/* ---- Post type ---- */
		$repeater->add_control(
			'eap_cd_post_types',
			array(
				'label'       => __( 'Post Types', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => EAP_Posts_Query::get_post_type_options(),
				'condition'   => array( 'eap_cd_type' => 'post_type' ),
			)
		);

		/* ---- Specific post ---- */
		$repeater->add_control(
			'eap_cd_posts',
			array(
				'label'       => __( 'Posts / Pages', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => EAP_Posts_Query::get_post_options( 100 ),
				'condition'   => array( 'eap_cd_type' => 'post' ),
			)
		);

		/* ---- Taxonomy term ---- */
		$repeater->add_control(
			'eap_cd_terms',
			array(
				'label'       => __( 'Terms', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => EAP_Posts_Query::get_term_options(),
				'condition'   => array( 'eap_cd_type' => 'taxonomy' ),
			)
		);

		/* ---- Date & time ---- */
		$repeater->add_control(
			'eap_cd_date_operator',
			array(
				'label'     => __( 'When', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => array(
					'after'   => __( 'After', 'elementor-animatepro' ),
					'before'  => __( 'Before', 'elementor-animatepro' ),
					'between' => __( 'Between', 'elementor-animatepro' ),
				),
				'condition' => array( 'eap_cd_type' => 'date_time' ),
			)
		);

		$repeater->add_control(
			'eap_cd_date_from',
			array(
				'label'          => __( 'Date', 'elementor-animatepro' ),
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => array( 'enableTime' => true ),
				'condition'      => array( 'eap_cd_type' => 'date_time' ),
			)
		);

		$repeater->add_control(
			'eap_cd_date_to',
			array(
				'label'          => __( 'End Date', 'elementor-animatepro' ),
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => array( 'enableTime' => true ),
				'condition'      => array(
					'eap_cd_type'          => 'date_time',
					'eap_cd_date_operator' => 'between',
				),
			)
		);

		/* ---- Day of week ---- */
		$repeater->add_control(
			'eap_cd_days',
			array(
				'label'       => __( 'Days', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => array(
					'1' => __( 'Monday', 'elementor-animatepro' ),
					'2' => __( 'Tuesday', 'elementor-animatepro' ),
					'3' => __( 'Wednesday', 'elementor-animatepro' ),
					'4' => __( 'Thursday', 'elementor-animatepro' ),
					'5' => __( 'Friday', 'elementor-animatepro' ),
					'6' => __( 'Saturday', 'elementor-animatepro' ),
					'0' => __( 'Sunday', 'elementor-animatepro' ),
				),
				'condition'   => array( 'eap_cd_type' => 'day_of_week' ),
			)
		);

		/* ---- Browser ---- */
		$repeater->add_control(
			'eap_cd_browsers',
			array(
				'label'       => __( 'Browsers', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => array(
					'chrome'  => 'Google Chrome',
					'firefox' => 'Mozilla Firefox',
					'safari'  => 'Safari',
					'edge'    => 'Edge',
					'opera'   => 'Opera',
					'ie'      => 'Internet Explorer',
				),
				'condition'   => array( 'eap_cd_type' => 'browser' ),
			)
		);

		/* ---- OS ---- */
		$repeater->add_control(
			'eap_cd_os',
			array(
				'label'       => __( 'Operating Systems', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => array(
					'windows' => 'Windows',
					'macos'   => 'macOS',
					'linux'   => 'Linux',
					'android' => 'Android',
					'ios'     => 'iOS',
				),
				'condition'   => array( 'eap_cd_type' => 'os' ),
			)
		);

		/* ---- Device ---- */
		$repeater->add_control(
			'eap_cd_devices',
			array(
				'label'       => __( 'Devices', 'elementor-animatepro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => array(
					'desktop' => __( 'Desktop', 'elementor-animatepro' ),
					'tablet'  => __( 'Tablet', 'elementor-animatepro' ),
					'mobile'  => __( 'Mobile', 'elementor-animatepro' ),
				),
				'condition'   => array( 'eap_cd_type' => 'device' ),
			)
		);

		/* ---- Query string ---- */
		$repeater->add_control(
			'eap_cd_query_key',
			array(
				'label'       => __( 'Key', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'blackfriday',
				'condition'   => array( 'eap_cd_type' => 'query_string' ),
			)
		);

		$repeater->add_control(
			'eap_cd_query_compare',
			array(
				'label'     => __( 'Compare', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'equals',
				'options'   => array(
					'exists'   => __( 'Key exists', 'elementor-animatepro' ),
					'equals'   => __( 'Equals value', 'elementor-animatepro' ),
					'contains' => __( 'Contains value', 'elementor-animatepro' ),
				),
				'condition' => array( 'eap_cd_type' => 'query_string' ),
			)
		);

		$repeater->add_control(
			'eap_cd_query_value',
			array(
				'label'       => __( 'Value', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '2024',
				'condition'   => array(
					'eap_cd_type'          => 'query_string',
					'eap_cd_query_compare' => array( 'equals', 'contains' ),
				),
			)
		);

		/* ---- URL / referrer ---- */
		$repeater->add_control(
			'eap_cd_url_source',
			array(
				'label'     => __( 'URL Type', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'current',
				'options'   => array(
					'current'  => __( 'Current URL', 'elementor-animatepro' ),
					'referrer' => __( 'Referrer URL', 'elementor-animatepro' ),
				),
				'condition' => array( 'eap_cd_type' => 'url' ),
			)
		);

		$repeater->add_control(
			'eap_cd_url_compare',
			array(
				'label'     => __( 'Compare', 'elementor-animatepro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'contains',
				'options'   => array(
					'contains'    => __( 'Contains', 'elementor-animatepro' ),
					'equals'      => __( 'Equals', 'elementor-animatepro' ),
					'starts_with' => __( 'Starts with', 'elementor-animatepro' ),
					'ends_with'   => __( 'Ends with', 'elementor-animatepro' ),
				),
				'condition' => array( 'eap_cd_type' => 'url' ),
			)
		);

		$repeater->add_control(
			'eap_cd_url_string',
			array(
				'label'       => __( 'String', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'deal',
				'condition'   => array( 'eap_cd_type' => 'url' ),
			)
		);

		/* ---- Country ---- */
		$repeater->add_control(
			'eap_cd_countries',
			array(
				'label'       => __( 'Country Codes', 'elementor-animatepro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'US, GB',
				'description' => __( 'Comma-separated ISO codes. Needs a geo provider — Cloudflare IP Geolocation or a host that sets a country header. No external lookups are made.', 'elementor-animatepro' ),
				'condition'   => array( 'eap_cd_type' => 'country' ),
			)
		);

		return $repeater->get_controls();
	}

	/* =====================================================================
	 * EVALUATION
	 * ================================================================== */

	/**
	 * Decide whether an element renders.
	 *
	 * @param bool                    $should_render Current decision.
	 * @param \Elementor\Element_Base $element       Element.
	 * @return bool
	 */
	public function should_render( $should_render, $element ) {
		if ( ! $should_render ) {
			return $should_render;
		}

		// Never hide anything on the editor canvas — it would become unselectable.
		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->editor ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return $should_render;
		}

		if ( ! is_object( $element ) || ! method_exists( $element, 'get_settings_for_display' ) ) {
			return $should_render;
		}

		$settings = $element->get_settings_for_display();

		if ( 'yes' !== ( $settings['eap_cd_enable'] ?? '' ) ) {
			return $should_render;
		}

		$rows = isset( $settings['eap_cd_conditions'] ) && is_array( $settings['eap_cd_conditions'] ) ? $settings['eap_cd_conditions'] : array();
		if ( empty( $rows ) ) {
			return $should_render;
		}

		$relation = ( 'any' === ( $settings['eap_cd_relation'] ?? 'all' ) ) ? 'any' : 'all';
		$matched  = ( 'all' === $relation );

		foreach ( $rows as $row ) {
			$result = $this->evaluate( $row );

			if ( 'is_not' === ( $row['eap_cd_operator'] ?? 'is' ) ) {
				$result = ! $result;
			}

			if ( 'all' === $relation ) {
				$matched = $matched && $result;
				if ( ! $matched ) {
					break;
				}
			} else {
				$matched = $matched || $result;
				if ( $matched ) {
					break;
				}
			}
		}

		return ( 'hide' === ( $settings['eap_cd_action'] ?? 'show' ) ) ? ! $matched : $matched;
	}

	/**
	 * Evaluate a single condition row.
	 *
	 * @param array $row Repeater row.
	 * @return bool
	 */
	protected function evaluate( $row ) {
		$type = $row['eap_cd_type'] ?? '';

		switch ( $type ) {
			case 'login_status':
				return ( 'logged_in' === ( $row['eap_cd_login_status'] ?? 'logged_in' ) ) ? is_user_logged_in() : ! is_user_logged_in();

			case 'user_role':
				$roles = (array) ( $row['eap_cd_roles'] ?? array() );
				if ( empty( $roles ) || ! is_user_logged_in() ) {
					return false;
				}
				$user = wp_get_current_user();
				return (bool) array_intersect( $roles, (array) $user->roles );

			case 'user':
				$users = array_map( 'intval', (array) ( $row['eap_cd_users'] ?? array() ) );
				return is_user_logged_in() && in_array( get_current_user_id(), $users, true );

			case 'page_type':
				return $this->match_page_type( $row['eap_cd_page_type'] ?? '' );

			case 'post_type':
				$types = (array) ( $row['eap_cd_post_types'] ?? array() );
				return ! empty( $types ) && in_array( (string) get_post_type(), $types, true );

			case 'post':
				$posts = array_map( 'intval', (array) ( $row['eap_cd_posts'] ?? array() ) );
				return in_array( (int) get_queried_object_id(), $posts, true );

			case 'taxonomy':
				return $this->match_terms( array_map( 'intval', (array) ( $row['eap_cd_terms'] ?? array() ) ) );

			case 'date_time':
				return $this->match_date( $row );

			case 'day_of_week':
				$days = array_map( 'strval', (array) ( $row['eap_cd_days'] ?? array() ) );
				return in_array( (string) current_time( 'w' ), $days, true );

			case 'browser':
				return in_array( self::get_browser(), (array) ( $row['eap_cd_browsers'] ?? array() ), true );

			case 'os':
				return in_array( self::get_os(), (array) ( $row['eap_cd_os'] ?? array() ), true );

			case 'device':
				return in_array( self::get_device(), (array) ( $row['eap_cd_devices'] ?? array() ), true );

			case 'query_string':
				return $this->match_query_string( $row );

			case 'url':
				return $this->match_url( $row );

			case 'country':
				$codes = array_filter( array_map( 'trim', explode( ',', strtoupper( (string) ( $row['eap_cd_countries'] ?? '' ) ) ) ) );
				$country = self::get_country();
				return '' !== $country && in_array( $country, $codes, true );
		}

		return false;
	}

	/**
	 * Match the current page type.
	 *
	 * @param string $type Page type key.
	 * @return bool
	 */
	protected function match_page_type( $type ) {
		switch ( $type ) {
			case 'front_page':
				return is_front_page();
			case 'blog':
				return is_home();
			case 'singular':
				return is_singular();
			case 'archive':
				return is_archive();
			case 'category':
				return is_category();
			case 'tag':
				return is_tag();
			case 'author':
				return is_author();
			case 'date':
				return is_date();
			case 'search':
				return is_search();
			case 'not_found':
				return is_404();
		}

		return false;
	}

	/**
	 * Whether the current post has (or the archive is) one of these terms.
	 *
	 * @param int[] $term_ids Term IDs.
	 * @return bool
	 */
	protected function match_terms( $term_ids ) {
		if ( empty( $term_ids ) ) {
			return false;
		}

		// On a term archive, compare the queried term itself.
		if ( is_category() || is_tag() || is_tax() ) {
			return in_array( (int) get_queried_object_id(), $term_ids, true );
		}

		$post_id = (int) get_queried_object_id();
		if ( ! $post_id || ! is_singular() ) {
			return false;
		}

		foreach ( $term_ids as $term_id ) {
			$term = get_term( $term_id );
			if ( $term && ! is_wp_error( $term ) && has_term( $term_id, $term->taxonomy, $post_id ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Match a date / time window against site time.
	 *
	 * @param array $row Repeater row.
	 * @return bool
	 */
	protected function match_date( $row ) {
		$from = trim( (string) ( $row['eap_cd_date_from'] ?? '' ) );
		if ( '' === $from ) {
			return false;
		}

		$now  = (int) current_time( 'timestamp' );
		$from = strtotime( $from );
		if ( ! $from ) {
			return false;
		}

		$operator = $row['eap_cd_date_operator'] ?? 'after';

		if ( 'before' === $operator ) {
			return $now < $from;
		}

		if ( 'between' === $operator ) {
			$to = strtotime( (string) ( $row['eap_cd_date_to'] ?? '' ) );
			return $to ? ( $now >= $from && $now <= $to ) : false;
		}

		return $now >= $from;
	}

	/**
	 * Match a query-string parameter.
	 *
	 * @param array $row Repeater row.
	 * @return bool
	 */
	protected function match_query_string( $row ) {
		$key = trim( (string) ( $row['eap_cd_query_key'] ?? '' ) );
		if ( '' === $key ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display condition.
		if ( ! isset( $_GET[ $key ] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display condition.
		$actual  = sanitize_text_field( wp_unslash( $_GET[ $key ] ) );
		$compare = $row['eap_cd_query_compare'] ?? 'equals';
		$value   = trim( (string) ( $row['eap_cd_query_value'] ?? '' ) );

		if ( 'exists' === $compare ) {
			return true;
		}

		if ( 'contains' === $compare ) {
			return '' !== $value && false !== strpos( $actual, $value );
		}

		return $actual === $value;
	}

	/**
	 * Match the current or referrer URL.
	 *
	 * @param array $row Repeater row.
	 * @return bool
	 */
	protected function match_url( $row ) {
		$needle = trim( (string) ( $row['eap_cd_url_string'] ?? '' ) );
		if ( '' === $needle ) {
			return false;
		}

		if ( 'referrer' === ( $row['eap_cd_url_source'] ?? 'current' ) ) {
			$haystack = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
		} else {
			$haystack = home_url( isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' );
		}

		if ( '' === $haystack ) {
			return false;
		}

		switch ( $row['eap_cd_url_compare'] ?? 'contains' ) {
			case 'equals':
				return untrailingslashit( $haystack ) === untrailingslashit( $needle );
			case 'starts_with':
				return 0 === strpos( $haystack, $needle );
			case 'ends_with':
				return substr( $haystack, -strlen( $needle ) ) === $needle;
		}

		return false !== strpos( $haystack, $needle );
	}

	/* =====================================================================
	 * VISITOR DETECTION
	 * ================================================================== */

	/**
	 * The request's user agent.
	 *
	 * @return string
	 */
	protected static function ua() {
		return isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
	}

	/**
	 * Detect the browser. Order matters — Edge and Opera both contain "Chrome",
	 * and Chrome contains "Safari".
	 *
	 * @return string
	 */
	public static function get_browser() {
		$ua = self::ua();

		if ( '' === $ua ) {
			return '';
		}
		if ( preg_match( '/Edge?\//i', $ua ) ) {
			return 'edge';
		}
		if ( preg_match( '/OPR\/|Opera/i', $ua ) ) {
			return 'opera';
		}
		if ( preg_match( '/MSIE|Trident/i', $ua ) ) {
			return 'ie';
		}
		if ( preg_match( '/Firefox|FxiOS/i', $ua ) ) {
			return 'firefox';
		}
		if ( preg_match( '/Chrome|CriOS/i', $ua ) ) {
			return 'chrome';
		}
		if ( preg_match( '/Safari/i', $ua ) ) {
			return 'safari';
		}

		return '';
	}

	/**
	 * Detect the operating system.
	 *
	 * @return string
	 */
	public static function get_os() {
		$ua = self::ua();

		if ( preg_match( '/Windows/i', $ua ) ) {
			return 'windows';
		}
		if ( preg_match( '/Android/i', $ua ) ) {
			return 'android';
		}
		if ( preg_match( '/iPhone|iPad|iPod/i', $ua ) ) {
			return 'ios';
		}
		if ( preg_match( '/Mac OS X|Macintosh/i', $ua ) ) {
			return 'macos';
		}
		if ( preg_match( '/Linux/i', $ua ) ) {
			return 'linux';
		}

		return '';
	}

	/**
	 * Detect the device class.
	 *
	 * @return string
	 */
	public static function get_device() {
		$ua = self::ua();

		if ( preg_match( '/iPad|Tablet/i', $ua ) || ( preg_match( '/Android/i', $ua ) && ! preg_match( '/Mobile/i', $ua ) ) ) {
			return 'tablet';
		}
		if ( preg_match( '/Mobi|iPhone|iPod|Android/i', $ua ) ) {
			return 'mobile';
		}

		return 'desktop';
	}

	/**
	 * The visitor's country code.
	 *
	 * Read from headers a geo-aware proxy/host already set — Cloudflare's IP
	 * Geolocation, or a server that populates GEOIP_COUNTRY_CODE. NO external
	 * lookup is made, so no visitor IP ever leaves the site. Sites with their own
	 * provider can supply it through the `eap_conditional_display_country` filter.
	 *
	 * @return string Two-letter uppercase code, or '' when unknown.
	 */
	public static function get_country() {
		$country = '';

		foreach ( array( 'HTTP_CF_IPCOUNTRY', 'GEOIP_COUNTRY_CODE', 'HTTP_X_COUNTRY_CODE', 'HTTP_CLOUDFRONT_VIEWER_COUNTRY' ) as $header ) {
			if ( ! empty( $_SERVER[ $header ] ) ) {
				$country = strtoupper( sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) ) );
				break;
			}
		}

		/**
		 * Filter the resolved visitor country code.
		 *
		 * @param string $country Two-letter code, or '' when unknown.
		 */
		return (string) apply_filters( 'eap_conditional_display_country', $country );
	}

	/* =====================================================================
	 * OPTION BUILDERS
	 * ================================================================== */

	/**
	 * Editable roles.
	 *
	 * @return array<string, string>
	 */
	public static function get_role_options() {
		$options = array();

		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		foreach ( get_editable_roles() as $key => $role ) {
			$options[ $key ] = translate_user_role( $role['name'] );
		}

		return $options;
	}

	/**
	 * Users for the picker (capped — a big site would otherwise stall the panel).
	 *
	 * @return array<int, string>
	 */
	public static function get_user_options() {
		$options = array();

		$users = get_users(
			array(
				'number'  => 100,
				'orderby' => 'display_name',
				'fields'  => array( 'ID', 'display_name' ),
			)
		);

		foreach ( $users as $user ) {
			$options[ $user->ID ] = $user->display_name;
		}

		return $options;
	}
}
