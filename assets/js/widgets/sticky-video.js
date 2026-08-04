(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	// --- External player APIs, loaded once and shared across widgets ---------

	let ytPromise = null;
	const loadYouTube = () => {
		if (window.YT && window.YT.Player) {
			return Promise.resolve();
		}
		if (ytPromise) {
			return ytPromise;
		}
		ytPromise = new Promise((resolve) => {
			const previous = window.onYouTubeIframeAPIReady;
			window.onYouTubeIframeAPIReady = () => {
				if (typeof previous === 'function') {
					previous();
				}
				resolve();
			};
			const script = document.createElement('script');
			script.src = 'https://www.youtube.com/iframe_api';
			document.head.appendChild(script);
		});
		return ytPromise;
	};

	let vimeoPromise = null;
	const loadVimeo = () => {
		if (window.Vimeo && window.Vimeo.Player) {
			return Promise.resolve();
		}
		if (vimeoPromise) {
			return vimeoPromise;
		}
		vimeoPromise = new Promise((resolve, reject) => {
			const script = document.createElement('script');
			script.src = 'https://player.vimeo.com/api/player.js';
			script.onload = () => resolve();
			script.onerror = reject;
			document.head.appendChild(script);
		});
		return vimeoPromise;
	};

	// --- Per-widget setup ----------------------------------------------------

	const setup = (el) => {
		if (el.dataset.eapSvBound === 'true') {
			return;
		}
		el.dataset.eapSvBound = 'true';

		if (el.dataset.eapSticky === 'off') {
			return; // Sticky disabled — nothing to observe.
		}

		const source = el.dataset.eapSource || 'youtube';
		const floatWhenPlaying = el.dataset.eapFloatPlaying !== 'no';

		const state = {
			playing: false,
			outOfView: false,
			dismissed: false,
			active: false,
		};

		let pausePlayer = () => {};

		const render = () => {
			el.classList.toggle('is-sticky', state.active);
		};

		// Decide when to START or STOP floating. The float is LATCHED: once it is
		// floating it stays — even if the video is paused — until it scrolls back
		// into view or the close button is used. The "only while playing" option
		// only gates whether it may START floating.
		const evaluate = () => {
			if (state.dismissed || !state.outOfView) {
				state.active = false;
			} else if (!state.active) {
				if (!floatWhenPlaying || state.playing) {
					state.active = true;
				}
			}
			render();
		};

		const onPlay = () => {
			state.playing = true;
			state.dismissed = false;
			evaluate();
		};
		const onPause = () => {
			// Pausing keeps the sticky player visible; it stays until closed or
			// scrolled back into view.
			state.playing = false;
			evaluate();
		};

		// Player adapter per source, wiring play/pause state + a pause() control.
		if (source === 'hosted') {
			const video = el.querySelector('video');
			if (video) {
				video.addEventListener('play', onPlay);
				video.addEventListener('playing', onPlay);
				video.addEventListener('pause', onPause);
				video.addEventListener('ended', onPause);
				pausePlayer = () => video.pause();
			}
		} else if (source === 'youtube') {
			const iframe = el.querySelector('iframe');
			if (iframe) {
				loadYouTube().then(() => {
					const player = new window.YT.Player(iframe, {
						events: {
							onStateChange: (event) => {
								const YT = window.YT;
								if (event.data === YT.PlayerState.PLAYING) {
									onPlay();
								} else if (
									event.data === YT.PlayerState.PAUSED
									|| event.data === YT.PlayerState.ENDED
								) {
									onPause();
								}
							},
						},
					});
					pausePlayer = () => {
						try {
							player.pauseVideo();
						} catch (e) {}
					};
				}).catch(() => {});
			}
		} else if (source === 'vimeo') {
			const iframe = el.querySelector('iframe');
			if (iframe) {
				loadVimeo().then(() => {
					const player = new window.Vimeo.Player(iframe);
					player.on('play', onPlay);
					player.on('playing', onPlay);
					player.on('pause', onPause);
					player.on('ended', onPause);
					pausePlayer = () => {
						player.pause().catch(() => {});
					};
				}).catch(() => {});
			}
		}

		// Float when the inline video leaves the viewport, un-float on return.
		if ('IntersectionObserver' in window) {
			const observer = new IntersectionObserver((entries) => {
				entries.forEach((entry) => {
					state.outOfView = !entry.isIntersecting;
					if (!state.outOfView) {
						// Back in view — clear any dismissal so it can float again.
						state.dismissed = false;
					}
					evaluate();
				});
			}, { threshold: 0 });
			observer.observe(el);
		}

		// Close button: pause + dismiss the floating player.
		const closeBtn = el.querySelector('[data-eap-sv-close]');
		if (closeBtn) {
			const doClose = (event) => {
				event.preventDefault();
				state.dismissed = true;
				state.active = false;
				render();
				pausePlayer();
			};
			closeBtn.addEventListener('click', doClose);
			closeBtn.addEventListener('keydown', (event) => {
				if (event.key === 'Enter' || event.key === ' ') {
					doClose(event);
				}
			});
		}
	};

	api.register('sticky-video', (root, { editorMode = false } = {}) => {
		// Don't float inside the editor — it would cover the canvas. The video
		// still renders normally for previewing.
		if (editorMode) {
			return;
		}
		api.getNodes(root, '[data-eap-sticky-video]').forEach(setup);
	});
})();
