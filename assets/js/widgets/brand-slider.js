(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const brandMarquees = new Set();
	let resizeBound = false;

	const initBrandMarquee = (slider, settings) => {
		if (slider.dataset.eapBrandMarqueeInit === 'true') {
			return;
		}

		slider.dataset.eapBrandMarqueeInit = 'true';
		const brand = slider.closest('.eap-brand-slider');
		const track = slider.querySelector('.swiper-wrapper');
		if (!brand || !track) {
			return;
		}

		brand.classList.add('eap-brand-slider--marquee');
		const originalSlides = Array.from(track.children).map((slide) => slide.cloneNode(true));
		let offset = 0;
		let frameId = 0;
		let lastTime = 0;
		let cycleWidth = 0;
		let paused = false;

		const refresh = () => {
			track.innerHTML = '';
			originalSlides.forEach((slide) => track.appendChild(slide.cloneNode(true)));
			const baseSlides = Array.from(track.children);
			cycleWidth = 0;
			baseSlides.forEach((slide) => {
				const slideStyles = window.getComputedStyle(slide);
				cycleWidth += slide.getBoundingClientRect().width;
				cycleWidth += parseFloat(slideStyles.marginRight || '0');
			});
			while (track.scrollWidth < slider.clientWidth + cycleWidth) {
				originalSlides.forEach((slide) => track.appendChild(slide.cloneNode(true)));
			}
			offset = 0;
			lastTime = 0;
			track.style.transform = settings.directionMode === 'right' ? `translate3d(${-cycleWidth}px, 0, 0)` : 'translate3d(0, 0, 0)';
		};

		const animate = (time) => {
			if (!lastTime) {
				lastTime = time;
			}
			const delta = time - lastTime;
			lastTime = time;
			if (!paused && cycleWidth > 0) {
				const duration = Math.max(parseFloat(settings.speed || 5000), 1);
				const pixelsPerMs = cycleWidth / duration;
				offset += delta * pixelsPerMs;
				if (offset >= cycleWidth) {
					offset -= cycleWidth;
				}
				track.style.transform = settings.directionMode === 'right'
					? `translate3d(${(-cycleWidth + offset)}px, 0, 0)`
					: `translate3d(${-offset}px, 0, 0)`;
			}
			frameId = window.requestAnimationFrame(animate);
		};

		refresh();
		if (settings.autoplayInteraction) {
			slider.addEventListener('mouseenter', () => { paused = true; });
			slider.addEventListener('mouseleave', () => { paused = false; });
		}
		frameId = window.requestAnimationFrame(animate);
		brandMarquees.add({ refresh, destroy: () => frameId && window.cancelAnimationFrame(frameId) });
	};

	const bindResize = () => {
		if (resizeBound) {
			return;
		}
		resizeBound = true;
		window.addEventListener('resize', () => {
			brandMarquees.forEach((instance) => instance && instance.refresh && instance.refresh());
		});
	};

	const initBrandSliders = (root) => {
		api.getNodes(root, '.eap-brand-slider__swiper').forEach((slider) => {
			if (slider.dataset.eapBrandSliderInit === 'true') {
				return;
			}
			slider.dataset.eapBrandSliderInit = 'true';
			if (typeof window.Swiper === 'undefined') {
				return;
			}

			let settings = {};
			try {
				settings = JSON.parse(slider.dataset.eapBrandSlider || '{}');
			} catch (error) {
				settings = {};
			}

			const wrapper = slider.closest('.eap-brand-slider');
			const pagination = wrapper ? wrapper.querySelector('.eap-brand-slider__pagination') : null;
			const nextEl = wrapper ? wrapper.querySelector('.eap-brand-slider__arrow--next') : null;
			const prevEl = wrapper ? wrapper.querySelector('.eap-brand-slider__arrow--prev') : null;
			const desktopSlides = settings.slidesDesktop === 'auto' ? 'auto' : (settings.slidesDesktop || 3);
			const tabletSlides = settings.slidesTablet === 'auto' ? 'auto' : (settings.slidesTablet || desktopSlides);
			const mobileSlides = settings.slidesMobile === 'auto' ? 'auto' : (settings.slidesMobile || 1);
			const useMarquee = !!settings.autoplay;
			if (useMarquee) {
				initBrandMarquee(slider, settings);
				bindResize();
				return;
			}

			const config = {
				slidesPerView: mobileSlides,
				spaceBetween: 0,
				speed: settings.speed || 5000,
				loop: !!settings.loop,
				allowTouchMove: !!settings.allowTouchMove,
				watchSlidesProgress: true,
				grid: settings.grid ? { rows: 2, fill: 'row' } : undefined,
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
					delay: Math.max(parseFloat(settings.autoplayDelay || 1) * 1000, 1),
					disableOnInteraction: !settings.autoplayInteraction
				};
			}
			new window.Swiper(slider, config);
		});
	};

	api.register('brand-slider', (root) => {
		initBrandSliders(root);
	});
})();
