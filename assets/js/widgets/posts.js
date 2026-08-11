(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '.eap-posts[data-eap-posts]';

	const setup = ( root, editorMode ) => {
		if ( editorMode || root.dataset.eapPostsBound === '1' ) {
			return;
		}
		root.dataset.eapPostsBound = '1';

		const grid = root.querySelector( '.eap-posts__grid' );
		if ( ! grid ) {
			return;
		}

		const mode = root.dataset.mode || 'load_more';
		const ajaxUrl = root.dataset.ajaxUrl || '';
		const nonce = root.dataset.nonce || '';
		const max = parseInt( root.dataset.max, 10 ) || 1;

		let spec;
		let display;
		try {
			spec = JSON.parse( root.dataset.spec || '{}' );
			display = JSON.parse( root.dataset.display || '{}' );
		} catch ( e ) {
			return;
		}

		let page = parseInt( root.dataset.page, 10 ) || 1;
		let loading = false;
		let done = page >= max;

		const button = root.querySelector( '.eap-posts__load-more' );
		const sentinel = root.querySelector( '.eap-posts__sentinel' );
		let observer = null;

		const finish = () => {
			done = true;
			if ( button ) {
				button.classList.add( 'hidden' );
			}
			if ( sentinel ) {
				sentinel.classList.add( 'hidden' );
			}
			if ( observer ) {
				observer.disconnect();
			}
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

		const loadNext = () => {
			if ( loading || done || ! ajaxUrl || ! nonce ) {
				return;
			}
			loading = true;
			root.classList.add( 'eap-posts--loading' );
			if ( button ) {
				button.classList.add( 'is-loading' );
			}

			const body = new URLSearchParams();
			body.set( 'action', 'eap_load_posts' );
			body.set( 'nonce', nonce );
			body.set( 'page', String( page + 1 ) );
			body.set( 'spec', JSON.stringify( spec ) );
			body.set( 'display', JSON.stringify( display ) );

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
						if ( json.data.html ) {
							appendHtml( json.data.html );
						}
						page += 1;
						root.dataset.page = String( page );
						const maxPages = parseInt( json.data.max_pages, 10 ) || max;
						if ( ! json.data.has_more || page >= maxPages ) {
							finish();
						}
					} else {
						finish();
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
		};

		if ( 'infinite' === mode && sentinel ) {
			if ( done ) {
				finish();
			} else if ( 'IntersectionObserver' in window ) {
				observer = new IntersectionObserver(
					( entries ) => {
						entries.forEach( ( entry ) => {
							if ( entry.isIntersecting ) {
								loadNext();
							}
						} );
					},
					{ rootMargin: '200px' }
				);
				observer.observe( sentinel );
			}
		} else if ( button ) {
			button.addEventListener( 'click', loadNext );
			if ( done ) {
				finish();
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
		api.register( 'posts', ( root, options = {} ) => run( root, !! options.editorMode ) );
	} else if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', () => run( document, false ) );
	} else {
		run( document, false );
	}
})();
