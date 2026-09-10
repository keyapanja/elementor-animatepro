/*
 * EAP Hover Interaction — cursor tilt only.
 *
 * Opacity, filters, offset and transform are handled entirely by
 * hover-interaction.css: the controls write CSS variables and `:hover` swaps
 * them. The one thing CSS cannot know is where the pointer is, so this file
 * exists purely to write --eap-hi-tilt-x / --eap-hi-tilt-y onto the wrapper and
 * let the already-composed transform pick them up by inheritance.
 *
 * It listens on the document rather than binding per element, because the
 * extension's state lives in `prefix_class`: toggling the control in the editor
 * swaps a class on the existing wrapper WITHOUT re-rendering it, so nothing that
 * scanned the DOM at render time would ever see the change. Delegation reads the
 * classes at event time, which makes the editor and the front end behave the
 * same for free.
 */
(() => {
	if (window.eapHoverInteractionBound) {
		return;
	}
	window.eapHoverInteractionBound = true;

	const TILT = '.eap-hi-active.eap-hi-tilt-active';

	const reduced = () =>
		window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	// Touch devices fire mouseover on tap, which would strand the tilt at
	// whatever angle the tap landed on. Tilt is for real pointers only.
	const hasHover = () =>
		!window.matchMedia || window.matchMedia('(hover: hover)').matches;

	const clamp = (n, min, max) => Math.min(max, Math.max(min, n));

	let active = null;
	let queued = false;
	let rx = 0;
	let ry = 0;

	const apply = () => {
		queued = false;
		if (!active) {
			return;
		}
		active.style.setProperty('--eap-hi-tilt-x', `${rx}deg`);
		active.style.setProperty('--eap-hi-tilt-y', `${ry}deg`);
	};

	const release = (el) => {
		if (!el) {
			return;
		}
		el.classList.remove('eap-hi--tilting');
		el.style.setProperty('--eap-hi-tilt-x', '0deg');
		el.style.setProperty('--eap-hi-tilt-y', '0deg');
	};

	const onMove = (event) => {
		if (!active) {
			return;
		}

		const rect = active.getBoundingClientRect();
		if (!rect.width || !rect.height) {
			return;
		}

		// -1 .. 1 from the element's centre.
		const nx = clamp(((event.clientX - rect.left) / rect.width) * 2 - 1, -1, 1);
		const ny = clamp(((event.clientY - rect.top) / rect.height) * 2 - 1, -1, 1);

		const raw = parseFloat(
			window.getComputedStyle(active).getPropertyValue('--eap-hi-tilt-max')
		);
		const max = Number.isFinite(raw) ? raw : 12;
		const dir = active.classList.contains('eap-hi-tiltrev-active') ? -1 : 1;

		// The edge nearest the cursor leans away, so the element reads as being
		// pushed wherever it is pointed at.
		rx = -ny * max * dir;
		ry = nx * max * dir;

		if (!queued) {
			queued = true;
			window.requestAnimationFrame(apply);
		}
	};

	document.addEventListener('mouseover', (event) => {
		const target = event.target;
		if (!target || typeof target.closest !== 'function') {
			return;
		}

		// closest() picks the innermost match, so a tilting element nested inside
		// another hands over cleanly in both directions.
		const el = target.closest(TILT);
		if (el === active) {
			return;
		}

		release(active);
		active = null;

		if (!el || reduced() || !hasHover()) {
			return;
		}

		active = el;
		el.classList.add('eap-hi--tilting');
	});

	document.addEventListener('mouseout', (event) => {
		if (!active) {
			return;
		}
		// mouseout also fires when crossing into a child; only a relatedTarget
		// outside the element (or none at all, i.e. leaving the window) is a real exit.
		const to = event.relatedTarget;
		if (to && active.contains(to)) {
			return;
		}
		release(active);
		active = null;
	});

	document.addEventListener('mousemove', onMove, { passive: true });
})();
