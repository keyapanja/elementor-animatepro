(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const galleryParallaxItems = new Set();
	let globalScrollBound = false;

	const updateGalleryParallax = () => {
		galleryParallaxItems.forEach((item) => {
			const rect = item.getBoundingClientRect();
			const speed = parseFloat(item.dataset.eapGallerySpeed || '0.9');
			const progress = api.clamp((window.innerHeight - rect.top) / (window.innerHeight + rect.height), 0, 1);
			const translateY = api.prefersReducedMotion() ? 0 : (0.5 - progress) * speed * 90;
			item.style.transform = `translate3d(0, ${translateY}px, 0)`;
		});
	};

	const bindGlobalScroll = () => {
		if (globalScrollBound) {
			return;
		}
		globalScrollBound = true;
		updateGalleryParallax();
		window.addEventListener('scroll', updateGalleryParallax, { passive: true });
		window.addEventListener('resize', updateGalleryParallax);
	};

	const initImageGalleries = (root) => {
		api.getNodes(root, '.eap-image-gallery').forEach((gallery) => {
			if (gallery.dataset.eapImageGalleryInit === 'true') {
				return;
			}
			gallery.dataset.eapImageGalleryInit = 'true';
			if (gallery.dataset.eapGallerySmooth !== 'yes') {
				return;
			}
			gallery.querySelectorAll('.eap-image-gallery__parallax').forEach((item) => galleryParallaxItems.add(item));
		});
		bindGlobalScroll();
	};

	api.register('image-gallery', (root) => {
		initImageGalleries(root);
	});
})();
