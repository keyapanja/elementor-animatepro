/* global EAPFrontend, Swiper */
(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const ROOT_SELECTOR = '[data-eap-ats-root]';

	/* ----------------------------------------------------------------- *
	 * Popup handling
	 * ----------------------------------------------------------------- */
	let activePopup = null;
	let activeTrigger = null;

	const closePopup = () => {
		if (!activePopup) {
			return;
		}
		activePopup.classList.remove('is-open');
		activePopup.setAttribute('aria-hidden', 'true');

		// Pause any video playing inside the popup.
		activePopup.querySelectorAll('video').forEach((video) => {
			try {
				video.pause();
				video.currentTime = 0;
			} catch (e) { /* noop */ }
		});

		document.body.classList.remove('eap-ats-popup-open');

		if (activeTrigger && typeof activeTrigger.focus === 'function') {
			activeTrigger.focus();
		}

		activePopup = null;
		activeTrigger = null;
	};

	const openPopup = (popup, trigger) => {
		if (!popup) {
			return;
		}
		if (activePopup) {
			closePopup();
		}
		popup.classList.add('is-open');
		popup.setAttribute('aria-hidden', 'false');
		document.body.classList.add('eap-ats-popup-open');
		activePopup = popup;
		activeTrigger = trigger || null;

		// Auto-play video in the popup (with audio). Use a microtask to let
		// the browser apply display:flex before .play() — some browsers reject
		// .play() on a still-display:none element.
		window.requestAnimationFrame(() => {
			popup.querySelectorAll('video').forEach((video) => {
				try {
					// Reset to start so reopens play from t=0.
					video.currentTime = 0;
					video.muted = false;
					const p = video.play();
					if (p && typeof p.catch === 'function') {
						p.catch(() => { /* user-gesture required; controls still work */ });
					}
				} catch (e) { /* noop */ }
			});

			// Focus the close button for keyboard users.
			const closeBtn = popup.querySelector('[data-eap-ats-close]');
			if (closeBtn && typeof closeBtn.focus === 'function') {
				closeBtn.focus({ preventScroll: true });
			}
		});
	};

	// Global keyboard handler — Esc closes any open popup.
	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && activePopup) {
			closePopup();
		}
	});

	/* ----------------------------------------------------------------- *
	 * Per-widget init
	 * ----------------------------------------------------------------- */

	const initSwiperFor = (slider, widget) => {
		if (typeof window.Swiper === 'undefined') {
			return null;
		}

		let settings = {};
		try {
			settings = JSON.parse(slider.dataset.eapAts || '{}');
		} catch (e) {
			settings = {};
		}

		const isBrandQuote = widget.classList.contains('eap-adv-testimonial-slider--brand-quote');

		const shell = slider.closest('.eap-adv-testimonial-slider__shell');
		const pagination = widget.querySelector('.eap-adv-testimonial-slider__pagination');
		const prevEl = shell ? shell.querySelector('.eap-adv-testimonial-slider__arrow--prev') : null;
		const nextEl = shell ? shell.querySelector('.eap-adv-testimonial-slider__arrow--next') : null;

		const realSlides = Array.from(slider.querySelectorAll('.swiper-slide')).filter(
			(s) => !s.classList.contains('swiper-slide-duplicate')
		);
		const slideCount = realSlides.length;

		// Brand Quote always shows exactly one slide at a time with a crossfade.
		// The "card" is the entire side-by-side editorial layout, not a grid.
		const desktop = isBrandQuote ? 1 : Math.max(parseInt(settings.slidesDesktop, 10) || 3, 1);
		const tablet  = isBrandQuote ? 1 : Math.max(parseInt(settings.slidesTablet, 10) || 2, 1);
		const mobile  = isBrandQuote ? 1 : Math.max(parseInt(settings.slidesMobile, 10) || 1, 1);
		const maxPerView = Math.max(desktop, tablet, mobile);
		const shouldLoop = !!settings.loop && slideCount > 1 && slideCount > maxPerView;
		const loopedSlides = shouldLoop ? Math.min(slideCount, maxPerView + 1) : 0;

		const config = {
			slidesPerView: mobile,
			spaceBetween: isBrandQuote ? 0 : (settings.spaceBetween || 24),
			speed: settings.speed || 650,
			loop: shouldLoop,
			rewind: !shouldLoop && !!settings.loop && slideCount > 1,
			loopedSlides,
			loopAdditionalSlides: loopedSlides,
			allowTouchMove: settings.allowTouchMove !== false,
			roundLengths: true,
			centerInsufficientSlides: !shouldLoop,
			observer: true,
			observeParents: true,
			observeSlideChildren: true,
			watchOverflow: true,
			updateOnWindowResize: true,
			resizeObserver: true,
			breakpoints: {
				768: { slidesPerView: tablet },
				1025: { slidesPerView: desktop }
			}
		};

		if (isBrandQuote) {
			config.effect = 'fade';
			config.fadeEffect = { crossFade: true };
		}

		if (settings.navigation && prevEl && nextEl) {
			config.navigation = { prevEl, nextEl };
		}

		if (settings.pagination && pagination) {
			config.pagination = {
				el: pagination,
				clickable: true,
				type: settings.paginationType || 'bullets'
			};
		}

		if (settings.autoplay) {
			config.autoplay = {
				delay: settings.autoplayDelay || 3500,
				disableOnInteraction: false,
				pauseOnMouseEnter: !!settings.pauseOnHover
			};
		}

		return new window.Swiper(slider, config);
	};

	const armBackgroundVideos = (widget) => {
		// Some browsers stall background <video autoplay> until layout settles.
		// Kick them along once on init.
		widget.querySelectorAll('.eap-adv-testimonial-slide__bg-video').forEach((video) => {
			video.muted = true;
			video.loop = true;
			video.playsInline = true;
			const p = video.play();
			if (p && typeof p.catch === 'function') {
				p.catch(() => { /* autoplay blocked — that's OK, fallback poster shows */ });
			}
		});
	};

	const checkClampOverflow = (textEl, btn) => {
		if (!textEl || !btn) {
			return;
		}
		// Use scrollHeight vs clientHeight to detect overflow caused by line-clamp.
		const overflows = textEl.scrollHeight - 1 > textEl.clientHeight;
		if (overflows) {
			btn.hidden = false;
		} else {
			btn.hidden = true;
		}
	};

	const armReadMoreButtons = (widget) => {
		const slides = widget.querySelectorAll('.eap-adv-testimonial-slider__slide:not(.swiper-slide-duplicate)');
		slides.forEach((slide) => {
			const text = slide.querySelector('.eap-adv-testimonial-slide__text');
			const btn = slide.querySelector('.eap-adv-testimonial-slide__read-more');
			if (!text || !btn) {
				return;
			}
			checkClampOverflow(text, btn);
		});

		// Recompute on resize (line-count can change as breakpoints shift the
		// container width).
		window.addEventListener('resize', () => {
			slides.forEach((slide) => {
				const text = slide.querySelector('.eap-adv-testimonial-slide__text');
				const btn = slide.querySelector('.eap-adv-testimonial-slide__read-more');
				checkClampOverflow(text, btn);
			});
		});
	};

	const bindPopupTriggers = (widget) => {
		// Use event delegation so Swiper clone slides also fire the right popup.
		widget.addEventListener('click', (event) => {
			const opener = event.target.closest('[data-eap-ats-open]');
			if (opener && widget.contains(opener)) {
				event.preventDefault();
				const id = opener.getAttribute('data-eap-ats-open');
				const popup = widget.querySelector('#' + CSS.escape(id));
				if (popup) {
					openPopup(popup, opener);
				}
				return;
			}

			const closer = event.target.closest('[data-eap-ats-close]');
			if (closer && widget.contains(closer)) {
				event.preventDefault();
				closePopup();
			}
		});
	};

	const initWidget = (widget) => {
		if (widget.dataset.eapAtsBound === '1') {
			return;
		}
		widget.dataset.eapAtsBound = '1';

		const slider = widget.querySelector('.eap-adv-testimonial-slider__swiper');
		if (!slider) {
			return;
		}

		const instance = initSwiperFor(slider, widget);

		// "Warm up" Swiper so the very first slide change doesn't glitch.
		// Right after construction:
		//   - Swiper's slide-shadow overlay elements don't exist yet (they're
		//     created lazily during the first transition)
		//   - CSS `transition` properties on slides haven't been computed by
		//     the browser (no transition has actually run)
		//   - The compositor layers for transformed slides haven't been
		//     allocated
		// The first user-triggered slide change pays the cost of all that
		// setup, which appears as the visible "first-time glitch". We fix
		// this by running a SILENT programmatic slide cycle at speed 0 —
		// Swiper's full transition pipeline executes (creating overlays,
		// applying inline transforms, triggering CSS transitions) but with
		// zero duration so nothing is visible. After this, the first real
		// user-triggered transition behaves identically to all subsequent
		// ones.
		const warmUpSwiper = () => {
			if (!instance || typeof instance.update !== 'function') {
				return;
			}
			try {
				// Force layout/style recalc on every slide so the browser
				// has computed `transition` properties before we run the
				// silent cycle.
				slider.querySelectorAll('.swiper-slide').forEach((s) => {
					/* eslint-disable-next-line no-unused-expressions */
					s.offsetHeight;
				});

				instance.update();

				// Speed = 0 → instant, no visible animation. runCallbacks =
				// false → don't fire slideChange so background-video pause
				// logic and any user-defined hooks stay silent. We cycle one
				// step forward and one step back so we end up exactly where
				// we started.
				const startIdx = instance.activeIndex;
				instance.slideTo(startIdx + 1, 0, false);
				instance.slideTo(startIdx, 0, false);

				instance.update();
			} catch (e) { /* noop */ }
		};

		window.requestAnimationFrame(warmUpSwiper);
		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(warmUpSwiper).catch(() => {});
		}
		if (document.readyState !== 'complete') {
			window.addEventListener('load', warmUpSwiper, { once: true });
		}

		armBackgroundVideos(widget);
		// Defer the clamp check until after layout has settled so scrollHeight
		// reflects the real wrapped lines.
		window.requestAnimationFrame(() => armReadMoreButtons(widget));
		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(() => armReadMoreButtons(widget)).catch(() => {});
		}

		bindPopupTriggers(widget);

		// Swiper sometimes hides duplicate-slide content via .eap-motion-style
		// gating in the parent. We don't use that here, but we do want clone
		// slides to fire popups — clones inherit data-eap-ats-open attributes
		// so delegation handles them naturally.
		if (instance && typeof instance.on === 'function') {
			instance.on('slideChange', () => {
				// Pause background videos in non-active slides to save CPU.
				widget.querySelectorAll('.eap-adv-testimonial-slide__bg-video').forEach((video) => {
					const slide = video.closest('.swiper-slide');
					if (!slide) return;
					if (slide.classList.contains('swiper-slide-active') || slide.classList.contains('swiper-slide-duplicate-active')) {
						const p = video.play();
						if (p && typeof p.catch === 'function') {
							p.catch(() => {});
						}
					} else {
						try { video.pause(); } catch (e) { /* noop */ }
					}
				});
			});
		}

		/* --------------------------------------------------------------- *
		 * Brand Quote specific behaviour
		 *   1) Click anywhere on the card advances to the next slide.
		 *   2) CSS slide-in animations on .swiper-slide-active are replayed
		 *      on every slide change (which CSS alone can't do, because the
		 *      `animation-name` is unchanged so the keyframes don't restart).
		 * --------------------------------------------------------------- */
		const isBrandQuote = widget.classList.contains('eap-adv-testimonial-slider--brand-quote');
		if (isBrandQuote && instance) {
			// Click-to-advance — ignore clicks that originated on real
			// interactive controls so links/buttons inside slides still work.
			widget.addEventListener('click', (event) => {
				if (!event.target.closest('[data-eap-tbq-advance]')) {
					return;
				}
				if (event.target.closest('a, button, [data-eap-ats-open], [data-eap-ats-close], .swiper-pagination, .swiper-pagination-bullet, .eap-adv-testimonial-slider__arrow')) {
					return;
				}
				event.preventDefault();
				try {
					instance.slideNext();
				} catch (e) { /* noop */ }
			});

			// Animation retrigger. Toggle `is-replaying` for one frame so the
			// browser drops the running animation, then remove it so the
			// `swiper-slide-active` rule re-evaluates with a fresh state.
			const retriggerAnimations = () => {
				const slides = slider.querySelectorAll('.swiper-slide-active, .swiper-slide-duplicate-active');
				slides.forEach((s) => {
					s.classList.add('is-replaying');
				});
				// Force a reflow so the animation:none takes effect synchronously.
				/* eslint-disable-next-line no-unused-expressions */
				slider.offsetHeight;
				window.requestAnimationFrame(() => {
					slides.forEach((s) => {
						s.classList.remove('is-replaying');
					});
				});
			};

			instance.on('slideChangeTransitionStart', retriggerAnimations);
			instance.on('slideChange', retriggerAnimations);
		}
	};

	const initAll = (root) => {
		if (!root) {
			return;
		}
		api.getNodes(root, ROOT_SELECTOR).forEach(initWidget);
	};

	api.register('advanced-testimonial-slider', (root) => initAll(root));
})();
