/* global EAPFrontend */
(() => {
	const SELECTOR = '.eap-onepage-nav';

	const initOnePageNav = (root) => {
		if (!root || !window.EAPFrontend) {
			return;
		}

		EAPFrontend.getNodes(root, SELECTOR).forEach((widget) => {
			if (widget.dataset.eapOnePageNavBound === '1') {
				return;
			}
			widget.dataset.eapOnePageNavBound = '1';

			const stickyMode = widget.dataset.stickyMode || 'page';
			const containerSelector = widget.dataset.container || '';
			const smoothScroll = widget.dataset.smoothScroll === '1';
			const scrollOffset = parseInt(widget.dataset.scrollOffset, 10) || 0;
			const activeOffsetPercent = parseInt(widget.dataset.activeOffset, 10) || 35;
			const items = Array.from(widget.querySelectorAll('.eap-onepage-nav__item'));
			const links = Array.from(widget.querySelectorAll('[data-eap-onepage-link]'));

			if (!items.length) {
				return;
			}

			/* --------------------------------------------------------------- *
			 * Resolve scope container — for section-only mode the JS watches
			 * this container's visibility and only activates nav items for
			 * sections inside it. The widget itself is position:fixed at the
			 * viewport edge (identical to whole-page mode), it just hides
			 * when the container is offscreen.
			 * --------------------------------------------------------------- */
			let scopeContainer = null;
			if (stickyMode === 'section') {
				if (containerSelector) {
					try {
						scopeContainer = document.querySelector(containerSelector);
					} catch (e) {
						scopeContainer = null;
					}
				}

				// Smart fallback: use the first nav target's nearest section
				// ancestor. This is almost always the right scope when items
				// point to inner sub-sections (e.g. #feature-1, #feature-2).
				if (!scopeContainer && items.length) {
					const firstTarget = items[0].getAttribute('data-target');
					let firstSection = null;
					if (firstTarget) {
						try {
							firstSection = document.querySelector(firstTarget);
						} catch (e) {
							firstSection = null;
						}
					}
					if (firstSection) {
						scopeContainer = firstSection.parentElement && firstSection.parentElement.closest(
							'.elementor-top-section, .elementor-section, .e-con-outer, .e-con, section[id], section'
						);
					}
				}

				// If we still have nothing, leave scopeContainer null — the
				// nav simply stays hidden, which is the safer default than
				// promoting an unrelated ancestor to "the section".
				if (!scopeContainer && window.console && typeof window.console.warn === 'function') {
					window.console.warn('[EAP One Page Nav] Section Only mode is on but no container could be resolved. Set the "Section Container Selector" field to fix this.');
				}
			}

			/* --------------------------------------------------------------- *
			 * Resolve target sections for each nav item. In section-only mode,
			 * only count targets that exist inside the scope container so the
			 * nav reflects sections within that scope.
			 * --------------------------------------------------------------- */
			const itemMap = []; // { item, section }
			items.forEach((item) => {
				const targetSelector = item.getAttribute('data-target');
				if (!targetSelector) {
					return;
				}

				let section = null;
				try {
					const candidates = (stickyMode === 'section' && scopeContainer)
						? scopeContainer.querySelectorAll(targetSelector)
						: document.querySelectorAll(targetSelector);
					if (candidates.length) {
						section = candidates[0];
					}
				} catch (e) {
					section = null;
				}

				itemMap.push({ item, section });
			});

			const orderedSections = itemMap
				.filter((entry) => entry.section)
				.map((entry) => entry.section);

			/* --------------------------------------------------------------- *
			 * Active section detection
			 * --------------------------------------------------------------- */
			const setActiveItem = (item) => {
				items.forEach((other) => {
					const isActive = other === item;
					other.classList.toggle('is-active', isActive);
					const link = other.querySelector('.eap-onepage-nav__link');
					if (link) {
						link.setAttribute('aria-current', isActive ? 'true' : 'false');
					}
				});
			};

			const findActiveSection = () => {
				if (!orderedSections.length) {
					return null;
				}

				const triggerY = window.innerHeight * (activeOffsetPercent / 100);
				let active = null;

				orderedSections.forEach((section) => {
					const rect = section.getBoundingClientRect();
					if (rect.top <= triggerY && rect.bottom > triggerY) {
						active = section;
					}
				});

				// If nothing is straddling the trigger line, fall back to the
				// last section whose top has scrolled past — keeps a sensible
				// active state between gaps or beyond the final section.
				if (!active) {
					orderedSections.forEach((section) => {
						if (section.getBoundingClientRect().top <= triggerY) {
							active = section;
						}
					});
				}

				return active;
			};

			const updateActive = () => {
				const activeSection = findActiveSection();
				if (!activeSection) {
					return;
				}
				const match = itemMap.find((entry) => entry.section === activeSection);
				if (match) {
					setActiveItem(match.item);
				}
			};

			/* --------------------------------------------------------------- *
			 * Throttled scroll handler via rAF
			 * --------------------------------------------------------------- */
			let scrollFrame = 0;
			const onScroll = () => {
				if (scrollFrame) {
					return;
				}
				scrollFrame = window.requestAnimationFrame(() => {
					scrollFrame = 0;
					updateActive();
				});
			};

			window.addEventListener('scroll', onScroll, { passive: true });
			window.addEventListener('resize', onScroll);

			// Initial pass after layout settles.
			window.requestAnimationFrame(updateActive);

			/* --------------------------------------------------------------- *
			 * Click handling — smooth scroll + active swap
			 * --------------------------------------------------------------- */
			links.forEach((link) => {
				link.addEventListener('click', (event) => {
					const item = link.closest('.eap-onepage-nav__item');
					if (!item) {
						return;
					}
					const entry = itemMap.find((candidate) => candidate.item === item);
					if (!entry || !entry.section) {
						return;
					}

					event.preventDefault();
					setActiveItem(item);

					if (smoothScroll) {
						const rect = entry.section.getBoundingClientRect();
						const targetY = window.scrollY + rect.top - scrollOffset;
						window.scrollTo({ top: targetY, behavior: 'smooth' });
					} else {
						entry.section.scrollIntoView({ block: 'start' });
					}
				});
			});

			/* --------------------------------------------------------------- *
			 * Section-only mode: toggle widget visibility based on whether
			 * the scope container is currently in the viewport. The widget
			 * appears fixed at its chosen edge while the section is on-screen
			 * and hides as soon as the user scrolls past it.
			 * --------------------------------------------------------------- */
			if (stickyMode === 'section' && scopeContainer) {
				const setInSection = (isIn) => {
					widget.classList.toggle('is-in-section', isIn);
				};

				// Scroll-driven check (more deterministic than IntersectionObserver
				// for tight thresholds — fires synchronously with the user's
				// scroll input). The section counts as "in view" only when its
				// top has scrolled into the upper half of the viewport AND its
				// bottom is still below the same line. That way the nav appears
				// the moment you actually reach the section, and disappears the
				// moment you scroll past it — no peeking, no lag.
				const checkSectionVisibility = () => {
					const rect = scopeContainer.getBoundingClientRect();
					const vh = window.innerHeight || document.documentElement.clientHeight;
					// Sentinel line at activeOffsetPercent (same threshold used
					// for active section detection — keeps both behaviors aligned).
					const sentinel = vh * (activeOffsetPercent / 100);
					const isIn = rect.top <= sentinel && rect.bottom > sentinel;
					setInSection(isIn);
				};

				let visFrame = 0;
				const onVisScroll = () => {
					if (visFrame) {
						return;
					}
					visFrame = window.requestAnimationFrame(() => {
						visFrame = 0;
						checkSectionVisibility();
					});
				};

				window.addEventListener('scroll', onVisScroll, { passive: true });
				window.addEventListener('resize', onVisScroll);
				window.requestAnimationFrame(checkSectionVisibility);
			}
		});
	};

	if (window.EAPFrontend && typeof window.EAPFrontend.register === 'function') {
		window.EAPFrontend.register('one-page-nav', (root) => initOnePageNav(root));
	} else {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', () => initOnePageNav(document));
		} else {
			initOnePageNav(document);
		}
	}
})();
