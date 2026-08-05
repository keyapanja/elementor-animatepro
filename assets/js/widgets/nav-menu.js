(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '.eap-nav-menu';

	const setup = (nav, editorMode) => {
		if (nav.dataset.eapNmBound === '1') {
			return;
		}
		nav.dataset.eapNmBound = '1';

		const breakpoint = parseInt(nav.dataset.breakpoint, 10) || 0;
		const mobileMode = nav.dataset.mobileMode || 'dropdown';
		const toggle = nav.querySelector('.eap-nav-menu__toggle');
		const list = nav.querySelector('.eap-nav-menu__list');
		const isFullscreen = mobileMode === 'fullscreen';
		const closeBtn = nav.querySelector('.eap-nav-menu__close');
		const lockScroll = isFullscreen && !editorMode;

		let portal = null;

		// Portal the overlay to <body> so its position:fixed is viewport-relative
		// (not trapped by a transformed Elementor container) and truly covers the
		// whole screen. The portal carries the widget wrapper class + the nav
		// classes, so both the {{WRAPPER}}-scoped styles and the structural CSS
		// still apply to the moved list/close button.
		const buildPortal = () => {
			if (portal) {
				return;
			}
			const wrapper = nav.closest('.elementor-element');
			portal = document.createElement('div');
			portal.className = 'eap-nav-menu-portal' + (wrapper ? ' ' + wrapper.className : '');
			const portalNav = document.createElement('nav');
			portalNav.className = nav.className;
			portalNav.classList.remove('is-open');
			portal.appendChild(portalNav);
			portal.__nav = portalNav;
			// Append INSIDE the .elementor page wrapper (not <body>): Elementor
			// scopes widget CSS as `.elementor-{postId} .elementor-element-{id} …`,
			// so the portal must stay within `.elementor-{postId}` for those styles
			// (alignment, colours) to apply. The page wrapper itself is not
			// transformed, so position:fixed is still viewport-relative and covers
			// the whole screen (the transform trap is on an inner container).
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
			if (isFullscreen && nav.classList.contains('is-mobile')) {
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

		const collapseAll = () => {
			nav.querySelectorAll('.eap-nav-menu__item.is-expanded').forEach((li) => {
				li.classList.remove('is-expanded');
				const st = li.querySelector('.eap-nav-menu__sub-toggle');
				if (st) {
					st.setAttribute('aria-expanded', 'false');
				}
			});
		};

		// Add/remove the mobile layout class based on the chosen breakpoint.
		const evaluate = () => {
			const mobile = breakpoint > 0 && window.innerWidth <= breakpoint;
			nav.classList.toggle('is-mobile', mobile);
			if (!mobile) {
				closeMenu();
				collapseAll();
			}
		};
		evaluate();

		let resizeTimer = 0;
		window.addEventListener('resize', () => {
			window.clearTimeout(resizeTimer);
			resizeTimer = window.setTimeout(evaluate, 150);
		});

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

		// Accordion submenu toggles (mobile).
		nav.querySelectorAll('.eap-nav-menu__sub-toggle').forEach((subToggle) => {
			subToggle.addEventListener('click', (event) => {
				event.preventDefault();
				const li = subToggle.closest('.eap-nav-menu__item');
				if (!li) {
					return;
				}
				const expanded = li.classList.toggle('is-expanded');
				subToggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
			});
		});

		// Close the mobile menu after tapping a real navigation link.
		if (list) {
			list.addEventListener('click', (event) => {
				const link = event.target.closest('.eap-nav-menu__link');
				if (!link || !nav.classList.contains('is-mobile') || !nav.classList.contains('is-open')) {
					return;
				}
				const href = link.getAttribute('href');
				if (href && href !== '#') {
					closeMenu();
				}
			});
		}

		// Escape closes the mobile menu and returns focus to the toggle.
		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape' && nav.classList.contains('is-open')) {
				closeMenu();
				if (toggle) {
					toggle.focus();
				}
			}
		});
	};

	const run = (root, editorMode) => {
		const nodes = (api && typeof api.getNodes === 'function')
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((nav) => setup(nav, editorMode));
	};

	if (api && typeof api.register === 'function') {
		api.register('nav-menu', (root, options = {}) => run(root, !!options.editorMode));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document, false));
	} else {
		run(document, false);
	}
})();
