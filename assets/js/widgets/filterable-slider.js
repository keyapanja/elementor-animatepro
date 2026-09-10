(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const setupWidget = (wrapper) => {
		if (wrapper.dataset.eapFsBound === '1') {
			return;
		}
		wrapper.dataset.eapFsBound = '1';

		const slider = wrapper.querySelector('.eap-filterable-slider__swiper');
		const track = slider ? slider.querySelector('.swiper-wrapper') : null;
		if (!slider || !track) {
			return;
		}

		const ajaxUrl = wrapper.dataset.ajaxUrl || '';
		const nonce = wrapper.dataset.nonce || '';
		const emptyEl = wrapper.querySelector('.eap-filterable-slider__empty');
		const filters = Array.from(wrapper.querySelectorAll('.eap-filterable-slider__filter'));

		let baseSpec;
		let display;
		let config;
		try {
			baseSpec = JSON.parse(wrapper.dataset.spec || '{}');
			display = JSON.parse(wrapper.dataset.display || '{}');
			config = JSON.parse(slider.dataset.eapFsConfig || '{}');
		} catch (error) {
			return;
		}

		// Active term = whichever tab is marked active on load (0 = "All").
		let activeTerm = 0;
		const activeBtn = wrapper.querySelector('.eap-filterable-slider__filter.is-active');
		if (activeBtn) {
			activeTerm = parseInt(activeBtn.dataset.term, 10) || 0;
		}

		let swiper = null;
		let loading = false;

		const buildSwiper = () => {
			if (typeof window.Swiper === 'undefined') {
				return;
			}

			const pagination = wrapper.querySelector('.eap-filterable-slider__pagination');
			const nextEl = wrapper.querySelector('.eap-filterable-slider__arrow--next');
			const prevEl = wrapper.querySelector('.eap-filterable-slider__arrow--prev');

			const desktop = parseInt(config.slidesDesktop, 10) || 3;
			const tablet = parseInt(config.slidesTablet, 10) || 2;
			const mobile = parseInt(config.slidesMobile, 10) || 1;
			const space = config.spaceBetween === undefined || config.spaceBetween === null
				? 24
				: parseInt(config.spaceBetween, 10) || 0;

			const opts = {
				slidesPerView: mobile,
				spaceBetween: space,
				speed: parseInt(config.speed, 10) || 600,
				loop: !!config.loop,
				watchOverflow: true,
				breakpoints: {
					768: { slidesPerView: tablet },
					1025: { slidesPerView: desktop }
				}
			};

			if (config.navigation && nextEl && prevEl) {
				opts.navigation = { nextEl, prevEl };
			}

			if (config.pagination && pagination) {
				opts.pagination = {
					el: pagination,
					clickable: true,
					type: config.paginationType || 'bullets'
				};
			}

			if (config.autoplay) {
				opts.autoplay = {
					delay: Math.max(parseFloat(config.autoplayDelay || 4) * 1000, 500),
					disableOnInteraction: false
				};
			}

			swiper = new window.Swiper(slider, opts);
		};

		const destroySwiper = () => {
			if (swiper && typeof swiper.destroy === 'function') {
				swiper.destroy(true, true);
			}
			swiper = null;
		};

		// The shared eap_load_posts endpoint returns BARE .eap-posts__item cards
		// (it feeds grids), so each one has to be wrapped as a slide here.
		const replaceSlides = (html) => {
			const tmp = document.createElement('div');
			tmp.innerHTML = html;
			const cards = Array.from(tmp.children);

			destroySwiper();
			track.innerHTML = '';

			cards.forEach((card) => {
				const slide = document.createElement('div');
				slide.className = 'swiper-slide eap-filterable-slider__slide';
				slide.appendChild(card);
				track.appendChild(slide);
			});

			const isEmpty = 0 === cards.length;
			wrapper.classList.toggle('is-empty', isEmpty);
			if (emptyEl) {
				emptyEl.hidden = !isEmpty;
			}

			// Rebuilding beats removeAllSlides()/appendSlide(): it is version-proof
			// across Swiper builds and sidesteps loop-mode's duplicated slides.
			if (!isEmpty) {
				buildSwiper();
			}
		};

		const applyFilter = (term, btn) => {
			if (loading || term === activeTerm) {
				return;
			}
			activeTerm = term;

			filters.forEach((b) => {
				const on = b === btn;
				b.classList.toggle('is-active', on);
				b.setAttribute('aria-selected', on ? 'true' : 'false');
			});

			loading = true;
			wrapper.classList.add('is-filtering');

			const spec = Object.assign({}, baseSpec, { filter_terms: term ? [term] : [] });
			const body = new URLSearchParams();
			body.set('action', 'eap_load_posts');
			body.set('nonce', nonce);
			body.set('page', '1');
			body.set('spec', JSON.stringify(spec));
			body.set('display', JSON.stringify(display));

			window.fetch(ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body: body.toString()
			})
				.then((res) => (res.ok ? res.json() : null))
				.then((json) => {
					const html = json && json.success && json.data ? (json.data.html || '') : '';
					replaceSlides(html);
				})
				.catch(() => {})
				.then(() => {
					loading = false;
					wrapper.classList.remove('is-filtering');
				});
		};

		filters.forEach((btn) => {
			btn.addEventListener('click', () => applyFilter(parseInt(btn.dataset.term, 10) || 0, btn));
		});

		// Bound once, and reads `swiper` at call time so it survives rebuilds.
		if (config.autoplay && config.pauseOnHover) {
			wrapper.addEventListener('mouseenter', () => {
				if (swiper && swiper.autoplay && typeof swiper.autoplay.stop === 'function') {
					swiper.autoplay.stop();
				}
			});
			wrapper.addEventListener('mouseleave', () => {
				if (swiper && swiper.autoplay && typeof swiper.autoplay.start === 'function') {
					swiper.autoplay.start();
				}
			});
		}

		buildSwiper();
	};

	const run = (root) => {
		api.getNodes(root, '.eap-filterable-slider[data-eap-filterable-slider]').forEach((wrapper) => setupWidget(wrapper));
	};

	api.register('filterable-slider', (root) => {
		run(root);
	});
})();
