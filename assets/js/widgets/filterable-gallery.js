/*
 * Filterable Gallery — filter tabs, search, Load More and a lightbox.
 *
 * The reflow is EAPFrontend.flipFilter() from core.js, shared with Portfolio
 * and Filterable Posts rather than reimplemented here.
 *
 * NOTE: Portfolio also has a lightbox, but a simpler one — image only, no
 * navigation. This one has to walk prev/next and play video, and the walk has
 * to follow the ACTIVE FILTER (see buildNavSet below), so the two are not
 * shared today. If a third widget needs a lightbox, the primitive should move
 * to core.js the way flipFilter did.
 */
(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '[data-eap-filterable-gallery]';
	const VIDEO_FILE = /\.(mp4|webm|ogv|ogg|mov|m4v)(\?.*)?$/i;

	/* ---------- Lightbox ---------------------------------------------------
	 * One overlay for the whole page, built on first use and reused by every
	 * gallery on it.
	 * -------------------------------------------------------------------- */

	let box = null;
	let boxStage = null;
	let boxCap = null;
	let boxCounter = null;
	let boxPrev = null;
	let boxNext = null;
	let lastFocus = null;

	// The set the arrows walk. Rebuilt on every open so it always reflects the
	// filter and paging state at that moment.
	let navSet = [];
	let navAt = 0;

	const icon = (path) =>
		'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="' + path
		+ '" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

	const buildBox = () => {
		if (box) {
			return;
		}

		box = document.createElement('div');
		box.className = 'eap-fg-lightbox';
		box.setAttribute('role', 'dialog');
		box.setAttribute('aria-modal', 'true');
		box.hidden = true;
		box.innerHTML =
			'<button type="button" class="eap-fg-lightbox__close" aria-label="Close">' + icon('M6 6l12 12M18 6L6 18') + '</button>'
			+ '<button type="button" class="eap-fg-lightbox__nav eap-fg-lightbox__nav--prev" aria-label="Previous">' + icon('M15 5l-7 7 7 7') + '</button>'
			+ '<button type="button" class="eap-fg-lightbox__nav eap-fg-lightbox__nav--next" aria-label="Next">' + icon('M9 5l7 7-7 7') + '</button>'
			+ '<figure class="eap-fg-lightbox__figure">'
			+ '<div class="eap-fg-lightbox__stage"></div>'
			+ '<figcaption class="eap-fg-lightbox__caption"></figcaption>'
			+ '</figure>'
			+ '<p class="eap-fg-lightbox__counter"></p>';

		boxStage = box.querySelector('.eap-fg-lightbox__stage');
		boxCap = box.querySelector('.eap-fg-lightbox__caption');
		boxCounter = box.querySelector('.eap-fg-lightbox__counter');
		boxPrev = box.querySelector('.eap-fg-lightbox__nav--prev');
		boxNext = box.querySelector('.eap-fg-lightbox__nav--next');

		// Backdrop click closes; clicks inside the figure must not.
		box.addEventListener('click', (event) => {
			if (event.target === box || event.target.closest('.eap-fg-lightbox__close')) {
				close();
			}
		});

		boxPrev.addEventListener('click', () => step(-1));
		boxNext.addEventListener('click', () => step(1));

		document.addEventListener('keydown', (event) => {
			if (!box || box.hidden) {
				return;
			}
			if (event.key === 'Escape') {
				close();
			} else if (event.key === 'ArrowLeft') {
				step(-1);
			} else if (event.key === 'ArrowRight') {
				step(1);
			}
		});

		document.body.appendChild(box);
	};

	// Dropping the media (rather than just hiding it) stops a video playing on
	// close and releases a large decoded image.
	const clearStage = () => {
		if (boxStage) {
			boxStage.textContent = '';
		}
	};

	const show = (index) => {
		const item = navSet[index];
		if (!item) {
			return;
		}

		navAt = index;

		const src = item.getAttribute('data-eap-fg-full') || '';
		const video = item.getAttribute('data-eap-fg-video') || '';
		const caption = item.getAttribute('data-eap-fg-caption') || '';

		clearStage();

		if (video && VIDEO_FILE.test(video)) {
			const el = document.createElement('video');
			el.className = 'eap-fg-lightbox__video';
			el.src = video;
			el.controls = true;
			el.autoplay = true;
			el.playsInline = true;
			if (src) {
				el.poster = src;
			}
			boxStage.appendChild(el);
		} else if (video) {
			// Anything that is not a recognised video FILE is treated as an
			// embed page (YouTube, Vimeo, a self-hosted player).
			const frame = document.createElement('iframe');
			frame.className = 'eap-fg-lightbox__frame';
			frame.src = video;
			frame.allow = 'accelerometer; autoplay; encrypted-media; picture-in-picture';
			frame.allowFullscreen = true;
			frame.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
			frame.title = caption || 'Video';
			boxStage.appendChild(frame);
		} else {
			const img = document.createElement('img');
			img.className = 'eap-fg-lightbox__img';
			img.src = src;
			img.alt = caption;
			boxStage.appendChild(img);
		}

		boxCap.textContent = caption;
		boxCap.hidden = !caption;

		const many = navSet.length > 1;
		boxPrev.hidden = !many;
		boxNext.hidden = !many;
		boxCounter.hidden = !many;
		if (many) {
			boxCounter.textContent = (index + 1) + ' / ' + navSet.length;
		}
	};

	const step = (delta) => {
		if (navSet.length < 2) {
			return;
		}
		// Wrap, so the arrows never dead-end.
		show((navAt + delta + navSet.length) % navSet.length);
	};

	const open = (set, index, trigger) => {
		buildBox();
		navSet = set;
		lastFocus = trigger || null;

		box.hidden = false;
		document.documentElement.style.overflow = 'hidden';
		show(index);
		box.querySelector('.eap-fg-lightbox__close').focus();
	};

	const close = () => {
		if (!box || box.hidden) {
			return;
		}
		box.hidden = true;
		clearStage();
		navSet = [];
		document.documentElement.style.overflow = '';
		if (lastFocus && document.contains(lastFocus)) {
			lastFocus.focus();
		}
		lastFocus = null;
	};

	/* ---------- Setup ---------------------------------------------------- */

	const setup = (root) => {
		if (root.dataset.eapFgBound === '1') {
			return;
		}
		root.dataset.eapFgBound = '1';

		let cfg = { duration: 400, loadMore: false, perPage: 6 };
		try {
			cfg = Object.assign(cfg, JSON.parse(root.getAttribute('data-eap-filterable-gallery') || '{}'));
		} catch (error) {
			// Keep the defaults.
		}

		const grid = root.querySelector('.eap-fg__grid');
		if (!grid) {
			return;
		}

		const items = Array.from(grid.querySelectorAll('.eap-fg__item'));
		const buttons = Array.from(root.querySelectorAll('.eap-fg__filter'));
		const search = root.querySelector('[data-eap-fg-search]');
		const empty = root.querySelector('.eap-fg__empty');
		const moreBtn = root.querySelector('[data-eap-fg-more]');
		const noMore = root.querySelector('[data-eap-fg-nomore]');

		const perPage = Math.max(1, parseInt(cfg.perPage, 10) || 6);
		const paged = !!cfg.loadMore;

		let active = '';
		let query = '';
		let page = 1;

		// Cache the searchable text once. Reading it per keystroke would walk
		// the DOM for every item on every character.
		items.forEach((item) => {
			const title = item.querySelector('.eap-fg__title');
			const text = item.querySelector('.eap-fg__text');
			item.eapFgHaystack = ((title ? title.textContent : '') + ' ' + (text ? text.textContent : ''))
				.toLowerCase()
				.trim();
		});

		// Matches the filter and the search, but NOT the page limit.
		const inSet = (item) => {
			if (active) {
				const cats = (item.getAttribute('data-eap-fg-cats') || '').split(' ');
				if (cats.indexOf(active) === -1) {
					return false;
				}
			}
			if (query && (item.eapFgHaystack || '').indexOf(query) === -1) {
				return false;
			}
			return true;
		};

		/*
		 * Paging applies to the FILTERED set, not the whole gallery: switching
		 * to a tab with 20 items shows the first batch of those 20, not
		 * whichever of them happened to fall in the first batch overall. That
		 * is why the cap is applied by rank within the matching list rather
		 * than by DOM index.
		 */
		let allowed = null;

		const recompute = () => {
			const matching = items.filter(inSet);
			allowed = paged ? new Set(matching.slice(0, page * perPage)) : new Set(matching);
			return matching;
		};

		const visible = (item) => allowed.has(item);

		const apply = (animate) => {
			const matching = recompute();

			if (empty) {
				empty.hidden = matching.length !== 0;
			}

			if (moreBtn) {
				const shown = Math.min(matching.length, page * perPage);
				const remaining = matching.length - shown;
				moreBtn.hidden = remaining <= 0;
				if (noMore) {
					// Only worth saying when there was actually more than one
					// batch to get through.
					noMore.hidden = !(remaining <= 0 && matching.length > perPage);
				}
			}

			if (animate && api && typeof api.flipFilter === 'function') {
				api.flipFilter(grid, items, visible, { duration: cfg.duration });
				return;
			}

			items.forEach((item) => item.classList.toggle('is-hidden', !visible(item)));
		};

		buttons.forEach((button) => {
			button.addEventListener('click', () => {
				const next = button.getAttribute('data-eap-fg-filter') || '';
				if (next === active) {
					return;
				}
				active = next;
				page = 1;

				buttons.forEach((other) => {
					const on = other === button;
					other.classList.toggle('is-active', on);
					other.setAttribute('aria-pressed', on ? 'true' : 'false');
				});

				apply(true);
			});
		});

		if (search) {
			let timer = null;
			search.addEventListener('input', () => {
				window.clearTimeout(timer);
				// Typing is bursty; reflowing on every keystroke fights the FLIP.
				timer = window.setTimeout(() => {
					const next = search.value.toLowerCase().trim();
					if (next === query) {
						return;
					}
					query = next;
					page = 1;
					apply(true);
				}, 180);
			});
		}

		if (moreBtn) {
			moreBtn.addEventListener('click', () => {
				page += 1;
				apply(true);
			});
		}

		// Delegated so one listener covers every tile.
		grid.addEventListener('click', (event) => {
			const zoom = event.target.closest('[data-eap-fg-zoom]');
			const media = event.target.closest('.eap-fg__media.is-clickable');

			// A link inside the tile must keep working.
			if (!zoom && (!media || event.target.closest('a'))) {
				return;
			}

			const item = event.target.closest('.eap-fg__item');
			if (!item || item.getAttribute('data-eap-fg-has-lightbox') !== '1') {
				return;
			}

			event.preventDefault();

			// Walk only what the reader can currently see.
			const set = items.filter((candidate) => visible(candidate) && candidate.getAttribute('data-eap-fg-has-lightbox') === '1');
			const index = set.indexOf(item);
			if (index === -1) {
				return;
			}

			open(set, index, zoom || item);
		});

		apply(false);
	};

	const run = (root) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((node) => setup(node));
	};

	if (api && typeof api.register === 'function') {
		api.register('filterable-gallery', (root) => run(root));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document));
	} else {
		run(document);
	}
})();
