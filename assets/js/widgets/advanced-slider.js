/* global EAPFrontend, Swiper */
(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const ROOT_SELECTOR = '[data-eap-asl-root]';

	const initSwiperFor = (slider) => {
		if (typeof window.Swiper === 'undefined') {
			return null;
		}

		let settings = {};
		try {
			settings = JSON.parse(slider.dataset.eapAsl || '{}');
		} catch (e) {
			settings = {};
		}

		const shell = slider.closest('.eap-adv-slider__shell');
		const widget = slider.closest('.eap-adv-slider');
		const pagination = widget ? widget.querySelector('.eap-adv-slider__pagination') : null;
		const prevEl = shell ? shell.querySelector('.eap-adv-slider__arrow--prev') : null;
		const nextEl = shell ? shell.querySelector('.eap-adv-slider__arrow--next') : null;

		const realSlides = Array.from(slider.querySelectorAll('.swiper-slide')).filter(
			(s) => !s.classList.contains('swiper-slide-duplicate')
		);
		const slideCount = realSlides.length;
		const isCoverflow = settings.effect === 'coverflow';
		const isCardStack = settings.effect === 'cards';
		const isVertical  = settings.direction === 'vertical';

		const shouldLoop = !!settings.loop && slideCount > 1;

		// Vertical layout uses fixed slidesPerView with breakpoints, all others
		// auto-size from each slide's intrinsic width.
		const vDesktop = Math.max(parseInt(settings.verticalSlides, 10) || 3, 1);
		const vTablet  = Math.max(parseInt(settings.verticalSlidesTablet, 10) || 2, 1);
		const vMobile  = Math.max(parseInt(settings.verticalSlidesMobile, 10) || 1, 1);

		const config = {
			slidesPerView: isVertical ? vMobile : 'auto',
			centeredSlides: isVertical ? !!settings.verticalCentered : true,
			spaceBetween: settings.spaceBetween || 24,
			speed: settings.speed || 650,
			loop: shouldLoop,
			rewind: !shouldLoop && !!settings.loop && slideCount > 1,
			allowTouchMove: settings.allowTouchMove !== false,
			roundLengths: true,
			centerInsufficientSlides: !shouldLoop,
			observer: true,
			observeParents: true,
			observeSlideChildren: true,
			watchOverflow: true,
			updateOnWindowResize: true,
			resizeObserver: true
		};

		if (isVertical) {
			config.direction = 'vertical';
			config.breakpoints = {
				768:  { slidesPerView: vTablet },
				1025: { slidesPerView: vDesktop }
			};

			// Scroll-to-navigate: only intercept mousewheel when the user
			// opted in. releaseOnEdges releases the wheel event back to the
			// page once the slider hits its first or last slide *while loop
			// is off*, so visitors are never trapped on a non-looping slider.
			if (settings.verticalScrollNav !== false) {
				config.mousewheel = {
					forceToAxis:    true,
					releaseOnEdges: !shouldLoop,
					sensitivity:    1
				};
			}
		}

		if (isCoverflow) {
			config.effect = 'coverflow';
			config.coverflowEffect = {
				rotate: typeof settings.cfRotate === 'number' ? settings.cfRotate : 35,
				stretch: typeof settings.cfStretch === 'number' ? settings.cfStretch : 0,
				depth: typeof settings.cfDepth === 'number' ? settings.cfDepth : 150,
				modifier: typeof settings.cfModifier === 'number' ? settings.cfModifier : 1,
				slideShadows: !!settings.cfShadows
			};
			config.grabCursor = true;
		}

		if (isCardStack) {
			const rotate = typeof settings.creativeRotate === 'number' ? settings.creativeRotate : 4;
			const offset = typeof settings.creativeOffset === 'number' ? settings.creativeOffset : 10;
			const shadows = !!settings.creativeShadows;

			config.effect = 'cards';
			config.grabCursor = true;
			config.cardsEffect = {
				rotate: rotate > 0,
				perSlideRotate: rotate,
				perSlideOffset: offset,
				slideShadows: shadows
			};
			if (slideCount < 2) {
				config.loop = false;
				config.rewind = false;
			}
		}

		if (settings.navigation && prevEl && nextEl) {
			config.navigation = { prevEl, nextEl };
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

		return new window.Swiper(slider, config);
	};

	const initWidget = (widget) => {
		if (widget.dataset.eapAslBound === '1') {
			return;
		}
		widget.dataset.eapAslBound = '1';

		const slider = widget.querySelector('.eap-adv-slider__swiper');
		if (!slider) {
			return;
		}

		const instance = initSwiperFor(slider);

		// Warm up Swiper so first slide change isn't glitchy. See the
		// advanced-testimonial-slider.js notes for why a silent slide cycle
		// at speed:0 fixes this in coverflow/cards effects.
		const warmUp = () => {
			if (!instance || typeof instance.update !== 'function') {
				return;
			}
			try {
				slider.querySelectorAll('.swiper-slide').forEach((s) => {
					/* eslint-disable-next-line no-unused-expressions */
					s.offsetHeight;
				});
				instance.update();
				const startIdx = instance.activeIndex;
				instance.slideTo(startIdx + 1, 0, false);
				instance.slideTo(startIdx, 0, false);
				instance.update();
			} catch (e) { /* noop */ }
		};

		window.requestAnimationFrame(warmUp);
		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(warmUp).catch(() => {});
		}
		if (document.readyState !== 'complete') {
			window.addEventListener('load', warmUp, { once: true });
		}
	};

	const initAll = (root) => {
		if (!root) {
			return;
		}
		api.getNodes(root, ROOT_SELECTOR).forEach(initWidget);
	};

	api.register('advanced-slider', (root) => initAll(root));
})();
