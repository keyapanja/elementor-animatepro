/*
 * Filterable Posts.
 *
 * Filtering is client-side over the pool the server already rendered, so a tab
 * click costs no round trip. The reflow uses FLIP: measure where every surviving
 * card is, change the DOM, measure again, then transform each card back to where
 * it was and release it. The browser animates one transform per card instead of
 * being asked to transition `top`/`left`, which it cannot do cheaply.
 */
(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '[data-eap-filterable-posts]';

	const reduced = () =>
		window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	const setup = (root) => {
		if (root.dataset.eapFpBound === '1') {
			return;
		}
		root.dataset.eapFpBound = '1';

		const grid = root.querySelector('.eap-filterable-posts__grid');
		if (!grid) {
			return;
		}

		const items = Array.from(grid.querySelectorAll('.eap-filterable-posts__item'));
		const buttons = Array.from(root.querySelectorAll('.eap-filterable-posts__filter'));
		const search = root.querySelector('.eap-filterable-posts__search');
		const empty = root.querySelector('.eap-filterable-posts__empty');

		let term = 0;
		let query = '';
		let running = 0;

		const active = buttons.find((b) => b.classList.contains('is-active'));
		if (active) {
			term = parseInt(active.getAttribute('data-filter'), 10) || 0;
		}

		const speed = () => {
			if (reduced()) {
				return 0;
			}
			const raw = parseFloat(
				window.getComputedStyle(root).getPropertyValue('--eap-fp-speed')
			);
			return Number.isFinite(raw) ? raw : 400;
		};

		/*
		 * Title + excerpt only — deliberately NOT the whole card.
		 * Every card carries the same boilerplate (the "Read More" link, a
		 * "3 Comments" meta item, a date), so searching item.textContent made
		 * "read", "comment" and "may" each match all nine cards. Cached because
		 * this is read for every card on every keystroke.
		 */
		const haystack = (item) => {
			if (item.eapHaystack === undefined) {
				const title = item.querySelector('.eap-posts__title');
				const excerpt = item.querySelector('.eap-posts__excerpt');
				item.eapHaystack = (
					(title ? title.textContent : '') + ' ' + (excerpt ? excerpt.textContent : '')
				).toLowerCase();
			}
			return item.eapHaystack;
		};

		// A card matches when it carries the chosen term (0 = All) AND its title
		// or excerpt contains the search string.
		const matches = (item) => {
			if (term) {
				const terms = (item.getAttribute('data-terms') || '').split(' ');
				if (terms.indexOf(String(term)) === -1) {
					return false;
				}
			}
			if (query && haystack(item).indexOf(query) === -1) {
				return false;
			}
			return true;
		};

		const apply = () => {
			const keep = items.filter(matches);

			if (empty) {
				empty.hidden = keep.length !== 0;
			}

			// The FLIP reflow itself lives in core.js so this widget and Portfolio
			// share one implementation — it has already needed one subtle
			// correctness fix, and a second copy would not have received it.
			if (api && typeof api.flipFilter === 'function') {
				api.flipFilter(root, items, matches, { duration: speed() });
				return;
			}

			// No shared runtime (core.js absent): still filter, just without motion.
			items.forEach((item) => {
				item.style.transition = '';
				item.style.transform = '';
				item.classList.remove('is-leaving', 'is-entering', 'is-entered');
				item.classList.toggle('is-hidden', !matches(item));
			});
		};

		buttons.forEach((button) => {
			button.addEventListener('click', () => {
				const next = parseInt(button.getAttribute('data-filter'), 10) || 0;
				if (next === term) {
					return;
				}
				term = next;

				buttons.forEach((other) => {
					const on = other === button;
					other.classList.toggle('is-active', on);
					other.setAttribute('aria-pressed', on ? 'true' : 'false');
				});

				apply();
			});
		});

		if (search) {
			let debounce = 0;
			search.addEventListener('input', () => {
				window.clearTimeout(debounce);
				debounce = window.setTimeout(() => {
					const next = search.value.trim().toLowerCase();
					if (next === query) {
						return;
					}
					query = next;
					apply();
				}, 180);
			});
		}

		// The server renders every card; if the initially-active tab isn't "All",
		// the first pass is what narrows the grid to it.
		if (term) {
			apply();
		}
	};

	const run = (root) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((node) => setup(node));
	};

	if (api && typeof api.register === 'function') {
		api.register('filterable-posts', (root) => run(root));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document));
	} else {
		run(document);
	}
})();
