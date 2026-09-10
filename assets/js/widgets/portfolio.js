/*
 * Portfolio — filter tabs and lightbox.
 *
 * The reflow is EAPFrontend.flipFilter() from core.js, shared with Filterable
 * Posts rather than reimplemented here.
 */
(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '[data-eap-portfolio]';

	/* ---------- Lightbox ---------------------------------------------------
	 * One overlay for the whole page, built on first use.
	 * -------------------------------------------------------------------- */

	let box = null;
	let boxImg = null;
	let boxCap = null;
	let lastFocus = null;

	const buildBox = () => {
		if (box) {
			return;
		}

		box = document.createElement('div');
		box.className = 'eap-pf-lightbox';
		box.setAttribute('role', 'dialog');
		box.setAttribute('aria-modal', 'true');
		box.hidden = true;
		box.innerHTML =
			'<button type="button" class="eap-pf-lightbox__close" aria-label="Close">'
			+ '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>'
			+ '</button>'
			+ '<figure class="eap-pf-lightbox__figure">'
			+ '<img class="eap-pf-lightbox__img" alt="">'
			+ '<figcaption class="eap-pf-lightbox__caption"></figcaption>'
			+ '</figure>';

		boxImg = box.querySelector('.eap-pf-lightbox__img');
		boxCap = box.querySelector('.eap-pf-lightbox__caption');

		// Backdrop click closes; clicks inside the figure must not.
		box.addEventListener('click', (event) => {
			if (event.target === box || event.target.closest('.eap-pf-lightbox__close')) {
				close();
			}
		});

		document.addEventListener('keydown', (event) => {
			if (!box.hidden && event.key === 'Escape') {
				close();
			}
		});

		document.body.appendChild(box);
	};

	const open = (src, caption, trigger) => {
		buildBox();
		lastFocus = trigger || null;

		boxImg.src = src;
		boxImg.alt = caption || '';
		boxCap.textContent = caption || '';
		boxCap.hidden = !caption;

		box.hidden = false;
		document.documentElement.style.overflow = 'hidden';
		box.querySelector('.eap-pf-lightbox__close').focus();
	};

	const close = () => {
		if (!box || box.hidden) {
			return;
		}
		box.hidden = true;
		// Drop the source so a large image is not held decoded in memory.
		boxImg.removeAttribute('src');
		document.documentElement.style.overflow = '';
		if (lastFocus && document.contains(lastFocus)) {
			lastFocus.focus();
		}
		lastFocus = null;
	};

	/* ---------- Setup ---------------------------------------------------- */

	const setup = (root) => {
		if (root.dataset.eapPfBound === '1') {
			return;
		}
		root.dataset.eapPfBound = '1';

		const grid = root.querySelector('.eap-portfolio__grid');
		if (!grid) {
			return;
		}

		const items = Array.from(grid.querySelectorAll('.eap-portfolio__item'));
		const buttons = Array.from(root.querySelectorAll('.eap-portfolio__filter'));
		const empty = root.querySelector('.eap-portfolio__empty');

		let term = 0;

		const matches = (item) => {
			if (!term) {
				return true;
			}
			return (item.getAttribute('data-terms') || '').split(' ').indexOf(String(term)) !== -1;
		};

		const apply = () => {
			const keep = items.filter(matches);
			if (empty) {
				empty.hidden = keep.length !== 0;
			}

			if (api && typeof api.flipFilter === 'function') {
				api.flipFilter(grid, items, matches, { duration: 400 });
				return;
			}

			items.forEach((item) => item.classList.toggle('is-hidden', !matches(item)));
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

		// Delegated so it covers every tile with one listener.
		grid.addEventListener('click', (event) => {
			const zoom = event.target.closest('.eap-portfolio__action--zoom');
			if (!zoom) {
				return;
			}
			event.preventDefault();
			open(zoom.getAttribute('data-eap-pf-full'), zoom.getAttribute('data-eap-pf-caption'), zoom);
		});
	};

	const run = (root) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((node) => setup(node));
	};

	if (api && typeof api.register === 'function') {
		api.register('portfolio', (root) => run(root));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document));
	} else {
		run(document);
	}
})();
