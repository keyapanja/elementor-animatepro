(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '.eap-offcanvas';

	const setup = (root, editorMode) => {
		// Bind in the editor too, so the panel opens on trigger click exactly like
		// the front-end. Only the portal, scroll-lock, focus handling and
		// auto-open (load / hash) are skipped in the editor.
		if ( ! root.hasAttribute( 'data-eap-off-canvas' ) ) {
			return;
		}
		if ( root.dataset.eapOcBound === '1' ) {
			return;
		}
		root.dataset.eapOcBound = '1';

		const trigger = root.querySelector( '.eap-offcanvas__trigger' );
		const stage = root.querySelector( '.eap-offcanvas__stage' );
		if ( ! trigger || ! stage ) {
			return;
		}
		const closeBtn = stage.querySelector( '.eap-offcanvas__close' );
		const overlay = stage.querySelector( '.eap-offcanvas__overlay' );
		const panel = stage.querySelector( '.eap-offcanvas__panel' );

		const overlayCloses = root.dataset.overlayClose === '1';
		const openOnLoad = root.dataset.openLoad === '1';
		const hashId = root.dataset.hash || '';

		let portal = null;
		let closeTimer = 0;
		let lastFocus = null;

		const duration = () => {
			const raw = parseFloat( getComputedStyle( stage ).getPropertyValue( '--eap-oc-duration' ) );
			return isNaN( raw ) ? 350 : raw;
		};

		// Portal the stage into the .elementor page wrapper so position:fixed is
		// viewport-relative (escaping transformed ancestors) while the widget's
		// {{WRAPPER}}-scoped styles still match (the portal carries the widget
		// wrapper class). Same lesson as the Nav / Mega Menu.
		const buildPortal = () => {
			if ( portal ) {
				return;
			}
			const wrapper = root.closest( '.elementor-element' );
			portal = document.createElement( 'div' );
			portal.className = 'eap-offcanvas-portal' + ( wrapper ? ' ' + wrapper.className : '' );
			const host = root.closest( '.elementor' ) || document.body;
			host.appendChild( portal );
			portal.appendChild( stage );
		};

		const destroyPortal = () => {
			if ( ! portal ) {
				return;
			}
			root.appendChild( stage );
			portal.remove();
			portal = null;
		};

		const isOpen = () => stage.classList.contains( 'is-open' );

		const open = () => {
			if ( isOpen() ) {
				return;
			}
			window.clearTimeout( closeTimer );
			lastFocus = document.activeElement;
			if ( ! editorMode ) {
				buildPortal();
			}
			window.requestAnimationFrame( () => stage.classList.add( 'is-open' ) );
			trigger.setAttribute( 'aria-expanded', 'true' );
			if ( ! editorMode ) {
				document.documentElement.style.overflow = 'hidden';
				if ( panel ) {
					window.setTimeout( () => panel.focus(), 60 );
				}
			}
		};

		const close = () => {
			if ( ! isOpen() ) {
				return;
			}
			stage.classList.remove( 'is-open' );
			trigger.setAttribute( 'aria-expanded', 'false' );
			if ( editorMode ) {
				return;
			}
			document.documentElement.style.overflow = '';
			closeTimer = window.setTimeout( destroyPortal, duration() + 40 );
			if ( lastFocus && typeof lastFocus.focus === 'function' ) {
				lastFocus.focus();
			} else {
				trigger.focus();
			}
		};

		trigger.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			isOpen() ? close() : open();
		} );

		if ( closeBtn ) {
			closeBtn.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				close();
			} );
		}

		if ( overlay && overlayCloses ) {
			overlay.addEventListener( 'click', close );
		}

		document.addEventListener( 'keydown', ( event ) => {
			if ( event.key === 'Escape' && isOpen() ) {
				close();
			}
		} );

		// Simple focus trap: keep Tab focus inside the open panel (front-end only —
		// in the editor normal focus must reach the Elementor UI).
		if ( panel && ! editorMode ) {
			panel.addEventListener( 'keydown', ( event ) => {
				if ( event.key !== 'Tab' || ! isOpen() ) {
					return;
				}
				const focusables = panel.querySelectorAll(
					'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
				);
				if ( ! focusables.length ) {
					return;
				}
				const first = focusables[0];
				const last = focusables[ focusables.length - 1 ];
				if ( event.shiftKey && document.activeElement === first ) {
					event.preventDefault();
					last.focus();
				} else if ( ! event.shiftKey && document.activeElement === last ) {
					event.preventDefault();
					first.focus();
				}
			} );
		}

		// Deep-link + auto-open are front-end only (they would fight editing).
		if ( ! editorMode ) {
			if ( hashId ) {
				const checkHash = () => {
					if ( window.location.hash.replace( '#', '' ) === hashId ) {
						open();
					}
				};
				window.addEventListener( 'hashchange', checkHash );
				checkHash();
			}

			if ( openOnLoad ) {
				window.setTimeout( open, 200 );
			}
		}
	};

	const run = ( root, editorMode ) => {
		const nodes = ( api && typeof api.getNodes === 'function' )
			? api.getNodes( root, SELECTOR )
			: Array.from( ( root || document ).querySelectorAll( SELECTOR ) );
		nodes.forEach( ( node ) => setup( node, editorMode ) );
	};

	if ( api && typeof api.register === 'function' ) {
		api.register( 'animated-off-canvas', ( root, options = {} ) => run( root, !! options.editorMode ) );
	} else if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', () => run( document, false ) );
	} else {
		run( document, false );
	}
})();
