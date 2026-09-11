/*
 * Vertical Marquee — deals the items into columns and times each column.
 *
 * The page carries ONE list. How many columns to use is a responsive
 * setting, so it is read from the CSS custom property the Columns control
 * writes (--eap-vm-cols) and the list is dealt into that many columns — and
 * dealt again whenever the breakpoint changes it.
 *
 * Per column: the items, topped up with extra copies while the list is
 * shorter than the viewport (otherwise the seam shows as a gap), then an
 * identical copy of the whole list. Only the first set of items is exposed to
 * assistive tech and the tab order; everything added for the loop is
 * aria-hidden with its links taken out of the tab order.
 */
(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '[data-eap-vm]';
	const FOCUSABLE = 'a, button, input, select, textarea, [tabindex]';

	const reduced = () => (api && typeof api.prefersReducedMotion === 'function'
		? api.prefersReducedMotion()
		: window.matchMedia('(prefers-reduced-motion: reduce)').matches);

	// Hidden from screen readers and out of the tab order.
	const mute = (el) => {
		el.setAttribute('aria-hidden', 'true');
		if (el.matches(FOCUSABLE)) {
			el.setAttribute('tabindex', '-1');
		}
		el.querySelectorAll(FOCUSABLE).forEach((node) => node.setAttribute('tabindex', '-1'));
	};

	const setup = (root) => {
		if (root.dataset.eapVmBound === '1') {
			return;
		}
		root.dataset.eapVmBound = '1';

		let cfg = { speed: 40, direction: 'up', alternate: true, stagger: true, distribute: 'split', i18n: {} };
		try {
			cfg = Object.assign(cfg, JSON.parse(root.getAttribute('data-eap-vm') || '{}'));
		} catch (error) {
			// Keep the defaults.
		}

		const viewport = root.querySelector(':scope > .eap-vm__viewport');
		const source = viewport ? viewport.querySelector(':scope > .eap-vm__source') : null;
		if (!viewport || !source) {
			return;
		}

		const items = Array.from(source.children);
		if (!items.length) {
			return;
		}

		let cols = 0;
		let timer = 0;

		const readCols = () => {
			const value = parseInt(window.getComputedStyle(root).getPropertyValue('--eap-vm-cols'), 10);
			return Math.max(1, Math.min(4, value || 1));
		};

		const columns = () => Array.from(viewport.querySelectorAll(':scope > .eap-vm__column'));

		/* --- Timing: fill, copy, duration, direction, phase --------------- */

		const measure = () => {
			const still = reduced();
			root.classList.toggle('is-still', still);

			const height = viewport.clientHeight;
			const speed = Math.max(1, Number(cfg.speed) || 40);

			columns().forEach((column, index) => {
				const track = column.firstElementChild;
				const list = track ? track.firstElementChild : null;
				if (!list) {
					return;
				}

				// Start from the dealt items only.
				track.querySelectorAll(':scope > [data-eap-vm-copy]').forEach((node) => node.remove());
				list.querySelectorAll(':scope > [data-eap-vm-fill]').forEach((node) => node.remove());

				if (still || !list.children.length) {
					track.style.removeProperty('--eap-vm-duration');
					return;
				}

				// Top up a short column so the loop never shows a gap.
				const dealt = Array.from(list.children);
				let guard = 0;
				while (list.getBoundingClientRect().height < height && guard < 24) {
					dealt.forEach((node) => {
						const fill = node.cloneNode(true);
						fill.dataset.eapVmFill = '1';
						mute(fill);
						list.appendChild(fill);
					});
					guard += 1;
				}

				const copy = list.cloneNode(true);
				copy.dataset.eapVmCopy = '1';
				copy.removeAttribute('role');
				mute(copy);
				track.appendChild(copy);

				// One list-height (its bottom padding is the gap to the copy).
				const distance = list.getBoundingClientRect().height;
				track.style.setProperty('--eap-vm-duration', `${(distance / speed).toFixed(3)}s`);

				const odd = index % 2 === 1;
				const down = (cfg.direction === 'down') !== (!!cfg.alternate && odd);
				track.classList.toggle('is-down', down);

				// Spread the columns out so rows never line up.
				const phase = cfg.stagger ? ((index / Math.max(1, cols)) * 0.5) + (odd ? 0.13 : 0) : 0;
				track.style.setProperty('--eap-vm-phase', phase.toFixed(3));
			});
		};

		/* --- Dealing items into columns ----------------------------------- */

		const build = () => {
			cols = readCols();
			columns().forEach((column) => column.remove());

			for (let c = 0; c < cols; c += 1) {
				const column = document.createElement('div');
				column.className = 'eap-vm__column';

				const track = document.createElement('div');
				track.className = 'eap-vm__track';

				const list = document.createElement('ul');
				list.className = 'eap-vm__list';
				list.setAttribute('role', 'list');

				const mine = cfg.distribute === 'repeat' ? items : items.filter((item, i) => i % cols === c);
				mine.forEach((item) => list.appendChild(item.cloneNode(true)));

				// Every column showing every item would read each one N times.
				if (cfg.distribute === 'repeat' && c > 0) {
					list.removeAttribute('role');
					mute(list);
				}

				track.appendChild(list);
				column.appendChild(track);
				viewport.appendChild(column);
			}

			// The dealt copies are the content now.
			source.hidden = true;

			measure();
		};

		const schedule = () => {
			window.clearTimeout(timer);
			timer = window.setTimeout(() => {
				if (readCols() !== cols) {
					build();
				} else {
					measure();
				}
			}, 120);
		};

		build();

		// Width changes can move the breakpoint; images arriving change heights.
		if (typeof window.ResizeObserver === 'function') {
			new window.ResizeObserver(schedule).observe(root);
		} else {
			window.addEventListener('resize', schedule);
		}
		viewport.addEventListener('load', (event) => {
			if (event.target && event.target.tagName === 'IMG') {
				schedule();
			}
		}, true);

		/* --- Pause button --------------------------------------------------- */

		const pause = root.querySelector(':scope > .eap-vm__pause');
		if (pause) {
			pause.hidden = reduced();
			pause.addEventListener('click', () => {
				const paused = root.classList.toggle('is-paused');
				pause.setAttribute('aria-pressed', paused ? 'true' : 'false');
				pause.setAttribute('aria-label', paused ? (cfg.i18n.play || 'Play') : (cfg.i18n.pause || 'Pause'));
			});
		}

		root.eapVerticalMarquee = { build, measure, columns: () => cols };
		root.classList.add('is-ready');
	};

	const run = (root) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((node) => setup(node));
	};

	if (api && typeof api.register === 'function') {
		api.register('vertical-marquee', (root) => run(root));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document));
	} else {
		run(document);
	}
})();
