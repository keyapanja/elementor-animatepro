(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '.eap-post-reactions';

	const safeGet = ( key ) => {
		try {
			return window.localStorage.getItem( key );
		} catch ( e ) {
			return null;
		}
	};
	const safeSet = ( key, value ) => {
		try {
			window.localStorage.setItem( key, value );
		} catch ( e ) {} // eslint-disable-line no-empty
	};
	const safeRemove = ( key ) => {
		try {
			window.localStorage.removeItem( key );
		} catch ( e ) {} // eslint-disable-line no-empty
	};

	const setup = ( root, editorMode ) => {
		if ( ! root.hasAttribute( 'data-eap-reactions' ) || root.dataset.eapReactBound === '1' ) {
			return;
		}
		root.dataset.eapReactBound = '1';

		const postId = root.dataset.post || '0';
		const ajaxUrl = root.dataset.ajaxUrl || '';
		const nonce = root.dataset.nonce || '';
		const storageKey = 'eap_reacted_' + postId;
		const buttons = Array.from( root.querySelectorAll( '.eap-post-reactions__btn' ) );

		if ( ! buttons.length ) {
			return;
		}

		// The visitor's current reaction (front-end only; the editor shows a
		// neutral, non-interactive preview).
		let current = editorMode ? null : safeGet( storageKey );

		const applyActive = () => {
			buttons.forEach( ( btn ) => {
				btn.classList.toggle( 'is-active', current !== null && btn.dataset.reaction === current );
			} );
		};

		applyActive();

		if ( editorMode ) {
			return;
		}

		const countEl = ( reaction ) =>
			root.querySelector( '.eap-post-reactions__btn[data-reaction="' + reaction + '"] .eap-post-reactions__count' );

		const bump = ( reaction, delta ) => {
			const el = countEl( reaction );
			if ( ! el ) {
				return;
			}
			const next = Math.max( 0, ( parseInt( el.textContent, 10 ) || 0 ) + delta );
			el.textContent = String( next );
		};

		const syncCounts = ( counts ) => {
			buttons.forEach( ( btn ) => {
				const el = btn.querySelector( '.eap-post-reactions__count' );
				if ( el ) {
					const key = btn.dataset.reaction;
					el.textContent = String( counts && counts[ key ] ? counts[ key ] : 0 );
				}
			} );
		};

		const send = ( reaction, op, from ) => {
			if ( ! ajaxUrl || ! nonce ) {
				return;
			}
			const body = new URLSearchParams();
			body.set( 'action', 'eap_react' );
			body.set( 'nonce', nonce );
			body.set( 'post_id', postId );
			body.set( 'reaction', reaction );
			body.set( 'op', op );
			if ( from ) {
				body.set( 'from', from );
			}

			window
				.fetch( ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
					body: body.toString(),
				} )
				.then( ( res ) => ( res.ok ? res.json() : null ) )
				.then( ( json ) => {
					if ( json && json.success && json.data && json.data.counts ) {
						syncCounts( json.data.counts );
					}
				} )
				.catch( () => {} );
		};

		buttons.forEach( ( btn ) => {
			btn.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				const reaction = btn.dataset.reaction;
				let op;
				let from = '';

				if ( current === reaction ) {
					op = 'remove';
				} else if ( current ) {
					op = 'switch';
					from = current;
				} else {
					op = 'add';
				}

				// Optimistic UI: adjust counts + active state immediately.
				if ( 'remove' === op ) {
					bump( reaction, -1 );
					current = null;
					safeRemove( storageKey );
				} else {
					bump( reaction, 1 );
					if ( 'switch' === op && from ) {
						bump( from, -1 );
					}
					current = reaction;
					safeSet( storageKey, reaction );
				}
				applyActive();

				send( reaction, op, from );
			} );
		} );
	};

	const run = ( root, editorMode ) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes( root, SELECTOR )
			: Array.from( ( root || document ).querySelectorAll( SELECTOR ) );
		nodes.forEach( ( node ) => setup( node, editorMode ) );
	};

	if ( api && typeof api.register === 'function' ) {
		api.register( 'post-reactions', ( root, options = {} ) => run( root, !! options.editorMode ) );
	} else if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', () => run( document, false ) );
	} else {
		run( document, false );
	}
})();
