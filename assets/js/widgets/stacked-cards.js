/* global EAPFrontend, gsap, ScrollTrigger */
(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '.eap-stacked-cards';

	const num = (value, fallback) => {
		const n = parseFloat(value);
		return isNaN(n) ? fallback : n;
	};

	// Kill any ScrollTriggers whose stacked-cards trigger has left the DOM.
	// Elementor's editor re-renders widgets on each edit, which would otherwise
	// leave orphan triggers (and pin-spacers) behind.
	const cleanupOrphans = () => {
		if (typeof window.ScrollTrigger === 'undefined') {
			return;
		}
		window.ScrollTrigger.getAll().forEach((trig) => {
			const t = trig.trigger;
			if (t && t.classList && t.classList.contains('eap-stacked-cards') && !document.body.contains(t)) {
				trig.kill();
			}
		});
	};

	const MIN_SCALE = 0.5;
	const MIN_BRIGHT = 0.4;

	const setupWidget = (widget) => {
		if (widget.dataset.eapScBound === '1') {
			return;
		}
		widget.dataset.eapScBound = '1';

		const stage = widget.querySelector('.eap-stacked-cards__stage');
		const cards = Array.from(widget.querySelectorAll('.eap-stacked-cards__card'));
		if (!stage || cards.length === 0) {
			return;
		}

		const N = cards.length;

		const cfg = {
			peek: num(widget.dataset.peek, 22),
			scaleStep: num(widget.dataset.scaleStep, 0.05),
			dim: num(widget.dataset.dim, 0),
			tilt: num(widget.dataset.tilt, 0),
			scrollLen: num(widget.dataset.scrollLength, 100),
			scrub: num(widget.dataset.scrub, 0.6),
			disableBelow: num(widget.dataset.disableBelow, 0),
		};

		const hasGsap = typeof window.gsap !== 'undefined';
		const hasST = hasGsap && typeof window.ScrollTrigger !== 'undefined';
		const reduced = !!(api && typeof api.prefersReducedMotion === 'function' && api.prefersReducedMotion());

		const canAnimate = () => {
			if (!hasST || reduced || N < 2) {
				return false;
			}
			if (cfg.disableBelow > 0 && window.innerWidth < cfg.disableBelow) {
				return false;
			}
			return true;
		};

		const depthY = (d) => -(d * cfg.peek);
		const depthScale = (d) => Math.max(MIN_SCALE, 1 - d * cfg.scaleStep);
		const depthBright = (d) => (cfg.dim > 0 ? Math.max(MIN_BRIGHT, 1 - d * cfg.dim) : 1);
		// Behind cards tilt in alternating directions by card index; front = 0.
		const tiltFor = (k) => (k % 2 === 0 ? 1 : -1) * cfg.tilt;

		// The Elementor widget wrapper is a flex item. Once the cards become
		// position:absolute (pinned mode) they add no in-flow size, so in a flex
		// container the wrapper shrink-wraps to min-content (~one word wide) and
		// every card wraps into a tall, unreadable sliver. Forcing that wrapper to
		// width:100% keeps the whole deck full width regardless of the container's
		// alignment. Falls back to our own root outside Elementor.
		const flexItem = widget.closest('.elementor-widget') || widget;

		// The stage Y at which an incoming card's TOP sits on the bottom edge of
		// the viewport while the (vertically-centered) deck is pinned. Recomputed
		// on every refresh and read live by the incoming tweens via a function
		// value + invalidateOnRefresh, so it stays correct across resizes.
		const enter = { y: 0 };
		let cardH = 0;

		// Measure the tallest card's natural content/setting height and cache it.
		// This MUST run in a stable state — never inside ScrollTrigger's
		// onRefreshInit, where the pin is mid-teardown and the measured width is
		// transient (which produced a wrongly-tall card).
		const measure = () => {
			flexItem.style.width = '100%';
			const prev = cards.map((c) => c.style.height);
			cards.forEach((c) => { c.style.height = 'auto'; });
			let h = 0;
			cards.forEach((c) => {
				if (c.offsetHeight > h) {
					h = c.offsetHeight;
				}
			});
			cards.forEach((c, i) => { c.style.height = prev[i]; });
			cardH = h;
		};

		// Apply the cached height, the viewport-derived entrance offset, and the
		// clip. Never re-measures, so it is safe to run on every refresh. The deck
		// is centered vertically, so a card's top rests at (viewport - cardH) / 2;
		// the viewport bottom is that plus a full viewport height => (viewport +
		// cardH) / 2, which is where an incoming card starts (its top on the
		// bottom edge of the screen). The clip is extended down to that edge so
		// off-screen cards stay hidden until they rise into view.
		const applyLayout = () => {
			flexItem.style.width = '100%';
			enter.y = Math.round((window.innerHeight + cardH) / 2);
			stage.style.height = cardH + 'px';
			cards.forEach((c) => { c.style.height = cardH + 'px'; });
			stage.style.clipPath = 'inset(-3000px 0px ' + (cardH - enter.y) + 'px 0px)';
		};

		const setInitial = () => {
			window.gsap.set(cards, { transformOrigin: '50% 0%' });
			cards.forEach((c, i) => window.gsap.set(c, { zIndex: i + 1 }));
			window.gsap.set(cards[0], { y: 0, scale: 1, rotation: 0, filter: 'brightness(1)' });
			for (let i = 1; i < N; i++) {
				window.gsap.set(cards[i], { y: enter.y, scale: 1, rotation: 0, filter: 'brightness(1)' });
			}
		};

		let tl = null;
		let built = false;

		const build = () => {
			if (built) {
				return;
			}
			built = true;
			widget.classList.add('is-pinned');
			if (api && typeof api.ensureScrollTrigger === 'function') {
				api.ensureScrollTrigger();
			}

			measure();
			applyLayout();
			setInitial();

			tl = window.gsap.timeline({
				scrollTrigger: {
					trigger: widget,
					start: 'center center',
					end: () => '+=' + Math.max(1, (N - 1) * window.innerHeight * (cfg.scrollLen / 100)),
					pin: true,
					pinSpacing: true,
					anticipatePin: 1,
					scrub: cfg.scrub > 0 ? cfg.scrub : true,
					invalidateOnRefresh: true,
					onRefreshInit: applyLayout,
				},
			});

			// One segment per transition: the incoming card rises from the bottom
			// of the viewport to the front, while every card already in the deck
			// recedes one level deeper (up by a peek + a touch smaller + dimmer).
			for (let t = 0; t < N - 1; t++) {
				tl.fromTo(cards[t + 1], { y: () => enter.y }, { y: 0, ease: 'none', duration: 1 }, t);
				for (let k = 0; k <= t; k++) {
					const d = t + 1 - k;
					tl.to(cards[k], {
						y: depthY(d),
						scale: depthScale(d),
						rotation: tiltFor(k),
						filter: 'brightness(' + depthBright(d) + ')',
						ease: 'none',
						duration: 1,
					}, t);
				}
			}

			window.ScrollTrigger.refresh();
		};

		const teardown = () => {
			if (!built) {
				return;
			}
			built = false;
			if (tl && tl.scrollTrigger) {
				tl.scrollTrigger.kill();
			}
			if (tl) {
				tl.kill();
			}
			tl = null;
			widget.classList.remove('is-pinned');
			if (window.gsap) {
				window.gsap.set(cards, { clearProps: 'all' });
			}
			cards.forEach((c) => { c.style.height = ''; });
			flexItem.style.width = '';
			stage.style.height = '';
			stage.style.clipPath = '';
		};

		if (canAnimate()) {
			build();
		}

		// Re-evaluate on resize so crossing the breakpoint switches modes, and
		// so a live effect re-measures. Debounced.
		let resizeTimer = 0;
		const onResize = () => {
			window.clearTimeout(resizeTimer);
			resizeTimer = window.setTimeout(() => {
				const should = canAnimate();
				if (should && !built) {
					build();
				} else if (!should && built) {
					teardown();
				} else if (should && built && window.ScrollTrigger) {
					measure();
					window.ScrollTrigger.refresh();
				}
			}, 160);
		};
		window.addEventListener('resize', onResize);

		// Late layout shifts (images, fonts) change the measured height — refresh
		// once things have settled so the final pin distance is correct.
		const refreshIfLive = () => {
			if (built && window.ScrollTrigger) {
				measure();
				window.ScrollTrigger.refresh();
			}
		};
		window.addEventListener('load', refreshIfLive, { once: true });
		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(refreshIfLive).catch(() => {});
		}
		cards.forEach((c) => {
			const img = c.querySelector('img');
			if (img && !img.complete) {
				img.addEventListener('load', refreshIfLive, { once: true });
			}
		});

		// If the widget is removed from the DOM (editor re-render), kill the
		// trigger so no pin-spacer lingers.
		if (typeof MutationObserver !== 'undefined' && widget.parentNode) {
			const observer = new MutationObserver(() => {
				if (!document.body.contains(widget)) {
					teardown();
					window.removeEventListener('resize', onResize);
					observer.disconnect();
				}
			});
			observer.observe(widget.parentNode, { childList: true });
		}
	};

	const init = (root, options) => {
		if (!root) {
			return;
		}
		cleanupOrphans();

		const editorMode = !!(
			(options && options.editorMode) ||
			(window.elementorFrontend && typeof window.elementorFrontend.isEditMode === 'function' && window.elementorFrontend.isEditMode())
		);

		const widgets = (api && typeof api.getNodes === 'function')
			? api.getNodes(root, SELECTOR)
			: Array.from(document.querySelectorAll(SELECTOR));

		widgets.forEach((widget) => {
			if (editorMode) {
				// Pinning inside the editor iframe is unreliable and covers the
				// canvas — show the cards as a plain editable list instead.
				widget.dataset.eapScBound = '1';
				widget.classList.remove('is-pinned');
				return;
			}
			setupWidget(widget);
		});
	};

	if (api && typeof api.register === 'function') {
		api.register('stacked-cards', (root, options) => init(root, options));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => init(document, {}));
	} else {
		init(document, {});
	}
})();
