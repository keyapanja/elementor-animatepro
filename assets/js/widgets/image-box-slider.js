(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	// Track which DOM nodes already have pointer handlers attached. We can't
	// use a dataset attribute as the guard because Swiper's loop mode clones
	// slides via cloneNode(true), which copies dataset attributes but NOT
	// event listeners — so the clone would look "already bound" while having
	// no actual handlers. A WeakSet keyed by the live DOM node treats originals
	// and clones as separate elements (which they are) and lets each one bind
	// exactly once. WeakSet also lets nodes get GC'd cleanly.
	const pointerBoundCards = new WeakSet();

	const bindPointerCard = (card) => {
		if (pointerBoundCards.has(card)) {
			return;
		}
		const mediaWrap = card.querySelector('.eap-image-box-media-wrap');
		const tooltip   = card.querySelector('.eap-image-box-tooltip');
		if (!mediaWrap || !tooltip) {
			return;
		}
		pointerBoundCards.add(card);
		mediaWrap.addEventListener('mouseenter', () => card.classList.add('is-pointer-active'));
		mediaWrap.addEventListener('mouseleave', () => card.classList.remove('is-pointer-active'));
		mediaWrap.addEventListener('mousemove', (event) => {
			const rect = mediaWrap.getBoundingClientRect();
			tooltip.style.left = `${event.clientX - rect.left}px`;
			tooltip.style.top  = `${event.clientY - rect.top}px`;
		});
	};

	// Force every .eap-image-box card inside the slider into the "is-visible"
	// state, AND bind the pointer-layout hover/tooltip handler. Both are
	// normally driven by IntersectionObserver / per-card init that fires once
	// when the element scrolls into view — but Swiper positions slides on
	// pages 2+ via CSS transform, which doesn't trigger either. The result is
	// the .eap-image-box-media::after white overlay stays scaleX(1), covering
	// the image, and the Pointer tooltip never gets bound. Doing both here
	// fixes both symptoms for non-first-page slides AND for loop clones.
	const revealAndBindCards = (slider) => {
		slider.querySelectorAll('.eap-image-box').forEach((card) => {
			card.classList.add('is-visible');
			if (card.classList.contains('eap-image-box--pointer')) {
				bindPointerCard(card);
			}
		});
	};

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

			const instance = new window.Swiper(slider, config);

			// Reveal + bind on the original DOM right away.
			revealAndBindCards(slider);

			// Re-run after Swiper's init/loop-fix passes so loop clones get
			// the same treatment as originals. Idempotent — classList.add and
			// dataset guard prevent double-binding.
			if (instance && typeof instance.on === 'function') {
				instance.on('init', () => revealAndBindCards(slider));
				instance.on('afterInit', () => revealAndBindCards(slider));
				instance.on('loopFix', () => revealAndBindCards(slider));
				instance.on('slideChangeTransitionEnd', () => revealAndBindCards(slider));
			}

			window.requestAnimationFrame(() => revealAndBindCards(slider));
		});
	};

	api.register('image-box-slider', (root) => {
		initImageBoxSliders(root);
	});
})();
