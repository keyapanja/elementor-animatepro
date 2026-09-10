(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	// The provider + id were parsed and escaped server-side, so we only ever
	// assemble an embed URL from a known-good id here.
	const buildEmbed = (item) => {
		const provider = item.getAttribute('data-provider');
		const id = item.getAttribute('data-video-id') || '';
		const src = item.getAttribute('data-src') || '';

		if (provider === 'youtube' && id) {
			const frame = document.createElement('iframe');
			frame.src = 'https://www.youtube.com/embed/' + encodeURIComponent(id) + '?autoplay=1&rel=0&playsinline=1';
			frame.setAttribute('allow', 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture');
			frame.setAttribute('allowfullscreen', '');
			frame.setAttribute('title', 'YouTube video');
			return frame;
		}

		if (provider === 'vimeo' && id) {
			const frame = document.createElement('iframe');
			frame.src = 'https://player.vimeo.com/video/' + encodeURIComponent(id) + '?autoplay=1';
			frame.setAttribute('allow', 'autoplay; fullscreen; picture-in-picture');
			frame.setAttribute('allowfullscreen', '');
			frame.setAttribute('title', 'Vimeo video');
			return frame;
		}

		if (provider === 'hosted' && src) {
			const video = document.createElement('video');
			video.src = src;
			video.controls = true;
			video.autoplay = true;
			video.playsInline = true;
			return video;
		}

		return null;
	};

	const setupWidget = (wrapper) => {
		if (wrapper.dataset.eapVbBound === '1') {
			return;
		}
		wrapper.dataset.eapVbBound = '1';

		const mode = wrapper.getAttribute('data-eap-vb-mode') === 'inline' ? 'inline' : 'lightbox';
		const lightbox = wrapper.querySelector('.eap-video-box-slider__lightbox');
		const frame = wrapper.querySelector('.eap-video-box-slider__frame');
		let swiper = null;

		const pauseAutoplay = () => {
			if (swiper && swiper.autoplay && typeof swiper.autoplay.stop === 'function') {
				swiper.autoplay.stop();
			}
		};

		const closeLightbox = () => {
			if (!lightbox || !frame) {
				return;
			}
			lightbox.hidden = true;
			frame.innerHTML = '';
			document.documentElement.style.overflow = '';
		};

		const closeInline = () => {
			wrapper.querySelectorAll('.eap-video-box-slider__item.is-playing').forEach((played) => {
				played.classList.remove('is-playing');
				const embed = played.querySelector('.eap-video-box-slider__embed');
				if (embed) {
					embed.innerHTML = '';
				}
			});
		};

		wrapper.querySelectorAll('.eap-video-box-slider__play').forEach((button) => {
			button.addEventListener('click', (event) => {
				event.preventDefault();
				event.stopPropagation();

				const item = button.closest('.eap-video-box-slider__item');
				if (!item) {
					return;
				}

				const media = buildEmbed(item);
				if (!media) {
					return;
				}

				pauseAutoplay();

				if (mode === 'inline') {
					closeInline();
					const embed = item.querySelector('.eap-video-box-slider__embed');
					if (embed) {
						embed.appendChild(media);
						item.classList.add('is-playing');
					}
					return;
				}

				if (lightbox && frame) {
					frame.innerHTML = '';
					frame.appendChild(media);
					lightbox.hidden = false;
					document.documentElement.style.overflow = 'hidden';
				}
			});
		});

		if (lightbox) {
			const backdrop = lightbox.querySelector('.eap-video-box-slider__backdrop');
			const close = lightbox.querySelector('.eap-video-box-slider__close');

			if (backdrop) {
				backdrop.addEventListener('click', closeLightbox);
			}
			if (close) {
				close.addEventListener('click', closeLightbox);
			}
			document.addEventListener('keydown', (event) => {
				if (event.key === 'Escape' && !lightbox.hidden) {
					closeLightbox();
				}
			});
		}

		// --- Swiper (house convention) ---
		const slider = wrapper.querySelector('.eap-video-box-slider__swiper');
		if (!slider || typeof window.Swiper === 'undefined') {
			return;
		}

		let settings = {};
		try {
			settings = JSON.parse(slider.dataset.eapVideoBoxSlider || '{}');
		} catch (error) {
			settings = {};
		}

		const pagination = wrapper.querySelector('.eap-video-box-slider__pagination');
		const nextEl = wrapper.querySelector('.eap-video-box-slider__arrow--next');
		const prevEl = wrapper.querySelector('.eap-video-box-slider__arrow--prev');

		const desktop = parseInt(settings.slidesDesktop, 10) || 3;
		const tablet = parseInt(settings.slidesTablet, 10) || 2;
		const mobile = parseInt(settings.slidesMobile, 10) || 1;
		const space = settings.spaceBetween === undefined || settings.spaceBetween === null
			? 24
			: parseInt(settings.spaceBetween, 10) || 0;

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
				delay: Math.max(parseFloat(settings.autoplayDelay || 5) * 1000, 500),
				disableOnInteraction: false
			};
		}

		swiper = new window.Swiper(slider, config);

		// A playing video shouldn't get slid away underneath the viewer.
		if (mode === 'inline' && swiper.on) {
			swiper.on('slideChange', closeInline);
		}

		if (settings.autoplay && settings.pauseOnHover && swiper.autoplay) {
			wrapper.addEventListener('mouseenter', () => {
				if (swiper.autoplay && typeof swiper.autoplay.stop === 'function') {
					swiper.autoplay.stop();
				}
			});
			wrapper.addEventListener('mouseleave', () => {
				if (swiper.autoplay && typeof swiper.autoplay.start === 'function') {
					swiper.autoplay.start();
				}
			});
		}
	};

	const run = (root) => {
		api.getNodes(root, '.eap-video-box-slider').forEach((wrapper) => setupWidget(wrapper));
	};

	api.register('video-box-slider', (root) => {
		run(root);
	});
})();
