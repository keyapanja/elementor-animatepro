/**
 * EAP Nested Slider — editor element-type registration.
 *
 * Same job, and the same timing constraints, as content-toggle-editor.js (read
 * that file's header for the full reasoning): a nested widget needs a
 * NestedElementBase-derived element type registered in the editor, or its
 * child containers never get a droppable region — and a saved instance
 * rendered before registration wedges the preview. So: register the instant
 * Elementor's element manager exists, poll with no early give-up, and reload
 * the preview once as a last resort.
 *
 * This is the third copy of the pattern (Content Toggle, Animated Off-Canvas,
 * this). The first two are left as they are: they work, and a shared
 * registrar is a change to verify in the real editor, not from a harness.
 */
( function() {
	'use strict';

	var WIDGET_TYPE = 'eap-nested-slider';
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

		function EapNestedSliderType() {
			NestedElementBase.apply( this, arguments );
		}

		EapNestedSliderType.prototype = Object.create( NestedElementBase.prototype );
		EapNestedSliderType.prototype.constructor = EapNestedSliderType;
		EapNestedSliderType.prototype.getType = function() {
			return WIDGET_TYPE;
		};

		EapNestedSliderType.eapRegistered = true;

		manager.registerElementType( new EapNestedSliderType() );
		registered = true;

		return true;
	}

	var attempts = 0;
	var MAX_ATTEMPTS = 1200; // ~60s hard cap.
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
