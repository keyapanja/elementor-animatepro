/*
 * Scroll Elements — scroll-spy and click-to-scroll.
 *
 * The scroll-spy is EAPFrontend.scrollSpy() from core.js, shared with Table
 * of Contents. (One Page Nav keeps its own: it decides on a percent-of-viewport
 * line with a straddle test, which is a different contract.)
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

		if (api && typeof api.scrollSpy === 'function') {
			root.eapSeSpy = api.scrollSpy(
				sections,
				(section) => setActive(section.getAttribute('data-eap-se-section')),
				{ offset }
			);
		} else {
			setActive(sections[0].getAttribute('data-eap-se-section'));
		}

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
