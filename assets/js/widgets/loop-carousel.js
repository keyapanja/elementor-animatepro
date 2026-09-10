(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const initLoopCarousels = (root) => {
		api.getNodes(root, '.eap-loop-carousel__swiper').forEach((slider) => {
			if (slider.dataset.eapLcInit === 'true') {
				return;
			}
			slider.dataset.eapLcInit = 'true';

			// Elementor's bundled Swiper; bail quietly when it isn't present.
			if (typeof window.Swiper === 'undefined') {
				return;
			}

			let settings = {};
			try {
				settings = JSON.parse(slider.dataset.eapLoopCarousel || '{}');
			} catch (error) {
				settings = {};
			}

			const wrapper = slider.closest('.eap-loop-carousel');
			const pagination = wrapper ? wrapper.querySelector('.eap-loop-carousel__pagination') : null;
			const nextEl = wrapper ? wrapper.querySelector('.eap-loop-carousel__arrow--next') : null;
			const prevEl = wrapper ? wrapper.querySelector('.eap-loop-carousel__arrow--prev') : null;

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
					delay: Math.max(parseFloat(settings.autoplayDelay || 4) * 1000, 500),
					disableOnInteraction: false
				};
			}

			const instance = new window.Swiper(slider, config);

			// Pause-on-hover wired here rather than via Swiper's
			// `autoplay.pauseOnMouseEnter`, which only exists on newer builds.
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

	api.register('loop-carousel', (root) => {
		initLoopCarousels(root);
	});
})();
