(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const RING_RADIUS = 45;
	const RING_CIRC = 2 * Math.PI * RING_RADIUS;

	const reduceMotion = () => typeof api.prefersReducedMotion === 'function' && api.prefersReducedMotion();
	const pad = (value, length) => String(value).padStart(length, '0');

	// One shared 1s ticker drives every countdown on the page. Detached nodes
	// (e.g. when Elementor re-renders a widget in the editor) are pruned, so we
	// never leak per-widget intervals.
	const instances = new Set();
	let ticker = null;

	const ensureTicker = () => {
		if (ticker) {
			return;
		}
		ticker = window.setInterval(() => {
			instances.forEach((inst) => {
				if (!document.contains(inst.el)) {
					instances.delete(inst);
					return;
				}
				inst.tick();
			});
			if (instances.size === 0) {
				window.clearInterval(ticker);
				ticker = null;
			}
		}, 1000);
	};

	const setFlipDigit = (flipEl, newChar, speed, animate) => {
		const cur = flipEl.dataset.val || '0';
		if (cur === newChar) {
			return;
		}

		const topNum = flipEl.querySelector('.eap-cd-flip__static--top .eap-cd-flip__num');
		const bottomNum = flipEl.querySelector('.eap-cd-flip__static--bottom .eap-cd-flip__num');
		const foldTopNum = flipEl.querySelector('.eap-cd-flip__fold--top .eap-cd-flip__num');
		const foldBottomNum = flipEl.querySelector('.eap-cd-flip__fold--bottom .eap-cd-flip__num');

		if (!topNum || !bottomNum || !foldTopNum || !foldBottomNum) {
			return;
		}

		if (!animate || reduceMotion()) {
			topNum.textContent = newChar;
			bottomNum.textContent = newChar;
			foldTopNum.textContent = newChar;
			foldBottomNum.textContent = newChar;
			flipEl.dataset.val = newChar;
			return;
		}

		// Static top shows the NEW value (revealed as the top leaf folds away).
		topNum.textContent = newChar;
		// Static bottom keeps the OLD value until the bottom leaf covers it.
		bottomNum.textContent = cur;
		// Top leaf shows OLD (folds down); bottom leaf shows NEW (folds onto bottom).
		foldTopNum.textContent = cur;
		foldBottomNum.textContent = newChar;

		flipEl.classList.remove('is-flipping');
		// Force reflow so the animation restarts on rapid consecutive flips.
		void flipEl.offsetWidth;
		flipEl.classList.add('is-flipping');

		window.clearTimeout(flipEl._eapFlipTimer);
		flipEl._eapFlipTimer = window.setTimeout(() => {
			bottomNum.textContent = newChar;
			foldTopNum.textContent = newChar;
			flipEl.classList.remove('is-flipping');
			flipEl.dataset.val = newChar;
		}, speed + 60);
	};

	const updateFlipUnit = (container, value, speed, animate) => {
		const digits = container.querySelectorAll('.eap-cd-flip');
		const str = pad(value, digits.length);
		digits.forEach((digit, index) => setFlipDigit(digit, str.charAt(index), speed, animate));
	};

	const updateNumber = (node, value, animate) => {
		const length = parseInt(node.dataset.digits || '2', 10);
		const text = pad(value, length);

		if (node.textContent === text) {
			return;
		}

		node.textContent = text;

		const max = parseInt(node.dataset.max || '0', 10);
		if (max > 0) {
			const circle = node.closest('.eap-countdown__circle');
			const ring = circle ? circle.querySelector('[data-eap-cd-ring]') : null;
			if (ring) {
				const fraction = Math.min(1, Math.max(0, value / max));
				ring.style.strokeDasharray = String(RING_CIRC);
				ring.style.strokeDashoffset = String(RING_CIRC * (1 - fraction));
			}
		}

		if (animate && !reduceMotion()) {
			node.classList.remove('is-tick');
			void node.offsetWidth;
			node.classList.add('is-tick');
		}
	};

	// Measure each roll's cell height and lock the window + reel position to it,
	// so the slide works at any font size the user picks.
	const sizeRolls = (root) => {
		root.querySelectorAll('[data-eap-cd-roll]').forEach((roll) => {
			const reel = roll.querySelector('.eap-cd-roll__reel');
			const cell = reel && reel.children[0];
			if (!cell) {
				return;
			}
			const height = cell.getBoundingClientRect().height;
			if (height <= 0) {
				return;
			}
			roll.style.height = height + 'px';
			roll.dataset.cellH = String(height);
			const index = parseInt(roll.dataset.val || '0', 10) || 0;
			const prev = reel.style.transition;
			reel.style.transition = 'none';
			reel.style.transform = 'translateY(' + (-(index * height)) + 'px)';
			void reel.offsetWidth;
			reel.style.transition = prev;
		});
	};

	const setRollDigit = (roll, newChar, animate) => {
		if (roll.dataset.val === newChar) {
			return;
		}
		const reel = roll.querySelector('.eap-cd-roll__reel');
		if (!reel) {
			return;
		}
		const height = parseFloat(roll.dataset.cellH || '0');
		const index = parseInt(newChar, 10) || 0;
		const y = height > 0 ? (-(index * height)) + 'px' : 'calc(' + index + ' * -1em)';

		if (!animate || reduceMotion()) {
			const prev = reel.style.transition;
			reel.style.transition = 'none';
			reel.style.transform = 'translateY(' + y + ')';
			void reel.offsetWidth;
			reel.style.transition = prev;
		} else {
			reel.style.transform = 'translateY(' + y + ')';
		}

		roll.dataset.val = newChar;
	};

	const updateRollUnit = (container, value, animate) => {
		const rolls = container.querySelectorAll('[data-eap-cd-roll]');
		const str = pad(value, rolls.length);
		rolls.forEach((roll, index) => setRollDigit(roll, str.charAt(index), animate));
	};

	const computeParts = (remaining) => ({
		days: Math.floor(remaining / 86400),
		hours: Math.floor((remaining % 86400) / 3600),
		minutes: Math.floor((remaining % 3600) / 60),
		seconds: Math.floor(remaining % 60)
	});

	const readSpeed = (el) => {
		const raw = window.getComputedStyle(el).getPropertyValue('--eap-cd-flip-speed');
		const parsed = parseFloat(raw);
		return isNaN(parsed) ? 600 : parsed;
	};

	const COOKIE_MAXAGE = 365 * 24 * 60 * 60; // 1 year, in seconds.

	const getCookie = (name) => {
		const parts = ('; ' + document.cookie).split('; ' + name + '=');
		if (parts.length === 2) {
			return parts.pop().split(';').shift();
		}
		return '';
	};

	const setCookie = (name, value, persistent) => {
		let cookie = name + '=' + value + '; path=/; SameSite=Lax';
		if (persistent) {
			cookie += '; max-age=' + COOKIE_MAXAGE;
		}
		document.cookie = cookie;
	};

	// Work out an evergreen timer's end time for THIS visitor. The first visit
	// records a start timestamp (persistent cookie for "per visitor", or a
	// session cookie for "per browser session"); later visits reuse it so the
	// countdown continues where it left off. Returns { start, target } in ms.
	const resolveEvergreen = (el, duration, editorMode) => {
		const now = Date.now();
		if (!duration) {
			return { start: now, target: now };
		}

		const id = el.dataset.eapId || 'cd';
		const persistent = (el.dataset.eapStorage || 'cookie') !== 'session';
		const recurring = el.dataset.eapRecurring === 'yes';
		const cookieName = 'eap_cd_' + id;

		// In the editor always start fresh so the preview shows the full duration.
		let start = editorMode ? 0 : (parseInt(getCookie(cookieName) || '0', 10) || 0);
		if (!start || start > now + 5000) {
			start = now;
			if (!editorMode) {
				setCookie(cookieName, String(start), persistent);
			}
		}

		let target = start + duration;
		if (recurring && now >= target) {
			const completed = Math.floor((now - start) / duration) + 1;
			target = start + (completed * duration);
		}

		return { start: start, target: target };
	};

	const revealOnView = (el, editorMode) => {
		if (!/eap-countdown--anim-/.test(el.className)) {
			return;
		}

		if (editorMode || reduceMotion()) {
			el.classList.add('is-visible');
			return;
		}

		if (typeof api.isInViewport === 'function' && api.isInViewport(el)) {
			window.requestAnimationFrame(() => el.classList.add('is-visible'));
			return;
		}

		if ('IntersectionObserver' in window) {
			const observer = new IntersectionObserver((entries, obs) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						obs.unobserve(entry.target);
					}
				});
			}, { threshold: 0.2 });
			observer.observe(el);
			return;
		}

		el.classList.add('is-visible');
	};

	const setup = (el, editorMode) => {
		if (el.dataset.eapCountdownBound === 'true') {
			return;
		}
		el.dataset.eapCountdownBound = 'true';

		const source = el.dataset.eapSource || 'date';
		const serverNow = parseInt(el.dataset.eapNow || '0', 10);
		const expire = el.dataset.eapExpire || 'none';
		const redirect = el.dataset.eapRedirect || '';
		const speed = readSpeed(el);

		// Resolve the end time (ms). Date mode reads a fixed server target and
		// corrects for clock skew. Evergreen mode derives a per-visitor target
		// from a stored start time so the countdown is relative to each visitor's
		// first visit rather than a shared moment.
		let target;
		let skew;
		let startMs = 0;
		let duration = 0;
		const recurring = el.dataset.eapRecurring === 'yes';

		if (source === 'evergreen') {
			duration = parseInt(el.dataset.eapDuration || '0', 10);
			const ev = resolveEvergreen(el, duration, editorMode);
			startMs = ev.start;
			target = ev.target;
			skew = 0;
		} else {
			target = parseInt(el.dataset.eapTarget || '0', 10);
			skew = serverNow ? serverNow - Date.now() : 0;
		}

		const rowEl = el.querySelector('[data-eap-countdown-row]');
		const messageEl = el.querySelector('[data-eap-countdown-message]');

		const flipUnits = {};
		const rollUnits = {};
		el.querySelectorAll('.eap-countdown__digits[data-unit]').forEach((node) => {
			if (node.querySelector('.eap-cd-flip')) {
				flipUnits[node.dataset.unit] = node;
			} else if (node.querySelector('[data-eap-cd-roll]')) {
				rollUnits[node.dataset.unit] = node;
			}
		});

		const numberNodes = {};
		el.querySelectorAll('[data-eap-cd-number][data-unit]').forEach((node) => {
			numberNodes[node.dataset.unit] = node;
		});

		if (Object.keys(rollUnits).length) {
			sizeRolls(el);
			if (document.fonts && document.fonts.ready) {
				document.fonts.ready.then(() => sizeRolls(el)).catch(() => {});
			}
			window.addEventListener('resize', () => sizeRolls(el));
		}

		let first = true;
		let expired = false;

		const apply = (parts, animate) => {
			Object.keys(flipUnits).forEach((key) => updateFlipUnit(flipUnits[key], parts[key], speed, animate));
			Object.keys(rollUnits).forEach((key) => updateRollUnit(rollUnits[key], parts[key], animate));
			Object.keys(numberNodes).forEach((key) => updateNumber(numberNodes[key], parts[key], animate));
		};

		const handleExpire = () => {
			if (expired) {
				return;
			}
			expired = true;

			if (editorMode) {
				return; // Never redirect or hide while editing.
			}

			if (expire === 'redirect' && redirect) {
				window.location.href = redirect;
				return;
			}

			if (expire === 'message' && messageEl) {
				if (rowEl) {
					rowEl.style.display = 'none';
				}
				messageEl.hidden = false;
			}
		};

		const instance = {
			el,
			tick() {
				const now = Date.now() + skew;
				let remaining = Math.max(0, Math.round((target - now) / 1000));

				// Recurring evergreen timer: when a cycle ends, roll forward to the
				// next cycle instead of expiring, so it loops for the visitor.
				if (remaining <= 0 && source === 'evergreen' && recurring && duration > 0) {
					const completed = Math.floor((now - startMs) / duration) + 1;
					target = startMs + (completed * duration);
					remaining = Math.max(0, Math.round((target - now) / 1000));
				}

				apply(computeParts(remaining), !first);
				first = false;

				if (remaining <= 0 && !(source === 'evergreen' && recurring)) {
					handleExpire();
					instances.delete(instance);
				}
			}
		};

		instance.tick();

		if (!expired) {
			instances.add(instance);
			ensureTicker();
		}

		revealOnView(el, editorMode);
	};

	api.register('countdown', (root, { editorMode = false } = {}) => {
		api.getNodes(root, '[data-eap-countdown]').forEach((el) => setup(el, editorMode));
	});
})();
