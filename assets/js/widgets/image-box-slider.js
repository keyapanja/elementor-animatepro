(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const initImageBoxSliders = (root) => {
		api.getNodes(root, '.eap-image-box-slider__swiper').forEach((slider) => {
			if (slider.dataset.eapImageBoxSliderInit === 'true') {
				return;
			}

			slider.dataset.eapImageBoxSliderInit = 'true';
			if (typeof window.Swiper === 'undefined') {
				return;
			}

			let settings = {};
			try {
				settings = JSON.parse(slider.dataset.eapImageBoxSlider || '{}');
			} catch (error) {
				settings = {};
			}

			const pagination = slider.querySelector('.eap-image-box-slider__pagination');
			const nextEl = slider.querySelector('.eap-image-box-slider__arrow--next');
			const prevEl = slider.querySelector('.eap-image-box-slider__arrow--prev');
			const config = {
				slidesPerView: settings.slidesMobile || 1,
				spaceBetween: settings.spaceBetween || 24,
				speed: settings.speed || 650,
				loop: !!settings.loop,
				observer: true,
				observeParents: true,
				watchOverflow: true,
				breakpoints: {
					768: { slidesPerView: settings.slidesTablet || 2 },
					1025: { slidesPerView: settings.slidesDesktop || 3 }
				}
			};

			if (settings.navigation && nextEl && prevEl) {
				config.navigation = { nextEl, prevEl };
			}

			if (settings.pagination && pagination) {
				config.pagination = {
					el: pagination,
					clickable: true,
					type: settings.paginationType || 'bullets'
				};
			}

			new window.Swiper(slider, config);
		});
	};

	api.register('image-box-slider', (root) => {
		initImageBoxSliders(root);
	});
})();
