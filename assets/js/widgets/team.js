(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const initTeamNumberedHover = (root) => {
		api.getNodes(root, '.eap-team--layout-numbered-hover .eap-team-card__media').forEach((media) => {
			if (media.dataset.eapTeamCursorInit === 'true') {
				return;
			}

			const button = media.querySelector('[data-eap-team-cursor-button]');
			if (!button) {
				return;
			}

			media.dataset.eapTeamCursorInit = 'true';

			let frameId = 0;
			let currentX = 0;
			let currentY = 0;
			let targetX = 0;
			let targetY = 0;
			let active = false;
			const ease = 0.18;

			const centerButton = () => {
				const rect = media.getBoundingClientRect();
				targetX = rect.width / 2;
				targetY = rect.height / 2;
				currentX = targetX;
				currentY = targetY;
				button.style.left = `${currentX}px`;
				button.style.top = `${currentY}px`;
			};

			const ensureAnimation = () => {
				if (frameId) {
					return;
				}

				frameId = window.requestAnimationFrame(function render() {
					currentX += (targetX - currentX) * ease;
					currentY += (targetY - currentY) * ease;
					button.style.left = `${currentX}px`;
					button.style.top = `${currentY}px`;

					if (Math.abs(targetX - currentX) < 0.2 && Math.abs(targetY - currentY) < 0.2 && !active) {
						frameId = 0;
						return;
					}

					frameId = window.requestAnimationFrame(render);
				});
			};

			const updateTarget = (event) => {
				const rect = media.getBoundingClientRect();
				const halfW = button.offsetWidth / 2;
				const halfH = button.offsetHeight / 2;
				targetX = api.clamp(event.clientX - rect.left, halfW, Math.max(halfW, rect.width - halfW));
				targetY = api.clamp(event.clientY - rect.top, halfH, Math.max(halfH, rect.height - halfH));
				ensureAnimation();
			};

			centerButton();

			media.addEventListener('mouseenter', (event) => {
				active = true;
				updateTarget(event);
			});

			media.addEventListener('mousemove', updateTarget);

			media.addEventListener('mouseleave', () => {
				active = false;
			});

			window.addEventListener('resize', () => {
				if (!active) {
					centerButton();
				}
			});
		});
	};

	const initTeamSliders = (root) => {
		api.getNodes(root, '.eap-team__swiper').forEach((slider) => {
			if (slider.dataset.eapTeamSliderInit === 'true') {
				return;
			}

			slider.dataset.eapTeamSliderInit = 'true';
			if (typeof window.Swiper === 'undefined') {
				return;
			}

			let settings = {};
			try {
				settings = JSON.parse(slider.dataset.eapTeamSlider || '{}');
			} catch (error) {
				settings = {};
			}

			const teamRoot = slider.closest('.eap-team');
			const shell = slider.closest('.eap-team__shell');
			const pagination = shell ? shell.querySelector('.eap-team__pagination') : null;
			const nextEl = shell ? shell.querySelector('.eap-team__arrow--next') : null;
			const prevEl = shell ? shell.querySelector('.eap-team__arrow--prev') : null;
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

			// Every .eap-team-card carries the .eap-motion class, which starts at
			// opacity:0 and waits for the IntersectionObserver to add `.is-visible`.
			// Inside a Swiper, slides are positioned via CSS transform — transforms
			// don't trigger IntersectionObserver, so cards that were transformed off
			// the original layout never get marked visible and the slider appears
			// blank. Force every card (original + duplicate) visible immediately;
			// the slider's own transition is the reveal animation in this mode.
			const syncDuplicateVisibility = () => {
				slider.querySelectorAll('.eap-team-card').forEach((card) => {
					card.classList.add('is-visible');
				});

				if (teamRoot) {
					initTeamNumberedHover(teamRoot);
				}
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

	api.register('team', (root) => {
		initTeamSliders(root);
		initTeamNumberedHover(root);
	});
})();
