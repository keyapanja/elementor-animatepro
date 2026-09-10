/*
 * Scroll Elements — scroll-spy and click-to-scroll.
 *
 * NOTE: One Page Nav also does scroll-spy, but against sections it does not own
 * and with its own scope/selector handling. The two are not shared today; if a
 * third widget needs it, the primitive should move to core.js the way
 * flipFilter did.
 */
(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '[data-eap-scroll-elements]';

	const setup = (root) => {
		if (root.dataset.eapSeBound === '1') {
			return;
		}
		root.dataset.eapSeBound = '1';

		let cfg = { offset: 90 };
		try {
			cfg = Object.assign(cfg, JSON.parse(root.getAttribute('data-eap-scroll-elements') || '{}'));
		} catch (error) {
			// Keep the default offset.
		}

		const offset = Math.max(0, parseInt(cfg.offset, 10) || 0);
		const links = Array.from(root.querySelectorAll('[data-eap-se-link]'));
		const sections = Array.from(root.querySelectorAll('[data-eap-se-section]'));

		if (!links.length || !sections.length) {
			return;
		}

		// The CSS uses the same offset for fragment links and hard reloads.
		root.style.setProperty('--eap-se-scroll-offset', `${offset}px`);

		const setActive = (id) => {
			links.forEach((link) => {
				const on = link.getAttribute('data-eap-se-link') === id;
				link.setAttribute('aria-current', on ? 'true' : 'false');
				const item = link.closest('.eap-se__item');
				if (item) {
					item.classList.toggle('is-active', on);
				}
			});
		};

		/*
		 * Scroll-spy: IntersectionObserver TRIGGERS the check, measurement MAKES
		 * the decision.
		 *
		 * Why not decide from the observer's own entries: it reports WHICH
		 * sections intersect, not which one the reader is on. With several short
		 * sections on screen at once several entries fire, and the "active" one
		 * would depend on callback order. Measuring "the last section whose top
		 * has passed the offset line" is unambiguous.
		 *
		 * Why not trigger from scroll alone: the observer is computed by the
		 * engine and fires without depending on scroll events or animation
		 * frames. Relying on both of those gave this two ways to silently stop —
		 * which is exactly what happened while verifying it, in an environment
		 * where neither fired even though the page had genuinely scrolled.
		 * Scroll and resize are kept as secondary triggers.
		 */
		let last = 0;

		const update = () => {
			last = Date.now();

			const line = offset + 1;
			let current = sections[0];

			sections.forEach((section) => {
				if (section.getBoundingClientRect().top <= line) {
					current = section;
				}
			});

			// At the very bottom, the last section is the one being read even if
			// its top never crossed the line.
			const atBottom = (window.innerHeight + window.scrollY) >= (document.body.scrollHeight - 2);
			if (atBottom) {
				current = sections[sections.length - 1];
			}

			setActive(current.getAttribute('data-eap-se-section'));
		};

		// A light time throttle rather than an animation frame, so a throttled or
		// non-painting frame loop cannot stall it.
		const queue = () => {
			if (Date.now() - last < 60) {
				return;
			}
			update();
		};

		if (typeof window.IntersectionObserver === 'function') {
			// The negative top margin puts the observer's boundary exactly on the
			// offset line, so it fires as each section crosses the same point the
			// measurement uses.
			const observer = new window.IntersectionObserver(
				() => update(),
				{ rootMargin: `-${offset}px 0px 0px 0px`, threshold: [0, 1] }
			);
			sections.forEach((section) => observer.observe(section));
		}

		window.addEventListener('scroll', queue, { passive: true });
		window.addEventListener('resize', queue);

		links.forEach((link) => {
			link.addEventListener('click', (event) => {
				const id = link.getAttribute('data-eap-se-link');
				const target = root.querySelector(`[data-eap-se-section="${id}"]`);
				if (!target) {
					return;
				}

				event.preventDefault();

				const top = window.scrollY + target.getBoundingClientRect().top - offset;
				window.scrollTo({
					top,
					behavior: api && api.prefersReducedMotion && api.prefersReducedMotion() ? 'auto' : 'smooth'
				});

				setActive(id);

				// Keep the URL linkable without letting the browser jump: replace
				// rather than assign, so the smooth scroll above is not interrupted.
				if (window.history && window.history.replaceState) {
					window.history.replaceState(null, '', `#${id}`);
				}
			});
		});

		update();
	};

	const run = (root) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((node) => setup(node));
	};

	if (api && typeof api.register === 'function') {
		api.register('scroll-elements', (root) => run(root));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document));
	} else {
		run(document);
	}
})();
