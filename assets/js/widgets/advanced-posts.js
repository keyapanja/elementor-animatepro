(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '.eap-advanced-posts[data-eap-advanced-posts]';

	const setup = ( root, editorMode ) => {
		// Filtering + AJAX pagination are front-end only.
		if ( editorMode || root.dataset.eapApBound === '1' ) {
			return;
		}
		root.dataset.eapApBound = '1';

		const grid = root.querySelector( '.eap-posts__grid' );
		if ( ! grid ) {
			return;
		}

		const ajaxUrl = root.dataset.ajaxUrl || '';
		const nonce = root.dataset.nonce || '';
		const mode = root.dataset.mode || 'none';

		let baseSpec;
		let display;
		try {
			baseSpec = JSON.parse( root.dataset.spec || '{}' );
			display = JSON.parse( root.dataset.display || '{}' );
		} catch ( e ) {
			return;
		}

		const filters = Array.from( root.querySelectorAll( '.eap-advanced-posts__filter' ) );
		const button = root.querySelector( '.eap-posts__load-more' );
		const sentinel = root.querySelector( '.eap-posts__sentinel' );

		// Active term = whichever tab is marked active on load (0 = "All").
		let activeTerm = 0;
		const activeBtn = root.querySelector( '.eap-advanced-posts__filter.is-active' );
		if ( activeBtn ) {
			activeTerm = parseInt( activeBtn.dataset.term, 10 ) || 0;
		}

		let page = parseInt( root.dataset.page, 10 ) || 1;
		let max = parseInt( root.dataset.max, 10 ) || 1;
		let loading = false;
		let done = page >= max;
		let observer = null;

		const currentSpec = () => Object.assign( {}, baseSpec, { filter_terms: activeTerm ? [ activeTerm ] : [] } );

		const post = ( nextPage ) => {
			const body = new URLSearchParams();
			body.set( 'action', 'eap_load_posts' );
			body.set( 'nonce', nonce );
			body.set( 'page', String( nextPage ) );
			body.set( 'spec', JSON.stringify( currentSpec() ) );
			body.set( 'display', JSON.stringify( display ) );
			return window.fetch( ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body: body.toString(),
			} ).then( ( res ) => ( res.ok ? res.json() : null ) );
		};

		const appendHtml = ( html ) => {
			const tmp = document.createElement( 'div' );
			tmp.innerHTML = html;
			const frag = document.createDocumentFragment();
			while ( tmp.firstChild ) {
				frag.appendChild( tmp.firstChild );
			}
			grid.appendChild( frag );
		};

		const setPagerVisible = ( visible ) => {
			if ( button ) {
				button.classList.toggle( 'hidden', ! visible );
			}
			if ( sentinel ) {
				sentinel.classList.toggle( 'hidden', ! visible );
			}
		};

		const ensureObserver = () => {
			if ( 'infinite' !== mode || ! sentinel ) {
				return;
			}
			if ( observer ) {
				observer.disconnect();
			}
			if ( done || ! ( 'IntersectionObserver' in window ) ) {
				return;
			}
			observer = new IntersectionObserver(
				( entries ) => entries.forEach( ( entry ) => {
					if ( entry.isIntersecting ) {
						loadNext();
					}
				} ),
				{ rootMargin: '200px' }
			);
			observer.observe( sentinel );
		};

		function loadNext() {
			if ( loading || done || 'none' === mode ) {
				return;
			}
			loading = true;
			root.classList.add( 'eap-posts--loading' );
			if ( button ) {
				button.classList.add( 'is-loading' );
			}
			post( page + 1 )
				.then( ( json ) => {
					if ( json && json.success && json.data ) {
						if ( json.data.html ) {
							appendHtml( json.data.html );
						}
						page += 1;
						const maxPages = parseInt( json.data.max_pages, 10 ) || max;
						if ( ! json.data.has_more || page >= maxPages ) {
							done = true;
							setPagerVisible( false );
							if ( observer ) {
								observer.disconnect();
							}
						}
					} else {
						done = true;
						setPagerVisible( false );
					}
				} )
				.catch( () => {} )
				.then( () => {
					loading = false;
					root.classList.remove( 'eap-posts--loading' );
					if ( button ) {
						button.classList.remove( 'is-loading' );
					}
				} );
		}

		const applyFilter = ( term, btn ) => {
			if ( loading || term === activeTerm ) {
				return;
			}
			activeTerm = term;
			filters.forEach( ( b ) => {
				const on = b === btn;
				b.classList.toggle( 'is-active', on );
				b.setAttribute( 'aria-selected', on ? 'true' : 'false' );
			} );

			loading = true;
			root.classList.add( 'is-filtering' );
			post( 1 )
				.then( ( json ) => {
					if ( json && json.success && json.data ) {
						grid.innerHTML = json.data.html || '';
						page = 1;
						max = parseInt( json.data.max_pages, 10 ) || 1;
						done = page >= max;
						setPagerVisible( 'none' !== mode && ! done );
						ensureObserver();
					}
				} )
				.catch( () => {} )
				.then( () => {
					loading = false;
					root.classList.remove( 'is-filtering' );
				} );
		};

		filters.forEach( ( btn ) => {
			btn.addEventListener( 'click', () => applyFilter( parseInt( btn.dataset.term, 10 ) || 0, btn ) );
		} );

		if ( 'load_more' === mode && button ) {
			button.addEventListener( 'click', loadNext );
			if ( done ) {
				setPagerVisible( false );
			}
		} else if ( 'infinite' === mode ) {
			if ( done ) {
				setPagerVisible( false );
			} else {
				ensureObserver();
			}
		}
	};

	const run = ( root, editorMode ) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes( root, SELECTOR )
			: Array.from( ( root || document ).querySelectorAll( SELECTOR ) );
		nodes.forEach( ( node ) => setup( node, editorMode ) );
	};

	if ( api && typeof api.register === 'function' ) {
		api.register( 'advanced-posts', ( root, options = {} ) => run( root, !! options.editorMode ) );
	} else if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', () => run( document, false ) );
	} else {
		run( document, false );
	}
})();
