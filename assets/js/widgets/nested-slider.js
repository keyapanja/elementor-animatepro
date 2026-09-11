/*
 * Nested Slider — Swiper on the page, a plain strip in the editor.
 *
 * In the editor this does nothing: the slides stay a scroll-snap strip (see
 * the CSS) so Elementor's views are never moved or cloned under it.
 *
 * Loop clones: Swiper 8 duplicates slides with cloneNode(), which copies our
 * `data-eap-*-bound` flags — a widget of ours inside a clone would believe it
 * was already set up and never start. Each clone gets those flags cleared and
 * our modules plus Elementor's element handlers run over it.
 */
(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '[data-eap-ns]';
	const BOUND = /^data-eap-[a-z0-9-]+-bound$/;

	const reduced = () => (api && typeof api.prefersReducedMotion === 'function'
		? api.prefersReducedMotion()
		: window.matchMedia('(prefers-reduced-motion: reduce)').matches);

	const inEditor = (options) => !!(options && options.editorMode)
		|| document.body.classList.contains('elementor-editor-active');

	const pick = (obj, key, fallback) => (obj && obj[key] !== undefined && obj[key] !== null ? obj[key] : fallback);

	// A clone is a copy of an already-started slide; start what is inside it.
	const startClone = (slide) => {
		[slide, ...slide.querySelectorAll('*')].forEach((el) => {
			Array.from(el.attributes).forEach((attr) => {
				if (BOUND.test(attr.name)) {
					el.removeAttribute(attr.name);
				}
			});
		});

		if (api && typeof api.boot === 'function') {
			api.boot(slide);
		}

		const ef = window.elementorFrontend;
		if (ef && ef.elementsHandler && typeof ef.elementsHandler.runReadyTrigger === 'function' && window.jQuery) {
			slide.querySelectorAll('.elementor-element').forEach((node) => {
				ef.elementsHandler.runReadyTrigger(window.jQuery(node));
			});
		}
	};

	const startClones = (swiper) => {
		(swiper.slides || []).forEach((slide) => {
			if (slide.classList.contains('swiper-slide-duplicate') && slide.dataset.eapNsClone !== '1') {
				slide.dataset.eapNsClone = '1';
				startClone(slide);
			}
		});
	};

	const setup = (root, options) => {
		if (root.dataset.eapNsBound === '1' || inEditor(options)) {
			return;
		}
		if (typeof window.Swiper !== 'function') {
			// No Swiper: the strip stays, which is usable on its own.
			return;
		}
		root.dataset.eapNsBound = '1';

		let cfg = {};
		try {
			cfg = JSON.parse(root.getAttribute('data-eap-ns') || '{}');
		} catch (error) {
			cfg = {};
		}

		const stage = root.querySelector(':scope > .eap-ns__stage');
		const viewport = stage ? stage.querySelector(':scope > .eap-ns__viewport') : null;
		if (!viewport) {
			return;
		}

		const i18n = cfg.i18n || {};
		const fade = cfg.effect === 'fade';
		const still = reduced();
		const per = cfg.perView || {};
		const group = cfg.group || {};
		const gap = cfg.gap || {};
		const bp = cfg.bp || {};

		const at = (size) => ({
			slidesPerView: fade ? 1 : Number(pick(per, size, 1)),
			slidesPerGroup: fade ? 1 : Number(pick(group, size, 1)),
			spaceBetween: fade ? 0 : Number(pick(gap, size, 0))
		});

		const config = Object.assign(at('mobile'), {
			breakpoints: {
				[pick(bp, 'tablet', 768)]: at('tablet'),
				[pick(bp, 'desktop', 1025)]: at('desktop')
			},
			speed: still ? 0 : Number(pick(cfg, 'speed', 500)),
			loop: !!cfg.loop,
			centeredSlides: !!cfg.centered,
			autoHeight: !!cfg.autoHeight,
			grabCursor: !!cfg.grab,
			watchOverflow: true,
			observer: true,
			observeParents: true,
			roundLengths: true,
			init: false,
			keyboard: { enabled: !!cfg.keyboard, onlyInViewport: true },
			a11y: {
				enabled: true,
				prevSlideMessage: i18n.prev || 'Previous slide',
				nextSlideMessage: i18n.next || 'Next slide',
				paginationBulletMessage: i18n.goTo || 'Go to slide {{index}}',
				slideLabelMessage: i18n.slide || '{{index}} / {{slidesLength}}'
			}
		});

		if (config.loop) {
			// Clone every slide at each end, so any Slides Per View at any
			// breakpoint has enough copies to wrap cleanly.
			config.loopedSlides = viewport.querySelectorAll(':scope > .eap-ns__track > .swiper-slide').length;
		}

		if (fade) {
			config.effect = 'fade';
			config.fadeEffect = { crossFade: true };
		}

		const prev = stage.querySelector(':scope > .eap-ns__arrow--prev');
		const next = stage.querySelector(':scope > .eap-ns__arrow--next');
		if (prev && next) {
			config.navigation = { prevEl: prev, nextEl: next };
		}

		const pagination = root.querySelector(':scope > .eap-ns__pagination');
		if (pagination && cfg.pagination && cfg.pagination !== 'none') {
			config.pagination = { el: pagination, clickable: true, type: cfg.pagination };
		}

		const autoplay = !!cfg.autoplay && !still;
		if (autoplay) {
			config.autoplay = {
				delay: Number(pick(cfg, 'delay', 5000)),
				disableOnInteraction: !!cfg.pauseInteraction,
				pauseOnMouseEnter: !!cfg.pauseHover
			};
		}

		const swiper = new window.Swiper(viewport, config);
		swiper.on('init', () => startClones(swiper));
		swiper.on('breakpoint', () => startClones(swiper));
		swiper.init();

		/* --- Pausing -------------------------------------------------------- */

		const pause = stage.querySelector(':scope > .eap-ns__pause');
		let pausedByUser = false;
		let pausedByFocus = false;

		const running = () => !!(swiper.autoplay && swiper.autoplay.running);

		if (pause) {
			if (autoplay) {
				// Nothing to slide (every slide fits): Swiper locks, and a pause
				// button on a slider that cannot move would be a dead control.
				pause.hidden = !!swiper.isLocked;
				swiper.on('lock', () => {
					pause.hidden = true;
				});
				swiper.on('unlock', () => {
					pause.hidden = false;
				});
				pause.addEventListener('click', () => {
					pausedByUser = !pausedByUser;
					if (pausedByUser) {
						swiper.autoplay.stop();
					} else {
						swiper.autoplay.start();
					}
					pause.setAttribute('aria-pressed', pausedByUser ? 'true' : 'false');
					pause.setAttribute('aria-label', pausedByUser ? (i18n.play || 'Play') : (i18n.pause || 'Pause'));
				});
			} else {
				// Reduced motion switched autoplay off; there is nothing to pause.
				pause.hidden = true;
			}
		}

		// Keyboard and screen-reader users are reading a slide: hold still
		// while focus is inside, resume when it leaves (unless paused by hand).
		if (autoplay) {
			root.addEventListener('focusin', () => {
				if (running()) {
					swiper.autoplay.stop();
					pausedByFocus = true;
				}
			});
			root.addEventListener('focusout', (event) => {
				if (pausedByFocus && !root.contains(event.relatedTarget)) {
					pausedByFocus = false;
					if (!pausedByUser) {
						swiper.autoplay.start();
					}
				}
			});
		}

		root.eapNestedSlider = swiper;
		root.classList.add('is-ready');
	};

	const run = (root, options) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((node) => setup(node, options));
	};

	if (api && typeof api.register === 'function') {
		api.register('nested-slider', (root, options) => run(root, options));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document));
	} else {
		run(document);
	}
})();
