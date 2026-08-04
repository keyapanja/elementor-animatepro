/* global EAPFrontend */
(() => {
	const SELECTOR = '.eap-services-tabs';

	const initServicesTabs = (root) => {
		if (!root || !window.EAPFrontend) {
			return;
		}

		EAPFrontend.getNodes(root, SELECTOR).forEach((widget) => {
			if (widget.dataset.eapServicesTabsBound === '1') {
				return;
			}
			widget.dataset.eapServicesTabsBound = '1';

			const trigger = widget.dataset.trigger || 'hover';
			const autoplayDelay = parseInt(widget.dataset.autoplayDelay, 10) || 3500;
			const pauseOnHover = widget.dataset.pauseOnHover === '1';
			const items = Array.from(widget.querySelectorAll('.eap-services-tabs__item'));
			const images = Array.from(widget.querySelectorAll('.eap-services-tabs__image'));

			if (!items.length) {
				return;
			}

			let activeIndex = parseInt(widget.dataset.defaultActive, 10);
			if (Number.isNaN(activeIndex) || activeIndex < 0 || activeIndex >= items.length) {
				activeIndex = items.findIndex((item) => item.classList.contains('is-active'));
				if (activeIndex < 0) {
					activeIndex = 0;
				}
			}

			const setActive = (next) => {
				if (next < 0) {
					next = items.length - 1;
				} else if (next >= items.length) {
					next = 0;
				}

				if (next === activeIndex && items[next].classList.contains('is-active')) {
					return;
				}

				items.forEach((item, idx) => {
					const isActive = idx === next;
					item.classList.toggle('is-active', isActive);
					item.setAttribute('aria-selected', isActive ? 'true' : 'false');
					item.setAttribute('tabindex', isActive ? '0' : '-1');
				});

				images.forEach((image, idx) => {
					const isActive = idx === next;
					image.classList.toggle('is-active', isActive);
					image.setAttribute('aria-hidden', isActive ? 'false' : 'true');
				});

				activeIndex = next;
			};

			// Apply initial state in case markup is stale.
			setActive(activeIndex);

			/* --------------------------------------------------------------- *
			 * Hover trigger
			 * --------------------------------------------------------------- */
			if (trigger === 'hover') {
				items.forEach((item, idx) => {
					item.addEventListener('mouseenter', () => setActive(idx));
					item.addEventListener('focus', () => setActive(idx));
				});
			}

			/* --------------------------------------------------------------- *
			 * Click trigger
			 * --------------------------------------------------------------- */
			if (trigger === 'click') {
				items.forEach((item, idx) => {
					item.addEventListener('click', (event) => {
						event.preventDefault();
						setActive(idx);
					});
				});
			}

			/* --------------------------------------------------------------- *
			 * Keyboard a11y (always on)
			 * --------------------------------------------------------------- */
			items.forEach((item, idx) => {
				item.addEventListener('keydown', (event) => {
					switch (event.key) {
						case 'ArrowDown':
						case 'ArrowRight':
							event.preventDefault();
							setActive(idx + 1);
							items[activeIndex].focus();
							break;
						case 'ArrowUp':
						case 'ArrowLeft':
							event.preventDefault();
							setActive(idx - 1);
							items[activeIndex].focus();
							break;
						case 'Home':
							event.preventDefault();
							setActive(0);
							items[activeIndex].focus();
							break;
						case 'End':
							event.preventDefault();
							setActive(items.length - 1);
							items[activeIndex].focus();
							break;
						case 'Enter':
						case ' ':
							if (trigger !== 'hover') {
								event.preventDefault();
								setActive(idx);
							}
							break;
						default:
							break;
					}
				});
			});

			/* --------------------------------------------------------------- *
			 * Auto trigger
			 * --------------------------------------------------------------- */
			if (trigger === 'auto') {
				let timerId = null;
				let paused = false;

				const tick = () => {
					if (paused) {
						return;
					}
					setActive(activeIndex + 1);
				};

				const start = () => {
					stop();
					timerId = window.setInterval(tick, autoplayDelay);
				};

				const stop = () => {
					if (timerId) {
						window.clearInterval(timerId);
						timerId = null;
					}
				};

				if (pauseOnHover) {
					widget.addEventListener('mouseenter', () => {
						paused = true;
					});
					widget.addEventListener('mouseleave', () => {
						paused = false;
					});
				}

				// Pause when tab is hidden to avoid burning CPU on background tabs.
				document.addEventListener('visibilitychange', () => {
					if (document.hidden) {
						stop();
					} else {
						start();
					}
				});

				// Also allow clicking to jump (without disabling autoplay).
				items.forEach((item, idx) => {
					item.addEventListener('click', (event) => {
						event.preventDefault();
						setActive(idx);
					});
				});

				start();
			}
		});
	};

	if (window.EAPFrontend && typeof window.EAPFrontend.register === 'function') {
		window.EAPFrontend.register('services-tabs', (root) => initServicesTabs(root));
	} else {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', () => initServicesTabs(document));
		} else {
			initServicesTabs(document);
		}
	}
})();
