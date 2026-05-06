(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const initImageHotspots = (root, editorMode = false) => {
		api.getNodes(root, '.eap-image-hotspot').forEach((widget) => {
			if (widget.dataset.eapHotspotInit === 'true') {
				return;
			}

			widget.dataset.eapHotspotInit = 'true';
			const trigger = widget.dataset.eapHotspotTrigger || 'hover';
			const items = api.getNodes(widget, '[data-eap-hotspot-item]');
			const closeAll = () => {
				items.forEach((item) => {
					item.classList.remove('is-active');
					const button = item.querySelector('.eap-image-hotspot__button');
					if (button) {
						button.setAttribute('aria-expanded', 'false');
					}
				});
			};

			items.forEach((item, index) => {
				const button = item.querySelector('.eap-image-hotspot__button');
				if (!button) {
					return;
				}

				if (trigger === 'click') {
					button.addEventListener('click', (event) => {
						event.preventDefault();
						const isActive = item.classList.contains('is-active');
						closeAll();
						if (!isActive) {
							item.classList.add('is-active');
							button.setAttribute('aria-expanded', 'true');
						}
					});
				} else {
					item.addEventListener('mouseenter', () => item.classList.add('is-active'));
					item.addEventListener('mouseleave', () => item.classList.remove('is-active'));
				}

				if (editorMode && index === 0) {
					item.classList.add('is-active');
					button.setAttribute('aria-expanded', 'true');
				}
			});

			if (trigger === 'click') {
				document.addEventListener('click', (event) => {
					if (!widget.contains(event.target)) {
						closeAll();
					}
				});
			}
		});
	};

	api.register('image-hotspot', (root, { editorMode = false } = {}) => {
		initImageHotspots(root, editorMode);
	});
})();
