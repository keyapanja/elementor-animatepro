/**
 * EAP Animated Off-Canvas — editor element-type registration.
 *
 * Nested widgets need their own editor element type (extending
 * `NestedElementBase`) so the droppable child-container region renders in the
 * editor. Without it the widget falls back to the generic `Widget` view, the
 * default child container is never created, and no "Drag widget here" drop zone
 * appears — and a *saved* nested instance can wedge the whole preview.
 *
 * This is the same generic registration our Content Toggle ships; only the
 * widget type id differs. No-ops safely when nested elements is unavailable.
 */
( function() {
	'use strict';

	var WIDGET_TYPE = 'eap-animated-off-canvas';
	var registered = false;

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

		if ( ! types || ! types.NestedElementBase || ! manager || typeof manager.registerElementType !== 'function' ) {
			return false;
		}

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

		function EapOffCanvasType() {
			NestedElementBase.apply( this, arguments );
		}

		EapOffCanvasType.prototype = Object.create( NestedElementBase.prototype );
		EapOffCanvasType.prototype.constructor = EapOffCanvasType;
		EapOffCanvasType.prototype.getType = function() {
			return WIDGET_TYPE;
		};

		EapOffCanvasType.eapRegistered = true;

		manager.registerElementType( new EapOffCanvasType() );
		registered = true;

		return true;
	}

	var attempts = 0;
	var MAX_ATTEMPTS = 1200; // ~60s hard cap, far beyond any real editor boot.
	var timer = window.setInterval( function() {
		attempts += 1;
		if ( doRegister() || attempts >= MAX_ATTEMPTS ) {
			window.clearInterval( timer );
		}
	}, 50 );

	doRegister();

	if ( window.elementor && typeof window.elementor.on === 'function' ) {
		window.elementor.on( 'elementor:init', doRegister );

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
