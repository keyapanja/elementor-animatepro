/* global EAPFrontend, gsap, ScrollTrigger */
(() => {
	const SELECTOR = '.eap-timeline';

	// Kill any ScrollTriggers attached to timeline widgets that are no longer
	// in the DOM. Elementor's editor re-renders widgets on each edit, which
	// would otherwise leave orphan triggers behind (and their pin-spacers),
	// causing duplicate spacers, layout shift, and the widget appearing to
	// "repeat" between sections.
	const cleanupOrphans = () => {
		if (typeof window.ScrollTrigger === 'undefined') {
			return;
		}
		window.ScrollTrigger.getAll().forEach((trig) => {
			const t = trig.trigger;
			if (t && t.classList && t.classList.contains('eap-timeline') && !document.body.contains(t)) {
				trig.kill();
			}
		});
	};

	const initTimeline = (root, options) => {
		if (!root) {
			return;
		}

		cleanupOrphans();

		// Detect Elementor editor mode (passed through by core.js or queried
		// directly). Pinning + scrub inside the editor iframe is unreliable
		// and tends to spawn duplicate spacers when the user edits controls.
		const editorMode = !!(
			(options && options.editorMode) ||
			(window.elementorFrontend && typeof window.elementorFrontend.isEditMode === 'function' && window.elementorFrontend.isEditMode())
		);

		const widgets = EAPFrontend.getNodes(root, SELECTOR);
		widgets.forEach((widget) => {
			if (widget.dataset.eapTimelineBound === '1') {
				return;
			}
			widget.dataset.eapTimelineBound = '1';

			const layout = widget.dataset.layout || 'center';
			const lineFillOn = widget.dataset.lineFill === '1';
			const markerOn = widget.dataset.markerFollow === '1';
			const intro = widget.dataset.intro || 'fade-up';
			const introDuration = parseInt(widget.dataset.introDuration, 10) || 800;
			const introStagger = parseInt(widget.dataset.introStagger, 10) || 120;
			const introDistance = parseInt(widget.dataset.introDistance, 10) || 50;
			const horizScrub = parseFloat(widget.dataset.horizontalScrub) || 1;
			const forceFullwidth = widget.dataset.forceFullwidth === '1';
			const pinPosition = widget.dataset.pinPosition === 'top' ? 'top' : 'center';

			widget.style.setProperty('--eap-tl-intro-duration', introDuration + 'ms');
			widget.style.setProperty('--eap-tl-intro-distance', introDistance + 'px');

			const items = Array.from(widget.querySelectorAll('.eap-timeline__item'));
			const line = widget.querySelector('.eap-timeline__line');
			const lineFill = widget.querySelector('.eap-timeline__line-fill');
			const lineMarker = widget.querySelector('.eap-timeline__line-marker');

			// Mark intro-ready so CSS hides the items before they animate in.
			if (intro !== 'none') {
				widget.setAttribute('data-intro-ready', '1');
			}

			const hasGsap = typeof window.gsap !== 'undefined';
			const hasScrollTrigger = hasGsap && typeof window.ScrollTrigger !== 'undefined';

			if (hasScrollTrigger && typeof EAPFrontend.ensureScrollTrigger === 'function') {
				EAPFrontend.ensureScrollTrigger();
			}

			/* -------------------------------------------------------------- *
			 * Intro animations
			 * -------------------------------------------------------------- */
			const triggerIntro = (item, idx) => {
				if (item.classList.contains('is-in')) {
					return;
				}
				const delay = idx * introStagger;
				item.style.setProperty('--eap-tl-intro-delay', delay + 'ms');
				// Defer one frame so the transition picks up the change.
				window.requestAnimationFrame(() => {
					item.classList.add('is-in');
				});
			};

			if (intro === 'none') {
				items.forEach((item) => item.classList.add('is-in'));
			} else if (hasScrollTrigger) {
				items.forEach((item, idx) => {
					window.ScrollTrigger.create({
						trigger: item,
						start: 'top 88%',
						once: true,
						onEnter: () => triggerIntro(item, idx)
					});
				});
			} else if ('IntersectionObserver' in window) {
				const observer = new IntersectionObserver((entries) => {
					entries.forEach((entry) => {
						if (entry.isIntersecting) {
							const idx = items.indexOf(entry.target);
							triggerIntro(entry.target, Math.max(idx, 0));
							observer.unobserve(entry.target);
						}
					});
				}, { threshold: 0.2, rootMargin: '0px 0px -10% 0px' });
				items.forEach((item) => observer.observe(item));
			} else {
				items.forEach((item, idx) => triggerIntro(item, idx));
			}

			/* -------------------------------------------------------------- *
			 * Vertical layouts: line fill + travelling marker on scroll
			 * -------------------------------------------------------------- */
			if (layout !== 'horizontal' && line && (lineFillOn || markerOn)) {
				const updateVertical = (progress) => {
					const p = Math.max(0, Math.min(1, progress));
					if (lineFillOn && lineFill) {
						lineFill.style.height = (p * 100) + '%';
					}
					if (markerOn && lineMarker) {
						lineMarker.style.top = (p * 100) + '%';
					}
				};

				if (hasScrollTrigger) {
					window.ScrollTrigger.create({
						trigger: widget,
						start: 'top 70%',
						end: 'bottom 70%',
						scrub: 0.6,
						onUpdate: (self) => updateVertical(self.progress)
					});
				} else {
					const onScroll = () => {
						const rect = widget.getBoundingClientRect();
						const vh = window.innerHeight || document.documentElement.clientHeight;
						const start = vh * 0.7;
						const end = vh * 0.7;
						const total = rect.height + (start - end);
						const progress = (start - rect.top) / total;
						updateVertical(progress);
					};
					window.addEventListener('scroll', onScroll, { passive: true });
					window.addEventListener('resize', onScroll);
					onScroll();
				}
			}

			/* -------------------------------------------------------------- *
			 * Horizontal layout: pinned, translateX driven by scroll
			 * -------------------------------------------------------------- */
			if (layout === 'horizontal') {
				const viewport = widget.querySelector('.eap-timeline__horizontal-viewport');
				const track = widget.querySelector('.eap-timeline__horizontal-track');

				if (viewport && track) {
					const measureDistance = () => Math.max(0, track.scrollWidth - viewport.clientWidth);

					const updateHorizontal = (progress) => {
						const p = Math.max(0, Math.min(1, progress));
						const distance = measureDistance();
						track.style.transform = 'translate3d(' + (-distance * p) + 'px, 0, 0)';
						if (lineFillOn && lineFill) {
							lineFill.style.width = (p * 100) + '%';
						}
						if (markerOn && lineMarker) {
							lineMarker.style.left = (p * 100) + '%';
						}
					};

					// In the Elementor editor, GSAP pin+scrub inside the iframe is
					// unreliable and rebuilding it on every edit cycle accumulates pin
					// spacers. Bail out to a plain scrollable row so editing controls
					// stays responsive and clean.
					if (editorMode || !hasScrollTrigger) {
						viewport.style.overflowX = 'auto';
						viewport.style.overflowY = 'hidden';
						track.style.transform = 'none';
						if (lineFillOn && lineFill) {
							lineFill.style.width = '100%';
						}
						if (markerOn && lineMarker) {
							lineMarker.style.left = '0%';
						}
						return;
					}

					// 'center center' pins the widget's vertical center to the viewport's
					// vertical center — gives card content equal room above and below the
					// line while scrolling. 'top top' is the classic flush-to-top behavior.
					const startPosition = pinPosition === 'top' ? 'top top' : 'center center';

					// Initialize the track at progress 0 so the very first frame after pin
					// engages does not flash an unset transform value.
					updateHorizontal(0);

					// SINGLE trigger — `invalidateOnRefresh: true` combined with `end` as
					// a function means the scroll distance is recalculated on every
					// ScrollTrigger.refresh() WITHOUT rebuilding the trigger or its pin
					// spacer. Rebuilding on resize used to leave duplicate pin spacers
					// behind, which is what made the widget appear to overlap or repeat
					// next to other sections.
					const trigger = window.ScrollTrigger.create({
						trigger: widget,
						start: startPosition,
						end: () => '+=' + Math.max(1, measureDistance() * horizScrub),
						pin: true,
						pinType: 'fixed',
						pinSpacing: true,
						scrub: 0.3,
						anticipatePin: 1,
						fastScrollEnd: true,
						invalidateOnRefresh: true,
						onUpdate: (self) => updateHorizontal(self.progress),
						onEnter: () => updateHorizontal(0),
						onLeaveBack: () => updateHorizontal(0),
						onLeave: () => updateHorizontal(1)
					});

					// Debounced refresh — recomputes start/end and pin layout without
					// killing/recreating the trigger.
					let resizeTimer = 0;
					const onResize = () => {
						window.clearTimeout(resizeTimer);
						resizeTimer = window.setTimeout(() => {
							window.ScrollTrigger.refresh();
						}, 150);
					};
					window.addEventListener('resize', onResize);

					// Late layout changes (fonts, images) can shift the measured distance.
					// Refresh once more when the page has fully loaded so the final pin
					// position matches the real content size.
					window.addEventListener('load', () => {
						window.ScrollTrigger.refresh();
					}, { once: true });
					if (document.fonts && document.fonts.ready) {
						document.fonts.ready.then(() => {
							window.ScrollTrigger.refresh();
						}).catch(() => {});
					}

					// If the widget gets removed from the DOM (e.g. Elementor editor
					// re-render), kill the trigger so its pin spacer doesn't linger.
					if (typeof MutationObserver !== 'undefined') {
						const observer = new MutationObserver(() => {
							if (!document.body.contains(widget)) {
								trigger.kill();
								window.removeEventListener('resize', onResize);
								observer.disconnect();
							}
						});
						if (widget.parentNode) {
							observer.observe(widget.parentNode, { childList: true });
						}
					}
				}
			}
		});
	};

	if (window.EAPFrontend && typeof window.EAPFrontend.register === 'function') {
		window.EAPFrontend.register('timeline', (root, options) => initTimeline(root, options));
	} else {
		// EAPFrontend missing — bind defensively.
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', () => initTimeline(document));
		} else {
			initTimeline(document);
		}
	}
})();
