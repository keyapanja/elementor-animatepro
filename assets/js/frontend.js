(() => {
	const clamp = (value, min, max) => Math.min(max, Math.max(min, value));
	const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	const horizontalStages = new Set();
	const scrollTransforms = new Set();
	const pinnedItems = new Set();
	const curves = new Set();
	const galleryParallaxItems = new Set();
	const parallaxStacks = new Set();
	const brandMarquees = new Set();
	const parallaxStackState = new WeakMap();

	let previewFloating;
	let cursorFollower;
	let intersectionObserver;
	let globalScrollBound = false;
	let gsapRegistered = false;

	const decodeHtml = (value) => {
		const textarea = document.createElement('textarea');
		textarea.innerHTML = value || '';
		return textarea.value;
	};

	const getNodes = (root, selector) => {
		if (!root) {
			return [];
		}

		const results = [];
		if (root.matches && root.matches(selector)) {
			results.push(root);
		}

		return results.concat(Array.from(root.querySelectorAll(selector)));
	};

	const ensureFloatingLayers = () => {
		if (!previewFloating) {
			previewFloating = document.createElement('div');
			previewFloating.className = 'eap-preview-floating';
			document.body.appendChild(previewFloating);
		}

		if (!cursorFollower) {
			cursorFollower = document.createElement('div');
			cursorFollower.className = 'eap-cursor-follower';
			document.body.appendChild(cursorFollower);
		}
	};

	const hasScrollTrigger = () => typeof window.gsap !== 'undefined' && typeof window.ScrollTrigger !== 'undefined';

	const ensureScrollTrigger = () => {
		if (!hasScrollTrigger()) {
			return false;
		}

		if (!gsapRegistered) {
			window.gsap.registerPlugin(window.ScrollTrigger);
			gsapRegistered = true;
		}

		return true;
	};

	const markElementVisible = (element) => {
		if (!element) {
			return;
		}

		if (element.classList.contains('eap-motion') || element.classList.contains('eap-nav-animate')) {
			element.style.setProperty('--eap-duration', `${element.dataset.eapDuration || 700}ms`);
			element.style.setProperty('--eap-delay', `${element.dataset.eapDelay || 0}ms`);
			element.classList.add('is-visible');
		}

		if (element.classList.contains('eap-icon-box')) {
			element.classList.add('is-visible');
		}

		if (element.classList.contains('eap-testimonial-box')) {
			element.classList.add('is-visible');
		}

		if (element.classList.contains('eap-advanced-button-wrap')) {
			const button = element.querySelector('.eap-advanced-button');
			if (button) {
				button.style.setProperty('--eap-ab-anim-duration', `${element.dataset.eapAnimDuration || 850}ms`);
				button.style.setProperty('--eap-ab-anim-delay', `${element.dataset.eapAnimDelay || 0}ms`);
			}
			element.classList.add('is-visible');
		}

		if (element.matches('[data-eap-counter]')) {
			const output = element.querySelector('.eap-counter-value');
			const target = parseInt(element.dataset.eapCounter || '0', 10);
			const duration = parseInt(element.dataset.eapDuration || '1800', 10);
			if (output && !element.dataset.eapCounterStarted) {
				element.dataset.eapCounterStarted = 'true';
				const start = performance.now();
				const tick = (timestamp) => {
					const progress = Math.min((timestamp - start) / Math.max(duration, 1), 1);
					output.textContent = Math.round(progress * target).toLocaleString();
					if (progress < 1) {
						window.requestAnimationFrame(tick);
					}
				};
				window.requestAnimationFrame(tick);
			}
		}

		if (element.classList.contains('eap-animated-text') || element.classList.contains('eap-animated-text-v2') || element.classList.contains('eap-text-mask-reveal')) {
			element.classList.add('is-visible', 'is-animated');
		}

		if (element.classList.contains('eap-image-box') || element.classList.contains('eap-image-hotspot') || element.classList.contains('eap-image-widget')) {
			element.classList.add('is-visible');
		}
	};

	const markElementHidden = (element) => {
		if (!element) {
			return;
		}

		if (element.classList.contains('eap-motion') || element.classList.contains('eap-nav-animate')) {
			element.classList.remove('is-visible');
		}

		if (element.classList.contains('eap-icon-box')) {
			element.classList.remove('is-visible');
		}

		if (element.classList.contains('eap-testimonial-box')) {
			element.classList.remove('is-visible');
		}

		if (element.classList.contains('eap-advanced-button-wrap')) {
			element.classList.remove('is-visible');
		}

		if (element.classList.contains('eap-animated-text-v2') && element.dataset.eapPlayOnce === 'true' && element.classList.contains('is-animated')) {
			return;
		}

		if (element.classList.contains('eap-animated-text') || element.classList.contains('eap-animated-text-v2')) {
			element.classList.remove('is-visible', 'is-animated');
		}

		if (element.classList.contains('eap-text-mask-reveal')) {
			element.classList.remove('is-visible', 'is-animated');
		}

		if (element.classList.contains('eap-image-box') || element.classList.contains('eap-image-hotspot') || element.classList.contains('eap-image-widget')) {
			element.classList.remove('is-visible');
		}
	};

	const ensureObserver = () => {
		if (intersectionObserver) {
			return intersectionObserver;
		}

		intersectionObserver = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				const element = entry.target;
				if (entry.isIntersecting) {
					markElementVisible(element);
				} else {
					markElementHidden(element);
				}
			});
		}, {
			threshold: 0.2,
			rootMargin: '0px 0px -5% 0px'
		});

		return intersectionObserver;
	};

	const observeElements = (root, selector) => {
		const elements = getNodes(root, selector);

		if (ensureScrollTrigger()) {
			elements.forEach((element) => {
				if (element.dataset.eapVisibilityBound === 'true') {
					return;
				}

				element.dataset.eapVisibilityBound = 'true';
				window.ScrollTrigger.create({
					trigger: element,
					start: 'top 85%',
					end: 'bottom 15%',
					onEnter: () => {
						markElementVisible(element);
					},
					onEnterBack: () => {
						markElementVisible(element);
					},
					onLeave: () => {
						markElementHidden(element);
					},
					onLeaveBack: () => {
						markElementHidden(element);
					}
				});

				window.requestAnimationFrame(() => {
					if (isInViewport(element)) {
						markElementVisible(element);
					}
				});
			});
			return;
		}

		const observer = ensureObserver();
		elements.forEach((element) => {
			if (element.dataset.eapVisibilityBound === 'true') {
				return;
			}

			element.dataset.eapVisibilityBound = 'true';
			observer.observe(element);

			window.requestAnimationFrame(() => {
				if (isInViewport(element)) {
					markElementVisible(element);
				}
			});
		});
	};

	const isInViewport = (element) => {
		if (!element || !element.getBoundingClientRect) {
			return false;
		}

		const rect = element.getBoundingClientRect();
		const height = window.innerHeight || document.documentElement.clientHeight;
		const width = window.innerWidth || document.documentElement.clientWidth;

		return rect.bottom >= 0 && rect.right >= 0 && rect.top <= height * 0.95 && rect.left <= width;
	};

	const refreshHorizontalStages = () => {
		horizontalStages.forEach((stage) => {
			const sticky = stage.querySelector('.eap-horizontal-sticky');
			const track = stage.querySelector('.eap-horizontal-track');
			const heightVh = parseInt(stage.dataset.eapHorizontalHeight || '220', 10);
			const start = parseInt(stage.dataset.eapHorizontalStart || '0', 10);
			const end = parseInt(stage.dataset.eapHorizontalEnd || '0', 10);

			if (!sticky || !track) {
				return;
			}

			stage.style.minHeight = `${heightVh}vh`;
			const travel = Math.max(0, track.scrollWidth - sticky.clientWidth);
			stage.dataset.eapHorizontalStartPosition = `${start}`;
			stage.dataset.eapHorizontalEndPosition = `${sticky.clientWidth - track.scrollWidth - end}`;
			stage.dataset.eapHorizontalTravel = `${travel}`;
		});
	};

	const getParallaxStackSettings = (stack) => {
		const styles = window.getComputedStyle(stack);
		return {
			transitionMode: stack.dataset.eapPanelTransition || 'slide-up',
			distanceValue: parseFloat(styles.getPropertyValue('--eap-parallax-transition-distance').trim() || '72') || 72
		};
	};

	const renderParallaxStack = (stack, activeIndex = 0) => {
		const panels = Array.from(stack.querySelectorAll('[data-eap-parallax-section="true"]'));
		if (!panels.length) {
			return;
		}

		const { transitionMode, distanceValue } = getParallaxStackSettings(stack);
		const sharedMedia = stack.querySelector('.eap-parallax-sections__shared-media .eap-parallax-section__bg, .eap-parallax-sections__shared-media .eap-parallax-section__video, .eap-parallax-sections__shared-media .eap-parallax-section__iframe');

		panels.forEach((panel, index) => {
			const relative = index - activeIndex;
			const content = panel.querySelector('[data-eap-parallax-content="true"]');
			const motionChildren = panel.querySelectorAll('.eap-animated-text, .eap-text-mask-reveal, .eap-motion');
			const media = panel.querySelector('.eap-parallax-section__bg, .eap-parallax-section__video, .eap-parallax-section__iframe');
			let translateX = 0;
			let translateY = 0;
			let scale = 1;
			let opacity = index === activeIndex ? 1 : 0;

			if (index !== activeIndex) {
				switch (transitionMode) {
					case 'fade':
						break;
					case 'slide-down':
						translateY = relative < 0 ? distanceValue : -distanceValue;
						break;
					case 'scale':
						scale = 0.92;
						break;
					case 'slide-up':
					default:
						translateY = relative < 0 ? -distanceValue : distanceValue;
						break;
				}
			}

			panel.style.opacity = `${opacity}`;
			panel.style.transform = `translate3d(${translateX}px, ${translateY}px, 0) scale(${scale})`;
			panel.classList.toggle('is-active', index === activeIndex);

			if (content) {
				if (index === activeIndex) {
					markElementVisible(content);
				} else {
					markElementHidden(content);
				}
			}

			motionChildren.forEach((element) => {
				if (index === activeIndex) {
					markElementVisible(element);
				} else {
					markElementHidden(element);
				}
			});

			if (media) {
				applyParallaxMedia(
					media,
					panel.dataset.eapParallaxEffect || 'up',
					parseFloat(panel.dataset.eapParallaxSpeed || '0.45'),
					0
				);
			}
		});

		if (sharedMedia) {
			const activePanel = panels[activeIndex] || panels[0];
			applyParallaxMedia(
				sharedMedia,
				activePanel.dataset.eapParallaxEffect || 'up',
				parseFloat(activePanel.dataset.eapParallaxSpeed || '0.45'),
				0
			);
		}
	};

	const syncParallaxStackLayout = (stack) => {
		if (!stack) {
			return;
		}

		const viewport = stack.querySelector('.eap-parallax-sections__viewport');
		const panels = Array.from(stack.querySelectorAll('[data-eap-parallax-section="true"]'));
		if (!viewport || !panels.length) {
			return;
		}

		const viewportHeight = viewport.getBoundingClientRect().height;
		if (!viewportHeight) {
			return;
		}

		stack.style.height = `${viewportHeight * panels.length}px`;
		stack.style.marginBottom = '0px';
	};

	const updateParallaxStacks = () => {
		parallaxStacks.forEach((stack) => {
			const viewport = stack.querySelector('.eap-parallax-sections__viewport');
			const panels = Array.from(stack.querySelectorAll('[data-eap-parallax-section="true"]'));
			const state = parallaxStackState.get(stack);
			if (!viewport || !panels.length || !state) {
				return;
			}

			const styles = window.getComputedStyle(stack);
			const pinOffset = parseFloat(styles.getPropertyValue('--eap-parallax-pin-offset').trim() || '0') || 0;
			const viewportHeight = viewport.getBoundingClientRect().height;
			const stickyTravel = viewportHeight * Math.max(panels.length - 1, 0);
			const rect = stack.getBoundingClientRect();
			const scrolled = clamp(pinOffset - rect.top, 0, stickyTravel);
			const isBefore = rect.top > pinOffset;
			const isEnded = stickyTravel > 0 && rect.top <= pinOffset - stickyTravel;
			const isPinned = !isBefore && !isEnded;
			const nextIndex = isEnded
				? panels.length - 1
				: stickyTravel > 0
				? clamp(Math.round(scrolled / Math.max(viewportHeight, 1)), 0, panels.length - 1)
				: 0;

			stack.classList.toggle('is-eap-before', isBefore);
			stack.classList.toggle('is-eap-pinned', isPinned);
			stack.classList.toggle('is-eap-ended', isEnded);

			if (isPinned) {
				stack.style.setProperty('--eap-parallax-fixed-left', `${rect.left}px`);
				stack.style.setProperty('--eap-parallax-fixed-width', `${rect.width}px`);
				viewport.style.position = 'fixed';
				viewport.style.top = `${pinOffset}px`;
				viewport.style.bottom = 'auto';
				viewport.style.left = `${rect.left}px`;
				viewport.style.right = 'auto';
				viewport.style.width = `${rect.width}px`;
				viewport.style.transform = 'translate3d(0, 0, 0)';
			} else if (isEnded) {
				viewport.style.position = 'absolute';
				viewport.style.top = '0px';
				viewport.style.bottom = 'auto';
				viewport.style.left = '0px';
				viewport.style.right = '0px';
				viewport.style.width = '100%';
				viewport.style.transform = `translate3d(0, ${stickyTravel}px, 0)`;
			} else {
				viewport.style.position = 'absolute';
				viewport.style.top = '0px';
				viewport.style.bottom = 'auto';
				viewport.style.left = '0px';
				viewport.style.right = '0px';
				viewport.style.width = '100%';
				viewport.style.transform = 'translate3d(0, 0, 0)';
			}

			if (state.index !== nextIndex || stack.dataset.eapParallaxRendered !== 'true') {
				state.index = nextIndex;
				renderParallaxStack(stack, state.index);
				stack.dataset.eapParallaxRendered = 'true';
			}
		});
	};

	const updateHorizontalStages = () => {
		horizontalStages.forEach((stage) => {
			const rect = stage.getBoundingClientRect();
			const track = stage.querySelector('.eap-horizontal-track');
			if (!track) {
				return;
			}

			const start = parseFloat(stage.dataset.eapHorizontalStartPosition || '0');
			const end = parseFloat(stage.dataset.eapHorizontalEndPosition || '0');
			const scrollableDistance = Math.max(1, rect.height - window.innerHeight);
			const progress = clamp((0 - rect.top) / scrollableDistance, 0, 1);
			const current = prefersReducedMotion() ? start : start + ((end - start) * progress);
			track.style.transform = `translate3d(${current}px, 0, 0)`;
		});
	};

	const applyParallaxMedia = (media, effect, speed, phase) => {
		if (!media) {
			return;
		}

		const motion = prefersReducedMotion() ? 0 : clamp(phase, -1, 1);
		const distance = motion * speed * 140;
		let translateX = 0;
		let translateY = 0;
		let scale = 1.12;

		switch (effect) {
			case 'down':
				translateY = -distance;
				break;
			case 'left':
				translateX = distance;
				break;
			case 'right':
				translateX = -distance;
				break;
			case 'zoom-in':
				scale = 1.08 + ((1 - Math.abs(motion)) * speed * 0.22);
				break;
			case 'zoom-out':
				scale = 1.18 - ((1 - Math.abs(motion)) * speed * 0.22);
				break;
			case 'up':
			default:
				translateY = distance;
				break;
		}

		media.style.transform = `translate3d(${translateX}px, ${translateY}px, 0) scale(${scale})`;
	};

	const updateScrollDrivenEffects = () => {
		scrollTransforms.forEach((element) => {
			const rect = element.getBoundingClientRect();
			const progress = clamp((window.innerHeight - rect.top) / (window.innerHeight + rect.height), 0, 1);
			const x = parseFloat(element.dataset.eapTranslateX || '0') * progress;
			const y = parseFloat(element.dataset.eapTranslateY || '0') * progress;
			const rotate = parseFloat(element.dataset.eapRotate || '0') * progress;
			const scaleFrom = parseFloat(element.dataset.eapScaleFrom || '0.92');
			const scaleTo = parseFloat(element.dataset.eapScaleTo || '1');
			const scale = scaleFrom + ((scaleTo - scaleFrom) * progress);
			element.style.transform = `translate3d(${x}px, ${y}px, 0) rotate(${rotate}deg) scale(${scale})`;
			element.style.opacity = `${0.35 + (progress * 0.65)}`;
		});

		curves.forEach((curve) => {
			const rect = curve.getBoundingClientRect();
			const progress = clamp((window.innerHeight - rect.top) / (window.innerHeight + rect.height), 0, 1);
			const svg = curve.querySelector('svg');
			if (!svg) {
				return;
			}

			const direction = curve.dataset.eapCurve || 'up';
			const amount = direction === 'down' ? progress * 22 : (1 - progress) * 22;
			svg.style.transform = `translateY(${amount}%)`;
		});

		pinnedItems.forEach((item) => {
			item.style.setProperty('--eap-pin-offset', `${parseInt(item.dataset.eapPinOffset || '24', 10)}px`);
		});

		galleryParallaxItems.forEach((item) => {
			const rect = item.getBoundingClientRect();
			const speed = parseFloat(item.dataset.eapGallerySpeed || '0.9');
			const progress = clamp((window.innerHeight - rect.top) / (window.innerHeight + rect.height), 0, 1);
			const translateY = prefersReducedMotion() ? 0 : (0.5 - progress) * speed * 90;
			item.style.transform = `translate3d(0, ${translateY}px, 0)`;
		});

		updateParallaxStacks();
		updateHorizontalStages();
	};

	const bindGlobalScroll = () => {
		if (globalScrollBound) {
			return;
		}

		globalScrollBound = true;
		updateScrollDrivenEffects();
		window.addEventListener('scroll', updateScrollDrivenEffects, { passive: true });
		window.addEventListener('resize', () => {
			refreshHorizontalStages();
			parallaxStacks.forEach((stack) => {
				syncParallaxStackLayout(stack);
				const state = parallaxStackState.get(stack);
				renderParallaxStack(stack, state ? state.index : 0);
			});
			brandMarquees.forEach((instance) => {
				if (!instance || !instance.refresh) {
					return;
				}

				instance.refresh();
			});
			updateScrollDrivenEffects();
		});
	};

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
			originalSlides.forEach((slide) => {
				track.appendChild(slide.cloneNode(true));
			});

			const baseSlides = Array.from(track.children);
			cycleWidth = 0;
			baseSlides.forEach((slide) => {
				const slideStyles = window.getComputedStyle(slide);
				cycleWidth += slide.getBoundingClientRect().width;
				cycleWidth += parseFloat(slideStyles.marginRight || '0');
			});

			while (track.scrollWidth < slider.clientWidth + cycleWidth) {
				originalSlides.forEach((slide) => {
					track.appendChild(slide.cloneNode(true));
				});
			}

			offset = 0;
			lastTime = 0;
			track.style.transform = settings.directionMode === 'right'
				? `translate3d(${-cycleWidth}px, 0, 0)`
				: 'translate3d(0, 0, 0)';
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

				if (settings.directionMode === 'right') {
					track.style.transform = `translate3d(${(-cycleWidth + offset)}px, 0, 0)`;
				} else {
					track.style.transform = `translate3d(${-offset}px, 0, 0)`;
				}
			}

			frameId = window.requestAnimationFrame(animate);
		};

		refresh();

		if (settings.autoplayInteraction) {
			slider.addEventListener('mouseenter', () => {
				paused = true;
			});

			slider.addEventListener('mouseleave', () => {
				paused = false;
			});
		}

		frameId = window.requestAnimationFrame(animate);

		brandMarquees.add({
			refresh,
			destroy: () => {
				if (frameId) {
					window.cancelAnimationFrame(frameId);
				}
			}
		});
	};

	const initAnimatedText = (root, editorMode = false) => {
		getNodes(root, '.eap-animated-text-v2').forEach((element) => {
			if (element.dataset.eapAnimatedTextV2Init === 'true') {
				if (editorMode) {
					element.classList.add('is-visible', 'is-animated');
				}
				return;
			}

			element.dataset.eapAnimatedTextV2Init = 'true';
			const units = element.querySelectorAll('.eap-animated-text-v2__unit');
			const animation = element.dataset.eapAnimation || 'fade';
			const duration = parseInt(element.dataset.eapDuration || '850', 10);
			const delay = parseInt(element.dataset.eapDelay || '0', 10);
			const stagger = parseInt(element.dataset.eapStagger || '55', 10);

			element.style.setProperty('--eap-at-duration', `${duration}ms`);
			element.style.setProperty('--eap-at-delay', `${delay}ms`);
			units.forEach((unit, index) => {
				const unitDelay = delay + ('split' === animation ? index * stagger : 0);
				unit.style.setProperty('--eap-at-piece-delay', `${unitDelay}ms`);
			});

			element.classList.add('is-eap-ready');

			if ('none' === animation || editorMode) {
				window.requestAnimationFrame(() => element.classList.add('is-visible', 'is-animated'));
			} else {
				window.requestAnimationFrame(() => {
					if (isInViewport(element)) {
						markElementVisible(element);
					}
				});

				window.setTimeout(() => {
					if (!element.classList.contains('is-visible') && isInViewport(element)) {
						markElementVisible(element);
					}
				}, 120);

				window.setTimeout(() => {
					if (!element.classList.contains('is-visible') && isInViewport(element)) {
						markElementVisible(element);
					}
				}, 700);
			}
		});
	};

	const initTextMasks = (root, editorMode = false) => {
		getNodes(root, '.eap-text-mask-reveal').forEach((element) => {
			if (element.dataset.eapTextMaskInit === 'true') {
				if (editorMode) {
					element.classList.add('is-visible', 'is-animated');
				}
				return;
			}

			element.dataset.eapTextMaskInit = 'true';
			if (editorMode || element.dataset.eapMaskTrigger === 'load') {
				window.requestAnimationFrame(() => element.classList.add('is-visible', 'is-animated'));
			}
		});
	};

	const initWrapperLinks = (root) => {
		getNodes(root, '[data-eap-link]').forEach((element) => {
			if (element.dataset.eapLinkInit === 'true') {
				return;
			}

			element.dataset.eapLinkInit = 'true';
			element.style.cursor = 'pointer';
			element.addEventListener('click', (event) => {
				if (event.target.closest('a, button, input, textarea, select')) {
					return;
				}

				const url = element.dataset.eapLink;
				const target = element.dataset.eapTarget || '_self';
				if (url) {
					window.open(url, target);
				}
			});
		});
	};

	const initTypewriters = (root) => {
		getNodes(root, '[data-eap-typewriter]').forEach((element) => {
			if (element.dataset.eapTypewriterInit === 'true') {
				return;
			}

			element.dataset.eapTypewriterInit = 'true';
			const terms = JSON.parse(element.dataset.eapTypewriter || '[]');
			const speed = parseInt(element.dataset.eapSpeed || '90', 10);
			const pause = parseInt(element.dataset.eapPause || '1400', 10);
			const output = element.querySelector('.eap-typewriter-target');

			if (!terms.length || !output || prefersReducedMotion()) {
				if (output && terms[0]) {
					output.textContent = terms[0];
				}
				return;
			}

			let termIndex = 0;
			let charIndex = 0;
			let deleting = false;

			const step = () => {
				const current = terms[termIndex] || '';
				if (!deleting) {
					charIndex += 1;
					output.textContent = current.slice(0, charIndex);
					if (charIndex === current.length) {
						deleting = true;
						window.setTimeout(step, pause);
						return;
					}
				} else {
					charIndex -= 1;
					output.textContent = current.slice(0, charIndex);
					if (charIndex === 0) {
						deleting = false;
						termIndex = (termIndex + 1) % terms.length;
					}
				}

				window.setTimeout(step, deleting ? speed / 2 : speed);
			};

			step();
		});
	};

	const initAdvancedAnimatedText = (root) => {
		getNodes(root, '[data-eap-advanced-text]').forEach((element) => {
			if (element.dataset.eapAdvancedTextInit === 'true') {
				return;
			}

			element.dataset.eapAdvancedTextInit = 'true';
			const terms = JSON.parse(element.dataset.eapAdvancedText || '[]');
			const effect = element.dataset.eapAdvancedTextEffect || 'typewriter';
			const output = element.querySelector('.eap-advanced-animated-text__dynamic');
			const speed = parseInt(element.dataset.eapSpeed || '90', 10);
			const pause = parseInt(element.dataset.eapPause || '1500', 10);

			if (!terms.length || !output || prefersReducedMotion()) {
				if (output && terms[0]) {
					output.textContent = terms[0];
				}
				return;
			}

			if ('changing' === effect) {
				let index = 0;
				window.setInterval(() => {
					index = (index + 1) % terms.length;
					output.classList.remove('is-changing');
					window.requestAnimationFrame(() => {
						output.textContent = terms[index];
						output.classList.add('is-changing');
					});
				}, Math.max(pause, 400));
				return;
			}

			if ('typewriter' !== effect) {
				return;
			}

			let termIndex = 0;
			let charIndex = 0;
			let deleting = false;

			const tick = () => {
				const current = terms[termIndex] || '';
				output.textContent = current.slice(0, charIndex);

				if (!deleting && charIndex < current.length) {
					charIndex += 1;
					window.setTimeout(tick, speed);
					return;
				}

				if (!deleting && charIndex >= current.length) {
					deleting = true;
					window.setTimeout(tick, pause);
					return;
				}

				if (deleting && charIndex > 0) {
					charIndex -= 1;
					window.setTimeout(tick, Math.max(speed * 0.55, 20));
					return;
				}

				deleting = false;
				termIndex = (termIndex + 1) % terms.length;
				window.setTimeout(tick, speed);
			};

			tick();
		});
	};

	const initPortfolioFilters = (root) => {
		getNodes(root, '.eap-portfolio-grid').forEach((grid) => {
			if (grid.dataset.eapPortfolioInit === 'true') {
				return;
			}

			grid.dataset.eapPortfolioInit = 'true';
			const buttons = grid.querySelectorAll('[data-filter]');
			const items = grid.querySelectorAll('.eap-portfolio-item');

			buttons.forEach((button) => {
				button.addEventListener('click', () => {
					const filter = button.dataset.filter;
					buttons.forEach((item) => item.classList.remove('is-active'));
					button.classList.add('is-active');
					items.forEach((item) => {
						item.hidden = !(filter === 'all' || item.dataset.category === filter);
					});
				});
			});
		});
	};

	const initAccordions = (root) => {
		getNodes(root, '.eap-accordion').forEach((accordion) => {
			if (accordion.dataset.eapAccordionInit === 'true') {
				return;
			}

			accordion.dataset.eapAccordionInit = 'true';
			accordion.querySelectorAll('.eap-accordion-trigger').forEach((trigger) => {
				trigger.addEventListener('click', () => {
					const item = trigger.closest('.eap-accordion-item');
					const isOpen = item.classList.contains('is-open');

					accordion.querySelectorAll('.eap-accordion-item').forEach((panel) => {
						panel.classList.remove('is-open');
						panel.querySelector('.eap-accordion-trigger')?.setAttribute('aria-expanded', 'false');
					});

					if (!isOpen) {
						item.classList.add('is-open');
						trigger.setAttribute('aria-expanded', 'true');
					}
				});
			});
		});
	};

	const initToc = (root) => {
		getNodes(root, '[data-eap-toc]').forEach((toc) => {
			if (toc.dataset.eapTocInit === 'true') {
				return;
			}

			toc.dataset.eapTocInit = 'true';
			const selector = toc.dataset.eapToc || 'h2, h3';
			const list = toc.querySelector('.eap-toc-list');
			if (!list) {
				return;
			}

			document.querySelectorAll(selector).forEach((heading, index) => {
				if (toc.contains(heading)) {
					return;
				}

				if (!heading.id) {
					heading.id = `eap-heading-${index + 1}`;
				}

				const item = document.createElement('li');
				const link = document.createElement('a');
				link.href = `#${heading.id}`;
				link.textContent = heading.textContent || `Section ${index + 1}`;
				item.appendChild(link);
				list.appendChild(item);
			});
		});
	};

	const initHorizontal = (root) => {
		getNodes(root, '.eap-horizontal-stage').forEach((stage) => {
			horizontalStages.add(stage);
		});

		refreshHorizontalStages();
		updateHorizontalStages();
	};

	const initAnimatedButtons = (root) => {
		getNodes(root, '.eap-animated-button').forEach((button) => {
			if (button.dataset.eapAnimatedButtonInit === 'true') {
				return;
			}

			button.dataset.eapAnimatedButtonInit = 'true';
			const labels = JSON.parse(button.dataset.eapButtonLabels || '[]');
			const primary = button.querySelector('.eap-animated-button-primary');
			const secondary = button.querySelector('.eap-animated-button-secondary');
			if ((button.dataset.eapButtonMode || 'hover') !== 'loop' || labels.length < 2 || !primary || !secondary || prefersReducedMotion()) {
				return;
			}

			let current = 0;
			const speed = parseInt(button.dataset.eapButtonSpeed || '1800', 10);
			window.setInterval(() => {
				const next = (current + 1) % labels.length;
				button.classList.add('is-looping');
				secondary.textContent = labels[next];
				window.setTimeout(() => {
					primary.textContent = labels[next];
					button.classList.remove('is-looping');
					current = next;
				}, 320);
			}, speed);
		});
	};

	const initCursorPreviewLists = (root) => {
		ensureFloatingLayers();
		getNodes(root, '.eap-cursor-preview-list').forEach((list) => {
			list.querySelectorAll('.eap-preview-item').forEach((item) => {
				if (item.dataset.eapPreviewInit === 'true') {
					return;
				}

				item.dataset.eapPreviewInit = 'true';
				const size = parseInt(list.dataset.eapPreviewSize || '220', 10);

				item.addEventListener('mouseenter', () => {
					const image = item.dataset.eapPreviewImage;
					if (!image) {
						return;
					}

					previewFloating.style.width = `${size}px`;
					previewFloating.style.height = `${size}px`;
					previewFloating.innerHTML = `<img src="${image}" alt="" />`;
					previewFloating.classList.add('is-visible');
				});

				item.addEventListener('mouseleave', () => {
					previewFloating.classList.remove('is-visible');
				});

				item.addEventListener('mousemove', (event) => {
					previewFloating.style.transform = `translate3d(${event.clientX + 28}px, ${event.clientY + 28}px, 0) scale(1)`;
				});
			});
		});
	};

	const initCursorHosts = (root) => {
		ensureFloatingLayers();
		getNodes(root, '.eap-cursor-host').forEach((host) => {
			if (host.dataset.eapCursorHostInit === 'true') {
				return;
			}

			host.dataset.eapCursorHostInit = 'true';
			host.addEventListener('mouseenter', () => {
				cursorFollower.innerHTML = decodeHtml(host.dataset.eapCursor);
				cursorFollower.style.width = `${parseInt(host.dataset.eapCursorSize || '70', 10)}px`;
				cursorFollower.style.height = `${parseInt(host.dataset.eapCursorSize || '70', 10)}px`;
				cursorFollower.classList.add('is-visible');
			});

			host.addEventListener('mouseleave', () => {
				cursorFollower.classList.remove('is-visible');
			});

			host.addEventListener('mousemove', (event) => {
				const offsetX = parseInt(host.dataset.eapCursorOffsetX || '24', 10);
				const offsetY = parseInt(host.dataset.eapCursorOffsetY || '24', 10);
				cursorFollower.style.transform = `translate3d(${event.clientX + offsetX}px, ${event.clientY + offsetY}px, 0) scale(1)`;
			});
		});
	};

	const initTextHoverImages = (root) => {
		getNodes(root, '[data-eap-text-hover-image]').forEach((wrapper) => {
			if (wrapper.dataset.eapTextHoverImageInit === 'true') {
				return;
			}

			wrapper.dataset.eapTextHoverImageInit = 'true';

			const trigger = wrapper.querySelector('.eap-text-hover-image__trigger');
			const preview = wrapper.querySelector('.eap-text-hover-image__preview');

			if (!trigger || !preview) {
				return;
			}

			let frameId = 0;
			let isAnimating = false;
			let currentX = 0;
			let currentY = 0;
			let targetX = 0;
			let targetY = 0;
			const ease = 0.16;

			const render = () => {
				currentX += (targetX - currentX) * ease;
				currentY += (targetY - currentY) * ease;

				preview.style.setProperty('--eap-text-hover-cursor-x', `${currentX}px`);
				preview.style.setProperty('--eap-text-hover-cursor-y', `${currentY}px`);

				if (
					Math.abs(targetX - currentX) < 0.2 &&
					Math.abs(targetY - currentY) < 0.2 &&
					!wrapper.classList.contains('is-active')
				) {
					isAnimating = false;
					frameId = 0;
					return;
				}

				frameId = window.requestAnimationFrame(render);
			};

			const startAnimation = () => {
				if (isAnimating) {
					return;
				}

				isAnimating = true;
				frameId = window.requestAnimationFrame(render);
			};

			const show = () => {
				wrapper.classList.add('is-active');
				startAnimation();
			};

			const hide = () => {
				wrapper.classList.remove('is-active');
				startAnimation();
			};

			const move = (event) => {
				const rect = wrapper.getBoundingClientRect();
				targetX = event.clientX - rect.left;
				targetY = event.clientY - rect.top;

				if (!frameId) {
					currentX = targetX;
					currentY = targetY;
				}

				startAnimation();
			};

			trigger.addEventListener('mouseenter', show);
			trigger.addEventListener('focus', show);
			trigger.addEventListener('mouseleave', hide);
			trigger.addEventListener('blur', hide);
			wrapper.addEventListener('mousemove', move);
			trigger.addEventListener('mousemove', move);
		});
	};

	const initOffcanvas = (root) => {
		getNodes(root, '.eap-offcanvas').forEach((wrapper) => {
			if (wrapper.dataset.eapOffcanvasInit === 'true') {
				return;
			}

			wrapper.dataset.eapOffcanvasInit = 'true';
			const toggle = wrapper.querySelector('[data-eap-offcanvas-toggle]');
			const overlay = wrapper.querySelector('.eap-offcanvas-overlay');
			const panel = wrapper.querySelector('.eap-offcanvas-panel');
			if (!toggle || !overlay || !panel) {
				return;
			}

			const open = () => {
				panel.classList.add('is-active');
				overlay.classList.add('is-active');
				panel.setAttribute('aria-hidden', 'false');
				toggle.setAttribute('aria-expanded', 'true');
				document.body.classList.add('eap-offcanvas-open');
			};

			const close = () => {
				panel.classList.remove('is-active');
				overlay.classList.remove('is-active');
				panel.setAttribute('aria-hidden', 'true');
				toggle.setAttribute('aria-expanded', 'false');
				document.body.classList.remove('eap-offcanvas-open');
			};

			toggle.addEventListener('click', () => {
				if (panel.classList.contains('is-active')) {
					close();
				} else {
					open();
				}
			});

			wrapper.querySelectorAll('[data-eap-offcanvas-close]').forEach((closeTarget) => {
				closeTarget.addEventListener('click', close);
			});

			document.addEventListener('keydown', (event) => {
				if (event.key === 'Escape') {
					close();
				}
			});
		});
	};

	const initImageBoxes = (root, editorMode = false) => {
		getNodes(root, '.eap-image-box').forEach((element) => {
			if (element.dataset.eapImageBoxInit !== 'true') {
				element.dataset.eapImageBoxInit = 'true';

				if (element.classList.contains('eap-image-box--pointer')) {
					const mediaWrap = element.querySelector('.eap-image-box-media-wrap');
					const tooltip = element.querySelector('.eap-image-box-tooltip');

					if (mediaWrap && tooltip) {
						mediaWrap.addEventListener('mouseenter', () => {
							element.classList.add('is-pointer-active');
						});

						mediaWrap.addEventListener('mouseleave', () => {
							element.classList.remove('is-pointer-active');
						});

						mediaWrap.addEventListener('mousemove', (event) => {
							const rect = mediaWrap.getBoundingClientRect();
							const x = event.clientX - rect.left;
							const y = event.clientY - rect.top;
							tooltip.style.left = `${x}px`;
							tooltip.style.top = `${y}px`;
						});
					}
				}
			}

			if (editorMode) {
				element.classList.add('is-visible');
			}
		});
	};

	const initImageBoxSliders = (root) => {
		getNodes(root, '.eap-image-box-slider__swiper').forEach((slider) => {
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
				breakpoints: {
					768: {
						slidesPerView: settings.slidesTablet || 2
					},
					1025: {
						slidesPerView: settings.slidesDesktop || 3
					}
				}
			};

			if (settings.navigation && nextEl && prevEl) {
				config.navigation = {
					nextEl,
					prevEl
				};
			}

			if (settings.pagination && pagination) {
				config.pagination = {
					el: pagination,
					type: settings.paginationType || 'bullets',
					clickable: true
				};
			}

			if (settings.autoplay) {
				config.autoplay = {
					delay: settings.autoplayDelay || 4000,
					disableOnInteraction: false,
					pauseOnMouseEnter: !!settings.pauseOnHover
				};
			}

			const syncSliderBoxes = () => {
				getNodes(slider, '.eap-image-box').forEach((box) => {
					box.classList.add('is-visible');
				});

				// Re-run the image box initializer in forced-visible mode so
				// loop duplicates keep their media/content state in sync.
				initImageBoxes(slider, true);
			};

			config.on = {
				init() {
					syncSliderBoxes();
				},
				slideChangeTransitionStart() {
					syncSliderBoxes();
				},
				slideChangeTransitionEnd() {
					syncSliderBoxes();
				},
				transitionEnd() {
					syncSliderBoxes();
				},
				loopFix() {
					syncSliderBoxes();
				},
				resize() {
					syncSliderBoxes();
				}
			};

			const swiper = new window.Swiper(slider, config);
			if (swiper && typeof swiper.update === 'function') {
				swiper.update();
			}
			syncSliderBoxes();
		});
	};

	const initBrandSliders = (root) => {
		getNodes(root, '.eap-brand-slider__swiper').forEach((slider) => {
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
				return;
			}

			const config = {
				slidesPerView: mobileSlides,
				spaceBetween: 0,
				speed: settings.speed || 5000,
				loop: !!settings.loop,
				allowTouchMove: !!settings.allowTouchMove,
				watchSlidesProgress: true,
				grid: settings.grid ? {
					rows: 2,
					fill: 'row'
				} : undefined,
				breakpoints: {
					768: {
						slidesPerView: tabletSlides
					},
					1025: {
						slidesPerView: desktopSlides
					}
				}
			};

			if (settings.navigation && nextEl && prevEl) {
				config.navigation = {
					nextEl,
					prevEl
				};
			}

			if (settings.pagination && pagination) {
				config.pagination = {
					el: pagination,
					type: settings.paginationType || 'bullets',
					clickable: true
				};
			}

			if (settings.mousewheel) {
				config.mousewheel = {
					forceToAxis: true
				};
			}

			// eslint-disable-next-line no-new
			const swiper = new window.Swiper(slider, config);
			if (swiper && typeof swiper.update === 'function') {
				swiper.update();
			}
		});
	};

	const initImageHotspots = (root, editorMode = false) => {
		getNodes(root, '.eap-image-hotspot').forEach((widget) => {
			if (widget.dataset.eapHotspotInit === 'true') {
				return;
			}

			widget.dataset.eapHotspotInit = 'true';

			const trigger = widget.dataset.eapHotspotTrigger || 'hover';
			const items = getNodes(widget, '[data-eap-hotspot-item]');

			const closeAll = () => {
				items.forEach((item) => {
					item.classList.remove('is-active');
					const button = item.querySelector('.eap-image-hotspot__button');
					if (button) {
						button.setAttribute('aria-expanded', 'false');
					}
				});
			};

			items.forEach((item, index) => {
				const button = item.querySelector('.eap-image-hotspot__button');
				if (!button) {
					return;
				}

				if (trigger === 'click') {
					button.addEventListener('click', (event) => {
						event.preventDefault();
						const isActive = item.classList.contains('is-active');
						closeAll();

						if (!isActive) {
							item.classList.add('is-active');
							button.setAttribute('aria-expanded', 'true');
						}
					});
				} else {
					item.addEventListener('mouseenter', () => {
						item.classList.add('is-active');
						button.setAttribute('aria-expanded', 'true');
					});

					item.addEventListener('mouseleave', () => {
						item.classList.remove('is-active');
						button.setAttribute('aria-expanded', 'false');
					});

					item.addEventListener('focusin', () => {
						item.classList.add('is-active');
						button.setAttribute('aria-expanded', 'true');
					});

					item.addEventListener('focusout', (event) => {
						if (!item.contains(event.relatedTarget)) {
							item.classList.remove('is-active');
							button.setAttribute('aria-expanded', 'false');
						}
					});
				}

				if (editorMode && index === 0) {
					item.classList.add('is-active');
					button.setAttribute('aria-expanded', 'true');
				}
			});

			if (trigger === 'click') {
				document.addEventListener('click', (event) => {
					if (!widget.contains(event.target)) {
						closeAll();
					}
				});
			}
		});
	};

	const initImageGalleries = (root) => {
		getNodes(root, '.eap-image-gallery').forEach((gallery) => {
			if (gallery.dataset.eapImageGalleryInit === 'true') {
				return;
			}

			gallery.dataset.eapImageGalleryInit = 'true';

			if (gallery.dataset.eapGallerySmooth !== 'yes') {
				return;
			}

			gallery.querySelectorAll('.eap-image-gallery__parallax').forEach((item) => {
				galleryParallaxItems.add(item);
			});
		});
	};

	const initImageComparisons = (root) => {
		getNodes(root, '[data-eap-image-comparison]').forEach((widget) => {
			if (widget.dataset.eapImageComparisonInit === 'true') {
				return;
			}

			widget.dataset.eapImageComparisonInit = 'true';

			const media = widget.querySelector('.eap-image-comparison__media');
			const overlay = widget.querySelector('[data-eap-image-comparison-overlay]');
			const handle = widget.querySelector('[data-eap-image-comparison-handle]');
			const direction = widget.dataset.eapDirection || 'horizontal';
			let position = parseFloat(widget.dataset.eapPosition || '50');
			let dragging = false;

			if (!media || !overlay || !handle) {
				return;
			}

			const applyPosition = (value) => {
				position = clamp(value, 0, 100);
				widget.dataset.eapPosition = `${position}`;
				if (direction === 'vertical') {
					overlay.style.clipPath = `inset(0 0 ${100 - position}% 0)`;
					handle.style.top = `${position}%`;
					handle.style.left = '50%';
				} else {
					overlay.style.clipPath = `inset(0 ${100 - position}% 0 0)`;
					handle.style.left = `${position}%`;
					handle.style.top = '50%';
				}
			};

			const updateFromEvent = (event) => {
				const rect = media.getBoundingClientRect();
				if (direction === 'vertical') {
					const offset = ((event.clientY - rect.top) / Math.max(rect.height, 1)) * 100;
					applyPosition(offset);
				} else {
					const offset = ((event.clientX - rect.left) / Math.max(rect.width, 1)) * 100;
					applyPosition(offset);
				}
			};

			const stopDragging = () => {
				dragging = false;
				document.body.classList.remove('eap-is-dragging');
			};

			handle.addEventListener('pointerdown', (event) => {
				event.preventDefault();
				dragging = true;
				document.body.classList.add('eap-is-dragging');
				handle.setPointerCapture?.(event.pointerId);
				updateFromEvent(event);
			});

			media.addEventListener('pointermove', (event) => {
				if (!dragging) {
					return;
				}
				updateFromEvent(event);
			});

			media.addEventListener('pointerdown', (event) => {
				if (event.target.closest('[data-eap-image-comparison-handle]')) {
					return;
				}
				dragging = true;
				document.body.classList.add('eap-is-dragging');
				updateFromEvent(event);
			});

			window.addEventListener('pointermove', (event) => {
				if (!dragging) {
					return;
				}
				updateFromEvent(event);
			});

			window.addEventListener('pointerup', stopDragging);
			window.addEventListener('pointercancel', stopDragging);
			window.addEventListener('resize', () => applyPosition(position));

			applyPosition(position);
		});
	};

	const initParallaxSections = (root, editorMode = false) => {
		getNodes(root, '[data-eap-parallax-stack="true"]').forEach((stack) => {
			parallaxStacks.add(stack);

			const panels = Array.from(stack.querySelectorAll('[data-eap-parallax-section="true"]'));
			const state = {
				index: 0
			};
			parallaxStackState.set(stack, state);

			panels.forEach((panel, index) => {
				const content = panel.querySelector('[data-eap-parallax-content="true"]');
				if (content) {
					content.dataset.eapVisibilityBound = 'true';
				}

				panel.querySelectorAll('.eap-animated-text, .eap-text-mask-reveal, .eap-motion').forEach((element) => {
					element.dataset.eapVisibilityBound = 'true';
				});

				if (editorMode) {
					panel.classList.toggle('is-active', index === 0);
					panel.style.opacity = index === 0 ? '1' : '0';
					panel.style.transform = 'translate3d(0, 0, 0) scale(1)';
					if (content) {
						content.classList.toggle('is-visible', index === 0);
					}
					panel.querySelectorAll('.eap-animated-text, .eap-text-mask-reveal, .eap-motion').forEach((element) => {
						element.classList.toggle('is-visible', index === 0);
						element.classList.toggle('is-animated', index === 0);
					});
				}
			});

			syncParallaxStackLayout(stack);
			renderParallaxStack(stack, state.index);
		});

		if (!editorMode) {
			updateParallaxStacks();
		}
	};

	const collectScrollDrivenElements = (root) => {
		getNodes(root, '[data-eap-transform="true"]').forEach((element) => scrollTransforms.add(element));
		getNodes(root, '.eap-curve-swipe').forEach((element) => curves.add(element));
		getNodes(root, '[data-eap-pin="true"]').forEach((element) => pinnedItems.add(element));
	};

	const initVisibilityObservers = (root, editorMode = false) => {
		observeElements(root, '.eap-motion,[data-eap-counter],.eap-nav-animate,.eap-animated-text[data-eap-split-trigger="scroll"],.eap-animated-text-v2[data-eap-animated-text-v2="true"],.eap-text-mask-reveal[data-eap-mask-trigger="scroll"],.eap-image-box,.eap-image-hotspot,.eap-image-widget,.eap-icon-box,.eap-testimonial-box,.eap-advanced-button-wrap');

		if (editorMode) {
			getNodes(root, '.eap-motion,.eap-nav-animate,.eap-animated-text,.eap-animated-text-v2,.eap-text-mask-reveal,.eap-image-box,.eap-image-hotspot,.eap-image-widget,.eap-icon-box,.eap-testimonial-box').forEach((element) => {
				element.classList.add('is-visible', 'is-animated');
			});
		}
	};

	const initRoot = (root, { editorMode = false } = {}) => {
		initParallaxSections(root, editorMode);
		initAnimatedText(root, editorMode);
		initVisibilityObservers(root, editorMode);
		initTextMasks(root, editorMode);
		initWrapperLinks(root);
		initTypewriters(root);
		initAdvancedAnimatedText(root);
		initPortfolioFilters(root);
		initAccordions(root);
		initToc(root);
		initHorizontal(root);
		initAnimatedButtons(root);
		initCursorPreviewLists(root);
		initCursorHosts(root);
		initTextHoverImages(root);
		initOffcanvas(root);
		initImageBoxes(root, editorMode);
		initImageBoxSliders(root);
		initBrandSliders(root);
		initImageHotspots(root, editorMode);
		initImageGalleries(root);
		initImageComparisons(root);
		collectScrollDrivenElements(root);
		bindGlobalScroll();
	};

	document.addEventListener('DOMContentLoaded', () => {
		initRoot(document);
	});

	window.addEventListener('elementor/frontend/init', () => {
		if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
			return;
		}

		const isEditorMode = () => {
			if (!window.elementorFrontend) {
				return false;
			}

			if (typeof window.elementorFrontend.isEditMode === 'function') {
				return !!window.elementorFrontend.isEditMode();
			}

			return !!window.elementorFrontend.isEditMode;
		};

		window.elementorFrontend.hooks.addAction('frontend/element_ready/global', ($scope) => {
			initRoot($scope[0], { editorMode: isEditorMode() });
		});
	});
})();
