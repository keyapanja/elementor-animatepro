(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const initTestimonialSliders = (root) => {
		api.getNodes(root, '.eap-testimonial-slider__swiper').forEach((slider) => {
			if (slider.dataset.eapTestimonialSliderInit === 'true') {
				return;
			}

			slider.dataset.eapTestimonialSliderInit = 'true';
			if (typeof window.Swiper === 'undefined') {
				return;
			}

			let settings = {};
			try {
				settings = JSON.parse(slider.dataset.eapTestimonialSlider || '{}');
			} catch (error) {
				settings = {};
			}

			const wrapper = slider.closest('.eap-testimonial-slider');
			const shell = slider.closest('.eap-testimonial-slider__shell');
			const pagination = wrapper ? wrapper.querySelector('.eap-testimonial-slider__pagination') : null;
			const nextEl = shell ? shell.querySelector('.eap-testimonial-slider__arrow--next') : null;
			const prevEl = shell ? shell.querySelector('.eap-testimonial-slider__arrow--prev') : null;
			const realSlides = Array.from(slider.querySelectorAll('.swiper-slide')).filter((slide) => {
				return !slide.classList.contains('swiper-slide-duplicate');
			});
			const slideCount = realSlides.length;
			const desktopSlides = Math.max(parseInt(settings.slidesDesktop || 3, 10) || 3, 1);
			const tabletSlides = Math.max(parseInt(settings.slidesTablet || 2, 10) || 2, 1);
			const mobileSlides = Math.max(parseInt(settings.slidesMobile || 1, 10) || 1, 1);
			const maxSlidesPerView = Math.max(desktopSlides, tabletSlides, mobileSlides);
			const shouldLoop = !!settings.loop && slideCount > 1 && slideCount > maxSlidesPerView;
			const loopedSlides = shouldLoop ? Math.min(slideCount, maxSlidesPerView + 1) : 0;
			const syncDuplicateVisibility = () => {
				slider.querySelectorAll('.swiper-slide-duplicate .eap-testimonial-box').forEach((box) => {
					box.classList.add('is-visible');
				});
			};
			const config = {
				slidesPerView: mobileSlides,
				spaceBetween: settings.spaceBetween || 24,
				speed: settings.speed || 650,
				loop: shouldLoop,
				rewind: !shouldLoop && !!settings.loop && slideCount > 1,
				loopedSlides,
				loopAdditionalSlides: loopedSlides,
				allowTouchMove: settings.allowTouchMove !== false,
				autoHeight: false,
				slidesPerGroup: 1,
				roundLengths: true,
				centerInsufficientSlides: !shouldLoop,
				observer: true,
				observeParents: true,
				observeSlideChildren: true,
				watchOverflow: true,
				updateOnWindowResize: true,
				resizeObserver: true,
				breakpoints: {
					768: { slidesPerView: tabletSlides },
					1025: { slidesPerView: desktopSlides }
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
					delay: settings.autoplayDelay || 3500,
					disableOnInteraction: false,
					pauseOnMouseEnter: !!settings.pauseOnHover
				};
			}

			const instance = new window.Swiper(slider, config);
			const refreshSlider = () => {
				if (!instance || instance.destroyed) {
					return;
				}

				syncDuplicateVisibility();
				instance.updateSlides();
				instance.updateProgress();
				instance.updateSize();
				instance.update();

				if (shouldLoop && typeof instance.loopFix === 'function') {
					instance.loopFix();
				}
			};

			requestAnimationFrame(refreshSlider);

			instance.on('init', syncDuplicateVisibility);
			instance.on('afterInit', syncDuplicateVisibility);
			instance.on('slideChangeTransitionEnd', syncDuplicateVisibility);
			instance.on('loopFix', syncDuplicateVisibility);

			if (document.fonts && document.fonts.ready) {
				document.fonts.ready.then(() => {
					refreshSlider();
				}).catch(() => {});
			}

			window.addEventListener('load', refreshSlider, { once: true });
		});
	};

	api.register('testimonial-slider', (root) => {
		initTestimonialSliders(root);
	});
})();
