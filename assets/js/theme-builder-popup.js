/*
 * Theme Builder popups — triggers, close rules and how often one may show.
 *
 * Every popup that matches the page is already in the markup, inert, so this
 * only decides when one opens. Frequency is remembered per visitor in storage;
 * a visitor who has blocked storage simply sees the popup again, which is the
 * safe way for that to fail.
 */
(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '[data-eap-popup]';
	const STORE_PREFIX = 'eap-popup-';

	const reduced = () => (api && typeof api.prefersReducedMotion === 'function'
		? api.prefersReducedMotion()
		: window.matchMedia('(prefers-reduced-motion: reduce)').matches);

	const readStore = (key) => {
		try {
			return window.localStorage.getItem(key);
		} catch (error) {
			return null;
		}
	};

	const writeStore = (key, value) => {
		try {
			window.localStorage.setItem(key, value);
		} catch (error) {
			// A visitor with storage blocked just sees it again.
		}
	};

	const readSession = (key) => {
		try {
			return window.sessionStorage.getItem(key);
		} catch (error) {
			return null;
		}
	};

	const writeSession = (key, value) => {
		try {
			window.sessionStorage.setItem(key, value);
		} catch (error) {
			// As above.
		}
	};

	const FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

	const setup = (root) => {
		if (root.dataset.eapPopupBound === '1') {
			return;
		}
		root.dataset.eapPopupBound = '1';

		let cfg = {};
		try {
			cfg = JSON.parse(root.getAttribute('data-eap-popup') || '{}');
		} catch (error) {
			cfg = {};
		}

		const id = root.getAttribute('data-eap-popup-id') || '0';
		const key = STORE_PREFIX + id;
		const box = root.querySelector('.eap-popup__box');
		const overlay = root.querySelector('[data-eap-popup-overlay]');
		const closeButton = root.querySelector('[data-eap-popup-close]');

		let lastFocus = null;
		let autoCloseTimer = null;
		let isOpen = false;

		/* Frequency ------------------------------------------------------- */

		const alreadySeen = () => {
			if (cfg.frequency === 'session') {
				return readSession(key) === '1';
			}

			if (cfg.frequency === 'days') {
				const until = parseInt(readStore(key) || '0', 10);
				return Number.isFinite(until) && until > Date.now();
			}

			return false;
		};

		const remember = () => {
			if (cfg.frequency === 'session') {
				writeSession(key, '1');
				return;
			}

			if (cfg.frequency === 'days') {
				const days = Math.max(1, parseInt(cfg.days, 10) || 7);
				writeStore(key, String(Date.now() + days * 86400000));
			}
		};

		/* Open and close -------------------------------------------------- */

		const focusInside = () => {
			const target = box.querySelector(FOCUSABLE) || box;
			if (!target.hasAttribute('tabindex') && target === box) {
				box.setAttribute('tabindex', '-1');
			}
			target.focus({ preventScroll: true });
		};

		const trapFocus = (event) => {
			if (event.key !== 'Tab' || !isOpen) {
				return;
			}

			const items = Array.from(box.querySelectorAll(FOCUSABLE)).filter((node) => node.offsetParent !== null);
			if (!items.length) {
				return;
			}

			const first = items[0];
			const last = items[items.length - 1];

			if (event.shiftKey && document.activeElement === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		};

		const close = () => {
			if (!isOpen) {
				return;
			}
			isOpen = false;

			window.clearTimeout(autoCloseTimer);
			root.classList.remove('is-open');
			document.body.classList.remove('eap-popup-open');

			const finish = () => {
				if (!isOpen) {
					root.hidden = true;
				}
			};

			if (cfg.animation === 'none' || reduced()) {
				finish();
			} else {
				window.setTimeout(finish, 240);
			}

			document.removeEventListener('keydown', onKeydown);

			if (lastFocus && typeof lastFocus.focus === 'function') {
				lastFocus.focus({ preventScroll: true });
			}
		};

		const onKeydown = (event) => {
			if (event.key === 'Escape' && cfg.closeEsc) {
				close();
				return;
			}

			trapFocus(event);
		};

		const open = () => {
			if (isOpen) {
				return;
			}
			isOpen = true;

			lastFocus = document.activeElement;
			root.hidden = false;

			// Force the browser to acknowledge the closed state so the
			// transition has somewhere to animate from. requestAnimationFrame
			// would do the same, but it does not fire in a background tab —
			// which would leave the popup displayed, scroll locked and holding
			// focus while still fully transparent.
			void root.offsetHeight;

			root.classList.add('is-open');

			document.body.classList.add('eap-popup-open');
			document.addEventListener('keydown', onKeydown);
			focusInside();
			remember();

			const auto = parseFloat(cfg.autoClose) || 0;
			if (auto > 0) {
				autoCloseTimer = window.setTimeout(close, auto * 1000);
			}
		};

		if (closeButton) {
			closeButton.addEventListener('click', close);
		}

		if (overlay && cfg.closeOverlay) {
			overlay.addEventListener('click', close);
		}

		/* Triggers -------------------------------------------------------- */

		// A click trigger is the one kind that should still work after the
		// visitor has dismissed it — they asked for it by clicking.
		if (cfg.trigger === 'click') {
			document.addEventListener('click', (event) => {
				const target = event.target;
				if (target instanceof Element && target.closest(cfg.selector)) {
					event.preventDefault();
					open();
				}
			});
			return;
		}

		if (alreadySeen()) {
			return;
		}

		if (cfg.trigger === 'load') {
			open();
			return;
		}

		if (cfg.trigger === 'delay') {
			window.setTimeout(open, Math.max(0, (parseFloat(cfg.delay) || 0) * 1000));
			return;
		}

		if (cfg.trigger === 'scroll') {
			const wanted = Math.min(100, Math.max(1, parseInt(cfg.scroll, 10) || 40));

			const onScroll = () => {
				const scrollable = document.documentElement.scrollHeight - window.innerHeight;

				// A page too short to scroll can never reach a percentage, so
				// treat it as already there rather than never firing.
				const percent = scrollable <= 0
					? 100
					: (window.scrollY / scrollable) * 100;

				if (percent >= wanted) {
					window.removeEventListener('scroll', onScroll);
					open();
				}
			};

			window.addEventListener('scroll', onScroll, { passive: true });
			onScroll();
			return;
		}

		if (cfg.trigger === 'inactivity') {
			const idle = Math.max(1, parseInt(cfg.inactivity, 10) || 30) * 1000;
			let timer = window.setTimeout(open, idle);

			const bump = () => {
				window.clearTimeout(timer);
				timer = window.setTimeout(open, idle);
			};

			['mousemove', 'keydown', 'scroll', 'touchstart', 'click'].forEach((name) => {
				document.addEventListener(name, bump, { passive: true });
			});
			return;
		}

		if (cfg.trigger === 'exit') {
			const onLeave = (event) => {
				// Only the top edge means leaving for the tab bar; sideways is
				// usually just reaching for a scrollbar.
				if (event.clientY > 0 || event.relatedTarget) {
					return;
				}

				document.removeEventListener('mouseout', onLeave);
				open();
			};

			document.addEventListener('mouseout', onLeave);
		}
	};

	const init = (root) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));

		nodes.forEach(setup);
	};

	if (api && typeof api.register === 'function') {
		api.register('theme-builder-popup', init);
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => init(document));
	} else {
		init(document);
	}
})();
