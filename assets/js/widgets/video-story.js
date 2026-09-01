(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '.eap-video-story__item[data-eap-vs]';

	// Every initialised card registers a stopper so only one video is ever
	// previewing / playing at a time.
	const registry = [];
	const stopOthers = ( current ) => registry.forEach( ( entry ) => {
		if ( entry.item !== current ) {
			entry.stop();
		}
	} );

	const prefersReducedMotion = () =>
		window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	const formatTime = ( seconds ) => {
		seconds = Math.max( 0, Math.round( seconds ) );
		const m = Math.floor( seconds / 60 );
		const s = seconds % 60;
		return m + ':' + ( s < 10 ? '0' + s : String( s ) );
	};

	const setup = ( item, editorMode ) => {
		if ( item.dataset.eapVsBound === '1' ) {
			return;
		}
		item.dataset.eapVsBound = '1';

		const video = item.querySelector( '.eap-video-story__video' );
		if ( ! video ) {
			return;
		}

		const wrapper = item.closest( '.eap-video-story' );
		const hoverEnabled = !! ( wrapper && wrapper.classList.contains( 'eap-video-story--hover' ) );
		const playBtn = item.querySelector( '.eap-video-story__play' );
		const closeBtn = item.querySelector( '.eap-video-story__close' );
		const durationEl = item.querySelector( '.eap-video-story__duration' );

		let sound = false;

		// Duration label from the video metadata.
		if ( durationEl ) {
			const paintDuration = () => {
				if ( isFinite( video.duration ) && video.duration > 0 ) {
					durationEl.textContent = formatTime( video.duration );
				}
			};
			if ( video.readyState >= 1 ) {
				paintDuration();
			} else {
				video.addEventListener( 'loadedmetadata', paintDuration, { once: true } );
			}
		}

		const safePlay = () => {
			const p = video.play();
			if ( p && typeof p.catch === 'function' ) {
				p.catch( () => {} );
			}
		};

		const stopPreview = () => {
			item.classList.remove( 'is-previewing' );
			if ( ! sound ) {
				video.pause();
				try {
					video.currentTime = 0;
				} catch ( e ) {}
			}
		};

		const exitSound = () => {
			sound = false;
			item.classList.remove( 'is-playing' );
			video.pause();
			video.controls = false;
			video.muted = true;
			video.loop = true;
			try {
				video.currentTime = 0;
			} catch ( e ) {}
		};

		const enterSound = () => {
			stopOthers( item );
			sound = true;
			item.classList.remove( 'is-previewing' );
			item.classList.add( 'is-playing' );
			video.muted = false;
			video.loop = false;
			video.controls = true;
			try {
				video.currentTime = 0;
			} catch ( e ) {}
			safePlay();
		};

		// Hover preview (muted). Desktop + motion-OK + not in the editor.
		if ( hoverEnabled && ! editorMode ) {
			item.addEventListener( 'mouseenter', () => {
				if ( sound || prefersReducedMotion() ) {
					return;
				}
				stopOthers( item );
				video.muted = true;
				video.loop = true;
				try {
					video.currentTime = 0;
				} catch ( e ) {}
				item.classList.add( 'is-previewing' );
				safePlay();
			} );
			item.addEventListener( 'mouseleave', stopPreview );
		}

		if ( playBtn ) {
			playBtn.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				e.stopPropagation();
				enterSound();
			} );
		}

		if ( closeBtn ) {
			closeBtn.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				e.stopPropagation();
				exitSound();
			} );
		}

		video.addEventListener( 'ended', () => {
			if ( sound ) {
				exitSound();
			}
		} );

		registry.push( {
			item,
			stop: () => {
				if ( sound ) {
					exitSound();
				} else {
					stopPreview();
				}
			},
		} );
	};

	const run = ( root, editorMode ) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes( root, SELECTOR )
			: Array.from( ( root || document ).querySelectorAll( SELECTOR ) );
		nodes.forEach( ( node ) => setup( node, editorMode ) );
	};

	if ( api && typeof api.register === 'function' ) {
		api.register( 'video-story', ( root, options = {} ) => run( root, !! options.editorMode ) );
	} else if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', () => run( document, false ) );
	} else {
		run( document, false );
	}
})();
