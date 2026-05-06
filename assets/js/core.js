(() => {
	if (window.EAPFrontend) {
		return;
	}

	const modules = new Map();
	let previewFloating;
	let cursorFollower;
	let intersectionObserver;
	let gsapRegistered = false;
	let domInitialized = false;

	const clamp = (value, min, max) => Math.min(max, Math.max(min, value));
	const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

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

		return {
			previewFloating,
			cursorFollower,
		};
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

		if (element.classList.contains('eap-icon-box') || element.classList.contains('eap-testimonial-box')) {
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

		if (element.classList.contains('eap-icon-box') || element.classList.contains('eap-testimonial-box') || element.classList.contains('eap-advanced-button-wrap')) {
			element.classList.remove('is-visible');
		}

		if (element.classList.contains('eap-animated-text-v2') && element.dataset.eapPlayOnce === 'true' && element.classList.contains('is-animated')) {
			return;
		}

		if (element.classList.contains('eap-animated-text') || element.classList.contains('eap-animated-text-v2') || element.classList.contains('eap-text-mask-reveal')) {
			element.classList.remove('is-visible', 'is-animated');
		}

		if (element.classList.contains('eap-image-box') || element.classList.contains('eap-image-hotspot') || element.classList.contains('eap-image-widget')) {
			element.classList.remove('is-visible');
		}
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
					onEnter: () => markElementVisible(element),
					onEnterBack: () => markElementVisible(element),
					onLeave: () => markElementHidden(element),
					onLeaveBack: () => markElementHidden(element)
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

	const runModules = (root, options = {}) => {
		modules.forEach((init) => {
			if (typeof init === 'function') {
				init(root, options, window.EAPFrontend);
			}
		});
	};

	const bootDom = () => {
		if (domInitialized) {
			return;
		}

		domInitialized = true;
		runModules(document, { editorMode: false });
	};

	const bindElementor = () => {
		const attachHook = () => {
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
				runModules($scope[0], { editorMode: isEditorMode() });
			});
		};

		if (window.elementorFrontend && window.elementorFrontend.hooks) {
			attachHook();
			return;
		}

		window.addEventListener('elementor/frontend/init', attachHook, { once: true });
	};

	window.EAPFrontend = {
		register(name, init) {
			modules.set(name, init);
			if (domInitialized && typeof init === 'function') {
				init(document, { editorMode: false }, window.EAPFrontend);
			}
		},
		clamp,
		prefersReducedMotion,
		decodeHtml,
		getNodes,
		ensureFloatingLayers,
		hasScrollTrigger,
		ensureScrollTrigger,
		markElementVisible,
		markElementHidden,
		isInViewport,
		observeElements,
		ensureObserver,
		boot(root, options = {}) {
			runModules(root, options);
		}
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', bootDom);
	} else {
		bootDom();
	}

	bindElementor();
})();
