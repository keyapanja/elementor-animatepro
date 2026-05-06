(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const initTextHoverImages = (root) => {
		api.getNodes(root, '[data-eap-text-hover-image]').forEach((wrapper) => {
			if (wrapper.dataset.eapTextHoverImageInit === 'true') {
				return;
			}

			wrapper.dataset.eapTextHoverImageInit = 'true';
			const trigger = wrapper.querySelector('.eap-text-hover-image__trigger');
			const preview = wrapper.querySelector('.eap-text-hover-image__preview');
			if (!trigger || !preview) {
				return;
			}

			let frameId = 0;
			let isAnimating = false;
			let currentX = 0;
			let currentY = 0;
			let targetX = 0;
			let targetY = 0;
			const ease = 0.16;

			const render = () => {
				currentX += (targetX - currentX) * ease;
				currentY += (targetY - currentY) * ease;
				preview.style.setProperty('--eap-text-hover-cursor-x', `${currentX}px`);
				preview.style.setProperty('--eap-text-hover-cursor-y', `${currentY}px`);

				if (Math.abs(targetX - currentX) < 0.2 && Math.abs(targetY - currentY) < 0.2 && !wrapper.classList.contains('is-active')) {
					isAnimating = false;
					frameId = 0;
					return;
				}

				frameId = window.requestAnimationFrame(render);
			};

			const ensureAnimation = () => {
				if (isAnimating) {
					return;
				}
				isAnimating = true;
				frameId = window.requestAnimationFrame(render);
			};

			const movePreview = (event) => {
				const rect = wrapper.getBoundingClientRect();
				targetX = event.clientX - rect.left;
				targetY = event.clientY - rect.top;
				ensureAnimation();
			};

			trigger.addEventListener('mouseenter', (event) => {
				wrapper.classList.add('is-active');
				movePreview(event);
			});
			trigger.addEventListener('mousemove', movePreview);
			trigger.addEventListener('mouseleave', () => {
				wrapper.classList.remove('is-active');
				ensureAnimation();
			});
		});
	};

	api.register('text-hover-image', (root) => {
		initTextHoverImages(root);
	});
})();
