(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '.eap-post-rating-form[data-eap-rating-form]';

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

	const setup = ( root, editorMode ) => {
		if ( root.dataset.eapRfBound === '1' ) {
			return;
		}
		root.dataset.eapRfBound = '1';

		const stars = Array.from( root.querySelectorAll( '.eap-post-rating-form__star' ) );
		if ( ! stars.length ) {
			return;
		}

		const postId = root.dataset.post || '0';
		const max = parseInt( root.dataset.max, 10 ) || stars.length;
		const ajaxUrl = root.dataset.ajaxUrl || '';
		const nonce = root.dataset.nonce || '';
		const storageKey = 'eap_rated_' + postId;

		const starsWrap = root.querySelector( '.eap-post-rating-form__stars' );
		const thanksEl = root.querySelector( '.eap-post-rating-form__thanks' );
		const statsEl = root.querySelector( '.eap-post-rating-form__stats' );
		const avgEl = root.querySelector( '.eap-post-rating-form__average' );
		const countEl = root.querySelector( '.eap-post-rating-form__count' );

		let selected = 0;
		let locked = false;

		const paint = ( value, cls ) => {
			stars.forEach( ( star ) => {
				star.classList.toggle( cls, parseInt( star.dataset.value, 10 ) <= value );
			} );
		};
		const clearClass = ( cls ) => stars.forEach( ( star ) => star.classList.remove( cls ) );

		const reflectSelected = () => {
			paint( selected, 'is-active' );
			stars.forEach( ( star ) => {
				star.setAttribute( 'aria-checked', String( parseInt( star.dataset.value, 10 ) === selected ) );
			} );
		};

		const showThanks = () => {
			if ( thanksEl ) {
				thanksEl.hidden = false;
			}
		};

		const updateStats = ( average, count ) => {
			if ( avgEl ) {
				avgEl.textContent = Math.floor( average ) === average ? String( average ) : Number( average ).toFixed( 1 );
			}
			if ( countEl ) {
				countEl.textContent = '(' + count + ' ' + ( 1 === count ? 'vote' : 'votes' ) + ')';
			}
			if ( statsEl ) {
				statsEl.hidden = false;
			}
		};

		// Restore a prior vote (front-end only).
		if ( ! editorMode ) {
			const prior = parseInt( safeGet( storageKey ), 10 );
			if ( prior > 0 ) {
				selected = Math.min( max, prior );
				locked = true;
			}
		}
		reflectSelected();
		if ( locked ) {
			root.classList.add( 'is-rated' );
			showThanks();
		}

		const submit = ( value ) => {
			if ( ! ajaxUrl || ! nonce ) {
				return;
			}
			const body = new URLSearchParams();
			body.set( 'action', 'eap_submit_rating' );
			body.set( 'nonce', nonce );
			body.set( 'post_id', postId );
			body.set( 'rating', String( value ) );

			window
				.fetch( ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
					body: body.toString(),
				} )
				.then( ( res ) => ( res.ok ? res.json() : null ) )
				.then( ( json ) => {
					if ( json && json.success && json.data ) {
						updateStats( json.data.average, json.data.count );
					}
				} )
				.catch( () => {} );
		};

		stars.forEach( ( star ) => {
			star.addEventListener( 'mouseenter', () => {
				if ( locked && ! editorMode ) {
					return;
				}
				paint( parseInt( star.dataset.value, 10 ), 'is-hover' );
			} );

			star.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				const value = parseInt( star.dataset.value, 10 );

				// Editor: preview the selection only — never submit or lock.
				if ( editorMode ) {
					selected = value;
					clearClass( 'is-hover' );
					reflectSelected();
					return;
				}

				if ( locked ) {
					return;
				}

				selected = value;
				locked = true;
				clearClass( 'is-hover' );
				reflectSelected();
				root.classList.add( 'is-rated' );
				showThanks();
				safeSet( storageKey, String( value ) );
				submit( value );
			} );
		} );

		if ( starsWrap ) {
			starsWrap.addEventListener( 'mouseleave', () => clearClass( 'is-hover' ) );
		}
	};

	const run = ( root, editorMode ) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes( root, SELECTOR )
			: Array.from( ( root || document ).querySelectorAll( SELECTOR ) );
		nodes.forEach( ( node ) => setup( node, editorMode ) );
	};

	if ( api && typeof api.register === 'function' ) {
		api.register( 'post-rating-form', ( root, options = {} ) => run( root, !! options.editorMode ) );
	} else if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', () => run( document, false ) );
	} else {
		run( document, false );
	}
})();
