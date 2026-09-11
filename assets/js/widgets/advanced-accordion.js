/*
 * Advanced Accordion — animation, animated exclusivity, deep links.
 *
 * The markup is native <details>, which already opens, closes and (in
 * accordion mode, through its `name` group) stays exclusive without this
 * file. What this adds:
 *
 * - Height animation. A <details> snaps; here the panel animates, and an item
 *   closing keeps [open] until its animation ends (marked .is-closing).
 * - Animated exclusivity. The browser would snap the other items shut the
 *   instant one opens, so the script takes the `name` group over and closes
 *   them itself. The `toggle` event still enforces it for openings the script
 *   did not start — find-in-page opening a match, say.
 * - Deep links (#item-id opens the item and scrolls it clear of a fixed
 *   header), keep-in-view, URL updates and expand/collapse-all.
 */
(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '[data-eap-acc]';

	const reduced = () => (api && typeof api.prefersReducedMotion === 'function'
		? api.prefersReducedMotion()
		: window.matchMedia('(prefers-reduced-motion: reduce)').matches);

	const setup = (root) => {
		if (root.dataset.eapAccBound === '1') {
			return;
		}
		root.dataset.eapAccBound = '1';

		let cfg = { type: 'accordion', keepOne: false, anim: 'slide', speed: 300, offset: 90, inView: false, hash: false };
		try {
			cfg = Object.assign(cfg, JSON.parse(root.getAttribute('data-eap-acc') || '{}'));
		} catch (error) {
			// Keep the defaults.
		}

		// :scope > — a saved template inside an item may hold another accordion.
		const items = Array.from(root.querySelectorAll(':scope > .eap-acc__item'));
		if (!items.length) {
			return;
		}

		const exclusive = cfg.type === 'accordion';
		const offset = Math.max(0, parseInt(cfg.offset, 10) || 0);
		const running = new Map();

		/*
		 * Items this script opened itself. Their `toggle` event arrives LATER,
		 * as a task, and by then the script has already made the accordion
		 * exclusive — acting on the event again would close whatever was opened
		 * in between. (Verified: open A by script, open B by other code in the
		 * same task, and A's late event closed B.)
		 */
		const selfOpened = new WeakSet();

		if (exclusive) {
			items.forEach((item) => {
				item.dataset.eapAccName = item.getAttribute('name') || '';
				item.removeAttribute('name');
			});
		}

		// So a jump that happens before this script (a hard load onto #id)
		// also clears a fixed header.
		items.forEach((item) => {
			item.style.scrollMarginTop = `${offset}px`;
		});

		const isOpen = (item) => item.open && !item.classList.contains('is-closing');
		const panelOf = (item) => item.querySelector(':scope > .eap-acc__panel');

		const setOpen = (item, value) => {
			if (value && !item.open) {
				selfOpened.add(item);
			}
			item.open = value;
		};

		const settle = (item, open) => {
			item.classList.remove('is-closing');
			setOpen(item, open);
		};

		const animate = (item, open) => {
			const panel = panelOf(item);
			const content = panel ? panel.firstElementChild : null;

			// Where the panel is NOW — mid-way through a previous animation
			// if one is running — so a reversal starts from there.
			const from = item.open && panel ? panel.getBoundingClientRect().height : 0;

			const previous = running.get(item);
			if (previous) {
				previous.forEach((animation) => animation.cancel());
				running.delete(item);
			}

			if (!panel || cfg.anim === 'none' || !cfg.speed || reduced() || typeof panel.animate !== 'function') {
				settle(item, open);
				return;
			}

			if (open) {
				item.classList.remove('is-closing');
				setOpen(item, true);
			} else {
				item.classList.add('is-closing');
			}

			const to = open ? panel.scrollHeight : 0;
			const timing = { duration: cfg.speed, easing: 'cubic-bezier(0.4, 0, 0.2, 1)' };
			const animations = [panel.animate([{ height: `${from}px` }, { height: `${to}px` }], timing)];

			if (cfg.anim === 'fade' && content) {
				animations.push(content.animate([{ opacity: open ? 0 : 1 }, { opacity: open ? 1 : 0 }], timing));
			}

			running.set(item, animations);

			// The `finished` promise rather than onfinish: it also settles when
			// finish() is called, and a cancelled animation rejects it — which
			// is ignored, because the animation that cancelled it takes over.
			// The identity check stops a late promise from settling an item a
			// newer animation now owns.
			animations[0].finished.then(() => {
				if (running.get(item) === animations) {
					running.delete(item);
					settle(item, open);
				}
			}).catch(() => {});
		};

		const closeOthers = (keep) => {
			items.forEach((other) => {
				if (other !== keep && isOpen(other)) {
					animate(other, false);
				}
			});
		};

		const scrollToItem = (item, smooth) => {
			const top = window.scrollY + item.getBoundingClientRect().top - offset;
			window.scrollTo({ top, behavior: smooth && !reduced() ? 'smooth' : 'auto' });
		};

		const open = (item) => {
			if (exclusive) {
				closeOthers(item);
			}
			animate(item, true);

			if (cfg.hash && item.id && window.history && window.history.replaceState) {
				window.history.replaceState(null, '', `#${item.id}`);
			}

			if (cfg.inView) {
				// After the item above has finished collapsing.
				window.setTimeout(() => {
					const top = item.getBoundingClientRect().top;
					if (top < offset || top > window.innerHeight) {
						scrollToItem(item, true);
					}
				}, (reduced() || cfg.anim === 'none' ? 0 : cfg.speed) + 30);
			}
		};

		items.forEach((item) => {
			const head = item.querySelector(':scope > .eap-acc__head');
			if (!head) {
				return;
			}

			head.addEventListener('click', (event) => {
				event.preventDefault();
				if (isOpen(item)) {
					if (exclusive && cfg.keepOne) {
						return;
					}
					animate(item, false);
				} else {
					open(item);
				}
			});

			// Openings this script did not start (find-in-page, another
			// script setting .open) still respect the accordion.
			item.addEventListener('toggle', () => {
				const self = selfOpened.has(item);
				selfOpened.delete(item);
				if (self || !exclusive || !isOpen(item)) {
					return;
				}
				closeOthers(item);
			});
		});

		/* --- Deep links ---------------------------------------------------- */

		const fromHash = (smooth) => {
			let id = '';
			try {
				id = decodeURIComponent((window.location.hash || '').slice(1));
			} catch (error) {
				return;
			}

			// Only the element the browser itself treats as the fragment
			// target. With the same ID in two accordions on one page, both
			// would otherwise open and fight over the scroll position.
			const target = id ? document.getElementById(id) : null;
			const item = target ? items.find((candidate) => candidate === target) : null;
			if (!item) {
				return;
			}

			if (!isOpen(item)) {
				if (exclusive) {
					items.forEach((other) => {
						if (other !== item) {
							settle(other, false);
						}
					});
				}
				settle(item, true);
			}
			scrollToItem(item, smooth);
		};

		fromHash(false);
		window.addEventListener('hashchange', () => fromHash(true));

		/* --- Expand / collapse all ---------------------------------------- */

		const expand = root.querySelector(':scope > .eap-acc__tools [data-eap-acc-expand]');
		const collapse = root.querySelector(':scope > .eap-acc__tools [data-eap-acc-collapse]');

		if (expand) {
			expand.addEventListener('click', () => items.forEach((item) => {
				if (!isOpen(item)) {
					animate(item, true);
				}
			}));
		}

		if (collapse) {
			collapse.addEventListener('click', () => items.forEach((item) => {
				if (isOpen(item)) {
					animate(item, false);
				}
			}));
		}

		root.eapAccordion = { items, open, close: (item) => animate(item, false), isOpen };
		root.classList.add('is-ready');
	};

	const run = (root) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((node) => setup(node));
	};

	if (api && typeof api.register === 'function') {
		api.register('advanced-accordion', (root) => run(root));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document));
	} else {
		run(document);
	}
})();
