/*
 * Flip Box — one flipped state for every input.
 *
 * Mouse: hover flips (pointerenter / pointerleave, mouse only).
 * Touch: a tap flips — hover does not exist there, so a hover box is tappable.
 * Keyboard: the box is focusable; Enter or Space flips, Escape flips back.
 * Click mode: a click on the box flips it; a click elsewhere flips it back.
 *
 * The face that is NOT showing is made `inert`, so keyboard focus and screen
 * readers only meet what can be seen. Links and buttons inside a face keep
 * working: a click on one never counts as a flip.
 */
(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '[data-eap-flip-box]';
	const INTERACTIVE = 'a, button, input, select, textarea, label, summary, [role="button"], [contenteditable="true"]';

	const setup = (root) => {
		if (root.dataset.eapFbBound === '1') {
			return;
		}
		root.dataset.eapFbBound = '1';

		let cfg = { trigger: 'hover', heightMode: 'auto', lock: false };
		try {
			cfg = Object.assign(cfg, JSON.parse(root.getAttribute('data-eap-flip-box') || '{}'));
		} catch (error) {
			// Keep the defaults.
		}

		const inner = root.querySelector('.eap-fb__inner');
		const front = root.querySelector('.eap-fb__face--front');
		const back = root.querySelector('.eap-fb__face--back');
		if (!inner || !front || !back) {
			return;
		}

		let flipped = root.classList.contains('is-flipped');
		let hovering = false;
		let pointerType = '';

		/* --- "Fit the side showing" height ------------------------------- */

		// A face stretches to the shared cell, so its own height says nothing
		// about its content; its body's height plus the face's padding does.
		const natural = (face) => {
			const body = face.querySelector('.eap-fb__body');
			const cs = window.getComputedStyle(face);
			const box = ['paddingTop', 'paddingBottom', 'borderTopWidth', 'borderBottomWidth']
				.reduce((sum, prop) => sum + (parseFloat(cs[prop]) || 0), 0);
			return Math.ceil((body ? body.getBoundingClientRect().height : 0) + box);
		};

		const fit = () => {
			if (cfg.heightMode !== 'visible') {
				return;
			}
			inner.style.height = `${natural(flipped ? back : front)}px`;
		};

		/* --- State --------------------------------------------------------- */

		const setInert = (el, on) => {
			if ('inert' in el) {
				el.inert = on;
			} else if (on) {
				el.setAttribute('aria-hidden', 'true');
			} else {
				el.removeAttribute('aria-hidden');
			}
		};

		const apply = () => {
			root.classList.toggle('is-flipped', flipped);
			setInert(front, flipped);
			setInert(back, !flipped);
			fit();
		};

		const set = (next) => {
			if (next === flipped) {
				return;
			}
			flipped = next;
			apply();
		};

		root.eapFlipBox = {
			set,
			get flipped() {
				return flipped;
			}
		};

		// The editor's "Show back" preview holds the back in view.
		if (cfg.lock) {
			flipped = true;
			root.classList.add('is-ready');
			apply();
			return;
		}

		/* --- Inputs -------------------------------------------------------- */

		root.addEventListener('pointerdown', (event) => {
			pointerType = event.pointerType;
		});

		root.addEventListener('pointerenter', (event) => {
			if (cfg.trigger === 'hover' && event.pointerType === 'mouse') {
				hovering = true;
				set(true);
			}
		});

		root.addEventListener('pointerleave', (event) => {
			if (cfg.trigger !== 'hover' || event.pointerType !== 'mouse') {
				return;
			}
			hovering = false;
			// A keyboard user working inside the back keeps it.
			if (!back.contains(document.activeElement)) {
				set(false);
			}
		});

		root.addEventListener('click', (event) => {
			if (event.target.closest(INTERACTIVE)) {
				return;
			}
			// Mouse hover already decided; a click on top would undo it.
			if (cfg.trigger === 'hover' && pointerType === 'mouse') {
				return;
			}
			set(!flipped);
		});

		root.addEventListener('keydown', (event) => {
			if (event.key === 'Escape' && flipped) {
				set(false);
				root.focus();
				return;
			}
			if (event.target !== root) {
				return;
			}
			if (event.key === 'Enter' || event.key === ' ') {
				event.preventDefault();
				set(!flipped);
			}
		});

		// Hover mode: keyboard focus leaving the box entirely flips it back,
		// unless the mouse is still over it.
		root.addEventListener('focusout', (event) => {
			if (cfg.trigger === 'hover' && !hovering && !root.contains(event.relatedTarget)) {
				set(false);
			}
		});

		if (cfg.trigger === 'click') {
			document.addEventListener('click', (event) => {
				if (flipped && !root.contains(event.target)) {
					set(false);
				}
			});
		}

		if (cfg.heightMode === 'visible' && typeof window.ResizeObserver === 'function') {
			const observer = new window.ResizeObserver(() => fit());
			root.querySelectorAll('.eap-fb__body').forEach((body) => observer.observe(body));
		}

		root.classList.add('is-ready');
		apply();
	};

	const run = (root) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((node) => setup(node));
	};

	if (api && typeof api.register === 'function') {
		api.register('flip-box', (root) => run(root));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document));
	} else {
		run(document);
	}
})();
