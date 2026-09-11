/*
 * Table of Contents — collects the page's headings, gives them ids, builds
 * the nested list, scrolls to them, and highlights the current one.
 *
 * The scroll-spy is EAPFrontend.scrollSpy() from core.js, shared with Scroll
 * Elements rather than reimplemented here.
 */
(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '[data-eap-toc]';

	const slug = (text) =>
		text
			.toLowerCase()
			.normalize('NFD')
			.replace(/[̀-ͯ]/g, '')
			.replace(/[^a-z0-9]+/g, '-')
			.replace(/^-+|-+$/g, '')
			.slice(0, 64) || 'section';

	const safeQuery = (root, selector, all) => {
		try {
			return all ? Array.from(root.querySelectorAll(selector)) : root.querySelector(selector);
		} catch (error) {
			return all ? [] : null;
		}
	};

	/*
	 * Find the headings, in document order, at the chosen levels, inside the
	 * container, not excluded, not inside any ToC, and not empty.
	 */
	const collect = (cfg, self) => {
		const scope = cfg.container ? safeQuery(document, cfg.container, false) : null;
		const root = scope || document.body;
		const levels = (cfg.levels || ['h2', 'h3']).map((l) => String(l).toLowerCase());

		const excluded = cfg.exclude ? safeQuery(document, cfg.exclude, true) : [];
		const isExcluded = (el) => excluded.some((x) => x === el || x.contains(el));

		return safeQuery(root, levels.join(','), true).filter((h) => {
			if (self.contains(h) || h.closest('.eap-toc')) {
				return false;
			}
			if (isExcluded(h)) {
				return false;
			}
			return (h.textContent || '').trim() !== '';
		});
	};

	// Existing ids are kept — they may already be linked to. Generated ones
	// are de-duplicated, because two headings often share text ("Overview").
	const ensureIds = (headings) => {
		const taken = new Set(Array.from(document.querySelectorAll('[id]')).map((el) => el.id));
		headings.forEach((h) => {
			if (h.id) {
				return;
			}
			const base = slug(h.textContent.trim());
			let id = base;
			let n = 2;
			while (taken.has(id)) {
				id = `${base}-${n}`;
				n += 1;
			}
			taken.add(id);
			h.id = id;
		});
	};

	/*
	 * Nest by heading level using a stack. Levels are RELATIVE: a page whose
	 * first heading is an H3 still starts at depth 0, and an H4 after an H2
	 * (with no H3 between) becomes a direct child rather than an orphan.
	 */
	const buildTree = (headings, nested) => {
		const roots = [];
		const stack = [];

		headings.forEach((h) => {
			const level = parseInt(h.tagName.slice(1), 10);
			const node = { heading: h, level, children: [] };

			if (!nested) {
				roots.push(node);
				return;
			}

			while (stack.length && stack[stack.length - 1].level >= level) {
				stack.pop();
			}

			if (stack.length) {
				stack[stack.length - 1].children.push(node);
			} else {
				roots.push(node);
			}
			stack.push(node);
		});

		return roots;
	};

	const renderList = (nodes, list, depth, items) => {
		nodes.forEach((node) => {
			const li = document.createElement('li');
			li.className = `eap-toc__item eap-toc__item--depth-${depth}`;

			const a = document.createElement('a');
			a.className = 'eap-toc__link';
			a.href = `#${node.heading.id}`;
			a.textContent = node.heading.textContent.trim();
			a.setAttribute('data-eap-toc-link', node.heading.id);
			li.appendChild(a);

			items.push({ item: li, link: a, heading: node.heading });

			if (node.children.length) {
				const sub = document.createElement('ol');
				sub.className = 'eap-toc__sub';
				renderList(node.children, sub, depth + 1, items);
				li.appendChild(sub);
			}

			list.appendChild(li);
		});
	};

	const setup = (root) => {
		if (root.dataset.eapTocBound === '1') {
			return;
		}
		root.dataset.eapTocBound = '1';

		let cfg = { levels: ['h2', 'h3'], container: '', exclude: '', nested: true, collapse: false, min: 2, offset: 90, smooth: true, highlight: true, hash: true, editor: false };
		try {
			cfg = Object.assign(cfg, JSON.parse(root.getAttribute('data-eap-toc') || '{}'));
		} catch (error) {
			// Keep the defaults.
		}

		const list = root.querySelector('[data-eap-toc-list]');
		const empty = root.querySelector('[data-eap-toc-empty]');
		if (!list) {
			return;
		}

		const headings = collect(cfg, root);

		if (headings.length < cfg.min) {
			// On the front end a ToC with nothing to list is noise; in the
			// editor the author needs to see the widget to configure it.
			if (cfg.editor) {
				if (empty) {
					empty.hidden = false;
				}
			} else {
				root.classList.add('is-empty');
				root.hidden = true;
			}
			return;
		}

		ensureIds(headings);

		const offset = Math.max(0, parseInt(cfg.offset, 10) || 0);
		// So a hard reload onto #id also lands clear of a fixed header.
		headings.forEach((h) => {
			h.style.scrollMarginTop = `${offset}px`;
		});

		const items = [];
		list.textContent = '';
		renderList(buildTree(headings, !!cfg.nested), list, 0, items);

		if (cfg.collapse && cfg.nested) {
			root.classList.add('eap-toc--collapse');
		}

		const setActive = (heading) => {
			items.forEach((entry) => {
				const on = entry.heading === heading;
				entry.item.classList.toggle('is-active', on);
				entry.link.setAttribute('aria-current', on ? 'true' : 'false');
				entry.item.classList.remove('is-open');
			});

			// Keep every ancestor of the active item open so its branch shows.
			const active = items.find((entry) => entry.heading === heading);
			if (active) {
				let li = active.item.parentElement && active.item.parentElement.closest('.eap-toc__item');
				while (li) {
					li.classList.add('is-open');
					li = li.parentElement && li.parentElement.closest('.eap-toc__item');
				}
			}
		};

		/* --- Scrolling ---------------------------------------------------- */

		const scrollToHeading = (heading) => {
			const top = window.scrollY + heading.getBoundingClientRect().top - offset;
			const reduce = api && typeof api.prefersReducedMotion === 'function' && api.prefersReducedMotion();
			window.scrollTo({ top, behavior: cfg.smooth && !reduce ? 'smooth' : 'auto' });
		};

		list.addEventListener('click', (event) => {
			const link = event.target.closest('[data-eap-toc-link]');
			if (!link) {
				return;
			}
			const entry = items.find((candidate) => candidate.link === link);
			if (!entry) {
				return;
			}

			event.preventDefault();
			scrollToHeading(entry.heading);
			setActive(entry.heading);

			if (cfg.hash && window.history && window.history.replaceState) {
				// replace rather than assign: a hash assignment jumps and
				// would cut the smooth scroll short.
				window.history.replaceState(null, '', `#${entry.heading.id}`);
			}
		});

		/* --- Scroll-spy --------------------------------------------------- */

		if (cfg.highlight) {
			if (api && typeof api.scrollSpy === 'function') {
				root.eapTocSpy = api.scrollSpy(headings, (heading) => setActive(heading), { offset });
			} else {
				setActive(headings[0]);
			}
		}

		/* --- Minimise ----------------------------------------------------- */

		const toggle = root.querySelector('[data-eap-toc-toggle]');
		const body = root.querySelector('.eap-toc__body');
		if (toggle && body) {
			toggle.addEventListener('click', () => {
				const minimized = root.classList.toggle('is-minimized');
				body.hidden = minimized;
				toggle.setAttribute('aria-expanded', minimized ? 'false' : 'true');
			});
		}

		/* --- Floating tab ------------------------------------------------- */

		const tab = root.querySelector('[data-eap-toc-tab]');
		if (tab) {
			const setOpen = (open) => {
				root.classList.toggle('is-closed', !open);
				tab.setAttribute('aria-expanded', open ? 'true' : 'false');
			};

			tab.addEventListener('click', () => setOpen(root.classList.contains('is-closed')));

			document.addEventListener('keydown', (event) => {
				if (event.key === 'Escape' && !root.classList.contains('is-closed')) {
					setOpen(false);
				}
			});

			// Tapping a link in the floating box is the end of the interaction
			// on a phone; leave the reader with the content, not the box.
			list.addEventListener('click', () => {
				if (window.matchMedia('(max-width: 767px)').matches) {
					setOpen(false);
				}
			});
		}
	};

	const run = (root) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((node) => setup(node));
	};

	if (api && typeof api.register === 'function') {
		api.register('table-of-content', (root) => run(root));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document));
	} else {
		run(document);
	}
})();
