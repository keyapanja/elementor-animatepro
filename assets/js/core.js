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

	/*
	 * Shared FLIP filter.
	 *
	 * Hides the items that no longer match and animates the survivors into their
	 * new positions: measure where they are, change the DOM, measure again,
	 * transform each back to where it was, then release it. The browser then
	 * animates one transform per item rather than being asked to transition
	 * top/left, which it cannot do cheaply.
	 *
	 * Used by Filterable Posts and Portfolio. Lives here rather than in either
	 * widget because a second copy would drift — this logic has already needed
	 * one subtle correctness fix (see the run-token note below).
	 *
	 * The caller supplies a `matches` predicate; everything about WHY an item
	 * matches stays in the widget.
	 */
	const flipRuns = new WeakMap();

	const flipFilter = (container, items, matches, options = {}) => {
		const duration = typeof options.duration === 'number' ? options.duration : 400;
		const onDone = typeof options.onDone === 'function' ? options.onDone : null;

		/*
		 * A cancelled run cannot tidy up after itself — it discovers it was
		 * cancelled inside a timeout and returns — so each run clears the previous
		 * one's residue before it starts. Leaving it to the cancelled run would
		 * strand an item on `is-entering` (opacity 0) or on an inline transform
		 * that outranks the class meant to reset it, and it would come back
		 * invisible or offset.
		 */
		const clear = (item) => {
			item.style.transition = '';
			item.style.transform = '';
			item.classList.remove('is-leaving', 'is-entering', 'is-entered');
		};

		const keep = items.filter(matches);

		if (!duration || prefersReducedMotion()) {
			items.forEach((item) => {
				clear(item);
				item.classList.toggle('is-hidden', !matches(item));
			});
			if (onDone) { onDone(keep); }
			return;
		}

		const run = (flipRuns.get(container) || 0) + 1;
		flipRuns.set(container, run);
		container.classList.add('is-animating');

		// FIRST: where the items ARE — measured before the reset below, because
		// getBoundingClientRect() includes transforms, so an interrupted run hands
		// over from where its items had got to rather than snapping back first.
		const before = new Map();
		items.forEach((item) => {
			if (!item.classList.contains('is-hidden')) {
				before.set(item, item.getBoundingClientRect());
			}
		});

		items.forEach(clear);

		const leaving = items.filter(
			(item) => !item.classList.contains('is-hidden') && !matches(item)
		);

		// Fade the departing items out before they leave the flow, so the
		// survivors do not jump while something is still painted over them.
		leaving.forEach((item) => item.classList.add('is-leaving'));

		window.setTimeout(() => {
			if (flipRuns.get(container) !== run) { return; }

			items.forEach((item) => {
				const show = matches(item);
				item.classList.remove('is-leaving');
				item.classList.toggle('is-hidden', !show);
			});

			const after = new Map();
			keep.forEach((item) => after.set(item, item.getBoundingClientRect()));

			// INVERT: put each survivor back where it was.
			keep.forEach((item) => {
				const first = before.get(item);
				const last = after.get(item);

				if (!first) {
					// Was not on screen before, so it fades in rather than moves.
					item.classList.add('is-entering');
					return;
				}

				const dx = first.left - last.left;
				const dy = first.top - last.top;
				if (!dx && !dy) { return; }

				item.style.transition = 'none';
				item.style.transform = `translate(${dx}px, ${dy}px)`;
			});

			// PLAY: next frame, drop the inverted transform and let it transition.
			window.requestAnimationFrame(() => {
				if (flipRuns.get(container) !== run) { return; }

				keep.forEach((item) => {
					if (item.classList.contains('is-entering')) {
						item.classList.remove('is-entering');
						item.classList.add('is-entered');
						return;
					}
					if (!item.style.transform) { return; }
					item.style.transition = `transform ${duration}ms ease`;
					item.style.transform = '';
				});

				window.setTimeout(() => {
					if (flipRuns.get(container) !== run) { return; }
					items.forEach(clear);
					container.classList.remove('is-animating');
					if (onDone) { onDone(keep); }
				}, duration + 40);
			});
		}, leaving.length ? duration : 0);
	};

	/*
	 * Scroll-spy: which of an ordered list of elements is the reader "on".
	 *
	 * Shared by Scroll Elements and Table of Contents. The DECISION is a
	 * measurement — the last element whose top has passed the offset line, or
	 * the final one once the page is scrolled to the bottom (its top may never
	 * reach the line) — so it is unambiguous however many are on screen. The
	 * TRIGGERS are an IntersectionObserver whose boundary sits on that same
	 * line, plus a time-throttled scroll/resize fallback: two independent ways
	 * to fire, so a stalled frame loop cannot silently stop it.
	 *
	 * `onChange(element)` is called only when the answer changes. Returns
	 * { update, destroy }.
	 */
	const scrollSpy = (elements, onChange, options = {}) => {
		const items = Array.from(elements || []).filter(Boolean);
		const offset = Math.max(0, parseInt(options.offset, 10) || 0);
		const throttle = typeof options.throttle === 'number' ? options.throttle : 60;

		let current = null;
		let last = 0;
		let observer = null;

		const update = () => {
			last = Date.now();
			if (!items.length) { return; }

			const line = offset + 1;
			let next = items[0];
			items.forEach((el) => {
				if (el.getBoundingClientRect().top <= line) { next = el; }
			});

			const atBottom = (window.innerHeight + window.scrollY) >= (document.documentElement.scrollHeight - 2);
			if (atBottom) { next = items[items.length - 1]; }

			if (next !== current) {
				current = next;
				if (typeof onChange === 'function') { onChange(next); }
			}
		};

		const queue = () => {
			if (Date.now() - last < throttle) { return; }
			update();
		};

		if (typeof window.IntersectionObserver === 'function') {
			observer = new window.IntersectionObserver(() => update(), {
				rootMargin: `-${offset}px 0px 0px 0px`,
				threshold: [0, 1]
			});
			items.forEach((el) => observer.observe(el));
		}

		window.addEventListener('scroll', queue, { passive: true });
		window.addEventListener('resize', queue);

		const destroy = () => {
			if (observer) { observer.disconnect(); }
			window.removeEventListener('scroll', queue);
			window.removeEventListener('resize', queue);
		};

		update();
		return { update, destroy };
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
		flipFilter,
		scrollSpy,
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
