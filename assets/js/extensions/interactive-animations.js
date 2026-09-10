(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '[data-eap-ia]';

	const reduced = () =>
		window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	const clamp = (n, min, max) => Math.min(max, Math.max(min, n));

	// --- Scroll ------------------------------------------------------------
	// One shared rAF pass over every scroll-driven element, rather than a
	// listener each: scroll fires far more often than it paints.
	const scrollItems = [];
	let scrollBound = false;
	let scrollQueued = false;

	const updateScroll = () => {
		scrollQueued = false;
		const vh = window.innerHeight || document.documentElement.clientHeight;

		scrollItems.forEach((item) => {
			const rect = item.el.getBoundingClientRect();

			// Start/end are viewport percentages measured from the top; the element
			// animates as its top edge travels from `start` up to `end`.
			const startPx = vh * (item.start / 100);
			const endPx = vh * (item.end / 100);
			const span = startPx - endPx;

			let progress = span <= 0 ? 1 : (startPx - rect.top) / span;
			progress = clamp(progress, 0, 1);

			if (item.once && progress >= 1) {
				item.done = true;
			}
			if (item.done) {
				progress = 1;
			}

			if (progress !== item.last) {
				item.last = progress;
				item.el.style.setProperty('--eap-ia-p', String(progress));
			}
		});
	};

	const queueScroll = () => {
		if (scrollQueued) {
			return;
		}
		scrollQueued = true;
		window.requestAnimationFrame(updateScroll);
	};

	const bindScroll = () => {
		if (scrollBound) {
			return;
		}
		scrollBound = true;
		window.addEventListener('scroll', queueScroll, { passive: true });
		window.addEventListener('resize', queueScroll);
	};

	// --- Mouse move --------------------------------------------------------
	const bindMouseMove = (el, scope) => {
		let queued = false;
		let mx = 0;
		let my = 0;

		const apply = () => {
			queued = false;
			el.style.setProperty('--eap-ia-mx', String(mx));
			el.style.setProperty('--eap-ia-my', String(my));
		};

		const onMove = (event) => {
			if (scope === 'element') {
				const rect = el.getBoundingClientRect();
				if (!rect.width || !rect.height) {
					return;
				}
				// -1 .. 1 relative to the element's centre.
				mx = clamp(((event.clientX - rect.left) / rect.width) * 2 - 1, -1, 1);
				my = clamp(((event.clientY - rect.top) / rect.height) * 2 - 1, -1, 1);
			} else {
				const vw = window.innerWidth || 1;
				const vh = window.innerHeight || 1;
				mx = clamp((event.clientX / vw) * 2 - 1, -1, 1);
				my = clamp((event.clientY / vh) * 2 - 1, -1, 1);
			}

			if (!queued) {
				queued = true;
				window.requestAnimationFrame(apply);
			}
		};

		const host = scope === 'element' ? el : window;
		host.addEventListener('mousemove', onMove, { passive: true });

		if (scope === 'element') {
			el.addEventListener('mouseleave', () => {
				mx = 0;
				my = 0;
				apply();
			});
		}
	};

	// --- Setup -------------------------------------------------------------
	const setup = (el) => {
		if (el.dataset.eapIaBound === '1') {
			return;
		}
		el.dataset.eapIaBound = '1';

		// Everything here is decoration; leave the resting state alone.
		if (reduced()) {
			return;
		}

		let config = {};
		try {
			config = JSON.parse(el.getAttribute('data-eap-ia') || '{}');
		} catch (error) {
			return;
		}

		if (config.trigger === 'click') {
			el.addEventListener('click', () => el.classList.toggle('is-active'));
			return;
		}

		if (config.trigger === 'mousemove') {
			bindMouseMove(el, config.scope === 'element' ? 'element' : 'viewport');
			return;
		}

		if (config.trigger === 'scroll') {
			const start = typeof config.start === 'number' ? config.start : 90;
			const end = typeof config.end === 'number' ? config.end : 50;

			scrollItems.push({
				el,
				start,
				end: end < start ? end : Math.max(0, start - 1), // guard an inverted range
				once: !!config.once,
				done: false,
				last: -1
			});

			bindScroll();
			queueScroll();
		}
	};

	const run = (root) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((node) => setup(node));
	};

	if (api && typeof api.register === 'function') {
		api.register('interactive-animations', (root) => run(root));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document));
	} else {
		run(document);
	}
})();
