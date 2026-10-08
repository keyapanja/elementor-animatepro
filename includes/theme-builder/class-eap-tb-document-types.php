<?php
/**
 * Elementor document types for Theme Builder templates.
 *
 * Loaded only from the `elementor/documents/register` callback, because these
 * classes extend an Elementor class that does not exist until Elementor has
 * loaded.
 *
 * A document type is what makes the editor treat a template as its own kind of
 * thing rather than a generic page. Elementor picks the type from the
 * `_elementor_template_type` meta, which EAP_TB_Post_Type::create() writes.
 *
 * @package elementor-animatepro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class EAP_TB_Document_Base extends \Elementor\Core\DocumentTypes\PageBase {

	/**
	 * Document properties.
	 *
	 * Page templates are off because a header or footer is a fragment, not a
	 * page; `register_type` is off because these documents live in our own post
	 * type rather than Elementor's template library.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['admin_tab_group']           = '';
		$properties['support_wp_page_templates'] = false;
		$properties['register_type']             = false;
		$properties['show_in_finder']            = false;
		$properties['support_kit']               = true;

		return $properties;
	}
}

class EAP_TB_Document_Header extends EAP_TB_Document_Base {

	/**
	 * Document type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'eap-header';
	}

	/**
	 * Document title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'Header', 'elementor-animatepro' );
	}

	/**
	 * Plural document title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Headers', 'elementor-animatepro' );
	}
}

class EAP_TB_Document_Single extends EAP_TB_Document_Base {

	/**
	 * Document type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'eap-single';
	}

	/**
	 * Document title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'Single', 'elementor-animatepro' );
	}

	/**
	 * Plural document title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Singles', 'elementor-animatepro' );
	}
}

class EAP_TB_Document_Archive extends EAP_TB_Document_Base {

	/**
	 * Document type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'eap-archive';
	}

	/**
	 * Document title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'Archive', 'elementor-animatepro' );
	}

	/**
	 * Plural document title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Archives', 'elementor-animatepro' );
	}
}

class EAP_TB_Document_Popup extends EAP_TB_Document_Base {

	/**
	 * Document type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'eap-popup';
	}

	/**
	 * Document title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'Popup', 'elementor-animatepro' );
	}

	/**
	 * Plural document title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Popups', 'elementor-animatepro' );
	}

	/**
	 * Document settings.
	 *
	 * A popup's behaviour belongs with the popup, not on a separate admin
	 * screen — whoever designs it decides when it opens and how it closes. The
	 * values land in `_elementor_page_settings` and the front end reads them
	 * back through get_settings().
	 *
	 * @return void
	 */
	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
			'eap_popup_section',
			array(
				'label' => esc_html__( 'Popup', 'elementor-animatepro' ),
				'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_control(
			'eap_popup_trigger',
			array(
				'label'   => esc_html__( 'Opens', 'elementor-animatepro' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'delay',
				'options' => array(
					'load'       => esc_html__( 'Immediately', 'elementor-animatepro' ),
					'delay'      => esc_html__( 'After a delay', 'elementor-animatepro' ),
					'scroll'     => esc_html__( 'After scrolling', 'elementor-animatepro' ),
					'exit'       => esc_html__( 'On exit intent', 'elementor-animatepro' ),
					'inactivity' => esc_html__( 'After inactivity', 'elementor-animatepro' ),
					'click'      => esc_html__( 'On click', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'eap_popup_delay',
			array(
				'label'     => esc_html__( 'Delay (seconds)', 'elementor-animatepro' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 3,
				'min'       => 0,
				'max'       => 600,
				'condition' => array( 'eap_popup_trigger' => 'delay' ),
			)
		);

		$this->add_control(
			'eap_popup_scroll',
			array(
				'label'     => esc_html__( 'Scrolled (%)', 'elementor-animatepro' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 40,
				'min'       => 1,
				'max'       => 100,
				'condition' => array( 'eap_popup_trigger' => 'scroll' ),
			)
		);

		$this->add_control(
			'eap_popup_inactivity',
			array(
				'label'     => esc_html__( 'Idle for (seconds)', 'elementor-animatepro' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 30,
				'min'       => 3,
				'max'       => 600,
				'condition' => array( 'eap_popup_trigger' => 'inactivity' ),
			)
		);

		$this->add_control(
			'eap_popup_click_selector',
			array(
				'label'       => esc_html__( 'Clicked Element', 'elementor-animatepro' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => '.open-popup',
				'description' => esc_html__( 'A CSS selector. Any matching element on the page opens this popup, so a button built with any widget can do it.', 'elementor-animatepro' ),
				'condition'   => array( 'eap_popup_trigger' => 'click' ),
			)
		);

		$this->add_control(
			'eap_popup_frequency',
			array(
				'label'     => esc_html__( 'Show', 'elementor-animatepro' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'session',
				'options'   => array(
					'always'  => esc_html__( 'Every time', 'elementor-animatepro' ),
					'session' => esc_html__( 'Once per visit', 'elementor-animatepro' ),
					'days'    => esc_html__( 'Once every few days', 'elementor-animatepro' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'eap_popup_days',
			array(
				'label'     => esc_html__( 'Days', 'elementor-animatepro' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 7,
				'min'       => 1,
				'max'       => 365,
				'condition' => array( 'eap_popup_frequency' => 'days' ),
			)
		);

		$this->add_control(
			'eap_popup_position',
			array(
				'label'     => esc_html__( 'Position', 'elementor-animatepro' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => array(
					'center'       => esc_html__( 'Center', 'elementor-animatepro' ),
					'top'          => esc_html__( 'Top', 'elementor-animatepro' ),
					'bottom'       => esc_html__( 'Bottom', 'elementor-animatepro' ),
					'left'         => esc_html__( 'Left', 'elementor-animatepro' ),
					'right'        => esc_html__( 'Right', 'elementor-animatepro' ),
					'bottom-right' => esc_html__( 'Bottom Right', 'elementor-animatepro' ),
					'bottom-left'  => esc_html__( 'Bottom Left', 'elementor-animatepro' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'eap_popup_width',
			array(
				'label'   => esc_html__( 'Width (px)', 'elementor-animatepro' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 640,
				'min'     => 200,
				'max'     => 1600,
			)
		);

		$this->add_control(
			'eap_popup_animation',
			array(
				'label'   => esc_html__( 'Animation', 'elementor-animatepro' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => array(
					'none'  => esc_html__( 'None', 'elementor-animatepro' ),
					'fade'  => esc_html__( 'Fade', 'elementor-animatepro' ),
					'slide' => esc_html__( 'Slide', 'elementor-animatepro' ),
					'zoom'  => esc_html__( 'Zoom', 'elementor-animatepro' ),
				),
			)
		);

		$this->add_control(
			'eap_popup_overlay_color',
			array(
				'label'     => esc_html__( 'Overlay Color', 'elementor-animatepro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => 'rgba(15, 23, 42, 0.55)',
				'selectors' => array(
					'{{WRAPPER}} .eap-popup__overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'eap_popup_close_button',
			array(
				'label'     => esc_html__( 'Close Button', 'elementor-animatepro' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'eap_popup_close_overlay',
			array(
				'label'   => esc_html__( 'Close on Overlay Click', 'elementor-animatepro' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'eap_popup_close_esc',
			array(
				'label'       => esc_html__( 'Close on Esc', 'elementor-animatepro' ),
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'description' => esc_html__( 'Leaving every close option off would trap the visitor, so the close button is forced back on if you do.', 'elementor-animatepro' ),
			)
		);

		$this->add_control(
			'eap_popup_auto_close',
			array(
				'label'       => esc_html__( 'Close After (seconds)', 'elementor-animatepro' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 600,
				'description' => esc_html__( '0 leaves it open until the visitor closes it.', 'elementor-animatepro' ),
			)
		);

		$this->end_controls_section();
	}
}

class EAP_TB_Document_Loop_Item extends EAP_TB_Document_Base {

	/**
	 * Document type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'eap-loop-item';
	}

	/**
	 * Document title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'Loop Item', 'elementor-animatepro' );
	}

	/**
	 * Plural document title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Loop Items', 'elementor-animatepro' );
	}
}

class EAP_TB_Document_Search extends EAP_TB_Document_Base {

	/**
	 * Document type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'eap-search';
	}

	/**
	 * Document title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'Search Results', 'elementor-animatepro' );
	}

	/**
	 * Plural document title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Search Results', 'elementor-animatepro' );
	}
}

class EAP_TB_Document_404 extends EAP_TB_Document_Base {

	/**
	 * Document type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'eap-404';
	}

	/**
	 * Document title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( '404 Page', 'elementor-animatepro' );
	}

	/**
	 * Plural document title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( '404 Pages', 'elementor-animatepro' );
	}
}

class EAP_TB_Document_Footer extends EAP_TB_Document_Base {

	/**
	 * Document type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'eap-footer';
	}

	/**
	 * Document title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'Footer', 'elementor-animatepro' );
	}

	/**
	 * Plural document title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Footers', 'elementor-animatepro' );
	}
}
