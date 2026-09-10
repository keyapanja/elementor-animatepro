(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const initTickers = (root) => {
		api.getNodes(root, '.eap-breaking-news__swiper').forEach((slider) => {
			if (slider.dataset.eapBnInit === 'true') {
				return;
			}
			slider.dataset.eapBnInit = 'true';

			// Elementor's bundled Swiper; bail quietly when it isn't present.
			if (typeof window.Swiper === 'undefined') {
				return;
			}

			let settings = {};
			try {
				settings = JSON.parse(slider.dataset.eapBreakingNews || '{}');
			} catch (error) {
				settings = {};
			}

			const wrapper = slider.closest('.eap-breaking-news');
			const nextEl = wrapper ? wrapper.querySelector('.eap-breaking-news__arrow--next') : null;
			const prevEl = wrapper ? wrapper.querySelector('.eap-breaking-news__arrow--prev') : null;

			const config = {
				direction: settings.direction === 'vertical' ? 'vertical' : 'horizontal',
				slidesPerView: 1,
				spaceBetween: 0,
				speed: parseInt(settings.speed, 10) || 600,
				loop: !!settings.loop,
				watchOverflow: true
			};

			if (settings.navigation && nextEl && prevEl) {
				config.navigation = { nextEl, prevEl };
			}

			if (settings.autoplay) {
				config.autoplay = {
					delay: Math.max(parseFloat(settings.autoplayDelay || 4) * 1000, 500),
					disableOnInteraction: false
				};
			}

			const instance = new window.Swiper(slider, config);

			// Pause-on-hover is wired here rather than via Swiper's
			// `autoplay.pauseOnMouseEnter`, which only exists on newer Swiper
			// builds — Elementor may bundle an older one.
			if (settings.autoplay && settings.pauseOnHover && instance.autoplay) {
				const host = wrapper || slider;
				host.addEventListener('mouseenter', () => {
					if (instance.autoplay && typeof instance.autoplay.stop === 'function') {
						instance.autoplay.stop();
					}
				});
				host.addEventListener('mouseleave', () => {
					if (instance.autoplay && typeof instance.autoplay.start === 'function') {
						instance.autoplay.start();
					}
				});
			}
		});
	};

	api.register('breaking-news-slider', (root) => {
		initTickers(root);
	});
})();
