/**
 * EAP Content Toggle — editor element-type registration.
 *
 * Elementor decides which editor Model/View a widget uses from its element-type
 * registry. Nested widgets (Tabs, Accordion) each register a type that extends
 * `NestedElementBase`, which supplies the `NestedModelBase` model + the nested
 * View that renders a droppable child-container region. A widget that does NOT
 * register such a type falls back to the generic `Widget` model — which has no
 * children region, so its default child containers are never created and no
 * "Drag widget here" drop zones ever appear in the editor.
 *
 * Our Content Toggle is a `Widget_Nested_Base` on the PHP side and its editor
 * config is already correct (support_nesting, defaults.elements, placeholder
 * selector). The only missing piece is this client-side type registration, which
 * built-in nested widgets ship as their own editor script. This file is that
 * piece for `eap-content-toggle`.
 *
 * TIMING IS CRITICAL: the registration MUST happen before the preview renders a
 * *saved* Content Toggle. An unregistered nested instance renders with the
 * generic widget view and wedges the whole preview (it never finishes loading).
 * So we register the instant Elementor's element manager exists — polling
 * tightly with NO premature give-up — plus event hooks and a reload safety net.
 *
 * No-ops safely when nested elements is unavailable (NestedElementBase missing)
 * or the widget is disabled (the type is only ever used by a real instance).
 */
( function() {
	'use strict';

	var WIDGET_TYPE = 'eap-content-toggle';
	var registered = false;

	/**
	 * Register the nested element type. Idempotent and safe to call repeatedly:
	 * returns true once our type is in place, false while the editor app (its
	 * element manager) is not ready yet.
	 *
	 * @return {boolean} Whether registration is complete.
	 */
	function doRegister() {
		if ( registered ) {
			return true;
		}

		if ( typeof window.elementor === 'undefined' ) {
			return false;
		}

		var types = window.elementor.modules
			&& window.elementor.modules.elements
			&& window.elementor.modules.elements.types;

		var manager = window.elementor.elementsManager;

		// Editor app / nested elements not ready (or not present at all).
		if ( ! types || ! types.NestedElementBase || ! manager || typeof manager.registerElementType !== 'function' ) {
			return false;
		}

		// Already registered by us (e.g. a prior run) — record and stop.
		try {
			if ( typeof manager.getElementTypeClass === 'function' ) {
				var current = manager.getElementTypeClass( WIDGET_TYPE );
				if ( current && current.constructor && current.constructor.eapRegistered ) {
					registered = true;
					return true;
				}
			}
		} catch ( e ) {}

		var NestedElementBase = types.NestedElementBase;

		// Extend NestedElementBase, overriding only the type id. The nested model,
		// nested view (which honours our `elements_placeholder_selector`) and empty
		// view are all inherited — identical to how Nested Tabs works.
		function EapContentToggleType() {
			NestedElementBase.apply( this, arguments );
		}

		EapContentToggleType.prototype = Object.create( NestedElementBase.prototype );
		EapContentToggleType.prototype.constructor = EapContentToggleType;
		EapContentToggleType.prototype.getType = function() {
			return WIDGET_TYPE;
		};

		// Marker so re-runs (poll + events) don't register twice.
		EapContentToggleType.eapRegistered = true;

		manager.registerElementType( new EapContentToggleType() );
		registered = true;

		return true;
	}

	// Primary mechanism: tight poll with no early give-up. The element manager can
	// take many seconds to come up on a busy machine; we keep trying until it does
	// (and clear once registered). Because rendering a saved instance into the
	// preview iframe is asynchronous (the iframe must load first), registering the
	// moment the manager exists reliably beats the first render.
	var attempts = 0;
	var MAX_ATTEMPTS = 1200; // ~60s hard cap, far beyond any real editor boot.
	var timer = window.setInterval( function() {
		attempts += 1;
		if ( doRegister() || attempts >= MAX_ATTEMPTS ) {
			window.clearInterval( timer );
		}
	}, 50 );

	// Immediate attempt (covers the already-initialised / fast-boot case).
	doRegister();

	// Extra early chance the instant Elementor announces init.
	if ( window.elementor && typeof window.elementor.on === 'function' ) {
		window.elementor.on( 'elementor:init', doRegister );

		// Last-resort safety net: if for any reason registration had not happened
		// by the time the preview finished loading, register now and re-render the
		// preview once so saved instances pick up the nested view.
		window.elementor.on( 'preview:loaded', function() {
			if ( registered ) {
				return;
			}
			if ( doRegister() && typeof window.elementor.reloadPreview === 'function' ) {
				window.elementor.reloadPreview();
			}
		} );
	}
} )();
