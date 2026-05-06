(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const initImageComparisons = (root) => {
		api.getNodes(root, '[data-eap-image-comparison]').forEach((widget) => {
			if (widget.dataset.eapImageComparisonInit === 'true') {
				return;
			}
			widget.dataset.eapImageComparisonInit = 'true';
			const media = widget.querySelector('.eap-image-comparison__media');
			const overlay = widget.querySelector('[data-eap-image-comparison-overlay]');
			const handle = widget.querySelector('[data-eap-image-comparison-handle]');
			const direction = widget.dataset.eapDirection || 'horizontal';
			let position = parseFloat(widget.dataset.eapPosition || '50');
			let dragging = false;
			if (!media || !overlay || !handle) {
				return;
			}

			const applyPosition = (value) => {
				position = api.clamp(value, 0, 100);
				widget.dataset.eapPosition = `${position}`;
				if (direction === 'vertical') {
					overlay.style.clipPath = `inset(0 0 ${100 - position}% 0)`;
					handle.style.top = `${position}%`;
					handle.style.left = '50%';
				} else {
					overlay.style.clipPath = `inset(0 ${100 - position}% 0 0)`;
					handle.style.left = `${position}%`;
					handle.style.top = '50%';
				}
			};

			const updateFromEvent = (event) => {
				const rect = media.getBoundingClientRect();
				if (direction === 'vertical') {
					applyPosition(((event.clientY - rect.top) / Math.max(rect.height, 1)) * 100);
				} else {
					applyPosition(((event.clientX - rect.left) / Math.max(rect.width, 1)) * 100);
				}
			};

			const stopDragging = () => { dragging = false; };
			handle.addEventListener('pointerdown', (event) => {
				dragging = true;
				handle.setPointerCapture(event.pointerId);
				updateFromEvent(event);
			});
			media.addEventListener('pointermove', (event) => {
				if (dragging) {
					updateFromEvent(event);
				}
			});
			media.addEventListener('click', updateFromEvent);
			window.addEventListener('pointerup', stopDragging);
			window.addEventListener('pointercancel', stopDragging);
			window.addEventListener('resize', () => applyPosition(position));
			applyPosition(position);
		});
	};

	api.register('image-comparison', (root) => {
		initImageComparisons(root);
	});
})();
