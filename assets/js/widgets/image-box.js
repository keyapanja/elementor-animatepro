(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const initImageBoxes = (root, editorMode = false) => {
		api.getNodes(root, '.eap-image-box').forEach((element) => {
			if (element.dataset.eapImageBoxInit !== 'true') {
				element.dataset.eapImageBoxInit = 'true';
				if (element.classList.contains('eap-image-box--pointer')) {
					const mediaWrap = element.querySelector('.eap-image-box-media-wrap');
					const tooltip = element.querySelector('.eap-image-box-tooltip');
					if (mediaWrap && tooltip) {
						mediaWrap.addEventListener('mouseenter', () => element.classList.add('is-pointer-active'));
						mediaWrap.addEventListener('mouseleave', () => element.classList.remove('is-pointer-active'));
						mediaWrap.addEventListener('mousemove', (event) => {
							const rect = mediaWrap.getBoundingClientRect();
							tooltip.style.left = `${event.clientX - rect.left}px`;
							tooltip.style.top = `${event.clientY - rect.top}px`;
						});
					}
				}
			}

			if (editorMode) {
				element.classList.add('is-visible');
			}
		});
	};

	api.register('image-box', (root, { editorMode = false } = {}) => {
		initImageBoxes(root, editorMode);
	});
})();
