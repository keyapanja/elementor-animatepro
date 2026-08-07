(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '.eap-mega-menu';

	const setup = (nav, editorMode) => {
		if (nav.dataset.eapMmBound === '1') {
			return;
		}
		nav.dataset.eapMmBound = '1';

		const trigger = nav.dataset.trigger || 'hover';
		const breakpoint = parseInt(nav.dataset.breakpoint, 10) || 0;
		const mobileMode = nav.dataset.mobileMode || 'accordion';
		const parsedDelay = parseInt(nav.dataset.hoverDelay, 10);
		const closeDelay = isNaN(parsedDelay) ? 160 : parsedDelay;
		const isFullscreen = mobileMode === 'fullscreen';

		const toggle = nav.querySelector('.eap-mega-menu__toggle');
		const list = nav.querySelector('.eap-mega-menu__list');
		const closeBtn = nav.querySelector('.eap-mega-menu__close');
		const lockScroll = isFullscreen && !editorMode;

		if (!list) {
			return;
		}

		const panelItems = Array.from(list.querySelectorAll(':scope > .eap-mega-menu__item--has-panel'));

		let portal = null;
		let hoverTimer = 0;

		const isMobile = () => nav.classList.contains('is-mobile');

		/* =====================================================================
		 * Desktop — wide-panel sizing + open / close
		 * ================================================================== */

		const panelOf = (item) => item.querySelector(':scope > .eap-mega-menu__panel');

		const sizePanel = (item) => {
			const panel = panelOf(item);
			if (!panel) {
				return;
			}
			if (isMobile()) {
				panel.style.left = '';
				panel.style.width = '';
				panel.style.right = '';
				return;
			}

			const isFull = panel.classList.contains('eap-mega-menu__panel--full');
			const isContainer = panel.classList.contains('eap-mega-menu__panel--container');
			const vw = document.documentElement.clientWidth;

			if (!isFull && !isContainer) {
				// Dropdown: content-sized under the item; flip right if it overflows.
				panel.style.left = '';
				panel.style.width = '';
				panel.style.right = '';
				const r = panel.getBoundingClientRect();
				if (r.right > vw - 4) {
					panel.style.left = 'auto';
					panel.style.right = '0';
				}
				return;
			}

			const itemRect = item.getBoundingClientRect();
			const maxW = parseFloat(getComputedStyle(nav).getPropertyValue('--eap-mm-max-width')) || 0;
			let width;
			let leftViewport;

			if (isFull) {
				// Edge-to-edge viewport bleed (ignores the max-width cap).
				width = vw;
				leftViewport = 0;
			} else {
				// Match the parent container, capped + centred by the max-width.
				const ref = nav.closest('.elementor-widget-container') ||
					nav.closest('.elementor-widget') || nav.parentElement;
				const refRect = ref.getBoundingClientRect();
				width = refRect.width;
				leftViewport = refRect.left;
				if (maxW && width > maxW) {
					leftViewport = refRect.left + (width - maxW) / 2;
					width = maxW;
				}
			}

			panel.style.right = '';
			panel.style.left = Math.round(leftViewport - itemRect.left) + 'px';
			panel.style.width = Math.round(width) + 'px';
		};

		const openDesktop = (item) => {
			sizePanel(item);
			item.classList.add('is-active');
			const link = item.querySelector(':scope > .eap-mega-menu__row > .eap-mega-menu__top-link');
			if (link) {
				link.setAttribute('aria-expanded', 'true');
			}
		};

		const closeDesktop = (item) => {
			item.classList.remove('is-active');
			const link = item.querySelector(':scope > .eap-mega-menu__row > .eap-mega-menu__top-link');
			if (link) {
				link.setAttribute('aria-expanded', 'false');
			}
		};

		const closeAllDesktop = (except) => {
			panelItems.forEach((li) => {
				if (li !== except) {
					closeDesktop(li);
				}
			});
		};

		panelItems.forEach((item) => {
			const link = item.querySelector(':scope > .eap-mega-menu__row > .eap-mega-menu__top-link');

			// Hover trigger — pointer + keyboard.
			item.addEventListener('mouseenter', () => {
				if (isMobile() || trigger !== 'hover') {
					return;
				}
				window.clearTimeout(hoverTimer);
				closeAllDesktop(item);
				openDesktop(item);
			});

			item.addEventListener('mouseleave', () => {
				if (isMobile() || trigger !== 'hover') {
					return;
				}
				window.clearTimeout(hoverTimer);
				hoverTimer = window.setTimeout(() => closeDesktop(item), closeDelay);
			});

			item.addEventListener('focusin', () => {
				if (isMobile() || trigger !== 'hover') {
					return;
				}
				closeAllDesktop(item);
				openDesktop(item);
			});

			item.addEventListener('focusout', (event) => {
				if (isMobile() || trigger !== 'hover') {
					return;
				}
				if (!item.contains(event.relatedTarget)) {
					closeDesktop(item);
				}
			});
		});

		/* =====================================================================
		 * Mobile — hamburger, accordion, portal
		 * ================================================================== */

		const buildPortal = () => {
			if (portal) {
				return;
			}
			const wrapper = nav.closest('.elementor-element');
			portal = document.createElement('div');
			portal.className = 'eap-mega-menu-portal' + (wrapper ? ' ' + wrapper.className : '');
			const portalNav = document.createElement('nav');
			portalNav.className = nav.className;
			portalNav.classList.remove('is-open');
			portal.appendChild(portalNav);
			portal.__nav = portalNav;
			// Append INSIDE the .elementor page wrapper (not <body>) so the
			// {{WRAPPER}}-scoped styles still match, while still escaping any
			// transformed inner container that would trap position:fixed.
			const host = nav.closest('.elementor') || document.body;
			host.appendChild(portal);
			portalNav.appendChild(list);
			if (closeBtn) {
				portalNav.appendChild(closeBtn);
			}
		};

		const destroyPortal = () => {
			if (!portal) {
				return;
			}
			nav.appendChild(list);
			if (closeBtn) {
				nav.appendChild(closeBtn);
			}
			portal.remove();
			portal = null;
		};

		const openMenu = () => {
			if (isFullscreen && isMobile()) {
				buildPortal();
				if (portal) {
					const portalNav = portal.__nav;
					window.requestAnimationFrame(() => portalNav.classList.add('is-open'));
				}
			}
			nav.classList.add('is-open');
			if (toggle) {
				toggle.setAttribute('aria-expanded', 'true');
			}
			if (lockScroll) {
				document.documentElement.style.overflow = 'hidden';
			}
		};

		const collapseAll = () => {
			list.querySelectorAll('.eap-mega-menu__item.is-expanded').forEach((li) => {
				li.classList.remove('is-expanded');
				const st = li.querySelector(':scope > .eap-mega-menu__row > .eap-mega-menu__sub-toggle');
				if (st) {
					st.setAttribute('aria-expanded', 'false');
				}
			});
		};

		const closeMenu = () => {
			nav.classList.remove('is-open');
			if (portal) {
				portal.__nav.classList.remove('is-open');
				destroyPortal();
			}
			if (toggle) {
				toggle.setAttribute('aria-expanded', 'false');
			}
			if (lockScroll) {
				document.documentElement.style.overflow = '';
			}
		};

		const toggleExpand = (item) => {
			const expanded = item.classList.toggle('is-expanded');
			const st = item.querySelector(':scope > .eap-mega-menu__row > .eap-mega-menu__sub-toggle');
			if (st) {
				st.setAttribute('aria-expanded', expanded ? 'true' : 'false');
			}
		};

		if (toggle) {
			toggle.addEventListener('click', () => {
				if (nav.classList.contains('is-open')) {
					closeMenu();
				} else {
					openMenu();
				}
			});
		}

		if (closeBtn) {
			closeBtn.addEventListener('click', (event) => {
				event.preventDefault();
				closeMenu();
			});
		}

		// Accordion sub-toggles.
		list.querySelectorAll('.eap-mega-menu__sub-toggle').forEach((subToggle) => {
			subToggle.addEventListener('click', (event) => {
				event.preventDefault();
				const li = subToggle.closest('.eap-mega-menu__item');
				if (li) {
					toggleExpand(li);
				}
			});
		});

		// Top-link behaviour (branches on mobile vs desktop / trigger).
		panelItems.forEach((item) => {
			const link = item.querySelector(':scope > .eap-mega-menu__row > .eap-mega-menu__top-link');
			if (!link) {
				return;
			}
			link.addEventListener('click', (event) => {
				const href = link.getAttribute('href');
				const real = href && href !== '#';

				if (isMobile()) {
					if (!real) {
						event.preventDefault();
						toggleExpand(item);
					} else {
						closeMenu();
					}
					return;
				}

				if (trigger !== 'click') {
					return;
				}
				if (!item.classList.contains('is-active')) {
					event.preventDefault();
					closeAllDesktop(item);
					openDesktop(item);
				} else if (!real) {
					event.preventDefault();
					closeDesktop(item);
				}
			});
		});

		// Close the mobile menu after tapping a real link inside a panel.
		list.addEventListener('click', (event) => {
			if (!isMobile() || !nav.classList.contains('is-open')) {
				return;
			}
			const link = event.target.closest('.eap-mega-menu__link, .eap-mega-menu__promo-btn, .eap-mega-menu__col-heading a');
			if (!link) {
				return;
			}
			const href = link.getAttribute('href');
			if (href && href !== '#') {
				closeMenu();
			}
		});

		// Outside click / Escape.
		document.addEventListener('click', (event) => {
			if (nav.contains(event.target) || (portal && portal.contains(event.target))) {
				return;
			}
			if (isMobile()) {
				if (nav.classList.contains('is-open')) {
					closeMenu();
				}
			} else {
				closeAllDesktop(null);
			}
		});

		document.addEventListener('keydown', (event) => {
			if (event.key !== 'Escape') {
				return;
			}
			if (isMobile() && nav.classList.contains('is-open')) {
				closeMenu();
				if (toggle) {
					toggle.focus();
				}
			} else if (!isMobile()) {
				closeAllDesktop(null);
			}
		});

		/* =====================================================================
		 * Responsive switch
		 * ================================================================== */

		const clearPanelSizing = () => {
			panelItems.forEach((item) => {
				const panel = panelOf(item);
				if (panel) {
					panel.style.left = '';
					panel.style.width = '';
					panel.style.right = '';
				}
			});
		};

		const evaluate = () => {
			const mobile = breakpoint > 0 && window.innerWidth <= breakpoint;
			const was = nav.classList.contains('is-mobile');
			nav.classList.toggle('is-mobile', mobile);

			if (mobile) {
				closeAllDesktop(null);
				clearPanelSizing();
			} else if (was) {
				closeMenu();
				collapseAll();
			}
		};
		evaluate();

		let resizeTimer = 0;
		window.addEventListener('resize', () => {
			window.clearTimeout(resizeTimer);
			resizeTimer = window.setTimeout(() => {
				evaluate();
				if (!isMobile()) {
					// Re-measure any panel that is currently open.
					nav.querySelectorAll('.eap-mega-menu__item.is-active').forEach(sizePanel);
				}
			}, 150);
		});
	};

	const run = (root, editorMode) => {
		const nodes = (api && typeof api.getNodes === 'function')
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((nav) => setup(nav, editorMode));
	};

	if (api && typeof api.register === 'function') {
		api.register('mega-menu', (root, options = {}) => run(root, !!options.editorMode));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document, false));
	} else {
		run(document, false);
	}
})();
