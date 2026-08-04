(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const reduceMotion = () => typeof api.prefersReducedMotion === 'function' && api.prefersReducedMotion();

	const setupEntrance = (el, editorMode) => {
		if (!/eap-image-accordion--anim-/.test(el.className)) {
			return;
		}

		if (editorMode || reduceMotion()) {
			el.classList.add('is-visible');
			return;
		}

		if (typeof api.isInViewport === 'function' && api.isInViewport(el)) {
			window.requestAnimationFrame(() => el.classList.add('is-visible'));
			return;
		}

		if ('IntersectionObserver' in window) {
			const observer = new IntersectionObserver((entries, obs) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						obs.unobserve(entry.target);
					}
				});
			}, { threshold: 0.15 });
			observer.observe(el);
			return;
		}

		el.classList.add('is-visible');
	};

	const setup = (el, editorMode) => {
		if (el.dataset.eapIaBound === 'true') {
			return;
		}
		el.dataset.eapIaBound = 'true';

		const trigger = el.dataset.trigger || 'hover';
		const items = Array.from(el.querySelectorAll('[data-eap-accordion-item]'));
		if (!items.length) {
			return;
		}

		let defaultIndex = parseInt(el.dataset.defaultIndex || '0', 10) || 0;
		if (defaultIndex < 0 || defaultIndex >= items.length) {
			defaultIndex = 0;
		}

		const setActive = (index) => {
			items.forEach((item, i) => item.classList.toggle('is-active', i === index));
		};

		if (trigger === 'click') {
			items.forEach((item, i) => {
				item.addEventListener('click', (event) => {
					// Don't hijack clicks on the actual button/link inside the panel.
					if (event.target.closest('a')) {
						return;
					}
					setActive(i);
				});
				item.addEventListener('keydown', (event) => {
					if (event.key === 'Enter' || event.key === ' ') {
						event.preventDefault();
						setActive(i);
					}
				});
			});
		} else {
			const hoverRest = el.dataset.hoverRest || 'default';
			items.forEach((item, i) => {
				item.addEventListener('mouseenter', () => setActive(i));
				item.addEventListener('focusin', () => setActive(i));
			});
			// 'default' restores the default panel on leave; 'last' keeps the
			// most recently hovered panel open.
			if (hoverRest !== 'last') {
				el.addEventListener('mouseleave', () => setActive(defaultIndex));
			}
		}

		setActive(defaultIndex);
		setupEntrance(el, editorMode);
	};

	api.register('image-accordion', (root, { editorMode = false } = {}) => {
		api.getNodes(root, '[data-eap-image-accordion]').forEach((el) => setup(el, editorMode));
	});
})();
