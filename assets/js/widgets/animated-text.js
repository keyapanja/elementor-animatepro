(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const initAnimatedText = (root, editorMode = false) => {
		api.getNodes(root, '.eap-animated-text-v2').forEach((element) => {
			if (element.dataset.eapAnimatedTextV2Init === 'true') {
				if (editorMode) {
					element.classList.add('is-visible', 'is-animated');
				}
				return;
			}

			element.dataset.eapAnimatedTextV2Init = 'true';
			const units = element.querySelectorAll('.eap-animated-text-v2__unit');
			const animation = element.dataset.eapAnimation || 'fade';
			const duration = parseInt(element.dataset.eapDuration || '850', 10);
			const delay = parseInt(element.dataset.eapDelay || '0', 10);
			const stagger = parseInt(element.dataset.eapStagger || '55', 10);

			element.style.setProperty('--eap-at-duration', `${duration}ms`);
			element.style.setProperty('--eap-at-delay', `${delay}ms`);
			units.forEach((unit, index) => {
				const unitDelay = delay + ('split' === animation ? index * stagger : 0);
				unit.style.setProperty('--eap-at-piece-delay', `${unitDelay}ms`);
			});

			element.classList.add('is-eap-ready');

			if ('none' === animation || editorMode) {
				window.requestAnimationFrame(() => element.classList.add('is-visible', 'is-animated'));
			} else {
				window.requestAnimationFrame(() => {
					if (api.isInViewport(element)) {
						api.markElementVisible(element);
					}
				});

				window.setTimeout(() => {
					if (!element.classList.contains('is-visible') && api.isInViewport(element)) {
						api.markElementVisible(element);
					}
				}, 120);
			}
		});
	};

	api.register('animated-text', (root, { editorMode = false } = {}) => {
		initAnimatedText(root, editorMode);
	});
})();
