(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const initPostsSliders = (root) => {
		api.getNodes(root, '.eap-posts-slider__swiper').forEach((slider) => {
			if (slider.dataset.eapPostsSliderInit === 'true') {
				return;
			}
			slider.dataset.eapPostsSliderInit = 'true';

			// Elementor's bundled Swiper; bail quietly when it isn't present.
			if (typeof window.Swiper === 'undefined') {
				return;
			}

			let settings = {};
			try {
				settings = JSON.parse(slider.dataset.eapPostsSlider || '{}');
			} catch (error) {
				settings = {};
			}

			const wrapper = slider.closest('.eap-posts-slider');
			const pagination = wrapper ? wrapper.querySelector('.eap-posts-slider__pagination') : null;
			const nextEl = wrapper ? wrapper.querySelector('.eap-posts-slider__arrow--next') : null;
			const prevEl = wrapper ? wrapper.querySelector('.eap-posts-slider__arrow--prev') : null;

			const desktop = parseInt(settings.slidesDesktop, 10) || 3;
			const tablet = parseInt(settings.slidesTablet, 10) || 2;
			const mobile = parseInt(settings.slidesMobile, 10) || 1;
			const space = settings.spaceBetween === undefined || settings.spaceBetween === null
				? 24
				: parseInt(settings.spaceBetween, 10) || 0;

			// Mobile-first: the base config is the mobile view, breakpoints widen it.
			const config = {
				slidesPerView: mobile,
				spaceBetween: space,
				speed: parseInt(settings.speed, 10) || 600,
				loop: !!settings.loop,
				centeredSlides: !!settings.centered,
				watchOverflow: true,
				breakpoints: {
					768: { slidesPerView: tablet },
					1025: { slidesPerView: desktop }
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

			if (settings.autoplay) {
				config.autoplay = {
					delay: Math.max(parseFloat(settings.autoplayDelay || 4) * 1000, 100),
					disableOnInteraction: !settings.resumeAfter
				};
			}

			new window.Swiper(slider, config);
		});
	};

	api.register('posts-slider', (root) => {
		initPostsSliders(root);
	});
})();
