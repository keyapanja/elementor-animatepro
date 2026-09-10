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

		// A card matches when it carries the chosen term (0 = All) AND its text
		// contains the search string.
		const matches = (item) => {
			if (term) {
				const terms = (item.getAttribute('data-terms') || '').split(' ');
				if (terms.indexOf(String(term)) === -1) {
					return false;
				}
			}
			if (query) {
				const title = item.textContent.toLowerCase();
				if (title.indexOf(query) === -1) {
					return false;
				}
			}
			return true;
		};

		const apply = () => {
			const ms = speed();
			const keep = items.filter(matches);

			if (empty) {
				empty.hidden = keep.length !== 0;
			}

			if (!ms) {
				items.forEach((item) => {
					item.classList.remove('is-leaving', 'is-entering', 'is-entered');
					item.classList.toggle('is-hidden', !matches(item));
				});
				return;
			}

			const run = ++running;
			root.classList.add('is-animating');

			// FIRST: where the cards that are currently on screen sit now.
			const before = new Map();
			items.forEach((item) => {
				if (!item.classList.contains('is-hidden')) {
					before.set(item, item.getBoundingClientRect());
				}
			});

			const leaving = items.filter(
				(item) => !item.classList.contains('is-hidden') && !matches(item)
			);

			// Fade the departing cards out before they leave the flow, so the
			// survivors don't jump while something is still visible on top of them.
			leaving.forEach((item) => item.classList.add('is-leaving'));

			window.setTimeout(() => {
				if (run !== running) {
					return;
				}

				items.forEach((item) => {
					const show = matches(item);
					item.classList.remove('is-leaving');
					item.classList.toggle('is-hidden', !show);
				});

				// LAST: measure the new layout.
				const after = new Map();
				keep.forEach((item) => after.set(item, item.getBoundingClientRect()));

				// INVERT: put each survivor back where it was.
				keep.forEach((item) => {
					const first = before.get(item);
					const last = after.get(item);

					if (!first) {
						// Wasn't on screen before — this one fades in rather than moves.
						item.classList.add('is-entering');
						return;
					}

					const dx = first.left - last.left;
					const dy = first.top - last.top;
					if (!dx && !dy) {
						return;
					}

					item.style.transition = 'none';
					item.style.transform = `translate(${dx}px, ${dy}px)`;
				});

				// PLAY: next frame, drop the inverted transform and let it transition.
				window.requestAnimationFrame(() => {
					if (run !== running) {
						return;
					}

					keep.forEach((item) => {
						if (item.classList.contains('is-entering')) {
							item.classList.remove('is-entering');
							item.classList.add('is-entered');
							return;
						}
						if (!item.style.transform) {
							return;
						}
						item.style.transition = `transform ${ms}ms ease`;
						item.style.transform = '';
					});

					window.setTimeout(() => {
						if (run !== running) {
							return;
						}
						keep.forEach((item) => {
							item.style.transition = '';
							item.style.transform = '';
							item.classList.remove('is-entered');
						});
						root.classList.remove('is-animating');
					}, ms + 40);
				});
			}, leaving.length ? ms : 0);
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
