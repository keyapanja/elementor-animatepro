(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const parallaxStacks = new Set();
	const parallaxStackState = new WeakMap();
	let globalScrollBound = false;

	const getParallaxStackSettings = (stack) => {
		const styles = window.getComputedStyle(stack);
		return {
			transitionMode: stack.dataset.eapPanelTransition || 'slide-up',
			distanceValue: parseFloat(styles.getPropertyValue('--eap-parallax-transition-distance').trim() || '72') || 72
		};
	};

	const applyParallaxMedia = (media, effect, speed, phase) => {
		if (!media) {
			return;
		}
		const motion = api.prefersReducedMotion() ? 0 : api.clamp(phase, -1, 1);
		const distance = motion * speed * 140;
		let translateX = 0;
		let translateY = 0;
		let scale = 1.12;
		switch (effect) {
			case 'down': translateY = -distance; break;
			case 'left': translateX = distance; break;
			case 'right': translateX = -distance; break;
			case 'zoom-in': scale = 1.08 + ((1 - Math.abs(motion)) * speed * 0.22); break;
			case 'zoom-out': scale = 1.18 - ((1 - Math.abs(motion)) * speed * 0.22); break;
			case 'up':
			default: translateY = distance; break;
		}
		media.style.transform = `translate3d(${translateX}px, ${translateY}px, 0) scale(${scale})`;
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
			let translateY = 0;
			let scale = 1;
			const opacity = index === activeIndex ? 1 : 0;
			if (index !== activeIndex) {
				switch (transitionMode) {
					case 'slide-down': translateY = relative < 0 ? distanceValue : -distanceValue; break;
					case 'scale': scale = 0.92; break;
					case 'fade': break;
					case 'slide-up':
					default: translateY = relative < 0 ? -distanceValue : distanceValue; break;
				}
			}
			panel.style.opacity = `${opacity}`;
			panel.style.transform = `translate3d(0, ${translateY}px, 0) scale(${scale})`;
			panel.classList.toggle('is-active', index === activeIndex);
			if (content) {
				index === activeIndex ? api.markElementVisible(content) : api.markElementHidden(content);
			}
			motionChildren.forEach((element) => {
				index === activeIndex ? api.markElementVisible(element) : api.markElementHidden(element);
			});
			if (media) {
				applyParallaxMedia(media, panel.dataset.eapParallaxEffect || 'up', parseFloat(panel.dataset.eapParallaxSpeed || '0.45'), 0);
			}
		});
		if (sharedMedia) {
			const activePanel = panels[activeIndex] || panels[0];
			applyParallaxMedia(sharedMedia, activePanel.dataset.eapParallaxEffect || 'up', parseFloat(activePanel.dataset.eapParallaxSpeed || '0.45'), 0);
		}
	};

	const syncParallaxStackLayout = (stack) => {
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
			const scrolled = api.clamp(pinOffset - rect.top, 0, stickyTravel);
			const isBefore = rect.top > pinOffset;
			const isEnded = stickyTravel > 0 && rect.top <= pinOffset - stickyTravel;
			const isPinned = !isBefore && !isEnded;
			const nextIndex = isEnded ? panels.length - 1 : stickyTravel > 0 ? api.clamp(Math.round(scrolled / Math.max(viewportHeight, 1)), 0, panels.length - 1) : 0;
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

	const bindGlobalScroll = () => {
		if (globalScrollBound) {
			return;
		}
		globalScrollBound = true;
		updateParallaxStacks();
		window.addEventListener('scroll', updateParallaxStacks, { passive: true });
		window.addEventListener('resize', () => {
			parallaxStacks.forEach((stack) => {
				syncParallaxStackLayout(stack);
				const state = parallaxStackState.get(stack);
				renderParallaxStack(stack, state ? state.index : 0);
			});
			updateParallaxStacks();
		});
	};

	const initParallaxSections = (root, editorMode = false) => {
		api.getNodes(root, '[data-eap-parallax-stack="true"]').forEach((stack) => {
			parallaxStacks.add(stack);
			const panels = Array.from(stack.querySelectorAll('[data-eap-parallax-section="true"]'));
			const state = { index: 0 };
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
		bindGlobalScroll();
	};

	api.register('parallax-sections', (root, { editorMode = false } = {}) => {
		initParallaxSections(root, editorMode);
	});
})();
