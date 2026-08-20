(() => {
	const api = window.EAPFrontend;
	const KEY = 'eap_read_later';
	const subscribers = [];

	/* --- Shared store (localStorage, array of post IDs, most-recent first) --- */

	const read = () => {
		try {
			const arr = JSON.parse( localStorage.getItem( KEY ) || '[]' );
			return Array.isArray( arr ) ? arr.filter( ( n ) => typeof n === 'number' && n > 0 ) : [];
		} catch ( e ) {
			return [];
		}
	};

	const notify = ( arr ) => subscribers.forEach( ( fn ) => {
		try {
			fn( arr );
		} catch ( e ) {}
	} );

	const persist = ( arr ) => {
		try {
			localStorage.setItem( KEY, JSON.stringify( arr ) );
		} catch ( e ) {}
	};

	const write = ( arr ) => {
		persist( arr );
		notify( arr );
	};

	const toggle = ( id ) => {
		const arr = read();
		const i = arr.indexOf( id );
		if ( i === -1 ) {
			arr.unshift( id );
		} else {
			arr.splice( i, 1 );
		}
		write( arr );
		return arr.indexOf( id ) !== -1;
	};

	const remove = ( id ) => {
		const arr = read();
		const i = arr.indexOf( id );
		if ( i !== -1 ) {
			arr.splice( i, 1 );
			write( arr );
		}
	};

	const clearAll = () => write( [] );

	const subscribe = ( fn ) => subscribers.push( fn );

	// Keep multiple tabs / windows in sync.
	window.addEventListener( 'storage', ( e ) => {
		if ( e.key === KEY ) {
			notify( read() );
		}
	} );

	/* --- Button mode: a save toggle for the current post --- */

	const initButton = ( root, editorMode ) => {
		if ( root.dataset.eapRlBound === '1' ) {
			return;
		}
		root.dataset.eapRlBound = '1';

		const btn = root.querySelector( '.eap-read-later__button' );
		if ( ! btn ) {
			return;
		}

		const id = parseInt( btn.getAttribute( 'data-post' ), 10 );
		if ( ! id ) {
			return;
		}

		const labelEl = btn.querySelector( '.eap-read-later__label' );
		const paint = ( saved ) => {
			btn.classList.toggle( 'is-saved', saved );
			btn.setAttribute( 'aria-pressed', saved ? 'true' : 'false' );
			if ( labelEl ) {
				labelEl.textContent = saved
					? ( btn.getAttribute( 'data-label-saved' ) || '' )
					: ( btn.getAttribute( 'data-label-save' ) || '' );
			}
		};

		if ( editorMode ) {
			// Preview only: toggle the visual state, never persist.
			let saved = false;
			paint( false );
			btn.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				saved = ! saved;
				paint( saved );
			} );
			return;
		}

		paint( read().indexOf( id ) !== -1 );
		subscribe( ( arr ) => paint( arr.indexOf( id ) !== -1 ) );
		btn.addEventListener( 'click', ( e ) => {
			e.preventDefault();
			toggle( id );
		} );
	};

	/* --- List mode: the visitor's saved posts, fetched as server cards --- */

	const initList = ( root, editorMode ) => {
		// The editor shows a server-rendered sample; leave it untouched.
		if ( editorMode || root.dataset.eapRlBound === '1' ) {
			return;
		}
		root.dataset.eapRlBound = '1';

		const list = root.querySelector( '.eap-read-later__list' );
		if ( ! list ) {
			return;
		}

		const grid = list.querySelector( '.eap-read-later__grid' );
		const countEls = root.querySelectorAll( '.eap-read-later__count' );
		const ajaxUrl = list.getAttribute( 'data-ajax-url' ) || '';
		const nonce = list.getAttribute( 'data-nonce' ) || '';
		const display = list.getAttribute( 'data-display' ) || '{}';
		let loading = false;

		const setCount = ( n ) => countEls.forEach( ( el ) => {
			el.textContent = String( n );
		} );

		const wireRemoves = () => {
			grid.querySelectorAll( '.eap-read-later__remove' ).forEach( ( b ) => {
				b.addEventListener( 'click', ( e ) => {
					e.preventDefault();
					const id = parseInt( b.getAttribute( 'data-post' ), 10 );
					if ( id ) {
						remove( id );
					}
				} );
			} );
		};

		const render = ( arr ) => {
			if ( ! arr.length ) {
				root.classList.add( 'is-empty' );
				grid.innerHTML = '';
				setCount( 0 );
				return;
			}
			if ( loading ) {
				return;
			}
			loading = true;
			root.classList.remove( 'is-empty' );
			root.classList.add( 'is-loading' );

			const body = new URLSearchParams();
			body.set( 'action', 'eap_read_later' );
			body.set( 'nonce', nonce );
			body.set( 'ids', JSON.stringify( arr ) );
			body.set( 'display', display );

			window.fetch( ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body: body.toString(),
			} )
				.then( ( res ) => ( res.ok ? res.json() : null ) )
				.then( ( json ) => {
					if ( ! json || ! json.success || ! json.data ) {
						return;
					}
					grid.innerHTML = json.data.html || '';
					wireRemoves();
					const valid = Array.isArray( json.data.valid_ids ) ? json.data.valid_ids : arr;
					setCount( valid.length );
					if ( ! valid.length ) {
						root.classList.add( 'is-empty' );
					}
					// Prune saved IDs that are no longer published (silent: the grid
					// already shows the valid set, so no re-render is needed).
					if ( valid.length !== arr.length ) {
						persist( valid );
					}
				} )
				.catch( () => {} )
				.then( () => {
					loading = false;
					root.classList.remove( 'is-loading' );
				} );
		};

		const clearBtn = root.querySelector( '.eap-read-later__clear' );
		if ( clearBtn ) {
			clearBtn.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				clearAll();
			} );
		}

		subscribe( render );
		render( read() );
	};

	/* --- Wiring --- */

	const setup = ( root, editorMode ) => {
		if ( root.classList && root.classList.contains( 'eap-read-later--button' ) ) {
			initButton( root, editorMode );
		} else if ( root.classList && root.classList.contains( 'eap-read-later--list' ) ) {
			initList( root, editorMode );
		}
	};

	const run = ( root, editorMode ) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes( root, '.eap-read-later' )
			: Array.from( ( root || document ).querySelectorAll( '.eap-read-later' ) );
		nodes.forEach( ( node ) => setup( node, editorMode ) );
	};

	if ( api && typeof api.register === 'function' ) {
		api.register( 'posts-read-later', ( root, options = {} ) => run( root, !! options.editorMode ) );
	} else if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', () => run( document, false ) );
	} else {
		run( document, false );
	}
})();
